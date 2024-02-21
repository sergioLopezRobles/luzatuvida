<?php

namespace App\Http\Controllers\Dominios\Administracion;

use App\archivos;
use App\Clases\contratosGlobal;
use App\Clases\globalesServicioWeb;
use App\Models\User;
use Carbon\Carbon;
use App\Clases\polizaGlobales;
use DateTime;
use Extractor;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use mysql_xdevapi\Exception;
use Session;
use Image;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use ZipArchive;
use Picqer\Barcode\BarcodeGeneratorPNG;


class franquicias extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }//constructor

    public function nuevafranquicia()
    {
        if (Auth::check() && (Auth::user()->rol_id) == 7) //ID DEL DIRECTOR
        {
            return view('administracion.franquicia.nueva');
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    //Funcion para dar de alta una nueva franquicia
    public function crearFranquicia()
    {
        if (Auth::check() && (Auth::user()->rol_id) == 7) //ID DEL DIRECTOR
        {
            request()->validate([
                'foto' => 'nullable|image|mimes:jpg',
                'curp' => 'nullable|file|mimes:pdf',
                'rfc' => 'nullable|file|mimes:pdf',
                'hacienda' => 'nullable|file|mimes:pdf',
                'actanacimiento' => 'nullable|file|mimes:pdf',
                'identificacion' => 'nullable|file|mimes:pdf',
                'estado' => 'required|max:30',
                'ciudad' => 'required|max:255',
                'colonia' => 'required|max:255',
                'calle' => 'required|max:255',
                'entrecalles' => 'required|max:255',
                'numero' => 'required|max:10',
                'telefonofranquicia' => 'required|string|size:10|regex:/[0-9]/',
                'comprobante' => 'nullable|file|mimes:pdf',
                'observaciones' => 'max:300',
                'telefonoatencionclientes' => 'required|string|min:10|max:13|regex:/^[0-9\-]+$/',
                'whatsapp' => 'required|string|min:10|max:13|regex:/^[0-9\-]+$/',
                'coordenadas' => 'nullable|regex:/^[-]?\d+[\.]?\d*, [-]?\d+[\.]?\d*$/'
            ]);

            //Validar tamaño de archivos adjuntos
            $contratosGlobal = new contratosGlobal();
            $contador = 0;
            $nombreArchivos = "";

            if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('foto'))){
                $nombreArchivos = " Foto,";
                $contador = $contador + 1;
            }
            if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('curp'))){
                $nombreArchivos = $nombreArchivos . " CURP,";
                $contador = $contador + 1;
            }
            if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('rfc'))){
                $nombreArchivos = $nombreArchivos . " RFC,";
                $contador = $contador + 1;
            }
            if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('hacienda'))){
                $nombreArchivos = $nombreArchivos . " Alta en hacienda,";
                $contador = $contador + 1;
            }
            if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('actanacimiento'))){
                $nombreArchivos = $nombreArchivos . " Acta de nacimiento,";
                $contador = $contador + 1;
            }
            if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('identificacion'))){
                $nombreArchivos = $nombreArchivos . " Idetntificacion,";
                $contador = $contador + 1;
            }
            if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('comprobante'))){
                $nombreArchivos = $nombreArchivos . " Comprobante de domicilio,";
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

            $total = 0;
            $franquicias = DB::select("SHOW TABLE STATUS LIKE 'franquicias'");
            $siguienteId = $franquicias[0]->Auto_increment;
            try {

                $foto = "";
                $fotoBool = false;
                if (request()->hasFile('foto')) {
                    $fotoBruta = 'Foto-Franquicia-' . $siguienteId . '-' . time() . '.' . request()->file('foto')->getClientOriginalExtension();
                    $foto = request()->file('foto')->storeAs('uploads/imagenes/franquicia/fotos', $fotoBruta, 'disco');
                    $fotoBool = true;
                    $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/franquicia/fotos/' . $fotoBruta)->height();
                    $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/franquicia/fotos/' . $fotoBruta)->width();
                    if ($alto > $ancho) {
                        $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/franquicia/fotos/' . $fotoBruta)->resize(600, 800);
                    } else {
                        $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/franquicia/fotos/' . $fotoBruta)->resize(800, 600);
                    }
                    $imagenfoto->save();
                }
                $curp = "";
                $curpBool = false;
                if (request()->hasFile('curp')) {
                    $curpBruto = 'Curp-Franquicia-' . $siguienteId . '-' . time() . '.' . request()->file('curp')->getClientOriginalExtension();
                    $curp = request()->file('curp')->storeAs('uploads/imagenes/franquicia/curp', $curpBruto, 'disco');
                    $curpBool = true;

                }
                $rfc = "";
                $rfcBool = false;
                if (request()->hasFile('rfc')) {
                    $rfcBruto = 'Rfc-Franquicia-' . $siguienteId . '-' . time() . '.' . request()->file('rfc')->getClientOriginalExtension();
                    $rfc = request()->file('rfc')->storeAs('uploads/imagenes/franquicia/rfc', $rfcBruto, 'disco');
                    $rfcBool = true;

                }
                $hacienda = "";
                $haciendaBool = false;
                if (request()->hasFile('hacienda')) {
                    $haciendaBruta = 'Hacienda-Franquicia-' . $siguienteId . '-' . time() . '.' . request()->file('hacienda')->getClientOriginalExtension();
                    $hacienda = request()->file('hacienda')->storeAs('uploads/imagenes/franquicia/hacienda', $haciendaBruta, 'disco');
                    $haciendaBool = true;

                }
                $actanacimiento = "";
                $actanacimientoBool = false;
                if (request()->hasFile('actanacimiento')) {
                    $actanacimientoBruta = 'Actanacimiento-Franquicia-' . $siguienteId . '-' . time() . '.' . request()->file('actanacimiento')->getClientOriginalExtension();
                    $actanacimiento = request()->file('actanacimiento')->storeAs('uploads/imagenes/franquicia/actanacimiento', $actanacimientoBruta, 'disco');
                    $actanacimientoBool = true;

                }
                $identificacion = "";
                $identificacionBool = false;
                if (request()->hasFile('identificacion')) {
                    $identificacionBruta = 'Identificacion-Franquicia-' . $siguienteId . '-' . time() . '.' . request()->file('identificacion')->getClientOriginalExtension();
                    $identificacion = request()->file('identificacion')->storeAs('uploads/imagenes/franquicia/identificacion', $identificacionBruta, 'disco');
                    $identificacionBool = true;

                }

                $estado = "";
                $estadoBool = false;
                if (strlen(request('estado')) > 0) {
                    $estado = request('estado');
                    $estadoBool = true;
                }

                $ciudad = "";
                $ciudadBool = false;
                if (strlen(request('ciudad')) > 0) {
                    $ciudad = request('ciudad');
                    $ciudadBool = true;
                }
                $colonia = "";
                $coloniaBool = false;
                if (strlen(request('colonia')) > 0) {
                    $colonia = request('colonia');
                    $coloniaBool = true;
                }
                $numero = "";
                $numeroBool = false;
                if (strlen(request('numero')) > 0) {
                    $numero = request('numero');
                    $numeroBool = true;
                }
                $comprobante = "";
                $comprobanteBool = false;
                if (request()->hasFile('comprobante')) {
                    $comprobanteBruto = 'Comprobante-Franquicia-' . $siguienteId . '-' . time() . '.' . request()->file('comprobante')->getClientOriginalExtension();
                    $comprobante = request()->file('comprobante')->storeAs('uploads/imagenes/franquicia/comprobante', $comprobanteBruto, 'disco');
                    $comprobanteBool = true;
                }

                $observaciones = "";
                $observacionesBool = false;
                if (strlen(request('observaciones')) > 0) {
                    $observaciones = request('observaciones');
                    $observacionesBool = true;
                }

                $generado = false;
                do {
                    $idFranquicia = strtoupper(Str::random(5));
                    $existe = DB::select("SELECT id FROM franquicias WHERE id =' $idFranquicia'");
                    if ($existe == null) {
                        try {
                            $generado = true;
                        } catch (\Exception $e) {
                            \Log::error('Error: ' . $e->getMessage());
                            return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina.')->with('fcreada');
                        }
                    }
                } while (!$generado);


                $actualizadopor = "";
                DB::table('franquicias')->insert([
                    'id' => $idFranquicia, 'creadopor' => strtoupper(Auth::user()->name), 'actualizadopor' => $actualizadopor, 'foto' => $foto, 'curp' => $curp, 'rfc' => $rfc,
                    'hacienda' => $hacienda, 'actanacimiento' => $actanacimiento, 'identificacion' => $identificacion, 'estado' => strtoupper($estado), 'ciudad' => strtoupper($ciudad),
                    'colonia' => strtoupper($colonia), 'calle' => strtoupper(request('calle')), 'entrecalles' => strtoupper(request('entrecalles')), 'numero' => strtoupper($numero),
                    'comprobante' => $comprobante, 'observaciones' => strtoupper($observaciones), 'telefonofranquicia' => request('telefonofranquicia'), 'created_at' => Carbon::now(),
                    'telefonoatencionclientes' => request('telefonoatencionclientes'), 'whatsapp' => request('whatsapp'), 'coordenadas' => request('coordenadas')
                ]);

                if (request('activo') == 1) {
                    $status = 1;
                } else {
                    if ($fotoBool && $curpBool && $rfcBool && $haciendaBool && $actanacimientoBool && $identificacionBool && $estadoBool && $ciudadBool && $coloniaBool && $numeroBool
                        && $comprobanteBool && $observacionesBool) {
                        $status = 1;
                    } else {
                        $status = 2;
                    }
                }


                //TRATAMIENTOS
                DB::table("tratamientos")->insert([
                    "id" => 3,
                    "id_franquicia" => $idFranquicia,
                    "nombre" => "Fotocromático",
                    "precio" => "600",
                    "created_at" => Carbon::now()
                ]);

                DB::table("tratamientos")->insert([
                    "id" => 4,
                    "id_franquicia" => $idFranquicia,
                    "nombre" => "A/R",
                    "precio" => "0",
                    "created_at" => Carbon::now()
                ]);

                DB::table("tratamientos")->insert([
                    "id" => 5,
                    "id_franquicia" => $idFranquicia,
                    "nombre" => "tinte",
                    "precio" => "200",
                    "created_at" => Carbon::now()
                ]);

                DB::table("tratamientos")->insert([
                    "id" => 6,
                    "id_franquicia" => $idFranquicia,
                    "nombre" => "BlueRay",
                    "precio" => "600",
                    "created_at" => Carbon::now()
                ]);

                //PAQUETES
                DB::table("paquetes")->insert([
                    "id" => 1,
                    "id_franquicia" => $idFranquicia,
                    "nombre" => "LECTURA",
                    "precio" => "900",
                    "created_at" => Carbon::now()
                ]);

                DB::table("paquetes")->insert([
                    "id" => 2,
                    "id_franquicia" => $idFranquicia,
                    "nombre" => "PROTECCION",
                    "precio" => "1490",
                    "created_at" => Carbon::now()
                ]);

                DB::table("paquetes")->insert([
                    "id" => 3,
                    "id_franquicia" => $idFranquicia,
                    "nombre" => "ECO JR",
                    "precio" => "1490",
                    "created_at" => Carbon::now()
                ]);

                DB::table("paquetes")->insert([
                    "id" => 4,
                    "id_franquicia" => $idFranquicia,
                    "nombre" => "JR",
                    "precio" => "1790",
                    "created_at" => Carbon::now()
                ]);

                DB::table("paquetes")->insert([
                    "id" => 5,
                    "id_franquicia" => $idFranquicia,
                    "nombre" => "DORADO 1",
                    "precio" => "1890",
                    "created_at" => Carbon::now()
                ]);

                DB::table("paquetes")->insert([
                    "id" => 6,
                    "id_franquicia" => $idFranquicia,
                    "nombre" => "DORADO 2",
                    "precio" => "2290",
                    "created_at" => Carbon::now()
                ]);

                DB::table("paquetes")->insert([
                    "id" => 7,
                    "id_franquicia" => $idFranquicia,
                    "nombre" => "PLATINO",
                    "precio" => "2590",
                    "created_at" => Carbon::now()
                ]);

                //PROMOCIONES

                DB::table("promocion")->insert([
                    "id_franquicia" => $idFranquicia,
                    "titulo" => "3 x 2 CONVENIO",
                    "precioP" => "100",
                    "inicio" => new DateTime('first day of January'),
                    "fin" => new DateTime('last day of December'),
                    "status" => "1",
                    "created_at" => Carbon::now(),
                    "id_tipopromocionusuario" => 0,
                    "armazones" => 3
                ]);

                DB::table("promocion")->insert([
                    "id_franquicia" => $idFranquicia,
                    "titulo" => "2 POR 1 Y MEDIO CONVENIO",
                    "precioP" => "50",
                    "inicio" => new DateTime('first day of January'),
                    "fin" => new DateTime('last day of December'),
                    "status" => "1",
                    "created_at" => Carbon::now(),
                    "id_tipopromocionusuario" => 0,
                    "armazones" => 2
                ]);

                DB::table("promocion")->insert([
                    "id_franquicia" => $idFranquicia,
                    "titulo" => "ARMAZON PROPIO ( PAQ. ECO JR | JR | DORADO 1 | PLATINO )",
                    "precioP" => NULL,
                    "inicio" => new DateTime('first day of January'),
                    "fin" => new DateTime('last day of December'),
                    "status" => "1",
                    "created_at" => Carbon::now(),
                    "id_tipopromocionusuario" => 0,
                    "armazones" => 1,
                    "preciouno" => 300
                ]);

                DB::table("promocion")->insert([
                    "id_franquicia" => $idFranquicia,
                    "titulo" => "CLIENTE FRECUENTE",
                    "precioP" => NULL,
                    "inicio" => new DateTime('first day of January'),
                    "fin" => new DateTime('last day of December'),
                    "status" => "1",
                    "created_at" => Carbon::now(),
                    "id_tipopromocionusuario" => 0,
                    "armazones" => 1,
                    "preciouno" => 300
                ]);

                //ZONAS

                DB::table("zonas")->insert([
                    "id_franquicia" => $idFranquicia,
                    "zona" => "1",
                    "created_at" => Carbon::now()
                ]);

                DB::table('configfranquicia')->insert([
                    'id_franquicia' => $idFranquicia, 'estado' => $status
                ]);

                //Agregar franquicia a tabla llaves
                DB::table('llaves')->insert([
                    'id_franquicia' => $idFranquicia,
                    'llave' => null,
                    'tipo' => 0,
                    'created_at' => Carbon::now()
                ]);

                DB::table('llaves')->insert([
                    'id_franquicia' => $idFranquicia,
                    'llave' => null,
                    'tipo' => 1,
                    'created_at' => Carbon::now()
                ]);

                //Insertar registros para abonominimofranquicia
                //Abono minimo semanal - 200
                DB::table('abonominimofranquicia')->insert([
                    'id_franquicia' => $idFranquicia,
                    'pago' => 1,
                    'abonominimo' => 200,
                    'created_at' => Carbon::now()
                ]);
                //Abono minimo quincenal - 400
                DB::table('abonominimofranquicia')->insert([
                    'id_franquicia' => $idFranquicia,
                    'pago' => 2,
                    'abonominimo' => 400,
                    'created_at' => Carbon::now()
                ]);
                //Abono minimo mensual - 600
                DB::table('abonominimofranquicia')->insert([
                    'id_franquicia' => $idFranquicia,
                    'pago' => 4,
                    'abonominimo' => 600,
                    'created_at' => Carbon::now()
                ]);

                //Insertar registros para comisiones de ventas
                //Comision 1 Asistente - 13 contratos $80
                DB::table('comisionesventas')->insert([
                    'id_franquicia' => $idFranquicia,
                    'usuario' => 0,
                    'totalcontratos' => 13,
                    'comision' => 1,
                    'valor' => 80,
                    'created_at' => Carbon::now()
                ]);
                //Comision 2 Asistente - 20 contratos $120
                DB::table('comisionesventas')->insert([
                    'id_franquicia' => $idFranquicia,
                    'usuario' => 0,
                    'totalcontratos' => 20,
                    'comision' => 2,
                    'valor' => 120,
                    'created_at' => Carbon::now()
                ]);
                //Comision 1 Optometrista - 30 contratos 4%
                DB::table('comisionesventas')->insert([
                    'id_franquicia' => $idFranquicia,
                    'usuario' => 1,
                    'totalcontratos' => 30,
                    'comision' => 1,
                    'valor' => 4,
                    'created_at' => Carbon::now()
                ]);
                //Comision 2 Optometrista - 40 contratos 5%
                DB::table('comisionesventas')->insert([
                    'id_franquicia' => $idFranquicia,
                    'usuario' => 1,
                    'totalcontratos' => 40,
                    'comision' => 2,
                    'valor' => 5,
                    'created_at' => Carbon::now()
                ]);
                //Accion bandera asistencia franquicia
                DB::table('accionesbanderasfranquicia')->insert([
                    'id_franquicia' => $idFranquicia,
                    'tipo' => 0,
                    'estatus' => 1,
                    'created_at' => Carbon::now()
                ]);

                //Registrar movimiento sucursal
                self::insertarHistorialSucursal($idFranquicia, Auth::user()->id, "Creo sucursal con identificador: '" . $idFranquicia . "' en ciudad: '" . $ciudad . "'", "0", "0");

                if ($status = 1) {
                    return redirect()->route('listafranquicia')->with('bien', 'La sucursal se creo correctamente.')->with('fcreada', 'Bien!');
                }
                return redirect()->route('listafranquicia')->with('alerta', 'La sucursal se creo, pero aun no se encuentra activa.')->with('fcreada', 'Bien!');
            } catch (\Exception $e) {
                \Log::error('Error: ' . $e->getMessage());
                return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina.')->with('fcreada');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }//crearFranquicia

    public function tablaFranquicia()
    {
        $contratosGlobal = new contratosGlobal;
        if (Auth::check() && (Auth::user()->rol_id) == 7) //ROL DEL DIRECTOR
        {

            $franquicias = DB::select("SELECT fra.id as ID,fra.estado as ESTADO,fra.ciudad as CIUDAD,cofra.estado AS ESTATUS
                                FROM franquicias fra
                                INNER JOIN  configfranquicia cofra
                                ON fra.id = cofra.id_franquicia
                                ORDER BY fra.created_at ASC");
            return view('administracion.franquicia.tabla', ['franquicias' => $franquicias]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function editarFranquicia($id)
    {
        if (Auth::check() && (Auth::user()->rol_id) == 7) //ROL DEL DIRECTOR
        {
            $franquicia = DB::select("SELECT * FROM franquicias WHERE id = '$id'");
            $estado = DB::select("SELECT estado FROM configfranquicia WHERE id_franquicia = '$id'");

            if ($franquicia == null) {
                return redirect()->route('listafranquicia');
            }

            try {

                //Obtenemos clave publicable
                $clavepublicablestripe = DB::select("SELECT llave FROM llaves WHERE id_franquicia = '$id' AND tipo = 0");
                if($clavepublicablestripe != null) {
                    if(strlen($clavepublicablestripe[0]->llave) > 0) {
                        //Tiene algo la llave
                        $franquicia[0]->clavepublicablestripe = Crypt::decryptString($clavepublicablestripe[0]->llave);
                    }else {
                        //Esta null la llave
                        $franquicia[0]->clavepublicablestripe = $clavepublicablestripe[0]->llave;
                    }
                }

                //Obtenemos clave secreta
                $clavesecretastripe = DB::select("SELECT llave FROM llaves WHERE id_franquicia = '$id' AND tipo = 1");
                if($clavesecretastripe != null) {
                    if(strlen($clavesecretastripe[0]->llave) > 0) {
                        //Tiene algo la llave
                        $franquicia[0]->clavesecretastripe = Crypt::decryptString($clavesecretastripe[0]->llave);
                    }else {
                        //Esta null la llave
                        $franquicia[0]->clavesecretastripe = $clavesecretastripe[0]->llave;
                    }
                }

            } catch (DecryptException $e) {
                return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina.');
            }

            return view('administracion.franquicia.editar', ['franquicia' => $franquicia, 'estado' => $estado]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarFranquicia($id, Request $request)
    {
        if (Auth::check() && (Auth::user()->rol_id) == 7) //ROL DEL DIRECTOR
        {
            request()->validate([
                'foto' => 'nullable|image|mimes:jpg',
                'curp' => 'nullable|file|mimes:pdf',
                'rfc' => 'nullable|file|mimes:pdf',
                'hacienda' => 'nullable|file|mimes:pdf',
                'actanacimiento' => 'nullable|file|mimes:pdf',
                'identificacion' => 'nullable|file|mimes:pdf',
                'estado' => 'max:30',
                'ciudad' => 'max:255',
                'colonia' => 'max:255',
                'calle' => 'required|max:255',
                'entrecalles' => 'required|max:255',
                'numero' => 'max:10',
                'comprobante' => 'nullable|file|mimes:pdf',
                'observaciones' => 'max:300',
                'telefonoatencionclientes' => 'required|string|min:10|max:13|regex:/^[0-9\-]+$/',
                'whatsapp' => 'required|string|min:10|max:13|regex:/^[0-9\-]+$/',
                'coordenadas' => 'nullable|regex:/^[-]?\d+[\.]?\d*, [-]?\d+[\.]?\d*$/'
            ]);

            $clavepublicablestripe = $request->input('claveP');
            $clavesecretastripe = $request->input('claveS');

            $banderaActualizoFoto = false;
            $banderaActualizoCURP = false;
            $banderaActualizoRFC = false;
            $banderaActualizoHacienda = false;
            $banderaActualizoActaN = false;
            $banderaActualizoINE = false;
            $banderaActualizoComprobante = false;
            $id_UsuarioC = Auth::user()->id;

            if ($clavepublicablestripe != null || $clavesecretastripe != null) {
                if($clavepublicablestripe == null || $clavesecretastripe == null) {
                    //Alguna de las 2 no tiene nada
                    return back()->with('alerta', 'Es necesario agregar tanto la clave publicable como la clave secreta.');
                }else {
                    //Las 2 tienen algo
                    $clavepublicablestripe = Crypt::encryptString($clavepublicablestripe);
                    $clavesecretastripe = Crypt::encryptString($clavesecretastripe);
                }
            }

            try {

                $franquicia = DB::select("SELECT * FROM franquicias WHERE id='$id'");

                $foto = "";
                $fotoBool = false;
                if (strlen($franquicia[0]->foto) > 0) {
                    if (request()->hasFile('foto')) {
                        Storage::disk('disco')->delete($franquicia[0]->foto);
                        $fotoBruta = 'Foto-Franquicia-' . $franquicia[0]->id . '-' . time() . '.' . request()->file('foto')->getClientOriginalExtension();
                        $foto = request()->file('foto')->storeAs('uploads/imagenes/franquicia/fotos', $fotoBruta, 'disco');
                        $fotoBool = true;
                        $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotos/' . $fotoBruta)->height();
                        $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotos/' . $fotoBruta)->width();
                        if ($alto > $ancho) {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotos/' . $fotoBruta)->resize(600, 800);
                        } else {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotos/' . $fotoBruta)->resize(800, 600);
                        }
                        $imagenfoto->save();
                        $banderaActualizoFoto = true;
                    } else {
                        $foto = $franquicia[0]->foto;
                        $fotoBool = true;
                    }
                } else {
                    if (request()->hasFile('foto')) {
                        $fotoBruta = 'Foto-Franquicia-' . $franquicia[0]->id . '-' . time() . '.' . request()->file('foto')->getClientOriginalExtension();
                        $foto = request()->file('foto')->storeAs('uploads/imagenes/franquicia/fotos', $fotoBruta, 'disco');
                        $fotoBool = true;
                        $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotos/' . $fotoBruta)->height();
                        $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotos/' . $fotoBruta)->width();
                        if ($alto > $ancho) {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotos/' . $fotoBruta)->resize(600, 800);
                        } else {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotos/' . $fotoBruta)->resize(800, 600);
                        }
                        $imagenfoto->save();
                        $banderaActualizoFoto = true;
                    } else {
                        $fotoBool = false;
                    }

                }

                $curp = "";
                $curpBool = false;
                if (strlen($franquicia[0]->curp) > 0) {
                    if (request()->hasFile('curp')) {
                        Storage::disk('disco')->delete($franquicia[0]->curp);
                        $curpBruto = 'Curp-Franquicia-' . $franquicia[0]->id . '-' . time() . '.' . request()->file('curp')->getClientOriginalExtension();
                        $curp = request()->file('curp')->storeAs('uploads/imagenes/franquicia/curp', $curpBruto, 'disco');
                        $curpBool = true;
                        $alto1 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/curp/' . $curpBruto)->height();
                        $ancho1 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/curp/' . $curpBruto)->width();
                        if ($alto1 > $ancho1) {
                            $imagencurp = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/curp/' . $curpBruto)->resize(600, 800);
                        } else {
                            $imagencurp = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/curp/' . $curpBruto)->resize(800, 600);
                        }
                        $imagencurp->save();
                        $banderaActualizoCURP = true;
                    } else {
                        $curp = $franquicia[0]->curp;
                        $curpBool = true;
                    }
                } else {
                    if (request()->hasFile('curp')) {
                        $curpBruto = 'Curp-Franquicia-' . $franquicia[0]->id . '-' . time() . '.' . request()->file('curp')->getClientOriginalExtension();
                        $curp = request()->file('curp')->storeAs('uploads/imagenes/franquicia/curp', $curpBruto, 'disco');
                        $curpBool = true;
                        $alto1 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/curp/' . $curpBruto)->height();
                        $ancho1 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/curp/' . $curpBruto)->width();
                        if ($alto1 > $ancho1) {
                            $imagencurp = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/curp/' . $curpBruto)->resize(600, 800);
                        } else {
                            $imagencurp = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/curp/' . $curpBruto)->resize(800, 600);
                        }
                        $imagencurp->save();
                        $banderaActualizoCURP = true;
                    } else {
                        $curpBool = false;
                    }
                }

                $rfc = "";
                $rfcBool = false;
                if (strlen($franquicia[0]->rfc) > 0) {
                    if (request()->hasFile('rfc')) {
                        Storage::disk('disco')->delete($franquicia[0]->rfc);
                        $rfcBruto = 'Rfc-Franquicia-' . $franquicia[0]->id . '-' . time() . '.' . request()->file('rfc')->getClientOriginalExtension();
                        $rfc = request()->file('rfc')->storeAs('uploads/imagenes/franquicia/rfc', $rfcBruto, 'disco');
                        $rfcBool = true;
                        $alto2 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/rfc/' . $rfcBruto)->height();
                        $ancho2 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/rfc/' . $rfcBruto)->width();
                        if ($alto2 > $ancho2) {
                            $imagenrfc = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/rfc/' . $rfcBruto)->resize(600, 800);
                        } else {
                            $imagenrfc = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/rfc/' . $rfcBruto)->resize(800, 600);
                        }
                        $imagenrfc->save();
                        $banderaActualizoRFC = true;
                    } else {
                        $rfc = $franquicia[0]->rfc;
                        $rfcBool = true;
                    }
                } else {
                    if (request()->hasFile('rfc')) {
                        $rfcBruto = 'Rfc-Franquicia-' . $franquicia[0]->id . '-' . time() . '.' . request()->file('rfc')->getClientOriginalExtension();
                        $rfc = request()->file('rfc')->storeAs('uploads/imagenes/franquicia/rfc', $rfcBruto, 'disco');
                        $rfcBool = true;
                        $alto2 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/rfc/' . $rfcBruto)->height();
                        $ancho2 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/rfc/' . $rfcBruto)->width();
                        if ($alto2 > $ancho2) {
                            $imagenrfc = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/rfc/' . $rfcBruto)->resize(600, 800);
                        } else {
                            $imagenrfc = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/rfc/' . $rfcBruto)->resize(800, 600);
                        }
                        $imagenrfc->save();
                        $banderaActualizoCURP = true;
                    } else {
                        $rfcBool = false;
                    }
                }

                $hacienda = "";
                $haciendaBool = false;
                if (strlen($franquicia[0]->hacienda) > 0) {
                    if (request()->hasFile('hacienda')) {
                        Storage::disk('disco')->delete($franquicia[0]->hacienda);
                        $haciendaBruta = 'Hacienda-Franquicia-' . $franquicia[0]->id . '-' . time() . '.' . request()->file('hacienda')->getClientOriginalExtension();
                        $hacienda = request()->file('hacienda')->storeAs('uploads/imagenes/franquicia/hacienda', $haciendaBruta, 'disco');
                        $haciendaBool = true;
                        $alto3 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/hacienda/' . $haciendaBruta)->height();
                        $ancho3 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/hacienda/' . $haciendaBruta)->width();
                        if ($alto3 > $ancho3) {
                            $imagenhacienda = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/hacienda/' . $haciendaBruta)->resize(600, 800);
                        } else {
                            $imagenhacienda = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/hacienda/' . $haciendaBruta)->resize(800, 600);
                        }
                        $imagenhacienda->save();
                        $banderaActualizoHacienda = true;
                    } else {
                        $hacienda = $franquicia[0]->hacienda;
                        $haciendaBool = true;
                    }
                } else {
                    if (request()->hasFile('hacienda')) {
                        $haciendaBruta = 'Hacienda-Franquicia-' . $franquicia[0]->id . '-' . time() . '.' . request()->file('hacienda')->getClientOriginalExtension();
                        $hacienda = request()->file('hacienda')->storeAs('uploads/imagenes/franquicia/hacienda', $haciendaBruta, 'disco');
                        $haciendaBool = true;
                        $alto3 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/hacienda/' . $haciendaBruta)->height();
                        $ancho3 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/hacienda/' . $haciendaBruta)->width();
                        if ($alto3 > $ancho3) {
                            $imagenhacienda = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/hacienda/' . $haciendaBruta)->resize(600, 800);
                        } else {
                            $imagenhacienda = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/hacienda/' . $haciendaBruta)->resize(800, 600);
                        }
                        $imagenhacienda->save();
                        $banderaActualizoHacienda = true;
                    } else {
                        $haciendaBool = false;
                    }
                }

                $actanacimiento = "";
                $actanacimientoBool = false;
                if (strlen($franquicia[0]->actanacimiento) > 0) {
                    if (request()->hasFile('actanacimiento')) {
                        Storage::disk('disco')->delete($franquicia[0]->actanacimiento);
                        $actanacimientoBruta = 'Actanacimiento-Franquicia-' . $franquicia[0]->id . '-' . time() . '.' . request()->file('actanacimiento')->getClientOriginalExtension();
                        $actanacimiento = request()->file('actanacimiento')->storeAs('uploads/imagenes/franquicia/actanacimiento', $actanacimientoBruta, 'disco');
                        $actanacimientoBool = true;
                        $alto4 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/actanacimiento/' . $actanacimientoBruta)->height();
                        $ancho4 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/actanacimiento/' . $actanacimientoBruta)->width();
                        if ($alto4 > $ancho4) {
                            $imagenactanacimientop = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/actanacimiento/' . $actanacimientoBruta)->resize(600, 800);
                        } else {
                            $imagenactanacimiento = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/actanacimiento/' . $actanacimientoBruta)->resize(800, 600);
                        }
                        $imagenactanacimiento->save();
                        $banderaActualizoActaN = true;
                    } else {
                        $actanacimiento = $franquicia[0]->actanacimiento;
                        $actanacimientoBool = true;
                    }
                } else {
                    if (request()->hasFile('actanacimiento')) {
                        $actanacimientoBruta = 'Actanacimiento-Franquicia-' . $franquicia[0]->id . '-' . time() . '.' . request()->file('actanacimiento')->getClientOriginalExtension();
                        $actanacimiento = request()->file('actanacimiento')->storeAs('uploads/imagenes/franquicia/actanacimiento', $actanacimientoBruta, 'disco');
                        $actanacimientoBool = true;
                        $alto4 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/actanacimiento/' . $actanacimientoBruta)->height();
                        $ancho4 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/actanacimiento/' . $actanacimientoBruta)->width();
                        if ($alto4 > $ancho4) {
                            $imagenactanacimientop = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/actanacimiento/' . $actanacimientoBruta)->resize(600, 800);
                        } else {
                            $imagenactanacimiento = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/actanacimiento/' . $actanacimientoBruta)->resize(800, 600);
                        }
                        $imagenactanacimiento->save();
                        $banderaActualizoActaN = true;
                    } else {
                        $actanacimientoBool = false;
                    }
                }

                $identificacion = "";
                $identificacionBool = false;
                if (strlen($franquicia[0]->identificacion) > 0) {
                    if (request()->hasFile('identificacion')) {
                        Storage::disk('disco')->delete($franquicia[0]->identificacion);
                        $identificacionBruta = 'Identificacion-Franquicia-' . $franquicia[0]->id . '-' . time() . '.' . request()->file('identificacion')->getClientOriginalExtension();
                        $identificacion = request()->file('identificacion')->storeAs('uploads/imagenes/franquicia/identificacion', $identificacionBruta, 'disco');
                        $identificacionBool = true;
                        $alto5 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/identificacion/' . $identificacionBruta)->height();
                        $ancho5 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/identificacion/' . $identificacionBruta)->width();
                        if ($alto5 > $ancho5) {
                            $imagenidentificacion = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/identificacion/' . $identificacionBruta)->resize(600, 800);
                        } else {
                            $imagenidentificacion = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/identificacion/' . $identificacionBruta)->resize(800, 600);
                        }
                        $imagenidentificacion->save();
                        $banderaActualizoINE = true;
                    } else {
                        $identificacion = $franquicia[0]->identificacion;
                        $identificacionBool = true;
                    }
                } else {
                    if (request()->hasFile('identificacion')) {
                        $identificacionBruta = 'Identificacion-Franquicia-' . $franquicia[0]->id . '-' . time() . '.' . request()->file('identificacion')->getClientOriginalExtension();
                        $identificacion = request()->file('identificacion')->storeAs('uploads/imagenes/franquicia/identificacion', $identificacionBruta, 'disco');
                        $identificacionBool = true;
                        $alto5 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/identificacion/' . $identificacionBruta)->height();
                        $ancho5 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/identificacion/' . $identificacionBruta)->width();
                        if ($alto5 > $ancho5) {
                            $imagenidentificacion = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/identificacion/' . $identificacionBruta)->resize(600, 800);
                        } else {
                            $imagenidentificacion = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/identificacion/' . $identificacionBruta)->resize(800, 600);
                        }
                        $imagenidentificacion->save();
                        $banderaActualizoINE = true;
                    } else {
                        $haciendaBool = false;
                    }
                }

                $estado = $franquicia[0]->estado;
                $estadoBool = false;
                if (strlen($estado) > 0) {
                    if (strlen(request('estado')) > 0) {
                        $estado = request('estado');
                    }
                    $estadoBool = true;
                } else {
                    if (strlen(request('estado')) > 0) {
                        $estado = request('estado');
                        $estadoBool = true;
                    }
                }


                $ciudad = $franquicia[0]->ciudad;
                $ciudadBool = false;
                if (strlen($ciudad) > 0) {
                    if (strlen(request('ciudad')) > 0) {
                        $ciudad = request('ciudad');
                    }
                    $ciudadBool = true;
                } else {
                    if (strlen(request('ciudad')) > 0) {
                        $ciudad = request('ciudad');
                        $ciudadBool = true;
                    }
                }

                $colonia = $franquicia[0]->colonia;
                $coloniaBool = false;
                if (strlen($colonia) > 0) {
                    if (strlen(request('colonia')) > 0) {
                        $colonia = request('colonia');
                    }
                    $coloniaBool = true;
                } else {
                    if (strlen(request('colonia')) > 0) {
                        $colonia = request('colonia');
                        $coloniaBool = true;
                    }
                }

                $numero = $franquicia[0]->numero;
                $numeroBool = false;
                if (strlen($numero) > 0) {
                    if (strlen(request('numero')) > 0) {
                        $numero = request('numero');
                    }
                    $numeroBool = true;
                } else {
                    if (strlen(request('numero')) > 0) {
                        $numero = request('numero');
                        $numeroBool = true;
                    }
                }

                $comprobante = "";
                $comprobanteBool = false;
                if (strlen($franquicia[0]->comprobante) > 0) {
                    if (request()->hasFile('comprobante')) {
                        Storage::disk('disco')->delete($franquicia[0]->comprobante);
                        $comprobanteBruto = 'Comprobante-Franquicia-' . $franquicia[0]->id . '-' . time() . '.' . request()->file('comprobante')->getClientOriginalExtension();
                        $comprobante = request()->file('comprobante')->storeAs('uploads/imagenes/franquicia/comprobante', $comprobanteBruto, 'disco');
                        $comprobanteBool = true;
                        $alto6 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/comprobante/' . $comprobanteBruto)->height();
                        $ancho6 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/comprobante/' . $comprobanteBruto)->width();
                        if ($alto6 > $ancho6) {
                            $imagencomprobante = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/comprobante/' . $comprobanteBruto)->resize(600, 800);
                        } else {
                            $imagencomprobante = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/comprobante/' . $comprobanteBruto)->resize(800, 600);
                        }
                        $imagencomprobante->save();
                        $banderaActualizoComprobante = true;
                    } else {
                        $comprobante = $franquicia[0]->comprobante;
                        $comprobanteBool = true;
                    }
                } else {
                    if (request()->hasFile('comprobante')) {
                        $comprobanteBruto = 'Comprobante-Franquicia-' . $franquicia[0]->id . '-' . time() . '.' . request()->file('comprobante')->getClientOriginalExtension();
                        $comprobante = request()->file('comprobante')->storeAs('uploads/imagenes/franquicia/comprobante', $comprobanteBruto, 'disco');
                        $comprobanteBool = true;
                        $alto6 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/comprobante/' . $comprobanteBruto)->height();
                        $ancho6 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/comprobante/' . $comprobanteBruto)->width();
                        if ($alto6 > $ancho6) {
                            $imagencomprobante = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/comprobante/' . $comprobanteBruto)->resize(600, 800);
                        } else {
                            $imagencomprobante = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/comprobante/' . $comprobanteBruto)->resize(800, 600);
                        }
                        $imagencomprobante->save();
                        $banderaActualizoComprobante = true;
                    } else {
                        $comprobanteBool = false;
                    }
                }

                $observaciones = $franquicia[0]->observaciones;
                $observacionesBool = false;
                if (strlen($observaciones) > 0) {
                    if (strlen(request('observaciones')) > 0) {
                        $observaciones = request('observaciones');
                    }
                    $observacionesBool = true;
                } else {
                    if (strlen(request('observaciones')) > 0) {
                        $observaciones = request('observaciones');
                        $observacionesBool = true;
                    }
                }

                $estadofranquicia = 1;
                if (strlen(request('activo')) == 0) {
                    $estadofranquicia = 0;
                }

                //Verificar cuales campos de texto fueron actualizados para la sucursal
                if($franquicia[0]->estado != request('estado')){
                    //Actualizo estado en el que se ubica la sucursal
                    $cambio = "Actualizo estado de '" . $franquicia[0]->estado . "' a '" . request('estado') . "' para sucursal: " . $ciudad;
                    self::insertarHistorialSucursal($id, $id_UsuarioC, $cambio,"0","2");
                }if($franquicia[0]->ciudad != request('ciudad')){
                    //Actualizo ciudad
                    $cambio = "Actualizo ciudad de '" . $franquicia[0]->ciudad . "' a '" . request('ciudad') . "' para sucursal: " . $ciudad;
                    self::insertarHistorialSucursal($id, $id_UsuarioC, $cambio,"0","2");
                }if($franquicia[0]->colonia != request('colonia')){
                    //Actualizo colonia
                    $cambio = "Actualizo colonia de '" . $franquicia[0]->colonia . "' a '" . request('colonia') . "' para sucursal: " . $ciudad;
                    self::insertarHistorialSucursal($id, $id_UsuarioC, $cambio,"0","2");
                }if($franquicia[0]->numero != request('numero')){
                    //Actualizo numero
                    $cambio = "Actualizo numero de '" . $franquicia[0]->numero . "' a '" . request('numero') . "' para sucursal: " . $ciudad;
                    self::insertarHistorialSucursal($id, $id_UsuarioC, $cambio,"0","2");
                }if($franquicia[0]->observaciones != request('observaciones')){
                    //Actualizo observaciones
                    $cambio = "Actualizo observaciones para sucursal: " . $ciudad;
                    self::insertarHistorialSucursal($id, $id_UsuarioC, $cambio,"0","2");
                }

                //Cuantos Documentos se actualizaros?
                $cambio = "Actualizo:";
                if($banderaActualizoFoto){
                    //Actualizo Foto
                    $cambio = $cambio . " Foto,";

                }if($banderaActualizoCURP){
                    //Actualizo CURP
                    $cambio = $cambio . " CURP,";

                }if($banderaActualizoRFC){
                    //Actualizo RFC
                    $cambio = $cambio . " RFC,";

                }if($banderaActualizoHacienda){
                    //Actualizo Hacienda
                    $cambio = $cambio . " Hacienda,";

                }if($banderaActualizoActaN){
                    //Actualizo acta de nacimiento
                    $cambio = $cambio . " Acta de nacimiento,";

                }if($banderaActualizoINE){
                    //Actualizo INE
                    $cambio = $cambio . " INE,";

                }if($banderaActualizoComprobante){
                    //Actualizo Comprobante de domicilio
                    $cambio = $cambio . " Comprobante de domicilio,";
                }
                $cambio = trim($cambio,",");

                if($banderaActualizoFoto || $banderaActualizoCURP || $banderaActualizoCURP || $banderaActualizoHacienda || $banderaActualizoINE
                    || $banderaActualizoActaN || $banderaActualizoComprobante){
                    //Registrar movimientos de los documentos que se actualizaron pertenecientes a la sucursal
                    self::insertarHistorialSucursal($id, $id_UsuarioC, $cambio, "0", "2");
                }


                DB::table('franquicias')->where('id', $franquicia[0]->id)->update([
                    'actualizadopor' => strtoupper(Auth::user()->name), 'foto' => $foto, 'curp' => $curp, 'rfc' => $rfc, 'hacienda' => $hacienda, 'actanacimiento' => $actanacimiento,
                    'identificacion' => $identificacion, 'estado' => strtoupper($estado), 'ciudad' => strtoupper($ciudad), 'colonia' => strtoupper($colonia),
                    'calle' => strtoupper(request('calle')), 'entrecalles' => strtoupper(request('entrecalles')), 'numero' => strtoupper($numero),
                    'comprobante' => $comprobante, 'observaciones' => strtoupper($observaciones), 'telefonofranquicia' => request('telefonofranquicia'), 'updated_at' => Carbon::now(),
                    'telefonoatencionclientes' => request('telefonoatencionclientes'), 'whatsapp' => request('whatsapp'), 'coordenadas' => request('coordenadas')
                ]);

                if ($fotoBool && $curpBool && $rfcBool && $haciendaBool && $actanacimientoBool && $identificacionBool && $estadoBool && $ciudadBool && $coloniaBool && $numeroBool
                    && $comprobanteBool && $observacionesBool) {
                    $status = 1;
                } else {
                    $status = 2;
                }

                if ((Auth::user()->rol_id) == 7) {
                    DB::table('configfranquicia')->where('id_franquicia', $franquicia[0]->id)->update(['estado' => $estadofranquicia]);
                } else {
                    DB::table('configfranquicia')->where('id_franquicia', $franquicia[0]->id)->update([
                        'estado' => $status
                    ]);
                }

                //Actualizar clavepublicable y clavesecreta en la tabla llaves
                DB::table('llaves')->where([['id_franquicia', '=', $franquicia[0]->id], ['tipo', '=', 0]])->update([
                    'llave' => $clavepublicablestripe
                ]);
                DB::table('llaves')->where([['id_franquicia', '=', $franquicia[0]->id], ['tipo', '=', 1]])->update([
                    'llave' => $clavesecretastripe
                ]);

                if ($status == 1 || $estadofranquicia = 1) {
                    return redirect()->route('listafranquicia')->with('bien', 'La sucursal se actualizo correctamente.')->with('fcreada', 'Bien!');
                }
                return redirect()->route('listafranquicia')->with('alerta', ' La sucursal se actualizo, pero no se encuentra activa.')->with('fcreada', 'Alerta!');

            } catch (\Exception $e) {
                \Log::error('Error: ' . $e->getMessage());
                return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function usuariosfranquicia($idFranquicia, Request $request)
    {
        if (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) //ROL DEL DIRECTOR
        {
            $ahora = Carbon::now();

            if(Auth::user()->rol_id == 7) {
                //Director
                if($idFranquicia == '00000') {
                    //Franquicia de prueba
                    $usuariosfranquicia = DB::select("SELECT u.id as ID, u.foto AS FOTO, u.name AS NOMBRE,u.email AS CORREO, r.rol AS ROL ,u.renovacion, u.codigoasistencia AS NOCONTROL,
                                                                   uf.id_franquicia as ID_FRANQUICIA, (SELECT ciudad FROM franquicias WHERE id = uf.id_franquicia) as CIUDADFRANQUICIA,
                                                                   u.supervisorcobranza as SUPERVISORCOBRANZA, u.created_at AS FECHACREACION,
                                                                   STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d') AS FECHACUMPLEANIOS,
                                                                   u.actanacimiento AS ACTANACIMIENTO, u.identificacion AS INE, u.curp AS CURP, u.segurosocial AS SEGURO,
                                                                   u.solicitud AS CV, u.tarjetapago AS TARJETA, u.otratarjetapago AS OTRATARJETA, u.contactoemergencia AS CONTACTO,
                                                                   u.contratolaboral AS CONTRATO, u.pagare AS PAGARE, (SELECT z.zona FROM zonas z WHERE z.id = u.id_zona) as ZONA,
                                                                   DATEDIFF(STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d'), '$ahora') AS DIASPARACUMPLEANIOS,
                                                                   u.ultimaconexion AS ULTIMACONEXION
                                                                   FROM users u INNER JOIN roles r ON r.id = u.rol_id
                                                                   INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                                   ORDER BY u.name");

                    $totalRenovacion = DB::select("SELECT COUNT(u.id) AS total FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                            WHERE u.renovacion IS NOT NULL AND STR_TO_DATE(u.renovacion,'%Y-%m-%d') <= ' $ahora'");
                }else {
                    //Otra franquicia diferente a la de prueba
                    $usuariosfranquicia = DB::select("SELECT u.id as ID, u.foto AS FOTO, u.name AS NOMBRE,u.email AS CORREO, r.rol AS ROL ,u.renovacion, u.codigoasistencia AS NOCONTROL,
                                                                   uf.id_franquicia as ID_FRANQUICIA, (SELECT ciudad FROM franquicias WHERE id = uf.id_franquicia) as CIUDADFRANQUICIA,
                                                                   u.supervisorcobranza as SUPERVISORCOBRANZA,u.created_at AS FECHACREACION,
                                                                   STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d') AS FECHACUMPLEANIOS,
                                                                   u.actanacimiento AS ACTANACIMIENTO, u.identificacion AS INE, u.curp AS CURP, u.segurosocial AS SEGURO,
                                                                   u.solicitud AS CV, u.tarjetapago AS TARJETA, u.otratarjetapago AS OTRATARJETA, u.contactoemergencia AS CONTACTO,
                                                                   u.contratolaboral AS CONTRATO, u.pagare AS PAGARE, (SELECT z.zona FROM zonas z WHERE z.id = u.id_zona) as ZONA,
                                                                   DATEDIFF(STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d'), '$ahora') AS DIASPARACUMPLEANIOS,
                                                                   u.ultimaconexion AS ULTIMACONEXION
                                                                   FROM users u INNER JOIN roles r ON r.id = u.rol_id
                                                                   INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                                   WHERE uf.id_franquicia != '00000' ORDER BY CIUDADFRANQUICIA, u.name");

                    $totalRenovacion = DB::select("SELECT COUNT(u.id) AS total FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id WHERE u.renovacion IS NOT NULL
                                                            AND  uf.id_franquicia != '00000' AND  STR_TO_DATE(u.renovacion,'%Y-%m-%d') <= ' $ahora'");
                }
            }else {
                //Adiministrador o principal
                $usuariosfranquicia = DB::select("SELECT u.id as ID, u.foto AS FOTO, u.name AS NOMBRE,u.email AS CORREO, r.rol AS ROL ,u.renovacion, u.codigoasistencia AS NOCONTROL,
                                                              uf.id_franquicia as ID_FRANQUICIA, (SELECT ciudad FROM franquicias WHERE id = uf.id_franquicia) as CIUDADFRANQUICIA,
                                                              u.supervisorcobranza as SUPERVISORCOBRANZA, u.created_at AS FECHACREACION,
                                                              STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d') AS FECHACUMPLEANIOS,
                                                              u.actanacimiento AS ACTANACIMIENTO, u.identificacion AS INE, u.curp AS CURP, u.segurosocial AS SEGURO,
                                                              u.solicitud AS CV, u.tarjetapago AS TARJETA, u.otratarjetapago AS OTRATARJETA, u.contactoemergencia AS CONTACTO,
                                                              u.contratolaboral AS CONTRATO, u.pagare AS PAGARE, (SELECT z.zona FROM zonas z WHERE z.id = u.id_zona) as ZONA,
                                                              DATEDIFF(STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d'), '$ahora') AS DIASPARACUMPLEANIOS,
                                                              u.ultimaconexion AS ULTIMACONEXION
                                                              FROM users u INNER JOIN roles r ON r.id = u.rol_id
                                                              INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                              WHERE uf.id_franquicia = '$idFranquicia' ORDER BY u.name");

                $totalRenovacion = DB::select("SELECT COUNT(u.id) AS total FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id WHERE u.renovacion IS NOT NULL
                                                        AND  uf.id_franquicia= '$idFranquicia' AND  STR_TO_DATE(u.renovacion,'%Y-%m-%d') <= ' $ahora'");
            }

            $franquicia = DB::select("SELECT * FROM franquicias WHERE id='$idFranquicia'");
            $sucursales = DB::select("SELECT id,estado,ciudad,colonia,numero FROM franquicias WHERE id != '00000' ORDER BY ciudad ASC");
            $roles = DB::select("SELECT * FROM roles WHERE id <> 7");
            $zonas = DB::select("SELECT id,zona FROM zonas WHERE id_franquicia = '$idFranquicia' ORDER BY zona");

            return view('administracion.franquicia.usuarios', ['usuariosfranquicia' => $usuariosfranquicia,
                'franquicia' => $franquicia,
                'roles' => $roles,
                'id' => $idFranquicia,
                'zonas' => $zonas,
                'sucursales' => $sucursales,
                'idFranquicia' => $idFranquicia,
                'totalRenovacion' => $totalRenovacion
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function nuevousuariofranquicia($id, Request $request)
    {
        if (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) //ROL DEL DIRECTOR
        {

            $contratosGlobal = new contratosGlobal;
            $polizaGlobales = new polizaGlobales;

            if (!$request->has('usuarioP')) {
                $rol = request()->rol;
                $idsSucursales = array();
                $contador = 0;
                if ($rol == 4 || $rol == 12 || $rol == 13 || $rol == 14) {
                    if ($rol == 4) {
                        request()->validate([
                            'nombre' => 'required|string|min:5|max:255',
                            'correo' => 'email',
                            'contrasena' => ['required', 'string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
                            'ccontrasena' => 'required|same:contrasena',
                            'rol' => 'required',
                            'foto' => 'nullable|image|mimes:jpg|mimes:jpeg',
                            'actanacimiento' => 'nullable|file|mimes:pdf',
                            'identificacion' => 'nullable|file|mimes:pdf',
                            'curp' => 'nullable|image|mimes:jpg|mimes:jpeg',
                            'comprobante' => 'nullable|file|mimes:pdf',
                            'seguro' => 'nullable|file|mimes:pdf',
                            'solicitud' => 'nullable|file|mimes:pdf',
                            'tarjetapago' => 'nullable|image|mimes:jpg|mimes:jpeg',
                            'otratarjetapago' => 'nullable|image|mimes:jpg|mimes:jpeg',
                            'contratolaboral' => 'nullable|file|mimes:pdf',
                            'contactoemergencia' => 'nullable|file|mimes:pdf',
                            'idzona' => 'required',
                            'sueldo' => 'required|numeric|gt:0',
                            'pagare' => 'nullable|file|mimes:pdf',
                            'tarjeta' => 'required|string|min:16|max:20',
                            'otratarjeta' => 'required|string|min:16|max:20',
                            'fechanacimiento' => 'required'
                        ]);
                    } else {
                        request()->validate([
                            'nombre' => 'required|string|min:5|max:255',
                            'correo' => 'email',
                            'contrasena' => ['required', 'string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
                            'ccontrasena' => 'required|same:contrasena',
                            'rol' => 'required',
                            'foto' => 'nullable|image|mimes:jpg|mimes:jpeg',
                            'actanacimiento' => 'nullable|file|mimes:pdf',
                            'identificacion' => 'nullable|file|mimes:pdf',
                            'curp' => 'nullable|image|mimes:jpg|mimes:jpeg',
                            'comprobante' => 'nullable|file|mimes:pdf',
                            'seguro' => 'nullable|file|mimes:pdf',
                            'solicitud' => 'nullable|file|mimes:pdf',
                            'contratolaboral' => 'nullable|file|mimes:pdf',
                            'tarjetapago' => 'nullable|image|mimes:jpg|mimes:jpeg',
                            'otratarjetapago' => 'nullable|image|mimes:jpg|mimes:jpeg',
                            'contactoemergencia' => 'nullable|file|mimes:pdf',
                            'sueldo' => 'required|numeric|gt:0',
                            'pagare' => 'nullable|file|mimes:pdf',
                            'tarjeta' => 'required|string|min:16|max:20',
                            'otratarjeta' => 'required|string|min:16|max:20',
                            'fechanacimiento' => 'required'
                        ]);
                    }
                } else {
                    request()->validate([
                        'nombre' => 'required|string|min:5|max:255',
                        'correo' => 'email',
                        'contrasena' => ['required', 'string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
                        'ccontrasena' => 'required|same:contrasena',
                        'rol' => 'required',
                        'foto' => 'nullable|image|mimes:jpg|mimes:jpeg',
                        'actanacimiento' => 'nullable|file|mimes:pdf',
                        'identificacion' => 'nullable|file|mimes:pdf',
                        'curp' => 'nullable|image|mimes:jpg|mimes:jpeg',
                        'comprobante' => 'nullable|file|mimes:pdf',
                        'seguro' => 'nullable|file|mimes:pdf',
                        'solicitud' => 'nullable|file|mimes:pdf',
                        'tarjetapago' => 'nullable|image|mimes:jpg|mimes:jpeg',
                        'otratarjetapago' => 'nullable|image|mimes:jpg|mimes:jpeg',
                        'contratolaboral' => 'nullable|file|mimes:pdf',
                        'contactoemergencia' => 'nullable|file|mimes:pdf',
                        'pagare' => 'nullable|file|mimes:pdf',
                        'tarjeta' => 'required|string|min:16|max:20',
                        'otratarjeta' => 'required|string|min:16|max:20',
                        'fechanacimiento' => 'required'
                    ]);

                    if ($rol == 15) {
                        $franquicias = DB::select("SELECT id FROM franquicias");
                        foreach ($franquicias as $franquicia) {
                            $franquiciaEntrada = request("$franquicia->id");
                            if (!is_null($franquiciaEntrada) && $franquiciaEntrada > 0) {
                                array_push($idsSucursales, $franquicia->id);
                                $contador++;
                            }
                        }
                        if ($contador == 0) {
                            return back()->with('alerta', 'Debes seleccionar al menos una sucursal')->withInput($request->all());
                        }
                    }
                }

                $supervisorcobranza = 0;
                if ($rol == 4) {
                    //Rol a crear es cobranza
                    $cobradornormal = DB::select("SELECT u.id FROM users u
                                                  INNER JOIN usuariosfranquicia uf
                                                  ON u.id = uf.id_usuario WHERE u.id_zona = '" . request('idzona') . "' AND u.supervisorcobranza = '0'");

                    if ($cobradornormal != null) {
                        //Existe cobrador normal
                        $cbsupervisorcobranza = request('cbsupervisorcobranza');
                        if ($cbsupervisorcobranza == null) {
                            //No checkearon la casilla de supervisor cobranza
                            return back()->with('alerta', 'No puedes tener dos cobradores en la misma zona, en caso de que se requiera puedes asignar como supervisor.')->withInput($request->all());
                        }
                        $supervisorcobranza = 1;
                    }
                }

                $correo = request()->correo;
                $existeCorreo = DB::select("SELECT * FROM users WHERE email = '$correo'");
                if ($existeCorreo == null) {
                    try {

                        $total = 0;
                        $usuarios = DB::select("SHOW TABLE STATUS LIKE 'users'");
                        $siguienteId = $usuarios[0]->Auto_increment;


                        $archivosComprimir = [];
                        $foto = "";
                        if (request()->hasFile('foto')) {
                            $fotoBruta = 'Foto-Usuario-' . $siguienteId . '-' . time() . '.' . request()->file('foto')->getClientOriginalExtension();
                            $foto = request()->file('foto')->storeAs('uploads/imagenes/usuarios/foto', $fotoBruta, 'disco');
                            $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/foto/' . $fotoBruta)->height();
                            $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/foto/' . $fotoBruta)->width();
                            if ($alto > $ancho) {
                                $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/foto/' . $fotoBruta)->resize(600, 800);
                            } else {
                                $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/foto/' . $fotoBruta)->resize(800, 600);
                            }
                            $imagenfoto->save();
                        }

                        $actanacimiento = "";
                        if (request()->hasFile('actanacimiento')) {
                            $actanacimientoBruta = 'Actanacimiento-Usuario-' . $siguienteId . '-' . time() . '.' . request()->file('actanacimiento')->getClientOriginalExtension();
                            $actanacimiento = request()->file('actanacimiento')->storeAs('uploads/imagenes/usuarios', $actanacimientoBruta, 'disco');
                            array_push($archivosComprimir, $actanacimientoBruta);
                        }

                        $identificacion = "";
                        if (request()->hasFile('identificacion')) {
                            $identificacionBruta = 'Identificacion-Usuario-' . $siguienteId . '-' . time() . '.' . request()->file('identificacion')->getClientOriginalExtension();
                            $identificacion = request()->file('identificacion')->storeAs('uploads/imagenes/usuarios', $identificacionBruta, 'disco');
                            array_push($archivosComprimir, $identificacionBruta);

                        }

                        $curp = "";
                        if (request()->hasFile('curp')) {
                            $curpBruta = 'Curp-Usuario-' . $siguienteId . '-' . time() . '.' . request()->file('curp')->getClientOriginalExtension();
                            $curp = request()->file('curp')->storeAs('uploads/imagenes/usuarios', $curpBruta, 'disco');
                            $alto3 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $curpBruta)->height();
                            $ancho3 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $curpBruta)->width();
                            if ($alto3 > $ancho3) {
                                $imagencurp = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $curpBruta)->resize(600, 800);
                            } else {
                                $imagencurp = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $curpBruta)->resize(800, 600);
                            }
                            $imagencurp->save();
                            array_push($archivosComprimir, $curpBruta);
                        }

                        $comprobante = "";
                        if (request()->hasFile('comprobante')) {
                            $comprobanteBruta = 'Comprobante-Usuario-' . $siguienteId . '-' . time() . '.' . request()->file('comprobante')->getClientOriginalExtension();
                            $comprobante = request()->file('comprobante')->storeAs('uploads/imagenes/usuarios', $comprobanteBruta, 'disco');
                            array_push($archivosComprimir, $comprobanteBruta);

                        }

                        $seguro = "";
                        if (request()->hasFile('seguro')) {
                            $seguroBruta = 'Seguro-Usuario-' . $siguienteId . '-' . time() . '.' . request()->file('seguro')->getClientOriginalExtension();
                            $seguro = request()->file('seguro')->storeAs('uploads/imagenes/usuarios', $seguroBruta, 'disco');
                            array_push($archivosComprimir, $seguroBruta);

                        }

                        $solicitud = "";
                        if (request()->hasFile('solicitud')) {
                            $solicitudBruta = 'Solicitud-Usuario-' . $siguienteId . '-' . time() . '.' . request()->file('solicitud')->getClientOriginalExtension();
                            $solicitud = request()->file('solicitud')->storeAs('uploads/imagenes/usuarios', $solicitudBruta, 'disco');
                            array_push($archivosComprimir, $solicitudBruta);
                        }

                        $tarjetapago = "";
                        if (request()->hasFile('tarjetapago')) {
                            $tarjetapagoBruta = 'Tarjetapago-Usuario-' . $siguienteId . '-' . time() . '.' . request()->file('tarjetapago')->getClientOriginalExtension();
                            $tarjetapago = request()->file('tarjetapago')->storeAs('uploads/imagenes/usuarios', $tarjetapagoBruta, 'disco');
                            $alto7 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $tarjetapagoBruta)->height();
                            $ancho7 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $tarjetapagoBruta)->width();
                            if ($alto7 > $ancho7) {
                                $imagentarjetapago = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $tarjetapagoBruta)->resize(600, 800);
                            } else {
                                $imagentarjetapago = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $tarjetapagoBruta)->resize(800, 600);
                            }
                            $imagentarjetapago->save();
                            array_push($archivosComprimir, $tarjetapagoBruta);
                        }

                        $contratolaboral = "";
                        if (request()->hasFile('contratolaboral')) {
                            $contratolaboralBruta = 'Contratolaboral-Usuario-' . $siguienteId . '-' . time() . '.' . request()->file('contratolaboral')->getClientOriginalExtension();
                            $contratolaboral = request()->file('contratolaboral')->storeAs('uploads/imagenes/usuarios', $contratolaboralBruta, 'disco');
                            array_push($archivosComprimir, $contratolaboralBruta);
                        }

                        $contactoemergencia = "";
                        if (request()->hasFile('contactoemergencia')) {
                            $contactoemergenciaBruta = 'Contactoemergencia-Usuario-' . $siguienteId . '-' . time() . '.' . request()->file('contactoemergencia')->getClientOriginalExtension();
                            $contactoemergencia = request()->file('contactoemergencia')->storeAs('uploads/imagenes/usuarios', $contactoemergenciaBruta, 'disco');
                            array_push($archivosComprimir, $contactoemergenciaBruta);

                        }

                        $pagare = "";
                        if (request()->hasFile('pagare')) {
                            $pagareBruta = 'Pagare-Usuario-' . $siguienteId . '-' . time() . '.' . request()->file('pagare')->getClientOriginalExtension();
                            $pagare = request()->file('pagare')->storeAs('uploads/imagenes/usuarios', $pagareBruta, 'disco');
                            array_push($archivosComprimir, $pagareBruta);
                        }

                        $otratarjetapago = "";
                        if (request()->hasFile('otratarjetapago')) {
                            $otratarjetapagoBruta = 'Otratarjetapago-Usuario-' . $siguienteId . '-' . time() . '.' . request()->file('otratarjetapago')->getClientOriginalExtension();
                            $otratarjetapago = request()->file('otratarjetapago')->storeAs('uploads/imagenes/usuarios', $otratarjetapagoBruta, 'disco');
                            $alto9 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $otratarjetapagoBruta)->height();
                            $ancho9 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $otratarjetapagoBruta)->width();
                            if ($alto9 > $ancho9) {
                                $imagenotratarjetapago = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $otratarjetapagoBruta)->resize(600, 800);
                            } else {
                                $imagenotratarjetapago = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $otratarjetapagoBruta)->resize(800, 600);
                            }
                            $imagenotratarjetapago->save();
                            array_push($archivosComprimir, $otratarjetapagoBruta);
                        }

                        self::comprimirArchivosUsuarios($siguienteId, $archivosComprimir, 0);


                        $idZona = null;
                        if ($rol == 4) {
                            $idZona = request('idzona');
                        }

                        $fecharenovacion = request('fecharenovacion');
                        $hoy = Carbon::now();
                        if (!is_null($fecharenovacion)) {
                            try {
                                $fecharenovacion = Carbon::parse($fecharenovacion);
                                if ($fecharenovacion <= $hoy) {
                                    return back()->withErrors(['fecharenovacion' => 'Fecha no valida'])->withInput($request->all());
                                }

                            } catch (\Exception $e) {
                                return back()->withErrors(['fecharenovacion' => 'Fecha no valida'])->withInput($request->all());
                            }
                        }

                        $numControl = "";
                        try {
                            $fecha =  Carbon::parse(Carbon::now())->format("Y");
                            $year = substr($fecha, -2);
                            $identificadorFranquicia = self::obtenerIdentificadorFormatoFranquicia($id);
                            //Damos el formato al codigo
                            $codigo = $year . $identificadorFranquicia;
                            $ultimoNumeroControl = DB::select("SELECT codigoasistencia FROM users WHERE codigoasistencia IS NOT NULL AND codigoasistencia LIKE '$codigo%'
                                                                     ORDER BY id DESC LIMIT 1");
                            if ($ultimoNumeroControl!= null) {
                                \Log::info("ULTIMO NUMERO DE CONTROL:" . $ultimoNumeroControl[0]->codigoasistencia);
                                $numControl .= $ultimoNumeroControl[0]->codigoasistencia + 1;
                            }else{
                                $numControl = "$year" . self::obtenerIdentificadorFormatoFranquicia($id) . "000";
                            }
                            \Log::info("NUEVO NUMERO DE CONTROL:" . $numControl);
                        } catch (\Exception $e) {
                            \Log::info("Error:" . $e);
                        }
                        $idUsuario = User::create([
                            'name' => request()->nombre,
                            'email' => request()->correo,
                            'password' => Hash::make(request()->contrasena),
                            'rol_id' => request()->rol,
                            'foto' => $foto,
                            'actanacimiento' => $actanacimiento,
                            'identificacion' => $identificacion,
                            'curp' => $curp,
                            'codigoasistencia' => $numControl,
                            'comprobantedomicilio' => $comprobante,
                            'segurosocial' => $seguro,
                            'solicitud' => $solicitud,
                            'tarjetapago' => $tarjetapago,
                            'otratarjetapago' => $otratarjetapago,
                            'contratolaboral' => $contratolaboral,
                            'contactoemergencia' => $contactoemergencia,
                            'pagare' => $pagare,
                            'id_zona' => $idZona,
                            'sueldo' => request()->sueldo,
                            'renovacion' => $fecharenovacion,
                            'tarjeta' => request()->tarjeta,
                            'otratarjeta' => request()->otratarjeta,
                            'supervisorcobranza' => $supervisorcobranza,
                            'id_franquiciaprincipal' => $id,
                            'fechanacimiento' => request()->fechanacimiento,
                            'barcode' => self::generarbarcodealeatorio(),
                            'created_at' => $hoy
                        ]);

                        DB::table('usuariosfranquicia')->insert([
                            'id_usuario' => $idUsuario->id, 'id_franquicia' => $id, 'created_at' => Carbon::now()
                        ]);

                        DB::table('franquicias')->where('id', $id)->update([
                            'actualizadopor' => strtoupper(Auth::user()->name), 'updated_at' => Carbon::now()
                        ]);

                        if ($contador > 0 && $rol == 15) {
                            foreach ($idsSucursales as $idSucursal) {
                                DB::table("sucursalesconfirmaciones")->insert([
                                    "id_usuario" => $idUsuario->id, "id_franquicia" => $idSucursal
                                ]);
                            }
                        }

                        //Registrar el usuario en tabla controlentradasalidausuario
                        DB::table("controlentradasalidausuario")->insert([
                            "id_usuario" => $idUsuario->id,
                            "horaini" => "08:10:00", //Horario inicial por default
                            "horafin" => "08:20:00", //Horario final por default
                            "created_at" => Carbon::now()
                        ]);

                        //Validar si usuario tiene registro de asistencia con poliza del dia
                        $polizaGlobales::buscarasistenciapolizaeinsertar($id, $idUsuario->id);

                        //Guardar contratos en tabla contratostemporalessincronizacion
                        if($rol == 4) {
                            $contratosGlobal::insertarDatosTablaContratosTemporalesSincronizacion($id, $idUsuario->id, $idZona, $rol);
                            $contratosGlobal::eliminarEInsertarAbonosContratosTemporalesSincronizacionPorUsuarios($idUsuario->id);

                            if ($supervisorcobranza == 0) {
                                //Se creo cobrador normal nuevo
                                $existeCobradorEliminadoZona = DB::select("SELECT id_usuario FROM cobradoreseliminados
                                                                        WHERE id_zona = '$idZona'");
                                if ($existeCobradorEliminadoZona != null) {
                                    //Actualizar registros polizacobranza (Por cobradoreliminado)
                                    $contratosGlobal::actualizarRegistrosPolizaCobranzaPolizasAnteriores($id, $existeCobradorEliminadoZona[0]->id_usuario, $idUsuario->id, $idZona, true);
                                }else {
                                    //Actualizar registros polizacobranza (Por zona)
                                    $contratosGlobal::actualizarRegistrosPolizaCobranzaPolizasAnteriores($id, "", $idUsuario->id, $idZona, true);
                                }
                            }
                        }

                        //Creacion de permisos
                        if ($rol == 6 || $rol == 7 || $rol == 8) {
                            //Administrador, director o principal
                            //Seccion usuarios
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 0, 0);
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 0, 1);
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 0, 2);
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 0, 3);
                            //Seccion contratos
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 1, 0);
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 1, 1);
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 1, 2);
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 1, 3);
                            //Seccion administracion
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 2, 0);
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 2, 1);
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 2, 2);
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 2, 3);
                            //Seccion vehiculos
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 5, 0);
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 5, 1);
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 5, 2);
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 5, 3);
                            //Seccion campañas
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 6, 0);
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 6, 1);
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 6, 2);
                            $contratosGlobal::crearPermisoSeccion($idUsuario->id, 6, 3);
                        }

                        //Registrar movimiento en historial sucursal
                        $id_UsuarioC = Auth::user()->id;
                        $cambio = "Creo un usuario con el nombre: '" . request()->nombre . "'";

                        self::insertarHistorialSucursal($id, $id_UsuarioC,$cambio, "3", "0");

                        return back()->with('bien', 'El usuario se creo correctamente');

                    } catch (\Exception $e) {
                        \Log::info("Error: " . $e->getMessage());
                        return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina.');

                    }
                } else {
                    return back()->with('alerta', 'El correo ya se encuentra en uso.')->withInput($request->all());
                }

            } else {

                $idUsuarioP = $request->input('usuarioP');
                $existeUsuario = DB::select("SELECT * FROM users WHERE id = ' $idUsuarioP'");
                if ($existeUsuario != null) {
                    $existeUsuarioEnFranquicia = DB::select("SELECT * FROM usuariosfranquicia WHERE id_usuario = ' $idUsuarioP' AND id_franquicia = '$id'");
                    if ($existeUsuarioEnFranquicia == null) {
                        try {

                            DB::table('usuariosfranquicia')->insert([
                                'id_usuario' => $idUsuarioP, 'id_franquicia' => $id, 'created_at' => Carbon::now()
                            ]);

                            DB::table('franquicias')->where('id', $id)->update([
                                'actualizadopor' => strtoupper(Auth::user()->name), 'updated_at' => Carbon::now()
                            ]);

                            $idUsuario = $existeUsuario[0]->id;

                            //Crear contratos vacios y actualizar id_franquicia
                            $rolUsuario = $existeUsuario[0]->rol_id;
                            if($rolUsuario == 12 || $rolUsuario == 13) {
                                //Rol es asistente/optometrista

                                $numContratosVacios = DB::select("SELECT COUNT(id) as totalids
                                                        FROM contratos WHERE id_franquicia = '$id' AND id_usuariocreacion = '" . $idUsuario . "' AND datos = '0'");

                                $numContratosACrear = 20 - $numContratosVacios[0]->totalids;
                                if ($numContratosACrear > 0) {
                                    //Se necesita crear mas contratos perzonalizados

                                    $globalesServicioWeb = new globalesServicioWeb;
                                    $nombreUsuario = $existeUsuario[0]->name;

                                    $anoActual = Carbon::now()->format('y'); //Obtener los ultimos 2 digitos del año 21, 22, 23, 24, etc

                                    //Obtener indice de la franquicia
                                    $franquicia = DB::select("SELECT indice FROM franquicias WHERE id = '$id'");
                                    $identificadorFranquicia = "";
                                    if ($franquicia != null) {
                                        //Existe franquicia
                                        $identificadorFranquicia = $globalesServicioWeb::obtenerIdentificadorFranquicia($franquicia[0]->indice);
                                    }
                                    $identificadorFranquicia = $anoActual . $identificadorFranquicia . $globalesServicioWeb::obtenerIdentificadorUsuario($idUsuario); //2200100001, 2200200001, etc

                                    //Obtener el ultimo id generado en la tabla de contrato
                                    $contratoSelect = DB::select("SELECT id FROM contratos
                                                                WHERE id_franquicia = '$id'
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
                                        DB::table("contratos")->insert(["id" => $arrayRespuesta[0], "id_franquicia" => $id, "id_usuariocreacion" => $idUsuario,
                                            "nombre_usuariocreacion" => $nombreUsuario, "poliza" => null]);
                                        $ultimoIdContratoPerzonalizado = $arrayRespuesta[1] + 1;
                                    }

                                }

                            }

                            $idfranquiciaprincipalactualizar = $existeUsuario[0]->id_franquiciaprincipal;
                            if ($idfranquiciaprincipalactualizar == null) {
                                //id_franquiciaprincipal es igual a null
                                $idfranquiciaprincipalactualizar = $id;
                            }

                            //Actualizar fechaeliminacion en null
                            DB::table('users')->where('id', $idUsuario)->update([
                                'fechaeliminacion' => null,
                                'id_zona' => null,
                                'id_franquiciaprincipal' => $idfranquiciaprincipalactualizar
                            ]);

                            //Validar si usuario tiene registro de asistencia con poliza del dia
                            $polizaGlobales::buscarasistenciapolizaeinsertar($id, $idUsuario);

                            //Guardar contratos en tabla contratostemporalessincronizacion
                            if($rolUsuario == 12 || $rolUsuario == 13) {
                                //Cobrador, Asistente o Optometrista
                                $contratosGlobal::insertarDatosTablaContratosTemporalesSincronizacion($id, $existeUsuario[0]->id, $existeUsuario[0]->id_zona, $rolUsuario);
                            }

                            //Creacion de permisos
                            if ($rolUsuario == 6 || $rolUsuario == 7 || $rolUsuario == 8) {
                                //Administrador, director o principal
                                //Seccion usuarios
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 0, 0);
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 0, 1);
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 0, 2);
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 0, 3);
                                //Seccion contratos
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 1, 0);
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 1, 1);
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 1, 2);
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 1, 3);
                                //Seccion administracion
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 2, 0);
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 2, 1);
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 2, 2);
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 2, 3);
                                //Seccion vehiculos
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 5, 0);
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 5, 1);
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 5, 2);
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 5, 3);
                                //Seccion de campañas
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 6, 0);
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 6, 1);
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 6, 2);
                                $contratosGlobal::crearPermisoSeccion($existeUsuario[0]->id, 6, 3);
                            }

                            return redirect()->route('editarUsuarioFranquicia', [$id, $idUsuarioP])->with('bien', 'El usuario se agrego correctamente');

                        } catch (\Exception $e) {
                            \Log::info("Error: " . $e->getMessage());
                            return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina');
                        }
                    }
                    return back()->with('alerta', 'El usuario ya esta asignado a esta sucursal.');
                }
                return back()->with('alerta', 'No se encontro el usuario.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function eliminarsuariofranquicia()
    {
        if ((Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8)) //ROL DEL DIRECTOR
        {
            $idfranquiciaactual = request('idFranquicia');
            $idfranquicia = request('idFranquiciaUsuario');
            $usuario = request('idUsuario');

            try {

                $contratosGlobal = new contratosGlobal;

                $existeFranquicia = DB::select("SELECT * FROM franquicias WHERE id = '$idfranquicia'");
                if ($existeFranquicia != null) {
                    $existeUsuario = DB::select("SELECT * FROM users WHERE id = '$usuario'");
                    if ($existeUsuario != null) {
                        $existeUsuarioEnFranquicia = DB::select("SELECT * FROM usuariosfranquicia WHERE id_usuario = '$usuario' AND id_franquicia = '$idfranquicia'");
                        if ($existeUsuarioEnFranquicia != null) {

                            //Valor check box modal baja usuario
                            $forzarBaja = request("cbForzarbaja");
                            //Recuperar estado de logueo para el usuario
                            $usuarioLogueado = DB::select("SELECT u.logueado, u.rol_id, u.name FROM users u WHERE u.id = '$usuario'");

                            //Esta logueado el usuario?
                            if($usuarioLogueado[0]->logueado == '0' || ($usuarioLogueado[0]->logueado != '0' && $forzarBaja == 1)){
                                //El usuario no esta logueado o esta logueado pero se forzo la baja

                                //Validar rol de usuario - No podra dar de baja usuario con un rol superior
                                switch(Auth::user()->rol_id){
                                    case 6:
                                        //Administracion
                                        if($usuarioLogueado[0]->rol_id == 7 || $usuarioLogueado[0]->rol_id == 8){
                                            return back()->with('alerta', "No cuentas con los permisos necesarios para eliminar el usuario " . $usuarioLogueado[0]->name . " ya que está fuera de tu autorización.");
                                        }
                                        break;
                                    case 8:
                                        //Principal
                                        if($usuarioLogueado[0]->rol_id == 7){
                                            return back()->with('alerta', "No cuentas con los permisos necesarios para eliminar el usuario " . $usuarioLogueado[0]->name . " ya que está fuera de tu autorización.");
                                        }
                                        break;
                                }

                                if ($usuarioLogueado[0]->rol_id == 4) {
                                    //Rol es igual a cobrador
                                    //Baja sin forzar
                                    if($forzarBaja != 1){
                                        //Validar si tiene abonos pendientes por realizar corte
                                        $abono = DB::select("SELECT indice FROM abonos WHERE id_usuario = '$usuario' AND id_corte IS NULL ORDER BY created_at DESC LIMIT 1");
                                        if ($abono != null) {
                                            //Se tiene un abono pendiente para reiniciar
                                            return back()->with('alerta', "Favor de actualizar al cobrador " . $usuarioLogueado[0]->name . " aplicar el reinicio del corte.");
                                        }
                                    }
                                }

                                $mensaje = "";
                                if ($existeUsuario[0]->rol_id == 4 && $existeUsuario[0]->supervisorcobranza == 0) {
                                    //Rol cobrador y es cobrador normal

                                    $cobradorsupervisor = DB::select("SELECT u.id as id, u.name as nombre, u.sueldo as sueldo, u.logueado as logueado FROM users u
                                                  INNER JOIN usuariosfranquicia uf
                                                  ON u.id = uf.id_usuario WHERE u.id_zona = '" . $existeUsuario[0]->id_zona . "' AND u.supervisorcobranza = '1'");

                                    $bandera = false;
                                    if ($cobradorsupervisor != null) {
                                        //Existe cobrador supervisor
                                        $mensaje = "<br>No se pudo realizar el cambio de cobrador supervisor a normal a " . $cobradorsupervisor[0]->nombre . " por algunas de las siguientes razones:
                                                    <br>1- No se ha cerrado sesión <br>2- No se le ha realizado el corte (Abonos sin corte)";
                                        if ($cobradorsupervisor[0]->logueado == 0) {
                                            //Tiene la sesion cerrada el cobrador supervisor
                                            $abono = DB::select("SELECT indice FROM abonos WHERE id_usuario = '" . $cobradorsupervisor[0]->id . "' AND id_corte IS NULL ORDER BY created_at DESC LIMIT 1");
                                            if ($abono == null) {
                                                //No existen abonos sin corte
                                                $bandera = true;
                                                $mensaje = "";
                                            }
                                        }
                                    }

                                    if ($bandera) {
                                        //Actualizar cobrador supervisor a cobrador normal
                                        DB::table("users")
                                            ->where("id", "=", $cobradorsupervisor[0]->id)
                                            ->update([
                                                "supervisorcobranza" => "0"
                                            ]);

                                        //Actualizar registros polizacobranza
                                        $contratosGlobal::actualizarRegistrosPolizaCobranzaPolizasAnteriores($idfranquicia, $usuario, $cobradorsupervisor[0]->id, $existeUsuario[0]->id_zona, true);

                                    }

                                    if ($existeUsuario[0]->id_zona != null) {
                                        //Cobrador tenia una zona asignada
                                        //Insertar registro en tabla cobradoreseliminados
                                        DB::table('cobradoreseliminados')->insert([
                                            'id_franquicia' => $idfranquicia,
                                            'id_usuario' => $usuario,
                                            'id_zona' => $existeUsuario[0]->id_zona,
                                            'created_at' => Carbon::now()
                                        ]);
                                    }

                                }

                                //Quitar asignacion de vehiculo
                                if($usuarioLogueado[0]->rol_id == 4 || $usuarioLogueado[0]->rol_id == 17){
                                    //Es rol de cobranaza o chofer
                                    $vehiculoUsuario = DB::select("SELECT * FROM vehiculosusuarios vu WHERE vu.id_franquicia = '$idfranquicia' AND vu.id_usuario = '$usuario'");
                                    if($vehiculoUsuario != null){
                                        //Tiene vehiculo asignado - Eliminar asignacion
                                        $id_vehiculo = $vehiculoUsuario[0]->id_vehiculo;
                                        DB::delete("DELETE FROM vehiculosusuarios WHERE id_vehiculo = '$id_vehiculo' AND id_usuario = '$usuario' AND id_franquicia = '$idfranquicia'");
                                        //Verificar supervision vehicular
                                        $existeSupervision = DB::select("SELECT * FROM vehiculossupervision vs WHERE vs.id_franquicia = '$idfranquicia'
                                                                                AND vs.id_usuario = '$usuario' AND vs.id_vehiculo = '$id_vehiculo' AND vs.estado = 0 ORDER BY vs.created_at DESC LIMIT 1");
                                        if($existeSupervision != null){
                                            //Existe supervision pendiente
                                            $indice = $existeSupervision[0]->indice;
                                            DB::update("UPDATE vehiculossupervision SET estado = 1 WHERE indice = '$indice'");
                                        }
                                    }
                                }

                                DB::delete("DELETE FROM usuariosfranquicia WHERE id_usuario = '$usuario' AND id_franquicia = '$idfranquicia'");
                                DB::delete("DELETE FROM dispositivosusuarios WHERE id_usuario = '$usuario'");
                                DB::delete("DELETE FROM tokenlolatv WHERE usuario_id = '$usuario'");

                                $idZonaActualizar = $existeUsuario[0]->id_zona;
                                if ($existeUsuario[0]->rol_id != 4) {
                                    //Rol diferente de cobrador
                                    $idZonaActualizar = null;
                                }

                                //Agregar fechaeliminacion del usuario
                                DB::table('users')->where('id', $usuario)->update([
                                    'fechaeliminacion' => Carbon::now(),
                                    'id_zona' => $idZonaActualizar
                                ]);

                                //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idUsuario
                                DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id_usuario = '$usuario'");

                                //Eliminar registros de la tabla abonoscontratostemporalessincronizacion que contengan ese idUsuario
                                DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_usuariocobrador = '$usuario'");

                                //Registrar movimiento en historial sucursal
                                $id_UsuarioC = Auth::user()->id;
                                $nombreUsuarioBaja = $existeUsuario[0]->name;
                                $sucursalBaja = $existeFranquicia[0]->ciudad;

                                if($forzarBaja == 1){
                                    //Se forzo la baja del usuario
                                    $cambio = "Forzó la baja del usuario '" . $nombreUsuarioBaja . "' de la sucursal '". $sucursalBaja ."'";;
                                    $tipomensaje = 2;
                                }else {
                                    //Baja sin seleccion de check box de forzar
                                    $cambio = "Dio de baja el usuario '" . $nombreUsuarioBaja . "' de la sucursal '". $sucursalBaja ."'";
                                    $tipomensaje = 1;
                                }

                                if ($usuarioLogueado[0]->rol_id == 6 || $usuarioLogueado[0]->rol_id == 7 || $usuarioLogueado[0]->rol_id == 8) {
                                    //Administrador, director o principal
                                    //Eliminar registros de la tabla permisosusuarios que contengan ese idUsuario
                                    DB::delete("DELETE FROM permisosusuarios WHERE id_usuario = '$usuario'");
                                }

                                //Insertar registro en tabla historialsucursal
                                self::insertarHistorialSucursal($idfranquiciaactual, $id_UsuarioC, $cambio, $tipomensaje, "0");

                                return redirect()->route('usuariosFranquicia', $idfranquiciaactual)->with('bien', 'El usuario se elimino correctamente de la franquicia' . $mensaje);
                            }
                            //El esta logueado y no se forza la baja
                            return back()->with('alerta', ' Para dar de baja un usuario es necesario que cierre sesión o forzar la baja.');
                        }
                        return redirect()->route('usuariosFranquicia', $idfranquiciaactual)->with('alerta', 'No se encontro el usuario dentro de la franquicia.');
                    }
                    return redirect()->route('usuariosFranquicia', $idfranquiciaactual)->with('alerta', 'No se encontro el usuario.');
                }
                return redirect()->route('usuariosFranquicia', $idfranquiciaactual)->with('alerta', 'No se encontro la sucursal.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return redirect()->route('usuariosFranquicia', $idfranquiciaactual)
                    ->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function editarsuariofranquicia($idFranquicia, $idusuario)
    {
        if ((Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8)) //ROL DEL DIRECTOR
        {
            try {
                $existeFranquicia = DB::select("SELECT * FROM franquicias WHERE id = '$idFranquicia'");
                if ($existeFranquicia != null) {
                    $existeUsuario = DB::select("SELECT id,rol_id,name,email,foto,actanacimiento,identificacion,curp,comprobantedomicilio,segurosocial,solicitud,
                                                                tarjetapago,otratarjetapago,contratolaboral,contactoemergencia,id_zona,sueldo,codigoasistencia,renovacion,
                                                                pagare,tarjeta,otratarjeta,supervisorcobranza, fechanacimiento, id_franquiciaprincipal, estatus, ultimaconexion,
                                                                barcode FROM users WHERE id = ' $idusuario' ");
                    if ($existeUsuario != null) {
                        $existeUsuarioEnFranquicia = DB::select("SELECT * FROM usuariosfranquicia WHERE id_usuario = ' $idusuario' AND id_franquicia = '$idFranquicia'");

                        //Bandera para usuario existente sin franquicia asignada
                        $usuarioSinFranquicia = false;
                        if ($existeUsuarioEnFranquicia == null) {
                            //Usuario no tiene una franquicia asignada
                            $usuarioSinFranquicia = true;
                        }
                        $roles = DB::select("SELECT * FROM roles");
                        $zonas = DB::select("SELECT id,zona FROM zonas WHERE id_franquicia = '$idFranquicia' ORDER BY zona");
                        $dispositivosusuario = DB::select("SELECT * FROM dispositivosusuarios WHERE id_usuario = '$idusuario'");
                        $sucursales = DB::select("SELECT id,estado,ciudad,colonia,numero FROM franquicias");
                        $sucursalesSeleccionadas = DB::select("SELECT id_franquicia FROM sucursalesconfirmaciones WHERE id_usuario = '$idusuario'");
                        $controlentradasalidausuario = DB::select("SELECT horaini, horafin FROM controlentradasalidausuario WHERE id_usuario = '$idusuario'");
                        $secciones = DB::select("SELECT s.descripcion FROM seccion s ORDER BY s.descripcion ASC");
                        $permisosUsuario = DB::select("SELECT s.id AS id_seccion, s.descripcion AS descripcion_seccion, tp.id AS tipo_permiso, tp.descripcion as descripcion_permiso,
                                                                    IFNULL((SELECT ps.indice FROM permisosusuarios ps WHERE s.id = ps.id_seccion
                                                                           AND tp.id = ps.id_permiso AND ps.id_usuario = '$idusuario'
                                                                           ORDER BY ps.created_at DESC LIMIT 1), '') AS id_permisoasignado,
                                                                    IFNULL((SELECT ps.created_at FROM permisosusuarios ps WHERE s.id = ps.id_seccion
                                                                           AND tp.id = ps.id_permiso AND ps.id_usuario = '$idusuario'
                                                                           ORDER BY ps.created_at DESC LIMIT 1), '') AS fecha_permisoasignado FROM seccion s
                                                                    INNER JOIN tipopermiso tp
                                                                    ORDER BY s.descripcion, tp.descripcion DESC");

                        if($controlentradasalidausuario != null) {
                            //Existe usuario en tabla controlentradasalidausuario
                            $existeUsuario[0]->horaini = $controlentradasalidausuario[0]->horaini;
                            $existeUsuario[0]->horafin = $controlentradasalidausuario[0]->horafin;
                        }

                        $documentosExpediente = DB::select("SELECT eu.created_at, eu.documento, SUBSTRING_INDEX(eu.documento,'/',-1) as nombre, eu.descripcion, eu.indice
                                                                      FROM expedienteusuarios eu WHERE eu.id_usuario = '$idusuario' AND eu.id_franquicia = '$idFranquicia' ORDER BY eu.created_at DESC");

                        $vehiculos = DB::select("SELECT * FROM vehiculos v WHERE v.id_franquicia = '$idFranquicia' ORDER BY v.marca ASC");

                        $vehiculoAsignado = DB::select("SELECT vu.indice as indice, vu.id_vehiculo, v.modelo, v.marca, v.placas FROM vehiculosusuarios vu
                                                                  INNER JOIN vehiculos v ON v.indice = vu.id_vehiculo
                                                                  WHERE vu.id_franquicia = '$idFranquicia' AND vu.id_usuario = '$idusuario' LIMIT 1");

                        $franquicias = DB::select("SELECT f.id AS id, f.ciudad AS ciudad FROM franquicias f where id != '00000'");

                        $solicitudAutorizacion = DB::select("SELECT a.id_referencia, a.estatus, a.created_at, (15 - DATEDIFF(SYSDATE(),a.updated_at)) AS diasRestantes
                                                                       FROM autorizaciones a WHERE a.id_franquicia = '$idFranquicia' AND a.id_referencia = '$idusuario'
                                                                       AND a.tipo = 14 AND a.estatus != 3 ORDER BY a.created_at DESC LIMIT 1");

                        $excepcionasistencia = DB::select("SELECT indice FROM excepciones WHERE id_usuario = '$idusuario' AND tipo = '0'");

                        return view('administracion.franquicia.editarusuario', ['idFranquicia' => $idFranquicia, 'id' => $idFranquicia, 'idusuario' => $idusuario,
                            'usuario' => $existeUsuario, 'roles' => $roles, 'zonas' => $zonas, 'dispositivosusuario' => $dispositivosusuario,
                            "sucursalesSeleccionadas" => $sucursalesSeleccionadas, "sucursales" => $sucursales, 'secciones' => $secciones, 'permisosUsuario' => $permisosUsuario,
                            'documentosExpediente' => $documentosExpediente, 'vehiculos' => $vehiculos, 'vehiculoAsignado' => $vehiculoAsignado, 'usuarioSinFranquicia' => $usuarioSinFranquicia,
                            'franquicias' => $franquicias, 'solicitudAutorizacion' => $solicitudAutorizacion, 'excepcionasistencia' => $excepcionasistencia]);

                    }
                    return back()->with('alerta', 'No se encontro el usuario.');
                }
                return back()->with('alerta', 'No se encontro la sucursal.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarsuariofranquicia($idFranquicia, $idUsuario, Request $request)
    {
        if ((Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8)) //ROL DEL DIRECTOR
        {
            $correo = request()->correo;
            $banderaActualizoFoto = false;
            $banderaActualizoActa = false;
            $banderaActualizoINE = false;
            $banderaActualizoCURP = false;
            $banderaActualizoCDomicilio = false;
            $banderaActualizoSeguro = false;
            $banderaActualizoCV = false;
            $banderaActualizoTarjeta = false;
            $banderaActualizoOtraTarjeta = false;
            $banderaActualizoContactoEmergencia = false;
            $banderaActualizoContrato = false;
            $banderaActualizoPagare = false;
            $banderaActualizoSucursalesConfirmacion = false;
            $banderaSupervisorCobranza = false;
            $id_UsuarioC = Auth::user()->id;

            $existeCorreo = DB::select("SELECT name FROM users WHERE id <> '$idUsuario' AND email = '$correo'");
            if ($existeCorreo != null) {
                return back()->with('alerta', 'Este correo ya esta siendo utilizado por otro usuario ('.$existeCorreo[0]->name.').');
            }

            $hoy = Carbon::now();
            $hoyNumero = $hoy->dayOfWeekIso;
            $usuario = DB::select("SELECT * FROM users WHERE id = '$idUsuario'");
            $rol = request('rol');

            $banderaActualizoCbSupervisorCobranza = false;
            $supervisorcobranza = $usuario[0]->supervisorcobranza;
            $cbsupervisorcobranza = request('cbsupervisorcobranza') == null ? 0 : 1;

            $idZonaActualizar = request('idzona');
            if($usuario[0]->id_zona != $idZonaActualizar && $supervisorcobranza == 0 && $cbsupervisorcobranza == 0) {
                //Se cambio la zona y era supervisor y seguira siendo supervisor
                $existecobradornormalzona = DB::select("SELECT u.id, u.name FROM users u
                                                  INNER JOIN usuariosfranquicia uf
                                                  ON u.id = uf.id_usuario WHERE u.id_zona = '" . $idZonaActualizar . "' AND u.supervisorcobranza = '0'");

                if ($existecobradornormalzona != null) {
                    //Existe cobrador normal en la zona a actualizar
                    return back()->with('alerta', "Ya existe un cobrador normal en la zona a asignar con el nombre " . $existecobradornormalzona[0]->name . ", en caso de que se requiera puedes asignar como supervisor.");
                }
            }

            if ($rol == 4 && $usuario[0]->supervisorcobranza != $cbsupervisorcobranza) {
                //Rol a modificar es cobranza y se hizo un movimiento en el switch de supervision

                if($usuario[0]->logueado == 0) {
                    //Ya se encuentra cerrada la sesion
                    $abono = DB::select("SELECT indice FROM abonos WHERE id_usuario = '$idUsuario' AND id_corte IS NULL ORDER BY created_at DESC LIMIT 1");
                    if ($abono != null) {
                        //Se tiene un abono pendiente para el corte
                        return back()->with('alerta', "Favor de actualizar al cobrador " . $usuario[0]->name . " aplicar el reinicio del corte.");
                    }
                }else {
                    //No se ha cerrado sesion
                    return back()->with('alerta', "Para cambiar de supervisor/cobrador normal es necesario que " . $usuario[0]->name . " cierre sesión.");
                }

                $supervisorcobranza = 0;
                $cobradornormal = DB::select("SELECT u.id FROM users u
                                                  INNER JOIN usuariosfranquicia uf
                                                  ON u.id = uf.id_usuario WHERE u.id_zona = '" . $idZonaActualizar . "' AND u.supervisorcobranza = '0'");

                if ($cbsupervisorcobranza == 1) {
                    //Chekearon para ser supervisor
                    $supervisorcobranza = 1;
                    $banderaSupervisorCobranza = true;
                    //Obtener solicitud de autorizacion
                    $solicituAutorizacion = DB::select("SELECT * FROM autorizaciones a WHERE a.id_franquicia = '$idFranquicia' AND a.id_referencia = '$idUsuario'
                                                                       AND a.tipo = 14 AND a.estatus = 1 ORDER BY a.created_at DESC LIMIT 1");
                } else {
                    //No chekearon para ser supervisor
                    if ($usuario[0]->supervisorcobranza == 1 && $cobradornormal != null) {
                        //Ya es supervisor y hay un cobrador normal
                        $supervisorcobranza = 1;
                    }
                    if ($cobradornormal != null && $cobradornormal[0]->id != $idUsuario) {
                        //Ya hay un cobrador normal y idCobradorActualNormal es diferente al idUsuario que se quiere actualizar
                        return back()->with('alerta', 'No puedes tener dos cobradores en la misma zona, en caso de que se requiera puedes asignar como supervisor.')->withInput($request->all());
                    }
                    if ($usuario[0]->supervisorcobranza == 1 && $cobradornormal == null && $hoyNumero != 1) {
                        //Es supervisor de cobranza y no hay cobrador normal en la zona y dia es diferente de lunes
                        return back()->with('alerta', 'No se puede cambiar de supervisor a normal, solo los días lunes.')->withInput($request->all());
                    }
                }

                $banderaActualizoCbSupervisorCobranza = true;
            }

            $contratosGlobal = new contratosGlobal;
            $polizaGlobales = new polizaGlobales;
            $now = Carbon::now();
            $numeroDia = $now->dayOfWeekIso;    //Obtenemos el dia de la semana actual

            $banderaActualizoIdFranquiciaPrincipal = false;
            $idfranquiciaprincipalactualizar = request('franquiciaprincipal');
            if ($idfranquiciaprincipalactualizar != null && ($rol == 12 || $rol == 13)) {
                //Rol es optometrista o asistente
                if ($usuario[0]->id_franquiciaprincipal != $idfranquiciaprincipalactualizar) {
                    //Se quiere cambiar la franquicia principal
                    if ($numeroDia != 1) {
                        //Dia es martes, miercoles, jueves, viernes, sabado o domingo
                        return back()->with('alerta', "Solo se puede cambiar la franquicia principal los días lunes.");
                    } else {
                        //Dia es lunes
                        if ($usuario[0]->logueado == 0) {
                            //Ya se encuentra cerrada la sesion
                            $banderaActualizoIdFranquiciaPrincipal = true;
                        } else {
                            //No se ha cerrado sesion
                            return back()->with('alerta', "Para cambiar la franquicia principal es necesario que " . $usuario[0]->name . " cierre sesión.");
                        }
                    }
                }
            }

            if ($rol == 12 || $rol == 13 || $rol == 14) {

                $fotoBool = false;
                if (strlen(request()->foto) > 0) {
                    if (strlen($usuario[0]->foto) > 0) {
                        $fotoBool = true;
                    }
                } else {
                    $fotoBool = true;
                }

                $actanacimientoBool = false;
                if (strlen(request()->actanacimiento) == 0) {
                    if (strlen($usuario[0]->actanacimiento) > 0) {
                        $actanacimientoBool = true;
                    }
                } else {
                    $actanacimientoBool = true;
                    $banderaActualizoActa = true;
                }

                $identificacionBool = false;
                if (strlen(request()->identificacion) == 0) {
                    if (strlen($usuario[0]->identificacion) > 0) {
                        $identificacionBool = true;
                    }
                } else {
                    $identificacionBool = true;
                    $banderaActualizoINE = true;
                }

                $curpBool = false;
                if (strlen(request()->curp) == 0) {
                    if (strlen($usuario[0]->curp) > 0) {
                        $curpBool = true;
                    }
                } else {
                    $curpBool = true;
                    $banderaActualizoCURP = true;
                }

                $comprobanteBool = false;
                if (strlen(request()->comprobante) == 0) {
                    if (strlen($usuario[0]->comprobantedomicilio) > 0) {
                        $comprobanteBool = true;
                    }
                } else {
                    $comprobanteBool = true;
                    $banderaActualizoCDomicilio = true;
                }

                $seguroBool = false;
                if (strlen(request()->seguro) == 0) {
                    if (strlen($usuario[0]->segurosocial) > 0) {
                        $seguroBool = true;
                    }
                } else {
                    $seguroBool = true;
                    $banderaActualizoSeguro = true;
                }

                $solicitudBool = false;
                if (strlen(request()->solicitud) == 0) {
                    if (strlen($usuario[0]->solicitud) > 0) {
                        $solicitudBool = true;
                    }
                } else {
                    $solicitudBool = true;
                    $banderaActualizoCV = true;
                }

                $tarjetapagoBool = false;
                if (strlen(request()->tarjetapago) == 0) {
                    if (strlen($usuario[0]->tarjetapago) > 0) {
                        $tarjetapagoBool = true;
                    }
                } else {
                    $tarjetapagoBool = true;
                    $banderaActualizoPagare = true;
                }

                $otratarjetapagoBool = false;
                if (strlen(request()->otratarjetapago) == 0) {
                    if (strlen($usuario[0]->otratarjetapago) > 0) {
                        $otratarjetapagoBool = true;
                    }
                } else {
                    $otratarjetapagoBool = true;
                    $banderaActualizoOtraTarjeta = true;
                }

                $contratolaboralBool = false;
                if (strlen(request()->contratolaboral) == 0) {
                    if (strlen($usuario[0]->contratolaboral) > 0) {
                        $contratolaboralBool = true;
                    }
                } else {
                    $contratolaboralBool = true;
                    $banderaActualizoContrato = true;
                }

                $contactoemergenciaBool = false;
                if (strlen(request()->contactoemergencia) == 0) {
                    if (strlen($usuario[0]->contactoemergencia) > 0) {
                        $contactoemergenciaBool = true;
                    }
                } else {
                    $contactoemergenciaBool = true;
                    $banderaActualizoContactoEmergencia = true;
                }

                /*
                if (!$fotoBool || !$actanacimientoBool || !$identificacionBool || !$curpBool || !$comprobanteBool
                    || !$seguroBool || !$solicitudBool || !$tarjetapagoBool || !$otratarjetapagoBool || !$contratolaboralBool || !$contactoemergenciaBool) {
                    return back()->with('alerta', 'Para el rol seleccionado es necesario ingresar todos los documentos.');
                }
                */
            }

            $contador = 0;
            $idsSucursales = array();
            if ($rol == 15) {
                $franquiciasSeleccionadas = array();
                $franquiciasAsignadasActual = array();

                $franquiciasUsuario = DB::select("SELECT sc.id_franquicia FROM sucursalesconfirmaciones sc WHERE sc.id_usuario = '$idUsuario'");
                //Recorremos la respuesta para ver cuales son las sucursales actuales del usuario
                foreach ($franquiciasUsuario as $franquiciaUsuario){
                    array_push($franquiciasAsignadasActual,$franquiciaUsuario->id_franquicia);
                }

                $franquicias = DB::select("SELECT id FROM franquicias");
                foreach ($franquicias as $franquicia) {
                    $franquiciaEntrada = request("$franquicia->id");
                    if (!is_null($franquiciaEntrada) && $franquiciaEntrada > 0) {
                        array_push($idsSucursales, $franquicia->id);
                        $contador++;
                    }
                }

                //Verificamos si existe diferencia entre las sucursales actuales entrantes y las asignadas
                $sucursalesDiferentes = array_diff($franquiciasAsignadasActual, $idsSucursales);
                if(sizeof($sucursalesDiferentes) > 0){
                    //Si existen diferencias entre arreglos es porque se actualizaron las sucursales
                    $banderaActualizoSucursalesConfirmacion = true;
                }

                if ($contador == 0) {
                    return back()->with('alerta', 'Debes seleccionar al menos una sucursal.')->withInput($request->all());
                }
            }

            if (strlen(request()->contrasena) > 0) {
                if ($rol == 4) {
                    request()->validate([
                        'nombre' => 'required|string|min:5|max:255',
                        'correo' => 'email',
                        'contrasena' => ['required', 'string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
                        'ccontrasena' => 'required|same:contrasena',
                        'rol' => 'required',
                        'idzona' => 'required',
                        'sueldo' => 'numeric|nullable',
                        'tarjeta' => 'required|string|min:16|max:20',
                        'otratarjeta' => 'required|string|min:16|max:20'
                    ]);
                } else {
                    request()->validate([
                        'nombre' => 'required|string|min:5|max:255',
                        'correo' => 'email',
                        'contrasena' => ['required', 'string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
                        'ccontrasena' => 'required|same:contrasena',
                        'rol' => 'required',
                        'sueldo' => 'numeric|nullable',
                        'tarjeta' => 'required|string|min:16|max:20',
                        'otratarjeta' => 'required|string|min:16|max:20'
                    ]);
                }

                $archivosComprimir = [];
                $foto = "";
                if (strlen(request()->foto) == 0) {
                    if (strlen($usuario[0]->foto) > 0) {
                        $foto = $usuario[0]->foto;
                    }
                } else {
                    if (request()->hasFile('foto')) {
                        $fotoBruta = 'Foto-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('foto')->getClientOriginalExtension();
                        $foto = request()->file('foto')->storeAs('uploads/imagenes/usuarios/foto', $fotoBruta, 'disco');
                        $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/foto/' . $fotoBruta)->height();
                        $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/foto/' . $fotoBruta)->width();
                        if ($alto > $ancho) {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/foto/' . $fotoBruta)->resize(600, 800);
                        } else {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/foto/' . $fotoBruta)->resize(800, 600);
                        }
                        $imagenfoto->save();
                        $banderaActualizoFoto = true;
                    }
                }
                $actanacimiento = "";
                if (strlen(request()->actanacimiento) == 0) {
                    if (strlen($usuario[0]->actanacimiento) > 0) {
                        $actanacimiento = $usuario[0]->actanacimiento;
                    }
                } else {
                    if (request()->hasFile('actanacimiento')) {
                        $actanacimientoBruta = 'Actanacimiento-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('actanacimiento')->getClientOriginalExtension();
                        $actanacimiento = request()->file('actanacimiento')->storeAs('uploads/imagenes/usuarios', $actanacimientoBruta, 'disco');
                        array_push($archivosComprimir, $actanacimientoBruta);
                        $banderaActualizoActa = true;
                    }
                }
                $identificacion = "";
                if (strlen(request()->identificacion) == 0) {
                    if (strlen($usuario[0]->identificacion) > 0) {
                        $identificacion = $usuario[0]->identificacion;
                    }
                } else {
                    if (request()->hasFile('identificacion')) {
                        $identificacionBruta = 'Identificacion-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('identificacion')->getClientOriginalExtension();
                        $identificacion = request()->file('identificacion')->storeAs('uploads/imagenes/usuarios', $identificacionBruta, 'disco');
                        array_push($archivosComprimir, $identificacionBruta);
                        $banderaActualizoINE = true;
                    }
                }
                $curp = "";
                if (strlen(request()->curp) == 0) {
                    if (strlen($usuario[0]->curp) > 0) {
                        $curp = $usuario[0]->curp;
                    }
                } else {
                    if (request()->hasFile('curp')) {
                        $curpBruta = 'Curp-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('curp')->getClientOriginalExtension();
                        $curp = request()->file('curp')->storeAs('uploads/imagenes/usuarios', $curpBruta, 'disco');
                        array_push($archivosComprimir, $curpBruta);
                        $banderaActualizoCURP = true;
                    }
                }
                $comprobante = "";
                if (strlen(request()->comprobante) == 0) {
                    if (strlen($usuario[0]->comprobantedomicilio) > 0) {
                        $comprobante = $usuario[0]->comprobantedomicilio;
                    }
                } else {
                    if (request()->hasFile('comprobante')) {
                        $comprobanteBruta = 'Comprobante-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('comprobante')->getClientOriginalExtension();
                        $comprobante = request()->file('comprobante')->storeAs('uploads/imagenes/usuarios', $comprobanteBruta, 'disco');
                        array_push($archivosComprimir, $comprobanteBruta);
                        $banderaActualizoCDomicilio = true;
                    }
                }
                $seguro = "";
                if (strlen(request()->seguro) == 0) {
                    if (strlen($usuario[0]->segurosocial) > 0) {
                        $seguro = $usuario[0]->segurosocial;
                    }
                } else {
                    if (request()->hasFile('seguro')) {
                        $seguroBruta = 'Seguro-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('seguro')->getClientOriginalExtension();
                        $seguro = request()->file('seguro')->storeAs('uploads/imagenes/usuarios', $seguroBruta, 'disco');
                        array_push($archivosComprimir, $seguroBruta);
                        $banderaActualizoSeguro = true;
                    }
                }
                $solicitud = "";
                if (strlen(request()->solicitud) == 0) {
                    if (strlen($usuario[0]->solicitud) > 0) {
                        $solicitud = $usuario[0]->solicitud;
                    }
                } else {
                    if (request()->hasFile('solicitud')) {
                        $solicitudBruta = 'Solicitud-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('solicitud')->getClientOriginalExtension();
                        $solicitud = request()->file('solicitud')->storeAs('uploads/imagenes/usuarios', $solicitudBruta, 'disco');
                        array_push($archivosComprimir, $solicitudBruta);
                        $banderaActualizoCV = true;
                    }
                }
                $tarjetapago = "";
                if (strlen(request()->tarjetapago) == 0) {
                    if (strlen($usuario[0]->tarjetapago) > 0) {
                        $tarjetapago = $usuario[0]->tarjetapago;
                    }
                } else {
                    if (request()->hasFile('tarjetapago')) {
                        $tarjetapagoBruta = 'Tarjetapago-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('tarjetapago')->getClientOriginalExtension();
                        $tarjetapago = request()->file('tarjetapago')->storeAs('uploads/imagenes/usuarios', $tarjetapagoBruta, 'disco');
                        $alto7 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $tarjetapagoBruta)->height();
                        $ancho7 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $tarjetapagoBruta)->width();
                        if ($alto7 > $ancho7) {
                            $imagentarjetapago = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $tarjetapagoBruta)->resize(600, 800);
                        } else {
                            $imagentarjetapago = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $tarjetapagoBruta)->resize(800, 600);
                        }
                        $imagentarjetapago->save();
                        $banderaActualizoTarjeta = true;
                    }
                    array_push($archivosComprimir, $tarjetapagoBruta);
                }

                $otratarjetapago = "";
                if (strlen(request()->otratarjetapago) == 0) {
                    if (strlen($usuario[0]->otratarjetapago) > 0) {
                        $otratarjetapago = $usuario[0]->otratarjetapago;
                    }
                } else {
                    if (request()->hasFile('otratarjetapago')) {
                        $otratarjetapagoBruta = 'Otratarjetapago-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('otratarjetapago')->getClientOriginalExtension();
                        $otratarjetapago = request()->file('otratarjetapago')->storeAs('uploads/imagenes/usuarios', $otratarjetapagoBruta, 'disco');
                        $alto9 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $otratarjetapagoBruta)->height();
                        $ancho9 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $otratarjetapagoBruta)->width();
                        if ($alto9 > $ancho9) {
                            $imagenotratarjetapago = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $otratarjetapagoBruta)->resize(600, 800);
                        } else {
                            $imagenotratarjetapago = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $otratarjetapagoBruta)->resize(800, 600);
                        }
                        $imagenotratarjetapago->save();
                        $banderaActualizoOtraTarjeta = true;
                    }
                    array_push($archivosComprimir, $otratarjetapagoBruta);
                }

                $contratolaboral = "";
                if (strlen(request()->contratolaboral) == 0) {
                    if (strlen($usuario[0]->contratolaboral) > 0) {
                        $contratolaboral = $usuario[0]->contratolaboral;
                    }
                } else {
                    if (request()->hasFile('contratolaboral')) {
                        $contratolaboralBruta = 'contratolaboral-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('contratolaboral')->getClientOriginalExtension();
                        $contratolaboral = request()->file('contratolaboral')->storeAs('uploads/imagenes/usuarios', $contratolaboralBruta, 'disco');
                        array_push($archivosComprimir, $contratolaboralBruta);
                        $banderaActualizoContrato = true;
                    }

                }
                $contactoemergencia = "";
                if (strlen(request()->contactoemergencia) == 0) {
                    if (strlen($usuario[0]->contactoemergencia) > 0) {
                        $contactoemergencia = $usuario[0]->contactoemergencia;
                    }
                } else {
                    if (request()->hasFile('contactoemergencia')) {
                        $contactoemergenciaBruta = 'Contactoemergencia-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('contactoemergencia')->getClientOriginalExtension();
                        $contactoemergencia = request()->file('contactoemergencia')->storeAs('uploads/imagenes/usuarios', $contactoemergenciaBruta, 'disco');
                        array_push($archivosComprimir, $contactoemergenciaBruta);
                        $banderaActualizoContactoEmergencia = true;
                    }
                }

                $pagare = "";
                if (strlen(request()->pagare) == 0) {
                    if (strlen($usuario[0]->pagare) > 0) {
                        $pagare = $usuario[0]->pagare;
                    }
                } else {
                    if (request()->hasFile('pagare')) {
                        $pagareBruta = 'Pagare-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('pagare')->getClientOriginalExtension();
                        $pagare = request()->file('pagare')->storeAs('uploads/imagenes/usuarios', $pagareBruta, 'disco');
                        array_push($archivosComprimir, $pagareBruta);
                        $banderaActualizoPagare = true;
                    }
                }

                self::comprimirArchivosUsuarios($idUsuario, $archivosComprimir, 1);

                $sueldo = $usuario[0]->sueldo;
                if (request('sueldo') != null) {
                    if (strlen(request('sueldo')) > 0) {
                        $sueldo = request('sueldo');
                    }
                }

                if($usuario[0]->rol_id == 4 && ($rol == 12 || $rol == 13)) {
                    //Antes era cobranza y se cambiara al rol de asistente/optometrsita
                    if($usuario[0]->logueado == 0) {
                        //Ya se encuentra cerrada la sesion
                        //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idUsuario
                        DB::delete("DELETE FROM contratostemporalessincronizacion where id_usuario = '$idUsuario'");

                        //Eliminar registros de la tabla abonoscontratostemporalessincronizacion que contengan ese idUsuario
                        DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_usuariocobrador = '$idUsuario'");
                    }else {
                        //No se ha cerrado sesion
                        return back()->with('alerta', "Para cambiar de rol asistente/optometrista es necesario que " . $usuario[0]->name . " cierre sesión.");
                    }
                }

                $idZona = null;
                $banderaActualizoZonaCobrador = false;
                if ($rol == 4) {
                    $idZona = $idZonaActualizar;

                    //Guardar contratos en tabla contratostemporalessincronizacion y eliminar los anteriores
                    if($usuario[0]->id_zona != $idZona) {
                        //Se cambio la zona

                        if ($numeroDia != 1) {
                            //Dia es martes, miercoles, jueves, viernes, sabado o domingo
                            $fechaLunesAntes = $polizaGlobales::obtenerDia($numeroDia, 1); //Se obtiene la fecha del lunes anterior a la fecha actual
                            if ($numeroDia == 7) {
                                //Es domingo
                                $fechaLunesAntes = Carbon::parse($now)->subDays(6)->format('Y-m-d'); //Obtengo la fecha del dia lunes anterior
                            }
                            $abono = DB::select("SELECT indice FROM abonos WHERE STR_TO_DATE(created_at ,'%Y-%m-%d') >= STR_TO_DATE('$fechaLunesAntes','%Y-%m-%d')
                                                            AND STR_TO_DATE(created_at ,'%Y-%m-%d') <= STR_TO_DATE('$now','%Y-%m-%d')
                                                            AND id_usuario = '$idUsuario' ORDER BY created_at DESC LIMIT 1");
                            if ($abono != null && $supervisorcobranza == 0) {
                                //Se tiene un abono entre el lunes al dia actual y no es supervisor
                                return back()->with('alerta', "Solo se puede cambiar de zona los días lunes.");
                            }
                        }else {
                            //Dia es lunes
                            $abono = DB::select("SELECT indice FROM abonos WHERE id_usuario = '$idUsuario' AND id_corte IS NULL ORDER BY created_at DESC LIMIT 1");
                            if ($abono != null && $supervisorcobranza == 0) {
                                //Se tiene un abono pendiente para el corte y no es supervisor
                                return back()->with('alerta', "Favor de actualizar al cobrador " . $usuario[0]->name . " aplicar el reinicio del corte.");
                            }
                        }

                        if ($usuario[0]->logueado == 0) {
                            //Ya se encuentra cerrada la sesion
                            $banderaActualizoZonaCobrador = true;
                        } else {
                            //No se ha cerrado sesion
                            return back()->with('alerta', "Para cambiar de zona es necesario que " . $usuario[0]->name . " cierre sesión.");
                        }

                    }

                }

                if(($usuario[0]->rol_id == 6 || $usuario[0]->rol_id == 7 || $usuario[0]->rol_id == 8) && ($rol != 6 && $rol != 7 && $rol != 8)) {
                    //Era administrador, director o principal y se cambiara a un rol diferente a administrador, director y principal
                    //Eliminar registros de la tabla permisosusuarios que contengan ese idUsuario
                    DB::delete("DELETE FROM permisosusuarios WHERE id_usuario = '$idUsuario'");
                }

                //Creacion de permisos
                if ($rol == 6 || $rol == 7 || $rol == 8) {
                    //Administrador, director o principal
                    //Seccion usuarios
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 0, 0);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 0, 1);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 0, 2);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 0, 3);
                    //Seccion contratos
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 1, 0);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 1, 1);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 1, 2);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 1, 3);
                    //Seccion administracion
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 2, 0);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 2, 1);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 2, 2);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 2, 3);
                    //Seccion vehiculos
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 5, 0);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 5, 1);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 5, 2);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 5, 3);
                    //Seccion campañas
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 6, 0);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 6, 1);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 6, 2);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 6, 3);
                }

                $fechaRenovacion = request('fecharenovacion');
                $sinRenovacion = request('sinrenovacion');

                if ($fechaRenovacion != null) {
                    if ($sinRenovacion != null) {
                        $fechaRenovacion = null;
                    }
                }

                if (!is_null($fechaRenovacion)) {
                    try {
                        $fechaRenovacion = Carbon::parse($fechaRenovacion);
                        if ($fechaRenovacion <= $hoy) {
                            return back()->withErrors(['fecharenovacion' => 'Fecha no valida.'])->withInput($request->all());
                        }

                    } catch (\Exception $e) {
                        return back()->withErrors(['fecharenovacion' => 'Fecha no valida.'])->withInput($request->all());
                    }
                }

                DB::table('users')->where('id', $idUsuario)->update([
                    'password' => Hash::make(request()->contrasena), 'rol_id' => request()->rol, 'email' => request()->correo, 'name' => request()->nombre, 'foto' => $foto,
                    'actanacimiento' => $actanacimiento, 'identificacion' => $identificacion, 'curp' => $curp, 'comprobantedomicilio' => $comprobante, 'segurosocial' => $seguro,
                    'solicitud' => $solicitud, 'tarjetapago' => $tarjetapago, 'otratarjetapago' => $otratarjetapago, 'contratolaboral' => $contratolaboral,
                    'contactoemergencia' => $contactoemergencia, 'updated_at' => Carbon::now(), 'id_zona' => $idZona, 'sueldo' => $sueldo, 'renovacion' => $fechaRenovacion,
                    'tarjeta'=>request()->tarjeta, 'otratarjeta'=>request()->otratarjeta, 'pagare'=>$pagare, 'supervisorcobranza' => $supervisorcobranza,
                    'fechanacimiento' => request()->fechanacimiento
                ]);

                if($banderaSupervisorCobranza){
                    //Actualizar solicitud de autorizacion a generado - solo si existe solicitud
                    if($solicituAutorizacion != null){
                        $indiceSolicitud = $solicituAutorizacion[0]->indice;
                        DB::update("UPDATE autorizaciones SET estatus = 3 WHERE indice = '$indiceSolicitud'");
                    }
                }

                if ($contador > 0 && $rol == 15) {
                    DB::delete("DELETE FROM sucursalesconfirmaciones WHERE id_usuario = '$idUsuario'");
                    foreach ($idsSucursales as $idSucursal) {
                        DB::table("sucursalesconfirmaciones")->insert([
                            "id_usuario" => $idUsuario, "id_franquicia" => $idSucursal
                        ]);
                    }
                }

                /* Registrar cada uno de los cambios generados*/
                if($banderaActualizoFoto){
                    //Se actualizo la foto del usuario
                    $cambio = "Actualizo el archivo 'Fotografia' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoActa){
                    //Se actualizo acta de nacimiento
                    $cambio = "Actualizo el archivo 'Acta de nacimiento' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoINE){
                    $cambio = "Actualizo el archivo 'Identificacion oficial' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoCURP){
                    $cambio = "Actualizo el archivo 'CURP' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoCDomicilio){
                    $cambio = "Actualizo el archivo 'Comprobante de domicilio' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoSeguro){
                    $cambio = "Actualizo el archivo 'Numero de seguro social' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoCV){
                    $cambio = "Actualizo el archivo 'Curriculum/Solicitud' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoTarjeta){
                    $cambio = "Actualizo el archivo 'Tarjeta pago' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoOtraTarjeta){
                    $cambio = "Actualizo el archivo 'Otra tarjeta pago' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoContactoEmergencia){
                    $cambio = "Actualizo el archivo 'Contacto de emergencia' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoContrato){
                    $cambio = "Actualizo el archivo 'Contacto laboral' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoPagare){
                    $cambio = "Actualizo el archivo 'Pagare' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }

                if ($banderaActualizoIdFranquiciaPrincipal) {
                    //Se modifico la franquicia principal

                    DB::table('users')->where('id', $idUsuario)->update([
                        'id_franquiciaprincipal' => $idfranquiciaprincipalactualizar
                    ]);

                    //Validar si usuario tiene registro de asistencia con poliza del dia
                    $polizaGlobales::buscarasistenciapolizaeinsertar($idfranquiciaprincipalactualizar, $idUsuario);

                    $ciudadfranquiciaprincipalactual = DB::select("SELECT ciudad FROM franquicias WHERE id = '" . $usuario[0]->id_franquiciaprincipal . "' LIMIT 1");
                    $ciudadfranquiciaprincipalactualizar = DB::select("SELECT ciudad FROM franquicias WHERE id = '" . $idfranquiciaprincipalactualizar . "' LIMIT 1");
                    $ciudadfranquiciaprincipalactual = $ciudadfranquiciaprincipalactual == null ? "" : $ciudadfranquiciaprincipalactual[0]->ciudad;
                    $ciudadfranquiciaprincipalactualizar = $ciudadfranquiciaprincipalactualizar == null ? "" : $ciudadfranquiciaprincipalactualizar[0]->ciudad;
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC, "Actualizo la franquicia principal
                        del usuario: '" . $usuario[0]->name. "' de la sucursal '$ciudadfranquiciaprincipalactual' a '$ciudadfranquiciaprincipalactualizar'","4", "0");
                }if ($banderaActualizoCbSupervisorCobranza) {
                    //Se hizo actualizo a supervisor o cobrador normal
                    $mensaje = "de supervisor a cobrador normal";
                    if ($cbsupervisorcobranza == 1) {
                        $mensaje = "de cobrador normal a supervisor";
                    }
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC, "Actualizo $mensaje
                        al usuario: '" . $usuario[0]->name . "'","4", "0");
                }if ($banderaActualizoZonaCobrador) {
                    //Se actualizo la zona al cobrador
                    //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idUsuario
                    DB::delete("DELETE FROM contratostemporalessincronizacion where id_usuario = '$idUsuario'");
                    $contratosGlobal::insertarDatosTablaContratosTemporalesSincronizacion($idFranquicia, $idUsuario, $idZona, $rol);

                    $contratosGlobal::eliminarEInsertarAbonosContratosTemporalesSincronizacionPorUsuarios($idUsuario);

                    if ($supervisorcobranza == 0) {
                        //Se creo cobrador normal nuevo
                        $existeCobradorEliminadoZona = DB::select("SELECT id_usuario FROM cobradoreseliminados
                                                                        WHERE id_zona = '$idZona'");
                        $idCobradorEliminado = ""; //Actualizar registros polizacobranza (Por zona)
                        if ($existeCobradorEliminadoZona != null) {
                            //Actualizar registros polizacobranza (Por cobradoreliminado)
                            $idCobradorEliminado = $existeCobradorEliminadoZona[0]->id_usuario;
                        }
                        $contratosGlobal::actualizarRegistrosPolizaCobranzaPolizasAnteriores($idFranquicia, $idCobradorEliminado, $idUsuario, $idZona, true);
                    }
                    $nombreZonaAnterior = DB::select("SELECT zona FROM zonas WHERE id = '" . $usuario[0]->id_zona . "' LIMIT 1");
                    $nombreZonaActualizar = DB::select("SELECT zona FROM zonas WHERE id = '" . $idZona . "' LIMIT 1");
                    $nombreZonaAnterior = $nombreZonaAnterior == null ? "" : $nombreZonaAnterior[0]->zona;
                    $nombreZonaActualizar = $nombreZonaActualizar == null ? "" : $nombreZonaActualizar[0]->zona;
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC, "Actualizó de la zona $nombreZonaAnterior a zona $nombreZonaActualizar
                        al usuario: '" . $usuario[0]->name . "'","4", "0");

                    $mensaje = "";
                    if ($rol == 4 && $usuario[0]->supervisorcobranza == 0) {
                        //Rol cobrador y era cobrador normal

                        $cobradorsupervisor = DB::select("SELECT u.id as id, u.name as nombre, u.sueldo as sueldo, u.logueado as logueado FROM users u
                                                  INNER JOIN usuariosfranquicia uf
                                                  ON u.id = uf.id_usuario WHERE u.id_zona = '" . $usuario[0]->id_zona . "' AND u.supervisorcobranza = '1'");

                        $bandera = false;
                        if ($cobradorsupervisor != null) {
                            //Existe cobrador supervisor
                            $mensaje = "No se pudo realizar el cambio de cobrador supervisor a normal a " . $cobradorsupervisor[0]->nombre . " por algunas de las siguientes razones:
                                                    1- No se ha cerrado sesión, 2- No se le ha realizado el corte (Abonos sin corte)";
                            if ($cobradorsupervisor[0]->logueado == 0) {
                                //Tiene la sesion cerrada el cobrador supervisor
                                $abono = DB::select("SELECT indice FROM abonos WHERE id_usuario = '" . $cobradorsupervisor[0]->id . "' AND id_corte IS NULL ORDER BY created_at DESC LIMIT 1");
                                if ($abono == null) {
                                    //No existen abonos sin corte
                                    $bandera = true;
                                    $mensaje = "Se realizo el cambio de cobrador supervisor a normal a " . $cobradorsupervisor[0]->nombre;
                                }
                            }
                        }

                        if ($bandera) {
                            //Actualizar cobrador supervisor a cobrador normal
                            DB::table("users")
                                ->where("id", "=", $cobradorsupervisor[0]->id)
                                ->update([
                                    "supervisorcobranza" => "0"
                                ]);

                            //Actualizar registros polizacobranza
                            $contratosGlobal::actualizarRegistrosPolizaCobranzaPolizasAnteriores($idFranquicia, $idUsuario, $cobradorsupervisor[0]->id, $usuario[0]->id_zona, true);

                            //Agregar movimiento de sucursal
                            self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC, $mensaje,"4", "0");
                        }

                        if ($usuario[0]->id_zona != null) {
                            //Cobrador tenia una zona asignada
                            //Insertar registro en tabla cobradoreseliminados
                            DB::table('cobradoreseliminados')->insert([
                                'id_franquicia' => $idFranquicia,
                                'id_usuario' => $idUsuario,
                                'id_zona' => $usuario[0]->id_zona,
                                'created_at' => Carbon::now()
                            ]);
                        }

                    }

                }

                return back()->with('bien', 'Los datos del usuario se actualizaron correctamente.');

            } else {

                if ($rol == 4) {
                    request()->validate([
                        'nombre' => 'required|string|min:5|max:255',
                        'correo' => 'email',
                        'rol' => 'required',
                        'idzona' => 'required',
                        'sueldo' => 'numeric|nullable',
                        'tarjeta' => 'required|string|min:16|max:20',
                        'otratarjeta' => 'required|string|min:16|max:20'
                    ]);
                } else {
                    request()->validate([
                        'nombre' => 'required|string|min:5|max:255',
                        'correo' => 'email',
                        'rol' => 'required',
                        'sueldo' => 'numeric|nullable',
                        'tarjeta' => 'required|string|min:16|max:20',
                        'otratarjeta' => 'required|string|min:16|max:20'
                    ]);
                }


                $archivosComprimir = [];
                $foto = "";
                if (strlen(request()->foto) == 0) {
                    if (strlen($usuario[0]->foto) > 0) {
                        $foto = $usuario[0]->foto;
                    }
                } else {
                    if (request()->hasFile('foto')) {
                        $fotoBruta = 'Foto-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('foto')->getClientOriginalExtension();
                        $foto = request()->file('foto')->storeAs('uploads/imagenes/usuarios/foto', $fotoBruta, 'disco');
                        $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/foto/' . $fotoBruta)->height();
                        $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/foto/' . $fotoBruta)->width();
                        if ($alto > $ancho) {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/foto/' . $fotoBruta)->resize(600, 800);
                        } else {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/foto/' . $fotoBruta)->resize(800, 600);
                        }
                        $imagenfoto->save();
                        $banderaActualizoFoto = true;
                    }
                }
                $actanacimiento = "";
                if (strlen(request()->actanacimiento) == 0) {
                    if (strlen($usuario[0]->actanacimiento) > 0) {
                        $actanacimiento = $usuario[0]->actanacimiento;
                    }
                } else {
                    if (request()->hasFile('actanacimiento')) {
                        $actanacimientoBruta = 'Actanacimiento-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('actanacimiento')->getClientOriginalExtension();
                        $actanacimiento = request()->file('actanacimiento')->storeAs('uploads/imagenes/usuarios', $actanacimientoBruta, 'disco');
                        array_push($archivosComprimir,$actanacimientoBruta);
                        $banderaActualizoActa = true;
                    }
                }
                $identificacion = "";
                if (strlen(request()->identificacion) == 0) {
                    if (strlen($usuario[0]->identificacion) > 0) {
                        $identificacion = $usuario[0]->identificacion;
                    }
                } else {
                    if (request()->hasFile('identificacion')) {
                        $identificacionBruta = 'Identificacion-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('identificacion')->getClientOriginalExtension();
                        $identificacion = request()->file('identificacion')->storeAs('uploads/imagenes/usuarios', $identificacionBruta, 'disco');
                        array_push($archivosComprimir,$identificacionBruta);
                        $banderaActualizoINE = true;

                    }
                }
                $curp = "";
                if (strlen(request()->curp) == 0) {
                    if (strlen($usuario[0]->curp) > 0) {
                        $curp = $usuario[0]->curp;
                    }
                } else {
                    if (request()->hasFile('curp')) {
                        $curpBruta = 'Curp-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('curp')->getClientOriginalExtension();
                        $curp = request()->file('curp')->storeAs('uploads/imagenes/usuarios', $curpBruta, 'disco');
                        $alto3 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $curpBruta)->height();
                        $ancho3 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $curpBruta)->width();
                        if ($alto3 > $ancho3) {
                            $imagencurp = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $curpBruta)->resize(600, 800);
                        } else {
                            $imagencurp = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $curpBruta)->resize(800, 600);
                        }
                        $imagencurp->save();
                        $banderaActualizoCURP = true;
                    }
                    array_push($archivosComprimir,$curpBruta);
                }
                $comprobante = "";
                if (strlen(request()->comprobante) == 0) {
                    if (strlen($usuario[0]->comprobantedomicilio) > 0) {
                        $comprobante = $usuario[0]->comprobantedomicilio;
                    }
                } else {
                    if (request()->hasFile('comprobante')) {
                        $comprobanteBruta = 'Comprobante-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('comprobante')->getClientOriginalExtension();
                        $comprobante = request()->file('comprobante')->storeAs('uploads/imagenes/usuarios', $comprobanteBruta, 'disco');
                        array_push($archivosComprimir,$comprobanteBruta);
                        $banderaActualizoCDomicilio = true;

                    }
                }
                $seguro = "";
                if (strlen(request()->seguro) == 0) {
                    if (strlen($usuario[0]->segurosocial) > 0) {
                        $seguro = $usuario[0]->segurosocial;
                    }
                } else {
                    if (request()->hasFile('seguro')) {
                        $seguroBruta = 'Seguro-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('seguro')->getClientOriginalExtension();
                        $seguro = request()->file('seguro')->storeAs('uploads/imagenes/usuarios', $seguroBruta, 'disco');
                        array_push($archivosComprimir,$seguroBruta);
                        $banderaActualizoSeguro = true;
                    }
                }
                $solicitud = "";
                if (strlen(request()->solicitud) == 0) {
                    if (strlen($usuario[0]->solicitud) > 0) {
                        $solicitud = $usuario[0]->solicitud;
                    }
                } else {
                    if (request()->hasFile('solicitud')) {
                        $solicitudBruta = 'Solicitud-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('solicitud')->getClientOriginalExtension();
                        $solicitud = request()->file('solicitud')->storeAs('uploads/imagenes/usuarios', $solicitudBruta, 'disco');
                        array_push($archivosComprimir,$solicitudBruta);
                        $banderaActualizoCV = true;

                    }
                }
                $tarjetapago = "";
                if (strlen(request()->tarjetapago) == 0) {
                    if (strlen($usuario[0]->tarjetapago) > 0) {
                        $tarjetapago = $usuario[0]->tarjetapago;
                    }
                } else {
                    if (request()->hasFile('tarjetapago')) {
                        $tarjetapagoBruta = 'Tarjetapago-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('tarjetapago')->getClientOriginalExtension();
                        $tarjetapago = request()->file('tarjetapago')->storeAs('uploads/imagenes/usuarios', $tarjetapagoBruta, 'disco');
                        $alto7 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $tarjetapagoBruta)->height();
                        $ancho7 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $tarjetapagoBruta)->width();
                        if ($alto7 > $ancho7) {
                            $imagentarjetapago = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $tarjetapagoBruta)->resize(600, 800);
                        } else {
                            $imagentarjetapago = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $tarjetapagoBruta)->resize(800, 600);
                        }
                        $imagentarjetapago->save();
                        $banderaActualizoTarjeta = true;
                    }
                    array_push($archivosComprimir,$tarjetapagoBruta);
                }
                $otratarjetapago = "";
                if (strlen(request()->otratarjetapago) == 0) {
                    if (strlen($usuario[0]->otratarjetapago) > 0) {
                        $otratarjetapago = $usuario[0]->otratarjetapago;
                    }
                } else {
                    if (request()->hasFile('otratarjetapago')) {
                        $otratarjetapagoBruta = 'Otratarjetapago-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('otratarjetapago')->getClientOriginalExtension();
                        $otratarjetapago = request()->file('otratarjetapago')->storeAs('uploads/imagenes/usuarios', $otratarjetapagoBruta, 'disco');
                        $alto9 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $otratarjetapagoBruta)->height();
                        $ancho9 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $otratarjetapagoBruta)->width();
                        if ($alto9 > $ancho9) {
                            $imagenotratarjetapago = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $otratarjetapagoBruta)->resize(600, 800);
                        } else {
                            $imagenotratarjetapago = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $otratarjetapagoBruta)->resize(800, 600);
                        }
                        $imagenotratarjetapago->save();
                        $banderaActualizoOtraTarjeta = true;
                    }
                    array_push($archivosComprimir,$otratarjetapagoBruta);
                }
                $contratolaboral = "";
                if (strlen(request()->contratolaboral) == 0) {
                    if (strlen($usuario[0]->contratolaboral) > 0) {
                        $contratolaboral = $usuario[0]->contratolaboral;
                    }
                } else {
                    if (request()->hasFile('contratolaboral')) {
                        $contratolaboralBruta = 'contratolaboral-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('contratolaboral')->getClientOriginalExtension();
                        $contratolaboral = request()->file('contratolaboral')->storeAs('uploads/imagenes/usuarios', $contratolaboralBruta, 'disco');
                        array_push($archivosComprimir,$contratolaboralBruta);
                        $banderaActualizoContrato = true;
                    }
                }
                $contactoemergencia = "";
                if (strlen(request()->contactoemergencia) == 0) {
                    if (strlen($usuario[0]->contactoemergencia) > 0) {
                        $contactoemergencia = $usuario[0]->contactoemergencia;
                    }
                } else {
                    if (request()->hasFile('contactoemergencia')) {
                        $contactoemergenciaBruta = 'Contactoemergencia-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('contactoemergencia')->getClientOriginalExtension();
                        $contactoemergencia = request()->file('contactoemergencia')->storeAs('uploads/imagenes/usuarios', $contactoemergenciaBruta, 'disco');
                        array_push($archivosComprimir,$contactoemergenciaBruta);
                        $banderaActualizoContactoEmergencia = true;
                    }
                }

                $pagare = "";
                if (strlen(request()->pagare) == 0) {
                    if (strlen($usuario[0]->pagare) > 0) {
                        $pagare = $usuario[0]->pagare;
                    }
                } else {
                    if (request()->hasFile('pagare')) {
                        $pagareBruta = 'Pagare-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('pagare')->getClientOriginalExtension();
                        $pagare = request()->file('pagare')->storeAs('uploads/imagenes/usuarios', $pagareBruta, 'disco');
                        array_push($archivosComprimir,$pagareBruta);
                        $banderaActualizoPagare = true;
                    }
                }

                self::comprimirArchivosUsuarios($idUsuario, $archivosComprimir, 1);

                $sueldo = $usuario[0]->sueldo;
                if (request('sueldo') != null) {
                    if (strlen(request('sueldo')) > 0) {
                        $sueldo = request('sueldo');
                    }
                }

                if($usuario[0]->rol_id == 4 && ($rol == 12 || $rol == 13)) {
                    //Antes era cobranza y se cambiara al rol de asistente/optometrsita
                    if($usuario[0]->logueado == 0) {
                        //Ya se encuentra cerrada la sesion
                        //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idUsuario
                        DB::delete("DELETE FROM contratostemporalessincronizacion where id_usuario = '$idUsuario'");

                        //Eliminar registros de la tabla abonoscontratostemporalessincronizacion que contengan ese idUsuario
                        DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_usuariocobrador = '$idUsuario'");
                    }else {
                        //No se ha cerrado sesion
                        return back()->with('alerta', "Para cambiar de rol asistente/optometrista es necesario que " . $usuario[0]->name . " cierre sesión.");
                    }
                }

                $idZona = null;
                $banderaActualizoZonaCobrador = false;
                if ($rol == 4) {
                    $idZona = $idZonaActualizar;

                    //Guardar contratos en tabla contratostemporalessincronizacion y eliminar los anteriores
                    if($usuario[0]->id_zona != $idZona) {
                        //Se cambio la zona

                        if ($numeroDia != 1) {
                            //Dia es martes, miercoles, jueves, viernes, sabado o domingo
                            $fechaLunesAntes = $polizaGlobales::obtenerDia($numeroDia, 1); //Se obtiene la fecha del lunes anterior a la fecha actual
                            if ($numeroDia == 7) {
                                //Es domingo
                                $fechaLunesAntes = Carbon::parse($now)->subDays(6)->format('Y-m-d'); //Obtengo la fecha del dia lunes anterior
                            }
                            $abono = DB::select("SELECT indice FROM abonos WHERE STR_TO_DATE(created_at ,'%Y-%m-%d') >= STR_TO_DATE('$fechaLunesAntes','%Y-%m-%d')
                                                            AND STR_TO_DATE(created_at ,'%Y-%m-%d') <= STR_TO_DATE('$now','%Y-%m-%d')
                                                            AND id_usuario = '$idUsuario' ORDER BY created_at DESC LIMIT 1");
                            if ($abono != null && $supervisorcobranza == 0) {
                                //Se tiene un abono entre el lunes al dia actual y no es supervisor
                                return back()->with('alerta', "Solo se puede cambiar de zona los días lunes.");
                            }
                        }else {
                            //Dia es lunes
                            $abono = DB::select("SELECT indice FROM abonos WHERE id_usuario = '$idUsuario' AND id_corte IS NULL ORDER BY created_at DESC LIMIT 1");
                            if ($abono != null && $supervisorcobranza == 0) {
                                //Se tiene un abono pendiente para el corte y no es supervisor
                                return back()->with('alerta', "Favor de actualizar al cobrador " . $usuario[0]->name . " aplicar el reinicio del corte.");
                            }
                        }

                        if($usuario[0]->logueado == 0) {
                            //Ya se encuentra cerrada la sesion
                            $banderaActualizoZonaCobrador = true;
                        }else {
                            //No se ha cerrado sesion
                            return back()->with('alerta', "Para cambiar de zona es necesario que " . $usuario[0]->name . " cierre sesión.");
                        }
                    }

                }

                if(($usuario[0]->rol_id == 6 || $usuario[0]->rol_id == 7 || $usuario[0]->rol_id == 8) && ($rol != 6 && $rol != 7 && $rol != 8)) {
                    //Era administrador, director o principal y se cambiara a un rol diferente a administrador, director y principal
                    //Eliminar registros de la tabla permisosusuarios que contengan ese idUsuario
                    DB::delete("DELETE FROM permisosusuarios WHERE id_usuario = '$idUsuario'");
                }

                //Creacion de permisos
                if ($rol == 6 || $rol == 7 || $rol == 8) {
                    //Administrador, director o principal
                    //Seccion usuarios
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 0, 0);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 0, 1);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 0, 2);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 0, 3);
                    //Seccion contratos
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 1, 0);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 1, 1);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 1, 2);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 1, 3);
                    //Seccion administracion
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 2, 0);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 2, 1);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 2, 2);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 2, 3);
                    //Seccion vehiculos
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 5, 0);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 5, 1);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 5, 2);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 5, 3);
                    //Seccion campañas
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 6, 0);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 6, 1);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 6, 2);
                    $contratosGlobal::crearPermisoSeccion($idUsuario, 6, 3);
                }

                $fechaRenovacion = request('fecharenovacion');
                $sinRenovacion = request('sinrenovacion');

                if ($fechaRenovacion != null) {
                    if ($sinRenovacion != null) {
                        $fechaRenovacion = null;
                    }
                }

                $hoy = Carbon::now();
                if (!is_null($fechaRenovacion)) {
                    try {
                        $fechaRenovacion = Carbon::parse($fechaRenovacion);
                        if ($fechaRenovacion <= $hoy) {
                            return back()->withErrors(['fecharenovacion' => 'Fecha no valida.'])->withInput($request->all());
                        }

                    } catch (\Exception $e) {
                        return back()->withErrors(['fecharenovacion' => 'Fecha no valida.'])->withInput($request->all());
                    }
                }



                DB::table('users')->where('id', $idUsuario)->update([
                    'rol_id' => request()->rol, 'name' => request()->nombre, 'email' => request()->correo, 'foto' => $foto, 'actanacimiento' => $actanacimiento,
                    'identificacion' => $identificacion, 'curp' => $curp, 'comprobantedomicilio' => $comprobante, 'segurosocial' => $seguro, 'solicitud' => $solicitud, 'tarjetapago' => $tarjetapago,
                    'otratarjetapago' => $otratarjetapago, 'contratolaboral' => $contratolaboral, 'contactoemergencia' => $contactoemergencia, 'updated_at' => Carbon::now(), 'id_zona' => $idZona,
                    'sueldo' => $sueldo, 'renovacion' => $fechaRenovacion,'tarjeta'=>request('tarjeta'),'otratarjeta'=>request('otratarjeta'),'pagare'=> $pagare,
                    'supervisorcobranza' => $supervisorcobranza, 'fechanacimiento' => request()->fechanacimiento
                ]);

                if($banderaSupervisorCobranza){
                    //Actualizar solicitud de autorizacion a generado - solo si existe solicitud
                    if($solicituAutorizacion != null){
                        $indiceSolicitud = $solicituAutorizacion[0]->indice;
                        DB::update("UPDATE autorizaciones SET estatus = 3 WHERE indice = '$indiceSolicitud'");
                    }
                }

                DB::delete("DELETE FROM sucursalesconfirmaciones WHERE id_usuario = '$idUsuario'");
                if ($contador > 0 && $rol == 15) {
                    foreach ($idsSucursales as $idSucursal) {
                        DB::table("sucursalesconfirmaciones")->insert([
                            "id_usuario" => $idUsuario, "id_franquicia" => $idSucursal
                        ]);
                    }
                }
                /* Registrar cada uno de los cambios generados*/
                if($banderaActualizoFoto){
                    //Se actualizo la foto del usuario
                    $cambio = "Actualizo el archivo 'Fotografia' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoActa){
                    //Se actualizo acta de nacimiento
                    $cambio = "Actualizo el archivo 'Acta de nacimiento' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoINE){
                    //Se actualizo INE
                    $cambio = "Actualizo el archivo 'Identificacion oficial' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoCURP){
                    //Se actualizo CURP
                    $cambio = "Actualizo el archivo 'CURP' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoCDomicilio){
                    //Se actualizo comprobante de domicilio
                    $cambio = "Actualizo el archivo 'Comprobante de domicilio' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoSeguro){
                    //Se actualio seguro social
                    $cambio = "Actualizo el archivo 'Numero de seguro social' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoCV){
                    //Se actualizo documento de CV o solicitud
                    $cambio = "Actualizo el archivo 'Curriculum/Solicitud' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoTarjeta){
                    //Se solicito la tarjeta de pago
                    $cambio = "Actualizo el archivo 'Tarjeta pago' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoOtraTarjeta){
                    //Se actualizo otra tarjeta de pago
                    $cambio = "Actualizo el archivo 'Otra tarjeta pago' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoContactoEmergencia){
                    //Se actualizo contacto de emergencia
                    $cambio = "Actualizo el archivo 'Contacto de emergencia' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoContrato){
                    //Se actualizo el contrato laboral
                    $cambio = "Actualizo el archivo 'Contacto laboral' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoPagare){
                    //Se actualizo Pagare
                    $cambio = "Actualizo el archivo 'Pagare' del usuario: '" . $usuario[0]->name. "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }if($banderaActualizoSucursalesConfirmacion){
                    $cambio = "Actualizo lista de sucursales asignadas de usuario: '" . $usuario[0]->name . "'";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"4", "0");
                }

                if ($banderaActualizoIdFranquiciaPrincipal) {
                    //Se modifico la franquicia principal

                    DB::table('users')->where('id', $idUsuario)->update([
                        'id_franquiciaprincipal' => $idfranquiciaprincipalactualizar
                    ]);

                    //Validar si usuario tiene registro de asistencia con poliza del dia
                    $polizaGlobales::buscarasistenciapolizaeinsertar($idfranquiciaprincipalactualizar, $idUsuario);

                    $ciudadfranquiciaprincipalactual = DB::select("SELECT ciudad FROM franquicias WHERE id = '" . $usuario[0]->id_franquiciaprincipal . "' LIMIT 1");
                    $ciudadfranquiciaprincipalactualizar = DB::select("SELECT ciudad FROM franquicias WHERE id = '" . $idfranquiciaprincipalactualizar . "' LIMIT 1");
                    $ciudadfranquiciaprincipalactual = $ciudadfranquiciaprincipalactual == null ? "" : $ciudadfranquiciaprincipalactual[0]->ciudad;
                    $ciudadfranquiciaprincipalactualizar = $ciudadfranquiciaprincipalactualizar == null ? "" : $ciudadfranquiciaprincipalactualizar[0]->ciudad;
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC, "Actualizo la franquicia principal
                        del usuario: '" . $usuario[0]->name. "' de la sucursal '$ciudadfranquiciaprincipalactual' a '$ciudadfranquiciaprincipalactualizar'","4", "0");
                }if ($banderaActualizoCbSupervisorCobranza) {
                    //Se hizo actualizo a supervisor o cobrador normal
                    $mensaje = "de supervisor a cobrador normal";
                    if ($cbsupervisorcobranza == 1) {
                        $mensaje = "de cobrador normal a supervisor";
                    }
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC, "Actualizo $mensaje
                        al usuario: '" . $usuario[0]->name . "'","4", "0");
                }if ($banderaActualizoZonaCobrador) {
                    //Se actualizo la zona al cobrador
                    //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idUsuario
                    DB::delete("DELETE FROM contratostemporalessincronizacion where id_usuario = '$idUsuario'");
                    $contratosGlobal::insertarDatosTablaContratosTemporalesSincronizacion($idFranquicia, $idUsuario, $idZona, $rol);

                    $contratosGlobal::eliminarEInsertarAbonosContratosTemporalesSincronizacionPorUsuarios($idUsuario);

                    if ($supervisorcobranza == 0) {
                        //Se creo cobrador normal nuevo
                        $existeCobradorEliminadoZona = DB::select("SELECT id_usuario FROM cobradoreseliminados
                                                                        WHERE id_zona = '$idZona'");
                        $idCobradorEliminado = ""; //Actualizar registros polizacobranza (Por zona)
                        if ($existeCobradorEliminadoZona != null) {
                            //Actualizar registros polizacobranza (Por cobradoreliminado)
                            $idCobradorEliminado = $existeCobradorEliminadoZona[0]->id_usuario;
                        }
                        $contratosGlobal::actualizarRegistrosPolizaCobranzaPolizasAnteriores($idFranquicia, $idCobradorEliminado, $idUsuario, $idZona, true);
                    }
                    $nombreZonaAnterior = DB::select("SELECT zona FROM zonas WHERE id = '" . $usuario[0]->id_zona . "' LIMIT 1");
                    $nombreZonaActualizar = DB::select("SELECT zona FROM zonas WHERE id = '" . $idZona . "' LIMIT 1");
                    $nombreZonaAnterior = $nombreZonaAnterior == null ? "" : $nombreZonaAnterior[0]->zona;
                    $nombreZonaActualizar = $nombreZonaActualizar == null ? "" : $nombreZonaActualizar[0]->zona;
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC, "Actualizó de la zona $nombreZonaAnterior a zona $nombreZonaActualizar
                        al usuario: '" . $usuario[0]->name . "'","4", "0");

                    $mensaje = "";
                    if ($rol == 4 && $usuario[0]->supervisorcobranza == 0) {
                        //Rol cobrador y era cobrador normal

                        $cobradorsupervisor = DB::select("SELECT u.id as id, u.name as nombre, u.sueldo as sueldo, u.logueado as logueado FROM users u
                                                  INNER JOIN usuariosfranquicia uf
                                                  ON u.id = uf.id_usuario WHERE u.id_zona = '" . $usuario[0]->id_zona . "' AND u.supervisorcobranza = '1'");

                        $bandera = false;
                        if ($cobradorsupervisor != null) {
                            //Existe cobrador supervisor
                            $mensaje = "No se pudo realizar el cambio de cobrador supervisor a normal a " . $cobradorsupervisor[0]->nombre . " por algunas de las siguientes razones:
                                                    1- No se ha cerrado sesión, 2- No se le ha realizado el corte (Abonos sin corte)";
                            if ($cobradorsupervisor[0]->logueado == 0) {
                                //Tiene la sesion cerrada el cobrador supervisor
                                $abono = DB::select("SELECT indice FROM abonos WHERE id_usuario = '" . $cobradorsupervisor[0]->id . "' AND id_corte IS NULL ORDER BY created_at DESC LIMIT 1");
                                if ($abono == null) {
                                    //No existen abonos sin corte
                                    $bandera = true;
                                    $mensaje = "Se realizo el cambio de cobrador supervisor a normal a " . $cobradorsupervisor[0]->nombre;
                                }
                            }
                        }

                        if ($bandera) {
                            //Actualizar cobrador supervisor a cobrador normal
                            DB::table("users")
                                ->where("id", "=", $cobradorsupervisor[0]->id)
                                ->update([
                                    "supervisorcobranza" => "0"
                                ]);

                            //Actualizar registros polizacobranza
                            $contratosGlobal::actualizarRegistrosPolizaCobranzaPolizasAnteriores($idFranquicia, $idUsuario, $cobradorsupervisor[0]->id, $usuario[0]->id_zona, true);

                            //Agregar movimiento de sucursal
                            self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC, $mensaje,"4", "0");
                        }

                        if ($usuario[0]->id_zona != null) {
                            //Cobrador tenia una zona asignada
                            //Insertar registro en tabla cobradoreseliminados
                            DB::table('cobradoreseliminados')->insert([
                                'id_franquicia' => $idFranquicia,
                                'id_usuario' => $idUsuario,
                                'id_zona' => $usuario[0]->id_zona,
                                'created_at' => Carbon::now()
                            ]);
                        }

                    }

                }

                return back()->with('bien', 'Los datos del usuario se actualizaron correctamente.');
            }

            return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina.');

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function actualizarusuariozonafranquicia($idFranquicia, $idUsuario)
    {
        if ((Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8)) //ROL DEL DIRECTOR
        {
            $usuario = DB::select("SELECT * FROM users WHERE id = '$idUsuario'");

            if ($usuario != null) {
                //Existe usuario

                if ($usuario[0]->logueado == 0) {
                    //Ya se encuentra cerrada la sesion

                    $contratosGlobal = new contratosGlobal;
                    //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idUsuario
                    DB::delete("DELETE FROM contratostemporalessincronizacion where id_usuario = '$idUsuario'");
                    $contratosGlobal::insertarDatosTablaContratosTemporalesSincronizacion($idFranquicia, $idUsuario, $usuario[0]->id_zona, $usuario[0]->rol_id);

                    $contratosGlobal::eliminarEInsertarAbonosContratosTemporalesSincronizacionPorUsuarios($idUsuario);

                    return back()->with('bien', 'Los datos del usuario se actualizaron correctamente.');
                }
                //No se ha cerrado sesion
                return back()->with('alerta', "Para cambiar de zona es necesario que " . $usuario[0]->name . " cierre sesión.");

            }
            //No existe usuario
            return back()->with('alerta', "No existe el usuario");

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function actualizarsuariofranquiciadispositivo($idFranquicia, $idUsuario, $idDispositivo)
    {
        if (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) //ROL DEL DIRECTOR
        {
            $dispositivo = DB::select("SELECT estatus, modelo FROM dispositivosusuarios WHERE id_usuario = '$idUsuario' AND id = '$idDispositivo'");
            $usuarioDispositivo = DB::select("SELECT u.name FROM users u WHERE u.id = '$idUsuario'");
            if($dispositivo != null){//Existe el dispositivo

                $id_UsuarioC = Auth::user()->id;

                $estatus = 0;
                DB::table('dispositivosusuarios')->where('id_usuario', '=', $idUsuario)->update(['estatus' => $estatus]); //Desactivamos todos los dispositivos
                if($dispositivo[0]->estatus == 0){
                    $estatus = 1;
                }else{
                    //Si estatus dispositivo es igual a 1 -> Se desactivo el dispositivo
                    $cambio = "Desactivo dispositivo: '" . $dispositivo[0]->modelo . "' de: '" . $usuarioDispositivo[0]->name . "'";
                    $tipomensaje = "5";
                    //Registrar historialsucursal dispositivo desctivado
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC, $cambio, $tipomensaje, "0");

                }

                DB::update("UPDATE dispositivosusuarios SET estatus =  '$estatus' WHERE id_usuario = '$idUsuario' AND id = '$idDispositivo'");
                if($estatus == 1) {
                    //Se activo un nuevo dispositivo
                    DB::delete("DELETE FROM dispositivosusuarios WHERE id_usuario = '$idUsuario' AND estatus = '0'"); //Eliminar todos los dispositivos con estatus en 0

                    //Registrar historialsucursal dispositivo activado
                    $cambio = "Activo dispositivo: '" . $dispositivo[0]->modelo . "' de: '" . $usuarioDispositivo[0]->name . "'";
                    $tipomensaje = "5";
                    self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC, $cambio, $tipomensaje, "0");

                }

                return back()->with('bien', 'El estatus del dispositivo se actualizo correctamente.');
            }
            return back()->with('alerta', 'Dispositivo no encontrato.');

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function insumos($idFranquicia)
    {
        if (Auth::check() && (Auth::user()->rol_id) == 7) //ROL DEL DIRECTOR
        {

            $insumos = DB::select("SELECT * FROM insumos");
            return view('administracion.franquicia.administracion.insumos', ['insumos' => $insumos, 'idFranquicia' => $idFranquicia]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarinsumos()
    {
        if (Auth::check() && (Auth::user()->rol_id) == 7) //ROL DEL DIRECTOR
        {
            request()->validate([
                'preciom' => 'required|integer|min:0',
                'precioa' => 'required|integer|min:0',
                'preciob' => 'required|integer|min:0',
                'preciot' => 'required|integer|min:0',
                'precioe' => 'required|integer|min:0'
            ]);

            try {

                //Verificar que campo fue actualizado
                $insumos = DB::select("SELECT * FROM insumos");
                $id_UsuarioC = Auth::user()->id;

                if($insumos[0]->preciom != request('preciom')){
                    //Actualizo precio Mica
                    $cambio = "Actualizo precio para insumo mica de '" . $insumos[0]->preciom . "' a '" . request('preciom') . "'";
                    self::insertarHistorialSucursal("6E2AA",$id_UsuarioC,$cambio,"9","2");
                }if($insumos[0]->precioa != request('precioa')){
                    //Actualizo precio armazon
                    $cambio = "Actualizo precio para insumo armazon de '" . $insumos[0]->precioa . "' a '" . request('precioa') . "'";
                    self::insertarHistorialSucursal("6E2AA",$id_UsuarioC,$cambio,"9","2");
                }if($insumos[0]->preciob != request('preciob')){
                    //Actualizo precio Bicce
                    $cambio = "Actualizo precio para insumo bicce de '" . $insumos[0]->preciob . "' a '" . request('preciob') . "'";
                    self::insertarHistorialSucursal("6E2AA",$id_UsuarioC,$cambio,"9","2");
                }if($insumos[0]->preciot != request('preciot')){
                    //Actualizo precio T
                    $cambio = "Actualizo precio para insumo T de '" . $insumos[0]->preciot . "' a '" . request('preciot') . "'";
                    self::insertarHistorialSucursal("6E2AA",$id_UsuarioC,$cambio,"9","2");
                } if($insumos[0]->precioe != request('precioe')){
                    //Actualizo precio Estuche
                    $cambio = "Actualizo precio para insumo estuche de '" . $insumos[0]->precioe . "' a '" . request('precioe') . "'";
                    self::insertarHistorialSucursal("6E2AA",$id_UsuarioC,$cambio,"9","2");
                }

                DB::table('insumos')->where('id', '=', '1')->update([
                    'preciom' => request('preciom'), 'precioa' => request('precioa'), 'preciob' => request('preciob'), 'preciot' => request('preciot'),
                    'precioe' => request('precioe')
                ]);
                return redirect()->route('listafranquicia')->with('bien', 'Los insumos se actualizaron correctamente.');
            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function descargarArchivo($idUsuario, $opcion)
    {
        if (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) //ROL DEL DIRECTOR
        {
            $existeUsuario = DB::select("SELECT foto,actanacimiento,identificacion,curp,comprobantedomicilio,segurosocial,solicitud,tarjetapago,contactoemergencia,contratolaboral,
                                                      pagare,otratarjetapago FROM users WHERE id = '$idUsuario'");
            if ($existeUsuario != null) { //Existe el usuario?
                //Si existe el usuario
                if ($opcion != null && $opcion >= 0 && $opcion <= 11) {
                    $archivo = '';

                    if(self::descomprimirArchivoUsuario($idUsuario, $opcion) == true){

                        switch ($opcion) {
                            case 0://Foto
                                $archivo = $existeUsuario[0]->foto;
                                break;
                            case 1:
                                $archivo = $existeUsuario[0]->actanacimiento;
                                break;
                            case 2:
                                $archivo = $existeUsuario[0]->identificacion;
                                break;
                            case 3:
                                $archivo = $existeUsuario[0]->curp;
                                break;
                            case 4:
                                $archivo = $existeUsuario[0]->comprobantedomicilio;
                                break;
                            case 5:
                                $archivo = $existeUsuario[0]->segurosocial;
                                break;
                            case 6:
                                $archivo = $existeUsuario[0]->solicitud;
                                break;
                            case 7:
                                $archivo = $existeUsuario[0]->tarjetapago;
                                break;
                            case 8:
                                $archivo = $existeUsuario[0]->contactoemergencia;
                                break;
                            case 9:
                                $archivo = $existeUsuario[0]->contratolaboral;
                                break;
                            case 10:
                                $archivo = $existeUsuario[0]->pagare;
                                break;
                            case 11: // Nuevo archivo otratarjeta
                                $archivo = $existeUsuario[0]->otratarjetapago;
                                break;
                        }
                        return Storage::disk('disco')->download($archivo);

                    } else {
                        return back()->with('alerta', 'No se encontro el archivo.');
                    }

                } else {
                    return back()->with('alerta', 'No se encontro el archivo.');
                }

            } else {
                //No existe el usuario

                return back()->with('alerta', 'No se encontro el usuario.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function usuariosfranquiciatiemporeal(Request $request) {

        if (strlen($request->input('filtro') > 0)) {
            //Filtro diferente de vacio
            $filtro = $request->input('filtro');
            $usuariosSinFranquicia = DB::select("SELECT u.id as ID, u.name as NOMBRE, u.email as CORREO, (SELECT r.rol FROM roles r WHERE r.id = rol_id) AS ROL
                                                        FROM users u WHERE  u.rol_id <> 19 AND u.name LIKE '%" . $filtro . "%'
                                                        AND u.id NOT IN (SELECT id_usuario FROM usuariosfranquicia WHERE u.id = id_usuario) ORDER BY u.name");
            $response = ['data' => $usuariosSinFranquicia];
            $cambio = "Filtro usuarios por: '" .$request->input('filtro'). "'";
            //Registrar filtro de usuarios franquicia en historialsucursal
            $idFranquicia =  $request->input('id_franquicia');
            DB::table('historialsucursal')->insert([
                'id_usuarioC' => Auth::user()->id,
                'id_franquicia' => $idFranquicia, 'tipomensaje' => '7',
                'created_at' => Carbon::now(), 'cambios' => $cambio, 'seccion' => '0'
            ]);
        } else {
            //Filtro vacio
            $usuariosSinFranquicia = DB::select("SELECT u.id as ID, u.name as NOMBRE, u.email as CORREO, (SELECT r.rol FROM roles r WHERE r.id = rol_id) AS ROL
                                                        FROM users u WHERE u.rol_id <> 19
                                                        AND u.id NOT IN (SELECT id_usuario FROM usuariosfranquicia WHERE u.id = id_usuario) ORDER BY u.name");
            $response = ['data' => $usuariosSinFranquicia];
        }
        return response()->json($response);
    }

    public function usuariosfiltrosucursal($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7)) {

            $contratosGlobal = new contratosGlobal;
            if($contratosGlobal::validarPermisoSeccion(Auth::user()->id, 0, 1)){
                $sucursalSeleccionada = request('sucursalSeleccionada');

                $ahora = Carbon::now();

                if(strlen($sucursalSeleccionada) > 0) {
                    //Se eligio una sucursal
                    $usuariosfranquicia = DB::select("SELECT u.id as ID, u.foto AS FOTO, u.name AS NOMBRE,u.email AS CORREO, r.rol AS ROL ,u.renovacion, u.codigoasistencia AS NOCONTROL,
                                                              uf.id_franquicia as ID_FRANQUICIA, (SELECT ciudad FROM franquicias WHERE id = uf.id_franquicia) as CIUDADFRANQUICIA,
                                                              u.supervisorcobranza as SUPERVISORCOBRANZA, u.created_at AS FECHACREACION,
                                                              STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d') AS FECHACUMPLEANIOS,
                                                              u.actanacimiento AS ACTANACIMIENTO, u.identificacion AS INE, u.curp AS CURP, u.segurosocial AS SEGURO,
                                                              u.solicitud AS CV, u.tarjetapago AS TARJETA, u.otratarjetapago AS OTRATARJETA, u.contactoemergencia AS CONTACTO,
                                                              u.contratolaboral AS CONTRATO, u.pagare AS PAGARE, (SELECT z.zona FROM zonas z WHERE z.id = u.id_zona) as ZONA,
                                                              DATEDIFF(STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d'), '$ahora') AS DIASPARACUMPLEANIOS,
                                                              u.ultimaconexion AS ULTIMACONEXION
                                                              FROM users u
                                                              INNER JOIN roles r ON r.id = u.rol_id
                                                              INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                              WHERE uf.id_franquicia = '$sucursalSeleccionada' ORDER BY u.name");

                    $totalRenovacion = DB::select("SELECT COUNT(u.id) AS total FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id WHERE u.renovacion IS NOT NULL
                                                        AND  uf.id_franquicia= '$sucursalSeleccionada' AND  STR_TO_DATE(u.renovacion,'%Y-%m-%d') <= ' $ahora'");

                    $nombreSucursalSeleccionada = DB::select("SELECT f.ciudad FROM franquicias f WHERE f.id = '$sucursalSeleccionada'");
                    $cambio = "Filtro usuarios por sucursal: '".$nombreSucursalSeleccionada[0]->ciudad . "'";
                }else {
                    //Todas las sucursales
                    if($idFranquicia == '00000') {
                        //Franquicia de prueba
                        $usuariosfranquicia = DB::select("SELECT u.id as ID, u.foto AS FOTO, u.name AS NOMBRE,u.email AS CORREO, r.rol AS ROL ,u.renovacion, u.codigoasistencia AS NOCONTROL,
                                                                   uf.id_franquicia as ID_FRANQUICIA, (SELECT ciudad FROM franquicias WHERE id = uf.id_franquicia) as CIUDADFRANQUICIA,
                                                                   u.supervisorcobranza as SUPERVISORCOBRANZA, u.created_at AS FECHACREACION,
                                                                   STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d') AS FECHACUMPLEANIOS,
                                                                   u.actanacimiento AS ACTANACIMIENTO, u.identificacion AS INE, u.curp AS CURP, u.segurosocial AS SEGURO,
                                                                   u.solicitud AS CV, u.tarjetapago AS TARJETA, u.otratarjetapago AS OTRATARJETA, u.contactoemergencia AS CONTACTO,
                                                                   u.contratolaboral AS CONTRATO, u.pagare AS PAGARE, (SELECT z.zona FROM zonas z WHERE z.id = u.id_zona) as ZONA,
                                                                   DATEDIFF(STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d'), '$ahora') AS DIASPARACUMPLEANIOS,
                                                                   u.ultimaconexion AS ULTIMACONEXION
                                                                   FROM users u
                                                                   INNER JOIN roles r ON r.id = u.rol_id
                                                                   INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id ORDER BY u.name");

                        $totalRenovacion = DB::select("SELECT COUNT(u.id) AS total FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id WHERE u.renovacion IS NOT NULL
                                                            AND STR_TO_DATE(u.renovacion,'%Y-%m-%d') <= ' $ahora'");

                        $cambio = "Filtro usuarios por sucursal: 'Pruebas'";
                    }else {
                        //Otra franquicia diferente a la de prueba
                        $usuariosfranquicia = DB::select("SELECT u.id as ID, u.foto AS FOTO, u.name AS NOMBRE,u.email AS CORREO, r.rol AS ROL ,u.renovacion, u.codigoasistencia AS NOCONTROL,
                                                                  uf.id_franquicia as ID_FRANQUICIA, (SELECT ciudad FROM franquicias WHERE id = uf.id_franquicia) as CIUDADFRANQUICIA,
                                                                  u.supervisorcobranza as SUPERVISORCOBRANZA, u.created_at AS FECHACREACION,
                                                                  STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d') AS FECHACUMPLEANIOS,
                                                                   u.actanacimiento AS ACTANACIMIENTO, u.identificacion AS INE, u.curp AS CURP, u.segurosocial AS SEGURO,
                                                                   u.solicitud AS CV, u.tarjetapago AS TARJETA, u.otratarjetapago AS OTRATARJETA, u.contactoemergencia AS CONTACTO,
                                                                   u.contratolaboral AS CONTRATO, u.pagare AS PAGARE, (SELECT z.zona FROM zonas z WHERE z.id = u.id_zona) as ZONA,
                                                                   DATEDIFF(STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(u.fechanacimiento, '%m-%d')),'%Y-%m-%d'), '$ahora') AS DIASPARACUMPLEANIOS,
                                                                   u.ultimaconexion AS ULTIMACONEXION
                                                                   FROM users u
                                                                   INNER JOIN roles r ON r.id = u.rol_id
                                                                   INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                                   WHERE uf.id_franquicia != '00000' ORDER BY u.name");

                        $totalRenovacion = DB::select("SELECT COUNT(u.id) AS total FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id WHERE u.renovacion IS NOT NULL
                                                            AND  uf.id_franquicia != '00000' AND  STR_TO_DATE(u.renovacion,'%Y-%m-%d') <= ' $ahora'");

                        $cambio = "Filtro usuarios por: 'Todas las sucursal'";
                    }
                }

                $franquicia = DB::select("SELECT * FROM franquicias WHERE id='$idFranquicia'");
                $sucursales = DB::select("SELECT id,estado,ciudad,colonia,numero FROM franquicias WHERE id != '00000'");
                $roles = DB::select("SELECT * FROM roles WHERE id <> 7");
                $zonas = DB::select("SELECT id,zona FROM zonas WHERE id_franquicia = '$idFranquicia'");

                //Registrar filtro de usuarios en historialsucursal
                DB::table('historialsucursal')->insert([
                    'id_usuarioC' => Auth::user()->id,
                    'id_franquicia' => $idFranquicia, 'tipomensaje' => '7',
                    'created_at' => Carbon::now(), 'cambios' => $cambio, 'seccion' => '0'
                ]);

                return view('administracion.franquicia.usuarios', ['usuariosfranquicia' => $usuariosfranquicia,
                    'franquicia' => $franquicia,
                    'roles' => $roles,
                    'id' => $idFranquicia,
                    'zonas' => $zonas,
                    'sucursales' => $sucursales,
                    'idFranquicia' => $idFranquicia,
                    'totalRenovacion' => $totalRenovacion,
                    'sucursalSeleccionada' => $sucursalSeleccionada
                ]);

            }else{
                return back()->with('alerta', 'No cuentas con los permisos necesarios.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarControlEntradaSalidaUsuarioFranquicia($idFranquicia, $idUsuario, Request $request)
    {
        if (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) //ROL DEL DIRECTOR
        {
            $horaini = request()->horaini;
            $horafin = request()->horafin;

            if($horaini != null && $horafin != null) {
                //horaini y horafin son diferentes de vacio

                if ($horaini > $horafin) {
                    return back()->with('alerta', 'El horario inicial/final no puede ser mayor/menor al horario final/inicial.');
                }

                if(strlen($horaini) <= 5) {
                    $horaini = $horaini . ":00";
                }

                if(strlen($horafin) <= 5) {
                    $horafin = $horafin . ":00";
                }

                DB::table('controlentradasalidausuario')->where('id_usuario', $idUsuario)->update([
                    'horaini' => $horaini,
                    'horafin' => $horafin,
                    'updated_at' => Carbon::now()
                ]);

                //Registrar movimiento en historialsucursal
                $id_UsuarioC = Auth::user()->id;
                $nombreUsuario = DB::select("SELECT u.name FROM users u WHERE u.id = '$idUsuario'");
                $cambio = "Actualizo el horario laboral de: '" .$nombreUsuario[0]->name . "'";
                self::insertarHistorialSucursal($idFranquicia, $id_UsuarioC,$cambio,"6", "0");

                return back()->with('bien', 'Se actualizaron los horarios del usuario correctamente.');

            }

            return back()->with('alerta', 'El horario inicial o final estan vacios.');

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    /* -----------------------------Funciones para crear archivos ZIP al crear un nuevo usuario o actualizar -------------------*/

    //Funcion: comprimirArchivosUsuarios
    //Descripcion: Sube las imagenes adjuntas al formulario al servidor y porteriormente general el zip
    //Cuando se actualiza se rescribe dentro del archivo zip correspondiente al usuario editado

    public function comprimirArchivosUsuarios($idUsuario, $archivosComprimir, $opcion){

        //Opcion
        // Opcion 0 -> Crear zip por priemra vez e insertar todos los archivos enviados en el
        // Opcion 1 -> Actualizar archivos en zip, sobre-escribir

        switch ($opcion){
            case 0:
                //Crear Zip primera vez
                //Crear, inicializar y abrir un archivo zip nuevo (Flags: Si no existe el archivo crea uno nuevo)
                $zip = new ZipArchive;
                $zip->open('uploads/imagenes/usuarios/zip/'.'Archivos-Usuario'.'-'.$idUsuario.'.zip', ZipArchive::CREATE);

                //Recorremos el arreglo de archivos
                foreach ($archivosComprimir as $archivo){
                    //Agregamos cada uno de los archivos al archivo zip
                    $zip->addFile(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $archivo, ''.$archivo);
                }

                //Cerrar archivo zip
                $zip->close();

                //Se recorren nuevamente los archivos y se eliminan para solo dejar el archivo zip
                foreach ($archivosComprimir as $archivo){
                    unlink(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $archivo);
                }
                break;

            case 1:
                //Actualizar Archivos en zip

                if($archivosComprimir != null || sizeof($archivosComprimir) > 0){
                    //Si se traen archivos tipo pdf o jpg a incluir en el zip
                    $zip = new ZipArchive;

                    //Validar si el usuario tiene archivo ZIP creado
                    if(file_exists('uploads/imagenes/usuarios/zip/'.'Archivos-Usuario'. '-' .$idUsuario. '.zip')){
                        //Si existe el directorio lo abrimos solamente
                        $zip->open('uploads/imagenes/usuarios/zip/'.'Archivos-Usuario'.'-'.$idUsuario.'.zip');
                    } else {
                        //Si no existe el archivo lo creamos y abrimos
                        $zip->open('uploads/imagenes/usuarios/zip/'.'Archivos-Usuario'. '-' .$idUsuario. '.zip', ZipArchive::CREATE);
                    }

                    //Recorremos el arreglo de archivos
                    foreach ($archivosComprimir as $archivo){
                        //Agregamos cada uno de los archivos al archivo zip
                        $zip->addFile(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $archivo, ''.$archivo);
                    }

                    //Es necesario cerrar el archivo zip? -> sistema en el servidor?
                    if(env('ZIP_CLOSE','') == true){
                        //Valor es  -> accion ejecutada desde el servidor
                        $zip->close();
                    }

                    //Se recorren nuevamente los archivos y se eliminan para solo dejar el archivo zip
                    foreach ($archivosComprimir as $archivo){
                        unlink(config('filesystems.disks.disco.root') . '/uploads/imagenes/usuarios/' . $archivo);
                    }
                }
                break;

        }


    }

    //Fucnion: descomprimirArchivoUsuario
    //Descripcion: Descomprime un archivo en especifico referente a un usuario para posteriormente descargarlo
    public function  descomprimirArchivoUsuario($idUsuario, $opcion){
        //Verificamos que existe el usuario y contenga el archivo a descomprimir
        $verificarArchivo = self::verificarExisteArchivoBD($idUsuario,$opcion);
        $nombreArchivo = null;
        $bandera = false;
        $resultado = "";

        if($verificarArchivo != null || sizeof($verificarArchivo)>0){
            //si el resultado fue diferente de null
            $bandera = $verificarArchivo[0];
            $archivoDescomprimir = $verificarArchivo[1];

            //Verificamos si es una ruta antigua o un usuario de nuevo ingreso con nueva ruta de almacenamiento
            $archivo= explode("/", $archivoDescomprimir);

            if(sizeof($archivo) > 1){
                //Si tenia una subcarpeta en la ruta
                $nombreArchivo = $archivo[sizeof($archivo)-1]; // Extraemos solo el nombre del archivo a descomprimir
            } else {
                //Si no tiene subcarpetas es una ruta actual entoces dejamos el nombre recibido
                $nombreArchivo = $archivoDescomprimir;
            }
        }

        if($bandera){
            //Si bandera es verdadera -> el usuario tiene registrado su docuemnto correcatemente

            $zip = new ZipArchive();

            if($zip->open('uploads/imagenes/usuarios/zip/'.'Archivos-Usuario'. '-' .$idUsuario. '.zip') === true){

                $resultado =  $zip->extractTo('uploads/imagenes/usuarios', ''.$nombreArchivo); //Extraemos el archivo que fue seleccionado
                $zip->close(); //Cerrar el archivo zip

                //Existe el archivo
                if($resultado === true){
                    return true; //Retornamos verdadero una vez que todo fue correcto
                }
            }
        }

        return false; //Si no existe el archivo retornamos falso

    }

    //Funcion: verificarExisteArchivoBD
    //Descripcion: Antes de intentar descargar el archivo se comprueba que exista ese documento para el usuario deseado
    public function verificarExisteArchivoBD($idUsuario, $opcion){
        //Almacenara la bandera true y el nombre del archivo en caso de que exista.
        $resultado = [];

        $existeArchivo = DB::select("SELECT actanacimiento,identificacion,curp,comprobantedomicilio,segurosocial,solicitud,tarjetapago,contactoemergencia,contratolaboral,pagare,otratarjetapago
                                            FROM users WHERE id = '$idUsuario'");
        switch ($opcion){
            //Casos: Verifican que tenga el registro de la BD no sea nulo y a demas que corresponda a la ruta de almacenamiento
            //Si es valido extraemos solo el nombre del archivo el cual se descomprime despues

            case 1:
                //Acta de nacimiento
                if($existeArchivo[0]->actanacimiento != null && str_contains($existeArchivo[0]->actanacimiento , 'uploads/imagenes/usuarios/')){
                    $resultado = [true,substr($existeArchivo[0]->actanacimiento,26)];
                }
                break;
            case 2:
                //Identificacion
                if($existeArchivo[0]->identificacion != null && str_contains($existeArchivo[0]->identificacion , 'uploads/imagenes/usuarios/')){
                    $resultado = [true,substr($existeArchivo[0]->identificacion,26)];
                }
                break;
            case 3:
                //CURP
                if($existeArchivo[0]->curp != null && str_contains($existeArchivo[0]->curp , 'uploads/imagenes/usuarios/')){
                    $resultado = [true,substr($existeArchivo[0]->curp,26)];
                }
                break;
            case 4:
                //Comprobante de domicilio
                if($existeArchivo[0]->comprobantedomicilio != null && str_contains($existeArchivo[0]->comprobantedomicilio , 'uploads/imagenes/usuarios/')){
                    $resultado = [true,substr($existeArchivo[0]->comprobantedomicilio,26)];
                }
                break;
            case 5:
                //Seguro Social
                if($existeArchivo[0]->segurosocial != null && str_contains($existeArchivo[0]->segurosocial , 'uploads/imagenes/usuarios/')){
                    $resultado = [true,substr($existeArchivo[0]->segurosocial,26)];
                }
                break;
            case 6:
                //Seguro Social
                if($existeArchivo[0]->solicitud != null && str_contains($existeArchivo[0]->solicitud , 'uploads/imagenes/usuarios/')){
                    $resultado = [true,substr($existeArchivo[0]->solicitud,26)];
                }
                break;
            case 7:
                //Tarje de Pago
                if($existeArchivo[0]->tarjetapago != null && str_contains($existeArchivo[0]->tarjetapago , 'uploads/imagenes/usuarios/')){
                    $resultado = [true,substr($existeArchivo[0]->tarjetapago,26)];
                }
                break;
            case 8:
                //Contacto de emergencia
                if($existeArchivo[0]->contactoemergencia != null && str_contains($existeArchivo[0]->contactoemergencia , 'uploads/imagenes/usuarios/')){
                    $resultado = [true,substr($existeArchivo[0]->contactoemergencia,26)];
                }
                break;
            case 9:
                //Contrato laboral
                if($existeArchivo[0]->contratolaboral != null && str_contains($existeArchivo[0]->contratolaboral,'uploads/imagenes/usuarios/')){
                    $resultado = [true,substr($existeArchivo[0]->contratolaboral,26)];
                }
                break;
            case 10:
                //Pagare
                if($existeArchivo[0]->pagare != null && str_contains($existeArchivo[0]->pagare,'uploads/imagenes/usuarios/')){
                    $resultado = [true,substr($existeArchivo[0]->pagare,26)];
                }
                break;
            case 11:
                //Otra tarjeta
                if($existeArchivo[0]->otratarjetapago != null && str_contains($existeArchivo[0]->otratarjetapago, 'uploads/imagenes/usuarios/')){
                    $resultado = [true,substr($existeArchivo[0]->otratarjetapago,26)];
                }
                break;

        }
        return $resultado;

    }

    /*-------------------Funciones para vistas previas de archivos en ventana Ver Usuario-------------------------*/

    //Funcion: descomprimirZipUsuario
    //Descripcion: extraer un archivo dentro del zip por medio de un parametro recibido, es mandado a llamar cuando se desea hacer la vista previa del archivo
    public function descomprimirZipUsuario(Request $request){
        if (Auth::user()->rol_id == 7 ){
            //ROL DEL DIRECTOR

            $idUsuario = $request->input('idUsuario');
            $archivoDescomprimir = $request->input('archivoServidorFTP');

            //Obtenemos el nombre del archivo a descomprimir
            $archivo = explode("/", $archivoDescomprimir);
            $nombreArchivo = $archivo[sizeof($archivo)-1];

            //Validar si existe el archivo ZIP
            if(file_exists('uploads/imagenes/usuarios/zip/'.'Archivos-Usuario'. '-' .$idUsuario. '.zip')){
                //Si existe archivo zip
                $zip = new ZipArchive();
                $zip->open('uploads/imagenes/usuarios/zip/'.'Archivos-Usuario'. '-' .$idUsuario. '.zip', ZipArchive::CREATE); // Abrimos el archivo ZIP correspondiente al usuario
                $zip->extractTo('uploads/imagenes/usuarios/', ''.$nombreArchivo); //Extraemos el archivo deseado
                $zip->close(); //Cerrar el archivo zip

                if(file_exists('uploads/imagenes/usuarios/' . $nombreArchivo)){
                    //Verificamos que exista la imagen
                    $bandera = true;
                } else {
                    //Si existe el archivo zip pero no el archivo
                    $bandera = false;
                }

            }else {
                //Si no existe el archivo zip
                $bandera = false;
            }

            //Retornamos nuestra bandera con el resultado si es correcto o no
            return response()->json(['bandera' => $bandera]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    /* Funcion global para registro de movimiento en historialsucursal*/
    public function insertarHistorialSucursal($idFranquicia, $idUsuarioC, $cambio, $tipomensaje, $seccion){
        DB::table('historialsucursal')->insert([
            'id_usuarioC' => $idUsuarioC, 'id_franquicia' => $idFranquicia,
            'tipomensaje' => $tipomensaje, 'created_at' => Carbon::now(), 'cambios' => $cambio, 'seccion' => $seccion
        ]);
    }

    public function asignarDenegarPermisosUsuarios($id_Usuario,$id_seccion, $id_permiso){
        if(Auth::check() && (Auth::user()->id == 1 || Auth::user()->id  == 19 || Auth::user()->id  == 376 || Auth::user()->id  == 61))
        {
            //Usuarios con acceso: Christian Arcadia, Alan Irving, Fernando Carrillo, Sergio Lopez
            $contratosGlobal = new contratosGlobal();
            //Validacion de usuario al que se le asignara el permiso
            $existeUsuario = DB::select("SELECT * FROM users u WHERE u.id = '$id_Usuario'");

            if($existeUsuario){
                //Existe el usuario
                if($existeUsuario[0]->rol_id == 6 || $existeUsuario[0]->rol_id == 7 || $existeUsuario[0]->rol_id == 8){
                    //El usuario pertenece a rol de administracion, Director o Principal

                    //Verificar si se quiere activar o desactivar permiso
                    $permisoActivado = DB::select("SELECT * FROM permisosusuarios p WHERE p.id_usuario = '$id_Usuario'
                                                            AND p.id_seccion = '$id_seccion' AND p.id_permiso = '$id_permiso'");

                    //Existe permiso para el usuario?
                    if($permisoActivado){
                        //Si existe el registro - Entonces desactivarlo (Accion a realizar: Desactivar)

                        //Validar tipo de permiso a desactivar
                        if($permisoActivado[0]->id_permiso == 1){
                            //Si el permiso es Leer - Debemos desactivar todos los permisos de la seccion
                            DB::delete("DELETE FROM permisosusuarios WHERE id_usuario = '$id_Usuario' AND id_seccion = '$id_seccion'");
                        }else {
                            //Si es un permiso de tipo crear, eliminar o actualizar - solo desactivamos el correspondiente
                            DB::delete("DELETE FROM permisosusuarios WHERE id_usuario = '$id_Usuario' AND id_seccion = '$id_seccion' AND id_permiso = '$id_permiso'");
                        }
                    } else {
                        //No existe ningun permiso de ese tipo para el usuario - Activarlo

                        //Deseas activar un permiso distinto a leer?
                        if($id_permiso != 1){
                            //Permiso es Actualizar, Crear o Eliminar - Verificar que primeramente tener permiso de Leer para la seccion

                            //Tiene permiso Leer?
                            if($contratosGlobal::validarPermisoSeccion($id_Usuario, $id_seccion, "1")){
                                //Ya cuenta con el permiso - insertar nuevo permiso en tabla permisosusuarios
                                $contratosGlobal::crearPermisoSeccion($id_Usuario, $id_seccion, $id_permiso);

                            } else {
                                //No se le asigno permiso de Leer
                                return back()->with('alerta','El usuario no cuenta con permiso de Leer.');
                            }
                        }else {
                            //Permiso a asignar es: Leer
                            $contratosGlobal::crearPermisoSeccion($id_Usuario, $id_seccion, $id_permiso);
                        }

                    }

                    return back()->with('bien',' Permiso actualizado correctamente.');

                } else {
                    return back()->with('alerta','No puedes asignar permisos a el usuario actual.');
                }

            } else {
                //No existe el usuario
                return back()->with('alerta','El usuario no existe.');
            }
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }


    function listavacantesadministracion($idFranquicia){
        if (Auth::check() && (Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)) {
            $sucursales = null;

            $roles = DB::select("SELECT * FROM roles r WHERE r.id NOT IN (1,2,3,7,19) ORDER BY r.rol ASC");
            $now = Carbon::now();
            $fechaInicial = Carbon::parse($now)->subWeek()->format('Y-m-d');
            $fechaFinal = Carbon::parse($now)->format('Y-m-d');
            $cbFechaCreacion = null;
            $cbFechaCita = null;
            $rolSeleccionado = null;

            if(Auth::user()->rol_id == 7){
                //Rol de director - Consultar lista de franquicias
                $sucursales = DB::select("SELECT * FROM franquicias f WHERE f.id != '00000' ORDER BY f.ciudad ASC");

                //Extraer todas las solicitudes agendadas y notificadas con asistencia
                $listaSolicitudesPendientes = DB::select("SELECT v.nombresolicitante, v.fechacita, v.observaciones, v.estado, v.id_franquicia,
                                                                v.indice, v.horacita, v.created_at, v.telefono, v.observacionessolicitud, v.curriculum,
                                                               (SELECT r.rol FROM roles r WHERE r.id = v.id_rol) AS rol,
                                                               (SELECT f.ciudad FROM franquicias f WHERE f.id = v.id_franquicia) AS ciudad FROM vacantes v
                                                                WHERE v.id_franquicia != '00000' AND v.estado IN (1, 3, 8)
                                                                ORDER BY v.estado ASC, v.created_at DESC");

                //Extraer solicitudes de la ultima semana que han sido canceladas, contratados y rechazados
                $listaSolicitudesNotificadas = DB::select("SELECT v.nombresolicitante, v.fechacita, v.observaciones, v.estado, v.id_franquicia,
                                                                v.indice, v.horacita, v.created_at, v.telefono, v.observacionessolicitud, v.curriculum,
                                                               (SELECT r.rol FROM roles r WHERE r.id = v.id_rol) AS rol,
                                                               (SELECT f.ciudad FROM franquicias f WHERE f.id = v.id_franquicia) AS ciudad FROM vacantes v
                                                                WHERE v.id_franquicia != '00000' AND v.estado IN (2,4,6,7)
                                                                AND (STR_TO_DATE(v.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicial','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d')
                                                                    OR STR_TO_DATE(v.updated_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicial','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d'))
                                                                ORDER BY v.estado ASC, v.created_at DESC");

                $listaSolicitudesGeneradas = array_merge($listaSolicitudesPendientes, $listaSolicitudesNotificadas);

                //Movimientos
                $movimientoVacantes = DB::select("SELECT hs.cambios, hs.created_at, (SELECT u.name FROM users u WHERE u.id = hs.id_usuarioC) as usuario FROM historialsucursal hs
                                                    WHERE hs.tipomensaje = 10 AND hs.seccion = 0 AND hs.id_franquicia != '00000'
                                                    AND STR_TO_DATE(hs.created_at,'%Y-%m-%d') = STR_TO_DATE('$fechaFinal','%Y-%m-%d')
                                                    ORDER BY hs.created_at DESC");
            } else {
                //Rol de administracion y principal - Consultar solicitudes pendientes solo de su sucursal
                $sucursales = DB::select("SELECT * FROM franquicias f WHERE f.id = '$idFranquicia' ORDER BY f.ciudad ASC");

                $listaSolicitudesPendientes = DB::select("SELECT v.nombresolicitante, v.fechacita, v.observaciones, v.estado, v.id_franquicia,
                                                                v.indice, v.horacita,v.created_at, v.telefono, v.observacionessolicitud, v.curriculum,
                                                               (SELECT r.rol FROM roles r WHERE r.id = v.id_rol) AS rol,
                                                               (SELECT f.ciudad FROM franquicias f WHERE f.id = v.id_franquicia) AS ciudad FROM vacantes v
                                                                WHERE v.id_franquicia = '$idFranquicia' AND v.estado IN (1,8)
                                                                ORDER BY v.created_at DESC");

                $listaSolicitudesNotificadas = DB::select("SELECT v.nombresolicitante, v.fechacita, v.observaciones, v.estado, v.id_franquicia,
                                                                v.indice, v.horacita, v.created_at, v.telefono, v.observacionessolicitud, v.curriculum,
                                                               (SELECT r.rol FROM roles r WHERE r.id = v.id_rol) AS rol,
                                                               (SELECT f.ciudad FROM franquicias f WHERE f.id = v.id_franquicia) AS ciudad FROM vacantes v
                                                                WHERE v.id_franquicia = '$idFranquicia' AND v.estado IN (2,3,4)
                                                                AND (STR_TO_DATE(v.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicial','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d')
                                                                    OR STR_TO_DATE(v.updated_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicial','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d'))
                                                                ORDER BY v.created_at DESC");

                $listaSolicitudesGeneradas = array_merge($listaSolicitudesPendientes, $listaSolicitudesNotificadas);

                //Movimientos
                $movimientoVacantes = DB::select("SELECT hs.cambios, hs.created_at, (SELECT u.name FROM users u WHERE u.id = hs.id_usuarioC) as usuario FROM historialsucursal hs
                                                    WHERE hs.tipomensaje = 10 AND hs.seccion = 0 AND hs.id_franquicia = '$idFranquicia'
                                                    AND STR_TO_DATE(hs.created_at,'%Y-%m-%d') = STR_TO_DATE('$fechaFinal','%Y-%m-%d')
                                                    ORDER BY hs.created_at DESC");
            }

            //Horarios de atencion
            $horarioAtencion = DB::select("SELECT f.horaatencioninicio, f.horaatencionfin FROM franquicias f WHERE f.id = '$idFranquicia'");

            //Lista de solicitudes
            $solicitudesRol = array();
            foreach ($sucursales as $sucursal){
                $id_sucursal = $sucursal->id;
                $solicitudes = DB::select("SELECT f.id, r.id, r.rol, (SELECT COUNT(v.indice) FROM vacantes v
                                                WHERE v.id_franquicia = f.id AND v.id_rol = r.id AND v.estado = 0) AS numeroSolicitudes
                                                FROM franquicias f
                                                INNER JOIN roles r
                                                WHERE f.id = '$id_sucursal' AND r.id NOT IN (1,2,3,7,19)
                                                GROUP BY f.id, r.id, r.rol
                                                ORDER BY f.ciudad ASC, r.rol ASC");

                array_push($solicitudesRol,$solicitudes);
            }

            return view('administracion.franquicia.vacantes.tablavacantesadministracion', [
                'idFranquicia' => $idFranquicia,
                'sucursales' => $sucursales,
                'roles' => $roles,
                'solicitudesRol' => $solicitudesRol,
                'listaSolicitudesGeneradas' => $listaSolicitudesGeneradas,
                'fechaFiltrar' => $fechaFinal,
                'movimientoVacantes' => $movimientoVacantes,
                'horarioAtencion' => $horarioAtencion,
                'franquiciaSeleccionada' => $idFranquicia,
                'rolSeleccionado' => $rolSeleccionado,
                'cbFechaCreacion' => $cbFechaCreacion,
                'cbFechaCita' => $cbFechaCita
            ]);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function filtrarsolicitudesvacantesadmin($idFranquicia, Request $request){
        if(Auth::check() && (Auth::user()->rol_id  == 6 || Auth::user()->rol_id  == 7 || Auth::user()->rol_id  == 8))
        {
            $now = Carbon::now();
            $franquiciaSeleccionada = (Auth::user()->rol_id  == 7)? $request->input('franquiciaSeleccionada') : $idFranquicia;
            $rolSeleccionado = $request->input('rolSeleccionado');
            $fechaInicial =Carbon::parse($request->input('fechaFiltroSolicitud'))->format('Y-m-d');
            $fechaFinal = Carbon::parse($now)->format('Y-m-d');
            $cbFechaCreacion = $request->input('cbFechaCreacion');
            $cbFechaCita = $request->input('cbFechaCita');
            $cadenaFranquiciaSolicitudes = "";
            $cadenaFranquiciaMovimientos = "";
            $cadenaRol = "";
            $cadenaFecha = "";

            //Validar fecha
            if($fechaInicial > $fechaFinal){
                return back()->with('alerta',' La fecha en filtrar no debe ser mayor al dia de hoy.');
            }

            $sucursales = null;

            $roles = DB::select("SELECT * FROM roles r WHERE r.id NOT IN (1,2,3,7,19) ORDER BY r.rol ASC");

            if(Auth::user()->rol_id == 7){
                //Rol de director - Consultar lista de franquicias
                $sucursales = DB::select("SELECT * FROM franquicias f WHERE f.id != '00000' ORDER BY f.ciudad ASC");

                if($franquiciaSeleccionada != null){
                    $cadenaFranquiciaSolicitudes = " AND v.id_franquicia = '$franquiciaSeleccionada'";
                    $cadenaFranquiciaMovimientos = " AND hs.id_franquicia = '$franquiciaSeleccionada'";
                }else{
                    $cadenaFranquiciaSolicitudes = " AND v.id_franquicia != '00000'";
                    $cadenaFranquiciaMovimientos = " AND hs.id_franquicia != '00000'";
                }

            } else {
                //Rol de administracion y principal - Consultar solicitudes pendientes solo de su sucursal
                $sucursales = DB::select("SELECT * FROM franquicias f WHERE f.id = '$idFranquicia' ORDER BY f.ciudad ASC");
                $cadenaFranquiciaSolicitudes = " AND v.id_franquicia = '$idFranquicia'";
                $cadenaFranquiciaMovimientos = " AND hs.id_franquicia = '$idFranquicia'";
            }

            //Filtros
            if($rolSeleccionado != null){
                //Selecciono rol
                $cadenaRol = " AND v.id_rol = '$rolSeleccionado'";
            }

            if(($cbFechaCreacion == null && $cbFechaCita == null) || ($cbFechaCreacion != null && $cbFechaCita != null)){
                $cadenaFecha = " AND (STR_TO_DATE(v.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicial','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d')
                                OR STR_TO_DATE(v.fechacita,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicial','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d'))";
            }else{
                //Solo selecciono un checkBox de fechas
                if($cbFechaCreacion != null){
                    $cadenaFecha = "AND  STR_TO_DATE(v.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicial','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d')";
                }
                if($cbFechaCita != null){
                    $cadenaFecha = " AND STR_TO_DATE(v.fechacita,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicial','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d')";
                }
            }

            //Extraer todas las solicitudes agendadas y notificadas con asistencia
            $listaSolicitudesPendientes = DB::select("SELECT v.nombresolicitante, v.fechacita, v.observaciones, v.estado, v.id_franquicia,
                                                                v.indice, v.horacita, v.created_at, v.telefono, v.observacionessolicitud, v.curriculum,
                                                               (SELECT r.rol FROM roles r WHERE r.id = v.id_rol) AS rol,
                                                               (SELECT f.ciudad FROM franquicias f WHERE f.id = v.id_franquicia) AS ciudad FROM vacantes v
                                                                WHERE v.estado IN (1, 3, 8)
                                                                " . $cadenaFranquiciaSolicitudes . "
                                                                " . $cadenaRol . "
                                                                " . $cadenaFecha . "
                                                                ORDER BY v.estado ASC, v.created_at DESC");

            //Extraer solicitudes de la ultima semana que han sido canceladas, contratados y rechazados
            $listaSolicitudesNotificadas = DB::select("SELECT v.nombresolicitante, v.fechacita, v.observaciones, v.estado, v.id_franquicia,
                                                                v.indice, v.horacita, v.created_at, v.telefono, v.observacionessolicitud, v.curriculum,
                                                               (SELECT r.rol FROM roles r WHERE r.id = v.id_rol) AS rol,
                                                               (SELECT f.ciudad FROM franquicias f WHERE f.id = v.id_franquicia) AS ciudad FROM vacantes v
                                                                WHERE v.estado IN (2,4,6,7)
                                                                " . $cadenaFranquiciaSolicitudes . "
                                                                " . $cadenaRol . "
                                                                " . $cadenaFecha . "
                                                                ORDER BY v.estado ASC, v.created_at DESC");

            $listaSolicitudesGeneradas = array_merge($listaSolicitudesPendientes, $listaSolicitudesNotificadas);

            $movimientoVacantes = DB::select("SELECT hs.cambios, hs.created_at, (SELECT u.name FROM users u WHERE u.id = hs.id_usuarioC) as usuario FROM historialsucursal hs
                                                    WHERE hs.tipomensaje = 10 AND hs.seccion = 0
                                                    " . $cadenaFranquiciaMovimientos ."
                                                    AND STR_TO_DATE(hs.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicial','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d')
                                                    ORDER BY hs.created_at DESC");

            //Consultar las solicitudes de todas las sucursales
            $solicitudesRol = array();
            foreach ($sucursales as $sucursal){
                $id_sucursal = $sucursal->id;
                $solicitudes = DB::select("SELECT f.id, r.id, r.rol, (SELECT COUNT(v.indice) FROM vacantes v
                                                WHERE v.id_franquicia = f.id AND v.id_rol = r.id AND v.estado = 0) AS numeroSolicitudes
                                                FROM franquicias f
                                                INNER JOIN roles r
                                                WHERE f.id = '$id_sucursal' AND r.id NOT IN (1,2,3,7,19)
                                                GROUP BY f.id, r.id,r.rol
                                                ORDER BY f.ciudad ASC, r.rol ASC");

                array_push($solicitudesRol,$solicitudes);
            }

            //Horarios de atencion
            $horarioAtencion = DB::select("SELECT f.horaatencioninicio, f.horaatencionfin FROM franquicias f WHERE f.id = '$idFranquicia'");

            return view('administracion.franquicia.vacantes.tablavacantesadministracion', [
                'idFranquicia' => $idFranquicia,
                'sucursales' => $sucursales,
                'roles' => $roles,
                'solicitudesRol' => $solicitudesRol,
                'listaSolicitudesGeneradas' => $listaSolicitudesGeneradas,
                'fechaFiltrar' => $fechaInicial,
                'movimientoVacantes' => $movimientoVacantes,
                'horarioAtencion' => $horarioAtencion,
                'franquiciaSeleccionada' => $franquiciaSeleccionada,
                'rolSeleccionado' => $rolSeleccionado,
                'cbFechaCreacion' => $cbFechaCreacion,
                'cbFechaCita' => $cbFechaCita
            ]);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }


    }

    function listavacantesredes($idFranquicia){
        if(Auth::check() && (Auth::user()->rol_id  == 7 || Auth::user()->rol_id  == 18 || Auth::user()->rol_id == 20)) {
            //ROL DIRECTOR - REDES - INNOVACION

            $now = Carbon::now();
            $fechaInicial =Carbon::parse($now)->subWeek()->format('Y-m-d');
            $fechaFinal = Carbon::parse($now)->format('Y-m-d');

            $roles = DB::select("SELECT * FROM roles r WHERE r.id != 1 AND r.id != 2 AND r.id != 3 ORDER BY r.rol ASC");
            $sucursales = DB::select("SELECT * FROM franquicias f WHERE f.id != '00000' ORDER BY f.ciudad ASC");
            $cbFechaCreacion = null;
            $cbFechaCita = null;
            $rolSeleccionado = null;

            //Consultar las solicitudes de todas las sucursales
            $solicitudesRol = array();
            foreach ($sucursales as $sucursal){
                $id_sucursal = $sucursal->id;
                $solicitudes = DB::select("SELECT f.id, r.id, r.rol, (SELECT COUNT(v.indice) FROM vacantes v
                                                WHERE v.id_franquicia = f.id AND v.id_rol = r.id AND v.estado = 0) AS numeroSolicitudes
                                                FROM franquicias f
                                                INNER JOIN roles r
                                                WHERE f.id = '$id_sucursal' AND r.id NOT IN (1,2,3,7,19)
                                                GROUP BY f.id, r.id,r.rol
                                                ORDER BY f.ciudad ASC, r.rol ASC");

                array_push($solicitudesRol,$solicitudes);
            }

            $listaSolicitudesGeneradas = DB::select("SELECT v.nombresolicitante, v.fechacita, v.observaciones, v.estado, v.id_franquicia,
                                                                v.indice, v.horacita, v.created_at, v.telefono, v.observacionessolicitud, v.curriculum,
                                                                (SELECT r.rol FROM roles r WHERE r.id = v.id_rol) AS rol,
                                                                (SELECT f.ciudad FROM franquicias f WHERE f.id = v.id_franquicia) AS sucursal,
                                                                (SELECT f.horaatencioninicio FROM franquicias f WHERE f.id = v.id_franquicia) AS horainicio,
                                                                (SELECT f.horaatencionfin FROM franquicias f WHERE f.id = v.id_franquicia) AS horafin
                                                                FROM vacantes v
                                                                WHERE v.id_franquicia != '00000' AND v.estado IN (0,1,8)
                                                                ORDER BY sucursal ASC,v.estado ASC, v.created_at DESC");

            //Solicitudes con notificadas como Asistio o Cancelo
            $solicitudesNotificadas = DB::select("SELECT v.nombresolicitante, v.fechacita, v.observaciones, v.estado, v.id_franquicia,
                                                                v.indice, v.horacita, v.created_at, v.telefono, v.observacionessolicitud, v.curriculum,
                                                                (SELECT r.rol FROM roles r WHERE r.id = v.id_rol) AS rol,
                                                                (SELECT f.ciudad FROM franquicias f WHERE f.id = v.id_franquicia) AS sucursal,
                                                                (SELECT f.horaatencioninicio FROM franquicias f WHERE f.id = v.id_franquicia) AS horainicio,
                                                                (SELECT f.horaatencionfin FROM franquicias f WHERE f.id = v.id_franquicia) AS horafin
                                                                FROM vacantes v
                                                                WHERE v.id_franquicia != '00000' AND v.estado IN (2,3,4)
                                                                AND (STR_TO_DATE(v.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicial','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d')
                                                                    OR STR_TO_DATE(v.updated_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicial','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d'))
                                                                ORDER BY sucursal ASC, v.estado ASC, v.created_at DESC");

            $listaSolicitudesGeneradas = array_merge($listaSolicitudesGeneradas, $solicitudesNotificadas);

            $movimientoVacantes = DB::select("SELECT hs.cambios, hs.created_at, (SELECT u.name FROM users u WHERE u.id = hs.id_usuarioC) as usuario,
                                                    (SELECT f.ciudad FROM franquicias f WHERE f.id = hs.id_franquicia) as ciudad FROM historialsucursal hs
                                                    WHERE hs.tipomensaje = 10 AND hs.seccion = 0
                                                    AND STR_TO_DATE(hs.created_at,'%Y-%m-%d') = STR_TO_DATE('$fechaFinal','%Y-%m-%d')
                                                    ORDER BY hs.created_at DESC");

            return view('administracion.franquicia.vacantes.tablavacantesagendarredes', [
                'idFranquicia' => $idFranquicia,
                'sucursales' => $sucursales,
                'roles' => $roles,
                'solicitudesRol' => $solicitudesRol,
                'listaSolicitudesGeneradas' => $listaSolicitudesGeneradas,
                'fechaFiltrar' => $fechaFinal,
                'movimientoVacantes' => $movimientoVacantes,
                'franquiciaSeleccionada' => $idFranquicia,
                'rolSeleccionado' => $rolSeleccionado,
                'cbFechaCreacion' => $cbFechaCreacion,
                'cbFechaCita' => $cbFechaCita
            ]);
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    function filtrarlistavacantesredes($idFranquicia, Request $request){
        if(Auth::check() && (Auth::user()->rol_id  == 7 || Auth::user()->rol_id  == 18 || Auth::user()->rol_id == 20)) {
            //ROL DIRECTOR - REDES - INNOVACION

            $franquiciaSeleccionada = (Auth::user()->rol_id  == 7)? $request->input('franquiciaSeleccionada') : $idFranquicia;
            $rolSeleccionado = $request->input('rolSeleccionado');
            $now = Carbon::now();
            $fechaInicial =Carbon::parse($request->input('fechaFiltroSolicitud'))->format('Y-m-d');
            $fechaFinal = Carbon::parse($now)->format('Y-m-d');
            $cbFechaCreacion = $request->input('cbFechaCreacion');
            $cbFechaCita = $request->input('cbFechaCita');
            $cadenaFranquiciaSolicitudes = "";
            $cadenaFranquiciaMovimientos = "";
            $cadenaRol = "";
            $cadenaFecha = "";

            //Validar fecha
            if($fechaInicial > $fechaFinal){
                return back()->with('alerta',' La fecha en filtrar no debe ser mayor al dia de hoy.');
            }

            $roles = DB::select("SELECT * FROM roles r WHERE r.id != 1 AND r.id != 2 AND r.id != 3 ORDER BY r.rol ASC");
            $sucursales = DB::select("SELECT * FROM franquicias f WHERE f.id != '00000' ORDER BY f.ciudad ASC");

            //Consultar las solicitudes de todas las sucursales
            $solicitudesRol = array();
            foreach ($sucursales as $sucursal){
                $id_sucursal = $sucursal->id;
                $solicitudes = DB::select("SELECT f.id, r.id, r.rol, (SELECT COUNT(v.indice) FROM vacantes v
                                                WHERE v.id_franquicia = f.id AND v.id_rol = r.id AND v.estado = 0) AS numeroSolicitudes
                                                FROM franquicias f
                                                INNER JOIN roles r
                                                WHERE f.id = '$id_sucursal' AND r.id NOT IN (1,2,3,7,19)
                                                GROUP BY f.id, r.id,r.rol
                                                ORDER BY f.ciudad ASC, r.rol ASC");

                array_push($solicitudesRol,$solicitudes);
            }

            //Filtros
            if($rolSeleccionado != null){
                //Selecciono rol
                $cadenaRol = " AND v.id_rol = '$rolSeleccionado'";
            }

            if(($cbFechaCreacion == null && $cbFechaCita == null) || ($cbFechaCreacion != null && $cbFechaCita != null)){
                $cadenaFecha = " AND (STR_TO_DATE(v.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicial','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d')
                                OR STR_TO_DATE(v.fechacita,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicial','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d'))";
            }else{
                //Solo selecciono un checkBox de fechas
                if($cbFechaCreacion != null){
                    $cadenaFecha = "AND  STR_TO_DATE(v.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicial','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d')";
                }
                if($cbFechaCita != null){
                    $cadenaFecha = " AND STR_TO_DATE(v.fechacita,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicial','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d')";
                }
            }

            if($franquiciaSeleccionada != null){
                $cadenaFranquiciaSolicitudes = " AND v.id_franquicia = '$franquiciaSeleccionada'";
                $cadenaFranquiciaMovimientos = " AND hs.id_franquicia = '$franquiciaSeleccionada'";
            }else{
                $cadenaFranquiciaSolicitudes = " AND v.id_franquicia != '00000'";
                $cadenaFranquiciaMovimientos = " AND hs.id_franquicia != '00000'";
            }


            $listaSolicitudesGeneradas = DB::select("SELECT v.nombresolicitante, v.fechacita, v.observaciones, v.estado, v.id_franquicia,
                                                                v.indice, v.horacita, v.created_at, v.telefono, v.observacionessolicitud, v.curriculum,
                                                                (SELECT r.rol FROM roles r WHERE r.id = v.id_rol) AS rol,
                                                                (SELECT f.ciudad FROM franquicias f WHERE f.id = v.id_franquicia) AS sucursal,
                                                                (SELECT f.horaatencioninicio FROM franquicias f WHERE f.id = v.id_franquicia) AS horainicio,
                                                                (SELECT f.horaatencionfin FROM franquicias f WHERE f.id = v.id_franquicia) AS horafin
                                                                FROM vacantes v
                                                                WHERE v.estado IN(0,1,8)
                                                                " . $cadenaFranquiciaSolicitudes . "
                                                                " . $cadenaRol . "
                                                                " . $cadenaFecha . "
                                                                ORDER BY sucursal ASC, v.estado ASC, v.created_at DESC");

            //Solicitudes con notificadas como Asistio o Cancelo
            $solicitudesNotificadas = DB::select("SELECT v.nombresolicitante, v.fechacita, v.observaciones, v.estado, v.id_franquicia,
                                                                v.indice, v.horacita, v.created_at, v.telefono, v.observacionessolicitud, v.curriculum,
                                                                (SELECT r.rol FROM roles r WHERE r.id = v.id_rol) AS rol,
                                                                (SELECT f.ciudad FROM franquicias f WHERE f.id = v.id_franquicia) AS sucursal,
                                                                (SELECT f.horaatencioninicio FROM franquicias f WHERE f.id = v.id_franquicia) AS horainicio,
                                                                (SELECT f.horaatencionfin FROM franquicias f WHERE f.id = v.id_franquicia) AS horafin
                                                                FROM vacantes v
                                                                WHERE v.estado IN (2,3,4)
                                                                " . $cadenaFranquiciaSolicitudes . "
                                                                " . $cadenaRol . "
                                                                " . $cadenaFecha . "
                                                                ORDER BY sucursal ASC, v.estado ASC, v.created_at DESC");

            $listaSolicitudesGeneradas = array_merge($listaSolicitudesGeneradas, $solicitudesNotificadas);

            $movimientoVacantes = DB::select("SELECT hs.cambios, hs.created_at, (SELECT u.name FROM users u WHERE u.id = hs.id_usuarioC) as usuario,
                                                    (SELECT f.ciudad FROM franquicias f WHERE f.id = hs.id_franquicia) as ciudad FROM historialsucursal hs
                                                    WHERE hs.tipomensaje = 10 AND hs.seccion = 0
                                                    " . $cadenaFranquiciaMovimientos . "
                                                    AND STR_TO_DATE(hs.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaInicial','%Y-%m-%d') AND STR_TO_DATE('$fechaFinal','%Y-%m-%d')
                                                    ORDER BY hs.created_at DESC");

            return view('administracion.franquicia.vacantes.tablavacantesagendarredes', [
                'idFranquicia' => $idFranquicia,
                'sucursales' => $sucursales,
                'roles' => $roles,
                'solicitudesRol' => $solicitudesRol,
                'listaSolicitudesGeneradas' => $listaSolicitudesGeneradas,
                'fechaFiltrar' => $fechaInicial,
                'movimientoVacantes' => $movimientoVacantes,
                'franquiciaSeleccionada' => $franquiciaSeleccionada,
                'rolSeleccionado' => $rolSeleccionado,
                'cbFechaCreacion' => $cbFechaCreacion,
                'cbFechaCita' => $cbFechaCita
            ]);
        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }
    }

    public function actualizarhorariocitavacantes($idFranquicia, Request $request){
        if(Auth::check() && (Auth::user()->rol_id  == 6 || Auth::user()->rol_id  == 7 || Auth::user()->rol_id  == 8 || Auth::user()->rol_id  == 18))
        {
            if(Auth::user()->rol_id  == 7){
                //Reglas para rol de director
                request()->validate([
                    'sucursalSeleccionadaHorario' => 'required',
                    'horaInicio' => 'required',
                    'horaFinal' => 'required|min:1'
                ]);
            }else{
                //Reglas para roles Administracion - Principal
                request()->validate([
                    'horaInicio' => 'required',
                    'horaFinal' => 'required'
                ]);
            }

            //Datos del formulario
            if(Auth::user()->rol_id  == 7){
                //Rol de director - Tomamos el idFranquicia del select
                $sucursalSeleccionada = $request->input('sucursalSeleccionadaHorario');
            }else {
                //Rol de principal o administracion - franquicia a la que corresponde
                $sucursalSeleccionada = $idFranquicia;
            }
            $horarioInicio = $request->input('horaInicio');
            $horarioFin = $request->input('horaFinal');

            $formato = 'H:i';
            //validar fechas
            $horaInicioTemporal = DateTime::createFromFormat($formato, $horarioInicio);
            $horaFinTemporal = DateTime::createFromFormat($formato, $horarioFin);
            if(($horaInicioTemporal && $horaInicioTemporal->format($formato) == $horarioInicio) && ($horaFinTemporal && $horaFinTemporal->format($formato) == $horarioFin)){
                //Horas correctas
                if($horaInicioTemporal < $horaFinTemporal){

                    //Actualizar hora de atencion
                    DB::table('franquicias')->where('id', '=', $sucursalSeleccionada)
                        ->update(['horaatencioninicio' => $horarioInicio, 'horaatencionfin' => $horarioFin]);

                    //Registrar movimiento
                    $id_usuario = Auth::id();
                    DB::table('historialsucursal')->insert([
                        'id_usuarioC' => $id_usuario,'id_franquicia' => $sucursalSeleccionada, 'tipomensaje' => '10',
                        'created_at' => Carbon::now(), 'cambios' => 'Agrego horario para atencion de citas a vacantes', 'seccion' => '0'
                    ]);

                    return back()->with('bien',' Hora de atención de citas agregada correctamente.');

                }else{
                    return back()->with('alerta',' La hora de inicio para atención no debe ser menor a hora final.');
                }

            }else{
                //Horas seleccionadas con formato no valido
                return back()->with('alerta','Verifica el horario de atencion.');
            }

        }else{
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            }else{
                return redirect()->route('login');
            }
        }

    }
    public function solicitarvacante($idFranquicia, Request $request){

        if(Auth::user()->rol_id  == 7){
            //Reglas para rol de director
            request()->validate([
                'sucursalSeleccionada' => 'required',
                'rolSeleccionado' => 'required',
                'numsolicitudes' => 'required|min:1',
                'observacionesSolicitud' => 'nullable|string|min:0|max:1000'
            ]);
        }else{
            //Reglas para roles Administracion - Principal
            request()->validate([
                'rolSeleccionado' => 'required',
                'numsolicitudes' => 'required|min:1',
                'observacionesSolicitud' => 'nullable|string|min:0|max:1000'
            ]);
        }

        //Validar cantidad de solicitudes
        if($request->input('numsolicitudes') <= 0){
            return back()->with('alerta',' Numero de vacantes a solicitar debe ser mayor o igual a 1.');
        }

        //Datos del formulario
        if(Auth::user()->rol_id  == 7){
            //Rol de director - Tomamos el idFranquicia del select
            $sucursalSeleccionada = $request->input('sucursalSeleccionada');
        }else {
            //Rol de principal o administracion - franquicia a la que corresponde
            $sucursalSeleccionada = $idFranquicia;
        }
        $rolSeleccionado = $request->input('rolSeleccionado');
        $numeroVacantes = $request->input('numsolicitudes');
        $observacionesSolicitud = $request->input('observacionesSolicitud');

        $existeSucursal = DB::select("SELECT f.id FROM franquicias f WHERE f.id = '$idFranquicia'");
        if($existeSucursal != null){
            //Existe la sucursal seleccionada
            $existeRol = DB::select("SELECT r.id FROM roles r WHERE r.id = '$rolSeleccionado'");
            if($existeRol != null){
                //Rol seleccionado es correcto

                //Validar limite de solicitudes para vacante por roles
                $limiteRol = DB::select("SELECT r.limitevacantes FROM roles r WHERE r.id = '$rolSeleccionado'");
                $limiteVacantesRol = $limiteRol[0]->limitevacantes;

                //Intentas ingresar un numero mayar al limite?
                if($numeroVacantes > $limiteVacantesRol){
                    //Numero de vacantes ingresado es mayor a limite vacantes para rol
                    return back()->with('alerta',"Solo puedes solicitar '" . $limiteVacantesRol ."' vacantes como máximo para el rol de '". self::obtenerRol($rolSeleccionado) ."'");
                }

                $vacantesSinAgendarRol = DB::select("SELECT COUNT(v.indice) AS vacantessinagendar FROM vacantes v WHERE v.id_rol = '$rolSeleccionado' AND v.id_franquicia = '$sucursalSeleccionada' AND v.estado = '0'");

                //Llegaste al limite de solicitudes?
                if($limiteVacantesRol == $vacantesSinAgendarRol[0]->vacantessinagendar){
                    return back()->with('alerta',"Por el momento no puedes solicitar mas vacantes para el rol de '" . self::obtenerRol($rolSeleccionado) . "' debido que haz llegado al limite.");
                }

                //Sobrepasaria el limite en caso de solicitar todal?
                if($limiteVacantesRol < ($numeroVacantes + $vacantesSinAgendarRol[0]->vacantessinagendar) ){
                    return back()->with('alerta',"Ya cuentas con '". $vacantesSinAgendarRol[0]->vacantessinagendar ."' vacantes solicitadas. " .
                        "El rol de '" . self::obtenerRol($rolSeleccionado) ."' permite un maximo de '". $limiteVacantesRol . "'. " .
                        "Te recomiendo solicitar '". ($limiteVacantesRol -  $vacantesSinAgendarRol[0]->vacantessinagendar)."' o menos vacantes");
                }

                //Registramos las solicitudes en la BD
                for ($i = 0; $i < $numeroVacantes; $i = $i + 1){
                    DB::table('vacantes')->insert([
                        'id_franquicia' => $sucursalSeleccionada,
                        'id_rol' => $rolSeleccionado,
                        'observacionessolicitud' => $observacionesSolicitud,
                        'estado' => 0,
                        'identificador' => self::generarIdentificadorVacantes(10),
                        'created_at' => Carbon::now()
                    ]);
                }

                //Registrar movimiento
                if($numeroVacantes == 1){
                    $mensaje = "Solicitó '".$numeroVacantes."' vacante para '".self::obtenerRol($rolSeleccionado)."'";
                } else {
                    //Multiples solicitudes
                    $mensaje = "Solicitó '".$numeroVacantes."' vacantes para '".self::obtenerRol($rolSeleccionado)."'";
                }

                $id_usuario = Auth::id();
                DB::table('historialsucursal')->insert([
                    'id_usuarioC' => $id_usuario,
                    'id_franquicia' => $sucursalSeleccionada, 'tipomensaje' => '10',
                    'created_at' => Carbon::now(), 'cambios' => $mensaje, 'seccion' => '0'
                ]);

                return back()->with('bien',' Solicitud de vacante generada correctamente.');
            }else{
                return back()->with('alerta',' No existe el rol seleccionado para la solicitud de vacante');
            }

        }else{
            return back()->with('alerta',' No existe la sucursal a la cual se quiere asignar la vacante');
        }
    }


    public function cancelarvacante(Request $request){
        if(Auth::check() && (Auth::user()->rol_id  == 6 || Auth::user()->rol_id  == 7 || Auth::user()->rol_id  == 8 || Auth::user()->rol_id  == 18 || Auth::user()->rol_id == 20)) {
            //ROL ADMINISTRACION - DIRECTOR - PRINCIPAL - REDES - INNOVACION

            $idFranquicia = $request->input('idFranquicia');
            $indice = $request->input('inidce');

            $existeSolicitud = DB::select("SELECT * FROM vacantes v WHERE v.indice = '$indice'");
            $franquicia_solicitud = (Auth::user()->rol_id  == 7)?$idFranquicia:$existeSolicitud[0]->id_franquicia;

            if($existeSolicitud != null){
                if($existeSolicitud[0]->estado != 2 || $existeSolicitud[0]->estado != 3){
                    //Verificar el estatus actual - Se puede cancelar solo si no a sido cancelada ya o si no a sido marcada como Llego

                    //Cambiar estado de solicitud vacante
                    DB::table('vacantes')->where('indice','=',$indice)->update(['estado'=>'4']);

                    //Registrar movimiento
                    $id_usuario = Auth::id();
                    DB::table('historialsucursal')->insert([
                        'id_usuarioC' => $id_usuario,
                        'id_franquicia' => $franquicia_solicitud, 'tipomensaje' => '10',
                        'created_at' => Carbon::now(), 'cambios' => "Canceló solicitud de vacate para el puesto de '".self::obtenerRol($existeSolicitud[0]->id_rol) ."'",
                        'seccion' => '0'
                    ]);

                    $bandera = true;
                    $mensaje = "Solicitud de vacante cancelada correctamente.  Espera a que se actualice la pagina.";

                }else{
                    //Estaod no valido
                    $bandera = false;
                    $mensaje = "No puedes cancelar la solicitud debido a su estado.";
                }

            }else{
                //No existe la solicitud
                $bandera = false;
                $mensaje = "No existe la solicitud.";
            }

            $response = ['mensaje' => $mensaje, 'bandera' => $bandera];
            return response()->json($response);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function notificacioncitavacante(Request $request){
        if(Auth::check() && (Auth::user()->rol_id  == 6 || Auth::user()->rol_id  == 7 || Auth::user()->rol_id  == 8 || Auth::user()->rol_id  == 18))
        {
            //Opciones de notificacion
            //asistio -> Asistio a entrevista
            //cancelo -> Cancelo cita
            //contratar -> Contratacion de personal

            $idFranquicia = $request->input('idFranquicia');
            $indice = $request->input('inidce');
            $opcion = $request->input('opcion');

            $existeSolicitud = DB::select("SELECT * FROM vacantes v WHERE v.indice = '$indice'");
            $franquicia_solicitud = (Auth::user()->rol_id  == 7)?$idFranquicia:$existeSolicitud[0]->id_franquicia;

            if($existeSolicitud != null){
                if($existeSolicitud[0]->estado == 1 || $existeSolicitud[0]->estado == 3 || $existeSolicitud[0]->estado == 8){
                    //Verificar el estatus actual - Actualizar estatus solo si a sido agendada o asitio

                    switch ($opcion){
                        case "asistio":
                            $hoy = Carbon::now();
                            $hoy = Carbon::parse($hoy)->format('Y-m-d');
                            $fechacita =Carbon::parse($existeSolicitud[0]->fechacita)->format('Y-m-d');
                            //Verificar fecha de cita
                            if($fechacita > $hoy){
                                //Fecha cita es mayor a fecha actual - No se puede marcar como asistio
                                $bandera = false;
                                $mensaje = "No puedes marcar cita como 'Asistió' debido a su fecha programada.";

                                $response = ['mensaje' => $mensaje, 'bandera' => $bandera];
                                return response()->json($response);
                            }else{
                                //Fecha cita es menor a fecha actual - Permitir marcar como asistio
                                $estado = 3;
                                $mensaje = "Notificó que '".$existeSolicitud[0]->nombresolicitante ."' asistió a la cita para el puesto de '".self::obtenerRol($existeSolicitud[0]->id_rol) ."'";
                                break;
                            }

                        case "cancelo":
                            $estado = 2;
                            $mensaje = "Notificó que '".$existeSolicitud[0]->nombresolicitante ."' canceló la cita para el puesto de '".self::obtenerRol($existeSolicitud[0]->id_rol) ."'";
                            break;

                        case "contratar":
                            $estado = 6;
                            $mensaje = "Notificó que '".$existeSolicitud[0]->nombresolicitante ."' fue contratado para el puesto de '".self::obtenerRol($existeSolicitud[0]->id_rol) ."'";

                            //Eliminar solicitud de vatante pendiente con identificador igual al marcado como contratado
                            $identificador = $existeSolicitud[0]->identificador;
                            DB::delete("DELETE FROM vacantes WHERE identificador = '$identificador' AND estado = '0'");
                            break;

                        case "rechazar":
                            $estado = 7;
                            $mensaje = "Notificó que '".$existeSolicitud[0]->nombresolicitante ."' fue rechazado para el puesto de '".self::obtenerRol($existeSolicitud[0]->id_rol) ."'";
                            break;
                    }

                    //Cambiar estado de solicitud vacante
                    DB::table('vacantes')->where('indice','=',$indice)->update(['estado'=>$estado, 'updated_at'=> Carbon::now()]);

                    //Registrar movimiento
                    $id_usuario = Auth::id();
                    DB::table('historialsucursal')->insert([
                        'id_usuarioC' => $id_usuario,
                        'id_franquicia' => $franquicia_solicitud, 'tipomensaje' => '10',
                        'created_at' => Carbon::now(), 'cambios' => $mensaje,
                        'seccion' => '0'
                    ]);

                    $bandera = true;
                    $mensaje = "Solicitud de vacante notificada correctamente.  Espera a que se actualice la pagina.";
                }else{
                    //Estado no valido
                    $bandera = false;
                    $mensaje = "No puedes generar la notificación de la solicitud debido a su estado.";
                }

            }else{
                //No existe la solicitud
                $bandera = false;
                $mensaje = " No existe la solicitud.";
            }

            $response = ['mensaje' => $mensaje, 'bandera' => $bandera];
            return response()->json($response);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function agendarcitavacante(Request $request){
        if(Auth::check() && (Auth::user()->rol_id  == 7 || Auth::user()->rol_id  == 18 || Auth::user()->rol_id == 20))
        {
            //ROL DE DIRECTOR - REDES - INNOVACION
            $now = Carbon::now();
            $hoy = Carbon::parse($now)->format('Y-m-d');
            $fechaHoraActual = Carbon::parse($now)->format('Y-m-d H:i');

            $indiceSolicitud = $request->input('indiceSolicitud');
            $nombre = $request->input('nombre');
            $telefono = $request->input('telefono');
            $fechaCita = $request->input('fechaCita');
            $horaCita = $request->input('horaCita');
            $observaciones = $request->input('observaciones');

            $existeSolicitud = DB::select("SELECT * FROM vacantes v WHERE v.indice = '$indiceSolicitud'");
            if($existeSolicitud != null){
                if($existeSolicitud[0]->estado == 0){
                    //Solicitud en estatus de pendiente por agendar
                    //Validar fecha de cita
                    $formato = 'Y-m-d';
                    $fechaTemporal = DateTime::createFromFormat($formato, $fechaCita);
                    if($fechaTemporal && $fechaTemporal->format($formato) == $fechaCita){

                        //validar horario de cita
                        $formato = 'H:i';
                        $horaTemporal = DateTime::createFromFormat($formato, $horaCita);

                        if($horaTemporal && $horaTemporal->format($formato) == $horaCita){
                            $id_franquicia = $existeSolicitud[0]->id_franquicia;
                            $horarioAtencionCitas = DB::select("SELECT f.horaatencioninicio,f.horaatencionfin FROM franquicias f WHERE f.id = '$id_franquicia'");
                            //Verificar si hora para agendar cita esta dentro del horario de atencion de la sucursal

                            if($horarioAtencionCitas[0]->horaatencioninicio != null && $horarioAtencionCitas[0]->horaatencionfin != null){
                                //la sucursal tiene hora de atencion en la BD
                                $horarioAtencionInicio = $horarioAtencionCitas[0]->horaatencioninicio;
                                $horarioAtencionFin = $horarioAtencionCitas[0]->horaatencionfin;
                            }else{
                                //No tiene registro de horario de atencion -> Asignar horario estatico por default
                                $horarioAtencionInicio = "08:00";
                                $horarioAtencionFin = "17:00";
                            }

                            if($horaCita >= $horarioAtencionInicio && $horaCita <= $horarioAtencionFin){
                                //Fecha y hora cita formato completo
                                $fechaHoraCita = $fechaCita . ' ' . $horaCita;
                                $fechaHoraCita = Carbon::parse($fechaHoraCita)->format('Y-m-d H:i');
                                if($fechaHoraCita >= $fechaHoraActual){
                                    //Validar si no existe una cita ya agendada en el horario elegido

                                    $existeCitaAgendada = DB::select("SELECT * FROM vacantes v WHERE v.id_franquicia = '$id_franquicia' AND v.fechacita = '$fechaCita'
                                                                            AND v.horacita = '$horaCita' AND v.estado = 1");
                                    if($existeCitaAgendada == null){
                                        //Horario disponible para agendar cita

                                        //Validar formato de telefono ingresado
                                        //Formatos: 333-333-3333, 333-333-33-33, 3333333333
                                        if(preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $telefono) || preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{2}-[0-9]{2}$/", $telefono)
                                            || preg_match("/^[0-9]{10}$/", $telefono)){
                                            //Obtener datos de solicitud de vacante
                                            $sucursal = $existeSolicitud[0]->id_franquicia;
                                            $rol = $existeSolicitud[0]->id_rol;
                                            $observacionesSolicitud = $existeSolicitud[0]->observacionessolicitud;
                                            $identificador = $existeSolicitud[0]->identificador;

                                            //Insertar cita para entrevista de trabajo
                                            DB::table('vacantes')->insert([
                                                'id_franquicia' => $sucursal, 'id_rol' => $rol, 'observacionessolicitud' => $observacionesSolicitud,
                                                'nombresolicitante' => $nombre, 'telefono' => $telefono, 'fechacita' => $fechaCita, 'horacita' => $horaCita,
                                                'observaciones' => $observaciones, 'estado'=>'1', 'identificador' => $identificador, 'created_at' => Carbon::now()
                                            ]);

                                            //Obtener nombre sucursal donde se agenda cita
                                            $nombreSucursal = DB::select("SELECT f.ciudad FROM franquicias f WHERE f.id = '$sucursal'");

                                            //Registrar movimiento
                                            $id_usuario = Auth::id();
                                            DB::table('historialsucursal')->insert([
                                                'id_usuarioC' => $id_usuario,
                                                'id_franquicia' => $id_franquicia, 'tipomensaje' => '10',
                                                'created_at' => Carbon::now(),
                                                'cambios' => "Agendó cita para vacante de '".self::obtenerRol($existeSolicitud[0]->id_rol)."' solicitada en: '" . $nombreSucursal[0]->ciudad ."' con observaciones: '".
                                                    $observaciones."'",
                                                'seccion' => '0'
                                            ]);

                                            $mensaje = "Cita para entrevista agendada correctamente. Espera a que se actualice la pagina.";
                                            $bandera = true;
                                        }else{
                                            //Formato de telefono no valido
                                            $mensaje = "Verifica el formato del telefono agregado. TEL:  333-333-3333 | 333-333-33-33 | 3333333333";
                                            $bandera = false;
                                        }

                                    }else{
                                        //Ya tienes una cita agendada a esa hora
                                        $mensaje = "Ya cuentas con una cita agendada en el dia y hora seleccionado, intenta agendarla en otro horario.";
                                        $bandera = false;
                                    }
                                }else{
                                    //Fecha no valida
                                    $mensaje = "La fecha para agendar cita debe ser mayor o igual al dia de hoy.";
                                    $bandera = false;
                                }

                            }else{
                                //Hora cita fuera de horario de atencion
                                $mensaje = "La hora de cita debe ser agendada dentro del horario de atención de la sucursal.";
                                $bandera = false;
                            }
                        }else{
                            //Fecha no valida
                            $mensaje = "Los datos ingresados como horario de cita no son validos.";
                            $bandera = false;
                        }

                    }else{
                        //Formato de fcaha no valido o fecha incorrecta
                        $mensaje = "La fecha ingresada para agendar cita es incorrecta.";
                        $bandera = false;
                    }
                }else{
                    //La solicitud esta en estatus diferente a pendiente por agendar
                    $mensaje = "No puedes agendar esta cita debido al estatus actual de la solicitud.";
                    $bandera = false;
                }
            }else{
                $mensaje = "Solicitud no disponible, verifica si ya fue ocupada la vacante.";
                $bandera = false;
            }
            $response = ['mensaje' => $mensaje, 'bandera' => $bandera];
            return response()->json($response);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarcitavacante(Request $request){
        if(Auth::check() && (Auth::user()->rol_id  == 7 || Auth::user()->rol_id  == 18 || Auth::user()->rol_id == 20)) {
            //ROL DIRECTOR - REDES - INNOVACION

            $now = Carbon::now();
            $hoy = Carbon::parse($now)->format('Y-m-d');
            $fechaActual = Carbon::parse($now)->format('Y-m-d H:i');

            $indiceSolicitud = $request->input('indiceSolicitud');
            $fechaCita = $request->input('fechaCita');
            $horaCita = $request->input('horaCita');
            $observaciones = $request->input('observaciones');

            $existeSolicitud = DB::select("SELECT * FROM vacantes v WHERE v.indice = '$indiceSolicitud'");
            if($existeSolicitud != null){
                if($existeSolicitud[0]->estado == 1 || $existeSolicitud[0]->estado == 8){
                    //Solicitud en estatus de agendado

                    //Validar fecha de cita
                    $formato = 'Y-m-d';
                    $fechaTemporal = DateTime::createFromFormat($formato, $fechaCita);
                    if($fechaTemporal && $fechaTemporal->format($formato) == $fechaCita){

                            //validar horario de cita
                            $formatoHora = 'H:i';
                            $horaTemporal = DateTime::createFromFormat($formatoHora, $horaCita);
                            if($horaTemporal && $horaTemporal->format($formatoHora) == $horaCita){

                                $id_franquicia = $existeSolicitud[0]->id_franquicia;
                                $horarioAtencionCitas = DB::select("SELECT f.horaatencioninicio,f.horaatencionfin FROM franquicias f WHERE f.id = '$id_franquicia'");
                                //Verificar si hora para agendar cita esta dentro del horario de atencion de la sucursal

                                if($horarioAtencionCitas[0]->horaatencioninicio != null && $horarioAtencionCitas[0]->horaatencionfin != null){
                                    //la sucursal tiene hora de atencion en la BD
                                    $horarioAtencionInicio = $horarioAtencionCitas[0]->horaatencioninicio;
                                    $horarioAtencionFin = $horarioAtencionCitas[0]->horaatencionfin;
                                }else{
                                    //No tiene registro de horario de atencion -> Asignar horario estatico por default
                                    $horarioAtencionInicio = "08:00";
                                    $horarioAtencionFin = "17:00";
                                }
                                if($horaCita >= $horarioAtencionInicio && $horaCita <= $horarioAtencionFin){
                                    //Validar fecha de cita agendada que sea mayor o igual a fecha y hora actual
                                    $fechaHoraCita = $fechaCita . ' ' . $horaCita;
                                    $fechaHoraCita = Carbon::parse($fechaHoraCita)->format('Y-m-d H:i');
                                    if($fechaHoraCita > $fechaActual){

                                        //Validar si no existe una cita ya agendada en el horario elegido
                                        $existeCitaAgendada = DB::select("SELECT v.indice FROM vacantes v WHERE v.id_franquicia = '$id_franquicia' AND v.fechacita = '$fechaCita'
                                                                            AND v.horacita = '$horaCita' AND v.estado = 1 AND v.indice != '$indiceSolicitud'");
                                        if($existeCitaAgendada == null){

                                            //Verificar que datos van a ser actualizados
                                            $camposActualiozados = "";
                                            if($existeSolicitud[0]->fechacita != $fechaCita){
                                                //Actualizo campo de fecha cita
                                                $camposActualiozados = "fecha cita,";
                                            }
                                            if($existeSolicitud[0]->horacita != $horaCita){
                                                //Actualizo campo de hora cita
                                                $camposActualiozados = $camposActualiozados . " hora cita,";
                                            }
                                            if($existeSolicitud[0]->observaciones != $observaciones){
                                                //Actualizo campo de observaciones cita
                                                $camposActualiozados = $camposActualiozados . " observaciones";
                                            }

                                            $camposActualiozados = trim($camposActualiozados, ",");

                                            //Actualizar datos de solicitud
                                            DB::table('vacantes')->where('indice','=',$indiceSolicitud) ->update(['fechacita' => $fechaCita, 'horacita' => $horaCita,
                                                'observaciones' => $observaciones, 'estado'=>'1', 'updated_at' =>  $hoy]);

                                            //Registrar movimiento
                                            if(strlen($camposActualiozados) > 0){
                                                //Se modifico algun campo
                                                $id_usuario = Auth::id();
                                                DB::table('historialsucursal')->insert([
                                                    'id_usuarioC' => $id_usuario,
                                                    'id_franquicia' => $id_franquicia, 'tipomensaje' => '10',
                                                    'created_at' => Carbon::now(),
                                                    'cambios' => "Actualizó '" .$camposActualiozados ."' para vacante de '".self::obtenerRol($existeSolicitud[0]->id_rol)."' con nombre: '". $existeSolicitud[0]->nombresolicitante."'",
                                                    'seccion' => '0'
                                                ]);
                                            }

                                            $mensaje = "Cita actualizada correctamente. Espera a que se actualice la pagina.";
                                            $bandera = true;

                                        }else{
                                            //Ya tienes una cita agendada a esa hora
                                            $mensaje = "Ya cuentas con una cita agendada en el dia y hora seleccionado, intenta agendarla en otro horario.";
                                            $bandera = false;
                                        }

                                    }else{
                                        //Fecha no valida
                                        $mensaje = "La fecha y hora para agendar cita debe ser mayor o igual al dia de hoy.";
                                        $bandera = false;
                                    }

                                }else{
                                    //Hora cita fuera de horario de atencion
                                    $mensaje = "La hora de cita debe ser agendada dentro del horario de atención de la sucursal.";
                                    $bandera = false;
                                }

                            }else{
                                //Hora no valida
                                $mensaje = "Los datos ingresados como horario de cita no son validos.";
                                $bandera = false;
                            }

                    }else{
                        //Formato de fcaha no valido o fecha incorrecta
                        $mensaje = "La fecha ingresada para agendar cita es incorrecta.";
                        $bandera = false;
                    }
                }else{
                    //La solicitud esta en estatus diferente a pendiente por agendar
                    $mensaje = "No puedes actualizar esta cita debido al estatus actual de la solicitud.";
                    $bandera = false;
                }
            }else{
                $mensaje = "Solicitud para vacante no existe.";
                $bandera = false;
            }
            $response = ['mensaje' => $mensaje, 'bandera' => $bandera];
            return response()->json($response);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function reportevacantesmensajes($idFranquicia){
        if(Auth::check() && (Auth::user()->rol_id  == 6 || Auth::user()->rol_id  == 18 || Auth::user()->id == 1 || Auth::user()->id == 61 || Auth::user()->id == 761)) {
            //ROL ADMINISTRACION - REDES - DESARROLLO

        $sucursales = DB::select("SELECT * FROM franquicias f WHERE f.id != '00000' ORDER BY f.ciudad ASC");

        if(Auth::user()->rol_id  == 6){
            //Rol de administracion - Traer mensajes de sucursal
            $mensajesVacantes = DB::select("SELECT vm.indice, vm.mensaje, vm.estadomensaje, vm.estadomensaje, vm.created_at, vm.respuesta,
                                                (SELECT u.name FROM users u WHERE u.id = vm.id_usuario) AS usuario,
                                                (SELECT f.ciudad FROM franquicias f WHERE f.id = vm.id_franquicia) AS sucursal
                                                FROM vacantesmensajes vm WHERE vm.id_franquicia = '$idFranquicia' ORDER BY vm.created_at DESC");
        }else{
            $mensajesVacantes = DB::select("SELECT vm.indice, vm.mensaje, vm.estadomensaje, vm.estadomensaje, vm.created_at, vm.respuesta,
                                                (SELECT u.name FROM users u WHERE u.id = vm.id_usuario) AS usuario,
                                                (SELECT f.ciudad FROM franquicias f WHERE f.id = vm.id_franquicia) AS sucursal
                                                FROM vacantesmensajes vm ORDER BY vm.created_at DESC");
        }

        return view('administracion.franquicia.vacantes.tablareportevacantes', [
            'idFranquicia' => $idFranquicia,
            'mensajesVacantes' => $mensajesVacantes,
            'sucursales' => $sucursales
        ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function nuevomensajevacantes($idFranquicia, Request $request){
        if(Auth::check() && (Auth::user()->rol_id == 18 || Auth::user()->id == 1 || Auth::user()->id == 61 || Auth::user()->id == 761)) {
            //ROL REDES - DESARROLLO

            request()->validate([
                'sucursalSeleccionada' => 'required',
                'mensaje' => 'required'
            ]);

            $franquiciaSeleccionada = $request->input('sucursalSeleccionada');
            $mensaje = $request->input('mensaje');

            try {
                $idUsuarioC = Auth::user()->id;

                //Insertar mensaje
                DB::table('vacantesmensajes')->insert([
                    'id_franquicia' => $franquiciaSeleccionada,
                    'id_usuario' => $idUsuarioC,
                    'mensaje' => $mensaje,
                    'estadomensaje' => '0',
                    'created_at' => Carbon::now()
                ]);

                //Registrar movimiento
                DB::table('historialsucursal')->insert([
                    'id_usuarioC' => $idUsuarioC,
                    'id_franquicia' => $idFranquicia,
                    'tipomensaje' => '16',
                    'created_at' => Carbon::now(),
                    'cambios' => "Agrego mensaje a reporte de vacantes con la siguiente descripcion: '" . $mensaje . "'",
                    'seccion' => '2'
                ]);

                return back()->with('bien', 'Mensaje registrado correctamente.');

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

    public function leermensajevacante($idFranquicia, $indice){
        if(Auth::check() && (Auth::user()->rol_id  == 6 || Auth::user()->rol_id  == 7)) {

            $existeMensaje = DB::select("SELECT * FROM vacantesmensajes vm WHERE vm.indice = '$indice'");

            if($existeMensaje != null){

                if($existeMensaje[0]->estadomensaje == 0){

                    //Actualizar estado del mensaje
                    DB::table('vacantesmensajes')->where("indice","=","$indice")->update([
                        'estadomensaje' => '1', 'updated_at' => Carbon::now()
                    ]);

                    //Registrar movimiento
                    DB::table('historialsucursal')->insert([
                        'id_usuarioC' => Auth::user()->id,
                        'id_franquicia' => $idFranquicia,
                        'tipomensaje' => '16',
                        'created_at' => Carbon::now(),
                        'cambios' => "Marco como leido mensaje en reporte de vacantes con la siguiente descripcion: '" . $existeMensaje[0]->mensaje . "'",
                        'seccion' => '2'
                    ]);

                    return back()->with('bien', 'Mensaje registrado como leido correctamente.');

                }else{
                    return back()->with('alerta', 'No puedes marcar mensaje como leido debido a su estatus actual.');
                }

            }else{
                return back()->with('alerta', 'No existe mensaje.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function respondermensajevacante($idFranquicia){
        if(Auth::check() && (Auth::user()->rol_id  == 6)) {

            $indiceMensaje = request('indiceMensaje');
            $existeMensaje = DB::select("SELECT * FROM vacantesmensajes vm WHERE vm.indice = '$indiceMensaje'");

            if($existeMensaje != null){

                if($existeMensaje[0]->estadomensaje == 1){

                    //Actualizar estado del mensaje
                    DB::table('vacantesmensajes')->where("indice","=",$indiceMensaje)->update([
                        'estadomensaje' => '2', 'respuesta' => request('respuestaMensaje'), 'updated_at' => Carbon::now()
                    ]);

                    //Registrar movimiento
                    DB::table('historialsucursal')->insert([
                        'id_usuarioC' => Auth::user()->id,
                        'id_franquicia' => $idFranquicia,
                        'tipomensaje' => '16',
                        'created_at' => Carbon::now(),
                        'cambios' => "Atendio mensaje en reporte de vacantes con la siguiente respuesta: '" . request('respuestaMensaje') . "'",
                        'seccion' => '2'
                    ]);

                    return back()->with('bien', 'Mensaje registrado como leido correctamente.');

                }else{
                    return back()->with('alerta', 'No puedes responder el mensaje debido a su estatus actual.');
                }

            }else{
                return back()->with('alerta', 'No existe mensaje.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function eliminarmensajevacante($idFranquicia, $indice){
        if(Auth::check() && (Auth::user()->rol_id  == 18 ||Auth::user()->rol_id  == 7)) {

            $existeMensaje = DB::select("SELECT * FROM vacantesmensajes vm WHERE vm.indice = '$indice'");

            if($existeMensaje != null){
                //Existe mensaje en BD
                if($existeMensaje[0]->estadomensaje == 0){
                    //Estado del mensaje en pendiente por leer

                    //Obtener fechas y dar formato
                    $hoy = Carbon::parse(Carbon::now())->format("Y-m-d H:i:s");
                    $fechaRegistro = Carbon::parse($existeMensaje[0]->created_at)->format("Y-m-d H:i:s");

                    //Convertir fechas string a tipo datetime
                    $fechaActual = new DateTime($hoy);
                    $fechaCreacionMensaje = new DateTime($fechaRegistro);

                    //Obtener diferencia entre fechas
                    $tiempoTranscurrdido = $fechaActual->diff($fechaCreacionMensaje);

                    //Tiene menos de 5 minutos que se registro el mensaje?
                    if($tiempoTranscurrdido->format('%i') <= 5){
                        //NMenos de 5 minutos de su creacion

                        //Eliminar mensaje
                        DB::delete("DELETE FROM vacantesmensajes WHERE indice = '$indice'");

                        //Registrar movimiento
                        DB::table('historialsucursal')->insert([
                            'id_usuarioC' => Auth::user()->id,
                            'id_franquicia' => $idFranquicia,
                            'tipomensaje' => '16',
                            'created_at' => Carbon::now(),
                            'cambios' => "Elimino mensaje en reporte de vacantes con la siguiente descripcion: '" . $existeMensaje[0]->mensaje . "'",
                            'seccion' => '2'
                        ]);

                        return back()->with('bien', 'Mensaje eliminado correctamente.');

                    }else{
                        //Ya excedieron mas de 5 minutos desde su hora de creacion
                        return back()->with('alerta', 'No es posible eliminar el mensaje, ya que ha superado su límite de tiempo.');
                    }

                }else{
                    return back()->with('alerta', 'No puedes eliminar el mensaje debido a su estatus actual.');
                }

            }else{
                return back()->with('alerta', 'No existe mensaje.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }


    public function obtenerRol($idRol){
        $rol = DB::select("SELECT rol FROM roles WHERE id = '$idRol'");
        return $rol[0]->rol;
    }

    function actualizarTelefonoSucursal(){
        $sucursales = DB::select("SELECT id, telefonoatencionclientes FROM franquicias");

        //Actualizamos campo whatsapp con numero de atenciona a clientes
        foreach ($sucursales as $sucursal){
            $telefono = $sucursal->telefonoatencionclientes;
            $idFranquicia =  $sucursal->id;
            DB::update("UPDATE franquicias SET whatsapp = '$telefono' WHERE id = '$idFranquicia'");
        }
        //Vaciar campo telefono atencion a cliente
        DB::update("UPDATE franquicias SET telefonoatencionclientes = '321-231-1232'");

    }

    function agregarExpedienteUsuario($idFranquicia, $idUsuario){
        request()->validate([
            'expDescripcion' => 'required|string',
            'documento' => 'required|file|mimes:pdf'
        ]);

        //Validar tamaño de archivo adjunto
        $contratosGlobal = new contratosGlobal();
        if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('documento'))){
            return back()->with('alerta', "El archivo adjunto sobrepasa el tamaño maximo permitido de 1MB.");
        }

        try {

            if (request()->hasFile('documento')) {
                $documentoNombre = 'Expediente-Usuario-' . $idUsuario . '-' . time() . '.' . request()->file('documento')->getClientOriginalExtension();
                $documento = request()->file('documento')->storeAs('uploads/imagenes/franquicia/expediente', $documentoNombre, 'disco');

            }

            DB::table('expedienteusuarios')
                ->where('id_franquicia', '=', $idFranquicia)
                ->where('id_usuario','=',$idUsuario)
                ->insert(['id_franquicia' => $idFranquicia, 'id_usuario' => $idUsuario, 'descripcion' => request('expDescripcion'),
                          'documento' => $documento, 'created_at' => Carbon::now()
            ]);

            //Insertar registro en tabla historialsucursal
            $usuario = DB::select("SELECT u.name FROM users u WHERE u.id = '$idUsuario'");
            $id_usuarioC = Auth::id();
            self::insertarHistorialSucursal($idFranquicia, $id_usuarioC, "Agrego documento a expediente de usuario: '" .$usuario[0]->name ."' con descripcion: '". request('expDescripcion') . "'", "14", "2");

            return back()->with('bien', 'Archivo almacenado en expediente correctamente.');

        } catch (\Exception $e) {
            \Log::info("Error: " . $e->getMessage());
            return back()->with('error', 'Tuvimos un error, por favor contacta al administrador de la pagina.');
        }



    }

    public function descargarArchivoExpedienteUsuario($idFranquicia, $idUsuario, $indice)
    {
        if (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) //ROL DEL DIRECTOR
        {
            $existeUsuario = DB::select("SELECT id FROM users WHERE id = '$idUsuario'");
            $existeArchivo = DB::select("SELECT eu.documento FROM expedienteusuarios eu WHERE eu.indice = '$indice'
                                              AND eu.id_usuario = '$idUsuario' AND eu.id_franquicia = '$idFranquicia'");

            if ($existeUsuario != null) { //Existe el usuario?
                //Si existe el usuario

                if($existeArchivo != null){
                    //Existe un archivo almacenado
                    $archivo = $existeArchivo[0]->documento;

                    return Storage::disk('disco')->download($archivo);
                }
                else {
                    return back()->with('alerta', 'No se encontro el archivo.');
                }

            } else {
                //No existe el usuario
                return back()->with('alerta', 'No se encontro el usuario.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function eliminarArchivoExpedienteUsuario($idFranquicia, $idUsuario, $indice){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) //ROL DE ADMIN, DIRECTOR, PRINCIPAL
        {
            $existeUsuario = DB::select("SELECT id FROM users WHERE id = '$idUsuario'");
            $existeArchivo = DB::select("SELECT eu.documento, eu.descripcion FROM expedienteusuarios eu WHERE eu.indice = '$indice'
                                              AND eu.id_usuario = '$idUsuario' AND eu.id_franquicia = '$idFranquicia'");

            if($existeUsuario != null){

                if($existeArchivo != null){
                    //Existe un archivo almacenado
                    $archivo = $existeArchivo[0]->documento;

                    //Eliminar imagen del servidor
                    try {
                        Storage::disk('disco')->delete($archivo);

                        //Eliminar registro de BD
                        DB::delete("DELETE FROM expedienteusuarios WHERE  indice = '$indice'
                                              AND id_usuario = '$idUsuario' AND id_franquicia = '$idFranquicia'");

                        //Insertar registro en tabla historialsucursal
                        $usuario = DB::select("SELECT u.name FROM users u WHERE u.id = '$idUsuario'");
                        $id_usuarioC = Auth::id();
                        self::insertarHistorialSucursal($idFranquicia, $id_usuarioC, "Elimino documento del expediente de usuario: '" .$usuario[0]->name ."' con descripcion: '". $existeArchivo[0]->descripcion . "'", "14", "2");

                        return back()->with('bien',"Archivo eliminado correctamente.");

                    }catch(\Exception $e){
                        \Log::info("Error: ".$e);
                    }

                } else {
                    return back()->with('alerta', 'No se encontro el archivo.');
                }

            }else{
                //No existe el usuario
                return back()->with('alerta', 'No se encontro el usuario.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function asignarVehiculoUsuarioChofer($idFranquicia, $idUsuario, Request $request){
        if (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) //ROL DEL DIRECTOR, ADMIN, PRINCIPAL
        {
            $indiceVehiculo = $request->input('vehiculoAsignado');

            if($indiceVehiculo != null){

                $existeUsuario = DB::select("SELECT id FROM users WHERE id = '$idUsuario' AND (rol_id = 4 OR rol_id = 17)");
                if($existeUsuario != null){
                    $vehiculoAsignado = DB::select("SELECT * FROM vehiculosusuarios vu WHERE vu.id_franquicia = '$idFranquicia' AND vu.id_vehiculo = '$indiceVehiculo'");

                    if($vehiculoAsignado == null){
                        //Vehiculo sin ser asignado - Verificar si no tiene otro ya asignado
                        $vehiculoUsuario = DB::select("SELECT * FROM vehiculosusuarios vu WHERE vu.id_franquicia = '$idFranquicia' AND vu.id_usuario = '$idUsuario'");

                        if($vehiculoUsuario != null){
                            //Tiene ya un vehiculo asignado - Verificar si no tiene asignacion pendiente
                            $id_vehiculoAsignado = $vehiculoUsuario[0]->id_vehiculo;
                            $supervisionPendiente = DB::select("SELECT vs.estado, (SELECT v.placas FROM vehiculos v WHERE v.indice = vs.id_vehiculo) as placas
                                                                        FROM vehiculossupervision vs WHERE vs.id_vehiculo = '$id_vehiculoAsignado'
                                                                        AND vs.id_franquicia = '$idFranquicia' AND vs.id_usuario = '$idUsuario' ORDER BY vs.created_at DESC LIMIT 1");
                            if($supervisionPendiente != null){
                                if($supervisionPendiente[0]->estado == 0){
                                    return back()->with('alerta', 'No puedes generar la asignación porque hay una supervisión pendiente de autorización para el vehículo asignado actualmente con placas: ' . "'" . $supervisionPendiente[0]->placa . "'". '. Por favor, solicita la autorización de la supervisión y luego procede con la asignación del nuevo vehículo.');
                                }
                            }

                            //Eliminar asignacion
                            $vehiculo = $vehiculoUsuario[0]->indice;
                            DB::delete("DELETE FROM vehiculosusuarios vu
                                          WHERE vu.indice = '$vehiculo' AND vu.id_usuario = '$idUsuario'
                                          AND vu.id_franquicia = '$idFranquicia'");
                        }

                        //Asignar nuevo vehiculo
                        DB::table('vehiculosusuarios')->insert([
                            'id_franquicia' => $idFranquicia, 'id_usuario' => $idUsuario, 'id_vehiculo' => $indiceVehiculo, 'created_at' => Carbon::now()
                        ]);

                        //Verificar si se le asigno un vehiculo con una supervision sin usuario creada en la noche anteriror
                        $existeSupervisionSinusuario = DB::select("SELECT vs.indice FROM vehiculossupervision vs WHERE vs.id_vehiculo = '$indiceVehiculo'
                                                                         AND vs.id_franquicia = '$idFranquicia' AND (vs.id_usuario = '' OR vs.id_usuario IS NULL) AND vs.estado = 0");

                        if($existeSupervisionSinusuario != null){
                            //Tenia una supervision sin usuario pendiente
                            $indiceSupervision = $existeSupervisionSinusuario[0]->indice;
                            DB::table('vehiculossupervision')->where("indice","=","$indiceSupervision")->update([
                                'id_usuario' => $idUsuario, 'created_at' => Carbon::now()
                            ]);
                        }

                        return back()->with('bien', 'Vehículo asignado correctamente.');

                    }else{
                        //Vehiculo ya asignado
                        return back()->with('alerta', 'No puedes asignar el mismo vehiculo a dos usuarios.');
                    }
                }else{
                    //No existe el usuario
                    return back()->with('alerta', 'No se encontro el usuario.');
                }
            }else{
                //No se selecciono una opcion valida
                return back()->with('alerta', 'Selecciona un vehículo para asignar al usuario.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function quitarAsignacionVehiculoUsuarioChofer($idFranquicia, $idUsuario){
        if (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) //ROL DEL DIRECTOR, ADMIN, PRINCIPAL
        {
            $existeUsuario = DB::select("SELECT id FROM users WHERE id = '$idUsuario' AND (rol_id = 4 OR rol_id = 17)");

            if($existeUsuario != null){
                $vehiculoUsuario = DB::select("SELECT * FROM vehiculosusuarios vu WHERE vu.id_franquicia = '$idFranquicia' AND vu.id_usuario = '$idUsuario'");

                if($vehiculoUsuario != null){
                    // Verificar si no tiene asignacion pendiente
                    $id_vehiculoAsignado = $vehiculoUsuario[0]->id_vehiculo;
                    $supervisionPendiente = DB::select("SELECT vs.estado, (SELECT v.placas FROM vehiculos v WHERE v.indice = vs.id_vehiculo) as placas FROM vehiculossupervision vs WHERE vs.id_vehiculo = '$id_vehiculoAsignado'
                                                                         AND vs.id_franquicia = '$idFranquicia' AND vs.id_usuario = '$idUsuario' ORDER BY vs.created_at DESC LIMIT 1");
                    if($supervisionPendiente != null){
                        if($supervisionPendiente[0]->estado == 0){
                            return back()->with('alerta', 'No puedes eliminar la asignación debido a que hay una supervisión pendiente de autorización en el vehículo asignado actualmente con placas: ' . "'" . $supervisionPendiente[0]->placas . "'" . '. Solicita la autorización de la supervisión y luego intenta eliminar la asignación nuevamente.');
                        }
                    }

                    $vehiculo = $vehiculoUsuario[0]->indice;

                    DB::delete("DELETE FROM vehiculosusuarios WHERE indice = '$vehiculo' AND id_usuario = '$idUsuario' AND id_franquicia = '$idFranquicia'");

                    return back()->with('bien', 'Asignacion de vehiculo eliminada correctamente.');
                    } else {
                    //No tiene asignacion el usuario seleccionado
                    return back()->with('alerta', 'Usuario no cuenta con una asignacion de vehiculo.');
                }
            }else{
                //No existe el usuario
                return back()->with('alerta', 'No se encontro el usuario.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function cargarlistausuariosasignados(Request $request){
        if (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) //ROL DEL DIRECTOR, ADMIN, PRINCIPAL
        {
            $buscar = $request->input('buscar');
            $idFranquicia =  $request->input('id_franquicia');

            if (strlen($buscar) > 0) {
                //Buscar diferente de vacio
                $usuarios = DB::select("SELECT u.name, (SELECT f.ciudad FROM franquicias f WHERE f.id = uf.id_franquicia) as sucursal, (SELECT r.rol FROM roles r WHERE r.id = u.rol_id) AS ROL
                                                       FROM users u
                                                      INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id  WHERE u.name LIKE '%$buscar%' AND uf.id_franquicia != '00000'
                                                      ORDER BY sucursal ASC, u.name ASC");

                $response = ['usuarios' => $usuarios];

                //Registrar busqueda de usuarios franquicia en historialsucursal
                $cambio = "Busco usuarios por: '" .$request->input('filtro'). "'";

                DB::table('historialsucursal')->insert([
                    'id_usuarioC' => Auth::user()->id,
                    'id_franquicia' => $idFranquicia, 'tipomensaje' => '7',
                    'created_at' => Carbon::now(), 'cambios' => $cambio, 'seccion' => '0'
                ]);

            } else {
                //Filtro vacio
                $usuarios = DB::select("SELECT u.name, (SELECT f.ciudad FROM franquicias f WHERE f.id = uf.id_franquicia) as sucursal, (SELECT r.rol FROM roles r WHERE r.id = u.rol_id) AS ROL
                                                      FROM users u
                                                      INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id  WHERE uf.id_franquicia != '00000'
                                                      ORDER BY sucursal ASC, u.name ASC");

                $response = ['usuarios' => $usuarios];
            }
            return response()->json($response);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function descargarcurriculumcitavacante($idFranquicia, $indice)
    {
        if (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) //ROL DEL DIRECTOR
        {
            $existeArchivo = DB::select("SELECT v.curriculum FROM vacantes v WHERE v.indice = '$indice'");

            if($existeArchivo != null){
                //Existe un archivo almacenado
                $archivo = $existeArchivo[0]->curriculum;
                if($archivo != null){
                    return Storage::disk('disco')->download($archivo);
                }else{
                    return back()->with('alerta', 'No se encontro el archivo.');
                }

            }
            else {
                return back()->with('alerta', 'No se encontro el archivo.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function solicitarautorizacioncambiocobranza($idFranquicia, $idusuario){
        if (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) //ROL DEL DIRECTOR, ADMINISTRACION Y PRINCIPAL
        {
            try {
                $idUsuarioC = Auth::user()->id;

                $cobrador = DB::select("SELECT u.name FROM users u WHERE u.id = '$idusuario'");
                //Insertar solicitud de autorizacion
                DB::table('autorizaciones')->insert([
                    'id_referencia' => $idusuario, 'id_usuarioC' => $idUsuarioC, 'id_franquicia' => $idFranquicia,
                    'mensaje' => "Solicitó autorizacion para cambio de cobranza a supervisor para: '" . $cobrador[0]->name . "'",
                    'estatus' => '0', 'tipo' => '14', 'created_at' => Carbon::now()
                ]);

                return back()->with('bien', 'Solicitud de cambio de rol cobranza generada correctamente');

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

    public function actualizarestadoautorizacioncambiocobranza($idFranquicia,$indice,$estado){
        if (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) //ROL DEL DIRECTOR, ADMINISTRACION Y PRINCIPAL
        {
            //estado
            // 1 -> aprobado
            // 2 -> rechazado

            $existeAutorizacion = DB::select("SELECT * FROM autorizaciones a WHERE a.indice = '$indice'");

            if ($existeAutorizacion != null){

                $id_usuario = $existeAutorizacion[0]->id_referencia;
                $cobrador = DB::select("SELECT u.name FROM users u WHERE u.id = '$id_usuario'");

                switch ($estado){
                    case 1:
                        $mensaje = "Solicitud de autorizacion aprobada correctamente.";
                        $cambio = "Autorizó solicitud para cambio de cobranza a supervisor para: '" . $cobrador[0]->name . "'";
                        break;
                    case 2:
                        $mensaje = "Solicitud de autorizacion rechazada correctamente.";
                        $cambio = "Rechazó solicitud para cambio de cobranza a supervisor para: '" . $cobrador[0]->name . "'";
                        break;
                }

                //Actualizar estatus de autorizacion
                DB::table('autorizaciones')->where('indice', '=', $indice)->update(['estatus' => $estado, 'updated_at' => Carbon::now()]);

                //Registrar movimeinto sucursal
                DB::table('historialsucursal')->insert([
                    'id_usuarioC' => Auth::user()->id,
                    'id_franquicia' => $idFranquicia, 'tipomensaje' => '0',
                    'created_at' => Carbon::now(), 'cambios' => $cambio, 'seccion' => '0'
                ]);

                return back()->with('bien',$mensaje);

            }else{
                //No existe solicitu de autorizacion
                return back()->with('alerta',"Solicitud de autorización no existente.");
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public static function generarIdentificadorVacantes($length)
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $globalesServicioWeb = new globalesServicioWeb();
            $temporalId = $globalesServicioWeb::generadorRandom($length);
            $existente = DB::select("select indice from vacantes where identificador = '$temporalId' LIMIT 1");
            if (sizeof($existente) == 0) {
                $unico = $temporalId;
                $esUnico = true;
            }
        }
        return $unico;
    }

    public static function obtenerIdentificadorFormatoFranquicia($idFranquicia){
        $identificador ="000";
        $franquicia = DB::select("SELECT f.indice FROM franquicias f WHERE f.id = '$idFranquicia' LIMIT 1");

        if($franquicia != null){
            $indice = $franquicia[0]->indice;
            switch (strlen($indice)){
                case 1:
                    //Indice del 1-9
                    $identificador = "00" . $indice;
                    break;
                case 2:
                    //Indice del 10-99
                    $identificador = "0" . $indice;
                    break;
                case 3:
                    //Indice del 100-999
                    $identificador = $indice;
                    break;
            }
        }
        return $identificador;
    }

    public function actualizarestatususuario($idFranquicia, $idUsuario)
    {

        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {

            try {

                $usuario = DB::select("SELECT u.name, u.rol_id FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                              WHERE uf.id_franquicia = '$idFranquicia' AND u.id = '$idUsuario'");
                if ($usuario != null) {
                    //Existe usuario

                    $nombreusuario = $usuario[0]->name;
                    $rolusuario = $usuario[0]->rol_id;

                    if (((Auth::user()->id) != 1) && ((Auth::user()->id) != 19) && ((Auth::user()->id) != 376) && ((Auth::user()->id) != 61) && ((Auth::user()->id) != 761)) {
                        //Usuario es diferente a Christian, Alan, Fernando, Yo o Ramon
                        if ((((Auth::user()->rol_id) == 6) && ($rolusuario == 6 || $rolusuario == 7 || $rolusuario == 8))
                            || (((Auth::user()->rol_id) == 7) && ($rolusuario == 7))
                            || (((Auth::user()->rol_id) == 8) && ($rolusuario == 7 || $rolusuario == 8))) {
                            //Administrador quiere suspender a un administrador, director o principal
                            return back()->with("alerta", "No puedes suspender el usuario $nombreusuario ya que tiene mayor o igual privilegios que tú.");
                        }
                    }

                    $estatususuario = request('estatususuario') == null ? 2 : 1;

                    $mensaje = "suspendió";
                    if ($estatususuario == 1){
                        $mensaje = "activo";
                    }

                    //Actualizar atributo estatus en tabla usuario
                    DB::table('users')->where('id', $idUsuario)->update([
                        'estatus' => $estatususuario
                    ]);

                    //Insertar movimiento en historialsucursal
                    self::insertarHistorialSucursal($idFranquicia, Auth::user()->id, "Se $mensaje el usuario $nombreusuario por administración","4", "0");

                    return back()->with("bien", "Se $mensaje el usuario: $nombreusuario");
                }
                //No existe el contrato
                return back()->with("alerta", "El usuario no existe.");

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

    public function actualizarexcepcionasistenciausuario($idFranquicia, $idUsuario)
    {

        if (Auth::check() && ((Auth::user()->id) == 1) || ((Auth::user()->id) == 61) || ((Auth::user()->id) == 761)) {

            try {

                $usuario = DB::select("SELECT u.name, u.rol_id FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                              WHERE uf.id_franquicia = '$idFranquicia' AND u.id = '$idUsuario'");
                if ($usuario != null) {
                    //Existe usuario

                    $contratosGlobal = new contratosGlobal();
                    $nombreusuario = $usuario[0]->name;

                    $estatusexcepcionusuario = request('estatusexcepcionusuario') == null ? 0 : 1;

                    $mensaje = "";
                    if ($estatusexcepcionusuario == 1){
                        $contratosGlobal::insertareliminaridusuarioexcepciones($idUsuario, 0, true);
                        $mensaje = "activo";
                    }else {
                        $contratosGlobal::insertareliminaridusuarioexcepciones($idUsuario, 0, false);
                        $mensaje = "desactivo";
                    }

                    //Insertar movimiento en historialsucursal
                    self::insertarHistorialSucursal($idFranquicia, Auth::user()->id, "Se $mensaje el usuario $nombreusuario para la excepción de asistencia","4", "0");

                    return back()->with("bien", "Se $mensaje el usuario: $nombreusuario para la excepción de asistencia");
                }
                //No existe el contrato
                return back()->with("alerta", "El usuario no existe.");

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

    public function eliminarzonausuario($idFranquicia, $idUsuario){
        if (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) //ROL DEL DIRECTOR, ADMINISTRACION Y PRINCIPAL
        {
            try {

                $usuario = DB::select("SELECT u.name, u.rol_id, u.id_zona FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id
                                                              WHERE uf.id_franquicia = '$idFranquicia' AND u.id = '$idUsuario'");

                if ($usuario != null) {
                    //Existe usuario

                    if ($usuario[0]->rol_id == 4) {
                        //Rol cobranza

                        //Actualizar atributo estatus en tabla usuario
                        DB::table('users')->where('id', $idUsuario)->update([
                            'id_zona' => null
                        ]);

                        $nombreZonaAnterior = DB::select("SELECT zona FROM zonas WHERE id = '" . $usuario[0]->id_zona . "' LIMIT 1");
                        $nombreZonaAnterior = $nombreZonaAnterior == null ? "" : $nombreZonaAnterior[0]->zona;

                        //Insertar movimiento en historialsucursal
                        self::insertarHistorialSucursal($idFranquicia, Auth::user()->id, "Se quito la zona '$nombreZonaAnterior' al cobrador " . $usuario[0]->name, "4", "0");

                        return back()->with('bien', 'Se quito la zona correctamente al cobrador');

                    }
                    //Rol diferente de cobranza
                    return back()->with("alerta", "El usuario no es cobrador.");

                }
                //No existe el contrato
                return back()->with("alerta", "El usuario no existe.");

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

    public function generarcodigodebarrasusuario($idFranquicia, $idusuario){

        $existeUsuario = DB::select("SELECT u.name, u.barcode FROM users u WHERE u.id = '$idusuario'");
        if($existeUsuario != null){
            // Crea una instancia del generador de códigos de barras PNG
            $generator = new BarcodeGeneratorPNG();
            $barcode = $existeUsuario[0]->barcode;

            // Genera el código de barras en formato PNG
            $imagenBarCode = $generator->getBarcode($barcode, $generator::TYPE_CODE_128);

            $nombreArchivo = "codigo_" . str_replace(" ", "", $existeUsuario[0]->name) . ".png";
            //Almacenar imagen codifo de barras en carpeta
            Storage::disk('disco')->put('/uploads/imagenes/barcode/' . $nombreArchivo, $imagenBarCode);

            // Obtener el contenido del archivo y descargar
            return Storage::disk('disco')->download('/uploads/imagenes/barcode/' . $nombreArchivo);

        }else{
            return back()->with('alerta', "Usuario no encontrado");
        }

    }

    //Ejecutar solo una vez
    function actualizarcampobarcodeusuariobd(){
        $usuarios = DB::select("SELECT u.id, u.barcode FROM  users u WHERE u.barcode is NULL ORDER BY u.name ASC");

        foreach ($usuarios as $usuario){
            //generar numero random de 9 digitos para codigo de barras por usuario
            DB::table('users')->where('id','=',$usuario->id)->update([
                'barcode'=> self::generarbarcodealeatorio()
            ]);

        }

        return back()->with('bien',"Codigos de barra de usuarios actualizados correctamente");
    }

    public function generarbarcodealeatorio(){
        $barcode = "";
        $esUnico = false;
        while (!$esUnico) {
            for ($i = 0; $i < 10; $i++){
                $barcode.= rand(0,9);
            }

            $existeBarcodeBD = DB::select("SELECT u.barcode FROM  users u WHERE u.barcode = '$barcode'");
            if ($existeBarcodeBD == null) {
                $esUnico = true;
            }
        }

        return $barcode;
    }
}
