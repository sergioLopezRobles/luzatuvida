<?php

namespace App\Http\Controllers\Dominios\Administracion;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\archivos;
use Illuminate\Support\Facades\Storage;
use Image;

class adminpaginaclientes extends Controller
{

    public function administracionimagenes($idFranquicia){
        if (Auth::check() && ((Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 18)) //ROL DE DIRECTOR, REDES
        {
            //Franquicias para modal de agendar citas

            $imagenesCarrusel = DB::select("SELECT * FROM imagenescarrusel ic ORDER BY ic.posicion ASC");

            return view('administracion.clientes.administracion.administracionimagenes', ['idFranquicia' => $idFranquicia,
                'imagenesCarrusel' => $imagenesCarrusel]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function agregarimagencarrucel($idFranquicia){
        if (Auth::check() && ((Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 18)) //ROL DE DIRECTOR, REDES
        {
            //Validar datos ingresados
            request()->validate([
                'nuevaImagen' => 'required|image|mimes:jpg',
            ]);

            try {

                //Procesar imagen para su almacenamiento
                $nuevaFotoCarrusel = "";
                $imagenBruta = 'Imagen-Carrusel-' . time() . '.' . request()->file('nuevaImagen')->getClientOriginalExtension();
                $nuevaFotoCarrusel = request()->file('nuevaImagen')->storeAs('uploads/imagenes/paginaclientes/carrusel', $imagenBruta, 'disco');

                //Validar tamaño de imagen tam: 1200 x 900
                $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/paginaclientes/carrusel/' . $imagenBruta)->width();
                $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/paginaclientes/carrusel/' . $imagenBruta)->height();
                if($ancho == 1350 || $alto == 900){
                    $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/paginaclientes/carrusel/' . $imagenBruta)->resize(1350, 900);
                    $imagenfoto->save();
                }else{
                    return back()->with('alerta', "Selecciona una imagen que cumpla con el tamaño requerido de 1350 x 900 píxeles.");
                }

                // Optener ultima posicion almacenada para determinar siguiente posicion siguienteposicion = ultima posicion + 1
                $siguienteposicion = 1; //Por defaul almacenar en posicion 1
                $ultimaPosicion = DB::select("SELECT ic.posicion FROM imagenescarrusel ic ORDER BY ic.posicion DESC LIMIT 1");

                if($ultimaPosicion != null){
                    //Existen imagenes ya almacenadas
                    $siguienteposicion = $ultimaPosicion[0]->posicion + 1;
                }

                //Almacenar imagen en tabla imagenescarrusel
                DB::table('imagenescarrusel')->insert([
                    'imagen' => $nuevaFotoCarrusel, 'nombre' => $imagenBruta, 'posicion' => $siguienteposicion, 'created_at' => Carbon::now(),
                ]);

                return back()->with('bien', "Imagen agregada correctamente al carrucel en posición: '".$siguienteposicion."'");

            } catch (\Exception $e) {
                \Log::error('Error: ' . $e->getMessage());
                return back()->with('error', 'Tuvimos un error, por favor contacta al Administrador de la pagina.\nError: ' . $e->getMessage());
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarposicionimagencarrucel($idFranquicia,  Request $request){
        if (Auth::check() && ((Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 18)) //ROL DE DIRECTOR, REDES
        {
            //Validar datos ingresados
            request()->validate([
                'imagenSeleccionada' => 'required|numeric',
                'posicionSeleccionada' => 'required|numeric'
            ]);

            $idImagenSeleccionada = $request->input('imagenSeleccionada');
            $posicionSeleccionada = $request->input('posicionSeleccionada');

            $existeImagen = DB::select("SELECT ic.id, ic.posicion, ic.nombre FROM imagenescarrusel ic WHERE ic.id = '$idImagenSeleccionada'");

            if($existeImagen != null){
                //Existe la imagen - Verificar posicion seleccionada
                $ultimaPosicion = DB::select("SELECT ic.posicion FROM imagenescarrusel ic ORDER BY ic.posicion DESC LIMIT 1");
                if($posicionSeleccionada > 0 && ($ultimaPosicion != null && $ultimaPosicion[0]->posicion >= $posicionSeleccionada)){
                    //Posicion ingresada es un numero positivo - Verificar que sea diferente a posicion actual
                    if($existeImagen[0]->posicion != $posicionSeleccionada){
                        //Posicion seleccionada distinta a la actual

                        $posicionActualImagen = $existeImagen[0]->posicion;

                        //Posicion seleccionada es mayor a posicion actual de imagen?
                        if($posicionActualImagen < $posicionSeleccionada){
                            //Mover una posicion todos los registros anteriores a la posicion seleccionada
                            DB::update("UPDATE imagenescarrusel ic SET ic.posicion = (ic.posicion - 1) WHERE (ic.posicion <= '$posicionSeleccionada' AND ic.posicion > '$posicionActualImagen') AND ic.posicion != '$posicionActualImagen'");
                        }else{
                            //Posicion seleccionada es menor a posicion actual de la imagen seleccionada
                            //Mover una posicion todos los registros despues a la posicion seleccionada
                            DB::update("UPDATE imagenescarrusel ic SET ic.posicion = (ic.posicion + 1) WHERE (ic.posicion >= '$posicionSeleccionada' AND ic.posicion < '$posicionActualImagen') AND ic.posicion != '$posicionActualImagen'");
                        }

                        //Actualizar posicion de imagen seleccionada a posicion seleccionada
                        DB::update("UPDATE imagenescarrusel ic SET ic.posicion = $posicionSeleccionada WHERE ic.id = '$idImagenSeleccionada'");
                        return back()->with('bien', "Se actualizó la posición de forma correcta para imagen: '".$existeImagen[0]->nombre."' | Nueva posición: '".$posicionSeleccionada."'");

                    }else{
                        //Posicion seleccionada igual a posicion actual
                        return back()->with('alerta', "No puedes cambiar la imagen a la misma posición donde se encuentra actualmente.");
                    }
                }else{
                    //Posicion seleccionada incorrecta
                    return back()->with('alerta', "Posición seleccionada es incorrecta.");
                }
            }else{
                //No existe la imagen en la BD
                return back()->with('alerta', "No se encontro la imagen seleccionada.");
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function eliminarimagencarrusel($idFranquicia, $idImagen){
        if (Auth::check() && ((Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 18)) //ROL DE DIRECTOR, REDES
        {
            $existeImagen = DB::select("SELECT * FROM imagenescarrusel ic WHERE ic.id = '$idImagen'");

            if($existeImagen != null){
                //existe la imagen en la BD
                $posicionImagenEliminada = $existeImagen[0]->posicion;

                //Eliminar registro de BD
                DB::delete("DELETE FROM imagenescarrusel WHERE id = '$idImagen'");

                //Eliminar imagen del servidor
                try {
                    Storage::disk('disco')->delete($existeImagen[0]->imagen);
                }catch(\Exception $e){
                    \Log::info("Error: ".$e);
                }

                //Actualizar posiciones de imagenes
                DB::update("UPDATE imagenescarrusel ic SET ic.posicion = (ic.posicion - 1) WHERE ic.posicion > '$posicionImagenEliminada'");

                return back()->with('bien',"Imagen: '".$existeImagen[0]->nombre."' fue eliminada correctamente.");

            }else{
                 return back()->with('alerta','No se encontro la imagen seleccionada.');
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
