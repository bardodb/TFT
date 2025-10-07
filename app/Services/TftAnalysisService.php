<?php

namespace App\Services;

use App\Models\TftMatch;
use App\Models\Player;
use App\Models\Build;
use App\Models\Champion;
use App\Models\Augment;
use App\Models\Item;
use App\Models\MatchParticipant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TftAnalysisService
{
    protected $riotService;

    public function __construct(RiotGamesService $riotService)
    {
        $this->riotService = $riotService;
    }

    /**
     * Process and store match data from Riot API
     */
    public function processMatchData(array $matchData): TftMatch
    {
        DB::beginTransaction();
        
        try {
            // Create or update match
            $match = TftMatch::updateOrCreate(
                ['match_id' => $matchData['metadata']['match_id']],
                [
                    'game_datetime' => now()->createFromTimestamp($matchData['info']['game_datetime'] / 1000),
                    'game_length' => $matchData['info']['game_length'],
                    'game_variation' => $matchData['info']['game_variation'] ?? null,
                    'game_version' => $matchData['info']['game_version'],
                    'queue_id' => $matchData['info']['queue_id'],
                    'tft_set_number' => $matchData['info']['tft_set_number'],
                    'data_version' => $matchData['metadata']['data_version'],
                ]
            );

            // Process participants
            foreach ($matchData['info']['participants'] as $participantData) {
                $this->processParticipant($match, $participantData);
            }

            DB::commit();
            return $match;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Process individual participant data
     */
    protected function processParticipant(TftMatch $match, array $participantData): void
    {
        // Create or update player
        $player = Player::updateOrCreate(
            ['puuid' => $participantData['puuid']],
            [
                'game_name' => $participantData['game_name'] ?? null,
                'tag_line' => $participantData['tag_line'] ?? null,
                'region' => 'americas', // Default region, should be determined from context
            ]
        );

        // Create match participant
        MatchParticipant::updateOrCreate(
            [
                'tft_match_id' => $match->id,
                'puuid' => $participantData['puuid'],
            ],
            [
                'player_id' => $player->id,
                'placement' => $participantData['placement'],
                'level' => $participantData['level'],
                'gold_left' => $participantData['gold_left'],
                'total_damage_to_players' => $participantData['total_damage_to_players'],
                'win' => $participantData['placement'] == 1,
                'last_round' => $participantData['last_round'],
                'time_eliminated' => $participantData['time_eliminated'],
                'companion' => $participantData['companion'] ?? null,
                'traits' => $participantData['traits'],
                'units' => $participantData['units'],
                'augments' => $participantData['augments'],
            ]
        );

        // Create build analysis
        $this->createBuildFromParticipant($match, $player, $participantData);
    }

    /**
     * Create build analysis from participant data
     */
    protected function createBuildFromParticipant(TftMatch $match, Player $player, array $participantData): void
    {
        $build = Build::create([
            'player_id' => $player->id,
            'tft_match_id' => $match->id,
            'placement' => $participantData['placement'],
            'level' => $participantData['level'],
            'gold_left' => $participantData['gold_left'],
            'total_damage_to_players' => $participantData['total_damage_to_players'],
            'win' => $participantData['placement'] == 1,
            'composition_name' => $this->generateCompositionName($participantData['units']),
        ]);

        // Attach champions
        $this->attachChampionsToBuild($build, $participantData['units']);

        // Attach augments
        $this->attachAugmentsToBuild($build, $participantData['augments']);

        // Attach items
        $this->attachItemsToBuild($build, $participantData['units']);
    }

    /**
     * Generate composition name based on units
     */
    protected function generateCompositionName(array $units): string
    {
        $traits = [];
        
        foreach ($units as $unit) {
            if (isset($unit['traits'])) {
                $traits = array_merge($traits, $unit['traits']);
            }
        }

        $traitCounts = array_count_values($traits);
        arsort($traitCounts);

        $primaryTraits = array_slice(array_keys($traitCounts), 0, 3);
        
        return implode(' / ', $primaryTraits) ?: 'Unknown Composition';
    }

    /**
     * Attach champions to build
     */
    protected function attachChampionsToBuild(Build $build, array $units): void
    {
        foreach ($units as $unit) {
            $champion = Champion::firstOrCreate(
                ['champion_id' => $unit['character_id']],
                [
                    'name' => $unit['character_id'], // Will be updated with proper name later
                    'cost' => $unit['cost'] ?? 1,
                    'traits' => $unit['traits'] ?? [],
                    'stats' => $unit['stats'] ?? [],
                    'ability' => $unit['ability'] ?? [],
                ]
            );

            $build->champions()->attach($champion->id, [
                'star_level' => $unit['tier'],
                'position' => $unit['position'] ?? null,
            ]);
        }
    }

    /**
     * Attach augments to build
     */
    protected function attachAugmentsToBuild(Build $build, array $augments): void
    {
        foreach ($augments as $augmentData) {
            $augment = Augment::firstOrCreate(
                ['augment_id' => $augmentData['name']],
                [
                    'name' => $augmentData['name'],
                    'description' => $augmentData['desc'] ?? '',
                    'tier' => $augmentData['tier'] ?? 1,
                    'category' => $augmentData['category'] ?? 'unknown',
                ]
            );

            $build->augments()->attach($augment->id);
        }
    }

    /**
     * Attach items to build
     */
    protected function attachItemsToBuild(Build $build, array $units): void
    {
        foreach ($units as $unit) {
            if (isset($unit['itemNames']) && is_array($unit['itemNames'])) {
                foreach ($unit['itemNames'] as $itemName) {
                    $item = Item::firstOrCreate(
                        ['item_id' => $itemName],
                        [
                            'name' => $itemName,
                            'description' => '',
                            'category' => 'unknown',
                            'gold_cost' => 0,
                        ]
                    );

                    $build->items()->attach($item->id, [
                        'champion_id' => Champion::where('champion_id', $unit['character_id'])->first()?->id,
                    ]);
                }
            }
        }
    }

    /**
     * Get best builds by placement
     */
    public function getBestBuilds(int $limit = 10, int $minPlacement = 4): Collection
    {
        return Build::with(['champions', 'augments', 'items', 'player'])
            ->where('placement', '<=', $minPlacement)
            ->orderBy('placement')
            ->limit($limit)
            ->get();
    }

    /**
     * Get most popular compositions
     */
    public function getMostPopularCompositions(int $limit = 10): Collection
    {
        return Build::select('composition_name', DB::raw('COUNT(*) as usage_count'))
            ->selectRaw('AVG(placement) as avg_placement')
            ->selectRaw('SUM(CASE WHEN win = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100 as win_rate')
            ->whereNotNull('composition_name')
            ->groupBy('composition_name')
            ->having('usage_count', '>=', 5)
            ->orderByDesc('usage_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get best augments by win rate
     */
    public function getBestAugments(int $limit = 10, int $minUsage = 10): Collection
    {
        return Augment::select('augments.*')
            ->selectRaw('COUNT(build_augments.build_id) as usage_count')
            ->selectRaw('AVG(builds.placement) as avg_placement')
            ->selectRaw('SUM(CASE WHEN builds.win = 1 THEN 1 ELSE 0 END) / COUNT(build_augments.build_id) * 100 as win_rate')
            ->join('build_augments', 'augments.id', '=', 'build_augments.augment_id')
            ->join('builds', 'build_augments.build_id', '=', 'builds.id')
            ->groupBy('augments.id')
            ->having('usage_count', '>=', $minUsage)
            ->orderByDesc('win_rate')
            ->limit($limit)
            ->get();
    }

    /**
     * Get best items by win rate
     */
    public function getBestItems(int $limit = 10, int $minUsage = 10): Collection
    {
        return Item::select('items.*')
            ->selectRaw('COUNT(build_items.build_id) as usage_count')
            ->selectRaw('AVG(builds.placement) as avg_placement')
            ->selectRaw('SUM(CASE WHEN builds.win = 1 THEN 1 ELSE 0 END) / COUNT(build_items.build_id) * 100 as win_rate')
            ->join('build_items', 'items.id', '=', 'build_items.item_id')
            ->join('builds', 'build_items.build_id', '=', 'builds.id')
            ->groupBy('items.id')
            ->having('usage_count', '>=', $minUsage)
            ->orderByDesc('win_rate')
            ->limit($limit)
            ->get();
    }

    /**
     * Get global match statistics
     */
    public function getGlobalStats(): array
    {
        $totalMatches = TftMatch::count();
        $totalPlayers = Player::count();
        $totalBuilds = Build::count();

        $avgPlacement = Build::avg('placement');
        
        // Evita divisão por zero
        $winRate = $totalBuilds > 0 
            ? Build::where('win', true)->count() / $totalBuilds * 100 
            : 0;

        return [
            'total_matches' => $totalMatches,
            'total_players' => $totalPlayers,
            'total_builds' => $totalBuilds,
            'avg_placement' => round($avgPlacement, 2),
            'global_win_rate' => round($winRate, 2),
        ];
    }

    /**
     * Update player ranking information
     */
    public function updatePlayerRanking(string $summonerId, string $region): void
    {
        $rankedData = $this->riotService->getTftRankedEntries($summonerId, $region);
        
        if ($rankedData && !empty($rankedData)) {
            $player = Player::where('summoner_id', $summonerId)->first();
            
            if ($player) {
                $rankedEntry = $rankedData[0]; // TFT typically has one ranked entry
                
                $player->update([
                    'tier' => $rankedEntry['tier'],
                    'rank' => $rankedEntry['rank'],
                    'league_points' => $rankedEntry['leaguePoints'],
                    'wins' => $rankedEntry['wins'],
                    'losses' => $rankedEntry['losses'],
                ]);
            }
        }
    }
}
