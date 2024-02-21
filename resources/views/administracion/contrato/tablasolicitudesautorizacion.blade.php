@extends('layouts.app')
@section('titulo','Solicitudes'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2 style="text-align: left; color: #0AA09E">Solicitud de autorizaciones</h2>
    @if(Auth::user()->rol_id == 7 || Auth::user()->rol_id == 16)
       <div class="col-5">
           <i class="bi bi-square-fill" style="color: rgba(255,15,0,0.17); font-size: 20px;"></i> <label>Solicitud de ARMAZÓN con menos de 10 piezas en inventario.</label>
       </div>
    @endif
    <table id="tablaContratos" class="table-bordered table-striped table-sm" style=" width: 100%; overflow-x: auto;margin-top: 10px; margin-top: 25px;">
        <thead>
        <tr>
            <th  style =" text-align:center; text-align:center; font-size: 11px; background: white;position: sticky; top: 0px; padding: 5px;box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                 scope="col">SUCURSAL</th>
            <th  style ="text-align:center; text-align:center; font-size: 11px; background: white;position: sticky; top: 0px; padding: 5px;box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            white-space: nowrap;"
                 scope="col">TIPO DE
                SOLICITUD</th>
            <th  style ="text-align:center; text-align:center; font-size: 11px; background: white;position: sticky; top: 0px; padding: 5px;box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                 scope="col">CONTRATO</th>
            <th  style ="text-align:center; text-align:center; font-size: 11px; background: white;position: sticky; top: 0px; padding: 5px;box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            white-space: nowrap;"
                 scope="col">FECHA
                CREACIÓN CONTRATO</th>
            <th  style ="text-align:center; text-align:center; font-size: 11px; background: white;position: sticky; top: 0px; padding: 5px;box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            white-space: nowrap;"
                 scope="col">FECHA
                CREACIÓN SOLICITUD</th>
            <th  style ="text-align:center; text-align:center; font-size: 11px; background: white;position: sticky; top: 0px; padding: 5px;box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            white-space: nowrap;"
                 scope="col">ESTADO
                CONTRATO</th>
            @if(Auth::user()->rol_id != 16)
            <th  style ="text-align:center; text-align:center; font-size: 11px; background: white;position: sticky; top: 0px; padding: 5px;box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            white-space: nowrap;"
                 scope="col">TOTAL DE CONTRATO</th>
            <th  style ="text-align:center; text-align:center; font-size: 11px; background: white;position: sticky; top: 0px; padding: 5px;box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            white-space: nowrap;"
                 scope="col">SALDO CONTRATO</th>
            @endif
            <th  style ="text-align:center; text-align:center; font-size: 11px; background: white;position: sticky; top: 0px; padding: 5px;box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            white-space: nowrap;"
                 scope="col">USUARIO
                DE SOLICITUD</th>
            <th  style ="text-align:center; text-align:center; font-size: 11px; background: white;position: sticky; top: 0px; padding: 5px;box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                 scope="col">MENSAJE</th>
            <th  style ="text-align:center; text-align:center; font-size: 11px; background: white;position: sticky; top: 0px; padding: 5px;box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                 scope="col">ACCIÓN</th>
            @if(Auth::user()->rol_id == 7)
                <th  style ="text-align:center; text-align:center; font-size: 11px; background: white;position: sticky; top: 0px; padding: 5px;box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">VER IMAGENES</th>
            @endif
            @if(Auth::user()->rol_id == 16)
                <th  style ="text-align:center; text-align:center; font-size: 11px; background: white;position: sticky; top: 0px; padding: 5px;box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">VER IMAGENES</th>
                <th  style ="text-align:center; text-align:center; font-size: 11px; background: white;position: sticky; top: 0px; padding: 5px;box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">TICKET</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @if(!is_null($solicitudesAutorizacion) && count($solicitudesAutorizacion)>0)
            @foreach($solicitudesAutorizacion as $solicitud)
                @if($solicitud->tipo == 13)
                    <tr>
                        <td align='center'  style="font-size: 10px; white-space: nowrap;">NO APLICA</td>
                        <td align='center' style="font-size: 10px;">ARMAZÓN BAJA</td>
                        <td align='center'  style="font-size: 10px; white-space: nowrap;">NO APLICA</td>
                        <td align='center'  style="font-size: 10px; white-space: nowrap;">NO APLICA</td>
                        <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$solicitud->fecha_solicitud}}</td>
                        <td align='center' style="font-size: 10px;">NO APLICA</td>
                        @if(Auth::user()->rol_id != 16)
                            <td align='center' style="font-size: 10px;">NO APLICA</td>
                            <td align='center' style="font-size: 10px;">NO APLICA</td>
                        @endif
                        <td align='center' style="font-size: 10px; white-space: nowrap;">{{$solicitud->usuario_solicitud}}</td>
                        <td style="font-size: 10px; text-align: justify;">{{$solicitud->mensaje}}</td>
                        <td align='center'>
                            <div style="padding-bottom: 10px;"><a class="btn btn-outline-success btn-sm" href="{{route('solicitudarmazonbajarechazarautorizar',[$solicitud->indice,'1'])}}">AUTORIZAR</a></div>
                            <div style="padding-bottom: 10px;"><a class="btn btn-outline-danger btn-sm" href="{{route('solicitudarmazonbajarechazarautorizar',[$solicitud->indice,'0'])}}">RECHAZAR</a></div>
                        </td>
                        @if(Auth::user()->rol_id == 7)
                            <th  style ="text-align:center; text-align:center; font-size: 11px; background: white;" scope="col">
                                <div><a class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#fotossolicitud" onclick="mostrarFotosSolicitud('{{asset($solicitud->fotofrente)}}', '{{asset($solicitud->fotoatras)}}', '{{asset($solicitud->fotolado1)}}', '{{asset($solicitud->fotolado2)}}')">FOTOS</a></div>
                            </th>
                        @endif
                        @if(Auth::user()->rol_id == 16)
                            <th  style ="text-align:center; text-align:center; font-size: 11px; background: white;" scope="col">
                                <div><a class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#fotossolicitud" onclick="mostrarFotosSolicitud('{{asset($solicitud->fotofrente)}}', '{{asset($solicitud->fotoatras)}}', '{{asset($solicitud->fotolado1)}}', '{{asset($solicitud->fotolado2)}}')">FOTOS</a></div>
                            </th>
                            <th  style ="text-align:center; text-align:center; font-size: 11px; background: white;" scope="col"></th>
                        @endif
                    </tr>
                @else
                    <tr @if((Auth::user()->rol_id == 7 || Auth::user()->rol_id == 16) && (($solicitud->tipo == 8 || $solicitud->tipo == 9 || $solicitud->tipo == 10) && $solicitud->piezas <= 10)) style="background-color: rgba(255,15,0,0.17)" @endif>
                        <td align='center'  style="font-size: 10px; white-space: nowrap;">{{$solicitud->sucursal}}</td>
                        @switch($solicitud->tipo)
                            @case(0)
                                <td align='center' style="font-size: 10px;">GARANTIA</td>
                                @break
                            @case(1)
                                <td align='center' style="font-size: 10px;">CANCELAR CONTRATO</td>
                                @break
                            @case(2)
                                <td align='center' style="font-size: 10px;">CAMBIO DE PRECIO</td>
                                @break
                            @case(4)
                                <td align='center' style="font-size: 10px;">CAMBIO DE PAQUETE</td>
                                @break
                            @case(6)
                                <td align='center' style="font-size: 10px;">TRASPASO CONTRATO</td>
                                @break
                            @case(7)
                                <td align='center' style="font-size: 10px;">ABONO MINIMO</td>
                                @break
                            @case(8)
                                <td align='center' style="font-size: 10px;">ARMAZÓN</td>
                                @break
                            @case(9)
                                <td align='center' style="font-size: 10px;">ARMAZÓN POLIZA</td>
                                @break
                            @case(10)
                                <td align='center' style="font-size: 10px;">ARMAZÓN POR DEFECTO DE FÁBRICA</td>
                                @break
                            @case(11)
                                <td align='center' style="font-size: 10px;">CONTRATO LIO/FUGA</td>
                                @break
                            @case(12)
                                <td align='center' style="font-size: 10px;">SUPERVISAR CONTRATO</td>
                                @break
                            @case(14)
                                <td align='center' style="font-size: 10px;">COBRANZA/SUPERVISOR</td>
                            @break
                            @case(15)
                                <td align='center' style="font-size: 10px;">LISTA NEGRA</td>
                                @break
                            @case(16)
                                <td align='center' style="font-size: 10px;">PROMOCION EMPLEADO</td>
                                @break
                        @endswitch
                        <td align='center' style="font-size: 10px;">{{$solicitud->id_contrato}}</td>
                        <td align='center' style="font-size: 10px;">@if($solicitud->fecha_contrato != null) {{\Carbon\Carbon::parse($solicitud->fecha_contrato)->format('Y-m-d')}} @else NO APLICA @endif</td>
                        <td align='center' style="font-size: 10px;">{{$solicitud->fecha_solicitud}}</td>
                        @switch($solicitud->estado_contrato)
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
                                    <button name="estatusactual"  type="button" class="btn btn-secondary btn" style="color:#FEFEFE;">PAGADO</button>
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
                            @case(1000)
                                <td align='center' style="font-size: 10px;"> NO APLICA </td>
                            @break
                        @endswitch
                        @if(Auth::user()->rol_id != 16)
                        <td align='center' style="font-size: 10px;">@if($solicitud->tipo != 14) ${{$solicitud->total}} @else NO APLICA @endif</td>
                        <td align='center' style="font-size: 10px;">@if($solicitud->tipo != 14) ${{$solicitud->saldo}} @else NO APLICA @endif</td>
                        @endif
                        <td align='center' style="font-size: 10px; white-space: nowrap;">{{$solicitud->usuario_solicitud}}</td>
                        <td style="font-size: 10px; text-align: justify;">{{$solicitud->mensaje}}</td>
                        <td align='center'>
                            @if($solicitud->tipo == 14)
                                <div style="padding-bottom: 10px;"><a class="btn btn-outline-success btn-sm" href="{{route('actualizarestadoautorizacioncambiocobranza',[$idFranquicia,$solicitud->indice,1])}}">AUTORIZAR</a></div>
                                <div style="padding-bottom: 10px;"><a class="btn btn-outline-danger btn-sm" href="{{route('actualizarestadoautorizacioncambiocobranza',[$idFranquicia,$solicitud->indice,2])}}">RECHAZAR</a></div>
                            @else
                                <div style="padding-bottom: 10px;"><a class="btn btn-outline-success btn-sm" href="{{route('autorizarcontrato',[$solicitud->id_contrato,$solicitud->indice])}}">AUTORIZAR</a></div>
                                <div style="padding-bottom: 10px;"><a class="btn btn-outline-danger btn-sm" href="{{route('rechazarcontrato',[$solicitud->id_contrato,$solicitud->indice])}}">RECHAZAR</a></div>
                            @endif
                        </td>
                        @if(Auth::user()->rol_id == 7 || Auth::user()->rol_id == 16)
                            <td align='center'></td>
                        @endif
                        @if(Auth::user()->rol_id == 16 && ($solicitud->tipo == 8 || $solicitud->tipo == 9 || $solicitud->tipo == 10))
                            <td align='center'>
                                <div><a class="btn btn-outline-info btn-sm" onclick="imprimirTicketSolicitudArmazon('{{$solicitud->sucursal}}',
                                                                                   '{{$solicitud->id_contrato}}', '{{$solicitud->usuario_solicitud}}',
                                                                                   '{{\Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('Y-m-d')}}',
                                                                                   '{{$solicitud->armazon}}' , '{{$solicitud->color}}' , '{{$solicitud->tipo}}',
                                                                                   '{{$solicitud->observaciones}}')">IMPRIMIR</a></div>
                            </td>
                        @endif
                    </tr>
                @endif
            @endforeach
        @else
            <tr>
                <td align='center' style="font-size: 10px;" @if(Auth::user()->rol_id == 16) colspan="10" @else colspan="12" @endif>Sin registros</td>
            </tr>
        @endif
        </tbody>
    </table>
    </div>

    <!--Ventana modal para carrucel de imagenes solicitud de armazon baja -->
    <div class="modal fade" id="fotossolicitud" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #0AA09E; color: white;"><b>Fotos del armazón</b></div>
                <div class="modal-body">
                        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                <li data-target="#carouselExampleIndicators" data-slide-to="1" class="active"></li>
                                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                                <li data-target="#carouselExampleIndicators" data-slide-to="3"></li>
                                <li data-target="#carouselExampleIndicators" data-slide-to="4"></li>
                            </ol>
                            <div class="carousel-inner">
                                <div class="carousel-item active" id="divFotoFrente"></div>
                                <div class="carousel-item" id="divFotoAtras"></div>
                                <div class="carousel-item" id="divFotoLado1"></div>
                                <div class="carousel-item" id="divFotoLado2"></div>
                            </div>
                            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only" style="background: black">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-ok" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
