<?php

namespace App\Console\Commands;

use App\Clases\contratosGlobal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class crearcobradoreliminadozona extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crear:crearcobradoreliminadozona';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear cobrador eliminado de la zona en el dia actual';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("COMANDO CREAR COBRADOR ELIMINADO ZONA EJECUTADO");

        //Validar dia de la semana
        $now = Carbon::now();
        //$now = Carbon::parse("2023-09-20 00:00:00");
        $contratosGlobal = new contratosGlobal;
        $primerdiaano = Carbon::now()->firstOfYear();

        //Obtener cobradores que hayan sido eliminados
        $cobradores = DB::select("SELECT * FROM cobradoreseliminados");

        foreach ($cobradores as $cobrador){
            //Recorrido de cobradores

            $indice = $cobrador->indice;
            $id_cobradoreliminado = $cobrador->id_usuario;
            $id_franquicia = $cobrador->id_franquicia;
            $id_zona = $cobrador->id_zona;

            try {

                $cobradornormal = DB::select("SELECT u.id FROM users u
                                                  INNER JOIN usuariosfranquicia uf
                                                  ON u.id = uf.id_usuario WHERE u.id_zona = '" . $id_zona . "' AND u.supervisorcobranza = '0'");

                if ($cobradornormal == null) {
                    //No existe cobrador normal en la zona

                    //GENERACION DE NUEVO COBRADOR

                    //Obtener nombre cobrador
                    $nombrecobrador = "Z ";
                    $zona = DB::select("SELECT zona FROM zonas WHERE id = '$id_zona'");
                    $nombrecobrador = $zona == null ? $nombrecobrador . "TEMPORAL" : $nombrecobrador . $zona[0]->zona . " TEMPORAL";

                    $identificadorFranquicia = $contratosGlobal::obtenerIdentificadorFormatoFranquiciaAsistencia($id_franquicia);

                    //Obtener correo cobrador
                    $correocobrador = "";
                    $esUnico = false;
                    while (!$esUnico) {
                        $temporalcorreo = "t" . $identificadorFranquicia . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT) . "@luzatuvida.com.mx";
                        $existente = DB::select("SELECT id FROM users WHERE email = '$temporalcorreo'");
                        if ($existente == null) {
                            $correocobrador = $temporalcorreo;
                            $esUnico = true;
                        }
                    }

                    //Obtener numero de control de asistencia
                    $numControl = "";
                    $fecha = Carbon::parse($now)->format("Y");
                    $year = substr($fecha, -2);
                    //Damos el formato al codigo
                    $codigo = $year . $identificadorFranquicia;
                    $ultimoNumeroControl = DB::select("SELECT codigoasistencia FROM users WHERE codigoasistencia IS NOT NULL AND codigoasistencia LIKE '%$codigo%'
                                                                     ORDER BY id DESC LIMIT 1");
                    if ($ultimoNumeroControl != null) {
                        $numControl .= $ultimoNumeroControl[0]->codigoasistencia + 1;
                    } else {
                        $numControl = $codigo . "000";
                    }

                    //Crear cobrador en tabla users
                    $idUsuario = User::create([
                        'rol_id' => '4',
                        'name' => $nombrecobrador,
                        'email' => $correocobrador,
                        'password' => Hash::make("123456789"),
                        'id_zona' => $id_zona,
                        'tarjeta' => '0000000000000000',
                        'otratarjeta' => '0000000000000000',
                        'fechanacimiento' => $primerdiaano,
                        'sueldo' => '1',
                        'codigoasistencia' => $numControl,
                        'supervisorcobranza' => '0',
                        'id_franquiciaprincipal' => $id_franquicia,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);

                    //Asignar a una franquicia
                    DB::table('usuariosfranquicia')->insert([
                        'id_usuario' => $idUsuario->id, 'id_franquicia' => $id_franquicia, 'created_at' => $now
                    ]);

                    //Registrar el usuario en tabla controlentradasalidausuario
                    DB::table("controlentradasalidausuario")->insert([
                        "id_usuario" => $idUsuario->id,
                        "horaini" => "08:10:00", //Horario inicial por default
                        "horafin" => "08:20:00", //Horario final por default
                        "created_at" => $now
                    ]);

                    //Guardar contratos en tabla contratostemporalessincronizacion al igaul que los abonostemporalessincronizacion
                    $contratosGlobal::insertarDatosTablaContratosTemporalesSincronizacion($id_franquicia, $idUsuario->id, $id_zona, "4");
                    $contratosGlobal::eliminarEInsertarAbonosContratosTemporalesSincronizacionPorUsuarios($idUsuario->id);

                    //Actualizar registros polizacobranza
                    $contratosGlobal::actualizarRegistrosPolizaCobranzaPolizasAnteriores($id_franquicia, $id_cobradoreliminado, $idUsuario->id, $id_zona, true);

                    \Log::info("COBRADOR NUEVO CREADO, NOMBRE: " . $nombrecobrador . " NUMERO DE CONTROL: " . $numControl . " ID: " . $idUsuario->id);

                }

                DB::delete("DELETE FROM cobradoreseliminados WHERE indice = '$indice'");

            } catch (\Exception $e) {
                \Log::info("Error: Comando : crearcobradoreliminadozona: id_franquicia: " . $id_franquicia . " id_usuario: " . $id_cobradoreliminado . "\n" . $e);
                continue;
            }

        }

        \Log::info("COMANDO CREAR COBRADOR ELIMINADO ZONA TERMINADO");
    }
}
