<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SesionCaducadaUsuario
{

    public function handle(Request $request, Closure $next)
    {

        try{

            $idUsuario = Auth::user()->id;
            $usuario = DB::select("SELECT uf.id_franquicia as id_franquicia
                                            FROM users u
                                            INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                            WHERE u.id = '$idUsuario'
                                            AND u.rol_id not in (8,16)
                                            AND u.id NOT IN (SELECT e.id_usuario FROM excepciones e WHERE e.id_usuario = '$idUsuario')");

            if ($usuario != null) {
                //Existe usuario

                $idFranquicia = $usuario[0]->id_franquicia;

                $poliza = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' ORDER BY created_at DESC LIMIT 1");

                if ($poliza != null) {
                    //Existe poliza

                    $idPolizaActual = $poliza[0]->id;
                    $asistenciausuario = DB::select("SELECT id_tipoasistencia FROM asistencia WHERE id_poliza = '$idPolizaActual' AND id_usuario = '$idUsuario'
                                                                    ORDER BY created_at DESC LIMIT 1");

                    if ($asistenciausuario != null) {
                        //Existe usuario en la lista de asistencia

                        $controlentradasalidausuario = DB::select("SELECT horafin FROM controlentradasalidausuario WHERE id_usuario = '$idUsuario'
                                                                                ORDER BY created_at DESC LIMIT 1");

                        if ($controlentradasalidausuario != null) {
                            //Existe control entrada salida del usuario
                            $horafin = $controlentradasalidausuario[0]->horafin;
                            $horafin = Carbon::parse($horafin)->second(0);
                            $horaactual = Carbon::now()->second(0);

                            if ($horaactual->greaterThan($horafin) && $asistenciausuario[0]->id_tipoasistencia == 0) {
                                //Ya paso la hora final y tiene falta
                                //\Log::info("YA PASO LA HORA FIN");
                                Auth::logout();
                                return redirect()->route('login')->with("alerta", 'Se cerró la sesión por inasistencia.');
                            } else {
                                //La hora actual no ha pasado la hora final
                                //\Log::info("NO A PASADO LA HORA FIN");
                            }

                        }

                    } else {
                        //No existe usuario en la lista de asistencia
                        Auth::logout();
                        return redirect()->route('login')->with("alerta", 'Se cerró la sesión por inasistencia.');
                    }

                }

            }

        }catch(\Exception $e){
            ("Error: ".$e->getMessage());
        }

        return $next($request);

    }

}
