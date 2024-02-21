<?php

namespace App\Http\Controllers\Dominios\Administracion;

use App\Clases\contratosGlobal;
use App\Clases\globalesServicioWeb;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Image;

class laboratorio extends Controller
{
    public function listalaboratorio()
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 16)) {
            //Solo los roles de Administracion, principal, director y confirmaciones pueden entrar

            $filtro = request('filtro');
            $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000'");
            $now = Carbon::now();

            $otrosContratos = null;
            $contratosSTerminar = null;
            $contratosPendientes = null;

            if (!is_null($filtro)) { //Tenemos un filtro?
                //Tenemos un filtro
                $contratosComentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,f.ciudad,c.created_at,
                                                            (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as fechaentrega,
                                                            (SELECT cl.ultimoestatusmanufactura FROM contratoslaboratorio cl WHERE cl.id_contrato = c.id ORDER BY cl.created_at DESC limit 1) as ultimoestatusmanufactura,
                                                            (SELECT g.id FROM garantias g WHERE g.id_contrato = c.id AND g.estadogarantia = 2 ORDER BY g.created_at DESC LIMIT 1) as tienegarantia
                                                            FROM contratos c
                                                            INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                            INNER JOIN franquicias f on f.id = c.id_franquicia
                                                            WHERE c.estatus_estadocontrato IN (7,10,11)
                                                            AND c.id like '%$filtro%'
                                                            AND c.banderacomentarioconfirmacion = 2 ORDER BY c.estatus_estadocontrato DESC,f.ciudad");

                $contratosSComentariosLabo = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,f.ciudad,c.created_at,
                                                                (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as fechaentrega,
                                                                (SELECT cl.ultimoestatusmanufactura FROM contratoslaboratorio cl WHERE cl.id_contrato = c.id ORDER BY cl.created_at DESC limit 1) as ultimoestatusmanufactura,
                                                                (SELECT g.id FROM garantias g WHERE g.id_contrato = c.id AND g.estadogarantia = 2 ORDER BY g.created_at DESC LIMIT 1) as tienegarantia
                                                                FROM contratos c
                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
                                                                WHERE c.banderacomentarioconfirmacion != 2 AND
                                                                c.estatus_estadocontrato IN (7,10,11) AND
                                                                c.id like '%$filtro%'
                                                                ORDER BY c.estatus_estadocontrato DESC,f.ciudad ASC
                                                               ");

//                $contratosSComentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,f.ciudad,c.created_at,(SELECT hc.fechaentrega
//                                                                FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as fechaentrega, r.created_at as fechaenvio,
//                                                            (SELECT rec.created_at FROM registroestadocontrato rec
//                                                                WHERE rec.id_contrato = c.id AND rec.estatuscontrato = 10
//                                                                ORDER BY rec.created_at DESC limit 1) as ultimoestatusmanufactura
//                                                                FROM contratos c
//                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
//                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
//                                                                INNER JOIN registroestadocontrato r ON r.id_contrato = c.id
//                                                                WHERE c.banderacomentarioconfirmacion != 2 AND
//                                                                c.estatus_estadocontrato = 12 AND
//                                                                c.id like '%$filtro%'
//                                                                ORDER BY c.estatus_estadocontrato DESC,f.ciudad ASC
//                                                               ");

                $contratosSTerminar = DB::select("SELECT c.id, c.estatus_estadocontrato, c.created_at, f.ciudad, ec.descripcion
                                                                FROM contratos c
                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
                                                                 INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                WHERE c.banderacomentarioconfirmacion != 2 AND
                                                                c.id_franquicia != '00000' AND
                                                                (c.datos = 1 AND c.estatus_estadocontrato = 0 )AND
                                                                c.id like '%$filtro%'
                                                                ORDER BY c.estatus_estadocontrato DESC,f.ciudad ASC");

                $contratosPendientes = DB::select("SELECT c.id, c.estatus_estadocontrato, f.ciudad
                                                                FROM contratos c
                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
                                                                WHERE c.banderacomentarioconfirmacion != 2 AND
                                                                c.id_franquicia != '00000' AND
                                                                (c.datos = 0 AND c.estatus_estadocontrato IS null ) AND
                                                                 c.id like '%$filtro%'
                                                                ORDER BY c.estatus_estadocontrato DESC,f.ciudad ASC");

                $otrosContratos = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,f.ciudad,c.created_at,
                                                                (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as fechaentrega,
                                                                (SELECT cl.ultimoestatusmanufactura FROM contratoslaboratorio cl WHERE cl.id_contrato = c.id ORDER BY cl.created_at DESC limit 1) as ultimoestatusmanufactura
                                                                FROM contratos c
                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
                                                                WHERE c.id_franquicia != '00000' AND
                                                                c.estatus_estadocontrato IN (1,2,3,4,5,6,8,9,12,14) AND
                                                                c.id like '%$filtro%'
                                                                ORDER BY c.estatus_estadocontrato ASC
                                                               ");

            } else {
                //Sin filtro
                $contratosComentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,f.ciudad,c.created_at,
                                                            (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as fechaentrega,
                                                            (SELECT cl.ultimoestatusmanufactura FROM contratoslaboratorio cl WHERE cl.id_contrato = c.id ORDER BY cl.created_at DESC limit 1) as ultimoestatusmanufactura,
                                                            (SELECT g.id FROM garantias g WHERE g.id_contrato = c.id AND g.estadogarantia = 2 ORDER BY g.created_at DESC LIMIT 1) as tienegarantia
                                                            FROM contratos c
                                                            INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato INNER JOIN franquicias f on f.id = c.id_franquicia
                                                            WHERE c.estatus_estadocontrato IN (7,10,11)
                                                            AND c.banderacomentarioconfirmacion = 2 ORDER BY c.estatus_estadocontrato DESC, f.ciudad");

                $contratosSComentariosLabo = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,f.ciudad,c.created_at,
                                                                (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as fechaentrega,
                                                                (SELECT cl.ultimoestatusmanufactura FROM contratoslaboratorio cl WHERE cl.id_contrato = c.id ORDER BY cl.created_at DESC limit 1) as ultimoestatusmanufactura,
                                                                (SELECT g.id FROM garantias g WHERE g.id_contrato = c.id AND g.estadogarantia = 2 ORDER BY g.created_at DESC LIMIT 1) as tienegarantia
                                                                FROM contratos c
                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
                                                                WHERE c.banderacomentarioconfirmacion != 2 AND
                                                                c.estatus_estadocontrato IN (7,10,11)
                                                                ORDER BY c.estatus_estadocontrato DESC,f.ciudad ASC
                                                               ");

//                $contratosSComentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,f.ciudad,c.created_at,(SELECT hc.fechaentrega
//                                                                FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as fechaentrega, r.created_at as fechaenvio,
//                                                            (SELECT rec.created_at FROM registroestadocontrato rec
//                                                                WHERE rec.id_contrato = c.id AND rec.estatuscontrato = 10
//                                                                ORDER BY rec.created_at DESC limit 1) as ultimoestatusmanufactura
//                                                                FROM contratos c
//                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
//                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
//                                                                INNER JOIN registroestadocontrato r ON r.id_contrato = c.id
//                                                                WHERE c.banderacomentarioconfirmacion != 2 AND
//                                                                c.estatus_estadocontrato = 12
//                                                                AND STR_TO_DATE(r.created_at,'%Y-%m-%d') >= STR_TO_DATE('$now','%Y-%m-%d') AND
//                                                                STR_TO_DATE(r.created_at,'%Y-%m-%d') <= STR_TO_DATE('$now','%Y-%m-%d')
//                                                                ORDER BY c.estatus_estadocontrato DESC,f.ciudad ASC
//                                                               ");

            }

            return view("administracion.laboratorio.tabla", [
                "contratosComentarios" => $contratosComentarios,
//                "contratosSComentarios" => $contratosSComentarios,
                'contratosSComentariosLabo' => $contratosSComentariosLabo,
                'contratosSTerminar'=>$contratosSTerminar,
                'contratosPendientes' => $contratosPendientes,
                "otrosContratos" => $otrosContratos,
                'franquicias' => $franquicias,
                'filtro' => $filtro
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function estadolaboratorio($idContrato)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 16)) {
            $contrato = DB::select("SELECT c.id,c.estatus_estadocontrato,z.zona,c.banderacomentarioconfirmacion,f.ciudad,
                                            (SELECT u.name FROM users u WHERE u.id = c.id_optometrista) as opto,
                                          c.nombre,c.calleentrega,c.numeroentrega,c.deptoentrega,c.alladode,c.frentea,c.entrecallesentrega,c.coloniaentrega,c.localidadentrega,c.telefono,
                                          c.casatipo,c.casacolor,c.nombrereferencia,c.telefonoreferencia,c.correo,c.fotoine,c.fotoineatras,c.fotocasa,c.comprobantedomicilio,
                                          c.tarjeta,c.tarjetapensionatras,c.pago,ec.descripcion,c.created_at as fecha, c.id_franquicia
                                          FROM contratos c
                                          INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                          INNER JOIN zonas z ON z.id = c.id_zona
                                          INNER JOIN franquicias f ON f.id = c.id_franquicia
                                          WHERE c.id = '$idContrato'");

            $datosContratos = DB::select("SELECT c.datos FROM contratos c WHERE c.id = '$idContrato'");
            if($datosContratos[0]->datos == 0){
                return back()->with("alerta", "No se encontro el contrato.");
            }

            $tieneGarantia = DB::select("SELECT id FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia = 2");
            $paqueteContrato = DB::select("SELECT hc.id_paquete FROM historialclinico hc WHERE hc.id_contrato = '$idContrato' ORDER BY hc.created_at DESC LIMIT 1");
            $idPaquete = $paqueteContrato[0]->id_paquete;
            $idFranquiciaContrato = $contrato[0]->id_franquicia;
            $numeroHistoriales = DB::select("SELECT p.numerohistoriales FROM paquetes p WHERE p.id = '$idPaquete' AND p.id_franquicia = '$idFranquiciaContrato'");
            $banderaHistorialesCorrectos = true;

            if ($tieneGarantia != null) {//Tiene garantia?
                //Si tiene garantia

                $historialClinico = DB::select("SELECT hc.id,hc.esfericoder,hc.cilindroder,hc.ejeder,hc.addder,hc.altder,hc.esfericoizq,hc.cilindroizq,hc.ejeizq,hc.addizq,hc.altizq,
                                                    (SELECT nombre FROM producto p WHERE p.id = hc.id_producto) as armazon,
                                                    (SELECT color FROM producto p WHERE p.id = hc.id_producto) as colorarmazon, hc.id_producto, hc.fechaentrega, hc.bifocalotro,
                                                    hc.material,hc.materialotro,hc.bifocal,hc.fotocromatico,hc.ar,hc.tinte,hc.blueray,hc.otroT,hc.tratamientootro,hc.observaciones,g.id as garantia,
                                                    (SELECT p.piezas FROM producto  p WHERE p.id = hc.id_producto) as piezasr,
                                                    (SELECT g.id FROM garantias g WHERE g.id_contrato = hc.id_contrato AND g.id_historialgarantia = hc.id) as garantia,
                                                    (SELECT u.name FROM users u WHERE u.id = (SELECT g.id_optometrista FROM garantias g WHERE g.id_contrato = hc.id_contrato
                                                    AND g.id_historialgarantia = hc.id)) as optogarantia,
                                                    hc.policarbonatotipo, estilotinte as estilotinte, hc.polarizado as polarizado, hc.espejo as espejo,
                                                    (SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = hc.id_tratamientocolortinte) as colortinte,
                                                    (SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = hc.id_tratamientocolorpolarizado) as colorpolarizado,
                                                    (SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = hc.id_tratamientocolorespejo) as colorespejo
                                                    FROM historialclinico hc
                                                    INNER JOIN garantias g ON g.id_contrato = hc.id_contrato
                                                    WHERE hc.id_contrato = '$idContrato' AND g.id_historialgarantia = hc.id AND g.estadogarantia = 2 AND hc.tipo != 2 ORDER BY hc.created_at DESC");

                //Validar paquete
                if($paqueteContrato == 6){
                    //DORADO 2
                    $garantiasActivas = DB::select("SELECT COUNT(g.id_historial) AS garantiasActivas FROM garantias g WHERE g.id_contrato = '$idContrato' AND g.estadogarantia = '2'");
                    if($garantiasActivas[0]->garantiasActivas != sizeof($historialClinico) || sizeof($historialClinico) > $numeroHistoriales[0]->numerohistoriales){
                        //Contrato con diferencia de historiales entre garantias activas e historiales que trae la consulta general
                        // Historiales traidos son mayor al numero maximo de historiales por paquete
                        $banderaHistorialesCorrectos = false;
                    }
                }else{
                    if(sizeof($historialClinico) > $numeroHistoriales[0]->numerohistoriales){
                        // Historiales traidos son mayor al numero maximo de historiales por paquete
                        $banderaHistorialesCorrectos = false;
                    }
                }
            } else {

                //No tiene garantia
                $historialClinico = DB::select("SELECT hc.id,hc.esfericoder,hc.cilindroder,hc.ejeder,hc.addder,hc.altder,hc.esfericoizq,hc.cilindroizq,hc.ejeizq,hc.addizq,hc.altizq,
                                                    (SELECT nombre FROM producto p WHERE p.id = hc.id_producto) as armazon,
                                                    (SELECT color FROM producto p WHERE p.id = hc.id_producto) as colorarmazon, hc.id_producto, hc.fechaentrega, hc.bifocalotro,
                                                    hc.material,hc.materialotro,hc.bifocal,hc.fotocromatico,hc.ar,hc.tinte,hc.blueray,hc.otroT,hc.tratamientootro,hc.observaciones,
                                                    (SELECT p.piezas FROM producto  p WHERE p.id = hc.id_producto) as piezasr,
                                                    (SELECT g.id FROM garantias g WHERE g.id_contrato = hc.id_contrato AND g.id_historialgarantia = hc.id AND (g.estadogarantia = 2
                                                    AND g.estadocontratogarantia = 2)) as garantia,
                                                    (SELECT u.name FROM users u WHERE u.id = (SELECT c.id_optometrista FROM contratos c WHERE c.id= hc.id_contrato)) as optocontrato,
                                                    hc.policarbonatotipo, estilotinte as estilotinte, hc.polarizado as polarizado, hc.espejo as espejo,
                                                    (SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = hc.id_tratamientocolortinte) as colortinte,
                                                    (SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = hc.id_tratamientocolorpolarizado) as colorpolarizado,
                                                    (SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = hc.id_tratamientocolorespejo) as colorespejo
                                                    FROM historialclinico hc WHERE id_contrato = '$idContrato' AND hc.tipo != 2 ORDER BY hc.created_at DESC");

                if(sizeof($historialClinico) > $numeroHistoriales[0]->numerohistoriales){
                    // Historiales traidos son mayor al numero maximo de historiales por paquete
                    $banderaHistorialesCorrectos = false;
                }

            }


            $comentarios = DB::select("SELECT u.name,m.comentario,m.fecha FROM mensajesconfirmaciones m INNER JOIN users u ON u.id = m.id_usuario WHERE m.id_contrato = '$idContrato'
                                                ORDER BY m.fecha DESC");
            if ($contrato[0]->banderacomentarioconfirmacion == 2) {
                DB::table("contratos")->where("id", "=", $idContrato)->update([
                    "banderacomentarioconfirmacion" => 0
                ]);
            }

            //Validacion de rol usuario para mostrar lista de movimientos
            switch (Auth::user()->rol_id){
                case 7: //ROL DIRECTOR
                    //Mostrar todos los cambios generados sobre el contrato sin importar el usuario que los realizo
                    $historialContrato = DB::select("SELECT u.name,hc.cambios,hc.created_at FROM historialcontrato hc
                                                           INNER JOIN users u ON u.id = hc.id_usuarioC
                                                           WHERE hc.id_contrato = '$idContrato' ORDER BY created_at DESC");
                    break;
                case 16: //ROL LABORATORIO
                    //Mostrar todos los cambios sobre el contrato hechos por usuarios de rol LABORATORIO
                    $historialContrato = DB::select("SELECT u.name,hc.cambios,hc.created_at FROM historialcontrato hc
                                                           INNER JOIN users u ON hc.id_usuarioC = u.id
                                                           WHERE u.rol_id = '16' AND hc.id_contrato = '$idContrato' ORDER BY created_at DESC");
                    break;
            }

            //Obtencion de datos para mandar a imprimir en impresora termica
            $historialesClinicosImpresoraTermica = $historialClinico;

            //Obtencion de datos sobre estado garantia para imprimir tiket
            $consultaGarantia = DB::select("SELECT estadogarantia FROM garantias WHERE id_contrato = '$idContrato'");
            if($consultaGarantia != null){
                $estadoGarantia = $consultaGarantia[0] -> estadogarantia;
            } else {
                $estadoGarantia = " ";
            }

            $armazones = DB::select("SELECT * FROM producto p WHERE id_tipoproducto = '1' ORDER BY p.nombre ASC");

            $contratoproductoarmazones = DB::select("SELECT p.nombre, cp.piezas, p.color
                                                        FROM contratoproducto cp
                                                        INNER JOIN producto p on cp.id_producto = p.id
                                                        WHERE cp.id_contrato = '$idContrato'
                                                        AND p.id_tipoproducto = '1'");

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
                            $nombreProducto1 = self::remplazarCaracteres($producto[0]->nombre);
                            $colorProducto1 = self::remplazarCaracteres($producto[0]->color);
                            $observacionesHistorial1 = self::remplazarCaracteres($historialClinicoImpresora->observaciones);
                            //Ojo derecho
                            $esfericoder1 = self::remplazarCaracteres($historialClinicoImpresora->esfericoder);
                            $cilindroder1 = self::remplazarCaracteres($historialClinicoImpresora->cilindroder);
                            $ejeder1 = self::remplazarCaracteres($historialClinicoImpresora->ejeder);
                            $addder1 = self::remplazarCaracteres($historialClinicoImpresora->addder);
                            $altder1 = self::remplazarCaracteres($historialClinicoImpresora->altder);
                            //Ojo izquierdo
                            $esfericoizq1 = self::remplazarCaracteres($historialClinicoImpresora->esfericoizq);
                            $cilindroizq1 = self::remplazarCaracteres($historialClinicoImpresora->cilindroizq);
                            $ejeizq1 = self::remplazarCaracteres($historialClinicoImpresora->ejeizq);
                            $addizq1 = self::remplazarCaracteres($historialClinicoImpresora->addizq);
                            $altizq1 = self::remplazarCaracteres($historialClinicoImpresora->altizq);

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
                            $material1 = self::remplazarCaracteres($historialClinicoImpresora->material);
                            $materialotro1 = self::remplazarCaracteres($historialClinicoImpresora->materialotro);
                            $policarbonatoTipo1 = self::remplazarCaracteres($historialClinicoImpresora->policarbonatotipo);
                            //Bifocal
                            $bifocal1 = self::remplazarCaracteres($historialClinicoImpresora->bifocal);
                            $bifocalotro1 = self::remplazarCaracteres($historialClinicoImpresora->bifocalotro);
                            //Tratamientos
                            $fotocromatico1 = self::remplazarCaracteres($historialClinicoImpresora->fotocromatico);
                            $ar1 = self::remplazarCaracteres($historialClinicoImpresora->ar);
                            $tinte1 = self::remplazarCaracteres($historialClinicoImpresora->tinte);
                            $polarizado1 = self::remplazarCaracteres($historialClinicoImpresora->polarizado);
                            $espejo1 = self::remplazarCaracteres($historialClinicoImpresora->espejo);
                            $blueray1 = self::remplazarCaracteres($historialClinicoImpresora->blueray);
                            $otroT1 = self::remplazarCaracteres($historialClinicoImpresora->otroT);
                            $tratamientootro1 = self::remplazarCaracteres($historialClinicoImpresora->tratamientootro);

                            if ($material1 == 0) {
                                $material1 = "Hi index";
                            } elseif ($material1 == 1) {
                                $material1 = "CR";
                            } elseif ($material1 == 2) {
                                $material1 = "POLICARBONATO";
                                if($policarbonatoTipo1 == 0){
                                    $material1 = $material1 . "(ADULTO)";
                                }elseif ($policarbonatoTipo1 == 1){
                                    $material1 = $material1 . "(NIÑO)";
                                }
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
                                //Color y estilo de tinte
                                if($historialClinicoImpresora->colortinte != null && $historialClinicoImpresora->estilotinte != null){
                                    $color = self::remplazarCaracteres($historialClinicoImpresora->colortinte);
                                    $estilo = self::remplazarCaracteres($historialClinicoImpresora->estilotinte);
                                    $estilo = ($historialClinicoImpresora->estilotinte == 0)? "Desvanecido": "Completo";
                                    $tratamientos1 = $tratamientos1 . "Tinte (" . $color . "|" . $estilo . ")|";
                                }else{
                                    $tratamientos1 = $tratamientos1 . "Tinte|";
                                }
                            }
                            if($polarizado1 == 1){
                                ($historialClinicoImpresora->colorpolarizado != null)? $tratamientos1 = $tratamientos1 . "Polarizado (" . $historialClinicoImpresora->colorpolarizado .")" : $tratamientos1 . "Polarizado|";
                            }

                            if($espejo1 == 1){
                                ($historialClinicoImpresora->colorespejo != null)? $tratamientos1 = $tratamientos1 . "Espejo (" . $historialClinicoImpresora->colorespejo .")" : $tratamientos1 . "Espejo|";
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
                            $nombreProducto2 = self::remplazarCaracteres($producto[0]->nombre);
                            $colorProducto2 = self::remplazarCaracteres($producto[0]->color);
                            $observacionesHistorial2 = self::remplazarCaracteres($historialClinicoImpresora->observaciones);
                            //Ojo derecho
                            $esfericoder2 = self::remplazarCaracteres($historialClinicoImpresora->esfericoder);
                            $cilindroder2 = self::remplazarCaracteres($historialClinicoImpresora->cilindroder);
                            $ejeder2 = self::remplazarCaracteres($historialClinicoImpresora->ejeder);
                            $addder2 = self::remplazarCaracteres($historialClinicoImpresora->addder);
                            $altder2 = self::remplazarCaracteres($historialClinicoImpresora->altder);
                            //Ojo izquierdo
                            $esfericoizq2 = self::remplazarCaracteres($historialClinicoImpresora->esfericoizq);
                            $cilindroizq2 = self::remplazarCaracteres($historialClinicoImpresora->cilindroizq);
                            $ejeizq2 = self::remplazarCaracteres($historialClinicoImpresora->ejeizq);
                            $addizq2 = self::remplazarCaracteres($historialClinicoImpresora->addizq);
                            $altizq2 = self::remplazarCaracteres($historialClinicoImpresora->altizq);

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
                            $material2 = self::remplazarCaracteres($historialClinicoImpresora->material);
                            $materialotro2 = self::remplazarCaracteres($historialClinicoImpresora->materialotro);
                            $policarbonatoTipo2 = self::remplazarCaracteres($historialClinicoImpresora->policarbonatotipo);
                            //Bifocal
                            $bifocal2 = self::remplazarCaracteres($historialClinicoImpresora->bifocal);
                            $bifocalotro2 = self::remplazarCaracteres($historialClinicoImpresora->bifocalotro);
                            //Tratamientos
                            $fotocromatico2 = self::remplazarCaracteres($historialClinicoImpresora->fotocromatico);
                            $ar2 = self::remplazarCaracteres($historialClinicoImpresora->ar);
                            $tinte2 = self::remplazarCaracteres($historialClinicoImpresora->tinte);
                            $polarizado2 = self::remplazarCaracteres($historialClinicoImpresora->polarizado);
                            $espejo2 = self::remplazarCaracteres($historialClinicoImpresora->espejo);
                            $blueray2 = self::remplazarCaracteres($historialClinicoImpresora->blueray);
                            $otroT2 = self::remplazarCaracteres($historialClinicoImpresora->otroT);
                            $tratamientootro2 = self::remplazarCaracteres($historialClinicoImpresora->tratamientootro);

                            if ($material2 == 0) {
                                $material2 = "Hi index";
                            } elseif ($material2 == 1) {
                                $material2 = "CR";
                            } elseif ($material2 == 2) {
                                $material2 = "POLICARBONATO";
                                if($policarbonatoTipo2 == 0){
                                    $material2 = $material2 . "(ADULTO)";
                                }elseif ($policarbonatoTipo2 == 1){
                                    $material2 = $material2 . "(NIÑO)";
                                }
                            }elseif ($material2 == 3) {
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
                                //Color y estilo de tinte
                                if($historialClinicoImpresora->colortinte != null && $historialClinicoImpresora->estilotinte != null){
                                    $color = self::remplazarCaracteres($historialClinicoImpresora->colortinte);
                                    $estilo = self::remplazarCaracteres($historialClinicoImpresora->estilotinte);
                                    $estilo = ($historialClinicoImpresora->estilotinte == 0)? "Desvanecido": "Completo";
                                    $tratamientos2 = $tratamientos2 . "Tinte (" . $color . "|" . $estilo . ")|";
                                }else{
                                    $tratamientos2 = $tratamientos2 . "Tinte|";
                                }
                            }
                            if($polarizado2 == 1){
                                ($historialClinicoImpresora->colorpolarizado != null)? $tratamientos2 = $tratamientos2 . "Polarizado (" . $historialClinicoImpresora->colorpolarizado .")" : $tratamientos2 . "Polarizado|";
                            }

                            if($espejo2 == 1){
                                ($historialClinicoImpresora->colorespejo != null)? $tratamientos2 = $tratamientos2 . "Espejo (" . $historialClinicoImpresora->colorespejo .")|" : $tratamientos2 . "Espejo|";
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
                        return back()->with("alerta", "No existe el producto, contacta a soporte.");
                    }

                }

            } else {
                return back()->with("alerta", "No existe el contrato, contacta a soporte.");
            }

            return view("administracion.laboratorio.estadolaboratorio", [
                "contrato" => $contrato,
                "comentarios" => $comentarios,
                'idContrato' => $idContrato,
                'historialClinico' => $historialClinico,
                'idHistorial1' => $idHistorial1,
                'fechaEntregaHistorial1' => $fechaEntregaHistorial1,
                'nombreProducto1' => $nombreProducto1,
                'colorProducto1' => $colorProducto1,
                'observacionesHistorial1' => $observacionesHistorial1,
                'esfericoder1' => $esfericoder1,
                'cilindroder1' => $cilindroder1,
                'ejeder1' => $ejeder1,
                'addder1' => $addder1,
                'altder1' => $altder1,
                'esfericoizq1' => $esfericoizq1,
                'cilindroizq1' => $cilindroizq1,
                'ejeizq1' => $ejeizq1,
                'addizq1' => $addizq1,
                'altizq1' => $altizq1,
                'material1' => $material1,
                'bifocal1' => $bifocal1,
                'tratamientos1' => $tratamientos1,
                'idHistorial2' => $idHistorial2,
                'fechaEntregaHistorial2' => $fechaEntregaHistorial2,
                'nombreProducto2' => $nombreProducto2,
                'colorProducto2' => $colorProducto2,
                'observacionesHistorial2' => $observacionesHistorial2,
                'esfericoder2' => $esfericoder2,
                'cilindroder2' => $cilindroder2,
                'ejeder2' => $ejeder2,
                'addder2' => $addder2,
                'altder2' => $altder2,
                'esfericoizq2' => $esfericoizq2,
                'cilindroizq2' => $cilindroizq2,
                'ejeizq2' => $ejeizq2,
                'addizq2' => $addizq2,
                'altizq2' => $altizq2,
                'material2' => $material2,
                'bifocal2' => $bifocal2,
                'tratamientos2' => $tratamientos2,
                'historialcontrato' => $historialContrato,
                'estadoGarantia' => $estadoGarantia,
                'comentariosContrato' => $comentariosContrato,
                'garantia' => $tieneGarantia,
                'armazones' => $armazones,
                'contratoproductoarmazones' => $contratoproductoarmazones,
                'banderaHistorialesCorrectos' => $banderaHistorialesCorrectos
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function comentariolaboratorio($idContrato)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 16)) {
            $rules = [
                'comentario' => 'required|string',
            ];
            request()->validate($rules);

            $contrato = DB::select("SELECT c.estatus_estadocontrato
                                          FROM contratos c
                                          WHERE c.id = '$idContrato'");
            if ($contrato != null) {
                if ($contrato[0]->estatus_estadocontrato != 7 && $contrato[0]->estatus_estadocontrato != 10 && $contrato[0]->estatus_estadocontrato != 11) {
                    return back()->with("alerta", "No es posible agregar comentarios debido al estatus actual del contrato.");
                }
            }

            try {
                $ahora = Carbon::now();
                DB::table('mensajesconfirmaciones')->insert([
                    "id_contrato" => $idContrato, "id_usuario" => Auth::user()->id, "comentario" => request("comentario"), "fecha" => $ahora
                ]);

                DB::table("contratos")->where("id", "=", $idContrato)->update([
                    "banderacomentarioconfirmacion" => 3
                ]);

                return back()->with("bien", "El mensaje se guardo correctamente.");
            } catch (\Exception $e) {
                return back()->with("alerta", "Error: " . $e);
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function estadolaboratorioactualizar($idContrato)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 16)) {
            $rules = [
                'estatus' => 'required|integer',
            ];
            request()->validate($rules);

            $estatus = request("estatus");
            if ($estatus != 8) {
                if ($estatus < 10 || $estatus > 12) {
                    return back()->with("alerta", "Estatus no valido.");
                }
            }

            $datosContratos = DB::select("SELECT c.datos FROM contratos c WHERE c.id = '$idContrato'");

            $contrato = DB::select("SELECT estatus_estadocontrato, id_zona, id_franquicia, pago FROM contratos WHERE id = '$idContrato'");

            $solicitudAutorizacionTraspasoSucursalPendiente = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato = '$idContrato' AND a.tipo = 6
                                                                    AND estatus = '0' ORDER BY a.created_at DESC LIMIT 1");

            if ($contrato != null) {
                //Si $estatus es menor al estatus_estadocontrato no se puede actualizar
                if($contrato[0]->estatus_estadocontrato > $estatus){
                    return back()->with("alerta", "No puedes cambiar el estatus del contrato a un estatus anterior.");
                } if($datosContratos[0]->datos == 0){
                    return back()->with("alerta", "No se encontro el contrato.");
                } if ($contrato[0]->estatus_estadocontrato == 12) {
                    return back()->with("alerta", "No puedes cambiar el estatus del contrato en este momento.");
                } if ($contrato[0]->estatus_estadocontrato == 7 && $estatus == 12) {
                    //Estado actual del contrato es APROBADO y se quiere cambiar a ENVIADO
                    return back()->with("alerta", "No puedes cambiar el estatus del contrato a ENVIADO.");
                } if ($solicitudAutorizacionTraspasoSucursalPendiente != null) {
                    //Existe solicitud pendiente de traspaso de sucursal en el contrato
                    return back()->with("alerta", "Antes de modificar el estatus, asegúrate de actualizar la solicitud pendiente de traspaso de sucursal en el contrato.");
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

                            //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                            $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, Auth::id());

                            //Eliminar registros de la tabla abonoscontratostemporalessincronizacion que contengan ese idContrato
                            DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_contrato = '$idContrato'");

                            $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($contrato[0]->id_zona); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona
                            if ($cobradoresAsignadosAZona != null) {
                                //Existen cobradores
                                foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                    //Recorrido cobradores
                                    $abonos = DB::select("SELECT * FROM abonos WHERE id_contrato = '$idContrato' ORDER BY created_at DESC");
                                    //Insertar abonos en tabla abonoscontratostemporalessincronizacion
                                    foreach ($abonos as $abono) {
                                        //Recorrido abonos
                                        //Insertar en tabla abonoscontratostemporalessincronizacion
                                        DB::table("abonoscontratostemporalessincronizacion")->insert([
                                            "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                            "id" => $abono->id,
                                            "folio" => $abono->folio,
                                            "id_contrato" => $abono->id_contrato,
                                            "id_usuario" => $abono->id_usuario,
                                            "abono" => $abono->abono,
                                            "adelantos" => $abono->adelantos,
                                            "tipoabono" => $abono->tipoabono,
                                            "atraso" => $abono->atraso,
                                            "metodopago" => $abono->metodopago,
                                            "corte" => $abono->corte,
                                            "created_at" => $abono->created_at,
                                            "updated_at" => $abono->updated_at
                                        ]);
                                    }
                                }
                            }

                        }
                        $estatusContrato = self::obtenerEstatusContrato($estatus);
                        DB::table('historialcontrato')->insert([
                            'id' => self::getHistorialContratoId(), 'id_usuarioC' => Auth::id(), 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                            'cambios' => " L - Cambio el estatus a '$estatusContrato'"
                        ]);

                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $idContrato,
                            'estatuscontrato' => $estatus,
                            'created_at' => Carbon::now()
                        ]);

                        $tieneGarantia = DB::select("SELECT id FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia = 2");
                        if($tieneGarantia != null) {
                            //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                            $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, Auth::id());
                        }

                        //Verificar a que estatus se actualizo el contrato
                        //Se actualizo a ENVIADO?
                        if($estatus == 12){
                            //Estatus es ENVIADO - Eliminamos de la tabla contratoslaboratorio
                            $contratosGlobal::actualizarRegistroTablaContratosLaboratorio($idContrato, "ELIMINAR");

                        }else{
                            //Se actualizo a MANUFACTURA o PROCESO DE ENVIO - mandar llamar metodo con funcion insertar para actualizar fecha manufactura
                            $contratosGlobal::actualizarRegistroTablaContratosLaboratorio($idContrato, "INSERTAR");
                        }

                        //Actualizar datos en tabla contratoslistatemporales
                        $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                        return redirect()->route('listalaboratorio')->with("bien", "El contrato cambio  a " . $estatusContrato);

                    } else {
                        return back()->with("alerta", "No puedes realizar esta acción debido al estado actual del contrato.");
                    }
                }
            } else {
                //El contrato no existe
                return back()->with("bien", "El contrato no es valido.");
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public static function obtenerEstatusContrato($estatus)
    {

        switch ($estatus) {
            case 10:
                return "Manufactura";
            case 11:
                return "En proceso de envio..";
            case 12:
                return "Enviado";
            case 8:
                return "Rechazado";
        }
        return "Estatus:" . $estatus;
    }

    /* Metodo/Funcion: getHistorialContratoId
    Descripcion: Esta función revisa si el ID alfanumerico que crea la funcion random no esta repetido en la BD es decir busca que sea unico.
    */
    public function getHistorialContratoId()
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $temporalId = $this->generadorRandom2();
            $existente = DB::select("select id from historialcontrato where id = '$temporalId'");
            if (sizeof($existente) == 0) {
                $unico = $temporalId;
                $esUnico = true;
            }
        }
        return $unico;
    }    // Comparar si ya existe el id en la base de datos

    /* Metodo/Funcion: generadorRandom2
    Descripcion: Esta función crea un ID alfanumerico de 5 digitos para registros
    */
    private function generadorRandom2($length = 5)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($caracteres);
        $randomId = '';
        for ($i = 0; $i < $length; $i++) {
            $randomId .= $caracteres[rand(0, $charactersLength - 1)];
        }
        return $randomId;
    }

    public function auxiliarlaboratorio()
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 2)) {
            //Solo los roles de Administracion, principal, director y confirmaciones pueden entrar
            $historiales = null;
            $filtro = request('filtro');
            if (!is_null($filtro)) { //Tenemos un filtro?
                //Tenemos un filtro
                $historiales = DB::select("SELECT * FROM historialclinico hc INNER JOIN contratos c ON hc.id_contrato = c.id
                                                    WHERE id_contrato = '$filtro' AND c.estatus_estadocontrato in (7,9,10,11,12)"); //Pendiente validar el estado del contrato
            }
            return view("administracion.laboratorio.auxiliar", ["historiales" => $historiales, "idContrato" => $filtro]);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarestadoenviado()
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 16)) {
            //Solo los roles de director y laboratorio pueden entrar
            $contratosSComentariosLabo = DB::select("SELECT c.id, c.id_zona, c.id_franquicia, c.pago
                                                                FROM contratos c
                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
                                                                WHERE c.estatus_estadocontrato = 11
                                                                AND c.banderacomentarioconfirmacion != 2
                                                                ORDER BY c.estatus_estadocontrato DESC,f.ciudad ASC");

            $mensajeSolicitudesPendientesTraspasoSucursal = ".";
            foreach ($contratosSComentariosLabo as $contratoSComentariosLabo) {
                $contratoEntrante = request('check' . $contratoSComentariosLabo->id);
                if ($contratoEntrante != null){

                    $solicitudAutorizacionTraspasoSucursalPendiente = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato = '" . $contratoSComentariosLabo->id . "' AND a.tipo = 6
                                                                    AND estatus = '0' ORDER BY a.created_at DESC LIMIT 1");

                    $enviar = true;
                    if ($solicitudAutorizacionTraspasoSucursalPendiente != null) {
                        //Existe solicitud pendiente de traspaso de sucursal en el contrato
                        $enviar = false;
                        if (strlen($mensajeSolicitudesPendientesTraspasoSucursal) == 1) {
                            //Mensaje esta vacio
                            $mensajeSolicitudesPendientesTraspasoSucursal = ", uno o más contratos tienen solicitudes pendientes de traspaso de sucursal. Es necesario actualizar esas solicitudes antes de cambiar el estatus del contrato.";
                        }
                    }

                    if ($enviar) {
                        //Actualizar el estado a enviado el contrato

                        $contratosGlobal = new contratosGlobal;

                        DB::table("contratos")->where("id", "=", $contratoSComentariosLabo->id)->update([
                            "estatus_estadocontrato" => 12
                        ]);

                        DB::table("garantias")->where("id_contrato", "=", $contratoSComentariosLabo->id)->update([
                            "estadogarantia" => 3
                        ]);

                        DB::table('historialcontrato')->insert([
                            'id' => self::getHistorialContratoId(), 'id_usuarioC' => Auth::id(), 'id_contrato' => $contratoSComentariosLabo->id, 'created_at' => Carbon::now(),
                            'cambios' => " L - Cambio el estatus a 'Enviado'"
                        ]);

                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $contratoSComentariosLabo->id,
                            'estatuscontrato' => 12,
                            'created_at' => Carbon::now()
                        ]);

                        //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                        $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($contratoSComentariosLabo->id, Auth::id());

                        //Eliminar registros de la tabla abonoscontratostemporalessincronizacion que contengan ese idContrato
                        DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_contrato = '" . $contratoSComentariosLabo->id . "'");

                        $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($contratoSComentariosLabo->id_zona); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona
                        if ($cobradoresAsignadosAZona != null) {
                            //Existen cobradores
                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                $abonos = DB::select("SELECT * FROM abonos WHERE id_contrato = '" . $contratoSComentariosLabo->id . "' ORDER BY created_at DESC");
                                //Insertar abonos en tabla abonoscontratostemporalessincronizacion
                                foreach ($abonos as $abono) {
                                    //Recorrido abonos
                                    //Insertar en tabla abonoscontratostemporalessincronizacion
                                    DB::table("abonoscontratostemporalessincronizacion")->insert([
                                        "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                        "id" => $abono->id,
                                        "folio" => $abono->folio,
                                        "id_contrato" => $abono->id_contrato,
                                        "id_usuario" => $abono->id_usuario,
                                        "abono" => $abono->abono,
                                        "adelantos" => $abono->adelantos,
                                        "tipoabono" => $abono->tipoabono,
                                        "atraso" => $abono->atraso,
                                        "metodopago" => $abono->metodopago,
                                        "corte" => $abono->corte,
                                        "created_at" => $abono->created_at,
                                        "updated_at" => $abono->updated_at
                                    ]);
                                }
                            }
                        }

                        //Eliminamos de la tabla contratoslaboratorio
                        $contratosGlobal::actualizarRegistroTablaContratosLaboratorio($contratoSComentariosLabo->id, "ELIMINAR");

                        //Actualizar datos en tabla contratoslistatemporales
                        $contratosGlobal::insertarActualizarDatosContratoListaTemporales($contratoSComentariosLabo->id);

                    }

                }

            }

            return redirect()->route('listalaboratorio')->with("bien", "El estatus de los contratos se actualizo correctamente" . $mensajeSolicitudesPendientesTraspasoSucursal);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public static function remplazarCaracteres($cadena)
    {
        $cadenaremplazo = str_replace('⁰', '', $cadena);
        $cadenaremplazo = str_replace('°', '', $cadenaremplazo);

        static $map = [
            // single letters
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'ą' => 'a',
            'å' => 'a',
            'ā' => 'a',
            'ă' => 'a',
            'ǎ' => 'a',
            'ǻ' => 'a',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Ą' => 'A',
            'Å' => 'A',
            'Ā' => 'A',
            'Ă' => 'A',
            'Ǎ' => 'A',
            'Ǻ' => 'A',


            'ç' => 'c',
            'ć' => 'c',
            'ĉ' => 'c',
            'ċ' => 'c',
            'č' => 'c',
            'Ç' => 'C',
            'Ć' => 'C',
            'Ĉ' => 'C',
            'Ċ' => 'C',
            'Č' => 'C',

            'ď' => 'd',
            'đ' => 'd',
            'Ð' => 'D',
            'Ď' => 'D',
            'Đ' => 'D',


            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ę' => 'e',
            'ē' => 'e',
            'ĕ' => 'e',
            'ė' => 'e',
            'ě' => 'e',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ę' => 'E',
            'Ē' => 'E',
            'Ĕ' => 'E',
            'Ė' => 'E',
            'Ě' => 'E',

            'ƒ' => 'f',


            'ĝ' => 'g',
            'ğ' => 'g',
            'ġ' => 'g',
            'ģ' => 'g',
            'Ĝ' => 'G',
            'Ğ' => 'G',
            'Ġ' => 'G',
            'Ģ' => 'G',


            'ĥ' => 'h',
            'ħ' => 'h',
            'Ĥ' => 'H',
            'Ħ' => 'H',

            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ĩ' => 'i',
            'ī' => 'i',
            'ĭ' => 'i',
            'į' => 'i',
            'ſ' => 'i',
            'ǐ' => 'i',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ĩ' => 'I',
            'Ī' => 'I',
            'Ĭ' => 'I',
            'Į' => 'I',
            'İ' => 'I',
            'Ǐ' => 'I',

            'ĵ' => 'j',
            'Ĵ' => 'J',

            'ķ' => 'k',
            'Ķ' => 'K',


            'ł' => 'l',
            'ĺ' => 'l',
            'ļ' => 'l',
            'ľ' => 'l',
            'ŀ' => 'l',
            'Ł' => 'L',
            'Ĺ' => 'L',
            'Ļ' => 'L',
            'Ľ' => 'L',
            'Ŀ' => 'L',


            'ñ' => 'n',
            'ń' => 'n',
            'ņ' => 'n',
            'ň' => 'n',
            'ŉ' => 'n',
            'Ñ' => 'N',
            'Ń' => 'N',
            'Ņ' => 'N',
            'Ň' => 'N',

            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ð' => 'o',
            'ø' => 'o',
            'ō' => 'o',
            'ŏ' => 'o',
            'ő' => 'o',
            'ơ' => 'o',
            'ǒ' => 'o',
            'ǿ' => 'o',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ø' => 'O',
            'Ō' => 'O',
            'Ŏ' => 'O',
            'Ő' => 'O',
            'Ơ' => 'O',
            'Ǒ' => 'O',
            'Ǿ' => 'O',


            'ŕ' => 'r',
            'ŗ' => 'r',
            'ř' => 'r',
            'Ŕ' => 'R',
            'Ŗ' => 'R',
            'Ř' => 'R',


            'ś' => 's',
            'š' => 's',
            'ŝ' => 's',
            'ş' => 's',
            'Ś' => 'S',
            'Š' => 'S',
            'Ŝ' => 'S',
            'Ş' => 'S',

            'ţ' => 't',
            'ť' => 't',
            'ŧ' => 't',
            'Ţ' => 'T',
            'Ť' => 'T',
            'Ŧ' => 'T',


            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'ũ' => 'u',
            'ū' => 'u',
            'ŭ' => 'u',
            'ů' => 'u',
            'ű' => 'u',
            'ų' => 'u',
            'ư' => 'u',
            'ǔ' => 'u',
            'ǖ' => 'u',
            'ǘ' => 'u',
            'ǚ' => 'u',
            'ǜ' => 'u',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ũ' => 'U',
            'Ū' => 'U',
            'Ŭ' => 'U',
            'Ů' => 'U',
            'Ű' => 'U',
            'Ų' => 'U',
            'Ư' => 'U',
            'Ǔ' => 'U',
            'Ǖ' => 'U',
            'Ǘ' => 'U',
            'Ǚ' => 'U',
            'Ǜ' => 'U',


            'ŵ' => 'w',
            'Ŵ' => 'W',

            'ý' => 'y',
            'ÿ' => 'y',
            'ŷ' => 'y',
            'Ý' => 'Y',
            'Ÿ' => 'Y',
            'Ŷ' => 'Y',

            'ż' => 'z',
            'ź' => 'z',
            'ž' => 'z',
            'Ż' => 'Z',
            'Ź' => 'Z',
            'Ž' => 'Z',


            // accentuated ligatures
            'Ǽ' => 'A',
            'ǽ' => 'a',
        ];
        return strtr($cadenaremplazo, $map);
    }

    public function rechazarContratoLaboratorio($idContrato)
    {
        $comentarios = request('comentarios');

        if (strlen($comentarios) == 0) {
            //Comentarios vacio
            return back()->with('alerta', 'Campo especificaciónes obligatorio.');
        }

        $contrato = DB::select("SELECT estatus_estadocontrato FROM contratos WHERE id = '$idContrato'");

        if ($contrato != null) {

            $existeGarantia = DB::select("SELECT * FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia = '2' ORDER BY created_at ASC limit 1");

            if($existeGarantia == null){
                //Si no tiene garantia
                if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 16)) {

                    if ($contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11) {

                        $actualizar = Carbon::now();
                        $usuarioId = Auth::user()->id;

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

                            //Regresar pieza de armazon de los historiales del contrato
                            $historialesclinicos = DB::select("SELECT id_producto FROM historialclinico WHERE id_contrato = '$idContrato' ORDER BY created_at DESC");
                            if ($historialesclinicos != null) {
                                //Existen historiales
                                foreach ($historialesclinicos as $historialclinico) {
                                    DB::update("UPDATE producto
                                    SET piezas = piezas + 1,
                                    updated_at = '" . Carbon::now() . "'
                                    WHERE id = '" . $historialclinico->id_producto . "'");
                                }
                            }

                            DB::table('historialcontrato')->insert([
                                'id' => $idHistorialContratoAlfanumerico, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar,
                                'cambios' => " L - Contrato rechazado con la siguiente descripción: '$comentarios'"
                            ]);

                            //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato
                            DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");

                            //Eliminamos de la tabla contratoslaboratorio
                            $contratosGlobal = new contratosGlobal;
                            $contratosGlobal::actualizarRegistroTablaContratosLaboratorio($idContrato, "ELIMINAR");

                            //Actualizar datos tabla contratoslistatemporales
                            $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                            return redirect()->route('listalaboratorio')->with('bien', 'El contrato se rechazo correctamente.');

                        } catch (\Exception $e) {
                            \Log::info("Error: " . $e->getMessage());
                            return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
                        }

                    } else {
                        return back()->with('alerta', 'Necesitas permisos adicionales para hacer esto.');
                    }

                } else {
                    if (Auth::check()) {
                        return redirect()->route('redireccionar');
                    } else {
                        return redirect()->route('login');
                    }
                }

            }else{
                //El contrato contiene garantia
                return back()->with('alerta', 'Para continuar es necesario cancelar o terminar el proceso de garantia.');
            }
        }
        return back()->with('alerta', 'No se encontro el contrato.');
    }

//    public function filtrarcontratosenviados()
//    {
//        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 16)) {
//            //Rol director o laboratorio
//
//            $now = Carbon::now();
//            $fechainibuscar = request('fechainibuscar');
//            $fechafinbuscar = request('fechafinbuscar');
//            $franquiciaSeleccionada = request('franquiciaSeleccionada');
//
//            $cadenaFechaIniYFechaFin = " ";
//            $cadenaFranquiciaSeleccionada = " ";
//
//            //Validacion para fechaini y fechafin
//            if ($fechainibuscar == null && $fechafinbuscar == null) {
//                $cadenaFechaIniYFechaFin = " AND STR_TO_DATE(r.created_at,'%Y-%m-%d') >= STR_TO_DATE('$now','%Y-%m-%d') AND STR_TO_DATE(r.created_at,'%Y-%m-%d') <= STR_TO_DATE('$now','%Y-%m-%d')";
//            }else {
//
//                if (strlen($fechafinbuscar) > 0 && strlen($fechainibuscar) == 0) {
//                    //fechafin diferente de vacio y fechaini vacio
//                    return redirect()->route('listalaboratorio')->with('alerta', 'Debes agregar una fecha inicial');
//                }
//
//                if (strlen($fechainibuscar) > 0) {
//                    //fechaini diferente de vacio
//                    $fechainibuscar = Carbon::parse($fechainibuscar)->format('Y-m-d');
//                    if (strlen($fechafinbuscar) > 0) {
//                        //fechafin diferente de vacio
//                        $fechafinbuscar = Carbon::parse($fechafinbuscar)->format('Y-m-d');
//                    } else {
//                        //fechafin vacio
//                        $fechafinbuscar = Carbon::parse(Carbon::now())->format('Y-m-d');
//                    }
//                    if ($fechafinbuscar < $fechainibuscar) {
//                        //fechafin menor a fechaini
//                        return redirect()->route('listalaboratorio')->with('alerta', 'La fecha inicial debe ser menor o igual a la final.');
//                    }
//
//                    $cadenaFechaIniYFechaFin = " AND STR_TO_DATE(r.created_at,'%Y-%m-%d') >= STR_TO_DATE('$fechainibuscar','%Y-%m-%d')
//                                                 AND STR_TO_DATE(r.created_at,'%Y-%m-%d') <= STR_TO_DATE('$fechafinbuscar','%Y-%m-%d')";
//                }
//
//            }
//
//            if($franquiciaSeleccionada != null) {
//                $cadenaFranquiciaSeleccionada = " AND c.id_franquicia = '$franquiciaSeleccionada'";
//            }
//
//            $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000'");
//
//            $contratosComentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,f.ciudad,c.created_at,(SELECT hc.fechaentrega
//                                                            FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as fechaentrega FROM contratos c
//                                                            INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato INNER JOIN franquicias f on f.id = c.id_franquicia
//                                                            WHERE c.estatus_estadocontrato IN (7,10,11,12)
//                                                            AND c.banderacomentarioconfirmacion = 2 ORDER BY c.estatus_estadocontrato DESC, f.ciudad");
//
//            $contratosSComentariosLabo = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,f.ciudad,c.created_at,(SELECT hc.fechaentrega
//                                                                FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as fechaentrega
//                                                                FROM contratos c
//                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
//                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
//                                                                WHERE c.banderacomentarioconfirmacion != 2 AND
//                                                                c.estatus_estadocontrato IN (7,10,11)
//                                                                ORDER BY c.estatus_estadocontrato DESC,f.ciudad ASC
//                                                               ");
//
//            $contratosSComentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,f.ciudad,c.created_at,(SELECT hc.fechaentrega
//                                                                FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as fechaentrega, r.created_at as fechaenvio
//                                                                FROM contratos c
//                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
//                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
//                                                                INNER JOIN registroestadocontrato r ON r.id_contrato = c.id
//                                                                WHERE c.banderacomentarioconfirmacion != 2 AND
//                                                                c.estatus_estadocontrato = 12
//                                                                " . $cadenaFechaIniYFechaFin . "
//                                                                " . $cadenaFranquiciaSeleccionada . "
//                                                                ORDER BY c.estatus_estadocontrato DESC,f.ciudad ASC
//                                                               ");
//
//
//            return view("administracion.laboratorio.tabla", [
//                "contratosComentarios" => $contratosComentarios,
//                "contratosSComentarios" => $contratosSComentarios,
//                'contratosSComentariosLabo' => $contratosSComentariosLabo,
//                'franquicias' => $franquicias,
//                'fechainibuscar' => $fechainibuscar,
//                'fechafinbuscar' => $fechafinbuscar,
//                'franquiciaSeleccionada' => $franquiciaSeleccionada
//            ]);
//
//        } else {
//            if (Auth::check()) {
//                return redirect()->route('redireccionar');
//            } else {
//                return redirect()->route('login');
//            }
//        }
//    }

    public function contratosenviadostiemporeal(Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 16)) {
            //Rol director o laboratorio

            $filtro = $request->input('filtro');
            $fechainibuscar = $request->input('fechainibuscar');
            $fechafinbuscar = $request->input('fechafinbuscar');
            $franquiciaSeleccionada = $request->input('franquiciaSeleccionada');

            $cadenaFranquiciaSeleccionada = " ";
            $cadenaFiltro = " ";

            $hoy = Carbon::now()->format('Y-m-d');

            if(strlen($fechainibuscar) == 0){
                $fechainibuscar = $hoy;

            } if(strlen($fechafinbuscar) == 0){
                $fechafinbuscar = $hoy;
            }

            if (strlen($fechainibuscar) > 0) {
                //fechaini diferente de vacio
                $fechainibuscar = Carbon::parse($fechainibuscar)->format('Y-m-d');
            } if (strlen($fechafinbuscar) > 0) {
                //fechafin diferente de vacio
                $fechafinbuscar = Carbon::parse($fechafinbuscar)->format('Y-m-d');
            }

            $cadenaFechaIniYFechaFin = " AND STR_TO_DATE(r.created_at,'%Y-%m-%d') >= STR_TO_DATE('$fechainibuscar','%Y-%m-%d')
                                         AND STR_TO_DATE(r.created_at,'%Y-%m-%d') <= STR_TO_DATE('$fechafinbuscar','%Y-%m-%d')";


            if($franquiciaSeleccionada != null) {
                $cadenaFranquiciaSeleccionada = " AND c.id_franquicia = '$franquiciaSeleccionada'";
            }

            if($filtro != null){
                $cadenaFiltro = "AND c.id like '%$filtro%'";
            }
            $contratosEnviadosSComentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,f.ciudad,c.created_at,
                                                                (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC limit 1) as fechaentrega,
                                                                r.created_at as fechaenvio,
                                                                (SELECT rec.created_at FROM registroestadocontrato rec
                                                                WHERE rec.id_contrato = c.id AND rec.estatuscontrato = 10
                                                                ORDER BY rec.created_at DESC limit 1) as ultimoestatusmanufactura
                                                                FROM contratos c
                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                INNER JOIN franquicias f ON f.id = c.id_franquicia
                                                                INNER JOIN registroestadocontrato r ON r.id_contrato = c.id
                                                                WHERE c.banderacomentarioconfirmacion != 2 AND
                                                                c.estatus_estadocontrato = 12
                                                                " . $cadenaFechaIniYFechaFin . "
                                                                " . $cadenaFranquiciaSeleccionada . "
                                                                " . $cadenaFiltro . "
                                                                ORDER BY c.estatus_estadocontrato DESC,f.ciudad ASC
                                                               ");

            $view = view("administracion.laboratorio.contratosenviados.tablacontratosenviados", [
                "contratosEnviadosSComentarios" => $contratosEnviadosSComentarios
            ])->render();

            return \Response::json(array("vaid"=>"true", "view"=>$view,"fechainibuscar" => $fechainibuscar, "fechafinbuscar" => $fechafinbuscar));

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function obtenerEstadoPromocion($idContrato)
    {
        $respuesta = false;

        $contrato = DB::select("SELECT * FROM contratos WHERE id = '$idContrato'");
        if ($contrato[0]->idcontratorelacion != null) {
            //Es un contrato hijo
            $idContrato = $contrato[0]->idcontratorelacion;
        }

        $promocioncontrato = DB::select("SELECT * FROM promocioncontrato WHERE id_contrato = '$idContrato'");

        if ($promocioncontrato != null) {
            if ($promocioncontrato[0]->estado == 1) {
                //Promocion esta activa
                $respuesta = true;
            }
        }
        return $respuesta;
    }

    public function cancelarGarantiaHistorialLaboratorio($idContrato, $idHistorial, Request $request)
    {
        if (Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 16))) {

            try {

                //Validacion de campo de mensaje
                $validacion = Validator::make($request->all(),[
                    'mensaje'=>'required|string|min:15|max:1000'
                ]);

                if($validacion->fails()){
                    return back()->with('alerta','El mensaje para cancelación de garantía debe contener como minimo 15 caracteres y un maximo de 1000.');
                }

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
                                $usuarioId = Auth::user()->id;

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

                                        if ($this->obtenerEstadoPromocion($idContrato)) {
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
                                        return redirect()->route('listaconfirmaciones')->with('alerta', 'No se encontro el contrato.');
                                    }

                                    //Actualizar estadogarantia a 4
                                    DB::table('garantias')->where([['id', '=', $idGarantia], ['id_contrato', '=', $idContrato], ['id_historial', '=', $idhistorial]])->update([
                                        'estadogarantia' => 4,
                                        'updated_at' => Carbon::now()
                                    ]);
                                    //Guardar movimiento
                                    DB::table('historialcontrato')->insert([
                                        'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato,
                                        'created_at' => Carbon::now(), 'cambios' => "L - Cancelo la garantia al historial '$idhistorial' con el siguiente mensaje: '" . $request->input('mensaje') . "'"
                                    ]);

                                    //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato
                                    DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");

                                    //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                                    $contratosGlobal = new contratosGlobal;
                                    $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, Auth::id());

                                    //Eliminamos contrato de tabla contratoslaboratorio
                                    $contratosGlobal::actualizarRegistroTablaContratosLaboratorio($idContrato, "ELIMINAR");

                                    //Actualizar datos tabla contratoslistatemporales
                                    $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                                }

                                return redirect()->route('listalaboratorio')->with('bien', 'Se cancelo correctamente la garantia.');
                            }

                            //No tiene garantias para cancelar
                            return redirect()->route('listalaboratorio')->with('alerta', 'No se puede cancelar la garantia por que no tiene asignada.');

                        }

                        return back()->with("alerta", "Necesitas permisos adicionales para hacer esto.");

                    }

                    return back()->with("alerta","No se encontro el contrato.");

                } else {
                    //No presenta historial clinico el contrato
                    return back()->with("alerta","No se encontro el historial clinico del contrato.");
                }

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizararmazonlaboratorio($idContrato, $idHistorial)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 16) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8)) {
            //Acesso solo para rol de: LABORATORIO - DIRECTOR - PRINCIPAL

            $armazon = request('armazon' . $idHistorial);

            if ($armazon == null) {
                return back()->with('alerta', 'Por favor selecciona un armazón.');
            }

            $existeArmazon = DB::select("SELECT * FROM producto WHERE id = '$armazon' AND id_tipoproducto = '1' LIMIT 1");

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

                            if ($idArmazonActual == $armazon) {
                                //No se podra actualizar al mismo armazon
                                return back()->with('alerta', 'No se puede actualizar al mismo armazón.');
                            }

                            $armazonActual = DB::select("SELECT * FROM producto WHERE id = '$idArmazonActual' AND id_tipoproducto = '1'");

                            //Sumarle una pieza al producto que se quito
                            DB::table('producto')->where('id', '=', $idArmazonActual)->update([
                                'piezas' => $armazonActual[0]->piezas + 1
                            ]);

                            //Restarle una pieza al producto que se actualizo
                            DB::table('producto')->where('id', '=', $armazon)->update([
                                'piezas' => $existeArmazon[0]->piezas - 1
                            ]);

                            $globalesServicioWeb = new globalesServicioWeb;
                            //Guardar movimiento
                            DB::table('historialcontrato')->insert([
                                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => Auth::user()->id, 'id_contrato' => $idContrato,
                                'created_at' => Carbon::now(), 'cambios' => " L - Modifico el historial clinico: '$idHistorial', Se cambio el armazon"
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
                                        'id_producto' => $armazon
                                    ]);

                                    //Actualizar id_producto en historiales clinicos garantias
                                    DB::update("UPDATE historialclinico hc
                                                        INNER JOIN garantias g ON g.id_historialgarantia = hc.id
                                                        SET hc.id_producto = '$armazon'
                                                        WHERE g.id_historial = '" . $historialPadre[0]->id_historial . "' AND hc.id_contrato = '$idContrato'");
                                }
                            }else {
                                //No es garantia
                                //Actualizar id_producto en historialclinico
                                DB::table('historialclinico')->where([['id_contrato', '=', $idContrato], ['id', '=', $idHistorial]])->update([
                                    'id_producto' => $armazon
                                ]);
                            }

                            return back()->with("bien", "El historial clinico se actualizo correctamente.");

                        } catch (\Exception $e) {
                            \Log::info("Error: " . $e->getMessage());
                            return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
                        }

                    }
                    //NO existe el historialclinico
                    return back()->with('alerta', 'No se encontro el historial.');

                }else{
                    //NO existe el contrato
                    return back()->with('alerta', 'No se encontro el contrato.');
                }

            }else {
                //No existe la armazon
                return back()->with('alerta', ' El armazón seleccionado no es válido.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function agregarproductoarmazoncontratolaboratorio($idContrato)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 16) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8)) {
            //Acesso solo para rol de: LABORATORIO - DIRECTOR - PRINCIPAL

            $producto = request('producto');

            if ($producto == null) {
                return back()->with('alerta', 'Por favor selecciona un armazón.');
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
                            'id_usuario' => Auth::user()->id, 'id_producto' => $producto, 'piezas' => 1, 'total' => 0, 'created_at' => Carbon::now()
                        ]);

                        //Restarle una pieza al producto que se actualizo
                        DB::table('producto')->where('id', '=', $producto)->update([
                            'piezas' => $existeArmazon[0]->piezas - 1
                        ]);

                        //Guardar movimiento producto
                        DB::table('historialcontrato')->insert([
                            'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => Auth::user()->id, 'id_contrato' => $idContrato,
                            'created_at' => Carbon::now(), 'cambios' => " L - Agrego el armazón: '" . $existeArmazon[0]->id . "'-'" . $existeArmazon[0]->nombre . "' cantidad de piezas: '1'"
                        ]);

                        //Agregar abono al contrato
                        DB::table('abonos')->insert([
                            'id' => $globalesServicioWeb::generarIdAlfanumerico('abonos', '5'),
                            'folio' => null,
                            'id_franquicia' => $idFranquicia,
                            'id_contrato' => $idContrato,
                            'id_usuario' => Auth::user()->id,
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
                            'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => Auth::user()->id, 'id_contrato' => $idContrato,
                            'created_at' => Carbon::now(), 'cambios' => " L - Agrego el abono : '0'"
                        ]);

                        return back()->with("bien", "El armazón se agrego correctamente.");

                    } catch (\Exception $e) {
                        \Log::info("Error: " . $e->getMessage());
                        return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
                    }

                }else{
                    //NO existe el contrato
                    return back()->with('alerta', 'No se encontro el contrato.');
                }

            }else {
                //No existe la armazon
                return back()->with('alerta', ' El armazón seleccionado no es válido.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function agregarhistorialmovimientolaboratorio($idContrato)
    {
        if (Auth::check() && (((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8))) {
            //Rol administrador, director o principal

            try {

                $contrato = DB::select("SELECT c.estatus_estadocontrato as estatus_estadocontrato
                                                FROM contratos c WHERE c.id = '$idContrato'");

                if ($contrato != null) {
                    //Existe el contrato

                    $movimiento = request('movimiento');

                    if (strlen($movimiento) == 0) {
                        return back()->with('alerta', "Favor de agregar el mensaje de movimiento.");
                    }

                    //Guardar en tabla historialcontrato
                    $usuarioId = Auth::user()->id;
                    DB::table('historialcontrato')->insert([
                        'id' => $this->getHistorialContratoId(), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                        'cambios' => " L - Agrego el movimiento: " . $movimiento
                    ]);

                    return back()->with('bien', "Se agrego correctamente el movimiento.");

                }
                return back()->with('alerta', 'No se encontro el contrato.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function listaproductoslaboratorio(){
        if (Auth::check() && ((Auth::user()->rol_id) == 16 || (Auth::user()->rol_id) == 7)) {
            //ACEESO ROL: LABORATORIO, DIRECTOR
            $hoy = Carbon::now()->format('Y-m-d');

            $listaProductos = DB::select("SELECT (SELECT f.ciudad FROM franquicias f WHERE f.id = c.id_franquicia) AS sucursal,
                                                            (SELECT u.name FROM users u WHERE u.id = hc.id_usuarioC) AS usuariocreacion,
                                                            hc.cambios, hc.created_at, c.id as id_contrato
                                                            FROM historialcontrato hc
                                                            INNER JOIN contratos c ON c.id = hc.id_contrato
                                                            WHERE hc.tipomensaje = '4'
                                                            AND STR_TO_DATE(hc.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$hoy','%Y-%m-%d') AND STR_TO_DATE('$hoy','%Y-%m-%d')
                                                            ORDER BY hc.created_at DESC");

            return view("administracion.laboratorio.tablacompraproductos", [
                'listaProductos' => $listaProductos,
                'fechaInicio' => $hoy,
                'fechaFinal' => $hoy

            ]);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function filtrarlistaproductoslaboratorio(Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 16 || (Auth::user()->rol_id) == 7)) {
            //ACEESO ROL: LABORATORIO, DIRECTOR

            $fechaInicio = $request->input('fechaInicio');
            $fechaFinal = $request->input('fechaFinal');

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
                return redirect()->route('listaproductoslaboratorio')->with('alerta',' Fecha de inicio debe ser menor o igual a final');
            }

            $listaProductos = DB::select("SELECT (SELECT f.ciudad FROM franquicias f WHERE f.id = c.id_franquicia) AS sucursal,
                                                            (SELECT u.name FROM users u WHERE u.id = hc.id_usuarioC) AS usuariocreacion,
                                                            hc.cambios, hc.created_at, c.id as id_contrato
                                                            FROM historialcontrato hc
                                                            INNER JOIN contratos c ON c.id = hc.id_contrato
                                                            WHERE hc.tipomensaje = '4'
                                                            AND STR_TO_DATE(hc.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicio','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d')
                                                            ORDER BY hc.created_at DESC");

            return view("administracion.laboratorio.tablacompraproductos", [
                'listaProductos' => $listaProductos,
                'fechaInicio' => $fechaInicio,
                'fechaFinal' => $fechaFinal

            ]);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function actualizarobservaciones($idContrato, $idHistorial){

        if (Auth::check() && ((Auth::user()->rol_id) == 16 || (Auth::user()->rol_id) == 7)) {
            //ACEESO ROL: LABORATORIO, DIRECTOR

            $observaciones = request('observaciones');

            //Validar campo
            if(strlen($observaciones) == 0){
                //Campo vacio
                return back()->with('alerta',' Ingrese las observaciones para el contrato.');
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
                            $usuarioId = Auth::user()->id;
                            DB::table('historialcontrato')->insert([
                                'id' => $this->getHistorialContratoId(), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                'cambios' => " L - Actualizo las observaciones."
                            ]);

                            return back()->with('bien', "Se actualizaron correctamente las observaciones.");
                        }else{
                            return back()->with('alerta',' No se puede actualizar las observaciones debido al estatus actual del contrato.');
                        }
                    }else{
                        return back()->with('alerta', "No se encontro el historial del contrato.");
                    }

                } else {
                    return back()->with('alerta',' No se encontro el contrato.');
                }
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function listaarmazoneslaboratorio(){
        if (Auth::check() && ((Auth::user()->rol_id) == 16 || (Auth::user()->rol_id) == 7)) {
            //ACEESO ROL: LABORATORIO, DIRECTOR

            $armazones = DB::select("SELECT p.id, p.nombre, p.color, p.totalpiezas FROM producto p WHERE p.id_tipoproducto = '1' AND p.estado = 1 ORDER BY p.nombre ASC");
            $soliciudes = DB::select("SELECT sab.indice, (SELECT p.nombre FROM producto p WHERE p.id = sab.id_armazon) AS armazon, sab.fotofrente,
                                            sab.fotoatras, sab.fotolado1, sab.fotolado2, (SELECT a.estatus FROM autorizaciones a WHERE a.indice = sab.id_autorizacion) as estado
                                            FROM solicitudarmazonbaja sab ORDER BY sab.created_at DESC ");

            return view("administracion.laboratorio.tablaarmazonesinventario", [
                'armazones' => $armazones, 'soliciudes' => $soliciudes
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function crearsolicitudarmazonbaja(Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 16 || (Auth::user()->rol_id) == 7)) {
            //ACEESO ROL: LABORATORIO, DIRECTOR

            request()->validate([
                'armazon' => 'required|string',
                'fotofrente' => 'required|image|mimes:jpg',
                'fotoatras' => 'required|image|mimes:jpg',
                'fotolado1' => 'required|image|mimes:jpg',
                'fotolado2' => 'required|image|mimes:jpg',
                'descripcion' => 'required|string'
            ]);

            $armazon = $request->input('armazon');
            if($armazon != null){
                $existeArmazon = DB::select("SELECT * FROM producto p WHERE p.id = '$armazon' AND p.id_tipoproducto = '1' LIMIT 1");
            }else{
                //No se selecciono armazon
                return  back()->with('alerta',"Selecciona un armazón valido.");
            }

            if($existeArmazon != null){

                //Solicitamos autorizacion
                $idUsuario = Auth::user()->id;
                DB::table('autorizaciones')->insert([
                    'id_usuarioC' => $idUsuario, 'mensaje' => "Solicitó autorizacion para baja de armazon: '" . $existeArmazon[0]->nombre . " | " . $existeArmazon[0]->color . "' con motivo de: '" .  $request->input('descripcion') ."'",
                    'estatus' => '0', 'tipo' => '13', 'created_at' => Carbon::now()
                ]);

                $ultimaAutorizacion = DB::select("SELECT a.indice FROM autorizaciones a WHERE a.id_usuarioC = '$idUsuario' AND tipo = '13' ORDER BY a.created_at DESC LIMIT 1");
                $idSolicitud = ($ultimaAutorizacion != null) ? $ultimaAutorizacion[0]->indice: "";

                try{
                    //Foto frente
                    $fotofrente = "";
                    if (request()->hasFile('fotofrente')) {
                        $foto1Bruta = 'Fotofrente-Laboratorio-Autorizacion' . $idSolicitud . '-' . time() . '.' . request()->file('fotofrente')->getClientOriginalExtension();
                        $fotofrente = request()->file('fotofrente')->storeAs('uploads/imagenes/laboratorio/solicitudes', $foto1Bruta, 'disco');
                        $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/laboratorio/solicitudes/' . $foto1Bruta)->height();
                        $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/laboratorio/solicitudes/' . $foto1Bruta)->width();
                        if ($alto > $ancho) {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/laboratorio/solicitudes/' . $foto1Bruta)->resize(600, 800);
                        } else {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/laboratorio/solicitudes/' . $foto1Bruta)->resize(800, 600);
                        }
                        $imagenfoto->save();
                    }

                    //Foto atras
                    $fotoatras = "";
                    if (request()->hasFile('fotoatras')) {
                        $foto2Bruta = 'Fotoatras-Laboratorio-Autorizacion' . $idSolicitud . '-' . time() . '.' . request()->file('fotoatras')->getClientOriginalExtension();
                        $fotoatras = request()->file('fotoatras')->storeAs('uploads/imagenes/laboratorio/solicitudes', $foto2Bruta, 'disco');
                        $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/laboratorio/solicitudes/' . $foto2Bruta)->height();
                        $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/laboratorio/solicitudes/' . $foto2Bruta)->width();
                        if ($alto > $ancho) {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/laboratorio/solicitudes/' . $foto2Bruta)->resize(600, 800);
                        } else {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/laboratorio/solicitudes/' . $foto2Bruta)->resize(800, 600);
                        }
                        $imagenfoto->save();
                    }

                    //Foto lado 1
                    $fotolado1 = "";
                    if (request()->hasFile('fotolado1')) {
                        $foto3Bruta = 'Fotolado1-Laboratorio-Autorizacion' . $idSolicitud . '-' . time() . '.' . request()->file('fotolado1')->getClientOriginalExtension();
                        $fotolado1 = request()->file('fotolado1')->storeAs('uploads/imagenes/laboratorio/solicitudes', $foto3Bruta, 'disco');
                        $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/laboratorio/solicitudes/' . $foto3Bruta)->height();
                        $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/laboratorio/solicitudes/' . $foto3Bruta)->width();
                        if ($alto > $ancho) {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/laboratorio/solicitudes/' . $foto3Bruta)->resize(600, 800);
                        } else {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/laboratorio/solicitudes/' . $foto3Bruta)->resize(800, 600);
                        }
                        $imagenfoto->save();
                    }

                    //Foto lado 2
                    $fotolado2 = "";
                    if (request()->hasFile('fotolado2')) {
                        $foto4Bruta = 'Fotolado2-Laboratorio-Autorizacion' . $idSolicitud . '-' . time() . '.' . request()->file('fotolado2')->getClientOriginalExtension();
                        $fotolado2 = request()->file('fotolado2')->storeAs('uploads/imagenes/laboratorio/solicitudes', $foto4Bruta, 'disco');
                        $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/laboratorio/solicitudes/' . $foto4Bruta)->height();
                        $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/laboratorio/solicitudes/' . $foto4Bruta)->width();
                        if ($alto > $ancho) {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/laboratorio/solicitudes/' . $foto4Bruta)->resize(600, 800);
                        } else {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/laboratorio/solicitudes/' . $foto4Bruta)->resize(800, 600);
                        }
                        $imagenfoto->save();
                    }

                    DB::table('solicitudarmazonbaja')->insert([
                        'id_autorizacion' => $idSolicitud, 'id_armazon' => $armazon, 'fotofrente' => $fotofrente, 'fotoatras' => $fotoatras,
                        'fotolado1' => $fotolado1, 'fotolado2' => $fotolado2, 'created_at' => Carbon::now()
                    ]);

                    return  back()->with('bien',"Solicitud para baja de armazón creada correctamente.");

                } catch (\Exception $e) {
                    \Log::error('Error: ' . $e->getMessage());
                    return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina.');
                }

            }else{
                //Armazon no encontrado
                return  back()->with('alerta',"No existe el armazon seleccionado, intenta de nuevo.");
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function solicitudarmazonbajarechazarautorizar($indice, $opcion){
        if (Auth::check() && ((Auth::user()->rol_id) == 16 || (Auth::user()->rol_id) == 7)) {
            //ACEESO ROL: LABORATORIO, DIRECTOR
            $existeSolicitud = DB::select("SELECT * FROM autorizaciones a WHERE a.indice = '$indice' AND tipo = '13'");
            if($existeSolicitud != null){

                $solicitudArmazon = DB::select("SELECT sab.id_armazon FROM solicitudarmazonbaja sab WHERE sab.id_autorizacion = '$indice'");
                $nombreArmazon = "Sin nombre";
                $color = "Sin color";
                $idArmazon = "Sin id";
                if($solicitudArmazon != null){
                    $idArmazon = $solicitudArmazon[0]->id_armazon;
                    $armazon = DB::select("SELECT p.id, p.nombre, p.color FROM producto p WHERE p.id = '$idArmazon' AND p.id_tipoproducto = 1");
                    if($armazon != null){
                        $idArmazon = $armazon[0]->id;
                        $nombreArmazon = $armazon[0]->nombre;
                        $color = $armazon[0]->color;
                    }
                }

                switch ($opcion){
                    case 0:
                        //Rechazar
                        DB::table('autorizaciones')->where('indice', '=', $indice)->update([
                            'estatus' => '2', 'updated_at' => Carbon::now()
                        ]);

                        $cambio = "Rechazó solicitud para dar de baja 1 pieza del armazon: '" . $idArmazon . " | " . $nombreArmazon . " | " . $color . ".";
                        $mensaje = "Solicitud rechazada correctamente";
                        break;

                    case 1:
                        //Autorizar
                        DB::table('autorizaciones')->where('indice', '=', $indice)->update([
                            'estatus' => '1', 'updated_at' => Carbon::now()
                        ]);

                        DB::update("UPDATE producto SET totalpiezas = (totalpiezas - 1) WHERE id = '$idArmazon' AND id_tipoproducto = '1'");

                        $cambio = "Autorizó solicitud para dar de baja 1 pieza del armazon: '" . $idArmazon . " | " . $nombreArmazon . " | " . $color . ".";
                        $mensaje = "Solicitud aprobada correctamente";
                        break;
                }

                //Registrar movimiento
                $idUsuario = Auth::user()->id;
                $franquicia =DB::select("SELECT uf.id_franquicia FROM usuariosfranquicia uf WHERE uf.id_usuario = '$idUsuario'");
                DB::table('historialsucursal')->insert([
                    'id_usuarioC' => $idUsuario,
                    'id_franquicia' => $franquicia[0]->id_franquicia, 'tipomensaje' => '0',
                    'created_at' => Carbon::now(), 'cambios' => $cambio, 'seccion' => '2'
                ]);

                return back()->with('bien',$mensaje);

            }else{
                //No existe la solicitud}
                return back()->with('alerta','No existe la solicitud.');
            }


        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }
    //Funcion: llenarTablaContratosLaboratorio
    //Descripcion: Inserta todos los contratos en estatus aprobado, munufactura y proceso de envio en tabla contratoslaboratorio
    public function llenarTablaContratosLaboratorio(){

        try {
            $llenarTablaContratosLaboratorio  = DB::select("INSERT INTO contratoslaboratorio (id_contrato, ultimoestatusmanufactura, created_at)
                                                            SELECT c.id, (SELECT rec.created_at FROM registroestadocontrato rec
                                                            WHERE rec.id_contrato = c.id AND rec.estatuscontrato = 10
                                                            ORDER BY rec.created_at DESC LIMIT 1),SYSDATE()
                                                            FROM contratos c WHERE c.estatus_estadocontrato IN (7,10,11)");

        } catch (\Exception $e) {
            \Log::info("Error: Funcion : llenarTablaContratosLaboratorio: " . $e);
        }

        \Log::info("Funcion llenarTablaContratosLaboratorio terminada");

    }
}
