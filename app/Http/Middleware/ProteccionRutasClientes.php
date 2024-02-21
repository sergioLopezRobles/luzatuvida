<?php

namespace App\Http\Middleware;

use App\Clases\contratosGlobal;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\DB;
use View;

class ProteccionRutasClientes
{
    public function handle(Request $request, Closure $next)
    {
        try{
            $idFranquicia = $request->idFranquicia;
            //Verificar si existe sucursal y esta activa
            $sucursalActiva = DB::select("SELECT estado FROM configfranquicia WHERE id_franquicia = '$idFranquicia'");

            if($sucursalActiva == null){
                //Es una sucursal no existente o aun no funcional
                return redirect()->route('bienvenida');
            }

        }catch(\Exception $e){
            ("Error: ".$e->getMessage());
        }

        return $next($request);
    }
}
