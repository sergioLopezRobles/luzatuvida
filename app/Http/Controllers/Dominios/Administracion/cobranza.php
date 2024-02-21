<?php

namespace App\Http\Controllers\Dominios\Administracion;

use App\Clases\contratosGlobal;
use App\Clases\globalesServicioWeb;
use Carbon\Carbon;
use App\Clases\polizaGlobales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class cobranza extends Controller
{

    public function mostrarMovimientos($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {
            $idUsuario = null; // Valor para verificar si entro a validacion de usuario Logueado al intentar hacer corte
            $hoy = Carbon::now()->format('Y-m-d');
            $fechaInicial = $hoy;
            $fechaFinal = $hoy;

            try {

                $usuarios = DB::select("SELECT u.id,u.name,(SELECT z.zona FROM zonas z WHERE z.id = u.id_zona) as zona, u.supervisorcobranza
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id=uf.id_usuario
                                              WHERE u.rol_id = 4  AND uf.id_franquicia='" . $idFranquicia . "' ORDER BY u.id_zona");

                $franquicia = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id ='" . $idFranquicia . "'");

                $totalContratos = DB::select("SELECT  SUM((SELECT COUNT(co.id) FROM contratos co WHERE co.id_zona = u.id_zona AND co.pago = 1
                                                                    AND co.estatus_estadocontrato IN (2,4,12) AND co.id_zona = u.id_zona)) as semanal,
                                                             SUM((SELECT COUNT(co.id) FROM contratos co WHERE co.id_zona = u.id_zona AND co.pago = 2
                                                                    AND co.estatus_estadocontrato IN (2,4,12) AND co.id_zona = u.id_zona)) as quincenal,
                                                             SUM((SELECT COUNT(co.id) FROM contratos co WHERE co.id_zona = u.id_zona AND co.pago = 4
                                                                    AND co.estatus_estadocontrato IN (2,4,12) AND co.id_zona = u.id_zona)) as mensual,
                                                             SUM((SELECT COUNT(co.id) FROM contratos co WHERE co.id_zona = u.id_zona
                                                                    AND co.pago IN (0,1,2,4) AND co.estatus_estadocontrato  = '5' AND co.id_zona = u.id_zona)) as pagado,
                                                             SUM((SELECT COUNT(co.id) FROM contratos co WHERE co.id_zona = u.id_zona
                                                                    AND co.pago IN (0,1,2,4) AND co.estatus_estadocontrato  = '6' AND co.id_zona = u.id_zona)) as cancelado

                                                    FROM users u
                                                    INNER JOIN usuariosfranquicia uf
                                                    ON uf.id_usuario = u.id
                                                    WHERE
                                                    u.rol_id = 4
                                                    AND uf.id_franquicia = '$idFranquicia'");

                $opciones = array();

                return view('administracion.cobranza.movimientos.movimientos',
                    ['usuarios' => $usuarios,
                        'franquicia' => $franquicia,
                        'opciones' => $opciones,
                        'idUsuario' => $idUsuario,
                        'fechaInicial' => $fechaInicial,
                        'fechaFinal' => $fechaFinal,
                        'totalContratos' => $totalContratos,
                        'idFranquicia' => $idFranquicia
                    ]);

            } catch (\Exception $e) {

                \Log::info("Error: " . $e);
                return back()->with('error', 'Tuvimos un error, por favor verifica que los datos esten correctos.');

            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    private static function validarTotalAbono($total)
    {
        if ($total > 0) {
            return true;
        }
        return false;
    }

    public function cobranzamovimientosreiniciarcorte(Request $request)
    {

        $idUsuario =  $request->input('idUsuario');
        $asistencia =  $request->input('asistencia');

        $abono = DB::select("SELECT indice FROM abonos WHERE id_usuario = '$idUsuario' AND id_corte IS NULL ORDER BY created_at DESC LIMIT 1");

        if ($abono != null) {
            //Existe por lo menos un abono sin corte

            $existeUsuario = DB::select("SELECT name, logueado, id_zona, supervisorcobranza FROM users WHERE id = '$idUsuario'");

            if ($existeUsuario != null) {
                //Se encontro el usuario

                //Obtener franquicia del cobrador
                $franquicia = DB::select("SELECT id_franquicia FROM usuariosfranquicia WHERE id_usuario = '$idUsuario'");
                $idFranquicia = $franquicia[0]->id_franquicia;

                $nombreUsuario = $existeUsuario[0]->name;
                $logueado = $existeUsuario[0]->logueado;

                if ($logueado == 0) {
                    //Reiniciar el corte

                    $polizaGlobales = new polizaGlobales(); //Creamos una nueva instancia de polizaGlobales
                    $contratosGlobal = new contratosGlobal; //Creamos una nueva instancia de contratosGlobal

                    //Datos para validar supervisiones de vehiculos
                    $vehiculoAsignado = DB::select("SELECT vu.id_vehiculo FROM vehiculosusuarios vu WHERE vu.id_usuario = '$idUsuario'");
                    $supervisionesVehiculo = null;

                    if ($vehiculoAsignado != null) {
                        //Si tiene vehiculo asignado verificar las supervisiones registradas para ese vehiculo
                        $idVehiculoAsignado = $vehiculoAsignado[0]->id_vehiculo;
                        $supervisionesVehiculo = DB::select("SELECT vs.estado, TIMESTAMPDIFF(HOUR,vs.created_at,DATE_FORMAT(NOW(), '%Y-%m-%d %h:%m:%S')) as horasCreacion
                                                                FROM vehiculossupervision vs WHERE vs.id_usuario = '$idUsuario' AND vs.id_vehiculo= '$idVehiculoAsignado' ORDER BY vs.created_at DESC LIMIT 1");
                    }

                    if ($vehiculoAsignado == null || $supervisionesVehiculo != null) {
                        //No tiene vehiculo asignado o
                        //Tiene vehiculo asignado y tiene supervisiones registradas

                        if ($vehiculoAsignado == null || ($vehiculoAsignado != null && ($supervisionesVehiculo != null && ($supervisionesVehiculo[0]->estado == 1
                                        || $supervisionesVehiculo[0]->horasCreacion <= 24)))) {
                            //No tiene vehiculos asignados
                            //Cuenta con supervision aprobada o esta pendiente pero fue creada antes de 24 horas

                            $polizaActual = DB::select("SELECT id, created_at, estatus FROM poliza WHERE id_franquicia = '$idFranquicia' ORDER BY created_at DESC LIMIT 1"); //Traemos la poliza actual.
                            $idPoliza = $polizaActual == null ? "" : $polizaActual[0]->id;
                            $estatusPoliza = $polizaActual == null ? "" : $polizaActual[0]->estatus;

                            $hoy = Carbon::now();
                            //$hoy = Carbon::parse("2023-09-04");

                            $ultimocorte = DB::select("SELECT created_at FROM historialcortes WHERE id_cobrador = '$idUsuario' ORDER BY created_at DESC LIMIT 1");

                            if ($ultimocorte != null && Carbon::parse($ultimocorte[0]->created_at)->format('Y-m-d') == Carbon::parse($hoy)->format('Y-m-d') && $estatusPoliza == 0) {
                                //Ultimo corte es igual al dia de hoy y estado de la poliza es igual a NO TERMINADO
                                $response = ['bandera' => false, 'mensaje' => 'Para poder realizar el corte nuevamente en el dia al cobrador ' . $nombreUsuario . ' deberas terminar antes la poliza'];
                                return response()->json($response);
                            }

                            //Validar si usuario tiene registro de asistencia con poliza del dia
                            $polizaGlobales::buscarasistenciapolizaeinsertar($idFranquicia, $idUsuario);

                            if ($asistencia == 0 || $asistencia == 1 || $asistencia == 2) {
                                //Asistencia es igual a falta, asistencia o retardo
                                DB::table("asistencia")->where("id_usuario", "=", $idUsuario)->where('id_poliza', '=', $idPoliza)->update([
                                    "id_tipoasistencia" => $asistencia,
                                    "updated_at" => $hoy
                                ]);
                            }

                            $id_corte = DB::table('historialcortes')->insertGetId([
                                'id_cobrador' => $idUsuario, 'id_usuarioC' => Auth::user()->id, 'created_at' => Carbon::now()
                            ]);

                            DB::table("abonos")->where("id_usuario", "=", $idUsuario)->where("corte", "=", "0")->update([
                                "corte" => "2",
                                "id_corte" => $id_corte
                            ]);

                            //Actualizar corte en tabla abonoscontratostemporalessincronizacion
                            DB::table("abonoscontratostemporalessincronizacion")->where("id_usuario", "=", $idUsuario)->where("corte", "=", "0")->update([
                                "corte" => "2"
                            ]);

                            $abonos = DB::select("SELECT a.id_contrato FROM abonos a
                                                    WHERE a.id_corte = '$id_corte'
                                                    AND a.id_usuario = '$idUsuario'
                                                    ORDER BY a.created_at DESC");

                            if ($abonos != null) {
                                //Existen abonos

                                shuffle($abonos); //Cambiar aleatoriamente los registros de posicion

                                $array = array(); //Bandera de ids de contratos para que no se mande 2 veces
                                foreach ($abonos as $abono) {

                                    if (!in_array($abono->id_contrato, $array)) {
                                        //No exite el id_contrato en el array

                                        $consultasumaabonos = DB::select("SELECT SUM(a.abono) as sumaabonos FROM abonos a
                                                    WHERE a.id_corte = '$id_corte'
                                                    AND a.id_contrato = '" . $abono->id_contrato . "'");

                                        $ultimoabono = "";
                                        if ($consultasumaabonos != null) {
                                            //Existen abonos
                                            $ultimoabono = $consultasumaabonos[0]->sumaabonos;
                                            $consultafechacreacionultimoabono = DB::select("SELECT a.created_at as ultimoabonofechacreacion FROM abonos a
                                                    WHERE a.id_corte = '$id_corte'
                                                    AND a.id_contrato = '" . $abono->id_contrato . "'
                                                    ORDER BY a.created_at DESC LIMIT 1");
                                            if ($consultafechacreacionultimoabono != null) {
                                                //Se obtiene fecha creacion del ultimo abono y se concatena al formato deseado
                                                $ultimoabono = $consultasumaabonos[0]->sumaabonos . " / " . $consultafechacreacionultimoabono[0]->ultimoabonofechacreacion;
                                            }
                                        }

                                        //Insertar en tabla contratoscortellamada
                                        DB::table('contratoscortellamada')->insert([
                                            'id_contrato' => $abono->id_contrato,
                                            'id_corte' => $id_corte,
                                            'id_historialcontrato' => null,
                                            'id_cobrador' => $idUsuario,
                                            'ultimoabono' => $ultimoabono,
                                            'created_at' => Carbon::now()
                                        ]);

                                        array_push($array, $abono->id_contrato); //Se agrega el id_contrato al array para que no se repita

                                        if (count($array) == 10) {
                                            //Ya estan los 10 registros en el array
                                            break;
                                        }
                                    }
                                }
                            }

                            $id_zona = $existeUsuario[0]->id_zona;

                            $bandera = true;
                            $existeCobradorEliminado = DB::select("SELECT indice FROM cobradoreseliminados WHERE id_zona = '$id_zona'");
                            if ($existeCobradorEliminado != null && $existeUsuario[0]->supervisorcobranza == 0) {
                                $bandera = false;
                            }

                            //SECCION PARA AGREGAR ABONOS A LA POLIZA ACTUAL
                            if ($estatusPoliza == 0 && $bandera) {
                                //Aun no ha sido terminada la poliza

                                //Traemos la ultima poliza de la semana actual.
                                $polizaAnterior = DB::select("SELECT * FROM poliza WHERE id_franquicia = '$idFranquicia'
                                                                AND STR_TO_DATE(created_at,'%Y-%m-%d') < STR_TO_DATE('" . $polizaActual[0]->created_at . "','%Y-%m-%d')
                                                                ORDER BY created_at DESC LIMIT 1");//Traemos la ultima poliza sin importar si es de la semana actual o no.
                                $polizaAnteriorId = $polizaAnterior == null ? "" : $polizaAnterior[0]->id;

                                $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($id_zona);
                                foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                    //Recorrido cobradores
                                    DB::update("UPDATE abonos
                                        SET poliza = '$idPoliza'
                                        WHERE id_usuario = '" . $cobradorAsignadoAZona->id . "'
                                        AND poliza IS NULL");//Actualizamos los abonos
                                }

                                //Seleccionamos abonos ingresados por administradores, directores y principal
                                $abonos = DB::select("SELECT a.id, a.indice
                                                        FROM abonos a
                                                        INNER JOIN users u
                                                        ON a.id_usuario = u.id
                                                        WHERE a.poliza IS NULL
                                                        AND a.id_zona = '$id_zona'
                                                        AND u.rol_id IN (6,7,8)");

                                if ($abonos != null) {
                                    //Existen abonos a actualizar
                                    foreach ($abonos as $abono) {
                                        $idAbono = $abono->id;
                                        $indice = $abono->indice;
                                        DB::update("UPDATE abonos
                                                                SET poliza  = '$idPoliza'
                                                                WHERE id = '$idAbono'
                                                                AND indice = '$indice'");//Actualizamos todos los abonos
                                    }
                                }

                                $polizaGlobales::calculoDeCobranza($idFranquicia, $idPoliza, $polizaAnteriorId, $idUsuario);

                                DB::table('historialpoliza')->insert([
                                    'id_usuarioC' => Auth::user()->id, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                                    'cambios' => " Reinicio el corte de $nombreUsuario"
                                ]);

                            }

                            //Datos ticket de abonos corte
                            $abonosCorteTicket = DB::select("SELECT c.id, SUM(a.abono) AS abono
                                                            FROM abonos a
                                                            INNER JOIN contratos c
                                                            ON c.id = a.id_contrato
                                                            INNER JOIN users u
                                                            ON u.id = a.id_usuario
                                                            WHERE u.rol_id  = '4'
                                                            AND a.id_corte = '$id_corte'
                                                            AND c.id_franquicia  = '$idFranquicia'
                                                            AND a.id_usuario  = '$idUsuario'
                                                            GROUP BY c.id");
                            $nombreCobrador = $existeUsuario[0]->name;
                            $jsonAbonosCorte = json_encode($abonosCorteTicket);
                            $fechaActual = Carbon::parse($hoy)->format('Y-m-d H:i:s');

                            //Ingresos en efectivo del corte
                            $abonosEfectivo = DB::select("SELECT SUM(a.abono) AS abonosEfectivo
                                                            FROM abonos a
                                                            INNER JOIN contratos c
                                                            ON c.id = a.id_contrato
                                                            INNER JOIN users u
                                                            ON u.id = a.id_usuario
                                                            WHERE u.rol_id  = '4'
                                                            AND a.id_corte = '$id_corte'
                                                            AND a.metodopago = '0'
                                                            AND c.id_franquicia  = '$idFranquicia'
                                                            AND a.id_usuario  = '$idUsuario'");

                            $abonosSEfectivo = 0;
                            if ($abonosEfectivo != null && $abonosEfectivo[0]->abonosEfectivo != null) {
                                $abonosSEfectivo = $abonosEfectivo[0]->abonosEfectivo;
                            }

                            $response = ['bandera' => true, 'mensaje' => "Se reinicio el corte de '$nombreUsuario' correctamente", 'abonosCorteTicket' => $jsonAbonosCorte,
                                'abonosSEfectivo' => $abonosSEfectivo, 'nombreCobrador' => $nombreCobrador, 'fechaActual' => $fechaActual];

                        } else {
                            //No cumplio con las validaciones de estar aprobada la supervision o si esta pendiente que tenga menos de 24 horas de creacion
                            $response = ['bandera' => false, 'mensaje' => 'Para reiniciar el corte es necesario que ' . $nombreUsuario . ' cuente con su ultima supervisión vehicular aprobada o no haya sido sido creada hace mas de 24 horas.'];
                        }

                    } else {
                        //No tiene ninguna supervision registrada
                        $response = ['bandera' => false, 'mensaje' => 'Para reiniciar el corte es necesario que ' . $nombreUsuario . ' cuente con al menos una supervisión de su vehículo.'];
                    }

                } else {
                    //No se puede reiniciar el corte
                    $response = ['bandera' => false, 'mensaje' => 'Para reiniciar el corte es necesario que ' . $nombreUsuario . ' cierre sesión'];

                }
            }

        }else {
            //No se puede reiniciar el corte
            $response = ['bandera' => false, 'mensaje' => 'No se detectaron abonos para realizar el corte'];
        }

        return response()->json($response);

    }

    public function movimientostiemporeal(Request $request){

        $idFranquicia = $request->input('idFranquicia');
        $usuario = $request->input('idUsuario');
        $fechaIni = $request->input('fechaInicio');
        $fechaFin = $request->input('fechaFin');
        $opcion = $request->input('opcion');
        $idUsuarioActual = $request->input('idUsuarioActual');

        $validacionUsuario = "";
        $fechasAbonoCobranza = "";
        $ultimocorte = null;
        $totalContadorContratosConAbonos = null;
        $totalContadorContratosConAbonosC = null;
        $mensajeHistorial = "";
        $ticketVisita = null;
        $fechaCorteAnterior = Carbon::parse(Carbon::now())->format("Y-m-d H:i:s");
        $fechaLimiteTicketVisita = Carbon::parse(Carbon::now())->format("Y-m-d H:i:s");
        $validacionFechaCorteSeleccionado = "";

        $hoy = Carbon::now()->format('Y-m-d');
        if(strlen($fechaIni) == 0){
            $fechaIni = $hoy;

        } if(strlen($fechaFin) == 0){
            $fechaFin = $hoy;
        }

        try {

            //Validacion si es el mismo usuario
            if($idUsuarioActual != $usuario && $opcion > 1) {
                $opcion = 1;
            }

            $existeUsuario = DB::select("SELECT u.id,u.id_zona,u.name,uf.id_franquicia FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id WHERE u.id = '$usuario'");
            $contratosUsuario = "";
            $abonoUsuario = "";
            $opciones = array();
            $historialcontratousuario = "";

            if ($existeUsuario != null) { //Esta seleccionado un usuario?
                //Si esta seleccionado
                $idUsuario = $existeUsuario[0]->id;
                $usuarioFranquicia = $existeUsuario[0]->id_franquicia;
                $idZonaUsuario =  $existeUsuario[0]->id_zona;
                $validacionUsuario = " AND a.id_usuario = '$usuario'";
                if($opcion == 1) {
                    //Traera el corte
                    $validacionUsuario = " AND a.id_usuario = '$usuario' AND a.corte = '0'";
                }
                $contratosUsuario = " AND u.id = '$idUsuario'";
                $abonoUsuario = " AND a.id_usuario = '$idUsuario'";
                $ultimocorte = DB::select("SELECT (SELECT u.name FROM users u WHERE u.id = hco.id_usuarioC) as nombreUsuarioC, hco.created_at FROM historialcortes hco
                                                     WHERE hco.id_cobrador = '$idUsuario' ORDER BY hco.created_at DESC limit 1");

                if($ultimocorte != null) {
                    //Existe un corte al menos ya realizado
                    $fechaCorteAnterior = Carbon::parse($ultimocorte[0]->created_at)->format("Y-m-d H:i:s");
                    $validacionFechaCorteSeleccionado = " AND hc.created_at > '$fechaCorteAnterior'";
                    $ultimocorte = $ultimocorte[0]->nombreUsuarioC . " | " . $ultimocorte[0]->created_at;
                }else {
                    //No existe un corte previo ya realizado
                    $ultimocorte = "No se ha realizado ningun corte";
                    $validacionFechaCorteSeleccionado = " AND hc.created_at < '$fechaCorteAnterior'";
                }

                $opciones = DB::select("SELECT * FROM historialcortes hc WHERE hc.indice IN (SELECT a.id_corte FROM abonos a WHERE a.id_franquicia = '$idFranquicia'
                                                  AND a.id_usuario = '$idUsuario') AND hc.id_cobrador = '$idUsuario' ORDER BY hc.created_at DESC");
                $historialcontratousuario = " AND hc.id_usuarioC = '$usuario'";
            }

            if($opcion == 0) {
                //Traera los movimientos
                $fechasAbonoCobranza = " AND DATE(a.created_at) BETWEEN '$fechaIni' AND '$fechaFin' ";
            }

            $corte = " ";

            if ($opcion == 1) {
                //Consulta de corte

                $consulta = "SELECT a.indice,users.name, a.id_contrato, c.nombre, a.abono, a.created_at,a.metodopago,a.folio,c.pago,c.total,c.abonominimo,a.coordenadas  FROM abonos a
                                                    INNER JOIN usuariosfranquicia ON usuariosfranquicia.id_usuario = a.id_usuario
                                                    INNER JOIN users ON users.id = usuariosfranquicia.id_usuario
                                                    INNER JOIN contratos c ON c.id = a.id_contrato
                                                    WHERE usuariosfranquicia.id_franquicia = '$idFranquicia'
                                                    AND users.rol_id = 4" . $validacionUsuario .
                                                    " ORDER BY a.created_at DESC";
                $movimientos = DB::select($consulta);
                $corte = " AND a.corte = 0 ";

                $fechaInicial = DB::select("SELECT DATE(a.created_at) as created_at FROM abonos a
                                                    INNER JOIN usuariosfranquicia ON usuariosfranquicia.id_usuario = a.id_usuario
                                                    INNER JOIN users ON users.id = usuariosfranquicia.id_usuario
                                                    INNER JOIN contratos c ON c.id = a.id_contrato
                                                    WHERE usuariosfranquicia.id_franquicia = '$idFranquicia'
                                                    AND users.rol_id = 4" . $validacionUsuario .
                                                    " ORDER BY a.created_at ASC LIMIT 1");

                $fechaFinal = DB::select("SELECT DATE(a.created_at) as created_at FROM abonos a
                                                    INNER JOIN usuariosfranquicia ON usuariosfranquicia.id_usuario = a.id_usuario
                                                    INNER JOIN users ON users.id = usuariosfranquicia.id_usuario
                                                    INNER JOIN contratos c ON c.id = a.id_contrato
                                                    WHERE usuariosfranquicia.id_franquicia = '$idFranquicia'
                                                    AND users.rol_id = 4" . $validacionUsuario .
                                                    " ORDER BY a.created_at DESC LIMIT 1");

                //Traer registro de tickets de visita referente al corte actual
                $ticketVisita = DB::select("SELECT * FROM historialcontrato hc WHERE hc.id_usuarioC = '$idUsuario'
                                                   AND hc.tipomensaje = '8' " . $validacionFechaCorteSeleccionado);

                $mensajeHistorial = "Filtro 'abonos cobranza' por usuario: '" . $existeUsuario[0]->name . ", corte actual";
            }else if ($opcion == 0){
                //Consulta movimientos

                $consulta = "SELECT a.indice,users.name, a.id_contrato, c.nombre, a.abono, a.created_at,a.metodopago,a.folio,c.pago,c.total,c.abonominimo,a.coordenadas FROM abonos a
                                                    INNER JOIN usuariosfranquicia ON usuariosfranquicia.id_usuario = a.id_usuario
                                                    INNER JOIN users ON users.id = usuariosfranquicia.id_usuario
                                                    INNER JOIN contratos c ON c.id = a.id_contrato
                                                    WHERE usuariosfranquicia.id_franquicia = '$idFranquicia'
                                                    AND users.rol_id = 4" . $fechasAbonoCobranza . $validacionUsuario .
                                                    " ORDER BY a.created_at DESC";
                $movimientos = DB::select($consulta);

                //Selecciono cobrador?
                if($existeUsuario != null){
                    //Cobrador seleccionado para checar sus movimientos
                    $mensajeHistorial = "Filtro 'abonos cobranza' por usuario: '" . $existeUsuario[0]->name . ", movimientos, periodo de fecha: " .$fechaIni . " a ".$fechaFin;
                } else{
                    //Consulto movimeintos en generar de un periodo de tiempo
                    $mensajeHistorial = "Filtro 'abonos cobranza' movimientos, periodo de fecha: " .$fechaIni . " a ".$fechaFin;
                }
            }else {
                //Consulta corte anterior

                $consulta = "SELECT a.indice,users.name, a.id_contrato, c.nombre, a.abono, a.created_at,a.metodopago,a.folio,c.pago,c.total,c.abonominimo,a.coordenadas FROM abonos a
                                                    INNER JOIN usuariosfranquicia ON usuariosfranquicia.id_usuario = a.id_usuario
                                                    INNER JOIN users ON users.id = usuariosfranquicia.id_usuario
                                                    INNER JOIN contratos c ON c.id = a.id_contrato
                                                    WHERE usuariosfranquicia.id_franquicia = '$idFranquicia'
                                                    AND users.rol_id = 4 AND a.id_corte = '$opcion'
                                                    ORDER BY a.created_at DESC";
                $movimientos = DB::select($consulta);
                $corte = " AND a.id_corte = '$opcion' ";

                $fechaInicial = DB::select("SELECT DATE(a.created_at) as created_at FROM abonos a
                                                    INNER JOIN usuariosfranquicia ON usuariosfranquicia.id_usuario = a.id_usuario
                                                    INNER JOIN users ON users.id = usuariosfranquicia.id_usuario
                                                    INNER JOIN contratos c ON c.id = a.id_contrato
                                                    WHERE usuariosfranquicia.id_franquicia = '$idFranquicia'
                                                    AND users.rol_id = 4 AND a.id_corte = '$opcion'
                                                    ORDER BY a.created_at ASC LIMIT 1");

                $fechaFinal = DB::select("SELECT DATE(a.created_at) as created_at FROM abonos a
                                                    INNER JOIN usuariosfranquicia ON usuariosfranquicia.id_usuario = a.id_usuario
                                                    INNER JOIN users ON users.id = usuariosfranquicia.id_usuario
                                                    INNER JOIN contratos c ON c.id = a.id_contrato
                                                    WHERE usuariosfranquicia.id_franquicia = '$idFranquicia'
                                                    AND users.rol_id = 4 AND a.id_corte = '$opcion'
                                                    ORDER BY a.created_at DESC LIMIT 1");


                $corteSeleccionado = DB::select("SELECT STR_TO_DATE(ccl.created_at,'%Y-%m-%d') AS fechaCorte FROM contratoscortellamada ccl
                                                WHERE id_corte = '$opcion' ORDER BY ccl.created_at DESC LIMIT 1;");

                //Traer registro de tickets de visita correspondientes a corte seleccionado
                $fechaCorteSeleccionado = DB::select("SELECT hco.created_at FROM historialcortes hco
                                                     WHERE hco.indice = '$opcion' AND hco.id_cobrador = '$idUsuario'
                                                     ORDER BY hco.created_at DESC limit 1");

                //Obtener fecha de creacion de corte seleccionado - Fecha limite para tickets de visita registrados
                if ($fechaCorteSeleccionado != null){
                    $fechaLimiteTicketVisita = $fechaCorteSeleccionado[0]->created_at;
                }

                //Obtener fecha de corte anterior - Limite inferior para obtener todos los tickets de visita registrados posterior a la fecha
                $corteAnterior = DB::select("SELECT hco.created_at FROM historialcortes hco
                                                     WHERE hco.id_cobrador = '$idUsuario' AND hco.created_at < '$fechaLimiteTicketVisita'
                                                     ORDER BY hco.created_at DESC limit 1");

                if($corteAnterior != null){
                    //Obtener fecha a partir de la cual se extraeran movimientos de tickets de visita
                    $fechaCorteAnterior = $corteAnterior[0]->created_at;
                    $validacionFechaCorteSeleccionado = " AND (hc.created_at > '$fechaCorteAnterior' AND hc.created_at <= '$fechaLimiteTicketVisita')";
                }else{
                    //No existe corte anterior - Fecha movimeintos de tickes vista menor o igual a fecha limite
                    $validacionFechaCorteSeleccionado = " AND hc.created_at <= '$fechaLimiteTicketVisita'";
                }

                $ticketVisita = DB::select("SELECT * FROM historialcontrato hc WHERE hc.id_usuarioC = '$usuario' AND hc.tipomensaje = '8' " . $validacionFechaCorteSeleccionado);

                $mensajeHistorial = "Filtro 'abonos cobranza' por usuario: '" . $existeUsuario[0]->name . ", corte: '".$corteSeleccionado[0]->fechaCorte;
            }

            //Agrupar abonos
            $contratoAbono = array();
            $movimientosTemporales = array();

            foreach ($movimientos as $movimiento){
                if (!in_array($movimiento->id_contrato . $movimiento->folio, $contratoAbono)) {
                    //No existe el abono en el arreglo - Insertar en arreglo de identificadores y arreglo de datos
                    array_push($contratoAbono, $movimiento->id_contrato . $movimiento->folio);
                    array_push($movimientosTemporales, ["contratofolio" => $movimiento->id_contrato . $movimiento->folio, "name" => $movimiento->name,
                        "id_contrato" => $movimiento->id_contrato, "nombre" => $movimiento->nombre, "abono" => $movimiento->abono, "created_at" => $movimiento->created_at,
                        "metodopago" => $movimiento->metodopago, "folio" => $movimiento->folio, "pago" => $movimiento->pago,  "total" => $movimiento->total,
                        "abonominimo" => $movimiento->abonominimo, "coordenadas" => $movimiento->coordenadas, "indice" => $movimiento->indice]);

                } else {
                    //Ya existe en el arreglo - sumar valor de abono para solo mostrar un registro
                    $indice =  array_search($movimiento->id_contrato . $movimiento->folio, $contratoAbono);
                    if($indice >= 0){
                        $movimientosTemporales[$indice]["abono"] = $movimientosTemporales[$indice]["abono"] + $movimiento->abono;
                        $movimientosTemporales[$indice]["abono"] = number_format($movimientosTemporales[$indice]["abono"], 1,".");
                    }
                }
            }

            $movimientosTemporales = array_reverse($movimientosTemporales); //Cambiar orden de regitros al reverso

            $numeroColor = 0;
            $contadorRegistrosConCoordenadas = 0;
            foreach ($movimientosTemporales as $key => $movimiento) {
                if ($movimientosTemporales[$key]['coordenadas'] != null) {
                    $movimientosTemporales[$key]['numColor'] = $numeroColor;
                    $contadorRegistrosConCoordenadas++;

                    // Verificar si se han alcanzado 5 registros con coordenadas
                    if ($contadorRegistrosConCoordenadas == 5) {
                        $contadorRegistrosConCoordenadas = 0; // Reiniciar contador
                        $numeroColor++; // Incrementar el número de color
                    }
                } else {
                    // Asignar un valor vacío si las coordenadas están vacías
                    $movimientosTemporales[$key]['numColor'] = '';
                }
            }

            $movimientosTemporales = array_reverse($movimientosTemporales); //Cambiar orden de regitros a como estaba anteriormente

            $movimientos = $movimientosTemporales;

            $totalContratos = DB::select("SELECT  SUM((SELECT COUNT(co.id) FROM contratos co WHERE co.id_zona = u.id_zona AND co.pago = 1 AND co.estatus_estadocontrato IN (2,4,12)
                                                                        AND co.id_zona = u.id_zona)) as semanal,
                                                             SUM((SELECT COUNT(co.id) FROM contratos co WHERE co.id_zona = u.id_zona AND co.pago = 2 AND co.estatus_estadocontrato IN (2,4,12)
                                                                        AND co.id_zona = u.id_zona)) as quincenal,
                                                             SUM((SELECT COUNT(co.id) FROM contratos co WHERE co.id_zona = u.id_zona AND co.pago = 4 AND co.estatus_estadocontrato IN (2,4,12)
                                                                        AND co.id_zona = u.id_zona)) as mensual,
                                                             SUM((SELECT COUNT(co.id) FROM contratos co WHERE co.id_zona = u.id_zona AND co.pago IN (0,1,2,4) AND co.estatus_estadocontrato  = '5'
                                                                        AND co.id_zona = u.id_zona)) as pagado,
                                                             SUM((SELECT COUNT(co.id) FROM contratos co WHERE co.id_zona = u.id_zona AND co.pago IN (0,1,2,4) AND co.estatus_estadocontrato  = '6'
                                                                        AND co.id_zona = u.id_zona)) as cancelado

                                                    FROM users u
                                                    INNER JOIN usuariosfranquicia uf
                                                    ON uf.id_usuario = u.id
                                                    WHERE
                                                    u.rol_id = 4
                                                    AND uf.id_franquicia = '$idFranquicia'" . $contratosUsuario);

            $contado = 0;
            $semanal = 0;
            $quincenal = 0;
            $mensual = 0;
            $contadoC = 0;
            $semanalC = 0;
            $quincenalC = 0;
            $mensualC = 0;
            $efectivoC = 0;
            $tarjetaC = 0;
            $tranferenciaC = 0;
            $cancelacionC = 0;
            $ingresosEfectivo = 0;
            $ingresosTarjeta = 0;
            $ingresosTransferencia = 0;
            $ingresosCancelacion = 0;
            $ingresosProducto = 0;
            $totalIngresosSEfectivo = 0;

            $totalIngresosC = 0;
            if (strlen($contratosUsuario) > 0) {
                //OBTENEMOS TODOS LOS ABONOS DE ADMINISTRACION

                $abonosCobranza = DB::select("SELECT a.id,c.pago,u.name,c.nombre,a.id_contrato
                                                        FROM abonos a
                                                        INNER JOIN contratos c
                                                        ON c.id = a.id_contrato
                                                        INNER JOIN users u
                                                        ON u.id = a.id_usuario
                                                        WHERE a.tipoabono != '7'
                                                        AND u.rol_id IN (6,7,8)
                                                        " . $fechasAbonoCobranza . $corte . "
                                                        AND c.id_franquicia  = '$usuarioFranquicia'
                                                        AND c.id_zona  = '$idZonaUsuario'");

                $totalContadorContratosConAbonos = 0;
                if($abonosCobranza != null) {
                    $array = array(); //Bandera de ids de promocioncontrato para que no se mande 2 veces
                    foreach ($abonosCobranza as $abonosC) {
                        if(!in_array($abonosC->id_contrato, $array)) {
                            //No existe id_contrato en el array el cual se contara
                            $totalContadorContratosConAbonos++;
                            array_push($array, $abonosC->id_contrato); //Se agrega el id_contrato al array para que este no vuelva a contarse de nuevo
                        }
                    }
                }

                //OBTENEMOS SOLO ABONOS QUE INGRESO EL COBRADOR
                $abonosCobranzaC = DB::select("SELECT a.id,c.pago,u.name,c.nombre,a.id_contrato, a.metodopago
                                                            FROM abonos a
                                                            INNER JOIN contratos c
                                                            ON c.id = a.id_contrato
                                                            INNER JOIN users u
                                                            ON u.id = a.id_usuario
                                                            WHERE a.tipoabono != '7'
                                                            AND u.rol_id  = '4'
                                                            " . $fechasAbonoCobranza . $corte . "
                                                            AND c.id_franquicia  = '$usuarioFranquicia'
                                                            AND a.id_usuario  = '$idUsuario'");

                $totalContadorContratosConAbonosC = 0;
                if($abonosCobranzaC != null) {
                    $array = array(); //Bandera de ids de promocioncontrato para que no se mande 2 veces
                    foreach ($abonosCobranzaC as $abonosCC) {
                        if(!in_array($abonosCC->id_contrato, $array)) {
                            //No existe id_contrato en el array el cual se contara
                            $totalContadorContratosConAbonosC++;
                            array_push($array, $abonosCC->id_contrato); //Se agrega el id_contrato al array para que este no vuelva a contarse de nuevo
                        }
                    }
                }

                //OBTENEMOS EL TOTAL DE INGRESOS EL COBRADOR
                $totalIngresos = DB::select("SELECT SUM(a.abono) as totalingresos
                                                    FROM abonos a
                                                    INNER JOIN contratos c ON c.id = a.id_contrato
                                                    WHERE a.id_franquicia = '$idFranquicia'" . $fechasAbonoCobranza . $abonoUsuario . $corte);

                $totalIngresosC = number_format($totalIngresos[0]->totalingresos);

                foreach ($abonosCobranza as $abono) {
                    switch ($abono->pago) { //FORMA DE PAGO - SEMANAL|QUINCENAL|MENSUAL
                        case 0://CONTADO
                            $contado++;
                            break;
                        case 1://SEMANAL
                            $semanal++;
                            break;
                        case 2://QUINCENAL
                            $quincenal++;
                            break;
                        case 4://MENSUAL
                            $mensual++;
                            break;

                    }
                }

                foreach ($abonosCobranzaC as $abonoC) {
                    switch ($abonoC->pago) { //FORMA DE PAGO - SEMANAL|QUINCENAL|MENSUAL
                        case 0://CONTADO
                            $contadoC++;
                            break;
                        case 1://SEMANAL
                            $semanalC++;
                            break;
                        case 2://QUINCENAL
                            $quincenalC++;
                            break;
                        case 4://MENSUAL
                            $mensualC++;
                            break;

                    }
                }

                foreach ($abonosCobranzaC as $abonoC) {
                    switch ($abonoC->metodopago) { //METODO DE PAGO - EFECTIVO|TARJETA|TRANSFERENCIA|CANCELACION
                        case 0://EFECTIVO
                            $efectivoC++;
                            break;
                        case 1://TARJETA
                            $tarjetaC++;
                            break;
                        case 2://TRANSFERENCIA
                            $tranferenciaC++;
                            break;
                        case 3://CANCELACION
                            $cancelacionC++;
                            break;
                    }
                }
            }

            //Ingresos en general en corte
            $ingresosC = DB::select("SELECT a.abono, a.metodopago, a.tipoabono
                                                    FROM abonos a
                                                    INNER JOIN contratos c ON c.id = a.id_contrato
                                                    WHERE a.id_franquicia = '$idFranquicia'" . $fechasAbonoCobranza . $abonoUsuario . $corte);

            //Recorrer los abonos y determinar total de ingreso por metodologia de pago, tipo de abono
            foreach ($ingresosC as $abono) {
                switch ($abono->metodopago) {
                    case 0://EFECTIVO
                        if($abono->tipoabono == 7){
                            //Tipo abono de producto
                            $ingresosProducto = $ingresosProducto + $abono->abono;
                        }else{
                            //Abono al saldo del contrato
                            $ingresosEfectivo = $ingresosEfectivo + $abono->abono;
                        }
                        break;
                    case 1://TARJETA
                        if($abono->tipoabono == 7){
                            //Tipo abono de producto
                            $ingresosProducto = $ingresosProducto + $abono->abono;
                        }else{
                            //Abono al saldo del contrato
                            $ingresosTarjeta = $ingresosTarjeta + $abono->abono;
                        }
                        break;
                    case 2://TRANSFERENCIA
                        if($abono->tipoabono == 7){
                            //Tipo abono de producto
                            $ingresosProducto = $ingresosProducto + $abono->abono;
                        }else{
                            //Abono al saldo del contrato
                            $ingresosTransferencia = $ingresosTransferencia + $abono->abono;
                        }
                        break;
                    case 3://CANCELACION
                        $ingresosCancelacion = $ingresosCancelacion + $abono->abono;
                        break;
                }
            }

            $totalIngresosSEfectivo = $ingresosEfectivo + $ingresosCancelacion + $ingresosProducto;
            $ingresosEfectivo = number_format($ingresosEfectivo);
            $ingresosTarjeta = number_format($ingresosTarjeta);
            $ingresosTransferencia = number_format($ingresosTransferencia);
            $ingresosCancelacion = number_format($ingresosCancelacion);
            $ingresosProducto = number_format($ingresosProducto);
            $totalIngresosSEfectivo = number_format($totalIngresosSEfectivo);;

            //Registrar movimiento historialSucursal
            $idUsuarioC = Auth::user()->id;
            self::insertarHistorialSucursalCobranza($idFranquicia, $idUsuarioC, $mensajeHistorial);

            //Datos ticket de abonos corte
            $fechaActual = Carbon::now();
            $nombreCobrador = "";
            $abonosCorteTicket = array();

            if($opcion != 0){
                //Seleccionaste la opcion de corte actual o un corte pasado
                $abonosCorteTicket = DB::select("SELECT c.id, SUM(a.abono) AS abono
                                                            FROM abonos a
                                                            INNER JOIN contratos c
                                                            ON c.id = a.id_contrato
                                                            INNER JOIN users u
                                                            ON u.id = a.id_usuario
                                                            WHERE u.rol_id  = '4'
                                                            " . $fechasAbonoCobranza . $corte . "
                                                            AND c.id_franquicia  = '$usuarioFranquicia'
                                                            AND a.id_usuario  = '$idUsuario'
                                                            GROUP BY c.id");
                $nombreCobrador = $existeUsuario[0]->name;
            }

            $view = view('administracion.cobranza.listas.listamovimientoscobranza',
                array('movimientos' => $movimientos,
                    'idFranquicia' => $idFranquicia,
                    'idUsuario' => $existeUsuario,
                    'totalContratos' => $totalContratos,
                    'contado'=>$contado,
                    'semanal' => $semanal,
                    'quincenal' => $quincenal,
                    'mensual' => $mensual,
                    'contadoC'=>$contadoC,
                    'semanalC' => $semanalC,
                    'quincenalC' => $quincenalC,
                    'totalIngresosC' => $totalIngresosC,
                    'mensualC' => $mensualC,
                    'ultimocorte' => $ultimocorte,
                    'totalContadorContratosConAbonos' => $totalContadorContratosConAbonos,
                    'totalContadorContratosConAbonosC' => $totalContadorContratosConAbonosC,
                    'opcion' => $opcion,
                    'abonosCorteTicket' => $abonosCorteTicket,
                    'nombreCobrador' => $nombreCobrador,
                    'fechaActual' => $fechaActual,
                    'efectivoC' => $efectivoC,
                    'tarjetaC' => $tarjetaC,
                    'transferenciaC' => $tranferenciaC,
                    'cancelacionC' => $cancelacionC,
                    'ingresosEfectivo' => $ingresosEfectivo,
                    'ingresosTarjeta' => $ingresosTarjeta,
                    'ingresosTransferencia' => $ingresosTransferencia,
                    'ingresosCancelacion' => $ingresosCancelacion,
                    'ingresosProducto' => $ingresosProducto,
                    'totalIngresosSEfectivo' => $totalIngresosSEfectivo,
                    'ticketVisita' => $ticketVisita
                ))->render();

        } catch (\Exception $e) {
            \Log::info("Error: " . $e);
            return back()->with('error', 'Tuvimos un error, por favor verifica que los datos esten correctos.');
        }

        return response()->json(array("valid" => "true",
            "view" => $view,
            "fechainicio" => $fechaIni,
            "fechafin" => $fechaFin,
            "opciones" => $opciones,
            "opcion" => $opcion,
            "usuario" => $usuario
        ));
    }

    public function marcarcontratocortellamada() {

        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {

            try {

                $indice = request('indice');
                $idContrato = request('id_contrato');
                $mensaje = request('mensaje');

                if(strlen($mensaje) == 0) {
                    return back()->with('alerta', "Campo mensaje vacío");
                }

                $contratocortellamada = DB::select("SELECT id_historialcontrato, id_corte, id_cobrador FROM contratoscortellamada
                                                    WHERE indice = '$indice'
                                                    AND id_contrato = '$idContrato'");

                $mensajeAlerta = "";
                if($contratocortellamada != null) {
                    //Existe el contratocortellamada
                    $idHistorialContrato = $contratocortellamada[0]->id_historialcontrato;
                    $idCorte = $contratocortellamada[0]->id_corte;
                    $idCobrador = $contratocortellamada[0]->id_cobrador;

                    //Obtener franquicia del cobrador
                    $franquicia = DB::select("SELECT id_franquicia FROM usuariosfranquicia WHERE id_usuario = '$idCobrador'");

                    if($franquicia != null) {
                        //Existe usuario con la franquicia

                        if($idHistorialContrato != null) {
                            //Ya se habia creado el movimiento UPDATE
                            $mensajeAlerta = "actualizo";
                            DB::table("historialcontrato")
                                ->where("id", "=", $idHistorialContrato)
                                ->where("id_contrato", "=", $idContrato)
                                ->where("tipomensaje", "=", 2)
                                ->update([
                                    'cambios' => "Llamada - " . $mensaje,
                                    'updated_at' => Carbon::now()
                                ]);

                        }else {
                            //No se habia creado el movimiento INSERT
                            $mensajeAlerta = "creo";
                            $globalesServicioWeb = new globalesServicioWeb;
                            $idHistorialContratoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');
                            DB::table('historialcontrato')->insert([
                                'id' => $idHistorialContratoAlfanumerico,
                                'id_usuarioC' => Auth::user()->id,
                                'id_contrato' => $idContrato,
                                'created_at' => Carbon::now(),
                                'cambios' => "Llamada - " . $mensaje,
                                'tipomensaje' => 2
                            ]);

                            //Actualizamos el id_historialcontrato del registro en tabla contratoscortellamada
                            DB::table("contratoscortellamada")
                                ->where("indice", "=", $indice)
                                ->where("id_contrato", "=", $idContrato)
                                ->update([
                                    'id_historialcontrato' => $idHistorialContratoAlfanumerico,
                                    'updated_at' => Carbon::now()
                                ]);
                        }
                    }
                }

                return back()->with('bien', "Se " . $mensajeAlerta . " el mensaje correctamente en el contrato.");

            } catch (\Exception $e) {
                \Log::info("Error: " . $e);
                return back()->with('error', 'Tuvimos un error, por favor verifica que los datos esten correctos.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function llamadascobranza($idFranquicia){
        if (Auth::check() &&  ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {
            try {

                $usuarios = DB::select("SELECT u.id,u.name,(SELECT z.zona FROM zonas z WHERE z.id = u.id_zona) as zona
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id=uf.id_usuario
                                              WHERE u.rol_id = 4  AND uf.id_franquicia='" . $idFranquicia . "' ORDER BY u.id_zona");

                $opciones = array();

                return view('administracion.cobranza.movimientos.llamadascobranza',
                    ['usuarios' => $usuarios,
                        'opciones' => $opciones,
                        'idFranquicia'=>$idFranquicia
                    ]);

            } catch (\Exception $e) {

                \Log::info("Error: " . $e);
                return back()->with('error', 'Tuvimos un error, por favor verifica que los datos esten correctos.');

            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function listallamadascobranza(Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {
            //Variables
            $contratoscortellamada = null;
            $opcionesCorte = null;

            $idFranquicia = $request->input('idFranquicia');
            $idUsuarioCobranza = $request->input('idUsuarioCobranza');
            $opcionCorte = $request->input('opcionCorte');

            $existeUsuario = DB::select("SELECT u.id,u.id_zona,u.name,uf.id_franquicia FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                    WHERE u.id = '$idUsuarioCobranza'");

            //Solo requiere cargar la lista de cortes
            if($opcionCorte == null){

                if($existeUsuario != null){
                    //Se extraen sus corte
                    $opcionesCorte = DB::select("SELECT * FROM historialcortes hc WHERE hc.indice IN (SELECT a.id_corte FROM abonos a WHERE a.id_franquicia = '$idFranquicia'
                                                            AND a.id_usuario = '$idUsuarioCobranza') AND hc.id_cobrador = '$idUsuarioCobranza' ORDER BY hc.created_at DESC");

                }
            }
            //Se requiere cargar la lista de llamadas
            if($opcionCorte != null){


                $datoscontratoscortellamada = DB::select("SELECT ccl.indice, ccl.id_contrato, ccl.id_cobrador, ccl.ultimoabono,
                    (SELECT hc.cambios FROM historialcontrato hc WHERE hc.id_contrato = ccl.id_contrato AND hc.id = ccl.id_historialcontrato AND hc.tipomensaje = '2') as mensaje,
                    (SELECT c.telefono FROM contratos c WHERE c.id = ccl.id_contrato AND c.id_franquicia = '$idFranquicia') as telefono,
                    (SELECT c.telefonoreferencia FROM contratos c WHERE c.id = ccl.id_contrato AND c.id_franquicia = '$idFranquicia') as telefonoreferencia,
                    (SELECT c.nombre FROM contratos c WHERE c.id = ccl.id_contrato AND c.id_franquicia = '$idFranquicia') as nombrecliente,
                    (SELECT u.name FROM users u WHERE u.id = ccl.id_cobrador) as nombrecobrador
                    FROM contratoscortellamada ccl WHERE ccl.id_corte = '$opcionCorte'");

                $contratosGlobal = new contratosGlobal;

                //Recorrido de datos para agregar al arreglo el total del contrato
                foreach ($datoscontratoscortellamada as $datoscontratocortellamada) {
                    $contratosGlobal::calculoTotal($datoscontratocortellamada->id_contrato);
                    $consultacontrato = DB::select("SELECT c.total FROM contratos c WHERE c.id = '" . $datoscontratocortellamada->id_contrato . "'");
                    if($consultacontrato != null) {
                        //Se encontro el contrato
                        $datoscontratocortellamada->total = $consultacontrato[0]->total;
                    }
                }

                $contratoscortellamada = $datoscontratoscortellamada; //Se le pasan los datos al arreglo contratoscortellamada

                //Registrar movimiento historial sucursal
                $corte = DB::select("SELECT STR_TO_DATE(ccl.created_at,'%Y-%m-%d') as fechaCorte FROM contratoscortellamada ccl WHERE ccl.id_corte = '$opcionCorte'
                                            ORDER BY ccl.created_at DESC LIMIT 1");
                $idFranquicia = $existeUsuario[0]->id_franquicia;
                $idUsuario = Auth::user()->id;
                $mensajeHistorial = "Filtro 'contratos para llamar' por usuario: '" . $existeUsuario[0]->name . "' corte: '".$corte[0]->fechaCorte ."'";
                self::insertarHistorialSucursalCobranza($idFranquicia, $idUsuario, $mensajeHistorial);
            }

            $view = view('administracion.cobranza.listas.listallamadascobranza', array(
                'contratoscortellamada' => $contratoscortellamada,
                'idFranquicia' => $idFranquicia
            ))->render();

            return response()->json(array("valid" => "true",
                "view" => $view,
                "opcionesCorte" => $opcionesCorte
            ));


        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function validacioncortecobranza($idFranquicia, $idUsuario){
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {

            $hoy = Carbon::now()->format('Y-m-d');
            $fechaInicial = $hoy;
            $fechaFinal = $hoy;

            try {

                $usuarios = DB::select("SELECT u.id,u.name,(SELECT z.zona FROM zonas z WHERE z.id = u.id_zona) as zona
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id=uf.id_usuario
                                              WHERE u.rol_id = 4  AND uf.id_franquicia='" . $idFranquicia . "' ORDER BY u.id_zona");

                $franquicia = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id ='" . $idFranquicia . "'");

                $opciones = array();

                $totalContratos = DB::select("SELECT  SUM((SELECT COUNT(co.id) FROM contratos co WHERE co.id_zona = u.id_zona AND co.pago = 1 AND co.estatus_estadocontrato IN (2,4,12)
                                                                    AND co.id_zona = u.id_zona)) as semanal,
                                                             SUM((SELECT COUNT(co.id) FROM contratos co WHERE co.id_zona = u.id_zona AND co.pago = 2 AND co.estatus_estadocontrato IN (2,4,12)
                                                                    AND co.id_zona = u.id_zona)) as quincenal,
                                                             SUM((SELECT COUNT(co.id) FROM contratos co WHERE co.id_zona = u.id_zona AND co.pago = 4 AND co.estatus_estadocontrato IN (2,4,12)
                                                                    AND co.id_zona = u.id_zona)) as mensual,
                                                             SUM((SELECT COUNT(co.id) FROM contratos co WHERE co.id_zona = u.id_zona AND co.pago IN (0,1,2,4) AND co.estatus_estadocontrato  = '5'
                                                                    AND co.id_zona = u.id_zona)) as pagado,
                                                             SUM((SELECT COUNT(co.id) FROM contratos co WHERE co.id_zona = u.id_zona AND co.pago IN (0,1,2,4) AND co.estatus_estadocontrato  = '6'
                                                                    AND co.id_zona = u.id_zona)) as cancelado

                                                    FROM users u
                                                    INNER JOIN usuariosfranquicia uf
                                                    ON uf.id_usuario = u.id
                                                    WHERE
                                                    u.rol_id = 4
                                                    AND uf.id_franquicia = '$idFranquicia'");

                return view('administracion.cobranza.movimientos.movimientos',
                    ['usuarios' => $usuarios,
                        'franquicia' => $franquicia,
                        'opciones' => $opciones,
                        'idUsuario' => $idUsuario,
                        'fechaInicial' => $fechaInicial,
                        'fechaFinal' => $fechaFinal,
                        'totalContratos' => $totalContratos,
                        'idFranquicia' => $idFranquicia
                    ]);

            } catch (\Exception $e) {

                \Log::info("Error: " . $e);
                return back()->with('error', 'Tuvimos un error, por favor verifica que los datos esten correctos.');

            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    private function insertarHistorialSucursalCobranza($idFranquicia, $idUsuarioC, $cambio){
        DB::table('historialsucursal')->insert([
            'id_usuarioC' => $idUsuarioC, 'id_franquicia' => $idFranquicia,
            'tipomensaje' => '7', 'created_at' => Carbon::now(), 'cambios' => $cambio, 'seccion' => '4'
        ]);
    }
}
