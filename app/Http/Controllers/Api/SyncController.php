<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RiotGamesService;
use App\Services\TftAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SyncController extends Controller
{
    protected $riotService;
    protected $analysisService;

    public function __construct(RiotGamesService $riotService, TftAnalysisService $analysisService)
    {
        $this->riotService = $riotService;
        $this->analysisService = $analysisService;
    }

    /**
     * Sincronizar partidas de um jogador específico
     */
    public function syncMatches(Request $request)
    {
        $request->validate([
            'puuid' => 'required|string',
            'region' => 'required|string|in:americas,europe,asia,sea',
            'count' => 'nullable|integer|min:1|max:100',
        ]);

        $puuid = $request->input('puuid');
        $region = $request->input('region', 'americas');
        $count = $request->input('count', 20);

        try {
            // Buscar IDs das partidas
            $matchIds = $this->riotService->getMatchIdsByPuuid($puuid, $region, $count, 0);

            if (!$matchIds || count($matchIds) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma partida encontrada para este jogador.',
                ], 404);
            }

            $processed = 0;
            $errors = [];

            // Processar cada partida
            foreach ($matchIds as $matchId) {
                try {
                    $matchData = $this->riotService->getMatchDetails($matchId, $region);
                    
                    if ($matchData) {
                        $this->analysisService->processMatchData($matchData);
                        $processed++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Erro ao processar partida {$matchId}: " . $e->getMessage();
                    Log::error("Erro ao processar partida {$matchId}", ['error' => $e->getMessage()]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Sincronização concluída! {$processed} partidas processadas.",
                'data' => [
                    'processed' => $processed,
                    'total' => count($matchIds),
                    'errors' => $errors,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error("Erro durante a sincronização", ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro durante a sincronização: ' . $e->getMessage(),
            ], 500);
        }
    }
}

