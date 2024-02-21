<?php

namespace App\Clases;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class polizaGlobales
{

    public static function calcularTotales($idFranquicia, $idPoliza, $fechaPoliza){

        //Traemos la ultima poliza de la semana actual.
        $ultimaPoliza = DB::select("SELECT * FROM poliza WHERE id_franquicia = '$idFranquicia' AND STR_TO_DATE(created_at,'%Y-%m-%d') < STR_TO_DATE('$fechaPoliza','%Y-%m-%d')
                                                    ORDER BY created_at DESC LIMIT 1");//Traemos la ultima poliza sin importar si es de la semana actual o no.
        $totalAnterior = $ultimaPoliza[0]->total == null ? 0 : $ultimaPoliza[0]->total;

        $totaldia = DB::select("SELECT SUM(ingresosventas) as ingreso FROM polizaventasdias WHERE id_poliza = '$idPoliza'");
        $totalingresocobranza = DB::select("SELECT SUM(ingresocobranza) as ingreso FROM polizacobranza WHERE id_poliza = '$idPoliza'");
        $ingresosAdmin = DB::select("SELECT COALESCE(SUM(monto),0) as monto FROM ingresosoficina WHERE id_poliza = '$idPoliza' AND tipo IN (0,1,2)");

        $ingresosVentas = $totaldia[0]->ingreso == null ? 0 : $totaldia[0]->ingreso;
        $ingresosCobranza = $totalingresocobranza[0]->ingreso == null ? 0: $totalingresocobranza[0]->ingreso;

        $ingresoAdmin = $ingresosAdmin[0]->monto == null ? 0 : $ingresosAdmin[0]->monto;

        $gastosadmon = DB::select("SELECT COALESCE(SUM(monto),0) as monto FROM gastos WHERE id_poliza = '$idPoliza' and tipogasto IN (0,7,8,15,16,17,18)");
        $gastosventas = DB::select("SELECT COALESCE(SUM(monto),0) as monto FROM gastos WHERE id_poliza = '$idPoliza' and tipogasto IN (1,4,9,10)");
        $gastoscobranza = DB::select("SELECT COALESCE(SUM(monto),0) as monto FROM gastos WHERE id_poliza = '$idPoliza' and tipogasto IN (2,5,6,11,12,13,14)");
        $otrosgastos = DB::select("SELECT COALESCE(SUM(monto),0) as monto FROM gastos WHERE id_poliza = '$idPoliza' and tipogasto = 3");

        $gastosAdmin = $gastosadmon[0]->monto == null ? 0 : $gastosadmon[0]->monto;
        $gastoVentas = $gastosventas[0]->monto == null ? 0 : $gastosventas[0]->monto;
        $gastoCobranza = $gastoscobranza[0]->monto == null ? 0 : $gastoscobranza[0]->monto;
        $otrosGastos = $otrosgastos[0]->monto == null ? 0 : $otrosgastos[0]->monto;

        $total = ((float)$totalAnterior+(float)$ingresoAdmin+(float)$ingresosVentas+(float)$ingresosCobranza)-((float)$gastosAdmin+(float)$gastoVentas+(float)$gastoCobranza+(float)$otrosGastos);

        DB::table("poliza")->where("id","=",$idPoliza)->where("id_franquicia","=",$idFranquicia)->update([
            "gastosadmin" => $gastosAdmin,
            "gastosventas" =>$gastoVentas,
            "gastoscobranza" => $gastoCobranza,
            "otrosgastos" => $otrosGastos,
            "ingresosadmin" => $ingresoAdmin,
            "ingresosventas" => $ingresosVentas,
            "ingresoscobranza" => $ingresosCobranza,
            "total" => $total
        ]);

    }

    public static function calculosVentasOptos($idPoliza, $ultimaPolizaId, $idUsuario)
    {

        $query = "SELECT u.id,u.name,
                                            COALESCE((SELECT acumuladas FROM polizaventasdias pvd  WHERE pvd.id_usuario = u.id AND pvd.rol = '12' AND pvd.id_poliza = '$ultimaPolizaId'),0) as acumuladas,
                                            (SELECT rol FROM polizaventasdias pvd WHERE pvd.id_usuario = u.id AND pvd.rol = '12' AND pvd.id_poliza = '$ultimaPolizaId') as rol,
                                            (SELECT count(c.id) FROM contratos c WHERE c.id_optometrista = u.id AND c.polizaoptometrista = '$idPoliza' AND c.aprobacionventa IN (0,1)) as diaActual,
                                            (SELECT id_tipoasistencia FROM asistencia a WHERE a.id_usuario = u.id AND a.id_poliza = '$idPoliza') as asistencia,
                                            COALESCE((SELECT SUM(cp.total)
                                                            FROM contratos c INNER JOIN contratoproducto cp ON c.id = cp.id_contrato
                                                            INNER JOIN abonos a ON a.id_contrato = c.id
                                                            INNER JOIN producto p ON cp.id_producto = p.id
                                                            WHERE (a.id_usuario = (SELECT us.id FROM users us WHERE us.rol_id IN (12,13) AND us.id = a.id_usuario))
															AND c.id_optometrista = u.id AND p.id_tipoproducto = 3
                                                            AND a.id_contratoproducto = cp.id AND a.poliza = '$idPoliza'),0) as gotas,
                                            COALESCE((SELECT SUM(a.abono)
                                                            FROM abonos a INNER JOIN contratos c ON c.id = a.id_contrato WHERE a.tipoabono IN (1,4,5)
                                                            AND c.id_optometrista = u.id AND (a.id_usuario = c.id_usuariocreacion OR a.id_usuario = c.id_optometrista)
                                                            AND a.poliza = '$idPoliza'),0) as enganche,
                                            COALESCE((SELECT SUM(a.abono)
                                                            FROM abonos a INNER JOIN contratos c ON c.id = a.id_contrato WHERE a.tipoabono NOT IN (1,4,5,7)
                                                            AND c.id_optometrista = u.id AND (a.id_usuario = c.id_usuariocreacion OR a.id_usuario = c.id_optometrista)
                                                            AND a.poliza = '$idPoliza'),0) as abonos,
                                           COALESCE((SELECT SUM(cp.total)
                                                            FROM contratos c INNER JOIN contratoproducto cp ON c.id = cp.id_contrato
                                                            INNER JOIN abonos a ON a.id_contrato = c.id
                                                            INNER JOIN producto p ON cp.id_producto = p.id
                                                            WHERE (a.id_usuario = (SELECT us.id FROM users us WHERE us.rol_id IN (12,13) AND us.id = a.id_usuario))
															AND c.id_optometrista = u.id AND p.id_tipoproducto = 2
                                                            AND a.id_contratoproducto = cp.id AND a.poliza = '$idPoliza'),0) as polizas,
                                            COALESCE((SELECT ingresosventasacumulado FROM polizaventasdias pvd  WHERE pvd.id_usuario = u.id AND pvd.rol = '12'
                                                            AND pvd.id_poliza = '$ultimaPolizaId'),0) as ingresosventasacumulado,
                                            COALESCE((SELECT SUM(p.polizagastos)
                                                            FROM contratos c INNER JOIN contratoproducto cp ON c.id = cp.id_contrato
                                                            INNER JOIN abonos a ON a.id_contrato = c.id
                                                            INNER JOIN producto p ON cp.id_producto = p.id
                                                            WHERE (a.id_usuario = (SELECT us.id FROM users us WHERE us.rol_id IN (12,13) AND us.id = a.id_usuario))
															AND c.id_optometrista = u.id AND p.id_tipoproducto = 3
                                                            AND a.id_contratoproducto = cp.id AND a.poliza = '$idPoliza'),0) as polizagastosgotas,
                                            COALESCE((SELECT SUM(p.polizagastos)
                                                            FROM contratos c INNER JOIN contratoproducto cp ON c.id = cp.id_contrato
                                                            INNER JOIN abonos a ON a.id_contrato = c.id
                                                            INNER JOIN producto p ON cp.id_producto = p.id
                                                            WHERE (a.id_usuario = (SELECT us.id FROM users us WHERE us.rol_id IN (12,13) AND us.id = a.id_usuario))
															AND c.id_optometrista = u.id AND p.id_tipoproducto = 2
                                            				AND a.id_contratoproducto = cp.id AND a.poliza = '$idPoliza'),0) as polizagastospolizas,
                                            COALESCE((SELECT SUM(cp.piezas)
                                                            FROM contratos c INNER JOIN contratoproducto cp ON c.id = cp.id_contrato
                                                            INNER JOIN abonos a ON a.id_contrato = c.id
                                                            INNER JOIN producto p ON cp.id_producto = p.id
                                                            WHERE (a.id_usuario = (SELECT us.id FROM users us WHERE us.rol_id IN (12,13) AND us.id = a.id_usuario))
															AND c.id_optometrista = u.id AND p.id_tipoproducto = 3
                                                            AND a.id_contratoproducto = cp.id AND a.poliza = '$idPoliza'),0) as piezasgotas,
                                            COALESCE((SELECT SUM(cp.piezas)
                                                            FROM contratos c INNER JOIN contratoproducto cp ON c.id = cp.id_contrato
                                                            INNER JOIN abonos a ON a.id_contrato = c.id
                                                            INNER JOIN producto p ON cp.id_producto = p.id
                                                            WHERE (a.id_usuario = (SELECT us.id FROM users us WHERE us.rol_id IN (12,13) AND us.id = a.id_usuario))
															AND c.id_optometrista = u.id AND p.id_tipoproducto = 2
                                            				AND a.id_contratoproducto = cp.id AND a.poliza = '$idPoliza'),0) as piezaspolizas
                                            FROM users u WHERE u.id = '$idUsuario'";

        return DB::select($query);

    }

    public static function calculosVentasAsis($idPoliza, $ultimaPolizaId, $idUsuario)
    {

        $query = "SELECT u.id,u.name,
                                        COALESCE((SELECT acumuladas FROM polizaventasdias pvd  WHERE pvd.id_usuario = u.id AND pvd.rol = '13' AND pvd.id_poliza = '$ultimaPolizaId'),0) as acumuladas,
                                        (SELECT rol FROM polizaventasdias pvd WHERE pvd.id_usuario = u.id AND pvd.rol = '13' AND pvd.id_poliza = '$ultimaPolizaId') as rol,
                                        (SELECT count(c.id) FROM contratos c WHERE c.id_usuariocreacion = u.id AND c.poliza = '$idPoliza' AND c.aprobacionventa IN (0,2)) as diaActual,
                                        (SELECT id_tipoasistencia FROM asistencia a WHERE a.id_usuario = u.id AND a.id_poliza = '$idPoliza') as asistencia
                                        FROM users u WHERE u.id = '$idUsuario'";

        return DB::select($query);

    }

    public static function calculoProductividadOptos($idPoliza, $ultimaPolizaId, $idUsuario)
    {

        $query = "SELECT
                                        u.id as ID,
                                        u.sueldo as SUELDO,
                                        COALESCE((SELECT pp.totaleco FROM polizaproductividad pp WHERE pp.id_poliza = '$ultimaPolizaId' AND pp.id_usuario = u.id AND pp.rol = '12'),0) as ECOJRANT,
                                        COALESCE((SELECT pp.totaljr FROM polizaproductividad pp WHERE pp.id_poliza = '$ultimaPolizaId' AND pp.id_usuario = u.id AND pp.rol = '12'),0) as JUNIORANT,
                                        COALESCE((SELECT pp.totaldoradouno FROM polizaproductividad pp WHERE pp.id_poliza = '$ultimaPolizaId' AND pp.id_usuario = u.id AND pp.rol = '12'),0) as DORADOUNOANT,
                                        COALESCE((SELECT pp.totaldoradodos FROM polizaproductividad pp WHERE pp.id_poliza = '$ultimaPolizaId' AND pp.id_usuario = u.id AND pp.rol = '12'),0) as DORADODOSANT,
                                        COALESCE((SELECT pp.totalplatino FROM polizaproductividad pp WHERE pp.id_poliza = '$ultimaPolizaId' AND pp.id_usuario = u.id AND pp.rol = '12'),0) as PLATINOANT,
                                        COALESCE((SELECT pp.totalpremium FROM polizaproductividad pp WHERE pp.id_poliza = '$ultimaPolizaId' AND pp.id_usuario = u.id AND pp.rol = '12'),0) as PREMIUMANT,
                                        (SELECT COUNT(hc.id) FROM historialclinico hc INNER JOIN contratos c ON c.id = hc.id_contrato
                                                WHERE hc.tipo = '0' AND hc.id_paquete IN (1,2,3) AND c.polizaoptometrista = '$idPoliza' AND c.id_optometrista = u.id AND c.aprobacionventa IN (0,1)) as ECOJR,
                                        (SELECT COUNT(hc.id) FROM historialclinico hc INNER JOIN contratos c ON c.id = hc.id_contrato
                                                WHERE hc.tipo = '0' AND hc.id_paquete  = 4 AND c.polizaoptometrista = '$idPoliza' AND c.id_optometrista = u.id AND c.aprobacionventa IN (0,1)) as JUNIOR,
                                        (SELECT COUNT(hc.id) FROM historialclinico hc INNER JOIN contratos c ON c.id = hc.id_contrato
                                                WHERE hc.tipo = '0' AND hc.id_paquete  = 5 AND c.polizaoptometrista = '$idPoliza' AND c.id_optometrista = u.id AND c.aprobacionventa IN (0,1)) as DORADOUNO,
                                        (SELECT COUNT(hc.id) FROM historialclinico hc INNER JOIN contratos c ON c.id = hc.id_contrato
                                                WHERE hc.tipo = '0' AND hc.id_paquete  = 6 AND c.polizaoptometrista = '$idPoliza' AND c.id_optometrista = u.id AND c.aprobacionventa IN (0,1)) as DORADODOS,
                                        (SELECT COUNT(hc.id) FROM historialclinico hc INNER JOIN contratos c ON c.id = hc.id_contrato
                                                WHERE hc.tipo = '0' AND hc.id_paquete  = 7 AND c.polizaoptometrista = '$idPoliza' AND c.id_optometrista = u.id AND c.aprobacionventa IN (0,1)) as PLATINO,
                                        (SELECT COUNT(hc.id) FROM historialclinico hc INNER JOIN contratos c ON c.id = hc.id_contrato
                                                WHERE hc.tipo = '0' AND hc.id_paquete  = 8 AND c.polizaoptometrista = '$idPoliza' AND c.id_optometrista = u.id AND c.aprobacionventa IN (0,1)) as PREMIUM,
                                        (SELECT preciom+precioa+preciob+preciot+precioe FROM insumos) as INSUMOS
                                        FROM users u INNER JOIN roles r ON r.id = u.rol_id WHERE u.id = '$idUsuario'";

        return DB::select($query);

    }

    public static function calculoProductividadAsis($idPoliza, $ultimaPolizaId, $idUsuario)
    {

        $query = "SELECT
                                        u.id as ID,
                                        u.sueldo as SUELDO,
                                         COALESCE((SELECT pp.totaleco FROM polizaproductividad pp WHERE pp.id_poliza = '$ultimaPolizaId' AND pp.id_usuario = u.id AND pp.rol = '13'),0) as ECOJRANT,
                                         COALESCE((SELECT pp.totaljr FROM polizaproductividad pp WHERE pp.id_poliza = '$ultimaPolizaId' AND pp.id_usuario = u.id AND pp.rol = '13'),0) as JUNIORANT,
                                         COALESCE((SELECT pp.totaldoradouno FROM polizaproductividad pp WHERE pp.id_poliza = '$ultimaPolizaId' AND pp.id_usuario = u.id AND pp.rol = '13'),0) as DORADOUNOANT,
                                         COALESCE((SELECT pp.totaldoradodos FROM polizaproductividad pp WHERE pp.id_poliza = '$ultimaPolizaId' AND pp.id_usuario = u.id AND pp.rol = '13'),0) as DORADODOSANT,
                                         COALESCE((SELECT pp.totalplatino FROM polizaproductividad pp WHERE pp.id_poliza = '$ultimaPolizaId' AND pp.id_usuario = u.id AND pp.rol = '13'),0) as PLATINOANT,
                                         COALESCE((SELECT pp.totalpremium FROM polizaproductividad pp WHERE pp.id_poliza = '$ultimaPolizaId' AND pp.id_usuario = u.id AND pp.rol = '13'),0) as PREMIUMANT,
                                        (SELECT COUNT(hc.id) FROM historialclinico hc INNER JOIN contratos c ON c.id = hc.id_contrato
                                                WHERE hc.tipo = '0' AND hc.id_paquete IN (1,2,3) AND c.poliza = '$idPoliza' AND c.id_usuariocreacion = u.id AND c.aprobacionventa IN (0,2)) as ECOJR,
                                        (SELECT COUNT(hc.id) FROM historialclinico hc INNER JOIN contratos c ON c.id = hc.id_contrato
                                                WHERE hc.tipo = '0' AND hc.id_paquete  = 4 AND c.poliza = '$idPoliza' AND c.id_usuariocreacion = u.id AND c.aprobacionventa IN (0,2)) as JUNIOR,
                                        (SELECT COUNT(hc.id) FROM historialclinico hc INNER JOIN contratos c ON c.id = hc.id_contrato
                                                WHERE hc.tipo = '0' AND hc.id_paquete  = 5 AND c.poliza = '$idPoliza' AND c.id_usuariocreacion = u.id AND c.aprobacionventa IN (0,2)) as DORADOUNO,
                                        (SELECT COUNT(hc.id) FROM historialclinico hc INNER JOIN contratos c ON c.id = hc.id_contrato
                                                WHERE hc.tipo = '0' AND hc.id_paquete  = 6 AND c.poliza = '$idPoliza' AND c.id_usuariocreacion = u.id AND c.aprobacionventa IN (0,2)) as DORADODOS,
                                        (SELECT COUNT(hc.id) FROM historialclinico hc INNER JOIN contratos c ON c.id = hc.id_contrato
                                                WHERE hc.tipo = '0' AND hc.id_paquete  = 7 AND c.poliza = '$idPoliza' AND c.id_usuariocreacion = u.id AND c.aprobacionventa IN (0,2)) as PLATINO,
                                        (SELECT COUNT(hc.id) FROM historialclinico hc INNER JOIN contratos c ON c.id = hc.id_contrato
                                                WHERE hc.tipo = '0' AND hc.id_paquete  = 8 AND c.poliza = '$idPoliza' AND c.id_usuariocreacion = u.id AND c.aprobacionventa IN (0,2)) as PREMIUM,
                                        (SELECT preciom+precioa+preciob+preciot+precioe FROM insumos) as INSUMOS
                                        FROM users u INNER JOIN roles r ON r.id = u.rol_id WHERE u.id = '$idUsuario'";

        return DB::select($query);

    }

    public static function calculoDeCobranza($idFranquicia, $idPoliza, $ultimaPolizaId, $idUsuario)
    {

        $cobradores = array();
        if(strlen($idUsuario) > 0) {
            //idUsuario es diferente de vacio
            $usuarioConsulta = DB::select("SELECT u.id as ID, u.supervisorcobranza as SUPERVISORCOBRANZA, z.zona as ZONA, u.id_zona as idZona FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                            INNER JOIN zonas z ON z.id = u.id_zona WHERE u.id = '$idUsuario' AND uf.id_franquicia = '$idFranquicia'");
            $idUsuarioConsulta = $usuarioConsulta == null ? null : $usuarioConsulta[0]->ID;
            $supervisorCobranzaConsulta = $usuarioConsulta == null ? null : $usuarioConsulta[0]->SUPERVISORCOBRANZA;
            $zonaConsulta = $usuarioConsulta == null ? null : $usuarioConsulta[0]->ZONA;
            $idZonaCobranzaConsulta = $usuarioConsulta == null ? null : $usuarioConsulta[0]->idZona;

            if ($supervisorCobranzaConsulta == 1) {
                //Es supervisor
                $existeCobradorNormalZona = DB::select("SELECT u.id as ID FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                                WHERE u.id_zona = '$idZonaCobranzaConsulta' AND uf.id_franquicia = '$idFranquicia' AND u.supervisorcobranza = '0'");
                if ($existeCobradorNormalZona != null) {
                    //Existe cobrador normal
                    $cobradores = DB::select("SELECT u.id as ID,u.name as NOMBRE,z.zona as ZONA,u.id_zona as idZona,u.sueldo FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                        INNER JOIN zonas z ON z.id = u.id_zona WHERE u.id = '" . $existeCobradorNormalZona[0]->ID . "' AND uf.id_franquicia = '$idFranquicia' AND u.supervisorcobranza = '0'");
                }else {
                    //No existe cobrador normal

                    $cobrador = (object)[
                        'ID' => null,
                        'NOMBRE' => null,
                        'idZona' => $idZonaCobranzaConsulta,
                        'ZONA' => $zonaConsulta,
                        'sueldo' => null,
                    ];

                    $existeCobradorEliminado = DB::select("SELECT id_usuario FROM cobradoreseliminados WHERE id_zona = '$idZonaCobranzaConsulta'");
                    if ($existeCobradorEliminado != null) {
                        //Existe cobrador eliminado
                        $usuario = DB::select("SELECT id, name, sueldo FROM users WHERE id = '" . $existeCobradorEliminado[0]->id_usuario . "'");
                        if ($usuario != null) {
                            //Existe usuario
                            $cobrador = (object)[
                                'ID' => $usuario[0]->id,
                                'NOMBRE' => $usuario[0]->name,
                                'idZona' => $idZonaCobranzaConsulta,
                                'ZONA' => $zonaConsulta,
                                'sueldo' => $usuario[0]->sueldo,
                            ];
                        }
                    }

                    array_push($cobradores, $cobrador);
                }
            }else {
                //No es supervisor
                $idUsuario = $idUsuarioConsulta; //Es cobrador normal
                $cobradores = DB::select("SELECT u.id as ID,u.name as NOMBRE,z.zona as ZONA,u.id_zona as idZona,u.sueldo FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                        INNER JOIN zonas z ON z.id = u.id_zona WHERE u.id = '$idUsuario' AND uf.id_franquicia = '$idFranquicia' AND u.supervisorcobranza = '0'");
            }
        }else {
            //Obtenemos todos los cobradores
            $zonas = DB::select("SELECT id, zona FROM zonas WHERE id_franquicia = '$idFranquicia'");
            foreach ($zonas as $zona) {
                //Recorrido de zonas
                $cobrador = DB::table('users as u')
                    ->select('u.id as ID', 'u.name as NOMBRE', 'z.zona as ZONA', 'u.id_zona as idZona', 'u.sueldo')
                    ->join('usuariosfranquicia as uf', 'uf.id_usuario', '=', 'u.id')
                    ->join('zonas as z', 'z.id', '=', 'u.id_zona')
                    ->where('u.rol_id', '4')
                    ->where('uf.id_franquicia', $idFranquicia)
                    ->where('u.id_zona', $zona->id)
                    ->where('u.supervisorcobranza', '0')
                    ->first();

                if ($cobrador == null) {
                    $cobrador = (object)[
                        'ID' => null,
                        'NOMBRE' => null,
                        'idZona' => $zona->id,
                        'ZONA' => $zona->zona,
                        'sueldo' => null,
                    ];
                }
                array_push($cobradores, $cobrador);
            }
        }

        $hoy = Carbon::now();
        //$hoy = Carbon::parse("2023-09-04");
        $hoyDiaDelMes = $hoy->format("d");
        $hoyNumero = $hoy->dayOfWeekIso; // Comienza en lunes -> 1 y obtenemos el dia actual de la semana
        $fechaSabadoAntes = self::obtenerDia($hoyNumero, 0); //Obtenemos la fecha del dia sabado anterior
        $fechaSabadoSiguiente = self::obtenerDia($hoyNumero, 2); //Obtenemos la fecha del dia sabado siguiente

        $fechaLunesAntes = Carbon::parse($fechaSabadoAntes)->addDays(2)->format('Y-m-d'); //Obtenemos la fecha del dia lunes anterior
        $fechaLunesSiguiente = Carbon::parse($fechaSabadoSiguiente)->addDays(2)->format('Y-m-d'); //Obtenemos la fecha del dia lunes siguiente

        if ($hoyNumero == 1) {
            //Lunes
            $fechaLunesSiguiente = Carbon::parse($hoy)->format('Y-m-d'); //Obtenemos la fecha del dia lunes siguiente
            $fechaLunesAntes = Carbon::parse($fechaLunesSiguiente)->subWeek()->format('Y-m-d'); //Obtenemos la fecha del dia lunes anterior
        }

        $primerpoliza = true; //Bandera para saber si sera la primerapoliza para hacer el calculo de tabular si no para tomar los datos de la tabla de polizacontratoscobranza
        $idPrimerPoliza = null;

        if(strlen($idUsuario) > 0 && $hoyNumero == 2) {
            //idUsuario es diferente de vacio && es martes
            $primerpoliza = false;
            $idPrimerPoliza = $idPoliza;
        }

        if($hoyNumero != 2) {
            //miercoles, jueves, viernes, sabado o lunes

            $fecha = Carbon::parse($hoy)->format('Y-m-d');

            $hoyNumeroTemporal = $hoyNumero;
            if ($hoyNumero == 1) {
                //Es lunes
                $hoyNumeroTemporal = 8;
            }

            for ($i = ($hoyNumeroTemporal - 2); $i > 0; $i--) {

                //Obtener fechas de dias anteriores
                $fecha = Carbon::create($fecha)->subDays(1)->format('Y-m-d'); //Descontando dias
                $poliza = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' AND STR_TO_DATE(created_at,'%Y-%m-%d') = STR_TO_DATE('$fecha','%Y-%m-%d')");

                if($poliza != null) {
                    //Existe poliza
                    $idPrimerPoliza = $poliza[0]->id;
                    $primerpoliza = false;
                }

            }

        }

        foreach ($cobradores as $cobrador) {

            $idCobrador = $cobrador->ID;
            $nombreCobrador = $cobrador->NOMBRE;
            $zonaCobrador = $cobrador->ZONA;
            $idZonaCobrador = $cobrador->idZona;
            $sueldoCobrador = $cobrador->sueldo == null ? 0 : $cobrador->sueldo;
            $tabular = 0;
            $arrayTarjetasPagadas = array();
            $arrayTarjetasCobradas = array();
            $sumaTotalAbonosPagadosTarjetas = 0;
            $sumaTotalAbonosPagadosTransferencia = 0;
            $cumplioSetentaYCinco = 0;
            $cumplioOchenta = 0;

            if($primerpoliza) {

                /* Se obtienen contratos con estado ENTREGADO, ABONO ATRASADO, ENVIADO, al igual que TERMINADO, PROCESO DE APROBACION, APROBADOS, MANOFACTURA, PROCESO DE ENVIO y en
                funcion validarContratoTabular() se validan si la garantia ha sido creada y LIQUIDADO y que ultimo abono haya sido despues del sabado anterior */
                $query = "SELECT c.id as ID,
                                    c.estatus_estadocontrato as ESTATUS,
                                        c.ultimoabono as ULTIMOABONO,
                                            c.pago as PAGO,
                                                c.fechaentrega as FECHAENTREGACONTRATO,
							                        (SELECT h.fechaentrega FROM historialclinico h WHERE h.id_contrato = c.id AND h.tipo != '2' ORDER BY h.created_at DESC LIMIT 1) as FECHAENTREGAHISTORIAL,
								                        c.fechacobroini as INICIAL,
                                                            c.fechacobrofin as FINAL,
                                                                (SELECT g.estadogarantia FROM garantias g
                                                                    WHERE g.id_contrato = c.id AND g.estadogarantia IN (1,2) ORDER BY g.created_at DESC LIMIT 1) as ESTADOGARANTIA
                                                    FROM contratos c
                                                    WHERE c.id_franquicia = '$idFranquicia' AND c.id_zona = '$idZonaCobrador'
                                                    AND (c.estatus_estadocontrato IN (1,2,4,7,10,11,12)
                                                        OR (c.estatus_estadocontrato = 5 AND c.ultimoabono >= '$fechaLunesAntes'))";

                $contratos = DB::select($query); //Obtenemos todos los contratos
                foreach ($contratos as $contrato) {
                    //Recorrido de contratos
                    $tabular = self::validarContratoTabular($tabular, $idPoliza, $contrato->ID, $contrato->ESTATUS, $contrato->ULTIMOABONO,
                        $contrato->PAGO, $contrato->FECHAENTREGACONTRATO, $contrato->FECHAENTREGAHISTORIAL,
                        $contrato->INICIAL, $contrato->FINAL, $contrato->ESTADOGARANTIA, $fechaLunesAntes, $fechaLunesSiguiente, $hoy); //Validar para incrementar tabular

                    $respuestaLiquidados = self::validarContratosLiquidados($contrato->ESTATUS, $contrato->ULTIMOABONO, $fechaLunesAntes); //Validar tarjetas pagadas esta semana
                    if ($respuestaLiquidados) {
                        //Entro en tarjetas liquidadas
                        array_push($arrayTarjetasPagadas, $contrato->ID); //Agregar id_contrato al $arrayTarjetasPagadas
                    }

                    if (strlen($idCobrador) > 0) {
                        //Existe cobrador en la zona
                        $respuestaCobradas = self::validarUltimasTarjetasCobradas($idPoliza, $idCobrador, $contrato->ID, $sumaTotalAbonosPagadosTarjetas, $sumaTotalAbonosPagadosTransferencia);
                        if ($respuestaCobradas[0]) {
                            //Entro en tarjetas cobradas
                            array_push($arrayTarjetasCobradas, $contrato->ID); //Agregar id_contrato al $arrayTarjetasCobradas
                            $sumaTotalAbonosPagadosTarjetas = $respuestaCobradas[1];
                            $sumaTotalAbonosPagadosTransferencia = $respuestaCobradas[2];
                        }
                    }
                }

            }else {

                $query = "SELECT c.id as ID,
                                    c.estatus_estadocontrato as ESTATUS,
                                        c.ultimoabono as ULTIMOABONO
                            FROM polizacontratoscobranza pcc INNER JOIN contratos c ON pcc.id_contrato = c.id
                            WHERE pcc.id_poliza = '$idPrimerPoliza' AND c.id_zona = '$idZonaCobrador' GROUP BY c.id, c.estatus_estadocontrato, c.ultimoabono";

                $contratos = DB::select($query); //Obtenemos todos los contratos de la tabla de polizacontratoscobranza

                //Obtenemos todos los contratos cancelados de la tabla polizacontratoscobranza
                $contratoscancelados = DB::select("SELECT COUNT(c.id) AS contratoscancelados
                                                            FROM polizacontratoscobranza pcc INNER JOIN contratos c ON pcc.id_contrato = c.id
                                                            WHERE pcc.id_poliza = '$idPrimerPoliza' AND c.id_zona = '$idZonaCobrador' AND c.estatus_estadocontrato IN (3,6,8,14,15)");
                $tabular = count($contratos) - $contratoscancelados[0]->contratoscancelados;

                if($tabular < 0) {
                    $tabular = 0;
                }

                foreach ($contratos as $contrato) {
                    //Recorrido de contratos
                    $respuestaLiquidados = self::validarContratosLiquidados($contrato->ESTATUS, $contrato->ULTIMOABONO, $fechaLunesAntes); //Validar tarjetas pagadas esta semana
                    if ($respuestaLiquidados) {
                        //Entro en tarjetas liquidadas
                        array_push($arrayTarjetasPagadas, $contrato->ID); //Agregar id_contrato al $arrayTarjetasPagadas
                    }

                    if (strlen($idCobrador) > 0) {
                        //Existe cobrador en la zona
                        $respuestaCobradas = self::validarUltimasTarjetasCobradas($idPoliza, $idCobrador, $contrato->ID, $sumaTotalAbonosPagadosTarjetas, $sumaTotalAbonosPagadosTransferencia);
                        if ($respuestaCobradas[0]) {
                            //Entro en tarjetas cobradas
                            array_push($arrayTarjetasCobradas, $contrato->ID); //Agregar id_contrato al $arrayTarjetasCobradas
                            $sumaTotalAbonosPagadosTarjetas = $respuestaCobradas[1];
                            $sumaTotalAbonosPagadosTransferencia = $respuestaCobradas[2];
                        }
                    }
                }

            }

            $mensajehistorialpoliza = "";
            if (strlen($idUsuario) > 0) {
                //idUsuario es diferente de vacio
                $mensajehistorialpoliza = " actualizado";
                //Eliminar gasto de la tabla gastos que contengan ese $idPoliza y el idusuario del cobrador
                DB::delete("DELETE FROM gastos WHERE id_poliza = '$idPoliza' AND tipogasto IN (5,6) AND id_zona = '$idZonaCobrador'");
                DB::delete("DELETE FROM gastos WHERE id_poliza = '$idPoliza' AND tipogasto IN (7,8,15,16) AND id_zona = '$idZonaCobrador'");
                DB::delete("DELETE FROM gastos WHERE id_poliza = '$idPoliza' AND tipogasto IN (11,12,13,14) AND id_zona = '$idZonaCobrador'");
                DB::delete("DELETE FROM ingresosoficina WHERE id_poliza = '$idPoliza' AND tipo IN (1) AND id_zona = '$idZonaCobrador'");
            }

            $archivo = 0;
            if (strlen($idCobrador) > 0) {
                //Existe cobrador en la zona
                //Obtener contratos que tuvieron por lo menos un abono y que no haya entrado en el tabular de la semana y que se cobraron
                $contratosAbonosPoliza = DB::select("SELECT c.id as ID,
                                    c.estatus_estadocontrato as ESTATUS,
                                        c.ultimoabono as ULTIMOABONO
                            FROM abonos a INNER JOIN contratos c ON a.id_contrato = c.id
                            WHERE a.poliza = '$idPoliza' AND a.id_usuario = '$idCobrador' GROUP BY c.id, c.estatus_estadocontrato, c.ultimoabono");

                $contadorAbonosPoliza = 0;
                foreach ($contratosAbonosPoliza as $contrato) {
                    //Recorrido de contratos
                    $respuestaLiquidados = self::validarContratosLiquidados($contrato->ESTATUS, $contrato->ULTIMOABONO, $fechaLunesAntes); //Validar tarjetas pagadas esta semana
                    if ($respuestaLiquidados && !in_array($contrato->ID, $arrayTarjetasPagadas)) {
                        //Entro en tarjetas liquidadas y no se encuentra el id_contrato en el $arrayTarjetasPagadas
                        array_push($arrayTarjetasPagadas, $contrato->ID); //Agregar id_contrato al $arrayTarjetasPagadas
                    }

                    $respuestaCobradas = self::validarUltimasTarjetasCobradas($idPoliza, $idCobrador, $contrato->ID, $sumaTotalAbonosPagadosTarjetas, $sumaTotalAbonosPagadosTransferencia);
                    if ($respuestaCobradas[0] && !in_array($contrato->ID, $arrayTarjetasCobradas)) {
                        //Entro en tarjetas cobradas y no se encuentra el id_contrato en el $arrayTarjetasCobradas
                        array_push($arrayTarjetasCobradas, $contrato->ID); //Agregar id_contrato al $arrayTarjetasCobradas
                        $sumaTotalAbonosPagadosTarjetas = $respuestaCobradas[1];
                        $sumaTotalAbonosPagadosTransferencia = $respuestaCobradas[2];

                        //Agregar nuevos registros a tabla polizacontratoscobranza en caso de que no exista
                        $idPolizaConsulta = $primerpoliza ? $idPoliza : $idPrimerPoliza;
                        $existeContratoPolizaContratosCobranza = DB::select("SELECT id_contrato FROM polizacontratoscobranza
                            WHERE id_poliza = '$idPolizaConsulta' AND id_contrato = '" . $contrato->ID . "'");

                        if ($existeContratoPolizaContratosCobranza == null) {
                            //No existe el registro en la tabla polizacontratoscobranza
                            DB::table('polizacontratoscobranza')->insert([
                                'id_poliza' => $idPolizaConsulta, 'id_contrato' => $contrato->ID, 'created_at' => $hoy
                            ]);
                            $contadorAbonosPoliza++;
                        }

                    }
                }

                $tabular = $tabular + $contadorAbonosPoliza; //Sumar tabular, de contratos que no entraron en el tabular en la semana y que se cobraron

                //SECCION PARA OBTENER ARCHIVO
                //Tomamos el campo de archivo de la tabla de polizas de cobranza
                $archivoQuery = DB::select("SELECT archivo FROM polizacobranza WHERE id_poliza = '$idPoliza' AND id_franquicia = '$idFranquicia' AND id_usuario = '$idCobrador'");
                $archivo = $archivoQuery == null ? 0 : $archivoQuery[0]->archivo;

                //SECCION PARA AGREGAR GASTOS DE ABONOS QUE FUERON PAGADOS CON TARJETA O TRANSFERENCIA
                if ($sumaTotalAbonosPagadosTarjetas > 0) {
                    //Hubo abonos pagados con tarjeta
                    DB::table('gastos')->insert([
                        'id_poliza' => $idPoliza, 'descripcion' => "Pagos con tarjeta del cobrador $nombreCobrador de la zona $zonaCobrador",
                        'monto' => $sumaTotalAbonosPagadosTarjetas, 'tipogasto' => 5, 'created_at' => Carbon::now(), 'id_zona' => $idZonaCobrador
                    ]);
                    DB::table('historialpoliza')->insert([
                        'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                        'cambios' => "Agregó un gasto de cobranza con un monto de $" . $sumaTotalAbonosPagadosTarjetas
                            . ", con la descripción: Pagos con tarjeta del cobrador $nombreCobrador de la zona $zonaCobrador" . $mensajehistorialpoliza
                    ]);//Se agrega idUsuario de Sistema automatico
                }
                if ($sumaTotalAbonosPagadosTransferencia > 0) {
                    //Hubo contratos pagados con transferencia
                    DB::table('gastos')->insert([
                        'id_poliza' => $idPoliza, 'descripcion' => "Pagos con transferencia del cobrador $nombreCobrador de la zona $zonaCobrador",
                        'monto' => $sumaTotalAbonosPagadosTransferencia, 'tipogasto' => 6, 'created_at' => Carbon::now(), 'id_zona' => $idZonaCobrador
                    ]);
                    DB::table('historialpoliza')->insert([
                        'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                        'cambios' => "Agregó un gasto de cobranza con un monto de $" . $sumaTotalAbonosPagadosTransferencia
                            . ", con la descripción: Pagos con transferencia del cobrador $nombreCobrador de la zona $zonaCobrador" . $mensajehistorialpoliza
                    ]);//Se agrega idUsuario de Sistema automatico
                }

            }

            //Sacar suma total de abonos pagados por tarjeta y suma total pagados por transferencia de oficina y hacer sumar total para sumarlo con el ingresooficina
            $abonosOficinaQuery = DB::select("SELECT a.indice, a.id_contrato, a.metodopago, a.abono, a.tipoabono, u.name, a.id_contratoproducto, a.id_usuario, c.estatus_estadocontrato as ESTATUS,
                                                        c.ultimoabono as ULTIMOABONO FROM abonos a INNER JOIN users u INNER JOIN contratos c ON a.id_contrato = c.id
                                                        WHERE u.rol_id IN (6,7,8) AND a.id_usuario = u.id AND a.poliza = '$idPoliza' AND a.id_zona = '$idZonaCobrador'
                                                        GROUP BY a.indice, a.id_contrato, a.metodopago, a.abono, a.tipoabono, u.name, a.id_contratoproducto, a.id_usuario, c.estatus_estadocontrato,
                                                        c.ultimoabono");

            $sumaTotalAbonosPagadosTarjetasOficina = 0;
            $sumaTotalAbonosPagadosTransferenciaOficina = 0;
            $ingresoOficina = 0;
            foreach ($abonosOficinaQuery as $abonoOficina) {
                //Recorrido de abonos de oficina

                $existeIngresoOficinaTipo2 = DB::select("SELECT id FROM ingresosoficina WHERE id_poliza = '$idPoliza' AND indiceabono = '" . $abonoOficina->indice . "' AND tipo = '2'");

                if ($existeIngresoOficinaTipo2 == null) {
                    //No existe el ingreso oficina de tipo 2

                    $metodopagoletra = "";
                    if ($abonoOficina->metodopago != null) {
                        //Se tiene metodo de pago
                        switch ($abonoOficina->metodopago) {
                            case 0:
                                //Pagaron con EFECTIVO
                                $metodopagoletra = "en efectivo";
                                break;
                            case 1:
                                //Pagaron con TARJETA
                                $sumaTotalAbonosPagadosTarjetasOficina += $abonoOficina->abono;
                                $metodopagoletra = "por tarjeta";
                                break;
                            case 2:
                                //Pagaron con TRANSFERENCIA
                                $sumaTotalAbonosPagadosTransferenciaOficina += $abonoOficina->abono;
                                $metodopagoletra = "por transferencia";
                                break;
                            case 3:
                                //Pagaron con CANCELACION
                                $metodopagoletra = "de cancelación";
                                break;
                        }
                    }
                    $ingresoOficina += $abonoOficina->abono;
                    $tipoabonoletra = " de producto";
                    if ($abonoOficina->tipoabono != 7) {
                        //Tipo abono diferente de producto
                        $tipoabonoletra = "";

                        $existeEstatusEnviado = DB::select("SELECT id_contrato FROM registroestadocontrato
                            WHERE id_contrato = '" . $abonoOficina->id_contrato . "' AND estatuscontrato = '12' ORDER BY created_at DESC LIMIT 1");

                        if ($existeEstatusEnviado != null) {
                            //Existe estatus enviado en el contrato

                            $respuestaLiquidados = self::validarContratosLiquidados($abonoOficina->ESTATUS, $abonoOficina->ULTIMOABONO, $fechaLunesAntes); //Validar tarjetas pagadas esta semana
                            if ($respuestaLiquidados && !in_array($abonoOficina->id_contrato, $arrayTarjetasPagadas)) {
                                //Entro en tarjetas liquidadas y no se encuentra el id_contrato en el $arrayTarjetasPagadas
                                array_push($arrayTarjetasPagadas, $abonoOficina->id_contrato); //Agregar id_contrato al $arrayTarjetasPagadas
                            }
                            if (!in_array($abonoOficina->id_contrato, $arrayTarjetasCobradas)) {
                                //No se encuentra el id_contrato en el $arrayTarjetasCobradas
                                array_push($arrayTarjetasCobradas, $abonoOficina->id_contrato); //Agregar id_contrato al $arrayTarjetasCobradas

                                //Agregar nuevos registros a tabla polizacontratoscobranza en caso de que no exista
                                $idPolizaConsulta = $primerpoliza ? $idPoliza : $idPrimerPoliza;
                                $existeContratoPolizaContratosCobranza = DB::select("SELECT id_contrato FROM polizacontratoscobranza
                            WHERE id_poliza = '$idPolizaConsulta' AND id_contrato = '" . $abonoOficina->id_contrato . "'");

                                if ($existeContratoPolizaContratosCobranza == null) {
                                    //No existe el registro en la tabla polizacontratoscobranza
                                    DB::table('polizacontratoscobranza')->insert([
                                        'id_poliza' => $idPolizaConsulta, 'id_contrato' => $abonoOficina->id_contrato, 'created_at' => $hoy
                                    ]);
                                    $tabular = $tabular + 1;
                                }

                            }

                        }
                    }

                    $nombreproducto = "";
                    if (strlen($tipoabonoletra) > 0) {
                        //tipoabonoletra es diferente de vacio por lo tanto es un abono de producto
                        $producto = DB::select("SELECT p.nombre, p.color, p.id_tipoproducto, cp.piezas, p.polizagastosadministracion FROM contratoproducto cp INNER JOIN producto p ON p.id = cp.id_producto
                            WHERE cp.id = '" . $abonoOficina->id_contratoproducto . "' AND cp.id_contrato = '" . $abonoOficina->id_contrato . "'");

                        if ($producto != null) {
                            //Existe el contratoproducto y el producto
                            $nombreproducto = " de " . $producto[0]->nombre;
                            if ($producto[0]->id_tipoproducto == 1) {
                                //Armazon
                                $nombreproducto = " de armazón " . $producto[0]->nombre . "|" . $producto[0]->color;
                            }

                            $piezasproducto = $producto[0]->piezas == null ? 0 : $producto[0]->piezas;
                            $polizagastosadministracion = $producto[0]->polizagastosadministracion == null ? 0 : $producto[0]->polizagastosadministracion;
                            $polizagastosadministracionproducto = $piezasproducto * $polizagastosadministracion;

                            if ($polizagastosadministracion > 0) {
                                //Agregar gasto de producto
                                DB::table('gastos')->insert([
                                    'id_poliza' => $idPoliza, 'descripcion' => "Pago $nombreproducto para administración " . $abonoOficina->name . " por la cantidad de $piezasproducto piezas con total de $$polizagastosadministracionproducto",
                                    'monto' => $polizagastosadministracionproducto, 'tipogasto' => 7, 'created_at' => Carbon::now(), 'id_zona' => $idZonaCobrador
                                ]);
                                DB::table('historialpoliza')->insert([
                                    'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                                    'cambios' => "Agregó un gasto de administración con un monto de $" . $polizagastosadministracionproducto
                                        . ", con la descripción: Pago $nombreproducto para administración " . $abonoOficina->name . " por la cantidad de $piezasproducto piezas con total de $$polizagastosadministracionproducto" . $mensajehistorialpoliza
                                ]);//Se agrega idUsuario de Sistema automatico
                            }

                        }
                    }

                    $tipoabonoletra = $tipoabonoletra . $nombreproducto;

                    //Formacion de descripcion para el ingreso de oficina
                    $descripcioningresooficinacobranza = $abonoOficina->name . " abono$tipoabonoletra con un total de $" . $abonoOficina->abono . " $metodopagoletra al contrato " .
                        $abonoOficina->id_contrato;

                    //Insertar ingreso de oficina y movimiento en historialpoliza
                    DB::table('ingresosoficina')->insert([
                        'id_poliza' => $idPoliza, 'descripcion' => $descripcioningresooficinacobranza, 'id_zona' => $idZonaCobrador,
                        'monto' => $abonoOficina->abono, 'tipo' => 1, 'created_at' => Carbon::now()
                    ]);
                    DB::table('historialpoliza')->insert([
                        'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                        'cambios' => "Agregó un ingreso de oficina de administración de cobranza con la siguiente descripción: $descripcioningresooficinacobranza" . $mensajehistorialpoliza
                    ]);//Se agrega idUsuario de Sistema automatico

                }

            }

            if ($sumaTotalAbonosPagadosTarjetasOficina > 0) {
                //Hubo abonos pagados con tarjeta
                DB::table('gastos')->insert([
                    'id_poliza' => $idPoliza, 'descripcion' => "Pagos con tarjeta de administración de la zona $zonaCobrador",
                    'monto' => $sumaTotalAbonosPagadosTarjetasOficina, 'tipogasto' => 7, 'created_at' => Carbon::now(), 'id_zona' => $idZonaCobrador
                ]);
                DB::table('historialpoliza')->insert([
                    'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                    'cambios' => "Agregó un gasto de oficina con un monto de $" . $sumaTotalAbonosPagadosTarjetasOficina
                        . ", con la descripción: Pagos con tarjeta de administración de la zona $zonaCobrador" . $mensajehistorialpoliza
                ]);//Se agrega idUsuario de Sistema automatico
            }
            if ($sumaTotalAbonosPagadosTransferenciaOficina > 0) {
                //Hubo contratos pagados con transferencia
                DB::table('gastos')->insert([
                    'id_poliza' => $idPoliza, 'descripcion' => "Pagos con transferencia de administración de la zona $zonaCobrador",
                    'monto' => $sumaTotalAbonosPagadosTransferenciaOficina, 'tipogasto' => 8, 'created_at' => Carbon::now(), 'id_zona' => $idZonaCobrador
                ]);
                DB::table('historialpoliza')->insert([
                    'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                    'cambios' => "Agregó un gasto de oficina con un monto de $" . $sumaTotalAbonosPagadosTransferenciaOficina
                        . ", con la descripción: Pagos con transferencia de administración de la zona $zonaCobrador" . $mensajehistorialpoliza
                ]);//Se agrega idUsuario de Sistema automatico
            }

            //Sacar suma total de abonos pagados por tarjeta y suma total pagados por transferencia de supervisor cobranza y hacer sumar total para sumarlo con el ingresooficina
            $abonosSupervisoresCobranzaQuery = DB::select("SELECT a.id_contrato, a.metodopago, a.abono, a.tipoabono, u.name, a.id_contratoproducto, a.id_usuario, c.estatus_estadocontrato as ESTATUS,
                                                        c.ultimoabono as ULTIMOABONO FROM abonos a INNER JOIN users u INNER JOIN contratos c ON a.id_contrato = c.id
                                                        WHERE u.rol_id IN (4) AND u.supervisorcobranza = '1' AND a.id_usuario = u.id AND a.poliza = '$idPoliza' AND a.id_zona = '$idZonaCobrador'
                                                        GROUP BY a.id_contrato, a.metodopago, a.abono, a.tipoabono, u.name, a.id_contratoproducto, a.id_usuario, c.estatus_estadocontrato,
                                                        c.ultimoabono");

            $sumaTotalAbonosPagadosTarjetasSupervisoresCobranza = 0;
            $sumaTotalAbonosPagadosTransferenciaSupervisoresCobranza = 0;
            $ingresoSupervisor = 0;
            foreach ($abonosSupervisoresCobranzaQuery as $abonosSupervisorCobranza) {
                //Recorrido de abonos de oficina
                $metodopagoletra = "";
                if ($abonosSupervisorCobranza->metodopago != null) {
                    //Se tiene metodo de pago
                    switch ($abonosSupervisorCobranza->metodopago) {
                        case 0:
                            //Pagaron con EFECTIVO
                            $metodopagoletra = "en efectivo";
                            break;
                        case 1:
                            //Pagaron con TARJETA
                            $sumaTotalAbonosPagadosTarjetasSupervisoresCobranza += $abonosSupervisorCobranza->abono;
                            $metodopagoletra = "por tarjeta";
                            break;
                        case 2:
                            //Pagaron con TRANSFERENCIA
                            $sumaTotalAbonosPagadosTransferenciaSupervisoresCobranza += $abonosSupervisorCobranza->abono;
                            $metodopagoletra = "por transferencia";
                            break;
                        case 3:
                            //Pagaron con CANCELACION
                            $metodopagoletra = "de cancelación";
                            break;
                    }
                }
                $ingresoSupervisor += $abonosSupervisorCobranza->abono;
                $tipoabonoletra = " de producto";
                if ($abonosSupervisorCobranza->tipoabono != 7) {
                    //Tipo abono diferente de producto
                    $tipoabonoletra = "";
                    $respuestaLiquidados = self::validarContratosLiquidados($abonosSupervisorCobranza->ESTATUS, $abonosSupervisorCobranza->ULTIMOABONO, $fechaLunesAntes); //Validar tarjetas pagadas esta semana
                    if ($respuestaLiquidados && !in_array($abonosSupervisorCobranza->id_contrato, $arrayTarjetasPagadas)) {
                        //Entro en tarjetas liquidadas y no se encuentra el id_contrato en el $arrayTarjetasPagadas
                        array_push($arrayTarjetasPagadas, $abonosSupervisorCobranza->id_contrato); //Agregar id_contrato al $arrayTarjetasPagadas
                    }
                    if (!in_array($abonosSupervisorCobranza->id_contrato, $arrayTarjetasCobradas)) {
                        //No se encuentra el id_contrato en el $arrayTarjetasCobradas
                        array_push($arrayTarjetasCobradas, $abonosSupervisorCobranza->id_contrato); //Agregar id_contrato al $arrayTarjetasCobradas

                        //Agregar nuevos registros a tabla polizacontratoscobranza en caso de que no exista
                        $idPolizaConsulta = $primerpoliza ? $idPoliza : $idPrimerPoliza;
                        $existeContratoPolizaContratosCobranza = DB::select("SELECT id_contrato FROM polizacontratoscobranza
                            WHERE id_poliza = '$idPolizaConsulta' AND id_contrato = '" . $abonosSupervisorCobranza->id_contrato . "'");

                        if ($existeContratoPolizaContratosCobranza == null) {
                            //No existe el registro en la tabla polizacontratoscobranza
                            DB::table('polizacontratoscobranza')->insert([
                                'id_poliza' => $idPolizaConsulta, 'id_contrato' => $abonosSupervisorCobranza->id_contrato, 'created_at' => $hoy
                            ]);
                            $tabular = $tabular + 1;
                        }

                    }
                }

                $nombreproducto = "";
                if (strlen($tipoabonoletra) > 0) {
                    //tipoabonoletra es diferente de vacio por lo tanto es un abono de producto
                    $producto = DB::select("SELECT p.nombre, p.color, p.id_tipoproducto, cp.piezas, p.polizagastoscobranza FROM contratoproducto cp INNER JOIN producto p ON p.id = cp.id_producto
                            WHERE cp.id = '" . $abonosSupervisorCobranza->id_contratoproducto . "' AND cp.id_contrato = '" . $abonosSupervisorCobranza->id_contrato . "'");

                    if ($producto != null) {
                        //Existe el contratoproducto y el producto
                        $nombreproducto = " de " . $producto[0]->nombre;
                        if ($producto[0]->id_tipoproducto == 1) {
                            //Armazon
                            $nombreproducto = " de armazón " . $producto[0]->nombre . "|" . $producto[0]->color;
                        }

                        $piezasproducto = $producto[0]->piezas == null ? 0 : $producto[0]->piezas;
                        $polizagastoscobranza = $producto[0]->polizagastoscobranza == null ? 0 : $producto[0]->polizagastoscobranza;
                        $polizagastoscobranzaproducto = $piezasproducto * $polizagastoscobranza;

                        if($polizagastoscobranza > 0) {
                            //Agregar gasto de producto
                            DB::table('gastos')->insert([
                                'id_poliza' => $idPoliza, 'descripcion' => "Pago $nombreproducto para supervisor cobranza " . $abonosSupervisorCobranza->name . " por la cantidad de $piezasproducto piezas con total de $$polizagastoscobranzaproducto",
                                'monto' => $polizagastoscobranzaproducto, 'tipogasto' => 15, 'created_at' => Carbon::now(), 'id_zona' => $idZonaCobrador
                            ]);
                            DB::table('historialpoliza')->insert([
                                'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                                'cambios' => "Agregó un gasto de supervisor cobranza con un monto de $" . $polizagastoscobranzaproducto
                                    . ", con la descripción: Pago $nombreproducto para supervisor cobranza " . $abonosSupervisorCobranza->name . " por la cantidad de $piezasproducto piezas con total de $$polizagastoscobranzaproducto" . $mensajehistorialpoliza
                            ]);//Se agrega idUsuario de Sistema automatico
                        }
                    }
                }

                $tipoabonoletra = $tipoabonoletra . $nombreproducto;

                //Formacion de descripcion para el ingreso de oficina supervisor cobranza
                $descripcioningresooficinacobranza = $abonosSupervisorCobranza->name . " abono$tipoabonoletra con un total de $" . $abonosSupervisorCobranza->abono . " $metodopagoletra al contrato " .
                    $abonosSupervisorCobranza->id_contrato;

                //Insertar ingreso de oficina y movimiento en historialpoliza
                DB::table('ingresosoficina')->insert([
                    'id_poliza' => $idPoliza, 'descripcion' => $descripcioningresooficinacobranza, 'id_zona' => $idZonaCobrador,
                    'monto' => $abonosSupervisorCobranza->abono, 'tipo' => 1, 'created_at' => Carbon::now()
                ]);
                DB::table('historialpoliza')->insert([
                    'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                    'cambios' => "Agregó un ingreso de oficina de supervisión de cobranza con la siguiente descripción: $descripcioningresooficinacobranza" . $mensajehistorialpoliza
                ]);//Se agrega idUsuario de Sistema automatico
            }

            if ($sumaTotalAbonosPagadosTarjetasSupervisoresCobranza > 0) {
                //Hubo abonos pagados con tarjeta
                DB::table('gastos')->insert([
                    'id_poliza' => $idPoliza, 'descripcion' => "Pagos con tarjeta de supervisión de cobranza de la zona $zonaCobrador",
                    'monto' => $sumaTotalAbonosPagadosTarjetasSupervisoresCobranza, 'tipogasto' => 15, 'created_at' => Carbon::now(), 'id_zona' => $idZonaCobrador
                ]);
                DB::table('historialpoliza')->insert([
                    'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                    'cambios' => "Agregó un gasto de oficina con un monto de $" . $sumaTotalAbonosPagadosTarjetasSupervisoresCobranza
                        . ", con la descripción: Pagos con tarjeta de supervisión de cobranza de la zona $zonaCobrador" . $mensajehistorialpoliza
                ]);//Se agrega idUsuario de Sistema automatico
            }
            if ($sumaTotalAbonosPagadosTransferenciaSupervisoresCobranza > 0) {
                //Hubo contratos pagados con transferencia
                DB::table('gastos')->insert([
                    'id_poliza' => $idPoliza, 'descripcion' => "Pagos con transferencia de supervisión de cobranza de la zona $zonaCobrador",
                    'monto' => $sumaTotalAbonosPagadosTransferenciaSupervisoresCobranza, 'tipogasto' => 16, 'created_at' => Carbon::now(), 'id_zona' => $idZonaCobrador
                ]);
                DB::table('historialpoliza')->insert([
                    'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                    'cambios' => "Agregó un gasto de oficina con un monto de $" . $sumaTotalAbonosPagadosTransferenciaSupervisoresCobranza
                        . ", con la descripción: Pagos con transferencia de supervisión de cobranza de la zona $zonaCobrador" . $mensajehistorialpoliza
                ]);//Se agrega idUsuario de Sistema automatico
            }

            //Sacar suma total de abonos pagados por tarjeta y suma total pagados por transferencia de usuarios eliminados y hacer sumar total para sumarlo con el ingresooficina
            $abonosUsuariosEliminadosQuery = DB::select("SELECT a.indice, a.id_contrato, a.metodopago, a.abono, a.tipoabono, u.name, a.id_contratoproducto, a.id_usuario, c.estatus_estadocontrato as ESTATUS,
                                                        c.ultimoabono as ULTIMOABONO, u.rol_id as ROL_ID FROM abonos a INNER JOIN users u INNER JOIN contratos c ON a.id_contrato = c.id
                                                        WHERE a.id_usuario = u.id AND a.poliza IS NULL AND a.id_zona = '$idZonaCobrador' AND (u.rol_id NOT IN (4,12,13) OR (u.rol_id = '4' AND u.fechaeliminacion IS NOT NULL))
                                                        GROUP BY a.indice, a.id_contrato, a.metodopago, a.abono, a.tipoabono, u.name, a.id_contratoproducto, a.id_usuario, c.estatus_estadocontrato,
                                                        c.ultimoabono, u.rol_id");

            $sumaTotalAbonosPagadosTarjetasUsuariosEliminados = 0;
            $sumaTotalAbonosPagadosTransferenciaUsuariosEliminados = 0;
            $ingresoUsuariosEliminados = 0;
            foreach ($abonosUsuariosEliminadosQuery as $abonoUsuarioEliminado) {
                //Recorrido de abonos de oficina
                $metodopagoletra = "";
                if ($abonoUsuarioEliminado->metodopago != null) {
                    //Se tiene metodo de pago
                    switch ($abonoUsuarioEliminado->metodopago) {
                        case 0:
                            //Pagaron con EFECTIVO
                            $metodopagoletra = "en efectivo";
                            break;
                        case 1:
                            //Pagaron con TARJETA
                            $sumaTotalAbonosPagadosTarjetasUsuariosEliminados += $abonoUsuarioEliminado->abono;
                            $metodopagoletra = "por tarjeta";
                            break;
                        case 2:
                            //Pagaron con TRANSFERENCIA
                            $sumaTotalAbonosPagadosTransferenciaUsuariosEliminados += $abonoUsuarioEliminado->abono;
                            $metodopagoletra = "por transferencia";
                            break;
                        case 3:
                            //Pagaron con CANCELACION
                            $metodopagoletra = "de cancelación";
                            break;
                    }
                }
                $ingresoUsuariosEliminados += $abonoUsuarioEliminado->abono;
                $tipoabonoletra = " de producto";
                if ($abonoUsuarioEliminado->tipoabono != 7) {
                    //Tipo abono diferente de producto
                    $tipoabonoletra = "";

                    $existeEstatusEnviado = DB::select("SELECT id_contrato FROM registroestadocontrato
                            WHERE id_contrato = '" . $abonoUsuarioEliminado->id_contrato . "' AND estatuscontrato = '12' ORDER BY created_at DESC LIMIT 1");

                    if ($existeEstatusEnviado != null) {
                        //Existe estatus enviado en el contrato

                        $respuestaLiquidados = self::validarContratosLiquidados($abonoUsuarioEliminado->ESTATUS, $abonoUsuarioEliminado->ULTIMOABONO, $fechaLunesAntes); //Validar tarjetas pagadas esta semana
                        if ($respuestaLiquidados && !in_array($abonoUsuarioEliminado->id_contrato, $arrayTarjetasPagadas)) {
                            //Entro en tarjetas liquidadas y no se encuentra el id_contrato en el $arrayTarjetasPagadas
                            array_push($arrayTarjetasPagadas, $abonoUsuarioEliminado->id_contrato); //Agregar id_contrato al $arrayTarjetasPagadas
                        }
                        if (!in_array($abonoUsuarioEliminado->id_contrato, $arrayTarjetasCobradas)) {
                            //No se encuentra el id_contrato en el $arrayTarjetasCobradas
                            array_push($arrayTarjetasCobradas, $abonoUsuarioEliminado->id_contrato); //Agregar id_contrato al $arrayTarjetasCobradas

                            //Agregar nuevos registros a tabla polizacontratoscobranza en caso de que no exista
                            $idPolizaConsulta = $primerpoliza ? $idPoliza : $idPrimerPoliza;
                            $existeContratoPolizaContratosCobranza = DB::select("SELECT id_contrato FROM polizacontratoscobranza
                            WHERE id_poliza = '$idPolizaConsulta' AND id_contrato = '" . $abonoUsuarioEliminado->id_contrato . "'");

                            if ($existeContratoPolizaContratosCobranza == null) {
                                //No existe el registro en la tabla polizacontratoscobranza
                                DB::table('polizacontratoscobranza')->insert([
                                    'id_poliza' => $idPolizaConsulta, 'id_contrato' => $abonoUsuarioEliminado->id_contrato, 'created_at' => $hoy
                                ]);
                                $tabular = $tabular + 1;
                            }

                        }

                    }
                }

                $nombreproducto = "";
                if (strlen($tipoabonoletra) > 0) {
                    //tipoabonoletra es diferente de vacio por lo tanto es un abono de producto
                    $producto = DB::select("SELECT p.nombre, p.color, p.id_tipoproducto, cp.piezas, p.polizagastosadministracion, p.polizagastos, p.polizagastoscobranza FROM contratoproducto cp INNER JOIN producto p ON p.id = cp.id_producto
                            WHERE cp.id = '" . $abonoUsuarioEliminado->id_contratoproducto . "' AND cp.id_contrato = '" . $abonoUsuarioEliminado->id_contrato . "'");

                    if ($producto != null) {
                        //Existe el contratoproducto y el producto
                        $nombreproducto = " de " . $producto[0]->nombre;
                        if ($producto[0]->id_tipoproducto == 1) {
                            //Armazon
                            $nombreproducto = " de armazón " . $producto[0]->nombre . "|" . $producto[0]->color;
                        }

                        $piezasproducto = $producto[0]->piezas == null ? 0 : $producto[0]->piezas;
                        $polizagastosadministracion = $producto[0]->polizagastosadministracion == null ? 0 : $producto[0]->polizagastosadministracion;
                        if ($abonoUsuarioEliminado->ROL_ID == 4) {
                            $polizagastosadministracion = $producto[0]->polizagastoscobranza == null ? 0 : $producto[0]->polizagastoscobranza;
                        }elseif ($abonoUsuarioEliminado->ROL_ID == 12 || $abonoUsuarioEliminado->ROL_ID == 13){
                            $polizagastosadministracion = $producto[0]->polizagastos == null ? 0 : $producto[0]->polizagastos;
                        }
                        $polizagastosadministracionproducto = $piezasproducto * $polizagastosadministracion;

                        if($polizagastosadministracion > 0) {
                            //Agregar gasto de producto
                            DB::table('gastos')->insert([
                                'id_poliza' => $idPoliza, 'descripcion' => "Pago $nombreproducto para administración " . $abonoUsuarioEliminado->name . " por la cantidad de $piezasproducto piezas con total de $$polizagastosadministracionproducto",
                                'monto' => $polizagastosadministracionproducto, 'tipogasto' => 17, 'created_at' => Carbon::now(), 'id_usuario' => $idCobrador
                            ]);
                            DB::table('historialpoliza')->insert([
                                'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                                'cambios' => "Agregó un gasto de administración con un monto de $" . $polizagastosadministracionproducto
                                    . ", con la descripción: Pago $nombreproducto para administración " . $abonoUsuarioEliminado->name . " por la cantidad de $piezasproducto piezas con total de $$polizagastosadministracionproducto" . $mensajehistorialpoliza
                            ]);//Se agrega idUsuario de Sistema automatico
                        }

                    }
                }

                $tipoabonoletra = $tipoabonoletra . $nombreproducto;

                //Formacion de descripcion para el ingreso de oficina
                $descripcioningresooficinacobranza = $abonoUsuarioEliminado->name . " abono$tipoabonoletra con un total de $" . $abonoUsuarioEliminado->abono . " $metodopagoletra al contrato " .
                    $abonoUsuarioEliminado->id_contrato;

                //Insertar ingreso de oficina y movimiento en historialpoliza
                DB::table('ingresosoficina')->insert([
                    'id_poliza' => $idPoliza, 'descripcion' => $descripcioningresooficinacobranza, 'id_usuario' => $idCobrador,
                    'monto' => $abonoUsuarioEliminado->abono, 'tipo' => 2, 'indiceabono' => $abonoUsuarioEliminado->indice, 'created_at' => Carbon::now()
                ]);
                DB::table('historialpoliza')->insert([
                    'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                    'cambios' => "Agregó un ingreso de oficina de administración de cobranza con la siguiente descripción: $descripcioningresooficinacobranza" . $mensajehistorialpoliza
                ]);//Se agrega idUsuario de Sistema automatico

                //Actualizar abono a la poliza actual
                DB::update("UPDATE abonos SET poliza = '$idPoliza' WHERE indice = '" . $abonoUsuarioEliminado->indice . "'");

            }

            if ($sumaTotalAbonosPagadosTarjetasUsuariosEliminados > 0) {
                //Hubo abonos pagados con tarjeta
                DB::table('gastos')->insert([
                    'id_poliza' => $idPoliza, 'descripcion' => "Pagos con tarjeta de administración de la zona $zonaCobrador",
                    'monto' => $sumaTotalAbonosPagadosTarjetasUsuariosEliminados, 'tipogasto' => 17, 'created_at' => Carbon::now(), 'id_usuario' => $idCobrador
                ]);
                DB::table('historialpoliza')->insert([
                    'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                    'cambios' => "Agregó un gasto de oficina con un monto de $" . $sumaTotalAbonosPagadosTarjetasUsuariosEliminados
                        . ", con la descripción: Pagos con tarjeta de administración de la zona $zonaCobrador" . $mensajehistorialpoliza
                ]);//Se agrega idUsuario de Sistema automatico
            }
            if ($sumaTotalAbonosPagadosTransferenciaUsuariosEliminados > 0) {
                //Hubo contratos pagados con transferencia
                DB::table('gastos')->insert([
                    'id_poliza' => $idPoliza, 'descripcion' => "Pagos con transferencia de administración de la zona $zonaCobrador",
                    'monto' => $sumaTotalAbonosPagadosTransferenciaUsuariosEliminados, 'tipogasto' => 18, 'created_at' => Carbon::now(), 'id_usuario' => $idCobrador
                ]);
                DB::table('historialpoliza')->insert([
                    'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                    'cambios' => "Agregó un gasto de oficina con un monto de $" . $sumaTotalAbonosPagadosTransferenciaUsuariosEliminados
                        . ", con la descripción: Pagos con transferencia de administración de la zona $zonaCobrador" . $mensajehistorialpoliza
                ]);//Se agrega idUsuario de Sistema automatico
            }

            //VENTA DE PRODUCTOS COBRADOR
            if (strlen($idCobrador) > 0) {
                //Existe cobrador en la zona
                //AGREGAR GASTOS DE POLIZAS DE SEGURO, VITAMINAS, GOTAS Y ARMAZONES
                $resultadoQueryProductosCobranza = DB::select("SELECT COALESCE((SELECT SUM(p.polizagastoscobranza)
                                FROM abonos a INNER JOIN contratoproducto cp ON a.id_contrato = cp.id_contrato
                                INNER JOIN producto p ON cp.id_producto = p.id WHERE cp.id_usuario = u.id AND p.id_tipoproducto = 1 AND a.id_contratoproducto = cp.id
                                AND a.poliza = '$idPoliza'),0) as polizagastosarmazones,
                            COALESCE((SELECT SUM(cp.piezas)
                                FROM abonos a INNER JOIN contratoproducto cp ON a.id_contrato = cp.id_contrato
                                INNER JOIN producto p ON cp.id_producto = p.id WHERE cp.id_usuario = u.id AND p.id_tipoproducto = 1 AND a.id_contratoproducto = cp.id
                                AND a.poliza = '$idPoliza'),0) as piezasarmazones,
                            COALESCE((SELECT SUM(p.polizagastoscobranza)
                                FROM abonos a INNER JOIN contratoproducto cp ON a.id_contrato = cp.id_contrato
                                INNER JOIN producto p ON cp.id_producto = p.id WHERE cp.id_usuario = u.id AND p.id_tipoproducto = 2 AND a.id_contratoproducto = cp.id
                                AND a.poliza = '$idPoliza'),0) as polizagastospolizas,
                            COALESCE((SELECT SUM(cp.piezas)
                                FROM abonos a INNER JOIN contratoproducto cp ON a.id_contrato = cp.id_contrato
                                INNER JOIN producto p ON cp.id_producto = p.id WHERE cp.id_usuario = u.id AND p.id_tipoproducto = 2 AND a.id_contratoproducto = cp.id
                                AND a.poliza = '$idPoliza'),0) as piezaspolizas,
                            COALESCE((SELECT SUM(p.polizagastoscobranza)
                                FROM abonos a INNER JOIN contratoproducto cp ON a.id_contrato = cp.id_contrato
                                INNER JOIN producto p ON cp.id_producto = p.id WHERE cp.id_usuario = u.id AND p.id_tipoproducto = 3 AND a.id_contratoproducto = cp.id
                                AND a.poliza = '$idPoliza'),0) as polizagastosgotas,
                            COALESCE((SELECT SUM(cp.piezas)
                                FROM abonos a INNER JOIN contratoproducto cp ON a.id_contrato = cp.id_contrato
                                INNER JOIN producto p ON cp.id_producto = p.id WHERE cp.id_usuario = u.id AND p.id_tipoproducto = 3 AND a.id_contratoproducto = cp.id
                                AND a.poliza = '$idPoliza'),0) as piezasgotas,
                            COALESCE((SELECT SUM(p.polizagastoscobranza)
                                FROM abonos a INNER JOIN contratoproducto cp ON a.id_contrato = cp.id_contrato
                                INNER JOIN producto p ON cp.id_producto = p.id WHERE cp.id_usuario = u.id AND p.id_tipoproducto = 4 AND a.id_contratoproducto = cp.id
                                AND a.poliza = '$idPoliza'),0) as polizagastosvitaminas,
                            COALESCE((SELECT SUM(cp.piezas)
                                FROM abonos a INNER JOIN contratoproducto cp ON a.id_contrato = cp.id_contrato
                                INNER JOIN producto p ON cp.id_producto = p.id WHERE cp.id_usuario = u.id AND p.id_tipoproducto = 4 AND a.id_contratoproducto = cp.id
                                AND a.poliza = '$idPoliza'),0) as piezasvitaminas
                            FROM users u WHERE u.id = '$idCobrador'");

                if ($resultadoQueryProductosCobranza != null) {
                    //Existen abonos de productos de cobradores

                    $polizagastosarmazones = $resultadoQueryProductosCobranza[0]->polizagastosarmazones;
                    $piezasarmazones = $resultadoQueryProductosCobranza[0]->piezasarmazones;
                    if ($polizagastosarmazones > 0 && $piezasarmazones > 0) {
                        //Se vendieron gotas
                        DB::table('gastos')->insert([
                            'id_poliza' => $idPoliza, 'descripcion' => "Pago armazones para cobrador $nombreCobrador por la cantidad de $piezasarmazones piezas con total de $$polizagastosarmazones",
                            'monto' => $polizagastosarmazones, 'tipogasto' => 11, 'created_at' => Carbon::now(), 'id_zona' => $idZonaCobrador
                        ]);
                        DB::table('historialpoliza')->insert([
                            'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                            'cambios' => "Agregó un gasto de cobranza con un monto de $" . $polizagastosarmazones
                                . ", con la descripción: Pago armazones para cobrador $nombreCobrador por la cantidad de $piezasarmazones piezas con total de $$polizagastosarmazones"
                        ]);//Se agrega idUsuario de Sistema automatico
                    }
                    $polizagastospolizas = $resultadoQueryProductosCobranza[0]->polizagastospolizas;
                    $piezaspolizas = $resultadoQueryProductosCobranza[0]->piezaspolizas;
                    if ($polizagastospolizas > 0 && $piezaspolizas > 0) {
                        //Se vendieron gotas
                        DB::table('gastos')->insert([
                            'id_poliza' => $idPoliza, 'descripcion' => "Pago polizas para cobrador $nombreCobrador por la cantidad de $piezaspolizas piezas con total de $$polizagastospolizas",
                            'monto' => $polizagastospolizas, 'tipogasto' => 12, 'created_at' => Carbon::now(), 'id_zona' => $idZonaCobrador
                        ]);
                        DB::table('historialpoliza')->insert([
                            'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                            'cambios' => "Agregó un gasto de cobranza con un monto de $" . $polizagastospolizas
                                . ", con la descripción: Pago polizas para cobrador $nombreCobrador por la cantidad de $piezaspolizas piezas con total de $$polizagastospolizas" . $mensajehistorialpoliza
                        ]);//Se agrega idUsuario de Sistema automatico
                    }
                    $polizagastosgotas = $resultadoQueryProductosCobranza[0]->polizagastosgotas;
                    $piezasgotas = $resultadoQueryProductosCobranza[0]->piezasgotas;
                    if ($polizagastosgotas > 0 && $piezasgotas > 0) {
                        //Se vendieron gotas
                        DB::table('gastos')->insert([
                            'id_poliza' => $idPoliza, 'descripcion' => "Pago gotas para cobrador $nombreCobrador por la cantidad de $piezasgotas piezas con total de $$polizagastosgotas",
                            'monto' => $polizagastosgotas, 'tipogasto' => 13, 'created_at' => Carbon::now(), 'id_zona' => $idZonaCobrador
                        ]);
                        DB::table('historialpoliza')->insert([
                            'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                            'cambios' => "Agregó un gasto de cobranza con un monto de $" . $polizagastosgotas
                                . ", con la descripción: Pago gotas para cobrador $nombreCobrador por la cantidad de $piezasgotas piezas con total de $$polizagastosgotas" . $mensajehistorialpoliza
                        ]);//Se agrega idUsuario de Sistema automatico
                    }
                    $polizagastosvitaminas = $resultadoQueryProductosCobranza[0]->polizagastosvitaminas;
                    $piezasvitaminas = $resultadoQueryProductosCobranza[0]->piezasvitaminas;
                    if ($polizagastosvitaminas > 0 && $piezasvitaminas > 0) {
                        //Se vendieron gotas
                        DB::table('gastos')->insert([
                            'id_poliza' => $idPoliza, 'descripcion' => "Pago vitaminas para cobrador $nombreCobrador por la cantidad de $piezasvitaminas piezas con total de $$polizagastosvitaminas",
                            'monto' => $polizagastosvitaminas, 'tipogasto' => 14, 'created_at' => Carbon::now(), 'id_zona' => $idZonaCobrador
                        ]);
                        DB::table('historialpoliza')->insert([
                            'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                            'cambios' => "Agregó un gasto de cobranza con un monto de $" . $polizagastosvitaminas
                                . ", con la descripción: Pago vitaminas para cobrador $nombreCobrador por la cantidad de $piezasvitaminas piezas con total de $$polizagastosvitaminas" . $mensajehistorialpoliza
                        ]);//Se agrega idUsuario de Sistema automatico
                    }
                }
            }

            //ESTA SECCION ES PARA CALCULAR LAS TARJETAS ACUMULADAS Y PROMEDIO DE ABONOS
            $tarjetaAcumuladaSemana = count($arrayTarjetasCobradas);

            //SECCION PARA CALCULAR LO QUE SE COBRO EN EL DIA
            $ingresoCobranzaQuery = null;
            if (strlen($idCobrador) > 0) {
                //Existe cobrador en la zona
                $ingresoCobranzaQuery = DB::select("SELECT COALESCE(SUM(a.abono),0) as ABONO FROM abonos a WHERE a.id_usuario = '$idCobrador' AND a.poliza = '$idPoliza'");
            }
            $ingresoCobranza = $ingresoCobranzaQuery == null ? 0 : $ingresoCobranzaQuery[0]->ABONO;
            $ingresoAcumulado = $ingresoCobranza + $ingresoOficina + $ingresoSupervisor + $ingresoUsuariosEliminados; //Lo igualamos al ingreso cobranza mas el ingreso oficina
            if ($hoyNumero != 2) {
                //Otro dia que no sea martes
                if ($ultimaPolizaId != null) {
                    $polizaCobranza = null;
                    if (strlen($idCobrador) > 0) {
                        //Existe cobrador en la zona
                        $polizaCobranza = DB::select("SELECT acumuladasemana as ACUMULADA,ingresoacumulado as INGRESOACUMULADO FROM polizacobranza
                                                            WHERE id_poliza = '$ultimaPolizaId' AND id_usuario = '$idCobrador'");
                    }
                    $acumulada = $polizaCobranza == null ? 0 :  $polizaCobranza[0]->ACUMULADA;
                    $tarjetaAcumuladaSemana = $tarjetaAcumuladaSemana + $acumulada;
                    //SECCION PARA CALCULAR EL ACUMULADO DE ABONO EN $$
                    $ingresoAcumuladoAnterior = $polizaCobranza == null ? 0 : $polizaCobranza[0]->INGRESOACUMULADO;
                    $ingresoAcumulado = $ingresoAcumulado + $ingresoAcumuladoAnterior;
                }
            }

            //SECCION PARA CALCULAR EL DIARIO ACUMULADO
            if($tabular > 0){
                $diarioAcumulado = ($tarjetaAcumuladaSemana * 100) / $tabular;
            }else{
                $diarioAcumulado = 0;
            }

            //SECCION PARA SABER SI EL COBRADOR RECIBIO PARA LA GAS
            $existeGastoGasCobrador = null;
            if (strlen($idCobrador) > 0) {
                //Existe cobrador en la zona
                $existeGastoGasCobrador = DB::select("SELECT gas FROM polizacobranza WHERE id_poliza = '$idPoliza' AND id_usuario = '$idCobrador'");
            }
            $gas = $existeGastoGasCobrador == null ? 0 : $existeGastoGasCobrador[0]->gas;

            $tarjetasAlSetentaYCinco = $tabular * 0.75;
            $tarjetasPorCobrarAlSetentaYCinco = $tarjetasAlSetentaYCinco - $tarjetaAcumuladaSemana;
            $dineroPorCobrarAlSetentaYCinco = $tarjetasPorCobrarAlSetentaYCinco * 150;
            $tarjetasAlOchenta = $tabular * 0.80;
            $tarjetasPorCobrarAlOchenta = $tarjetasAlOchenta - $tarjetaAcumuladaSemana;
            $dineroPorCobrarAlOchenta = $tarjetasPorCobrarAlOchenta * 150;


            $total = $sueldoCobrador;
            if ($tarjetasPorCobrarAlSetentaYCinco <= 0) {
                $cumplioSetentaYCinco = $ingresoAcumulado * 0.06;
                $total += $cumplioSetentaYCinco;
            } elseif ($tarjetasPorCobrarAlOchenta <= 0) {
                $cumplioSetentaYCinco = 0;
                $cumplioOchenta = $ingresoAcumulado * 0.08;
                $total += $cumplioOchenta;
            }

            if(strlen($idUsuario) > 0) {
                //idUsuario es diferente de vacio

                DB::table("polizacobranza")
                    ->where("id_usuario", "=", $idCobrador)
                    ->where("id_franquicia", "=", $idFranquicia)
                    ->where("id_poliza", "=", $idPoliza)
                    ->update([
                        "fechapoliza" => $hoy,
                        "nombre" => $nombreCobrador,
                        "zona" => $zonaCobrador,
                        "tabular" => $tabular,
                        "archivo" => $archivo,
                        "pagadas" => count($arrayTarjetasPagadas),
                        "cobradas" => count($arrayTarjetasCobradas),
                        "acumuladasemana" => $tarjetaAcumuladaSemana,
                        "diarioacumulado" => $diarioAcumulado,
                        "gas" => $gas,
                        "ingresocobranza" => $ingresoCobranza,
                        "ingresooficina" => $ingresoOficina,
                        "ingresosupervisor" => $ingresoSupervisor,
                        "ingresousuarioseliminados" => $ingresoUsuariosEliminados,
                        "ingresoacumulado" => $ingresoAcumulado,
                        "sueldo" => $sueldoCobrador,
                        "totalpagar" => $total,
                        "id_zona" => $idZonaCobrador
                ]);

            }else {
                //idUsuario es vacio

                DB::table("polizacobranza")->insert([
                    "id_usuario" => $idCobrador,
                    "id_franquicia" => $idFranquicia,
                    "id_poliza" => $idPoliza,
                    "fechapoliza" => $hoy,
                    "nombre" => $nombreCobrador,
                    "zona" => $zonaCobrador,
                    "tabular" => $tabular,
                    "archivo" => $archivo,
                    "pagadas" => count($arrayTarjetasPagadas),
                    "cobradas" => count($arrayTarjetasCobradas),
                    "acumuladasemana" => $tarjetaAcumuladaSemana,
                    "diarioacumulado" => $diarioAcumulado,
                    "gas" => $gas,
                    "ingresocobranza" => $ingresoCobranza,
                    "ingresooficina" => $ingresoOficina,
                    "ingresosupervisor" => $ingresoSupervisor,
                    "ingresousuarioseliminados" => $ingresoUsuariosEliminados,
                    "ingresoacumulado" => $ingresoAcumulado,
                    "sueldo" => $sueldoCobrador,
                    "totalpagar" => $total,
                    "id_zona" => $idZonaCobrador
                ]);

            }

        }

    }

    private static function validarUltimasTarjetasCobradas($idPoliza, $idCobrador, $idContrato, $sumaTotalAbonosPagadosTarjetas, $sumaTotalAbonosPagadosTransferencia)
    {

        $respuesta = array();
        $respuestaBoleana = false;
        $abonos = DB::select("SELECT metodopago, abono FROM abonos
                         WHERE id_contrato = '$idContrato' AND poliza = '$idPoliza' AND id_usuario = '$idCobrador' AND tipoabono != 7"); //Obtenemos los abonos del contrato
        if ($abonos != null) { //Hay abonos??
            //Si hay abonos
            foreach ($abonos as $abono) {
                if ($abono->metodopago != null) {
                    switch ($abono->metodopago) {
                        case 1:
                            //Pagaron con TARJETA
                            $sumaTotalAbonosPagadosTarjetas += $abono->abono;
                            break;
                        case 2:
                            //Pagaron con TRANSFERENCIA
                            $sumaTotalAbonosPagadosTransferencia += $abono->abono;
                            break;
                    }
                }
            }
            $respuestaBoleana = true;
        }
        //No hay abonos
        array_push($respuesta, $respuestaBoleana);
        array_push($respuesta, $sumaTotalAbonosPagadosTarjetas);
        array_push($respuesta, $sumaTotalAbonosPagadosTransferencia);

        return $respuesta;

    }

    private static function validarContratosLiquidados($estatus, $ultimoAbono, $fechaLunesAntes)
    {
        $respuesta = false;

        if ($estatus == 5) { //Es un contrato pagado?
            //Si esta pagado
            $ultimoAbono = Carbon::parse($ultimoAbono);
            if ($ultimoAbono->gte($fechaLunesAntes)) { //La fecha del abono es mayor o igual al dia lunes?
                $respuesta = true;
            }
        }

        return $respuesta;
    }

    private static function validarContratoTabular($tabular, $idPoliza, $idContrato, $estadoContrato, $ultimoAbono, $pago, $fechaEntregaContrato, $fechaEntregaHistorial,
                                                   $fechaCobroInicial, $fechaCobroFinal, $estadoGarantia, $fechaLunesAntes, $fechaLunesSiguiente, $hoy)
    {

        if($estadoGarantia != null) { //Tiene garantia?
            //Tiene garantia
            switch ($estadoGarantia) {
                case 1:
                    //Estado garantia ASIGNADA
                    if($estadoContrato == 2 || $estadoContrato == 4 || $estadoContrato == 12 || $estadoContrato == 5) {
                        //estadoContrato ENTREGADO, ABONO ATRASADO, ENVIADO o LIQUIDADO
                        DB::table('polizacontratoscobranza')->insert([
                            'id_poliza' => $idPoliza, 'id_contrato' => $idContrato, 'created_at' => $hoy
                        ]);
                        return $tabular + 1;
                    }
                    break;
                case 2:
                    //Estado garantia es CREADA
                    if($estadoContrato == 1 || $estadoContrato == 7 || $estadoContrato == 9 || $estadoContrato == 10 || $estadoContrato == 11) {
                        //estadoContrato TERMINADO, APROBADO, EN PROCESO DE APROBACION, MANUFACTURA, EN PROCESO DE ENVIO
                        DB::table('polizacontratoscobranza')->insert([
                            'id_poliza' => $idPoliza, 'id_contrato' => $idContrato, 'created_at' => $hoy
                        ]);
                        return $tabular + 1;
                    }
                    break;
            }
        }else {
            //No tiene garantia

            if(($estadoContrato == 7 || $estadoContrato == 10 || $estadoContrato == 11)
                && (Carbon::parse($fechaEntregaHistorial)->format('Y-m-d') >= Carbon::parse($fechaLunesAntes)->format('Y-m-d')
                    && Carbon::parse($fechaEntregaHistorial)->format('Y-m-d') < Carbon::parse($fechaLunesSiguiente)->format('Y-m-d'))
                || ($estadoContrato == 12)) {
                //estadoContrato APROBADO, EN PROCESO DE APROBACION, MANUFACTURA, EN PROCESO DE ENVIO, ENVIADO && fechadeentregahistorialcaeensabadoanterioraviernessiguiente
                DB::table('polizacontratoscobranza')->insert([
                    'id_poliza' => $idPoliza, 'id_contrato' => $idContrato, 'created_at' => $hoy
                ]);
                return $tabular + 1;
            }elseif($estadoContrato == 5) { //estadoContrato LIQUIDADO?
                //estadoContrato LIQUIDADO
                if((Carbon::parse($ultimoAbono)->format('Y-m-d') >= Carbon::parse($fechaLunesAntes)->format('Y-m-d')
                    && Carbon::parse($ultimoAbono)->format('Y-m-d') < Carbon::parse($fechaLunesSiguiente)->format('Y-m-d'))) {
                    //tieneabonoensabadoanterioraviernessiguiente
                    DB::table('polizacontratoscobranza')->insert([
                        'id_poliza' => $idPoliza, 'id_contrato' => $idContrato, 'created_at' => $hoy
                    ]);
                    return $tabular + 1;
                }
            }elseif($estadoContrato == 2 || $estadoContrato == 4) {
                //estadoContrato ENTREGADO o ABONO ATRASADO

                if($estadoContrato == 2 &&
                    (Carbon::parse($fechaEntregaContrato)->format('Y-m-d') >= Carbon::parse($fechaLunesAntes)->format('Y-m-d')
                        && Carbon::parse($fechaEntregaContrato)->format('Y-m-d') < Carbon::parse($fechaLunesSiguiente)->format('Y-m-d'))) {
                    //estadoContrato ENTREGADO && fechaentregacontratocaeensabadoanterioraviernessiguiente
                    DB::table('polizacontratoscobranza')->insert([
                        'id_poliza' => $idPoliza, 'id_contrato' => $idContrato, 'created_at' => $hoy
                    ]);
                    return $tabular + 1;
                }else {

                    if($pago == 1) {
                        //SEMANAL
                        if((((Carbon::parse($fechaCobroInicial)->format('Y-m-d') >= Carbon::parse($fechaLunesAntes)->format('Y-m-d')
                                        && Carbon::parse($fechaCobroInicial)->format('Y-m-d') < Carbon::parse($fechaLunesSiguiente)->format('Y-m-d'))
                                    || (Carbon::parse($fechaCobroFinal)->format('Y-m-d') >= Carbon::parse($fechaLunesAntes)->format('Y-m-d')
                                        && Carbon::parse($fechaCobroFinal)->format('Y-m-d') < Carbon::parse($fechaLunesSiguiente)->format('Y-m-d')))
                                && Carbon::parse($ultimoAbono)->format('Y-m-d') <= Carbon::parse($fechaLunesAntes)->format('Y-m-d'))
                            || (Carbon::parse($ultimoAbono)->format('Y-m-d') >= Carbon::parse($fechaLunesAntes)->format('Y-m-d')
                                && Carbon::parse($ultimoAbono)->format('Y-m-d') < Carbon::parse($fechaLunesSiguiente)->format('Y-m-d'))
                            || ($ultimoAbono == null)
                            || (Carbon::parse($hoy)->format('Y-m-d') >= Carbon::parse($fechaCobroInicial)->format('Y-m-d')
                                && Carbon::parse($hoy)->format('Y-m-d') <= Carbon::parse($fechaCobroFinal)->format('Y-m-d')
                                && Carbon::parse($ultimoAbono)->format('Y-m-d') < Carbon::parse($fechaCobroInicial)->format('Y-m-d'))) {
                            //fechaini y fechafin caen en la semana actual (sabadoanterioraviernessiguiente) && notenganabonoensabadoanterioraviernessiguiente
                            DB::table('polizacontratoscobranza')->insert([
                                'id_poliza' => $idPoliza, 'id_contrato' => $idContrato, 'created_at' => $hoy
                            ]);
                            return $tabular + 1;
                        }
                    }else {
                        //QUINCENAL O MENSUAL
                        if((((Carbon::parse($fechaCobroInicial)->format('Y-m-d') >= Carbon::parse($fechaLunesAntes)->format('Y-m-d')
                                        && Carbon::parse($fechaCobroInicial)->format('Y-m-d') < Carbon::parse($fechaLunesSiguiente)->format('Y-m-d'))
                                    || (Carbon::parse($fechaCobroFinal)->format('Y-m-d') >= Carbon::parse($fechaLunesAntes)->format('Y-m-d')
                                        && Carbon::parse($fechaCobroFinal)->format('Y-m-d') < Carbon::parse($fechaLunesSiguiente)->format('Y-m-d')))
                                && Carbon::parse($ultimoAbono)->format('Y-m-d') <= Carbon::parse($fechaCobroInicial)->format('Y-m-d'))
                            || (Carbon::parse($ultimoAbono)->format('Y-m-d') >= Carbon::parse($fechaLunesAntes)->format('Y-m-d')
                                && Carbon::parse($ultimoAbono)->format('Y-m-d') < Carbon::parse($fechaLunesSiguiente)->format('Y-m-d'))
                            || ($ultimoAbono == null)
                            || (Carbon::parse($hoy)->format('Y-m-d') >= Carbon::parse($fechaCobroInicial)->format('Y-m-d')
                                && Carbon::parse($hoy)->format('Y-m-d') <= Carbon::parse($fechaCobroFinal)->format('Y-m-d')
                                && Carbon::parse($ultimoAbono)->format('Y-m-d') < Carbon::parse($fechaCobroInicial)->format('Y-m-d'))) {
                            //fechaini y fechafin caen en la semana actual (sabadoanterioraviernessiguiente) && notenganabonoenperiodofechainiyfechafin
                            DB::table('polizacontratoscobranza')->insert([
                                'id_poliza' => $idPoliza, 'id_contrato' => $idContrato, 'created_at' => $hoy
                            ]);
                            return $tabular + 1;
                        }
                    }

                }
            }
        }

        return $tabular;
    }

    private static function validarPolizaAbono($idContrato, $idPoliza)
    {
        $tienePolizaElAbono = DB::select("SELECT poliza FROM abonos WHERE id_contrato = '$idContrato' ORDER BY created_at DESC LIMIT 1");
        if ($tienePolizaElAbono != null) { //Ya tiene una poliza?
            if ($tienePolizaElAbono[0]->poliza == $idPoliza) {//Si tiene una poliza igual a la actual es porque aun no se contabilizaba
                return false;
            }
            //Quiere decir que ya tenia una poliza diferente a la actual
            return true;
        }
        return false;
    }

    public static function obtenerDia($diaActualNumero, $diaSeleccionado)
    {
        //Dia seleccionado
        // 0-> SabadoAnterior
        // 1-> Lunes
        // 2-> SabadoSiguiente

        switch ($diaActualNumero) {
            case 1://Es Lunes
                if ($diaSeleccionado == 0) {
                    return Carbon::now()->subDays(2)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                }
                return Carbon::now()->addDays(5)->format('Y-m-d'); //Obtengo la fecha del dia sabado siguiente
            case 2://Es Martes
                if ($diaSeleccionado == 0) {
                    return Carbon::now()->subDays(3)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return Carbon::now()->subDays(1)->format('Y-m-d'); //Obtengo la fecha del dia lunes
                }
                return Carbon::now()->addDays(4)->format('Y-m-d'); //Obtengo la fecha del dia sabado siguiente
            case 3://Es Miercoles
                if ($diaSeleccionado == 0) {
                    return Carbon::now()->subDays(4)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return Carbon::now()->subDays(2)->format('Y-m-d'); //Obtengo la fecha del dia lunes
                }
                return Carbon::now()->addDays(3)->format('Y-m-d'); //Obtengo la fecha del dia sabado siguiente
            case 4://Es Jueves
                if ($diaSeleccionado == 0) {
                    return Carbon::now()->subDays(5)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return Carbon::now()->subDays(3)->format('Y-m-d'); //Obtengo la fecha del dia lunes
                }
                return Carbon::now()->addDays(2)->format('Y-m-d'); //Obtengo la fecha del dia sabado siguiente
            case 5://Es Viernes
                if ($diaSeleccionado == 0) {
                    return Carbon::now()->subDays(6)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return Carbon::now()->subDays(4)->format('Y-m-d'); //Obtengo la fecha del dia lunes
                }
                return Carbon::now()->addDays(1)->format('Y-m-d'); //Obtengo la fecha del dia sabado siguiente
            case 6://Es Sabado
                if ($diaSeleccionado == 0) {
                    return Carbon::now()->subDays(7)->format('Y-m-d'); //Obtengo la fecha del dia sabado anterior
                } elseif ($diaSeleccionado == 1) {
                    return Carbon::now()->subDays(5)->format('Y-m-d'); //Obtengo la fecha del dia lunes
                }
                return Carbon::now()->addDays(0)->format('Y-m-d'); //Obtengo la fecha del dia sabado siguiente
        }
        return Carbon::now();
    }

    public static function entregarPoliza($idFranquicia,$idPoliza){

        $idUsuario = Auth::id();
        $usuario = Auth::user()->name;
        $hoy = Carbon::now();
        DB::table("poliza")->where("id","=",$idPoliza)->where("id_franquicia","=",$idFranquicia)->update([
            "estatus" => 2,
            "updated_at" => $hoy,
            "realizo" => $usuario
        ]);

        DB::table('historialpoliza')->insert([
            'id_usuarioC' =>  $idUsuario, 'id_poliza' => $idPoliza, 'created_at' => $hoy,
            'cambios' => "El usuario '$usuario' entrego la poliza."
        ]);
    }

    public static function entregarORegresarPoliza($idPoliza,$entregarORegresar){
        $idUsuario = Auth::id();
        $usuario = Auth::user()->name;
        $hoy = Carbon::now();
        if($entregarORegresar == 1){
            DB::table("poliza")->where("id","=",$idPoliza)->update([
                "estatus" => 1,
                "updated_at" => $hoy,
                "autorizo" => $usuario
            ]);

            DB::table('historialpoliza')->insert([
                'id_usuarioC' =>  $idUsuario, 'id_poliza' => $idPoliza, 'created_at' => $hoy,
                'cambios' => "El usuario '$usuario' autorizo la poliza."
            ]);
        }else{
            DB::table("poliza")->where("id","=",$idPoliza)->update([
                "estatus" => 0,
                "updated_at" => $hoy
            ]);

            DB::table('historialpoliza')->insert([
                'id_usuarioC' =>  $idUsuario, 'id_poliza' => $idPoliza, 'created_at' => $hoy,
                'cambios' => "El usuario '$usuario' regreso la poliza a su estado anterior."
            ]);
        }

    }

    public static function obtenerContratosPorEntregarOMontoTotalRealPoliza($idFranquicia, $idPoliza, $fechaCreacionPolizaEntrante, $id_usuario, $opcion)
    {
        //opcion
        //0 - Contratos por entregar
        //1 - Contratos monto total real

        $sumaContratos = 0;
        $query = self::obtenerQueryProductividadPoliza($idPoliza, $id_usuario, $opcion);

        //Se obtienen los contratos de idpoliza entrante
        $contratosPolizaEntrante = DB::select($query);

        if($contratosPolizaEntrante != null) {
            //Existen contratos por entregar
            $sumaContratos += $contratosPolizaEntrante[0]->suma;
        }

        $fechaCreacionPolizaEntrante = Carbon::parse($fechaCreacionPolizaEntrante)->format('Y-m-d'); //Se obtiene fechaCreacionPolizaEntrante con formato Ejem. 2022-04-01

        if(Carbon::parse($fechaCreacionPolizaEntrante)->dayOfWeekIso != 2) {
            //Es miercoles, jueves, viernes, sabado o lunes

            $fecha = $fechaCreacionPolizaEntrante;

            $limite = 2;
            $diasdecremento = Carbon::parse($fechaCreacionPolizaEntrante)->dayOfWeekIso;
            if (Carbon::parse($fechaCreacionPolizaEntrante)->dayOfWeekIso == 1) {
                //Es lunes
                $limite = 1;
                $diasdecremento = 7;
            }

            for ($i = $diasdecremento; $i > $limite; $i--){
                //Dia es mayor o igual a martes

                //Obtener fechas de dias anteriores
                $fecha = Carbon::create($fecha)->subDays(1)->format('Y-m-d'); //Descontando dias

                if (Carbon::parse($fecha)->dayOfWeekIso != 7) {
                    //Fecha diferente de domingo

                    $polizaanterior = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' AND STR_TO_DATE(created_at,'%Y-%m-%d') = STR_TO_DATE('$fecha','%Y-%m-%d')");
                    $polizaAnteriorId = $polizaanterior == null ? 0 : $polizaanterior[0]->id;

                    if (strlen($polizaAnteriorId) > 0) {
                        //Obtener productividad poliza anterior

                        $query = self::obtenerQueryProductividadPoliza($polizaAnteriorId, $id_usuario, $opcion);

                        $contratos = DB::select($query);

                        if($contratos != null) {
                            //Existen contratos por entregar
                            $sumaContratos += $contratos[0]->suma;
                        }

                    }

                }

            }

        }

        return $sumaContratos;

    }

    private static function obtenerQueryProductividadPoliza($idPoliza, $id_usuario, $opcion) {

        $query = "";

        switch ($opcion) {
            case 0:
                //Suma contratos por entregar OPTOMETRISTA
                $query = "SELECT COUNT(c.id) as suma FROM contratos c WHERE c.polizaoptometrista = '$idPoliza' AND c.id_optometrista = '$id_usuario'
                                              AND c.estatus_estadocontrato IN (1,7,9,10,11,12) AND c.aprobacionventa IN (0,1) AND c.entregaproducto = 0";
                break;
            case 1:
                //Suma monto total real OPTOMETRISTA
                $query = "SELECT COALESCE(SUM(c.totalreal),0) as suma FROM contratos c WHERE c.polizaoptometrista = '$idPoliza' AND c.id_optometrista = '$id_usuario' AND c.aprobacionventa IN (0,1)";
                break;
            case 2:
                //Contratos entregados OPTOMETRISTA
                $query = "SELECT c.id, h.id_paquete, c.totalreal FROM contratos c INNER JOIN historialclinico h ON c.id = h.id_contrato
                            WHERE h.tipo = '0' AND c.polizaoptometrista = '$idPoliza' AND c.id_optometrista = '$id_usuario'
                            AND (c.estatus_estadocontrato IN (2,4,5) OR c.id IN (SELECT g.id_contrato FROM garantias g WHERE g.estadocontratogarantia IN (2,4,5) AND g.estadogarantia IN (1,2)))
                            AND c.aprobacionventa IN (0,1)";
                break;
            case 3:
                //Suma contratos objetivo semana anterior ASISTENTE
                $query = "SELECT COUNT(c.id) as suma FROM contratos c WHERE c.poliza = '$idPoliza' AND c.id_usuariocreacion = '$id_usuario'
                            AND (c.estatus_estadocontrato IN (2,4,5,7,9,10,11,12) OR c.id IN (SELECT g.id_contrato FROM garantias g WHERE g.estadogarantia IN (1,2)))
                            AND c.aprobacionventa IN (0,2)";
                break;
            case 4:
                //Suma contratos no.ventas o ventas acumuladas semana actual ASISTENTE
                $query = "SELECT COUNT(c.id) as suma FROM contratos c WHERE c.poliza = '$idPoliza' AND c.id_usuariocreacion = '$id_usuario'
                            AND (c.estatus_estadocontrato IN (1,2,4,5,7,9,10,11,12) OR c.id IN (SELECT g.id_contrato FROM garantias g WHERE g.estadogarantia IN (1,2)))
                            AND c.aprobacionventa IN (0,2)";
                break;
            case 5:
                //Suma contratos entregados o ventas acumuladas entregadas semana actual ASISTENTE
                $query = "SELECT COUNT(c.id) as suma FROM contratos c WHERE c.poliza = '$idPoliza' AND c.id_usuariocreacion = '$id_usuario'
                            AND (c.estatus_estadocontrato IN (2,4,5,7,10,11,12) OR c.id IN (SELECT g.id_contrato FROM garantias g WHERE g.estadogarantia IN (1,2)))
                            AND c.aprobacionventa IN (0,2)";
                break;
        }

        return $query;
    }

    public static function obtenerNumeroContratosEntregadosAbonoAtrasadoLiquidadosConGarantiaPorPaqueteYContratosYSumaTotalRealContratosEntregadosPorUsuario($idFranquicia, $idPoliza,
                                                                                                                                                             $fechaCreacionPolizaEntrante, $id_usuario)
    {

        $arrayRespuesta = array();
        $arrayNumContratosPorPaquetes = array();

        $query = self::obtenerQueryProductividadPoliza($idPoliza, $id_usuario, 2);

        //Se obtienen los contratos entregados del idpoliza entrante
        $contratosEntregadosAbonoAtrasadoLiquidadosConGarantia = DB::select($query);

        $array = array();
        $contratosEntregadosAbonoAtrasadoLiquidadosConGarantiaCorrecto = array();

        if($contratosEntregadosAbonoAtrasadoLiquidadosConGarantia != null) {
            //Existen contratos

            foreach ($contratosEntregadosAbonoAtrasadoLiquidadosConGarantia as $contrato) {
                $idContrato = $contrato->id;
                if(!in_array($idContrato, $array)) {
                    //No existe contrato en el arreglo $contratosEntregadosAbonoAtrasadoLiquidadosConGarantiaCorrecto
                    array_push($contratosEntregadosAbonoAtrasadoLiquidadosConGarantiaCorrecto, $contrato); //Agregacion a $contratosEntregadosAbonoAtrasadoLiquidadosConGarantiaCorrecto
                    array_push($array, $idContrato); //Se agrega el id del contrato al array para que este no vuelva a insertarse de nuevo
                }
            }

        }

        $fechaCreacionPolizaEntrante = Carbon::parse($fechaCreacionPolizaEntrante)->format('Y-m-d'); //Se obtiene fechaCreacionPolizaEntrante con formato Ejem. 2022-04-01

        if(Carbon::parse($fechaCreacionPolizaEntrante)->dayOfWeekIso != 2) {
            //Es miercoles, jueves, viernes, sabado o lunes

            $fecha = $fechaCreacionPolizaEntrante;

            $limite = 2;
            $diasdecremento = Carbon::parse($fechaCreacionPolizaEntrante)->dayOfWeekIso;
            if (Carbon::parse($fechaCreacionPolizaEntrante)->dayOfWeekIso == 1) {
                //Es lunes
                $limite = 1;
                $diasdecremento = 7;
            }

            for ($i = $diasdecremento; $i > $limite; $i--){
                //Dia es mayor o igual a martes

                //Obtener fechas de dias anteriores
                $fecha = Carbon::create($fecha)->subDays(1)->format('Y-m-d'); //Descontando dias

                if (Carbon::parse($fecha)->dayOfWeekIso != 7) {
                    //Fecha diferente de domingo

                    $polizaanterior = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' AND STR_TO_DATE(created_at,'%Y-%m-%d') = STR_TO_DATE('$fecha','%Y-%m-%d')");
                    $polizaAnteriorId = $polizaanterior == null ? 0 : $polizaanterior[0]->id;

                    if (strlen($polizaAnteriorId) > 0) {
                        //Obtener productividad poliza anterior

                        $query = self::obtenerQueryProductividadPoliza($polizaAnteriorId, $id_usuario, 2);
                        $contratos = DB::select($query);

                        if ($contratos != null) {
                            //Existen contratos
                            foreach ($contratos as $contrato) {
                                $idContrato = $contrato->id;
                                if (!in_array($idContrato, $array)) {
                                    //No existe contrato en el arreglo $contratosEntregadosAbonoAtrasadoLiquidadosConGarantiaCorrecto
                                    array_push($contratosEntregadosAbonoAtrasadoLiquidadosConGarantiaCorrecto, $contrato); //Agregacion a $contratosEntregadosAbonoAtrasadoLiquidadosConGarantiaCorrecto
                                    array_push($array, $idContrato); //Se agrega el id del contrato al array para que este no vuelva a insertarse de nuevo
                                }
                            }
                        }

                    }

                }

            }

        }

        $sumaTotalRealContratosEntregados = 0; //Para guardar la suma de los totalReal de los contratos entregados
        foreach ($contratosEntregadosAbonoAtrasadoLiquidadosConGarantiaCorrecto as $contratoCorrecto) {
            //Recorrido para obtener el numero de contratos entregados por paquetes
            $id_paquete = $contratoCorrecto->id_paquete;
            $totalReal = $contratoCorrecto->totalreal;
            $sumaTotalRealContratosEntregados += $totalReal;
            if(array_key_exists($id_paquete, $arrayNumContratosPorPaquetes)) {
                //Existe la llave del id_paquete
                $arrayNumContratosPorPaquetes[$id_paquete] = $arrayNumContratosPorPaquetes[$id_paquete] + 1;
            }else {
                //No se encontro la llave del id_paquete
                $arrayNumContratosPorPaquetes[$id_paquete] = 1;
            }
        }

        array_push($arrayRespuesta, $arrayNumContratosPorPaquetes);
        array_push($arrayRespuesta, $contratosEntregadosAbonoAtrasadoLiquidadosConGarantiaCorrecto);
        array_push($arrayRespuesta, $sumaTotalRealContratosEntregados);

        return $arrayRespuesta;

    }

    public static function obtenerDineroObjetivoEnVentas($contratos, $numeroventas, $totaleco, $totaljr, $totaldoradounoydoradodos, $totalplatino, $totalpremium,
                                                         $comisionunototalcontratosoptometrista, $comisionunovaloroptometrista, $comisiondostotalcontratosoptometrista,
                                                         $comisiondosvaloroptometrista, $comisiontrestotalcontratosoptometrista, $comisiontresvaloroptometrista) {

        $arrayRespuesta = array();

        $dinerotreinta = 0;
        $dinerocuarenta = 0;
        $dineropremium = 0;

        $sumaTotalRealEcoJr = 0;
        $sumaTotalRealJr = 0;
        $sumaTotalRealDorado1YDorado2 = 0;
        $sumaTotalRealPlatino = 0;
        $sumaTotalRealPremium = 0;

        foreach ($contratos as $contrato) {
            $idPaquete = $contrato->id_paquete;
            $totalRealContrato = $contrato->totalreal;
            switch ($idPaquete) {
                case 1:
                case 2:
                case 3://Lectura, Proteccion y Eco Jr
                    $sumaTotalRealEcoJr += $totalRealContrato;
                    break;
                case 4://Jr
                    $sumaTotalRealJr += $totalRealContrato;
                    break;
                case 5:
                case 6://Dorado 1 y Dorado 2
                    $sumaTotalRealDorado1YDorado2 += $totalRealContrato;
                    break;
                case 7://Platino
                    $sumaTotalRealPlatino += $totalRealContrato;
                    break;
                case 8://Premium
                    $sumaTotalRealPremium += $totalRealContrato;
                    break;
            }
        }

        //Calculo de premium
        $numeroventaspremiumboolean = $totalpremium >= $comisiontrestotalcontratosoptometrista ? true : false;
        if ($numeroventaspremiumboolean) {
            //No.ventas premium es mayor o igual a comisiontrestotalcontratos
            $dineropremium = $sumaTotalRealPremium * ($comisiontresvaloroptometrista / 100);
        }

        $numeroventastreintaboolean = $numeroventas >= $comisionunototalcontratosoptometrista ? true : false;

        if($numeroventastreintaboolean) {
            //No.ventas es mayor o igual a comisionunototalcontratos

            $numeroventascuarentaboolean = $numeroventas >= $comisiondostotalcontratosoptometrista ? true : false;
            if($numeroventascuarentaboolean) {
                //No.ventas es mayor o igual a comisiondostotalcontratos
                $dinerocuarenta = ($sumaTotalRealEcoJr + $sumaTotalRealJr + $sumaTotalRealDorado1YDorado2 + $sumaTotalRealPlatino) * ($comisiondosvaloroptometrista / 100);
            }else {
                //No.ventas es menor a comisiondostotalcontratos

                $validacionEcoJr = $totaleco >= 24 ? true : false;
                $validacionJr = $totaljr >= 4 ? true : false;
                $validacionDorado1YDorado2 = $totaldoradounoydoradodos >= 10 ? true : false;
                $validacionPlatino = $totalplatino >= 2 ? true : false;

                if (!$validacionEcoJr) {
                    $sumaTotalRealEcoJr = 0;
                }

                if (!$validacionJr) {
                    $sumaTotalRealJr = 0;
                }

                if (!$validacionDorado1YDorado2) {
                    $sumaTotalRealDorado1YDorado2 = 0;
                }

                if (!$validacionPlatino) {
                    $sumaTotalRealPlatino = 0;
                }

                if ($validacionEcoJr || $validacionJr || $validacionDorado1YDorado2 || $validacionPlatino) {
                    $dinerotreinta = ($sumaTotalRealEcoJr + $sumaTotalRealJr + $sumaTotalRealDorado1YDorado2 + $sumaTotalRealPlatino) * ($comisionunovaloroptometrista / 100);
                } else {
                    $dinerotreinta = 500;
                }

            }

        }

        array_push($arrayRespuesta, $dinerotreinta);
        array_push($arrayRespuesta, $dinerocuarenta);
        array_push($arrayRespuesta, $dineropremium);

        return $arrayRespuesta;

    }

    public static function obtenerNumeroObjetivoSemanaAnteriorAsistente($idFranquicia, $fechaCreacionPolizaEntrante, $id_usuario)
    {

        $numObjetivoSemanaAnterior = 0;

        $fechaCreacionPolizaEntrante = Carbon::parse($fechaCreacionPolizaEntrante)->format('Y-m-d'); //Se obtiene fechaCreacionPolizaEntrante con formato Ejem. 2022-04-01
        $diaEnNumeroFechaCreacionPolizaEntrante = Carbon::parse($fechaCreacionPolizaEntrante)->dayOfWeekIso; //Se obtiene el numero de la semana de la fechaCreacionPolizaEntrante

        $esLunes = true;
        if($diaEnNumeroFechaCreacionPolizaEntrante > 1) {
            //diaEnNumeroFechaCreacionPolizaEntrante es martes, miercoles, jueves, viernes o sabado
            $esLunes = false;
        }

        $fecha = $fechaCreacionPolizaEntrante;
        $bandera = 0;

        for ($i = $diaEnNumeroFechaCreacionPolizaEntrante; $i >= 0; $i--) {

            if($bandera == 0) {
                if ($esLunes) {
                    //Es lunes
                    if ($i == 0) {
                        $i = 6; //Empezar desde el sabado
                        $bandera = 1;
                    }
                }else {
                    //Es martes, miercoles, jueves, viernes o sabado
                    if ($i == 2) {
                        $bandera = 1;
                        $i = 1;
                    }

                }
            }

            //Obtener fechas de dias anteriores
            $fecha = Carbon::create($fecha)->subDays(1)->format('Y-m-d'); //Descontando dias

            if($bandera == 1) {

                if ($i == 1) {
                    //Es lunes

                    $poliza = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' AND STR_TO_DATE(created_at,'%Y-%m-%d') = STR_TO_DATE('$fecha','%Y-%m-%d')");

                    if ($poliza != null) {
                        //Existe poliza

                        $idPolizaExistente = $poliza[0]->id;

                        $query = self::obtenerQueryProductividadPoliza($idPolizaExistente, $id_usuario, 3);

                        $contratos = DB::select($query);

                        if ($contratos != null) {
                            //Existen contratos por entregar
                            $numObjetivoSemanaAnterior += $contratos[0]->suma;
                        }

                        if(Carbon::parse($fecha)->dayOfWeekIso != 2) {
                            //Es miercoles, jueves, viernes, sabado o lunes

                            $limite = 2;
                            $diasdecremento = Carbon::parse($fecha)->dayOfWeekIso;
                            if (Carbon::parse($fecha)->dayOfWeekIso == 1) {
                                //Es lunes
                                $limite = 1;
                                $diasdecremento = 7;
                            }

                            for ($i = $diasdecremento; $i > $limite; $i--){
                                //Dia es mayor o igual a martes

                                //Obtener fechas de dias anteriores
                                $fecha = Carbon::create($fecha)->subDays(1)->format('Y-m-d'); //Descontando dias

                                if (Carbon::parse($fecha)->dayOfWeekIso != 7) {
                                    //Fecha diferente de domingo

                                    $polizaanterior = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' AND STR_TO_DATE(created_at,'%Y-%m-%d') = STR_TO_DATE('$fecha','%Y-%m-%d')");
                                    $polizaAnteriorId = $polizaanterior == null ? 0 : $polizaanterior[0]->id;

                                    if (strlen($polizaAnteriorId) > 0) {
                                        //Obtener productividad poliza anterior

                                        $query = self::obtenerQueryProductividadPoliza($polizaAnteriorId, $id_usuario, 3);

                                        $contratos = DB::select($query);

                                        if ($contratos != null) {
                                            //Existen contratos por entregar
                                            $numObjetivoSemanaAnterior += $contratos[0]->suma;
                                        }

                                    }

                                }

                            }

                        }

                    }

                }
            }

        }

        return $numObjetivoSemanaAnterior;

    }

    public static function obtenerNoVentasAprobadasVentasAcumuladasYVentasAcumuladasAprobadasAsistente($idFranquicia, $idPoliza, $fechaCreacionPolizaEntrante, $id_usuario)
    {

        $arrayResultados = array();

        $sumaContratosNumVentas = 0;
        $sumaContratosAprobadas = 0;
        $sumaContratosVentasAcumuladas = 0;
        $sumaContratosVentasAcumuladasAprobadas = 0;

        //Se obtienen los contratos de idpoliza entrante
        $query1 = self::obtenerQueryProductividadPoliza($idPoliza, $id_usuario, 4);
        $contratosPolizaEntrante1 = DB::select($query1);
        if($contratosPolizaEntrante1 != null) {
            //Existen contratos
            $sumaContratosNumVentas += $contratosPolizaEntrante1[0]->suma; //Suma numero ventas
        }

        $query2 = self::obtenerQueryProductividadPoliza($idPoliza, $id_usuario, 5);
        $contratosPolizaEntrante2 = DB::select($query2);
        if($contratosPolizaEntrante2 != null) {
            //Existen contratos
            $sumaContratosAprobadas += $contratosPolizaEntrante2[0]->suma; //Suma numero entregados
        }

        $fechaCreacionPolizaEntrante = Carbon::parse($fechaCreacionPolizaEntrante)->format('Y-m-d'); //Se obtiene fechaCreacionPolizaEntrante con formato Ejem. 2022-04-01

        if(Carbon::parse($fechaCreacionPolizaEntrante)->dayOfWeekIso != 2) {
            //Es miercoles, jueves, viernes, sabado o lunes

            $fecha = $fechaCreacionPolizaEntrante;

            $limite = 2;
            $diasdecremento = Carbon::parse($fechaCreacionPolizaEntrante)->dayOfWeekIso;
            if (Carbon::parse($fechaCreacionPolizaEntrante)->dayOfWeekIso == 1) {
                //Es lunes
                $limite = 1;
                $diasdecremento = 7;
            }

            for ($i = $diasdecremento; $i > $limite; $i--){
                //Dia es mayor o igual a martes

                //Obtener fechas de dias anteriores
                $fecha = Carbon::create($fecha)->subDays(1)->format('Y-m-d'); //Descontando dias

                if (Carbon::parse($fecha)->dayOfWeekIso != 7) {
                    //Fecha diferente de domingo

                    $polizaanterior = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' AND STR_TO_DATE(created_at,'%Y-%m-%d') = STR_TO_DATE('$fecha','%Y-%m-%d')");
                    $polizaAnteriorId = $polizaanterior == null ? 0 : $polizaanterior[0]->id;

                    if (strlen($polizaAnteriorId) > 0) {
                        //Obtener productividad poliza anterior

                        $query1 = self::obtenerQueryProductividadPoliza($polizaAnteriorId, $id_usuario, 4);
                        $contratos1 = DB::select($query1);
                        if($contratos1 != null) {
                            //Existen contratos
                            $sumaContratosVentasAcumuladas += $contratos1[0]->suma; //Suma ventas acumuladas
                        }

                        $query2 = self::obtenerQueryProductividadPoliza($polizaAnteriorId, $id_usuario, 5);
                        $contratos2 = DB::select($query2);
                        if($contratos2 != null) {
                            //Existen contratos
                            $sumaContratosVentasAcumuladasAprobadas += $contratos2[0]->suma; //Suma ventas acumuladas entregadas
                        }

                    }

                }

            }

        }

        array_push($arrayResultados, $sumaContratosNumVentas);
        array_push($arrayResultados, $sumaContratosAprobadas);
        array_push($arrayResultados, $sumaContratosNumVentas + $sumaContratosVentasAcumuladas);
        array_push($arrayResultados, $sumaContratosAprobadas + $sumaContratosVentasAcumuladasAprobadas);

        return $arrayResultados;

    }

    public static function obtenerQueryVentasOptos($polizaAnteriorId,$hoyNumero,$diaactual,$idFranquicia,$idUsuario,$rol){

        switch ($hoyNumero){
            case 1: //LUNES

                //Obtenemos ventas de la semana pasada
                $ventaDiasSemanaPasada = DB::select("SELECT pvd.viernes, pvd.jueves, pvd.miercoles, pvd.martes, pvd.lunes
                                                            FROM polizaventasdias pvd WHERE pvd.id_franquicia = '$idFranquicia'
                                                            AND pvd.id_poliza = '$polizaAnteriorId' AND pvd.id_usuario = '$idUsuario' AND pvd.rol = '$rol'");

                //Ventas del viernes pasado
                $ventaViernesPasado = $ventaDiasSemanaPasada == null ? 0 : $ventaDiasSemanaPasada[0]->viernes;
                //Ventas del jueves pasado
                $ventaJuevesPasado = $ventaDiasSemanaPasada == null ? 0 : $ventaDiasSemanaPasada[0]->jueves;
                //Ventas del miercoles pasado
                $ventaMiercolesPasado = $ventaDiasSemanaPasada == null ? 0 : $ventaDiasSemanaPasada[0]->miercoles;
                //Ventas del martes pasado
                $ventaMartesPasado = $ventaDiasSemanaPasada == null ? 0 : $ventaDiasSemanaPasada[0]->martes;
                //Ventas del lunes pasado
                $ventaLunesPasado = $ventaDiasSemanaPasada == null ? 0 : $ventaDiasSemanaPasada[0]->lunes;

                return "$ventaLunesPasado,$ventaMartesPasado,$ventaMiercolesPasado,$ventaJuevesPasado,$ventaViernesPasado,$diaactual";
            case 2://MARTES
                //Ventas del diaactual, que en realidad son las ventas del LUNES actual por que la poliza se crea en la madrugada
                return "$diaactual,0,0,0,0,0";
            case 3://MIERCOLES

                //Obtenemos ventas de la semana actual
                $ventaDiasSemanaActual = DB::select("SELECT pvd.lunes
                                                            FROM polizaventasdias pvd WHERE pvd.id_franquicia = '$idFranquicia'
                                                            AND pvd.id_poliza = '$polizaAnteriorId' AND pvd.id_usuario = '$idUsuario' AND pvd.rol = '$rol'");

                //Ventas del lunes actual
                $ventaLunes = $ventaDiasSemanaActual == null ? 0 : $ventaDiasSemanaActual[0]->lunes;

                return "$ventaLunes,$diaactual,0,0,0,0";
            case 4://JUEVES

                //Obtenemos ventas de la semana actual
                $ventaDiasSemanaActual = DB::select("SELECT pvd.lunes, pvd.martes
                                                            FROM polizaventasdias pvd WHERE pvd.id_franquicia = '$idFranquicia'
                                                            AND pvd.id_poliza = '$polizaAnteriorId' AND pvd.id_usuario = '$idUsuario' AND pvd.rol = '$rol'");

                //Ventas del lunes actual
                $ventaLunes = $ventaDiasSemanaActual == null ? 0 : $ventaDiasSemanaActual[0]->lunes;
                //Ventas del martes actual
                $ventaMartes = $ventaDiasSemanaActual == null ? 0 : $ventaDiasSemanaActual[0]->martes;

                return "$ventaLunes,$ventaMartes,$diaactual,0,0,0";
            case 5://VIERNES

                //Obtenemos ventas de la semana actual
                $ventaDiasSemanaActual = DB::select("SELECT pvd.lunes, pvd.martes, pvd.miercoles
                                                            FROM polizaventasdias pvd WHERE pvd.id_franquicia = '$idFranquicia'
                                                            AND pvd.id_poliza = '$polizaAnteriorId' AND pvd.id_usuario = '$idUsuario' AND pvd.rol = '$rol'");

                //Ventas del lunes actual
                $ventaLunes = $ventaDiasSemanaActual == null ? 0 : $ventaDiasSemanaActual[0]->lunes;
                //Ventas del martes actual
                $ventaMartes = $ventaDiasSemanaActual == null ? 0 : $ventaDiasSemanaActual[0]->martes;
                //Ventas del miercoles actual
                $ventaMiercoles = $ventaDiasSemanaActual == null ? 0 : $ventaDiasSemanaActual[0]->miercoles;

                return "$ventaLunes,$ventaMartes,$ventaMiercoles,$diaactual,0,0";
            case 6://SABADO

                //Obtenemos ventas de la semana actual
                $ventaDiasSemanaActual = DB::select("SELECT pvd.lunes, pvd.martes, pvd.miercoles, pvd.jueves
                                                            FROM polizaventasdias pvd WHERE pvd.id_franquicia = '$idFranquicia'
                                                            AND pvd.id_poliza = '$polizaAnteriorId' AND pvd.id_usuario = '$idUsuario' AND pvd.rol = '$rol'");

                //Ventas del lunes actual
                $ventaLunes = $ventaDiasSemanaActual == null ? 0 : $ventaDiasSemanaActual[0]->lunes;
                //Ventas del martes actual
                $ventaMartes = $ventaDiasSemanaActual == null ? 0 : $ventaDiasSemanaActual[0]->martes;
                //Ventas del miercoles actual
                $ventaMiercoles = $ventaDiasSemanaActual == null ? 0 : $ventaDiasSemanaActual[0]->miercoles;
                //Ventas del jueves actual
                $ventaJueves = $ventaDiasSemanaActual == null ? 0 : $ventaDiasSemanaActual[0]->jueves;

                return "$ventaLunes,$ventaMartes,$ventaMiercoles,$ventaJueves,$diaactual,0";
        }
        return "";
    }

    public static function crearpoliza($idFranquicia){

        $hoy = Carbon::now();
        //$hoy = Carbon::parse("2023-09-27");
        $hoyNumero = $hoy->dayOfWeekIso;

        if ($hoyNumero != 7) {
            //Dia es diferente a domingo

            $hoyFormato = $hoy->format('Y-m-d');

            //Traemos la ultima poliza de la semana actual.
            $polizaAnterior = DB::select("SELECT estatus, id, total, totalcontratosasistentecomision1, valorasistentecomision1, totalcontratosasistentecomision2,
                                                                valorasistentecomision2, totalcontratosoptometristacomision1, valoroptometristacomision1,
                                                                totalcontratosoptometristacomision2, valoroptometristacomision2,
                                                                totalcontratosoptometristacomision3, valoroptometristacomision3
                                                                FROM poliza WHERE id_franquicia = '$idFranquicia' ORDER BY created_at DESC LIMIT 1");
            $polizaAnteriorId = $polizaAnterior == null ? "" : $polizaAnterior[0]->id;

            $comisionunototalcontratosasistente = 13;
            $comisionunovalorasistente = 80;
            $comisiondostotalcontratosasistente = 20;
            $comisiondosvalorasistente = 120;
            $comisionunototalcontratosoptometrista = 30;
            $comisionunovaloroptometrista = 4;
            $comisiondostotalcontratosoptometrista = 40;
            $comisiondosvaloroptometrista = 5;
            $comisiontrestotalcontratosoptometrista = 10;
            $comisiontresvaloroptometrista = 3;

            if ($hoyNumero == 2 || $polizaAnterior == null) {
                //Es martes o polizaAnterior es igual a nulo

                //CONSULTA DE COMISIONES PARA ASISTENTES Y OPTOMETRISTAS
                //Consulta de primera comision para asistentes
                $comisionunoasistente = DB::select("SELECT totalcontratos, valor FROM comisionesventas
                                                        WHERE id_franquicia = '$idFranquicia'
                                                        AND usuario = '0' AND comision = '1'");
                if ($comisionunoasistente != null) {
                    //Existe primera comision para asistentes
                    $comisionunototalcontratosasistente = $comisionunoasistente[0]->totalcontratos;
                    $comisionunovalorasistente = $comisionunoasistente[0]->valor;
                }

                //Consulta de segunda comision para asistentes
                $comisiondosasistente = DB::select("SELECT totalcontratos, valor FROM comisionesventas
                                                        WHERE id_franquicia = '$idFranquicia'
                                                        AND usuario = '0' AND comision = '2'");
                if ($comisiondosasistente != null) {
                    //Existe segunda comision para asistentes
                    $comisiondostotalcontratosasistente = $comisiondosasistente[0]->totalcontratos;
                    $comisiondosvalorasistente = $comisiondosasistente[0]->valor;
                }

                //Consulta de primera comision para optometristas
                $comisionunooptometrista = DB::select("SELECT totalcontratos, valor FROM comisionesventas
                                                        WHERE id_franquicia = '$idFranquicia'
                                                        AND usuario = '1' AND comision = '1'");
                if ($comisionunooptometrista != null) {
                    //Existe primera comision para optometristas
                    $comisionunototalcontratosoptometrista = $comisionunooptometrista[0]->totalcontratos;
                    $comisionunovaloroptometrista = $comisionunooptometrista[0]->valor;
                }

                //Consulta de segunda comision para optometristas
                $comisiondosoptometrista = DB::select("SELECT totalcontratos, valor FROM comisionesventas
                                                        WHERE id_franquicia = '$idFranquicia'
                                                        AND usuario = '1' AND comision = '2'");
                if ($comisiondosoptometrista != null) {
                    //Existe segunda comision para optometristas
                    $comisiondostotalcontratosoptometrista = $comisiondosoptometrista[0]->totalcontratos;
                    $comisiondosvaloroptometrista = $comisiondosoptometrista[0]->valor;
                }

                //Consulta de tercera comision para optometristas
                $comisiontresoptometrista = DB::select("SELECT totalcontratos, valor FROM comisionesventas
                                                        WHERE id_franquicia = '$idFranquicia'
                                                        AND usuario = '1' AND comision = '3'");
                if ($comisiontresoptometrista != null) {
                    //Existe segunda comision para optometristas
                    $comisiontrestotalcontratosoptometrista = $comisiontresoptometrista[0]->totalcontratos;
                    $comisiontresvaloroptometrista = $comisiontresoptometrista[0]->valor;
                }

            }else {
                //No es martes y poliza es diferente de nulo
                $comisionunototalcontratosasistente = $polizaAnterior[0]->totalcontratosasistentecomision1;
                $comisionunovalorasistente = $polizaAnterior[0]->valorasistentecomision1;
                $comisiondostotalcontratosasistente = $polizaAnterior[0]->totalcontratosasistentecomision2;
                $comisiondosvalorasistente = $polizaAnterior[0]->valorasistentecomision2;
                $comisionunototalcontratosoptometrista = $polizaAnterior[0]->totalcontratosoptometristacomision1;
                $comisionunovaloroptometrista = $polizaAnterior[0]->valoroptometristacomision1;
                $comisiondostotalcontratosoptometrista = $polizaAnterior[0]->totalcontratosoptometristacomision2;
                $comisiondosvaloroptometrista = $polizaAnterior[0]->valoroptometristacomision2;
                $comisiontrestotalcontratosoptometrista = $polizaAnterior[0]->totalcontratosoptometristacomision3;
                $comisiontresvaloroptometrista = $polizaAnterior[0]->valoroptometristacomision3;

            }

            //Consultamos si ya existe una poliza el dia de hoy
            $polizaAbierta = DB::select("SELECT id FROM poliza WHERE created_at  like '%" . $hoyFormato . "%' AND id_franquicia = '$idFranquicia'");

            if ($polizaAbierta == null) {
                //Aun no existe la poliza del dia

                if ($polizaAnterior != null && $polizaAnterior[0]->estatus != 1) {
                    $idUsuario = 699; //idUsuario de sistemas automatico
                    $datosusuario = DB::select("SELECT name FROM users WHERE id = '$idUsuario'");
                    if ($datosusuario != null) {
                        $nameUsuario = $datosusuario[0]->name;

                        if ($polizaAnterior[0]->estatus == 0) {
                            //No ha sido terminada ni autorizada
                            DB::table("poliza")->where("id", "=", $polizaAnterior[0]->id)->update([
                                "estatus" => 1,
                                "updated_at" => $hoy,
                                "realizo" => $nameUsuario,
                                "autorizo" => $nameUsuario
                            ]);
                            DB::table('historialpoliza')->insert([
                                'id_usuarioC' => $idUsuario, 'id_poliza' => $polizaAnterior[0]->id, 'created_at' => $hoy,
                                'cambios' => "El usuario '$nameUsuario' entrego la poliza."
                            ]);
                        } else {
                            //Esta terminada pero no ha sido autorizada
                            DB::table("poliza")->where("id", "=", $polizaAnterior[0]->id)->update([
                                "estatus" => 1,
                                "updated_at" => $hoy,
                                "autorizo" => $nameUsuario
                            ]);
                        }

                        DB::table('historialpoliza')->insert([
                            'id_usuarioC' => $idUsuario, 'id_poliza' => $polizaAnterior[0]->id, 'created_at' => $hoy,
                            'cambios' => "El usuario '$nameUsuario' autorizo la poliza."
                        ]);
                    }
                }

                $nombrefranquicia = DB::select("SELECT f.ciudad FROM franquicias f WHERE f.id = '$idFranquicia'");
                $nombrefranquicia = $nombrefranquicia[0]->ciudad == null ? "SIN NOMBRE" : $nombrefranquicia[0]->ciudad;

                \Log::info("Poliza : Creacion " . $idFranquicia . " - $nombrefranquicia");

                //Creamos la poliza
                $idPoliza = DB::table("poliza")->insertGetId([
                    "id_franquicia" => $idFranquicia,
                    "ingresosadmin" => 0,
                    "ingresosventas" => 0,
                    "ingresoscobranza" => 0,
                    "gastosadmin" => 0,
                    "gastoscobranza" => 0,
                    "otrosgastos" => 0,
                    "estatus" => 0,
                    "total" => 0,
                    "totalcontratosasistentecomision1" => $comisionunototalcontratosasistente,
                    "valorasistentecomision1" => $comisionunovalorasistente,
                    "totalcontratosasistentecomision2" => $comisiondostotalcontratosasistente,
                    "valorasistentecomision2" => $comisiondosvalorasistente,
                    "totalcontratosoptometristacomision1" => $comisionunototalcontratosoptometrista,
                    "valoroptometristacomision1" => $comisionunovaloroptometrista,
                    "totalcontratosoptometristacomision2" => $comisiondostotalcontratosoptometrista,
                    "valoroptometristacomision2" => $comisiondosvaloroptometrista,
                    "totalcontratosoptometristacomision3" => $comisiontrestotalcontratosoptometrista,
                    "valoroptometristacomision3" => $comisiontresvaloroptometrista,
                    "created_at" => $hoy
                ]);

                $arrayUsuarios = array();
                $usuarios = DB::select("SELECT u.id FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                              WHERE uf.id_franquicia = '$idFranquicia' AND rol_id NOT IN (12,13) ORDER BY u.name ASC");

                foreach ($usuarios as $usuario) {
                    //Recorrido de usuarios dados de alta en sucursal
                    array_push($arrayUsuarios, $usuario->id);
                }

                $usuarios = DB::select("SELECT u.id FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                              WHERE u.id_franquiciaprincipal = '$idFranquicia' AND rol_id IN (12,13) ORDER BY u.name ASC");

                foreach ($usuarios as $usuario) {
                    //Recorrido de usuarios asistentes y optometristas dados de alta en sucursal principal
                    if (!in_array($usuario->id, $arrayUsuarios)) {
                        //No existe usuario en arrayUsuario
                        array_push($arrayUsuarios, $usuario->id);
                    }
                }

                foreach ($arrayUsuarios as $id_usuario) {
                    DB::table("asistencia")->insert([
                        "id_poliza" => $idPoliza,
                        "id_usuario" => $id_usuario,
                        "id_tipoasistencia" => 0,
                        "created_at" => $hoy
                    ]);
                }

                \Log::info("Log");

                $usuariosFranquicia = DB::select("SELECT u.id, u.rol_id FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                              WHERE u.id_franquiciaprincipal = '$idFranquicia' AND u.rol_id IN (12,13) ORDER BY u.name ASC");

                foreach ($usuariosFranquicia as $usuarioFranquicia) {
                    //Recorrido de asistentes y optometristas
                    $idUsuarioFranquicia = $usuarioFranquicia->id;
                    $rolUsuarioFranquicia = $usuarioFranquicia->rol_id;

                    if ($rolUsuarioFranquicia == 12) {
                        //Optometrista
                        DB::update("UPDATE contratos SET polizaoptometrista = '$idPoliza' WHERE id_optometrista = '$idUsuarioFranquicia'
                                                AND (estatus_estadocontrato IN (2,4,5,7,10,11,12)
                                                         OR (estatus_estadocontrato = '9' AND esperapoliza = 0))
                                                AND datos = '1' AND polizaoptometrista IS NULL");//Actualizamos todos los contratos
                    }else {
                        //Asistente
                        DB::update("UPDATE contratos SET poliza = '$idPoliza' WHERE id_usuariocreacion = '$idUsuarioFranquicia'
                                                AND (estatus_estadocontrato IN (2,4,5,7,10,11,12)
                                                         OR (estatus_estadocontrato = '9' AND esperapoliza = 0))
                                                AND datos = '1' AND poliza IS NULL");//Actualizamos todos los contratos
                    }

                    DB::update("UPDATE abonos
                                            SET poliza = '$idPoliza'
                                            WHERE id_usuario = '$idUsuarioFranquicia'
                                            AND id_franquicia = '$idFranquicia'
                                            AND poliza IS NULL");//Actualizamos los abonos

                }

                \Log::info("Log 0");

                $usuariosFranquiciaCobranza = DB::select("SELECT u.id FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                              WHERE uf.id_franquicia = '$idFranquicia' AND u.rol_id IN (4) ORDER BY u.name ASC");
                //Actualizar abonos ingresados por cobranza
                foreach ($usuariosFranquiciaCobranza as $usuarioFranquiciaCobranza) {
                    //Recorrido de cobranza
                    $idUsuarioFranquicia = $usuarioFranquiciaCobranza->id;
                    DB::update("UPDATE abonos
                                            SET poliza = '$idPoliza'
                                            WHERE id_usuario = '$idUsuarioFranquicia'
                                            AND poliza IS NULL");//Actualizamos los abonos
                }

                //Actualizar abonos ingresados por administradores, directores y principal
                DB::update("UPDATE abonos a
                                    INNER JOIN users u
                                        ON a.id_usuario = u.id
                                    SET a.poliza = '$idPoliza'
                                    WHERE u.rol_id IN (6,7,8)
                                    AND a.id_franquicia = '$idFranquicia'
                                    AND a.poliza IS NULL");//Actualizamos los abonos

                \Log::info("Log 1");

                //OBTENCION DE IDS DE OPTOMETRISTAS Y ASISTENTES
                $idsOptometristas = array();
                $idsAsistentes = array();

                //Obtener contratos que entraron en la poliza creada
                $contratos = DB::select("SELECT id_usuariocreacion, id_optometrista FROM contratos WHERE poliza = '$idPoliza' OR polizaoptometrista = '$idPoliza'");

                foreach ($contratos as $contrato) {
                    //Recorrido de contratos para obtener optometristas y asistentes

                    if (!in_array($contrato->id_optometrista, $idsOptometristas)) {
                        //No existe el id_optometrista en el array
                        array_push($idsOptometristas, $contrato->id_optometrista); //Se agrega el id_optometrista para que este no vuelva a insertarse de nuevo
                    }

                    $usuario = DB::select("SELECT rol_id, id_franquiciaprincipal FROM users WHERE id = '" . $contrato->id_usuariocreacion  . "'");
                    if ($usuario != null) {
                        //Existe usuario
                        if ($usuario[0]->id_franquiciaprincipal != null && $usuario[0]->id_franquiciaprincipal == $idFranquicia) {
                            //Tiene algo en id_franquiciaprincipal y la franquicia principal es igual a la franquicia ejecutada
                            if ($usuario[0]->rol_id == 13) {
                                //Rol es igual a ASISTENTE
                                if (!in_array($contrato->id_usuariocreacion, $idsAsistentes)) {
                                    //No existe el id_usuariocreacion en el array
                                    array_push($idsAsistentes, $contrato->id_usuariocreacion); //Se agrega el id_usuariocreacion para que este no vuelva a insertarse de nuevo
                                }
                            }
                        }
                    }

                }

                //Obtener usuarios con rol asistente/optometrista que esten dadas de alta en la sucursal
                $consultaoptometristasasistentes = DB::select("SELECT u.id, u.rol_id FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                              WHERE u.id_franquiciaprincipal = '$idFranquicia' AND u.rol_id IN (12,13) ORDER BY u.name ASC");

                if ($consultaoptometristasasistentes != null) {
                    //Existen registros
                    foreach ($consultaoptometristasasistentes as $optometristaAsistente) {
                        //Recorrido de optometristas y asistentes de la sucursal
                        switch ($optometristaAsistente->rol_id) {
                            case 12:
                                //OPTOMETRISTA
                                if (!in_array($optometristaAsistente->id, $idsOptometristas)) {
                                    //No existe el id_optometrista en el array
                                    array_push($idsOptometristas, $optometristaAsistente->id); //Se agrega el id_usuario para que este no vuelva a insertarse de nuevo
                                }
                                break;
                            case 13:
                                //ASISTENTE
                                if (!in_array($optometristaAsistente->id, $idsAsistentes)) {
                                    //No existe el id_usuariocreacion en el array
                                    array_push($idsAsistentes, $optometristaAsistente->id); //Se agrega el id_usuario para que este no vuelva a insertarse de nuevo
                                }
                                break;
                        }
                    }
                }


                //OBTENER CONTRATOS DE ASISTENTES Y OPTOS QUE HAYAN SIDO ELIMINADAS
                $contratosAsistentesOptometristasEliminadas = DB::select("SELECT id, id_usuariocreacion, id_optometrista FROM contratos
                                                WHERE id_franquicia = '$idFranquicia'
                                                AND (estatus_estadocontrato IN (2,4,5,7,10,11,12)
                                                         OR (estatus_estadocontrato = '9' AND esperapoliza = 0))
                                                AND datos = '1' AND (poliza IS NULL OR polizaoptometrista IS NULL)");

                foreach ($contratosAsistentesOptometristasEliminadas as $contratoAsistenteOptometristaEliminada) {
                    //Recorrido de contratos (Agregacion de optos y asistentes)

                    $usuario = DB::select("SELECT id_franquiciaprincipal FROM users WHERE id = '" . $contratoAsistenteOptometristaEliminada->id_optometrista  . "'");
                    if ($usuario != null) {
                        //Existe usuario
                        if ($usuario[0]->id_franquiciaprincipal != null && $usuario[0]->id_franquiciaprincipal == $idFranquicia) {
                            //Tiene algo en id_franquiciaprincipal y la franquicia principal es igual a la franquicia ejecutada
                            //Actualizamos contrato a la poliza
                            DB::update("UPDATE contratos SET polizaoptometrista = '$idPoliza' WHERE id = '" . $contratoAsistenteOptometristaEliminada->id . "' AND polizaoptometrista IS NULL");
                        }

                        //Actualizar abono a la poliza actual
                        DB::update("UPDATE abonos
                                            SET poliza = '$idPoliza'
                                            WHERE id_usuario = '" . $contratoAsistenteOptometristaEliminada->id_optometrista . "'
                                            AND id_franquicia = '$idFranquicia'
                                            AND poliza IS NULL");//Actualizamos los abonos

                    }

                    if (!in_array($contratoAsistenteOptometristaEliminada->id_optometrista, $idsOptometristas)) {
                        //No existe el id_optometrista en el array
                        array_push($idsOptometristas, $contratoAsistenteOptometristaEliminada->id_optometrista); //Se agrega el id_optometrista para que este no vuelva a insertarse de nuevo
                    }

                    $usuario = DB::select("SELECT rol_id, id_franquiciaprincipal FROM users WHERE id = '" . $contratoAsistenteOptometristaEliminada->id_usuariocreacion  . "'");
                    if ($usuario != null) {
                        //Existe usuario
                        if ($usuario[0]->rol_id == 13) {
                            //Rol es igual a ASISTENTE
                            if ($usuario[0]->id_franquiciaprincipal != null && $usuario[0]->id_franquiciaprincipal == $idFranquicia) {
                                //Tiene algo en id_franquiciaprincipal y la franquicia principal es igual a la franquicia ejecutada
                                //Actualizamos contrato a la poliza
                                DB::update("UPDATE contratos SET poliza = '$idPoliza' WHERE id = '" . $contratoAsistenteOptometristaEliminada->id . "' AND poliza IS NULL");

                                if (!in_array($contratoAsistenteOptometristaEliminada->id_usuariocreacion, $idsAsistentes)) {
                                    //No existe el id_usuariocreacion en el array
                                    array_push($idsAsistentes, $contratoAsistenteOptometristaEliminada->id_usuariocreacion); //Se agrega el id_usuariocreacion para que este no vuelva a insertarse de nuevo
                                }
                            }

                            //Actualizar abono a la poliza actual
                            DB::update("UPDATE abonos
                                            SET poliza = '$idPoliza'
                                            WHERE id_usuario = '" . $contratoAsistenteOptometristaEliminada->id_usuariocreacion . "'
                                            AND id_franquicia = '$idFranquicia'
                                            AND poliza IS NULL");//Actualizamos los abonos

                        }
                    }

                }

                \Log::info("Log 2");

                foreach ($idsOptometristas as $idOptometrista) {
                    //Recorrido para calculo de ventas optometristas

                    $ventaOpto = self::calculosVentasOptos($idPoliza, $polizaAnteriorId, $idOptometrista); //Obtenemos las ventas de Optometrista

                    if ($ventaOpto != null) {
                        //Existen ventas de la optometrista

                        $idOpto = $ventaOpto[0]->id;
                        $nombreOpto = $ventaOpto[0]->name;
                        $rolOpto = $ventaOpto[0]->rol; //Rol obtenido de la tabla de polizaventasdias de la poliza anterior
                        $acumuladas = $ventaOpto[0]->acumuladas;
                        $ingresosventasacumulado = $ventaOpto[0]->ingresosventasacumulado == null ? 0 : $ventaOpto[0]->ingresosventasacumulado;
                        if ($hoyNumero == 2) {
                            //Es martes
                            $acumuladas = 0;
                            $ingresosventasacumulado = 0;
                        }
                        $diaActual = $ventaOpto[0]->diaActual;
                        $acumuladasTotal = $diaActual + $acumuladas;
                        $ingresosGotas = $ventaOpto[0]->gotas;
                        $ingresoEnganche = $ventaOpto[0]->enganche;
                        $ingresoAbonos = $ventaOpto[0]->abonos;
                        $ingresoPoliza = $ventaOpto[0]->polizas;

                        $polizagastosgotas = $ventaOpto[0]->polizagastosgotas;
                        $piezasgotas = $ventaOpto[0]->piezasgotas;
                        if ($polizagastosgotas > 0 && $piezasgotas > 0) {
                            //Se vendieron gotas
                            DB::table('gastos')->insert([
                                'id_poliza' => $idPoliza, 'descripcion' => "Pago gotas para optometrista $nombreOpto por la cantidad de $piezasgotas piezas con total de $$polizagastosgotas",
                                'monto' => $polizagastosgotas, 'tipogasto' => 9, 'created_at' => Carbon::now(), 'id_usuario' => $idOpto
                            ]);
                            DB::table('historialpoliza')->insert([
                                'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                                'cambios' => "Agregó un gasto de ventas con un monto de $" . $polizagastosgotas
                                    . ", con la descripción: Pago gotas para optometrista $nombreOpto por la cantidad de $piezasgotas piezas con total de $$polizagastosgotas"
                            ]);//Se agrega idUsuario de Sistema automatico

                        }
                        $polizagastospolizas = $ventaOpto[0]->polizagastospolizas;
                        $piezaspolizas = $ventaOpto[0]->piezaspolizas;
                        if ($polizagastospolizas > 0 && $piezaspolizas > 0) {
                            //Se vendieron gotas
                            DB::table('gastos')->insert([
                                'id_poliza' => $idPoliza, 'descripcion' => "Pago polizas para optometrista $nombreOpto por la cantidad de $piezaspolizas piezas con total de $$polizagastospolizas",
                                'monto' => $polizagastospolizas, 'tipogasto' => 10, 'created_at' => Carbon::now(), 'id_usuario' => $idOpto
                            ]);
                            DB::table('historialpoliza')->insert([
                                'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                                'cambios' => "Agregó un gasto de ventas con un monto de $" . $polizagastospolizas
                                    . ", con la descripción: Pago polizas para optometrista $nombreOpto por la cantidad de $piezaspolizas piezas con total de $$polizagastospolizas"
                            ]);//Se agrega idUsuario de Sistema automatico
                        }

                        $ingresosVentas = ($ingresoPoliza == null ? 0 : $ingresoPoliza) + ($ingresoEnganche == null ? 0 : $ingresoEnganche)
                                            + ($ingresosGotas == null ? 0 : $ingresosGotas) + ($ingresoAbonos == null ? 0 : $ingresoAbonos);
                        $ingresosVentasAcumuladas = $ingresosVentas + $ingresosventasacumulado;

                        $ventasOptosQuery = self::obtenerQueryVentasOptos($polizaAnteriorId, $hoyNumero, $diaActual, $idFranquicia, $idOpto, $rolOpto);
                        $query = "INSERT INTO polizaventasdias (id,id_franquicia,id_usuario,rol,id_poliza,fechapoliza,fechapolizacierre,nombre,lunes,martes,miercoles,jueves,viernes,
                                                                        sabado,acumuladas,asistencia,ingresosgotas,ingresosenganche,ingresospoliza,totaldia,ingresosventas,ingresosventasacumulado,ingresosabonos)
                                        VALUES(null,'$idFranquicia','$idOpto','12','$idPoliza','$hoy',null,'$nombreOpto'," . $ventasOptosQuery . ",'$acumuladasTotal',null,'$ingresosGotas',
                                                    '$ingresoEnganche','$ingresoPoliza',null,'$ingresosVentas','$ingresosVentasAcumuladas','$ingresoAbonos')";

                        DB::insert($query);
                    }

                }

                \Log::info("Log 3");

                foreach ($idsAsistentes as $idAsistente) {
                    //Recorrido para calculo de ventas asistentes

                    $ventaAsis = self::calculosVentasAsis($idPoliza, $polizaAnteriorId, $idAsistente); //Obtenemos las ventas de Asistente

                    if ($ventaAsis != null) {
                        //Existen ventas de la asistente

                        $idOpto = $ventaAsis[0]->id;
                        $nombreOpto = $ventaAsis[0]->name;
                        $rolOpto = $ventaAsis[0]->rol; //Rol obtenido de la tabla de polizaventasdias de la poliza anterior
                        $acumuladas = $ventaAsis[0]->acumuladas;
                        if ($hoyNumero == 2) {
                            //Es martes
                            $acumuladas = 0;
                        }
                        $diaActual = $ventaAsis[0]->diaActual;
                        $acumuladasTotal = $diaActual + $acumuladas;
                        $ingresosGotas = 0;
                        $ingresoEnganche = 0;
                        $ingresoAbonos = 0;
                        $ingresoPoliza = 0;
                        $ingresosVentas = 0;
                        $ingresosVentasAcumuladas = 0;

                        $ventasOptosQuery = self::obtenerQueryVentasOptos($polizaAnteriorId, $hoyNumero, $diaActual, $idFranquicia, $idOpto, $rolOpto);
                        $query = "INSERT INTO polizaventasdias (id,id_franquicia,id_usuario,rol,id_poliza,fechapoliza,fechapolizacierre,nombre,lunes,martes,miercoles,jueves,viernes,sabado,
                                                                        acumuladas,asistencia,ingresosgotas,ingresosenganche,ingresospoliza,totaldia,ingresosventas,ingresosventasacumulado,ingresosabonos)
                                        VALUES(null,'$idFranquicia','$idOpto','13','$idPoliza','$hoy',null,'$nombreOpto'," . $ventasOptosQuery . ",'$acumuladasTotal',null,'$ingresosGotas',
                                                    '$ingresoEnganche','$ingresoPoliza',null,'$ingresosVentas','$ingresosVentasAcumuladas','$ingresoAbonos')";
                        DB::insert($query);

                    }
                }

                \Log::info("Log 4");

                foreach ($idsOptometristas as $idOptometrista) {
                    //Recorrido para calculo de productividad optometristas

                    $productividadOpto = self::calculoProductividadOptos($idPoliza, $polizaAnteriorId, $idOptometrista); //Obtenemos la productividad de Optometrista

                    if ($productividadOpto != null) {
                        //Existe productividad de la optometrista

                        $idOpto = $productividadOpto[0]->ID;
                        $sueldo = $productividadOpto[0]->SUELDO;
                        $ECOJRANT = $productividadOpto[0]->ECOJRANT == null ? 0 : $productividadOpto[0]->ECOJRANT;
                        $JUNIORANT = $productividadOpto[0]->JUNIORANT == null ? 0 : $productividadOpto[0]->JUNIORANT;
                        $DORADOUNOANT = $productividadOpto[0]->DORADOUNOANT == null ? 0 : $productividadOpto[0]->DORADOUNOANT;
                        $DORADODOSANT = $productividadOpto[0]->DORADODOSANT == null ? 0 : $productividadOpto[0]->DORADODOSANT;
                        $PLATINOANT = $productividadOpto[0]->PLATINOANT == null ? 0 : $productividadOpto[0]->PLATINOANT;
                        $PREMIUMANT = $productividadOpto[0]->PREMIUMANT == null ? 0 : $productividadOpto[0]->PREMIUMANT;
                        if ($hoyNumero == 2) {
                            //Es martes
                            $ECOJRANT = 0;
                            $JUNIORANT = 0;
                            $DORADOUNOANT = 0;
                            $DORADODOSANT = 0;
                            $PLATINOANT = 0;
                            $PREMIUMANT = 0;
                        }
                        $ECOJR = $productividadOpto[0]->ECOJR == null ? 0 : $productividadOpto[0]->ECOJR;
                        $totalEcoAcu = $ECOJRANT + $ECOJR;
                        $JUNIOR = $productividadOpto[0]->JUNIOR == null ? 0 : $productividadOpto[0]->JUNIOR;
                        $totalJrAcu = $JUNIORANT + $JUNIOR;
                        $DORADOUNO = $productividadOpto[0]->DORADOUNO == null ? 0 : $productividadOpto[0]->DORADOUNO;
                        $totalDoradoAcu = $DORADOUNOANT + $DORADOUNO;
                        $DORADODOS = $productividadOpto[0]->DORADODOS == null ? 0 : $productividadOpto[0]->DORADODOS;
                        $totalDoradoDosAcu = $DORADODOSANT + ($DORADODOS == 0 ? $DORADODOS : ($DORADODOS / 2));
                        $PLATINO = $productividadOpto[0]->PLATINO == null ? 0 : $productividadOpto[0]->PLATINO;
                        $totalPlatinoAcu = $PLATINOANT + $PLATINO;
                        $PREMIUM = $productividadOpto[0]->PREMIUM == null ? 0 : $productividadOpto[0]->PREMIUM;
                        $totalPremiumAcu = $PREMIUMANT + $PREMIUM;
                        $numeroVentas = $totalEcoAcu + $totalJrAcu + $totalDoradoAcu + $totalDoradoDosAcu + $totalPlatinoAcu + $totalPremiumAcu;
                        $productividad = ($numeroVentas * 100) / 30;
                        $insumos = ($productividadOpto[0]->INSUMOS == null ? 0 : $productividadOpto[0]->INSUMOS) * $numeroVentas;

                        DB::table("polizaproductividad")->insert([
                            "id_franquicia" => $idFranquicia,
                            "id_poliza" => $idPoliza,
                            "id_usuario" => $idOpto,
                            "rol" => '12',
                            "sueldo" => $sueldo,
                            "totaleco" => $totalEcoAcu,
                            "totaljr" => $totalJrAcu,
                            "totaldoradouno" => $totalDoradoAcu,
                            "totaldoradodos" => $totalDoradoDosAcu,
                            "totalplatino" => $totalPlatinoAcu,
                            "totalpremium" => $totalPremiumAcu,
                            "numeroventas" => $numeroVentas,
                            "productividad" => $productividad,
                            "insumos" => $insumos
                        ]);

                    }

                }

                \Log::info("Log 5");

                foreach ($idsAsistentes as $idAsistente) {

                    $productividadAsi = self::calculoProductividadAsis($idPoliza, $polizaAnteriorId, $idAsistente); //Obtenemos la productividad de Asistente

                    if ($productividadAsi != null) {
                        //Existe productividad de la asistente

                        $idAsis = $productividadAsi[0]->ID;
                        $sueldo = $productividadAsi[0]->SUELDO;
                        $ECOJRANT = $productividadAsi[0]->ECOJRANT == null ? 0 : $productividadAsi[0]->ECOJRANT;
                        $JUNIORANT = $productividadAsi[0]->JUNIORANT == null ? 0 : $productividadAsi[0]->JUNIORANT;
                        $DORADOUNOANT = $productividadAsi[0]->DORADOUNOANT == null ? 0 : $productividadAsi[0]->DORADOUNOANT;
                        $DORADODOSANT = $productividadAsi[0]->DORADODOSANT == null ? 0 : $productividadAsi[0]->DORADODOSANT;
                        $PLATINOANT = $productividadAsi[0]->PLATINOANT == null ? 0 : $productividadAsi[0]->PLATINOANT;
                        $PREMIUMANT = $productividadAsi[0]->PREMIUMANT == null ? 0 : $productividadAsi[0]->PREMIUMANT;
                        if ($hoyNumero == 2) {
                            //Es martes
                            $ECOJRANT = 0;
                            $JUNIORANT = 0;
                            $DORADOUNOANT = 0;
                            $DORADODOSANT = 0;
                            $PLATINOANT = 0;
                            $PREMIUMANT = 0;
                        }
                        $ECOJR = $productividadAsi[0]->ECOJR == null ? 0 : $productividadAsi[0]->ECOJR;
                        $totalEcoAcu = $ECOJRANT + $ECOJR;
                        $JUNIOR = $productividadAsi[0]->JUNIOR == null ? 0 : $productividadAsi[0]->JUNIOR;
                        $totalJrAcu = $JUNIORANT + $JUNIOR;
                        $DORADOUNO = $productividadAsi[0]->DORADOUNO == null ? 0 : $productividadAsi[0]->DORADOUNO;
                        $totalDoradoAcu = $DORADOUNOANT + $DORADOUNO;
                        $DORADODOS = $productividadAsi[0]->DORADODOS == null ? 0 : $productividadAsi[0]->DORADODOS;
                        $totalDoradoDosAcu = $DORADODOSANT + ($DORADODOS == 0 ? $DORADODOS : ($DORADODOS / 2));
                        $PLATINO = $productividadAsi[0]->PLATINO == null ? 0 : $productividadAsi[0]->PLATINO;
                        $totalPlatinoAcu = $PLATINOANT + $PLATINO;
                        $PREMIUM = $productividadAsi[0]->PREMIUM == null ? 0 : $productividadAsi[0]->PREMIUM;
                        $totalPremiumAcu = $PREMIUMANT + $PREMIUM;
                        $numeroVentas = $totalEcoAcu + $totalJrAcu + $totalDoradoAcu + $totalDoradoDosAcu + $totalPlatinoAcu + $totalPremiumAcu;
                        $productividad = ($numeroVentas * 100) / 10;
                        $insumos = $productividadAsi[0]->INSUMOS == null ? 0 : $productividadAsi[0]->INSUMOS;

                        DB::table("polizaproductividad")->insert([
                            "id_franquicia" => $idFranquicia,
                            "id_poliza" => $idPoliza,
                            "id_usuario" => $idAsis,
                            "rol" => '13',
                            "sueldo" => $sueldo,
                            "totaleco" => $totalEcoAcu,
                            "totaljr" => $totalJrAcu,
                            "totaldoradouno" => $totalDoradoAcu,
                            "totaldoradodos" => $totalDoradoDosAcu,
                            "totalplatino" => $totalPlatinoAcu,
                            "totalpremium" => $totalPremiumAcu,
                            "numeroventas" => $numeroVentas,
                            "productividad" => $productividad,
                            "insumos" => $insumos
                        ]);

                    }

                }

                \Log::info("Log 6");

                self::calculoDeCobranza($idFranquicia, $idPoliza, $polizaAnteriorId, "");

                \Log::info("Log 7");

                $totalUltimaPoliza = $polizaAnterior[0]->total == null ? 0 : $polizaAnterior[0]->total;

                DB::table('gastos')->insert([
                    'id_poliza' => $idPoliza, 'descripcion' => "Pago saldo poliza anterior",
                    'monto' => $totalUltimaPoliza, 'tipogasto' => 3, 'created_at' => Carbon::now()
                ]);
                DB::table('historialpoliza')->insert([
                    'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                    'cambios' => "Agregó un gasto de otros con un monto de $" . $totalUltimaPoliza
                        . ", con la descripción: Pago saldo poliza anterior con total de $$totalUltimaPoliza"
                ]);//Se agrega idUsuario de Sistema automatico

                self::calcularTotales($idFranquicia, $idPoliza, $hoyFormato);

                \Log::info("Log 8");

            } else {
                \Log::info("Ya existe la poliza del dia franquicia " . $idFranquicia);
            }
        }

    }

    public static function actualizarTotalGasolinaPorUsuario($idFranquicia, $idPoliza, $idUsuario)
    {

        $poliza = DB::select("SELECT id, created_at FROM poliza WHERE id = '$idPoliza'");

        $sumatipocobranzatotal = 0;
        if ($poliza != null) {
            //Existe poliza
            $fechapoliza = Carbon::parse($poliza[0]->created_at)->format('Y-m-d');
            $hoyNumero = Carbon::parse($fechapoliza)->dayOfWeekIso;

            if ($hoyNumero != 2) {
                //Dia diferente de martes

                $fecha = Carbon::parse($fechapoliza)->format('Y-m-d');

                $hoyNumeroTemporal = $hoyNumero;
                if ($hoyNumero == 1) {
                    //Es lunes
                    $hoyNumeroTemporal = 8;
                }

                for ($i = ($hoyNumeroTemporal - 2); $i > 0; $i--) {

                    //Obtener fechas de dias anteriores
                    $fecha = Carbon::create($fecha)->subDays(1)->format('Y-m-d'); //Descontando dias
                    $polizaAnterior = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' AND STR_TO_DATE(created_at,'%Y-%m-%d') = STR_TO_DATE('$fecha','%Y-%m-%d')");

                    if($polizaAnterior != null) {
                        //Existe poliza
                        $tipocobranza = DB::select("SELECT COALESCE(SUM(cantidad),0) as sumatipocobranza FROM tipocobranza WHERE id_poliza = '" . $polizaAnterior[0]->id . "' AND id_usuario = '$idUsuario'");
                        $sumatipocobranza = $tipocobranza == null ? 0 : $tipocobranza[0]->sumatipocobranza;
                        $sumatipocobranzatotal += $sumatipocobranza;
                    }

                }

            }

            $tipocobranza = DB::select("SELECT COALESCE(SUM(cantidad),0) as sumatipocobranza FROM tipocobranza WHERE id_poliza = '$idPoliza' AND id_usuario = '$idUsuario'");
            $sumatipocobranza = $tipocobranza == null ? 0 : $tipocobranza[0]->sumatipocobranza;
            $sumatipocobranzatotal += $sumatipocobranza;

            DB::table('polizacobranza')->where([['id_franquicia', '=', $idFranquicia], ['id_poliza', '=', $idPoliza], ['id_usuario', '=', $idUsuario]])->update([
                'gas' => $sumatipocobranzatotal
            ]);
        }

        return $sumatipocobranzatotal;

    }

    public static function buscarasistenciapolizaeinsertar($idFranquicia, $idUsuario)
    {

        $polizaDia = DB::select("SELECT p.id FROM poliza p WHERE p.id_franquicia = '$idFranquicia' ORDER BY p.created_at DESC LIMIT 1");

        if ($polizaDia != null) {
            //Existe poliza

            $idPoliza = $polizaDia[0]->id;
            $hoy = Carbon::now();

            $bandera = true;
            $existeAsistencia = DB::select("SELECT a.id, a.id_poliza FROM asistencia a WHERE a.id_usuario = '$idUsuario' AND STR_TO_DATE(a.created_at,'%Y-%m-%d') = STR_TO_DATE('$hoy','%Y-%m-%d')");
            if ($existeAsistencia != null) {
                //Existe asistencia
                if ($existeAsistencia[0]->id_poliza != $idPoliza) {
                    //Es diferente la poliza donde esta registrada la asistencia
                    DB::delete("DELETE FROM asistencia WHERE id = '" . $existeAsistencia[0]->id . "' AND id_poliza = '" . $existeAsistencia[0]->id_poliza . "'");
                } else {
                    //Es igual la poliza donde esta registrada la asistencia
                    $bandera = false;
                }
            }

            if ($bandera) {
                //Insertar en tabla asistencia
                DB::table("asistencia")->insert([
                    "id_poliza" => $idPoliza, "id_usuario" => $idUsuario, "id_tipoasistencia" => '0', 'created_at' => $hoy]);
            }

        }

    }

}//clase
