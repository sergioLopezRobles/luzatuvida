@extends('layouts.app')
@section('titulo','Insumos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2>@lang('mensajes.mensajeinsumos')</h2>
    <form id="frmFranquiciaNueva" action="{{route('actualizarinsumos')}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
      @csrf
      <div class="row">
        <div class="col-2">
          <div class="form-group">
            <label>Precio Mica</label>
            <input type="number" name="preciom" class="form-control {!! $errors->first('preciom','is-invalid')!!}" min="0" placeholder="Precio" value="{{$insumos[0]->preciom}}">
            {!! $errors->first('preciom','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
          </div>
        </div>
        <div class="col-2">
          <div class="form-group">
            <label>Precio Armazon</label>
            <input type="number" name="precioa" class="form-control {!! $errors->first('precioa','is-invalid')!!}" min="0" placeholder="Precio" value="{{$insumos[0]->precioa}}">
            {!! $errors->first('precioa','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
          </div>
        </div>
        <div class="col-2">
          <div class="form-group">
            <label>Precio Bicce</label>
            <input type="number" name="preciob" class="form-control {!! $errors->first('preciob','is-invalid')!!}" min="0" placeholder="Precio" value="{{$insumos[0]->preciob}}">
            {!! $errors->first('preciob','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
          </div>
        </div>
        <div class="col-2">
          <div class="form-group">
            <label>Precio T</label>
            <input type="number" name="preciot" class="form-control {!! $errors->first('preciot','is-invalid')!!}" min="0" placeholder="Precio" value="{{$insumos[0]->preciot}}">
            {!! $errors->first('preciot','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
          </div>
        </div>
        <div class="col-2">
          <div class="form-group">
            <label>Precio Estuche</label>
            <input type="number" name="precioe" class="form-control {!! $errors->first('precioe','is-invalid')!!}" min="0" placeholder="Precio" value="{{$insumos[0]->precioe}}">
            {!! $errors->first('precioe','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-4">
            <a href="{{route('listafranquicia')}}" class="btn btn-outline-success btn-block">@lang('mensajes.regresar')</a>
        </div>
        <div class="col">
          <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">@lang('mensajes.actualizar')</button>
        </div>
      </div>
    </form>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
