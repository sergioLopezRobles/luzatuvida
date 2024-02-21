<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class activaraplicacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activar:aplicacion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activa la ultima aplicacion movil';

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
        try{

            $now = Carbon::now(); 
            $aplicacion = DB::select("SELECT fechaactivacion FROM dispositivos WHERE fechaactivacion = '".$now->format('Y-m-d')."'");
            if($aplicacion != null){               
                $activar = 1;
                $desactivar = 0;
                DB::table('dispositivos')->where('fechaactivacion','=','null')->update([
                    'estatus'=>$desactivar
                ]);
                DB::table('dispositivos')->where('fechaactivacion','!=','null')->update([
                    'estatus'=>$activar,'fechaactivacion'=>null
                ]);
            }

        }catch(\Exception $e){
            \Log::info($e);
        }
    }
}
