<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class activardesactivarpromociones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activardesactivar:promociones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activa todas las promociones dentro del rango de fechas.';

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
            $promociones = DB::select("SELECT p.id,p.status,p.inicio,p.fin,p.forzado FROM promocion p");
            $activar = 1;
            $desactivar = 2;
            foreach($promociones as $promocion){
                if($now >= Carbon::parse($promocion->inicio) && $now <= Carbon::parse($promocion->fin) && $promocion->forzado == 0){
                    DB::table('promocion')->where('id','=',$promocion->id)->update(['status'=>$activar]);
                }elseif($now > Carbon::parse($promocion->fin)){
                    DB::table('promocion')->where('id','=',$promocion->id)->update(['status'=>$desactivar]);
                }
            }

            $promocionproducto = DB::select("SELECT id,activo,iniciop,finp,forzado FROM producto WHERE id_tipoproducto > 1");
            foreach($promocionproducto as $promocionproducto){
                if($now >= Carbon::parse($promocionproducto->iniciop) && $now <= Carbon::parse($promocionproducto->finp) && $promocionproducto->forzado == 0){
                    DB::table('promocion')->where('id','=',$promocion->id)->update(['status'=>$activar]);
                }elseif($now > Carbon::parse($promocion->fin)){
                    DB::table('promocion')->where('id','=',$promocion->id)->update(['status'=>$desactivar]);
                }
            }

            //Activar desactivar campanias
            $campanias = DB::select("SELECT c.id, c.fechainicio, c.fechafinal FROM campanias c");
            foreach($campanias as $campania){
                if($now >= Carbon::parse($campania->fechainicio) && $now <= Carbon::parse($campania->fechafinal)){
                    DB::table('campanias')->where('id','=',$campania->id)->update(['estado'=> '1']);
                }elseif($now > Carbon::parse($campania->fechafinal)){
                    DB::table('campanias')->where('id','=',$campania->id)->update(['estado'=> '0']);
                }
            }

            \Log::info("Las promociones se activaron/desactivaron");
        }catch(\Exception $e){
            \Log::info("Error: Comando : Activar promociones" .$e);
        }
    }
}
