<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class registrarsalidausuarios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'registrarsalidausuarios:registrar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizara valor registrosalida en tabla de asistencia para usuarios sin registro de salida';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        \Log::info("COMANDO REGISTRAR SALIDA USUARIOS EJECUTADO");

        $franquicias = DB::select("SELECT * FROM franquicias f WHERE f.id != '00000'");

        foreach ($franquicias as $franquicia){
            try {
                //Optener ultima poliza registrada para cada sucursal
                $idFranquicia = $franquicia->id;
                $poliza = DB::select("SELECT p.id FROM poliza p WHERE p.id_franquicia = '$idFranquicia' ORDER BY p.created_at DESC LIMIT 1");
                $idPolizaAsistencia = ($poliza != null)? $poliza[0]->id : "" ;
                $usuariosVentas = DB::select("SELECT u.id, u.name AS nombre FROM users u
                                                    INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                    INNER JOIN asistencia a ON a.id_usuario = u.id
                                                    WHERE uf.id_franquicia = '$idFranquicia' AND u.rol_id IN(12,13)
                                                    AND a.id_poliza = '$idPolizaAsistencia' AND a.registrosalida IS NULL AND a.id_tipoasistencia != 0
                                                    ORDER BY nombre ");

                //Actualizar registro salida por usuario
                foreach ($usuariosVentas as $usuario){
                    DB::table("asistencia")->where("id_poliza", "=", $idPolizaAsistencia)
                        ->where("id_usuario","=",$usuario->id)
                        ->where("registrosalida","=",NULL)
                        ->update(["registrosalida" => Carbon::now(), "updated_at" => Carbon::now() ]);
                }

                //Guardar movimiento en historial sucursal
                DB::table('historialsucursal')->insert([
                    'id_usuarioC' => '699', 'id_franquicia' => $idFranquicia,
                    'tipomensaje' => "0", 'created_at' => Carbon::now(),
                    'cambios' => "Registró hora de salida a usuarios faltantes por checar salida.", 'seccion' => "0"
                ]);

            } catch (\Exception $e) {
                \Log::info("Error: Comando : registrarsalidausuarios: " . $idFranquicia . "\n" . $e);
                continue;
            }
        }

        \Log::info("COMANDO REGISTRAR SALIDA USUARIOS TERMINADO");

    }
}
