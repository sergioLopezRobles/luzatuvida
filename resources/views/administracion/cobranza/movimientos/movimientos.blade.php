@extends('layouts.app')
@section('titulo','Abonos de cobranza'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2>Abonos de cobranza</h2>
    <div class="row">
        <input type="hidden" id="idFranquicia" value="{{$franquicia[0]->idFranquicia}}">
        <div class="col-3">
            <label for="">Usuario</label>
            <select class="custom-select {!! $errors->first('usuario','is-invalid')!!}" id="usuario" name="usuario">
                @if(count($usuarios) > 0)
                    <option selected value="">Seleccionar</option>
                    @foreach($usuarios as $usuario)
                        <option value="{{$usuario->id}}">{{$usuario->zona}} - {{$usuario->name}} - Cobrador @if($usuario->supervisorcobranza == 0) Normal @else Supervisor @endif</option>
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
                <input type="date" name="inicio" id="inicio" class="form-control {!! $errors->first('inicio','is-invalid')!!}" value="{{$fechaInicial}}">
                @if($errors->has('inicio'))
                    <div class="invalid-feedback">{{$errors->first('inicio')}}</div>
                @endif
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Fecha final</label>
                <input type="date" name="fin" id="fin" class="form-control {!! $errors->first('fin','is-invalid')!!}" value="{{$fechaFinal}}">
                @if($errors->has('fin'))
                    <div class="invalid-feedback">{{$errors->first('fin')}}</div>
                @endif
            </div>
        </div>
        <div class="col-3">
            <label for="">Opciones</label>
            <select class="custom-select cargarTabla" id="opcion" name="opcion">
                <option value="0">Movimientos</option>
                @if($idUsuario != null)
                    <option value="1">Corte Actual</option>
                @endif
            </select>
        </div>
        <div class="col-1">
                <button class="btn btn-outline-success" id="btnFiltrosMovimientosCobranza">Aplicar </button>
        </div>
        <div class="col-1" id="spCargando">
            <div class="d-flex justify-content-center">
                <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 25px;" role="status">
                    <span class="visually-hidden"></span>
                </div>
            </div>
        </div>
    </div>
        <div class="row">
            <div class="col-12" style="color: #dc3545">
                <b>Nota: Solo podrás hacer un corte por día por cada cobrador</b>
            </div>
        </div>
        <br>
    <div class="form-group" id="listamovimientoscobranza">
        <h4>Abonos</h4>
        <table  class="table-bordered table-striped table-general table-sm">
            <thead>
            <tr>
                <th  style =" text-align:center; border: #707070 solid 1px;" scope="col">NOMBRE</th>
                <th  style =" text-align:center; border: #707070 solid 1px;" scope="col">CONTRATO</th>
                <th  style =" text-align:center; border: #707070 solid 1px;" scope="col">NOMBRE CLIENTE</th>
                <th  style =" text-align:center; border: #707070 solid 1px;" scope="col">ABONO</th>
                <th  style =" text-align:center; border: #707070 solid 1px;" scope="col">FORMA DE PAGO</th>
                <th  style =" text-align:center; border: #707070 solid 1px;" scope="col">FOLIO</th>
                <th  style =" text-align:center; border: #707070 solid 1px;" scope="col">TIPO DE PAGO</th>
                <th  style =" text-align:center; border: #707070 solid 1px;" scope="col">FECHA</th>
                <th  style =" text-align:center; border: #707070 solid 1px;" scope="col">LINK</th>
            </tr>
            </thead>
            <tbody>
                <td align='center' style="width: 20%; border: #707070 solid 1px;" colspan="10">Sin registros</td>
            </tbody>
        </table>
    </div>


@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
