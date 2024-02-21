<?php

namespace App\Clases;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use App\Clases\calculofechaspago;

class contratosGlobal
{

    public static function calculoCantidadFormaDePago($idFranquicia, $formaDePago)
    {
        //Obtener cantidad para abono minimo de la sucursal si forma de pago es diferente a de contado
        if($formaDePago != 0){
            $cantidadAbonoMinimo = DB::select("SELECT amf.abonominimo FROM abonominimofranquicia amf
                                                             WHERE amf.id_franquicia = '$idFranquicia' AND amf.pago = '$formaDePago'");
            //Retornar valor
            return $cantidadAbonoMinimo[0]->abonominimo;
        }
    }

    public static function getFolioId()
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $randomFolio2 = self::generadorRandom2();
            $existente = DB::select("select id from abonos where id = '$randomFolio2'");
            if (sizeof($existente) == 0) {
                $unico = $randomFolio2;
                $esUnico = true;
            }
        }
        return $unico;
    }

    public static function generadorRandom2($length = 5)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($caracteres);
        $randomFolio2 = '';
        for ($i = 0; $i < $length; $i++) {
            $randomFolio2 .= $caracteres[rand(0, $charactersLength - 1)];
        }
        return $randomFolio2;
    }

    public static function validarSiExisteFolioAlfanumericoEnAbonosContrato($idContrato)
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $randomFolio2 = self::generadorRandom2();
            $existente = DB::select("select folio from abonos where folio = '$randomFolio2' AND id_contrato = '$idContrato'");
            if (sizeof($existente) == 0) {
                $unico = $randomFolio2;
                $esUnico = true;
            }
        }
        return $unico;
    }

    public static function calculoTotal($idContrato)
    {

        self::actualizarTotalProductoContrato($idContrato);
        self::actualizarTotalAbonoContrato($idContrato);

        if (self::obtenerEstadoPromocion($idContrato)) {
            //Tiene promocion y esta activa
            $promocionterminada = DB::select("SELECT promocionterminada FROM contratos where id = '$idContrato'");
            if ($promocionterminada != null) {
                if ($promocionterminada[0]->promocionterminada == 1) {
                    //Promocion ha sido terminada
                    DB::update("UPDATE contratos
                        SET total = coalesce(totalpromocion,0)  + coalesce(totalproducto,0) - coalesce(totalabono,0)
                        WHERE idcontratorelacion = '$idContrato' OR id ='$idContrato'");
                } else {
                    //Promocion no ha sido terminada
                    DB::update("UPDATE contratos
                    SET total = coalesce(totalhistorial,0) + coalesce(totalproducto,0) - coalesce(totalabono,0)
                    WHERE idcontratorelacion = '$idContrato' OR id ='$idContrato'");
                }
            }
        } else {
            //No tiene promocion o existe la promocion pero esta desactivada
            DB::update("UPDATE contratos
                    SET total = coalesce(totalhistorial,0) + coalesce(totalproducto,0) - coalesce(totalabono,0)
                    WHERE idcontratorelacion = '$idContrato' OR id ='$idContrato'");
        }
        //Actualizar campos de totales tabla contratos lista temporales
        self::actualizarTotalContratosListaTemporales($idContrato);
    }

    private static function obtenerEstadoPromocion($idContrato)
    {
        $respuesta = false;

        $contrato = DB::select("SELECT idcontratorelacion FROM contratos WHERE id = '$idContrato'");
        if ($contrato[0]->idcontratorelacion != null) {
            //Es un contrato hijo
            $idContrato = $contrato[0]->idcontratorelacion;
        }

        $promocioncontrato = DB::select("SELECT * FROM promocioncontrato WHERE id_contrato = '$idContrato'");

        if ($promocioncontrato != null) {
            if ($promocioncontrato[0]->estado == 1) {
                //Promocion esta activa
                $respuesta = true;
            }
        }
        return $respuesta;
    }

    private static function actualizarTotalProductoContrato($idContrato)
    {
        DB::update("UPDATE contratos c
                    SET c.totalproducto = coalesce((SELECT SUM(cp.total) FROM contratoproducto cp WHERE cp.id_contrato = c.id), 0)
                    WHERE c.id = '$idContrato'");
    }

    private static function actualizarTotalAbonoContrato($idContrato)
    {
        DB::update("UPDATE contratos c
                    SET c.totalabono = coalesce((SELECT SUM(a.abono) FROM abonos a WHERE a.id_contrato = c.id), 0)
                    WHERE c.id = '$idContrato'");
    }

    private static function actualizarTotalContratosListaTemporales($idContrato){
        //Actualizar total productos, total abonos y total del contrato - Tabla contratoslistatemporales
        DB::update("UPDATE contratoslistatemporales ct
                    SET ct.totalproducto = (SELECT c.totalproducto FROM contratos c WHERE c.id = ct.id),
                        ct.totalabono = (SELECT c.totalabono FROM contratos c WHERE c.id = ct.id),
                        ct.total = (SELECT c.total FROM contratos c WHERE c.id = ct.id)
                    WHERE ct.id = '$idContrato'");

    }

    public static function obtenerVentasSemana($usuarios, $opcion, $porPoliza, $rangoFechas)
    {

        if($porPoliza){
            //optener ventas base a poliza

            foreach ($usuarios as $usuario) {
                $consultaVentas = DB::select("SELECT * FROM polizaventasdias pvd WHERE pvd.id_usuario = '" . $usuario->id . "' ORDER BY  STR_TO_DATE(pvd.fechapoliza,'%Y-%m-%d') DESC LIMIT 1");

                if($consultaVentas != null) {

                    $usuario->ventasLunes = $consultaVentas[0]->lunes;
                    $usuario->ventasMartes = $consultaVentas[0]->martes;
                    $usuario->ventasMiercoles = $consultaVentas[0]->miercoles;
                    $usuario->ventasJueves = $consultaVentas[0]->jueves;
                    $usuario->ventasViernes = $consultaVentas[0]->viernes;
                    $usuario->ventasSabado = $consultaVentas[0]->sabado;
                    $usuario->totalVentas = $consultaVentas[0]->acumuladas;

                    //Calculo de fechas para contratos cancelados o lio fuga
                    $fecha = Carbon::parse($consultaVentas[0]->fechapoliza)->format("Y-m-d");
                    $diaActual = date("w", strtotime($fecha));
                    $diasAtrasar = ($diaActual + 6) % 7;
                    $fechaLunes = date("Y-m-d", strtotime("- $diasAtrasar days", strtotime($fecha)));
                    $diasSabado = 6 - $diaActual;
                    $fechaSabado = date("Y-m-d", strtotime("+ $diasSabado days", strtotime($fecha)));

                    //Contratos Rechazados - Cancelados - Lio/Fuga
                    switch ($opcion) {
                        case 0:
                            //Condicion para ventas de Optometristas
                            $condicionVenta = "(c.id_usuariocreacion = '" . $usuario->id . "' OR c.id_optometrista = '" . $usuario->id . "')";
                            break;

                        case 1:
                            //Condicion para ventas de Asistentes
                            $condicionVenta = "c.id_usuariocreacion = '" . $usuario->id . "'";
                            break;
                    }

                    $consultaRechazados = DB::select("SELECT COUNT(c.id) as totalRechazados FROM contratos c
                                               INNER JOIN franquicias f ON f.id = c.id_franquicia
                                               WHERE  $condicionVenta
                                               AND (c.estatus_estadocontrato = 6 OR c.estatus_estadocontrato = 8 OR c.estatus_estadocontrato = 14)
                                               AND STR_TO_DATE(c.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaLunes','%Y-%m-%d') AND STR_TO_DATE('$fechaSabado','%Y-%m-%d')");

                    $usuario->ventasRechazadas = $consultaRechazados[0]->totalRechazados;
                }else{
                    $usuario->ventasLunes = null;
                    $usuario->ventasMartes = null;
                    $usuario->ventasMiercoles = null;
                    $usuario->ventasJueves = null;
                    $usuario->ventasViernes = null;
                    $usuario->ventasSabado = null;
                    $usuario->ventasRechazadas = 0;
                    $usuario->totalVentas = null;
                }
            }

        }else {
            //Obtener perido de fechas a consultar
            $now = Carbon::now();
            $nowParse = Carbon::parse($now)->format('Y-m-d');
            $polizaGlobales = new polizaGlobales();
            $numeroDia = $now->dayOfWeekIso;    //Obtenemos el dia de la semana actual
            $fechaLunes = $nowParse;    //Obtener fecha con formato

            //Por default se tarer fecha lunes semana actual
            if ($numeroDia != 1 && $numeroDia != 7) {
                //Si no es lunes y domingo
                $fechaLunes = $polizaGlobales::obtenerDia($numeroDia, 1);   //se obtenie la fecha del lunes anterior a la fecha actaul
            }
            if ($numeroDia == 7) {
                //Si es domigo obtenemos la fecha del lunes pasado
                $fechaLunes = date("Y-m-d", strtotime($nowParse . "- 6 days"));
                $numeroDia = 6; //Dia semana lo tomaremos como sabado por ser ultimo dia de trabajo
            }

            if($rangoFechas != null){
                //Se selecciono un rango de fechas
                $arrayFechas = explode(" ",$rangoFechas);
                if(Carbon::parse($arrayFechas[0])->format("Y-m-d") < Carbon::parse($fechaLunes)->format("Y-m-d")){
                    //Si fecha lunes seleccionado es diferente a fecha lunes semana actual
                    $fechaLunes = Carbon::parse($arrayFechas[0])->format("Y-m-d");
                    $numeroDia = 6; //Dia sera sabado para traer reporte completo de una semana pasada
                }
            }

            $fechaMartes = date("Y-m-d", strtotime($fechaLunes . "+ 1 days"));
            $fechaMiercoles = date("Y-m-d", strtotime($fechaLunes . "+ 2 days"));
            $fechaJueves = date("Y-m-d", strtotime($fechaLunes . "+ 3 days"));
            $fechaViernes = date("Y-m-d", strtotime($fechaLunes . "+ 4 days"));
            $fechaSabado = date("Y-m-d", strtotime($fechaLunes . "+ 5 days"));

            //Opciones - $opcion
            // 0 -> Ventas para Optometristas
            //1 -> Ventas para Asistentes

            foreach ($usuarios as $usuario) {

                switch ($opcion) {
                    case 0:
                        //Condicion para ventas de Optometristas
                        $condicionVenta = "(c.id_usuariocreacion = '" . $usuario->id . "' OR c.id_optometrista = '" . $usuario->id . "')";
                        break;

                    case 1:
                        //Condicion para ventas de Asistentes
                        $condicionVenta = "c.id_usuariocreacion = '" . $usuario->id . "'";
                        break;
                }

                //Consulta ventas
                $consultaVentas = DB::select("SELECT distinct (r.id_contrato), STR_TO_DATE(r.created_at,'%Y-%m-%d') as fechaAprobado FROM registroestadocontrato r
                                               INNER JOIN contratos c ON c.id = r.id_contrato
                                                WHERE $condicionVenta
                                                AND r.estatuscontrato IN (7) AND c.estatus_estadocontrato IN (7,10,11,12,2,4,5)
                                                AND NOT EXISTS (SELECT * FROM garantias g WHERE g.id_contrato = c.id)
                                                AND STR_TO_DATE(r.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaLunes','%Y-%m-%d') AND STR_TO_DATE('$fechaSabado','%Y-%m-%d')
                                                ORDER BY STR_TO_DATE(r.created_at,'%Y-%m-%d') DESC");

                //Eliminar aquellos contratos aprobados varias veces en el periodo de tiempo de la semana - dejamos el mas reciente
                $consultaVentas = self::eliminarVentaRepetidaReporteContratosVentasAsistentesOptometristas($consultaVentas);

                //Contratos Rechazados - Cancelados - Lio/Fuga
                $consultaRechazados = DB::select("SELECT COUNT(c.id) as totalRechazados FROM contratos c
                                               INNER JOIN franquicias f ON f.id = c.id_franquicia
                                               WHERE  $condicionVenta
                                               AND (c.estatus_estadocontrato = 6 OR c.estatus_estadocontrato = 8 OR c.estatus_estadocontrato = 14)
                                               AND STR_TO_DATE(c.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaLunes','%Y-%m-%d') AND STR_TO_DATE('$nowParse','%Y-%m-%d')");

                //Variables de conteo
                $totalLunes = 0;
                $totalMartes = 0;
                $totalMiercoles = 0;
                $totalJueves = 0;
                $totalViernes = 0;
                $totalSabado = 0;

                foreach ($consultaVentas as $datos) {

                    if ($datos->fechaAprobado == $fechaLunes) {
                        //Suma contrato aprobados en Lunes
                        $totalLunes = $totalLunes + 1;
                    }
                    if ($datos->fechaAprobado == $fechaMartes) {
                        //Suma contrato aprobados en Martes
                        $totalMartes = $totalMartes + 1;
                    }
                    if ($datos->fechaAprobado == $fechaMiercoles) {
                        //Suma contrato aprobados en Miercoles
                        $totalMiercoles = $totalMiercoles + 1;
                    }
                    if ($datos->fechaAprobado == $fechaJueves) {
                        //Suma contrato aprobados en Jueves
                        $totalJueves = $totalJueves + 1;
                    }
                    if ($datos->fechaAprobado == $fechaViernes) {
                        //Suma contrato aprobados en Viernes
                        $totalViernes = $totalViernes + 1;
                    }
                    if ($datos->fechaAprobado == $fechaSabado) {
                        //Suma contrato aprobados en Sabado
                        $totalSabado = $totalSabado + 1;
                    }

                }

                switch ($numeroDia) {

                    case 1:
                        //Lunes
                        $usuario->ventasLunes = $totalLunes;
                        $usuario->ventasMartes = null;
                        $usuario->ventasMiercoles = null;
                        $usuario->ventasJueves = null;
                        $usuario->ventasViernes = null;
                        $usuario->ventasSabado = null;
                        $usuario->ventasRechazadas = $consultaRechazados[0]->totalRechazados;
                        $usuario->totalVentas = $totalLunes;
                        break;

                    case 2:
                        //Martes
                        $usuario->ventasLunes = $totalLunes;
                        $usuario->ventasMartes = $totalMartes;
                        $usuario->ventasMiercoles = null;
                        $usuario->ventasJueves = null;
                        $usuario->ventasViernes = null;
                        $usuario->ventasSabado = null;
                        $usuario->ventasRechazadas = $consultaRechazados[0]->totalRechazados;
                        $usuario->totalVentas = $totalLunes + $totalMartes;
                        break;

                    case 3:
                        //Miercoles
                        $usuario->ventasLunes = $totalLunes;
                        $usuario->ventasMartes = $totalMartes;
                        $usuario->ventasMiercoles = $totalMiercoles;
                        $usuario->ventasJueves = null;
                        $usuario->ventasViernes = null;
                        $usuario->ventasSabado = null;
                        $usuario->ventasRechazadas = $consultaRechazados[0]->totalRechazados;
                        $usuario->totalVentas = $totalLunes + $totalMartes + $totalMiercoles;
                        break;

                    case 4:
                        //Jueves
                        $usuario->ventasLunes = $totalLunes;
                        $usuario->ventasMartes = $totalMartes;
                        $usuario->ventasMiercoles = $totalMiercoles;
                        $usuario->ventasJueves = $totalJueves;
                        $usuario->ventasViernes = null;
                        $usuario->ventasSabado = null;
                        $usuario->ventasRechazadas = $consultaRechazados[0]->totalRechazados;
                        $usuario->totalVentas = $totalLunes + $totalMartes + $totalMiercoles + $totalJueves;
                        break;

                    case 5:
                        //Viernes
                        $usuario->ventasLunes = $totalLunes;
                        $usuario->ventasMartes = $totalMartes;
                        $usuario->ventasMiercoles = $totalMiercoles;
                        $usuario->ventasJueves = $totalJueves;
                        $usuario->ventasViernes = $totalViernes;
                        $usuario->ventasSabado = null;
                        $usuario->ventasRechazadas = $consultaRechazados[0]->totalRechazados;
                        $usuario->totalVentas = $totalLunes + $totalMartes + $totalMiercoles + $totalJueves + $totalViernes;
                        break;

                    case 6:
                        //Sabado
                        $usuario->ventasLunes = $totalLunes;
                        $usuario->ventasMartes = $totalMartes;
                        $usuario->ventasMiercoles = $totalMiercoles;
                        $usuario->ventasJueves = $totalJueves;
                        $usuario->ventasViernes = $totalViernes;
                        $usuario->ventasSabado = $totalSabado;
                        $usuario->ventasRechazadas = $consultaRechazados[0]->totalRechazados;
                        $usuario->totalVentas = $totalLunes + $totalMartes + $totalMiercoles + $totalJueves + $totalViernes + $totalSabado;
                        break;

                }

            }
        }

        return $usuarios;
    }

    public static function totalVentasPorDia($usuarios)
    {

        $totalVentasPorDia = [0, 0, 0, 0, 0, 0, 0];
        foreach ($usuarios as $usuario) {

            if ($usuario->ventasLunes != null) {
                $totalVentasPorDia[0] = $totalVentasPorDia[0] + $usuario->ventasLunes;
            }
            if ($usuario->ventasMartes != null) {
                $totalVentasPorDia[1] = $totalVentasPorDia[1] + $usuario->ventasMartes;
            }
            if ($usuario->ventasMiercoles != null) {
                $totalVentasPorDia[2] = $totalVentasPorDia[2] + $usuario->ventasMiercoles;
            }
            if ($usuario->ventasJueves != null) {
                $totalVentasPorDia[3] = $totalVentasPorDia[3] + $usuario->ventasJueves;
            }
            if ($usuario->ventasViernes != null) {
                $totalVentasPorDia[4] = $totalVentasPorDia[4] + $usuario->ventasViernes;
            }
            if ($usuario->ventasSabado != null) {
                $totalVentasPorDia[5] = $totalVentasPorDia[5] + $usuario->ventasSabado;
            }
            $totalVentasPorDia[6] = $totalVentasPorDia[6] + $usuario->ventasRechazadas;

        }

        return $totalVentasPorDia;

    }

    public static function insertarDatosTablaContratosTemporalesSincronizacion($idFranquicia, $idUsuario, $idZona, $rol)
    {

        if ($rol == 4) {
            //Cobranza
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
                            IFNULL(c.abonominimo, '') as abonominimo,
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
                            IFNULL(c.calleentrega, '') as calleentrega,
                            IFNULL(c.numeroentrega, '') as numeroentrega,
                            IFNULL(c.deptoentrega, '') as deptoentrega,
                            IFNULL(c.alladodeentrega, '') as alladodeentrega,
                            IFNULL(c.frenteaentrega, '') as frenteaentrega,
                            IFNULL(c.entrecallesentrega, '') as entrecallesentrega,
                            IFNULL(c.coloniaentrega, '') as coloniaentrega,
                            IFNULL(c.localidadentrega, '') as localidadentrega,
                            IFNULL(c.casatipoentrega, '') as casatipoentrega,
                            IFNULL(c.casacolorentrega, '') as casacolorentrega,
                            IFNULL(c.alias, '') as alias,
                            IFNULL(c.opcionlugarentrega, '') as opcionlugarentrega,
                            IFNULL(c.observacionfotoine, '') as observacionfotoine,
                            IFNULL(c.observacionfotoineatras, '') as observacionfotoineatras,
                            IFNULL(c.observacionfotocasa, '') as observacionfotocasa,
                            IFNULL(c.observacioncomprobantedomicilio, '') as observacioncomprobantedomicilio,
                            IFNULL(c.observacionpagare, '') as observacionpagare,
                            IFNULL(c.observacionfotootros, '') as observacionfotootros,
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

        } else {
            //Asistente o Optometrista
            $nowParse = Carbon::now()->format('Y-m-d');
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
                            IFNULL(c.abonominimo, '') as abonominimo,
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
                            IFNULL(c.calleentrega, '') as calleentrega,
                            IFNULL(c.numeroentrega, '') as numeroentrega,
                            IFNULL(c.deptoentrega, '') as deptoentrega,
                            IFNULL(c.alladodeentrega, '') as alladodeentrega,
                            IFNULL(c.frenteaentrega, '') as frenteaentrega,
                            IFNULL(c.entrecallesentrega, '') as entrecallesentrega,
                            IFNULL(c.coloniaentrega, '') as coloniaentrega,
                            IFNULL(c.localidadentrega, '') as localidadentrega,
                            IFNULL(c.casatipoentrega, '') as casatipoentrega,
                            IFNULL(c.casacolorentrega, '') as casacolorentrega,
                            IFNULL(c.alias, '') as alias,
                            IFNULL(c.opcionlugarentrega, '') as opcionlugarentrega,
                            IFNULL(c.observacionfotoine, '') as observacionfotoine,
                            IFNULL(c.observacionfotoineatras, '') as observacionfotoineatras,
                            IFNULL(c.observacionfotocasa, '') as observacionfotocasa,
                            IFNULL(c.observacioncomprobantedomicilio, '') as observacioncomprobantedomicilio,
                            IFNULL(c.observacionpagare, '') as observacionpagare,
                            IFNULL(c.observacionfotootros, '') as observacionfotootros
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

        if ($contratos != null) {
            //Existen contratos

            foreach ($contratos as $contrato) {

                try {

                    $nombrepaquete = "";
                    $ultimoabonoreal = "";
                    $titulopromocion = "";

                    if ($rol == 4) {
                        //Rol cobranza
                        $nombrepaquete = $contrato->nombrepaquete;
                        $ultimoabonoreal = $contrato->ultimoabonoreal;
                        $titulopromocion = $contrato->titulopromocion;
                    }

                    //Insertar en tabla contratostemporalessincronizacion
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
                        'alladode' => $contrato->alladode,
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
                        'abonominimo' => $contrato->abonominimo,
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
                        'calleentrega' => $contrato->calleentrega,
                        'numeroentrega' => $contrato->numeroentrega,
                        'deptoentrega' => $contrato->deptoentrega,
                        'alladodeentrega' => $contrato->alladodeentrega,
                        'frenteaentrega' => $contrato->frenteaentrega,
                        'entrecallesentrega' => $contrato->entrecallesentrega,
                        'coloniaentrega' => $contrato->coloniaentrega,
                        'localidadentrega' => $contrato->localidadentrega,
                        'casatipoentrega' => $contrato->casatipoentrega,
                        'casacolorentrega' => $contrato->casacolorentrega,
                        'alias' => $contrato->alias,
                        'opcionlugarentrega' => $contrato->opcionlugarentrega,
                        'observacionfotoine' => $contrato->observacionfotoine,
                        'observacionfotoineatras' => $contrato->observacionfotoineatras,
                        'observacionfotocasa' => $contrato->observacionfotocasa,
                        'observacioncomprobantedomicilio' => $contrato->observacioncomprobantedomicilio,
                        'observacionpagare' => $contrato->observacionpagare,
                        'observacionfotootros' => $contrato->observacionfotootros,
                        'nombrepaquete' => $nombrepaquete,
                        'ultimoabonoreal' => $ultimoabonoreal,
                        'titulopromocion' => $titulopromocion,
                        'created_at' => $contrato->created_at,
                        'updated_at' => ($contrato->updated_at == null ? $contrato->created_at : $contrato->updated_at)
                    ]);

                } catch (\Exception $e) {
                    \Log::info("Error: Funcion : insertarDatosTablaContratosTemporalesSincronizacion: " . $contrato->id . " '$idFranquicia'" . "\n" . $e);
                    continue;
                }

            }

        }

        if ($rol == 4) {
            //Es rol cobranza
            //Obtener contratos que tienen alguna autorizacion (PENDIENTE, APROBADA, RECHAZADA)
            $contratosautorizacion = DB::select(
                "SELECT a.id_contrato
                            FROM autorizaciones a
                            INNER JOIN contratos c ON c.id = a.id_contrato
                            WHERE c.id_franquicia = '$idFranquicia'
                            AND c.id_zona = '" . $idZona . "'
                            AND a.tipo IN (8,9)
                            AND a.estatus != '3'
                            AND (c.estatus_estadocontrato IN (2,4,12)
                                    OR c.id IN (SELECT g.id_contrato
                                                    FROM garantias g
                                                    INNER JOIN contratos con ON con.id = g.id_contrato
                                                    WHERE con.estatus_estadocontrato IN (1,7,9,10,11)
                                                    AND con.id_franquicia = '$idFranquicia'
                                                    AND con.id_zona = '" . $idZona . "'
                                                    AND g.estadogarantia IN (1,2)))");

            foreach ($contratosautorizacion as $contrato) {
                //Recorrido de contratos
                DB::table("contratostemporalessincronizacion")->where("id", "=", $contrato->id_contrato)->update([
                    'autorizacion' => self::obtenerEstatusAutorizacionContratoTemporal($contrato->id_contrato)
                ]);
            }

        }

    }

    public static function insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $idUsuario)
    {

        try {

            $usuario = DB::select("SELECT rol_id FROM users WHERE id = '$idUsuario'");

            $arregloIdsUsuarios = array();
            array_push($arregloIdsUsuarios, $idUsuario);

            if ($usuario != null) {
                //Existe el usuario
                $rol = $usuario[0]->rol_id;

                $contrato = DB::select(
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
                                IFNULL(c.abonominimo, '') as abonominimo,
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
                                IFNULL(c.calleentrega, '') as calleentrega,
                                IFNULL(c.numeroentrega, '') as numeroentrega,
                                IFNULL(c.deptoentrega, '') as deptoentrega,
                                IFNULL(c.alladodeentrega, '') as alladodeentrega,
                                IFNULL(c.frenteaentrega, '') as frenteaentrega,
                                IFNULL(c.entrecallesentrega, '') as entrecallesentrega,
                                IFNULL(c.coloniaentrega, '') as coloniaentrega,
                                IFNULL(c.localidadentrega, '') as localidadentrega,
                                IFNULL(c.casatipoentrega, '') as casatipoentrega,
                                IFNULL(c.casacolorentrega, '') as casacolorentrega,
                                IFNULL(c.alias, '') as alias,
                                IFNULL(c.opcionlugarentrega, '') as opcionlugarentrega,
                                IFNULL(c.observacionfotoine, '') as observacionfotoine,
                                IFNULL(c.observacionfotoineatras, '') as observacionfotoineatras,
                                IFNULL(c.observacionfotocasa, '') as observacionfotocasa,
                                IFNULL(c.observacioncomprobantedomicilio, '') as observacioncomprobantedomicilio,
                                IFNULL(c.observacionpagare, '') as observacionpagare,
                                IFNULL(c.observacionfotootros, '') as observacionfotootros,
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
                                        WHERE (pr.id = c.id_promocion OR pr.id = (SELECT pc.id_promocion FROM promocioncontrato pc WHERE pc.id_contrato = c.idcontratorelacion))), '') as titulopromocion,
                                c.fecharegistro
                                FROM contratos c
                                WHERE c.id = '$idContrato'");

                if ($contrato != null) {
                    //Existe el contrato

                    if ($rol == 16 || $rol == 6 || $rol == 7 || $rol == 8) {
                        //Laboratorio, Administrativo, Director o Principal

                        if ($contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11 || $contrato[0]->estatus_estadocontrato == 12
                            || $contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 4 || $contrato[0]->estatus_estadocontrato == 5) {
                            //MANOFACTURA, EN PROCESO DE ENVIO, ENVIADO, ENTREGADO, ABONO ATRASADO O LIQUIDADO

                            $cobradoresAsignadosAZona = DB::select("SELECT u.id
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id = uf.id_usuario
                                              WHERE u.rol_id = 4 AND u.id_zona = '" . $contrato[0]->id_zona . "'"); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona

                            if ($cobradoresAsignadosAZona != null) {
                                //Existen cobradores
                                $arregloIdsUsuarios = array();
                                foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                    //Recorrido cobradores
                                    array_push($arregloIdsUsuarios, $cobradorAsignadoAZona->id);
                                }
                            }

                        }
                    }

                    if ($rol == 15 || $rol == 7 || $rol == 8) {
                        //Confirmaciones, Director o Principal

                        if ($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9
                            || $contrato[0]->estatus_estadocontrato == 7) {
                            //NO TERMINADO, TERMINADO, EN PROCESO DE APROBACION, APROBADO
                            $arregloIdsUsuarios = array();
                            array_push($arregloIdsUsuarios, $contrato[0]->id_usuariocreacion);
                        }

                    }

                    foreach ($arregloIdsUsuarios as $arregloIdUsuario) {
                        //Recorrido de arregloIdsUsuarios

                        try {

                            $existeRegistro = DB::select("SELECT indice FROM contratostemporalessincronizacion WHERE id = '$idContrato' AND id_usuario = '$arregloIdUsuario'");

                            if ($existeRegistro != null) {
                                //Existe el registro en la tabla (Actualizar)

                                DB::table("contratostemporalessincronizacion")->where("id", "=", $idContrato)->update([
                                    'datos' => $contrato[0]->datos,
                                    'id_usuariocreacion' => $contrato[0]->id_usuariocreacion,
                                    'nombre_usuariocreacion' => $contrato[0]->nombre_usuariocreacion,
                                    'id_zona' => $contrato[0]->id_zona,
                                    'estatus' => $contrato[0]->estatus,
                                    'nombre' => $contrato[0]->nombre,
                                    'calle' => $contrato[0]->calle,
                                    'numero' => $contrato[0]->numero,
                                    'depto' => $contrato[0]->depto,
                                    'alladode' => $contrato[0]->alladode,
                                    'frentea' => $contrato[0]->frentea,
                                    'entrecalles' => $contrato[0]->entrecalles,
                                    'colonia' => $contrato[0]->colonia,
                                    'localidad' => $contrato[0]->localidad,
                                    'telefono' => $contrato[0]->telefono,
                                    'telefonoreferencia' => $contrato[0]->telefonoreferencia,
                                    'correo' => $contrato[0]->correo,
                                    'nombrereferencia' => $contrato[0]->nombrereferencia,
                                    'casatipo' => $contrato[0]->casatipo,
                                    'casacolor' => $contrato[0]->casacolor,
                                    'fotoine' => $contrato[0]->fotoine,
                                    'fotocasa' => $contrato[0]->fotocasa,
                                    'comprobantedomicilio' => $contrato[0]->comprobantedomicilio,
                                    'pagare' => $contrato[0]->pagare,
                                    'fotootros' => $contrato[0]->fotootros,
                                    'pagosadelantar' => $contrato[0]->pagosadelantar,
                                    'id_optometrista' => $contrato[0]->id_optometrista,
                                    'tarjeta' => $contrato[0]->tarjeta,
                                    'pago' => $contrato[0]->pago,
                                    'abonominimo' => $contrato[0]->abonominimo,
                                    'id_promocion' => $contrato[0]->id_promocion,
                                    'fotoineatras' => $contrato[0]->fotoineatras,
                                    'tarjetapensionatras' => $contrato[0]->tarjetapensionatras,
                                    'total' => $contrato[0]->total,
                                    'idcontratorelacion' => $contrato[0]->idcontratorelacion,
                                    'contador' => $contrato[0]->contador,
                                    'totalhistorial' => $contrato[0]->totalhistorial,
                                    'totalpromocion' => $contrato[0]->totalpromocion,
                                    'totalproducto' => $contrato[0]->totalproducto,
                                    'totalabono' => $contrato[0]->totalabono,
                                    'fechaatraso' => $contrato[0]->fechaatraso,
                                    'costoatraso' => $contrato[0]->costoatraso,
                                    'ultimoabono' => $contrato[0]->ultimoabono,
                                    'estatus_estadocontrato' => $contrato[0]->estatus_estadocontrato,
                                    'diapago' => $contrato[0]->diapago,
                                    'fechacobroini' => $contrato[0]->fechacobroini,
                                    'fechacobrofin' => $contrato[0]->fechacobrofin,
                                    'enganche' => $contrato[0]->enganche,
                                    'entregaproducto' => $contrato[0]->entregaproducto,
                                    'diaseleccionado' => $contrato[0]->diaseleccionado,
                                    'fechaentrega' => $contrato[0]->fechaentrega,
                                    'promocionterminada' => $contrato[0]->promocionterminada,
                                    'subscripcion' => $contrato[0]->subscripcion,
                                    'fechasubscripcion' => $contrato[0]->fechasubscripcion,
                                    'nota' => $contrato[0]->nota,
                                    'totalreal' => $contrato[0]->totalreal,
                                    'diatemporal' => $contrato[0]->diatemporal,
                                    'coordenadas' => $contrato[0]->coordenadas,
                                    'calleentrega' => $contrato[0]->calleentrega,
                                    'numeroentrega' => $contrato[0]->numeroentrega,
                                    'deptoentrega' => $contrato[0]->deptoentrega,
                                    'alladodeentrega' => $contrato[0]->alladodeentrega,
                                    'frenteaentrega' => $contrato[0]->frenteaentrega,
                                    'entrecallesentrega' => $contrato[0]->entrecallesentrega,
                                    'coloniaentrega' => $contrato[0]->coloniaentrega,
                                    'localidadentrega' => $contrato[0]->localidadentrega,
                                    'casatipoentrega' => $contrato[0]->casatipoentrega,
                                    'casacolorentrega' => $contrato[0]->casacolorentrega,
                                    'alias' => $contrato[0]->alias,
                                    'autorizacion' => self::obtenerEstatusAutorizacionContratoTemporal($idContrato),
                                    'opcionlugarentrega' => $contrato[0]->opcionlugarentrega,
                                    'observacionfotoine' => $contrato[0]->observacionfotoine,
                                    'observacionfotoineatras' => $contrato[0]->observacionfotoineatras,
                                    'observacionfotocasa' => $contrato[0]->observacionfotocasa,
                                    'observacioncomprobantedomicilio' => $contrato[0]->observacioncomprobantedomicilio,
                                    'observacionpagare' => $contrato[0]->observacionpagare,
                                    'observacionfotootros' => $contrato[0]->observacionfotootros,
                                    'nombrepaquete' => $contrato[0]->nombrepaquete,
                                    'ultimoabonoreal' => $contrato[0]->ultimoabonoreal,
                                    'titulopromocion' => $contrato[0]->titulopromocion,
                                    'fecharegistro' => $contrato[0]->fecharegistro,
                                    'created_at' => $contrato[0]->created_at,
                                    'updated_at' => ($contrato[0]->updated_at == null ? $contrato[0]->created_at : $contrato[0]->updated_at)
                                ]);

                            } else {
                                //No existe el registro en la tabla (Insertar)

                                DB::table('contratostemporalessincronizacion')->insert([
                                    'id_usuario' => $arregloIdUsuario,
                                    'id' => $contrato[0]->id,
                                    'datos' => $contrato[0]->datos,
                                    'id_usuariocreacion' => $contrato[0]->id_usuariocreacion,
                                    'nombre_usuariocreacion' => $contrato[0]->nombre_usuariocreacion,
                                    'id_zona' => $contrato[0]->id_zona,
                                    'estatus' => $contrato[0]->estatus,
                                    'nombre' => $contrato[0]->nombre,
                                    'calle' => $contrato[0]->calle,
                                    'numero' => $contrato[0]->numero,
                                    'depto' => $contrato[0]->depto,
                                    'alladode' => $contrato[0]->alladode,
                                    'frentea' => $contrato[0]->frentea,
                                    'entrecalles' => $contrato[0]->entrecalles,
                                    'colonia' => $contrato[0]->colonia,
                                    'localidad' => $contrato[0]->localidad,
                                    'telefono' => $contrato[0]->telefono,
                                    'telefonoreferencia' => $contrato[0]->telefonoreferencia,
                                    'correo' => $contrato[0]->correo,
                                    'nombrereferencia' => $contrato[0]->nombrereferencia,
                                    'casatipo' => $contrato[0]->casatipo,
                                    'casacolor' => $contrato[0]->casacolor,
                                    'fotoine' => $contrato[0]->fotoine,
                                    'fotocasa' => $contrato[0]->fotocasa,
                                    'comprobantedomicilio' => $contrato[0]->comprobantedomicilio,
                                    'pagare' => $contrato[0]->pagare,
                                    'fotootros' => $contrato[0]->fotootros,
                                    'pagosadelantar' => $contrato[0]->pagosadelantar,
                                    'id_optometrista' => $contrato[0]->id_optometrista,
                                    'tarjeta' => $contrato[0]->tarjeta,
                                    'pago' => $contrato[0]->pago,
                                    'abonominimo' => $contrato[0]->abonominimo,
                                    'id_promocion' => $contrato[0]->id_promocion,
                                    'fotoineatras' => $contrato[0]->fotoineatras,
                                    'tarjetapensionatras' => $contrato[0]->tarjetapensionatras,
                                    'total' => $contrato[0]->total,
                                    'idcontratorelacion' => $contrato[0]->idcontratorelacion,
                                    'contador' => $contrato[0]->contador,
                                    'totalhistorial' => $contrato[0]->totalhistorial,
                                    'totalpromocion' => $contrato[0]->totalpromocion,
                                    'totalproducto' => $contrato[0]->totalproducto,
                                    'totalabono' => $contrato[0]->totalabono,
                                    'fechaatraso' => $contrato[0]->fechaatraso,
                                    'costoatraso' => $contrato[0]->costoatraso,
                                    'ultimoabono' => $contrato[0]->ultimoabono,
                                    'estatus_estadocontrato' => $contrato[0]->estatus_estadocontrato,
                                    'diapago' => $contrato[0]->diapago,
                                    'fechacobroini' => $contrato[0]->fechacobroini,
                                    'fechacobrofin' => $contrato[0]->fechacobrofin,
                                    'enganche' => $contrato[0]->enganche,
                                    'entregaproducto' => $contrato[0]->entregaproducto,
                                    'diaseleccionado' => $contrato[0]->diaseleccionado,
                                    'fechaentrega' => $contrato[0]->fechaentrega,
                                    'promocionterminada' => $contrato[0]->promocionterminada,
                                    'subscripcion' => $contrato[0]->subscripcion,
                                    'fechasubscripcion' => $contrato[0]->fechasubscripcion,
                                    'nota' => $contrato[0]->nota,
                                    'totalreal' => $contrato[0]->totalreal,
                                    'diatemporal' => $contrato[0]->diatemporal,
                                    'coordenadas' => $contrato[0]->coordenadas,
                                    'calleentrega' => $contrato[0]->calleentrega,
                                    'numeroentrega' => $contrato[0]->numeroentrega,
                                    'deptoentrega' => $contrato[0]->deptoentrega,
                                    'alladodeentrega' => $contrato[0]->alladodeentrega,
                                    'frenteaentrega' => $contrato[0]->frenteaentrega,
                                    'entrecallesentrega' => $contrato[0]->entrecallesentrega,
                                    'coloniaentrega' => $contrato[0]->coloniaentrega,
                                    'localidadentrega' => $contrato[0]->localidadentrega,
                                    'casatipoentrega' => $contrato[0]->casatipoentrega,
                                    'casacolorentrega' => $contrato[0]->casacolorentrega,
                                    'alias' => $contrato[0]->alias,
                                    'autorizacion' => self::obtenerEstatusAutorizacionContratoTemporal($idContrato),
                                    'opcionlugarentrega' => $contrato[0]->opcionlugarentrega,
                                    'observacionfotoine' => $contrato[0]->observacionfotoine,
                                    'observacionfotoineatras' => $contrato[0]->observacionfotoineatras,
                                    'observacionfotocasa' => $contrato[0]->observacionfotocasa,
                                    'observacioncomprobantedomicilio' => $contrato[0]->observacioncomprobantedomicilio,
                                    'observacionpagare' => $contrato[0]->observacionpagare,
                                    'observacionfotootros' => $contrato[0]->observacionfotootros,
                                    'nombrepaquete' => $contrato[0]->nombrepaquete,
                                    'ultimoabonoreal' => $contrato[0]->ultimoabonoreal,
                                    'titulopromocion' => $contrato[0]->titulopromocion,
                                    'fecharegistro' => $contrato[0]->fecharegistro,
                                    'created_at' => $contrato[0]->created_at,
                                    'updated_at' => ($contrato[0]->updated_at == null ? $contrato[0]->created_at : $contrato[0]->updated_at)
                                ]);

                            }

                        } catch (\Exception $e) {
                            \Log::info("Error: Funcion : insertarOActualizarDatosTablaContratosTemporalesSincronizacionPorContrato al insertar contrato: " . $idContrato . " con el id_usuario: " . $arregloIdUsuario . "\n" . $e);
                        }

                    }

                }

            }

        } catch (\Exception $e) {
            \Log::info("Error: Funcion : insertarOActualizarDatosTablaContratosTemporalesSincronizacionPorContrato al insertar contrato: " . $idContrato . "\n" . $e);
        }

    }

    public static function actualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato)
    {

        try {

            $contrato = DB::select(
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
                                IFNULL(c.abonominimo, '') as abonominimo,
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
                                IFNULL(c.calleentrega, '') as calleentrega,
                                IFNULL(c.numeroentrega, '') as numeroentrega,
                                IFNULL(c.deptoentrega, '') as deptoentrega,
                                IFNULL(c.alladodeentrega, '') as alladodeentrega,
                                IFNULL(c.frenteaentrega, '') as frenteaentrega,
                                IFNULL(c.entrecallesentrega, '') as entrecallesentrega,
                                IFNULL(c.coloniaentrega, '') as coloniaentrega,
                                IFNULL(c.localidadentrega, '') as localidadentrega,
                                IFNULL(c.casatipoentrega, '') as casatipoentrega,
                                IFNULL(c.casacolorentrega, '') as casacolorentrega,
                                IFNULL(c.alias, '') as alias,
                                IFNULL(c.opcionlugarentrega, '') as opcionlugarentrega,
                                IFNULL(c.observacionfotoine, '') as observacionfotoine,
                                IFNULL(c.observacionfotoineatras, '') as observacionfotoineatras,
                                IFNULL(c.observacionfotocasa, '') as observacionfotocasa,
                                IFNULL(c.observacioncomprobantedomicilio, '') as observacioncomprobantedomicilio,
                                IFNULL(c.observacionpagare, '') as observacionpagare,
                                IFNULL(c.observacionfotootros, '') as observacionfotootros,
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
                                WHERE c.id = '$idContrato'");

            if ($contrato != null) {
                //Existe el contrato

                if ($contrato[0]->estatus_estadocontrato == 6) {
                    //Estado del contrato cancelado

                    //Eliminar registros de tabla contratostemporalessincronizacion
                    DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");

                    //Eliminar registros de la tabla abonoscontratostemporalessincronizacion que contengan ese idContrato
                    DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_contrato = '$idContrato'");

                    //Rechazar solicitudes que haya por autorizar
                    $solicitudesautorizacion = DB::select("SELECT indice FROM autorizaciones a WHERE a.id_contrato = '$idContrato' AND a.estatus = 0
                                                                                    ORDER BY a.created_at DESC");
                    foreach ($solicitudesautorizacion as $solicitudautorizacion) {
                        //Tiene solicitudes pendientes por autorizar
                        DB::table('autorizaciones')->where([['indice', '=', $solicitudautorizacion->indice], ['id_contrato', '=', $idContrato]])->update([
                            'estatus' => '2', 'updated_at' => Carbon::now()
                        ]);
                    }

                } else {
                    //Estado del contrato diferente a cancelado

                    DB::table("contratostemporalessincronizacion")->where("id", "=", $idContrato)->update([
                        'datos' => $contrato[0]->datos,
                        'id_usuariocreacion' => $contrato[0]->id_usuariocreacion,
                        'nombre_usuariocreacion' => $contrato[0]->nombre_usuariocreacion,
                        'id_zona' => $contrato[0]->id_zona,
                        'estatus' => $contrato[0]->estatus,
                        'nombre' => $contrato[0]->nombre,
                        'calle' => $contrato[0]->calle,
                        'numero' => $contrato[0]->numero,
                        'depto' => $contrato[0]->depto,
                        'alladode' => $contrato[0]->alladode,
                        'frentea' => $contrato[0]->frentea,
                        'entrecalles' => $contrato[0]->entrecalles,
                        'colonia' => $contrato[0]->colonia,
                        'localidad' => $contrato[0]->localidad,
                        'telefono' => $contrato[0]->telefono,
                        'telefonoreferencia' => $contrato[0]->telefonoreferencia,
                        'correo' => $contrato[0]->correo,
                        'nombrereferencia' => $contrato[0]->nombrereferencia,
                        'casatipo' => $contrato[0]->casatipo,
                        'casacolor' => $contrato[0]->casacolor,
                        'fotoine' => $contrato[0]->fotoine,
                        'fotocasa' => $contrato[0]->fotocasa,
                        'comprobantedomicilio' => $contrato[0]->comprobantedomicilio,
                        'pagare' => $contrato[0]->pagare,
                        'fotootros' => $contrato[0]->fotootros,
                        'pagosadelantar' => $contrato[0]->pagosadelantar,
                        'id_optometrista' => $contrato[0]->id_optometrista,
                        'tarjeta' => $contrato[0]->tarjeta,
                        'pago' => $contrato[0]->pago,
                        'abonominimo' => $contrato[0]->abonominimo,
                        'id_promocion' => $contrato[0]->id_promocion,
                        'fotoineatras' => $contrato[0]->fotoineatras,
                        'tarjetapensionatras' => $contrato[0]->tarjetapensionatras,
                        'total' => $contrato[0]->total,
                        'idcontratorelacion' => $contrato[0]->idcontratorelacion,
                        'contador' => $contrato[0]->contador,
                        'totalhistorial' => $contrato[0]->totalhistorial,
                        'totalpromocion' => $contrato[0]->totalpromocion,
                        'totalproducto' => $contrato[0]->totalproducto,
                        'totalabono' => $contrato[0]->totalabono,
                        'fechaatraso' => $contrato[0]->fechaatraso,
                        'costoatraso' => $contrato[0]->costoatraso,
                        'ultimoabono' => $contrato[0]->ultimoabono,
                        'estatus_estadocontrato' => $contrato[0]->estatus_estadocontrato,
                        'diapago' => $contrato[0]->diapago,
                        'fechacobroini' => $contrato[0]->fechacobroini,
                        'fechacobrofin' => $contrato[0]->fechacobrofin,
                        'enganche' => $contrato[0]->enganche,
                        'entregaproducto' => $contrato[0]->entregaproducto,
                        'diaseleccionado' => $contrato[0]->diaseleccionado,
                        'fechaentrega' => $contrato[0]->fechaentrega,
                        'promocionterminada' => $contrato[0]->promocionterminada,
                        'subscripcion' => $contrato[0]->subscripcion,
                        'fechasubscripcion' => $contrato[0]->fechasubscripcion,
                        'nota' => $contrato[0]->nota,
                        'totalreal' => $contrato[0]->totalreal,
                        'diatemporal' => $contrato[0]->diatemporal,
                        'coordenadas' => $contrato[0]->coordenadas,
                        'calleentrega' => $contrato[0]->calleentrega,
                        'numeroentrega' => $contrato[0]->numeroentrega,
                        'deptoentrega' => $contrato[0]->deptoentrega,
                        'alladodeentrega' => $contrato[0]->alladodeentrega,
                        'frenteaentrega' => $contrato[0]->frenteaentrega,
                        'entrecallesentrega' => $contrato[0]->entrecallesentrega,
                        'coloniaentrega' => $contrato[0]->coloniaentrega,
                        'localidadentrega' => $contrato[0]->localidadentrega,
                        'casatipoentrega' => $contrato[0]->casatipoentrega,
                        'casacolorentrega' => $contrato[0]->casacolorentrega,
                        'alias' => $contrato[0]->alias,
                        'autorizacion' => self::obtenerEstatusAutorizacionContratoTemporal($idContrato),
                        'opcionlugarentrega' => $contrato[0]->opcionlugarentrega,
                        'observacionfotoine' => $contrato[0]->observacionfotoine,
                        'observacionfotoineatras' => $contrato[0]->observacionfotoineatras,
                        'observacionfotocasa' => $contrato[0]->observacionfotocasa,
                        'observacioncomprobantedomicilio' => $contrato[0]->observacioncomprobantedomicilio,
                        'observacionpagare' => $contrato[0]->observacionpagare,
                        'observacionfotootros' => $contrato[0]->observacionfotootros,
                        'nombrepaquete' => $contrato[0]->nombrepaquete,
                        'ultimoabonoreal' => $contrato[0]->ultimoabonoreal,
                        'titulopromocion' => $contrato[0]->titulopromocion,
                        'created_at' => $contrato[0]->created_at,
                        'updated_at' => ($contrato[0]->updated_at == null ? $contrato[0]->created_at : $contrato[0]->updated_at)
                    ]);

                }

            }

        } catch (\Exception $e) {
            \Log::info("Error: Funcion : actualizarDatosPorContratoTablaContratosTemporalesSincronizacion al actualizar contrato: " . $idContrato . "\n" . $e);
        }

    }

    public static function verificarContratosNoExistentesEnContratosTemporalesSincronizacion()
    {

        $contratos = DB::select("SELECT c.id_franquicia as id_franquicia,
                            c.id as id,
                            c.estatus_estadocontrato as estatus_estadocontrato,
                            (SELECT zona FROM zonas z WHERE c.id_zona = z.id) as zona,
                            id_zona as id_zona,
                            (SELECT u.name FROM users u WHERE u.id_zona = c.id_zona LIMIT 1) as nombrecobrador,
                            id_usuariocreacion as id_usuariocreacion,
                            (SELECT u.name FROM users u WHERE u.id = c.id_usuariocreacion LIMIT 1) as nombreusuariocreacion
                            FROM contratos c
                            WHERE c.id NOT IN (SELECT cts.id
                            FROM contratostemporalessincronizacion cts) AND c.estatus_estadocontrato IN (0,1,7,9,10,11,12,2,4)
                            AND c.id_franquicia != '00000' AND c.id_zona NOT IN (31,32,33,34,35,43,57) ORDER BY c.id_franquicia");

        foreach ($contratos as $contrato) {
            //Recorrido contratos

            $idFranquicia = $contrato->id_franquicia;
            $idContrato = $contrato->id;

            try {

                $estadoContrato = $contrato->estatus_estadocontrato;
                $idZona = $contrato->id_zona;
                $idUsuarioCreacion = $contrato->id_usuariocreacion;

                //Validacion de si es garantia o no
                $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1 ORDER BY created_at LIMIT 1");

                switch ($estadoContrato) {
                    case 0:
                    case 1:
                    case 9: //NO TERMINADO, TERMINADO Y EN PROCESO DE APROBACION
                        //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                        if ($tieneHistorialGarantia != null) {
                            //Tiene garantia

                            //Validacion para optometrista
                            $ultimaGarantiaCreada = DB::select("SELECT id_optometrista FROM garantias WHERE id_contrato = '$idContrato'
                                                                                    AND id_historialgarantia = '" . $tieneHistorialGarantia[0]->id . "'
                                                                                    ORDER BY created_at LIMIT 1");
                            if ($ultimaGarantiaCreada != null) {
                                $existeOptometrista = DB::select("SELECT u.id, uf.id_franquicia
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id = uf.id_usuario
                                              WHERE u.rol_id = 12 AND u.id = '" . $ultimaGarantiaCreada[0]->id_optometrista . "'");

                                if ($existeOptometrista != null) {
                                    //Existe optometrista
                                    if ($existeOptometrista[0]->id_franquicia == $idFranquicia) {
                                        //La optometrista sigue estando asignada en la misma sucursal
                                        self::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $ultimaGarantiaCreada[0]->id_optometrista);
                                    }
                                }
                            }

                            //Validacion para cobradores
                            self::insertarCobradoresAsignadosZona($idContrato, $idZona);

                        } else {
                            //No tiene garantia
                            $existeAsistente = DB::select("SELECT u.id, uf.id_franquicia
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id = uf.id_usuario
                                              WHERE u.id = '" . $idUsuarioCreacion . "'");

                            if ($existeAsistente != null) {
                                //Existe asistente
                                if ($existeAsistente[0]->id_franquicia == $idFranquicia) {
                                    //La asistente sigue estando asignada en la misma sucursal
                                    self::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $idUsuarioCreacion);
                                }
                            }
                        }
                        break;
                    case 7:
                    case 10:
                    case 11: //APROBADO, MANOFACTURA, EN PROCESO DE ENVIO
                        //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato (Opcional lo puse en caso de que no se haga en confirmaciones)
                        DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");

                        //Agregar contrato a cobradores si tiene garantia el contrato
                        if ($tieneHistorialGarantia != null) {
                            //Tiene garantia
                            self::insertarCobradoresAsignadosZona($idContrato, $idZona);
                        }
                        break;
                    case 12:
                    case 2:
                    case 4:
                        self::insertarCobradoresAsignadosZona($idContrato, $idZona);
                        break;
                }

            } catch (\Exception $e) {
                \Log::info("Error: Funcion : verificarContratosNoExistentesEnContratosTemporalesSincronizacion: " . $idContrato . " '$idFranquicia'" . "\n" . $e);
                continue;
            }

        }

        \Log::info("Funcion verificarContratosNoExistentesEnContratosTemporalesSincronizacion terminada");

    }

    public static function insertarCobradoresAsignadosZona($idContrato, $idZona){

        $cobradoresAsignadosAZona = DB::select("SELECT u.id
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id = uf.id_usuario
                                              WHERE u.rol_id = 4 AND u.id_zona = '" . $idZona . "'"); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona

        if ($cobradoresAsignadosAZona != null) {
            //Existen cobradores
            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                //Recorrido cobradores
                self::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $cobradorAsignadoAZona->id);
            }
        }

    }

    public static function actualizarContratosEntregaProductoEnCeroYQueHayanSidoEntregados()
    {

        $contratos = DB::select("SELECT c.id, c.id_franquicia FROM contratos c
                    INNER JOIN abonos a ON c.id = a.id_contrato
                    INNER JOIN historialcontrato hc ON c.id = hc.id_contrato
                    WHERE c.entregaproducto = 0 AND hc.cambios LIKE '%M - Cambio el estatus a entregado%'
                    AND a.tipoabono = 2 GROUP BY c.id, c.id_franquicia");

        foreach ($contratos as $contrato) {
            //Recorrido contratos

            $idFranquicia = $contrato->id_franquicia;
            $idContrato = $contrato->id;

            try {

                $abono = DB::select("SELECT a.created_at FROM abonos a
                    WHERE a.id_contrato = '$idContrato'
                    AND a.tipoabono = 2 ORDER BY a.created_at ASC LIMIT 1");

                $fechaentrega = Carbon::now();
                if($abono != null) {
                    //Existe abono de entrega
                    $fechaentrega = $abono[0]->created_at;
                }

                DB::table("contratos")->where([['id', '=' ,$idContrato],['id_franquicia', '=' ,$idFranquicia]])->update([
                    'entregaproducto' => 1,
                    'fechaentrega' => $fechaentrega
                ]);

                self::actualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato);

            } catch (\Exception $e) {
                \Log::info("Error: Funcion : actualizarContratosEntregaProductoEnCeroYQueHayanSidoEntregados: " . $idContrato . " '$idFranquicia'" . "\n" . $e);
                continue;
            }

        }

        \Log::info("Funcion actualizarContratosEntregaProductoEnCeroYQueHayanSidoEntregados terminada");

    }

    public static function eliminarcontratostemporalessincronizacionrepetidos()
    {

        try {

            $contratosrepetidos = DB::select("SELECT id_usuario, id, COUNT(*) AS contador
                                                            FROM contratostemporalessincronizacion GROUP BY id_usuario, id HAVING COUNT(*) > 1");

            if ($contratosrepetidos != null) {
                //Existen contratos repetidos en tabla contratostemporalessincronizacion

                $array = array(); //Bandera de ids contrato para que se que solo deje uno y se eliminen los demas repetidos
                foreach ($contratosrepetidos as $contratorepetido) {

                    if ($contratorepetido->contador > 1) {
                        //Hay mas de uno repetido

                        $contratos = DB::select("SELECT indice, id, id_usuario
                                                            FROM contratostemporalessincronizacion
                                                            WHERE id_usuario = '" . $contratorepetido->id_usuario . "' AND id = '" . $contratorepetido->id . "'");

                        if ($contratos != null) {
                            //Existen contratos

                            foreach ($contratos as $contrato) {
                                //RECORRIDO DE CONTRATOS

                                if (!in_array($contrato->id . $contrato->id_usuario, $array)) {
                                    //No existe el id_contrato y el id_usuario aun en el array (No se borrara el primer registro)

                                    //Se agrega el id_contrato y el id_usuario al array para que este no vuelva a insertarse de nuevo
                                    array_push($array, $contrato->id . $contrato->id_usuario);
                                } else {
                                    //Existe el id_contrato y y el id_usuario en el array (Eliminar los demas registros repetidos)
                                    DB::delete("DELETE FROM contratostemporalessincronizacion WHERE indice = '" . $contrato->indice
                                        . "' AND id = '" . $contrato->id . "' AND id_usuario = '" . $contrato->id_usuario . "'");
                                }
                            }

                        }

                    }

                }

            }

        } catch (\Exception $e) {
            \Log::info("Error: Funcion : eliminarcontratostemporalessincronizacionrepetidos: " . "\n" . $e);
        }

        \Log::info("Funcion eliminarcontratostemporalessincronizacionrepetidos terminada");

    }

    public static function obtenerNombrePaquete($idPaquete)
    {
        $nombrePaquete = "";

        switch ($idPaquete) {
            case 1:
                $nombrePaquete = 'LECTURA';
                break;
            case 2:
                $nombrePaquete = 'PROTECCION';
                break;
            case 3:
                $nombrePaquete = 'ECO JR';
                break;
            case 4:
                $nombrePaquete = 'JR';
                break;
            case 5:
                $nombrePaquete = 'DORADO 1';
                break;
            case 6:
                $nombrePaquete = 'DORADO 2';
                break;
            case 7:
                $nombrePaquete = 'PLATINO';
                break;
        }

        return $nombrePaquete;
    }

    public static function actualizarContratoHistorialesClinicosMayusculasAcentos($idContrato, $opcion)
    {
        //Opcion
        //0 - Contrato
        //1 - Historiales clinicos

        switch ($opcion) {
            case 0: //Contrato
                DB::update("UPDATE contratos SET
                                     nombre = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(nombre),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     calle = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(calle),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     numero = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(numero),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     depto = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(depto),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     alladode = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(alladode),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     frentea = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(frentea),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     entrecalles = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(entrecalles),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     colonia = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(colonia),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     localidad = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(localidad),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     nombrereferencia = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(nombrereferencia),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     casatipo = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(casatipo),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     casacolor = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(casacolor),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     observaciones = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(observaciones),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     nota = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(nota),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     calleentrega = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(calleentrega),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     numeroentrega = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(numeroentrega),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     deptoentrega = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(deptoentrega),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     alladodeentrega = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(alladodeentrega),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     frenteaentrega = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(frenteaentrega),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     entrecallesentrega = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(entrecallesentrega),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     coloniaentrega = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(coloniaentrega),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     localidadentrega = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(localidadentrega),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     casatipoentrega = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(casatipoentrega),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     casacolorentrega = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(casacolorentrega),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     alias = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(alias),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     observacionfotoine = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(observacionfotoine),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     observacionfotoineatras = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(observacionfotoineatras),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     observacionfotocasa = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(observacionfotocasa),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     observacioncomprobantedomicilio = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(observacioncomprobantedomicilio),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     observacionpagare = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(observacionpagare),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                     observacionfotootros = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(observacionfotootros),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U')
                                     WHERE id = '$idContrato'");
                break;
            case 1: //Historiales clinicos
                DB::update("UPDATE historialclinico SET
                                    edad = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(edad),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                    diagnostico = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(diagnostico),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                    ocupacion = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(ocupacion),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                    diabetes = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(diabetes),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                    hipertension = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(hipertension),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                    materialotro = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(materialotro),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                    tratamientootro = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(tratamientootro),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                    observaciones = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(observaciones),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                    observacionesinterno = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(observacionesinterno),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                    bifocalotro = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(bifocalotro),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                    embarazada = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(embarazada),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                    durmioseisochohoras = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(durmioseisochohoras),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                    actividaddia = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(actividaddia),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U'),
                                    problemasojos = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(problemasojos),'Á','A'),'É','E'),'Í','I'),'Ó','O'),'Ú','U')
                                    WHERE id_contrato = '$idContrato'");
                break;
        }

    }

    public static function actualizarpiezasproductos()
    {

        $productos = DB::select("SELECT id, totalpiezas FROM producto WHERE estado = 1 ORDER BY id_tipoproducto, nombre");

        if ($productos != null) {
            //Existen productos

            foreach ($productos as $producto) {

                $idProducto = $producto->id;
                $totalPiezasActual = $producto->totalpiezas;
                $contadorPiezas = 0;

                try {

                    //Obtener cuantos historiales tienen ese producto
                    $contadorHistoriales = DB::select("SELECT COUNT(id) as contador FROM historialclinico WHERE id_producto = '$idProducto' AND tipo = '0'");
                    if ($contadorHistoriales != null) {
                        $contadorPiezas = $contadorPiezas + $contadorHistoriales[0]->contador;
                    }

                    //Obtener cuantos contratos tienen ese producto
                    $contadorContratosProductos = DB::select("SELECT SUM(piezas) as contador FROM contratoproducto WHERE id_producto = '$idProducto'");
                    if ($contadorContratosProductos != null) {
                        $contadorPiezas = $contadorPiezas + $contadorContratosProductos[0]->contador;
                    }

                    //Obtener el total de piezas a actualizar
                    $totalPiezasActualizar = $totalPiezasActual - $contadorPiezas;

                    //Actualizar totalpiezas del producto
                    DB::update("UPDATE producto SET piezas = '$totalPiezasActualizar' WHERE id = '$idProducto'");

                } catch (\Exception $e) {
                    \Log::info("Error: Funcion : actualizarpiezasproductos: " . $idProducto . "\n" . $e);
                    continue;
                }

            }

        }

        \Log::info("Funcion actualizarpiezasproductos terminada");

    }

    public static function eliminarImagenesInnecesariasContratos($carpeta, $atributo)
    {

        $archivos = Storage::disk('disco')->allFiles("uploads/imagenes/contratos/$carpeta/");

        foreach ($archivos as $archivo) {

            try {

                $contrato = DB::select("SELECT id FROM contratos WHERE $atributo = '$archivo'");

                if ($contrato == null) {
                    //No existe ningun contrato con esa imagen
                    Storage::disk('disco')->delete($archivo);
                }

            } catch (\Exception $e) {
                \Log::info("Error: Funcion : eliminarImagenesInnecesariasContratos: " . $archivo . "\n" . $e);
                continue;
            }

        }

        \Log::info("Funcion eliminarImagenesInnecesariasContratos '$atributo' terminada");

    }

    public static function crearPermisoSeccion($id_usuario, $seccion, $tipopermiso)
    {

        try {

            if (!self::validarPermisoSeccion($id_usuario, $seccion, $tipopermiso)) {
                //No existe permiso en el usuario
                //Insertar en tabla permisosusuarios
                DB::table('permisosusuarios')->insert([
                    'id_usuario' => $id_usuario,
                    'id_seccion' => $seccion,
                    'id_permiso' => $tipopermiso,
                    'created_at' => Carbon::now()
                ]);
            }

        } catch (\Exception $e) {
            \Log::info("Error: Funcion : crearPermisoSeccion: " . $id_usuario . "\n" . $e);
        }

    }

    public static function validarPermisoSeccion($id_usuario, $seccion, $tipopermiso)
    {
        $respuesta = false;

        try {

            $existePermisoUsuario = DB::select("SELECT id_usuario
                                                        FROM permisosusuarios WHERE id_usuario = '$id_usuario'
                                                        AND id_seccion = '$seccion'
                                                        AND id_permiso = '$tipopermiso'");

            if ($existePermisoUsuario != null) {
                //Existe permiso en el usuario
                $respuesta = true;
            }

        } catch (\Exception $e) {
            \Log::info("Error: Funcion : validarPermisoSeccion: " . $id_usuario . "\n" . $e);
        }

        return $respuesta;
    }

    public static function insertarTablaPermisosUsuariosPorDefault()
    {

        try {

            $usuarios = DB::select("SELECT u.id as id_usuario, u.name as nombreusuario FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                              WHERE u.rol_id IN (6,7,8) ORDER BY u.name DESC");

            if ($usuarios != null) {
                //Existen usuarios
                foreach ($usuarios as $usuario) {
                    //Seccion usuarios
                    self::crearPermisoSeccion($usuario->id_usuario, 0, 0); //Crear
                    self::crearPermisoSeccion($usuario->id_usuario, 0, 1); //Leer
                    self::crearPermisoSeccion($usuario->id_usuario, 0, 2); //Actualizar
                    self::crearPermisoSeccion($usuario->id_usuario, 0, 3); //Eliminar

                    //Seccion contratos
                    self::crearPermisoSeccion($usuario->id_usuario, 1, 0); //Crear
                    self::crearPermisoSeccion($usuario->id_usuario, 1, 1); //Leer
                    self::crearPermisoSeccion($usuario->id_usuario, 1, 2); //Actualizar
                    self::crearPermisoSeccion($usuario->id_usuario, 1, 3); //Eliminar

                    //Seccion administracion
                    self::crearPermisoSeccion($usuario->id_usuario, 2, 0); //Crear
                    self::crearPermisoSeccion($usuario->id_usuario, 2, 1); //Leer
                    self::crearPermisoSeccion($usuario->id_usuario, 2, 2); //Actualizar
                    self::crearPermisoSeccion($usuario->id_usuario, 2, 3); //Eliminar
                }
            }

        } catch (\Exception $e) {
            \Log::info("Error: Funcion : insertarTablaPermisosUsuariosPorDefault: " .  "\n" . $e);
        }

    }

    public static function obtenerCobradoresAsignadosZona($idZona){

        $cobradoresAsignadosAZona = DB::select("SELECT u.id
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id = uf.id_usuario
                                              WHERE u.rol_id = 4 AND u.id_zona = '" . $idZona . "'"); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona

        return $cobradoresAsignadosAZona;
    }

    public static function eliminarEInsertarAbonosContratosTemporalesSincronizacionPorUsuarios($idUsuario){

        DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_usuariocobrador = '$idUsuario'");

        $contratos = DB::select("SELECT DISTINCT cts.id, cts.id_usuario
                                        FROM contratostemporalessincronizacion cts
                                        WHERE cts.id_usuario = '$idUsuario'");

        if ($contratos != null) {
            //Tiene contratos creados
            foreach ($contratos as $contrato) {
                //Recorrido contratos

                try {

                    $idContrato = $contrato->id;
                    \Log::info("idContrato: " . $idContrato);

                    $abonosC = DB::select("SELECT IFNULL(a.id, '') as id, IFNULL(a.folio, '') as folio,
                                                    IFNULL(a.id_contrato, '') as id_contrato, IFNULL(a.id_usuario, '') as id_usuario,
                                                    IFNULL(a.abono, '') as abono, IFNULL(a.adelantos, '0') as adelantos, IFNULL(a.tipoabono, '') as tipoabono,
                                                    IFNULL(a.atraso, '0') as atraso, IFNULL(a.metodopago, '') as metodopago, IFNULL(a.corte, '') as corte,
                                                    IFNULL(a.created_at, '') as created_at, IFNULL(a.updated_at, '') as updated_at
                                                    FROM abonos a WHERE a.id_contrato = '$idContrato'");

                    if ($abonosC != null) {
                        //Hay abonos contrato
                        foreach ($abonosC as $abono) {

                            \Log::info("idContrato: " . $idContrato . " idAbono " . $abono->id);

                            DB::table("abonoscontratostemporalessincronizacion")->insert([
                                "id_usuariocobrador" => $contrato->id_usuario,
                                "id" => $abono->id,
                                "folio" => $abono->folio,
                                "id_contrato" => $abono->id_contrato,
                                "id_usuario" => $abono->id_usuario,
                                "abono" => $abono->abono,
                                "adelantos" => $abono->adelantos,
                                "tipoabono" => $abono->tipoabono,
                                "atraso" => $abono->atraso,
                                "metodopago" => $abono->metodopago,
                                "corte" => $abono->corte,
                                "created_at" => $abono->created_at,
                                "updated_at" => $abono->updated_at
                            ]);
                        }
                    }

                } catch (\Exception $e) {
                    \Log::info("Error: Metodo : insertarAbonosContratosTemporalesSincronizacionPorUsuarios: " . $contrato->id . "\n" . $e);
                    continue;
                }
            }
        }

    }

    public static function obtenerIdFranquiciaUsuario($idUsuario) {
        $usuarioFranquicia = DB::select("SELECT id_franquicia FROM usuariosfranquicia f where id_usuario = '$idUsuario'");
        if($usuarioFranquicia != null) {
            $idFranquicia = $usuarioFranquicia[0]->id_franquicia;
        }
        return $idFranquicia;
    }

    //Funcion: insertarAbonoMinimoContrato
    //Descripcion: Actualizara el campo de abonominimo de la tabla contrato con los precios correspondiente a la forma de pago del contrato y fecha de creacion
    public static function insertarAbonoMinimoTablaContrato(){
        //Contratos de pago SEMANAL
        DB::update("UPDATE contratos c SET c.abonominimo = 150 WHERE  c.pago = 1 AND c.created_at < '2022-12-24 00:00:00'");
        DB::update("UPDATE contratostemporalessincronizacion ct SET ct.abonominimo = 150 WHERE  ct.pago = 1 AND ct.created_at < '2022-12-24 00:00:00'");

        DB::update("UPDATE contratos c SET c.abonominimo = 200 WHERE  c.pago = 1 AND c.created_at > '2022-12-24 00:00:00'");
        DB::update("UPDATE contratostemporalessincronizacion ct SET ct.abonominimo = 200 WHERE  ct.pago = 1 AND ct.created_at > '2022-12-24 00:00:00'");

        //Contratos de pago QUINCENAL
        DB::update("UPDATE contratos c SET c.abonominimo = 300 WHERE  c.pago = 2 AND c.created_at < '2022-12-24 00:00:00'");
        DB::update("UPDATE contratostemporalessincronizacion ct SET ct.abonominimo = 300 WHERE  ct.pago = 2 AND ct.created_at < '2022-12-24 00:00:00'");

        DB::update("UPDATE contratos c SET c.abonominimo = 400 WHERE  c.pago = 2 AND c.created_at > '2022-12-24 00:00:00'");
        DB::update("UPDATE contratostemporalessincronizacion ct SET ct.abonominimo = 400 WHERE  ct.pago = 2 AND ct.created_at > '2022-12-24 00:00:00'");

        //Contratos de pago MENSUAL
        DB::update("UPDATE contratos c SET c.abonominimo = 400 WHERE  c.pago = 4 AND c.created_at < '2022-12-24 00:00:00'");
        DB::update("UPDATE contratostemporalessincronizacion ct SET ct.abonominimo = 400 WHERE  ct.pago = 4 AND ct.created_at < '2022-12-24 00:00:00'");

        DB::update("UPDATE contratos c SET c.abonominimo = 600 WHERE  c.pago = 4 AND c.created_at > '2022-12-24 00:00:00'");
        DB::update("UPDATE contratostemporalessincronizacion ct SET ct.abonominimo = 600 WHERE  ct.pago = 4 AND ct.created_at > '2022-12-24 00:00:00'");
    }

    //Funcion: actualizarRegistroTablaContratosLaboratorio
    //Descripcion: Recibe un contrato y una accion a generar para actualizar registros en tabla contratoslaboratorio
    public static function actualizarRegistroTablaContratosLaboratorio($id_Contrato, $accionRealizar){

        //Validar contrato
        $existeContrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$id_Contrato'");
        if($existeContrato){
            //Verificar que el contratos existe o no en tabla contratoslaboratorio
            $existeContratoTablaLaboratorio =  DB::select("SELECT * FROM contratoslaboratorio cl WHERE cl.id_contrato = '$id_Contrato'");

            switch ($accionRealizar){
                case 'INSERTAR':

                    //Obtener fecha ultimo estatus manufactura
                    $fechaManufactura = DB::select("SELECT rec.created_at FROM registroestadocontrato rec
                                                            WHERE rec.id_contrato = '$id_Contrato' AND rec.estatuscontrato = 10
                                                            ORDER BY rec.created_at DESC LIMIT 1");

                    $ultimaFechaManufactura = null;
                    if(sizeof($fechaManufactura) > 0){
                        //Tiene fecha de manufactura
                        $ultimaFechaManufactura = $fechaManufactura[0]->created_at;
                    }

                    //Existe contrato en tabla contratoslaboratorio?
                    if(!$existeContratoTablaLaboratorio){
                        //No existe - Insertamos
                        //Insertar nuevo registro en tabla
                        DB::table('contratoslaboratorio')->insert([
                            'id_contrato' => $id_Contrato,
                            'ultimoestatusmanufactura' => $ultimaFechaManufactura,
                            'created_at' => Carbon::now()
                        ]);

                    }else{
                        //Ya existe el contrato -> Actualizamos para ver si ya tiene fecha de munufactura
                        if($ultimaFechaManufactura != null){
                            DB::table('contratoslaboratorio')->where('id_contrato', '=', $id_Contrato)
                                ->update(['ultimoestatusmanufactura' => $ultimaFechaManufactura, 'updated_at' => Carbon::now()]);
                        }
                    }

                    break;
                case 'ELIMINAR':

                    //Si existe contrato en tabla contratoslaboratorio -> Eliminamos
                    if($existeContratoTablaLaboratorio != null){
                        DB::delete("DELETE FROM contratoslaboratorio WHERE id_contrato = '$id_Contrato' ");
                    }
                    break;

            }

        }
    }

    //Funcion: eliminarVentaRepetidaReporteContratosVentasAsistentesOptometristas
    //Descripcion: Recibe un arreglo con las ventas de la semana - Puede tarer ventas repetidas debido a que fueron aprobadas varias veces
    //Retornara un arreglo con solo la ultima aprobacion para evitar repetir contratos aprobados multiple vez
    public static function eliminarVentaRepetidaReporteContratosVentasAsistentesOptometristas($ventas){
        $arregloVentasSinRepetir = array();

        //Recorremos el arreglo de ventas obtenido desde la consulta
        foreach ($ventas as $datosVenta){
            $banderaExiste = false; //Bandera para saber si es un contrato ya existente y no repetir
            //Recorremos el nuevo arreglo de contratos no repetidos
            foreach ($arregloVentasSinRepetir as $noRepetido){
                //Ya se encuentra el contrato registrado?
                if($datosVenta->id_contrato == $noRepetido->id_contrato){
                    //Ya esta almacenado ese contrato
                    $banderaExiste = true;
                }
            }
            //Es un contrato que no existe en el arreglo?
            if(!$banderaExiste){
                //No existe aun el contrato en arreglo ventas sin repetir
                array_push($arregloVentasSinRepetir, $datosVenta);
            }
        }

        return $arregloVentasSinRepetir;
    }

    public static function eliminarGarantiasRepetidasTabla($idContrato)
    {

        $cadenaWhere = " ";
        if (strlen($idContrato) > 0) {
            $cadenaWhere = " WHERE id_contrato = '$idContrato'";
        }

        try {

            $garantiasrepetidas = DB::select("SELECT id_contrato, id, COUNT(*) AS contador
                                                       FROM garantias
                                                       " . $cadenaWhere . "
                                                       GROUP BY id_contrato, id HAVING COUNT(*) > 1");

            if($garantiasrepetidas != null) {
                //Existen garantias repetidas en tabla garantias

                $array = array(); //Bandera de ids garantias para que se que solo deje uno y se eliminen las demas repetidas
                foreach ($garantiasrepetidas as $garantiarepetida) {

                    if($garantiarepetida->contador > 1) {
                        //Hay mas de uno repetida

                        $garantias = DB::select("SELECT indice, id
                                                            FROM garantias WHERE id_contrato = '" . $garantiarepetida->id_contrato . "'
                                                            AND id = '" . $garantiarepetida->id . "'");

                        if($garantias != null) {
                            //Existen garantias

                            foreach ($garantias as $garantia) {
                                //RECORRIDO DE GARANTIAS

                                if (!in_array($garantiarepetida->id_contrato . $garantia->id, $array)) {
                                    //No existe el id (No se borrara el primer registro)

                                    //Se agrega el id al array para que este no vuelva a insertarse de nuevo
                                    array_push($array, $garantiarepetida->id_contrato . $garantia->id);
                                } else {
                                    //Existe el id en el array (Eliminar los demas registros repetidos)
                                    DB::delete("DELETE FROM garantias WHERE indice = '" . $garantia->indice
                                        . "' AND id = '" . $garantia->id . "'");
                                }
                            }

                        }

                    }

                }

                //\Log::info("Termino funcion para eliminarGarantiasRepetidasTabla en tabla garantias");

            }else {
                //No existen garantiasrepetidas en tabla garantias
                //\Log::info("No existen garantiasrepetidas en tabla garantias");
            }

        } catch (\Exception $e) {
            \Log::info("Error: Funcion : eliminarGarantiasRepetidasTabla: " . $idContrato . "\n" . $e);
        }

        if (strlen($idContrato) == 0) {
            \Log::info("Funcion eliminarGarantiasRepetidasTabla terminada");
        }

    }

    public static function insertarActualizarDatosContratoListaTemporales($idContrato){
        try {

            $contrato = DB::select("SELECT c.id AS id,
                              c.id_franquicia AS id_franquicia,
                              c.estatus_estadocontrato AS estatus_estadocontrato,
                              (SELECT e.descripcion FROM estadocontrato e WHERE e.estatus = c.estatus_estadocontrato) AS descripcion,
                              c.idcontratorelacion AS idcontratorelacion,
                              (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC LIMIT 1) AS fechaentrega,
                              c.fechaatraso AS fechaatraso,
                              (SELECT g.created_at FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS fechagarantia,
                              (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia,
                              c.nombre_usuariocreacion AS nombre_usuariocreacion,
                              c.id_zona  AS id_zona,
                              (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS zona,
                              c.localidad AS localidad,
                              c.colonia AS colonia,
                              c.calle AS calle,
                              c.numero AS numero,
                              c.telefono AS telefono,
                              c.nombre AS nombre,
                              c.nombrereferencia AS nombrereferencia,
                              c.telefonoreferencia AS telefonoreferencia,
                              c.totalreal AS totalreal,
                              c.totalproducto AS totalproducto,
                              c.totalpromocion AS totalpromocion,
                              c.totalabono AS totalabono,
                              c.total AS total,
                              c.ultimoabono AS ultimoabono,
                              (SELECT p.estado FROM promocioncontrato p WHERE id_contrato = '$idContrato') AS promocionactiva,
                              c.alias, c.created_at
                                FROM contratos c
                                WHERE c.id = '$idContrato'");

            if ($contrato != null){
                //Se obtuvieron los datos correctamente de la tabla contratos - Se encontro el contrato registrado

                //Validar si contrato existe en tabla contratoslistatemporal
                $existeContrato = DB::select("SELECT * FROM contratoslistatemporales WHERE id = '$idContrato'");

                if ($existeContrato != null) {
                    //Existe el contrato - Actualizamos datos del contrato
                    DB::table("contratoslistatemporales")->where("id", "=", $idContrato)->update([
                        'id' => $contrato[0]->id,
                        'id_franquicia' => $contrato[0]->id_franquicia,
                        'estatus_estadocontrato' => $contrato[0]->estatus_estadocontrato,
                        'descripcion' => $contrato[0]->descripcion,
                        'idcontratorelacion' => $contrato[0]->idcontratorelacion,
                        'fechaentrega' => $contrato[0]->fechaentrega,
                        'fechaatraso' => $contrato[0]->fechaatraso,
                        'fechagarantia' => $contrato[0]->fechagarantia,
                        'estadogarantia' => $contrato[0]->estadogarantia,
                        'nombre_usuariocreacion' => $contrato[0]->nombre_usuariocreacion,
                        'id_zona' => $contrato[0]->id_zona,
                        'zona' => $contrato[0]->zona,
                        'localidad' => $contrato[0]->localidad,
                        'colonia' => $contrato[0]->colonia,
                        'calle' => $contrato[0]->calle,
                        'numero' => $contrato[0]->numero,
                        'nombre' => $contrato[0]->nombre,
                        'telefono' => $contrato[0]->telefono,
                        'nombrereferencia' => $contrato[0]->nombrereferencia,
                        'telefonoreferencia' => $contrato[0]->telefonoreferencia,
                        'totalreal' => $contrato[0]->totalreal,
                        'totalproducto' => $contrato[0]->totalproducto,
                        'totalpromocion' => $contrato[0]->totalpromocion,
                        'totalabono' => $contrato[0]->totalabono,
                        'total' => $contrato[0]->total,
                        'ultimoabono' => $contrato[0]->ultimoabono,
                        'promocionactiva' => $contrato[0]->promocionactiva,
                        'alias' => $contrato[0]->alias,
                        'created_at' => $contrato[0]->created_at,
                        'updated_at' => Carbon::now()
                    ]);

                }else{
                    //Se insertara por primera vez en la tabla de contratos lista temporales
                    DB::table('contratoslistatemporales')->insert([
                        'id' => $contrato[0]->id,
                        'id_franquicia' => $contrato[0]->id_franquicia,
                        'estatus_estadocontrato' => $contrato[0]->estatus_estadocontrato,
                        'descripcion' => $contrato[0]->descripcion,
                        'idcontratorelacion' => $contrato[0]->idcontratorelacion,
                        'fechaentrega' => $contrato[0]->fechaentrega,
                        'fechaatraso' => $contrato[0]->fechaatraso,
                        'fechagarantia' => $contrato[0]->fechagarantia,
                        'estadogarantia' => $contrato[0]->estadogarantia,
                        'nombre_usuariocreacion' => $contrato[0]->nombre_usuariocreacion,
                        'id_zona' => $contrato[0]->id_zona,
                        'zona' => $contrato[0]->zona,
                        'localidad' => $contrato[0]->localidad,
                        'colonia' => $contrato[0]->colonia,
                        'calle' => $contrato[0]->calle,
                        'numero' => $contrato[0]->numero,
                        'nombre' => $contrato[0]->nombre,
                        'telefono' => $contrato[0]->telefono,
                        'nombrereferencia' => $contrato[0]->nombrereferencia,
                        'telefonoreferencia' => $contrato[0]->telefonoreferencia,
                        'totalreal' => $contrato[0]->totalreal,
                        'totalproducto' => $contrato[0]->totalproducto,
                        'totalpromocion' => $contrato[0]->totalpromocion,
                        'totalabono' => $contrato[0]->totalabono,
                        'total' => $contrato[0]->total,
                        'ultimoabono' => $contrato[0]->ultimoabono,
                        'promocionactiva' => $contrato[0]->promocionactiva,
                        'alias' => $contrato[0]->alias,
                        'created_at' => $contrato[0]->created_at,
                        'updated_at' => Carbon::now()
                    ]);

                }

            }

        } catch (\Exception $e) {
            \Log::info("Error: Funcion insertarActualizarDatosContratoListaTemporales:" . $idContrato . "\n" . $e);
        }
    }

    public static function llenartablacontratoslistatemporalesporsucursal($idFranquicia){

        //Obtener los contratos con datos = 1 de la franquicia recibida e insertar en tabla contratoslistatemporales
        DB::select("INSERT INTO contratoslistatemporales (id, id_franquicia, estatus_estadocontrato, descripcion, idcontratorelacion, created_at,
                                                                fechaentrega,fechaatraso, fechagarantia, estadogarantia, nombre_usuariocreacion, id_zona,
                                                                zona, localidad, colonia, calle, numero, nombre, telefono, nombrereferencia, telefonoreferencia,
                                                                totalreal, totalproducto, totalpromocion, totalabono, total, ultimoabono, promocionactiva, alias)
                                                                SELECT c.id, c.id_franquicia, c.estatus_estadocontrato,
                                                                (SELECT e.descripcion FROM estadocontrato e WHERE e.estatus = c.estatus_estadocontrato) as descripcion,
                                                                c.idcontratorelacion, c.created_at,
                                                                (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC LIMIT 1) as fechaentrega, c.fechaatraso,
                                                                (SELECT g.created_at FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS fechagarantia,
                                                                (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia,
                                                                c.nombre_usuariocreacion, c.id_zona, (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS zona,
                                                                c.localidad, c.colonia, c.calle, c.numero, c.nombre, c.telefono, c.nombrereferencia, c.telefonoreferencia,
                                                                c.totalreal, c.totalproducto, c.totalpromocion, c.totalabono, c.total, c.ultimoabono,
                                                                (SELECT p.estado FROM promocioncontrato p WHERE p.id_contrato = c.id AND p.id_franquicia = c.id_franquicia) AS promo, c.alias
                                                                FROM contratos c WHERE c.datos = 1 AND c.id_franquicia = '$idFranquicia'");

    }

    public static function obtenerEstatusAutorizacionContratoTemporal($idContrato){

        try {

            $estatusAutorizacion = 1;
            $contratosautorizacion = DB::select(
                "SELECT estatus
                            FROM autorizaciones
                            WHERE id_contrato = '$idContrato'
                            AND tipo IN (8,9)
                            AND estatus != '3' ORDER BY created_at DESC LIMIT 1");

            if ($contratosautorizacion != null){
                //Tiene una autorizacion
                $estatusAutorizacion = $contratosautorizacion[0]->estatus;
            }

            return $estatusAutorizacion;

        } catch (\Exception $e) {
            \Log::info("Error: Funcion obtenerEstatusAutorizacionContrato:" . $idContrato . "\n" . $e);
        }

    }

    public static function limpiarCadenaCaracteresEspeciales($cadena){

        try {

            $cadena = str_replace(
                array('ñ', 'Ñ', 'Ã', 'ç', 'Ç', 'á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ü', 'Ü'),
                array('n', 'N','N', 'c', 'C', 'a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'u', 'U'),
                $cadena
            );

            $cadena = str_replace('_', '', $cadena);
            $cadena = str_replace('-', '', $cadena);
            $cadena = str_replace("\"", '', $cadena);
            $cadena = str_replace("‘", '', $cadena);

            return preg_replace('/[^a-zA-Z0-9_ -]/s', '', $cadena); //Remover caracteres especiales.

        } catch (\Exception $e) {
            \Log::info("Error: Funcion limpiarCadenaCaracteresEspeciales:" . $cadena . "\n" . $e);
        }

    }

    public static function reiniciarNumeroVacantesSucursales(){
        //REINICIAR SOLICITUDES VACANTES - SOLO LOS DOMINGOS
        $now = Carbon::now();
        $numeroDia = $now->dayOfWeekIso;    //Obtenemos el dia de la semana actual

        if($numeroDia == 6){
            //Si es sabado -> Actualizar a estatus 5 (Cancelacion automatica) todas las solicitudes generadas sin agendar (solicitudes con estatus 0)
            DB::update("UPDATE vacantes SET estado = '5' WHERE estado = '0'");
        }
    }

    public static function validarPesoArchivosAdjuntosSucursalContrato($archivoAdjunto){

        $archivoCorrecto = true;

        //Archivo es vacio?
        if($archivoAdjunto != null){
            //Se adjunto un archivo
            $fileSize = filesize($archivoAdjunto);
            //Validar peso del archivo
            if($fileSize > 1048576){
                //Archivo con tamaño mayor a 1MB
                $archivoCorrecto = false;
            }
        }

        //Retornamos valor booleano
        return $archivoCorrecto;
    }

    public static function generarReferenciaCita($idFranquicia){

        //Obtener año actual
        $year = Carbon::now()->format('y');
        $sigReferencia = $year + '000000';

        //Extraer ultimo folio existente en BD
        $ultimaReferencia = DB::select("SELECT ac.referencia FROM agendacitas ac WHERE ac.id_franquicia = '$idFranquicia' AND ac.referencia LIKE '$year%' ORDER BY ac.created_at DESC LIMIT 1");

        if($ultimaReferencia != null){
            //Existe al menos un folio en la BD almacenado
            $referenciaBD = $ultimaReferencia[0]->referencia;
            $sigReferencia = $referenciaBD + 1;

        }else{
            //Es el primer folio a ingresar

            //Obtener identificador de franquicia
            $globalesServicioWeb = new globalesServicioWeb();
            $franquicia = DB::select("SELECT f.indice FROM franquicias f WHERE f.id = '$idFranquicia'");
            $indiceFranquicia = $franquicia[0]->indice;
            $idetificadorFranquicia = $globalesServicioWeb::obtenerIdentificadorFranquicia($indiceFranquicia);

            //Forma siguiente folio
            $sigReferencia = $year . $idetificadorFranquicia . "000";
        }

        return $sigReferencia;
    }

    public static function actualizarEstatusCitaAgendadaPorReferencia($idFranquicia, $id_usuario, $referencia){

        //Verificar tipo de referencia
        $existeReferencia = DB::select("SELECT r.tipo FROM referencias r WHERE r.referencia = '$referencia'");

        if($existeReferencia != null){
            //Existe referencia en tabla de referencias
            $tipoReferencia = $existeReferencia[0]->tipo;

            switch ($tipoReferencia){
                case '01':
                    //TIPO CITA
                    $existeCita = DB::select("SELECT * FROM agendacitas ac WHERE ac.referencia = '$referencia' AND ac.estadocita != '1'");

                    if($existeCita != null){
                        //Actualizar estatus de cita
                        DB::table('agendacitas')->where('referencia', '=', $referencia)->where('indice', '=', $existeCita[0]->indice)->update([
                            'estadocita' => '1', 'updated_at' => Carbon::now()
                        ]);

                        //Registrar movimiento de notificacion estatus de cita desde venta
                        DB::table('historialsucursal')->insert([
                            'id_usuarioC' => $id_usuario,
                            'id_franquicia' => $idFranquicia, 'tipomensaje' => '11',
                            'created_at' => Carbon::now(),
                            'cambios' => "M - Notificó que paciente '" . $existeCita[0]->nombre . "' asistió a cita agendada con fecha: '".$existeCita[0]->fechacitaagendada."' hora: '".$existeCita[0]->horacitaagendada."'",
                            'seccion' => '2'
                        ]);

                    }
                    break;
                case '02':
                    //TIPO CAMPAÑA
                    $existeCampaniaAgendada = DB::select("SELECT * FROM campaniasagendadas ca WHERE ca.referencia = '$referencia' AND ca.estado != '1'");

                    if($existeCampaniaAgendada != null){
                        //Actualizar estatus de campaña agendada
                        DB::table('campaniasagendadas')->where('referencia', '=', $referencia)->where('indice', '=', $existeCampaniaAgendada[0]->indice)->update([
                            'estado' => '1', 'updated_at' => Carbon::now()
                        ]);

                        //Datos de campaña
                        $idCampania = $existeCampaniaAgendada[0]->id_campania;
                        $campania = DB::select("SELECT c.titulo FROM campanias c WHERE c.id = '$idCampania'");
                        $nombreCampania = "Sin nombre";
                        if($campania != null){
                            $nombreCampania = $campania[0]->titulo;
                        }

                        //Registrar movimiento de notificacion estatus de cita desde venta
                        DB::table('historialsucursal')->insert([
                            'id_usuarioC' => $id_usuario,
                            'id_franquicia' => $idFranquicia, 'tipomensaje' => '15',
                            'created_at' => Carbon::now(),
                            'cambios' => "M - Notificó que paciente '" . $existeCampaniaAgendada[0]->nombre . "' obtuvo su examen de salud visual mendiante la campaña: '$nombreCampania'",
                            'seccion' => '2'
                        ]);
                    }
                    break;
            }

        }

    }

    public static function validacionAumentarDisminuirTabularPolizaActualYPolizasAnterioresSemana($idFranquicia, $idContrato, $zonaActual, $zonaActualizar, $idFranquiciaTraspaso){

        $calculofechaspago = new calculofechaspago;
        $hoy = Carbon::now();
        $hoyNumero = $hoy->dayOfWeekIso;
        $fechaSabadoSiguiente = $calculofechaspago::obtenerDia($hoy, $hoyNumero, 2); //Obtenemos la fecha del dia sabado siguiente
        $fechaLunesSiguiente = Carbon::parse($fechaSabadoSiguiente)->addDays(2)->format('Y-m-d'); //Obtenemos la fecha del dia lunes siguiente

        $contrato = DB::select("SELECT c.estatus_estadocontrato as ESTATUS,
                                                    c.pago as PAGO,
                                                            (SELECT h.fechaentrega FROM historialclinico h WHERE h.id_contrato = c.id AND h.tipo != '2' ORDER BY h.created_at DESC LIMIT 1) as FECHAENTREGAHISTORIAL,
								                                c.fechacobroini as INICIAL,
                                                                    c.fechacobrofin as FINAL,
                                                                        (SELECT g.estadogarantia FROM garantias g
                                                                            WHERE g.id_contrato = c.id AND g.estadogarantia IN (1,2) ORDER BY g.created_at DESC LIMIT 1) as ESTADOGARANTIA
                                                    FROM contratos c
                                                    WHERE c.id = '$idContrato'");

        if ($contrato != null) {
            //Existe contrato

            $pago = $contrato[0]->PAGO;
            $fechacobroini = $contrato[0]->INICIAL;
            $fechacobrofin = $contrato[0]->FINAL;

            if ($fechacobroini == null && $fechacobrofin == null) {
                //No se tienen fechas del periodo
                $arrayRespuesta = $calculofechaspago::obtenerFechasPeriodoActualOPeriodoSiguiente(Carbon::now(), $pago, true);
                $fechacobroini = $arrayRespuesta[0];
                $fechacobrofin = $arrayRespuesta[1];
            }

            if ($contrato[0]->ESTADOGARANTIA != null && ($contrato[0]->ESTATUS == 2 || $contrato[0]->ESTATUS == 4 || $contrato[0]->ESTATUS == 1
                    || $contrato[0]->ESTATUS == 9 || $contrato[0]->ESTATUS == 7 || $contrato[0]->ESTATUS == 10 || $contrato[0]->ESTATUS == 11 || $contrato[0]->ESTATUS == 12)){
                //Tiene garantia el contrato y estado del contrato es igual a ENTREGADO, ATRASADO, TERMINADO, PROCESO DE APROBACION, APROBADO, MANUFACTURA, PROCESO DE ENVIO, ENVIADO
                $consultaUltimoAbono = DB::select("SELECT created_at FROM abonos WHERE id_contrato = '$idContrato' AND tipoabono != '7' ORDER BY created_at DESC LIMIT 1");
                if ($consultaUltimoAbono != null) {
                    //Existe abono
                    if (Carbon::parse($consultaUltimoAbono[0]->created_at)->format('Y-m-d') >= Carbon::parse($fechacobroini)->format('Y-m-d')
                        && Carbon::parse($consultaUltimoAbono[0]->created_at)->format('Y-m-d') <= Carbon::parse($fechacobrofin)->format('Y-m-d')) {
                        //No pasara nada por que el abono ya se cobro en la zona que estaba por lo tanto (Se cambia la zona normalmente)
                    } else {
                        //Se disminuira el contrato en el tabular al cobrador de la zona actual y se le aumentara al cobrador de la zona a la que se actualizara
                        self::aumentarDisminuirTabularPolizaActualYPolizasAnterioresSemana($hoy, $hoyNumero, $idFranquicia, $idContrato, $zonaActual, $zonaActualizar, $idFranquiciaTraspaso);
                    }
                } else {
                    //No existe abono
                    //Se disminuira el contrato en el tabular al cobrador de la zona actual y se le aumentara al cobrador de la zona a la que se actualizara
                    self::aumentarDisminuirTabularPolizaActualYPolizasAnterioresSemana($hoy, $hoyNumero, $idFranquicia, $idContrato, $zonaActual, $zonaActualizar, $idFranquiciaTraspaso);
                }
            }elseif ($contrato[0]->ESTATUS == 7 || $contrato[0]->ESTATUS == 10 || $contrato[0]->ESTATUS == 11
                && (Carbon::parse($contrato[0]->FECHAENTREGAHISTORIAL)->format('Y-m-d') < Carbon::parse($fechaLunesSiguiente)->format('Y-m-d'))) {
                //Estado del contrato es igual a APROBADO, MANUFACTURA O PROCESO DE ENVIO Y fecha entrega es en esta semana y no tiene garantia
                self::aumentarDisminuirTabularPolizaActualYPolizasAnterioresSemana($hoy, $hoyNumero, $idFranquicia, $idContrato, $zonaActual, $zonaActualizar, $idFranquiciaTraspaso);
            }elseif ($contrato[0]->ESTATUS == 12){
                //Estado del contrato es igual a ENVIADO
                $consultaUltimoAbono = DB::select("SELECT indice FROM abonos WHERE id_contrato = '$idContrato' AND tipoabono = '2' ORDER BY created_at DESC LIMIT 1");
                if ($consultaUltimoAbono == null) {
                    //No tiene abono de entrega
                    self::aumentarDisminuirTabularPolizaActualYPolizasAnterioresSemana($hoy, $hoyNumero, $idFranquicia, $idContrato, $zonaActual, $zonaActualizar, $idFranquiciaTraspaso);
                }
            }elseif($contrato[0]->ESTATUS == 2 || $contrato[0]->ESTATUS == 4){
                //Estado del contrato es igual a ENTREGADO, ATRASADO
                $consultaUltimoAbono = DB::select("SELECT created_at FROM abonos WHERE id_contrato = '$idContrato' AND tipoabono != '7' ORDER BY created_at DESC LIMIT 1");
                if ($consultaUltimoAbono != null) {
                    //Existe abono
                    if (Carbon::parse($consultaUltimoAbono[0]->created_at)->format('Y-m-d') >= Carbon::parse($fechacobroini)->format('Y-m-d')
                        && Carbon::parse($consultaUltimoAbono[0]->created_at)->format('Y-m-d') <= Carbon::parse($fechacobrofin)->format('Y-m-d')) {
                        //No pasara nada por que el abono ya se cobro en la zona que estaba por lo tanto (Se cambia la zona normalmente)
                    } else {
                        //Se disminuira el contrato en el tabular al cobrador de la zona actual y se le aumentara al cobrador de la zona a la que se actualizara
                        self::aumentarDisminuirTabularPolizaActualYPolizasAnterioresSemana($hoy, $hoyNumero, $idFranquicia, $idContrato, $zonaActual, $zonaActualizar, $idFranquiciaTraspaso);
                    }
                } else {
                    //No existe abono
                    //Se disminuira el contrato en el tabular al cobrador de la zona actual y se le aumentara al cobrador de la zona a la que se actualizara
                    self::aumentarDisminuirTabularPolizaActualYPolizasAnterioresSemana($hoy, $hoyNumero, $idFranquicia, $idContrato, $zonaActual, $zonaActualizar, $idFranquiciaTraspaso);
                }
            }

        }

    }

    public static function aumentarDisminuirTabularPolizaActualYPolizasAnterioresSemana($hoy, $hoyNumero, $idFranquicia, $idContrato, $zonaActual, $zonaActualizar, $idFranquiciaTraspaso){

        $idPrimerPoliza = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' ORDER BY created_at DESC LIMIT 1");

        $cobradoresAsignadosAZonaActual = self::obtenerCobradoresAsignadosZona($zonaActual); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona actual
        $cobradoresAsignadosAZonaActualizar = self::obtenerCobradoresAsignadosZona($zonaActualizar); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona a actualizar

        if (strlen($idFranquiciaTraspaso) > 0) {
            //idFranquiciaTraspaso es diferente de vacio

            $idPrimerPolizaTraspaso = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquiciaTraspaso' ORDER BY created_at DESC LIMIT 1");

            if($hoyNumero != 2) {
                //miercoles, jueves, viernes, sabado o lunes

                $fecha = Carbon::parse($hoy)->format('Y-m-d');

                $hoyNumeroTemporal = $hoyNumero;
                if ($hoyNumero == 1) {
                    //Es lunes
                    $hoyNumeroTemporal = 8;
                }

                //Disminucion y aumento a polizas anteriores
                for ($i = ($hoyNumeroTemporal - 2); $i > 0; $i--) {

                    //Obtener fechas de dias anteriores
                    $fecha = Carbon::create($fecha)->subDays(1)->format('Y-m-d'); //Descontando dias

                    //Polizas de la franquicia actual
                    $poliza = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' AND STR_TO_DATE(created_at,'%Y-%m-%d') = STR_TO_DATE('$fecha','%Y-%m-%d')");

                    if ($poliza != null) {
                        //Existe poliza
                        $idPrimerPoliza = $poliza[0]->id;

                        //Disminuir a tabular a cobradores dados de alta en la zona actual del contrato
                        if ($cobradoresAsignadosAZonaActual != null) {
                            //Existen cobradores
                            foreach ($cobradoresAsignadosAZonaActual as $cobradorAsignadoAZonaActual) {
                                //Recorrido cobradores
                                $polizacobranza = DB::select("SELECT tabular FROM polizacobranza WHERE id_franquicia = '$idFranquicia'
                                                                    AND id_poliza = '$idPrimerPoliza' AND id_usuario = '" . $cobradorAsignadoAZonaActual->id . "'");
                                if ($polizacobranza != null) {
                                    //Existe registro en tabla polizacobranza
                                    DB::table("polizacobranza")
                                        ->where("id_usuario", "=", $cobradorAsignadoAZonaActual->id)
                                        ->where("id_franquicia", "=", $idFranquicia)
                                        ->where("id_poliza", "=", $idPrimerPoliza)
                                        ->update([
                                            "tabular" => $polizacobranza[0]->tabular - 1
                                        ]);
                                }
                            }
                        }

                    }

                    //Polizas de la franquicia traspaso
                    $polizaTraspaso = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquiciaTraspaso' AND STR_TO_DATE(created_at,'%Y-%m-%d') = STR_TO_DATE('$fecha','%Y-%m-%d')");

                    if ($polizaTraspaso != null) {
                        //Existe poliza
                        $idPrimerPolizaTraspaso = $polizaTraspaso[0]->id;

                        //Aumento a tabular a cobradores dados de alta en la zona a actualizar del contrato
                        if ($cobradoresAsignadosAZonaActualizar != null) {
                            //Existen cobradores
                            foreach ($cobradoresAsignadosAZonaActualizar as $cobradorAsignadoAZonaActualizar) {
                                //Recorrido cobradores
                                $polizacobranza = DB::select("SELECT tabular FROM polizacobranza WHERE id_franquicia = '$idFranquiciaTraspaso'
                                                                    AND id_poliza = '$idPrimerPolizaTraspaso' AND id_usuario = '" . $cobradorAsignadoAZonaActualizar->id . "'");
                                if ($polizacobranza != null) {
                                    //Existe registro en tabla polizacobranza
                                    DB::table("polizacobranza")
                                        ->where("id_usuario", "=", $cobradorAsignadoAZonaActualizar->id)
                                        ->where("id_franquicia", "=", $idFranquiciaTraspaso)
                                        ->where("id_poliza", "=", $idPrimerPolizaTraspaso)
                                        ->update([
                                            "tabular" => $polizacobranza[0]->tabular + 1
                                        ]);
                                }
                            }
                        }

                    }

                }

            }else {
                //Es martes
                $idPrimerPoliza = $idPrimerPoliza[0]->id;
                $idPrimerPolizaTraspaso = $idPrimerPolizaTraspaso[0]->id;
            }

            //Disminucion y aumento de tabular a poliza actual
            $idPolizaActual = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' ORDER BY created_at DESC LIMIT 1");

            if ($idPolizaActual != null) {
                //Existe poliza actual
                $idPolizaActual = $idPolizaActual[0]->id;

                //Disminuir a tabular a cobradores dados de alta en la zona actual del contrato
                if ($cobradoresAsignadosAZonaActual != null) {
                    //Existen cobradores

                    foreach ($cobradoresAsignadosAZonaActual as $cobradorAsignadoAZonaActual) {
                        //Recorrido cobradores
                        $polizacobranza = DB::select("SELECT tabular FROM polizacobranza WHERE id_franquicia = '$idFranquicia'
                                                                    AND id_poliza = '$idPolizaActual' AND id_usuario = '" . $cobradorAsignadoAZonaActual->id . "'");
                        if ($polizacobranza != null) {
                            //Existe registro en tabla polizacobranza
                            DB::table("polizacobranza")
                                ->where("id_usuario", "=", $cobradorAsignadoAZonaActual->id)
                                ->where("id_franquicia", "=", $idFranquicia)
                                ->where("id_poliza", "=", $idPolizaActual)
                                ->update([
                                    "tabular" => $polizacobranza[0]->tabular - 1
                                ]);
                        }
                    }
                }

            }

            //Disminucion y aumento de tabular a poliza traspaso
            $idPolizaActualTraspaso = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquiciaTraspaso' ORDER BY created_at DESC LIMIT 1");

            if ($idPolizaActualTraspaso != null) {
                //Existe poliza actual
                $idPolizaActualTraspaso = $idPolizaActualTraspaso[0]->id;

                //Aumento a tabular a cobradores dados de alta en la zona a actualizar del contrato
                if ($cobradoresAsignadosAZonaActualizar != null) {
                    //Existen cobradores
                    foreach ($cobradoresAsignadosAZonaActualizar as $cobradorAsignadoAZonaActualizar) {
                        //Recorrido cobradores
                        $polizacobranza = DB::select("SELECT tabular FROM polizacobranza WHERE id_franquicia = '$idFranquiciaTraspaso'
                                                                    AND id_poliza = '$idPolizaActualTraspaso' AND id_usuario = '" . $cobradorAsignadoAZonaActualizar->id . "'");
                        if ($polizacobranza != null) {
                            //Existe registro en tabla polizacobranza
                            DB::table("polizacobranza")
                                ->where("id_usuario", "=", $cobradorAsignadoAZonaActualizar->id)
                                ->where("id_franquicia", "=", $idFranquiciaTraspaso)
                                ->where("id_poliza", "=", $idPolizaActualTraspaso)
                                ->update([
                                    "tabular" => $polizacobranza[0]->tabular + 1
                                ]);
                        }
                    }
                }

            }

            //Eliminar contratos que esten en el tabular de los cobradores de la zona anterior de la tabla polizacontratoscobranza
            DB::delete("DELETE pcc FROM polizacontratoscobranza pcc INNER JOIN contratos c ON pcc.id_contrato = c.id
                            WHERE pcc.id_poliza = '$idPrimerPoliza' AND c.id_zona = '$zonaActual' AND c.id = '$idContrato'");

            //Insertar contrato en polizacontratoscobranza
            DB::table('polizacontratoscobranza')->insert([
                'id_poliza' => $idPrimerPolizaTraspaso, 'id_contrato' => $idContrato, 'created_at' => $hoy
            ]);

        }else {
            //idFranquiciaTraspaso es igual a vacio

            if($hoyNumero != 2) {
                //miercoles, jueves, viernes, sabado o lunes

                $fecha = Carbon::parse($hoy)->format('Y-m-d');

                $hoyNumeroTemporal = $hoyNumero;
                if ($hoyNumero == 1) {
                    //Es lunes
                    $hoyNumeroTemporal = 8;
                }

                //Disminucion y aumento a polizas anteriores
                for ($i = ($hoyNumeroTemporal - 2); $i > 0; $i--) {

                    //Obtener fechas de dias anteriores
                    $fecha = Carbon::create($fecha)->subDays(1)->format('Y-m-d'); //Descontando dias
                    $poliza = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' AND STR_TO_DATE(created_at,'%Y-%m-%d') = STR_TO_DATE('$fecha','%Y-%m-%d')");

                    if ($poliza != null) {
                        //Existe poliza
                        $idPrimerPoliza = $poliza[0]->id;

                        //Disminuir a tabular a cobradores dados de alta en la zona actual del contrato
                        if ($cobradoresAsignadosAZonaActual != null) {
                            //Existen cobradores
                            foreach ($cobradoresAsignadosAZonaActual as $cobradorAsignadoAZonaActual) {
                                //Recorrido cobradores
                                $polizacobranza = DB::select("SELECT tabular FROM polizacobranza WHERE id_franquicia = '$idFranquicia'
                                                                    AND id_poliza = '$idPrimerPoliza' AND id_usuario = '" . $cobradorAsignadoAZonaActual->id . "'");
                                if ($polizacobranza != null) {
                                    //Existe registro en tabla polizacobranza
                                    DB::table("polizacobranza")
                                        ->where("id_usuario", "=", $cobradorAsignadoAZonaActual->id)
                                        ->where("id_franquicia", "=", $idFranquicia)
                                        ->where("id_poliza", "=", $idPrimerPoliza)
                                        ->update([
                                            "tabular" => $polizacobranza[0]->tabular - 1
                                        ]);
                                }
                            }
                        }

                        //Aumento a tabular a cobradores dados de alta en la zona a actualizar del contrato
                        if ($cobradoresAsignadosAZonaActualizar != null) {
                            //Existen cobradores
                            foreach ($cobradoresAsignadosAZonaActualizar as $cobradorAsignadoAZonaActualizar) {
                                //Recorrido cobradores
                                $polizacobranza = DB::select("SELECT tabular FROM polizacobranza WHERE id_franquicia = '$idFranquicia'
                                                                    AND id_poliza = '$idPrimerPoliza' AND id_usuario = '" . $cobradorAsignadoAZonaActualizar->id . "'");
                                if ($polizacobranza != null) {
                                    //Existe registro en tabla polizacobranza
                                    DB::table("polizacobranza")
                                        ->where("id_usuario", "=", $cobradorAsignadoAZonaActualizar->id)
                                        ->where("id_franquicia", "=", $idFranquicia)
                                        ->where("id_poliza", "=", $idPrimerPoliza)
                                        ->update([
                                            "tabular" => $polizacobranza[0]->tabular + 1
                                        ]);
                                }
                            }
                        }

                    }

                }

            }else {
                //Es martes
                $idPrimerPoliza = $idPrimerPoliza[0]->id;
            }

            //Disminucion y aumento de tabular a poliza actual
            $idPolizaActual = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' ORDER BY created_at DESC LIMIT 1");

            if ($idPolizaActual != null) {
                //Existe poliza actual
                $idPolizaActual = $idPolizaActual[0]->id;

                //Disminuir a tabular a cobradores dados de alta en la zona actual del contrato
                if ($cobradoresAsignadosAZonaActual != null) {
                    //Existen cobradores

                    foreach ($cobradoresAsignadosAZonaActual as $cobradorAsignadoAZonaActual) {
                        //Recorrido cobradores
                        $polizacobranza = DB::select("SELECT tabular FROM polizacobranza WHERE id_franquicia = '$idFranquicia'
                                                                    AND id_poliza = '$idPolizaActual' AND id_usuario = '" . $cobradorAsignadoAZonaActual->id . "'");
                        if ($polizacobranza != null) {
                            //Existe registro en tabla polizacobranza
                            DB::table("polizacobranza")
                                ->where("id_usuario", "=", $cobradorAsignadoAZonaActual->id)
                                ->where("id_franquicia", "=", $idFranquicia)
                                ->where("id_poliza", "=", $idPolizaActual)
                                ->update([
                                    "tabular" => $polizacobranza[0]->tabular - 1
                                ]);
                        }
                    }
                }

                //Aumento a tabular a cobradores dados de alta en la zona a actualizar del contrato
                if ($cobradoresAsignadosAZonaActualizar != null) {
                    //Existen cobradores
                    foreach ($cobradoresAsignadosAZonaActualizar as $cobradorAsignadoAZonaActualizar) {
                        //Recorrido cobradores
                        $polizacobranza = DB::select("SELECT tabular FROM polizacobranza WHERE id_franquicia = '$idFranquicia'
                                                                    AND id_poliza = '$idPolizaActual' AND id_usuario = '" . $cobradorAsignadoAZonaActualizar->id . "'");
                        if ($polizacobranza != null) {
                            //Existe registro en tabla polizacobranza
                            DB::table("polizacobranza")
                                ->where("id_usuario", "=", $cobradorAsignadoAZonaActualizar->id)
                                ->where("id_franquicia", "=", $idFranquicia)
                                ->where("id_poliza", "=", $idPolizaActual)
                                ->update([
                                    "tabular" => $polizacobranza[0]->tabular + 1
                                ]);
                        }
                    }
                }

            }

            //Eliminar contratos que esten en el tabular de los cobradores de la zona anterior de la tabla polizacontratoscobranza
            DB::delete("DELETE pcc FROM polizacontratoscobranza pcc INNER JOIN contratos c ON pcc.id_contrato = c.id
                            WHERE pcc.id_poliza = '$idPrimerPoliza' AND c.id_zona = '$zonaActual' AND c.id = '$idContrato'");

            //Insertar contrato en polizacontratoscobranza
            DB::table('polizacontratoscobranza')->insert([
                'id_poliza' => $idPrimerPoliza, 'id_contrato' => $idContrato, 'created_at' => $hoy
            ]);

        }

    }

    public static function obtenerIdPrimerPolizaSemana($idFranquicia){

        $hoy = Carbon::now();
        //$hoy = Carbon::parse("2023-03-13");
        $hoyNumero = $hoy->dayOfWeekIso; // Comienza en lunes -> 1 y obtenemos el dia actual de la semana

        $idPrimerPoliza = null;

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
                }

            }

        }else {
            //Martes
            $idPrimerPoliza = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' ORDER BY created_at DESC LIMIT 1");
            $idPrimerPoliza = $idPrimerPoliza[0]->id;
        }

        return $idPrimerPoliza;

    }

    public static function verificarPiezasRestantesProducto($idProducto){

        //Numero de piezas existentes
        $productoPiezas = DB::select("SELECT p.piezas FROM producto p WHERE p.id = '$idProducto'");

        if($productoPiezas[0]->piezas <= 10){
            //Solo existen 10 piezas o menos del producto
            $now = Carbon::now();
            $fechaNotificacion = Carbon::parse($now)->format('Y-m-d');

            // Insertar producto por agotarse en tabla de notificacionesvisuzalicaciones
            DB::table("notificacionesvisualizaciones")->insert([
                'fechanotificacion' => $fechaNotificacion,
                'numeronotificaciones' => 20,
                'tiponotificacion' => '0',
                'id_producto' => $idProducto,
                'created_at' => $now
            ]);
        }
    }

    public static function insertarEliminarContratosLioFuga($idContrato, $cambio, $opcion){
        //Opciones
        //0 -> insertar
        //1 -> eliminar

        switch ($opcion){
            case 0:
                //Insertar
                $contrato = DB::select("SELECT c.id_franquicia, c.nombre AS nombre, c.coloniaentrega AS colonia, c.calleentrega AS calle,
                                              c.numeroentrega AS numero, c.telefono, c.created_at
                                              FROM contratos c WHERE c.id = '$idContrato'");
                if($contrato != null){
                    //Verificar si ya existe contrato en tabla de contratosliofuga
                    $existeContrato = DB::select("SELECT * FROM contratosliofuga clf WHERE clf.id_contrato = '$idContrato'");

                    if($existeContrato == null){
                        //No existe el contrato registrado

                        //Insertar registro en tabla de contratosliofuga
                        DB::table("contratosliofuga")->insert([
                            'id_contrato' => $idContrato,
                            'id_franquicia' => $contrato[0]->id_franquicia,
                            'nombre' => $contrato[0]->nombre,
                            'colonia' => $contrato[0]->colonia,
                            'calle' => $contrato[0]->calle,
                            'numero' => $contrato[0]->numero,
                            'telefono' => $contrato[0]->telefono,
                            'cambios' => $cambio,
                            'created_at' => Carbon::now()
                        ]);
                    }
                }
                break;

            case 1:
                //Eliminar
                $existeContrato = DB::select("SELECT * FROM contratosliofuga clf WHERE clf.id_contrato = '$idContrato'");
                //Verificar si existe el contrato en la tabla de contratosliofuga
                if($existeContrato != null){
                    //Existe el contrato - Eliminarlo
                    DB::delete("DELETE FROM contratosliofuga WHERE id_contrato = '$idContrato'");
                }
                break;
        }

    }

    public static function obtenerIdentificadorFormatoFranquiciaAsistencia($idFranquicia){
        $identificador = "000";
        $franquicia = DB::select("SELECT f.indice FROM franquicias f WHERE f.id = '$idFranquicia' LIMIT 1");

        if($franquicia != null){
            $indice = $franquicia[0]->indice;
            switch (strlen($indice)){
                case 1:
                    //Indice del 1-9
                    $identificador = "00" . $indice;
                    break;
                case 2:
                    //Indice del 10-99
                    $identificador = "0" . $indice;
                    break;
                case 3:
                    //Indice del 100-999
                    $identificador = $indice;
                    break;
            }
        }
        return $identificador;
    }

    public static function actualizarRegistrosPolizaCobranzaPolizasAnteriores($idFranquicia, $idcobradoreliminado, $idcobradornuevo, $id_zona, $banderapolizaactual) {

        $poliza = DB::select("SELECT id, created_at FROM poliza WHERE id_franquicia = '$idFranquicia' ORDER BY created_at DESC");

        if ($poliza != null) {
            //Existe poliza

            if ($banderapolizaactual) {
                //Actualizar poliza actual
                //Actualizar polizacobranza actual al cobrador nuevo

                //Por zona
                $polizacobranza = DB::select("SELECT id FROM polizacobranza WHERE id_zona = '$id_zona'
                                AND id_franquicia = '$idFranquicia' AND id_poliza = '" . $poliza[0]->id . "'");
                if (strlen($idcobradoreliminado) > 0) {
                    //idcobradoreliminado es diferente de vacio (Por idcobradoreliminado)
                    $polizacobranza = DB::select("SELECT id FROM polizacobranza WHERE id_usuario = '$idcobradoreliminado'
                                AND id_franquicia = '$idFranquicia' AND id_poliza = '" . $poliza[0]->id . "'");
                }

                if ($polizacobranza != null) {
                    //Existe registro
                    DB::table("polizacobranza")
                        ->where("id", "=", $polizacobranza[0]->id)
                        ->update([
                            "id_usuario" => $idcobradornuevo
                        ]);
                }
            }

            $fechaCreacionPolizaEntrante = Carbon::parse($poliza[0]->created_at)->format('Y-m-d');

            if (Carbon::parse($fechaCreacionPolizaEntrante)->dayOfWeekIso != 2) {
                //Es miercoles, jueves, viernes, sabado o lunes

                $fecha = $fechaCreacionPolizaEntrante;

                $limite = 2;
                $diasdecremento = Carbon::parse($fechaCreacionPolizaEntrante)->dayOfWeekIso;
                if (Carbon::parse($fechaCreacionPolizaEntrante)->dayOfWeekIso == 1) {
                    //Es lunes
                    $limite = 1;
                    $diasdecremento = 7;
                }

                for ($i = $diasdecremento; $i > $limite; $i--) {
                    //Dia es mayor o igual a martes

                    //Obtener fechas de dias anteriores
                    $fecha = Carbon::create($fecha)->subDays(1)->format('Y-m-d'); //Descontando dias

                    if (Carbon::parse($fecha)->dayOfWeekIso != 7) {
                        //Fecha diferente de domingo

                        $polizaanterior = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' AND STR_TO_DATE(created_at,'%Y-%m-%d') = STR_TO_DATE('$fecha','%Y-%m-%d')");
                        $polizaAnteriorId = $polizaanterior == null ? 0 : $polizaanterior[0]->id;

                        if (strlen($polizaAnteriorId) > 0) {
                            //Obtener poliza anterior

                            //Por zona
                            $polizacobranza = DB::select("SELECT id FROM polizacobranza WHERE id_zona = '$id_zona'
                                                                    AND id_franquicia = '$idFranquicia' AND id_poliza = '" . $polizaAnteriorId . "'");
                            if (strlen($idcobradoreliminado) > 0) {
                                //idcobradoreliminado es diferente de vacio (Por idcobradoreliminado)
                                $polizacobranza = DB::select("SELECT id FROM polizacobranza WHERE id_usuario = '$idcobradoreliminado'
                                                                        AND id_franquicia = '$idFranquicia' AND id_poliza = '" . $polizaAnteriorId . "'");
                            }

                            if ($polizacobranza != null) {
                                //Existe registro
                                DB::table("polizacobranza")
                                    ->where("id", "=", $polizacobranza[0]->id)
                                    ->update([
                                        "id_usuario" => $idcobradornuevo
                                    ]);
                            }

                        }

                    }

                }

            }

        }

    }

    public static function actualizarPrecioDolar(){

        $url = 'https://api.exchangerate-api.com/v4/latest/USD';

        try {
            // Realiza la solicitud HTTP
            $json_data = file_get_contents($url);

            // Decodifica la respuesta JSON
            $data = json_decode($json_data, true);

            // Obtiene el precio del dólar en la moneda deseada
            $precio_dolar = $data['rates']['MXN'];
            $precio_dolar = ceil($precio_dolar);

            //Actualizar valor en DB
            $datosFtp = DB::select("SELECT * FROM configuracionmovil ORDER BY created_at DESC LIMIT 1");

            DB::table('configuracionmovil')->where('indice','=', $datosFtp[0]->indice)->update([
                'preciodolar' => Crypt::encryptString($precio_dolar),
                'updated_at' => Carbon::now()
            ]);

        } catch (Exception $e) {
            \Log::info("ERROR: (actualizarPrecioDolar): " . $e->getMessage());
        }

    }

    public static function insertareliminaridusuarioexcepciones($idUsuario, $tipo, $bandera) {

        //bandera
        //true - Insertar registro
        //false - Eliminar registro

        if ($bandera) {
            //Insertar
            $existeExcepcion = DB::select("SELECT indice FROM excepciones WHERE id_usuario = '$idUsuario' AND tipo = '$tipo' ORDER BY created_at DESC");

            if ($existeExcepcion == null) {
                //No existe registro en la tabla
                DB::table('excepciones')->insert([
                    'id_usuario' => $idUsuario,
                    'tipo' => $tipo,
                    'created_at' => Carbon::now(),
                ]);
            }

        }else {
            //Eliminar
            //Eliminar registros de tabla excepciones
            DB::delete("DELETE FROM excepciones WHERE id_usuario = '$idUsuario' AND tipo = '$tipo'");
        }

    }

    public static function insertarActualizarTablaAbonoMinimoContratos($idContrato, $id_zona, $abonominimocontrato) {

        $existeIdContrato = DB::select("SELECT indice FROM abonominimocontratos WHERE id_contrato = '$idContrato'");

        if ($existeIdContrato == null) {
            //No existe registro en tabla
            DB::table('abonominimocontratos')->insert([
                'id_contrato' => $idContrato, 'id_zona' => $id_zona,
                'abonominimo' => $abonominimocontrato, 'created_at' => Carbon::now()
            ]);
        }else {
            //Existe registro en tabla
            DB::table("abonominimocontratos")
                ->where("id_contrato", "=", $idContrato)
                ->update([
                    "abonominimo" => $abonominimocontrato,
                    'updated_at' => Carbon::now()
                ]);
        }

    }

    public static function eliminarimagencodigobarrasusuarios(){
        // Definir la carpeta en la que se encuentran las imágenes
        $carpeta = 'uploads/imagenes/barcode';

        // Obtener una lista de todos los archivos en la carpeta
        $archivos = Storage::disk('disco')->files($carpeta);

        // Eliminar cada archivo
        foreach ($archivos as $archivo) {
            Storage::disk('disco')->delete($archivo);
        }

        \Log::info("IMAGENES CODIGO DE BARRAS USUARIOS ELIMINADAS CORRECTAMENTE.");
    }

}//clase
