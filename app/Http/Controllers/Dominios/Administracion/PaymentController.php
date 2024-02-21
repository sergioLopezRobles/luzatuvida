<?php

namespace App\Http\Controllers\Dominios\Administracion;

use App\Clases\contratosGlobal;
use App\Http\Controllers\Exception;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Omnipay\Omnipay;

class PaymentController extends Controller
{
    public function index($idFranquicia, $idContrato, $abono, $banderacase, $nuevoabono, $cantidadsubscripcion)
    {
        $contrato = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");

        return view('payment', ["idFranquicia" => $idFranquicia, "idContrato" => $idContrato, "abono" => $abono, "banderacase" => $banderacase, "contrato" => $contrato,
                         "nuevoabono" => $nuevoabono, "cantidadsubscripcion" => $cantidadsubscripcion]);
    }

    private function randomPayment($length = 5)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($caracteres);
        $randomId = '';
        for ($i = 0; $i < $length; $i++) {
            $randomId .= $caracteres[rand(0, $charactersLength - 1)];
        }
        return $randomId;
    }

    private function getHistorialContratoPay()
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $temporalId = $this->randomPayment();
            $existente = DB::select("select id from historialcontrato where id = '$temporalId'");
            if (sizeof($existente) == 0) {
                $unico = $temporalId;
                $esUnico = true;
            }
        }
        return $unico;
    }

    private function getAbonoPay()
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $temporalId = $this->randomPayment();
            $existente = DB::select("select id from historialcontrato where id = '$temporalId'");
            if (sizeof($existente) == 0) {
                $unico = $temporalId;
                $esUnico = true;
            }
        }
        return $unico;
    }

    public function charge($idFranquicia, $idContrato, $abono, $banderacase, $abononuevo, $cantidadsubscripcion, Request $request)
    {

        $contrato = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
        $cantidad = request('amount');

        $contratosGlobal = new contratosGlobal;
        $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($contrato[0]->id_zona);

        $folio = null;
        if(((Auth::user()->rol_id) == 4)) {
            $folio = $contratosGlobal::validarSiExisteFolioAlfanumericoEnAbonosContrato($idContrato);
        }

        $totalabono = $contrato[0]->totalabono + $cantidad;
        $total = $contrato[0]->total;
        $correo = $contrato[0]->correo;
        $usuarioId = Auth::user()->id;
        $randomId = $this->getAbonoPay();
        $randomId2 = $this->getHistorialContratoPay();

        if ($cantidad == null) {
            return redirect()->route('payment', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato, "abono" => $abono, "banderacase" => $banderacase, "contrato" => $contrato,
                                           "nuevoabono" => $abononuevo, "cantidadsubscripcion" => $cantidadsubscripcion])->with('alerta', 'La cantidad es un campo obligatorio');
        }

        if ($abononuevo > 0) {

            try {

                $actualizar = Carbon::now();
                $nowparce = Carbon::parse($actualizar)->format('Y-m-d');
                $correoCliente = $contrato[0]->correo;

                // Use Stripe's library to make requests...
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

                $intent = \Stripe\PaymentIntent::create([
                    'payment_method' => $request->paymentMethod,
                    'amount' => $cantidad * 100,
                    'currency' => 'mxn',
                    'payment_method_options' => [
                        'card' => [
                            'installments' => [
                                'enabled' => true,
                                'plan' => [
                                    'count' => $cantidadsubscripcion,
                                    'interval' => 'month',
                                    'type' => 'fixed_count'
                                ]
                            ]
                        ]
                    ],
                    'receipt_email' => $correoCliente,
                    'description' => 'P - ' . $cantidad . ' - ' . $randomId . ' - Subscripción',
                    'confirm' => true
                ]);

                if($intent != null) {

                    //\Log::info("Intent " . $intent);

                    //Guardar datos en tabla datosstripe
                    DB::table('datosstripe')->insert([
                        'id_contrato' => $idContrato, 'id_paymentintent' => $intent->id,
                        'id_paymentmethod' => $request->paymentMethod, 'created_at' => $actualizar
                    ]);

                    $tipoAbono = null;

                    if((Auth::user()->rol_id) == 4) {
                        //Rol cobranza
                        $tipoAbono = 6;
                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'estatus_estadocontrato' => 5, 'fechaentrega' => $actualizar, 'entregaproducto' => 1,
                            'fechasubscripcion' => $nowparce, 'subscripcion' => $cantidadsubscripcion,
                            'totalabono' => $totalabono, 'ultimoabono' => $actualizar
                        ]);
                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $idContrato,
                            'estatuscontrato' => 5,
                            'created_at' => Carbon::now()
                        ]);
                    }else {
                        //Rol diferente a cobranza
                        if($contrato[0]->entregaproducto == 1) {
                            //Ya fue entregado el producto
                            $tipoAbono = 6;
                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'estatus_estadocontrato' => 5, 'fechasubscripcion' => $nowparce, 'subscripcion' => $cantidadsubscripcion,
                                'totalabono' => $totalabono, 'ultimoabono' => $actualizar
                            ]);
                            //Insertar en tabla registroestadocontrato
                            DB::table('registroestadocontrato')->insert([
                                'id_contrato' => $idContrato,
                                'estatuscontrato' => 5,
                                'created_at' => Carbon::now()
                            ]);
                        }else {
                            //No ha sido entregado el producto
                            $tipoAbono = 0;
                            if($contrato[0]->estatus_estadocontrato == 12) {
                                //Estado "ENVIADO"
                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'estatus_estadocontrato' => $contrato[0]->estatus_estadocontrato, 'fechasubscripcion' => $nowparce,
                                    'subscripcion' => $cantidadsubscripcion,
                                    'totalabono' => $totalabono, 'ultimoabono' => $actualizar
                                ]);
                                //Insertar en tabla registroestadocontrato
                                DB::table('registroestadocontrato')->insert([
                                    'id_contrato' => $idContrato,
                                    'estatuscontrato' => $contrato[0]->estatus_estadocontrato,
                                    'created_at' => Carbon::now()
                                ]);
                            }else {
                                //Cambiar estado a "TERMINADO"
                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'estatus_estadocontrato' => 1, 'fechasubscripcion' => $nowparce, 'subscripcion' => $cantidadsubscripcion,
                                    'totalabono' => $totalabono, 'ultimoabono' => $actualizar
                                ]);
                                //Insertar en tabla registroestadocontrato
                                DB::table('registroestadocontrato')->insert([
                                    'id_contrato' => $idContrato,
                                    'estatuscontrato' => 1,
                                    'created_at' => Carbon::now()
                                ]);
                            }
                        }
                    }

                    //Actualizar total contrato
                    DB::update("UPDATE contratos SET total = CAST((CASE WHEN LENGTH(totalhistorial) = 0 THEN '0' ELSE totalhistorial END) AS DECIMAL(10,1))
                                                                - CAST((CASE WHEN LENGTH(totalabono) = 0 THEN '0' ELSE totalabono END) AS DECIMAL(10,1)) WHERE id = '$idContrato'
                                                                AND id_franquicia = '$idFranquicia'");
                    //Agregar abono
                    DB::table('abonos')->insert([
                        'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'tipoabono' => $tipoAbono,
                        'abono' => $cantidad, 'poliza' => null, 'metodopago' => 1, "id_zona" => $contrato[0]->id_zona, 'created_at' => $actualizar
                    ]);

                    foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                        //Recorrido cobradores
                        //Insertar en tabla abonoscontratostemporalessincronizacion
                        DB::table("abonoscontratostemporalessincronizacion")->insert([
                            "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                            "id" => $randomId,
                            "folio" => $folio,
                            "id_contrato" => $idContrato,
                            "id_usuario" => $usuarioId,
                            "abono" => $cantidad,
                            "tipoabono" => $tipoAbono,
                            "metodopago" => 1,
                            "created_at" => Carbon::now()
                        ]);
                    }

                    //Guardar en tabla historialcontrato
                    DB::table('historialcontrato')->insert([
                        'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Abono la cantidad: '$cantidad' de subscripción"
                    ]);
                    $randomId3 = $this->getHistorialContratoId();
                    $cantidadPorMeses = number_format(($cantidad / $cantidadsubscripcion), 2, ".", "");
                    DB::table('historialcontrato')->insert([
                        'id' => $randomId3, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar,
                        'cambios' => " Creo una subscripción por " . $cantidadsubscripcion . " meses con pagos de $" . $cantidadPorMeses
                    ]);

                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('bien', "SUBSCRIPCIÓN CREADA EXITOSAMENTE");
                }

            } catch(\Stripe\Exception\CardException $e) {
                // Since it's a decline, \Stripe\Exception\CardException will be caught
                \Log::info('Status is:' . $e->getHttpStatus());
                \Log::info('Type is:' . $e->getError()->type);
                \Log::info('Code is:' . $e->getError()->code);
                \Log::info('Param is:' . $e->getError()->param);
                \Log::info('Message is 1:' . $e->getError()->message);
                return redirect()->route('payment', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                    "contrato" => $contrato, "nuevoabono" => $abononuevo, "cantidadsubscripcion" => $cantidadsubscripcion])
                    ->with('alerta', 'La tarjeta no es valida por algunas de las siguientes razones:<br>
                                        1- Tarjeta de débito (Necesita ser de crédito).<br>
                                        2- Tarjeta no tiene fondos.<br>
                                        3- Requiere autenticación.');
            } catch (\Stripe\Exception\RateLimitException $e) {
                // Too many requests made to the API too quickly
                \Log::info('Message is 2:' . $e->getError()->message);
                return redirect()->route('payment', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                    "contrato" => $contrato, "nuevoabono" => $abononuevo, "cantidadsubscripcion" => $cantidadsubscripcion])
                    ->with('alerta', 'La tarjeta no es valida por algunas de las siguientes razones:<br>
                                        1- Tarjeta de débito (Necesita ser de crédito).<br>
                                        2- Tarjeta no tiene fondos.<br>
                                        3- Requiere autenticación.');
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                // Invalid parameters were supplied to Stripe's API
                \Log::info('Message is 3:' . $e->getError()->message);
                return redirect()->route('payment', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                    "contrato" => $contrato, "nuevoabono" => $abononuevo, "cantidadsubscripcion" => $cantidadsubscripcion])
                    ->with('alerta', 'La tarjeta no es valida por algunas de las siguientes razones:<br>
                                        1- Tarjeta de débito (Necesita ser de crédito).<br>
                                        2- Tarjeta no tiene fondos.<br>
                                        3- Requiere autenticación.');
            } catch (\Stripe\Exception\AuthenticationException $e) {
                // Authentication with Stripe's API failed
                // (maybe you changed API keys recently)
                \Log::info('Message is 4:' . $e->getError()->message);
                return redirect()->route('payment', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                    "contrato" => $contrato, "nuevoabono" => $abononuevo, "cantidadsubscripcion" => $cantidadsubscripcion])
                    ->with('alerta', 'La tarjeta no es valida por algunas de las siguientes razones:<br>
                                        1- Tarjeta de débito (Necesita ser de crédito).<br>
                                        2- Tarjeta no tiene fondos.<br>
                                        3- Requiere autenticación.');
            } catch (\Stripe\Exception\ApiConnectionException $e) {
                // Network communication with Stripe failed
                \Log::info('Message is 5:' . $e->getError()->message);
                return redirect()->route('payment', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                    "contrato" => $contrato, "nuevoabono" => $abononuevo, "cantidadsubscripcion" => $cantidadsubscripcion])
                    ->with('alerta', 'La tarjeta no es valida por algunas de las siguientes razones:<br>
                                        1- Tarjeta de débito (Necesita ser de crédito).<br>
                                        2- Tarjeta no tiene fondos.<br>
                                        3- Requiere autenticación.');
            } catch (\Stripe\Exception\ApiErrorException $e) {
                // Display a very generic error to the user, and maybe send
                // yourself an email
                \Log::info('Message is 6:' . $e->getError()->message);
                return redirect()->route('payment', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                    "contrato" => $contrato, "nuevoabono" => $abononuevo, "cantidadsubscripcion" => $cantidadsubscripcion])
                    ->with('alerta', 'La tarjeta no es valida por algunas de las siguientes razones:<br>
                                        1- Tarjeta de débito (Necesita ser de crédito).<br>
                                        2- Tarjeta no tiene fondos.<br>
                                        3- Requiere autenticación.');
            } catch (Exception $e) {
                // Something else happened, completely unrelated to Stripe
                \Log::info('Message is 7:' . $e->getError()->message);
                return redirect()->route('payment', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                    "contrato" => $contrato, "nuevoabono" => $abononuevo, "cantidadsubscripcion" => $cantidadsubscripcion])
                    ->with('alerta', 'La tarjeta no es valida por algunas de las siguientes razones:<br>
                                        1- Tarjeta de débito (Necesita ser de crédito).<br>
                                        2- Tarjeta no tiene fondos.<br>
                                        3- Requiere autenticación.');
            }

        }else {

            if ($request->input('stripeToken')) {

                $gateway = Omnipay::create('Stripe');
                $gateway->setApiKey(env('STRIPE_SECRET'));
                $token = $request->input('stripeToken');

                // Parte de agregar abonos a contratos
                $metodopago = 1;
                $adelantar = request('adelantar');
                $adelanto = request('adelanto');
                $adelanto = intval($adelanto);
                $usuarioId = Auth::user()->id;
                $actualizar = Carbon::now();
                $nowparce = Carbon::parse($actualizar)->format('Y-m-d');
                $now = Carbon::now();
                $semana = $now->weekOfYear;
                $contra = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
                $abonoperiodo = DB::select("SELECT a.id, a.adelantos, a.id_contrato, c.fechacobroini, a.created_at
            FROM abonos a
            INNER JOIN contratos c
            ON c.id = a.id_contrato
            WHERE a.id_contrato = '$idContrato'
            AND  STR_TO_DATE(a.created_at,'%Y-%m-%d') >= STR_TO_DATE(c.fechacobroini,'%Y-%m-%d')
            AND  STR_TO_DATE(a.created_at,'%Y-%m-%d') <= STR_TO_DATE(c.fechacobrofin,'%Y-%m-%d')
            AND tipoabono = 3");
                $abonosPeriodo = DB::select("SELECT COALESCE(SUM(a.abono),0) as total
            FROM abonos a
            INNER JOIN contratos c
            ON c.id = a.id_contrato
            WHERE a.id_contrato = '$idContrato'
            AND  STR_TO_DATE(a.created_at,'%Y-%m-%d') >= STR_TO_DATE(c.fechacobroini,'%Y-%m-%d')
            AND  STR_TO_DATE(a.created_at,'%Y-%m-%d') <= STR_TO_DATE(c.fechacobrofin,'%Y-%m-%d')
            AND tipoabono = 3");
                $ec = $contra[0]->idcontratorelacion;
                $costoatraso = $contra[0]->costoatraso;
                $promocionterminada = $contra[0]->promocionterminada;
                $iniantes = $contra[0]->fechacobroini;
                $finantes = $contra[0]->fechacobrofin;
                $totalA = $contra[0]->totalabono;
                $enganche = $contra[0]->enganche;
                $fechaentrega = $contra[0]->fechaentrega;
                $creacion = $contra[0]->created_at;
                $creacionparce = Carbon::parse($creacion)->format('Y-m-d');
                $adelantados = $contra[0]->pagosadelantar + $adelanto;
                $entregaproducto = $contra[0]->entregaproducto;
                $es = $contra[0]->estatus;
                $costoatraso = $contra[0]->costoatraso;
                $estadocontrato = $contra[0]->estatus_estadocontrato;
                $totalproductos = $contra[0]->totalproducto;
                $totalhistorial = $contra[0]->totalhistorial;
                $totalpromocion = $contra[0]->totalpromocion;
                $totalengancheresta = $totalhistorial - 100;
                $totalenganchepromo = $totalpromocion - 100;
                $pago = $contra[0]->pago;
                $totaladelantos = $contra[0]->pagosadelantar + $adelanto;
                $ultimoabono = $contra[0]->ultimoabono;
                $tot2 = $totalA + $abono;
                $tot = number_format($tot2, 1, ".", "");
                $costo2 = $abono + $totalA;
                $costo = number_format($costo2, 1, ".", "");
                $totalcontrato = $contra[0]->total;
                $totalhistorialconenganche = $contra[0]->totalhistorial - 200;
                $totalhistorialsinenganche = $contra[0]->totalhistorial - 300;
                $totalconenganche10 = $contra[0]->total - 200;
                $totalconenganche = number_format($totalconenganche10, 1, ".", "");
                $totalsinenganche10 = $contra[0]->total - 300;
                $totalsinenganche = number_format($totalsinenganche10, 1, ".", "");
                $totalnocontado = $contra[0]->total - 100;
                $totalcr = $totalengancheresta + $totalproductos - $tot;
                $totalcontratoresta = number_format($totalcr, 1, ".", "");
                $totalpresta = $totalenganchepromo + $totalproductos - $tot;
                $totalpromoresta = number_format($totalpresta, 1, ".", "");
                $totalcontra2 = $totalcontrato - $abono;
                $totalcontratoresta2 = number_format($totalcontra2, 1, ".", "");
                $totabono = $totalcontrato + $totalproductos;
                $nowparce = Carbon::parse($now)->format('Y-m-d');
                $ultimoabonoparce = Carbon::parse($ultimoabono)->format('Y-m-d');
                $totalfinal = $totalhistorial + $totalproductos - $tot;
                $descuento = 0;
                if ($pago == 0) {
                    if ($enganche == 1) {
                        $descuento = 200;
                    } else {
                        $descuento = 300;
                    }
                }
                $sumadescuento = $descuento + $abono;
                if ($ec != null) {
                    $contra2 = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$ec'");
                    $pr = $contra2[0]->id_promocion;
                } else {
                    $pr = $contra[0]->id_promocion;
                }

                switch ($pago) {
                    case 0:
                        $totalcontrato = $totalcontrato - 100;
                        break;
                    case 1:
                        if ($abonosPeriodo[0]->total >= 150 && $estadocontrato != 4) {
                            $minimoadelanto = $adelanto * 150;
                        } else {
                            $minimoadelanto = ($adelanto * 150) + 150;
                        }
                        if ($estadocontrato == 4) {
                            if ($abonosPeriodo[0]->total >= 150) {
                                $minimoadelanto = ($adelanto * 150) + $costoatraso;
                            } else {
                                $minimoadelanto = ($adelanto * 150) + 150 + $costoatraso;
                            }
                        }
                        $minimo = 150;
                        break;
                    case 2:
                        if ($abonosPeriodo[0]->total >= 300 && $estadocontrato != 4) {
                            $minimoadelanto = $adelanto * 300;
                        } else {
                            $minimoadelanto = ($adelanto * 300) + 300;
                        }
                        if ($estadocontrato == 4) {
                            if ($abonosPeriodo[0]->total >= 300) {
                                $minimoadelanto = ($adelanto * 300) + $costoatraso;
                            } else {
                                $minimoadelanto = ($adelanto * 300) + 300 + $costoatraso;
                            }
                        }
                        $minimo = 300;
                        break;
                    case 4:
                        if ($abonosPeriodo[0]->total >= 450 && $estadocontrato != 4) {
                            $minimoadelanto = $adelanto * 450;
                        } else {
                            $minimoadelanto = ($adelanto * 450) + 450;
                        }
                        if ($estadocontrato == 4) {
                            if ($abonosPeriodo[0]->total >= 450) {
                                $minimoadelanto = ($adelanto * 450) + $costoatraso;
                            } else {
                                $minimoadelanto = ($adelanto * 450) + 450 + $costoatraso;
                            }
                        }
                        $minimo = 450;
                        break;
                }

                $response = $gateway->purchase([
                    'amount' => $request->input('amount'),
                    'currency' => env('STRIPE_CURRENCY'),
                    'receipt_email' => $correo,
                    'description' => 'P - ' . $cantidad . ' - ' . $randomId,
                    'token' => $token,
                ])->send();

                if ($response->isSuccessful()) {
                    // payment was successful: insert transaction data into the database

                    switch ($banderacase) {
                        case 1:
                            if ($estadocontrato == 0) {
                                $numeroentrega = 0;
                            } else {
                                $numeroentrega = 1;
                            }

                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'tipoabono' => 5,
                                'abono' => $abono, 'poliza' => null, 'metodopago' => $metodopago, "id_zona" => $contra[0]->id_zona, 'created_at' => Carbon::now()
                            ]);

                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                    "id" => $randomId,
                                    "folio" => $folio,
                                    "id_contrato" => $idContrato,
                                    "id_usuario" => $usuarioId,
                                    "abono" => $abono,
                                    "tipoabono" => 5,
                                    "metodopago" => $metodopago,
                                    "created_at" => Carbon::now()
                                ]);
                            }

                            DB::table('historialcontrato')->insert([
                                'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Agrego el abono : '$abono'"
                            ]);

                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'totalabono' => $tot, 'ultimoabono' => $actualizar, 'totalhistorial' => $totalhistorialconenganche, 'entregaproducto' => $numeroentrega
                            ]);
                            $contra5 = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
                            $totalfinal = $contra5[0]->total - $abono - 200;
                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'total' => $totalfinal
                            ]);
                            break;
                        case 2:
                            if ($estadocontrato == 0) {
                                $numeroentrega = 0;
                            } else {
                                $numeroentrega = 1;
                            }

                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'tipoabono' => 5,
                                'abono' => $abono, 'poliza' => null, 'metodopago' => $metodopago, "id_zona" => $contra[0]->id_zona, 'created_at' => Carbon::now()
                            ]);

                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                    "id" => $randomId,
                                    "folio" => $folio,
                                    "id_contrato" => $idContrato,
                                    "id_usuario" => $usuarioId,
                                    "abono" => $abono,
                                    "tipoabono" => 5,
                                    "metodopago" => $metodopago,
                                    "created_at" => Carbon::now()
                                ]);
                            }

                            DB::table('historialcontrato')->insert([
                                'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Agrego el abono : '$abono'"
                            ]);

                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'totalabono' => $costo, 'ultimoabono' => $actualizar, 'totalhistorial' => $totalhistorialsinenganche, 'entregaproducto' => $numeroentrega
                            ]);
                            $contra5 = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
                            $totalfinal = $contra5[0]->total - $abono - 300;
                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'total' => $totalfinal
                            ]);
                            break;
                        case 3:
                            if ($estadocontrato > 1) {
                                $estadocontra = 5;
                                $ent = 1;
                            } else {
                                $estadocontra = $estadocontrato;
                                $ent = 0;
                            }

                            if ($costoatraso > 0) {
                                $cantidadatraso = $costoatraso;
                                $restaatrasocontrato = $costoatraso - $cantidadatraso;
                            } else {
                                $cantidadatraso = null;
                                $restaatrasocontrato = $costoatraso;
                            }
                            $abonosliquidados = DB::select("SELECT * FROM abonos WHERE id_contrato = '$idContrato' AND tipoabono = 6");
                            if ($abonosliquidados != null) {
                                foreach ($abonosliquidados as $ab) {
                                    DB::table('abonos')->where([['id', '=', $ab->id], ['id_contrato', '=', $idContrato]])->update([
                                        'tipoabono' => 0
                                    ]);
                                }
                            }
                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'atraso' => $cantidadatraso,
                                'abono' => $abono, 'metodopago' => $metodopago, 'tipoabono' => 6, "id_zona" => $contra[0]->id_zona, 'created_at' => Carbon::now()
                            ]);

                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                    "id" => $randomId,
                                    "folio" => $folio,
                                    "id_contrato" => $idContrato,
                                    "id_usuario" => $usuarioId,
                                    "abono" => $abono,
                                    "tipoabono" => 6,
                                    "atraso" => $cantidadatraso,
                                    "metodopago" => $metodopago,
                                    "created_at" => Carbon::now()
                                ]);
                            }

                            DB::table('historialcontrato')->insert([
                                'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Agrego el abono : '$abono'"
                            ]);

                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'totalabono' => $costo, 'ultimoabono' => $actualizar, 'total' => 0, 'estatus_estadocontrato' => $estadocontra, 'costoatraso' => $restaatrasocontrato,
                                'entregaproducto' => $ent
                            ]);
                            //Insertar en tabla registroestadocontrato
                            DB::table('registroestadocontrato')->insert([
                                'id_contrato' => $idContrato,
                                'estatuscontrato' => $estadocontra,
                                'created_at' => Carbon::now()
                            ]);
                            break;
                        case 4:
                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'abono' => $abono,
                                'metodopago' => $metodopago, 'tipoabono' => 6, "id_zona" => $contra[0]->id_zona, 'created_at' => Carbon::now()
                            ]);

                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                    "id" => $randomId,
                                    "folio" => $folio,
                                    "id_contrato" => $idContrato,
                                    "id_usuario" => $usuarioId,
                                    "abono" => $abono,
                                    "tipoabono" => 6,
                                    "metodopago" => $metodopago,
                                    "created_at" => Carbon::now()
                                ]);
                            }

                            DB::table('historialcontrato')->insert([
                                'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Agrego el abono : '$abono'"
                            ]);

                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'totalabono' => $costo, 'ultimoabono' => $actualizar, 'total' => 0, 'entregaproducto' => 1
                            ]);
                            break;
                        case 5:
                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'abono' => $abono,
                                'metodopago' => $metodopago, 'tipoabono' => 1, 'poliza' => null, "id_zona" => $contra[0]->id_zona, 'created_at' => Carbon::now()
                            ]);

                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                    "id" => $randomId,
                                    "folio" => $folio,
                                    "id_contrato" => $idContrato,
                                    "id_usuario" => $usuarioId,
                                    "abono" => $abono,
                                    "tipoabono" => 1,
                                    "metodopago" => $metodopago,
                                    "created_at" => Carbon::now()
                                ]);
                            }

                            DB::table('historialcontrato')->insert([
                                'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Agrego el abono : '$abono'"
                            ]);

                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'totalabono' => $costo, 'ultimoabono' => $actualizar, 'total' => 0, 'entregaproducto' => 1, 'enganche' => 1, 'totalhistorial' => $totalengancheresta
                            ]);
                            break;
                        case 6:
                            if ($estadocontrato == 1) {
                                $tipoabonocontado = 4;
                                if ($pago != 0) {
                                    $tipoabonocontado = 1;
                                }
                                DB::table('abonos')->insert([
                                    'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'tipoabono' => $tipoabonocontado,
                                    'id_usuario' => $usuarioId, 'abono' => $abono, 'metodopago' => $metodopago, 'poliza' => null, "id_zona" => $contra[0]->id_zona, 'created_at' => Carbon::now()
                                ]);

                                foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                    //Recorrido cobradores
                                    //Insertar en tabla abonoscontratostemporalessincronizacion
                                    DB::table("abonoscontratostemporalessincronizacion")->insert([
                                        "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                        "id" => $randomId,
                                        "folio" => $folio,
                                        "id_contrato" => $idContrato,
                                        "id_usuario" => $usuarioId,
                                        "abono" => $abono,
                                        "tipoabono" => $tipoabonocontado,
                                        "metodopago" => $metodopago,
                                        "created_at" => Carbon::now()
                                    ]);
                                }

                                DB::table('historialcontrato')->insert([
                                    'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar,
                                    'cambios' => " Abono la cantidad con enganche: '$abono'"
                                ]);

                                if ($pr > 0 && $es == 1 || $ec != null) {
                                    DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                        'totalabono' => $tot, 'enganche' => 1, 'totalhistorial' => $totalengancheresta, 'totalpromocion' => $totalenganchepromo, 'ultimoabono' => $actualizar,
                                        'total' => $totalpromoresta,
                                    ]);
                                } else {
                                    DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                        'totalabono' => $tot, 'enganche' => 1, 'totalhistorial' => $totalengancheresta, 'totalpromocion' => $totalenganchepromo, 'ultimoabono' => $actualizar,
                                        'total' => $totalcontratoresta,
                                    ]);
                                }
                            }
                            break;
                        case 7:
                            $tipoabonocontado = 1;

                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'tipoabono' => $tipoabonocontado,
                                'id_usuario' => $usuarioId, 'abono' => $abono, 'metodopago' => $metodopago, 'poliza' => null, "id_zona" => $contra[0]->id_zona, 'created_at' => Carbon::now()
                            ]);

                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                    "id" => $randomId,
                                    "folio" => $folio,
                                    "id_contrato" => $idContrato,
                                    "id_usuario" => $usuarioId,
                                    "abono" => $abono,
                                    "tipoabono" => $tipoabonocontado,
                                    "metodopago" => $metodopago,
                                    "created_at" => Carbon::now()
                                ]);
                            }

                            DB::table('historialcontrato')->insert([
                                'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Abono la cantidad con enganche: '$abono'"
                            ]);

                            if ($pr > 0 && $es == 1 || $ec != null) {
                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'totalabono' => $tot, 'enganche' => 1, 'totalhistorial' => $totalengancheresta, 'totalpromocion' => $totalenganchepromo, 'ultimoabono' => $actualizar,
                                    'total' => $totalpromoresta,
                                ]);
                            } else {
                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'totalabono' => $tot, 'enganche' => 1, 'totalhistorial' => $totalengancheresta, 'totalpromocion' => $totalenganchepromo, 'ultimoabono' => $actualizar,
                                    'total' => $totalcontratoresta,
                                ]);
                            }
                            break;
                        case 8:
                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'tipoabono' => 2,
                                'abono' => $abono, 'poliza' => null, 'metodopago' => $metodopago, "id_zona" => $contra[0]->id_zona, 'created_at' => Carbon::now()
                            ]);

                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                    "id" => $randomId,
                                    "folio" => $folio,
                                    "id_contrato" => $idContrato,
                                    "id_usuario" => $usuarioId,
                                    "abono" => $abono,
                                    "tipoabono" => 2,
                                    "metodopago" => $metodopago,
                                    "created_at" => Carbon::now()
                                ]);
                            }

                            DB::table('historialcontrato')->insert([
                                'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar,
                                'cambios' => " Abono la cantidad para poder entregar el producto: '$abono'"
                            ]);

                            if ($pr > 0 && $es == 1 || $ec != null) {
                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'totalabono' => $tot, 'entregaproducto' => 1, 'ultimoabono' => $actualizar, 'pagosadelantar' => $adelantados,
                                    'total' => $totalcontratoresta2, 'fechaentrega' => $nowparce
                                ]);
                            } else {
                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'totalabono' => $tot, 'entregaproducto' => 1, 'ultimoabono' => $actualizar, 'pagosadelantar' => $adelantados,
                                    'total' => $totalcontratoresta2, 'fechaentrega' => $nowparce
                                ]);
                            }
                            break;
                        case 9:
                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'tipoabono' => 3,
                                'abono' => $abono, 'poliza' => null, 'metodopago' => $metodopago, "id_zona" => $contra[0]->id_zona, 'created_at' => Carbon::now()
                            ]);

                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                    "id" => $randomId,
                                    "folio" => $folio,
                                    "id_contrato" => $idContrato,
                                    "id_usuario" => $usuarioId,
                                    "abono" => $abono,
                                    "tipoabono" => 3,
                                    "metodopago" => $metodopago,
                                    "created_at" => Carbon::now()
                                ]);
                            }

                            DB::table('historialcontrato')->insert([
                                'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar,
                                'cambios' => " Abono la cantidad para poder entregar el producto: '$abono'"
                            ]);

                            if ($pr > 0 && $es == 1 || $ec != null) {
                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'totalabono' => $tot, 'ultimoabono' => $actualizar, 'pagosadelantar' => $adelantados,
                                    'total' => $totalcontratoresta2,
                                ]);
                            } else {
                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'totalabono' => $tot, 'ultimoabono' => $actualizar, 'pagosadelantar' => $adelantados,
                                    'total' => $totalcontratoresta2,
                                ]);
                            }
                            break;
                        case 10:
                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'tipoabono' => 7,
                                'abono' => $abono, 'adelantos' => $adelanto, 'metodopago' => $metodopago, "id_zona" => $contra[0]->id_zona, 'created_at' => Carbon::now()
                            ]);

                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                    "id" => $randomId,
                                    "folio" => $folio,
                                    "id_contrato" => $idContrato,
                                    "id_usuario" => $usuarioId,
                                    "abono" => $abono,
                                    "adelantos" => $adelanto,
                                    "tipoabono" => 7,
                                    "metodopago" => $metodopago,
                                    "created_at" => Carbon::now()
                                ]);
                            }

                            DB::table('historialcontrato')->insert([
                                'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Abono la cantidad: '$abono'"
                            ]);
                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'totalabono' => $tot, 'ultimoabono' => $actualizar, 'total' => $totalcontratoresta2, 'pagosadelantar' => $adelantados,
                            ]);
                            break;
                        case 11:
                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'tipoabono' => 0,
                                'abono' => $abono, 'adelantos' => $adelanto, 'metodopago' => $metodopago, "id_zona" => $contra[0]->id_zona, 'created_at' => Carbon::now()
                            ]);

                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                    "id" => $randomId,
                                    "folio" => $folio,
                                    "id_contrato" => $idContrato,
                                    "id_usuario" => $usuarioId,
                                    "abono" => $abono,
                                    "adelantos" => $adelanto,
                                    "tipoabono" => 0,
                                    "metodopago" => $metodopago,
                                    "created_at" => Carbon::now()
                                ]);
                            }

                            DB::table('historialcontrato')->insert([
                                'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Abono la cantidad: '$abono'"
                            ]);

                            if ($pr > 0 && $es == 1 || $ec != null) {
                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'totalabono' => $tot, 'ultimoabono' => $actualizar, 'total' => $totalcontratoresta2, 'pagosadelantar' => $adelantados,
                                ]);
                            } else {
                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'totalabono' => $tot, 'ultimoabono' => $actualizar, 'total' => $totalcontratoresta2, 'pagosadelantar' => $adelantados,
                                ]);
                            }
                            break;
                        case 12:
                            $dentroRango = DB::select("SELECT id FROM contratos where id = '$idContrato' AND
                   ((STR_TO_DATE('$now','%Y-%m-%d') = STR_TO_DATE(diaseleccionado,'%Y-%m-%d')) OR
                     (STR_TO_DATE('$now','%Y-%m-%d') >= STR_TO_DATE(fechacobroini,'%Y-%m-%d')) AND STR_TO_DATE('$now','%Y-%m-%d') <= STR_TO_DATE(fechacobrofin,'%Y-%m-%d'))");

                            $totaladeudo = $minimo + $costoatraso;
                            $restoadeudo = $totaladeudo - $abono;
                            $atrasocorto = $costoatraso - $abono;
                            $diaseleccionado = false;
                            if ($contra[0]->diaseleccionado != null || strlen($contra[0]->diaseleccionado) > 0) { //VALIDAMOS SI TENEMOS UN DIA SELECCIONADO
                                if ($now == Carbon::parse($contra[0]->diaseleccionado)) { // VALIDAMOS SI EL DIA SELECCIONADO ES IGUAL AL DIA DE ACTUAL
                                    $diaseleccionado = true;
                                }
                            }

                            //Tabla de abonos ->  periodo
                            $insertarAbono = 0;
                            $insertarTipoAbono = 0; // 0 -> Abono normal
                            $insertarAtraso = 0;
                            $adelantarpagos = 0;
                            $adelantarpagos2 = 0;
                            //Tabla de abonos -> atrasos
                            $insertarAbono2 = null;
                            $insertarTipoAbono2 = null; // 0 -> Abono normal
                            $insertarAtraso2 = null;
                            //Tabla de contrato
                            $atrasoMenosAbono = 0;
                            $atrasoMenosAbono2 = null;
                            $estadodelcontrato = 4;
                            $fechaatraso = null; //para saber los dias de atraso

                            //VALIDAMOS SI EL DIA ACTUAL SE ENCUENTRA ENTRE LA FECHAINI Y FECHA FIN / DIA ACTUAL IGUAL AL DIA SELECCIONADO EN CASO DE EXISTIR
                            if (($now >= Carbon::parse($contra[0]->fechacobroini) && $now <= Carbon::parse($contra[0]->fechacobrofin)) || $diaseleccionado) {

                                if ($abonosPeriodo[0]->total >= $contratosGlobal::calculoCantidadFormaDePago($idFranquicia,$pago)) { //Validamos si se encuentra cubierto lo del periodo
                                    if ($atrasocorto <= 0) { //Si el abono supero lo que ya se tiene de atraso.
                                        $insertarAbono = $abono;
                                        $insertarTipoAbono = 0; // 0 -> Abono normal
                                        $insertarAtraso = $costoatraso; // Lo que se tiene de atraso
                                        $atrasoMenosAbono = 0; //Como el abono supero lo que tenia de atraso entonces se deja en 0.
                                        $estadodelcontrato = 2; //Se elimina el atraso del contrato y vuelve a un estado de entregado.
                                        $fechaatraso = null; // se regresa al campo para el contado de dias atrasados en null
                                        $adelantarpagos = $adelanto;

                                    } else {
                                        $insertarAbono = $abono;
                                        $insertarTipoAbono = 0; // 0 -> Abono normal
                                        $insertarAtraso = $abono;
                                        $atrasoMenosAbono = $costoatraso - $abono;
                                        //Lo que tenemos de atraso menos el abono.
                                    }

                                    //Validamos si los abonos del periodo + el abono ya cubren lo del periodo
                                } else if (($abonosPeriodo[0]->total + $abono) >= $contratosGlobal::calculoCantidadFormaDePago($idFranquicia, $pago)) {
                                    $restanPorPagarPeriodo = $contratosGlobal::calculoCantidadFormaDePago($idFranquicia, $pago) - $abonosPeriodo[0]->total; //abono del periodo menos el total de
                                    // abonos del periodo
                                    $faltaPorPagarMenosElAbonoPeriodo = $restanPorPagarPeriodo - $abono; // Lo que resta por pagar del periodo - el abono.
                                    if ($faltaPorPagarMenosElAbonoPeriodo >= 0) { // Validamos si sigue quedANDo lo del periodo sin pagar
                                        $insertarAbono = $abono;
                                        $insertarTipoAbono = 3; // 0 -> Abono del periodo
                                        $insertarAtraso = 0;
                                        $atrasoMenosAbono = $costoatraso;
                                    } else {
                                        // Se dio dinero de mas de lo del periodo
                                        $insertarAbono = $restanPorPagarPeriodo;
                                        $insertarTipoAbono = 3; //  Abono del periodo
                                        $insertarAtraso = 0;
                                        // $atrasoMenosAbono = $atrasocorto;

                                        if (($costoatraso - abs($faltaPorPagarMenosElAbonoPeriodo)) == 0) { //Validamos si el costo atraso menos el sobrante cubre todo lo atrasado
                                            $insertarAbono2 = abs($faltaPorPagarMenosElAbonoPeriodo);
                                            $insertarTipoAbono2 = 0; // 0 -> Abono normal
                                            $insertarAtraso2 = abs($faltaPorPagarMenosElAbonoPeriodo);
                                            $atrasoMenosAbono2 = 0;
                                            $estadodelcontrato = 2;
                                            $fechaatraso = null; // se regresa al campo para el contado de dias atrasados en null

                                        } else if (($costoatraso - abs($faltaPorPagarMenosElAbonoPeriodo)) > 0) { //Valida si aun queda costo atraso
                                            $insertarAbono2 = abs($faltaPorPagarMenosElAbonoPeriodo);
                                            $insertarTipoAbono2 = 0; // Abono  normal
                                            $insertarAtraso2 = abs($faltaPorPagarMenosElAbonoPeriodo);
                                            $atrasoMenosAbono2 = $costoatraso - abs($faltaPorPagarMenosElAbonoPeriodo);
                                        } else {//Si el costo atraso se supero y ademas sobro para abonar
                                            $insertarAbono2 = abs($faltaPorPagarMenosElAbonoPeriodo);
                                            $insertarTipoAbono2 = 0; //  Abono normal
                                            $insertarAtraso2 = $costoatraso;
                                            $atrasoMenosAbono2 = 0;
                                            $estadodelcontrato = 2;
                                            $adelantarpagos2 = $adelanto;
                                            $fechaatraso = null; // se regresa al campo para el contado de dias atrasados en null
                                        }
                                    }
                                } else if (($abonosPeriodo[0]->total + $abono) < $contratosGlobal::calculoCantidadFormaDePago($idFranquicia, $pago)) { // Validamos si aun no se cubre lo del periodo
                                    $insertarAbono = $abono;
                                    $insertarTipoAbono = 3; //Abono del periodo
                                    $insertarAtraso = 0;
                                    $atrasoMenosAbono = $costoatraso;
                                }


                            } else if ($now > Carbon::parse($contra[0]->fechacobrofin) || $now < Carbon::parse($contra[0]->fechacobroini)) {

                                if ($abono >= $costoatraso) {
                                    $insertarAbono = $abono;
                                    $insertarTipoAbono = 0; // Abono normal
                                    $insertarAtraso = $costoatraso;
                                    $atrasoMenosAbono = 0;

                                } else {
                                    $insertarAbono = $abono;
                                    $insertarTipoAbono = 0; // Abono normal
                                    $insertarAtraso = $abono;
                                    $atrasoMenosAbono = $costoatraso - $abono;
                                }
                            }

                            $insertarAbono = number_format($insertarAbono, 1, ".", "");
                            $insertarAtraso = number_format($insertarAtraso, 1, ".", "");
                            $insertarAbono2 = number_format($insertarAbono2, 1, ".", "");
                            $insertarAtraso2 = number_format($insertarAtraso2, 1, ".", "");
                            $ID2 = $contratosGlobal::getFolioId();
                            $ID3 = $contratosGlobal::getFolioId();

                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId,
                                'adelantos' => $adelantarpagos, 'poliza' => null, 'metodopago' => $metodopago, 'tipoabono' => $insertarTipoAbono, 'abono' => $insertarAbono,
                                'atraso' => $insertarAtraso, "id_zona" => $contra[0]->id_zona, 'created_at' => Carbon::now()
                            ]);

                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                    "id" => $randomId,
                                    "folio" => $folio,
                                    "id_contrato" => $idContrato,
                                    "id_usuario" => $usuarioId,
                                    "abono" => $insertarAbono,
                                    "adelantos" => $adelantarpagos,
                                    "tipoabono" => $insertarTipoAbono,
                                    "atraso" => $insertarAtraso,
                                    "metodopago" => $metodopago,
                                    "created_at" => Carbon::now()
                                ]);
                            }

                            DB::table('historialcontrato')->insert([
                                'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Agrego el abono : '$insertarAbono'"
                            ]);

                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'totalabono' => $tot, 'pagosadelantar' => $adelantados, 'ultimoabono' => $actualizar, 'total' => $totalcontratoresta2, 'costoatraso' => $atrasoMenosAbono,
                                'estatus_estadocontrato' => $estadodelcontrato, 'fechaatraso' => $fechaatraso
                            ]);

                            //Insertar en tabla registroestadocontrato
                            DB::table('registroestadocontrato')->insert([
                                'id_contrato' => $idContrato,
                                'estatuscontrato' => $estadodelcontrato,
                                'created_at' => Carbon::now()
                            ]);


                            if ($insertarAbono2 != null && $insertarAbono2 > 0) {
                                DB::table('abonos')->insert([
                                    'id' => $ID2, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId,
                                    'adelantos' => $adelantarpagos2, 'poliza' => null, 'metodopago' => $metodopago, 'tipoabono' => $insertarTipoAbono2,
                                    'abono' => $insertarAbono2, 'atraso' => $insertarAtraso2, "id_zona" => $contra[0]->id_zona, 'created_at' => Carbon::now()
                                ]);

                                foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                    //Recorrido cobradores
                                    //Insertar en tabla abonoscontratostemporalessincronizacion
                                    DB::table("abonoscontratostemporalessincronizacion")->insert([
                                        "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                        "id" => $ID2,
                                        "folio" => $folio,
                                        "id_contrato" => $idContrato,
                                        "id_usuario" => $usuarioId,
                                        "abono" => $insertarAbono2,
                                        "adelantos" => $adelantarpagos2,
                                        "tipoabono" => $insertarTipoAbono2,
                                        "atraso" => $insertarAtraso2,
                                        "metodopago" => $metodopago,
                                        "created_at" => Carbon::now()
                                    ]);
                                }

                                DB::table('historialcontrato')->insert([
                                    'id' => $ID3, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Agrego el abono : ' $insertarAbono2'"
                                ]);

                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'totalabono' => $tot, 'ultimoabono' => $actualizar, 'total' => $totalcontratoresta2, 'costoatraso' => $atrasoMenosAbono2,
                                    'estatus_estadocontrato' => $estadodelcontrato, 'fechaatraso' => $fechaatraso
                                ]);

                                //Insertar en tabla registroestadocontrato
                                DB::table('registroestadocontrato')->insert([
                                    'id_contrato' => $idContrato,
                                    'estatuscontrato' => $estadodelcontrato,
                                    'created_at' => Carbon::now()
                                ]);
                            }
                            break;
                    }// fin switch
                    $arr_payment_data = $response->getData();

                    $isPaymentExist = Payment::where('payment_id', $arr_payment_data['id'])->first();

                    if (!$isPaymentExist) {
                        $payment = new Payment;
                        $payment->payment_id = $arr_payment_data['id'];
                        $payment->payer_email = $request->input('email');
                        $payment->id_abono = $randomId;
                        $payment->amount = $arr_payment_data['amount'] / 100;
                        $payment->currency = env('STRIPE_CURRENCY');
                        $payment->payment_status = $arr_payment_data['status'];
                        $payment->tipoorigen = 'P';
                        $payment->save();
                    }

                    // return "Payment is successful. Your payment id is: ". $arr_payment_data['id'];
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('bien', "PAGO CON TARJETA EXITOSO");
                } else {
                    // payment failed: display message to customer
                    return back()->with('alerta', 'La tarjeta fue declinada, favor de revisar o elegir otra');
                    return $response->getMessage();
                }
            }

        }
    }

    /* Metodo/Funcion: getHistorialContratoId
    Descripcion: Esta función revisa si el ID alfanumerico que crea la funcion random no esta repetido en la BD es decir busca que sea unico.
    */
    private function getHistorialContratoId()
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $temporalId = $this->generadorRandom2();
            $existente = DB::select("select id from historialcontrato where id = '$temporalId'");
            if (sizeof($existente) == 0) {
                $unico = $temporalId;
                $esUnico = true;
            }
        }
        return $unico;
    }    // Comparar si ya existe el id en la base de datos

    /* Metodo/Funcion: generadorRandom2
    Descripcion: Esta función crea un ID alfanumerico de 5 digitos para registros
    */
    private function generadorRandom2($length = 5)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($caracteres);
        $randomId = '';
        for ($i = 0; $i < $length; $i++) {
            $randomId .= $caracteres[rand(0, $charactersLength - 1)];
        }
        return $randomId;
    }

}
