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

class historialclinico extends Controller
{
    public function nuevohistorialclinico($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12) ||
            Auth::check() && ((Auth::user()->rol_id) == 6)) {
            $fotocromatico = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'fotocromático'");
            $AR = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'A/R'");
            $tinte = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'tinte'");
            $blueray = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'BlueRay'");
            $paquetes = DB::select("SELECT * FROM paquetes WHERE id_franquicia = '$idFranquicia'");
            $armazones = DB::select("SELECT * FROM producto WHERE  id_tipoproducto = '1' order by nombre");
            //contrato
            $idUsuario = Auth::user()->id;
            $ultimoOptometrista = DB::select("SELECT
                                id_optometrista as ID,  u.name as NAME
                                from contratos c
                                inner join users u on c.id_optometrista = u.id
                                AND c.id_usuariocreacion = '$idUsuario'
                                order by c.created_at desc
                                limit 1");
            $ultimaZona = DB::select("SELECT
            id_zona as ID,  z.zona as zona
            from contratos c
            inner join zonas z on c.id_zona = z.id
            AND c.id_usuariocreacion = '$idUsuario'
            order by c.created_at desc
            limit 1");
            $zonas = DB::select("SELECT id as ID, zona as zona FROM zonas where id_franquicia = '$idFranquicia'");
            $optometristas = DB::select("SELECT u.ID,u.NAME
                                FROM users u
                                INNER JOIN usuariosfranquicia uf
                                ON uf.id_usuario = u.id
                                WHERE uf.id_franquicia = '$idFranquicia'
                                AND u.rol_id = 12");
            $usuaricontrato = DB::select("SELECT COUNT(id) as prueba FROM contratos WHERE id_usuariocreacion = '$idUsuario' AND estatus_estadocontrato = 0");
            $contratohoypromo = DB::select("SELECT id FROM contratos WHERE id_usuariocreacion = '$idUsuario' AND id_promocion >= 1 AND ifnull(promocionterminada,0) < 1");
            if ($contratohoypromo != null) {
                $contratohoypromoid = $contratohoypromo[0]->id;
            }
            if (Auth::user()->rol_id == 13 && $usuaricontrato[0]->prueba > 0 || Auth::user()->rol_id == 12 && $usuaricontrato[0]->prueba > 0) {
                return redirect()->route('listacontrato', $idFranquicia)->with('alerta', 'Necesitas terminar/cancelar los contratos pendientes para continuar.');
            }
            if ((((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12)) && $contratohoypromo != null) {
                return redirect()->route('listacontrato', $idFranquicia)->with('alerta', 'Revisar el contrato con promoción aun no esta completado ' . $contratohoypromoid);
            }

            return view('administracion.historialclinico.nuevo', ['idFranquicia' => $idFranquicia, 'optometristas' => $optometristas, 'paquetes' => $paquetes, 'fotocromatico' => $fotocromatico,
                'AR' => $AR, 'tinte' => $tinte, 'blueray' => $blueray, 'armazones' => $armazones,
                'ultimoOptometrista' => $ultimoOptometrista, 'ultimaZona' => $ultimaZona, 'zonas' => $zonas]);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function nuevohijo($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12) ||
            Auth::check() && ((Auth::user()->rol_id) == 6)) {
            $fotocromatico = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'fotocromático'");
            $AR = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'A/R'");
            $tinte = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'tinte'");
            $blueray = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'BlueRay'");
            $paquetes = DB::select("SELECT * FROM paquetes WHERE id_franquicia = '$idFranquicia'");
            $armazones = DB::select("SELECT * FROM producto WHERE  id_tipoproducto = '1' order by nombre");
            //contrato
            $idUsuario = Auth::user()->id;
            $ultimoOptometrista = DB::select("SELECT
                            id_optometrista as ID,  u.name as NAME
                            from contratos c
                            inner join users u on c.id_optometrista = u.id
                            AND c.id_usuariocreacion = '$idUsuario'
                            order by c.created_at desc
                            limit 1");
            $ultimaZona = DB::select("SELECT
        id_zona as ID,  z.zona as zona
        from contratos c
        inner join zonas z on c.id_zona = z.id
        AND c.id_usuariocreacion = '$idUsuario'
        order by c.created_at desc
        limit 1");
            $zonas = DB::select("SELECT id as ID, zona as zona FROM zonas where id_franquicia = '$idFranquicia'");
            $optometristas = DB::select("SELECT u.ID,u.NAME
                            FROM users u
                            INNER JOIN usuariosfranquicia uf
                            ON uf.id_usuario = u.id
                            WHERE uf.id_franquicia = '$idFranquicia'
                            AND u.rol_id = 12");

            return view('administracion.historialclinico.nuevo', ['idFranquicia' => $idFranquicia, 'optometristas' => $optometristas, 'paquetes' => $paquetes, 'fotocromatico' => $fotocromatico,
                'AR' => $AR, 'tinte' => $tinte, 'blueray' => $blueray, 'armazones' => $armazones,
                'ultimoOptometrista' => $ultimoOptometrista, 'ultimaZona' => $ultimaZona, 'zonas' => $zonas]);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }


    public function nuevohistorialclinico2($idFranquicia, $idContrato)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12) ||
            Auth::check() && ((Auth::user()->rol_id) == 6)) {
            $optometristas = DB::select("SELECT u.id,u.name
                                    FROM users u
                                    INNER JOIN usuariosfranquicia uf
                                    ON u.id = uf.id_usuario
                                    WHERE u.rol_id = 12
                                    AND uf.id_franquicia = '$idFranquicia' ");
            $datosContrato = DB::select("SELECT * FROM contratos WHERE id = '$idContrato'");
            $hc2 = DB::select("SELECT COUNT(id) as canti FROM historialclinico WHERE id_contrato = '$idContrato'");
            $historialC = DB::select("SELECT h.edad, h.diagnostico, h.hipertension, h.diabetes, h.ocupacion, h.dolor, h.ardor, h.golpeojos, h.otroM, h.molestiaotro, h.ultimoexamen, p.nombre
        FROM historialclinico h
        INNER JOIN paquetes p
        ON p.id = h.id_paquete
        WHERE id_contrato = '$idContrato'");
            $fotocromatico = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'fotocromático'");
            $AR = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'A/R'");
            $tinte = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'tinte'");
            $blueray = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'BlueRay'");
            $paquetes = DB::select("SELECT * FROM paquetes WHERE id_franquicia = '$idFranquicia'");
            $armazones = DB::select("SELECT * FROM producto WHERE id_tipoproducto = '1' order by nombre");
            $cont = $hc2[0]->canti;

            if ($cont == 2) {
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])->with('alerta', 'Llenar los historiales clinicos correctamente.');
            }

            return view('administracion.historialclinico.nuevo2', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato, 'optometristas' => $optometristas,
                'paquetes' => $paquetes, 'fotocromatico' => $fotocromatico, 'AR' => $AR, 'tinte' => $tinte, 'blueray' => $blueray, 'datosContrato' => $datosContrato,
                'armazones' => $armazones, 'historialC' => $historialC]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarhistorial($idFranquicia, $idContrato, $idHistorial)
    {

        $datosHistorial = DB::select("SELECT h.edad, h.diagnostico, h.hipertension, h.diabetes, h.ocupacion, h.dolor, h.ardor, h.golpeojos, h.otroM, h.molestiaotro, h.ultimoexamen,
        p.nombre, pr.nombre as nombre2, h.id_contrato, h.fechaentrega, h.esfericoder, h.cilindroder, h.ejeder, h.addder, h.altder, h.esfericoizq, h.cilindroizq, h.ejeizq, h.addizq, h.altizq,
        h.material, h.materialotro, h.costomaterial, h.bifocal, h.fotocromatico, h.ar, h.tinte, h.blueray, h.otroT, h.tratamientootro, h.costotratamiento, h.observaciones,h.observacionesinterno
        FROM historialclinico h
        INNER JOIN paquetes p
        ON p.id = h.id_paquete
        INNER JOIN producto pr
        ON pr.id = h.id_producto
        WHERE h.id = '$idHistorial' AND h.id_contrato = '$idContrato'");
        $fotocromatico = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'fotocromático'");
        $AR = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'A/R'");
        $tinte = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'tinte'");
        $blueray = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'BlueRay'");

        return view('administracion.historialclinico.actualizarhistorial', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato,
            'idHistorial' => $idHistorial, 'datosHistorial' => $datosHistorial, 'fotocromatico' => $fotocromatico,
            'ar' => $AR, 'tinte' => $tinte, 'blueray' => $blueray]);
    }

    private function getContratoId()
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $temporalId = $this->generadorRandom();
            $existente = DB::select("select id from contratos where id = '$temporalId'");
            if (sizeof($existente) == 0) {
                $unico = $temporalId;
                $esUnico = true;
            }
        }
        return $unico;
    }


    private function generadorRandom($length = 10)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($caracteres);
        $randomId = '';
        for ($i = 0; $i < $length; $i++) {
            $randomId .= $caracteres[rand(0, $charactersLength - 1)];
        }
        return $randomId;
    }

    private function getHistorialId5()
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $temporalId = $this->generadorRandom5();
            $existente = DB::select("select id from contratos where id = '$temporalId'");
            if (sizeof($existente) == 0) {
                $unico = $temporalId;
                $esUnico = true;
            }
        }
        return $unico;
    }

    private function getHistorialId2()
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $temporalId = $this->generadorRandom5();
            $existente = DB::select("select id from historialclinico where id = '$temporalId'");
            if (sizeof($existente) == 0) {
                $unico = $temporalId;
                $esUnico = true;
            }
        }
        return $unico;
    }

    public function getContratoHistorialId()
    {
        $unico = "";
        $esUnico = false;
        while (!$esUnico) {
            $temporalId = $this->generadorRandom5();
            $existente = DB::select("select id from historialcontrato where id = '$temporalId'");
            if (sizeof($existente) == 0) {
                $unico = $temporalId;
                $esUnico = true;
            }
        }
        return $unico;
    }

    private function generadorRandom5($length = 5)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($caracteres);
        $randomId = '';
        for ($i = 0; $i < $length; $i++) {
            $randomId .= $caracteres[rand(0, $charactersLength - 1)];
        }
        return $randomId;
    }

    // Generador random

    public function crearhistorialclinico($idFranquicia, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12) ||
            Auth::check() && ((Auth::user()->rol_id) == 6)) {
            $idContrato = request('idcontrato');
            $idcontratopadre = request('idcontratopadre');
            if ($idcontratopadre != null) {
                $contratovalido = DB::select(" SELECT co.id,pro.armazones,(select count(id) from contratos where idcontratorelacion = '$idcontratopadre') as totalhijos
            from contratos co
            inner join promocion pro
            on pro.id = co.id_promocion
            where co.id = '$idcontratopadre';");
                if ($contratovalido != null) {
                    if (($contratovalido[0]->totalhijos + 1) < $contratovalido[0]->armazones) {
                        $rules2 = [

                            'nombre' => 'required|string|max:255',
                            'telefono' => 'required|string|size:10|regex:/[0-9]/',
                            // 'fotoine'=>'required|image|mimes:jpg',
                            // 'fotoineatras'=>'required|image|mimes:jpg',
                            // 'fotocasa'=>'required|image|mimes:jpg',
                            // 'comprobantedomicilio'=>'required|image|mimes:jpg',
                            'edad' => 'required|string|max:255',
                            'diagnostico' => 'required|string|max:255',
                            'ocupacion' => 'required|string|max:255',
                            'diabetes' => 'required|string|max:255',
                            'hipertension' => 'required|string|max:255',
                            'paquete' => 'required|integer',
                            'producto' => 'required|string|max:255',
                            'ultimoexamen' => 'nullable|date',
                            'correo' => 'nullable|email',
                            'fechaentrega' => 'required|date'

                        ];
                        if (request('material') != '3' && request('motro') != null && request('costomaterial') != null) {
                            return back()->withErrors(['motro' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
                        }
                        if (request('material') == '3' && request('motro') == null && request('costomaterial') == null) {
                            return back()->withErrors(['motro' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
                        }
                        if (request('material') == '3' && request('motro') == null && request('costomaterial') != null) {
                            return back()->withErrors(['motro' => 'Llenar ambos campos para otro.'])->withInput($request->all());
                        }
                        if (request('material') == '3' && request('motro') != null && request('costomaterial') == null) {
                            return back()->withErrors(['motro' => 'Llenar ambos campos para otro.'])->withInput($request->all());
                        }
                        if (request('molestia') != null && request('otroM') == null) {
                            return back()->withErrors(['molestia' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
                        }
                        if (request('otroTra') != null && request('otroT') == null && request('costoT') == null) {
                            return back()->withErrors(['otroT' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
                        }
                        if (request('otroTra') != null && request('otroT') != null && request('costoT') == null) {
                            return back()->withErrors(['otroT' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
                        }
                        if (request('otroTra') != null && request('otroT') == null && request('costoT') != null) {
                            return back()->withErrors(['otroT' => 'Llenar ambos campos para otro tratamiento.'])->withInput($request->all());
                        }
                        if (request('otroTra') == null && request('otroT') != null && request('costoT') != null) {
                            return back()->withErrors(['otroT' => 'Llenar ambos campos para otro tratamiento.'])->withInput($request->all());
                        }
                        if (request('otroTra') == null && request('otroT') != null && request('costoT') == null) {
                            return back()->withErrors(['otroT' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
                        }
                        if (request('otroTra') == null && request('otroT') == null && request('costoT') != null) {
                            return back()->withErrors(['costoT' => 'Llenar ambos campos para otro tratamiento.'])->withInput($request->all());
                        }
                        if (request('dolor') == null && request('ardor') == null && request('golpe') == null && request('molestia') == null && request('otroM') == null) {
                            return back()->withErrors(['molestia' => 'Elegir al menos una molestia.'])->withInput($request->all());
                        }
                        if (request('fotocromatico') == null && request('ar') == null && request('tinte') == null && request('blueray') == null && request('otroTra') == null) {
                            return back()->withErrors(['fotocromatico' => 'Elegir al menos una molestia.'])->withInput($request->all());
                        }
                        if (request('paquete') == 0) {
                            return back()->withErrors(['paquete' => 'Campo obligatorio.'])->withInput($request->all());
                        }
                        if (request('producto') == 'nada') {
                            return back()->withErrors(['producto' => 'Campo obligatorio.'])->withInput($request->all());
                        }
                        if (request('tinte') == 1 && request('paquete') == 1) {
                            return back()->withErrors(['tinte' => 'No se permite agregar tinte con este paquete.'])->withInput($request->all());
                        }
                        if (request('tinte') == 1 && request('paquete') == 2) {
                            return back()->withErrors(['tinte' => 'No se permite agregar tinte con este paquete.'])->withInput($request->all());
                        }
                        if (request('zona') == 'Seleccionar') {
                            return back()->withErrors(['zona' => 'Elige una zona, campo obligatorio.'])->withInput($request->all());
                        }
                        if (request('optometrista') == 'Seleccionar') {
                            return back()->withErrors(['optometrista' => 'Elige una zona, campo obligatorio.'])->withInput($request->all());
                        }
                        if (request('telefono') == null && request('correo') == null) {
                            return back()->withErrors(['correo' => 'Ingresar algun correo o el telefono del cliente.'])->withInput($request->all());
                        }
                        if (request('ar') != null && request('blueray') != null) {
                            return back()->withErrors(['ar' => 'Solo se puede elegir uno entre AR y BlueRay.'])->withInput($request->all());
                        }
                        if (request('paquete') == 1) {
                            if (request('fotocromatico') != null && request('ar') != null || request('blueray') != null || request('otroTra') != null) {
                                $n = 0;
                                if (request('fotocromatico') == 1) {

                                    $n = $n + 1;
                                }
                                if (request('ar') == 1) {

                                    $n = $n + 1;
                                }
                                if (request('blueray') == 1) {

                                    $n = $n + 1;
                                }
                                if (request('otroTra') == 1) {
                                    $n = $n + 1;
                                }

                                if ($n > 1) {

                                    return back()->withErrors(['fotocromatico' => 'Solo se permite un tratamiento con el paquete de lectura.'])->withInput($request->all());
                                }
                            }
                        }
                        request()->validate($rules2);
                        if (request('principal') == null) {
                            $validacion = Validator::make($request->all(), [
                                'fotoine' => 'required|image',
                                'fotoineatras' => 'required|image',
                                'fotocasa' => 'required|image',
                                'comprobantedomicilio' => 'required|image'
                            ]);


                            if ($validacion->fails()) {
                                return back()->withErrors([
                                    'fotoine' => 'Campo requerido',
                                    'fotoineatras' => 'Campo requerido',
                                    'fotocasa' => 'Campo requerido',
                                    'comprobantedomicilio' => 'Campo requerido'
                                ])->withInput($request->all());
                            }
                        }


                        if (request('paquete') == 1) {
                            $validacion = Validator::make($request->all(), [
                                'esfericod' => 'required|string',
                                'cilindrod' => 'required|string',
                                'ejed' => 'required|string',
                                'altd' => 'required|string',
                                'esfericod2' => 'required|string',
                                'cilindrod2' => 'required|string',
                                'ejed2' => 'required|string',
                                'altd2' => 'required|string',

                            ]);
                            if ($validacion->fails()) {
                                return back()->withErrors([
                                    'esfericod' => 'Campo requerido para LECTURA',
                                    'cilindrod' => 'Campo requerido para LECTURA',
                                    'ejed' => 'Campo requerido para LECTURA',
                                    'altd' => 'Campo requerido para LECTURA',
                                    'esfericod2' => 'Campo requerido para LECTURA',
                                    'cilindrod2' => 'Campo requerido para LECTURA',
                                    'ejed2' => 'Campo requerido para LECTURA',
                                    'altd2' => 'Campo requerido para LECTURA',
                                ])->withInput($request->all());
                            }


                        }
                        if (request('paquete') == 3) {
                            $validacion = Validator::make($request->all(), [
                                'esfericod' => 'required|string',
                                'cilindrod' => 'required|string',
                                'ejed' => 'required|string',
                                'esfericod2' => 'required|string',
                                'cilindrod2' => 'required|string',
                                'ejed2' => 'required|string',

                            ]);
                            if ($validacion->fails()) {
                                return back()->withErrors([
                                    'esfericod' => 'Campo requerido para ECO JR',
                                    'cilindrod' => 'Campo requerido para ECO JR',
                                    'ejed' => 'Campo requerido para ECO JR',
                                    'esfericod2' => 'Campo requerido para ECO JR',
                                    'cilindrod2' => 'Campo requerido para ECO JR',
                                    'ejed2' => 'Campo requerido para ECO JR',
                                ])->withInput($request->all());
                            }
                        }

                        if (request('paquete') == 4) {
                            $validacion = Validator::make($request->all(), [
                                'esfericod' => 'required|string',
                                'cilindrod' => 'required|string',
                                'ejed' => 'required|string',
                                'esfericod2' => 'required|string',
                                'cilindrod2' => 'required|string',
                                'ejed2' => 'required|string',


                            ]);
                            if ($validacion->fails()) {
                                return back()->withErrors([
                                    'esfericod' => 'Campo requerido para JR',
                                    'cilindrod' => 'Campo requerido para JR',
                                    'ejed' => 'Campo requerido para JR',
                                    'esfericod2' => 'Campo requerido para JR',
                                    'cilindrod2' => 'Campo requerido para JR',
                                    'ejed2' => 'Campo requerido para JR',
                                ])->withInput($request->all());
                            }
                        }
                        if (request('paquete') == 5) {
                            $validacion = Validator::make($request->all(), [
                                'esfericod' => 'required|string',
                                'cilindrod' => 'required|string',
                                'ejed' => 'required|string',
                                'addd' => 'required|string',
                                'altd' => 'required|string',
                                'esfericod2' => 'required|string',
                                'cilindrod2' => 'required|string',
                                'ejed2' => 'required|string',
                                'addd2' => 'required|string',
                                'altd2' => 'required|string',

                            ]);
                            if ($validacion->fails()) {
                                return back()->withErrors([
                                    'esfericod' => 'Campo requerido para DORADO 1',
                                    'cilindrod' => 'Campo requerido para DORADO 1',
                                    'ejed' => 'Campo requerido para DORADO 1',
                                    'addd' => 'required|string',
                                    'altd' => 'Campo requerido para DORADO 1',
                                    'esfericod2' => 'Campo requerido para DORADO 1',
                                    'cilindrod2' => 'Campo requerido para DORADO 1',
                                    'ejed2' => 'Campo requerido para DORADO 1',
                                    'addd2' => 'required|string',
                                    'altd2' => 'Campo requerido para DORADO 1',
                                ])->withInput($request->all());
                            }
                        }
                        if (request('paquete') == 6) {
                            $validacion = Validator::make($request->all(), [
                                'esfericod' => 'required|string',
                                'cilindrod' => 'required|string',
                                'ejed' => 'required|string',
                                'esfericod2' => 'required|string',
                                'cilindrod2' => 'required|string',
                                'ejed2' => 'required|string',

                            ]);
                            if ($validacion->fails()) {
                                return back()->withErrors([
                                    'esfericod' => 'Campo requerido para DORADO2',
                                    'cilindrod' => 'Campo requerido para DORADO2',
                                    'ejed' => 'Campo requerido para DORADO2',
                                    'esfericod2' => 'Campo requerido para DORADO2',
                                    'cilindrod2' => 'Campo requerido para DORADO2',
                                    'ejed2' => 'Campo requerido para DORADO2',

                                ])->withInput($request->all());
                            }
                        }
                        if (request('paquete') == 7) {
                            $validacion = Validator::make($request->all(), [
                                'esfericod' => 'required|string',
                                'cilindrod' => 'required|string',
                                'ejed' => 'required|string',
                                'addd' => 'required|string',
                                'altd' => 'required|string',
                                'esfericod2' => 'required|string',
                                'cilindrod2' => 'required|string',
                                'ejed2' => 'required|string',
                                'addd2' => 'required|string',
                                'altd2' => 'required|string',

                            ]);
                            if ($validacion->fails()) {
                                return back()->withErrors([
                                    'esfericod' => 'Campo requerido para PLATINO',
                                    'cilindrod' => 'Campo requerido para PLATINO',
                                    'ejed' => 'Campo requerido para PLATINO',
                                    'addd' => 'Campo requerido para PLATINO',
                                    'altd' => 'Campo requerido para PLATINO',
                                    'esfericod2' => 'Campo requerido para PLATINO',
                                    'cilindrod2' => 'Campo requerido para PLATINO',
                                    'ejed2' => 'Campo requerido para PLATINO',
                                    'addd2' => 'Campo requerido para PLATINO',
                                    'altd2' => 'Campo requerido para PLATINO',
                                ])->withInput($request->all());
                            }


                        }

                        try {
                            // contrato 2
                            $contratos = DB::select("SHOW TABLE STATUS LIKE 'contratos'");
                            $randomId = $this->getContratoId();
                            $randomIdH = $this->getHistorialId5();

                            $fotoine = "";


                            if (request('principal') == null) {

                                $fotoBruta = 'Foto-Ine-Frente-Contrato-' . $randomId . '-' . time() . '.' . request()->file('fotoine')->getClientOriginalExtension();
                                $fotoine = request()->file('fotoine')->storeAs('uploads/imagenes/contratos/fotoine', $fotoBruta, 'disco');

                                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotoine/' . $fotoBruta)->height();
                                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotoine/' . $fotoBruta)->width();
                                if ($alto > $ancho) {
                                    $imagenfotoine = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotoine/' . $fotoBruta)->resize(600, 800);
                                } else {
                                    $imagenfotoine = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotoine/' . $fotoBruta)->resize(800, 600);
                                }
                                $imagenfotoine->save();


                                $fotoBruta2 = 'Foto-Ine-Atras-Contrato-' . $randomId . '-' . time() . '.' . request()->file('fotoineatras')->getClientOriginalExtension();
                                $fotoineatras = request()->file('fotoineatras')->storeAs('uploads/imagenes/contratos/fotoineatras', $fotoBruta2, 'disco');
                                $alto2 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotoineatras/' . $fotoBruta2)->height();
                                $ancho2 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotoineatras/' . $fotoBruta2)->width();
                                if ($alto2 > $ancho2) {
                                    $imagenfotoineatras = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotoineatras/' . $fotoBruta2)->resize(600, 800);
                                } else {
                                    $imagenfotoineatras = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotoineatras/' . $fotoBruta2)->resize(800, 600);
                                }
                                $imagenfotoineatras->save();

                                $fotoBruta3 = 'Foto-Casa-Contrato-' . $randomId . '-' . time() . '.' . request()->file('fotocasa')->getClientOriginalExtension();
                                $fotocasa = request()->file('fotocasa')->storeAs('uploads/imagenes/contratos/fotocasa', $fotoBruta3, 'disco');
                                $alto3 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotocasa/' . $fotoBruta3)->height();
                                $ancho3 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotocasa/' . $fotoBruta3)->width();
                                if ($alto3 > $ancho3) {
                                    $imagenfotocasa = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotocasa/' . $fotoBruta3)->resize(600, 800);
                                } else {
                                    $imagenfotocasa = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotocasa/' . $fotoBruta3)->resize(800, 600);
                                }
                                $imagenfotocasa->save();

                                $fotoBruta5 = 'Foto-Pagare-Contrato-' . $randomId . '-' . time() . '.' . request()->file('pagare')->getClientOriginalExtension();
                                $fotopagare = request()->file('pagare')->storeAs('uploads/imagenes/contratos/pagare', $fotoBruta5, 'disco');
                                $alto5 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/pagare/' . $fotoBruta5)->height();
                                $ancho5 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/pagare/' . $fotoBruta5)->width();
                                if ($alto5 > $ancho5) {
                                    $imagenfotopagare = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/pagare/' . $fotoBruta5)->resize(600, 800);
                                } else {
                                    $imagenfotopagare = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/pagare/' . $fotoBruta5)->resize(800, 600);
                                }
                                $imagenfotopagare->save();

                                $fotoBruta4 = 'Foto-comprobantedomicilio-Contrato-' . $randomId . '-' . time() . '.' . request()->file('comprobantedomicilio')->getClientOriginalExtension();
                                $comprobantedomicilio = request()->file('comprobantedomicilio')->storeAs('uploads/imagenes/contratos/comprobantedomicilio', $fotoBruta4, 'disco');
                                $alto4 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/comprobantedomicilio/' . $fotoBruta4)->height();
                                $ancho4 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/comprobantedomicilio/' . $fotoBruta4)->width();
                                if ($alto4 > $ancho4) {
                                    $imagencomprobantedomicilio = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/comprobantedomicilio/' . $fotoBruta4)
                                                                         ->resize(600, 800);
                                } else {
                                    $imagencomprobantedomicilio = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/comprobantedomicilio/' . $fotoBruta4)
                                                                         ->resize(800, 600);
                                }
                                $imagencomprobantedomicilio->save();
                            }


                            $datos = 1;
                            $creacion = Carbon::now();
                            $usuarioId = Auth::user()->id;
                            $usuarioNombre = Auth::user()->name;


                            $contra = DB::select("SELECT c.id,c.datos,c.id_franquicia,c.id_usuariocreacion,c.nombre_usuariocreacion,z.zona,c.nombre,c.calle,c.numero, c.pago, u.name,
                    pr.titulo, c.telefonoreferencia, c.nombrereferencia, c.correo, c.depto,c.alladode,c.frentea,c.entrecalles,c.colonia,c.localidad,c.telefono,c.casatipo,c.casacolor,
                    c.created_at,c.updated_at,c.id_optometrista, c.id_promocion, c.contador, c.fotoine, c.fotoineatras, c.fotocasa, c.pagare, c.comprobantedomicilio,
                    (SELECT COUNT(id) from promocioncontrato pc where pc.id_contrato = c.id) as promo
                    FROM contratos c
                    INNER JOIN zonas z
                    ON z.id = c.id_zona
                    INNER JOIN users u
                    ON u.id = c.id_optometrista
                    INNER JOIN promocion pr
                    ON pr.id = c.id_promocion
                    WHERE c.datos = 1
                    AND c.id = '$idcontratopadre'
                    AND c.id_franquicia = '$idFranquicia'");
                            $zona = $contra[0]->zona;
                            $optometrista = $contra[0]->id_optometrista;
                            $calle = $contra[0]->calle;
                            $pago = $contra[0]->pago;
                            $numero = $contra[0]->numero;
                            $depto = $contra[0]->depto;
                            $alladode = $contra[0]->alladode;
                            $frentea = $contra[0]->frentea;
                            $entrecalles = $contra[0]->entrecalles;
                            $colonia = $contra[0]->colonia;
                            $localidad = $contra[0]->localidad;
                            $casatipo = $contra[0]->casatipo;
                            $casacolor = $contra[0]->casacolor;
                            $contador = $contra[0]->contador;
                            $telefonoR = $contra[0]->telefonoreferencia;
                            $nombreR = $contra[0]->nombrereferencia;
                            $correo = $contra[0]->correo;
                            $suma = $contador + 1;

                            if (request('principal') == 1) {
                                $fotoine = $contra[0]->fotoine;
                                $fotoineatras = $contra[0]->fotoineatras;
                                $fotocasa = $contra[0]->fotocasa;
                                $comprobantedomicilio = $contra[0]->comprobantedomicilio;
                                $fotopagare = $contra[0]->pagare;
                            }

                            $contratos = DB::select("SHOW TABLE STATUS LIKE 'contratos'");
                            $randomId = $this->getContratoId();
                            $datos = 1;
                            $creacion = Carbon::now();
                            $usuarioId = Auth::user()->id;
                            $usuarioNombre = Auth::user()->name;

                            DB::table('contratos')->insert([
                                'id' => $randomId, 'datos' => $datos, 'id_franquicia' => $idFranquicia, 'id_usuariocreacion' => $usuarioId, 'nombre_usuariocreacion' => $usuarioNombre,
                                'id_zona' => $zona, 'id_optometrista' => $optometrista, 'nombre' => request('nombre'), 'pago' => $pago, 'calle' => $calle, 'numero' => $numero,
                                'depto' => $depto, 'alladode' => $alladode, 'frentea' => $frentea, 'entrecalles' => $entrecalles, 'colonia' => $colonia, 'localidad' => $localidad,
                                'telefono' => request('telefono'), 'casatipo' => $casatipo, 'casacolor' => $casacolor, 'created_at' => $creacion, 'idcontratorelacion' => $idcontratopadre,
                                'nombrereferencia' => $nombreR, 'telefonoreferencia' => $telefonoR, 'correo' => $correo, 'estatus_estadocontrato' => 0, 'fotoine' => $fotoine, 'fotocasa' => $fotocasa,
                                'comprobantedomicilio' => $comprobantedomicilio, 'fotoineatras' => $fotoineatras, 'pagare' => $fotopagare, 'poliza' => null, 'totalpromocion' => 0
                            ]);

                            DB::table('contratos')->where([['id', '=', $idcontratopadre], ['id_franquicia', '=', $idFranquicia]])->update([
                                'contador' => $suma
                            ]);

                            //historial clinico
                            $fotocromatico2 = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'fotocromático'");
                            $AR2 = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'A/R'");
                            $tinte2 = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'tinte'");
                            $blueray2 = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'BlueRay'");
                            $dolor = request('dolor') != Null;
                            $ardor = request('ardor') != Null;
                            $golpe = request('golpe') != Null;
                            $otroM = request('otroM') != Null;
                            $otroTra = request('otroTra') != Null;
                            $fotocromatico = request('fotocromatico');
                            $ar = request('ar');
                            $tinte = request('tinte');
                            $blueray = request('blueray');
                            $otromaterial = request('costomaterial');
                            $otrotratamiento = request('costoT');
                            if ($tinte != null) {
                                $tinte = 1;
                                $tinte5 = $tinte2[0]->precio;
                                $fotocromatico = null;
                                $ar = null;
                                $blueray = null;
                            } else {
                                $tinte = null;
                                $tinte5 = 0;
                            }
                            if ($fotocromatico != null) {
                                $fotocromatico5 = $fotocromatico2[0]->precio;
                                $fotocromatico = 1;
                            } else {
                                $fotocromatico = null;
                                $fotocromatico5 = 0;
                            }
                            if ($ar != null) {
                                $ar5 = $AR2[0]->precio;
                                $ar = 1;
                            } else {
                                $ar = null;
                                $ar5 = 0;
                            }
                            if ($blueray != null) {
                                $blueray5 = $blueray2[0]->precio;
                                $blueray = 1;
                            } else {
                                $blueray = null;
                                $blueray5 = 0;
                            }
                            $totalidad = "";
                            if ($tinte != null && request('paquete') > 2) {
                                $totalidad = $tinte5 + $otromaterial + $otrotratamiento;
                            } else {
                                if (request('paquete') == 2) {
                                    $totalidad = $ar5 + $blueray5 + $otromaterial + $otrotratamiento;
                                } else {
                                    $totalidad = $fotocromatico5 + $ar5 + $blueray5 + $otromaterial + $otrotratamiento;
                                }
                            }


                            $historial = DB::select("SHOW TABLE STATUS LIKE 'historialclinico'");
                            $siguienteId = $historial[0]->Auto_increment;
                            $idContrato = $randomId;
                            $value = request('paquete');
                            $value2 = request('producto');
                            $paquetes = DB::select("SELECT * FROM paquetes WHERE id_franquicia = '$idFranquicia' and id = '$value'");
                            $con = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' and id = '$idContrato'");
                            $armazon = DB::select("SELECT * FROM producto WHERE  id = '$value2' AND id_tipoproducto = '1'");
                            $arma = $armazon[0]->id;
                            $armapz = $armazon[0]->piezas - 1;
                            $hijo = $con[0]->idcontratorelacion;
                            $val = $con[0]->totalhistorial;
                            $valor = $paquetes[0]->precio;
                            $total = $valor + $val + $totalidad;

                            if ($value == 1) {
                                if (request('cilindrod') != 0 || request('cilindrod2') != 0) {
                                    $total = $total + 590;
                                }
                            }

                            if ($hijo == null) {
                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'totalhistorial' => $total, 'total' => $total, 'totalreal' => $total
                                ]);
                            } else {
                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                    'totalhistorial' => $total, 'estatus' => 0, 'estatus_estadocontrato' => 0, 'total' => $total, 'totalreal' => $total
                                ]);
                                //Insertar en tabla registroestadocontrato
                                DB::table('registroestadocontrato')->insert([
                                    'id_contrato' => $idContrato,
                                    'estatuscontrato' => 0,
                                    'created_at' => Carbon::now()
                                ]);
                            }
                            DB::table('producto')->where([['id', '=', $arma], ['id_franquicia', '=', $idFranquicia]])->update([
                                'piezas' => $armapz
                            ]);

                            if (request('paquete') == 6) {
                                DB::table('historialclinico')->insert([
                                    'id' => $randomIdH, 'id_contrato' => $idContrato, 'edad' => request('edad'), 'diagnostico' => request('diagnostico'),
                                    'ocupacion' => request('ocupacion'), 'diabetes' => request('diabetes'), 'hipertension' => request('hipertension'), 'dolor' => $dolor,
                                    'ardor' => $ardor, 'golpeojos' => $golpe, 'otroM' => $otroM, 'ultimoexamen' => request('ultimoexamen'), 'molestiaotro' => request('molestia'),
                                    'esfericoder' => request('esfericod'), 'cilindroder' => request('cilindrod'), 'ejeder' => request('ejed'), 'addder' => request('addd'),
                                    'altder' => request('altd'), 'esfericoizq' => request('esfericod2'), 'cilindroizq' => request('cilindrod2'), 'ejeizq' => request('ejed2'),
                                    'addizq' => request('addd2'), 'altizq' => request('altd2'), 'altizq' => request('altd2'), 'id_producto' => request('producto'),
                                    'id_paquete' => request('paquete'), 'fechaentrega' => request('fechaentrega'), 'material' => request('material'),
                                    'materialotro' => request('motro'), 'bifocal' => request('bifocal'), 'fotocromatico' => $fotocromatico, 'ar' => $ar, 'tinte' => $tinte,
                                    'blueray' => $blueray, 'otroT' => $otroTra, 'tratamientootro' => request('otroT'), 'costomaterial' => request('costomaterial'),
                                    'costotratamiento' => request('costoT'), 'observaciones' => request('observaciones'), 'observacionesinterno' => request('observacionesinterno'),
                                    'created_at' => Carbon::now()
                                ]);
                                return redirect()->route('nuevohistorialclinico2', ['id' => $idFranquicia, 'idContrato' => $idContrato])
                                                 ->with('bien', 'El historial clinico y el contrato se crearon correctamente.');

                            } elseif (request('paquete') == 1) {
                                DB::table('historialclinico')->insert([
                                    'id' => $randomIdH, 'id_contrato' => $idContrato, 'edad' => request('edad'), 'diagnostico' => request('diagnostico'),
                                    'ocupacion' => request('ocupacion'), 'diabetes' => request('diabetes'), 'hipertension' => request('hipertension'), 'dolor' => $dolor,
                                    'ardor' => $ardor, 'golpeojos' => $golpe, 'otroM' => $otroM, 'ultimoexamen' => request('ultimoexamen'), 'molestiaotro' => request('molestia'),
                                    'esfericoder' => request('esfericod'), 'cilindroder' => request('cilindrod'), 'ejeder' => request('ejed'), 'esfericoizq' => request('esfericod2'),
                                    'cilindroizq' => request('cilindrod2'), 'ejeizq' => request('ejed2'), 'id_producto' => request('producto'), 'id_paquete' => request('paquete'),
                                    'fechaentrega' => request('fechaentrega'), 'material' => request('material'), 'materialotro' => request('motro'), 'bifocal' => request('bifocal'),
                                    'fotocromatico' => $fotocromatico, 'ar' => $ar, 'blueray' => $blueray, 'otroT' => $otroTra, 'tratamientootro' => request('otroT'),
                                    'costomaterial' => request('costomaterial'), 'costotratamiento' => request('costoT'), 'observaciones' => request('observaciones'),
                                    'observacionesinterno' => request('observacionesinterno'), 'created_at' => Carbon::now()
                                ]);
                                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                                 ->with('bien', 'El historial clinico y el contrato se crearon correctamente.');

                            } elseif (request('paquete') == 2) {
                                DB::table('historialclinico')->insert([
                                    'id' => $randomIdH, 'id_contrato' => $idContrato, 'edad' => request('edad'), 'diagnostico' => request('diagnostico'),
                                    'ocupacion' => request('ocupacion'), 'diabetes' => request('diabetes'), 'hipertension' => request('hipertension'),
                                    'dolor' => $dolor, 'ardor' => $ardor, 'golpeojos' => $golpe, 'otroM' => $otroM, 'ultimoexamen' => request('ultimoexamen'),
                                    'molestiaotro' => request('molestia'), 'id_producto' => request('producto'), 'id_paquete' => request('paquete'),
                                    'fechaentrega' => request('fechaentrega'), 'material' => request('material'), 'materialotro' => request('motro'), 'bifocal' => request('bifocal'),
                                    'fotocromatico' => 0, 'ar' => $ar, 'blueray' => $blueray, 'otroT' => $otroTra, 'tratamientootro' => request('otroT'),
                                    'costomaterial' => request('costomaterial'), 'costotratamiento' => request('costoT'), 'observaciones' => request('observaciones'),
                                    'observacionesinterno' => request('observacionesinterno'), 'created_at' => Carbon::now()
                                ]);
                                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                                 ->with('bien', 'El historial clinico y el contrato se crearon correctamente.');

                            } elseif (request('paquete') == 3) {
                                DB::table('historialclinico')->insert([
                                    'id' => $randomIdH, 'id_contrato' => $idContrato, 'edad' => request('edad'), 'diagnostico' => request('diagnostico'),
                                    'ocupacion' => request('ocupacion'), 'diabetes' => request('diabetes'), 'hipertension' => request('hipertension'),
                                    'dolor' => $dolor, 'ardor' => $ardor, 'golpeojos' => $golpe, 'otroM' => $otroM, 'ultimoexamen' => request('ultimoexamen'),
                                    'molestiaotro' => request('molestia'), 'esfericoder' => request('esfericod'), 'cilindroder' => request('cilindrod'),
                                    'ejeder' => request('ejed'), 'esfericoizq' => request('esfericod2'), 'cilindroizq' => request('cilindrod2'), 'ejeizq' => request('ejed2'),
                                    'id_producto' => request('producto'), 'id_paquete' => request('paquete'), 'fechaentrega' => request('fechaentrega'),
                                    'material' => request('material'), 'materialotro' => request('motro'), 'bifocal' => request('bifocal'),
                                    'fotocromatico' => $fotocromatico, 'ar' => $ar, 'tinte' => $tinte, 'blueray' => $blueray, 'otroT' => $otroTra,
                                    'tratamientootro' => request('otroT'), 'costomaterial' => request('costomaterial'), 'costotratamiento' => request('costoT'),
                                    'observaciones' => request('observaciones'), 'observacionesinterno' => request('observacionesinterno'), 'created_at' => Carbon::now()
                                ]);
                                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                                 ->with('bien', 'El historial clinico y el contrato se crearon correctamente.');

                            } elseif (request('paquete') == 4) {
                                DB::table('historialclinico')->insert([
                                    'id' => $randomIdH, 'id_contrato' => $idContrato, 'edad' => request('edad'), 'diagnostico' => request('diagnostico'),
                                    'ocupacion' => request('ocupacion'), 'diabetes' => request('diabetes'), 'hipertension' => request('hipertension'),
                                    'dolor' => $dolor, 'ardor' => $ardor, 'golpeojos' => $golpe, 'otroM' => $otroM, 'ultimoexamen' => request('ultimoexamen'),
                                    'molestiaotro' => request('molestia'), 'esfericoder' => request('esfericod'), 'cilindroder' => request('cilindrod'),
                                    'ejeder' => request('ejed'), 'esfericoizq' => request('esfericod2'), 'cilindroizq' => request('cilindrod2'), 'ejeizq' => request('ejed2'),
                                    'id_producto' => request('producto'), 'id_paquete' => request('paquete'), 'fechaentrega' => request('fechaentrega'),
                                    'material' => request('material'), 'materialotro' => request('motro'), 'bifocal' => request('bifocal'),
                                    'fotocromatico' => $fotocromatico, 'ar' => $ar, 'tinte' => $tinte, 'blueray' => $blueray, 'otroT' => $otroTra,
                                    'tratamientootro' => request('otroT'), 'costomaterial' => request('costomaterial'), 'costotratamiento' => request('costoT'),
                                    'observaciones' => request('observaciones'), 'observacionesinterno' => request('observacionesinterno'), 'created_at' => Carbon::now()
                                ]);
                                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                                 ->with('bien', 'El historial clinico y el contrato se crearon correctamente.');

                            } elseif (request('paquete') == 5) {
                                DB::table('historialclinico')->insert([
                                    'id' => $randomIdH, 'id_contrato' => $idContrato, 'edad' => request('edad'), 'diagnostico' => request('diagnostico'),
                                    'ocupacion' => request('ocupacion'), 'diabetes' => request('diabetes'), 'hipertension' => request('hipertension'),
                                    'dolor' => $dolor, 'ardor' => $ardor, 'golpeojos' => $golpe, 'otroM' => $otroM, 'ultimoexamen' => request('ultimoexamen'),
                                    'molestiaotro' => request('molestia'), 'esfericoder' => request('esfericod'), 'cilindroder' => request('cilindrod'),
                                    'ejeder' => request('ejed'), 'addder' => request('addd'), 'altder' => request('altd'), 'esfericoizq' => request('esfericod2'),
                                    'cilindroizq' => request('cilindrod2'), 'ejeizq' => request('ejed2'), 'addizq' => request('addd2'), 'altizq' => request('altd2'),
                                    'altizq' => request('altd2'), 'id_producto' => request('producto'), 'id_paquete' => request('paquete'), 'fechaentrega' => request('fechaentrega'),
                                    'material' => request('material'), 'materialotro' => request('motro'), 'bifocal' => request('bifocal'), 'fotocromatico' => $fotocromatico,
                                    'ar' => $ar, 'tinte' => $tinte, 'blueray' => $blueray, 'otroT' => $otroTra, 'tratamientootro' => request('otroT'),
                                    'costomaterial' => request('costomaterial'), 'costotratamiento' => request('costoT'), 'observaciones' => request('observaciones'),
                                    'observacionesinterno' => request('observacionesinterno'), 'created_at' => Carbon::now()
                                ]);
                                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                                 ->with('bien', 'El historial clinico y el contrato se crearon correctamente.');

                            } elseif (request('paquete') == 7) {
                                DB::table('historialclinico')->insert([
                                    'id' => $randomIdH, 'id_contrato' => $idContrato, 'edad' => request('edad'), 'diagnostico' => request('diagnostico'),
                                    'ocupacion' => request('ocupacion'), 'diabetes' => request('diabetes'), 'hipertension' => request('hipertension'),
                                    'dolor' => $dolor, 'ardor' => $ardor, 'golpeojos' => $golpe, 'otroM' => $otroM, 'ultimoexamen' => request('ultimoexamen'),
                                    'molestiaotro' => request('molestia'), 'esfericoder' => request('esfericod'), 'cilindroder' => request('cilindrod'),
                                    'ejeder' => request('ejed'), 'esfericoizq' => request('esfericod2'), 'cilindroizq' => request('cilindrod2'), 'ejeizq' => request('ejed2'),
                                    'id_producto' => request('producto'), 'id_paquete' => request('paquete'), 'fechaentrega' => request('fechaentrega'),
                                    'material' => request('material'), 'materialotro' => request('motro'), 'bifocal' => request('bifocal'), 'fotocromatico' => $fotocromatico,
                                    'ar' => $ar, 'tinte' => $tinte, 'blueray' => $blueray, 'otroT' => $otroTra, 'tratamientootro' => request('otroT'),
                                    'costomaterial' => request('costomaterial'), 'costotratamiento' => request('costoT'), 'observaciones' => request('observaciones'),
                                    'observacionesinterno' => request('observacionesinterno'), 'created_at' => Carbon::now()
                                ]);

                                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato, 'idcontratopadre' => $idcontratopadre])
                                                 ->with('bien', 'El historial clinico y el contrato se crearon correctamente.');
                            }
                        } catch (\Exception $e) {
                            \Log::info("Error: " . $e);
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
            }
// Sino detecta un contrato padre entonces sera un contrato nuevo sin dependencias
            //PRIMER CONTRATO
            //  return;
            try{
                $rules = [
                    'zona' => 'required',
                    'nombre' => 'required|string|max:255',
                    'optometrista' => 'required|integer',
                    'calle' => 'required|string|max:255',
                    'numero' => 'required|string|min:1|max:255',
                    'departamento' => 'required|string|max:255',
                    'alladode' => 'required|string|max:255',
                    'frentea' => 'required|string|max:255',
                    'entrecalles' => 'required|string|max:255',
                    'colonia' => 'required|string|max:255',
                    'localidad' => 'required|string|max:255',
                    'telefono' => 'required|string|size:10|regex:/[0-9]/',
                    'tr' => 'required|string|size:10|regex:/[0-9]/',
                    'casatipo' => 'required|string|max:255',
                    'nr' => 'required|string|max:255',
                    'casacolor' => 'required|string|max:255',
                    'fotoine' => 'required|image',
                    'fotoineatras' => 'required|image',
                    'fotocasa' => 'required|image',
                    'pagare' => 'required|image',
                    'comprobantedomicilio' => 'required|image',
                    'edad' => 'required|string|max:255',
                    'diagnostico' => 'required|string|max:255',
                    'ocupacion' => 'required|string|max:255',
                    'diabetes' => 'required|string|max:255',
                    'hipertension' => 'required|string|max:255',
                    'paquete' => 'required|integer',
                    'producto' => 'required|string|max:255',
                    'ultimoexamen' => 'nullable|date',
                    'correo' => 'nullable|email',
                    'fechaentrega' => 'required|date'
                ];

                if (request('zona') == 'Seleccionar') {
                    return back()->withErrors(['zona' => 'Elige una zona, campo obligatorio.'])->withInput($request->all());
                }
                if (request('optometrista') == 'Seleccionar') {
                    return back()->withErrors(['optometrista' => 'Elige un optometrista, campo obligatorio.'])->withInput($request->all());
                }
                if (request('material') != '3' && request('motro') != null && request('costomaterial') != null) {
                    return back()->withErrors(['motro' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
                }
                if (request('material') == '3' && request('motro') == null && request('costomaterial') == null) {
                    return back()->withErrors(['motro' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
                }
                if (request('material') == '3' && request('motro') == null && request('costomaterial') != null) {
                    return back()->withErrors(['motro' => 'Llenar ambos campos para otro.'])->withInput($request->all());
                }
                if (request('material') == '3' && request('motro') != null && request('costomaterial') == null) {
                    return back()->withErrors(['motro' => 'Llenar ambos campos para otro.'])->withInput($request->all());
                }
                if (request('molestia') != null && request('otroM') == null) {
                    return back()->withErrors(['molestia' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
                }
                if (request('otroTra') != null && request('otroT') == null && request('costoT') == null) {
                    return back()->withErrors(['otroT' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
                }
                if (request('otroTra') != null && request('otroT') != null && request('costoT') == null) {
                    return back()->withErrors(['otroT' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
                }
                if (request('otroTra') != null && request('otroT') == null && request('costoT') != null) {
                    return back()->withErrors(['otroT' => 'Llenar ambos campos para otro tratamiento.'])->withInput($request->all());
                }
                if (request('otroTra') == null && request('otroT') != null && request('costoT') != null) {
                    return back()->withErrors(['otroT' => 'Llenar ambos campos para otro tratamiento.'])->withInput($request->all());
                }
                if (request('otroTra') == null && request('otroT') != null && request('costoT') == null) {
                    return back()->withErrors(['otroT' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
                }
                if (request('otroTra') == null && request('otroT') == null && request('costoT') != null) {
                    return back()->withErrors(['costoT' => 'Llenar ambos campos para otro tratamiento.'])->withInput($request->all());
                }
                if (request('dolor') == null && request('ardor') == null && request('golpe') == null && request('molestia') == null && request('otroM') == null) {
                    return back()->withErrors(['dolor' => 'Elegir al menos una molestia.'])->withInput($request->all());
                }
                if (request('fotocromatico') == null && request('ar') == null && request('tinte') == null && request('blueray') == null && request('otroTra') == null) {
                    return back()->withErrors(['fotocromatico' => 'Elegir al menos una molestia.'])->withInput($request->all());
                }
                if (request('paquete') == 0) {
                    return back()->withErrors(['paquete' => 'Campo obligatorio.'])->withInput($request->all());
                }
                if (request('producto') == 'nada') {
                    return back()->withErrors(['producto' => 'Campo obligatorio.'])->withInput($request->all());
                }
                if (request('tinte') == 1 && request('paquete') == 1) {
                    return back()->withErrors(['tinte' => 'No se permite agregar tinte con este paquete.'])->withInput($request->all());
                }
                if (request('tinte') == 1 && request('paquete') == 2) {
                    return back()->withErrors(['tinte' => 'No se permite agregar tinte con este paquete.'])->withInput($request->all());
                }
                if (request('telefono') == null && request('correo') == null) {
                    return back()->withErrors(['correo' => 'Ingresar algun correo o el telefono del cliente.'])->withInput($request->all());
                }
                if (request('ar') != null && request('blueray') != null) {
                    return back()->withErrors(['ar' => 'Solo se puede elegir uno entre AR y BlueRay.'])->withInput($request->all());
                }
                if (request('paquete') == 1) {
                    if (request('fotocromatico') != null && request('ar') != null || request('blueray') != null || request('otroTra') != null) {
                        $n = 0;
                        if (request('fotocromatico') == 1) {

                            $n = $n + 1;
                        }
                        if (request('ar') == 1) {

                            $n = $n + 1;
                        }
                        if (request('blueray') == 1) {

                            $n = $n + 1;
                        }
                        if (request('otroTra') == 1) {
                            $n = $n + 1;
                        }

                        if ($n > 1) {

                            return back()->withErrors(['fotocromatico' => 'Solo se permite uno con el paquete de lectura.'])->withInput($request->all());
                        }
                    }
                }
                request()->validate($rules);


                if (request('paquete') == 1) {
                    $validacion = Validator::make($request->all(), [
                        'esfericod' => 'required|string',
                        'cilindrod' => 'required|string',
                        'ejed' => 'required|string',
                        'altd' => 'required|string',
                        'esfericod2' => 'required|string',
                        'cilindrod2' => 'required|string',
                        'ejed2' => 'required|string',
                        'altd2' => 'required|string',

                    ]);
                    if ($validacion->fails()) {
                        return back()->withErrors([
                            'esfericod' => 'Campo requerido para LECTURA',
                            'cilindrod' => 'Campo requerido para LECTURA',
                            'ejed' => 'Campo requerido para LECTURA',
                            'altd' => 'Campo requerido para LECTURA',
                            'esfericod2' => 'Campo requerido para LECTURA',
                            'cilindrod2' => 'Campo requerido para LECTURA',
                            'ejed2' => 'Campo requerido para LECTURA',
                            'altd2' => 'Campo requerido para LECTURA',
                        ])->withInput($request->all());
                    }


                }
                if (request('paquete') == 3) {
                    $validacion = Validator::make($request->all(), [
                        'esfericod' => 'required|string',
                        'cilindrod' => 'required|string',
                        'ejed' => 'required|string',
                        'esfericod2' => 'required|string',
                        'cilindrod2' => 'required|string',
                        'ejed2' => 'required|string',

                    ]);
                    if ($validacion->fails()) {
                        return back()->withErrors([
                            'esfericod' => 'Campo requerido para ECO JR',
                            'cilindrod' => 'Campo requerido para ECO JR',
                            'ejed' => 'Campo requerido para ECO JR',
                            'esfericod2' => 'Campo requerido para ECO JR',
                            'cilindrod2' => 'Campo requerido para ECO JR',
                            'ejed2' => 'Campo requerido para ECO JR',
                        ])->withInput($request->all());
                    }
                }

                if (request('paquete') == 4) {
                    $validacion = Validator::make($request->all(), [
                        'esfericod' => 'required|string',
                        'cilindrod' => 'required|string',
                        'ejed' => 'required|string',
                        'esfericod2' => 'required|string',
                        'cilindrod2' => 'required|string',
                        'ejed2' => 'required|string',


                    ]);
                    if ($validacion->fails()) {
                        return back()->withErrors([
                            'esfericod' => 'Campo requerido para JR',
                            'cilindrod' => 'Campo requerido para JR',
                            'ejed' => 'Campo requerido para JR',
                            'esfericod2' => 'Campo requerido para JR',
                            'cilindrod2' => 'Campo requerido para JR',
                            'ejed2' => 'Campo requerido para JR',
                        ])->withInput($request->all());
                    }
                }
                if (request('paquete') == 5) {
                    $validacion = Validator::make($request->all(), [
                        'esfericod' => 'required|string',
                        'cilindrod' => 'required|string',
                        'ejed' => 'required|string',
                        'addd' => 'required|string',
                        'altd' => 'required|string',
                        'esfericod2' => 'required|string',
                        'cilindrod2' => 'required|string',
                        'ejed2' => 'required|string',
                        'addd2' => 'required|string',
                        'altd2' => 'required|string',

                    ]);
                    if ($validacion->fails()) {
                        return back()->withErrors([
                            'esfericod' => 'Campo requerido para DORADO 1',
                            'cilindrod' => 'Campo requerido para DORADO 1',
                            'ejed' => 'Campo requerido para DORADO 1',
                            'addd' => 'required|string',
                            'altd' => 'Campo requerido para DORADO 1',
                            'esfericod2' => 'Campo requerido para DORADO 1',
                            'cilindrod2' => 'Campo requerido para DORADO 1',
                            'ejed2' => 'Campo requerido para DORADO 1',
                            'addd2' => 'required|string',
                            'altd2' => 'Campo requerido para DORADO 1',
                        ])->withInput($request->all());
                    }
                }
                if (request('paquete') == 6) {
                    $validacion = Validator::make($request->all(), [
                        'esfericod' => 'required|string',
                        'cilindrod' => 'required|string',
                        'ejed' => 'required|string',
                        'esfericod2' => 'required|string',
                        'cilindrod2' => 'required|string',
                        'ejed2' => 'required|string',

                    ]);
                    if ($validacion->fails()) {
                        return back()->withErrors([
                            'esfericod' => 'Campo requerido para DORADO2',
                            'cilindrod' => 'Campo requerido para DORADO2',
                            'ejed' => 'Campo requerido para DORADO2',
                            'esfericod2' => 'Campo requerido para DORADO2',
                            'cilindrod2' => 'Campo requerido para DORADO2',
                            'ejed2' => 'Campo requerido para DORADO2',

                        ])->withInput($request->all());
                    }
                }
                if (request('paquete') == 7) {
                    $validacion = Validator::make($request->all(), [
                        'esfericod' => 'required|string',
                        'cilindrod' => 'required|string',
                        'ejed' => 'required|string',
                        'addd' => 'required|string',
                        'altd' => 'required|string',
                        'esfericod2' => 'required|string',
                        'cilindrod2' => 'required|string',
                        'ejed2' => 'required|string',
                        'addd2' => 'required|string',
                        'altd2' => 'required|string',

                    ]);
                    if ($validacion->fails()) {
                        return back()->withErrors([
                            'esfericod' => 'Campo requerido para PLATINO',
                            'cilindrod' => 'Campo requerido para PLATINO',
                            'ejed' => 'Campo requerido para PLATINO',
                            'addd' => 'Campo requerido para PLATINO',
                            'altd' => 'Campo requerido para PLATINO',
                            'esfericod2' => 'Campo requerido para PLATINO',
                            'cilindrod2' => 'Campo requerido para PLATINO',
                            'ejed2' => 'Campo requerido para PLATINO',
                            'addd2' => 'Campo requerido para PLATINO',
                            'altd2' => 'Campo requerido para PLATINO',
                        ])->withInput($request->all());
                    }


                }

                // contrato
                $contratos = DB::select("SHOW TABLE STATUS LIKE 'contratos'");
                $randomId = $this->getContratoId();
                $randomIdH = $this->getHistorialId5();

                $fotoBruta = 'Foto-Ine-Frente-Contrato-' . $randomId . '-' . time() . '.' . request()->file('fotoine')->getClientOriginalExtension();
                $fotoine = request()->file('fotoine')->storeAs('uploads/imagenes/contratos/fotoine', $fotoBruta, 'disco');
                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotoine/' . $fotoBruta)->height();
                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotoine/' . $fotoBruta)->width();

                if ($alto > $ancho) {
                    $imagenfotoine = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotoine/' . $fotoBruta)->resize(600, 800);
                } else {
                    $imagenfotoine = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotoine/' . $fotoBruta)->resize(800, 600);
                }
                $imagenfotoine->save();

                $fotoBruta2 = 'Foto-Ine-Atras-Contrato-' . $randomId . '-' . time() . '.' . request()->file('fotoineatras')->getClientOriginalExtension();
                $fotoineatras = request()->file('fotoineatras')->storeAs('uploads/imagenes/contratos/fotoineatras', $fotoBruta2, 'disco');
                $alto2 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotoineatras/' . $fotoBruta2)->height();
                $ancho2 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotoineatras/' . $fotoBruta2)->width();
                if ($alto2 > $ancho2) {
                    $imagenfotoineatras = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotoineatras/' . $fotoBruta2)->resize(600, 800);
                } else {
                    $imagenfotoineatras = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotoineatras/' . $fotoBruta2)->resize(800, 600);
                }
                $imagenfotoineatras->save();

                $fotoBruta5 = 'Foto-Pagare-Contrato-' . $randomId . '-' . time() . '.' . request()->file('pagare')->getClientOriginalExtension();
                $fotopagare = request()->file('pagare')->storeAs('uploads/imagenes/contratos/pagare', $fotoBruta5, 'disco');
                $alto5 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/pagare/' . $fotoBruta5)->height();
                $ancho5 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/pagare/' . $fotoBruta5)->width();
                if ($alto5 > $ancho5) {
                    $imagenfotopagare = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/pagare/' . $fotoBruta5)->resize(600, 800);
                } else {
                    $imagenfotopagare = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/pagare/' . $fotoBruta5)->resize(800, 600);
                }
                $imagenfotopagare->save();

                $fotoBruta3 = 'Foto-Casa-Contrato-' . $randomId . '-' . time() . '.' . request()->file('fotocasa')->getClientOriginalExtension();
                $fotocasa = request()->file('fotocasa')->storeAs('uploads/imagenes/contratos/fotocasa', $fotoBruta3, 'disco');
                $alto3 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotocasa/' . $fotoBruta3)->height();
                $ancho3 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotocasa/' . $fotoBruta3)->width();
                if ($alto3 > $ancho3) {
                    $imagenfotocasa = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotocasa/' . $fotoBruta3)->resize(600, 800);
                } else {
                    $imagenfotocasa = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/fotocasa/' . $fotoBruta3)->resize(800, 600);
                }
                $imagenfotocasa->save();

                $fotoBruta4 = 'Foto-comprobantedomicilio-Contrato-' . $randomId . '-' . time() . '.' . request()->file('comprobantedomicilio')->getClientOriginalExtension();
                $comprobantedomicilio = request()->file('comprobantedomicilio')->storeAs('uploads/imagenes/contratos/comprobantedomicilio', $fotoBruta4, 'disco');
                $alto4 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/comprobantedomicilio/' . $fotoBruta4)->height();
                $ancho4 = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/comprobantedomicilio/' . $fotoBruta4)->width();
                if ($alto4 > $ancho4) {
                    $imagencomprobantedomicilio = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/comprobantedomicilio/' . $fotoBruta4)->resize(600, 800);
                } else {
                    $imagencomprobantedomicilio = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/contratos/comprobantedomicilio/' . $fotoBruta4)->resize(800, 600);
                }
                $imagencomprobantedomicilio->save();

                $datos = 1;
                $creacion = Carbon::now();
                $usuarioId = Auth::user()->id;
                $usuarioNombre = Auth::user()->name;

                DB::table('contratos')->insert([
                    'id' => $randomId, 'datos' => $datos, 'id_franquicia' => $idFranquicia, 'id_usuariocreacion' => $usuarioId, 'nombre_usuariocreacion' => $usuarioNombre,
                    'id_zona' => request('zona'), 'id_promocion' => request('promocion'), 'id_optometrista' => request('optometrista'),
                    'nombre' => request('nombre'), 'pago' => request('formapago'), 'calle' => request('calle'), 'numero' => request('numero'),
                    'depto' => request('departamento'), 'alladode' => request('alladode'), 'frentea' => request('frentea'), 'entrecalles' => request('entrecalles'),
                    'colonia' => request('colonia'), 'localidad' => request('localidad'), 'telefono' => request('telefono'), 'casatipo' => request('casatipo'),
                    'casacolor' => request('casacolor'), 'created_at' => $creacion, 'correo' => request('correo'), 'nombrereferencia' => request('nr'),
                    'telefonoreferencia' => request('tr'), 'fotoine' => $fotoine, 'fotocasa' => $fotocasa, 'comprobantedomicilio' => $comprobantedomicilio,
                    'fotoineatras' => $fotoineatras, 'pagare' => $fotopagare, 'contador' => 1, 'poliza' => null
                ]);

                //historial clinico
                $dolor = request('dolor') != Null;
                $ardor = request('ardor') != Null;
                $golpe = request('golpe') != Null;
                $otroM = request('otroM') != Null;
                $otroTra = request('otroTra') != Null;
                $fotocromatico2 = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'fotocromático'");
                $AR2 = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'A/R'");
                $tinte2 = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'tinte'");
                $blueray2 = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'BlueRay'");
                $fotocromatico = request('fotocromatico');
                $ar = request('ar');
                $tinte = request('tinte');
                $blueray = request('blueray');
                $otromaterial = request('costomaterial');
                $otrotratamiento = request('costoT');
                if ($tinte != null) {
                    $tinte = 1;
                    $tinte6 = $tinte2[0]->precio;
                    $fotocromatico = null;
                    $ar = null;
                    $blueray = null;
                } else {
                    $tinte6 = null;
                    $tinte = 0;
                }
                $fotocromaticoActivo = false;
                if ($fotocromatico != null) {
                    $fotocromatico = 1;
                    $fotocromatico6 = $fotocromatico2[0]->precio;
                    $fotocromaticoActivo = true;
                } else {
                    $fotocromatico = null;
                    $fotocromatico6 = 0;
                }
                if ($ar != null) {
                    $ar = 1;
                    $ar6 = $AR2[0]->precio;
                } else {
                    $ar = null;
                    $ar6 = 0;
                }
                if ($blueray != null) {
                    $blueray = 1;
                    $blueray6 = $blueray2[0]->precio;
                    if (request('paquete') == 2) {
                        if (!$fotocromaticoActivo) {
                            $blueray6 = 0;
                        }
                    }
                } else {
                    $blueray = null;
                    $blueray6 = 0;
                }

                $totalidad = "";
                if ($tinte != null && request('paquete') > 2) {
                    $totalidad = $tinte6 + $otromaterial + $otrotratamiento;
                } else {
                    if (request('paquete') == 2) {
                        $totalidad = $ar6 + $blueray6 + $otromaterial + $otrotratamiento;
                    } else {
                        $totalidad = $fotocromatico6 + $ar6 + $blueray6 + $otromaterial + $otrotratamiento;
                    }
                }

                $historial = DB::select("SHOW TABLE STATUS LIKE 'historialclinico'");
                $siguienteId = $historial[0]->Auto_increment;
                $idContrato = $randomId;
                $value = request('paquete');
                $value2 = request('producto');
                $paquetes = DB::select("SELECT * FROM paquetes WHERE id_franquicia = '$idFranquicia' and id = '$value'");
                $con = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' and id = '$idContrato'");
                $armazon = DB::select("SELECT * FROM producto WHERE id = '$value2' AND id_tipoproducto = '1'");
                $arma = $armazon[0]->id;
                $armapz = $armazon[0]->piezas - 1;
                $hijo = $con[0]->idcontratorelacion;
                $val = $con[0]->totalhistorial;
                $valor = $paquetes[0]->precio;
                $total = $valor + $val + $totalidad;
                if ($value == 1) {
                    if (request('cilindrod') != 0 || request('cilindrod2') != 0) {
                        $total = $total + 590;
                    }
                }
                if ($hijo == null) {
                    DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                        'totalhistorial' => $total, 'total' => $total, 'estatus_estadocontrato' => 0, 'totalreal' => $total
                    ]);
                } else {
                    DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                        'totalhistorial' => $total, 'total' => $total, 'estatus' => 0, 'estatus_estadocontrato' => 0, 'totalreal' => $total
                    ]);
                }

                //Insertar en tabla registroestadocontrato
                DB::table('registroestadocontrato')->insert([
                    'id_contrato' => $idContrato,
                    'estatuscontrato' => 0,
                    'created_at' => Carbon::now()
                ]);

                DB::table('producto')->where([['id', '=', $arma], ['id_franquicia', '=', $idFranquicia]])->update([
                    'piezas' => $armapz
                ]);

                if (request('paquete') == 6) {
                    DB::table('historialclinico')->insert([
                        'id' => $randomIdH, 'id_contrato' => $idContrato, 'edad' => request('edad'), 'diagnostico' => request('diagnostico'), 'ocupacion' => request('ocupacion'),
                        'diabetes' => request('diabetes'), 'hipertension' => request('hipertension'), 'dolor' => $dolor, 'ardor' => $ardor, 'golpeojos' => $golpe, 'otroM' => $otroM,
                        'ultimoexamen' => request('ultimoexamen'), 'molestiaotro' => request('molestia'), 'esfericoder' => request('esfericod'),
                        'cilindroder' => request('cilindrod'), 'ejeder' => request('ejed'), 'addder' => request('addd'), 'altder' => request('altd'),
                        'esfericoizq' => request('esfericod2'), 'cilindroizq' => request('cilindrod2'), 'ejeizq' => request('ejed2'), 'addizq' => request('addd2'),
                        'altizq' => request('altd2'), 'altizq' => request('altd2'), 'id_producto' => request('producto'), 'id_paquete' => request('paquete'),
                        'fechaentrega' => request('fechaentrega'), 'material' => request('material'), 'materialotro' => request('motro'), 'bifocal' => request('bifocal'),
                        'fotocromatico' => $fotocromatico, 'ar' => $ar, 'tinte' => $tinte, 'blueray' => $blueray, 'otroT' => $otroTra, 'tratamientootro' => request('otroT'),
                        'costomaterial' => request('costomaterial'), 'costotratamiento' => request('costoT'), 'observaciones' => request('observaciones'),
                        'observacionesinterno' => request('observacionesinterno'), 'created_at' => Carbon::now()
                    ]);
                    return redirect()->route('nuevohistorialclinico2', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                     ->with('bien', 'El historial clinico y el contrato se crearon correctamente.');
                } elseif (request('paquete') == 1) {
                    DB::table('historialclinico')->insert([
                        'id' => $randomIdH, 'id_contrato' => $idContrato, 'edad' => request('edad'), 'diagnostico' => request('diagnostico'), 'ocupacion' => request('ocupacion'),
                        'diabetes' => request('diabetes'), 'hipertension' => request('hipertension'), 'dolor' => $dolor, 'ardor' => $ardor, 'golpeojos' => $golpe, 'otroM' => $otroM,
                        'ultimoexamen' => request('ultimoexamen'), 'molestiaotro' => request('molestia'), 'esfericoder' => request('esfericod'),
                        'cilindroder' => request('cilindrod'), 'ejeder' => request('ejed'), 'esfericoizq' => request('esfericod2'), 'cilindroizq' => request('cilindrod2'),
                        'ejeizq' => request('ejed2'), 'id_producto' => request('producto'), 'id_paquete' => request('paquete'), 'fechaentrega' => request('fechaentrega'),
                        'material' => request('material'), 'materialotro' => request('motro'), 'bifocal' => request('bifocal'), 'fotocromatico' => $fotocromatico, 'ar' => $ar,
                        'blueray' => $blueray, 'otroT' => $otroTra, 'tratamientootro' => request('otroT'), 'costomaterial' => request('costomaterial'),
                        'costotratamiento' => request('costoT'), 'observaciones' => request('observaciones'), 'observacionesinterno' => request('observacionesinterno'),
                        'created_at' => Carbon::now()
                    ]);
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                     ->with('bien', 'El historial clinico y el contrato se crearon correctamente.');

                } elseif (request('paquete') == 2) {
                    DB::table('historialclinico')->insert([
                        'id' => $randomIdH, 'id_contrato' => $idContrato, 'edad' => request('edad'), 'diagnostico' => request('diagnostico'), 'ocupacion' => request('ocupacion'),
                        'diabetes' => request('diabetes'), 'hipertension' => request('hipertension'), 'dolor' => $dolor, 'ardor' => $ardor, 'golpeojos' => $golpe, 'otroM' => $otroM,
                        'ultimoexamen' => request('ultimoexamen'), 'molestiaotro' => request('molestia'), 'id_producto' => request('producto'), 'id_paquete' => request('paquete'),
                        'fechaentrega' => request('fechaentrega'), 'material' => request('material'), 'materialotro' => request('motro'), 'bifocal' => request('bifocal'),
                        'fotocromatico' => $fotocromatico, 'ar' => $ar, 'blueray' => $blueray, 'otroT' => $otroTra, 'tratamientootro' => request('otroT'),
                        'costomaterial' => request('costomaterial'), 'costotratamiento' => request('costoT'), 'observaciones' => request('observaciones'),
                        'observacionesinterno' => request('observacionesinterno'), 'created_at' => Carbon::now()
                    ]);
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                     ->with('bien', 'El historial clinico y el contrato se crearon correctamente.');

                } elseif (request('paquete') == 3) {
                    DB::table('historialclinico')->insert([
                        'id' => $randomIdH, 'id_contrato' => $idContrato, 'edad' => request('edad'), 'diagnostico' => request('diagnostico'), 'ocupacion' => request('ocupacion'),
                        'diabetes' => request('diabetes'), 'hipertension' => request('hipertension'), 'dolor' => $dolor, 'ardor' => $ardor, 'golpeojos' => $golpe, 'otroM' => $otroM,
                        'ultimoexamen' => request('ultimoexamen'), 'molestiaotro' => request('molestia'), 'esfericoder' => request('esfericod'),
                        'cilindroder' => request('cilindrod'), 'ejeder' => request('ejed'), 'esfericoizq' => request('esfericod2'), 'cilindroizq' => request('cilindrod2'),
                        'ejeizq' => request('ejed2'), 'id_producto' => request('producto'), 'id_paquete' => request('paquete'), 'fechaentrega' => request('fechaentrega'),
                        'material' => request('material'), 'materialotro' => request('motro'), 'bifocal' => request('bifocal'), 'fotocromatico' => $fotocromatico, 'ar' => $ar,
                        'tinte' => $tinte, 'blueray' => $blueray, 'otroT' => $otroTra, 'tratamientootro' => request('otroT'), 'costomaterial' => request('costomaterial'),
                        'costotratamiento' => request('costoT'), 'observaciones' => request('observaciones'), 'observacionesinterno' => request('observacionesinterno'),
                        'created_at' => Carbon::now()
                    ]);
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                     ->with('bien', 'El historial clinico y el contrato se crearon correctamente.');

                } elseif (request('paquete') == 4) {
                    DB::table('historialclinico')->insert([
                        'id' => $randomIdH, 'id_contrato' => $idContrato, 'edad' => request('edad'), 'diagnostico' => request('diagnostico'), 'ocupacion' => request('ocupacion'),
                        'diabetes' => request('diabetes'), 'hipertension' => request('hipertension'), 'dolor' => $dolor, 'ardor' => $ardor, 'golpeojos' => $golpe, 'otroM' => $otroM,
                        'ultimoexamen' => request('ultimoexamen'), 'molestiaotro' => request('molestia'), 'esfericoder' => request('esfericod'),
                        'cilindroder' => request('cilindrod'), 'ejeder' => request('ejed'), 'esfericoizq' => request('esfericod2'), 'cilindroizq' => request('cilindrod2'),
                        'ejeizq' => request('ejed2'), 'id_producto' => request('producto'), 'id_paquete' => request('paquete'), 'fechaentrega' => request('fechaentrega'),
                        'material' => request('material'), 'materialotro' => request('motro'), 'bifocal' => request('bifocal'), 'fotocromatico' => $fotocromatico, 'ar' => $ar,
                        'tinte' => $tinte, 'blueray' => $blueray, 'otroT' => $otroTra, 'tratamientootro' => request('otroT'), 'costomaterial' => request('costomaterial'),
                        'costotratamiento' => request('costoT'), 'observaciones' => request('observaciones'), 'observacionesinterno' => request('observacionesinterno'),
                        'created_at' => Carbon::now()
                    ]);
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                     ->with('bien', 'El historial clinico y el contrato se crearon correctamente.');

                } elseif (request('paquete') == 5) {
                    DB::table('historialclinico')->insert([
                        'id' => $randomIdH, 'id_contrato' => $idContrato, 'edad' => request('edad'), 'diagnostico' => request('diagnostico'), 'ocupacion' => request('ocupacion'),
                        'diabetes' => request('diabetes'), 'hipertension' => request('hipertension'), 'dolor' => $dolor, 'ardor' => $ardor, 'golpeojos' => $golpe, 'otroM' => $otroM,
                        'ultimoexamen' => request('ultimoexamen'), 'molestiaotro' => request('molestia'), 'esfericoder' => request('esfericod'),
                        'cilindroder' => request('cilindrod'), 'ejeder' => request('ejed'), 'addder' => request('addd'), 'altder' => request('altd'),
                        'esfericoizq' => request('esfericod2'), 'cilindroizq' => request('cilindrod2'), 'ejeizq' => request('ejed2'), 'addizq' => request('addd2'),
                        'altizq' => request('altd2'), 'altizq' => request('altd2'), 'id_producto' => request('producto'), 'id_paquete' => request('paquete'),
                        'fechaentrega' => request('fechaentrega'), 'material' => request('material'), 'materialotro' => request('motro'), 'bifocal' => request('bifocal'),
                        'fotocromatico' => $fotocromatico, 'ar' => $ar, 'tinte' => $tinte, 'blueray' => $blueray, 'otroT' => $otroTra, 'tratamientootro' => request('otroT'),
                        'costomaterial' => request('costomaterial'), 'costotratamiento' => request('costoT'), 'observaciones' => request('observaciones'),
                        'observacionesinterno' => request('observacionesinterno'), 'created_at' => Carbon::now()
                    ]);
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                     ->with('bien', 'El historial clinico y el contrato se crearon correctamente.');

                } elseif (request('paquete') == 7) {
                    DB::table('historialclinico')->insert([
                        'id' => $randomIdH, 'id_contrato' => $idContrato, 'edad' => request('edad'), 'diagnostico' => request('diagnostico'), 'ocupacion' => request('ocupacion'),
                        'diabetes' => request('diabetes'), 'hipertension' => request('hipertension'), 'dolor' => $dolor, 'ardor' => $ardor, 'golpeojos' => $golpe, 'otroM' => $otroM,
                        'ultimoexamen' => request('ultimoexamen'), 'molestiaotro' => request('molestia'), 'esfericoder' => request('esfericod'),
                        'cilindroder' => request('cilindrod'), 'ejeder' => request('ejed'), 'esfericoizq' => request('esfericod2'), 'cilindroizq' => request('cilindrod2'),
                        'ejeizq' => request('ejed2'), 'id_producto' => request('producto'), 'id_paquete' => request('paquete'), 'fechaentrega' => request('fechaentrega'),
                        'material' => request('material'), 'materialotro' => request('motro'), 'bifocal' => request('bifocal'), 'fotocromatico' => $fotocromatico, 'ar' => $ar,
                        'tinte' => $tinte, 'blueray' => $blueray, 'otroT' => $otroTra, 'tratamientootro' => request('otroT'), 'costomaterial' => request('costomaterial'),
                        'costotratamiento' => request('costoT'), 'observaciones' => request('observaciones'), 'observacionesinterno' => request('observacionesinterno'),
                        'created_at' => Carbon::now()
                    ]);
                    return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                     ->with('bien', 'El historial clinico y el contrato se crearon correctamente.');
                }
            } catch (\Exception $e) {
                \Log::info("Error: " . $e);
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

    public function crearhistorialclinico2($idFranquicia, $idContrato, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12) ||
            Auth::check() && ((Auth::user()->rol_id) == 6)) {
            $rules = [
                'edad' => 'required|string|max:255',
                'diagnostico' => 'required|string|max:255',
                'ocupacion' => 'required|string|max:255',
                'diabetes' => 'required|string|max:255',
                'hipertension' => 'required|string|max:255',
                'producto' => 'required|string',
                'ultimoexamen' => 'nullable|date',
                'fechaentrega' => 'required|date'

            ];
            if (request('material') != '3' && request('motro') != null && request('costomaterial') != null) {
                return back()->withErrors(['motro' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
            }
            if (request('material') == '3' && request('motro') == null && request('costomaterial') == null) {
                return back()->withErrors(['motro' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
            }
            if (request('material') == '3' && request('motro') == null && request('costomaterial') != null) {
                return back()->withErrors(['motro' => 'Llenar ambos campos para otro.'])->withInput($request->all());
            }
            if (request('material') == '3' && request('motro') != null && request('costomaterial') == null) {
                return back()->withErrors(['motro' => 'Llenar ambos campos para otro.'])->withInput($request->all());
            }
            if (request('producto') == 'nada') {
                return back()->withErrors(['producto' => 'Campo obligatorio.'])->withInput($request->all());
            }
            if (request('otroTra') != null && request('otroT') == null && request('costoT') == null) {
                return back()->withErrors(['otroT' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
            }
            if (request('otroTra') != null && request('otroT') != null && request('costoT') == null) {
                return back()->withErrors(['otroT' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
            }
            if (request('otroTra') != null && request('otroT') == null && request('costoT') != null) {
                return back()->withErrors(['otroT' => 'Llenar ambos campos para otro tratamiento.'])->withInput($request->all());
            }
            if (request('otroTra') == null && request('otroT') != null && request('costoT') != null) {
                return back()->withErrors(['otroT' => 'Llenar ambos campos para otro tratamiento.'])->withInput($request->all());
            }
            if (request('paquete') == 1) {
                if (request('fotocromatico') != null && request('ar') != null || request('blueray') != null || request('otroTra') != null) {
                    $n = 0;
                    if (request('fotocromatico') == 1) {

                        $n = $n + 1;
                    }
                    if (request('ar') == 1) {

                        $n = $n + 1;
                    }
                    if (request('blueray') == 1) {

                        $n = $n + 1;
                    }
                    if (request('otroTra') == 1) {
                        $n = $n + 1;
                    }

                    if ($n > 1) {

                        return back()->withErrors(['fotocromatico' => 'Solo se permite uno con el paquete de lectura.'])->withInput($request->all());
                    }
                }
            }
            if (request('tinte') == 1 && request('paquete') == 1) {
                return back()->withErrors(['tinte' => 'No se permite agregar tinte con este paquete.'])->withInput($request->all());
            }
            if (request('tinte') == 1 && request('paquete') == 2) {
                return back()->withErrors(['tinte' => 'No se permite agregar tinte con este paquete.'])->withInput($request->all());
            }
            request()->validate($rules);

            if (request('paquete') == 'DORADO 2') {
                $validacion = Validator::make($request->all(), [
                    'esfericod' => 'required|string',
                    'cilindrod' => 'required|string',
                    'ejed' => 'required|string',
                    'esfericod2' => 'required|string',
                    'cilindrod2' => 'required|string',
                    'ejed2' => 'required|string',

                ]);
                if ($validacion->fails()) {
                    return back()->withErrors([
                        'esfericod' => 'Campo requerido para DORADO2',
                        'cilindrod' => 'Campo requerido para DORADO2',
                        'ejed' => 'Campo requerido para DORADO2',
                        'esfericod2' => 'Campo requerido para DORADO2',
                        'cilindrod2' => 'Campo requerido para DORADO2',
                        'ejed2' => 'Campo requerido para DORADO2',
                    ])->withInput($request->all());
                }
            }
            try {
                $randomIdH2 = $this->getHistorialId2();
                $historialC = DB::select("SELECT h.edad, h.diagnostico, h.hipertension, h.id_paquete, h.diabetes, h.ocupacion, h.dolor, h.ardor, h.golpeojos,
                h.molestiaotro, h.ultimoexamen, p.nombre
                FROM historialclinico h
                INNER JOIN paquetes p
                ON p.id = h.id_paquete
                WHERE id_contrato = '$idContrato'");
                $paquetes = DB::select("SELECT * FROM paquetes WHERE id_franquicia = '$idFranquicia'");
                $edad = $historialC[0]->edad;
                $diagnostico = $historialC[0]->diagnostico;
                $ocupacion = $historialC[0]->ocupacion;
                $diabetes = $historialC[0]->diabetes;
                $hipertension = $historialC[0]->hipertension;
                $dolor = $historialC[0]->dolor;
                $ardor = $historialC[0]->ardor;
                $golpeojos = $historialC[0]->golpeojos;
                $ultimoexamen = $historialC[0]->ultimoexamen;
                $molestiaotro = $historialC[0]->molestiaotro;
                $idpaquete = $historialC[0]->id_paquete;
                $paquete = $paquetes[0]->id;
                $otroM = request('otroM') != Null;
                $otroTra = request('otroTra') != Null;
                $value2 = request('producto');
                $armazon = DB::select("SELECT * FROM producto WHERE id = '$value2' AND id_tipoproducto = '1'");
                $arma = $armazon[0]->id;
                $armapz = $armazon[0]->piezas - 1;

                $fotocromatico2 = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'fotocromático'");
                $AR2 = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'A/R'");
                $tinte2 = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'tinte'");
                $blueray2 = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'BlueRay'");

                $fotocromatico = request('fotocromatico');
                $ar = request('ar');
                $tinte = request('tinte');
                $blueray = request('blueray');
                $otromaterial = request('costomaterial');
                $otrotratamiento = request('costoT');
                if ($tinte != null) {
                    $tinte = $tinte2[0]->precio;
                    $tinte6 = 1;
                    $fotocromatico = null;
                    $ar = null;
                    $blueray = null;
                } else {
                    $tinte = null;
                    $tinte6 = 0;
                }
                if ($fotocromatico != null && $idpaquete != 2) {
                    $fotocromatico = $fotocromatico2[0]->precio;
                    $fotocromatico6 = 1;
                } else {
                    $fotocromatico = null;
                    $fotocromatico6 = 0;
                }
                if ($ar != null) {
                    $ar = $AR2[0]->precio;
                    $ar6 = 1;
                } else {
                    $ar = null;
                    $ar6 = 0;
                }
                if ($blueray != null) {
                    $blueray = $blueray2[0]->precio;
                    $blueray6 = 1;
                } else {
                    $blueray6 = 0;
                }
                $totalidad = "";
                if ($tinte != null && $idpaquete > 2) {
                    $totalidad = $tinte + $otromaterial + $otrotratamiento;
                } else {
                    $totalidad = $fotocromatico + $ar + $blueray + $otromaterial + $otrotratamiento;
                }

                $paquetes = DB::select("SELECT * FROM paquetes WHERE id_franquicia = '$idFranquicia' and id = '6'");
                $con = DB::select("SELECT * FROM contratos WHERE id_franquicia = '$idFranquicia' and id = '$idContrato'");
                $val = $con[0]->totalhistorial;
                $total = $val + $totalidad;
                $historial = DB::select("SHOW TABLE STATUS LIKE 'historialclinico'");
                $siguienteId = $historial[0]->Auto_increment;


                DB::table('historialclinico')->insert([
                    'id' => $randomIdH2, 'id_contrato' => $idContrato, 'edad' => $edad, 'diagnostico' => $diagnostico, 'ocupacion' => $ocupacion, 'diabetes' => $diabetes,
                    'hipertension' => $hipertension, 'dolor' => $dolor, 'ardor' => $ardor, 'golpeojos' => $golpeojos, 'otroM' => $otroM, 'ultimoexamen' => $ultimoexamen,
                    'molestiaotro' => $molestiaotro, 'esfericoder' => request('esfericod'), 'cilindroder' => request('cilindrod'), 'ejeder' => request('ejed'),
                    'esfericoizq' => request('esfericod2'), 'cilindroizq' => request('cilindrod2'), 'ejeizq' => request('ejed2'), 'id_producto' => request('producto'),
                    'id_paquete' => $idpaquete, 'fechaentrega' => request('fechaentrega'), 'material' => request('material'), 'materialotro' => request('motro'),
                    'bifocal' => request('bifocal'), 'fotocromatico' => $fotocromatico6, 'ar' => $ar6, 'tinte' => $tinte6, 'blueray' => $blueray6, 'otroT' => $otroTra,
                    'tratamientootro' => request('otroT'), 'costomaterial' => request('costomaterial'), 'costotratamiento' => request('costoT'),
                    'observaciones' => request('observaciones'), 'observacionesinterno' => request('observacionesinterno'), 'created_at' => Carbon::now()
                ]);

                DB::table('producto')->where([['id', '=', $arma], ['id_franquicia', '=', $idFranquicia]])->update([
                    'piezas' => $armapz
                ]);

                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                    'totalhistorial' => $total, 'total' => $total, 'estatus' => 0, 'totalreal' => $total
                ]);
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $idContrato])
                                 ->with('bien', 'El historial clinico se creo correctamente.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function editarHistorial($idFranquicia, $idContrato, $idHistorial, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 13) || ((Auth::user()->rol_id) == 12) ||
            Auth::check() && ((Auth::user()->rol_id) == 6)) {

            if (request('tinte') == 1 && request('paquete') == 'PROTECCION') {
                return back()->withErrors(['tinte' => 'No se permite agregar tinte con este paquete.']);
            }
            if (request('tinte') == 1 && request('paquete') == 'LECTURA') {
                return back()->withErrors(['tinte' => 'No se permite agregar tinte con este paquete.']);
            }
            if (request('otroTra') != null && request('otroT') == null && request('costoT') == null) {
                return back()->withErrors(['otroT' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
            }
            if (request('otroTra') != null && request('otroT') != null && request('costoT') == null) {
                return back()->withErrors(['otroT' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
            }
            if (request('otroTra') != null && request('otroT') == null && request('costoT') != null) {
                return back()->withErrors(['otroT' => 'Llenar ambos campos para otro tratamiento.'])->withInput($request->all());
            }
            if (request('otroTra') == null && request('otroT') != null && request('costoT') != null) {
                return back()->withErrors(['otroT' => 'Llenar ambos campos para otro tratamiento.'])->withInput($request->all());
            }
            if (request('otroTra') == null && request('otroT') != null && request('costoT') == null) {
                return back()->withErrors(['otroT' => 'Solo se permite con la opción de "otro".'])->withInput($request->all());
            }
            if (request('otroTra') == null && request('otroT') == null && request('costoT') != null) {
                return back()->withErrors(['costoT' => 'Llenar ambos campos para otro tratamiento.'])->withInput($request->all());
            }
            if (request('ar') != null && request('blueray') != null) {
                return back()->withErrors(['ar' => 'Solo se puede elegir uno entre AR y BlueRay.'])->withInput($request->all());
            }

            $datosHistorial = DB::select("SELECT id_contrato ,id, tinte, ar, fotocromatico, blueray, otroT, id_paquete
                                                    FROM historialclinico WHERE id = '$idHistorial' AND id_contrato = '$idContrato'");
            $contrato = $datosHistorial[0]->id_contrato;
            $tinte3 = $datosHistorial[0]->tinte;
            $ar3 = $datosHistorial[0]->ar;
            $fotocromatico3 = $datosHistorial[0]->fotocromatico;
            $blueray3 = $datosHistorial[0]->blueray;
            $otroT3 = $datosHistorial[0]->otroT;
            $paquetedatos = $datosHistorial[0]->id_paquete;

            $fotocromatico2 = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'fotocromático'");
            $AR2 = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'A/R'");
            $tinte2 = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'tinte'");
            $blueray2 = DB::select("SELECT * FROM tratamientos WHERE id_franquicia = '$idFranquicia' AND nombre = 'BlueRay'");
            $randomId2 = $this->getContratoHistorialId();
            $fotocromatico = request('fotocromatico');
            $ar = request('ar');
            $tinte = request('tinte');
            $blueray = request('blueray');
            $otrotra = request('otroTra');
            $otroT = request('otroT');
            $costotra = request('costoT');
            $movimientos = "";
            if ($paquetedatos == 1) {
                if (request('fotocromatico') != null && request('ar') != null || request('blueray') != null || request('otroTra') != null) {
                    $n = 0;
                    if (request('fotocromatico') == 1) {

                        $n = $n + 1;
                    }
                    if (request('ar') == 1) {

                        $n = $n + 1;
                    }
                    if (request('blueray') == 1) {
                        $n = $n + 1;
                    }
                    if (request('otroTra') == 1) {
                        $n = $n + 1;
                    }
                    if ($n > 1) {

                        return back()->withErrors(['fotocromatico' => 'Solo se permite un tratamiento con el paquete de lectura.'])->withInput($request->all());
                    }
                }
            }
            if ($tinte != null && $tinte3 == null) {
                $tinte = 1;
                $tinte7 = $tinte2[0]->precio;
                $movimientos = 'tinte';
            } else {
                if ($tinte === null && $tinte3 != null) {
                    $tinte = null;
                    $tinte7 = 0;
                    $movimientos = strlen($movimientos) == 0 ? 'tinte' : $movimientos . '/tinte';
                } else {
                    $tinte = $tinte3;
                }
                $tinte7 = 0;
            }
            if ($fotocromatico != null && $fotocromatico3 == null) { //FOto request && foto BD
                $fotocromatico = 1;
                $fotocromatico7 = $fotocromatico2[0]->precio;
                $movimientos = strlen($movimientos) == 0 ? 'fotocromatico' : $movimientos . '/fotocromatico';
            } else {
                if ($fotocromatico === null && $fotocromatico3 != null) {
                    $fotocromatico = null;
                    $fotocromatico7 = 0;
                    $movimientos = strlen($movimientos) == 0 ? 'fotocromatico' : $movimientos . '/fotocromatico';
                } else {
                    $fotocromatico = $fotocromatico3;
                }
                $fotocromatico7 = 0;
            }
            if ($blueray != null && $blueray3 == null) { // BLueray request y blueray de BD.
                $blueray = 1;
                $blueray7 = $blueray2[0]->precio;
                $movimientos = strlen($movimientos) == 0 ? 'blueray' : $movimientos . '/blueray';
            } else {
                if ($blueray === null && $blueray3 != null) {
                    $blueray = null;
                    $movimientos = strlen($movimientos) == 0 ? 'fotocromatico' : $movimientos . '/fotocromatico';
                } else {
                    $blueray = $blueray3;
                }
                $blueray7 = 0;
            }
            if ($ar != null && $ar3 === null) {
                $ar = 1;
                $ar7 = $AR2[0]->precio;
                $movimientos = strlen($movimientos) == 0 ? 'AR' : $movimientos . '/AR';
            } else {
                if ($ar === null && $ar3 != 0) {
                    $ar = null;
                    $movimientos = strlen($movimientos) == 0 ? 'AR' : $movimientos . '/AR';
                } else {
                    $ar = $ar3;
                }
                $ar7 = 0;
            }
            $trataientoextra = 0;
            if ($otrotra != null && $otroT3 == 0) {
                $trataientoextra = $costotra;
                $movimientos = strlen($movimientos) == 0 ? 'Tratamiento extra' : $movimientos . '/Tratamiento extra';
            } else {
                if ($otrotra === null && $otroT3 == 0) {
                    $otrotra = null;
                    $movimientos = strlen($movimientos) == 0 ? 'AR' : $movimientos . '/AR';
                } else {
                    $otrotra = $otroT3;
                    $trataientoextra = 0;
                }
            }
            $totalidad = "";
            if ($tinte != null) {
                $totalidad = $tinte7 + $trataientoextra;
            } else {
                if (request('paquete') == 'PROTECCION') {
                    $totalidad = $ar7 + $blueray7 + $trataientoextra;
                } else {
                    $totalidad = $fotocromatico7 + $ar7 + $blueray7 + $trataientoextra;
                }
            }


            $contratos = DB::select("SELECT * FROM contratos  WHERE  id = '$contrato'");
            $TH = $contratos[0]->totalhistorial + $totalidad;
            $TH2 = $contratos[0]->total + $totalidad;
            $TH3 = $contratos[0]->totalpromocion + $totalidad;
            $totalreal = $contratos[0]->totalreal + $totalidad;
            $usuarioId = Auth::user()->id;
            $actualizar = Carbon::now();
            $esIgual = 0;

            if ($ar == $ar3 && $fotocromatico == $fotocromatico3 && $blueray == $blueray3 && $tinte == $tinte3 && $otroT3 == $otrotra) {
                $esIgual = 1;
            }
            try {

                DB::table('contratos')->where([['id', '=', $contrato], ['id_franquicia', '=', $idFranquicia]])->update([
                    'totalhistorial' => $TH, 'total' => $TH2, 'totalreal' => $totalreal
                ]);
                if ($esIgual == 0) {
                    DB::table('historialcontrato')->insert([
                        'id' => $randomId2, 'id_usuarioC' => $usuarioId, 'id_contrato' => $contrato, 'created_at' => $actualizar,
                        'cambios' => " Se modifico el historial clinico: '$idHistorial', Se agrego el cambio en tratamientos: '$movimientos'"
                    ]);
                }


                DB::table('historialclinico')->where([['id', '=', $idHistorial], ['id_contrato', '=', $contrato]])->update([
                    'fotocromatico' => $fotocromatico, 'ar' => $ar, 'tinte' => $tinte, 'blueray' => $blueray, 'otroT' => $otrotra, 'tratamientootro' => $otroT, 'costotratamiento' => $costotra
                ]);
                return redirect()->route('vercontrato', ['idFranquicia' => $idFranquicia, 'idContrato' => $contrato])->with('bien', 'El historial clinico se actualizo correctamente.');


            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function editarHistorialArmazon($idFranquicia, $idContrato, $idHistorial, Request $request)
    {
        if (Auth::check() && (((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8))) {

            $producto = request('producto');

            $existeArmazon = DB::select("SELECT * FROM producto p WHERE p.id = '$producto' LIMIT 1");

            $datosHistorial = DB::select("SELECT id_contrato, id, id_producto FROM historialclinico WHERE id = '$idHistorial' AND id_contrato = '$idContrato'");
            $idcontrato = $datosHistorial[0]->id_contrato;

            $contrato = DB::select("SELECT estatus_estadocontrato FROM contratos WHERE id = '$idcontrato'");

            if($existeArmazon != null) {

                if ($contrato != null) {
                    if ($contrato[0]->estatus_estadocontrato > 1 && $contrato[0]->estatus_estadocontrato != 9) {
                        return back()->with('alerta', 'No puedes cambiar el modelo del armazon en este momento.');
                    }
                }

                $usuarioId = Auth::user()->id;
                $actualizar = Carbon::now();
                $randomId2 = $this->getContratoHistorialId();

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
                    return redirect()->route('contratoactualizar', [$idFranquicia, $idcontrato])->with("bien", "El historial clinico se actualizo correctamente.");

                } catch (\Exception $e) {
                    \Log::info("Error: " . $e->getMessage());
                    return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
                }

            }else{
                //No existe el armazon
                return back()->with('alerta', 'Selecciona un producto valido.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function agregarGarantiaHistorial($idFranquicia, $idContrato, $idHistorial, Request $request)
    {
        if (Auth::check() && (((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8))) {
            if (request('optometristagarantia') == 'nada') {
                return back()->withErrors(['optometristagarantia' => 'Campo obligatorio.'])->withInput($request->all());
            }

            try {

                $datosHistorial = DB::select("SELECT id_contrato, tipo FROM historialclinico WHERE id = '$idHistorial' AND id_contrato = '$idContrato'");

                if ($datosHistorial != null) {
                    //Existe historial

                    $tipoHistorial = $datosHistorial[0]->tipo;

                    if($tipoHistorial == 0) {
                        //Tipo historial es igual a 0

                        $idContrato = $datosHistorial[0]->id_contrato;
                        $id_optometrista = request('optometristagarantia');

                        $optometrista = DB::select("SELECT name FROM users WHERE id = '$id_optometrista'");

                        if ($optometrista != null) {
                            //Existe optometrista

                            $nombreOptometrista = $optometrista[0]->name;
                            $globalesServicioWeb = new globalesServicioWeb;

                            $contrato = DB::select("SELECT estatus_estadocontrato, totalhistorial, totalpromocion, totalreal FROM contratos
                                                            WHERE id = '$idContrato' AND id_franquicia = '$idFranquicia'");

                            if ($contrato != null) {
                                //Existe contrato

                                $estatus_estadocontrato = $contrato[0]->estatus_estadocontrato;

                                if($estatus_estadocontrato == 2 || $estatus_estadocontrato == 5 || $estatus_estadocontrato == 4 || $estatus_estadocontrato == 12) {
                                    //ENTREGADO, LIQUIDADO, ABONO ATRASADO, ENVIADO

                                    $totalhistorialcontratogarantia = $contrato[0]->totalhistorial;
                                    $totalpromocioncontratogarantia = $contrato[0]->totalpromocion;
                                    $totalrealcontratogarantia = $contrato[0]->totalreal;
                                    $tieneGarantiaReportada = DB::select("SELECT id FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia = 0");

                                    if ($tieneGarantiaReportada != null) {
                                        //Tiene garantia reportada

                                        $idGarantia = $tieneGarantiaReportada[0]->id;
                                        DB::table('garantias')->where([['id', '=', $idGarantia], ['id_contrato', '=', $idContrato]])->update([
                                            'id_historial' => $idHistorial,
                                            'id_optometrista' => $id_optometrista,
                                            'estadogarantia' => 1,
                                            'estadocontratogarantia' => $estatus_estadocontrato,
                                            'totalhistorialcontratogarantia' => $totalhistorialcontratogarantia,
                                            'totalpromocioncontratogarantia' => $totalpromocioncontratogarantia,
                                            'totalrealcontratogarantia' => $totalrealcontratogarantia
                                        ]);

                                    } else {
                                        //No tiene garantia reportada

                                        $tieneHistorialGarantiaSinCrear = DB::select("SELECT id FROM garantias WHERE id_contrato = '$idContrato'
                                                                                            AND id_historial = '$idHistorial' AND estadogarantia = 1");

                                        if ($tieneHistorialGarantiaSinCrear != null) {
                                            //Ya tiene asignada una garantia el historial
                                            return redirect()->route('contratoactualizar', [$idFranquicia, $idContrato])
                                                ->with('alerta', 'Ya existe una garantia en el historial la cual no ha sido creada.');
                                        } else {
                                            //No tiene asignada una garantia el historial
                                            $idGarantiaAlfanumerico = $globalesServicioWeb::generarIdAlfanumerico('garantias', '5');
                                            DB::table('garantias')->insert([
                                                'id' => $idGarantiaAlfanumerico,
                                                'id_contrato' => $idContrato,
                                                'id_historial' => $idHistorial,
                                                'id_optometrista' => $id_optometrista,
                                                'estadogarantia' => 1,
                                                'estadocontratogarantia' => $estatus_estadocontrato,
                                                'totalhistorialcontratogarantia' => $totalhistorialcontratogarantia,
                                                'totalpromocioncontratogarantia' => $totalpromocioncontratogarantia,
                                                'totalrealcontratogarantia' => $totalrealcontratogarantia,
                                                'created_at' => Carbon::now()
                                            ]);
                                        }

                                    }

                                    $usuarioId = Auth::user()->id;
                                    //Guardar movimiento
                                    DB::table('historialcontrato')->insert([
                                        'id' => $this->getContratoHistorialId(), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                        'cambios' => " Se asigno a '$nombreOptometrista' la garantia para el historial '$idHistorial'"
                                    ]);

                                    //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                                    $contratosGlobal = new contratosGlobal;
                                    $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, $id_optometrista);

                                    return redirect()->route('contratoactualizar', [$idFranquicia, $idContrato])->with('bien', "Se agrego correctamente la garantia al historial.<br>Recuerda que al reportar garantia solo se deben enviar las micas.");

                                }
                                //ESTATUS DEL CONTRATO DIFERENTE A ENTREGADO, LIQUIDADO, ABONO ATRASADO, ENVIADO
                                return redirect()->route('contratoactualizar', [$idFranquicia, $idContrato])->with('alerta', 'El estatus del contrato no es autorizado para realizar garatías');

                            }
                            //No existe el contrato
                            return redirect()->route('contratoactualizar', [$idFranquicia, $idContrato])->with('alerta', 'No existe el contrato');

                        }
                        //No existe el optometrista
                        return redirect()->route('contratoactualizar', [$idFranquicia, $idContrato])->with('alerta', 'No existe el optometrista que se quiere asignar');

                    }
                    //Tipo historial es diferente de 0
                    return back()->with('alerta', 'Ha este tipo de historial no se puede asignar garantía.');

                }
                //No existe el historial
                return back()->with('alerta', 'No existe el historial.');

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

    public function cancelarGarantiaHistorial($idFranquicia, $idContrato, $idHistorial, Request $request)
    {

        if (Auth::check() && (((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8))) {
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

                    $garantiasCancelar = DB::select("SELECT id, id_historial, estadogarantia, estadocontratogarantia, totalhistorialcontratogarantia, totalpromocioncontratogarantia,
                                                            totalrealcontratogarantia, id_optometrista FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia IN (0,1,2)");

                    if($garantiasCancelar != null) {//Tiene garantias para cancelar?
                        //Tiene garantias para cancelar

                        foreach ($garantiasCancelar as $garantiaCancelar) {

                            $idGarantia = $garantiaCancelar->id;
                            $idhistorial = $garantiaCancelar->id_historial;
                            $estadogarantia = $garantiaCancelar->estadogarantia;
                            $estadocontratogarantia = $garantiaCancelar->estadocontratogarantia;
                            $totalhistorialcontratogarantia = $garantiaCancelar->totalhistorialcontratogarantia;
                            $totalpromocioncontratogarantia = $garantiaCancelar->totalpromocioncontratogarantia;
                            $totalrealcontratogarantia = $garantiaCancelar->totalrealcontratogarantia;
                            $idoptometristagarantia = $garantiaCancelar->id_optometrista;

                            $usuarioId = Auth::user()->id;

                            switch ($estadogarantia) {
                                case 0:
                                    //No se han creado las garantia 0
                                    DB::table('garantias')->where([['id', '=', $idGarantia], ['id_contrato', '=', $idContrato]])->update([
                                        'estadogarantia' => 4,
                                        'updated_at' => Carbon::now()
                                    ]);
                                    //Guardar movimiento
                                    DB::table('historialcontrato')->insert([
                                        'id' => $this->getContratoHistorialId(), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                        'cambios' => " Cancelo la garantia reportada con el siguiente mensaje: '" . $request->input('mensaje') . "'"
                                    ]);
                                    break;
                                case 1:
                                    //No se han creado las garantia 1
                                    DB::table('garantias')->where([['id', '=', $idGarantia], ['id_contrato', '=', $idContrato], ['id_historial', '=', $idhistorial]])->update([
                                        'estadogarantia' => 4,
                                        'updated_at' => Carbon::now()
                                    ]);
                                    //Guardar movimiento
                                    DB::table('historialcontrato')->insert([
                                        'id' => $this->getContratoHistorialId(), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                        'cambios' => " Cancelo la garantia al historial '$idhistorial' con el siguiente mensaje: '" . $request->input('mensaje') . "'"
                                    ]);
                                    //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato y el idoptometristagarantia
                                    DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato' AND id_usuario = '$idoptometristagarantia'");
                                    break;
                                case 2:
                                    //Ya se habian creado las garantias
                                    $globalesServicioWeb = new globalesServicioWeb;
                                    $contrato = DB::select("SELECT totalhistorial, totalpromocion, totalabono, totalproducto FROM contratos
                                                                    WHERE id = '$idContrato' AND id_franquicia = '$idFranquicia'");

                                    if($contrato != null) {
                                        //Se encontro el contrato
                                        $totalhistorial = $contrato[0]->totalhistorial;
                                        $totalpromocion = $contrato[0]->totalpromocion;
                                        $totalabono = $contrato[0]->totalabono;
                                        $totalproducto = $contrato[0]->totalproducto;

                                        if($globalesServicioWeb::obtenerEstadoPromocion($idContrato, $idFranquicia)) {
                                            //Tiene promocion
                                            if($totalpromocion > $totalpromocioncontratogarantia) {
                                                //Devolver el estado del contrato, el total, y el totalpromocion a como estaban
                                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                                    'estatus_estadocontrato' => $estadocontratogarantia,
                                                    'total' => $totalpromocioncontratogarantia + $totalproducto - $totalabono,
                                                    'totalpromocion' => $totalpromocioncontratogarantia,
                                                    'totalhistorial' => $totalhistorialcontratogarantia,
                                                    'totalreal' => $totalrealcontratogarantia
                                                ]);
                                            }else {
                                                //Devolver el estado del contrato
                                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                                    'estatus_estadocontrato' => $estadocontratogarantia
                                                ]);
                                            }

                                        }else {
                                            //No tiene promocion
                                            if($totalhistorial > $totalhistorialcontratogarantia) {
                                                //Devolver el estado del contrato, el total, y el totalhistorial a como estaban
                                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
                                                    'estatus_estadocontrato' => $estadocontratogarantia,
                                                    'total' => $totalhistorialcontratogarantia + $totalproducto - $totalabono,
                                                    'totalhistorial' => $totalhistorialcontratogarantia,
                                                    'totalpromocion' => $totalpromocioncontratogarantia,
                                                    'totalreal' => $totalrealcontratogarantia
                                                ]);
                                            }else {
                                                //Devolver el estado del contrato
                                                DB::table('contratos')->where([['id', '=', $idContrato], ['id_franquicia', '=', $idFranquicia]])->update([
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

                                    }else {
                                        return redirect()->route('contratoactualizar', [$idFranquicia, $idContrato])->with('alerta', 'No se encontro el contrato.');
                                    }

                                    //Actualizar estadogarantia a 4
                                    DB::table('garantias')->where([['id', '=', $idGarantia], ['id_contrato', '=', $idContrato], ['id_historial', '=', $idhistorial]])->update([
                                        'estadogarantia' => 4,
                                        'updated_at' => Carbon::now()
                                    ]);
                                    //Guardar movimiento
                                    DB::table('historialcontrato')->insert([
                                        'id' => $this->getContratoHistorialId(), 'id_usuarioC' => $usuarioId, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                        'cambios' => "Cancelo la garantia al historial '$idhistorial' con el siguiente mensaje: '" . $request->input('mensaje') . "'"
                                    ]);

                                    //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato
                                    DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");

                                    //Insertar o actualizar contrato en tabla contratostemporalessincronizacion
                                    $contratosGlobal = new contratosGlobal;
                                    $contratosGlobal::insertarOActualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato, Auth::id());
                                    break;
                            }

                        }

                        return redirect()->route('contratoactualizar', [$idFranquicia, $idContrato])->with('bien', 'Se cancelo correctamente la garantia del historial.');
                    }

                    //No tiene garantias para cancelar
                    return redirect()->route('contratoactualizar', [$idFranquicia, $idContrato])->with('alerta', 'No se puede cancelar la garantia por que no hay un optometrista asignado.');

                }
                //No existe el historial
                return redirect()->route('contratoactualizar', [$idFranquicia, $idContrato])->with('alerta', 'No existe el historial');

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

    public function actualizarhistorialclinico($idFranquicia, $idContrato, $idHistorial){
        if (Auth::check() && ((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8)) {

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
            $contrato = DB::select("SELECT estatus_estadocontrato, promocionterminada, totalhistorial, totalpromocion, totalreal   FROM contratos WHERE id = '$idContrato'");

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
                                    'id' => $this->getContratoHistorialId(), 'id_usuarioC' => Auth::user()->id, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
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
                                'id' => $this->getContratoHistorialId(), 'id_usuarioC' => Auth::user()->id, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
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

                        /*
                        if($historial[0]->id_paquete == 1 && (str_replace(array('+', '-'), "", $historial[0]->cilindroder) != 0
                           || str_replace(array('+', '-'), "", $historial[0]->cilindroizq) != 0
                           || str_replace(array('+', '-'), "", $historial[0]->esfericoder) > 5.25
                           || str_replace(array('+', '-'), "", $historial[0]->esfericoizq) > 5.25)){

                            $precioCilindroLectura = 590;
                        }
                        */

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

                        /*
                        if($historial[0]->id_paquete == 1){
                            //Paquete de LECTURA
                            if($precioCilindroLectura > 0){
                                //Ya habia sumado el precio del cilindro
                                if(str_replace(array('+', '-'), "", $cilindroDer) != 0 || str_replace(array('+', '-'), "", $cilindroIzq) != 0
                                || str_replace(array('+', '-'), "", $esfericoDer) > 5.25 || str_replace(array('+', '-'), "", $esfericoIzq) > 5.25){
                                    //Sigue estando igual los valores en los cilindros (No se hace nada)
                                    $precioCilindroLectura = 0;

                                }else{
                                    //Cambian los valores de cilindros por 0 (Restar el precio del cilindro)
                                    $precioCilindroLectura = $precioCilindroLectura * -1;
                                }

                            }else{
                                //No habian sumado el precio del cilindro
                                if(str_replace(array('+', '-'), "", $cilindroDer) != 0 || str_replace(array('+', '-'), "", $cilindroIzq) != 0
                                    || str_replace(array('+', '-'), "", $esfericoDer) > 5.25 || str_replace(array('+', '-'), "", $esfericoIzq) > 5.25){
                                    //Los valores en los cilindros son diferentes a 0 (Se suma el precio del cilindro)
                                    $precioCilindroLectura = 590;
                                }
                            }
                        }
                        */

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

    public function actualizarfotoarmazon($idFranquicia, $idContrato, $idHistorial){
        if (Auth::check() && (((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8))) {

            request()->validate([
                'fotoArmazon' . $idHistorial => 'required|image|mimes:jpg'
            ]);

            $contrato = DB::select("SELECT estatus_estadocontrato FROM contratos c WHERE c.id = '$idContrato' AND c.id_franquicia = '$idFranquicia'");
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

                                DB::table('historialcontrato')->insert([
                                    'id' => $this->getContratoHistorialId(), 'id_usuarioC' => Auth::user()->id, 'id_contrato' => $idContrato, 'created_at' => Carbon::now(),
                                    'cambios' => "Actualizo foto armazon para historial clinico '$idHistorial'"
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

}
