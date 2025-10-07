<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TftAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    protected $analysisService;

    public function __construct(TftAnalysisService $analysisService)
    {
        $this->analysisService = $analysisService;
    }

    /**
     * Get best builds by placement
     */
    public function getBestBuilds(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'integer|min:1|max:100',
            'min_placement' => 'integer|min:1|max:8',
        ]);

        $limit = $request->input('limit', 10);
        $minPlacement = $request->input('min_placement', 4);

        $builds = $this->analysisService->getBestBuilds($limit, $minPlacement);

        return response()->json([
            'success' => true,
            'data' => $builds,
        ]);
    }

    /**
     * Get most popular compositions
     */
    public function getMostPopularCompositions(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'integer|min:1|max:50',
        ]);

        $limit = $request->input('limit', 10);
        $compositions = $this->analysisService->getMostPopularCompositions($limit);

        return response()->json([
            'success' => true,
            'data' => $compositions,
        ]);
    }

    /**
     * Get best augments by win rate
     */
    public function getBestAugments(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'integer|min:1|max:100',
            'min_usage' => 'integer|min:1',
        ]);

        $limit = $request->input('limit', 10);
        $minUsage = $request->input('min_usage', 10);
        
        $augments = $this->analysisService->getBestAugments($limit, $minUsage);

        return response()->json([
            'success' => true,
            'data' => $augments,
        ]);
    }

    /**
     * Get best items by win rate
     */
    public function getBestItems(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'integer|min:1|max:100',
            'min_usage' => 'integer|min:1',
        ]);

        $limit = $request->input('limit', 10);
        $minUsage = $request->input('min_usage', 10);
        
        $items = $this->analysisService->getBestItems($limit, $minUsage);

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    /**
     * Get comprehensive analytics dashboard data
     */
    public function getDashboardData(): JsonResponse
    {
        $globalStats = $this->analysisService->getGlobalStats();
        $bestBuilds = $this->analysisService->getBestBuilds(5, 4);
        $popularCompositions = $this->analysisService->getMostPopularCompositions(5);
        $bestAugments = $this->analysisService->getBestAugments(5, 5);
        $bestItems = $this->analysisService->getBestItems(5, 5);

        return response()->json([
            'success' => true,
            'data' => [
                'global_stats' => $globalStats,
                'best_builds' => $bestBuilds,
                'popular_compositions' => $popularCompositions,
                'best_augments' => $bestAugments,
                'best_items' => $bestItems,
            ],
        ]);
    }
}
