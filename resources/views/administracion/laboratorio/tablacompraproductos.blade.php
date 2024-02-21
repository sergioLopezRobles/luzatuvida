@extends('layouts.app')
@section('titulo','Control de armazones'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2>Control de armazones</h2>

    <div id="accordion">
        <div class="card">
            <div class="card-header" id="headingOne">
                <h5 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne"
                            aria-expanded="true"
                            aria-controls="collapseOne">
                        Filtros
                    </button>
                </h5>
            </div>
            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body">
                    <form action="{{route('filtrarlistaproductoslaboratorio')}}"
                          enctype="multipart/form-data" method="POST"
                          onsubmit="btnSubmit.disabled = true;">
                        @csrf
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label>Fecha inicial</label>
                                <input type="date" name="fechaInicio" id="fechaInicio" class="form-control {!! $errors->first('fechaInicio','is-invalid')!!}"
                                       @isset($fechaInicio) value = "{{$fechaInicio}}" @endisset>
                                @if($errors->has('fechaInicio'))
                                    <div class="invalid-feedback">{{$errors->first('fechaInicio')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label>Fecha final</label>
                                <input type="date" name="fechaFinal" id="fechaFinal" class="form-control {!! $errors->first('fechaFinal','is-invalid')!!}"
                                       @isset($fechaFinal) value = "{{$fechaFinal}}" @endisset>
                                @if($errors->has('fechaFinal'))
                                    <div class="invalid-feedback">{{$errors->first('fechaFinal')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col">
                            <button class="btn btn-outline-success" name="btnSubmit"
                                    type="submit">Aplicar
                            </button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <table id="tablaHistorialC" class="table-bordered table-striped table-sm" style="margin-top: 20px; width: 100%; overflow-x: auto;">
        <thead>
        <tr>
            <th style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">CONTRATO</th>
            <th style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">SUCURSAL</th>
            <th style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">USUARIO</th>
            <th style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">MENSAJE</th>
            <th style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">FECHA</th>
        </tr>
        </thead>
        <tbody>
        @if(sizeof($listaProductos)>0)
            @foreach($listaProductos as $producto)
                <tr>
                    <td style=" text-align:center; font-size: 11px; padding: 5px; white-space: nowrap;" align='center'>{{$producto->id_contrato}}</td>
                    <td style=" text-align:center; font-size: 11px; padding: 5px; white-space: nowrap;" align='center'>{{$producto->sucursal}}</td>
                    <td align='center' style=" text-align:center; font-size: 11px; padding: 5px; white-space: nowrap;">{{$producto->usuariocreacion}}</td>
                    <td align='center' style=" text-align:center; font-size: 11px; padding: 5px;">{{$producto->cambios}}</td>
                    <td align='center' style=" text-align:center; font-size: 11px; padding: 5px; white-space: nowrap;">{{$producto->created_at}}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td align='center' style=" text-align:center; font-size: 11px; padding: 5px; white-space: nowrap;" colspan="5">Sin registros</td>
            </tr>
        @endif
        </tbody>
    </table>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
