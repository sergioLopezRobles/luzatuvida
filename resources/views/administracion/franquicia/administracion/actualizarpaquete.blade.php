@extends('layouts.app')
@section('titulo','Actualizar paquete'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2>@lang('mensajes.mensajeeditartratamiento')</h2>
    <form  action="{{route('paqueteeditar',[$idFranquicia,$paquete[0]->id])}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
        @csrf
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="paquete" class="form-control {!! $errors->first('paquete','is-invalid')!!}"  placeholder="Tratamiento"  value="{{$paquete[0]->nombre}}">
                    {!! $errors->first('paquete','<div class="invalid-feedback">Nombre no valido</div>')!!}
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label>Precio</label>
                    <input type="number" name="precio" class="form-control {!! $errors->first('precio','is-invalid')!!}" min="0" placeholder="Precio"  value="{{$paquete[0]->precio}}">
                    {!! $errors->first('precio','<div class="invalid-feedback">Precio no valido</div>')!!}
                </div>
            </div>
        </div>
        <div class="row">
          <div class="col-4">
            <a href="{{route('listasfranquicia',$idFranquicia)}}" class="btn btn-outline-success btn-block">@lang('mensajes.volveradminfranquicia')</a>
          </div>
          <div class="col-8">
            <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">@lang('mensajes.actualizar')</button>
          </div>
        </div>
    </form>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
