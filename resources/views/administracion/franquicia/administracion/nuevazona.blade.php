@extends('layouts.app')
@section('titulo','Actualizar tratamiento'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>Nueva zona</h2>
        <form  action="{{route('zonacrear',$idFranquicia)}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>Nombre de zona</label>
                        <input type="text" name="nombreZona" class="form-control {!! $errors->first('nombreZona','is-invalid')!!}"  placeholder="NOMBRE">
                        {!! $errors->first('nombreZona','<div class="invalid-feedback">Nombre de zona obligatorio.</div>')!!}
                    </div>
                </div>
                <div class="col-6">
                    <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit" style="margin-top: 30px;">Crear nueva zona</button>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <a href="{{route('listasfranquicia',$idFranquicia)}}" class="btn btn-outline-success btn-block">@lang('mensajes.volveradminfranquicia')</a>
                </div>
            </div>
        </form>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
