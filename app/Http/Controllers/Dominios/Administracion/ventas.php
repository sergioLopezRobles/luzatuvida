<?php

namespace App\Http\Controllers\Dominios\Administracion;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ventas extends Controller
{

    public function mostrarMovimientosVentas($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {

            $usuarioDefault = DB::select("SELECT u.id FROM users u INNER JOIN usuariosfranquicia uf ON u.id=uf.id_usuario WHERE u.rol_id IN (12,13) AND uf.id_franquicia='" . $idFranquicia . "'
                                                ORDER BY u.name LIMIT 1");
            $usuario = $usuarioDefault[0] -> id;
            $hoy = Carbon::now()->format('Y-m-d');
            $fechaIni = $hoy;
            $fechaFin = $hoy;
            $queryUsuarioHistorialContrato = "";
            $queryUsuarioContrato = "";
            $queryFechasHistorialContrato = "";
            $queryFechasContratos = "";
            $mostrarSeccionFecha = false;

            try {

                $existeUsuario = DB::select("SELECT id, rol_id FROM users WHERE id = '$usuario'");
                $rolUsuario = null;
                if ($existeUsuario != null) {
                    //Seleccionaron un usuario en especifico
                    $idUsuario = $existeUsuario[0]->id; //Se obtiene id del usuario
                    $rolUsuario = $existeUsuario[0]->rol_id; //Se obtiene el rol del usuario
                    $queryUsuarioHistorialContrato = " AND hc.id_usuarioC = '$usuario' ";
                    if($rolUsuario == 12) {
                        //Rol optometrista
                        $queryUsuarioContrato = " AND c.id_optometrista = '$idUsuario'";
                    }else {
                        //Rol asistente
                        $queryUsuarioContrato = " AND c.id_usuariocreacion = '$idUsuario'";
                    }
                }


                $queryFechasHistorialContrato = " AND DATE(hc.created_at) BETWEEN '$fechaIni' AND '$fechaFin' ";
                $queryFechasContratos = " AND DATE(c.created_at) BETWEEN '$fechaIni' AND '$fechaFin' ";
                $mostrarSeccionFecha = true;



                $movimientos = DB::select("SELECT users.name, hc.id_contrato, hc.cambios, hc.created_at,(SELECT u.name FROM users u WHERE u.id = c.id_optometrista) as optometrista
                                                    FROM historialcontrato as hc
                                                    INNER JOIN usuariosfranquicia ON usuariosfranquicia.id_usuario = hc.id_usuarioC
                                                    INNER JOIN users ON users.id = usuariosfranquicia.id_usuario
                                                    INNER JOIN contratos c ON c.id = hc.id_contrato
                                                    WHERE usuariosfranquicia.id_franquicia = '$idFranquicia'
                                                    AND users.rol_id IN (12,13)" . $queryFechasHistorialContrato . $queryUsuarioHistorialContrato .
                                                    " ORDER BY hc.created_at DESC");

                $gotas = null;
                $polizas = null;
                $gotasFecha = null;
                $polizasFecha = null;
                if($rolUsuario == 12 || $rolUsuario == null) {
                    //Obtener gotas y polizas si el rol es optometrista o no se selecciono ningun usuario

                    $consulta = "SELECT SUM((SELECT cp.piezas
                                                                FROM contratoproducto cp
                                                                INNER JOIN producto p
                                                                ON cp.id_producto = p.id
                                                                WHERE  p.id_tipoproducto = 3
                                                                AND cp.id_contrato = c.id AND c.estatus_estadocontrato IN (2,4,5,6,7,8,10,11,12)  LIMIT 1)) AS gotas,
                                                            SUM((SELECT cp.piezas
                                                                FROM contratoproducto cp
                                                                INNER JOIN producto p
                                                                ON cp.id_producto = p.id
                                                                WHERE  p.id_tipoproducto = 2
                                                                AND cp.id_contrato = c.id AND c.estatus_estadocontrato IN (2,4,5,6,7,8,10,11,12) LIMIT 1)) AS polizas
                                                            FROM contratos c
                                                            WHERE c.id_franquicia = '$idFranquicia'" . $queryUsuarioContrato;

                    $gotasYPolizas = DB::select($consulta);

                    if($gotasYPolizas != null) {
                        if($gotasYPolizas[0]->gotas != null) {
                            $gotas = $gotasYPolizas[0]->gotas;
                        }else {
                            $gotas = 0;
                        }

                        if($gotasYPolizas[0]->polizas != null) {
                            $polizas = $gotasYPolizas[0]->polizas;
                        }else {
                            $polizas = 0;
                        }
                    }

                    if ($mostrarSeccionFecha) {
                        //Se selecciono un margen de fechas
                        //Obtener gotas y polizas en un margen de fechas

                        $consulta = "SELECT SUM((SELECT cp.piezas
                                                                FROM contratoproducto cp
                                                                INNER JOIN producto p
                                                                ON cp.id_producto = p.id
                                                                WHERE  p.id_tipoproducto = 3
                                                                AND cp.id_contrato = c.id AND c.estatus_estadocontrato IN (2,4,5,6,7,8,10,11,12))) AS gotas,
                                                            SUM((SELECT cp.piezas
                                                                FROM contratoproducto cp
                                                                INNER JOIN producto p
                                                                ON cp.id_producto = p.id
                                                                WHERE  p.id_tipoproducto = 2
                                                                AND cp.id_contrato = c.id AND c.estatus_estadocontrato IN (2,4,5,6,7,8,10,11,12))) AS polizas
                                                            FROM contratos c
                                                            WHERE c.id_franquicia = '$idFranquicia'" . $queryUsuarioContrato . $queryFechasContratos;

                        $gotasYPolizasFecha = DB::select($consulta);

                        if($gotasYPolizasFecha[0]->gotas != null) {
                            $gotasFecha = $gotasYPolizasFecha[0]->gotas;
                        }else {
                            $gotasFecha = 0;
                        }

                        if($gotasYPolizasFecha[0]->polizas != null) {
                            $polizasFecha = $gotasYPolizasFecha[0]->polizas;
                        }else {
                            $polizasFecha = 0;
                        }

                    }
                }

