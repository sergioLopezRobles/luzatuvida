<?php

namespace App\Http\Controllers\Dominios\Clientes;

use App\Http\Controllers\Dominios\Administracion\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class navegacion extends Controller
{
    public function bienvenidaClientes(){
        $idFranquicia = "6E2AA";
        $imagnesCarrusel = DB::select("SELECT * FROM imagenescarrusel ic ORDER BY ic.posicion ASC");
        $fechaActual = Carbon::parse(Carbon::now())->format("Y-m-d");

        return view('clientes.bienvenida', ['imagnesCarrusel' => $imagnesCarrusel, 'idFranquicia' => $idFranquicia, 'fechaActual' => $fechaActual ]);
    }
    public function informaciongeneral(){
        $idFranquicia = "6E2AA";
        return view('clientes.informaciongeneral', ['idFranquicia' => $idFranquicia]);
    }
    public function horariodeatencion(){
        $idFranquicia = "6E2AA";
        $franquicias = DB::select("SELECT f.id, f.ciudad, f.calle, f.entrecalles, f.colonia, f.numero, f.estado, f.whatsapp, f.telefonoatencionclientes,
                                         REPLACE(f.coordenadas,' ', '') AS coordenadas FROM franquicias f WHERE f.id != '00000'
                                         ORDER BY f.indice ASC");

        return view('clientes.horariodeatencion', ['franquicias' => $franquicias, 'idFranquicia' => $idFranquicia]);
    }
    public function productoslista(){
        $idFranquicia = "6E2AA";
        return view('clientes.productos.productos', ['idFranquicia' => $idFranquicia]);
    }

    public function servicioslista(){
        $idFranquicia = "6E2AA";
        $fechaActual = Carbon::parse(Carbon::now())->format("Y-m-d");
        return view('clientes.servicios.servicios', ['idFranquicia' => $idFranquicia, 'fechaActual' => $fechaActual ]);
    }
    public function formulariorastreo(){
        $idFranquicia = "6E2AA";

        return view('clientes.contrato.formulariorastreo', ['idFranquicia' => $idFranquicia]);

    }
    public function rastrearContrato(Request $request){

        $validaciones = Validator::make($request->all(),[
            'idContrato' => 'required|string|size:14',
            'telefono' => 'required|string|size:13',
            'g-recaptcha-response' => 'required'
        ]);


        if ($validaciones->fails()) {
            return back()->withErrors($validaciones)->withInput()->with('alerta',"Verifica la información ingresada.");
        }

        $idContrato = request('idContrato');
       $telefonoCliente = request('telefono');
       $banderaGarantia = 0;
       $estadoContrato = "";

       //Validacion Capcha

       //Contiene 14 caracteres el idContrato?
            if(strlen($idContrato) == 14){
                //Validar id contrato
                $year = Carbon::now()->format('y'); //Obtener los ultimos 2 digitos del año 21, 22, 23, 24, etc
                $subcadenaContrato = substr($idContrato,0,2);   //Obtener los 2 primeros caracteres del idContrato
                //Corresponde al anio actaul o al anio pasado (para contratos elaborados a fin del mes de diciembre)?
                if($subcadenaContrato == $year || $subcadenaContrato == ($year - 1)){
                    //Id contrato contiene el anio actual

                    //Validar formato de telefono ingresado - formato: 333-333-33-33 o 3333333333
                    if(preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{2}-[0-9]{2}$/", $telefonoCliente) || preg_match("/^[0-9]{10}$/", $telefonoCliente)){

                        $existeContrato = DB::select("SELECT c.id, c.telefono, c.estatus_estadocontrato FROM contratos c WHERE c.id = '$idContrato' LIMIT 1");
                        if($existeContrato != null){
                            //Existe el contrato
                            $estadoContrato = $existeContrato[0]->estatus_estadocontrato;
                                //Es venta nueva
                                $telefonoContrato = $existeContrato[0]->telefono;
                                $telefonoCliente = str_replace("-","",$telefonoCliente);
                                if($telefonoContrato == $telefonoCliente){
                                    //Telefono ingresado es igual al telefono registrado en el contrato

                                    //Verificar si es venta nueva o con garantia
                                    $existeGarantia = DB::select("SELECT g.id_historialgarantia FROM garantias g WHERE g.id_contrato = '$idContrato'");
                                    if($existeGarantia == null || ($existeGarantia != null && $estadoContrato == 2)){
                                        //No tiene garantia o es una garantia y estatus de contrato es entregado

                                        $estados = "1,2,7,9,10,11,12";
                                        //Orden de posiciones para rastreo de contrato - orden estatus: 1,9,7,10,11,12,2
                                        $ordenEstatus = [0,3,2,4,5,6,1];

                                    }else{
                                        //Tiene garantia y su estatus es enviado
                                        $estados = "1,7,9,10,11,12";
                                        //Orden de posiciones para rastreo de contrato - orden estatus: 1,9,7,10,11,12
                                        $ordenEstatus = [0,2,1,3,4,5];
                                        //Bandera garantia -> 1
                                        $banderaGarantia = 1;

                                    }

                                    $contratoEstatus = DB::select("SELECT estatus, (SELECT rec.created_at FROM registroestadocontrato rec
                                                                                      WHERE rec.estatuscontrato = ec.estatus
                                                                                      AND rec.id_contrato = '$idContrato' ORDER BY rec.created_at DESC LIMIT 1) AS fecharegistro
                                                                                      FROM estadocontrato ec WHERE ec.estatus IN($estados)");

                                    $rastreoContrato = array();
                                    foreach($ordenEstatus as $orden){
                                        array_push($rastreoContrato, $contratoEstatus[$orden]);
                                    }

                                    //Datos contrato
                                    $datosContrato = DB::select("SELECT c.id, c.nombre, c.telefono, c.calleentrega,
                                                                    c.calle, c.colonia, c.localidad, c.numero, c.fechaentrega,UPPER(c.nombre_usuariocreacion) as usuariocreacion, c.created_at,
                                                                    c.totalreal, c.totalabono,c.totalproducto, c.total, c.id_promocion, c.totalpromocion,
                                                                    (SELECT f.ciudad FROM franquicias f WHERE f.id = c.id_franquicia) as sucursal,
                                                                   (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = '$idContrato' LIMIT 1) as fechaentregaestimada
                                                                   FROM contratos c WHERE c.id = '$idContrato' LIMIT 1");

                                    //Franquicias para modal de agendar citas
                                    $franquicias = DB::select("SELECT f.id, f.ciudad, f.calle, f.entrecalles, f.colonia, f.numero, f.estado, f.whatsapp, f.telefonoatencionclientes, REPLACE(f.coordenadas,' ', '') AS coordenadas
                                                                     FROM franquicias f WHERE f.id != '00000'
                                                                     ORDER BY f.ciudad ASC");

                                    //Historial de credito
                                    $abonos = DB::select("SELECT folio, abono, tipoabono, metodopago, created_at FROM abonos WHERE id_contrato = '$idContrato' ORDER BY created_at DESC ");
                                    $abonosProductos = DB::select("SELECT folio, abono, metodopago, (SELECT p.nombre FROM producto p WHERE p.id = (SELECT cp.id_producto
                                                                                               FROM contratoproducto cp WHERE cp.id_contrato = '$idContrato')) as tipoproducto,
                                                                                               (SELECT p.nombre FROM producto p WHERE p.id = (SELECT cp.id_producto
                                                                                               FROM contratoproducto cp WHERE cp.id_contrato = '$idContrato')) as nombreproducto,
                                                                                               (SELECT p.color FROM producto p WHERE p.id = (SELECT cp.id_producto
                                                                                               FROM contratoproducto cp WHERE cp.id_contrato = '$idContrato')) as colorproducto, created_at
                                                                                               FROM abonos WHERE id_contrato = '$idContrato' AND tipoabono = '7' ORDER BY created_at DESC");
                                    $ultimoAbono = DB::select("SELECT created_at as fechaultimoabono FROM abonos WHERE id_contrato = '$idContrato' AND tipoabono != '7' ORDER BY created_at DESC LIMIT 1");

                                    return view('clientes.contrato.rastrear', ['rastreoContrato' => $rastreoContrato, 'datosContrato' => $datosContrato,
                                        'banderaGarantia' => $banderaGarantia, 'estadoContrato' => $estadoContrato,
                                        'franquicias' => $franquicias, 'ultimoAbono' => $ultimoAbono, 'abonos' => $abonos, 'abonosProductos' => $abonosProductos]);

                                }else{
                                    //No telefono ingresado es diferente al registrado en el contrato
                                    return back()->with('alerta', 'Verifica que el número de teléfono ingresado sea el mismo que registraste cuando se creo el contrato.');
                                }

                        }else{
                            //No existe el contrato
                            return back()->with('alerta', 'Contrato no encontrado.');
                        }

                    }else{
                        //Numero de telefono incorrecto
                        return back()->with('alerta', 'Número de teléfono incorrecto.');
                    }
                }else{
                    //No es in Id valido o puede ser uno mas antiguo - por lo tanto no puede tener rastreo debido que ya debio ser entregado
                    return back()->with('alerta', 'Verifica el número de contrato, debe iniciar con los dos ultimos digitos del año actual.');
                }

            }else{
                //No es un Id valido
                return back()->with('alerta', 'Numero contrato incorrecto. Debe conetener 14 digitos.');
            }
    }

    public function listavisitanosucursales(){

        //Obtener sucursales
        $franquicias = DB::select("SELECT f.id, f.ciudad, f.calle, f.entrecalles, f.colonia, f.numero, f.estado, f.whatsapp, f.telefonoatencionclientes, REPLACE(f.coordenadas,' ', '') AS coordenadas
                                         FROM franquicias f WHERE f.id != '00000'
                                         ORDER BY f.ciudad ASC");

        return view('clientes.visitanos', ['franquicias' => $franquicias]);
    }

    public function filtrarSucursales(Request $request){
        //TIPO FILTRO DE SUCURSALES
        //Barra de busqueda -> Filtra por la cadena introducida en la barra de busqueda de la vista
        //Sucursal mas cercana -> Filtro por el boton "Ir a mi ubicacion" muestra sucursal o sucursales cercanas a tu ubicacion actual

        $tipoFiltro = $request->input("tipoFiltro");
        $idFranquicia = $request->input("idFranquicia");
        $franquicias = null;

        switch ($tipoFiltro){
            case "Barra de busqueda":

                $ciudad = $request->input("ciudad");
                $estado = $request->input("estado");
                $cadenaFiltro = "";

                if($ciudad != null || $estado != null){
                    //Se escribio algo en el filtro
                    if($ciudad != null){
                        $cadenaFiltro = "f.ciudad like '%$ciudad%'";
                    }
                    if($estado != null){
                        if($ciudad != null){
                            $cadenaFiltro = $cadenaFiltro . " OR ";
                        }
                        $cadenaFiltro = $cadenaFiltro . " f.estado like '%$estado%' ";
                    }

                    $franquicias = DB::select("SELECT f.id, f.ciudad, f.calle, f.entrecalles, f.colonia, f.numero, f.estado, f.whatsapp, f.telefonoatencionclientes, REPLACE(f.coordenadas,' ', '') AS coordenadas
                                         FROM franquicias f WHERE f.id != '00000' AND (" . $cadenaFiltro .")
                                         ORDER BY f.indice ASC");
                }else{
                    //Filtro vacio
                    $franquicias = DB::select("SELECT f.id, f.ciudad, f.calle, f.entrecalles, f.colonia, f.numero, f.estado, f.whatsapp, f.telefonoatencionclientes, REPLACE(f.coordenadas,' ', '') AS coordenadas
                                         FROM franquicias f WHERE f.id != '00000' ORDER BY f.indice ASC");
                }


                break;

            case "Sucursal mas cercana":
                $latitud = $request->input("latitud");
                $longitud = $request->input("longitud");
                $radioBusqueda = 50;    //Radio en kilometros para buscar sucursales cerca a tu ubicacion

                $franquicias = array();
                $listaSucursales = DB::select("SELECT f.id, f.ciudad, f.calle, f.entrecalles, f.colonia, f.numero, f.estado, f.whatsapp, f.telefonoatencionclientes, REPLACE(f.coordenadas,' ', '') AS coordenadas
                                         FROM franquicias f WHERE f.id != '00000' ORDER BY f.indice ASC");
                //Recorrer lista de sucursales para verificar cuales estan dnetro del radio de tus coordenadas
                foreach ($listaSucursales as $sucursal) {
                    //Verificar que la franquicia tenga coordenadas
                    if($sucursal->coordenadas != null){
                       $ubicacion = explode(",",$sucursal->coordenadas);
                       //Enviar ubicacion actual y ubicacion de sucursal en BD para obtener distancia en km entre ellas
                       $distanciaSucursal = self::convertirCoordenadasAdistancia($latitud, $longitud, $ubicacion[0], $ubicacion[1]);
                       //Sucursal esta dentro del radio de busqueda?
                       if($distanciaSucursal <= $radioBusqueda){
                           //Distancia sucursal esta dentro del radio de mi ubicacion
                           array_push($franquicias, $sucursal);
                       }
                    }
                }

                break;
        }

        $view = view('clientes.listasucursalesvisitar', ['franquicias' => $franquicias, 'idFranquicia' => $idFranquicia ])->render();

        return \Response::json(array("valid"=>"true","view"=>$view, 'franquicias' => $franquicias));
    }

    function convertirCoordenadasAdistancia($latitudActual, $longitudActual, $latitudSucursal, $longitudSucursal){
        //Si longitud es un valor negativo - Invertir para obtener distancia correcta
        $theta = $longitudActual - $longitudSucursal;
        $distanciaKM = sin(deg2rad($latitudActual)) * sin(deg2rad($latitudSucursal)) + cos(deg2rad($latitudActual)) * cos(deg2rad($latitudSucursal)) * cos(deg2rad($theta));

        $distanciaKM = acos($distanciaKM);
        $distanciaKM = rad2deg($distanciaKM);
        $distanciaKM = $distanciaKM * 60 * 1.1515;
        //Convertir distancia km
        $distanciaKM = $distanciaKM * 1.609344;
        //Redondear distancia a solo 2 decimales y retornar
        return (round($distanciaKM,2));

    }
}
