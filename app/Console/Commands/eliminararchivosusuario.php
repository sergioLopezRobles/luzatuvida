<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class eliminararchivosusuario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eliminararchivos:usuario';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Este comando elimina los archivos de un usuario despues de que se descomprime para hacer una vista previa o ser descargado.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {

            //Se eliminar todos los archivos de tipo jpg que fueron extraidos del zip para su visualizacion
            foreach (glob(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/'.'*.jpg') as $filename) {
                unlink($filename);
            }

            foreach (glob(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/'.'*.jpeg') as $filename) {
                unlink($filename);
            }

            //Se eliminar todos los archivos de tipo pdf que fueron extraidos del zip para su visualizacion
            foreach (glob(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/'.'*.pdf') as $filename) {
                unlink($filename);
            }

        }catch (\Exception $e){
            \Log::info("Error: ".$e);
        }

        \Log::info("Los documentos de los archivos se eliminaron correctamente.");
    }
}
