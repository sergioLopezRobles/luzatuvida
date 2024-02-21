@extends('layouts.app')
@section('titulo','Inicio'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')  
  @include('parciales.notificaciones')
  <div class="contenedor">
    @if(Auth::user()->rol_id==7)      
      <div id="franquiciaLista">@include('administracion.franquicia.lista')</div> 
      <div id="franquicia">@include('administracion.franquicia.nueva')</div> 
    @endif     
    <div id="finanzas"><h1>Finanzas</h1></div>
    <div id="cobranza"><h1>Cobranza</h1></div>
    <div id="auxiliar"><h1>Auxiliar</h1></div>
  </div>
  @include('administracion.validaciones')
@endsection
