@extends('layouts.app')
@section('titulo','Desarrollo'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <div class="row">
        <div class="col-8">
            <h2 style="text-align: left; color: #0AA09E">@lang('mensajes.mensajedispositivos')</h2>
        </div>
        <div class="col-2">
            <a type="button" class="btn btn-outline-success btn-block" href="{{route('configuracion', $idFranquicia)}}">Configuración</a>
        </div>
        <div class="col-2">
            <a type="button" class="btn btn-outline-success btn-block" href="{{route('dispositivonuevo', $idFranquicia)}}">Nuevo dispositivo</a>
        </div>
    </div>
    <table id="tablaContratos" class="table-bordered table-striped table-sm" style="width: 100%;">
        <thead>
            <tr>
                <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">ESTATUS</th>
                <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">IDENTIFICADOR</th>
                <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">TITULO</th>
                <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">DESCRIPCION</th>
                <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">VERSION</th>
                <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">APLICACION</th>
                <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">ACTIVAR/DESACTIVAR</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dispositivos as $dispositivo)
            <tr>
                @if($dispositivo->estatus  == 1)
                    <td align='center'><i class='fas fa-check' style="color:#9be09c;font-size:20px;"></i></td>
                @else
                    <td align='center' ><i class='fas fa-check' style="color:#ffaca6;font-size:20px;"></i></td>
                @endif
                <td style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;">{{$dispositivo->id}}</td>
                <td style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;">{{$dispositivo->titulo}}</td>
                <td style="text-align:center; font-size: 11px; padding: 5px;">{{$dispositivo->descripcion}}</td>
                <td style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;">{{$dispositivo->version}}</td>
                <td align='center' style="padding: 5px;">
                    <a href="{{$dispositivo->apk}}" class="btn btn-outline-primary btn-sm" role="button" aria-pressed="true" style="font-size: 10px; padding: 5px;">DESCARGAR APLICACION</a></td>
                @if($dispositivo->estatus  == 1 )
                    <td align='center'> <a href="{{route('dispositivoestatus',[$idFranquicia, $dispositivo->id,$dispositivo->estatus])}}" class="btn btn-outline-danger btn-sm">DESACTIVAR</a></td>
                @else
                    <td align='center'> <a href="{{route('dispositivoestatus',[$idFranquicia, $dispositivo->id,$dispositivo->estatus])}}" class="btn btn-outline-primary btn-sm">ACTIVAR</a></td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
