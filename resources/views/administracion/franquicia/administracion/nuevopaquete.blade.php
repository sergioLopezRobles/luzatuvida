@extends('layouts.app')
@section('titulo','Nuevo paquete'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2>@lang('mensajes.mensajenuevopaquete')</h2>
    <form id="frmFranquiciaNueva" action="{{route('paquetecrear',$idFranquicia)}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
      @csrf
      <div class="row">
        <div class="col-6">
          <div class="form-group">
            <label>Paquete</label>
            <input type="text" name="paquete" class="form-control {!! $errors->first('paquete','is-invalid')!!}"  placeholder="Nombre del paquete" value="{{ old('paquete') }}">
            {!! $errors->first('paquete','<div class="invalid-feedback">El nombre del paquete es obligatorio.</div>')!!}
          </div>
        </div>
        <div class="col-6">
          <div class="form-group">
            <label>Precio</label>
            <input type="number" name="precio" class="form-control {!! $errors->first('precio','is-invalid')!!}" min="0" placeholder="Precio del paquete" value="{{ old('precio') }}">
            {!! $errors->first('precio','<div class="invalid-feedback">El precio es obligatorio y/o un numero positivo.</div>')!!}
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-4">
            <a href="{{route('listasfranquicia',$idFranquicia)}}" class="btn btn-outline-success btn-block">@lang('mensajes.regresar')</a>
        </div>
        <div class="col">
          <button class="btn btn-outline-success btn-block"  name="btnSubmit" type="submit">@lang('mensajes.mensajecrearpaquete')</button>
        </div>
      </div>
    </form>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
