<?php

namespace App\Console\Commands;

use App\Clases\contratosGlobal;
use Illuminate\Console\Command;

class eliminarimagenescontratosinnecesariasservidorftp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eliminar:eliminarimagenescontratosinnecesariasservidorftp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se eliminaran las imagenes del servidor ftp que no son necesarias para el contrato';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        \Log::info("COMANDO ELIMINAR IMAGENES CONTRATOS INNECESARIAS SERVIDOR FTP EJECUTADO");

        $contratosGlobal = new contratosGlobal;

        //$contratosGlobal::eliminarImagenesInnecesariasContratos('fotoine', 'fotoine');
        //$contratosGlobal::eliminarImagenesInnecesariasContratos('fotoineatras', 'fotoineatras');
        //$contratosGlobal::eliminarImagenesInnecesariasContratos('fotocasa', 'fotocasa');
        //$contratosGlobal::eliminarImagenesInnecesariasContratos('comprobantedomicilio', 'comprobantedomicilio');
        //$contratosGlobal::eliminarImagenesInnecesariasContratos('tarjetapension', 'tarjeta');
        //$contratosGlobal::eliminarImagenesInnecesariasContratos('tarjetapensionatras', 'tarjetapensionatras');
        $contratosGlobal::eliminarImagenesInnecesariasContratos('pagare', 'pagare');
        //$contratosGlobal::eliminarImagenesInnecesariasContratos('fotootros', 'fotootros');

        \Log::info("COMANDO ELIMINAR IMAGENES CONTRATOS INNECESARIAS SERVIDOR FTP TERMINADO");

    }
}
