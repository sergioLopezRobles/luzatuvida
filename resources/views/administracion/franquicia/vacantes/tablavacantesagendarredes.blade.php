@extends('layouts.app')
@section('titulo','Vacantes'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>Redes</h2>
        @php
            $i = 0;
        @endphp
        @if($sucursales > 0)
            @foreach($sucursales as $sucursal)
                <h5>{{$sucursal->ciudad}}</h5>
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
                        <form action="{{route('filtrarlistavacantesredes',$idFranquicia)}}" enctype="multipart/form-data" id="formFiltrarSolicitudesRedes" name="formFiltrarSolicitudesRedes"
                              method="POST" onsubmit="btnSubmit.disabled = true;" >
                            @csrf
                            <div class="row" style="margin-top: 20px;">
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
                                        <input type="date" name="fechaFiltroSolicitud" id="fechaFiltroSolicitud" class="form-control" max="<?= date('Y-m-d');?>" value="{{$fechaFiltrar}}">
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
                                    <button type="submit" name="btnSubmit" class="btn btn-outline-success" form="formFiltrarSolicitudesRedes" style="margin-top: 30px;">Filtrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{--Tabla de solicitudes--}}
        <div class="contenedortblContratos" style="max-height: 700px; overflow-y: auto; width: 100%; overflow-x: auto; margin-top: 20px;">
        <input type="hidden" id="idFranquiciaActual" value="{{$idFranquicia}}">
        <table id="tablaSolicitudesRedes" class="table table-striped table-general table-sm">
            <thead>
            <tr>
                <th scope="col">Estatus</th>
                <th scope="col" style="min-width: 100px;">Sucursal</th>
                <th scope="col" style="min-width: 100px;">Rol</th>
                <th scope="col" >Fecha solicitud</th>
                <th scope="col">Horario atención</th>
                <th scope="col" style="min-width: 250px;">Observaciones solicitud</th>
                <th scope="col" style="min-width: 250px;">Nombre</th>
                <th scope="col" style="min-width: 200px;">Teléfono</th>
                <th scope="col">Curriculum</th>
                <th scope="col">Fecha cita</th>
                <th scope="col">Hora cita</th>
                <th scope="col" style="min-width: 250px;">Observaciones cita</th>
                <th scope="col">Opcion</th>
            </tr>
            </thead>
            <tbody style="alignment: center;">
            @foreach($listaSolicitudesGeneradas as $solicitudGenerada)
                <tr style="@if($solicitudGenerada->estado == 1 || $solicitudGenerada->estado == 8) background: #D6EAF8 @endif
                @if($solicitudGenerada->estado == 2 || $solicitudGenerada->estado == 4) background-color: rgba(255,15,0,0.17); @endif @if($solicitudGenerada->estado == 3) background: #5bc0de @endif">
                    @switch($solicitudGenerada->estado)
                        @case(0)
                            <td align='center' style="vertical-align:middle; font-size: 10px;" colspan="1"> PENDIENTE</td>
                        @break
                        @case(1)
                            <td align='center' style="vertical-align:middle; font-size: 10px;" colspan="1"> AGENDÓ </td>
                            @break
                        @case(2)
                            <td align='center' style="vertical-align:middle; font-size: 10px;" colspan="1"> CANCELÓ </td>
                            @break
                        @case(3)
                            <td align='center' style="vertical-align:middle; font-size: 10px;"  colspan="1"> ASISTIÓ </td>
                            @break
                        @case(4)
                            <td align='center' style="vertical-align:middle; font-size: 10px;"  colspan="1"> CANCELADA </td>
                            @break
                        @case(8)
                            <td align='center' style="vertical-align:middle; font-size: 10px;"  colspan="1"> AGENDADA EN LINEA</td>
                            @break
                    @endswitch
                    <td style="vertical-align:middle;"><input type="text" class="form-control" value="{{$solicitudGenerada->sucursal}}" readonly></td>
                    <td style="vertical-align:middle;"><input type="text" class="form-control" value="{{$solicitudGenerada->rol}}" readonly></td>
                    <td style="vertical-align:middle;"><input type="text" class="form-control" value="{{$solicitudGenerada->created_at}}" readonly></td>
                    <td style="vertical-align:middle;">
                        <input type="text" class="form-control"
                               value="@if($solicitudGenerada->horainicio != null && $solicitudGenerada->horafin != null) {{$solicitudGenerada->horainicio}} A {{$solicitudGenerada->horafin}}
                                      @else 08:00 A 17:00 @endif" readonly>
                    </td>
                    <td style="vertical-align:middle;"><textarea type="text" style="text-transform: uppercase;"  rows="2" cols="10" class="form-control" readonly>{{$solicitudGenerada->observacionessolicitud}}</textarea></td>
                    @if($solicitudGenerada->estado == 0)
                        <td style="vertical-align:middle;"><input type="text" class="form-control" id="nombre{{$solicitudGenerada->indice}}" name="nombre{{$solicitudGenerada->indice}}" placeholder="NOMBRE" required></td>
                        <td style="vertical-align:middle;"><input type="tel" class="form-control" id="telefono{{$solicitudGenerada->indice}}" name="telefono{{$solicitudGenerada->indice}}" placeholder="TEL. 333-333-3333 | 333-333-33-33 | 3333333333" required></td>
                        <td style="vertical-align:middle;"></td>
                        <td style="vertical-align:middle;">
                            <input type="date" name="fechacita{{$solicitudGenerada->indice}}" id="fechacita{{$solicitudGenerada->indice}}" class="form-control" min="<?= date('Y-m-d');?>" value="<?= date('Y-m-d');?>">
                        </td>
                        <td style="vertical-align:middle;">
                            <input type="time" name="horacita{{$solicitudGenerada->indice}}" id="horacita{{$solicitudGenerada->indice}}" class="form-control" value="<?= date('H:i');?>">
                        </td>
                        <td style="vertical-align:middle;" style="min-width: 400px;">
                            <textarea name="textarea" rows="2" cols="10" class="form-control" id="observaciones{{$solicitudGenerada->indice}}" placeholder="OBSERVACIONES"></textarea>
                        </td>
                        <td style="vertical-align:middle;" id="columnaBotones">
                            <div id="divBotonesAccion{{$solicitudGenerada->indice}}">
                                <div class="row" style="justify-content: center; margin: 5px;">
                                    <button class="btn btn-outline-success small-button" onclick="agendarCitaVacante({{$solicitudGenerada->indice}})" id="btnAgendar">AGENDAR</button>
                                </div>
                                <div class="row" style="justify-content: center;">
                                    <button class="btn btn-outline-danger small-button" onclick="cancelarVacanteRedes({{$solicitudGenerada->indice}})" id="btnCancelar">CANCELAR</button>
                                </div>
                            </div>
                            <div id="controlSpinner{{$solicitudGenerada->indice}}"> </div>
                        </td>
                    @else
                        <td style="vertical-align:middle;"><input type="text" class="form-control" value="{{$solicitudGenerada->nombresolicitante}}" readonly></td>
                        <td style="vertical-align:middle;"><input type="text" class="form-control" value="{{$solicitudGenerada->telefono}}" readonly></td>
                        <td style="vertical-align:middle; width: fit-content;">
                            <div class="row" style="justify-content: center;">
                                @if($solicitudGenerada->curriculum != null)
                                    <a href="{{route('descargarcurriculumcitavacante',[$idFranquicia, $solicitudGenerada->indice])}}">
                                        <button type="button" class="btn btn-outline-success small-button">DESCARGAR</button>
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td style="vertical-align:middle;"><input type="date" id="fechacita{{$solicitudGenerada->indice}}" class="form-control"  min="<?= date('Y-m-d');?>" value="{{$solicitudGenerada->fechacita}}" @if($solicitudGenerada->estado != 1 && $solicitudGenerada->estado != 8)readonly @endif></td>
                        <td style="vertical-align:middle;"><input type="time" id="horacita{{$solicitudGenerada->indice}}" class="form-control" value="{{$solicitudGenerada->horacita}}" @if($solicitudGenerada->estado != 1 && $solicitudGenerada->estado != 8)readonly @endif></td>
                        <td style="vertical-align:middle;"><textarea type="text" id="observaciones{{$solicitudGenerada->indice}}" style="text-transform: uppercase;" rows="2" cols="10" class="form-control" @if($solicitudGenerada->estado != 1 && $solicitudGenerada->estado != 8)readonly @endif>{{$solicitudGenerada->observaciones}}</textarea></td>
                            @if($solicitudGenerada->estado == 1 || $solicitudGenerada->estado == 8)
                                <td style="vertical-align:middle;" id="columnaBotones">
                                    <div>
                                        <button class="btn btn-outline-info small-button" onclick="actualizarCitaRedes({{$solicitudGenerada->indice}})" id="btnActualizar">ACTUALIZAR</button>
                                    </div>
                                    <div id="controlSpinner{{$solicitudGenerada->indice}}"> </div>
                                </td>
                            @else
                                <td style="vertical-align:middle;"></td>
                            @endif
                    @endif
                </tr>
            @endforeach
            @if(sizeof($listaSolicitudesGeneradas) == 0)
                <tr>
                    <td align='center' colspan="12">Sin registros</td>
                </tr>
            @endif
            </tbody>
        </table>
        </div>
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
                    <td align='center' style="white-space: normal">{{$movimiento->ciudad}} - {{$movimiento->cambios}}</td>
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
