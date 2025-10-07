<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\TftAnalysisService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    protected $analysisService;

    public function __construct(TftAnalysisService $analysisService)
    {
        $this->analysisService = $analysisService;
    }

    /**
     * Show the main dashboard
     */
    public function index(): Response
    {
        $globalStats = $this->analysisService->getGlobalStats();
        $bestBuilds = $this->analysisService->getBestBuilds(10, 4);
        $popularCompositions = $this->analysisService->getMostPopularCompositions(10);
        $bestAugments = $this->analysisService->getBestAugments(10, 10);
        $bestItems = $this->analysisService->getBestItems(10, 10);

        return Inertia::render('Dashboard', [
            'globalStats' => $globalStats,
            'bestBuilds' => $bestBuilds,
            'popularCompositions' => $popularCompositions,
            'bestAugments' => $bestAugments,
            'bestItems' => $bestItems,
        ]);
    }

    /**
     * Show the matches page
     */
    public function matches(): Response
    {
        return Inertia::render('Matches/Index');
    }

    /**
     * Show the builds page
     */
    public function builds(): Response
    {
        return Inertia::render('Builds/Index');
    }

    /**
     * Show the analytics page
     */
    public function analytics(): Response
    {
        return Inertia::render('Analytics/Index');
    }
}
