<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Clases\globalesServicioWeb;

class abonossubscripcionstripe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comando:abonossubscripcionstripe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para crear abonos de subscripciones en stripe';

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

        $contratosSubscripcion = DB::select("SELECT * FROM contratos WHERE estatus_estadocontrato IN (14,15) AND subscripcion IS NOT NULL");

        if($contratosSubscripcion != null) { //Hay contratos con subscripcion?
            //Hay contratos con subscripcion

            foreach ($contratosSubscripcion as $contrato) {

                $idContrato = $contrato->id;
                $idFranquicia = $contrato->id_franquicia;

                $fechaSubscripcion = $contrato->fechasubscripcion;
                $mesSubscripcion = Carbon::parse($fechaSubscripcion); //Ejemplo: 2021-09-10
                $hoy = Carbon::parse('2021-11-11')->format('Y-m-d'); //Ejemplo: 2021-10-10
                $diff = $mesSubscripcion->diffInMonths($hoy); //Ejemplo: 2021-09-10 a 2021-10-10 = 1

                if ($diff >= 1) {
                    //Ya se cumplio (1, 2, 3, 4...) meses

                    $mesSubscripcion = $mesSubscripcion->addMonths($diff);

                    if($mesSubscripcion->diffInDays($hoy) >= 1){ //Ejemplo: 2021-09-10 diferencia entre dias a hoy 2021-10-10 = 0 hasta el dia siguiente ya va a haber diferencia
                        //Ya es un dia despues de que se refleja el pago del mes en la subscripcion

                        $datosstripe = DB::select("SELECT * FROM datosstripe where id_contrato = '$idContrato' AND cancelado = '0'");

                        $stripe = new \Stripe\StripeClient(
                            'sk_test_51JLATqGxL0OdC7rPfHqgBBRoOYbuEz7RaBiJCuoHDJVSGF1nXMuDyY8qzNs90f9jyGt6SPavq0rz8KcxtyDTh2Uh00RYpyoYV6'
                        );

                        if ($stripe->subscriptions->retrieve($datosstripe[0]->id_subscripcion, [])->status == 'active') {
                            //Subscripcion activa

                            $fechaActualizar = Carbon::now();
                            $numTotalPagosStripe = $stripe->charges->all(['customer' => $datosstripe[0]->id_cliente]);

                            if($diff == $numTotalPagosStripe->count()) {
                                //Se dio el pago correctamente del mes

                                $mesesSubscripcion = $contrato->subscripcion;

                                if ($numTotalPagosStripe == $mesesSubscripcion) {
                                    //numPagos es igual a numero de meses - Se pago correctamente todos los pagos (Se debe cancelar la subscripcion)

                                    $totalContrato = $contrato->total;

                                    if ($totalContrato <= 1) {
                                        //Se pago el total del contrato

                                        //Cancelar subscripcion
                                        $stripe->subscriptions->cancel(
                                            $datosstripe[0]->id_subscripcion,
                                            []
                                        );
                                        //Actualizar datosstripe
                                        DB::table("datosstripe")->where("id", "=", $datosstripe[0]->id)->update([
                                            'cancelado' => 2,
                                            'updated_at' => $fechaActualizar
                                        ]);
                                        //Actualizar contrato a liquidado
                                        DB::table("contratos")->where("id", "=", $idContrato)->where("id_franquicia", "=", $idFranquicia)->update([
                                            'total' => 0,
                                            'estatus_estadocontrato' => 5
                                        ]);

                                    }

                                } else {

                                    $numTotalAbonosContrato = DB::select("SELECT count(id) as contador FROM abonos where id_contrato = '$idContrato' AND tipoabono != '7'");
                                    //\Log::info('Total abonos ' . $numTotalAbonosContrato[0]->contador);

                                    $diferencia = $numTotalPagosStripe->count() - $numTotalAbonosContrato[0]->contador;

                                    if ($diferencia > 0) {

                                        $idusuarioc = $datosstripe[0]->id_usuarioc;
                                        $usuario = DB::select("SELECT rol_id FROM users where id = '$idusuarioc'");
                                        $poliza = null;

                                        if ($usuario[0]->rol_id == 4) {
                                            $poliza = 0;
                                        }

                                        $globalesServicioWeb = new globalesServicioWeb;

                                        for ($i = 0; $i < $diferencia; $i++) {

                                            $idAbonoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('abonos', '5');
                                            $idHistorialClinicoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('historialclinico', '5');
                                            $abono = $datosstripe[0]->precioproducto;
                                            $totalabono = $contrato->totalabono + $abono;
                                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                                'totalabono' => $totalabono, 'ultimoabono' => $fechaActualizar, 'estatus_estadocontrato' => 14
                                            ]);
                                            DB::update("UPDATE contratos SET total = CAST((CASE WHEN LENGTH(totalhistorial) = 0 THEN '0' ELSE totalhistorial END) AS DECIMAL(10,2))
                                                                - CAST((CASE WHEN LENGTH(totalabono) = 0 THEN '0' ELSE totalabono END) AS DECIMAL(10,2))
                                                                WHERE id = '$idContrato' AND id_franquicia = '$idFranquicia'");
                                            DB::table('abonos')->insert([
                                                'id' => $idAbonoAlfanumerico, 'folio' => null, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $idusuarioc,
                                                'tipoabono' => 0, 'abono' => $abono, 'poliza' => $poliza, 'metodopago' => 1, 'created_at' => $fechaActualizar
                                            ]);
                                            DB::table('historialcontrato')->insert([
                                                'id' => $idHistorialClinicoAlfanumerico, 'id_usuarioC' => $idusuarioc, 'id_contrato' => $idContrato, 'created_at' => $fechaActualizar,
                                                'cambios' => " Abono la cantidad: '$abono' de subscripción"
                                            ]);
                                            \Log::info('Se guardo correctamente el abono');

                                        }

                                    } else {
                                        \Log::info('Ya se agregaron los abonos correspondientes a la subscripción');
                                    }

                                }

                            }else {

                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'estatus_estadocontrato' => 15
                                ]);

                            }



                        }

                    }else{
                        \Log::info("No ha pasado un dia despues para realizar el pago");
                    }

                }else {
                    \Log::info('No ha pasado mas de un mes');
                }

            }

        }else {
            \Log::info('No hay contratos subscripcion');
        }

    }
}
