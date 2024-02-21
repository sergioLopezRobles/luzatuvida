<?php

namespace App\Providers;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use View;


class sucursalGlobal extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view) {
            if (Auth::check()) {
                $idUsuario = Auth::user()->id;
                $existeUsuario = DB::select("SELECT id_franquicia FROM usuariosfranquicia where id_usuario = '$idUsuario'");
                View::share('idSucursalGlobal', $existeUsuario[0]->id_franquicia);
            }
        });
    }
}
