<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class eliminararchivozipbaja extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eliminararchivozipbaja:usuarios';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina los archivos de usuarios dados de baja en el ultimo mes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $ahora = Carbon::now();
        $idUsuarios = DB::select("SELECT s.id FROM users s WHERE NOT EXISTS (SELECT uf.id_usuario FROM usuariosfranquicia uf WHERE uf.id_usuario = s.id)
                                        AND s.fechaeliminacion != 'null' AND DATEDIFF('$ahora',s.fechaeliminacion)  > 30");

        foreach ($idUsuarios as $idUsuario) {
            try {
                //Eliminar el zip correspondinete al usuario
                Storage::disk('disco')->delete('uploads/imagenes/usuarios/zip/'.'Archivos-Usuario'. '-' .$idUsuario->id. '.zip');
            }catch(\Exception $e){
                \Log::info("Error: ".$e);
                continue;
            }
        }

        \Log::info("Los documentos de los archivos se eliminaron correctamente.");
    }
}
