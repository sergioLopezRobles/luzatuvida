@extends('layouts.app')
@section('titulo','Contratos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">

    <h2>@lang('mensajes.mensajetitulotablalaboratorio')</h2>

    <form action="{{route('listagarantiasconfirmaciones')}}" enctype="multipart/form-data" method="GET">
        <div class="row">
            <div class="col-4">
                <input name="filtro" type="text" class="form-control" placeholder="Buscar..">
            </div>
            <div class="col-5">
                <button type="submit" class="btn btn-outline-success">Filtrar</button>
            </div>
        </div>
    </form>
    <table id="tablaContratos" class="table-bordered table-striped table-general table-sm" style="margin-top: 10px;">
        <thead>
        <tr>
            <th  style =" text-align:center;" scope="col">CONTRATO</th>
            <th  style =" text-align:center;" scope="col">NOMBRE CLIENTE</th>
            <th  style =" text-align:center;" scope="col">FECHA CREACION</th>
            <th  style =" text-align:center;" scope="col">VER</th>
        </tr>
        </thead>
        <tbody>
        @if(!is_null($contratosGaratias) && count($contratosGaratias)>0)
            @foreach($contratosGaratias as $contratoGarantia)
                <tr>
                    <td align='center'>{{$contratoGarantia->id}}</td>
                    <td align='center'>{{$contratoGarantia->nombre}}</td>
                    <td align='center'>{{\Carbon\Carbon::parse($contratoGarantia->created_at)->format('Y-m-d')}}</td>
                    <td align='center'> <a href="{{route('vercontratogarantiaconfirmaciones',[$contratoGarantia->id])}}" ><button type="button" class="btn btn-outline-success"><i
                                    class="fas fa-book-open btn-sm"></i></button></a></td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
    @if(!is_null($contratosGaratias) && count($contratosGaratias)>0)
        <div class="d-flex justify-content-center">
            {{$contratosGaratias->links('pagination::bootstrap-4')}}
        </div>
    @endif
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
