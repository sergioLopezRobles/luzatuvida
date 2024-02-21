<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Clases\calculofechaspago;
use App\Clases\contratosGlobal;

class atrasosenabonos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actualizar:AbonosAtrasados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detectar los contratos con abonos atrasados';

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

        \Log::info("COMANDO ATRASOS EN ABONOS EJECUTADO");

        $diaActual = Carbon::now();
//        $diaActual = Carbon::parse('2023-11-10 00:00:00');
        $diaActual = Carbon::parse($diaActual)->format('Y-m-d');
        $calculofechaspago = new calculofechaspago;
        $contratosGlobal = new contratosGlobal;
        $contratos = DB::select("SELECT * FROM contratostemporalesatrasosenabonos ORDER BY indice");
//        $contratos = DB::select("SELECT c.id, c.estatus_estadocontrato, c.fechacobroini, c.fechacobrofin, c.diapago, c.pago as formadepago, c.id_franquicia, c.abonominimo,
//                                        c.pagosadelantar, c.fechaatraso, c.diaseleccionado, c.fechaentrega, c.fechacobroiniantes, c.fechacobrofinantes, c.costoatraso, c.total
//                                        FROM contratos c WHERE c.id = '24002000710012'");

        foreach ($contratos as $contrato) {
            //RECORRIDO DE CONTRATOS

            try {

                \Log::info("COMANDO ATRASOS EN ABONOS ID_CONTRATO: " . $contrato->id);

                $estadoContrato = null;
                $fechaatraso = null;
                $fechaInicial = null;
                $fechaFinal = null;
                $diaSeleccionado = null;
                $fechaCobroIniAntes = $contrato->fechacobroini;
                $fechaCobroFinAntes = $contrato->fechacobrofin;
                $estadoContratoActual = $contrato->estatus_estadocontrato;
                $costoatraso = 0;
                $pagosadelantar = $contrato->pagosadelantar;

                $abonominimocontrato = $contratosGlobal::calculoCantidadFormaDePago($contrato->id_franquicia, $contrato->formadepago);
                $existeIdContratoTablaAbonoMinimoContratos = DB::select("SELECT abonominimo FROM abonominimocontratos WHERE id_contrato = '" . $contrato->id . "'");
                if ($existeIdContratoTablaAbonoMinimoContratos != null) {
                    //Existe contrato en tabla abonominimocontratos
                    $abonominimocontrato = $existeIdContratoTablaAbonoMinimoContratos[0]->abonominimo;
                }
                $abonoMinimoSucursal = $abonominimocontrato;

                $abonoMinimoSucursalEntrega = $contratosGlobal::calculoCantidadFormaDePago($contrato->id_franquicia, 1);
                if ($abonominimocontrato == 250 || $abonominimocontrato == 500 || $abonominimocontrato == 800) {
                    //Abonominimo es igual a 250, 500 o 800
                    $abonoMinimoSucursalEntrega = 250;
                }

                if (is_null($contrato->fechacobroini)) {
                    //CONTRATO ENTREGADO, PERO SIN FECHAS DE PROXIMOS PAGOS

                    $abonoEntrega = DB::select("SELECT SUM(abono) AS totalabono FROM abonos WHERE id_contrato = '" . $contrato->id . "' AND tipoabono = '2'");

                    $adelantos = 0;
                    if (!is_null($abonoEntrega[0]->totalabono)) {
                        //TIENE ABONO DE ENTREGA

                        if ($pagosadelantar == 1) {
                            //pagosadelantar esta activo
                            if ($estadoContratoActual != 4 && $estadoContratoActual != 15) {
                                //Estado del contrato actual es diferente a ABONO ATRASADO O SUPERVISION

                                //Ejem. Es semanal y dieron 600, restar el abono de entrega que son 200 y el resto se divide entre el abono minimo de la forma de pago 600 - 200 = 400 / 200 = 2 adelantos
                                $adelantos = intval(floor(($abonoEntrega[0]->totalabono - $abonoMinimoSucursalEntrega) / $abonoMinimoSucursal));
                                if ($adelantos > 3) {
                                    //Es mas de 3 adelantos
                                    $adelantos = 3; //Solo se pueden hacer 3 adelantos a lo mucho
                                }
                            }
                        }

                    }

                    $arraySiguientesFechas = $calculofechaspago::obtenerCalculoSiguienteFechas($contrato->formadepago, $contrato->fechaentrega,
                        $contrato->fechacobrofin, $contrato->diapago,
                        false, $diaActual, $adelantos);
                    $fechaInicial = $arraySiguientesFechas[0];
                    $fechaFinal = $arraySiguientesFechas[1];
                    $diaSeleccionado = $arraySiguientesFechas[2];
                    $estadoContrato = $estadoContratoActual;

                } else {
                    //CONTRATO ENTREGADO, CON FECHAS

                    $abonoPeriodo = DB::select("SELECT SUM(abono) AS totalabono FROM abonos WHERE id_contrato = '" . $contrato->id . "'
                                                                AND  STR_TO_DATE(created_at,'%Y-%m-%d') >= STR_TO_DATE('" . $contrato->fechacobroini . "','%Y-%m-%d')
                                                                AND STR_TO_DATE(created_at,'%Y-%m-%d') <= STR_TO_DATE('" . $contrato->fechacobrofin . "','%Y-%m-%d')
                                                                AND tipoabono != '7' AND (atraso IS NULL OR atraso <= 0)");

                    if (!is_null($abonoPeriodo[0]->totalabono)) {
                        //TIENE ABONO DENTRO DEL PERIODO

                        $adelantos = 0;
                        if ($pagosadelantar == 1) {
                            //pagosadelantar esta activo
                            if ($estadoContratoActual != 4 && $estadoContratoActual != 15) {
                                //Estado del contrato actual es diferente a ABONO ATRASADO O SUPERVISION

                                //Se obtienen los adelantos ejem. Es semanal y dieron 450 lo que se hace es dividir 150 / 450 = 3 y le restamos 1 para que sean 2 que serian los pagos a adelantar
                                $adelantos = intval(floor(($abonoPeriodo[0]->totalabono / $abonoMinimoSucursal) - 1));
                                if ($adelantos > 3) {
                                    //Es mas de 3 adelantos
                                    $adelantos = 3; //Solo se pueden hacer 3 adelantos a lo mucho
                                }
                            }
                        }

                        $arraySiguientesFechas = $calculofechaspago::obtenerCalculoSiguienteFechas($contrato->formadepago, $contrato->fechaentrega,
                            $contrato->fechacobrofin, $contrato->diapago,
                            false, $diaActual, $adelantos);
                        $fechaInicial = $arraySiguientesFechas[0];
                        $fechaFinal = $arraySiguientesFechas[1];
                        $diaSeleccionado = $arraySiguientesFechas[2];
                        $estadoContrato = 2;

                    }elseif ($diaActual > Carbon::parse($contrato->fechacobrofin)) {
                        //EL DIA ACTUAL ES MAYOR A FECHACOBROFIN

                        $arraySiguientesFechas = $calculofechaspago::obtenerCalculoSiguienteFechas($contrato->formadepago, $contrato->fechaentrega,
                            $contrato->fechacobrofin, $contrato->diapago,
                            true, $diaActual, 0);
                        $fechaInicial = $arraySiguientesFechas[0];
                        $fechaFinal = $arraySiguientesFechas[1];
                        $diaSeleccionado = $arraySiguientesFechas[2];
                        $estadoContrato = 4;
                        if($contrato->fechaatraso != null) {
                            //Ya se tenia una fechaatraso
                            $fechaatraso = $contrato->fechaatraso;
                        }else {
                            //No se tenia fechaatraso
                            $fechaatraso = $contrato->diaseleccionado;
                        }
                        $costoatraso = ($contrato->costoatraso == null ? 0 : $contrato->costoatraso) + $abonoMinimoSucursal;

                    }elseif($diaActual > Carbon::parse($contrato->diaseleccionado)) {
                        //EL DIA ACTUAL ES MAYOR AL DIASELECCIONADO

                        $fechaInicial = $contrato->fechacobroini;
                        $fechaFinal = $contrato->fechacobrofin;
                        $diaSeleccionado = $contrato->diaseleccionado;
                        $estadoContrato = 4;
                        if($contrato->fechaatraso != null) {
                            //Ya se tenia una fechaatraso
                            $fechaatraso = $contrato->fechaatraso;
                        }else {
                            //No se tenia fechaatraso
                            $fechaatraso = $diaSeleccionado;
                        }

                        $fechaCobroIniAntes = $contrato->fechacobroiniantes;
                        $fechaCobroFinAntes = $contrato->fechacobrofinantes;
                        $costoatraso = ($contrato->costoatraso == null ? 0 : $contrato->costoatraso);

                    }else {
                        // EL DIA ACTUAL NO ES MAYOR AL DIASELECCIONADO NI MAYOR A FECHACOBROFIN

                        $fechaInicial = $contrato->fechacobroini;
                        $fechaFinal = $contrato->fechacobrofin;
                        $diaSeleccionado = $contrato->diaseleccionado;
                        $estadoContrato = $estadoContratoActual;
                        $fechaatraso = $contrato->fechaatraso;

                        $fechaCobroIniAntes = $contrato->fechacobroiniantes;
                        $fechaCobroFinAntes = $contrato->fechacobrofinantes;
                        $costoatraso = ($contrato->costoatraso == null ? 0 : $contrato->costoatraso);

                    }

                }

                if ($costoatraso > $contrato->total) { // Validamos si el costo atraso ya supero al total del contrato
                    $costoatraso = $contrato->total;
                }

                if($estadoContratoActual == 1 || $estadoContratoActual == 7 ||
                    $estadoContratoActual == 9 || $estadoContratoActual == 10 ||
                    $estadoContratoActual == 11 || $estadoContratoActual == 15 || $estadoContratoActual == 12) {
                    //estadoContratoActual es igual a TERMINADO, APROBADO, EN PROCESO DE APROBACION, MANOFACTURA, PROCESO DE ENVIO, SUPERVISION O ENVIADO
                    $estadoContrato = $estadoContratoActual;
                }

                DB::table('contratos')->where('id', '=', $contrato->id)->update([
                    'estatus_estadocontrato' => $estadoContrato, 'fechaatraso' => $fechaatraso,
                    'fechacobroini' => $fechaInicial, 'fechacobrofin' => $fechaFinal, 'diaseleccionado' => $diaSeleccionado,
                    'fechacobroiniantes' => $fechaCobroIniAntes, 'fechacobrofinantes' => $fechaCobroFinAntes, 'updated_at' => $diaActual,
                    'costoatraso' => $costoatraso
                ]);

                if($estadoContrato != $estadoContratoActual) {
                    //Insertar en tabla registroestadocontrato
                    DB::table('registroestadocontrato')->insert([
                        'id_contrato' => $contrato->id,
                        'estatuscontrato' => $estadoContrato,
                        'created_at' => Carbon::now()
                    ]);
                }

                //Actualizar contrato en tabla contratostemporalessincronizacion
                $contratosGlobal::actualizarDatosPorContratoTablaContratosTemporalesSincronizacion($contrato->id);

                //Eliminar registro de la tabla contratostemporalesatrasosenabonos
                DB::delete("DELETE FROM contratostemporalesatrasosenabonos WHERE id = '" . $contrato->id . "'");

                //Actualizar datos en tabla contratos lista temporales
                $contratosGlobal::insertarActualizarDatosContratoListaTemporales($contrato->id);

            } catch (\Exception $e) {
                \Log::info("Error: Comando : atrasoenabonos: " . $contrato->id . "\n" . $e);
                continue;
            }

        }

        \Log::info("COMANDO ATRASOS EN ABONOS TERMINADO");

    }
}

