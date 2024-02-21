<?php

namespace App\Clases;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class calculofechaspago
{
    public static function obtenerCalculoSiguienteFechas($pago, $fechaentrega, $fechacobrofin, $diapago, $pasoFechaCobroFin, $diaActual, $adelantos)
    {

        $arraySiguientesFechas = array();

        $fechaCobroIniRespuesta = null;
        $fechaCobroFinRespuesta = null;
        $diaSeleccionadoRespuesta = null;

        //CALCULO FECHACOBROINI
        if(!$pasoFechaCobroFin) {
            //No ha pasado la fechacobrofin

            $fechaentrega = ($fechaentrega == null ? Carbon::parse($diaActual)->subDay() : $fechaentrega); //(Si es null fechaentrega se tomara el diaactual menos un dia)
            if ($fechacobrofin != null) {
                //Ya tiene fechacobrofin
                $fechaentrega = $fechacobrofin;
            }

            $fechaentrega = Carbon::parse($fechaentrega)->format('Y-m-d'); //Dar formato a fechaentrega o fechacobrofin 2022-08-12 para cuando se de formato al final aparezca como 00:00:00

            switch ($pago) {
                case 1:
                    //Semanal
                    $hoyNumero = Carbon::parse($fechaentrega)->dayOfWeekIso;
                    $fechaCobroIniRespuesta = Carbon::parse($fechaentrega)->addWeek(); //Se suma una semana
                    $fechaCobroIniRespuesta = Carbon::parse($fechaCobroIniRespuesta)->subDays($hoyNumero - 1); //Se obtiene el lunes siguiente
                    break;
                case 2:
                    //Quincenal
                    //Obtener los digitos de fechaentrega o fechacobrofin 1, 2, 3, 4 etc
                    $diaFechaEntrega = Carbon::parse($fechaentrega)->format('d');
                    if ($diaFechaEntrega > 14) {
                        //Se entrego del dia 14 en adelante
                        $fechaCobroIniRespuesta = Carbon::parse($fechaentrega)->firstOfMonth()->addMonth(); //Se pondra el primer dia del mes actual y se sumara un mes
                    } else {
                        //Se entrego antes del dia 14
                        $fechaCobroIniRespuesta = Carbon::parse($fechaentrega)->firstOfMonth()->addDays(14); //Se obtendra el 15 del mes actual
                    }
                    break;
                case 4:
                    //Mensual
                    $fechaCobroIniRespuesta = Carbon::parse($fechaentrega)->firstOfMonth()->addMonth(); //Se pondra el primer dia del mes actual y se sumara un mes
                    break;
            }

        }else {
            //Ya paso la fechacobrofin
            $fechaCobroIniRespuesta = Carbon::parse($diaActual); //Se toma el dia actual como fechacobroini
        }

        //CALCULO FECHACOBROINI EN CASO DE QUE SE ADELANTO UNO O MAS ABONOS
        if($adelantos > 0) {
            //Mas de un adelanto

            for ($i = $adelantos; $i > 0; $i--) {
                //Recorrido de adelantos
                switch ($pago) {
                    case 1:
                        //Semanal
                        $fechaCobroIniRespuesta = Carbon::parse($fechaCobroIniRespuesta)->addWeek(); //Se obtiene el sabado siguiente
                        break;
                    case 2:
                        //Quincenal
                        $diaFechaEntrega = Carbon::parse($fechaCobroIniRespuesta)->format('d'); //Obtener los digitos del dia fechacobroini 1, 2, 3, 4 etc
                        if ($diaFechaEntrega > 14) {
                            //Se entrego del dia 14 en adelante
                            $fechaCobroIniRespuesta = Carbon::parse($fechaCobroIniRespuesta)->firstOfMonth()->addMonth(); //Se pondra el primer dia del mes y se sumara un mes
                        } else {
                            //Se entrego antes del dia 14
                            $fechaCobroIniRespuesta = Carbon::parse($fechaCobroIniRespuesta)->firstOfMonth()->addDays(14); //Se obtendra el 15 del mes actual
                        }
                        break;
                    case 4:
                        //Mensual
                        $fechaCobroIniRespuesta = Carbon::parse($fechaCobroIniRespuesta)->firstOfMonth()->addMonth(); //Se pondra el primer dia del mes y se sumara un mes
                        break;
                }
            }

        }

        //CALCULO FECHACOBROFIN
        switch ($pago) {
            case 1:
                //Semanal
                $fechaCobroFinRespuesta = Carbon::parse($fechaCobroIniRespuesta)->addDays(6); //Se obtiene el viernes siguiente
                break;
            case 2:
                //Quincenal
                $diaFechaCobroIni = Carbon::parse($fechaCobroIniRespuesta)->format('d'); //Obtener los digitos del dia fechacobroini 1, 2, 3, 4 etc
                if ($diaFechaCobroIni > 14) {
                    //diaFechaCobroIni es mayor a 14
                    $fechaCobroFinRespuesta = Carbon::parse($fechaCobroIniRespuesta)->endOfMonth()->format('Y-m-d'); //Se obtendra el ultimo dia del mes
                    $fechaCobroFinRespuesta = Carbon::parse($fechaCobroFinRespuesta); //Parsear fechacobrofin para obtener Ejem. 2022-08-31 00:00:00 por que si no me traeria 2022-08-31 23:59:59
                } else {
                    //diaFechaCobroIni es menor a 14
                    $fechaCobroFinRespuesta = Carbon::parse($fechaCobroIniRespuesta)->firstOfMonth()->addDays(13); //Se obtendra el 14 del mes
                }
                break;
            case 4:
                //Mensual
                $fechaCobroFinRespuesta = Carbon::parse($fechaCobroIniRespuesta)->endOfMonth()->format('Y-m-d'); //Se obtendra el ultimo dia del mes
                $fechaCobroFinRespuesta = Carbon::parse($fechaCobroFinRespuesta); //Parsear fechacobrofin para obtener Ejem. 2022-08-31 00:00:00 por que si no me traeria 2022-08-31 23:59:59
                break;
        }

        //OBTENER DIASELECCIONADO
        if(strlen($diapago) > 0) {
            //Se tiene un dia de pago
            $diaSeleccionadoRespuesta = self::obtenerDiaSeleccionado($diapago, $fechaCobroIniRespuesta, $fechaCobroFinRespuesta);
        }

        array_push($arraySiguientesFechas, $fechaCobroIniRespuesta);
        array_push($arraySiguientesFechas, $fechaCobroFinRespuesta);
        array_push($arraySiguientesFechas, $diaSeleccionadoRespuesta);

        return $arraySiguientesFechas;

    }

