@extends('layouts.app')
@section('titulo','Reporte productos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2 style="text-align: left; color: #0AA09E">Solicitudes de armazón</h2>
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
                        <form action="{{route('listareporteproductosfiltrar',$idFranquicia)}}"
                              enctype="multipart/form-data" method="POST"
                              onsubmit="btnSubmit.disabled = true;">
                            @csrf
                            <div class="row">
                                @if(Auth::user()->rol_id == 7)
                                    <div class="col-3">
                                        <label for="franquiciaSeleccionada">Sucursal</label>
                                        <div class="form-group">
                                            <select name="franquiciaSeleccionada"
                                                    class="form-control">
                                                @if(count($franquicias) > 0)
                                                    <option value="" selected>Todas las sucursales</option>
                                                    @foreach($franquicias as $franquicia)
                                                        <option value="{{$franquicia->id}}" @if($franquiciaSeleccionada == $franquicia->id) selected @endif>{{$franquicia->ciudad}}</option>
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                @endif
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
                                <div class="col-3">
                                    <button class="btn btn-outline-success" name="btnSubmit" type="submit">Filtrar</button>
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
                    Total registros <span class="badge bg-secondary">{{sizeof($solicitudesArmazones)}}</span>
                </button>
                @if(count($solicitudesArmazones)>0)
                    <a href="#" id="btnExportarExcel" onclick="exportarAExcel('Reporte solicitud de armazon','tablaReporteProductos');" style="text-decoration:none; color:black; padding-left: 15px;">
                        <button type="button" class=" btn btn-success" style="margin-bottom: 10px;"> Exportar </button>
                    </a>
                @endif
            </div>
        </div>
        <div class="contenedortblReportes">
            <table class="table-bordered table-striped table-general table-sm" style="margin-top: 20px;" id="tablaReporteProductos" name="tablaReporteProductos">
                <thead>
                <tr>
                    <th style="text-align:center;" scope="col">SUCURSAL</th>
                    <th style="text-align:center;" scope="col">TIPO DE SOLICITUD</th>
                    <th style="text-align:center;" scope="col">ESTATUS</th>
                    <th style="text-align:center;" scope="col">CONTRATO</th>
                    <th style="text-align:center;" scope="col">FECHA CREACION CONTRATO</th>
                    <th style="text-align:center;" scope="col">FECHA CREACIÓN SOLICITUD</th>
                    <th style="text-align:center;" scope="col">ESTADO CONTRATO</th>
                    <th style="text-align:center;" scope="col">USUARIO DE SOLICITUD</th>
                    <th style="text-align:center;" scope="col">MENSAJE</th>
                </tr>
                </thead>
                <tbody>
                @if($solicitudesArmazones != null && sizeof($solicitudesArmazones) > 0)
                    @foreach($solicitudesArmazones as $armazon)
                        <tr>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$armazon->sucursal}}</td>
                            @switch($armazon->tipo)
                                @case(8)
                                    <td align='center' style="font-size: 10px;">ARMAZÓN</td>
                                    @break
                                @case(9)
                                    <td align='center' style="font-size: 10px;">ARMAZÓN POLIZA</td>
                                    @break
                                @case(10)
                                    <td align='center' style="font-size: 10px;">ARMAZÓN POR DEFECTO DE FÁBRICA</td>
                                    @break
                            @endswitch
                            @switch($armazon->estatus)
                                @case(0)
                                    <td align='center' style="font-size: 10px;">PENDIENTE</td>
                                    @break
                                @case(1)
                                    <td align='center' style="font-size: 10px;">AUTORIZADA</td>
                                    @break
                                @case(2)
                                    <td align='center' style="font-size: 10px;">RECHAZADA</td>
                                    @break
                            @endswitch
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$armazon->id_contrato}}</td>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$armazon->fecha_contrato}}</td>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$armazon->fecha_solicitud}}</td>
                            @switch($armazon->estado_contrato)
                                @case(0)
                                    <td align='center'>
                                        <button name="estatusactual" type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">NO TERMINADO</button>
                                    </td>
                                    @break
                                @case(1)
                                    <td align='center'>
                                        <button name="estatusactual" type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">TERMINADO</button>
                                    </td>
                                    @break
                                @case(2)
                                    <td align='center'>
                                        <button name="estatusactual" type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">ENTREGADO</button>
                                    </td>
                                    @break
                                @case(3)
                                    <td align='center'>
                                        <button name="estatusactual" type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">PRE-CANCELADO</button>
                                    </td>
                                    @break
                                @case(4)
                                    <td align='center'>
                                        <button name="estatusactual"  type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">ABONO ATRASADO</button>
                                    </td>
                                    @break
                                @case(5)
                                    <td align='center'>
                                        <button name="estatusactual"  type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">PAGADO</button>
                                    </td>
                                    @break
                                @case(6)
                                    <td align='center'>
                                        <button name="estatusactual"  type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">CANCELADO</button>
                                    </td>
                                    @break
                                @case(7)
                                    <td align='center'>
                                        <button name="estatusactual"  type="button" class="btn btn-secondary btn" style="color:#FEFEFE;">APROBADO</button>
                                    </td>
                                    @break
                                @case(8)
                                    <td align='center'>
                                        <button name="estatusactual"  type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">RECHAZADO</button>
                                    </td>
                                    @break
                                @case(9)
                                    <td align='center'>
                                        <button name="estatusactual"  type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">EN PROCESO DE APROBACIÓN</button>
                                    </td>
                                    @break
                                @case(10)
                                    <td align='center'>
                                        <button name="estatusactual"  type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">MANUFACTURA</button>
                                    </td>
                                    @break
                                @case(11)
                                    <td align='center'>
                                        <button name="estatusactual"  type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">EN PROCESO DE ENVIO</button>
                                    </td>
                                    @break
                                @case(12)
                                    <td align='center'>
                                        <button name="estatusactual" type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">ENVIADO</button>
                                    </td>
                                    @break
                                @case(14)
                                    <td align='center'>
                                        <button name="estatusactual" type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">LIO/FUGA</button>
                                    </td>
                                    @break
                                @case(15)
                                    <td align='center'>
                                        <button name="estatusactual" type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">SUPERVISIÓN</button>
                                    </td>
                                    @break
                            @endswitch
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$armazon->usuario_solicitud}}</td>
                            <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$armazon->mensaje}}</td>

                        </tr>
                    @endforeach
                @else

                @endif

                </tbody>
            </table>
        </div>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
