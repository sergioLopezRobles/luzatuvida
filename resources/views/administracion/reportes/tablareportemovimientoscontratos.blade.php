@extends('layouts.app')
@section('titulo','Reporte movimientos contratos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2 style="text-align: left; color: #0AA09E">Movimientos contratos</h2>
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
                        <form action="{{route('reportemovimientoscontratosfiltrar',$idFranquicia)}}"
                              enctype="multipart/form-data" method="POST"
                              onsubmit="btnSubmit.disabled = true;">
                            @csrf
                            <div class="row">
                                @if(Auth::user()->rol_id == 7)
                                    <div class="col-3">
                                        <label for="franquiciaSeleccionada">Sucursal</label>
                                        <div class="form-group">
                                            <select name="franquiciaSeleccionada"
                                                    id="franquiciaSeleccionada"
                                                    class="form-control">
                                                @if(count($franquicias) > 0)
                                                    <option value="" selected>Todas las sucursales</option>
                                                    @foreach($franquicias as $franquicia)
                                                        <option value="{{$franquicia->id}}" @if($idFranquicia == $franquicia->id) selected @endif>{{$franquicia->ciudad}}</option>
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                    <div class="col-3">
                                        <label for="usuarioSeleccionado">Usuarios</label>
                                        <div class="form-group">
                                            <select name="usuarioSeleccionado"
                                                    id="usuarioSeleccionado"
                                                    class="form-control">
                                                @if(count($usuarios) > 0)
                                                    <option value="" selected>Todos los usuarios</option>
                                                    @foreach($usuarios as $usuario)
                                                        <option value="{{$usuario->id}}" @if($usuarioSeleccionado == $usuario->id) selected @endif>{{$usuario->name}} - {{$usuario->rol}}</option>
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Fecha inicial</label>
                                        <input type="date" name="fechaInicio" id="fechaInicio" class="form-control"
                                               value = "{{$fechaInicial}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Fecha final</label>
                                        <input type="date" name="fechaFin" id="fechaFin" class="form-control"
                                               value = "{{$fechaFinal}}">
                                    </div>
                                </div>
                                <div class="col-1" id="spCargando">
                                    <div class="d-flex justify-content-center">
                                        <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 30px;" role="status">
                                            <span class="visually-hidden"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-1">
                                     <button class="btn btn-outline-success" name="btnSubmit" id="btnSubmit" type="submit">Filtrar</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="cbLioFuga" id="cbLioFuga"
                                               value="1" @if($cbLioFuga != null) checked @endif>
                                        <label class="custom-control-label" for="cbLioFuga">Lio/Fuga</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="cbCorteLlamada" id="cbCorteLlamada"
                                               value="1" @if($cbCorteLlamada != null) checked @endif>
                                        <label class="custom-control-label" for="cbCorteLlamada">Llamadas</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="cbAutorizacion" id="cbAutorizacion"
                                               value="1" @if($cbAutorizacion != null) checked @endif>
                                        <label class="custom-control-label" for="cbAutorizacion">Autorizaciones</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="cbProducto" id="cbProducto"
                                               value="1" @if($cbProducto != null) checked @endif>
                                        <label class="custom-control-label" for="cbProducto">Nuevo Producto</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="cbMovimientoManual" id="cbMovimientoManual"
                                               value="1" @if($cbMovimientoManual != null) checked @endif>
                                        <label class="custom-control-label" for="cbMovimientoManual">Seguimiento de contratos</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="cbGeneral" id="cbGeneral"
                                               value="1"  @if($cbGeneral != null) checked @endif>
                                        <label class="custom-control-label" for="cbGeneral">General</label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="padding-top: 10px;">
            <div class="col-4">
                <button type="button" class="btn btn-primary" style="margin-bottom: 10px;">
                    Total registros <span class="badge bg-secondary">{{sizeof($movimientosContratos)}}</span>
                </button>
                @if(count($movimientosContratos)>0)
                    <a href="#" id="btnExportarExcel" onclick="exportarAExcel('Reporte Movimientos Contratos','tablaReporteMovimientosContratos');" style="text-decoration:none; color:black; padding-left: 15px;">
                        <button type="button" class=" btn btn-success" style="margin-bottom: 10px;"> Exportar </button>
                    </a>
                @endif
            </div>
        </div>

        <div class="contenedortblReportes">
            <table class="table-bordered table-striped table-general table-sm" style="margin-top: 20px;" id="tablaReporteMovimientosContratos" name="tablaReporteMovimientosContratos">
                <thead>
                <tr>
                    <th style="text-align:center;" scope="col">SUCURSAL</th>
                    <th style="text-align:center;" scope="col">CONTRATO</th>
                    <th style="text-align:center;" scope="col">FECHA CREACION CONTRATO</th>
                    <th style="text-align:center;" scope="col">FECHA CREACIÓN MOVIMIENTO</th>
                    <th style="text-align:center;" scope="col">USUARIO</th>
                    <th style="text-align:center;" scope="col">MOVIMIENTO</th>
                    <th style="text-align:center;" scope="col">NOTA COBRANZA</th>
                </tr>
                </thead>
                <tbody>
                @if($movimientosContratos != null)
                    @foreach($movimientosContratos as $movimiento)
                        <tr>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$movimiento->sucursal}}</td>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$movimiento->id_contrato}}</td>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$movimiento->fechacontrato}}</td>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$movimiento->fechamovimiento}}</td>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$movimiento->name}}</td>
                            <td align='center'  style="font-size: 10px; white-space: normal;">{{$movimiento->cambios}}</td>
                            <td align='center'  style="font-size: 10px; white-space: normal;">{{$movimiento->nota}}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td align='center'  style="font-size: 10px; white-space: nowrap;" colspan="7">Sin registros</td>
                    </tr>
                @endif

                </tbody>
            </table>

        </div>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
