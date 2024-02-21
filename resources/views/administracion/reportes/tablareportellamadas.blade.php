@extends('layouts.app')
@section('titulo','Reporte llamadas'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2 style="text-align: left; color: #0AA09E">Reporte llamadas</h2>
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
                    <div class="row">
                         @if(Auth::user()->rol_id == 7)
                            <div class="col-4">
                                <label for="">Usuario</label>
                                <select class="custom-select" id="usuario" name="usuario">
                                    @if(count($usuarios) > 0)
                                        <option selected value="">Seleccionar</option>
                                        @foreach($usuarios as $usuario)
                                            <option value="{{$usuario->id}}">{{$usuario->zona}} - {{$usuario->ciudad}} - {{$usuario->name}}</option>
                                        @endforeach
                                    @else
                                        <option selected>No se encontro ningun usuario</option>
                                    @endif
                                </select>
                            </div>

                             @else
                        <div class="col-4">
                            <label for="">Usuario</label>
                            <select class="custom-select" id="usuario" name="usuario">
                                @if(count($usuarios) > 0)
                                    <option selected value="">Seleccionar</option>
                                    @foreach($usuarios as $usuario)
                                        <option value="{{$usuario->id}}">{{$usuario->zona}} - {{$usuario->name}}</option>
                                    @endforeach
                                @else
                                    <option selected>No se encontro ningun usuario</option>
                                @endif
                            </select>
                        </div>
                        @endif
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
                                <input type="date" name="fechaFin" id="fechaFin" class="form-control {!! $errors->first('fechaFinal','is-invalid')!!}"
                                       @isset($fechaFinal) value = "{{$fechaFinal}}" @endisset>
                                @if($errors->has('fechaFinal'))
                                    <div class="invalid-feedback">{{$errors->first('fechaFinal')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-2" id="spCargando">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 30px;" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="tblreportellamadas">
        <table class="table-bordered table-striped table-sm" style="margin-top: 20px; width: 100%; overflow-x: auto;">
            <thead>
            <tr>
                <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" >COBRADOR</th>
                <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);">CONTRATO</th>
                <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" >CLIENTE</th>
                <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"  colspan="4">MENSAJE</th>
            </tr>
            </thead>
            <tbody>
                <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;"  colspan="7">Sin registros</td>
            </tbody>
        </table>
    </div>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
