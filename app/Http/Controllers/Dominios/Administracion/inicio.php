<?php

namespace App\Http\Controllers\Dominios\Administracion;

class inicio extends Controller
{
    public function cargarTodo(){
      return view('administracion.administracion');
    }
}
