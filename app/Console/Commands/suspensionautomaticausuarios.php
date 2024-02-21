<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class suspensionautomaticausuarios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actualizar:suspensionautomaticausuarios';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizacion de estatus a suspendido en tabla usuarios si en los ultimos 30 dias tiene 4 o mas faltas el usuario';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        \Log::info("COMANDO SUPERVISION AUTOMATICA USUARIOS EJECUTADO");

        $polizas = DB::select("SELECT created_at FROM poliza WHERE id_franquicia = '6E2AA' ORDER BY created_at DESC LIMIT 30");

        if ($polizas != null) {
            //Existen polizas

            $usuarios = DB::select("SELECT u.id as id, u.name as name, uf.id_franquicia as id_franquicia
                                                FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                WHERE u.estatus = '1' AND u.id NOT IN (SELECT e.id_usuario FROM excepciones e WHERE e.tipo = '0')
                                                ORDER BY u.name ASC");

            foreach ($usuarios as $usuario) {
                //Recorrido de usuarios

                $idusuario = $usuario->id;
                $nombreusuario = $usuario->name;
                $idfranquiciausuario = $usuario->id_franquicia;
                $contador = 0;

                try {

                    foreach ($polizas as $poliza) {
                        //Recorrido de polizas

                        \Log::info("COMANDO SUPERVISION AUTOMATICA USUARIOS ID_USUARIO: $idusuario, NOMBRE: $nombreusuario");

                        if ($contador >= 4) {
                            //En los ultimos 30 dias tiene 4 o mas faltas

                            //Actualizar atributo estatus en tabla usuario
                            DB::table('users')->where('id', $idusuario)->update([
                                'estatus' => 0
                            ]);

                            //Insertar movimiento en historialsucursal
                            DB::table('historialsucursal')->insert([
                                'id_usuarioC' => 699, 'id_franquicia' => $idfranquiciausuario,
                                'tipomensaje' => 4, 'created_at' => Carbon::now(), 'cambios' => "Se suspendió el usuario $nombreusuario por 4 o más faltas en los ultimos 30 días", 'seccion' => 0
                            ]);

                            \Log::info("SE SUSPENDIO ID_USUARIO: $idusuario, NOMBRE: $nombreusuario");
                            break;
                        }

                        $fechacreacionpoliza = $poliza->created_at;

                        $asistencias = DB::select("SELECT id_tipoasistencia FROM asistencia
                                                        WHERE id_usuario = '$idusuario' AND STR_TO_DATE(created_at ,'%Y-%m-%d') = STR_TO_DATE('$fechacreacionpoliza','%Y-%m-%d')");

                        if ($asistencias != null) {
                            //Se obtuvo resultado

                            $contadorTemporal = 0;
                            $contadorRegistrosAsistencia = count($asistencias);

                            foreach ($asistencias as $asistencia) {
                                //Recorrido de asistencias

                                if ($asistencia->id_tipoasistencia == 0) {
                                    //Falto
                                    $contadorTemporal++;
                                }

                            }

                            if ($contadorTemporal == $contadorRegistrosAsistencia) {
                                //Falto los en las 1, 2 o 3 sucursales donde se dio de alta la asistencia
                                $contador++;
                            }

                        }

                    }

                } catch (\Exception $e) {
                    \Log::info("Error: Comando : suspensionautomaticausuarios: ID_USUARIO: $idusuario NOMBRE: $nombreusuario\n" . $e);
                    continue;
                }

            }

        }

        \Log::info("COMANDO SUPERVISION AUTOMATICA USUARIOS TERMINADO");

    }
}
