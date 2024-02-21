@extends('layouts.app')
@section('titulo','Contratos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2>@lang('mensajes.mensajeNuevoMensaje')</h2>
    <form id="frmFranquiciaNueva" action="{{route('crearmensaje',$idFranquicia)}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>Descripcion</label>
                    <input type="text" name="descripcion" class="form-control {!! $errors->first('descripcion','is-invalid')!!}" placeholder="Descripcion" value="{{ old('descripcion') }}">
                    {!! $errors->first('descripcion','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-2">
                <div class="form-group">
                    <label>Fecha para que deje de aparecer el mensaje</label>
                    <input type="date" name="fecha" class="form-control {!! $errors->first('fecha','is-invalid')!!}"  placeholder="Fecha" value="{{ old('fecha') }}">
                    @if($errors->has('fecha'))
                        <div class="invalid-feedback">{{$errors->first('fecha')}}</div>
                    @endif
                </div>
            </div>
            <div class="col-2">
              <div class="form-group">
                <label>Numero de veces que pueden ver el mensaje</label>
                <input type="number" min="0" name="numero" class="form-control {!! $errors->first('numero','is-invalid')!!}"  placeholder="Numero">
                {!! $errors->first('numero','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
              </div>
            </div>
            @if(Auth::user()->rol_id == 7)
                <div class="col-2" style="padding-top: 60px;">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="cbTodasSucursales" id="cbTodasSucursales" value="1">
                        <label class="custom-control-label" for="cbTodasSucursales">Mensaje para todas las sucursales.</label>
                    </div>
                </div>
            @endif
        </div>
        <div class="row">
            <div class="col-4">
                <a href="{{route('listasfranquicia',$idFranquicia)}}" class="btn btn-outline-success btn-block">@lang('mensajes.regresar')</a>
            </div>
            <div class="col">
                <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">@lang('mensajes.mensajeCrearMensaje')</button>
            </div>
        </div>
    </form>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
