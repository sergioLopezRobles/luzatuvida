<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class contratoscalculototalescontrato extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crear:contratoscalculototalescontrato';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insertar en tabla contratostemporalescalculototalescontrato los contratos para posteriormente sacar su calculo';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        \Log::info("COMANDO INSERCION CONTRATOS CALCULO TOTALES EJECUTADO");

        $contratos = DB::select("SELECT c.id, c.id_franquicia, c.estatus_estadocontrato, c.totalabono, c.totalproducto, c.total, c.promocionterminada FROM contratos c
                                            WHERE c.estatus_estadocontrato IN (0,1,2,4,12)
                                            OR c.id IN (SELECT cts.id FROM contratostemporalessincronizacion cts WHERE cts.estatus_estadocontrato = '5')");

        if($contratos != null) {
            //Hay contratos

            foreach ($contratos as $contrato) {

                try {

                    DB::table("contratostemporalescalculototalescontrato")->insert([
                        "id" => $contrato->id,
                        "id_franquicia" => $contrato->id_franquicia,
                        "estatus_estadocontrato" => $contrato->estatus_estadocontrato,
                        "totalabono" => $contrato->totalabono,
                        "totalproducto" => $contrato->totalproducto,
                        "total" => $contrato->total,
                        "promocionterminada" => $contrato->promocionterminada,
                        "created_at" => Carbon::now()
                    ]);

                }catch(\Exception $e){
                    \Log::info("Error: Comando : contratoscalculototalescontrato: " . $contrato->id . "\n" . $e);
                    continue;
                }

            }

        }

        \Log::info("COMANDO INSERCION CONTRATOS CALCULO TOTALES TERMINADO");

    }
}
