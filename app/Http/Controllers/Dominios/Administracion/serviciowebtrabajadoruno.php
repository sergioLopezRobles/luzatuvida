<?php

namespace App\Http\Controllers\Dominios\Administracion;

use App\Clases\contratosGlobal;
use App\Clases\globalesServicioWeb;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class serviciowebtrabajadoruno extends Controller
{
    public function iniciarsesion()
    {

        //\Log::info("iniciarsesion");

        $globalesServicioWeb = new globalesServicioWeb;

        $correo = strtoupper(request("correo"));
        $contrasena = request("contrasena");
        $dispositivo = request("dispositivo");
        $idunico = request("idunico");
        $version = request("version");
        $modelo = request("modelo");
        $versiongradle = request("versiongradle");
        $lenguajetelefono = request("lenguajetelefono");

        try {

            $validarAplicacion = DB::select("SELECT id FROM dispositivos WHERE id = '$dispositivo' AND estatus = 1");

            $datos = [];
            if ($validarAplicacion != null) {//Existe aplicacion?
                //Aplicacion existe

                $usuario = DB::select("SELECT u.id, u.rol_id, u.name, UPPER(u.email) as email, u.password, u.id_zona, u.logueado, u.supervisorcobranza, u.ultimaconexion
                                             FROM users u WHERE UPPER(u.email) = '$correo'");

                if ($usuario != null) { //Usuario exite?
                    //Usuario existe

                    $id_usuario = $usuario[0]->id;
                    $usuariofranquicia = DB::select("SELECT (SELECT f.ciudad FROM franquicias f WHERE f.id = uf.id_franquicia) as sucursal,
                                                                  (SELECT f.telefonoatencionclientes FROM franquicias f WHERE f.id = uf.id_franquicia) as telefonoatencionclientessucursal,
                                                                  (SELECT f.whatsapp FROM franquicias f WHERE f.id = uf.id_franquicia) as whatsapp,
                                                                  uf.id_franquicia
                                                                  FROM usuariosfranquicia uf WHERE uf.id_usuario = '$id_usuario'");

                    if ($usuariofranquicia != null) {
                        //Usuario franquicia existe

                        if ($usuario[0]->rol_id == 12 || $usuario[0]->rol_id == 13 || $usuario[0]->rol_id == 4) {
                            //Usuario admitido

                            if ($usuario[0]->logueado == 1) {
                                //Usuario logueado en la pagina
                                $datos[0]["codigo"] = "yqZKQB8et5w3N7dK3ZZC";
                                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                            } else {

                                if (Hash::check($contrasena, $usuario[0]->password)) { //Validacion credenciales
                                    //Credenciales correctas

                                    $fechaActual = Carbon::now();
                                    $dispositivoActivo = DB::select("SELECT estatus FROM dispositivosusuarios WHERE identificadorunico = '$idunico' AND id_usuario = '$id_usuario'");

                                    if ($dispositivoActivo != null) { //Existe el dispositivo?
                                        //Existe dispositivo

                                        if ($dispositivoActivo[0]->estatus == 1) {
                                            //Dispositivo activado

                                            //Asistencia solo para ventas
                                            $idFranquicia = $usuariofranquicia[0]->id_franquicia;
                                            $ultimaPoliza = null;
                                            if($idFranquicia != "00000"){
                                                //Franquicia usuario de diferente que prueba
                                                $ultimaPoliza = DB::select("SELECT p.id FROM poliza p WHERE p.id_franquicia = '$idFranquicia' ORDER BY p.created_at DESC LIMIT 1");
                                            }

                                            if($ultimaPoliza != null){
                                                $idPoliza = $ultimaPoliza[0]->id;
                                                $asistencia = DB::select("SELECT a.id_tipoasistencia FROM asistencia a WHERE a.id_poliza = '$idPoliza' AND a.id_usuario = '$id_usuario' ORDER BY a.created_at DESC LIMIT 1");
                                            }

                                            if($usuario[0]->rol_id == 4 || (($usuario[0]->rol_id == 12 || $usuario[0]->rol_id == 13) && ($ultimaPoliza == null ||
                                                ($ultimaPoliza != null && ($asistencia == null || ($asistencia != null && ($asistencia[0]->id_tipoasistencia == '1' || $asistencia[0]->id_tipoasistencia == '2'))))))){
                                                //Es rol cobranza o
                                                //Rol asistente u opto pero existe una poliza creada para el registro de su asistencia y cuentan con una falta o retardo
                                                //No existe ninguna poliza creada para la sucursal aun
                                                //Asistente u Opto no cuentan con registro de asistencia en la poliza por un cambio de sucursal

                                                $validarEstatus = DB::select("SELECT ts.tipo as tipo FROM users u INNER JOIN tiposuspension ts ON ts.id = u.estatus WHERE u.id = '" . $usuario[0]->id . "' AND u.estatus IN (0,2)");
                                                if($validarEstatus != null) {
                                                    //Ha sido suspendido el usuario
                                                    $datos[0]["codigo"] = "Q7YuAnPM6ykW51SWmdmu";
                                                }else {
                                                    //No ha sido suspendido el usuario

                                                    $token = Str::random(60); //Token
                                                    DB::delete("DELETE FROM tokenlolatv where usuario_id =" . $usuario[0]->id);
                                                    DB::table("tokenlolatv")->insert(["token" => $token, "usuario_id" => $usuario[0]->id]);
                                                    $datos[0]["codigo"] = "4vdw3EAq7xfyeKVg0NN7";
                                                    $datos[0]["id"] = $id_usuario;
                                                    $datos[0]["usuario"] = $usuario[0]->name;
                                                    $datos[0]["correo"] = $usuario[0]->email;
                                                    $datos[0]["rol"] = $usuario[0]->rol_id;
                                                    $datos[0]["fechaactual"] = Carbon::parse($fechaActual)->format('Y-m-d');
                                                    $datos[0]["id_zona"] = $usuario[0]->id_zona;
                                                    $datos[0]["token"] = $token;
                                                    $datos[0]["sucursal"] = $usuariofranquicia[0]->sucursal;
                                                    $datos[0]["telefonoatencionclientessucursal"] = $usuariofranquicia[0]->telefonoatencionclientessucursal;
                                                    $datos[0]["id_franquicia"] = $usuariofranquicia[0]->id_franquicia;

                                                    $datosFtp = DB::select("SELECT * FROM configuracionmovil ORDER BY created_at DESC LIMIT 1");
                                                    if ($datosFtp != null) {
                                                        //Existen datos ftp
                                                        $datos[0]["ruta_ftp"] = Crypt::decryptString($datosFtp[0]->ruta_ftp);
                                                        $datos[0]["usuario_ftp"] = Crypt::decryptString($datosFtp[0]->usuario_ftp);
                                                        $datos[0]["contrasena_ftp"] = Crypt::decryptString($datosFtp[0]->contrasena_ftp);
                                                        $datos[0]["preciodolar"] = $datosFtp[0]->preciodolar == null ? "" : Crypt::decryptString($datosFtp[0]->preciodolar);
                                                    }
                                                    $datos[0]["whatsapp"] = $usuariofranquicia[0]->whatsapp;

                                                    $contratosGlobal = new contratosGlobal();
                                                    $abonoMinimoSemanal = $contratosGlobal::calculoCantidadFormaDePago($usuariofranquicia[0]->id_franquicia, 1);
                                                    $datos[0]["abonominimosemanal"] = $abonoMinimoSemanal == null ? 200 : $abonoMinimoSemanal;
                                                    $datos[0]["supervisorcobranza"] = $usuario[0]->supervisorcobranza;
                                                    $datos[0]["ultimaconexion"] =  Carbon::parse(Carbon::now())->format("Y-m-d H:i:s");

                                                    DB::table("users")->where("id", "=", $usuario[0]->id)->update([
                                                        "logueado" => 2
                                                    ]);

                                                    //Validacion de archivo de movimientos
                                                    $globalesServicioWeb::validacionAbonosArchivo(request("datosarchivo"), $id_usuario);

                                                    //Rol de ASISTENTE/OPTO
                                                    if ($usuario[0]->rol_id == 12 || $usuario[0]->rol_id == 13) {
                                                        //Validacion de archivo historiales clinicos
                                                        $globalesServicioWeb::validacionHistorialesClinicosContratos(request("datosarchivohistorialesclinicos"));
                                                    }

                                                    //\Log::info("{'datos': '" . base64_encode(json_encode($datos)) . "'}");

                                                }

                                                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                                            }
                                            //Es un rol de asisten u opto y no cuenta con asistencia o retardo
                                            $datos[0]["codigo"] = "CSrOppxysdlvvd1JhEsF";
                                            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                                        }

                                        //Dispositivo activado
                                        $datos[0]["codigo"] = "Ppts8qWkkqosQQqRKlMz";
                                        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                                    }

                                    //No existe dispositivo
                                    DB::table("dispositivosusuarios")->insert([
                                        "id_usuario" => $usuario[0]->id,
                                        "versionandroid" => $version,
                                        "modelo" => $modelo,
                                        "identificadorunico" => $idunico,
                                        "versiongradle" => $versiongradle,
                                        "lenguajetelefono" => $lenguajetelefono,
                                        "created_at" => $fechaActual]);
                                    $datos[0]["codigo"] = "Ppts8qWkkqosQQqRKlMz";
                                    return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";;

                                }

                                //Credenciales incorrectas
                                $datos[0]["codigo"] = "5Gn6oZ7QUFxT4uDULhAB";
                                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                            }

                        }

                        //Usuario no admitido
                        $datos[0]["codigo"] = "swYbf6Diq6DiRS67lXaA";
                        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                    }

                    //Usuario franquicia no existe
                    $datos[0]["codigo"] = "eQiNoYUY0qV4dSlaHMMf";
                    return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                }

                //Usuario no existe
                $datos[0]["codigo"] = "5Gn6oZ7QUFxT4uDULhAB";
                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

            } else {

                //Aplicacion no existe
                $appactual = DB::select("SELECT apk FROM dispositivos WHERE estatus = '1'");
                $datos[0]["codigo"] = "ozpw6GLnvbCMpL2QjIQl";
                $datos[0]["appactual"] = asset($appactual[0]->apk);
                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

            }

        }catch(\Exception $e){
            \Log::info("Error: serviciowebtrabajadoruno: (iniciarsesion) - correo: " . $correo . "\n" . $e);
        }

    }

    public function sincronizarcero()
    { //Esta funcion se mandara a llamar cuando se tiene el token y cuando se hacen cambios constantes en las tablas

        //\Log::info("Sincronizar0");

        $globalesServicioWeb = new globalesServicioWeb;

        $fechaActual = Carbon::now();

        $token = request("token");
        $dispositivo = request("dispositivo");
        $idunico = request("idunico");
        $version = request("version");
        $modelo = request("modelo");

        $validarToken = DB::select("SELECT * FROM tokenlolatv WHERE token = '$token'"); //Validamos si el token es valido

        $tokenValido = false;
        $id_usuario = "";
        if ($validarToken != null) {
            $tokenValido = true;
            $id_usuario = $validarToken[0]->usuario_id;
        }

        try {

            $validarAplicacion = DB::select("SELECT id FROM dispositivos WHERE id = '$dispositivo' AND estatus = 1");
            $dispositivoActivo = DB::select("SELECT estatus FROM dispositivosusuarios WHERE identificadorunico = '$idunico' AND id_usuario = '$id_usuario'");

            if ($validarAplicacion != null) { //Existe aplicacion?
                //Aplicacion existe

                if ($dispositivoActivo != null) { //Existe el dispositivo usuario?
                    //Existe dispositivo usuario

                    if ($dispositivoActivo[0]->estatus == 1) {
                        //Dispositivo activado

                        if ($tokenValido) {
                            //Token valido
                            $jsonDatos = request("datos");
                            $usuario = DB::select("SELECT uf.id_franquicia, u.id, u.rol_id FROM users u INNER JOIN usuariosfranquicia uf ON u.id = uf.id_usuario
                                                            WHERE u.id = '" . $validarToken[0]->usuario_id . "'");

                            //Mandamos el jsonDatos y obtenemos el id de la franquicia del usuario logueado
                            $respuesta = $globalesServicioWeb::insertarOModificarRegistrosTablas($jsonDatos, $usuario[0]->id_franquicia, $usuario[0]->id, $idunico, $modelo);

                            //Validacion de archivo de movimientos
                            $globalesServicioWeb::validacionAbonosArchivo(request("datosarchivo"), $usuario[0]->id);

                            //Rol de ASISTENTE/OPTO
                            if($usuario[0]->rol_id == 12 || $usuario[0]->rol_id == 13){
                                //Validacion de archivo historiales clinicos
                                $globalesServicioWeb::validacionHistorialesClinicosContratos(request("datosarchivohistorialesclinicos"));
                            }

                            return $respuesta;
                        }

                        //Token no valido
                        $datos[0]["codigo"] = "P1zv7ZFD8dOvOTQVUTys";
                        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                    }

                    //No esta activado
                    $datos[0]["codigo"] = "Ppts8qWkkqosQQqRKlMz";
                    return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                }

                //No existe dispositivo usuario
                $datos[0]["codigo"] = "P1zv7ZFD8dOvOTQVUTys";
                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

            } else {
                //Aplicacion no existe

                if ($dispositivoActivo != null) { //Existe el dispositivo usuario?
                    //Existe el dispositivo usuario

                    if ($dispositivoActivo[0]->estatus == 1) { //Activado

                        if ($tokenValido) {
                            //Token es valido
                            $jsonDatos = request("datos");

                            $usuario = DB::select("SELECT uf.id_franquicia, u.id FROM users u INNER JOIN usuariosfranquicia uf ON u.id = uf.id_usuario
                                                            WHERE u.id = '" . $validarToken[0]->usuario_id . "'");

                            //Mandamos el jsonDatos y obtenemos el id de la franquicia del usuario logueado
                            $globalesServicioWeb::insertarOModificarRegistrosTablas($jsonDatos, $usuario[0]->id_franquicia, $usuario[0]->id, $idunico, $modelo);

                            //Validacion de archivo de movimientos
                            $globalesServicioWeb::validacionAbonosArchivo(request("datosarchivo"), $usuario[0]->id);

                            $datos[0]["codigo"] = "AUeYOgyZnyTAJXJMA59u";
                            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                        }

                        //Token no es valido
                        $datos[0]["codigo"] = "P1zv7ZFD8dOvOTQVUTys";
                        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                    }

                    //No esta activado
                    $datos[0]["codigo"] = "Ppts8qWkkqosQQqRKlMz";
                    return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                }

                //No existe el dispositivo usuario
                $datos[0]["codigo"] = "P1zv7ZFD8dOvOTQVUTys";
                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

            }

        }catch(\Exception $e){
            \Log::info("Error: serviciowebtrabajadoruno: (sincronizarcero) - id_usuario: " . $id_usuario . "\n" . $e);
        }

    }

    public function sincronizaruno()
    { //Esta funcion se mandara a llamar cuando se tiene el token y ademas la version de aplicacion que tiene el usuario ya noes valido.

        $globalesServicioWeb = new globalesServicioWeb;

        $token = request("token");
        $correo = request("correo");
        $contrasena = request("contrasena");
        $jsonDatos = request("datos");

        $validarToken = DB::select("SELECT * FROM tokenlolatv WHERE token = '$token'"); //Validamos si el token es valido
        $tokenValido = false;
        if ($validarToken != null) {
            $tokenValido = true;
        }

        if ($tokenValido) {
            $usuario = DB::select("SELECT u.id, u.password, uf.id_franquicia FROM users u INNER JOIN usuariosfranquicia uf ON u.id = uf.id_usuario WHERE u.email = '$correo'");
            if ($usuario != null) {
                if (Hash::check($contrasena, $usuario[0]->password)) {
                    if ($validarToken[0]->usuario_id == $usuario[0]->id) { // Validamos que el token corresponda al mismo usuario

                        //Mandamos el jsonDatos y obtenemos el id de la franquicia del usuario logueado
                        $respuesta = $globalesServicioWeb::insertarOModificarRegistrosTablas($jsonDatos, $usuario[0]->id_franquicia);
                        return $respuesta;

                    }
                }
            }

        }

    }

    public function sincronizardos()
    { //Funcion para mandar la informacion desde la pagina web hacia la aplicacion movil.
        //\Log::info("Sincronizar2");

        $token = request("token");
        $tokenValido = DB::select("SELECT usuario_id FROM tokenlolatv WHERE token = '$token'");
        if ($tokenValido != null) {// Existe el token
            //El token es valido

            try {

                $usuario = DB::select("SELECT uf.id_franquicia, u.name, u.rol_id, u.id_zona, u.id FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                WHERE u.id = '" . $tokenValido[0]->usuario_id . "'");
                $idFranquicia = $usuario[0]->id_franquicia; //Obtenemos el id de la franquicia del usuario logueado

                $globalesServicioWeb = new globalesServicioWeb;

                $mensajes = null;
                $contratos = null;
                $historialesClinicos = null;
                $abonos = array();
                $productosDeContrato = null;
                $promocionContrato = array();
                $productos = null;
                $usuarios = null;
                $zonas = null;
                $promociones = null;
                $paquetes = null;
                $tratamientos = null;
                $numContratosVacios = null;
                $contratosnuevos = null;
                $garantias = null;
                $ruta = null;
                $totalcontratosconabonos = null;
                $jsonabonosgeneral = null;
                $contratosliosfugas = null;
                $contratospagoatrasado = null;
                $historialessinconversion = null;
                $ventas = array();
                $configuracionmovil = array();
                $colonias = null;
                $tratamientoscolores = null;
                $imagenescontratoservidor = null;
                $contratosListaNegra = null;
                $notasCobranza = null;
                $asistencia = null;
                $agendaCitasPacientes = null;

                $now = Carbon::parse();
                //$now = Carbon::parse("2022-02-12");
                $nowParse = Carbon::parse($now)->format('Y-m-d');

                $mensajes = DB::select(
                    "SELECT IFNULL(id, '') as id,
                            IFNULL(descripcion, '') as descripcion,
                            IFNULL(fechalimite, '') as fechalimite,
                            IFNULL(intentos, '') as intentos
                            FROM mensajes
                            WHERE id_franquicia = '$idFranquicia'");

                $validacionPrecioProductosArmazon = " ";
                if ($usuario[0]->rol_id == 4) {
                    //Cobranza
                    $validacionPrecioProductosArmazon = " AND precio IS NOT NULL";
                }

                $productos = DB::select(
                    "SELECT IFNULL(id, '') as id,
                            IFNULL(id_tipoproducto, '') as id_tipoproducto,
                            IFNULL(nombre, '') as nombre,
                            IFNULL(piezas, '') as piezas,
                            IFNULL(color, '') as color,
                            IFNULL(precio, '') as precio,
                            IFNULL(activo, '') as activo,
                            IFNULL(preciop, '') as preciop,
                            IFNULL(premium, '0') as premium
                            FROM producto
                            WHERE estado = 1
                            AND ((id_tipoproducto != 1
                                AND id_franquicia = '$idFranquicia')
                                OR (id_tipoproducto = 1
                                    " . $validacionPrecioProductosArmazon . "))");

                //Validacion de archivo de movimientos
                $globalesServicioWeb::validacionAbonosArchivo(request("datosarchivo"), $usuario[0]->id);

                if ($usuario[0]->rol_id == 12 || $usuario[0]->rol_id == 13) {
                    //Si es un optometrista o un asistente

                    //validacion de archivo historiales clinicos
                    $globalesServicioWeb::validacionHistorialesClinicosContratos(request("datosarchivohistorialesclinicos"));

                    $numContratosVacios = DB::select("SELECT COUNT(id) as totalids
                        FROM contratos WHERE id_franquicia = '$idFranquicia' AND id_usuariocreacion = '" . $tokenValido[0]->usuario_id . "' AND datos = '0'");

                    $numContratosACrear = 20 - $numContratosVacios[0]->totalids;
                    if ($numContratosACrear > 0) {
                        //Se necesita crear mas contratos perzonalizados

                        $anoActual = Carbon::now()->format('y'); //Obtener los ultimos 2 digitos del año 21, 22, 23, 24, etc

                        //Obtener indice de la franquicia
                        $franquicia = DB::select("SELECT indice FROM franquicias WHERE id = '$idFranquicia'");
                        $identificadorFranquicia = "";
                        if ($franquicia != null) {
                            //Existe franquicia
                            $identificadorFranquicia = $globalesServicioWeb::obtenerIdentificadorFranquicia($franquicia[0]->indice);
                        }
                        $identificadorFranquicia = $anoActual . $identificadorFranquicia . $globalesServicioWeb::obtenerIdentificadorUsuario($tokenValido[0]->usuario_id); //2200100001, 2200200001, etc

                        //Obtener el ultimo id generado en la tabla de contrato
                        $contratoSelect = DB::select("SELECT id FROM contratos
                                                                WHERE id_franquicia = '$idFranquicia'
                                                                AND id LIKE '%$identificadorFranquicia%'
                                                                AND LENGTH (id) = 14 ORDER BY id DESC LIMIT 1");
                        if ($contratoSelect != null) {
                            //Existe registro (Significa que ya hay contratos personalizados creados)
                            $idContrato = substr($contratoSelect[0]->id, -4);
                            $ultimoIdContratoPerzonalizado = $idContrato;
                        }else {
                            //Sera el primer contrato perzonalizado a crear de la sucursal
                            $ultimoIdContratoPerzonalizado = 0;
                        }

                        //Recorrido de contratos a crear
                        for ($i = 0; $i < $numContratosACrear; $i++) {
                            $arrayRespuesta = $globalesServicioWeb::generarIdContratoPersonalizado($identificadorFranquicia, $ultimoIdContratoPerzonalizado);
                            DB::table("contratos")->insert(["id" => $arrayRespuesta[0], "id_franquicia" => $idFranquicia, "id_usuariocreacion" => $tokenValido[0]->usuario_id,
                                "nombre_usuariocreacion" => $usuario[0]->name, "poliza" => null]);
                            $ultimoIdContratoPerzonalizado = $arrayRespuesta[1] + 1;
                        }

                    }

                    $contratosnuevos = DB::select(
                        "SELECT IFNULL(id, '') as id,
                            IFNULL(datos, '') as datos,
                            IFNULL(id_usuariocreacion, '') as id_usuariocreacion,
                            IFNULL(nombre_usuariocreacion, '') as nombre_usuariocreacion,
                            IFNULL(id_zona, '') as id_zona,
                            IFNULL(estatus, '') as estatus, IFNULL(nombre, '') as nombre,
                            IFNULL(calle, '') as calle,
                            IFNULL(numero, '') as numero,
                            IFNULL(depto, '') as depto,
                            IFNULL(alladode, '') as alladode,
                            IFNULL(frentea, '') as frentea,
                            IFNULL(entrecalles, '') as entrecalles,
                            IFNULL(colonia, '') as colonia,
                            IFNULL(localidad, '') as localidad,
                            IFNULL(telefono, '') as telefono,
                            IFNULL(telefonoreferencia, '') as telefonoreferencia,
                            IFNULL(correo, '') as correo,
                            IFNULL(nombrereferencia, '') as nombrereferencia,
                            IFNULL(casatipo, '') as casatipo,
                            IFNULL(casacolor, '') as casacolor,
                            IFNULL(fotoine, '') as fotoine,
                            IFNULL(fotocasa, '') as fotocasa,
                            IFNULL(comprobantedomicilio, '') as comprobantedomicilio,
                            IFNULL(pagare, '') as pagare,
                            IFNULL(fotootros, '') as fotootros,
                            IFNULL(pagosadelantar, '') as pagosadelantar,
                            IFNULL(created_at, '') as created_at,
                            IFNULL(updated_at, '') as updated_at,
                            IFNULL(id_optometrista, '') as id_optometrista,
                            IFNULL(tarjeta, '') as tarjeta,
                            IFNULL(pago, '') as pago,
                            IFNULL(id_promocion, '') as id_promocion,
                            IFNULL(fotoineatras, '') as fotoineatras,
                            IFNULL(tarjetapensionatras, '') as tarjetapensionatras,
                            IFNULL(total, '') as total,
                            IFNULL(idcontratorelacion, '') as idcontratorelacion,
                            IFNULL(contador, '') as contador,
                            IFNULL(totalhistorial, '') as totalhistorial,
                            IFNULL(totalpromocion, '') as totalpromocion,
                            IFNULL(totalproducto, '') as totalproducto,
                            IFNULL(totalabono, '') as totalabono,
                            IFNULL(fechaatraso, '') as fechaatraso,
                            IFNULL(costoatraso, '') as costoatraso,
                            IFNULL(ultimoabono, '') as ultimoabono,
                            IFNULL(estatus_estadocontrato, '') as estatus_estadocontrato,
                            IFNULL(diapago, '') as diapago,
                            IFNULL(fechacobroini, '') as fechacobroini,
                            IFNULL(fechacobrofin, '') as fechacobrofin,
                            IFNULL(enganche, '') as enganche,
                            IFNULL(entregaproducto, '') as entregaproducto,
                            IFNULL(diaseleccionado, '') as diaseleccionado,
                            IFNULL(fechaentrega, '') as fechaentrega,
                            IFNULL(promocionterminada, '') as promocionterminada,
                            IFNULL(subscripcion, '') as subscripcion,
                            IFNULL(fechasubscripcion, '') as fechasubscripcion,
                            IFNULL(nota, '') as nota,
                            IFNULL(totalreal, '') as totalreal,
                            IFNULL(diatemporal, '') as diatemporal,
                            IFNULL(coordenadas, '') as coordenadas,
                            IFNULL(calleentrega, '') as calleentrega,
                            IFNULL(numeroentrega, '') as numeroentrega,
                            IFNULL(deptoentrega, '') as deptoentrega,
                            IFNULL(alladodeentrega, '') as alladodeentrega,
                            IFNULL(frenteaentrega, '') as frenteaentrega,
                            IFNULL(entrecallesentrega, '') as entrecallesentrega,
                            IFNULL(coloniaentrega, '') as coloniaentrega,
                            IFNULL(localidadentrega, '') as localidadentrega,
                            IFNULL(casatipoentrega, '') as casatipoentrega,
                            IFNULL(casacolorentrega, '') as casacolorentrega,
                            IFNULL(abonominimo, '') as abonominimo,
                            IFNULL(alias, '') as alias,
                            IFNULL(opcionlugarentrega, '') as opcionlugarentrega,
                            IFNULL(observacionfotoine, '') as observacionfotoine,
                            IFNULL(observacionfotoineatras, '') as observacionfotoineatras,
                            IFNULL(observacionfotocasa, '') as observacionfotocasa,
                            IFNULL(observacioncomprobantedomicilio, '') as observacioncomprobantedomicilio,
                            IFNULL(observacionpagare, '') as observacionpagare,
                            IFNULL(observacionfotootros, '') as observacionfotootros
                            FROM contratos
                            WHERE id_franquicia = '$idFranquicia'
                            AND id_usuariocreacion = '" . $tokenValido[0]->usuario_id . "'
                            AND datos = '0'");

                    $contratos = DB::select("SELECT DISTINCT id,
                                                    datos,
                                                    id_usuariocreacion,
                                                    nombre_usuariocreacion,
                                                    id_zona,
                                                    estatus,
                                                    nombre,
                                                    calle,
                                                    numero,
                                                    depto,
                                                    alladode,
                                                    frentea,
                                                    entrecalles,
                                                    colonia,
                                                    localidad,
                                                    telefono,
                                                    telefonoreferencia,
                                                    correo,
                                                    nombrereferencia,
                                                    casatipo,
                                                    casacolor,
                                                    fotoine,
                                                    fotocasa,
                                                    comprobantedomicilio,
                                                    pagare,
                                                    fotootros,
                                                    pagosadelantar,
                                                    id_optometrista,
                                                    tarjeta,
                                                    pago,
                                                    id_promocion,
                                                    fotoineatras,
                                                    tarjetapensionatras,
                                                    total,
                                                    idcontratorelacion,
                                                    contador,
                                                    totalhistorial,
                                                    totalpromocion,
                                                    totalproducto,
                                                    totalabono,
                                                    fechaatraso,
                                                    costoatraso,
                                                    ultimoabono,
                                                    estatus_estadocontrato,
                                                    diapago,
                                                    fechacobroini,
                                                    fechacobrofin,
                                                    enganche,
                                                    entregaproducto,
                                                    diaseleccionado,
                                                    fechaentrega,
                                                    promocionterminada,
                                                    subscripcion,
                                                    fechasubscripcion,
                                                    nota,
                                                    totalreal,
                                                    diatemporal,
                                                    coordenadas,
                                                    nombrepaquete,
                                                    ultimoabonoreal,
                                                    titulopromocion,
                                                    calleentrega,
                                                    numeroentrega,
                                                    deptoentrega,
                                                    alladodeentrega,
                                                    frenteaentrega,
                                                    entrecallesentrega,
                                                    coloniaentrega,
                                                    localidadentrega,
                                                    casatipoentrega,
                                                    casacolorentrega,
                                                    abonominimo,
                                                    alias,
                                                    autorizacion,
                                                    opcionlugarentrega,
                                                    observacionfotoine,
                                                    observacionfotoineatras,
                                                    observacionfotocasa,
                                                    observacioncomprobantedomicilio,
                                                    observacionpagare,
                                                    observacionfotootros,
                                                    created_at,
                                                    updated_at
                                                    FROM contratostemporalessincronizacion WHERE id_usuario = '" . $usuario[0]->id . "'");

                    //Obtener imagenes del contratato en estatus NO TERMINADO
                    $imagenescontratoservidor = DB::select("SELECT IFNULL(id, '') as id,
                                                                   IFNULL(fotoine, '') as fotoine,
                                                                   IFNULL(fotoineatras, '') as fotoineatras,
                                                                   IFNULL(fotocasa, '') as fotocasa,
                                                                   IFNULL(comprobantedomicilio, '') as comprobantedomicilio,
                                                                   IFNULL(pagare, '') as pagare,
                                                                   IFNULL(fotootros, '') as fotootros
                                                                   FROM contratostemporalessincronizacion
                                                                   WHERE id_usuario = '" . $usuario[0]->id . "' AND estatus_estadocontrato = '0'");

                    if($imagenescontratoservidor != null){
                        $imagenescontratoservidor = self::verificarExisteImagenContratoEnServidor($imagenescontratoservidor);
                    }

                    $usuarios = DB::select(
                        "SELECT IFNULL(u.id, '') as id,
                            IFNULL(u.rol_id, '') as rol_id,
                            IFNULL(name, '') as name
                            FROM users u
                            INNER JOIN usuariosfranquicia uf ON u.id = uf.id_usuario
                            WHERE u.rol_id = 12
                            AND uf.id_franquicia = '$idFranquicia'");

                    $zonas = DB::select(
                        "SELECT IFNULL(id, '') as id,
                            IFNULL(zona, '') as zona
                            FROM zonas
                            WHERE id_franquicia = '$idFranquicia'");

                    $promociones = DB::select(
                        "SELECT IFNULL(id, '') as id,
                            IFNULL(titulo, '') as titulo,
                            IFNULL(precioP, '') as precioP,
                            IFNULL(inicio, '') as inicio,
                            IFNULL(fin, '') as fin,
                            IFNULL(status, '') as status,
                            IFNULL(asignado, '') as asignado,
                            IFNULL(id_tipopromocionusuario, '') as id_tipopromocionusuario,
                            IFNULL(contado, '') as contado,
                            IFNULL(armazones, '') as armazones,
                            IFNULL(tipopromocion, '') as tipopromocion,
                            IFNULL(preciouno, '') as preciouno
                            FROM promocion
                            WHERE id_franquicia = '$idFranquicia'
                            AND status = '1'
                            AND id_tipopromocionusuario = '0'");

                    $paquetes = DB::select(
                        "SELECT IFNULL(id, '') as id,
                            IFNULL(nombre, '') as nombre,
                            IFNULL(precio, '') as precio
                            FROM paquetes
                            WHERE id_franquicia = '$idFranquicia'");

                    $tratamientos = DB::select(
                        "SELECT IFNULL(id, '') as id,
                            IFNULL(nombre, '') as nombre,
                            IFNULL(precio, '') as precio
                            FROM tratamientos
                            WHERE id_franquicia = '$idFranquicia'");

                    $contratosliosfugas = DB::select(
                        "SELECT IFNULL(clf.id_contrato, '') AS id,
                            IFNULL(clf.nombre, '') AS nombre,
                            IFNULL(clf.colonia, '') AS colonia,
                            IFNULL(clf.calle, '') AS calle,
                            IFNULL(clf.numero, '') AS numero,
                            IFNULL(clf.telefono, '') AS telefono,
                            IFNULL(clf.cambios, '') AS cambios
                            FROM contratosliofuga clf");

                    $contratospagoatrasado = DB::select(
                        "SELECT IFNULL(c.id, '') AS id,
                            IFNULL(c.nombre, '') AS nombre,
                            IFNULL(c.colonia, '') AS colonia,
                            IFNULL(c.calle, '') AS calle,
                            IFNULL(c.numero, '') AS numero,
                            IFNULL(c.telefono, '') AS telefono,
                            'PAGO ATRASADO' AS cambios
                            FROM contratos c
                            WHERE c.id_franquicia = '$idFranquicia'
                            AND c.estatus_estadocontrato = '4'");

                    $contratosliosfugas = array_merge($contratosliosfugas,$contratospagoatrasado);

                    $historialessinconversion = $globalesServicioWeb::obtenerHistorialesSinConversionContratos($contratos);

                    //Obtenemos el Optometrista y Asistente con mas ventas en la semana
                    $ventas = $globalesServicioWeb::obtenerVentas($now,$nowParse);

                    $abonos = $globalesServicioWeb::obtenerAbonosContratos($contratos);

                    $colonias = $globalesServicioWeb::validacionBanderaColonias(request("banderacolonias"), $idFranquicia);

                    $tratamientoscolores =DB::select("SELECT tc.indice, tc.id_tratamiento, tc.color, tc.created_at FROM tratamientoscolores tc
                                                            WHERE tc.id_franquicia = '$idFranquicia' ORDER BY tc.color ASC");

                    $ultimaPoliza = DB::select("SELECT p.id, p.created_at FROM poliza p WHERE p.id_franquicia = '$idFranquicia' ORDER BY p.created_at DESC LIMIT 1");
                    $idPoliza = ($ultimaPoliza !=null)? $ultimaPoliza[0]->id : "" ;
                    $asistencia = DB::select("SELECT a.id_usuario, UPPER(u.name) AS nombre, a.id_tipoasistencia as asistencia,
                                                    IFNULL(a.registrosalida,'') as registrosalida  FROM asistencia a
                                                    INNER JOIN users u ON u.id = a.id_usuario
                                                    WHERE a.id_poliza = '$idPoliza' AND u.rol_id IN (12,13)");

                    $agendaCitasPacientes = DB::select("SELECT IFNULL(ac.indice, '') as indice,
                                                             IFNULL(ac.fechacitaagendada, '') as fechacitaagendada,
                                                             IFNULL(ac.horacitaagendada, '') as horacitaagendada,
                                                             IFNULL(ac.nombre, '') as nombre,
                                                             IFNULL(ac.email, '') as email,
                                                             IFNULL(ac.telefono, '') as telefono,
                                                             IFNULL(ac.tipocita, '') as tipocita,
                                                             IFNULL(ac.otrotipocita, '') as otrotipocita,
                                                             IFNULL(ac.lugarcita, '') as lugarcita,
                                                             IFNULL(ac.localidad, '') as localidad,
                                                             IFNULL(ac.colonia, '') as colonia,
                                                             IFNULL(ac.domicilio, '') as domicilio,
                                                             IFNULL(ac.numero, '') as numero,
                                                             IFNULL(ac.observaciones, '') as observaciones,
                                                             IFNULL(ac.entrecalles, '') as entrecalles,
                                                             IFNULL(ac.created_at, '') as created_at
                                                             FROM agendacitas ac
                                                             WHERE ac.id_franquicia = '$idFranquicia' AND ac.estadocita = 0
                                                             AND STR_TO_DATE(ac.fechacitaagendada,'%Y-%m-%d') >= '$nowParse'");

                } elseif ($usuario[0]->rol_id == 4) {
                    //Si el usuario es alguien de cobranza

                    $contratosListaNegra = DB::select("SELECT * FROM contratoslistanegra l
                                                    INNER JOIN contratostemporalessincronizacion cts
                                                    ON l.id_contrato = cts.id
                                                    WHERE cts.id_usuario = '" . $usuario[0]->id . "' ORDER BY l.id_contrato ASC, l.created_at DESC");

                    $contratos = DB::select("SELECT DISTINCT id,
                                                    datos,
                                                    id_usuariocreacion,
                                                    nombre_usuariocreacion,
                                                    id_zona,
                                                    estatus,
                                                    nombre,
                                                    calle,
                                                    numero,
                                                    depto,
                                                    alladode,
                                                    frentea,
                                                    entrecalles,
                                                    colonia,
                                                    localidad,
                                                    telefono,
                                                    telefonoreferencia,
                                                    correo,
                                                    nombrereferencia,
                                                    casatipo,
                                                    casacolor,
                                                    fotoine,
                                                    fotocasa,
                                                    comprobantedomicilio,
                                                    pagare,
                                                    fotootros,
                                                    pagosadelantar,
                                                    id_optometrista,
                                                    tarjeta,
                                                    pago,
                                                    id_promocion,
                                                    fotoineatras,
                                                    tarjetapensionatras,
                                                    total,
                                                    idcontratorelacion,
                                                    contador,
                                                    totalhistorial,
                                                    totalpromocion,
                                                    totalproducto,
                                                    totalabono,
                                                    fechaatraso,
                                                    costoatraso,
                                                    ultimoabono,
                                                    estatus_estadocontrato,
                                                    diapago,
                                                    fechacobroini,
                                                    fechacobrofin,
                                                    enganche,
                                                    entregaproducto,
                                                    diaseleccionado,
                                                    fechaentrega,
                                                    promocionterminada,
                                                    subscripcion,
                                                    fechasubscripcion,
                                                    nota,
                                                    totalreal,
                                                    diatemporal,
                                                    coordenadas,
                                                    nombrepaquete,
                                                    ultimoabonoreal,
                                                    titulopromocion,
                                                    calleentrega,
                                                    numeroentrega,
                                                    deptoentrega,
                                                    alladodeentrega,
                                                    frenteaentrega,
                                                    entrecallesentrega,
                                                    coloniaentrega,
                                                    localidadentrega,
                                                    casatipoentrega,
                                                    casacolorentrega,
                                                    abonominimo,
                                                    alias,
                                                    autorizacion,
                                                    opcionlugarentrega,
                                                    observacionfotoine,
                                                    observacionfotoineatras,
                                                    observacionfotocasa,
                                                    observacioncomprobantedomicilio,
                                                    observacionpagare,
                                                    observacionfotootros,
                                                    created_at,
                                                    updated_at
                                                    FROM contratostemporalessincronizacion WHERE id_usuario = '" . $usuario[0]->id . "'");

                    $arrayContratos = array();
                    //$now = Carbon::parse("2023-05-22");
                    $hoyNumero = $now->dayOfWeekIso; // Comienza en lunes -> 1 y obtenemos el dia actual de la semana
                    $fechaSabadoAntes = $globalesServicioWeb::obtenerDia($now, $hoyNumero, 0); //Obtenemos la fecha del dia sabado anterior
                    $fechaViernesSiguiente = $globalesServicioWeb::obtenerDia($now, $hoyNumero, 1); //Obtenemos la fecha del dia viernes siguiente

                    if ($hoyNumero == 6 || $hoyNumero == 7) {
                        //Es sabado o domingo
                        $fechaLunesAntes = Carbon::parse($fechaSabadoAntes)->subDays(5)->format('Y-m-d');
                        $fechaDomingoSiguiente = Carbon::parse($fechaLunesAntes)->addDays(6)->format('Y-m-d');
                    }else {
                        //Dia diferente a sabado y domingo
                        $fechaLunesAntes = Carbon::parse($fechaSabadoAntes)->addDays(2)->format('Y-m-d');
                        $fechaDomingoSiguiente = Carbon::parse($fechaViernesSiguiente)->addDays(2)->format('Y-m-d');
                    }

                    $arrayAbonosTemporalesEnviar = array();
                    foreach ($contratos as $contrato) {

                        $enviarAbonoTemporal = false;
                        $diaTemporal = $contrato->diatemporal;

                        if ($contrato->estatus_estadocontrato == 12 || $contrato->estatus_estadocontrato == 5) {
                            //ENVIADOS Y LIQUIDADOS
                            if ($diaTemporal != null) { //Tiene dia temporal?
                                //Tiene dia temporal
                                if (Carbon::parse($nowParse)->format('Y-m-d') >= Carbon::parse($diaTemporal)->format('Y-m-d')) {
                                    //Actualizar diatemporal a null
                                    DB::table("contratos")->where("id", "=", $contrato->id)->where("id_franquicia", "=", $idFranquicia)->update([
                                        "diatemporal" => null
                                    ]);
                                    DB::table("contratostemporalessincronizacion")->where("id", "=", $contrato->id)->update([
                                        "diatemporal" => ""
                                    ]);
                                    $contrato->diatemporal = ""; //Mandar vacio el atributo diatemporal al movil
                                    array_push($arrayContratos, $contrato); //Agregar contrato a array
                                    $enviarAbonoTemporal = true;
                                }
                            } else {
                                //No tiene dia temporal
                                array_push($arrayContratos, $contrato); //Agregar contrato a array
                                $enviarAbonoTemporal = true;
                            }
                        } else {
                            //ENTREGADOS Y ABONOS ATRASADOS

                            switch ($contrato->pago) {
                                case 1:
                                    //SEMANAL
                                    if ($contrato->fechacobroini == null && $contrato->fechacobrofin == null) {
                                        //fechacobroini y fechacobrofin son null
                                        if ($diaTemporal != null) { //Tiene dia temporal?
                                            //Tiene dia temporal
                                            if (Carbon::parse($nowParse)->format('Y-m-d') >= Carbon::parse($diaTemporal)->format('Y-m-d')) {
                                                //Actualizar diatemporal a null
                                                DB::table("contratos")->where("id", "=", $contrato->id)->where("id_franquicia", "=", $idFranquicia)->update([
                                                    "diatemporal" => null
                                                ]);
                                                DB::table("contratostemporalessincronizacion")->where("id", "=", $contrato->id)->update([
                                                    "diatemporal" => ""
                                                ]);
                                                $contrato->diatemporal = ""; //Mandar vacio el atributo diatemporal al movil
                                                array_push($arrayContratos, $contrato); //Agregar contrato a array
                                                $enviarAbonoTemporal = true;
                                            }
                                        } else {
                                            //No tiene dia temporal
                                            array_push($arrayContratos, $contrato); //Agregar contrato a array
                                            $enviarAbonoTemporal = true;
                                        }
                                    } else {
                                        //fechacobroini y fechacobrofin son diferente de null
                                        if (Carbon::parse($nowParse)->format('Y-m-d') >= Carbon::parse($contrato->fechacobroini)->format('Y-m-d')
                                            && Carbon::parse($nowParse)->format('Y-m-d') <= Carbon::parse($contrato->fechacobrofin)->format('Y-m-d')) {

                                            if (!$globalesServicioWeb::tieneAbonoEnFechas($contrato->ultimoabonoreal, $contrato->fechacobroini, $contrato->fechacobrofin)) {
                                                //No tiene abono el contrato

                                                if ($diaTemporal != null) { //Tiene dia temporal?
                                                    //Tiene dia temporal
                                                    if (Carbon::parse($nowParse)->format('Y-m-d') >= Carbon::parse($diaTemporal)->format('Y-m-d')) {
                                                        //Actualizar diatemporal a null
                                                        DB::table("contratos")->where("id", "=", $contrato->id)->where("id_franquicia", "=", $idFranquicia)->update([
                                                            "diatemporal" => null
                                                        ]);
                                                        DB::table("contratostemporalessincronizacion")->where("id", "=", $contrato->id)->update([
                                                            "diatemporal" => ""
                                                        ]);
                                                        $contrato->diatemporal = ""; //Mandar vacio el atributo diatemporal al movil
                                                        array_push($arrayContratos, $contrato); //Agregar contrato a array
                                                        $enviarAbonoTemporal = true;
                                                    }
                                                } else {
                                                    //No tiene dia temporal
                                                    array_push($arrayContratos, $contrato); //Agregar contrato a array
                                                    $enviarAbonoTemporal = true;
                                                }

                                            } else {
                                                //Tiene abono el contrato
                                                //Actualizar diatemporal a null
                                                DB::table("contratos")->where("id", "=", $contrato->id)->where("id_franquicia", "=", $idFranquicia)->update([
                                                    "diatemporal" => null
                                                ]);
                                                DB::table("contratostemporalessincronizacion")->where("id", "=", $contrato->id)->update([
                                                    "diatemporal" => ""
                                                ]);
                                            }

                                        }
                                    }
                                    break;
                                default:
                                    //QUICENAL O MENSUAL
                                    if ($contrato->fechacobroini == null && $contrato->fechacobrofin == null) {
                                        //fechacobroini y fechacobrofin son null
                                        if ($diaTemporal != null) { //Tiene dia temporal?
                                            //Tiene dia temporal
                                            if (Carbon::parse($nowParse)->format('Y-m-d') >= Carbon::parse($diaTemporal)->format('Y-m-d')) {
                                                //Actualizar diatemporal a null
                                                DB::table("contratos")->where("id", "=", $contrato->id)->where("id_franquicia", "=", $idFranquicia)->update([
                                                    "diatemporal" => null
                                                ]);
                                                DB::table("contratostemporalessincronizacion")->where("id", "=", $contrato->id)->update([
                                                    "diatemporal" => ""
                                                ]);
                                                $contrato->diatemporal = ""; //Mandar vacio el atributo diatemporal al movil
                                                array_push($arrayContratos, $contrato); //Agregar contrato a array
                                                $enviarAbonoTemporal = true;
                                            }
                                        } else {
                                            //No tiene dia temporal
                                            array_push($arrayContratos, $contrato); //Agregar contrato a array
                                            $enviarAbonoTemporal = true;
                                        }
                                    } else {
                                        //fechacobroini y fechacobrofin son diferente de null
                                        if ((Carbon::parse($contrato->fechacobroini)->format('Y-m-d') >= Carbon::parse($fechaLunesAntes)->format('Y-m-d')
                                                && Carbon::parse($contrato->fechacobroini)->format('Y-m-d') <= Carbon::parse($fechaDomingoSiguiente)->format('Y-m-d'))
                                            || (Carbon::parse($contrato->fechacobrofin)->format('Y-m-d') >= Carbon::parse($fechaLunesAntes)->format('Y-m-d')
                                                && Carbon::parse($contrato->fechacobrofin)->format('Y-m-d') <= Carbon::parse($fechaDomingoSiguiente)->format('Y-m-d'))
                                            || (Carbon::parse($nowParse)->format('Y-m-d') >= Carbon::parse($contrato->fechacobroini)->format('Y-m-d')
                                                && Carbon::parse($nowParse)->format('Y-m-d') <= Carbon::parse($contrato->fechacobrofin)->format('Y-m-d'))
                                            || (Carbon::parse($fechaLunesAntes)->format('Y-m-d') >= Carbon::parse($contrato->fechacobroini)->format('Y-m-d')
                                                && Carbon::parse($fechaDomingoSiguiente)->format('Y-m-d') <= Carbon::parse($contrato->fechacobrofin)->format('Y-m-d'))) {

                                            if (!$globalesServicioWeb::tieneAbonoEnFechas($contrato->ultimoabonoreal, $contrato->fechacobroini, $contrato->fechacobrofin)
                                                && !$globalesServicioWeb::tieneAbonoEnFechas($contrato->ultimoabonoreal, $fechaLunesAntes, $fechaDomingoSiguiente)) {
                                                //No tiene abono el contrato

                                                if ($diaTemporal != null) { //Tiene dia temporal?
                                                    //Tiene dia temporal
                                                    if (Carbon::parse($nowParse)->format('Y-m-d') >= Carbon::parse($diaTemporal)->format('Y-m-d')) {
                                                        //Actualizar diatemporal a null
                                                        DB::table("contratos")->where("id", "=", $contrato->id)->where("id_franquicia", "=", $idFranquicia)->update([
                                                            "diatemporal" => null
                                                        ]);
                                                        DB::table("contratostemporalessincronizacion")->where("id", "=", $contrato->id)->update([
                                                            "diatemporal" => ""
                                                        ]);
                                                        $contrato->diatemporal = ""; //Mandar vacio el atributo diatemporal al movil
                                                        array_push($arrayContratos, $contrato); //Agregar contrato a array
                                                        $enviarAbonoTemporal = true;
                                                    }
                                                } else {
                                                    //No tiene dia temporal
                                                    array_push($arrayContratos, $contrato); //Agregar contrato a array
                                                    $enviarAbonoTemporal = true;
                                                }

                                            } else {
                                                //Tiene abono el contrato
                                                //Actualizar diatemporal a null
                                                DB::table("contratos")->where("id", "=", $contrato->id)->where("id_franquicia", "=", $idFranquicia)->update([
                                                    "diatemporal" => null
                                                ]);
                                                DB::table("contratostemporalessincronizacion")->where("id", "=", $contrato->id)->update([
                                                    "diatemporal" => ""
                                                ]);
                                            }

                                        }
                                    }
                                    break;
                            }

                        }

                        if ($enviarAbonoTemporal) {
                            //enviarAbonoTemporal = true
                            array_push($arrayAbonosTemporalesEnviar, $contrato->id); //Agregar id_contrato a arrayAbonosTemporalesEnviar
                        }

                    }

                    $contratos = $arrayContratos;

                    $usuarios = DB::select("SELECT IFNULL(u.id, '') as id, IFNULL(u.rol_id, '') as rol_id, IFNULL(name, '') as name FROM users u
                                                  INNER JOIN usuariosfranquicia uf ON u.id = uf.id_usuario WHERE u.rol_id = 12 AND uf.id_franquicia = '$idFranquicia'");

                    $ruta = $globalesServicioWeb::obtenerRuta($contratos, $usuario[0]->id);

                    $totalcontratosconabonos = DB::select("SELECT COUNT(c.id) as totalcontratosconabonos FROM contratos c
                                                                 WHERE c.id IN (SELECT a.id_contrato FROM abonos a WHERE a.corte = '0' AND a.id_franquicia = '$idFranquicia'
                                                                 AND a.id_usuario = '" . $usuario[0]->id . "' AND a.tipoabono NOT IN (7) GROUP BY a.id_contrato)");

                    $jsonabonosgeneral = $globalesServicioWeb::obtenerJsonAbonosGeneral($usuario[0]->id);

                    $abonosTemporales = DB::select("SELECT IFNULL(a.id, '') as id, IFNULL(a.folio, '') as folio,
                                                IFNULL(a.id_contrato, '') as id_contrato, IFNULL(a.id_usuario, '') as id_usuario,
                                                IFNULL(a.abono, '') as abono, IFNULL(a.adelantos, '0') as adelantos, IFNULL(a.tipoabono, '') as tipoabono,
                                                IFNULL(a.atraso, '0') as atraso, IFNULL(a.metodopago, '') as metodopago, IFNULL(a.corte, '') as corte, IFNULL(a.coordenadas, '') as coordenadas,
                                                IFNULL(a.created_at, '') as created_at, IFNULL(a.updated_at, '') as updated_at
                                                FROM abonoscontratostemporalessincronizacion a WHERE a.id_usuariocobrador = '" . $usuario[0]->id . "'");

                    foreach ($abonosTemporales as $abonoT) {
                        //Recorrido abonoscontratostemporalessincronizacion
                        if (in_array($abonoT->id_contrato, $arrayAbonosTemporalesEnviar)) {
                            //existe id_contrato en arrayAbonosTemporalesEnviar
                            array_push($abonos, $abonoT); //Agregar abonoT a abonos
                        }
                    }

                    //Notas de cobrador
                    $notasCobranza = DB::select("SELECT * FROM notascobranza nc WHERE nc.id_usuario = '" . $usuario[0]->id . "' ORDER BY nc.created_at DESC");

                }

                $historialesClinicos = $globalesServicioWeb::obtenerHistorialesClinicosContratos($contratos);
                $productosDeContrato = $globalesServicioWeb::obtenerProductosDeContratoContratos($contratos);
                $promocionContrato = $globalesServicioWeb::obtenerPromocionesContratos($contratos);
                $garantias = $globalesServicioWeb::obtenerGarantiasContratos($contratos, $usuario[0]->rol_id, $nowParse);

                $llaves = DB::select("SELECT IFNULL(ll.llave, '') as llave, IFNULL(ll.tipo, '') as tipo FROM llaves ll WHERE ll.id_franquicia = '$idFranquicia'");

                //Obtenemos la configuracion activa para la app movil
                $consulta = "SELECT IFNULL(indice, '') as indice,
                        IFNULL(fotologo, '') as fotologo,
                        IFNULL(coloriconos, '') as coloriconos,
                        IFNULL(colorencabezados, '') as colorencabezados,
                        IFNULL(colornavbar, '') as colornavbar,
                        IFNULL(estadoconfiguracion, '') as estadoconfiguracion
                        FROM temamovil
                        WHERE estadoconfiguracion = 1";

                $configuracionmovil = DB::select($consulta);

                //Desencriptar llaves para que vallan al movil desencriptadas
                foreach ($llaves as $llave) {
                    try {
                        if(strlen($llave->llave) > 0) {
                            //Tiene algo la llave
                            $llave->llave = Crypt::decryptString($llave->llave);
                        }
                    } catch (DecryptException $e) {
                        \Log::info("Error: serviciowebtrabajadoruno llaves tipo: " . $llave->tipo . "\n" . $e->getMessage());
                    }
                }

                //Actualizar fecha de ultima conexion
                $ultimaConexion = Carbon::parse(Carbon::now())->format("Y-m-d H:i:s");
                DB::table("users")->where("id", "=", $tokenValido[0]->usuario_id)->update([
                    "ultimaconexion" => $ultimaConexion
                ]);

                $datos[0]["codigo"] = "4vdw3EAq7xfyeKVg0NN7";
                $datos[0]["mensajes"] = $mensajes;
                $datos[0]["contratosnuevos"] = $contratosnuevos;
                $datos[0]["contratos"] = $contratos;
                $datos[0]["historialesclinicos"] = $historialesClinicos;
                $datos[0]["abonos"] = $abonos;
                $datos[0]["contratosproductos"] = $productosDeContrato;
                $datos[0]["promocioncontratos"] = $promocionContrato;
                $datos[0]["productos"] = $productos;
                $datos[0]["usuarios"] = $usuarios;
                $datos[0]["zonas"] = $zonas;
                $datos[0]["promociones"] = $promociones;
                $datos[0]["paquetes"] = $paquetes;
                $datos[0]["tratamientos"] = $tratamientos;
                $datos[0]["garantias"] = $garantias;
                $datos[0]["ruta"] = $ruta;
                $datos[0]["totalcontratosconabonos"] = $totalcontratosconabonos;
                $datos[0]["jsonabonosgeneral"] = $jsonabonosgeneral;
                $datos[0]["contratosliosfugas"] = $contratosliosfugas;
                $datos[0]["historialessinconversion"] = $historialessinconversion;
                $datos[0]["llaves"] = $llaves;
                $datos[0]["ventas"] = $ventas;
                $datos[0]["configuracionmovil"] = $configuracionmovil;
                $datos[0]["colonias"] = $colonias;
                $datos[0]["tratamientoscolores"] = $tratamientoscolores;
                $datos[0]["imagenescontratoservidor"] = $imagenescontratoservidor;
                $datos[0]["contratoslistanegra"] = $contratosListaNegra;
                $datos[0]["ultimaconexion"] = $ultimaConexion;
                $datos[0]["notascobranza"] = $notasCobranza;
                $datos[0]["asistencia"] = $asistencia;
                $datos[0]["agendacitas"] = $agendaCitasPacientes;

                //\Log::info("{'datos': '" . base64_encode(json_encode($datos)) . "'}");
                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

            }catch(\Exception $e){
                \Log::info("Error: serviciowebtrabajadoruno: (sincronizardos) - id_usuario: " . $tokenValido[0]->usuario_id . "\n" . $e);
            }

        } else {
            //El token no es valido, se debera cerrar sesion.
            $datos[0]["codigo"] = "JGvcYgZn8PD4KpIm4uIN";
            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
        }
    }

    public function cerrarsesion()
    {

        //\Log::info("cerrarsesion");

        $token = request("token");

        $validarToken = DB::select("SELECT * FROM tokenlolatv WHERE token = '$token'"); //Validamos si el token es valido

        $tokenValido = false;
        if ($validarToken != null) {
            $tokenValido = true;
        }

        if ($tokenValido) {
            //Token valido
            $usuario = DB::select("SELECT id FROM users WHERE id = '" . $validarToken[0]->usuario_id . "'");
            DB::delete("DELETE FROM tokenlolatv where usuario_id =" . $usuario[0]->id);
            DB::table("users")->where("id", "=", $usuario[0]->id)->update([
                "logueado" => 0
            ]);

            //Actualizar fecha de ultima conexion
            DB::table("users")->where("id", "=", $usuario[0]->id)->update([
                "ultimaconexion" => Carbon::now()
            ]);

            $datos[0]["codigo"] = "NXQRwc2HsxVyIS43QWqO";
            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
        }

        //Token no valido
        $datos[0]["codigo"] = "P1zv7ZFD8dOvOTQVUTys";
        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

    }

    public function verificarsupervisionvehiculo()
    {

        $correo = request("correo");
        $dispositivo = request("dispositivo");
        $idunico = request("idunico");
        $version = request("version");
        $modelo = request("modelo");
        $versiongradle = request("versiongradle");
        $lenguajetelefono = request("lenguajetelefono");

        try {

            $validarAplicacion = DB::select("SELECT id FROM dispositivos WHERE id = '$dispositivo' AND estatus = 1");

            $datos = [];
            if ($validarAplicacion != null) {//Existe aplicacion?
                //Aplicacion existe

                $usuario = DB::select("SELECT u.id, u.rol_id, u.name, u.email, u.password, u.id_zona, u.logueado FROM users u WHERE UPPER(u.email) = '$correo'");
                if ($usuario != null) { //Usuario exite?
                    //Usuario existe

                    $id_usuario = $usuario[0]->id;
                    $usuariofranquicia = DB::select("SELECT (SELECT f.id FROM franquicias f WHERE f.id = uf.id_franquicia) as id_franquicia
                                                                  FROM usuariosfranquicia uf WHERE uf.id_usuario = '$id_usuario'");

                    if ($usuariofranquicia != null) {
                        //Usuario franquicia existe

                        if ($usuario[0]->rol_id == 4) {
                            //Usuario de cobranza

                            if ($usuario[0]->logueado == 1) {
                                //Usuario logueado en la pagina
                                $datos[0]["codigo"] = "yqZKQB8et5w3N7dK3ZZC";
                                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                            } else {

                                $fechaActual = Carbon::now();
                                $dispositivoActivo = DB::select("SELECT estatus FROM dispositivosusuarios WHERE identificadorunico = '$idunico' AND id_usuario = '$id_usuario'");

                                if ($dispositivoActivo != null) { //Existe el dispositivo?
                                    //Existe dispositivo

                                    if ($dispositivoActivo[0]->estatus == 1) {
                                        //Dispositivo activado

                                        $id_franquicia = $usuariofranquicia[0]->id_franquicia;
                                        $vehiculoAsignado = DB::select("SELECT vu.id_vehiculo FROM vehiculosusuarios vu WHERE vu.id_usuario = '$id_usuario'
                                                                              AND vu.id_franquicia = '$id_franquicia'");

                                        if($vehiculoAsignado != null){
                                            //Tiene un vehiculo asignado
                                            $id_vehiculo = $vehiculoAsignado[0]->id_vehiculo;

                                            //Verificar si se le asigno un vehiculo con una supervision sin usuario creada en la noche anteriror
                                            $existeSupervisionSinusuario = DB::select("SELECT vs.indice FROM vehiculossupervision vs WHERE vs.id_vehiculo = '$id_vehiculo'
                                                                                             AND vs.id_franquicia = '$id_franquicia' AND (vs.id_usuario = '' OR vs.id_usuario IS NULL) AND vs.estado = 0");

                                            if($existeSupervisionSinusuario != null){
                                                //Tenia una supervision sin usuario pendiente
                                                $indiceSupervision = $existeSupervisionSinusuario[0]->indice;
                                                DB::table('vehiculossupervision')->where("indice","=","$indiceSupervision")->update([
                                                    'id_usuario' => $id_usuario, 'created_at' => Carbon::now()
                                                ]);

                                            }

                                            $supervisionVehiculo = DB::select("SELECT vs.indice, vs.id_franquicia, vs.id_usuario,vs.id_vehiculo,
                                                                                    (SELECT v.numserie FROM vehiculos v WHERE v.indice = vs.id_vehiculo) as numserie, vs.estado,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.kilometraje1, ''), '/', -1) as kilometraje1,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.kilometraje2, ''), '/', -1) as kilometraje2,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.ladoizquierdo, ''), '/', -1) as ladoizquierdo,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.ladoderecho, ''), '/', -1) as ladoderecho,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.frente, ''), '/', -1) as frente,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.atras, ''), '/', -1) as atras,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.extra1, ''), '/', -1) as extra1,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.extra2, ''), '/', -1) as extra2,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.extra3, ''), '/', -1) as extra3,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.extra4, ''), '/', -1) as extra4,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.extra5, ''), '/', -1) as extra5,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.extra6, ''), '/', -1) as extra6,
                                                                                    TIMESTAMPDIFF(HOUR,vs.created_at,DATE_FORMAT(NOW(), '%Y-%m-%d %h:%m:%S')) as horasCreacion
                                                                                    FROM vehiculossupervision vs
                                                                                     WHERE vs.id_franquicia = '$id_franquicia' AND vs.id_usuario = '$id_usuario'
                                                                                     AND vs.id_vehiculo = '$id_vehiculo' ORDER BY vs.created_at DESC LIMIT 1");

                                            $vehiculo = DB::select("SELECT v.numserie FROM vehiculos v WHERE v.indice = '$id_vehiculo'");
                                            $numSerie = "";
                                            if($vehiculo != null){
                                                $numSerie = $vehiculo[0]->numserie;
                                            }

                                            if($supervisionVehiculo != null){
                                                //Tiene supervisiones registradas

                                                if($supervisionVehiculo[0]->estado == 0){
                                                    //Es una supervision pendiente por autorizar

                                                    //Fue creada hace mas de 24 hrs?
                                                    if($supervisionVehiculo[0]->horasCreacion <= 24 ){
                                                        //Supervision creada hace menos de 24hrs

                                                        if($supervisionVehiculo[0]->kilometraje1 == null && $supervisionVehiculo[0]->kilometraje2 == null){
                                                            //Registros de fotos kilometraje1 y kilometraje2 son vacios - supervision creada por el sistema
                                                            $datos[0]["codigo"] = "BCE3KTxrNUqff5opY5J9";
                                                            $datos[0]["nunserie"] = $numSerie;
                                                            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                                                        }else{
                                                            //Tienen valores al menos kilometraje1 o kilometraje2 - supervision ya creada - actualizar
                                                            $datos[0]["codigo"] = "oZ6kPihlk0LwE9Je7S5g";
                                                            $datos[0]["indice"] = $supervisionVehiculo[0]->indice;
                                                            $datos[0]["supervision"] = $supervisionVehiculo;

                                                            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                                                        }

                                                    }else{
                                                        //Supervision en estatus pendiente con las de 24 hrs de creacion
                                                        $datos[0]["codigo"] = "ZlbS4yVHLdebhF8F83sf";
                                                        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                                                    }

                                                }else{
                                                    //Supervision ya aprobada - permitir acceso para generar una nueva
                                                    $datos[0]["codigo"] = "BCE3KTxrNUqff5opY5J9";
                                                    $datos[0]["nunserie"] = $numSerie;
                                                    return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                                                }
                                            }else {
                                                //Sera apenas la primer supervision
                                                $datos[0]["codigo"] = "BCE3KTxrNUqff5opY5J9";
                                                $datos[0]["nunserie"] = $numSerie;
                                                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                                            }

                                        }else{
                                            //Cobrador sin vehiculo dado de alta
                                            $datos[0]["codigo"] = "qefs3ZPOvqz2E2cZsPbp";
                                            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                                        }
                                    }
                                    //Dispositivo activado
                                    $datos[0]["codigo"] = "Ppts8qWkkqosQQqRKlMz";
                                    return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                                }

                                //No existe dispositivo
                                DB::table("dispositivosusuarios")->insert([
                                    "id_usuario" => $usuario[0]->id,
                                    "versionandroid" => $version,
                                    "modelo" => $modelo,
                                    "identificadorunico" => $idunico,
                                    "versiongradle" => $versiongradle,
                                    "lenguajetelefono" => $lenguajetelefono,
                                    "created_at" => $fechaActual]);
                                $datos[0]["codigo"] = "Ppts8qWkkqosQQqRKlMz";
                                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";;

                            }

                        }

                        //Usuario no admitido
                        $datos[0]["codigo"] = "swYbf6Diq6DiRS67lXaA";
                        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                    }

                    //Usuario franquicia no existe
                    $datos[0]["codigo"] = "eQiNoYUY0qV4dSlaHMMf";
                    return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                }

                //Usuario no existe
                $datos[0]["codigo"] = "5Gn6oZ7QUFxT4uDULhAB";
                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

            } else {

                //Aplicacion no existe
                $appactual = DB::select("SELECT apk FROM dispositivos WHERE estatus = '1'");
                $datos[0]["codigo"] = "ozpw6GLnvbCMpL2QjIQl";
                $datos[0]["appactual"] = asset($appactual[0]->apk);
                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

            }

        }catch(\Exception $e){
            \Log::info("Error: serviciowebtrabajadoruno: (verificarsupervisionvehiculo) - correo: " . $correo . "\n" . $e);
        }

    }

    public function verificarfotossupervisionvehiculo(){
        $id_usuario = request("id_usuario");
        $id_franquicia = request("id_franquicia");
        $kilometraje1 = request("kilometraje1");
        $kilometraje2 = request("kilometraje2");
        $esActualizarSupervision = request("banderaActualizar");

        //Dia de la semana
        $hoy = Carbon::now();

        //Verificar horario para actualizacion de imagen
        $horaActual = Carbon::now();
        $horaActual = Carbon::parse($horaActual)->format('H:i');
        $horaLimite = Carbon::parse('09:00')->format('H:i');

        $existeHorarioSupervision = DB::select("SELECT vh.horalimitechoferfoto1 FROM vehiculoshorariosupervision vh WHERE vh.id_franquicia = '$id_franquicia'");
        if ($existeHorarioSupervision != null) {
            //Existe un horario registrado
            $horaLimite = Carbon::parse($existeHorarioSupervision[0]->horalimitechoferfoto1)->format('H:i');
        }

        //Verificar que no se intenten registrar 2 supervisiones un mismo dia
        $ultimaSupervision = DB::select("SELECT vs.kilometraje1, vs.kilometraje2, vs.created_at FROM vehiculossupervision vs WHERE vs.id_usuario = '$id_usuario'
                                               AND vs.id_franquicia = '$id_franquicia' ORDER BY vs.created_at DESC LIMIT 1");

        if($ultimaSupervision != null && ($ultimaSupervision[0]->kilometraje1 != null && $ultimaSupervision[0]->kilometraje2 != null) &&
            (Carbon::parse($hoy)->format('Y-m-d') == Carbon::parse($ultimaSupervision[0]->created_at)->format('Y-m-d'))){
            $datos[0]["codigo"] = "e91pVlsRSJInQ5RegdPK";
            $datos[0]["mensaje"] = "La supervisión de hoy ya está registrada, no se puede crear otra.";
            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
        }

        //Evaluar que foto permitira actualizar referente a kilometraje

        if(!$esActualizarSupervision){
            //Se creara pro primera vez la supervision - No puedes dejar los campos de kilometraje vacios

            if($kilometraje1 == "0" && $kilometraje2 == "0") {
                //No pueden estar las dos imagenes vacias
                $datos[0]["codigo"] = "e91pVlsRSJInQ5RegdPK";
                $datos[0]["mensaje"] = "Toma las imagenes y posterior da clic en CREAR";
                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

            }
        }

        if($kilometraje1 == "1" && $kilometraje2 == "1") {
            //No puedes adjuntar las dos imagenes al mismo tiempo
            $datos[0]["codigo"] = "e91pVlsRSJInQ5RegdPK";
            $datos[0]["mensaje"] = "No puedes adjuntar foto Kilometraje mañana y tarde al mismo momento.";
            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

        }

        if ($horaActual < $horaLimite && $kilometraje2 == "1") {
            //Horario para actualizar kilometraje1 pero adjunto imagen de kilometraje 2
            $datos[0]["codigo"] = "e91pVlsRSJInQ5RegdPK";
            $datos[0]["mensaje"] = "No puedes adjuntar foto Kilometraje tarde debido a la hora actual";
            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

        }

        if ($horaActual > $horaLimite && $kilometraje1 == "1") {
            //Horario para actualizar kilometraje2 pero adjunto imagen de kilometraje 1
            $datos[0]["codigo"] = "e91pVlsRSJInQ5RegdPK";
            $datos[0]["mensaje"] = "No puedes adjuntar foto Kilometraje mañana debido a la hora actual";
            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
        }

        //Retornar clave de correcto
        $datos[0]["codigo"] = "lazeXsDzR8pptisupKhP";
        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

    }
    function registrarsupervisionvehiculo(){
        $correo = request("correo");
        $dispositivo = request("dispositivo");
        $idunico = request("idunico");
        $version = request("version");
        $modelo = request("modelo");
        $versiongradle = request("versiongradle");
        $lenguajetelefono = request("lenguajetelefono");
        $kilometraje1 = request("kilometraje1");
        $kilometraje2 = request("Kilometraje2");
        $ladoizquierdo = request("ladoizquierdo");
        $ladoderecho = request("ladoderecho");
        $frente = request("frente");
        $atras = request("atras");
        $extra1 = request("extra1");
        $extra2 = request("extra2");
        $extra3 = request("extra3");
        $extra4 = request("extra4");
        $extra5 = request("extra5");
        $extra6 = request("extra6");
        $esActualizacion = true;

        try {

            $validarAplicacion = DB::select("SELECT id FROM dispositivos WHERE id = '$dispositivo' AND estatus = 1");

            $datos = [];
            if ($validarAplicacion != null) {//Existe aplicacion?
                //Aplicacion existe

                $usuario = DB::select("SELECT u.id, u.rol_id, u.name, u.email, u.password, u.id_zona, u.logueado FROM users u WHERE UPPER(u.email) = '$correo'");
                if ($usuario != null) { //Usuario exite?
                    //Usuario existe

                    $id_usuario = $usuario[0]->id;
                    $usuariofranquicia = DB::select("SELECT (SELECT f.id FROM franquicias f WHERE f.id = uf.id_franquicia) as id_franquicia
                                                                  FROM usuariosfranquicia uf WHERE uf.id_usuario = '$id_usuario'");

                    if ($usuariofranquicia != null) {
                        //Usuario franquicia existe

                        if ($usuario[0]->rol_id == 4) {
                            //Usuario de cobranza

                            if ($usuario[0]->logueado == 1) {
                                //Usuario logueado en la pagina
                                $datos[0]["codigo"] = "yqZKQB8et5w3N7dK3ZZC";
                                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                            } else {

                                $fechaActual = Carbon::now();
                                $dispositivoActivo = DB::select("SELECT estatus FROM dispositivosusuarios WHERE identificadorunico = '$idunico' AND id_usuario = '$id_usuario'");

                                if ($dispositivoActivo != null) { //Existe el dispositivo?
                                    //Existe dispositivo

                                    if ($dispositivoActivo[0]->estatus == 1) {
                                        //Dispositivo activado

                                        $id_franquicia = $usuariofranquicia[0]->id_franquicia;
                                        $vehiculoAsignado = DB::select("SELECT vu.id_vehiculo FROM vehiculosusuarios vu WHERE vu.id_usuario = '$id_usuario'
                                                                              AND vu.id_franquicia = '$id_franquicia'");

                                        if($vehiculoAsignado != null){
                                            //Tiene un vehiculo asignado
                                            $id_vehiculo = $vehiculoAsignado[0]->id_vehiculo;

                                            $existeSupervision = DB::select("SELECT * FROM vehiculossupervision vs WHERE vs.estado = '0'
                                                                                   AND vs.id_usuario = '$id_usuario' AND vs.id_franquicia = '$id_franquicia' ORDER BY vs.created_at DESC LIMIT 1");

                                            //Verificar si se registrara como nueva supervision o como una actualizacion
                                            if($existeSupervision == null || ($existeSupervision != null && ($existeSupervision[0]->kilometraje1 == null &&
                                               $existeSupervision[0]->kilometraje2 == null))){
                                                //No existe una supervision o fue creada por el sistema y es la primer carga de imagenes - validaciones de supervision a crear

                                                //Dia de la semana
                                                $hoy = Carbon::now();
                                                $diaSemana = Carbon::parse($hoy)->dayOfWeekIso;

                                                //Verificar horario para actualizacion de imagen
                                                $horaActual = Carbon::now();
                                                $horaActual = Carbon::parse($horaActual)->format('H:i');
                                                $horaLimite = Carbon::parse('09:00')->format('H:i');

                                                $existeHorarioSupervision = DB::select("SELECT vh.horalimitechoferfoto1 FROM vehiculoshorariosupervision vh WHERE vh.id_franquicia = '$id_franquicia'");
                                                if ($existeHorarioSupervision != null) {
                                                    //Existe un horario registrado
                                                    $horaLimite = Carbon::parse($existeHorarioSupervision[0]->horalimitechoferfoto1)->format('H:i');
                                                }

                                                if($diaSemana == 5){
                                                    //Dia de la semana viernes
                                                    if($horaActual < $horaLimite){
                                                        //Estas a tiempo de subir foto kilometraje manana

                                                        if($kilometraje1 == null || $ladoizquierdo == null || $ladoderecho == null || $frente == null || $atras == null){
                                                            //Obligatoria foto kilometraje manana, foto lado izq - derecho, frente y atras
                                                            $datos[0]["codigo"] = "e91pVlsRSJInQ5RegdPK";
                                                            $datos[0]["mensaje"] = "Una o mas imagenes pendiente por capturar";
                                                            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                                                        }

                                                    }else{
                                                        //horario para subir foto kilometraje manana exedido
                                                        if($kilometraje2 == null || $ladoizquierdo == null || $ladoderecho == null || $frente == null || $atras == null){
                                                            //Obligatorio foto kilometraje tarde, lado izq - derecho, frente y atras
                                                            $datos[0]["codigo"] = "e91pVlsRSJInQ5RegdPK";
                                                            $datos[0]["mensaje"] = "Una o mas imagenes pendiente por capturar";
                                                            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                                                        }

                                                    }

                                                }else{
                                                    //Dia de la semana diferente de viernes
                                                    if($horaActual < $horaLimite){
                                                        //Estas a tiempo de subir foto kilometraje manana
                                                        if($kilometraje1 == null){
                                                            //Obligatoria foto kilometraje manana
                                                            $datos[0]["codigo"] = "e91pVlsRSJInQ5RegdPK";
                                                            $datos[0]["mensaje"] = "Foto kilometraje mañana obligatoria.";
                                                            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                                                        }


                                                    }else{
                                                        //horario para subir foto kilometraje manana exedido
                                                        if($kilometraje2 == null){
                                                            //Obligatoria foto kilometraje tarde
                                                            $datos[0]["codigo"] = "e91pVlsRSJInQ5RegdPK";
                                                            $datos[0]["mensaje"] = "Foto kilometraje tarde obligatoria.";
                                                            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                                                        }

                                                    }

                                                }

                                                //No existe supervision?
                                                if($existeSupervision == null){
                                                    //Crear supervision vacia
                                                    DB::table('vehiculossupervision')->insert([
                                                        'id_franquicia' => $id_franquicia, 'id_usuario' => $id_usuario, 'id_vehiculo' => $id_vehiculo,
                                                        'estado' => '0','created_at' => Carbon::now()
                                                    ]);
                                                }

                                                $obtenerIndiceSupervision = DB::select("SELECT vs.indice FROM vehiculossupervision vs WHERE vs.estado = '0'
                                                                                   AND vs.id_usuario = '$id_usuario' AND vs.id_franquicia = '$id_franquicia' ORDER BY vs.created_at DESC LIMIT 1");

                                                $id_supervisionActualizar = $obtenerIndiceSupervision[0]->indice;
                                                $esActualizacion = false;   //Se creara la supervision
                                            }else{
                                                //Existe la supervision
                                                $id_supervisionActualizar = $existeSupervision[0]->indice;
                                            }

                                            //Es crear supervision o Actualizar registros de supervision?
                                            $archivosModificado = "";
                                            if($existeSupervision != null){
                                                //Actualizar registros

                                                //Kilometraje mañana
                                                if($kilometraje1 != null){
                                                    $kilometraje1 = 'uploads/imagenes/vehiculos/supervisionimagenes/' . $kilometraje1;
                                                    $archivosModificado = $archivosModificado . "Kilometraje mañana,";

                                                }else{
                                                    $kilometraje1 = $existeSupervision[0]->kilometraje1;
                                                }

                                                //Kilometraje tarde
                                                if($kilometraje2 != null){
                                                    $kilometraje2 = 'uploads/imagenes/vehiculos/supervisionimagenes/' . $kilometraje2;
                                                    $archivosModificado = $archivosModificado . "Kilometraje tarde,";
                                                }else{
                                                    $kilometraje2 = $existeSupervision[0]->kilometraje2;
                                                }

                                                //Lado izquierdo
                                                if($ladoizquierdo != null){
                                                    $ladoizquierdo = 'uploads/imagenes/vehiculos/supervisionimagenes/' . $ladoizquierdo;
                                                    $archivosModificado = $archivosModificado . "Lado izquierdo,";
                                                }else{
                                                    $ladoizquierdo = $existeSupervision[0]->ladoizquierdo;
                                                }

                                                //Lado derecho
                                                if($ladoderecho != null){
                                                    $ladoderecho = 'uploads/imagenes/vehiculos/supervisionimagenes/' . $ladoderecho;
                                                    $archivosModificado = $archivosModificado . "Lado derecho,";

                                                }else{
                                                    $ladoderecho = $existeSupervision[0]->ladoderecho;
                                                }

                                                //Frente
                                                if($frente != null){
                                                    $frente = 'uploads/imagenes/vehiculos/supervisionimagenes/' . $frente;
                                                    $archivosModificado = $archivosModificado . "Frente,";

                                                }else{
                                                    $frente = $existeSupervision[0]->frente;
                                                }

                                                //Atras
                                                if($atras != null){
                                                    $atras = 'uploads/imagenes/vehiculos/supervisionimagenes/' . $atras;
                                                    $archivosModificado = $archivosModificado . "Atras,";

                                                }else{
                                                    $atras = $existeSupervision[0]->atras;
                                                }

                                                //Extra 1
                                                if($extra1 != null){
                                                    $extra1 = 'uploads/imagenes/vehiculos/supervisionimagenes/' . $extra1;
                                                    $archivosModificado = $archivosModificado . "Extra 1,";

                                                }else{
                                                    $extra1 = $existeSupervision[0]->extra1;
                                                }

                                                //Extra 2
                                                if($extra2 != null){
                                                    $extra2 = 'uploads/imagenes/vehiculos/supervisionimagenes/' . $extra2;
                                                    $archivosModificado = $archivosModificado . "Extra 2,";
                                                }else{
                                                    $extra2 = $existeSupervision[0]->extra2;
                                                }

                                                //Extra 3
                                                if($extra3 != null){
                                                    $extra3 = 'uploads/imagenes/vehiculos/supervisionimagenes/' . $extra3;
                                                    $archivosModificado = $archivosModificado . "Extra 3,";
                                                }else{
                                                    $extra3 = $existeSupervision[0]->extra3;
                                                }

                                                //Extra 4
                                                if($extra4 != null){
                                                    $extra4 = 'uploads/imagenes/vehiculos/supervisionimagenes/' . $extra4;
                                                    $archivosModificado = $archivosModificado . "Extra 4,";
                                                }else{
                                                   $extra4 = $existeSupervision[0]->extra4;
                                                }

                                                //Extra 5
                                                if($extra5 != null){
                                                    $extra5 = 'uploads/imagenes/vehiculos/supervisionimagenes/' . $extra5;
                                                    $archivosModificado = $archivosModificado . "Extra 5";
                                                }else{
                                                    $extra5 = $existeSupervision[0]->extra5;
                                                }

                                                //Extra 6
                                                if($extra6 != null){
                                                    $extra6 = 'uploads/imagenes/vehiculos/supervisionimagenes/' . $extra6;
                                                    $archivosModificado = $archivosModificado . "Extra 6";
                                                }else{
                                                    $extra6 = $existeSupervision[0]->extra6;
                                                }

                                            }else{
                                                //Crear registros
                                                $kilometraje1 = ($kilometraje1 != null)? 'uploads/imagenes/vehiculos/supervisionimagenes/' . $kilometraje1:$kilometraje1;
                                                $kilometraje2 = ($kilometraje2 != null)? 'uploads/imagenes/vehiculos/supervisionimagenes/' . $kilometraje2:$kilometraje2;
                                                $ladoizquierdo = ($ladoizquierdo != null)? 'uploads/imagenes/vehiculos/supervisionimagenes/' . $ladoizquierdo:$ladoizquierdo;
                                                $ladoderecho = ($ladoderecho != null)? 'uploads/imagenes/vehiculos/supervisionimagenes/' . $ladoderecho:$ladoderecho;
                                                $frente = ($frente != null)? 'uploads/imagenes/vehiculos/supervisionimagenes/' . $frente:$frente;
                                                $atras = ($atras != null)? 'uploads/imagenes/vehiculos/supervisionimagenes/' . $atras:$atras;
                                                $extra1 = ($extra1 != null)? 'uploads/imagenes/vehiculos/supervisionimagenes/' . $extra1:$extra1;
                                                $extra2 = ($extra2 != null)? 'uploads/imagenes/vehiculos/supervisionimagenes/' . $extra2:$extra2;
                                                $extra3 = ($extra3 != null)? 'uploads/imagenes/vehiculos/supervisionimagenes/' . $extra3:$extra3;
                                                $extra4 = ($extra4 != null)? 'uploads/imagenes/vehiculos/supervisionimagenes/' . $extra4:$extra4;
                                                $extra5 = ($extra5 != null)? 'uploads/imagenes/vehiculos/supervisionimagenes/' . $extra5:$extra5;
                                                $extra6 = ($extra6 != null)? 'uploads/imagenes/vehiculos/supervisionimagenes/' . $extra6:$extra6;
                                            }

                                            DB::table('vehiculossupervision')->where("indice","=","$id_supervisionActualizar")->update([
                                                'kilometraje1' => $kilometraje1, 'kilometraje2' => $kilometraje2, 'ladoizquierdo' => $ladoizquierdo, 'ladoderecho' => $ladoderecho,
                                                'frente' => $frente, 'atras' => $atras, 'extra1' => $extra1, 'extra2' => $extra2, 'extra3' => $extra3, 'extra4' => $extra4, 'extra5' => $extra5,
                                                'extra6' => $extra6, 'updated_at' => Carbon::now()
                                            ]);

                                            //Datos de vehiculo para registro de movimientos
                                            $vehiculo = DB::select("SELECT v.numserie, v.placas FROM vehiculos v WHERE v.indice = '$id_vehiculo'");
                                            $numSerie = "";
                                            $placasVehiculo = "";
                                            if($vehiculo != null){
                                                $numSerie = $vehiculo[0]->numserie;
                                                $placasVehiculo = $vehiculo[0]->placas;
                                            }

                                            //Registrar movimiento
                                            if($esActualizacion){
                                                //Es un movimiento de actualizar

                                                if($archivosModificado != ""){
                                                    //Se modifico al menos una imagen
                                                    $archivosModificado = trim($archivosModificado,",");
                                                    DB::table('historialsucursal')->insert([
                                                        'id_usuarioC' => $id_usuario, 'id_franquicia' => $id_franquicia, 'referencia' => $id_vehiculo,
                                                        'tipomensaje' => '0', 'created_at' => Carbon::now(),
                                                        'cambios' => "Actualizo foto: '". $archivosModificado ."' para vehículo con numero de serie: '" . $numSerie . "' placas: '" . $placasVehiculo ."'",
                                                        'seccion' => '5'
                                                    ]);
                                                }

                                            }else{
                                                //Es un registro como neuva supervision
                                                DB::table('historialsucursal')->insert([
                                                    'id_usuarioC' => $id_usuario, 'id_franquicia' => $id_franquicia, 'referencia' => $id_vehiculo,
                                                    'tipomensaje' => '0', 'created_at' => Carbon::now(),
                                                    'cambios' => "Registro supervisión para vehículo numero de serie: '" . $numSerie . "' placas: '" . $placasVehiculo ."'",
                                                    'seccion' => '5'
                                                ]);
                                            }

                                            //Obtener datos de supervision ya actualizada
                                            $supervisionVehiculo = DB::select("SELECT vs.indice, vs.id_franquicia, vs.id_usuario,vs.id_vehiculo,
                                                                                    (SELECT v.numserie FROM vehiculos v WHERE v.indice = vs.id_vehiculo) as numserie, vs.estado,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.kilometraje1, ''), '/', -1) as kilometraje1,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.kilometraje2, ''), '/', -1) as kilometraje2,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.ladoizquierdo, ''), '/', -1) as ladoizquierdo,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.ladoderecho, ''), '/', -1) as ladoderecho,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.frente, ''), '/', -1) as frente,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.atras, ''), '/', -1) as atras,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.extra1, ''), '/', -1) as extra1,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.extra2, ''), '/', -1) as extra2,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.extra3, ''), '/', -1) as extra3,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.extra4, ''), '/', -1) as extra4,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.extra5, ''), '/', -1) as extra5,
                                                                                    SUBSTRING_INDEX(IFNULL(vs.extra6, ''), '/', -1) as extra6
                                                                                    FROM vehiculossupervision vs
                                                                                     WHERE vs.id_franquicia = '$id_franquicia' AND vs.id_usuario = '$id_usuario'
                                                                                     AND vs.id_vehiculo = '$id_vehiculo' ORDER BY vs.created_at DESC LIMIT 1");

                                            //Retornar clave de registro correcto
                                            $datos[0]["codigo"] = "lazeXsDzR8pptisupKhP";
                                            $datos[0]["indice"] = $supervisionVehiculo[0]->indice;
                                            $datos[0]["supervision"] = $supervisionVehiculo;
                                            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                                        }else{
                                            //Cobrador sin vehiculo dado de alta
                                            $datos[0]["codigo"] = "qefs3ZPOvqz2E2cZsPbp";
                                            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                                        }
                                    }
                                    //Dispositivo activado
                                    $datos[0]["codigo"] = "Ppts8qWkkqosQQqRKlMz";
                                    return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                                }

                                //No existe dispositivo
                                DB::table("dispositivosusuarios")->insert([
                                    "id_usuario" => $usuario[0]->id,
                                    "versionandroid" => $version,
                                    "modelo" => $modelo,
                                    "identificadorunico" => $idunico,
                                    "versiongradle" => $versiongradle,
                                    "lenguajetelefono" => $lenguajetelefono,
                                    "created_at" => $fechaActual]);
                                $datos[0]["codigo"] = "Ppts8qWkkqosQQqRKlMz";
                                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";;

                            }

                        }

                        //Usuario no admitido
                        $datos[0]["codigo"] = "swYbf6Diq6DiRS67lXaA";
                        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                    }

                    //Usuario franquicia no existe
                    $datos[0]["codigo"] = "eQiNoYUY0qV4dSlaHMMf";
                    return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                }

                //Usuario no existe
                $datos[0]["codigo"] = "5Gn6oZ7QUFxT4uDULhAB";
                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

            } else {

                //Aplicacion no existe
                $appactual = DB::select("SELECT apk FROM dispositivos WHERE estatus = '1'");
                $datos[0]["codigo"] = "ozpw6GLnvbCMpL2QjIQl";
                $datos[0]["appactual"] = asset($appactual[0]->apk);
                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

            }

        }catch(\Exception $e){
            \Log::info("Error: serviciowebtrabajadoruno: (registrarsupervisionvehiculo) - correo: " . $correo . "\n" . $e);
        }

    }

    function obtenerhistorialmovimientoscontratos(){
        $correo = request("correo");
        $dispositivo = request("dispositivo");
        $idunico = request("idunico");
        $version = request("version");
        $modelo = request("modelo");
        $versiongradle = request("versiongradle");
        $lenguajetelefono = request("lenguajetelefono");
        $idContrato = request("idContrato");

        try {

            $validarAplicacion = DB::select("SELECT id FROM dispositivos WHERE id = '$dispositivo' AND estatus = 1");

            $datos = [];
            if ($validarAplicacion != null) {//Existe aplicacion?
                //Aplicacion existe

                $usuario = DB::select("SELECT u.id, u.rol_id, u.name, u.email, u.password, u.id_zona, u.logueado FROM users u WHERE UPPER(u.email) = '$correo'");
                if ($usuario != null) { //Usuario exite?
                    //Usuario existe

                    $id_usuario = $usuario[0]->id;
                    $usuariofranquicia = DB::select("SELECT (SELECT f.id FROM franquicias f WHERE f.id = uf.id_franquicia) as id_franquicia
                                                                  FROM usuariosfranquicia uf WHERE uf.id_usuario = '$id_usuario'");

                    if ($usuariofranquicia != null) {
                        //Usuario franquicia existe

                        if ($usuario[0]->rol_id == 4) {
                            //Usuario de cobranza

                            if ($usuario[0]->logueado == 1) {
                                //Usuario logueado en la pagina
                                $datos[0]["codigo"] = "yqZKQB8et5w3N7dK3ZZC";
                                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                            } else {

                                $fechaActual = Carbon::now();
                                $dispositivoActivo = DB::select("SELECT estatus FROM dispositivosusuarios WHERE identificadorunico = '$idunico' AND id_usuario = '$id_usuario'");

                                if ($dispositivoActivo != null) { //Existe el dispositivo?
                                    //Existe dispositivo

                                    if ($dispositivoActivo[0]->estatus == 1) {
                                        //Dispositivo activado

                                        $historialcontrato = DB::select("SELECT id_usuarioC, h.id, h.cambios, u.name, h.created_at, h.id_contrato
                                                        from historialcontrato h
                                                        inner join users u on h.id_usuarioC = u.id
                                                        WHERE id_contrato = '$idContrato' order by h.created_at desc");

                                        $datos[0]["codigo"] = "fiVwIpStKaBRIAJpyVHO";
                                        $datos[0]["historialcontrato"] = $historialcontrato;
                                        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                                    }
                                    //Dispositivo activado
                                    $datos[0]["codigo"] = "Ppts8qWkkqosQQqRKlMz";
                                    return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
                                }

                                //No existe dispositivo
                                DB::table("dispositivosusuarios")->insert([
                                    "id_usuario" => $usuario[0]->id,
                                    "versionandroid" => $version,
                                    "modelo" => $modelo,
                                    "identificadorunico" => $idunico,
                                    "versiongradle" => $versiongradle,
                                    "lenguajetelefono" => $lenguajetelefono,
                                    "created_at" => $fechaActual]);
                                $datos[0]["codigo"] = "Ppts8qWkkqosQQqRKlMz";
                                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";;

                            }

                        }

                        //Usuario no admitido
                        $datos[0]["codigo"] = "swYbf6Diq6DiRS67lXaA";
                        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                    }

                    //Usuario franquicia no existe
                    $datos[0]["codigo"] = "eQiNoYUY0qV4dSlaHMMf";
                    return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

                }

                //Usuario no existe
                $datos[0]["codigo"] = "5Gn6oZ7QUFxT4uDULhAB";
                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

            } else {

                //Aplicacion no existe
                $appactual = DB::select("SELECT apk FROM dispositivos WHERE estatus = '1'");
                $datos[0]["codigo"] = "ozpw6GLnvbCMpL2QjIQl";
                $datos[0]["appactual"] = asset($appactual[0]->apk);
                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

            }

        }catch(\Exception $e){
            \Log::info("Error: serviciowebtrabajadoruno: (registrarsupervisionvehiculo) - correo: " . $correo . "\n" . $e);
        }

    }

    function verificarExisteImagenContratoEnServidor($arrayContratosNoTerminados){
        $arrayBanderasImagenes = null;

        foreach ($arrayContratosNoTerminados as $contratoNoTerminado){
            if(Storage::disk('disco')->exists('uploads/imagenes/contratos/fotoine/' . $contratoNoTerminado->fotoine)){
                //Existe imagen foto ine frente en servidor
                $contratoNoTerminado->fotoine = 1;
            }else{
                $contratoNoTerminado->fotoine = 0;
            }

            if(Storage::disk('disco')->exists('uploads/imagenes/contratos/fotoineatras/' . $contratoNoTerminado->fotoineatras)){
                //Existe imagen foto ine atras en servidor
                $contratoNoTerminado->fotoineatras = 1;
            }else{
                $contratoNoTerminado->fotoineatras = 0;
            }

            if(Storage::disk('disco')->exists('uploads/imagenes/contratos/fotocasa/' . $contratoNoTerminado->fotocasa)){
                //Existe imagen foto casa en servidor
                $contratoNoTerminado->fotocasa = 1;
            }else{
                $contratoNoTerminado->fotocasa = 0;
            }

            if(Storage::disk('disco')->exists('uploads/imagenes/contratos/comprobantedomicilio/' . $contratoNoTerminado->comprobantedomicilio)){
                //Existe imagen foto comprobante domicilio en servidor
                $contratoNoTerminado->comprobantedomicilio = 1;
            }else{
                $contratoNoTerminado->comprobantedomicilio = 0;
            }

            if(Storage::disk('disco')->exists('uploads/imagenes/contratos/pagare/' . $contratoNoTerminado->pagare)){
                //Existe imagen foto pagare en servidor
                $contratoNoTerminado->pagare = 1;
            }else{
                $contratoNoTerminado->pagare = 0;
            }

            if(Storage::disk('disco')->exists('uploads/imagenes/contratos/fotootros/' . $contratoNoTerminado->fotootros)){
                //Existe imagen foto otros en servidor
                $contratoNoTerminado->fotootros = 1;
            }else{
                $contratoNoTerminado->fotootros = 0;
            }

        }

        $arrayBanderasImagenes = $arrayContratosNoTerminados;

        return $arrayBanderasImagenes;
    }

}
