<?php

namespace App\Console\Commands;

use App\Clases\globalesServicioWeb;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class recorridoabonoscontratostemporalessincronizacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crear:recorridoabonoscontratostemporalessincronizacion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recorrido de abonos de contratos para insertar a tabla abonoscontratostemporalessincronizacion';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        \Log::info("COMANDO RECORRIDO ABONOS CONTRATOS TEMPORALES SINCRONIZACION EJECUTADO");

        $globalesServicioWeb = new globalesServicioWeb();

        $contratos = DB::select("SELECT * FROM contratosabonoscontratostemporalessincronizacion");

        if ($contratos != null) {
            //Tiene contratos creados
            foreach ($contratos as $contrato) {
                //Recorrido contratos

                try {

                    $idContrato = $contrato->id_contrato;
                    \Log::info("idContrato: " . $idContrato);

                    $abonosC = DB::select("SELECT IFNULL(a.id, '') as id, IFNULL(a.folio, '') as folio,
                                                    IFNULL(a.id_contrato, '') as id_contrato, IFNULL(a.id_usuario, '') as id_usuario,
                                                    IFNULL(a.abono, '') as abono, IFNULL(a.adelantos, '0') as adelantos, IFNULL(a.tipoabono, '') as tipoabono,
                                                    IFNULL(a.atraso, '0') as atraso, IFNULL(a.metodopago, '') as metodopago, IFNULL(a.corte, '') as corte,
                                                    IFNULL(a.created_at, '') as created_at, IFNULL(a.updated_at, '') as updated_at
                                                    FROM abonos a WHERE a.id_contrato = '$idContrato'");

                    if ($abonosC != null) {
                        //Hay abonos contrato
                        foreach ($abonosC as $abono) {

                            \Log::info("idContrato: " . $idContrato . " idAbono " . $abono->id);

                            DB::table("abonoscontratostemporalessincronizacion")->insert([
                                "id_usuariocobrador" => $contrato->id_usuario,
                                "id" => $abono->id,
                                "folio" => $abono->folio,
                                "id_contrato" => $abono->id_contrato,
                                "id_usuario" => $abono->id_usuario,
                                "abono" => $abono->abono,
                                "adelantos" => $abono->adelantos,
                                "tipoabono" => $abono->tipoabono,
                                "atraso" => $abono->atraso,
                                "metodopago" => $abono->metodopago,
                                "corte" => $abono->corte,
                                "created_at" => $abono->created_at,
                                "updated_at" => $abono->updated_at
                            ]);
                        }
                    }

                    //Eliminar registro de la tabla contratosabonoscontratostemporalessincronizacion
                    DB::delete("DELETE FROM contratosabonoscontratostemporalessincronizacion
                                        WHERE id_contrato = '" . $contrato->id_contrato . "'
                                        AND id_usuario = '" . $contrato->id_usuario . "'");

                } catch (\Exception $e) {
                    \Log::info("Error: Metodo : recorridoabonoscontratostemporalessincronizacion: " . $contrato->id_contrato . "\n" . $e);
                    continue;
                }
            }
        }

        $globalesServicioWeb::eliminarAbonosContratosTemporalesRepetidosContrato();

        \Log::info("COMANDO RECORRIDO ABONOS CONTRATOS TEMPORALES SINCRONIZACION TERMINADO");

    }
}
