<?php

namespace App\Http\Controllers\Dominios\Administracion;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class promociones extends Controller
{
    public function nuevapromoarmazones($idFranquicia){
			if(Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6))
			{
				request()->validate([
					// 'titulo'=>'required|string|max:255',
					// 'numero'=>'required|integer|min:1'
				]);
				$franquiciaAdmin = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");

                $sucursales = DB::select("SELECT f.id AS id, f.ciudad AS ciudad, f.colonia, f.estado, f.numero FROM franquicias f where id != '00000' ORDER BY f.ciudad ASC");

				return view('administracion.franquicia.administracion.promociones.nuevaarmazon',[
                                 'franquiciaAdmin' => $franquiciaAdmin,'idFranquicia' => $idFranquicia, 'sucursales' => $sucursales]);
			}else{
				if (Auth::check()) {
						return redirect()->route('redireccionar');
				}else{
						return redirect()->route('login');
				}
			}
    }

	public function promocionnueva($idFranquicia){
        if(Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6))
        {
            $franquiciaAdmin = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");
            return view('administracion.franquicia.administracion.nuevapromocion',['idFranquicia' => $idFranquicia,'franquiciaAdmin' => $franquiciaAdmin]);
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function promocioncrear($idFranquicia, Request $request){
        if(Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6))
        {
            $rules = [
                'titulo'=>'required|string|max:255',

                'inicio'=>'required|string|max:255',
                'fin'=>'required|string|max:255',
                'tipopromocion2'=>'required'
                // 'tipopromo'=>'required|integer',
            ];
            $ini = request('inicio');
            $fin = request('fin');
            if ($ini > $fin){
                return back()->withErrors(['inicio' => 'La fecha inicial debe ser menor que la final.'])->withInput($request->all());
            }
            if (request('cantidad') > 100){
                return back()->withErrors(['cantidad' => 'No puede ser mayor a 100 la cantidad.'])->withInput($request->all());
            }
            if (request('tipopromocion') == 1 && request('cantidad') != null){
                return back()->withErrors(['cantidad' => 'Debe ir vacio si se selecciono por la opción por precio.'])->withInput($request->all());
            }
            if (request('preciouno') == null && request('cantidad') == null){
                return back()->withErrors(['cantidad' => 'Elegir al menos una opcion de descuento.'])->withInput($request->all());
            }
            if (request('preciouno') != null && request('cantidad') != null){
                return back()->withErrors(['cantidad' => 'Solo puedes elegir una opción de descuento.'])->withInput($request->all());
            }
            if (request('preciouno') != null && request('tipopromocion') != 1){
                return back()->withErrors(['preciouno' => 'Solo permitido con la opcion seleccionada de por precio.'])->withInput($request->all());
            }
            if (request('cantidad') < 0){
                return back()->withErrors(['cantidad' => 'No puede ser menor a 0.'])->withInput($request->all());
            }
            if (request('armazones') < 1){
                return back()->withErrors(['armazones' => 'No puede ser menor a 1.'])->withInput($request->all());
            }
            if (request('contarventa') != 0 && request('contarventa') != 1 && request('contarventa') != 2 && request('contarventa') != 3){
                return back()->withErrors(['contarventa' => 'Por favor, selecciona una opcion.'])->withInput($request->all());
            }
            if (request('tipopromocion2') != 0 && request('tipopromocion2') != 1 && request('tipopromocion2') != 2){
                return back()->withErrors(['tipopromocion2' => 'Por favor, selecciona una opcion.'])->withInput($request->all());
            }

            //Recorrer checkBox de sucursales a las que aplicarar promocion - Solo rol director
            $idsSucursalesPromocion = array();
            if(Auth::user()->rol_id == 7){
                //Rol de director - Obtener sucursales
                $franquicias = DB::select("SELECT f.id AS id FROM franquicias f where id != '00000' ORDER BY f.ciudad ASC");
                $contador = 0;

                //Recorrer checkbox para obtener valor de sucursale seleccionadas
                foreach ($franquicias as $franquicia) {
                    $franquiciaPromocion= request("$franquicia->id");
                    if (!is_null($franquiciaPromocion) && $franquiciaPromocion > 0) {
                        array_push($idsSucursalesPromocion, $franquicia->id);
                        $contador++;
                    }
                }
                if ($contador == 0) {
                    //No se selecciono ninguna sucursal
                    return back()->with('alerta', 'Debes seleccionar al menos una sucursal')->withInput($request->all());
                }
            }else{
                //Rol de Administracion o Principal - Ingresar solo su franquicia al arreglo de ids
                array_push($idsSucursalesPromocion, $idFranquicia);
            }

            request()->validate($rules);
            try{
                $estado = 0;
                if(!is_null(request('administrador'))){
                    if(request('administrador') == 1){
                        $estado = 1;
                    }else{
                        $estado = 0;
                    }
                }

                $fijo = 0;
                if(!is_null(request('tipopromocion'))){
                    if(request('tipopromocion') == 1){
                        $fijo = 1;
                    }else{
                        $fijo = 0;
                    }
                }

                // Recorrer arreglo de ids para crear promocion en sucursales selecconadas o sucursal correspondiente a usuario rol admin o principal
                foreach ($idsSucursalesPromocion as $idSucursal){
                    DB::table('promocion')->insert([
                        'id_franquicia' => $idSucursal,'titulo' => request('titulo'),'armazones' => request('armazones'),'id_tipopromocionusuario' => $estado,
                        'contado' => $estado, 'preciouno' => request('preciouno'), 'tipopromocion' => $fijo,
                        'precioP' => request('cantidad'),'inicio'=> request('inicio'),'fin'=> request('fin'), 'status' => 1,
                        'contarventa' => request('contarventa'), 'tipo' => request('tipopromocion2'), 'created_at' => Carbon::now()
                    ]);

                    //Registrar movimiento de nueva promocion creada
                    DB::table('historialsucursal')->insert([
                        'id_usuarioC' => Auth::user()->id, 'id_franquicia' => $idSucursal,
                        'tipomensaje' => '0', 'created_at' => Carbon::now(), 'cambios' => " Creo la promocion: '" . request('titulo') ."'", 'seccion' => '2'
                    ]);
                }

                return redirect()->route('listasfranquicia',$idFranquicia)->with('bien','La promoción se creo correctamente.');
            }catch(\Exception $e){
                \Log::info("Error: ".$e->getMessage());
                return back()->with('error','Tuvimos un problema, por favor contacta al dministrador de la pagina.');
            }

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

	public function promocionactualizar($idFranquicia,$idPromocion){
        if(Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6))
        {
            $promocion = DB::select("SELECT * FROM promocion WHERE id_franquicia = '$idFranquicia' AND id = '$idPromocion'");
            $franquiciaAdmin = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");
            return view('administracion.franquicia.administracion.actualizarpromocion',['idFranquicia' => $idFranquicia,'promocion'=>$promocion, 'franquiciaAdmin'=>$franquiciaAdmin]);
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

	public function promocioneditar($idFranquicia,$idPromocion, Request $request){
        if(Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6))
        {
			$rules = [
                'titulo'=>'required|string|max:255',
                'inicio'=>'required|string|max:255',
                'fin'=>'required|string|max:255',
                'tipopromocion2'=>'required|string|max:255'
            ];
            $now = Carbon::now();
            $ini = request('inicio');
            $fin = request('fin');
            if ($ini > $fin){
                return back()->withErrors(['inicio' => 'La fecha inicial debe ser menor que la final.'])->withInput($request->all());
            }
            if (request('cantidad') > 100){
                return back()->withErrors(['cantidad' => 'Debe ser menor a 100.'])->withInput($request->all());
            }
            if (request('tipopromocion') == 1 && request('cantidad') != null){
                return back()->withErrors(['cantidad' => 'Debe ir vacio si se selecciono por la opción por precio.'])->withInput($request->all());
            }
            if (request('preciouno') == null && request('cantidad') == null){
                return back()->withErrors(['cantidad' => 'Elegir al menos una opcion de descuento.'])->withInput($request->all());
            }
            if (request('preciouno') != null && request('cantidad') != null){
                return back()->withErrors(['cantidad' => 'Solo puedes elegir una opción de descuento.'])->withInput($request->all());
            }
            if (request('preciouno') != null && request('tipopromocion') != 1){
                return back()->withErrors(['preciouno' => 'Solo permitido con la opcion seleccionada de por precio.'])->withInput($request->all());
            }
            if($now > Carbon::parse($fin)){
                return back()->withErrors(['fin' => 'No puede ser menor a la fecha actual.'])->withInput($request->all());
            }

            if (request('cantidad') < 0){
                return back()->withErrors(['cantidad' => 'No puede ser menor a 0.'])->withInput($request->all());
            }
            if (request('armazones2') < 1){
                return back()->withErrors(['armazones2' => 'No puede ser menor a 1.'])->withInput($request->all());
            }
            if (request('contarventa') != 0 && request('contarventa') != 1 && request('contarventa') != 2 && request('contarventa') != 3){
                return back()->withErrors(['contarventa' => 'Por favor, selecciona una opcion.'])->withInput($request->all());
            }
            if (request('tipopromocion2') != 0 && request('tipopromocion2') != 1 && request('tipopromocion2') != 2){
                return back()->withErrors(['tipopromocion2' => 'Por favor, selecciona una opcion.'])->withInput($request->all());
            }

            request()->validate($rules);
            try{
                $estado = 0;
                if(!is_null(request('administrador'))){
                    if(request('administrador') == 1){
                        $estado = 1;
                    }else{
                        $estado = 0;
                    }
                }
                $fijo = 0;
                if(!is_null(request('tipopromocion'))){
                    if(request('tipopromocion') == 1){
                        $fijo = 1;
                    }else{
                        $fijo = 0;
                    }
                }
                $forzado = 0;
                //Datos actuales promocion
                $promocion = DB::select("SELECT * FROM promocion p WHERE p.id = '$idPromocion' AND p.id_franquicia = '$idFranquicia'");
                $id_UsuarioC = Auth::user()->id;

                //Verificar que campos se actualizaron
                if($promocion[0]->titulo != request('titulo')){
                    //Actualizo titulo de promocion
                    $cambio = "Actualizo titulo promocion: '" . $promocion[0]->titulo ."' a '" . request('titulo') ."'";
                    self::insertarMovimientoHistorialSucursal($idFranquicia, $idPromocion, $id_UsuarioC,$cambio);
                }if($promocion[0]->armazones != request('armazones2')){
                    //Actualizo cantidad de contratos para promocion
                    $cambio = "Actualizo cantidad de contratos para promocion " . $promocion[0]->titulo ." de '" .$promocion[0]->armazones."' a '" . request('armazones2') ."'";
                    self::insertarMovimientoHistorialSucursal($idFranquicia, $idPromocion, $id_UsuarioC,$cambio);
                }if($promocion[0]->precioP != request('cantidad')){
                    //Actualizo descuento por procentaje
                    $cambio = "Actualizo porcentaje de descuento de promocion " . $promocion[0]->titulo . " a '" . request('cantidad') ."'";
                    self::insertarMovimientoHistorialSucursal($idFranquicia, $idPromocion, $id_UsuarioC,$cambio);
                }if($promocion[0]->preciouno != request('preciouno')){
                    //Actualizo descuento con precio
                    $cambio = "Actualizo descuento por precio para promocion " . $promocion[0]->titulo ." de '" .$promocion[0]->preciouno."' a '" . request('preciouno') ."'";
                    self::insertarMovimientoHistorialSucursal($idFranquicia, $idPromocion, $id_UsuarioC,$cambio);
                }if($promocion[0]->inicio != request('inicio') || $promocion[0]->fin != request('fin')){
                    //Actualizo periodo de fecha
                    $cambio = "Actualizo periodo de fechas para promocion " . $promocion[0]->titulo ." de " .$promocion[0]->inicio ." / ".$promocion[0]->fin
                        ." a " . request('inicio') ." / " . request('fin');
                    self::insertarMovimientoHistorialSucursal($idFranquicia, $idPromocion,$id_UsuarioC,$cambio);
                }if($promocion[0]->tipopromocion != $fijo){
                    //Actualizo tipo promocion
                    $cambio = "Actualizo tipo promocion para: '" . $promocion[0]->titulo . "'";
                    self::insertarMovimientoHistorialSucursal($idFranquicia, $idPromocion,$id_UsuarioC,$cambio);
                }

                DB::table('promocion')->where([['id','=',$idPromocion],['id_franquicia','=',$idFranquicia]])->update([
                    'titulo'=>request('titulo'),'precioP'=>request('cantidad'),'preciouno'=>request('preciouno'),'armazones' => request('armazones2'),
                    'inicio'=>request('inicio'),'id_tipopromocionusuario' => $estado,'contado' => $estado,'tipopromocion' => $fijo,'fin' =>request('fin'),'status' => 1,
                    'contarventa' => request('contarventa'), 'tipo' => request('tipopromocion2'), 'updated_at' => Carbon::now(),'forzado' => $forzado
                ]);

                return redirect()->route('listasfranquicia',$idFranquicia)->with('bien','La promoción se actualizo correctamente.');
            }catch(\Exception $e){
                \Log::info("Error: ".$e->getMessage());
                return back()->with('error','Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

	public function estadoPromocionEditar($idFranquicia,$idPromocion){
        if(Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6))
        {
            $promociones = DB::select("SELECT * FROM promocion WHERE id_franquicia = '$idFranquicia' and id = '$idPromocion'");
            $estado = $promociones[0]->status;
            $id_UsuarioC = Auth::user()->id;

            if($estado == 1){
                $estado = 2;
                $cambio = "Desactivo promocion: '" . $promociones[0]->titulo . "'";
            }else{
                $cambio = "Activo promocion: '" . $promociones[0]->titulo . "'";
            }


            try{
                $forzado = 1;
                DB::table('promocion')->where([['id','=',$idPromocion],['id_franquicia','=',$idFranquicia]])->update([
                    'status'=> $estado,'updated_at' => Carbon::now(),'forzado' => $forzado
                ]);

                //Registrar movimiento estatus promocion
                self::insertarMovimientoHistorialSucursal($idFranquicia, $idPromocion,$id_UsuarioC,$cambio);

                return redirect()->route('listasfranquicia',$idFranquicia)->with('bien','La promoción se actualizo correctamente.');
            }catch(\Exception $e){
                \Log::info("Error: ".$e->getMessage());
                return back()->with('error','Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function insertarMovimientoHistorialSucursal($idFranquicia, $idProducto, $idUsuarioC, $cambio){
        DB::table('historialsucursal')->insert([
            'id_usuarioC' => $idUsuarioC, 'id_franquicia' => $idFranquicia, 'id_producto' => $idProducto,
            'tipomensaje' => '0', 'created_at' => Carbon::now(), 'cambios' => $cambio, 'seccion' => '2'
        ]);
    }


}
