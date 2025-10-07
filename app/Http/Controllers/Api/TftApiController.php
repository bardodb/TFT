<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RiotGamesService;
use App\Services\TftAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TftApiController extends Controller
{
    protected $riotService;
    protected $analysisService;

    public function __construct(RiotGamesService $riotService, TftAnalysisService $analysisService)
    {
        $this->riotService = $riotService;
        $this->analysisService = $analysisService;
    }

    /**
     * Get global TFT statistics
     */
    public function getGlobalStats(): JsonResponse
    {
        $stats = $this->analysisService->getGlobalStats();
        
        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get TFT status for all regions
     */
    public function getTftStatus(): JsonResponse
    {
        $regions = ['americas', 'asia', 'europe'];
        $status = [];

        foreach ($regions as $region) {
            $regionStatus = $this->riotService->getTftStatus($region);
            if ($regionStatus) {
                $status[$region] = $regionStatus;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $status,
        ]);
    }

    /**
     * Sync match data from Riot API
     */
    public function syncMatches(Request $request): JsonResponse
    {
        $request->validate([
            'puuid' => 'required|string',
            'region' => 'required|string|in:americas,asia,europe',
            'count' => 'integer|min:1|max:100',
            'start' => 'integer|min:0',
        ]);

        $puuid = $request->input('puuid');
        $region = $request->input('region');
        $count = $request->input('count', 20);
        $start = $request->input('start', 0);

        try {
            // Get match IDs
            $matchIds = $this->riotService->getMatchIdsByPuuid($puuid, $region, $count, $start);
            
            if (!$matchIds) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch match IDs',
                ], 400);
            }

            $processedMatches = 0;
            $errors = [];

            foreach ($matchIds as $matchId) {
                try {
                    $matchData = $this->riotService->getMatchDetails($matchId, $region);
                    
                    if ($matchData) {
                        $this->analysisService->processMatchData($matchData);
                        $processedMatches++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Failed to process match {$matchId}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Processed {$processedMatches} matches successfully",
                'data' => [
                    'processed_matches' => $processedMatches,
                    'total_requested' => count($matchIds),
                    'errors' => $errors,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync matches: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get player information by PUUID
     */
    public function getPlayer(Request $request): JsonResponse
    {
        $request->validate([
            'puuid' => 'required|string',
            'region' => 'required|string|in:americas,asia,europe',
        ]);

        $puuid = $request->input('puuid');
        $region = $request->input('region');

        try {
            $summonerData = $this->riotService->getSummonerByPuuid($puuid, $region);
            
            if (!$summonerData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Player not found',
                ], 404);
            }

            // Get ranked information
            $rankedData = $this->riotService->getTftRankedEntries($summonerData['id'], $region);

            return response()->json([
                'success' => true,
                'data' => [
                    'summoner' => $summonerData,
                    'ranked' => $rankedData,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch player data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get top players from leaderboards
     */
    public function getTopPlayers(Request $request): JsonResponse
    {
        $request->validate([
            'region' => 'required|string|in:americas,asia,europe',
            'tier' => 'string|in:challenger,grandmaster,master',
        ]);

        $region = $request->input('region');
        $tier = $request->input('tier', 'challenger');

        try {
            $method = 'getTft' . ucfirst($tier) . 'League';
            $leaderboard = $this->riotService->$method($region);

            if (!$leaderboard) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch leaderboard',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $leaderboard,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch top players: ' . $e->getMessage(),
            ], 500);
        }
    }
}
