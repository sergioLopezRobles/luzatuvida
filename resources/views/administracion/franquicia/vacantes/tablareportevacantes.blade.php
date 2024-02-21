@extends('layouts.app')
@section('titulo','Reporte vacantes'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>Reporte de mensajes</h2>
        @if((Auth::user()->id == 1 || Auth::user()->id == 61 || Auth::user()->id == 761 || Auth::user()->rol_id == 18) && Auth::user()->rol_id != 6)
            <form action="{{route('nuevomensajevacantes', $idFranquicia)}}" enctype="multipart/form-data"
                  method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="col">
                    <div class="col-3">
                        <div class="form-group">
                            <label>Sucursales:</label>
                            <select name="sucursalSeleccionada"
                                    id="sucursalSeleccionada"
                                    class="form-control {!! $errors->first('sucursalSeleccionada','is-invalid')!!}">
                                @if(count($sucursales) > 0)
                                    <option value="">Selecciona una sucursal</option>
                                    @foreach($sucursales as $sucursal)
                                        <option
                                            value="{{$sucursal->id}}" {{isset($idFranquicia)?($idFranquicia == $sucursal->id ? 'selected' : '' ) : '' }}>{{$sucursal->ciudad}}
                                        </option>
                                    @endforeach
                                @else
                                    <option selected>Sin registros</option>
                                @endif
                            </select>
                            {!! $errors->first('sucursalSeleccionada','<div class="invalid-feedback">Selecciona una sucursal.</div>')!!}
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea  rows="3" name="mensaje" id="mensaje" class="form-control {!! $errors->first('mensaje','is-invalid')!!}" style="text-transform: uppercase"
                                       placeholder="Mensaje"> {{old('mensaje')}} </textarea>
                            {!! $errors->first('mensaje','<div class="invalid-feedback">Campo mensaje obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div style="display: flex; justify-content: end;">
                        <div class="col-3">
                            <button class="btn btn-outline-success btn-ok btn-block" name="btnSubmit" type="submit">Agregar</button>
                        </div>
                    </div>
                </div>
            </form>
        @endif

        {{--Tabla de reportes de mensajes --}}
        <table id="tablaSolicitudes" class="table-general table-bordered table-striped table-sm mt-2">
            <thead>
            <tr>
                <th scope="col">Identificador</th>
                @if(Auth::user()->rol_id == 7 || Auth::user()->rol_id == 18)
                    <th scope="col" style="vertical-align:middle;">Sucursal</th>
                @endif
                <th scope="col">Usuario</th>
                <th scope="col" style="white-space: normal">mensaje</th>
                <th scope="col" style="white-space: normal">respuesta</th>
                <th scope="col">Hora de registro</th>
                <th scope="col">Accion</th>
            </tr>
            </thead>
            <tbody>
            @if($mensajesVacantes != null)
                @foreach($mensajesVacantes as $mensaje)
                    <tr style="@if($mensaje->estadomensaje == 0)background-color: rgba(255,15,0,0.17); @endif @if($mensaje->estadomensaje == 1) background: rgba(96,241,178,0.91); @endif
                    @if($mensaje->estadomensaje == 2) background: #D6EAF8; @endif">
                        <td align="center">{{$mensaje->indice}}</td>
                        @if(Auth::user()->rol_id == 7 || Auth::user()->rol_id == 18)
                            <td align="center">{{$mensaje->sucursal}}</td>
                        @endif
                        <td align="center">{{$mensaje->usuario}}</td>
                        <td align="center" style="white-space: normal; text-align: justify;">{{$mensaje->mensaje}}</td>
                        <td align="center" style="white-space: normal; text-align: justify;">{{$mensaje->respuesta}}</td>
                        <td align="center">{{$mensaje->created_at}}</td>
                        <td align="center">
                            @if(Auth::user()->rol_id == 6)
                                @if($mensaje->estadomensaje == 0)
                                    <a href="{{route('leermensajevacante',[$idFranquicia, $mensaje->indice])}}">
                                        <button type="button" class="btn btn-outline-success btn-sm">LEIDO</button>
                                    </a>
                                @else
                                    @if($mensaje->estadomensaje == 1)
                                        <a class="btn btn-outline-success btnResponderMensajeRedes btn-sm" data-toggle="modal" data-target="#modalResponderMensajeRedes"
                                           data_parametros_modal="{{$mensaje->indice}}">RESPONDER
                                        </a>
                                    @endif
                                @endif
                            @else
                                @if((Auth::user()->rol_id == 7 || Auth::user()->rol_id == 18) && $mensaje->estadomensaje == 0)
                                    @if(date("Y-m-d H:i:s", strtotime($mensaje->created_at ." +5 minutes")) > Carbon\Carbon::now())
                                        <a href="{{route('eliminarmensajevacante',[$idFranquicia, $mensaje->indice])}}">
                                            <button type="button" class="btn btn-outline-danger btn-sm">ELIMINAR</button>
                                        </a>
                                    @endif
                                    @if(Auth::user()->rol_id == 7)
                                        <a href="{{route('leermensajevacante',[$idFranquicia, $mensaje->indice])}}">
                                            <button type="button" class="btn btn-outline-success btn-sm">LEIDO</button>
                                        </a>
                                    @endif
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <th scope="col" @if(Auth::user()->rol_id == 6) colspan="5" @else colspan="6" @endif >Sin registros</th>
                </tr>
            @endif

            </tbody>
        </table>

        <!-- Modal de respuesta a mensaje redes-->
        <div class="modal fade" id="modalResponderMensajeRedes"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  aria-hidden="true">
            <form action="{{route('respondermensajevacante', $idFranquicia)}}" enctype="multipart/form-data"
                  method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #0AA09E;">
                            <h5 style="color: white;"> Responder mensaje redes</h5>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" class="form-control" name="indiceMensaje" id="indiceMensaje">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Respuesta</label>
                                        <textarea  rows="3" name="respuestaMensaje" id="respuestaMensaje" class="form-control" style="text-transform: uppercase"
                                                   placeholder="RESPUESTA"> {{old('respuestaMensaje')}} </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
                            <button class="btn btn-outline-success btn-ok" name="btnSubmit" type="submit">Aceptar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
