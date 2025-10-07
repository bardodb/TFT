<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Build;
use Inertia\Inertia;
use Inertia\Response;

class BuildsController extends Controller
{
    /**
     * Show the builds index page
     */
    public function index(): Response
    {
        $builds = Build::with(['champions', 'items', 'augments'])
            ->orderBy('placement', 'asc')
            ->paginate(20);

        return Inertia::render('Builds/Index', [
            'builds' => $builds,
        ]);
    }

    /**
     * Show a specific build
     */
    public function show(string $buildId): Response
    {
        $build = Build::with(['champions', 'items', 'augments', 'participant.player'])
            ->findOrFail($buildId);

        return Inertia::render('Builds/Show', [
            'build' => $build,
        ]);
    }
}
