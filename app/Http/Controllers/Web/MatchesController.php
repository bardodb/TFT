<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TftMatch;
use Inertia\Inertia;
use Inertia\Response;

class MatchesController extends Controller
{
    /**
     * Show the matches index page
     */
    public function index(): Response
    {
        $matches = TftMatch::with(['participants.player'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Matches/Index', [
            'matches' => $matches,
        ]);
    }

    /**
     * Show a specific match
     */
    public function show(string $matchId): Response
    {
        $match = TftMatch::with(['participants.player', 'participants.build'])
            ->where('match_id', $matchId)
            ->firstOrFail();

        return Inertia::render('Matches/Show', [
            'match' => $match,
        ]);
    }
}
