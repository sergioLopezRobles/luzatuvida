<?php

namespace App\Http\Controllers\Dominios\Administracion;

use App\Clases\calculofechaspago;
use App\Clases\contratosGlobal;
use App\Clases\globales;
use App\Clases\globalesServicioWeb;
use App\Clases\polizaGlobales;
use App\Http\Controllers\Exception;
use App\Http\Controllers\Log;
use App\Imports\CsvImport;
use App\Mail\prueba;
use App\Mail\recordatorio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Image;
use Maatwebsite\Excel\Facades\Excel;
use NumberFormatter;
use phpDocumentor\Reflection\Types\Array_;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class contratos extends Controller
{
    /* Metodo/Funcion: listacontrato
    Descripcion: Carga la lista de contratos, ademas hace validaciones para actualizar los estados de los contratos por si se salen
    si realizar las modificaciones necesarias.
    */
    public function listacontrato($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8)) {
            $contratoslaboratorio = null;
            $contratosconfirmaciones = null;
            $contratosreportesgarantia = null;
            $contratossupervision = null;
            $contratosnoenviados = null;
            $contratosgarantiascreadas = null;
            $cbGarantias = null;
            $cbSupervision = null;
            $cbAtrasado = null;
            $cbEntrega = null;
            $cbLaboratorio = null;
            $cbConfirmacion = null;
            $cbTodos = null;
            $zonaU = null;
            $fechainibuscar = null;
            $fechafinbuscar = null;

            $arrayCheckBox = array();

            $filtro = null;

            $now = Carbon::now();

            //Valores por default
            $cbGarantias = 1;
            $cbSupervision = 0;
            $cbAtrasado = 1;
            $cbEntrega = 1;
            $cbLaboratorio = 0;
            $cbConfirmacion = 0;
            $cbTodos = 0;
            $swBusquedaAvanzada = 0;
            $swBusquedaFiltro = 0;

            array_push($arrayCheckBox, $cbGarantias);
            array_push($arrayCheckBox, $cbSupervision);
            array_push($arrayCheckBox, $cbAtrasado);
            array_push($arrayCheckBox, $cbEntrega);
            array_push($arrayCheckBox, $cbLaboratorio);
            array_push($arrayCheckBox, $cbConfirmacion);
            array_push($arrayCheckBox, $cbTodos);
            array_push($arrayCheckBox, $zonaU);
            array_push($arrayCheckBox, $fechainibuscar);
            array_push($arrayCheckBox, $fechafinbuscar);
            array_push($arrayCheckBox, $swBusquedaAvanzada);

            $arrayContratos = self::obtenerListaContratosConOSinFiltro($idFranquicia, $filtro, $arrayCheckBox);

            $contratosprioritarios = $arrayContratos[0];
            $contratosatrasados = $arrayContratos[1];
            $contratosperiodo = $arrayContratos[2];
            $contratosentregar = $arrayContratos[3];
            $contratoslaboratorio = $arrayContratos[4];
            $contratosconfirmaciones = $arrayContratos[5];
            $contratos = $arrayContratos[6];
            $contratosreportesgarantia = $arrayContratos[7];
            $contratossupervision = $arrayContratos[8];
            $contratosnoenviados = $arrayContratos[9];
            $contratosgarantiascreadas = $arrayContratos[10];

            //Unir arreglos para contar registros sin repetir contratos
            $contratosGeneral = array_merge($contratosprioritarios, $contratosatrasados, $contratosperiodo, $contratosentregar, $contratoslaboratorio, $contratosconfirmaciones, $contratos,
                $contratosreportesgarantia, $contratossupervision, $contratosnoenviados, $contratosgarantiascreadas);
            $idContratos = [];

            //Extraemos el id de cada contrato
            foreach ($contratosGeneral as $contratoGeneral){
                array_push($idContratos, $contratoGeneral->id);
            }

            //Eliminamos contratos repetidos
            $idContratosUnicos = array_unique($idContratos);
            $totalRegistros = sizeof($idContratosUnicos);

            $zonas = DB::select("SELECT id,zona FROM zonas WHERE id_franquicia = '$idFranquicia'");
            $franquiciaContratos = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");
            return view('administracion.contrato.tabla', ['contratos' => $contratos, 'franquiciaContratos' => $franquiciaContratos,
                'contratosperiodo' => $contratosperiodo, 'now' => $now, 'contratosatrasados' => $contratosatrasados, 'contratosprioritarios' => $contratosprioritarios,
                'contratosentregar' => $contratosentregar, 'contratoslaboratorio' => $contratoslaboratorio, 'contratosconfirmaciones' => $contratosconfirmaciones,
                'contratosreportesgarantia' => $contratosreportesgarantia, 'contratossupervision' => $contratossupervision, 'contratosnoenviados' => $contratosnoenviados,
                'contratosgarantiascreadas' => $contratosgarantiascreadas, 'zonas' => $zonas, 'cbGarantias' => $cbGarantias, 'cbSupervision' => $cbSupervision, 'cbAtrasado' => $cbAtrasado,
                'cbEntrega' => $cbEntrega, 'cbLaboratorio' => $cbLaboratorio, 'cbConfirmacion' => $cbConfirmacion, 'cbTodos' => $cbTodos, 'zonaU' => $zonaU, 'idFranquicia' => $idFranquicia,
                'totalRegistros' => $totalRegistros, 'swBusquedaAvanzada' => $swBusquedaAvanzada, 'swBusquedaFiltro' => $swBusquedaFiltro]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    private static function obtenerListaContratosConOSinFiltro($idFranquicia, $filtro, $arrayCheckBox)
    {

        $now = Carbon::now();
        $arrayContratos = array();

        $contratosprioritarios = array();
        $contratosatrasados = array();
        $contratosperiodo = array();
        $contratosentregar = array();
        $contratoslaboratorio = array();
        $contratosconfirmaciones = array();
        $contratos = array();
        $contratosreportesgarantia = array();
        $contratossupervision = array();
        $contratosnoenviados = array();
        $contratosgarantiascreadas = array();


            $cbGarantias = $arrayCheckBox[0];
            $cbSupervision = $arrayCheckBox[1];
            $cbAtrasado = $arrayCheckBox[2];
            $cbEntrega = $arrayCheckBox[3];
            $cbLaboratorio = $arrayCheckBox[4];
            $cbConfirmacion = $arrayCheckBox[5];
            $cbTodos = $arrayCheckBox[6];
            $zonaU = $arrayCheckBox[7];
            $fechainibuscar = $arrayCheckBox[8];
            $fechafinbuscar = $arrayCheckBox[9];
            $swBusqueda = $arrayCheckBox[10];

            $cadenaZona = " ";
            if ($zonaU != null) {
               $cadenaZona = " AND id_zona = '$zonaU' ";
            }

            if ($fechainibuscar == null && $fechafinbuscar == null) {
                $fechainibuscar = Carbon::yesterday()->format('Y-m-d');
                $fechafinbuscar = Carbon::parse($now)->format('Y-m-d');
            }

            $cadenaFiltro = " ";
            $cadenaFechaIniYFechaFin = " ";
        switch ($swBusqueda){
            case 0:
                //BUSQUEDA RAPIDA
                if ($filtro != null) {
                    //Se buscara por filtro
                    $cadenaFiltro = " AND (id LIKE '%$filtro%' OR nombre_usuariocreacion LIKE '%$filtro%' OR nombre LIKE '%$filtro%'
                OR zona LIKE '%$filtro%' OR calle LIKE '%$filtro%' OR numero LIKE '%$filtro%' OR localidad LIKE '%$filtro%'
                OR total LIKE '%$filtro%' OR totalabono LIKE '%$filtro%' OR telefono LIKE '%$filtro%'
                OR colonia LIKE '%$filtro%' OR telefonoreferencia LIKE '%$filtro%' OR idcontratorelacion LIKE '%$filtro%'
                OR nombrereferencia LIKE '%$filtro%' OR alias LIKE '%$filtro%') ";
                } else {
                    //Se hace caso a las fechas
                    $cadenaFechaIniYFechaFin = " AND id IN (SELECT r.id_contrato  FROM registroestadocontrato r
                WHERE (STR_TO_DATE(r.created_at ,'%Y-%m-%d') >= STR_TO_DATE('$fechainibuscar','%Y-%m-%d')
                AND STR_TO_DATE(r.created_at ,'%Y-%m-%d') <= STR_TO_DATE('$fechafinbuscar','%Y-%m-%d'))
                AND r.estatuscontrato = estatus_estadocontrato)";
                }
                break;
            case 1:
                //BUSQUEDA AVANZADA
                if ($filtro != null) {
                    //Se buscara por filtro
                    $cadenaFiltro = " AND (c.id LIKE '%$filtro%' OR c.nombre_usuariocreacion LIKE '%$filtro%' OR c.nombre LIKE '%$filtro%'
                OR z.zona LIKE '%$filtro%' OR c.calle LIKE '%$filtro%' OR c.numero LIKE '%$filtro%' OR c.localidad LIKE '%$filtro%'
                OR c.total LIKE '%$filtro%' OR c.totalabono LIKE '%$filtro%' OR c.telefono LIKE '%$filtro%'
                OR c.colonia LIKE '%$filtro%' OR c.telefonoreferencia LIKE '%$filtro%' OR c.idcontratorelacion LIKE '%$filtro%'
                OR c.nombrereferencia LIKE '%$filtro%' OR c.alias LIKE '%$filtro%') ";
                } else {
                    //Se hace caso a las fechas
                    $cadenaFechaIniYFechaFin = " AND c.id IN (SELECT r.id_contrato  FROM registroestadocontrato r
                WHERE (STR_TO_DATE(r.created_at ,'%Y-%m-%d') >= STR_TO_DATE('$fechainibuscar','%Y-%m-%d')
                AND STR_TO_DATE(r.created_at ,'%Y-%m-%d') <= STR_TO_DATE('$fechafinbuscar','%Y-%m-%d'))
                AND r.estatuscontrato = c.estatus_estadocontrato)";
                }
                break;
        }


            if ($cbGarantias != null) {
                //cbGarantias esta checkeado

                $contratosgarantia = null;
                $garantiascreadas = null;

                switch ($swBusqueda){
                    case 0:
                        //BUSQUEDA RAPIDA
                        $contratosgarantia = DB::select("SELECT id, id_franquicia, estatus_estadocontrato, descripcion, idcontratorelacion, created_at, fechaentrega, fechaatraso, fechagarantia,
                                                nombre_usuariocreacion, zona, localidad, colonia, calle, numero, nombre, telefono, totalreal, estadogarantia, fechagarantia,
                                                totalproducto, totalpromocion, totalabono, total,
                                                IFNULL(ultimoabono,'') AS ultimoabono, promocionactiva as promo,
                                                (SELECT TIMESTAMPDIFF(DAY, fechaatraso ,'$now')) AS dias
                                                FROM contratoslistatemporales
                                                WHERE id_franquicia = '$idFranquicia'
                                                " . $cadenaFiltro . "
                                                " . $cadenaZona . "
                                                AND estadogarantia IN (0,1)
                                                AND estatus_estadocontrato IN (2,5,12,4)
                                                order by fechagarantia desc");
                        $garantiascreadas = DB::select("SELECT id, id_franquicia, estatus_estadocontrato, descripcion, idcontratorelacion, created_at, fechaentrega, fechaatraso, fechagarantia,
                                                nombre_usuariocreacion, zona, localidad, colonia, calle, numero, nombre, telefono, totalreal, estadogarantia, fechagarantia,
                                                totalproducto, totalpromocion, totalabono, total,
                                                IFNULL(ultimoabono,'') AS ultimoabono, promocionactiva as promo,
                                                (SELECT TIMESTAMPDIFF(DAY, fechaatraso ,'$now')) AS dias
                                                FROM contratoslistatemporales
                                                WHERE id_franquicia = '$idFranquicia'
                                                " . $cadenaFiltro . "
                                                " . $cadenaZona . "
                                                AND estadogarantia IN (2)
                                                AND estatus_estadocontrato IN (1)
                                                order by fechagarantia desc");
                        break;
                    case 1:
                        //BUSQUEDA AVANZADA
                        $contratosgarantia = DB::select("SELECT c.id,c.datos,c.id_franquicia,c.id_usuariocreacion,c.nombre_usuariocreacion,z.zona,c.nombre,c.calle,c.numero, c.estatus,
                                                c.totalpromocion, e.descripcion, c.diaseleccionado, c.fechaatraso, c.fechaentrega, c.pago, c.depto,c.alladode,c.frentea,c.entrecalles,c.colonia,c.localidad,c.telefono,
                                                c.casatipo,c.casacolor,c.created_at,c.updated_at, c.idcontratorelacion,  c.totalabono, c.estatus_estadocontrato, c.diaseleccionado,  c.fechacobrofin, c.fechacobroini,
                                                c.total, c.totalreal, g.estadogarantia as estadogarantia, g.created_at as fechagarantia, c.totalproducto, c.totalpromocion,
                                                COALESCE((SELECT a.created_at FROM abonos a WHERE a.id_contrato = c.id ORDER BY a.created_at DESC LIMIT 1), '') as ultimoabono,
                                                (SELECT p.estado FROM promocioncontrato p WHERE p.id_contrato = c.id AND p.id_franquicia = c.id_franquicia) AS promo,
                                                g.created_at AS fechagarantia, (SELECT TIMESTAMPDIFF(DAY, c.fechaatraso ,'$now')) AS dias
                                                FROM contratos c
                                                INNER JOIN zonas z
                                                ON z.id = c.id_zona
                                                INNER JOIN estadocontrato e
                                                ON e.estatus = c.estatus_estadocontrato
                                                INNER JOIN garantias g
                                                ON g.id_contrato = c.id
                                                WHERE c.datos = 1
                                                AND c.id_franquicia = '$idFranquicia'
                                                " . $cadenaFiltro . "
                                                " . $cadenaZona . "
                                                AND g.estadogarantia IN (0,1)
                                                AND c.estatus_estadocontrato IN (2,5,12,4)
                                                order by fechagarantia desc");
                        $garantiascreadas = DB::select("SELECT c.id,c.datos,c.id_franquicia,c.id_usuariocreacion,c.nombre_usuariocreacion,z.zona,c.nombre,c.calle,c.numero, c.estatus,
                                                c.totalpromocion, e.descripcion, c.diaseleccionado, c.fechaatraso, c.fechaentrega, c.pago, c.depto,c.alladode,c.frentea,c.entrecalles,c.colonia,c.localidad,c.telefono,
                                                c.casatipo,c.casacolor,c.created_at,c.updated_at, c.idcontratorelacion,  c.totalabono, c.estatus_estadocontrato, c.diaseleccionado,  c.fechacobrofin, c.fechacobroini,
                                                c.total, c.totalreal, g.estadogarantia as estadogarantia, g.created_at as fechagarantia, c.totalproducto, c.totalpromocion,
                                                COALESCE((SELECT a.created_at FROM abonos a WHERE a.id_contrato = c.id ORDER BY a.created_at DESC LIMIT 1), '') as ultimoabono,
                                                (SELECT p.estado FROM promocioncontrato p WHERE p.id_contrato = c.id AND p.id_franquicia = c.id_franquicia) AS promo,
                                                (SELECT TIMESTAMPDIFF(DAY, c.fechaatraso ,'$now')) AS dias
                                                FROM contratos c
                                                INNER JOIN zonas z
                                                ON z.id = c.id_zona
                                                INNER JOIN estadocontrato e
                                                ON e.estatus = c.estatus_estadocontrato
                                                INNER JOIN garantias g
                                                ON g.id_contrato = c.id
                                                WHERE c.datos = 1
                                                AND c.id_franquicia = '$idFranquicia'
                                                " . $cadenaFiltro . "
                                                " . $cadenaZona . "
                                                AND g.estadogarantia IN (2)
                                                AND c.estatus_estadocontrato IN (1)
                                                order by fechagarantia desc");
                        break;
                }

                $array = array(); //Bandera de ids contratos para que no se mande 2 veces
                if ($contratosgarantia != null) {
                    //Tiene contratos
                    foreach ($contratosgarantia as $contrato) {
                        //Recorrido contratosgarantia
                        $idContrato = $contrato->id;
                        if (!in_array($idContrato, $array)) {
                            array_push($contratosreportesgarantia, $contrato); //Agregacion del contrato al arreglo de contratosreportesgaratia
                            array_push($array, $idContrato); //Se agrega el id_contrato al array para que este no vuelva a insertarse de nuevo
                        }

                    }
                }

                $array = array(); //Bandera de ids contratos para que no se mande 2 veces
                if ($garantiascreadas != null) {
                    //Tiene contratos
                    foreach ($garantiascreadas as $contrato) {
                        //Recorrido $garantiascreadas
                        $idContrato = $contrato->id;

                        if (!in_array($idContrato, $array)) {
                            //No existe id_contrato en el arreglo $array
                            array_push($contratosgarantiascreadas, $contrato); //Agregacion del contrato al arreglo de contratosgarantiascreadas
                            array_push($array, $idContrato); //Se agrega el id_contrato al array para que este no vuelva a insertarse de nuevo
                        }

                    }
                }

            }

            $validacionEstados = "";
            if ($cbAtrasado != null || $cbTodos != null) {
                //Alguno de los 2 cb estan checkeados
                if ($cbAtrasado != null && $cbTodos != null) {
                    //Los 2 cb estan checkeados
                    $validacionEstados = "0,2,3,4,5,6,8,14";
                } else {
                    //1 de los 2 cb esta checkeado
                    if ($cbTodos != null) {
                        //cbTodos esta checkeado
                        $validacionEstados = "0,2,3,5,6,8,14";
                    } else {
                        //cbAtrasado esta checkeado
                        $validacionEstados = "4";
                    }
                }
            }

            $array = array(); //Bandera de ids contratos para que no se mande 2 veces
            if (strlen($validacionEstados) > 0) {
                //Alguno de los 2 cb (cbAtrasado o cbTodos) estan checkeados

                switch ($swBusqueda){
                    case 0:
                        //BUSQUEDA RAPIDA
                        $query = "SELECT id, id_franquicia, estatus_estadocontrato, descripcion, idcontratorelacion, created_at, fechaentrega, fechaatraso, fechagarantia,
                                                nombre_usuariocreacion, zona, localidad, colonia, calle, numero, nombre, telefono, totalreal, estadogarantia, fechagarantia,
                                                totalproducto, totalpromocion, totalabono, total,
                                                IFNULL(ultimoabono,'') AS ultimoabono, promocionactiva as promo,
                                                (SELECT TIMESTAMPDIFF(DAY, fechaatraso ,'$now')) AS dias
                                                FROM contratoslistatemporales
                                                WHERE estatus_estadocontrato IN (" . $validacionEstados . ")
                                                AND id_franquicia = '$idFranquicia'
                                                " . $cadenaFiltro . "
                                                " . $cadenaZona . "
                                                " . $cadenaFechaIniYFechaFin . "
                                                order by created_at, localidad, colonia, calle, numero, nombre DESC";
                        break;
                    case 1:
                        //BUSQUEDA AVANZADA
                        $query = "SELECT c.id,c.datos,c.id_franquicia,c.id_usuariocreacion,c.nombre_usuariocreacion,z.zona,c.nombre,c.calle,c.numero, c.estatus,  c.totalpromocion,
                                                e.descripcion, c.diaseleccionado, c.fechaatraso, c.fechaentrega, c.pago, c.depto,c.alladode,c.frentea,c.entrecalles,c.colonia,c.localidad,c.telefono,c.casatipo,
                                                c.casacolor,c.created_at,c.updated_at, c.idcontratorelacion,  c.totalabono, c.estatus_estadocontrato, c.diaseleccionado,  c.fechacobrofin, c.fechacobroini,  c.total, c.totalreal,
                                                COALESCE((SELECT a.created_at FROM abonos a WHERE a.id_contrato = c.id ORDER BY a.created_at DESC LIMIT 1), '') as ultimoabono, c.totalproducto, c.totalpromocion,
                                                (SELECT p.estado FROM promocioncontrato p WHERE p.id_contrato = c.id AND p.id_franquicia = c.id_franquicia) AS promo,
                                                (SELECT g.created_at FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS fechagarantia,
                                                (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia,
                                                (SELECT TIMESTAMPDIFF(DAY, c.fechaatraso ,'$now')) AS dias
                                                FROM contratos c
                                                INNER JOIN zonas z
                                                ON z.id = c.id_zona
                                                INNER JOIN estadocontrato e
                                                ON e.estatus = c.estatus_estadocontrato
                                                WHERE c.datos = 1
                                                AND c.estatus_estadocontrato IN (" . $validacionEstados . ")
                                                AND c.id_franquicia = '$idFranquicia'
                                                " . $cadenaFiltro . "
                                                " . $cadenaZona . "
                                                " . $cadenaFechaIniYFechaFin . "
                                                order by c.created_at, c.localidad, c.colonia, c.calle, c.numero, c.nombre DESC;
                                                ";
                        break;
                }

                $contratosTodos = DB::select($query);

                foreach ($contratosTodos as $contrato) {

                    $idContrato = $contrato->id;

                    if (!in_array($idContrato, $array)) {
                        //No existe id_contrato en el arreglo $array

                        //Contratos atrasados
                        if ($cbAtrasado != null) {
                            if ($contrato->total > 0 && $contrato->estatus_estadocontrato == 4) {
                                array_push($contratosatrasados, $contrato);
                                array_push($array, $idContrato); //Se agrega el id_contrato al array para que este no vuelva a insertarse de nuevo
                                continue;
                            }
                        }

                        //Contratos todos
                        if ($cbTodos != null) {
                            if ($contrato->estatus_estadocontrato != 4 && $contrato->estatus_estadocontrato != 12 && ($contrato->estatus_estadocontrato == 0 || $contrato->estatus_estadocontrato == 2
                                    || $contrato->estatus_estadocontrato == 3 || $contrato->estatus_estadocontrato == 5 || $contrato->estatus_estadocontrato == 6
                                    || $contrato->estatus_estadocontrato == 8 || $contrato->estatus_estadocontrato == 14)) {
                                array_push($contratos, $contrato);
                                array_push($array, $idContrato); //Se agrega el id_contrato al array para que este no vuelva a insertarse de nuevo
                            }
                        }

                    }

                }

            }

            $validacionEstados = "";
            if ($cbEntrega != null || $cbLaboratorio != null || $cbConfirmacion != null || $cbSupervision != null) {
                //Alguno de los 4 cb estan checkeados
                if ($cbEntrega != null && $cbLaboratorio != null && $cbConfirmacion != null && $cbSupervision != null) {
                    //Los 4 cb estan checkeados
                    $validacionEstados = "1,7,9,10,11,12,15";
                } else {
                    //Alguno de los 4 cb no esta checkeado
                    if ($cbLaboratorio != null) {
                        //cbLaboratorio esta checkeado
                        $validacionEstados = "7,10,11";
                        if ($cbConfirmacion != null) {
                            //cbConfirmacion esta checkeado
                            $validacionEstados = "1,7,9,10,11";
                            if ($cbEntrega != null) {
                                //cbEntrega esta checkeado
                                $validacionEstados = "1,7,9,10,11,12";
                                if ($cbSupervision != null) {
                                    //cbSupervision esta checkeado
                                    $validacionEstados = "1,7,9,10,11,12,15";
                                }
                            } else {
                                //cbSupervision esta checkeado
                                if ($cbSupervision != null) {
                                    //cbSupervision esta checkeado
                                    $validacionEstados = "1,7,9,10,11,15";
                                }
                            }
                        } else {
                            //cbConfirmacion no esta checkeado
                            if ($cbEntrega != null) {
                                //cbEntrega esta checkeado
                                $validacionEstados = "7,10,11,12";
                                if ($cbSupervision != null) {
                                    //cbSupervision esta checkeado
                                    $validacionEstados = "7,10,11,12,15";
                                }
                            } else {
                                //cbEntrega no esta checkeado
                                if ($cbSupervision != null) {
                                    //cbSupervision esta checkeado
                                    $validacionEstados = "7,10,11,15";
                                }
                            }
                        }
                    } else {
                        //cbLaboratorio no esta checkeado
                        if ($cbConfirmacion != null) {
                            //cbConfirmacion esta checkeado
                            $validacionEstados = "1,9";
                            if ($cbEntrega != null) {
                                //cbEntrega esta checkeado
                                $validacionEstados = "1,9,12";
                                if ($cbSupervision != null) {
                                    //cbSupervision esta checkeado
                                    $validacionEstados = "1,9,12,15";
                                }
                            } else {
                                //cbSupervision esta checkeado
                                if ($cbSupervision != null) {
                                    //cbSupervision esta checkeado
                                    $validacionEstados = "1,9,15";
                                }
                            }
                        } else {
                            //cbConfirmacion no esta checkeado
                            if ($cbEntrega != null) {
                                //cbEntrega esta checkeado
                                $validacionEstados = "12";
                                if ($cbSupervision != null) {
                                    //cbSupervision esta checkeado
                                    $validacionEstados = "12,15";
                                }
                            } else {
                                //cbEntrega no esta checkeado
                                if ($cbSupervision != null) {
                                    //cbSupervision esta checkeado
                                    $validacionEstados = "15";
                                }
                            }
                        }
                    }
                }
            }

            if (strlen($validacionEstados) > 0) {
                //Alguno de los 4 cb (cbEntrega, cbLaboratorio, cbConfirmacion, cbSupervision) estan checkeados

                switch ($swBusqueda){
                    case 0:
                        //BUSQUEDA RAPIDA
                        $consultacontratos = DB::select("SELECT id, id_franquicia, estatus_estadocontrato, descripcion, idcontratorelacion, created_at, fechaentrega, fechaatraso, fechagarantia,
                                                nombre_usuariocreacion, zona, localidad, colonia, calle, numero, nombre, telefono, totalreal, estadogarantia, fechagarantia,
                                                totalproducto, totalpromocion, totalabono, total,
                                                IFNULL(ultimoabono,'') AS ultimoabono, promocionactiva as promo,
                                                (SELECT TIMESTAMPDIFF(DAY, fechaatraso ,'$now')) AS dias
                                                FROM contratoslistatemporales
                                                WHERE id_franquicia = '$idFranquicia'
                                                " . $cadenaFiltro . "
                                                " . $cadenaZona . "
                                                AND estatus_estadocontrato IN (" . $validacionEstados . ")
                                                order by created_at,id_zona, localidad, colonia, calle, numero, nombre DESC");
                        break;
                    case 1:
                        //BUSQUEDA AVANZADA
                        $consultacontratos = DB::select("SELECT c.id,c.datos,c.id_franquicia,c.id_usuariocreacion,c.nombre_usuariocreacion,z.zona,c.nombre,c.calle,c.numero, c.estatus,
                                                c.totalpromocion, e.descripcion, c.diaseleccionado, c.fechaatraso, c.fechaentrega, c.pago, c.depto,c.alladode,c.frentea,c.entrecalles,c.colonia,c.localidad,c.telefono,
                                                c.casatipo,c.casacolor,c.created_at,c.updated_at, c.idcontratorelacion,  c.totalabono, c.estatus_estadocontrato, c.diaseleccionado,  c.fechacobrofin, c.fechacobroini,
                                                c.total, c.totalreal, c.totalproducto, c.totalpromocion,
                                                c.totalproducto, c.totalpromocion,
                                                COALESCE((SELECT a.created_at FROM abonos a WHERE a.id_contrato = c.id ORDER BY a.created_at DESC LIMIT 1), '') as ultimoabono,
                                                (SELECT p.estado FROM promocioncontrato p WHERE p.id_contrato = c.id AND p.id_franquicia = c.id_franquicia) AS promo,
                                                (SELECT g.created_at FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS fechagarantia,
                                                (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia,
                                                (SELECT TIMESTAMPDIFF(DAY, c.fechaatraso ,'$now')) AS dias
                                                FROM contratos c
                                                INNER JOIN zonas z
                                                ON z.id = c.id_zona
                                                INNER JOIN estadocontrato e
                                                ON e.estatus = c.estatus_estadocontrato
                                                WHERE c.datos = 1
                                                AND c.id_franquicia = '$idFranquicia'
                                                " . $cadenaFiltro . "
                                                " . $cadenaZona . "
                                                AND c.estatus_estadocontrato IN (" . $validacionEstados . ")
                                                order by c.created_at, c.localidad, c.colonia, c.calle, c.numero, c.nombre DESC;
                                                ");
                        break;
                }


                foreach ($consultacontratos as $contrato) {

                    $idContrato = $contrato->id;

                    //Contratos enviados
                    if ($cbEntrega != null) {
                        if ($contrato->estatus_estadocontrato == 12) {
                            if (Carbon::parse($now)->format('Y-m-d') > Carbon::parse($contrato->fechaentrega)->format('Y-m-d')) {
                                //Ya paso la fecha de entrega
                                array_push($contratosnoenviados, $contrato);
                            } else {
                                //No ha pasado la fecha de entrega
                                array_push($contratosentregar, $contrato);
                            }
                            continue;
                        }
                    }

                    //Contratos laboratorio
                    if ($cbLaboratorio != null) {
                        if ($contrato->estatus_estadocontrato == 7 || $contrato->estatus_estadocontrato == 10 || $contrato->estatus_estadocontrato == 11) {
                            array_push($contratoslaboratorio, $contrato);
                            continue;
                        }
                    }

                    //Contratos confirmaciones
                    if ($cbConfirmacion != null) {
                        if ($contrato->estatus_estadocontrato == 1 || $contrato->estatus_estadocontrato == 9) {
                            array_push($contratosconfirmaciones, $contrato);
                            continue;
                        }
                    }

                    //Contratos supervision
                    if ($cbSupervision != null) {
                        if ($contrato->estatus_estadocontrato == 15) {
                            array_push($contratossupervision, $contrato);
                        }
                    }

                }

            }


            array_push($arrayContratos, $contratosprioritarios);
            array_push($arrayContratos, $contratosatrasados);
            array_push($arrayContratos, $contratosperiodo);
            array_push($arrayContratos, $contratosentregar);
            array_push($arrayContratos, $contratoslaboratorio);
            array_push($arrayContratos, $contratosconfirmaciones);
            array_push($arrayContratos, $contratos);
            array_push($arrayContratos, $contratosreportesgarantia);
            array_push($arrayContratos, $contratossupervision);
            array_push($arrayContratos, $contratosnoenviados);
            array_push($arrayContratos, $contratosgarantiascreadas);


        return $arrayContratos;

    }

    private static function subquerysConsultaContratosLista($idContrato, $idFranquicia, $opcion)
    {
        $respuesta = "";

        switch ($opcion) {
            case 0:
                $respuesta = 0;
                $contrato = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
                if ($contrato[0]->idcontratorelacion == null) {
                    //Es un contrato padre
                    $promocioncontrato = DB::select("SELECT * FROM promocioncontrato WHERE id_franquicia = '$idFranquicia' AND id_contrato = '$idContrato'");

                    if ($promocioncontrato != null) {
                        if ($promocioncontrato[0]->estado == 1) {
                            //Promocion esta activa
                            $respuesta = 1;
                        }
                    }
                }
                break;
            case 1:
                $historialclinico = DB::select("SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = '$idContrato' ORDER BY hc.created_at LIMIT 1");
                if ($historialclinico != null) {
                    $respuesta = $historialclinico[0]->fechaentrega;
                }
                break;
        }

        return $respuesta;
    }

    /* Metodo/Funcion: getAbonosId
     Descripcion: Esta función revisa si el ID alfanumerico que crea la funcion random no esta repetido en la BD es decir busca que sea unico.
     */
    private function getAbonosId()
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $temporalId = $this->generadorRandom2();
            $existente = DB::select("select id from abonos where id = '$temporalId'");
            if (sizeof($existente) == 0) {
                $unico = $temporalId;
                $esUnico = true;
            }
        }
        return $unico;
    }

    /* Metodo/Funcion: getPromocionContratoId
    Descripcion: Esta función revisa si el ID alfanumerico que crea la funcion random no esta repetido en la BD es decir busca que sea unico.
    */
    private function getPromocionContratoId()
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $temporalId = $this->generadorRandom2();
            $existente = DB::select("select id from promocioncontrato where id = '$temporalId'");
            if (sizeof($existente) == 0) {
                $unico = $temporalId;
                $esUnico = true;
            }
        }
        return $unico;
    }

    /* Metodo/Funcion: getContratoContratoId
    Descripcion: Esta función revisa si el ID alfanumerico que crea la funcion random no esta repetido en la BD es decir busca que sea unico.
    */
    private function getContratoContratoId()
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $temporalId = $this->generadorRandom2();
            $existente = DB::select("select id from contratoproducto where id = '$temporalId'");
            if (sizeof($existente) == 0) {
                $unico = $temporalId;
                $esUnico = true;
            }
        }
        return $unico;
    }

    private static function getAbonosContratoId()
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $temporalId = self::generadorRandom2();
            $existente = DB::select("select id from abonos where id = '$temporalId'");
            if (sizeof($existente) == 0) {
                $unico = $temporalId;
                $esUnico = true;
            }
        }
        return $unico;
    }    // Comparar si ya existe el id en la base de datos


    /* Metodo/Funcion: getHistorialContratoId
    Descripcion: Esta función revisa si el ID alfanumerico que crea la funcion random no esta repetido en la BD es decir busca que sea unico.
    */
    private function getHistorialContratoId()
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $temporalId = self::generadorRandom2();
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
    private static function generadorRandom2($length = 5)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($caracteres);
        $randomId = '';
        for ($i = 0; $i < $length; $i++) {
            $randomId .= $caracteres[rand(0, $charactersLength - 1)];
        }
        return $randomId;
    }

    /* Metodo/Funcion: nuevocontrato
    Descripcion: Esta función  carga los datos necesarios para la vista de crear contratos
    */
    public function nuevocontrato($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12) || ((Auth::user()->rol_id) == 6)) {
            $idUsuario = Auth::user()->id;
            //Consulta para traer el ultimo optometrista que registro el usuario en su ultimo contrato
            $ultimoOptometrista = DB::select("SELECT
                                id_optometrista as ID,  u.name as NAME
                                from contratos c
                                inner join users u on c.id_optometrista = u.id
                                AND c.id_usuariocreacion = '$idUsuario'
                                order by c.created_at desc
                                limit 1");
            //Consulta para traer la ultima zona registrada por el usuario en su ultimo contrato
            $ultimaZona = DB::select("SELECT
            id_zona as ID,  z.zona as zona
            from contratos c
            inner join zonas z on c.id_zona = z.id
            AND c.id_usuariocreacion = '$idUsuario'
            order by c.created_at desc
            limit 1");
            //Consulta para llenar el campo de select de zonas en la vista para crear contratos
            $zonas = DB::select("SELECT id as ID, zona as zona FROM zonas where id_franquicia = '$idFranquicia'");
            //Consulta para llenar el campo de select de zonas en la vista para crear contratos
            $optometristas = DB::select("SELECT u.ID,u.NAME
                                FROM users u
                                INNER JOIN usuariosfranquicia uf
                                ON uf.id_usuario = u.id
                                WHERE uf.id_franquicia = '$idFranquicia'
                                AND u.rol_id = 12");
            return view('administracion.contrato.nuevo', ['idFranquicia' => $idFranquicia, 'zonas' => $zonas, 'optometristas' => $optometristas,
                             'ultimaZona' => $ultimaZona, 'ultimoOptometrista' => $ultimoOptometrista]);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    /* Metodo/Funcion: nuevocontrato2
    Descripcion: Esta función se manda a llamar en terminar contrato de los contratos padres con promocion o los que no tienen,
    se hacen lso calculos necsarios para los totales y mandara a llamar la creación de nuevos contratos si es necesario.
    */
    public function nuevocontrato2($idFranquicia, $idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12) || ((Auth::user()->rol_id) == 6)) {
            $idUsuario = Auth::user()->id;
            $ultimoOptometrista = DB::select("SELECT
                                id_optometrista as ID,  u.name as NAME
                                from contratos c
                                inner join users u on c.id_optometrista = u.id
                                AND c.id_usuariocreacion = '$idUsuario'
                                order by c.created_at desc
                                limit 1");
            $ultimaZona = DB::select("SELECT
            id_zona as ID,  z.zona as zona
            from contratos c
            inner join zonas z on c.id_zona = z.id
            AND c.id_usuariocreacion = '$idUsuario'
            order by c.created_at desc
            limit 1");
            $zonas = DB::select("SELECT id as ID, zona as zona FROM zonas where id_franquicia = '$idFranquicia'");
            $optometristas = DB::select("SELECT u.ID,u.NAME
                                FROM users u
                                INNER JOIN usuariosfranquicia uf
                                ON uf.id_usuario = u.id
                                WHERE uf.id_franquicia = '$idFranquicia'
                                AND u.rol_id = 12");
            //Consulta para traer los datos necesarios del contrato y con ello poder realizar divorsos movimientos en la vista y el controlador
            $contratos = DB::select("SELECT c.id,c.datos,c.id_franquicia,c.id_usuariocreacion,c.nombre_usuariocreacion,z.zona,c.nombre,c.calle,c.numero, c.pago, u.name,
            pr.titulo, pr.armazones, c.totalproducto, c.totalhistorial, c.totalpromocion, c.correo, c.estatus, c.depto,c.alladode,c.frentea,c.entrecalles,c.colonia,c.localidad,
            c.telefono,c.casatipo,c.casacolor,c.created_at,c.updated_at,c.id_optometrista, c.id_promocion, c.contador, pr.preciop, c.total, c.totalabono, c.nombrereferencia,
            pr.preciouno,pr.tipopromocion, c.telefonoreferencia,
            (SELECT COUNT(id) from promocioncontrato pc where pc.id_contrato = c.id) as promo
            FROM contratos c
            INNER JOIN zonas z
            ON z.id = c.id_zona
            INNER JOIN users u
            ON u.id = c.id_optometrista
			INNER JOIN promocion pr
			ON pr.id = c.id_promocion
            WHERE c.datos = 1
            AND c.id = '$idContrato'
            AND c.id_franquicia = '$idFranquicia'");
            //Consulta para traer los datos del tratamiento fotocromatico en la tabla de tratamientos
            $fotocromatico = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'fotocromático'");
            //Consulta para traer los datos del tratamiento A/R en la tabla de tratamientos
            $AR = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'A/R'");
            //Consulta para traer los datos del tratamiento Tinte en la tabla de tratamientos
            $tinte = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'tinte'");
            //Consulta para traer los datos del tratamiento Blueray en la tabla de tratamientos
            $blueray = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'BlueRay'");
            //Consulta para traer los datos de los paquetes en la tabla de paquetes
            $paquetes = DB::select("SELECT * FROM paquetes WHERE id_franquicia = '$idFranquicia'");
            //Consulta para traer los registros de los productos de tipo armazon para el select en la creacion de historialclinico
            $armazones = DB::select("SELECT * FROM producto WHERE  id_tipoproducto = '1' order by nombre");
            //Consulta de los datos del contrato
            $ct = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
            //Consulta de los datos de los historiales clinicos del contrato
            $historialesclinicos = DB::select("SELECT nombre, h.edad, h.fechaentrega, c.telefono, h.id_paquete, h.created_at, h.diagnostico FROM historialclinico h
            inner join contratos c
            on c.id = h.id_contrato
            WHERE id_contrato ='$idContrato'");
            //Consulta de los contratos hijos de una promocion que aun no esten terminados.
            $contrashijos = DB::select("SELECT * FROM contratos  WHERE id_franquicia = '$idFranquicia' AND idcontratorelacion = '$idContrato' AND estatus_estadocontrato = 0 limit 1");
            $Hi = DB::select("SELECT * FROM contratos  WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
            $Hi2 = DB::select("SELECT * FROM contratos  WHERE id_franquicia = '$idFranquicia' AND idcontratorelacion = '$idContrato'");
            $relacion = $Hi[0]->id;
            $idPadre = DB::select("SELECT * FROM contratos  WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
            $abonoproducto = DB::select("SELECT * FROM abonos  WHERE id_contrato = '$idContrato' AND tipoabono = '7'");
            $contratoproducto = DB::select("SELECT * FROM contratoproducto  WHERE id_franquicia = '$idFranquicia' AND id_contrato = '$idContrato'");
            $contratohijo = $ct[0]->idcontratorelacion;
            $contador = $ct[0]->contador;
            $totalhistorial = $ct[0]->totalhistorial;
            $totalabonos = $ct[0]->totalabono;
            $estatus = $ct[0]->estatus_estadocontrato;
            $totalproductos = $ct[0]->totalproducto;
            $promo = $ct[0]->id_promocion;
            $pago = $ct[0]->pago;
            $total = $ct[0]->total;
            if ($abonoproducto == null && $totalproductos > 0) {
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                 ->with('alerta', 'Los abonos de producto no concuerdan con el total de productos');
            }
            if ($abonoproducto != null && $abonoproducto[0]->abono != $totalproductos) {
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                 ->with('alerta', 'Los abonos de producto no concuerdan con el total de productos');
            }
            if ($totalabonos < $totalproductos) {
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                 ->with('alerta', 'Abonar el total de productos');
            }
            if ($pago === null) {
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                 ->with('alerta', 'Favor de elegir una forma de pago');
            }
            if ($contratohijo != null && $pago == 0 && $estatus >= 0) {
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $contratohijo])
                                 ->with('bien', 'El contrato se terminara al final de los demas, cuando tenga el costo promoción');
            }
            if ($abonoproducto != null && $contratoproducto == null) {
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                 ->with('alerta', 'El contrato tiene abono de producto y no hay productos registrados, favor de eliminarlos');
            }
            if ($contratohijo != null && $promo == null) {
                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                    'estatus' => 1, 'estatus_estadocontrato' => 1,
                ]);
                //Insertar en tabla registroestadocontrato
                DB::table('registroestadocontrato')->insert([
                    'id_contrato' => $idContrato,
                    'estatuscontrato' => 1,
                    'created_at' => Carbon::now()
                ]);
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $contratohijo])->with('bien', 'El contrato se termino correcatemente');
            }
            if ($historialesclinicos == null) {
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('alerta', 'Favor de llenar los historiales clinicos necesarios');
            }

            if ($pago == 0 && $total == 0 && $Hi2 == null) {

                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                    'estatus_estadocontrato' => 1,
                ]);
                //Insertar en tabla registroestadocontrato
                DB::table('registroestadocontrato')->insert([
                    'id_contrato' => $idContrato,
                    'estatuscontrato' => 1,
                    'created_at' => Carbon::now()
                ]);
                return redirect()->route('listacontrato', $idFranquicia)->with('bien', 'No hay contratos pendientes por completar');
            }
            if ($promo == null) {

                $contrato = DB::select("SELECT estatus_estadocontrato FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");

                if ($contrato != null) {
                    //Existe contrato

                    $vistaprincipal = true;
                    if ($contrato[0]->estatus_estadocontrato == 0) {
                        //Estado NO TERMINADO
                        $vistaprincipal = false;
                    }

                    $totalcontrato = $totalhistorial + $totalproductos - $totalabonos;
                    DB::table('contratos')->where('id', '=', $idContrato)->update([
                        'total' => $totalcontrato, 'estatus' => 1, 'estatus_estadocontrato' => 1,
                    ]);

                    //Insertar en tabla registroestadocontrato
                    DB::table('registroestadocontrato')->insert([
                        'id_contrato' => $idContrato,
                        'estatuscontrato' => 1,
                        'created_at' => Carbon::now()
                    ]);

                    if ($vistaprincipal) {
                        return redirect()->route('listacontrato', $idFranquicia);
                    }
                    return back()->with('bien', 'Se termino correctamente el contrato');

                }
            } else {
                if ($contrashijos != null) {
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $contrashijos[0]->id])->with('alerta', 'Favor de completar el contrato pendiente');
                }
                $cant = $contratos[0]->armazones;
                $cont = $contratos[0]->contador;
                $tothistorial = $contratos[0]->totalhistorial;
                $porcentaje = $contratos[0]->preciop;
                $preciounico = $contratos[0]->preciouno;
                $tipopro = $contratos[0]->tipopromocion;
                $estado = $contratos[0]->estatus;

                if ($cont == $cant) {

                    if ($contratohijo == null && $promo != null && $contador == 1 && $estado == 0) {
                        if ($tipopro == 1) {
                            $totalporcentaje = $preciounico;
                        } else {
                            $totalporcentaje = (($tothistorial * $porcentaje) / 100);
                        }
                        $totalporcentaje = number_format($totalporcentaje, 1, '.', '');
                        $totalnuevo = $tothistorial - $totalporcentaje;
                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'total' => $totalnuevo, 'totalpromocion' => $totalnuevo, 'estatus' => 1, 'estatus_estadocontrato' => 1, 'promocionterminada' => 1
                        ]);
                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $idContrato,
                            'estatuscontrato' => 1,
                            'created_at' => Carbon::now()
                        ]);
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('bien', 'Se realizo el calculo del contrato con promoción');
                    }

                    $contrashijos = DB::select("SELECT * FROM contratos  WHERE id_franquicia = '$idFranquicia' AND idcontratorelacion = '$idContrato' AND estatus_estadocontrato = 0 limit 1");
                    if ($contrashijos != null && $contrashijos[0]->estatus == 1) {
                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'estatus_estadocontrato' => 1, 'promocionterminada' => 1
                        ]);
                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $idContrato,
                            'estatuscontrato' => 1,
                            'created_at' => Carbon::now()
                        ]);
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $contrashijos[0]->id])->with('bien', 'Favor de completar el contrato pendiente');
                    }
                    if ($contrashijos != null && $contrashijos[0]->estatus == 0) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $contrashijos[0]->id])->with('bien', 'Favor de completar el contrato pendiente');
                    }
                    if ($contrashijos == null) {

                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'estatus_estadocontrato' => 1, 'promocionterminada' => 1
                        ]);
                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $idContrato,
                            'estatuscontrato' => 1,
                            'created_at' => Carbon::now()
                        ]);
                        return redirect()->route('listacontrato', $idFranquicia)->with('bien', 'No hay contratos pendientes por completar');
                    }
                }
            }
            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                'estatus_estadocontrato' => 1,
            ]);
            //Insertar en tabla registroestadocontrato
            DB::table('registroestadocontrato')->insert([
                'id_contrato' => $idContrato,
                'estatuscontrato' => 1,
                'created_at' => Carbon::now()
            ]);
            return view('administracion.historialclinico.nuevohijo', ['idFranquicia' => $idFranquicia, 'optometristas' => $optometristas, 'paquetes' => $paquetes,
                'fotocromatico' => $fotocromatico, 'AR' => $AR, 'tinte' => $tinte, 'blueray' => $blueray, 'armazones' => $armazones, 'contratos' => $contratos,
                'ultimoOptometrista' => $ultimoOptometrista, 'ultimaZona' => $ultimaZona, 'zonas' => $zonas, 'idContrato' => $idContrato, 'idcontratopadre' => $relacion]);


        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function contratoHijos($idFranquicia, $idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12) || ((Auth::user()->rol_id) == 6)) {
            $idUsuario = Auth::user()->id;
            $ultimoOptometrista = DB::select("SELECT
                                id_optometrista as ID,  u.name as NAME
                                from contratos c
                                inner join users u on c.id_optometrista = u.id
                                AND c.id_usuariocreacion = '$idUsuario'
                                order by c.created_at desc
                                limit 1");
            $ultimaZona = DB::select("SELECT
            id_zona as ID,  z.zona as zona
            from contratos c
            inner join zonas z on c.id_zona = z.id
            AND c.id_usuariocreacion = '$idUsuario'
            order by c.created_at desc
            limit 1");
            $zonas = DB::select("SELECT id as ID, zona as zona FROM zonas where id_franquicia = '$idFranquicia'");
            $optometristas = DB::select("SELECT u.ID,u.NAME
                                FROM users u
                                INNER JOIN usuariosfranquicia uf
                                ON uf.id_usuario = u.id
                                WHERE uf.id_franquicia = '$idFranquicia'
                                AND u.rol_id = 12");
            $fotocromatico = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'fotocromático'");
            $AR = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'A/R'");
            $tinte = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'tinte'");
            $blueray = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'BlueRay'");
            $paquetes = DB::select("SELECT * FROM paquetes WHERE id_franquicia = '$idFranquicia'");
            $armazones = DB::select("SELECT * FROM producto WHERE  id_tipoproducto = '1' order by nombre");
            $ct = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
            $historialesclinicos = DB::select("SELECT nombre, h.edad, h.fechaentrega, c.telefono, h.id_paquete, h.created_at, h.diagnostico FROM historialclinico h
            inner join contratos c
            on c.id = h.id_contrato
            WHERE id_contrato ='$idContrato'");
            $Hi = DB::select("SELECT * FROM contratos  WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
            $abonoproducto = DB::select("SELECT * FROM abonos  WHERE id_contrato = '$idContrato' AND tipoabono = '7'");
            $relacion = $Hi[0]->idcontratorelacion;
            $pagohijofinal = $Hi[0]->pago;
            $contratos = DB::select("SELECT c.id,c.datos,c.id_franquicia,c.id_usuariocreacion,c.nombre_usuariocreacion,z.zona,c.nombre,c.calle,c.numero, c.pago, u.name,
            pr.titulo, pr.armazones, c.totalproducto, c.totalhistorial, c.totalpromocion, c.correo, c.depto,c.alladode,c.frentea,c.entrecalles,c.colonia,c.localidad,c.telefono,
            c.casatipo,c.casacolor,c.created_at,c.updated_at,c.id_optometrista, c.id_promocion, c.contador, pr.preciop, c.total, c.totalabono, pr.preciouno, pr.tipopromocion,
            c.nombrereferencia, c.telefonoreferencia,
            (SELECT COUNT(id) from promocioncontrato pc where pc.id_contrato = c.id) as promo
            FROM contratos c
            INNER JOIN zonas z
            ON z.id = c.id_zona
            INNER JOIN users u
            ON u.id = c.id_optometrista
            INNER JOIN promocion pr
            ON pr.id = c.id_promocion
            WHERE c.datos = 1
            AND c.id = '$relacion'
            AND c.id_franquicia = '$idFranquicia'");
            $estadocontrato = $ct[0]->estatus_estadocontrato;
            $contratohijo = $ct[0]->idcontratorelacion;
            $totalhistorial = $ct[0]->totalhistorial;
            $totalabonos = $ct[0]->totalabono;
            $estatus = $ct[0]->estatus;
            $totalproductos = $ct[0]->totalproducto;
            $promo = $ct[0]->id_promocion;
            $pago = $ct[0]->pago;
            $total = $ct[0]->total;
            if ($abonoproducto == null && $totalproductos > 0) {
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                 ->with('alerta', 'Los abonos de producto no concuerdan con el total de productos');
            }
            if ($abonoproducto != null && $abonoproducto[0]->abono != $totalproductos) {
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                 ->with('alerta', 'Los abonos de producto no concuerdan con el total de productos');
            }
            if ($totalabonos < $totalproductos) {
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                 ->with('alerta', 'Favor de abonar el total de los productos');
            }
            $cant = $contratos[0]->armazones;
            $cont = $contratos[0]->contador;
            $tothistorial = $contratos[0]->totalhistorial;
            if ($cont == $cant) {

                $contras = DB::select("SELECT * FROM contratos  WHERE id_franquicia = '$idFranquicia' AND idcontratorelacion = '$relacion'
                    order by totalhistorial asc
                    limit 1;");

                $cantmenor = $contras[0]->totalhistorial;
                $cantpadre = $contratos[0]->totalhistorial;
                $cantpromo = $contratos[0]->totalpromocion;
                $porcentaje = $contratos[0]->preciop;
                $preciounico = $contratos[0]->preciouno;
                $tipopro = $contratos[0]->tipopromocion;

                if ($cantmenor >= $cantpadre) {
                    if ($tipopro == 1) {
                        $totalporcentaje = $preciounico;
                    } else {
                        $totalporcentaje = (($cantpadre * $porcentaje) / 100);
                    }
                    $totalporcentaje = number_format($totalporcentaje, 1, '.', '');
                } else {
                    if ($tipopro == 1) {
                        $totalporcentaje = $preciounico;
                    } else {
                        $totalporcentaje = (($cantmenor * $porcentaje) / 100) + $cantpromo;
                    }
                    $totalporcentaje = number_format($totalporcentaje, 1, '.', '');
                }

                $contrashijos = DB::select("SELECT * FROM contratos  WHERE id_franquicia = '$idFranquicia' AND idcontratorelacion = '$relacion'");
                $contraspadre = DB::select("SELECT * FROM contratos  WHERE id_franquicia = '$idFranquicia' AND id = '$relacion'");

                $sumahijos = DB::select("SELECT SUM(totalhistorial) AS sumahijos FROM contratos  WHERE id_franquicia = '$idFranquicia' AND idcontratorelacion = '$relacion'");
                $canthijos = DB::select("SELECT COUNT(id) AS canthijos FROM contratos  WHERE id_franquicia = '$idFranquicia' AND idcontratorelacion = '$relacion'");
                $floatsumahijos = intval($sumahijos[0]->sumahijos);
                $floatcanthijos = intval($canthijos[0]->canthijos);
                $totalpromofinal = (($contraspadre[0]->totalhistorial + $floatsumahijos - $totalporcentaje) / ($floatcanthijos + 1));
                $totalpromofinal = number_format($totalpromofinal, 1, '.', '');
                $totalfinalhijos = $contrashijos[0]->totalproducto + $totalpromofinal - $contrashijos[0]->totalabono;
                $totalfinal = $contraspadre[0]->totalproducto + $totalpromofinal - $contraspadre[0]->totalabono;
                if ($contrashijos[0]->pago == 0) {
                    $totalfinalhijos = $totalpromofinal;
                    $totalfinal = $totalpromofinal;
                }
                if ($contraspadre[0]->pago == 0) {
                    $totalfinalhijos = $totalpromofinal;
                    $totalfinal = $totalpromofinal;
                }

                $hijocontra = DB::select("SELECT * FROM contratos  WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
                $contrashijos = DB::select("SELECT * FROM contratos  WHERE id_franquicia = '$idFranquicia' AND idcontratorelacion = '$relacion' AND estatus_estadocontrato = 0");
                $pagohijo = $hijocontra[0]->pago;
                $estatushijo = $hijocontra[0]->estatus;

                if ($estatushijo == 0) {
                    DB::table('contratos')->where([['id', '=', $relacion], ['id_franquicia', '=', $idFranquicia]])->update([
                        'estatus' => 1, 'total' => $totalfinal, 'totalpromocion' => $totalpromofinal, 'promocionterminada' => 1
                    ]);
                    DB::table('contratos')->where([['idcontratorelacion', '=', $relacion], ['id_franquicia', '=', $idFranquicia]])->update([
                        'estatus' => 1, 'total' => $totalfinalhijos, 'totalpromocion' => $totalpromofinal, 'promocionterminada' => 1
                    ]);
                    DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                        'estatus' => 1, 'estatus_estadocontrato' => 1, 'total' => $totalfinalhijos, 'totalpromocion' => $totalpromofinal, 'promocionterminada' => 1
                    ]);
                    //Insertar en tabla registroestadocontrato
                    DB::table('registroestadocontrato')->insert([
                        'id_contrato' => $idContrato,
                        'estatuscontrato' => 1,
                        'created_at' => Carbon::now()
                    ]);
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $relacion])->with('bien', 'Se han creado todos los contratos ');
                }
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $relacion])->with('bien', 'Se han creado todos los contratos');
            } else {

                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                    'estatus_estadocontrato' => 1,
                ]);
                //Insertar en tabla registroestadocontrato
                DB::table('registroestadocontrato')->insert([
                    'id_contrato' => $idContrato,
                    'estatuscontrato' => 1,
                    'created_at' => Carbon::now()
                ]);
                return view('administracion.historialclinico.nuevohijo', ['idFranquicia' => $idFranquicia, 'optometristas' => $optometristas,
                    'paquetes' => $paquetes, 'fotocromatico' => $fotocromatico, 'AR' => $AR, 'tinte' => $tinte, 'blueray' => $blueray, 'armazones' => $armazones, 'contratos' => $contratos,
                    'ultimoOptometrista' => $ultimoOptometrista, 'ultimaZona' => $ultimaZona, 'zonas' => $zonas, 'idContrato' => $idContrato, 'idcontratopadre' => $relacion]);
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }


    public function agregarpromocion($idFranquicia, $idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12) || ((Auth::user()->rol_id) == 6)) {
            $rules = [
                'promocion' => 'required|integer',
            ];
            if (request('promocion') == 0) {
                return back()->withErrors(['promocion' => 'Elegir una promoción'])->withInput($request->all());

            }
            request()->validate($rules);
            $abonospromo = DB::select("SELECT COUNT(a.id) as conteo FROM abonos a INNER JOIN contratos c ON a.id_contrato = c.id
            WHERE CAST(a.tipoabono as SIGNED) != 7 AND (c.id = '$idContrato' OR c.idcontratorelacion = '$idContrato')");
            if ($abonospromo[0]->conteo > 0) {
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                 ->with('alerta', 'No se puede agregar con abonos ya registrados en los contratos de la promoción');
            }
            $randomId = $this->getPromocionContratoId();

            $promo = request('promocion');
            $creacion = Carbon::now();
            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                'id_promocion' => $promo
            ]);
            DB::table('promocion')->where([['id', '=', $promo], ['id_franquicia', '=', $idFranquicia]])->update([
                'asignado' => 1
            ]);
            DB::table('promocioncontrato')->insert([
                'id' => $randomId, 'id_contrato' => $idContrato, 'id_franquicia' => $idFranquicia, 'id_promocion' => $promo, 'estado' => 1, 'created_at' => $creacion
            ]);
            return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('bien', 'la promoción se agrego correctamente.');

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    private function getContratoId()
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $temporalId = $this->generadorRandom();
            $existente = DB::select("select id from contratos where id = '$temporalId'");
            if (sizeof($existente) == 0) {
                $unico = $temporalId;
                $esUnico = true;
            }
        }
        return $unico;
    }   // Comparar si ya existe el id en la base de datos

    private function generadorRandom($length = 10)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($caracteres);
        $randomId = '';
        for ($i = 0; $i < $length; $i++) {
            $randomId .= $caracteres[rAND(0, $charactersLength - 1)];
        }
        return $randomId;
    }

    // Generador rANDom

    public function crearcontrato($idFranquiciaContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12) || ((Auth::user()->rol_id) == 6)) {
            $rules = [
                'zona' => 'required',
                'nombre' => 'required|string|max:255',
                'optometrista' => 'required|integer',
                'calle' => 'required|string|max:255',
                'numero' => 'required|string|min:1|max:255',
                'formapago' => 'required|string|min:1|max:255',
                'departamento' => 'required|string|max:255',
                'alladode' => 'required|string|max:255',
                'frentea' => 'required|string|max:255',
                'entrecalles' => 'required|string|max:255',
                'colonia' => 'required|string|max:255',
                'localidad' => 'required|string|max:255',
                'telefono' => 'required|string|size:10|regex:/[0-9]/',
                'tr' => 'required|string|size:10|regex:/[0-9]/',
                'casatipo' => 'required|string|max:255',
                'nr' => 'required|string|max:255',
                'casacolor' => 'required|string|max:255',
                'fotoine' => 'required|image|mimes:png',
                'fotoineatras' => 'required|image|mimes:png',
                'fotocasa' => 'required|image|mimes:png',
                'comprobantedomicilio' => 'required|image|mimes:png',
            ];
            if (request('tarjetapension') != null && request('formapago') != 'Mensual') {
                return back()->withErrors(['tarjetapension' => 'Solo se permite con forma de pago mensual'])->withInput($request->all());
            }
            if (request('formapago') == 'Seleccionar') {
                return back()->withErrors(['formapago' => 'Elegir una forma de pago'])->withInput($request->all());
            }
            if (request('tarjetapensionatras') != null && request('formapago') != 'Mensual') {
                return back()->withErrors(['tarjetapensionatras' => 'Solo se permite con forma de pago mensual'])->withInput($request->all());
            }
            if (request('tarjetapension') != null && request('tarjetapensionatras') == null) {
                return back()->withErrors(['tarjetapensionatras' => 'Llenar ambos campos de la tarjeta'])->withInput($request->all());
            }
            if (request('tarjetapension') == null && request('tarjetapensionatras') != null) {
                return back()->withErrors(['tarjetapensionatras' => 'Llenar ambos campos de la tarjeta'])->withInput($request->all());
            }
            if (request('zona') == 'Seleccionar') {
                return back()->withErrors(['zona' => 'Elige una zona, campo obligatorio'])->withInput($request->all());
            }
            if (request('optometrista') == 'Seleccionar') {
                return back()->withErrors(['optometrista' => 'Elige una zona, campo obligatorio'])->withInput($request->all());
            }
            request()->validate($rules);
            if (strlen(request('zona')) > 0 && strlen(request('optometrista')) && strlen(request('formapago')) > 0 && strlen(request('nombre')) > 0
                && strlen(request('calle')) > 0 && strlen(request('numero')) > 0
                && strlen(request('alladode')) > 0 && strlen(request('frentea')) > 0 && strlen(request('entrecalles')) > 0 && strlen(request('colonia')) > 0
                && strlen(request('localidad')) > 0 && strlen(request('telefono')) > 0 && strlen(request('casatipo')) > 0 && strlen(request('casacolor')) > 0
                && strlen(request('fotoine')) && strlen(request('fotocasa')) && strlen(request('comprobantedomicilio'))) {


                try {

                    $contratos = DB::select("SHOW TABLE STATUS LIKE 'contratos'");
                    $randomId = $this->getContratoId();


                    $fotoBruta = 'Foto-Ine-Frente-Contrato-' . $randomId . '-' . time() . '.' . request()->file('fotoine')->getClientOriginalExtension();
                    $fotoine = request()->file('fotoine')->storeAs('uploads/imagenes/contratos/fotoine', $fotoBruta, 'disco');

                    $fotoBruta = 'Foto-Ine-Atras-Contrato-' . $randomId . '-' . time() . '.' . request()->file('fotoineatras')->getClientOriginalExtension();
                    $fotoineatras = request()->file('fotoineatras')->storeAs('uploads/imagenes/contratos/fotoineatras', $fotoBruta, 'disco');

                    $fotoBruta = 'Foto-Casa-Contrato-' . $randomId . '-' . time() . '.' . request()->file('fotocasa')->getClientOriginalExtension();
                    $fotocasa = request()->file('fotocasa')->storeAs('uploads/imagenes/contratos/fotocasa', $fotoBruta, 'disco');

                    $fotoBruta = 'Foto-comprobantedomicilio-Contrato-' . $randomId . '-' . time() . '.' . request()->file('comprobantedomicilio')->getClientOriginalExtension();
                    $comprobantedomicilio = request()->file('comprobantedomicilio')->storeAs('uploads/imagenes/contratos/comprobantedomicilio', $fotoBruta, 'disco');

                    $tarjetapension = '';   // by default empty
                    if (request('tarjetapension') != null && request('formapago') == 'Mensual') {
                        $fotoBruta = 'Foto-Tarjetapension-Frente-Contrato-' . $randomId . '-' . time() . '.' . request()->file('tarjetapension')->getClientOriginalExtension();
                        $tarjetapension = request()->file('tarjetapension')->storeAs('uploads/imagenes/contratos/tarjetapension', $fotoBruta, 'disco');
                    }

                    $tarjetapensionatras = '';   // by default empty
                    if (request('tarjetapensionatras') != null && request('formapago') == 'Mensual') {
                        $fotoBruta = 'Foto-Tarjetapension-Atras-Contrato-' . $randomId . '-' . time() . '.' . request()->file('tarjetapensionatras')->getClientOriginalExtension();
                        $tarjetapensionatras = request()->file('tarjetapensionatras')->storeAs('uploads/imagenes/contratos/tarjetapensionatras', $fotoBruta, 'disco');
                    }


                    $datos = 1;
                    $creacion = Carbon::now();
                    $usuarioId = Auth::user()->id;
                    $usuarioNombre = Auth::user()->name;
                    DB::table('contratos')->insert([
                        'id' => $randomId, 'datos' => $datos, 'id_franquicia' => $idFranquiciaContrato, 'id_usuariocreacion' => $usuarioId, 'nombre_usuariocreacion' => $usuarioNombre,
                        'id_zona' => request('zona'), 'id_promocion' => request('promocion'), 'id_optometrista' => request('optometrista'), 'nombre' => request('nombre'),
                        'pago' => request('formapago'), 'calle' => request('calle'), 'numero' => request('numero'), 'depto' => request('departamento'),
                        'alladode' => request('alladode'), 'frentea' => request('frentea'), 'entrecalles' => request('entrecalles'), 'colonia' => request('colonia'),
                        'localidad' => request('localidad'), 'telefono' => request('telefono'), 'casatipo' => request('casatipo'), 'casacolor' => request('casacolor'),
                        'created_at' => $creacion, 'nombrereferencia' => request('nr'), 'telefonoreferencia' => request('tr'), 'fotoine' => $fotoine, 'fotocasa' => $fotocasa,
                        'comprobantedomicilio' => $comprobantedomicilio, 'tarjeta' => $tarjetapension, 'fotoineatras' => $fotoineatras, 'tarjetapensionatras' => $tarjetapensionatras, 'contador' => 1
                    ]);
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquiciaContrato, 'idContrato' => $randomId])->with('bien', 'El contrato se actualizo correctamente.');
                } catch (\Exception $e) {
                    \Log::info("Error: " . $e->getMessage());
                    return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
                }
            } else {
                return back()->with('alerta', 'Para continuar es necesario llenar todos los campos.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function crearcontrato2($idFranquiciaContrato, $idContrato)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12) || ((Auth::user()->rol_id) == 6)) {

            $rules = [
                // 'nombre'=>'required|string|max:255',
                // 'telefono' => 'required|string|size:10|regex:/[0-9]/',
            ];
            request()->validate($rules);

            try {

                $contra = DB::select("SELECT c.id,c.datos,c.id_franquicia,c.id_usuariocreacion,c.nombre_usuariocreacion,z.zona,c.nombre,c.calle,c.numero, c.pago, u.name, pr.titulo,
                    c.telefonoreferencia, c.nombrereferencia, c.depto,c.alladode,c.frentea,c.entrecalles,c.colonia,c.localidad,c.telefono,c.casatipo,c.casacolor,c.created_at,c.updated_at,
                    c.id_optometrista, c.id_promocion, c.contador,
                    (SELECT COUNT(id) from promocioncontrato pc where pc.id_contrato = c.id) as promo
                    FROM contratos c
                    INNER JOIN zonas z
                    ON z.id = c.id_zona
                    INNER JOIN users u
                    ON u.id = c.id_optometrista
                    INNER JOIN promocion pr
                    ON pr.id = c.id_promocion
                    WHERE c.datos = 1
                    AND c.id = '$idContrato'
                    AND c.id_franquicia = '$idFranquiciaContrato'");
                $zona = $contra[0]->zona;
                $optometrista = $contra[0]->id_optometrista;
                $calle = $contra[0]->calle;
                $pago = $contra[0]->pago;
                $numero = $contra[0]->numero;
                $depto = $contra[0]->depto;
                $alladode = $contra[0]->alladode;
                $frentea = $contra[0]->frentea;
                $entrecalles = $contra[0]->entrecalles;
                $colonia = $contra[0]->colonia;
                $localidad = $contra[0]->localidad;
                $casatipo = $contra[0]->casatipo;
                $casacolor = $contra[0]->casacolor;
                $contador = $contra[0]->contador;
                $telefonoR = $contra[0]->telefonoreferencia;
                $nombreR = $contra[0]->nombrereferencia;
                $suma = $contador + 1;

                $contratos = DB::select("SHOW TABLE STATUS LIKE 'contratos'");
                $randomId = $this->getContratoId();
                $datos = 1;
                $creacion = Carbon::now();
                $usuarioId = Auth::user()->id;
                $usuarioNombre = Auth::user()->name;

                DB::table('contratos')->insert([
                    'id' => $randomId, 'datos' => $datos, 'id_franquicia' => $idFranquiciaContrato, 'id_usuariocreacion' => $usuarioId, 'nombre_usuariocreacion' => $usuarioNombre,
                    'id_zona' => $zona, 'id_optometrista' => $optometrista, 'nombre' => request('nombre'), 'pago' => $pago, 'calle' => $calle, 'numero' => $numero, 'depto' => $depto,
                    'alladode' => $alladode, 'frentea' => $frentea, 'entrecalles' => $entrecalles, 'colonia' => $colonia, 'localidad' => $localidad, 'telefono' => request('telefono'),
                    'casatipo' => $casatipo, 'casacolor' => $casacolor, 'created_at' => $creacion, 'idcontratorelacion' => $idContrato, 'nombrereferencia' => $nombreR,
                    'telefonoreferencia' => $telefonoR
                ]);

                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquiciaContrato]])->update([
                    'contador' => $suma
                ]);
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquiciaContrato, 'idContrato' => $randomId])->with('bien', 'El contrato se actualizo correctamente.');
            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function filtrarlistacontrato($idFranquicia)
    {
        if (Auth::check() && (((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8))) {
            $contratoslaboratorio = null;
            $contratosconfirmaciones = null;
            $contratosreportesgarantia = null;
            $contratossupervision = null;
            $contratosnoenviados = null;
            $contratosgarantiascreadas = null;
            $cbGarantias = null;
            $cbSupervision = null;
            $cbAtrasado = null;
            $cbEntrega = null;
            $cbLaboratorio = null;
            $cbConfirmacion = null;
            $cbTodos = null;
            $zonaU = null;
            $fechainibuscar = null;
            $fechafinbuscar = null;

            $arrayCheckBox = array();

            $filtro = request('filtro');
            $swBusquedaFiltro = request('swBusquedaFiltro');
            $swBusquedaFiltro = ($swBusquedaFiltro == null)? '0':$swBusquedaFiltro;
            $now = Carbon::now();

                //Valores por default
                $cbGarantias = 1;
                $cbSupervision = 0;
                $cbAtrasado = 1;
                $cbEntrega = 1;
                $cbLaboratorio = 0;
                $cbConfirmacion = 0;
                $cbTodos = 0;
                $swBusquedaAvanzada = 0;

                if ($filtro != null) {
                    $cbGarantias = 1;
                    $cbSupervision = 1;
                    $cbAtrasado = 1;
                    $cbEntrega = 1;
                    $cbLaboratorio = 1;
                    $cbConfirmacion = 1;
                    $cbTodos = 1;
                }
                array_push($arrayCheckBox, $cbGarantias);
                array_push($arrayCheckBox, $cbSupervision);
                array_push($arrayCheckBox, $cbAtrasado);
                array_push($arrayCheckBox, $cbEntrega);
                array_push($arrayCheckBox, $cbLaboratorio);
                array_push($arrayCheckBox, $cbConfirmacion);
                array_push($arrayCheckBox, $cbTodos);
                array_push($arrayCheckBox, $zonaU);
                array_push($arrayCheckBox, $fechainibuscar);
                array_push($arrayCheckBox, $fechafinbuscar);
                array_push($arrayCheckBox, $swBusquedaFiltro);

                $arrayContratos = self::obtenerListaContratosConOSinFiltro($idFranquicia, $filtro, $arrayCheckBox);

                $contratosprioritarios = $arrayContratos[0];
                $contratosatrasados = $arrayContratos[1];
                $contratosperiodo = $arrayContratos[2];
                $contratosentregar = $arrayContratos[3];
                $contratoslaboratorio = $arrayContratos[4];
                $contratosconfirmaciones = $arrayContratos[5];
                $contratos = $arrayContratos[6];
                $contratosreportesgarantia = $arrayContratos[7];
                $contratossupervision = $arrayContratos[8];
                $contratosnoenviados = $arrayContratos[9];
                $contratosgarantiascreadas = $arrayContratos[10];

                //Unir arreglos para contar registros sin repetir contratos
                $contratosGeneral = array_merge($contratosprioritarios, $contratosatrasados, $contratosperiodo, $contratosentregar, $contratoslaboratorio, $contratosconfirmaciones, $contratos,
                    $contratosreportesgarantia, $contratossupervision, $contratosnoenviados, $contratosgarantiascreadas);
                $idContratos = [];

                //Extraemos el id de cada contrato
                foreach ($contratosGeneral as $contratoGeneral){
                    array_push($idContratos, $contratoGeneral->id);
                }

                //Eliminamos contratos repetidos
                $idContratosUnicos = array_unique($idContratos);
                $totalRegistros = sizeof($idContratosUnicos);


            //Insertar registro en historial Sucursal seccion busqueda
            if($filtro != null){
                $historialBuaqueda = "Filtro contratos por: ".$filtro;
            }else{
                //Busqueda general
                $historialBuaqueda = "Realizo una busqueda sin ningun filtro";
            }
            self::insertarHistorialSucursal($idFranquicia, Auth::user()->id, $historialBuaqueda);

            $zonas = DB::select("SELECT id,zona FROM zonas WHERE id_franquicia = '$idFranquicia'");
            $franquiciaContratos = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");
            return view('administracion.contrato.tabla', ['contratos' => $contratos, 'franquiciaContratos' => $franquiciaContratos,
                'contratosperiodo' => $contratosperiodo, 'now' => $now, 'contratosatrasados' => $contratosatrasados, 'contratosprioritarios' => $contratosprioritarios,
                'contratosentregar' => $contratosentregar, 'contratoslaboratorio' => $contratoslaboratorio, 'contratosconfirmaciones' => $contratosconfirmaciones,
                'contratosreportesgarantia' => $contratosreportesgarantia, 'contratossupervision' => $contratossupervision, 'contratosnoenviados' => $contratosnoenviados,
                'contratosgarantiascreadas' => $contratosgarantiascreadas, 'zonas' => $zonas, 'cbGarantias' => $cbGarantias, 'cbSupervision' => $cbSupervision, 'cbAtrasado' => $cbAtrasado,
                'cbEntrega' => $cbEntrega, 'cbLaboratorio' => $cbLaboratorio, 'cbConfirmacion' => $cbConfirmacion, 'cbTodos' => $cbTodos, 'zonaU' => $zonaU, 'idFranquicia' => $idFranquicia,
                'totalRegistros' => $totalRegistros, 'swBusquedaAvanzada' => $swBusquedaAvanzada, 'swBusquedaFiltro' => $swBusquedaFiltro,'filtro'=>$filtro]);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function filtrarlistacontratocheckbox($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8)) {

            $arrayCheckBox = array();

            $cbGarantias = request('cbGarantias');
            $cbSupervision = request('cbSupervision');
            $cbAtrasado = request('cbAtrasado');
            $cbEntrega = request('cbEntrega');
            $cbLaboratorio = request('cbLaboratorio');
            $cbConfirmacion = request('cbConfirmacion');
            $cbTodos = request('cbTodos');
            $zonaU = request('zonaU');
            $fechainibuscar = request('fechainibuscar');
            $fechafinbuscar = request('fechafinbuscar');
            $swBusquedaAvanzada = request('swBusquedaAvanzada');
            $swBusquedaAvanzada = ($swBusquedaAvanzada == null)? '0':$swBusquedaAvanzada;
            $cadenaHistorialFiltro = "Filtro contratos por: ";


            if (strlen($fechafinbuscar) > 0 && strlen($fechainibuscar) == 0) {
                //fechafin diferente de vacio y fechaini vacio
                return back()->with('alerta', 'Debes agregar una fecha inicial.');
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
                    return back()->with('alerta', 'La fecha inicial debe ser menor o igual a la final.');
                }
            }

            array_push($arrayCheckBox, $cbGarantias);
            array_push($arrayCheckBox, $cbSupervision);
            array_push($arrayCheckBox, $cbAtrasado);
            array_push($arrayCheckBox, $cbEntrega);
            array_push($arrayCheckBox, $cbLaboratorio);
            array_push($arrayCheckBox, $cbConfirmacion);
            array_push($arrayCheckBox, $cbTodos);
            array_push($arrayCheckBox, $zonaU);
            array_push($arrayCheckBox, $fechainibuscar);
            array_push($arrayCheckBox, $fechafinbuscar);
            array_push($arrayCheckBox, $swBusquedaAvanzada);

            $now = Carbon::now();

            $arrayCheckBox = self::obtenerListaContratosConOSinFiltro($idFranquicia, null, $arrayCheckBox);

            $contratosprioritarios = $arrayCheckBox[0];
            $contratosatrasados = $arrayCheckBox[1];
            $contratosperiodo = $arrayCheckBox[2];
            $contratosentregar = $arrayCheckBox[3];
            $contratoslaboratorio = $arrayCheckBox[4];
            $contratosconfirmaciones = $arrayCheckBox[5];
            $contratos = $arrayCheckBox[6];
            $contratosreportesgarantia = $arrayCheckBox[7];
            $contratossupervision = $arrayCheckBox[8];
            $contratosnoenviados = $arrayCheckBox[9];
            $contratosgarantiascreadas = $arrayCheckBox[10];

            //Unir arrelos para contar registros sin repetir contratos
            $contratosGeneral = array_merge($contratosprioritarios, $contratosatrasados, $contratosperiodo, $contratosentregar, $contratoslaboratorio, $contratosconfirmaciones, $contratos,
            $contratosreportesgarantia, $contratossupervision, $contratosnoenviados, $contratosgarantiascreadas);
            $idContratos = [];

            //Extraemos el id de cada contrato
            foreach ($contratosGeneral as $contratoGeneral){
                array_push($idContratos, $contratoGeneral->id);
            }

            //Eliminamos contratos repetidos
            $idContratosUnicos = array_unique($idContratos);
            $totalRegistros = sizeof($idContratosUnicos);

            //Verifiar que parametros fueron seleccionados para generar la busqueda
            $parametrosBusqueda = array();
            if($cbGarantias != null){
                array_push($parametrosBusqueda, "Garantia");
            }if($cbSupervision != null){
                array_push($parametrosBusqueda, "Supervision");
            }if($cbAtrasado != null){
                array_push($parametrosBusqueda, "Atrasado");
            }if($cbEntrega != null){
                array_push($parametrosBusqueda, "Entrega");
            }if($cbLaboratorio != null){
                array_push($parametrosBusqueda, "Laboratorio");
            }if($cbConfirmacion != null){
                array_push($parametrosBusqueda, "Confirmacion");
            }if($cbTodos != null){
                array_push($parametrosBusqueda, "Todos");
            }if($zonaU != null){
                array_push($parametrosBusqueda, $zonaU);
            }if(($fechainibuscar != null) && ($fechafinbuscar != null)){
                array_push($parametrosBusqueda, "Periodo de fecha");
            }

            //Concatenamos parametros de busqueda
            foreach ($parametrosBusqueda as $parametro){
                $cadenaHistorialFiltro = $cadenaHistorialFiltro . " ".$parametro . ",";
            }
            //Eliminamos la ultima , de la cadena
            $cadenaHistorialFiltro = trim($cadenaHistorialFiltro, ",");

            //Insertamos el movimiento en historialSucursal
            self::insertarHistorialSucursal($idFranquicia, Auth::user()->id, $cadenaHistorialFiltro);

            $zonas = DB::select("SELECT id,zona FROM zonas WHERE id_franquicia = '$idFranquicia'");
            $franquiciaContratos = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");
            return view('administracion.contrato.tabla', ['contratos' => $contratos, 'franquiciaContratos' => $franquiciaContratos,
                'contratosperiodo' => $contratosperiodo, 'now' => $now, 'contratosatrasados' => $contratosatrasados, 'contratosprioritarios' => $contratosprioritarios,
                'contratosentregar' => $contratosentregar, 'contratoslaboratorio' => $contratoslaboratorio, 'contratosconfirmaciones' => $contratosconfirmaciones, 'zonas' => $zonas,
                'contratosreportesgarantia' => $contratosreportesgarantia, 'contratossupervision' => $contratossupervision, 'contratosnoenviados' => $contratosnoenviados,
                'contratosgarantiascreadas' => $contratosgarantiascreadas, 'cbGarantias' => $cbGarantias, 'cbSupervision' => $cbSupervision, 'cbAtrasado' => $cbAtrasado,
                'cbEntrega' => $cbEntrega, 'cbLaboratorio' => $cbLaboratorio, 'cbConfirmacion' => $cbConfirmacion, 'cbTodos' => $cbTodos, 'zonaU' => $zonaU,
                'idFranquicia' => $idFranquicia, 'totalRegistros' => $totalRegistros, 'swBusquedaAvanzada' => $swBusquedaAvanzada, 'swBusquedaFiltro' => '0']);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function vercontrato($idFranquicia, $idContrato)
    {
        if (Auth::check() && (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 13 || (Auth::user()->rol_id) == 12
            || (Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 4) {

            $existeContrato = DB::select("SELECT id, estatus_estadocontrato FROM contratos WHERE id = '$idContrato' AND id_franquicia = '$idFranquicia'");
            if ($existeContrato != null) {

                $now = Carbon::now();
                $nowparce = Carbon::parse($now)->format('Y-m-d');
                $hoyNumero = $now->dayOfWeekIso;
                $contratosGlobal = new contratosGlobal;

                $this->calculoTotal($idContrato, $idFranquicia);

                //Validacion de si quedo con estado liquidado y total es mayor a 0
                $estadoActualizadoContrato = DB::select("SELECT estatus_estadocontrato, total, id_zona FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
                if($estadoActualizadoContrato != null) {
                    //Existe contrato
                    if($estadoActualizadoContrato[0]->estatus_estadocontrato == 5 && $estadoActualizadoContrato[0]->total > 0) {
                        //Estado contrato es LIQUIDADO y total es mayor a 0
                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'estatus_estadocontrato' => 2,
                            'costoatraso' => 0
                        ]);
                    }

                    if(($estadoActualizadoContrato[0]->estatus_estadocontrato == 2 || $estadoActualizadoContrato[0]->estatus_estadocontrato == 4)
                        && $estadoActualizadoContrato[0]->total <= 0) {
                        //Estado contrato es ENTREGADO o ABONO ATRASADO y total es menor o igual a 0
                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'estatus_estadocontrato' => 5,
                            'costoatraso' => 0
                        ]);
                    }

                    //Validacion para los cobradores con estatus 2,4,12 traerme cobradores y validar si esta el contrato asignado a ellos
                    $estadoActualizadoContratoCobradores = DB::select("SELECT estatus_estadocontrato,
                                                                                    id_zona
                                                                                    FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");

                    if ($estadoActualizadoContratoCobradores != null) {
                        //Existe contrato

                        if ($estadoActualizadoContratoCobradores[0]->estatus_estadocontrato == 2 || $estadoActualizadoContratoCobradores[0]->estatus_estadocontrato == 4
                            || $estadoActualizadoContratoCobradores[0]->estatus_estadocontrato == 12) {
                            //ENTREGADO, ABONO ATRASADO, ENVIADO
                            $cobradoresAsignadosAZona = DB::select("SELECT u.id
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id = uf.id_usuario
                                              WHERE u.rol_id = 4 AND u.id_zona = '" . $estadoActualizadoContratoCobradores[0]->id_zona . "'"); //idsUsuarios cobranza que este asignados a la zona

                            if ($cobradoresAsignadosAZona != null) {
                                //Existen cobradores
                                foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                    //Recorrido cobradores
                                    $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $cobradorAsignadoAZona->id);
                                }
                            }
                        }
                    }

                }

                //Actualizar ultimo abono
                $respuesta = $this->obtenerFechaUltimoAbonoDadoEnContrato($idFranquicia, $idContrato);
                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                    'ultimoabono' => $respuesta
                ]);

                //Actualizar contrato en tabla contratostemporalessincronizacion
                $contratosGlobal::actualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato);

                //Eliminar garantias repetidas del contrato
                $contratosGlobal::eliminarGarantiasRepetidasTabla($idContrato);

                $abonos = DB::select("SELECT a.indice, a.id, a.folio, a.id_franquicia, a.id_contrato, a.id_usuario, (SELECT u.name FROM users u WHERE u.id = a.id_usuario) as usuario,
                                            a.abono, a.metodopago, a.adelantos, a.tipoabono, a.atraso, a.poliza, a.corte, a.id_corte, a.id_contratoproducto, a.id_zona,
                                            a.fecharegistro, a.coordenadas, a.created_at, a.updated_at FROM abonos a WHERE id_contrato ='$idContrato' ORDER BY created_at DESC");

                //Validacion de si es garantia o no
                $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1 ORDER BY created_at LIMIT 1");

                if ($estadoActualizadoContrato[0]->estatus_estadocontrato == 2 || $estadoActualizadoContrato[0]->estatus_estadocontrato == 4
                    || $estadoActualizadoContrato[0]->estatus_estadocontrato == 12
                    || (($estadoActualizadoContrato[0]->estatus_estadocontrato == 1 || $estadoActualizadoContrato[0]->estatus_estadocontrato == 9
                            || $estadoActualizadoContrato[0]->estatus_estadocontrato == 10 || $estadoActualizadoContrato[0]->estatus_estadocontrato == 11
                            || $estadoActualizadoContrato[0]->estatus_estadocontrato == 7) && $tieneHistorialGarantia != null)) {
                    //ENTREGADO, ABONO ATRASADO, LIQUIDADO, ENVIADO O TERMINADO, EN PROCESO DE APROBACION, MANUFACTURA, EN PROCESO DE ENVIO, APROBADO Y TENGA GARANTIA

                    //Actualizar si existe o insertar si no existe abonos en tabla abonoscontratostemporalessincronizacion
                    foreach ($abonos as $abono) {
                        //Recorrido abonos

                        $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($estadoActualizadoContrato[0]->id_zona);
                        foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                            //Recorrido cobradores
                            $existeAbono = DB::select("SELECT id FROM abonoscontratostemporalessincronizacion WHERE id_contrato = '$idContrato'
                                                         AND id = '" . $abono->id . "'
                                                         AND id_usuariocobrador = '" . $cobradorAsignadoAZona->id . "'");

                            if ($existeAbono != null) {
                                //Existe abono en tabla abonoscontratostemporalessincronizacion (Actualizar)
                                DB::table("abonoscontratostemporalessincronizacion")->where("id", "=", $abono->id)
                                    ->where("id_contrato", "=", $idContrato)->update([
                                        "folio" => $abono->folio,
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
                            } else {
                                //No existe abono en tabla abonoscontratostemporalessincronizacion (Insertar)
                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
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
                    }

                }

                //Verificamos si tiene solicitud de cancelar contrato
                $solicitudCancelar = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato  = '$idContrato' AND a.tipo IN (1,11) AND a.estatus != '1' ORDER BY a.created_at DESC LIMIT 1");

                //Verificamos si tiene solicitud de aumetar/disminuir
                $solicitudAumentarDisminuir = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato  = '$idContrato' AND a.tipo = 2 ORDER BY a.created_at DESC LIMIT 1");

                //Verificamos si tiene solicitud de armazon
                $solicitudArmazon = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato = '$idContrato' AND a.tipo IN (8,9,10) ORDER BY a.created_at DESC LIMIT 1");

                //Verificamos si tiene solicitud de supervisar contrato
                $solicitudSupervisar = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato  = '$idContrato' AND a.tipo IN (12) ORDER BY a.created_at DESC LIMIT 1");

                //Verificar si tiene fecha inicial y fecha final
                $periodo = null;
                $fechasPeriodo = DB::select("SELECT c.fechacobroini AS fechaIni, c.fechacobrofin AS fechaFin FROM contratos c WHERE c.id = '$idContrato' AND c.id_franquicia = '$idFranquicia'");
                if(($fechasPeriodo != null) && ($fechasPeriodo[0]->fechaIni != null && $fechasPeriodo[0]->fechaFin != null )){
                    //Si tiene periodo de fechas
                    $periodo = Carbon::parse($fechasPeriodo[0]->fechaIni)->format('d') . "-" . Carbon::parse($fechasPeriodo[0]->fechaFin)->format('d');
                }

                $historialesclinicos = DB::select("SELECT h.id, c.nombre, h.observaciones , h.edad, h.fechaentrega, c.telefono, h.id_paquete, h.created_at, h.diagnostico,
                                                    h.observaciones,h.observacionesinterno FROM historialclinico h
                                                    inner join contratos c
                                                    on c.id = h.id_contrato
                                                    WHERE id_contrato ='$idContrato'
                                                    ORDER BY created_at ASC");
                $contrashijos = DB::select("SELECT * FROM contratos  WHERE id_franquicia = '$idFranquicia' AND idcontratorelacion = '$idContrato' AND estatus_estadocontrato = 0 limit 1");

                if (((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8)) {
                    $promociones = DB::select("SELECT * FROM promocion where id_franquicia = '$idFranquicia' AND status != 2 AND id_tipopromocionusuario >= 0");
                } else {
                    $promociones = DB::select("SELECT * FROM promocion where id_franquicia = '$idFranquicia' AND status != 2 AND id_tipopromocionusuario = 0");
                }

                $abonostarjetameses = DB::select("SELECT count(id) as cont FROM abonos where id_contrato ='$idContrato' AND tipoabono != 7");

                $dentroRango = DB::select("SELECT id FROM contratos where id = '$idContrato' AND
                                                ((STR_TO_DATE('$now','%Y-%m-%d') = STR_TO_DATE(diaseleccionado,'%Y-%m-%d')) OR
                                                (STR_TO_DATE('$now','%Y-%m-%d') >= STR_TO_DATE(fechacobroini,'%Y-%m-%d')) AND STR_TO_DATE('$now','%Y-%m-%d') <= STR_TO_DATE(fechacobrofin,'%Y-%m-%d'))");
                $promocioncontrato = DB::select("SELECT id_promocion as id, p.titulo, p.asignado, p.inicio, p.fin, p.status,pr.id_contrato, pr.estado
                                                FROM promocioncontrato  pr
                                                inner join promocion p on pr.id_promocion = p.id
                                                WHERE id_contrato = '$idContrato'");

                $historialcontrato = DB::select("SELECT id_usuarioC, h.id, h.cambios, u.name, h.created_at, h.id_contrato, h.tipomensaje
                                                        from historialcontrato h
                                                        inner join users u on h.id_usuarioC = u.id
                                                        WHERE id_contrato = '$idContrato' order by h.created_at desc");

                $historialFotosContrato = DB::select("SELECT h.id, h.observaciones, u.name, h.created_at, h.foto
                                                        from historialfotoscontrato h
                                                        inner join users u on h.id_usuarioC = u.id
                                                        WHERE id_contrato = '$idContrato' order by h.created_at desc");

                $productos = DB::select("SELECT * FROM producto WHERE id_franquicia = '$idFranquicia' AND id_tipoproducto != 1 ORDER BY nombre ASC");
                $productosArmazon = DB::select("SELECT * FROM producto p WHERE id_tipoproducto = '1'
                                                        AND STR_TO_DATE(p.created_at,'%Y-%m-%d') >= '2022-11-13'
                                                        ORDER BY p.nombre, p.color ASC");
                $productos = array_merge($productos,$productosArmazon);
                $contratoproducto = DB::select("SELECT cp.id, cp.id_contrato, cp.created_at, cp.id_franquicia, p.id_tipoproducto, p.nombre, p.precio, cp.piezas, cp.total, p.preciop, p.color,
                                                        (SELECT a.metodopago FROM abonos a WHERE a.id_contrato = '$idContrato' AND a.id_contratoproducto = cp.id) as existeAbono, cp.estadoautorizacion, cp.id_producto
                                                        FROM contratoproducto cp
                                                        INNER JOIN producto p ON cp.id_producto = p.id
                                                        WHERE cp.id_contrato = '$idContrato'");


                if (((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12)) {

                    $idUsuario = Auth::user()->id;
                    $contrato = DB::select("SELECT c.id,c.datos,c.id_franquicia,c.id_usuariocreacion,c.nombre_usuariocreacion,z.zona,c.nombre,c.calle,c.numero,c.total,
                                                    c.contador, c.id_promocion, c.totalproducto, c.fechacobroini, c.fechacobrofin, c.totalabono, c.costoatraso, c.promocionterminada,
                                                    c.subscripcion, c.diapago, c.depto,c.alladode,c.frentea,c.entrecalles,c.nombrereferencia,c.telefonoreferencia,c.colonia,c.localidad,
                                                    c.telefono,c.casatipo,c.casacolor,c.pago,c.tarjeta,u.name,c.id_optometrista,c.created_at,c.updated_at,c.idcontratorelacion,
                                                    c.estatus_estadocontrato, c.ultimoabono, c.enganche, c.entregaproducto, c.totalreal, c.coloniaentrega, c.localidadentrega,
                                                    c.calleentrega, c.numeroentrega, c.entrecallesentrega, c.diatemporal, c.abonominimo, c.pagosadelantar,
                                                    (IFNULL(costoatraso,0) + 150) as semanaatraso,
                                                    (IFNULL(costoatraso,0) + 300) as quincenaatraso,
                                                    (IFNULL(costoatraso,0) + 400) as mesatraso,
                                                    (SELECT TIMESTAMPDIFF(DAY, co.fechaatraso ,'$now') FROM contratos co WHERE co.id = c.id LIMIT 1)   AS dias,
                                                    (IFNULL(total,0) + ifnull(totalproducto,0) - ifnull(totalabono,0)) as tiemporeal
                                                    FROM contratos c
                                                    INNER JOIN zonas z
                                                    ON z.id = c.id_zona
                                                    INNER JOIN users u
                                                    ON u.id = c.id_optometrista
                                                    WHERE c.datos = 1
                                                    AND c.id_franquicia = '$idFranquicia'
                                                    AND c.id_usuariocreacion = '$idUsuario'
                                                    AND c.id = '$idContrato'
                                                    AND c.estatus_estadocontrato NOT IN(3,6)
                                                    ");
                    $promo = DB::select("SELECT c.id,c.datos,c.id_promocion, p.armazones
                                                FROM contratos c
                                                INNER JOIN promocion p
                                                ON p.id = c.id_promocion
                                                WHERE c.datos = 1
                                                AND c.id_franquicia = '$idFranquicia'
                                                AND c.id_usuariocreacion = '$idUsuario'
                                                AND c.id = '$idContrato'
                                                ");
                } else {
                    $contrato = DB::select("SELECT c.id,c.datos,c.id_franquicia,c.id_usuariocreacion,c.nombre_usuariocreacion,z.zona,c.nombre,c.calle,c.numero,c.total, c.contador,
                                                    c.id_promocion, c.estatus,  c.totalproducto, c.fechacobroini, c.fechacobrofin, c.totalabono, c.costoatraso,  c.promocionterminada,
                                                    c.subscripcion, c.totalpromocion, c.diapago, c.depto,c.alladode,c.frentea,c.entrecalles,c.nombrereferencia,c.telefonoreferencia,
                                                    c.colonia,c.localidad,c.telefono,c.casatipo,c.casacolor,c.pago,c.tarjeta,u.name,c.id_optometrista,c.created_at,c.updated_at,
                                                    c.idcontratorelacion, c.estatus_estadocontrato, c.ultimoabono, c.enganche, c.entregaproducto, c.totalreal, c.coloniaentrega, c.localidadentrega,
                                                    c.calleentrega, c.numeroentrega, c.entrecallesentrega, c.diatemporal, c.abonominimo, c.pagosadelantar, c.alias,
                                                      -- (IFNULL((SELECT CASE WHEN TOTALPROMOCION < 0 THEN NULL ELSE TOTALPROMOCION END FROM contratos co WHERE co.id = c.id),
                                                      -- TOTALHISTORIAL) + ifnull(totalproducto,0) - ifnull(totalabono,0)) as tiemporeal
                                                    (IFNULL(costoatraso,0) + 150) as semanaatraso,
                                                    (IFNULL(costoatraso,0) + 300) as quincenaatraso,
                                                    (IFNULL(costoatraso,0) + 400) as mesatraso,
                                                    (SELECT TIMESTAMPDIFF(DAY, co.fechaatraso ,'$now') FROM contratos co WHERE co.id = c.id LIMIT 1)   AS dias,
                                                    (IFNULL(total,0) + ifnull(totalproducto,0) - ifnull(totalabono,0)) as tiemporeal,
                                                    (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia,
                                                    (SELECT SUM(a.abono) FROM abonos a WHERE a.id_contrato = c.id) as totalabonos,
                                                    (SELECT SUM(prod.total) FROM contratoproducto prod WHERE prod.id_contrato = c.id) as totalproductos,
                                                    (SELECT (SELECT promoc.id FROM promocion promoc WHERE promoc.id = pc.id_promocion) FROM promocioncontrato pc WHERE pc.id_contrato = c.id
                                                    AND pc.estado = '1') AS promocion
                                                    FROM contratos c
                                                    INNER JOIN zonas z
                                                    ON z.id = c.id_zona
                                                    INNER JOIN users u
                                                    ON u.id = c.id_optometrista
                                                    WHERE c.datos = 1
                                                    AND c.id_franquicia = '$idFranquicia'
                                                    AND c.id = '$idContrato'
                                                    ");
                    $promo = DB::select("SELECT c.id,c.datos,c.id_promocion, p.armazones
                                                  FROM contratos c
                                                  INNER JOIN promocion p
                                                  ON p.id = c.id_promocion
                                                  INNER JOIN users u
                                                  ON u.id = c.id_optometrista
                                                  WHERE c.datos = 1
                                                  AND c.id_franquicia = '$idFranquicia'
                                                  AND c.id = '$idContrato'
                                                  ");
                }
                if ($contrato == null) {
                    return back()->with("alerta", "No tienes acceso al contrato en este momento.");
                }

                if ($contrato[0]->estadogarantia != null && $contrato[0]->estadogarantia != 2 && $contrato[0]->estatus_estadocontrato == 1) {
                    //Tiene garantia y estado de la garantia es diferente de 2 y estatus del contrato es igual a TERMINADO
                    $ultimoRegistroEstadoContrato = DB::select("SELECT estatuscontrato FROM registroestadocontrato WHERE id_contrato ='$idContrato'
                                                     AND estatuscontrato NOT IN (1,9) ORDER BY created_at DESC LIMIT 1");
                    if ($ultimoRegistroEstadoContrato != null) {
                        //Existe ultimo registro de estatus ENTREGADO, ABONO ATRASADO O ENVIADO

                        //Actualizar estado del contrato
                        DB::table("contratos")->where("id", "=", $idContrato)->update([
                            "estatus_estadocontrato" => $ultimoRegistroEstadoContrato[0]->estatuscontrato,
                            "updated_at" => Carbon::now()
                        ]);

                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $idContrato,
                            'estatuscontrato' => $ultimoRegistroEstadoContrato[0]->estatuscontrato,
                            'created_at' => Carbon::now()
                        ]);

                    }
                }

                $rela = $contrato[0]->idcontratorelacion;
                $contraspadre5 = DB::select("SELECT c.id, c.contador, c.id_promocion, pr.armazones FROM contratos c INNER JOIN promocion pr
                                                    ON pr.id = c.id_promocion
                                                    WHERE c.id_franquicia = '$idFranquicia'
                                                    AND c.id = '$rela'");

                $contraspadre2 = DB::select("SELECT c.id, c.contador, pr.armazones FROM contratos c INNER JOIN promocion pr
                                                    ON pr.id = c.id_promocion
                                                    WHERE c.id_franquicia = '$idFranquicia'
                                                    AND c.id = '$idContrato'");

                $contratosterminadostodos = DB::select("SELECT id FROM contratos
                                                                WHERE id_franquicia = '$idFranquicia'
                                                                AND (idcontratorelacion = '$idContrato'
                                                                OR id = '$idContrato')
                                                                AND  estatus_estadocontrato = 0");

                $contratohoy = DB::select("SELECT created_at FROM contratos WHERE id = '$idContrato' AND STR_TO_DATE(created_at,'%Y-%m-%d') = '$nowparce'");

                if ((((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12)) && $contratohoy == null && $contrato[0]->estatus_estadocontrato != 0) {
                    return redirect()->route('listacontrato', $idFranquicia)->with('alerta', 'Ya no puedes consultar el contrato terminado');
                }
                if (((Auth::user()->rol_id) == 4) && $contrato[0]->estatus_estadocontrato == 0) {
                    return redirect()->route('listacontrato', $idFranquicia)->with('alerta', 'No puedes ingresar a contratos no terminados');
                }

                $folioOIdUltimoAbonoEliminar = DB::select("SELECT id, folio, created_at FROM abonos WHERE id_contrato ='$idContrato'
                                                                    AND metodopago != '1' AND tipoabono != 7 ORDER BY created_at DESC LIMIT 1");
                $fechaHoraUltimoAbonoEliminar = null;

                if($folioOIdUltimoAbonoEliminar != null) {
                    //Existe por lo menos un abono
                    $fechaHoraUltimoAbonoEliminar = $folioOIdUltimoAbonoEliminar[0]->created_at;
                    if($folioOIdUltimoAbonoEliminar[0]->folio != null) {
                        //Tiene folio el ultimo abono
                        $folioOIdUltimoAbonoEliminar = $folioOIdUltimoAbonoEliminar[0]->folio;
                    }else {
                        //No tiene folio el ultimo abono
                        $folioOIdUltimoAbonoEliminar = $folioOIdUltimoAbonoEliminar[0]->id;
                    }
                }

                $fechaUltimoAbonoConsulta = DB::select("SELECT created_at FROM abonos WHERE id_contrato ='$idContrato' ORDER BY created_at DESC LIMIT 1");
                $fechaultimoabono = null; //Para obtener la fecha del ultimoabono
                if($fechaUltimoAbonoConsulta != null) {
                    //Existe por lo menos un abono
                    $fechaultimoabono = Carbon::parse($fechaUltimoAbonoConsulta[0]->created_at)->format('Y-m-d'); //Obtener la fecha de este ultimo abono
                }

                $solicitudAbonoMinimo = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato  = '$idContrato' AND (a.tipo = 7) ORDER BY a.created_at DESC LIMIT 1");

                //Validacion de si tiene o no poliza, en caso de tenerla verificar si aun esta vigente
                $consultaPolizaActiva = DB::select("SELECT cp.id_contrato, cp.id_producto, p.nombre, cp.created_at FROM contratoproducto cp
                                                        INNER JOIN producto p ON p.id = cp.id_producto
                                                        WHERE cp.id_contrato = '$idContrato'
                                                        AND p.id_tipoproducto = 2 ORDER BY cp.created_at DESC LIMIT 1");

                $polizaActiva = true;
                //Tiene una poliza?
                if($consultaPolizaActiva){
                    //Si tiene una poliza el contrato - Validar vigencia del producto
                    $fechaActual = Carbon::now();
                    $fechaPoliza = $consultaPolizaActiva[0]-> created_at;
                    $diasCreacionPoliza = DB::select("SELECT DATEDIFF('$fechaActual', '$fechaPoliza') AS diferencia");

                    if($diasCreacionPoliza[0]->diferencia > 365){
                        //Si la fecha de creacion de la poliza pasa de un año
                        $polizaActiva = false;
                    }
                }else{
                    //No cuenta con poliza
                    $polizaActiva = false;
                }

                //Actualizar datos tabla contratoslistatemporales
                $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                /* Datos para ticket abono*/
                $sucursal = DB::select("SELECT f.ciudad, f.telefonoatencionclientes, f.whatsapp FROM franquicias f WHERE f.id = '$idFranquicia'");
                $idUsuario = Auth::user()->id;
                $usuario = DB::select("SELECT u.name FROM users u WHERE u.id = '$idUsuario'");
                $nombreUsuario = strtoupper($usuario[0]->name);
                $banderaAProducto = false;
                //Solo se tomaran 10 caracteres para el nombre del usuario que realizo el cobro
                if(strlen($nombreUsuario) > 10){
                    $nombreTemporal = substr($nombreUsuario,0,10);
                    $nombreUsuario = $nombreTemporal;
                }

                //Cantidad ultimo abono generado, folio y fecha creacion abono
                $abono = 0;
                $folioAbono = null;
                $fechaImprimirTicket = null;
                $totalAnterior = null;
                $fechaUltimoAbono =null;
                $ultimoAbono = DB::select("SELECT id, folio, created_at, abono, tipoabono FROM abonos WHERE id_contrato ='$idContrato'
                                                                    AND metodopago != '1' ORDER BY created_at DESC LIMIT 1");
                //Abonos que no pertenecen a compra de productos
                $folioOIdUltimoAbono = null;
                //Si el contrato tiene abonos
                if($ultimoAbono != null){
                    //Traer el ultimo abono generado - Verificar cuantos abonos existen con el mismo folio
                    $folioAbono = $ultimoAbono[0]->folio;
                    $fechaUltimoAbono = $ultimoAbono[0]->created_at;
                    if($folioAbono == null){
                        //Verificar el tipo de abono del ultimo abono generado
                        if($ultimoAbono[0]->tipoabono != 7){
                            //Es un abono diferente a la compra de productos - Obtener todos los abonos del mismo dia diferentes a compra de productos
                            $abonosExistentes = DB::select("SELECT * FROM abonos a WHERE a.folio is null AND a.id_contrato = '$idContrato'
                                                              AND tipoabono != 7 AND a.created_at = '$fechaUltimoAbono' ORDER BY a.created_at DESC");

                        }else{
                            //Fue la compra de un producto - Obtener el ultimo producto vendido
                            $abonosExistentes = DB::select("SELECT * FROM abonos a WHERE a.folio is null AND a.id_contrato = '$idContrato'
                                                              AND a.created_at = '$fechaUltimoAbono' AND tipoabono = 7 ORDER BY a.created_at LIMIT 1");
                            $totalAnterior = "$".$contrato[0]->total.".0";
                            $banderaAProducto = true;
                        }
                        $folioOIdUltimoAbono = $ultimoAbono[0]->id;
                    }else{
                        //Un abono con folio - Filtar por el folio del abono, id Contrato
                        $abonosExistentes = DB::select("SELECT * FROM abonos a WHERE a.folio = '$folioAbono' AND a.id_contrato = '$idContrato'");
                        $folioOIdUltimoAbono = $ultimoAbono[0]->folio;
                    }

                    //Sumar todos los abonos obtenidos que coicidan con el folio o la hora de registro para obtener una sola cantidad abonada
                    foreach ($abonosExistentes as $abonoExistente){
                        $abono = ($abono + $abonoExistente->abono);
                    }

                    if($ultimoAbono[0]->tipoabono != 7){
                        //Si es un abono diferente a la compra de un producto - Saldo anterior = total actual + abonos
                        $totalAnterior = "$".($contrato[0]->total + $abono).".0";
                    }

                    //Concatenar simbolo de pesos al abono
                    $abono ="$". $abono;

                    //Validar si bandera abono producto es verdadera
                    if($banderaAProducto){
                        //EL abono corresponde a la compra de producto - Concatenar leyenda
                        $abono = $abono . " (DE PRODUCTO)";
                    }

                    //Verificar si tiene folio el abono
                    $folioAbono = ($ultimoAbono[0]->folio != null)?$ultimoAbono[0]->folio:"S / F";

                    //Fecha de impresion del ticket
                    $fechaImprimirTicket = Carbon::now();
                    // $fechaImprimirTicket = $ultimoAbono[0]->created_at;
                }

                //Cantidad con letra
                $formatoLetra = new NumberFormatter("es", NumberFormatter::SPELLOUT);
                $totalAbonoNumero = $contrato[0]->total;
                $totalAbonoLetra = strtoupper($formatoLetra->format($totalAbonoNumero)) . " PESOS";

                //Cobradores con el contrato asignado a su tabular
                $cobradoresContrato = DB::select("SELECT u.name, u.ultimaconexion FROM  contratostemporalessincronizacion c
                                                        INNER JOIN users u ON u.id = c.id_usuario
                                                        WHERE c.id = '$idContrato' AND  u.rol_id  = '4'");

                //Contrato lista negra
                $contratoListaNegra = DB::select("SELECT * FROM contratoslistanegra cln WHERE cln.id_contrato = '$idContrato' ORDER BY cln.created_at DESC LIMIT 1");

                //Datos para ticket solicitud de armazon
                $solicitudArmazonTicket = DB::select("SELECT a.indice,(SELECT f.ciudad FROM franquicias f WHERE f.id = a.id_franquicia) AS sucursal, a.tipo, a.id_contrato, id_armazon,
                                                                    a.created_at AS fecha_solicitud,(SELECT u.name FROM users u WHERE u.id = a.id_usuarioC) AS usuario_solicitud,
                                                                    (select nombre from producto p where p.id = aal.id_armazon) as armazon,
                                                                    (select color from producto p where p.id = aal.id_armazon) as color,
                                                                    UPPER(aal.observaciones) AS observaciones
                                                                    FROM autorizaciones a
                                                                    inner join autorizacionarmazonlaboratorio aal on aal.id_autorizacion = a.indice
                                                                    WHERE a.id_contrato = '$idContrato' AND a.tipo IN (8,9,10)
                                                                    ORDER BY  fecha_solicitud DESC LIMIT 1");

                return view('administracion.historialclinico.tabla', ['historialesclinicos' => $historialesclinicos, 'historialcontrato' => $historialcontrato, 'abonos' => $abonos,
                    'promociones' => $promociones, 'promocioncontrato' => $promocioncontrato, 'idFranquicia' => $idFranquicia, 'contratoproducto' => $contratoproducto,
                    'idContrato' => $idContrato, 'contrato' => $contrato, 'productos' => $productos, 'now' => $now, 'contrashijos' => $contrashijos, 'promo' => $promo,
                    'contraspadre5' => $contraspadre5, 'contraspadre2' => $contraspadre2, 'contratosterminadostodos' => $contratosterminadostodos,
                    'abonostarjetameses' => $abonostarjetameses, 'hoyNumero' => $hoyNumero, 'folioOIdUltimoAbonoEliminar' => $folioOIdUltimoAbonoEliminar,
                    'solicitudCancelar' => $solicitudCancelar, 'solicitudAumentarDisminuir' => $solicitudAumentarDisminuir, 'periodo' => $periodo, 'fechaultimoabono' => $fechaultimoabono,
                    'solicitudAbonoMinimo' => $solicitudAbonoMinimo, 'tieneHistorialGarantia' => $tieneHistorialGarantia, 'solicitudArmazon' => $solicitudArmazon,
                    'polizaActiva' => $polizaActiva, 'sucursal' => $sucursal, 'nombreUsuario' => $nombreUsuario, 'abono' => $abono, 'folioAbono' => $folioAbono,
                    'fechaImprimirTicket' => $fechaImprimirTicket,'totalAnterior' => $totalAnterior,'totalAbonoLetra' => $totalAbonoLetra,
                    'folioOIdUltimoAbono' => $folioOIdUltimoAbono, 'fechaUltimoAbono' => $fechaUltimoAbono, 'fechaHoraUltimoAbonoEliminar' => $fechaHoraUltimoAbonoEliminar,
                    'solicitudSupervisar' => $solicitudSupervisar, 'historialFotosContrato' => $historialFotosContrato, 'cobradoresContrato' => $cobradoresContrato,
                    'contratoListaNegra' => $contratoListaNegra, 'solicitudArmazonTicket' => $solicitudArmazonTicket]);

            } else {

                if (((Auth::user()->rol_id) == 7)) {
                    //Rol director
                    $contratoOtraSucursal = DB::select("SELECT id_franquicia FROM contratos WHERE id = '$idContrato'");

                    if ($contratoOtraSucursal != null) {
                        //Existe el contrato en otra sucursal
                        $idFranquiciaOtraSucursal = $contratoOtraSucursal[0]->id_franquicia;
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquiciaOtraSucursal, 'idContrato' => $idContrato]);
                    } else {
                        //El contrato no existe en otra sucursal
                        return back()->with("alerta", "El contrato no existe/ esta mal escrito");
                    }

                } else {
                    //Rol diferente de director
                    return back()->with("alerta", "El contrato no existe/ esta mal escrito/ no pertenece a esta sucursal.");
                }

            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function contratoactualizar($idFranquicia, $idContrato)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {

            $solicitudAutorizacion = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato = '$idContrato' AND a.tipo = '0' ORDER BY a.created_at DESC LIMIT 1");

            //Solicitud de cambio de paquete
            $solicitudCambioPaquete = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato  = '$idContrato' AND a.tipo = 4 ORDER BY a.created_at DESC LIMIT 1");

            //Actualizar contrato en tabla contratostemporalessincronizacion
            $contratosGlobal = new contratosGlobal;
            $contratosGlobal::actualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato);

            //Eliminar garantias repetidas del contrato
            $contratosGlobal::eliminarGarantiasRepetidasTabla($idContrato);

            //Actualizar contrato en tabla contratoslistatemporales
            $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

            $contrato = DB::select("SELECT c.nombre,c.calle,c.numero,c.depto,c.alladode,c.frentea,c.coordenadas,c.entrecalles,c.colonia,c.localidad,c.telefono,c.casatipo,
                                          c.casacolor,c.nombrereferencia, c.telefonoreferencia,c.correo,c.fotoine,c.fotoineatras,c.pagare, c.fotootros, c.fotocasa,
                                          c.comprobantedomicilio,c.tarjeta,c.fotoine,c.fotoineatras,c.fotocasa,c.comprobantedomicilio, c.tarjetapensionatras,c.id,c.nota,
                                          c.estatus_estadocontrato,(SELECT u.name FROM users u WHERE u.id = c.id_optometrista) as nombreopto,c.id_optometrista,c.id_zona,
                                          (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia,
                                          c.id_usuariocreacion,c.nombre_usuariocreacion, c.fechaentrega, c.calleentrega, c.numeroentrega, c.deptoentrega, c.alladodeentrega,
                                          c.frenteaentrega, c.entrecallesentrega, c.coloniaentrega, c.localidadentrega, c.casatipoentrega, c.casacolorentrega, c.alias,
                                          c.observacionfotoine, c.observacionfotoineatras, c.observacionfotocasa, c.observacioncomprobantedomicilio, c.observacionpagare, c.observacionfotootros
                                          FROM contratos c WHERE c.id_franquicia = '$idFranquicia' AND c.id = '$idContrato'");
            if ($contrato != null) {

                $zonas = DB::select("SELECT * FROM zonas where id_franquicia = '$idFranquicia' ORDER BY zona");
                $promociones = DB::select("SELECT * FROM promocion where id_franquicia = '$idFranquicia' AND status != 2");
                $optometristas = DB::select("SELECT u.ID,u.NAME
                                    FROM users u
                                    INNER JOIN usuariosfranquicia uf
                                    ON uf.id_usuario = u.id
                                    WHERE uf.id_franquicia = '$idFranquicia'
                                    AND u.rol_id = 12 ORDER BY u.name");

                $asistentes = DB::select("SELECT u.ID,u.NAME
                                    FROM users u
                                    INNER JOIN usuariosfranquicia uf
                                    ON uf.id_usuario = u.id
                                    WHERE uf.id_franquicia = '$idFranquicia'
                                    AND u.rol_id IN (12,13) ORDER BY u.name");

                $franquiciaAdmin = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");

                if ($contrato[0]->fechaentrega != null) {
                    $contrato[0]->cuentaregresivafechaentrega = 15 - Carbon::parse($contrato[0]->fechaentrega)->diffInDays(Carbon::now()->format('Y-m-d'));
                } else {
                    $contrato[0]->cuentaregresivafechaentrega = -1;
                }

                $contrato[0]->garantiacanceladaelmismodia = false;
                $actualizarHistorialBase = false;
                $actualizarHistorialGarantia = false;

                //Obtener garantias
                $garantias = DB::select("SELECT estadogarantia, updated_at FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia IN (0,1,2,4) ORDER BY created_at DESC LIMIT 1");
                $bandera = true;
                if ($garantias != null) {
                    foreach ($garantias as $garantia) {
                        $estadogarantia = $garantia->estadogarantia;
                        switch ($estadogarantia) {
                            case 0: //Reportada
                                $bandera = false;
                                break;
                            case 1: //Asignada
                                //Se utiliza para paquetes DORADO 2 que ha sido asignada una garantia a un historial y para que se pueda asignar la otra garantia al otro historial
                                $historial = DB::select("SELECT (SELECT p.nombre FROM paquetes p WHERE p.id = hc.id_paquete AND p.id_franquicia = '$idFranquicia' LIMIT 1) as paquete
                                                        FROM historialclinico hc WHERE hc.id_contrato = '$idContrato' ORDER BY hc.created_at");
                                if ($historial != null) {
                                    //Existe historial
                                    if ($historial[0]->paquete = 'DORADO 2') {
                                        //Es paquete DORADO 2
                                        $contrato[0]->garantiacanceladaelmismodia = true;
                                    }
                                }
                                break;
                            case 4: //Cancelada
                                if (Carbon::parse($garantia->updated_at)->format('Y-m-d') == Carbon::now()->format('Y-m-d')) {
                                    //La fecha de cancelacion es igual al dia actual
                                    $contrato[0]->garantiacanceladaelmismodia = true;
                                }
                                break;
                        }
                    }
                    if($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9){
                        //Contrato en NO TERMINADO - TERMINADO - PROCESO DE APROBACION
                        $actualizarHistorialGarantia = true;
                    }
                }else{
                    //Contrato sin garantia
                    if($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9){
                        //Contrato en NO TERMINADO - TERMINADO - PROCESO DE APROBACION
                        $actualizarHistorialBase = true;
                    }
                }

                $historialesBase = array();
                $historialesActivos = array();
                $historialesCancelados = array();
                $historialesCambio = array();
                $historialesGarantiaTerminada = array();
                $arrayHistoriales = array();

                if(!$bandera){
                    //Tiene garantia el contrato
                    $historiales = DB::select("SELECT hc.id,hc.esfericoder,hc.cilindroder,hc.ejeder,hc.addder,hc.altder,hc.esfericoizq,hc.cilindroizq,hc.ejeizq,hc.addizq,
                                                        hc.altizq,(SELECT nombre FROM producto p WHERE p.id = hc.id_producto) as armazon, hc.id_producto,
                                                        hc.material,hc.materialotro,hc.bifocal,hc.fotocromatico,hc.ar,hc.tinte,hc.blueray,hc.otroT,hc.tratamientootro,hc.observaciones,
                                                        hc.observacionesinterno,hc.tipo,hc.bifocalotro,hc.costomaterial,hc.costobifocal,hc.costotratamiento, hc.fotoarmazon,
                                                        (SELECT color FROM producto p WHERE p.id = hc.id_producto) as colorarmazon,
                                                        (SELECT p.nombre FROM paquetes p WHERE p.id = hc.id_paquete AND p.id_franquicia = '$idFranquicia' LIMIT 1) as paquete,
                                                        (SELECT COUNT(g.id) FROM garantias g WHERE g.id_contrato = hc.id_contrato AND g.id_historial = hc.id) as numGarantias,
                                                        (SELECT g.id_historial FROM garantias g WHERE g.id_contrato = hc.id_contrato AND g.id_historialgarantia = hc.id) as idhistorialpadre,
                                                        (SELECT u.name FROM users u WHERE id = (SELECT g.id_optometrista FROM garantias g WHERE g.id_contrato = hc.id_contrato
                                                        AND g.id_historial = hc.id AND g.estadogarantia = 1)) as optometristaasignado,
                                                        (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = hc.id_contrato AND g.id_historialgarantia = hc.id) as estadogarantia,
                                                        (SELECT hsc.esfericoder FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id) as hscesfericoder,
                                                        (SELECT hsc.cilindroder FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id) AS hsccilindroder,
                                                        (SELECT hsc.ejeder FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id) as hscejeder,
                                                        (SELECT hsc.addder FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id) as hscaddder,
                                                        (SELECT hsc.esfericoizq FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id) as hscesfericoizq,
                                                        (SELECT hsc.cilindroizq FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id) as hsccilindroizq,
                                                        (SELECT hsc.ejeizq FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id) as hscejeizq,
                                                        (SELECT hsc.addizq FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id) as hscaddizq,
                                                        hc.policarbonatotipo, estilotinte as estilotinte, hc.polarizado as polarizado, hc.espejo as espejo,
                                                        hc.id_tratamientocolortinte, hc.id_tratamientocolorpolarizado, hc.id_tratamientocolorespejo,
                                                        (SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = hc.id_tratamientocolortinte) as colortinte,
                                                        (SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = hc.id_tratamientocolorpolarizado) as colorpolarizado,
                                                        (SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = hc.id_tratamientocolorespejo) as colorespejo
                                                        FROM historialclinico hc WHERE hc.id_contrato = '$idContrato' ORDER BY hc.created_at");

                }else{
                    //Historial sin garantia
                    $historiales = DB::select("SELECT hc.id,hc.esfericoder,hc.cilindroder,hc.ejeder,hc.addder,hc.altder,hc.esfericoizq,hc.cilindroizq,hc.ejeizq,hc.addizq,
                                                        hc.altizq,(SELECT nombre FROM producto p WHERE p.id = hc.id_producto) as armazon, hc.id_producto,
                                                        hc.material,hc.materialotro,hc.bifocal,hc.fotocromatico,hc.ar,hc.tinte,hc.blueray,hc.otroT,hc.tratamientootro,hc.observaciones,
                                                        hc.observacionesinterno,hc.tipo,hc.bifocalotro,hc.costomaterial,hc.costobifocal,hc.costotratamiento, hc.fotoarmazon,
                                                        (SELECT color FROM producto p WHERE p.id = hc.id_producto) as colorarmazon,
                                                        (SELECT p.nombre FROM paquetes p WHERE p.id = hc.id_paquete AND p.id_franquicia = '$idFranquicia' LIMIT 1) as paquete,
                                                        (SELECT COUNT(g.id) FROM garantias g WHERE g.id_contrato = hc.id_contrato AND g.id_historial = hc.id) as numGarantias,
                                                        (SELECT g.id_historial FROM garantias g WHERE g.id_contrato = hc.id_contrato AND g.id_historialgarantia = hc.id) as idhistorialpadre,
                                                        (SELECT u.name FROM users u WHERE id = (SELECT g.id_optometrista FROM garantias g WHERE g.id_contrato = hc.id_contrato
                                                        AND g.id_historial = hc.id AND g.estadogarantia IN (1,2))) as optometristaasignado,
                                                        (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = hc.id_contrato AND g.id_historialgarantia = hc.id) as estadogarantia,
                                                        (SELECT g.id FROM garantias g WHERE g.id_contrato = hc.id_contrato AND g.id_historial = hc.id AND g.estadogarantia IN (1,2)) as cancelargarantia,
                                                        (SELECT hsc.esfericoder FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id) as hscesfericoder,
                                                        (SELECT hsc.cilindroder FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id) AS hsccilindroder,
                                                        (SELECT hsc.ejeder FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id) as hscejeder,
                                                        (SELECT hsc.addder FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id) as hscaddder,
                                                        (SELECT hsc.esfericoizq FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id) as hscesfericoizq,
                                                        (SELECT hsc.cilindroizq FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id) as hsccilindroizq,
                                                        (SELECT hsc.ejeizq FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id) as hscejeizq,
                                                        (SELECT hsc.addizq FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id) as hscaddizq,
                                                        hc.policarbonatotipo, estilotinte as estilotinte, hc.polarizado as polarizado, hc.espejo as espejo,
                                                        hc.id_tratamientocolortinte, hc.id_tratamientocolorpolarizado, hc.id_tratamientocolorespejo,
                                                        (SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = hc.id_tratamientocolortinte) as colortinte,
                                                        (SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = hc.id_tratamientocolorpolarizado) as colorpolarizado,
                                                        (SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = hc.id_tratamientocolorespejo) as colorespejo
                                                        FROM historialclinico hc WHERE hc.id_contrato = '$idContrato' ORDER BY hc.created_at");

                }

                foreach ($historiales as $historial){

                    switch ($historial->tipo){
                        case 0:
                            //Historiales de tipo 0 -> historial base
                            array_push($historialesBase, $historial);
                            //Ingresaremos los idHistorial al arreglo - Funcionarara para crear los eventos de componentes en las pestañas de historiales
                            array_push($arrayHistoriales,$historial->id);
                            break;
                        case 1:
                            //Historiales de tipo 1 -> historial de garantia
                            switch ($historial->estadogarantia){
                                case 2:
                                    //Garantia creada
                                    array_push($historialesActivos,$historial);
                                    //Ingresaremos los idHistorial al arreglo - Funcionarara para crear los eventos de componentes en las pestañas de historiales
                                    array_push($arrayHistoriales,$historial->id);
                                    break;
                                case 3:
                                    //Garantia liberada por laboratorio
                                    array_push($historialesGarantiaTerminada, $historial);
                                    //Ingresaremos los idHistorial al arreglo - Funcionarara para crear los eventos de componentes en las pestañas de historiales
                                    array_push($arrayHistoriales,$historial->id);
                                    break;
                                case 4:
                                    //Garantia en estatus de cancelada
                                    array_push($historialesCancelados,$historial);
                            }
                            break;
                        case 2:
                            //Historiales de tipo 2 -> Cambio de paquete
                            array_push($historialesCambio, $historial);
                            break;
                    }
                }

                //Productos tipo armazon
                $armazones = DB::select("SELECT * FROM producto WHERE id_tipoproducto = '1' order by nombre");

                $numTotalGarantias = DB::select("SELECT COUNT(id) as numTotalGarantias FROM garantias WHERE id_contrato = '$idContrato'");

                $paquetes = DB::select("SELECT id, nombre FROM paquetes WHERE id_franquicia = '$idFranquicia'");

                $datosDiagnosticoHistorial = DB::select("SELECT hc.edad, hc.diagnostico, hc.ocupacion, hc.diabetes, hc.hipertension,
                                                    hc.embarazada, hc.durmioseisochohoras, hc.actividaddia, hc.problemasojos, hc.dolor,
                                                    hc.ardor, hc.golpeojos, hc.otroM, hc.molestiaotro, hc.ultimoexamen
                                                    FROM historialclinico hc
                                                    WHERE id_contrato = '$idContrato' ORDER BY created_at ASC LIMIT 1");

                //Cobradores con el contrato asignado a su tabular
                $cobradoresContrato = DB::select("SELECT u.name, u.ultimaconexion FROM  contratostemporalessincronizacion c
                                                        INNER JOIN users u ON u.id = c.id_usuario
                                                        WHERE c.id = '$idContrato' AND  u.rol_id  = '4'");

                //Colores tratamientos
                $coloresTratamientos = DB::select("SELECT * FROM tratamientoscolores tc WHERE tc.id_franquicia = '$idFranquicia'");

                //Contrato lista negra
                $contratoListaNegra = DB::select("SELECT * FROM contratoslistanegra cln WHERE cln.id_contrato = '$idContrato' ORDER BY cln.created_at DESC LIMIT 1");

                //Solicitud de lista negra
                $solicitudAutorizacionListaNegra = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato  = '$idContrato' AND a.tipo = 15 AND a.estatus != 2 ORDER BY a.created_at DESC LIMIT 1");

                //Lista de promociones de tipo 1 (Reposicion)
                $promocionesReposicion = DB::select("SELECT * FROM promocion p WHERE p.id_franquicia = '$idFranquicia' AND p.tipo = 1 AND status != 2");

                return view('administracion.contrato.actualizarcontrato',
                    [
                        'idFranquicia' => $idFranquicia,
                        'idContrato' => $idContrato,
                        'contrato' => $contrato,
                        'promociones' => $promociones,
                        'franquiciaAdmin' => $franquiciaAdmin,
                        'zonas' => $zonas,
                        'optometristas' => $optometristas,
                        'historialesBase' => $historialesBase,
                        'historialesActivos' => $historialesActivos,
                        'historialesCancelados' => $historialesCancelados,
                        'historialesCambio' => $historialesCambio,
                        'historialesGarantiaTerminada' => $historialesGarantiaTerminada,
                        'armazones' => $armazones,
                        'numTotalGarantias' => $numTotalGarantias,
                        'bandera' => $bandera,
                        'paquetes' => $paquetes,
                        'asistentes' => $asistentes,
                        'datosDiagnosticoHistorial' => $datosDiagnosticoHistorial,
                        'solicitudAutorizacion' => $solicitudAutorizacion,
                        'solicitudCambioPaquete' => $solicitudCambioPaquete,
                        'garantias' => $garantias,
                        'cobradoresContrato' => $cobradoresContrato,
                        'actualizarHistorialBase' => $actualizarHistorialBase,
                        'actualizarHistorialGarantia' => $actualizarHistorialGarantia,
                        'coloresTratamientos'=> $coloresTratamientos,
                        'arrayHistoriales' => $arrayHistoriales,
                        'contratoListaNegra' => $contratoListaNegra,
                        'solicitudAutorizacionListaNegra' => $solicitudAutorizacionListaNegra,
                        'promocionesReposicion' => $promocionesReposicion
                    ]);

            } else {

                if (((Auth::user()->rol_id) == 7)) {
                    //Rol director
                    $contratoOtraSucursal = DB::select("SELECT id_franquicia FROM contratos WHERE id = '$idContrato'");

                    if ($contratoOtraSucursal != null) {
                        //Existe el contrato en otra sucursal
                        $idFranquiciaOtraSucursal = $contratoOtraSucursal[0]->id_franquicia;
                        return redirect()->route('contratoactualizar', ['idFranquicia' => $idFranquiciaOtraSucursal, 'idContrato' => $idContrato]);
                    } else {
                        //El contrato no existe en otra sucursal
                        return back()->with("alerta", "El contrato no existe/ esta mal escrito");
                    }

                } else {
                    //Rol diferente de director
                    return back()->with("alerta", "El contrato no existe/ esta mal escrito/ no pertenece a esta sucursal.");
                }

            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function contratoeditar($idFranquicia, $idContrato)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {

            request()->validate([
                'zona' => 'required',
                'nombre' => 'required|string|max:255',
                'calle' => 'required|string|max:255',
                'numero' => 'required|string|min:1|max:255',
                'departamento' => 'required|string|max:255',
                'alladode' => 'required|string|max:255',
                'frentea' => 'required|string|max:255',
                'entrecalles' => 'required|string|max:255',
                'colonia' => 'required|string|max:255',
                'localidad' => 'required|string|max:255',
                'telefono' => 'required|string|size:10|regex:/[0-9]/',
                'tr' => 'required|string|size:10|regex:/[0-9]/',
                'casatipo' => 'required|string|max:255',
                'nr' => 'required|string|max:255',
                'casacolor' => 'required|string|max:255',
                'fotoine' => 'nullable|image|mimes:jpg|mimes:jpeg',
                'fotocasa' => 'nullable|image|mimes:jpg|mimes:jpeg',
                'tarjeta' => 'nullable|image|mimes:jpg|mimes:jpeg',
                'pagare' => 'nullable|image|mimes:jpg|mimes:jpeg',
                // 'promocion'=>'nullable|inte',
                'comprobantedomicilio' => 'nullable|image|mimes:jpg|mimes:jpeg',
                'tarjeta' => 'nullable|image|mimes:jpg|mimes:jpeg',
                'tarjetapensionatras' => 'nullable|image|mimes:jpg|mimes:jpeg',
                'fotootros' => 'nullable|image|mimes:jpg|mimes:jpeg',
                'calleentrega' => 'required|string|max:255',
                'numeroentrega' => 'required|string|min:1|max:255',
                'departamentoentrega' => 'required|string|max:255',
                'alladodeentrega' => 'required|string|max:255',
                'frenteaentrega' => 'required|string|max:255',
                'entrecallesentrega' => 'required|string|max:255',
                'coloniaentrega' => 'required|string|max:255',
                'localidadentrega' => 'required|string|max:255',
                'casatipoentrega' => 'required|string|max:255',
                'casacolorentrega' => 'required|string|max:255',
                'alias' => 'required|string|max:255',
            ]);

            $correo = request('correo');
            //si correo electronico es diferente de vacio - validar
            if($correo != null){
                if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    return back()->with('alerta', 'Dirección de correo electronico incorrecto.');
                }
            }

            try {

                $contrato = DB::select("SELECT * FROM contratos WHERE id = '$idContrato' AND id_franquicia = '$idFranquicia'");
                $fotoine = "";
                $fotoineatras = "";
                $fotocasa = "";
                $comprobantedomicilio = "";
                $fotopagare = "";
                $tarjeta = "";
                $tarjetapensionatras = "";
                $fotootros = "";
                $ine = false;
                $ineatras = false;
                $casa = false;
                $comprobante = false;
                $tarjetaP = false;
                $tarjetaPatras = false;
                $otros = false;
                $randomId2 = $this->getHistorialContratoId();

                //Validacion de tamaño de archivos
                $contratosGlobal = new contratosGlobal;
                $contador = 0;
                $nombreArchivos = "";
                $camposActualizados = "";

                if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('fotoine'))){
                    $nombreArchivos = $nombreArchivos . " Foto INE frente,";
                    $contador = $contador + 1;
                }
                if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('fotoineatras'))){
                    $nombreArchivos = $nombreArchivos . " Foto INE atras,";
                    $contador = $contador + 1;
                }
                if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('pagare'))){
                    $nombreArchivos = $nombreArchivos . " Pagare,";
                    $contador = $contador + 1;
                }
                if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('fotocasa'))){
                    $nombreArchivos = $nombreArchivos . " Foto de la casa,";
                    $contador = $contador + 1;
                }
                if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('comprobantedomicilio'))){
                    $nombreArchivos = $nombreArchivos . " Comprobante de domicilio,";
                    $contador = $contador + 1;
                }
                if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('tarjeta'))){
                    $nombreArchivos = $nombreArchivos . " Tarjeta pension frente,";
                    $contador = $contador + 1;
                }
                if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('tarjetapensionatras'))){
                    $nombreArchivos = $nombreArchivos . " Tarjeta pension atras,";
                    $contador = $contador + 1;
                }
                if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('fotootros'))){
                    $nombreArchivos = $nombreArchivos . " Foto otros";
                    $contador = $contador + 1;
                }

                //Verificar si 1 o mas archivos no cumplen con el tamaño maximo
                if($contador > 0){
                    $nombreArchivos = trim($nombreArchivos, ',');
                    if($contador == 1){
                        //Solo un archivo es demasiado pesado
                        return back()->with('alerta', "Verifica el archivo: " .$nombreArchivos.". El tamaño permitido para los archivos es maximo de 1MB.");
                    }else{
                        //2 o mas archivos sobrepasan el peso de 1MB
                        return back()->with('alerta', "Verifica los siguientes archivos: " .$nombreArchivos.". El tamaño permitido para los archivos es maximo de 1MB.");
                    }
                }

                //Verificar si se actualizo algun campo del contrato
                if($contrato[0]->id_zona != request('zona')){
                    //Actualizaron zona
                    $camposActualizados = "Zona,";
                }
                if($contrato[0]->coordenadas != request('coordenadas')){
                    //Actualizaron coordenadas
                    $camposActualizados = "Ubicacion,";
                }
                if($contrato[0]->nombre != request('nombre')){
                    //Actualizaron nombre paciente
                    $camposActualizados = $camposActualizados . "Nombre paciente,";
                }
                if($contrato[0]->alias != request('alias')){
                    //Actualizaron alias paciente
                    $camposActualizados = $camposActualizados . "Alias,";
                }
                if($contrato[0]->telefono != request('telefono')){
                    //Actualizaron telefono paciente
                    $camposActualizados = $camposActualizados . "Telefono paciente,";
                }
                if($contrato[0]->telefonoreferencia != request('tr')){
                    //Actualizaron telefono referencia
                    $camposActualizados = $camposActualizados . "Telefono referencia,";
                }
                if($contrato[0]->nombrereferencia != request('nr')){
                    //Actualizaron nombre referencia
                    $camposActualizados = $camposActualizados . "Nombre referencia,";
                }
                if($contrato[0]->correo != request('correo')){
                    //Actualizaron correo
                    $camposActualizados = $camposActualizados . "Correo electronico,";
                }
                if($contrato[0]->localidad != request('localidad')){
                    //Actualizaron Localidad venta
                    $camposActualizados = $camposActualizados . "Localidad (Venta),";
                }
                if($contrato[0]->colonia != request('colonia')){
                    //Actualizaron Colonia venta
                    $camposActualizados = $camposActualizados . "Colonia (Venta),";
                }
                if($contrato[0]->calle != request('calle')){
                    //Actualizaron Calle venta
                    $camposActualizados = $camposActualizados . "Calle (Venta),";
                }
                if($contrato[0]->entrecalles != request('entrecalles')){
                    //Actualizaron Entre calles venta
                    $camposActualizados = $camposActualizados . "Entre calles (Venta),";
                }
                if($contrato[0]->numero != request('numero')){
                    //Actualizaron Entre Numero venta
                    $camposActualizados = $camposActualizados . "Numero de domicilio (Venta),";
                }
                if($contrato[0]->depto != request('departamento')){
                    //Actualizaron Departamento venta
                    $camposActualizados = $camposActualizados . "Departamento (Venta),";
                }
                if($contrato[0]->alladode != request('alladode')){
                    //Actualizaron A lado de - venta
                    $camposActualizados = $camposActualizados . "A lado de (Venta),";
                }
                if($contrato[0]->frentea != request('frentea')){
                    //Actualizaron Frente a - venta
                    $camposActualizados = $camposActualizados . "Frente a (Venta),";
                }
                if($contrato[0]->casatipo != request('casatipo')){
                    //Actualizaron Tipo de casa - venta
                    $camposActualizados = $camposActualizados . "Tipo de casa (Venta),";
                }
                if($contrato[0]->casacolor != request('casacolor')){
                    //Actualizaron casa color - venta
                    $camposActualizados = $camposActualizados . "Color de casa (Venta),";
                }
                if($contrato[0]->localidadentrega != request('localidadentrega')){
                    //Actualizaron Localidad Entrega
                    $camposActualizados = $camposActualizados . "Localidad (Entrega),";
                }
                if($contrato[0]->coloniaentrega != request('coloniaentrega')){
                    //Actualizaron Colonia Entrega
                    $camposActualizados = $camposActualizados . "Colonia (Entrega),";
                }
                if($contrato[0]->calleentrega != request('calleentrega')){
                    //Actualizaron Calle Entrega
                    $camposActualizados = $camposActualizados . "Calle (Entrega),";
                }
                if($contrato[0]->entrecallesentrega != request('entrecallesentrega')){
                    //Actualizaron Entre calles Entrega
                    $camposActualizados = $camposActualizados . "Entre calles (Entrega),";
                }
                if($contrato[0]->numeroentrega != request('numeroentrega')){
                    //Actualizaron numero Entrega
                    $camposActualizados = $camposActualizados . "Numero de domicilio (Entrega),";
                }
                if($contrato[0]->deptoentrega != request('departamentoentrega')){
                    //Actualizaron numero Entrega
                    $camposActualizados = $camposActualizados . "Departamento (Entrega),";
                }
                if($contrato[0]->alladodeentrega != request('alladodeentrega')){
                    //Actualizaron A lado de - Entrega
                    $camposActualizados = $camposActualizados . "A lado de (Entrega),";
                }
                if($contrato[0]->frenteaentrega != request('frenteaentrega')){
                    //Actualizaron Frente a - Entrega
                    $camposActualizados = $camposActualizados . "Frente a (Entrega),";
                }
                if($contrato[0]->casatipoentrega != request('casatipoentrega')){
                    //Actualizaron Tipo casa - Entrega
                    $camposActualizados = $camposActualizados . "Tipo de casa (Entrega),";
                }
                if($contrato[0]->casacolorentrega != request('casacolorentrega')){
                    //Actualizaron Color de casa - Entrega
                    $camposActualizados = $camposActualizados . "Color de casa (Entrega),";
                }
                if($contrato[0]->observacionfotoine != request('observacionfotoine')){
                    //Actualizaron observacionfotoine
                    $camposActualizados = $camposActualizados . "Observación foto ine frente,";
                }
                if($contrato[0]->observacionfotoineatras != request('observacionfotoineatras')){
                    //Actualizaron observacionfotoineatras
                    $camposActualizados = $camposActualizados . "Observación foto ine atras,";
                }
                if($contrato[0]->observacionfotocasa != request('observacionfotocasa')){
                    //Actualizaron observacionfotocasa
                    $camposActualizados = $camposActualizados . "Observación foto casa,";
                }
                if($contrato[0]->observacioncomprobantedomicilio != request('observacioncomprobantedomicilio')){
                    //Actualizaron observacioncomprobantedomicilio
                    $camposActualizados = $camposActualizados . "Observación foto comprobante domicilio,";
                }
                if($contrato[0]->observacionpagare != request('observacionpagare')){
                    //Actualizaron observacionpagare
                    $camposActualizados = $camposActualizados . "Observación foto pagare,";
                }
                if($contrato[0]->observacionfotootros != request('observacionfotootros')){
                    //Actualizaron observacionfotootros
                    $camposActualizados = $camposActualizados . "Observación foto otros,";
                }

                //foto ine frente
                if (strlen($contrato[0]->fotoine) > 0) {
                    if (request()->hasFile('fotoine')) {
                        Storage::disk('disco')->delete($contrato[0]->fotoine);
                        $fotoBruta = 'Foto-ine-' . $contrato[0]->id . '-' . time() . '.' . request()->file('fotoine')->getClientOriginalExtension();
                        $fotoine = request()->file('fotoine')->storeAs('uploads/imagenes/contratos/fotoine', $fotoBruta, 'disco');
                        $ine = true;
                        $camposActualizados = "Foto INE frente,";
                    } else {
                        $fotoine = $contrato[0]->fotoine;
                        $ine = true;
                    }
                } else {
                    if (request()->hasFile('fotoine')) {
                        $fotoBruta = 'Foto-ine-' . $contrato[0]->id . '-' . time() . '.' . request()->file('fotoine')->getClientOriginalExtension();
                        $fotoine = request()->file('fotoine')->storeAs('uploads/imagenes/contratos/fotoine', $fotoBruta, 'disco');
                        $ine = true;
                        $camposActualizados = "Foto INE frente,";
                    } else {
                        $foto = false;
                    }
                }

                //foto ine atras
                if (strlen($contrato[0]->fotoineatras) > 0) {
                    if (request()->hasFile('fotoineatras')) {
                        Storage::disk('disco')->delete($contrato[0]->fotoineatras);
                        $fotoBruta = 'Foto-ine-atras-' . $contrato[0]->id . '-' . time() . '.' . request()->file('fotoineatras')->getClientOriginalExtension();
                        $fotoineatras = request()->file('fotoineatras')->storeAs('uploads/imagenes/contratos/fotoineatras', $fotoBruta, 'disco');
                        $ineatras = true;
                        $camposActualizados = $camposActualizados . "Foto INE atras,";
                    } else {
                        $fotoineatras = $contrato[0]->fotoineatras;
                        $ineatras = true;
                    }
                } else {
                    if (request()->hasFile('fotoineatras')) {
                        $fotoBruta = 'Foto-ine-atras-' . $contrato[0]->id . '-' . time() . '.' . request()->file('fotoineatras')->getClientOriginalExtension();
                        $fotoineatras = request()->file('fotoineatras')->storeAs('uploads/imagenes/contratos/fotoineatras', $fotoBruta, 'disco');
                        $ineatras = true;
                        $camposActualizados = $camposActualizados . "Foto INE atras,";
                    } else {
                        $ineatras = false;
                    }
                }

                //foto pagare
                if (strlen($contrato[0]->pagare) > 0) {
                    if (request()->hasFile('pagare')) {
                        Storage::disk('disco')->delete($contrato[0]->pagare);
                        $fotoBruta5 = 'Foto-ine-atras-' . $contrato[0]->id . '-' . time() . '.' . request()->file('pagare')->getClientOriginalExtension();
                        $fotopagare = request()->file('pagare')->storeAs('uploads/imagenes/contratos/pagare', $fotoBruta5, 'disco');
                        $pagare2 = true;
                        $camposActualizados = $camposActualizados . "Pagare,";
                    } else {
                        $fotopagare = $contrato[0]->pagare;
                        $pagare2 = true;
                    }
                } else {
                    if (request()->hasFile('pagare')) {
                        $fotoBruta5 = 'Foto-pagare-' . $contrato[0]->id . '-' . time() . '.' . request()->file('pagare')->getClientOriginalExtension();
                        $fotopagare = request()->file('pagare')->storeAs('uploads/imagenes/contratos/pagare', $fotoBruta, 'disco');
                        $pagare2 = true;
                        $camposActualizados = $camposActualizados . "Pagare,";
                    } else {
                        $pagare2 = false;
                    }
                }

                //foto casa
                if (strlen($contrato[0]->fotocasa) > 0) {
                    if (request()->hasFile('fotocasa')) {
                        Storage::disk('disco')->delete($contrato[0]->fotocasa);
                        $fotoBruta1 = 'Foto-casa-' . $contrato[0]->id . '-' . time() . '.' . request()->file('fotocasa')->getClientOriginalExtension();
                        $fotocasa = request()->file('fotocasa')->storeAs('uploads/imagenes/contratos/fotocasa', $fotoBruta1, 'disco');
                        $casa = true;
                        $camposActualizados = $camposActualizados . "Foto de la casa,";
                    } else {
                        $fotocasa = $contrato[0]->fotocasa;
                        $casa = true;
                    }
                } else {
                    if (request()->hasFile('fotocasa')) {
                        $fotoBruta1 = 'Foto-casa-' . $contrato[0]->id . '-' . time() . '.' . request()->file('fotocasa')->getClientOriginalExtension();
                        $fotocasa = request()->file('fotocasa')->storeAs('uploads/imagenes/contratos/fotocasa', $fotoBruta1, 'disco');
                        $casa = true;
                        $camposActualizados = $camposActualizados . "Foto de la casa,";
                    } else {
                        $casa = false;
                    }
                }
                //comprobante de domicilio
                if (strlen($contrato[0]->comprobantedomicilio) > 0) {
                    if (request()->hasFile('comprobantedomicilio')) {
                        Storage::disk('disco')->delete($contrato[0]->comprobantedomicilio);
                        $fotoBruta2 = 'Foto-comprobantedomicilio-' . $contrato[0]->id . '-' . time() . '.' . request()->file('comprobantedomicilio')->getClientOriginalExtension();
                        $comprobantedomicilio = request()->file('comprobantedomicilio')->storeAs('uploads/imagenes/contratos/comprobantedocmicilio', $fotoBruta2, 'disco');
                        $comprobante = true;
                        $camposActualizados = $camposActualizados . "Comprobante de domicilio,";
                    } else {
                        $comprobantedomicilio = $contrato[0]->comprobantedomicilio;
                        $comprobante = true;
                    }
                } else {
                    if (request()->hasFile('comprobantedocmicilio')) {
                        $fotoBruta2 = 'Foto-comprobantedomicilio-' . $contrato[0]->id . '-' . time() . '.' . request()->file('comprobantedomicilio')->getClientOriginalExtension();
                        $comprobantedomicilio = request()->file('comprobantedomicilio')->storeAs('uploads/imagenes/contratos/comprobantedocmicilio', $fotoBruta2, 'disco');
                        $comprobante = true;
                        $camposActualizados = $camposActualizados . "Comprobante de domicilio,";
                    } else {
                        $comprobante = false;
                    }
                }

                // tarjeta de pension frente
                if (strlen($contrato[0]->tarjeta) > 0) {
                    if (request()->hasFile('tarjeta')) {
                        Storage::disk('disco')->delete($contrato[0]->tarjeta);
                        $fotoBruta3 = 'Foto-tarjeta-' . $contrato[0]->id . '-' . time() . '.' . request()->file('tarjeta')->getClientOriginalExtension();
                        $tarjeta = request()->file('tarjeta')->storeAs('uploads/imagenes/contratos/tarjeta', $fotoBruta3, 'disco');
                        $tarjetaP = true;
                        $camposActualizados = $camposActualizados . "Tarjeta pension frente,";
                    } else {
                        $tarjeta = $contrato[0]->tarjeta;
                        $tarjetaP = true;
                    }
                } else {
                    if (request()->hasFile('tarjeta')) {
                        $fotoBruta3 = 'Foto-tarjeta-' . $contrato[0]->id . '-' . time() . '.' . request()->file('tarjeta')->getClientOriginalExtension();
                        $tarjeta = request()->file('tarjeta')->storeAs('uploads/imagenes/contratos/tarjeta', $fotoBruta3, 'disco');
                        $tarjetaP = true;
                        $camposActualizados = $camposActualizados . "Tarjeta pension frente,";
                    } else {
                        $tarjetaP = false;
                    }
                }
                // tarjeta de pension atras
                if (strlen($contrato[0]->tarjetapensionatras) > 0) {
                    if (request()->hasFile('tarjetapensionatras')) {
                        Storage::disk('disco')->delete($contrato[0]->tarjetapensionatras);
                        $fotoBruta3 = 'Foto-tarjetapensionatras' . $contrato[0]->id . '-' . time() . '.' . request()->file('tarjetapensionatras')->getClientOriginalExtension();
                        $tarjetapensionatras = request()->file('tarjetapensionatras')->storeAs('uploads/imagenes/contratos/tarjetapensionatras', $fotoBruta3, 'disco');
                        $tarjetaPatras = true;
                        $camposActualizados = $camposActualizados . "Tarjeta pension atras,";
                    } else {
                        $tarjetapensionatras = $contrato[0]->tarjetapensionatras;
                        $tarjetaPatras = true;
                    }
                } else {
                    if (request()->hasFile('tarjetapensionatras')) {
                        $fotoBruta3 = 'Foto-tarjeta-' . $contrato[0]->id . '-' . time() . '.' . request()->file('tarjetapensionatras')->getClientOriginalExtension();
                        $tarjetapensionatras = request()->file('tarjetapensionatras')->storeAs('uploads/imagenes/contratos/tarjetapensionatras', $fotoBruta3, 'disco');
                        $tarjetaPatras = true;
                        $camposActualizados = $camposActualizados . "Tarjeta pension atras,";
                    } else {
                        $tarjetaPatras = false;
                    }
                }
                // Foto Otros
                if (strlen($contrato[0]->fotootros) > 0) {
                    if (request()->hasFile('fotootros')) {
                        Storage::disk('disco')->delete($contrato[0]->fotootros);
                        $fotoBrutaOtros = 'Foto-Otros' . $contrato[0]->id . '-' . time() . '.' . request()->file('fotootros')->getClientOriginalExtension();
                        $fotootros = request()->file('fotootros')->storeAs('uploads/imagenes/contratos/fotootros', $fotoBrutaOtros, 'disco');
                        $otros = true;
                        $camposActualizados = $camposActualizados . "Foto otros,";
                    } else {
                        $fotootros = $contrato[0]->fotootros;
                        $otros = true;
                    }
                } else {
                    if (request()->hasFile('fotootros')) {
                        $fotoBrutaOtros = 'Foto-Otros-' . $contrato[0]->id . '-' . time() . '.' . request()->file('fotootros')->getClientOriginalExtension();
                        $fotootros = request()->file('fotootros')->storeAs('uploads/imagenes/contratos/fotootros', $fotoBrutaOtros, 'disco');
                        $otros = true;
                        $camposActualizados = $camposActualizados . "Foto otros,";
                    } else {
                        $otros = false;
                    }
                }

                $camposActualizados = trim($camposActualizados, " ");
                $camposActualizados = trim($camposActualizados, ",");

                $datos = 1;
                $actualizar = Carbon::now();
                $usuarioId = Auth::user()->id;

                $idAsistenteActualizar = $contrato[0]->id_usuariocreacion;
                $nombreAsistenteActualizar = $contrato[0]->nombre_usuariocreacion;
                $idOptometristaActualizar = $contrato[0]->id_optometrista;
                $seActualizoAsistente = false;

                if ($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) {
                    $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1");
                    if ($tieneHistorialGarantia == null) {
                        //No tiene garantias
                        $consultaAsistenteActualizar = DB::select("SELECT name FROM users WHERE id = '" . request('asistente') . "'");
                        if ($consultaAsistenteActualizar != null) {
                            $idAsistenteActualizar = request('asistente');
                            $nombreAsistenteActualizar = $consultaAsistenteActualizar[0]->name;
                            $idOptometristaActualizar = request('optometrista');
                            $seActualizoAsistente = true;
                        }
                    }
                }

                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                    'datos' => $datos, 'id_franquicia' => $idFranquicia, 'id_zona' => request('zona'), 'id_optometrista' => $idOptometristaActualizar,
                    'id_usuariocreacion' => $idAsistenteActualizar, 'nombre_usuariocreacion' => $nombreAsistenteActualizar, 'nombre' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('nombre')),
                    'calle' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('calle')), 'numero' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('numero')),
                    'depto' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('departamento')), 'alladode' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('alladode')),
                    'frentea' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('frentea')), 'entrecalles' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('entrecalles')),
                    'colonia' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('colonia')), 'localidad' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('localidad')),
                    'telefono' => request('telefono'), 'casatipo' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('casatipo')), 'casacolor' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('casacolor')), 'fotoine' => $fotoine,
                    'fotocasa' => $fotocasa, 'pagare' => $fotopagare, 'comprobantedomicilio' => $comprobantedomicilio, 'tarjeta' => $tarjeta, 'updated_at' => $actualizar,
                    'nombrereferencia' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('nr')), 'telefonoreferencia' => request('tr'), 'correo' => request('correo'), 'fotoineatras' => $fotoineatras,
                    'tarjetapensionatras' => $tarjetapensionatras, 'coordenadas' => request('coordenadas'), 'fotootros' => $fotootros, 'calleentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('calleentrega')),
                    'numeroentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('numeroentrega')), 'deptoentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('departamentoentrega')),
                    'alladodeentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('alladodeentrega')), 'frenteaentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('frenteaentrega')),
                    'entrecallesentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('entrecallesentrega')), 'coloniaentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('coloniaentrega')),
                    'localidadentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('localidadentrega')), 'casatipoentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('casatipoentrega')),
                    'casacolorentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('casacolorentrega')), 'alias' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('alias')),
                    'observacionfotoine' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('observacionfotoine')), 'observacionfotoineatras' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('observacionfotoineatras')),
                    'observacionfotocasa' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('observacionfotocasa')), 'observacioncomprobantedomicilio' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('observacioncomprobantedomicilio')),
                    'observacionpagare' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('observacionpagare')), 'observacionfotootros' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('observacionfotootros'))
                ]);

                //historial de contrato
                if($camposActualizados != ""){
                    $cambio = "Actualizo los siguientes campos para el contrato: '" . $camposActualizados . "'";
                    $cambio = trim($cambio,",");
                    //Registrar movimiento
                    DB::table('historialcontrato')->insert([
                        'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => $cambio
                    ]);
                }

                //Actualizar datos contrato a mayusculas y quitar acentos
                $contratosGlobal::actualizarContratoHistorialesClinicosMayusculasAcentos($idContrato, 0);

                //ACTUALIZAR ASISTENTE EN TABLA contratostemporalessincronizacion
                if($seActualizoAsistente) {
                    //Se cambiara el contrato de la tabla contratostemporalessincronizacion a la asistente que se haya cambiado y se eliminara a la anterior

                    //Eliminar registro de la tabla contratostemporalessincronizacion de la asistente anterior
                    DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato' AND id_usuario = '" . $contrato[0]->id_usuariocreacion . "'");

                    //Insertar contrato en tabla contratostemporalessincronizacion de la asistente a la que se actualizo
                    $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $idAsistenteActualizar);

                }

                //Validacion de si es garantia o no
                $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1 ORDER BY created_at LIMIT 1");

                $idZonaActualizarAprobados = $contrato[0]->id_zona;
                if($contrato[0]->id_zona != request('zona')) {
                    //Es por que se cambio la zona del contrato

                    $contratosGlobal::validacionAumentarDisminuirTabularPolizaActualYPolizasAnterioresSemana($idFranquicia, $idContrato, $contrato[0]->id_zona, request('zona'), "");

                    if ($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 4 || $contrato[0]->estatus_estadocontrato == 5
                        || $contrato[0]->estatus_estadocontrato == 12 ||
                        (($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 10
                        || $contrato[0]->estatus_estadocontrato == 11 || $contrato[0]->estatus_estadocontrato == 7) && $tieneHistorialGarantia != null)) {
                        //ENTREGADO, ABONO ATRASADO, LIQUIDADO, ENVIADO O TERMINADO, EN PROCESO DE APROBACION, MANUFACTURA, EN PROCESO DE ENVIO, APROBADO Y TENGA GARANTIA

                        $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($contrato[0]->id_zona); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona anterior

                        if ($cobradoresAsignadosAZona != null) {
                            //Existen cobradores
                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato' AND id_usuario = '" . $cobradorAsignadoAZona->id . "'");
                                DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_contrato = '$idContrato' AND id_usuariocobrador = '" . $cobradorAsignadoAZona->id . "'");
                            }
                        }

                        $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona(request('zona')); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona nueva

                        if ($cobradoresAsignadosAZona != null) {
                            //Existen cobradores
                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $cobradorAsignadoAZona->id);

                                $abonos = DB::select("SELECT * FROM abonos WHERE id_contrato ='$idContrato' ORDER BY created_at DESC");
                                //Insertar abonos en tabla abonoscontratostemporalessincronizacion
                                foreach ($abonos as $abono) {
                                    //Recorrido abonos
                                    //Insertar en tabla abonoscontratostemporalessincronizacion
                                    DB::table("abonoscontratostemporalessincronizacion")->insert([
                                        "id_usuariocobrador" => $cobradorAsignadoAZona->id,
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
                        }

                    }

                    $idZonaActualizarAprobados = request('zona');
                }

                switch ($contrato[0]->estatus_estadocontrato) {
                    case 0:
                    case 1:
                    case 9: //NO TERMINADO, TERMINADO, EN PROCESO DE APROBACION
                        //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                        if ($tieneHistorialGarantia != null) {
                            //Tiene garantia
                            $ultimaGarantiaCreada = DB::select("SELECT id_optometrista FROM garantias WHERE id_contrato = '$idContrato'
                                                                                AND id_historialgarantia = '" . $tieneHistorialGarantia[0]->id . "'
                                                                                ORDER BY created_at LIMIT 1");
                            if($ultimaGarantiaCreada != null) {
                                $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $ultimaGarantiaCreada[0]->id_optometrista);
                            }
                        }else {
                            //No tiene garantia
                            $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $idAsistenteActualizar);
                        }
                        break;
                    case 2:
                    case 4:
                    case 5:
                    case 12: //ENTREGADO, ABONO ATRASADO, LIQUIDADO Y ENVIADO
                        $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, Auth::id());
                        break;
                    case 10:
                    case 11: //MANOFACTURA, EN PROCESO DE ENVIO
                        if ($tieneHistorialGarantia != null) {
                            $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, Auth::id());
                        }
                        break;
                    case 7: //APROBADO
                        //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato (Opcional lo puse en caso de que no se haga en confirmaciones)
                        DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");

                        //Agregar contrato a cobradores si tiene garantia el contrato
                        if ($tieneHistorialGarantia != null) {
                            //Tiene garantia
                            $cobradoresAsignadosAZona = DB::select("SELECT u.id
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id = uf.id_usuario
                                              WHERE u.rol_id = 4 AND u.id_zona = '" . $idZonaActualizarAprobados . "'"); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona

                            if ($cobradoresAsignadosAZona != null) {
                                //Existen cobradores
                                foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                    //Recorrido cobradores
                                    $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $cobradorAsignadoAZona->id);
                                }
                            }
                        }
                        break;
                    case 3:
                    case 6:
                    case 8:
                    case 14:
                    case 15: //PRE-CANCELADO, CANCELADO, RECHAZADO, LIO/FUGA Y SUPERVISION
                        //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato
                        DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");
                        //Eliminar registros de la tabla abonoscontratostemporalessincronizacion que contengan ese idContrato
                        DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_contrato = '$idContrato'");
                        break;
                }

                return redirect()->route('contratoactualizar', [$idFranquicia, $idContrato])->with('bien', 'El contrato se actualizo correctamente.');
            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function desactivarPromocion($idFranquicia, $idContrato, $idPromocion)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12) || ((Auth::user()->rol_id) == 6)) {
            $sql = "SELECT id_promocion as id, p.titulo, p.asignado, p.inicio, p.fin, p.status, pr.estado,pr.id as idpromo
            FROM promocioncontrato  pr
            inner join promocion p on pr.id_promocion = p.id
            WHERE id_contrato = '$idContrato'";
            $promocioncontrato = DB::select($sql);
            if ($promocioncontrato == null) {
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('bien', 'Se actualizo correctamente.');
            }
            $promocioncontratoid = $promocioncontrato[0]->idpromo;
            $randomId2 = $this->getHistorialContratoId();
            try {
                $contrato = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
                $estado = $promocioncontrato[0]->estado;
                $abonos = $contrato[0]->totalabono;
                $totalcontra = $contrato[0]->total;
                $fechacontrato = DB::select("SELECT created_at FROM contratos where id = '$idContrato'");
                $abonospromo = DB::select("SELECT COUNT(a.id) as conteo FROM abonos a INNER JOIN contratos c ON a.id_contrato = c.id
                WHERE CAST(a.tipoabono as SIGNED) != 7 AND (c.id = '$idContrato' OR c.idcontratorelacion = '$idContrato')");
                $now = Carbon::now();
                $nowparce = Carbon::parse($now)->format('Y-m-d');
                $todayparce = Carbon::parse($fechacontrato[0]->created_at)->format('Y-m-d');
                if ($estado == 0 && $abonospromo[0]->conteo > 0) {
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                        ->with('alerta', 'No se puede activar la promoción debido que el contrato ya cuenta con abonos.');
                }
                if ($estado == 0 && $nowparce != $todayparce) {
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                        ->with('alerta', 'No se puede activar la promoción, solo el dia de creación del contrato');
                }
                if ($estado == 1) {
                    DB::table('promocioncontrato')->where([['id', '=', $promocioncontratoid], ['id_franquicia', '=', $idFranquicia]])->update([
                        'estado' => 0
                    ]);
                    $this->calculoTotal($idContrato, $idFranquicia);
                    $estado = 0;
                } else if ($estado == 0) {
                    DB::table('promocioncontrato')->where([['id', '=', $promocioncontratoid], ['id_franquicia', '=', $idFranquicia]])->update([
                        'estado' => 1
                    ]);
                    $this->calculoTotal($idContrato, $idFranquicia);
                    $estado = 1;
                }
                $usuarioId = Auth::user()->id;
                $actualizar = Carbon::now();

                if ($estado == 0) {
                    DB::table('historialcontrato')->insert([
                        'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => 'Desactivo la promoción en el contrato'
                    ]);
                } else {
                    DB::table('historialcontrato')->insert([
                        'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => 'Activo la promoción en el contrato'
                    ]);
                }

                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('bien', 'La promoción actualizo correctamente.');
            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function calculoTotal($idContrato, $idFranquicia)
    {

        $this->actualizarTotalProductoContrato($idContrato, $idFranquicia);
        $this->actualizarTotalAbonoContrato($idContrato, $idFranquicia);

        if ($this->obtenerEstadoPromocion($idContrato, $idFranquicia)) {
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
    }

    private function actualizarTotalProductoContrato($idContrato, $idFranquicia)
    {
        $totalproductos = DB::select("SELECT coalesce(SUM(cp.total), 0) as totalproductos FROM contratoproducto cp WHERE cp.id_contrato = '$idContrato'");
        DB::update("UPDATE contratos c
                    SET c.totalproducto = '" . $totalproductos[0]->totalproductos . "'
                    WHERE c.id = '$idContrato' AND c.id_franquicia ='$idFranquicia'");
    }

    private function actualizarTotalAbonoContrato($idContrato, $idFranquicia)
    {
        DB::update("UPDATE contratos c
                    SET c.totalabono = coalesce((SELECT SUM(a.abono) FROM abonos a WHERE a.id_contrato = c.id), 0)
                    WHERE c.id = '$idContrato'");
    }

    public function eliminarPromocion($idFranquicia, $idContrato, $idPromocion)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12) || ((Auth::user()->rol_id) == 6)) {
            $sql = "SELECT id_promocion as id, p.titulo, p.asignado, p.inicio, p.fin, p.status, pr.estado,pr.id as idpromo
            FROM promocioncontrato  pr
            inner join promocion p on pr.id_promocion = p.id
            WHERE id_contrato = '$idContrato'";
            $promocioncontrato = DB::select($sql);
            if ($idPromocion == null) {
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                 ->with('alerta', 'No se encontro el registro de la promoción en el contrato');
            }
            if ($promocioncontrato == null) {
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                 ->with('alerta', 'No se encontro el registro de la promoción en el contrato');
            }

            $promocioncontrato = DB::select($sql);
            $promotitulo = $promocioncontrato[0]->titulo;
            $promocioncontratoid = $promocioncontrato[0]->idpromo;
            $randomId2 = $this->getHistorialContratoId();
            $usuarioId = Auth::user()->id;
            $actualizar = Carbon::now();
            $existepromo = DB::delete("SELECT * FROM promocioncontrato WHERE id = '$promocioncontratoid' AND id_franquicia = '$idFranquicia'");


            try {
                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                    'id_promocion' => null
                ]);
                DB::delete("DELETE FROM promocioncontrato WHERE id = '$promocioncontratoid' AND id_franquicia = '$idFranquicia'");
                DB::table('historialcontrato')->insert([
                    'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => 'se elimino la promoción en el contrato ' . $promotitulo
                ]);
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('bien', 'La promoción se elimino correctamente.');
            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function entregarContrato($idFranquicia, $idContrato)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 4) || ((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8)) {

            try {
                $contrato = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
                $totalabono = $contrato[0]->totalabono;
                $total = $contrato[0]->total;
                $entregaproducto = $contrato[0]->entregaproducto;
                $estadocontrato = $contrato[0]->estatus_estadocontrato;
                $pago = $contrato[0]->pago;
                $estatus = $contrato[0]->estatus;
                $totalproductos = $contrato[0]->totalproducto;
                $diapago = $contrato[0]->diaseleccionado;
                $abono = request('abono');
                $adelanto = request('adelanto');
                $tot = $totalabono + $abono;
                $now = carbon::now();
                $nowparce = Carbon::parse($now)->format('Y-m-d');

                $contratosGlobal  = new contratosGlobal();
                $abonoMinimoSemanal = $contratosGlobal::calculoCantidadFormaDePago($idFranquicia, 1);
                $existeIdContratoTablaAbonoMinimoContratos = DB::select("SELECT abonominimo FROM abonominimocontratos WHERE id_contrato = '" . $idContrato . "'");
                if ($existeIdContratoTablaAbonoMinimoContratos != null
                    && ($existeIdContratoTablaAbonoMinimoContratos[0]->abonominimo == 250
                        || $existeIdContratoTablaAbonoMinimoContratos[0]->abonominimo == 500
                        ||$existeIdContratoTablaAbonoMinimoContratos[0]->abonominimo == 800)) {
                    //Existe contrato en tabla abonominimocontratos
                    $abonoMinimoSemanal = 250;
                }

                if ($pago == 0 && $estadocontrato == 12 && $total > 0) {
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                        ->with('alerta', 'El pago es de contado favor de completar el saldo del contrato');
                }
                if ($totalabono == null && $contrato[0]->subscripcion == null) {
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                        ->with('alerta', "Debes abonar $abonoMinimoSemanal al menos para entregar el producto");
                }
                if ($pago == 0 && $estatus == 1 && $estadocontrato == 1 && $total > 0) {
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                        ->with('alerta', 'El pago es de contado favor de completar el saldo del contrato');
                }

                if ($entregaproducto == 1 || ($entregaproducto == 0 && $total == 0) || $contrato[0]->subscripcion != null) {
                    if ($total > 0) {
                        $estatuscontrato = 2;
                    } else {
                        $estatuscontrato = 5;
                    }
                    DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                        'estatus_estadocontrato' => $estatuscontrato, 'entregaproducto' => 1, 'fechaentrega' => $nowparce
                    ]);
                    //Insertar en tabla registroestadocontrato
                    DB::table('registroestadocontrato')->insert([
                        'id_contrato' => $idContrato,
                        'estatuscontrato' => $estatuscontrato,
                        'created_at' => Carbon::now()
                    ]);
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('bien', 'Se entrego el producto al cliente correctamente.');

                } else {
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('alerta', "Debes abonar al menos $$abonoMinimoSemanal para entregar el producto");
                }

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function abonoAtrasado($idFranquicia, $idContrato)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 4)) {

            try {
                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                    'estatus_estadocontrato' => 4, 'fechaatraso' => Carbon::now()
                ]);
                //Insertar en tabla registroestadocontrato
                DB::table('registroestadocontrato')->insert([
                    'id_contrato' => $idContrato,
                    'estatuscontrato' => 4,
                    'created_at' => Carbon::now()
                ]);
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('bien', 'Se agrego el retraso en abonos de este contrato');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function cancelarContrato($idFranquicia, $idContrato)
    {

        $now = Carbon::now();
        $hoyNumero = $now->dayOfWeekIso;
        if ($hoyNumero >= 6 || $hoyNumero <= 2) {
            //Dia es sabado, domingo, lunes o martes

            $tieneGarantiaPendiente = DB::select("SELECT id FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia IN (0,1)");

            if ($tieneGarantiaPendiente != null) {
                return back()->with('alerta', 'No se puede cancelar el contrato (Tiene una garantia pendiente/Tiene reportada una garantia)');
            } else {

                //Verificamos si tiene una solicitud pendiente por confirmar
                $solicitudPendiente = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato  = '$idContrato' AND a.tipo IN (1,11) AND estatus = '0' ORDER BY a.created_at DESC LIMIT 1");

                if ($solicitudPendiente == null) {
                    //No tiene solicitud pendiente

                    $rol_id = Auth::user()->rol_id;
                    $idUsuario = Auth::user()->id;

                    $comentarios = request('comentarios');
                    $cbLioFuga = request('cbLioFuga');

                    if (strlen($comentarios) < 60) {
                        //Comentarios menor a 60 caracteres
                        return back()->with('alerta', 'El motivo debe contener al menos 60 caracteres.');
                    }

                    $mensajealerta = "cancelar el";
                    $tipoautorizacion = 1;
                    $valor = null;
                    if ($cbLioFuga != null) { //cbLioFuga esta checkeado?
                        $mensajealerta = "levantar lio/fuga al";
                        $tipoautorizacion = 11;
                        $valor = $comentarios;
                    }

                    if ($rol_id == 15) {
                        //Si es rol CONFIRMACIONES
                        $validacionContrato = "c.id = '$idContrato'";
                    } else {
                        //Si es rol DIRECTOR, ADMINISTRACION, PRINCIPAL
                        $validacionContrato = "c.id = '$idContrato' AND c.id_franquicia = '$idFranquicia'";
                    }

                    $existeContrato = DB::select("SELECT * FROM contratos c WHERE $validacionContrato");

                    if ($existeContrato != null) {
                        //Si existe el contrato

                        $promocionterminada = $existeContrato[0]->promocionterminada;
                        $estatusContrato = $existeContrato[0]->estatus_estadocontrato;

                        if ($estatusContrato == 5 && $cbLioFuga == null) {
                            return back()->with('alerta', 'No se puede solicitar cancelar el contrato, solo levantar lio/fuga');
                        }

                        if ($this->obtenerEstadoPromocion($idContrato, $idFranquicia) && $promocionterminada == 0) { //Tiene Promocion y aun no ha sido terminada
                            return back()->with('alerta', 'No se puede solicitar cancelar el contrato en este momento, por algunas de las siguientes razones:<br>
                                1.- Contrato no terminado.<br>
                                2.- Promoción que incluye dos o más contratos sin terminar.');
                        }

                        if ($rol_id == 15) {
                            //Si es CONFIRMACIONES valida que el contrato pertenezca a una franquicia asiganada

                            $banderaFranquicia = false;
                            //optenemos sucursales asignadas
                            $franquicias = DB::select("SELECT f.id FROM franquicias f
                                                INNER JOIN sucursalesconfirmaciones sf ON f.id = sf.id_franquicia
                                                WHERE f.id != '00000' AND sf.id_usuario = '$idUsuario' ORDER BY ciudad ASC");

                            foreach ($franquicias as $franquicia) {
                                if ($franquicia->id == $existeContrato[0]->id_franquicia) {
                                    //Si la sucursal del contrato pertenece a una asiganada a confirmaciones
                                    $banderaFranquicia = true; // Bandera = verdadero y salir del ciclo
                                    break;
                                }
                            }
                            if ($banderaFranquicia == false) {
                                //No pertenece a ningua fraqnuicia asiganada
                                return back()->with('alerta', 'No puedes accesar al contrato debido a la sucursal que pertenece.');
                            }
                        }

                        if ($existeContrato[0]->estatus_estadocontrato == 0 || $existeContrato[0]->estatus_estadocontrato == 1 || $existeContrato[0]->estatus_estadocontrato == 2
                            || $existeContrato[0]->estatus_estadocontrato == 3 || $existeContrato[0]->estatus_estadocontrato == 4 || $existeContrato[0]->estatus_estadocontrato == 5
                            || $existeContrato[0]->estatus_estadocontrato == 12 || $existeContrato[0]->estatus_estadocontrato == 14 || $existeContrato[0]->estatus_estadocontrato == 15) {
                            //El contrato tiene estatus NO TERMINADO, TERMINADO, ENTREGADO, PRE-CANCELADO, ATRASADO, PAGADO, ENVIADO, LIO/FUGA, SUPERVISION

                            try {
                                //Insertamos valores de peticion y movimiento

                                //Insertar solicitud de autorizacion
                                DB::table('autorizaciones')->insert([
                                    'id_contrato' => $idContrato, 'id_usuarioC' => $idUsuario, 'id_franquicia' => $idFranquicia,
                                    'fechacreacioncontrato' => $existeContrato[0]->created_at,
                                    'estadocontrato' => $existeContrato[0]->estatus_estadocontrato,
                                    'mensaje' => "Solicitó autorizacion con el siguiente mensaje: '$comentarios'",
                                    'estatus' => '0', 'tipo' => $tipoautorizacion, 'valor' => $valor, 'created_at' => Carbon::now()
                                ]);

                                //Guardar en historial de movimientos
                                DB::table('historialcontrato')->insert([
                                    'id' => $this->getHistorialContratoId(),
                                    'id_usuarioC' => $idUsuario,
                                    'id_contrato' => $idContrato,
                                    'created_at' => Carbon::now(),
                                    'cambios' => "Solicitó autorizacion para $mensajealerta contrato con el siguiente mensaje: '$comentarios'",
                                    'tipomensaje' => '3'
                                ]);

                                return back()->with('bien', "Solicitud para $mensajealerta contrato generada correctamente");

                            } catch (Exception $e) {
                                \Log::info("Error: " . $e->getMessage());
                                return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina   Error:' . $e->getMessage());
                            }

                        }
                        return back()->with('alerta', 'No puedes cancelar o levantar lio/fuga al contrato debido a su estatus actual.');

                    }
                    return back()->with('alerta', 'Contrato no existente, verifica el ID CONTRATO.');

                }
                return back()->with('alerta', 'Se tiene una solicitud cancelacion o lio/fuga pendiente, es necesario que se autorice o rechace primero antes de proceder con la cancelación.');

            }

        } else {
            //Dia es miercoles, jueves, o viernes
            return back()->with('alerta', 'El contrato solo se puede cancelar de sabado a martes.');
        }

    }

    public function validarContrato($idFranquicia, $idContrato)
    {
        $contrato = DB::select("SELECT estatus_estadocontrato, id_usuariocreacion FROM contratos WHERE id='$idContrato'");
        if ((Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8) && $contrato[0]->estatus_estadocontrato == 3) {

            try {
                $usuarioId = Auth::user()->id;
                $randomId2 = $this->getHistorialContratoId();
                $ahora = Carbon::now();
                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                    'estatus_estadocontrato' => 0, 'poliza' => null
                ]);
                //Insertar en tabla registroestadocontrato
                DB::table('registroestadocontrato')->insert([
                    'id_contrato' => $idContrato,
                    'estatuscontrato' => 0,
                    'created_at' => Carbon::now()
                ]);
                DB::table('historialcontrato')->insert([
                    'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $ahora, 'cambios' => "El contrato fue declarado como valido."
                ]);

                //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                $contratosGlobal = new contratosGlobal;
                $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $contrato[0]->id_usuariocreacion);

                return back()->with('bien', 'Se declaro el contrato como valido.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function eliminarAbono($idFranquicia, $idContrato, $idAbono)
    {
        if (Auth::check()) {

            try {

                $abono = DB::select("SELECT * FROM abonos WHERE id_contrato = '$idContrato' AND id = '$idAbono'");
                if ($abono == null) {
                    return back()->with('alerta', 'No se encontro el abono');
                }

                $abonocant = $abono[0]->abono;
                $folioabono = $abono[0]->folio;
                $tipoabono = $abono[0]->tipoabono;
                $metodopago = $abono[0]->metodopago;
                $user = $abono[0]->id_usuario;
                $fecha = $abono[0]->created_at;
                $cosatraso = $abono[0]->atraso;
                $fechaabono = Carbon::parse($fecha)->format('Y-m-d');
                $contrato = DB::select("SELECT c.totalabono, c.entregaproducto, c.fechaentrega, c.estatus_estadocontrato, c.costoatraso, c.pagosadelantar, c.pago,
                                                c.id_promocion, c.enganche, c.total, c.totalhistorial, c.totalpromocion, c.id_zona,
                                                (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia
                                                FROM contratos c WHERE c.id_franquicia = '$idFranquicia' AND c.id = '$idContrato'");
                $totalabono = $contrato[0]->totalabono;
                $entregadeproducto = $contrato[0]->entregaproducto;
                $fechaentrega = $contrato[0]->fechaentrega;
                $contratoestatus = $contrato[0]->estatus_estadocontrato;
                $costoatraso = $contrato[0]->costoatraso;
                $pagosadelantar = $contrato[0]->pagosadelantar;
                $formapago = $contrato[0]->pago;
                $pro = $contrato[0]->id_promocion;
                $engancheactivo = $contrato[0]->enganche;
                $totalcontrato = $contrato[0]->total;
                $th = $contrato[0]->totalhistorial;
                $totalhistorial = $contrato[0]->totalhistorial + 100;
                $totalhistorial2 = $contrato[0]->totalhistorial + 200;
                $totalhistorial3 = $contrato[0]->totalhistorial + 300;
                $totalpromocion = $contrato[0]->totalpromocion + 100;
                $resta = $totalabono - $abonocant;
                $totalliquidado = $totalcontrato + $abonocant;
                $suma = $totalcontrato + $abonocant + 100;
                $sumacontadoenganche = $abonocant + 200;
                $sumacontadosinenganche = $abonocant + 300;
                $suma2 = $totalcontrato + $abonocant;

                $folioOIdUltimoAbonoEliminar = DB::select("SELECT id, folio FROM abonos WHERE id_contrato ='$idContrato'
                                                                    AND metodopago != '1' AND tipoabono != 7 ORDER BY created_at DESC LIMIT 1");

                if($folioOIdUltimoAbonoEliminar != null) {
                    //Existe por lo menos un abono
                    if($folioOIdUltimoAbonoEliminar[0]->folio != null) {
                        //Tiene folio el ultimo abono
                        $folioOIdUltimoAbonoEliminar = $folioOIdUltimoAbonoEliminar[0]->folio;
                    }else {
                        //No tiene folio el ultimo abono
                        $folioOIdUltimoAbonoEliminar = $folioOIdUltimoAbonoEliminar[0]->id;

                        if($folioOIdUltimoAbonoEliminar !=  $idAbono){
                            $folioOIdUltimoAbonoEliminar = DB::select("SELECT id, folio FROM abonos WHERE id_contrato ='$idContrato'
                                                                    AND metodopago != '1' AND tipoabono != 7 AND id != '$folioOIdUltimoAbonoEliminar' ORDER BY created_at DESC LIMIT 1");

                            if($folioOIdUltimoAbonoEliminar != null){
                                $folioOIdUltimoAbonoEliminar = $folioOIdUltimoAbonoEliminar[0]->id;
                            }
                        }
                    }
                }

                if((Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8) && ($tipoabono != 7) && ($metodopago != 1)
                    && ($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1
                        || $contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 4
                        || $contrato[0]->estatus_estadocontrato == 5 || $contrato[0]->estatus_estadocontrato == 12)
                    && ($folioOIdUltimoAbonoEliminar != null && ($folioOIdUltimoAbonoEliminar == $idAbono || $folioOIdUltimoAbonoEliminar == $folioabono))) {

                    if ($tipoabono == 1) {
                        //ENGANCHE

                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'total' => $suma,
                            'totalabono' => $resta,
                            'totalhistorial' => $totalhistorial,
                            'totalpromocion' => $totalpromocion,
                            'enganche' => 0,
                            'entregaproducto' => 0
                        ]);

                    }
                    if ($tipoabono == 2) {
                        //ENTREGA DE PRODUCTO

                        //ACTUALIZAR EL ESTADO A ENVIADO
                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'estatus_estadocontrato' => 12
                        ]);

                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $idContrato,
                            'estatuscontrato' => 12,
                            'created_at' => Carbon::now()
                        ]);

                        if($contrato[0]->estadogarantia == null || $contrato[0]->estadogarantia == 0 || $contrato[0]->estadogarantia == 3) {
                            //NO TIENE NINGUN REGISTRO EN LA TABLA GARANTIAS, ESTADOGARANTIA ES IGUAL A REPORTADA O ENVIADA EN LABORATORIO
                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'fechacobroini' => null,
                                'fechacobrofin' => null,
                                'entregaproducto' => 0,
                                'fechaentrega' => null
                            ]);
                        }

                    }
                    if ($tipoabono == 4) {
                        //CONTADOENGANCHE

                        $abonoenganche5 = DB::select("SELECT * FROM abonos WHERE id_contrato = '$idContrato' AND tipoabono = 5");
                        if ($abonoenganche5 != null) {
                            $eng = 1;
                        } else {
                            $eng = 0;
                        }
                        if ($contratoestatus < 1) {
                            //No se ha terminado el contrato
                            $contratoestatus2 = 0;
                        } else {
                            if ($entregadeproducto == 1) {
                                //Ya fue entregado el contrato
                                $contratoestatus2 = 12;
                            } else {
                                //No ha sido entregado el contrato
                                if ($contratoestatus == 7 || $contratoestatus == 10 || $contratoestatus == 11 || $contratoestatus == 9) {
                                    //Estado del contrato es APROBADO, MANUFACTURA, EN PROCESO DE ENVIO o EN PROCESO DE APROBACION
                                    $contratoestatus2 = $contratoestatus;
                                } else {
                                    //Estado del contrato es diferente a APROBADO, MANUFACTURA, EN PROCESO DE ENVIO o EN PROCESO DE APROBACION
                                    if($contratoestatus == 12) {
                                        //Sigue estando en ENVIADO el contrato
                                        $contratoestatus2 = $contratoestatus;
                                    }else {
                                        //El estado es diferente a ENVIADO
                                        $contratoestatus2 = 1;
                                    }
                                }
                            }
                        }

                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'total' => $suma,
                            'totalabono' => $resta,
                            'totalhistorial' => $totalhistorial,
                            'totalpromocion' => $totalpromocion,
                            'enganche' => $eng,
                            'estatus_estadocontrato' => $contratoestatus2,
                            'entregaproducto' => 0
                        ]);

                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $idContrato,
                            'estatuscontrato' => $contratoestatus2,
                            'created_at' => Carbon::now()
                        ]);

                    }
                    if ($tipoabono == 5) {
                        //CONTADOSINENGANCHE

                        $abonoenganche4 = DB::select("SELECT * FROM abonos WHERE id_contrato = '$idContrato' AND tipoabono = 4");
                        if ($abonoenganche4 != null) {
                            $eng = 1;
                        } else {
                            $eng = 0;
                        }
                        if ($engancheactivo == 1) {
                            $sumacontadosinenganche = $abonocant + 200;
                            $totalhistorial3 = $contrato[0]->totalhistorial + 200;
                        }
                        if ($contratoestatus < 1) {
                            //No se ha terminado el contrato
                            $contratoestatus2 = 0;
                        } else {
                            if ($entregadeproducto == 1) {
                                //Ya fue entregado el contrato
                                $contratoestatus2 = 12;
                            } else {
                                //No ha sido entregado el contrato
                                if ($contratoestatus == 7 || $contratoestatus == 10 || $contratoestatus == 11 || $contratoestatus == 9) {
                                    //Estado del contrato es APROBADO, MANUFACTURA, EN PROCESO DE ENVIO o EN PROCESO DE APROBACION
                                    $contratoestatus2 = $contratoestatus;
                                } else {
                                    //Estado del contrato es diferente a APROBADO, MANUFACTURA, EN PROCESO DE ENVIO o EN PROCESO DE APROBACION
                                    if($contratoestatus == 12) {
                                        //Sigue estando en ENVIADO el contrato
                                        $contratoestatus2 = $contratoestatus;
                                    }else {
                                        //El estado es diferente a ENVIADO
                                        $contratoestatus2 = 1;
                                    }
                                }
                            }
                        }
                        $suma3 = $sumacontadosinenganche + $totalcontrato;

                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'total' => $suma3,
                            'totalhistorial' => $totalhistorial3,
                            'totalabono' => $resta,
                            'entregaproducto' => 0,
                            'enganche' => $eng,
                            'estatus_estadocontrato' => $contratoestatus2
                        ]);

                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $idContrato,
                            'estatuscontrato' => $contratoestatus2,
                            'created_at' => Carbon::now()
                        ]);

                    }
                    if ($tipoabono == 6) {

                        $abonodeproducto = DB::select("SELECT * FROM abonos WHERE id_contrato = '$idContrato' AND tipoabono = 2");
                        if ($abonodeproducto == null) {
                            $entregaP = 0;
                        } else {
                            $entregaP = 1;
                        }

                        if ($cosatraso > 0) {
                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'total' => $totalliquidado,
                                'totalabono' => $resta,
                                'estatus_estadocontrato' => 4
                            ]);
                            //Insertar en tabla registroestadocontrato
                            DB::table('registroestadocontrato')->insert([
                                'id_contrato' => $idContrato,
                                'estatuscontrato' => 4,
                                'created_at' => Carbon::now()
                            ]);
                        }
                        if ($pro != null) {
                            if (((Auth::user()->rol_id) != 12 && (Auth::user()->rol_id) != 13)) {

                                $estado = null;
                                if ($entregadeproducto == 1) {
                                    //Se entrego el producto
                                    $estado = 12;
                                } else {
                                    if ($contratoestatus < 1) {
                                        //No ha sido terminado el contrato
                                        $estado = 0;
                                    } else {
                                        //Ya se encuentra terminado el contrato
                                        $estado = 1;
                                    }
                                }

                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'total' => $totalliquidado,
                                    'totalabono' => $resta,
                                    'estatus_estadocontrato' => $estado
                                ]);
                                //Insertar en tabla registroestadocontrato
                                DB::table('registroestadocontrato')->insert([
                                    'id_contrato' => $idContrato,
                                    'estatuscontrato' => $estado,
                                    'created_at' => Carbon::now()
                                ]);
                            } else {
                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'total' => $totalliquidado,
                                    'totalabono' => $resta,
                                    'estatus_estadocontrato' => 0
                                ]);
                                //Insertar en tabla registroestadocontrato
                                DB::table('registroestadocontrato')->insert([
                                    'id_contrato' => $idContrato,
                                    'estatuscontrato' => 0,
                                    'created_at' => Carbon::now()
                                ]);
                            }
                        }
                        if ($abonocant == $th) {
                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'total' => $totalliquidado,
                                'totalabono' => $resta,
                                'estatus_estadocontrato' => 12,
                                'entregaproducto' => $entregaP
                            ]);
                            //Insertar en tabla registroestadocontrato
                            DB::table('registroestadocontrato')->insert([
                                'id_contrato' => $idContrato,
                                'estatuscontrato' => 12,
                                'created_at' => Carbon::now()
                            ]);
                        } elseif (((Auth::user()->rol_id) != 12 && (Auth::user()->rol_id) != 13) && $fechaentrega != null) {
                            if($cosatraso <= 0) {
                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'total' => $totalliquidado,
                                    'totalabono' => $resta,
                                    'estatus_estadocontrato' => 2
                                ]);
                                //Insertar en tabla registroestadocontrato
                                DB::table('registroestadocontrato')->insert([
                                    'id_contrato' => $idContrato,
                                    'estatuscontrato' => 2,
                                    'created_at' => Carbon::now()
                                ]);
                            }
                        } else {
                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'total' => $totalliquidado,
                                'totalabono' => $resta,
                                'entregaproducto' => $entregaP,
                            ]);
                        }

                    }

                    if ($formapago == 0 && ($contrato[0]->estatus_estadocontrato == 12 || $contrato[0]->estatus_estadocontrato == 5)) {
                        //Forma de pago es de contado y estado es igual a ENVIADO o LIQUIDADO
                        $abonocontadosinenganche = DB::select("SELECT * FROM abonos WHERE id_contrato = '$idContrato' AND tipoabono = 5");
                        if($abonocontadosinenganche == null) {
                            //No se tiene el abonocontadosinenganche

                            //ACTUALIZAR EL ESTADO A ENVIADO
                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'estatus_estadocontrato' => 12
                            ]);

                            //Insertar en tabla registroestadocontrato
                            DB::table('registroestadocontrato')->insert([
                                'id_contrato' => $idContrato,
                                'estatuscontrato' => 12,
                                'created_at' => Carbon::now()
                            ]);

                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'fechacobroini' => null,
                                'fechacobrofin' => null,
                                'entregaproducto' => 0,
                                'fechaentrega' => null
                            ]);

                        }
                    }

                    $abonosFolio = DB::select("SELECT * FROM abonos WHERE id_contrato ='$idContrato'
                                                        AND (id = '$folioOIdUltimoAbonoEliminar' OR folio = '$folioOIdUltimoAbonoEliminar')");

                    $polizaGlobales = new polizaGlobales; //Creamos una nueva instancia de polizaGlobales
                    $contratosGlobal = new contratosGlobal; //Creamos una nueva instancia de contratosGlobal

                    foreach ($abonosFolio as $abonoFolio) {
                        //Recorrido de abonos

                        //Insertar movimiento
                        DB::table('historialcontrato')->insert([
                            'id' => $this->getHistorialContratoId(),
                            'id_usuarioC' => Auth::user()->id,
                            'id_contrato' => $idContrato,
                            'created_at' => Carbon::now(),
                            'cambios' => " Se elimino un abono con la cantidad: '" . $abonoFolio->abono . "'"
                        ]);

                        //Insertar abono en tabla abonoseliminados
                        DB::table("abonoseliminados")->insert([
                            "id" => $abonoFolio->id,
                            "folio" => $abonoFolio->folio,
                            "id_franquicia" => $abonoFolio->id_franquicia,
                            "id_contrato" => $abonoFolio->id_contrato,
                            "id_usuario" => $abonoFolio->id_usuario,
                            "abono" => $abonoFolio->abono,
                            "adelantos" => $abonoFolio->adelantos,
                            "tipoabono" => $abonoFolio->tipoabono,
                            "atraso" => $abonoFolio->atraso,
                            "metodopago" => $abonoFolio->metodopago,
                            "corte" => $abonoFolio->corte,
                            "poliza" => $abonoFolio->poliza,
                            "id_corte" => $abonoFolio->id_corte,
                            "coordenadas" => $abonoFolio->coordenadas,
                            "created_at" => $abonoFolio->created_at,
                            "updated_at" => $abonoFolio->updated_at
                        ]);

                    }

                    DB::delete("DELETE FROM abonos WHERE id_contrato = '$idContrato'
                                        AND (id = '$folioOIdUltimoAbonoEliminar' OR folio = '$folioOIdUltimoAbonoEliminar')");

                    DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_contrato = '$idContrato'
                                        AND (id = '$folioOIdUltimoAbonoEliminar' OR folio = '$folioOIdUltimoAbonoEliminar')");

                    if ($abono[0]->poliza != null) {
                        //Tiene poliza asignada el abono
                        $poliza = DB::select("SELECT id, estatus, created_at FROM poliza WHERE id_franquicia = '$idFranquicia' AND id = '" . $abono[0]->poliza . "' ORDER BY created_at DESC LIMIT 1");
                        if ($poliza != null && $poliza[0]->estatus != 1) {
                            //Existe poliza y no ha sido terminada
                            //Traemos la ultima poliza de la semana actual.
                            $polizaAnterior = DB::select("SELECT * FROM poliza WHERE id_franquicia = '$idFranquicia'
                                                                AND STR_TO_DATE(created_at,'%Y-%m-%d') < STR_TO_DATE('" . $poliza[0]->created_at . "','%Y-%m-%d')
                                                                ORDER BY created_at DESC LIMIT 1");//Traemos la ultima poliza sin importar si es de la semana actual o no.
                            $polizaAnteriorId = $polizaAnterior == null ? "" : $polizaAnterior[0]->id;

                            $idPrimerPoliza = $contratosGlobal::obtenerIdPrimerPolizaSemana($idFranquicia);
                            $polizacontratoscobranza = DB::select("SELECT created_at FROM polizacontratoscobranza WHERE id_poliza = '$idPrimerPoliza'
                                                                        AND id_contrato = '$idContrato'");
                            if ($polizacontratoscobranza != null) {
                                //Existe registro en tabla polizacontratoscobranza
                                if (Carbon::now()->format('Y-m-d') == Carbon::parse($polizacontratoscobranza[0]->created_at)->format('Y-m-d')) {
                                    //Fecha de hoy es igual a la fecha de creacion del registro polizacobranza
                                    DB::delete("DELETE FROM polizacontratoscobranza WHERE id_poliza = '$idPrimerPoliza'
                                                            AND id_contrato = '$idContrato'");
                                }
                            }

                            $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($contrato[0]->id_zona); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona
                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                $polizaGlobales::calculoDeCobranza($idFranquicia, $poliza[0]->id, $polizaAnteriorId, $cobradorAsignadoAZona->id);
                            }
                        }
                    }

                    //CALCULO DE FECHAS
                    $contratoActualizado = DB::select("SELECT c.fechacobroini, c.fechacobrofin, c.estatus_estadocontrato, c.pago, c.diapago
                                                FROM contratos c WHERE c.id_franquicia = '$idFranquicia' AND c.id = '$idContrato'");

                    if($contratoActualizado != null) {
                        //EXISTE EL CONTRATO

                        $ultimoAbono = DB::select("SELECT created_at FROM abonos WHERE id_contrato ='$idContrato'
                                                            AND tipoabono != '7' ORDER BY created_at DESC LIMIT 1");
                        $fechaCobroIniActualizar = null;
                        $fechaCobroFinActualizar = null;
                        $fechaDiaSeleccionadoActualizar = null;

                        if ($ultimoAbono != null) {
                            //EXISTE POR LO MENOS UN ABONO DIFERENTE A PRODUCTO

                            if($contratoActualizado[0]->estatus_estadocontrato != 0 && $contratoActualizado[0]->estatus_estadocontrato != 1) {
                                //ENTREGADO, ATRASADO, LIQUIDADO O ENVIADO

                                if($contratoActualizado[0]->pago != 0 && $contratoActualizado[0]->fechacobroini != null && $contratoActualizado[0]->fechacobrofin != null) {
                                    //FORMA DE PAGO SEMANAL, QUICENAL O MENSUAL Y YA SE TIENEN FECHAS

                                    $fechaCreacionUltimoAbono = $ultimoAbono[0]->created_at;
                                    $calculofechaspago = new calculofechaspago;

                                    //Calculo fechaCobroIniActual y fechaCobroFinActual
                                    $arrayRespuesta = $calculofechaspago::obtenerFechasPeriodoActualOPeriodoSiguiente(Carbon::now(), $contratoActualizado[0]->pago, true);
                                    $fechaCobroIniActual = $arrayRespuesta[0];
                                    $fechaCobroFinActual = $arrayRespuesta[1];

                                    //Calculo fechaCobroIniSiguiente y fechaCobroFinSiguiente
                                    $arrayRespuesta = $calculofechaspago::obtenerFechasPeriodoActualOPeriodoSiguiente(Carbon::now(), $contratoActualizado[0]->pago, false);
                                    $fechaCobroIniSiguiente = $arrayRespuesta[0];
                                    $fechaCobroFinSiguiente = $arrayRespuesta[1];

                                    if(Carbon::parse($fechaCreacionUltimoAbono)->format('Y-m-d') >= Carbon::parse($fechaCobroIniActual)->format('Y-m-d')
                                        && Carbon::parse($fechaCreacionUltimoAbono)->format('Y-m-d') <= Carbon::parse($fechaCobroFinActual)->format('Y-m-d')) {
                                        $fechaCobroIniActualizar = $fechaCobroIniSiguiente;
                                        $fechaCobroFinActualizar = $fechaCobroFinSiguiente;
                                    }else {
                                        $fechaCobroIniActualizar = $fechaCobroIniActual;
                                        $fechaCobroFinActualizar = $fechaCobroFinActual;
                                    }

                                    //OBTENER DIASELECCIONADO
                                    if(strlen($contratoActualizado[0]->diapago) > 0) {
                                        //Se tiene un dia de pago
                                        $fechaDiaSeleccionadoActualizar = $calculofechaspago::obtenerDiaSeleccionado($contratoActualizado[0]->diapago, $fechaCobroIniActualizar, $fechaCobroFinActualizar);
                                    }
                                }
                            }

                        }

                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'fechacobroini' => $fechaCobroIniActualizar,
                            'fechacobrofin' => $fechaCobroFinActualizar,
                            'diaseleccionado' => $fechaDiaSeleccionadoActualizar
                        ]);

                    }

                    return back()->with('bien', 'El abono se elimino correctamente del contrato');

                }
                //NO CUMPLIO CON LAS VALIDACIONES
                return back()->with('bien', 'No se puede eliminar el abono');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function eliminarcontratoproducto($idFranquicia, $idContrato, $idContratoProducto)
    {
        if (Auth::check()) {

            try {

                $contratoproducto = DB::select("SELECT cp.id, cp.total, cp.piezas, cp.id_producto, p.nombre, p.id_tipoproducto, p.color
                                                        FROM contratoproducto cp INNER JOIN producto p ON p.id = cp.id_producto
                                                        WHERE cp.id_contrato = '$idContrato' AND cp.id = '$idContratoProducto'");

                if ($contratoproducto != null) {
                    //Existe contratoproducto

                    $abono = DB::select("SELECT * FROM abonos a
                                                WHERE a.id_contrato = '$idContrato' AND a.id_contratoproducto = '$idContratoProducto'");

                    if ($abono != null) {
                        //Existe el abono del contratoproducto

                        if ($abono[0]->metodopago != 1) {
                            //Metodo pago diferente a tarjeta

                            $globalesServicioWeb = new globalesServicioWeb;

                            //Eliminar contratoproducto
                            DB::delete("DELETE FROM contratoproducto WHERE id_contrato = '$idContrato' AND id = '$idContratoProducto'");

                            DB::update("UPDATE producto
                                            SET piezas = piezas + '" . $contratoproducto[0]->piezas . "',
                                            updated_at = '" . Carbon::now() . "'
                                            WHERE id = '" . $contratoproducto[0]->id_producto . "'");

                            $cadenamensaje = "'" . $contratoproducto[0]->nombre . "'";
                            $tipomensaje = 0;
                            if ($contratoproducto[0]->id_tipoproducto == 1) {
                                //Tipo producto armazon
                                $cadenamensaje = "'" . $contratoproducto[0]->nombre . "' de color '" . $contratoproducto[0]->color . "'";
                                $tipomensaje = 4;
                            }

                            //Insertamos el movimiento de la eliminacion del contratoproducto
                            DB::table('historialcontrato')->insert([
                                    'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => Auth::user()->id,
                                    'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                    'cambios' => "Se elimino el producto: " . $cadenamensaje . ", piezas: '" . $contratoproducto[0]->piezas . "'",
                                    'tipomensaje' => $tipomensaje
                            ]);

                            //Eliminar abono
                            DB::delete("DELETE FROM abonos WHERE id_contrato = '$idContrato' AND id = '" . $abono[0]->id . "' AND id_contratoproducto = '$idContratoProducto'");
                            DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_contrato = '$idContrato' AND id = '" . $abono[0]->id . "'");

                            //Insertar abono en tabla abonoseliminados
                            DB::table("abonoseliminados")->insert([
                                "id" => $abono[0]->id,
                                "folio" => $abono[0]->folio,
                                "id_franquicia" => $abono[0]->id_franquicia,
                                "id_contrato" => $abono[0]->id_contrato,
                                "id_usuario" => $abono[0]->id_usuario,
                                "abono" => $abono[0]->abono,
                                "adelantos" => $abono[0]->adelantos,
                                "tipoabono" => $abono[0]->tipoabono,
                                "atraso" => $abono[0]->atraso,
                                "metodopago" => $abono[0]->metodopago,
                                "corte" => $abono[0]->corte,
                                "poliza" => $abono[0]->poliza,
                                "id_corte" => $abono[0]->id_corte,
                                "created_at" => $abono[0]->created_at,
                                "updated_at" => $abono[0]->updated_at
                            ]);

                            //Insertamos el movimiento de la eliminacion del abono
                            DB::table('historialcontrato')->insert([
                                    'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => Auth::user()->id,
                                    'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                    'cambios' => "Se elimino un abono con la cantidad: '" . $abono[0]->abono . "'"
                            ]);

                            return back()->with('bien', 'El producto se elimino correctamente del contrato');

                        }
                        //Metodo pago con tarjeta
                        return back()->with('alerta', 'No se puede eliminar el producto por que fue pagado con tarjeta.');

                    }
                    //No existe el abono del contratoproducto
                    return back()->with('alerta', 'No se puede eliminar el producto por que no se tiene el abono.');

                }
                //No existe contratoproducto
                return back()->with('alerta', 'El producto no existe.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function entregarDiaPago($idFranquicia, $idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 4) || ((Auth::user()->rol_id) == 12)
            || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 6)) {

            try {

                $diaPagoActualizar = request('diapago');
                if ($diaPagoActualizar == '0') {
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('alerta', 'Debes elegir un dia para abonar');
                }

                $contrato = DB::select("SELECT c.estatus_estadocontrato, c.fechacobroini, c.fechacobrofin, c.pago, c.diapago
                                            FROM contratos c WHERE c.id_franquicia = '$idFranquicia' AND c.id = '$idContrato'");

                if($contrato != null) {
                    //EXISTE EL CONTRATO

                    if($contrato[0]->pago != 0) {
                        //FORMA DE PAGO SEMANAL, QUICENAL O MENSUAL

                        if ($contrato[0]->diapago != $diaPagoActualizar) {
                            //DIAPAGO ES DIFERENTE AL QUE SE QUIERE CAMBIAR

                            if ($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 4 || $contrato[0]->estatus_estadocontrato == 12) {
                                //ENTREGADO, ABONO ATRASADO O ENVIADO

                                $diaSeleccionadoActualizar = null;

                                if($contrato[0]->fechacobroini != null && $contrato[0]->fechacobrofin != null) {
                                    //YA SE TIENE FECHACOBROINI Y FECHACOBROFIN
                                    $calculofechaspago = new calculofechaspago;
                                    $diaSeleccionadoActualizar = $calculofechaspago::obtenerDiaSeleccionado($diaPagoActualizar, $contrato[0]->fechacobroini, $contrato[0]->fechacobrofin);
                                }

                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'diapago' => $diaPagoActualizar, 'diaseleccionado' => $diaSeleccionadoActualizar
                                ]);
                                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                    ->with('bien', 'Se actualizó correctamente el dia de pago.');

                            }
                            //ESTADO DIFERENTE A ENTREGADO, ABONO ATRASADO O ENVIADO
                            return back()->with('alerta', 'No se puede cambiar el dia de pago debido al estatus del contrato.');

                        }
                        //DIAPAGO ES EL MISMO AL QUE SE QUIERE CAMBIAR
                        return back()->with('alerta', 'Debes seleccionar un día diferente al que ya está registrado.');

                    }
                    //FORMA DE PAGO CONTADO
                    return back()->with('alerta', 'No se puede agregar un dia de pago por que esta de contado el contrato.');

                }
                //NO EXISTE EL CONTRATO
                return back()->with('alerta', 'El contrato no existe.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function agregarproducto($idFranquicia, $idContrato)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12) || ((Auth::user()->rol_id) == 6)) {

            try {

                $rules = [
                    'piezas' => 'required|integer'
                ];
                if (request('piezas') < 1) {
                    // return back()->withErrors(['precio' =>  'La cantidad de piezas no puede ser menor a 0']);
                    return back()->with('error', 'El numero de piezas es obligatorio, intenta de nuevo');
                }
                if (request('producto') == 'nada') {
                    // return back()->withErrors(['producto' => 'Campo obligatorio']);
                    return back()->with('alerta', 'El producto es obligatorio, intenta de nuevo');
                }
                if (request('piezas') == null) {
                    // return back()->withErrors(['precio' =>  'La cantidad de piezas no puede ser menor a 0']);
                    return back()->with('error', 'El numero de piezas es obligatorio, intenta de nuevo');
                }
                request()->validate($rules);

                $idpro = request('producto');
                $piezas = request('piezas');
                $opcion = request('opcion');
                $folioPoliza = request('folioPoliza');
                $observaciones = request('observaciones');

                $tipoProducto = DB::select("SELECT * FROM producto p WHERE p.id = '$idpro'");

                $tipoautorizacion = null;
                $foliopolizaautorizacionarmazon = null;
                $mensaje = null;

                //Validar si es un producto tipo armazon
                if($tipoProducto[0]->id_tipoproducto == 1){
                    //Es armazon - Validar si se aplica poliza

                    //Es armazon debe llevar observaciones su solicitud
                    if (request('observaciones') == null) {
                        return back()->with('error', 'El campo observaciones es obligatorio, intenta de nuevo');
                    }
                    switch ($opcion) {
                        case 1:
                            //Se agregara el prducto con el descuento de poliza - Validar que se ingrese el folio

                            if($folioPoliza != null){
                                //Ingreso el folio de la poliza - Validar que exista la poliza

                                $polizaActiva = DB::select("SELECT cp.id_contrato, cp.id_producto, p.nombre, cp.created_at FROM contratoproducto cp
                                                            INNER JOIN producto p ON p.id = cp.id_producto
                                                            WHERE cp.id_contrato = '$idContrato'
                                                            AND p.id_tipoproducto = 2 ORDER BY cp.created_at DESC LIMIT 1");

                                //Tiene una poliza?
                                if($polizaActiva){
                                    //Si tiene una poliza el contrato - Validar vigencia del producto
                                    $fechaActual = Carbon::now();
                                    $fechaPoliza = $polizaActiva[0]-> created_at;
                                    $diasCreacionPoliza = DB::select("SELECT DATEDIFF('$fechaActual', '$fechaPoliza') AS diferencia");

                                    if($diasCreacionPoliza[0]->diferencia > 365){
                                        //Si la fecha de creacion de la poliza pasa de un año
                                        return back()->with('alerta', 'Poliza caducada.');
                                    }

                                    $tipoautorizacion = 9;
                                    $foliopolizaautorizacionarmazon = $folioPoliza;
                                    $mensaje = ' por poliza';
                                }else{
                                    //No cuenta con poliza
                                    return back()->with('alerta', 'El contrato no cuenta con una poliza.');
                                }

                            }else{
                                //Seleciono check de poliza pero no ingreso folio
                                return back()->with('alerta', 'El folio de poliza es obligatorio, intenta de nuevo');
                            }
                            break;
                        case 2:
                            //Por defecto de fábrica
                            $tipoautorizacion = 10;
                            $mensaje = ' por defecto de fábrica';
                            break;
                    }

                    if ($opcion == 0) {
                        //Ninguna
                        $tipoautorizacion = 8;
                        $mensaje = '';
                    }

                    $autorizacionarmazonpendiente = DB::select("SELECT indice FROM autorizaciones WHERE id_contrato = '$idContrato' AND tipo IN (8,9,10) AND estatus = '0'");

                    if ($autorizacionarmazonpendiente == null) {
                        //No hay solicitud pendiente de armazon por defecto de fabrica

                        $contrato = DB::select("SELECT created_at, estatus_estadocontrato FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");

                        if ($contrato != null) {
                            //Existe contrato

                            //Insertar solicitud de autorizacion
                            $idAutorizacion = DB::table('autorizaciones')->insertGetId([
                                'id_contrato' => $idContrato, 'id_usuarioC' => Auth::user()->id, 'id_franquicia' => $idFranquicia,
                                'fechacreacioncontrato' => $contrato[0]->created_at,
                                'estadocontrato' => $contrato[0]->estatus_estadocontrato,
                                'mensaje' => "Solicitó agregar el armazón " . $tipoProducto[0]->id . " | " . $tipoProducto[0]->nombre . " | " . $tipoProducto[0]->color . $mensaje . ' con observaciones: ' . $observaciones,
                                'estatus' => '0', 'tipo' => $tipoautorizacion, 'created_at' => Carbon::now()
                            ]);

                            //Insertar registro en tabla autorizacionarmazonlaboratorio
                            DB::table('autorizacionarmazonlaboratorio')->insert([
                                'id_autorizacion' => $idAutorizacion,
                                'id_armazon' => $tipoProducto[0]->id,
                                'piezas' => $piezas,
                                'foliopoliza' => $foliopolizaautorizacionarmazon,
                                'observaciones' => $observaciones,
                                'created_at' => Carbon::now()
                            ]);

                            $globalesServicioWeb = new globalesServicioWeb;

                            //Insertar registro tabla producto con estatus de pendiente por autorizar y sin total
                            $idcontratoproducto = $globalesServicioWeb::generarIdAlfanumerico('contratoproducto', '5');
                            DB::table('contratoproducto')->insertGetId([
                                'id' => $idcontratoproducto,
                                'id_franquicia' => $idFranquicia,
                                'id_contrato' => $idContrato,
                                'id_usuario' => Auth::user()->id,
                                'id_producto' => $tipoProducto[0]->id,
                                'piezas' => $piezas,
                                'total' => 0,
                                'estadoautorizacion' => '0',
                                'created_at' => Carbon::now()
                            ]);

                            //Insertamos el movimiento
                            DB::table('historialcontrato')->insert([
                                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => Auth::user()->id,
                                'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                'cambios' => "Solicitó agregar el armazón " . $tipoProducto[0]->id . " | " . $tipoProducto[0]->nombre . " | " . $tipoProducto[0]->color . $mensaje . ' con observaciones: ' . $observaciones,
                                'tipomensaje' => '3']);

                            return back()->with('bien', "Solicitud de armazón" . $mensaje . " generada correctamente.");

                        }
                        //No existe contrato
                        return back()->with('alerta', 'El contrato no existe.');

                    }
                    //Hay solicitud pendiente de armazon
                    return back()->with('alerta', "Hay una solicitud pendiente, si deseas solicitar otro, primero debes pedir a laboratorio que cancele la actual.");

                }else {
                    //Tipo diferente a armazon

                    $usuarioId = Auth::user()->id;
                    $actualizar = Carbon::now();
                    $operacion = DB::select("SELECT * FROM producto WHERE  id = '$idpro'");
                    $contrato = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
                    $abonos = DB::select("SELECT * FROM abonos WHERE id_contrato = '$idContrato'");
                    $precio1 = $operacion[0]->precio;
                    $nombre = $operacion[0]->nombre;
                    $color = $operacion[0]->color;
                    $precio2 = $operacion[0]->preciop;
                    $arma = $operacion[0]->id;
                    $armapz = $operacion[0]->piezas - $piezas;
                    $tipoMensaje = 0; //Por defaul el tipo de mensaje para historialcontrato es 0 -> Normal (Producto tipo poliza o gotas)

                    if ($precio2 == null) {
                        //No tiene promocion
                        $total = $precio1 * $piezas;
                    } else {
                        $total = $precio2 * $piezas;
                    }

                    $sumacontrato = $total + $contrato[0]->total;
                    $randomId2 = $this->getHistorialContratoId();
                    $idcontratoproducto = $this->getContratoContratoId();

                    $estadoContrato = $contrato[0]->estatus_estadocontrato;
                    if ($estadoContrato == 0) {
                        if ($abonos) {
                            return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                ->with('alerta', 'Necesitas eliminar los abonos para agregar productos');
                        }
                    }

                    DB::table('contratoproducto')->insertGetId([
                        'id' => $idcontratoproducto, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'id_producto' => request('producto'),
                        'piezas' => request('piezas'), 'total' => $total, 'created_at' => Carbon::now()
                    ]);

                    DB::table('producto')->where('id', '=', $arma)->update([
                        'piezas' => $armapz
                    ]);

                    DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                        'total' => $sumacontrato
                    ]);

                    //Mensaje para historial
                    $mensajeHistorialContrato = " Se agrego el producto con identificador: $idpro | $nombre | $color | cantidad de piezas: '$piezas'";

                    DB::table('historialcontrato')->insert([
                        'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar,
                        'tipomensaje' => $tipoMensaje, 'cambios' => $mensajeHistorialContrato
                    ]);

                    if (((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8)) {
                        //Rol administrador, director o principal
                        $idAbono = $this->getAbonosId();
                        $idHistorialContrato = $this->getHistorialContratoId();

                        $contratoproductos = DB::select("SELECT COALESCE(SUM(total),0) as suma FROM contratoproducto WHERE id_franquicia = '$idFranquicia' AND id_contrato = '$idContrato'");
                        $sumaabonostipoproducto = DB::select("SELECT COALESCE(SUM(abono),0) as suma FROM abonos WHERE id_contrato = '$idContrato' AND tipoabono = '7'");

                        $sumacontratoproducto = $contratoproductos[0]->suma;
                        $sumacontratoabonosproducto = $sumaabonostipoproducto[0]->suma;
                        $sumatotalabono = $sumacontratoproducto - $sumacontratoabonosproducto;

                        //Agregar abono al contrato
                        DB::table('abonos')->insert([
                            'id' => $idAbono,
                            'folio' => null,
                            'id_franquicia' => $idFranquicia,
                            'id_contrato' => $idContrato,
                            'id_usuario' => $usuarioId,
                            'tipoabono' => 7,
                            'abono' => $sumatotalabono,
                            'metodopago' => 0,
                            'adelantos' => 0,
                            'corte' => 2,
                            'id_contratoproducto' => $idcontratoproducto,
                            "id_zona" => $contrato[0]->id_zona,
                            'created_at' => Carbon::now()
                        ]);

                        //Validacion de si es garantia o no
                        $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1 ORDER BY created_at LIMIT 1");

                        if ($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 4
                            || $contrato[0]->estatus_estadocontrato == 12
                            || (($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9
                                    || $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11
                                    || $contrato[0]->estatus_estadocontrato == 7) && $tieneHistorialGarantia != null)) {
                            //ENTREGADO, ABONO ATRASADO, LIQUIDADO, ENVIADO O TERMINADO, EN PROCESO DE APROBACION, MANUFACTURA, EN PROCESO DE ENVIO, APROBADO Y TENGA GARANTIA

                            $contratosGlobal = new contratosGlobal;
                            $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($contrato[0]->id_zona);

                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                    "id" => $idAbono,
                                    "folio" => null,
                                    "id_contrato" => $idContrato,
                                    "id_usuario" => $usuarioId,
                                    "abono" => $sumatotalabono,
                                    "adelantos" => 0,
                                    "tipoabono" => 7,
                                    "atraso" => 0,
                                    "metodopago" => 0,
                                    "corte" => 2,
                                    "created_at" => Carbon::now()
                                ]);
                            }

                        }

                        //Guardar en historial de movimientos el abono
                        DB::table('historialcontrato')->insert([
                            'id' => $idHistorialContrato,
                            'id_usuarioC' => $usuarioId,
                            'id_contrato' => $idContrato,
                            'created_at' => Carbon::now(),
                            'cambios' => " Se agrego el abono : '$sumatotalabono'"
                        ]);

                    }

                    $contratos2 = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
                    $valor = $contratos2[0]->totalproducto + $total;
                    DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                        'totalproducto' => $valor
                    ]);

                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('bien', 'El producto se agrego correctamente.');

                }

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }


    public function agregarabono($idFranquicia, $idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 4) || ((Auth::user()->rol_id) == 12)
            || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 6)) {

            $rules = [
                'abono' => 'required|numeric',
                'metodopago' => 'required|numeric'
            ];
            if (request('abono') <= 0) {
                return back()->with('alerta', 'Ingresar la cantidad del abono');
            }
            if (request('abono') == null) {
                return back()->with('alerta', 'La cantidad de abono es obligatoria, intenta de nuevo');
            }
            if (request('adelanto') != null && request('adelanto') < 1 || request('adelanto') != null && request('adelanto') > 3) {
                return back()->with('alerta', 'La cantidad maxima a adelantar son 3');
            }

            $contratosGlobal = new contratosGlobal;

            request()->validate($rules);
            $abono2 = request('abono');
            $metodopago = request('metodopago');
            $tarjetameses = request('meses');
            $abono = number_format($abono2, 1, ".", "");
            $adelantar = request('adelantar');
            $adelanto = request('adelanto');
            $adelanto = intval($adelanto);
            $fechaHoraRegistroAbono = Carbon::now();

            $folio = null;
            if (((Auth::user()->rol_id) == 4)) {
                $folio = $contratosGlobal::validarSiExisteFolioAlfanumericoEnAbonosContrato($idContrato);
            }

            if ($metodopago == 3) {
                //Cancelacion

                try{

                    $contrato = DB::select("SELECT totalreal, id_zona FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");

                    if ($contrato != null) {
                        //Existe contrato

                        $garantia = DB::select("SELECT estadogarantia FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia IN (0,1) ORDER BY created_at DESC LIMIT 1");

                        if ($garantia == null) {
                            //No tiene garantia reportada/asignada

                            $abono = number_format($contrato[0]->totalreal * 0.30, 0, ".", "");

                            $globalesServicioWeb = new globalesServicioWeb;

                            DB::table('abonos')->insert([
                                'id' => $globalesServicioWeb::generarIdAlfanumerico('abonos', '5'), 'folio' => $folio,
                                'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => Auth::user()->id,
                                'adelantos' => 0, 'poliza' => null, 'metodopago' => $metodopago, 'tipoabono' => 8, 'abono' => $abono,
                                'atraso' => 0, "id_zona" => $contrato[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
                            ]);

                            DB::table('historialcontrato')->insert([
                                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                                'id_usuarioC' => Auth::user()->id, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                'cambios' => " Se agrego el abono de cancelación : '$abono'"
                            ]);

                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'estatus_estadocontrato' => 6
                            ]);

                            //Insertar en tabla registroestadocontrato
                            DB::table('registroestadocontrato')->insert([
                                'id_contrato' => $idContrato,
                                'estatuscontrato' => 6,
                                'created_at' => Carbon::now()
                            ]);

                            return back()->with('bien', 'El abono de cancelación se agrego correctamente.');

                        }
                        //Tiene garantia reportada/asignada
                        return back()->with('alerta', 'No se puede agregar el abono de cancelacion de momento, es necesario cancelar o terminar el proceso de garantía.');

                    }
                    //No existe contrato
                    return back()->with('alerta', 'El contrato no existe.');

                } catch (\Exception $e) {
                    \Log::info("Error: " . $e->getMessage());
                    return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
                }

            }else {
                //Efectivo, Trajeta o Transferencia

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
                $nuevoabono = 0;
                $cantidadsubscripcion = 0;
                if ($metodopago == 1 && $tarjetameses > 0) {
                    if ($tarjetameses == 1) {
                        $nuevoabono = $totalcontrato;
                        $cantidadsubscripcion = 3;
                    }
                    if ($tarjetameses == 2) {
                        $nuevoabono = $totalcontrato;
                        $cantidadsubscripcion = 6;
                    }
                    if ($tarjetameses == 3) {
                        $nuevoabono = $totalcontrato;
                        $cantidadsubscripcion = 9;
                    }
                    $nuevoabono = number_format($nuevoabono, 1, ".", "");
                }
                if ($pago == 0) {
                    if ($enganche == 1) {
                        $descuento = 200;
                    } else {
                        $descuento = 300;
                    }
                }
                $sumadescuento = $descuento + $abono;
                $contratos = DB::select("SHOW TABLE STATUS LIKE 'contratos'");
                $contaenganche = DB::select("SELECT * FROM abonos WHERE id_contrato = '$idContrato' AND tipoabono = 5");
                $contaenganche2 = DB::select("SELECT * FROM abonos WHERE id_contrato = '$idContrato' AND tipoabono = 4");
                $abonoprod = DB::select("SELECT COALESCE(SUM(abono),0) as suma FROM abonos WHERE id_contrato = '$idContrato' AND tipoabono = 7");
                $dentroRango = DB::select("SELECT id FROM contratos where id = '$idContrato' AND
                ((STR_TO_DATE('$now','%Y-%m-%d') = STR_TO_DATE(diaseleccionado,'%Y-%m-%d')) OR
               (STR_TO_DATE('$now','%Y-%m-%d') >= STR_TO_DATE(fechacobroini,'%Y-%m-%d')) AND STR_TO_DATE('$now','%Y-%m-%d') <= STR_TO_DATE(fechacobrofin,'%Y-%m-%d'))");
                $randomId = $this->getAbonosId();
                $randomId2 = $this->getHistorialContratoId();
                $tienePromocion = $this->obtenerEstadoPromocion($idContrato, $idFranquicia);

                if ($ec != null) {
                    $contra2 = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$ec'");
                    $pr = $contra2[0]->id_promocion;
                } else {
                    $pr = $contra[0]->id_promocion;
                }

                $abonoContadoLiquidadoContadoEngancheYSinContadoSinEnganche = false;
                if ($pago == 0 && $totalcontrato < 400 && $contaenganche == null && $contaenganche2 == null) {
                    //TOTALCONTRATO ES MENOR A 400 Y NO SE TIENE EL ABONO CONTADOENGACHE NI TAMPOCO EL CONTADOSINENGANCHE
                    // (CUANDO ES UNA POLIZA DE SEGURO Y ES SEMANAL, QUICENAL O MENSUAL Y SE PASA A CONTADO)
                    $abonoContadoLiquidadoContadoEngancheYSinContadoSinEnganche = true;
                }

                $consultaPaqueteHistorialContrato = DB::select("SELECT id_paquete FROM historialclinico WHERE id_contrato ='$idContrato' ORDER BY created_at DESC LIMIT 1");
                $paqueteHistorialContrato = false;
                if ($consultaPaqueteHistorialContrato != null) {
                    //Tiene paquete el contarto
                    if ($consultaPaqueteHistorialContrato[0]->id_paquete == 1 || $consultaPaqueteHistorialContrato[0]->id_paquete == 2 || $consultaPaqueteHistorialContrato[0]->id_paquete == 6) {
                        //PAQUETE DE LECTURA, PROTECCION O DORADO 2
                        $paqueteHistorialContrato = true;
                    }
                }

                $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($contra[0]->id_zona);

                //Obtener abono minimo para contrato
                $abonoMinimo = $contra[0]->abonominimo;
                if (is_null($abonoMinimo) || strlen($abonoMinimo) == 0 || $abonoMinimo == 0) {
                    //El abonominimo en la BD es null o vacio o es 0
                    $contratosGlobal = new contratosGlobal();
                    $abonoMinimo = $contratosGlobal::calculoCantidadFormaDePago($idFranquicia, $contra[0]->pago);
                }

                $abonoMinimoSemanal = $contratosGlobal::calculoCantidadFormaDePago($idFranquicia, 1);
                $existeIdContratoTablaAbonoMinimoContratos = DB::select("SELECT abonominimo FROM abonominimocontratos WHERE id_contrato = '" . $idContrato . "'");
                if ($existeIdContratoTablaAbonoMinimoContratos != null
                    && ($existeIdContratoTablaAbonoMinimoContratos[0]->abonominimo == 250
                        || $existeIdContratoTablaAbonoMinimoContratos[0]->abonominimo == 500
                        ||$existeIdContratoTablaAbonoMinimoContratos[0]->abonominimo == 800)) {
                    //Existe contrato en tabla abonominimocontratos
                    $abonoMinimoSemanal = 250;
                }

                if ($estadocontrato == 7 || $estadocontrato == 10 || $estadocontrato == 11 || $estadocontrato == 9 || $abonoContadoLiquidadoContadoEngancheYSinContadoSinEnganche ||
                    ($paqueteHistorialContrato && $estadocontrato != 12)) {
                    //Estado del contrato es APROBADO, MANUFACTURA, EN PROCESO DE ENVIO, EN PROCESO DE APROBACION, abonoContadoLiquidadoContadoEngancheYSinContadoSinEnganche = true
                    //o (paqueteHistorialContrato = true y es diferente  a ENVIADO)

                    $mensajeAlerta = "";

                    if ($abono > $totalcontrato) {
                        //Abono es mayor al total del contrato
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('alerta', 'No se puede abonar mas del total del contrato');
                    }

                    if ($pago != 0 && $abono < $abonoMinimo) {
                        //El abono a ingresar es menos al abono minimo para la forma de pago del contrato o el abonominimo registrado
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('alerta', "El abono minimo es de '$abonoMinimo' pesos");
                    }

                    $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1");

                    if ($pago == 0 && !$tienePromocion && $tieneHistorialGarantia == null && !$abonoContadoLiquidadoContadoEngancheYSinContadoSinEnganche && !$paqueteHistorialContrato) {
                        //Forma de pago es de contado, no tiene promocion y no tiene historiales con garantia y
                        // abonoContadoLiquidadoContadoEngancheYSinContadoSinEnganche = false && paqueteHistorialContrato = false

                        if ($enganche == 1 && $abono < $totalconenganche) {
                            //Tiene solo el abono de contadoenganche
                            return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                ->with('alerta', 'Favor de liquidar el total del contrato de contado, con la cantidad: $ ' . $totalconenganche);
                        }

                        if ($enganche == 1 && $contaenganche == null && $abono > $totalconenganche) {
                            //Tiene solo el abono de contadoengache y esta dando el total exacto del contrato
                            return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                ->with('alerta', 'Favor de liquidar el total del contrato de contado, con la cantidad: $ ' . $totalconenganche);
                        }

                        if ($enganche < 1 && $abono < $totalsinenganche) {
                            //No tiene el abono de contadoenganche y contadosinenganche
                            return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                ->with('alerta', 'Favor de liquidar el total del contrato de contado, con la cantidad: $ ' . $totalsinenganche);
                        }

                        if ($enganche < 1 && $abono > $totalsinenganche && $contaenganche == null) {
                            //No tiene el abono de contadoenganche y contadosinenganche y esta dando el total exacto del contrato
                            return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                ->with('alerta', 'Favor de liquidar el total del contrato de contado, con la cantidad: $ ' . $totalsinenganche);
                        }

                        DB::table('abonos')->insert([
                            'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'tipoabono' => 5,
                            'abono' => $abono, 'poliza' => null, 'metodopago' => $metodopago, "id_zona" => $contra[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
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
                                "created_at" => $fechaHoraRegistroAbono
                            ]);
                        }

                        DB::table('historialcontrato')->insert([
                            'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Se agrego el abono : '$abono'"
                        ]);

                        $contra5 = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
                        $totalfinal = 0;

                        if ($enganche < 1 && $abono == $totalsinenganche) {
                            //No tiene contado enganche
                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'totalabono' => $costo, 'ultimoabono' => $actualizar, 'totalhistorial' => $totalhistorialsinenganche
                            ]);
                            $totalfinal = $contra5[0]->total - $abono - 300;
                            $mensajeAlerta = "Se liquido el costo del contrato con descuento de 300 pesos.";
                        } else {
                            //Tiene contado enganche
                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'totalabono' => $tot, 'ultimoabono' => $actualizar, 'totalhistorial' => $totalhistorialconenganche
                            ]);
                            $totalfinal = $contra5[0]->total - $abono - 200;
                            $mensajeAlerta = "Se liquido el costo del contrato con descuento de 200 pesos.";
                        }

                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'total' => $totalfinal
                        ]);

                    } else {
                        //Forma de pago es Semanal, Quincenal, Mesual o de contado con promocion

                        if ($pago == 0 && $abono != $totalcontrato) {
                            //Contado, tiene promocion y abono no es igual al total del contrato
                            return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                ->with('alerta', 'Favor de liquidar el total del contrato de contado, con la cantidad: $ ' . $totalcontrato);
                        }

                        if ($abonoContadoLiquidadoContadoEngancheYSinContadoSinEnganche) {
                            //abonoContadoLiquidadoContadoEngancheYSinContadoSinEnganche == true
                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'tipoabono' => 6,
                                'abono' => $abono, 'adelantos' => $adelanto, 'metodopago' => $metodopago, "id_zona" => $contra[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
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
                                    "tipoabono" => 6,
                                    "metodopago" => $metodopago,
                                    "created_at" => $fechaHoraRegistroAbono
                                ]);
                            }
                        } else {
                            //abonoContadoLiquidadoContadoEngancheYSinContadoSinEnganche == false
                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'tipoabono' => 0,
                                'abono' => $abono, 'adelantos' => $adelanto, 'metodopago' => $metodopago, "id_zona" => $contra[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
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
                                    "created_at" => $fechaHoraRegistroAbono
                                ]);
                            }
                        }
                        DB::table('historialcontrato')->insert([
                            'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(), 'cambios' => " Se abono la cantidad: '$abono'"
                        ]);

                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'totalabono' => $tot, 'ultimoabono' => Carbon::now(), 'total' => $totalcontratoresta2, 'pagosadelantar' => $adelantados,
                        ]);

                        $mensajeAlerta = "El Abono se agrego correctamente.";

                    }

                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('bien', $mensajeAlerta);

                } else {
                    //Estado del contrato es diferente a APROBADO, MANUFACTURA, EN PROCESO DE ENVIO o EN PROCESO DE APROBACION

                    if ($pago === null) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('alerta', 'Debes elegir una forma de pago para poder abonar');
                    }
                    if ($totaladelantos > 3) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('alerta', 'No se puede adelantar mas de 3 en el mismo dia');
                    }
                    if ($abono > $totalcontrato) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('alerta', 'No se puede abonar mas del total del contrato');
                    }
                    if (($estadocontrato <= 1 || $estadocontrato == 12) && $abono == $totalcontrato && $enganche <= 1 && $contaenganche != null && $contaenganche2 == null) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('alerta', 'Para hacer valido el enganche abonar la cantidad :' . ($totalcontrato - 100));
                    }
                    if ($estadocontrato == 0 && $abonoprod[0]->suma < $totalproductos && $abono < $totalproductos) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('alerta', 'Abonar al menos el total de productos');
                    }
                    if ($estadocontrato == 0 && $abono > $totalproductos && $abonoprod[0]->suma < $totalproductos) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('alerta', 'Abonar el total de productos');
                    }
                    if ($estadocontrato == 0 && $tot > $totalproductos && $ec != null && $es == 0) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('alerta', 'Existe promocion, abonar al final de los contratos');
                    }
                    if ($estadocontrato == 0 && $tienePromocion && $es == 0 && $tot > $totalproductos) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('alerta', 'Existe promocion, abonar al final de los contratos');
                    }
                    if ($estadocontrato == 1 && $ec != null && $promocionterminada != 1) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('alerta', 'Existe promocion, abonar al final de completar y terminar los contratos');
                    }
                    if ($estadocontrato == 1 && $tienePromocion && $promocionterminada != 1) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('alerta', 'Existe promocion, abonar al final de completar y terminar los contratos');
                    }
                    if ($estadocontrato == 12 && ((Auth::user()->rol_id) != 12 && (Auth::user()->rol_id) != 13) && $abono < $abonoMinimoSemanal && $entregaproducto < 1 && $pago != 0) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('alerta', "No se puede abonar menos de $$abonoMinimoSemanal en contratos enviados");
                    }
                    if ($pago != 0 && $estadocontrato <= 1 && $enganche < 1 && $abono > $totalnocontado && !$tienePromocion) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('alerta', 'Para liquidar el contrato pagar la cantidad: $ ' . $totalnocontado);
                    }
                    if ($metodopago == 1 && $tarjetameses > 0) {
                        $banderacase = 13;
                        return redirect()->route('payment', ["idFranquicia" => $idFranquicia, "idContrato" => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                            "nuevoabono" => $nuevoabono, "cantidadsubscripcion" => $cantidadsubscripcion]);
                    }
                    if ($pago == 0 && $estadocontrato >= 0 && $enganche == 1 && $contaenganche == null && $abono > $totalconenganche) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('alerta', 'Favor de liquidar el total del contrato de contado para entregar el producto, con la cantidad: $ ' . $totalconenganche);
                    }

                    if ($pago == 0 && $estadocontrato == 4 && $abono < $totalcontrato) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('alerta', 'Favor de liquidar el total del contrato de contado, con la cantidad: $ ' . $totalcontrato);
                    }

                    if ($pago == 0 && $estadocontrato >= 0 && $estadocontrato != 4 && $enganche < 1 && $abono > $totalsinenganche && !$tienePromocion && $contaenganche == null) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('alerta', 'Favor de liquidar el total del contrato de contado para entregar el producto, con la cantidad: $ ' . $totalsinenganche);
                    }
                    if ($pago == 0 && ($estadocontrato == 1 || $estadocontrato == 12) && $enganche == 1 && $abono < $totalconenganche) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('alerta', 'Favor de liquidar el total del contrato de contado para entregar el producto, con la cantidad: $ ' . $totalconenganche);
                    }
                    if ($pago == 0 && $estadocontrato == 12 && $enganche < 1 && $abono < $totalsinenganche && !$tienePromocion) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('alerta', 'Favor de liquidar el total del contrato de contado para entregar el producto, con la cantidad: $ ' . $totalsinenganche);
                    }
                    if ($pago == 0 && $metodopago == 1 && $tarjetameses == 0 && $enganche < 1 && $abono != $totalsinenganche && !$tienePromocion) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('alerta', 'Favor de liquidar el total del contrato de contado en pago con tarjeta, con la cantidad: $ ' . $totalsinenganche);
                    }
                    if ($pago == 0 && $metodopago == 1 && $tarjetameses == 0 && $enganche == 1 && $abono != $totalconenganche && !$tienePromocion) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('alerta', 'Favor de liquidar el total del contrato de contado en pago con tarjeta, con la cantidad: $ ' . $totalconenganche);
                    }

                    //Validacion para promociones de contado
                    if ($pago == 0 && ((Auth::user()->rol_id) != 12 && (Auth::user()->rol_id) != 13) && $estadocontrato == 12 && $abono != $totalcontrato && $tienePromocion) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('alerta', 'Favor de liquidar el total del contrato de contado, con la cantidad: $ ' . $totalcontrato);
                    }

                    if ($pago == 0 && $estadocontrato >= 0 && $enganche == 1 && $abono == $totalconenganche) {
                        if ($estadocontrato == 0) {
                            $numeroentrega = 0;
                        } else {
                            if ($estadocontrato == 12) {
                                $numeroentrega = 1;
                            } else {
                                $numeroentrega = 0;
                            }
                        }

                        if ($metodopago == 1) {
                            $banderacase = 1;
                            return redirect()->route('payment', ["idFranquicia" => $idFranquicia, "idContrato" => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                                "nuevoabono" => $nuevoabono, "cantidadsubscripcion" => $cantidadsubscripcion]);
                        }
                        DB::table('abonos')->insert([
                            'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'tipoabono' => 5,
                            'abono' => $abono, 'poliza' => null, 'metodopago' => $metodopago, "id_zona" => $contra[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
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
                                "created_at" => $fechaHoraRegistroAbono
                            ]);
                        }

                        DB::table('historialcontrato')->insert([
                            'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Se agrego el abono : '$abono'"
                        ]);

                        $fechaentregaactualizar = null;
                        if ($numeroentrega == 1) {
                            //Se entregara el contrato
                            $fechaentregaactualizar = Carbon::now();
                        }

                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'totalabono' => $tot, 'ultimoabono' => $actualizar, 'totalhistorial' => $totalhistorialconenganche, 'entregaproducto' => $numeroentrega,
                            'fechaentrega' => $fechaentregaactualizar
                        ]);
                        $contra5 = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
                        $totalfinal = $contra5[0]->total - $abono - 200;
                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'total' => $totalfinal
                        ]);
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('bien', 'Se liquido el costo del contrato con descuento de 200 pesos');
                    }
                    if ($pago == 0 && $estadocontrato >= 0 && $enganche < 1 && $abono == $totalsinenganche) {
                        //Aqui
                        if ($estadocontrato == 0) {
                            $numeroentrega = 0;
                        } else {
                            if ($estadocontrato == 12) {
                                $numeroentrega = 1;
                            } else {
                                $numeroentrega = 0;
                            }
                        }

                        if ($metodopago == 1) {
                            $banderacase = 2;
                            return redirect()->route('payment', ["idFranquicia" => $idFranquicia, "idContrato" => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                                "nuevoabono" => $nuevoabono, "cantidadsubscripcion" => $cantidadsubscripcion]);
                        }
                        DB::table('abonos')->insert([
                            'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'tipoabono' => 5,
                            'abono' => $abono, 'poliza' => null, 'metodopago' => $metodopago, "id_zona" => $contra[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
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
                                "created_at" => $fechaHoraRegistroAbono
                            ]);
                        }

                        DB::table('historialcontrato')->insert([
                            'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Se agrego el abono : '$abono'"
                        ]);

                        $fechaentregaactualizar = null;
                        if ($numeroentrega == 1) {
                            //Se entregara el contrato
                            $fechaentregaactualizar = Carbon::now();
                        }

                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'totalabono' => $costo, 'ultimoabono' => $actualizar, 'totalhistorial' => $totalhistorialsinenganche, 'entregaproducto' => $numeroentrega,
                            'fechaentrega' => $fechaentregaactualizar
                        ]);
                        $contra5 = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");
                        $totalfinal = $contra5[0]->total - $abono - 300;
                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'total' => $totalfinal
                        ]);
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('bien', 'Se liquido el costo del contrato con descuento de 300 pesos');
                    }
                    if ($abono == $totalcontrato && $pago != 0) {
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
                        if ($metodopago == 1) {
                            $banderacase = 3;
                            return redirect()->route('payment', ["idFranquicia" => $idFranquicia, "idContrato" => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                                "nuevoabono" => $nuevoabono, "cantidadsubscripcion" => $cantidadsubscripcion]);
                        }
                        DB::table('abonos')->insert([
                            'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'atraso' => $cantidadatraso,
                            'abono' => $abono, 'metodopago' => $metodopago, 'tipoabono' => 6, "id_zona" => $contra[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
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
                                "created_at" => $fechaHoraRegistroAbono
                            ]);
                        }

                        DB::table('historialcontrato')->insert([
                            'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Se agrego el abono : '$abono'"
                        ]);

                        $fechaentregaactualizar = null;
                        if ($ent == 1) {
                            //Se entregara el contrato
                            $fechaentregaactualizar = Carbon::now();
                        }

                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'totalabono' => $costo, 'ultimoabono' => $actualizar, 'total' => 0, 'estatus_estadocontrato' => $estadocontra, 'costoatraso' => $restaatrasocontrato,
                            'entregaproducto' => $ent, 'fechaentrega' => $fechaentregaactualizar
                        ]);
                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $idContrato,
                            'estatuscontrato' => $estadocontra,
                            'created_at' => Carbon::now()
                        ]);
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('bien', 'El Abono se agregó correctamente y se liquidó el costo del contrato.');
                    }
                    if ($abono == $totalcontrato && $pago == 0 && $es == 1 && $tienePromocion) {
                        if ($metodopago == 1) {
                            $banderacase = 4;
                            return redirect()->route('payment', ["idFranquicia" => $idFranquicia, "idContrato" => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                                "nuevoabono" => $nuevoabono, "cantidadsubscripcion" => $cantidadsubscripcion]);
                        }
                        DB::table('abonos')->insert([
                            'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'abono' => $abono,
                            'metodopago' => $metodopago, 'tipoabono' => 6, "id_zona" => $contra[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
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
                                "created_at" => $fechaHoraRegistroAbono
                            ]);
                        }

                        DB::table('historialcontrato')->insert([
                            'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Se agrego el abono : '$abono'"
                        ]);

                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'totalabono' => $costo, 'ultimoabono' => $actualizar, 'total' => 0, 'entregaproducto' => 1, 'fechaentrega' => Carbon::now()
                        ]);
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('bien', 'El Abono se agrego correctamente y se liquido el costo del contrato.');
                    }
                    if ($pago != 0 && $estadocontrato < 1 && $enganche < 1 && $abono == ($totalcontrato - 100) && !$tienePromocion) {
                        if ($metodopago == 1) {
                            $banderacase = 5;
                            return redirect()->route('payment', ["idFranquicia" => $idFranquicia, "idContrato" => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                                "nuevoabono" => $nuevoabono, "cantidadsubscripcion" => $cantidadsubscripcion]);
                        }

                        DB::table('abonos')->insert([
                            'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'abono' => $abono,
                            'metodopago' => $metodopago, 'tipoabono' => 1, 'poliza' => null, "id_zona" => $contra[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
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
                                "created_at" => $fechaHoraRegistroAbono
                            ]);
                        }

                        DB::table('historialcontrato')->insert([
                            'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Se agrego el abono : '$abono'"
                        ]);

                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'totalabono' => $costo, 'ultimoabono' => $actualizar, 'total' => 0, 'entregaproducto' => 1, 'enganche' => 1, 'totalhistorial' => $totalengancheresta,
                            'fechaentrega' => Carbon::now()
                        ]);
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('bien', 'El Abono se agrego correctamente y se liquido el costo del contrato.');
                    }
                    $totalenganche = 100;
                    if ($totalproductos > 0 && $totalA < $totalproductos) {
                        $totalenganche = $totalproductos + 100;
                    }
                    switch ($pago) {
                        case 0:
                            if ($pago == 0 && $estadocontrato == 4) {
                                $minimo = $totalcontrato;
                            } else {
                                $totalcontrato = $totalcontrato - 100;
                            }
                            break;
                        case 1:
                        case 2:
                        case 4:
                            if ($abonosPeriodo[0]->total >= $abonoMinimo && $estadocontrato != 4) {
                                $minimoadelanto = $adelanto * $abonoMinimo;
                            } else {
                                $minimoadelanto = ($adelanto * $abonoMinimo) + $abonoMinimo;
                            }
                            if ($estadocontrato == 4) {
                                if ($abonosPeriodo[0]->total >= $abonoMinimo) {
                                    $minimoadelanto = ($adelanto * $abonoMinimo) + $costoatraso;
                                } else {
                                    $minimoadelanto = ($adelanto * $abonoMinimo) + $abonoMinimo + $costoatraso;
                                }
                            }
                            $minimo = $abonoMinimo;
                            break;
                    }

                    if ($estadocontrato >= 2 && $estadocontrato < 12) {
                        if ($pago == 1 && $adelanto > 0 && $abono < $minimoadelanto) {
                            return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                ->with('alerta', 'Respecto a los abonos que quieres adelantar, abonar minimo la cantida: $' . $minimoadelanto);
                        }
                        if ($pago == 1 && $abono < $abonoMinimo && $abonoperiodo == null && $costoatraso == 0 && $fechaentrega != $nowparce) {
                            return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                ->with('alerta', "No se puede abonar menos de $$abonoMinimo en pago semanal");
                        }
                        if ($pago == 2 && $adelanto > 0 && $abono < $minimoadelanto) {
                            return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                ->with('alerta', 'Respecto a los abonos que quieres adelantar, abonar minimo la cantida: $' . $minimoadelanto);
                        }
                        if ($pago == 2 && $abono < $abonoMinimo && $abonoperiodo == null && $costoatraso == 0 && $fechaentrega != $nowparce) {
                            return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                ->with('alerta', "No se puede abonar menos de $$abonoMinimo en pago Quincenal");
                        }
                        if ($pago == 4 && $adelanto > 0 && $abono < $minimoadelanto) {
                            return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                ->with('alerta', 'Respecto a los abonos que quieres adelantar, abonar minimo la cantida: $' . $minimoadelanto);
                        }
                        if ($pago == 4 && $abono < $abonoMinimo && $abonoperiodo == null && $costoatraso == 0 && $fechaentrega != $nowparce) {
                            return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                ->with('alerta', "No se puede abonar menos de $$abonoMinimo en pago Mensual");
                        }

                    }
                    if ($adelantar == 1 && $adelanto == null) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('alerta', 'Favor de elegir una cantidad de semanas a adelantar');
                    }
                    if ($adelantar == null && $adelanto != null) {
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('alerta', 'Solo permitido si elige la opcion de adelantar abono');
                    }

                    if ($estadocontrato == 4) {
                        //CONTRATO CON ABONO ATRASADO
                        if ($metodopago == 1) {
                            $banderacase = 12;
                            return redirect()->route('payment', ["idFranquicia" => $idFranquicia, "idContrato" => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                                "nuevoabono" => $nuevoabono, "cantidadsubscripcion" => $cantidadsubscripcion]);
                        }
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

                            if ($abonosPeriodo[0]->total >= $contratosGlobal::calculoCantidadFormaDePago($idFranquicia, $pago)) { //Validamos si se encuentra cubierto lo del periodo
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

                            } else if (($abonosPeriodo[0]->total + $abono) >= $contratosGlobal::calculoCantidadFormaDePago($idFranquicia, $pago)) {
                                //Validamos si los abonos del periodo + el abono ya cubren lo del periodo

                                $restanPorPagarPeriodo = $contratosGlobal::calculoCantidadFormaDePago($idFranquicia, $pago) - $abonosPeriodo[0]->total; //abono del periodo menos el total de abonos del
                                // periodo
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
                                if ($totalcontrato - $abono == 0) {
                                    $estadodelcontrato = 5;
                                    $insertarTipoAbono = 6; // Abono liquidado
                                }

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
                            'atraso' => $insertarAtraso, "id_zona" => $contra[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
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
                                "created_at" => $fechaHoraRegistroAbono
                            ]);
                        }

                        DB::table('historialcontrato')->insert([
                            'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Se agrego el abono : '$insertarAbono'"
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
                                'id' => $ID2, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'adelantos' => $adelantarpagos2,
                                'poliza' => null, 'metodopago' => $metodopago, 'tipoabono' => $insertarTipoAbono2, 'abono' => $insertarAbono2, 'atraso' => $insertarAtraso2,
                                "id_zona" => $contra[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
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
                                    "created_at" => $fechaHoraRegistroAbono
                                ]);
                            }

                            DB::table('historialcontrato')->insert([
                                'id' => $ID3, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Se agrego el abono : ' $insertarAbono2'"
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


                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('bien', 'Se agrego el abono correctamente');


                    }

                    if ($enganche < 1 && $abono >= $totalenganche && $estadocontrato <= 1 && !$tienePromocion && $contaenganche2 == null) {
                        if ($estadocontrato == 1) {
                            $tipoabonocontado = 4;

                            if ($pago != 0) {
                                $tipoabonocontado = 1;
                            }

                            if ($metodopago == 1) {
                                $banderacase = 6;
                                return redirect()->route('payment', ["idFranquicia" => $idFranquicia, "idContrato" => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                                    "nuevoabono" => $nuevoabono, "cantidadsubscripcion" => $cantidadsubscripcion]);
                            }
                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'tipoabono' => $tipoabonocontado, 'id_usuario' => $usuarioId,
                                'abono' => $abono, 'metodopago' => $metodopago, 'poliza' => null, "id_zona" => $contra[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
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
                                    "created_at" => $fechaHoraRegistroAbono
                                ]);
                            }

                            DB::table('historialcontrato')->insert([
                                'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Se abono la cantidad con enganche: '$abono'"
                            ]);

                            if ($tienePromocion && $es == 1 || $ec != null) {
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

                            return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                ->with('bien', 'El abono y el enganche se agregaron correctamente');
                        } elseif ($estadocontrato == 0 && $enganche < 1) {
                            $tipoabonocontado = 1;

                            if ($pago == 0) {
                                $tipoabonocontado = 4;
                                $polizaabo = null;
                            }
                            if ($metodopago == 1) {
                                $banderacase = 7;
                                return redirect()->route('payment', ["idFranquicia" => $idFranquicia, "idContrato" => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                                    "nuevoabono" => $nuevoabono, "cantidadsubscripcion" => $cantidadsubscripcion]);
                            }
                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'tipoabono' => $tipoabonocontado, 'id_usuario' => $usuarioId,
                                'abono' => $abono, 'metodopago' => $metodopago, 'poliza' => null, "id_zona" => $contra[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
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
                                    "created_at" => $fechaHoraRegistroAbono
                                ]);
                            }

                            DB::table('historialcontrato')->insert([
                                'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Se abono la cantidad con enganche: '$abono'"
                            ]);

                            if ($tienePromocion && $es == 1 || $ec != null) {
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

                            return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('bien', 'El abono y el enganche se agregaron correctamente');
                        }
                    }
                    if ($abono >= $abonoMinimoSemanal && $entregaproducto == 0 && $pago != 0 && $estadocontrato == 12 && ((Auth::user()->rol_id) != 12 || (Auth::user()->rol_id) == 13)) {
                        if ($metodopago == 1) {
                            $banderacase = 8;
                            return redirect()->route('payment', ["idFranquicia" => $idFranquicia, "idContrato" => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                                "nuevoabono" => $nuevoabono, "cantidadsubscripcion" => $cantidadsubscripcion]);
                        }

                        DB::table('abonos')->insert([
                            'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'tipoabono' => 2, 'abono' => $abono,
                            'poliza' => null, 'metodopago' => $metodopago, "id_zona" => $contra[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
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
                                "created_at" => $fechaHoraRegistroAbono
                            ]);
                        }

                        DB::table('historialcontrato')->insert([
                            'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar,
                            'cambios' => " Se abono la cantidad para poder entregar el producto: '$abono'"
                        ]);

                        if ($tienePromocion && $es == 1 || $ec != null) {
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
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('bien', 'El abono para la entrega de producto se agrego correctamente');
                    }
                    if (((Auth::user()->rol_id) == 4) && $pago != 0 && $abono >= $minimo && $abonoperiodo == null && $dentroRango != null && $estadocontrato >= 2) {
                        if ($metodopago == 1) {
                            $banderacase = 9;
                            return redirect()->route('payment', ["idFranquicia" => $idFranquicia, "idContrato" => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                                "nuevoabono" => $nuevoabono, "cantidadsubscripcion" => $cantidadsubscripcion]);
                        }

                        DB::table('abonos')->insert([
                            'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'tipoabono' => 3,
                            'abono' => $abono, 'poliza' => null, 'metodopago' => $metodopago, "id_zona" => $contra[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
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
                                "created_at" => $fechaHoraRegistroAbono
                            ]);
                        }

                        DB::table('historialcontrato')->insert([
                            'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar,
                            'cambios' => " Se abono la cantidad para poder entregar el producto: '$abono'"
                        ]);

                        if ($tienePromocion && $es == 1 || $ec != null) {
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
                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                            ->with('bien', 'El abono del periodo se agrego correctamente');
                    }

                    try {

                        if ($abono == $totalproductos && $abonoprod[0]->suma != $totalproductos) {
                            if ($metodopago == 1) {
                                $banderacase = 10;
                                return redirect()->route('payment', ["idFranquicia" => $idFranquicia, "idContrato" => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                                    "nuevoabono" => $nuevoabono, "cantidadsubscripcion" => $cantidadsubscripcion]);
                            }
                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'tipoabono' => 7,
                                'abono' => $abono, 'adelantos' => $adelanto, 'metodopago' => $metodopago, "id_zona" => $contra[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
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
                                    "created_at" => $fechaHoraRegistroAbono
                                ]);
                            }
                        } else {
                            if ($metodopago == 1) {
                                $banderacase = 11;
                                return redirect()->route('payment', ["idFranquicia" => $idFranquicia, "idContrato" => $idContrato, "abono" => $abono, "banderacase" => $banderacase,
                                    "nuevoabono" => $nuevoabono, "cantidadsubscripcion" => $cantidadsubscripcion]);
                            }

                            DB::table('abonos')->insert([
                                'id' => $randomId, 'folio' => $folio, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato, 'id_usuario' => $usuarioId, 'tipoabono' => 0,
                                'abono' => $abono, 'adelantos' => $adelanto, 'metodopago' => $metodopago, "id_zona" => $contra[0]->id_zona, 'created_at' => $fechaHoraRegistroAbono
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
                                    "created_at" => $fechaHoraRegistroAbono
                                ]);
                            }
                        }


                        DB::table('historialcontrato')->insert([
                            'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => " Se abono la cantidad: '$abono'"
                        ]);

                        if ($tienePromocion && $es == 1 || $ec != null) {
                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'totalabono' => $tot, 'ultimoabono' => $actualizar, 'total' => $totalcontratoresta2, 'pagosadelantar' => $adelantados,
                            ]);
                        } else {
                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                'totalabono' => $tot, 'ultimoabono' => $actualizar, 'total' => $totalcontratoresta2, 'pagosadelantar' => $adelantados,
                            ]);
                        }


                        return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('bien', 'El Abono se agrego correctamente.');
                    } catch (\Exception $e) {
                        \Log::info("Error: " . $e->getMessage());
                        return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
                    }

                }

            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    private function obtenerEstadoPromocion($idContrato, $idFranquicia)
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

    public function agregarformapago($idFranquicia, $idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {

            $rules = [
                'formapago' => 'required|string',
            ];
            if (request('formapago') != 0 && request('formapago') != 1 && request('formapago') != 2 && request('formapago') != 4) {
                return back()->withErrors(['formapago' => 'Elegir una forma de pago correcta']);
            }
            if (request('formapago') == 'nada') {
                return back()->withErrors(['formapago' => 'Elegir una forma de pago']);
            }

            request()->validate($rules);

            $contrato = DB::select("SELECT c.estatus_estadocontrato, c.fechacobroini, c.fechacobrofin, c.pago, c.diapago,
                                            (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia
                                            FROM contratos c WHERE c.id_franquicia = '$idFranquicia' AND c.id = '$idContrato'");

            if($contrato != null) {
                //Existe contrato

                if(($contrato[0]->estatus_estadocontrato == 0
                        || $contrato[0]->estatus_estadocontrato == 2
                        || $contrato[0]->estatus_estadocontrato == 4
                        || ($contrato[0]->estatus_estadocontrato == 12 && $contrato[0]->fechacobroini == null && $contrato[0]->fechacobrofin == null))) {
                    //Cumplio con las validaciones

                    $formaPagoActualizar = request('formapago');

                    if($contrato[0]->pago != $formaPagoActualizar) {
                        //Se cambio la forma de pago

                        $abonoMinimo = 0;
                        //Forma de pago diferente a la de contado?
                        if($formaPagoActualizar != 0){
                            //Forma de pago semanal,quincenal o mensual
                            $contratosGlobal  = new contratosGlobal();
                            $abonoMinimo = $contratosGlobal::calculoCantidadFormaDePago($idFranquicia, $formaPagoActualizar);
                        }

                        $fechaCobroIniActualizar = null;
                        $fechaCobroFinActualizar = null;
                        $fechaDiaSeleccionadoActualizar = null;

                        if($contrato[0]->estatus_estadocontrato != 0 && $contrato[0]->estatus_estadocontrato != 12) {
                            //ESTADO DIFERENTE A NO TERMINADO O ENVIADO

                            $ultimoAbono = DB::select("SELECT abono, folio, created_at, tipoabono FROM abonos
                                                                WHERE id_contrato = '$idContrato'
                                                                AND tipoabono != '7' ORDER BY created_at DESC LIMIT 1");

                            if($ultimoAbono != null) {
                                //Se encontro por lo menos un abono

                                $contratosGlobal = new contratosGlobal;

                                $sumatotal = $ultimoAbono[0]->abono;
                                if($ultimoAbono[0]->folio != null) {
                                    //Tiene folio el ultimo abono
                                    $sumaAbonosFolio = DB::select("SELECT SUM(abono) as sumatotal FROM abonos
                                                                WHERE id_contrato = '$idContrato'
                                                                AND folio = '" . $ultimoAbono[0]->folio . "'");
                                    $sumatotal = $sumaAbonosFolio[0]->sumatotal;
                                }else{
                                    //No tiene folio ultimo abono - abono en sucursal
                                    $fechaUltimoAbonoSFolio = Carbon::parse( $ultimoAbono[0]->created_at)->format('Y-m-d');
                                    //Suma de abonos sin folio diferentes de abonos de tipo producto y cancelacion, filtrados por fecha de creacion
                                    $sumaAbonosSFolio = DB::select("SELECT SUM(abono) as sumatotal FROM abonos
                                                                WHERE id_contrato = '$idContrato'
                                                                AND (tipoabono != 7 AND tipoabono != 8)
                                                                AND STR_TO_DATE(created_at,'%Y-%m-%d') = '$fechaUltimoAbonoSFolio'
                                                                AND folio IS NULL  ");
                                    $sumatotal = $sumaAbonosSFolio[0]->sumatotal;
                                }

                                if($sumatotal >= $contratosGlobal::calculoCantidadFormaDePago($idFranquicia, $formaPagoActualizar) || $ultimoAbono[0]->tipoabono == 2) {
                                    //Suma de abono es mayor o igual a la forma de pago a actualizar - Ultimo abono es: abono entrega de producto

                                    $fechaCreacionUltimoAbono = $ultimoAbono[0]->created_at;
                                    $tipoUltimoAbono = $ultimoAbono[0]->tipoabono;

                                    $calculofechaspago = new calculofechaspago;

                                    //Calculo fechaCobroIniActual y fechaCobroFinActual
                                    $arrayRespuesta = $calculofechaspago::obtenerFechasPeriodoActualOPeriodoSiguiente(Carbon::now(), $formaPagoActualizar, true);
                                    $fechaCobroIniActual = $arrayRespuesta[0];
                                    $fechaCobroFinActual = $arrayRespuesta[1];

                                    //Calculo fechaCobroIniSiguiente y fechaCobroFinSiguiente
                                    $arrayRespuesta = $calculofechaspago::obtenerFechasPeriodoActualOPeriodoSiguiente(Carbon::now(), $formaPagoActualizar, false);
                                    $fechaCobroIniSiguiente = $arrayRespuesta[0];
                                    $fechaCobroFinSiguiente = $arrayRespuesta[1];

                                    if(($tipoUltimoAbono == 2 && $contrato[0]->estatus_estadocontrato == 2) ||
                                        (Carbon::parse($fechaCreacionUltimoAbono)->format('Y-m-d') >= Carbon::parse($fechaCobroIniActual)->format('Y-m-d')
                                        && Carbon::parse($fechaCreacionUltimoAbono)->format('Y-m-d') <= Carbon::parse($fechaCobroFinActual)->format('Y-m-d'))) {
                                        $fechaCobroIniActualizar = $fechaCobroIniSiguiente;
                                        $fechaCobroFinActualizar = $fechaCobroFinSiguiente;
                                    }else {
                                        $fechaCobroIniActualizar = $fechaCobroIniActual;
                                        $fechaCobroFinActualizar = $fechaCobroFinActual;
                                    }

                                    //OBTENER DIASELECCIONADO
                                    if(strlen($contrato[0]->diapago) > 0) {
                                        //Se tiene un dia de pago
                                        $fechaDiaSeleccionadoActualizar = $calculofechaspago::obtenerDiaSeleccionado($contrato[0]->diapago, $fechaCobroIniActualizar, $fechaCobroFinActualizar);
                                    }

                                }else {
                                    //Suma de abono es menor a la forma de pago a actualizar
                                    return back()->with('alerta', 'Para cambiar la forma de pago es necesario que el ultimo abono cubra el minimo a la forma de pago que se quiere cambiar.');
                                }

                            }else {
                                //No se encontro ningun abono
                                return back()->with('alerta', 'No se puede cambiar la forma de pago por que no se encontro ningun abono en el contrato.');
                            }

                        }

                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'pago' => $formaPagoActualizar,
                            'abonominimo' => $abonoMinimo,
                            'fechacobroini' => $fechaCobroIniActualizar,
                            'fechacobrofin' => $fechaCobroFinActualizar,
                            'diaseleccionado' => $fechaDiaSeleccionadoActualizar
                        ]);

                        //Guardar en tabla historialcontrato
                        $randomId2 = $this->getHistorialContratoId();
                        $usuarioId = Auth::user()->id;
                        $formaPagoTexto = null;
                        if ($formaPagoActualizar == 0) {
                            $formaPagoTexto = 'Contado';
                        } elseif ($formaPagoActualizar == 1) {
                            $formaPagoTexto = 'Semanal';
                        } elseif ($formaPagoActualizar == 2) {
                            $formaPagoTexto = 'Quicenal';
                        } elseif ($formaPagoActualizar == 4) {
                            $formaPagoTexto = 'Mensual';
                        }
                        DB::table('historialcontrato')->insert([
                            'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                            'cambios' => "Se actualizo la forma de pago '$formaPagoTexto'"
                        ]);
                        return back()->with('bien', 'La forma de pago se actualizó correctamente.');

                    }
                    //Forma de pago sigue siendo la misma
                    return back()->with('alerta', 'Se esta cambiando a la misma forma de pago.');

                }
                //No cumplio con las validaciones
                return back()->with('alerta', 'No se puede cambiar la forma de pago al contrato.');

            }
            //No existe el contrato
            return back()->with('alerta', 'El contrato no existe.');

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function correo()
    {

        $datos = [
            "usuario" => "Christian",
            "Fecha" => Carbon::now(),
            "Saldo" => "1600"
        ];
        Mail::to("marcosyera10@gmail.com")->queue(new prueba($datos));
    }

    public function agregarArchivoExcel()
    {
        \Log::info("Entro");
        $jsonDatos = request("datos");
        //dd($jsonDatos); //Asi es como llegaria
        $todosLosDatos = base64_decode($jsonDatos);//Obtenemos todos los datos
        $jsonContratos = json_decode($todosLosDatos, true);
        //dd($jsonContratos);

        //RECORRIDO DE CONTRATOS PARA INSERTAR REGISTRO EN TABLA CONTRATOS Y HISTORIALCLINICO
        if (!empty($jsonContratos)) {
            //jsonContratos es diferente a vacio

            foreach ($jsonContratos as $contrato) {

                $fechaActual = Carbon::now()->format('Y-m-d H:i:s');

                $IDCONTRATO = $this->getContratoId();
                $IDFRANQUICIA = self::validacionDeNulo($contrato['IDFRANQUICIA']);
                $IDZONA = self::validacionDeNulo($contrato['IDZONA']);
                $NOMBRE = self::validacionDeNulo($contrato['NOMBRE']);
                $CALLE = self::validacionDeNulo($contrato['CALLE']);
                $NUMERO = self::validacionDeNulo($contrato['NUMERO']);
                $COLONIA = self::validacionDeNulo($contrato['COLONIA']);
                $LOCALIDAD = self::validacionDeNulo($contrato['LOCALIDAD']);
                $TELEFONO = self::validacionDeNulo($contrato['TELEFONO']);
                $TELEFONOREFERENCIA = self::validacionDeNulo($contrato['TELEFONOREFERENCIA']);
                $FORMADEPAGO = self::validacionDeNulo($contrato['FORMADEPAGO']);
                $TOTAL = self::validacionDeNulo($contrato['TOTAL']);
                $ESTATUS_ESTADOCONTRATO = 2;
                if ($TOTAL == null || $TOTAL == 0) {
                    $ESTATUS_ESTADOCONTRATO = 5;
                }
                \Log::info($IDCONTRATO);

                //Crear contrato
                DB::table("contratos")->insert([
                    "id" => $IDCONTRATO,
                    "datos" => '1',
                    "id_franquicia" => $IDFRANQUICIA,
                    "id_usuariocreacion" => '335', //Agregar el id_usuariocreacion
                    "nombre_usuariocreacion" => 'SISTEMAS TUXPAN', //Agregar el nombre_usuariocreacion
                    "id_zona" => $IDZONA,
                    "nombre" => $NOMBRE,
                    "calle" => $CALLE,
                    "numero" => $NUMERO,
                    "colonia" => $COLONIA,
                    "localidad" => $LOCALIDAD,
                    "telefono" => $TELEFONO,
                    "telefonoreferencia" => $TELEFONOREFERENCIA,
                    "id_optometrista" => '335', //Agregar el id_optometrista
                    "pago" => $FORMADEPAGO,
                    "total" => $TOTAL,
                    "contador" => '1',
                    "totalhistorial" => $TOTAL,
                    "estatus_estadocontrato" => $ESTATUS_ESTADOCONTRATO,
                    "fechaentrega" => $fechaActual,
                    "enganche" => 1,
                    "entregaproducto" => 1,
                    "poliza" => 1,
                    "created_at" => $fechaActual,
                    "updated_at" => $fechaActual,
                    'fechacobroini' => '2021-10-29',
                    'fechacobrofin' => '2021-11-07',
                ]);

                //Insertar en tabla registroestadocontrato
                DB::table('registroestadocontrato')->insert([
                    'id_contrato' => $IDCONTRATO,
                    'estatuscontrato' => $ESTATUS_ESTADOCONTRATO,
                    'created_at' => Carbon::now()
                ]);

                //Crear historial clinico
                $IDHISTORIAL = $this->getHistorialContratoId();
                $FECHAENTREGA = Carbon::now()->format('Y-m-d');
                DB::table("historialclinico")->insert([
                    "id" => $IDHISTORIAL,
                    "id_contrato" => $IDCONTRATO,
                    "edad" => '0',
                    "fechaentrega" => $FECHAENTREGA,
                    "diagnostico" => '?????',
                    "hipertension" => '?????',
                    "id_producto" => '1', //Agregar el idproducto
                    "id_paquete" => '1', //Agregar el idpaquete
                    "created_at" => $fechaActual,
                    "updated_at" => $fechaActual
                ]);

            }

        }//CONTRATOS

    }

    public static function liquidarContratosArchivo()
    {
        \Log::info("Entro");
        $jsonDatos = request("datos");
        //dd($jsonDatos); //Asi es como llegaria
        $todosLosDatos = base64_decode($jsonDatos);//Obtenemos todos los datos
        $jsonContratos = json_decode($todosLosDatos, true);
        //dd($jsonContratos);
        $idUsuario = "416";
        $idFranquicia = "6E2AA";

        //RECORRIDO DE CONTRATOS PARA INSERTAR REGISTRO EN TABLA CONTRATOS Y HISTORIALCLINICO
        if (!empty($jsonContratos)) {
            //jsonContratos es diferente a vacio

            $ahora = Carbon::now();
            foreach ($jsonContratos as $contrato) {
                $idContrato = $contrato['IDCONTRATO'];
                \Log::info("CONTRATO: $idContrato");
                $existeContrato = DB::select("SELECT id,estatus_estadocontrato,total,totalabono,costoatraso,id_zona FROM contratos WHERE id = '$idContrato'");
                if ($existeContrato != null) {
                    if ($existeContrato[0]->estatus_estadocontrato == 2 || $existeContrato[0]->estatus_estadocontrato == 4 || $existeContrato[0]->estatus_estadocontrato == 12) {
                        $totalabono = $existeContrato[0]->totalabono;
                        $total = $existeContrato[0]->total;
                        $idAbono = self::getAbonosContratoId();

                        $atraso = 0;
                        if ($existeContrato[0]->estatus_estadocontrato == 4) {
                            $atraso = $existeContrato[0]->costoatraso;
                        }
                        try {
                            DB::table("contratos")->where("id", "=", $idContrato)->update([
                                "estatus_estadocontrato" => 5,
                                "ultimoabono" => $ahora,
                                "totalabono" => $totalabono + $total,
                                "total" => 0,
                                "costoatraso" => 0,
                                "entregaproducto" => 1,
                                "estatusanteriorcontrato" => $existeContrato[0]->estatus_estadocontrato
                            ]);

                            //Insertar en tabla registroestadocontrato
                            DB::table('registroestadocontrato')->insert([
                                'id_contrato' => $idContrato,
                                'estatuscontrato' => 5,
                                'created_at' => Carbon::now()
                            ]);

                            DB::table('abonos')->insert([
                                "id" => $idAbono,
                                "id_franquicia" => $idFranquicia,
                                "id_contrato" => $idContrato,
                                "id_usuario" => $idUsuario,
                                "abono" => $total,
                                "adelantos" => 0,
                                "tipoabono" => 6,
                                "atraso" => $atraso,
                                "poliza" => 0,
                                "metodopago" => 0,
                                "id_zona" => $existeContrato[0]->id_zona,
                                "created_at" => $ahora
                            ]);
                        } catch (\Exception $e) {
                            \Log::info("Error: $e");
                        }
                    }

                } else {
                    \Log::info("No se encontro el contrato: $idContrato");
                }
            }
        }
    }


    private static function validacionDeNulo($valor)
    {
        $respuesta = null;
        if (strlen($valor) > 0) {
            $respuesta = $valor;
        }
        return $respuesta;
    }

    public function agregarnota($idFranquicia, $idContrato)
    {

        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12)
            || ((Auth::user()->rol_id) == 4) || ((Auth::user()->rol_id) == 6)) {

            request()->validate([
                'nota' => 'nullable|string|max:255'
            ]);

            $nota = request("nota");
            if ($nota == null || strlen($nota) == 0) {
                $nota = "";
            }

            DB::table("contratos")->where("id_franquicia", "=", $idFranquicia)->where("id", "=", $idContrato)->update([
                "nota" => $nota
            ]);
            return back()->with("bien", "La nota se actualizo correctamente.");

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    private function obtenerFechaUltimoAbonoDadoEnContrato($idFranquicia, $idContrato)
    {
        $respuesta = null;

        $abono = DB::select("SELECT a.created_at FROM abonos a INNER JOIN contratos c ON c.id = a.id_contrato WHERE c.id = '$idContrato' ORDER BY a.created_at DESC limit 1");

        if ($abono != null) {
            //Se encontro abono anterior
            $respuesta = $abono[0]->created_at;
        }

        return $respuesta;
    }

    public function agregarhistorialmovimientocontrato($idFranquicia, $idContrato)
    {
        if (Auth::check() && (((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8))) {
            //Rol administrador, director o principal

            $rules = [
                'movimiento' => 'required|string',
                'tipoMovimiento' => 'required',
            ];

            request()->validate($rules);

            try {

                $contrato = DB::select("SELECT c.estatus_estadocontrato as estatus_estadocontrato
                                                FROM contratos c WHERE c.id_franquicia = '$idFranquicia' AND c.id = '$idContrato'");

                if ($contrato != null) {
                    //Existe el contrato

                    $movimiento = request('movimiento');
                    $tipoMovimiento = request('tipoMovimiento');

                    if (strlen($movimiento) == 0) {
                        return back()->with('alerta', "Favor de agregar el mensaje de movimiento");
                    }

                    if($tipoMovimiento == 5 || $tipoMovimiento == 6){
                        //Tipo de movimientos: Normal (Movimiento manual), Seguimiento lio/fuga garantia.

                        //Guardar en tabla historialcontrato
                        $usuarioId = Auth::user()->id;
                        DB::table('historialcontrato')->insert([
                            'id' => $this->getHistorialContratoId(), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                            'cambios' => "Agrego el movimiento: " . $movimiento, 'tipomensaje' => $tipoMovimiento
                        ]);

                        return back()->with('bien', "Se agrego correctamente el movimiento.");
                    }

                    return back()->with('alerta', 'Selecciona un tipo de movimiento valido.');
                }
                return back()->with('alerta', 'No se encontro el contrato.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function eliminarhistorialmovimientocontrato($idFranquicia, $idContrato, $idHistorial)
    {
        if (Auth::check() && (((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8))) {
            //Rol administrador, director o principal

            //Usuario logueado
            $idUsuario = Auth::user()->id;
            $historialContrato = DB::select("SELECT * FROM historialcontrato hc where id = '$idHistorial' AND hc.id_contrato = '$idContrato'
                                                        AND hc.id_usuarioC = '$idUsuario' ORDER BY hc.created_at DESC");

            if ($historialContrato != null) {
                //Existe el historial del contrato - Fue creado por el mismo usuario que desea eliminarlo

                $fechaActual = Carbon::now();
                $fechaLimiteEliminacion = date('Y-m-d H:i:s', strtotime($historialContrato[0]->created_at . "+15 minutes"));

                if(Carbon::parse($fechaActual) < Carbon::parse($fechaLimiteEliminacion)){
                    //Movimiento creado hace menos de 15 minutos

                    //Eliminar movimiento
                    DB::delete("DELETE FROM historialcontrato WHERE id = '$idHistorial' AND id_contrato = '$idContrato' AND id_usuarioC = '$idUsuario'");

                    //Registrar movimiento de eliminacion
                    DB::table('historialsucursal')->insert([
                        'id_usuarioC' => $idUsuario, 'id_franquicia' => $idFranquicia,
                        'tipomensaje' => '0', 'created_at' => Carbon::now(), 'cambios' => "Elimino movimiento: '" . $historialContrato[0]->cambios . "' registrado en historial del contrato", 'seccion' => '1'
                    ]);

                    return back()->with('bien', "Movimiento eliminado correctamente.");
                }
                return back()->with('bien', "El tiempo para eliminar movimientos en el historial del contrato ha vencido.");
            }
            return back()->with('alerta', 'No hay registro del movimiento en el historial del contrato.');

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function solicitarautorizacionsupervisarcontrato($idFranquicia, $idContrato){
        if (Auth::check() && (((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8))) {
            //Rol administrador, director o principal

            request()->validate([
                'mensaje' => 'required|string|min:15|max:1000'
            ]);

            try {

                $contrato = DB::select("SELECT c.estatus_estadocontrato as estatus_estadocontrato, created_at,
                                                (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia
                                                    FROM contratos c WHERE c.id_franquicia = '$idFranquicia' AND c.id = '$idContrato'");

                if ($contrato != null) {
                    //Existe el contrato

                    $estadocontrato = $contrato[0]->estatus_estadocontrato;

                    if ($estadocontrato == 2 || $estadocontrato == 4 || $estadocontrato == 12) {

                        $estadogarantia = $contrato[0]->estadogarantia;

                        if ($estadogarantia == null || $estadogarantia >= 2) {
                            //estadogarantia ya fue creada

                            //Generar registro de solicitud
                            $idUsuario = Auth::user()->id;
                            DB::table('autorizaciones')->insert([
                                'id_contrato' => $idContrato, 'id_usuarioC' => $idUsuario, 'id_franquicia' => $idFranquicia,
                                'fechacreacioncontrato' => $contrato[0]->created_at,
                                'estadocontrato' => $contrato[0]->estatus_estadocontrato,
                                'mensaje' => "Solicitó autorizacion para el cambio de estatus del contrato a supervisión con el siguiente mensaje: '" . request("mensaje") . "'",
                                'estatus' => '0', 'tipo' => '12', 'created_at' => Carbon::now()
                            ]);

                            //Registrar movimiento
                            $globalesServicioWeb = new globalesServicioWeb;
                            DB::table('historialcontrato')->insert([
                                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $idUsuario,
                                'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                'cambios' => "Solicitó autorizacion para el cambio de estatus del contrato a supervisión con el siguiente mensaje: '" . request("mensaje") . "'", 'tipomensaje' => '3']);

                            return back()->with('bien', 'Solicitud de cambio de estatus del contrato a supervision generada correctamente');
                        }

                        return back()->with('alerta', 'No se puede generar la solicitud porque el contrato (Tiene una garantia pendiente/Tiene reportada una garantia)');
                    }

                    return back()->with('alerta', 'No se puede generar la solicitud porque necesita el contrato estar en estado ENTREGA/ABONO ATRASADO/ENVIADO.');

                }
                return back()->with('alerta', 'No se encontro el contrato.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function restablecercontrato($idFranquicia, $idContrato)
    {
        if (Auth::check() && (((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8))) {
            //Rol administrador, director o principal

            try {

                $contrato = DB::select("SELECT c.estatus_estadocontrato as estatus_estadocontrato, c.id_zona as id_zona
                                                    FROM contratos c WHERE c.id_franquicia = '$idFranquicia' AND c.id = '$idContrato'");

                if ($contrato != null) {
                    //Existe el contrato

                    $estadocontrato = $contrato[0]->estatus_estadocontrato;
                    $id_zona = $contrato[0]->id_zona;

                    if ($estadocontrato == 15) {

                        $registroestadocontrato = DB::select("SELECT estatuscontrato
                                                    FROM registroestadocontrato WHERE id_contrato = '$idContrato' AND estatuscontrato != '15' ORDER BY created_at DESC LIMIT 1");

                        $estadoactualizar = 2;
                        if ($registroestadocontrato != null) {
                            $estadoactualizar = $registroestadocontrato[0]->estatuscontrato;
                        }

                        //Actualizar estado
                        DB::table("contratos")->where("id", "=", $idContrato)->where("id_franquicia", "=", $idFranquicia)->update([
                            'estatus_estadocontrato' => $estadoactualizar
                        ]);

                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $idContrato,
                            'estatuscontrato' => $estadoactualizar,
                            'created_at' => Carbon::now()
                        ]);

                        //Guardar en tabla historialcontrato
                        $usuarioId = Auth::user()->id;
                        DB::table('historialcontrato')->insert([
                            'id' => $this->getHistorialContratoId(), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                            'cambios' => "Se restauro el estatus del contrato de 'supervision'"
                        ]);

                        $contratosGlobal = new contratosGlobal;

                        //ELiminar contrato de tabla contratosliofuga
                        $contratosGlobal::insertarEliminarContratosLioFuga($idContrato, "",1);

                        $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($id_zona); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona

                        if ($cobradoresAsignadosAZona != null) {
                            //Existen cobradores
                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $cobradorAsignadoAZona->id);

                                $abonos = DB::select("SELECT * FROM abonos WHERE id_contrato ='$idContrato' ORDER BY created_at DESC");
                                //Insertar abonos en tabla abonoscontratostemporalessincronizacion
                                foreach ($abonos as $abono) {
                                    //Recorrido abonos
                                    //Insertar en tabla abonoscontratostemporalessincronizacion
                                    DB::table("abonoscontratostemporalessincronizacion")->insert([
                                        "id_usuariocobrador" => $cobradorAsignadoAZona->id,
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
                        }

                        return back()->with("bien", "El estatus del contrato se actualizo correctamente");

                    }

                    return back()->with('alerta', 'No se puede restablecer el contrato, debe estar en estado de SUPERVISION.');

                }
                return back()->with('alerta', 'No se encontro el contrato.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function migrarcuentas($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7)) {
            //Rol director

            return view('administracion.contrato.tablamigrarcuentas', [
                "idFranquicia" => $idFranquicia
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function migrarcuentasarchivo($idFranquicia, Request $request)
    {

        $archivoExcel = $request->file('archivo');
        if ($archivoExcel != null) {
            //Existe archivo

            if (request()->hasFile('archivo')) {

                $extension = $archivoExcel->getClientOriginalExtension();
                if ($extension == "xlsx" || $extension == "xls") { //Es un archivo de excel?

                    try {

                        $errores = array();
                        $filas = Excel::toArray(new CsvImport(), $archivoExcel);

                        $globalesServicioWeb = new globalesServicioWeb;

                        $anoActual = Carbon::now()->format('y'); //Obtener los ultimos 2 digitos del año 21, 22, 23, 24, etc

                        //Obtener indice de la franquicia
                        $franquicia = DB::select("SELECT indice FROM franquicias WHERE id = '$idFranquicia'");
                        $identificadorFranquicia = "";
                        if ($franquicia != null) {
                            //Existe franquicia
                            $identificadorFranquicia = $globalesServicioWeb::obtenerIdentificadorFranquicia($franquicia[0]->indice);
                        }
                        $identificadorFranquicia = $anoActual . $identificadorFranquicia; //Seria el identificadorFranquicia completo = 22001, 22002, 22003, etc

                        //Obtener el ultimo id generado en la tabla de contrato
                        $contratoSelect = DB::select("SELECT id FROM contratos WHERE id_franquicia = '$idFranquicia' AND id LIKE '%$identificadorFranquicia%' ORDER BY id DESC LIMIT 1");
                        if ($contratoSelect != null) {
                            //Existe registro (Significa que ya hay contratos personalizados creados)
                            $idContrato = substr($contratoSelect[0]->id, -5);
                            $ultimoIdContratoPerzonalizado = $idContrato;
                        } else {
                            //Sera el primer contrato perzonalizado a crear de la sucursal
                            $ultimoIdContratoPerzonalizado = 0;
                        }

                        foreach ($filas[0] as $key => $contrato) {

                            try {

                                $fechaActual = Carbon::now()->format('Y-m-d H:i:s');

                                $arrayRespuesta = $globalesServicioWeb::generarIdContratoPersonalizado($identificadorFranquicia, $ultimoIdContratoPerzonalizado);
                                $IDCONTRATO = $arrayRespuesta[0];
                                $IDZONA = $contrato[0];
                                if (strlen($IDZONA) == 0) {
                                    //IDZONA es igual a vacio
                                    $IDZONA = null;
                                }

                                $NOMBRE = $contrato[1];
                                if (strlen($NOMBRE) == 0) {
                                    //NOMBRE es igual a vacio
                                    $NOMBRE = null;
                                }

                                $CALLE = $contrato[2];
                                if (strlen($CALLE) == 0) {
                                    //CALLE es igual a vacio
                                    $CALLE = null;
                                }

                                $NUMERO = $contrato[3];
                                if (strlen($NUMERO) == 0) {
                                    //NUMERO es igual a vacio
                                    $NUMERO = null;
                                }

                                $COLONIA = $contrato[4];
                                if (strlen($COLONIA) == 0) {
                                    //COLONIA es igual a vacio
                                    $COLONIA = null;
                                }

                                $LOCALIDAD = $contrato[5];
                                if (strlen($LOCALIDAD) == 0) {
                                    //LOCALIDAD es igual a vacio
                                    $LOCALIDAD = null;
                                }

                                $TELEFONO = $contrato[6];
                                if (strlen($TELEFONO) == 0) {
                                    //TELEFONO es igual a vacio
                                    $TELEFONO = null;
                                }

                                $TELEFONOREFERENCIA = $contrato[7];
                                if (strlen($TELEFONOREFERENCIA) == 0) {
                                    //TELEFONOREFERENCIA es igual a vacio
                                    $TELEFONOREFERENCIA = null;
                                }

                                $FORMADEPAGO = $contrato[8];
                                if (strlen($FORMADEPAGO) == 0) {
                                    //FORMADEPAGO es igual a vacio
                                    $FORMADEPAGO = null;
                                }

                                $TOTAL = $contrato[9];
                                if (strlen($TOTAL) == 0) {
                                    //TOTAL es igual a vacio
                                    $TOTAL = null;
                                }

                                $IDENTIFICADORARCHIVO = $contrato[10];
                                if (strlen($IDENTIFICADORARCHIVO) > 0) {
                                    //Contiene algo el IDENTIFICADORARCHIVO
                                    $IDENTIFICADORARCHIVO = "CONTRATO FISICO: " . $IDENTIFICADORARCHIVO;
                                } else {
                                    //IDENTIFICADORARCHIVO esta vacio
                                    $IDENTIFICADORARCHIVO = null;
                                }

                                $ESTATUS_ESTADOCONTRATO = 2;
                                if ($TOTAL == null || $TOTAL == 0) {
                                    $ESTATUS_ESTADOCONTRATO = 5;
                                }

                                //\Log::info($IDCONTRATO);

                                //Crear contrato
                                DB::table("contratos")->insert([
                                    "id" => $IDCONTRATO,
                                    "datos" => '1',
                                    "id_franquicia" => $idFranquicia,
                                    "id_usuariocreacion" => '749', //Agregar el id_usuariocreacion
                                    "nombre_usuariocreacion" => 'SISTEMAS XONACATLAN', //Agregar el nombre_usuariocreacion
                                    "id_zona" => $IDZONA,
                                    "nombre" => $NOMBRE,
                                    "calle" => $CALLE,
                                    "numero" => $NUMERO,
                                    "colonia" => $COLONIA,
                                    "localidad" => $LOCALIDAD,
                                    "telefono" => $TELEFONO,
                                    "telefonoreferencia" => $TELEFONOREFERENCIA,
                                    "id_optometrista" => '749', //Agregar el id_optometrista
                                    "pago" => $FORMADEPAGO,
                                    "total" => $TOTAL,
                                    "totalreal" => $TOTAL,
                                    "contador" => '1',
                                    "totalhistorial" => $TOTAL,
                                    "estatus_estadocontrato" => $ESTATUS_ESTADOCONTRATO,
                                    "fechaentrega" => $fechaActual,
                                    "enganche" => 0,
                                    "entregaproducto" => 1,
                                    "poliza" => null,
                                    "created_at" => $fechaActual,
                                    "updated_at" => $fechaActual,
                                    'fechacobroini' => null,
                                    'fechacobrofin' => null
                                ]);

                                //Insertar en tabla registroestadocontrato
                                DB::table('registroestadocontrato')->insert([
                                    'id_contrato' => $IDCONTRATO,
                                    'estatuscontrato' => $ESTATUS_ESTADOCONTRATO,
                                    'created_at' => $fechaActual
                                ]);

                                //Crear historial clinico
                                $IDHISTORIAL = $globalesServicioWeb::generarIdAlfanumerico('historialclinico', '5');
                                $FECHAENTREGA = Carbon::now()->format('Y-m-d');
                                DB::table("historialclinico")->insert([
                                    "id" => $IDHISTORIAL,
                                    "id_contrato" => $IDCONTRATO,
                                    "edad" => '0',
                                    "fechaentrega" => $FECHAENTREGA,
                                    "diagnostico" => '?????',
                                    "hipertension" => '?????',
                                    "esfericoder" => '0',
                                    "cilindroder" => '0',
                                    "ejeder" => '0',
                                    "addder" => '0',
                                    "altder" => '0',
                                    "esfericoizq" => '0',
                                    "cilindroizq" => '0',
                                    "ejeizq" => '0',
                                    "addizq" => '0',
                                    "altizq" => '0',
                                    "id_producto" => '00000', //idproducto PROPIO
                                    "id_paquete" => '1', //Agregar el idpaquete
                                    "material" => '1', //CR
                                    "bifocal" => '3', //NA
                                    "fotocromatico" => '0',
                                    "ar" => '1',
                                    "tinte" => '0',
                                    "blueray" => '0',
                                    "otroT" => '0',
                                    "observacionesinterno" => $IDENTIFICADORARCHIVO,
                                    "created_at" => $fechaActual,
                                    "updated_at" => $fechaActual
                                ]);

                                $ultimoIdContratoPerzonalizado = $arrayRespuesta[1] + 1;

                                $IDENTIFICADORARCHIVO = $IDENTIFICADORARCHIVO == null ? "NA" : $IDENTIFICADORARCHIVO;
                                \Log::info($IDENTIFICADORARCHIVO . ", CODIGO CONTRATO GENERADO: " . $IDCONTRATO);

                                if($IDZONA != null) {
                                    //idzona es diferente de null
                                    $cobradoresAsignadosAZona = DB::select("SELECT u.id
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id = uf.id_usuario
                                              WHERE u.rol_id = 4 AND u.id_zona = '" . $IDZONA . "'"); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona

                                    $contratosGlobal = new contratosGlobal;
                                    if ($cobradoresAsignadosAZona != null) {
                                        //Existen cobradores
                                        foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                            //Recorrido cobradores
                                            $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($IDCONTRATO, $cobradorAsignadoAZona->id);
                                        }
                                    }
                                }

                            } catch (\Exception $e) {
                                \Log::info("Error al migrar cuenta: " . $contrato[1] . "\n" . $e);
                                continue;
                            }

                        }

                        return back()->with("bien", "Se migraron correctamente los contratos.");

                    } catch (\Exception $e) {
                        \Log::info("ERROR: " . $e);
                        return back()->with("error", "Tuvimos un problema, por favor contacta al administrador de la pagina.");
                    }

                }

                return back()->with("alerta", "Por favor, selecciona un archivo valido.");

            }

        }

        return back()->with("alerta", "Por favor, selecciona un archivo.");

    }

    public function traspasarcontrato($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 15)) {
            //Rol administrador, director, principal, confirmaciones

            $contrato = null;
            $idContrato = "";
            $id_franquicia = $idFranquicia;


            return view('administracion.contrato.traspasarcontrato', [
                'idFranquicia' => $id_franquicia,
                'idContrato' => $idContrato,
                'contrato' => $contrato
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function buscarcontratotraspasar($idFranquicia)
    {

        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 15)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL

            //Valor de idContrato
            $idContrato = request('idContrato');

            //Validacion de campo
            request()->validate([
                'idContrato' => 'required|string'
            ]);

            return redirect()->route('obtenercontratotraspasar', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function cargarpromocionesfranquicia(Request $request)
    {

        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 15)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL - ADMINISTRACION
            $id_franquicia = $request->input("sucursalSeleccionada");
            $zonas = DB::select("SELECT z.id, z.zona FROM zonas z WHERE z.id_franquicia = '$id_franquicia' ORDER BY z.zona ASC");
            //Promociones para la sucursal seleccionada
            $promociones = DB::select("SELECT p.id, p.titulo FROM promocion p WHERE p.id_franquicia = '$id_franquicia'");

            $response = ['promociones' => $promociones, 'zonas' => $zonas];

            return response()->json($response);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function obtenercontratotraspasar($idFranquicia, $idContrato)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 15)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL

            $existeContrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato'");

            if ($existeContrato != null) {
                //Si existe contrato

                $franquiciaContratoExistente = $existeContrato[0]->id_franquicia;
                $rolUsuario = Auth::user()->rol_id;
                $idUsuario = Auth::user()->id;

                switch ($rolUsuario) {
                    case 7:
                        //Si es director puede acceder a todos los contratos
                        if ($existeContrato[0]->estatus_estadocontrato == '1' || $existeContrato[0]->estatus_estadocontrato == '2' || $existeContrato[0]->estatus_estadocontrato == '4' ||
                            $existeContrato[0]->estatus_estadocontrato == '5' || $existeContrato[0]->estatus_estadocontrato == '7' || $existeContrato[0]->estatus_estadocontrato == '9' ||
                            $existeContrato[0]->estatus_estadocontrato == '10' || $existeContrato[0]->estatus_estadocontrato == '11' || $existeContrato[0]->estatus_estadocontrato == '12') {
                            //El contrato tiene estatus 1, 2, 4, 5, 7, 9, 10, 11, 12

                            $contrato = DB::select("SELECT c.nombre AS nombreCliente, c.telefono AS telefono, c.telefonoreferencia AS telefonoReferencia,
                                                    c.localidad, c.colonia, c.calle, c.numero, (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) as zona, c.totalreal AS total, c.total AS saldo, c.ultimoabono,
                                                    (SELECT p.titulo FROM promocion p WHERE p.id = c.id_promocion) AS promociones,
                                                    (SELECT f.ciudad FROM franquicias f WHERE f.id = c.id_franquicia) AS sucursalActual,
                                                    c.estatus_estadocontrato AS estatus_estadocontrato
                                                    FROM contratos c
                                                    WHERE c.id = '$idContrato' AND c.id_franquicia = '$franquiciaContratoExistente'");

                            $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f WHERE (f.id != '$franquiciaContratoExistente') ORDER BY ciudad ASC");

                            $idFranquicia = $franquiciaContratoExistente;
                        } else {
                            //El contrato no tiene estatus de atrasado, aprovado, manufactura, proceso de envio o enviado
                            return redirect()->route('traspasarcontrato', [$idFranquicia])->with('alerta', 'No puedes generar un traspaso de este contrato debido a su estatus actual.');
                        }
                        break;  // Fin de case

                    case 6:
                        //Rol de Administrador
                    case 8:
                        //Rol de Principal
                        if ($existeContrato[0]->id_franquicia == $idFranquicia) {
                            //La sucursal del usuario es igual a la del contrato

                            if ($existeContrato[0]->estatus_estadocontrato == '2' || $existeContrato[0]->estatus_estadocontrato == '4' || $existeContrato[0]->estatus_estadocontrato == '5' ||
                                $existeContrato[0]->estatus_estadocontrato == '7' || $existeContrato[0]->estatus_estadocontrato == '10' || $existeContrato[0]->estatus_estadocontrato == '11' ||
                                $existeContrato[0]->estatus_estadocontrato == '12') {
                                //El contrato tiene estatus 4, 7, 10, 11, 12

                                $contrato = DB::select("SELECT c.nombre AS nombreCliente, c.telefono AS telefono, c.telefonoreferencia AS telefonoReferencia,
                                                    c.localidad, c.colonia, c.calle, c.numero, (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) as zona, c.totalreal AS total, c.total AS saldo, c.ultimoabono,
                                                    (SELECT p.titulo FROM promocion p WHERE p.id = c.id_promocion) AS promociones,
                                                    (SELECT f.ciudad FROM franquicias f WHERE f.id = c.id_franquicia) AS sucursalActual,
                                                    c.estatus_estadocontrato AS estatus_estadocontrato
                                                    FROM contratos c
                                                    WHERE c.id = '$idContrato' AND c.id_franquicia = '$idFranquicia'");

                                //Si el rol es Administrador, Principal o confirmaciones excluir franquicia de Pruebas
                                $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where (f.id != '00000' AND f.id != '$idFranquicia') ORDER BY ciudad ASC");

                            } else {
                                //El contrato no tiene estatus de atrasado, aprovado, manufactura, proceso de envio o enviado
                                return redirect()->route('traspasarcontrato', [$idFranquicia])->with('alerta', 'No puedes generar un traspaso de este contrato debido a su estatus actual.');
                            }
                        } else {
                            //El contrato encontrado pertenece a otra sucursal
                            return redirect()->route('traspasarcontrato', [$idFranquicia])->with('alerta', 'Contrato perteneciente a otra sucursal');
                        }
                        break;  //Fin de case

                    case 15:
                        //Rol de Confirmaciones

                        $existeGarantia = DB::select("SELECT * FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia = '2' ORDER BY created_at ASC limit 1");

                        if ($existeGarantia == null) {
                            //No tiene garantia el contrato
                            $bandera = false;
                            //optenemos sucursales asignadas
                            $franquicias = DB::select("SELECT f.id FROM franquicias f
                                                INNER JOIN sucursalesconfirmaciones sf ON f.id = sf.id_franquicia
                                                WHERE f.id != '00000' AND sf.id_usuario = '$idUsuario' ORDER BY ciudad ASC");

                            foreach ($franquicias as $franquicia) {
                                if ($franquicia->id == $franquiciaContratoExistente) {
                                    //Si la sucursal del contrato pertenece a una asiganada a confirmaciones
                                    $bandera = true; // Bandera colcar en verdadero y salir del ciclo
                                    break;  //Fin de ciclo
                                }
                            }
                            if ($bandera == true) {
                                //El contrato pertenece a una sucursal asiganada a confirmaciones
                                if ($existeContrato[0]->estatus_estadocontrato == '1' || $existeContrato[0]->estatus_estadocontrato == '9' || $existeContrato[0]->estatus_estadocontrato == '7') {
                                    //El contrato tiene estatus de TERMINADO, PROCESO DE APROBACION O APROBADO

                                    $contrato = DB::select("SELECT c.nombre AS nombreCliente, c.telefono AS telefono, c.telefonoreferencia AS telefonoReferencia,
                                                    c.localidad, c.colonia, c.calle, c.numero, (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) as zona, c.totalreal AS total, c.total AS saldo, c.ultimoabono,
                                                    (SELECT p.titulo FROM promocion p WHERE p.id = c.id_promocion) AS promociones,
                                                    (SELECT f.ciudad FROM franquicias f WHERE f.id = c.id_franquicia) AS sucursalActual,
                                                    c.estatus_estadocontrato AS estatus_estadocontrato
                                                    FROM contratos c
                                                    WHERE c.id = '$idContrato' AND c.id_franquicia = '$franquiciaContratoExistente'");

                                    //Si el rol es Administrador, Principal o confirmaciones excluir franquicia de Pruebas
                                    $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where (f.id != '00000' AND f.id != '$franquiciaContratoExistente')
                                                                     ORDER BY ciudad ASC");

                                } else {
                                    //El contrato no tiene estatus de atrasado, aprovado, manufactura, proceso de envio o enviado
                                    return redirect()->route('traspasarcontrato', [$idFranquicia])->with('alerta', 'No puedes generar un traspaso de este contrato debido a su estatus actual.');
                                }
                            } else {
                                //No pertenece a una sucursal asignada a confirmaciones
                                return redirect()->route('traspasarcontrato', [$idFranquicia])->with('alerta', 'Contrato perteneciente a otra sucursal');
                            }

                            break; // Fin de case

                        } else {
                            //Si tiene garantia el contrato
                            return redirect()->route('traspasarcontrato', [$idFranquicia])->with('alerta', 'No se puede hacer el traspaso de este contrato debido que presenta una garantia');
                        }
                }

                $solicitudAutorizacion = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato = '$idContrato' AND a.tipo = 6 ORDER BY a.created_at DESC LIMIT 1");
                $garantias = DB::select("SELECT * FROM garantias g WHERE g.id_contrato = '$idContrato' ORDER BY g.created_at DESC LIMIT 1");

                return view('administracion.contrato.traspasarcontrato',
                    ['contrato' => $contrato,
                        'idContrato' => $idContrato,
                        'idFranquicia' => $idFranquicia,
                        'franquicias' => $franquicias,
                        'solicitudAutorizacion' => $solicitudAutorizacion,
                        'garantias' => $garantias
                    ]);

            } else {
                //No existe el contrato
                return redirect()->route('traspasarcontrato', [$idFranquicia])->with('alerta', 'Contrato no existente, verifica el ID CONTRATO');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function generartraspasocontrato($idFranquicia, $idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 15)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL - CONFIRMACIONES

            //Valor de sucursal traspaso
            $franquiciaTraspaso = $request->input('sucursalSeleccionada');
            $zonaTraspaso = $request->input('zonaSeleccionada');
            $promocionTraspaso = $request->input('promocionSeleccionada');
            $promocionContrato = $request->input('promocionContrato');
            $idFranquiciaContrato = "";
            $rolUsuario = Auth::user()->rol_id; // Rol de usuario
            $idUsuario = Auth::user()->id; // ID usuario
            $franquiciaUsuario = $idFranquicia;

            //Consultas verificar contrato
            if ((Auth::user()->rol_id) == 15) {
                //Para rol de Confirmaciones verificar sucursales
                $existeContrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato'");
                $idFranquiciaContrato = $existeContrato[0]->id_franquicia;
                $tieneProductosContrato = DB::select("SELECT * FROM contratoproducto cp WHERE cp.id_contrato = '$idContrato' AND cp.id_franquicia = '$idFranquiciaContrato'");
            } else {
                $existeContrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato' AND c.id_franquicia = '$idFranquicia'");
                $tieneProductosContrato = DB::select("SELECT * FROM contratoproducto cp WHERE cp.id_contrato = '$idContrato' AND cp.id_franquicia = '$idFranquicia'");
            }


            if ($promocionContrato != null) {
                //Validar parametros sucursal a traspasar Sucursal seleccionada - Zona - Promocion
                request()->validate([
                    'sucursalSeleccionada' => 'required',
                    'zonaSeleccionada' => 'required',
                    'promocionSeleccionada' => 'required'
                ]);
            } else {
                //Si no tenia el contrato promocion
                //No requerir promocion en select
                request()->validate([
                    'sucursalSeleccionada' => 'required',
                    'zonaSeleccionada' => 'required'
                ]);
            }

            if ($existeContrato != null) {
                //Si existe contrato

                $garantias = DB::select("SELECT * FROM garantias g WHERE g.id_contrato = '$idContrato' ORDER BY g.created_at DESC LIMIT 1");
                $solicitudAutorizacion = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato = '$idContrato' AND a.tipo = 6 ORDER BY a.created_at DESC LIMIT 1");

                if (($existeContrato[0]->estatus_estadocontrato != 7 && $existeContrato[0]->estatus_estadocontrato != 10 && $existeContrato[0]->estatus_estadocontrato != 11
                        && ($garantias == null || $garantias[0]->estadogarantia == 3 || $garantias[0]->estadogarantia == 4))
                    || ($solicitudAutorizacion != null && $solicitudAutorizacion[0]->estatus == 1 && $garantias == null)) {
                    //Estado diferente a APROBADO, MANUFACTURA y EN PROCESO DE ENVIO

                    $banderaContrato = false; //Bandera para validacion de contrato

                    switch ($rolUsuario) {
                        case 7:
                            //Rol director
                            if ($existeContrato[0]->estatus_estadocontrato == '1' || $existeContrato[0]->estatus_estadocontrato == '2' || $existeContrato[0]->estatus_estadocontrato == '4' ||
                                $existeContrato[0]->estatus_estadocontrato == '5' || $existeContrato[0]->estatus_estadocontrato == '7' || $existeContrato[0]->estatus_estadocontrato == '9' ||
                                $existeContrato[0]->estatus_estadocontrato == '10' || $existeContrato[0]->estatus_estadocontrato == '11' || $existeContrato[0]->estatus_estadocontrato == '12') {
                                //El contrato tiene estatus 1, 2, 4, 5, 7, 9, 10, 11, 12

                                $existeGarantia = DB::select("SELECT * FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia IN (0,1,2) ORDER BY created_at ASC limit 1");
                                if ($existeGarantia == null) {
                                    //Si NO tiene garantia puedes hacer el traspaso
                                    $banderaContrato = true; //El contrato existe, tiene el estatus correcto y no tiene garantia
                                } else {
                                    //Si tiene garantia -> No se puede hacer el traspaso
                                    return redirect()->route('traspasarcontrato', [$franquiciaUsuario])->with('alerta', 'Contrato presenta garantia, es necesario cancelarla para el proceso.');
                                }

                            } else {
                                //El contrato no tiene estatus de atrasado, aprovado, manufactura, proceso de envio o enviado
                                return redirect()->route('traspasarcontrato', [$franquiciaUsuario])->with('alerta', 'No puedes generar un traspaso de este contrato debido a su estatus actual.');
                            }
                            break;
                        case 6:
                            //Rol Administracion
                        case 8:
                            //Rol de principal
                            if ($existeContrato[0]->estatus_estadocontrato == '2' || $existeContrato[0]->estatus_estadocontrato == '4' || $existeContrato[0]->estatus_estadocontrato == '7' ||
                                $existeContrato[0]->estatus_estadocontrato == '10' || $existeContrato[0]->estatus_estadocontrato == '11' || $existeContrato[0]->estatus_estadocontrato == '12') {
                                //Si el estatus del contrato pertenece a 4,7,10,11,12
                                $existeGarantia = DB::select("SELECT * FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia IN (0,1,2) ORDER BY created_at ASC limit 1");
                                if ($existeGarantia == null) {
                                    //Si NO tiene garantia puedes hacer el traspaso
                                    $banderaContrato = true; //El contrato existe, tiene el estatus correcto y no tiene garantia
                                } else {
                                    //Si tiene garantia -> No se puede hacer el traspaso
                                    return redirect()->route('traspasarcontrato', [$franquiciaUsuario])->with('alerta', 'Contrato presenta garantia, es necesario cancelarla para el proceso.');
                                }

                            } else {
                                //El contrato no tiene estatus de atrasado, aprovado, manufactura, proceso de envio o enviado
                                return redirect()->route('traspasarcontrato', [$franquiciaUsuario])->with('alerta', 'No puedes generar un traspaso de este contrato debido a su estatus actual.');
                            }
                            break;
                        case 15:
                            //Rol de Confirmaciones

                            $existeGarantia = DB::select("SELECT * FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia = '2' ORDER BY created_at ASC limit 1");

                            if ($existeGarantia == null) {
                                //Si NO tiene garantia puedes hacer el traspaso

                                $banderaFranquicia = false;
                                //optenemos sucursales asignadas
                                $franquicias = DB::select("SELECT f.id FROM franquicias f
                                                INNER JOIN sucursalesconfirmaciones sf ON f.id = sf.id_franquicia
                                                WHERE f.id != '00000' AND sf.id_usuario = '$idUsuario' ORDER BY ciudad ASC");

                                foreach ($franquicias as $franquicia) {
                                    if ($franquicia->id == $existeContrato[0]->id_franquicia) {
                                        //Si la sucursal del contrato pertenece a una asiganada a confirmaciones
                                        $banderaFranquicia = true; // Bandera colcar en verdadero y salir del ciclo
                                        break;
                                    }
                                }
                                if ($banderaFranquicia == true) {
                                    //El contrato pertenece a una sucursal asiganada a confirmaciones
                                    if ($existeContrato[0]->estatus_estadocontrato == '1' || $existeContrato[0]->estatus_estadocontrato == '9' || $existeContrato[0]->estatus_estadocontrato == '7') {
                                        //El contrato tiene estatus de TERMINADO, PROCESO DE APROBACION O APROBADO

                                        $banderaContrato = true; //Contrato validado correctamente y perteneciente a sucursal asiganada
                                        $idFranquicia = $idFranquiciaContrato;
                                    } else {
                                        return redirect()->route('traspasarcontrato', [$franquiciaUsuario])
                                            ->with('alerta', 'No puedes generar un traspaso de este contrato debido a su estatus actual.');
                                    }
                                } else {
                                    //No pertenece a una sucursal asignada a confirmaciones
                                    return redirect()->route('traspasarcontrato', [$franquiciaUsuario])
                                        ->with('alerta', 'Contrato perteneciente a otra sucursal');
                                }
                            } else {
                                //Si tiene garantia -> No se puede hacer el traspaso
                                return redirect()->route('traspasarcontrato', [$franquiciaUsuario])
                                    ->with('alerta', 'Contrato presenta garantia, es necesario cancelarla para el proceso.');
                            }
                            break;
                    }

                    if ($banderaContrato == true) {
                        //Si el contrato es validado correctamente

                        try {
                            //Actualizamos el contrato con la nueva sucursal

                            //Actualizar tabla contratos
                            if ($existeContrato[0]->id_promocion == null || $promocionTraspaso == "Sin promocion") {
                                //No tiene promocion
                                DB::table('contratos')->where("id", "=", $idContrato)->where("id_franquicia", "=", $idFranquicia)->update([
                                    'id_franquicia' => $franquiciaTraspaso, 'id_zona' => $zonaTraspaso, 'id_promocion' => null, 'updated_at' => Carbon::now()
                                ]);
                            } else {
                                //Si tiene promocion
                                DB::table('contratos')->where("id", "=", $idContrato)->where("id_franquicia", "=", $idFranquicia)->update([
                                    'id_franquicia' => $franquiciaTraspaso, 'id_zona' => $zonaTraspaso, 'id_promocion' => $promocionTraspaso, 'updated_at' => Carbon::now()
                                ]);

                                //Actualizar tabla promocioncontrato
                                DB::table('promocioncontrato')->where("id_contrato", "=", $idContrato)->where("id_franquicia", "=", $idFranquicia)->update([
                                    'id_franquicia' => $franquiciaTraspaso, 'updated_at' => Carbon::now()
                                ]);
                            }

                            $contratosGlobal = new contratosGlobal;
                            $contratosGlobal::validacionAumentarDisminuirTabularPolizaActualYPolizasAnterioresSemana($idFranquicia, $idContrato, $existeContrato[0]->id_zona, $zonaTraspaso, $franquiciaTraspaso);

                            //Actualizar tabla contratoproducto
                            if ($tieneProductosContrato != null) {
                                //Tiene contratoproductos el contrato
                                DB::table('contratoproducto')->where("id_contrato", "=", $idContrato)->where("id_franquicia", "=", $idFranquicia)->update([
                                    'id_franquicia' => $franquiciaTraspaso, 'updated_at' => Carbon::now()
                                ]);
                            }

                            //Registrar movimiento en historial
                            $globalesServicioWeb = new globalesServicioWeb;
                            $idHistorialContratoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');
                            $usuarioId = Auth::user()->id;
                            $franquicia = DB::select("SELECT f.ciudad FROM franquicias f where f.id = '$franquiciaTraspaso'");
                            $nombreFranquicia = $franquicia[0]->ciudad;
                            DB::table('historialcontrato')->insert([
                                'id' => $idHistorialContratoAlfanumerico, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                'cambios' => "Se traspaso el contrato a sucursal: '$nombreFranquicia'"]);

                            //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato
                            DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");

                            //Eliminar registros de la tabla abonoscontratostemporalessincronizacion que contengan ese idContrato
                            DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_contrato = '$idContrato'");

                            //Actualizar datos tabla contratoslistatemporales
                            $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                            if ($existeContrato[0]->estatus_estadocontrato == '4' || $existeContrato[0]->estatus_estadocontrato == '12') {
                                //ABONO ATRASADO O ENVIADO

                                $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($zonaTraspaso); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona detraspaso

                                if ($cobradoresAsignadosAZona != null) {
                                    //Existen cobradores
                                    foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                        //Recorrido cobradores
                                        $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $cobradorAsignadoAZona->id);
                                        //Actualizar contrato en tabla contratoslistatemporales
                                        $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                                        $abonos = DB::select("SELECT * FROM abonos WHERE id_contrato ='$idContrato' ORDER BY created_at DESC");
                                        //Insertar abonos en tabla abonoscontratostemporalessincronizacion
                                        foreach ($abonos as $abono) {
                                            //Recorrido abonos
                                            //Insertar en tabla abonoscontratostemporalessincronizacion
                                            DB::table("abonoscontratostemporalessincronizacion")->insert([
                                                "id_usuariocobrador" => $cobradorAsignadoAZona->id,
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
                                }
                            }

                            //Actualizar en tabla autorizaciones a estatus 3 (Solicitud generada)
                            $solicitudtraspasocontrato = DB::select("SELECT indice FROM autorizaciones a WHERE a.id_contrato = '$idContrato' AND a.estatus = 1 AND a.tipo = 6
                                                                                    ORDER BY a.created_at DESC LIMIT 1");
                            if ($solicitudtraspasocontrato != null) {
                                //Tiene solicitud aprobada para cambio de paquete
                                DB::table('autorizaciones')->where([['indice', '=', $solicitudtraspasocontrato[0]->indice], ['id_contrato', '=', $idContrato]])->update([
                                    'estatus' => '3', 'updated_at' => Carbon::now()
                                ]);
                            }

                            return redirect()->route('traspasarcontrato', ['idFranquicia' => $franquiciaUsuario])->with('bien', "El contrato a sido transferido correctamente");

                        } catch (\Exception $e) {
                            \Log::info("Error: " . $e->getMessage());
                            return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina   Error:' . $e->getMessage());
                        }
                    }
                    //Y si no es valido?
                    return redirect()->route('traspasarcontrato', [$franquiciaUsuario])->with('alerta', 'Contrato no valido.');

                }

                return back()->with('alerta', 'Solo se pueden realizar traspasos los días sábados.');

            } else {
                //No existe el contrato
                return redirect()->route('traspasarcontrato', [$franquiciaUsuario])->with('alerta', 'Contrato no existente, verifica el ID CONTRATO');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function reportecontratos($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 15)) {
            //Rol administrador, director, principal, confirmaciones

            switch (Auth::user()->rol_id) {
                case 7:
                    //Rol de director
                    $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f WHERE (f.id != '00000') ORDER BY ciudad ASC");
                    break;
                case 6:
                    //Rol de Administracion
                case 8:
                    //Rol de Principal
                    $franquicias = DB::select("SELECT f.id, f.ciudad FROM franquicias f WHERE f.id = '$idFranquicia'");
                    break;
                case 15:
                    //Rol de administracion
                    $idUsuario = Auth::user()->id;
                    //Obtener las sucursales asigandas
                    $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f
                                                INNER JOIN sucursalesconfirmaciones sf ON f.id = sf.id_franquicia
                                                WHERE f.id != '00000' AND sf.id_usuario = '$idUsuario' ORDER BY ciudad ASC");
                    break;
            }

            $fechaIni = Carbon::parse(Carbon::now())->format("Y-m-d");

            return view('administracion.contrato.reportecontratos', [
                'franquicias' => $franquicias,
                'idFranquicia' => $idFranquicia,
                'fechaIni' => $fechaIni
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function obtenerreportecontratosdirector(Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 15)) {
            //Rol administrador, director, principal, confirmaciones

            //Variables
            $totalVentasAsistentes = null;
            $totalVentasOptometristas = null;
            $ventasPorFranquiciaOptometrsitas = [];
            $ventasPorFranquiciaAsistentes = [];

            //Datos recibidos
            $franquiciaSeleccionada = $request->input("franquiciaSeleccionada");
            $cbPeriodoActual = $request->input("cbPeriodoActual");
            $cbPeriodoActual = ($cbPeriodoActual == "true")? true : false;
            $rangoFechas = Carbon::parse($request->input("fechaLunes"))->format("Y-m-d") . " " . Carbon::parse($request->input("fechaSabado"))->format("Y-m-d");

            if ($franquiciaSeleccionada != null) {
                //se selecciono una franquicia
                //Obtenemos las franquicias - en este caso solo sera 1
                $franquicias = DB::select("SELECT f.id, f.ciudad FROM franquicias f WHERE f.id = '$franquiciaSeleccionada';");
            } else {
                //Selecciono - Todas las sucursales
                $franquicias = DB::select("SELECT f.id, f.ciudad FROM franquicias f WHERE f.id != '00000' ORDER BY f.ciudad ASC;");
            }

            //Obtener datos para sucursal seleccionada o todas las sucursales
            $ventasPorFranquicias = $franquicias;
            $indice = 0;

            foreach ($ventasPorFranquicias as $ventasPorFranquicia) {

                $id_Franquicia = $ventasPorFranquicia->id;

                //Optener datos de ventas en la semana para solo la sucursal asiganda
                $asistentes = DB::select("SELECT u.name, u.id FROM users u inner join usuariosfranquicia uf ON uf.id_usuario = u.id
                                                WHERE u.rol_id = '13' AND uf.id_franquicia = '$id_Franquicia'");
                $optometristas = DB::select("SELECT u.name, u.id  FROM users u inner join usuariosfranquicia uf ON uf.id_usuario = u.id
                                                   WHERE u.rol_id = '12' AND uf.id_franquicia = '$id_Franquicia'");

                //Obtener ventas de la semana Asistente y Optometrista
                $ventasOptometristas = contratosGlobal::obtenerVentasSemana($optometristas, 0, $cbPeriodoActual, $rangoFechas);
                $ventasAsistentes = contratosGlobal::obtenerVentasSemana($asistentes, 1, $cbPeriodoActual, $rangoFechas);

                //Suma de ventas por dia
                $totalVentasAsistentes[$indice] = contratosGlobal::totalVentasPorDia($asistentes);
                $totalVentasOptometristas[$indice] = contratosGlobal::totalVentasPorDia($optometristas);

                //Se ingresan las ventas a un uevo arreglo pero en cada indice va una franquicia
                $ventasPorFranquiciaAsistentes[$indice] = $ventasAsistentes;
                $ventasPorFranquiciaOptometrsitas[$indice] = $ventasOptometristas;
                $indice = $indice + 1; //Incrementar indice para ingresar a otro espacio del arreglo
            }

            $view = view('administracion.contrato.listas.listareportecontratos', [
                'totalVentasAsistentes' => $totalVentasAsistentes,
                'totalVentasOptometristas' => $totalVentasOptometristas,
                'franquicias' => $franquicias,
                'franquiciaSeleccionada' => $franquiciaSeleccionada,
                'ventasPorFranquiciaOptometrsitas' => $ventasPorFranquiciaOptometrsitas,
                'ventasPorFranquiciaAsistentes' => $ventasPorFranquiciaAsistentes
            ])->render();

            return \Response::json(array("valid" => "true", "view" => $view));


        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function listasolicitudautorizacion($idFranquicia){
        if (Auth::check() && ((Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8  || (Auth::user()->rol_id) == 15 || (Auth::user()->rol_id) == 16)){
            //ROL DE DIRECTOR - GENERAL - CONFIRMACIONES - LABORATORIO

            $rol_id = Auth::user()->rol_id;
            $idUsuario = Auth::user()->id;

            switch ($rol_id){
                case 8:
                    //Rol de Principal
                    //Solo optener solicitudes de su franquiciaa
                    $solicitudesAutorizacion = DB::select("SELECT a.indice,(SELECT f.ciudad FROM franquicias f WHERE f.id = a.id_franquicia) AS sucursal, a.tipo, a.id_contrato,
                                                                a.fechacreacioncontrato AS fecha_contrato, a.created_at AS fecha_solicitud, IFNULL(a.estadocontrato,1000) AS estado_contrato,
                                                                a.mensaje as mensaje,(SELECT u.name FROM users u WHERE u.id = a.id_usuarioC) AS usuario_solicitud,
                                                                (SELECT FORMAT(c.totalreal,1) FROM contratos c WHERE c.id = a.id_contrato) as total,
                                                                (SELECT FORMAT(c.total,1) FROM contratos c WHERE c.id = a.id_contrato) as saldo
                                                                FROM autorizaciones a
                                                                WHERE a.estatus = 0 AND a.tipo IN (0,1,2,4,11,12,14,15,16) AND a.id_franquicia = '$idFranquicia'
                                                                ORDER BY sucursal, a.tipo ASC, fecha_solicitud DESC");
                    break;

                case 7:
                    //Rol de director

                    $solicitudesArmazones = array();
                    if($idUsuario == 1 || $idUsuario == 61 || $idUsuario == 761 ){
                        //A nosotros los desarrolladores se nos mostraran todos los tipos de autorizaciones
                        $tipoAutorizacion = '0,1,2,4,6,7,11,12,13,15,16';

                        $solicitudesArmazones = DB::select("SELECT a.indice,(SELECT f.ciudad FROM franquicias f WHERE f.id = a.id_franquicia) AS sucursal, a.tipo, a.id_contrato,
                                                                a.fechacreacioncontrato AS fecha_contrato, a.created_at AS fecha_solicitud, a.estadocontrato AS estado_contrato,
                                                                a.mensaje as mensaje,(SELECT u.name FROM users u WHERE u.id = a.id_usuarioC) AS usuario_solicitud,
                                                                (select piezas from producto p where p.id = aal.id_armazon) as piezas,
                                                                (SELECT FORMAT(c.totalreal,1) FROM contratos c WHERE c.id = a.id_contrato) as total,
                                                                (SELECT FORMAT(c.total,1) FROM contratos c WHERE c.id = a.id_contrato) as saldo
                                                                FROM autorizaciones a
                                                                inner join autorizacionarmazonlaboratorio aal on aal.id_autorizacion = a.indice
                                                                WHERE a.estatus = 0 AND a.tipo IN (8,9,10)
                                                                AND (a.id_franquicia != '00000' OR a.id_franquicia IS NULL)
                                                                ORDER BY sucursal, a.tipo ASC, fecha_solicitud DESC");
                    }else{
                        //A los demas directores se les mostraran todos menos los que tienen que ver con el armazon
                        $tipoAutorizacion = '0,1,2,4,6,11,12,13,15,16';
                    }
                    //Obtener solicitudes de todas las franquicias

                    $solicitudesCobranza = DB::select("SELECT a.indice,(SELECT f.ciudad FROM franquicias f WHERE f.id = a.id_franquicia) AS sucursal, a.tipo, 'NO APLICA' AS id_contrato,
                                                                a.fechacreacioncontrato AS fecha_contrato, a.created_at AS fecha_solicitud, IFNULL(a.estadocontrato,1000) AS estado_contrato,
                                                                a.mensaje as mensaje,(SELECT u.name FROM users u WHERE u.id = a.id_usuarioC) AS usuario_solicitud
                                                                FROM autorizaciones a
                                                                WHERE a.estatus = 0 AND a.tipo = 14 AND a.id_franquicia != '00000'
                                                                ORDER BY sucursal, a.tipo ASC, fecha_solicitud DESC");

                   $solicitudesAutorizacion = DB::select("SELECT a.indice,(SELECT f.ciudad FROM franquicias f WHERE f.id = a.id_franquicia) AS sucursal, a.tipo, a.id_contrato,
                                                                a.fechacreacioncontrato AS fecha_contrato, a.created_at AS fecha_solicitud, a.estadocontrato AS estado_contrato,
                                                                a.mensaje as mensaje,(SELECT u.name FROM users u WHERE u.id = a.id_usuarioC) AS usuario_solicitud,
                                                                (SELECT sab.fotofrente FROM solicitudarmazonbaja sab WHERE sab.id_autorizacion = a.indice) as fotofrente,
                                                                (SELECT sab.fotoatras FROM solicitudarmazonbaja sab WHERE sab.id_autorizacion = a.indice) as fotoatras,
                                                                (SELECT sab.fotolado1 FROM solicitudarmazonbaja sab WHERE sab.id_autorizacion = a.indice) as fotolado1,
                                                                (SELECT sab.fotolado2 FROM solicitudarmazonbaja sab WHERE sab.id_autorizacion = a.indice) as fotolado2,
                                                                (SELECT FORMAT(c.totalreal,1) FROM contratos c WHERE c.id = a.id_contrato) as total,
                                                                (SELECT FORMAT(c.total,1) FROM contratos c WHERE c.id = a.id_contrato) as saldo
                                                                FROM autorizaciones a
                                                                WHERE a.estatus = 0 AND a.tipo IN ($tipoAutorizacion)
                                                                AND (a.id_franquicia != '00000' OR a.id_franquicia IS NULL)
                                                                ORDER BY sucursal, a.tipo ASC, fecha_solicitud DESC");

                    $solicitudesAutorizacion = array_merge($solicitudesAutorizacion,$solicitudesCobranza,$solicitudesArmazones);
                    break;

                case 15:
                    //Rol de Confirmaciones
                    //Obtener solicitudes de franquicias asignadas
                    $solicitudesAutorizacion = DB::select("SELECT a.indice,(SELECT f.ciudad FROM franquicias f WHERE f.id = a.id_franquicia) AS sucursal, a.tipo, a.id_contrato,
                                                                a.fechacreacioncontrato AS fecha_contrato, a.created_at AS fecha_solicitud, a.estadocontrato AS estado_contrato,
                                                                a.mensaje as mensaje,(SELECT u.name FROM users u WHERE u.id = a.id_usuarioC) AS usuario_solicitud,
                                                                (SELECT FORMAT(c.totalreal,1) FROM contratos c WHERE c.id = a.id_contrato) as total,
                                                                (SELECT FORMAT(c.total,1) FROM contratos c WHERE c.id = a.id_contrato) as saldo
                                                                FROM autorizaciones a
                                                                INNER JOIN sucursalesconfirmaciones sc ON a.id_franquicia = sc.id_franquicia
                                                                WHERE sc.id_usuario = '$idUsuario' and a.estatus = 0 AND a.tipo = 0
                                                                ORDER BY  sucursal, a.tipo ASC, fecha_solicitud DESC");
                    break;
                case 16:
                    //Rol de laboratorio
                    //Obtener solicitudes de todas las franquicias
                    $solicitudesAutorizacion = DB::select("SELECT a.indice,(SELECT f.ciudad FROM franquicias f WHERE f.id = a.id_franquicia) AS sucursal, a.tipo, a.id_contrato,
                                                                    a.fechacreacioncontrato AS fecha_contrato, a.created_at AS fecha_solicitud, a.estadocontrato AS estado_contrato,
                                                                    a.mensaje as mensaje,(SELECT u.name FROM users u WHERE u.id = a.id_usuarioC) AS usuario_solicitud,
                                                                    (select nombre from producto p where p.id = aal.id_armazon) as armazon,
                                                                    (select color from producto p where p.id = aal.id_armazon) as color,
                                                                    (select piezas from producto p where p.id = aal.id_armazon) as piezas,
                                                                    (SELECT FORMAT(c.totalreal,1) FROM contratos c WHERE c.id = a.id_contrato) as total,
                                                                    (SELECT FORMAT(c.total,1) FROM contratos c WHERE c.id = a.id_contrato) as saldo,
                                                                    UPPER(aal.observaciones) AS observaciones
                                                                    FROM autorizaciones a
                                                                    inner join autorizacionarmazonlaboratorio aal on aal.id_autorizacion = a.indice
                                                                    WHERE a.estatus = 0 AND a.tipo IN (6,8,9,10,13) AND (a.id_franquicia != '00000' OR a.id_franquicia IS NULL)
                                                                    ORDER BY sucursal, a.tipo ASC, fecha_solicitud DESC");

                    $solicitudesArmazonBaja = DB::select("SELECT a.indice,(SELECT f.ciudad FROM franquicias f WHERE f.id = a.id_franquicia) AS sucursal, a.tipo, a.id_contrato,
                                                                a.fechacreacioncontrato AS fecha_contrato, a.created_at AS fecha_solicitud, a.estadocontrato AS estado_contrato,
                                                                a.mensaje as mensaje,(SELECT u.name FROM users u WHERE u.id = a.id_usuarioC) AS usuario_solicitud,
                                                                (SELECT sab.fotofrente FROM solicitudarmazonbaja sab WHERE sab.id_autorizacion = a.indice) as fotofrente,
                                                                (SELECT sab.fotoatras FROM solicitudarmazonbaja sab WHERE sab.id_autorizacion = a.indice) as fotoatras,
                                                                (SELECT sab.fotolado1 FROM solicitudarmazonbaja sab WHERE sab.id_autorizacion = a.indice) as fotolado1,
                                                                (SELECT sab.fotolado2 FROM solicitudarmazonbaja sab WHERE sab.id_autorizacion = a.indice) as fotolado2
                                                                FROM autorizaciones a
                                                                WHERE a.estatus = 0 AND a.tipo IN (13)
                                                                AND a.id_franquicia IS NULL
                                                                ORDER BY sucursal, a.tipo ASC, fecha_solicitud DESC");

                    $solicitudesAutorizacion = array_merge($solicitudesAutorizacion,$solicitudesArmazonBaja);
                    break;
            }

            return view('administracion.contrato.tablasolicitudesautorizacion', [
                'solicitudesAutorizacion' => $solicitudesAutorizacion,
                'idFranquicia' => $idFranquicia,
            ]);
        }else{
            if(Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function autorizarcontrato($idContrato, $indice){
        if (Auth::check() && ((Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8  || (Auth::user()->rol_id) == 15 || (Auth::user()->rol_id) == 16)){
            //ROL DE DIRECTOR - GENERAL - CONFIRMACIONES - LABORATORIO

            $rol_id = Auth::user()->rol_id;
            $idUsuario = Auth::user()->id;

            $globalesServicioWeb = new globalesServicioWeb;
            $contratosGlobal = new contratosGlobal;

            //Comprobar que contrato pertenezca a sucursal asignada
            switch ($rol_id){
                case 8:
                    //Rol de PRINCIPAL
                    $existeContrato = DB::select("SELECT * FROM contratos c
                                                inner join usuariosfranquicia uf ON uf.id_franquicia = c.id_franquicia
                                                INNER JOIN users u ON uf.id_usuario = u.id
                                                WHERE c.id = '$idContrato' AND u.id = '$idUsuario'");
                    break;

                case 7:
                case 16:
                    //Rol de DIRECTOR
                    $existeContrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato' and c.id_franquicia != '00000'");
                    break;
                case 15:
                    //Rol de CONFIRMACIONES
                    $existeContrato = DB::select("SELECT * FROM contratos c
                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
                                                INNER JOIN sucursalesconfirmaciones sc ON c.id_franquicia = sc.id_franquicia
                                                INNER JOIN users u ON u.id = sc.id_usuario
                                                WHERE c.id = '$idContrato' AND  sc.id_usuario = '$idUsuario'");
                    break;
            }

            if($existeContrato != null){
                //Contrato existe y pertenece a sucursal
                $existeSolicitud = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato = '$idContrato' AND a.estatus = '0' AND a.indice = '$indice'");

                if($existeSolicitud != null){
                    //Si existe solicitud
                    switch ($existeSolicitud[0]->tipo){
                        case 0:
                            //Tipo Solicitud de garantia
                            $cambios = "Solicitud de garantia autorizada.";
                            $mensaje = " Solicitud de garantia autorizada correctamente.";
                            break;

                        case 2:
                            //Tipo aumentar/descontar
                            try {

                                $estadocontrato = $existeContrato[0]->estatus_estadocontrato;
                                $total = $existeContrato[0]->total;
                                $totalhistorial = $existeContrato[0]->totalhistorial;
                                $totalpromocion = $existeContrato[0]->totalpromocion;
                                $fechacobroini = $existeContrato[0]->fechacobroini;
                                $fechacobrofin = $existeContrato[0]->fechacobrofin;
                                $totalreal = $existeContrato[0]->totalreal;
                                $diaseleccionado = $existeContrato[0]->diaseleccionado;

                                $aumentardescontar = $existeSolicitud[0]->valor;
                                $mensajeautorizacion = "";

                                if ($aumentardescontar < 0) {
                                    //Estan descontando

                                    $mensajeautorizacion = "descontar";
                                    $estadoactualizar = $estadocontrato;
                                    if (($estadocontrato == 2 || $estadocontrato == 4) && ($total + $aumentardescontar) == 0) {
                                        //ENTREGADO O ABONOATRASADO && total == 0
                                        $estadoactualizar = 5; //LIQUIDADO
                                    }

                                    $totalhistorialactualizar = $totalhistorial + $aumentardescontar;
                                    $totalpromocionactualizar = $totalpromocion + $aumentardescontar;
                                    $totalrealactualizar = $totalreal + $aumentardescontar;

                                    if ($this->obtenerEstadoPromocion($idContrato, $existeContrato[0]->id_franquicia)) {
                                        //Tiene promocion y esta activa
                                        $promocionterminada = DB::select("SELECT promocionterminada FROM contratos where id = '$idContrato'");
                                        if ($promocionterminada != null) {
                                            if ($promocionterminada[0]->promocionterminada == 1) {
                                                //Promocion ha sido terminada
                                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $existeContrato[0]->id_franquicia]])->update([
                                                    'totalhistorial' => $totalhistorialactualizar,
                                                    'totalpromocion' => $totalpromocionactualizar,
                                                    'totalreal' => $totalrealactualizar,
                                                    'estatus_estadocontrato' => $estadoactualizar
                                                ]);
                                            } else {
                                                //Promocion no ha sido terminada
                                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $existeContrato[0]->id_franquicia]])->update([
                                                    'totalhistorial' => $totalhistorialactualizar,
                                                    'totalreal' => $totalrealactualizar,
                                                    'estatus_estadocontrato' => $estadoactualizar
                                                ]);
                                            }

                                            //Insertar en tabla registroestadocontrato
                                            DB::table('registroestadocontrato')->insert([
                                                'id_contrato' => $idContrato,
                                                'estatuscontrato' => $estadoactualizar,
                                                'created_at' => Carbon::now()
                                            ]);
                                        }
                                    } else {
                                        //No tiene promocion o existe la promocion pero esta desactivada
                                        if ($totalpromocion > 0) {
                                            //Si se tiene una promocion pero esta desactivada
                                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $existeContrato[0]->id_franquicia]])->update([
                                                'totalhistorial' => $totalhistorialactualizar,
                                                'totalpromocion' => $totalpromocionactualizar,
                                                'totalreal' => $totalrealactualizar,
                                                'estatus_estadocontrato' => $estadoactualizar
                                            ]);
                                        } else {
                                            //No se tiene ninguna promocion
                                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $existeContrato[0]->id_franquicia]])->update([
                                                'totalhistorial' => $totalhistorialactualizar,
                                                'totalreal' => $totalrealactualizar,
                                                'estatus_estadocontrato' => $estadoactualizar
                                            ]);
                                        }

                                        //Insertar en tabla registroestadocontrato
                                        DB::table('registroestadocontrato')->insert([
                                            'id_contrato' => $idContrato,
                                            'estatuscontrato' => $estadoactualizar,
                                            'created_at' => Carbon::now()
                                        ]);

                                    }

                                } else {
                                    //Estan aumentando
                                    $mensajeautorizacion = "aumentar";
                                    $totalhistorialactualizar = $totalhistorial + $aumentardescontar;
                                    $totalpromocionactualizar = $totalpromocion + $aumentardescontar;
                                    $totalrealactualizar = $totalreal + $aumentardescontar;

                                    $estadoactualizar = $estadocontrato;
                                    $fechacobroiniactualizar = $fechacobroini;
                                    $fechacobrofinactualizar = $fechacobrofin;
                                    $diaseleccionadoactualizar = $diaseleccionado;

                                    if ($estadocontrato == 5) {
                                        //LIQUIDADO
                                        $estadoactualizar = 2;

                                        $calculofechaspago = new calculofechaspago;

                                        //Calculo fechaCobroIniActual y fechaCobroFinActual
                                        $arrayRespuesta = $calculofechaspago::obtenerFechasPeriodoActualOPeriodoSiguiente(Carbon::now(), $existeContrato[0]->pago, true);
                                        $fechacobroiniactualizar = $arrayRespuesta[0];
                                        $fechacobrofinactualizar = $arrayRespuesta[1];

                                        //OBTENER DIASELECCIONADO
                                        if(strlen($existeContrato[0]->diapago) > 0) {
                                            //Se tiene un dia de pago
                                            $diaseleccionadoactualizar = $calculofechaspago::obtenerDiaSeleccionado($existeContrato[0]->diapago, $fechacobroiniactualizar, $fechacobrofinactualizar);
                                        }

                                    }

                                    if ($this->obtenerEstadoPromocion($idContrato, $existeContrato[0]->id_franquicia)) {
                                        //Tiene promocion y esta activa
                                        $promocionterminada = DB::select("SELECT promocionterminada FROM contratos where id = '$idContrato'");
                                        if ($promocionterminada != null) {
                                            if ($promocionterminada[0]->promocionterminada == 1) {
                                                //Promocion ha sido terminada
                                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $existeContrato[0]->id_franquicia]])->update([
                                                    'totalhistorial' => $totalhistorialactualizar,
                                                    'totalpromocion' => $totalpromocionactualizar,
                                                    'totalreal' => $totalrealactualizar,
                                                    'estatus_estadocontrato' => $estadoactualizar,
                                                    'fechacobroini' => $fechacobroiniactualizar,
                                                    'fechacobrofin' => $fechacobrofinactualizar,
                                                    'diaseleccionado' => $diaseleccionadoactualizar
                                                ]);
                                            } else {
                                                //Promocion no ha sido terminada
                                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $existeContrato[0]->id_franquicia]])->update([
                                                    'totalhistorial' => $totalhistorialactualizar,
                                                    'totalreal' => $totalrealactualizar,
                                                    'estatus_estadocontrato' => $estadoactualizar,
                                                    'fechacobroini' => $fechacobroiniactualizar,
                                                    'fechacobrofin' => $fechacobrofinactualizar,
                                                    'diaseleccionado' => $diaseleccionadoactualizar
                                                ]);
                                            }

                                            //Insertar en tabla registroestadocontrato
                                            DB::table('registroestadocontrato')->insert([
                                                'id_contrato' => $idContrato,
                                                'estatuscontrato' => $estadoactualizar,
                                                'created_at' => Carbon::now()
                                            ]);
                                        }
                                    } else {
                                        //No tiene promocion o existe la promocion pero esta desactivada
                                        if ($totalpromocion > 0) {
                                            //Si se tiene una promocion pero esta desactivada
                                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $existeContrato[0]->id_franquicia]])->update([
                                                'totalhistorial' => $totalhistorialactualizar,
                                                'totalpromocion' => $totalpromocionactualizar,
                                                'totalreal' => $totalrealactualizar,
                                                'estatus_estadocontrato' => $estadoactualizar,
                                                'fechacobroini' => $fechacobroiniactualizar,
                                                'fechacobrofin' => $fechacobrofinactualizar,
                                                'diaseleccionado' => $diaseleccionadoactualizar
                                            ]);
                                        } else {
                                            //No se tiene ninguna promocion
                                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $existeContrato[0]->id_franquicia]])->update([
                                                'totalhistorial' => $totalhistorialactualizar,
                                                'totalreal' => $totalrealactualizar,
                                                'estatus_estadocontrato' => $estadoactualizar,
                                                'fechacobroini' => $fechacobroiniactualizar,
                                                'fechacobrofin' => $fechacobrofinactualizar,
                                                'diaseleccionado' => $diaseleccionadoactualizar
                                            ]);
                                        }

                                        //Insertar en tabla registroestadocontrato
                                        DB::table('registroestadocontrato')->insert([
                                            'id_contrato' => $idContrato,
                                            'estatuscontrato' => $estadoactualizar,
                                            'created_at' => Carbon::now()
                                        ]);

                                    }
                                }

                                if ($estadocontrato == 5 && $mensajeautorizacion == 'aumentar') {
                                    //LIQUIDADO y aumento se cambio a ENTREGADO el contrato

                                    $abonos = DB::select("SELECT * FROM abonos WHERE id_contrato ='$idContrato' ORDER BY created_at DESC");

                                    //Insertar o actualizar datos contratostemporalessincronizacion
                                    $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($existeContrato[0]->id_zona); //Obtener idsUsuarios con rol cobranza asignados a zona
                                    if ($cobradoresAsignadosAZona != null) {
                                        //Existen cobradores
                                        foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                            //Recorrido cobradores
                                            $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $cobradorAsignadoAZona->id);
                                        }
                                    }

                                    //Actualizar si existe o insertar si no existe abonos en tabla abonoscontratostemporalessincronizacion
                                    foreach ($abonos as $abono) {
                                        //Recorrido abonos

                                        foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                            //Recorrido cobradores
                                            $existeAbono = DB::select("SELECT id FROM abonoscontratostemporalessincronizacion WHERE id_contrato = '$idContrato'
                                                         AND id = '" . $abono->id . "'
                                                         AND id_usuariocobrador = '" . $cobradorAsignadoAZona->id . "'");

                                            if ($existeAbono != null) {
                                                //Existe abono en tabla abonoscontratostemporalessincronizacion (Actualizar)
                                                DB::table("abonoscontratostemporalessincronizacion")->where("id", "=", $abono->id)
                                                    ->where("id_contrato", "=", $idContrato)->update([
                                                        "folio" => $abono->folio,
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
                                            } else {
                                                //No existe abono en tabla abonoscontratostemporalessincronizacion (Insertar)
                                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
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
                                    }

                                }

                                $cambios = "Solicitud para $mensajeautorizacion '$aumentardescontar' al contrato autorizada, total anterior: " . $totalreal . ", saldo anterior: " . $total;
                                $mensaje = " Solicitud para $mensajeautorizacion al contrato autorizada correctamente.";

                            } catch (\Exception $e) {
                                \Log::info("Error: " . $e->getMessage());
                                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
                            }
                            break;

                        case 4:
                            //Tipo cambiar paquete

                            try {

                                $paquetehistorialeditar = $existeSolicitud[0]->valor;

                                $historiales = DB::select("SELECT * FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = '0' ORDER BY created_at DESC");

                                if($historiales != null) {

                                    $contador = 0;
                                    $idpaquetehistorialentrante = null;

                                    foreach ($historiales as $historial) {

                                        $idhistorialentrante = $historial->id;
                                        $idpaquetehistorialentrante = $historial->id_paquete;
                                        $esfericoderhistorialentrante = $historial->esfericoder;
                                        $cilindroderhistorialentrante = $historial->cilindroder;
                                        $ejederhistorialentrante = $historial->ejeder;
                                        $addderhistorialentrante = $historial->addder;
                                        $altderhistorialentrante = $historial->altder;
                                        $esfericoizqhistorialentrante = $historial->esfericoizq;
                                        $cilindroizqhistorialentrante = $historial->cilindroizq;
                                        $ejeizqhistorialentrante = $historial->ejeizq;
                                        $addizqhistorialentrante = $historial->addizq;
                                        $altizqhistorialentrante = $historial->altizq;

                                        $esfericoderactualizar = null;
                                        $cilindroderactualizar = null;
                                        $ejederactualizar = null;
                                        $addderactualizar = null;
                                        $altderactualizar = null;
                                        $esfericoizqactualizar = null;
                                        $cilindroizqactualizar = null;
                                        $ejeizqactualizar = null;
                                        $addizqactualizar = null;
                                        $altizqactualizar = null;
                                        $tipohistorialactualizar = 0;
                                        $crearsegundohistorial = false;
                                        $crearsegundohistorialsinconversion = false;

                                        if($contador == 0 && $idpaquetehistorialentrante == 6) {
                                            $tipohistorialactualizar = 2;
                                            DB::update("UPDATE garantias SET estadogarantia = '4' WHERE id_contrato = '$idContrato'
                                                                    AND id_historial = '$idhistorialentrante' AND estadogarantia IN (0,1)");
                                        }

                                        if($paquetehistorialeditar == 6) {
                                            //Se va a editar a DORADO2
                                            $crearsegundohistorial = true;
                                            if($idpaquetehistorialentrante == 1) {
                                                //Paquete entrante es LECTURA
                                                $crearsegundohistorialsinconversion = true;
                                            }
                                        }

                                        if($paquetehistorialeditar != 2) {
                                            //LECTURA, ECO JR, JR, DORADO 2, DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)

                                            if(strlen($esfericoderhistorialentrante) > 0) {
                                                //LECTURA, ECO JR, JR, DORADO 2, DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                if($paquetehistorialeditar == 1 || $paquetehistorialeditar == 3 || $paquetehistorialeditar == 4 || $paquetehistorialeditar == 6) {
                                                    //LECTURA, ECO JR, JR, DORADO 2, DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                    $esfericoderactualizar = $esfericoderhistorialentrante;
                                                }elseif ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                    // (PAQUETE ACTUALIZAR)
                                                    $esfericoderactualizar = $esfericoderhistorialentrante;
                                                }
                                            }else {
                                                //PROTECCION (PAQUETE ACTUAL)
                                                $esfericoderactualizar = 0;
                                            }

                                            if(strlen($cilindroderhistorialentrante) > 0) {
                                                //LECTURA, ECO JR, JR, DORADO 2, DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                if($paquetehistorialeditar == 1 || $paquetehistorialeditar == 3 || $paquetehistorialeditar == 4 || $paquetehistorialeditar == 6) {
                                                    //LECTURA, ECO JR, JR, DORADO 2 (PAQUETE ACTUALIZAR)
                                                    $cilindroderactualizar = $cilindroderhistorialentrante;
                                                }elseif ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                    $cilindroderactualizar = $cilindroderhistorialentrante;
                                                }
                                            }else {
                                                //PROTECCION (PAQUETE ACTUAL)
                                                $cilindroderactualizar = 0;
                                            }

                                            if(strlen($ejederhistorialentrante) > 0) {
                                                //LECTURA, ECO JR, JR, DORADO 2, DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                if($paquetehistorialeditar == 1 || $paquetehistorialeditar == 3 || $paquetehistorialeditar == 4 || $paquetehistorialeditar == 6) {
                                                    //LECTURA, ECO JR, JR, DORADO 2 (PAQUETE ACTUALIZAR)
                                                    $ejederactualizar = $ejederhistorialentrante;
                                                }elseif ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                    $ejederactualizar = $ejederhistorialentrante;
                                                }
                                            }else {
                                                //PROTECCION (PAQUETE ACTUAL)
                                                $ejederactualizar = 0;
                                            }

                                            if(strlen($addderhistorialentrante) > 0) {
                                                //DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                if ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                    $addderactualizar = $addderhistorialentrante;
                                                }
                                            }else {
                                                //LECTURA, ECO JR, JR, DORADO 2 o PROTECCION (PAQUETE ACTUAL)
                                                if ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                    $addderactualizar = 0;
                                                }
                                            }

                                            if(strlen($altderhistorialentrante) > 0) {
                                                //DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                if ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                    $altderactualizar = $altderhistorialentrante;
                                                }
                                            }else {
                                                //LECTURA, ECO JR, JR, DORADO 2 o PROTECCION (PAQUETE ACTUAL)
                                                if ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                    $altderactualizar = 0;
                                                }
                                            }

                                            if(strlen($esfericoizqhistorialentrante) > 0) {
                                                //LECTURA, ECO JR, JR, DORADO 2, DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                if($paquetehistorialeditar == 1 || $paquetehistorialeditar == 3 || $paquetehistorialeditar == 4 || $paquetehistorialeditar == 6) {
                                                    //LECTURA, ECO JR, JR, DORADO 2 (PAQUETE ACTUALIZAR)
                                                    $esfericoizqactualizar = $esfericoizqhistorialentrante;
                                                }elseif ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                    $esfericoizqactualizar = $esfericoizqhistorialentrante;
                                                }
                                            }else {
                                                //PROTECCION (PAQUETE ACTUAL)
                                                $esfericoizqactualizar = 0;
                                            }

                                            if(strlen($cilindroizqhistorialentrante) > 0) {
                                                //LECTURA, ECO JR, JR, DORADO 2, DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                if($paquetehistorialeditar == 1 || $paquetehistorialeditar == 3 || $paquetehistorialeditar == 4 || $paquetehistorialeditar == 6) {
                                                    //LECTURA, ECO JR, JR, DORADO 2 (PAQUETE ACTUALIZAR)
                                                    $cilindroizqactualizar = $cilindroizqhistorialentrante;
                                                }elseif ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                    $cilindroizqactualizar = $cilindroizqhistorialentrante;
                                                }
                                            }else {
                                                //PROTECCION (PAQUETE ACTUAL)
                                                $cilindroizqactualizar = 0;
                                            }

                                            if(strlen($ejeizqhistorialentrante) > 0) {
                                                //LECTURA, ECO JR, JR, DORADO 2, DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                if($paquetehistorialeditar == 1 || $paquetehistorialeditar == 3 || $paquetehistorialeditar == 4 || $paquetehistorialeditar == 6) {
                                                    //LECTURA, ECO JR, JR, DORADO 2 (PAQUETE ACTUALIZAR)
                                                    $ejeizqactualizar = $ejeizqhistorialentrante;
                                                }elseif ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                    $ejeizqactualizar = $ejeizqhistorialentrante;
                                                }
                                            }else {
                                                //PROTECCION (PAQUETE ACTUAL)
                                                $ejeizqactualizar = 0;
                                            }

                                            if(strlen($addizqhistorialentrante) > 0) {
                                                //DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                if ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                    $addizqactualizar = $addizqhistorialentrante;
                                                }
                                            }else {
                                                //LECTURA, ECO JR, JR, DORADO 2 o PROTECCION (PAQUETE ACTUAL)
                                                if ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                    $addizqactualizar = 0;
                                                }
                                            }

                                            if(strlen($altizqhistorialentrante) > 0) {
                                                //DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                if ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                    $altizqactualizar = $altizqhistorialentrante;
                                                }
                                            }else {
                                                //LECTURA, ECO JR, JR, DORADO 2 o PROTECCION (PAQUETE ACTUAL)
                                                if ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                    $altizqactualizar = 0;
                                                }
                                            }

                                        }

                                        if($crearsegundohistorial) {

                                            $idhistorialnuevo = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');

                                            DB::table('historialclinico')->insert([
                                                'id' => $idhistorialnuevo,
                                                'id_contrato' => $idContrato,
                                                'edad' => $historial->edad,
                                                'fechaentrega' => $historial->fechaentrega,
                                                'diagnostico' => $historial->diagnostico,
                                                'ocupacion' => $historial->ocupacion,
                                                'diabetes' => $historial->diabetes,
                                                'hipertension' => $historial->hipertension,
                                                'dolor' => $historial->dolor,
                                                'ardor' => $historial->ardor,
                                                'golpeojos' => $historial->golpeojos,
                                                'otroM' => $historial->otroM,
                                                'molestiaotro' => $historial->molestiaotro,
                                                'ultimoexamen' => $historial->ultimoexamen,
                                                'esfericoder' => $esfericoderactualizar,
                                                'cilindroder' => $cilindroderactualizar,
                                                'ejeder' => $ejederactualizar,
                                                'addder' => $addderactualizar,
                                                'altder' => $altderactualizar,
                                                'esfericoizq' => $esfericoizqactualizar,
                                                'cilindroizq' => $cilindroizqactualizar,
                                                'ejeizq' => $ejeizqactualizar,
                                                'addizq' => $addizqactualizar,
                                                'altizq' => $altizqactualizar,
                                                'id_producto' => '00000',
                                                'id_paquete' => $paquetehistorialeditar,
                                                'material' => $historial->material,
                                                'materialotro' => $historial->materialotro,
                                                'costomaterial' => $historial->costomaterial,
                                                'bifocal' => $historial->bifocal,
                                                'fotocromatico' => $historial->fotocromatico,
                                                'ar' => $historial->ar,
                                                'tinte' => $historial->tinte,
                                                'blueray' => $historial->blueray,
                                                'otroT' => $historial->otroT,
                                                'tratamientootro' => $historial->tratamientootro,
                                                'costotratamiento' => $historial->costotratamiento,
                                                'observaciones' => $historial->observaciones,
                                                'observacionesinterno' => $historial->observacionesinterno,
                                                'tipo' => $tipohistorialactualizar,
                                                'created_at' => Carbon::now()
                                            ]);

                                            if($crearsegundohistorialsinconversion) {

                                                $historialsinconversionentrante = DB::select("SELECT * FROM historialsinconversion WHERE id_contrato = '$idContrato'
                                                                                                            AND id_historial = '$idhistorialentrante' ORDER BY created_at DESC LIMIT 1");

                                                if ($historialsinconversionentrante != null) {

                                                    DB::table('historialsinconversion')->insert([
                                                        'id_contrato' => $idContrato,
                                                        'id_historial' => $idhistorialnuevo,
                                                        'esfericoder' => $historialsinconversionentrante[0]->esfericoder,
                                                        'cilindroder' => $historialsinconversionentrante[0]->cilindroder,
                                                        'ejeder' => $historialsinconversionentrante[0]->ejeder,
                                                        'addder' => $historialsinconversionentrante[0]->addder,
                                                        'esfericoizq' => $historialsinconversionentrante[0]->esfericoizq,
                                                        'cilindroizq' => $historialsinconversionentrante[0]->cilindroizq,
                                                        'ejeizq' => $historialsinconversionentrante[0]->ejeizq,
                                                        'addizq' => $historialsinconversionentrante[0]->addizq,
                                                        'created_at' => Carbon::now()
                                                    ]);

                                                }

                                            }

                                        }

                                        DB::table('historialclinico')->where([['id', '=', $idhistorialentrante], ['id_contrato', '=', $idContrato]])->update([
                                            'id_paquete' => $paquetehistorialeditar,
                                            'esfericoder' => $esfericoderactualizar,
                                            'cilindroder' => $cilindroderactualizar,
                                            'ejeder' => $ejederactualizar,
                                            'addder' => $addderactualizar,
                                            'altder' => $altderactualizar,
                                            'esfericoizq' => $esfericoizqactualizar,
                                            'cilindroizq' => $cilindroizqactualizar,
                                            'ejeizq' => $ejeizqactualizar,
                                            'addizq' => $addizqactualizar,
                                            'altizq' => $altizqactualizar,
                                            'tipo' => $tipohistorialactualizar
                                        ]);

                                        $contador++;

                                    }

                                    $nombrePaqueteEntrante = $contratosGlobal::obtenerNombrePaquete($idpaquetehistorialentrante);
                                    $nombrePaqueteActualizar = $contratosGlobal::obtenerNombrePaquete($paquetehistorialeditar);

                                    $cambios = "Solicitud para cambio de paquete '" . $nombrePaqueteEntrante . "'" . " a " . "'" . $nombrePaqueteActualizar . "' autorizada.";
                                    $mensaje = " Solicitud para cambio de paquete autorizada correctamente.";

                                }

                            } catch (\Exception $e) {
                                \Log::info("Error: " . $e->getMessage());
                                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
                            }
                            break;

                        case 6:
                            //Tipo traspaso contrato
                            $cambios = "Solicitud traspaso de contrato autorizada.";
                            $mensaje = " Solicitud traspaso de contrato autorizada correctamente.";
                            break;

                        case 7:
                            //Tipo abono minimo
                            $cambios = "Solicitud de abono minimo autorizada.";
                            $mensaje = " Solicitud de abono minimo autorizada correctamente.";
                            break;

                        case 8:
                        case 9:
                        case 10:
                            //Tipo armazón por ninguna, poliza o defecto de fabrica
                            try {

                                $autorizacionarmazonlaboratorio = DB::select("SELECT * FROM autorizacionarmazonlaboratorio WHERE id_autorizacion = '$indice'");

                                if ($autorizacionarmazonlaboratorio != null) {
                                    //Existe una solicitud de armazon por defecto de fabrica

                                    $producto = DB::select("SELECT * FROM producto WHERE id = '" . $autorizacionarmazonlaboratorio[0]->id_armazon . "'");

                                    if ($producto != null) {
                                        //Existe el producto

                                        $abonototal = 0;
                                        if ($producto[0]->preciop == null) {
                                            //No tiene promocion
                                            if($producto[0]->id_tipoproducto == 1){ //Es un armazon?
                                                //Productos tipo armazon
                                                switch ($existeSolicitud[0]->tipo) {
                                                    case 8:
                                                        //Asignar el precio normal del armazon
                                                        $abonototal = $producto[0]->precio * $autorizacionarmazonlaboratorio[0]->piezas;
                                                        break;
                                                    case 9:
                                                        //Aplicar descuento por poliza
                                                        $abonototal = 120 * $autorizacionarmazonlaboratorio[0]->piezas;
                                                        break;
                                                    case 10:
                                                        //Aplicar defecto de fabrica
                                                        $abonototal = 0;
                                                        break;
                                                }
                                            }else {
                                                //Producto tipo poliza o gotas
                                                $abonototal = $producto[0]->precio * $autorizacionarmazonlaboratorio[0]->piezas;
                                            }
                                        } else {
                                            //Tiene promocion
                                            if($producto[0]->id_tipoproducto == 1){
                                                //Productos tipo armazon
                                                switch ($existeSolicitud[0]->tipo) {
                                                    case 8:
                                                        //Asignar el precio normal del armazon
                                                        $abonototal = $producto[0]->preciop * $autorizacionarmazonlaboratorio[0]->piezas;
                                                        break;
                                                    case 9:
                                                        //Aplicar descuento por poliza
                                                        $abonototal = 120 * $autorizacionarmazonlaboratorio[0]->piezas;
                                                        break;
                                                    case 10:
                                                        //Aplicar defecto de fabrica
                                                        $abonototal = 0;
                                                        break;
                                                }
                                            } else {
                                                //Procuto tipo poliza o gotas
                                                $abonototal = $producto[0]->preciop * $autorizacionarmazonlaboratorio[0]->piezas;
                                            }
                                        }

                                        $mensajesolicitudarmazon = "";
                                        //Mensaje solicitud historial movimientos
                                        switch ($existeSolicitud[0]->tipo) {
                                            case 9:
                                                //Aplicar descuento por poliza
                                                $mensajesolicitudarmazon = " por poliza";
                                                break;
                                            case 10:
                                                //Aplicar defecto de fabrica
                                                $mensajesolicitudarmazon = " por defecto de fábrica";
                                                break;
                                        }

                                        $usuario = DB::select("SELECT rol_id FROM users WHERE id = '" . $existeSolicitud[0]->id_usuarioC . "'");

                                        if ($usuario != null) {
                                            //Existe usuario

                                            $folioAbono = null;
                                            $corte = 2;
                                            if ($usuario[0]->rol_id == 4) {
                                                $folioAbono = $contratosGlobal::validarSiExisteFolioAlfanumericoEnAbonosContrato($idContrato);
                                                $corte = 0;
                                            }

                                            $productoArmazon = DB::select("SELECT cp.id FROM contratoproducto cp WHERE cp.id_contrato = '$idContrato'
                                                                                    AND cp.id_producto = '" . $autorizacionarmazonlaboratorio[0]->id_armazon . "' AND cp.estadoautorizacion = 0
                                                                                    ORDER BY cp.created_at DESC LIMIT 1");

                                            if($productoArmazon != null){
                                                $idcontratoproducto = $productoArmazon[0]->id;
                                            }else{
                                                $idcontratoproducto = $globalesServicioWeb::generarIdAlfanumerico('contratoproducto', '5');
                                            }

                                            //Actualizar registro de contratoproducto
                                            DB::table("contratoproducto")->where("id", "=", $idcontratoproducto)
                                                ->update(['estadoautorizacion' => "1", 'total' => $abonototal, 'updated_at' => Carbon::now()]);

                                            //Descontar pieza del producto
                                            DB::table('producto')->where('id', '=', $producto[0]->id)->update([
                                                'piezas' => $producto[0]->piezas - $autorizacionarmazonlaboratorio[0]->piezas
                                            ]);

                                            //Verificar numero de piezas en existencia del producto
                                            $contratosGlobal::verificarPiezasRestantesProducto($autorizacionarmazonlaboratorio[0]->id_armazon);

                                            $idAbono = $globalesServicioWeb::generarIdAlfanumerico('abonos', '5');

                                            //Agregar abono al contrato
                                            DB::table('abonos')->insert([
                                                'id' => $idAbono,
                                                'folio' => $folioAbono,
                                                'id_franquicia' => $existeContrato[0]->id_franquicia,
                                                'id_contrato' => $idContrato,
                                                'id_usuario' => $existeSolicitud[0]->id_usuarioC,
                                                'tipoabono' => 7,
                                                'abono' => $abonototal,
                                                'metodopago' => 0,
                                                'adelantos' => 0,
                                                'atraso' => 0,
                                                'corte' => $corte,
                                                'id_contratoproducto' => $idcontratoproducto,
                                                "id_zona" => $existeContrato[0]->id_zona,
                                                'created_at' => Carbon::now()
                                            ]);

                                            //Validacion de si es garantia o no
                                            $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1 ORDER BY created_at LIMIT 1");

                                            if ($existeContrato[0]->estatus_estadocontrato == 2 || $existeContrato[0]->estatus_estadocontrato == 4
                                                || $existeContrato[0]->estatus_estadocontrato == 12
                                                || (($existeContrato[0]->estatus_estadocontrato == 1 || $existeContrato[0]->estatus_estadocontrato == 9
                                                        || $existeContrato[0]->estatus_estadocontrato == 10 || $existeContrato[0]->estatus_estadocontrato == 11
                                                        || $existeContrato[0]->estatus_estadocontrato == 7) && $tieneHistorialGarantia != null)) {
                                                //ENTREGADO, ABONO ATRASADO, LIQUIDADO, ENVIADO O TERMINADO, EN PROCESO DE APROBACION, MANUFACTURA, EN PROCESO DE ENVIO, APROBADO Y TENGA GARANTIA

                                                //Insertar abono en abonoscontratostemporalessincronizacion
                                                $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($existeContrato[0]->id_zona);
                                                foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                                    //Recorrido cobradores
                                                    //Insertar en tabla abonoscontratostemporalessincronizacion
                                                    DB::table("abonoscontratostemporalessincronizacion")->insert([
                                                        "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                                        "id" => $idAbono,
                                                        "folio" => $folioAbono,
                                                        "id_contrato" => $idContrato,
                                                        "id_usuario" => $existeSolicitud[0]->id_usuarioC,
                                                        "abono" => $abonototal,
                                                        "adelantos" => 0,
                                                        "tipoabono" => 7,
                                                        "atraso" => 0,
                                                        "metodopago" => 0,
                                                        "corte" => $corte,
                                                        "created_at" => Carbon::now()
                                                    ]);
                                                }

                                            }

                                            $mensajefoliocambios = $autorizacionarmazonlaboratorio[0]->foliopoliza != null ? " folio: '" . $autorizacionarmazonlaboratorio[0]->foliopoliza . "'" : "";

                                            //Registrar movimientos para Control de armazones
                                            DB::table('historialcontrato')->insert([
                                                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                                                'id_usuarioC' => Auth::user()->id,
                                                'id_contrato' => $idContrato,
                                                'created_at' => Carbon::now(),
                                                'cambios' => "Autorizó el envio de un armazon con identificador: " . $producto[0]->id . " | " . $producto[0]->nombre
                                                    . " | " . $producto[0]->color . " | cantidad de piezas: '" . $autorizacionarmazonlaboratorio[0]->piezas
                                                    . "'" . $mensajesolicitudarmazon . $mensajefoliocambios . ".",
                                                'tipomensaje' => '4'
                                            ]);

                                            //Guardar en historial de movimientos el abono
                                            DB::table('historialcontrato')->insert([
                                                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                                                'id_usuarioC' => $existeSolicitud[0]->id_usuarioC,
                                                'id_contrato' => $idContrato,
                                                'created_at' => Carbon::now(),
                                                'cambios' => " Se agrego el abono : '$abonototal'"
                                            ]);

                                            //Actualizar atributo autorizacion como aprobado al contratotemporal
                                            DB::table("contratostemporalessincronizacion")->where("id", "=", $idContrato)->update([
                                                'autorizacion' => "1"
                                            ]);

                                            $cambios = "Solicitud de armazón" . $mensajesolicitudarmazon . " autorizada.";
                                            $mensaje = " Solicitud de armazón" . $mensajesolicitudarmazon . " autorizada correctamente.";

                                        }else {
                                            //No existe el usuario
                                            return back()->with('alerta', 'No existe el usuario');
                                        }
                                    }else {
                                        //No existe el producto
                                        return back()->with('alerta', 'No existe ningun registro de armazón para el contrato (Producto no encontrado)');
                                    }
                                }else {
                                    //No existe un registro de armazon
                                    return back()->with('alerta', 'No existe ningun registro de armazón para el contrato');
                                }
                            } catch (\Exception $e) {
                                \Log::info("Error: " . $e->getMessage());
                                return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
                            }
                            break;

                        case 1:
                        case 11:
                            //Tipo cancelar o lio/fuga contrato

                            $estatusContrato = $existeContrato[0]->estatus_estadocontrato;
                            $estatusActualizar = 6;
                            $mensajesolicitud = "cancelacion de";

                            if ($existeSolicitud[0]->tipo == 11) {
                                //Tipo lio/fuga
                                $estatusActualizar = 14;
                                $mensajesolicitud = "levantar lio/fuga al";

                                DB::table('historialcontrato')->insert([
                                    'id' => $this->getHistorialContratoId(), 'id_usuarioC' => Auth::user()->id, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                    'cambios' => $existeSolicitud[0]->valor, 'tipomensaje' => 1
                                ]);

                                //Insertar contrato a tabla contratosliofuga
                                $motivoSolicitud = explode(":",$existeSolicitud[0]->mensaje);
                                $contratosGlobal::insertarEliminarContratosLioFuga($idContrato,$motivoSolicitud[1],0);
                            }

                            DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $existeContrato[0]->id_franquicia]])->update([
                                'estatus_estadocontrato' => $estatusActualizar, 'poliza' => $existeContrato[0]->poliza, 'estatusanteriorcontrato' => $estatusContrato
                            ]);

                            //Insertar en tabla registroestadocontrato
                            DB::table('registroestadocontrato')->insert([
                                'id_contrato' => $idContrato,
                                'estatuscontrato' => $estatusActualizar,
                                'created_at' => Carbon::now()
                            ]);

                            //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato
                            DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");

                            //Eliminar registros de la tabla abonoscontratostemporalessincronizacion que contengan ese idContrato
                            DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_contrato = '$idContrato'");

                            $cambios = "Solicitud para $mensajesolicitud contrato autorizada.";
                            $mensaje = " Solicitud para $mensajesolicitud contrato autorizada correctamente.";
                            break;

                        case 12:
                            //Tipo de supervision de contrato
                            try {

                                //Actualizar estado SUPERVISION
                                DB::table("contratos")->where("id", "=", $idContrato)->update([
                                    'estatus_estadocontrato' => 15
                                ]);

                                //Insertar en tabla registroestadocontrato
                                DB::table('registroestadocontrato')->insert([
                                    'id_contrato' => $idContrato,
                                    'estatuscontrato' => 15,
                                    'created_at' => Carbon::now()
                                ]);

                                //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato
                                DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");

                                //Eliminar registros de la tabla abonoscontratostemporalessincronizacion que contengan ese idContrato
                                DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_contrato = '$idContrato'");

                                //Insertar contrato a tabla contratosliofuga
                                $motivoSolicitud = explode(":",$existeSolicitud[0]->mensaje);
                                $contratosGlobal::insertarEliminarContratosLioFuga($idContrato,$motivoSolicitud[1],0);

                                $cambios = "Se cambio el estatus del contrato a 'supervision'";
                                $mensaje = "El estatus del contrato se actualizo correctamente";

                            } catch (\Exception $e) {
                                \Log::info("Error: " . $e->getMessage());
                                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
                            }
                            break;

                        case 15:
                            //Tipo de lista negra contrato
                            $contratoListaNegra = DB::select("SELECT * FROM contratoslistanegra cln WHERE cln.id_contrato = '$idContrato' AND cln.estado = 1 ORDER BY cln.created_at DESC LIMIT 1");

                            $descripcion = "";
                            if($contratoListaNegra != null){
                                $descripcion = $contratoListaNegra[0]->descripcion;
                            }

                            //Insertar contrato a tabla contratosliofuga
                            $contratosGlobal::insertarEliminarContratosLioFuga($idContrato,$descripcion,0);

                            $cambios = "Se ingreso contrato a lista negra con la siguiente descripcion: '" . $descripcion . "'";
                            $mensaje = "Se ingreso contrato a lista negra correctamente";
                            break;

                        case 16:
                            //Tipo de promocion empleado
                            $idPromocion = $existeSolicitud[0]->valor;
                            $randomId = $this->getPromocionContratoId();
                            $idFranquicia = $existeContrato[0]->id_franquicia;

                            $existePromocion = DB::select("SELECT * FROM promocion p WHERE p.id = '$idPromocion' AND p.id_franquicia = '$idFranquicia' AND p.status = 1");

                            if($existePromocion != null){
                                //Existe la promocion

                                //Promocion es diferente a empleado
                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'id_promocion' => $idPromocion, 'promocionterminada' => '1', 'estatus' => '1'
                                ]);
                                DB::table('promocion')->where([['id', '=', $idPromocion], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'asignado' => 1
                                ]);
                                DB::table('promocioncontrato')->insert([
                                    'id' => $randomId, 'id_contrato' => $idContrato, 'id_franquicia' => $idFranquicia, 'id_promocion' => $idPromocion, 'estado' => 1,
                                    'created_at' => Carbon::now()
                                ]);

                                //Calculo de precio con promocion
                                $totalPromocion = 0;
                                if($existePromocion[0]->tipopromocion == 0){
                                    //Descuento por porcentaje
                                    $totalPromocion = ($existeContrato[0]->totalreal * $existePromocion[0]->precioP) / 100;
                                }else{
                                    //Descuento por precio fijo
                                    $totalPromocion = $existeContrato[0]->totalreal - $existePromocion[0]->preciouno;
                                }

                                DB::table('contratos')->where('id', '=', $idContrato)->update(['totalpromocion' => $totalPromocion]);

                                //Actualizar total del contrato
                                $this->calculoTotal($idContrato, $idFranquicia);

                                $cambios = "Agrego promocion con titulo: '" . $existePromocion[0]->titulo . "' de tipo: 'Empleado'";
                                $mensaje = "Solicitud de promocion para empleado autorizada correctamente.";

                            }else{
                                return back()->with('alerta',"No existe la promocion/fue desactivada.");
                            }
                            break;
                    }

                    try {
                        //Actualizar a estatus AUTORIZADA
                        DB::table('autorizaciones')->where([['indice', '=', $indice], ['id_contrato', '=', $idContrato]])->update([
                            'estatus' => '1', 'updated_at' => Carbon::now()
                        ]);

                        if($existeSolicitud[0]->tipo == 0){
                            //Si es una solicitud de garantia

                            //Generar una garantia con estatus 0
                            $idGarantiaAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('garantias', '5');
                            DB::table('garantias')->insert([
                                'id' => $idGarantiaAlfanumerico,
                                'id_contrato' => $idContrato,
                                'estadogarantia' => 0,
                                'created_at' => Carbon::now()
                            ]);
                        }

                        //Registrar movimiento
                        $idHistorialContratoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');
                        DB::table('historialcontrato')->insert([
                            'id' => $idHistorialContratoAlfanumerico,
                            'id_usuarioC' => Auth::user()->id,
                            'id_contrato' => $idContrato,
                            'created_at' => Carbon::now(),
                            'cambios' => $cambios,
                            'tipomensaje' => '3'
                        ]);

                        return back()->with('bien', $mensaje);

                    } catch (Exception $e){
                        \Log::info("Error: " . $e->getMessage());
                        return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina   Error:' . $e->getMessage());
                    }

                }else{
                    //No existe ninguna solicitud para el contrato
                    return back()->with('alerta', 'No existe ninguna solicitud para el contrato');
                }

            }else {
                return back()->with('alerta', 'No existe el contrato o no pertenece a la sucursal.');
            }

        }else{
            if(Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function rechazarcontrato($idContrato, $indice){
        if (Auth::check() && ((Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8  || (Auth::user()->rol_id) == 15 || (Auth::user()->rol_id) == 16)){
            //ROL DE DIRECTOR  - GENERAL - CONFIRMACIONES - LABORATORIO

            $rol_id = Auth::user()->rol_id;
            $idUsuario = Auth::user()->id;
            $globalesServicioWeb = new globalesServicioWeb;

            //Comprobar que contrato pertenezca a sucursal asignada
            switch ($rol_id){
                case 8:
                    //Rol de PRINCIPAL
                    $existeContrato = DB::select("SELECT * FROM contratos c
                                                inner join usuariosfranquicia uf ON uf.id_franquicia = c.id_franquicia
                                                INNER JOIN users u ON uf.id_usuario = u.id
                                                WHERE c.id = '$idContrato' AND u.id = '$idUsuario'");
                    break;

                case 7:
                case 16:
                    //Rol de DIRECTOR
                    $existeContrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato' and c.id_franquicia != '00000'");
                    break;
                case 15:
                    //Rol de CONFIRMACIONES
                    $existeContrato = DB::select("SELECT * FROM contratos c
                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
                                                INNER JOIN sucursalesconfirmaciones sc ON c.id_franquicia = sc.id_franquicia
                                                INNER JOIN users u ON u.id = sc.id_usuario
                                                WHERE c.id = '$idContrato' AND  sc.id_usuario = '$idUsuario'");
                    break;
            }

            $existeSolicitud = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato = '$idContrato' AND a.estatus = '0' AND a.indice = '$indice'");
            if($existeContrato != null){
                //Existe el contrato y pertenece a la sucursal
                if($existeSolicitud != null){
                    //Si existe solicitud
                    switch ($existeSolicitud[0]->tipo){
                        case 0:
                            //Tipo Solicitud de garantia
                            $cambios = "Solicitud de garantia rechazada.";
                            $mensaje = " Solicitud de garantia rechazada correctamente.";
                            break;

                        case 1:
                            //Tipo cancelar contrato
                            $cambios = "Solicitud para cancelacion de contrato rechazada.";
                            $mensaje = " Solicitud para cancelacion de contrato rechazada correctamente.";
                            break;

                        case 2:
                            //Tipo aumentar/descontar
                            $cambios = "Solicitud para cambio de precio en contrato rechazada.";
                            $mensaje = " Solicitud para cambio de precio en contrato rechazada correctamente.";
                            break;

                        case 4:
                            //Tipo cambiar paquete
                            $cambios = "Solicitud para cambio de paquete rechazada.";
                            $mensaje = " Solicitud para cambio de paquete rechazada correctamente.";
                            break;

                        case 6:
                            //Tipo traspaso contrato
                            $cambios = "Solicitud traspaso de contrato rechazada.";
                            $mensaje = " Solicitud traspaso de contrato rechazada correctamente.";
                            break;

                        case 7:
                            //Abono minimo
                            $cambios = "Solicitud de abono minimo rechazada.";
                            $mensaje = " Solicitud de abono minimo rechazada correctamente.";
                            break;

                        case 8:
                        case 9:
                        case 10:
                            //Armazón por ninguna, poliza o defecto de fabrica

                            $autorizacionarmazonlaboratorio = DB::select("SELECT * FROM autorizacionarmazonlaboratorio WHERE id_autorizacion = '$indice'");

                            if ($autorizacionarmazonlaboratorio != null) {
                                //Existe una solicitud de armazon

                                $producto = DB::select("SELECT * FROM producto WHERE id = '" . $autorizacionarmazonlaboratorio[0]->id_armazon . "'");

                                if ($producto != null) {
                                    //Existe el producto

                                    $mensajesolicitudarmazon = "";
                                    //Mensaje solicitud historial movimientos
                                    switch ($existeSolicitud[0]->tipo) {
                                        case 9:
                                            //Aplicar descuento por poliza
                                            $mensajesolicitudarmazon = " por poliza";
                                            break;
                                        case 10:
                                            //Aplicar defecto de fabrica
                                            $mensajesolicitudarmazon = " por defecto de fábrica";
                                            break;
                                    }

                                    //Registrar movimientos para Control de armazones
                                    DB::table('historialcontrato')->insert([
                                        'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                                        'id_usuarioC' => Auth::user()->id,
                                        'id_contrato' => $idContrato,
                                        'created_at' => Carbon::now(),
                                        'cambios' => "Rechazo el envio de un armazon con identificador: " . $producto[0]->id . " | " . $producto[0]->nombre
                                            . " | " . $producto[0]->color . " | cantidad de piezas: '" . $autorizacionarmazonlaboratorio[0]->piezas . "'" . $mensajesolicitudarmazon . ".",
                                        'tipomensaje' => '4'
                                    ]);

                                    //Eliminar registro de contratoproducto
                                    DB::delete("DELETE FROM contratoproducto WHERE id_contrato = '$idContrato' AND id_producto = '" . $autorizacionarmazonlaboratorio[0]->id_armazon . "' AND estadoautorizacion = 0 ORDER BY created_at DESC LIMIT 1");

                                    /*
                                    DB::table("contratoproducto")->where("id_contrato", "=", $idContrato)
                                        ->where("id_producto","=",$autorizacionarmazonlaboratorio[0]->id_armazon)
                                        ->where("estadoautorizacion","=","0")
                                        ->update(['estadoautorizacion' => "2", 'updated_at' => Carbon::now()]);
                                    */

                                    //Actualizar atributo autorizacion como rechazado al contratotemporal
                                    DB::table("contratostemporalessincronizacion")->where("id", "=", $idContrato)->update([
                                        'autorizacion' => "2"
                                    ]);

                                    $cambios = "Solicitud de armazón" . $mensajesolicitudarmazon . " rechazada.";
                                    $mensaje = " Solicitud de armazón" . $mensajesolicitudarmazon . " rechazada correctamente.";

                                }else {
                                    //No existe el producto
                                    return back()->with('alerta', 'No existe ningun registro de armazon para el contrato (Producto no encontrado)');
                                }
                            }else {
                                //No existe un registro de armazon
                                return back()->with('alerta', 'No existe ningun registro de armazon para el contrato');
                            }
                            break;

                        case 11:
                            //Tipo lio/fuga contrato
                            $cambios = "Solicitud para levantar lio/fuga al contrato rechazada.";
                            $mensaje = " Solicitud para levantar lio/fuga al contrato rechazada correctamente.";
                            break;

                        case 12:
                            //Supervision
                            $cambios = "Solicitud de cambio de estatus del contrato a 'supervision' rechazada.";
                            $mensaje = " Solicitud de cambio de estatus del contrato a 'supervision' rechazada.";
                            break;
                        case 15:
                            //Lista negra
                            $contratoListaNegra = DB::select("SELECT * FROM contratoslistanegra cln WHERE cln.id_contrato = '$idContrato' AND cln.estado = 1 ORDER BY cln.created_at DESC LIMIT 1");

                            $descripcion = "";
                            if($contratoListaNegra != null){
                                $descripcion = $contratoListaNegra[0]->descripcion;
                                DB::table('contratoslistanegra')->where([['indice', '=', $contratoListaNegra[0]->indice], ['id_contrato', '=', $idContrato]])->update([
                                    'estado' => '2', 'updated_at' => Carbon::now()
                                ]);
                            }

                            $cambios = "Solicitud para ingreso de contrato a lista negra con la siguiente descripcion: '" . $descripcion . "' rechazada";
                            $mensaje = "Solicitud para ingreso de contrato a lista negra rechazada";
                            break;
                        case 16:
                            //Promocion empleado
                            $idPromocion = $existeSolicitud[0]->valor;
                            $idFranquicia = $existeContrato[0]->id_franquicia;
                            $existePromocion = DB::select("SELECT * FROM promocion p WHERE p.id = '$idPromocion' AND p.id_franquicia = '$idFranquicia' AND p.status = 1");

                            if($existePromocion != null) {
                                //Existe la promocion
                                $cambios = "Solicitud promocion con titulo: '" . $existePromocion[0]->titulo . "' de tipo: 'Empleado' rechazada.";
                                $mensaje = "Solicitud promocion para empleado rechazada correctamente.";

                            }else{
                                return back()->with('alerta',"No existe la promocion/fue desactivada.");
                            }

                            break;

                    }
                    try {
                        //Actualizar a estatus AUTORIZADA
                        DB::table('autorizaciones')->where([['indice', '=', $indice], ['id_contrato', '=', $idContrato]])->update([
                            'estatus' => '2', 'updated_at' => Carbon::now()
                        ]);

                        //Registrar movimiento
                        $idHistorialContratoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');
                        DB::table('historialcontrato')->insert([
                            'id' => $idHistorialContratoAlfanumerico,
                            'id_usuarioC' => Auth::user()->id,
                            'id_contrato' => $idContrato,
                            'created_at' => Carbon::now(),
                            'cambios' => $cambios,
                            'tipomensaje' => '3'
                        ]);

                        return back()->with('bien', $mensaje);

                    } catch (Exception $e){
                        \Log::info("Error: " . $e->getMessage());
                        return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina   Error:' . $e->getMessage());
                    }

                }else{
                    //No existe ninguna solicitud para el contrato
                    return back()->with('alerta', 'No existe ninguna solicitud para el contrato.');
                }
            } else {
                return back()->with('alerta', 'No existe el contrato o no pertenece a sucursal.');
            }

        }else{
            if(Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function solicitarautorizaciongarantia($idFranquicia, $idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 15)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL - CONFIRMACIONES

            $rol_id = Auth::user()->rol_id;
            $idUsuario = Auth::user()->id;

            $mensaje = $request->input("mensaje");

            //Validaciones de campo mensaje
            request()->validate([
                'mensaje' => 'required|string|min:1'
            ]);

            if ($rol_id == 15) {
                //Si es rol CONFIRMACIONES
                $validacionContrato = "c.id = '$idContrato'";
            } else {
                //Si es rol DIRECTOR, ADMINISTRACION, PRINCIPAL
                $validacionContrato = "c.id = '$idContrato' AND c.id_franquicia = '$idFranquicia'";

            }
            $existeContrato = DB::select("SELECT * FROM contratos c WHERE $validacionContrato");

            if ($existeContrato != null) {
                //Si existe el contrato
                if ($rol_id == 15) {
                    //Si es CONFIRMACIONES valida que el contrato pertenezca a una franquicia asiganada

                    $banderaFranquicia = false;
                    //optenemos sucursales asignadas
                    $franquicias = DB::select("SELECT f.id FROM franquicias f
                                                INNER JOIN sucursalesconfirmaciones sf ON f.id = sf.id_franquicia
                                                WHERE f.id != '00000' AND sf.id_usuario = '$idUsuario' ORDER BY ciudad ASC");

                    foreach ($franquicias as $franquicia) {
                        if ($franquicia->id == $existeContrato[0]->id_franquicia) {
                            //Si la sucursal del contrato pertenece a una asiganada a confirmaciones
                            $banderaFranquicia = true; // Bandera = verdadero y salir del ciclo
                            break;
                        }
                    }
                    if ($banderaFranquicia == false) {
                        //No pertenece a ningua fraqnuicia asiganada
                        return back()->with('alerta', 'No puedes accesar al contrato debido a la sucursal que pertenece.');
                    }
                }

                if ($existeContrato[0]->estatus_estadocontrato == 2 || $existeContrato[0]->estatus_estadocontrato == 4 || $existeContrato[0]->estatus_estadocontrato == 5) {
                    //El contrato tiene estatus ENTREGADO, ATRASADO, PAGADO

                    try {
                        //Insertamos valores de peticion y movimiento

                        //Generamos ID alfanumero de identificacion de mensaje y movimiento
                        $globalesServicioWeb = new globalesServicioWeb;
                        $idHistorialContratoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');

                        //Insertar solicitud de autorizacion
                        DB::table('autorizaciones')->insert([
                            'id_contrato' => $idContrato, 'id_usuarioC' => $idUsuario, 'id_franquicia' => $idFranquicia,
                            'fechacreacioncontrato' => $existeContrato[0]->created_at,
                            'estadocontrato' => $existeContrato[0]->estatus_estadocontrato,
                            'mensaje' => "Solicitó autorizacion con el siguiente mensaje: '$mensaje'",
                            'estatus' => '0', 'tipo' => '0', 'created_at' => Carbon::now()
                        ]);

                        //Insertamos el movimiento con su respectivo mensaje de solicitud
                        DB::table('historialcontrato')->insert([
                            'id' => $idHistorialContratoAlfanumerico, 'id_usuarioC' => $idUsuario, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                            'cambios' => "Solicitó autorizacion para generar garantia a contrato con el siguiente mensaje: '$mensaje'", 'tipomensaje' => '3']);

                        return back()->with('bien', 'Solicitud de garantia generada correctamente');

                    } catch (Exception $e) {
                        \Log::info("Error: " . $e->getMessage());
                        return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina   Error:' . $e->getMessage());
                    }
                } else {
                    return back()->with('alerta', 'No puedes acceder al contrato debido a su estatus actual.');
                }
            } else {
                return back()->with('alerta', 'Contrato no existente, verifica el ID CONTRATO.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    //Funcion: solicitarautorizacionaumentardisminuir
    //Descripcion: Genera la solicitud para aumentra o diminuir el total del contrato, almacena en tabla autorizaciones, movimientos con un tipo 2 (aumentar/disminuir)
    public function solicitarautorizacionaumentardisminuir($idFranquicia, $idContrato)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) {
            //ROL DE DIRECTOR - ADMINISTRADOR

            $idUsuario = Auth::user()->id;

            $mensaje = request('mensaje');

            //Validaciones de campo mensaje
            if (strlen($mensaje) < 15) {
                //mensaje menor a 15 caracteres
                return back()->with('alerta', 'La explicación debe contener al menos 15 caracteres.');
            }

            try {

                $contrato = DB::select("SELECT c.estatus_estadocontrato as estatus_estadocontrato,
                                                (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia,
                                                    c.total as total,
                                                        c.created_at as created_at
                                                            FROM contratos c WHERE c.id_franquicia = '$idFranquicia' AND c.id = '$idContrato'");

                if ($contrato != null) {
                    //Existe el contrato

                    $estadocontrato = $contrato[0]->estatus_estadocontrato;

                    if ($estadocontrato == 2 || $estadocontrato == 4 || $estadocontrato == 5 || $estadocontrato == 12) {
                        //estadocontrato ENTREGADO, ABONOATRASADO, LIQUIDADO O ENVIADO

                        $estadogarantia = $contrato[0]->estadogarantia;

                        if ($estadogarantia != 2) {
                            //estadogarantia sea diferente a creada

                            $total = $contrato[0]->total;

                            $aumentardescontar = request('aumentardescontar');
                            $mensajeautorizacion = "";

                            if ($aumentardescontar < 0) {
                                //Estan descontando
                                if (($total + $aumentardescontar) < 0) {
                                    return back()->with('alerta', 'No se puede descontar mas de lo que es el saldo actual.');
                                }
                                $mensajeautorizacion = "descontar";
                            } else {
                                //Estan aumentando
                                if ($aumentardescontar > 9999) {
                                    return back()->with('alerta', 'No se puede aumentar mas de $9999.');
                                }
                                $mensajeautorizacion = "aumentar";
                            }

                            //Insertamos valores de peticion y movimiento
                            //Generamos ID alfanumero de identificacion de mensaje y movimiento
                            $globalesServicioWeb = new globalesServicioWeb;

                            //Insertar solicitud de autorizacion
                            DB::table('autorizaciones')->insert([
                                'id_contrato' => $idContrato, 'id_usuarioC' => $idUsuario, 'id_franquicia' => $idFranquicia,
                                'fechacreacioncontrato' => $contrato[0]->created_at,
                                'estadocontrato' => $estadocontrato,
                                'mensaje' => "Solicitó autorizacion para $mensajeautorizacion con el siguiente mensaje: '$mensaje'",
                                'estatus' => '0', 'tipo' => '2', 'valor' => $aumentardescontar, 'created_at' => Carbon::now()
                            ]);

                            //Insertamos el movimiento con su respectivo mensaje de solicitud
                            DB::table('historialcontrato')->insert([
                                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $idUsuario,
                                'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                'cambios' => "Solicitó autorizacion para $mensajeautorizacion con el siguiente mensaje: '$mensaje'", 'tipomensaje' => '3']);

                            return back()->with('bien', "Solicitud para $mensajeautorizacion generada correctamente");

                        }
                        return back()->with('alerta', 'No se puede solicitar el cambio de precio (Se tiene garantia).');

                    }
                    return back()->with('alerta', 'No se puede solicitar el cambio de precio (Verificar el estado del contrato).');

                }
                return back()->with('alerta', 'No se encontro el contrato.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    //Funcion: solicitarautorizacioncambiopaquete
    //Descripcion: Genera la solicitud para actualizar el paquete seleccionado del contrato, almacena en tabla autorizaciones, movimientos con un tipo 2 (aumentar/disminuir)
    public function solicitarautorizacioncambiopaquete($idFranquicia, $idContrato, $idHistorial)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) {
            //ROL DE DIRECTOR - ADMINISTRADOR

            $idUsuario = Auth::user()->id;

            $mensaje = request("mensaje");
            $paquetehistorialeditar = request('paquetehistorialeditar'.$idHistorial);

            if ($paquetehistorialeditar == '') {
                return back()->with('alerta', 'No se selecciono ningun paquete a actualizar');
            }

            //Validaciones de campo mensaje
            if (strlen($mensaje) < 15) {
                //mensaje menor a 60 caracteres
                return back()->with('alerta', 'La explicación debe contener al menos 15 caracteres.');
            }

            $existeContrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato' AND c.id_franquicia = '$idFranquicia'");

            if ($existeContrato != null) {
                //Si existe el contrato

                if ($existeContrato[0]->estatus_estadocontrato == 0 || $existeContrato[0]->estatus_estadocontrato == 1 || $existeContrato[0]->estatus_estadocontrato == 2
                    || $existeContrato[0]->estatus_estadocontrato == 3 || $existeContrato[0]->estatus_estadocontrato == 4 || $existeContrato[0]->estatus_estadocontrato == 5
                    || $existeContrato[0]->estatus_estadocontrato == 12 ||  $existeContrato[0]->estatus_estadocontrato == 15) {
                    //El contrato tiene estatus NO TERMINADO, TERMINADO, ENTREGADO, PRE-CANCELADO, ATRASADO, PAGADO, ENVIADO, SUPERVISION

                    try {

                        $datosHistorial = DB::select("SELECT id_contrato, tipo, id_paquete FROM historialclinico WHERE id = '$idHistorial' AND id_contrato = '$idContrato'");

                        if ($datosHistorial != null) {
                            //Existe historial

                            $tipoHistorial = $datosHistorial[0]->tipo;
                            $idpaquetehistorial = $datosHistorial[0]->id_paquete;

                            if($idpaquetehistorial == $paquetehistorialeditar) {
                                //Se quiere actualizar al mismo paquete
                                return back()->with('alerta', 'No se puede actualizar al mismo paquete');
                            }

                            if($tipoHistorial == 0) {
                                //Es tipo 0 (Historial no garantia)

                                $idContrato = $datosHistorial[0]->id_contrato;

                                $contrato = DB::select("SELECT (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia
                                                            FROM contratos c WHERE c.id_franquicia = '$idFranquicia' AND c.id = '$idContrato'");

                                if($contrato != null) {
                                    //Existe contrato
                                    $estadogarantia = $contrato[0]->estadogarantia;

                                    if($estadogarantia != 2) {
                                        //estadogarantia sea diferente a creada

                                        //Insertamos valores de peticion y movimiento
                                        //Generamos ID alfanumero de identificacion de mensaje y movimiento
                                        $globalesServicioWeb = new globalesServicioWeb;
                                        $idHistorialContratoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');

                                        //Insertar solicitud de autorizacion
                                        DB::table('autorizaciones')->insert([
                                            'id_contrato' => $idContrato, 'id_usuarioC' => $idUsuario, 'id_franquicia' => $idFranquicia,
                                            'fechacreacioncontrato' => $existeContrato[0]->created_at,
                                            'estadocontrato' => $existeContrato[0]->estatus_estadocontrato,
                                            'mensaje' => "Solicitó autorizacion con el siguiente mensaje: '$mensaje'",
                                            'estatus' => '0', 'tipo' => '4', 'valor'=> $paquetehistorialeditar, 'created_at' => Carbon::now()
                                        ]);

                                        //Insertamos el movimiento con su respectivo mensaje de solicitud
                                        DB::table('historialcontrato')->insert([
                                            'id' => $idHistorialContratoAlfanumerico, 'id_usuarioC' => $idUsuario, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                            'cambios' => "Solicitó autorizacion para cambio de paquete con el siguiente mensaje: '$mensaje'", 'tipomensaje' => '3']);

                                        return back()->with('bien', 'Solicitud para cambio de paquete generada correctamente');

                                    }
                                    return back()->with('alerta', 'No se puede cambiar el paquete (Se tiene garantia).');

                                }
                                //No existe contrato
                                return back()->with('alerta', 'No se encontro el contrato.');

                            }
                            //Es tipo 1 (Historial de garantia)
                            return back()->with('alerta', 'El historial es de garantia');

                        }
                        //No existe el historial
                        return back()->with('alerta', 'No existe el historial');

                    } catch (Exception $e) {
                        \Log::info("Error: " . $e->getMessage());
                        return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina   Error:' . $e->getMessage());
                    }
                } else {
                    return back()->with('alerta', 'No puedes acceder al contrato debido a su estatus actual.');
                }
            } else {
                return back()->with('alerta', 'Contrato no existente, verifica el ID CONTRATO.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarfechaultimoabonocontrato($idFranquicia, $idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8)) {

            try {

                $contrato = DB::select("SELECT c.estatus_estadocontrato, c.pago, c.diapago,
                                                    (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia
                                                    FROM contratos c
                                                    WHERE c.id_franquicia = '$idFranquicia'
                                                    AND c.id = '$idContrato'");

                if($contrato != null) {
                    //EXISTE EL CONTRATO

                    if($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 4 || $contrato[0]->estatus_estadocontrato == 5
                        || ($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9
                            ||  $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11 && $contrato[0]->estadogarantia == 2)) {
                        //CUMPLE CON LAS VALIDACIONES DE ESTADOS EL CONTRATO

                        //Calculo de fechaini y fechafin
                        $fechaCobroIniActualizar = null;
                        $fechaCobroFinActualizar = null;
                        $fechaDiaSeleccionadoActualizar = null;

                        $calculofechaspago = new calculofechaspago;

                        if($contrato[0]->pago != 0) {
                            //Pago semanal, quincenal o mensual
                            //Calculo fechaCobroIniActual y fechaCobroFinActual
                            $arrayRespuesta = $calculofechaspago::obtenerFechasPeriodoActualOPeriodoSiguiente(Carbon::now(), $contrato[0]->pago, true);
                            $fechaCobroIniActualizar = $arrayRespuesta[0];
                            $fechaCobroFinActualizar = $arrayRespuesta[1];

                            //OBTENER DIASELECCIONADO
                            if(strlen($contrato[0]->diapago) > 0) {
                                //Se tiene un dia de pago
                                $fechaDiaSeleccionadoActualizar = $calculofechaspago::obtenerDiaSeleccionado($contrato[0]->diapago, $fechaCobroIniActualizar, $fechaCobroFinActualizar);
                            }

                        }

                        $fechaultimoabono = request('fechaultimoabono');

                        if (strlen($fechaultimoabono) > 0) {
                            //$fechaultimoabono diferente de vacio

                            //Validacion para poder saber si la fecha que se quiere actualizar cae entre fechaIni y fechaFin actual
                            if ((Carbon::parse($fechaultimoabono)->format('Y-m-d') >= Carbon::parse($fechaCobroIniActualizar)->format('Y-m-d')
                                && Carbon::parse($fechaultimoabono)->format('Y-m-d') <= Carbon::parse($fechaCobroFinActualizar)->format('Y-m-d'))
                                || (Carbon::parse($fechaultimoabono)->format('Y-m-d') > Carbon::parse($fechaCobroFinActualizar)->format('Y-m-d'))) {
                                return back()->with('alerta', 'La fecha a actualizar no debe caer entre las siguientes fechas: '
                                    . Carbon::parse($fechaCobroIniActualizar)->format('Y-m-d') . " al " . Carbon::parse($fechaCobroFinActualizar)->format('Y-m-d')
                                . " y no debe ser mayor a " . Carbon::parse($fechaCobroFinActualizar)->format('Y-m-d'));
                            }

                            $abonos = DB::select("SELECT indice, created_at, folio FROM abonos WHERE id_contrato ='$idContrato' ORDER BY created_at DESC");

                            $arrayIndicesAbonosActualizar = array();
                            $fechaAbonoPrincipal = "";
                            $fechaPenultimoAbono = "";
                            foreach ($abonos as $abono) {
                                //Recorrido de abonos

                                if (strlen($fechaAbonoPrincipal) > 0) {
                                    //$fechaAbonoPrincipal es diferente a vacio

                                    if ($fechaAbonoPrincipal === Carbon::parse($abono->created_at)->format('Y-m-d')) {
                                        //$fechaAbonoPrincipal es igual a la fecha de creacion del abono
                                        array_push($arrayIndicesAbonosActualizar, $abono->indice);
                                    }else {
                                        //$fechaAbonoPrincipal es diferente a la fecha de creacion del abono
                                        if (Carbon::parse($fechaultimoabono)->format('Y-m-d') < Carbon::parse($abono->created_at)->format('Y-m-d')) {
                                            //fechaultimoabono es menor a la fecha del penultimo abono al que no se va a actualizar
                                            return back()->with('alerta', 'La fecha a actualizar debe ser mayor a la fecha del penúltimo abono.');
                                        } else if (Carbon::parse($fechaultimoabono)->format('Y-m-d') === Carbon::parse($abono->created_at)->format('Y-m-d')) {
                                            //fechaultimoabono igual a la fecha del penultimo abono al que no se va a actualizar
                                            $fechaPenultimoAbono = $abono->created_at;
                                        }
                                        //Detener foreach
                                        break;
                                    }

                                }

                                if (strlen($fechaAbonoPrincipal) == 0) {
                                    //$fechaAbonoPrincipal es igual a vacio
                                    $fechaAbonoPrincipal = Carbon::parse($abono->created_at)->format('Y-m-d');
                                    array_push($arrayIndicesAbonosActualizar, $abono->indice);
                                }

                            }

                            foreach ($arrayIndicesAbonosActualizar as $indiceAbonoActualizar) {

                                $abono = DB::select("SELECT id, created_at, updated_at FROM abonos WHERE id_contrato ='$idContrato' AND indice = '" . $indiceAbonoActualizar . "'");

                                $fechaultimoabonoanterior = Carbon::parse($abono[0]->created_at);

                                if (strlen($fechaPenultimoAbono) > 0) {
                                    //Tiene algo $fechaPenultimoAbono
                                    $fechaultimoabono = Carbon::parse($fechaPenultimoAbono)->addSecond();
                                    $fechaPenultimoAbono = $fechaultimoabono;
                                }else {
                                    //No tiene nada en $fechaPenultimoAbono
                                    $fechaultimoabono = Carbon::parse($fechaultimoabono)->addSecond();
                                }

                                $tieneUpdateAt = " ";
                                if($abono[0]->updated_at != null) {
                                    //Tiene updated_at
                                    $tieneUpdateAt = ", updated_at = '"  . $fechaultimoabono . "'";
                                }

                                //Actualizar abono a fecha que se quiere actualizar
                                DB::update("UPDATE abonos
                                        SET created_at = '" . $fechaultimoabono . "'"
                                    . $tieneUpdateAt .
                                    " WHERE id_contrato = '$idContrato'
                                        AND indice = '" . $indiceAbonoActualizar . "'");

                                //Actualizar abonoscontratostemporalessincronizacion a fecha que se quiere actualizar
                                DB::update("UPDATE abonoscontratostemporalessincronizacion
                                        SET created_at = '" . $fechaultimoabono . "'"
                                    . $tieneUpdateAt .
                                    " WHERE id_contrato = '$idContrato'
                                        AND id = '" . $abono[0]->id . "'");

                                //Guadar movimiento en tabla historialcontrato
                                DB::table('historialcontrato')->insert([
                                    'id' => $this->getHistorialContratoId(),
                                    'id_usuarioC' => Auth::id(),
                                    'id_contrato' => $idContrato,
                                    'created_at' => Carbon::now(),
                                    'cambios' => "Se cambio la fecha del último abono de " . $fechaultimoabonoanterior
                                        . " a " . $fechaultimoabono . " con el codigo '" . $abono[0]->id . "'"
                                ]);

                            }

                            //Actualizar contrato
                            DB::table('contratos')->where([['id_franquicia', '=', $idFranquicia], ['id', '=', $idContrato]])->update([
                                'ultimoabono' => $fechaultimoabono,
                                'fechacobroini' => $fechaCobroIniActualizar,
                                'fechacobrofin' => $fechaCobroFinActualizar,
                                'diaseleccionado' => $fechaDiaSeleccionadoActualizar
                            ]);

                            return back()->with('bien', 'Se cambio correctamente la fecha del último abono.');

                        }else {
                            return back()->with('alerta', 'Campo fecha ultimo abono actualizar vacío.');
                        }


                    }
                    //NO CUMPLE CON LAS VALIDACIONES DE ESTADOS EL CONTRATO
                    return back()->with('alerta', 'No se puede actualizar la fecha del ultimo abono.');

                }
                //NO EXISTE EL CONTRATO
                return back()->with('alerta', 'El contrato no existe.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function restablecercontratocanceladorechazadoliofuga($idFranquicia, $idContrato)
    {
        if (Auth::check() && (((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8))) {
            //Rol administrador, director o principal

            try {

                $contrato = DB::select("SELECT c.estatus_estadocontrato as estatus_estadocontrato, c.id_zona as id_zona,
                                                    c.id_usuariocreacion as id_usuariocreacion
                                                    FROM contratos c WHERE c.id_franquicia = '$idFranquicia' AND c.id = '$idContrato'");

                if ($contrato != null) {
                    //Existe el contrato

                    $estadocontrato = $contrato[0]->estatus_estadocontrato;
                    $id_zona = $contrato[0]->id_zona;
                    $id_usuariocreacion = $contrato[0]->id_usuariocreacion;

                    if ($estadocontrato == 6 || $estadocontrato == 8 || $estadocontrato == 14) {
                        //Cancelado, rechazado o lio/fuga

                        switch($estadocontrato) {
                            case 6:
                                $mensaje = "cancelado";
                                break;
                            case 8:
                                $mensaje = "rechazado";
                                break;
                            default:
                                $mensaje = "lio/fuga";
                        }

                        $estadoactualizar = 9;
                        if($estadocontrato != 8) {
                            $registroestadocontrato = DB::select("SELECT estatuscontrato
                                                    FROM registroestadocontrato WHERE id_contrato = '$idContrato' AND estatuscontrato NOT IN (6,8,14)
                                                    ORDER BY created_at DESC LIMIT 1");

                            if ($registroestadocontrato != null) {
                                $estadoactualizar = $registroestadocontrato[0]->estatuscontrato;
                            } else {
                                return back()->with('alerta', 'No se pudo restablecer, favor de contactar a soporte.');
                            }
                        }

                        //Actualizar estado
                        DB::table("contratos")->where("id", "=", $idContrato)->where("id_franquicia", "=", $idFranquicia)->update([
                            'estatus_estadocontrato' => $estadoactualizar
                        ]);

                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $idContrato,
                            'estatuscontrato' => $estadoactualizar,
                            'created_at' => Carbon::now()
                        ]);

                        if ($estadocontrato == 8) {
                            //Estado rechazado
                            //Regresar pieza de armazon de los historiales del contrato
                            $historialesclinicos = DB::select("SELECT id_producto FROM historialclinico WHERE id_contrato = '$idContrato' ORDER BY created_at DESC");
                            if ($historialesclinicos != null) {
                                //Existen historiales
                                foreach ($historialesclinicos as $historialclinico) {
                                    DB::update("UPDATE producto
                                    SET piezas = piezas - 1,
                                    updated_at = '" . Carbon::now() . "'
                                    WHERE id = '" . $historialclinico->id_producto . "'");
                                }
                            }
                        }

                        //Guardar en tabla historialcontrato
                        $usuarioId = Auth::user()->id;
                        DB::table('historialcontrato')->insert([
                            'id' => $this->getHistorialContratoId(), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                            'cambios' => "Se restauro el estatus del contrato de '$mensaje'"
                        ]);

                        $contratosGlobal = new contratosGlobal;

                        //Validacion de si es garantia o no
                        $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1 ORDER BY created_at LIMIT 1");

                        switch ($estadoactualizar) {
                            case 0:
                            case 1:
                            case 9:
                                //NO TERMINADO, TERMINADO, EN PROCESO DE APROBACION
                                if ($tieneHistorialGarantia != null) {
                                    //Tiene historial con garantia
                                    $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($id_zona); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona

                                    if ($cobradoresAsignadosAZona != null) {
                                        //Existen cobradores
                                        foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                            //Recorrido cobradores
                                            $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $cobradorAsignadoAZona->id);

                                            $abonos = DB::select("SELECT * FROM abonos WHERE id_contrato ='$idContrato' ORDER BY created_at DESC");
                                            //Insertar abonos en tabla abonoscontratostemporalessincronizacion
                                            foreach ($abonos as $abono) {
                                                //Recorrido abonos
                                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
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
                                    }
                                }else {
                                    //No tiene historiales con garantia
                                    $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $id_usuariocreacion);
                                }
                                break;
                            case 2:
                            case 4:
                            case 12:
                                //ENTREGADO, ABONO ATRASADO, ENVIADO
                                $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($id_zona); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona

                                if ($cobradoresAsignadosAZona != null) {
                                    //Existen cobradores
                                    foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                        //Recorrido cobradores
                                        $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $cobradorAsignadoAZona->id);

                                        $abonos = DB::select("SELECT * FROM abonos WHERE id_contrato ='$idContrato' ORDER BY created_at DESC");
                                        //Insertar abonos en tabla abonoscontratostemporalessincronizacion
                                        foreach ($abonos as $abono) {
                                            //Recorrido abonos
                                            //Insertar en tabla abonoscontratostemporalessincronizacion
                                            DB::table("abonoscontratostemporalessincronizacion")->insert([
                                                "id_usuariocobrador" => $cobradorAsignadoAZona->id,
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
                                }
                                break;
                        }

                        //Actualizar contrato en tabla contratoslistatemporales
                        $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);
                        //ELiminar contrato de tabla contratosliofuga
                        $contratosGlobal::insertarEliminarContratosLioFuga($idContrato, "",1);

                        return back()->with("bien", "El estatus del contrato se actualizo correctamente");

                    }

                    return back()->with('alerta', 'No se puede restablecer el contrato, debe estar en estado de CANCELADO o RECHAZADO.');

                }
                return back()->with('alerta', 'No se encontro el contrato.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function eliminardiatemporal($idFranquicia, $idContrato)
    {
        if ((Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)) {

            try {

                $contrato = DB::select("SELECT diatemporal FROM contratos WHERE id= '$idContrato'");

                if($contrato != null) {
                    //Existe el contrato

                    $diatemporal = $contrato[0]->diatemporal;

                    if ($diatemporal != null) {
                        //Tiene un diatemporal en el contrato

                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'diatemporal' => null
                        ]);

                        DB::table('historialcontrato')->insert([
                            'id' => $this->getHistorialContratoId(), 'id_usuarioC' => Auth::user()->id,
                            'id_contrato' => $idContrato, 'created_at' => Carbon::now(), 'cambios' => "Se elimino el dia temporal con fecha: " . $diatemporal
                        ]);

                        return back()->with('bien', 'Se elimino correctamente el dia temporal.');

                    }
                    return back()->with('alerta', 'No se tiene un dia temporal en el contrato.');

                }
                return back()->with('alerta', 'No se encontro el contrato.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function solicitarautorizaciontraspasocontratolaboratorio($idFranquicia, $idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) {
            //ROL DE DIRECTOR - ADMINISTRADOR

            $idUsuario = Auth::user()->id;

            $mensaje = $request->input("mensaje");

            //Validaciones de campo mensaje
            if (strlen($mensaje) == 0) {
                return back()->with('alerta', 'Ingresa el motivo de traspaso.');
            }

            $existeContrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato' AND c.id_franquicia = '$idFranquicia'");

            if ($existeContrato != null) {
                //Si existe el contrato

                if ($existeContrato[0]->estatus_estadocontrato == 7 || $existeContrato[0]->estatus_estadocontrato == 10 || $existeContrato[0]->estatus_estadocontrato == 11) {
                    //El contrato tiene estatus  APROBADO, MANUFACTURA, PROCESO DE ENVIO

                    try {
                        //Insertamos valores de peticion y movimiento

                        //Generamos ID alfanumero de identificacion de mensaje y movimiento
                        $globalesServicioWeb = new globalesServicioWeb;
                        $idHistorialContratoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');

                        //Insertar solicitud de autorizacion
                        DB::table('autorizaciones')->insert([
                            'id_contrato' => $idContrato, 'id_usuarioC' => $idUsuario, 'id_franquicia' => $idFranquicia,
                            'fechacreacioncontrato' => $existeContrato[0]->created_at,
                            'estadocontrato' => $existeContrato[0]->estatus_estadocontrato,
                            'mensaje' => "Solicitó autorizacion con el siguiente mensaje: '$mensaje'",
                            'estatus' => '0', 'tipo' => '6', 'created_at' => Carbon::now()
                        ]);

                        //Insertamos el movimiento con su respectivo mensaje de solicitud
                        DB::table('historialcontrato')->insert([
                            'id' => $idHistorialContratoAlfanumerico, 'id_usuarioC' => $idUsuario, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                            'cambios' => "Solicitó autorizacion para traspaso de sucursal con el siguiente mensaje: '$mensaje'", 'tipomensaje' => '3']);

                        return back()->with('bien', 'Solicitud de traspasar contrato generada correctamente');

                    } catch (Exception $e) {
                        \Log::info("Error: " . $e->getMessage());
                        return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina   Error:' . $e->getMessage());
                    }
                } else {
                    return back()->with('alerta', 'No puedes acceder al contrato debido a su estatus actual.');
                }
            } else {
                return back()->with('alerta', 'Contrato no existente, verifica el codigo del contrato.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public static function  insertarHistorialSucursal($idFranquicia, $idUsuarioC, $cambio){
        DB::table('historialsucursal')->insert([
            'id_usuarioC' => $idUsuarioC, 'id_franquicia' => $idFranquicia,
            'tipomensaje' => '7', 'created_at' => Carbon::now(), 'cambios' => $cambio, 'seccion' => '1'
        ]);
    }

    public function traspasarcontratozona($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) {
            //Rol administrador, director, principal

            $hoy = Carbon::now();
            $hoyNumero = $hoy->dayOfWeekIso; // Comienza en lunes -> 1 y obtenemos el dia actual de la semana

            $arrayObjetos = array();

            $franquiciaSeleccionada = $idFranquicia;
            $zonaSeleccionada = '1';

            array_push($arrayObjetos, $franquiciaSeleccionada);
            array_push($arrayObjetos, $zonaSeleccionada);

            $arrayRespuesta = self::obtenerListaContratosTraspasoContratoZona($arrayObjetos, false);

            $idFranquicia = $arrayRespuesta[0];
            $franquicias = $arrayRespuesta[1];
            $zonasPrincipal = $arrayRespuesta[2];

            return view('administracion.contrato.traspasarcontratozona', [
                'franquicias' => $franquicias,
                'franquiciaSeleccionada' => $franquiciaSeleccionada,
                'zonaSeleccionada' => $zonaSeleccionada,
                'idFranquicia' => $idFranquicia,
                'zonasPrincipal' => $zonasPrincipal,
                'hoyNumero' => $hoyNumero
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function traspasarcontratozonatiemporeal(Request $request)
    {

        $hoy = Carbon::now();
        $hoyNumero = $hoy->dayOfWeekIso; // Comienza en lunes -> 1 y obtenemos el dia actual de la semana

        $arrayObjetos = array();

        $franquiciaSeleccionada = $request->input('franquiciaSeleccionada');
        $zonaSeleccionada = $request->input('zonaSeleccionada');

        array_push($arrayObjetos, $franquiciaSeleccionada);
        array_push($arrayObjetos, $zonaSeleccionada);

        $arrayRespuesta = self::obtenerListaContratosTraspasoContratoZona($arrayObjetos, true);

        $idFranquicia = $arrayRespuesta[0];
        $franquicias = $arrayRespuesta[1];
        $cuentasColonia = $arrayRespuesta[2];
        $zonas = $arrayRespuesta[3];
        $zonaSeleccionada = $arrayRespuesta[4];
        $zonasPrincipal = $arrayRespuesta[5];

        $view = view('administracion.contrato.listas.listatraspasarcontratozona', [
            'franquicias' => $franquicias,
            'franquiciaSeleccionada' => $franquiciaSeleccionada,
            'zonaSeleccionada' => $zonaSeleccionada,
            'idFranquicia' => $idFranquicia,
            'cuentasColonia' => $cuentasColonia,
            'zonas' => $zonas,
            'zonasPrincipal' => $zonasPrincipal,
            'hoyNumero' => $hoyNumero
        ])->render();

        return \Response::json(array("valid"=>"true","view"=>$view));
    }

    private static function obtenerListaContratosTraspasoContratoZona($arrayObjetos, $cargarLista)
    {

        $arrayRespuesta = array();
        $idUsuario = Auth::id();

        $franquicias = null;
        $idFranquicia = null;
        $zonasPrincipal = null;

        if ($cargarLista) {
            //Cargar lista

            $franquiciaSeleccionada = null;
            if (((Auth::user()->rol_id) == 7)) {
                //Director
                $franquiciaSeleccionada = $arrayObjetos[0];
            }
            $zonaSeleccionada = $arrayObjetos[1];

            $cadenaFranquiciaSeleccionada = " ";
            if ($franquiciaSeleccionada != null) {
                $cadenaFranquiciaSeleccionada = " WHERE c.id_franquicia = '$franquiciaSeleccionada'";
            }

            $cadenaZona = " ";
            if ($zonaSeleccionada != null) {
                if ($franquiciaSeleccionada != null) {
                    $cadenaZona = " AND c.id_zona = (SELECT z.id FROM zonas z WHERE z.zona = '$zonaSeleccionada' AND z.id_franquicia = '$franquiciaSeleccionada')";
                } else {
                    $cadenaZona = " AND c.id_zona = (SELECT z.id FROM zonas z WHERE z.zona = '$zonaSeleccionada' AND z.id_franquicia = c.id_franquicia)";
                }
            }

            $contratosTodos = null;

            if (((Auth::user()->rol_id) == 7)) {
                //Director
                $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f WHERE id != '00000'");
                $zonas = DB::select("SELECT z.id AS id, z.zona AS zona FROM zonas z WHERE z.id_franquicia = '$franquiciaSeleccionada'
                                                    AND z.zona != '$zonaSeleccionada'");
                $zonasPrincipal = DB::select("SELECT z.zona AS zona FROM zonas z WHERE z.id_franquicia = '$franquiciaSeleccionada'");
                $query = "SELECT UPPER(c.colonia) AS COLONIA
                                 FROM contratos c
                                 " . $cadenaFranquiciaSeleccionada . "
                                 " . $cadenaZona . "
                                 ORDER BY c.created_at DESC";
                $contratosTodos = DB::select($query);

            } else {
                //Principal o administrador
                $contratosGlobal = new contratosGlobal;
                $idFranquicia = $contratosGlobal::obtenerIdFranquiciaUsuario($idUsuario);
                $zonas = DB::select("SELECT z.id AS id, z.zona AS zona FROM zonas z WHERE z.id_franquicia = '$idFranquicia'
                                                AND z.zona != '$zonaSeleccionada'");
                $zonasPrincipal = DB::select("SELECT z.zona AS zona FROM zonas z WHERE z.id_franquicia = '$idFranquicia'");
                $query = "SELECT UPPER(c.colonia) AS COLONIA
                                 FROM contratos c
                                 WHERE c.id_franquicia = '$idFranquicia'
                                 " . $cadenaZona . "
                                 ORDER BY c.created_at DESC;";
                $contratosTodos = DB::select($query);
            }

            $cuentasColonia = array();
            foreach ($contratosTodos as $contrato) {
                //Recorremos todos los contratos
                //Obtener colonias
                if (array_key_exists($contrato->COLONIA, $cuentasColonia)) {
                    //Existe la llave de la colonia
                    $cuentasColonia[$contrato->COLONIA] = $cuentasColonia[$contrato->COLONIA] + 1;
                } else {
                    //No se encontro la llave de la colonia
                    $cuentasColonia[$contrato->COLONIA] = 1;
                }
            }

            array_push($arrayRespuesta, $idFranquicia);
            array_push($arrayRespuesta, $franquicias);
            array_push($arrayRespuesta, $cuentasColonia);
            array_push($arrayRespuesta, $zonas);
            array_push($arrayRespuesta, $zonaSeleccionada);
            array_push($arrayRespuesta, $zonasPrincipal);

        }else {
            //No cargar lista

            if (((Auth::user()->rol_id) == 7)) {
                //Director
                $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f WHERE id != '00000'");
                $zonasPrincipal = DB::select("SELECT z.zona AS zona FROM zonas z WHERE z.id_franquicia = '$arrayObjetos[0]'");
            }else {
                //Admin/principal
                $contratosGlobal = new contratosGlobal;
                $idFranquicia = $contratosGlobal::obtenerIdFranquiciaUsuario($idUsuario);
                $zonasPrincipal = DB::select("SELECT z.zona AS zona FROM zonas z WHERE z.id_franquicia = '$idFranquicia'");
            }

            array_push($arrayRespuesta, $idFranquicia);
            array_push($arrayRespuesta, $franquicias);
            array_push($arrayRespuesta, $zonasPrincipal);

        }

        return $arrayRespuesta;

    }

    public function actualizartraspasarcontratozona($idFranquicia, $zonaSeleccionada)
    {

        $hoy = Carbon::now();
        $hoyNumero = $hoy->dayOfWeekIso; // Comienza en lunes -> 1 y obtenemos el dia actual de la semana

        if ($hoyNumero != 1) {
            //Dia es diferente de lunes
            return back()->with('alerta', 'Solo se pueden realizar traspasos los días lunes.');
        }else {
            //Dia es igual a lunes

            $zonaTraspasoSeleccionada = request('zonaTraspasoSeleccionada');
            $contratosGlobal = new contratosGlobal;

            $query = "SELECT UPPER(c.colonia) AS COLONIA
                         FROM contratos c
                         WHERE c.id_franquicia = '$idFranquicia'
                         AND c.id_zona = (SELECT z.id FROM zonas z WHERE z.zona = '$zonaSeleccionada' AND z.id_franquicia = '$idFranquicia')
                         ORDER BY c.created_at DESC;";
            $contratosTodos = DB::select($query);

            $cuentasColonia = array();
            foreach ($contratosTodos as $contrato) {
                //Recorremos todos los contratos
                //Obtener colonias
                if (array_key_exists($contrato->COLONIA, $cuentasColonia)) {
                    //Existe la llave de la colonia
                    $cuentasColonia[$contrato->COLONIA] = $cuentasColonia[$contrato->COLONIA] + 1;
                } else {
                    //No se encontro la llave de la colonia
                    $cuentasColonia[$contrato->COLONIA] = 1;
                }
            }

            $mensajeColoniasActualizadasZona = " ";
            foreach ($cuentasColonia as $i => $cuentaColonia) {
                //Recorremos las colonias
                $coloniaSinEspacios = str_replace(' ', '', $i);
                $coloniaSinEspacios = str_replace('.', '', $coloniaSinEspacios);
                $coloniaEntrante = request('check' . $coloniaSinEspacios);

                if ($coloniaEntrante != null) {

                    $mensajeColoniasActualizadasZona = $mensajeColoniasActualizadasZona . $i . ",";

                    $contratosActualizar = DB::select("SELECT c.id, c.estatus_estadocontrato, c.id_zona FROM contratos c WHERE c.id_franquicia = '$idFranquicia'
                                                    AND c.id_zona = (SELECT z.id FROM zonas z WHERE z.zona = '$zonaSeleccionada' AND z.id_franquicia = '$idFranquicia')
                                                    AND REPLACE(REPLACE(c.colonia, ' ', ''), '.', '') = '$coloniaSinEspacios'");

                    foreach ($contratosActualizar as $contratoActualizar) {
                        //Validacion de si es garantia o no

                        $idContrato = $contratoActualizar->id;

                        $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($contratoActualizar->id_zona);
                        foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                            //Recorrido de cobradores de la zona anterior
                            //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato y idCobrador
                            DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato' AND id_usuario = '" . $cobradorAsignadoAZona->id . "'");
                            //Eliminar registros de la tabla abonoscontratostemporalessincronizacion que contengan ese idContrato y idCobrador
                            DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_contrato = '$idContrato' AND id_usuariocobrador = '" . $cobradorAsignadoAZona->id . "'");
                        }

                        //Actualizamos el contrato en tabla contratos
                        DB::update("UPDATE contratos SET id_zona = '$zonaTraspasoSeleccionada'
                                        WHERE id_franquicia = '$idFranquicia' AND id = '$idContrato'");

                        //Actualizar contrato tabla contratoslistatemporales
                        $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                        //Validar si aun existe un registro del contrato en la tabla contratostempralessincronizacion
                        $existeContratoTemporalSincronizacion = DB::select("SELECT id FROM contratostemporalessincronizacion WHERE id = '$idContrato'");

                        if ($existeContratoTemporalSincronizacion != null) {
                            //Existe uno o mas registros del contrato
                            //Actualizamos el/los contratos en tabla contratostemporalessincronizacion
                            DB::update("UPDATE contratostemporalessincronizacion SET id_zona = '$zonaTraspasoSeleccionada'
                                        WHERE id = '$idContrato'");
                        }

                        //Validacion de garantia
                        $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1 ORDER BY created_at LIMIT 1");

                        switch ($contratoActualizar->estatus_estadocontrato) {
                            case 1:
                            case 9:
                            case 7:
                            case 10:
                            case 11: //TERMINADO, EN PROCESO DE APROBACION, APROBADO, MANOFACTURA, EN PROCESO DE ENVIO
                                //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                                if ($tieneHistorialGarantia != null) {
                                    //Tiene garantia
                                    //Validacion para cobradores
                                    $contratosGlobal::insertarCobradoresAsignadosZona($idContrato, $zonaTraspasoSeleccionada);
                                    $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($zonaTraspasoSeleccionada); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona
                                    foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                        //Recorrido cobradores
                                        $abonos = DB::select("SELECT * FROM abonos WHERE id_contrato = '$idContrato' ORDER BY created_at DESC");
                                        //Insertar abonos en tabla abonoscontratostemporalessincronizacion
                                        foreach ($abonos as $abono) {
                                            //Recorrido abonos
                                            //Insertar en tabla abonoscontratostemporalessincronizacion
                                            DB::table("abonoscontratostemporalessincronizacion")->insert([
                                                "id_usuariocobrador" => $cobradorAsignadoAZona->id,
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
                                }
                                break;
                            case 12:
                            case 2:
                            case 4:
                                $contratosGlobal::insertarCobradoresAsignadosZona($idContrato, $zonaTraspasoSeleccionada);
                                $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($zonaTraspasoSeleccionada); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona
                                foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                    //Recorrido cobradores
                                    $abonos = DB::select("SELECT * FROM abonos WHERE id_contrato = '$idContrato' ORDER BY created_at DESC");
                                    //Insertar abonos en tabla abonoscontratostemporalessincronizacion
                                    foreach ($abonos as $abono) {
                                        //Recorrido abonos
                                        //Insertar en tabla abonoscontratostemporalessincronizacion
                                        DB::table("abonoscontratostemporalessincronizacion")->insert([
                                            "id_usuariocobrador" => $cobradorAsignadoAZona->id,
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
                                break;
                        }
                    }
                }
            }

            $nombreZonaTraspasoSeleccionada = DB::select("SELECT zona FROM zonas WHERE id = '$zonaTraspasoSeleccionada'");
            if ($nombreZonaTraspasoSeleccionada != null) {
                //Existe id_zona y se obtiene nombre de la zona
                $nombreZonaTraspasoSeleccionada = $nombreZonaTraspasoSeleccionada[0]->zona;
            } else {
                //No existe id_zona y se deja el id_zona
                $nombreZonaTraspasoSeleccionada = $zonaTraspasoSeleccionada . " (no se encontro nombrezona)";
            }

            $mensajeColoniasActualizadasZona = substr_replace($mensajeColoniasActualizadasZona, '', -1);

            //Registrar movimiento en historialsucursal
            DB::table('historialsucursal')->insert([
                'id_usuarioC' => Auth::user()->id, 'id_franquicia' => $idFranquicia,
                'tipomensaje' => '0', 'created_at' => Carbon::now(),
                'cambios' => "Se cambiaron los contratos de la colonia - " . $mensajeColoniasActualizadasZona . " de la zona " . $zonaSeleccionada
                    . " a " . $nombreZonaTraspasoSeleccionada,
                'seccion' => '1'
            ]);

            return redirect()->route('traspasarcontratozona', ['idFranquicia' => $idFranquicia])
                ->with('bien', 'Se actualizaron los contratos de zona correctamente');

        }

    }

    public function zonastraspasarcontratozona(Request $request){
        if (((Auth::user()->rol_id) == 7)) {
            //Rol director

            $franquiciaSeleccionada = $request->input('franquiciaSeleccionada');

            $zonasPrincipal = DB::select("SELECT z.zona AS zona FROM zonas z WHERE z.id_franquicia = '$franquiciaSeleccionada'");

            $response = ['data' => $zonasPrincipal];
            return response()->json($response);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function solicitarautorizacionabonominimo($idFranquicia, $idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL - CONFIRMACIONES
            $idUsuario = Auth::user()->id;

            $mensaje = $request->input("mensaje");

            //Validaciones de campo mensaje
            request()->validate([
                'mensaje' => 'required|string|min:1'
            ]);

            $existeContrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato' AND c.id_franquicia = '$idFranquicia'");

            if ($existeContrato != null) {
                //Si existe el contrato

                if ($existeContrato[0]->estatus_estadocontrato == 0 || $existeContrato[0]->estatus_estadocontrato == 2 || $existeContrato[0]->estatus_estadocontrato == 4 ||
                $existeContrato[0]->estatus_estadocontrato == 12) {
                    //El contrato tiene estatus NO TERMINADO, ENTREGADO, ATRASADO, ENVIADO

                    try {
                        //Insertamos valores de peticion y movimiento

                        //Insertar solicitud de autorizacion
                        DB::table('autorizaciones')->insert([
                            'id_contrato' => $idContrato, 'id_usuarioC' => $idUsuario, 'id_franquicia' => $idFranquicia,
                            'fechacreacioncontrato' => $existeContrato[0]->created_at,
                            'estadocontrato' => $existeContrato[0]->estatus_estadocontrato,
                            'mensaje' => "Solicitó autorizacion con el siguiente mensaje: '$mensaje'",
                            'estatus' => '0', 'tipo' => '7', 'created_at' => Carbon::now()
                        ]);

                        return back()->with('bien', 'Solicitud de abono minimo generada correctamente');

                    } catch (Exception $e) {
                        \Log::info("Error: " . $e->getMessage());
                        return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina   Error:' . $e->getMessage());
                    }
                } else {
                    return back()->with('alerta', 'No puedes acceder al contrato debido a su estatus actual.');
                }
            } else {
                return back()->with('alerta', 'Contrato no existente, verifica el ID CONTRATO.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function agregarabonominimo($idFranquicia, $idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL - CONFIRMACIONES
            $idUsuario = Auth::user()->id;

            $abonoMinimo = $request->input("abonoMinimo");

            //Validaciones de campo abono minimo
            if($abonoMinimo < 150){
                return back()->with('alerta', "El abono minimo debe ser mayor de $150 pesos");
            }

            request()->validate([
                'abonoMinimo' => 'required|integer'
            ]);

            $existeContrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato' AND c.id_franquicia = '$idFranquicia'");

            if ($existeContrato != null) {
                //Si existe el contrato

                if ($existeContrato[0]->estatus_estadocontrato == 0 || $existeContrato[0]->estatus_estadocontrato == 2 || $existeContrato[0]->estatus_estadocontrato == 4 ||
                    $existeContrato[0]->estatus_estadocontrato == 12) {
                    //El contrato tiene estatus NO TERMINADO, ENTREGADO, ATRASADO, ENVIADO

                    try {
                        //Actualizamos abono minimo en contratos y contratos temporales sincronizacion
                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])
                            ->update([
                            'abonominimo' => $abonoMinimo
                        ]);

                        $contratosGlobal = new contratosGlobal;
                        $contratosGlobal::actualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato);

                        //Insertar registro en tabla abonominimocontratos
                        $contratosGlobal::insertarActualizarTablaAbonoMinimoContratos($idContrato, $existeContrato[0]->id_zona, $abonoMinimo);

                        if($idUsuario != 1 && $idUsuario != 61 && $idUsuario != 761){
                            //Cambiar estatus de solicitud a geenrado
                            $solicitudAbono = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato = '$idContrato' AND a.tipo = 7
                                                                AND a.estatus = 1 ORDER BY a.created_at DESC LIMIT 1");
                            if($solicitudAbono != null){
                                DB::table('autorizaciones')->where([['indice', '=', $solicitudAbono[0]->indice], ['id_contrato', '=', $idContrato]])->update([
                                    'estatus' => '3', 'updated_at' => Carbon::now()
                                ]);
                            }
                        }

                        return back()->with('bien', 'Abono minimo actualizado correctamente.');

                    } catch (Exception $e) {
                        \Log::info("Error: " . $e->getMessage());
                        return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina   Error:' . $e->getMessage());
                    }
                } else {
                    return back()->with('alerta', 'No puedes acceder al contrato debido a su estatus actual.');
                }
            } else {
                return back()->with('alerta', 'Contrato no existente, verifica el ID CONTRATO.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    //Funcion: actualizarTablaAutorizacionesNuevosCampos
    //Descripcion: Creara la actualizacion de la tabla autorizaciones con los nuevos campos agregados (Solo se ejecuta una vez)
    public function actualizarTablaAutorizacionesNuevosCampos(){

        //Llenar columna fecha creacion contrato
        DB::update("UPDATE autorizaciones AS a SET fechacreacioncontrato = (SELECT c.created_at FROM contratos c WHERE a.id_contrato = c.id)");

        //Llenar columna estado contrato
        DB::update("UPDATE autorizaciones AS a SET a.estadocontrato = (SELECT c.estatus_estadocontrato FROM contratos c WHERE a.id_contrato = c.id)");

        //Llenar columna franquicia
        DB::update("UPDATE autorizaciones AS a SET a.id_franquicia = (SELECT c.id_franquicia FROM contratos c WHERE a.id_contrato = c.id)");

        //Llenar columna mensaje
        DB::update("UPDATE autorizaciones AS a SET a.mensaje = (SELECT hc.cambios FROM historialcontrato hc WHERE a.id_mensaje = hc.id)");

    }

    public function actualizarpagosadelantarcontrato($idFranquicia, $idContrato)
    {

        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {

            try {

                $contrato = DB::select("SELECT estatus_estadocontrato, pago, diapago FROM contratos WHERE id = '$idContrato' AND id_franquicia = '$idFranquicia'");

                if ($contrato != null) {
                    //Existe contrato

                    //Validacion de si es garantia o no
                    $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1 ORDER BY created_at LIMIT 1");

                    if ($contrato[0]->estatus_estadocontrato == 12 || $contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 4
                        || (($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9
                                || $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11) && $tieneHistorialGarantia != null)) {
                        //Estado es ENVIADO, ENTREGADO, ABONO ATRASADO o TERMINADO, APROBADO, EN PROCESO DE APROBACION, MANUFACTURA, PROCESO DE ENVIO con garantia

                        $pagosadelantar = request("pagosadelantar");

                        $pagosadelantar = $pagosadelantar == null ? 0 : 1;
                        $mensaje = $pagosadelantar == null ? 'desactivo' : 'activo';

                        if (($contrato[0]->estatus_estadocontrato == 2 || $tieneHistorialGarantia != null) && $pagosadelantar == 0) {
                            //ENTREGADO o tiene garantia y deshabilitan el checkbox

                            $calculofechaspago = new calculofechaspago;
                            $arrayRespuesta = $calculofechaspago::obtenerFechasPeriodoActualOPeriodoSiguiente(Carbon::now(), $contrato[0]->pago, true);
                            $fechaCobroIniActualizar = $arrayRespuesta[0];
                            $fechaCobroFinActualizar = $arrayRespuesta[1];

                            //OBTENER DIASELECCIONADO
                            $fechaDiaSeleccionadoActualizar = null;
                            if(strlen($contrato[0]->diapago) > 0) {
                                //Se tiene un dia de pago
                                $fechaDiaSeleccionadoActualizar = $calculofechaspago::obtenerDiaSeleccionado($contrato[0]->diapago, $fechaCobroIniActualizar, $fechaCobroFinActualizar);
                            }

                            DB::table("contratos")->where("id_franquicia", "=", $idFranquicia)->where("id", "=", $idContrato)->update([
                                "pagosadelantar" => $pagosadelantar,
                                "fechacobroini" => $fechaCobroIniActualizar,
                                "fechacobrofin" => $fechaCobroFinActualizar,
                                "diaseleccionado" => $fechaDiaSeleccionadoActualizar
                            ]);

                        }else {
                            //Estado diferente a ENTREGADO o no tiene garantia
                            DB::table("contratos")->where("id_franquicia", "=", $idFranquicia)->where("id", "=", $idContrato)->update([
                                "pagosadelantar" => $pagosadelantar
                            ]);
                        }

                        //Registrar el movimiento al contrato
                        $globalesServicioWeb = new globalesServicioWeb;
                        DB::table('historialcontrato')->insert([
                            'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => Auth::user()->id,
                            'id_contrato' => $idContrato, 'created_at' => Carbon::now(), 'cambios' => "Se $mensaje el adelanto de abonos correctamente"
                        ]);

                        return back()->with("bien", "Se $mensaje el adelanto de abonos correctamente.");

                    }
                    //Estado es diferente a ENTREGADO o ABONO ATRASADO
                    return back()->with("alerta", "No se puede activar/desactivar adelanto de abonos en el contrato en este momento.");

                }
                //No existe el contrato
                return back()->with("alerta", "El contrato no existe.");

            } catch (Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina   Error:' . $e->getMessage());
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizardiagnosticoeditarcontrato($idFranquicia, $idContrato)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {

            $contratosGlobal = new contratosGlobal;

            $edad = request('edad');
            $diagnostico = $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('diagnostico'));
            $ocupacion = $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('ocupacion'));
            $diabetes = $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('diabetes'));
            $hipertension = $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('hipertension'));
            $embarazada = $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('embarazada'));
            $durmioseisochohoras = $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('durmioseisochohoras'));
            $actividaddia = $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('actividaddia'));
            $problemasojos = $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('problemasojos'));
            $dolor = request('dolor');
            $golpeojos = request('golpeojos');
            $ardor = request('ardor');
            $otroM = request('otroM');
            $molestiaotro = request('molestiaotro');
            $ultimoexamen = request('ultimoexamen');

            //Valores checkBox
            $dolor = ($dolor != null)? $dolor = 1: $dolor = 0;
            $ardor = ($ardor != null)? $ardor = 1: $ardor = 0;
            $golpeojos = ($golpeojos != null)? $golpeojos = 1: $golpeojos = 0;
            $otroM = ($otroM != null)? $otroM = 1: $otroM = 0;

            //Si se selecciono Otra Molestia
            if($otroM == 1){
                //Si se selecciona es obligatorio llenar la opcion de otro
                if (strlen($molestiaotro) == 0) {
                    //Campo molestia es igual a vacio
                    return back()->with("alerta"," Campo otro molestia obligatorio.");
                }
            }else{
                //Si no esta seleccionado -> vaciar el campo de otra molestia para evitar datos erroneos
                $molestiaotro = null;
            }

            $existeContrato = DB::select("SELECT * FROM contratos WHERE id ='$idContrato' AND id_franquicia = '$idFranquicia'");

            if($existeContrato != null){
                //Existe el contrato

                $existeDiagnostico = DB::select("SELECT * FROM historialclinico hc WHERE hc.id_contrato = '$idContrato'");

                if($existeDiagnostico != null){
                    //Si tiene diagnostico

                    try {

                        //Actualizar diagnosticos del contrato
                        DB::table("historialclinico")
                            ->where("id_contrato", "=", $idContrato)
                            ->update([
                                'edad' => $edad,
                                'diagnostico' => $diagnostico,
                                'ocupacion' => $ocupacion,
                                'diabetes' => $diabetes,
                                'hipertension' => $hipertension,
                                'dolor' => $dolor,
                                'ardor' => $ardor,
                                'golpeojos' => $golpeojos,
                                'otroM' => $otroM,
                                'molestiaotro' => $molestiaotro,
                                'ultimoexamen' => $ultimoexamen,
                                'embarazada' => $embarazada,
                                'durmioseisochohoras' => $durmioseisochohoras,
                                'actividaddia' => $actividaddia,
                                'problemasojos' => $problemasojos,
                                'updated_at' => Carbon::now()
                            ]);

                        //Registrar el movimiento al contrato
                        $globalesServicioWeb = new globalesServicioWeb;
                        $usuarioId = Auth::user()->id;

                        //Registrar movimiento
                        $camposActualizados = "";
                        if($existeDiagnostico[0]->edad != $edad){
                            //Actualizo edad
                            $camposActualizados = "Edad, ";
                        }
                        if($existeDiagnostico[0]->diagnostico != $diagnostico){
                            //Actualizo diagnostico
                            $camposActualizados = $camposActualizados . "Diagnostico, ";
                        }
                        if($existeDiagnostico[0]->ocupacion != $ocupacion){
                            //Actualizo Ocupacion
                            $camposActualizados = $camposActualizados . "Ocupación, ";
                        }
                        if($existeDiagnostico[0]->diabetes != $diabetes){
                            //Actualizo diabetes
                            $camposActualizados = $camposActualizados . "Diabetes, ";
                        }
                        if($existeDiagnostico[0]->hipertension != $hipertension){
                            //Actualizo hipertencion
                            $camposActualizados = $camposActualizados . "Hipertención, ";
                        }
                        if($existeDiagnostico[0]->embarazada != $embarazada){
                            //Actualizo embarazada
                            $camposActualizados = $camposActualizados . "Embarazo, ";
                        }
                        if($existeDiagnostico[0]->durmioseisochohoras != $durmioseisochohoras){
                            //Actualizo horas de sueño
                            $camposActualizados = $camposActualizados . "Horas de sueño, ";
                        }
                        if($existeDiagnostico[0]->actividaddia != $actividaddia){
                            //Actualizo actividad del dia
                            $camposActualizados = $camposActualizados . "Principal actividad del dia, ";
                        }
                        if($existeDiagnostico[0]->problemasojos != $problemasojos){
                            //Actualizo actividad del dia
                            $camposActualizados = $camposActualizados . "Principal problema, ";
                        }
                        if($existeDiagnostico[0]->dolor != $dolor){
                            //Actualizo dolor de cabeza
                            if(str_contains($camposActualizados, "Molestia")){
                                $camposActualizados = $camposActualizados . "Dolor de cabeza, ";
                            }else{
                                $camposActualizados = $camposActualizados . "Molestias: Dolor de cabeza, ";
                            }
                        }
                        if($existeDiagnostico[0]->ardor != $ardor){
                            //Actualizo ardor
                            if(str_contains($camposActualizados, "Molestia")){
                                $camposActualizados = $camposActualizados . "Ardor en los ojos, ";
                            }else{
                                $camposActualizados = $camposActualizados . "Molestias: Ardor en los ojos, ";
                            }
                        }
                        if($existeDiagnostico[0]->golpeojos != $golpeojos){
                            //Actualizo golpe
                            if(str_contains($camposActualizados, "Molestia")){
                                $camposActualizados = $camposActualizados . "Golpe en los ojos, ";
                            }else{
                                $camposActualizados = $camposActualizados . "Molestias: Golpe en los ojos, ";
                            }
                        }
                        if($existeDiagnostico[0]->otroM != $otroM){
                            //Actualizo dolor de cabeza
                            if(str_contains($camposActualizados, "Molestia")){
                                $camposActualizados = $camposActualizados . "Otro ";
                                if(strlen($molestiaotro) > 0){
                                    $camposActualizados = $camposActualizados . "(" . $molestiaotro . ")";
                                }
                            }else{
                                $camposActualizados = $camposActualizados . "Molestias: Otro ";
                                if(strlen($molestiaotro) > 0){
                                    $camposActualizados = $camposActualizados . "(" . $molestiaotro . ")";
                                }
                            }
                        }

                        $camposActualizados = trim($camposActualizados, " ");
                        $camposActualizados = trim($camposActualizados, ",");

                        if($camposActualizados != ""){
                            DB::table('historialcontrato')->insert([
                                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $usuarioId,
                                'id_contrato' => $idContrato, 'created_at' =>  Carbon::now(), 'cambios' => "Actualizo los siguientes campos del diagnostico: '" . $camposActualizados ."'"
                            ]);
                        }

                        //Actualizar datos historiales clinicos a mayusculas y quitar acentos
                        $contratosGlobal::actualizarContratoHistorialesClinicosMayusculasAcentos($idContrato, 1);

                        return back()->with("bien"," Diagnostico actualizado correctamente.");

                    } catch (\Exception $e) {
                        \Log::info("Error: " . $e->getMessage());
                        return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
                    }

                }
                //No presenta ningun diagnostico el contrato
                return back()->with("alerta"," Diagnostico no encontrado para el contrato.");

            }
            //No existe contrato
            return back()->with("alerta","No existe el contrato.");

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function obtenerprecioproductomodal(Request $request)
    {

        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 15)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL - ADMINISTRACION

            $id_producto = $request->input("id_producto");
            $opcion = $request->input("opcion");
            $piezas = $request->input("piezas");
            $alerta = false;

            $producto = DB::select("SELECT p.precio, p.preciop, p.id_tipoproducto FROM producto p WHERE p.id = '$id_producto'");

            $precioproducto = null;
            if ($producto != null) {
                //Existe producto
                $precioproducto = $producto[0]->preciop != null ? $producto[0]->preciop : $producto[0]->precio;
                if ($producto[0]->id_tipoproducto == 1) {
                    //Producto de armazon
                    if ($opcion == 1) {
                        //Poliza
                        $precioproducto = 120;
                    }elseif ($opcion == 2) {
                        //Por defecto de fábrica
                        $precioproducto = 0;
                    }
                }else {

                    if ($opcion != 0) {
                        //Se puso opcion de Con Poliza/Defecto de fabrica a un producto diferente de armazon
                        $alerta = true;
                    }
                    $precioproducto = $producto[0]->preciop != null ? $producto[0]->preciop : $producto[0]->precio;
                }
            }

            //Sacar el total con el numero de piezas
            $precioproducto = $precioproducto * $piezas;

            $response = ['precioproducto' => $precioproducto, 'alerta' => $alerta];

            return response()->json($response);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function agregarhistorialfotocontrato($idFranquicia, $idContrato){

        //Validaciones
        request()->validate([
            'fotomovimiento' => 'required|image|mimes:jpg',
            'observaciones' => 'nullable|string'
        ]);


        try{
            //Foto
            $foto = "";
            if (request()->hasFile('fotomovimiento')) {
                $fotoBruta = 'Foto-Movimiento-Contrato' . $idContrato . '-' . time() . '.' . request()->file('fotomovimiento')->getClientOriginalExtension();
                $foto = request()->file('fotomovimiento')->storeAs('uploads/imagenes/contratos/fotomovimiento', $fotoBruta, 'disco');
                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotomovimiento/' . $fotoBruta)->height();
                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotomovimiento/' . $fotoBruta)->width();
                if ($alto > $ancho) {
                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotomovimiento/' . $fotoBruta)->resize(600, 800);
                } else {
                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotomovimiento/' . $fotoBruta)->resize(800, 600);
                }
                $imagenfoto->save();
            }

            $globalesServicioWeb = new globalesServicioWeb;

            //Registrar movimeinto con fotografia para el contrato
            $id_usuarioC = Auth::id();
            DB::table('historialfotoscontrato')->insert([
                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialfotoscontrato', '5'), 'id_usuarioC' => $id_usuarioC,
                'id_contrato' => $idContrato, 'foto' => $foto,'observaciones' => request('observaciones'), 'tipomensaje' => '0','created_at' => Carbon::now()
            ]);

            //Registrar historial del contrato nuevo movimiento
            $cambios = (strlen(request('observaciones')) > 0)? "Agrego evidencia con foto con las siguientes observaciones: '". request('observaciones') ."'": "Agrego evidencia con foto y sin observaciones.";

            DB::table('historialcontrato')->insert([
                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                'id_usuarioC' => $id_usuarioC,
                'id_contrato' => $idContrato,
                'created_at' => Carbon::now(),
                'cambios' => $cambios
            ]);

        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
            return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina.');
        }

        return back()->with("bien","Evidencia del contrato registrada correctamente.");

    }

    public function migrarcuentasarchivoexcelpolizaprueba($rutaarchivoscontratos, $rutaarchivohistoriales, $rutaarchivoabonos)
    {

        //C:\Users\Usuario\Desktop\abonoslunes.xlsx

        \Log::info("Metodo migrarcuentasarchivoexcelpolizaprueba INICIO");

        //CONTRATOS
        try {

            if (strlen($rutaarchivoscontratos) > 0) {
                //$rutaarchivoscontratos diferente de vacio

                $filas = Excel::toArray(new CsvImport(), $rutaarchivoscontratos);

                foreach ($filas[0] as $key => $contrato) {

                    try {

                        $ID_CONTRATO = $contrato[0];
                        if (strlen($ID_CONTRATO) == 0) {
                            //IDCONTRATO es igual a vacio
                            $ID_CONTRATO = null;
                        }

                        $DATOS = $contrato[1];
                        if (strlen($DATOS) == 0) {
                            //DATOS es igual a vacio
                            $DATOS = null;
                        }

                        $ID_FRANQUICIA = $contrato[2];
                        if (strlen($ID_FRANQUICIA) == 0) {
                            //ID_FRANQUICIA es igual a vacio
                            $ID_FRANQUICIA = null;
                        }

                        $ID_USUARIOCREACION = $contrato[3];
                        if (strlen($ID_USUARIOCREACION) == 0) {
                            //ID_USUARIOCREACION es igual a vacio
                            $ID_USUARIOCREACION = null;
                        }

                        $NOMBRE_USUARIOCREACION = $contrato[4];
                        if (strlen($NOMBRE_USUARIOCREACION) == 0) {
                            //NOMBRE_USUARIOCREACION es igual a vacio
                            $NOMBRE_USUARIOCREACION = null;
                        }

                        $ID_ZONA = $contrato[5];
                        if (strlen($ID_ZONA) == 0) {
                            //ID_ZONA es igual a vacio
                            $ID_ZONA = null;
                        }

                        $ESTATUS = $contrato[6];
                        if (strlen($ESTATUS) == 0) {
                            //ESTATUS es igual a vacio
                            $ESTATUS = null;
                        }

                        $NOMBRE = $contrato[7];
                        if (strlen($NOMBRE) == 0) {
                            //NOMBRE es igual a vacio
                            $NOMBRE = null;
                        }

                        $CALLE = $contrato[8];
                        if (strlen($CALLE) == 0) {
                            //CALLE es igual a vacio
                            $CALLE = null;
                        }

                        $NUMERO = $contrato[9];
                        if (strlen($NUMERO) == 0) {
                            //NUMERO es igual a vacio
                            $NUMERO = null;
                        }

                        $DEPTO = $contrato[10];
                        if (strlen($DEPTO) == 0) {
                            //DEPTO es igual a vacio
                            $DEPTO = null;
                        }

                        $ALLADODE = $contrato[11];
                        if (strlen($ALLADODE) == 0) {
                            //ALLADODE es igual a vacio
                            $ALLADODE = null;
                        }

                        $FRENTEA = $contrato[12];
                        if (strlen($FRENTEA) == 0) {
                            //FRENTEA es igual a vacio
                            $FRENTEA = null;
                        }

                        $ENTRECALLES = $contrato[13];
                        if (strlen($ENTRECALLES) == 0) {
                            //ENTRECALLES es igual a vacio
                            $ENTRECALLES = null;
                        }

                        $COLONIA = $contrato[14];
                        if (strlen($COLONIA) == 0) {
                            //COLONIA es igual a vacio
                            $COLONIA = null;
                        }

                        $LOCALIDAD = $contrato[15];
                        if (strlen($LOCALIDAD) == 0) {
                            //LOCALIDAD es igual a vacio
                            $LOCALIDAD = null;
                        }

                        $TELEFONO = $contrato[16];
                        if (strlen($TELEFONO) == 0) {
                            //TELEFONO es igual a vacio
                            $TELEFONO = null;
                        }

                        $TELEFONOREFERENCIA = $contrato[17];
                        if (strlen($TELEFONOREFERENCIA) == 0) {
                            //TELEFONOREFERENCIA es igual a vacio
                            $TELEFONOREFERENCIA = null;
                        }

                        $CORREO = $contrato[18];
                        if (strlen($CORREO) == 0) {
                            //CORREO es igual a vacio
                            $CORREO = null;
                        }

                        $NOMBREREFERENCIA = $contrato[19];
                        if (strlen($NOMBREREFERENCIA) == 0) {
                            //NOMBREREFERENCIA es igual a vacio
                            $NOMBREREFERENCIA = null;
                        }

                        $CASATIPO = $contrato[20];
                        if (strlen($CASATIPO) == 0) {
                            //CASATIPO es igual a vacio
                            $CASATIPO = null;
                        }

                        $CASACOLOR = $contrato[21];
                        if (strlen($CASACOLOR) == 0) {
                            //CASACOLOR es igual a vacio
                            $CASACOLOR = null;
                        }

                        $FOTOINE = $contrato[22];
                        if (strlen($FOTOINE) == 0) {
                            //FOTOINE es igual a vacio
                            $FOTOINE = null;
                        }

                        $FOTOCASA = $contrato[23];
                        if (strlen($FOTOCASA) == 0) {
                            //FOTOCASA es igual a vacio
                            $FOTOCASA = null;
                        }

                        $COMPROBANTEDOMICILIO = $contrato[24];
                        if (strlen($COMPROBANTEDOMICILIO) == 0) {
                            //COMPROBANTEDOMICILIO es igual a vacio
                            $COMPROBANTEDOMICILIO = null;
                        }

                        $PAGARE = $contrato[25];
                        if (strlen($PAGARE) == 0) {
                            //PAGARE es igual a vacio
                            $PAGARE = null;
                        }

                        $FOTOATROS = $contrato[26];
                        if (strlen($FOTOATROS) == 0) {
                            //FOTOATROS es igual a vacio
                            $FOTOATROS = null;
                        }

                        $OBSERVACIONES = $contrato[27];
                        if (strlen($OBSERVACIONES) == 0) {
                            //OBSERVACIONES es igual a vacio
                            $OBSERVACIONES = null;
                        }

                        $NOTA = $contrato[28];
                        if (strlen($NOTA) == 0) {
                            //NOTA es igual a vacio
                            $NOTA = null;
                        }

                        $PAGOSADELANTAR = $contrato[29];
                        if (strlen($PAGOSADELANTAR) == 0) {
                            //PAGOSADELANTAR es igual a vacio
                            $PAGOSADELANTAR = null;
                        }

                        $BANDERACOMENTARIOCONFIRMACIONES = $contrato[30];
                        if (strlen($BANDERACOMENTARIOCONFIRMACIONES) == 0) {
                            //BANDERACOMENTARIOCONFIRMACIONES es igual a vacio
                            $BANDERACOMENTARIOCONFIRMACIONES = null;
                        }

                        $ESTATUSANTERIORCONTRATO = $contrato[31];
                        if (strlen($ESTATUSANTERIORCONTRATO) == 0) {
                            //ESTATUSANTERIORCONTRATO es igual a vacio
                            $ESTATUSANTERIORCONTRATO = null;
                        }

                        $DIATEMPORAL = $contrato[32];
                        if (strlen($DIATEMPORAL) == 0) {
                            //DIATEMPORAL es igual a vacio
                            $DIATEMPORAL = null;
                        }

                        $COORDENADAS = $contrato[33];
                        if (strlen($COORDENADAS) == 0) {
                            //COORDENADAS es igual a vacio
                            $COORDENADAS = null;
                        }

                        $CREATED_AT = $contrato[34];
                        if (strlen($CREATED_AT) == 0) {
                            //CREATED_AT es igual a vacio
                            $CREATED_AT = null;
                        }

                        $UPDATED_AT = $contrato[35];
                        if (strlen($UPDATED_AT) == 0) {
                            //UPDATED_AT es igual a vacio
                            $UPDATED_AT = null;
                        }

                        $ID_OPTOMETRISTA = $contrato[36];
                        if (strlen($ID_OPTOMETRISTA) == 0) {
                            //ID_OPTOMETRISTA es igual a vacio
                            $ID_OPTOMETRISTA = null;
                        }

                        $TARJETA = $contrato[37];
                        if (strlen($TARJETA) == 0) {
                            //TARJETA es igual a vacio
                            $TARJETA = null;
                        }

                        $PAGO = $contrato[38];
                        if (strlen($PAGO) == 0) {
                            //PAGO es igual a vacio
                            $PAGO = 1;
                        }

                        $ABONOMINIMO = $contrato[39];
                        if (strlen($ABONOMINIMO) == 0) {
                            //ABONOMINIMO es igual a vacio
                            $ABONOMINIMO = 200;
                        }

                        $ID_PROMOCION = $contrato[40];
                        if (strlen($ID_PROMOCION) == 0) {
                            //ID_PROMOCION es igual a vacio
                            $ID_PROMOCION = null;
                        }

                        $FOTOINEATRAS = $contrato[41];
                        if (strlen($FOTOINEATRAS) == 0) {
                            //FOTOINEATRAS es igual a vacio
                            $FOTOINEATRAS = null;
                        }

                        $TARJETAPENSIONATRAS = $contrato[42];
                        if (strlen($TARJETAPENSIONATRAS) == 0) {
                            //TARJETAPENSIONATRAS es igual a vacio
                            $TARJETAPENSIONATRAS = null;
                        }

                        $TOTAL = $contrato[43];
                        if (strlen($TOTAL) == 0) {
                            //TOTAL es igual a vacio
                            $TOTAL = null;
                        }

                        $IDCONTRATORELACION = $contrato[44];
                        if (strlen($IDCONTRATORELACION) == 0) {
                            //IDCONTRATORELACION es igual a vacio
                            $IDCONTRATORELACION = null;
                        }

                        $CONTADOR = $contrato[45];
                        if (strlen($CONTADOR) == 0) {
                            //CONTADOR es igual a vacio
                            $CONTADOR = null;
                        }

                        $TOTALHISTORIAL = $contrato[46];
                        if (strlen($TOTALHISTORIAL) == 0) {
                            //TOTALHISTORIAL es igual a vacio
                            $TOTALHISTORIAL = null;
                        }

                        $TOTALPROMOCION = $contrato[47];
                        if (strlen($TOTALPROMOCION) == 0) {
                            //TOTALPROMOCION es igual a vacio
                            $TOTALPROMOCION = null;
                        }

                        $TOTALPRODUCTO = $contrato[48];
                        if (strlen($TOTALPRODUCTO) == 0) {
                            //TOTALPRODUCTO es igual a vacio
                            $TOTALPRODUCTO = null;
                        }

                        $TOTALABONO = $contrato[49];
                        if (strlen($TOTALABONO) == 0) {
                            //TOTALABONO es igual a vacio
                            $TOTALABONO = null;
                        }

                        $TOTALREAL = $contrato[50];
                        if (strlen($TOTALREAL) == 0) {
                            //TOTALREAL es igual a vacio
                            $TOTALREAL = null;
                        }

                        $FECHAATRASO = $contrato[51];
                        if (strlen($FECHAATRASO) == 0) {
                            //FECHAATRASO es igual a vacio
                            $FECHAATRASO = null;
                        }

                        $COSTOATRASO = $contrato[52];
                        if (strlen($COSTOATRASO) == 0) {
                            //COSTOATRASO es igual a vacio
                            $COSTOATRASO = null;
                        }

                        $ULTIMOABONO = $contrato[53];
                        if (strlen($ULTIMOABONO) == 0) {
                            //ULTIMOABONO es igual a vacio
                            $ULTIMOABONO = null;
                        }

                        $ESTATUS_ESTADOCONTRATO = $contrato[54];
                        if (strlen($ESTATUS_ESTADOCONTRATO) == 0) {
                            //ESTATUS_ESTADOCONTRATO es igual a vacio
                            $ESTATUS_ESTADOCONTRATO = null;
                        }

                        $DIAPAGO = $contrato[55];
                        if (strlen($DIAPAGO) == 0) {
                            //DIAPAGO es igual a vacio
                            $DIAPAGO = null;
                        }

                        $FECHACOBROINI = $contrato[56];
                        if (strlen($FECHACOBROINI) == 0) {
                            //FECHACOBROINI es igual a vacio
                            $FECHACOBROINI = null;
                        }

                        $FECHACOBROFIN = $contrato[57];
                        if (strlen($FECHACOBROFIN) == 0) {
                            //FECHACOBROFIN es igual a vacio
                            $FECHACOBROFIN = null;
                        }

                        $FECHACOBROINIANTES = $contrato[58];
                        if (strlen($FECHACOBROINIANTES) == 0) {
                            //FECHACOBROINIANTES es igual a vacio
                            $FECHACOBROINIANTES = null;
                        }

                        $FECHACOBROFINANTES = $contrato[59];
                        if (strlen($FECHACOBROFINANTES) == 0) {
                            //FECHACOBROFINANTES es igual a vacio
                            $FECHACOBROFINANTES = null;
                        }

                        $ENGANCHE = $contrato[60];
                        if (strlen($ENGANCHE) == 0) {
                            //ENGANCHE es igual a vacio
                            $ENGANCHE = null;
                        }

                        $ENTREGAPRODUCTO = $contrato[61];
                        if (strlen($ENTREGAPRODUCTO) == 0) {
                            //ENTREGAPRODUCTO es igual a vacio
                            $ENTREGAPRODUCTO = null;
                        }

                        $DIASELECCIONADO = $contrato[62];
                        if (strlen($DIASELECCIONADO) == 0) {
                            //DIASELECCIONADO es igual a vacio
                            $DIASELECCIONADO = null;
                        }

                        $FECHAENTREGA = $contrato[63];
                        if (strlen($FECHAENTREGA) == 0) {
                            //FECHAENTREGA es igual a vacio
                            $FECHAENTREGA = null;
                        }

                        $PROMOCIONTERMINADA = $contrato[64];
                        if (strlen($PROMOCIONTERMINADA) == 0) {
                            //PROMOCIONTERMINADA es igual a vacio
                            $PROMOCIONTERMINADA = null;
                        }

                        $POLIZA = $contrato[65];
                        if (strlen($POLIZA) == 0) {
                            //POLIZA es igual a vacio
                            $POLIZA = null;
                        }

                        $FECHASUBSCRIPCION = $contrato[66];
                        if (strlen($FECHASUBSCRIPCION) == 0) {
                            //FECHASUBSCRIPCION es igual a vacio
                            $FECHASUBSCRIPCION = null;
                        }

                        $SUBSCRIPCION = $contrato[67];
                        if (strlen($SUBSCRIPCION) == 0) {
                            //SUBSCRIPCION es igual a vacio
                            $SUBSCRIPCION = null;
                        }

                        $CALLEENTREGA = $contrato[68];
                        if (strlen($CALLEENTREGA) == 0) {
                            //CALLEENTREGA es igual a vacio
                            $CALLEENTREGA = null;
                        }

                        $NUMEROENTREGA = $contrato[69];
                        if (strlen($NUMEROENTREGA) == 0) {
                            //NUMEROENTREGA es igual a vacio
                            $NUMEROENTREGA = null;
                        }

                        $DEPTOENTREGA = $contrato[70];
                        if (strlen($DEPTOENTREGA) == 0) {
                            //DEPTOENTREGA es igual a vacio
                            $DEPTOENTREGA = null;
                        }

                        $ALLADODEENTREGA = $contrato[71];
                        if (strlen($ALLADODEENTREGA) == 0) {
                            //ALLADODEENTREGA es igual a vacio
                            $ALLADODEENTREGA = null;
                        }

                        $FRENTEAENTREGA = $contrato[72];
                        if (strlen($FRENTEAENTREGA) == 0) {
                            //FRENTEAENTREGA es igual a vacio
                            $FRENTEAENTREGA = null;
                        }

                        $ENTRECALLESENTREGA = $contrato[73];
                        if (strlen($ENTRECALLESENTREGA) == 0) {
                            //ENTRECALLESENTREGA es igual a vacio
                            $ENTRECALLESENTREGA = null;
                        }

                        $COLONIAENTREGA = $contrato[74];
                        if (strlen($COLONIAENTREGA) == 0) {
                            //COLONIAENTREGA es igual a vacio
                            $COLONIAENTREGA = null;
                        }

                        $LOCALIDADENTREGA = $contrato[75];
                        if (strlen($LOCALIDADENTREGA) == 0) {
                            //LOCALIDADENTREGA es igual a vacio
                            $LOCALIDADENTREGA = null;
                        }

                        $CASATIPOENTREGA = $contrato[76];
                        if (strlen($CASATIPOENTREGA) == 0) {
                            //CASATIPOENTREGA es igual a vacio
                            $CASATIPOENTREGA = null;
                        }

                        $CASACOLORENTREGA = $contrato[77];
                        if (strlen($CASACOLORENTREGA) == 0) {
                            //CASACOLORENTREGA es igual a vacio
                            $CASACOLORENTREGA = null;
                        }

                        $consultacontrato = DB::select("SELECT indice FROM contratos WHERE id = '$ID_CONTRATO'");

                        if ($consultacontrato == null) {
                            //No existe contrato

                            //Crear contrato
                            DB::table("contratos")->insert([
                                "id" => $ID_CONTRATO,
                                "datos" => $DATOS,
                                "id_franquicia" => $ID_FRANQUICIA,
                                "id_usuariocreacion" => $ID_USUARIOCREACION,
                                "nombre_usuariocreacion" => $NOMBRE_USUARIOCREACION,
                                "id_zona" => $ID_ZONA,
                                "estatus" => $ESTATUS,
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
                                "correo" => $CORREO,
                                "nombrereferencia" => $NOMBREREFERENCIA,
                                "casatipo" => $CASATIPO,
                                "casacolor" => $CASACOLOR,
                                "fotoine" => $FOTOINE,
                                "fotocasa" => $FOTOCASA,
                                "comprobantedomicilio" => $COMPROBANTEDOMICILIO,
                                "pagare" => $PAGARE,
                                "fotootros" => $FOTOATROS,
                                "observaciones" => $OBSERVACIONES,
                                "nota" => $NOTA,
                                "pagosadelantar" => $PAGOSADELANTAR,
                                "banderacomentarioconfirmacion" => $BANDERACOMENTARIOCONFIRMACIONES,
                                "estatusanteriorcontrato" => $ESTATUSANTERIORCONTRATO,
                                "diatemporal" => Carbon::now(),
                                "coordenadas" => $COORDENADAS,
                                "created_at" => $CREATED_AT == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($CREATED_AT)),
                                "updated_at" => $UPDATED_AT == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($UPDATED_AT)),
                                "id_optometrista" => $ID_OPTOMETRISTA,
                                "tarjeta" => $TARJETA,
                                "pago" => $PAGO,
                                "abonominimo" => $ABONOMINIMO,
                                "id_promocion" => $ID_PROMOCION,
                                "fotoineatras" => $FOTOINEATRAS,
                                "tarjetapensionatras" => $TARJETAPENSIONATRAS,
                                "total" => $TOTAL,
                                "idcontratorelacion" => $IDCONTRATORELACION,
                                "contador" => $CONTADOR,
                                "totalhistorial" => $TOTALHISTORIAL,
                                "totalpromocion" => $TOTALPROMOCION,
                                "totalproducto" => $TOTALPRODUCTO,
                                "totalabono" => $TOTALABONO,
                                "totalreal" => $TOTALREAL,
                                "fechaatraso" => $FECHAATRASO == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($FECHAATRASO)),
                                "costoatraso" => $COSTOATRASO,
                                "ultimoabono" => $ULTIMOABONO == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($ULTIMOABONO)),
                                "estatus_estadocontrato" => $ESTATUS_ESTADOCONTRATO,
                                "diapago" => $DIAPAGO,
                                "fechacobroini" => $FECHACOBROINI == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($FECHACOBROINI)),
                                "fechacobrofin" => $FECHACOBROFIN == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($FECHACOBROFIN)),
                                "fechacobroiniantes" => $FECHACOBROINIANTES == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($FECHACOBROINIANTES)),
                                "fechacobrofinantes" => $FECHACOBROFINANTES == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($FECHACOBROFINANTES)),
                                "enganche" => $ENGANCHE,
                                "entregaproducto" => $ENTREGAPRODUCTO,
                                "diaseleccionado" => $DIASELECCIONADO == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($DIASELECCIONADO)),
                                "fechaentrega" => $FECHAENTREGA == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($FECHAENTREGA)),
                                "promocionterminada" => $PROMOCIONTERMINADA,
                                "poliza" => $POLIZA,
                                "fechasubscripcion" => $FECHASUBSCRIPCION == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($FECHASUBSCRIPCION)),
                                "subscripcion" => $SUBSCRIPCION,
                                'calleentrega' => $CALLEENTREGA,
                                'numeroentrega' => $NUMEROENTREGA,
                                'deptoentrega' => $DEPTOENTREGA,
                                'alladodeentrega' => $ALLADODEENTREGA,
                                'frenteaentrega' => $FRENTEAENTREGA,
                                'entrecallesentrega' => $ENTRECALLESENTREGA,
                                'coloniaentrega' => $COLONIAENTREGA,
                                'localidadentrega' => $LOCALIDADENTREGA,
                                'casatipoentrega' => $CASATIPOENTREGA,
                                'casacolorentrega' => $CASACOLORENTREGA
                            ]);

                        } else {
                            //Existe contrato

                            //Actualizar contrato
                            DB::table("contratos")->where([['indice', '=', $consultacontrato[0]->indice]])->update([
                                "id" => $ID_CONTRATO,
                                "datos" => $DATOS,
                                "id_franquicia" => $ID_FRANQUICIA,
                                "id_usuariocreacion" => $ID_USUARIOCREACION,
                                "nombre_usuariocreacion" => $NOMBRE_USUARIOCREACION,
                                "id_zona" => $ID_ZONA,
                                "estatus" => $ESTATUS,
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
                                "correo" => $CORREO,
                                "nombrereferencia" => $NOMBREREFERENCIA,
                                "casatipo" => $CASATIPO,
                                "casacolor" => $CASACOLOR,
                                "fotoine" => $FOTOINE,
                                "fotocasa" => $FOTOCASA,
                                "comprobantedomicilio" => $COMPROBANTEDOMICILIO,
                                "pagare" => $PAGARE,
                                "fotootros" => $FOTOATROS,
                                "observaciones" => $OBSERVACIONES,
                                "nota" => $NOTA,
                                "pagosadelantar" => $PAGOSADELANTAR,
                                "banderacomentarioconfirmacion" => $BANDERACOMENTARIOCONFIRMACIONES,
                                "estatusanteriorcontrato" => $ESTATUSANTERIORCONTRATO,
                                "diatemporal" => Carbon::now(),
                                "coordenadas" => $COORDENADAS,
                                "created_at" => $CREATED_AT == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($CREATED_AT)),
                                "updated_at" => $UPDATED_AT == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($UPDATED_AT)),
                                "id_optometrista" => $ID_OPTOMETRISTA,
                                "tarjeta" => $TARJETA,
                                "pago" => $PAGO,
                                "abonominimo" => $ABONOMINIMO,
                                "id_promocion" => $ID_PROMOCION,
                                "fotoineatras" => $FOTOINEATRAS,
                                "tarjetapensionatras" => $TARJETAPENSIONATRAS,
                                "total" => $TOTAL,
                                "idcontratorelacion" => $IDCONTRATORELACION,
                                "contador" => $CONTADOR,
                                "totalhistorial" => $TOTALHISTORIAL,
                                "totalpromocion" => $TOTALPROMOCION,
                                "totalproducto" => $TOTALPRODUCTO,
                                "totalabono" => $TOTALABONO,
                                "totalreal" => $TOTALREAL,
                                "fechaatraso" => $FECHAATRASO == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($FECHAATRASO)),
                                "costoatraso" => $COSTOATRASO,
                                "ultimoabono" => $ULTIMOABONO == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($ULTIMOABONO)),
                                "estatus_estadocontrato" => $ESTATUS_ESTADOCONTRATO,
                                "diapago" => $DIAPAGO,
                                "fechacobroini" => $FECHACOBROINI == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($FECHACOBROINI)),
                                "fechacobrofin" => $FECHACOBROFIN == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($FECHACOBROFIN)),
                                "fechacobroiniantes" => $FECHACOBROINIANTES == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($FECHACOBROINIANTES)),
                                "fechacobrofinantes" => $FECHACOBROFINANTES == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($FECHACOBROFINANTES)),
                                "enganche" => $ENGANCHE,
                                "entregaproducto" => $ENTREGAPRODUCTO,
                                "diaseleccionado" => $DIASELECCIONADO == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($DIASELECCIONADO)),
                                "fechaentrega" => $FECHAENTREGA == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($FECHAENTREGA)),
                                "promocionterminada" => $PROMOCIONTERMINADA,
                                "poliza" => $POLIZA,
                                "fechasubscripcion" => $FECHASUBSCRIPCION == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($FECHASUBSCRIPCION)),
                                "subscripcion" => $SUBSCRIPCION,
                                'calleentrega' => $CALLEENTREGA,
                                'numeroentrega' => $NUMEROENTREGA,
                                'deptoentrega' => $DEPTOENTREGA,
                                'alladodeentrega' => $ALLADODEENTREGA,
                                'frenteaentrega' => $FRENTEAENTREGA,
                                'entrecallesentrega' => $ENTRECALLESENTREGA,
                                'coloniaentrega' => $COLONIAENTREGA,
                                'localidadentrega' => $LOCALIDADENTREGA,
                                'casatipoentrega' => $CASATIPOENTREGA,
                                'casacolorentrega' => $CASACOLORENTREGA
                            ]);
                        }

                    } catch (\Exception $e) {
                        \Log::info("Error al migrar cuenta contrato: " . $contrato[0] . "\n" . $e);
                        continue;
                    }

                }

            }

        } catch (\Exception $e) {
            \Log::info("ERROR: " . $e);
        }


        //HISTORIALES CLINICOS
        try {

            if (strlen($rutaarchivohistoriales) > 0) {
                //$rutaarchivohistoriales diferente de vacio

                $filas = Excel::toArray(new CsvImport(), $rutaarchivohistoriales);

                foreach ($filas[0] as $key => $historial) {

                    try {

                        $ID_HISTORIAL = $historial[0];
                        if (strlen($ID_HISTORIAL) == 0) {
                            //IDHISTORIAL es igual a vacio
                            $ID_HISTORIAL = null;
                        }

                        $ID_CONTRATO = $historial[1];
                        if (strlen($ID_CONTRATO) == 0) {
                            //IDCONTRATO es igual a vacio
                            $ID_CONTRATO = null;
                        }

                        $EDAD = $historial[2];
                        if (strlen($EDAD) == 0) {
                            //EDAD es igual a vacio
                            $EDAD = null;
                        }

                        $FECHAENTREGA = $historial[3];
                        if (strlen($FECHAENTREGA) == 0) {
                            //FECHAENTREGA es igual a vacio
                            $FECHAENTREGA = null;
                        }

                        $DIAGNOSTICO = $historial[4];
                        if (strlen($DIAGNOSTICO) == 0) {
                            //DIAGNOSTICO es igual a vacio
                            $DIAGNOSTICO = null;
                        }

                        $OCUPACION = $historial[5];
                        if (strlen($OCUPACION) == 0) {
                            //OCUPACION es igual a vacio
                            $OCUPACION = null;
                        }

                        $DIABETES = $historial[6];
                        if (strlen($DIABETES) == 0) {
                            //DIABETES es igual a vacio
                            $DIABETES = null;
                        }

                        $HIPERTENSION = $historial[7];
                        if (strlen($HIPERTENSION) == 0) {
                            //HIPERTENSION es igual a vacio
                            $HIPERTENSION = null;
                        }

                        $DOLOR = $historial[8];
                        if (strlen($DOLOR) == 0) {
                            //DOLOR es igual a vacio
                            $DOLOR = null;
                        }

                        $ARDOR = $historial[9];
                        if (strlen($ARDOR) == 0) {
                            //ARDOR es igual a vacio
                            $ARDOR = null;
                        }

                        $GOLPEOJOS = $historial[10];
                        if (strlen($GOLPEOJOS) == 0) {
                            //GOLPEOJOS es igual a vacio
                            $GOLPEOJOS = null;
                        }

                        $OTROM = $historial[11];
                        if (strlen($OTROM) == 0) {
                            //OTROM es igual a vacio
                            $OTROM = null;
                        }

                        $MOLESTIAOTRO = $historial[12];
                        if (strlen($MOLESTIAOTRO) == 0) {
                            //MOLESTIAOTRO es igual a vacio
                            $MOLESTIAOTRO = null;
                        }

                        $ULTIMOEXAMEN = $historial[13];
                        if (strlen($ULTIMOEXAMEN) == 0) {
                            //ULTIMOEXAMEN es igual a vacio
                            $ULTIMOEXAMEN = null;
                        }

                        $ESFERICODER = $historial[14];
                        if (strlen($ESFERICODER) == 0) {
                            //ESFERICODER es igual a vacio
                            $ESFERICODER = null;
                        }

                        $CILINDRODER = $historial[15];
                        if (strlen($CILINDRODER) == 0) {
                            //CILINDRODER es igual a vacio
                            $CILINDRODER = null;
                        }

                        $EJEDER = $historial[16];
                        if (strlen($EJEDER) == 0) {
                            //EJEDER es igual a vacio
                            $EJEDER = null;
                        }

                        $ADDDER = $historial[17];
                        if (strlen($ADDDER) == 0) {
                            //ADDDER es igual a vacio
                            $ADDDER = null;
                        }

                        $ALTDER = $historial[18];
                        if (strlen($ALTDER) == 0) {
                            //ALTDER es igual a vacio
                            $ALTDER = null;
                        }

                        $ESFERICOIZQ = $historial[19];
                        if (strlen($ESFERICOIZQ) == 0) {
                            //ESFERICOIZQ es igual a vacio
                            $ESFERICOIZQ = null;
                        }

                        $CILINDROIZQ = $historial[20];
                        if (strlen($CILINDROIZQ) == 0) {
                            //CILINDROIZQ es igual a vacio
                            $CILINDROIZQ = null;
                        }

                        $EJEIZQ = $historial[21];
                        if (strlen($EJEIZQ) == 0) {
                            //EJEIZQ es igual a vacio
                            $EJEIZQ = null;
                        }

                        $ADDIZQ = $historial[22];
                        if (strlen($ADDIZQ) == 0) {
                            //ADDIZQ es igual a vacio
                            $ADDIZQ = null;
                        }

                        $ALTIZQ = $historial[23];
                        if (strlen($ALTIZQ) == 0) {
                            //ALTIZQ es igual a vacio
                            $ALTIZQ = null;
                        }

                        $ID_PRODUCTO = $historial[24];
                        if (strlen($ID_PRODUCTO) == 0) {
                            //ID_PRODUCTO es igual a vacio
                            $ID_PRODUCTO = null;
                        }

                        $ID_PAQUETE = $historial[25];
                        if (strlen($ID_PAQUETE) == 0) {
                            //ID_PAQUETE es igual a vacio
                            $ID_PAQUETE = null;
                        }

                        $MATERIAL = $historial[26];
                        if (strlen($MATERIAL) == 0) {
                            //MATERIAL es igual a vacio
                            $MATERIAL = null;
                        }

                        $MATERIALOTRO = $historial[27];
                        if (strlen($MATERIALOTRO) == 0) {
                            //MATERIALOTRO es igual a vacio
                            $MATERIALOTRO = null;
                        }

                        $COSTOMATERIAL = $historial[28];
                        if (strlen($COSTOMATERIAL) == 0) {
                            //COSTOMATERIAL es igual a vacio
                            $COSTOMATERIAL = null;
                        }

                        $BIFOCAL = $historial[29];
                        if (strlen($BIFOCAL) == 0) {
                            //BIFOCAL es igual a vacio
                            $BIFOCAL = null;
                        }

                        $FOTOCROMATICO = $historial[30];
                        if (strlen($FOTOCROMATICO) == 0) {
                            //FOTOCROMATICO es igual a vacio
                            $FOTOCROMATICO = 0;
                        }

                        $AR = $historial[31];
                        if (strlen($AR) == 0) {
                            //AR es igual a vacio
                            $AR = 1;
                        }

                        $TINTE = $historial[32];
                        if (strlen($TINTE) == 0) {
                            //TINTE es igual a vacio
                            $TINTE = 0;
                        }

                        $BLUERAY = $historial[33];
                        if (strlen($BLUERAY) == 0) {
                            //BLUERAY es igual a vacio
                            $BLUERAY = 0;
                        }

                        $OTROT = $historial[34];
                        if (strlen($OTROT) == 0) {
                            //OTROT es igual a vacio
                            $OTROT = 0;
                        }

                        $TRATAMIENTOOTRO = $historial[35];
                        if (strlen($TRATAMIENTOOTRO) == 0) {
                            //TRATAMIENTOOTRO es igual a vacio
                            $TRATAMIENTOOTRO = null;
                        }

                        $COSTOTRATAMIENTO = $historial[36];
                        if (strlen($COSTOTRATAMIENTO) == 0) {
                            //COSTOTRATAMIENTO es igual a vacio
                            $COSTOTRATAMIENTO = null;
                        }

                        $OBSERVACIONES = $historial[37];
                        if (strlen($OBSERVACIONES) == 0) {
                            //OBSERVACIONES es igual a vacio
                            $OBSERVACIONES = null;
                        }

                        $OBSERVACIONESINTERNO = $historial[38];
                        if (strlen($OBSERVACIONESINTERNO) == 0) {
                            //OBSERVACIONESINTERNO es igual a vacio
                            $OBSERVACIONESINTERNO = null;
                        }

                        $TIPO = $historial[39];
                        if (strlen($TIPO) == 0) {
                            //TIPO es igual a vacio
                            $TIPO = 0;
                        }

                        $BIFOCALOTRO = $historial[40];
                        if (strlen($BIFOCALOTRO) == 0) {
                            //BIFOCALOTRO es igual a vacio
                            $BIFOCALOTRO = null;
                        }

                        $COSTOBIFOCAL = $historial[41];
                        if (strlen($COSTOBIFOCAL) == 0) {
                            //COSTOBIFOCAL es igual a vacio
                            $COSTOBIFOCAL = null;
                        }

                        $EMBARAZADA = $historial[42];
                        if (strlen($EMBARAZADA) == 0) {
                            //EMBARAZADA es igual a vacio
                            $EMBARAZADA = null;
                        }

                        $DURMIOSEISOCHOHORAS = $historial[43];
                        if (strlen($DURMIOSEISOCHOHORAS) == 0) {
                            //DURMIOSEISOCHOHORAS es igual a vacio
                            $DURMIOSEISOCHOHORAS = null;
                        }

                        $ACTIVIDADDIA = $historial[44];
                        if (strlen($ACTIVIDADDIA) == 0) {
                            //ACTIVIDADDIA es igual a vacio
                            $ACTIVIDADDIA = null;
                        }

                        $PROBLEMASOJOS = $historial[45];
                        if (strlen($PROBLEMASOJOS) == 0) {
                            //PROBLEMASOJOS es igual a vacio
                            $PROBLEMASOJOS = null;
                        }

                        $CREATED_AT = $historial[46];
                        if (strlen($CREATED_AT) == 0) {
                            //CREATED_AT es igual a vacio
                            $CREATED_AT = null;
                        }

                        $UPDATED_AT = $historial[47];
                        if (strlen($UPDATED_AT) == 0) {
                            //UPDATED_AT es igual a vacio
                            $UPDATED_AT = null;
                        }

                        $consultahistorial = DB::select("SELECT indice FROM historialclinico WHERE id_contrato = '$ID_CONTRATO' AND id = '$ID_HISTORIAL'");

                        if ($consultahistorial == null) {
                            //No existe historial

                            //Crear historial clinico
                            DB::table("historialclinico")->insert([
                                "id" => $ID_HISTORIAL,
                                "id_contrato" => $ID_CONTRATO,
                                "edad" => $EDAD,
                                "fechaentrega" => $FECHAENTREGA == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($FECHAENTREGA)),
                                "diagnostico" => $DIAGNOSTICO,
                                "ocupacion" => $OCUPACION,
                                "diabetes" => $DIABETES,
                                "hipertension" => $HIPERTENSION,
                                "dolor" => $DOLOR,
                                "ardor" => $ARDOR,
                                "golpeojos" => $GOLPEOJOS,
                                "otroM" => $OTROM,
                                "molestiaotro" => $MOLESTIAOTRO,
                                "ultimoexamen" => Carbon::now(),
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
                                "created_at" => $CREATED_AT == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($CREATED_AT)),
                                "updated_at" => $UPDATED_AT == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($UPDATED_AT))
                            ]);

                        } else {
                            //Existe historial

                            //Actualizar historial clinico
                            DB::table("historialclinico")->where([['indice', '=', $consultahistorial[0]->indice]])->update([
                                "id" => $ID_HISTORIAL,
                                "id_contrato" => $ID_CONTRATO,
                                "edad" => $EDAD,
                                "fechaentrega" => $FECHAENTREGA == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($FECHAENTREGA)),
                                "diagnostico" => $DIAGNOSTICO,
                                "ocupacion" => $OCUPACION,
                                "diabetes" => $DIABETES,
                                "hipertension" => $HIPERTENSION,
                                "dolor" => $DOLOR,
                                "ardor" => $ARDOR,
                                "golpeojos" => $GOLPEOJOS,
                                "otroM" => $OTROM,
                                "molestiaotro" => $MOLESTIAOTRO,
                                "ultimoexamen" => Carbon::now(),
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
                                "created_at" => $CREATED_AT == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($CREATED_AT)),
                                "updated_at" => $UPDATED_AT == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($UPDATED_AT))
                            ]);
                        }

                    } catch (\Exception $e) {
                        \Log::info("Error al migrar cuenta historial: " . $historial[0] . "\n" . $e);
                        continue;
                    }

                }

            }

        } catch (\Exception $e) {
            \Log::info("ERROR: " . $e);
        }

        //ABONOS
        try {

            if (strlen($rutaarchivoabonos) > 0) {
                //$rutaarchivoabonos diferente de vacio

                $filas = Excel::toArray(new CsvImport(), $rutaarchivoabonos);

                foreach ($filas[0] as $key => $abono) {

                    try {

                        $ID_ABONO = $abono[0];
                        if (strlen($ID_ABONO) == 0) {
                            //ID_ABONO es igual a vacio
                            $ID_ABONO = null;
                        }

                        $FOLIO = $abono[1];
                        if (strlen($FOLIO) == 0) {
                            //FOLIO es igual a vacio
                            $FOLIO = null;
                        }

                        $ID_FRANQUICIA = $abono[2];
                        if (strlen($ID_FRANQUICIA) == 0) {
                            //ID_FRANQUICIA es igual a vacio
                            $ID_FRANQUICIA = null;
                        }

                        $ID_CONTRATO = $abono[3];
                        if (strlen($ID_CONTRATO) == 0) {
                            //ID_CONTRATO es igual a vacio
                            $ID_CONTRATO = null;
                        }

                        $ID_USUARIO = $abono[4];
                        if (strlen($ID_USUARIO) == 0) {
                            //ID_USUARIO es igual a vacio
                            $ID_USUARIO = null;
                        }

                        $ABONO = $abono[5];
                        if (strlen($ABONO) == 0) {
                            //ABONO es igual a vacio
                            $ABONO = null;
                        }

                        $METODOPAGO = $abono[6];
                        if (strlen($METODOPAGO) == 0) {
                            //METODOPAGO es igual a vacio
                            $METODOPAGO = null;
                        }

                        $ADELANTOS = $abono[7];
                        if (strlen($ADELANTOS) == 0) {
                            //ADELANTOS es igual a vacio
                            $ADELANTOS = null;
                        }

                        $TIPOABONO = $abono[8];
                        if (strlen($TIPOABONO) == 0) {
                            //TIPOABONO es igual a vacio
                            $TIPOABONO = null;
                        }

                        $ATRASO = $abono[9];
                        if (strlen($ATRASO) == 0) {
                            //ATRASO es igual a vacio
                            $ATRASO = null;
                        }

                        $POLIZA = $abono[10];
                        if (strlen($POLIZA) == 0) {
                            //POLIZA es igual a vacio
                            $POLIZA = null;
                        }

                        $CORTE = $abono[11];
                        if (strlen($CORTE) == 0) {
                            //CORTE es igual a vacio
                            $CORTE = null;
                        }

                        $ID_CORTE = $abono[12];
                        if (strlen($ID_CORTE) == 0) {
                            //ID_CORTE es igual a vacio
                            $ID_CORTE = null;
                        }

                        $ID_CONTRATOPRODUCTO = $abono[13];
                        if (strlen($ID_CONTRATOPRODUCTO) == 0) {
                            //ID_CONTRATOPRODUCTO es igual a vacio
                            $ID_CONTRATOPRODUCTO = null;
                        }

                        $CREATED_AT = $abono[14];
                        if (strlen($CREATED_AT) == 0) {
                            //CREATED_AT es igual a vacio
                            $CREATED_AT = null;
                        }

                        $UPDATED_AT = $abono[15];
                        if (strlen($UPDATED_AT) == 0) {
                            //UPDATED_AT es igual a vacio
                            $UPDATED_AT = null;
                        }

                        $consultaabono = DB::select("SELECT indice FROM abonos WHERE id_contrato = '$ID_CONTRATO' AND id = '$ID_ABONO'");

                        if ($consultaabono == null) {
                            //No existe abono

                            $contrato = DB::select("SELECT id_zona FROM contrato WHERE id = '$ID_CONTRATO'");

                            $idZona = $contrato != null ? $contrato[0]->id_zona : null;

                            //Crear abono
                            DB::table('abonos')->insert([
                                "id" => $ID_ABONO,
                                "folio" => $FOLIO,
                                "id_franquicia" => $ID_FRANQUICIA,
                                "id_contrato" => $ID_CONTRATO,
                                "id_usuario" => $ID_USUARIO,
                                "abono" => $ABONO,
                                "metodopago" => $METODOPAGO,
                                "adelantos" => $ADELANTOS,
                                "tipoabono" => $TIPOABONO,
                                "atraso" => $ATRASO,
                                "poliza" => $POLIZA,
                                "corte" => $CORTE,
                                "id_corte" => $ID_CORTE,
                                "id_contratoproducto" => $ID_CONTRATOPRODUCTO,
                                "id_zona" => $idZona,
                                "created_at" => $CREATED_AT == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($CREATED_AT)),
                                "updated_at" => $UPDATED_AT == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($UPDATED_AT))
                            ]);

                        } else {
                            //Existe el abono

                            //Actualizar abono
                            DB::table("abonos")->where([['indice', '=', $consultaabono[0]->indice]])->update([
                                "id" => $ID_ABONO,
                                "folio" => $FOLIO,
                                "id_franquicia" => $ID_FRANQUICIA,
                                "id_contrato" => $ID_CONTRATO,
                                "id_usuario" => $ID_USUARIO,
                                "abono" => $ABONO,
                                "metodopago" => $METODOPAGO,
                                "adelantos" => $ADELANTOS,
                                "tipoabono" => $TIPOABONO,
                                "atraso" => $ATRASO,
                                "poliza" => $POLIZA,
                                "corte" => $CORTE,
                                "id_corte" => $ID_CORTE,
                                "id_contratoproducto" => $ID_CONTRATOPRODUCTO,
                                "created_at" => $CREATED_AT == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($CREATED_AT)),
                                "updated_at" => $UPDATED_AT == null ? Carbon::now() : Carbon::instance(Date::excelToDateTimeObject($UPDATED_AT))
                            ]);
                        }

                    } catch (\Exception $e) {
                        \Log::info("Error al migrar cuenta abono: " . $abono[0] . "\n" . $e);
                        continue;
                    }

                }

            }

        } catch (\Exception $e) {
            \Log::info("ERROR: " . $e);
        }

        \Log::info("Metodo migrarcuentasarchivoexcelpolizaprueba TERMINO");

    }

    public function reportaractualizarsolicitudcontratolistanegra($idFranquicia, $idContrato){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - PRINCIPAL

            request()->validate([
                'descripcion' => 'required|string'
            ]);

            $contrato = DB::select("SELECT c.estatus_estadocontrato FROM contratos c WHERE c.id = '$idContrato' AND c.id_franquicia = '$idFranquicia'");

            if($contrato != null){
                //Existe el contrato
                $contratolistanegra = DB::select("SELECT * FROM contratoslistanegra cln WHERE cln.id_contrato = '$idContrato' AND cln.estado = '0'");
                if($contratolistanegra != null){
                    //Existe reporte de contrato a lista negra pendiente - Actualizar

                    DB::table("contratoslistanegra")->where([['indice', '=', $contratolistanegra[0]->indice]])->update([
                        "descripcion" => request('descripcion'),
                        'updated_at' => Carbon::now()
                    ]);

                    return back()->with('bien',"Reporte de lista negra actualizado correctamente.");

                }else{
                    //No existe solicitud de lista negra pendiente - Crear nueva
                    DB::table("contratoslistanegra")->insert([
                        'id_contrato' => $idContrato,
                        'descripcion' => request('descripcion'),
                        'estado' => '0',
                        'created_at' => Carbon::now()
                    ]);

                    return back()->with('bien',"Reporte de lista negra registrado correctamente.");
                }

            }else{
                //No existe el contrato
                return back()->with('alerta',"No existe el contrato");
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function solicitudrechazaraprobarcontratolistanegra($idFranquicia, $idContrato, $opcion){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - PRINCIPAL

            //Opciones
            // 1 -> Aprobar
            // 2 -> Rechazar

            $contrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato' AND c.id_franquicia = '$idFranquicia'");

            if($contrato != null){
                //Existe el contrato
                $contratolistanegra = DB::select("SELECT * FROM contratoslistanegra cln WHERE cln.id_contrato = '$idContrato' AND cln.estado = '0' ORDER BY cln.created_at DESC LIMIT 1");
                if($contratolistanegra != null){
                    //Existe reporte de contrato a lista negra aceptado - solicitar autorizacion

                    if ($opcion == 1){
                        //Aprobo solicitud
                        $cambio = "Aprobo reporte de contrato lista negra con descripcion: '" . $contratolistanegra[0]->descripcion ."'";
                        $mensaje = "Reporte de solicitud para ingreso de contrato a lista negra aprobado correctamente.";
                    }else{
                        //Rechazo solicitud
                        $cambio = "Rechazo reporte de contrato lista negra con descripcion: '" . $contratolistanegra[0]->descripcion ."'";
                        $mensaje = "Reporte de solicitud para ingreso de contrato a lista negra rechazado correctamente.";
                    }

                    DB::table("contratoslistanegra")->where([['indice', '=', $contratolistanegra[0]->indice]])->update([
                        "estado" => $opcion,
                        'updated_at' => Carbon::now()
                    ]);

                    //Generar registro de solicitud
                    $idUsuario = Auth::user()->id;

                    //Registrar movimiento
                    $globalesServicioWeb = new globalesServicioWeb;
                    DB::table('historialcontrato')->insert([
                        'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $idUsuario,
                        'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                        'cambios' => $cambio]);

                    return back()->with('bien',$mensaje);

                }else{
                    //No existe solicitud de lista negra aceptada
                    return back()->with('bien',"No puedes solicitar autorizacion debido al estado actual del reporte de lista negra.");
                }

            }else{
                //No existe el contrato
                return back()->with('alerta',"No existe el contrato");
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }


    }
    public function solicitudautorizacioncontratolistanegra($idFranquicia, $idContrato){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - PRINCIPAL

            $contrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato' AND c.id_franquicia = '$idFranquicia'");

            if($contrato != null){
                //Existe el contrato
                $contratolistanegra = DB::select("SELECT * FROM contratoslistanegra cln WHERE cln.id_contrato = '$idContrato' AND cln.estado = '1' ORDER BY cln.created_at DESC LIMIt 1");
                if($contratolistanegra != null){
                    //Existe reporte de contrato a lista negra aceptado - solicitar autorizacion

                    //Generar registro de solicitud
                    $idUsuario = Auth::user()->id;
                    DB::table('autorizaciones')->insert([
                        'id_contrato' => $idContrato, 'id_usuarioC' => $idUsuario, 'id_franquicia' => $idFranquicia,
                        'fechacreacioncontrato' => $contrato[0]->created_at,
                        'estadocontrato' => $contrato[0]->estatus_estadocontrato,
                        'mensaje' => "Solicitó autorizacion para ingresar contrato a lista negra con la siguiente descripcion: '" . $contratolistanegra[0]->descripcion . "'",
                        'estatus' => '0', 'tipo' => '15', 'created_at' => Carbon::now()
                    ]);

                    //Registrar movimiento
                    $globalesServicioWeb = new globalesServicioWeb;
                    DB::table('historialcontrato')->insert([
                        'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $idUsuario,
                        'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                        'cambios' => "Solicitó autorizacion para ingresar contrato a lista negra con la siguiente descripcion: '" . $contratolistanegra[0]->descripcion . "'", 'tipomensaje' => '3']);

                    return back()->with('bien',"Solicitud para autorizacion de lista negra generada correctamente.");

                }else{
                    //No existe solicitud de lista negra aceptada
                    return back()->with('bien',"No puedes solicitar autorizacion debido al estado actual del reporte de lista negra.");
                }

            }else{
                //No existe el contrato
                return back()->with('alerta',"No existe el contrato");
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function contratoreponer($idFranquicia, $idContrato){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - PRINCIPAL

            request()->validate([
                'optometristaReposicion' => 'required',
                'promocionReposicion' => 'required'
            ]);

            $idOptometristaReposicion =  request('optometristaReposicion');
            $idPromocionReposicion =  request('promocionReposicion');

            $existeOpto = DB::select("SELECT * FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                            WHERE u.id = '$idOptometristaReposicion' AND u.rol_id = '12'");

            if($existeOpto != null) {
                //Existe opto

                $existePromoReposicion = DB::select("SELECT * FROM promocion p WHERE p.id = '$idPromocionReposicion' AND p.id_franquicia = '$idFranquicia' AND p.tipo = 1 AND p.status = 1");

                if($existePromoReposicion != null) {

                    $contrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato' AND c.id_franquicia = '$idFranquicia'");

                    if ($contrato != null) {
                        //Existe el contrato

                        $globalesServicioWeb = new globalesServicioWeb;
                        //Formar nuevo idContrato para crear contrato y asignarlo a la opto
                        $anoActual = Carbon::now()->format('y'); //Obtener los ultimos 2 digitos del año 21, 22, 23, 24, etc
                        //Obtener indice de la franquicia
                        $franquicia = DB::select("SELECT indice FROM franquicias WHERE id = '$idFranquicia'");
                        $identificadorFranquicia = "";
                        if ($franquicia != null) {
                            //Existe franquicia
                            $identificadorFranquicia = $globalesServicioWeb::obtenerIdentificadorFranquicia($franquicia[0]->indice);
                        }
                        $identificadorFranquicia = $anoActual . $identificadorFranquicia . $globalesServicioWeb::obtenerIdentificadorUsuario($idOptometristaReposicion); //2200100001, 2200200001, etc

                        //Obtener el ultimo id generado en la tabla de contrato
                        $contratoSelect = DB::select("SELECT id FROM contratos
                                                                WHERE id_franquicia = '$idFranquicia'
                                                                AND id LIKE '%$identificadorFranquicia%'
                                                                AND LENGTH (id) = 14 ORDER BY id DESC LIMIT 1");
                        if ($contratoSelect != null) {
                            //Existe registro (Significa que ya hay contratos personalizados creados)
                            $idContratoTemp = substr($contratoSelect[0]->id, -4);
                            $ultimoIdContratoPerzonalizado = $idContratoTemp;
                        } else {
                            //Sera el primer contrato perzonalizado a crear de la sucursal
                            $ultimoIdContratoPerzonalizado = 0;
                        }

                        $arrayNuevoIdContrato = $globalesServicioWeb::generarIdContratoPersonalizado($identificadorFranquicia, $ultimoIdContratoPerzonalizado);
                        $siguienteIdContrato = $arrayNuevoIdContrato[0];

                        //Crear nuevo contrato con informacion base
                        DB::table("contratos")->insert([
                            "id" => $siguienteIdContrato,
                            "id_franquicia" => $idFranquicia,
                            "datos" => 1,
                            "id_usuariocreacion" => $idOptometristaReposicion,
                            "nombre_usuariocreacion" => $existeOpto[0]->name,
                            "id_zona" => $contrato[0]->id_zona,
                            "nombre" => $contrato[0]->nombre,
                            "calle" => $contrato[0]->calle,
                            "numero" => $contrato[0]->numero,
                            "depto" => $contrato[0]->depto,
                            "alladode" => $contrato[0]->alladode,
                            "frentea" => $contrato[0]->frentea,
                            "entrecalles" => $contrato[0]->entrecalles,
                            "colonia" => $contrato[0]->colonia,
                            "localidad" => $contrato[0]->localidad,
                            "telefono" => $contrato[0]->telefono,
                            "telefonoreferencia" => $contrato[0]->telefonoreferencia,
                            "nombrereferencia" => $contrato[0]->nombrereferencia,
                            "casatipo" => $contrato[0]->casatipo,
                            "casacolor" => $contrato[0]->casacolor,
                            "fotoine" => $contrato[0]->fotoine,
                            "fotoineatras" => $contrato[0]->fotoineatras,
                            "fotocasa" => $contrato[0]->fotoineatras,
                            "comprobantedomicilio" => $contrato[0]->comprobantedomicilio,
                            "id_optometrista" => $idOptometristaReposicion,
                            "tarjeta" => $contrato[0]->tarjeta,
                            "tarjetapensionatras" => $contrato[0]->tarjetapensionatras,
                            "pago" => '0',
                            "id_promocion" => $idPromocionReposicion,
                            "idcontratorelacion" => $contrato[0]->idcontratorelacion,
                            "contador" => '1',
                            "ultimoabono" => $contrato[0]->ultimoabono,
                            "estatus_estadocontrato" => '0',
                            "diapago" => $contrato[0]->diapago,
                            "costoatraso" => '0',
                            "pagosadelantar" => '0',
                            "correo" => $contrato[0]->correo,
                            "estatus" => '0',
                            "pagare" => $contrato[0]->pagare,
                            "fotootros" => $contrato[0]->fotootros,
                            "poliza" => null,
                            "subscripcion" => $contrato[0]->subscripcion,
                            "fechasubscripcion" => $contrato[0]->fechasubscripcion,
                            "enganche" => '0',
                            "entregaproducto" => '0',
                            "promocionterminada" => '0',
                            "totalhistorial" => $contrato[0]->totalreal,
                            "totalreal" => $contrato[0]->totalreal,
                            "total" => $contrato[0]->totalreal,
                            "totalpromocion" => '0',
                            "coordenadas" => $contrato[0]->coordenadas,
                            "calleentrega" => $contrato[0]->calleentrega,
                            "numeroentrega" => $contrato[0]->numeroentrega,
                            "deptoentrega" => $contrato[0]->deptoentrega,
                            "alladodeentrega" => $contrato[0]->alladodeentrega,
                            "frenteaentrega" => $contrato[0]->frenteaentrega,
                            "entrecallesentrega" => $contrato[0]->entrecallesentrega,
                            "coloniaentrega" => $contrato[0]->coloniaentrega,
                            "localidadentrega" => $contrato[0]->localidadentrega,
                            "casatipoentrega" => $contrato[0]->casatipoentrega,
                            "casacolorentrega" => $contrato[0]->casacolorentrega,
                            "alias" => $contrato[0]->alias,
                            "created_at" => Carbon::now()
                            ]);

                        DB::table("promocioncontrato")->insert([
                            "id" => $globalesServicioWeb::generarIdAlfanumerico('promocioncontrato', '5'),
                            "id_contrato" => $siguienteIdContrato,
                            "id_promocion" => $idPromocionReposicion,
                            "estado" => '1',
                            "created_at" => Carbon::now(),
                            "id_franquicia" => $idFranquicia
                        ]);

                        //Insertar contrato en tabla contratostemporalessincronizacion
                        $contratosGlobal = new contratosGlobal;
                        $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($siguienteIdContrato, Auth::user()->id);

                        //Actualizar datos contrato a mayusculas y quitar acentos
                        $contratosGlobal::actualizarContratoHistorialesClinicosMayusculasAcentos($siguienteIdContrato, 0);

                        //Insertar o actualizar contrato tabla contratoslistatemporales
                        $contratosGlobal::insertarActualizarDatosContratoListaTemporales($siguienteIdContrato);

                        //Historial base
                        $historiales = DB::select("SELECT * FROM historialclinico hc WHERE hc.id_contrato = '$idContrato' AND hc.tipo = 0");

                        foreach ($historiales as $historial){
                            $idHistorialClinico = $historial->id;
                            $nuevoIdHistorial = $globalesServicioWeb::generarIdAlfanumerico('historialclinico', '5');
                            DB::table("historialclinico")->insert([
                                "id" => $nuevoIdHistorial,
                                "id_contrato" => $siguienteIdContrato,
                                "edad" => $historial->edad,
                                "fechaentrega" => $historial->fechaentrega,
                                "diagnostico" => $historial->diagnostico,
                                "ocupacion" => $historial->ocupacion,
                                "diabetes" => $historial->diabetes,
                                "hipertension" => $historial->hipertension,
                                "dolor" => $historial->dolor,
                                "ardor" => $historial->ardor,
                                "golpeojos" => $historial->golpeojos,
                                "otroM" => $historial->otroM,
                                "molestiaotro" => $historial->molestiaotro,
                                "ultimoexamen" => $historial->ultimoexamen,
                                "esfericoder" => $historial->esfericoder,
                                "cilindroder" => $historial->cilindroder,
                                "ejeder" => $historial->ejeder,
                                "addder" => $historial->addder,
                                "altder" => $historial->esfericoizq,
                                "esfericoizq" => $historial->esfericoizq,
                                "cilindroizq" => $historial->cilindroizq,
                                "ejeizq" => $historial->ejeizq,
                                "addizq" => $historial->addizq,
                                "altizq" => $historial->altizq,
                                "id_producto" => $historial->id_producto,
                                "id_paquete" => $historial->id_paquete,
                                "material" => $historial->material,
                                "materialotro" => $historial->materialotro,
                                "costomaterial" => $historial->costomaterial,
                                "bifocal" => $historial->bifocal,
                                "fotocromatico" => $historial->fotocromatico,
                                "ar" => $historial->ar,
                                "tinte" => $historial->tinte,
                                "blueray" => $historial->blueray,
                                "otroT" => $historial->otroT,
                                "tratamientootro" => $historial->tratamientootro,
                                "costotratamiento" => $historial->costotratamiento,
                                "observaciones" => $historial->observaciones,
                                "observacionesinterno" => $historial->observacionesinterno,
                                "tipo" => $historial->tipo,
                                "bifocalotro" => $historial->bifocalotro,
                                "costobifocal" => $historial->costobifocal,
                                "embarazada" => $historial->embarazada,
                                "durmioseisochohoras" => $historial->durmioseisochohoras,
                                "actividaddia" => $historial->actividaddia,
                                "problemasojos" => $historial->problemasojos,
                                "policarbonatotipo" => $historial->policarbonatotipo,
                                "id_tratamientocolortinte" => $historial->id_tratamientocolortinte,
                                "estilotinte" => $historial->estilotinte,
                                "polarizado" => $historial->polarizado,
                                "id_tratamientocolorpolarizado" => $historial->id_tratamientocolorpolarizado,
                                "espejo" => $historial->espejo,
                                "id_tratamientocolorespejo" => $historial->id_tratamientocolorespejo,
                                "created_at" => $historial->created_at,
                                "updated_at" => $historial->updated_at
                            ]);

                            //Lectura y Dorado 2
                            if($historial->id_paquete == 1 || $historial->id_paquete == 6){
                                $historialSinConversion = DB::select("SELECT * FROM historialsinconversion hsc WHERE hsc.id_contrato = '$idContrato' AND hsc.id_historial = '$idHistorialClinico'");
                                if($historialSinConversion != null){
                                    //Generar historial sin conversion
                                    DB::table('historialsinconversion')->insert([
                                        'id_contrato' => $siguienteIdContrato,
                                        'id_historial' => $nuevoIdHistorial,
                                        'esfericoder' => $historialSinConversion[0]->esfericoder,
                                        'cilindroder' => $historialSinConversion[0]->cilindroder,
                                        'ejeder' => $historialSinConversion[0]->ejeder,
                                        'addder' => $historialSinConversion[0]->addder,
                                        'esfericoizq' => $historialSinConversion[0]->esfericoizq,
                                        'cilindroizq' => $historialSinConversion[0]->cilindroizq,
                                        'ejeizq' => $historialSinConversion[0]->ejeizq,
                                        'addizq' => $historialSinConversion[0]->addizq,
                                        'created_at' => Carbon::now()]);
                                }
                            }
                        }

                        //Actualizar datos historial clinico a mayusculas y quitar acentos
                        $contratosGlobal::actualizarContratoHistorialesClinicosMayusculasAcentos($siguienteIdContrato, 1);

                        //Guardar movimiento de creacion de reposicion
                        DB::table('historialcontrato')->insert([
                            'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                            'id_usuarioC' => Auth::user()->id,
                            'id_contrato' => $idContrato,
                            'created_at' => Carbon::now(),
                            'cambios' => "Creo contrato de reposicion con el numero: '" . $siguienteIdContrato . "'"]);


                        return redirect()->route('vercontrato',['idFranquicia'=>$idFranquicia, 'idContrato' => $siguienteIdContrato])
                            ->with('bien', "Reposicion para contrato: '" . $idContrato . "' creada correctamente.");

                    } else {
                        //No existe el contrato
                        return back()->with('alerta', "No existe el contrato");
                    }
                }else{
                    //No existe promocion
                    return back()->with('alerta', "No existe promoción seleccionado o se encuentra desactivada");
                }
            }else{
                //No existe opto
                return back()->with('alerta', "No existe optometrista seleccionado");
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function llenarTablaContratosLioFuga($idFranquicia){
        //Contratos en lio fuga x sucursal
        $contratos = DB::select("SELECT c.id, c.id_franquicia, c.nombre, c.coloniaentrega AS colonia, c.calleentrega AS calle,
                                c.numeroentrega AS numero, c.telefono, c.created_at
                                FROM contratos c WHERE c.id_franquicia = '$idFranquicia' AND c.estatus_estadocontrato = '14'
                                ORDER BY c.created_at ASC");

        foreach ($contratos as $contrato){
            $idContrato = $contrato->id;
            $existeContrato = DB::select("SELECT * FROM contratosliofuga clf WHERE clf.id_contrato = '$idContrato'");
            //Verificar que contrato no este ya en la tabla
            if($existeContrato == null){
                //Consultar mensaje de cancelacion contrato por lio fuga
                $cambios = DB::select("SELECT hco.cambios FROM historialcontrato hco WHERE hco.id_contrato = '$idContrato' AND hco.tipomensaje = '1' ORDER BY hco.created_at DESC LIMIT 1");
                $cambio = "";
                //Existe el mensaje de cambio?
                if($cambios != null){
                    $cambio = $cambios[0]->cambios;
                }
                //Insertar nuevo registro en tabla contratosliofuga
                DB::table("contratosliofuga")->insert([
                    'id_contrato' => $idContrato,
                    'id_franquicia' => $contrato->id_franquicia,
                    'nombre' => $contrato->nombre,
                    'colonia' => $contrato->colonia,
                    'calle' => $contrato->calle,
                    'numero' => $contrato->numero,
                    'telefono' => $contrato->telefono,
                    'cambios' => $cambio,
                    'created_at' => Carbon::now()
                ]);
            }
        }

        return 'Insercion de contratos terminada';
    }

}
