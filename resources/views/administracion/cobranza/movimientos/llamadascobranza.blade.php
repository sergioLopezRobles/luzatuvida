@extends('layouts.app')
@section('titulo','Contratos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2 style="text-align: left; color: #0AA09E">Contratos para llamar</h2>
    <input type="hidden" id="idFranquicia" value="{{$idFranquicia}}">
    <div class="row">
        <div class="col-4">
            <label for="">Usuario</label>
            <select class="custom-select {!! $errors->first('usuario','is-invalid')!!} cargarTabla" id="usuarioCobranza" name="usuario">
                @if(count($usuarios) > 0)
                    <option selected value="">Seleccionar</option>
                    @foreach($usuarios as $usuario)
                        <option value="{{$usuario->id}}">{{$usuario->zona}} - {{$usuario->name}}</option>
                    @endforeach
                @else
                    <option selected>No se encontro ningun usuario</option>
                @endif
            </select>
            {!! $errors->first('zona','<div class="invalid-feedback">Elegir una zona, campo obligatorio </div>')!!}
        </div>
        <div class="col-4">
            <label for="">Opciones</label>
            <select class="custom-select cargarTabla" id="opcionCorte" name="opcion">
                <option value="">Seleccionar corte</option>
            </select>
        </div>
        <div class="col-2" id="spCargandoLlamadas">
            <div class="d-flex justify-content-center">
                <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 25px;" role="status">
                    <span class="visually-hidden"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group" id="listallamadascobranza">

    </div>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
