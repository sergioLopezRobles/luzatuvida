<?php

namespace App\Http\Controllers\Dominios\Administracion;

use App\Clases\contratosGlobal;
use App\Clases\globalesServicioWeb;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Image;

class campanias extends Controller
{

    public function listacampanias($idFranquicia){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 20)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL - INNOVACION

            $listaCampanias =DB::select("SELECT * FROM campanias c ORDER BY c.created_at DESC");
            $indice = count($listaCampanias) + 1;;

            return view('administracion.campanias.tablacampanias', [
                'idFranquicia' => $idFranquicia, 'listaCampanias' => $listaCampanias, 'indice' => $indice]);

        }else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function crearcampania($idFranquicia){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 20)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL - INNOVACION

            request()->validate([
                'titulo' => 'required|string',
                'foto' => 'required|image|mimes:jpg',
                'fechaInicio' => 'required',
                'fechaFin' => 'required',
                'observaciones' => 'nullable|string'
            ]);

            //Datos del formulario
            $fechaInicio = request()->input('fechaInicio');
            $fechaFin = request()->input('fechaFin');
            $tipoReferencia = request()->input('swReferenciaAutomatica');
            $tipoReferencia = ($tipoReferencia != null)? $tipoReferencia: '0';

            $formato = 'Y-m-d';
            //validar fechas
            $fechaTemporalInicio = DateTime::createFromFormat($formato, $fechaInicio);
            $fechaTemporalFin = DateTime::createFromFormat($formato, $fechaFin);

            if(($fechaTemporalInicio && $fechaTemporalInicio->format($formato) == $fechaInicio)  && ($fechaTemporalFin && $fechaTemporalFin->format($formato) == $fechaFin) ){
                //Horas correctas

                if($fechaFin > $fechaInicio){

                    //Generar id de camapia
                    $globalesServicioWeb = new globalesServicioWeb();
                    $idCampania = $globalesServicioWeb::generarIdAlfanumerico('campanias', '5');

                    $foto = "";
                    if (request()->hasFile('foto')) {
                        $fotoBruta = 'Foto-Campania-' . $idCampania . '-' . time() . '.' . request()->file('foto')->getClientOriginalExtension();
                        $foto = request()->file('foto')->storeAs('uploads/imagenes/campania', $fotoBruta, 'disco');
                        $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/campania/' . $fotoBruta)->height();
                        $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/campania/' . $fotoBruta)->width();
                        if ($alto > $ancho) {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/campania/' . $fotoBruta)->resize(600, 800);
                        } else {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/campania/' . $fotoBruta)->resize(800, 600);
                        }
                        $imagenfoto->save();
                    }

                    DB::table('campanias')->insert([
                        'titulo' => request()->input('titulo'), 'id' => $idCampania, 'foto' => $foto, 'fechainicio' => $fechaInicio,
                        'fechafinal' => $fechaFin,  'observaciones' => request()->input('observaciones'), 'estado' => '1', 'tiporeferencia' => $tipoReferencia,
                        'created_at' => Carbon::now()
                    ]);

                    //Registrar movimiento
                    $id_usuario = Auth::id();
                    DB::table('historialsucursal')->insert([
                        'id_usuarioC' => $id_usuario,
                        'id_franquicia' => $idFranquicia, 'tipomensaje' => '15',
                        'created_at' => Carbon::now(),
                        'cambios' => "Creo campaña con titulo: '" . request()->input('titulo') . "'",
                        'seccion' => '2'
                    ]);

                    return back()->with('bien','Campañia agregada correctamente.');

                } else {
                    //Fecha final menor a fecha inicial
                    return back()->with('alerta','Fecha final debe ser mayor o igual a fecha inicial.');
                }

            }else{
                //fechas seleccionadas con formato no valido
                return back()->with('alerta','Fechas ingresadas no validas.');
            }
        }else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function vercampania($idFranquicia, $idCampania){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 20)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL - INNOVACION

            $campania = DB::select("SELECT c.titulo, c.foto, c.id, c.tiporeferencia,c.estado FROM campanias c WHERE c.id = '$idCampania' ORDER BY c.created_at DESC LIMIT 1");

            if($campania != null){
                //Existe la campania

                return view('administracion.campanias.vercampania', [
                    'idFranquicia' => $idFranquicia, 'campania' => $campania]);

            }else{
                return back()->with('alerta','No existe la campaña.');
            }

        }else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function cargarlistacampaniasagendadas(){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 20)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL - INNOVACION

            $idCampania = request('idCampania');

            $listaCampaniasAgendadas = DB::select("SELECT ca.indice, ca.id_campania, ca.nombre, ca.telefono, ca.referencia,
                                                         ca.created_at, ca.updated_at, ca.observaciones, ca.estado,
                                                         (SELECT c.tiporeferencia FROM campanias c WHERE c.id = ca.id_campania) as tiporeferencia
                                                         FROM campaniasagendadas ca WHERE ca.id_campania = '$idCampania' ORDER BY ca.created_at DESC");

            $indice = count($listaCampaniasAgendadas) + 1;

            $view = view('administracion.campanias.listas.listacampaniasagendadas', [
                'listaCampaniasAgendadas' => $listaCampaniasAgendadas, 'indice' => $indice
            ])->render();

            return \Response::json(array("valid"=>"true","view"=>$view));

        }else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function agendarcampania() {
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 20)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL - INNOVACION
            $idCampania = request('idCampania');
            $idFranquicia = request('idFranquicia');
            $tipoReferencia = request('tipoReferencia');
            $nombre = request('nombre');
            $telefono = request('telefono');
            $observaciones = request('observaciones');
            $referencia = request('referencia');

            $existeCampania = DB::select("SELECT c.id, c.titulo, c.estado FROM campanias c WHERE c.id = '$idCampania'");

            //Validar si usuario tiene permisos de creacion en seccion de campañas o es rol de innovacion
            $contratosGlobal = new contratosGlobal();
            if($contratosGlobal::validarPermisoSeccion(Auth::user()->id, 6, 0) || Auth::user()->rol_id == 20){
                if($existeCampania != null){
                    //Campania registrada

                    if($existeCampania[0]->estado == 1){
                        //Campania con estado activo

                        if($tipoReferencia == 1){
                            //Selecciono generar numero de referencia de forma automatica
                            $referencia = self::generarReferenciaCamapnia($idCampania);
                        }else{
                            //Numero de referencia ingresado de forma manual
                            $referencia = request()->input('referencia');
                            //Validar que referencia solo tenga 5 digitos si es ingresado de forma manual
                            if(strlen($referencia) > 0){
                                //Contiene al menos un digito
                                //Dar formato de 5 digitos a folio referencia ingresado
                                $referencia = self::formatoReferenciaManual($referencia);
                                //Concatenar folio de referencia con idCampania para formar numero de referencia completo
                                $referencia = $idCampania . $referencia;
                                //Validar si existe ya registrada ese numero de referencia
                                $existeReferencia = DB::select("SELECT r.referencia FROM referencias r WHERE r.referencia = '$referencia' AND r.tipo = '02'");

                                if($existeReferencia != null) {
                                    //Numero de referencia ya registrado
                                    $mensaje = "Numero de referencia ingresado ya se encuentra registrado, intenta con uno distinto.";
                                    $response = ['bandera' => false, 'mensaje' => $mensaje];
                                    return response()->json($response);
                                }
                            }else{
                                //No contiene los 5 digitos
                                $mensaje = "Ingresa numero de referencia e intenta de nuevo.";
                                $response = ['bandera' => false, 'mensaje' => $mensaje];
                                return response()->json($response);
                            }
                        }

                        //Validar formato del telefono
                        if (!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{2}-[0-9]{2}$/", $telefono) && !preg_match("/^[0-9]{10}$/", $telefono)) {
                            //Telefono no cumple con formato de: tel:3333333333 o 333-333-33-33
                            $mensaje = "Verifica el formato para numero de telefono. <br>Ejemplo: 3333333333 o 333-333-33-33.";
                            $response = ['bandera' => false, 'mensaje' => $mensaje];
                            return response()->json($response);
                        }

                        DB::table('campaniasagendadas')->insert([
                            'id_campania' => $idCampania, 'nombre' => $nombre, 'telefono' => $telefono,
                            'observaciones' => $observaciones, 'referencia' => $referencia, 'estado' => '0',
                            'created_at' => Carbon::now()
                        ]);

                        //Registrar numero de referencia en tabla de referencias de tipo cita
                        DB::table('referencias')->insert([
                            'tipo' => '02', 'referencia' => $referencia, 'created_at' => Carbon::now()
                        ]);

                        //Registrar movimiento
                        $id_usuario = Auth::id();
                        DB::table('historialsucursal')->insert([
                            'id_usuarioC' => $id_usuario,
                            'id_franquicia' => $idFranquicia, 'tipomensaje' => '15',
                            'created_at' => Carbon::now(),
                            'cambios' => "Agendo paciente con nombre: '" . $nombre . "' para campaña con titulo: '" . $existeCampania[0]->titulo."'",
                            'seccion' => '2'
                        ]);

                        //Retornar valores a la vista
                        $ultimaReferencia = $referencia;
                        $mensaje = "Campaña agendada correctamente.";
                        $response = ['bandera' => true, 'mensaje' => $mensaje, 'ultimaReferencia' => $ultimaReferencia];
                        return response()->json($response);

                    }else{
                        //Campania esta desactivada
                        $mensaje = "No puedes agendar a esta campaña debido a que se encuentra desactivada.";
                        $response = ['bandera' => false, 'mensaje' => $mensaje];
                        return response()->json($response);
                    }

                }else{
                    //No existe la campania
                    $mensaje = "Campaña no regitsrada.";
                    $response = ['bandera' => false, 'mensaje' => $mensaje];
                    return response()->json($response);
                }

            }else{
                //No cuentas con los permisos
                $mensaje = "No cuentas con los permisos necesarios.";
                $response = ['bandera' => false, 'mensaje' => $mensaje];
                return response()->json($response);
            }

        }else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function actualizarcampania(){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 20)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL - INNOVACION

            request()->validate([
                'fotoModal' => 'nullable|image|mimes:jpg',
                'tipoReferenciaModal' => 'nullable',
                'tituloModal' => 'nullable|string',
                'fechaInicioModal' => 'nullable',
                'fechaFinModal' => 'nullable',
                'observacionesModal' => 'nullable|string'
            ]);

            $idCampania = request('idCampaniaModal');
            $idFranquicia = request('idFranquicia');
            $titulo = request('tituloModal');
            $tipoReferencia = request('swReferenciaAutomaticaActualizar');
            $tipoReferencia = ($tipoReferencia != null)? $tipoReferencia: '0';
            $fechaInicio = request()->input('fechaInicioModal');
            $fechaFin = request()->input('fechaFinModal');
            $observaciones = request('observacionesModal');
            $cambios = "";

            $formato = 'Y-m-d';
            //validar fechas
            $fechaTemporalInicio = DateTime::createFromFormat($formato, $fechaInicio);
            $fechaTemporalFin = DateTime::createFromFormat($formato, $fechaFin);

            $existeCampania = DB::select("SELECT * FROM campanias c WHERE c.id = '$idCampania'");

            if($existeCampania != null){

                //Validar si se desea actualizar la forma de registrar numero de referencia y ya se cuentan con personas registradas para la campaña
                $existenRegistrosCampania = DB::select("SELECT ca.nombre FROM campaniasagendadas ca WHERE ca.id_campania = '$idCampania'");

                if($existenRegistrosCampania != null && $existeCampania[0]->tiporeferencia != $tipoReferencia) {
                    //Ya existen registros para la campaña y valor del formulario de actualizar es distinto al tiporeferencia de BD
                    return back()->with('alerta', 'No puedes actualizar la forma para registrar el numero de referencia debido que ya cuentas con registros para esta campaña.');
                }

                if(($fechaTemporalInicio && $fechaTemporalInicio->format($formato) == $fechaInicio)  && ($fechaTemporalFin && $fechaTemporalFin->format($formato) == $fechaFin) ){
                    //Horas correctas
                    if($fechaFin > $fechaInicio){

                        $foto = "";
                        if (request()->hasFile('fotoModal')) {
                            $fotoBruta = 'Foto-Campania-' . $idCampania . '-' . time() . '.' . request()->file('fotoModal')->getClientOriginalExtension();
                            $foto = request()->file('fotoModal')->storeAs('uploads/imagenes/campania', $fotoBruta, 'disco');
                            $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/campania/' . $fotoBruta)->height();
                            $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/campania/' . $fotoBruta)->width();
                            if ($alto > $ancho) {
                                $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/campania/' . $fotoBruta)->resize(600, 800);
                            } else {
                                $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/campania/' . $fotoBruta)->resize(800, 600);
                            }
                            $imagenfoto->save();
                            //Eliminar foto actual
                            Storage::disk('disco')->delete($existeCampania[0]->foto);
                        }else{
                            $foto = $existeCampania[0]->foto;
                        }

                        //Actualizar registro de campaña
                        DB::table('campanias')->where('id', '=', $idCampania)->update([
                            'titulo' => $titulo, 'foto' => $foto, 'fechainicio' => $fechaInicio,
                            'fechafinal' => $fechaFin,  'observaciones' => $observaciones, 'tiporeferencia' => $tipoReferencia,
                            'updated_at' => Carbon::now()
                        ]);

                        //Verificar que campos se actualizaron
                        if($foto != $existeCampania[0]->foto){
                            //Cambio titulo de promocion
                            $cambios = $cambios ."Foto,";
                        }
                        if($titulo != $existeCampania[0]->titulo){
                            //Cambio titulo de promocion
                            $cambios = $cambios ."Titulo,";
                        }
                        if($fechaInicio != $existeCampania[0]->fechainicio){
                            //Cambio titulo de promocion
                            $cambios = $cambios . "Fecha de inicio,";
                        }
                        if($fechaFin != $existeCampania[0]->fechafinal){
                            //Cambio titulo de promocion
                            $cambios = $cambios . "Fecha final,";
                        }
                        if($tipoReferencia != $existeCampania[0]->tiporeferencia){
                            //Cambio titulo de promocion
                            $cambios = $cambios . "Forma de gerenar numero de referencia,";
                        }
                        if($observaciones != $existeCampania[0]->observaciones){
                            //Cambio titulo de promocion
                            $cambios = $cambios ."Observaciones,";
                        }

                        //Validar si existio algun cambio para registrar el movimeinto
                        if(strlen($cambios) > 0){
                            $cambios = trim($cambios,",");
                            //Registrar movimiento
                            $id_usuario = Auth::id();
                            DB::table('historialsucursal')->insert([
                                'id_usuarioC' => $id_usuario,
                                'id_franquicia' => $idFranquicia, 'tipomensaje' => '15',
                                'created_at' => Carbon::now(),
                                'cambios' => "Actualizo datos de: '". $cambios ."' para campaña con titulo: '" . $existeCampania[0]->titulo . "'",
                                'seccion' => '2'
                            ]);
                        }

                        return back()->with('bien','Campañia actualizada correctamente.');

                    } else {
                        //Fecha final menor a fecha inicial
                        return back()->with('alerta','Fecha final debe ser mayor o igual a fecha inicial.');
                    }

                }else{
                    //fechas seleccionadas con formato no valido
                    return back()->with('alerta','Fechas ingresadas no validas.');
                }

            }else{
                //No existe la camapaña
                return back()->with('alerta','Campaña no registrada.');
            }

        }else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function eliminarcampania(){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 20)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL - INNOVACION

            $idCampania = request('idCampania');
            $idFranquicia = request('idFranquicia');

            $existeCampania = DB::select("SELECT c.id,c.titulo FROM campanias c WHERE c.id = '$idCampania'");
            if($existeCampania != null){
                //Existe la camapaña registrada
                $existenCuponesRegistrados = DB::select("SELECT ca.nombre FROM campaniasagendadas ca WHERE ca.id_campania = '$idCampania' ORDER BY ca.created_at DESC LIMIT 1");
                if($existenCuponesRegistrados == null){
                    //Aun no existe ningun registro para esta campaña

                    try {
                        //Eliminar campaña
                        DB::delete("DELETE FROM campanias WHERE id = '$idCampania'");

                        //Registrar movimiento
                        $id_usuario = Auth::id();
                        DB::table('historialsucursal')->insert([
                            'id_usuarioC' => $id_usuario,
                            'id_franquicia' => $idFranquicia, 'tipomensaje' => '15',
                            'created_at' => Carbon::now(),
                            'cambios' => "Elimino campaña con titulo: '" . $existeCampania[0]->titulo . "'",
                            'seccion' => '2'
                        ]);

                        return back()->with('bien','Campaña eliminada correctamente');

                    } catch (\Exception $e){
                        \Log::info("Error: " . $e->getMessage());
                        return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina   Error:' . $e->getMessage());
                    }

                }else{
                    //Ya existen personas agendadas para esa campaña
                    return back()->with('alerta','No puedes eliminar la campaña debido que ya existen personas registradas.');
                }

            }else{
                //No existe la campaña
                return back()->with('alerta','No existe la campaña.');
            }

        }else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizardatospaciente(){
        if (Auth::check() && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 20)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL - INNOVACION
            $idCampania = request('idCampana');
            $idCuponCampana = request('idCuponCampana');
            $nombre = request('nombrePaciente');
            $telefono = request('telefonoPaciente');
            $telefono = str_replace("-","",$telefono);
            $referencia = request('numReferencia');
            $observaciones = request('observaciones');
            $mensajeErrores = "";

            //Validar si usuario tiene permisos de creacion en seccion de campañas o es rol de innovacion
            $contratosGlobal = new contratosGlobal();
            if($contratosGlobal::validarPermisoSeccion(Auth::user()->id, 6, 0) || Auth::user()->rol_id == 20){

                $cupon = DB::select("SELECT ca.indice, ca.id_campania, ca.nombre, ca.telefono, ca.referencia,
                                                  ca.created_at, ca.updated_at, ca.observaciones, ca.estado,
                                                  (SELECT c.tiporeferencia FROM campanias c WHERE c.id = ca.id_campania) as tiporeferencia
                                                  FROM campaniasagendadas ca WHERE ca.indice = '$idCuponCampana' AND ca.id_campania = '$idCampania'");

                if($cupon != null){
                    //Existe el cupon registrado
                    if($cupon[0]->estado == 0){
                        //Aun tiene estatus de 0, todavia no se hace valido el cupon
                        //Validar nombre
                        if($nombre == null || $nombre == ""){
                            //Nombre vacio
                            $mensajeErrores = "Campo de nombre paciente obligatorio.<br>";
                        }

                        //Validar telefono
                        if($telefono == null || $telefono == ""){
                            //Telefono vacio
                            $mensajeErrores = $mensajeErrores ."Campo de telefono paciente obligatorio.<br>";
                        }else{
                            //Validar formato telefono
                            if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{2}-[0-9]{2}$/", $telefono) && !preg_match("/^[0-9]{10}$/", $telefono)){
                                $mensajeErrores = $mensajeErrores . "Numero de telefono incorrecto. Ej: 3333333333 | 333-333-33-33.<br>";
                            }
                        }

                        //Validar referencia
                        if($referencia == null || $referencia == ""){
                            //Nombre vacio
                            $mensajeErrores = $mensajeErrores . "Campo de nombre referencia obligatorio.<br>";
                        }

                        if($mensajeErrores == ""){
                            //Cumplio con todas las validaciones

                            //Tipo de referencia
                            if($cupon[0]->tiporeferencia == 0){
                                //Referencia manual
                                $referencia = self::formatoReferenciaManual($referencia);
                                //Concatenar idCampaña para formato completo
                                $referencia = $idCampania . $referencia;
                                //Verificar si existe
                                $existeReferencia = DB::select("SELECT ca.referencia FROM campaniasagendadas ca WHERE ca.referencia = '$referencia' AND ca.indice != '$idCuponCampana'");

                                if($existeReferencia != null){
                                    //Ya existe la referencia en otro cupon
                                    return back()->with('alerta',"Ya se encuentra registrado el numero de referencia: '" . $referencia . "'. Intenta ingresando uno distinto.");
                                }
                            }else{
                                //Referencia automatica
                                $referencia = $idCampania . $referencia;
                            }

                            //Actualizar datos de cupon
                            DB::table('campaniasagendadas')->where('indice', '=', $idCuponCampana)->update([
                                'nombre' => $nombre, 'telefono' => $telefono, 'referencia' => $referencia, 'observaciones' => $observaciones
                            ]);

                            //Actualizar tabla de referencia en caso de ser actualizado el numero de referencia
                            DB::table('referencias')->where('referencia', '=', $cupon[0]->referencia)->where('tipo','=','02')
                                ->update(['referencia' => $referencia]);

                            //Campos modificados
                            $movimiento = "";
                            if($cupon[0]->nombre != $nombre){
                                $movimiento = "nombre,";
                            }

                            if($cupon[0]->telefono != $telefono){
                                $movimiento = $movimiento . "telefono,";
                            }

                            if($cupon[0]->referencia != $referencia){
                                $movimiento = $movimiento . "referencia,";
                            }

                            if($cupon[0]->observaciones != $observaciones){
                                $movimiento = $movimiento . "observaciones,";
                            }


                            //Registrar movimiento
                            $id_usuario = Auth::id();
                            $franquicia = DB::select("SELECT uf.id_franquicia FROM usuariosfranquicia uf WHERE uf.id_usuario = '$id_usuario'");
                            $id_franquicia = "";
                            if($franquicia != null){
                                $id_franquicia = $franquicia[0]->id_franquicia;
                            }

                            if($movimiento != ""){
                                DB::table('historialsucursal')->insert([
                                    'id_usuarioC' => $id_usuario,
                                    'id_franquicia' => $id_franquicia, 'tipomensaje' => '15',
                                    'created_at' => Carbon::now(),
                                    'cambios' => "Actualizó datos:'" . $movimiento. "' para paciente: '" . $cupon[0]->nombre . "' en campaña con codigo: '" . $cupon[0]->id_campania . "' y referencia: '" . $cupon[0]->referencia . "'.",
                                    'seccion' => '2'
                                ]);
                            }

                            return back()->with('bien',"Datos de paciente actualizados correctamente.");

                        }else{
                            //Existen mensajes de errores
                            return back()->with('alerta',$mensajeErrores);
                        }

                    }else{
                        //Ya fue registrado
                        return back()->with('alerta',"Ya no puedes hacer ninguna modificación sobre los datos del paciente debido al estatus actual del cupon.");
                    }

                }else{
                    //No existe el cupon
                    return back()->with('alerta',"No existe registro de paciente.");
                }

            }else{
                //No cuentas con los permisos
                return back()->with('alerta',"No cuentas con los permisos necesarios.");
            }

        }else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function generarReferenciaCamapnia($idCampania){

        //Extraer ultimo identificador existente en BD
        $ultimaReferenciaC = DB::select("SELECT r.referencia FROM referencias r WHERE r.referencia like '$idCampania%' AND r.tipo = '02' ORDER BY r.created_at DESC LIMIT 1");

        if($ultimaReferenciaC != null){
            //Existe al menos una referencia en la BD almacenado
            $ReferenciaBD = $ultimaReferenciaC[0]->referencia;
            $incremental = substr($ReferenciaBD,5,5);
            $incremental = $incremental + 1;

            //Formato para incremental
            switch (strlen($incremental)){
                case 1:
                    $incremental = "0000" . $incremental;
                    break;
                case 2:
                    $incremental = "000" . $incremental;
                    break;
                case 3:
                    $incremental = "00" . $incremental;
                    break;
                case 4:
                    $incremental = "0" . $incremental;
                    break;
                case 5:
                    $incremental = $incremental;
                    break;
            }

            //Formar siguiente referencia
            $sigReferencia = $idCampania . $incremental;

        }else{
            //Es el primera referencia a ingresar
            $sigReferencia = $idCampania . "00001";
        }

        return $sigReferencia;
    }

    public function formatoReferenciaManual($referecia){

        //Eliminar los ceros de la izquierda de la referencia
        $referecniaTemporal = ltrim($referecia, '0');

        //Asignar cantidad de 0 a la izquiera para tamaño de 5 digitos
        switch (strlen($referecniaTemporal)){
            case 1:
                $referenciaFormato = "0000" . $referecniaTemporal;
                break;
            case 2:
                $referenciaFormato = "000" . $referecniaTemporal;
                break;
            case 3:
                $referenciaFormato = "00" . $referecniaTemporal;
                break;
            case 4:
                $referenciaFormato = "0" . $referecniaTemporal;
                break;
            case 5:
                $referenciaFormato = $referecniaTemporal;
                break;
        }

        return $referenciaFormato;
    }
}
