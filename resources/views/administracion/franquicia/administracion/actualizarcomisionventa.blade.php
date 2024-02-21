@extends('layouts.app')
@section('titulo','Actualizar comision venta'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>Editar comisión {{$comisionventa[0]->comision}} @switch($comisionventa[0]->usuario) @case(0) asistente @break @case(1) optometrista @break @endswitch</h2>
        <form  action="{{route('actualizarcomisionventa',[$idFranquicia,$comisionventa[0]->indice])}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>Contratos</label>
                        <input type="number" name="totalcontratos" id="totalcontratos" class="form-control" min="0" placeholder="Contratos"
                               value="{{$comisionventa[0]->totalcontratos}}" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>Pago @switch($comisionventa[0]->usuario) @case(0) $ @break @case(1) % @break @endswitch</label>
                        <input type="number" name="valor" id="valor" class="form-control" min="0" placeholder="Pago"
                               value="{{$comisionventa[0]->valor}}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <a href="{{route('listasfranquicia',$idFranquicia)}}" class="btn btn-outline-success btn-block">@lang('mensajes.volveradminfranquicia')</a>
                </div>
                <div class="col-8">
                    <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">Actualizar comisión</button>
                </div>
            </div>
        </form>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
