<?php

namespace App\Http\Middleware;

use App\Clases\contratosGlobal;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\DB;
use View;

class EmpresaActiva
{

    public function handle(Request $request, Closure $next)
    {
        try{

            $varIdFranquicia = $request->idFranquicia;
            $idUsuario = Auth::user()->id;
            $rolUsuario = Auth::user()->rol_id;
            $existeUsuario = DB::select("SELECT id_franquicia FROM usuariosfranquicia where id_usuario = '$idUsuario'");
            if(!$existeUsuario){
                Auth::logout();
                return redirect()->route('login');
            }else{
                $estadoSucursal =  DB::select("SELECT estado FROM configfranquicia WHERE id_franquicia = '".$existeUsuario[0]->id_franquicia."'");
                if($estadoSucursal != null){
                     if($estadoSucursal[0]->estado != 1){
                        return redirect()->route('estadofranquicia');
                    }else if(!is_null($varIdFranquicia) && ($varIdFranquicia != $existeUsuario[0]->id_franquicia)&& $rolUsuario != 7){
                         return redirect()->route('redireccionar');
                     }
                }else{
                    Auth::logout();
                    return redirect()->route('login');
                }
            }
        }catch(\Exception $e){
             ("Error: ".$e->getMessage());
        }

        //Validar ruta y permisos usuarios
        $ruta = $request->route()->getName();
        if(Auth::check() && (((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6))
            && !self::validarRutaPermisosUsuarios($ruta)) {
            //Es rol: Administracion, Director o general pero no cuenta con los permisos
            if ((Auth::user()->rol_id) == 7) {
                //Rol DIRECTOR - Direccionar lista franquicias
                return redirect()->route('listafranquicia')->with('alerta', ' No cuentas con los permisos necesarios.');
            } else {
                //Rol PRINCIPAL o ADMINISTRACION - Direccionar a polizas
                return redirect()->route('listapoliza', $existeUsuario[0]->id_franquicia)->with('alerta', ' No cuentas con los permisos necesarios.');
            }
        }

        //Almacenar sucursal actual para director
        if((Auth::user()->rol_id) == 7 && ($varIdFranquicia != null || $request->id != null)){
            if($varIdFranquicia != null){
                $idFranquicia = $varIdFranquicia;
            } else {
                $idFranquicia = $request->id;
            }

        //Almacenar ciudad sucursal - Mostrar menu para rol de director
        $sucursal = DB::select("SELECT f.ciudad FROM franquicias f WHERE f.id = '$idFranquicia'");
            if($sucursal != null){
                View::share('sucursalGlobalDirector', $sucursal[0]->ciudad);
            }
        }

        //Registrar hora ultima peticion
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
            DB::table("users")->where("id", "=", $idUsuario)->update([
                "ultimaconexion" => Carbon::now()
            ]);
        }

        return $next($request);
    }

    //Funcion: validarRutaPermisosUsuarios
    //Descripcion: Recibe el nombre de la ruta a la que se intenta acceder, Validamos si el usuario logueado tiene permisos y retorna un boolean
    private function validarRutaPermisosUsuarios($nombreRuta){
        $contratosGlobal = new contratosGlobal;
        switch ($nombreRuta){
            //SECCION CONTRATOS
            //RUTAS CON PERMISO CREAR
            case 'solicitarautorizacionaumentardisminuir':
            case 'agregarabono':
            case 'agregarhistorialmovimientocontrato':
            case 'agregarhistorialmovimientocontrato':
            case 'solicitarautorizacioncambiopaquete':
            case 'solicitarautorizaciongarantia':
            case 'agregarproducto':
            case 'solicitarautorizaciontraspasocontratolaboratorio':
            case 'solicitarautorizacionsupervisarcontrato':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 1, 0);
                break;

            //RUTAS CON PERMISO LEER
            case 'listacontrato':
            case 'filtrarlistacontrato':
            case 'filtrarlistacontratocheckbox':
            case 'vercontrato':
            case 'contratoactualizar':
            case 'listasolicitudautorizacion':
            case 'traspasarcontrato':
            case 'buscarcontratotraspasar':
            case 'obtenercontratotraspasar':
            case 'cobranzamovimientos':
            case 'movimientostiemporeal':
            case 'llamadascobranza':
            case 'listallamadascobranza':
            case 'filtrarventasmovimientos':
            case 'traspasarcontratozona':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 1, 1);
                break;

