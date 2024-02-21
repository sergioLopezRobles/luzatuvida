@extends('layouts.app')
@section('titulo','Movimientos de ventas'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2>Movimientos de ventas</h2>
    <form action="{{route('filtrarventasmovimientos',[$franquicia[0]->idFranquicia])}}" enctype="multipart/form-data"
          method="GET">
        <div class="row">
            <div class="col-2">
                <label for="">Usuario</label>
                <select class="custom-select {!! $errors->first('usuario','is-invalid')!!}" name="usuario">
                    @if(count($usuarios) > 0)
                        <option selected value="">Seleccionar</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{$usuario->id}}"
                                    @isset($idUsuario[0]->id) @if($idUsuario[0]->id == $usuario->id) selected @endif @endisset>{{$usuario->name}}</option>
                        @endforeach
                    @else
                        <option selected>No se encontro ningun usuario</option>
                    @endif
                </select>
                {!! $errors->first('zona','<div class="invalid-feedback">Elegir una zona, campo obligatorio </div>')!!}
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Fecha inicial</label>
                    <input type="date" name="inicio" class="form-control {!! $errors->first('inicio','is-invalid')!!}"
                           @isset($fechaIni) value="{{$fechaIni}}" @endisset>
                    @if($errors->has('inicio'))
                        <div class="invalid-feedback">{{$errors->first('inicio')}}</div>
                    @endif
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Fecha final</label>
                    <input type="date" name="fin" class="form-control {!! $errors->first('fin','is-invalid')!!}"
                           @isset($fechaFin) value="{{$fechaFin}}" @endisset>
                    @if($errors->has('fin'))
                        <div class="invalid-feedback">{{$errors->first('fin')}}</div>
                    @endif
                </div>
            </div>
            <div class="col-1">
                <button class="btn btn-outline-success btn-block"
                        type="submit">@lang('mensajes.mensajefiltrar')</button>
            </div>
        </div>
    </form>
    <h4>Contratos</h4>
    <div class="row">
        <div class="col-2">
            <div class="form-group">
                <label>Cancelados</label>
                <input type="text" name="estado" class="form-control" readonly
                       value="{{$cancelados}}">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Rechazados</label>
                <input type="text" name="estado" class="form-control" readonly
                       value="{{$rechazados}}">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Aprobados</label>
                <input type="text" name="estado" class="form-control" readonly
                       value="{{$aprobados}}">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Total</label>
                <input type="text" name="estado" class="form-control" readonly value="{{$todos}}">
            </div>
        </div>
        @if($rolUsuario != 13)
            <div class="col-2">
                <div class="form-group">
                    <label>Gotas</label>
                    <input type="text" name="estado" class="form-control" readonly
                           value="{{$gotas}}">
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Polizas</label>
                    <input type="text" name="estado" class="form-control" readonly
                           value="{{$polizas}}">
                </div>
            </div>
        @endif
    </div>
    @if($mostrarSeccionFecha)
        <h4>Contratos en un lapso de tiempo </h4>
        <div class="row">
            <div class="col-2">
                <div class="form-group">
                    <label>Cancelados</label>
                    <input type="text" name="estado" class="form-control" readonly
                           value="{{$canceladosFecha}}">
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Rechazados</label>
                    <input type="text" name="estado" class="form-control" readonly
                           value="{{$rechazadosFecha}}">
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Aprobados</label>
                    <input type="text" name="estado" class="form-control" readonly
                           value="{{$aprobadosFecha}}">
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Total</label>
                    <input type="text" name="estado" class="form-control" readonly value="{{$todosFecha}}">
                </div>
            </div>
            @if($rolUsuario != 13)
                <div class="col-2">
                    <div class="form-group">
                        <label>Gotas</label>
                        <input type="text" name="estado" class="form-control" readonly
                               value="{{$gotasFecha}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Polizas</label>
                        <input type="text" name="estado" class="form-control" readonly
                               value="{{$polizasFecha}}">
                    </div>
                </div>
            @endif
        </div>
    @endif
    <table class="table-bordered table-striped table-general table-sm">
        <thead>
        <tr>
            <th style=" text-align:center;" scope="col">NOMBRE</th>
            <th style=" text-align:center;" scope="col">OPTOMETRISTA</th>
            <th style=" text-align:center;" scope="col">CONTRATO</th>
            <th style=" text-align:center;" scope="col">DESCRIPCION</th>
            <th style=" text-align:center;" scope="col">FECHA</th>
            <th style=" text-align:center;" scope="col">LINK</th>
        </tr>
        </thead>
        <tbody>
        @foreach($movimientos as $movimiento)
            <tr>
                <td align='center'>{{$movimiento->name}}</td>
                <td align='center'>{{$movimiento->optometrista}}</td>
                <td align='center'>{{$movimiento->id_contrato}}</td>
                <td align='center'>{{$movimiento->cambios}}</td>
                <td align='center'>{{$movimiento->created_at}}</td>
                <td align='center'><a href="{{route('vercontrato',[$idFranquicia,$movimiento->id_contrato])}}"
                                      class="btn btn-primary btn-sm">ABRIR</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
