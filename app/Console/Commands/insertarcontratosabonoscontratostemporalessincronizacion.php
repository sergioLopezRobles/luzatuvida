<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class insertarcontratosabonoscontratostemporalessincronizacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crear:insertarcontratosabonoscontratostemporalessincronizacion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insertar contratos abonos en tabla contratosabonoscontratostemporalessincronizacion';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        \Log::info("COMANDO INSERTAR CONTRATOS ABONOS CONTRATOS TEMPORALES SINCRONIZACION EJECUTADO");

        DB::delete("DELETE FROM abonoscontratostemporalessincronizacion");

        $contratos = DB::select("SELECT DISTINCT cts.id, cts.id_usuario
                                        FROM contratostemporalessincronizacion cts
                                        INNER JOIN users u ON cts.id_usuario = u.id
                                        INNER JOIN usuariosfranquicia uf ON cts.id_usuario = uf.id_usuario
                                        WHERE u.rol_id IN (4)");

        if ($contratos != null) {
            //Tiene contratos creados
            foreach ($contratos as $contrato) {
                //Recorrido contratos
                \Log::info("idContrato: " . $contrato->id);
                DB::table("contratosabonoscontratostemporalessincronizacion")->insert([
                    "id_contrato" => $contrato->id,
                    "id_usuario" => $contrato->id_usuario
                ]);
            }
        }

        \Log::info("COMANDO INSERTAR CONTRATOS ABONOS CONTRATOS TEMPORALES SINCRONIZACION TERMINADO");

    }
}
