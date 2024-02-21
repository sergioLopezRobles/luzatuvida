<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class terminarpolizaautomatico extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actualizar:terminarpolizaautomatico';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para actualizar poliza a terminado con horario especifico';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("COMANDO TERMINAR POLIZA AUTOMATICO EJECUTADO");

        $accionesbanderasfranquiciahoraterminacionpoliza = DB::select("SELECT id_franquicia, estatus FROM accionesbanderasfranquicia WHERE tipo = '1' ORDER BY created_at DESC");

        if($accionesbanderasfranquiciahoraterminacionpoliza != null) {
            //accionesbanderasfranquiciahoraterminacionpoliza es diferente de null

            foreach ($accionesbanderasfranquiciahoraterminacionpoliza as $horaterminacionpoliza) {

                $idFranquicia = $horaterminacionpoliza->id_franquicia;
                $estatus = $horaterminacionpoliza->estatus;

                try {

                    $diaActual = Carbon::now();
                    $horaActual = Carbon::parse($diaActual)->format('H:i:s');
                    $hoyNumero = Carbon::parse($diaActual)->dayOfWeekIso;

                    if ($estatus != null) {
                        //Estatus es diferente de vacio

                        if ($hoyNumero != 7) {
                            //Dia es diferente de domingo

                            if (Carbon::parse($horaActual)->gt(Carbon::parse($estatus . ":00:00"))) {
                                //La hora ya pasó

                                $poliza = DB::select("SELECT id, estatus FROM poliza WHERE id_franquicia = '$idFranquicia' ORDER BY created_at DESC LIMIT 1");

                                if ($poliza != null) {
                                    //Existe poliza

                                    $idPoliza = $poliza[0]->id;
                                    $estatusPoliza = $poliza[0]->estatus;

                                    if ($estatusPoliza == 0) {
                                        //Poliza no ha sido terminada

                                        $idUsuario = 699; //idUsuario de sistemas automatico
                                        $datosusuario = DB::select("SELECT name FROM users WHERE id = '$idUsuario'");
                                        if ($datosusuario != null) {
                                            //Existe usuario
                                            $nameUsuario = $datosusuario[0]->name;

                                            DB::table("poliza")->where("id", "=", $idPoliza)->update([
                                                "estatus" => 2,
                                                "updated_at" => $diaActual,
                                                "realizo" => $nameUsuario
                                            ]);
                                            DB::table('historialpoliza')->insert([
                                                'id_usuarioC' => $idUsuario, 'id_poliza' => $idPoliza, 'created_at' => $diaActual,
                                                'cambios' => "El usuario '$nameUsuario' entregó la poliza por hora automatica."
                                            ]);
                                        }

                                        \Log::info("COMANDO TERMINAR POLIZA AUTOMATICO, ID_POLIZA: " . $idPoliza);

                                    }
                                }

                            }

                        }

                    }

                } catch (\Exception $e) {
                    \Log::info("Error: Comando : terminarpolizaautomatico: " . $idFranquicia . "\n" . $e);
                    continue;
                }

            }

        }

        \Log::info("COMANDO TERMINAR POLIZA AUTOMATICO TERMINADO");
    }
}
