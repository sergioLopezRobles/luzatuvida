@extends('layouts.app')
@section('titulo','Reporte cuentas activas'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    @include('parciales.notificaciones')
    <h2 style="text-align: left; color: #0AA09E">Cuentas Fisicas</h2>
    <form action="{{route('cuentasfisicasarchivo')}}"
          enctype="multipart/form-data" method="POST"
          onsubmit="btnSubmit.disabled = true;">
        @csrf
        <div class="row">
            <div class="col-2">
                <label for="zona">Zonas</label>
                <select name="zona"
                        class="form-control">
                    @if(count($zonas) > 0)
                        <option value="" selected>Seleccionar..</option>
                        @foreach($zonas as $zona)
                            <option
                                value="{{$zona->id}}" >{{$zona->zona}}@if(Auth::user()->rol_id == 7) - {{$zona->ciudad}}  @endif</option>
                        @endforeach
                    @else
                        <option selected>Sin registros</option>
                    @endif
                </select>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Archivo excel</label>
                    <input type="file" name="archivo" class="form-control-file {!! $errors->first('archivo','is-invalid')!!}"  accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" >
                    {!! $errors->first('archivo','<div class="invalid-feedback">Archivo no valido.</div>')!!}
                </div>
            </div>
            <div class="col-2">
                <button class="btn btn-outline-success btn-ca" name="btnSubmit"
                        type="submit">Aplicar
                </button>
            </div>
        </div>
    </form>
    @if($contratosPagina != null)
        <button type="button" class="btn btn-primary" style="margin-bottom: 10px;">
            Pagina <span class="badge bg-secondary">{{ count($contratosPagina['id'])}}</span>
        </button>
    @endif
    @if($contratosArchivo != null)
        <button type="button" class="btn btn-primary" style="margin-bottom: 10px;">
            Archivo <span class="badge bg-secondary">{{ count($contratosArchivo['id'])}}</span>
        </button>
    @endif
    <table id="tablaContratos" class="table table-bordered table-striped" style="text-align: left; position: relative; border-collapse: collapse;">
        <thead>
            <tr>
                <th colspan="4" style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">CONTRATOS QUE ESTAN EN LA PAGINA PERO EN EL ARCHIVO NO, CON UN ESTATUS DE ENVIADO,ENTREGADO,ATRASADO</th>
            </tr>
            <tr>
                <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">CODIGO</th>
                <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">ESTATUS</th>
                <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">ZONA</th>
                <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">SUCURSAL</th>
            </tr>
        </thead>
        <tbody>
            @if($contratosPagina != null)
                {{ info(count($contratosPagina['id']))}}
                @for($i = 0;$i<=count($contratosPagina['id'])-1;$i++ )
                    <tr>
                        <td align='center' style="font-size: 10px;">{{$contratosPagina['id'][$i]}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratosPagina['estatus'][$i]}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratosPagina['zona'][$i]}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratosPagina['sucursal'][$i]}}</td>
                    </tr>
                @endfor
            @endif
            <tr>
                <td colspan="3" style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">CONTRATOS QUE ESTAN EN EL ARCHIVO PERO NO ESTAN CON ESTATUS DE ENVIADO,ENTREGADO,ATRASADO O NO TIENEN LA MISMA ZONA.</td>
            </tr>
            @if($contratosArchivo != null)
                {{ info(count($contratosArchivo['id']))}}
                @for($i = 0;$i<=count($contratosArchivo['id'])-1;$i++ )
                    <tr>
                        <td align='center' style="font-size: 10px;">{{$contratosArchivo['id'][$i]}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratosArchivo['estatus'][$i]}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratosArchivo['zona'][$i]}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratosArchivo['sucursal'][$i]}}</td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
@endsection
