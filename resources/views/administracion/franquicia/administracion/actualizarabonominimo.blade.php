@extends('layouts.app')
@section('titulo','Actualizar abono minimo'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>Formulario para editar abono minimo </h2>
        <form  action="{{route('actualizarabonominimo',[$idFranquicia,$abonoMinimo[0]->pago])}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>Tipo pago</label>
                        @switch($abonoMinimo[0]->pago)
                            @case(1)
                            <input type="text" name="paquete" class="form-control" value="SEMANAL" readonly>
                            @break
                            @case(2)
                            <input type="text" name="paquete" class="form-control" value="QUINCENAL" readonly>
                            @break
                            @case(4)
                            <input type="text" name="paquete" class="form-control" value="MENSUAL" readonly>
                            @break
                        @endswitch
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>Abono minimo</label>
                        <input type="number" name="abonominimo" id="abonominimo" class="form-control {!! $errors->first('abonominimo','is-invalid')!!}" min="0" placeholder="Abono minimo"
                               value="{{$abonoMinimo[0]->abonominimo}}" required>
                        {!! $errors->first('abonominimo','<div class="invalid-feedback">Cantidad no valida.</div>')!!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <a href="{{route('listasfranquicia',$idFranquicia)}}" class="btn btn-outline-success btn-block">@lang('mensajes.volveradminfranquicia')</a>
                </div>
                <div class="col-8">
                    <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">Actualizar abono minimo</button>
                </div>
            </div>
        </form>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
