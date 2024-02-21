@extends('layouts.app')
@section('titulo','Poliza'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <input type="hidden" id="idFranquicia" value="{{$idFranquicia}}">
    <input type="hidden" id="opcion" value="{{$opcion}}">
    <input type="hidden" id="idPoliza" value="{{$idPoliza}}">
    <div class="row">
        <div class="col-2" style="margin-left:80%;">
            <div class="row" style="display: flex; flex-direction: column; justify-items: center;">
                <h3 style="text-align: center;">{{$nombrefranquicia}}</h3>
                <input type="text" name="fechahoy" class="form-control" style="text-align: center"
                       readonly value="{{$fecha}}">
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-2" style="margin-left:80%;">
            @if($poliza[0]->estatus == 0)
                <form action="{{route('terminarPoliza',[$idFranquicia,$idPoliza])}}" enctype="multipart/form-data"
                      method="POST">
                    @csrf
                    <div class="form-group">
                        <button class="btn btn-outline-success btn-block" name="btnSubmit" id="btnTerminarPoliza" type="submit">TERMINAR
                            POLIZA
                        </button>
                    </div>
                </form>
            @endif
            @if($poliza[0]->estatus == 2)
                <div class="col-12">
                    @if(Auth::user()->rol_id == 7)
                        <form action="{{route('terminarPoliza',[$idFranquicia,$idPoliza])}}" enctype="multipart/form-data"
                              method="POST" onsubmit="btnSubmit.disabled = true;">
                            @csrf
                            <div class="form-group">
                                <label for=""></label>
                                <select class="custom-select {!! $errors->first('entregar','is-invalid')!!}"
                                        name="entregar">
                                    <option value="1">AUTORIZAR POLIZA</option>
                                    <option value="2">REGRESAR POLIZA</option>
                                </select>
                                {!! $errors->first('entregar','<div class="invalid-feedback">Elegir una zona, campo
                                    obligatorio </div>')!!}
                            </div>
                            <div class="form-group">
                                <button class="btn btn-outline-success btn-block" name="btnSubmit" id="btnAplicarEstadoPoliza" type="submit">APLICAR
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="form-group">
                            <select class="custom-select" disabled>
                                <option value="1">TERMINADA</option>
                            </select>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-2" style="margin-left:80%;">
            <form action="{{route('tablaAsistencia',[$idFranquicia,$idPoliza])}}" enctype="multipart/form-data"
                  method="GET" target="_blank">
                @csrf
                <div class="form-group">
                    <button class="btn btn-outline-success btn-block" name="btnSubmit" id="btnTablaAsistencia" type="submit">TABLA
                        DE ASISTENCIA
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-2" style="margin-left:80%;" id="spCargando">
            <div class="d-flex justify-content-end">
                <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 25px;" role="status">
                    <span class="visually-hidden"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group" id="contenidopoliza">

    </div>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
