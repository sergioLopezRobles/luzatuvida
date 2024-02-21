<?php

namespace App\Http\Controllers\Dominios\Administracion;

use App\Clases\contratosGlobal;
use App\Clases\globalesServicioWeb;
use App\Http\Controllers\Exception;
use App\Http\Controllers\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Image;
use Session;

class confirmaciones extends Controller
{

    public function listaconfirmaciones(){
        if(Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15)))        {

            //Solo los roles de Administracion, principal, director y confirmaciones pueden entrar
            $filtro = request('filtro');
            $contratosScomentarios = null;
            $contratosConComentarios = null;
            $contratosSTerminar = null;
            $contratosPendientes = null;
            $contratosFueraConfimaciones = null;

            if($filtro != null){ //Tenemos un filtro?
                //Tenemos un filtro

                try{

                    if(Auth::user()->rol_id == 7){
                        //Es un usuario director
                        $contratosScomentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.fecharegistro, u.name,
                                                                          us.name as usuariocreacion, f.ciudad as sucursal, c.nombre, c.telefono, c.created_at,
                                                                          (SELECT g.id FROM garantias g WHERE g.id_contrato = c.id AND g.estadogarantia = '2' ORDER BY g.created_at DESC LIMIT 1) as tienegarantia
                                                                   FROM contratos c
                                                                   INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                   INNER JOIN usuariosfranquicia uf ON c.id_franquicia = uf.id_franquicia
                                                                   INNER JOIN users u ON c.id_optometrista = u.id
                                                                   INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                                   INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                   WHERE c.estatus_estadocontrato IN (1,9)
                                                                   AND (c.id like '%$filtro%' OR us.name like '%$filtro%' OR c.nombre like '%$filtro%' OR c.telefono like '%$filtro%'
                                                                       OR c.nombrereferencia like '%$filtro%' OR c.telefonoreferencia like '%$filtro%')
                                                                   AND c.banderacomentarioconfirmacion != 3
                                                                   AND c.id_franquicia != '00000'
                                                                   GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.fecharegistro,c.id_optometrista,u.name,
                                                                            us.name, f.ciudad, c.nombre, c.telefono, c.created_at
                                                                   ORDER BY c.estatus_estadocontrato,c.fecharegistro ASC");

                        $contratosConComentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.fecharegistro, u.name,
                                                                            us.name as usuariocreacion, f.ciudad as sucursal, c.nombre, c.telefono, c.created_at,
                                                                            (SELECT g.id FROM garantias g WHERE g.id_contrato = c.id AND g.estadogarantia = '2' ORDER BY g.created_at DESC LIMIT 1) as tienegarantia
                                                                   FROM contratos c
                                                                   INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                   INNER JOIN usuariosfranquicia uf ON c.id_franquicia = uf.id_franquicia
                                                                   INNER JOIN users u ON c.id_optometrista = u.id
                                                                   INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                                   INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                   WHERE c.estatus_estadocontrato IN (1,9)
                                                                   AND (c.id like '%$filtro%' OR us.name like '%$filtro%' OR c.nombre like '%$filtro%' OR c.telefono like '%$filtro%'
                                                                       OR c.nombrereferencia like '%$filtro%' OR c.telefonoreferencia like '%$filtro%')
                                                                   AND c.banderacomentarioconfirmacion = 3
                                                                   GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.fecharegistro,c.id_optometrista,u.name,
                                                                            us.name, f.ciudad, c.nombre, c.telefono, c.created_at
                                                                   ORDER BY c.estatus_estadocontrato,c.fecharegistro ASC");

                        $contratosSTerminar = DB::select("SELECT c.id,c.estatus_estadocontrato,c.fecharegistro, us.name as usuariocreacion, u.name as optometrista, ec.descripcion,
                                                                       f.ciudad as sucursal, c.nombre, c.telefono, c.created_at FROM contratos c
	                                                            INNER JOIN users u ON c.id_optometrista = u.id
                                                                INNER JOIN users as us ON  us.id =  c.id_usuariocreacion
                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                WHERE   c.id_franquicia != '00000' AND
                                                                (c.datos = 1 AND c.estatus_estadocontrato = 0 )AND
                                                                 (c.id like '%$filtro%' OR us.name like '%$filtro%' OR c.nombre like '%$filtro%' OR c.telefono like '%$filtro%'
                                                                  OR c.nombrereferencia like '%$filtro%' OR c.telefonoreferencia like '%$filtro%')
                                                                  ORDER BY c.estatus_estadocontrato DESC");

                        $contratosPendientes = DB::select("SELECT c.id,c.estatus_estadocontrato,c.fecharegistro, us.name as usuariocreacion, f.ciudad as sucursal, c.created_at FROM contratos c
                                                                INNER JOIN users as us ON  us.id =  c.id_usuariocreacion
                                                                INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                 WHERE   c.id_franquicia != '00000' AND
                                                                 (c.datos = 0 AND c.estatus_estadocontrato IS null ) AND
                                                                 (c.id like '%$filtro%' OR us.name like '%$filtro%' OR c.nombre like '%$filtro%' OR c.telefono like '%$filtro%'
                                                                  OR c.nombrereferencia like '%$filtro%' OR c.telefonoreferencia like '%$filtro%')
                                                                  ORDER BY c.estatus_estadocontrato DESC");

                        $contratosFueraConfimaciones = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.fecharegistro, u.name,
                                                                                us.name as usuariocreacion, f.ciudad as sucursal, c.nombre, c.telefono, c.created_at FROM contratos c
                                                                   INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                   INNER JOIN usuariosfranquicia uf ON c.id_franquicia = uf.id_franquicia
                                                                   INNER JOIN users u ON c.id_optometrista = u.id
                                                                   INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                                   INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                   WHERE c.estatus_estadocontrato IN (2,3,4,5,6,8,12,13,14)
                                                                   AND (c.id like '%$filtro%' OR us.name like '%$filtro%' OR c.nombre like '%$filtro%' OR c.telefono like '%$filtro%'
                                                                       OR c.nombrereferencia like '%$filtro%' OR c.telefonoreferencia like '%$filtro%')
                                                                   AND c.id_franquicia != '00000'
                                                                   GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.fecharegistro,c.id_optometrista,u.name,
                                                                            us.name, f.ciudad, c.nombre, c.telefono, c.created_at
                                                                   ORDER BY c.estatus_estadocontrato,c.fecharegistro ASC");


                    }else if(Auth::user()->rol_id == 15){
                        $contratosScomentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.fecharegistro, u.name,
                                                                          us.name as usuariocreacion, f.ciudad as sucursal, c.nombre, c.telefono, c.created_at,
                                                                          (SELECT g.id FROM garantias g WHERE g.id_contrato = c.id AND g.estadogarantia = '2' ORDER BY g.created_at DESC LIMIT 1) as tienegarantia
                                                FROM contratos c
                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                INNER JOIN sucursalesconfirmaciones sc ON c.id_franquicia = sc.id_franquicia
                                                INNER JOIN users u ON c.id_optometrista = u.id
                                                INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                WHERE sc.id_usuario = '".Auth::user()->id."'
                                                AND c.estatus_estadocontrato IN (1,9)
                                                AND (c.id like '%$filtro%' OR us.name like '%$filtro%' OR c.nombre like '%$filtro%' OR c.telefono like '%$filtro%'
                                                     OR c.nombrereferencia like '%$filtro%' OR c.telefonoreferencia like '%$filtro%')
                                                AND c.banderacomentarioconfirmacion != 3
                                                GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.fecharegistro,c.id_optometrista,u.name, us.name, f.ciudad, c.nombre, c.telefono, c.created_at
                                                ORDER BY c.estatus_estadocontrato,c.fecharegistro ASC");

                        $contratosConComentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.fecharegistro, u.name,
                                                                            us.name as usuariocreacion, f.ciudad as sucursal, c.nombre, c.telefono, c.created_at,
                                                                          (SELECT g.id FROM garantias g WHERE g.id_contrato = c.id AND g.estadogarantia = '2' ORDER BY g.created_at DESC LIMIT 1) as tienegarantia
                                                FROM contratos c
                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                INNER JOIN sucursalesconfirmaciones sc ON c.id_franquicia = sc.id_franquicia
                                                INNER JOIN users u ON c.id_optometrista = u.id
                                                INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                WHERE sc.id_usuario = '".Auth::user()->id."'
                                                AND c.estatus_estadocontrato IN (1,9)
                                                AND (c.id like '%$filtro%' OR us.name like '%$filtro%' OR c.nombre like '%$filtro%' OR c.telefono like '%$filtro%'
                                                     OR c.nombrereferencia like '%$filtro%' OR c.telefonoreferencia like '%$filtro%')
                                                AND c.banderacomentarioconfirmacion = 3
                                                GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.fecharegistro,c.id_optometrista,u.name, us.name, f.ciudad, c.nombre, c.telefono, c.created_at
                                                ORDER BY c.estatus_estadocontrato,c.fecharegistro ASC");

                        $contratosSTerminar = DB::select("SELECT c.id,c.estatus_estadocontrato,c.fecharegistro, us.name as usuariocreacion, u.name as optometrista, ec.descripcion,
                                                                       f.ciudad as sucursal, c.nombre, c.telefono, c.created_at FROM contratos c
	                                                            INNER JOIN users u ON c.id_optometrista = u.id
                                                                INNER JOIN users as us ON  us.id =  c.id_usuariocreacion
                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                WHERE   c.id_franquicia != '00000' AND
                                                                (c.datos = 1 AND c.estatus_estadocontrato = 0 )AND
                                                                 (c.id like '%$filtro%' OR us.name like '%$filtro%' OR c.nombre like '%$filtro%' OR c.telefono like '%$filtro%'
                                                                  OR c.nombrereferencia like '%$filtro%' OR c.telefonoreferencia like '%$filtro%')
                                                                  ORDER BY c.estatus_estadocontrato DESC");

                        $contratosPendientes = DB::select("SELECT c.id,c.estatus_estadocontrato,c.fecharegistro, us.name as usuariocreacion, f.ciudad as sucursal, c.created_at FROM contratos c
                                                                INNER JOIN users as us ON  us.id =  c.id_usuariocreacion
                                                                INNER JOIN sucursalesconfirmaciones sc ON c.id_franquicia = sc.id_franquicia
                                                                INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                 WHERE sc.id_usuario = '".Auth::user()->id."' AND
                                                                 (c.datos = 0 AND c.estatus_estadocontrato IS null ) AND
                                                                 (c.id like '%$filtro%' OR us.name like '%$filtro%' OR c.nombre like '%$filtro%' OR c.telefono like '%$filtro%'
                                                                 OR c.nombrereferencia like '%$filtro%' OR c.telefonoreferencia like '%$filtro%')
                                                                  ORDER BY c.estatus_estadocontrato DESC");

                        $contratosFueraConfimaciones = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato, c.id_optometrista, c.fecharegistro, u.name, us.name as usuariocreacion,
                                                                                f.ciudad as sucursal, c.nombre, c.telefono, c.created_at FROM contratos c
                                                                 INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                 INNER JOIN sucursalesconfirmaciones sc ON c.id_franquicia = sc.id_franquicia
                                                                 INNER JOIN users u ON c.id_optometrista = u.id
                                                                 INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                                 INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                 WHERE sc.id_usuario = '".Auth::user()->id."'
                                                                 AND c.estatus_estadocontrato IN (2,3,4,5,6,8,12,13,14)
                                                                 AND (c.id like '%$filtro%' OR us.name like '%$filtro%' OR c.nombre like '%$filtro%' OR c.telefono like '%$filtro%'
                                                                      OR c.nombrereferencia like '%$filtro%' OR c.telefonoreferencia like '%$filtro%')
                                                                 GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.fecharegistro,c.id_optometrista,u.name, us.name, f.ciudad, c.nombre, c.telefono, c.created_at
                                                                 ORDER BY c.estatus_estadocontrato,c.fecharegistro ASC");

                    }else{
                        //Cualquier otro usuario
                        return redirect()->route('redireccionar');
                    }

                }catch(\Exception $e){
                    \Log::info("Error".$e);
                }

            }else{
                //Sin filtro

                try{

                    if(Auth::user()->rol_id == 7){
                        //Es un usuario director

                        $contratosScomentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.fecharegistro, u.name,
                                                                           us.name as usuariocreacion, f.ciudad as sucursal, c.nombre, c.telefono, c.created_at,
                                                                          (SELECT g.id FROM garantias g WHERE g.id_contrato = c.id AND g.estadogarantia = '2' ORDER BY g.created_at DESC LIMIT 1) as tienegarantia
                                                                   FROM contratos c
                                                                   INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                   INNER JOIN usuariosfranquicia uf ON c.id_franquicia = uf.id_franquicia
                                                                   INNER JOIN users u ON c.id_optometrista = u.id
                                                                   INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                                   INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                   WHERE c.estatus_estadocontrato IN (1,9)
                                                                   AND c.banderacomentarioconfirmacion != 3
                                                                   AND c.id_franquicia != '00000'
                                                                   GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.fecharegistro,c.id_optometrista,u.name,
                                                                            us.name, f.ciudad, c.nombre, c.telefono, c.created_at
                                                                   ORDER BY c.estatus_estadocontrato,c.fecharegistro ASC");

                        $contratosConComentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.fecharegistro,
                                                                            u.name,c.nombre, us.name as usuariocreacion, f.ciudad as sucursal, c.nombre, c.telefono, c.created_at,
                                                                          (SELECT g.id FROM garantias g WHERE g.id_contrato = c.id AND g.estadogarantia = '2' ORDER BY g.created_at DESC LIMIT 1) as tienegarantia
                                                                   FROM contratos c
                                                                   INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                   INNER JOIN usuariosfranquicia uf ON c.id_franquicia = uf.id_franquicia
                                                                   INNER JOIN users u ON c.id_optometrista = u.id
                                                                   INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                                   INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                   WHERE c.estatus_estadocontrato IN (1,9)
                                                                   AND c.banderacomentarioconfirmacion = 3
                                                                   GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.fecharegistro,c.id_optometrista,u.name,c.nombre,
                                                                            us.name, f.ciudad, c.nombre, c.telefono, c.created_at
                                                                   ORDER BY c.estatus_estadocontrato,c.fecharegistro ASC");

                    }else if(Auth::user()->rol_id == 15){
                        //Es un usuario de confirmaciones

                        $contratosScomentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.fecharegistro, u.name,
                                                                          us.name as usuariocreacion, f.ciudad as sucursal, c.nombre, c.telefono, c.created_at,
                                                                          (SELECT g.id FROM garantias g WHERE g.id_contrato = c.id AND g.estadogarantia = '2' ORDER BY g.created_at DESC LIMIT 1) as tienegarantia
                                                FROM contratos c
                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                INNER JOIN sucursalesconfirmaciones sc ON c.id_franquicia = sc.id_franquicia
                                                INNER JOIN users u ON c.id_optometrista = u.id
                                                INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                WHERE sc.id_usuario = '".Auth::user()->id."'
                                                AND c.estatus_estadocontrato IN (1,9)
                                                AND c.banderacomentarioconfirmacion != 3
                                                GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.fecharegistro,c.id_optometrista,u.name, us.name, f.ciudad, c.nombre, c.telefono, c.created_at
                                                ORDER BY c.estatus_estadocontrato,c.fecharegistro ASC");

                        $contratosConComentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.fecharegistro,
                                                                            u.name, us.name as usuariocreacion, f.ciudad as sucursal, c.nombre, c.telefono, c.created_at,
                                                                          (SELECT g.id FROM garantias g WHERE g.id_contrato = c.id AND g.estadogarantia = '2' ORDER BY g.created_at DESC LIMIT 1) as tienegarantia
                                                FROM contratos c
                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                INNER JOIN sucursalesconfirmaciones sc ON c.id_franquicia = sc.id_franquicia
                                                INNER JOIN users u ON c.id_optometrista = u.id
                                                INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                WHERE sc.id_usuario = '".Auth::user()->id."'
                                                AND c.estatus_estadocontrato IN (1,9)
                                                AND c.banderacomentarioconfirmacion = 3
                                                GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.fecharegistro,c.id_optometrista,u.name, us.name, f.ciudad, c.nombre, c.telefono, c.created_at
                                                ORDER BY c.estatus_estadocontrato,c.fecharegistro ASC");

                    }else{
                        //Cualquier otro usuario
                        return redirect()->route('redireccionar');
                    }

                }catch(\Exception $e){
                    \Log::info("Error".$e);
                }

            }

            return view("administracion.confirmaciones.tabla",
                ["contratosScomentarios"=>$contratosScomentarios,
                    "contratosConComentarios"=>$contratosConComentarios,
                    "contratosSTerminar"=>$contratosSTerminar,
                    "contratosPendientes"=>$contratosPendientes,
                    "filtro"=>$filtro,
                    'contratosFueraConfimaciones' => $contratosFueraConfimaciones]);

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function estadoconfirmacion($idContrato){
        if(Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15)))
        {

            $actualizarHistorialBase = false;
            $actualizarHistorialGarantia = false;
            $numeroHistorialesBase = 0;
            $banderaEliminarHistorial = false;

            $existeContrato = DB::select("SELECT * FROM contratos WHERE id ='$idContrato'");
            if($existeContrato != null){
                //Si existe contrato
                $garantia = DB::select("SELECT id, indice FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia = '2' ORDER BY created_at ASC limit 1");

                if($garantia != null) {
                    //Tiene garantia en estado 2
                    $idGarantia = $garantia[0]->id;
                    $indice = $garantia[0]->indice;
                    //Eliminar garantia con estado en 2 si es que las hay excepto la elegida anteriormente
                    DB::delete("DELETE FROM garantias WHERE id = '$idGarantia' AND estadogarantia = '2' AND indice != '$indice'");
                }

                //Actualizar contrato en tabla contratostemporalessincronizacion
                $contratosGlobal = new contratosGlobal;
                $contratosGlobal::actualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato);

                //Eliminar garantias repetidas del contrato
                $contratosGlobal::eliminarGarantiasRepetidasTabla($idContrato);

                //Actualizar contrato en tabla contratoslistatemporales
                $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                //Calculo total
                $this->calculoTotal($idContrato, $existeContrato[0]->id_franquicia);

                $contrato = DB::select("SELECT c.id, c.id_franquicia, c.estatus_estadocontrato,ec.descripcion,z.zona,c.banderacomentarioconfirmacion,c.id_optometrista, c.alias, c.aprobacionventa,
                                          (SELECT u.name FROM users u WHERE u.id = c.id_optometrista) as opto,c.poliza,c.esperapoliza, c.idcontratorelacion, c.id_promocion, c.enganche,
                                          (SELECT hc.edad FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY c.created_at DESC LIMIT 1) as edad,
                                          c.nombre,c.calle,c.numero,c.depto,c.alladode,c.frentea,c.entrecalles,c.colonia,c.localidad,c.telefono,
                                          c.casatipo,c.casacolor,c.nombrereferencia,c.telefonoreferencia,c.correo,c.fotoine,c.fotoineatras,c.fotocasa, c.fotootros, c.comprobantedomicilio,
                                          c.tarjeta,c.tarjetapensionatras,c.pago,ec.descripcion,c.pagare,c.total,c.totalpromocion,c.calleentrega,c.numeroentrega,c.deptoentrega,c.alladodeentrega,
                                          c.frenteaentrega,c.entrecallesentrega,c.coloniaentrega,c.localidadentrega,c.casatipoentrega,c.casacolorentrega,c.nota, c.abonominimo,
                                          c.opcionlugarentrega, (SELECT SUM(a.abono) FROM abonos a WHERE a.id_contrato = c.id)as totalabonos,c.id_zona, c.subscripcion,
                                          (SELECT p.titulo FROM promocioncontrato pc INNER JOIN promocion p ON p.id = pc.id_promocion WHERE pc.id_contrato = c.id AND pc.estado = 1) as titulopromocion,
                                          (SELECT fechaentrega FROM historialclinico WHERE id_contrato = c.id ORDER BY c.created_at LIMIT 1) as fechaentrega, c.coordenadas,
                                          c.nombre_usuariocreacion as nombreasistente, c.id_usuariocreacion, c.totalreal,
                                          (SELECT SUM(prod.total) FROM contratoproducto prod WHERE prod.id_contrato = c.id)as totalproductos,
                                          (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia,
                                          (SELECT p.contarventa FROM promocion p WHERE p.id = (SELECT pc.id_promocion FROM promocioncontrato pc WHERE pc.id_contrato = c.id) AND p.status = 1) AS contarventacontrato,
                                          c.abonominimo, c.observacionfotoine, c.observacionfotoineatras, c.observacionfotocasa, c.observacioncomprobantedomicilio, c.observacionpagare, c.observacionfotootros
                                          FROM contratos c
                                          INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                          INNER JOIN zonas z ON z.id = c.id_zona
                                          WHERE c.id = '$idContrato'");

                if ($contrato[0]->estadogarantia != null && $contrato[0]->estadogarantia != 2 && $contrato[0]->estatus_estadocontrato == 1) {
                    //Tiene garantia y estado de la garantia es diferente de 2 y estatus del contrato es igual a TERMINADO

                    $ultimoRegistroEstadoContrato = DB::select("SELECT estatuscontrato FROM registroestadocontrato WHERE id_contrato ='$idContrato'
                                                     AND estatuscontrato NOT IN (1,9) ORDER BY created_at DESC LIMIT 1");
                    if ($ultimoRegistroEstadoContrato != null) {
                        //Existe ultimo registro de estatus ENTREGADO, ABONO ATRASADO O ENVIADO

                        //Actualizar estado del contrato
                        DB::table("contratos")->where("id", "=", $idContrato)->update([
                            "estatus_estadocontrato" => $ultimoRegistroEstadoContrato[0]->estatuscontrato,
                            "updated_at" => Carbon::now()
                        ]);

                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $idContrato,
                            'estatuscontrato' => $ultimoRegistroEstadoContrato[0]->estatuscontrato,
                            'created_at' => Carbon::now()
                        ]);

                        return redirect()->route('listaconfirmaciones')->with("alerta","El contrato se regreso a su estado anterior por que se cancelo garantía.");

                    }
                }

                $arrayHistoriales = array();

                $franquicia = DB::select("SELECT id_franquicia FROM contratos WHERE id = '$idContrato'");
                $idFranquicia =  $franquicia[0]->id_franquicia;
                $historiales =  DB::select("SELECT hc.indice, hc.id,hc.esfericoder,hc.cilindroder,hc.ejeder,hc.addder,hc.altder,hc.esfericoizq,hc.cilindroizq,hc.ejeizq,hc.addizq,hc.altizq,
                                                    (SELECT nombre FROM producto p WHERE p.id = hc.id_producto) as armazon, hc.tipo, hc.id_producto,
                                                    hc.material,hc.materialotro,hc.bifocal,hc.fotocromatico,hc.ar,hc.tinte,hc.blueray,hc.otroT,hc.tratamientootro,hc.observaciones,
                                                    hc.observacionesinterno,hc.created_at,hc.bifocalotro,hc.costomaterial, hc.costobifocal, hc.costotratamiento, hc.id_paquete, hc.fotoarmazon,
                                                    (SELECT color FROM producto p WHERE p.id = hc.id_producto) as colorarmazon,
                                                    (SELECT p.nombre FROM paquetes p WHERE p.id = hc.id_paquete AND p.id_franquicia = '$idFranquicia' LIMIT 1) as paquete,
                                                    (SELECT g.id FROM garantias g WHERE g.id_contrato = hc.id_contrato AND g.id_historialgarantia = hc.id) as garantia,
                                                    (SELECT u.name FROM users u WHERE u.id = (SELECT g.id_optometrista FROM garantias g WHERE g.id_contrato = hc.id_contrato
                                                                                                    AND g.id_historialgarantia = hc.id)) as optogarantia,
                                                    (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = hc.id_contrato AND g.id_historialgarantia = hc.id) as estadogarantia,
                                                    (SELECT g.id FROM garantias g WHERE g.id_historial = hc.id AND g.estadogarantia = 2) as cancelargarantia,
                                                    (SELECT hsc.esfericoder FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id AND hsc.id_contrato = hc.id_contrato) as hscesfericoder,
                                                    (SELECT hsc.cilindroder FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id AND hsc.id_contrato = hc.id_contrato) AS hsccilindroder,
                                                    (SELECT hsc.ejeder FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id AND hsc.id_contrato = hc.id_contrato) as hscejeder,
                                                    (SELECT hsc.addder FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id AND hsc.id_contrato = hc.id_contrato) as hscaddder,
                                                    (SELECT hsc.esfericoizq FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id AND hsc.id_contrato = hc.id_contrato) as hscesfericoizq,
                                                    (SELECT hsc.cilindroizq FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id AND hsc.id_contrato = hc.id_contrato) as hsccilindroizq,
                                                    (SELECT hsc.ejeizq FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id AND hsc.id_contrato = hc.id_contrato) as hscejeizq,
                                                    (SELECT hsc.addizq FROM historialsinconversion hsc WHERE hsc.id_historial = hc.id AND hsc.id_contrato = hc.id_contrato) as hscaddizq,
                                                    hc.id_tratamientocolortinte, hc.id_tratamientocolorpolarizado, hc.id_tratamientocolorespejo,
                                                    hc.policarbonatotipo, hc.estilotinte as estilotinte, hc.polarizado as polarizado, hc.espejo as espejo,
                                                    (SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = hc.id_tratamientocolortinte) as colortinte,
                                                    (SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = hc.id_tratamientocolorpolarizado) as colorpolarizado,
                                                    (SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = hc.id_tratamientocolorespejo) as colorespejo
                                                    FROM historialclinico hc
                                                    WHERE hc.id_contrato = '$idContrato' ORDER BY hc.created_at ASC");

                $historialesBase = array();
                $historialesActivos = array();
                $historialesCancelados = array();
                $historialesCambio = array();
                $historialesGarantiaTerminada = array();

                foreach ($historiales as $historial){

                    switch ($historial->tipo){
                        case 0:
                            //Historiales de tipo 0 -> historial base
                            array_push($historialesBase, $historial);
                            //Ingresaremos los idHistorial al arreglo - Funcionarara para crear los eventos de componentes en las pestañas de historiales
                            array_push($arrayHistoriales,$historial->id);
                            break;
                        case 1:
                            //Historiales de tipo 1 -> historial de garantia
                            switch ($historial->estadogarantia){
                                case 2:
                                    //Garantia creada
                                    array_push($historialesActivos,$historial);
                                    //Ingresaremos los idHistorial al arreglo - Funcionarara para crear los eventos de componentes en las pestañas de historiales
                                    array_push($arrayHistoriales,$historial->id);
                                    break;
                                case 3:
                                    //Garantia liberada por laboratorio
                                    array_push($historialesGarantiaTerminada, $historial);
                                    //Ingresaremos los idHistorial al arreglo - Funcionarara para crear los eventos de componentes en las pestañas de historiales
                                    array_push($arrayHistoriales,$historial->id);
                                    break;
                                case 4:
                                    //Garantia en estatus de cancelada
                                    array_push($historialesCancelados,$historial);
                            }
                            break;
                        case 2:
                            //Historiales de tipo 2 -> Cambio de paquete
                            array_push($historialesCambio, $historial);
                            break;
                    }
                }

                //Editar-Eliminar historiales
                if($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9){
                    //Contrato en NO TERMINADO - TERMINADO - PROCESO DE APROBACION
                    if($contrato[0]->estadogarantia != null){
                        //Tiene garantia
                        $actualizarHistorialGarantia = true;

                    }else{
                        //Sin garantia
                        $actualizarHistorialBase = true;

                        //Validar cantidad de historiales para poder eliminar
                        if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9){
                            //Contrato en TERMINADO - PROCESO DE APROBACION
                            if($historiales != null){
                                //Si presenta historiales
                                $idPaquete = $historiales[0]->id_paquete;
                                //Numero limite de historiales permitido por paquete
                                $numeroLimiteHistoriales = DB::select("SELECT p.numerohistoriales FROM paquetes p WHERE p.id = '$idPaquete' AND p.id_franquicia = '$idFranquicia'");
                                //Numero de historiales que presenta el contrato
                                $numeroHistorialesBase = sizeof($historiales);
                                if($numeroLimiteHistoriales != null){
                                    //Verificar si cumple con el limite de historiales el contrato
                                    $numeroHistorialesPaquete = $numeroLimiteHistoriales[0]->numerohistoriales;
                                    if($numeroHistorialesBase > $numeroHistorialesPaquete){
                                        //Contrato con mas de historiales clinicos permitidos
                                        $banderaEliminarHistorial = true;
                                    }
                                }
                            }
                        }
                    }
                }

                $zonas = DB::select("SELECT id,zona FROM zonas where id_franquicia ='$idFranquicia' ORDER BY id");

                $infoFranquicia = DB::select("SELECT estado,ciudad,colonia,numero FROM franquicias WHERE id = '".$franquicia[0]->id_franquicia."'");
                $comentarios = DB::select("SELECT u.name,m.comentario,m.fecha FROM mensajesconfirmaciones m INNER JOIN users u ON u.id = m.id_usuario
                                                    WHERE m.id_contrato = '$idContrato' ORDER BY m.fecha DESC");

                //Retornar historial movimientos
                $historialContrato = DB::select("SELECT u.name,hc.cambios,hc.created_at FROM historialcontrato hc INNER JOIN users u ON u.id = hc.id_usuarioC
                                                            WHERE hc.id_contrato = '$idContrato' ORDER BY created_at DESC");

                $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1");

                if($contrato[0]->banderacomentarioconfirmacion == 3){
                    DB::table("contratos")->where("id","=",$idContrato)->update([
                        "banderacomentarioconfirmacion" => 0
                    ]);
                }

                $productos = DB::select("SELECT * FROM producto WHERE id_franquicia = '$idFranquicia' AND id_tipoproducto = '2'");
                $contratoproducto = DB::select("SELECT p.nombre, p.precio, c.piezas, c.total, p.preciop,
                                                        (SELECT a.metodopago FROM abonos a WHERE a.id_contrato = '$idContrato' AND a.id_contratoproducto = c.id) as existeAbono
                                                        FROM contratoproducto c
                                                        inner join producto p on c.id_producto = p.id
                                                        WHERE id_contrato = '$idContrato'");

                $optometristas = DB::select("SELECT u.ID,u.NAME
                                    FROM users u
                                    INNER JOIN usuariosfranquicia uf
                                    ON uf.id_usuario = u.id
                                    WHERE uf.id_franquicia = '$idFranquicia'
                                    AND u.rol_id = 12 ORDER BY u.name");

                $asistentes = DB::select("SELECT u.ID,u.NAME
                                    FROM users u
                                    INNER JOIN usuariosfranquicia uf
                                    ON uf.id_usuario = u.id
                                    WHERE uf.id_franquicia = '$idFranquicia'
                                    AND u.rol_id IN (12,13) ORDER BY u.name");

                $datosDiagnosticoHistorial = DB::select("SELECT hc.edad, hc.diagnostico, hc.ocupacion, hc.diabetes, hc.hipertension,
                                                    hc.embarazada, hc.durmioseisochohoras, hc.actividaddia, hc.problemasojos, hc.dolor,
                                                    hc.ardor, hc.golpeojos, hc.otroM, hc.molestiaotro, hc.ultimoexamen
                                                    FROM historialclinico hc
                                                    WHERE id_contrato = '$idContrato' ORDER BY created_at ASC LIMIT 1");

                $paquetes = DB::select("SELECT id, nombre FROM paquetes WHERE id_franquicia = '$idFranquicia'");

                $armazones = DB::select("SELECT * FROM producto p WHERE id_tipoproducto = '1' ORDER BY p.nombre ASC");

                $solicitudAbonoMinimo = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato  = '$idContrato' AND (a.tipo = 7) ORDER BY a.created_at DESC LIMIT 1");

                //Promociones activadas para la sucursal
                $promociones = DB::select("SELECT * FROM promocion WHERE id_franquicia = '$idFranquicia' AND status = '1'");

                //Abonos registrados en el contrato
                $abonos = DB::select("SELECT * FROM abonos WHERE id_contrato ='$idContrato' ORDER BY created_at DESC");

                //Productos asignados al contrato
                $contratoproducto = DB::select("SELECT cp.id, cp.id_contrato, cp.created_at, cp.id_franquicia, p.id_tipoproducto, p.nombre, p.precio, cp.piezas, cp.total, p.preciop, p.color,
                                                        (SELECT a.metodopago FROM abonos a WHERE a.id_contrato = '$idContrato' AND a.id_contratoproducto = cp.id) as existeAbono
                                                        FROM contratoproducto cp
                                                        INNER JOIN producto p ON cp.id_producto = p.id
                                                        WHERE cp.id_contrato = '$idContrato'");

                //Zonas
                $zonasColonias = DB::select("SELECT * FROM zonas z WHERE z.id_franquicia = '$idFranquicia' ORDER BY z.zona ASC");
                //Colonias por zona
                $indice = 0;
                $arregloColonias = Array();

                foreach ($zonasColonias as $zonac){
                    $id_zona = $zonac->id;
                    $coloniasZona = DB::select("SELECT c.id_zona, c.colonia, c.localidad FROM colonias c WHERE c.id_zona = '$id_zona' ORDER BY c.localidad, c.colonia ASC");
                    array_push($arregloColonias, $coloniasZona);
                }

                //Bandera contrato con imagenes pendientes
                $banderaImagenesPendientes = false;

                if($contrato[0]->fotoine != null ){
                    //Existe en BD - Validar que existe en el servidor
                    if(!Storage::disk('disco')->exists($contrato[0]->fotoine)){
                        //No existe en el servidor
                        $banderaImagenesPendientes = true;
                    }
                }else{
                    //No existe en BD
                    $banderaImagenesPendientes = true;
                }

                if($contrato[0]->fotoineatras != null ){
                    //Existe en BD - Validar que existe en el servidor
                    if(!Storage::disk('disco')->exists($contrato[0]->fotoineatras)){
                        //No existe en el servidor
                        $banderaImagenesPendientes = true;
                    }
                }else{
                    //No existe en BD
                    $banderaImagenesPendientes = true;
                }

                if($contrato[0]->pagare != null ){
                    //Existe en BD - Validar que existe en el servidor
                    if(!Storage::disk('disco')->exists($contrato[0]->pagare)){
                        //No existe en el servidor
                        $banderaImagenesPendientes = true;
                    }
                }else{
                    //No existe en BD
                    $banderaImagenesPendientes = true;
                }

                if($contrato[0]->fotocasa != null ){
                    //Existe en BD - Validar que existe en el servidor
                    if(!Storage::disk('disco')->exists($contrato[0]->fotocasa)){
                        //No existe en el servidor
                        $banderaImagenesPendientes = true;
                    }
                }else{
                    //No existe en BD
                    $banderaImagenesPendientes = true;
                }

                //Colores tratamientos
                $coloresTratamientos = DB::select("SELECT * FROM tratamientoscolores tc WHERE tc.id_franquicia = '$idFranquicia'");

                //Promociones del contrato
                $promocionesConfirmaciones = DB::select("SELECT * FROM promocion WHERE id_franquicia = '$idFranquicia' AND status = '1' AND armazones = 1");

                $promocioncontrato = DB::select("SELECT id_promocion as id, p.titulo, p.asignado, p.inicio, p.fin, p.status,pr.id_contrato, pr.estado
                                                FROM promocioncontrato  pr
                                                inner join promocion p on pr.id_promocion = p.id
                                                WHERE id_contrato = '$idContrato'");

                $solicitudPromocion = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato = '$idContrato' AND a.tipo = 16 ORDER BY a.created_at DESC LIMIT 1");

                return view("administracion.confirmaciones.estadoconfirmacion",["contrato"=>$contrato,"infoFranquicia" => $infoFranquicia,"comentarios"=>$comentarios,
                    'idContrato'=>$idContrato,'zonas'=>$zonas, 'historialcontrato' => $historialContrato, 'historialesBase' => $historialesBase, 'historialesActivos' => $historialesActivos,
                    'historialesCancelados' => $historialesCancelados, 'historialesCambio' => $historialesCambio, 'historialesGarantiaTerminada' => $historialesGarantiaTerminada,
                    'tieneHistorialGarantia' => $tieneHistorialGarantia, 'productos' => $productos, 'contratoproducto' => $contratoproducto, 'asistentes' => $asistentes, 'optometristas' => $optometristas,
                    'datosDiagnosticoHistorial' => $datosDiagnosticoHistorial, 'garantia' => $garantia, 'idFranquicia' => $idFranquicia, 'paquetes' => $paquetes, 'armazones' => $armazones,
                    'solicitudAbonoMinimo' => $solicitudAbonoMinimo, 'promociones' => $promociones, 'abonos' => $abonos, 'contratoproducto' => $contratoproducto,
                    'zonasColonias' => $zonasColonias, 'arregloColonias' => $arregloColonias, 'indice' => $indice, 'banderaImagenesPendientes' => $banderaImagenesPendientes,
                    'actualizarHistorialBase' => $actualizarHistorialBase, 'actualizarHistorialGarantia' => $actualizarHistorialGarantia, 'coloresTratamientos'=> $coloresTratamientos,
                    'arrayHistoriales' => $arrayHistoriales, 'banderaEliminarHistorial' => $banderaEliminarHistorial, 'promocionesConfirmaciones' => $promocionesConfirmaciones,
                    'promocioncontrato' => $promocioncontrato, 'solicitudPromocion' => $solicitudPromocion
                ]);

            } else {
                //No existe contrato
                return redirect()->route('listaconfirmaciones')->with("alerta","No se encontro el contrato.");
            }

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function observacioninternalaboratoriohistorial($idContrato,$idHistorial,$opcion){
        if(Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15)))
        {

            //opcion
            //0-> Observacion laboratorio
            //1-> Observacion interna

            $contrato = DB::select("SELECT estatus_estadocontrato FROM contratos WHERE id = '$idContrato'");

            if($contrato != null) {
                //Existe el contrato

                if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9) {
                    //TERMINADO, APROBADO O EN PROCESO DE APROBACION

                    $historial = DB::select("SELECT id, observaciones, observacionesinterno FROM historialclinico WHERE id_contrato = '$idContrato' AND id = '$idHistorial'");

                    if ($historial != null) {//Existe el historial??
                        //Existe el historial

                        $observaciones = $historial[0]->observaciones;
                        $observacionesinterno = $historial[0]->observacionesinterno;

                        if($opcion == 0) {
                            //Observacion laboratorio
                            $campoobservacionlaboratorio = request('observacionlaboratorio');
                            $observaciones = $campoobservacionlaboratorio;
                            if ($campoobservacionlaboratorio == null) {
                                $observaciones = "";
                            }
                            $mensajeAlerta = "laboratorio";
                        }else {
                            //Observacion interna
                            $campoobservacioninterna = request('observacioninterna');
                            $observacionesinterno = $campoobservacioninterna;
                            if ($campoobservacioninterna == null) {
                                $observacionesinterno = "";
                            }
                            $mensajeAlerta = "interna";
                        }

                        DB::table("historialclinico")
                            ->where("id_contrato", "=", $idContrato)
                            ->where("id", "=", $idHistorial)
                            ->update([
                                "observaciones" => $observaciones,
                                "observacionesinterno" => $observacionesinterno
                            ]);

                        //Registrar movimiento
                        $globalesServicioWeb = new globalesServicioWeb;
                        DB::table('historialcontrato')->insert([
                            'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => Auth::user()->id, 'id_contrato' => $idContrato,
                            'created_at' => Carbon::now(), 'cambios' => "Actualizo observaciones " . $mensajeAlerta
                        ]);

                        return back()->with("bien", "La observacion " . $mensajeAlerta . " se actualizo correctamente.");

                    }

                    //No existe el historial
                    return back()->with("alerta", "No se encontro el historial.");

                }

                return back()->with("alerta", "Necesitas permisos adicionales para hacer esto.");

            }

            return back()->with("alerta","No se encontro el contrato.");

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }

    }

    public function actualizarfechaentregaconfirmaciones($idContrato) {

        $fechaentrega = request('fechaentrega');

        if (strlen($fechaentrega) > 0) {
            //fechaentrega diferente de vacio

            $fechaentrega = Carbon::parse($fechaentrega)->format('Y-m-d');

            try {

                $contrato = DB::select("SELECT estatus_estadocontrato FROM contratos WHERE id = '$idContrato'");

                if ($contrato != null) {
                    //Existe el contrato

                    if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9) {
                        //TERMINADO, APROBADO O EN PROCESO DE APROBACION

                        $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1");

                        $tipo = 0;
                        if ($tieneHistorialGarantia != null) {
                            //Tiene historiales con garantia
                            $tipo = 1;
                        }

                        $historiales = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = '$tipo'");

                        if ($historiales != null) {
                            foreach ($historiales as $historial) {
                                DB::table("historialclinico")->where("id", "=", $historial->id)->update([
                                    "fechaentrega" => $fechaentrega
                                ]);

                                //Registrar movimeinto en historialcontrato
                                $globalesServicioWeb = new globalesServicioWeb();
                                DB::table('historialcontrato')->insert([
                                    'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => Auth::user()->id, 'id_contrato' => $idContrato,
                                    'created_at' => Carbon::now(), 'cambios' => " Se actualizo la fecha de entrega a historial clinico '" . $historial->id ."'"
                                ]);
                            }
                        }

                        return back()->with("bien", "Se actualizo correctamente la fecha de entrega.");

                    }

                    return back()->with("alerta", "Necesitas permisos adicionales para hacer esto.");

                }

                return back()->with("alerta","No se encontro el contrato.");

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }

        }else {
            //fechaentrega vacio
            return back()->with("alerta","Campo fecha entrega vacio.");
        }

    }

    public function comentarioconfirmacion($idContrato){
        if(Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15)))
        {
            $rules = [
                'comentario' => 'required|string',
            ];
            request()->validate($rules);
            $contrato = DB::select("SELECT c.estatus_estadocontrato
                                          FROM contratos c
                                          WHERE c.id = '$idContrato'");
            if($contrato != null){
                if($contrato[0]->estatus_estadocontrato != 1 && $contrato[0]->estatus_estadocontrato != 7 && $contrato[0]->estatus_estadocontrato != 9
                    && $contrato[0]->estatus_estadocontrato != 10 && $contrato[0]->estatus_estadocontrato !=11){
                    return back()->with("alerta","Ya no tienes permisos de agregar comentarios.");
                }
            }

            try {
                $ahora = Carbon::now();
                DB::table('mensajesconfirmaciones')->insert([
                    "id_contrato"=>$idContrato,"id_usuario"=>Auth::user()->id,"comentario"=>request("comentario"),"fecha"=>$ahora
                ]);

                DB::table("contratos")->where("id","=",$idContrato)->update([
                    "banderacomentarioconfirmacion" => 2
                ]);

                return back()->with("bien","El mensaje se guardo correctamente");
            }catch(\Exception $e){
                return back()->with("alerta","Error: ".$e);
            }

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }

    }

    public function estadoconfirmacionactualizar($idContrato){
        if(Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15)))
        {

            $rules = [
                'estatus' => 'required|integer',
            ];
            request()->validate($rules);

            $estatus = request("estatus");
            $imagenesPendientesContrato =  request("banderaImagenesPendientes");
            $hoy = Carbon::now();

            $contratosGlobal = new contratosGlobal;
            $globalesServicioWeb = new globalesServicioWeb;
            $idHistorialContratoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');
            $usuarioId = Auth::user()->id;

            //Validar promociones del contrato
            $promocionPendiente = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato = '$idContrato' AND a.tipo = 16 AND a.estatus = 0 ORDER BY created_at DESC LIMIT 1");
            if($promocionPendiente != null && $estatus == 7){
                //Cambio de estatus a APROBADO y existe una solicitud de promocion tipo empleado pendiente por autorizar
                return back()->with('alerta',"La aprobación de la venta no es posible en este momento, ya que hay una solicitud de autorización de tipo 'PROMOCIÓN EMPLEADO' en este contrato.");
            }

            if($estatus == 1) {
                //Contrato con historial con garantia (Editable)

                $ultimoHistorialClinicoGarantia = DB::select("SELECT id, id_paquete, created_at FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1
                                                                    ORDER BY created_at DESC limit 1");

                if($ultimoHistorialClinicoGarantia != null) {

                    $idUltimoHistorialClinicoGarantia = $ultimoHistorialClinicoGarantia[0]->id;
                    $idPaqueteUltimoHistorialClinicoGarantia = $ultimoHistorialClinicoGarantia[0]->id_paquete;

                    if($idPaqueteUltimoHistorialClinicoGarantia == 6) {
                        //Dorado 2
                        $createdAtUltimoHistorialClinicoGarantia = $ultimoHistorialClinicoGarantia[0]->created_at;
                        $historialesClinicosGarantias = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1
                                                                            AND STR_TO_DATE(created_at,'%Y-%m-%d') = STR_TO_DATE('$createdAtUltimoHistorialClinicoGarantia','%Y-%m-%d')");

                        if($historialesClinicosGarantias != null) {
                            foreach ($historialesClinicosGarantias as $historialgarantia) {
                                if($historialgarantia->id != $idUltimoHistorialClinicoGarantia) {
                                    DB::table("garantias")->where("id_historialgarantia", "=", $historialgarantia->id)->update([
                                        "updated_at" => $hoy
                                    ]);
                                }
                            }
                        }
                    }

                    DB::table("garantias")->where("id_historialgarantia", "=", $idUltimoHistorialClinicoGarantia)->update([
                        "updated_at" => $hoy
                    ]);
                    DB::table("contratos")->where("id", "=", $idContrato)->update([
                        "estatus_estadocontrato" => $estatus,
                        "updated_at" => $hoy
                    ]);

                    //Insertar en tabla registroestadocontrato
                    DB::table('registroestadocontrato')->insert([
                        'id_contrato' => $idContrato,
                        'estatuscontrato' => $estatus,
                        'created_at' => Carbon::now()
                    ]);

                    DB::table('historialcontrato')->insert([
                        'id' => $idHistorialContratoAlfanumerico, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato,
                        'created_at' => $hoy, 'cambios' => "Se cambio el estatus a editable"
                    ]);

                    //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                    $ultimaGarantiaCreada = DB::select("SELECT id_optometrista FROM garantias WHERE id_contrato = '$idContrato' AND id_historialgarantia = '$idUltimoHistorialClinicoGarantia'
                                                                ORDER BY created_at LIMIT 1");
                    if($ultimaGarantiaCreada != null) {
                        $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $ultimaGarantiaCreada[0]->id_optometrista);
                    }

                    //Actualizar contrato en tabla contratoslistatemporales
                    $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                    return redirect()->route('listaconfirmaciones')->with("bien", "El estatus se actualizo correctamente y ahora se puede editar nuevamente.");

                }

            }else {
                //Contrato sin historial con garantia (NO TERMINADO, EN PROCESO DE APROBACION, APROBADO)

                if ($estatus != 0) {
                    if ($estatus < 7 || $estatus > 9) {
                        return back()->with("alerta", "Estatus no valido.");
                    }
                }
                $contrato = DB::select("SELECT estatus_estadocontrato, promocionterminada, id_zona, poliza, id_franquicia, pago FROM contratos WHERE id = '$idContrato'");

                if ($this->obtenerEstadoPromocion($idContrato) && $contrato[0]->promocionterminada == 0) {
                    //Tiene promocion y promocion no ha sido terminada
                    return redirect()->route('estadoconfirmacion', [$idContrato])->with('alerta', 'No se puede cambiar el estado por que tiene una promoción sin terminar');
                }

                $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1 ORDER BY created_at LIMIT 1");

                if (($contrato[0]->estatus_estadocontrato != 7 && $contrato[0]->poliza != null) || $contrato[0]->poliza == null || $tieneHistorialGarantia != null) {
                    //Estatus es diferente de APROBADO y no se ha contado en la poliza actual o tiene garantia

                    if ($contrato != null) {
                        if ($contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11 || $contrato[0]->estatus_estadocontrato == 12
                            || $contrato[0]->estatus_estadocontrato == 8) {
                            return back()->with("alerta", "No puedes cambiar el estatus del contrato en este momento.");
                        } else {
                            if ($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7) {

                                DB::table("contratos")->where("id", "=", $idContrato)->update([
                                    "updated_at" => $hoy,
                                    "estatus_estadocontrato" => $estatus
                                ]);

                                //Insertar en tabla registroestadocontrato
                                DB::table('registroestadocontrato')->insert([
                                    'id_contrato' => $idContrato,
                                    'estatuscontrato' => $estatus,
                                    'created_at' => Carbon::now()
                                ]);

                                //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                                if ($tieneHistorialGarantia != null) {
                                    //Tiene garantia
                                    $ultimaGarantiaCreada = DB::select("SELECT id_optometrista FROM garantias WHERE id_contrato = '$idContrato'
                                                                            AND id_historialgarantia = '" . $tieneHistorialGarantia[0]->id . "'
                                                                            ORDER BY created_at LIMIT 1");
                                    if ($ultimaGarantiaCreada != null) {
                                        $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $ultimaGarantiaCreada[0]->id_optometrista);
                                    }
                                } else {
                                    //No tiene garantia
                                    $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, Auth::id());
                                }

                                if (($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) && $tieneHistorialGarantia == null) {
                                    //Estado del contrato es igual a TERMINADO y no tiene garantia
                                    if ($contrato[0]->poliza == null) {
                                        //Poliza es igual a nulo
                                        $aprobacionventa = request("aprobacionventa");
                                        DB::table("contratos")->where("id", "=", $idContrato)->update([
                                            "aprobacionventa" => $aprobacionventa,
                                        ]);
                                    }
                                }

                                if ($estatus == 7) {
                                    //Cambio de estado a APROBADO

                                    //Validar bandera de imagenes pendientes para contrato
                                    $cambio = "Cambió el estatus a aprobado";

                                    if($imagenesPendientesContrato){
                                        $imagenes = DB::select("SELECT c.fotoine, c.fotoineatras, c.fotocasa, c.pagare FROM contratos c WHERE c.id = '$idContrato'");
                                        if($imagenes != null){
                                            $imagenesPendiente = "";
                                            if($imagenes[0]->fotoine == null){
                                                $imagenesPendiente = $imagenesPendiente . "Foto ine,";
                                            }else{
                                                //Existe en el servidor?
                                                if(!Storage::disk('disco')->exists($imagenes[0]->fotoine)){
                                                    //No existe en el servidor
                                                    $imagenesPendiente = $imagenesPendiente . "Foto ine,";
                                                }
                                            }
                                            if($imagenes[0]->fotoineatras == null){
                                                $imagenesPendiente = $imagenesPendiente . "Foto ine atras,";
                                            }else{
                                                //Existe en el servidor?
                                                if(!Storage::disk('disco')->exists($imagenes[0]->fotoineatras)){
                                                    //No existe en el servidor
                                                    $imagenesPendiente = $imagenesPendiente . "Foto ine atras,";
                                                }
                                            }
                                            if($imagenes[0]->fotocasa == null){
                                                $imagenesPendiente = $imagenesPendiente . "Foto casa,";
                                            }else{
                                                //Existe en el servidor?
                                                if(!Storage::disk('disco')->exists($imagenes[0]->fotocasa)){
                                                    //No existe en el servidor
                                                    $imagenesPendiente = $imagenesPendiente . "Foto casa,";
                                                }
                                            }
                                            if($imagenes[0]->pagare == null){
                                                $imagenesPendiente = $imagenesPendiente . "Foto pagare,";
                                            }else{
                                                //Existe en el servidor?
                                                if(!Storage::disk('disco')->exists($imagenes[0]->pagare)){
                                                    //No existe en el servidor
                                                    $imagenesPendiente = $imagenesPendiente . "Foto pagare,";
                                                }
                                            }
                                        }
                                        $imagenesPendiente = trim($imagenesPendiente,",");

                                        $cambio = "Cambió el estatus a aprobado con imagenes pendientes: '" . $imagenesPendiente . "'";
                                    }

                                    DB::table('historialcontrato')->insert([
                                        'id' => $idHistorialContratoAlfanumerico, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato,
                                        'created_at' => $hoy, 'cambios' => $cambio]);

                                    //Reducir calidad a imagenes del contrato
                                    $this->reducirCalidadImagenesContrato($idContrato);

                                    //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato
                                    DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");

                                    //Agregar contrato a cobradores si tiene garantia el contrato
                                    if ($tieneHistorialGarantia != null) {
                                        //Tiene garantia
                                        $cobradoresAsignadosAZona = DB::select("SELECT u.id
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id = uf.id_usuario
                                              WHERE u.rol_id = 4 AND u.id_zona = '" . $contrato[0]->id_zona . "'"); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona

                                        if ($cobradoresAsignadosAZona != null) {
                                            //Existen cobradores
                                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                                //Recorrido cobradores
                                                $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $cobradorAsignadoAZona->id);
                                            }
                                        }
                                    } else {
                                        //No tiene garantia

                                        $formapago = $contrato[0]->pago;
                                        $idFranquicia = $contrato[0]->id_franquicia;

                                        $abonominimocontrato = null;
                                        if ($formapago != 0) {
                                            //Forma de pago semanal, quincenal o mensual
                                            $abonominimocontrato = request("abonominimocontrato"); //Obtener abonominimocontrato
                                            if ($idFranquicia == 'TXDHF' || $idFranquicia == 'WJPQB') {
                                                //Franquicia es igual de XONACATLAN o ATLACOMULCO
                                                $abonominimocontrato = contratosGlobal::calculoCantidadFormaDePago($idFranquicia, $formapago);
                                            }else {
                                                //Franquicia es diferente de XONACATLAN o ATLACOMULCO
                                                //Insertar registro en tabla abonominimocontratos
                                                $contratosGlobal::insertarActualizarTablaAbonoMinimoContratos($idContrato, $contrato[0]->id_zona, $abonominimocontrato);
                                            }
                                        }

                                        DB::table("contratos")->where("id", "=", $idContrato)->update([
                                            "esperapoliza" => 0,
                                            "abonominimo" => $abonominimocontrato
                                        ]);

                                    }

                                    if ($estatus == 7) {
                                        //Cambio de estado a APROBADO
                                        $contratosGlobal::actualizarRegistroTablaContratosLaboratorio($idContrato, "INSERTAR");
                                    }

                                    //Actualizar contrato en tabla contratoslistatemporales
                                    $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                                    return redirect()->route('listaconfirmaciones')->with("bien", "El contrato fue aprobado.");
                                } elseif ($estatus == 0) {
                                    //Cambio de estado a NO TERMINADO
                                    DB::table('historialcontrato')->insert([
                                        'id' => $idHistorialContratoAlfanumerico, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato,
                                        'created_at' => $hoy, 'cambios' => "Se cambio el estatus a no terminado"
                                    ]);

                                    //Actualizar contrato en tabla contratoslistatemporales
                                    $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                                    return redirect()->route('listaconfirmaciones')->with("bien", "El estatus del contrato se actualizo correctamente a no terminado.");
                                }

                                //Cambio de estado a EN PROCESO DE APROBACION
                                DB::table('historialcontrato')->insert([
                                    'id' => $idHistorialContratoAlfanumerico, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato,
                                    'created_at' => $hoy, 'cambios' => "Se cambio el estatus a en proceso de aprobacion"
                                ]);

                                //Eliminamos de la tabla contratoslaboratorio
                                $contratosGlobal::actualizarRegistroTablaContratosLaboratorio($idContrato, "ELIMINAR");

                                //Actualizar contrato en tabla contratoslistatemporales
                                $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                                return back()->with("bien", "El estatus se actualizo correctamente.");

                            } else {
                                return back()->with("alerta", "Necesitas permisos adicionales para hacer esto.");
                            }

                        }

                    } else {
                        //El contrato no existe
                        return back()->with("bien", "El contrato no es valido.");
                    }

                }
                //Estatus es igual a APROBADO y se ha contado en la poliza actual
                return back()->with("alerta", "No es posible cambiar el estatus por que ya paso uno o mas dias de haber sido aprobado.");

            }

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function reducirCalidadImagenesContrato($idContrato)
    {

        $contrato = DB::select("SELECT fotoine, fotoineatras, fotocasa, comprobantedomicilio, pagare, tarjeta, tarjetapensionatras, fotootros FROM contratos WHERE id = '$idContrato'");

        if($contrato != null) {
            //Existe el contrato
            $rutaimagenes = array();

            array_push($rutaimagenes, $contrato[0]->fotoine);
            array_push($rutaimagenes, $contrato[0]->fotoineatras);
            array_push($rutaimagenes, $contrato[0]->fotocasa);
            array_push($rutaimagenes, $contrato[0]->comprobantedomicilio);
            array_push($rutaimagenes, $contrato[0]->pagare);
            array_push($rutaimagenes, $contrato[0]->tarjeta);
            array_push($rutaimagenes, $contrato[0]->tarjetapensionatras);
            array_push($rutaimagenes, $contrato[0]->fotootros);

            foreach ($rutaimagenes as $rutaimagen) {

                try {

                    if ($rutaimagen != null) {
                        $alto = Image::make(config('filesystems.disks.disco.root') . '/' . $rutaimagen)->height();
                        $ancho = Image::make(config('filesystems.disks.disco.root') . '/' . $rutaimagen)->width();
                        if ($alto > $ancho) {
                            $imagen = Image::make(config('filesystems.disks.disco.root') . '/' . $rutaimagen)->resize(600, 800);
                        } else {
                            $imagen = Image::make(config('filesystems.disks.disco.root') . '/' . $rutaimagen)->resize(800, 600);
                        }
                        $imagen->save();
                    }

                }catch(\Exception $e){
                    continue;
                }

            }
        }
    }

    public function confirmacionesagregardocumentos($idContrato){
        if(Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15)))
        {

            $contrato = DB::select("SELECT id,fotoine,fotoineatras,fotocasa,comprobantedomicilio,pagare,tarjeta,tarjetapensionatras,fotootros,estatus_estadocontrato,
                                            observacionfotoine, observacionfotoineatras, observacionfotocasa, observacioncomprobantedomicilio, observacionpagare, observacionfotootros
                                          FROM contratos WHERE id = '$idContrato'");

            if($contrato != null) {
                //Existe el contrato

                if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 10) {
                    //TERMINADO, APROBADO, EN PROCESO DE APROBACION O MANOFACTURA

                    request()->validate([
                        'fotoine' => 'nullable|image|mimes:jpg',
                        'fotocasa' => 'nullable|image|mimes:jpg',
                        '$fotoineatras' => 'nullable|image|mimes:jpg',
                        'pagare' => 'nullable|image|mimes:jpg',
                        'comprobantedomicilio' => 'nullable|image|mimes:jpg',
                        'tarjetapensionatras' => 'nullable|image|mimes:jpg',
                        'tarjetapension' => 'nullable|image|mimes:jpg',
                        'fotootros' => 'nullable|image|mimes:jpg'

                    ]);

                    //Validar tamaño de archivos adjuntos
                    $contratosGlobal = new contratosGlobal();
                    $contador = 0;
                    $nombreArchivos = "";

                    if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('fotoine'))){
                        $nombreArchivos = $nombreArchivos . " Foto INE frente,";
                        $contador = $contador + 1;
                    }
                    if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('$fotoineatras'))){
                        $nombreArchivos = $nombreArchivos . " Foto INE atras,";
                        $contador = $contador + 1;
                    }
                    if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('pagare'))){
                        $nombreArchivos = $nombreArchivos . " Pagare,";
                        $contador = $contador + 1;
                    }
                    if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('fotocasa'))){
                        $nombreArchivos = $nombreArchivos . " Foto de la casa,";
                        $contador = $contador + 1;
                    }
                    if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('comprobantedomicilio'))){
                        $nombreArchivos = $nombreArchivos . " Comprobante de domicilio,";
                        $contador = $contador + 1;
                    }
                    if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('tarjetapension'))){
                        $nombreArchivos = $nombreArchivos . " Tarjeta pension frente,";
                        $contador = $contador + 1;
                    }
                    if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('tarjetapensionatras'))){
                        $nombreArchivos = $nombreArchivos . " Tarjeta pension atras,";
                        $contador = $contador + 1;
                    }
                    if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('fotootros'))){
                        $nombreArchivos = $nombreArchivos . " Foto otros";
                        $contador = $contador + 1;
                    }

                    //Verificar si 1 o mas archivos no cumplen con el tamaño maximo
                    if($contador > 0){
                        $nombreArchivos = trim($nombreArchivos, ',');
                        if($contador == 1){
                            //Solo un archivo es demasiado pesado
                            return back()->with('alerta', "Verifica el archivo: " .$nombreArchivos.". El tamaño permitido para los archivos es maximo de 1MB.");
                        }else{
                            //2 o mas archivos sobrepasan el peso de 1MB
                            return back()->with('alerta', "Verifica los siguientes archivos: " .$nombreArchivos.". El tamaño permitido para los archivos es maximo de 1MB.");
                        }
                    }
                    $globalesServicioWeb = new globalesServicioWeb;
                    $usuarioId = Auth::user()->id;
                    $actualizar = Carbon::now();
                    $imagenesActualizadas = "";

                    if (request()->hasFile('fotoine')) {
                        //Se ingreso una identificacion
                        if ($contrato[0]->fotoine != null) {
                            //fotoine es diferente de nulo
                            Storage::disk('disco')->delete($contrato[0]->fotoine);
                        }
                        $fotoBruta = 'Foto-ine-' . $contrato[0]->id . '-' . time() . '.' . request()->file('fotoine')->getClientOriginalExtension();
                        $fotoine = request()->file('fotoine')->storeAs('uploads/imagenes/contratos/fotoine', $fotoBruta, 'disco');
                        $imagenesActualizadas = "INE frente, ";

                    } else {
                        //Se toamara la identificacion anterior
                        $fotoine = $contrato[0]->fotoine;
                    }

                    if (request()->hasFile('fotoineatras')) {
                        //Se ingreso una identificacion
                        if ($contrato[0]->fotoineatras != null) {
                            //fotoineatras es diferente de nulo
                            Storage::disk('disco')->delete($contrato[0]->fotoineatras);
                        }
                        $fotoBruta = 'Foto-ine-atras-' . $contrato[0]->id . '-' . time() . '.' . request()->file('fotoineatras')->getClientOriginalExtension();
                        $fotoineatras = request()->file('fotoineatras')->storeAs('uploads/imagenes/contratos/fotoineatras', $fotoBruta, 'disco');
                        $imagenesActualizadas = $imagenesActualizadas . "INE atras, ";

                    } else {
                        //Se toamara la identificacion anterior
                        $fotoineatras = $contrato[0]->fotoineatras;
                    }

                    if (request()->hasFile('fotocasa')) {
                        if ($contrato[0]->fotocasa != null) {
                            //fotocasa es diferente de nulo
                            Storage::disk('disco')->delete($contrato[0]->fotocasa);
                        }
                        $fotoBruta1 = 'Foto-casa-' . $contrato[0]->id . '-' . time() . '.' . request()->file('fotocasa')->getClientOriginalExtension();
                        $fotocasa = request()->file('fotocasa')->storeAs('uploads/imagenes/contratos/fotocasa', $fotoBruta1, 'disco');
                        $imagenesActualizadas = $imagenesActualizadas . "Casa, ";

                    } else {
                        $fotocasa = $contrato[0]->fotocasa;
                    }

                    if (request()->hasFile('comprobantedomicilio')) {
                        if ($contrato[0]->comprobantedomicilio != null) {
                            //comprobantedomicilio es diferente de nulo
                            Storage::disk('disco')->delete($contrato[0]->comprobantedomicilio);
                        }
                        $fotoBruta2 = 'Foto-comprobantedomicilio-' . $contrato[0]->id . '-' . time() . '.' . request()->file('comprobantedomicilio')->getClientOriginalExtension();
                        $comprobantedomicilio = request()->file('comprobantedomicilio')->storeAs('uploads/imagenes/contratos/comprobantedocmicilio', $fotoBruta2, 'disco');
                        $imagenesActualizadas = $imagenesActualizadas . "Comprobante de domicilio, ";

                    } else {
                        $comprobantedomicilio = $contrato[0]->comprobantedomicilio;
                    }

                    if (request()->hasFile('pagare')) {
                        if ($contrato[0]->pagare != null) {
                            //pagare es diferente de nulo
                            Storage::disk('disco')->delete($contrato[0]->pagare);
                        }
                        $fotoBruta5 = 'Foto-pagare-' . $contrato[0]->id . '-' . time() . '.' . request()->file('pagare')->getClientOriginalExtension();
                        $fotopagare = request()->file('pagare')->storeAs('uploads/imagenes/contratos/pagare', $fotoBruta5, 'disco');
                        $imagenesActualizadas = $imagenesActualizadas . "Pagare, ";

                    } else {
                        $fotopagare = $contrato[0]->pagare;
                    }

                    if (request()->hasFile('tarjetapension')) {
                        if ($contrato[0]->tarjeta != null) {
                            //tarjeta es diferente de nulo
                            Storage::disk('disco')->delete($contrato[0]->tarjeta);
                        }
                        $fotoBruta3 = 'Foto-tarjeta-' . $contrato[0]->id . '-' . time() . '.' . request()->file('tarjetapension')->getClientOriginalExtension();
                        $tarjeta = request()->file('tarjetapension')->storeAs('uploads/imagenes/contratos/tarjeta', $fotoBruta3, 'disco');
                        $imagenesActualizadas = $imagenesActualizadas . "Tarjeta pension frente, ";

                    } else {
                        $tarjeta = $contrato[0]->tarjeta;
                    }

                    if (request()->hasFile('tarjetapensionatras')) {
                        if ($contrato[0]->tarjetapensionatras != null) {
                            //tarjetapensionatras es diferente de nulo
                            Storage::disk('disco')->delete($contrato[0]->tarjetapensionatras);
                        }
                        $fotoBruta3 = 'Foto-tarjetapensionatras' . $contrato[0]->id . '-' . time() . '.' . request()->file('tarjetapensionatras')->getClientOriginalExtension();
                        $tarjetapensionatras = request()->file('tarjetapensionatras')->storeAs('uploads/imagenes/contratos/tarjetapensionatras', $fotoBruta3, 'disco');
                        $imagenesActualizadas = $imagenesActualizadas . "Tarjeta pension atras, ";

                    } else {
                        $tarjetapensionatras = $contrato[0]->tarjetapensionatras;
                    }

                    if (request()->hasFile('fotootros')) {
                        if ($contrato[0]->fotootros != null) {
                            //fotootros es diferente de nulo
                            Storage::disk('disco')->delete($contrato[0]->fotootros);
                        }
                        $fotoBruta6 = 'Foto-Otros' . $contrato[0]->id . '-' . time() . '.' . request()->file('fotootros')->getClientOriginalExtension();
                        $fotootros = request()->file('fotootros')->storeAs('uploads/imagenes/contratos/fotootros', $fotoBruta6, 'disco');
                        $imagenesActualizadas = $imagenesActualizadas . "Otros, ";

                    } else {
                        $fotootros = $contrato[0]->fotootros;
                    }

                    $imagenesActualizadas = trim($imagenesActualizadas, " ");
                    $imagenesActualizadas = trim($imagenesActualizadas, ",");

                    //Guardar movimiento en historialcontrato
                    DB::table('historialcontrato')->insert([
                        'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato,
                        'created_at' => $actualizar, 'cambios' => "Actualizo fotos: '" . $imagenesActualizadas . "'"
                    ]);

                    $camposModificados = "";
                    if($contrato[0]->observacionfotoine != request('observacionfotoine')){
                        //Actualizaron observacionfotoine
                        $camposModificados = $camposModificados . "Observación foto ine frente,";
                    }
                    if($contrato[0]->observacionfotoineatras != request('observacionfotoineatras')){
                        //Actualizaron observacionfotoineatras
                        $camposModificados = $camposModificados . "Observación foto ine atras,";
                    }
                    if($contrato[0]->observacionfotocasa != request('observacionfotocasa')){
                        //Actualizaron observacionfotocasa
                        $camposModificados = $camposModificados . "Observación foto casa,";
                    }
                    if($contrato[0]->observacioncomprobantedomicilio != request('observacioncomprobantedomicilio')){
                        //Actualizaron observacioncomprobantedomicilio
                        $camposModificados = $camposModificados . "Observación foto comprobante domicilio,";
                    }
                    if($contrato[0]->observacionpagare != request('observacionpagare')){
                        //Actualizaron observacionpagare
                        $camposModificados = $camposModificados . "Observación foto pagare,";
                    }
                    if($contrato[0]->observacionfotootros != request('observacionfotootros')){
                        //Actualizaron observacionfotootros
                        $camposModificados = $camposModificados . "Observación foto otros,";
                    }

                    $camposModificados = trim($camposModificados," ");
                    $camposModificados = trim($camposModificados,",");

                    //Guardar movimiento en historialcontrato
                    DB::table('historialcontrato')->insert([
                        'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => "Actualizo los siguientes campos del contrato: '" . $camposModificados . "'"
                    ]);

                    DB::table("contratos")->where("id", "=", $idContrato)->update([
                        "fotoine" => $fotoine,
                        "fotoineatras" => $fotoineatras,
                        "fotocasa" => $fotocasa,
                        "comprobantedomicilio" => $comprobantedomicilio,
                        "pagare" => $fotopagare,
                        "tarjeta" => $tarjeta,
                        "tarjetapensionatras" => $tarjetapensionatras,
                        "fotootros" => $fotootros,
                        'observacionfotoine' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('observacionfotoine')),
                        'observacionfotoineatras' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('observacionfotoineatras')),
                        'observacionfotocasa' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('observacionfotocasa')),
                        'observacioncomprobantedomicilio' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('observacioncomprobantedomicilio')),
                        'observacionpagare' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('observacionpagare')),
                        'observacionfotootros' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('observacionfotootros')),
                    ]);

                    return redirect()->route('estadoconfirmacion', [$idContrato])->with("bien", "Las imagenes se actualizaron correctamente.");

                }

                return back()->with("alerta", "Necesitas permisos adicionales para hacer esto.");

            }

            return back()->with("alerta","No se encontro el contrato.");

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function actualizarContratoConfirmaciones($idContrato,Request $request){
        if(Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15)))
        {
            $existeContrato = DB::select("SELECT * FROM contratos WHERE id ='$idContrato'");
            if($existeContrato != null){
                //Existe el contrato

                if($existeContrato[0]->estatus_estadocontrato == 1 || $existeContrato[0]->estatus_estadocontrato == 7 || $existeContrato[0]->estatus_estadocontrato == 9
                    || $existeContrato[0]->estatus_estadocontrato == 10) {
                    //TERMINADO, APROBADO, EN PROCESO DE APROBACION O MANOFACTURA

                    $rules = [
                        'zona' => 'required',
                        'nombre' => 'required|string|max:255',
                        'alias' => 'required|string|max:255',
                        'edad' => 'required|string|max:255',
                        'calle' => 'required|string|max:255',
                        'numero' => 'required|string|min:1|max:255',
                        'departamento' => 'required|string|max:255',
                        'alladode' => 'required|string|max:255',
                        'frentea' => 'required|string|max:255',
                        'entrecalles' => 'required|string|max:255',
                        'colonia' => 'required|string|max:255',
                        'localidad' => 'required|string|max:255',
                        'telefono' => 'required|string|size:10|regex:/[0-9]/',
                        'casatipo' => 'required|string|max:255',
                        'casacolor' => 'required|string|max:255',
                        'calleentrega' => 'required|string|max:255',
                        'numeroentrega' => 'required|string|min:1|max:255',
                        'departamentoentrega' => 'required|string|max:255',
                        'alladodeentrega' => 'required|string|max:255',
                        'frenteaentrega' => 'required|string|max:255',
                        'entrecallesentrega' => 'required|string|max:255',
                        'coloniaentrega' => 'required|string|max:255',
                        'localidadentrega' => 'required|string|max:255',
                        'casatipoentrega' => 'required|string|max:255',
                        'casacolorentrega' => 'required|string|max:255',
                        'nr' => 'required|string|max:255',
                        'tr' => 'required|string|size:10|regex:/[0-9]/',
                    ];

                    request()->validate($rules);

                    $camposModificados = "";
                    DB::table("historialclinico")->where("id_contrato", "=", $idContrato)->update([
                        'edad' => request('edad')
                    ]);

                    if(request('edad') != null){
                        $camposModificados = "Edad, ";
                    }

                    $idAsistenteActualizar = $existeContrato[0]->id_usuariocreacion;
                    $nombreAsistenteActualizar = $existeContrato[0]->nombre_usuariocreacion;
                    $idOptometristaActualizar = $existeContrato[0]->id_optometrista;
                    $idZonaActualizar = $existeContrato[0]->id_zona;
                    $seActualizoAsistente = false;

                    //ACTUALIZAR ASISTENTE Y OPTO
                    if ($existeContrato[0]->estatus_estadocontrato == 0 || $existeContrato[0]->estatus_estadocontrato == 1 || $existeContrato[0]->estatus_estadocontrato == 9) {
                        //NO TERMINADO, TERMINADO, EN PROCESO DE APROBACION
                        $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1");
                        if ($tieneHistorialGarantia == null) {
                            //No tiene garantias
                            $consultaAsistenteActualizar = DB::select("SELECT name FROM users WHERE id = '" . request('asistente') . "'");
                            if ($consultaAsistenteActualizar != null) {
                                $idAsistenteActualizar = request('asistente');
                                $nombreAsistenteActualizar = $consultaAsistenteActualizar[0]->name;
                                $idOptometristaActualizar = request('optometrista');
                                $seActualizoAsistente = true;
                            }
                        }
                    }

                    //ACTUALIZAR ZONA
                    if ($existeContrato[0]->estatus_estadocontrato == 0 || $existeContrato[0]->estatus_estadocontrato == 1 || $existeContrato[0]->estatus_estadocontrato == 9
                        || $existeContrato[0]->estatus_estadocontrato == 7) {
                        //NO TERMINADO, TERMINADO, EN PROCESO DE APROBACION Y APROBADO
                        $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1");
                        if ($tieneHistorialGarantia == null) {
                            //No tiene garantias
                            $idZonaActualizar = request('zona');
                        }
                    }

                    $contratosGlobal = new contratosGlobal;

                    DB::table("contratos")->where("id", "=", $idContrato)->update([
                        'id_zona' => $idZonaActualizar,
                        'nombre' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('nombre')),
                        'alias' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('alias')),
                        'calle' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('calle')),
                        'numero' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('numero')),
                        'depto' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('departamento')),
                        'alladode' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('alladode')),
                        'frentea' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('frentea')),
                        'entrecalles' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('entrecalles')),
                        'colonia' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('colonia')),
                        'localidad' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('localidad')),
                        'telefono' => request('telefono'),
                        'casatipo' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('casatipo')),
                        'casacolor' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('casacolor')),
                        'calleentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('calleentrega')),
                        'numeroentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('numeroentrega')),
                        'deptoentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('departamentoentrega')),
                        'alladodeentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('alladodeentrega')),
                        'frenteaentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('frenteaentrega')),
                        'entrecallesentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('entrecallesentrega')),
                        'coloniaentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('coloniaentrega')),
                        'localidadentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('localidadentrega')),
                        'casatipoentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('casatipoentrega')),
                        'casacolorentrega' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('casacolorentrega')),
                        'telefonoreferencia' => request('tr'),
                        'nombrereferencia' => $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('nr')),
                        'coordenadas' => request('coordenadas'),
                        'id_usuariocreacion' => $idAsistenteActualizar,
                        'nombre_usuariocreacion' => $nombreAsistenteActualizar,
                        'id_optometrista' => $idOptometristaActualizar
                    ]);

                    //Guardar movimiento en historialcontrato
                    $globalesServicioWeb = new globalesServicioWeb;
                    $idHistorialContratoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');
                    $usuarioId = Auth::user()->id;
                    $actualizar = Carbon::now();

                    if($existeContrato[0]->id_zona != $idZonaActualizar){
                        $camposModificados = $camposModificados . "Zona, ";
                    }
                    if($existeContrato[0]->coordenadas != request('coordenadas')){
                        $camposModificados = $camposModificados . "Coordenadas, ";
                    }
                    if($existeContrato[0]->id_usuariocreacion != $idAsistenteActualizar){
                        $camposModificados = $camposModificados . "Asistente, ";
                    }
                    if($existeContrato[0]->id_optometrista != $idOptometristaActualizar){
                        $camposModificados = $camposModificados . "Optometrista, ";
                    }
                    if($existeContrato[0]->nombre != request('nombre')){
                        $camposModificados = $camposModificados . "Nombre, ";
                    }
                    if($existeContrato[0]->alias != request('alias')){
                        $camposModificados = $camposModificados . "Alias, ";
                    }
                    if($existeContrato[0]->telefono != request('telefono')){
                        $camposModificados = $camposModificados . "Telefono (Venta), ";
                    }
                    if($existeContrato[0]->nombrereferencia != request('nr')){
                        $camposModificados = $camposModificados . "Nombre referencia (Entrega), ";
                    }
                    if($existeContrato[0]->telefonoreferencia != request('tr')){
                        $camposModificados = $camposModificados . "Telefono referencia (Entrega), ";
                    }
                    if($existeContrato[0]->calle != request('calle')){
                        $camposModificados = $camposModificados . "Calle (Venta), ";
                    }
                    if($existeContrato[0]->numero != request('numero')){
                        $camposModificados = $camposModificados . "Numero de domicilio (Venta), ";
                    }
                    if($existeContrato[0]->depto != request('departamento')){
                        $camposModificados = $camposModificados . "Departamento (Venta), ";
                    }
                    if($existeContrato[0]->alladode != request('alladode')){
                        $camposModificados = $camposModificados . "A lado de (Venta), ";
                    }
                    if($existeContrato[0]->frentea != request('frentea')){
                        $camposModificados = $camposModificados . "Frente a (Venta), ";
                    }
                    if($existeContrato[0]->entrecalles != request('entrecalles')){
                        $camposModificados = $camposModificados . "Entre calles (Venta), ";
                    }
                    if($existeContrato[0]->colonia != request('colonia')){
                        $camposModificados = $camposModificados . "Colonia (Venta), ";
                    }
                    if($existeContrato[0]->localidad != request('localidad')){
                        $camposModificados = $camposModificados . "Localidad (Venta), ";
                    }
                    if($existeContrato[0]->casatipo != request('casatipo')){
                        $camposModificados = $camposModificados . "Casa tipo (Venta), ";
                    }
                    if($existeContrato[0]->casacolor != request('casacolor')){
                        $camposModificados = $camposModificados . "Casa color (Venta), ";
                    }
                    if($existeContrato[0]->calleentrega != request('calleentrega')){
                        $camposModificados = $camposModificados . "Calle (Entrega), ";
                    }
                    if($existeContrato[0]->numeroentrega != request('numeroentrega')){
                        $camposModificados = $camposModificados . "Numero domicilio (Entrega), ";
                    }
                    if($existeContrato[0]->deptoentrega != request('departamentoentrega')){
                        $camposModificados = $camposModificados . "Departamento (Entrega), ";
                    }
                    if($existeContrato[0]->alladodeentrega != request('alladodeentrega')){
                        $camposModificados = $camposModificados . "A lado de (Entrega), ";
                    }
                    if($existeContrato[0]->frenteaentrega != request('frenteaentrega')){
                        $camposModificados = $camposModificados . "Frente a (Entrega), ";
                    }
                    if($existeContrato[0]->entrecallesentrega != request('entrecallesentrega')){
                        $camposModificados = $camposModificados . "Entre calles (Entrega), ";
                    }
                    if($existeContrato[0]->coloniaentrega != request('coloniaentrega')){
                        $camposModificados = $camposModificados . "Colonia (Entrega), ";
                    }
                    if($existeContrato[0]->localidadentrega != request('localidadentrega')){
                        $camposModificados = $camposModificados . "Localidad (Entrega), ";
                    }
                    if($existeContrato[0]->casatipoentrega != request('casatipoentrega')){
                        $camposModificados = $camposModificados . "Casa tipo (Entrega), ";
                    }
                    if($existeContrato[0]->casacolorentrega != request('casacolorentrega')){
                        $camposModificados = $camposModificados . "Casa color (Entrega), ";
                    }

                    $camposModificados = trim($camposModificados," ");
                    $camposModificados = trim($camposModificados,",");

                    DB::table('historialcontrato')->insert([
                        'id' => $idHistorialContratoAlfanumerico, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => $actualizar, 'cambios' => "Actualizo los siguientes campos del contrato: '" . $camposModificados . "'"
                    ]);

                    //Actualizar datos contrato a mayusculas y quitar acentos
                    $contratosGlobal::actualizarContratoHistorialesClinicosMayusculasAcentos($idContrato, 0);

                    //ACTUALIZAR ASISTENTE EN TABLA contratostemporalessincronizacion
                    if($seActualizoAsistente) {
                        //Se cambiara el contrato de la tabla contratostemporalessincronizacion a la asistente que se haya cambiado y se eliminara a la anterior

                        //Eliminar registro de la tabla contratostemporalessincronizacion de la asistente anterior
                        DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato' AND id_usuario = '" . $existeContrato[0]->id_usuariocreacion . "'");

                        //Insertar contrato en tabla contratostemporalessincronizacion de la asistente a la que se actualizo
                        $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $idAsistenteActualizar);

                        //Actualizar abonos que haya dado la asistente anterior
                        DB::update("UPDATE abonos
                                    SET id_usuario = '$idAsistenteActualizar'
                                    WHERE id_contrato = '$idContrato'
                                        AND id_usuario = '" . $existeContrato[0]->id_usuariocreacion . "'");

                    }

                    $idZonaActualizarAprobados = $existeContrato[0]->id_zona;
                    if($existeContrato[0]->id_zona != $idZonaActualizar) {
                        //Es por que se cambio la zona del contrato
                        $cobradoresAsignadosAZona = DB::select("SELECT u.id
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id = uf.id_usuario
                                              WHERE u.rol_id = 4 AND u.id_zona = '" . $existeContrato[0]->id_zona . "'"); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona anterior

                        if ($cobradoresAsignadosAZona != null) {
                            //Existen cobradores
                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato' AND id_usuario = '" . $cobradorAsignadoAZona->id . "'");
                            }
                        }

                        $cobradoresAsignadosAZona = DB::select("SELECT u.id
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id = uf.id_usuario
                                              WHERE u.rol_id = 4 AND u.id_zona = '" . $idZonaActualizar . "'"); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona nueva

                        if ($cobradoresAsignadosAZona != null) {
                            //Existen cobradores
                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $cobradorAsignadoAZona->id);
                            }
                        }

                        $idZonaActualizarAprobados = $idZonaActualizar;
                    }

                    //Validacion de si es garantia o no
                    $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1 ORDER BY created_at LIMIT 1");

                    switch ($existeContrato[0]->estatus_estadocontrato) {
                        case 0:
                        case 1:
                        case 9: //NO TERMINADO, TERMINADO, EN PROCESO DE APROBACION
                            //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                            if ($tieneHistorialGarantia != null) {
                                //Tiene garantia
                                $ultimaGarantiaCreada = DB::select("SELECT id_optometrista FROM garantias WHERE id_contrato = '$idContrato'
                                                                                AND id_historialgarantia = '" . $tieneHistorialGarantia[0]->id . "'
                                                                                ORDER BY created_at LIMIT 1");
                                if($ultimaGarantiaCreada != null) {
                                    $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $ultimaGarantiaCreada[0]->id_optometrista);
                                }
                            }else {
                                //No tiene garantia
                                $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $idAsistenteActualizar);
                            }
                            break;
                        case 7: //APROBADO
                            //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato (Opcional lo puse en caso de que no se haga en confirmaciones)
                            DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");

                            //Agregar contrato a cobradores si tiene garantia el contrato
                            if ($tieneHistorialGarantia != null) {
                                //Tiene garantia
                                $cobradoresAsignadosAZona = DB::select("SELECT u.id
                                              FROM users u
                                              INNER JOIN usuariosfranquicia uf
                                              ON u.id = uf.id_usuario
                                              WHERE u.rol_id = 4 AND u.id_zona = '" . $idZonaActualizarAprobados . "'"); //Obtener idsUsuarios con rol cobranza y que este asignado a la zona

                                if ($cobradoresAsignadosAZona != null) {
                                    //Existen cobradores
                                    foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                        //Recorrido cobradores
                                        $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $cobradorAsignadoAZona->id);
                                    }
                                }
                            }
                            break;
                    }

                    return back()->with("bien", "El contrato se actualizo correctamente.");

                }

                return back()->with("alerta", "Necesitas permisos adicionales para hacer esto.");

            }

            return back()->with("alerta","No se encontro el contrato.");

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }

    }

    public function actualizarTotalContratoConfirmacioness($idContrato){
        if(Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15)))
        {
            $rules = [
                'totalActualizado' => 'required|integer',
            ];

            request()->validate($rules);

            $existeContrato = DB::select("SELECT c.id,c.estatus_estadocontrato,c.promocionterminada,c.id_franquicia,c.pago,c.enganche,
                                                (SELECT (SELECT promoc.id FROM promocion promoc WHERE promoc.armazones = '1' AND promoc.id = pc.id_promocion) FROM promocioncontrato pc
                                                WHERE pc.id_contrato = c.id AND pc.estado = '1') AS promocion,
                                                c.subscripcion FROM contratos  c WHERE  c.id = '$idContrato'");
            if($existeContrato != null){
                //Existe el contrato
                if($existeContrato[0]->estatus_estadocontrato == 1 || $existeContrato[0]->estatus_estadocontrato == 9){
                    //TERMINADO/PROCESO DE APROBACION

                    if($existeContrato[0]->subscripcion == null){
                        //No esta subscripto a meses con tarjeta

                        $totalActualizado = request('totalActualizado');
                        if($totalActualizado >= 0){
                            //Total mayor o igual a 0

                            $totalAbono = DB::select("SELECT SUM(abono) as sumaabonos FROM abonos where id_contrato = '$idContrato' AND tipoabono != 7");
                            if($totalAbono != null && ($totalAbono[0]->sumaabonos <= $totalActualizado)){

                                if($existeContrato[0]->promocion != null) {
                                    //Tiene promocion y esta activa
                                    if($existeContrato[0]->promocionterminada == 1) {
                                        //Promocion terminada
                                        $id_promocion = $existeContrato[0]->promocion;
                                        $promocion = DB::select("SELECT tipopromocion, precioP, preciouno FROM promocion where id = '$id_promocion'");

                                        if($promocion != null) {
                                            //Si existe la promocion

                                            if($promocion[0]->tipopromocion == 1) {
                                                //Promocion fija
                                                $preciouno = $promocion[0]->preciouno;
                                                DB::table("contratos")->where("id","=",$idContrato)->update([
                                                    "totalhistorial" => $totalActualizado,
                                                    "totalreal" => $totalActualizado,
                                                    "totalpromocion" => $totalActualizado - $preciouno
                                                ]);
                                            }else {
                                                //Promocion por porcentaje
                                                $precioP = $promocion[0]->precioP;
                                                $totalpromocion = $totalActualizado - (($totalActualizado / 100) * $precioP);
                                                DB::table("contratos")->where("id","=",$idContrato)->update([
                                                    "totalhistorial" => $totalActualizado,
                                                    "totalreal" => $totalActualizado,
                                                    "totalpromocion" => $totalpromocion
                                                ]);
                                            }

                                        }else {
                                            //No existe la promocion
                                            return back()->with("alerta","La promocion ya no existe actualmente.");
                                        }

                                    }else {
                                        //Promocion no ha sido terminada
                                        return back()->with("alerta","No se puede actualizar el total del contrato por que la promocion aun no ha sido terminada.");
                                    }

                                }else {
                                    //No tiene promocion

                                    if($existeContrato[0]->pago == 0 && $totalAbono[0]->sumaabonos > 0) {
                                        //Forma de pago de contado y tiene abonos

                                        $descuento = 0;
                                        if($this->obtenerBanderaContadoEngancheOSinEnganche($idContrato, $existeContrato[0]->id_franquicia, 4)) {
                                            //Tiene contadoenganche

                                            if($this->obtenerBanderaContadoEngancheOSinEnganche($idContrato, $existeContrato[0]->id_franquicia, 5)) {
                                                //Tiene contadosinenganche
                                                $descuento = 300;
                                            }else {
                                                //No tiene contadosinenganche
                                                $descuento = 100;
                                            }
                                        }else {
                                            //No tiene contadoenganche

                                            if($this->obtenerBanderaContadoEngancheOSinEnganche($idContrato, $existeContrato[0]->id_franquicia, 5)) {
                                                //Tiene contadosinenganche
                                                if ($existeContrato[0]->enganche == 1) {
                                                    //Tiene activado el enganche
                                                    $descuento = 200;
                                                }else {
                                                    //No tiene activado el enganche
                                                    $descuento = 300;
                                                }
                                            }else {
                                                //No tiene contadosinenganche
                                                $descuento = 0;
                                            }
                                        }

                                        $totalrealActualizar = $totalActualizado;
                                        $totalActualizado = $totalActualizado - $descuento;

                                    }else {
                                        //Forma de pago contado y no tiene abono o semanal, quicenal o mensual

                                        $totalrealActualizar = $totalActualizado;
                                        if ($existeContrato[0]->enganche == 1) {
                                            //Tiene activado el enganche
                                            $totalActualizado = $totalActualizado - 100;
                                        }

                                    }

                                    if($totalActualizado < $totalAbono[0]->sumaabonos) {
                                        return back()->with("alerta","No se puede actualizar el total del contrato por que es menor al total de abonos.");
                                    }

                                    DB::table("contratos")->where("id","=",$idContrato)->update([
                                        "totalhistorial" => $totalActualizado,
                                        "totalreal" => $totalrealActualizar
                                    ]);

                                    //Registrar movimiento en historialcontrato
                                    $globalesServicioWeb = new globalesServicioWeb();
                                    DB::table('historialcontrato')->insert([
                                        'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => Auth::user()->id, 'id_contrato' => $idContrato,
                                        'created_at' => Carbon::now(), 'cambios' => " Se actualizo el total del contrato a '$" . $totalActualizado . "'"
                                    ]);
                                }

                                return back()->with("bien","El total se actualizo correctamente.");

                            }else{
                                return back()->with("alerta","No se puede actualizar el total del contrato por que es mayor el total de abonos.");
                            }
                        }else{
                            return back()->withErrors(['totalActualizado' => 'Ingresa cantidad para nuevo total.'])->with("alerta","Total debe ser mayor o igual a 0.");
                        }
                    }else{
                        return back()->with("alerta","El contrato tiene una subscripcion.");
                    }

                }else{
                    return back()->with("alerta","El contrato no tiene el estatus de TERMINADO/PROCESO DE APROBACION.");
                }
            }else{
                return back()->with("alerta","No se encontro el contrato.");
            }

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function obtenerBanderaContadoEngancheOSinEnganche($idContrato, $idFranquicia, $tipo) {

        $respuesta = false;

        $consultaabono = DB::select("SELECT id FROM abonos WHERE id_contrato = '$idContrato' AND tipoabono = '$tipo'");

        if ($consultaabono != null) {
            $respuesta = true;
        }

        return $respuesta;
    }

    public function listagarantiasconfirmaciones(){

        if(Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15)))        {

            //Solo los roles de principal, director y confirmaciones pueden entrar
            $filtro = request('filtro');
            $contratosGaratias = null;
            if($filtro != null){ //Tenemos un filtro?
                //Tenemos un filtro
                try{
                    if((Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 7){
                        //Es un usuario principal o administrador

                        $contratosGaratias = DB::table('contratos as c')
                            ->join('estadocontrato as ec', 'ec.estatus', '=', 'c.estatus_estadocontrato')
                            ->join('sucursalesconfirmaciones as sc', 'c.id_franquicia', '=', 'sc.id_franquicia')
                            ->select('c.id','ec.descripcion','c.nombre','c.created_at')
                            ->whereRaw("c.datos = 1")
                            ->whereRaw("c.id like '%$filtro%'")
                            ->groupBy('c.id','ec.descripcion','c.nombre','c.created_at')
                            ->orderBy('c.created_at', 'DESC')
                            ->paginate(200);
                    }else{

                        $contratosGaratias = DB::table('contratos as c')
                            ->join('estadocontrato as ec', 'ec.estatus', '=', 'c.estatus_estadocontrato')
                            ->join('sucursalesconfirmaciones as sc', 'c.id_franquicia', '=', 'sc.id_franquicia')
                            ->select('c.id','ec.descripcion','c.nombre','c.created_at')
                            ->whereRaw("sc.id_usuario = '".Auth::user()->id."'")
                            ->whereRaw("c.datos = 1")
                            ->whereRaw("c.id like '%$filtro%'")
                            ->groupBy('c.id','ec.descripcion','c.nombre','c.created_at')
                            ->orderBy('c.created_at', 'DESC')
                            ->paginate(200);
                    }
                }catch(\Exception $e){
                    \Log::info("Error".$e);
                }
            }else{
                //Sin filtro
                try{
                    if((Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 7){
                        //Es un usuario principal o administrador

                        $contratosGaratias = DB::table('contratos as c')
                            ->join('estadocontrato as ec', 'ec.estatus', '=', 'c.estatus_estadocontrato')
                            ->join('sucursalesconfirmaciones as sc', 'c.id_franquicia', '=', 'sc.id_franquicia')
                            ->select('c.id','ec.descripcion','c.nombre','c.created_at')
                            ->whereRaw("c.datos = 1")
                            ->groupBy('c.id','ec.descripcion','c.nombre','c.created_at')
                            ->orderBy('c.created_at', 'DESC')
                            ->paginate(50);
                    }else{
                        //Es un usuario de confirmaciones
                        $contratosGaratias = DB::table('contratos as c')
                            ->join('estadocontrato as ec', 'ec.estatus', '=', 'c.estatus_estadocontrato')
                            ->join('sucursalesconfirmaciones as sc', 'c.id_franquicia', '=', 'sc.id_franquicia')
                            ->select('c.id','ec.descripcion','c.nombre','c.created_at')
                            ->whereRaw("sc.id_usuario = '".Auth::user()->id."'")
                            ->whereRaw("c.datos = 1")
                            ->groupBy('c.id','ec.descripcion','c.nombre','c.created_at')
                            ->orderBy('c.created_at', 'DESC')
                            ->paginate(50);

                    }
                }catch(\Exception $e){
                    \Log::info("Error".$e);
                }
            }

            return view("administracion.confirmaciones.tablagarantias",['contratosGaratias' => $contratosGaratias]);
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function vercontratogarantiaconfirmaciones($idContrato){

        if(Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15)))        {

            $contrato = null;
            $historiales = null;

            try{

                $contrato = DB::select("SELECT c.id,c.estatus_estadocontrato,ec.descripcion,z.zona,c.banderacomentarioconfirmacion,
                                            (SELECT u.name FROM users u WHERE u.id = c.id_optometrista) as opto,
                                            (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) as zonacontrato,
                                          c.nombre,c.calle,c.numero,c.depto,c.alladode,c.frentea,c.entrecalles,c.colonia,c.localidad,c.telefono,
                                          c.casatipo,c.casacolor,c.nombrereferencia,c.telefonoreferencia,c.correo,c.fotoine,c.fotoineatras,c.fotocasa,c.comprobantedomicilio,
                                          c.tarjeta,c.tarjetapensionatras,c.pago,ec.descripcion,c.pagare,c.total,c.totalpromocion,
                                          (SELECT estado FROM promocioncontrato pc WHERE pc.id_contrato = c.id) AS promocion,
                                          (SELECT SUM(a.abono) FROM abonos a WHERE a.id_contrato = c.id)as totalabonos,c.id_zona, c.subscripcion,
                                          (SELECT p.titulo FROM promocion p WHERE p.id = c.id_promocion) as titulopromocion
                                          FROM contratos c
                                          INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                          INNER JOIN zonas z ON z.id = c.id_zona
                                          WHERE c.datos = 1
                                          AND c.id = '$idContrato'");
                if($contrato == null){
                    return back()->with("alerta","No se encontro el contrato.");
                }
                $franquicia = DB::select("SELECT id_franquicia FROM contratos WHERE id = '$idContrato'");
                $idFranquicia =  $franquicia[0]->id_franquicia;
                $historiales =  DB::select("SELECT hc.id,hc.esfericoder,hc.cilindroder,hc.ejeder,hc.addder,hc.altder,hc.esfericoizq,hc.cilindroizq,hc.ejeizq,hc.addizq,hc.altizq,
                                                    (SELECT nombre FROM producto p WHERE p.id = hc.id_producto) as armazon,
                                                    hc.material,hc.materialotro,hc.bifocal,hc.fotocromatico,hc.ar,hc.tinte,hc.blueray,hc.otroT,hc.tratamientootro,hc.observaciones,
                                                    hc.observacionesinterno, (SELECT p.nombre FROM paquetes p WHERE p.id = hc.id_paquete AND p.id_franquicia = '$idFranquicia' LIMIT 1) as paquete
                                                    FROM historialclinico hc WHERE id_contrato = '$idContrato'");

                $infoFranquicia = DB::select("SELECT estado,ciudad,colonia,numero FROM franquicias WHERE id = '".$franquicia[0]->id_franquicia."'");

            }catch(\Exception $e){
                \Log::info("Error".$e);
            }

            return view("administracion.confirmaciones.contratogarantia",["contrato" => $contrato, "infoFranquicia" => $infoFranquicia,
                'idContrato'=>$idContrato,'historiales'=> $historiales]);
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function rechazarContratoConfirmaciones($idContrato)
    {
        $comentarios = request('comentarios');

        if (strlen($comentarios) == 0) {
            //Comentarios vacio
            return back()->with('alerta', 'Campo especificaciónes obligatorio');
        }

        $contrato = DB::select("SELECT estatus_estadocontrato, promocionterminada FROM contratos WHERE id = '$idContrato'");

        if ($contrato != null) {

            $existeGarantia = DB::select("SELECT * FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia = '2' ORDER BY created_at ASC limit 1");

            if($existeGarantia == null){
                //No tiene garantia el contrato
                if(Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15))) {

                    if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) {
                        //Estado del contrato es TERMIANDO o EN PROCESO DE APROBACION

                        if ($this->obtenerEstadoPromocion($idContrato) && $contrato[0]->promocionterminada == 0) {
                            //Tiene promocion y promocion no ha sido terminada
                            return redirect()->route('estadoconfirmacion', [$idContrato])->with('alerta', 'No se puede rechazar el contrato por que tiene una promocion sin terminar');
                        }

                        $actualizar = Carbon::now();
                        $usuarioId = Auth::user()->id;

                        $globalesServicioWeb = new globalesServicioWeb;
                        $idHistorialContratoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');

                        try {

                            DB::table('contratos')->where('id', '=', $idContrato)->update([
                                'estatus_estadocontrato' => 8, 'estatusanteriorcontrato' => $contrato[0]->estatus_estadocontrato,
                                'fecharechazadoconfirmaciones' => Carbon::now()
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
                                'id' => $idHistorialContratoAlfanumerico, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato,
                                'created_at' => $actualizar, 'cambios' => "Contrato rechazado por confirmaciones con la siguiente descripción: '$comentarios'"
                            ]);

                            //Reducir calidad a imagenes del contrato
                            $this->reducirCalidadImagenesContrato($idContrato);

                            //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato
                            DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");

                            //Eliminamos de la tabla contratoslaboratorio
                            $contratosGlobal = new contratosGlobal();
                            $contratosGlobal::actualizarRegistroTablaContratosLaboratorio($idContrato, "ELIMINAR");

                            //Actualizar datos en tabla contratoslistatemporales
                            $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                            return redirect()->route('listaconfirmaciones')->with('bien', 'El contrato se rechazo correctamente.');

                        } catch (\Exception $e) {
                            \Log::info("Error: " . $e->getMessage());
                            return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
                        }

                    }else{
                        return back()->with('alerta','Necesitas permisos adicionales para hacer esto.');
                    }

                } else {
                    if (Auth::check()) {
                        return redirect()->route('redireccionar');
                    } else {
                        return redirect()->route('login');
                    }
                }
            }else{
                //Si tiene garantia -> no se puede rechazar el contrato
                return back()->with('alerta', 'No se puedes rechazar el contrato debido a que tiene garantia.');
            }
        }
        return back()->with('alerta', 'No se encontro el contrato.');
    }

    private function obtenerEstadoPromocion($idContrato)
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

    public function cancelarGarantiaHistorialConfirmaciones($idContrato, $idHistorial, Request $request)
    {
        if (Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15))) {

            try {

                //Validacion de campo de mensaje
                $validacion = Validator::make($request->all(),[
                    'mensaje'=>'required|string|min:15|max:1000'
                ]);

                if($validacion->fails()){
                    return back()->with('alerta','El mensaje para cancelación de garantía debe contener como minimo 15 caracteres y un maximo de 1000.');
                }

                $datosHistorial = DB::select("SELECT id_contrato FROM historialclinico WHERE id = '$idHistorial' AND id_contrato = '$idContrato'");

                if ($datosHistorial != null) {
                    $idContrato = $datosHistorial[0]->id_contrato;

                    $contrato = DB::select("SELECT estatus_estadocontrato FROM contratos WHERE id = '$idContrato'");

                    if ($contrato != null) {
                        //Existe el contrato

                        if ($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9) {
                            //TERMINADO, APROBADO O EN PROCESO DE APROBACION

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
                                        'created_at' => Carbon::now(), 'cambios' => "Cancelo la garantia al historial '$idhistorial' con el siguiente mensaje: '" . $request->input('mensaje') . "'"
                                    ]);

                                    //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato
                                    DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");

                                    //Insertar o actualizar contrato en tabla contratostemporalessincronizacion (Pondre mi id_usuario (Sergio) para que salgan bien las cosas)
                                    $contratosGlobal = new contratosGlobal;
                                    $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, '61');

                                }
                                //Eliminamos de la tabla contratoslaboratorio
                                $contratosGlobal = new contratosGlobal();
                                $contratosGlobal::actualizarRegistroTablaContratosLaboratorio($idContrato, "ELIMINAR");

                                //Actualizar contrato en tabla contratoslistatemporales
                                $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                                return redirect()->route('listaconfirmaciones')->with('bien', 'Se cancelo correctamente la garantia.');
                            }

                            //No tiene garantias para cancelar
                            return redirect()->route('listaconfirmaciones')->with('alerta', 'No se puede cancelar la garantia por que no tiene asignada.');

                        }

                        return back()->with("alerta", "Necesitas permisos adicionales para hacer esto.");

                    }

                    return back()->with("alerta","No se encontro el contrato.");

                } else {
                    //No presenta hitorial clinico
                    return back()->with("alerta","No se encontro el historial clinico del contrato");
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

    public function listaconfirmacioneslaboratrio(Request $request){

        if(Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15)))        {

            //Solo los roles de Administracion, principal, director y confirmaciones pueden entrar

            $filtro = $request->input('filtro');
            $cbAprobados = $request->input('cbAprobados');
            $cbManofactura = $request->input('cbManofactura');
            $cbEnviados = $request->input('cbEnviados');
            $cbComentarios = $request->input('cbComentarios');

            $contratosScomentarios = null;
            $contratosConComentarios = null;

            $estado_contrato = "";

            if($cbAprobados == "true" && $cbManofactura == "true" && $cbEnviados == "true"){
                $estado_contrato = "7,10,11";
            }if(($cbAprobados == "true") && ($cbManofactura == "true") && ($cbEnviados == "false")){
                $estado_contrato = "7,10";
            }if(($cbAprobados == "true") && ($cbManofactura == "false") && ($cbEnviados == "false")){
                $estado_contrato = "7";
            }if(($cbAprobados == "false") && ($cbManofactura == "true") && ($cbEnviados == "false")) {
                $estado_contrato = "10";
            }if(($cbAprobados == "false") && ($cbManofactura == "true") && ($cbEnviados == "true")){
                $estado_contrato = "10,11";
            } if(($cbAprobados == "false") && ($cbManofactura == "false") && ($cbEnviados == "true")){
                $estado_contrato = "11";
            }if(($cbAprobados == "true") && ($cbManofactura == "false") && ($cbEnviados == "true")){
                $estado_contrato = "7,11";
            }if(($cbAprobados == "false") && ($cbManofactura == "false") && ($cbEnviados == "true")){
                $estado_contrato = "11";
            }

            if($filtro != null){ //Tenemos un filtro?
                //Tenemos un filtro
                try{
                    if((Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 7){
                        //Es un usuario principal o administrador
                        if($cbComentarios == "true"){
                            //Esta seleccionado con Comentarios
                            $contratosConComentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.created_at,
                                                                                u.name, us.name as usuariocreacion, f.ciudad as sucursal FROM contratos c
                                                                   INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                   INNER JOIN usuariosfranquicia uf ON c.id_franquicia = uf.id_franquicia
                                                                   INNER JOIN users u ON c.id_optometrista = u.id
                                                                   INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                                   INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                   WHERE c.estatus_estadocontrato IN ($estado_contrato)
                                                                   AND (c.id like '%$filtro%' or us.name like '%$filtro%' or c.nombre like '%$filtro%' or c.telefono like '%$filtro%'
                                                                        or c.nombrereferencia like '%$filtro%' or c.telefonoreferencia like '%$filtro%')
                                                                   AND c.banderacomentarioconfirmacion = 3
                                                                   GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.created_at,c.id_optometrista,u.name,
                                                                            us.name, f.ciudad
                                                                   ORDER BY c.estatus_estadocontrato DESC");
                        } else{
                            $contratosScomentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.created_at,
                                                                              u.name, us.name as usuariocreacion, f.ciudad as sucursal FROM contratos c
                                                                   INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                   INNER JOIN usuariosfranquicia uf ON c.id_franquicia = uf.id_franquicia
                                                                   INNER JOIN users u ON c.id_optometrista = u.id
                                                                   INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                                   INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                   WHERE c.estatus_estadocontrato IN ($estado_contrato)
                                                                   AND (c.id like '%$filtro%' or us.name like '%$filtro%' or c.nombre like '%$filtro%' or c.telefono like '%$filtro%'
                                                                        or c.nombrereferencia like '%$filtro%' or c.telefonoreferencia like '%$filtro%')
                                                                   AND c.banderacomentarioconfirmacion != 3
                                                                   GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.created_at,c.id_optometrista,u.name,
                                                                            us.name, f.ciudad
                                                                   ORDER BY c.estatus_estadocontrato DESC");
                        }
                    }else{
                        if($cbComentarios == "true"){
                            $contratosConComentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.created_at,
                                                                                u.name, us.name as usuariocreacion, f.ciudad as sucursal FROM contratos c
                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                INNER JOIN sucursalesconfirmaciones sc ON c.id_franquicia = sc.id_franquicia
                                                INNER JOIN users u ON c.id_optometrista = u.id
                                                INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                WHERE sc.id_usuario = '".Auth::user()->id."'
                                                AND c.estatus_estadocontrato IN ($estado_contrato)
                                                AND (c.id like '%$filtro%' or us.name like '%$filtro%' or c.nombre like '%$filtro%' or c.telefono like '%$filtro%'
                                                     or c.nombrereferencia like '%$filtro%' or c.telefonoreferencia like '%$filtro%')
                                                AND c.banderacomentarioconfirmacion = 3
                                                GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.created_at,c.id_optometrista,u.name, us.name, f.ciudad
                                                ORDER BY c.estatus_estadocontrato DESC");
                        } else{
                            $contratosScomentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.created_at,
                                                                              u.name, us.name as usuariocreacion, f.ciudad as sucursal   FROM contratos c
                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                INNER JOIN sucursalesconfirmaciones sc ON c.id_franquicia = sc.id_franquicia
                                                INNER JOIN users u ON c.id_optometrista = u.id
                                                INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                WHERE sc.id_usuario = '".Auth::user()->id."'
                                                AND c.estatus_estadocontrato IN ($estado_contrato)
                                                AND (c.id like '%$filtro%' or us.name like '%$filtro%' or c.nombre like '%$filtro%' or c.telefono like '%$filtro%'
                                                     or c.nombrereferencia like '%$filtro%' or c.telefonoreferencia like '%$filtro%')
                                                AND c.banderacomentarioconfirmacion != 3
                                                GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.created_at,c.id_optometrista,u.name, us.name, f.ciudad
                                                ORDER BY c.estatus_estadocontrato DESC");
                        }
                    }
                }catch(\Exception $e){
                    \Log::info("Error".$e);
                }
            }else{
                //Sin filtro
                try{
                    if((Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 7){
                        //Es un usuario principal o administrador

                        if($cbComentarios == "true"){
                            $contratosConComentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.created_at,
                                                                                u.name, us.name as usuariocreacion, f.ciudad as sucursal FROM contratos c
                                                                   INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                   INNER JOIN usuariosfranquicia uf ON c.id_franquicia = uf.id_franquicia
                                                                   INNER JOIN users u ON c.id_optometrista = u.id
                                                                   INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                                   INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                   WHERE c.estatus_estadocontrato IN ($estado_contrato)
                                                                   AND c.banderacomentarioconfirmacion = 3
                                                                   GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.created_at,c.id_optometrista,u.name,
                                                                            us.name, f.ciudad
                                                                   ORDER BY c.estatus_estadocontrato DESC");
                        } else {
                            $contratosScomentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.created_at,
                                                                              u.name, us.name as usuariocreacion, f.ciudad as sucursal FROM contratos c
                                                                   INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                   INNER JOIN usuariosfranquicia uf ON c.id_franquicia = uf.id_franquicia
                                                                   INNER JOIN users u ON c.id_optometrista = u.id
                                                                   INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                                   INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                   WHERE c.estatus_estadocontrato IN ($estado_contrato)
                                                                   AND c.banderacomentarioconfirmacion != 3
                                                                   GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.created_at,c.id_optometrista,u.name,
                                                                            us.name, f.ciudad
                                                                   ORDER BY c.estatus_estadocontrato DESC");

                        }
                    }else{
                        //Es un usuario de confirmaciones

                        if($cbComentarios == "true"){
                            $contratosConComentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.created_at,
                                                                                u.name, us.name as usuariocreacion, f.ciudad as sucursal FROM contratos c
                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                INNER JOIN sucursalesconfirmaciones sc ON c.id_franquicia = sc.id_franquicia
                                                INNER JOIN users u ON c.id_optometrista = u.id
                                                INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                WHERE sc.id_usuario = '".Auth::user()->id."'
                                                AND c.estatus_estadocontrato IN ($estado_contrato)
                                                AND c.banderacomentarioconfirmacion = 3
                                                GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.created_at,c.id_optometrista,u.name, us.name, f.ciudad
                                                ORDER BY c.estatus_estadocontrato DESC");
                        } else {
                            $contratosScomentarios = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, c.created_at,
                                                                              u.name, us.name as usuariocreacion, f.ciudad as sucursal FROM contratos c
                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                INNER JOIN sucursalesconfirmaciones sc ON c.id_franquicia = sc.id_franquicia
                                                INNER JOIN users u ON c.id_optometrista = u.id
                                                INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                WHERE sc.id_usuario = '".Auth::user()->id."'
                                                AND c.estatus_estadocontrato IN ($estado_contrato)
                                                AND c.banderacomentarioconfirmacion != 3
                                                GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.created_at,c.id_optometrista,u.name, us.name, f.ciudad
                                                ORDER BY c.estatus_estadocontrato DESC");
                        }
                    }
                }catch(\Exception $e){
                    \Log::info("Error".$e);
                }
            }

            $view = view('administracion.confirmaciones.confirmacioneslaboratorio.tablalaboratorio', [
                'contratosScomentarios'=>$contratosScomentarios,
                'contratosConComentarios'=>$contratosConComentarios]) -> render();

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
        return \Response::json(array("valid"=>"true","view"=>$view));
    }

    public function agregarproductoconfirmaciones($idContrato) {

        $producto = request('producto');

        if (strlen($producto) > 0) {
            //producto diferente de vacio

            try {

                $contrato = DB::select("SELECT estatus_estadocontrato, id_franquicia, id_usuariocreacion, id_zona FROM contratos WHERE id = '$idContrato'");

                if($contrato != null) {
                    $estadoContrato = $contrato[0]->estatus_estadocontrato;

                    if($estadoContrato == 1 || $estadoContrato == 9) {
                        //TERMINADO O EN PROCESO DE APROBACION
                        $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1");

                        if($tieneHistorialGarantia != null) {
                            //Tiene historiales con garantia
                            return back()->with("alerta","No se puede agregar producto por que es una garantia.");
                        }

                        $globalesServicioWeb = new globalesServicioWeb;

                        $idFranquicia = $contrato[0]->id_franquicia;
                        $id_usuariocreacion = $contrato[0]->id_usuariocreacion;

                        $datosproducto = DB::select("SELECT * FROM producto WHERE id_franquicia = '$idFranquicia' AND id = '$producto'");

                        if($datosproducto != null) {
                            //Existe el producto

                            $precio = $datosproducto[0]->precio;
                            $nombre = $datosproducto[0]->nombre;
                            $preciop = $datosproducto[0]->preciop;
                            $piezasactualizar = $datosproducto[0]->piezas - 1;
                            if ($preciop == null) {
                                $precioproducto = $precio * 1;
                            } else {
                                $precioproducto = $preciop * 1;
                            }

                            $idcontratoproducto = $globalesServicioWeb::generarIdAlfanumerico('contratoproducto', '5');
                            //Agregar producto al contrato
                            DB::table('contratoproducto')->insert([
                                'id' => $idcontratoproducto,
                                'id_franquicia' => $idFranquicia,
                                'id_contrato' => $idContrato,
                                'id_usuario' => $id_usuariocreacion,
                                'id_producto' => $producto,
                                'piezas' => 1,
                                'total' => $precioproducto,
                                'created_at' => Carbon::now()
                            ]);

                            //Descontar pieza al producto
                            DB::table('producto')->where([['id', '=', $producto], ['id_franquicia', '=', $idFranquicia]])->update([
                                'piezas' => $piezasactualizar
                            ]);

                            //Guardar en historial de movimientos el producto
                            DB::table('historialcontrato')->insert([
                                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                                'id_usuarioC' => Auth::user()->id,
                                'id_contrato' => $idContrato,
                                'created_at' => Carbon::now(),
                                'cambios' => " Se agrego el producto: '$producto'-'$nombre' cantidad de piezas: '1' con total de: $'$precioproducto'"
                            ]);

                            $idAbono = $globalesServicioWeb::generarIdAlfanumerico('abonos', '5');
                            //Agregar abono al contrato
                            DB::table('abonos')->insert([
                                'id' => $idAbono,
                                'folio' => null,
                                'id_franquicia' => $idFranquicia,
                                'id_contrato' => $idContrato,
                                'id_usuario' => $id_usuariocreacion,
                                'tipoabono' => 7,
                                'abono' => $precioproducto,
                                'metodopago' => 0,
                                'adelantos' => 0,
                                'corte' => 2,
                                'id_contratoproducto' => $idcontratoproducto,
                                "id_zona" => $contrato[0]->id_zona,
                                'created_at' => Carbon::now()
                            ]);

                            $contratosGlobal = new contratosGlobal;
                            $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($contrato[0]->id_zona);

                            foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                //Recorrido cobradores
                                //Insertar en tabla abonoscontratostemporalessincronizacion
                                DB::table("abonoscontratostemporalessincronizacion")->insert([
                                    "id_usuariocobrador" => $cobradorAsignadoAZona->id,
                                    "id" => $idAbono,
                                    "folio" => null,
                                    "id_contrato" => $idContrato,
                                    "id_usuario" => $id_usuariocreacion,
                                    "abono" => $precioproducto,
                                    "adelantos" => 0,
                                    "tipoabono" => 7,
                                    "metodopago" => 0,
                                    "corte" => 2,
                                    "created_at" => Carbon::now()
                                ]);
                            }

                            //Guardar en historial de movimientos el abono
                            DB::table('historialcontrato')->insert([
                                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                                'id_usuarioC' => Auth::user()->id,
                                'id_contrato' => $idContrato,
                                'created_at' => Carbon::now(),
                                'cambios' => " Se agrego el abono : '$precioproducto'"
                            ]);

                            return back()->with("bien", "Se agrego correctamente el producto.");

                        }

                        return back()->with("alerta","El producto no existe.");

                    }

                    return back()->with("alerta","Solo se puede agregar producto con estatus TERMINADO/EN PROCESO DE APROBACION.");

                }

                return back()->with("alerta","El contrato no existe.");

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }

        }

        return back()->with("alerta","Seleccionar el producto a agregar.");

    }

    public function calculoTotal($idContrato, $idFranquicia)
    {

        $this->actualizarTotalProductoContrato($idContrato, $idFranquicia);
        $this->actualizarTotalAbonoContrato($idContrato, $idFranquicia);

        if ($this->obtenerEstadoPromocion($idContrato)) {
            //Tiene promocion y esta activa
            $promocionterminada = DB::select("SELECT promocionterminada FROM contratos where id = '$idContrato'");
            if ($promocionterminada != null) {
                if ($promocionterminada[0]->promocionterminada == 1) {
                    //Promocion ha sido terminada
                    DB::update("UPDATE contratos
                        SET total = coalesce(totalpromocion,0)  + coalesce(totalproducto,0) - coalesce(totalabono,0)
                        WHERE idcontratorelacion = '$idContrato' OR id ='$idContrato'");
                } else {
                    //Promocion no ha sido terminada
                    DB::update("UPDATE contratos
                    SET total = coalesce(totalhistorial,0) + coalesce(totalproducto,0) - coalesce(totalabono,0)
                    WHERE idcontratorelacion = '$idContrato' OR id ='$idContrato'");
                }
            }
        } else {
            //No tiene promocion o existe la promocion pero esta desactivada
            DB::update("UPDATE contratos
                    SET total = coalesce(totalhistorial,0) + coalesce(totalproducto,0) - coalesce(totalabono,0)
                    WHERE idcontratorelacion = '$idContrato' OR id ='$idContrato'");
        }
    }

    private function actualizarTotalProductoContrato($idContrato, $idFranquicia)
    {
        $totalproductos = DB::select("SELECT coalesce(SUM(cp.total), 0) as totalproductos FROM contratoproducto cp WHERE cp.id_contrato = '$idContrato'");
        DB::update("UPDATE contratos c
                    SET c.totalproducto = '" . $totalproductos[0]->totalproductos . "'
                    WHERE c.id = '$idContrato' AND c.id_franquicia ='$idFranquicia'");
    }

    private function actualizarTotalAbonoContrato($idContrato, $idFranquicia)
    {
        DB::update("UPDATE contratos c
                    SET c.totalabono = coalesce((SELECT SUM(a.abono) FROM abonos a WHERE a.id_contrato = c.id), 0)
                    WHERE c.id = '$idContrato'");
    }

    public function listaconfirmacionesgarantiasprincipal(){

        if(Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15)))        {

            //Solo los roles de Administracion, principal, director y confirmaciones pueden entrar

            try{

                if((Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 7){
                    //Es un usuario principal o director

                    $contratosGarantias = DB::select("SELECT g.id_contrato AS id_contrato,
                                                                c.created_at AS fechacreacioncontrato,
                                                                c.estatus_estadocontrato AS estatus_estadocontrato,
                                                                c.nombre_usuariocreacion AS nombre_usuariocreacion,
                                                                (SELECT u.name FROM users u WHERE u.id = c.id_optometrista) AS nombreoptometrista,
                                                                (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as descripcion,
                                                                (SELECT ga.created_at FROM garantias ga WHERE ga.id_contrato = g.id_contrato
                                                                    AND ga.estadogarantia IN (0,1) ORDER BY created_at DESC LIMIT 1) as fechacreaciongarantia,
                                                                (SELECT ga.estadogarantia FROM garantias ga WHERE ga.id_contrato = g.id_contrato
                                                                    AND ga.estadogarantia IN (0,1) ORDER BY created_at DESC LIMIT 1) as estadogarantia,
                                                                f.ciudad as sucursal
                                                                FROM garantias g INNER JOIN contratos c
                                                                INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                WHERE g.id_contrato = c.id
                                                                AND c.id_franquicia != '00000'
                                                                AND g.estadogarantia IN (0,1)
                                                                AND c.estatus_estadocontrato IN (2,4,5,12)
                                                                GROUP BY g.id_contrato, c.created_at, c.estatus_estadocontrato, c.nombre_usuariocreacion, c.id_optometrista, f.ciudad");

                }else{
                    //Es un usuario de confirmaciones

                    $contratosGarantias = DB::select("SELECT g.id_contrato AS id_contrato,
                                                                c.created_at AS fechacreacioncontrato,
                                                                c.estatus_estadocontrato AS estatus_estadocontrato,
                                                                c.nombre_usuariocreacion AS nombre_usuariocreacion,
                                                                (SELECT u.name FROM users u WHERE u.id = c.id_optometrista) AS nombreoptometrista,
                                                                (SELECT ec.descripcion FROM estadocontrato ec WHERE ec.estatus = c.estatus_estadocontrato) as descripcion,
                                                                (SELECT ga.created_at FROM garantias ga WHERE ga.id_contrato = g.id_contrato
                                                                    AND ga.estadogarantia IN (0,1) ORDER BY created_at DESC LIMIT 1) as fechacreaciongarantia,
                                                                (SELECT ga.estadogarantia FROM garantias ga WHERE ga.id_contrato = g.id_contrato
                                                                    AND ga.estadogarantia IN (0,1) ORDER BY created_at DESC LIMIT 1) as estadogarantia,
                                                                f.ciudad as sucursal
                                                                FROM garantias g INNER JOIN contratos c
                                                                INNER JOIN sucursalesconfirmaciones sc ON c.id_franquicia = sc.id_franquicia
                                                                INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                WHERE g.id_contrato = c.id
                                                                AND sc.id_usuario = '" . Auth::user()->id . "'
                                                                AND g.estadogarantia IN (0,1)
                                                                AND c.estatus_estadocontrato IN (2,4,5,12)
                                                                GROUP BY g.id_contrato, c.created_at, c.estatus_estadocontrato, c.nombre_usuariocreacion, c.id_optometrista, f.ciudad");

                }

            }catch(\Exception $e){
                \Log::info("Error".$e);
            }

            $view = view('administracion.confirmaciones.confirmacioneslaboratorio.tablagarantiasvistaprincipal', [
                'contratosGarantias'=>$contratosGarantias])->render();

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
        return \Response::json(array("valid"=>"true","view"=>$view));
    }

    //Funcion: actualizardiagnosticoconfirmaciones
    //Descripcion: Permite actualizar el diagnostico para un contrato y actualiza los historiales
    public function actualizardiagnosticoconfirmaciones($idContrato){
        if (Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15))) {

            $contratosGlobal = new contratosGlobal;

            $edad = request('edad');
            $diagnostico = $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('diagnostico'));
            $ocupacion = $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('ocupacion'));
            $diabetes = $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('diabetes'));
            $hipertension = $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('hipertension'));
            $embarazada = $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('embarazada'));
            $durmioseisochohoras = $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('durmioseisochohoras'));
            $actividaddia = $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('actividaddia'));
            $problemasojos = $contratosGlobal::limpiarCadenaCaracteresEspeciales(request('problemasojos'));
            $dolor = request('dolor');
            $golpeojos = request('golpeojos');
            $ardor = request('ardor');
            $otroM = request('otroM');
            $molestiaotro = request('molestiaotro');
            $ultimoexamen = request('ultimoexamen');

            //Valores checkBox
            $dolor = ($dolor != null)? $dolor = 1: $dolor = 0;
            $ardor = ($ardor != null)? $ardor = 1: $ardor = 0;
            $golpeojos = ($golpeojos != null)? $golpeojos = 1: $golpeojos = 0;
            $otroM = ($otroM != null)? $otroM = 1: $otroM = 0;

            //Si se selecciono Otra Molestia
            if($otroM == 1){
                //Si se selecciona es obligatorio llenar la opcion de otro
                $rules = [
                    'molestiaotro' => 'required|string'
                ];
                request()->validate($rules);
            }else{
                //Si no esta seleccionado -> vaciar el campo de otra molestia para evitar datos erroneos
                $molestiaotro = null;
            }

            $existeContrato = DB::select("SELECT * FROM contratos WHERE id ='$idContrato'");

            if($existeContrato != null){
                //Existe el contrato

                if($existeContrato[0]->estatus_estadocontrato == 1 || $existeContrato[0]->estatus_estadocontrato == 7 || $existeContrato[0]->estatus_estadocontrato == 9
                    || $existeContrato[0]->estatus_estadocontrato == 10) {
                    //TERMINADO, APROBADO, EN PROCESO DE APROBACION O MANOFACTURA

                    $existeDiagnostico = DB::select("SELECT * FROM historialclinico hc WHERE hc.id_contrato = '$idContrato'");

                    if($existeDiagnostico != null){
                        //Si tiene diagnostico

                        try {

                            //Actualizar diagnosticos del contrato
                            DB::table("historialclinico")
                                ->where("id_contrato", "=", $idContrato)
                                ->update([
                                    'edad' => $edad,
                                    'diagnostico' => $diagnostico,
                                    'ocupacion' => $ocupacion,
                                    'diabetes' => $diabetes,
                                    'hipertension' => $hipertension,
                                    'dolor' => $dolor,
                                    'ardor' => $ardor,
                                    'golpeojos' => $golpeojos,
                                    'otroM' => $otroM,
                                    'molestiaotro' => $molestiaotro,
                                    'ultimoexamen' => $ultimoexamen,
                                    'embarazada' => $embarazada,
                                    'durmioseisochohoras' => $durmioseisochohoras,
                                    'actividaddia' => $actividaddia,
                                    'problemasojos' => $problemasojos,
                                    'updated_at' => Carbon::now()
                                ]);

                            //Registrar el movimiento al contrato
                            $globalesServicioWeb = new globalesServicioWeb;
                            $usuarioId = Auth::user()->id;

                            //Registrar movimiento
                            $camposActualizados = "";
                            if($existeDiagnostico[0]->edad != $edad){
                                //Actualizo edad
                                $camposActualizados = "Edad, ";
                            }
                            if($existeDiagnostico[0]->diagnostico != $diagnostico){
                                //Actualizo diagnostico
                                $camposActualizados = $camposActualizados . "Diagnostico, ";
                            }
                            if($existeDiagnostico[0]->ocupacion != $ocupacion){
                                //Actualizo Ocupacion
                                $camposActualizados = $camposActualizados . "Ocupación, ";
                            }
                            if($existeDiagnostico[0]->diabetes != $diabetes){
                                //Actualizo diabetes
                                $camposActualizados = $camposActualizados . "Diabetes, ";
                            }
                            if($existeDiagnostico[0]->hipertension != $hipertension){
                                //Actualizo hipertencion
                                $camposActualizados = $camposActualizados . "Hipertención, ";
                            }
                            if($existeDiagnostico[0]->embarazada != $embarazada){
                                //Actualizo embarazada
                                $camposActualizados = $camposActualizados . "Embarazo, ";
                            }
                            if($existeDiagnostico[0]->durmioseisochohoras != $durmioseisochohoras){
                                //Actualizo horas de sueño
                                $camposActualizados = $camposActualizados . "Horas de sueño, ";
                            }
                            if($existeDiagnostico[0]->actividaddia != $actividaddia){
                                //Actualizo actividad del dia
                                $camposActualizados = $camposActualizados . "Principal actividad del dia, ";
                            }
                            if($existeDiagnostico[0]->problemasojos != $problemasojos){
                                //Actualizo actividad del dia
                                $camposActualizados = $camposActualizados . "Principal problema, ";
                            }
                            if($existeDiagnostico[0]->dolor != $dolor){
                                //Actualizo dolor de cabeza
                                if(str_contains($camposActualizados, "Molestia")){
                                    $camposActualizados = $camposActualizados . "Dolor de cabeza, ";
                                }else{
                                    $camposActualizados = $camposActualizados . "Molestias: Dolor de cabeza, ";
                                }
                            }
                            if($existeDiagnostico[0]->ardor != $ardor){
                                //Actualizo ardor
                                if(str_contains($camposActualizados, "Molestia")){
                                    $camposActualizados = $camposActualizados . "Ardor en los ojos, ";
                                }else{
                                    $camposActualizados = $camposActualizados . "Molestias: Ardor en los ojos, ";
                                }
                            }
                            if($existeDiagnostico[0]->golpeojos != $golpeojos){
                                //Actualizo golpe
                                if(str_contains($camposActualizados, "Molestia")){
                                    $camposActualizados = $camposActualizados . "Golpe en los ojos, ";
                                }else{
                                    $camposActualizados = $camposActualizados . "Molestias: Golpe en los ojos, ";
                                }
                            }
                            if($existeDiagnostico[0]->otroM != $otroM){
                                //Actualizo dolor de cabeza
                                if(str_contains($camposActualizados, "Molestia")){
                                    $camposActualizados = $camposActualizados . "Otro ";
                                    if(strlen($molestiaotro) > 0){
                                        $camposActualizados = $camposActualizados . "(" . $molestiaotro . ")";
                                    }
                                }else{
                                    $camposActualizados = $camposActualizados . "Molestias: Otro ";
                                    if(strlen($molestiaotro) > 0){
                                        $camposActualizados = $camposActualizados . "(" . $molestiaotro . ")";
                                    }
                                }
                            }

                            $camposActualizados = trim($camposActualizados, " ");
                            $camposActualizados = trim($camposActualizados, ",");

                            if($camposActualizados != ""){
                                DB::table('historialcontrato')->insert([
                                    'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $usuarioId,
                                    'id_contrato' => $idContrato, 'created_at' =>  Carbon::now(), 'cambios' => "Actualizo los siguientes campos del diagnostico: '" . $camposActualizados ."'"
                                ]);
                            }

                            //Actualizar datos historiales clinicos a mayusculas y quitar acentos
                            $contratosGlobal::actualizarContratoHistorialesClinicosMayusculasAcentos($idContrato, 1);

                            return back()->with("bien"," Diagnostico actualizado correctamente.");

                        } catch (\Exception $e) {
                            \Log::info("Error: " . $e->getMessage());
                            return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
                        }

                    }
                    //No presenta ningun diagnostico el contrato
                    return back()->with("alerta"," Diagnostico no encontrado para el contrato.");
                }
                //Pertenece a otro estatus
                return back()->with("alerta"," No se puede actualizar el diagnostico debido al estatus del contrato.");
            }
            //No existe contrato
            return back()->with("alerta","No existe el contrato.");

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarformapagoconfirmaciones($idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 15)) {
            $rules = [
                'formapago' => 'required|string',
            ];
            if (request('formapago') != 0 && request('formapago') != 1 && request('formapago') != 2 && request('formapago') != 4) {
                return back()->withErrors(['formapago' => 'Elegir una forma de pago correcta']);
            }
            if (request('formapago') == 'nada') {
                return back()->withErrors(['formapago' => 'Elegir una forma de pago']);
            }

            request()->validate($rules);

            $idFranquicia = $request->input('idFranquiciaContrato');

            $contrato = DB::select("SELECT c.id_franquicia, c.estatus_estadocontrato, c.fechacobroini, c.fechacobrofin, c.pago, c.diapago, c.total,
                                            (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia
                                            FROM contratos c WHERE c.id_franquicia = '$idFranquicia' AND c.id = '$idContrato'");

            if($contrato != null) {
                //Existe contrato

                if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9) {
                    //El contrado es una venta nueva, en estatus TERMINADO, EN PROCESO DE APROBACION, APROBADOS

                    $formaPagoActualizar = request('formapago');

                    if($contrato[0]->pago != $formaPagoActualizar) {
                        //La forma de pago es diferente a la actual
                        if($formaPagoActualizar != 0){
                            //La forma de pago es diferente a de contado
                            if($contrato[0]->total > 0){
                                //Total es diferente de 0

                                $fechaCobroIniActualizar = null;
                                $fechaCobroFinActualizar = null;
                                $fechaDiaSeleccionadoActualizar = null;

                                $contratosGlobal  = new contratosGlobal();
                                $abonoMinimo = $contratosGlobal::calculoCantidadFormaDePago($contrato[0]->id_franquicia,$formaPagoActualizar);

                                //Actualizar forma de pago en contratos
                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'pago' => $formaPagoActualizar,
                                    'abonominimo' => $abonoMinimo,
                                    'fechacobroini' => $fechaCobroIniActualizar,
                                    'fechacobrofin' => $fechaCobroFinActualizar,
                                    'diaseleccionado' => $fechaDiaSeleccionadoActualizar,
                                    'updated_at' => Carbon::now()
                                ]);

                                //Guardar en tabla historialcontrato
                                $globalesServicioWeb = new globalesServicioWeb;
                                $usuarioId = Auth::user()->id;
                                $formaPagoTexto = null;

                                if ($formaPagoActualizar == 1) {
                                    $formaPagoTexto = 'Semanal';
                                } elseif ($formaPagoActualizar == 2) {
                                    $formaPagoTexto = 'Quincenal';
                                }elseif ($formaPagoActualizar == 4) {
                                    $formaPagoTexto = 'Mensual';
                                }

                                DB::table('historialcontrato')->insert([
                                    'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $usuarioId,
                                    'id_contrato' => $idContrato, 'created_at' => Carbon::now(), 'cambios' => "Se actualizo la forma de pago '$formaPagoTexto'"
                                ]);

                                return back()->with('bien', 'La forma de pago se actualizó correctamente.');
                            }
                            //El total del contrato es 0
                            return back()->with('alerta', 'No se puede cambiar la forma de pago al contrato.');

                        }
                        //Se intenta cambiar a forma de contado
                        return back()->with('alerta', 'No se puede cambiar de forma de pago de contado.');

                    }
                    //Forma de pago sigue siendo la misma
                    return back()->with('alerta', 'Se esta cambiando a la misma forma de pago.');

                }else
                    //No cumplio con las validaciones
                    return back()->with('alerta', 'No se puede cambiar la forma de pago al contrato.');

            }
            //No existe el contrato
            return back()->with('alerta', 'El contrato no existe.');

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function restablecercontratoconfirmaciones($idContrato)
    {
        if (Auth::check() && (((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15))) {
            //Rol director, principal o confirmaciones

            try {

                $contrato = DB::select("SELECT c.estatus_estadocontrato as estatus_estadocontrato, c.id_usuariocreacion as id_usuariocreacion
                                                    FROM contratos c WHERE c.id = '$idContrato'");

                if ($contrato != null) {
                    //Existe el contrato

                    $estadocontrato = $contrato[0]->estatus_estadocontrato;
                    $id_usuariocreacion = $contrato[0]->id_usuariocreacion;

                    if ($estadocontrato == 8) {
                        //Estado rechazado

                        $garantia = DB::select("SELECT id, indice FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia = '2' ORDER BY created_at ASC limit 1");

                        if($garantia == null) {
                            //No se tiene garantia

                            $estadoactualizar = 9; //Se pondra siempre en estado EN PROCESO DE APROBACION

                            //Actualizar estado
                            DB::table("contratos")->where("id", "=", $idContrato)->update([
                                'estatus_estadocontrato' => $estadoactualizar
                            ]);

                            //Insertar en tabla registroestadocontrato
                            DB::table('registroestadocontrato')->insert([
                                'id_contrato' => $idContrato,
                                'estatuscontrato' => $estadoactualizar,
                                'created_at' => Carbon::now()
                            ]);

                            //Guardar en tabla historialcontrato
                            $usuarioId = Auth::user()->id;
                            $globalesServicioWeb = new globalesServicioWeb;
                            $idHistorialContratoAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');
                            DB::table('historialcontrato')->insert([
                                'id' => $idHistorialContratoAlfanumerico, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                'cambios' => "Se restauro el estatus del contrato de 'rechazado'"
                            ]);

                            //Agregarle el contrato a asistente/optometrista en la tabla contratostemporalessincronizacion
                            $contratosGlobal = new contratosGlobal;
                            $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $id_usuariocreacion);

                            //Actualizar datos en tabla contratoslistatemporales
                            $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                            return back()->with("bien", "El estatus del contrato se actualizo correctamente");

                        }
                        return back()->with('alerta', 'No se puede restablecer el contrato, se tiene garantia.');

                    }
                    return back()->with('alerta', 'No se puede restablecer el contrato, debe estar en estado de RECHAZADO.');

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

    public function actualizarpaquetehistorialconfirmaciones($idContrato, $idHistorial)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 15)) {

            $paquetehistorialeditar = request('paquetehistorialeditarconfirmaciones'.$idHistorial);

            if ($paquetehistorialeditar == '') {
                return back()->with('alerta', 'No se selecciono ningun paquete a actualizar');
            }

            try {

                $datosHistorial = DB::select("SELECT id_contrato, tipo, id_paquete FROM historialclinico WHERE id = '$idHistorial' AND id_contrato = '$idContrato'");

                if ($datosHistorial != null) {
                    //Existe historial

                    $tipoHistorial = $datosHistorial[0]->tipo;
                    $idpaquetehistorial = $datosHistorial[0]->id_paquete;

                    if($idpaquetehistorial == $paquetehistorialeditar) {
                        //Se quiere actualizar al mismo paquete
                        return back()->with('alerta', 'No se puede actualizar al mismo paquete');
                    }

                    if($tipoHistorial == 0) {
                        //Es tipo 0 (Historial no garantia)

                        $idContrato = $datosHistorial[0]->id_contrato;

                        $contrato = DB::select("SELECT c.estatus_estadocontrato as estatus_estadocontrato,
                                                        (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia
                                                            FROM contratos c WHERE c.id = '$idContrato'");

                        if($contrato != null) {
                            //Existe contrato

                            $estadocontrato = $contrato[0]->estatus_estadocontrato;

                            if($estadocontrato == 1 || $estadocontrato == 9) {
                                //estadocontrato TERMINADO o EN PROCESO DE APROBACION

                                $estadogarantia = $contrato[0]->estadogarantia;

                                if($estadogarantia == null) {
                                    //No ha tenido ninguna garantia el contrato

                                    $globalesServicioWeb = new globalesServicioWeb;
                                    $contratosGlobal = new contratosGlobal;

                                    $historiales = DB::select("SELECT * FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = '0' ORDER BY created_at DESC");

                                    if($historiales != null) {

                                        $contador = 0;
                                        $idpaquetehistorialentrante = null;

                                        foreach ($historiales as $historial) {

                                            $idhistorialentrante = $historial->id;
                                            $idpaquetehistorialentrante = $historial->id_paquete;
                                            $esfericoderhistorialentrante = $historial->esfericoder;
                                            $cilindroderhistorialentrante = $historial->cilindroder;
                                            $ejederhistorialentrante = $historial->ejeder;
                                            $addderhistorialentrante = $historial->addder;
                                            $altderhistorialentrante = $historial->altder;
                                            $esfericoizqhistorialentrante = $historial->esfericoizq;
                                            $cilindroizqhistorialentrante = $historial->cilindroizq;
                                            $ejeizqhistorialentrante = $historial->ejeizq;
                                            $addizqhistorialentrante = $historial->addizq;
                                            $altizqhistorialentrante = $historial->altizq;

                                            $esfericoderactualizar = null;
                                            $cilindroderactualizar = null;
                                            $ejederactualizar = null;
                                            $addderactualizar = null;
                                            $altderactualizar = null;
                                            $esfericoizqactualizar = null;
                                            $cilindroizqactualizar = null;
                                            $ejeizqactualizar = null;
                                            $addizqactualizar = null;
                                            $altizqactualizar = null;
                                            $tipohistorialactualizar = 0;
                                            $crearsegundohistorial = false;
                                            $crearsegundohistorialsinconversion = false;

                                            if($contador == 0 && $idpaquetehistorialentrante == 6) {
                                                $tipohistorialactualizar = 2;
                                                DB::update("UPDATE garantias SET estadogarantia = '4' WHERE id_contrato = '$idContrato'
                                                                    AND id_historial = '$idhistorialentrante' AND estadogarantia IN (0,1)");
                                            }

                                            if($paquetehistorialeditar == 6) {
                                                //Se va a editar a DORADO2
                                                $crearsegundohistorial = true;
                                                if($idpaquetehistorialentrante == 1) {
                                                    //Paquete entrante es LECTURA
                                                    $crearsegundohistorialsinconversion = true;
                                                }
                                            }

                                            if($paquetehistorialeditar != 2) {
                                                //LECTURA, ECO JR, JR, DORADO 2, DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)

                                                if(strlen($esfericoderhistorialentrante) > 0) {
                                                    //LECTURA, ECO JR, JR, DORADO 2, DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                    if($paquetehistorialeditar == 1 || $paquetehistorialeditar == 3 || $paquetehistorialeditar == 4 || $paquetehistorialeditar == 6) {
                                                        //LECTURA, ECO JR, JR, DORADO 2, DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                        $esfericoderactualizar = $esfericoderhistorialentrante;
                                                    }elseif ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                        // (PAQUETE ACTUALIZAR)
                                                        $esfericoderactualizar = $esfericoderhistorialentrante;
                                                    }
                                                }else {
                                                    //PROTECCION (PAQUETE ACTUAL)
                                                    $esfericoderactualizar = 0;
                                                }

                                                if(strlen($cilindroderhistorialentrante) > 0) {
                                                    //LECTURA, ECO JR, JR, DORADO 2, DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                    if($paquetehistorialeditar == 1 || $paquetehistorialeditar == 3 || $paquetehistorialeditar == 4 || $paquetehistorialeditar == 6) {
                                                        //LECTURA, ECO JR, JR, DORADO 2 (PAQUETE ACTUALIZAR)
                                                        $cilindroderactualizar = $cilindroderhistorialentrante;
                                                    }elseif ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                        //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                        $cilindroderactualizar = $cilindroderhistorialentrante;
                                                    }
                                                }else {
                                                    //PROTECCION (PAQUETE ACTUAL)
                                                    $cilindroderactualizar = 0;
                                                }

                                                if(strlen($ejederhistorialentrante) > 0) {
                                                    //LECTURA, ECO JR, JR, DORADO 2, DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                    if($paquetehistorialeditar == 1 || $paquetehistorialeditar == 3 || $paquetehistorialeditar == 4 || $paquetehistorialeditar == 6) {
                                                        //LECTURA, ECO JR, JR, DORADO 2 (PAQUETE ACTUALIZAR)
                                                        $ejederactualizar = $ejederhistorialentrante;
                                                    }elseif ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                        //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                        $ejederactualizar = $ejederhistorialentrante;
                                                    }
                                                }else {
                                                    //PROTECCION (PAQUETE ACTUAL)
                                                    $ejederactualizar = 0;
                                                }

                                                if(strlen($addderhistorialentrante) > 0) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                    if ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                        //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                        $addderactualizar = $addderhistorialentrante;
                                                    }
                                                }else {
                                                    //LECTURA, ECO JR, JR, DORADO 2 o PROTECCION (PAQUETE ACTUAL)
                                                    if ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                        //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                        $addderactualizar = 0;
                                                    }
                                                }

                                                if(strlen($altderhistorialentrante) > 0) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                    if ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                        //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                        $altderactualizar = $altderhistorialentrante;
                                                    }
                                                }else {
                                                    //LECTURA, ECO JR, JR, DORADO 2 o PROTECCION (PAQUETE ACTUAL)
                                                    if ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                        //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                        $altderactualizar = 0;
                                                    }
                                                }

                                                if(strlen($esfericoizqhistorialentrante) > 0) {
                                                    //LECTURA, ECO JR, JR, DORADO 2, DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                    if($paquetehistorialeditar == 1 || $paquetehistorialeditar == 3 || $paquetehistorialeditar == 4 || $paquetehistorialeditar == 6) {
                                                        //LECTURA, ECO JR, JR, DORADO 2 (PAQUETE ACTUALIZAR)
                                                        $esfericoizqactualizar = $esfericoizqhistorialentrante;
                                                    }elseif ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                        //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                        $esfericoizqactualizar = $esfericoizqhistorialentrante;
                                                    }
                                                }else {
                                                    //PROTECCION (PAQUETE ACTUAL)
                                                    $esfericoizqactualizar = 0;
                                                }

                                                if(strlen($cilindroizqhistorialentrante) > 0) {
                                                    //LECTURA, ECO JR, JR, DORADO 2, DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                    if($paquetehistorialeditar == 1 || $paquetehistorialeditar == 3 || $paquetehistorialeditar == 4 || $paquetehistorialeditar == 6) {
                                                        //LECTURA, ECO JR, JR, DORADO 2 (PAQUETE ACTUALIZAR)
                                                        $cilindroizqactualizar = $cilindroizqhistorialentrante;
                                                    }elseif ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                        //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                        $cilindroizqactualizar = $cilindroizqhistorialentrante;
                                                    }
                                                }else {
                                                    //PROTECCION (PAQUETE ACTUAL)
                                                    $cilindroizqactualizar = 0;
                                                }

                                                if(strlen($ejeizqhistorialentrante) > 0) {
                                                    //LECTURA, ECO JR, JR, DORADO 2, DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                    if($paquetehistorialeditar == 1 || $paquetehistorialeditar == 3 || $paquetehistorialeditar == 4 || $paquetehistorialeditar == 6) {
                                                        //LECTURA, ECO JR, JR, DORADO 2 (PAQUETE ACTUALIZAR)
                                                        $ejeizqactualizar = $ejeizqhistorialentrante;
                                                    }elseif ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                        //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                        $ejeizqactualizar = $ejeizqhistorialentrante;
                                                    }
                                                }else {
                                                    //PROTECCION (PAQUETE ACTUAL)
                                                    $ejeizqactualizar = 0;
                                                }

                                                if(strlen($addizqhistorialentrante) > 0) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                    if ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                        //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                        $addizqactualizar = $addizqhistorialentrante;
                                                    }
                                                }else {
                                                    //LECTURA, ECO JR, JR, DORADO 2 o PROTECCION (PAQUETE ACTUAL)
                                                    if ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                        //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                        $addizqactualizar = 0;
                                                    }
                                                }

                                                if(strlen($altizqhistorialentrante) > 0) {
                                                    //DORADO 1 o PLATINO (PAQUETE ACTUAL)
                                                    if ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                        //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                        $altizqactualizar = $altizqhistorialentrante;
                                                    }
                                                }else {
                                                    //LECTURA, ECO JR, JR, DORADO 2 o PROTECCION (PAQUETE ACTUAL)
                                                    if ($paquetehistorialeditar == 5 || $paquetehistorialeditar == 7) {
                                                        //DORADO 1 o PLATINO (PAQUETE ACTUALIZAR)
                                                        $altizqactualizar = 0;
                                                    }
                                                }

                                            }

                                            if($crearsegundohistorial) {

                                                $idhistorialnuevo = $globalesServicioWeb::generarIdAlfanumerico('historialclinico', '5');

                                                DB::table('historialclinico')->insert([
                                                    'id' => $idhistorialnuevo,
                                                    'id_contrato' => $idContrato,
                                                    'edad' => $historial->edad,
                                                    'fechaentrega' => $historial->fechaentrega,
                                                    'diagnostico' => $historial->diagnostico,
                                                    'ocupacion' => $historial->ocupacion,
                                                    'diabetes' => $historial->diabetes,
                                                    'hipertension' => $historial->hipertension,
                                                    'dolor' => $historial->dolor,
                                                    'ardor' => $historial->ardor,
                                                    'golpeojos' => $historial->golpeojos,
                                                    'otroM' => $historial->otroM,
                                                    'molestiaotro' => $historial->molestiaotro,
                                                    'ultimoexamen' => $historial->ultimoexamen,
                                                    'esfericoder' => $esfericoderactualizar,
                                                    'cilindroder' => $cilindroderactualizar,
                                                    'ejeder' => $ejederactualizar,
                                                    'addder' => $addderactualizar,
                                                    'altder' => $altderactualizar,
                                                    'esfericoizq' => $esfericoizqactualizar,
                                                    'cilindroizq' => $cilindroizqactualizar,
                                                    'ejeizq' => $ejeizqactualizar,
                                                    'addizq' => $addizqactualizar,
                                                    'altizq' => $altizqactualizar,
                                                    'id_producto' => '00000',
                                                    'id_paquete' => $paquetehistorialeditar,
                                                    'material' => $historial->material,
                                                    'materialotro' => $historial->materialotro,
                                                    'costomaterial' => $historial->costomaterial,
                                                    'bifocal' => $historial->bifocal,
                                                    'fotocromatico' => $historial->fotocromatico,
                                                    'ar' => $historial->ar,
                                                    'tinte' => $historial->tinte,
                                                    'blueray' => $historial->blueray,
                                                    'otroT' => $historial->otroT,
                                                    'tratamientootro' => $historial->tratamientootro,
                                                    'costotratamiento' => $historial->costotratamiento,
                                                    'observaciones' => $historial->observaciones,
                                                    'observacionesinterno' => $historial->observacionesinterno,
                                                    'tipo' => $tipohistorialactualizar,
                                                    'created_at' => Carbon::now()
                                                ]);

                                                if($crearsegundohistorialsinconversion) {

                                                    $historialsinconversionentrante = DB::select("SELECT * FROM historialsinconversion WHERE id_contrato = '$idContrato'
                                                                                                            AND id_historial = '$idhistorialentrante' ORDER BY created_at DESC LIMIT 1");

                                                    if ($historialsinconversionentrante != null) {

                                                        DB::table('historialsinconversion')->insert([
                                                            'id_contrato' => $idContrato,
                                                            'id_historial' => $idhistorialnuevo,
                                                            'esfericoder' => $historialsinconversionentrante[0]->esfericoder,
                                                            'cilindroder' => $historialsinconversionentrante[0]->cilindroder,
                                                            'ejeder' => $historialsinconversionentrante[0]->ejeder,
                                                            'addder' => $historialsinconversionentrante[0]->addder,
                                                            'esfericoizq' => $historialsinconversionentrante[0]->esfericoizq,
                                                            'cilindroizq' => $historialsinconversionentrante[0]->cilindroizq,
                                                            'ejeizq' => $historialsinconversionentrante[0]->ejeizq,
                                                            'addizq' => $historialsinconversionentrante[0]->addizq,
                                                            'created_at' => Carbon::now()
                                                        ]);

                                                    }

                                                }

                                            }

                                            DB::table('historialclinico')->where([['id', '=', $idhistorialentrante], ['id_contrato', '=', $idContrato]])->update([
                                                'id_paquete' => $paquetehistorialeditar,
                                                'esfericoder' => $esfericoderactualizar,
                                                'cilindroder' => $cilindroderactualizar,
                                                'ejeder' => $ejederactualizar,
                                                'addder' => $addderactualizar,
                                                'altder' => $altderactualizar,
                                                'esfericoizq' => $esfericoizqactualizar,
                                                'cilindroizq' => $cilindroizqactualizar,
                                                'ejeizq' => $ejeizqactualizar,
                                                'addizq' => $addizqactualizar,
                                                'altizq' => $altizqactualizar,
                                                'tipo' => $tipohistorialactualizar
                                            ]);

                                            $contador++;

                                        }

                                        $nombrePaqueteEntrante = $contratosGlobal::obtenerNombrePaquete($idpaquetehistorialentrante);
                                        $nombrePaqueteActualizar = $contratosGlobal::obtenerNombrePaquete($paquetehistorialeditar);

                                        //Guardar en tabla historialcontrato
                                        $usuarioId = Auth::user()->id;
                                        DB::table('historialcontrato')->insert([
                                            'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                                            'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                            'cambios' => "Se actualizo correctamente el paquete de " ."'". $nombrePaqueteEntrante ."'". " a " . "'" .$nombrePaqueteActualizar. "'"
                                        ]);

                                        return back()->with('bien', 'Se actualizo correctamente el paquete.');

                                    }

                                }
                                return back()->with('alerta', 'No se puede cambiar el paquete (Se tiene garantia).');
                            }
                            return back()->with('alerta', 'No se puede cambiar el paquete (Verificar el estado del contrato).');
                        }
                        //No existe contrato
                        return back()->with('alerta', 'No se encontro el contrato.');
                    }
                    //Es tipo 1 (Historial de garantia)
                    return back()->with('alerta', 'El historial es de garantia');

                }
                //No existe el historial
                return back()->with('alerta', 'No existe el historial');

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

    public function agregarnotaconfirmaciones($idContrato)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15)) {

            request()->validate([
                'nota' => 'nullable|string|max:255'
            ]);

            $nota = request("nota");
            if ($nota == null || strlen($nota) == 0) {
                $nota = "";
            }

            DB::table("contratos")->where("id", "=", $idContrato)->update([
                "nota" => $nota
            ]);

            //Registrar movimiento
            $globalesServicioWeb = new globalesServicioWeb;
            DB::table('historialcontrato')->insert([
                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => Auth::user()->id, 'id_contrato' => $idContrato,
                'created_at' => Carbon::now(), 'cambios' => "Actualizo nota del contrato."
            ]);

            return back()->with("bien", "La nota se actualizo correctamente.");

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function editarHistorialArmazonConfirmaciones($idContrato, $idHistorial)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 15) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8)) {
            //Acesso solo para rol de: CONFIRMACIONES - DIRECTOR - PRINCIPAL

            $producto = request('producto');

            $existeArmazon = DB::select("SELECT * FROM producto p WHERE p.id = '$producto' LIMIT 1");

            if($existeArmazon != null){
                //Si existe el armazon

                $datosHistorial = DB::select("SELECT id_contrato, id, id_producto FROM historialclinico WHERE id = '$idHistorial' AND id_contrato = '$idContrato'");
                $idcontrato = $datosHistorial[0]->id_contrato;

                $contrato = DB::select("SELECT estatus_estadocontrato FROM contratos WHERE id = '$idcontrato'");
                if ($contrato != null) {
                    //Validar el estatus actual del contrato

                    $garantia = DB::select("SELECT id, indice FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia = '2' ORDER BY created_at ASC limit 1");

                    if (($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) && $garantia == null) {
                        //Pertenece a estatus TERMINADO o PROCESO DE APROBACION y no tiene garantia

                        $usuarioId = Auth::user()->id;
                        $actualizar = Carbon::now();
                        $globalesServicioWeb = new globalesServicioWeb;
                        $randomId2 = $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5');

                        try {

                            //Obtener producto actual del historial
                            $idProductoActual = $datosHistorial[0]->id_producto;
                            $armazonActual = DB::select("SELECT * FROM producto WHERE id = '$idProductoActual' AND id_tipoproducto = '1'");
                            $idArmazonActual = $armazonActual[0]->id;
                            $piezasArmazonActualAumentado = $armazonActual[0]->piezas + 1;

                            //Sumarle una pieza al producto que se quito
                            DB::table('producto')->where('id', '=', $idArmazonActual)->update([
                                'piezas' => $piezasArmazonActualAumentado
                            ]);

                            //Obtener producto a actualizar
                            $idArmazonActualizar = request('producto');
                            $armazonActualizar = DB::select("SELECT * FROM producto WHERE id = '$idArmazonActualizar' AND id_tipoproducto = '1'");
                            $piezasArmazonActualizarDecrementado = $armazonActualizar[0]->piezas - 1;

                            //Restarle una pieza al producto que se actualizo
                            DB::table('producto')->where('id', '=', $idArmazonActualizar)->update([
                                'piezas' => $piezasArmazonActualizarDecrementado
                            ]);

                            //Guardar movimiento
                            DB::table('historialcontrato')->insert([
                                'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $idcontrato, 'created_at' => $actualizar,
                                'cambios' => " Se modifico el historial clinico: '$idHistorial', Se cambio el armazon"
                            ]);

                            //Guardar id_producto en historialclinico
                            DB::table('historialclinico')->where([['id', '=', $idHistorial], ['id_contrato', '=', $idcontrato]])->update([
                                'id_producto' => $idArmazonActualizar
                            ]);
                            return back()-> with("bien", "El historial clinico se actualizo correctamente.");

                        } catch (\Exception $e) {
                            \Log::info("Error: " . $e->getMessage());
                            return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
                        }

                    }else{
                        //No pertenece al estatus de TERMINADO o PROCESO DE APROVACION
                        return back()->with('alerta', 'No puedes cambiar el modelo del armazon en este momento.');
                    }
                }else{
                    //NO existe el contrato
                    return back()->with('alerta', 'No se encontro el contrato');
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

    public function solicitarautorizacionabonominimoconfirmaciones($idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 15)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL - CONFIRMACIONES
            $idUsuario = Auth::user()->id;

            $mensaje = $request->input("mensaje");

            //Validaciones de campo mensaje
            request()->validate([
                'mensaje' => 'required|string|min:1'
            ]);

            $existeContrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato'");

            if ($existeContrato != null) {
                //Si existe el contrato

                if ($existeContrato[0]->estatus_estadocontrato == 1 || $existeContrato[0]->estatus_estadocontrato == 7 || $existeContrato[0]->estatus_estadocontrato == 9 ) {
                    //El contrato tiene estatus TERMINADO, APROBADO, PROCESO DE APROBACION

                    try {
                        //Insertamos valores de peticion y movimiento

                        //Insertar solicitud de autorizacion
                        DB::table('autorizaciones')->insert([
                            'id_contrato' => $idContrato, 'id_usuarioC' => $idUsuario, 'id_franquicia' => $existeContrato[0]->id_franquicia,
                            'fechacreacioncontrato' => $existeContrato[0]->created_at,
                            'estadocontrato' => $existeContrato[0]->estatus_estadocontrato,
                            'mensaje' => "Solicitó autorizacion con el siguiente mensaje: '$mensaje'",
                            'estatus' => '0', 'tipo' => '7', 'created_at' => Carbon::now()
                        ]);

                        return back()->with('bien', 'Solicitud de garantia generada correctamente');

                    } catch (Exception $e) {
                        \Log::info("Error: " . $e->getMessage());
                        return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina   Error:' . $e->getMessage());
                    }
                } else {
                    return back()->with('alerta', 'No puedes acceder al contrato debido a su estatus actual.');
                }
            } else {
                return back()->with('alerta', 'Contrato no existente, verifica el ID CONTRATO.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function agregarabonominimoconfirmaciones($idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 15)) {
            //ROL DE DIRECTOR - ADMINISTRADOR - GENERAL - CONFIRMACIONES
            $idUsuario = Auth::user()->id;

            $abonoMinimo = $request->input("abonoMinimo");

            //Validaciones de campo abono minimo
            if($abonoMinimo < 150){
                return back()->with('alerta', " El abono minimo debe ser de '150' pesos");
            }

            request()->validate([
                'abonoMinimo' => 'required|integer'
            ]);

            $existeContrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato'");

            if ($existeContrato != null) {
                //Si existe el contrato

                if ($existeContrato[0]->estatus_estadocontrato == 1 || $existeContrato[0]->estatus_estadocontrato == 7 || $existeContrato[0]->estatus_estadocontrato == 9) {
                    //El contrato tiene estatus TERMINADO, APROBADO, PROCESO DE APROBACION

                    try {
                        //Actualizamos abono minimo en contratos y contratos temporales sincronizacion
                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $existeContrato[0]->id_franquicia]])
                            ->update([
                                'abonominimo' => $abonoMinimo
                            ]);

                        $contratosGlobal = new contratosGlobal;
                        $contratosGlobal::actualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato);

                        //Insertar registro en tabla abonominimocontratos
                        $contratosGlobal::insertarActualizarTablaAbonoMinimoContratos($idContrato, $existeContrato[0]->id_zona, $abonoMinimo);

                        //Cambiar estatus de solicitud a geenrado
                        $solicitudAbono = DB::select("SELECT * FROM autorizaciones a WHERE a.id_contrato = '$idContrato' AND a.tipo = 7 AND a.estatus = 1 ORDER BY a.created_at DESC LIMIT 1");

                        DB::table('autorizaciones')->where([['indice', '=', $solicitudAbono[0]->indice], ['id_contrato', '=', $idContrato]])->update([
                            'estatus' => '3', 'updated_at' => Carbon::now()
                        ]);

                        //Registrar movimiento
                        $globalesServicioWeb = new globalesServicioWeb;
                        DB::table('historialcontrato')->insert([
                            'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => Auth::user()->id, 'id_contrato' => $idContrato,
                            'created_at' => Carbon::now(), 'cambios' => "Actualizo abono minimo a '$" . $abonoMinimo . "'"
                        ]);

                        return back()->with('bien', 'Abono minimo actualizado correctamente.');

                    } catch (Exception $e) {
                        \Log::info("Error: " . $e->getMessage());
                        return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina   Error:' . $e->getMessage());
                    }
                } else {
                    return back()->with('alerta', 'No puedes acceder al contrato debido a su estatus actual.');
                }
            } else {
                return back()->with('alerta', 'Contrato no existente, verifica el ID CONTRATO.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizaresperapolizacontrato($idContrato) {

        $esperapoliza = request("esperapoliza");

        $esperapoliza = $esperapoliza == null ? 0 : 1;
        $mensaje = $esperapoliza== null ? 'desactivo' : 'activo';

        try {

            $contrato = DB::select("SELECT estatus_estadocontrato, poliza FROM contratos WHERE id = '$idContrato'");
            $tieneHistorialGarantia = DB::select("SELECT id FROM historialclinico WHERE id_contrato = '$idContrato' AND tipo = 1");

            if ($contrato != null) {
                //Existe contrato
                if (($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9)
                    && $tieneHistorialGarantia == null && $contrato[0]->poliza == null) {
                    //Estado del contrato es TERMINADO o EN PROCESO DE APROBACION, no tiene historiales con garantias y no tiene aun la poliza (Venta nueva)

                    //Actualizar esperapoliza contrato
                    DB::table("contratos")->where("id", "=", $idContrato)->update([
                        "esperapoliza" => $esperapoliza
                    ]);

                    //Registrar el movimiento al contrato
                    $globalesServicioWeb = new globalesServicioWeb;
                    DB::table('historialcontrato')->insert([
                        'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => Auth::user()->id,
                        'id_contrato' => $idContrato, 'created_at' => Carbon::now(), 'cambios' => "$mensaje la espera del contrato"
                    ]);

                    return back()->with('bien', "Se $mensaje la espera del contrato correctamente.");

                }
                //Tiene historiales con garantia
                return back()->with('alerta', 'No se puede actualizar la espera por que el contrato cuenta con garantía.');
            }
            //No existe contrato
            return back()->with('alerta', 'El contrato no existe.');

        } catch (Exception $e) {
            \Log::info("Error: " . $e->getMessage());
            return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina   Error:' . $e->getMessage());
        }
    }

    public function agregarhistorialmovimientocontratoconfirmaciones($idContrato)
    {
        if (Auth::check() && ((Auth::user()->rol_id == 7) || (Auth::user()->rol_id) == 15)) {
            //Rol director, confirmaciones

            $rules = [
                'movimiento' => 'required|string'
            ];

            request()->validate($rules);

            try {
                $contrato = DB::select("SELECT c.id FROM contratos c WHERE c.id = '$idContrato'");

                if ($contrato != null) {
                    //Existe el contrato
                    $movimiento = request('movimiento');

                    if (strlen($movimiento) == 0) {
                        return back()->with('alerta', "Favor de agregar el mensaje de movimiento");
                    }

                    //Guardar en tabla historialcontrato
                    $globalesServicioWeb = new globalesServicioWeb;
                    $usuarioId = Auth::user()->id;
                    DB::table('historialcontrato')->insert([
                        'id' =>$globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                        'cambios' => "Agrego el movimiento: " . $movimiento, 'tipomensaje' => '7'
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

    public function eliminarhistorialclinicoconfirmaciones($idContrato, $idHistorial, $indiceHistorial){
        if (Auth::check() && ((Auth::user()->rol_id == 7) || (Auth::user()->rol_id) == 15)) {
            //Rol director, confirmaciones

            try {
                $contrato = DB::select("SELECT c.id, c.estatus_estadocontrato, (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia
                                              FROM contratos c WHERE c.id = '$idContrato'");

                if ($contrato != null) {
                    //Existe el contrato

                    if($contrato[0]->estadogarantia == null){
                        //Venta nueva
                        if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9){
                            //Estado contrato TERMINADO - PROCESO DE APROBACION

                            //Eliminar historial
                            DB::delete("DELETE FROM historialclinico WHERE indice = '$indiceHistorial' AND id = '$idHistorial' AND id_contrato = '$idContrato'");

                            //Guardar en tabla historialcontrato
                            $globalesServicioWeb = new globalesServicioWeb;
                            $usuarioId = Auth::user()->id;
                            DB::table('historialcontrato')->insert([
                                'id' =>$globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                                'id_usuarioC' => $usuarioId,
                                'id_contrato' => $idContrato,
                                'created_at' => Carbon::now(),
                                'cambios' => "Elimino historial clinico con identificador: '" . $idHistorial . "'"
                            ]);

                            return back()->with('bien', "Historial clinico eliminado correctamente.");

                        }else{
                            return back()->with('alerta', 'El historial clínico no puede ser eliminado debido al estado actual del contrato.');
                        }

                    }else{
                        return back()->with('alerta', 'El historial clínico no puede ser eliminado debido a que el contrato presenta una garantía.');
                    }

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

    public function agregarpromocionconfirmaciones($idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15) || ((Auth::user()->rol_id) == 6)) {

            //Varidad promocion seleccionada valida
            $rules = [
                'promocion' => 'required|integer',
            ];
            if (request('promocion') == 0) {
                return back()->withErrors(['promocion' => 'Elegir una promoción'])->withInput($request->all());
            }

            request()->validate($rules);

            //Verificar que exista contrato
            $contrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato'");

            if($contrato != null){
                //Existe contrato - Tiene ya una promoción?
                if($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9){
                    //Contrato en estado No Terminado, Terminado o proceso de aprobacion

                    $promocionExistente = DB::select("SELECT * FROM promocioncontrato pc WHERE pc.id_contrato = '$idContrato' AND pc.estado = 1");
                    if($promocionExistente == null){
                        //No tienen ninguna promocion asignada ni activada
                        $abonospromo = DB::select("SELECT * FROM abonos a WHERE a.id_contrato = '$idContrato' AND a.tipoabono IN (1,4,5)");
                        if ($abonospromo != null) {
                            return back()->with('alerta', 'No se puede agregar la promocion porque existe un abono de enganche');

                        }else{
                            $randomId = $this->getPromocionContratoId();
                            $promo = request('promocion');
                            $creacion = Carbon::now();
                            $idFranquicia = $contrato[0]->id_franquicia;
                            $globalesServicioWeb = new globalesServicioWeb;
                            $usuarioId = Auth::user()->id;

                            $existePromocion = DB::select("SELECT * FROM promocion p WHERE p.id = '$promo' AND p.id_franquicia = '$idFranquicia' AND p.status = 1");

                            if($existePromocion != null){
                                //Existe la promocion

                                if($existePromocion[0]->tipo != 2){

                                    //Promocion es diferente a empleado
                                    DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                        'id_promocion' => $promo, 'promocionterminada' => '1', 'estatus' => '1'
                                    ]);

                                    DB::table('promocion')->where([['id', '=', $promo], ['id_franquicia', '=', $idFranquicia]])->update([
                                        'asignado' => 1
                                    ]);

                                    DB::table('promocioncontrato')->insert([
                                        'id' => $randomId, 'id_contrato' => $idContrato, 'id_franquicia' => $idFranquicia, 'id_promocion' => $promo, 'estado' => 1,
                                        'created_at' => $creacion
                                    ]);

                                    //Calculo de precio con promocion
                                    $totalPromocion = 0;
                                    if($existePromocion[0]->tipopromocion == 0){
                                        //Descuento por porcentaje
                                        $totalPromocion = ($contrato[0]->totalreal * $existePromocion[0]->precioP) / 100;
                                    }else{
                                        //Descuento por precio fijo
                                        $totalPromocion = $contrato[0]->totalreal - $existePromocion[0]->preciouno;
                                    }

                                    DB::table('contratos')->where('id', '=', $idContrato)->update(['totalpromocion' => $totalPromocion]);

                                    //Guardar en tabla historialcontrato
                                    DB::table('historialcontrato')->insert([
                                        'id' =>$globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                                        'id_usuarioC' => $usuarioId,
                                        'id_contrato' => $idContrato,
                                        'created_at' => Carbon::now(),
                                        'cambios' => "Agrego promocion con titulo: '" . $existePromocion[0]->titulo . "'"
                                    ]);

                                }else{
                                    //Promocion de tipo empleado - Generar solicitud de autorizacion

                                    //Insertar solicitud de autorizacion
                                    DB::table('autorizaciones')->insert([
                                        'id_contrato' => $idContrato, 'id_usuarioC' => $usuarioId, 'id_franquicia' => $idFranquicia,
                                        'fechacreacioncontrato' => $contrato[0]->created_at,
                                        'estadocontrato' => $contrato[0]->estatus_estadocontrato,
                                        'mensaje' => "Solicito autorizacion para promocion con titulo: '" . $existePromocion[0]->titulo . "' de tipo: 'Empleado'.",
                                        'estatus' => '0', 'tipo' => '16', 'valor' => $promo, 'created_at' => Carbon::now()
                                    ]);

                                    //Guardar en tabla historialcontrato
                                    DB::table('historialcontrato')->insert([
                                        'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                                        'id_usuarioC' => $usuarioId,
                                        'id_contrato' => $idContrato,
                                        'created_at' => Carbon::now(),
                                        'cambios' => "Solicitó autorización para promoción con titulo: '" . $existePromocion[0]->titulo . "' de tipo: 'Empleado'."
                                    ]);

                                }
                                return back()->with('bien', 'la promoción se agrego correctamente.');
                            }else{
                                return back()->with('alerta',"No existe promocion selecionada");
                            }
                        }

                    }else{
                        //Contrato con una promocion existente
                        return back()->with('alerta',"No puedes agregar una promoción debido a que ya existe una asignada al contrato.");
                    }
                }else{
                    return back()->with('alerta',"No puedes agregar una promocion debido al estado actual de este contrato.");
                }
            } else{
                return back()->with('alerta',"No existe el contrato.");
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function eliminarpromocionconfirmaciones($idContrato, $idPromocion)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15) || ((Auth::user()->rol_id) == 6)) {
            $contrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato'");

            if($contrato != null){
                //Existe el contrato
                if($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9){
                    //Contrato en estado No Terminado, Terminado o proceso de aprobacion

                    $sql = "SELECT id_promocion as id, p.titulo, p.asignado, p.inicio, p.fin, p.status, pr.estado,pr.id as idpromo FROM promocioncontrato  pr
                    inner join promocion p on pr.id_promocion = p.id
                    WHERE id_contrato = '$idContrato'";

                    $promocioncontrato = DB::select($sql);

                    if ($idPromocion == null) {
                        return back()->with('alerta', 'No se encontro el registro de la promoción en el contrato');
                    }
                    if ($promocioncontrato == null) {
                        return back()->with('alerta', 'No se encontro el registro de la promoción en el contrato');
                    }

                    $promocioncontrato = DB::select($sql);
                    $promotitulo = $promocioncontrato[0]->titulo;
                    $promocioncontratoid = $promocioncontrato[0]->idpromo;
                    $globalesServicioWeb = new globalesServicioWeb;
                    $usuarioId = Auth::user()->id;
                    $actualizar = Carbon::now();
                    $idFranquicia = $contrato[0]->id_franquicia;

                    try {
                        DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                            'id_promocion' => null, 'promocionterminada' => '0', 'estatus' => '0', 'totalpromocion' => '0'
                        ]);

                        DB::delete("DELETE FROM promocioncontrato WHERE id = '$promocioncontratoid' AND id_franquicia = '$idFranquicia'");

                        DB::table('historialcontrato')->insert(['id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato,
                            'created_at' => $actualizar, 'cambios' => 'Elimino la promoción en el contrato ' . $promotitulo]);

                        return back()->with('bien', 'La promoción se elimino correctamente.');

                    } catch (\Exception $e) {
                        \Log::info("Error: " . $e->getMessage());
                        return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
                    }
                }else{
                    return back()->with('alerta',"No puedes agregar una promocion debido al estado actual de este contrato.");
                }
            }else{
                return back()->with('alerta',"No existe el contrato");
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarlugarentregaconfirmaciones($idContrato, Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15) || ((Auth::user()->rol_id) == 6)) {
            //Validar lugar de entrega seleccionado
            $rules = [
                'lugarEntrega' => 'required',
            ];

            request()->validate($rules);

            $contrato = DB::select("SELECT * FROM contratos c WHERE c.id = '$idContrato'");

            if($contrato != null){
                //Existe el contrato
                if($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9){
                    //Contrato en estado No Terminado, Terminado o proceso de aprobacion
                    $lugarentregaSeleccionado = request('lugarEntrega');

                    if($lugarentregaSeleccionado != $contrato[0]->opcionlugarentrega) {

                        if ($lugarentregaSeleccionado == 0 || $lugarentregaSeleccionado == 1) {
                            $idFranquicia = $contrato[0]->id_franquicia;

                            try {

                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'opcionlugarentrega' => $lugarentregaSeleccionado, 'updated_at' => Carbon::now()
                                ]);

                                //Registrar movimiento
                                $globalesServicioWeb = new globalesServicioWeb;
                                DB::table('historialcontrato')->insert([
                                    'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                                    'id_usuarioC' => Auth::user()->id,
                                    'id_contrato' => $idContrato,
                                    'created_at' =>  Carbon::now(),
                                    'cambios' => "Actualizo lugar de entrega seleccionado."]);

                                return back()->with('bien', "Lugar de entrega actualizado correctamente");

                            } catch (\Exception $e) {
                                \Log::info("Error: " . $e->getMessage());
                                return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
                            }
                        } else {
                            return back()->with('alerta', "Selecciona un lugar de entrega valido.");
                        }
                    }else{
                        return back()->with('alerta', "Selecciona un lugar de entrega diferente al actual.");
                    }
                }else{
                    return back()->with('alerta',"No puedes agregar una promocion debido al estado actual de este contrato.");
                }

            }else{
                return back()->with('alerta',"No existe el contrato");
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarhistorialclinicoconfirmaciones($idContrato, $idHistorial){
        if (Auth::check() && ((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15)) {

            $globalesServicioWeb = new globalesServicioWeb;
            $esfericoDer = request('esfericod' . $idHistorial);
            $cilindroDer = request('cilindrod' . $idHistorial);
            $ejeDer = request('ejed' . $idHistorial);
            $addDer = request('addd' . $idHistorial);
            $altDer = request('altd' . $idHistorial);
            $esfericoIzq = request('esfericod2' . $idHistorial);
            $cilindroIzq = request('cilindrod2' . $idHistorial);
            $ejeIzq = request('ejed2' . $idHistorial);
            $addIzq = request('addd2' . $idHistorial);
            $altIzq = request('altd2' . $idHistorial);
            $material = (isset($_POST['material' . $idHistorial]))? $_POST['material' . $idHistorial] : "";
            $otroMaterial = null;
            $costoOtroMaterial = request('costoMaterial' . $idHistorial);
            $costoOtroMaterial = trim($costoOtroMaterial,"$");
            $costoOtroMaterial = (trim($costoOtroMaterial," ") == "")? null: $costoOtroMaterial;
            $policarbonato = 0;
            $bifocal = (isset($_POST['bifocal' . $idHistorial]))? $_POST['bifocal' . $idHistorial] : "";
            $otroBifocal = null;
            $costoOtroBifocal = request('costoBifocal' . $idHistorial);
            $costoOtroBifocal = trim($costoOtroBifocal,"$");
            $costoOtroBifocal = (trim($costoOtroBifocal," ") == "")? null: $costoOtroBifocal;
            $fotocromatico = (request('fotocromatico' . $idHistorial) != null)? 1 : null;
            $ar = (request('ar' . $idHistorial) != null)? 1 : null;
            $tinte = (request('tinte' . $idHistorial) != null)? 1 : null;
            $colorTinte = request('colorTinte' . $idHistorial);
            $estiloTinte = request('estiloTinte' . $idHistorial);
            $polarizado = (request('polarizado' . $idHistorial) != null)? 1 : null;
            $colorPolarizado = request('colorPolarizado' . $idHistorial);
            $espejo = (request('espejo' . $idHistorial) != null)? 1 : null;
            $colorEspejo = request('colorEspejo' . $idHistorial);
            $blueray = (request('blueray' . $idHistorial) != null)? 1 : null;
            $otroTratamiento = (request('otroTra' . $idHistorial) != null)? 1 : null;
            $otroTratamientoNombre = request('otroT' . $idHistorial);
            $precioOtroTratamiento = request('costoTratamiento' . $idHistorial);
            $precioOtroTratamiento = trim($precioOtroTratamiento,"$");
            $precioOtroTratamiento = (trim($precioOtroTratamiento," ") == "")? null: $precioOtroTratamiento;
            $movimientos = "";
            $cadenaErrores = "";
            $contrato = DB::select("SELECT id_franquicia, estatus_estadocontrato, promocionterminada, totalhistorial, totalpromocion, totalreal   FROM contratos WHERE id = '$idContrato'");

            if($tinte != null && ($colorTinte == null || $estiloTinte == null)){
                $cadenaErrores = "Debes elegir un color y un estilo para el tratamiento de tinte.<br>";
            }

            if($polarizado != null && $colorPolarizado == null){
                $cadenaErrores = $cadenaErrores . "Debes elegir un color para el tratamiento de polarizado.<br>";
            }

            if($espejo != null && $colorEspejo == null){
                $cadenaErrores = $cadenaErrores . "Debes elegir un color para el tratamiento de espejo.<br>";
            }

            if($otroTratamiento != null && ($otroTratamientoNombre == null || $precioOtroTratamiento == null)){
                $cadenaErrores = $cadenaErrores . "Por favor, proporciona la descripción de otro tratamiento junto con su respectivo precio.<br>";
            }

            if($cadenaErrores != ""){
                return back()->with('alerta',$cadenaErrores);
            }

            if($contrato != null){
                if($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9){
                    //Contrato en NO TERMINADO - TERMINADO - PROCESO DE APROBACION

                    $idFranquicia = $contrato[0]->id_franquicia;
                    $paquete = DB::select("SELECT p.nombre FROM paquetes p INNER JOIN historialclinico hc ON hc.id_paquete = p.id
                                                 WHERE p.id_franquicia = '$idFranquicia' AND hc.id = '$idHistorial'");
                    if($paquete != null){
                        //Verificar que paquete es

                        $historial = DB::select("SELECT * FROM historialclinico hc WHERE hc.id = '$idHistorial' AND hc.id_contrato = '$idContrato'");

                        if($paquete[0]->nombre == 'DORADO 2' || $paquete[0]->nombre == 'LECTURA'){
                            //Es paquete DORADO 2 o LECTURA
                            $esfericoSCDer = request('esfericodsc' . $idHistorial);
                            $cilindroSCDer = request('cilindrodsc' . $idHistorial);
                            $ejeSCDer = request('ejedsc' . $idHistorial);
                            $addSCDer = request('adddsc' . $idHistorial);
                            $esfericoSCIzq = request('esfericoizqsc' . $idHistorial);
                            $cilindroSCIzq = request('cilindroizqsc' . $idHistorial);
                            $ejeSCIzq = request('ejeizqsc' . $idHistorial);
                            $addSCIzq = request('addizqsc' . $idHistorial);
                            $movimientoHistorialSinConversion = "";

                            $historialSinConversion = DB::select("SELECT * FROM historialsinconversion hsc WHERE hsc.id_historial = '$idHistorial' AND hsc.id_contrato = '$idContrato'");

                            if($historialSinConversion[0]->esfericoder != $esfericoSCDer){
                                $movimientoHistorialSinConversion = $movimientoHistorialSinConversion . "Esferico(Derecho) Ant: " . $historialSinConversion[0]->esfericoder . " Nvo: " . $esfericoSCDer . " | ";
                            }

                            if($historialSinConversion[0]->cilindroder != $cilindroSCDer){
                                $movimientoHistorialSinConversion = $movimientoHistorialSinConversion . "Cilindro(Derecho) Ant: " . $historialSinConversion[0]->cilindroder . " Nvo: " . $cilindroSCDer . " | ";
                            }

                            if($historialSinConversion[0]->ejeder != $ejeSCDer){
                                $movimientoHistorialSinConversion = $movimientoHistorialSinConversion . "Eje(Derecho) Ant: " . $historialSinConversion[0]->ejeder . " Nvo: " . $ejeSCDer . " | ";
                            }

                            if($historialSinConversion[0]->addder != $addSCDer){
                                $movimientoHistorialSinConversion = $movimientoHistorialSinConversion . "Add(Derecho) Ant: " . $historialSinConversion[0]->addder . " Nvo: " . $addSCDer . " | ";
                            }

                            if($historialSinConversion[0]->esfericoizq != $esfericoSCIzq){
                                $movimientoHistorialSinConversion = $movimientoHistorialSinConversion . "Esferico(Izquierdo) Ant: " . $historialSinConversion[0]->esfericoizq . " Nvo: " . $esfericoSCIzq . " | ";
                            }

                            if($historialSinConversion[0]->cilindroizq != $cilindroSCIzq){
                                $movimientoHistorialSinConversion = $movimientoHistorialSinConversion . "Cilindro(Izquierdo) Ant: " . $historialSinConversion[0]->cilindroizq . " Nvo: " . $cilindroSCIzq . " | ";
                            }

                            if($historialSinConversion[0]->ejeizq != $ejeSCIzq){
                                $movimientoHistorialSinConversion = $movimientoHistorialSinConversion . "Eje(Izquierdo) Ant: " . $historialSinConversion[0]->ejeizq . " Nvo: " . $ejeSCIzq . " | ";
                            }

                            if($historialSinConversion[0]->addizq != $addSCIzq){
                                $movimientoHistorialSinConversion = $movimientoHistorialSinConversion . "Add(Izquierdo) Ant: " . $historialSinConversion[0]->addizq . " Nvo: " . $addSCIzq . " | ";
                            }

                            $movimientoHistorialSinConversion = trim($movimientoHistorialSinConversion," | ");

                            //Actualizar historial sin conversion
                            DB::table('historialsinconversion')->where([['id_historial', '=', $idHistorial], ['id_contrato', '=', $idContrato]])->update([
                                'esfericoder' => $esfericoSCDer, 'cilindroder' => $cilindroSCDer, 'ejeder' => $ejeSCDer, 'addder' => $addSCDer,
                                'esfericoizq' => $esfericoSCIzq, 'cilindroizq' => $cilindroSCIzq, 'ejeizq' => $ejeSCIzq, 'addizq' => $addSCIzq
                            ]);

                            //Guardar movimiento
                            if($movimientoHistorialSinConversion != ""){
                                DB::table('historialcontrato')->insert([
                                    'id' =>  $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => Auth::user()->id, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                    'cambios' => "Se modifico historial sin conversion '$idHistorial' con los siguientes movimientos: '" . $movimientoHistorialSinConversion . "'"
                                ]);
                            }

                        }

                        if($historial[0]->esfericoder != $esfericoDer){
                            $movimientos = $movimientos . "Esferico(Derecho) Ant: " . $historial[0]->esfericoder . " Nvo: " . $esfericoDer . " | ";
                        }

                        if($historial[0]->cilindroder != $cilindroDer){
                            $movimientos = $movimientos . "Cilindro(Derecho) Ant: " . $historial[0]->cilindroder . " Nvo: " . $cilindroDer . " | ";
                        }

                        if($historial[0]->ejeder != $ejeDer){
                            $movimientos = $movimientos . "Eje(Derecho) Ant: " . $historial[0]->ejeder . " Nvo: " . $ejeDer . " | ";
                        }

                        if($historial[0]->addder != $addDer){
                            $movimientos = $movimientos . "Add(Derecho) Ant: " . $historial[0]->addder . " Nvo: " . $addDer . " | ";
                        }

                        if($historial[0]->altder != $altDer){
                            $movimientos = $movimientos . "Alt(Derecho) Ant: " . $historial[0]->altder . " Nvo: " . $altDer . " | ";
                        }

                        if($historial[0]->esfericoizq != $esfericoIzq){
                            $movimientos = $movimientos . "Esferico(Izquierdo) Ant: " . $historial[0]->esfericoizq . " Nvo: " . $esfericoIzq . " | ";
                        }

                        if($historial[0]->cilindroizq != $cilindroIzq){
                            $movimientos = $movimientos . "Cilindro(Izquierdo) Ant: " . $historial[0]->cilindroizq . " Nvo: " . $cilindroIzq . " | ";
                        }

                        if($historial[0]->ejeizq != $ejeIzq){
                            $movimientos = $movimientos . "Eje(Izquierdo) Ant: " . $historial[0]->ejeizq . " Nvo: " . $ejeIzq . " | ";
                        }

                        if($historial[0]->addizq != $addIzq){
                            $movimientos = $movimientos . "Add(Izquierdo) Ant: " . $historial[0]->addizq . " Nvo: " . $addIzq . " | ";
                        }

                        if($historial[0]->altizq != $altIzq){
                            $movimientos = $movimientos . "Alt(Izquierdo) Ant: " . $historial[0]->altizq . " Nvo: " . $altIzq . " | ";
                        }

                        if($historial[0]->material != $material){
                            $movimientos = $movimientos . "Material Ant: " . self::obtenerNombreMaterialOBifocal($historial[0]->material, 0) . " Nvo: " . self::obtenerNombreMaterialOBifocal($material, 0)  . " | ";
                            if($material == 3){
                                //Selecciono otro material
                                $otroMaterial = request('motro' .$idHistorial);
                                $costoOtroMaterial = request('costoMaterial' . $idHistorial);
                                $costoOtroMaterial = trim($costoOtroMaterial,"$");
                                $costoOtroMaterial = trim($costoOtroMaterial," ");

                            }else{
                                if($material == 2){
                                    //Selecciono policarbonato
                                    $policarbonato =  (request('policarbonato' . $idHistorial) != 1)? 0 : 1 ;
                                }
                            }
                        }

                        if($historial[0]->material == 2 && $historial[0]->policarbonatotipo != request('policarbonato' . $idHistorial)){
                            $tipoPolicarbonatoActual = "(Adulto)";
                            $tipoPolicarbonatoNuevo = "(Adulto)";
                            if($historial[0]->policarbonatotipo == 1){
                                $tipoPolicarbonatoActual = "(Niño)";
                            }
                            if(request('policarbonato' . $idHistorial) == 1){
                                $tipoPolicarbonatoNuevo = "(Niño)";
                            }

                            $movimientos = $movimientos . "Material Ant: Policarbonato" . $tipoPolicarbonatoActual. " Nvo: Policarbonato" . $tipoPolicarbonatoNuevo . " | ";
                            $policarbonato =  (request('policarbonato' . $idHistorial) != 1)? 0 : 1 ;
                        }

                        if($historial[0]->bifocal != $bifocal){
                            $movimientos = $movimientos . "Tipo de bifocal Ant: " . self::obtenerNombreMaterialOBifocal($historial[0]->bifocal, 1) . " Nvo: " . self::obtenerNombreMaterialOBifocal($bifocal, 1)  . " | ";
                            if($bifocal == 4){
                                //Selecciono otro material
                                $otroBifocal = request('otroB' . $idHistorial);
                                $costoOtroBifocal = request('costoBifocal' . $idHistorial);
                                $costoOtroBifocal = trim($costoOtroBifocal,"$");
                                $costoOtroBifocal = trim($costoOtroBifocal," ");
                            }
                        }

                        $cadenaTratamientos = "Tratamiento ";
                        $cadenaNTratamiento = " Nvo: ";
                        if($historial[0]->fotocromatico == 1){
                            $cadenaTratamientos = $cadenaTratamientos . "Fotocromatico,";
                        }
                        if($historial[0]->ar == 1){
                            $cadenaTratamientos = $cadenaTratamientos . "AR,";
                        }
                        if($historial[0]->tinte == 1){
                            $cadenaTratamientos = $cadenaTratamientos . "Tinte(" . self::obtenerColorTratamiento($historial[0]->id_tratamientocolortinte) . " - " . (($historial[0]->estilotinte != null)? ($historial[0]->estilotinte == 0)? "DESVANECIDO": "COMPLETO" : "SIN ESTILO") . "),";
                        }
                        if($historial[0]->polarizado == 1){
                            $cadenaTratamientos = $cadenaTratamientos . "Polarizado(" . self::obtenerColorTratamiento($historial[0]->id_tratamientocolorpolarizado) . "),";
                        }
                        if($historial[0]->espejo == 1){
                            $cadenaTratamientos = $cadenaTratamientos . "Espejo(" . self::obtenerColorTratamiento($historial[0]->id_tratamientocolorespejo) . "),";
                        }
                        if($historial[0]->blueray == 1){
                            $cadenaTratamientos = $cadenaTratamientos . "BlueRay,";
                        }
                        if($historial[0]->otroT == 1){
                            $cadenaTratamientos = $cadenaTratamientos . "Otro(" . $historial[0]->tratamientootro . " - $" . $historial[0]->costotratamiento . ")";
                        }

                        if($fotocromatico != $historial[0]->fotocromatico && $fotocromatico != null){
                            $cadenaNTratamiento = $cadenaNTratamiento . "Fotocromatico,";
                        }
                        if($ar != $historial[0]->ar && $ar != null){
                            $cadenaNTratamiento = $cadenaNTratamiento . "AR,";
                        }
                        if(($tinte != $historial[0]->tinte && $tinte != null) || ($historial[0]->id_tratamientocolortinte != $colorTinte || $historial[0]->estilotinte != $estiloTinte)){
                            $cadenaNTratamiento = $cadenaNTratamiento . "Tinte(" . self::obtenerColorTratamiento($colorTinte) . " - " . (($estiloTinte == 0)?"DESVANECIDO" : "COMPLETO") . "),";
                        }
                        if(($polarizado != $historial[0]->polarizado && $polarizado != null) || $historial[0]->id_tratamientocolorpolarizado != $colorPolarizado){
                            $cadenaNTratamiento = $cadenaNTratamiento . "Polarizado(" . self::obtenerColorTratamiento($colorPolarizado) . "),";
                        }
                        if(($espejo != $historial[0]->espejo && $espejo != null) || $historial[0]->id_tratamientocolorespejo != $colorEspejo){
                            $cadenaNTratamiento = $cadenaNTratamiento . "Espejo(" . self::obtenerColorTratamiento($colorEspejo) . "),";
                        }
                        if($blueray != $historial[0]->blueray && $blueray != null){
                            $cadenaNTratamiento = $cadenaNTratamiento . "BlueRay,";
                        }
                        if($otroMaterial != $historial[0]->otroT && $otroMaterial != null){
                            $cadenaNTratamiento = $cadenaNTratamiento . "Otro (" . $otroTratamientoNombre . " - $" . $precioOtroTratamiento . ")";
                        }

                        $cadenaTratamientos = trim($cadenaTratamientos,",");
                        $cadenaNTratamiento = trim($cadenaNTratamiento,",");
                        $cadenaNTratamiento = trim($cadenaNTratamiento," ");
                        $movimientos = trim($movimientos," | ");

                        if($cadenaNTratamiento != "Nvo:"){
                            //Se modificaron los tratamientos
                            $movimientos = $movimientos . " " . $cadenaTratamientos . " " .$cadenaNTratamiento;
                        }

                        //Actualizar historial
                        DB::table('historialclinico')->where([['id', '=', $idHistorial], ['id_contrato', '=', $idContrato]])->update([
                            'esfericoder' => $esfericoDer, 'cilindroder' => $cilindroDer, 'ejeder' => $ejeDer, 'addder' => $addDer, 'altder' => $altDer,
                            'esfericoizq' => $esfericoIzq, 'cilindroizq' => $cilindroIzq, 'ejeizq' => $ejeIzq, 'addizq' => $addIzq, 'altizq' => $altIzq,
                            'material' => $material, 'materialotro' => $otroMaterial, 'costomaterial' => $costoOtroMaterial, 'policarbonatotipo' => $policarbonato,
                            'bifocal' => $bifocal, 'bifocalotro' => $otroBifocal, 'costobifocal' => $costoOtroBifocal, 'fotocromatico' => $fotocromatico,
                            'ar' => $ar, 'tinte' => $tinte, 'id_tratamientocolortinte' => $colorTinte,'estilotinte' => $estiloTinte,'polarizado' => $polarizado,
                            'id_tratamientocolorpolarizado' => $colorPolarizado, 'espejo' => $espejo, 'id_tratamientocolorespejo' => $colorEspejo,
                            'blueray' => $blueray, 'otroT' => $otroTratamiento, 'tratamientootro' => $otroTratamientoNombre, 'costotratamiento' => $precioOtroTratamiento
                        ]);

                        //Guardar movimiento
                        if($movimientos != ""){
                            DB::table('historialcontrato')->insert([
                                'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => Auth::user()->id, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                'cambios' => "Se modifico historial clinico '$idHistorial' con los siguientes movimientos: '" . $movimientos . "'"
                            ]);
                        }

                        //Calculo de precio
                        $precioMaterialOtro = $historial[0]->costomaterial;
                        $precioPolicarbonato = 300;
                        $precioMaterial = ($historial[0]->material == 2 && $historial[0]->policarbonatotipo == 0)? $precioPolicarbonato : 0;
                        $precioBifocalOtro = $historial[0]->costobifocal;
                        $precioTratamientoOtro = $historial[0]->costotratamiento;
                        $precioCilindroLectura = 0;

                        //Datos ingresados desde pagina
                        $costoOtroMaterial = ($costoOtroMaterial != null )? $costoOtroMaterial: 0;
                        $costoOtroBifocal = ($costoOtroBifocal != null )? $costoOtroBifocal: 0;

                        if($precioMaterialOtro > 0){
                            //Ya habia sido seleccionado otro material
                            if($material == 3){
                                //Sigue seleccionado otro material
                                if($precioMaterialOtro == $costoOtroMaterial){
                                    //No se suma ni se resta nada
                                    $precioMaterialOtro = 0;
                                }else if($costoOtroMaterial > $precioMaterialOtro){
                                    //El precio que se puso actual es mayor a lo que estaba anteriormente (Se suma el restante de lo que se puso actual)
                                    $precioMaterialOtro = $costoOtroMaterial - $precioMaterialOtro;
                                }else{
                                    //El precio que se puso actual es menor a lo que estaba anteriormente (Se resta el restante de lo que estaba anteriormente)
                                    $precioMaterialOtro = $precioMaterialOtro - $costoOtroMaterial;
                                    $precioMaterialOtro = ($precioMaterialOtro * -1);
                                }
                            }else{
                                //Se deselecciona otro material (Se resta el precio)
                                $precioMaterialOtro = $precioMaterialOtro * -1;
                            }
                        }else{
                            //No habia sido seleccionado otro material
                            if($material == 3){
                                //Se selecciono otro material (Se suma a precio)
                                $precioMaterialOtro = $costoOtroMaterial;
                            }
                        }

                        if($historial[0]->material == 2){
                            //Ya habia seleccionado policarbonato
                            if($material == 2){
                                //Esta aun seleccionado material policarbonato
                                if($precioMaterial > 0){
                                    //Es policarbonato para adulto
                                    if($policarbonato == 1){
                                        //Se selecciono policarbonato para niño - Descontar precio de policrabonato
                                        $precioMaterial = ($precioPolicarbonato * -1);
                                    }else{
                                        $precioMaterial = 0;
                                    }
                                }else{
                                    //Es policarbonato para niño
                                    if($policarbonato == 0){
                                        //Se quito checkbox para niño - Sumar precio a policarbonato
                                        $precioMaterial = $precioPolicarbonato;
                                    }
                                }

                            }else{
                                //No se selecciono rbPolicarbonato
                                if($precioMaterial > 0){
                                    //Estaba seleccionado antes
                                    $precioMaterial = ($precioPolicarbonato * -1);
                                }else{
                                    $precioMaterial = 0;
                                }
                            }

                        }else{
                            //No habia sido seleccionado policarbonato
                            if($material == 2){
                                //Se selecciono policarbonato
                                if($policarbonato == 0){
                                    //Es policarbonato adulto - Sumar precio de policarbonato.
                                    $precioMaterial = $precioPolicarbonato;
                                }
                            }
                        }

                        if($precioBifocalOtro > 0){
                            //Ya habia sido seleccionado otro bifocal
                            if($bifocal == 4){
                                //Sigue seleccionado otro bifocal
                                if($precioBifocalOtro == $costoOtroBifocal){
                                    //No se suma ni resta nada
                                    $precioBifocalOtro = 0;
                                } else if ($costoOtroBifocal > $precioBifocalOtro){
                                    //El precio que se puso actual es mayor a lo que estaba anteriormente (Se suma el restante de lo que se puso actual)
                                    $precioBifocalOtro = $costoOtroBifocal - $precioBifocalOtro;
                                }else{
                                    //El precio que se puso actual es menor a lo que estaba anteriormente (Se resta el restante de lo que estaba anteriormente)
                                    $precioBifocalOtro = $precioBifocalOtro - $costoOtroBifocal;
                                    $precioBifocalOtro = ($precioBifocalOtro * -1);
                                }

                            }else{
                                //Se deselecciona otro bifocal (Se resta el precio)
                                $precioBifocalOtro = ($precioBifocalOtro * -1);
                            }
                        }else{
                            //No habia sido seleccionado otro bifocal
                            if($bifocal == 4){
                                //Se selecciona otro bifocal (Se suma el precio)
                                $precioBifocalOtro = $costoOtroBifocal;
                            }
                        }

                        if($precioTratamientoOtro > 0){
                            //Ya habia sido seleccionado otro tratamiento
                            if($otroTratamiento != null){
                                //Sigue seleccionado otro tratamiento
                                if($precioTratamientoOtro == $precioOtroTratamiento){
                                    //No se suma ni resta nada
                                    $precioTratamientoOtro = 0;
                                } else if ($precioOtroTratamiento > $precioTratamientoOtro){
                                    //El precio que se puso actual es mayor a lo que estaba anteriormente (Se suma el restante de lo que se puso actual)
                                    $precioTratamientoOtro = $precioOtroTratamiento - $precioTratamientoOtro;
                                }else{
                                    //El precio que se puso actual es menor a lo que estaba anteriormente (Se resta el restante de lo que estaba anteriormente)
                                    $precioTratamientoOtro = $precioTratamientoOtro - $precioOtroTratamiento;
                                    $precioTratamientoOtro = ($precioTratamientoOtro * -1);
                                }

                            }else{
                                //Se deselecciona otro bifocal (Se resta el precio)
                                $precioTratamientoOtro = ($precioTratamientoOtro * -1);
                            }
                        }else{
                            //No habia sido seleccionado otro tratamiento
                            if($otroTratamiento != null){
                                //Se selecciona otro tratamiento (Se suma el precio)
                                $precioTratamientoOtro = $precioOtroTratamiento;
                            }
                        }

                        $totalHistorial = $contrato[0]->totalhistorial;
                        $totalPromocion = $contrato[0]->totalpromocion;
                        $totalReal = $contrato[0]->totalreal;

                        self::actualizarPrecioTotalContrato($idContrato,"totalhistorial", $totalHistorial + ($precioMaterialOtro + $precioMaterial + $precioBifocalOtro + $precioTratamientoOtro));
                        if($contrato[0]->promocionterminada == 1){
                            self::actualizarPrecioTotalContrato($idContrato,"totalpromocion", $totalPromocion + ($precioMaterialOtro + $precioMaterial + $precioBifocalOtro + $precioTratamientoOtro));
                        }
                        self::actualizarPrecioTotalContrato($idContrato,"totalreal", $totalReal + ($precioMaterialOtro + $precioMaterial + $precioBifocalOtro + $precioTratamientoOtro));

                        return back()->with('bien',"Historial clinico actualizado correctamente");

                    }else{
                        return back()->with('alerta',"No es posible actualizar el historial clínico, existe un error con los datos actuales.");
                    }

                }else{
                    //Contrato en estatus superior a APROBACION
                    return back()->with('alerta',"No es posible actualizar el historial clínico del contrato debido a su estado actual.");
                }

            }else{
                //No existe el contrato
                return back()->with('alerta',"No existe el contrato.");
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function actualizarfotoarmazonconfirmaciones($idContrato, $idHistorial){
        if (Auth::check() && (((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15))) {

            request()->validate([
                'fotoArmazon' . $idHistorial => 'required|image|mimes:jpg'
            ]);

            $contrato = DB::select("SELECT estatus_estadocontrato FROM contratos c WHERE c.id = '$idContrato'");
            if($contrato != null){
                //Existe el contrato
                if($contrato[0]->estatus_estadocontrato <= 1 || $contrato[0]->estatus_estadocontrato == 9){
                    //Contrato en estado NO TERMINADO, TERMINADO o PROCESO DE APROBACION
                    $existeHistorial = DB::select("SELECT * FROM historialclinico hc WHERE hc.id_contrato = '$idContrato' AND hc.id = '$idHistorial'");
                    if($existeHistorial != null){

                        $garantia = DB::select("SELECT estadogarantia FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia IN (0,1,2) ORDER BY created_at DESC LIMIT 1");
                        //Tiene garantia?
                        if($garantia == null){
                            //Validar peso del archivo
                            $contratosGlobal = new contratosGlobal;
                            if($contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('fotoArmazon' . $idHistorial))){
                                //Imagen pesa menos de 1MB.

                                if($existeHistorial[0]->id_paquete != "6"){
                                    //Paquete diferente de DORADO 2
                                    $rutaAlmacenamiento = "uploads/imagenes/contratos/fotoarmazon1";
                                }else{
                                    //Es DORADO 2
                                    $rutaAlmacenamiento = "uploads/imagenes/contratos/fotoarmazon1";
                                    $historialesContrato = DB::select("SELECT * FROM historialclinico hc WHERE hc.id_contrato = '$idContrato' ORDER BY hc.created_at ASC");
                                    if(sizeof($historialesContrato) == 2){
                                        //Tiene 2 historiales
                                        if($historialesContrato[1]->id == $idHistorial){
                                            $rutaAlmacenamiento = "uploads/imagenes/contratos/fotoarmazon2";
                                        }
                                    }
                                }

                                if($existeHistorial[0]->fotoarmazon != null){
                                    //Existe imagen - Eliminar para colocar nueva
                                    Storage::disk('disco')->delete($existeHistorial[0]->fotoarmazon);
                                }

                                //Almacenar nueva imagen
                                if (request()->hasFile('fotoArmazon' . $idHistorial)) {
                                    $fotoArmazonBruta = 'Foto-Armazon-Propio-' . $idContrato . '-' . $idHistorial . "-" . time() . '.' . request()->file('fotoArmazon' . $idHistorial)->getClientOriginalExtension();
                                    $fotoArmazon = request()->file('fotoArmazon' . $idHistorial)->storeAs($rutaAlmacenamiento, $fotoArmazonBruta, 'disco');
                                    $alto = Image::make(config('filesystems.disks.disco.root') . '/' . $rutaAlmacenamiento . '/' . $fotoArmazonBruta)->height();
                                    $ancho = Image::make(config('filesystems.disks.disco.root') . '/' . $rutaAlmacenamiento . '/' . $fotoArmazonBruta)->width();
                                    if ($alto > $ancho) {
                                        $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/' . $rutaAlmacenamiento . '/' . $fotoArmazonBruta)->resize(600, 800);
                                    } else {
                                        $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/' . $rutaAlmacenamiento . '/' . $fotoArmazonBruta)->resize(800, 600);
                                    }
                                    $imagenfoto->save();
                                }

                                DB::table('historialclinico')->where([['id_contrato', '=', $idContrato], ['id', '=', $idHistorial]])->update([
                                    'fotoarmazon' => $fotoArmazon
                                ]);

                                $globalesServicioWeb = new globalesServicioWeb;
                                DB::table('historialcontrato')->insert([
                                    'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => Auth::user()->id, 'id_contrato' => $idContrato,
                                    'created_at' => Carbon::now(), 'cambios' => "Actualizo foto armazon para historial clinico '$idHistorial'"
                                ]);

                                return back()->with('bien',"Foto armazon actualizada correctamente");

                            }else{
                                //Imagen mayor a un MB
                                return back()->with('alerta',"Verifica el archivo 'Foto armazon', el tamaño maximo permitido es 1MB.");
                            }

                        }else{
                            //Contrato con garantia
                            return back()->with('alerta',"NO puedes actualizar foto armazon debido a que existe una garantia.");
                        }
                    }else{
                        //No existe historial clinico
                        return back()->with('alerta',"No existe historial clinico.");
                    }

                }else{
                    return back()->with('alerta','No puedes actualizar la foto armazon debido a el estado actual del contrato.');
                }
            }else{
                return back()->with('alerta','No existe el contrato');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    private function getPromocionContratoId()
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $temporalId = $this->generadorRandom2();
            $existente = DB::select("select id from promocioncontrato where id = '$temporalId'");
            if (sizeof($existente) == 0) {
                $unico = $temporalId;
                $esUnico = true;
            }
        }
        return $unico;
    }
    private static function generadorRandom2($length = 5)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($caracteres);
        $randomId = '';
        for ($i = 0; $i < $length; $i++) {
            $randomId .= $caracteres[rand(0, $charactersLength - 1)];
        }
        return $randomId;
    }

    public static function obtenerNombreMaterialOBifocal($id_material, $opcion){
        //Opciones
        // 0 -> Material
        // 1 -> Bifocal
        $nombre = "";
        switch ($opcion){
            case 0:
                //Material
                switch ($id_material){
                    case 0:
                        //Hi Index
                        $nombre = "Hi Index";
                        break;
                    case 1:
                        //CR
                        $nombre = "CR";
                        break;
                    case 2:
                        //Policarbonato
                        $nombre = "Policarbonato";
                        break;
                    case 3:
                        //Otro
                        $nombre = "Otro";
                        break;
                }
                break;
            case 1:
                //Bifocal
                switch ($id_material){
                    case 0:
                        //FT
                        $nombre = "FT";
                        break;
                    case 1:
                        //Blend
                        $nombre = "Blend";
                        break;
                    case 2:
                        //Progresivo
                        $nombre = "Progresivo";
                        break;
                    case 3:
                        //N/A
                        $nombre = "N/A";
                        break;
                    case 4:
                        //Otro
                        $nombre = "Otro";
                        break;
                    case "":
                        //N/A
                        $nombre = "N/A";
                        break;
                }
                break;
        }

        return $nombre;
    }

    public static function obtenerColorTratamiento($indiceColor){
        $color = "";
        $tratamiento = DB::select("SELECT tc.color FROM tratamientoscolores tc WHERE tc.indice = '$indiceColor'");

        if($tratamiento != null){
            $color = $tratamiento[0]->color;
        }

        return $color;
    }

    public static function actualizarPrecioTotalContrato($id_contrato, $atributo, $precioTotalFinal){
        DB::table('contratos')->where('id', '=', $id_contrato)->update([
            $atributo => $precioTotalFinal]);
    }

    public function listaconfirmacionesrechazadosprincipal(Request $request){

        if(Auth::check() && (((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15)))        {

            $fechaIniSeleccionada = $request->input('fechaIni');

            //Solo los roles principal, director y confirmaciones pueden entrar
            $hoy = Carbon::now()->format('Y-m-d');
            if(strlen($fechaIniSeleccionada) == 0){
                //Si fecha inicial es vacia -> colocar fecha del dia de hoy
                $fechaIniSeleccionada = $hoy;
            }

            $fecha = Carbon::parse($fechaIniSeleccionada);
            $lunes = $fecha->copy()->startOfWeek();
            $sabado = $fecha->copy()->endOfWeek()->subDay();


            try{

                if((Auth::user()->rol_id) == 8 || (Auth::user()->rol_id) == 7){
                    //Es un usuario principal o director

                    $contratosRechazados = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, u.name,
                                                                           us.name as usuariocreacion, f.ciudad as sucursal, c.nombre, c.telefono, c.created_at, c.fecharechazadoconfirmaciones
                                                                   FROM contratos c
                                                                   INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                                   INNER JOIN usuariosfranquicia uf ON c.id_franquicia = uf.id_franquicia
                                                                   INNER JOIN users u ON c.id_optometrista = u.id
                                                                   INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                                   INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                                   WHERE c.estatus_estadocontrato = '8'
                                                                   AND c.id_franquicia != '00000'
                                                                   AND DATE(c.fecharechazadoconfirmaciones) BETWEEN '$lunes' AND '$sabado'
                                                                   GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.id_optometrista,u.name,
                                                                            us.name, f.ciudad, c.nombre, c.telefono, c.created_at, c.fecharechazadoconfirmaciones
                                                                   ORDER BY c.fecharechazadoconfirmaciones DESC");
                }else{
                    //Es un usuario de confirmaciones

                    $contratosRechazados = DB::select("SELECT c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion, c.id_optometrista, u.name,
                                                                          us.name as usuariocreacion, f.ciudad as sucursal, c.nombre, c.telefono, c.created_at, c.fecharechazadoconfirmaciones
                                                FROM contratos c
                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato
                                                INNER JOIN sucursalesconfirmaciones sc ON c.id_franquicia = sc.id_franquicia
                                                INNER JOIN users u ON c.id_optometrista = u.id
                                                INNER JOIN users us ON c.id_usuariocreacion = us.id
                                                INNER JOIN franquicias f ON c.id_franquicia = f.id
                                                WHERE sc.id_usuario = '".Auth::user()->id."'
                                                AND c.estatus_estadocontrato = '8'
                                                AND DATE(c.fecharechazadoconfirmaciones) BETWEEN '$lunes' AND '$sabado'
                                                GROUP BY c.id,ec.descripcion,c.estatus_estadocontrato,c.banderacomentarioconfirmacion,c.id_optometrista,u.name,
                                                us.name, f.ciudad, c.nombre, c.telefono, c.created_at, c.fecharechazadoconfirmaciones
                                                ORDER BY c.fecharechazadoconfirmaciones DESC");
                }

            }catch(\Exception $e){
                \Log::info("Error".$e);
            }

            $view = view('administracion.confirmaciones.confirmacioneslaboratorio.tablarechazadosvistaprincipal', [
                'contratosRechazados'=> $contratosRechazados])->render();

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
        return \Response::json(array("valid"=>"true","view"=>$view, "fechaIni" => $fechaIniSeleccionada));
    }

    public function restablecercontratorechazado($idContrato){
        if(Auth::check() && (((Auth::user()->rol_id) == 7)  || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 15)))
        {

            try {

                $contrato = DB::select("SELECT c.estatus_estadocontrato as estatus_estadocontrato,
                                                    c.id_usuariocreacion as id_usuariocreacion
                                                    FROM contratos c WHERE c.id = '$idContrato'");

                if ($contrato != null) {
                    //Existe el contrato

                    $estadocontrato = $contrato[0]->estatus_estadocontrato;
                    $id_usuariocreacion = $contrato[0]->id_usuariocreacion;

                    if ($estadocontrato == 8) {
                        //Rechazado

                        $estadoactualizar = 1;
                        $registroestadocontrato = DB::select("SELECT estatuscontrato
                                                    FROM registroestadocontrato WHERE id_contrato = '$idContrato' AND estatuscontrato NOT IN (8)
                                                    ORDER BY created_at DESC LIMIT 1");

                        if ($registroestadocontrato != null) {
                            $estadoactualizar = $registroestadocontrato[0]->estatuscontrato;
                        }

                        //Actualizar estado
                        DB::table("contratos")->where("id", "=", $idContrato)->update([
                            'estatus_estadocontrato' => $estadoactualizar,
                            'fecharechazadoconfirmaciones' => null
                        ]);

                        //Insertar en tabla registroestadocontrato
                        DB::table('registroestadocontrato')->insert([
                            'id_contrato' => $idContrato,
                            'estatuscontrato' => $estadoactualizar,
                            'created_at' => Carbon::now()
                        ]);

                        //Regresar pieza de armazon de los historiales del contrato
                        $historialesclinicos = DB::select("SELECT id_producto FROM historialclinico WHERE id_contrato = '$idContrato' ORDER BY created_at DESC");
                        if ($historialesclinicos != null) {
                            //Existen historiales
                            foreach ($historialesclinicos as $historialclinico) {
                                DB::update("UPDATE producto
                                    SET piezas = piezas - 1,
                                    updated_at = '" . Carbon::now() . "'
                                    WHERE id = '" . $historialclinico->id_producto . "'");
                            }
                        }

                        //Guardar en tabla historialcontrato
                        $usuarioId = Auth::user()->id;
                        $globalesServicioWeb = new globalesServicioWeb;
                        DB::table('historialcontrato')->insert([
                            'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                            'cambios' => "Se restauro el estatus del contrato de 'rechazado'"
                        ]);

                        //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                        $contratosGlobal = new contratosGlobal;
                        $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $id_usuariocreacion);

                        //Actualizar contrato en tabla contratoslistatemporales
                        $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                        return redirect()->route('estadoconfirmacion', [$idContrato])->with('bien', 'El contrato se restauro correctamente');

                    }

                    return back()->with('alerta', 'No se puede restablecer el contrato, debe estar en estado de RECHAZADO.');

                }
                return back()->with('alerta', 'No se encontro el contrato.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

}
