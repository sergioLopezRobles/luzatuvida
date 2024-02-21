<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\abonoscorreo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class abonoscomando extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'correosabonos:abonoscorreo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia el estado de cuenta si abonaron un dia antes en el contrato';

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
            $ayer = new Carbon('yesterday');
            $datoscontrato = DB::select("SELECT SUM(ab.abono) AS totalabono , c.id,c.nombre, ch.fechaentrega,c.total, c.correo,c.telefono,f.telefonofranquicia,p.nombre as nombrepaquete
            FROM contratos c
            INNER JOIN abonos ab ON c.id = ab.id_contrato
            INNER JOIN franquicias f ON f.id = c.id_franquicia
            INNER JOIN historialclinico ch ON c.id = ch.id_contrato
            INNER JOIN paquetes p ON p.id = ch.id_paquete
            WHERE STR_TO_DATE(ab.created_at,'%Y-%m-%d') = '$ayer'
            AND c.estatus_estadocontrato IN(12,2,4,5)
            GROUP BY c.id,c.nombre,ch.fechaentrega,c.total,c.correo,c.telefono,p.nombre,f.telefonofranquicia");

            foreach($datoscontrato as $datosC){
                if($datosC->correo != null){
                    $datos = [
                        "nombrecliente"=>$datosC->nombre,
                        "idcontrato"=>$datosC->id,
                        "entrega"=>$datosC->fechaentrega,
                        "abonos"=>$datosC->totalabono,
                        "total"=>$datosC->total,
                        "saldoanteriro"=>$datosC->total + $datosC->totalabono,
                        "telefonofranquicia"=>$datosC->telefonofranquicia,
                    ];
                 Mail::to($datosC->correo)->queue(new abonoscorreo($datos));
                }
            }

        }catch(\Exception $e){
            \Log::info($e);
        }
    }
}
