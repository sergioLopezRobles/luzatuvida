@extends('layouts.app')
@section('titulo','Lista Asistencia'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
  <div class="contenedor">
  <div class="row">
      <div class="col-9">
          <h3>Control de asistencia</h3>
      </div>
      <div class="col-3">
          <div class="row" style="display: flex; flex-direction: column; justify-items: center;">
              <h3 style="text-align: center;">{{$franquiciaPoliza[0]->ciudad}}</h3>
              <a class="btn btn-outline-success btn-block" href="{{route('asistenciaIndividual',[$idFranquicia,$idPoliza])}}">CAPTURA INDIVIDUAL</a>
          </div>
      </div>
  </div>
  <form action="{{route('registrarAsistenciaTabla',[$idFranquicia,$idPoliza])}}" enctype="multipart/form-data"
        method="POST" onsubmit="btnSubmit.disabled = true;">
      <div class="row">
          @csrf
          <div class="col-3">
              <div class="form-group">
                  <label for="">Usuarios</label>
                  <select class="custom-select {!! $errors->first('usuario','is-invalid')!!}"
                          name="usuario">
                      <option selected value="nada">Seleccionar</option>
                      <option value="0">TODOS</option>
                      @foreach($usuarios as $usuario)
                          <option value="{{$usuario->id}}">{{$usuario->name}} - {{$usuario->rol}} </option>
                      @endforeach
                  </select>
                  {!! $errors->first('usuario','<div class="invalid-feedback">Elegir un opto/asistente, campo
                      obligatorio </div>')!!}
              </div>
          </div>
          <div class="col-3">
              <div class="form-group">
                  <label for="">Asistencia</label>
                  <select class="custom-select {!! $errors->first('asistencia','is-invalid')!!}"
                          name="asistencia">
                      <option selected value="nada">Seleccionar</option>
                      <option value="0">Falta</option>
                      <option value="1">Asistencia</option>
                      <option value="2">Retardo</option>
                  </select>
                  {!! $errors->first('asistencia','<div class="invalid-feedback">Campo
                      obligatorio </div>')!!}
              </div>
          </div>
          <div class="col-2">
              <div class="form-group">
                  <label for="">Tipo de asistencia</label>
                  <select class="custom-select {!! $errors->first('asistenciaTipo','is-invalid')!!}" name="asistenciaTipo" id="asistenciaTipo">
                      <option selected value="">Seleccionar</option>
                      <option value="0">Entrada</option>
                      <option value="1">Salida</option>
                  </select>
                  {!! $errors->first('asistenciaTipo','<div class="invalid-feedback">Campo obligatorio </div>')!!}
              </div>
          </div>
          <div class="col-3">
              <label>&nbsp;</label>
              <div class="form-group">
                  <button  class="btn btn-primary" name="btnSubmit" type="submit">APLICAR</button>
              </div>
          </div>
      </div>
  </form>
  <table id="tablaFranquicias" class="table table-bordered">
    <thead>
      <tr>
        <th  style =" text-align:center;" scope="col">NOMBRE</th>
        <th  style =" text-align:center;" scope="col">ROL</th>
        <th  style =" text-align:center;" scope="col">ASISTENCIA</th>
        <th  style =" text-align:center;" scope="col">REGISTRO DE ENTRADA</th>
        <th  style =" text-align:center;" scope="col">REGISTRO DE SALIDA</th>
      </tr>
    </thead>
    <tbody>
        @foreach( $listaAsistencia as $usuario)
          <tr>
            <td align='center'>{{$usuario->name}}</td>
              @if($usuario->rol != "COBRANZA")
                  <td align='center'>{{$usuario->rol}}</td>
              @else
                  <td align='center'>{{$usuario->rol . ($usuario->supervisorcobranza == 0 ? "" : " (SUPERVISOR)")}}</td>
              @endif
              @if($usuario->id_tipoasistencia == 0 || $usuario->id_tipoasistencia == null)
                  <td align='center' colspan="">F</td>
              @endif
              @if($usuario->id_tipoasistencia == 1)
                  <td align='center' colspan="">A</td>
              @endif
              @if($usuario->id_tipoasistencia == 2)
                  <td align='center' colspan="">R</td>
              @endif
            <td align='center'>@if($usuario->registroentrada != null) {{\Carbon\Carbon::parse($usuario->registroentrada)->format("Y-m-d H:i:s")}} @endif</td>
            <td align='center'>@if($usuario->registrosalida != null) {{\Carbon\Carbon::parse($usuario->registrosalida)->format("Y-m-d H:i:s")}} @endif</td>
          </tr>
        @endforeach
    </tbody>
  </table>
  </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
