@extends('layouts.app')
@section('titulo','Reporte cancelados'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2 style="text-align: left; color: #0AA09E">Cancelados</h2>
    <input type="hidden" value="{{$idFranquicia}}" id="idFranquiciaActual" name="idFranquiciaActual">
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
                                            <option value="" selected>Todas las sucursales</option>
                                            @foreach($franquicias as $franquicia)
                                                <option
                                                    value="{{$franquicia->id}}" {{ isset($franquiciaSeleccionada) ? ($franquiciaSeleccionada == $franquicia->id ? 'selected' : '' ) : '' }}>
                                                    {{$franquicia->ciudad}}
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
                            <label for="zonaSeleccionada">Zonas</label>
                            <select class="custom-select {!! $errors->first('zonaSeleccionada','is-invalid')!!}" name="zonaSeleccionada" id="zonaSeleccionada">
                                <option value="" selected>Todas las zonas</option>
                                <option value="1" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 1 ? 'selected' : '' ) : '' }}>1</option>
                                <option value="2" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 2 ? 'selected' : '' ) : '' }}>2</option>
                                <option value="3" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 3 ? 'selected' : '' ) : '' }}>3</option>
                                <option value="4" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 4 ? 'selected' : '' ) : '' }}>4</option>
                                <option value="5" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 5 ? 'selected' : '' ) : '' }}>5</option>
                                <option value="6" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 6 ? 'selected' : '' ) : '' }}>6</option>
                                <option value="7" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 7 ? 'selected' : '' ) : '' }}>7</option>
                                <option value="8" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 8 ? 'selected' : '' ) : '' }}>8</option>
                                <option value="Oficina" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 'Oficina' ? 'selected' : '' ) : '' }}>Oficina</option>
                            </select>
                            {!! $errors->first('zonaSeleccionada','<div class="invalid-feedback">Por favor, selecciona la zona.</div>')!!}
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label>Fecha inicial</label>
                                <input type="date" name="fechaInicio" id="fechaInicio" class="form-control {!! $errors->first('fechaInicial','is-invalid')!!}"
                                       @isset($fechaInicial) value = "{{$fechaInicial}}" @endisset>
                                @if($errors->has('fechaInicial'))
                                    <div class="invalid-feedback">{{$errors->first('fechaInicial')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-2">
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
    <div id="tablaContratosCancelados">

    </div>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