    public static function obtenerDiaSeleccionado($diapago, $fechaCobroIniRespuesta, $fechaCobroFinRespuesta)
    {
        $diaSeleccionadoRespuesta = null;

        $diasCobro = CarbonPeriod::create($fechaCobroIniRespuesta, $fechaCobroFinRespuesta); //Obtenemos los dias entre fechacobroini y fechacobrofin
        foreach ($diasCobro as $diaCobro) {
            //Recorrido de dias entre fechacobroini y fechacobrofin
            if (self::diaStringAdiaNumero($diapago) === self::diaStringAdiaNumero($diaCobro->format('l'))) {
                //Dia recorrido es igual a diapago (Viernes = Viernes)
                $diaSeleccionadoRespuesta = $diaCobro; //Se asigna a diaseleccionado el dia del recorrido
                break;
            }
        }

        return $diaSeleccionadoRespuesta;
    }

    private static function diaStringAdiaNumero($diaString)
    {
        switch ($diaString) {
            case "Monday":
                return 0;
            case "Tuesday":
                return 1;
            case "Wednesday":
                return 2;
            case "Thursday":
                return 3;
            case "Friday":
                return 4;
            case "Saturday":
                return 5;
            case "Sunday":
                return 6;
        }
    }

    public static function obtenerDia($dia, $diaActualNumero, $diaSeleccionado)
    {
        //Dia seleccionado
        // 0-> SabadoAnterior
        // 1-> Lunes
        // 2-> SabadoSiguiente

        switch ($diaActualNumero) {
            case 1://Es Lunes
                if ($diaSeleccionado == 0) {
                    return Carbon::parse($dia)->subDays(2)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                }
                return Carbon::parse($dia)->addDays(5)->format('Y-m-d'); //Obtengo la fecha del dia sabado siguiente
            case 2://Es Martes
                if ($diaSeleccionado == 0) {
                    return Carbon::parse($dia)->subDays(3)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return Carbon::parse($dia)->subDays(1)->format('Y-m-d'); //Obtengo la fecha del dia lunes
                }
                return Carbon::parse($dia)->addDays(4)->format('Y-m-d'); //Obtengo la fecha del dia sabado siguiente
            case 3://Es Miercoles
                if ($diaSeleccionado == 0) {
                    return Carbon::parse($dia)->subDays(4)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return Carbon::parse($dia)->subDays(2)->format('Y-m-d'); //Obtengo la fecha del dia lunes
                }
                return Carbon::parse($dia)->addDays(3)->format('Y-m-d'); //Obtengo la fecha del dia sabado siguiente
            case 4://Es Jueves
                if ($diaSeleccionado == 0) {
                    return Carbon::parse($dia)->subDays(5)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return Carbon::parse($dia)->subDays(3)->format('Y-m-d'); //Obtengo la fecha del dia lunes
                }
                return Carbon::parse($dia)->addDays(2)->format('Y-m-d'); //Obtengo la fecha del dia sabado siguiente
            case 5://Es Viernes
                if ($diaSeleccionado == 0) {
                    return Carbon::parse($dia)->subDays(6)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return Carbon::parse($dia)->subDays(4)->format('Y-m-d'); //Obtengo la fecha del dia lunes
                }
                return Carbon::parse($dia)->addDays(1)->format('Y-m-d'); //Obtengo la fecha del dia sabado siguiente
            case 6://Es Sabado
                if ($diaSeleccionado == 0) {
                    return Carbon::parse($dia)->subDays(7)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return Carbon::parse($dia)->subDays(5)->format('Y-m-d'); //Obtengo la fecha del dia lunes
                }
                return Carbon::parse($dia)->addDays(0)->format('Y-m-d'); //Obtengo la fecha del dia sabado siguiente
        }
        return Carbon::parse($dia);
    }

