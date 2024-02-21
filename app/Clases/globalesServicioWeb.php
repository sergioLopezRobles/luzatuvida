<?php

namespace App\Clases;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class globalesServicioWeb
{

    public static function insertarOModificarRegistrosTablas($jsonDatos, $idFranquicia, $id_usuario, $idunico, $modelo)
    {

        $contratosGlobal = new contratosGlobal;

        $usuario = DB::select("SELECT rol_id FROM users WHERE id = '$id_usuario'");

        $rol_id = null;
        if ($usuario != null) {
            $rol_id = $usuario[0]->rol_id;
        }

        $fechaActual = Carbon::now()->format('Y-m-d H:i:s');

        $todosLosDatos = json_decode($jsonDatos, true);//Obtenemos todos los datos

        if (!empty($todosLosDatos[0]['contratos'])) {
            //Json contratos es diferente a vacio
            $jsonContratos = self::obtenerJsonDecodificado($todosLosDatos[0]['contratos']);//Obtenemos solo los contratos
        }
        $jsonAbonos = null;
        if (!empty($todosLosDatos[0]['abonos'])) {
            //Json abonos es diferente a vacio
            $jsonAbonos = self::obtenerJsonDecodificado($todosLosDatos[0]['abonos']);//Obtenemos los abonos
        }
        if (!empty($todosLosDatos[0]['historialesclinicos'])) {
            //Json historialesclinicos es diferente a vacio
            $jsonHistorialesClinicos = self::obtenerJsonDecodificado($todosLosDatos[0]['historialesclinicos']);//Obtenemos los historiales clinicos
        }
        if (!empty($todosLosDatos[0]['contratosproductos'])) {
            //Json contratosproductos es diferente a vacio
            $jsonContratosProductos = self::obtenerJsonDecodificado($todosLosDatos[0]['contratosproductos']);;//Obtenemos los productos del contrato
        }
        if (!empty($todosLosDatos[0]['productoseliminados'])) {
            //Json productoseliminados es diferente a vacio
            $jsonProductosEliminados = self::obtenerJsonDecodificado($todosLosDatos[0]['productoseliminados']);//Obtenemos los productos eliminados
        }
        $jsonAbonosEliminados = null;
        if (!empty($todosLosDatos[0]['abonoseliminados'])) {
            //Json abonoseliminados es diferente a vacio
            $jsonAbonosEliminados = self::obtenerJsonDecodificado($todosLosDatos[0]['abonoseliminados']);//Obtenemos los abonos eliminados
        }
        if (!empty($todosLosDatos[0]['historialcontratos'])) {
            //Json historialcontratos es diferente a vacio
            $jsonHistorialContratos = self::obtenerJsonDecodificado($todosLosDatos[0]['historialcontratos']);//Obtenemos los historiales de movimientos de los contratos
        }
        if (!empty($todosLosDatos[0]['promocioncontratos'])) {
            //Json promocioncontratos es diferente a vacio
            $jsonPromocionContratos = self::obtenerJsonDecodificado($todosLosDatos[0]['promocioncontratos']);//Obtenemos las promociones de los contratos
        }
        if (!empty($todosLosDatos[0]['payments'])) {
            //Json payments es diferente a vacio
            $jsonPayments = self::obtenerJsonDecodificado($todosLosDatos[0]['payments']);//Obtenemos los payments
        }
        if (!empty($todosLosDatos[0]['promocioneseliminadas'])) {
            //Json promocioneseliminadas es diferente a vacio
            $jsonPromocionesEliminadas = self::obtenerJsonDecodificado($todosLosDatos[0]['promocioneseliminadas']);//Obtenemos las promociones eliminadas
        }
        if (!empty($todosLosDatos[0]['datosstripe'])) {
            //Json datosstripe es diferente a vacio
            $jsonDatosStripe = self::obtenerJsonDecodificado($todosLosDatos[0]['datosstripe']);//Obtenemos los datos stripe
        }
        if (!empty($todosLosDatos[0]['garantias'])) {
            //Json garantias es diferente a vacio
            $jsonGarantias = self::obtenerJsonDecodificado($todosLosDatos[0]['garantias']);//Obtenemos las garantias
        }
        if (!empty($todosLosDatos[0]['ruta'])) {
            //Json ruta es diferente a vacio
            $jsonRuta = self::obtenerJsonDecodificado($todosLosDatos[0]['ruta']);//Obtenemos las rutas

        }
        if (!empty($todosLosDatos[0]['historialessinconversion'])) {
            //Json historialessinconversion es diferente a vacio
            $jsonHistorialesSinConversion = self::obtenerJsonDecodificado($todosLosDatos[0]['historialessinconversion']);//Obtenemos los historialessinconversion
        }
        if (!empty($todosLosDatos[0]['buzon'])) {
            //Json buzon es diferente a vacio
            $jsonBuzon = self::obtenerJsonDecodificado($todosLosDatos[0]['buzon']);//Obtenemos los buzones
        }

        if (!empty($todosLosDatos[0]['autorizacionesarmazon'])) {
            //Json autorizacionesarmazon es diferente a vacio
            $jsonAutorizacionesArmazon = self::obtenerJsonDecodificado($todosLosDatos[0]['autorizacionesarmazon']);//Obtenemos las autorizacionesarmazon
        }

        if (!empty($todosLosDatos[0]['historialfotoscontratos'])) {
            //Json historialfotoscontratos es diferente a vacio
            $jsonHistorialMovimientosFotosContratos = self::obtenerJsonDecodificado($todosLosDatos[0]['historialfotoscontratos']);//Obtenemos los movimientos con fotos del contrato
        }

        $jsonContratosListaNegra = null;
        if (!empty($todosLosDatos[0]['contratoslistanegra'])) {
            //Json $jsonContratosListaNegra es diferente a vacio
            $jsonContratosListaNegra = self::obtenerJsonDecodificado($todosLosDatos[0]['contratoslistanegra']);//Obtenemos los contratos registrados en lista negra
        }

        $jsonNotasCobranza = null;
        if (!empty($todosLosDatos[0]['notascobranza'])) {
            //Json $jsonNotasCobranza es diferente a vacio
            $jsonNotasCobranza = self::obtenerJsonDecodificado($todosLosDatos[0]['notascobranza']);//Obtenemos las notas del cobrador
        }

        $jsonAsistencia = null;
        if (!empty($todosLosDatos[0]['asistencia'])) {
            //Json $jsonAsistencia es diferente a vacio
            $jsonAsistencia = self::obtenerJsonDecodificado($todosLosDatos[0]['asistencia']);//Obtenemos registro de salida
        }
        $jsonAgendaCitas = null;
        if (!empty($todosLosDatos[0]['agendacitas'])) {
            //Json $jsonAgendaCitas es diferente a vacio
            $jsonAgendaCitas = self::obtenerJsonDecodificado($todosLosDatos[0]['agendacitas']);//Obtenemos registro de salida
        }

        if (!empty($jsonAutorizacionesArmazon)) {
            //$jsonAutorizacionesArmazon es diferente a vacio

            foreach ($jsonAutorizacionesArmazon as $autorizacionArmazon) {
                //Recorrido de $jsonAutorizacionesArmazon

                try {

                    $contrato = DB::select("SELECT created_at, estatus_estadocontrato FROM contratos
                                          WHERE id_franquicia = '$idFranquicia' AND id = '" . $autorizacionArmazon['ID_CONTRATO'] . "'");

                    if ($contrato != null) {
                        //Existe contrato

                        $armazon = DB::select("SELECT * FROM producto p WHERE p.id = '" . $autorizacionArmazon['ID_PRODUCTO'] . "'");

                        if ($armazon != null) {
                            //Existe armazon

                            $FOLIOPOLIZA = self::validacionDeNulo($autorizacionArmazon['FOLIOPOLIZA']);

                            $mensaje = "";
                            if ($autorizacionArmazon['ID_PRODUCTO'] == 9) {
                                $mensaje = " por poliza";
                            }

                            //Insertar solicitud de autorizacion
                            $idAutorizacion = DB::table('autorizaciones')->insertGetId([
                                'id_contrato' => $autorizacionArmazon['ID_CONTRATO'], 'id_usuarioC' => $autorizacionArmazon['ID_USUARIOC'], 'id_franquicia' => $idFranquicia,
                                'fechacreacioncontrato' => $contrato[0]->created_at,
                                'estadocontrato' => $contrato[0]->estatus_estadocontrato,
                                'mensaje' => "Solicitó agregar el armazón " . $armazon[0]->id . " | " . $armazon[0]->nombre . " | " . $armazon[0]->color . $mensaje,
                                'estatus' => '0', 'tipo' => $autorizacionArmazon['TIPO'], 'created_at' => $autorizacionArmazon['CREATED_AT']
                            ]);

                            //Insertar registro en tabla autorizacionarmazonlaboratorio
                            DB::table('autorizacionarmazonlaboratorio')->insert([
                                'id_autorizacion' => $idAutorizacion,
                                'id_armazon' => $armazon[0]->id,
                                'piezas' => $autorizacionArmazon['PIEZAS'],
                                'foliopoliza' => $FOLIOPOLIZA,
                                'created_at' => $autorizacionArmazon['CREATED_AT']
                            ]);

                            //Insertamos el movimiento
                            DB::table('historialcontrato')->insert([
                                'id' => self::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $autorizacionArmazon['ID_USUARIOC'],
                                'id_contrato' => $autorizacionArmazon['ID_CONTRATO'], 'created_at' => $autorizacionArmazon['CREATED_AT'],
                                'cambios' => "Solicitó agregar el armazón " . $armazon[0]->id . " | " . $armazon[0]->nombre . " | " . $armazon[0]->color . $mensaje,
                                'tipomensaje' => '3']);

                        }

                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonAutorizacionesArmazon: " . $autorizacionArmazon['ID_CONTRATO'] . "\n" . $e);
                    continue;
                }

            }

        }//AUTORIZACIONESARMAZONES

        //RECORRIDO DE JSONS PARA INSERTAR O ACTUALIZAR REGISTROS EN TABLAS
        if($rol_id == 4) {
            self::insertarActualizarAbonosYAbonosEliminados($idFranquicia, $id_usuario, $rol_id, $jsonAbonos, $jsonAbonosEliminados);
            self::insertarcontratoslistanegra($jsonContratosListaNegra);
            self::insertarActualizarEliminarNotasCobranza($jsonNotasCobranza, $id_usuario);
        }//ABONOS Y ABONOS ELIMINADOS (COBRANZA) - LISTA NEGRA CONTRATOS

        if (!empty($jsonContratos)) {
            //jsonContratos es diferente a vacio

            foreach ($jsonContratos as $contrato) {
                //Recorrido de jsonContratos

                try {

                    $PAGO = self::validacionDeNulo($contrato['PAGO']);
                    $TARJETAFRENTE = self::validacionDeNulo($contrato['TARJETAFRENTE']);
                    if ($TARJETAFRENTE != null) {
                        $TARJETAFRENTE = 'uploads/imagenes/contratos/tarjetapension/' . $TARJETAFRENTE;
                    }
                    $TARJETAATRAS = self::validacionDeNulo($contrato['TARJETAATRAS']);
                    if ($TARJETAATRAS != null) {
                        $TARJETAATRAS = 'uploads/imagenes/contratos/tarjetapensionatras/' . $TARJETAATRAS;
                    }
                    $FOTOOTROS = self::validacionDeNulo($contrato['FOTOOTROS']);
                    if ($FOTOOTROS != null) {
                        $FOTOOTROS = 'uploads/imagenes/contratos/fotootros/' . $FOTOOTROS;
                    }
                    $ID_PROMOCION = self::validacionDeNulo($contrato['ID_PROMOCION']);
                    $IDCONTRATORELACION = self::validacionDeNulo($contrato['IDCONTRATORELACION']);
                    $ULTIMOABONO = self::validacionDeNulo($contrato['ULTIMOABONO']);
                    $DIAPAGO = self::validacionDeNulo($contrato['DIAPAGO']);
                    $FECHACOBROINI = self::validacionDeNulo($contrato['FECHACOBROINI']);
                    $FECHACOBROFIN = self::validacionDeNulo($contrato['FECHACOBROFIN']);
                    $FECHAATRASO = self::validacionDeNulo($contrato['FECHAATRASO']);
                    $DIASELECCIONADO = self::validacionDeNulo($contrato['DIASELECCIONADO']);
                    $ENTREGAPRODUCTO = self::validacionDeNulo($contrato['ENTREGAPRODUCTO']);
                    $FECHAENTREGA = self::validacionDeNulo($contrato['FECHAENTREGA']);
                    $CORREO = self::validacionDeNulo($contrato['CORREO']);
                    $SUBSCRIPCION = self::validacionDeNulo($contrato['SUBSCRIPCION']);
                    $FECHASUBSCRIPCION = self::validacionDeNulo($contrato['FECHASUBSCRIPCION']);
                    $NOTA = self::validacionDeNulo($contrato['NOTA']);
                    $TOTALREAL = self::validacionDeNulo($contrato['TOTALREAL']);
                    $DIATEMPORAL = self::validacionDeNulo($contrato['DIATEMPORAL']);
                    $COORDENADAS = self::validacionDeNulo($contrato['COORDENADAS']);
                    $UPDATED_AT = self::validacionDeNulo($contrato['UPDATED_AT']);

                    $existeContrato = DB::select("SELECT * FROM contratos
                                                        WHERE id_franquicia = '$idFranquicia' AND id = '" . $contrato['ID_CONTRATO'] . "'");

                    if ($existeContrato != null) {
                        //Existe el contrato
                        $estadoContratoActual = $existeContrato[0]->estatus_estadocontrato;
                        $pagoContratoActualizar = $existeContrato[0]->pago;

                        if ($estadoContratoActual == 0 || $estadoContratoActual == 1 || $estadoContratoActual == 9
                            || $estadoContratoActual == 12 || $estadoContratoActual == 2 || $estadoContratoActual == 4
                            || $estadoContratoActual == 5 || $estadoContratoActual == 7 || $estadoContratoActual == 10 || $estadoContratoActual == 11) {
                            //NO TERMINADO, TERMINADO, EN PROCESO DE APROBACION, ENVIADO, ENTREGADO, ABONOATRASADO, LIQUIDADO, APROBADO, MANUFACTURA, EN PROCESO DE ENVIO

                            if ($rol_id == 4) {
                                //Cobranza

                                $contratosGlobal::calculoTotal($contrato['ID_CONTRATO']);

                                $totalesContrato = DB::select("SELECT total, totalhistorial, totalreal FROM contratos
                                                                     WHERE id_franquicia = '$idFranquicia' AND id = '" . $contrato['ID_CONTRATO'] . "'");
                                $totalHistorialActualizar = null;
                                $totalActualizar = null;
                                if($totalesContrato != null) {
                                    //Existen totales
                                    $totalHistorialActualizar = $totalesContrato[0]->totalhistorial;
                                    $totalActualizar = $totalesContrato[0]->total;
                                }

                                if($contrato['ESTATUS_ESTADOCONTRATO'] == 6) {
                                    //Estado que viene del movil es CANCELADO
                                    $estadoActualizar = $contrato['ESTATUS_ESTADOCONTRATO']; //Obtenemos el estatus del cobrador
                                }else {
                                    //Estado que viene del movil es diferente a CANCELADO

                                    if ($estadoContratoActual == 2 || $estadoContratoActual == 4 || $estadoContratoActual == 5 || $estadoContratoActual == 12) {
                                        //Estado actual en la pagina es ENTREGADO, ABONO ATRASADO, LIQUIDADO O ENVIADO

                                        $validarGarantia = DB::select("SELECT id FROM garantias WHERE id_contrato = '" . $contrato['ID_CONTRATO'] . "' AND estadogarantia = '2'
                                                                         ORDER BY created_at DESC LIMIT 1");

                                        if ($validarGarantia != null) {
                                            //Estado garantia es igual a 2
                                            $estadoActualizar = $estadoContratoActual; //Obtenemos el estatus de la pagina
                                            if ($totalesContrato != null) {
                                                //Existen totales
                                                $diferenciaTotalReal = $totalesContrato[0]->totalreal - $TOTALREAL; //El restante que se sumo de lo que se haya agregado
                                                $totalHistorialActualizar = $contrato['TOTALHISTORIAL'] + $diferenciaTotalReal;
                                                $totalActualizar = $contrato['TOTAL'] + $diferenciaTotalReal;
                                            }
                                        } else {
                                            //Estado garantia es diferente de 2

                                            switch ($estadoContratoActual) {
                                                case 12: //Estado actual en la pagina es ENVIADO
                                                    $pagoContratoActualizar = $contrato['PAGO'];
                                                    if ($contrato['ESTATUS_ESTADOCONTRATO'] != 2 && $contrato['ESTATUS_ESTADOCONTRATO'] != 4 && $contrato['ESTATUS_ESTADOCONTRATO'] != 5) {
                                                        //Estado que viene del movil es diferenete a ENTREGADO, ABONO ATRASADO Y LIQUIDADO
                                                        $estadoActualizar = $estadoContratoActual; //Obtenemos el estatus de la pagina
                                                    } else {
                                                        //Estado que viene del movil es igual a ENTREGADO, ABONO ATRASADO O LIQUIDADO
                                                        if ($pagoContratoActualizar == 0) {
                                                            //Pago de contado
                                                            $abonoContadoSinEnganche = DB::select("SELECT * FROM abonos
                                                                                                        WHERE id_contrato = '" . $contrato['ID_CONTRATO'] . "'
                                                                                                        AND tipoabono = 5");

                                                            if ($contrato['ESTATUS_ESTADOCONTRATO'] == 5 && $abonoContadoSinEnganche == null) {
                                                                //LIQUIDADO y no tiene contadosinenganche

                                                                $ultimoAbono = DB::select("SELECT abono, folio FROM abonos
                                                                                                WHERE id_contrato = '" . $contrato['ID_CONTRATO'] . "'
                                                                                                AND tipoabono != '7'
                                                                                                ORDER BY created_at DESC LIMIT 1");
                                                                if ($ultimoAbono != null) {
                                                                    //Se encontro ultimo abono
                                                                    $sumatotal = $ultimoAbono[0]->abono;
                                                                    if ($ultimoAbono[0]->folio != null) {
                                                                        //Tiene folio el ultimo abono
                                                                        $sumaAbonosFolio = DB::select("SELECT SUM(abono) as sumatotal FROM abonos
                                                                                                            WHERE id_contrato = '" . $contrato['ID_CONTRATO'] . "'
                                                                                                            AND folio = '" . $ultimoAbono[0]->folio . "'");
                                                                        $sumatotal = $sumaAbonosFolio[0]->sumatotal;
                                                                    }

                                                                    if ($totalActualizar == $sumatotal || $totalActualizar == 0) {
                                                                        //Total es igual a la sumaabono o Total es igual a 0
                                                                        $totalHistorialActualizar = $contrato['TOTALHISTORIAL']; //Obtenemos el totalhistorial del cobrador
                                                                        $totalActualizar = $contrato['TOTAL']; //Obtenemos el total del cobrador
                                                                        $estadoActualizar = $contrato['ESTATUS_ESTADOCONTRATO']; //Obtenemos el estatus del cobrador
                                                                    } else {
                                                                        //Total no es igual a la sumaabono
                                                                        $estadoActualizar = $estadoContratoActual; //Obtenemos el estatus de la pagina
                                                                        $FECHAENTREGA = null; //fechaentrega igualarla a nulo
                                                                        $ENTREGAPRODUCTO = 0; //entregaproducto igualarlo a nulo
                                                                    }

                                                                } else {
                                                                    //No se encontro ultimo abono
                                                                    $estadoActualizar = $estadoContratoActual; //Obtenemos el estatus de la pagina
                                                                    $FECHAENTREGA = null; //fechaentrega igualarla a nulo
                                                                    $ENTREGAPRODUCTO = 0; //entregaproducto igualarlo a nulo
                                                                }

                                                            } else {
                                                                //LIQUIDADO y tiene contadosinenganche
                                                                $totalHistorialActualizar = $contrato['TOTALHISTORIAL']; //Obtenemos el totalhistorial del cobrador
                                                                $totalActualizar = $contrato['TOTAL']; //Obtenemos el total del cobrador
                                                                $estadoActualizar = $contrato['ESTATUS_ESTADOCONTRATO']; //Obtenemos el estatus del cobrador
                                                            }

                                                        } else {
                                                            //Semanal, quincenal o mensual
                                                            $abonoEntrega = DB::select("SELECT id FROM abonos WHERE id_contrato = '" . $contrato['ID_CONTRATO'] . "' AND tipoabono = '2'");
                                                            if ($abonoEntrega != null) {
                                                                //Tiene abono entrega producto
                                                                $estadoActualizar = $contrato['ESTATUS_ESTADOCONTRATO']; //Obtenemos el estatus del cobrador
                                                            } else {
                                                                //No tiene abono entrega producto
                                                                $estadoActualizar = $estadoContratoActual; //Obtenemos el estatus de la pagina
                                                                $FECHAENTREGA = null; //fechaentrega igualarla a nulo
                                                                $ENTREGAPRODUCTO = 0; //entregaproducto igualarlo a nulo
                                                            }
                                                        }
                                                    }
                                                    break;
                                                case 2: //Estado actual en la pagina es ENTREGADO
                                                    $ultimoAbono = DB::select("SELECT created_at FROM abonos
                                                                                    WHERE id_contrato = '" . $contrato['ID_CONTRATO'] . "'
                                                                                    AND tipoabono != '7'
                                                                                    ORDER BY created_at DESC LIMIT 1");
                                                    if ($ultimoAbono != null) {
                                                        //Existe un abono
                                                        if ($existeContrato[0]->fechacobroini != null && $existeContrato[0]->fechacobrofin != null) {
                                                            //fechacobroini y fechacobrofin son diferentes de nulo
                                                            if ((Carbon::parse($ultimoAbono[0]->created_at)->format('Y-m-d') >= Carbon::parse($existeContrato[0]->fechacobroini)->format('Y-m-d')
                                                                && Carbon::parse($ultimoAbono[0]->created_at)->format('Y-m-d') <= Carbon::parse($existeContrato[0]->fechacobrofin)->format('Y-m-d'))) {
                                                                //Hay un abono dentro del periodo
                                                                if ($totalActualizar <= 0) {
                                                                    //Se liquido el contrato
                                                                    $estadoActualizar = $contrato['ESTATUS_ESTADOCONTRATO']; //Obtenemos el estatus del cobrador
                                                                }else {
                                                                    //Aun no se ha liquidado el contrato
                                                                    $estadoActualizar = $estadoContratoActual; //Obtenemos el estatus de la pagina
                                                                }
                                                            } else {
                                                                //No hay abono dentro del periodo
                                                                $canceloGarantia = DB::select("SELECT id FROM garantias WHERE id_contrato = '" . $contrato['ID_CONTRATO'] . "' AND estadogarantia = '4'
                                                                         ORDER BY created_at DESC LIMIT 1");
                                                                if ($canceloGarantia != null) {
                                                                    //Se cancelo garantia en laboratorio, confirmaciones o en administracion
                                                                    $estadoActualizar = $estadoContratoActual; //Obtenemos el estatus de la pagina
                                                                }else {
                                                                    //No se cancelo garantia en laboratorio, confirmaciones o en administracion
                                                                    $estadoActualizar = $contrato['ESTATUS_ESTADOCONTRATO']; //Obtenemos el estatus del cobrador
                                                                }
                                                            }
                                                        } else {
                                                            //fechacobroini y fechacobrofin son nulo
                                                            $estadoActualizar = $estadoContratoActual; //Obtenemos el estatus de la pagina
                                                        }
                                                    } else {
                                                        //No existe abono
                                                        $estadoActualizar = $estadoContratoActual; //Obtenemos el estatus de la pagina
                                                    }
                                                    break;
                                                case 4: //Estado actual en la pagina es ABONO ATRASADO
                                                    $ultimoAbono = DB::select("SELECT created_at FROM abonos WHERE id_contrato = '" . $contrato['ID_CONTRATO'] . "'
                                                                                    AND tipoabono != '7'
                                                                                    ORDER BY created_at DESC LIMIT 1");
                                                    if ($ultimoAbono != null) {
                                                        //Existe un abono
                                                        if ($existeContrato[0]->fechacobroini != null && $existeContrato[0]->fechacobrofin != null) {
                                                            //fechacobroini y fechacobrofin son diferentes de nulo
                                                            if ((Carbon::parse($ultimoAbono[0]->created_at)->format('Y-m-d') >= Carbon::parse($existeContrato[0]->fechacobroini)->format('Y-m-d')
                                                                && Carbon::parse($ultimoAbono[0]->created_at)->format('Y-m-d') <= Carbon::parse($existeContrato[0]->fechacobrofin)->format('Y-m-d'))) {
                                                                //Hay un abono dentro del periodo
                                                                $estadoActualizar = $contrato['ESTATUS_ESTADOCONTRATO']; //Obtenemos el estatus del cobrador
                                                            } else {
                                                                //No hay abono dentro del periodo
                                                                $estadoActualizar = $estadoContratoActual; //Obtenemos el estatus de la pagina
                                                            }
                                                        } else {
                                                            //fechacobroini y fechacobrofin son nulo
                                                            $estadoActualizar = $contrato['ESTATUS_ESTADOCONTRATO']; //Obtenemos el estatus del cobrador
                                                        }
                                                    } else {
                                                        //No existe abono
                                                        $estadoActualizar = $contrato['ESTATUS_ESTADOCONTRATO']; //Obtenemos el estatus del cobrador
                                                    }
                                                    break;
                                                case 5: //Estado actual en la pagina es LIQUIDADO
                                                    $totalcontratoactualizado = DB::select("SELECT total FROM contratos
                                                                                                WHERE id_franquicia = '$idFranquicia' AND id = '" . $contrato['ID_CONTRATO'] . "'");
                                                    if ($totalcontratoactualizado != null) {
                                                        //Existe el contrato
                                                        if ($totalcontratoactualizado[0]->total <= 0) {
                                                            //Total es menor o igual a 0
                                                            $estadoActualizar = $estadoContratoActual; //Obtenemos el estatus de la pagina
                                                        } else {
                                                            //Total es mayor a 0
                                                            if ($pagoContratoActualizar == 0) {
                                                                //Pago de contado
                                                                $totalHistorialActualizar = $contrato['TOTALHISTORIAL']; //Obtenemos el totalhistorial del cobrador
                                                                $totalActualizar = $contrato['TOTAL']; //Obtenemos el total del cobrador
                                                            }
                                                            $estadoActualizar = $contrato['ESTATUS_ESTADOCONTRATO']; //Obtenemos el estatus del cobrador
                                                        }
                                                    }
                                                    break;
                                            }

                                        }

                                    } else {
                                        //Para todos los demas estatus, obtenemos el estatus de la pagina
                                        $estadoActualizar = $estadoContratoActual; //Obtenemos el estatus de la pagina
                                        if ($estadoContratoActual == 1 || $estadoContratoActual == 9) {
                                            $validarGarantia = DB::select("SELECT id FROM garantias WHERE id_contrato = '" . $contrato['ID_CONTRATO'] . "' AND estadogarantia = '2'
                                                                             ORDER BY created_at DESC limit 1");
                                            if ($validarGarantia != null) {
                                                //Existe garantia creada
                                                if ($totalesContrato != null) {
                                                    //Existen totales
                                                    $diferenciaTotalReal = $totalesContrato[0]->totalreal - $TOTALREAL; //El restante que se sumo de lo que se haya agregado
                                                    $totalHistorialActualizar = $contrato['TOTALHISTORIAL'] + $diferenciaTotalReal;
                                                    $totalActualizar = $contrato['TOTAL'] + $diferenciaTotalReal;
                                                }
                                            } else {
                                                //No existe garantia creada
                                                if ($pagoContratoActualizar == 0) {
                                                    //Pago de contado
                                                    $totalHistorialActualizar = $contrato['TOTALHISTORIAL']; //Obtenemos el totalhistorial del cobrador
                                                    $totalActualizar = $contrato['TOTAL']; //Obtenemos el total del cobrador
                                                }
                                            }
                                        } else {
                                            if ($pagoContratoActualizar == 0) {
                                                //Pago de contado
                                                $totalHistorialActualizar = $contrato['TOTALHISTORIAL']; //Obtenemos el totalhistorial del cobrador
                                                $totalActualizar = $contrato['TOTAL']; //Obtenemos el total del cobrador
                                            }
                                        }
                                    }

                                }

                                //Actualizar foto de foto casa
                                $FOTOCASA = self::validacionDeNulo($contrato['FOTOCASA']);  //Validar que no sea null el valor recibido
                                if ($FOTOCASA != null) {
                                    //Concatenra nombre completo ruta de almacenamiento imagen casa
                                    $FOTOCASA = 'uploads/imagenes/contratos/fotocasa/' . $contrato['FOTOCASA'];
                                }

                                DB::table("contratos")->where("id", "=", $contrato['ID_CONTRATO'])->where("id_franquicia", "=", $idFranquicia)->update([
                                    "pago" => $pagoContratoActualizar,
                                    "totalhistorial" => $totalHistorialActualizar,
                                    "total" => $totalActualizar,
                                    "ultimoabono" => $ULTIMOABONO,
                                    "estatus_estadocontrato" => $estadoActualizar,
                                    "diapago" => $DIAPAGO,
                                    "costoatraso" => $contrato['COSTOATRASO'],
                                    "fechaentrega" => $FECHAENTREGA,
                                    "pagosadelantar" => $contrato['PAGOSADELANTAR'], // Este campo se utilizara para validar si quiere adelantar abonos y hacer el calculo
                                    "enganche" => $contrato['ENGANCHE'],
                                    "entregaproducto" => $ENTREGAPRODUCTO,
                                    "promocionterminada" => $contrato['PROMOCIONTERMINADA'],
                                    "subscripcion" => $SUBSCRIPCION,
                                    "fechasubscripcion" => $FECHASUBSCRIPCION,
                                    "nota" => $NOTA,
                                    "diatemporal" => $DIATEMPORAL,
                                    "coordenadas" => $COORDENADAS,
                                    "fotocasa" => $FOTOCASA,
                                    "created_at" => $contrato['CREATED_AT'],
                                    "updated_at" => $UPDATED_AT
                                ]);

                                //Insertar en tabla registroestadocontrato
                                DB::table('registroestadocontrato')->insert([
                                    'id_contrato' => $contrato['ID_CONTRATO'],
                                    'estatuscontrato' => $estadoActualizar,
                                    'created_at' => Carbon::now()
                                ]);

                                //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                                $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($contrato['ID_CONTRATO'], $id_usuario);
                                //Actualizar contrato en tabla contratostemporalessincronizacion (Mas que nada para validar si fue cancelado y eliminar registros)
                                $contratosGlobal::actualizarDatosPorContratoTablaContratosTemporalesSincronizacion($contrato['ID_CONTRATO']);
                                //Insertar o actualizar contrato tabla contratoslistatemporales
                                $contratosGlobal::insertarActualizarDatosContratoListaTemporales($contrato['ID_CONTRATO']);

                            } else {
                                //Asistente/Opto

                                $DATOS = self::validacionDeNulo($contrato['DATOS']);
                                $ID_USUARIOCREACION = self::validacionDeNulo($contrato['ID_USUARIOCREACION']);
                                $NOMBRE_USUARIOCREACION = self::validacionDeNulo($contrato['NOMBRE_USUARIOCREACION']);
                                $ID_ZONA = self::validacionDeNulo($contrato['ID_ZONA']);
                                $NOMBRE = self::validacionDeNulo($contrato['NOMBRE']);
                                $CALLE = self::validacionDeNulo($contrato['CALLE']);
                                $NUMERO = self::validacionDeNulo($contrato['NUMERO']);
                                $DEPTO = self::validacionDeNulo($contrato['DEPTO']);
                                $ALLADODE = self::validacionDeNulo($contrato['ALLADODE']);
                                $FRENTEA = self::validacionDeNulo($contrato['FRENTEA']);
                                $ENTRECALLES = self::validacionDeNulo($contrato['ENTRECALLES']);
                                $COLONIA = self::validacionDeNulo($contrato['COLONIA']);
                                $LOCALIDAD = self::validacionDeNulo($contrato['LOCALIDAD']);
                                $TELEFONO = self::validacionDeNulo($contrato['TELEFONO']);
                                $TELEFONOREFERENCIA = self::validacionDeNulo($contrato['TELEFONOREFERENCIA']);
                                $NOMBREREFERENCIA = self::validacionDeNulo($contrato['NOMBREREFERENCIA']);
                                $CASATIPO = self::validacionDeNulo($contrato['CASATIPO']);
                                $CASACOLOR = self::validacionDeNulo($contrato['CASACOLOR']);
                                $FOTOINEFRENTE = 'uploads/imagenes/contratos/fotoine/' . $contrato['FOTOINEFRENTE']; //CONCATENAR LA RUTA CON EL NOMBRE
                                $FOTOINEATRAS = 'uploads/imagenes/contratos/fotoineatras/' . $contrato['FOTOINEATRAS']; //CONCATENAR LA RUTA CON EL NOMBRE
                                $FOTOCASA = 'uploads/imagenes/contratos/fotocasa/' . $contrato['FOTOCASA']; //CONCATENAR LA RUTA CON EL NOMBRE
                                $COMPROBANTEDOMICILIO = 'uploads/imagenes/contratos/comprobantedomicilio/' . $contrato['COMPROBANTEDOMICILIO']; //CONCATENAR LA RUTA CON EL NOMBRE
                                $ID_OPTOMETRISTA = self::validacionDeNulo($contrato['ID_OPTOMETRISTA']);
                                $CONTADOR = self::validacionDeNulo($contrato['CONTADOR']);
                                $TOTALPRODUCTO = self::validacionDeNulo($contrato['TOTALPRODUCTO']);
                                $PAGARE = self::validacionDeNulo($contrato['PAGARE']);
                                if ($PAGARE != null) {
                                    $PAGARE = 'uploads/imagenes/contratos/pagare/' . $contrato['PAGARE']; //CONCATENAR LA RUTA CON EL NOMBRE
                                }

                                $CALLEENTREGA = self::validacionDeNulo($contrato['CALLEENTREGA']);
                                $NUMEROENTREGA = self::validacionDeNulo($contrato['NUMEROENTREGA']);
                                $DEPTOENTREGA = self::validacionDeNulo($contrato['DEPTOENTREGA']);
                                $ALLADODEENTREGA = self::validacionDeNulo($contrato['ALLADODEENTREGA']);
                                $FRENTEAENTREGA = self::validacionDeNulo($contrato['FRENTEAENTREGA']);
                                $ENTRECALLESENTREGA = self::validacionDeNulo($contrato['ENTRECALLESENTREGA']);
                                $COLONIAENTREGA = self::validacionDeNulo($contrato['COLONIAENTREGA']);
                                $LOCALIDADENTREGA = self::validacionDeNulo($contrato['LOCALIDADENTREGA']);
                                $CASATIPOENTREGA = self::validacionDeNulo($contrato['CASATIPOENTREGA']);
                                $CASACOLORENTREGA = self::validacionDeNulo($contrato['CASACOLORENTREGA']);
                                $ALIAS = self::validacionDeNulo($contrato['ALIAS']);
                                $OPCIONLUGARENTREGA = self::validacionDeNulo($contrato['OPCIONLUGARENTREGA']);
                                $OBSERVACIONFOTOINE = self::validacionDeNulo($contrato['OBSERVACIONFOTOINE']);
                                $OBSERVACIONFOTOINEATRAS = self::validacionDeNulo($contrato['OBSERVACIONFOTOINEATRAS']);
                                $OBSERVACIONFOTOCASA = self::validacionDeNulo($contrato['OBSERVACIONFOTOCASA']);
                                $OBSERVACIONCOMPROBANTEDOMICILIO = self::validacionDeNulo($contrato['OBSERVACIONCOMPROBANTEDOMICILIO']);
                                $OBSERVACIONPAGARE = self::validacionDeNulo($contrato['OBSERVACIONPAGARE']);
                                $OBSERVACIONFOTOOTROS = self::validacionDeNulo($contrato['OBSERVACIONFOTOOTROS']);

                                if($existeContrato[0]->pagare == $PAGARE) {
                                    $PAGARE = $existeContrato[0]->pagare;
                                }

                                if($estadoContratoActual > 0) {
                                    //ESTADO DEL CONTRATO DIFERENTE A NO TERMINADO
                                    $PAGO = $existeContrato[0]->pago;
                                    $TARJETAFRENTE = $existeContrato[0]->tarjeta;
                                    $TARJETAATRAS = $existeContrato[0]->tarjetapensionatras;
                                    $FOTOOTROS = $existeContrato[0]->fotootros;
                                    $CORREO = $existeContrato[0]->correo;

                                    $ID_USUARIOCREACION = $existeContrato[0]->id_usuariocreacion;
                                    $NOMBRE_USUARIOCREACION = $existeContrato[0]->nombre_usuariocreacion;
                                    $ID_ZONA = $existeContrato[0]->id_zona;
                                    $NOMBRE = $existeContrato[0]->nombre;
                                    $CALLE = $existeContrato[0]->calle;
                                    $NUMERO = $existeContrato[0]->numero;
                                    $DEPTO = $existeContrato[0]->depto;
                                    $ALLADODE = $existeContrato[0]->alladode;
                                    $FRENTEA = $existeContrato[0]->frentea;
                                    $ENTRECALLES = $existeContrato[0]->entrecalles;
                                    $COLONIA = $existeContrato[0]->colonia;
                                    $LOCALIDAD = $existeContrato[0]->localidad;
                                    $TELEFONO = $existeContrato[0]->telefono;
                                    $TELEFONOREFERENCIA = $existeContrato[0]->telefonoreferencia;
                                    $NOMBREREFERENCIA = $existeContrato[0]->nombrereferencia;
                                    $CASATIPO = $existeContrato[0]->casatipo;
                                    $CASACOLOR = $existeContrato[0]->casacolor;
                                    $FOTOINEFRENTE = $existeContrato[0]->fotoine;
                                    $FOTOINEATRAS = $existeContrato[0]->fotoineatras;
                                    $FOTOCASA = $existeContrato[0]->fotocasa;
                                    $COMPROBANTEDOMICILIO = $existeContrato[0]->comprobantedomicilio;
                                    $ID_OPTOMETRISTA = $existeContrato[0]->id_optometrista;
                                    $TOTALPRODUCTO = $existeContrato[0]->totalproducto;

                                    $CALLEENTREGA = $existeContrato[0]->calleentrega;
                                    $NUMEROENTREGA = $existeContrato[0]->numeroentrega;
                                    $DEPTOENTREGA = $existeContrato[0]->deptoentrega;
                                    $ALLADODEENTREGA = $existeContrato[0]->alladodeentrega;
                                    $FRENTEAENTREGA = $existeContrato[0]->frenteaentrega;
                                    $ENTRECALLESENTREGA = $existeContrato[0]->entrecallesentrega;
                                    $COLONIAENTREGA = $existeContrato[0]->coloniaentrega;
                                    $LOCALIDADENTREGA = $existeContrato[0]->localidadentrega;
                                    $CASATIPOENTREGA = $existeContrato[0]->casatipoentrega;
                                    $CASACOLORENTREGA = $existeContrato[0]->casacolorentrega;
                                    $ALIAS = $existeContrato[0]->alias;
                                    $OPCIONLUGARENTREGA = $existeContrato[0]->opcionlugarentrega;
                                    $OBSERVACIONFOTOINE = $existeContrato[0]->observacionfotoine;
                                    $OBSERVACIONFOTOINEATRAS = $existeContrato[0]->observacionfotoineatras;
                                    $OBSERVACIONFOTOCASA = $existeContrato[0]->observacionfotocasa;
                                    $OBSERVACIONCOMPROBANTEDOMICILIO = $existeContrato[0]->observacioncomprobantedomicilio;
                                    $OBSERVACIONFOTOOTROS = $existeContrato[0]->observacionfotootros;
                                }

                                $totalHistorialActualizar = $contrato['TOTALHISTORIAL'];
                                $totalActualizar = $contrato['TOTAL'];
                                $totalRealActualizar = $TOTALREAL;
                                $totalPromocionActualizar = $contrato['TOTALPROMOCION'];
                                $totalAbonoActualizar = $contrato['TOTALABONO'];

                                $estadoActualizar = $contrato['ESTATUS_ESTADOCONTRATO'];
                                $garantiaTotales = false;

                                switch ($estadoContratoActual) {
                                    case 0: //No terminado
                                    case 1: //Terminado
                                    case null: // Contrato por crear
                                        $bandera = true;
                                        if($estadoContratoActual == 1) {
                                            //TERMINADO
                                            $validarGarantia = DB::select("SELECT id FROM garantias WHERE id_contrato = '" . $contrato['ID_CONTRATO'] . "' AND estadogarantia = '2'
                                                                                    ORDER BY created_at DESC limit 1");
                                            if ($validarGarantia != null) {
                                                //Existe garantia creada
                                                $garantiaTotales = true;
                                            }
                                        }
                                        break;
                                    case 9: // En proceso de aprobacion
                                        $estadoActualizar = $estadoContratoActual; //Dejamos el estatus actual de la pagina
                                        $bandera = true;
                                        $validarGarantia = DB::select("SELECT id FROM garantias WHERE id_contrato = '" . $contrato['ID_CONTRATO'] . "' AND estadogarantia = '2'
                                                                                ORDER BY created_at DESC limit 1");
                                        if ($validarGarantia != null) {
                                            //Existe garantia creada
                                            $garantiaTotales = true;
                                        }
                                        break;
                                    case 2: //Entregado
                                    case 4: //Abono atrasado
                                    case 12: //Enviado
                                    case 5: //Liquidado
                                        $validarGarantia = DB::select("SELECT id FROM garantias WHERE id_contrato = '" . $contrato['ID_CONTRATO'] . "' ORDER BY created_at DESC limit 1");
                                        $bandera = false;
                                        if($validarGarantia != null) {
                                            $bandera = true;
                                            $garantiaTotales = true;
                                        }
                                        break;
                                    default:
                                        $bandera = false;
                                        break;
                                }//switch

                                if($garantiaTotales) {
                                    //Sacar diferencia entre totales

                                    $totalesContrato = DB::select("SELECT total, totalhistorial, totalreal, totalpromocion, totalabono
                                                                        FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '" . $contrato['ID_CONTRATO'] . "'");

                                    if ($totalesContrato != null) {
                                        //Existen totales
                                        $diferenciaTotalReal = $totalRealActualizar - $totalesContrato[0]->totalreal; //El restante que se sumo de lo que se haya agregado
                                        $totalRealActualizar = $totalesContrato[0]->totalreal + $diferenciaTotalReal;
                                        $totalHistorialActualizar = $totalesContrato[0]->totalhistorial + $diferenciaTotalReal;
                                        $totalActualizar = $totalesContrato[0]->total + $diferenciaTotalReal;
                                        $totalAbonoActualizar = $totalesContrato[0]->totalabono;
                                        if(self::obtenerEstadoPromocion($contrato['ID_CONTRATO'], $idFranquicia)) {
                                            //Tiene promocion
                                            $totalPromocionActualizar = $totalesContrato[0]->totalpromocion + $diferenciaTotalReal;
                                        }
                                    }
                                }

                                //Verificar fecha de insercion
                                $fechaRegistroServidor = $existeContrato[0]->fecharegistro;
                                if($fechaRegistroServidor == null){
                                    //Es primera vez que se insertara el contrato
                                    $fechaRegistroServidor = Carbon::now();
                                }

                                if ($bandera) {

                                    DB::table("contratos")->where("id", "=", $contrato['ID_CONTRATO'])->where("id_franquicia", "=", $idFranquicia)->update([
                                        "datos" => $DATOS,
                                        "id_usuariocreacion" => $ID_USUARIOCREACION,
                                        "nombre_usuariocreacion" => $NOMBRE_USUARIOCREACION,
                                        "id_zona" => $ID_ZONA,
                                        "nombre" => $NOMBRE,
                                        "calle" => $CALLE,
                                        "numero" => $NUMERO,
                                        "depto" => $DEPTO,
                                        "alladode" => $ALLADODE,
                                        "frentea" => $FRENTEA,
                                        "entrecalles" => $ENTRECALLES,
                                        "colonia" => $COLONIA,
                                        "localidad" => $LOCALIDAD,
                                        "telefono" => $TELEFONO,
                                        "telefonoreferencia" => $TELEFONOREFERENCIA,
                                        "nombrereferencia" => $NOMBREREFERENCIA,
                                        "casatipo" => $CASATIPO,
                                        "casacolor" => $CASACOLOR,
                                        "fotoine" => $FOTOINEFRENTE,
                                        "fotoineatras" => $FOTOINEATRAS,
                                        "fotocasa" => $FOTOCASA,
                                        "comprobantedomicilio" => $COMPROBANTEDOMICILIO,
                                        "id_optometrista" => $ID_OPTOMETRISTA,
                                        "tarjeta" => $TARJETAFRENTE, //CONCATENAR LA RUTA CON EL NOMBRE
                                        "tarjetapensionatras" => $TARJETAATRAS,//CONCATENAR LA RUTA CON EL NOMBRE
                                        "pago" => $PAGO,
                                        "id_promocion" => $ID_PROMOCION,
                                        "total" => $totalActualizar,
                                        "idcontratorelacion" => $IDCONTRATORELACION,
                                        "contador" => $CONTADOR,
                                        "totalhistorial" => $totalHistorialActualizar,
                                        "totalpromocion" => $totalPromocionActualizar,
                                        "totalproducto" => $TOTALPRODUCTO,
                                        "totalabono" => $totalAbonoActualizar,
                                        "ultimoabono" => $ULTIMOABONO,
                                        "estatus_estadocontrato" => $estadoActualizar,
                                        "diapago" => $DIAPAGO,
                                        "costoatraso" => $contrato['COSTOATRASO'],
                                        "pagosadelantar" => 1, // Este campo se utilizara para validar si quiere adelantar abonos y hacer el calculo
                                        "enganche" => $contrato['ENGANCHE'],
                                        "entregaproducto" => $contrato['ENTREGAPRODUCTO'],
                                        "correo" => $CORREO,
                                        "estatus" => $contrato['ESTATUS'],
                                        "pagare" => $PAGARE,
                                        "fotootros" => $FOTOOTROS,
                                        "promocionterminada" => $contrato['PROMOCIONTERMINADA'],
                                        "subscripcion" => $SUBSCRIPCION,
                                        "fechasubscripcion" => $FECHASUBSCRIPCION,
                                        "totalreal" => $totalRealActualizar,
                                        "coordenadas" => $COORDENADAS,
                                        "calleentrega" => $CALLEENTREGA,
                                        "numeroentrega" => $NUMEROENTREGA,
                                        "deptoentrega" => $DEPTOENTREGA,
                                        "alladodeentrega" => $ALLADODEENTREGA,
                                        "frenteaentrega" => $FRENTEAENTREGA,
                                        "entrecallesentrega" => $ENTRECALLESENTREGA,
                                        "coloniaentrega" => $COLONIAENTREGA,
                                        "localidadentrega" => $LOCALIDADENTREGA,
                                        "casatipoentrega" => $CASATIPOENTREGA,
                                        "casacolorentrega" => $CASACOLORENTREGA,
                                        "alias" => $ALIAS,
                                        "fecharegistro" => $fechaRegistroServidor,
                                        "opcionlugarentrega" => $OPCIONLUGARENTREGA,
                                        "observacionfotoine" => $OBSERVACIONFOTOINE,
                                        "observacionfotoineatras" => $OBSERVACIONFOTOINEATRAS,
                                        "observacionfotocasa" => $OBSERVACIONFOTOCASA,
                                        "observacioncomprobantedomicilio" => $OBSERVACIONCOMPROBANTEDOMICILIO,
                                        "observacionpagare" => $OBSERVACIONPAGARE,
                                        "observacionfotootros" => $OBSERVACIONFOTOOTROS,
                                        "created_at" => $contrato['CREATED_AT'],
                                        "updated_at" => $UPDATED_AT
                                    ]);

                                    //Insertar en tabla registroestadocontrato
                                    DB::table('registroestadocontrato')->insert([
                                        'id_contrato' => $contrato['ID_CONTRATO'],
                                        'estatuscontrato' => $estadoActualizar,
                                        'created_at' => Carbon::now()
                                    ]);

                                    //Insertar en tabla dispositivoscontratosusuarios
                                    DB::table('dispositivoscontratosusuarios')->insert([
                                        'id_contrato' => $contrato['ID_CONTRATO'],
                                        'id_usuario' => $id_usuario,
                                        'identificadorunico' => $idunico,
                                        'modelo' => $modelo,
                                        'created_at' => Carbon::now()
                                    ]);

                                    //Actualizar datos contrato a mayusculas y quitar acentos
                                    $contratosGlobal::actualizarContratoHistorialesClinicosMayusculasAcentos($contrato['ID_CONTRATO'], 0);

                                    //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                                    $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($contrato['ID_CONTRATO'], $id_usuario);

                                    //Insertar o actualizar contrato tabla contratoslistatemporales
                                    $contratosGlobal::insertarActualizarDatosContratoListaTemporales($contrato['ID_CONTRATO']);

                                    //Actualizar estatus de cita si fue venta realizada por medio de cita previa o es venta normal
                                    $REFERENCIA = self::validacionDeNulo($contrato['REFERENCIA']);
                                    if($REFERENCIA != null){
                                        $contratosGlobal::actualizarEstatusCitaAgendadaPorReferencia($idFranquicia,$contrato['ID_USUARIOCREACION'],$contrato['REFERENCIA']);
                                    }

                                    if($estadoActualizar == 3) {
                                        //Estado del contrato se cambio a PRE-CANCELADO
                                        //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato
                                        DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '" . $contrato['ID_CONTRATO'] . "'");
                                    }

                                }

                            }

                        }

                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonContratos: " . $contrato['ID_CONTRATO'] . "\n" . $e);
                    continue;
                }

            }

        }//CONTRATOS

        if($rol_id == 12 || $rol_id == 13) {
            self::insertarActualizarAbonosYAbonosEliminados($idFranquicia, $id_usuario, $rol_id, $jsonAbonos, $jsonAbonosEliminados);
            self::actualizarRegistroSalidaUsuariosVentas($idFranquicia, $id_usuario, $jsonAsistencia);
            self::insertarNuevaCitaPaciente($idFranquicia, $id_usuario, $jsonAgendaCitas);
        }//ABONOS Y ABONOS ELIMINADOS (ASISTENTE/OPTOMETRISTA)

        if (!empty($jsonHistorialesClinicos)) {
            //jsonHistorialesClinicos es diferente a vacio

            foreach ($jsonHistorialesClinicos as $historial) {
                //Recorrido de jsonHistorialesClinicos

                try {

                    //Validacion estado contrato
                    $contrato = DB::select("SELECT id, estatus_estadocontrato FROM contratos WHERE id = '" . $historial['ID_CONTRATO'] . "'");

                    if ($contrato != null) {

                        $estadoContratoActual = $contrato[0]->estatus_estadocontrato;

                        if ($estadoContratoActual == 0 || $estadoContratoActual == 1 || $estadoContratoActual == 9
                            || $estadoContratoActual == 12 || $estadoContratoActual == 2 || $estadoContratoActual == 4
                            || $estadoContratoActual == 5 || $estadoContratoActual == 7 || $estadoContratoActual == 10 || $estadoContratoActual == 11) {
                            //NO TERMINADO, TERMINADO, EN PROCESO DE APROBACION, ENVIADO, ENTREGADO, ABONOATRASADO, LIQUIDADO, APROBADO, MANUFACTURA, EN PROCESO DE ENVIO

                            $EDAD = self::validacionDeNulo($historial['EDAD']);
                            $FECHAENTREGA = self::validacionDeNulo($historial['FECHAENTREGA']);
                            $DIAGNOSTICO = self::validacionDeNulo($historial['DIAGNOSTICO']);
                            $OCUPACION = self::validacionDeNulo($historial['OCUPACION']);
                            $DIABETES = self::validacionDeNulo($historial['DIABETES']);
                            $HIPERTENSION = self::validacionDeNulo($historial['HIPERTENSION']);
                            $DOLOR = self::validacionDeNulo($historial['DOLOR']);
                            $ARDOR = self::validacionDeNulo($historial['ARDOR']);
                            $GOLPEOJOS = self::validacionDeNulo($historial['GOLPEOJOS']);
                            $OTROM = self::validacionDeNulo($historial['OTROM']);

                            $MOLESTIAOTRO = self::validacionDeNulo($historial['MOLESTIAOTRO']);
                            $ULTIMOEXAMEN = self::validacionDeNulo($historial['ULTIMOEXAMEN']);
                            $ESFERICODER = self::validacionDeNulo($historial['ESFERICODER']);
                            $CILINDRODER = self::validacionDeNulo($historial['CILINDRODER']);
                            $EJEDER = self::validacionDeNulo($historial['EJEDER']);
                            $ADDDER = self::validacionDeNulo($historial['ADDDER']);
                            $ALTDER = self::validacionDeNulo($historial['ALTDER']);
                            $ESFERICOIZQ = self::validacionDeNulo($historial['ESFERICOIZQ']);
                            $CILINDROIZQ = self::validacionDeNulo($historial['CILINDROIZQ']);
                            $EJEIZQ = self::validacionDeNulo($historial['EJEIZQ']);
                            $ADDIZQ = self::validacionDeNulo($historial['ADDIZQ']);
                            $ALTIZQ = self::validacionDeNulo($historial['ALTIZQ']);

                            $ID_PRODUCTO = self::validacionDeNulo($historial['ID_PRODUCTO']);
                            $ID_PAQUETE = self::validacionDeNulo($historial['ID_PAQUETE']);
                            $MATERIAL = self::validacionDeNulo($historial['MATERIAL']);
                            $MATERIALOTRO = self::validacionDeNulo($historial['MATERIALOTRO']);
                            $COSTOMATERIAL = self::validacionDeNulo($historial['COSTOMATERIAL']);
                            $BIFOCAL = self::validacionDeNulo($historial['BIFOCAL']);
                            $FOTOCROMATICO = self::validacionDeNulo($historial['FOTOCROMATICO']);
                            $AR = self::validacionDeNulo($historial['AR']);
                            $TINTE = self::validacionDeNulo($historial['TINTE']);
                            $BLUERAY = self::validacionDeNulo($historial['BLUERAY']);
                            $OTROT = self::validacionDeNulo($historial['OTROT']);
                            $TRATAMIENTOOTRO = self::validacionDeNulo($historial['TRATAMIENTOOTRO']);
                            $COSTOTRATAMIENTO = self::validacionDeNulo($historial['COSTOTRATAMIENTO']);
                            $OBSERVACIONES = self::validacionDeNulo($historial['OBSERVACIONES']);
                            $OBSERVACIONESINTERNO = self::validacionDeNulo($historial['OBSERVACIONESINTERNO']);
                            $TIPO = self::validacionDeNulo($historial['TIPO']);
                            $BIFOCALOTRO = self::validacionDeNulo($historial['BIFOCALOTRO']);
                            $COSTOBIFOCAL = self::validacionDeNulo($historial['COSTOBIFOCAL']);

                            $EMBARAZADA = self::validacionDeNulo($historial['EMBARAZADA']);
                            $DURMIOSEISOCHOHORAS = self::validacionDeNulo($historial['DURMIOSEISOCHOHORAS']);
                            $ACTIVIDADDIA = self::validacionDeNulo($historial['ACTIVIDADDIA']);
                            $PROBLEMASOJOS = self::validacionDeNulo($historial['PROBLEMASOJOS']);
                            $POLICARBONATOTIPO = self::validacionDeNulo($historial['POLICARBONATOTIPO']);
                            $ID_TRATAMIENTOCOLORTINTE = self::validacionDeNulo($historial['ID_TRATAMIENTOCOLORTINTE']);
                            $ESTILOTINTE = self::validacionDeNulo($historial['ESTILOTINTE']);
                            $POLARIZADO = self::validacionDeNulo($historial['POLARIZADO']);
                            $ID_TRATAMIENTOCOLORPOLARIZADO = self::validacionDeNulo($historial['ID_TRATAMIENTOCOLORPOLARIZADO']);
                            $ESPEJO = self::validacionDeNulo($historial['ESPEJO']);
                            $ID_TRATAMIENTOCOLORESPEJO = self::validacionDeNulo($historial['ID_TRATAMIENTOCOLORESPEJO']);
                            $historialContrato = DB::select("SELECT * FROM historialclinico hc WHERE hc.id_contrato = '" . $historial['ID_CONTRATO'] . "'");
                            //Validacion de armazon
                            $FOTOARMAZON = null;
                            if(array_key_exists('FOTOARMAZON', $historial)){
                                $FOTOARMAZON = self::validacionDeNulo($historial['FOTOARMAZON']);
                                if($FOTOARMAZON != null) {
                                    if ($ID_PAQUETE != "6") {
                                        //DIFERENTE DE DORADO 2
                                        $FOTOARMAZON = 'uploads/imagenes/contratos/fotoarmazon1/' . $historial['FOTOARMAZON'];
                                    } else {
                                        //ES DORADO 2
                                        if ($historialContrato == null) {
                                            //Primer historial para el contrato
                                            $FOTOARMAZON = 'uploads/imagenes/contratos/fotoarmazon1/' . $historial['FOTOARMAZON'];
                                        } else {
                                            //Segundo historial - DORADO 2
                                            $FOTOARMAZON = 'uploads/imagenes/contratos/fotoarmazon2/' . $historial['FOTOARMAZON'];
                                        }
                                    }
                                }
                            }
                            $CREATED_AT = self::validacionDeNulo($historial['CREATED_AT']);
                            $UPDATED_AT = self::validacionDeNulo($historial['UPDATED_AT']);

                            $existeHistorial = DB::select("SELECT * FROM historialclinico hc
                                                                    WHERE hc.id = '" . $historial['ID'] . "' AND hc.id_contrato = '" . $historial['ID_CONTRATO'] . "'");

                            //Asistente/Opto
                            switch ($estadoContratoActual) {
                                case 0: //No terminado
                                case 1: //Terminado
                                case null: // Contrato por crear
                                    $bandera = true;
                                    break;
                                case 9: // En proceso de aprobacion
                                    $bandera = true;
                                    break;
                                default:
                                    $bandera = false;
                                    break;
                            }//switch


                            if ($existeHistorial != null) { //Existe el historial?
                                if ($bandera) {
                                    //Existe el historial

                                    if ($estadoContratoActual == 7 || $estadoContratoActual == 10 || $estadoContratoActual == 11) {
                                        //ESTADO DEL CONTRATO APROBADO, MANUFACTURA O EN PROCESO DE ENVIO
                                        $EDAD = $existeHistorial[0]->edad;
                                        $FECHAENTREGA = $existeHistorial[0]->fechaentrega;
                                        $DIAGNOSTICO = $existeHistorial[0]->diagnostico;
                                        $OCUPACION = $existeHistorial[0]->ocupacion;
                                        $DIABETES = $existeHistorial[0]->diabetes;
                                        $HIPERTENSION = $existeHistorial[0]->hipertension;
                                        $DOLOR = $existeHistorial[0]->dolor;
                                        $ARDOR = $existeHistorial[0]->ardor;
                                        $GOLPEOJOS = $existeHistorial[0]->golpeojos;
                                        $OTROM = $existeHistorial[0]->otroM;

                                        $MOLESTIAOTRO = $existeHistorial[0]->molestiaotro;
                                        $ULTIMOEXAMEN = $existeHistorial[0]->ultimoexamen;
                                        $ESFERICODER = $existeHistorial[0]->esfericoder;
                                        $CILINDRODER = $existeHistorial[0]->cilindroder;
                                        $EJEDER = $existeHistorial[0]->ejeder;
                                        $ADDDER = $existeHistorial[0]->addder;
                                        $ALTDER = $existeHistorial[0]->altder;
                                        $ESFERICOIZQ = $existeHistorial[0]->esfericoizq;
                                        $CILINDROIZQ = $existeHistorial[0]->cilindroizq;
                                        $EJEIZQ = $existeHistorial[0]->ejeizq;
                                        $ADDIZQ = $existeHistorial[0]->addizq;
                                        $ALTIZQ = $existeHistorial[0]->altizq;

                                        $ID_PRODUCTO = $existeHistorial[0]->id_producto;
                                        $ID_PAQUETE = $existeHistorial[0]->id_paquete;
                                        $MATERIAL = $existeHistorial[0]->material;
                                        $MATERIALOTRO = $existeHistorial[0]->materialotro;
                                        $COSTOMATERIAL = $existeHistorial[0]->costomaterial;
                                        $BIFOCAL = $existeHistorial[0]->bifocal;
                                        $FOTOCROMATICO = $existeHistorial[0]->fotocromatico;
                                        $AR = $existeHistorial[0]->ar;
                                        $TINTE = $existeHistorial[0]->tinte;
                                        $BLUERAY = $existeHistorial[0]->blueray;
                                        $OTROT = $existeHistorial[0]->otroT;
                                        $TRATAMIENTOOTRO = $existeHistorial[0]->tratamientootro;
                                        $COSTOTRATAMIENTO = $existeHistorial[0]->costotratamiento;
                                        $OBSERVACIONES = $existeHistorial[0]->observaciones;
                                        $OBSERVACIONESINTERNO = $existeHistorial[0]->observacionesinterno;
                                        $TIPO = $existeHistorial[0]->tipo;
                                        $BIFOCALOTRO = $existeHistorial[0]->bifocalotro;
                                        $COSTOBIFOCAL = $existeHistorial[0]->costobifocal;

                                        $EMBARAZADA = $existeHistorial[0]->embarazada;
                                        $DURMIOSEISOCHOHORAS = $existeHistorial[0]->durmioseisochohoras;
                                        $ACTIVIDADDIA = $existeHistorial[0]->actividaddia;
                                        $PROBLEMASOJOS = $existeHistorial[0]->problemasojos;
                                        $POLICARBONATOTIPO = $existeHistorial[0]->policarbonatotipo;
                                        $ID_TRATAMIENTOCOLORTINTE = $existeHistorial[0]->id_tratamientocolortinte;
                                        $ESTILOTINTE = $existeHistorial[0]->estilotinte;
                                        $POLARIZADO = $existeHistorial[0]->polarizado;
                                        $ID_TRATAMIENTOCOLORPOLARIZADO = $existeHistorial[0]->id_tratamientocolorpolarizado;
                                        $ESPEJO = $existeHistorial[0]->espejo;
                                        $ID_TRATAMIENTOCOLORESPEJO = $existeHistorial[0]->id_tratamientocolorespejo;
                                        $FOTOARMAZON = $existeHistorial[0]->fotoarmazon;
                                        $CREATED_AT = $existeHistorial[0]->created_at;
                                        $UPDATED_AT = $existeHistorial[0]->updated_at;
                                    }

                                    DB::table("historialclinico")->where("id", "=", $historial['ID'])->where("id_contrato", "=", $historial['ID_CONTRATO'])->update([
                                        "edad" => $EDAD,
                                        "fechaentrega" => $FECHAENTREGA,
                                        "diagnostico" => $DIAGNOSTICO,
                                        "ocupacion" => $OCUPACION,
                                        "diabetes" => $DIABETES,
                                        "hipertension" => $HIPERTENSION,
                                        "dolor" => $DOLOR,
                                        "ardor" => $ARDOR,
                                        "golpeojos" => $GOLPEOJOS,
                                        "otroM" => $OTROM,
                                        "molestiaotro" => $MOLESTIAOTRO,
                                        "ultimoexamen" => $ULTIMOEXAMEN,
                                        "esfericoder" => $ESFERICODER,
                                        "cilindroder" => $CILINDRODER,
                                        "ejeder" => $EJEDER,
                                        "addder" => $ADDDER,
                                        "altder" => $ALTDER,
                                        "esfericoizq" => $ESFERICOIZQ,
                                        "cilindroizq" => $CILINDROIZQ,
                                        "ejeizq" => $EJEIZQ,
                                        "addizq" => $ADDIZQ,
                                        "altizq" => $ALTIZQ,
                                        "id_producto" => $ID_PRODUCTO,
                                        "id_paquete" => $ID_PAQUETE,
                                        "material" => $MATERIAL,
                                        "materialotro" => $MATERIALOTRO,
                                        "costomaterial" => $COSTOMATERIAL,
                                        "bifocal" => $BIFOCAL,
                                        "fotocromatico" => $FOTOCROMATICO,
                                        "ar" => $AR,
                                        "tinte" => $TINTE,
                                        "blueray" => $BLUERAY,
                                        "otroT" => $OTROT,
                                        "tratamientootro" => $TRATAMIENTOOTRO,
                                        "costotratamiento" => $COSTOTRATAMIENTO,
                                        "observaciones" => $OBSERVACIONES,
                                        "observacionesinterno" => $OBSERVACIONESINTERNO,
                                        "tipo" => $TIPO,
                                        "bifocalotro" => $BIFOCALOTRO,
                                        "costobifocal" => $COSTOBIFOCAL,
                                        "embarazada" => $EMBARAZADA,
                                        "durmioseisochohoras" => $DURMIOSEISOCHOHORAS,
                                        "actividaddia" => $ACTIVIDADDIA,
                                        "problemasojos" => $PROBLEMASOJOS,
                                        "policarbonatotipo" => $POLICARBONATOTIPO,
                                        "id_tratamientocolortinte" => $ID_TRATAMIENTOCOLORTINTE,
                                        "estilotinte" => $ESTILOTINTE,
                                        "polarizado" => $POLARIZADO,
                                        "id_tratamientocolorpolarizado" => $ID_TRATAMIENTOCOLORPOLARIZADO,
                                        "espejo" => $ESPEJO,
                                        "id_tratamientocolorespejo" => $ID_TRATAMIENTOCOLORESPEJO,
                                        "fotoarmazon" => $FOTOARMAZON,
                                        "created_at" => $CREATED_AT,
                                        "updated_at" => $UPDATED_AT
                                    ]);

                                    //Actualizar datos historiales clinicos a mayusculas y quitar acentos
                                    $contratosGlobal::actualizarContratoHistorialesClinicosMayusculasAcentos($historial['ID_CONTRATO'], 1);
                                }
                            } else {
                                //No existe el historial
                                DB::table("historialclinico")->insert([
                                    "id" => $historial['ID'],
                                    "id_contrato" => $historial['ID_CONTRATO'],
                                    "edad" => $EDAD,
                                    "fechaentrega" => $FECHAENTREGA,
                                    "diagnostico" => $DIAGNOSTICO,
                                    "ocupacion" => $OCUPACION,
                                    "diabetes" => $DIABETES,
                                    "hipertension" => $HIPERTENSION,
                                    "dolor" => $DOLOR,
                                    "ardor" => $ARDOR,
                                    "golpeojos" => $GOLPEOJOS,
                                    "otroM" => $OTROM,
                                    "molestiaotro" => $MOLESTIAOTRO,
                                    "ultimoexamen" => $ULTIMOEXAMEN,
                                    "esfericoder" => $ESFERICODER,
                                    "cilindroder" => $CILINDRODER,
                                    "ejeder" => $EJEDER,
                                    "addder" => $ADDDER,
                                    "altder" => $ALTDER,
                                    "esfericoizq" => $ESFERICOIZQ,
                                    "cilindroizq" => $CILINDROIZQ,
                                    "ejeizq" => $EJEIZQ,
                                    "addizq" => $ADDIZQ,
                                    "altizq" => $ALTIZQ,
                                    "id_producto" => $ID_PRODUCTO,
                                    "id_paquete" => $ID_PAQUETE,
                                    "material" => $MATERIAL,
                                    "materialotro" => $MATERIALOTRO,
                                    "costomaterial" => $COSTOMATERIAL,
                                    "bifocal" => $BIFOCAL,
                                    "fotocromatico" => $FOTOCROMATICO,
                                    "ar" => $AR,
                                    "tinte" => $TINTE,
                                    "blueray" => $BLUERAY,
                                    "otroT" => $OTROT,
                                    "tratamientootro" => $TRATAMIENTOOTRO,
                                    "costotratamiento" => $COSTOTRATAMIENTO,
                                    "observaciones" => $OBSERVACIONES,
                                    "observacionesinterno" => $OBSERVACIONESINTERNO,
                                    "tipo" => $TIPO,
                                    "bifocalotro" => $BIFOCALOTRO,
                                    "costobifocal" => $COSTOBIFOCAL,
                                    "embarazada" => $EMBARAZADA,
                                    "durmioseisochohoras" => $DURMIOSEISOCHOHORAS,
                                    "actividaddia" => $ACTIVIDADDIA,
                                    "problemasojos" => $PROBLEMASOJOS,
                                    "policarbonatotipo" => $POLICARBONATOTIPO,
                                    "id_tratamientocolortinte" => $ID_TRATAMIENTOCOLORTINTE,
                                    "estilotinte" => $ESTILOTINTE,
                                    "polarizado" => $POLARIZADO,
                                    "id_tratamientocolorpolarizado" => $ID_TRATAMIENTOCOLORPOLARIZADO,
                                    "espejo" => $ESPEJO,
                                    "id_tratamientocolorespejo" => $ID_TRATAMIENTOCOLORESPEJO,
                                    "fotoarmazon" => $FOTOARMAZON,
                                    "created_at" => $CREATED_AT,
                                    "updated_at" => $UPDATED_AT
                                ]);

                                if ($TIPO == 0) {
                                    //Historial normal
                                    DB::update("UPDATE producto
                                    SET piezas = piezas - 1,
                                    updated_at = '$fechaActual'
                                    WHERE id = '" . $historial['ID_PRODUCTO'] . "'");
                                }

                                //Verificar numero de piezas en existencia del producto
                                $contratosGlobal::verificarPiezasRestantesProducto($historial['ID_PRODUCTO']);

                                //Actualizar datos historiales clinicos a mayusculas y quitar acentos
                                $contratosGlobal::actualizarContratoHistorialesClinicosMayusculasAcentos($historial['ID_CONTRATO'], 1);
                            }
                        }
                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonHistorialesClinicos: " . $historial['ID_CONTRATO'] . "\n" . $e);
                    continue;
                }

            }

        }//HISTORIALES

        if (!empty($jsonContratosProductos)) {
            //jsonContratosProductos es diferente a vacio

            foreach ($jsonContratosProductos as $productoContrato) {
                //Recorrido de jsonContratosProductos

                try {

                    $existeProductoContrato = DB::select("SELECT id FROM contratoproducto WHERE id = '" . $productoContrato['ID'] . "'
                                                                    AND id_contrato = '" . $productoContrato['ID_CONTRATO'] . "'
                                                                    AND id_franquicia = '$idFranquicia'");
                    if ($existeProductoContrato == null) { //Existe el producto en ese contrato?
                        //No existe
                        DB::table("contratoproducto")->insert([
                            "id" => $productoContrato['ID'],
                            "id_contrato" => $productoContrato['ID_CONTRATO'],
                            "id_producto" => $productoContrato['ID_PRODUCTO'],
                            "id_franquicia" => $idFranquicia,
                            "id_usuario" => $productoContrato['ID_USUARIO'],
                            "piezas" => $productoContrato['PIEZAS'],
                            "total" => $productoContrato['TOTAL'],
                            "created_at" => $productoContrato['CREATED_AT'],
                            "updated_at" => $productoContrato['UPDATED_AT']
                        ]);

                        DB::update("UPDATE producto
                                            SET piezas = piezas - '" . $productoContrato['PIEZAS'] . "',
                                            updated_at = '$fechaActual'
                                            WHERE id = '" . $productoContrato['ID_PRODUCTO'] . "'");

                        //Insertar o actualizar contrato tabla contratoslistatemporales
                        $contratosGlobal:: calculoTotal($productoContrato['ID_CONTRATO']);

                        //Verificar numero de piezas en existencia del producto
                        $contratosGlobal::verificarPiezasRestantesProducto($productoContrato['ID_PRODUCTO']);
                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonContratosProductos: " . $productoContrato['ID_CONTRATO'] . "\n" . $e);
                    continue;
                }
            }

        }//PRODUCTOS DE CONTRATO

        if (!empty($jsonProductosEliminados)) {
            //jsonProductosEliminados es diferente a vacio

            foreach ($jsonProductosEliminados as $productoEliminado) {
                //Recorrido de jsonProductosEliminados

                try {

                    DB::delete("DELETE FROM contratoproducto
                                        WHERE id='" . $productoEliminado['ID_CONTRATOPRODUCTO'] . "'
                                        AND id_contrato = '" . $productoEliminado['ID_CONTRATO'] . "'
                                        AND id_franquicia = '$idFranquicia'");
                    DB::update("UPDATE producto
                                            SET piezas = piezas + '" . $productoEliminado['PIEZAS'] . "',
                                            updated_at = '$fechaActual'
                                            WHERE id = '" . $productoEliminado['ID_PRODUCTO'] . "'");

                    //Insertar o actualizar contrato tabla contratoslistatemporales
                    $contratosGlobal:: calculoTotal($productoEliminado['ID_CONTRATO']);

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonProductosEliminados: " . $productoEliminado['ID_CONTRATO'] . "\n" . $e);
                    continue;
                }

            }

        }//PRODUCTOS ELIMINADOS

        if (!empty($jsonHistorialContratos)) {
            //jsonHistorialContratos es diferente a vacio

            foreach ($jsonHistorialContratos as $historialContrato) {
                //Recorrido de jsonHistorialContratos

                try {

                    $existeHistorialContrato = DB::select("SELECT id FROM historialcontrato WHERE id= '" . $historialContrato['ID'] . "'
                                                                    AND id_usuarioC = '" . $historialContrato['ID_USUARIOC'] . "'");
                    if ($existeHistorialContrato == null) {
                        //No existe el movimiento en la BD
                        $tipomensaje = $historialContrato['TIPOMENSAJE'];
                        DB::table("historialcontrato")->insert([
                            "id" => $historialContrato['ID'],
                            "id_contrato" => $historialContrato['ID_CONTRATO'],
                            "id_usuarioC" => $historialContrato['ID_USUARIOC'],
                            "cambios" => $historialContrato['CAMBIOS'],
                            "tipomensaje" => $tipomensaje,
                            "created_at" => $historialContrato['CREATED_AT'],
                            "updated_at" => $historialContrato['UPDATED_AT']
                        ]);
                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonHistorialContratos: " . $historialContrato['ID_CONTRATO'] . "\n" . $e);
                    continue;
                }

            }

        }//HISTORIAL DE MOVIMIENTO DE LOS CONTRATOS

        if (!empty($jsonPromocionContratos)) {
            //jsonPromocionContratos es diferente a vacio

            foreach ($jsonPromocionContratos as $promocionDeUnContrato) {
                //Recorrido de jsonPromocionContratos

                try {

                    $existePromocionContrato = DB::select("SELECT id FROM promocioncontrato WHERE id_contrato = '" . $promocionDeUnContrato['ID_CONTRATO'] . "'
                                                                    AND id_promocion = '" . $promocionDeUnContrato['ID_PROMOCION'] . "'");
                    if ($existePromocionContrato == null) {
                        //No existe la promocion para el contrato
                        DB::table("promocioncontrato")->insert([
                            "id" => $promocionDeUnContrato['ID'],
                            "id_contrato" => $promocionDeUnContrato['ID_CONTRATO'],
                            "id_promocion" => $promocionDeUnContrato['ID_PROMOCION'],
                            "estado" => $promocionDeUnContrato['ESTADO'],
                            "created_at" => $promocionDeUnContrato['CREATED_AT'],
                            "updated_at" => $promocionDeUnContrato['UPDATED_AT'],
                            "id_franquicia" => $idFranquicia
                        ]);
                    } else {
                        //Ya existe la promocion
                        DB::table("promocioncontrato")
                            ->where("id", "=", $promocionDeUnContrato['ID'])
                            ->where("id_contrato", "=", $promocionDeUnContrato['ID_CONTRATO'])
                            ->where("id_promocion", "=", $promocionDeUnContrato['ID_PROMOCION'])
                            ->where("id_franquicia", "=", $idFranquicia)
                            ->update([
                                "estado" => $promocionDeUnContrato['ESTADO'],
                                "updated_at" => $promocionDeUnContrato['UPDATED_AT']
                            ]);
                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonPromocionContratos: " . $promocionDeUnContrato['ID_CONTRATO'] . "\n" . $e);
                    continue;
                }

            }

        }//PROMOCIONES DE CADA UNO DE LOS CONTRATOS

        if (!empty($jsonPromocionesEliminadas)) {
            //jsonPromocionesEliminadas es diferente a vacio

            foreach ($jsonPromocionesEliminadas as $promocionEliminada) {
                //Recorrido de jsonPromocionesEliminadas

                try {

                    DB::delete("DELETE FROM promocioncontrato
                                        WHERE id = '" . $promocionEliminada['ID_PROMOCIONCONTRATO'] . "'
                                        AND id_contrato = '" . $promocionEliminada['ID_CONTRATO'] . "'
                                        AND id_franquicia = '$idFranquicia'");

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonPromocionesEliminadas: " . $promocionEliminada['ID_CONTRATO'] . "\n" . $e);
                    continue;
                }

            }

        }//PROMOCIONES ELIMINADAS

        if (!empty($jsonPayments)) {
            //jsonPayments es diferente a vacio

            foreach ($jsonPayments as $payment) {
                //Recorrido de jsonPayments

                try {

                    DB::table("payments")->insert([
                        "payment_id" => $payment['PAYMENT_ID'],
                        "payer_email" => $payment['PAYER_EMAIL'],
                        "id_abono" => $payment['ID_ABONO'],
                        "amount" => $payment['AMOUNT'],
                        "currency" => $payment['CURRENCY'],
                        "payment_status" => $payment['PAYMENT_STATUS'],
                        "tipoorigen" => $payment['TIPOORIGEN'],
                        "created_at" => $payment['CREATED_AT'],
                        "updated_at" => $payment['UPDATED_AT']
                    ]);

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonPayments: " . $payment['ID_ABONO'] . "\n" . $e);
                    continue;
                }

            }

        }//PAYMENTS

        if (!empty($jsonDatosStripe)) {
            //jsonPayments es diferente a vacio

            foreach ($jsonDatosStripe as $datosstripe) {
                //Recorrido de jsonDatosStripe

                try {

                    DB::table("datosstripe")->insert([
                        "id_contrato" => $datosstripe['ID_CONTRATO'],
                        "id_paymentintent" => $datosstripe['ID_PAYMENTINTENT'],
                        "id_paymentmethod" => $datosstripe['ID_PAYMENTMETHOD'],
                        "created_at" => $datosstripe['CREATED_AT'],
                        "updated_at" => $datosstripe['UPDATED_AT']
                    ]);

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonDatosStripe: " . $datosstripe['ID_CONTRATO'] . "\n" . $e);
                    continue;
                }

            }

        }//DATOSSTRIPE

        if (!empty($jsonGarantias)) {
            //$jsonGarantias es diferente a vacio

            foreach ($jsonGarantias as $garantia) {
                //Recorrido de jsonGarantias

                try {

                    $ID = self::validacionDeNulo($garantia['ID']);
                    $ID_CONTRATO = self::validacionDeNulo($garantia['ID_CONTRATO']);
                    $ID_HISTORIAL = self::validacionDeNulo($garantia['ID_HISTORIAL']);
                    $ID_HISTORIALGARANTIA = self::validacionDeNulo($garantia['ID_HISTORIALGARANTIA']);
                    $ID_OPTOMETRISTA = self::validacionDeNulo($garantia['ID_OPTOMETRISTA']);
                    $ESTADOGARANTIA = self::validacionDeNulo($garantia['ESTADOGARANTIA']);
                    $CREATED_AT = self::validacionDeNulo($garantia['CREATED_AT']);
                    $UPDATED_AT = self::validacionDeNulo($garantia['UPDATED_AT']);

                    $existeGarantia = DB::select("SELECT id FROM garantias WHERE id = '" . $ID . "' AND id_contrato = '" . $ID_CONTRATO . "' AND estadogarantia = 1");
                    if ($existeGarantia != null) { //Existe el garantia?
                        //Existe garantia
                        DB::table("garantias")->where("id", "=", $ID)->update([
                            "id_historialgarantia" => $ID_HISTORIALGARANTIA,
                            "estadogarantia" => $ESTADOGARANTIA,
                            "updated_at" => $UPDATED_AT
                        ]);

                    } else {
                        //No existe la garantia

                        //Validamos si existe la garantia (Esto se hace por que se vuelve a insertar el registro)
                        $existeGarantia = DB::select("SELECT id FROM garantias WHERE id = '" . $ID . "' AND id_contrato = '" . $ID_CONTRATO . "'");

                        if($existeGarantia == null) {
                            //No existe garantia

                            DB::table("garantias")->insert([
                                "id" => $ID,
                                "id_contrato" => $ID_CONTRATO,
                                "id_historial" => $ID_HISTORIAL,
                                "id_historialgarantia" => $ID_HISTORIALGARANTIA,
                                "id_optometrista" => $ID_OPTOMETRISTA,
                                "estadogarantia" => $ESTADOGARANTIA,
                                "created_at" => $CREATED_AT,
                                "updated_at" => $UPDATED_AT
                            ]);
                        }
                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonGarantias: " . $garantia['ID_CONTRATO'] . "\n" . $e);
                    continue;
                }

            }

        }//GARANTIAS

        if (!empty($jsonRuta)) {
            //$jsonRuta es diferente a vacio

            foreach ($jsonRuta as $ruta) {
                //Recorrido de jsonRuta

                try {

                    $ID = self::validacionDeNulo($ruta['ID']);
                    $DIA = self::validacionDeNulo($ruta['DIA']);
                    $ID_CONTRATO = self::validacionDeNulo($ruta['ID_CONTRATO']);
                    $ID_USUARIO = self::validacionDeNulo($ruta['ID_USUARIO']);
                    $POSICION = self::validacionDeNulo($ruta['POSICION']);
                    $ESTADO = self::validacionDeNulo($ruta['ESTADO']);

                    $existeRuta = DB::select("SELECT id FROM ruta WHERE id = '" . $ID . "' AND id_usuario = '" . $ID_USUARIO . "'");
                    if ($existeRuta != null) { //Existe el ruta?
                        //Existe ruta
                        DB::table("ruta")->where("id", "=", $ID)->where("id_usuario", "=", $ID_USUARIO)->update([
                            "id_contrato" => $ID_CONTRATO,
                            "posicion" => $POSICION,
                            "estado" => $ESTADO
                        ]);

                    } else {
                        //No existe la ruta
                        DB::table("ruta")->insert([
                            "id" => $ID,
                            "dia" => $DIA,
                            "id_contrato" => $ID_CONTRATO,
                            "id_usuario" => $ID_USUARIO,
                            "posicion" => $POSICION,
                            "estado" => $ESTADO
                        ]);
                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonRuta: " . $ruta['ID_CONTRATO'] . "\n" . $e);
                    continue;
                }

            }

        }//RUTA

        if (!empty($jsonHistorialesSinConversion)) {
            //$jsonHistorialesSinConversion es diferente a vacio

            foreach ($jsonHistorialesSinConversion as $historialsinconversion) {
                //Recorrido de jsonHistorialesSinConversion

                try {

                    $ID_CONTRATO = self::validacionDeNulo($historialsinconversion['ID_CONTRATO']);
                    $ID_HISTORIAL = self::validacionDeNulo($historialsinconversion['ID_HISTORIAL']);
                    $ESFERICODER = self::validacionDeNulo($historialsinconversion['ESFERICODER']);
                    $CILINDRODER = self::validacionDeNulo($historialsinconversion['CILINDRODER']);
                    $EJEDER = self::validacionDeNulo($historialsinconversion['EJEDER']);
                    $ADDDER = self::validacionDeNulo($historialsinconversion['ADDDER']);
                    $ESFERICOIZQ = self::validacionDeNulo($historialsinconversion['ESFERICOIZQ']);
                    $CILINDROIZQ = self::validacionDeNulo($historialsinconversion['CILINDROIZQ']);
                    $EJEIZQ = self::validacionDeNulo($historialsinconversion['EJEIZQ']);
                    $ADDIZQ = self::validacionDeNulo($historialsinconversion['ADDIZQ']);
                    $CREATED_AT = self::validacionDeNulo($historialsinconversion['CREATED_AT']);

                    $existeHistorialSinConversion = DB::select("SELECT id_contrato FROM historialsinconversion
                                                                        WHERE id_contrato = '" . $ID_CONTRATO . "' AND id_historial = '" . $ID_HISTORIAL . "'");
                    if ($existeHistorialSinConversion != null) { //Existe el historialsinconversion?
                        //Existe historialsinconversion
                        DB::table("historialsinconversion")->where("id_contrato", "=", $ID_CONTRATO)->where("id_historial", "=", $ID_HISTORIAL)->update([
                            "esfericoder" => $ESFERICODER,
                            "cilindroder" => $CILINDRODER,
                            "ejeder" => $EJEDER,
                            "addder" => $ADDDER,
                            "esfericoizq" => $ESFERICOIZQ,
                            "cilindroizq" => $CILINDROIZQ,
                            "ejeizq" => $EJEIZQ,
                            "addizq" => $ADDIZQ
                        ]);

                    } else {
                        //No existe el historialsinconversion
                        DB::table("historialsinconversion")->insert([
                            "id_contrato" => $ID_CONTRATO,
                            "id_historial" => $ID_HISTORIAL,
                            "esfericoder" => $ESFERICODER,
                            "cilindroder" => $CILINDRODER,
                            "ejeder" => $EJEDER,
                            "addder" => $ADDDER,
                            "esfericoizq" => $ESFERICOIZQ,
                            "cilindroizq" => $CILINDROIZQ,
                            "ejeizq" => $EJEIZQ,
                            "addizq" => $ADDIZQ,
                            "created_at" => $CREATED_AT
                        ]);
                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonHistorialesSinConversion: " . $historialsinconversion['ID_CONTRATO'] . "\n" . $e);
                    continue;
                }

            }

        }//HISTORIALES SIN CONVERSION

        if (!empty($jsonBuzon)) {
            //$jsonBuzon es diferente a vacio

            foreach ($jsonBuzon as $quejaSugerencia) {
                //Recorrido de $jsonBuzon

                try {

                    DB::table("buzon")->insert([
                        "id_usuario" => $quejaSugerencia['ID_USUARIO'],
                        "id_franquicia" => $quejaSugerencia['ID_FRANQUICIA'],
                        "mensaje" => $quejaSugerencia['MENSAJE'],
                        "created_at" => $quejaSugerencia['CREATED_AT']
                    ]);

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : $jsonBuzon: " . $quejaSugerencia['ID_USUARIO'] . "\n" . $e);
                    continue;
                }

            }

        }//BUZON

        if (!empty($jsonHistorialMovimientosFotosContratos)) {
            //$jsonHistorialMovimientosFotosContratos es diferente a vacio

            foreach ($jsonHistorialMovimientosFotosContratos as $historialContrato) {
                //Recorrido de jsonHistorialContratos

                try {

                    $existeHistorialContrato = DB::select("SELECT id FROM historialfotoscontrato WHERE id= '" . $historialContrato['ID'] . "'
                                                                    AND id_usuarioC = '" . $historialContrato['ID_USUARIOC'] . "'");
                    if ($existeHistorialContrato == null) {
                        //No existe el movimiento en la BD
                        $tipomensaje = $historialContrato['TIPOMENSAJE'];
                        DB::table("historialfotoscontrato")->insert([
                            "id" => $historialContrato['ID'],
                            "id_contrato" => $historialContrato['ID_CONTRATO'],
                            "id_usuarioC" => $historialContrato['ID_USUARIOC'],
                            "foto" => 'uploads/imagenes/contratos/fotomovimiento/' . $historialContrato['FOTO'], //CONCATENAR LA RUTA CON EL NOMBRE
                            "observaciones" => $historialContrato['OBSERVACIONES'],
                            "tipomensaje" => $tipomensaje,
                            "created_at" => $historialContrato['CREATED_AT'],
                            "updated_at" => $historialContrato['UPDATED_AT']
                        ]);
                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonHistorialFotosContratos: " . $historialContrato['ID_CONTRATO'] . "\n" . $e);
                    continue;
                }

            }

        }//HISTORIAL DE MOVIMIENTO CON FOTOS DE LOS CONTRATOS

        $datos[0]["codigo"] = "JGvcYgZn8PD4KpIm4uIN";
        //\Log::info("{'datos': '" . base64_encode(json_encode($datos)) . "'}");
        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

    }

    private static function insertarActualizarAbonosYAbonosEliminados($idFranquicia, $id_usuario, $rol_id, $jsonAbonos, $jsonAbonosEliminados)
    {

        $contador = 0;
        if (!empty($jsonAbonos)) {
            //jsonAbonos es diferente a vacio

            foreach ($jsonAbonos as $abono) {
                //Recorrido de jsonAbonos

                try {

                    //Validacion estado contrato
                    $contrato = DB::select("SELECT id, estatus_estadocontrato, id_zona FROM contratos WHERE id = '" . $abono['ID_CONTRATO'] . "'");

                    if ($contrato != null) {

                        $estadoContratoActual = $contrato[0]->estatus_estadocontrato;

                        if ($estadoContratoActual == 0 || $estadoContratoActual == 1 || $estadoContratoActual == 9
                            || $estadoContratoActual == 12 || $estadoContratoActual == 2 || $estadoContratoActual == 4
                            || $estadoContratoActual == 5 || $estadoContratoActual == 7 || $estadoContratoActual == 10 || $estadoContratoActual == 11) {
                            //NO TERMINADO, TERMINADO, EN PROCESO DE APROBACION, ENVIADO, ENTREGADO, ABONOATRASADO, LIQUIDADO, APROBADO, MANUFACTURA, EN PROCESO DE ENVIO

                            $FOLIO = self::validacionDeNulo($abono['FOLIO']);
                            $CORTE = self::validacionDeNulo($abono['CORTE']);

                            if ($contador == 0 && $CORTE === "1") {
                                DB::table("abonos")->where("id_usuario", "=", $id_usuario)->where("corte", "=", "0")->update([
                                    "corte" => "2"
                                ]);
                                $contador++;
                            }

                            if ($CORTE == "1") {
                                $CORTE = "2";
                            }

                            $existeAbono = DB::select("SELECT id FROM abonos
                                                                WHERE id = '" . $abono['ID'] . "'
                                                                AND id_contrato = '" . $abono['ID_CONTRATO'] . "'");

                            if ($existeAbono != null) { //Existe el abono?
                                //Existe

                                //Actualizar en tabla abonos
                                DB::table("abonos")->where("id", "=", $abono['ID'])->where("id_contrato", "=", $abono['ID_CONTRATO'])->update([
                                    "tipoabono" => $abono['TIPOABONO']
                                ]);

                                //Actualizar en tabla abonoscontratostemporalessincronizacion
                                DB::table("abonoscontratostemporalessincronizacion")->where("id", "=", $abono['ID'])
                                    ->where("id_contrato", "=", $abono['ID_CONTRATO'])->update([
                                    "tipoabono" => $abono['TIPOABONO']
                                ]);

                            } else {
                                //No existe
                                $ID_CONTRATOPRODUCTO = self::validacionDeNulo($abono['ID_CONTRATOPRODUCTO']);

                                //Insertar en tabla abonos
                                DB::table("abonos")->insert([
                                    "id" => $abono['ID'],
                                    "folio" => $FOLIO,
                                    "id_franquicia" => $idFranquicia,
                                    "id_contrato" => $abono['ID_CONTRATO'],
                                    "id_usuario" => $abono['ID_USUARIO'],
                                    "abono" => $abono['ABONO'],
                                    "adelantos" => $abono['ADELANTOS'],
                                    "tipoabono" => $abono['TIPOABONO'],
                                    "atraso" => $abono['ATRASO'],
                                    "metodopago" => $abono['METODOPAGO'],
                                    "corte" => $CORTE,
                                    "poliza" => null,
                                    "id_contratoproducto" => $ID_CONTRATOPRODUCTO,
                                    "id_zona" => $contrato[0]->id_zona,
                                    "fecharegistro" => Carbon::now(),
                                    "coordenadas" => $abono['COORDENADAS'],
                                    "created_at" => $abono['CREATED_AT'],
                                    "updated_at" => $abono['UPDATED_AT']
                                ]);

                                self::eliminarAbonosRepetidosContrato();

                                if ($rol_id == 4) {
                                    //Cobranza

                                    $contratosGlobal = new contratosGlobal;
                                    $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($contrato[0]->id_zona);

                                    if ($cobradoresAsignadosAZona != null) {
                                        //Existen cobradores asignados a la zona
                                        foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                            //Recorrido cobradores
                                            //Insertar en tabla abonoscontratostemporalessincronizacion
                                            DB::table("abonoscontratostemporalessincronizacion")->insert([
                                                "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                                "id" => $abono['ID'],
                                                "folio" => $FOLIO,
                                                "id_contrato" => $abono['ID_CONTRATO'],
                                                "id_usuario" => $abono['ID_USUARIO'],
                                                "abono" => $abono['ABONO'],
                                                "adelantos" => $abono['ADELANTOS'],
                                                "tipoabono" => $abono['TIPOABONO'],
                                                "atraso" => $abono['ATRASO'],
                                                "metodopago" => $abono['METODOPAGO'],
                                                "corte" => $CORTE,
                                                "coordenadas" => $abono['COORDENADAS'],
                                                "created_at" => $abono['CREATED_AT'],
                                                "updated_at" => $abono['UPDATED_AT']
                                            ]);
                                        }
                                    }

                                    self::eliminarAbonosContratosTemporalesRepetidosContrato();

                                }

                            }

                        }

                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonAbonos: " . $abono['ID_CONTRATO'] . "\n" . $e);
                    continue;
                }

            }

        }//ABONOS

        if (!empty($jsonAbonosEliminados)) {
            //jsonAbonosEliminados es diferente a vacio

            foreach ($jsonAbonosEliminados as $abonoEliminado) {
                //Recorrido de jsonAbonosEliminados

                try {

                    //Validacion estado contrato
                    $contrato = DB::select("SELECT id, estatus_estadocontrato FROM contratos WHERE id = '" . $abonoEliminado['ID_CONTRATO'] . "'");

                    if ($contrato != null) {

                        $estadoContratoActual = $contrato[0]->estatus_estadocontrato;

                        if ($estadoContratoActual == 0 || $estadoContratoActual == 1 || $estadoContratoActual == 9
                            || $estadoContratoActual == 12 || $estadoContratoActual == 2 || $estadoContratoActual == 4
                            || $estadoContratoActual == 5 || $estadoContratoActual == 7 || $estadoContratoActual == 10 || $estadoContratoActual == 11) {
                            //NO TERMINADO, TERMINADO, EN PROCESO DE APROBACION, ENVIADO, ENTREGADO, ABONOATRASADO, LIQUIDADO, APROBADO, MANUFACTURA, EN PROCESO DE ENVIO
                            if ($rol_id == 4) {
                                //Cobranza

                                $abonosInsertarEliminados = DB::select("SELECT * FROM abonos
                                                WHERE folio = '" . $abonoEliminado['ID_ABONO'] . "'
                                                    AND id_contrato = '" . $abonoEliminado['ID_CONTRATO'] . "'");

                                if ($abonosInsertarEliminados != null) {
                                    //Hay abonos
                                    foreach ($abonosInsertarEliminados as $abonoInsertarEliminado) {
                                        //Recorrido de abonos a insertar en tabla abonoseliminados
                                        DB::table("abonoseliminados")->insert([
                                            "id" => $abonoInsertarEliminado->id,
                                            "folio" => $abonoInsertarEliminado->folio,
                                            "id_franquicia" => $abonoInsertarEliminado->id_franquicia,
                                            "id_contrato" => $abonoInsertarEliminado->id_contrato,
                                            "id_usuario" => $abonoInsertarEliminado->id_usuario,
                                            "abono" => $abonoInsertarEliminado->abono,
                                            "adelantos" => $abonoInsertarEliminado->adelantos,
                                            "tipoabono" => $abonoInsertarEliminado->tipoabono,
                                            "atraso" => $abonoInsertarEliminado->atraso,
                                            "metodopago" => $abonoInsertarEliminado->metodopago,
                                            "corte" => $abonoInsertarEliminado->corte,
                                            "poliza" => $abonoInsertarEliminado->poliza,
                                            "id_corte" => $abonoInsertarEliminado->id_corte,
                                            "created_at" => $abonoInsertarEliminado->created_at,
                                            "updated_at" => $abonoInsertarEliminado->updated_at
                                        ]);
                                    }

                                    //Eliminar en tabla abonos
                                    DB::delete("DELETE FROM abonos
                                                    WHERE folio = '" . $abonoEliminado['ID_ABONO'] . "'
                                                    AND id_contrato = '" . $abonoEliminado['ID_CONTRATO'] . "'");

                                    //Eliminar en tabla abonoscontratostemporalessincronizacion
                                    DB::delete("DELETE FROM abonoscontratostemporalessincronizacion
                                                    WHERE folio = '" . $abonoEliminado['ID_ABONO'] . "'
                                                    AND id_contrato = '" . $abonoEliminado['ID_CONTRATO'] . "'");

                                }

                            }else {
                                //Asistente/Opto -> Consultar y eliminar abonos por id (abonos agregados por Asistente/opto no tienen folio)

                                $abonoInsertarEliminados = DB::select("SELECT * FROM abonos
                                                WHERE id = '" . $abonoEliminado['ID_ABONO'] . "'
                                                    AND id_contrato = '" . $abonoEliminado['ID_CONTRATO'] . "'");

                                if ($abonoInsertarEliminados != null) {
                                    //Hay abonos

                                    //Recorrido de abonos a insertar en tabla abonoseliminados
                                    DB::table("abonoseliminados")->insert([
                                        "id" => $abonoInsertarEliminados[0]->id,
                                        "folio" => $abonoInsertarEliminados[0]->folio,
                                        "id_franquicia" => $abonoInsertarEliminados[0]->id_franquicia,
                                        "id_contrato" => $abonoInsertarEliminados[0]->id_contrato,
                                        "id_usuario" => $abonoInsertarEliminados[0]->id_usuario,
                                        "abono" => $abonoInsertarEliminados[0]->abono,
                                        "adelantos" => $abonoInsertarEliminados[0]->adelantos,
                                        "tipoabono" => $abonoInsertarEliminados[0]->tipoabono,
                                        "atraso" => $abonoInsertarEliminados[0]->atraso,
                                        "metodopago" => $abonoInsertarEliminados[0]->metodopago,
                                        "corte" => $abonoInsertarEliminados[0]->corte,
                                        "poliza" => $abonoInsertarEliminados[0]->poliza,
                                        "id_corte" => $abonoInsertarEliminados[0]->id_corte,
                                        "created_at" => $abonoInsertarEliminados[0]->created_at,
                                        "updated_at" => $abonoInsertarEliminados[0]->updated_at
                                    ]);

                                    //Eliminar tabla abonos
                                    DB::delete("DELETE FROM abonos
                                                    WHERE id = '" . $abonoEliminado['ID_ABONO'] . "'
                                                    AND id_contrato = '" . $abonoEliminado['ID_CONTRATO'] . "'");

                                    //Eliminar en tabla abonoscontratostemporalessincronizacion
                                    DB::delete("DELETE FROM abonoscontratostemporalessincronizacion
                                                    WHERE id = '" . $abonoEliminado['ID_ABONO'] . "'
                                                    AND id_contrato = '" . $abonoEliminado['ID_CONTRATO'] . "'");
                                }
                            }

                        }

                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonAbonosEliminados: " . $abonoEliminado['ID_CONTRATO'] . "\n" . $e);
                    continue;
                }

            }

        }//ABONOS ELIMINADOS

    }

    private static function obtenerJsonDecodificado($datos)
    {
        $datosDecodificado = base64_decode($datos);
        $json = json_decode($datosDecodificado, true);
        return $json;
    }

    private static function validacionDeNulo($valor)
    {
        $respuesta = null;
        if (strlen($valor) > 0) {
            $respuesta = $valor;
        }
        return $respuesta;
    }

    public static function generarIdAlfanumerico($tabla, $length)
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $temporalId = self::generadorRandom($length);
            $existente = DB::select("select id from $tabla where id = '$temporalId'");
            if (sizeof($existente) == 0) {
                $unico = $temporalId;
                $esUnico = true;
            }
        }
        return $unico;
    }   // Comparar si ya existe el id en la base de datos

    public static function generadorRandom($length)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($caracteres);
        $randomId = '';
        for ($i = 0; $i < $length; $i++) {
            $randomId .= $caracteres[rAND(0, $charactersLength - 1)];
        }
        return $randomId;
    }

    public static function obtenerPromocionesContratos($contratos)
    {

        $promocionContrato = array();
        $array = array(); //Bandera de ids de promocioncontrato para que no se mande 2 veces
        if ($contratos != null) {
            //Tiene contratos creados
            foreach ($contratos as $contrato) {
                //Recorrido contratos
                $idContrato = $contrato->id;
                if ($contrato->idcontratorelacion != null) {
                    //Es un hijo
                    $idContrato = $contrato->idcontratorelacion;
                }
                $promocionC = DB::select("SELECT IFNULL(id, '') as id,
                                                       IFNULL(id_contrato, '') as id_contrato,
                                                       IFNULL(id_promocion, '') as id_promocion,
                                                       IFNULL(estado, '') as estado,
                                                       IFNULL(created_at, '') as created_at,
                                                       IFNULL(updated_at, '') as updated_at
                                                FROM promocioncontrato
                                                WHERE id_contrato = '$idContrato'");

                if ($promocionC != null) {
                    //Hay promociones contrato
                    if (!in_array($promocionC[0]->id, $array)) {
                        //No existe promocion en el arreglo $promocionContrato
                        $cadena = str_replace("[", "", json_encode($promocionC)); //Remplazar [ del json
                        $cadena = str_replace("]", "", $cadena); //Remplazar ] del json
                        array_push($promocionContrato, json_decode($cadena, true)); //Agregacion de json ya modificaco sin los [] al principio y al final
                        array_push($array, $promocionC[0]->id); //Se agrega el id de promocioncontrato al array para que este no vuelva a insertarse de nuevo
                    }
                }
            }
        }

        return $promocionContrato;

    }

    public static function obtenerEstadoPromocion($idContrato, $idFranquicia)
    {
        $respuesta = false;

        $contrato = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
        if ($contrato[0]->idcontratorelacion != null) {
            //Es un contrato hijo
            $idContrato = $contrato[0]->idcontratorelacion;
        }

        $promocioncontrato = DB::select("SELECT * FROM promocioncontrato WHERE id_franquicia = '$idFranquicia' AND id_contrato = '$idContrato'");

        if ($promocioncontrato != null) {
            if ($promocioncontrato[0]->estado == 1) {
                //Promocion esta activa
                $respuesta = true;
            }
        }
        return $respuesta;
    }

    public static function obtenerGarantiasContratos($contratos, $rol_id, $nowParse)
    {

        $queryRol = " AND (estadogarantia = 1
                        OR (estadogarantia = 2 AND STR_TO_DATE(updated_at,'%Y-%m-%d') = STR_TO_DATE('$nowParse','%Y-%m-%d')))";
        if($rol_id == 4) {
            //Cobranza
            $queryRol = " AND estadogarantia IN (0,1,2,3) ORDER BY created_at DESC limit 1";
        }

        $garantiasContrato = array();
        if ($contratos != null) {
            //Tiene contratos creados
            foreach ($contratos as $contrato) {
                //Recorrido contratos
                $idContrato = $contrato->id;
                $garantiaC = DB::select("SELECT IFNULL(id, '') as id,
                                                      IFNULL(id_contrato, '') as id_contrato,
                                                      IFNULL(id_historial, '') as id_historial,
                                                      IFNULL(id_historialgarantia, '') as id_historialgarantia,
                                                      IFNULL(id_optometrista, '') as id_optometrista,
                                                      IFNULL(estadogarantia, '') as estadogarantia,
                                                      IFNULL(created_at, '') as created_at,
                                                      IFNULL(updated_at, '') as updated_at
                                                FROM garantias
                                                WHERE id_contrato = '" . $idContrato . "'
                                                " . $queryRol);

                if ($garantiaC != null) {
                    //Hay garantias contrato
                    foreach ($garantiaC as $garantia) {
                        array_push($garantiasContrato, $garantia);
                    }
                }
            }
        }

        return $garantiasContrato;

    }

    public static function obtenerDia($now, $diaActualNumero, $diaSeleccionado)
    {
        //Dia seleccionado
        // 0-> SabadoAnterior
        // 1-> ViernesSiguiente

        switch ($diaActualNumero) {
            case 1://Es Lunes
                if ($diaSeleccionado == 0) {
                    return $now->subDays(2)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return $now->addDays(6)->format('Y-m-d'); //Obtengo la fecha del dia viernes
                }
                break;
            case 2://Es Martes
                if ($diaSeleccionado == 0) {
                    return $now->subDays(3)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return $now->addDays(6)->format('Y-m-d'); //Obtengo la fecha del dia viernes
                }
                break;
            case 3://Es Miercoles
                if ($diaSeleccionado == 0) {
                    return $now->subDays(4)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return $now->addDays(6)->format('Y-m-d'); //Obtengo la fecha del dia viernes
                }
                break;
            case 4://Es Jueves
                if ($diaSeleccionado == 0) {
                    return $now->subDays(5)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return $now->addDays(6)->format('Y-m-d'); //Obtengo la fecha del dia viernes
                }
                break;
            case 5://Es Viernes
                if ($diaSeleccionado == 0) {
                    return $now->subDays(6)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return $now->addDays(6)->format('Y-m-d'); //Obtengo la fecha del dia viernes
                }
                break;
            case 6://Es Sabado
                if ($diaSeleccionado == 0) {
                    return $now->subDays(0)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return $now->addDays(6)->format('Y-m-d'); //Obtengo la fecha del dia viernes
                }
                break;
            case 7://Es Domingo
                if ($diaSeleccionado == 0) {
                    return $now->subDays(1)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return $now->addDays(6)->format('Y-m-d'); //Obtengo la fecha del dia viernes
                }
                break;
        }
        return $now;
    }

    public static function tieneAbonoEnFechas($ultimoabono, $fechaini, $fechafin)
    {
        if (is_null($ultimoabono) || strlen($ultimoabono) == 0) {
            return false;
        } else {
            if (Carbon::parse($ultimoabono)->format('Y-m-d') >= Carbon::parse($fechaini)->format('Y-m-d')
                && Carbon::parse($ultimoabono)->format('Y-m-d') <= Carbon::parse($fechafin)->format('Y-m-d')) {
                return true;
            }
        }

        return false;
    }

    public static function obtenerRuta($contratos, $idUsuario)
    {

        try {

            //Eliminar todos los registros con estado 0
            DB::delete("DELETE FROM ruta WHERE id_usuario = '$idUsuario' AND estado = '0'");

            //Actualizar posiciones ruta
            $rutas = DB::select("SELECT * FROM ruta WHERE id_usuario = '$idUsuario' ORDER BY CAST(dia AS signed), CAST(posicion AS signed) ASC");
            $posicionNueva = 0;
            $banderaDia = 0;

            foreach ($rutas as $ruta) {

                if ($ruta->dia != $banderaDia) {
                    $banderaDia = $ruta->dia;
                    $posicionNueva = 0;
                }

                if (array_search($ruta->id_contrato, array_column($contratos, 'id'))) {
                    //Se encontro el id_contrato

                    DB::table("ruta")
                        ->where("id", "=", $ruta->id)
                        ->where("id_usuario", "=", $ruta->id_usuario)
                        ->where("dia", "=", $banderaDia)
                        ->update(["posicion" => $posicionNueva]);

                    if ($ruta->estado == 2) {
                        DB::table("ruta")
                            ->where("id", "=", $ruta->id)
                            ->where("id_usuario", "=", $ruta->id_usuario)
                            ->where("dia", "=", $banderaDia)
                            ->update(["estado" => 1]);
                    }

                } else {
                    //No se encontro el id_contrato
                    DB::table("ruta")
                        ->where("id", "=", $ruta->id)
                        ->where("id_usuario", "=", $ruta->id_usuario)
                        ->where("dia", "=", $banderaDia)
                        ->update(["posicion" => $posicionNueva, "estado" => '2']);
                }

                $posicionNueva++;
            }

            //Obtener rutas actualizadas
            $rutasActualizadas = DB::select("SELECT * FROM ruta WHERE id_usuario = '$idUsuario' ORDER BY CAST(dia AS signed), CAST(posicion AS signed) ASC");

            return $rutasActualizadas;

        } catch (\Exception $e) {
            \Log::info("Error:" . $e);
        }


    }

    public static function obtenerJsonAbonosGeneral($idUsuario)
    {

        try {

            $jsonabonosgeneral = array();
            $abonos = DB::select("SELECT abono FROM abonos WHERE id_usuario = '$idUsuario' AND corte = '0'");

            foreach ($abonos as $abono) {

                $abonoentero = intval($abono->abono);

                if (array_key_exists($abonoentero, $jsonabonosgeneral)) {
                    //Existe la llave del abono
                    $jsonabonosgeneral[$abonoentero] = $jsonabonosgeneral[$abonoentero] + 1;

                } else {
                    //No se encontro la llave del abono
                    $jsonabonosgeneral[$abonoentero] = 1;
                }

            }

        } catch (\Exception $e) {
            \Log::info("Error:" . $e);
        }

        return $jsonabonosgeneral;

    }

    public static function obtenerAbonosContratos($contratos)
    {

        $abonos = array();
        if ($contratos != null) {
            //Tiene contratos creados
            foreach ($contratos as $contrato) {
                //Recorrido contratos
                $idContrato = $contrato->id;
                $abonosC = DB::select("SELECT IFNULL(a.id, '') as id, IFNULL(a.folio, '') as folio,
                                                IFNULL(a.id_contrato, '') as id_contrato, IFNULL(a.id_usuario, '') as id_usuario,
                                                IFNULL(a.abono, '') as abono, IFNULL(a.adelantos, '0') as adelantos, IFNULL(a.tipoabono, '') as tipoabono,
                                                IFNULL(a.atraso, '0') as atraso, IFNULL(a.metodopago, '') as metodopago, IFNULL(a.corte, '') as corte,
                                                IFNULL(a.id_contratoproducto, '') as id_contratoproducto, IFNULL(a.poliza, '') as poliza, IFNULL(a.coordenadas, '') as coordenadas,
                                                IFNULL(a.created_at, '') as created_at, IFNULL(a.updated_at, '') as updated_at
                                                FROM abonos a WHERE a.id_contrato = '$idContrato'");

                if ($abonosC != null) {
                    //Hay abonos contrato
                    foreach ($abonosC as $abono) {
                        array_push($abonos, $abono);
                    }
                }
            }
        }

        return $abonos;

    }

    public static function generarIdContratoPersonalizado($identificadorFranquicia, $ultimoIdContratoPerzonalizado)
    {

        $arrayRespuesta = array();
        $idContratoPerzonalizado = "";

        $esUnico = false;
        while (!$esUnico) {
            switch (strlen($ultimoIdContratoPerzonalizado)) {
                case 1:
                    $idContratoPerzonalizado = $identificadorFranquicia . "000" . $ultimoIdContratoPerzonalizado;
                    break;
                case 2:
                    $idContratoPerzonalizado = $identificadorFranquicia . "00" . $ultimoIdContratoPerzonalizado;
                    break;
                case 3:
                    $idContratoPerzonalizado = $identificadorFranquicia . "0" . $ultimoIdContratoPerzonalizado;
                    break;
                default:
                    $idContratoPerzonalizado = $identificadorFranquicia . $ultimoIdContratoPerzonalizado;
            }

            $existente = DB::select("select id from contratos where id = '$idContratoPerzonalizado'");
            if (sizeof($existente) == 0) {
                //No existe
                $esUnico = true;
            }else {
                //Existe
                $ultimoIdContratoPerzonalizado = $ultimoIdContratoPerzonalizado + 1;
            }

        }

        array_push($arrayRespuesta, $idContratoPerzonalizado); //Ejemplo: 22001000010001
        array_push($arrayRespuesta, $ultimoIdContratoPerzonalizado); //Ejemplo: 0001

        return $arrayRespuesta;
    }

    public static function obtenerIdentificadorFranquicia($indice)
    {
        $identificadorFranquicia = "";

        //Se creara el identificador de la franquicia
        switch (strlen($indice)) {
            case 1:
                $identificadorFranquicia = "00" . $indice;
                break;
            case 2:
                $identificadorFranquicia = "0" . $indice;
                break;
            default:
                $identificadorFranquicia = $indice;
        }

        return $identificadorFranquicia;
    }

    public static function obtenerIdentificadorUsuario($id)
    {
        $identificadorUsuario = "";

        //Se creara el identificador de la franquicia
        switch (strlen($id)) {
            case 1:
                $identificadorUsuario = "0000" . $id;
                break;
            case 2:
                $identificadorUsuario = "000" . $id;
                break;
            case 3:
                $identificadorUsuario = "00" . $id;
                break;
            case 4:
                $identificadorUsuario = "0" . $id;
                break;
            default:
                $identificadorUsuario = $id;
        }

        return $identificadorUsuario;
    }

    public static function obtenerVentas($now, $nowParse)
    {

        $ventas = array();
        $hoynumero = $now->dayOfWeekIso;    //Obtenemos el dia de la semana actual
        $ventas;

        if($hoynumero == 7){
            //Es domingo - Traer fecha del sabado para obtener top de ventas
            $nowParse = Carbon::now()->subDays(1)->format('Y-m-d');
        }

        //consulta para obtener Asistente con mas ventas en lo que va de la semana
        $ventasAsistente =DB::select("SELECT pv.nombre AS nombre, pv.id_usuario AS id_usuario, pv.rol AS rol, CAST(pv.acumuladas AS SIGNED) as numeroventas,
                                            (SELECT f.ciudad FROM franquicias f WHERE f.id = pv.id_franquicia) as sucursal
                                            FROM polizaventasdias pv
                                            WHERE pv.rol = 13 AND id_franquicia != '00000'
                                            AND STR_TO_DATE(pv.fechapoliza,'%Y-%m-%d') = STR_TO_DATE('$nowParse','%Y-%m-%d')
                                            ORDER BY numeroventas DESC LIMIT 5");

        //Obtener Optometrista con mas ventas asignadas en la semana
        $ventasOpto = DB::select("SELECT pv.nombre AS nombre, pv.id_usuario AS id_usuario, pv.rol AS rol, CAST(pv.acumuladas AS SIGNED) as numeroventas,
                                        (SELECT f.ciudad FROM franquicias f WHERE f.id = pv.id_franquicia) as sucursal
                                        FROM polizaventasdias pv
                                        WHERE pv.rol = 12 AND id_franquicia != '00000'
                                        AND STR_TO_DATE(pv.fechapoliza,'%Y-%m-%d') = STR_TO_DATE('$nowParse','%Y-%m-%d')
                                        ORDER BY numeroventas DESC LIMIT 5");

        $ventas = array_merge($ventasAsistente,$ventasOpto);

        return $ventas; //Retornar arreglo con el dato de obtometrista y Asistente

    }

    private static function eliminarAbonosRepetidosContrato()
    {
        try {

            $abonosrepetidos = DB::select("SELECT id_contrato, id_usuario, id, COUNT(*) AS contador
                                                    FROM abonos
                                                    GROUP BY id_contrato, id_usuario, id HAVING COUNT(*) > 1");

            if($abonosrepetidos != null) {
                //Existen abonos repetidos en tabla abonos

                $array = array(); //Bandera de ids abonos para que se que solo deje uno y se eliminen los demas repetidos
                foreach ($abonosrepetidos as $abonorepetido) {

                    if($abonorepetido->contador > 1) {
                        //Hay mas de uno repetido

                        $abonos = DB::select("SELECT indice, id, id_usuario
                                                            FROM abonos WHERE id_contrato = '" . $abonorepetido->id_contrato . "'
                                                            AND id_usuario = '" . $abonorepetido->id_usuario . "' AND id = '" . $abonorepetido->id . "'");

                        if($abonos != null) {
                            //Existen contratos

                            foreach ($abonos as $abono) {
                                //RECORRIDO DE ABONOS

                                if (!in_array($abonorepetido->id_contrato . $abono->id . $abono->id_usuario, $array)) {
                                    //No existe el id y el id_usuario aun en el array (No se borrara el primer registro)

                                    //Se agrega el id y el id_usuario al array para que este no vuelva a insertarse de nuevo
                                    array_push($array, $abonorepetido->id_contrato . $abono->id . $abono->id_usuario);
                                } else {
                                    //Existe el id y y el id_usuario en el array (Eliminar los demas registros repetidos)
                                    DB::delete("DELETE FROM abonos WHERE indice = '" . $abono->indice
                                        . "' AND id = '" . $abono->id . "' AND id_usuario = '" . $abono->id_usuario . "'");
                                }
                            }

                        }

                    }

                }

                //\Log::info("Termino funcion para eliminarAbonosRepetidosContrato en tabla abonos");

            }else {
                //No existen abonosrepetidos en tabla abonos
                //\Log::info("No existen abonosrepetidos en tabla abonos");
            }

        } catch (\Exception $e) {
            \Log::info("Error: " . $e->getMessage());
        }
    }

    public static function eliminarAbonosContratosTemporalesRepetidosContrato()
    {
        try {

            $abonosrepetidos = DB::select("SELECT id_contrato, id_usuario, id, id_usuariocobrador, COUNT(*) AS contador
                                                    FROM abonoscontratostemporalessincronizacion
                                                    GROUP BY id_contrato, id_usuario, id, id_usuariocobrador HAVING COUNT(*) > 1");

            if($abonosrepetidos != null) {
                //Existen abonos repetidos en tabla abonoscontratostemporalessincronizacion

                $array = array(); //Bandera de ids abonos para que se que solo deje uno y se eliminen los demas repetidos
                foreach ($abonosrepetidos as $abonorepetido) {

                    if($abonorepetido->contador > 1) {
                        //Hay mas de uno repetido

                        $abonos = DB::select("SELECT indice, id, id_usuario, id_usuariocobrador
                                                            FROM abonoscontratostemporalessincronizacion WHERE id_contrato = '" . $abonorepetido->id_contrato . "'
                                                            AND id_usuario = '" . $abonorepetido->id_usuario
                                                            . "' AND id = '" . $abonorepetido->id . "' AND id_usuariocobrador = '" . $abonorepetido->id_usuariocobrador . "'");

                        if($abonos != null) {
                            //Existen contratos

                            foreach ($abonos as $abono) {
                                //RECORRIDO DE ABONOS

                                if (!in_array($abonorepetido->id_contrato . $abono->id . $abono->id_usuario . $abono->id_usuariocobrador, $array)) {
                                    //No existe el id y el id_usuario aun en el array (No se borrara el primer registro)

                                    //Se agrega el id y el id_usuario al array para que este no vuelva a insertarse de nuevo
                                    array_push($array, $abonorepetido->id_contrato . $abono->id . $abono->id_usuario . $abono->id_usuariocobrador);
                                } else {
                                    //Existe el id y y el id_usuario en el array (Eliminar los demas registros repetidos)
                                    DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE indice = '" . $abono->indice
                                        . "' AND id = '" . $abono->id . "' AND id_usuario = '" . $abono->id_usuario . "' AND id_usuariocobrador = '" . $abono->id_usuariocobrador . "'");
                                }
                            }

                        }

                    }

                }

                //\Log::info("Termino funcion para eliminarAbonosContratosTemporalesRepetidosContrato en tabla abonoscontratostemporalessincronizacion");

            }else {
                //No existen abonosrepetidos en tabla abonoscontratostemporalessincronizacion
                //\Log::info("No existen abonosrepetidos en tabla abonoscontratostemporalessincronizacion");
            }

        } catch (\Exception $e) {
            \Log::info("Error: " . $e->getMessage());
        }
    }

    public static function obtenerHistorialesClinicosContratos($contratos)
    {

        $historialesClinicos = array();
        if ($contratos != null) {
            //Tiene contratos creados
            foreach ($contratos as $contrato) {
                //Recorrido contratos
                $idContrato = $contrato->id;
                $historialesC = DB::select("SELECT IFNULL(hc.id, '') as id,
                            IFNULL(hc.id_contrato, '') as id_contrato,
                            IFNULL(hc.edad, '') as edad,
                            IFNULL(hc.fechaentrega, '') as fechaentrega,
                            IFNULL(hc.diagnostico, '') as diagnostico,
                            IFNULL(hc.ocupacion, '') as ocupacion,
                            IFNULL(hc.diabetes, '') as diabetes,
                            IFNULL(hc.hipertension, '') as hipertension,
                            IFNULL(hc.dolor, '') as dolor,
                            IFNULL(hc.ardor, '') as ardor,
                            IFNULL(hc.golpeojos, '') as golpeojos,
                            IFNULL(hc.otroM, '') as otroM,
                            IFNULL(hc.molestiaotro, '') as molestiaotro,
                            IFNULL(hc.ultimoexamen, '') as ultimoexamen,
                            IFNULL(hc.esfericoder, '') as esfericoder,
                            IFNULL(hc.cilindroder, '') as cilindroder,
                            IFNULL(hc.ejeder, '') as ejeder,
                            IFNULL(hc.addder, '') as addder,
                            IFNULL(hc.altder, '') as altder,
                            IFNULL(hc.esfericoizq, '') as esfericoizq,
                            IFNULL(hc.cilindroizq, '') as cilindroizq,
                            IFNULL(hc.ejeizq, '') as ejeizq,
                            IFNULL(hc.addizq, '') as addizq,
                            IFNULL(hc.altizq, '') as altizq,
                            IFNULL(hc.id_producto, '') as id_producto,
                            IFNULL(hc.id_paquete, '') as id_paquete,
                            IFNULL(hc.material, '') as material,
                            IFNULL(hc.materialotro, '') as materialotro,
                            IFNULL(hc.costomaterial, '') as costomaterial,
                            IFNULL(hc.bifocal, '') as bifocal,
                            IFNULL(hc.fotocromatico, '0') as fotocromatico,
                            IFNULL(hc.ar, '0') as ar,
                            IFNULL(hc.tinte, '0') as tinte,
                            IFNULL(hc.blueray, '0') as blueray,
                            IFNULL(hc.otroT, '') as otroT,
                            IFNULL(hc.tratamientootro, '') as tratamientootro,
                            IFNULL(hc.costotratamiento, '') as costotratamiento,
                            IFNULL(hc.observaciones, '') as observaciones,
                            IFNULL(hc.observacionesinterno, '') as observacionesinterno,
                            IFNULL(hc.tipo, '') as tipo,
                            IFNULL(hc.bifocalotro, '') as bifocalotro,
                            IFNULL(hc.costobifocal, '') as costobifocal,
                            IFNULL(hc.embarazada, '') as embarazada,
                            IFNULL(hc.durmioseisochohoras, '') as durmioseisochohoras,
                            IFNULL(hc.actividaddia, '') as actividaddia,
                            IFNULL(hc.problemasojos, '') as problemasojos,
                            IFNULL(hc.policarbonatotipo, '') as policarbonatotipo,
                            IFNULL(hc.id_tratamientocolortinte, '') as id_tratamientocolortinte,
                            IFNULL(hc.estilotinte, '') as estilotinte,
                            IFNULL(hc.polarizado, '') as polarizado,
                            IFNULL(hc.id_tratamientocolorpolarizado, '') as id_tratamientocolorpolarizado,
                            IFNULL(hc.espejo, '') as espejo,
                            IFNULL(hc.id_tratamientocolorespejo, '') as id_tratamientocolorespejo,
                            IFNULL(hc.fotoarmazon, '') as fotoarmazon,
                            IFNULL(hc.created_at, '') as created_at,
                            IFNULL(hc.updated_at, '') as updated_at
                            FROM historialclinico hc
                            WHERE hc.id_contrato = '$idContrato'
                            AND hc.tipo IN (0,1)");

                if ($historialesC != null) {
                    //Hay historiales del contrato
                    foreach ($historialesC as $historial) {
                        array_push($historialesClinicos, $historial);
                    }
                }
            }
        }

        return $historialesClinicos;

    }

    public static function obtenerProductosDeContratoContratos($contratos)
    {

        $productosDeContrato = array();
        if ($contratos != null) {
            //Tiene contratos creados
            foreach ($contratos as $contrato) {
                //Recorrido contratos
                $idContrato = $contrato->id;
                $productosC = DB::select("SELECT IFNULL(cp.id, '') as id,
                            IFNULL(cp.id_contrato, '') as id_contrato,
                            IFNULL(cp.id_producto, '') as id_producto,
                            IFNULL(cp.id_franquicia, '') as id_franquicia,
                            IFNULL(cp.id_usuario, '') as id_usuario,
                            IFNULL(cp.created_at, '') as created_at,
                            IFNULL(cp.updated_at, '') as updated_at,
                            IFNULL(cp.piezas, '') as piezas,
                            IFNULL(cp.total, '') as total
                            FROM contratoproducto cp
                            WHERE cp.id_contrato = '$idContrato'");

                if ($productosC != null) {
                    //Hay productos del contrato
                    foreach ($productosC as $productoContrato) {
                        array_push($productosDeContrato, $productoContrato);
                    }
                }
            }
        }

        return $productosDeContrato;

    }

    public static function obtenerHistorialesSinConversionContratos($contratos)
    {

        $historialessinconversion = array();
        if ($contratos != null) {
            //Tiene contratos creados
            foreach ($contratos as $contrato) {
                //Recorrido contratos
                $idContrato = $contrato->id;
                $historialesSC = DB::select("SELECT IFNULL(hs.id_contrato, '') as id_contrato,
                            IFNULL(hs.id_historial, '') as id_historial,
                            IFNULL(hs.esfericoder, '') as esfericoder,
                            IFNULL(hs.cilindroder, '') as cilindroder,
                            IFNULL(hs.ejeder, '') as ejeder,
                            IFNULL(hs.addder, '') as addder,
                            IFNULL(hs.esfericoizq, '') as esfericoizq,
                            IFNULL(hs.cilindroizq, '') as cilindroizq,
                            IFNULL(hs.ejeizq, '') as ejeizq,
                            IFNULL(hs.addizq, '') as addizq,
                            IFNULL(hs.created_at, '') as created_at
                            FROM historialsinconversion hs
                            WHERE hs.id_contrato = '$idContrato'");

                if ($historialesSC != null) {
                    //Hay historiales sin conversion del contrato
                    foreach ($historialesSC as $historial) {
                        array_push($historialessinconversion, $historial);
                    }
                }
            }
        }

        return $historialessinconversion;

    }

    public static function validacionAbonosArchivo($datosarchivo, $id_usuario)
    {
        $todosLosDatos = json_decode($datosarchivo, true);//Obtenemos todos los datos

        $jsonAbonosArchivo = null;
        if (!empty($todosLosDatos[0]['abonosarchivo'])) {
            //Json abonosarchivo es diferente a vacio
            $jsonAbonosArchivo = self::obtenerJsonDecodificado($todosLosDatos[0]['abonosarchivo']);//Obtenemos los abonos archivo
        }

        if (!empty($jsonAbonosArchivo)) {
            //jsonAbonosArchivo es diferente a vacio

            foreach ($jsonAbonosArchivo as $abonoArchivo) {
                //Recorrido de jsonAbonosArchivo

                $idAbono = $abonoArchivo['ID_ABONO'];
                $idContrato = $abonoArchivo['ID_CONTRATO'];

                try {

                    $contrato = DB::select("SELECT id_franquicia, id_zona FROM contratos WHERE id = '$idContrato'");

                    if($contrato != null) {
                        //Existe contrato

                        $idFranquicia = $contrato[0]->id_franquicia;

                        if ($abonoArchivo['TITULO'] == 'Agregado') {
                            //Se agrego abono

                            $existeAbonoEnTablaAbonos = DB::select("SELECT id FROM abonos
                                                WHERE id = '$idAbono'
                                                    AND id_contrato = '$idContrato'");

                            if ($existeAbonoEnTablaAbonos == null) {
                                //No existe el abono en tabla de abonos

                                $existeAbonoEnTablaAbonosEliminados = DB::select("SELECT id FROM abonoseliminados
                                                WHERE id = '$idAbono'
                                                    AND id_contrato = '$idContrato'");

                                if ($existeAbonoEnTablaAbonosEliminados == null) {
                                    //No existe el abono en tabla de abonoseliminados

                                    $idContratoProducto = ($abonoArchivo['ID_CONTRATOPRODUCTO'] != "")? $abonoArchivo['ID_CONTRATOPRODUCTO'] : null;
                                    $tipoAbono = $abonoArchivo['TIPOABONO'];
                                    //Validar si es abono diferente de producto o si es de producto y cuenta con idcontratoproducto
                                    if($tipoAbono != 7 || ($tipoAbono == 7 && $idContratoProducto != null)){
                                        $contratosGlobal = new contratosGlobal;

                                        //Insertar abono en la tabla abonos
                                        DB::table("abonos")->insert([
                                            "id" => $idAbono,
                                            "folio" => $abonoArchivo['FOLIO'],
                                            "id_franquicia" => $idFranquicia,
                                            "id_contrato" => $idContrato,
                                            "id_usuario" => $id_usuario,
                                            "abono" => $abonoArchivo['ABONO'],
                                            "adelantos" => $abonoArchivo['ADELANTOS'],
                                            "tipoabono" => $tipoAbono,
                                            "atraso" => $abonoArchivo['ATRASO'],
                                            "metodopago" => $abonoArchivo['METODOPAGO'],
                                            "corte" => 0,
                                            "poliza" => null,
                                            "id_corte" => null,
                                            "id_zona" => $contrato[0]->id_zona,
                                            "fecharegistro" => $abonoArchivo['CREATED_AT'],
                                            "coordenadas" => $abonoArchivo['COORDENADAS'],
                                            "id_contratoproducto" => $idContratoProducto,
                                            "created_at" => $abonoArchivo['CREATED_AT'],
                                            "updated_at" => $abonoArchivo['CREATED_AT']
                                        ]);

                                        //Agregar movimiento de abono insertado desde archivo
                                        DB::table('historialcontrato')->insert([
                                            'id' => self::generarIdAlfanumerico('historialcontrato', '5'),
                                            'id_usuarioC' => $id_usuario,
                                            'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                            'cambios' => "Se agrego abono: '" . $abonoArchivo['ABONO'] . "' con folio: '" . $abonoArchivo['FOLIO'] . "' (Archivo)",
                                            'tipomensaje' => '0']);

                                        $usuario = DB::select("SELECT rol_id FROM users WHERE id = '$id_usuario'");

                                        if ($usuario != null) {
                                            //Existe usuario

                                            if ($usuario[0]->rol_id == 4) {
                                                //Cobrador

                                                if ($abonoArchivo['TIPOABONO'] == '2') {
                                                    //Abono de entrega de producto

                                                    //Actualizar fecha entrega y entrega producto
                                                    DB::table("contratos")->where("id", "=", $idContrato)->where("id_franquicia", "=", $idFranquicia)->update([
                                                        'fechaentrega' => Carbon::now(),
                                                        'entregaproducto' => 1
                                                    ]);

                                                    //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                                                    $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $id_usuario);

                                                    //Insertar o actualizar contrato tabla contratoslistatemporales
                                                    $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);
                                                }

                                                $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($contrato[0]->id_zona);

                                                if ($cobradoresAsignadosAZona != null) {
                                                    //Existen cobradores asignados a la zona
                                                    foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                                        //Recorrido cobradores

                                                        $existeAbonoEnTablaAbonosContratosTemporalesSinconizacion = DB::select("SELECT id FROM abonoscontratostemporalessincronizacion
                                                        WHERE id = '$idAbono'
                                                        AND id_contrato = '$idContrato'
                                                        AND id_usuariocobrador = '" . $cobradorAsignadoAZona->id . "'");

                                                        if ($existeAbonoEnTablaAbonosContratosTemporalesSinconizacion == null) {
                                                            //No existe el abono en tabla de abonoscontratostemporalessincronizacion

                                                            //Insertar abono en la tabla abonoscontratostemporalessincronizacion
                                                            DB::table("abonoscontratostemporalessincronizacion")->insert([
                                                                "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                                                "id" => $idAbono,
                                                                "folio" => $abonoArchivo['FOLIO'],
                                                                "id_contrato" => $idContrato,
                                                                "id_usuario" => $id_usuario,
                                                                "abono" => $abonoArchivo['ABONO'],
                                                                "adelantos" => $abonoArchivo['ADELANTOS'],
                                                                "tipoabono" => $abonoArchivo['TIPOABONO'],
                                                                "atraso" => $abonoArchivo['ATRASO'],
                                                                "metodopago" => $abonoArchivo['METODOPAGO'],
                                                                "corte" => 0,
                                                                "coordenadas" => $abonoArchivo['COORDENADAS'],
                                                                "created_at" => $abonoArchivo['CREATED_AT'],
                                                                "updated_at" => $abonoArchivo['CREATED_AT']
                                                            ]);

                                                        }

                                                    }
                                                }

                                            }

                                        }
                                    }

                                }

                            }

                        }else {
                            //Se elimino abono

                            $usuario = DB::select("SELECT rol_id FROM users WHERE id = '$id_usuario'");

                            if ($usuario != null) {
                                //Existe usuario

                                if ($usuario[0]->rol_id != 4) {
                                    //Asistente/Optometrista

                                    //Eliminar registro de tabla abonos
                                    DB::delete("DELETE FROM abonos
                                                    WHERE id = '" . $idAbono . "'
                                                    AND id_contrato = '" . $idContrato . "'");

                                    $existeAbonoEliminado = DB::select("SELECT * FROM abonoseliminados
                                                WHERE id = '" . $idAbono . "'
                                                    AND id_contrato = '" . $idContrato . "'");

                                    if ($existeAbonoEliminado == null) {
                                        //No existe registro en tabla abonoseliminados

                                        //Insertar en tabla abonoseliminados
                                        DB::table("abonoseliminados")->insert([
                                            "id" => $idAbono,
                                            "folio" => null,
                                            "id_franquicia" => $idFranquicia,
                                            "id_contrato" => $idContrato,
                                            "id_usuario" => $id_usuario,
                                            "abono" => $abonoArchivo['ABONO'],
                                            "adelantos" => $abonoArchivo['ADELANTOS'],
                                            "tipoabono" => $abonoArchivo['TIPOABONO'],
                                            "atraso" => $abonoArchivo['ATRASO'],
                                            "metodopago" => $abonoArchivo['METODOPAGO'],
                                            "corte" => 2,
                                            "poliza" => null,
                                            "id_corte" => null,
                                            "created_at" => $abonoArchivo['CREATED_AT'],
                                            "updated_at" => $abonoArchivo['CREATED_AT']
                                        ]);

                                    }

                                }

                            }

                        }

                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonAbonosArchivo: " . $idContrato . "\n" . $e);
                    continue;
                }

            }

        }//ABONOS ARCHIVO

    }

    public static function validacionHistorialesClinicosContratos($datosarchivohistorialesclinicos)
    {
        $todosLosDatos = json_decode($datosarchivohistorialesclinicos, true);//Obtenemos todos los datos

        $jsonHistorialesClinicosArchivo = null;
        if (!empty($todosLosDatos[0]['historialesclinicosarchivo'])) {
            //Json historialesclinicos es diferente a vacio
            $jsonHistorialesClinicosArchivo = self::obtenerJsonDecodificado($todosLosDatos[0]['historialesclinicosarchivo']);//Obtenemos los abonos archivo
        }

        if (!empty($jsonHistorialesClinicosArchivo)) {
            //$jsonHistorialesClinicosArchivo es diferente a vacio


            foreach ($jsonHistorialesClinicosArchivo as $historialArchivo) {
                //Recorrido de jsonAbonosArchivo

                $idHistorial = $historialArchivo['ID'];
                $idContrato = $historialArchivo['ID_CONTRATO'];

                try {

                    $contrato = DB::select("SELECT id FROM contratos WHERE id = '$idContrato'");
                    if($contrato != null){
                        //Si existe el contrato
                        $existeHistorialClinico = DB::select("SELECT hc.id FROM historialclinico hc WHERE hc.id = '$idHistorial' AND hc.id_contrato = '$idContrato'");
                        if($existeHistorialClinico == null){
                            //No existe historial clinico en tabla - insertar historial clinico

                            $EDAD = self::validacionDeNulo($historialArchivo['EDAD']);
                            $FECHAENTREGA = self::validacionDeNulo($historialArchivo['FECHAENTREGA']);
                            $DIAGNOSTICO = self::validacionDeNulo($historialArchivo['DIAGNOSTICO']);
                            $OCUPACION = self::validacionDeNulo($historialArchivo['OCUPACION']);
                            $DIABETES = self::validacionDeNulo($historialArchivo['DIABETES']);
                            $HIPERTENSION = self::validacionDeNulo($historialArchivo['HIPERTENSION']);
                            $DOLOR = self::validacionDeNulo($historialArchivo['DOLOR']);
                            $ARDOR = self::validacionDeNulo($historialArchivo['ARDOR']);
                            $GOLPEOJOS = self::validacionDeNulo($historialArchivo['GOLPEOJOS']);
                            $OTROM = self::validacionDeNulo($historialArchivo['OTROM']);

                            $MOLESTIAOTRO = self::validacionDeNulo($historialArchivo['MOLESTIAOTRO']);
                            $ULTIMOEXAMEN = self::validacionDeNulo($historialArchivo['ULTIMOEXAMEN']);
                            $ESFERICODER = self::validacionDeNulo($historialArchivo['ESFERICODER']);
                            $CILINDRODER = self::validacionDeNulo($historialArchivo['CILINDRODER']);
                            $EJEDER = self::validacionDeNulo($historialArchivo['EJEDER']);
                            $ADDDER = self::validacionDeNulo($historialArchivo['ADDDER']);
                            $ALTDER = self::validacionDeNulo($historialArchivo['ALTDER']);
                            $ESFERICOIZQ = self::validacionDeNulo($historialArchivo['ESFERICOIZQ']);
                            $CILINDROIZQ = self::validacionDeNulo($historialArchivo['CILINDROIZQ']);
                            $EJEIZQ = self::validacionDeNulo($historialArchivo['EJEIZQ']);
                            $ADDIZQ = self::validacionDeNulo($historialArchivo['ADDIZQ']);
                            $ALTIZQ = self::validacionDeNulo($historialArchivo['ALTIZQ']);
                            $MATERIALOTRO = self::validacionDeNulo($historialArchivo['MATERIALOTRO']);
                            $COSTOMATERIAL = self::validacionDeNulo($historialArchivo['COSTOMATERIAL']);
                            $TRATAMIENTOOTRO = self::validacionDeNulo($historialArchivo['TRATAMIENTOOTRO']);
                            $COSTOTRATAMIENTO = self::validacionDeNulo($historialArchivo['COSTOTRATAMIENTO']);
                            $OBSERVACIONES = self::validacionDeNulo($historialArchivo['OBSERVACIONES']);
                            $OBSERVACIONESINTERNO = self::validacionDeNulo($historialArchivo['OBSERVACIONESINTERNO']);
                            $BIFOCALOTRO = self::validacionDeNulo($historialArchivo['BIFOCALOTRO']);
                            $COSTOBIFOCAL = self::validacionDeNulo($historialArchivo['COSTOBIFOCAL']);
                            $EMBARAZADA = self::validacionDeNulo($historialArchivo['EMBARAZADA']);
                            $DURMIOSEISOCHOHORAS = self::validacionDeNulo($historialArchivo['DURMIOSEISOCHOHORAS']);
                            $ACTIVIDADDIA = self::validacionDeNulo($historialArchivo['ACTIVIDADDIA']);
                            $PROBLEMASOJOS = self::validacionDeNulo($historialArchivo['PROBLEMASOJOS']);
                            $POLICARBONATOTIPO = self::validacionDeNulo($historialArchivo['POLICARBONATOTIPO']);
                            $ID_TRATAMIENTOCOLORTINTE = self::validacionDeNulo($historialArchivo['ID_TRATAMIENTOCOLORTINTE']);
                            $ESTILOTINTE = self::validacionDeNulo($historialArchivo['ESTILOTINTE']);
                            $POLARIZADO = self::validacionDeNulo($historialArchivo['POLARIZADO']);
                            $ID_TRATAMIENTOCOLORPOLARIZADO = self::validacionDeNulo($historialArchivo['ID_TRATAMIENTOCOLORPOLARIZADO']);
                            $ESPEJO = self::validacionDeNulo($historialArchivo['ESPEJO']);
                            $ID_TRATAMIENTOCOLORESPEJO = self::validacionDeNulo($historialArchivo['ID_TRATAMIENTOCOLORESPEJO']);

                            DB::table("historialclinico")->insert([
                                "id" => $idHistorial,
                                "id_contrato" => $idContrato,
                                "edad" => $EDAD,
                                "fechaentrega" => $FECHAENTREGA,
                                "diagnostico" => $DIAGNOSTICO,
                                "ocupacion" => $OCUPACION,
                                "diabetes" => $DIABETES,
                                "hipertension" => $HIPERTENSION,
                                "dolor" => $DOLOR,
                                "ardor" => $ARDOR,
                                "golpeojos" => $GOLPEOJOS,
                                "otroM" => $OTROM,
                                "molestiaotro" => $MOLESTIAOTRO,
                                "ultimoexamen" => $ULTIMOEXAMEN,
                                "esfericoder" => $ESFERICODER,
                                "cilindroder" => $CILINDRODER,
                                "ejeder" => $EJEDER,
                                "addder" => $ADDDER,
                                "altder" => $ALTDER,
                                "esfericoizq" => $ESFERICOIZQ,
                                "cilindroizq" => $CILINDROIZQ,
                                "ejeizq" => $EJEIZQ,
                                "addizq" => $ADDIZQ,
                                "altizq" => $ALTIZQ,
                                "id_producto" => $historialArchivo['ID_PRODUCTO'],
                                "id_paquete" => $historialArchivo['ID_PAQUETE'],
                                "material" => $historialArchivo['MATERIAL'],
                                "materialotro" => $MATERIALOTRO,
                                "costomaterial" => $COSTOMATERIAL,
                                "bifocal" => $historialArchivo['BIFOCAL'],
                                "fotocromatico" => $historialArchivo['FOTOCROMATICO'],
                                "ar" => $historialArchivo['AR'],
                                "tinte" => $historialArchivo['TINTE'],
                                "blueray" => $historialArchivo['BLUERAY'],
                                "otroT" => $historialArchivo['OTROT'],
                                "tratamientootro" => $TRATAMIENTOOTRO,
                                "costotratamiento" => $COSTOTRATAMIENTO,
                                "observaciones" => $OBSERVACIONES,
                                "observacionesinterno" => $OBSERVACIONESINTERNO,
                                "tipo" => $historialArchivo['TIPO'],
                                "bifocalotro" => $BIFOCALOTRO,
                                "costobifocal" => $COSTOBIFOCAL,
                                "embarazada" => $EMBARAZADA,
                                "durmioseisochohoras" => $DURMIOSEISOCHOHORAS,
                                "actividaddia" => $ACTIVIDADDIA,
                                "problemasojos" => $PROBLEMASOJOS,
                                "policarbonatotipo" => $POLICARBONATOTIPO,
                                "id_tratamientocolortinte" => $ID_TRATAMIENTOCOLORTINTE,
                                "estilotinte" => $ESTILOTINTE,
                                "polarizado" => $POLARIZADO,
                                "id_tratamientocolorpolarizado" => $ID_TRATAMIENTOCOLORPOLARIZADO,
                                "espejo" => $ESPEJO,
                                "id_tratamientocolorespejo" => $ID_TRATAMIENTOCOLORESPEJO,
                                "created_at" => $historialArchivo['CREATED_AT'],
                                "updated_at" => $historialArchivo['UPDATED_AT']
                            ]);

                            $fechaActual = Carbon::now()->format('Y-m-d H:i:s');
                            DB::update("UPDATE producto
                                    SET piezas = piezas - 1,
                                    updated_at = '$fechaActual'
                                    WHERE id = '" . $historialArchivo['ID_PRODUCTO'] . "'");

                            //Actualizar datos historiales clinicos a mayusculas y quitar acentos
                            $contratosGlobal = new contratosGlobal();
                            $contratosGlobal::actualizarContratoHistorialesClinicosMayusculasAcentos($historialArchivo['ID_CONTRATO'], 1);

                        }
                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonHistorialesClinicosArchivo: " . $idContrato . "\n" . $e);
                    continue;
                }
            }

        }
    }

    public static function validacionBanderaColonias($banderaColoniasMovil, $idFranquicia)
    {
        //Arreglo de colonias
        $colonias = null;

        if($banderaColoniasMovil != null && $banderaColoniasMovil != ""){
            //Bandera colonias enviada desde app movil es diferente de null o vacio
            $banderaActualColonias = DB::select("SELECT c.bandera FROM colonias c WHERE c.id_franquicia = '$idFranquicia' LIMIT 1");

            if($banderaActualColonias != null){
                //Si existe un registro en la BD

                //Verificar si existe un cambio en la lista de colonias de la sucursal
                if($banderaActualColonias[0]->bandera != $banderaColoniasMovil){
                    //Se actualizo lista de colonias - consultar colonias
                    $colonias = DB::select("SELECT IFNULL(indice, '') as indice,
                                          IFNULL(id_zona, '') as id_zona,
                                          IFNULL(colonia, '') as colonia,
                                          IFNULL(localidad, '') as localidad,
                                          IFNULL(bandera, '') as bandera
                                          FROM colonias
                                          WHERE id_franquicia = '$idFranquicia'");
                }
            }
        }else{
            //Primera vez a insertar colonias en app movil
            $colonias = DB::select("SELECT IFNULL(indice, '') as indice,
                                          IFNULL(id_zona, '') as id_zona,
                                          IFNULL(colonia, '') as colonia,
                                          IFNULL(localidad, '') as localidad,
                                          IFNULL(bandera, '') as bandera
                                          FROM colonias
                                          WHERE id_franquicia = '$idFranquicia'");
        }

        return $colonias;
    }

    public static function insertarcontratoslistanegra($datoscontratoslistanegra){

        if (!empty($datoscontratoslistanegra)) {
            //$datoscontratoslistanegra es diferente a vacio

            foreach ($datoscontratoslistanegra as $contratolistanegra) {

                $idContrato = $contratolistanegra['ID_CONTRATO'];

                try {

                    $contrato = DB::select("SELECT id FROM contratos WHERE id = '$idContrato'");
                    if($contrato != null){
                        //Si existe el contrato

                        $existeContratoListaNegra = DB::select("SELECT * FROM contratoslistanegra WHERE id_contrato = '$idContrato' AND estado != '2'");
                        if($existeContratoListaNegra == null){
                            //No existe el contrato con una solicitud de lista negra pendiente por revisar - Ingresar nuevo registro

                            DB::table("contratoslistanegra")->insert([
                                "id_contrato" => $idContrato,
                                "descripcion" => $contratolistanegra['DESCRIPCION'],
                                "estado" => $contratolistanegra['ESTADO'],
                                "created_at" => Carbon::now()
                            ]);
                        }

                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : datoscontratoslistanegra: " . $idContrato . "\n" . $e);
                    continue;
                }
            }

        }
    }

    public static function insertarActualizarEliminarNotasCobranza($jsonNotasCobranza, $id_usuario){

        if (!empty($jsonNotasCobranza)) {
            //$jsonNotasCobranza es diferente a vacio

            foreach ($jsonNotasCobranza as $notacobranza) {
                //Recorrido de $jsonNotasCobranza

                try {

                    $existeNota = DB::select("SELECT * FROM notascobranza WHERE  id_usuario = '$id_usuario' AND id = '" . $notacobranza['ID'] . "'");
                    if ($existeNota == null) {
                        //No existe nota en la BD
                        if($notacobranza['BANDERAELIMINADO'] != 1){
                            //No existe y no fue elimiada de la app movil
                            DB::table("notascobranza")->insert([
                                "id" => $notacobranza['ID'],
                                "id_usuario" => $id_usuario,
                                "nota" => $notacobranza['NOTA'],
                                "created_at" => $notacobranza['CREATED_AT']
                            ]);
                        }
                    }else{
                        //Existe la nota en la BD - Validar que se hizo con la nota
                        if($notacobranza['BANDERAELIMINADO'] == 1){
                            //Nota eliminada - Eliminar
                            DB::delete("DELETE FROM notascobranza WHERE indice = '" . $existeNota[0]->indice . "'");
                        }else{
                            //Se edito la nota - Actualizar
                            DB::table("notascobranza")->where("id_usuario", "=", $id_usuario)->where("indice", "=", $existeNota[0]->indice)->update([
                                "nota" => $notacobranza['NOTA'],
                                "updated_at" => Carbon::now()
                            ]);
                        }
                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonNotasCobranza: " . $notacobranza['ID_CONTRATO'] . "\n" . $e);
                    continue;
                }

            }

        }

    }

    public static function actualizarRegistroSalidaUsuariosVentas($idFranquicia, $idUsuario, $jsonAsistencia){
        if (!empty($jsonAsistencia)) {
            //$jsonAsistencia es diferente a vacio

            foreach ($jsonAsistencia as $asistencia) {
                //Recorrido de $jsonAsistencia

                try {

                    $existeUsuarioAsistencia = DB::select("SELECT * FROM asistencia WHERE  id_usuario = '" . $asistencia['ID_USUARIO'] . "' ORDER BY created_at DESC LIMIT 1");
                    if ($existeUsuarioAsistencia != null) {
                        if($existeUsuarioAsistencia[0]->registrosalida == null){
                            //No tiene registrada la salida el usuario - Actualizar registro
                            $idPoliza = $existeUsuarioAsistencia[0]->id_poliza;

                            //Actualizar registro de salida
                            DB::table("asistencia")->where("id_usuario", "=", $asistencia['ID_USUARIO'])->where('id_poliza', '=', $idPoliza)->update([
                                "registrosalida" => Carbon::now(), "updated_at" => Carbon::now()
                            ]);

                            $existeUsuarioLogeado = DB::select("SELECT name FROM users WHERE id = '$idUsuario'");
                            $existeUsuarioAsistencia = DB::select("SELECT name FROM users WHERE id = '" . $asistencia['ID_USUARIO'] . "'");
                            $existeUsuarioLogeado = $existeUsuarioLogeado == null ? "" : $existeUsuarioLogeado[0]->name;
                            $existeUsuarioAsistencia = $existeUsuarioAsistencia == null ? "" : $existeUsuarioAsistencia[0]->name;

                            //Guardar movimiento en historial poliza
                            DB::table('historialpoliza')->insert([
                                'id_usuarioC' => $idUsuario, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                                'cambios' => "$existeUsuarioLogeado registró hora de salida a $existeUsuarioAsistencia desde movil."
                            ]);

                            //Guardar movimiento en historial sucursal
                            DB::table('historialsucursal')->insert([
                                'id_usuarioC' => $idUsuario, 'id_franquicia' => $idFranquicia,
                                'tipomensaje' => "0", 'created_at' => Carbon::now(),
                                'cambios' => "$existeUsuarioLogeado registró hora de salida a $existeUsuarioAsistencia desde movil", 'seccion' => "0"
                            ]);
                        }
                    }

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : jsonAsistencia: " . $asistencia['ID_USUARIO'] . "\n" . $e);
                    continue;
                }

            }

        }

    }

    public static function insertarNuevaCitaPaciente($idFranquicia, $idUsuario, $jsonAgendaCita){
        if (!empty($jsonAgendaCita)) {
            //$jsonAgendaCita es diferente a vacio

            foreach ($jsonAgendaCita as $cita) {
                //Recorrido de $jsonAgendaCita

                try {

                    $contratosGlobal = new contratosGlobal();
                    $referenciaCita = $contratosGlobal::generarReferenciaCita($idFranquicia);

                    //Insertar nuevo registro de cita
                    DB::table('agendacitas')->insert([
                        'id_franquicia' => $idFranquicia, 'nombre' => $cita['NOMBRE'], 'email' => $cita['EMAIL'],'telefono' => $cita['TELEFONO'], 'observaciones' => $cita['OBSERVACIONES'],
                        'fechacitaagendada' => $cita['FECHACITAAGENDADA'], 'horacitaagendada' => $cita['HORACITAAGENDADA'], 'estadocita' => '0',
                        'localidad' => $cita['LOCALIDAD'], 'colonia' => $cita['COLONIA'], 'domicilio' => $cita['DOMICILIO'], 'numero' => $cita['NUMERO'],
                        'entrecalles' => $cita['ENTRECALLES'], 'lugarcita' => $cita['LUGARCITA'], 'tipocita' => $cita['TIPOCITA'], 'otrotipocita' => $cita['OTROTIPOCITA'],
                        'referencia' => $referenciaCita, 'created_at' => Carbon::now()
                    ]);

                    //Guardar movimiento en historial sucursal
                    DB::table('historialsucursal')->insert([
                        'id_usuarioC' => $idUsuario,
                        'id_franquicia' => $idFranquicia, 'tipomensaje' => '11',
                        'created_at' => Carbon::now(),
                        'cambios' => "Agendó cita con nombre de: '".$cita['NOMBRE']."' para dia: '".$cita['FECHACITAAGENDADA']."' horario: '".$cita['HORACITAAGENDADA']."'",
                        'seccion' => '2'
                    ]);

                } catch (\Exception $e) {
                    \Log::info("Error: globalesServicioWeb : $jsonAgendaCita: " . $cita['NOMBRE'] . "\n" . $e);
                    continue;
                }

            }

        }

    }
}//clase
