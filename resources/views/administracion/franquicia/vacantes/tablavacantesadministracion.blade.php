@extends('layouts.app')
@section('titulo','Vacantes'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>Vacantes</h2>
        <h5>HORARIO ATENCIÓN</h5>
        <form action="{{route('actualizarhorariocitavacantes',$idFranquicia)}}" enctype="multipart/form-data"
              method="POST" onsubmit="btnSubmit.disabled = true;" id="formHorarioAtencion" name="formHorarioAtencion">
            @csrf
            <div class="row">
                @if(Auth::user()->rol_id == 7)
                    <div class="col-3">
                        <div class="form-group">
                            <label>Sucursales:</label>
                            <select name="sucursalSeleccionadaHorario"
                                    id="sucursalSeleccionadaHorario"
                                    class="form-control {!! $errors->first('sucursalSeleccionadaHorario','is-invalid')!!}">
                                @if(count($sucursales) > 0)
                                    <option value="">Todas las sucursales</option>
                                    @foreach($sucursales as $sucursal)
                                        <option
                                            value="{{$sucursal->id}}" {{isset($idFranquicia)?($idFranquicia == $sucursal->id ? 'selected' : '' ) : '' }}>{{$sucursal->ciudad}}
                                        </option>
                                    @endforeach
                                @else
                                    <option selected>Sin registros</option>
                                @endif
                            </select>
                            {!! $errors->first('sucursalSeleccionadaHorario','<div class="invalid-feedback">Selecciona una sucursal.</div>')!!}
                        </div>
                    </div>
                @endif
                <div class="col-3">
                    <div class="form-group">
                        <label>Hora inicio citas:</label>
                        <input type="time" class="form-control {!! $errors->first('horaInicio','is-invalid')!!}" id="horaInicio" name="horaInicio"
                               @if($horarioAtencion[0]->horaatencioninicio != null) value="{{$horarioAtencion[0]->horaatencioninicio}}" @else value="08:00" @endif required>
                        {!! $errors->first('horaInicio','<div class="invalid-feedback">Ingresa una hora para inicio atención de citas.</div>')!!}
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Hora fin citas:</label>
                        <input type="time" class="form-control {!! $errors->first('horaFinal','is-invalid')!!}" id="horaFinal" name="horaFinal"
                               @if($horarioAtencion[0]->horaatencionfin != null) value="{{$horarioAtencion[0]->horaatencionfin}}" @else value="17:00" @endif required>
                        {!! $errors->first('horaFinal','<div class="invalid-feedback">Ingresa una hora para fin atención de citas.</div>')!!}
                    </div>
                </div>
                <div class="col-2">
                    <button type="submit" name="btnSubmit" class="btn btn-outline-success" style="margin-top: 30px;" form="formHorarioAtencion">Aplicar</button>
                </div>
            </div>
        </form>
        <h5>SOLICITUDES</h5>
        <form action="{{route('solicitarvacante',$idFranquicia)}}" enctype="multipart/form-data"
              method="POST" onsubmit="btnSubmit.disabled = true;" >
            @csrf
            <div class="row">
                @if(Auth::user()->rol_id == 7)
                    <div class="col-2">
                        <div class="form-group">
                            <label>Sucursales:</label>
                            <select name="sucursalSeleccionada"
                                    id="sucursalSeleccionada"
                                    class="form-control {!! $errors->first('sucursalSeleccionada','is-invalid')!!}">
                                @if(count($sucursales) > 0)
                                    <option value="">Todas las sucursales</option>
                                    @foreach($sucursales as $sucursal)
                                        <option
                                            value="{{$sucursal->id}}" {{isset($idFranquicia)?($idFranquicia == $sucursal->id ? 'selected' : '' ) : '' }}>{{$sucursal->ciudad}}</option>
                                    @endforeach
                                @else
                                    <option selected>Sin registros</option>
                                @endif
                            </select>
                            {!! $errors->first('sucursalSeleccionada','<div class="invalid-feedback">Selecciona una sucursal.</div>')!!}
                        </div>
                    </div>
                @endif
                    <div class="col-2">
                        <div class="form-group">
                            <label>Roles:</label>
                            <select name="rolSeleccionado"
                                    id="rolSeleccionado"
                                    class="form-control {!! $errors->first('rolSeleccionado','is-invalid')!!}">
                                @if(count($roles) > 0)
                                    <option value="">Seleccionar rol</option>
                                    @foreach($roles as $rol)
                                        <option
                                            value="{{$rol->id}}">{{$rol->rol}}</option>
                                    @endforeach
                                @else
                                    <option selected>Sin registros</option>
                                @endif
                            </select>
                            {!! $errors->first('rolSeleccionado','<div class="invalid-feedback">Selecciona un rol.</div>')!!}
                        </div>
                    </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Vacantes a solicitar</label>
                        <input type="number" name="numsolicitudes" id="numsolicitudes" class="form-control {!! $errors->first('numsolicitudes','is-invalid')!!}" min="1" placeholder="VACANTES">
                        {!! $errors->first('numsolicitudes','<div class="invalid-feedback">Numero de vacantes debe ser mayor a 0.</div>')!!}
                    </div>
                </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea name="observacionesSolicitud" id="observacionesSolicitud" class="form-control {!! $errors->first('observacionesSolicitud','is-invalid')!!}"
                                      cols="50" rows="5" max="1000" placeholder="OBSERVACIONES"></textarea>
                            {!! $errors->first('observacionesSolicitud','<div class="invalid-feedback">El texto de observaciones debe ser menor a 1000 caracteres.</div>')!!}
                        </div>
                    </div>
                <div class="col-2">
                    <button type="submit" name="btnSubmit" class="btn btn-outline-success" style="margin-top: 30px;">Solicitar</button>
                </div>
            </div>
        </form>
{{--Contabilidad de solicitudes existentes--}}
        <div class="row" style="margin: 0px;">
            @php
                $i = 0;
            @endphp
            @if($sucursales > 0)
                @foreach($sucursales as $sucursal)
                    <h5 style="margin-top: 10px;">{{$sucursal->ciudad}}</h5>
                    <div class="row">
                        @if(count($solicitudesRol) > 0)
                            @foreach($solicitudesRol[$i] as $solicitud)
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>{{$solicitud->rol}}</label>
                                        <input type="text" class="form-control" value="{{$solicitud->numeroSolicitudes}}"
                                               @if($solicitud->numeroSolicitudes > 0) style="background-color: #0AA09E; color: white;" @endif readonly>
                                    </div>
                                </div>
                            @endforeach
                    </div>
                    @endif
                    @php
                        $i = $i + 1;
                    @endphp
                @endforeach
            @endif
        </div>
        <div id="accordion">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Filtrar
                        </button>
                    </h5>
                </div>
                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <form action="{{route('filtrarsolicitudesvacantesadmin',$idFranquicia)}}" enctype="multipart/form-data" id="formFiltrarSolicitudes" name="formFiltrarSolicitudes"
                              method="POST" onsubmit="btnSubmit.disabled = true;" >
                            @csrf
                            <div class="row" style="margin-top: 20px;">
                                @if(Auth::user()->rol_id == 7)
                                <div class="col-2">
                                    <label for="franquiciaSeleccionada">Sucursal</label>
                                    <div class="form-group">
                                        <select name="franquiciaSeleccionada"
                                                class="form-control"
                                                id="franquiciaSeleccionada">
                                            @if(count($sucursales) > 0)
                                                <option value="" selected>Todas las sucursales</option>
                                                @foreach($sucursales as $sucursal)
                                                    <option
                                                        value="{{$sucursal->id}}"
                                                        {{ isset($franquiciaSeleccionada) ? ($franquiciaSeleccionada == $sucursal->id ? 'selected' : '' ) : '' }}>{{$sucursal->ciudad}}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option selected>Sin registros</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                @endif
                                <div class="col-2">
                                    <label for="rolSeleccionado">Rol</label>
                                    <div class="form-group">
                                        <select name="rolSeleccionado"
                                                class="form-control"
                                                id="rolSeleccionado">
                                            @if(count($roles) > 0)
                                                <option value="" selected>Todos los roles</option>
                                                @foreach($roles as $rol)
                                                    <option
                                                        value="{{$rol->id}}"
                                                        {{ isset($rolSeleccionado) ? ($rolSeleccionado == $rol->id ? 'selected' : '' ) : '' }}>{{$rol->rol}}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option selected>Sin registros</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Fecha</label>
                                        <input type="date" name="fechaFiltroSolicitud" id="fechaFiltroSolicitud" class="form-control" value="{{$fechaFiltrar}}" max="<?= date('Y-m-d');?>">
                                    </div>
                                </div>
                                    <div class="col-2">
                                        <div class="custom-control custom-checkbox" style="margin-top: 35px;">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="cbFechaCreacion" id="cbFechaCreacion"
                                                   value="1" @if($cbFechaCreacion != null) checked @endif>
                                            <label class="custom-control-label" for="cbFechaCreacion">Fecha de creacion solicitud</label>
                                        </div>
                                    </div>
                                <div class="col-2">
                                    <div class="custom-control custom-checkbox" style="margin-top: 35px;">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="cbFechaCita" id="cbFechaCita"
                                               value="1" @if($cbFechaCita != null) checked @endif>
                                        <label class="custom-control-label" for="cbFechaCita">Fecha de cita solicitud</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <button type="submit" name="btnSubmit" class="btn btn-outline-success" form="formFiltrarSolicitudes" style="margin-top: 30px;">Filtrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
{{--Tabla de solicitudes--}}
        <input type="hidden" value="{{$idFranquicia}}" id="idFraquicia">
        <table id="tablaSolicitudes" class="table table-striped table-general table-sm mt-2">
            <thead>
            <tr>
                @if(Auth::user()->rol_id == 7)
                    <th scope="col" style="vertical-align:middle;">Sucursal</th>
                @endif
                <th scope="col">Fecha solicitud</th>
                <th scope="col">Fecha cita</th>
                <th scope="col">Hora cita</th>
                <th scope="col">Observaciones solicitud</th>
                <th scope="col">Nombre</th>
                <th scope="col">Teléfono</th>
                <th scope="col">Rol</th>
                <th scope="col">Observaciones cita</th>
                <th scope="col">Curriculum</th>
                <th scope="col" colspan="2">Opcion</th>
            </tr>
            </thead>
            <tbody>
            @foreach($listaSolicitudesGeneradas as $solicitudGenerada)
            <tr style="@if($solicitudGenerada->estado == 1 || $solicitudGenerada->estado == 8) background: #D6EAF8 @endif
                @if($solicitudGenerada->estado == 2 || $solicitudGenerada->estado == 4 || $solicitudGenerada->estado == 7) background-color: rgba(255,15,0,0.17); @endif
                @if($solicitudGenerada->estado == 3) background: #5bc0de @endif
                @if($solicitudGenerada->estado == 6) background: #5cb85c @endif">
                @if(Auth::user()->rol_id == 7)
                    <td align='center' style="vertical-align:middle;">{{$solicitudGenerada->ciudad}}</td>
                @endif
                <td align='center' style="vertical-align:middle;">{{$solicitudGenerada->created_at}}</td>
                <td align='center' style="vertical-align:middle;">{{$solicitudGenerada->fechacita}}</td>
                <td align='center' style="vertical-align:middle;">@if($solicitudGenerada->horacita != null){{$solicitudGenerada->horacita}}:00 HRS @endif</td>
                <td align='center' style="white-space: normal;" style="vertical-align:middle; min-width: 400px;">{{$solicitudGenerada->observacionessolicitud}}</td>
                <td align='center' style="vertical-align:middle;">{{$solicitudGenerada->nombresolicitante}}</td>
                <td align='center' style="vertical-align:middle;">{{$solicitudGenerada->telefono}}</td>
                <td align='center' style="vertical-align:middle;">{{$solicitudGenerada->rol}}</td>
                <td align='center' style="white-space: normal; vertical-align:middle; min-width: 400px;">{{$solicitudGenerada->observaciones}}</td>
                <td align='center' style="vertical-align:middle;">
                    @if($solicitudGenerada->curriculum != null)
                        <a href="{{route('descargarcurriculumcitavacante',[$idFranquicia, $solicitudGenerada->indice])}}">
                            <button type="button" class="btn btn-outline-success btn-sm">DESCARGAR</button>
                        </a>
                    @endif
                </td>
                @if($solicitudGenerada->estado == 1 || $solicitudGenerada->estado == 8)
                <td align='center' style="vertical-align:middle; width: 50px;">
                    <button class="btn btn-outline-success btn-sm" onclick="notificarCitaVacante('{{$solicitudGenerada->id_franquicia}}',{{$solicitudGenerada->indice}}, 'asistio')" id="btnNotificar">LLEGÓ</button>
                </td>
                <td style="vertical-align:middle; width: 50px;">
                    <button class="btn btn-outline-danger btn-sm" onclick="notificarCitaVacante('{{$solicitudGenerada->id_franquicia}}',{{$solicitudGenerada->indice}}, 'cancelo')" id="btnNotificar">CANCELÓ</button>
                </td>
                @endif
                @if($solicitudGenerada->estado == 2)
                    <td align='center' style="vertical-align:middle;" colspan="2"> CANCELÓ </td>
                @endif
                @if($solicitudGenerada->estado == 3)
                 <td align='center' style="vertical-align:middle; width: 50px;">
                     <button class="btn btn-outline-success  btn-sm" onclick="notificarCitaVacante('{{$solicitudGenerada->id_franquicia}}',{{$solicitudGenerada->indice}}, 'contratar')" id="btnNotificar">CONTRATADO</button>
                 </td>
                 <td style="vertical-align:middle; width: 50px;">
                     <button class="btn btn-outline-danger  btn-sm" onclick="notificarCitaVacante('{{$solicitudGenerada->id_franquicia}}',{{$solicitudGenerada->indice}}, 'rechazar')" id="btnNotificar">RECHAZADO</button>
                 </td>
                @endif
                @if($solicitudGenerada->estado == 4)
                        <td align='center' style="vertical-align:middle;" colspan="2"> CANCELADA </td>
                @endif
                @if($solicitudGenerada->estado == 6)
                        <td align='center' style="vertical-align:middle;" colspan="2"> CONTRATADO</td>
                @endif
                @if($solicitudGenerada->estado == 7)
                        <td align='center' style="vertical-align:middle;" colspan="2"> RECHAZADO</td>
                @endif
            </tr>
            @endforeach
            @if(sizeof($listaSolicitudesGeneradas) == 0)
                @if(Auth::user()->rol_id == 7)
                    <tr>
                        <td align='center' style="vertical-align:middle;" colspan="12">Sin registros</td>
                    </tr>
                @else
                    <tr>
                        <td align='center' style="vertical-align:middle;" colspan="11">Sin registros</td>
                    </tr>
                @endif
            @endif
            </tbody>
        </table>
        <hr>
{{--Tabla de movimientos--}}
        <h2>Historial de movimientos </h2>
        <table id="tablaMovimientos" class="table table-striped table-general table-sm">
            <thead>
            <tr>
                <th scope="col">Usuario</th>
                <th scope="col">Cambios</th>
                <th scope="col">Fecha</th>
            </tr>
            </thead>
            <tbody>
            @foreach($movimientoVacantes as $movimiento)
                <tr>
                    <td align='center'>{{$movimiento->usuario}}</td>
                    <td align='center' style="white-space: normal">{{$movimiento->cambios}}</td>
                    <td align='center'>{{$movimiento->created_at}}</td>
                </tr>
            @endforeach
            @if(sizeof($movimientoVacantes) == 0)
                <td align='center' colspan="3">Sin registros</td>
            @endif
            </tbody>
        </table>

    </div>
    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