    public static function calculoSiguientesFechasManual($contratos)
    {
        foreach ($contratos as $contrato) {
            //RECORRIDO DE CONTRATOS

            $idContrato = $contrato->id;

            try {

                $abono = DB::select("SELECT created_at FROM abonos WHERE id_contrato = '$idContrato' ORDER BY created_at DESC LIMIT 1");

                $diapago = $contrato->diapago;
                $fechacobroini = null;
                $fechacobrofin = null;
                $diaseleccionado = null;

                if($abono != null) {
                    //Hay abono

                    $fechacreacionultimoabono = $abono[0]->created_at;
                    if(strlen($diapago) == 0) {
                        //diapago es igual a vacio
                        $diapago = Carbon::parse($fechacreacionultimoabono)->format('l');
                    }

                    switch($contrato->formadepago) {
                        case 1: //Semanal
                            if((Carbon::parse($fechacreacionultimoabono)->format('Y-m-d') >= Carbon::parse('2022-09-17')->format('Y-m-d')
                                && Carbon::parse($fechacreacionultimoabono)->format('Y-m-d') <= Carbon::parse('2022-09-23')->format('Y-m-d'))) {
                                //Ya se dio el abono del periodo (Se pondran el rango de las siguientes fechas)
                                $fechacobroini = Carbon::parse('2022-09-24');
                                $fechacobrofin = Carbon::parse('2022-09-30');
                            }else {
                                //No se ha dado el abono del periodo (Se pondran el rango de fechas actuales)
                                $fechacobroini = Carbon::parse('2022-09-17');
                                $fechacobrofin = Carbon::parse('2022-09-23');
                            }
                            break;
                        case 2: //Quincenal
                            if((Carbon::parse($fechacreacionultimoabono)->format('Y-m-d') >= Carbon::parse('2022-09-15')->format('Y-m-d')
                                && Carbon::parse($fechacreacionultimoabono)->format('Y-m-d') <= Carbon::parse('2022-09-30')->format('Y-m-d'))) {
                                //Ya se dio el abono del periodo (Se pondran el rango de las siguientes fechas)
                                $fechacobroini = Carbon::parse('2022-10-01');
                                $fechacobrofin = Carbon::parse('2022-10-15');
                            }else {
                                //No se ha dado el abono del periodo (Se pondran el rango de fechas actuales)
                                $fechacobroini = Carbon::parse('2022-09-15');
                                $fechacobrofin = Carbon::parse('2022-09-30');
                            }
                            break;
                        case 4: //Mensual
                            if((Carbon::parse($fechacreacionultimoabono)->format('Y-m-d') >= Carbon::parse('2022-09-01')->format('Y-m-d')
                                && Carbon::parse($fechacreacionultimoabono)->format('Y-m-d') <= Carbon::parse('2022-09-30')->format('Y-m-d'))) {
                                //Ya se dio el abono del periodo (Se pondran el rango de las siguientes fechas)
                                $fechacobroini = Carbon::parse('2022-10-01');
                                $fechacobrofin = Carbon::parse('2022-10-31');
                            }else {
                                //No se ha dado el abono del periodo (Se pondran el rango de fechas actuales)
                                $fechacobroini = Carbon::parse('2022-09-01');
                                $fechacobrofin = Carbon::parse('2022-09-30');
                            }
                            break;
                    }

                }else {
                    //No hay abono (Se pondran el rango de fechas actuales)

                    if(strlen($diapago) == 0) {
                        //diapago es igual a vacio
                        $diapago = "Monday";
                    }

                    switch($contrato->formadepago) {
                        case 1: //Semanal
                            $fechacobroini = Carbon::parse('2022-09-17');
                            $fechacobrofin = Carbon::parse('2022-09-23');
                            break;
                        case 2: //Quincenal
                            $fechacobroini = Carbon::parse('2022-09-15');
                            $fechacobrofin = Carbon::parse('2022-09-30');
                            break;
                        case 4: //Mensual
                            $fechacobroini = Carbon::parse('2022-09-01');
                            $fechacobrofin = Carbon::parse('2022-09-30');
                            break;
                    }

                }

                //OBTENER DIASELECCIONADO
                $diasCobro = CarbonPeriod::create($fechacobroini, $fechacobrofin); //Obtenemos los dias entre fechacobroini y fechacobrofin
                foreach ($diasCobro as $diaCobro) {
                    //Recorrido de dias entre fechacobroini y fechacobrofin
                    if (self::diaStringAdiaNumero($diapago) === self::diaStringAdiaNumero($diaCobro->format('l'))) {
                        //Dia recorrido es igual a diapago (Viernes = Viernes)
                        $diaseleccionado = $diaCobro; //Se asigna a diaseleccionado el dia del recorrido
                        break;
                    }
                }

                DB::table('contratos')->where('id', '=', $idContrato)->update([
                    'fechacobroini' => $fechacobroini, 'fechacobrofin' => $fechacobrofin,
                    'fechacobroiniantes' => null, 'fechacobrofinantes' => null,
                    'diapago' => $diapago, 'diaseleccionado' => $diaseleccionado
                ]);

            } catch (\Exception $e) {
                \Log::info("Error: Metodo : calculoSiguientesFechasManual: " . $idContrato . "\n" . $e);
                continue;
            }

        }
    }

