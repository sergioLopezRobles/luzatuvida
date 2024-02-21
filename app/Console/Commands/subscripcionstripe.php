<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class subscripcionstripe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comando:subscripcionstripe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para crear subscripciones en stripe';

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

        $contratosSubscripcion = DB::select("SELECT * FROM contratos WHERE estatus_estadocontrato = '14' AND subscripcion IS NOT NULL");

        if($contratosSubscripcion != null) { //Hay contratos con subscripcion?
            //Hay contratos con subscripcion

            foreach ($contratosSubscripcion as $contrato) {

                $idContrato = $contrato->id;

                $fechaSubscripcion = $contrato->fechasubscripcion;
                $mesSubscripcion = Carbon::parse($fechaSubscripcion);
                $hoy = Carbon::now()->format('Y-m-d');
                $diff = $mesSubscripcion->diffInMonths($hoy);

                if ($diff == 1) {
                    //Ya paso un mes

                    $datosstripe = DB::select("SELECT * FROM datosstripe where id_contrato = '$idContrato' AND cancelado = '0'");

                    if ($datosstripe != null) { //Hay registro en la tabla de datosstripe?
                        //Hay registro

                        $idSubscripcion = $datosstripe[0]->id_subscripcion;

                        if($idSubscripcion == null) {
                            //No se ha subscrito

                            \Stripe\Stripe::setApiKey('sk_test_51JLATqGxL0OdC7rPfHqgBBRoOYbuEz7RaBiJCuoHDJVSGF1nXMuDyY8qzNs90f9jyGt6SPavq0rz8KcxtyDTh2Uh00RYpyoYV6');

                            $idDatosStripe = $datosstripe[0]->id;
                            $idCliente = $datosstripe[0]->id_cliente;
                            $idPaymentMethod = $datosstripe[0]->id_paymentmethod;
                            $idPrecioProducto = $datosstripe[0]->id_precio_producto;

                            //Creacion de subcripcion en stripe
                            try {

                                $subscription = \Stripe\Subscription::create([
                                    'customer' => $idCliente,
                                    'default_payment_method' => $idPaymentMethod,
                                    'items' => [[
                                        'price' => $idPrecioProducto
                                    ]],
                                ]);

                                if ($subscription != null) {
                                    DB::table('datosstripe')->where('id', '=', $idDatosStripe)->update([
                                        'id_subscripcion' => $subscription->id
                                    ]);
                                    \Log::info('Subscripción creada correctamente');
                                }

                            } catch (\Stripe\Exception\CardException $e) {
                                // Since it's a decline, \Stripe\Exception\CardException will be caught
                                \Log::info('Status is:' . $e->getHttpStatus());
                                \Log::info('Type is:' . $e->getError()->type);
                                \Log::info('Code is:' . $e->getError()->code);
                                \Log::info('Param is:' . $e->getError()->param);
                                \Log::info('Message is 1:' . $e->getError()->message);
                            } catch (\Stripe\Exception\RateLimitException $e) {
                                // Too many requests made to the API too quickly
                                \Log::info('Message is 2:' . $e->getError()->message);
                            } catch (\Stripe\Exception\InvalidRequestException $e) {
                                // Invalid parameters were supplied to Stripe's API
                                \Log::info('Message is 3:' . $e->getError()->message);
                            } catch (\Stripe\Exception\AuthenticationException $e) {
                                // Authentication with Stripe's API failed
                                // (maybe you changed API keys recently)
                                \Log::info('Message is 4:' . $e->getError()->message);
                            } catch (\Stripe\Exception\ApiConnectionException $e) {
                                // Network communication with Stripe failed
                                \Log::info('Message is 5:' . $e->getError()->message);
                            } catch (\Stripe\Exception\ApiErrorException $e) {
                                // Display a very generic error to the user, and maybe send
                                // yourself an email
                                \Log::info('Message is 6:' . $e->getError()->message);
                            } catch (Exception $e) {
                                // Something else happened, completely unrelated to Stripe
                                \Log::info('Message is 7:' . $e->getError()->message);
                            }

                        }else {
                            \Log::info('Ya se encuentra subscrito el contrato con el id_subscripcion: ' . $datosstripe[0]->id_subscripcion);
                        }

                    }else {
                        \Log::info('No se encontro registro en tabla datosstripe');
                    }

                }else {
                    \Log::info('No ha llegado la fecha para subscripcion');
                }

            }

        }else {
            \Log::info('No hay contratos subscripcion');
        }

    }
}
