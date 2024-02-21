@extends('layouts.app')
@section('titulo','Movimientos sucursal'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2 style="text-align: left; color: #0AA09E">Movimientos de sucursal</h2>
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
                        <div class="col-3">
                            <label for="">Sucursal</label>
                            <select class="custom-select" id="sucursalSeleccionada" name="sucursalSeleccionada">
                                @if(count($franquicias) > 0)
                                    <option selected value="">Seleccionar sucursal</option>
                                    @foreach($franquicias as $franquicia)
                                        <option value="{{$franquicia->id}}" @if($id_franquicia == $franquicia->id) selected @endif>{{$franquicia->ciudad}}</option>
                                    @endforeach
                                @else
                                    <option selected>No se encontro ningun usuario</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="">Sección</label>
                            <select class="custom-select" id="accionSeleccionada" name="accionSeleccionada">
                                <option selected value="">Todas las secciones</option>
                                <option value="2">Administración</option>
                                <option value="4">Cobranza/Ventas</option>
                                <option value="1">Historial de busqueda</option>
                                <option value="3">Reportes</option>
                                <option value="0">Usuarios</option>
                                <option value="5">Vehiculos</option>
                            </select>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label>Fecha inicial</label>
                                <input type="date" name="fechaInicio" id="fechaInicio" class="form-control"
                                       @isset($fechaInicio) value = "{{$fechaInicio}}" @endisset>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label>Fecha final</label>
                                <input type="date" name="fechaFin" id="fechaFin" class="form-control"
                                       @isset($fechaFinal) value = "{{$fechaFinal}}" @endisset>
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="form-group">
                                <button type="button" class="btn btn-outline-success btn-block" name="btnFiltrarMovimientosSucursal" id="btnFiltrarMovimientosSucursal">Aplicar</button>
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

    <div style="padding-top: 20px; margin-bottom: 5%;">
        <table class="table-bordered table-striped table-general table-sm" style="margin-top: 20px;">
            <thead>
            <tr>
                <th  style =" text-align:center; width: 20%;" >USUARIO</th>
                <th  style =" text-align:center; width: 60%;">MOVIMIENTO</th>
                <th  style =" text-align:center; width: 20%;" >FECHA</th>
            </tr>
            </thead>
            <tbody id="tblreportemovimientossucursal">
            <tr>
                <td align='center' colspan="3">Sin registros</td>
            </tr>
            </tbody>
        </table>
    </div>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
