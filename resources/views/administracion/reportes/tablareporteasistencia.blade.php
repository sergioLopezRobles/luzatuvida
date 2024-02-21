@extends('layouts.app')
@section('titulo','Reporte asistencia'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2>Reporte de asistencia</h2>
    <input type="hidden" id="idFranquicia" value="{{$idFranquicia}}">
    <input type="hidden" id="rolUsuario" value="{{$rolUsuario}}">
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
                                <div class="col-3">
                                    <label for="franquiciaSeleccionada">Sucursal</label>
                                    <div class="form-group">
                                        <select name="franquiciaSeleccionada"
                                                class="form-control"
                                                id="franquiciaSeleccionada">
                                            @if(count($franquicias) > 0)
                                                <option value="" selected>Seleccionar sucursal</option>
                                                @foreach($franquicias as $franquicia)
                                                    <option
                                                        value="{{$franquicia->id}}"
                                                        {{ isset($franquiciaSeleccionada) ? ($franquiciaSeleccionada == $franquicia->id ? 'selected' : '' ) : '' }}>{{$franquicia->ciudad}}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option selected>Sin registros</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Seleccionar dia</label>
                                    <input type="date" name="semanaAsistencia" id="semanaAsistencia" class="form-control" max="<?php echo date("Y-m-d",strtotime(date("Y-m-d")));?>"
                                           value="{{$fechaHoy}}">
                                    <input type="text" name="periodoAsistencia" id="periodoAsistencia" class="form-control"  readonly value="{{"De ".$fechaLunes." a ".$fechaSabadoSiguiente}}"
                                           style="margin-top: 25px;">
                                    <input type="hidden" name="fechaLunes" id="fechaLunes" class="form-control"  readonly value="{{$fechaLunes}}" style="margin-top: 15px;">
                                    <input type="hidden" name="fechaSabadoSiguiente" id="fechaSabadoSiguiente" class="form-control"  readonly value="{{$fechaSabadoSiguiente}}" style="margin-top:
                                    15px;">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <button type="button" class="btn btn-outline-success btn-block" name="btnCargarAsistencia" id="btnCargarAsistencia">Aplicar</button>
                                    <a href="#" id="btnExportarExcel" onclick="exportarListaAsistencia();" style="text-decoration:none;">
                                        <button type="button" class="btn btn-success btn-block" id="btnExportarListaAsistencia"> Exportar lista de asistencia</button>
                                    </a>
                                </div>
                            </div>
                            <div class="col-2" id="spCargando">
                                <div class="d-flex justify-content-center">
                                    <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 25px;" role="status">
                                        <span class="visually-hidden"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row" style="margin-left: 3px; margin-top: 20px; margin-right: 3px;" id="contenedortblReporteAsistencia">

    </div>
    </div>
@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