            //RUTAS CON PERMISO ACTUALIZAR
            case 'contratoeditar':
            case 'agregarnota':
            case 'editarHistorialArmazon':
            case 'agregarGarantiaHistorial':
            case 'cancelarGarantiaHistorial':
            case 'validarContrato':
            case 'entregarContrato':
            case 'entregarDiaPago':
            case 'agregarformapago':
            case 'desactivarPromocion':
            case 'eliminardiatemporal':
            case 'actualizarfechaultimoabonocontrato':
            case 'restablecercontrato':
            case 'cancelarContrato':
            case 'autorizarcontrato':
            case 'rechazarcontrato':
            case 'generartraspasocontrato':
            case 'marcarcontratocortellamada':
            case 'actualizartraspasarcontratozona':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 1, 2);
                break;

            //RUTAS PERMISOS ELIMINAR
            case 'eliminarAbono':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 1, 3);
                break;

            //SECCION USUARIOS
            //RUTAS PERMISOS CREAR
            case 'nuevoUsuarioFranquicia':
            case 'asignarDenegarPermisosUsuarios':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 0, 0);
                break;

            //RUTAS PERMISOS LEER
            case 'usuariosFranquicia':
            case 'usuariosfranquiciatiemporeal':
            case 'editarUsuarioFranquicia':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 0, 1);
                break;

            //RUTAS PERMISOS ACTUALIZAR
            case 'actualizarUsuarioFranquicia':
            case 'actualizarControlEntradaSalidaUsuarioFranquicia':
            case 'actualizarUsuarioFranquiciadispositivo':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 0, 2);
                break;

            //RUTAS PERMISOS ELIMINAR
            case 'eliminarUsuarioFranquicia':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 0, 3);
                break;

            //SECCION ADMINISTRACION
            //RUTAS PERMISOS CREAR
            case 'crearfranquicia':
            case 'productonuevo':
            case 'productocrear':
            case 'tratamientonuevo':
            case 'tratamientocrear':
            case 'promocionnueva':
            case 'promocioncrear':
            case 'mensajenuevo':
            case 'crearmensaje':
            case 'agendarcitaadministracion':
            case 'agregarObservacion':
            case 'ingresarOficina':
            case 'ingresarGasto':
            case 'registrarAsistencia':
            case 'tablaAsistencia':
            case 'ingresooficinaproducto':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 2, 0);
                break;

            //RUTAS PERMISOS LEER
            case 'filtrarproducto':
            case 'editarFranquicia':
            case 'productoactualizar':
            case 'tratamientoactualizar':
            case 'paqueteactualizar':
            case 'promocionactualizar':
            case 'insumos':
            case 'listaagendacitas':
            case 'listareporteasistencia':
            case 'cargarListaAsistencia':
            case 'reportebuzon':
            case 'listacontratoscancelados':
            case 'listacontratoscuentasactivas':
            case 'listacontratosreportes':
            case 'filtrarlistacontratosreportes':
            case 'reportellamadas':
            case 'reportemovimientos':
            case 'filtroreportemovimientos':
            case 'listacontratospagados':
            case 'listacontratospagadosseguimiento':
            case 'filtrarlistacontratospagadosseguimiento':
            case 'listacontratospaquetes':
            case 'verpoliza':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 2, 1);
                break;

            //RUTAS PERMISOS ACTUALIZAR
            case 'actualizarFranquicia':
            case 'productoeditar':
            case 'tratamientoeditar':
            case 'paqueteeditar':
            case 'promocioneditar':
            case 'estadoPromocionEditar':
            case 'actualizarinsumos':
            case 'notificarcitapacienteadministracion':
            case 'terminarPoliza':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 2, 2);
                break;

            //RUTAS PERMISOS ELIMINAR
            case 'eliminarmensaje':
            case 'eliminarGasto':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 2, 3);
                break;

            //SECCION VEHICULOS
            //RUTAS PERMISOS CREAR
            case 'nuevovehiculo':
            case 'registrarnuevoservicio':
            case 'crearnuevasupervision':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 5, 0);
                break;

            //RUTAS PERMISOS LEER
            case 'listavehiculos':
            case 'vervehiculo':
            case 'nuevasupervision':
            case 'actualizarsupervisionvehiculo':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 5, 1);
                break;

            //RUTAS PERMISOS ACTUALIZAR
            case 'actualizarvehiculo':
            case 'actualizarhorariolimitechofer':
            case 'editarsupervisionvehiculo':
            case 'autorizarsupervisionvehiculo':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 5, 2);
                break;

            //RUTAS PERMISOS ELIMINAR
            case 'eliminarVehiculo':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 5, 3);
                break;

            //SECCION CAMPAÑAS
            //RUTAS PERMISOS CREAR
            case 'crearcampania':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 6, 0);
                break;

            //RUTAS PERMISOS LEER
            case 'vercampania':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 6, 1);
                break;

            //RUTAS PERMISOS ACTUALIZAR
            case 'actualizarcampania':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 6, 2);
                break;

            //RUTAS PERMISOS ELIMINAR
            case 'eliminarcampania':
                $tienePermiso = $contratosGlobal::validarPermisoSeccion(Auth::user()->id, 6, 3);
                break;

            //TODA RUTA DIFERENTE NO VALIDAR - RETORNAR VERDADERO PARA DEJAR SEGUIR EL METODO
            default:
                $tienePermiso = true;
                break;

        }
        return $tienePermiso;

    }

    private function registrarHoraUltimaPeticionServidor($idUsuario){



    }
}
