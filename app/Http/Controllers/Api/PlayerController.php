<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Services\RiotGamesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlayerController extends Controller
{
    protected $riotService;

    public function __construct(RiotGamesService $riotService)
    {
        $this->riotService = $riotService;
    }

    /**
     * Search for a player by Riot ID (gameName + tagLine)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'game_name' => 'required|string|max:255',
            'tag_line' => 'required|string|max:10',
            'region' => 'nullable|string|in:americas,asia,europe',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $gameName = $request->input('game_name');
        $tagLine = $request->input('tag_line');
        $region = $request->input('region', 'americas');

        try {
            $playerData = $this->riotService->getPlayerByRiotId($gameName, $tagLine, $region);

            if (!$playerData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jogador não encontrado',
                ], 404);
            }

            // Verificar se já existe no banco de dados
            $existingPlayer = Player::where('puuid', $playerData['account']['puuid'])->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'player_data' => $playerData,
                    'exists_in_database' => !is_null($existingPlayer),
                    'database_record' => $existingPlayer,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar jogador: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get player by PUUID
     * 
     * @param string $puuid
     * @return JsonResponse
     */
    public function show(string $puuid): JsonResponse
    {
        try {
            $player = Player::where('puuid', $puuid)
                ->with(['matchParticipants', 'builds'])
                ->first();

            if (!$player) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jogador não encontrado no banco de dados',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $player,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar jogador: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all players from database
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 20);
            
            $players = Player::orderBy('league_points', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $players,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar jogadores: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync player data from Riot API
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function sync(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'game_name' => 'required|string|max:255',
            'tag_line' => 'required|string|max:10',
            'region' => 'nullable|string|in:americas,asia,europe',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $gameName = $request->input('game_name');
        $tagLine = $request->input('tag_line');
        $region = $request->input('region', 'americas');

        try {
            $playerData = $this->riotService->getPlayerByRiotId($gameName, $tagLine, $region);

            if (!$playerData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jogador não encontrado',
                ], 404);
            }

            // Criar ou atualizar jogador no banco de dados
            $rankedData = $playerData['ranked'][0] ?? null;
            
            $player = Player::updateOrCreate(
                ['puuid' => $playerData['account']['puuid']],
                [
                    'summoner_id' => $playerData['summoner']['id'],
                    'game_name' => $playerData['account']['gameName'],
                    'tag_line' => $playerData['account']['tagLine'],
                    'tier' => $rankedData['tier'] ?? null,
                    'rank' => $rankedData['rank'] ?? null,
                    'league_points' => $rankedData['leaguePoints'] ?? 0,
                    'wins' => $rankedData['wins'] ?? 0,
                    'losses' => $rankedData['losses'] ?? 0,
                    'region' => $region,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Jogador sincronizado com sucesso',
                'data' => $player,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao sincronizar jogador: ' . $e->getMessage(),
            ], 500);
        }
    }
}
