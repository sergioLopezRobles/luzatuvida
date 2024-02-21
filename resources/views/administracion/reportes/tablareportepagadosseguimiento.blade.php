@extends('layouts.app')
@section('titulo','Seguimiento cliente'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2 style="text-align: left; color: #0AA09E">Seguimiento paciente</h2>
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
                        <form action="{{route('filtrarlistacontratospagadosseguimiento', $idFranquicia)}}"
                              enctype="multipart/form-data" method="POST"
                              onsubmit="btnSubmitSeguimiento.disabled = true;">
                            @csrf
                            <div class="row">
                                @if(Auth::user()->rol_id == 7)
                                    <div class="col-3">
                                        <label for="franquiciaSeleccionadaSeguimiento">Sucursal</label>
                                        <div class="form-group">
                                            <select name="franquiciaSeleccionadaSeguimiento" id="franquiciaSeleccionadaSeguimiento"
                                                    class="form-control">
                                                @if(count($franquicias) > 0)
                                                    <option value="" selected>Seleccionar sucursal</option>
                                                    @foreach($franquicias as $franquicia)
                                                        <option
                                                            value="{{$franquicia->id}}" {{ isset($idFranquicia) ? ($idFranquicia == $franquicia->id ? 'selected' : '' ) : '' }}>
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
                                        <label for="zonaSeleccionadaSeguimiento">Zona</label>
                                        <div class="form-group">
                                            <select name="zonaSeleccionadaSeguimiento" id="zonaSeleccionadaSeguimiento"
                                                    class="form-control">
                                                @if(count($zonas) > 0)
                                                    <option value="">Seleccionar zona</option>
                                                    <option value="0" selected>Todas las zonas</option>
                                                    @foreach($zonas as $zona)
                                                        <option
                                                            value="{{$zona->id}}" {{ isset($zonaDefault) ? ($zonaDefault == $zona->id ? 'selected' : '' ) : '' }}>
                                                            {{$zona->zona}}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <input type="hidden" value="{{$idFranquicia}}" id="idFranquicia" name="idFranquicia">
                                        <label for="coloniaSeleccionada">Colonias</label>
                                        <div class="form-group">
                                            <select name="coloniaSeleccionada" id="coloniaSeleccionada"
                                                    class="form-control">
                                                @if(count($colonias) > 0)
                                                    <option value="">Seleccionar colonia</option>
                                                    <option value="0" selected>Todas las colonias</option>
                                                    @foreach($colonias as $colonia)
                                                        <option
                                                            value="{{$colonia->indice}}" {{ isset($coloniaDefault) ? ($coloniaDefault == $colonia->indice ? 'selected' : '' ) : '' }}>
                                                            {{$colonia->localidad}} - {{$colonia->colonia}}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <label for="periodoLiquidadoSeleccionado">Periodo de contratos pagados</label>
                                        <div class="form-group">
                                            <select name="periodoLiquidadoSeleccionado" id="periodoLiquidadoSeleccionado"
                                                    class="form-control">
                                                <option value="" selected>Seleccionar periodo de liquidación</option>
                                                <option value="0" @if($opcion == 0) selected @endif>Pagados entre 9-10 meses</option>
                                                <option value="1" @if($opcion == 1) selected @endif>Pagados entre 10-11 meses</option>
                                                <option value="2" @if($opcion == 2) selected @endif>Pagados entre 11-12 meses</option>
                                                <option value="3" @if($opcion == 3) selected @endif>Pagados entre 12-13 meses</option>
                                                <option value="4" @if($opcion == 4) selected @endif>Pagados entre 13-14 meses</option>
                                                <option value="5" @if($opcion == 5) selected @endif>Pagados entre 14-15 meses</option>
                                                <option value="6" @if($opcion == 6) selected @endif>Pagados entre 15-16 meses</option>
                                                <option value="7" @if($opcion == 7) selected @endif>Pagados entre 16-17 meses</option>
                                                <option value="8" @if($opcion == 8) selected @endif>Pagados entre 17-18 meses</option>
                                                <option value="9" @if($opcion == 9) selected @endif>Pagados entre 18-19 meses</option>
                                                <option value="10" @if($opcion == 10) selected @endif>Pagados entre 19-20 meses</option>
                                                <option value="11" @if($opcion == 11) selected @endif>Pagados entre 20-21 meses</option>
                                                <option value="12" @if($opcion == 12) selected @endif>Pagados entre 21-22 meses</option>
                                                <option value="13" @if($opcion == 13) selected @endif>Pagados entre 22-23 meses</option>
                                                <option value="14" @if($opcion == 14) selected @endif>Pagados entre 23-24 meses</option>
                                            </select>
                                        </div>
                                    </div>
                                <div class="col-1">
                                    <button class="btn btn-outline-success" name="btnSubmitSeguimiento" id="btnSubmitSeguimiento" type="submit">Filtrar</button>
                                </div>
                                <div class="col-1" id="spCargando">
                                    <div class="d-flex justify-content-center">
                                        <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 25px;" role="status">
                                            <span class="visually-hidden"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 10px;">
            <div class="col-4">
                <button type="button" class="btn btn-primary">
                    Total registros <span class="badge bg-secondary">{{sizeof($contratosLiquidados)}}</span>
                </button>
                @if(count($contratosLiquidados)>0)
                    <a href="#" id="btnExportarExcel" onclick="exportarAExcel('Reporte Seguimiento Paciente','tablaReportePagadosSeguimiento');" style="text-decoration:none; color:black; padding-left: 15px;">
                        <button type="button" class="btn btn-success"> Exportar </button>
                    </a>
                @endif
            </div>
            <div class="col-6"></div>
            <div class="col-2">
                <i class="fa-solid fa-location-dot" data-toggle="modal" data-target="#modalgooglemaps" style="cursor: pointer" id="btnCrearMarcadores"
                   onclick="crearMarcadoresReportes('{{json_encode($contratosLiquidados)}}')";>Ver Mapa</i>
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
                        <div id="simbologia" style="background-color: white; margin-right: 20px; margin-top: 60px; font-size: 12px; font-weight: bold;text-align: center;">Información</div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-success" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="contenedortblReportes" style="max-height: 600px; overflow-y: auto; width: 100%; overflow-x: auto; margin-top: 10px;">
            <table class="table table-bordered table-striped table-general table-sm" id="tablaReportePagadosSeguimiento">
                <thead>
                <tr>
                    <th scope="col">FECHA VENTA</th>
                    <th scope="col">ZONA</th>
                    <th scope="col">CONTRATO</th>
                    <th scope="col">LOCALIDAD</th>
                    <th scope="col">ENTRE CALLES</th>
                    <th scope="col">COLONIA</th>
                    <th scope="col">CALLE</th>
                    <th scope="col">NUMERO</th>
                    <th scope="col">NOMBRE</th>
                    <th scope="col">TELEFONO</th>
                </tr>
                </thead>
                <tbody>
                @if(!is_null($contratosLiquidados) && count($contratosLiquidados)>0)
                    @foreach($contratosLiquidados as $contratoliquidado)
                        <tr>
                            <td align='center'>{{$contratoliquidado->FECHAVENTA}}</td>
                            <td align='center'>{{$contratoliquidado->ZONA}}</td>
                            <td align='center'>{{$contratoliquidado->CONTRATO}}</td>
                            <td align='center'>{{$contratoliquidado->LOCALIDAD}}</td>
                            <td align='center'>{{$contratoliquidado->ENTRECALLES}}</td>
                            <td align='center'>{{$contratoliquidado->COLONIA}}</td>
                            <td align='center'>{{$contratoliquidado->CALLE}}</td>
                            <td align='center'>{{$contratoliquidado->NUMERO}}</td>
                            <td align='center'>{{$contratoliquidado->NOMBRE}}</td>
                            <td align='center'>{{$contratoliquidado->TELEFONO}}</td>
                        </tr>
                    @endforeach
                @endif
                @if(count($contratosLiquidados) == 0)
                    <tr>
                        <th style="text-align: center;" colspan="9">SIN REGISTROS
                        </th>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
