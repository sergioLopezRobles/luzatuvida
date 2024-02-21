<?php

namespace App\Http\Controllers\Dominios\Administracion;


use App\Clases\contratosGlobal;
use Carbon\Carbon;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Image;
use mysql_xdevapi\Exception;


class adminfranquicia extends Controller
{
    public function listas($idFranquicia){
        $contratosGlobal = new contratosGlobal;
        if(Auth::check() && ($contratosGlobal::validarPermisoSeccion(Auth::user()->id, 2, 1)) && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {

            $productosSucursal = DB::select("SELECT p.id, p.estado, p.nombre, tipoproducto.tipo, p.foto, p.totalpiezas, p.piezas, p.color, p.precio,p.id_tipoproducto, p.activo, p.preciop
                FROM producto as p
                INNER JOIN tipoproducto
                ON tipoproducto.id = p.id_tipoproducto
                WHERE p.id_tipoproducto != 1
                AND p.estado = 1
                AND p.id_franquicia = '$idFranquicia'
                order by p.created_at desc
                ");

            $productosGeneral = DB::select("SELECT p.id, p.estado, p.nombre, tipoproducto.tipo, p.foto, p.totalpiezas, p.piezas, p.color, p.precio,p.id_tipoproducto, p.activo, p.preciop
                FROM producto as p
                INNER JOIN tipoproducto
                ON tipoproducto.id = p.id_tipoproducto
                WHERE p.id_tipoproducto = 1
                AND p.estado = 1
                order by p.created_at desc
                ");

            $tratamientos = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia'");
            $paquetes = DB::select("SELECT * FROM paquetes WHERE id_franquicia = '$idFranquicia'");
            $promociones = DB::select("SELECT * FROM promocion WHERE id_franquicia = '$idFranquicia'");
            $franquiciaAdmin = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");
            $mensajes = DB::select("SELECT * FROM mensajes WHERE id_franquicia = '$idFranquicia'");
            $abonosMinimosSucursal = DB::select("SELECT amf.pago, amf.abonominimo, STR_TO_DATE(amf.updated_at,'%Y-%m-%d') as updated_at FROM abonominimofranquicia amf
                                                        WHERE amf.id_franquicia = '$idFranquicia'");
            $comisionesventas = DB::select("SELECT * FROM comisionesventas
                                                        WHERE id_franquicia = '$idFranquicia' ORDER BY usuario, comision ASC");
            $zonas = DB::select("SELECT * FROM zonas z WHERE z.id_franquicia = '$idFranquicia' ORDER BY z.zona ASC");

            $accionbanderaasistenciafranquicia = DB::select("SELECT estatus FROM accionesbanderasfranquicia WHERE id_franquicia = '$idFranquicia' AND tipo = '0' ORDER BY created_at DESC LIMIT 1");

            $accionbanderaterminarpolizafranquicia = DB::select("SELECT estatus FROM accionesbanderasfranquicia WHERE id_franquicia = '$idFranquicia' AND tipo = '1' ORDER BY created_at DESC LIMIT 1");
            $horabanderaterminarpolizafranquicia = $accionbanderaterminarpolizafranquicia == null ? Carbon::parse("13:00:00")->format("H:i:s") : Carbon::parse($accionbanderaterminarpolizafranquicia[0]->estatus . ":00:00")->format("H:i:s");

            return view('administracion.franquicia.administracion.listas',['productosGeneral' => $productosGeneral, 'productosSucursal' => $productosSucursal,
                                'franquiciaAdmin' => $franquiciaAdmin,'idFranquicia' => $idFranquicia,'tratamientos'=>$tratamientos,'paquetes'=>$paquetes,
                                'promociones'=>$promociones,'mensajes'=> $mensajes, 'abonosMinimos' => $abonosMinimosSucursal, 'comisionesventas' => $comisionesventas,
                                'zonas' => $zonas, 'accionbanderaasistenciafranquicia' => $accionbanderaasistenciafranquicia, 'horabanderaterminarpolizafranquicia' => $horabanderaterminarpolizafranquicia]);
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function filtrarproducto($idFranquicia){
        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
            $filtro = request('filtro');

            $productosSucursal = DB::select("SELECT p.id, p.estado, p.nombre, tipoproducto.tipo, p.foto, p.totalpiezas, p.piezas, p.color, p.precio,p.id_tipoproducto, p.activo, p.preciop
                FROM producto as p
                INNER JOIN tipoproducto
                ON tipoproducto.id = p.id_tipoproducto
                WHERE p.id_tipoproducto != 1
                AND p.estado = 1
                AND p.id_franquicia = '$idFranquicia'
                AND (p.estado LIKE '%$filtro%'
                    OR p.nombre LIKE '%$filtro%' OR tipoproducto.tipo LIKE '%$filtro%' OR p.piezas LIKE '$filtro' OR p.color LIKE '%$filtro%' OR p.preciop LIKE '%$filtro%')
                order by p.created_at desc
                ");

            $productosGeneral = DB::select("SELECT p.id, p.estado, p.nombre, tipoproducto.tipo, p.foto, p.totalpiezas, p.piezas, p.color, p.precio,p.id_tipoproducto, p.activo, p.preciop
                FROM producto as p
                INNER JOIN tipoproducto
                ON tipoproducto.id = p.id_tipoproducto
                WHERE p.id_tipoproducto = 1
                AND p.estado = 1
                AND (p.estado LIKE '%$filtro%'
                    OR p.nombre LIKE '%$filtro%' OR tipoproducto.tipo LIKE '%$filtro%' OR p.piezas LIKE '$filtro' OR p.color LIKE '%$filtro%' OR p.preciop LIKE '%$filtro%')
                order by p.created_at desc
                ");

            $tratamientos = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia'");
            $paquetes = DB::select("SELECT * FROM paquetes WHERE id_franquicia = '$idFranquicia'");
            $promociones = DB::select("SELECT * FROM promocion WHERE id_franquicia = '$idFranquicia'");
            $franquiciaAdmin = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");
            $mensajes = DB::select("SELECT * FROM mensajes WHERE id_franquicia = '$idFranquicia'");

            $abonosMinimosSucursal = DB::select("SELECT amf.pago, amf.abonominimo, STR_TO_DATE(amf.updated_at,'%Y-%m-%d') as updated_at FROM abonominimofranquicia amf
                                                        WHERE amf.id_franquicia = '$idFranquicia'");
            $comisionesventas = DB::select("SELECT * FROM comisionesventas
                                                        WHERE id_franquicia = '$idFranquicia' ORDER BY usuario, comision ASC");
            $zonas = DB::select("SELECT * FROM zonas z WHERE z.id_franquicia = '$idFranquicia' ORDER BY z.zona ASC");

            $franquiciaAdmin = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");
            return view('administracion.franquicia.administracion.listas',['productosGeneral' => $productosGeneral, 'productosSucursal' => $productosSucursal, 'franquiciaAdmin' => $franquiciaAdmin,
                'idFranquicia' => $idFranquicia,'tratamientos'=>$tratamientos,'paquetes'=>$paquetes,'promociones'=>$promociones,'mensajes'=> $mensajes,
                'abonosMinimos' => $abonosMinimosSucursal, 'comisionesventas' => $comisionesventas, 'zonas' => $zonas]);
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    private  function getProductosId() {
        $unico = "";
        $esUnico = false;
        while(!$esUnico){
            $temporalId = $this->generadorRandom();
            $existente = DB::select("select id from producto where id = '$temporalId'");
            if (sizeof ($existente) == 0){
                $unico = $temporalId;
                $esUnico = true;
            }
        }
           return $unico;
    }

    private function generadorRandom($length = 5){
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($caracteres);
        $randomId = '';
        for ($i = 0; $i < $length; $i++) {
            $randomId .= $caracteres[rand(0, $charactersLength - 1)];
        }
        return $randomId;
    }

    public function productonuevo($idFranquicia){
        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
            $franquiciaAdmin = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");
            $tipoproducto  = DB::select("SELECT * FROM tipoproducto");
            return view('administracion.franquicia.administracion.nuevoproducto',['franquiciaAdmin' => $franquiciaAdmin,'idFranquicia' => $idFranquicia, 'tipoproducto' => $tipoproducto]);

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function productocrear($idFranquicia, Request $request){

        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {

            $existeTipoProducto = DB::select("SELECT id FROM tipoproducto WHERE id = '" . request('tipoproducto') . "'");

            if ($existeTipoProducto != null) {
                //Tipo de producto existe

                //VALIDACION PARA POLIZAGASTOS
                $polizagastos = 0;
                $polizagastoscobranza = 0;
                $polizagastosadministracion = 0;
                $tipoproductopolizagotas = false;
                if (request('tipoproducto') == 2 || request('tipoproducto') == 3) {
                    //Tipo producto es poliza o gotas
                    $tipoproductopolizagotas = true;
                    $polizagastos = request('polizagastos');
                    $polizagastoscobranza = request('polizagastoscobranza');
                    $polizagastosadministracion = request('polizagastosadministracion');
                    if ($polizagastos < 0 || $polizagastoscobranza < 0 || $polizagastosadministracion < 0) {
                        //Poliza gastos es menor a cero
                        return back()->with('alerta', 'No puede ser menor a 0 el gasto en poliza.')->withInput($request->all());
                    }
                }

                if (request('tipoproducto') == 1) {
                    $validacion = Validator::make($request->all(), [
                        'producto' => 'required|string|max:255',
                        'piezas' => 'required|integer',
                        'foto' => 'nullable|image|mimes:jpg',
                        'color' => 'required|string|max:255'
                    ]);

                    if ($validacion->fails()) {
                        return back()->withErrors($validacion);
                    }
                    $precio = null;
                    if (strlen(request('precio')) > 0) {
                        //Agregaron precio al armazon
                        $precio = request('precio');
                    }
                    $in = null;
                    $fi = null;
                    $preciop = null;
                } else {

                    if ($tipoproductopolizagotas) {
                        //Tipo producto es poliza o gotas
                        $validacion = Validator::make($request->all(), [
                            'producto' => 'required|string|max:255',
                            'piezas' => 'required|integer',
                            'foto' => 'nullable|image|mimes:jpg',
                            'color' => 'required|string|max:255',
                            'precio' => 'required|integer',
                            'polizagastos' => 'required|integer',
                            'polizagastoscobranza' => 'required|integer',
                            'polizagastosadministracion' => 'required|integer'
                        ]);
                    } else {
                        //Tipo producto diferente a poliza o gotas
                        $validacion = Validator::make($request->all(), [
                            'producto' => 'required|string|max:255',
                            'piezas' => 'required|integer',
                            'foto' => 'nullable|image|mimes:jpg',
                            'color' => 'required|string|max:255',
                            'precio' => 'required|integer'
                        ]);
                    }
                    if ($validacion->fails()) {
                        return back()->withErrors($validacion);
                    }
                    $precio = request('precio');
                    $in = request('iniciop');
                    $fi = request('finp');
                    $preciop = request('preciop');
                }

                //Validar producto - Solo armazon puede ser premium
                $premium = request('premium');
                $premium = ($premium == "1")? $premium: "0";
                if(request('tipoproducto') != 1 && $premium == 1){
                    //Tipo producto es diferente de armazon y checkbox premion fue seleccionado
                    return back()->with("alerta","Caracteristica 'Premium' solo aplica para productos de tipo 'Armazón'");
                }

                if (request('finp') != null || request('iniciop') != null || request('preciop') != null || request('activo') != null) {
                    //Se quiere agregar promocion al producto

                    $validacion = Validator::make($request->all(), [
                        'iniciop' => 'required|date',
                        'finp' => 'required|date',
                        'preciop' => 'required|integer'
                    ]);
                    if ($validacion->fails()) {
                        return back()->withErrors([
                            'iniciop' => "Campos requeridos",
                            'finp' => "Campos requeridos",
                            'preciop' => "Campos requeridos"
                        ]);
                    }

                    $fecha1 = Carbon::parse(request('iniciop'));
                    $fecha2 = Carbon::parse(request('finp'));

                    if ($fecha1->gt($fecha2)) {
                        return back()->withErrors([
                            'iniciop' => "No puede ser mayor a la fecha final",
                            'finp' => "No puede ser menor a la fecha inicial",

                        ])->withInput($request->all());
                    }

                    if ($tipoproductopolizagotas && (request('polizagastos') > request('preciop')
                            || request('polizagastoscobranza') > request('preciop') || request('polizagastosadministracion') > request('preciop'))) {
                        //Tipo producto es poliza o gotas
                        return back()->with('alerta', 'No puede ser mayor el gasto en poliza que el precio de la promoción.')->withInput($request->all());
                    }

                } else {
                    //No se agrego promocion al producto
                    if ($tipoproductopolizagotas && (request('polizagastos') > request('precio')
                            || request('polizagastoscobranza') > request('precio') || request('polizagastosadministracion') > request('precio'))) {
                        //Tipo producto es poliza o gotas
                        return back()->with('alerta', 'No puede ser mayor el gasto en poliza que el precio del producto.')->withInput($request->all());
                    }
                }

                if (request('preciop') > request('precio')) {
                    return back()->withErrors([
                        'preciop' => "No puede ser mayor el precio de la promoción que el principal",

                    ])->withInput($request->all());
                }
                try {
                    $contratos = DB::select("SHOW TABLE STATUS LIKE 'contratos'");
                    $randomId = $this->getProductosId();

                    $estado = 0;
                    if (!is_null(request('estado'))) {
                        if (request('estado') == 1) {
                            $estado = 1;
                        } else {
                            $estado = 0;
                        }
                    }

                    $activo = 0;
                    if (!is_null(request('activo'))) {
                        if (request('activo') == 1) {
                            $activo = 1;
                        } else {
                            $activo = 0;
                        }
                    }

                    $productos = DB::select("SHOW TABLE STATUS LIKE 'producto'");
                    $siguienteId = $productos[0]->Auto_increment;

                $foto="";
                //Validar tamaño de foto
                $contratosGlobal = new contratosGlobal;
                if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('foto'))){
                    return back()->with('alerta',"Verifica el archivo 'Foto', el tamaño maximo permitido es 1MB.");
                }

                if(request()->hasFile('foto')){
                    $fotoBruta='Foto-Producto-'.$siguienteId.'-'.time(). '.' . request()->file('foto')->getClientOriginalExtension();
                    $foto=request()->file('foto')->storeAs('uploads/imagenes/productos/fotos', $fotoBruta,'disco');
                    $alto = Image::make(config('filesystems.disks.disco.root').'/uploads/imagenes/productos/fotos/'.$fotoBruta)->height();
                    $ancho = Image::make(config('filesystems.disks.disco.root').'/uploads/imagenes/productos/fotos/'.$fotoBruta)->width();
                    if($alto > $ancho){
                        $imagenfoto=Image::make(config('filesystems.disks.disco.root').'/uploads/imagenes/productos/fotos/'.$fotoBruta)->resize(600,800);
                    }else{
                        $imagenfoto=Image::make(config('filesystems.disks.disco.root').'/uploads/imagenes/productos/fotos/'.$fotoBruta)->resize(800,600);
                    }
                    $imagenfoto->save();
                }

                    DB::table('producto')->insert([
                        'id' => $randomId, 'id_franquicia' => $idFranquicia, 'nombre' => request('producto'), 'id_tipoproducto' => request('tipoproducto'),
                        'piezas' => request('piezas'), 'precio' => $precio, 'foto' => $foto, 'color' => request('color'), 'estado' => $estado,
                        'created_at' => Carbon::now(), 'iniciop' => $in, 'finp' => $fi, 'preciop' => $preciop, 'activo' => $activo, 'totalpiezas' => request('piezas'),
                        'polizagastos' => $polizagastos, 'polizagastoscobranza' => $polizagastoscobranza, 'polizagastosadministracion' => $polizagastosadministracion,
                        'premium' => $premium
                    ]);

                    return redirect()->route('listasfranquicia', $idFranquicia)->with('bien', 'El producto se creo correctamente.');

                } catch (\Exception $e) {
                    \Log::info("Error: " . $e->getMessage());
                    return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
                }

            }
            //Tipo de producto no existe
            return back()->with('alerta', 'El tipo de producto seleccionado no existe.');

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function productoactualizar($idFranquicia,$idProducto){
        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
            $producto = DB::select("SELECT * FROM producto WHERE id = '$idProducto'");
            if ($producto != null) {
                //Existe el producto
                if ((Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) && $producto[0]->id_tipoproducto == 1) {
                    //Rol administrador o principal y tipo producto es igual de ARMAZON
                    return back()->with('alerta', "No puedes actualizar el producto de tipo armazón.");
                }
                $tipoproducto = DB::select("SELECT * FROM tipoproducto");
                $franquiciaAdmin = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");
                return view('administracion.franquicia.administracion.actualizarproducto', ['idFranquicia' => $idFranquicia, 'producto' => $producto, 'franquiciaAdmin' => $franquiciaAdmin,
                    'tipoproducto' => $tipoproducto]);
            }
            //No existe el producto
            return back()->with('alerta', "No existe el producto.");
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function productoeditar($idFranquicia,$idProducto, Request $request){
        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {

            //Recuperar los datos actuales del producto
            $datosActualesProducto = DB::select("SELECT * FROM producto WHERE id = '$idProducto'");

            if ($datosActualesProducto != null) {
                //Existe producto

                if ((Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) && $datosActualesProducto[0]->id_tipoproducto != 1) {
                    //Rol administrador o principal y tipo producto es diferente de ARMAZON
                    if ($idFranquicia != $datosActualesProducto[0]->id_franquicia) {
                        //Franquicia actual es diferente a la franquicia donde se dio de alta el producto
                        return back()->with('alerta', "No se puede actualizar el producto ya que no pertenece a tu sucursal asignada.");
                    }
                }

                //VALIDACION PARA POLIZAGASTOS
                $polizagastos = 0;
                $polizagastosadministracion = 0;
                $polizagastoscobranza = 0;
                $tipoproductopolizagotas = false;
                if (request('tipoproducto') == 2 || request('tipoproducto') == 3) {
                    //Tipo producto es poliza o gotas
                    $tipoproductopolizagotas = true;
                    $polizagastos = request('polizagastos');
                    $polizagastosadministracion = request('polizagastosadministracion');
                    $polizagastoscobranza = request('polizagastoscobranza');
                    if ($polizagastos < 0 || $polizagastosadministracion < 0 || $polizagastoscobranza < 0) {
                        //Poliza gastos es menor a cero
                        return back()->with('alerta', 'No puede ser menor a 0 el gasto en poliza.')->withInput($request->all());
                    }
                }

                if (request('tipoproducto') == 1) {
                    $validacion = Validator::make($request->all(), [
                        'nombre' => 'required|string|max:255',
                        'piezas' => 'required|integer',
                        'foto' => 'nullable|image|mimes:jpg',
                        'color' => 'required|string|max:255'
                    ]);

                    if ($validacion->fails()) {
                        return back()->withErrors($validacion);
                    }

                    $precio = null;
                    if (strlen(request('precio')) > 0) {
                        //Agregaron precio al armazon
                        $precio = request('precio');
                    }

                    $in = null;
                    $fi = null;
                    $preciop = null;

                } else {
                    if ($tipoproductopolizagotas) {
                        //Tipo producto es poliza o gotas
                        $validacion = Validator::make($request->all(), [
                            'nombre' => 'required|string|max:255',
                            'piezas' => 'required|integer',
                            'foto' => 'nullable|image|mimes:jpg',
                            'color' => 'required|string|max:255',
                            'precio' => 'required|integer',
                            'polizagastos' => 'required|integer',
                            'polizagastosadministracion' => 'required|integer',
                            'polizagastoscobranza' => 'required|integer'
                        ]);
                    } else {
                        //Tipo producto diferente a poliza o gotas
                        $validacion = Validator::make($request->all(), [
                            'nombre' => 'required|string|max:255',
                            'piezas' => 'required|integer',
                            'foto' => 'nullable|image|mimes:jpg',
                            'color' => 'required|string|max:255',
                            'precio' => 'required|integer'
                        ]);
                    }
                    if ($validacion->fails()) {
                        return back()->withErrors($validacion);
                    }
                    $precio = request('precio');
                    $in = request('iniciop');
                    $fi = request('finp');
                    $preciop = request('preciop');
                }
                if (request('finp') != null || request('iniciop') != null || request('preciop') != null || request('activo') != null) {
                    $validacion = Validator::make($request->all(), [
                        'iniciop' => 'required|date',
                        'finp' => 'required|date',
                        'preciop' => 'required|integer'

                    ]);
                    if ($validacion->fails()) {
                        return back()->withErrors([
                            'iniciop' => "Campos requeridos",
                            'finp' => "Campos requeridos",
                            'preciop' => "Campos requeridos"
                        ])->withInput($request->all());
                    }

                    $fecha1 = Carbon::parse(request('iniciop'));
                    $fecha2 = Carbon::parse(request('finp'));
                    if ($fecha1->gt($fecha2)) {
                        return back()->withErrors([
                            'iniciop' => "No puede ser mayor a la fecha final"
                        ])->withInput($request->all());
                    }

                    if ($tipoproductopolizagotas && (request('polizagastos') > request('preciop')
                            || request('polizagastoscobranza') > request('preciop') || request('polizagastosadministracion') > request('preciop'))) {
                        //Tipo producto es poliza o gotas
                        return back()->with('alerta', 'No puede ser mayor el gasto en poliza que el precio de la promoción.')->withInput($request->all());
                    }

                } else {
                    //No se agrego promocion al producto
                    if ($tipoproductopolizagotas && (request('polizagastos') > request('precio')
                            || request('polizagastoscobranza') > request('precio') || request('polizagastosadministracion') > request('precio'))) {
                        //Tipo producto es poliza o gotas
                        return back()->with('alerta', 'No puede ser mayor el gasto en poliza que el precio del producto.')->withInput($request->all());
                    }
                }

                if (request('preciop') > request('precio')) {
                    return back()->withErrors([
                        'preciop' => "El precio no puede ser mayor al precio normal del producto."
                    ])->withInput($request->all());
                }

                //Validar tamaño de foto
                $contratosGlobal = new contratosGlobal;
                if (!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('foto'))) {
                    return back()->with('alerta', "Verifica el archivo 'Foto', el tamaño maximo permitido es 1MB.");
                }

                try {

                    $estado = 0;
                    if (!is_null(request('estado'))) {
                        if (strlen(request('estado')) == 0) {
                            $estado = 0;
                        } else {
                            $estado = 1;
                        }
                    }
                    $activo = 0;
                    if (!is_null(request('activo'))) {
                        if (strlen(request('activo')) == 0) {
                            $activo = 0;
                        } else {
                            $activo = 1;
                        }
                    }

                    $foto = "";
                    $fotoBool = false;
                    $banderaActualizoFoto = false;
                    if (strlen($datosActualesProducto[0]->foto) > 0) {
                        if (request()->hasFile('foto')) {
                            Storage::disk('disco')->delete($datosActualesProducto[0]->foto);
                            $fotoBruta = 'Foto-Producto-' . $datosActualesProducto[0]->id . '-' . time() . '.' . request()->file('foto')->getClientOriginalExtension();
                            $foto = request()->file('foto')->storeAs('uploads/imagenes/productos/fotos', $fotoBruta, 'disco');

                        } else {
                            $foto = $datosActualesProducto[0]->foto;
                        }
                    } else {
                        if (request()->hasFile('foto')) {
                            $fotoBruta = 'Foto-Producto-' . $datosActualesProducto[0]->id . '-' . time() . '.' . request()->file('foto')->getClientOriginalExtension();
                            $foto = request()->file('foto')->storeAs('uploads/imagenes/productos/fotos', $fotoBruta, 'disco');
                            $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/productos/fotos/' . $fotoBruta)->height();
                            $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/productos/fotos/' . $fotoBruta)->width();
                            if ($alto > $ancho) {
                                $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/productos/fotos/' . $fotoBruta)->resize(600, 800);
                            } else {
                                $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/productos/fotos/' . $fotoBruta)->resize(800, 600);
                            }
                            $imagenfoto->save();
                            $banderaActualizoFoto = true;

                        } else {
                            $foto = null;
                        }
                    }

                    $totalpiezasactuales = $datosActualesProducto[0]->totalpiezas;
                    $piezasrestantesactuales = $datosActualesProducto[0]->piezas;
                    $totalpiezasactualizar = request('piezas');

                    $piezasutilizadas = $totalpiezasactuales - $piezasrestantesactuales;
                    $piezasrestantesactualizar = $totalpiezasactualizar - $piezasutilizadas;

                    if ($piezasrestantesactualizar < 0) {
                        //Piezas restantes del producto es menor a 0
                        return back()->with('alerta', 'No se puede actualizar el total de piezas del producto, ya que el numero total de piezas necesita ser mayor que las piezas vendidas.')->withInput($request->all());
                    }

                    //Datos recibidos desde formualrio en la vista
                    $nombre = request('nombre');
                    $color = request('color');
                    $tipoProducto = request('tipoproducto');
                    $premium = request('premium');
                    $premium = ($premium == 1)? $premium: "0";

                    //Validar cuales datos son actualizados - Si son diferentes a los actuales registrar movimiento
                    $id_UsuarioC = Auth::user()->id;

                    //Validar que no se cambie tipo de producto
                    if($datosActualesProducto[0]->id_tipoproducto != $tipoProducto) {
                        return back()->with('alerta', 'No tienes permitido actualizar el tipo de producto.');
                    }

                    //Se modifico el nombre?
                    if ($datosActualesProducto[0]->nombre != $nombre) {
                        //Se modifico el nombre del producto
                        $cambio = "Actualizo el nombre del producto: " . $datosActualesProducto[0]->id . " - " . $datosActualesProducto[0]->nombre .
                            " color: " . $datosActualesProducto[0]->color . " a '" . $nombre . "'";
                        //Insertamos el movimiento
                        self::insertarHistorialSucursal($idFranquicia, $idProducto, $id_UsuarioC, $cambio);

                    }

                    //Se modifico el numero de piezas?
                    if ($totalpiezasactuales != $totalpiezasactualizar) {
                        //Se modifico el numero de piezas
                        $cambio = "Actualizo el numero de piezas del producto: " . $datosActualesProducto[0]->id . " - " . $datosActualesProducto[0]->nombre .
                            " color: " . $datosActualesProducto[0]->color . " de: '" . $totalpiezasactuales . "' a '" . $totalpiezasactualizar . "'";
                        //Insertamos el movimiento
                        self::insertarHistorialSucursal($idFranquicia, $idProducto, $id_UsuarioC, $cambio);
                    }

                    //Se modifico el precio del producto?
                    if ($datosActualesProducto[0]->precio != $precio) {
                        //Se modifico el precio
                        $cambio = "Actualizo el precio del producto: " . $datosActualesProducto[0]->id . " - " . $datosActualesProducto[0]->nombre .
                            " color: " . $datosActualesProducto[0]->color . " de: '" . $datosActualesProducto[0]->precio . "' a '" . $precio . "'";
                        //Insertamos el movimiento
                        self::insertarHistorialSucursal($idFranquicia, $idProducto, $id_UsuarioC, $cambio);
                    }

                    //Se modifico el color del producto?
                    if ($datosActualesProducto[0]->color != $color) {
                        //Se modifico el color
                        $cambio = "Actualizo el color del producto: " . $datosActualesProducto[0]->id . " - " . $datosActualesProducto[0]->nombre .
                            " color: " . $datosActualesProducto[0]->color . " de: '" . $datosActualesProducto[0]->color . "' a '" . $color . "'";
                        //Insertamos el movimiento
                        self::insertarHistorialSucursal($idFranquicia, $idProducto, $id_UsuarioC, $cambio);
                    }

                    //Se modifico el tipo del producto?
                    if ($datosActualesProducto[0]->id_tipoproducto != $tipoProducto) {
                        //Se modifico el tipo producto
                        $cambio = "Actualizo el tipo del producto: " . $datosActualesProducto[0]->id . " - " . $datosActualesProducto[0]->nombre .
                            " color: " . $datosActualesProducto[0]->color . "de '" . $datosActualesProducto[0]->id_tipoproducto . "' a '" . $tipoProducto . "'";
                        //Insertamos el movimiento
                        self::insertarHistorialSucursal($idFranquicia, $idProducto, $id_UsuarioC, $cambio);
                    }

                    //Se actualizo la foto?
                    if ($banderaActualizoFoto) {
                        //BanderaActualizoFoto = true
                        $cambio = "Actualizo la foto del producto: " . $datosActualesProducto[0]->id . " - " . $datosActualesProducto[0]->nombre .
                            " color: " . $datosActualesProducto[0]->color;
                        self::insertarHistorialSucursal($idFranquicia, $idProducto, $id_UsuarioC, $cambio);
                    }

                    //Se actualizo caracteristica premium?
                    if ($datosActualesProducto[0]->premium != $premium) {
                        //Se modifico premium
                        if($premium == "1"){
                            //Se activo
                            $cambio = "Actualizo estado de premium a: 'activo' del producto: " . $datosActualesProducto[0]->id . " - " . $datosActualesProducto[0]->nombre .
                                " color: " . $datosActualesProducto[0]->color;
                        }else{
                            //Se desactivo
                            $cambio = "Actualizo estado de premium a: 'desactivado' del producto: " . $datosActualesProducto[0]->id . " - " . $datosActualesProducto[0]->nombre .
                                " color: " . $datosActualesProducto[0]->color;
                        }
                        //Insertamos el movimiento
                        self::insertarHistorialSucursal($idFranquicia, $idProducto, $id_UsuarioC, $cambio);
                    }

                    $forzado = 0;
                    DB::table('producto')->where('id', '=', $idProducto)->update([
                        'nombre' => request('nombre'), 'id_tipoproducto' => request('tipoproducto'), 'piezas' => $piezasrestantesactualizar, 'precio' => $precio,
                        'color' => request('color'), 'estado' => $estado, 'foto' => $foto, 'iniciop' => $in, 'finp' => $fi, 'preciop' => $preciop,
                        'activo' => $activo, 'forzado' => $forzado, 'polizagastos' => $polizagastos, 'polizagastoscobranza' => $polizagastoscobranza,
                        'polizagastosadministracion' => $polizagastosadministracion, 'premium' => $premium, 'totalpiezas' => $totalpiezasactualizar
                    ]);
                    return redirect()->route('listasfranquicia', $idFranquicia)->with('bien', 'El producto se actualizo correctamente.');
                } catch (\Exception $e) {
                    \Log::info("Error: " . $e->getMessage());
                    return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
                }

            }
            //No existe producto
            return back()->with('alerta', "El producto no existe.");

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }


    public function productodesactivarpromo($idFranquicia,$idProducto){
        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
            $activo = 0;
            $forzado = 1;
            DB::table('producto')->where('id','=',$idProducto)->update([
                'activo'=>$activo,'forzado' => $forzado
            ]);

            //Registrar movimiento historialSucursal
            $producto = DB::select("SELECT p.nombre FROM producto p WHERE p.id = '$idProducto' AND p.id_franquicia = '$idFranquicia'");
            $id_UsuarioC = Auth::user()->id;
            $cambio = "Desactivo promoción para producto: '" . $producto[0]->nombre. "'";
            self::insertarHistorialSucursal($idFranquicia, $idProducto,$id_UsuarioC ,$cambio);

            return redirect()->route('listasfranquicia',$idFranquicia)->with('bien','La promocion del producto se desactivo correctamente.');
          }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function tratamientonuevo($idFranquicia){
        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
            $franquiciaAdmin = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");
            return view('administracion.franquicia.administracion.nuevotratamiento',['franquiciaAdmin' => $franquiciaAdmin,'idFranquicia' => $idFranquicia]);
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function  tratamientocrear($idFranquicia,Request $request){
        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
            $rules = [
                'tratamiento'=>'required|string|max:255',
                'precio'=>'required|integer'
            ];
            if (request('precio') < 0){
                return back()->withErrors(['precio' => 'El precio no puede ser menor a 0'])->withInput($request->all());
            }
            request()->validate($rules);
            try{
                DB::table('tratamientos')->insert([
                    'id_franquicia' => $idFranquicia,'nombre' => request('tratamiento'),'precio'=> request('precio'),'created_at' => Carbon::now()
                ]);

                //Registrar movimiento en historialsucursal
                $id_UsuarioC = Auth::user()->id;
                $cambio = "Creo tratamiento con nombre de: " . request('tratamiento');
                self::insertarMovimientoHistorialSucursal($idFranquicia, $id_UsuarioC, $cambio);

                return redirect()->route('listasfranquicia',$idFranquicia)->with('bien','El tratamiento se creo correctamente.');
            }catch(\Exception $e){
                \Log::info("Error: ".$e->getMessage());
                return back()->with('error','Tuvimos un problema, por favor contacta al aministrador de la pagina.');
            }

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function tratamientoactualizar($idFranquicia,$idTratamiento){
        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
            $tratamiento = DB::select("SELECT * FROM tratamientos WHERE id = '$idTratamiento' AND id_franquicia = '$idFranquicia'");
            $franquiciaAdmin = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");
            $coloresTratamiento = DB::select("SELECT * FROM tratamientoscolores tc WHERE tc.id_franquicia = '$idFranquicia' AND tc.id_tratamiento = '$idTratamiento'
                                                    ORDER BY tc.color ASC");

            return view('administracion.franquicia.administracion.actualizartratamiento',['idFranquicia' => $idFranquicia,'tratamiento'=>$tratamiento,
                'franquiciaAdmin' => $franquiciaAdmin, 'coloresTratamiento' => $coloresTratamiento, 'indice' => 1]);
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function tratamientoeditar($idFranquicia,$idTratamiento,Request $request){
        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
            $rules =  [
                'tratamiento'=>'required|string|max:255',
                'precio'=>'required|integer'
            ];
            if (request('precio') < 0){
                return back()->withErrors(['precio' => 'El precio no puede ser menor a 0'])->withInput($request->all());
            }
            try{
                DB::table('tratamientos')->where([['id','=',$idTratamiento],['id_franquicia','=',$idFranquicia]])->update([
                    'precio'=>request('precio'),
                ]);

                //Registrar movimiento en historialsucursal
                $id_UsuarioC = Auth::user()->id;
                $cambio = "Actualizo precio de tratamiento: '" . request('tratamiento'). "' a '" . request('precio') . "'";
                self::insertarHistorialSucursal($idFranquicia, $idTratamiento,$id_UsuarioC, $cambio);

                return redirect()->route('listasfranquicia',$idFranquicia)->with('bien','El tratamiento se actualizo correctamente.');
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

    public function agregarcolortratamiento($idFranquicia,$idTratamiento,Request $request){
        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
            $rules = [
                'colorTratamiento' => 'required|string'
            ];

            request()->validate($rules);

            $existeTratamiento = DB::select("SELECT * FROM tratamientos t WHERE t.id = '$idTratamiento' AND t.id_franquicia = '$idFranquicia'");
            if($existeTratamiento != null){

                try{

                    DB::table('tratamientoscolores')->insert([
                        'id_franquicia' => $idFranquicia, 'id_tratamiento' => $idTratamiento, 'color' => strtoupper(request('colorTratamiento')),
                        'created_at' => Carbon::now()
                    ]);

                    //Registrar movimiento en historialsucursal
                    $id_UsuarioC = Auth::user()->id;
                    $cambio = "Agrego color: '" . request('colorTratamiento') ."' a tratamiento: '" .$existeTratamiento[0]->nombre. "'";
                    self::insertarHistorialSucursal($idFranquicia, $idTratamiento,$id_UsuarioC, $cambio);

                    return back()->with('bien','Color registrado correctamente.');

                }catch(\Exception $e){
                    \Log::info("Error: ".$e->getMessage());
                    return back()->with('error','Tuvimos un problema, por favor contacta al administrador de la pagina.');
                }

            }else{
                return back()->with('alerta','No existe el tratamiento.');
            }

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function eliminarcolortratamiento($idFranquicia){
        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
            $idColor = request('idColor');

            $existeColor = DB::select("SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = '$idColor'");
            $sucursal = DB::select("SELECT f.ciudad FROM franquicias f WHERE f.id = '$idFranquicia'");

            if($existeColor != null){

                try{

                    DB::delete("DELETE FROM tratamientoscolores WHERE indice = '$idColor'");

                    //Registrar movimiento en historialsucursal
                    $id_UsuarioC = Auth::user()->id;
                    $cambio = "Elimino color: '" . $existeColor[0]->color. "' de tratamiento: 'Fotocromatico' para sucursal: '" . $sucursal[0]->ciudad."'.";
                    self::insertarMovimientoHistorialSucursal($idFranquicia,$id_UsuarioC, $cambio);

                    return back()->with('bien','Color eliminado correctamente.');

                }catch(\Exception $e){
                    \Log::info("Error: ".$e->getMessage());
                    return back()->with('error','Tuvimos un problema, por favor contacta al administrador de la pagina.');
                }
            }else{
                return back()->with('bien','No existe el color.');
            }

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function paquetenuevo($idFranquicia){
        $contratosGlobal = new contratosGlobal;
        if(Auth::check() && ($contratosGlobal::validarPermisoSeccion(Auth::user()->id, 2, 0)) && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
            $franquiciaAdmin = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");
            return view('administracion.franquicia.administracion.nuevopaquete',['franquiciaAdmin' => $franquiciaAdmin,'idFranquicia' => $idFranquicia]);
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function  paquetecrear($idFranquicia,Request $request){
        $contratosGlobal = new contratosGlobal;
        if(Auth::check() && ($contratosGlobal::validarPermisoSeccion(Auth::user()->id, 2, 0)) && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
                $rules = [
                'paquete'=>'required|string|max:255',
                'precio'=>'required|integer'
            ];
            if (request('precio') < 0){
                return back()->withErrors(['precio' => 'El precio no puede ser menor a 0'])->withInput($request->all());
            }
            request()->validate($rules);
            try{
                DB::table('paquetes')->insert([
                    'id_franquicia' => $idFranquicia,'nombre' => request('paquete'),'precio'=> request('precio'),'created_at' => Carbon::now()
                ]);

                //Registrar movimiento en historialsucursal
                $id_UsuarioC = Auth::user()->id;
                $cambio = "Creo paquete con nombre: '" . request('paquete'). "'";
                self::insertarMovimientoHistorialSucursal($idFranquicia, $id_UsuarioC, $cambio);

                return redirect()->route('listasfranquicia',$idFranquicia)->with('bien','el paquete se creo correctamente.');
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

    public function paqueteactualizar($idFranquicia,$idPaquete){
        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
            $paquete = DB::select("SELECT * FROM paquetes WHERE id = '$idPaquete' AND id_franquicia = '$idFranquicia'");
            $franquiciaAdmin = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");
            return view('administracion.franquicia.administracion.actualizarpaquete',['idFranquicia' => $idFranquicia,'paquete'=>$paquete,'franquiciaAdmin' => $franquiciaAdmin]);
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function paqueteeditar($idFranquicia,$idPaquete,Request $request){

        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
            $rules = [
                'paquete'=>'required|string|max:255',
                'precio'=>'required|integer'
            ];
            if (request('precio') < 0){
                return back()->withErrors(['precio' => 'El precio no puede ser menor a 0'])->withInput($request->all());
            }

            //Datos actuales paquete
            $paquete = DB::select("SELECT p.nombre, p.precio FROM paquetes p WHERE p.id = '$idPaquete' AND p.id_franquicia =  '$idFranquicia'");
            $nombrePaquete = $paquete[0]->nombre;
            $precioPaquete = $paquete[0]->precio;
            $id_UsuarioC = Auth::user()->id;

            try{
                DB::table('paquetes')->where([['id','=',$idPaquete],['id_franquicia','=',$idFranquicia]])->update([
                    'nombre'=>request('paquete'),'precio'=>request('precio'),'updated_at' => Carbon::now()
                ]);

                //Se actualizo nombre paquete?
                if($nombrePaquete != request('paquete')){
                    //Si se actualizo el nombre del paquete
                    $cambio = "Actualizo nombre paquete: '" . $nombrePaquete ."' a '" . request('paquete') ."'";
                    self::insertarHistorialSucursal($idFranquicia, $idPaquete, $id_UsuarioC,$cambio);
                }
                //Se actualizo precio paquete?
                if($precioPaquete != request('precio')){
                    //Si se actualizo el nombre del paquete
                    $cambio = "Actualizo precio paquete " . $nombrePaquete ." de '" . $precioPaquete ."' a '" . request('precio') ."'";
                    self::insertarHistorialSucursal($idFranquicia, $idPaquete, $id_UsuarioC,$cambio);
                }

                return redirect()->route('listasfranquicia',$idFranquicia)->with('bien','El paquete se actualizo correctamente.');
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

    public function mensajeNuevo($idFranquicia){
        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
            return view('administracion.franquicia.administracion.mensajes.nuevo',["idFranquicia"=>$idFranquicia]);
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function crearmensaje($idFranquicia, Request $request){

        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
            $rules = [
                'descripcion'=>'required|string',
                'numero'=>'required|integer'
            ];
            request()->validate($rules);
            $fecha = null;
            if(strlen($request->fecha) > 0){
                try{ $fecha =   Carbon::parse($request->fecha);}catch(\Exception $e){ return back()->with("alerta","Ingresa una fecha correcta.")->withInput($request->all());}
            }

            $cadenaSucursales = "";
            if(Auth::user()->rol_id != 7 || (Auth::user()->rol_id == 7 && $request->cbTodasSucursales != 1)){
                //Es diferente de director o es director pero no requiere el mensaje para todas las sucursales
                $cadenaSucursales = " WHERE f.id = '$idFranquicia' ";
            }

            //Obtener todas las sucursales a las cuales les aparecera el mensaje
            $sucursales = DB::select("SELECT f.id FROM franquicias f " . $cadenaSucursales . " ORDER BY f.ciudad ASC");

            foreach ($sucursales as $sucursal){
                DB::table("mensajes")->insert([
                    "id_franquicia" => $sucursal->id,
                    "descripcion" => $request->descripcion,
                    "fechalimite" =>$fecha,
                    "intentos" => $request->numero,
                    "created_at" => Carbon::now()
                ]);

                //Registrar movimiento
                $id_UsuarioC = Auth::user()->id;
                $cambio = "Agrego nuevo mensaje: '". $request->descripcion . "'";
                self::insertarMovimientoHistorialSucursal($sucursal->id, $id_UsuarioC, $cambio);
            }

            return redirect()->route('listasfranquicia',$idFranquicia)->with("bien","El mensaje fue creado correctamente.");

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }
    public function eliminarmensaje($idFranquicia,$idMensaje){

        if(Auth::check() && ((Auth::user()->rol_id) == 6 || ((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8)))
        {
            try{
                $mensaje = DB::select("SELECT m.descripcion FROM mensajes m WHERE m.id = '$idMensaje' AND m.id_franquicia = '$idFranquicia'");
                DB::delete("DELETE FROM mensajes WHERE id = '$idMensaje'");

                //Registrar movimiento
                $id_UsuarioC = Auth::user()->id;
                $cambio = "Elimino mensaje: '". $mensaje[0]-> descripcion. "'";
                self::insertarMovimientoHistorialSucursal($idFranquicia, $id_UsuarioC, $cambio);

            }catch(\Exception $e){
                \Log::info("Error:".$e);
            }
            return redirect()->route('listasfranquicia',$idFranquicia)->with("bien","El mensaje se elimino correctamente.");
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }


    public function editarabonominimo($idFranquicia, $tipoPago)
    {
        if (Auth::check() && (Auth::user()->rol_id) == 7) //ROL DEL DIRECTOR
        {
            $abonoMinimo = DB::select("SELECT pago, abonominimo FROM abonominimofranquicia WHERE pago = '$tipoPago' AND id_franquicia = '$idFranquicia'");

            return view('administracion.franquicia.administracion.actualizarabonominimo', ['abonoMinimo' => $abonoMinimo, 'idFranquicia' => $idFranquicia]);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarabonominimo($idFranquicia, $tipoPago) {
        if (Auth::check() && (Auth::user()->rol_id) == 7) //ROL DEL DIRECTOR
        {
            request()->validate([
                'abonominimo' => 'required|integer|min:0'
            ]);

            //Actualizar abono minimo para la sucursal
            DB::table('abonominimofranquicia')->where([['id_franquicia', '=', $idFranquicia], ['pago', '=', $tipoPago]])
                ->update(['abonominimo' => request('abonominimo'), 'updated_at' => Carbon::now()]);

            return back()->with('bien',' Abono minimo actualizado correctamente.');

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    //Funcion: insertarHistorialSucursal
    //Descripcion: Inserta un movimiento generado sobre un producto (Esta funcion requiere el id del Producto insertado o actualizado)
    public function  insertarHistorialSucursal($idFranquicia, $idProducto, $idUsuarioC, $cambio){
        DB::table('historialsucursal')->insert([
            'id_usuarioC' => $idUsuarioC, 'id_franquicia' => $idFranquicia, 'id_producto' => $idProducto,
            'tipomensaje' => '0', 'created_at' => Carbon::now(), 'cambios' => $cambio, 'seccion' => '2'
        ]);
    }

    //Funcion: insertarMovimientoHistorialSucursal
    //Descripcion: Inserta los movimientos generados en administracion que no requieren id del producto
    public function insertarMovimientoHistorialSucursal($idFranquicia, $idUsuarioC, $cambio){
        DB::table('historialsucursal')->insert([
            'id_usuarioC' => $idUsuarioC, 'id_franquicia' => $idFranquicia,
            'tipomensaje' => '0', 'created_at' => Carbon::now(), 'cambios' => $cambio, 'seccion' => '2'
        ]);
    }

    public function editarcomisionventa($idFranquicia, $indice)
    {
        if (Auth::check() && (Auth::user()->rol_id) == 7) //ROL DEL DIRECTOR
        {
            $comisionventa = DB::select("SELECT * FROM comisionesventas WHERE id_franquicia = '$idFranquicia' AND indice = '$indice'");

            if ($comisionventa != null) {
                //Existe comision venta
                return view('administracion.franquicia.administracion.actualizarcomisionventa', ['idFranquicia' => $idFranquicia, 'comisionventa' => $comisionventa]);
            }
            return back()->with('alerta','La comisión no existe.');

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarcomisionventa($idFranquicia, $indice) {
        if (Auth::check() && (Auth::user()->rol_id) == 7) //ROL DEL DIRECTOR
        {

            $comisionventa = DB::select("SELECT * FROM comisionesventas WHERE id_franquicia = '$idFranquicia' AND indice = '$indice'");

            if ($comisionventa != null) {
                //Existe comision venta

                $totalcontratos = request('totalcontratos');
                $valor = request('valor');

                if (strlen($totalcontratos) == 0) {
                    //totalcontratos vacio
                    return back()->with('alerta', 'Campo contratos vacío.');
                }

                if (strlen($valor) == 0) {
                    //valor vacio
                    return back()->with('alerta', 'Campo pago vacío.');
                }

                if ($totalcontratos < 0) {
                    //totalcontratos menor a 0
                    return back()->with('alerta', 'Campo contratos no debe de ser menor a 0.');
                }

                if ($valor < 0 || ($comisionventa[0]->usuario == 1 && $valor > 100)) {
                    //valor menor a 0
                    return back()->with('alerta', 'El pago debe de contener un porcentaje del 0 al 100.');
                }

                //Actualizar comision para la sucursal
                DB::table('comisionesventas')->where([['id_franquicia', '=', $idFranquicia], ['indice', '=', $indice]])
                    ->update(['totalcontratos' => $totalcontratos, 'valor' => $valor, 'updated_at' => Carbon::now()]);

                return redirect()->route('listasfranquicia', $idFranquicia)->with('bien', ' Comisión actualizada correctamente.');

            }
            return back()->with('alerta','La comisión no existe.');

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }


    public function zonanueva($idFranquicia){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 ||  (Auth::user()->rol_id) == 8)) //ROL DE ADMINISTRACION, DIRECTOR, PRINCIPAL
        {

            return view('administracion.franquicia.administracion.nuevazona', ['idFranquicia' => $idFranquicia]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function zonacrear($idFranquicia, Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 ||  (Auth::user()->rol_id) == 8)) //ROL DE ADMINISTRACION, DIRECTOR, PRINCIPAL
        {
            $validaciones = Validator::make($request->all(),[
                'nombreZona' => 'required|string'
            ]);

            if ($validaciones->fails()) {
                return back() ->withErrors($validaciones)->withInput();
            }

            try{
                //Agergar nueva zona a sucursal
                DB::table('zonas')->insert([
                    'id_franquicia' => $idFranquicia,
                    'zona' => $request->input("nombreZona"),
                    'created_at' => Carbon::now()
                ]);

                //Registrar movimiento en tabla historialsucursal
                $idUsuario = Auth::id();
                $sucursal = DB::select("SELECT ciudad FROM franquicias WHERE id = '$idFranquicia'");
                $mensaje = " Creo la zona '". $request->input("nombreZona") ."' para sucursal '".$sucursal[0]->ciudad ."'";

                DB::table('historialsucursal')->insert([
                    'id_usuarioC' => $idUsuario,
                    'id_franquicia' => $idFranquicia, 'tipomensaje' => '12',
                    'created_at' => Carbon::now(), 'cambios' => $mensaje,
                    'seccion' => '2'
                ]);

                return redirect()->route('listasfranquicia', $idFranquicia)->with('bien', ' Zona creada correctamente.');

            }catch(\Exception $e){
                \Log::info("Error crear zona:".$e);
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function zonaeditar($idFranquicia, $idZona){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 ||  (Auth::user()->rol_id) == 8)) //ROL DE ADMINISTRACION, DIRECTOR, PRINCIPAL
        {
            $existeZona = DB::select("SELECT z.zona FROM zonas z WHERE z.id = '$idZona' AND z.id_franquicia = '$idFranquicia'");
            if($existeZona){
                $nombreZona = $existeZona[0]->zona;
                $colonias = DB::select("SELECT * FROM colonias c WHERE c.id_franquicia = '$idFranquicia' AND c.id_zona = '$idZona' ORDER BY c.localidad ASC, c.colonia ASC");
                $zonas = DB::select("SELECT * FROM zonas z WHERE z.id_franquicia = '$idFranquicia' ORDER BY z.zona ASC");

                return view('administracion.franquicia.administracion.actualizarzona', ['idFranquicia' => $idFranquicia, 'idZona' => $idZona, 'nombreZona' => $nombreZona,
                            'colonias' => $colonias, 'zonas' => $zonas]);

            }else{
                //No existe la zona seleccionada para actualizar
                return redirect()->route('listasfranquicia',$idFranquicia)->with('alerta','La zona no existe.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function zonafiltrarcolonias($idFranquicia, $idZona, Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 ||  (Auth::user()->rol_id) == 8)) //ROL DE ADMINISTRACION, DIRECTOR, PRINCIPAL
        {
            $filtroColonia = $request->input("filtroColonias");
            $existeZona = DB::select("SELECT z.zona FROM zonas z WHERE z.id = '$idZona' AND z.id_franquicia = '$idFranquicia'");

            if($existeZona){
                $nombreZona = $existeZona[0]->zona;
                $colonias = DB::select("SELECT * FROM colonias c WHERE c.id_franquicia = '$idFranquicia' AND c.id_zona = '$idZona'
                                              AND (c.colonia LIKE '%$filtroColonia%' OR c.localidad LIKE '%$filtroColonia%')  ORDER BY c.localidad ASC, c.colonia ASC");
                $zonas = DB::select("SELECT * FROM zonas z WHERE z.id_franquicia = '$idFranquicia' ORDER BY z.zona ASC");
                return view('administracion.franquicia.administracion.actualizarzona', ['idFranquicia' => $idFranquicia, 'idZona' => $idZona, 'nombreZona' => $nombreZona,
                    'colonias' => $colonias, 'zonas' => $zonas]);

            }else{
                //No existe la zona seleccionada para actualizar
                return redirect()->route('listasfranquicia',$idFranquicia)->with('alerta','La zona no existe.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function cambiarzonaeditar($idFranquicia, Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 ||  (Auth::user()->rol_id) == 8)) //ROL DE ADMINISTRACION, DIRECTOR, PRINCIPAL
        {
            $idZonaSeleccionada = $request->input("zonaSeleccionada");

            $validaciones = Validator::make($request->all(),[
                'zonaSeleccionada' => 'required'
            ]);

            if ($validaciones->fails()) {
                return back() ->withErrors($validaciones)->withInput($request->all())->with('alerta','Seleccionar una zona valida.');
            }

            $existeZona = DB::select("SELECT z.zona FROM zonas z WHERE z.id = '$idZonaSeleccionada' AND z.id_franquicia = '$idFranquicia'");
            if($existeZona){
                //Si se selecciona una zona correcta - Redireccionar a vista editar zona pero con nueva zona seleccionada
                return redirect()->route('zonaeditar',[$idFranquicia,$idZonaSeleccionada]);
            }else{
                //No existe la zona seleccionada para actualizar
                return redirect()->route('listasfranquicia',$idFranquicia)->with('alerta','La zona no existe.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function agregarcoloniazona($idFranquicia, $idZona, Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 ||  (Auth::user()->rol_id) == 8)) //ROL DE ADMINISTRACION, DIRECTOR, PRINCIPAL
        {
            $validaciones = Validator::make($request->all(),[
                'colonia' => 'required|string',
                'localidad' => 'required|string'
            ]);

            if ($validaciones->fails()) {
                return back() ->withErrors($validaciones)->withInput($request->all())->with('alerta','Debes ingresar los datos completos de la nueva colonia a agregar.');
            }

            $existeZona = DB::select("SELECT z.zona FROM zonas z WHERE z.id = '$idZona' AND z.id_franquicia = '$idFranquicia'");
            if($existeZona){
                //Zona valida

                $banderaActual=DB::select("SELECT * FROM colonias c WHERE c.id_franquicia = '$idFranquicia' ORDER BY c.created_at ASC LIMIT 1");
                $bandera = 0;

                //Existe una bandera registrada?
                if($banderaActual != null && !is_null($banderaActual[0]->bandera)){
                    //Existe ya un registro con un balor de bandera en BD
                    $bandera = $banderaActual[0]->bandera;
                }

                // Insertar nueva colonia en tabla colonias
                DB::table('colonias')->insert([
                    'id_franquicia' => $idFranquicia,
                    'id_zona' => $idZona,
                    'colonia' => $request->input("colonia"),
                    'localidad' => $request->input("localidad"),
                    'bandera' => $bandera,
                    'created_at' => Carbon::now()
                ]);

                //Actualizar bandera para todas las colonias de la sucursal
                self::actualizarBanderaColoniaSucursal($idFranquicia);

                //Registrar movimiento en tabla historialsucursal
                $idUsuario = Auth::id();
                $sucursal = DB::select("SELECT ciudad FROM franquicias WHERE id = '$idFranquicia'");
                $zona = DB::select("SELECT z.zona FROM zonas z WHERE z.id = '$idZona' AND z.id_franquicia = '$idFranquicia'");
                $mensaje = " Agrego la colonia '".$request->input("colonia"). "' de zona '" . $zona[0]->zona . "' para sucursal '".$sucursal[0]->ciudad ."'";

                DB::table('historialsucursal')->insert([
                    'id_usuarioC' => $idUsuario,
                    'id_franquicia' => $idFranquicia, 'tipomensaje' => '13',
                    'created_at' => Carbon::now(), 'cambios' => $mensaje,
                    'seccion' => '2'
                ]);

                //Retornar mensaje de correcto
                return back()->with('bien','Colonia agregada de la zona correctamente.');

            }else{
                //No existe la zona
                return back()->with('alerta','La zona no existe.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function eliminarcoloniazona($idFranquicia, $idZona, $indiceColonia){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 ||  (Auth::user()->rol_id) == 8)) //ROL DE ADMINISTRACION, DIRECTOR, PRINCIPAL
        {

            $existeZona = DB::select("SELECT z.zona FROM zonas z WHERE z.id = '$idZona' AND z.id_franquicia = '$idFranquicia'");
            if($existeZona){
                //Zona valida
                $existeColonia = DB::select("SELECT * FROM colonias c WHERE c.indice = '$indiceColonia' AND c.id_franquicia = '$idFranquicia' AND c.id_zona = '$idZona'");

                if($existeColonia){
                    //Colonia si existe - Eliminar colonia de tabla colonias
                    DB::delete("DELETE FROM colonias WHERE indice = '$indiceColonia' AND id_franquicia = '$idFranquicia' AND id_zona = '$idZona'");

                    //Actualizar bandera para todas las colonias de la sucursal
                    self::actualizarBanderaColoniaSucursal($idFranquicia);

                    //Registrar movimiento en tabla historialsucursal
                    $idUsuario = Auth::id();
                    $sucursal = DB::select("SELECT ciudad FROM franquicias WHERE id = '$idFranquicia'");
                    $zona = DB::select("SELECT z.zona FROM zonas z WHERE z.id = '$idZona' AND z.id_franquicia = '$idFranquicia'");
                    $mensaje = " Elimino la colonia '".$existeColonia[0]->colonia . "' de zona '" . $zona[0]->zona . "' para sucursal '".$sucursal[0]->ciudad ."'";

                    DB::table('historialsucursal')->insert([
                        'id_usuarioC' => $idUsuario,
                        'id_franquicia' => $idFranquicia, 'tipomensaje' => '13',
                        'created_at' => Carbon::now(), 'cambios' => $mensaje,
                        'seccion' => '2'
                    ]);

                    //Retornar mensaje de correcto
                    return back()->with('bien','Colonia eliminada de la zona correctamente.');

                }else{
                    //No existe la colonia seleccionada para actualizar
                    return back()->with('alerta','La colonia seleccionada no existe.');
                }
            }else{
                //No existe la zona
                return back()->with('alerta','La zona no existe.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function eliminarzona($idFranquicia, Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 ||  (Auth::user()->rol_id) == 8)) {
            //ROL DE ADMINISTRACION, DIRECTOR, PRINCIPAL

            $idZona = $request->input("idZona");

            $existeZona = DB::select("SELECT z.zona FROM zonas z WHERE z.id = '$idZona' AND z.id_franquicia = '$idFranquicia'");
            if($existeZona){
                //Zona valida

                $existenContratosZona = DB::select("SELECT * FROM contratos c WHERE c.id_zona = '$idZona' ORDER BY c.created_at DESC LIMIT 1");

                if($existenContratosZona == null){
                    //Es una zona sin contratos registrados - Eliminar zona

                    DB::delete("DELETE FROM zonas WHERE id = '$idZona' AND id_franquicia = '$idFranquicia'");
                    DB::delete("DELETE FROM  colonias WHERE id_zona = '$idZona' AND id_franquicia = '$idFranquicia'");

                    //Registrar movimiento en tabla historialsucursal
                    $idUsuario = Auth::id();
                    $sucursal = DB::select("SELECT ciudad FROM franquicias WHERE id = '$idFranquicia'");
                    DB::table('historialsucursal')->insert([
                        'id_usuarioC' => $idUsuario,
                        'id_franquicia' => $idFranquicia, 'tipomensaje' => '13',
                        'created_at' => Carbon::now(), 'cambios' => " Elimino la zona '".$existeZona[0]->zona . "' para sucursal '".$sucursal[0]->ciudad ."'",
                        'seccion' => '2'
                    ]);

                    return back()->with('bien','Zona eliminada correctamente.');

                }else{
                    //Existe al menos un contrato registrado para esa zona
                    return back()->with('alerta','No puedes eliminar la zona debido a que tiene contratos registrados.');
                }

            }else{
                //No existe la zona
                return back()->with('alerta','La zona no existe.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarBanderaColoniaSucursal($idFranquicia){

        //Verificar bandera actual para sucursal
        $banderaActual = DB::select("SELECT c.bandera FROM colonias c WHERE c.id_franquicia = '$idFranquicia' ORDER BY c.created_at DESC LIMIT 1");
        $nuevaBandera = 0 ;

        if($banderaActual != null && !is_null($banderaActual[0]->bandera)){
            //Existen colonias para esta sucursal y a demas no es la primer colonia insertada en la BD
                $nuevaBandera = $banderaActual[0]->bandera + 1;
        }

        //Actualizar bandera para todas las colonias de la sucursal
        DB::update("UPDATE colonias SET bandera = '$nuevaBandera' WHERE id_franquicia = '$idFranquicia'");

    }

    public function actualizaraccionbanderaasistenciafranquicia($idFranquicia)
    {

        if (Auth::check() && ((Auth::user()->rol_id) == 7)) {

            try {

                $accionbanderaasistenciafranquicia = DB::select("SELECT indice, estatus FROM accionesbanderasfranquicia WHERE id_franquicia = '$idFranquicia' AND tipo = '0' ORDER BY created_at DESC LIMIT 1");

                if ($accionbanderaasistenciafranquicia != null) {
                    //Existe accion bandera asistencia franquicia

                    $indiceAccionBanderaAsietenciaFranquicia = $accionbanderaasistenciafranquicia[0]->indice;
                    $estatusaccionbanderaasistenciafranquicia = request('estatusaccionbanderaasistenciafranquicia') == null ? 0 : 1;

                    //Actualizar bandera para todas las colonias de la sucursal
                    DB::update("UPDATE accionesbanderasfranquicia SET estatus = '$estatusaccionbanderaasistenciafranquicia' WHERE indice = '$indiceAccionBanderaAsietenciaFranquicia'");

                    $mensaje = "";
                    if ($estatusaccionbanderaasistenciafranquicia == 1){
                        $mensaje = "por lector de barras";
                    }else {
                        $mensaje = "por número de control";
                    }

                    //Insertar movimiento en historialsucursal
                    self::insertarHistorialSucursal($idFranquicia, null, Auth::user()->id, "Acción de asistencia $mensaje actualizada.");

                    return back()->with("bien", "Acción de asistencia $mensaje actualizada.");
                }
                //No existe accion bandera asistencia franquicia
                return back()->with("alerta", "No existe accion asistencia franquicia para esta franquicia.");

            } catch (Exception $e) {
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

    public function actualizarhorabanderaterminarpolizafranquicia($idFranquicia)
    {
        if (Auth::user()->rol_id == 7) //ROL DEL DIRECTOR
        {
            $horaterminarpolizafranquicia = request('horaterminarpolizafranquicia');

            if($horaterminarpolizafranquicia != null) {
                //horaterminarpolizafranquicia es diferente de vacio

                if(strlen($horaterminarpolizafranquicia) <= 5) {
                    $horaterminarpolizafranquicia = $horaterminarpolizafranquicia . ":00";
                }

                $horaterminarpolizafranquicia = Carbon::parse($horaterminarpolizafranquicia)->minute(0)->second(0)->format('H:i:s');
                $horaterminarpolizafranquiciaactualizar = Carbon::parse($horaterminarpolizafranquicia)->format('H');

                DB::table('accionesbanderasfranquicia')->where('id_franquicia', $idFranquicia)->where('tipo', '1')->update([
                    'estatus' => $horaterminarpolizafranquiciaactualizar,
                    'updated_at' => Carbon::now()
                ]);

                //Registrar movimiento en historialsucursal
                self::insertarHistorialSucursal($idFranquicia, null, Auth::user()->id, "Actualizó horario $horaterminarpolizafranquicia para terminación de poliza automaticamente.");

                return back()->with('bien', 'El horario se actualizó correctamente.');

            }

            return back()->with('alerta', 'El horario a actualizar esta vacio.');

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

}









