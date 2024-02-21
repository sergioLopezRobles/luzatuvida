<?php

namespace App\Http\Controllers\Dominios\Administracion;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Image;
use Uuid;

class general extends Controller
{
    public function listas($idFranquicia){
        if(Auth::check() && ((Auth::user()->rol_id) == 7))
        {
            $dispositivos = DB::select("SELECT * FROM dispositivos ORDER BY created_at DESC");
            return view('administracion.general.listas',['dispositivos'=>$dispositivos, 'idFranquicia' => $idFranquicia]);
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function dispositivonuevo($idFranquicia){
        if(Auth::check() && ((Auth::user()->rol_id) == 7))
        {
            $continuar = false;
            do{
                $idDispositivo = strtoupper(Uuid::generate()->string);
                $existe = DB::select("SELECT id FROM dispositivos WHERE id = '$idDispositivo'");
                if($existe == null){
                    $continuar = true;
                }
            }while(!$continuar);
            return view('administracion.general.dispositivos.nuevo',["idDispositivo" => $idDispositivo, 'idFranquicia' => $idFranquicia]);
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function dispositivocrear($idFranquicia, Request $request){
        if(Auth::check() && ((Auth::user()->rol_id) == 7))
        {
            $titulo =  request('titulo');
            $descripcion =  request('descripcion');
            $version =  request('version');
            $tipo = request('tipoDispositivoSeleccionado');

            $rules = [
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string|max:255',
                'version' => 'required|string|max:7',
                'apk' => 'required',
                'idDispositivo' => 'required|string',
                'tipoDispositivoSeleccionado' => 'required'
            ];

            request()->validate($rules);

            $extencionApp = '';
            switch ($tipo){
                case '0':
                    //Si es de tipo app movil requerido y apk
                    $extencionApp = 'apk';
                    break;
                case '1':
                    //Si es de tipo app escritorio -> requerido un exe
                    $extencionApp = 'exe';
                    break;

            }

            if(strcmp(request()->file('apk')->getClientOriginalExtension(),$extencionApp)  != 0 ) {
                return back()->withErrors(['apk' => ''])->withInput($request->all());
            }

            $activar = 0;
            $ahora = Carbon::now();
            if(request('activo') == 1){
                $activar = 1;
                $fechaActivacion = "";
            }else{
                $fechaActivacion = request('fechaactivacion');
                if(Carbon::parse($fechaActivacion) <= $ahora){
                    return back()->with(['error'=>'La fecha de activacion no puede ser menor o gual a la fecha actual.'])->withInput($request->all());
                }
            }

            $fotoBruta='Aplicacion-'.time(). '.' . request()->file('apk')->getClientOriginalExtension();
            $apk=request()->file('apk')->storeAs('uploads/aplicaciones', $fotoBruta,'disco');

            if($activar == 1){
                $desactivar = 0;
                DB::table('dispositivos')->where('estatus','=','1')
                    ->where('tipoapp','=',$tipo)
                    ->update(['estatus'=>$desactivar]);
            }

            DB::table('dispositivos')->insert([
                'id'=> $request ->idDispositivo,'titulo' => $titulo,'descripcion' => $descripcion, 'version' => $version, 'apk' => $apk,'fechaactivacion' =>  $fechaActivacion,
                'estatus'=> $activar,'tipoapp' => $tipo,'created_at' => $ahora
            ]);

            //Registrar movimiento en historial sucursal
            // Dejar como sucursal por default TEPIC debido que estos cambios solo seran generados al actualizar App
            DB::table('historialsucursal')->insert([
                'id_usuarioC' => Auth::user()->id, 'id_franquicia' => '6E2AA',
                'tipomensaje' => '8', 'created_at' => Carbon::now(),
                'cambios' => "Registro dispositivo: '" .  $titulo. "' de tipo: '" . $extencionApp . "'", 'seccion' => '2'
            ]);

            return redirect()->route('general',$idFranquicia)->with('bien','El dispositivo se creo correctamente.');

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function dispositivoestatus($idFranquicia, $idDispositivo,$estatus){
        if(Auth::check() && ((Auth::user()->rol_id) == 7))
        {

            $estado = 0;
            if($estatus == 0 ){
                $estado = 1;
                $cambio = "Activo dispositivo con identificador: " . $idDispositivo;
            }else {
                $cambio = "Desactivo dispositivo con identificador: " . $idDispositivo;
            }

            //Obtener tipo de aplicaccion para cambio de estatus
            $dispositivo = DB::select("SELECT d.tipoapp FROM dispositivos d WHERE d.id = '$idDispositivo'");
            $tipoApp = $dispositivo[0]->tipoapp;

            DB::table('dispositivos')->where('estatus','=','1')
                ->where('tipoapp','=',$tipoApp)
                ->update(['estatus' => 0]);

            DB::table('dispositivos')->where('id','=',$idDispositivo)->update([
                'estatus' => $estado
            ]);

            //Registrar movimiento en historialsucursal
            DB::table('historialsucursal')->insert([
                'id_usuarioC' => Auth::user()->id, 'id_franquicia' => '6E2AA',
                'tipomensaje' => '8', 'created_at' => Carbon::now(),
                'cambios' => $cambio, 'seccion' => '2'
            ]);

            return redirect()->route('general', $idFranquicia)->with('bien','El dispositivo de actualizo correctamente.');
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function configuracion($idFranquicia){
        if(Auth::check() && ((Auth::user()->rol_id) == 7))
        {

            $configuracionActual = DB::select("SELECT * FROM temamovil cm WHERE cm.estadoconfiguracion = 1");

            $datosFtp = DB::select("SELECT * FROM configuracionmovil ORDER BY created_at DESC LIMIT 1");

            if ($datosFtp != null) {
                $datosFtp[0]->ruta_ftp = Crypt::decryptString($datosFtp[0]->ruta_ftp);
                $datosFtp[0]->usuario_ftp = Crypt::decryptString($datosFtp[0]->usuario_ftp);
                $datosFtp[0]->contrasena_ftp = Crypt::decryptString($datosFtp[0]->contrasena_ftp);
                $datosFtp[0]->preciodolar = $datosFtp[0]->preciodolar == null ? $datosFtp[0]->preciodolar : Crypt::decryptString($datosFtp[0]->preciodolar);
            }

            return view('administracion.general.configuracion',
                [
                    'configuracionActual' => $configuracionActual,
                    'datosFtp' => $datosFtp,
                    'idFranquicia' => $idFranquicia
                ]);

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function actualizarConfiguracion(){
        if(Auth::check() && ((Auth::user()->rol_id) == 7))
        {

            $logo =  request('imagenLogo');
            $icono =  request('icono');
            $encabezados =  request('encabezados');
            $navbar =  request('navbar');
            $configuracionPredeterminada = request('cbDefault');

            $existeConfiguracion = DB::select("SELECT * FROM temamovil cm WHERE cm.estadoconfiguracion = 1");

            if($existeConfiguracion != null){
                //Si ya existe una configuracion activa
                $idConfiguracion = $existeConfiguracion[0]->indice;

                //Desactivamos la configuracion actual
                DB::table('temamovil')->where('indice','=',$idConfiguracion)->update([
                    'estadoconfiguracion' => 0
                ]);
            }

            //configuracion predeterminada
            if($configuracionPredeterminada == 1){
                //Si esta seleccionado el checkbox
                $existeConfiguracionDefault = DB::select("SELECT * FROM temamovil cm WHERE cm.fotologo = '' AND cm.coloriconos = ''
                                                                AND cm.colorencabezados = '' AND cm.colornavbar = ''");

                if($existeConfiguracionDefault != null){
                    //Si ya se habia registrado antes la configuracion por default
                    //Actualizamos ese registro a un estatus 1 (activar configuracion)
                    $indiceConfiguracionDefault = $existeConfiguracionDefault[0]->indice;

                    DB::table('temamovil')->where('indice','=',$indiceConfiguracionDefault)->update([
                        'estadoconfiguracion' => 1, 'updated_at' => Carbon::now()
                    ]);
                } else {
                    //Si no existe la registramos por primera vez
                    DB::table('temamovil')->insert([
                        'fotologo' => null,'coloriconos' => null, 'colorencabezados' => null, 'colornavbar' => null,
                        'estadoconfiguracion' =>  1,'created_at' => Carbon::now()
                    ]);
                }

            } else {

                //Validacion de tamaño de imagen -> Recmendado a 500x163 px
                $fotoLogo = null;
                if (request()->hasFile('imagenLogo')) {
                    $fotoLogoBruta = 'Logotipo-Configuracion-' . time() . '.' . request()->file('imagenLogo')->getClientOriginalExtension();
                    $fotoLogo = request()->file('imagenLogo')->storeAs('uploads/imagenes/configuracion/logo', $fotoLogoBruta, 'disco');
                    $alto = Image::make(config('filesystems.disks.disco.root').'/uploads/imagenes/configuracion/logo/'.$fotoLogoBruta)->height();
                    $ancho = Image::make(config('filesystems.disks.disco.root').'/uploads/imagenes/configuracion/logo/'.$fotoLogoBruta)->width();
                    if($alto > $ancho){
                        $imagenfoto=Image::make(config('filesystems.disks.disco.root').'/uploads/imagenes/configuracion/logo/'.$fotoLogoBruta)->resize(500,163);
                    }else{
                        $imagenfoto=Image::make(config('filesystems.disks.disco.root').'/uploads/imagenes/configuracion/logo/'.$fotoLogoBruta)->resize(500,163);
                    }
                    $imagenfoto->save();

                }

                //Registramos la configuracion nueva y activamos por default
                DB::table('temamovil')->insert([
                    'fotologo' => $fotoLogo,'coloriconos' => $icono, 'colorencabezados' => $encabezados, 'colornavbar' => $navbar,
                    'estadoconfiguracion' =>  1,'created_at' => Carbon::now()
                ]);
            }

            //Registrar movimiento en historialsucursal
            DB::table('historialsucursal')->insert([
                'id_usuarioC' => Auth::user()->id, 'id_franquicia' => '6E2AA',
                'tipomensaje' => '8', 'created_at' => Carbon::now(),
                'cambios' => " Genero un nuevo tema de diseño para aplicacion movil", 'seccion' => '2'
            ]);

            return back()->with('bien','El tema se actualizó correctamente.');

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function actualizarconfiguracionftp(){

        if(Auth::check() && ((Auth::user()->rol_id) == 7))
        {

            $ruta_ftp =  request('ruta_ftp');
            $usuario_ftp =  request('usuario_ftp');
            $contrasena_ftp =  request('contrasena_ftp');
            $preciodolar =  request('preciodolar');

            if ($ruta_ftp == null || $usuario_ftp == null || $contrasena_ftp == null || $preciodolar == null) {
                //Algunos de los campos esta vacio
                return back()->with('alerta', 'Todos los campos son requeridos.');
            }

            $datosFtp = DB::select("SELECT * FROM configuracionmovil ORDER BY created_at DESC LIMIT 1");

            if($datosFtp != null){
                //Si ya existe una configuracion
                DB::table('configuracionmovil')->where('indice','=', $datosFtp[0]->indice)->update([
                    'ruta_ftp' => Crypt::encryptString($ruta_ftp),
                    'usuario_ftp' => Crypt::encryptString($usuario_ftp),
                    'contrasena_ftp' => Crypt::encryptString($contrasena_ftp),
                    'preciodolar' => Crypt::encryptString($preciodolar),
                    'updated_at' => Carbon::now()
                ]);
            }else {
                //No existe ninguna configuracion
                DB::table('configuracionmovil')->insert([
                    'ruta_ftp' => Crypt::encryptString($ruta_ftp),
                    'usuario_ftp' => Crypt::encryptString($usuario_ftp),
                    'contrasena_ftp' => Crypt::encryptString($contrasena_ftp),
                    'preciodolar' => Crypt::encryptString($preciodolar),
                    'created_at' => Carbon::now()
                ]);
            }

            //Registrar movimiento en historialsucursal
            DB::table('historialsucursal')->insert([
                'id_usuarioC' => Auth::user()->id, 'id_franquicia' => '6E2AA',
                'tipomensaje' => '8', 'created_at' => Carbon::now(),
                'cambios' => " Genero una nueva configuracion para aplicacion movil", 'seccion' => '2'
            ]);

            return back()->with('bien','La configuracion se actualizó correctamente.');

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }

    }

}
