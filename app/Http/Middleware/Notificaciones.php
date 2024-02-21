<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;

class Notificaciones
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check() && (((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 16))){
            $now = Carbon::now();
            $fechaNotificacion = Carbon::parse($now)->format('Y-m-d');

            //Bandera notificicaciones armazon por agotarse actuvada/desactivado
            $banderaNotificacionArmazonAgotada = (getenv('NOTIFICACION_ARMAZONAGOTADA') == "true")? true : false;

            //Verificar si hay inserciones de notificaciones recientemente
            $notificaciones = DB::select("SELECT * FROM notificacionesvisualizaciones nv WHERE nv.fechanotificacion = '$fechaNotificacion'");

            if($notificaciones != null){
                //Existen notificaciones  registradas por desplegar

                //Cadenas para conformar alerta
                $mensajeProducto = "";
                $mensajeCita = "";
                $mensajeNotificacion = "";

                foreach ($notificaciones as $notificacion){
                    $id_notificacionesvisualizacion = $notificacion->indice;
                    $numeroNotificacionesPProducto= $notificacion->numeronotificaciones;
                    $tipoNotificacion = $notificacion->tiponotificacion;

                    $notificacionesMostradas = DB::select("SELECT COUNT(n.indice) AS notificacionesmostradas FROM notificaciones n
                                                             WHERE n.id_notificacionesvisualizacion = '$id_notificacionesvisualizacion'
                                                             AND STR_TO_DATE(n.created_at,'%Y-%m-%d') = '$fechaNotificacion'");

                    if($notificacionesMostradas[0]->notificacionesmostradas < $numeroNotificacionesPProducto){

                        //Tipo de notificacion -> mensaje a mostrar en alerta
                        switch($tipoNotificacion){
                            case 0:
                                //PRODUCTOS
                                $id_producto = $notificacion->id_producto;

                                $armazon = DB::select("SELECT p.nombre, p.color, p.piezas FROM producto p WHERE p.id = '$id_producto'");

                                //Cadena de producto para notificacion
                                $mensajeProducto = $mensajeProducto . $armazon[0]->nombre . " | " . $armazon[0]->color . " | Piezas: " . $armazon[0]->piezas . "<br>";

                                break;
                            case 1:
                                //CITAS
                                if((Auth::user()->rol_id) != 16){
                                    //Rol diferente a laboratorio
                                    $referencia_cita = $notificacion->referencia_cita;

                                    $citasPendientes = DB::select("SELECT ac.nombre, ac.telefono, ac.fechacitaagendada, ac.horacitaagendada
                                                                     FROM agendacitas ac WHERE ac.referencia = '$referencia_cita' AND ac.estadocita = 0");

                                    if($citasPendientes != null){
                                        $mensajeCita = $mensajeCita . "Cita: " . $citasPendientes[0]->fechacitaagendada . ", " . $citasPendientes[0]->horacitaagendada. "<br>";
                                    }
                                }
                                break;
                        }

                        //Rol diferente de laboratorio
                        if((Auth::user()->rol_id) != 16 || ($tipoNotificacion != 0) || ($tipoNotificacion == 0 && $banderaNotificacionArmazonAgotada == true)){
                            //Insertar nuevo registro en la tabla de notificaciones
                            DB::table("notificaciones")->insert([
                                "id_notificacionesvisualizacion" => $id_notificacionesvisualizacion,
                                "created_at" => Carbon::now()
                            ]);
                        }
                    }

                }

                //Formar mensaje de notificacion
                if($mensajeProducto != null && $banderaNotificacionArmazonAgotada){
                    //Se presenta un mensaje de armazon por agotarse
                    $mensajeNotificacion = "Armazones agotadas:<br>" . $mensajeProducto;

                    if($mensajeCita != ""){
                        //Si se tendra un mensaje de cita agendada -> generar un doble salto de linea para formato
                        $mensajeNotificacion = $mensajeNotificacion . "<br>";
                    }
                }

                if($mensajeCita != ""){
                    //Se tiene alerta de cita agendada
                    $mensajeNotificacion = $mensajeNotificacion . "Existen citas agendadas pendientes por atender. <br>" . $mensajeCita;
                }

                if($mensajeNotificacion != ""){
                    //Se tuvo algun mensaje para desplegar en la notificacion
                    session()->flash('mensaje',$mensajeNotificacion);
                }

            }
        }

        return $next($request);
    }
}
