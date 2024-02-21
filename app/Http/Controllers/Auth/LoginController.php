<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Dominios\Administracion\Controller;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
     public function authenticated()
     {

        $idUsuario = Auth::id();
        $validarLogin = DB::select("SELECT id FROM users WHERE id = '$idUsuario' AND logueado = 2");
        if($validarLogin != null && Auth::user()->rol_id != 6 && Auth::user()->rol_id != 7 && Auth::user()->rol_id != 8 && Auth::user()->rol_id != 15
            && Auth::user()->rol_id != 16 && Auth::user()->rol_id != 18) {
            Auth::logout();
            return redirect()->route('login')->with("alerta", 'Ya tienes una sesión iniciada en la aplicación movil.');
        }else {

            $validarEstatus = DB::select("SELECT ts.tipo as tipo FROM users u INNER JOIN tiposuspension ts ON ts.id = u.estatus WHERE u.id = '$idUsuario' AND u.estatus IN (0,2)");
            if($validarEstatus != null) {
                //Ha sido suspendido el usuario
                Auth::logout();
                return redirect()->route('login')->with("alerta", 'Has sido suspendido ' . $validarEstatus[0]->tipo . ", comunicate con tu jefe inmediato.");
            }else {
                //No ha sido suspendido el usuario
                $franquicia = DB::select("SELECT uf.id_franquicia FROM usuariosfranquicia uf WHERE uf.id_usuario = '$idUsuario'");
                if ($franquicia != null) {
                    $logueoCorrecto = false;

                    //Validar si es rol de chofer o cobranza que tenga vehiculo asignado
                    if (Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) {
                        $vehiculoAsignado = DB::select("SELECT v.numserie, v.estado FROM vehiculos v INNER JOIN vehiculosusuarios vu ON vu.id_vehiculo = v.indice
                                                          WHERE vu.id_usuario = '$idUsuario'");
                        if ($vehiculoAsignado != null) {
                            //Tiene vehiculo asignado
                            if ($vehiculoAsignado[0]->estado == 1) {
                                //Vehiculo activo
                                $logueoCorrecto = true;
                            } else {
                                //Vehiculo a sido eliminado
                                Auth::logout();
                                return redirect()->route('login')->with("alerta", 'El vehículo asignado fue dado de baja del sistema, comunicate con administración.');
                            }
                        } else {
                            //No cuenta con vehiculo asignado
                            Auth::logout();
                            return redirect()->route('login')->with("alerta", 'Usuario sin vehículo asignado.');
                        }
                    } else {
                        //Resto de roles
                        $logueoCorrecto = true;
                    }

                    //Es un logueo correcto ?
                    if ($logueoCorrecto) {

                        // Solo se actualizara la sesion a usuarios que pueden acceder a la pagina.
                        DB::table("users")->where("id", "=", $idUsuario)->update([
                            "logueado" => 1
                        ]);
                        //Registrar inicio de sesion en historialsucursal
                        DB::table('historialsucursal')->insert([
                            'id_usuarioC' => $idUsuario,
                            'id_franquicia' => $franquicia[0]->id_franquicia, 'tipomensaje' => '0',
                            'created_at' => Carbon::now(), 'cambios' => 'Inicio sesion', 'seccion' => '2'
                        ]);

                        return redirect()->route('redireccionar');
                    }
                }
                Auth::logout();
                return redirect()->route('login')->with("alerta", 'No tienes los permisos necesarios para acceder.');
            }

        }
     }

    // protected $redirectTo = '/inicio';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function logout() {
        DB::table("users")->where("id","=",Auth::id())->update([
            "logueado" => 0
        ]);
        //Registrar cierre de sesion en historialsucursal
        $id_usuario = Auth::id();
        $franquicia = DB::select("SELECT uf.id_franquicia FROM usuariosfranquicia uf WHERE uf.id_usuario = '$id_usuario'");
        DB::table('historialsucursal')->insert([
            'id_usuarioC' => $id_usuario,
            'id_franquicia' => $franquicia[0]->id_franquicia, 'tipomensaje' => '0',
            'created_at' => Carbon::now(), 'cambios' => 'Cerro sesion', 'seccion' => '2'
        ]);
        Auth::logout();
        return redirect()->route('login');
    }


}
