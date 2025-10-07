<?php

namespace App\Console\Commands;

use App\Services\RiotGamesService;
use App\Services\TftAnalysisService;
use Illuminate\Console\Command;

class SyncTftData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tft:sync {puuid} {region=americas} {--count=20} {--start=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronizar dados de partidas TFT de um jogador específico';

    protected $riotService;
    protected $analysisService;

    public function __construct(RiotGamesService $riotService, TftAnalysisService $analysisService)
    {
        parent::__construct();
        $this->riotService = $riotService;
        $this->analysisService = $analysisService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $puuid = $this->argument('puuid');
        $region = $this->argument('region');
        $count = $this->option('count');
        $start = $this->option('start');

        $this->info("Iniciando sincronização para PUUID: {$puuid}");
        $this->info("Região: {$region}");
        $this->info("Quantidade: {$count}, Início: {$start}");

        try {
            // Buscar IDs das partidas
            $this->info('Buscando IDs das partidas...');
            $matchIds = $this->riotService->getMatchIdsByPuuid($puuid, $region, $count, $start);

            if (!$matchIds) {
                $this->error('Nenhuma partida encontrada para este PUUID.');
                return 1;
            }

            $this->info("Encontradas " . count($matchIds) . " partidas.");

            // Processar cada partida
            $progressBar = $this->output->createProgressBar(count($matchIds));
            $progressBar->start();

            $processed = 0;
            $errors = [];

            foreach ($matchIds as $matchId) {
                try {
                    $matchData = $this->riotService->getMatchDetails($matchId, $region);
                    
                    if ($matchData) {
                        $this->analysisService->processMatchData($matchData);
                        $processed++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Erro ao processar partida {$matchId}: " . $e->getMessage();
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine();

            $this->info("Sincronização concluída!");
            $this->info("Partidas processadas: {$processed}/" . count($matchIds));

            if (!empty($errors)) {
                $this->warn("Erros encontrados:");
                foreach ($errors as $error) {
                    $this->line("  - {$error}");
                }
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Erro durante a sincronização: " . $e->getMessage());
            return 1;
        }
    }
}
