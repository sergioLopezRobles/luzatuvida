<?php

namespace App\Http\Controllers\Dominios\Administracion;

use App\Clases\contratosGlobal;
use App\Clases\globalesServicioWeb;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Image;
use DateTime;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;

class vehiculos extends Controller {

    public function listavehiculos($idFranquicia){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL

            $tipoVehiculos = DB::select("SELECT * FROM tipovehiculo ORDER BY tipo ASC");

            return view('administracion.vehiculos.tablavehiculos', [
                'idFranquicia' => $idFranquicia, 'tipoVehiculos' => $tipoVehiculos
            ]);
        }else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public  function cargarlistavehiculos(Request $request){

        $idFranquicia = $request->input('idFranquicia');

        if((Auth::user()->rol_id) == 7) {
            //Director
            if($idFranquicia == '00000') {
                //Franquicia de prueba
                $listavehiculos = DB::select("SELECT v.indice, v.numserie, v.marca, v.modelo, v.id_franquicia, v.placas,
                                            (SELECT s.ultimoservicio from servicio s WHERE v.indice = s.id_vehiculo ORDER BY s.created_at DESC LIMIT 1 ) AS ultimoservicio,
                                            (SELECT s.siguienteservicio from servicio s WHERE v.indice = s.id_vehiculo ORDER BY s.created_at DESC LIMIT 1 ) AS siguienteservicio,
                                            (SELECT f.ciudad FROM franquicias f WHERE f.id = v.id_franquicia) as ciudadfranquicia,
                                            (SELECT u.name FROM users u WHERE u.id = (SELECT vu.id_usuario FROM vehiculosusuarios vu WHERE vu.id_vehiculo = v.indice AND vu.id_franquicia= v.id_franquicia)) as asignacion,
                                            (SELECT u.rol_id FROM users u WHERE u.id = (SELECT vu.id_usuario FROM vehiculosusuarios vu WHERE vu.id_vehiculo = v.indice AND vu.id_franquicia= v.id_franquicia)) as rol,
                                            (SELECT z.zona FROM zonas z WHERE z.id = (SELECT u.id_zona FROM users u WHERE u.id = (SELECT vu.id_usuario FROM vehiculosusuarios vu WHERE vu.id_vehiculo = v.indice AND vu.id_franquicia= v.id_franquicia))) as zona,
                                            (SELECT tv.tipo FROM tipovehiculo tv WHERE tv.id = v.id_tipovehiculo) as tipovehiculo
                                            FROM vehiculos v WHERE v.estado = 1
                                            ORDER BY ciudadfranquicia ASC, zona ASC;");
            }else {
                //Otra franquicia diferente a la de prueba
                $listavehiculos = DB::select("SELECT v.indice, v.numserie, v.marca, v.modelo, v.id_franquicia, v.placas,
                                            (SELECT s.ultimoservicio from servicio s WHERE v.indice = s.id_vehiculo ORDER BY s.created_at DESC LIMIT 1 ) AS ultimoservicio,
                                            (SELECT s.siguienteservicio from servicio s WHERE v.indice = s.id_vehiculo ORDER BY s.created_at DESC LIMIT 1 ) AS siguienteservicio,
                                            (SELECT f.ciudad FROM franquicias f WHERE f.id = v.id_franquicia) as ciudadfranquicia,
                                            (SELECT u.name FROM users u WHERE u.id = (SELECT vu.id_usuario FROM vehiculosusuarios vu WHERE vu.id_vehiculo = v.indice AND vu.id_franquicia= v.id_franquicia)) as asignacion,
                                            (SELECT u.rol_id FROM users u WHERE u.id = (SELECT vu.id_usuario FROM vehiculosusuarios vu WHERE vu.id_vehiculo = v.indice AND vu.id_franquicia= v.id_franquicia)) as rol,
                                            (SELECT z.zona FROM zonas z WHERE z.id = (SELECT u.id_zona FROM users u WHERE u.id = (SELECT vu.id_usuario FROM vehiculosusuarios vu WHERE vu.id_vehiculo = v.indice AND vu.id_franquicia= v.id_franquicia))) as zona,
                                            (SELECT tv.tipo FROM tipovehiculo tv WHERE tv.id = v.id_tipovehiculo) as tipovehiculo
                                            FROM vehiculos v
                                            WHERE v.id_franquicia != '00000' AND v.estado = '1'
                                            ORDER BY ciudadfranquicia ASC, zona ASC;");
            }
        }else {
            //Administrador o principal
            $listavehiculos = DB::select("SELECT v.indice, v.numserie, v.marca, v.modelo, v.id_franquicia, v.placas,
                                            (SELECT s.ultimoservicio from servicio s WHERE v.indice = s.id_vehiculo ORDER BY s.created_at DESC LIMIT 1 ) AS ultimoservicio,
                                            (SELECT s.siguienteservicio from servicio s WHERE v.indice = s.id_vehiculo ORDER BY s.created_at DESC LIMIT 1 ) AS siguienteservicio,
                                            (SELECT f.ciudad FROM franquicias f WHERE f.id = v.id_franquicia) as ciudadfranquicia,
                                            (SELECT u.name FROM users u WHERE u.id = (SELECT vu.id_usuario FROM vehiculosusuarios vu WHERE vu.id_vehiculo = v.indice AND vu.id_franquicia= v.id_franquicia)) as asignacion,
                                            (SELECT u.rol_id FROM users u WHERE u.id = (SELECT vu.id_usuario FROM vehiculosusuarios vu WHERE vu.id_vehiculo = v.indice AND vu.id_franquicia= v.id_franquicia)) as rol,
                                            (SELECT z.zona FROM zonas z WHERE z.id = (SELECT u.id_zona FROM users u WHERE u.id = (SELECT vu.id_usuario FROM vehiculosusuarios vu WHERE vu.id_vehiculo = v.indice AND vu.id_franquicia= v.id_franquicia))) as zona,
                                            (SELECT tv.tipo FROM tipovehiculo tv WHERE tv.id = v.id_tipovehiculo) as tipovehiculo
                                            FROM vehiculos v WHERE v.id_franquicia = '$idFranquicia'
                                            AND v.estado = '1'
                                            ORDER BY ciudadfranquicia ASC, zona ASC;");
        }

        $view = view('administracion.vehiculos.listas.listavehiculosregistrados', [
            'listavehiculos' => $listavehiculos
        ])->render();

        return \Response::json(array("valid"=>"true","view"=>$view));

    }

    public  function nuevovehiculo($id_franquicia, Request $request){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8)){
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL

            //Valores de formulario
            $numSerie = $request->input('numSerie');
            $kilometraje = $request->input('kilometraje');
            $sigKilometraje = $request->input('sigKilometraje');
            $ultimoServicio = $request->input('ultimoServicio');
            $sigServicio = $request->input('sigServicio');
            $idTipoVehiculo = $request->input('idTipoVehiculo');
            $marcaVehiculo = $request->input('marcaVehiculo');
            $numCilindros = $request->input('numCilindros');
            $lineaVehiculo = $request->input('lineaVehiculo');
            $modelo = $request->input('modeloVehiculo');
            $claseVehiculo = $request->input('claseVehiculo');
            $tipoVehiculo = $request->input('tipoVehiculo');
            $capacidad = $request->input('capacidad');
            $numMotor = $request->input('numMotor');
            $placas = $request->input('placas');
            $numeropoliza = $request->input('numeroPoliza');
            $vigenciaPoliza = $request->input('vigenciaPoliza');
            $descripcion = $request->input('descripcion');


            //Validaciones de campos
            request()->validate([
                'numSerie' => 'required|string|min:16|max:16',
                'kilometraje' => 'required|numeric',
                'sigKilometraje' => 'required|numeric',
                'ultimoServicio' => 'required',
                'sigServicio' => 'required',
                'idTipoVehiculo' => 'required',
                'marcaVehiculo' => 'required|string|min:2|max:255',
                'numCilindros' => 'required|numeric',
                'lineaVehiculo' => 'required|string|min:1|max:255',
                'modeloVehiculo' => 'required|string|min:1|max:255',
                'claseVehiculo' => 'required|string|min:1|max:255',
                'tipoVehiculo' => 'required|string|min:1|max:255',
                'capacidad' => 'required|string|min:2|max:255',
                'numMotor' => 'required|string|min:11|max:17',
                'placas' => 'required|string',
                'numeroPoliza' => 'nullable|string',
                'vigenciaPoliza' => 'nullable',
                'factura' => 'required|file|mimes:pdf',
                'descripcion' => 'required|string|min:1|max:250'
            ]);

            try {

                //Verificar que tipo de vehiculo seleccionado sea correcto
                $existeTipoVehiculo = DB::select("SELECT id FROM tipovehiculo WHERE id = '$idTipoVehiculo' LIMIT 1");
                if($existeTipoVehiculo == null){
                    //No existe tipo de vehiculo
                    return back()->with('alerta', 'Selecciona un tipo de vehículo valido.');
                }

                $vehiculoExistente = DB::select("SELECT * FROM vehiculos v WHERE v.numserie = '$numSerie' AND estado = '1'");

                if($vehiculoExistente == null ){
                    //No existe un vehiculo con ese nuemero de serie o existe pero esta inactivo

                    //Velidar fecha
                    $ultimoServicio = Carbon::parse($ultimoServicio)->format('Y-m-d');
                    $sigServicio =  Carbon::parse($sigServicio)->format('Y-m-d');

                    if($sigServicio > $ultimoServicio){

                        $facturaServicioVehiculo = "";
                        if (request()->hasFile('factura')) {
                            $facturaServicioVehiculoBruta = 'factura-servicio-vehiculo-' . $numSerie . '-' . time() . '.' . request()->file('factura')->getClientOriginalExtension();
                            $facturaServicioVehiculo = request()->file('factura')->storeAs('uploads/imagenes/vehiculos/factura', $facturaServicioVehiculoBruta, 'disco');

                        }

                        //Obtener identificador incremental del vehiculo
                        $identificadorVehiculo = self::generarIdentificadorVehiculo($marcaVehiculo, $id_franquicia);

                        //Insertar nuevo registro tabla vehiculos

                        DB::table('vehiculos')->insert([
                            'id_franquicia' => $id_franquicia, 'numserie' => $numSerie, 'marca' => $marcaVehiculo, 'cilindros' => $numCilindros,
                            'linea' => $lineaVehiculo, 'id_tipovehiculo' => $idTipoVehiculo, 'modelo' => $modelo, 'clase' => $claseVehiculo, 'tipo' => $tipoVehiculo,
                            'capacidad' => $capacidad, 'nummotor' => $numMotor, 'placas' => $placas, 'numeropoliza' => $numeropoliza, 'vigenciapoliza' => $vigenciaPoliza,
                            'identificador' => $identificadorVehiculo, 'estado' => '1', 'created_at' => Carbon::now()
                        ]);

                        //Registrar movimiento historial sucursal
                        $vehiculo = DB::select("SELECT v.indice FROM vehiculos v WHERE v.numserie = '$numSerie' AND v.id_tipovehiculo = '$idTipoVehiculo'
                                                        AND v.id_franquicia = '$id_franquicia'");
                        $referencia = "";
                        if($vehiculo != null){
                            $referencia = $vehiculo[0]->indice;
                        }

                        $id_UsuarioC = Auth::user()->id;
                        $movimientoHistorial = "Registró nuevo vehículo con numero de serie: '" . $numSerie . "' | " . $marcaVehiculo . " | " . $modelo;
                        self::insertarHistorialSucursalVehiculos($id_franquicia, $id_UsuarioC, $referencia, $movimientoHistorial);

                        //Insertar nuevo registro tabla servicio
                        $id_vehiculo = ($vehiculo != null)? $vehiculo[0]->indice: "";
                        DB::table('servicio')->insert([
                            'id_vehiculo' => $id_vehiculo, 'kilometraje' => $kilometraje, 'siguientekilometraje' => $sigKilometraje, 'ultimoservicio' => $ultimoServicio,
                            'siguienteservicio' => $sigServicio, 'descripcion' => $descripcion, 'factura' => $facturaServicioVehiculo, 'created_at' => Carbon::now()
                        ]);

                        //Registrar nuevo servicio en tabla historial sucursal
                        $movimientoHistorialServicio = "Registró nuevo servicio para vehículo con numero de serie: '" . $numSerie . "'";
                        self::insertarHistorialSucursalVehiculos($id_franquicia, $id_UsuarioC, $referencia, $movimientoHistorialServicio);

                        return redirect()->route('listavehiculos',[$id_franquicia])->with('bien', 'El vehiculo se registro correctamente');

                    } else {
                        return back()->with('alerta', 'La fecha de ultimo servicio debe ser menor a siguiente servicio');
                    }

                } else {
                    //Ya existe numero de serie registrado
                    return back()->with('alerta', 'El vehiculo ya esta registrado');
                }

            } catch (\Exception $e){
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

    public function vervehiculo($idFranquicia, $idVehiculo){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 17 || Auth::user()->rol_id == 4)){


            try {
                $existeVehiculo = DB::select("SELECT * FROM vehiculos WHERE indice = '$idVehiculo' AND id_franquicia = '$idFranquicia' AND estado = '1'");

                if ($existeVehiculo != null) {
                    //Existe vehiculo

                    $vehiculo = DB::select("SELECT * FROM vehiculos WHERE indice = '$idVehiculo' AND id_franquicia = '$idFranquicia' ORDER BY created_at DESC LIMIT 1");
                    $servicios = DB::select("SELECT s.kilometraje, s.siguientekilometraje, s.ultimoservicio, s.siguienteservicio, s.factura, s.descripcion,
                                                    (SELECT numserie FROM vehiculos v WHERE v.indice = s.id_vehiculo ORDER BY v.created_at DESC LIMIT 1) as numserie
                                                    FROM servicio s WHERE s.id_vehiculo = '$idVehiculo'
                                                    ORDER BY s.created_at DESC");

                    $ultimoServicio = DB::select("SELECT s.ultimoservicio FROM servicio s WHERE s.id_vehiculo = '$idVehiculo' ORDER BY s.ultimoservicio DESC LIMIT 1");

                    $i = count($servicios) + 1;

                    //Supervision de vehiculo por usuario de asignacion
                    $supervisionVehicular = array();
                    if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17){
                        //Rol es cobranza o chofer - Supervisiones solo de ellos
                        $asignacion = DB::select("SELECT vu.id_usuario FROM vehiculosusuarios vu WHERE vu.id_vehiculo = '$idVehiculo'");
                        $idUsuario = "";
                        if($asignacion != null){
                            //Vehiculo con asignacion
                            $idUsuario = (Auth::user()->rol_id == 17 || Auth::user()->rol_id == 4)? Auth::user()->id: $asignacion[0]->id_usuario;
                            $supervisionVehicular = DB::select("SELECT * FROM vehiculossupervision vs WHERE vs.id_usuario = '$idUsuario' AND vs.id_vehiculo = '$idVehiculo'
                                                                   ORDER BY vs.created_at DESC");
                        }

                    }else{
                        //Rol administracion, director o principal
                        $supervisionVehicular = DB::select("SELECT * FROM vehiculossupervision vs WHERE vs.id_franquicia = '$idFranquicia' AND vs.id_vehiculo = '$idVehiculo'
                                                                   ORDER BY vs.created_at DESC");
                    }
                    $indiceSupervision = count($supervisionVehicular) + 1;

                    $horarioImagenes = DB::select("SELECT * FROM vehiculoshorariosupervision vh WHERE vh.id_franquicia = '$idFranquicia'");

                    $historialMovimientos = DB::select("SELECT hs.cambios, hs.created_at, (SELECT u.name FROM users u WHERE u.id = hs.id_usuarioC) as usuario
                                                          FROM historialsucursal hs
                                                          WHERE hs.tipomensaje = '0' AND hs.seccion = '5' AND hs.referencia = '$idVehiculo' AND hs.id_franquicia = '$idFranquicia'
                                                          ORDER BY hs.created_at DESC");

                    $tipoVehiculos = DB::select("SELECT * FROM tipovehiculo ORDER BY tipo ASC");

                    return view('administracion.vehiculos.vervehiculo', [
                        'vehiculo' => $vehiculo,
                        'servicios' => $servicios,
                        'idFranquicia'=>$idFranquicia,
                        'ultimoServicio' => $ultimoServicio,
                        'i' => $i,
                        'supervisionVehicular' => $supervisionVehicular,
                        'indiceSupervision' => $indiceSupervision,
                        'horarioImagenes' => $horarioImagenes,
                        'historialMovimientos' => $historialMovimientos,
                        'tipoVehiculos' => $tipoVehiculos
                    ]);

                }
                return back()->with('alerta', 'No se encontro el vehiculo');

            } catch (\Exception $e){
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

    public  function actualizarvehiculo($idFranquicia, $idVehiculo, Request $request){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8)){


            //Valores de formulario
            $numSerie = $request->input('numSerie');
            $idTipoVehiculo = $request->input('idTipoVehiculo');
            $marcaVehiculo = $request->input('marcaVehiculo');
            $numCilindros = $request->input('numCilindros');
            $lineaVehiculo = $request->input('lineaVehiculo');
            $modelo = $request->input('modeloVehiculo');
            $claseVehiculo = $request->input('claseVehiculo');
            $tipoVehiculo = $request->input('tipoVehiculo');
            $capacidad = $request->input('capacidad');
            $numMotor = $request->input('numMotor');
            $numeropoliza = $request->input('numeroPoliza');
            $vigenciaPoliza = $request->input('vigenciaPoliza');
            $placas = $request->input('placas');

            //Validaciones de campos
            request()->validate([
                'numSerie' => 'required|string|min:16|max:16',
                'idTipoVehiculo' => 'required',
                'marcaVehiculo' => 'required|string|min:5|max:255',
                'numCilindros' => 'required|numeric',
                'lineaVehiculo' => 'required|string|min:1|max:255',
                'modeloVehiculo' => 'required|string|min:1|max:255',
                'claseVehiculo' => 'required|string|min:1|max:255',
                'tipoVehiculo' => 'required|string|min:5|max:255',
                'capacidad' => 'required|string|min:2|max:255',
                'numMotor' => 'required|string|min:11|max:17',
                'numeroPoliza' => 'nullable|string',
                'vigenciaPoliza' => 'nullable',
                'placas' => 'required|string'
            ]);

            //Verificar que tipo de vehiculo seleccionado sea correcto
            $existeTipoVehiculo = DB::select("SELECT id FROM tipovehiculo WHERE id = '$idTipoVehiculo' LIMIT 1");
            if($existeTipoVehiculo != null){
                //Tipo de vehiuclo correcto

                //Obtenemos datos antes de actualizar
                $vehiculo = DB::select("SELECT * FROM vehiculos v WHERE v.indice = '$idVehiculo' AND v.id_franquicia = '$idFranquicia'");

                if($vehiculo != null){
                    //Vehiculo si existe

                    if($vehiculo[0]->estado == 1){
                        //Estado de vehiculo es igual a activo

                        try {

                            //Verificar cuales datos son actualizados
                            $movimientoHitorial = "Actualizo";
                            if($vehiculo[0]->numserie != $numSerie){
                                $movimientoHitorial = $movimientoHitorial . " numero de serie,";
                            }if($vehiculo[0]->id_tipovehiculo != $idTipoVehiculo){
                                $movimientoHitorial = $movimientoHitorial . " tipo vehiculo,";
                            }if($vehiculo[0]->marca != $marcaVehiculo){
                                $movimientoHitorial = $movimientoHitorial . " marca,";
                            }if($vehiculo[0]->cilindros != $numCilindros){
                                $movimientoHitorial = $movimientoHitorial . " cilindros,";
                            }if($vehiculo[0]->linea != $lineaVehiculo){
                                $movimientoHitorial = $movimientoHitorial . " linea,";
                            }if($vehiculo[0]->modelo != $modelo){
                                $movimientoHitorial = $movimientoHitorial . " modelo,";
                            }if($vehiculo[0]->clase != $claseVehiculo){
                                $movimientoHitorial = $movimientoHitorial . " clase,";
                            }if($vehiculo[0]->tipo != $tipoVehiculo){
                                $movimientoHitorial = $movimientoHitorial . " tipo,";
                            }if($vehiculo[0]->capacidad != $capacidad){
                                $movimientoHitorial = $movimientoHitorial . " capacidad,";
                            }if($vehiculo[0]->nummotor != $numMotor){
                                $movimientoHitorial = $movimientoHitorial . " numero de motor, ";
                            }if($vehiculo[0]->placas != $placas){
                                $movimientoHitorial = $movimientoHitorial . " placas,";
                            }if($vehiculo[0]->numeropoliza != $numeropoliza){
                                $movimientoHitorial = $movimientoHitorial . " numero de póliza, ";
                            }if($vehiculo[0]->vigenciapoliza != $vigenciaPoliza){
                                $movimientoHitorial = $movimientoHitorial . " vigencia de póliza";
                            }

                            //Actualizamos el vehiculo

                            DB::table('vehiculos')->where("indice","=", $idVehiculo)->where("id_Franquicia","=",$idFranquicia)->update([
                                'numserie' => $numSerie, 'id_tipovehiculo' => $idTipoVehiculo, 'marca' => $marcaVehiculo, 'cilindros' => $numCilindros,
                                'linea' => $lineaVehiculo, 'modelo' => $modelo, 'clase' => $claseVehiculo, 'tipo' => $tipoVehiculo,
                                'capacidad' => $capacidad, 'nummotor' => $numMotor, 'placas' => $placas, 'numeropoliza' => $numeropoliza,
                                'vigenciapoliza' => $vigenciaPoliza, 'updated_at' => Carbon::now()
                            ]);

                            //Registrar movimiento en historial sucursal
                            $id_UsuarioC = Auth::user()->id;
                            $movimientoHitorial = trim($movimientoHitorial,",");
                            $movimientoHitorial = $movimientoHitorial . " para vehículo con numero de serie: '" . $vehiculo[0]->numserie ."'";
                            self::insertarHistorialSucursalVehiculos($idFranquicia,$id_UsuarioC, $idVehiculo, $movimientoHitorial);

                            return redirect()->route('vervehiculo', ['idFranquicia' => $idFranquicia, 'idVehiculo' => $idVehiculo])->with('bien', "El vehiculo se actualizó correctamente");

                        } catch (\Exception $e){
                            \Log::info("Error: " . $e->getMessage());
                            return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina   Error:' . $e->getMessage());
                        }

                    }else{
                        //Vehiculo en estatus inactivo
                        return back()->with('alerta', 'No puedes actualizar el vehiculo debido que fue dado de baja.');
                    }

                }else{
                    //Vehiculo no encontrado
                    return back()->with('alerta', 'No existe el vehículo.');
                }
            }else{
                //No existe tipo de vehiculo
                return back()->with('alerta', 'Selecciona un tipo de vehículo valido.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function registrarnuevoservicio($idFranquicia, $idVehiculo, Request $request){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 17 || Auth::user()->rol_id == 4)){


            //Valores de formulario
            $kilometraje = $request->input('kilometraje');
            $sigKilometraje = $request->input('sigKilometraje');
            $ultimoServicio = $request->input('ultimoServicio');
            $sigServicio = $request->input('sigServicio');
            $descripcion = $request->input('descripcion');


            //Validaciones de campos
            request()->validate([
                'kilometraje' => 'required|numeric',
                'sigKilometraje' => 'required|numeric',
                'ultimoServicio' => 'required',
                'sigServicio' => 'required',
                'factura' => 'required|file|mimes:pdf',
                'descripcion' => 'required|string|min:1|max:250'
            ]);

            try {

                $vehiculoExistente = DB::select("SELECT * FROM vehiculos v WHERE v.indice = '$idVehiculo' AND id_franquicia = '$idFranquicia'");

                if($vehiculoExistente != null ){
                    //Existe un vehiculo con ese nuemero de serie

                    if($vehiculoExistente[0]->estado == 1){
                        //Vehiculo en estatus de activo

                        //Velidar fecha
                        $ultimoServicio = Carbon::parse($ultimoServicio)->format('Y-m-d');
                        $sigServicio =  Carbon::parse($sigServicio)->format('Y-m-d');

                        if($sigServicio > $ultimoServicio){

                            $facturaServicioVehiculo = "";
                            if (request()->hasFile('factura')) {
                                $facturaServicioVehiculoBruta = 'factura-servicio-vehiculo-' . $vehiculoExistente[0]->numserie . '-' . time() . '.' . request()->file('factura')->getClientOriginalExtension();
                                $facturaServicioVehiculo = request()->file('factura')->storeAs('uploads/imagenes/vehiculos/factura', $facturaServicioVehiculoBruta, 'disco');

                            }

                            //Insertar nuevo registro tabla servicio
                            DB::table('servicio')->insert([
                                'id_vehiculo' => $idVehiculo, 'kilometraje' => $kilometraje, 'siguientekilometraje' => $sigKilometraje, 'ultimoservicio' => $ultimoServicio,
                                'siguienteservicio' => $sigServicio, 'descripcion' => $descripcion, 'factura' => $facturaServicioVehiculo, 'created_at' => Carbon::now()
                            ]);

                            //Registrar movimeinto tabla historial sucursal
                            $movimientoHistorial = "Registró nuevo servicio para vehículo con numero de serie: '" . $vehiculoExistente[0]->numserie . "'";
                            $id_UsuarioC = Auth::user()->id;
                            self::insertarHistorialSucursalVehiculos($idFranquicia, $id_UsuarioC,$vehiculoExistente[0]->indice, $movimientoHistorial);

                            return redirect()->route('vervehiculo', ['idFranquicia' => $idFranquicia, 'idVehiculo' => $idVehiculo])->with('bien', 'El servicio se registro correctamente');

                        } else {
                            return back()->with('alerta', 'La fecha de ultimo servicio debe ser menor a siguiente servicio');
                        }

                    }else{
                    //Vehiculo en estatus inactivo
                    return back()->with('alerta', 'No puedes generar un nuevo servicio para el vehiculo debido que fue dado de baja.');
                    }

                } else {
                    //No existe numero de serie registrado
                    return back()->with('alerta', 'Vehiculo no registrado');
                }

            } catch (\Exception $e){
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

    public function descargarfacturaservicio($idVehiculo)
    {
        if ((Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8)) //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL
        {
            $existeVehiculo = DB::select("SELECT factura FROM servicio WHERE id_vehiculo = '$idVehiculo'");
            if ($existeVehiculo != null) { //Existe el vehiculo?
                //Si existe el vehiculo
                $archivo = $existeVehiculo[0]->factura;
                if (file_exists($archivo)){
                    return Storage::disk('disco')->download($archivo);
                }else{
                    return back()->with('alerta', 'No se encontro el archivo.');
                }

            } else {
                //No existe el vehiculo
                return back()->with('alerta', 'No se encontro el vehiculo.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function nuevasupervision($idFranquicia, $idVehiculo){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 17 || Auth::user()->rol_id == 4)){

            //Dia de la semana
            $hoy = Carbon::now();
            $diaSemana = Carbon::parse($hoy)->dayOfWeekIso;

            //Si es rol de chofer -> los dias sabados no tiene permitido generar una nueva supervision.
            if(Auth::user()->rol_id == 17 && $diaSemana == 6){
                return back()->with('alerta', 'No tienes permitido crear una nueva supervisón en dia sabado.');
            }

            $existeVehiculo = DB::select("SELECT * FROM vehiculos v WHERE v.indice = '$idVehiculo'");

            if($existeVehiculo != null){
                //Existe vehiculo registrado

                if($existeVehiculo[0]->estado == 1){
                    //Vehiculo en estado activo

                    $usuarioAsigado = DB::select("SELECT vu.id_usuario  FROM vehiculosusuarios vu WHERE vu.id_vehiculo = '$idVehiculo' ORDER BY vu.created_at DESC LIMIT 1");
                    if($usuarioAsigado != null){
                        //Vehiculo con usuario asignado
                        $id_usuario = $usuarioAsigado[0]->id_usuario;
                        $ultimaSupervision = DB::select("SELECT * FROM vehiculossupervision vs WHERE vs.id_franquicia= '$idFranquicia' AND vs.id_vehiculo = '$idVehiculo'
                                                                  AND vs.id_usuario = '$id_usuario' ORDER BY vs.created_at DESC LIMIT 1");
                    }else{
                        //Vehiculo sin asignacion
                        $ultimaSupervision = DB::select("SELECT * FROM vehiculossupervision vs WHERE vs.id_franquicia= '$idFranquicia' AND vs.id_vehiculo = '$idVehiculo'
                                                                ORDER BY vs.created_at DESC LIMIT 1");
                    }

                    //Horarios permitidos para generar actualizaciones
                    $horarioActualizacionFotos = DB::select("SELECT vh.horalimitechoferfoto1, vh.horalimitechoferfoto2 FROM vehiculoshorariosupervision vh WHERE vh.id_franquicia = '$idFranquicia'");

                    //Dia de la semana
                    $hoy = Carbon::now();
                    $diaSemana = Carbon::parse($hoy)->dayOfWeekIso;

                    if($ultimaSupervision != null){
                        if($ultimaSupervision[0]->estado == 1){
                            //Ultima supervision fue aprobada - Verificar fecha
                            if(Carbon::parse($hoy)->format('Y-m-d') > Carbon::parse($ultimaSupervision[0]->created_at)->format('Y-m-d')){
                                //Fecha de hoy es mayor a fecha de ultima supervision

                                return view('administracion.vehiculos.nuevasupervisionvehiculo', [
                                    'idFranquicia' => $idFranquicia, 'idVehiculo' => $idVehiculo, 'horarioActualizacionFotos' => $horarioActualizacionFotos, 'diaSemana' => $diaSemana
                                ]);

                            }else{
                                //Intenta hacer 2 supervisiones un mismo dia
                                return back()->with('alerta', 'No es posible crear una nueva supervisión, ya que ya has registrado la correspondiente al día de hoy.');
                            }

                        }else{
                            //Supervision en estatus pendiente por autorizar
                            return back()->with('alerta', "Existe una supervisión pendiente por autorizar con fecha: '". Carbon::parse($ultimaSupervision[0]->created_at)->format("Y-m-d") ."'. Por favor, solicita la autorización primero y luego vuelve a intentarlo.");
                        }
                    }else{
                        //No existe ninguna supervision
                        return view('administracion.vehiculos.nuevasupervisionvehiculo', [
                            'idFranquicia' => $idFranquicia, 'idVehiculo' => $idVehiculo, 'horarioActualizacionFotos' => $horarioActualizacionFotos, 'diaSemana' => $diaSemana
                        ]);
                    }
                }else{
                    //Vehiculo en estatus de inactivo
                    return back()->with('alerta', 'No puedes crear una nueva supervisión debido que el vehículo fue dado de baja.');
                }
            }else{
                //No existe vehiculo registrado
                return back()->with('alerta', 'No se encontro el vehiculo.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function crearnuevasupervision($idFranquicia, $idVehiculo){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 17 || Auth::user()->rol_id == 4)){
            $existeVehiculo = DB::select("SELECT v.indice, v.numserie, v.placas, v.estado FROM vehiculos v WHERE v.indice = '$idVehiculo'");
            if($existeVehiculo != null){
                //Existe vehiculo registrado
                if($existeVehiculo[0]->estado == 1){
                    //Vehiculo en estado de activo
                    $supervisionpendiente = DB::select("SELECT * FROM vehiculossupervision vs WHERE vs.id_franquicia= '$idFranquicia' AND vs.id_vehiculo = '$idVehiculo' AND vs.estado = '0' ORDER BY vs.created_at DESC LIMIT 1");
                    if($supervisionpendiente == null){
                        //Sin supervisiones pendientes

                        $numSerie = $existeVehiculo[0]->numserie;
                        //Dia de la semana
                        $hoy = Carbon::now();
                        $diaSemana = Carbon::parse($hoy)->dayOfWeekIso;

                        //Solicitar imagenes
                        $rol = Auth::user()->rol_id;

                        //Verificar horario para actualizacion de imagen
                        $horaActual = Carbon::now();
                        $horaActual = Carbon::parse($horaActual)->format('H:i');
                        $horaLimite = Carbon::parse('09:00')->format('H:i');

                        $existeHorarioSupervision = DB::select("SELECT vh.horalimitechoferfoto1 FROM vehiculoshorariosupervision vh WHERE vh.id_franquicia = '$idFranquicia'");
                        if ($existeHorarioSupervision != null) {
                            //Existe un horario registrado
                            $horaLimite = Carbon::parse($existeHorarioSupervision[0]->horalimitechoferfoto1)->format('H:i');
                        }

                        switch($rol){
                            case 7:
                                //Director
                                request()->validate([
                                    'kilometraje1' => 'required|image|mimes:jpg',
                                    'kilometraje2' => 'nullable|image|mimes:jpg',
                                    'ladoizquierdo' => 'nullable|image|mimes:jpg',
                                    'ladoderecho' => 'nullable|image|mimes:jpg',
                                    'frente' => 'nullable|image|mimes:jpg',
                                    'atras' => 'nullable|image|mimes:jpg',
                                    'extra1' => 'nullable|image|mimes:jpg',
                                    'extra2' => 'nullable|image|mimes:jpg',
                                    'extra3' => 'nullable|image|mimes:jpg',
                                    'extra4' => 'nullable|image|mimes:jpg',
                                    'extra5' => 'nullable|image|mimes:jpg',
                                    'extra6' => 'nullable|image|mimes:jpg'
                                ]);
                                break;

                            case 6:
                                //Administracion
                            case 8:
                                //Principal
                                if($diaSemana == 6){
                                    //Dias de Sabado son requeridas todas las fotos- excepto foto kilometraje tarde
                                    request()->validate([
                                        'kilometraje1' => 'required|image|mimes:jpg',
                                        'kilometraje2' => 'nullable|image|mimes:jpg',
                                        'ladoizquierdo' => 'required|image|mimes:jpg',
                                        'ladoderecho' => 'required|image|mimes:jpg',
                                        'frente' => 'required|image|mimes:jpg',
                                        'atras' => 'required|image|mimes:jpg',
                                        'extra1' => 'required|image|mimes:jpg',
                                        'extra2' => 'required|image|mimes:jpg',
                                        'extra3' => 'required|image|mimes:jpg',
                                        'extra4' => 'required|image|mimes:jpg',
                                        'extra5' => 'required|image|mimes:jpg',
                                        'extra6' => 'required|image|mimes:jpg'
                                    ]);
                                }else{
                                    //Dia diferente de sabado
                                    request()->validate([
                                        'kilometraje1' => 'required|image|mimes:jpg',
                                        'kilometraje2' => 'nullable|image|mimes:jpg',
                                        'ladoizquierdo' => 'nullable|image|mimes:jpg',
                                        'ladoderecho' => 'nullable|image|mimes:jpg',
                                        'frente' => 'nullable|image|mimes:jpg',
                                        'atras' => 'nullable|image|mimes:jpg',
                                        'extra1' => 'nullable|image|mimes:jpg',
                                        'extra2' => 'nullable|image|mimes:jpg',
                                        'extra3' => 'nullable|image|mimes:jpg',
                                        'extra4' => 'nullable|image|mimes:jpg',
                                        'extra5' => 'nullable|image|mimes:jpg',
                                        'extra6' => 'nullable|image|mimes:jpg'
                                    ]);
                                }
                                break;

                            case 4:
                                //Cobranza
                                if($diaSemana != 5){
                                    //Dias de Sabado a jueves - Solo requeridas las fotos de kilometraje

                                    //Esta disponible la opcion de subor Kilometraje mañana?
                                    if ($horaActual < $horaLimite) {
                                        //A tiempo para ingresar fotografia kilometraje mañana
                                        request()->validate([
                                            'kilometraje1' => 'required|image|mimes:jpg',
                                            'kilometraje2' => 'nullable|image|mimes:jpg',
                                            'ladoizquierdo' => 'nullable|image|mimes:jpg',
                                            'ladoderecho' => 'nullable|image|mimes:jpg',
                                            'frente' => 'nullable|image|mimes:jpg',
                                            'atras' => 'nullable|image|mimes:jpg',
                                            'extra1' => 'nullable|image|mimes:jpg',
                                            'extra2' => 'nullable|image|mimes:jpg',
                                            'extra3' => 'nullable|image|mimes:jpg',
                                            'extra4' => 'nullable|image|mimes:jpg',
                                            'extra5' => 'nullable|image|mimes:jpg',
                                            'extra6' => 'nullable|image|mimes:jpg'
                                        ]);

                                    }else{
                                        //Excedio hora limite para ingresar imagenes de kilometraje mañana
                                        request()->validate([
                                            'kilometraje1' => 'nullable|image|mimes:jpg',
                                            'kilometraje2' => 'required|image|mimes:jpg',
                                            'ladoizquierdo' => 'nullable|image|mimes:jpg',
                                            'ladoderecho' => 'nullable|image|mimes:jpg',
                                            'frente' => 'nullable|image|mimes:jpg',
                                            'atras' => 'nullable|image|mimes:jpg',
                                            'extra1' => 'nullable|image|mimes:jpg',
                                            'extra2' => 'nullable|image|mimes:jpg',
                                            'extra3' => 'nullable|image|mimes:jpg',
                                            'extra4' => 'nullable|image|mimes:jpg',
                                            'extra5' => 'nullable|image|mimes:jpg',
                                            'extra6' => 'nullable|image|mimes:jpg'
                                        ]);

                                    }
                                }else{
                                    //Dias viernes son requeridas las 7 primeras fotos, excepto foto kilometraje tarde
                                    if ($horaActual < $horaLimite) {
                                        //Hora para subir fotografia kilometraje mañana menor a fecha limite
                                        request()->validate([
                                            'kilometraje1' => 'required|image|mimes:jpg',
                                            'kilometraje2' => 'nullable|image|mimes:jpg',
                                            'ladoizquierdo' => 'required|image|mimes:jpg',
                                            'ladoderecho' => 'required|image|mimes:jpg',
                                            'frente' => 'required|image|mimes:jpg',
                                            'atras' => 'required|image|mimes:jpg',
                                            'extra1' => 'required|image|mimes:jpg',
                                            'extra2' => 'nullable|image|mimes:jpg',
                                            'extra3' => 'nullable|image|mimes:jpg',
                                            'extra4' => 'nullable|image|mimes:jpg',
                                            'extra5' => 'nullable|image|mimes:jpg',
                                            'extra6' => 'nullable|image|mimes:jpg'
                                        ]);
                                    }else{
                                        //Paso hora limite para subir foto kilometraje mañana
                                        request()->validate([
                                            'kilometraje1' => 'nullable|image|mimes:jpg',
                                            'kilometraje2' => 'required|image|mimes:jpg',
                                            'ladoizquierdo' => 'required|image|mimes:jpg',
                                            'ladoderecho' => 'required|image|mimes:jpg',
                                            'frente' => 'required|image|mimes:jpg',
                                            'atras' => 'required|image|mimes:jpg',
                                            'extra1' => 'required|image|mimes:jpg',
                                            'extra2' => 'nullable|image|mimes:jpg',
                                            'extra3' => 'nullable|image|mimes:jpg',
                                            'extra4' => 'nullable|image|mimes:jpg',
                                            'extra5' => 'nullable|image|mimes:jpg',
                                            'extra6' => 'nullable|image|mimes:jpg'
                                        ]);
                                    }

                                }
                                break;
                            case 17:
                                //Chofer
                                if($diaSemana != 6) {
                                    //Es un dia difrente de sabado

                                    //Esta disponible la opcion de subor Kilometraje mañana?
                                    if ($horaActual < $horaLimite) {
                                        //Chofer puede subir foto 1
                                        request()->validate([
                                            'kilometraje1' => 'required|image|mimes:jpg',
                                            'kilometraje2' => 'nullable|image|mimes:jpg',
                                            'ladoizquierdo' => 'nullable|image|mimes:jpg',
                                            'ladoderecho' => 'nullable|image|mimes:jpg',
                                            'frente' => 'nullable|image|mimes:jpg',
                                            'atras' => 'nullable|image|mimes:jpg',
                                            'extra1' => 'nullable|image|mimes:jpg',
                                            'extra2' => 'nullable|image|mimes:jpg',
                                            'extra3' => 'nullable|image|mimes:jpg',
                                            'extra4' => 'nullable|image|mimes:jpg',
                                            'extra5' => 'nullable|image|mimes:jpg',
                                            'extra6' => 'nullable|image|mimes:jpg'
                                        ]);

                                    } else {
                                        //Solo puede subir Kilometraje tarde y necesita que administracion actualice las demas fotos
                                        request()->validate([
                                            'kilometraje1' => 'nullable|image|mimes:jpg',
                                            'kilometraje2' => 'required|image|mimes:jpg',
                                            'ladoizquierdo' => 'nullable|image|mimes:jpg',
                                            'ladoderecho' => 'nullable|image|mimes:jpg',
                                            'frente' => 'nullable|image|mimes:jpg',
                                            'atras' => 'nullable|image|mimes:jpg',
                                            'extra1' => 'nullable|image|mimes:jpg',
                                            'extra2' => 'nullable|image|mimes:jpg',
                                            'extra3' => 'nullable|image|mimes:jpg',
                                            'extra4' => 'nullable|image|mimes:jpg',
                                            'extra5' => 'nullable|image|mimes:jpg',
                                            'extra6' => 'nullable|image|mimes:jpg'
                                        ]);
                                    }
                                }else{
                                    //Es dia sabado - No tiene permitido generar una nueva supervision
                                    return back()->with('alerta', 'No tienes permitido crear una nueva supervisión en día sábado.');
                                }
                                break;
                        }

                        try{
                            //Kilometraje mañana
                            $kilometraje1 = "";
                            if (request()->hasFile('kilometraje1')) {
                                $foto1Bruta = 'Kilometraje1-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('kilometraje1')->getClientOriginalExtension();
                                $kilometraje1 = request()->file('kilometraje1')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto1Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto1Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto1Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto1Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto1Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                            }

                            //Kilometraje tarde
                            $kilometraje2 = "";
                            if (request()->hasFile('kilometraje2')) {
                                $foto2Bruta = 'Kilometraje2-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('kilometraje2')->getClientOriginalExtension();
                                $kilometraje2 = request()->file('kilometraje2')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto2Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto2Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto2Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto2Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto2Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                            }

                            //Lado izquierdo
                            $ladoizquierdo = "";
                            if (request()->hasFile('ladoizquierdo')) {
                                $foto3Bruta = 'LadoIzquierdo-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('ladoizquierdo')->getClientOriginalExtension();
                                $ladoizquierdo = request()->file('ladoizquierdo')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto3Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto3Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto3Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto3Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto3Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                            }

                            //Lado derecho
                            $ladoderecho = "";
                            if (request()->hasFile('ladoderecho')) {
                                $foto4Bruta = 'LadoDerecho-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('ladoderecho')->getClientOriginalExtension();
                                $ladoderecho = request()->file('ladoderecho')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto4Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto4Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto4Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto4Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto4Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                            }

                            //Frente
                            $frente = "";
                            if (request()->hasFile('frente')) {
                                $foto5Bruta = 'Frente-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('frente')->getClientOriginalExtension();
                                $frente = request()->file('frente')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto5Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto5Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto5Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto5Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto5Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                            }

                            //Atras
                            $atras = "";
                            if (request()->hasFile('atras')) {
                                $foto6Bruta = 'Atras-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('atras')->getClientOriginalExtension();
                                $atras = request()->file('atras')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto6Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto6Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto6Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto6Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto6Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                            }

                            //Extra 1
                            $extra1 = "";
                            if (request()->hasFile('extra1')) {
                                $foto7Bruta = 'Extra1-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('extra1')->getClientOriginalExtension();
                                $extra1 = request()->file('extra1')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto7Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto7Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto7Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto7Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto7Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                            }

                            //Extra 2
                            $extra2 = "";
                            if (request()->hasFile('extra2')) {
                                $foto8Bruta = 'Extra2-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('extra2')->getClientOriginalExtension();
                                $extra2 = request()->file('extra2')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto8Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto8Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto8Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto8Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto8Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                            }

                            //Extra 3
                            $extra3 = "";
                            if (request()->hasFile('extra3')) {
                                $foto9Bruta = 'Extra3-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('extra3')->getClientOriginalExtension();
                                $extra3 = request()->file('extra3')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto9Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto9Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto9Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto9Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto9Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                            }

                            //Extra 4
                            $extra4 = "";
                            if (request()->hasFile('extra4')) {
                                $foto10Bruta = 'Extra4-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('extra4')->getClientOriginalExtension();
                                $extra4 = request()->file('extra4')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto10Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto10Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto10Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto10Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto10Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                            }

                            //Extra 5
                            $extra5 = "";
                            if (request()->hasFile('extra5')) {
                                $foto11Bruta = 'Extra5-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('extra5')->getClientOriginalExtension();
                                $extra5 = request()->file('extra5')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto11Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto11Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto11Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto11Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto11Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                            }

                            //Extra 6
                            $extra6 = "";
                            if (request()->hasFile('extra6')) {
                                $foto12Bruta = 'Extra6-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('extra6')->getClientOriginalExtension();
                                $extra6 = request()->file('extra6')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto12Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto12Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto12Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto12Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto12Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                            }

                            //Ingresar nuevo registro a base de datos
                            $choferAsignado = null;
                            $existeAsignacion = DB::select("SELECT vu.id_usuario FROM vehiculosusuarios vu WHERE vu.id_franquicia= '$idFranquicia' AND vu.id_vehiculo = '$idVehiculo' ORDER BY vu.created_at DESC");
                            if($existeAsignacion != null){
                                $choferAsignado = $existeAsignacion[0]->id_usuario;
                            }

                            DB::table('vehiculossupervision')->insert([
                                'id_franquicia' => $idFranquicia, 'id_usuario' => $choferAsignado, 'id_vehiculo' => $idVehiculo, 'estado' => '0',
                                'kilometraje1' => $kilometraje1, 'kilometraje2' => $kilometraje2, 'ladoizquierdo' => $ladoizquierdo, 'ladoderecho' => $ladoderecho,
                                'frente' => $frente, 'atras' => $atras, 'extra1' => $extra1, 'extra2' => $extra2, 'extra3' => $extra3, 'extra4' => $extra4, 'extra5' => $extra5,
                                'extra6' => $extra6, 'created_at' => Carbon::now()
                            ]);

                            //Registrar movimiento sucursal
                            $id_usuarioC = Auth::user()->id;
                            self::insertarHistorialSucursalVehiculos($idFranquicia,$id_usuarioC,$idVehiculo,"Registro supervisión para vehículo numero de serie: '" .$existeVehiculo[0]->numserie . "' placas: '" .$existeVehiculo[0]->placas ."'");

                            return redirect()->route('vervehiculo',[$idFranquicia, $idVehiculo])->with('bien', 'Supervisión de vehículo registrada correctamente.');

                        } catch (\Exception $e) {
                            \Log::error('Error: ' . $e->getMessage());
                            return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina.');
                        }

                    }else{
                        //Existe una supervision pendiente
                        return back()->with('alerta', "Existe una supervisión pendiente por autorizar con fecha: '". Carbon::parse($supervisionpendiente[0]->created_at)->format("Y-m-d") ."'. Por favor, solicita la autorización primero y luego vuelve a intentarlo.");
                    }

                }else{
                    //Vehiculo en estatus de inactivo
                    return back()->with('alerta', 'No puedes crear una nueva supervisión debido que el vehículo fue dado de baja.');
                }

            }else{
                //No existe vehiculo registrado
                return back()->with('alerta', 'No se encontro el vehiculo.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }


    public function actualizarsupervisionvehiculo($idFranquicia, $idVehiculo, $idSupervision){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 17 || Auth::user()->rol_id == 4)){

            $existeVehiculo = DB::select("SELECT * FROM vehiculos v WHERE v.indice = '$idVehiculo' ORDER BY v.created_at DESC LIMIT 1");

            if($existeVehiculo != null){
                //Existe vehiculo registrado
                if($existeVehiculo[0]->estado == 1){
                    //Vehiculo en estado de activo
                    $indiceVehiculo = $existeVehiculo[0]->indice;

                    $supervisionActualizar = DB::select("SELECT * FROM vehiculossupervision vs WHERE vs.id_franquicia= '$idFranquicia' AND vs.id_vehiculo = '$indiceVehiculo'
                                                          AND vs.indice = '$idSupervision' ORDER BY vs.created_at DESC");
                    if($supervisionActualizar != null){
                        //Existe supervision de vehiculo del dia de hoy

                        //Horarios permitidos para generar actualizaciones
                        $horarioActualizacionFotos = DB::select("SELECT vh.horalimitechoferfoto1, horalimitechoferfoto2 FROM vehiculoshorariosupervision vh WHERE vh.id_franquicia = '$idFranquicia'");

                        return view('administracion.vehiculos.actualizarsupervisionvehiculo', [
                            'idFranquicia' => $idFranquicia, 'idVehiculo' => $idVehiculo, 'idSupervision' => $idSupervision, 'horarioActualizacionFotos' => $horarioActualizacionFotos,
                            'supervisionActualizar' => $supervisionActualizar
                        ]);

                    }else{
                        //Existe supervision por autorizar
                        return back()->with('alerta', 'No existe una supervisión.');
                    }
                }else{
                    //Vehiculo en estatus de inactivo
                    return back()->with('alerta', 'No puedes actualizar la supervisión debido que el vehículo fue dado de baja.');
                }
            }else{
                //No existe vehiculo registrado
                return back()->with('alerta', 'No se encontro el vehiculo.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function editarsupervisionvehiculo($idFranquicia, $idVehiculo, $idSupervision){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 17 || Auth::user()->rol_id == 4)){
            $existeVehiculo = DB::select("SELECT v.indice, v.numserie, v.placas, v.estado FROM vehiculos v WHERE v.indice = '$idVehiculo'");
            if($existeVehiculo != null){
                //Existe vehiculo registrado
                if($existeVehiculo[0]->estado == 1){
                    //Vehiculo en estatus activo
                    request()->validate([
                        'kilometraje1' => 'nullable|image|mimes:jpg',
                        'kilometraje2' => 'nullable|image|mimes:jpg',
                        'ladoizquierdo' => 'nullable|image|mimes:jpg',
                        'ladoderecho' => 'nullable|image|mimes:jpg',
                        'frente' => 'nullable|image|mimes:jpg',
                        'atras' => 'nullable|image|mimes:jpg',
                        'extra1' => 'nullable|image|mimes:jpg',
                        'extra2' => 'nullable|image|mimes:jpg',
                        'extra3' => 'nullable|image|mimes:jpg',
                        'extra4' => 'nullable|image|mimes:jpg',
                        'extra5' => 'nullable|image|mimes:jpg',
                        'extra6' => 'nullable|image|mimes:jpg'
                    ]);

                    $numSerie = $existeVehiculo[0]->numserie;

                    $existeSupervisionBD = DB::select("SELECT * FROM vehiculossupervision vs WHERE vs.id_franquicia = '$idFranquicia' AND vs.indice = '$idSupervision'");
                    if($existeSupervisionBD != null){
                        //Si existe supervision en BD

                        if ((Auth::user()->rol_id == 4 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 17) && $existeSupervisionBD[0]->estado != 0) {
                            //Supervision ya aprobada
                            return redirect()->route('vervehiculo', [$idFranquicia, $idVehiculo])->with('alerta', 'No puedes actualizar las fotos debido al estatus de la supervisón.');
                        }

                        $archivosModificado = "";
                        try{
                            //Kilometraje mañana
                            $kilometraje1 = "";
                            if (request()->hasFile('kilometraje1')) {
                                $foto1Bruta = 'Kilometraje1-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('kilometraje1')->getClientOriginalExtension();
                                $kilometraje1 = request()->file('kilometraje1')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto1Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto1Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto1Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto1Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto1Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                                $archivosModificado = $archivosModificado . "Kilometraje mañana,";
                            }else{
                                //Tomar registro de BD
                                $kilometraje1 = $existeSupervisionBD[0]->kilometraje1;
                            }

                            //Kilometraje tarde
                            $kilometraje2 = "";
                            if (request()->hasFile('kilometraje2')) {
                                $foto2Bruta = 'Kilometraje2-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('kilometraje2')->getClientOriginalExtension();
                                $kilometraje2 = request()->file('kilometraje2')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto2Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto2Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto2Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto2Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto2Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                                $archivosModificado = $archivosModificado . "Kilometraje tarde,";
                            }else{
                                //Tomar registro de BD
                                $kilometraje2 = $existeSupervisionBD[0]->kilometraje2;
                            }

                            //Lado izquierdo
                            $ladoizquierdo = "";
                            if (request()->hasFile('ladoizquierdo')) {
                                $foto3Bruta = 'LadoIzquierdo-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('ladoizquierdo')->getClientOriginalExtension();
                                $ladoizquierdo = request()->file('ladoizquierdo')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto3Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto3Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto3Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto3Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto3Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                                $archivosModificado = $archivosModificado . "Lado izquierdo,";
                            }else{
                                //Tomar registro de BD
                                $ladoizquierdo = $existeSupervisionBD[0]->ladoizquierdo;
                            }

                            //Lado derecho
                            $ladoderecho = "";
                            if (request()->hasFile('ladoderecho')) {
                                $foto4Bruta = 'LadoDerecho-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('ladoderecho')->getClientOriginalExtension();
                                $ladoderecho = request()->file('ladoderecho')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto4Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto4Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto4Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto4Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto4Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                                $archivosModificado = $archivosModificado . "Lado derecho,";
                            }else{
                                //Tomar registro de BD
                                $ladoderecho = $existeSupervisionBD[0]->ladoderecho;
                            }

                            //Frente
                            $frente = "";
                            if (request()->hasFile('frente')) {
                                $foto5Bruta = 'Frente-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('frente')->getClientOriginalExtension();
                                $frente = request()->file('frente')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto5Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto5Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto5Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto5Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto5Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                                $archivosModificado = $archivosModificado . "Frente,";
                            }else{
                                //Tomar registro de BD
                                $frente = $existeSupervisionBD[0]->frente;
                            }

                            //Atras
                            $atras = "";
                            if (request()->hasFile('atras')) {
                                $foto6Bruta = 'Atras-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('atras')->getClientOriginalExtension();
                                $atras = request()->file('atras')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto6Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto6Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto6Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto6Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto6Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                                $archivosModificado = $archivosModificado . "Atras,";
                            }else{
                                //Tomar registro de BD
                                $atras = $existeSupervisionBD[0]->atras;
                            }

                            //Extra 1
                            $extra1 = "";
                            if (request()->hasFile('extra1')) {
                                $foto7Bruta = 'Extra1-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('extra1')->getClientOriginalExtension();
                                $extra1 = request()->file('extra1')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto7Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto7Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto7Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto7Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto7Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                                $archivosModificado = $archivosModificado . "Extra 1,";
                            }else{
                                //Tomar registro de BD
                                $extra1 = $existeSupervisionBD[0]->extra1;
                            }


                            //Extra 2
                            $extra2 = "";
                            if (request()->hasFile('extra2')) {
                                $foto8Bruta = 'Extra2-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('extra2')->getClientOriginalExtension();
                                $extra2 = request()->file('extra2')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto8Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto8Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto8Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto8Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto8Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                                $archivosModificado = $archivosModificado . "Extra 2,";
                            }else{
                                //Tomar registro de BD
                                $extra2 = $existeSupervisionBD[0]->extra2;
                            }

                            //Extra 3
                            $extra3 = "";
                            if (request()->hasFile('extra3')) {
                                $foto9Bruta = 'Extra3-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('extra3')->getClientOriginalExtension();
                                $extra3 = request()->file('extra3')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto9Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto9Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto9Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto9Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto9Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                                $archivosModificado = $archivosModificado . "Extra 3,";
                            }else{
                                //Tomar registro de BD
                                $extra3 = $existeSupervisionBD[0]->extra3;
                            }

                            //Extra 4
                            $extra4 = "";
                            if (request()->hasFile('extra4')) {
                                $foto10Bruta = 'Extra4-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('extra4')->getClientOriginalExtension();
                                $extra4 = request()->file('extra4')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto10Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto10Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto10Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto10Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto10Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                                $archivosModificado = $archivosModificado . "Extra 4,";
                            }else{
                                //Tomar registro de BD
                                $extra4 = $existeSupervisionBD[0]->extra4;
                            }

                            //Extra 5
                            $extra5 = "";
                            if (request()->hasFile('extra5')) {
                                $foto11Bruta = 'Extra5-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('extra5')->getClientOriginalExtension();
                                $extra5 = request()->file('extra5')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto11Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto11Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto11Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto11Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto11Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                                $archivosModificado = $archivosModificado . "Extra 5,";
                            }else{
                                //Tomar registro de BD
                                $extra5 = $existeSupervisionBD[0]->extra5;
                            }

                            //Extra 6
                            $extra6 = "";
                            if (request()->hasFile('extra6')) {
                                $foto12Bruta = 'Extra6-Vehiculos-Supervision' . $numSerie . '-' . time() . '.' . request()->file('extra6')->getClientOriginalExtension();
                                $extra6 = request()->file('extra6')->storeAs('uploads/imagenes/vehiculos/supervisionimagenes', $foto12Bruta, 'disco');
                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto12Bruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto12Bruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto12Bruta)->resize(600, 800);
                                } else {
                                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/vehiculos/supervisionimagenes/' . $foto12Bruta)->resize(800, 600);
                                }
                                $imagenfoto->save();
                                $archivosModificado = $archivosModificado . "Extra 6,";
                            }else{
                                //Tomar registro de BD
                                $extra6 = $existeSupervisionBD[0]->extra6;
                            }

                            DB::table('vehiculossupervision')->where("indice","=","$idSupervision")->update([
                                'kilometraje1' => $kilometraje1, 'kilometraje2' => $kilometraje2, 'ladoizquierdo' => $ladoizquierdo, 'ladoderecho' => $ladoderecho,
                                'frente' => $frente, 'atras' => $atras, 'extra1' => $extra1, 'extra2' => $extra2, 'extra3' => $extra3, 'extra4' => $extra4, 'extra5' => $extra5,
                                'extra6' => $extra6, 'updated_at' => Carbon::now()
                            ]);

                            //Registrar movimiento sucursal
                            if($archivosModificado != ""){
                                $archivosModificado = trim($archivosModificado,",");
                                $id_usuarioC = Auth::user()->id;
                                self::insertarHistorialSucursalVehiculos($idFranquicia,$id_usuarioC,$existeVehiculo[0]->indice,"Actualizo foto: '". $archivosModificado ."' para vehículo con numero de serie: '" .$existeVehiculo[0]->numserie . "' placas: '" .$existeVehiculo[0]->placas ."'");
                            }

                            return redirect()->route('vervehiculo',[$idFranquicia, $idVehiculo])->with('bien', 'Supervisión de vehículo registrada correctamente.');

                        } catch (\Exception $e) {
                            \Log::error('Error: ' . $e->getMessage());
                            return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina.');
                        }

                    }else{
                        //No existe supervision de vehiculo
                        return back()->with('alerta', 'No se encontro supervisión registrada para el vehiculo.');
                    }
                }else{
                    //Vehiculo en estatus de inactivo
                    return back()->with('alerta', 'No puedes actualizar la supervisión debido que el vehículo fue dado de baja.');
                }

            }else{
                //No existe vehiculo registrado
                return back()->with('alerta', 'No se encontro el vehiculo.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function autorizarsupervisionvehiculo($idFranquicia, $idSupervision){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8)){
            $existeSupervision = DB::select("SELECT * FROM vehiculossupervision vs WHERE vs.id_franquicia= '$idFranquicia' AND vs.indice = '$idSupervision' AND vs.estado = '0' ORDER BY vs.created_at DESC");

            if($existeSupervision != null){
                //Si existe solicitud de supervision
                $registroKilometraje1 = ($existeSupervision[0]->kilometraje1 != null)?$existeSupervision[0]->kilometraje1 : "";
                $registroKilometraje2 = ($existeSupervision[0]->kilometraje2 != null)?$existeSupervision[0]->kilometraje2 : "";
                if(strlen($registroKilometraje1) > 0 && strlen($registroKilometraje2) > 0){
                    //Cuenta con al menos las dos fotografias del kilometraje
                    $indiceVehiculo = $existeSupervision[0]->id_vehiculo;
                    $existeVehiculo = DB::select("SELECT v.numserie, v.placas, v.estado FROM vehiculos v WHERE v.indice = '$indiceVehiculo' AND v.id_franquicia = '$idFranquicia'");

                    if($existeVehiculo != null){
                        if($existeVehiculo[0]->estado == 1){
                            //Vehiculo en estado activo - Actualizar estado supervision
                            DB::table('vehiculossupervision')->where('indice', '=', $idSupervision)->update(['estado' => '1']);

                            //Registrar movimiento sucursal
                            $id_usuarioC = Auth::user()->id;
                            self::insertarHistorialSucursalVehiculos($idFranquicia,$id_usuarioC,$indiceVehiculo,"Autorizó supervisión para vehículo numero de serie: '" .$existeVehiculo[0]->numserie . "' placas: '" .$existeVehiculo[0]->placas ."'");

                            return back()->with('bien', 'Supervisión de vehículo autorizada correctamente.');
                        }else{
                            return back()->with('alerta', 'No puedes autorizar la supervisión debido que el vehículo fue dado de baja.');
                        }

                    }else{
                        //No existe vehiculo
                        return back()->with('alerta', 'El vehículo asigando a supervisón no existe.');
                    }

                }else{
                    //No cuenta con las dos imagenes del kilometraje
                    return back()->with('alerta', 'No puedes autorizar la supervisión debido que no cuenta con las dos imagenes del kilometraje.');
                }
            }else{
                //No existe solicitud de supervision de vehiculo
                return back()->with('alerta', 'No se encontro la solicitud de supervisión.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarhorariolimitechofer($idFranquicia){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8)){

            //Reglas para roles Administracion - Principal
            request()->validate([
                'horalimiteFoto1' => 'required'
            ]);

            //Datos del formulario
            $horalimiteFoto1 = request()->input('horalimiteFoto1');
            $horalimiteFoto2 = request()->input('horalimiteFoto2');

            $formato = 'H:i';
            //validar fechas
            $horaTemporal = DateTime::createFromFormat($formato, $horalimiteFoto1);
            $horaTemporal2 = DateTime::createFromFormat($formato, $horalimiteFoto2);

            if(($horaTemporal && $horaTemporal->format($formato) == $horalimiteFoto1)  && ($horaTemporal2 && $horaTemporal2->format($formato) == $horalimiteFoto2) ){
                //Horas correctas

                if($horaTemporal < $horaTemporal2){

                    $existeHorarioRegistrado = DB::select("SELECT vh.horalimitechoferfoto1 FROM vehiculoshorariosupervision vh WHERE vh.id_franquicia = '$idFranquicia'");

                    if($existeHorarioRegistrado != null){
                        //Existe horario ya registrado - Actualizar
                        DB::table('vehiculoshorariosupervision')->where('id_franquicia', '=', $idFranquicia)
                            ->update(['horalimitechoferfoto1' => $horalimiteFoto1, 'horalimitechoferfoto2' => $horalimiteFoto2]);
                    }else{
                        //Primera vez que se ingresara horario
                        DB::table('vehiculoshorariosupervision')->insert([
                            'id_franquicia' => $idFranquicia, 'horalimitechoferfoto1' => $horalimiteFoto1, 'horalimitechoferfoto2' => $horalimiteFoto2, 'created_at' => Carbon::now()
                        ]);
                    }

                    //Registrar movimiento
                    $id_usuario = Auth::id();
                    DB::table('historialsucursal')->insert([
                        'id_usuarioC' => $id_usuario,'id_franquicia' => $idFranquicia, 'tipomensaje' => '0',
                        'created_at' => Carbon::now(), 'cambios' => 'Actualizo hora limite para actualizar fotos vehiculo', 'seccion' => '5'
                    ]);

                    return back()->with('bien',' Hora limite para actualización de imagenes vehículos registrada correctamente.');

                }else{
                    return back()->with('alerta',"La hora limite para actualizar 'Foto 1' no debe ser mayor a hora limite 'Foto 2'.");
                }

            }else{
                //Horas seleccionadas con formato no valido
                return back()->with('alerta','Verifica el horario de atencion.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function eliminarVehiculo(Request $request){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8)){

            $idFranquicia =  $request->input('idFranquicia');
            $idVehiculo = $request->input('idVehiculo');

            $existeVehiculo = DB::select("SELECT * FROM vehiculos v WHERE v.indice = '$idVehiculo' AND v.estado = '1'");

            if($existeVehiculo != null){
                //Existe el vehiculo
                if($existeVehiculo[0]->estado == 1){
                    //Vehiculo con estado activo

                    $numSerie = $existeVehiculo[0]->numserie;
                    DB::table("vehiculos")->where("indice","=", $idVehiculo)->where("id_Franquicia","=",$idFranquicia)->update([
                        'estado' => '0',
                    ]);

                    //Registrar movimiento
                    $id_usuario = Auth::id();
                    DB::table('historialsucursal')->insert([
                        'id_usuarioC' => $id_usuario,'id_franquicia' => $idFranquicia, 'tipomensaje' => '0',
                        'created_at' => Carbon::now(), 'cambios' => "Eliminó vehículo con numero de serie: '" . $numSerie . "'", 'seccion' => '5'
                    ]);

                    return back()->with('bien','Eliminación de vehiculo correcta.');

                }else{
                    //Vehiculo ya a sido dado de baja
                    return back()->with('alerta', 'Vehículo ya se encuentra dado de baja del sistema.');
                }

            }else{
                //No existe vehiculo registrado
                return back()->with('alerta', 'No se encontro el vehiculo.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    private function insertarHistorialSucursalVehiculos($idFranquicia, $idUsuarioC, $referencia, $cambio){
        DB::table('historialsucursal')->insert([
            'id_usuarioC' => $idUsuarioC, 'id_franquicia' => $idFranquicia, 'referencia' => $referencia,
            'tipomensaje' => '0', 'created_at' => Carbon::now(), 'cambios' => $cambio, 'seccion' => '5'
        ]);
    }

    public static function generarIdentificadorVehiculo($marca, $idFranquicia){

        //Obtener 2 primeras letras de la marca
        $idMarca = strtoupper(substr($marca,0,2));

        //Extraer ultimo identificador existente en BD
        $ultimoIdentificador = DB::select("SELECT v.identificador FROM vehiculos v WHERE v.id_franquicia = '$idFranquicia' AND v.identificador LIKE '%$idMarca' ORDER BY v.created_at DESC LIMIT 1");

        if($ultimoIdentificador != null){
            //Existe al menos un folio en la BD almacenado
            $identificadorBD = $ultimoIdentificador[0]->identificador;
            $sigIdentificador = $identificadorBD + 1;

        }else{
            //Es el primer identificador a ingresar

            //Obtener identificador de franquicia
            $globalesServicioWeb = new globalesServicioWeb();
            $franquicia = DB::select("SELECT f.indice FROM franquicias f WHERE f.id = '$idFranquicia'");
            $indiceFranquicia = $franquicia[0]->indice;
            $idetificadorFranquicia = $globalesServicioWeb::obtenerIdentificadorFranquicia($indiceFranquicia);

            //Forma siguiente identificador
            $sigIdentificador = $idMarca . $idetificadorFranquicia . "001";
        }

        return $sigIdentificador;
    }

    public function actualizarTablaServiciosVehiculos(){
        $servicios = DB::select("SELECT * FROM servicio");
        if($servicios != null){

            foreach ($servicios as $servicio){
                $numSerie = $servicio->id_moto;
                $vehiculo = DB::select("SELECT v.indice, v.numserie FROM vehiculos v WHERE v.numserie = '$numSerie' ORDER BY v.created_at DESC LIMIT 1");
                if($vehiculo != null){
                    if($vehiculo[0]->numserie == $numSerie){
                        $id_vehiculo = $vehiculo[0]->indice;
                        DB::update("UPDATE servicio SET id_vehiculo = '$id_vehiculo' WHERE id_moto = '$numSerie'");
                    }
                }
            }
            return back()->with('bien',"Tabla servicios actualizada correctamente");
        }
    }

}