    public static function insertarcontratostemporalessincronizacion()
    {
        \Log::info('Inició metodo insertarcontratostemporalessincronizacion');

        $nowParse = Carbon::now()->format('Y-m-d');
        $usuarios = DB::select("SELECT u.id as id, u.id_zona as id_zona, u.rol_id as rol_id, uf.id_franquicia as id_franquicia
                                                  FROM users u
                                                  INNER JOIN usuariosfranquicia uf
                                                  ON u.id = uf.id_usuario
                                                  WHERE u.rol_id IN (4,12,13)
                                                  ORDER BY u.rol_id ASC"); //Obtener usuario con rol cobranza, asists y optos activos por franquicia

        if($usuarios != null) {
            //Existen usuario activos

            foreach ($usuarios as $usuario) {
                //Recorrido por usuario

                $idUsuario = $usuario->id;
                $rolUsuario = $usuario->rol_id;
                $idFranquicia = $usuario->id_franquicia;
                $contratos = null;

                try {

                    if($rolUsuario == 4) {
                        //Rol cobranza
                        $idZona = $usuario->id_zona;

                        $contratos = DB::select(
                            "SELECT IFNULL(c.id, '') as id,
                                IFNULL(c.datos, '') as datos,
                                IFNULL(c.id_usuariocreacion, '') as id_usuariocreacion,
                                IFNULL(c.nombre_usuariocreacion, '') as nombre_usuariocreacion,
                                IFNULL(c.id_zona, '') as id_zona,
                                IFNULL(c.estatus, '0') as estatus,
                                IFNULL(c.nombre, '') as nombre,
                                IFNULL(c.calle, '') as calle,
                                IFNULL(c.numero, '') as numero,
                                IFNULL(c.depto, '') as depto,
                                IFNULL(c.alladode, '') as alladode,
                                IFNULL(c.frentea, '') as frentea,
                                IFNULL(c.entrecalles, '') as entrecalles,
                                IFNULL(c.colonia, '') as colonia,
                                IFNULL(c.localidad, '') as localidad,
                                IFNULL(c.telefono, '') as telefono,
                                IFNULL(c.telefonoreferencia, '') as telefonoreferencia,
                                IFNULL(c.correo, '') as correo,
                                IFNULL(c.nombrereferencia, '') as nombrereferencia,
                                IFNULL(c.casatipo, '') as casatipo,
                                IFNULL(c.casacolor, '') as casacolor,
                                SUBSTRING_INDEX(IFNULL(c.fotoine, ''), '/', -1) AS fotoine,
                                SUBSTRING_INDEX(IFNULL(c.fotocasa, ''), '/', -1) as fotocasa,
                                SUBSTRING_INDEX(IFNULL(c.comprobantedomicilio, ''), '/', -1) as comprobantedomicilio,
                                SUBSTRING_INDEX(IFNULL(c.pagare, ''), '/', -1) as pagare,
                                SUBSTRING_INDEX(IFNULL(c.fotootros, ''), '/', -1) as fotootros,
                                IFNULL(c.pagosadelantar, '0') as pagosadelantar,
                                IFNULL(c.created_at, '') as created_at,
                                IFNULL(c.updated_at, '') as updated_at,
                                IFNULL(c.id_optometrista, '') as id_optometrista,
                                SUBSTRING_INDEX(IFNULL(c.tarjeta, ''), '/', -1) as tarjeta,
                                IFNULL(c.pago, '') as pago,
                                IFNULL(c.id_promocion, '') as id_promocion,
                                SUBSTRING_INDEX(IFNULL(c.fotoineatras, ''), '/', -1) as fotoineatras,
                                SUBSTRING_INDEX(IFNULL(c.tarjetapensionatras, ''), '/', -1) as tarjetapensionatras,
                                IFNULL(c.total, '') as total,
                                IFNULL(c.idcontratorelacion, '') as idcontratorelacion,
                                IFNULL(c.contador, '') as contador,
                                IFNULL(c.totalhistorial, '0') as totalhistorial,
                                IFNULL(c.totalpromocion, '0') as totalpromocion,
                                IFNULL(c.totalproducto, '0') as totalproducto,
                                IFNULL(c.totalabono, '0') as totalabono,
                                IFNULL(c.fechaatraso, '') as fechaatraso,
                                IFNULL(c.costoatraso, '0') as costoatraso,
                                IFNULL(c.ultimoabono, '') as ultimoabono,
                                IFNULL(c.estatus_estadocontrato, '') as estatus_estadocontrato,
                                IFNULL(c.diapago, '') as diapago,
                                IFNULL(c.fechacobroini, '') as fechacobroini,
                                IFNULL(c.fechacobrofin, '') as fechacobrofin,
                                IFNULL(c.enganche, '0') as enganche,
                                IFNULL(c.entregaproducto, '0') as entregaproducto,
                                IFNULL(c.diaseleccionado, '') as diaseleccionado,
                                IFNULL(c.fechaentrega, '') as fechaentrega,
                                IFNULL(c.promocionterminada, '0') as promocionterminada,
                                IFNULL(c.subscripcion, '') as subscripcion,
                                IFNULL(c.fechasubscripcion, '') as fechasubscripcion,
                                IFNULL(c.nota, '') as nota,
                                IFNULL(c.totalreal, '') as totalreal,
                                IFNULL(c.diatemporal, '') as diatemporal,
                                IFNULL(c.coordenadas, '') as coordenadas,
                                IFNULL((SELECT p.nombre
                                        FROM paquetes p
                                        WHERE p.id = (SELECT hc.id_paquete
                                                      FROM historialclinico hc
                                                      WHERE hc.id_contrato = c.id
                                                      ORDER BY hc.created_at
                                                      DESC LIMIT 1) LIMIT 1), '') as nombrepaquete,
                                (SELECT a.created_at
                                    FROM abonos a
                                    WHERE a.id_contrato = c.id
                                    AND a.tipoabono != '7'
                                    ORDER BY a.created_at
                                    DESC LIMIT 1) as ultimoabonoreal,
                                IFNULL((SELECT pr.titulo
                                        FROM promocion pr
                                        WHERE (pr.id = c.id_promocion OR pr.id = (SELECT pc.id_promocion FROM promocioncontrato pc WHERE pc.id_contrato = c.idcontratorelacion))), '') as titulopromocion
                                FROM contratos c
                                WHERE c.id_franquicia = '$idFranquicia'
                                AND c.id_zona = '" . $idZona . "'
                                AND (c.estatus_estadocontrato IN (2,4,12)
                                        OR c.id IN (SELECT g.id_contrato
                                                        FROM garantias g
                                                        INNER JOIN contratos con ON con.id = g.id_contrato
                                                        WHERE con.estatus_estadocontrato IN (1,7,9,10,11)
                                                        AND con.id_franquicia = '$idFranquicia'
                                                        AND con.id_zona = '" . $idZona . "'
                                                        AND g.estadogarantia IN (1,2)))");

                    }else {
                        //Asistente o Optometrista

                        $contratos = DB::select(
                            "SELECT IFNULL(c.id, '') as id,
                                IFNULL(c.datos, '') as datos,
                                IFNULL(c.id_usuariocreacion, '') as id_usuariocreacion,
                                IFNULL(c.nombre_usuariocreacion, '') as nombre_usuariocreacion,
                                IFNULL(c.id_zona, '') as id_zona,
                                IFNULL(c.estatus, '0') as estatus,
                                IFNULL(c.nombre, '') as nombre,
                                IFNULL(c.calle, '') as calle,
                                IFNULL(c.numero, '') as numero,
                                IFNULL(c.depto, '') as depto,
                                IFNULL(c.alladode, '') as alladode,
                                IFNULL(c.frentea, '') as frentea,
                                IFNULL(c.entrecalles, '') as entrecalles,
                                IFNULL(c.colonia, '') as colonia,
                                IFNULL(c.localidad, '') as localidad,
                                IFNULL(c.telefono, '') as telefono,
                                IFNULL(c.telefonoreferencia, '') as telefonoreferencia,
                                IFNULL(c.correo, '') as correo,
                                IFNULL(c.nombrereferencia, '') as nombrereferencia,
                                IFNULL(c.casatipo, '') as casatipo,
                                IFNULL(c.casacolor, '') as casacolor,
                                SUBSTRING_INDEX(IFNULL(c.fotoine, ''), '/', -1) AS fotoine,
                                SUBSTRING_INDEX(IFNULL(c.fotocasa, ''), '/', -1) as fotocasa,
                                SUBSTRING_INDEX(IFNULL(c.comprobantedomicilio, ''), '/', -1) as comprobantedomicilio,
                                SUBSTRING_INDEX(IFNULL(c.pagare, ''), '/', -1) as pagare,
                                SUBSTRING_INDEX(IFNULL(c.fotootros, ''), '/', -1) as fotootros,
                                IFNULL(c.pagosadelantar, '0') as pagosadelantar,
                                IFNULL(c.created_at, '') as created_at,
                                IFNULL(c.updated_at, '') as updated_at,
                                IFNULL(c.id_optometrista, '') as id_optometrista,
                                SUBSTRING_INDEX(IFNULL(c.tarjeta, ''), '/', -1) as tarjeta,
                                IFNULL(c.pago, '') as pago,
                                IFNULL(c.id_promocion, '') as id_promocion,
                                SUBSTRING_INDEX(IFNULL(c.fotoineatras, ''), '/', -1) as fotoineatras,
                                SUBSTRING_INDEX(IFNULL(c.tarjetapensionatras, ''), '/', -1) as tarjetapensionatras,
                                IFNULL(c.total, '') as total,
                                IFNULL(c.idcontratorelacion, '') as idcontratorelacion,
                                IFNULL(c.contador, '') as contador,
                                IFNULL(c.totalhistorial, '0') as totalhistorial,
                                IFNULL(c.totalpromocion, '0') as totalpromocion,
                                IFNULL(c.totalproducto, '0') as totalproducto,
                                IFNULL(c.totalabono, '0') as totalabono,
                                IFNULL(c.fechaatraso, '') as fechaatraso,
                                IFNULL(c.costoatraso, '0') as costoatraso,
                                IFNULL(c.ultimoabono, '') as ultimoabono,
                                IFNULL(c.estatus_estadocontrato, '') as estatus_estadocontrato,
                                IFNULL(c.diapago, '') as diapago,
                                IFNULL(c.fechacobroini, '') as fechacobroini,
                                IFNULL(c.fechacobrofin, '') as fechacobrofin,
                                IFNULL(c.enganche, '0') as enganche,
                                IFNULL(c.entregaproducto, '0') as entregaproducto,
                                IFNULL(c.diaseleccionado, '') as diaseleccionado,
                                IFNULL(c.fechaentrega, '') as fechaentrega,
                                IFNULL(c.promocionterminada, '0') as promocionterminada,
                                IFNULL(c.subscripcion, '') as subscripcion,
                                IFNULL(c.fechasubscripcion, '') as fechasubscripcion,
                                IFNULL(c.nota, '') as nota,
                                IFNULL(c.totalreal, '') as totalreal,
                                IFNULL(c.diatemporal, '') as diatemporal,
                                IFNULL(c.coordenadas, '') as coordenadas
                                FROM contratos c
                                WHERE c.id_franquicia = '$idFranquicia'
                                AND (c.id_usuariocreacion = '" . $idUsuario . "' AND c.estatus_estadocontrato IN (0,1,9))
                                OR c.id IN (SELECT g.id_contrato
                                            FROM garantias g
                                            INNER JOIN contratos con ON con.id = g.id_contrato
                                            WHERE g.id_optometrista = '" . $idUsuario . "'
                                            AND con.id_franquicia = '$idFranquicia'
                                            AND (g.estadogarantia = 1 OR (g.estadogarantia = 2 AND STR_TO_DATE(g.updated_at,'%Y-%m-%d') = STR_TO_DATE('$nowParse','%Y-%m-%d')))
                                                AND con.estatus_estadocontrato IN (1,2,5,12,4,9))");

                    }

                    if($contratos != null) {
                        //Existen contratos

                        $existenRegistros = DB::select("SELECT indice
                                                      FROM contratostemporalessincronizacion WHERE id_usuario = '$idUsuario'");

                        foreach ($contratos as $contrato) {

                            try {

                                $nombrepaquete = "";
                                $ultimoabonoreal = "";
                                $titulopromocion = "";

                                if ($rolUsuario == 4) {
                                    //Rol cobranza
                                    $nombrepaquete = $contrato->nombrepaquete;
                                    $ultimoabonoreal = $contrato->ultimoabonoreal;
                                    $titulopromocion = $contrato->titulopromocion;
                                }

                                if($existenRegistros == null) {
                                    //Es la primera vez que se insertan los registros a la tabla contratostemporalessincronizacion con este idUsuario

                                    //Insertar en tabla contratostemporalessincronizacion con estado en 0
                                    DB::table('contratostemporalessincronizacion')->insert([
                                        'id_usuario' => $idUsuario,
                                        'id' => $contrato->id,
                                        'datos' => $contrato->datos,
                                        'id_usuariocreacion' => $contrato->id_usuariocreacion,
                                        'nombre_usuariocreacion' => $contrato->nombre_usuariocreacion,
                                        'id_zona' => $contrato->id_zona,
                                        'estatus' => $contrato->estatus,
                                        'nombre' => $contrato->nombre,
                                        'calle' => $contrato->calle,
                                        'numero' => $contrato->numero,
                                        'depto' => $contrato->depto,
                                        'alladode' => $contrato->depto,
                                        'frentea' => $contrato->frentea,
                                        'entrecalles' => $contrato->entrecalles,
                                        'colonia' => $contrato->colonia,
                                        'localidad' => $contrato->localidad,
                                        'telefono' => $contrato->telefono,
                                        'telefonoreferencia' => $contrato->telefonoreferencia,
                                        'correo' => $contrato->correo,
                                        'nombrereferencia' => $contrato->nombrereferencia,
                                        'casatipo' => $contrato->casatipo,
                                        'casacolor' => $contrato->casacolor,
                                        'fotoine' => $contrato->fotoine,
                                        'fotocasa' => $contrato->fotocasa,
                                        'comprobantedomicilio' => $contrato->comprobantedomicilio,
                                        'pagare' => $contrato->pagare,
                                        'fotootros' => $contrato->fotootros,
                                        'pagosadelantar' => $contrato->pagosadelantar,
                                        'id_optometrista' => $contrato->id_optometrista,
                                        'tarjeta' => $contrato->tarjeta,
                                        'pago' => $contrato->pago,
                                        'id_promocion' => $contrato->id_promocion,
                                        'fotoineatras' => $contrato->fotoineatras,
                                        'tarjetapensionatras' => $contrato->tarjetapensionatras,
                                        'total' => $contrato->total,
                                        'idcontratorelacion' => $contrato->idcontratorelacion,
                                        'contador' => $contrato->contador,
                                        'totalhistorial' => $contrato->totalhistorial,
                                        'totalpromocion' => $contrato->totalpromocion,
                                        'totalproducto' => $contrato->totalproducto,
                                        'totalabono' => $contrato->totalabono,
                                        'fechaatraso' => $contrato->fechaatraso,
                                        'costoatraso' => $contrato->costoatraso,
                                        'ultimoabono' => $contrato->ultimoabono,
                                        'estatus_estadocontrato' => $contrato->estatus_estadocontrato,
                                        'diapago' => $contrato->diapago,
                                        'fechacobroini' => $contrato->fechacobroini,
                                        'fechacobrofin' => $contrato->fechacobrofin,
                                        'enganche' => $contrato->enganche,
                                        'entregaproducto' => $contrato->entregaproducto,
                                        'diaseleccionado' => $contrato->diaseleccionado,
                                        'fechaentrega' => $contrato->fechaentrega,
                                        'promocionterminada' => $contrato->promocionterminada,
                                        'subscripcion' => $contrato->subscripcion,
                                        'fechasubscripcion' => $contrato->fechasubscripcion,
                                        'nota' => $contrato->nota,
                                        'totalreal' => $contrato->totalreal,
                                        'diatemporal' => $contrato->diatemporal,
                                        'coordenadas' => $contrato->coordenadas,
                                        'nombrepaquete' => $nombrepaquete,
                                        'ultimoabonoreal' => $ultimoabonoreal,
                                        'titulopromocion' => $titulopromocion,
                                        'created_at' => $contrato->created_at,
                                        'updated_at' => ($contrato->updated_at == null ? $contrato->created_at : $contrato->updated_at)
                                    ]);

                                }

                            } catch (\Exception $e) {
                                \Log::info("Error: Comando : insercioneliminacioncontratostemporalessincronizacion: " . $contrato->id . " '$idFranquicia'" . "\n" . $e);
                                continue;
                            }

                        }

                    }

                } catch (\Exception $e) {
                    \Log::info("Error: Comando : insertarcontratostemporalessincronizacion: " . $idUsuario . "\n" . $e);
                    continue;
                }

            }

        }

        \Log::info('Finalizó metodo insertarcontratostemporalessincronizacion');
    }

    public static function obtenerFechasPeriodoActualOPeriodoSiguiente($fechaActual, $formaPago, $periodoActual) {

        $arrayRespuesta = array();

        //$fechaActual = Carbon::parse('2022-12-02 00:00:00');
        $fechaActual = Carbon::parse($fechaActual)->format('Y-m-d');
        $diaActual = Carbon::parse($fechaActual)->format('d');
        $hoyNumero = Carbon::parse($fechaActual)->dayOfWeekIso;

        $fechaCobroIniActual = null;
        $fechaCobroFinActual = null;
        $fechaCobroIniSiguiente = null;
        $fechaCobroFinSiguiente = null;

        switch ($formaPago) { //VALIDAMOS QUE FORMA DE PAGO
            case 1: //SEMANAL
                //Calculo fechaCobroIniActual y fechaCobroIniSiguiente
                if($hoyNumero == 1) {
                    //Lunes
                    $fechaCobroIniActual = Carbon::parse($fechaActual); //Se da formato ejemplo: 2022-01-02 00:00:00
                }else if ($hoyNumero == 7) {
                    //Domingo
                    $fechaCobroIniActual = Carbon::parse($fechaActual)->subDays(6); //Se obtiene el lunes anterior
                }else {
                    //Martes, Miercoles, Jueves, Viernes, Sabado
                    $fechaCobroIniActual = self::obtenerDia($fechaActual, $hoyNumero, 1); //Se obtiene el lunes anterior
                    $fechaCobroIniActual = Carbon::parse($fechaCobroIniActual); //Se da formato ejemplo: 2022-01-02 00:00:00
                }
                $fechaCobroIniSiguiente = Carbon::parse($fechaCobroIniActual)->addWeek(); //Se obtiene el lunes de la siguiente semana

                //Calculo fechaCobroFinActual y fechaCobroFinSiguiente
                $fechaCobroFinActual = Carbon::parse($fechaCobroIniActual)->addDays(6); //Se obtiene el viernes siguiente de las fechas actuales
                $fechaCobroFinSiguiente = Carbon::parse($fechaCobroIniSiguiente)->addDays(6); //Se obtiene el viernes siguiente de las fechas siguientes
                break;
            case 2: //QUINCENAL
                if ($diaActual > 14) {
                    //diaActual es del 14 en adelante

                    //Calculo fechaCobroIniActual y fechaCobroFinActual
                    $fechaCobroIniActual = Carbon::parse($fechaActual)->firstOfMonth()->addDays(14); //Se pondra el primer dia del mes actual y se sumaran catorce dias
                    $fechaCobroFinActual = Carbon::parse($fechaCobroIniActual)->endOfMonth()->format('Y-m-d'); //Se obtendra el ultimo dia del mes
                    $fechaCobroFinActual = Carbon::parse($fechaCobroFinActual); //Parsear fechaCobroFinActual para obtener Ejem. 2022-08-31 00:00:00

                    //Calculo fechaCobroIniSiguiente y fechaCobroFinSiguiente
                    $fechaCobroIniSiguiente = Carbon::parse($fechaActual)->firstOfMonth()->addMonth(); //Se pondra el primer dia del mes actual y se sumara un mes
                    $fechaCobroFinSiguiente = Carbon::parse($fechaCobroIniSiguiente)->addDays(13); //Se le sumaran 13 dias a fechaCobroIniSiguiente
                } else {
                    //diaActual es menor o igual a 14

                    //Calculo fechaCobroIniActual y fechaCobroFinActual
                    $fechaCobroIniActual = Carbon::parse($fechaActual)->firstOfMonth();
                    $fechaCobroFinActual = Carbon::parse($fechaCobroIniActual)->addDays(13);

                    //Calculo fechaCobroIniSiguiente y fechaCobroFinSiguiente
                    $fechaCobroIniSiguiente = Carbon::parse($fechaActual)->firstOfMonth()->addDays(14);
                    $fechaCobroFinSiguiente = Carbon::parse($fechaCobroIniSiguiente)->endOfMonth()->format('Y-m-d'); //Se obtendra el ultimo dia del mes
                    $fechaCobroFinSiguiente = Carbon::parse($fechaCobroFinSiguiente); //Parsear fechaCobroFinSiguiente para obtener Ejem. 2022-08-31 00:00:00
                }
                break;
            case 4: //MENSUAL
                //Calculo fechaCobroIniActual y $fechaCobroFinActual
                $fechaCobroIniActual = Carbon::parse($fechaActual)->firstOfMonth(); //Se pondra el primer dia del mes actual
                $fechaCobroFinActual = Carbon::parse($fechaCobroIniActual)->endOfMonth()->format('Y-m-d'); //Se obtendra el ultimo dia del mes
                $fechaCobroFinActual = Carbon::parse($fechaCobroFinActual); //Parsear fechaCobroFinActual para obtener Ejem. 2022-08-31 00:00:00

                //Calculo fechaCobroIniSiguiente y $fechaCobroFinSiguiente
                $fechaCobroIniSiguiente = Carbon::parse($fechaActual)->firstOfMonth()->addMonth(); //Se pondra el primer dia del mes actual y se sumara un mes
                $fechaCobroFinSiguiente = Carbon::parse($fechaCobroIniSiguiente)->endOfMonth()->format('Y-m-d'); //Se obtendra el ultimo dia del mes
                $fechaCobroFinSiguiente = Carbon::parse($fechaCobroFinSiguiente); //Parsear fechaCobroFinSiguiente para obtener Ejem. 2022-08-31 00:00:00
                break;
        }

        if ($periodoActual) {
            //Obtener periodo actual
            array_push($arrayRespuesta, $fechaCobroIniActual);
            array_push($arrayRespuesta, $fechaCobroFinActual);
        }else {
            //Obtener periodo siguiente
            array_push($arrayRespuesta, $fechaCobroIniSiguiente);
            array_push($arrayRespuesta, $fechaCobroFinSiguiente);
        }

        return $arrayRespuesta;
    }

}//clase
