<?php

namespace App\Http\Controllers\Dominios\Administracion;

use App\Clases\polizaGlobales;
use App\Imports\CsvImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use DateTime;
use Illuminate\Support\Facades\Validator;

class reportes extends Controller
{
    public function listacontratosreportes($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 15)) {
            //Rol administrador, director, principal o confirmaciones

            $now = Carbon::now();
            $contratosreportes = null;
            $franquicias = null;
            $idUsuario = Auth::id();

            if(((Auth::user()->rol_id) == 15) || ((Auth::user()->rol_id) == 7)) {
                //Rol confirmaciones o director

                if(((Auth::user()->rol_id) == 15)) {
                    //Rol confirmaciones
                    $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f INNER JOIN sucursalesconfirmaciones sc ON f.id = sc.id_franquicia
                                                            WHERE sc.id_usuario = '$idUsuario'");
                    $contratosreportes = DB::select("SELECT r.created_at AS FECHAENVIO, UPPER(c.id) AS CONTRATO,
                                                                        (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS,
                                                                        UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.entrecallesentrega) AS ENTRECALLES, UPPER(c.coloniaentrega) AS COLONIA, UPPER(c.calleentrega) AS CALLE,
                                                                        UPPER(c.numeroentrega) AS NUMERO, UPPER(c.nombre) AS NOMBRE,UPPER(c.telefono) AS TELEFONO,
                                                                        (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS ZONA, c.created_at AS FECHAVENTA,c.total as TOTAL,
                                                                        c.pago as FORMAPAGO,
                                                                        (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as FECHAENTREGA,
                                                                        COALESCE((SELECT a.created_at FROM abonos a WHERE a.id_contrato = c.id ORDER BY a.created_at DESC LIMIT 1), '') as ULTIMOABONO
                                                                        FROM contratos c INNER JOIN registroestadocontrato r ON r.id_contrato = c.id
                                                                        WHERE STR_TO_DATE(r.created_at,'%Y-%m-%d') >= STR_TO_DATE('$now','%Y-%m-%d')
                                                                        AND STR_TO_DATE(r.created_at,'%Y-%m-%d') <= STR_TO_DATE('$now','%Y-%m-%d') AND r.estatuscontrato = '12'
                                                                        AND c.id_franquicia IN (SELECT f.id FROM franquicias f INNER JOIN sucursalesconfirmaciones sc ON f.id = sc.id_franquicia
                                                                        WHERE sc.id_usuario = '$idUsuario')
                                                                        ORDER BY r.created_at DESC;");
                }else {
                    //Rol director
                    $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000'");
                    $contratosreportes = DB::select("SELECT r.created_at AS FECHAENVIO, UPPER(c.id) AS CONTRATO,
                                                          (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS,
                                                                        UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.entrecallesentrega) AS ENTRECALLES, UPPER(c.coloniaentrega) AS COLONIA, UPPER(c.calleentrega) AS CALLE,
                                                                        UPPER(c.numeroentrega) AS NUMERO, UPPER(c.nombre) AS NOMBRE,UPPER(c.telefono) AS TELEFONO,
                                                                        (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS ZONA, c.created_at AS FECHAVENTA,c.total as TOTAL,
                                                                        c.pago as FORMAPAGO,
                                                                        (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as FECHAENTREGA,
                                                                        COALESCE((SELECT a.created_at FROM abonos a WHERE a.id_contrato = c.id ORDER BY a.created_at DESC LIMIT 1), '') as ULTIMOABONO
                                                                        FROM contratos c INNER JOIN registroestadocontrato r ON r.id_contrato = c.id
                                                                        WHERE STR_TO_DATE(r.created_at,'%Y-%m-%d') >= STR_TO_DATE('$now','%Y-%m-%d')
                                                                        AND STR_TO_DATE(r.created_at,'%Y-%m-%d') <= STR_TO_DATE('$now','%Y-%m-%d') AND r.estatuscontrato = '12'
                                                                        ORDER BY r.created_at DESC;");
                }

            }else {
                //Rol administrador o principal
                $idFranquicia = self::obtenerIdFranquiciaUsuario($idUsuario);
                $contratosreportes = DB::select("SELECT r.created_at AS FECHAENVIO, UPPER(c.id) AS CONTRATO,
                                                                        (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS,
                                                                        UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.entrecallesentrega) AS ENTRECALLES, UPPER(c.coloniaentrega) AS COLONIA, UPPER(c.calleentrega) AS CALLE,
                                                                        UPPER(c.numeroentrega) AS NUMERO, UPPER(c.nombre) AS NOMBRE,UPPER(c.telefono) AS TELEFONO,
                                                                        (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS ZONA, c.created_at AS FECHAVENTA,c.total as TOTAL,
                                                                        c.pago as FORMAPAGO,
                                                                        (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as FECHAENTREGA,
                                                                        COALESCE((SELECT a.created_at FROM abonos a WHERE a.id_contrato = c.id ORDER BY a.created_at DESC LIMIT 1), '') as ULTIMOABONO
                                                                        FROM contratos c INNER JOIN registroestadocontrato r ON r.id_contrato = c.id
                                                                        WHERE STR_TO_DATE(r.created_at,'%Y-%m-%d') >= STR_TO_DATE('$now','%Y-%m-%d')
                                                                        AND STR_TO_DATE(r.created_at,'%Y-%m-%d') <= STR_TO_DATE('$now','%Y-%m-%d') AND r.estatuscontrato = '12'
                                                                        AND c.id_franquicia = '$idFranquicia'
                                                                        ORDER BY r.created_at DESC;");

            }

            return view('administracion.reportes.tabla', [
                'contratosreportes' => $contratosreportes,
                'franquicias' => $franquicias,
                'idFranquicia' => $idFranquicia
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function filtrarlistacontratosreportes()
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 15)) {
            //Rol administrador, director, principal o confirmaciones

            $now = Carbon::now();
            $contratosreportes = null;
            $franquicias = null;
            $idFranquicia = request("idFranquicia");
            $idUsuario = Auth::id();
            $fechainibuscar = request('fechainibuscar');
            $fechafinbuscar = request('fechafinbuscar');
            $zonaSeleccionada = request('zonaSeleccionada');
            $franquiciaSeleccionada = request('franquiciaSeleccionada');

            $cadenaFechaIniYFechaFin = " ";
            $cadenaZona = " ";
            $cadenaFranquiciaSeleccionada = " ";

            $idFranquiciaFiltro = "";
            $cadenaFranquiciaFiltro = "";
            $cadenaZonaFiltro = "";

            //Validacion para fechaini y fechafin
            if ($fechainibuscar == null && $fechafinbuscar == null) {
                $cadenaFechaIniYFechaFin = " WHERE STR_TO_DATE(r.created_at,'%Y-%m-%d') >= STR_TO_DATE('$now','%Y-%m-%d')
                                             AND STR_TO_DATE(r.created_at,'%Y-%m-%d') <= STR_TO_DATE('$now','%Y-%m-%d') AND r.estatuscontrato = '12'";
            }else {

                if (strlen($fechafinbuscar) > 0 && strlen($fechainibuscar) == 0) {
                    //fechafin diferente de vacio y fechaini vacio
                    return redirect()->route('listacontratosreportes')->with('alerta', 'Debes agregar una fecha inicial');
                }

                if (strlen($fechainibuscar) > 0) {
                    //fechaini diferente de vacio
                    $fechainibuscar = Carbon::parse($fechainibuscar)->format('Y-m-d');
                    if (strlen($fechafinbuscar) > 0) {
                        //fechafin diferente de vacio
                        $fechafinbuscar = Carbon::parse($fechafinbuscar)->format('Y-m-d');
                    } else {
                        //fechafin vacio
                        $fechafinbuscar = Carbon::parse(Carbon::now())->format('Y-m-d');
                    }
                    if ($fechafinbuscar < $fechainibuscar) {
                        //fechafin menor a fechaini
                        return redirect()->route('listacontratosreportes')->with('alerta', 'La fecha inicial debe ser menor o igual a la final.');
                    }

                    $cadenaFechaIniYFechaFin = " WHERE STR_TO_DATE(r.created_at,'%Y-%m-%d') >= STR_TO_DATE('$fechainibuscar','%Y-%m-%d')
                                                 AND STR_TO_DATE(r.created_at,'%Y-%m-%d') <= STR_TO_DATE('$fechafinbuscar','%Y-%m-%d') AND r.estatuscontrato = '12'";
                }

            }

            if($zonaSeleccionada != null) {
                if($franquiciaSeleccionada != null) {
                    $cadenaZona = " AND c.id_zona = (SELECT z.id FROM zonas z WHERE z.zona = '$zonaSeleccionada' AND z.id_franquicia = '$franquiciaSeleccionada')";
                }else {
                    $cadenaZona = " AND c.id_zona = (SELECT z.id FROM zonas z WHERE z.zona = '$zonaSeleccionada' AND z.id_franquicia = c.id_franquicia)";
                }

                $cadenaZonaFiltro = " zona: '" . $zonaSeleccionada . "'";
            }

            if($franquiciaSeleccionada != null) {
                $cadenaFranquiciaSeleccionada = " AND c.id_franquicia = '$franquiciaSeleccionada'";
            }

            if(((Auth::user()->rol_id) == 15) || ((Auth::user()->rol_id) == 7)) {
                //Rol confirmaciones o director

                if(((Auth::user()->rol_id) == 15)) {
                    //Rol confirmaciones
                    $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f INNER JOIN sucursalesconfirmaciones sc ON f.id = sc.id_franquicia
                                                            WHERE sc.id_usuario = '$idUsuario'");
                    $contratosreportes = DB::select("SELECT r.created_at AS FECHAENVIO, UPPER(c.id) AS CONTRATO,
                                                                    (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS,
                                                                    UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.entrecallesentrega) AS ENTRECALLES, UPPER(c.coloniaentrega) AS COLONIA, UPPER(c.calleentrega) AS CALLE,
                                                                    UPPER(c.numeroentrega) AS NUMERO,
                                                                    UPPER(c.nombre) AS NOMBRE,UPPER(c.telefono) AS TELEFONO, (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS ZONA,
                                                                    c.created_at AS FECHAVENTA,c.total as TOTAL, c.pago as FORMAPAGO,
                                                                    (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as FECHAENTREGA,
                                                                    COALESCE((SELECT a.created_at FROM abonos a WHERE a.id_contrato = c.id ORDER BY a.created_at DESC LIMIT 1), '') as ULTIMOABONO
                                                                    FROM contratos c INNER JOIN registroestadocontrato r ON r.id_contrato = c.id
                                                                    " . $cadenaFechaIniYFechaFin . "
                                                                    " . $cadenaZona . "
                                                                    " . $cadenaFranquiciaSeleccionada . "
                                                                    AND c.id_franquicia IN (SELECT f.id FROM franquicias f INNER JOIN sucursalesconfirmaciones sc ON f.id = sc.id_franquicia
                                                                    WHERE sc.id_usuario = '$idUsuario')
                                                                    ORDER BY r.created_at DESC;");
                }else {
                    //Rol director
                    $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000'");
                    $contratosreportes = DB::select("SELECT r.created_at AS FECHAENVIO, UPPER(c.id) AS CONTRATO,
                                                                    (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS,
                                                                    UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.entrecallesentrega) AS ENTRECALLES, UPPER(c.coloniaentrega) AS COLONIA, UPPER(c.calleentrega) AS CALLE,
                                                                    UPPER(c.numeroentrega) AS NUMERO, UPPER(c.nombre) AS NOMBRE,UPPER(c.telefono) AS TELEFONO,
                                                                    (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS ZONA, c.created_at AS FECHAVENTA,c.total as TOTAL, c.pago as FORMAPAGO,
                                                                    (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as FECHAENTREGA,
                                                                    COALESCE((SELECT a.created_at FROM abonos a WHERE a.id_contrato = c.id ORDER BY a.created_at DESC LIMIT 1), '') as ULTIMOABONO
                                                                    FROM contratos c INNER JOIN registroestadocontrato r ON r.id_contrato = c.id
                                                                    " . $cadenaFechaIniYFechaFin . "
                                                                    " . $cadenaZona . "
                                                                    " . $cadenaFranquiciaSeleccionada . "
                                                                    ORDER BY r.created_at DESC;");
                }

                //Cadena sucursal para registro de movimiento
                if($franquiciaSeleccionada != null){
                    $idFranquiciaFiltro = $franquiciaSeleccionada;
                    $nombreSucursal = self::obtenerNombreFranquicia($idFranquiciaFiltro);
                    $cadenaFranquiciaFiltro = " sucursal: '" . $nombreSucursal . "'";
                } else {
                    $idFranquiciaFiltro = self::obtenerIdFranquiciaUsuario($idUsuario);
                }
            }else {
                //Rol administrador  o principal
                $idFranquicia = self::obtenerIdFranquiciaUsuario($idUsuario);
                $contratosreportes = DB::select("SELECT r.created_at AS FECHAENVIO, UPPER(c.id) AS CONTRATO,
                                                                    (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS,
                                                                    UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.entrecallesentrega) AS ENTRECALLES, UPPER(c.coloniaentrega) AS COLONIA, UPPER(c.calleentrega) AS CALLE,
                                                                    UPPER(c.numeroentrega) AS NUMERO, UPPER(c.nombre) AS NOMBRE,UPPER(c.telefono) AS TELEFONO,
                                                                    (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS ZONA, c.created_at AS FECHAVENTA,c.total as TOTAL, c.pago as FORMAPAGO,
                                                                    (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as FECHAENTREGA,
                                                                    COALESCE((SELECT a.created_at FROM abonos a WHERE a.id_contrato = c.id ORDER BY a.created_at DESC LIMIT 1), '') as ULTIMOABONO
                                                                    FROM contratos c INNER JOIN registroestadocontrato r ON r.id_contrato = c.id
                                                                    " . $cadenaFechaIniYFechaFin . "
                                                                    " . $cadenaZona . "
                                                                    AND c.id_franquicia = '$idFranquicia'
                                                                    ORDER BY r.created_at DESC;");

                $idFranquiciaFiltro = $idFranquicia;
                $nombreSucursal = self::obtenerNombreFranquicia($idFranquiciaFiltro);
                $cadenaFranquiciaFiltro = " sucursal: '" . $nombreSucursal . "'";
            }

            $mensajeHistorial = "Filtro reporte 'contratos enviados' por periodo de fecha: " . $fechainibuscar . " a " .$fechafinbuscar . $cadenaFranquiciaFiltro . $cadenaZonaFiltro;
            $mensajeHistorial = trim($mensajeHistorial,",");
            self::insertarHistorialSucursalReportes($idFranquiciaFiltro,$idUsuario,$mensajeHistorial);


            return view('administracion.reportes.tabla', [
                'contratosreportes' => $contratosreportes,
                'franquicias' => $franquicias,
                'franquiciaSeleccionada' => $franquiciaSeleccionada,
                'zonaSeleccionada' => $zonaSeleccionada,
                'fechainibuscar' => $fechainibuscar,
                'fechafinbuscar' => $fechafinbuscar,
                'idFranquicia' => $idFranquicia
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    private static function obtenerIdFranquiciaUsuario($idUsuario) {
        $usuarioFranquicia = DB::select("SELECT id_franquicia FROM usuariosfranquicia f where id_usuario = '$idUsuario'");
        if($usuarioFranquicia != null) {
            $idFranquicia = $usuarioFranquicia[0]->id_franquicia;
        }
        return $idFranquicia;
    }

    public function listacontratoscuentasactivas($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {
            //Rol administrador, director, principal

            $arrayCheckBox = array();

            $cbAprobados = 1;
            $cbManofactura = 1;
            $cbProcesoAprobacion = 1;
            $cbEnviados = 1;
            $cbEntregados = 1;
            $cbAtrasados = 1;
            $formaPagoSeleccionada = null;
            $franquiciaSeleccionada = '6E2AA';
            $zonaSeleccionada = '1';
            $cbContratosPeriodoActual = null;
            $cbUltimoAbono = null;

            array_push($arrayCheckBox, $cbAprobados);
            array_push($arrayCheckBox, $cbManofactura);
            array_push($arrayCheckBox, $cbProcesoAprobacion);
            array_push($arrayCheckBox, $cbEnviados);
            array_push($arrayCheckBox, $cbEntregados);
            array_push($arrayCheckBox, $cbAtrasados);
            array_push($arrayCheckBox, $formaPagoSeleccionada);
            array_push($arrayCheckBox, $franquiciaSeleccionada);
            array_push($arrayCheckBox, $zonaSeleccionada);
            array_push($arrayCheckBox, $cbContratosPeriodoActual);
            array_push($arrayCheckBox, $idFranquicia);
            array_push($arrayCheckBox, $cbUltimoAbono);

            $arrayContratos = self::obtenerListaContratosCheckBoxsOFormaPago($arrayCheckBox);

//            $contratosaprobados = $arrayContratos[0];
//            $contratosmanofactura = $arrayContratos[1];
//            $contratosprocesoaprobacion = $arrayContratos[2];
//            $contratosenviados = $arrayContratos[3];
//            $contratosentregados = $arrayContratos[4];
//            $contratosatrasados = $arrayContratos[5];
//            $formaPagoSeleccionada = $arrayContratos[6];
            $idFranquicia = $arrayContratos[7];
            $franquicias = $arrayContratos[8];
//            $numTotalContratos = count($contratosaprobados) + count($contratosmanofactura) + count($contratosprocesoaprobacion) + count($contratosenviados) +
//                                 count($contratosentregados) + count($contratosatrasados);

            return view('administracion.reportes.tablacuentasactivas', [
//                'contratosaprobados' => $contratosaprobados,
//                'contratosmanofactura' => $contratosmanofactura,
//                'contratosprocesoaprobacion' => $contratosprocesoaprobacion,
//                'contratosenviados' => $contratosenviados,
//                'contratosentregados' => $contratosentregados,
//                'contratosatrasados' => $contratosatrasados,
                'cbAprobados' => $cbAprobados,
                'cbManofactura' => $cbManofactura,
                'cbProcesoAprobacion' => $cbProcesoAprobacion,
                'cbEnviados' => $cbEnviados,
                'cbEntregados' => $cbEntregados,
                'cbAtrasados' => $cbAtrasados,
                'formaPagoSeleccionada' => $formaPagoSeleccionada,
//                'numTotalContratos' => $numTotalContratos,
                'franquicias' => $franquicias,
                'franquiciaSeleccionada' => $franquiciaSeleccionada,
                'zonaSeleccionada' => $zonaSeleccionada,
                'idFranquicia' => $idFranquicia
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function filtrarlistacontratoscuentasactivas($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {
            //Rol administrador, director, principal

            $arrayCheckBox = array();

            $cbAprobados = request('cbAprobados');
            $cbManofactura = request('cbManofactura');
            $cbProcesoAprobacion = request('cbProcesoAprobacion');
            $cbEnviados = request('cbEnviados');
            $cbEntregados = request('cbEntregados');
            $cbAtrasados = request('cbAtrasados');
            $formaPagoSeleccionada = request('formaPagoSeleccionada');
            $franquiciaSeleccionada = request('franquiciaSeleccionada');
            $zonaSeleccionada = request('zonaSeleccionada');
            $idFranquicia = request('idFranquiciaActual');

            array_push($arrayCheckBox, $cbAprobados);
            array_push($arrayCheckBox, $cbManofactura);
            array_push($arrayCheckBox, $cbProcesoAprobacion);
            array_push($arrayCheckBox, $cbEnviados);
            array_push($arrayCheckBox, $cbEntregados);
            array_push($arrayCheckBox, $cbAtrasados);
            array_push($arrayCheckBox, $formaPagoSeleccionada);
            array_push($arrayCheckBox, $franquiciaSeleccionada);
            array_push($arrayCheckBox, $zonaSeleccionada);

            $arrayContratos = self::obtenerListaContratosCheckBoxsOFormaPago($arrayCheckBox);

            $contratosaprobados = $arrayContratos[0];
            $contratosmanofactura = $arrayContratos[1];
            $contratosprocesoaprobacion = $arrayContratos[2];
            $contratosenviados = $arrayContratos[3];
            $contratosentregados = $arrayContratos[4];
            $contratosatrasados = $arrayContratos[5];
            $formaPagoSeleccionada = $arrayContratos[6];
            $idFranquicia = $arrayContratos[7];
            $franquicias = $arrayContratos[8];
            $contratosotros = $arrayContratos[11];
            $numTotalContratos = count($contratosaprobados) + count($contratosmanofactura) + count($contratosprocesoaprobacion) + count($contratosenviados) +
                                 count($contratosentregados) + count($contratosatrasados) + count($contratosotros);

            return view('administracion.reportes.tablacuentasactivas', [
                'contratosaprobados' => $contratosaprobados,
                'contratosmanofactura' => $contratosmanofactura,
                'contratosprocesoaprobacion' => $contratosprocesoaprobacion,
                'contratosenviados' => $contratosenviados,
                'contratosentregados' => $contratosentregados,
                'contratosatrasados' => $contratosatrasados,
                'contratosotros' => $contratosotros,
                'cbAprobados' => $cbAprobados,
                'cbManofactura' => $cbManofactura,
                'cbProcesoAprobacion' => $cbProcesoAprobacion,
                'cbEnviados' => $cbEnviados,
                'cbEntregados' => $cbEntregados,
                'cbAtrasados' => $cbAtrasados,
                'formaPagoSeleccionada' => $formaPagoSeleccionada,
                'numTotalContratos' => $numTotalContratos,
                'franquicias' => $franquicias,
                'franquiciaSeleccionada' => $franquiciaSeleccionada,
                'zonaSeleccionada' => $zonaSeleccionada,
                'idFranquicia' => $idFranquicia
            ]);


        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    private static function obtenerListaContratosCheckBoxsOFormaPago($arrayCheckBox)
    {

        $arrayContratos = array();
        $idUsuario = Auth::id();

        $contratosaprobados = array();
        $contratosmanofactura = array();
        $contratosprocesoaprobacion = array();
        $contratosenviados = array();
        $contratosentregados = array();
        $contratosatrasados = array();
        $contratosotros = array();

        $cbAprobados = $arrayCheckBox[0];
        $cbManofactura = $arrayCheckBox[1];
        $cbProcesoAprobacion = $arrayCheckBox[2];
        $cbEnviados = $arrayCheckBox[3];
        $cbEntregados = $arrayCheckBox[4];
        $cbAtrasados = $arrayCheckBox[5];
        $formaPagoSeleccionada = $arrayCheckBox[6];
        $franquiciaSeleccionada = null;
        $cbContratosPeriodoActual= $arrayCheckBox[9];
        $idFranquicia= $arrayCheckBox[10];
        $cbUltomoAbono= $arrayCheckBox[11];
        $now = Carbon::now();
        $idPrimerPoliza = "";

        if(((Auth::user()->rol_id) == 7)) {
            //Director
            $franquiciaSeleccionada = $arrayCheckBox[7];
        }
        $zonaSeleccionada = $arrayCheckBox[8];

        $cadenaFormaPago = " ";
        if($formaPagoSeleccionada != null) {
            $cadenaFormaPago = " AND c.pago = '$formaPagoSeleccionada'";
        }

        $cadenaFranquiciaSeleccionada = " ";
        if($franquiciaSeleccionada != null) {
            $cadenaFranquiciaSeleccionada = " AND c.id_franquicia = '$franquiciaSeleccionada'";
        }

        $cadenaZona = " ";
        if($zonaSeleccionada != null) {
            if($franquiciaSeleccionada != null) {
                $cadenaZona = " AND c.id_zona = (SELECT z.id FROM zonas z WHERE z.zona = '$zonaSeleccionada' AND z.id_franquicia = '$franquiciaSeleccionada')";
            }else {
                $cadenaZona = " AND c.id_zona = (SELECT z.id FROM zonas z WHERE z.zona = '$zonaSeleccionada' AND z.id_franquicia = c.id_franquicia)";
            }
        }

        $cadenaPeriodoActual = " ";
        $validacionEstadosQuery = " WHERE c.estatus_estadocontrato IN (7,9,10,11,12,2,4)";
        if($cbContratosPeriodoActual == 1){
            //Si se selecciono el checkBox de Periodo actual

            //Obtener idFranquicia para extraer primer poliza de la semana
            if(((Auth::user()->rol_id) == 7)) {
                //ROl de director
                $idFranquicia = $franquiciaSeleccionada;
            }else{
                $idFranquicia = self::obtenerIdFranquiciaUsuario($idUsuario);
            }

            //Obtener fechas periodo de fechas actual
            $hoyNumero = $now->dayOfWeekIso;    //Obtenemos el dia de la semana actual

            if($hoyNumero != 2) {
                //miercoles, jueves, viernes, sabado o lunes

                $fecha = Carbon::parse($now)->format('Y-m-d');

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
            }else{
                //Dia es igual a martes - Traer poliza martes
                $poliza = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' ORDER BY created_at DESC LIMIT 1");
                if($poliza != null){
                    $idPrimerPoliza = $poliza[0]->id;
                }
            }

        }

        $contratosTodos = null;
        $franquicias = null;

        $cadenaOrdenContratos = "";
        if($cbUltomoAbono != null){
            $cadenaOrdenContratos = "ORDER BY ULTIMOABONO ASC";
        } else {
            $cadenaOrdenContratos = "ORDER BY c.created_at DESC";
        }

        if(((Auth::user()->rol_id) == 7)) {
            //Director
            $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000'");

            //Obtener contratos con filtro o COntratos del periodo actual?
            if($cbContratosPeriodoActual == 1){
                //Obtener contratos de periodo actual para rol de director
                $query = "SELECT DISTINCT UPPER(c.id) AS CONTRATO, (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS,
                                                                    UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.entrecallesentrega) AS ENTRECALLES, UPPER(c.coloniaentrega) AS COLONIA, UPPER(c.calleentrega) AS CALLE,
                                                                    UPPER(c.numeroentrega) AS NUMERO, UPPER(c.nombre) AS NOMBRE,UPPER(c.telefono) AS TELEFONO,
                                                                    (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS ZONA, c.created_at AS FECHAVENTA,c.total as TOTAL, c.pago as FORMAPAGO,
                                                                    (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as FECHAENTREGAHISTORIAL,
                                                                    c.estatus_estadocontrato as ESTATUS_ESTADOCONTRATO, c.coordenadas as COORDENADAS,
                                                                    COALESCE((SELECT a.created_at FROM abonos a WHERE a.id_contrato = c.id ORDER BY a.created_at DESC LIMIT 1), '') as ULTIMOABONO,
                                                                    TIMESTAMPDIFF(DAY, c.fechaatraso ,'$now') AS DIASATRASO, DATE_FORMAT(c.fechacobroini, '%d') as PERIODOINI, DATE_FORMAT(c.fechacobrofin, '%d') as PERIODOFIN
                                                                    FROM polizacontratoscobranza pcc
                                                                    INNER JOIN contratos c ON c.id = pcc.id_contrato
                                                                    WHERE pcc.id_poliza = '$idPrimerPoliza'
                                                                    " . $cadenaFormaPago ."
                                                                    " . $cadenaFranquiciaSeleccionada . "
                                                                    " . $cadenaZona . "
                                                                    " . $cadenaOrdenContratos . " ";

            }else{
                //Obtener contratos basados en filtros seleccionados para rol de director
                $query = "SELECT DISTINCT UPPER(c.id) AS CONTRATO, (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS,
                                                                    UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.entrecallesentrega) AS ENTRECALLES, UPPER(c.coloniaentrega) AS COLONIA, UPPER(c.calleentrega) AS CALLE,
                                                                    UPPER(c.numeroentrega) AS NUMERO, UPPER(c.nombre) AS NOMBRE,UPPER(c.telefono) AS TELEFONO,
                                                                    (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS ZONA, c.created_at AS FECHAVENTA,c.total as TOTAL, c.pago as FORMAPAGO,
                                                                    (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as FECHAENTREGAHISTORIAL,
                                                                    c.estatus_estadocontrato as ESTATUS_ESTADOCONTRATO, c.coordenadas as COORDENADAS,
                                                                    COALESCE((SELECT a.created_at FROM abonos a WHERE a.id_contrato = c.id ORDER BY a.created_at DESC LIMIT 1), '') as ULTIMOABONO,
                                                                    TIMESTAMPDIFF(DAY, c.fechaatraso ,'$now') AS DIASATRASO, DATE_FORMAT(c.fechacobroini, '%d') as PERIODOINI, DATE_FORMAT(c.fechacobrofin, '%d') as PERIODOFIN
                                                                    FROM contratos c
                                                                    " . $validacionEstadosQuery . "
                                                                    " . $cadenaFormaPago ."
                                                                    " . $cadenaFranquiciaSeleccionada . "
                                                                    " . $cadenaZona . "
                                                                    " . $cadenaOrdenContratos . " ";
            }

            $contratosTodos = DB::select($query);

        }else {
            //Principal o administrador
            $idFranquicia = self::obtenerIdFranquiciaUsuario($idUsuario);
            //Obtener contratos con filtro o Contratos del periodo actual?
            if($cbContratosPeriodoActual == 1){
                //Obtener contratos de periodo actual para rol de director
                $query = "SELECT DISTINCT UPPER(c.id) AS CONTRATO, (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS,
                                                                    UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.entrecallesentrega) AS ENTRECALLES, UPPER(c.coloniaentrega) AS COLONIA, UPPER(c.calleentrega) AS CALLE,
                                                                    UPPER(c.numeroentrega) AS NUMERO, UPPER(c.nombre) AS NOMBRE,UPPER(c.telefono) AS TELEFONO,
                                                                    (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS ZONA, c.created_at AS FECHAVENTA,c.total as TOTAL, c.pago as FORMAPAGO,
                                                                    (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as FECHAENTREGAHISTORIAL,
                                                                    c.estatus_estadocontrato as ESTATUS_ESTADOCONTRATO, c.coordenadas as COORDENADAS,
                                                                    COALESCE((SELECT a.created_at FROM abonos a WHERE a.id_contrato = c.id ORDER BY a.created_at DESC LIMIT 1), '') as ULTIMOABONO,
                                                                    TIMESTAMPDIFF(DAY, c.fechaatraso ,'$now') AS DIASATRASO, DATE_FORMAT(c.fechacobroini, '%d') as PERIODOINI, DATE_FORMAT(c.fechacobrofin, '%d') as PERIODOFIN
                                                                    FROM polizacontratoscobranza pcc
                                                                    INNER JOIN contratos c ON c.id = pcc.id_contrato
                                                                    WHERE pcc.id_poliza = '$idPrimerPoliza'
                                                                    AND c.id_franquicia = '$idFranquicia'
                                                                    " . $cadenaFormaPago . "
                                                                    " . $cadenaZona . "
                                                                    " . $cadenaOrdenContratos . " ";

            }else{
                //Obtener contratos basados en filtros seleccionados para rol de director
                $query = "SELECT DISTINCT UPPER(c.id) AS CONTRATO, (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS,
                                                                    UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.entrecallesentrega) AS ENTRECALLES, UPPER(c.coloniaentrega) AS COLONIA, UPPER(c.calleentrega) AS CALLE,
                                                                    UPPER(c.numeroentrega) AS NUMERO, UPPER(c.nombre) AS NOMBRE,UPPER(c.telefono) AS TELEFONO,
                                                                    (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS ZONA, c.created_at AS FECHAVENTA,c.total as TOTAL, c.pago as FORMAPAGO,
                                                                    (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as FECHAENTREGAHISTORIAL,
                                                                    c.estatus_estadocontrato as ESTATUS_ESTADOCONTRATO, c.coordenadas as COORDENADAS,
                                                                    COALESCE((SELECT a.created_at FROM abonos a WHERE a.id_contrato = c.id ORDER BY a.created_at DESC LIMIT 1), '') as ULTIMOABONO,
                                                                    TIMESTAMPDIFF(DAY, c.fechaatraso ,'$now') AS DIASATRASO, DATE_FORMAT(c.fechacobroini, '%d') as PERIODOINI, DATE_FORMAT(c.fechacobrofin, '%d') as PERIODOFIN
                                                                    FROM contratos c
                                                                    " . $validacionEstadosQuery . "
                                                                    AND c.id_franquicia = '$idFranquicia'
                                                                    " . $cadenaFormaPago . "
                                                                    " . $cadenaZona . "
                                                                    " . $cadenaOrdenContratos . " ";
            }
            $contratosTodos = DB::select($query);
        }

        $cuentasLocalidad = array();
        $cuentasColonia = array();

        //Recorremos el arreglo de todos los contratos para asignarlo a su estatus correspondinete
        foreach ($contratosTodos as $contrato) {

            $agregarCuentaLocCol = false;

            //Contratos aprobados
            if ($cbAprobados != null) {
                if ($contrato->ESTATUS_ESTADOCONTRATO == 7) {
                    array_push($contratosaprobados, $contrato);
                    $agregarCuentaLocCol = true;
                }
            }

            //Contratos manofactura y en proceso de envio
            if ($cbManofactura != null) {
                if (($contrato->ESTATUS_ESTADOCONTRATO == 10) || ($contrato->ESTATUS_ESTADOCONTRATO == 11)) {
                    array_push($contratosmanofactura, $contrato);
                    $agregarCuentaLocCol = true;
                }
            }

            //Contratos en proceso de aprobacion
            if ($cbProcesoAprobacion != null) {
                if ($contrato->ESTATUS_ESTADOCONTRATO == 9) {
                    array_push($contratosprocesoaprobacion, $contrato);
                    $agregarCuentaLocCol = true;
                }
            }

            //Contratos enviados
            if ($cbEnviados != null) {
                if ($contrato->ESTATUS_ESTADOCONTRATO == 12) {
                    array_push($contratosenviados, $contrato);
                    $agregarCuentaLocCol = true;
                }
            }

            //Contratos entregados
            if ($cbEntregados != null) {
                if ($contrato->ESTATUS_ESTADOCONTRATO == 2) {
                    array_push($contratosentregados, $contrato);
                    $agregarCuentaLocCol = true;
                }
            }

            //Contratos atrasados
            if ($cbAtrasados != null) {
                if ($contrato->ESTATUS_ESTADOCONTRATO == 4) {
                    array_push($contratosatrasados, $contrato);
                    $agregarCuentaLocCol = true;
                }
            }

            //Contratos otros (Terminados, pagados, lio/fuga, cancelacion)
            if($contrato->ESTATUS_ESTADOCONTRATO == 1 || $contrato->ESTATUS_ESTADOCONTRATO == 6 || $contrato->ESTATUS_ESTADOCONTRATO == 14 || $contrato->ESTATUS_ESTADOCONTRATO == 15 || $contrato->ESTATUS_ESTADOCONTRATO == 5){
                array_push($contratosotros, $contrato);
                $agregarCuentaLocCol = true;
            }


            if ($agregarCuentaLocCol) {
                //$agregarCuentaLocCol = true

                if (((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {
                    //Director, principal o administrador

                    //Obtener localidades
                    if (array_key_exists($contrato->LOCALIDAD, $cuentasLocalidad)) {
                        //Existe la llave de la localidad
                        $cuentasLocalidad[$contrato->LOCALIDAD] = $cuentasLocalidad[$contrato->LOCALIDAD] + 1;
                    } else {
                        //No se encontro la llave de la localidad
                        $cuentasLocalidad[$contrato->LOCALIDAD] = 1;
                    }
/*
                    //Obtener colonias
                    if (array_key_exists($contrato->LOCALIDAD . "-" . $contrato->COLONIA, $cuentasColonia)) {
                        //Existe la llave de la colonia
                        $cuentasColonia[$contrato->LOCALIDAD . "-" . $contrato->COLONIA] = $cuentasColonia[$contrato->LOCALIDAD . "-" . $contrato->COLONIA] + 1;
                    } else {
                        //No se encontro la llave de la colonia
                        $cuentasColonia[$contrato->LOCALIDAD . "-" . $contrato->COLONIA] = 1;
                    }
*/
                }

            }

        }

        //Validacion de checkbox filtros seleccionado
        $cadenaFiltrosSeleccionado = "";

        if($cbAprobados != null){
            $cadenaFiltrosSeleccionado = "7,";
        }if($cbManofactura != null){
            $cadenaFiltrosSeleccionado = $cadenaFiltrosSeleccionado . "10,11,";
        }if($cbProcesoAprobacion != null){
            $cadenaFiltrosSeleccionado = $cadenaFiltrosSeleccionado . "9,";
        }if($cbEnviados != null){
            $cadenaFiltrosSeleccionado = $cadenaFiltrosSeleccionado . "12,";
        }if($cbEntregados != null){
            $cadenaFiltrosSeleccionado = $cadenaFiltrosSeleccionado . "2,";
        }if($cbAtrasados != null){
            $cadenaFiltrosSeleccionado = $cadenaFiltrosSeleccionado . "4";
        }

        $cadenaFiltrosSeleccionado = trim($cadenaFiltrosSeleccionado, ",");

        //Obtener cuentas por colonia
        if(strlen($cadenaFiltrosSeleccionado) > 0){
            //Se selecciono al menos un cb de los filtros
            if((Auth::user()->rol_id) == 7){
                //Rol de director
                $query = "SELECT UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.coloniaentrega) AS COLONIA, COUNT(c.coloniaentrega) AS CUENTAS
                                                                    FROM contratos c
                                                                    WHERE c.estatus_estadocontrato IN ($cadenaFiltrosSeleccionado)
                                                                    " . $cadenaPeriodoActual . "
                                                                    " . $cadenaFormaPago ."
                                                                    " . $cadenaFranquiciaSeleccionada . "
                                                                    " . $cadenaZona . "
                                                                    GROUP BY  c.coloniaentrega, c.localidadentrega
                                                                    ORDER BY c.localidadentrega ASC";
                $cuentasColonia = DB::select($query);

            }else{
                //Administracion o principal
                $query = "SELECT UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.coloniaentrega) AS COLONIA, COUNT(c.coloniaentrega) AS CUENTAS
                                                                    FROM contratos c
                                                                    WHERE c.estatus_estadocontrato IN ($cadenaFiltrosSeleccionado)
                                                                    " . $cadenaPeriodoActual . "
                                                                    AND c.id_franquicia = '$idFranquicia'
                                                                    " . $cadenaFormaPago . "
                                                                    " . $cadenaZona . "
                                                                    GROUP BY  c.coloniaentrega, c.localidadentrega
                                                                    ORDER BY c.localidadentrega ASC";
                $cuentasColonia = DB::select($query);
            }
        }

        array_push($arrayContratos, $contratosaprobados);
        array_push($arrayContratos, $contratosmanofactura);
        array_push($arrayContratos, $contratosprocesoaprobacion);
        array_push($arrayContratos, $contratosenviados);
        array_push($arrayContratos, $contratosentregados);
        array_push($arrayContratos, $contratosatrasados);
        array_push($arrayContratos, $formaPagoSeleccionada);
        array_push($arrayContratos, $idFranquicia);
        array_push($arrayContratos, $franquicias);
        array_push($arrayContratos, $cuentasLocalidad);
        array_push($arrayContratos, $cuentasColonia);
        array_push($arrayContratos, $contratosotros);

        return $arrayContratos;

    }

    public function listacontratospaquetes($idFranquicia){

        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {
            //Rol administrador, director, principal

//            $sucursal = "";
            if((Auth::user()->rol_id) == 7){ //Es director
                $usuariosVentas = DB::select("SELECT u.id,u.name as nombre, (SELECT r.rol FROM roles r WHERE r.id = u.rol_id) as rol FROM users u
                                                    INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                    WHERE rol_id = '12' OR rol_id = '13' ORDER BY rol_id ASC, name ASC");
            }else{
                //No es el director
                $idUsuario = Auth::user()->id;
                $sucursal = DB::select("SELECT id_franquicia FROM usuariosfranquicia WHERE id_usuario = '$idUsuario'");
                $sucursal = $sucursal[0]->id_franquicia;
                $consulta = "SELECT u.id,u.name as nombre, (SELECT r.rol FROM roles r WHERE r.id = u.rol_id) as rol FROM users u
                             INNER JOIN usuariosfranquicia uf ON u.id = uf.id_usuario
                             WHERE (u.rol_id = '12' OR rol_id = '13') AND uf.id_franquicia = '$sucursal' ORDER BY rol_id ASC, name ASC";
                $usuariosVentas = DB::select($consulta);
//                $sucursal = " AND uf.id_franquicia = '$sucursal'";
            }

            $idUsuarioSeleccionado = request('idUsuario');
//            $idOpto = "";
//            if($idOptoSeleccionado != null){
//                $idOpto = " WHERE c.id_optometrista = '$idOptoSeleccionado'";
//            }

//            $fechaIniSeleccionada = request('fechaIni');
//            $fechaFinSeleccionada = request('fechaFin');
//
//            if($fechaIniSeleccionada != null && $fechaFinSeleccionada != null ){
//                if(strlen($idOpto)>0){ //Seleccionaron algun Opto?
//                    //Si lo seleccionaron
//                    $fecha = " AND DATE(c.created_at) BETWEEN '$fechaIniSeleccionada' AND '$fechaFinSeleccionada' ";
//                }else{
//                    //No seleccionaron un opto
//                    $fecha = " WHERE DATE(c.created_at) BETWEEN '$fechaIniSeleccionada' AND '$fechaFinSeleccionada' ";
//                }
//            }else{
//                $hoy = Carbon::now()->format('Y-m-d');
//                if(strlen($idOpto)>0){ //Seleccionaron algun Opto?
//                    //Si lo seleccionaron
//                    $fecha = " AND DATE(c.created_at) BETWEEN '$hoy' AND '$hoy' ";
//                }else{
//                    //No seleccionaron un opto
//                    $fecha = " WHERE DATE(c.created_at) BETWEEN '$hoy' AND '$hoy' ";
//                }
//            }
//
//            $consulta = "SELECT (SELECT u.name FROM users u WHERE u.id = c.id_optometrista) as NOMBRE,
//                                            c.id as CONTRATO,
//                                            c.created_at as FECHACREACION,
//                                            (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS,
//                                            (SELECT p.nombre FROM historialclinico hc INNER JOIN paquetes p ON p.id = hc.id_paquete WHERE hc.id_contrato = c.id
//                                            ORDER BY hc.created_at DESC LIMIT 1) as PAQUETE,
//                                            (SELECT hc.fotocromatico FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY c.created_at DESC LIMIT 1) as foto,
//                                            (SELECT hc.ar FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY c.created_at DESC LIMIT 1) as ar,
//                                            (SELECT hc.tinte FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY c.created_at DESC LIMIT 1) as tinte,
//                                            (SELECT hc.blueray FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY c.created_at DESC LIMIT 1) as blueray,
//                                            (SELECT hc.otroT FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY c.created_at DESC LIMIT 1) as otroT,
//                                            (SELECT hc.tratamientootro FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY c.created_at DESC LIMIT 1) as tratamientootro,
//                                            c.totalreal as TOTAL
//                                            FROM contratos c
//                                            INNER JOIN usuariosfranquicia uf
//                                            ON uf.id_usuario = c.id_optometrista
//                                            ".$idOpto.$fecha.$sucursal."
//                                             ORDER BY c.created_at DESC";
//
//            $contratos = DB::select($consulta);

            return view('administracion.reportes.paquetes', [
//                'contratos' => $contratos,
                'idUsuario' => $idUsuarioSeleccionado,
                'usuariosVentas' => $usuariosVentas,
                'idFranquicia' => $idFranquicia
//                'fechaIni' => $fechaIniSeleccionada,
//                'fechaFin' => $fechaFinSeleccionada
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function validarCuentasLocalPagina(){
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {
            //Rol administrador, director, principal

            if((Auth::user()->rol_id) == 7){ //Es un director?
                //Si, es un director
                $zonas = DB::select("SELECT z.id,z.zona,(SELECT f.ciudad FROM franquicias f WHERE f.id = z.id_franquicia) as ciudad FROM zonas z ORDER BY z.id");
            }else{
                //Es un administrador o principal.
                $idUsuario = Auth::user()->id;
                $sucursal = DB::select("SELECT id_franquicia FROM usuariosfranquicia WHERE id_usuario = '$idUsuario'");
                $sucursal = $sucursal[0]->id_franquicia;
                $zonas = DB::select("SELECT z.id,z.zona FROM zonas z WHERE z.id_franquicia = '$sucursal' ORDER BY z.zona");
            }

            return view('administracion.reportes.cuentasfisicas', [
                "zonas" => $zonas,
                "contratosPagina" => null,
                "contratosArchivo" => null,
                "contratosArchivoNoEncontrato" => null

            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function validarCuentasLocalPaginaArchivo(Request $request){

        $archivoExcel =$request->file('archivo');
        if($archivoExcel != null){
            if(request()->hasFile('archivo')){

                $idZona = request('zona');
                if($idZona != null){// Tenemos una zona?
                    //Si, tenemos una zona
                    if((Auth::user()->rol_id) == 7){ //El usuario, es un director?
                        //Si, es un director
                        $existeZona = DB::select("SELECT id FROM zonas WHERE id = '$idZona'");
                        $zonas = DB::select("SELECT z.id,z.zona,(SELECT f.ciudad FROM franquicias f WHERE f.id = z.id_franquicia) as ciudad FROM zonas z ORDER BY z.id");
                    }else{
                        //Es un administrador o principal.
                        $idUsuario = Auth::user()->id;
                        $existeZona = DB::select("SELECT z.id FROM zonas z INNER JOIN usuariosfranquicia uf ON uf.id_franquicia = z.id_franquicia WHERE z.id = '$idZona'
                                                        AND uf.id_usuario = '$idUsuario'");
                        $sucursal = DB::select("SELECT id_franquicia FROM usuariosfranquicia WHERE id_usuario = '$idUsuario'");
                        $sucursal = $sucursal[0]->id_franquicia;
                        $zonas = DB::select("SELECT z.id,z.zona FROM zonas z WHERE z.id_franquicia = '$sucursal' ORDER BY z.zona");
                    }
                }else{
                    //No, no tenemos una zona
                    return back()->with("alerta","Por favor, selecciona una zona valida.");
                }


                if($existeZona != null){ //Existe la zona seleccionada?
                    //Si existe la zona.
                    $extension = $archivoExcel->getClientOriginalExtension();
                    if ($extension == "xlsx" || $extension == "xls") { //Es un archivo de excel?
                        try {

                            $filas = Excel::toArray(new CsvImport(), $archivoExcel);

                            $listaContratos = array();
                            $i = 0;
                            foreach ($filas[0] as $key => $contrato) {
                                $listaContratos[] =  $contrato[0];
                            }

                            $contratosExistentesPagina = DB::select("SELECT id FROM contratos WHERE id_zona = '$idZona' AND (estatus_estadocontrato = 2 OR estatus_estadocontrato = 4
                                                                            OR estatus_estadocontrato = 12) ");
                            $listaContratosPagina = array();
                            foreach ($contratosExistentesPagina as $contratoExistente){
                                $listaContratosPagina[] = $contratoExistente -> id;
                            }

                            $diferenciaContratosPagina = array_diff($listaContratosPagina,$listaContratos); //Contratos que existen en la pagina/zona pero en el archivo no.
                            $diferenciaContratosArchivo = array_diff($listaContratos,$listaContratosPagina); //Contratos que existen en el archivo pero en la pagina y zona no.

                            $contratosPagina = array();
                            foreach ($diferenciaContratosPagina as $difContratoPagina){
                                \Log::info($difContratoPagina);
                                $diferenciaContratosPaginaBD = DB::select("SELECT (SELECT ec.descripcion FROM estadocontrato ec WHERE c.estatus_estadocontrato = ec.estatus) as ESTATUS,
                                                                (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) as ZONA,
                                                                (SELECT f.ciudad FROM franquicias f WHERE f.id = c.id_franquicia) as SUCURSAL
                                                                                    FROM contratos c WHERE c.id = '$difContratoPagina'");
                                $contratosPagina["id"][] = $difContratoPagina;
                                $contratosPagina["estatus"][] =  $diferenciaContratosPaginaBD[0]->ESTATUS;
                                $contratosPagina["zona"][] = $diferenciaContratosPaginaBD[0]->ZONA;
                                $contratosPagina["sucursal"][] =  $diferenciaContratosPaginaBD[0]->SUCURSAL;
                            }

                            $contratosArchivo = array();

                            foreach ($diferenciaContratosArchivo as $difContratoArchivo){
                                \Log::info($difContratoArchivo);
                                if($difContratoArchivo != null){
                                    $diferenciaContratosPaginaBD = DB::select("SELECT (SELECT ec.descripcion FROM estadocontrato ec WHERE c.estatus_estadocontrato = ec.estatus) as ESTATUS,
                                                                    (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) as ZONA,
                                                                    (SELECT f.ciudad FROM franquicias f WHERE f.id = c.id_franquicia) as SUCURSAL
                                                                                    FROM contratos c WHERE c.id = '$difContratoArchivo'");

                                    $contratosArchivo["id"][] = $difContratoArchivo;
                                    if($diferenciaContratosPaginaBD != null){
                                        $contratosArchivo["estatus"][] =  $diferenciaContratosPaginaBD[0]->ESTATUS;
                                        $contratosArchivo["zona"][] = $diferenciaContratosPaginaBD[0]->ZONA;
                                        $contratosArchivo["sucursal"][] = $diferenciaContratosPaginaBD[0]->SUCURSAL;
                                    }else{
                                        $contratosArchivo["estatus"][] = "SIN ESTATUS";
                                        $contratosArchivo["zona"][] = "SIN ZONA";
                                        $contratosArchivo["sucursal"][] ="SIN SUCURSAL";
                                    }
                                }
                            }

                            return view('administracion.reportes.cuentasfisicas', [
                                "zonas" => $zonas,
                                "contratosPagina" => $contratosPagina,
                                "contratosArchivo" => $contratosArchivo

                            ]);

                        } catch (\Exception $e) {
                            \Log::info("ERROR: ".$e);
                            return back()->with("error","Tuvimos un problema, por favor contacta al administrador de la pagina.");
                        }

                    }
                    return back()->with("alerta","Por favor, selecciona un archivo valido.");
                }
                //No existe la zona o no es valida.

                return back()->with("alerta","La zona no es valida.");
            }
        }
        return back()->with("alerta","Por favor, selecciona un archivo.");
    }

    public function paquetestiemporeal(Request $request){

        $idUsuarioSeleccionado = $request->input('idUsuario');
        $fechaIniSeleccionada = $request->input('fechaIni');
        $filtro = $request->input('filtro');
        $ordenarPaquetes = $request->input('ordenarPaquetes');

        $idUsuario = Auth::user()->id;
        $sucursal = DB::select("SELECT id_franquicia FROM usuariosfranquicia WHERE id_usuario = '$idUsuario'");
        $sucursal = $sucursal[0]->id_franquicia;

        $hoy = Carbon::now()->format('Y-m-d');

        if(strlen($fechaIniSeleccionada) == 0){
            //Si fecha inicial es vacia -> colocar fecha del dia de hoy
            $fechaIniSeleccionada = $hoy;

        }

        $fecha = Carbon::parse($fechaIniSeleccionada);
        $lunes = $fecha->copy()->startOfWeek();
        $sabado = $fecha->copy()->endOfWeek()->subDay();

        //Validar si es por poliza
        if($filtro == 'false'){
            //Filtrar por poliza
            $lunes = Carbon::parse($lunes)->addDays(1);     //Fecha lunes tendra fecha martes - Inicio de semana
            $sabado = Carbon::parse($sabado)->addDays(2);   //Fecha sabado tendra fecha sig. lunes - poliza con valores del fin de semana
        }

        $existeUsuario = DB::select("select u.name, u.rol_id from users u inner join usuariosfranquicia uf on uf.id_usuario = u.id where u.id = '$idUsuarioSeleccionado'");
        $validaUsuario = ' ';
        $mensajeHistorial = "Filtro reporte 'paquetes' por periodo de fecha: " . $lunes . " a " .$sabado;
        $usuario = "";
        if($existeUsuario != null){
            if ($existeUsuario[0]->rol_id == 12){
                //Es optometrista
                $validaUsuario = "AND c.id_optometrista = '$idUsuarioSeleccionado'";
                $mensajeHistorial = "Filtro reporte 'paquetes' por periodo de fecha: " . $lunes . " a " .$sabado . ", optometrista: '" .$existeUsuario[0]->name ."'";
                $opcion = " INNER JOIN poliza p ON p.id = c.polizaoptometrista
                            WHERE DATE(p.created_at) BETWEEN '$lunes' AND '$sabado' ";
                $usuario = " (SELECT u.name FROM users u WHERE u.id = c.id_optometrista) as NOMBRE, ";
            }else if ($existeUsuario[0]->rol_id == 13){
                //Es asistente
                $validaUsuario = "AND c.id_usuariocreacion = '$idUsuarioSeleccionado'";
                $mensajeHistorial = "Filtro reporte 'paquetes' por periodo de fecha: " . $lunes . " a " .$sabado . ", asistente: '" .$existeUsuario[0]->name ."'";
                $opcion = " INNER JOIN poliza p ON p.id = c.poliza
                            WHERE DATE(p.created_at) BETWEEN '$lunes' AND '$sabado' ";
                $usuario = " (SELECT u.name FROM users u WHERE u.id = c.id_usuariocreacion) as NOMBRE, ";
            }
        }



        $ordenarPor = " ORDER BY sucursal,c.fecharegistro DESC " ;
        if($ordenarPaquetes == 'true') {
            $ordenarPor = " ORDER BY sucursal,ID_PAQUETE ASC ";
        }

        if($filtro == 'true'){
            $opcion = " WHERE DATE(c.fecharegistro) BETWEEN '$lunes' AND '$sabado' ";
        }

        $consulta = "SELECT      c.poliza as POLIZA,"
                                . $usuario .
                                "(SELECT f.ciudad FROM franquicias f WHERE f.id = c.id_franquicia) as SUCURSAL,
                                c.id as CONTRATO,
                                c.fecharegistro as FECHACREACION,
                                (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS,
                                (SELECT p.nombre FROM historialclinico hc INNER JOIN paquetes p ON p.id = hc.id_paquete WHERE hc.id_contrato = c.id ORDER BY c.fecharegistro ASC LIMIT 1) as PAQUETE,
                                (SELECT p.id FROM historialclinico hc INNER JOIN paquetes p ON p.id = hc.id_paquete WHERE hc.id_contrato = c.id ORDER BY c.fecharegistro ASC LIMIT 1) as ID_PAQUETE,
                                (SELECT hc.fotocromatico FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY c.fecharegistro DESC LIMIT 1) as foto,
                                (SELECT hc.ar FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY c.fecharegistro DESC LIMIT 1) as ar,
                                (SELECT hc.tinte FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY c.fecharegistro DESC LIMIT 1) as tinte,
                                (SELECT hc.blueray FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY c.fecharegistro DESC LIMIT 1) as blueray,
                                (SELECT hc.otroT FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY c.fecharegistro DESC LIMIT 1) as otroT,
                                (SELECT hc.tratamientootro FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY c.fecharegistro DESC LIMIT 1) as tratamientootro,
                                c.totalreal as TOTAL,
                                c.estatus_estadocontrato as ESTADOCONTRATO
                                FROM contratos c
                                INNER JOIN usuariosfranquicia uf
                                ON uf.id_usuario = c.id_optometrista
                                ".$opcion.
                                 $validaUsuario.
                                 $ordenarPor;

        \Log::info($consulta);
        //Registrar movimiento historial sucursal
        $idFranquicia = $sucursal;
        self::insertarHistorialSucursalReportes($idFranquicia,$idUsuario,$mensajeHistorial);

        $contratos = DB::select($consulta);
        $response = ['data' => $contratos, 'fechaIniSeleccionada'=>$fechaIniSeleccionada];

        return response()->json($response);
    }

    public function cuentasactivastiemporeal(Request $request)
    {

        $arrayCheckBox = array();

        $cbAprobados = $request->input('cbAprobados');
        $cbManofactura = $request->input('cbManofactura');
        $cbProcesoAprobacion = $request->input('cbProcesoAprobacion');
        $cbEnviados = $request->input('cbEnviados');
        $cbEntregados = $request->input('cbEntregados');
        $cbAtrasados = $request->input('cbAtrasados');
        $formaPagoSeleccionada = $request->input('formaPagoSeleccionada');
        $franquiciaSeleccionada = $request->input('franquiciaSeleccionada');
        $zonaSeleccionada = $request->input('zonaSeleccionada');
        $cbContratosPeriodoActual = $request->input('cbContratosPeriodoActual');
        $idFranquicia = $request->input('idFranquiciaActual');
        $cbUltimoAbono = $request->input('cbUltimoAbono');

        array_push($arrayCheckBox, $cbAprobados);
        array_push($arrayCheckBox, $cbManofactura);
        array_push($arrayCheckBox, $cbProcesoAprobacion);
        array_push($arrayCheckBox, $cbEnviados);
        array_push($arrayCheckBox, $cbEntregados);
        array_push($arrayCheckBox, $cbAtrasados);
        array_push($arrayCheckBox, $formaPagoSeleccionada);
        array_push($arrayCheckBox, $franquiciaSeleccionada);
        array_push($arrayCheckBox, $zonaSeleccionada);
        array_push($arrayCheckBox, $cbContratosPeriodoActual);
        array_push($arrayCheckBox, $idFranquicia);
        array_push($arrayCheckBox, $cbUltimoAbono);

        $arrayContratos = self::obtenerListaContratosCheckBoxsOFormaPago($arrayCheckBox);

        $contratosaprobados = $arrayContratos[0];
        $contratosmanofactura = $arrayContratos[1];
        $contratosprocesoaprobacion = $arrayContratos[2];
        $contratosenviados = $arrayContratos[3];
        $contratosentregados = $arrayContratos[4];
        $contratosatrasados = $arrayContratos[5];
        $formaPagoSeleccionada = $arrayContratos[6];
        $idFranquicia = $arrayContratos[7];
        $franquicias = $arrayContratos[8];
        $cuentasLocalidad = $arrayContratos[9];
        $cuentasColonia = $arrayContratos[10];
        $contratosotros = $arrayContratos[11];
        $numTotalContratos = count($contratosaprobados) + count($contratosmanofactura) + count($contratosprocesoaprobacion) + count($contratosenviados) +
            count($contratosentregados) + count($contratosatrasados) + count($contratosotros);

        //Registro de filtro en historial sucursal
        $idUsuario = Auth::user()->id;
        $idFranquiciaFiltro = "";
        $mensajeHistorial = "Filtro reporte 'cuentas activas' por ";
        if($cbAprobados != null){ $mensajeHistorial = $mensajeHistorial . "aprobados, "; }
        if($cbManofactura != null){ $mensajeHistorial = $mensajeHistorial . "manufactura, "; }
        if($cbProcesoAprobacion != null){ $mensajeHistorial = $mensajeHistorial . "proceso de aprobacion, "; }
        if($cbEnviados != null){ $mensajeHistorial = $mensajeHistorial . "enviados, "; }
        if($cbEntregados != null){ $mensajeHistorial = $mensajeHistorial . "entregados, "; }
        if($cbAtrasados != null){ $mensajeHistorial = $mensajeHistorial . "atrasados, "; }
        if($formaPagoSeleccionada != null){
            switch ($formaPagoSeleccionada){
                case 0:
                    $mensajeHistorial = $mensajeHistorial . "forma de pago: 'contado', ";
                    break;
                case 1:
                    $mensajeHistorial = $mensajeHistorial . "forma de pago: 'semanal', ";
                    break;
                case 2:
                    $mensajeHistorial = $mensajeHistorial . "forma de pago: 'quincenal', ";
                    break;
                case 4:
                    $mensajeHistorial = $mensajeHistorial . "forma de pago: 'mensual', ";
                    break;
            }
        }

        if(Auth::user()->rol_id == 7){
            //Rol director
            if($franquiciaSeleccionada != null){
                $idFranquiciaFiltro = $franquiciaSeleccionada;
                $nombreFranquicia = self::obtenerNombreFranquicia($franquiciaSeleccionada);
            } else {
                $idFranquiciaFiltro = self::obtenerIdFranquiciaUsuario($idUsuario);
                $nombreFranquicia = self::obtenerNombreFranquicia($idFranquiciaFiltro);
            }
            $mensajeHistorial = $mensajeHistorial . "sucursal: '" . $nombreFranquicia ."', ";
        }else{
            //Rol principal o administracion
            $idFranquiciaFiltro = self::obtenerIdFranquiciaUsuario($idUsuario);
            $nombreFranquicia = self::obtenerNombreFranquicia($idFranquiciaFiltro);
            $mensajeHistorial = $mensajeHistorial . "sucursal: '" . $nombreFranquicia ."', ";
        }

        if($zonaSeleccionada != null){
            $mensajeHistorial = $mensajeHistorial . "zona: '" . $zonaSeleccionada ."', ";
        }
        if($cbUltimoAbono != null){
            $mensajeHistorial = $mensajeHistorial . "ultimo abono";
        }

        $mensajeHistorial = trim($mensajeHistorial,",");
        //Registrar movimiento en historial sucursal
        self::insertarHistorialSucursalReportes($idFranquiciaFiltro,$idUsuario,$mensajeHistorial);

        $view = view('administracion.reportes.listas.listacuentasactivas', [
            'contratosaprobados' => $contratosaprobados,
            'contratosmanofactura' => $contratosmanofactura,
            'contratosprocesoaprobacion' => $contratosprocesoaprobacion,
            'contratosenviados' => $contratosenviados,
            'contratosentregados' => $contratosentregados,
            'contratosatrasados' => $contratosatrasados,
            'contratosotros' => $contratosotros,
            'cbAprobados' => $cbAprobados,
            'cbManofactura' => $cbManofactura,
            'cbProcesoAprobacion' => $cbProcesoAprobacion,
            'cbEnviados' => $cbEnviados,
            'cbEntregados' => $cbEntregados,
            'cbAtrasados' => $cbAtrasados,
            'formaPagoSeleccionada' => $formaPagoSeleccionada,
            'numTotalContratos' => $numTotalContratos,
            'franquicias' => $franquicias,
            'franquiciaSeleccionada' => $franquiciaSeleccionada,
            'zonaSeleccionada' => $zonaSeleccionada,
            'idFranquicia' => $idFranquicia,
            'cuentasLocalidad' => $cuentasLocalidad,
            'cuentasColonia' => $cuentasColonia
        ])->render();

        return \Response::json(array("valid"=>"true","view"=>$view));
    }

    public function listacontratoscancelados($idFranquicia){
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {
            //Rol administrador, director, principal

            $idUsuario = Auth::id();
            $franquiciaSeleccionada = '6E2AA';
            $zonaSeleccionada = 1;
            $hoy = Carbon::now()->format('Y-m-d');
            $fechaInicial = $fechaFinal = $hoy;

            if(((Auth::user()->rol_id) == 7)) {
                //Director
                $franquiciaSeleccionada = '6E2AA';
            }

            $franquicias = null;
            if(((Auth::user()->rol_id) == 7)) {
                //Director
                $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000'");

            }else {
                //Principal o administrador
                $idFranquicia = self::obtenerIdFranquiciaUsuario($idUsuario);

            }

            return view('administracion.reportes.tablacancelados', [
                'franquicias' => $franquicias,
                'franquiciaSeleccionada' => $franquiciaSeleccionada,
                'zonaSeleccionada' => $zonaSeleccionada,
                'idFranquicia' => $idFranquicia,
                'fechaInicial' => $fechaInicial,
                'fechaFinal' => $fechaFinal

            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function listacontratospagados($idFranquicia){
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {
            //Rol administrador, director, principal

            $idUsuario = Auth::id();
            $franquiciaSeleccionada = '6E2AA';
            $zonaSeleccionada = 1;
            $hoy = Carbon::now()->format('Y-m-d');
            $fechaInicial = $fechaFinal = $hoy;

            if(((Auth::user()->rol_id) == 7)) {
                //Director
                    $franquiciaSeleccionada = '6E2AA';
            }

            $franquicias = null;
            if(((Auth::user()->rol_id) == 7)) {
                //Director
                $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000'");

            }else {
                //Principal o administrador
                $idFranquicia = self::obtenerIdFranquiciaUsuario($idUsuario);

            }

            return view('administracion.reportes.tablapagados', [
                'franquicias' => $franquicias,
                'franquiciaSeleccionada' => $franquiciaSeleccionada,
                'zonaSeleccionada' => $zonaSeleccionada,
                'idFranquicia' => $idFranquicia,
                'fechaInicial' => $fechaInicial,
                'fechaFinal' => $fechaFinal

            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function contratosPagadosTiempoReal(Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {
            //Rol administrador, director, principal

            $idUsuario = Auth::id();
            $idFranquicia = null;

            $franquiciaSeleccionada = $request->input('franquiciaSeleccionada');;
            $zonaSeleccionada = $request->input('zonaSeleccionada');
            $fechaInicial = $request->input('fechaInicial');
            $fechaFinal = $request->input('fechaFinal');
            $cadenaSucursalFiltro  = "";
            $cadenaZonaFiltro = "";
            $franquiciaFiltro = "";

            $hoy = Carbon::now()->format('Y-m-d');
            if(strlen($fechaInicial) == 0){
                $fechaInicial = $hoy;

            } if(strlen($fechaFinal) == 0){
                $fechaFinal = $hoy;
            }

            $cadenaFranquiciaSeleccionada = " ";
            if($franquiciaSeleccionada != null) {
                $cadenaFranquiciaSeleccionada = " AND c.id_franquicia = '$franquiciaSeleccionada'";
                $franquiciaFiltro = $franquiciaSeleccionada;
                $nombreFranquicia = self::obtenerNombreFranquicia($franquiciaFiltro);
                $cadenaSucursalFiltro = ", sucursal: '" .$nombreFranquicia ."'";
            } else {
                $franquiciaFiltro = self::obtenerIdFranquiciaUsuario($idUsuario);
            }

            $cadenaZona = " ";
            if($zonaSeleccionada != null) {
                if($franquiciaSeleccionada != null) {
                    $cadenaZona = " AND c.id_zona = (SELECT z.id FROM zonas z WHERE z.zona = '$zonaSeleccionada' AND z.id_franquicia = '$franquiciaSeleccionada')";
                }else {
                    $cadenaZona = " AND c.id_zona = (SELECT z.id FROM zonas z WHERE z.zona = '$zonaSeleccionada' AND z.id_franquicia = c.id_franquicia)";
                }
                $cadenaZonaFiltro = ", zona: '" . $zonaSeleccionada . "'";
            }

            $cadenaFechaPago = "AND DATE(c.ultimoabono) BETWEEN '$fechaInicial' AND '$fechaFinal'";
            $franquicias = null;
            if(((Auth::user()->rol_id) == 7)) {
                //Director
                $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000'");

                $contratosPagados = DB::select("SELECT UPPER(c.id) AS CONTRATO, (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS,
                                                                    UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.entrecallesentrega) AS ENTRECALLES, UPPER(c.coloniaentrega) AS COLONIA, UPPER(c.calleentrega) AS CALLE,
                                                                    UPPER(c.numeroentrega) AS NUMERO, UPPER(c.nombre) AS NOMBRE,UPPER(c.telefono) AS TELEFONO,
                                                                    (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS ZONA, c.created_at AS FECHAVENTA,c.total as TOTAL, c.pago as FORMAPAGO,
                                                                    (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as FECHAENTREGA,
                                                                    c.estatus_estadocontrato as ESTATUS_ESTADOCONTRATO, c.coordenadas as COORDENADAS,
                                                                    COALESCE((SELECT a.created_at FROM abonos a WHERE a.id_contrato = c.id ORDER BY a.created_at DESC LIMIT 1), '') as ULTIMOABONO
                                                                    FROM contratos c
                                                                    WHERE c.estatus_estadocontrato IN (5)
                                                                    " . $cadenaFranquiciaSeleccionada . "
                                                                    " . $cadenaZona . "
                                                                    " . $cadenaFechaPago . "
                                                                    ORDER BY ULTIMOABONO DESC;");
            }else {
                //Principal o administrador
                $idFranquicia = self::obtenerIdFranquiciaUsuario($idUsuario);
                $contratosPagados = DB::select("SELECT UPPER(c.id) AS CONTRATO, (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS,
                                                                    UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.entrecallesentrega) AS ENTRECALLES, UPPER(c.coloniaentrega) AS COLONIA, UPPER(c.calleentrega) AS CALLE,
                                                                    UPPER(c.numeroentrega) AS NUMERO, UPPER(c.nombre) AS NOMBRE,UPPER(c.telefono) AS TELEFONO,
                                                                    (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS ZONA, c.created_at AS FECHAVENTA,c.total as TOTAL, c.pago as FORMAPAGO,
                                                                    (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as FECHAENTREGA,
                                                                    c.estatus_estadocontrato as ESTATUS_ESTADOCONTRATO, c.coordenadas as COORDENADAS,
                                                                    COALESCE((SELECT a.created_at FROM abonos a WHERE a.id_contrato = c.id ORDER BY a.created_at DESC LIMIT 1), '') as ULTIMOABONO
                                                                    FROM contratos c
                                                                    WHERE c.estatus_estadocontrato IN (5)
                                                                    AND c.id_franquicia = '$idFranquicia'
                                                                    " . $cadenaZona . "
                                                                    " . $cadenaFechaPago . "
                                                                    ORDER BY ULTIMOABONO DESC;");

                //Movimiento
                $franquiciaFiltro = $idFranquicia;
                $nombreFranquicia = self::obtenerNombreFranquicia($franquiciaFiltro);
                $cadenaSucursalFiltro = ", sucursal: '" .$nombreFranquicia ."'";
            }
            //Registrar movimiento historial sucursal
            $mensajeHistorial = "Filtro reporte 'contratos pagados' por periodo de fecha: " . $fechaInicial . " a " .$fechaFinal . $cadenaSucursalFiltro . $cadenaZonaFiltro;
            self::insertarHistorialSucursalReportes($franquiciaFiltro,$idUsuario,$mensajeHistorial);

            $numTotalContratos = count($contratosPagados);
            $view = view('administracion.reportes.listas.listacontratospagados', [
                'franquicias' => $franquicias,
                'franquiciaSeleccionada' => $franquiciaSeleccionada,
                'zonaSeleccionada' => $zonaSeleccionada,
                'idFranquicia' => $idFranquicia,
                'contratosPagados' => $contratosPagados,
                'numTotalContratos' => $numTotalContratos
            ])->render();

            return \Response::json(array("valid"=>"true","view"=>$view, 'fechaInicial' => $fechaInicial, 'fechaFinal' => $fechaFinal));

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function contratosCanceladosTiempoReal(Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {
            //Rol administrador, director, principal

            $idUsuario = Auth::id();
            $idFranquicia = null;

            $franquiciaSeleccionada = $request->input('franquiciaSeleccionada');;
            $zonaSeleccionada = $request->input('zonaSeleccionada');
            $fechaInicial = $request->input('fechaInicial');
            $fechaFinal = $request->input('fechaFinal');
            $cadenaFranquiciaFiltro = "";
            $cadenaZonaFiltro = "";

            $hoy = Carbon::now()->format('Y-m-d');
            if(strlen($fechaInicial) == 0){
                $fechaInicial = $hoy;

            } if(strlen($fechaFinal) == 0){
                $fechaFinal = $hoy;
            }

            $cadenaFranquiciaSeleccionada = " ";
            if($franquiciaSeleccionada != null) {
                $cadenaFranquiciaSeleccionada = " AND c.id_franquicia = '$franquiciaSeleccionada' ";
                $nombreFranquicia = self::obtenerNombreFranquicia($franquiciaSeleccionada);
                $cadenaFranquiciaFiltro = " , sucursal: '" . $nombreFranquicia . "' ";
            }

            $cadenaZona = " ";
            if($zonaSeleccionada != null) {
                if($franquiciaSeleccionada != null) {
                    $cadenaZona = " AND c.id_zona = (SELECT z.id FROM zonas z WHERE z.zona = '$zonaSeleccionada' AND z.id_franquicia = '$franquiciaSeleccionada') ";
                }else {
                    $cadenaZona = " AND c.id_zona = (SELECT z.id FROM zonas z WHERE z.zona = '$zonaSeleccionada' AND z.id_franquicia = c.id_franquicia) ";
                }
                $cadenaZonaFiltro = " , zona: '" . $zonaSeleccionada . "'";
            }

            $cadenaFechaCancelado = " DATE(aut.updated_at) BETWEEN '$fechaInicial' AND '$fechaFinal' ";
            $franquicias = null;
            if(((Auth::user()->rol_id) == 7)) {
                //Director
                $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000'");

                $query = "SELECT UPPER(c.id) AS CONTRATO, (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS,
                                                                    UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.entrecallesentrega) AS ENTRECALLES, UPPER(c.coloniaentrega) AS COLONIA, UPPER(c.calleentrega) AS CALLE,
                                                                    UPPER(c.numeroentrega) AS NUMERO, UPPER(c.nombre) AS NOMBRE,UPPER(c.telefono) AS TELEFONO,
                                                                    (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS ZONA, c.created_at AS FECHAVENTA,c.total as TOTAL,
                                                                    c.ultimoabono as ULTIMOABONO, c.pago as FORMAPAGO,
                                                                    (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as FECHAENTREGA,
																	    c.estatus_estadocontrato as ESTATUS_ESTADOCONTRATO, c.coordenadas as COORDENADAS, aut.updated_at as FECHACANCELACION
                                                                        FROM autorizaciones aut
                                                                        inner join contratos c
                                                                        on c.id = aut.id_contrato
                                                                        WHERE c.estatus_estadocontrato IN (6,8,14)
                                                                        AND aut.estatus  = '1'
                                                                        AND (" . $cadenaFechaCancelado . ")
                                                                        " . $cadenaFranquiciaSeleccionada . "
                                                                        " . $cadenaZona . "
                                                                        ORDER BY aut.created_at DESC;";

                $contratosCancelados = DB::select($query);

                //Asignar como idFranquicia la seleccionada en el filtro
                $idFranquicia = $franquiciaSeleccionada;

            }else {
                //Principal o administrador
                $idFranquicia = self::obtenerIdFranquiciaUsuario($idUsuario);

                $query = "SELECT UPPER(c.id) AS CONTRATO, (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS,
                                                                    UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.entrecallesentrega) AS ENTRECALLES, UPPER(c.coloniaentrega) AS COLONIA, UPPER(c.calleentrega) AS CALLE,
                                                                    UPPER(c.numeroentrega) AS NUMERO, UPPER(c.nombre) AS NOMBRE,UPPER(c.telefono) AS TELEFONO,
                                                                    (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS ZONA, c.created_at AS FECHAVENTA,c.total as TOTAL,
                                                                    c.ultimoabono as ULTIMOABONO, c.pago as FORMAPAGO,
                                                                    (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as FECHAENTREGA,
                                                                    c.estatus_estadocontrato as ESTATUS_ESTADOCONTRATO, c.coordenadas as COORDENADAS, aut.updated_at as FECHACANCELACION
                                                                    FROM autorizaciones aut
                                                                    inner join contratos c
                                                                    on c.id = aut.id_contrato
                                                                    WHERE c.estatus_estadocontrato IN (6,8,14)
                                                                    AND aut.estatus  = '1'
                                                                    AND (" . $cadenaFechaCancelado . ")
                                                                    AND c.id_franquicia = '$idFranquicia'
                                                                    " . $cadenaZona . "
                                                                    ORDER BY aut.created_at DESC";

                $contratosCancelados = DB::select($query);

                $nombreFranquicia = self::obtenerNombreFranquicia($idFranquicia);
                $cadenaFranquiciaFiltro = ", sucursal: '" . $nombreFranquicia . "'";
            }

            //Registrar movimiento en historial sucursal
            $mensajeHistorial = "Filtro reporte 'contratos cancelados' por periodo de fecha: " . $fechaInicial . " a " .$fechaFinal . $cadenaFranquiciaFiltro . $cadenaZonaFiltro;
            self::insertarHistorialSucursalReportes($idFranquicia,$idUsuario,$mensajeHistorial);

            $numTotalContratos = count($contratosCancelados);
            $view = view('administracion.reportes.listas.listacontratoscancelados', [
                'franquicias' => $franquicias,
                'franquiciaSeleccionada' => $franquiciaSeleccionada,
                'zonaSeleccionada' => $zonaSeleccionada,
                'idFranquicia' => $idFranquicia,
                'contratosCancelados' => $contratosCancelados,
                'numTotalContratos' => $numTotalContratos
            ])->render();

            return \Response::json(array("valid"=>"true","view"=>$view, 'fechaInicial' => $fechaInicial, 'fechaFinal' => $fechaFinal));

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }
    public function reportemovimientos($idFranquicia){
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {
            //Rol administrador, director, principal

            $idUsuario = Auth::id();
            $franquicias = null;
            $usuarios = null;

            $hoy = Carbon::now()->format('Y-m-d');
            $fechaInicial = $hoy;
            $fechaFinal = $hoy;

            if(((Auth::user()->rol_id) == 7)) {
                //Director
                $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000'");

            }else {
                //Principal o administrador
                $idFranquicia = self::obtenerIdFranquiciaUsuario($idUsuario);
                $usuarios = DB::select("SELECT u.id,u.name,(SELECT z.zona FROM zonas z WHERE z.id = u.id_zona) as zona
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id=uf.id_usuario
                                              WHERE u.rol_id = 4  AND uf.id_franquicia='" . $idFranquicia . "' ORDER BY u.id_zona");

            }

            return view('administracion.reportes.tablareportemovimientos', [
                'franquicias' => $franquicias,
                'idFranquicia' => $idFranquicia,
                'usuarios'=>$usuarios,
                'fechaInicial' => $fechaInicial,
                'fechaFinal' => $fechaFinal
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function usuariosreportemovimiento(Request $request){
        if (((Auth::user()->rol_id) == 7)) {
            //Rol director

            $usuarios = null;
            $franquiciaSeleccionada = $request->input('franquiciaSeleccionada');;

            $usuarios = DB::select("SELECT u.id,u.name,(SELECT z.zona FROM zonas z WHERE z.id = u.id_zona) as zona
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id=uf.id_usuario
                                              WHERE u.rol_id = 4  AND uf.id_franquicia='" . $franquiciaSeleccionada . "' ORDER BY u.id_zona");

            $response = ['data' => $usuarios];
            return response()->json($response);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function filtroreportemovimientos(Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {
            //Rol administrador, director, principal

            $usuarioSeleccionado = $request->input('usuarioSeleccionado');
            $franquiciaSeleccionada = $request->input('franquiciaSeleccionada');
            $fechaInicial = $request->input('fechaInicial');
            $fechaFinal = $request->input('fechaFinal');


            $hoy = Carbon::now()->format('Y-m-d');
            if(strlen($fechaInicial) == 0){
                $fechaInicial = $hoy;

            } if(strlen($fechaFinal) == 0){
                $fechaFinal = $hoy;
            }

            if(((Auth::user()->rol_id) == 7)) {
                //Director
                $idFranquicia = $franquiciaSeleccionada;
            }else {
                //Principal o administrador
                $idFranquicia = self::obtenerIdFranquiciaUsuario($usuarioSeleccionado);
            }

            $historialMovimientos = DB::select("SELECT (SELECT u.name FROM users u WHERE u.id = '$usuarioSeleccionado') AS name, hc.id_contrato,
                                                    (SELECT c.nombre FROM contratos c WHERE c.id = hc.id_contrato) as nombre, hc.cambios, hc.created_at
                                                    FROM historialcontrato hc
                                                    WHERE hc.id_usuarioC = '$usuarioSeleccionado'
                                                    AND DATE(hc.created_at) BETWEEN '$fechaInicial' AND '$fechaFinal'
                                                    ORDER BY hc.created_at DESC");

            //Registrar movimiento historial sucursal
            $idUsuario = Auth::user()->id;
            $nombreFranquicia = self::obtenerNombreFranquicia($idFranquicia);
            $usuario = DB::select("SELECT * FROM users u WHERE u.id = '$usuarioSeleccionado'");
            $mensajeHistorial = "Filtro reporte 'movimientos' por periodo de fecha: " . $fechaInicial . " a " .$fechaFinal . ", sucursal: '" .$nombreFranquicia
                . "' usuario : '" . $usuario[0]->name . "'";
            self::insertarHistorialSucursalReportes($idFranquicia,$idUsuario,$mensajeHistorial);

            $view = view('administracion.reportes.listas.listareportemovimientos', [
                'idFranquicia' => $idFranquicia,
                'historialMovimientos' => $historialMovimientos
            ])->render();

            return \Response::json(array("valid"=>"true","view"=>$view, 'fechaInicial' => $fechaInicial, 'fechaFinal' => $fechaFinal));


        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function obtenerUsuariosFranquicia(Request $request){
        $franquiciaSeleccionada = $request->input('franquiciaSeleccionada');
        $rolSeleccionado = $request->input("rolSeleccionado");
        $cadenaRol = "";

        if($rolSeleccionado != null){
            //Se selecciono un rol
            $cadenaRol = " AND u.rol_id IN (".$rolSeleccionado.")";
        }

        $usuarios = DB::select("SELECT u.id,u.name as nombre, u.rol_id,  (SELECT r.rol FROM roles r WHERE r.id = u.rol_id) AS rol  FROM users u
                                            INNER JOIN usuariosfranquicia uf ON u.id = uf.id_usuario
                                            WHERE uf.id_franquicia = '$franquiciaSeleccionada'
                                            ". $cadenaRol ."
                                            ORDER BY name");

        $response = ['usuarios' => $usuarios];

        return response()->json($response);
    }

    public function reportegraficas($idFranquicia){
        if (Auth::check() && ((Auth::user()->rol_id) == 7)) {
            //Rol director
            $cadenaRoles = "4,12,13";

            $hoy = Carbon::now()->format('Y-m-d');
            $fechaInicial = $fechaFinal = $hoy;

            $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000'");

            $roles = DB::select("SELECT r.id, r.rol FROM roles r where r.id IN (" .$cadenaRoles  .") ORDER BY r.rol ASC");

            return view('administracion.reportes.reportegraficas', [
                'franquicias' => $franquicias,
                'roles' => $roles,
                'cadenaRoles' => $cadenaRoles,
                'fechaInicial' => $fechaInicial,
                'fechaFinal' => $fechaFinal,
                'idFranquicia' => $idFranquicia
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function creargraficaventas(Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 7)) {
            //Rol director

            $contratosAprobados = "";
            $contratosRechazados = "";
            $contratosCancelados = "";
            $contratosLioFuga = "";
            $contratosGarantia = "";
            $abonosCobranza = "";

            $cadenaFraquicia = "";
            $cadenaUsuraio = "";
            $cadenaCobrador = "";
            $cadenaInnerJoin = "INNER JOIN users u on c.id_usuariocreacion = u.id";

            $franquiciaSeleccionada = $request->input('franquiciaSeleccionada');
            $rolSeleccionado = $request->input('rolSeleccionado');
            $usuario = $request->input('usuarioSeleccionado');
            $fechaInicial = $request->input('fechaInicial');
            $fechaFinal = $request->input('fechaFinal');

            $hoy = Carbon::now()->format('Y-m-d');
            if(strlen($fechaInicial) == 0){
                $fechaInicial = $hoy;

            } if(strlen($fechaFinal) == 0){
                $fechaFinal = $hoy;
            }
            if(strlen($franquiciaSeleccionada) == 0 || $franquiciaSeleccionada == null){
                $cadenaFraquicia = "AND c.id_franquicia != '00000'";
                $cadenaCobrador = "AND a.id_franquicia != '00000'";
            } if(strlen($franquiciaSeleccionada) != 0){
                $cadenaFraquicia = " AND c.id_franquicia = '$franquiciaSeleccionada'";
                $cadenaCobrador = " AND a.id_franquicia = '$franquiciaSeleccionada'";
            } if($rolSeleccionado == '12'){
                //Si el rol es optometrista el parametro de filtro es usuariocreacion u optometrista asignado a contrato
                $cadenaInnerJoin = "INNER JOIN users u on (c.id_optometrista = u.id OR u.id = c.id_usuariocreacion)";
            }if(strlen($usuario) != 0){
                if($rolSeleccionado == '12'){
                    //Si el rol es optometrista
                    $cadenaUsuraio = " AND (c.id_usuariocreacion = '$usuario' OR c.id_optometrista = '$usuario')";
                } if($rolSeleccionado == '13'){
                    //Si el rol es asistente
                    $cadenaUsuraio = " AND c.id_usuariocreacion = '$usuario'";
                } if($rolSeleccionado == '4'){
                    //Si el rol es cobranza
                    $cadenaCobrador = "$cadenaCobrador" . " AND a.id_usuario = '$usuario'";
                }
            }

            //Consultas para extraccion de datos ventas y cobranza
            $consultaAprobados  = "SELECT c.id, c.id_usuariocreacion, c.id_franquicia, c.created_at, c.estatus_estadocontrato FROM contratos c
                                                    " .  $cadenaInnerJoin."
                                                    WHERE (NOT EXISTS (SELECT * FROM garantias g WHERE g.id_contrato = c.id)
                                                    OR  EXISTS (SELECT * FROM garantias g WHERE (g.id_contrato = c.id  AND (g.estadogarantia != 2 AND g.estadogarantia != 3))))
                                                    AND c.estatus_estadocontrato IN (2,4,7,10,11,12)
                                                    AND u.rol_id IN (".$rolSeleccionado.")
                                                     " . $cadenaFraquicia . "
                                                     " . $cadenaUsuraio . "
                                                     AND DATE(c.created_at) BETWEEN '$fechaInicial' AND '$fechaFinal'";

            $consultaRechazados = "SELECT DISTINCT c.id, c.id_usuariocreacion, c.id_franquicia, c.created_at FROM contratos c
                                                    " .  $cadenaInnerJoin."
                                                    where estatus_estadocontrato IN (8)
                                                    AND u.rol_id IN (".$rolSeleccionado.")
                                                     " . $cadenaFraquicia . "
                                                     " . $cadenaUsuraio . "
                                                     AND DATE(c.created_at) BETWEEN '$fechaInicial' AND '$fechaFinal'";

            $consultaCancelados = "SELECT DISTINCT c.id, c.id_usuariocreacion, c.id_franquicia, c.created_at FROM contratos c
                                                    " .  $cadenaInnerJoin."
                                                    where estatus_estadocontrato IN (6)
                                                    AND u.rol_id IN (".$rolSeleccionado.")
                                                    " . $cadenaFraquicia . "
                                                    " . $cadenaUsuraio . "
                                                     AND DATE(c.created_at) BETWEEN '$fechaInicial' AND '$fechaFinal'";

            $consultaLioFuga = "SELECT  DISTINCT c.id, c.id_usuariocreacion, c.id_franquicia, c.created_at FROM contratos c
                                                    " .  $cadenaInnerJoin."
                                                    where estatus_estadocontrato IN (14)
                                                    AND u.rol_id IN (".$rolSeleccionado.")
                                                    " . $cadenaFraquicia . "
                                                    " . $cadenaUsuraio . "
                                                     AND DATE(c.created_at) BETWEEN '$fechaInicial' AND '$fechaFinal'";

            $consultaGarantias = "SELECT c.id, c.id_usuariocreacion, c.id_franquicia, c.created_at FROM contratos c
                                                    INNER JOIN garantias g on c.id = g.id_contrato
                                                    " .  $cadenaInnerJoin."
                                                    where c.estatus_estadocontrato IN (2,4,7,10,11,12)
                                                    AND (g.estadogarantia = 2 OR g.estadogarantia = 3)
                                                    AND u.rol_id IN (".$rolSeleccionado.")
                                                    " . $cadenaFraquicia . "
                                                    " . $cadenaUsuraio . "
                                                    AND DATE(c.created_at) BETWEEN '$fechaInicial' AND '$fechaFinal'";

            $consultaAbonos = "SELECT SUM(a.abono) AS Total, DATE_FORMAT(a.created_at, '%Y-%m-%d') AS Fecha  FROM abonos a where DATE(a.created_at) BETWEEN '$fechaInicial' AND '$fechaFinal'
                            " . $cadenaCobrador . "
                            GROUP BY DATE_FORMAT(a.created_at,'%Y-%m-%d')
                            ORDER BY DATE_FORMAT(a.created_at,'%Y-%m-%d') ASC";

            //Consultas dependiendo los parametros del rol
            if($rolSeleccionado == '4,12,13'){
                //Si el rol es Asistente, Opto o Todos

                $contratosAprobados = DB::select($consultaAprobados);

                $contratosRechazados = DB::select($consultaRechazados);

                $contratosCancelados = DB::select($consultaCancelados);

                $contratosLioFuga = DB::select($consultaLioFuga);

                $contratosGarantia = DB::select($consultaGarantias);

                $abonosCobranza = DB::select($consultaAbonos);

            } if(($rolSeleccionado == '12') || ($rolSeleccionado == '13')){
                //Si el rol es Asistente u Opto

                $contratosAprobados = DB::select($consultaAprobados);

                $contratosRechazados = DB::select($consultaRechazados);

                $contratosCancelados = DB::select($consultaCancelados);

                $contratosLioFuga = DB::select($consultaLioFuga);

                $contratosGarantia = DB::select($consultaGarantias);

            } if($rolSeleccionado == '4'){
                //Si Rol es igual a Cobranza
                $abonosCobranza = DB::select($consultaAbonos);
            }


            $response = ['contratosAprobados' => $contratosAprobados, 'contratosRechazados' => $contratosRechazados, 'contratosCancelados' => $contratosCancelados ,
                        'contratosLioFuga' => $contratosLioFuga, 'contratosGarantia' => $contratosGarantia, 'abonosCobranza'=>$abonosCobranza, 'rolSeleccionado' =>$rolSeleccionado,
                        'fechaInicial'=>$fechaInicial, 'fechaFinal' => $fechaFinal];

            return response()->json($response);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function reportellamadas($idFranquicia){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7   || (Auth::user()->rol_id) == 8)) {
            //Rol ADMINISTADOR, PRINCIPAL, DIRECTOR

            //Inicializar fechas
            $hoy = Carbon::now()->format('Y-m-d');
            $fechaInicio = $fechaFinal = $hoy;

            if((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 8){
                //Si rol es Administrador o Principal solo trae usuarios rol cobranza de la sucursal
                //Id usuario logueado

                $idUsuario = Auth::user()->id;
                $franquicia = DB::select("SELECT uf.id_franquicia FROM usuariosfranquicia uf WHERE uf.id_usuario = '$idUsuario'");
                $idFranquicia = $franquicia[0]->id_franquicia;

                $usuarios = DB::select("SELECT u.id,u.name,(SELECT z.zona FROM zonas z WHERE z.id = u.id_zona) as zona
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id=uf.id_usuario
                                              WHERE u.rol_id = 4  AND uf.id_franquicia='" . $idFranquicia . "' ORDER BY u.id_zona");
            } else{
                //Si es rol de riector extrae todos los usuarios rol cobranza

                $usuarios = DB::select("SELECT u.id,u.name,(SELECT z.zona FROM zonas z WHERE z.id = u.id_zona) as zona,
                                              (SELECT f.ciudad FROM franquicias f WHERE uf.id_franquicia = f.id) as ciudad
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id=uf.id_usuario
                                              WHERE u.rol_id = 4  AND uf.id_franquicia != '00000' ORDER BY ciudad, zona");
            }

            return view('administracion.reportes.tablareportellamadas', [
                'usuarios' =>$usuarios,
                'fechaInicio' => $fechaInicio,
                'fechaFinal' => $fechaFinal,
                'idFranquicia' => $idFranquicia
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function listareportellamadas(Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7   || (Auth::user()->rol_id) == 8)) {
            //Rol ADMINISTADOR, PRINCIPAL, DIRECTOR

            $contratoscorte = null;
            $cadenaCortes = "";

            $usuarioSeleccionado = $request->input("usuarioSeleccionado");
            $fechaInicio = $request->input("fechaInicio");
            $fechaFinal = $request->input("fechaFinal");

            //Validacion de fechas
            $hoy = Carbon::now()->format('Y-m-d');
            if(strlen($fechaInicio) == 0){
                //Esta vacia fecha Inicio
                $fechaInicio = $hoy;

            } if(strlen($fechaFinal) == 0){
                //Esta vacia fecha final
                $fechaFinal = $hoy;
            }

            $fechaInicio = Carbon::parse($fechaInicio)->format('Y-m-d');
            $fechaFinal = Carbon::parse($fechaFinal)->format('Y-m-d');

            if($fechaInicio < $fechaFinal){
                //Es mayor la fecha final a inicial

                //Validacion de usuario
                $existeUsuario = DB::select("SELECT u.id,u.id_zona,u.name,uf.id_franquicia FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                    WHERE u.id = '$usuarioSeleccionado'");

                if ($existeUsuario != null) { //Esta seleccionado un usuario valido?
                    //Si esta seleccionado

                    $idUsuario = $existeUsuario[0]->id;
                    $usuarioFranquicia = $existeUsuario[0]->id_franquicia;

                    //Cortes en un periodo de tiempo para el usuario seleccionado
                    $cortesUsuario = DB::select("SELECT * FROM historialcortes hc WHERE hc.indice IN (SELECT a.id_corte FROM abonos a WHERE a.id_usuario = '$idUsuario')
                                                        AND hc.id_cobrador = '$idUsuario' AND hc.created_at between '$fechaInicio' and '$fechaFinal'
                                                        ORDER BY hc.created_at DESC");

                    if($cortesUsuario != null){
                        //Si existe al menos un corte

                        //Recorrido para obtener indice de corte
                        foreach ($cortesUsuario as $corteUsuario) {
                            $cadenaCortes =  $cadenaCortes . $corteUsuario->indice . ', ';
                        }

                        //Eliminamos la ultima coma sobrante
                        $cadenaCortes = trim($cadenaCortes, ', ');

                        $consulta = "SELECT ccl.indice, ccl.id_contrato,
                    (SELECT hc.cambios FROM historialcontrato hc WHERE hc.id_contrato = ccl.id_contrato AND hc.id = ccl.id_historialcontrato AND hc.tipomensaje = '2') as mensaje,
                    (SELECT c.nombre FROM contratos c WHERE c.id = ccl.id_contrato AND c.id_franquicia = '$usuarioFranquicia') as nombrecliente,
                    (SELECT u.name FROM users u WHERE u.id = ccl.id_cobrador) as nombrecobrador
                    FROM contratoscortellamada ccl WHERE ccl.id_corte IN (" . $cadenaCortes . ") ORDER BY ccl.indice DESC";

                        $datoscontratoscorte =  DB::select($consulta);

                        $contratoscorte = $datoscontratoscorte; //Se le pasan los datos al arreglo contratoscortellamada

                        //Registrar movimiento historial sucursal
                        $idFranquicia = self::obtenerIdFranquiciaUsuario($idUsuario);
                        $mensajeHistorial = "Filtro reporte 'llamadas' por periodo de fecha: " . $fechaInicio . " a " .$fechaFinal . ", usuario : '" . $existeUsuario[0]->name . "'";
                        self::insertarHistorialSucursalReportes($idFranquicia,$idUsuario,$mensajeHistorial);
                    }
                }
            }

            $view = view('administracion.reportes.listas.listareportellamadas', [
                '$usuarioSeleccionado' => $usuarioSeleccionado,
                'contratoscorte' => $contratoscorte
            ])->render();

            return \Response::json(array("valid"=>"true","view"=>$view, 'fechaInicio' => $fechaInicio, 'fechaFinal' => $fechaFinal));


        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    //Funcion: listareporteasistencia
    //Descripcion: Traera el reporte de asistencia de la sucursal una vez seleccionada una semana en particular
    public function listareporteasistencia($idFranquicia){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7   || (Auth::user()->rol_id) == 8)) {
            //Rol ADMINISTADOR, PRINCIPAL, DIRECTOR

            //Obtener perido de fechas a consultar
            $now = Carbon::now();
            $nowParse = Carbon::parse($now)->format('Y-m-d');
            $polizaGlobales = new polizaGlobales();
            $numeroDia = $now->dayOfWeekIso;    //Obtenemos el dia de la semana actual
            $fechaLunes = $nowParse;    //Obtener fecha con formato

            if($numeroDia != 1 && $numeroDia != 7){
                //Si no es lunes y domingo
                $fechaLunes = $polizaGlobales::obtenerDia($numeroDia, 1);   //se obtenie la fecha del lunes anterior a la fecha actaul
            }
            if($numeroDia == 7){
                //Si es domigo obtenemos la fecha del lunes pasado
                $fechaLunes = date("Y-m-d",strtotime($nowParse."- 6 days"));
                $numeroDia = 6; //Dia semana lo tomaremos como sabado por ser ultimo dia de trabajo
            }

            $fechaSabadoSiguiente = date("Y-m-d",strtotime($fechaLunes."+ 5 days"));

            //Lista de franquicias para rol Director
            $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000'");

            $idUsuario = Auth::user()->id;
            $rolUsuario = Auth::user()->rol_id;

            return view('administracion.reportes.tablareporteasistencia', [
                'idFranquicia' =>$idFranquicia,
                'rolUsuario' => $rolUsuario,
                'franquicias' => $franquicias,
                'fechaHoy' => $nowParse,
                'fechaLunes' => $fechaLunes,
                'fechaSabadoSiguiente' => $fechaSabadoSiguiente
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    //Funcion: obtenerdiasasistencia
    //Descripcion: Trae la fecha que tomara el periodo de asistencia
    public function obtenerdiasasistencia(Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7   || (Auth::user()->rol_id) == 8)) {
            //Rol ADMINISTADOR, PRINCIPAL, DIRECTOR

            $diaSeleccionado = $request->input("diaSeleccionado");
            $diaSeleccionado = Carbon::parse($diaSeleccionado)->format('Y-m-d');
            $numeroDia = $request->input("numeroDia");
            $diasRestar = $numeroDia - 1;
            $fechaLunes = $diaSeleccionado;

            if($numeroDia != 1){
                //Si no es lunes
                $fechaLunes = date("Y-m-d",strtotime($diaSeleccionado."- ". $diasRestar ." days"));  //se obtenie la fecha del lunes anterior a la fecha actaul
            }
            $fechaSabadoSiguiente = date("Y-m-d",strtotime($fechaLunes."+ 5 days")); // se obtiene la fecha del siguinete sabado

            return \Response::json(array("valid"=>"true", 'fechaLunes' => $fechaLunes, 'fechaSabadoSiguiente' => $fechaSabadoSiguiente));

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function cargarListaAsistencia(Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7   || (Auth::user()->rol_id) == 8)) {
            //Rol ADMINISTADOR, PRINCIPAL, DIRECTOR

            $franquiciaSeleccionada = $request->input("franquiciaSeleccionada");
            $rolUsuario = $request->input("rol_usuario");
            $fechaLunes = $request->input("fechaLunes");
            $fechaSabadoSiguiente = $request->input("fechaSabadoSiguiente");
            $diaSeleccionado = $request->input("diaSeleccionado");
            $cadenaPoliza = "";
            $idPolizaDiaSeleccionado = "";
            $usuariosPoliza = null;

            $idUsuario = Auth::user()->id;

            switch ($rolUsuario){
                case 6:
                    //Rol de administrador
                case 8:
                    //Rol de Principal
                    $idFranquicia = self::obtenerIdFranquiciaUsuario($idUsuario);
                    break;

                case 7:
                    //Rol de director
                    //Por default envia la sucursal en la posicion 0 del arreglo de franquicias
                    $idFranquicia = $franquiciaSeleccionada;
                    break;
            }

            $consulta = "SELECT p.id, p.id_franquicia,STR_TO_DATE(p.created_at,'%Y-%m-%d') AS fechaPoliza FROM poliza p
                                                WHERE p.id_franquicia = '$idFranquicia'
                                                AND STR_TO_DATE(p.created_at,'%Y-%m-%d') BETWEEN '$fechaLunes' AND '$fechaSabadoSiguiente'
                                                ORDER BY p.created_at asc";

            $polizas = DB::select($consulta);

            if($polizas != null){

                //Se tiene polizas existentes
                foreach ($polizas as $poliza){
                    $cadenaPoliza = $cadenaPoliza . $poliza->id . ',';
                    if(Carbon::parse($poliza->fechaPoliza)->format('Y-m-d') == Carbon::parse($diaSeleccionado)->format('Y-m-d')){
                        $idPolizaDiaSeleccionado = $poliza->id;
                    }
                }

                //Quitamos la ultima "," de la cadena
                $cadenaPoliza = trim($cadenaPoliza, ',');

                $usuarios = DB::select("SELECT DISTINCT (u.id), u.name, u.fechaeliminacion AS eliminacion, u.created_at AS fechaCreacion FROM users u
                                                INNER JOIN asistencia a ON a.id_usuario = u.id
                                                WHERE a.id_poliza IN ($cadenaPoliza)
                                                ORDER BY u.name ASC");

                $usuariosPoliza = DB::select("SELECT u.id, UPPER(u.name) AS nombre FROM asistencia a INNER JOIN users u ON u.id = a.id_usuario
                                                WHERE a.id_poliza = '$idPolizaDiaSeleccionado' ORDER BY nombre ASC");

                $asistenciaUsuarios = self::obtenerAsistenciaUsuario($usuarios, $polizas, $fechaLunes);

                //Registrar movimiento en historial sucursal
                $nombreFranquicia = self::obtenerNombreFranquicia($idFranquicia);
                $mensajeHistorial = "Filtro reporte 'asistencia' por periodo de fecha: " . $fechaLunes . " a " .$fechaSabadoSiguiente . ", sucursal : '" . $nombreFranquicia . "'";
                self::insertarHistorialSucursalReportes($idFranquicia,$idUsuario,$mensajeHistorial);

            }else {
                //No existen polizas en lo que va de la semana
                $asistenciaUsuarios = null;
            }

            $view = view('administracion.reportes.listas.listareporteasistencia', [
                'asistenciaUsuarios' => $asistenciaUsuarios, 'diaSeleccionado' => $diaSeleccionado,
                'usuariosPoliza' => $usuariosPoliza, 'idPolizaDiaSeleccionado' => $idPolizaDiaSeleccionado
            ])->render();

            return \Response::json(array("valid"=>"true","view"=>$view));

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function registrarAsistenciaUsuario(Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7   || (Auth::user()->rol_id) == 8)) {
            //Rol ADMINISTADOR, PRINCIPAL, DIRECTOR

            $usuarioSeleccionado = $request->input("usuarioSeleccionado");
            $asistencia = $request->input('asistencia');
            $asistenciaTipo = $request->input('asistenciaTipo');
            $idPolizaDiaSeleccionado = $request->input("idPolizaDiaSeleccionado");
            $franquiciaSeleccionada = $request->input("franquiciaSeleccionada");
            $rolUsuario = $request->input("rol_usuario");

            $mensaje = "";
            $bandera = true;

            $existeUsuario = DB::select("SELECT u.id, UPPER(u.name) as nombre FROM users u WHERE u.id = '$usuarioSeleccionado'");
            if($existeUsuario != null){
                //Existe el usuario - Verificar si entro en la lista de asistencia de la poliza
                $existeUsuarioAsistencia = DB::select("SELECT * FROM asistencia a WHERE a.id_poliza = '$idPolizaDiaSeleccionado' AND a.id_usuario = '$usuarioSeleccionado'");
                if($existeUsuarioAsistencia != null){
                    //Si existe usuario en lista de asistencia correspondiente a poliza del dia seleccionado
                    if($asistenciaTipo == 0 || ($asistenciaTipo == 1 && ($existeUsuarioAsistencia[0]->registroentrada != null && $existeUsuarioAsistencia[0]->id_tipoasistencia != 0))) {
                        $idUsuario = Auth::user()->id;

                        switch ($rolUsuario) {
                            case 6:
                                //Rol de administrador
                            case 8:
                                //Rol de Principal
                                $idFranquicia = self::obtenerIdFranquiciaUsuario($idUsuario);
                                break;

                            case 7:
                                //Rol de director
                                //Por default envia la sucursal en la posicion 0 del arreglo de franquicias
                                $idFranquicia = $franquiciaSeleccionada;
                                break;
                        }

                        //Actualizar valor de asistencia para usuario seleccionado
                        switch ($asistenciaTipo) {
                            case 0:
                                //Registro de entrada
                                DB::table('asistencia')->where('id_poliza', $idPolizaDiaSeleccionado)->where('id_usuario', $usuarioSeleccionado)->update([
                                    'id_tipoasistencia' => $asistencia, 'registroentrada' => Carbon::now(), 'updated_at' => Carbon::now()
                                ]);

                                $mensajeHistorial = "Actualizo asistencia de entrada para usuario: '" . $existeUsuario[0]->nombre . "' en lista asistencia del dia '" . Carbon::parse($existeUsuarioAsistencia[0]->created_at)->format('Y-m-d') . "'";
                                break;
                            case 1:
                                //Registro de salida
                                DB::table('asistencia')->where('id_poliza', $idPolizaDiaSeleccionado)->where('id_usuario', $usuarioSeleccionado)->update([
                                    'registrosalida' => Carbon::now(), 'updated_at' => Carbon::now()
                                ]);

                                $mensajeHistorial = "Actualizo registro de salida para usuario: '" . $existeUsuario[0]->nombre . "' en lista asistencia del dia '" . Carbon::parse($existeUsuarioAsistencia[0]->created_at)->format('Y-m-d') . "'";
                                break;
                        }

                        //Guardar movimiento en historial poliza
                        DB::table('historialpoliza')->insert([
                            'id_usuarioC' => $idUsuario, 'id_poliza' => $idPolizaDiaSeleccionado, 'created_at' => Carbon::now(),
                            'cambios' => $mensajeHistorial
                        ]);

                        //Registrar movimiento en historial sucursal
                        self::insertarHistorialSucursalReportes($idFranquicia, $idUsuario, $mensajeHistorial);
                        $mensaje = "Asistencia registrada correctamente para usuario: " . $existeUsuario[0]->nombre . "'. Espera a que cargue de nuevo la lista de asistencia.";
                    }else{
                        //No tiene asistencia de entrada
                        $bandera = false;
                        $mensaje = "No puedes registrar hora de salida debido a que no cuentas con asistencia.";
                    }
                }else{
                    //No existe usuario en la poliza
                    $bandera = false;
                    $mensaje = "No se encuentro el usuario registrados en la lista de asistencia del dia seleccionado.";
                }

            }else{
                //No existe usuario
                $bandera = false;
                $mensaje = "No existe usuario seleccionado.";
            }

            return \Response::json(array("valid"=>"true", 'mensaje' => $mensaje, 'bandera' => $bandera));

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function obtenerAsistenciaUsuario($usuarios,$polizas, $fechaLunes){

        //Fechas de dias para comparar polizas
        $fechaLunes = $fechaLunes;
        $fechaMartes = date("Y-m-d",strtotime($fechaLunes."+ 1 days"));
        $fechaMiercoles = date("Y-m-d",strtotime($fechaLunes."+ 2 days"));
        $fechaJueves = date("Y-m-d",strtotime($fechaLunes."+ 3 days"));
        $fechaViernes = date("Y-m-d",strtotime($fechaLunes."+ 4 days"));
        $fechaSabado = date("Y-m-d",strtotime($fechaLunes."+ 5 days"));

        foreach ($usuarios as $usuario) {
            $idUsuario = $usuario ->id;
            $fechaEliminacion = $usuario ->eliminacion;

            if($fechaEliminacion != null){
                //Si usuario presenta fecha de eliminacion convertimos a tipo fecha con formato
                $fechaEliminacion = Carbon::parse($fechaEliminacion)->format('Y-m-d');
            }

            //Colocamos los nuevos atributos a cada usuario
            $usuario->asistenciaLunes = null;
            $usuario->asistenciaMartes = null;
            $usuario->asistenciaMiercoles = null;
            $usuario->asistenciaJueves = null;
            $usuario->asistenciaViernes = null;
            $usuario->asistenciaSabado = null;

            foreach ($polizas as $poliza){
                $idPoliza = $poliza -> id;

                $asistencia = DB::select("SELECT a.id_tipoasistencia FROM asistencia a WHERE a.id_poliza = '$idPoliza' AND a.id_usuario = '$idUsuario'");

                if($asistencia != null){
                    $valorAsistencia = $asistencia[0]->id_tipoasistencia;
                    switch ($valorAsistencia){
                        case 0:
                            //Falta
                            $valorAsistencia = 'F';
                            break;

                        case 1:
                            //Asistencia
                            $valorAsistencia = 'A';
                            break;

                        case 2:
                            //Retardo
                            $valorAsistencia = 'R';
                            break;
                    }
                    if($poliza->fechaPoliza == $fechaLunes){
                        $usuario->asistenciaLunes = $valorAsistencia;
                    }if($poliza->fechaPoliza == $fechaMartes){
                        $usuario->asistenciaMartes = $valorAsistencia;
                    }if($poliza->fechaPoliza == $fechaMiercoles){
                        $usuario->asistenciaMiercoles = $valorAsistencia;
                    }if($poliza->fechaPoliza == $fechaJueves){
                        $usuario->asistenciaJueves = $valorAsistencia;
                    }if($poliza->fechaPoliza == $fechaViernes){
                        $usuario->asistenciaViernes = $valorAsistencia;
                    }if($poliza->fechaPoliza == $fechaSabado){
                        $usuario->asistenciaSabado = $valorAsistencia;
                    }
                }
                //Verificamos que no haya sido dado de baja
                if(($fechaEliminacion != null) && ($poliza->fechaPoliza > $fechaEliminacion)){
                    //Si se dio de baja este usuario colocamos asistencia como F
                    if($poliza->fechaPoliza == $fechaLunes){
                        $usuario->asistenciaLunes = 'F';
                    }if($poliza->fechaPoliza == $fechaMartes){
                        $usuario->asistenciaMartes = 'F';
                    }if($poliza->fechaPoliza == $fechaMiercoles){
                        $usuario->asistenciaMiercoles = 'F';
                    }if($poliza->fechaPoliza == $fechaJueves){
                        $usuario->asistenciaJueves = 'F';
                    }if($poliza->fechaPoliza == $fechaViernes){
                        $usuario->asistenciaViernes = 'F';
                    }if($poliza->fechaPoliza == $fechaSabado){
                        $usuario->asistenciaSabado = 'F';
                    }
                }

            } // Fin ciclo polizas

        } // Fin ciclo usuarios

        return $usuarios;
    }

    public function reportebuzon($idFranquicia){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7   || (Auth::user()->rol_id) == 8)) {
            //Rol ADMINISTADOR, PRINCIPAL, DIRECTOR

            $franquicias = null;
            $mensajesBuzon = null;
            $idUsuario = Auth::id();

            if(((Auth::user()->rol_id) == 7)) {
                //ROL DE DIRECTOR
                $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000' ORDER BY f.ciudad ASC");
            }else {
                //ROL DE ADMINISTRADOR O PRINCIPAL
                $mensajesBuzon = DB::select("SELECT b.created_at, b.mensaje, (SELECT u.name FROM users u WHERE u.id = b.id_usuario ) AS usuario_creacion,
                                    (SELECT f.ciudad FROM franquicias f WHERE f.id = b.id_franquicia) AS ciudad FROM buzon b
                                    WHERE b.id_franquicia='$idFranquicia' ORDER BY b.created_at DESC");
            }

            return view('administracion.reportes.tablareportebuzon', [
                'franquicias' => $franquicias,
                'idFranquicia' => $idFranquicia,
                'mensajesBuzon' => $mensajesBuzon
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function reportebuzontiemporeal(Request $request){
        if (Auth::check() && (Auth::user()->rol_id) == 7) {
            //Rol ADMINISTADOR, PRINCIPAL, DIRECTOR

            $idFranquiciaSeleccionada = $request->input('idFranquiciaSeleccionada');

            if(strlen($idFranquiciaSeleccionada) > 0){
                //Si se selecciono una sucursal
                $cadenaFranquicia = "WHERE b.id_franquicia = '$idFranquiciaSeleccionada'";
            } else {
                //Se selecciono la opcion de todas las sucursales
                $cadenaFranquicia = "WHERE b.id_franquicia != '00000'";
            }

            $cadena = "SELECT b.created_at, b.mensaje, (SELECT u.name FROM users u WHERE u.id = b.id_usuario ) AS usuario_creacion,
                                    (SELECT f.ciudad FROM franquicias f WHERE f.id = b.id_franquicia) AS ciudad FROM buzon b
                                     ". $cadenaFranquicia ." ORDER BY b.created_at DESC";

            $mensajesBuzon = DB::select($cadena);

            $response = ['mensajesBuzon' => $mensajesBuzon];

            return response()->json($response);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function listareportehistorialsucursal($idFranquicia){
            if (Auth::check() && ((Auth::user()->rol_id) == 7)) {
                //DIRECTOR

                //Asignamos fecha actual por defaul
                $now = Carbon::now();
                $nowParse = Carbon::parse($now)->format('Y-m-d');
                $fechaInicio = $nowParse;
                $fechaFinal = $nowParse;

                $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000' ORDER BY f.ciudad ASC");

                $id_Usuario = Auth::user()->id;
                $franquicia = DB::select("SELECT uf.id_franquicia FROM usuariosfranquicia uf WHERE uf.id_usuario = '$id_Usuario' LIMIT 1");
                $id_franquicia = $franquicia[0]-> id_franquicia;

                return view('administracion.reportes.tablareportehistorialsucursal', [
                    'franquicias' => $franquicias,
                    'id_franquicia' => $id_franquicia,
                    'fechaInicio' => $fechaInicio,
                    'fechaFinal' => $fechaFinal,
                    'idFranquicia' => $idFranquicia
                ]);

            } else {
                if (Auth::check()) {
                    return redirect()->route('redireccionar');
                } else {
                    return redirect()->route('login');
                }
            }

    }

    public function cargarlistamovimientossucursal(Request $request){
        $sucursalSeleccionada = $request->input('sucursalSeleccionada');
        $accionSeleccionada = $request->input('accionSeleccionada');
        $fechaInicio = $request->input('fechaInicio');
        $fechaFin = $request->input('fechaFin');
        $cadenaAccion = " ";

        //Validacion de fechas
        $hoy = Carbon::now()->format('Y-m-d');
        if(strlen($fechaInicio) == 0){
            //Esta vacia fecha Inicio
            $fechaInicio = $hoy;

        } if(strlen($fechaFin) == 0){
            //Esta vacia fecha final
            $fechaFin = $hoy;
        }

        $fechaInicio = Carbon::parse($fechaInicio)->format('Y-m-d');
        $fechaFin = Carbon::parse($fechaFin)->format('Y-m-d');

        //Selecciono todas las seciones?
        if($accionSeleccionada != null){
            //Si es diferente de null solo filtrara por la seccion seleccionada
            $cadenaAccion = " AND hs.seccion = '$accionSeleccionada' ";

        }

        $listaMovimientos = DB::select("SELECT u.name, hs.cambios, hs.created_at FROM historialsucursal hs
                                                INNER JOIN users u ON u.id = hs.id_usuarioC
                                                WHERE hs.id_franquicia = '$sucursalSeleccionada'
                                                AND STR_TO_DATE(hs.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicio','%Y-%m-%d')
                                                AND STR_TO_DATE('$fechaFin','%Y-%m-%d')
                                                ". $cadenaAccion ." ORDER BY hs.created_at DESC");

        $view = view('administracion.reportes.listas.listareportehistorialsucursal', [
            'listaMovimientos' => $listaMovimientos

        ])->render();

        return \Response::json(array("valid"=>"true","view"=>$view, 'fechaInicio' => $fechaInicio, 'fechaFin' => $fechaFin));

    }

    private static function obtenerNombreFranquicia($idFranquicia) {
        $franquicia = DB::select("SELECT f.ciudad FROM franquicias f WHERE f.id = '$idFranquicia'");
        if($franquicia != null) {
            $nombreFranquicia = $franquicia[0]->ciudad;
        }
        return $nombreFranquicia;
    }

    //Funcion: insertarHistorialSucursalReportes
    //Descripcion: Inserta un nuevo registro de historial de busqueda en reportes (Tipo mensaje: 7 -> Historial de busqueda, seccion: 3 -> Reportes)
    private function  insertarHistorialSucursalReportes($idFranquicia, $idUsuarioC, $cambio){
        DB::table('historialsucursal')->insert([
            'id_usuarioC' => $idUsuarioC, 'id_franquicia' => $idFranquicia,
            'tipomensaje' => '7', 'created_at' => Carbon::now(), 'cambios' => $cambio, 'seccion' => '3'
        ]);
    }


    public function listareportearmazones($idFranquicia){
        if (Auth::check() && ((Auth::user()->rol_id) == 7)) {
            //DIRECTOR

            $armazones = DB::select("SELECT * FROM producto p WHERE id_tipoproducto = '1' and STR_TO_DATE(p.created_at,'%Y-%m-%d') >= '2022-11-13' ORDER BY p.nombre, p.color ASC");
            $totalPiezas = 0;
            $totalPiezasVendidas = 0;
            $totalPiezasRestantes = 0;
            $armazonSeleccionada = null;
            $armazonContratos = array();
            $totalRegistros = 0;

            return view('administracion.reportes.tablareportearmazones', [
                'armazones' => $armazones,
                'armazonSeleccionada' => $armazonSeleccionada,
                'totalPiezas' => $totalPiezas,
                'totalPiezasVendidas' => $totalPiezasVendidas,
                'totalPiezasRestantes' => $totalPiezasRestantes,
                'totalRegistros' => $totalRegistros,
                'armazonContratos' => $armazonContratos,
                'idFranquicia' => $idFranquicia
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function filtrareportearmazones(Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 7)) {
            //DIRECTOR
            $id_armazonSeleccionada = $request->input('armazonSeleccionada');
            $idFranquicia = $request->input('idFranquiciaActual');

            if($id_armazonSeleccionada != null){
                //Selecciono un armazon valido
                $armazonSeleccionada = DB::select("SELECT * FROM producto p WHERE p.id = '$id_armazonSeleccionada' AND p.id_tipoproducto = '1'");
                $totalPiezas = $armazonSeleccionada[0]->totalpiezas;
                $totalPiezasVendidas = $armazonSeleccionada[0]->totalpiezas - $armazonSeleccionada[0]->piezas;
                $totalPiezasRestantes = $armazonSeleccionada[0]->piezas;

                $armazones = DB::select("SELECT * FROM producto p WHERE id_tipoproducto = '1' AND STR_TO_DATE(p.created_at,'%Y-%m-%d') >= '2022-11-13' ORDER BY p.nombre, p.color ASC");

                //Consulta para obtener lista de contratos con el producto agregado en su historial clinico
                $historialesContrato = DB::select("SELECT DISTINCT c.id, c.created_at, c.estatus_estadocontrato, ec.descripcion, hc.id_producto,
                                            (SELECT COUNT(hc.id) FROM historialclinico hc WHERE hc.id_contrato = c.id AND hc.id_producto = '$id_armazonSeleccionada') AS totalhistoriales
                                            FROM contratos c
                                            INNER JOIN historialclinico hc ON hc.id_contrato = c.id
                                            INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                            WHERE hc.id_producto = '$id_armazonSeleccionada'
                                            ORDER BY c.created_at DESC");

                //Consulta para obtener lista contratos con producto agregado por separado al contrato
                $productosContrato = DB::select("SELECT DISTINCT c.id, c.created_at, c.estatus_estadocontrato, ec.descripcion,
                                            (SELECT COUNT(cp.id) FROM contratoproducto cp WHERE cp.id_contrato = c.id AND cp.id_producto = '$id_armazonSeleccionada') AS totalarmazones
                                            FROM contratos c
                                            INNER JOIN contratoproducto cp ON cp.id_contrato = c.id
                                            INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                            WHERE cp.id_producto = '$id_armazonSeleccionada'
                                            ORDER BY c.created_at DESC");

                //Nota: Se aplicaron 2 consulta debido a que las subconsultas para el total de productos y total de historiales con ese producto
                // tenian un tiempo de respuesta muy alto

                $armazonContratos = array();

                //Validar si existen productos comprados por separado al contrato
                if(sizeof($historialesContrato) > 0 && sizeof($productosContrato) == 0){
                    //No se compro ningun producto armazon por separado al contrato
                    foreach ($historialesContrato as $historialContrato){
                        $historialContrato->totalarmazones = 0; //Asignar como total de armazones 0
                    }
                    $armazonContratos = $historialesContrato;   //Pasamos los contratos al arreglo que enviaremos como resultado

                }

                if(sizeof($productosContrato) > 0 && sizeof($historialesContrato) == 0){
                    //Solo tenemos armazones compradas por separado para el contrato
                    foreach ($productosContrato as $productoContrato){
                        $productoContrato->totalhistoriales = 0;    //Si asignaran como total de historiales para el contrato con ese producto como 0
                    }
                    $armazonContratos = $productosContrato; //Pasamos los contratos al arreglo que enviaremos como resultado
                }

                //Se tienen productos tanto en los historiales como en compras por separado
                if(sizeof($historialesContrato) > 0 && sizeof($productosContrato) > 0){

                    //Recorrer ambos arreglos para poder verificar si el contrato tiene un producto agregado
                    foreach ($productosContrato as $productoContrato){
                        foreach ($historialesContrato as $historialContrato){
                            $tieneHistorial = false;
                            if($historialContrato->id == $productoContrato->id){
                                //Se agrego el producto al contrato
                                $historialContrato->totalarmazones = $productoContrato->totalarmazones; //Asignar la cantidad de armazones compradas para el contrato
                                $tieneHistorial = true;
                            } else {
                                $historialContrato->totalarmazones = 0; //No coinciden los contratos - Total armazon para el contrato es 0
                            }
                            //Insertamos el registro al arreglo de resultado final
                            array_push($armazonContratos,$historialContrato);

                        }
                        //Fue un producto agregado posterior al contrato?
                        if(!$tieneHistorial){
                            //Este producto no se encuentra en el historial clinico del contrato pero fue agregado
                            $productoContrato->totalhistoriales = 0;    //Se asigna como total de producto en el historial clinico 0
                            //Insertamos el registro al arreglo de resultado final
                            array_push($armazonContratos,$productoContrato);
                        }
                    }
                }

                $totalRegistros = sizeof($armazonContratos);

                return view('administracion.reportes.tablareportearmazones', [
                    'armazones' => $armazones,
                    'armazonSeleccionada' => $id_armazonSeleccionada,
                    'totalPiezas' => $totalPiezas,
                    'totalPiezasVendidas' => $totalPiezasVendidas,
                    'totalPiezasRestantes' => $totalPiezasRestantes,
                    'totalRegistros' => $totalRegistros,
                    'armazonContratos' => $armazonContratos,
                    'idFranquicia' => $idFranquicia
                ]);

            } else {
                return back()->with("alerta","Selecciona un armazon valido.");
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    function listacontratospagadosseguimiento($idFranquicia){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7   || (Auth::user()->rol_id) == 8)) {
            //Rol ADMINISTADOR, PRINCIPAL, DIRECTOR

            //Por default extraera contratos liquidados entre 9 y 12 meses
            $opcion = 0;
            $zonaDefault = 0;
            $coloniaDefault = "";


            //Periodo de fechas
            $hoy = Carbon::now();
            $fechaInicio =  date("Y-m-d",strtotime($hoy."-10 month"));
            $fechaFinal = date("Y-m-d",strtotime($hoy."-9 month"));

            $franquicias = null;
            if(Auth::user()->rol_id == 7){
                $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000' ORDER BY f.ciudad ASC");
            }

            //Obtener contratos liquidados
            $contratosLiquidados = DB::select("SELECT UPPER(c.id) AS CONTRATO, UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.entrecallesentrega) AS ENTRECALLES,
                                                                    UPPER(c.coloniaentrega) AS COLONIA, UPPER(c.calleentrega) AS CALLE, UPPER(c.numeroentrega) AS NUMERO,
                                                                    UPPER(c.nombre) AS NOMBRE,UPPER(c.telefono) AS TELEFONO, c.created_at AS FECHAVENTA,
                                                                    (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS ZONA
                                                                    FROM contratos c
                                                                    WHERE c.estatus_estadocontrato IN (5)
                                                                    AND c.id_franquicia = '$idFranquicia'
                                                                    AND STR_TO_DATE(c.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicio','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d')
                                                                    ORDER BY ZONA ASC, STR_TO_DATE(c.created_at,'%Y-%m-%d') DESC");

            $zonas = DB::select("SELECT * FROM zonas z WHERE z.id_franquicia = '$idFranquicia' ORDER bY z.zona ASC");

            $colonias = DB::select("SELECT * FROM colonias c WHERE c.id_franquicia = '$idFranquicia' ORDER BY c.localidad ASC, c.colonia ASC");

            return view('administracion.reportes.tablareportepagadosseguimiento', [
                'idFranquicia' => $idFranquicia,
                'zonaDefault' => $zonaDefault,
                'franquicias' => $franquicias,
                'opcion' => $opcion,
                'contratosLiquidados' => $contratosLiquidados,
                'zonas' => $zonas,
                'colonias' => $colonias,
                'coloniaDefault' => $coloniaDefault
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    function filtrarlistacontratospagadosseguimiento($idFranquicia, Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7   || (Auth::user()->rol_id) == 8)) {
            //Rol ADMINISTADOR, PRINCIPAL, DIRECTOR

            $opcion = $request->input('periodoLiquidadoSeleccionado');
            $zonaSeleccionada = $request->input('zonaSeleccionadaSeguimiento');
            $coloniaSeleccionada = $request->input('coloniaSeleccionada');
            $zonaDefault = $zonaSeleccionada;
            $coloniaDefault = $coloniaSeleccionada;

            if($opcion == null || ($opcion < 0 || $opcion > 14)){
                return back()->with("alerta","Selecciona un periodo de contratos pagados.");
            }

            if($zonaSeleccionada == null){
                return back()->with("alerta","Selecciona la zona por la cual desees filtrar los contratos.");
            }

            if($coloniaSeleccionada == null){
                return back()->with("alerta","Selecciona la colonia por la cual desees filtrar los contratos.");
            }

            //Opcion seleccionada
            $hoy = Carbon::now();

            switch ($opcion){
                case 0:
                    //Opcion de 9 a 10 meses
                    $fechaInicio = date("Y-m-d",strtotime($hoy."-10 month"));
                    $fechaFinal =  date("Y-m-d",strtotime($hoy."-9 month"));
                    break;

                case 1:
                    //Opcion de 10 a 11 meses
                    $fechaInicio = date("Y-m-d",strtotime($hoy."-11 month"));
                    $fechaFinal =  date("Y-m-d",strtotime($hoy."-10 month"));
                    break;
                case 2:
                    //Opcion de 11 a 12 meses
                    $fechaInicio = date("Y-m-d",strtotime($hoy."-12 month"));
                    $fechaFinal =  date("Y-m-d",strtotime($hoy."-11 month"));
                    break;
                case 3:
                    //Opcion de 12 a 13 meses
                    $fechaInicio = date("Y-m-d",strtotime($hoy."-13 month"));
                    $fechaFinal =  date("Y-m-d",strtotime($hoy."-12 month"));
                    break;
                case 4:
                    //Opcion de 13 a 14 meses
                    $fechaInicio = date("Y-m-d",strtotime($hoy."-14 month"));
                    $fechaFinal =  date("Y-m-d",strtotime($hoy."-13 month"));
                    break;
                case 5:
                    //Opcion de 14 a 15 meses
                    $fechaInicio = date("Y-m-d",strtotime($hoy."-15 month"));
                    $fechaFinal =  date("Y-m-d",strtotime($hoy."-14 month"));
                    break;
                case 6:
                    //Opcion de 14 a 15 meses
                    $fechaInicio = date("Y-m-d",strtotime($hoy."-16 month"));
                    $fechaFinal =  date("Y-m-d",strtotime($hoy."-15 month"));
                    break;
                case 7:
                    //Opcion de 14 a 15 meses
                    $fechaInicio = date("Y-m-d",strtotime($hoy."-17 month"));
                    $fechaFinal =  date("Y-m-d",strtotime($hoy."-16 month"));
                    break;
                case 8:
                    //Opcion de 14 a 15 meses
                    $fechaInicio = date("Y-m-d",strtotime($hoy."-18 month"));
                    $fechaFinal =  date("Y-m-d",strtotime($hoy."-17 month"));
                    break;
                case 9:
                    //Opcion de 14 a 15 meses
                    $fechaInicio = date("Y-m-d",strtotime($hoy."-19 month"));
                    $fechaFinal =  date("Y-m-d",strtotime($hoy."-18 month"));
                    break;
                case 10:
                    //Opcion de 14 a 15 meses
                    $fechaInicio = date("Y-m-d",strtotime($hoy."-20 month"));
                    $fechaFinal =  date("Y-m-d",strtotime($hoy."-19 month"));
                    break;
                case 11:
                    //Opcion de 14 a 15 meses
                    $fechaInicio = date("Y-m-d",strtotime($hoy."-21 month"));
                    $fechaFinal =  date("Y-m-d",strtotime($hoy."-20 month"));
                    break;
                case 12:
                    //Opcion de 14 a 15 meses
                    $fechaInicio = date("Y-m-d",strtotime($hoy."-22 month"));
                    $fechaFinal =  date("Y-m-d",strtotime($hoy."-21 month"));
                    break;
                case 13:
                    //Opcion de 14 a 15 meses
                    $fechaInicio = date("Y-m-d",strtotime($hoy."-23 month"));
                    $fechaFinal =  date("Y-m-d",strtotime($hoy."-22 month"));
                    break;
                case 14:
                    //Opcion de 14 a 15 meses
                    $fechaInicio = date("Y-m-d",strtotime($hoy."-24 month"));
                    $fechaFinal =  date("Y-m-d",strtotime($hoy."-23 month"));
                    break;
            }

            //Rol de director
            $franquicias = null;
            if(Auth::user()->rol_id == 7){
                $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000' ORDER BY f.ciudad ASC");
                if($request->input('franquiciaSeleccionadaSeguimiento') != null){
                    $idFranquicia = $request->input('franquiciaSeleccionadaSeguimiento');
                }
            }

            $cadenaFiltroZona = "";
            if($zonaSeleccionada != 0){
                //Selecciono opcion deferente de Todas las zonas
                $cadenaFiltroZona = " AND c.id_zona = '$zonaSeleccionada'";
            }

            $cadenaColonia = "";
            if($coloniaSeleccionada != 0){
                $cadenaColonia = " AND c.coloniaentrega = (SELECT col.colonia FROM colonias col WHERE col.indice = '" . $coloniaSeleccionada . "')";
            }

            //Obtener contratos liquidados
            $contratosLiquidados = DB::select("SELECT UPPER(c.id) AS CONTRATO, UPPER(c.localidadentrega) AS LOCALIDAD, UPPER(c.entrecallesentrega) AS ENTRECALLES,
                                                                    UPPER(c.coloniaentrega) AS COLONIA, UPPER(c.calleentrega) AS CALLE, UPPER(c.numeroentrega) AS NUMERO,
                                                                    UPPER(c.nombre) AS NOMBRE,UPPER(c.telefono) AS TELEFONO, c.created_at AS FECHAVENTA,
                                                                    (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS ZONA
                                                                    FROM contratos c
                                                                    WHERE c.estatus_estadocontrato IN (5)
                                                                    AND c.id_franquicia = '$idFranquicia'
                                                                    " . $cadenaFiltroZona . "
                                                                    " . $cadenaColonia . "
                                                                    AND STR_TO_DATE(c.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicio','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d')
                                                                    ORDER BY ZONA ASC, STR_TO_DATE(c.created_at,'%Y-%m-%d') DESC");

            $zonas = DB::select("SELECT * FROM zonas z WHERE z.id_franquicia = '$idFranquicia' ORDER bY z.zona ASC");

            $colonias = DB::select("SELECT * FROM colonias c WHERE c.id_franquicia = '$idFranquicia' " . $cadenaFiltroZona ." ORDER BY c.localidad ASC, c.colonia ASC");

            return view('administracion.reportes.tablareportepagadosseguimiento', [
                'idFranquicia' => $idFranquicia,
                'zonaDefault' => $zonaDefault,
                'franquicias' => $franquicias,
                'opcion' => $opcion,
                'contratosLiquidados' => $contratosLiquidados,
                'zonas' => $zonas,
                'colonias' => $colonias,
                'coloniaDefault' => $coloniaDefault
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function cargarlistazonasfranquicia(Request $request){
        $idFranquicia = $request->input('idFranquicia');
        $zonas = DB::select("SELECT * FROM zonas z WHERE z.id_franquicia = '$idFranquicia' ORDER bY z.zona ASC");
        $response = ['zonas' => $zonas];
        return response()->json($response);
    }
    public function cargarlistacoloniasfranquicia(Request $request){
        $idFranquicia = $request->input('idFranquicia');
        $idZonaSeleccionada = $request->input('idZona');

        $cadenaZona = "";
        if($idZonaSeleccionada != 0 && $idZonaSeleccionada != ""){
            $cadenaZona = " AND c.id_zona = '" . $idZonaSeleccionada . "'";
        }

        $colonias = DB::select("SELECT * FROM colonias c WHERE c.id_franquicia = '$idFranquicia'" . $cadenaZona . " ORDER BY c.localidad ASC, c.colonia ASC");

        $response = ['colonias' => $colonias];

        return response()->json($response);
    }

    function listareporteproductos($idFranquicia){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7   || (Auth::user()->rol_id) == 8)) {
            //Rol ADMINISTADOR, PRINCIPAL, DIRECTOR

            //Rango de fechas para filtro por default
            $hoy = Carbon::now();
            $numeroDia = $hoy->dayOfWeekIso;
            $fechaInicial = Carbon::parse($hoy)->format('Y-m-d');   //Por defaul carga dia actual

            if($numeroDia != 1){
                //Dia de hoy es diferente de lunes
                $polizaGlobales = new polizaGlobales();
                $fechaInicial = $polizaGlobales::obtenerDia($numeroDia, 1);   //Obtener fecha del lunes anterior
            }
            //Fecha final siempre sera dia actual
            $fechaFinal = Carbon::parse($hoy)->format('Y-m-d');

            //Validacion de usuario
            $rol = Auth::user()->rol_id;
            $franquicias = null;

            switch ($rol){
                case 7:
                    //DIRECTOR
                    $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000' ORDER BY f.ciudad ASC");

                    //Lista de solicitudes de armazones de todas las sucursales
                    $solicitudesArmazones = DB::select("SELECT a.indice,(SELECT f.ciudad FROM franquicias f WHERE f.id = a.id_franquicia) AS sucursal, a.tipo, a.estatus, a.id_contrato,
                                                                a.fechacreacioncontrato AS fecha_contrato, a.created_at AS fecha_solicitud, a.estadocontrato AS estado_contrato,
                                                                a.mensaje as mensaje,(SELECT u.name FROM users u WHERE u.id = a.id_usuarioC) AS usuario_solicitud
                                                                FROM autorizaciones a
                                                                WHERE a.tipo IN (8,9,10) AND a.id_franquicia != '00000'
                                                                AND  STR_TO_DATE(a.created_at,'%Y-%m-%d') BETWEEN '$fechaInicial' AND '$fechaFinal'
                                                                ORDER BY sucursal ASC, fecha_solicitud DESC");
                    break;
                case 6:
                    //ADMINISTRACION
                case 8:
                    //PRINCIPAL
                    //Lista de solicitudes de armazones de sucursal asignada a usuario
                    $solicitudesArmazones = DB::select("SELECT a.indice,(SELECT f.ciudad FROM franquicias f WHERE f.id = a.id_franquicia) AS sucursal, a.tipo, a.estatus, a.id_contrato,
                                                                a.fechacreacioncontrato AS fecha_contrato, a.created_at AS fecha_solicitud, a.estadocontrato AS estado_contrato,
                                                                a.mensaje as mensaje,(SELECT u.name FROM users u WHERE u.id = a.id_usuarioC) AS usuario_solicitud
                                                                FROM autorizaciones a
                                                                WHERE a.tipo IN (8,9,10) AND a.id_franquicia = '$idFranquicia'
                                                                AND  STR_TO_DATE(a.created_at,'%Y-%m-%d') BETWEEN '$fechaInicial' AND '$fechaFinal'
                                                                ORDER BY fecha_solicitud DESC");
                    break;
            }


            return view('administracion.reportes.tablareporteproductos', [
                'idFranquicia' => $idFranquicia,  'franquicias' => $franquicias, 'fechaInicial' => $fechaInicial, 'fechaFinal' => $fechaFinal,
                'solicitudesArmazones' => $solicitudesArmazones, 'franquiciaSeleccionada' => ""
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    function listareporteproductosfiltrar($idFranquicia, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) {
            //Rol ADMINISTADOR, PRINCIPAL, DIRECTOR

            $franquiciaSeleccionada = $request->input('franquiciaSeleccionada');
            $fechaInicial = $request->input('fechaInicio');
            $fechaFinal = $request->input('fechaFin');
            $franquicias = null;
            $cadenaFiltrarSucursal = "";

            //Validaciones de efchas para el filtro
            $hoy = Carbon::now();
            if(strlen($fechaInicial) == 0){
                $fechaInicial = $hoy;
            }
            if(strlen($fechaFinal) == 0){
                $fechaFinal = $hoy;
            }

            if(Carbon::parse($fechaInicial)->format('Y-m-d') > Carbon::parse($fechaFinal)->format('Y-m-d')){
                return back()->with('alerta',"La 'Fecha inicial' debe ser menor o igual a 'Fecha final'.");
            }

            if(Auth::user()->rol_id == 7){
                //Director
                $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000' ORDER BY f.ciudad ASC");

                //Validar sucursal seleccionada
                if($franquiciaSeleccionada != null){
                    //Selecciono una sucursal
                    $cadenaFiltrarSucursal = " AND a.id_franquicia = '" . $franquiciaSeleccionada ."'";

                }

            }else{
                //Administracion, Principal
                $cadenaFiltrarSucursal = " AND a.id_franquicia = '" . $idFranquicia ."'";
                $franquiciaSeleccionada = $idFranquicia;
            }

            $solicitudesArmazones = DB::select("SELECT a.indice,(SELECT f.ciudad FROM franquicias f WHERE f.id = a.id_franquicia) AS sucursal, a.tipo, a.estatus, a.id_contrato,
                                                                a.fechacreacioncontrato AS fecha_contrato, a.created_at AS fecha_solicitud, a.estadocontrato AS estado_contrato,
                                                                a.mensaje as mensaje,(SELECT u.name FROM users u WHERE u.id = a.id_usuarioC) AS usuario_solicitud
                                                                FROM autorizaciones a
                                                                WHERE a.tipo IN (8,9,10)
                                                                " . $cadenaFiltrarSucursal . "
                                                                AND  STR_TO_DATE(a.created_at,'%Y-%m-%d') BETWEEN '$fechaInicial' AND '$fechaFinal'
                                                                ORDER BY sucursal ASC, fecha_solicitud DESC");

            return view('administracion.reportes.tablareporteproductos', [
                'idFranquicia' => $idFranquicia, 'franquicias' => $franquicias, 'fechaInicial' => $fechaInicial, 'fechaFinal' => $fechaFinal,
                'solicitudesArmazones' => $solicitudesArmazones, 'franquiciaSeleccionada' => $franquiciaSeleccionada
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    function reportemovimientoscontratos($idFranquicia){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) {
            //Rol ADMINISTADOR, PRINCIPAL, DIRECTOR

            //Valores por default a checkbox
            $cbLioFuga = null;
            $cbCorteLlamada = null;
            $cbAutorizacion = null;
            $cbProducto = null;
            $cbMovimientoManual = null;
            $cbGeneral = null;

            //Fechas
            $hoy = Carbon::now();
            $fechaInicial = Carbon::parse($hoy)->format('Y-m-d');
            $fechaFinal = Carbon::parse($hoy)->format('Y-m-d');

            $franquicias = null;
            $usuarioSeleccionado = null;
            $movimientosContratos = array();

            if(Auth::user()->rol_id == 7){
                //DIRECTOR
                $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000' ORDER BY f.ciudad ASC");
            }

            $usuarios = DB::select("SELECT u.id, u.name, (SELECT r.rol FROM roles r WHERE r.id = u.rol_id) AS rol FROM users u
                                         INNER JOIN usuariosfranquicia uf  ON uf.id_usuario = u.id WHERE uf.id_franquicia = '$idFranquicia' ORDER BY u.name ASC, rol ASC");

            return view('administracion.reportes.tablareportemovimientoscontratos', [
                'idFranquicia' => $idFranquicia, 'franquicias' => $franquicias, 'fechaInicial' => $fechaInicial, 'fechaFinal' => $fechaFinal,
                'movimientosContratos' => $movimientosContratos, 'usuarios' => $usuarios, 'usuarioSeleccionado' => $usuarioSeleccionado,
                'cbLioFuga' => $cbLioFuga, 'cbCorteLlamada' => $cbCorteLlamada, 'cbAutorizacion' => $cbAutorizacion, 'cbProducto' => $cbProducto,
                'cbMovimientoManual' => $cbMovimientoManual, 'cbGeneral' => $cbGeneral
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    function reportemovimientoscontratosfiltrar($idFranquicia, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) {
            //Rol ADMINISTADOR, PRINCIPAL, DIRECTOR

            $franquiciaSeleccionada = $request->input('franquiciaSeleccionada');
            $usuarioSeleccionado = $request->input('usuarioSeleccionado');
            $fechaInicial = $request->input('fechaInicio');
            $fechaFinal = $request->input('fechaFin');
            $cbLioFuga = $request->input('cbLioFuga');
            $cbCorteLlamada = $request->input('cbCorteLlamada');
            $cbAutorizacion = $request->input('cbAutorizacion');
            $cbProducto = $request->input('cbProducto');
            $cbMovimientoManual = $request->input('cbMovimientoManual');
            $cbGeneral = $request->input('cbGeneral');
            $franquicias = null;
            $cadenaFiltrarMovimiento = "";
            $cadenaFiltrarUsuario = "";
            $cadenaSucursal = "";

            //Validaciones de efchas para el filtro
            $hoy = Carbon::now();
            if(strlen($fechaInicial) == 0){
                $fechaInicial = $hoy;
            }
            if(strlen($fechaFinal) == 0){
                $fechaFinal = $hoy;
            }

            if(Carbon::parse($fechaInicial)->format('Y-m-d') > Carbon::parse($fechaFinal)->format('Y-m-d')){
                return back()->with('alerta',"La 'Fecha inicial' debe ser menor o igual a 'Fecha final'.");
            }

            if(Auth::user()->rol_id == 7){
                //Director
                $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000' ORDER BY f.ciudad ASC");

                //Validar sucursal seleccionada
                if($franquiciaSeleccionada != null){
                    //Selecciono una sucursal
                    $idFranquicia = $franquiciaSeleccionada;
                    $cadenaSucursal = " AND c.id_franquicia = '$idFranquicia' ";
                }

            }else{
                //Rol Admin o Principal
                $cadenaSucursal = " AND c.id_franquicia = '$idFranquicia' ";
            }

            if($usuarioSeleccionado != null){
                //Se selecciono un usuario
                $cadenaFiltrarUsuario = " AND h.id_usuarioC = '" . $usuarioSeleccionado ."'";
            }

            //Validar checkbox seleccionados para filtro
            if($cbLioFuga == null && $cbCorteLlamada == null && $cbAutorizacion == null && $cbProducto == null && $cbMovimientoManual == null && $cbGeneral == null){
                //Todos los checkbox  sin seleccionar
                $cadenaFiltrarMovimiento = " AND h.tipomensaje IN (0,1,2,3,4,5,6) ";
                $cbLioFuga = 1;
                $cbCorteLlamada = 1;
                $cbAutorizacion = 1;
                $cbProducto = 1;
                $cbMovimientoManual = 1;
                $cbGeneral = 1;
            }else{
                //Se selecciono al menos una opcion
                $tiposMovimientos = "";
                if($cbLioFuga != null){
                    $tiposMovimientos = "1,6,";
                }
                if($cbCorteLlamada != null){
                    $tiposMovimientos = $tiposMovimientos . "2,";
                }
                if($cbAutorizacion != null){
                    $tiposMovimientos = $tiposMovimientos . "3,";
                }
                if($cbProducto != null){
                    $tiposMovimientos = $tiposMovimientos . "4,";
                }
                if($cbMovimientoManual != null){
                    $tiposMovimientos = $tiposMovimientos . "5,";
                }
                if($cbGeneral != null){
                    $tiposMovimientos = $tiposMovimientos . "0";
                }
                //Quitar comas sobrantes
                $tiposMovimientos = trim($tiposMovimientos,",");
                $cadenaFiltrarMovimiento = " AND h.tipomensaje IN (".$tiposMovimientos .") ";
            }

            $movimientosContratos = DB::select("SELECT h.id_contrato, c.created_at as fechacontrato, h.created_at as fechamovimiento, u.name, h.cambios, c.nota,
                                                        (SELECT f.ciudad FROM franquicias f WHERE f.id = c.id_franquicia) as sucursal
                                                        from historialcontrato h
                                                        inner join users u on h.id_usuarioC = u.id
                                                        inner join contratos c on c.id = h.id_contrato
                                                         WHERE STR_TO_DATE(h.created_at,'%Y-%m-%d') BETWEEN '$fechaInicial' AND '$fechaFinal'
                                                         " . $cadenaSucursal . "
                                                         " . $cadenaFiltrarMovimiento . "
                                                         " . $cadenaFiltrarUsuario . "
                                                         ORDER BY h.created_at desc");

            $usuarios = DB::select("SELECT u.id, u.name, (SELECT r.rol FROM roles r WHERE r.id = u.rol_id) AS rol FROM users u
                                         INNER JOIN usuariosfranquicia uf  ON uf.id_usuario = u.id WHERE uf.id_franquicia = '$idFranquicia' ORDER BY u.name ASC, rol ASC");

            return view('administracion.reportes.tablareportemovimientoscontratos', [
                'idFranquicia' => $idFranquicia, 'franquicias' => $franquicias, 'fechaInicial' => $fechaInicial, 'fechaFinal' => $fechaFinal,
                'movimientosContratos' => $movimientosContratos, 'usuarios' => $usuarios, 'usuarioSeleccionado' => $usuarioSeleccionado,
                'cbLioFuga' => $cbLioFuga, 'cbCorteLlamada' => $cbCorteLlamada, 'cbAutorizacion' => $cbAutorizacion, 'cbProducto' => $cbProducto,
                'cbMovimientoManual' => $cbMovimientoManual, 'cbGeneral' => $cbGeneral
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    function reporteabonossucursal($idFranquicia)
    {
        if (Auth::check() && (Auth::user()->rol_id) == 7) {
            //Rol DIRECTOR

            $cbTipoAbono = 1;
            $hoy = Carbon::parse(Carbon::now())->format("Y-m-d");
            $fechaActual = new \DateTime();
            $fechaQuincena = new DateTime($fechaActual->format('Y-m-15'));

            if(Carbon::parse($hoy)->format("Y-m-d") <= Carbon::parse($fechaQuincena)->format("Y-m-d") ){
                //Dia actual cae en primer quincena del mes
                $fechaInicio =  new DateTime($fechaActual->format('Y-m-01'));
                $fechaFin =  new DateTime($fechaActual->format('Y-m-16'));
            }else{
                //Dia actual cae en segunda quincena del mes
                $fechaInicio =  new DateTime($fechaActual->format('Y-m-16'));
                $fechaFin =  new DateTime($fechaActual->format('Y-m-t'));
                $fechaFin->add(new \DateInterval('P1D'));

            }

            //Dar formato a fecha
            $fechaInicio = Carbon::parse($fechaInicio)->format("Y-m-d");
            $fechaFin = Carbon::parse($fechaFin)->format("Y-m-d");
            $quincena = "DE " . $fechaInicio . " A " . $fechaFin;

            $query = "SELECT
                            c.id, c.localidad, c.colonia, c.calle, c.numero, c.nombre, c.telefono,
                            a.abono, a.created_at, t.DESCRIPCION, a.tipoabono,
                            CASE
                                WHEN a.tipoabono = '7' THEN p.nombre
                                ELSE NULL
                            END as producto,
                            CASE
                                WHEN a.tipoabono = '7' THEN p.color
                                ELSE NULL
                            END as color
                            FROM abonos a
                            INNER JOIN contratos c ON c.id = a.id_contrato
                            INNER JOIN tipoabono t ON t.id = a.tipoabono
                            LEFT JOIN contratoproducto cp ON cp.id = a.id_contratoproducto
                            LEFT JOIN producto p ON p.id = cp.id_producto
                            WHERE
                                a.id_franquicia = '$idFranquicia'
                                AND a.created_at
                                BETWEEN STR_TO_DATE('$fechaInicio','%Y-%m-%d') AND STR_TO_DATE('$fechaFin','%Y-%m-%d')
                            ORDER BY a.created_at DESC";

            $abonos = DB::select($query);

            $franquicias = DB::select("SELECT f.id, f.ciudad FROM franquicias f WHERE f.id != '00000' ORDER BY f.ciudad ASC");

            return view('administracion.reportes.tablareporteabonos', [
                'idFranquicia' => $idFranquicia, 'franquiciaSeleccionada' => $idFranquicia,'fechaInicio' => $fechaInicio, 'fechaFin' => $fechaFin,
                'quincena' => $quincena, 'abonos' => $abonos, 'cbTipoAbono' => $cbTipoAbono, 'franquicias' => $franquicias, 'fechaSeleccionada' => $hoy
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function filtrarreporteabonossucursal($idFranquicia, Request $request){

        $fechaMinima = Carbon::parse("2023-09-01")->format("Y-m-d");

        $validator = Validator::make($request->all(), [
            'franquiciaSeleccionada' => "required",
            'fechaSeleccionada' => "required|date|after_or_equal:$fechaMinima",
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('alerta',"Verifica los datos ingresados para el filtro.");
        }

        $franquiciaSeleccionada = $request->input('franquiciaSeleccionada');
        $fechaSeleccionada = $request->input('fechaSeleccionada');
        $cbTipoAbono = $request->input('cbTipoAbono');

        //Obtener quincena
        $mesSeleccionado = Carbon::parse($fechaSeleccionada)->format("m");

        $fechaSeleccionada = DateTime::createFromFormat('Y-m-d', $fechaSeleccionada);
        $fechaQuincena = new DateTime($fechaSeleccionada->format('Y-' . $mesSeleccionado .'-15'));

        if(Carbon::parse($fechaSeleccionada)->format("Y-m-d") <= Carbon::parse($fechaQuincena)->format("Y-m-d") ){
            //Dia seleccionado cae en primer quincena del mes
            $fechaInicio =  new DateTime($fechaSeleccionada->format('Y-'.$mesSeleccionado.'-01'));
            $fechaFin =  new DateTime($fechaSeleccionada->format('Y-'.$mesSeleccionado.'-16'));
        }else{
            //Dia actual cae en segunda quincena del mes
            $fechaInicio =  new DateTime($fechaSeleccionada->format('Y-'.$mesSeleccionado.'-16'));
            $fechaFin =  new DateTime($fechaSeleccionada->format('Y-'.$mesSeleccionado.'-t'));
            $fechaFin->add(new \DateInterval('P1D'));
        }

        //Dar formato a fecha
        $fechaInicio = Carbon::parse($fechaInicio)->format("Y-m-d");
        $fechaFin = Carbon::parse($fechaFin)->format("Y-m-d");
        $quincena = "DE " . $fechaInicio . " A " . $fechaFin;

        if($cbTipoAbono != 1){
            //cbTipoabono no checked - Traer abonos que no son de tipo producto poliza
            $query = "SELECT
                            c.id, c.localidad, c.colonia, c.calle, c.numero, c.nombre, c.telefono,
                            a.abono, a.created_at, t.DESCRIPCION, a.tipoabono,
                            CASE
                                WHEN a.tipoabono = '7' THEN p.nombre
                                ELSE NULL
                            END as producto,
                            CASE
                                WHEN a.tipoabono = '7' THEN p.color
                                ELSE NULL
                            END as color
                            FROM abonos a
                            INNER JOIN contratos c ON c.id = a.id_contrato
                            INNER JOIN tipoabono t ON t.id = a.tipoabono
                            LEFT JOIN contratoproducto cp ON cp.id = a.id_contratoproducto
                            LEFT JOIN producto p ON p.id = cp.id_producto
                            WHERE
                                a.id_franquicia = '$franquiciaSeleccionada'
                                AND a.created_at BETWEEN STR_TO_DATE('$fechaInicio','%Y-%m-%d') AND STR_TO_DATE('$fechaFin','%Y-%m-%d')
                                AND (p.id_tipoproducto IS NULL OR p.id_tipoproducto != '2') -- Agregar esta condición
                        ORDER BY a.created_at DESC";

            $abonos = DB::select($query);

        }else{
            //Traer abonos en general dentro del rango de fechas
            $query = "SELECT
                            c.id, c.localidad, c.colonia, c.calle, c.numero, c.nombre, c.telefono,
                            a.abono, a.created_at, t.DESCRIPCION, a.tipoabono,
                            CASE
                                WHEN a.tipoabono = '7' THEN p.nombre
                                ELSE NULL
                            END as producto,
                            CASE
                                WHEN a.tipoabono = '7' THEN p.color
                                ELSE NULL
                            END as color
                            FROM abonos a
                            INNER JOIN contratos c ON c.id = a.id_contrato
                            INNER JOIN tipoabono t ON t.id = a.tipoabono
                            LEFT JOIN contratoproducto cp ON cp.id = a.id_contratoproducto
                            LEFT JOIN producto p ON p.id = cp.id_producto
                            WHERE
                                a.id_franquicia = '$franquiciaSeleccionada'
                                AND a.created_at BETWEEN STR_TO_DATE('$fechaInicio','%Y-%m-%d') AND STR_TO_DATE('$fechaFin','%Y-%m-%d')
                            ORDER BY a.created_at DESC";

            $abonos = DB::select($query);

        }

        $franquicias = DB::select("SELECT f.id, f.ciudad FROM franquicias f WHERE f.id != '00000' ORDER BY f.ciudad ASC");

        //Registrar fecha seleccionada a String
        $fechaSeleccionada = Carbon::parse($fechaSeleccionada)->format('Y-m-d');

        return view('administracion.reportes.tablareporteabonos', [
            'idFranquicia' => $idFranquicia, 'franquiciaSeleccionada' => $franquiciaSeleccionada,'fechaInicio' => $fechaInicio, 'fechaFin' => $fechaFin,
            'quincena' => $quincena, 'abonos' => $abonos, 'cbTipoAbono' => $cbTipoAbono, 'franquicias' => $franquicias, 'fechaSeleccionada' => $fechaSeleccionada
        ]);

    }

    public function reportecontratossupervision($idFranquicia){
        if (Auth::check() && (Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)) {
            //Rol DIRECTOR, ADMINISTRACION, PRINCIPAL

        $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000' ORDER BY f.ciudad ASC");

        $contratosSupervision = DB::select("SELECT c.id, (SELECT f.ciudad FROM franquicias f WHERE f.id = c.id_franquicia) AS sucursal, c.created_at,
                                                  c.nombre, c.telefono, c.calleentrega, c.numeroentrega, c.localidadentrega, c.numeroentrega,
                                                  c.coloniaentrega, a.created_at AS fechaReporte, DATEDIFF(CURRENT_DATE() , a.created_at) AS diasReporte, a.mensaje,
                                                  (SELECT u.name FROM users u WHERE u.id = a.id_usuarioC) AS usuario_solicitud
                                                  FROM contratos c
                                                  INNER JOIN autorizaciones a ON a.id_contrato = c.id
                                                  WHERE c.estatus_estadocontrato = 15 AND c.id_franquicia = '$idFranquicia' ORDER BY sucursal, fechaReporte DESC, c.created_at DESC");

            $contratosSupervisionMapa = DB::select("SELECT c.id AS CONTRATO, c.nombre AS NOMBRE, c.telefono AS TELEFONO, c.calleentrega AS CALLE, c.numeroentrega AS NUMERO,
                                                  c.localidadentrega AS LOCALIDAD, c.coloniaentrega AS COLONIA, c.estatus_estadocontrato AS ESTATUS_ESTADOCONTRATO,
                                                  c.coordenadas AS COORDENADAS, (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS
                                                  FROM contratos c
                                                  INNER JOIN autorizaciones a ON a.id_contrato = c.id
                                                  WHERE c.estatus_estadocontrato = 15 AND c.id_franquicia = '$idFranquicia' ORDER BY c.created_at DESC");

        return view('administracion.reportes.tablareportesupervisioncontratos', [
            'idFranquicia' => $idFranquicia, 'franquiciaSeleccionada' => $idFranquicia, 'franquicias' => $franquicias,
            'contratosSupervision' => $contratosSupervision, 'contratosSupervisionMapa' => $contratosSupervisionMapa
        ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function reportecontratossupervisionfiltrar($idFranquicia, Request $request){
        if (Auth::check() && (Auth::user()->rol_id) == 7) {
            //Rol DIRECTOR

            $validator = Validator::make($request->all(), [
                'franquiciaSeleccionada' => "required",
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with('alerta',"Selecciona una sucursal correcta.");
            }

            $franquiciaSeleccionada = $request->input('franquiciaSeleccionada');

            $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000' ORDER BY f.ciudad ASC");

            $contratosSupervision = DB::select("SELECT c.id, (SELECT f.ciudad FROM franquicias f WHERE f.id = c.id_franquicia) AS sucursal, c.created_at,
                                                  c.nombre, c.telefono, c.calleentrega, c.numeroentrega, c.localidadentrega, c.numeroentrega,
                                                  c.coloniaentrega, a.created_at AS fechaReporte, DATEDIFF(CURRENT_DATE() , a.created_at) AS diasReporte, a.mensaje,
                                                  (SELECT u.name FROM users u WHERE u.id = a.id_usuarioC) AS usuario_solicitud
                                                  FROM contratos c
                                                  INNER JOIN autorizaciones a ON a.id_contrato = c.id
                                                  WHERE c.estatus_estadocontrato = 15 AND c.id_franquicia = '$franquiciaSeleccionada' ORDER BY sucursal, fechaReporte DESC, c.created_at DESC");

            $contratosSupervisionMapa = DB::select("SELECT c.id AS CONTRATO, c.nombre AS NOMBRE, c.telefono AS TELEFONO, c.calleentrega AS CALLE, c.numeroentrega AS NUMERO,
                                                  c.localidadentrega AS LOCALIDAD, c.coloniaentrega AS COLONIA, c.estatus_estadocontrato AS ESTATUS_ESTADOCONTRATO,
                                                  c.coordenadas AS COORDENADAS, (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as ESTATUS
                                                  FROM contratos c
                                                  INNER JOIN autorizaciones a ON a.id_contrato = c.id
                                                  WHERE c.estatus_estadocontrato = 15 AND c.id_franquicia = '$franquiciaSeleccionada' ORDER BY c.created_at DESC");

            return view('administracion.reportes.tablareportesupervisioncontratos', [
                'idFranquicia' => $idFranquicia, 'franquiciaSeleccionada' => $franquiciaSeleccionada, 'franquicias' => $franquicias,
                'contratosSupervision' => $contratosSupervision, 'contratosSupervisionMapa' => $contratosSupervisionMapa
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }
}
