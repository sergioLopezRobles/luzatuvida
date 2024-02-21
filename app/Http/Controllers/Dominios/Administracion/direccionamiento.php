<?php

namespace App\Http\Controllers\Dominios\Administracion;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;

class direccionamiento extends Controller
{
    public function redireccionar(){
        if(Auth::check()){
            $mensajeArmazones = "Armazones proximas por agotarse:<br>";
            $banderaNotificacionArmazonAgotada = (getenv('NOTIFICACION_ARMAZONAGOTADA') == "true")? true : false;

            switch(Auth::user()->rol_id){
                case 6:                         //ADMINISTRATIVO
                    $idUsuario = Auth::user()->id;
                    $existeUsuario = DB::select("SELECT id_franquicia FROM usuariosfranquicia where id_usuario = '$idUsuario'");
                    if($existeUsuario == null || $existeUsuario[0]->id_franquicia == ""){
                        Auth::logout();
                        return redirect()->route('login')->with("alerta", 'Usuario sin sucursal asignada');
                    }else {
                        $idFranquicia = DB::select("SELECT id_franquicia FROM usuariosfranquicia WHERE id_usuario = '$idUsuario'");
                        $ahora = Carbon::now();
                        $idFra = $idFranquicia[0]->id_franquicia;
                        $totalRenovacion = DB::select("SELECT COUNT(u.id) AS total FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                                WHERE u.renovacion IS NOT NULL AND  uf.id_franquicia= '$idFra' AND  STR_TO_DATE(u.renovacion,'%Y-%m-%d') <= ' $ahora'");
                        $totalGarantias = DB::select("SELECT COUNT(g.id_contrato) AS total FROM garantias g  WHERE  g.estadogarantia IN (0)
                                                                AND STR_TO_DATE(g.created_at,'%Y-%m-%d') = '".$ahora->format('Y-m-d')."'");
                        $mensajeGarantias = "";

                        if($totalGarantias[0]->total == 1){
                            $mensajeGarantias = "<br><br>Hay ".$totalGarantias[0]->total." reporte de garatia nuevo.";
                        }if($totalGarantias[0]->total > 1){
                            $mensajeGarantias = "<br><br>Hay ".$totalGarantias[0]->total." reportes de garatias nuevos.";
                        }


                        $mensajeCumpleanios = "";
                        //Obtener cumpleaños de usuarios - Cumpleaños de un dia aterior y proximos 5 dias
                        $cumpleanios = DB::select("SELECT UPPER(u.name) AS name, STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d') as fecha
                                                         FROM users u WHERE STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d')  BETWEEN STR_TO_DATE(DATE_SUB(NOW() , INTERVAL 1 DAY),'%Y-%m-%d')
                                                         AND STR_TO_DATE(DATE_ADD(NOW() , INTERVAL 5 DAY),'%Y-%m-%d') ORDER BY fecha ASC");

                        if(sizeof($cumpleanios) > 0){
                            foreach ($cumpleanios as $cumpleanio){
                                $mensajeCumpleanios = $mensajeCumpleanios . $cumpleanio->name . " | " . $cumpleanio->fecha . "<br>";
                            }
                        }

                        $armazones = array();
                        //Bandera de notificacion de armazones activada?
                        if($banderaNotificacionArmazonAgotada){
                            //Obtener armazones con 10 o menos piezas disponibles
                            $armazones = DB::select("SELECT * FROM producto p WHERE id_tipoproducto = '1' AND STR_TO_DATE(p.created_at,'%Y-%m-%d') >= '2022-11-13'
                                                       AND p.piezas <= 10 ORDER BY p.nombre, p.color ASC");
                            foreach ($armazones as $armazon){
                                $mensajeArmazones = $mensajeArmazones . "$armazon->nombre | $armazon->color | Piezas: $armazon->piezas <br>";
                            }

                        }

                        $mensajeAlerta = "";
                        if($totalRenovacion[0]->total > 0){
                            $mensajeAlerta = "Existen ".$totalRenovacion[0]->total." contratos para renovar.";
                        } if($totalGarantias[0]->total > 0){
                            $mensajeAlerta = $mensajeAlerta . $mensajeGarantias;
                        } if(sizeof($cumpleanios) > 0){
                            $mensajeAlerta = $mensajeAlerta . "<br><br> Proximos cumpleaños: <br>" . $mensajeCumpleanios;
                        } if(sizeof($armazones) > 0){
                            $mensajeAlerta = $mensajeAlerta . "<br>" . $mensajeArmazones;
                        }

                        //Obtener citas agendadas pendientes - fecha de cita mayor o igual a fecha actual
                        $citasPendientes = DB::select("SELECT ac.nombre, ac.telefono, ac.fechacitaagendada, ac.horacitaagendada FROM agendacitas ac WHERE ac.id_franquicia = '$idFra' AND ac.estadocita = 0
                                                             AND STR_TO_DATE(ac.fechacitaagendada,'%Y-%m-%d') >= STR_TO_DATE('$ahora','%Y-%m-%d')
                                                             ORDER BY ac.fechacitaagendada, ac.horacitaagendada");

                        if(sizeof($citasPendientes) > 0){
                            //Existen citas pendientes
                            $mensajeAlerta = "Existen citas agendadas pendientes por atender. <br>" . "Cita mas cercana: " . $citasPendientes[0]->nombre . ", " . $citasPendientes[0]->fechacitaagendada . ", " . $citasPendientes[0]->horacitaagendada. "<br><br>" . $mensajeAlerta;
                        }

                        if($totalRenovacion[0]->total > 0 || $totalGarantias[0]->total > 0 || sizeof($armazones) > 0 || sizeof($citasPendientes) || sizeof($cumpleanios)){
                            return redirect()->route('listapoliza',$idFranquicia[0]->id_franquicia)
                                             ->with("mensaje",$mensajeAlerta);
                        }
                        return redirect()->route('listapoliza',$idFranquicia[0]->id_franquicia);
                    }
                case 7:                         //DIRECTOR
                    $idUsuario = Auth::user()->id;
                    $idFranquicia = DB::select("SELECT id_franquicia FROM usuariosfranquicia WHERE id_usuario = '$idUsuario'");
                    $idFra = $idFranquicia[0]->id_franquicia;

                    $armazones = array();
                    //Bandera de notificacion de armazones activada?
                    if($banderaNotificacionArmazonAgotada) {
                        //Obtener armazones con 10 o menos piezas disponibles

                        $armazones = DB::select("SELECT * FROM producto p WHERE id_tipoproducto = '1' AND STR_TO_DATE(p.created_at,'%Y-%m-%d') >= '2022-11-13'
                                                    AND p.piezas <= 10 ORDER BY p.nombre, p.color ASC");

                        foreach ($armazones as $armazon) {
                            $mensajeArmazones = $mensajeArmazones . "$armazon->nombre | $armazon->color | Piezas: $armazon->piezas <br>";
                        }
                    }

                    //Validar si no existen armazones por agotarse
                    if(sizeof($armazones) == 0){
                        //Limpiar cadena de notificacion
                        $mensajeArmazones = "";
                    }

                    //Obtener citas agendadas pendientes
                    $mensajeCitasPendientes = "";
                    $ahora = Carbon::now();
                    $citasPendientes = DB::select("SELECT ac.nombre, ac.telefono, ac.fechacitaagendada, ac.horacitaagendada FROM agendacitas ac WHERE ac.id_franquicia = '$idFra' AND ac.estadocita = 0
                                                             AND STR_TO_DATE(ac.fechacitaagendada,'%Y-%m-%d') >= STR_TO_DATE('$ahora','%Y-%m-%d')
                                                             ORDER BY ac.fechacitaagendada, ac.horacitaagendada");

                    if(sizeof($citasPendientes) > 0){
                        //Existen citas pendientes
                        $mensajeCitasPendientes = "Existen citas agendadas pendientes por atender. <br>" . "Cita mas cercana: " . $citasPendientes[0]->nombre . ", " . $citasPendientes[0]->fechacitaagendada . ", " . $citasPendientes[0]->horacitaagendada . "<br><br>";
                    }

                    $mensajeCumpleanios = "";
                    //Obtener cumpleaños de usuarios - Cumpleaños de un dia aterior y proximos 5 dias
                    $cumpleanios = DB::select("SELECT UPPER(u.name) AS name, STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d') as fecha
                                                         FROM users u WHERE STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d')  BETWEEN STR_TO_DATE(DATE_SUB(NOW() , INTERVAL 1 DAY),'%Y-%m-%d')
                                                         AND STR_TO_DATE(DATE_ADD(NOW() , INTERVAL 5 DAY),'%Y-%m-%d') ORDER BY fecha ASC");

                    if(sizeof($cumpleanios) > 0){
                        $mensajeCumpleanios = "Cumpleaños proximos. <br>";
                        foreach ($cumpleanios as $cumpleanio){
                            $mensajeCumpleanios = $mensajeCumpleanios . $cumpleanio->name . " | " . $cumpleanio->fecha . "<br>";
                        }
                        $mensajeCumpleanios = $mensajeCumpleanios . "<br>";
                    }

                    //Existen armazones por agotarse o citas pendientes?
                    if(sizeof($armazones) > 0 || sizeof($citasPendientes) > 0 || sizeof($cumpleanios) > 0 ){
                        //Si existen armazones, citas pendientes lanzar la notificacion al iniciar sesion
                        return redirect()->route('listafranquicia')
                                         ->with("mensaje",$mensajeCitasPendientes .  $mensajeCumpleanios . $mensajeArmazones);
                    }
                    return redirect()->route('listafranquicia');

                case 8:                         //PRINCIPAL
                    $idUsuario = Auth::user()->id;
                    $idFranquicia = DB::select("SELECT id_franquicia FROM usuariosfranquicia WHERE id_usuario = '$idUsuario'");
                    $id_franquicia = $idFranquicia[0]->id_franquicia;

                    $armazones = array();
                    //Bandera de notificacion de armazones activada?
                    if($banderaNotificacionArmazonAgotada) {
                        //Obtener armazones con 10 o menos piezas disponibles
                        $armazones = DB::select("SELECT * FROM producto p WHERE id_tipoproducto = '1' AND STR_TO_DATE(p.created_at,'%Y-%m-%d') >= '2022-11-13'
                                                    AND p.piezas <= 10 ORDER BY p.nombre, p.color ASC");
                        foreach ($armazones as $armazon) {
                            $mensajeArmazones = $mensajeArmazones . "$armazon->nombre | $armazon->color | Piezas: $armazon->piezas <br>";
                        }

                    }

                    //Validar si no existen armazones por agotarse
                    if(sizeof($armazones) == 0){
                        //Limpiar cadena de notificacion
                        $mensajeArmazones = "";
                    }

                    //Obtener citas agendadas pendientes
                    $mensajeCitasPendientes = "";
                    $ahora = Carbon::now();
                    $citasPendientes = DB::select("SELECT ac.nombre, ac.telefono, ac.fechacitaagendada, ac.horacitaagendada FROM agendacitas ac WHERE ac.id_franquicia = '$id_franquicia' AND ac.estadocita = 0
                                                             AND STR_TO_DATE(ac.fechacitaagendada,'%Y-%m-%d') >= STR_TO_DATE('$ahora','%Y-%m-%d')
                                                             ORDER BY ac.fechacitaagendada, ac.horacitaagendada");

                    if(sizeof($citasPendientes) > 0){
                        //Existen citas pendientes
                        $mensajeCitasPendientes = "Existen citas agendadas pendientes por atender. <br>" . "Cita mas cercana: " . $citasPendientes[0]->nombre . ", " . $citasPendientes[0]->fechacitaagendada . ", " . $citasPendientes[0]->horacitaagendada . "<br><br>";
                    }

                    $mensajeCumpleanios = "";
                    //Obtener cumpleaños de usuarios - Cumpleaños de un dia aterior y proximos 5 dias
                    $cumpleanios = DB::select("SELECT UPPER(u.name) AS name, STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d') as fecha
                                                         FROM users u WHERE STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d')  BETWEEN STR_TO_DATE(DATE_SUB(NOW() , INTERVAL 1 DAY),'%Y-%m-%d')
                                                         AND STR_TO_DATE(DATE_ADD(NOW() , INTERVAL 5 DAY),'%Y-%m-%d') ORDER BY fecha ASC");

                    if(sizeof($cumpleanios) > 0){
                        $mensajeCumpleanios = "Cumpleaños proximos. <br>";
                        foreach ($cumpleanios as $cumpleanio){
                            $mensajeCumpleanios = $mensajeCumpleanios . $cumpleanio->name . " | " . $cumpleanio->fecha . "<br>";
                        }
                        $mensajeCumpleanios = $mensajeCumpleanios . "<br>";
                    }

                    //Existen armazones por agotarse o citas pendientes?
                    if(sizeof($armazones) > 0 || sizeof($citasPendientes) > 0 || sizeof($cumpleanios) > 0){
                        //Si existen armazones, citas pendientes  lanzar la notificacion al iniciar sesion
                        return redirect()->route('listapoliza',$idFranquicia[0]->id_franquicia)
                            ->with("mensaje",$mensajeCitasPendientes . $mensajeCumpleanios . $mensajeArmazones );
                    }
                    return redirect()->route('listapoliza',$idFranquicia[0]->id_franquicia);

                case 15:                        //CONFIRMACIONES
                    return redirect()->route('listaconfirmaciones');
                case 16: //LABORATORIO PRINCIPAL
                    return redirect()->route('listalaboratorio');
                case 4:
                    //COBRANZA
                case 17:
                    //CHOFER
                $idUsuario = Auth::user()->id;
                $existeUsuario = DB::select("SELECT id_franquicia FROM usuariosfranquicia where id_usuario = '$idUsuario'");
                if($existeUsuario == null || $existeUsuario[0]->id_franquicia == ""){
                    Auth::logout();
                    return redirect()->route('login')->with("alerta", 'Usuario sin sucursal asignada');
                }else {
                    $vehiculoAsignado = DB::select("SELECT v.indice, v.estado FROM vehiculos v INNER JOIN vehiculosusuarios vu ON vu.id_vehiculo = v.indice
                                                          WHERE vu.id_usuario = '$idUsuario'");

                    return redirect()->route('vervehiculo',[$existeUsuario[0]->id_franquicia, $vehiculoAsignado[0]->indice]);
                }
                case 18: //REDES
                case 20: //INNOVACION
                    $idUsuario = Auth::user()->id;
                    $existeUsuario = DB::select("SELECT id_franquicia FROM usuariosfranquicia where id_usuario = '$idUsuario'");

                    if($existeUsuario == null || $existeUsuario[0]->id_franquicia == ""){
                        Auth::logout();
                        return redirect()->route('login')->with("alerta", 'Usuario sin sucursal asignada');
                    }else {
                        $idFranquicia = DB::select("SELECT id_franquicia FROM usuariosfranquicia WHERE id_usuario = '$idUsuario'");
                        return redirect()->route('listavacantesredes', $idFranquicia[0]->id_franquicia);
                    }

                default:
                    Auth::logout();
                    return redirect()->route('login');
            }
        }else{
            return redirect()->route('login');
        }
    }

    public function estadofranquicia(){
        try{
            $idUsuario = Auth::user()->id;
            $existeUsuario = DB::select("SELECT id_franquicia FROM usuariosfranquicia where id_usuario = '$idUsuario'");
            if(!$existeUsuario){
                Auth::logout();
                return redirect()->route('login');
            }else{
                $estadoSucursal =  DB::select("SELECT estado FROM configfranquicia WHERE id_franquicia = '".$existeUsuario[0]->id_franquicia."'");
                if($estadoSucursal != null){
                     if($estadoSucursal[0]->estado == 1){
                        return redirect()->route('redireccionar');
                    }else{
                        return view('administracion.franquicia.estadofranquicia');
                    }
                }else{
                    Auth::logout();
                    return redirect()->route('login');
                }
            }
        }catch(\Exception $e){
             \Log::info("Error: ".$e->getMessage());
        }

    }

}
