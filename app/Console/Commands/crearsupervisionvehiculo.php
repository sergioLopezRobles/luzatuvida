<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class crearsupervisionvehiculo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crearsupervisionvehiculo:nuevasupervision';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se ejecutara cada noche para crear una nueva supervision vehicular con datos en blanco';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("COMANDO CREACION DE SUPERVISIONES VEHICULARES EJECUTANDOSE.");

        //Traer todos los vehiculos
        $vehiculos = DB::select("SELECT v.id_franquicia, v.indice FROM vehiculos v WHERE v.estado = '1'");

        foreach ($vehiculos as $vehiculo){
            //Verificar si tiene asignado un cobrador o chofer
            $idFranquicia = $vehiculo->id_franquicia;
            $idVehiculo = $vehiculo->indice;
            $asignacion = DB::select("SELECT vu.id_usuario FROM vehiculosusuarios vu WHERE vu.id_vehiculo = '$idVehiculo' AND vu.id_franquicia = '$idFranquicia'");
            $idUsuario = null;

            if($asignacion != null){
                //Tiene un cobrador o chofer asignado
                $idUsuario = $asignacion[0]->id_usuario;
                $ultimaSupervision = DB::select("SELECT vs.estado FROM vehiculossupervision vs
                                                       WHERE vs.id_usuario = '$idUsuario' AND vs.id_vehiculo= '$idVehiculo' AND vs.id_franquicia = '$idFranquicia' ORDER BY vs.created_at DESC LIMIT 1");
            }else{
                //No cuenta con asignacion
                $ultimaSupervision = DB::select("SELECT vs.estado FROM vehiculossupervision vs
                                                       WHERE vs.id_vehiculo= '$idVehiculo' AND vs.id_franquicia = '$idFranquicia' ORDER BY vs.created_at DESC LIMIT 1");
            }

            if($ultimaSupervision == null || ($ultimaSupervision != null && $ultimaSupervision[0]->estado == 1)){
                //No tiene supervisones o ultima supervision fue aprobada
                DB::table('vehiculossupervision')->insert([
                    'id_franquicia' => $idFranquicia, 'id_usuario' => $idUsuario, 'id_vehiculo' => $idVehiculo,
                    'estado' => '0','created_at' => Carbon::now()
                ]);

            }
        }

        \Log::info("COMANDO CREACION DE SUPERVISIONES VEHICULARES FINALIZÓ.");
    }
}
