@extends('layouts.app')
@section('titulo','Reporte movimientos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2 style="text-align: left; color: #0AA09E">Movimientos</h2>
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
                            <label for="usuarioSeleccionado">Cobrador</label>
                            <div class="form-group">
                                <select name="usuarioSeleccionado"
                                        class="form-control"
                                        id="usuarioSeleccionado">
                                        @if(($usuarios != null))
                                            <option selected value="">Seleccionar</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{$usuario->id}}">{{$usuario->zona}} - {{$usuario->name}}</option>
                                            @endforeach
                                        @else
                                            <option selected value="">No se encontro ningun usuario</option>
                                        @endif
                                </select>
                            </div>
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
                            <div class="col-1">
                                <button class="btn btn-outline-success btn-block" name="btnFiltrarMovimientos" id="btnFiltrarMovimientos">Filtrar</button>
                            </div>
                        <div class="col-1" id="spCargando">
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
    <div id="contenedortblMovimientos" style="overflow-x: auto">
        <table class="table-bordered table-striped table-general table-sm" style="margin-top: 20px;">
            <thead>
            <tr>
                <th style=" text-align:center;" scope="col">NOMBRE</th>
                <th style=" text-align:center;" scope="col">CONTRATO</th>
                <th style=" text-align:center;" scope="col">NOMBRE CLIENTE</th>
                <th style=" text-align:center;" scope="col">MOVIMIENTO</th>
                <th style=" text-align:center;" scope="col">FECHA</th>
                <th style=" text-align:center;" scope="col">LINK</th>
            </tr>
            </thead>
            <tbody id="tblReporteMovimientos">
            <tr>
                <th style="text-align: center;" colspan="6"> SIN REGISTROS </th>
            </tr>
            </tbody>
        </table>
    </div>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
