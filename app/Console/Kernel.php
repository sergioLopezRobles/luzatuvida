<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\atrasosenabonos',
        'App\Console\Commands\activardesactivarpromociones',
        'App\Console\Commands\activaraplicacion',
        'App\Console\Commands\eliminarmensajes',
        'App\Console\Commands\eliminardocumentoscontrato',
        'App\Console\Commands\abonoscomando',
        'App\Console\Commands\eliminartokens',
        'App\Console\Commands\subscripcionstripe',
        'App\Console\Commands\abonossubscripcionstripe',
        'App\Console\Commands\calculototalescontrato',
        'App\Console\Commands\eliminararchivosusuario',
        'App\Console\Commands\creararchivoszip',
        'App\Console\Commands\eliminararchivozipbaja',
        'App\Console\Commands\contratosatrasosenabonos',
        'App\Console\Commands\contratoscalculototalescontrato',
        'App\Console\Commands\funcionesextrasglobal',
        'App\Console\Commands\eliminarimagenescontratosinnecesariasservidorftp',
        'App\Console\Commands\recorridoabonoscontratostemporalessincronizacion',
        'App\Console\Commands\insertarcontratosabonoscontratostemporalessincronizacion',
        'App\Console\Commands\franquiciaregistrostemporales',
        'App\Console\Commands\recorridofranquiciaregistrostemporales',
        'App\Console\Commands\contratoslistatemporales',
        'App\Console\Commands\crearsupervisionvehiculo',
        'App\Console\Commands\actualizarabonosidzonacontratos',
        'App\Console\Commands\crearcobradoreliminadozona',
        'App\Console\Commands\suspensionautomaticausuarios',
        'App\Console\Commands\terminarpolizaautomatico',
        'App\Console\Commands\registrarsalidausuarios'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        //LOCAL
        //$schedule->command('correosabonos:abonoscorreo')->daily();
        //$schedule->command('comando:eliminartokens')->daily();
        //$schedule->command('actualizar:AbonosAtrasados')->everyMinute();
        //$schedule->command('actualizar:calculototalescontrato')->everyMinute();
        //$schedule->command('actualizar:AbonosAtrasados')->daily();
        //$schedule->command('activardesactivar:promociones')->daily();
        //$schedule->command('activar:aplicacion')->daily();
        //$schedule->command('eliminar:mensajes')->daily();
        //$schedule->command('eliminardocumentos:contrato')->daily();
        //$schedule->command('comando:subscripcionstripe')->everyMinute();
        //$schedule->command('comando:abonossubscripcionstripe')->everyMinute();
        //$schedule->command('crear:crearpoliza')->everyMinute();
        //$schedule->command('eliminararchivos:usuario')->everyMinute();
        //$schedule->command('creararchivoszip:usuario')->everyMinute();
        //$schedule->command('eliminararchivozipbaja:usuarios')->everyMinute();

        //$schedule->command('crear:contratoscalculototalescontrato')->everyMinute();
        //$schedule->command('actualizar:calculototalescontrato')->everyMinute();

        //$schedule->command('crear:ContratosAbonosAtrasados')->everyMinute();
        //$schedule->command('actualizar:AbonosAtrasados')->everyMinute();
        //$schedule->command('actualizar:funcionesextrasglobal')->everyMinute();

        //$schedule->command('eliminar:eliminarimagenescontratosinnecesariasservidorftp')->everyMinute();
        //$schedule->command('crear:insertarcontratosabonoscontratostemporalessincronizacion')->everyMinute();
        //$schedule->command('crear:recorridoabonoscontratostemporalessincronizacion')->everyMinute();
        //$schedule->command('contratoslistatemporales:llenartabla')->everyMinute();
        //$schedule->command('franquiciaregistrostemporales:llenartabla')->everyMinute();
        //$schedule->command('recorridofranquiciaregistrostemporales:ejecutartareasporfranquicia')->everyMinute();
        //$schedule->command('contratoslistatemporales:llenartabla')->everyMinute();
        //$schedule->command('crearsupervisionvehiculo:nuevasupervision')->everyMinute();
        //$schedule->command('actualizar:actualizarabonosidzonacontratos')->everyMinute();
        //$schedule->command('crear:crearcobradoreliminadozona')->everyMinute();
        //$schedule->command('actualizar:suspensionautomaticausuarios')->everyMinute();
        //$schedule->command('actualizar:terminarpolizaautomatico')->everyMinute();
        //$schedule->command('registrarsalidausuarios:registrar')->everyMinute();

        \Log::info("Ejecutando comandos..");
        //SERVIDOR
        //$schedule->command('correosabonos:abonoscorreo')->daily();
        //$schedule->command('comando:eliminartokens')->daily();
        //$schedule->command('actualizar:AbonosAtrasados')->daily();
        //$schedule->command('activardesactivar:promociones')->daily();
        //$schedule->command('activar:aplicacion')->daily();
        //$schedule->command('eliminar:mensajes')->daily();
        //$schedule->command('eliminardocumentos:contrato')->monthlyOn(1, '02:00');
        //$schedule->command('actualizar:calculototalescontrato')->daily();
        //$schedule->command('eliminar:eliminarcontratosdatoscero')->weeklyOn(1, '2:10');
        //$schedule->command('crear:crearpoliza')->dailyAt('07:10');
        //$schedule->command('eliminararchivos:usuario')->monthlyOn(1, '02:00');
        //$schedule->command('creararchivoszip:usuario')->monthlyOn(1, '02:00');
        //$schedule->command('eliminararchivozipbaja:usuarios')->monthlyOn(1, '02:00');
        //$schedule->command('contratoslistatemporales:llenartabla')->everyMinute();
        //$schedule->command('registrarsalidausuarios:registrar')->dailyAt('17:30');

        \Log::info("Comandos ejecutados.");
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
