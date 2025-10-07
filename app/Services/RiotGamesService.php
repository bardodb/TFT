<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RiotGamesService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;
    protected $regions = [
        'americas' => 'https://americas.api.riotgames.com',
        'asia' => 'https://asia.api.riotgames.com',
        'europe' => 'https://europe.api.riotgames.com',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.riot.api_key');
        
        // Verificar se a chave da API está configurada
        if (!$this->apiKey || $this->apiKey === 'your_riot_api_key_here') {
            Log::warning('Riot API key not configured or using default value');
            throw new \Exception('Riot API key not configured. Please set RIOT_API_KEY in your .env file.');
        }
        
        // Configuração do cliente HTTP
        $clientConfig = [
            'timeout' => 30,
            'headers' => [
                'X-Riot-Token' => $this->apiKey,
                'Accept' => 'application/json',
            ],
        ];

        // Em desenvolvimento, desabilita verificação SSL se necessário
        if (config('app.debug') && config('app.env') === 'local') {
            $clientConfig['verify'] = false;
        }

        $this->client = new Client($clientConfig);
    }

    /**
     * Get account by Riot ID (gameName + tagLine)
     * Returns PUUID and account info
     */
    public function getAccountByRiotId(string $gameName, string $tagLine, string $region = 'americas'): ?array
    {
        $cacheKey = "account_{$gameName}_{$tagLine}_{$region}";
        
        return Cache::remember($cacheKey, 3600, function () use ($gameName, $tagLine, $region) {
            try {
                $response = $this->client->get(
                    "{$this->regions[$region]}/riot/account/v1/accounts/by-riot-id/{$gameName}/{$tagLine}"
                );
                
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error("Error fetching account by Riot ID: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get complete player info by Riot ID (gameName + tagLine)
     * Returns account, summoner and ranked data
     */
    public function getPlayerByRiotId(string $gameName, string $tagLine, string $region = 'americas'): ?array
    {
        // Primeiro busca a conta para obter o PUUID
        $account = $this->getAccountByRiotId($gameName, $tagLine, $region);
        
        if (!$account) {
            return null;
        }

        $puuid = $account['puuid'];

        // Busca informações do summoner
        $summoner = $this->getSummonerByPuuid($puuid, $region);
        
        if (!$summoner) {
            return null;
        }

        // Busca ranked entries
        $rankedData = $this->getTftRankedEntries($summoner['id'], $region);

        return [
            'account' => $account,
            'summoner' => $summoner,
            'ranked' => $rankedData ?? [],
        ];
    }

    /**
     * Get summoner by PUUID
     */
    public function getSummonerByPuuid(string $puuid, string $region): ?array
    {
        $cacheKey = "summoner_puuid_{$puuid}_{$region}";
        
        return Cache::remember($cacheKey, 3600, function () use ($puuid, $region) {
            try {
                $response = $this->client->get(
                    "{$this->regions['americas']}/tft/summoner/v1/summoners/by-puuid/{$puuid}"
                );
                
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error("Error fetching summoner by PUUID: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get summoner by summoner ID
     */
    public function getSummonerBySummonerId(string $summonerId, string $region): ?array
    {
        $cacheKey = "summoner_id_{$summonerId}_{$region}";
        
        return Cache::remember($cacheKey, 3600, function () use ($summonerId, $region) {
            try {
                $response = $this->client->get(
                    "{$this->regions['americas']}/tft/summoner/v1/summoners/{$summonerId}"
                );
                
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error("Error fetching summoner by ID: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get TFT ranked entries by summoner ID
     */
    public function getTftRankedEntries(string $summonerId, string $region): ?array
    {
        $cacheKey = "tft_ranked_{$summonerId}_{$region}";
        
        return Cache::remember($cacheKey, 1800, function () use ($summonerId, $region) {
            try {
                $response = $this->client->get(
                    "{$this->regions['americas']}/tft/league/v1/entries/by-summoner/{$summonerId}"
                );
                
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error("Error fetching TFT ranked entries: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get match IDs by PUUID
     */
    public function getMatchIdsByPuuid(string $puuid, string $region, int $count = 20, int $start = 0): ?array
    {
        $cacheKey = "match_ids_{$puuid}_{$region}_{$count}_{$start}";
        
        return Cache::remember($cacheKey, 900, function () use ($puuid, $region, $count, $start) {
            try {
                // Validar se o PUUID parece válido (deve ser uma string longa sem caracteres especiais)
                if (strlen($puuid) < 36 || strpos($puuid, '#') !== false) {
                    Log::error("Invalid PUUID format: " . $puuid);
                    throw new \InvalidArgumentException("Invalid PUUID format");
                }

                $url = "{$this->regions[$region]}/tft/match/v1/matches/by-puuid/{$puuid}/ids";
                Log::info("Fetching match IDs from URL: " . $url);
                
                $response = $this->client->get($url, [
                    'query' => [
                        'count' => $count,
                        'start' => $start,
                    ]
                ]);
                
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error("Error fetching match IDs for PUUID '{$puuid}': " . $e->getMessage());
                return null;
            } catch (\InvalidArgumentException $e) {
                Log::error("Invalid PUUID provided: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get match details by match ID
     */
    public function getMatchDetails(string $matchId, string $region): ?array
    {
        $cacheKey = "match_details_{$matchId}_{$region}";
        
        return Cache::remember($cacheKey, 3600, function () use ($matchId, $region) {
            try {
                $response = $this->client->get(
                    "{$this->regions['americas']}/tft/match/v1/matches/{$matchId}"
                );
                
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error("Error fetching match details: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get TFT league entries
     */
    public function getTftLeagueEntries(string $tier, string $division, string $region, int $page = 1): ?array
    {
        $cacheKey = "league_entries_{$tier}_{$division}_{$region}_{$page}";
        
        return Cache::remember($cacheKey, 1800, function () use ($tier, $division, $region, $page) {
            try {
                $response = $this->client->get(
                    "{$this->regions['americas']}/tft/league/v1/entries/{$tier}/{$division}",
                    [
                        'query' => [
                            'page' => $page,
                        ]
                    ]
                );
                
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error("Error fetching league entries: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get TFT challenger league
     */
    public function getTftChallengerLeague(string $region): ?array
    {
        $cacheKey = "challenger_league_{$region}";
        
        return Cache::remember($cacheKey, 1800, function () use ($region) {
            try {
                $response = $this->client->get(
                    "{$this->regions['americas']}/tft/league/v1/challenger"
                );
                
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error("Error fetching challenger league: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get TFT grandmaster league
     */
    public function getTftGrandmasterLeague(string $region): ?array
    {
        $cacheKey = "grandmaster_league_{$region}";
        
        return Cache::remember($cacheKey, 1800, function () use ($region) {
            try {
                $response = $this->client->get(
                    "{$this->regions['americas']}/tft/league/v1/grandmaster"
                );
                
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error("Error fetching grandmaster league: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get TFT master league
     */
    public function getTftMasterLeague(string $region): ?array
    {
        $cacheKey = "master_league_{$region}";
        
        return Cache::remember($cacheKey, 1800, function () use ($region) {
            try {
                $response = $this->client->get(
                    "{$this->regions['americas']}/tft/league/v1/master"
                );
                
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error("Error fetching master league: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get TFT status
     */
    public function getTftStatus(string $region): ?array
    {
        $cacheKey = "tft_status_{$region}";
        
        return Cache::remember($cacheKey, 300, function () use ($region) {
            try {
                $response = $this->client->get(
                    "{$this->regions['americas']}/tft/status/v1/platform-data"
                );
                
                return json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                Log::error("Error fetching TFT status: " . $e->getMessage());
                return null;
            }
        });
    }
}
