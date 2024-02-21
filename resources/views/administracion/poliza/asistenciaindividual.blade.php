@extends('layouts.app')
@section('titulo','Asistencia Individual'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
  <div class="contenedor">
    <h1 class="text-center">"REGISTRO DE ASISTENCIA PARA SUCURSAL DE {{$franquicia[0]->ciudad}}"</h1>
  @if(!isset($usuario))
      <div class="text-center">
          <img src="{{asset('imagenes\general\asistencia\avatarasistencia.png')}}"   width="250" height="250" style="border-radius: 50%">
      </div>
  @else
      <div class="text-center">
          <img src="{{asset($usuario[0]->foto)}}"   width="210" height="280" style="border-radius: 10%">
      </div>
  @endif
  @if(!isset($usuario))
    <h3 class="text-center" style="color: #fd6464;margin-top: 20px;">NO SE ENCONTRO EL USUARIO INGRESADO</h3>
  @else
      <h3  class="text-center" style="color: #0AA09E;margin-top: 20px;">BIENVENIDA(O)</h3>
  @endif

    @if(isset($usuario))
        <h1  class="text-center" style="color: #0074cd;margin-top: 20px;">{{$usuario[0]->name}}</h1>
    @endif
    @if(isset($asistencia) && $asistencia == 0)
      <h6  class="text-center" style="color: #ff0000;margin-top: 20px;">LO SIENTO, TIENES FALTA!</h6>
    @elseif(isset($asistencia) && $asistencia == 1)
      <h6  class="text-center" style="color: #2aa100;margin-top: 20px;">JUSTO A TIEMPO!</h6>
    @elseif(isset($asistencia) && $asistencia == 2)
      <h6  class="text-center" style="color: #ffaa00;margin-top: 20px;">TIENES RETARDO!</h6>
    @endif

  <form action="{{route('asistenciaIndividual',[$idFranquicia,$idPoliza])}}"  method="GET">
      <div class="row">
          <div class="col-3" style="float:none;margin:0 auto;">
              <div class="form-group">
                  <input type="password" name="usuario" id="usuario"
                         class="form-control" oncopy="return false" oncut="return false" onpaste="return false"
                         placeholder="@if($accionbanderaasistenciafranquicia == 1) Escanea tu código de barras @else Ingresa tu código de asistencia @endif " style="font-size: medium;text-align: center;">
              </div>
          </div>
      </div>
      <div class="row">
          <div class="col-3" style="float:none;margin:0 auto;">
              <div class="form-group">
                  <button  class="btn btn-outline-primary btn-block" name="btnSubmit" type="submit">ACEPTAR</button>
              </div>
          </div>
      </div>
  </form>
  </div>
  <script  type="text/javascript">
      $(document).ready(function() {
          $('#usuario').focus();
      });
  </script>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
