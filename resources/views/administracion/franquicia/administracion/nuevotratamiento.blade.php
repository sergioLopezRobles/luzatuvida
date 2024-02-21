@extends('layouts.app')
@section('titulo','Nuevo tratamiento'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2>@lang('mensajes.mensajenuevotratamiento')</h2>
    <form id="frmFranquiciaNueva" action="{{route('tratamientocrear',$idFranquicia)}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
      @csrf
      <div class="row">
        <div class="col-6">
          <div class="form-group">
            <label>Tratamiento</label>
            <input type="text" name="tratamiento" class="form-control {!! $errors->first('tratamiento','is-invalid')!!}"  placeholder="Nombre del tratamiento" value="{{ old('tratamiento') }}">
            {!! $errors->first('tratamiento','<div class="invalid-feedback">El nombre del tratamiento es obligatorio.</div>')!!}
          </div>
        </div>
        <div class="col-6">
          <div class="form-group">
            <label>Precio</label>
            <input type="number" name="precio" class="form-control {!! $errors->first('precio','is-invalid')!!}" min="0" placeholder="Precio del tratamiento" value="{{ old('precio') }}">
            {!! $errors->first('precio','<div class="invalid-feedback">El precio es obligatorio y/o un numero positivo.</div>')!!}
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-4">
            <a href="{{route('listasfranquicia',$idFranquicia)}}" class="btn btn-outline-success btn-block">@lang('mensajes.regresar')</a>
        </div>
        <div class="col">
          <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">@lang('mensajes.mensajecreartratamiento')</button>
        </div>
      </div>
    </form>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
