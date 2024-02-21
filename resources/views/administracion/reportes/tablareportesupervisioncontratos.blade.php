@extends('layouts.app')
@section('titulo','Reporte contratos supervision'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2 style="text-align: left; color: #0AA09E">Contratos en supervisión</h2>
        @if(Auth::user()->rol_id == 7)
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
                            <form action="{{route('reportecontratossupervisionfiltrar', $idFranquicia)}}"
                                  enctype="multipart/form-data" method="POST"
                                  onsubmit="btnSubmit.disabled = true;">
                                @csrf
                                <div class="row">
                                    <div class="col-3">
                                        <label for="franquiciaSeleccionada">Sucursal</label>
                                        <div class="form-group">
                                            <select name="franquiciaSeleccionada"
                                                    class="form-control {!! $errors->first('franquiciaSeleccionada','is-invalid')!!}">
                                                @if(count($franquicias) > 0)
                                                    <option value="" selected>Seleccionar sucursal</option>
                                                    @foreach($franquicias as $franquicia)
                                                        <option value="{{$franquicia->id}}" @if($franquiciaSeleccionada == $franquicia->id) selected @endif>{{$franquicia->ciudad}}</option>
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                            {!! $errors->first('franquiciaSeleccionada','<div class="invalid-feedback">Selecciona una sucursal valida</div>')!!}
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <button class="btn btn-outline-success" name="btnSubmit" type="submit">Filtrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="row" style="padding-top: 10px;">
            <div class="col-4">
                <button type="button" class="btn btn-primary" style="margin-bottom: 10px;">
                    Total registros <span class="badge bg-secondary">{{sizeof($contratosSupervision)}}</span>
                </button>
                @if(count($contratosSupervision)>0)
                    <a href="#" id="btnExportarExcel" onclick="exportarAExcel('Reporte Contratos supervisón','tablaReporteSupervision');" style="text-decoration:none; color:black; padding-left: 15px;">
                        <button type="button" class=" btn btn-success" style="margin-bottom: 10px;"> Exportar </button>
                    </a>
                @endif
            </div>
            <div class="col-6"></div>
            <div class="col-2">
                <i class="fa-solid fa-location-dot" data-toggle="modal" data-target="#modalgooglemaps" style="cursor: pointer" id="btnCrearMarcadores"
                   onclick="crearMarcadoresReportes('{{json_encode($contratosSupervisionMapa)}}')";>Ver Mapa</i>
            </div>
            <div class="col-4">
                <i class="bi bi-square-fill" style="color: rgba(255,15,0,0.17); font-size: 20px;"></i> <label>Contratos en supervisión, actualizacion de estatus hace más de 15 días.</label>
            </div>
        </div>
        <div class="modal fade" id="modalgooglemaps" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        Marcadores
                    </div>
                    <div class="modal-body">
                        <div id="map" style="border: black 3px solid; width:100%; height: 500px;"></div>
                        <div id="simbologia" style="background-color: white; margin-right: 20px; margin-top: 60px; font-size: 12px; font-weight: bold; text-align: center;">Información</div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-success" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="contenedortblReportes" style="width: 100%; max-height: 900px; overflow-x: auto;">
            <table class="table-bordered table-striped table-general table-sm" style="margin-top: 20px;" id="tablaReporteSupervision" name="tablaReporteSupervision">
                <thead>
                <tr>
                    <th style="text-align:center;" scope="col">CONTRATO</th>
                    <th style="text-align:center;" scope="col">SUCURSAL</th>
                    <th style="text-align:center;" scope="col">FECHA DE CREACIÓN</th>
                    <th style="text-align:center;" scope="col">FECHA DE REPORTE</th>
                    <th style="text-align:center;" scope="col">SOLICITANTE</th>
                    <th style="text-align:center;" scope="col">NOMBRE CLIENTE</th>
                    <th style="text-align:center;" scope="col">TELEFONO</th>
                    <th style="text-align:center;" scope="col">CALLE</th>
                    <th style="text-align:center;" scope="col">NUMERO</th>
                    <th style="text-align:center;" scope="col">LOCALIDAD</th>
                    <th style="text-align:center;" scope="col">COLONIA</th>
                    <th style="text-align:center;" scope="col">MENSAJE</th>
                </tr>
                </thead>
                <tbody>
                @if($contratosSupervision != null)
                    @foreach($contratosSupervision as $contrato)
                        <tr @if($contrato->diasReporte >= 15) style=" background-color: rgba(255,15,0,0.17);" @endif>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$contrato->id}}</td>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$contrato->sucursal}}</td>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$contrato->created_at}}</td>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$contrato->fechaReporte}}</td>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$contrato->usuario_solicitud}}</td>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$contrato->nombre}}</td>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$contrato->telefono}}</td>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$contrato->calleentrega}}</td>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$contrato->numeroentrega}}</td>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$contrato->localidadentrega}}</td>
                            <td align='center'  style="font-size: 10px; white-space: normal;">{{$contrato->coloniaentrega}}</td>
                            <td align='center'  style="font-size: 10px; white-space: normal;">{{$contrato->mensaje}}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td align='center'  style="font-size: 10px; white-space: nowrap;" colspan="12">SIN REGISTROS</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
