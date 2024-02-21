<?php

namespace App\Console\Commands;

use App\Clases\contratosGlobal;
use Illuminate\Console\Command;

class funcionesextrasglobal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actualizar:funcionesextrasglobal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se ejecutara para funciones de actualizaciones';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        \Log::info("COMANDO FUNCIONES EXTRAS GLOBAL EJECUTADO");

        $contratosGlobal = new contratosGlobal;
/*
        $contratosGlobal::verificarContratosNoExistentesEnContratosTemporalesSincronizacion();
        $contratosGlobal::actualizarContratosEntregaProductoEnCeroYQueHayanSidoEntregados();
        $contratosGlobal::eliminarcontratostemporalessincronizacionrepetidos();
        $contratosGlobal::actualizarpiezasproductos();
        $contratosGlobal::eliminarGarantiasRepetidasTabla("");
        $contratosGlobal::reiniciarNumeroVacantesSucursales();
        $contratosGlobal::actualizarPrecioDolar();
*/
        $contratosGlobal::eliminarimagencodigobarrasusuarios();

        \Log::info("COMANDO FUNCIONES EXTRAS GLOBAL TERMINADO");

    }
}
