<?php

namespace App\Console\Commands;

use App\Clases\contratosGlobal;
use App\Clases\polizaGlobales;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class recorridofranquiciaregistrostemporales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recorridofranquiciaregistrostemporales:ejecutartareasporfranquicia';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecutara las tareas registradas en la tabla de franquiciaregistrostemporales eliminando el registro de misma tabla conforma termine la tarea de la sucursal';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $franquiciapendiente = DB::select("SELECT * FROM franquiciaregistrostemporales WHERE tipotarea = 0 AND bandera = 1 ORDER BY indice LIMIT 1");

        if($franquiciapendiente == null) {
            //No hay franquicia pendiente

            $franquicia = DB::select("SELECT * FROM franquiciaregistrostemporales WHERE tipotarea = 0 AND bandera = 0 ORDER BY indice LIMIT 1");

            if ($franquicia != null) {
                //Falta franquicia por crear poliza

                $indice = $franquicia[0]->indice;
                $idFranquicia = $franquicia[0]->id_franquicia;

                DB::update("UPDATE franquiciaregistrostemporales SET bandera = '1' WHERE indice = '$indice'");

                try {
                    //Ejecutar tarea crear poliza
                    $polizaGlobales = new polizaGlobales();
                    $polizaGlobales::crearpoliza($idFranquicia);

                    //Eliminar registro de tabla franquiciaregistrostemporales una vez ejecutada la funcion
                    DB::delete("DELETE FROM franquiciaregistrostemporales WHERE indice = '$indice'");

                } catch (\Exception $e) {
                    \Log::info("Error: Comando : recorridofranquiciaregistrostemporales: " . $idFranquicia . "\n" . $e);
                }

            }

        }
    }
}
