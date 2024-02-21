@extends('layouts.app')
@section('titulo','Dispositivo'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2>@lang('mensajes.mensajenuevodispositivo')</h2>
    <form id="frmFranquiciaNueva" action="{{route('dispositivocrear', $idFranquicia)}}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-2" hidden>
                <div class="form-group">
                    <input type="text" name="idDispositivo" class="form-control" placeholder="idDispositivo" value="{{$idDispositivo}}">
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Titulo</label>
                    <input type="text" name="titulo" class="form-control {!! $errors->first('titulo','is-invalid')!!}" placeholder="Titulo" value="{{ old('titulo') }}">
                    {!! $errors->first('titulo','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label>Descripcion</label>
                    <input type="text" name="descripcion" class="form-control {!! $errors->first('descripcion','is-invalid')!!}" placeholder="Descripcion" value="{{ old('descripcion') }}">
                    {!! $errors->first('descripcion','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Version</label>
                    <input type="text" name="version" class="form-control {!! $errors->first('version','is-invalid')!!}" placeholder="Version" value="{{ old('version') }}">
                    {!! $errors->first('version','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Archivo</label>
                    <input type="file" name="apk" class="form-control-file {!! $errors->first('apk','is-invalid')!!}">
                    {!! $errors->first('apk','<div class="invalid-feedback">Archivo incopatible con tipo de dispositivo.</div>')!!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label>Fecha para activar la aplicacion automaticamente</label>
                    <input type="date" name="fechaactivacion" class="form-control {!! $errors->first('fechaactivacion','is-invalid')!!}"  placeholder="Fecha" value="{{ old('fechaactivacion') }}">
                    @if($errors->has('fechaactivacion'))
                        <div class="invalid-feedback">{{$errors->first('fechaactivacion')}}</div>
                    @endif
                </div>
            </div>
            <div class="col-3">
                <label> ¿Activar la aplicacion ahora?</label>
                <div class="form-check">
                    <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1">
                    <label class="form-check-label" for="activo">Activar</label>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label> Tipo aplicación</label>
                    <select name="tipoDispositivoSeleccionado"
                            class="form-control {!! $errors->first('tipoDispositivoSeleccionado','is-invalid')!!}"
                            id="tipoDispositivoSeleccionado" value="{{ old('fechaactivacion') }}">
                        <option value="">Seleccionar tipo dispositivo</option>
                        <option value="0" selected>Aplicación móvil</option>
                        <option value="1">Aplicación escritorio</option>
                    </select>
                    @if($errors->has('tipoDispositivoSeleccionado'))
                        <div class="invalid-feedback">{{$errors->first('tipoDispositivoSeleccionado')}}</div>
                    @endif
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Identificador unico</label>
                    <input type="text" class="form-control"  value="{{$idDispositivo}}" readonly>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <a href="{{route('listafranquicia')}}" class="btn btn-outline-success btn-block">@lang('mensajes.regresar')</a>
            </div>
            <div class="col">
                <button class="btn btn-outline-success btn-block" type="submit">@lang('mensajes.mensajecreardispositivo')</button>
            </div>
        </div>
    </form>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
