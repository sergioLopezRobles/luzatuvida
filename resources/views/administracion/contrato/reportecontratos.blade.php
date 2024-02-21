@extends('layouts.app')
@section('titulo','Contratos reporte'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2>Reporte de contratos</h2>
        <input type="hidden" id="idFranquicia" value="{{$idFranquicia}}">
        <input type="hidden" id="idRol" value="{{Auth::user()->rol_id}}">
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
                            @if(Auth::user()->rol_id == 7 || Auth::user()->rol_id == 15)
                                <div class="col-3">
                                    <label for="franquiciaSeleccionada">Sucursal</label>
                                    <div class="form-group">
                                        <select name="franquiciaSeleccionada"
                                                class="form-control"
                                                id="franquiciaSeleccionada">
                                            @if(count($franquicias) > 0)
                                                <option value="" selected>Todas las sucursales</option>
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
                                    <label>Selecciona un dia de la semana </label>
                                    <input type="date" name="fechaIni" id="fechaIni" class="form-control {!! $errors->first('fechaIni','is-invalid')!!}" @isset($fechaIni) value = "{{$fechaIni}}" @endisset>
                                    <input type="text" name="periodoFecha" id="periodoFecha" class="form-control"  readonly value=""
                                           style="margin-top: 25px;">
                                    @if($errors->has('fechaIni'))
                                        <div class="invalid-feedback">{{$errors->first('fechaIni')}}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-2" style="margin-top: 35px;">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="cbPeriodoActual" id="cbPeriodoActual">
                                    <label class="custom-control-label" for="cbPeriodoActual" id="labelFormaFiltroReporte">Filtrar por poliza/fecha: Fecha</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-outline-success btn-block" name="btnCargarAsistencia" id="btnCargarContratos">Aplicar</button>
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

    <div id="tblReporteContratos">

    </div>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
