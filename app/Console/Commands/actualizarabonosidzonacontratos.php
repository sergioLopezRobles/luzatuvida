<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class actualizarabonosidzonacontratos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actualizar:actualizarabonosidzonacontratos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        \Log::info("COMANDO ACTUALIZAR ID_ZONA ABONOS EJECUTADO");

        $abonos = DB::select("SELECT id_contrato FROM abonos WHERE id_zona IS NULL GROUP BY id_contrato");

        if($abonos != null) {
            //Hay abonos por actualizar id_zona

            foreach ($abonos as $abono) {

                $idContrato = $abono->id_contrato;

                try {

                    $contrato = DB::select("SELECT id_zona FROM contratos WHERE id = '$idContrato'");

                    if ($contrato != null) {
                        //Existe contrato
                        $idZonaContrato = $contrato[0]->id_zona;

                        DB::update("UPDATE abonos SET id_zona = '$idZonaContrato' WHERE id_contrato = '$idContrato'");

                        \Log::info("COMANDO ACTUALIZAR ID_ZONA ABONOS, ID_CONTRATO: " . $idContrato);
                    }

                } catch (\Exception $e) {
                    \Log::info("Error: Comando : actualizarabonosidzonacontratos: " . $idContrato . "\n" . $e);
                    continue;
                }

            }

        }

        \Log::info("COMANDO ACTUALIZAR ID_ZONA ABONOS TERMINADO");
    }
}
