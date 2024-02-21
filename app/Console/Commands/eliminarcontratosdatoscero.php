<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class eliminarcontratosdatoscero extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eliminar:eliminarcontratosdatoscero';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borrar contratos con datos en 0 de usuarios que no fueron asignados a franquicias durante 15 dias habiles';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       
        $usuarios = DB::select("SELECT id, rol_id, fechaeliminacion FROM users WHERE fechaeliminacion IS NOT NULL");

        if($usuarios != null) {
            //Se encontro usuarios
            $now = Carbon::now();

            foreach ($usuarios as $usuario) {
                try{
                    $idUsuario = $usuario->id;
                    $rolUsuario = $usuario->rol_id;
                    $diaElimacion = Carbon::parse($usuario->fechaeliminacion)->addDays(15);//Adelantar 15 dias a la fechaeliminacion

                    if (Carbon::parse($now)->format('Y-m-d') === Carbon::parse($diaElimacion)->format('Y-m-d')) {
                        //Dia actual es igual a 15 dias despues de la fechaeliminacion
                        if($rolUsuario == 12 || $rolUsuario == 13) {
                            //Rol asistente/optometrista
                            DB::delete("DELETE FROM contratos WHERE datos = '0' AND id_usuariocreacion = '$idUsuario'");
                        }

                    }
                }catch(\Exception $e){
                    \Log::info("Error: ".$e);
                    continue;
                }

            }
        }

        \Log::info("Los contratos de los usuarios dados de baja se eliminaron correctamente.");

    }
}
