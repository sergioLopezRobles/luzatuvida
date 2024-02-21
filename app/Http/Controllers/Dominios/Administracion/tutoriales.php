<?php

namespace App\Http\Controllers\Dominios\Administracion;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class tutoriales extends Controller
{

    public function listatutoriales($idFranquicia) {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8) {
            //ADMINISTRACION, DIRECTOR, PRINCIPAL

            $tutorialesAdministracion = array();
            $tutorialesCobranza = array();
            $tutorialesConfirmaciones = array();
            $tutorialesLaboratorio = array();
            $tutorialesVentas = array();
            $tutorialesOtros= array();

            $tutoriales = DB::select("SELECT * FROM tutoriales  ORDER BY titulo ASC");

            foreach ($tutoriales as $tutorial){

                switch ($tutorial->id_seccion){
                    case 1:
                        //Administracion
                        array_push($tutorialesAdministracion, $tutorial);
                        break;
                    case 2:
                        //Cobranza
                        array_push($tutorialesCobranza, $tutorial);
                        break;
                    case 3:
                        //Confirmaciones
                        array_push($tutorialesConfirmaciones, $tutorial);
                        break;
                    case 4:
                        //Laboratorio
                        array_push($tutorialesLaboratorio, $tutorial);
                        break;
                    case 5:
                        //Ventas
                        array_push($tutorialesVentas, $tutorial);
                        break;
                    case 6:
                        //Otros
                        array_push($tutorialesOtros, $tutorial);
                        break;
                }
            }

            $secciones = DB::select("SELECT * FROM secciontutorial ORDER BY created_at ASC");

            return view('administracion.tutoriales.listatutoriales', [
                'idFranquicia' => $idFranquicia, 'secciones' => $secciones, 'tutorialesAdministracion' => $tutorialesAdministracion, 'tutorialesCobranza' => $tutorialesCobranza,
                'tutorialesConfirmaciones' => $tutorialesConfirmaciones, 'tutorialesLaboratorio' => $tutorialesLaboratorio, 'tutorialesVentas' => $tutorialesVentas,
                'tutorialesOtros' => $tutorialesOtros
            ]);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function listatutorialesfiltrar($idFranquicia, Request $request) {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8) {
            //ADMINISTRACION, DIRECTOR, PRINCIPAL

            $filtro = $request->input('filtro');

            $tutorialesAdministracion = array();
            $tutorialesCobranza = array();
            $tutorialesConfirmaciones = array();
            $tutorialesLaboratorio = array();
            $tutorialesVentas = array();
            $tutorialesOtros= array();

            if($filtro != null){
                //Filtro es distinto de vacio
                $tutoriales = DB::select("SELECT * FROM tutoriales WHERE titulo LIKE '%$filtro%' OR descripcion LIKE '%$filtro%' ORDER BY titulo ASC");
            }else{
                //Filtro vacio
                $tutoriales = DB::select("SELECT * FROM tutoriales  ORDER BY titulo ASC");
            }

            foreach ($tutoriales as $tutorial){

                switch ($tutorial->id_seccion){
                    case 1:
                        //Administracion
                        array_push($tutorialesAdministracion, $tutorial);
                        break;
                    case 2:
                        //Cobranza
                        array_push($tutorialesCobranza, $tutorial);
                        break;
                    case 3:
                        //Confirmaciones
                        array_push($tutorialesConfirmaciones, $tutorial);
                        break;
                    case 4:
                        //Laboratorio
                        array_push($tutorialesLaboratorio, $tutorial);
                        break;
                    case 5:
                        //Ventas
                        array_push($tutorialesVentas, $tutorial);
                        break;
                    case 6:
                        //Otros
                        array_push($tutorialesOtros, $tutorial);
                        break;
                }
            }

            $secciones = DB::select("SELECT * FROM secciontutorial ORDER BY created_at ASC");

            return view('administracion.tutoriales.listatutoriales', [
                'idFranquicia' => $idFranquicia, 'secciones' => $secciones, 'tutorialesAdministracion' => $tutorialesAdministracion, 'tutorialesCobranza' => $tutorialesCobranza,
                'tutorialesConfirmaciones' => $tutorialesConfirmaciones, 'tutorialesLaboratorio' => $tutorialesLaboratorio, 'tutorialesVentas' => $tutorialesVentas,
                'tutorialesOtros' => $tutorialesOtros
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function agregarvideotutorial($idFranquicia,  Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8) {
            //ADMINISTRACION, DIRECTOR, PRINCIPAL

            request()->validate([
                'titulo' => 'required|string',
                'descripcion' => 'required|string',
                'enlace' => 'required|url',
                'seccionSeleccionada' => 'required'
            ]);

            $titulo = $request->input('titulo');
            $descripcion = $request->input('descripcion');
            $link = $request->input('enlace');
            $seccion = $request->input('seccionSeleccionada');

            $existeSeccion = DB::select("SELECT * FROM secciontutorial WHERE id = '$seccion'");
            if($existeSeccion != null){
                //Existe la seccion
                DB::table('tutoriales')->insert([
                    'titulo' => $titulo, 'descripcion' => $descripcion, 'link' => $link, 'id_seccion' => $seccion, 'created_at' => Carbon::now()
                ]);

                return redirect()->route('listatutoriales',$idFranquicia)->with('bien','Video tutorial registrado correctamente.');

            }else{
                //No existe la seccion de videos
                return back()->with('alerta','Seccion seleccionada para video no encontrada.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function eliminarvideotutorial($idFranquicia, $idVideo)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8) {
            //ADMINISTRACION, DIRECTOR, PRINCIPAL

            $existeVideo = DB::select("SELECT * FROM tutoriales WHERE indice = '$idVideo'");

            if ($existeVideo != null) {
                //Existe la seccion
                DB::delete("DELETE FROM tutoriales WHERE indice = '$idVideo'");
                return redirect()->route('listatutoriales', $idFranquicia)->with('bien', 'Video tutorial eliminado correctamente.');

            } else {
                //No existe la seccion de videos
                return back()->with('alerta', 'No existe el video.');
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
