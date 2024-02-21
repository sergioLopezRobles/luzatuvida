<?php

namespace App\Console\Commands;

use App\Clases\polizaGlobales;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class franquiciaregistrostemporales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'franquiciaregistrostemporales:llenartabla';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Llenara tabla franquiciaregistrostemporales asignando a cada sucursal la tarea de crear poliza y llenar tabla contratoslistatemporales';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("COMANDO FRANQUICIA REGISTROS TEMPORALES : LLENAR TABLA EJECUTADO");

        //Tipo tarea a asignar por sucursal
        //0 -> Crear poliza
        //1 -> llenar tabla contratoslistatemporales

        //Limpiar tabala franquiciaregistrostemporales
        DB::delete("DELETE FROM franquiciaregistrostemporales");

        //Validar dia de la semana
        $now = Carbon::now();
        $numeroDia = $now->dayOfWeekIso;    //Obtenemos el dia de la semana actual

        if($numeroDia == 7){
            //Si es domingo -> Limpiar tabla contratoslistatemporales
            DB::delete("DELETE FROM contratoslistatemporales");
        }

        //Obtener franquicias existentes hasta el momento dieferente a la de pruebas
        $sucursales = DB::select("SELECT f.id FROM franquicias f WHERE f.id != '00000'");

        foreach ($sucursales as $sucursal){

            //Que dia de la semana es?
            if($numeroDia != 7){
                //Lunes a Sabado -> Insertar registros para tarea crear poliza

                //Insertar a sucursal tarea tipo 0 -> crerar poliza
                DB::table('franquiciaregistrostemporales')->insert([
                    'id_franquicia' => $sucursal->id,
                    'tipotarea' => '0',
                    'created_at' => Carbon::now()
                ]);

            }else{
                //Domingo -> Insertar registros para tarea de contratoslistatemporales

                //Insertar a sucusal tarea tipo 1-> llenar tabla contratoslistatemporales
                DB::table('franquiciaregistrostemporales')->insert([
                    'id_franquicia' => $sucursal->id,
                    'tipotarea' => '1',
                    'created_at' => Carbon::now()
                ]);
            }

        }

    }
}