                //Obtener los todos los contratos
                $totalContratos = DB::select("SELECT c.estatus_estadocontrato FROM contratos c WHERE c.id_franquicia = '$idFranquicia'
                                                    AND c.estatus_estadocontrato IN (2,4,5,6,7,8,10,11,12)" . $queryUsuarioContrato);
                $cancelados = 0;
                $rechazados = 0;
                $aprobados = 0;
                $todos = 0;

                //Recorrer contratos y hacer el conteo de los estatus
                foreach ($totalContratos as $contrato) {
                    if($contrato->estatus_estadocontrato == 6) { //Cancelados
                        $cancelados++;
                    }

                    if($contrato->estatus_estadocontrato == 8) { //Rechazados
                        $rechazados++;
                    }

                    if($contrato->estatus_estadocontrato == 2 || $contrato->estatus_estadocontrato == 4
                        || $contrato->estatus_estadocontrato == 5 || $contrato->estatus_estadocontrato == 7
                        || $contrato->estatus_estadocontrato == 10 || $contrato->estatus_estadocontrato == 11
                        || $contrato->estatus_estadocontrato == 12) { //Aprobados
                        $aprobados++;
                    }

                    $todos++;
                }


                $canceladosFecha = 0;
                $rechazadosFecha = 0;
                $aprobadosFecha = 0;
                $todosFecha = 0;
                if ($mostrarSeccionFecha) {
                    //Obtener los todos los contratos en un margen de fechas
                    $totalContratosFecha = DB::select("SELECT c.estatus_estadocontrato FROM contratos c WHERE c.id_franquicia = '$idFranquicia'
                                                             AND c.estatus_estadocontrato IN (2,4,5,6,7,8,10,11,12)" . $queryUsuarioContrato . $queryFechasContratos);

                    //Recorrer contratos en un margen de fechas y hacer el conteo de los estatus
                    foreach ($totalContratosFecha as $contrato) {
                        if($contrato->estatus_estadocontrato == 6) { //Cancelados
                            $canceladosFecha++;
                        }

                        if($contrato->estatus_estadocontrato == 8) { //Rechazados
                            $rechazadosFecha++;
                        }

                        if($contrato->estatus_estadocontrato == 2 || $contrato->estatus_estadocontrato == 4
                            || $contrato->estatus_estadocontrato == 5 || $contrato->estatus_estadocontrato == 7
                            || $contrato->estatus_estadocontrato == 10 || $contrato->estatus_estadocontrato == 11
                            || $contrato->estatus_estadocontrato == 12) { //Aprobados
                            $aprobadosFecha++;
                        }

                        $todosFecha++;
                    }

                }

                $usuarios = DB::select("SELECT u.id,u.name FROM users u INNER JOIN usuariosfranquicia uf ON u.id=uf.id_usuario WHERE u.rol_id IN (12,13)
                                              AND uf.id_franquicia='" . $idFranquicia . "' ORDER BY u.name");

                $franquicia = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id ='" . $idFranquicia . "'");

                return view('administracion.cobranza.movimientos.movimientosventas',
                    ['usuarios' => $usuarios,
                        'franquicia' => $franquicia,
                        'movimientos' => $movimientos,
                        'idFranquicia' => $idFranquicia,
                        'idUsuario' => $existeUsuario,
                        'fechaIni' => $fechaIni,
                        'fechaFin' => $fechaFin,
                        'cancelados' => $cancelados,
                        'rechazados'=> $rechazados,
                        'aprobados'=> $aprobados,
                        'todos'=> $todos,
                        'canceladosFecha' => $canceladosFecha,
                        'rechazadosFecha'=> $rechazadosFecha,
                        'aprobadosFecha'=> $aprobadosFecha,
                        'todosFecha'=> $todosFecha,
                        'gotas'=> $gotas,
                        'polizas'=> $polizas,
                        'gotasFecha'=> $gotasFecha,
                        'polizasFecha'=> $polizasFecha,
                        'mostrarSeccionFecha'=> $mostrarSeccionFecha,
                        'rolUsuario' => $rolUsuario
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

    public function filtrarMovimientosVentas($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {

            $usuario = request('usuario');
            $fechaIni = request('inicio');
            $fechaFin = request('fin');
            $queryUsuarioHistorialContrato = "";
            $queryUsuarioContrato = "";
            $queryFechasHistorialContrato = "";
            $queryFechasContratos = "";
            $mostrarSeccionFecha = false;

            try {

                $existeUsuario = DB::select("SELECT id, rol_id, name FROM users WHERE id = '$usuario'");
                $rolUsuario = null;
                if ($existeUsuario != null) {
                    //Seleccionaron un usuario en especifico
                    $idUsuario = $existeUsuario[0]->id; //Se obtiene id del usuario
                    $rolUsuario = $existeUsuario[0]->rol_id; //Se obtiene el rol del usuario
                    $queryUsuarioHistorialContrato = " AND hc.id_usuarioC = '$usuario' ";
                    if($rolUsuario == 12) {
                        //Rol optometrista
                        $queryUsuarioContrato = " AND c.id_optometrista = '$idUsuario'";
                    }else {
                        //Rol asistente
                        $queryUsuarioContrato = " AND c.id_usuariocreacion = '$idUsuario'";
                    }
                }

                $hoy = Carbon::now()->format('Y-m-d');

                if(strlen($fechaIni) == 0){
                    $fechaIni = $hoy;
                }
                if(strlen($fechaFin) == 0){
                    $fechaFin = $hoy;
                }

                if (strlen($fechaIni) > 0 && strlen($fechaFin) > 0) {
                    $fechaIniParse = Carbon::parse($fechaIni)->format('Y-m-d');
                    $fechaFinParse = Carbon::parse($fechaFin)->format('Y-m-d');

                    if ($fechaFinParse < $fechaIniParse) {
                        return back()->with('alerta', 'La fecha inicial debe ser menor o igual a la final.');
                    }
                    $queryFechasHistorialContrato = " AND DATE(hc.created_at) BETWEEN '$fechaIni' AND '$fechaFin' ";
                    $queryFechasContratos = " AND DATE(c.created_at) BETWEEN '$fechaIni' AND '$fechaFin' ";
                    $mostrarSeccionFecha = true;
                }


                $movimientos = DB::select("SELECT users.name, hc.id_contrato, hc.cambios, hc.created_at,(SELECT u.name FROM users u WHERE u.id = c.id_optometrista) as optometrista
                                                    FROM historialcontrato as hc
                                                    INNER JOIN usuariosfranquicia ON usuariosfranquicia.id_usuario = hc.id_usuarioC
                                                    INNER JOIN users ON users.id = usuariosfranquicia.id_usuario
                                                    INNER JOIN contratos c ON c.id = hc.id_contrato
                                                    WHERE usuariosfranquicia.id_franquicia = '$idFranquicia'
                                                    AND users.rol_id IN (12,13)" . $queryFechasHistorialContrato . $queryUsuarioHistorialContrato .
                    " ORDER BY hc.created_at DESC");

                $gotas = null;
                $polizas = null;
                $gotasFecha = null;
                $polizasFecha = null;
                if($rolUsuario == 12 || $rolUsuario == null) {
                    //Obtener gotas y polizas si el rol es optometrista o no se selecciono ningun usuario

                    $consulta = "SELECT SUM((SELECT cp.piezas
                                                                FROM contratoproducto cp
                                                                INNER JOIN producto p
                                                                ON cp.id_producto = p.id
                                                                WHERE  p.id_tipoproducto = 3
                                                                AND cp.id_contrato = c.id AND c.estatus_estadocontrato IN (2,4,5,6,7,8,10,11,12)  LIMIT 1)) AS gotas,
                                                            SUM((SELECT cp.piezas
                                                                FROM contratoproducto cp
                                                                INNER JOIN producto p
                                                                ON cp.id_producto = p.id
                                                                WHERE  p.id_tipoproducto = 2
                                                                AND cp.id_contrato = c.id AND c.estatus_estadocontrato IN (2,4,5,6,7,8,10,11,12) LIMIT 1)) AS polizas
                                                            FROM contratos c
                                                            WHERE c.id_franquicia = '$idFranquicia'" . $queryUsuarioContrato;

                    $gotasYPolizas = DB::select($consulta);

                    if($gotasYPolizas != null) {
                        if($gotasYPolizas[0]->gotas != null) {
                            $gotas = $gotasYPolizas[0]->gotas;
                        }else {
                            $gotas = 0;
                        }

                        if($gotasYPolizas[0]->polizas != null) {
                            $polizas = $gotasYPolizas[0]->polizas;
                        }else {
                            $polizas = 0;
                        }
                    }

                    if ($mostrarSeccionFecha) {
                        //Se selecciono un margen de fechas
                        //Obtener gotas y polizas en un margen de fechas

                        $consulta = "SELECT SUM((SELECT cp.piezas
                                                                FROM contratoproducto cp
                                                                INNER JOIN producto p
                                                                ON cp.id_producto = p.id
                                                                WHERE  p.id_tipoproducto = 3
                                                                AND cp.id_contrato = c.id AND c.estatus_estadocontrato IN (2,4,5,6,7,8,10,11,12))) AS gotas,
                                                            SUM((SELECT cp.piezas
                                                                FROM contratoproducto cp
                                                                INNER JOIN producto p
                                                                ON cp.id_producto = p.id
                                                                WHERE  p.id_tipoproducto = 2
                                                                AND cp.id_contrato = c.id AND c.estatus_estadocontrato IN (2,4,5,6,7,8,10,11,12))) AS polizas
                                                            FROM contratos c
                                                            WHERE c.id_franquicia = '$idFranquicia'" . $queryUsuarioContrato . $queryFechasContratos;

                        $gotasYPolizasFecha = DB::select($consulta);

                        if($gotasYPolizasFecha[0]->gotas != null) {
                            $gotasFecha = $gotasYPolizasFecha[0]->gotas;
                        }else {
                            $gotasFecha = 0;
                        }

                        if($gotasYPolizasFecha[0]->polizas != null) {
                            $polizasFecha = $gotasYPolizasFecha[0]->polizas;
                        }else {
                            $polizasFecha = 0;
                        }

                    }
                }

                //Obtener los todos los contratos
                $totalContratos = DB::select("SELECT c.estatus_estadocontrato FROM contratos c WHERE c.id_franquicia = '$idFranquicia'
                                                    AND c.estatus_estadocontrato IN (2,4,5,6,7,8,10,11,12)" . $queryUsuarioContrato);
                $cancelados = 0;
                $rechazados = 0;
                $aprobados = 0;
                $todos = 0;

                //Recorrer contratos y hacer el conteo de los estatus
                foreach ($totalContratos as $contrato) {
                    if($contrato->estatus_estadocontrato == 6) { //Cancelados
                        $cancelados++;
                    }

                    if($contrato->estatus_estadocontrato == 8) { //Rechazados
                        $rechazados++;
                    }

                    if($contrato->estatus_estadocontrato == 2 || $contrato->estatus_estadocontrato == 4
                        || $contrato->estatus_estadocontrato == 5 || $contrato->estatus_estadocontrato == 7
                        || $contrato->estatus_estadocontrato == 10 || $contrato->estatus_estadocontrato == 11
                        || $contrato->estatus_estadocontrato == 12) { //Aprobados
                        $aprobados++;
                    }

                    $todos++;
                }


                $canceladosFecha = 0;
                $rechazadosFecha = 0;
                $aprobadosFecha = 0;
                $todosFecha = 0;
                if ($mostrarSeccionFecha) {
                    //Obtener los todos los contratos en un margen de fechas
                    $totalContratosFecha = DB::select("SELECT c.estatus_estadocontrato FROM contratos c WHERE c.id_franquicia = '$idFranquicia'
                                                             AND c.estatus_estadocontrato IN (2,4,5,6,7,8,10,11,12)" . $queryUsuarioContrato . $queryFechasContratos);

                    //Recorrer contratos en un margen de fechas y hacer el conteo de los estatus
                    foreach ($totalContratosFecha as $contrato) {
                        if($contrato->estatus_estadocontrato == 6) { //Cancelados
                            $canceladosFecha++;
                        }

                        if($contrato->estatus_estadocontrato == 8) { //Rechazados
                            $rechazadosFecha++;
                        }

                        if($contrato->estatus_estadocontrato == 2 || $contrato->estatus_estadocontrato == 4
                            || $contrato->estatus_estadocontrato == 5 || $contrato->estatus_estadocontrato == 7
                            || $contrato->estatus_estadocontrato == 10 || $contrato->estatus_estadocontrato == 11
                            || $contrato->estatus_estadocontrato == 12) { //Aprobados
                            $aprobadosFecha++;
                        }

                        $todosFecha++;
                    }

                }

                $usuarios = DB::select("SELECT u.id,u.name FROM users u INNER JOIN usuariosfranquicia uf ON u.id=uf.id_usuario WHERE u.rol_id IN (12,13)
                                              AND uf.id_franquicia='" . $idFranquicia . "' ORDER BY u.name");

                $franquicia = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id ='" . $idFranquicia . "'");

                //Registrar movimiento
                $mensajeHistorial = "Filtro 'movimientos ventas' por periodo de fechas: " .$fechaIni . " a " . $fechaFin ." usuario: '".$existeUsuario[0]->name . "'";
                DB::table('historialsucursal')->insert([
                    'id_usuarioC' => Auth::user()->id, 'id_franquicia' => $idFranquicia,
                    'tipomensaje' => '7', 'created_at' => Carbon::now(), 'cambios' => $mensajeHistorial, 'seccion' => '4'
                ]);

                return view('administracion.cobranza.movimientos.movimientosventas',
                    ['usuarios' => $usuarios,
                        'franquicia' => $franquicia,
                        'movimientos' => $movimientos,
                        'idFranquicia' => $idFranquicia,
                        'idUsuario' => $existeUsuario,
                        'fechaIni' => $fechaIni,
                        'fechaFin' => $fechaFin,
                        'cancelados' => $cancelados,
                        'rechazados'=> $rechazados,
                        'aprobados'=> $aprobados,
                        'todos'=> $todos,
                        'canceladosFecha' => $canceladosFecha,
                        'rechazadosFecha'=> $rechazadosFecha,
                        'aprobadosFecha'=> $aprobadosFecha,
                        'todosFecha'=> $todosFecha,
                        'gotas'=> $gotas,
                        'polizas'=> $polizas,
                        'gotasFecha'=> $gotasFecha,
                        'polizasFecha'=> $polizasFecha,
                        'mostrarSeccionFecha'=> $mostrarSeccionFecha,
                        'rolUsuario' => $rolUsuario
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

}
