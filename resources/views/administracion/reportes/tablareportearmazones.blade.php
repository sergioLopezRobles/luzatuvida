@extends('layouts.app')
@section('titulo','Reporte armazones'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2 style="text-align: left; color: #0AA09E">Armazones</h2>
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
                    <form id="frmFranquiciaNueva" action="{{route('filtrareportearmazones',$idFranquicia)}}"
                          enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                        @csrf
                    <div class="row">
                        <input type="hidden" value="{{$idFranquicia}}" id="idFranquiciaActual" name="idFranquiciaActual">
                            <div class="col-3">
                                <label for="armazonSeleccionada">Armazones</label>
                                <div class="form-group">
                                    <select name="armazonSeleccionada"
                                            class="form-control"
                                            id="armazonSeleccionada">
                                        @if(count($armazones) > 0)
                                            <option value="" selected>Seleccionar armazon</option>
                                            @foreach($armazones as $armazon)
                                                <option
                                                    value="{{$armazon->id}}"
                                                    {{ isset($armazonSeleccionada) ? ($armazonSeleccionada == $armazon->id ? 'selected' : '' ) : '' }}>{{$armazon->nombre}} | {{$armazon->color}}
                                                </option>
                                            @endforeach
                                        @else
                                            <option selected>Sin registros</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-1">
                                <button class="btn btn-outline-success" name="btnSubmit"
                                        type="submit">Aplicar
                                </button>
                            </div>
                            <div class="col-1"></div>
                            <div class="col-2">
                            <div class="form-group">
                                <label>Total de piezas</label>
                                <input type="text" name="totalPiezas"
                                       class="form-control" readonly value="{{$totalPiezas}}">
                            </div>
                            </div>
                            <div class="col-2">
                            <div class="form-group">
                                <label>Total piezas vendidas</label>
                                <input type="text" name="totalPiezasVendidas"
                                       class="form-control" readonly value="{{$totalPiezasVendidas}}">
                            </div>
                            </div>
                            <div class="col-2">
                            <div class="form-group">
                                <label>Total de piezas restantes</label>
                                <input type="text" name="totalPiezasRestantes"
                                       class="form-control" readonly value="{{$totalPiezasRestantes}}">
                            </div>
                            </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <button type="button" style="margin-left: 15px; margin-top: 10px;" class="btn btn-primary">
            Total registros <span class="badge bg-secondary" id="totalRegistros">{{$totalRegistros}}</span>
        </button>
    </div>
    <table class="table-bordered table-striped table-general table-sm" style="margin-top: 20px;">
        <thead>
        <tr>
            <th style="text-align:center;" scope="col">CONTRATO</th>
            <th style="text-align:center;" scope="col">FECHA CREACION</th>
            <th style="text-align:center;" scope="col">ESTATUS</th>
            <th style="text-align:center;" scope="col">HISTORIAL CLINICO</th>
            <th style="text-align:center;" scope="col">ARMAZONES</th>
        </tr>
        </thead>
        <tbody>
        @if(sizeof($armazonContratos) > 0)
            @foreach($armazonContratos as $armazonContrato)
                <tr>
                    <td align='center'>{{$armazonContrato->id}}</td>
                    <td align='center'>{{$armazonContrato->created_at}}</td>
                    @switch($armazonContrato->estatus_estadocontrato)
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
                            <button name="estatusactual" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">ENTREGADO</button>
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
                            <button name="estatusactual"  type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">APROBADO</button>
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
                    <td align='center'>{{$armazonContrato->totalhistoriales}}</td>
                    <td align='center'>{{$armazonContrato->totalarmazones}}</td>
                </tr>
            @endforeach
        @else
        <tr>
            <th style="text-align: center;" colspan="5"> SIN REGISTROS </th>
        </tr>
        @endif
        </tbody>
    </table>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
