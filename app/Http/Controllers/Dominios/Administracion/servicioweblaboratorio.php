<?php

namespace App\Http\Controllers\Dominios\Administracion;

use App\Clases\contratosGlobal;
use App\Clases\globalesServicioWeb;
use App\Http\Controllers\Exception;
use App\Http\Controllers\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use function App\Http\Controllers\utf8_decode;

class servicioweblaboratorio extends Controller
{

    //---------------------------------METODOS INICIO Y CIERRE DE SESION ------------------------------------------------------------------
    public function iniciarsesionlaboratorio()
    {

        $correo = strtoupper(request("correo"));
        $password = request("password");
        $dispositivo = request("dispositivo");
        $idunico = request("idunico");
        $version = request("version");
        $modelo = request("modelo");
        $versiongradle = request("versiongradle");
        $lenguajetelefono = utf8_decode(request("lenguaje"));
        $tokenApp = request("tokenApp");  //Este se verificara si es un token valido aun

        try {

            $validarAplicacion = DB::select("SELECT id FROM dispositivos WHERE id = '$dispositivo' AND estatus = 1 AND tipoapp = 1");

            $datos = [];
            if ($validarAplicacion != null) {//Existe aplicacion?
                //Aplicacion existe

                $usuario = DB::select("SELECT u.id, u.rol_id, u.name, u.email, u.password, u.id_zona, u.logueado FROM users u WHERE UPPER(u.email) = '$correo'");
                if ($usuario != null) { //Usuario exite?
                    //Usuario existe

                    $id_usuario = $usuario[0]->id;
                    $usuariofranquicia = DB::select("SELECT (SELECT f.ciudad FROM franquicias f WHERE f.id = uf.id_franquicia) as sucursal,
                                                                  (SELECT f.telefonoatencionclientes FROM franquicias f WHERE f.id = uf.id_franquicia) as telefonoatencionclientessucursal,
                                                                  uf.id_franquicia
                                                                  FROM usuariosfranquicia uf WHERE uf.id_usuario = '$id_usuario'");

                    if ($usuariofranquicia != null) {
                        //Usuario franquicia existe

                        if ($usuario[0]->rol_id == 16) {
                            //Usuario admitido

                            if ($usuario[0]->logueado == 1) {
                                //Usuario logueado en la pagina
                                $datos[0]["codigo"] = "LOLATV8";
                                return "{'datos':'" . base64_encode(json_encode($datos)) . "'}";

                            } else {

                                if (Hash::check($password, $usuario[0]->password)) { //Validacion credenciales
                                    //Credenciales correctas

                                    $fechaActual = Carbon::now();
                                    $dispositivoActivo = DB::select("SELECT estatus FROM dispositivosusuarios WHERE identificadorunico = '$idunico' AND id_usuario = '$id_usuario'");

                                    if ($dispositivoActivo != null) { //Existe el dispositivo?
                                        //Existe dispositivo

                                        if ($dispositivoActivo[0]->estatus == 1) {
                                            //Dispositivo activado

                                            //Verificamos si existe el token recibido desde la app
                                            $tokenVigente = DB::select("SELECT * FROM tokenlolatv WHERE usuario_id = " .$usuario[0]->id . " AND token = '" .$tokenApp. "'");

                                                //Es un token valido o existe?
                                            if($tokenVigente != null){
                                                //Es un token aun vigente
                                                $datos[0]["codigo"] = "LOLATV3";
                                                $datos[0]["token"] = $tokenVigente[0]->token;
                                            }else {
                                                $token = Str::random(60); //Token
                                                DB::delete("DELETE FROM tokenlolatv where usuario_id =" . $usuario[0]->id);
                                                DB::table("tokenlolatv")->insert(["token" => $token, "usuario_id" => $usuario[0]->id]);
                                                $datos[0]["codigo"] = "LOLATV3";
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
                                                $datos[0]["id_equipo"] = $idunico;
                                                DB::table("users")->where("id", "=", $usuario[0]->id)->update([
                                                    "logueado" => 2
                                                ]);
                                            }

                                            //Damos formato al arreglo para enviarlo a la app
                                            return "{'datos':'" . base64_encode(json_encode($datos)) . "'}";

                                        }

                                        //Dispositivo activado
                                        $datos[0]["codigo"] = "LOLATV2";
                                        return "{'datos':'" . base64_encode(json_encode($datos)) . "'}";

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
                                    $datos[0]["codigo"] = "LOLATV2";
                                    return "{'datos':'" . base64_encode(json_encode($datos)) . "'}";

                                }

                                //Credenciales incorrectas
                                $datos[0]["codigo"] = "LOLATV1";
                                return "{'datos':'" . base64_encode(json_encode($datos)) . "'}";

                            }

                        }

                        //Usuario no admitido
                        $datos[0]["codigo"] = "LOLATV10";
                        return "{'datos':'" . base64_encode(json_encode($datos)) . "'}";

                    }

                    //Usuario franquicia no existe
                    $datos[0]["codigo"] = "LOLATV11";
                    return "{'datos':'" . base64_encode(json_encode($datos)) . "'}";

                }

                //Usuario no existe
                $datos[0]["codigo"] = "LOLATV1";
                return "{'datos':'" . base64_encode(json_encode($datos)) . "'}";

            } else {
                //Aplicacion no existe
                $appactual = DB::select("SELECT apk FROM dispositivos WHERE estatus = '1' AND tipoapp = '1'");
                $datos[0]["codigo"] = "LOLATV4";
                $datos[0]["appactual"] = asset($appactual[0]->apk);
                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

            }

        }catch(\Exception $e){
            \Log::info("Error: servicioweblaboratorio: (iniciarsesion) - correo: " . $correo . "\n" . $e);
        }

    }

    public function cerrarsesionlaboratorio() {
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

            $datos[0]["codigo"] = "LOLATV9";
            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
        }

        //Token no valido
        $datos[0]["codigo"] = "LOLATV7";
        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

    }

    public function verificarConexionAppLaboratorio() {
        $datos[0]["estatus"] = "true";
        return "{'datos':'" . base64_encode(json_encode($datos)) . "'}";
    }

    //------------------------------------SINCRONIZAR BD CONTRATOS LABORATORIO ----------------------------------------------------------------

    public function sincronizarBD()
    {
        $token = request("token");
        $tokenValido = DB::select("SELECT usuario_id FROM tokenlolatv WHERE token = '$token'");

        if ($tokenValido != null) {// Existe el token

            try {

                $usuario = DB::select("SELECT uf.id_franquicia, u.name, u.rol_id, u.id_zona, u.id FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                WHERE u.id = '" . $tokenValido[0]->usuario_id . "'");

                $idFranquicia = $usuario[0]->id_franquicia; //Obtenemos el id de la franquicia del usuario logueado


                $contratosComentarios = DB::select("SELECT IFNULL(c.id, '') as id,
                                                                 IFNULL(ec.descripcion, '') as descripcion,
                                                                 IFNULL(c.estatus_estadocontrato, '') as estatus_estadocontrato,
                                                                 IFNULL(c.banderacomentarioconfirmacion, '') as banderacomentarioconfirmacion,
                                                                 IFNULL(f.ciudad, '') as ciudad,
                                                                 IFNULL(c.created_at, '') as created_at,
                                                                 IFNULL((SELECT hc.fechaentrega FROM historialclinico hc
                                                                         WHERE hc.id_contrato = c.id
                                                                         ORDER BY hc.created_at DESC limit 1), '') as fechaentrega,
                                                                IFNULL((SELECT cl.ultimoestatusmanufactura FROM contratoslaboratorio cl
                                                                        WHERE cl.id_contrato = c.id), '') as ultimoestatusmanufactura
                                                                 FROM contratos c
                                                                 INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                 INNER JOIN franquicias f on f.id = c.id_franquicia
                                                                 WHERE c.estatus_estadocontrato IN (7,10,11)
                                                                 AND c.banderacomentarioconfirmacion = 2
                                                                 ORDER BY f.ciudad ASC, fechaentrega ASC, c.estatus_estadocontrato ASC");

                $contratosSComentarios = DB::select("SELECT IFNULL(c.id, '') as id,
                                                                 IFNULL(ec.descripcion, '') as descripcion,
                                                                 IFNULL(c.estatus_estadocontrato, '') as estatus_estadocontrato,
                                                                 IFNULL(c.banderacomentarioconfirmacion, '') as banderacomentarioconfirmacion,
                                                                 IFNULL(f.ciudad, '') as ciudad,
                                                                 IFNULL(c.created_at, '') as created_at,
                                                                 IFNULL((SELECT hc.fechaentrega FROM historialclinico hc
                                                                         WHERE hc.id_contrato = c.id
                                                                         ORDER BY hc.created_at DESC limit 1), '') as fechaentrega,
                                                                IFNULL((SELECT cl.ultimoestatusmanufactura FROM contratoslaboratorio cl
                                                                        WHERE cl.id_contrato = c.id), '') as ultimoestatusmanufactura
                                                                 FROM contratos c
                                                                 INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                 INNER JOIN franquicias f on f.id = c.id_franquicia
                                                                 WHERE c.estatus_estadocontrato IN (7,10,11)
                                                                 AND c.banderacomentarioconfirmacion != 2
                                                                 ORDER BY f.ciudad ASC, fechaentrega ASC, c.estatus_estadocontrato ASC");

                $contratosLaboratorio = array_merge($contratosComentarios,$contratosSComentarios);
                $datos[0]["codigo"] = "LOLATV3";
                $datos[0]["contratosLaboratorio"] = $contratosLaboratorio;

                return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

            }catch(\Exception $e){
                \Log::info("Error: servicioweblaboratorio: (LlenarTablaBD) - id_usuario: " . $tokenValido[0]->usuario_id . "\n" . $e);
            }

        } else {
            //El token no es valido, se debera cerrar sesion.
            $datos[0]["codigo"] = "LOLATV5";
            return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
        }
    }

    //------------------------------------SECCION PARA LISTA DE CONTRATOS LABORATORIO -------------------------------------
    public function filtrarContrato(){
        $filtro = request("filtro");
        $dispositivo = request("dispositivo");
        $correo = request("correo");
        $idUnico = request("idUnico");
        $token = request("token");

        $verificacionRespuesta = self::verificarSesionActiva($dispositivo,$correo,$idUnico,$token);
        $datos = [];

        //Verificacion de sesion activa correcta?
        if($verificacionRespuesta== "LOLATV9"){
            //Es correcta la sesion
            $contratosComentariosFiltro = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,f.ciudad,c.created_at,
                                                            (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as fechaentrega,
                                                            (SELECT cl.ultimoestatusmanufactura FROM contratoslaboratorio cl WHERE cl.id_contrato = c.id) as ultimoestatusmanufactura
                                                            FROM contratos c
                                                            INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                            INNER JOIN franquicias f on f.id = c.id_franquicia
                                                            WHERE c.estatus_estadocontrato IN (7,10,11)
                                                            AND c.id like '%$filtro%'
                                                            AND c.banderacomentarioconfirmacion = 2 ORDER BY f.ciudad ASC, fechaentrega ASC, c.estatus_estadocontrato ASC");

            $contratosSComentariosFiltro = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,f.ciudad,c.created_at,
                                                                (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as fechaentrega,
                                                                (SELECT cl.ultimoestatusmanufactura FROM contratoslaboratorio cl WHERE cl.id_contrato = c.id) as ultimoestatusmanufactura
                                                                FROM contratos c
                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
                                                                WHERE c.banderacomentarioconfirmacion != 2 AND
                                                                c.estatus_estadocontrato IN (7,10,11) AND
                                                                c.id like '%$filtro%'
                                                               ORDER BY f.ciudad ASC, fechaentrega ASC, c.estatus_estadocontrato ASC
                                                               ");

            $contratosSTerminar = DB::select("SELECT c.id, c.estatus_estadocontrato, c.created_at, f.ciudad, ec.descripcion
                                                                FROM contratos c
                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                WHERE c.banderacomentarioconfirmacion != 2 AND
                                                                c.id_franquicia != '00000' AND
                                                                (c.datos = 1 AND c.estatus_estadocontrato = 0 )AND
                                                                c.id like '%$filtro%'
                                                                ORDER BY f.ciudad ASC, c.estatus_estadocontrato ASC");

            $contratosPendientes = DB::select("SELECT c.id, c.estatus_estadocontrato, f.ciudad
                                                                FROM contratos c
                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
                                                                WHERE c.banderacomentarioconfirmacion != 2 AND
                                                                c.id_franquicia != '00000' AND
                                                                (c.datos = 0 AND c.estatus_estadocontrato IS null ) AND
                                                                 c.id like '%$filtro%'
                                                                ORDER BY f.ciudad ASC, c.estatus_estadocontrato ASC");

            $otrosContratos = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,f.ciudad,c.created_at,
                                                                (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as fechaentrega,
                                                                IFNULL((SELECT cl.ultimoestatusmanufactura FROM contratoslaboratorio cl WHERE cl.id_contrato = c.id), '') as ultimoestatusmanufactura
                                                                FROM contratos c
                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
                                                                WHERE c.id_franquicia != '00000' AND
                                                                c.estatus_estadocontrato IN (1,2,3,4,5,6,8,9,12,14) AND
                                                                c.id like '%$filtro%'
                                                                ORDER BY f.ciudad ASC, fechaentrega ASC, c.estatus_estadocontrato ASC
                                                               ");

            $contratosLaboratorioFiltro = array_merge($contratosComentariosFiltro,$contratosSComentariosFiltro);
            $contratosFueraLaboratorio = array_merge($otrosContratos, $contratosSTerminar, $contratosPendientes);

            if(sizeof($contratosLaboratorioFiltro) > 0 || sizeof($contratosFueraLaboratorio) > 0){
                $datos[0]["codigo"] = "LOLATV3";    //Datos correctamente obtenidos
                $datos[0]["contratosLaboratorioFiltro"] = $contratosLaboratorioFiltro;
                $datos[0]["contratosFueraLaboratorio"] = $contratosFueraLaboratorio;
            } else {
                $datos[0]["codigo"] = "SIN RESULTADOS"; //Indicara que no existen resultados en el arreglo
            }

        } else {
            //Sesion ya caducada o no cumple con alguna validacion de estatus activo
            $datos[0]["codigo"] =  $verificacionRespuesta;
        }

        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

    }


    public function actualizarestadoenviado() {

        //Datos recibidos desde app
        $dispositivo = request("dispositivo");
        $correo = request("correo");
        $idUnico = request("idUnico");
        $token = request("token");
        $idUsuario = request("idUsuario");
        $idContratos = request("contratosEnviar");

        //Eliminamos [] de la cadena de idContratos
        $idContratos = str_replace("[","",$idContratos);
        $idContratos = str_replace("]","",$idContratos);

        //Convertimos la cadena en un arreglo separando cada idContrato
        $contratosEnviar = explode(",",$idContratos);


        //Validamos el estatus de sesion para la peticion
        $verificacionRespuesta = self::verificarSesionActiva($dispositivo,$correo,$idUnico,$token);
        $datos = [];

        //Sesion activa correcta?
        if($verificacionRespuesta== "LOLATV9"){

            //Obtenemos la lista de contratos en estatus de proceso de envio
            $contratosSComentariosLabo = DB::select("SELECT c.id
                                                                FROM contratos c
                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
                                                                WHERE c.estatus_estadocontrato = 11
                                                                AND c.banderacomentarioconfirmacion != 2
                                                                ORDER BY c.estatus_estadocontrato DESC,f.ciudad ASC");

            foreach ($contratosSComentariosLabo as $contratoSComentariosLabo) {
                foreach ($contratosEnviar as $contratoEnviar){

                    //Comparamos si los que existen en la BD son iguales a los que recibimos desde la app
                    $contratoEnviar = trim($contratoEnviar, " ");
                    if($contratoEnviar == $contratoSComentariosLabo->id){

                        //Si son iguales - actualizamos el estatus contrato
                        DB::table("contratos")->where("id", "=", $contratoSComentariosLabo->id)->update([
                            "estatus_estadocontrato" => 12
                        ]);

                        DB::table("garantias")->where("id_contrato", "=", $contratoSComentariosLabo->id)->update([
                            "estadogarantia" => 3
                        ]);

                        $laboratorio = new laboratorio;
                        DB::table('historialcontrato')->insert([
                            'id' => $laboratorio->getHistorialContratoId(), 'id_usuarioC' => $idUsuario, 'id_contrato' => $contratoSComentariosLabo->id, 'created_at' => Carbon::now(),
                            'cambios' => "L - Cambio el estatus a 'Enviado'"
                        ]);

                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $contratoSComentariosLabo->id,
                            'estatuscontrato' => 12,
                            'created_at' => Carbon::now()
                        ]);

                        //Actualizar abono minimo contrato
                        $contratosGlobal = new contratosGlobal();
                        $contratoActualizar = DB::select("SELECT c.pago, c.id_franquicia FROM contratos c WHERE c.id = '$contratoEnviar'");
                        if($contratoActualizar != null){
                            //Existe el contrato
                            $pago = $contratoActualizar[0]->pago;
                            $idFranquicia_contrato = $contratoActualizar[0]->id_franquicia;

                            if($pago != 0){
                                //Si forma de pago es diferente de contado
                                $abonominimo = $contratosGlobal::calculoCantidadFormaDePago($idFranquicia_contrato, $pago);

                                //Generar cambio a abono minimo contrato
                                DB::table("contratos")->where("id", "=", $contratoEnviar)->update([
                                    "abonominimo" => $abonominimo
                                ]);
                            }
                        }
                        //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                        $contratosGlobal = new contratosGlobal;
                        $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($contratoSComentariosLabo->id, $idUsuario);

                        //Eliminamos contrato de tabla contratoslaboratorio
                        $contratosGlobal::actualizarRegistroTablaContratosLaboratorio($contratoSComentariosLabo->id, "ELIMINAR");

                    }

                }
            }
            $datos[0]["codigo"] =  $verificacionRespuesta;
            $datos[0]["respuesta"] = "El estatus de los contratos se actualizo correctamente.";
        }else {
            $datos[0]["codigo"] =  $verificacionRespuesta;
        }

        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

    }

    //--------------------------------------------------PETICIONES PARA ESTADO CONTRATO -----------------------------//

    public function estadolaboratorio() {
        //Instanciar clase laboratorio para hacer uso de metodos
        $laboratorio = new laboratorio;

        //Parametro recibido desde app
        $idContrato = request("idContrato");
        $dispositivo = request("dispositivo");
        $correo = request("correo");
        $idUnico = request("idUnico");
        $token = request("token");
        $usuario = request("idUsuario");

        $verificacionRespuesta = self::verificarSesionActiva($dispositivo,$correo,$idUnico,$token);
        $datos = [];

        //Verificacion de sesion activa correcta?
        if($verificacionRespuesta== "LOLATV9"){

        $contrato = DB::select("SELECT c.id,c.estatus_estadocontrato,z.zona,c.banderacomentarioconfirmacion,f.ciudad,
                                            (SELECT u.name FROM users u WHERE u.id = c.id_optometrista) as opto,
                                          c.nombre,c.calle,c.numero,c.depto,c.alladode,c.frentea,c.entrecalles,c.colonia,c.localidad,c.telefono,
                                          c.casatipo,c.casacolor,c.nombrereferencia,c.telefonoreferencia,c.correo,c.fotoine,c.fotoineatras,c.fotocasa,c.comprobantedomicilio,
                                          c.tarjeta,c.tarjetapensionatras,c.pago,ec.descripcion,c.created_at as fecha
                                          FROM contratos c
                                          INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                          INNER JOIN zonas z ON z.id = c.id_zona
                                          INNER JOIN franquicias f ON f.id = c.id_franquicia
                                          WHERE c.id = '$idContrato'");

        $datosContratos = DB::select("SELECT c.datos FROM contratos c WHERE c.id = '$idContrato'");
        if($datosContratos[0]->datos == 0){
            $datos[0]["codigo"] = "ERROR";
            $datos[0]["validacion"] = "No se encontro el contrato.";
            return "{'datos':'" . base64_encode(json_encode($datos)) . "'}";
        }

            $franquicia = DB::select("SELECT c.id_franquicia FROM contratos c WHERE c.id = '$idContrato'");

            $idFranquicia = $franquicia[0]->id_franquicia;

            //Obtener paquete de contrato
            $nombrePaquete = DB::select("SELECT (SELECT p.nombre FROM paquetes p WHERE p.id = hc.id_paquete AND p.id_franquicia = '$idFranquicia' LIMIT 1) as paquete
                                                        FROM historialclinico hc WHERE hc.id_contrato = '$idContrato' ORDER BY hc.created_at LIMIT 1");

            $tieneGarantia = DB::select("SELECT id FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia = 2");

            $historialClinico = [];

            if ($tieneGarantia != null) {//Tiene garantia?
                //Si tiene garantia

                //Es paquete DORADO 2?
                if($nombrePaquete[0]->paquete == 'DORADO 2') {
                    //Si es paquete DORADO 2

                    $garantias = DB::select("SELECT estadogarantia, id FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia IN (2,3) ORDER BY created_at DESC LIMIT 2");

                    if ($garantias != null) {
                        foreach ($garantias as $garantia) {
                            $estadogarantia = $garantia->estadogarantia;
                            switch ($estadogarantia) {
                                case 2:
                                    //Garantia activa
                                    $historialClinicoGarantiaActiva = DB::select("SELECT hc.id,hc.esfericoder,hc.cilindroder,hc.ejeder,hc.addder,hc.altder,hc.esfericoizq,hc.cilindroizq,hc.ejeizq,hc.addizq,hc.altizq,
                                                    (SELECT nombre FROM producto p WHERE p.id = hc.id_producto) as armazon,
                                                    (SELECT color FROM producto p WHERE p.id = hc.id_producto) as colorarmazon, hc.id_producto, hc.fechaentrega, hc.bifocalotro,
                                                    hc.material,hc.materialotro,hc.bifocal,hc.fotocromatico,hc.ar,hc.tinte,hc.blueray,hc.otroT,hc.tratamientootro,hc.observaciones,g.id as garantia,
                                                    (SELECT p.piezas FROM producto  p WHERE p.id = hc.id_producto) as piezasr,
                                                    (SELECT g.id FROM garantias g WHERE g.id_contrato = hc.id_contrato AND g.id_historialgarantia = hc.id) as garantia,
                                                    (SELECT u.name FROM users u WHERE u.id = (SELECT g.id_optometrista FROM garantias g WHERE g.id_contrato = hc.id_contrato
                                                    AND g.id_historialgarantia = hc.id)) as optogarantia
                                                    FROM historialclinico hc
                                                    INNER JOIN garantias g ON g.id_contrato = hc.id_contrato
                                                    WHERE hc.id_contrato = '$idContrato' AND g.id_historialgarantia = hc.id AND g.estadogarantia = 2 AND hc.tipo != 2
                                                    ORDER BY hc.created_at DESC");

                                    array_push($historialClinico, $historialClinicoGarantiaActiva);
                                    break;
                                case 3:
                                    //Garantia en contrato enviado
                                    $historialClinicoContratoEnviado = DB::select("SELECT hc.id,hc.esfericoder,hc.cilindroder,hc.ejeder,hc.addder,hc.altder,hc.esfericoizq,hc.cilindroizq,hc.ejeizq,hc.addizq,hc.altizq,
                                                    (SELECT nombre FROM producto p WHERE p.id = hc.id_producto) as armazon,
                                                    (SELECT color FROM producto p WHERE p.id = hc.id_producto) as colorarmazon, hc.id_producto, hc.fechaentrega, hc.bifocalotro,
                                                    hc.material,hc.materialotro,hc.bifocal,hc.fotocromatico,hc.ar,hc.tinte,hc.blueray,hc.otroT,hc.tratamientootro,hc.observaciones,g.id as garantia,
                                                    (SELECT p.piezas FROM producto  p WHERE p.id = hc.id_producto) as piezasr,
                                                    (SELECT g.id FROM garantias g WHERE g.id_contrato = hc.id_contrato AND g.id_historialgarantia = hc.id) as garantia,
                                                    (SELECT u.name FROM users u WHERE u.id = (SELECT g.id_optometrista FROM garantias g WHERE g.id_contrato = hc.id_contrato
                                                    AND g.id_historialgarantia = hc.id)) as optogarantia
                                                    FROM historialclinico hc
                                                    INNER JOIN garantias g ON g.id_contrato = hc.id_contrato
                                                    WHERE hc.id_contrato = '$idContrato' AND g.id_historialgarantia = hc.id AND g.estadogarantia = 3 AND hc.tipo != 2
                                                    ORDER BY hc.created_at DESC");

                                    array_push($historialClinico, $historialClinicoContratoEnviado);
                                    break;
                            }

                        }
                    }

                }else{
                    //Tiene garantia pero es diferente a DORADO 2
                    $historialClinico = DB::select("SELECT hc.id,hc.esfericoder,hc.cilindroder,hc.ejeder,hc.addder,hc.altder,hc.esfericoizq,hc.cilindroizq,hc.ejeizq,hc.addizq,hc.altizq,
                                                    (SELECT nombre FROM producto p WHERE p.id = hc.id_producto) as armazon,
                                                    (SELECT color FROM producto p WHERE p.id = hc.id_producto) as colorarmazon, hc.id_producto, hc.fechaentrega, hc.bifocalotro,
                                                    hc.material,hc.materialotro,hc.bifocal,hc.fotocromatico,hc.ar,hc.tinte,hc.blueray,hc.otroT,hc.tratamientootro,hc.observaciones,g.id as garantia,
                                                    (SELECT p.piezas FROM producto  p WHERE p.id = hc.id_producto) as piezasr,
                                                    (SELECT g.id FROM garantias g WHERE g.id_contrato = hc.id_contrato AND g.id_historialgarantia = hc.id) as garantia,
                                                    (SELECT u.name FROM users u WHERE u.id = (SELECT g.id_optometrista FROM garantias g WHERE g.id_contrato = hc.id_contrato
                                                    AND g.id_historialgarantia = hc.id)) as optogarantia
                                                    FROM historialclinico hc
                                                    INNER JOIN garantias g ON g.id_contrato = hc.id_contrato
                                                    WHERE hc.id_contrato = '$idContrato' AND g.id_historialgarantia = hc.id AND g.estadogarantia = 2 AND hc.tipo != 2
                                                    ORDER BY hc.created_at DESC");

                }

            }else{
                //No tiene garantia
                $historialClinico = DB::select("SELECT hc.id,hc.esfericoder,hc.cilindroder,hc.ejeder,hc.addder,hc.altder,hc.esfericoizq,hc.cilindroizq,hc.ejeizq,hc.addizq,hc.altizq,
                                                    (SELECT nombre FROM producto p WHERE p.id = hc.id_producto) as armazon,
                                                    (SELECT color FROM producto p WHERE p.id = hc.id_producto) as colorarmazon, hc.id_producto, hc.fechaentrega, hc.bifocalotro,
                                                    hc.material,hc.materialotro,hc.bifocal,hc.fotocromatico,hc.ar,hc.tinte,hc.blueray,hc.otroT,hc.tratamientootro,hc.observaciones,
                                                    (SELECT p.piezas FROM producto  p WHERE p.id = hc.id_producto) as piezasr,
                                                    (SELECT g.id FROM garantias g WHERE g.id_contrato = hc.id_contrato AND g.id_historialgarantia = hc.id AND (g.estadogarantia = 2
                                                    AND g.estadocontratogarantia = 2)) as garantia,
                                                    (SELECT u.name FROM users u WHERE u.id = (SELECT c.id_optometrista FROM contratos c WHERE c.id= hc.id_contrato)) as optocontrato
                                                    FROM historialclinico hc WHERE id_contrato = '$idContrato' AND hc.tipo != 2
                                                    ORDER BY hc.created_at DESC");

            }


        $comentarios = DB::select("SELECT u.name,m.comentario,m.fecha FROM mensajesconfirmaciones m INNER JOIN users u ON u.id = m.id_usuario WHERE m.id_contrato = '$idContrato'
                                                ORDER BY m.fecha DESC");
        if ($contrato[0]->banderacomentarioconfirmacion == 2) {
            DB::table("contratos")->where("id", "=", $idContrato)->update([
                "banderacomentarioconfirmacion" => 0
            ]);
        }

        $historialContrato = DB::select("SELECT u.name,hc.cambios,hc.created_at FROM historialcontrato hc
                                                           INNER JOIN users u ON hc.id_usuarioC = u.id
                                                           WHERE u.rol_id = '16' AND hc.id_contrato = '$idContrato' ORDER BY created_at DESC");

        $armazones = DB::select("SELECT * FROM producto p WHERE p.id_tipoproducto = '1' AND p.estado = 1 AND p.piezas > 0 ORDER BY p.nombre ASC");

        $listaArmazonesContrato = DB::select("SELECT p.nombre, cp.piezas, p.color FROM contratoproducto cp
                                                        INNER JOIN producto p on cp.id_producto = p.id
                                                        WHERE cp.id_contrato = '$idContrato' AND p.id_tipoproducto = '1'");

        //Obtencion de datos para mandar a imprimir en impresora termica
        $historialesClinicosImpresoraTermica = $historialClinico;

        //Obtencion de datos sobre estado garantia para imprimir tiket
        $consultaGarantia = DB::select("SELECT estadogarantia FROM garantias WHERE id_contrato = '$idContrato'");
        if($consultaGarantia != null){
            $estadoGarantia = $consultaGarantia[0] -> estadogarantia;
        } else {
            $estadoGarantia = " ";
        }

        if ($historialesClinicosImpresoraTermica != null) {

            $contadorHistorial = 1;
            $idHistorial1 = null;
            $fechaEntregaHistorial1 = null;
            $nombreProducto1 = null;
            $colorProducto1 = null;
            $observacionesHistorial1 = null;
            $esfericoder1 = null;
            $cilindroder1 = null;
            $ejeder1 = null;
            $addder1 = null;
            $altder1 = null;
            $esfericoizq1 = null;
            $cilindroizq1 = null;
            $ejeizq1 = null;
            $addizq1 = null;
            $altizq1 = null;
            $material1 = null;
            $bifocal1 = null;
            $tratamientos1 = "";
            $idHistorial2 = null;
            $fechaEntregaHistorial2 = null;
            $nombreProducto2 = null;
            $colorProducto2 = null;
            $observacionesHistorial2 = null;
            $esfericoder2 = null;
            $cilindroder2 = null;
            $ejeder2 = null;
            $addder2 = null;
            $altder2 = null;
            $esfericoizq2 = null;
            $cilindroizq2 = null;
            $ejeizq2 = null;
            $addizq2 = null;
            $altizq2 = null;
            $material2 = null;
            $bifocal2 = null;
            $tratamientos2 = "";
            $comentariosContrato = "";

            //Obtener comentarios contrato
            foreach ($comentarios as $comentario) {
                $comentariosContrato = $comentariosContrato . $comentario->comentario . "&";
            }

            foreach ($historialesClinicosImpresoraTermica as $historialClinicoImpresora) {

                $idProducto = $historialClinicoImpresora->id_producto;
                $producto = DB::select("SELECT nombre, color FROM producto WHERE id_tipoproducto = '1' AND id = '$idProducto'");
                if ($producto != null) {

                    if ($contadorHistorial == 1) {
                        $idHistorial1 = $historialClinicoImpresora->id;
                        $fechaEntregaHistorial1 = $historialClinicoImpresora->fechaentrega;
                        $nombreProducto1 = $laboratorio::remplazarCaracteres($producto[0]->nombre);
                        $colorProducto1 = $laboratorio::remplazarCaracteres($producto[0]->color);
                        $observacionesHistorial1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->observaciones);
                        //Ojo derecho
                        $esfericoder1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->esfericoder);
                        $cilindroder1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->cilindroder);
                        $ejeder1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->ejeder);
                        $addder1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->addder);
                        $altder1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->altder);
                        //Ojo izquierdo
                        $esfericoizq1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->esfericoizq);
                        $cilindroizq1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->cilindroizq);
                        $ejeizq1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->ejeizq);
                        $addizq1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->addizq);
                        $altizq1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->altizq);

                        if ($esfericoder1 == null) {
                            $esfericoder1 = "NA";
                        }
                        if ($cilindroder1 == null) {
                            $cilindroder1 = "NA";
                        }
                        if ($ejeder1 == null) {
                            $ejeder1 = "NA";
                        }
                        if ($addder1 == null) {
                            $addder1 = "NA";
                        }
                        if ($altder1 == null) {
                            $altder1 = "NA";
                        }
                        if ($esfericoizq1 == null) {
                            $esfericoizq1 = "NA";
                        }
                        if ($cilindroizq1 == null) {
                            $cilindroizq1 = "NA";
                        }
                        if ($ejeizq1 == null) {
                            $ejeizq1 = "NA";
                        }
                        if ($addizq1 == null) {
                            $addizq1 = "NA";
                        }
                        if ($altizq1 == null) {
                            $altizq1 = "NA";
                        }

                        //Material
                        $material1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->material);
                        $materialotro1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->materialotro);
                        //Bifocal
                        $bifocal1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->bifocal);
                        $bifocalotro1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->bifocalotro);
                        //Tratamientos
                        $fotocromatico1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->fotocromatico);
                        $ar1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->ar);
                        $tinte1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->tinte);
                        $blueray1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->blueray);
                        $otroT1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->otroT);
                        $tratamientootro1 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->tratamientootro);

                        if ($material1 == 0) {
                            $material1 = "Hi index";
                        } elseif ($material1 == 1) {
                            $material1 = "CR";
                        } elseif ($material1 == 3) {
                            $material1 = $materialotro1;
                        }

                        if ($bifocal1 == 0) {
                            $bifocal1 = "FT";
                        } elseif ($bifocal1 == 1) {
                            $bifocal1 = "Blend";
                        } elseif ($bifocal1 == 2) {
                            $bifocal1 = "Progresivo";
                        } elseif ($bifocal1 == 3) {
                            $bifocal1 = "NA";
                        } elseif ($bifocal1 == 4) {
                            $bifocal1 = $bifocalotro1;
                        }

                        //Validacion tratamientos
                        if ($fotocromatico1 == 1) {
                            $tratamientos1 = "Fotocromatico|";
                        }
                        if ($ar1 == 1) {
                            $tratamientos1 = $tratamientos1 . "AR|";
                        }
                        if ($tinte1 == 1) {
                            $tratamientos1 = $tratamientos1 . "Tinte|";
                        }
                        if ($blueray1 == 1) {
                            $tratamientos1 = $tratamientos1 . "BlueRay|";
                        }
                        if ($otroT1 == 1) {
                            $tratamientos1 = $tratamientos1 . $tratamientootro1 . "|";
                        }
                        $tratamientos1 = substr_replace($tratamientos1, '', -1); //Quitar el ultimo |


                    } else {

                        $idHistorial2 = $historialClinicoImpresora->id;
                        $fechaEntregaHistorial2 = $historialClinicoImpresora->fechaentrega;
                        $nombreProducto2 = $laboratorio::remplazarCaracteres($producto[0]->nombre);
                        $colorProducto2 = $laboratorio::remplazarCaracteres($producto[0]->color);
                        $observacionesHistorial2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->observaciones);
                        //Ojo derecho
                        $esfericoder2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->esfericoder);
                        $cilindroder2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->cilindroder);
                        $ejeder2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->ejeder);
                        $addder2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->addder);
                        $altder2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->altder);
                        //Ojo izquierdo
                        $esfericoizq2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->esfericoizq);
                        $cilindroizq2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->cilindroizq);
                        $ejeizq2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->ejeizq);
                        $addizq2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->addizq);
                        $altizq2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->altizq);

                        if ($esfericoder2 == null) {
                            $esfericoder2 = "NA";
                        }
                        if ($cilindroder2 == null) {
                            $cilindroder2 = "NA";
                        }
                        if ($ejeder2 == null) {
                            $ejeder2 = "NA";
                        }
                        if ($addder2 == null) {
                            $addder2 = "NA";
                        }
                        if ($altder2 == null) {
                            $altder2 = "NA";
                        }
                        if ($esfericoizq2 == null) {
                            $esfericoizq2 = "NA";
                        }
                        if ($cilindroizq2 == null) {
                            $cilindroizq2 = "NA";
                        }
                        if ($ejeizq2 == null) {
                            $ejeizq2 = "NA";
                        }
                        if ($addizq2 == null) {
                            $addizq2 = "NA";
                        }
                        if ($altizq2 == null) {
                            $altizq2 = "NA";
                        }

                        //Material
                        $material2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->material);
                        $materialotro2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->materialotro);
                        //Bifocal
                        $bifocal2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->bifocal);
                        $bifocalotro2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->bifocalotro);
                        //Tratamientos
                        $fotocromatico2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->fotocromatico);
                        $ar2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->ar);
                        $tinte2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->tinte);
                        $blueray2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->blueray);
                        $otroT2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->otroT);
                        $tratamientootro2 = $laboratorio::remplazarCaracteres($historialClinicoImpresora->tratamientootro);

                        if ($material2 == 0) {
                            $material2 = "Hi index";
                        } elseif ($material2 == 1) {
                            $material2 = "CR";
                        } elseif ($material2 == 3) {
                            $material2 = $materialotro2;
                        }

                        if ($bifocal2 == 0) {
                            $bifocal2 = "FT";
                        } elseif ($bifocal2 == 1) {
                            $bifocal2 = "Blend";
                        } elseif ($bifocal2 == 2) {
                            $bifocal2 = "Progresivo";
                        } elseif ($bifocal2 == 3) {
                            $bifocal2 = "NA";
                        } elseif ($bifocal2 == 4) {
                            $bifocal2 = $bifocalotro2;
                        }

                        $tratamientos2 = "";
                        //Validacion tratamientos
                        if ($fotocromatico2 == 1) {
                            $tratamientos2 = "Fotocromatico|";
                        }
                        if ($ar2 == 1) {
                            $tratamientos2 = $tratamientos2 . "AR|";
                        }
                        if ($tinte2 == 1) {
                            $tratamientos2 = $tratamientos2 . "Tinte|";
                        }
                        if ($blueray2 == 1) {
                            $tratamientos2 = $tratamientos2 . "BlueRay|";
                        }
                        if ($otroT2 == 1) {
                            $tratamientos2 = $tratamientos2 . $tratamientootro2 . "|";
                        }
                        $tratamientos2 = substr_replace($tratamientos2, '', -1); //Quitar el ultimo |
                    }

                    $contadorHistorial++;

                } else {
                    $datos[0]["codigo"] = "ERROR"; //indicara que tuvimos un error las validaciones
                    $datos[0]["validacion"] = "No existe el producto";
                    return "{'datos':'" . base64_encode(json_encode($datos)) . "'}";
                }

            }

        } else {
            $datos[0]["codigo"] = "ERROR";   //No existe el contrato
            $datos[0]["validacion"] = "No existe el contrato";
            return "{'datos':'" . base64_encode(json_encode($datos)) . "'}";
        }

        $datosTicket = null;
        $datosTicket[0]["ciudad"] = $contrato[0]->ciudad;
        $datosTicket[0]["zona"] = $contrato[0]->zona;
        $datosTicket[0]["idContrato"] = $idContrato;
        $datosTicket[0]["idHistorial1"] = $idHistorial1;
        $datosTicket[0]["fechaEntregaHistorial1"] = $fechaEntregaHistorial1;
        $datosTicket[0]["nombreProducto1"] = $nombreProducto1;
        $datosTicket[0]["colorProducto1"] = $colorProducto1;
        $datosTicket[0]["observacionesHistorial1"] = $observacionesHistorial1;
        $datosTicket[0]["esfericoder1"] = $esfericoder1;
        $datosTicket[0]["cilindroder1"] = $cilindroder1;
        $datosTicket[0]["ejeder1"] = $ejeder1;
        $datosTicket[0]["addder1"] = $addder1;
        $datosTicket[0]["altder1"] = $altder1;
        $datosTicket[0]["esfericoizq1"] = $esfericoizq1;
        $datosTicket[0]["cilindroizq1"] = $cilindroizq1;
        $datosTicket[0]["ejeizq1"] = $ejeizq1;
        $datosTicket[0]["addizq1"] = $addizq1;
        $datosTicket[0]["altizq1"] =$altizq1 ;
        $datosTicket[0]["material1"] = $material1 ;
        $datosTicket[0]["bifocal1"] = $bifocal1 ;
        $datosTicket[0]["tratamientos1"] = $tratamientos1;
        $datosTicket[0]["idHistorial2"] = $idHistorial2;
        $datosTicket[0]["fechaEntregaHistorial2"] = $fechaEntregaHistorial2;
        $datosTicket[0]["nombreProducto2"] = $nombreProducto2;
        $datosTicket[0]["colorProducto2"] = $colorProducto2;
        $datosTicket[0]["observacionesHistorial2"] = $observacionesHistorial2;
        $datosTicket[0]["esfericoder2"] = $esfericoder2;
        $datosTicket[0]["cilindroder2"] = $cilindroder2;
        $datosTicket[0]["ejeder2"] = $ejeder2;
        $datosTicket[0]["addder2"] = $addder2;
        $datosTicket[0]["altder2"] = $altder2;
        $datosTicket[0]["esfericoizq2"] = $esfericoizq2;
        $datosTicket[0]["cilindroizq2"] = $cilindroizq2;
        $datosTicket[0]["ejeizq2"] = $ejeizq2;
        $datosTicket[0]["addizq2"] = $addizq2;
        $datosTicket[0]["altizq2"] = $altizq2;
        $datosTicket[0]["material2"] = $material2;
        $datosTicket[0]["bifocal2"] = $bifocal2;
        $datosTicket[0]["tratamientos2"] = $tratamientos2;
        $datosTicket[0]["estadoGarantia"] = $estadoGarantia;
        $datosTicket[0]["comentariosContrato"] = $comentariosContrato;

        $datos[0]["codigo"] = "LOLATV3";    //Datos correctamente obtenidos
        $datos[0]["contrato"] = $contrato;
        $datos[0]["comentarios"] = $comentarios;
        $datos[0]["historialClinico"] = $historialClinico;
        $datos[0]["historialcontrato"] = $historialContrato;
        $datos[0]["datosTicket"] = $datosTicket;
        $datos[0]["armazones"] = $armazones;
        $datos[0]["listaArmazonesContrato"] = $listaArmazonesContrato;

        } else {
            //Sesion ya caducada o no cumple con alguna validacion de estatus activo
            $datos[0]["codigo"] =  $verificacionRespuesta;
        }

        return "{'datos':'" . base64_encode(json_encode($datos)) . "'}";

    }


    //------------------------------------------------SECCION DE CONTRATOS EN ESTATUS DE ENVIADO ----------------------------------------


    public function listaSucursales(){

        $sucursales = DB::select("SELECT f.id, f.ciudad FROM franquicias f WHERE f.id != '00000' ORDER BY f.ciudad ASC");

        if(sizeof($sucursales) > 0 ){
            $datos[0]["bandera"] = "1"; //indicara que al menos obtuvimos un resultado
            $datos[0]["sucursales"] = $sucursales;
        } else {
            $datos[0]["bandera"] = "0"; //Indicara que no existen resultados en el arreglo
        }

        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
    }

    public function filtrarContratosEnviados(){
        $filtro = request("filtro");
        $fechaInicial = request("fechaInicial");
        $fechaFinal = request("fechaFinal");
        $franquiciaSeleccionada = request("sucursalSeleccionada");
        $dispositivo = request("dispositivo");
        $correo = request("correo");
        $idUnico = request("idUnico");
        $token = request("token");

        $verificacionRespuesta = self::verificarSesionActiva($dispositivo,$correo,$idUnico,$token);
        $datos = [];

        //Verificacion de sesion activa correcta?
        if($verificacionRespuesta== "LOLATV9"){

            $cadenaFranquiciaSeleccionada = "";
            $cadenaFiltro = "";

            //Cadena de filtro por ID contratos
            if($filtro != null){
                $cadenaFiltro = " AND c.id like '%$filtro%' ";
            }

            //Cadena de filtro de periodo de fecha

            //Dar formato de fecha
            $fechaInicial = Carbon::parse($fechaInicial)->format('Y-m-d');

            //Dar formato de fecha
            $fechaFinal = Carbon::parse($fechaFinal)->format('Y-m-d');

            //Cadena de sucursal seleccionada
            if($franquiciaSeleccionada != null){
                $cadenaFranquiciaSeleccionada = " AND c.id_franquicia = '$franquiciaSeleccionada' ";

            } else {
                //Todas las sucursales diferentes a la de pruebas
                $cadenaFranquiciaSeleccionada = " AND c.id_franquicia != '00000' ";
            }



            $consulta = "SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,f.ciudad,c.created_at,
                                                                (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as fechaentrega,
                                                                r.created_at as fechaenvio
                                                                FROM contratos c
                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
                                                                INNER JOIN registroestadocontrato r ON r.id_contrato = c.id
                                                                WHERE c.banderacomentarioconfirmacion != 2 AND
                                                                c.estatus_estadocontrato = 12
                                                                AND STR_TO_DATE(r.created_at,'%Y-%m-%d') >= STR_TO_DATE('$fechaInicial','%Y-%m-%d')
                                                                AND STR_TO_DATE(r.created_at,'%Y-%m-%d') <= STR_TO_DATE('$fechaFinal','%Y-%m-%d')
                                                                " . $cadenaFranquiciaSeleccionada . "
                                                                " . $cadenaFiltro . "
                                                                ORDER BY f.ciudad ASC, fechaentrega ASC ";

            $contratosEnviados = DB::select($consulta);


            if(sizeof($contratosEnviados) > 0 ){
                //Arraglo tiene datos
                $datos[0]["codigo"] = "LOLATV3";    //Datos correctamente obtenidos
                $datos[0]["contratosEnviados"] = $contratosEnviados;
            }else {
                //Arreglo vacio
                $datos[0]["codigo"] = "SIN RESULTADOS"; //Indicara que no existen resultados en el arreglo
            }
        } else {
            //Sesion ya caducada o no cumple con alguna validacion de estatus activo
            $datos[0]["codigo"] =  $verificacionRespuesta;
        }
        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";

    }


/* METODOS ACTUALIZAR ESTADO, AGREGAR COMENTARIOS, CANCELAR GARANTIAS, RECHAZAR CONTRATO, ACTUALIZAR ARMAZON, AGREGAR ARMAZON, AGREGAR MOVIMIENTO, ACTUALIZAR OBSERVACIONES */

    //Funcion: actualizarContratoLaboratorio
    //Descripcion: Recibe los datos desde la app escritorio valida la sesion y posteriomente dependiendo la opcion recibida manda llamar el metodo de actualizacion que se requiera
    public function actualizarContratoLaboratorio(){

        //Opciones para cambioRealizar
        //1 -> Cambio de estatus a contrato
        //2 -> Agregar comentario al contrato
        //3 -> Rechazar contrato
        //4 -> Cancelar garantia
        //5 -> Actualizar armazon
        //6 -> Agregar armazon contrato
        //7 -> Agregar movimiento contrato
        //8 -> Actualizar observaciones

        //Datos recibidos desde app
        $dispositivo = request("dispositivo");
        $correo = request("correo");
        $idUnico = request("idUnico");
        $token = request("token");
        $cambioRealizar = request("OpcionActualizar");
        $idContrato = request("idContrato");
        $idUsuario = request("idUsuario");

        //Validamos el estatus de sesion para la peticion
        $verificacionRespuesta = self::verificarSesionActiva($dispositivo,$correo,$idUnico,$token);
        $datos = [];

        //Sesion activa correcta?
        if($verificacionRespuesta== "LOLATV9"){

            //Generamos solicitud para hacer el cambio sobre el contrato
            switch ($cambioRealizar){
                case '1':
                    //Actualizar Estado del contrato
                    $nuevoEstado = request("nuevoEstado");
                    $resultadoActualizacion = self::actualizarEstadoContrato($idContrato,$nuevoEstado, $idUsuario);
                    $datos[0]["codigo"] =  $verificacionRespuesta;
                    $datos[0]["mensaje"] = $resultadoActualizacion;
                    break;

                case '2':
                    //Agregar comentario al contrato
                    $comentario = request("comentario");
                    $resultadoActualizacion = self::comentariolaboratorio($idContrato, $comentario, $idUsuario);
                    $datos[0]["codigo"] =  $verificacionRespuesta;
                    $datos[0]["mensaje"] = $resultadoActualizacion;
                    break;

                case '3':
                    //Rechazar contrato
                    $comentario = request("comentario");
                    $resultadoActualizacion = self::rechazarContratoLaboratorio($idContrato, $comentario, $idUsuario);
                    $datos[0]["codigo"] =  $verificacionRespuesta;
                    $datos[0]["mensaje"] = $resultadoActualizacion;
                    break;

                case '4':
                    //Cancelar garantia contrato
                    $idHistorial = request("idHistorialClinico");
                    $resultadoActualizacion = self::cancelarGarantiaHistorialLaboratorio($idContrato, $idHistorial, $idUsuario);
                    $datos[0]["codigo"] =  $verificacionRespuesta;
                    $datos[0]["mensaje"] = $resultadoActualizacion;
                    break;

                case '5':
                    //Actualizar armazon
                    $idArmazon = request("idArmazon");
                    $idHistorial = request("idHistorial");
                    $resultadoActualizacion = self::actualizarArmazonContrato($idContrato, $idHistorial, $idArmazon, $idUsuario);
                    $datos[0]["codigo"] =  $verificacionRespuesta;
                    $datos[0]["mensaje"] = $resultadoActualizacion;
                    break;

                case '6':
                    $idArmazon = request("idArmazon");
                    $resultadoActualizacion = self::agregarproductoarmazoncontratolaboratorio($idContrato, $idArmazon, $idUsuario);
                    $datos[0]["codigo"] =  $verificacionRespuesta;
                    $datos[0]["mensaje"] = $resultadoActualizacion;
                    break;

                case '7':
                    $idArmazon = request("movimiento");
                    $resultadoActualizacion = self::agregarhistorialmovimientolaboratorio($idContrato, $idArmazon, $idUsuario);
                    $datos[0]["codigo"] =  $verificacionRespuesta;
                    $datos[0]["mensaje"] = $resultadoActualizacion;
                    break;

                case '8':
                    $idArmazon = request("idHistorial");
                    $observaciones = request("observaciones");
                    $resultadoActualizacion = self::actualizarObservacionesHitorialClinicoContrato($idContrato, $idArmazon, $observaciones, $idUsuario);
                    $datos[0]["codigo"] =  $verificacionRespuesta;
                    $datos[0]["mensaje"] = $resultadoActualizacion;
                    break;

            }

        }else {
            //Sesion ya caducada o no cumple con alguna validacion de estatus activo
            $datos[0]["codigo"] =  $verificacionRespuesta;
        }

        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
    }


    //Funcion: actualizarEstadoContrato
    public function actualizarEstadoContrato($idContrato, $estatus, $idUsuarioC){
        $laboratorio = new laboratorio;
        $resultado = "";

        $contrato = DB::select("SELECT datos, estatus_estadocontrato, pago, id_franquicia FROM contratos WHERE id = '$idContrato'");

        if ($contrato != null) {
            //Si $estatus es menor al estatus_estadocontrato no se puede actualizar
            if($contrato[0]->estatus_estadocontrato > $estatus){
                $resultado = "No puedes cambiar el estatus del contrato a un estatus anterior.";
                return $resultado;
            } if($contrato[0]->datos == 0){
                $resultado = "No se encontro el contrato.";
                return $resultado;
            } if ($contrato[0]->estatus_estadocontrato == 12) {
                $resultado = "No puedes cambiar el estatus del contrato en este momento.";
                return $resultado;
            } else {
                if ($contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11) {

                    $contratosGlobal = new contratosGlobal;

                    DB::table("contratos")->where("id", "=", $idContrato)->update([
                        "estatus_estadocontrato" => $estatus,
                        "costoatraso" => 0
                    ]);

                    if ($estatus == 12) {
                        DB::table("garantias")->where("id_contrato", "=", $idContrato)->update([
                            "estadogarantia" => 3
                        ]);

                        //Actualizar abono minimo contrato
                        $pago = $contrato[0]->pago;
                        $idFranquicia_contrato = $contrato[0]->id_franquicia;
                        if($pago != 0){
                            //Si forma de pago es diferente de contado
                            $abonominimo = $contratosGlobal::calculoCantidadFormaDePago($idFranquicia_contrato, $pago);

                            //Generar actualizacion abono minimo contrato
                            DB::table("contratos")->where("id", "=", $idContrato)->update([
                                "abonominimo" => $abonominimo
                            ]);
                        }

                        //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                        $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $idUsuarioC);

                        //Eliminamos contrato de tabla contratoslaboratorio
                        $contratosGlobal::actualizarRegistroTablaContratosLaboratorio($idContrato, "ELIMINAR");

                        //Insertar o actualizar contrato tabla contratoslistatemporales
                        $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);
                    }

                    $estatusContrato = $laboratorio::obtenerEstatusContrato($estatus);
                    $globalesServicioWeb = new globalesServicioWeb;

                    DB::table('historialcontrato')->insert([
                        'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $idUsuarioC,
                        'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                        'cambios' => " L - Cambio el estatus a '$estatusContrato'"
                    ]);

                    //Insertar en tabla registroestadocontrato
                    DB::table('registroestadocontrato')->insert([
                        'id_contrato' => $idContrato,
                        'estatuscontrato' => $estatus,
                        'created_at' => Carbon::now()
                    ]);

                    if($estatus != 12){
                        //Estatus de aprobado - manufactura o en proceso de envio - Actualizar fecha manufactura si es necesario
                        $contratosGlobal::actualizarRegistroTablaContratosLaboratorio($idContrato, "INSERTAR");
                    }

                    $tieneGarantia = DB::select("SELECT id FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia = 2");
                    if($tieneGarantia != null) {
                        //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                        $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $idUsuarioC);

                        //Insertar o actualizar contrato tabla contratoslistatemporales
                        $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);
                    }

                    $resultado = "El contrato cambio  a " . $estatusContrato;

                } else {
                    $resultado = "Necesitas permisos adicionales para hacer esto.";
                }
            }
        } else {
            //El contrato no existe
            $resultado = "El contrato no es valido.";
        }

        return $resultado;
    }

    //Funcion: comentariolaboratorio
    //Descripcion: Agrega un comentario al contrato
    public function comentariolaboratorio($idContrato, $comentario, $idUsuario)
    {
        $resultado = "";
        $contrato = DB::select("SELECT c.estatus_estadocontrato
                                          FROM contratos c
                                          WHERE c.id = '$idContrato'");
        if ($contrato != null) {
            if ($contrato[0]->estatus_estadocontrato != 7 && $contrato[0]->estatus_estadocontrato != 10 && $contrato[0]->estatus_estadocontrato != 11) {
                $resultado = "Ya no tienes permisos de agregar comentariros.";

            }else {
                try {
                    $ahora = Carbon::now();
                    DB::table('mensajesconfirmaciones')->insert([
                        "id_contrato" => $idContrato, "id_usuario" => $idUsuario, "comentario" => $comentario, "fecha" => $ahora
                    ]);

                    DB::table("contratos")->where("id", "=", $idContrato)->update([
                        "banderacomentarioconfirmacion" => 3
                    ]);

                    $resultado = "El mensaje se guardo correctamente";
                } catch (\Exception $e) {
                    return back()->with("alerta", "Error: " . $e);
                }
            }
        }

        return $resultado;
    }

    //Funcion: rechazarContratoLaboratorio
    //Descripcion: Permite rechazar un cntrato siempre y cuando no tenga garantias registradas
    public function rechazarContratoLaboratorio($idContrato, $comentarios, $usuarioId)
    {
        $resultado = "";
        $contrato = DB::select("SELECT estatus_estadocontrato FROM contratos WHERE id = '$idContrato'");

        if ($contrato != null) {

            $existeGarantia = DB::select("SELECT * FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia = '2' ORDER BY created_at ASC limit 1");

            if($existeGarantia == null){

                if ($contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11) {

                    $actualizar = Carbon::now();

                    $globalesServicioWeb = new globalesServicioWeb;
                    $idHistorialContratoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');

                    try {

                        DB::table('contratos')->where('id', '=', $idContrato)->update([
                            'estatus_estadocontrato' => 8, 'estatusanteriorcontrato' => $contrato[0]->estatus_estadocontrato
                        ]);

                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $idContrato,
                            'estatuscontrato' => 8,
                            'created_at' => Carbon::now()
                        ]);

                        DB::table('historialcontrato')->insert([
                            'id' => $idHistorialContratoAlfanumerico, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar,
                            'cambios' => "L - Rechazo con la siguiente descripcion: '$comentarios'"
                        ]);

                        //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato
                        DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");

                        //Eliminamos contrato de tabla contratoslaboratorio
                        $contratosGlobal = new contratosGlobal;
                        $contratosGlobal::actualizarRegistroTablaContratosLaboratorio($idContrato, "ELIMINAR");

                        $resultado = 'El contrato se rechazo correctamente.';

                    } catch (\Exception $e) {
                        \Log::info("Error: " . $e->getMessage());
                        $resultado = 'Tuvimos un problema, por favor contacta al administrador de la pagina.';
                    }

                } else {
                    $resultado = 'Necesitas permisos adicionales para hacer esto.';
                }

            }else{
                //El contrato contiene garantia
                $resultado = 'No se puedes rechazar el contrato debido a que tiene garantia.';
            }
        }
        else {
            $resultado = 'No se encontro el contrato.';
        }
        return $resultado;
    }

    //Funcion: cancelarGarantiaHistorialLaboratorio
    //Descripcion: Se genera la cancelacion de la garantia para un historial clinico, registra el movimiento del historial
    public function cancelarGarantiaHistorialLaboratorio($idContrato, $idHistorial, $usuarioId)
    {

        $resultado = "";
        $laboratorio = new laboratorio;

        try {

            $datosHistorial = DB::select("SELECT id_contrato FROM historialclinico WHERE id = '$idHistorial'  AND id_contrato = '$idContrato'");

            if ($datosHistorial != null) {
                $idContrato = $datosHistorial[0]->id_contrato;

                $contrato = DB::select("SELECT estatus_estadocontrato FROM contratos WHERE id = '$idContrato'");

                if ($contrato != null) {
                    //Existe el contrato

                    if ($contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11 ||
                        $contrato[0]->estatus_estadocontrato == 12) {
                        //MANOFACTURA, PROCESO DE ENVIO, ENVIADO

                        $garantiasCancelar = DB::select("SELECT id, id_historial, estadocontratogarantia, totalhistorialcontratogarantia, totalpromocioncontratogarantia,
                                                                        totalrealcontratogarantia FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia = 2");

                        if ($garantiasCancelar != null) {//Tiene garantias para cancelar?
                            //Tiene garantias para cancelar

                            $globalesServicioWeb = new globalesServicioWeb;


                            foreach ($garantiasCancelar as $garantiaCancelar) {

                                $idGarantia = $garantiaCancelar->id;
                                $idhistorial = $garantiaCancelar->id_historial;
                                $estadocontratogarantia = $garantiaCancelar->estadocontratogarantia;
                                $totalhistorialcontratogarantia = $garantiaCancelar->totalhistorialcontratogarantia;
                                $totalpromocioncontratogarantia = $garantiaCancelar->totalpromocioncontratogarantia;
                                $totalrealcontratogarantia = $garantiaCancelar->totalrealcontratogarantia;

                                //Ya se habian creado las garantias
                                $contrato = DB::select("SELECT totalhistorial, totalpromocion, totalabono, totalproducto FROM contratos WHERE id = '$idContrato'");

                                if ($contrato != null) {
                                    //Se encontro el contrato
                                    $totalhistorial = $contrato[0]->totalhistorial;
                                    $totalpromocion = $contrato[0]->totalpromocion;
                                    $totalabono = $contrato[0]->totalabono;
                                    $totalproducto = $contrato[0]->totalproducto;

                                    if ($laboratorio->obtenerEstadoPromocion($idContrato)) {
                                        //Tiene promocion
                                        if ($totalpromocion > $totalpromocioncontratogarantia) {
                                            //Devolver el estado del contrato, el total, y el totalpromocion a como estaban
                                            DB::table('contratos')->where('id', '=', $idContrato)->update([
                                                'estatus_estadocontrato' => $estadocontratogarantia,
                                                'total' => $totalpromocioncontratogarantia + $totalproducto - $totalabono,
                                                'totalpromocion' => $totalpromocioncontratogarantia,
                                                'totalhistorial' => $totalhistorialcontratogarantia,
                                                'totalreal' => $totalrealcontratogarantia
                                            ]);
                                        } else {
                                            //Devolver el estado del contrato
                                            DB::table('contratos')->where('id', '=', $idContrato)->update([
                                                'estatus_estadocontrato' => $estadocontratogarantia
                                            ]);
                                        }

                                    } else {
                                        //No tiene promocion
                                        if ($totalhistorial > $totalhistorialcontratogarantia) {
                                            //Devolver el estado del contrato, el total, y el totalhistorial a como estaban
                                            DB::table('contratos')->where('id', '=', $idContrato)->update([
                                                'estatus_estadocontrato' => $estadocontratogarantia,
                                                'total' => $totalhistorialcontratogarantia + $totalproducto - $totalabono,
                                                'totalhistorial' => $totalhistorialcontratogarantia,
                                                'totalpromocion' => $totalpromocioncontratogarantia,
                                                'totalreal' => $totalrealcontratogarantia
                                            ]);
                                        } else {
                                            //Devolver el estado del contrato
                                            DB::table('contratos')->where('id', '=', $idContrato)->update([
                                                'estatus_estadocontrato' => $estadocontratogarantia
                                            ]);
                                        }

                                    }

                                    //Insertar en tabla registroestadocontrato
                                    DB::table('registroestadocontrato')->insert([
                                        'id_contrato' => $idContrato,
                                        'estatuscontrato' => $estadocontratogarantia,
                                        'created_at' => Carbon::now()
                                    ]);

                                } else {
                                    return $resultado =  'No se encontro el contrato.';
                                }

                                //Actualizar estadogarantia a 4
                                DB::table('garantias')->where([['id', '=', $idGarantia], ['id_contrato', '=', $idContrato], ['id_historial', '=', $idhistorial]])->update([
                                    'estadogarantia' => 4,
                                    'updated_at' => Carbon::now()
                                ]);
                                //Guardar movimiento
                                DB::table('historialcontrato')->insert([
                                    'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato,
                                    'created_at' => Carbon::now(), 'cambios' => " Cancelo la garantia al historial '$idhistorial'"
                                ]);

                                //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato
                                DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");

                                //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                                $contratosGlobal = new contratosGlobal;
                                $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $usuarioId);

                                //Eliminamos contrato de tabla contratoslaboratorio
                                $contratosGlobal::actualizarRegistroTablaContratosLaboratorio($idContrato, "ELIMINAR");

                                //Insertar o actualizar contrato tabla contratoslistatemporales
                                $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                            }

                            $resultado = 'Se cancelo correctamente la garantia.';
                        } else {
                            //No tiene garantias para cancelar
                            $resultado =  'No se puede cancelar la garantia por que no tiene asignada.';
                        }

                    } else {
                        //Estado del contrato no perteneciente a laboratorio
                        $resultado = "Necesitas permisos adicionales para hacer esto.";
                    }

                } else {
                    //Contrato no existe
                    $resultado = "No se encontro el contrato.";
                }
            } else {
                //No presenta historial clinico el contrato
                $resultado = "No se encontro el historial clinico del contrato";
            }

        } catch (\Exception $e) {
            $resultado = 'Tuvimos un problema, por favor contacta al administrador de la pagina.';
        }

        return $resultado;
    }

    public function actualizarArmazonContrato($idContrato, $idHistorial, $idArmazon, $usuarioId)
    {
        $resultado = "";

        try {
            $existeArmazon = DB::select("SELECT * FROM producto WHERE id = '$idArmazon' AND id_tipoproducto = '1' LIMIT 1");

            if($existeArmazon != null){
                //Si existe el armazon

                $contrato = DB::select("SELECT estatus_estadocontrato FROM contratos WHERE id = '$idContrato'");

                if ($contrato != null) {
                    //Validar el estatus actual del contrato

                    $datosHistorial = DB::select("SELECT id, id_producto, tipo FROM historialclinico WHERE id = '$idHistorial' AND id_contrato = '$idContrato'");

                    if ($datosHistorial != null) {
                        //Existe el historial

                        try {

                            //Obtener producto actual del historial
                            $idArmazonActual = $datosHistorial[0]->id_producto;

                            if ($idArmazonActual == $idArmazon) {
                                //No se podra actualizar al mismo armazon
                                $resultado = 'No se puede actualizar al mismo armazón.';
                            }

                            $armazonActual = DB::select("SELECT * FROM producto WHERE id = '$idArmazonActual' AND id_tipoproducto = '1'");

                            //Sumarle una pieza al producto que se quito
                            DB::table('producto')->where('id', '=', $idArmazonActual)->update([
                                'piezas' => $armazonActual[0]->piezas + 1
                            ]);

                            //Restarle una pieza al producto que se actualizo
                            DB::table('producto')->where('id', '=', $idArmazon)->update([
                                'piezas' => $existeArmazon[0]->piezas - 1
                            ]);

                            $globalesServicioWeb = new globalesServicioWeb;
                            //Guardar movimiento
                            DB::table('historialcontrato')->insert([
                                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato,
                                'created_at' => Carbon::now(), 'cambios' => " Modifico el historial clinico: '$idHistorial', Se cambio el armazon"
                            ]);

                            //Validar si el historial es garantia o no
                            if ($datosHistorial[0]->tipo == 1) {
                                //Es garantia
                                $historialPadre = DB::select("SELECT id_historial FROM garantias
                                                                        WHERE id_contrato = '$idContrato'
                                                                        AND id_historialgarantia = '$idHistorial'
                                                                        AND estadogarantia = 2
                                                                        ORDER BY created_at DESC LIMIT 1");
                                if ($historialPadre != null) {
                                    //Actualizar id_producto en historialclinico padre
                                    DB::table('historialclinico')->where([['id_contrato', '=', $idContrato], ['id', '=', $historialPadre[0]->id_historial]])->update([
                                        'id_producto' => $idArmazon
                                    ]);

                                    //Actualizar id_producto en historiales clinicos garantias
                                    DB::update("UPDATE historialclinico hc
                                                        INNER JOIN garantias g ON g.id_historialgarantia = hc.id
                                                        SET hc.id_producto = '$idArmazon'
                                                        WHERE g.id_historial = '" . $historialPadre[0]->id_historial . "' AND hc.id_contrato = '$idContrato'");
                                }
                            }else {
                                //No es garantia
                                //Actualizar id_producto en historialclinico
                                DB::table('historialclinico')->where([['id_contrato', '=', $idContrato], ['id', '=', $idHistorial]])->update([
                                    'id_producto' => $idArmazon
                                ]);
                            }

                            $resultado = 'El historial clinico se actualizo correctamente.';
                        } catch (\Exception $e) {
                            \Log::info("Error: " . $e->getMessage());
                            $resultado = 'Tuvimos un problema, por favor contacta al administrador de la pagina.';
                        }

                    }else{
                        //NO existe el historialclinico
                        $resultado = 'No se encontro el historial.';
                    }

                }else{
                    //NO existe el contrato
                    $resultado = 'No se encontro el contrato.';
                }

            }else {
                //No existe la armazon
                $resultado = 'El armazón seleccionado no es válido.';
            }

        } catch (\Exception $e) {
            $resultado = 'Tuvimos un problema, por favor contacta al administrador de la pagina.';
        }

        return $resultado;
    }

    public function agregarproductoarmazoncontratolaboratorio($idContrato, $producto, $idUsuario){

        $resultado = "";

        if ($producto == null) {
            $resultado = "Por favor seleccionar un armazón";
        }

        $existeArmazon = DB::select("SELECT * FROM producto WHERE id = '$producto' AND id_tipoproducto = '1' LIMIT 1");

        if($existeArmazon != null){
            //Si existe el armazon

            $contrato = DB::select("SELECT id_franquicia, id_zona FROM contratos WHERE id = '$idContrato'");

            if ($contrato != null) {
                //Validar el estatus actual del contrato

                $idFranquicia = $contrato[0]->id_franquicia;

                try {

                    $globalesServicioWeb = new globalesServicioWeb;

                    $idcontratoproducto = $globalesServicioWeb::generarIdAlfanumerico('contratoproducto', '5');
                    //Agregar producto a tabla contratoproducto
                    DB::table('contratoproducto')->insert([
                        'id' => $idcontratoproducto, 'id_franquicia' => $idFranquicia, 'id_contrato' => $idContrato,
                        'id_usuario' => $idUsuario, 'id_producto' => $producto, 'piezas' => 1, 'total' => 0, 'created_at' => Carbon::now()
                    ]);

                    //Restarle una pieza al producto que se actualizo
                    DB::table('producto')->where('id', '=', $producto)->update([
                        'piezas' => $existeArmazon[0]->piezas - 1
                    ]);

                    //Guardar movimiento producto
                    DB::table('historialcontrato')->insert([
                        'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $idUsuario, 'id_contrato' => $idContrato,
                        'created_at' => Carbon::now(), 'cambios' => " Agrego el armazón: '" . $existeArmazon[0]->id . "'-'" . $existeArmazon[0]->nombre . "' cantidad de piezas: '1'"
                    ]);

                    //Agregar abono al contrato
                    DB::table('abonos')->insert([
                        'id' => $globalesServicioWeb::generarIdAlfanumerico('abonos', '5'),
                        'folio' => null,
                        'id_franquicia' => $idFranquicia,
                        'id_contrato' => $idContrato,
                        'id_usuario' => $idUsuario,
                        'tipoabono' => 7,
                        'abono' => 0,
                        'metodopago' => 0,
                        'adelantos' => 0,
                        'corte' => 2,
                        'id_contratoproducto' => $idcontratoproducto,
                        "id_zona" => $contrato[0]->id_zona,
                        'created_at' => Carbon::now()
                    ]);

                    //Guardar movimiento abono
                    DB::table('historialcontrato')->insert([
                        'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $idUsuario, 'id_contrato' => $idContrato,
                        'created_at' => Carbon::now(), 'cambios' => " Agrego el abono : '0'"
                    ]);

                    $resultado = "El armazón se agrego correctamente.";

                } catch (\Exception $e) {
                    \Log::info("Error: " . $e->getMessage());
                    $resultado = 'Tuvimos un problema, por favor contacta al administrador de la pagina.';
                }

            }else{
                //NO existe el contrato
                $resultado =  "No se encontro el contrato";
            }

        }else {
            //No existe la armazon
            $resultado = "El armazón seleccionado no es válido.";
        }

        return $resultado;

    }

    public function agregarhistorialmovimientolaboratorio($idContrato, $usuarioId, $movimiento)
    {
        $resultado = "";
            try {

                $contrato = DB::select("SELECT c.estatus_estadocontrato as estatus_estadocontrato
                                                FROM contratos c WHERE c.id = '$idContrato'");

                if ($contrato != null) {
                    //Existe el contrato

                    if (strlen($movimiento) == 0) {
                        $resultado = "Favor de agregar el mensaje de movimiento";
                    }

                    //Guardar en tabla historialcontrato
                    $globalesServicioWeb = new globalesServicioWeb;
                    DB::table('historialcontrato')->insert([
                        'id' => $globalesServicioWeb::generarIdAlfanumerico('contratoproducto', '5'), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                        'cambios' => " Agrego el movimiento: " . $movimiento
                    ]);

                   $resultado = "Se agrego correctamente el movimiento.";

                }else{
                    $resultado = "No se encontro el contrato.";
                }

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                $resultado = "Tuvimos un problema, por favor contacta al administrador de la pagina.";
            }
            return $resultado;
    }

    public function actualizarObservacionesHitorialClinicoContrato($idContrato, $idHistorial, $observaciones, $usuarioId){
        //Validar campo
        $resultado = "";
        if(strlen($observaciones) == 0){
            //Campo vacio
            $resultado ="Ingrese las observaciones para el contrato.";
        }else{
            //Si el campo es correcto
            $existeContrato = DB::select("SELECT c.id, c.estatus_estadocontrato, c.observaciones, c.id_franquicia FROM contratos c WHERE c.id = '$idContrato'");
            if($existeContrato){
                //Existe historial clinico del contrato?
                $existeHistorial = DB::select("SELECT hc.id, hc.id_contrato FROM historialclinico hc WHERE hc.id = '$idHistorial' AND hc.id_contrato = '$idContrato'");
                if($existeHistorial){

                    //El contrato esta en estatus APROBADO, MANUFACTURA, PROCESO DE ENVIO?
                    if($existeContrato[0]->estatus_estadocontrato == 7 || $existeContrato[0]->estatus_estadocontrato == 10 || $existeContrato[0]->estatus_estadocontrato == 11){
                        //El contrato pertenece a un estatus de laboratorio
                        DB::table("historialclinico")->where("id_contrato", "=", $idContrato)->where("id", "=", $idHistorial)
                            ->update(["observaciones" => $observaciones]);

                        //Guardar en tabla historialcontrato
                        $globalesServicioWeb = new globalesServicioWeb;
                        DB::table('historialcontrato')->insert([
                            'id' => $globalesServicioWeb::generarIdAlfanumerico('contratoproducto', '5'), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                            'cambios' => " Actualizo las observaciones."
                        ]);

                        $resultado = "raanestratdato@ittepic.edu.mx";
                    }else{
                        $resultado = "No se puede actualizar las observaciones debido al estatus actual del contrato.";
                    }
                }else{
                    $resultado = "No se encontro el historial del contrato.";
                }

            } else {
                $resultado = "No se encontro el contrato.";
            }
        }

        return $resultado;
    }

    //-----------------------------------------------SECCION DE AUTORIZACIONES ----------------------------------------------
    public function listaSolicitudesAutorizacion(){

        $solicitudesAutorizacion = DB::select("SELECT f.ciudad AS sucursal,a.id_contrato,c.created_at AS fecha_contrato, a.created_at AS fecha_solicitud,
                                                                 c.estatus_estadocontrato AS estado_contrato, u.name AS usuario_solicitud, a.indice,
                                                                a.mensaje AS mensaje, a.tipo FROM autorizaciones a
                                                                INNER JOIN contratos c ON c.id = a.id_contrato
                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
                                                                INNER JOIN users u ON u.id = a.id_usuarioC
                                                                WHERE a.estatus = 0 AND a.tipo IN (6,8,9,10) AND c.id_franquicia != '00000'
                                                                ORDER BY sucursal, a.tipo ASC, fecha_solicitud DESC");

        $datos[0]["solicitudesAutorizacion"] = $solicitudesAutorizacion;

        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
    }

    public function autorizarcontrato()
    {
        //Datos recibidos desde app
        $dispositivo = request("dispositivo");
        $correo = request("correo");
        $idUnico = request("idUnico");
        $token = request("token");
        $accionSolicitud = request("accionSolicitud");
        $idContrato = request("idContrato");
        $indice = request("indice");
        $idUsuarioC= request("idUsuario");

        //Validamos el estatus de sesion para la peticion
        $verificacionRespuesta = self::verificarSesionActiva($dispositivo,$correo,$idUnico,$token);
        $datos = [];
        $mensaje = "";
        $cambios = "";

        $globalesServicioWeb = new globalesServicioWeb;
        $contratosGlobal = new contratosGlobal;

        //Sesion activa correcta?
        if($verificacionRespuesta== "LOLATV9"){
            //Comprobar que contrato pertenezca a sucursal asignada

            $existeContrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato' and c.id_franquicia != '00000'");

            if ($existeContrato != null) {
                //Contrato existe y pertenece a sucursal
                $existeSolicitud = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato = '$idContrato' AND a.estatus = '0' AND a.indice = '$indice'");

                if ($existeSolicitud != null) {
                    //Si existe solicitud

                    //Tipo TRASPASO CONTRATO
                    if($existeSolicitud[0]->tipo == 6){

                        switch ($accionSolicitud){
                            case "AUTORIZAR":
                                $cambios = "Solicitud traspaso de contrato autorizada.";
                                $mensaje = "Solicitud traspaso de contrato autorizada correctamente.";
                                $estatusSolicitud = "1";
                                break;

                            case "RECHAZAR":
                                $cambios = "Solicitud traspaso de contrato rechazada.";
                                $mensaje = "Solicitud traspaso de contrato rechazada correctamente.";
                                $estatusSolicitud = "2";
                                break;

                        }

                        try {
                            //Actualizar a estatus AUTORIZADA
                            DB::table('autorizaciones')->where([['indice', '=', $indice], ['id_contrato', '=', $idContrato]])->update([
                                'estatus' => $estatusSolicitud, 'updated_at' => Carbon::now()
                            ]);

                            //Registrar movimiento
                            $idHistorialContratoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');
                            DB::table('historialcontrato')->insert([
                                'id' => $idHistorialContratoAlfanumerico,
                                'id_usuarioC' => $idUsuarioC,
                                'id_contrato' => $idContrato,
                                'created_at' => Carbon::now(),
                                'cambios' => $cambios,
                                'tipomensaje' => '3'
                            ]);

                        } catch (Exception $e) {
                            Log::info("Error: " . $e->getMessage());
                            $mensaje = 'Tuvimos un error, por favor contacta al administrador de la pagina   Error:' . $e->getMessage();
                        }

                    }

                    //Solicitud de tipo ARMAZON
                    if($existeSolicitud[0]->tipo == 8 || $existeSolicitud[0]->tipo == 9 || $existeSolicitud[0]->tipo == 10){
                        switch ($accionSolicitud){
                            case "AUTORIZAR":
                                try {

                                    $autorizacionarmazonlaboratorio = DB::select("SELECT * FROM autorizacionarmazonlaboratorio WHERE id_autorizacion = '$indice'");

                                    if ($autorizacionarmazonlaboratorio != null) {
                                        //Existe una solicitud de armazon por defecto de fabrica

                                        $producto = DB::select("SELECT * FROM producto WHERE id = '" . $autorizacionarmazonlaboratorio[0]->id_armazon . "'");

                                        if ($producto != null) {
                                            //Existe el producto

                                            $abonototal = 0;
                                            if ($producto[0]->preciop == null) {
                                                //No tiene promocion
                                                if($producto[0]->id_tipoproducto == 1){ //Es un armazon?
                                                    //Productos tipo armazon
                                                    switch ($existeSolicitud[0]->tipo) {
                                                        case 8:
                                                            //Asignar el precio normal del armazon
                                                            $abonototal = $producto[0]->precio * $autorizacionarmazonlaboratorio[0]->piezas;
                                                            break;
                                                        case 9:
                                                            //Aplicar descuento por poliza
                                                            $abonototal = 90 * $autorizacionarmazonlaboratorio[0]->piezas;
                                                            break;
                                                        case 10:
                                                            //Aplicar defecto de fabrica
                                                            $abonototal = 0;
                                                            break;
                                                    }
                                                }else {
                                                    //Producto tipo poliza o gotas
                                                    $abonototal = $producto[0]->precio * $autorizacionarmazonlaboratorio[0]->piezas;
                                                }
                                            } else {
                                                //Tiene promocion
                                                if($producto[0]->id_tipoproducto == 1){
                                                    //Productos tipo armazon
                                                    switch ($existeSolicitud[0]->tipo) {
                                                        case 8:
                                                            //Asignar el precio normal del armazon
                                                            $abonototal = $producto[0]->preciop * $autorizacionarmazonlaboratorio[0]->piezas;
                                                            break;
                                                        case 9:
                                                            //Aplicar descuento por poliza
                                                            $abonototal = 90 * $autorizacionarmazonlaboratorio[0]->piezas;
                                                            break;
                                                        case 10:
                                                            //Aplicar defecto de fabrica
                                                            $abonototal = 0;
                                                            break;
                                                    }
                                                } else {
                                                    //Procuto tipo poliza o gotas
                                                    $abonototal = $producto[0]->preciop * $autorizacionarmazonlaboratorio[0]->piezas;
                                                }
                                            }

                                            $mensajesolicitudarmazon = "";
                                            //Mensaje solicitud historial movimientos
                                            switch ($existeSolicitud[0]->tipo) {
                                                case 9:
                                                    //Aplicar descuento por poliza
                                                    $mensajesolicitudarmazon = " por poliza";
                                                    break;
                                                case 10:
                                                    //Aplicar defecto de fabrica
                                                    $mensajesolicitudarmazon = " por defecto de fábrica";
                                                    break;
                                            }

                                            $idcontratoproducto = $globalesServicioWeb::generarIdAlfanumerico('contratoproducto', '5');
                                            //Agregar producto al contrato
                                            DB::table('contratoproducto')->insertGetId([
                                                'id' => $idcontratoproducto,
                                                'id_franquicia' => $existeContrato[0]->id_franquicia,
                                                'id_contrato' => $idContrato,
                                                'id_usuario' => $idUsuarioC,
                                                'id_producto' => $autorizacionarmazonlaboratorio[0]->id_armazon,
                                                'piezas' => $autorizacionarmazonlaboratorio[0]->piezas,
                                                'total' => $abonototal,
                                                'created_at' => Carbon::now()
                                            ]);

                                            //Descontar pieza del producto
                                            DB::table('producto')->where('id', '=', $producto[0]->id)->update([
                                                'piezas' => $producto[0]->piezas - $autorizacionarmazonlaboratorio[0]->piezas
                                            ]);

                                            $idAbono = $globalesServicioWeb::generarIdAlfanumerico('abonos', '5');

                                            //Agregar abono al contrato
                                            DB::table('abonos')->insert([
                                                'id' => $idAbono,
                                                'folio' => null,
                                                'id_franquicia' => $existeContrato[0]->id_franquicia,
                                                'id_contrato' => $idContrato,
                                                'id_usuario' => $idUsuarioC,
                                                'tipoabono' => 7,
                                                'abono' => $abonototal,
                                                'metodopago' => 0,
                                                'adelantos' => 0,
                                                'corte' => 2,
                                                'id_contratoproducto' => $idcontratoproducto,
                                                "id_zona" => $existeContrato[0]->id_zona,
                                                'created_at' => Carbon::now()
                                            ]);

                                            //Insertar abono en abonoscontratostemporalessincronizacion
                                            $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($existeContrato[0]->id_zona);
                                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                                //Recorrido cobradores
                                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                                    "id" => $idAbono,
                                                    "folio" => null,
                                                    "id_contrato" => $idContrato,
                                                    "id_usuario" => $idUsuarioC,
                                                    "abono" => $abonototal,
                                                    "adelantos" => 0,
                                                    "tipoabono" => 7,
                                                    "atraso" => 0,
                                                    "metodopago" => 0,
                                                    "corte" => 2,
                                                    "created_at" => Carbon::now()
                                                ]);
                                            }

                                            $mensajefoliocambios = $autorizacionarmazonlaboratorio[0]->foliopoliza != null ? " folio: '" . $autorizacionarmazonlaboratorio[0]->foliopoliza . "'" : "";

                                            //Registrar movimientos para Control de armazones
                                            DB::table('historialcontrato')->insert([
                                                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                                                'id_usuarioC' => $idUsuarioC,
                                                'id_contrato' => $idContrato,
                                                'created_at' => Carbon::now(),
                                                'cambios' => "Autorizo el envio de un armazon con identificador: " . $producto[0]->id . " | " . $producto[0]->nombre
                                                    . " | " . $producto[0]->color . " | cantidad de piezas: '" . $autorizacionarmazonlaboratorio[0]->piezas
                                                    . "'" . $mensajesolicitudarmazon . $mensajefoliocambios . ".",
                                                'tipomensaje' => '4'
                                            ]);

                                            //Guardar en historial de movimientos el abono
                                            DB::table('historialcontrato')->insert([
                                                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                                                'id_usuarioC' => $idUsuarioC,
                                                'id_contrato' => $idContrato,
                                                'created_at' => Carbon::now(),
                                                'cambios' => " Se agrego el abono : '$abonototal'"
                                            ]);

                                            //Registrar movimiento
                                            $idHistorialContratoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');
                                            DB::table('historialcontrato')->insert([
                                                'id' => $idHistorialContratoAlfanumerico,
                                                'id_usuarioC' => $idUsuarioC,
                                                'id_contrato' => $idContrato,
                                                'created_at' => Carbon::now(),
                                                'cambios' => "Solicitud de armazón" . $mensajesolicitudarmazon . " rechazada.",
                                                'tipomensaje' => '3'
                                            ]);

                                            //Actualizar a estatus AUTORIZADA
                                            DB::table('autorizaciones')->where([['indice', '=', $indice], ['id_contrato', '=', $idContrato]])->update([
                                                'estatus' => '1', 'updated_at' => Carbon::now()
                                            ]);

                                            $mensaje = " Solicitud de armazón" . $mensajesolicitudarmazon . " autorizada correctamente.";

                                        }else {
                                            //No existe el producto
                                            $mensaje = "No existe ningun registro de armazón para el contrato (Producto no encontrado)";
                                        }
                                    }else {
                                        //No existe un registro de armazon
                                        $mensaje = "No existe ningun registro de armazón para el contrato";
                                    }
                                } catch (\Exception $e) {
                                    \Log::info("Error: " . $e->getMessage());
                                    $mensaje = "Tuvimos un problema, por favor contacta al dministrador de la pagina.";
                                }
                                break;

                            case "RECHAZAR":
                                //Armazón por ninguna, poliza o defecto de fabrica

                                $autorizacionarmazonlaboratorio = DB::select("SELECT * FROM autorizacionarmazonlaboratorio WHERE id_autorizacion = '$indice'");

                                if ($autorizacionarmazonlaboratorio != null) {
                                    //Existe una solicitud de armazon

                                    $producto = DB::select("SELECT * FROM producto WHERE id = '" . $autorizacionarmazonlaboratorio[0]->id_armazon . "'");

                                    if ($producto != null) {
                                        //Existe el producto

                                        $mensajesolicitudarmazon = "";
                                        //Mensaje solicitud historial movimientos
                                        switch ($existeSolicitud[0]->tipo) {
                                            case 9:
                                                //Aplicar descuento por poliza
                                                $mensajesolicitudarmazon = " por poliza";
                                                break;
                                            case 10:
                                                //Aplicar defecto de fabrica
                                                $mensajesolicitudarmazon = " por defecto de fábrica";
                                                break;
                                        }

                                        //Registrar movimientos para Control de armazones
                                        DB::table('historialcontrato')->insert([
                                            'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                                            'id_usuarioC' => $idUsuarioC,
                                            'id_contrato' => $idContrato,
                                            'created_at' => Carbon::now(),
                                            'cambios' => "Rechazo el envio de un armazon con identificador: " . $producto[0]->id . " | " . $producto[0]->nombre
                                                . " | " . $producto[0]->color . " | cantidad de piezas: '" . $autorizacionarmazonlaboratorio[0]->piezas . "'" . $mensajesolicitudarmazon . ".",
                                            'tipomensaje' => '4'
                                        ]);

                                        //Registrar movimiento
                                        $idHistorialContratoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');
                                        DB::table('historialcontrato')->insert([
                                            'id' => $idHistorialContratoAlfanumerico,
                                            'id_usuarioC' => $idUsuarioC,
                                            'id_contrato' => $idContrato,
                                            'created_at' => Carbon::now(),
                                            'cambios' => "Solicitud de armazón" . $mensajesolicitudarmazon . " rechazada.",
                                            'tipomensaje' => '3'
                                        ]);

                                        //Actualizar estatus solicitud a RECHAZADA
                                        DB::table('autorizaciones')->where([['indice', '=', $indice], ['id_contrato', '=', $idContrato]])->update([
                                            'estatus' => '2', 'updated_at' => Carbon::now()
                                        ]);

                                        $mensaje = " Solicitud de armazón" . $mensajesolicitudarmazon . " rechazada correctamente.";

                                    }else {
                                        //No existe el producto
                                        $mensaje = "No existe ningun registro de armazon para el contrato (Producto no encontrado)";
                                    }
                                }else {
                                    //No existe un registro de armazon
                                    $mensaje = "No existe ningun registro de armazon para el contrato";
                                }
                                break;
                        }
                    }

                }else {
                    //No existe ninguna solicitud para el contrato
                    $mensaje = "No existe ninguna solicitud para el contrato";
                }


            } else {
                $mensaje = "No existe el contrato o no pertenece a la sucursal.";
            }
        }else {
            $datos[0]["codigo"] =  $verificacionRespuesta;
        }

        $datos[0]["codigo"] =  $verificacionRespuesta;
        $datos[0]["mensaje"] = $mensaje;

        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";
    }

    //----------------------------------------------------SECCION DE CONTROL DE ARMAZONES ----------------------------------------------
    public function controlArmazonesLaboratorio(Request $request){
        //Datos recibidos desde app
        $dispositivo = request("dispositivo");
        $correo = request("correo");
        $idUnico = request("idUnico");
        $token = request("token");
        $fechaInicio = $request->input('fechaInicio');
        $fechaFinal = $request->input('fechaFinal');

        //Validamos el estatus de sesion para la peticion
        $verificacionRespuesta = self::verificarSesionActiva($dispositivo,$correo,$idUnico,$token);
        $datos = [];

        //Sesion activa correcta?
        if($verificacionRespuesta== "LOLATV9"){
            $hoy = Carbon::now()->format('Y-m-d');

            //Validar fechas
            if(strlen($fechaInicio) == 0){
                $fechaInicio = $hoy;

            } if(strlen($fechaFinal) == 0){
                $fechaFinal = $hoy;
            }

            if (strlen($fechaInicio) > 0) {
                //fechaini diferente de vacio
                $fechaInicio = Carbon::parse($fechaInicio)->format('Y-m-d');
            } if (strlen($fechaFinal) > 0) {
                //fechafin diferente de vacio
                $fechaFinal = Carbon::parse($fechaFinal)->format('Y-m-d');
            }

            //Verificar que el periodo de fechas sea correcto
            if(Carbon::parse($fechaInicio)->format('Y-m-d') > Carbon::parse($fechaFinal)->format('Y-m-d')){
                return redirect()->route('listaproductoslaboratorio')->with('alerta','');
                $datos[0]["codigo"] =  $verificacionRespuesta;
                $datos[0]["mensaje"] = " Fecha de inicio debe ser menor o igual a final";
            }

            $listaProductos = DB::select("SELECT (SELECT f.ciudad FROM franquicias f WHERE f.id = c.id_franquicia) AS sucursal,
                                                            (SELECT u.name FROM users u WHERE u.id = hc.id_usuarioC) AS usuariocreacion,
                                                            hc.cambios, hc.created_at, c.id as id_contrato
                                                            FROM historialcontrato hc
                                                            INNER JOIN contratos c ON c.id = hc.id_contrato
                                                            WHERE hc.tipomensaje = '4'
                                                            AND STR_TO_DATE(hc.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicio','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d')
                                                            ORDER BY hc.created_at DESC");

            $datos[0]["codigo"] =  $verificacionRespuesta;
            $datos[0]["mensaje"] = "Lista control de armazones obtenida correctamente.";
            $datos[0]["listaProductos"] = $listaProductos;

        } else {
            //Sesion ya caducada o no cumple con alguna validacion de estatus activo
            $datos[0]["codigo"] =  $verificacionRespuesta;
        }

        return "{'datos': '" . base64_encode(json_encode($datos)) . "'}";


    }

    // ---------------------------------------------------FUNCIONES GENERALES -----------------------------------------------------------
    public function verificarSesionActiva($dispositivo, $correo, $idUnico, $token){

        try {

            $validarAplicacion = DB::select("SELECT id FROM dispositivos WHERE id = '$dispositivo' AND estatus = 1 AND tipoapp = 1");

            $codigoVerificacion = "";

            if ($validarAplicacion != null) {//Existe aplicacion?
                //Aplicacion existe

                $usuario = DB::select("SELECT u.id, u.rol_id, u.name, u.email, u.password, u.id_zona, u.logueado FROM users u WHERE UPPER(u.email) = '$correo'");
                $id_usuario = $usuario[0]->id;

                if ($usuario != null) { //Usuario exite?
                    //Usuario existe

                    $dispositivoActivo = DB::select("SELECT estatus FROM dispositivosusuarios WHERE identificadorunico = '$idUnico' AND id_usuario = '$id_usuario'");

                    if ($dispositivoActivo != null) {
                        //Existe dispositivo

                        if ($dispositivoActivo[0]->estatus == 1) {
                            //Dispositivo activado

                            if ($usuario[0]->logueado == 2) {
                                //Usuario logueado

                                $tokenValido = DB::select("SELECT usuario_id FROM tokenlolatv WHERE token = '$token'");

                                if ($tokenValido != null) {// Existe el token

                                    $codigoVerificacion = "LOLATV9";
                                    return $codigoVerificacion;
                                }
                                //Token no valido
                                $codigoVerificacion = "LOLATV7";
                                return $codigoVerificacion;

                            }
                            //Tiene todos los registros correctos pero no esta logueado el usuario
                            $codigoVerificacion = "LOLATV2";
                            return $codigoVerificacion;

                        }
                        //Dispositivo desactivado
                        $codigoVerificacion = "LOLATV2";
                        return $codigoVerificacion;
                    }

                    //No existe dispositivo registrado
                    $codigoVerificacion = "LOLATV2";
                    return $codigoVerificacion;
                }
                //Usuario no existe
                $codigoVerificacion = "LOLATV1";
                return $codigoVerificacion;

            } else {
                //Aplicacion no existe
                $codigoVerificacion = "LOLATV4";
                return $codigoVerificacion;

            }

        }catch(\Exception $e){
            \Log::info("Error: servicioweblaboratorio: (iniciarsesion) - correo: " . $correo . "\n" . $e);
        }

    }
}
