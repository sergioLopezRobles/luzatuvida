<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class contratosatrasosenabonos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crear:ContratosAbonosAtrasados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insertar en tabla contratostemporalesatrasoenabonos los contratos con abonos atrasados';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        \Log::info("COMANDO INSERCION CONTRATOS ATRASOS EN ABONOS EJECUTADO");

        $contratos = DB::select("SELECT c.id_franquicia, c.id, c.estatus_estadocontrato, c.fechacobroini, c.fechacobrofin, c.diapago, c.pago as formadepago, c.fechaatraso, c.diaseleccionado, c.fechaentrega,
                                                c.fechacobroiniantes, c.fechacobrofinantes, c.costoatraso, c.total, c.pagosadelantar, c.abonominimo
                                                FROM contratos c WHERE c.id_franquicia != '00000'
                                                AND ((c.estatus_estadocontrato IN (2,4) AND c.pago != 0 AND c.total > 0)
                                                OR (c.id IN (SELECT g.id_contrato FROM garantias g WHERE c.estatus_estadocontrato IN (1,7,9,10,11)
                                                AND c.fechacobroini IS NOT NULL AND g.estadogarantia IN (1,2)))
                                                OR (c.estatus_estadocontrato IN (12,15) AND c.pago != 0 AND c.fechacobroini IS NOT NULL))
                                                ORDER BY c.pago DESC");

        foreach ($contratos as $contrato) {
            //RECORRIDO DE CONTRATOS

            try {

                DB::table("contratostemporalesatrasosenabonos")->insert([
                    "id_franquicia" => $contrato->id_franquicia,
                    "id" => $contrato->id,
                    "estatus_estadocontrato" => $contrato->estatus_estadocontrato,
                    "fechacobroini" => $contrato->fechacobroini,
                    "fechacobrofin" => $contrato->fechacobrofin,
                    "diapago" => $contrato->diapago,
                    "formadepago" => $contrato->formadepago,
                    "fechaatraso" => $contrato->fechaatraso,
                    "diaseleccionado" => $contrato->diaseleccionado,
                    "fechaentrega" => $contrato->fechaentrega,
                    "fechacobroiniantes" => $contrato->fechacobroiniantes,
                    "fechacobrofinantes" => $contrato->fechacobrofinantes,
                    "costoatraso" => $contrato->costoatraso,
                    "total" => $contrato->total,
                    "pagosadelantar" => $contrato->pagosadelantar,
                    "abonominimo" => $contrato->abonominimo,
                    "created_at" => Carbon::now()
                ]);

            } catch (\Exception $e) {
                \Log::info("Error: Comando : contratosatrasoenabonos: " . $contrato->id . "\n" . $e);
                continue;
            }

        }

        \Log::info("COMANDO INSERCION CONTRATOS ATRASOS EN ABONOS TERMINADO");

    }
}
