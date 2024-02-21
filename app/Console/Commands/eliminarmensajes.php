<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class eliminarmensajes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eliminar:mensajes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para eliminar los mensajes, se eliminan despues de la fecha.';

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
        $hoy = Carbon::now();
        $hoy = Carbon::parse($hoy)->format('Y-m-d');
        try{
                DB::delete("DELETE FROM mensajes WHERE fechalimite IS NOT NULL AND fechalimite < '$hoy' ");
        }catch(\Exception $e){
            \Log::info("Error: ".$e);
        }
        \Log::info("Los mensajes se eliminaron correctamente.");
    }
}
