@extends('layouts.app')
@section('titulo','Contratos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <!-- {{isset($errors)? var_dump($errors) : ''}} -->
        <!---Leyenda de lista negra-->
        @if($contratoListaNegra != null && $contratoListaNegra[0]->estado != 2)
            <div class="row mb-2" style="display: flex; flex-direction: row-reverse">
                <div class="col-2">
                        <input type="text" style="background-color:#050505;color:#FFFFFF;text-align:center" class="form-control" readonly value="LISTA NEGRA">
                </div>
            </div>
        @endif
        <form action="{{route('actualizarpagosadelantarcontrato',[$idFranquicia,$idContrato])}}"
              method="POST">
            @csrf
            <div class="row">
                <div class="col-3">
                    @if($contrato[0]->idcontratorelacion == null)
                        <h2>@lang('mensajes.mensajecontrato') @if($contrato[0]->id_promocion > 0)
                                - Principal
                            @endif</h2>
                    @else
                        <h2>@lang('mensajes.mensajecontrato') - Secundario</h2>
                    @endif
                </div>
                @if($contrato[0]->estatus_estadocontrato == 12 || $contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 4
                        || (($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9
                            || $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11) && $tieneHistorialGarantia != null))
                    <div class="col-5"></div>
                    <div class="col-2">
                        <div class="custom-control custom-switch" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" name="pagosadelantar" id="pagosadelantar"
                                   @if($contrato[0]->pagosadelantar == 1) checked @endif
                                   onclick="eventactualizarpagosadelantarcontrato(event)">
                            <label class="custom-control-label" for="pagosadelantar" style="font-size: 20px">Adelantos</label>
                        </div>
                    </div>
                @else
                    <div class="col-7"></div>
                @endif
                <div class="col-2">
                    @switch($contrato[0]->estatus_estadocontrato)
                        @case(0)
                            <input type="text" style="background-color:#6c757d;color:#FFFFFF;text-align:center"
                                   name="estatuscontrato"
                                   class="form-control" readonly value="NO TERMINADO">
                            @break
                        @case(1)
                            <input type="text" style="background-color:#5bc0de;color:#FFFFFF;text-align:center"
                                   name="estatuscontrato"
                                   class="form-control" readonly value="TERMINADO">
                            @break
                        @case(2)
                            <input type="text" style="background-color:#0275d8;color:#FFFFFF;text-align:center"
                                   name="estatuscontrato"
                                   class="form-control" readonly value="ENTREGADO">
                            @break
                        @case(3)
                            <input type="text" style="background-color:#ea9999;color:#FFFFFF;text-align:center"
                                   name="estatuscontrato"
                                   class="form-control" readonly value="PRE-CANCELADO">
                            @break
                        @case(4)
                            @if($contrato[0]->dias <= 10)
                                <input type="text"
                                       style="background-color:#fff2cc;color:#000000;text-align:center"
                                       name="estatuscontrato" class="form-control"
                                       readonly value="ABONO ATRASADO">
                            @endif
                            @if($contrato[0]->dias >= 11 && $contrato[0]->dias <= 20)
                                <input type="text"
                                       style="background-color:#fce5cd;color:#000000;text-align:center"
                                       name="estatuscontrato"
                                       class="form-control" readonly
                                       value="ABONO ATRASADO">
                            @endif
                            @if($contrato[0]->dias > 20)
                                <input type="text" style="background-color:#f4cccc;color:#000000;text-align:center"
                                       name="estatuscontrato" class="form-control" readonly value="ABONO ATRASADO">
                            @endif
                            @break
                        @case(5)
                            <input type="text" style="background-color:#5cb85c;color:#FFFFFF;text-align:center"
                                   name="estatuscontrato" class="form-control" readonly value="PAGADO">
                            @break
                        @case(6)
                            <input type="text" style="background-color:#ff0000;color:#FFFFFF;text-align:center"
                                   name="estatuscontrato" class="form-control" readonly value="CANCELADO">
                            @break
                        @case(7)
                            <input type="text" style="background-color:#5cb85c;color:#FFFFFF;text-align:center"
                                   name="estatuscontrato" class="form-control" readonly value="APROBADO">
                            @break
                        @case(8)
                            <input type="text" style="background-color:#ff0000;color:#FFFFFF;text-align:center"
                                   name="estatuscontrato"
                                   class="form-control" readonly value="RECHAZADO">
                            @break
                        @case(9)
                            <input type="text" style="background-color:#f0cc0a;color:#FFFFFF;text-align:center"
                                   name="estatuscontrato"
                                   class="form-control" readonly value="EN PROCESO DE APROBACION">
                            @break
                        @case(10)
                            <input type="text" style="background-color:#f0cc0a;color:#FFFFFF;text-align:center"
                                   name="estatuscontrato"
                                   class="form-control" readonly value="MANUFACTURA">
                            @break
                        @case(11)
                            <input type="text" style="background-color:#f0cc0a;color:#FFFFFF;text-align:center"
                                   name="estatuscontrato"
                                   class="form-control" readonly value="EN PROCESO DE ENVIO">
                            @break
                        @case(12)
                            <input type="text" style="background-color:#5bc0de;color:#FFFFFF;text-align:center"
                                   name="estatuscontrato"
                                   class="form-control" readonly value="ENVIADO">
                            @break
                        @case(14)
                            <input type="text" style="background-color:#ff0000;color:#FFFFFF;text-align:center"
                                   name="estatuscontrato" class="form-control" readonly value="LIO/FUGA">
                            @break
                        @case(15)
                            <input type="text" style="background-color:#F88F32;color:#FFFFFF;text-align:center"
                                   name="estatuscontrato" class="form-control" readonly value="SUPERVISION">
                            @break
                    @endswitch
                </div>
            </div>
        </form>
        @if($contrato[0]->estatus_estadocontrato <= 1 && Auth::user()->rol_id != 4)
            @if($contrato[0]->idcontratorelacion == null)
                <div class="row">
                    @if($contrato[0]->id_promocion > 0)
                        @if($contrato[0]->estatus_estadocontrato == 1 && (Auth::user()->rol_id == 13 || Auth::user()->rol_id == 12) &&
                        $contratosterminadostodos == null && $contraspadre2[0]->armazones == $contraspadre2[0]->contador)
                            <div class="col-6">
                            </div>
                            <div class="col-6">
                                <a class="btn btn-outline-success btn-block" data-toggle="modal"
                                   data-target="#Abrirconfirmar">SALIR</a>
                            </div>
                        @endif
                        @if($contrato[0]->estatus_estadocontrato <= 1)
                            <div class="col-6">
                            </div>
                            <div class="col-6">
                                @if($contraspadre2[0]->armazones != $contraspadre2[0]->contador || $contratosterminadostodos != null &&
                                $contraspadre2[0]->contador > 1)
                                    <a class="btn btn-outline-success btn-block" data-toggle="modal"
                                       data-target="#Abrirconfirmar">SIGUIENTE
                                        CONTRATO</a>
                                @endif
                                @if($contraspadre2[0]->armazones == 1 && $contraspadre2[0]->contador == 1 &&
                                $contrato[0]->estatus_estadocontrato == 0)
                                    <a class="btn btn-outline-success btn-block" data-toggle="modal"
                                       data-target="#Abrirconfirmar">TERMINAR
                                        CONTRATO</a>
                                @endif
                            </div>
                        @endif
                    @else
                        @if($contrato[0]->id_promocion == null)
                            <div class="col-6">
                            </div>
                            <div class="col-6">
                                @if($contrato[0]->estatus_estadocontrato < 1)
                                    <a
                                        class="btn btn-outline-success btn-block"
                                        data-toggle="modal" data-target="#Abrirconfirmar">TERMINAR
                                        CONTRATO</a>
                                @else
                                    <a class="btn btn-outline-success btn-block" data-toggle="modal"
                                       data-target="#Abrirconfirmar">SALIR
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            @else
                <div class="row">
                    @if($historialesclinicos != null && $contrato[0]->estatus_estadocontrato == 0)
                        <div class="col-6">
                        </div>
                        <div class="col-6">
                            @if($contraspadre5[0]->id_promocion > 0 && $contraspadre5[0]->armazones == $contraspadre5[0]->contador)
                                <a class="btn btn-outline-success btn-block" data-toggle="modal"
                                   data-target="#Abrirconfirmarhijo">TERMINAR CONTRATO</a>
                            @else
                                <a class="btn btn-outline-success btn-block" data-toggle="modal"
                                   data-target="#Abrirconfirmarhijo">SIGUIENTE
                                    CONTRATO</a>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        @endif
        @if((Auth::user()->rol_id != 12 && Auth::user()->rol_id != 13) && $contrato[0]->estatus_estadocontrato == 12)
            <div class="row">
                <div class="col-6">
                </div>
                <div class="col-6">
                    @if($contrato[0]->id_promocion < 1)
                        <a class="btn btn-outline-success btn-block" data-toggle="modal"
                           data-target="#Abrirentregar">PRODUCTO
                            ENTREGADO
                        </a>
                    @elseif($contraspadre2[0]->armazones == $contraspadre2[0]->contador && $contratosterminadostodos == null
                    && $contraspadre2[0]->contador >= 1)
                        <a class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#Abrirentregar">PRODUCTO
                            ENTREGADO
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <div class="row">
            @if(Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8)
                @if(($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 4 ||
                    $contrato[0]->estatus_estadocontrato == 5 || $contrato[0]->estatus_estadocontrato == 12)
                    && ($contrato[0]->estadogarantia != 2))
                    <div class="col-2">
                        <div class="form-group">
                            <label># Contrato</label>
                            <input type="text" name="idcontra" class="form-control" readonly value="{{$contrato[0]->id}}">
                        </div>
                    </div>
                    @if($periodo != null)
                        <div class="col-1">
                            <div class="form-group">
                                <label>Periodo</label>
                                <input type="text" name="idcontra" class="form-control" readonly value="{{$periodo}}">
                            </div>
                        </div>
                    @endif
                    <div @if($contrato[0]->promocion != null && $periodo != null)class="col-1" @else class="col-2"@endif>
                        <div class="form-group">
                            <label>Fecha venta</label>
                            <input type="text" name="fechaCre" class="form-control" readonly
                                   value="{{$contrato[0]->created_at}}">
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="form-group">
                            <label>Total</label>
                            <input type="text" name="totalreal" id="totalreal" class="form-control" readonly
                                   value="$ {{$contrato[0]->totalreal}}">
                        </div>
                    </div>
                    @if($contrato[0]->promocion != null)
                        <div class="col-1">
                            <div class="form-group">
                                <label>Total promocion</label>
                                <input type="text" name="totalpromocion" class="form-control" readonly value="$ {{$contrato[0]->totalpromocion}}">
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="form-group">
                                <label>Total productos</label>
                                @if($contrato[0]->totalproductos != null)
                                    <input type="text" name="totalproductos" class="form-control" readonly value="$ {{$contrato[0]->totalproductos}}">
                                @else
                                    <input type="text" name="totalproductos" class="form-control" readonly value="$ 0">
                                @endif
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="form-group">
                                <label>Total abonos</label>
                                @if($contrato[0]->totalabonos != null)
                                    <input type="text" name="totalabonos" class="form-control" readonly value="$ {{$contrato[0]->totalabonos}}">
                                @else
                                    <input type="text" name="totalabonos" class="form-control" readonly value="$ 0">
                                @endif
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" name="saldo" class="form-control" readonly value="$ {{$contrato[0]->total}}">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="row">
                                @if($solicitudAumentarDisminuir == null || $solicitudAumentarDisminuir[0]->estatus == 1)
                                    <div class="col-12">
                                        <div class="form-group">
                                            <a type="button" class="btn btn-outline-success btn-block"
                                               data-toggle="modal"
                                               data-target="#modalsolicitaraumentardescontar">SOLICITAR CAMBIO DE PRECIO</a>
                                        </div>
                                    </div>
                                @endif
                                @if($solicitudAumentarDisminuir != null)
                                    @if($solicitudAumentarDisminuir[0]->estatus == 0)
                                        <div class="col-12">
                                            <div class="row" style="color: #0AA09E; font-weight: bold; padding-top:10px; padding-left: 15px; margin-top: 30px;">
                                                Solicitud para cambio de precio pendiente.
                                            </div>
                                        </div>
                                    @endif
                                    @if($solicitudAumentarDisminuir[0]->estatus == 2)
                                        <div class="col-12">
                                            <div class="form-group">
                                                <a type="button" class="btn btn-outline-success btn-block"
                                                   data-toggle="modal"
                                                   data-target="#modalsolicitaraumentardescontar">SOLICITAR CAMBIO DE PRECIO</a>
                                                <div class="row" style="color: #ea9999; font-weight: bold; margin-left: 5px;">
                                                    Ultima solicitud para cambio de precio rechazada.
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="col-1">
                            <div class="form-group">
                                <label>Total productos</label>
                                @if($contrato[0]->totalproductos != null)
                                    <input type="text" name="totalproductos" class="form-control" readonly value="$ {{$contrato[0]->totalproductos}}">
                                @else
                                    <input type="text" name="totalproductos" class="form-control" readonly value="$ 0">
                                @endif
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="form-group">
                                <label>Total abonos</label>
                                @if($contrato[0]->totalabonos != null)
                                    <input type="text" name="totalabonos" class="form-control" readonly value="$ {{$contrato[0]->totalabonos}}">
                                @else
                                    <input type="text" name="totalabonos" class="form-control" readonly value="$ 0">
                                @endif
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" name="saldo" class="form-control" readonly value="$ {{$contrato[0]->total}}">
                            </div>
                        </div>
                        <div @if($periodo != null) class="col-3" @else class="col-4" @endif>
                            <div class="row">
                                @if($solicitudAumentarDisminuir == null || $solicitudAumentarDisminuir[0]->estatus == 1)
                                    <div class="col-12">
                                        <div class="form-group">
                                            <a type="button" class="btn btn-outline-success btn-block"
                                               data-toggle="modal"
                                               data-target="#modalsolicitaraumentardescontar">SOLICITAR CAMBIO DE PRECIO</a>
                                        </div>
                                    </div>
                                @endif
                                @if($solicitudAumentarDisminuir != null)
                                    @if($solicitudAumentarDisminuir[0]->estatus == 0)
                                        <div class="col-12">
                                            <div class="row" style="color: #0AA09E; font-weight: bold; padding-top:5px; padding-left: 15px; margin-top: 30px;">
                                                Solicitud para cambio de precio pendiente.
                                            </div>
                                        </div>
                                    @endif
                                    @if($solicitudAumentarDisminuir[0]->estatus == 2)
                                        <div class="col-12">
                                            <div class="form-group">
                                                <a type="button" class="btn btn-outline-success btn-block"
                                                   data-toggle="modal"
                                                   data-target="#modalsolicitaraumentardescontar">SOLICITAR CAMBIO DE PRECIO</a>
                                                <div class="row" style="color: #ea9999; font-weight: bold; margin-left: 5px;">
                                                    Ultima solicitud para cambio de precio rechazada.
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif
                @else
                    <div class="col-2">
                        <div class="form-group">
                            <label># Contrato</label>
                            <input type="text" name="idcontra" class="form-control" readonly value="{{$contrato[0]->id}}">
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Fecha venta</label>
                            <input type="text" name="fechaCre" class="form-control" readonly
                                   value="{{$contrato[0]->created_at}}">
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Total</label>
                            <input type="text" name="totalreal" id="totalreal" class="form-control" readonly
                                   value="$ {{$contrato[0]->totalreal}}">
                        </div>
                    </div>
                    @if($contrato[0]->promocion != null)
                        <div class="col-2">
                            <div class="form-group">
                                <label>Total promocion</label>
                                <input type="text" name="totalpromocion" class="form-control" readonly value="$ {{$contrato[0]->totalpromocion}}">
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="form-group">
                                <label>Total productos</label>
                                @if($contrato[0]->totalproductos != null)
                                    <input type="text" name="totalproductos" class="form-control" readonly value="$ {{$contrato[0]->totalproductos}}">
                                @else
                                    <input type="text" name="totalproductos" class="form-control" readonly value="$ 0">
                                @endif
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="form-group">
                                <label>Total abonos</label>
                                @if($contrato[0]->totalabonos != null)
                                    <input type="text" name="totalabonos" class="form-control" readonly value="$ {{$contrato[0]->totalabonos}}">
                                @else
                                    <input type="text" name="totalabonos" class="form-control" readonly value="$ 0">
                                @endif
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" name="saldo" class="form-control" readonly value="$ {{$contrato[0]->total}}">
                            </div>
                        </div>
                    @else
                        <div class="col-2">
                            <div class="form-group">
                                <label>Total productos</label>
                                @if($contrato[0]->totalproductos != null)
                                    <input type="text" name="totalproductos" class="form-control" readonly value="$ {{$contrato[0]->totalproductos}}">
                                @else
                                    <input type="text" name="totalproductos" class="form-control" readonly value="$ 0">
                                @endif
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label>Total abonos</label>
                                @if($contrato[0]->totalabonos != null)
                                    <input type="text" name="totalabonos" class="form-control" readonly value="$ {{$contrato[0]->totalabonos}}">
                                @else
                                    <input type="text" name="totalabonos" class="form-control" readonly value="$ 0">
                                @endif
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" name="saldo" class="form-control" readonly value="$ {{$contrato[0]->total}}">
                            </div>
                        </div>
                    @endif
                @endif
            @else
                <div class="col-2">
                    <div class="form-group">
                        <label># Contrato</label>
                        <input type="text" name="idcontra" class="form-control" readonly value="{{$contrato[0]->id}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Fecha venta</label>
                        <input type="text" name="fechaCre" class="form-control" readonly
                               value="{{$contrato[0]->created_at}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Total</label>
                        <input type="text" name="totalreal" id="totalreal" class="form-control" readonly
                               value="$ {{$contrato[0]->totalreal}}">
                    </div>
                </div>
                @if($contrato[0]->promocion != null)
                    <div class="col-2">
                        <div class="form-group">
                            <label>Total promocion</label>
                            <input type="text" name="totalpromocion" class="form-control" readonly value="$ {{$contrato[0]->totalpromocion}}">
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="form-group">
                            <label>Total productos</label>
                            @if($contrato[0]->totalproductos != null)
                                <input type="text" name="totalproductos" class="form-control" readonly value="$ {{$contrato[0]->totalproductos}}">
                            @else
                                <input type="text" name="totalproductos" class="form-control" readonly value="$ 0">
                            @endif
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="form-group">
                            <label>Total abonos</label>
                            @if($contrato[0]->totalabonos != null)
                                <input type="text" name="totalabonos" class="form-control" readonly value="$ {{$contrato[0]->totalabonos}}">
                            @else
                                <input type="text" name="totalabonos" class="form-control" readonly value="$ 0">
                            @endif
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Saldo</label>
                            <input type="text" name="saldo" class="form-control" readonly value="$ {{$contrato[0]->total}}">
                        </div>
                    </div>
                @else
                    <div class="col-2">
                        <div class="form-group">
                            <label>Total productos</label>
                            @if($contrato[0]->totalproductos != null)
                                <input type="text" name="totalproductos" class="form-control" readonly value="$ {{$contrato[0]->totalproductos}}">
                            @else
                                <input type="text" name="totalproductos" class="form-control" readonly value="$ 0">
                            @endif
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Total abonos</label>
                            @if($contrato[0]->totalabonos != null)
                                <input type="text" name="totalabonos" class="form-control" readonly value="$ {{$contrato[0]->totalabonos}}">
                            @else
                                <input type="text" name="totalabonos" class="form-control" readonly value="$ 0">
                            @endif
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Saldo</label>
                            <input type="text" name="saldo" class="form-control" readonly value="$ {{$contrato[0]->total}}">
                        </div>
                    </div>
                @endif
            @endif
        </div>
        <div class="row">
            @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 12 || Auth::user()->rol_id == 13)
                <div class="col-4">
                    <div class="form-group">
                        <label>Cliente</label>
                        <input type="text" name="nombreC" class="form-control" readonly value="{{$contrato[0]->nombre}}">
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefonoC" class="form-control" readonly
                               value="{{$contrato[0]->telefono}}">
                    </div>
                </div>
                @if(Auth::user()->rol_id == 4)
                    <div class="col-2">
                        <div class="form-group">
                            <label>Nombre referencia</label>
                            <input type="text" name="nombrereferenciaC" class="form-control" readonly
                                   value="{{$contrato[0]->nombrereferencia}}">
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Teléfono referencia</label>
                            <input type="text" name="telefonoRC" class="form-control" readonly
                                   value="{{$contrato[0]->telefonoreferencia}}">
                        </div>
                    </div>
                @else
                    <div class="col-4">
                        <div class="form-group">
                            <label>Teléfono referencia</label>
                            <input type="text" name="telefonoRC" class="form-control" readonly
                                   value="{{$contrato[0]->telefonoreferencia}}">
                        </div>
                    </div>
                @endif
            @else
                <div class="col-3">
                    <div class="form-group">
                        @if($contrato[0]->alias != null)<label>Nombre / Alias</label>@else<label>Nombre</label>@endif
                        <input type="text" name="nombreC" class="form-control" readonly
                        @if($contrato[0]->alias != null) value="{{$contrato[0]->nombre}} / {{$contrato[0]->alias}}" @else value="{{$contrato[0]->nombre}}" @endif>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Teléfono / Teléfono referencia</label>
                        <input type="text" name="telefonoC" class="form-control" readonly
                               value="{{$contrato[0]->telefono . ' / ' . $contrato[0]->telefonoreferencia}}">
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Creado por</label>
                        <input type="text" name="creadorporC" class="form-control" readonly
                               value="{{$contrato[0]->nombre_usuariocreacion}}">
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Optometrista asignado</label>
                        <input type="text" class="form-control" readonly value="{{$contrato[0]->name}}">
                    </div>
                </div>
            @endif
        </div>
        <div class="row mt-3 mb-3">
            <div class="col-3">
                <table id="tblCobradorAsignado" class="table-bordered table-striped table-general table-sm">
                    <thead>
                    <tr>
                        <th colspan="2">Cobrador asignado a contrato</th>
                    </tr>
                    <tr>
                        <th>Nombre</th>
                        <th>Ultima sincronizacion</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($cobradoresContrato != null)
                        @foreach($cobradoresContrato as $cobrador)
                            <tr>
                                <td style ="text-align:center; white-space: normal;" scope="col">{{$cobrador->name}}</td>
                                <td style ="text-align:center; white-space: normal;" scope="col">@if($cobrador->ultimaconexion != null) {{$cobrador->ultimaconexion}} @else SIN REGISTRO @endif</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="2">SIN REGISTROS</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <h3>Lugar de venta</h3>
        <div class="row">
            <div class="col-2">
                <div class="form-group">
                    <label>Localidad</label>
                    <input type="text" name="localidadC" class="form-control" readonly value="{{$contrato[0]->localidad}}">
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Colonia</label>
                    <input type="text" name="coloniaC" class="form-control" readonly value="{{$contrato[0]->colonia}}">
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Calle</label>
                    <input type="text" name="calleC" class="form-control" readonly value="{{$contrato[0]->calle}}">
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Entre calles</label>
                    <input type="text" name="entrecallesC" class="form-control" readonly
                           value="{{$contrato[0]->entrecalles}}">
                </div>
            </div>
            <div class="col-1">
                <div class="form-group">
                    <label>Número</label>
                    <input type="text" name="numeroC" class="form-control" readonly value="{{$contrato[0]->numero}}">
                </div>
            </div>
        </div>
        <h3>Lugar de cobranza</h3>
        <div class="row">
            <div class="col-2">
                <div class="form-group">
                    <label>Localidad</label>
                    <input type="text" name="localidadCentrega" class="form-control" readonly value="{{$contrato[0]->localidadentrega}}">
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Colonia</label>
                    <input type="text" name="coloniaCentrega" class="form-control" readonly value="{{$contrato[0]->coloniaentrega}}">
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Calle</label>
                    <input type="text" name="calleCentrega" class="form-control" readonly value="{{$contrato[0]->calleentrega}}">
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Entre calles</label>
                    <input type="text" name="entrecallesCentrega" class="form-control" readonly
                           value="{{$contrato[0]->entrecallesentrega}}">
                </div>
            </div>
            <div class="col-1">
                <div class="form-group">
                    <label>Número</label>
                    <input type="text" name="numeroCentrega" class="form-control" readonly value="{{$contrato[0]->numeroentrega}}">
                </div>
            </div>
        </div>

        <div class="row">

            @if(((Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)
                    && ($contrato[0]->estatus_estadocontrato == 0
                        || $contrato[0]->estatus_estadocontrato == 2
                        || $contrato[0]->estatus_estadocontrato == 4
                        || ($contrato[0]->estatus_estadocontrato == 12 && $contrato[0]->fechacobroini == null && $contrato[0]->fechacobrofin == null))))
                <div class="col-4">
                    <form action="{{route('agregarformapago',[$idFranquicia,$idContrato])}}" enctype="multipart/form-data"
                          method="POST" onsubmit="btnSubmit.disabled = true;">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <label for="">FORMA DE PAGO</label>
                                <select class="custom-select {!! $errors->first('formapago','is-invalid')!!}" name="formapago"
                                        id="formapago">
                                    <option selected value='nada'>Seleccionar</option>
                                    @if($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 12)
                                        <option value=0 {{isset($contrato) ? ($contrato[0]->pago == '0' ? 'selected' : '') : ''}}>
                                            Contado
                                        </option>
                                    @endif
                                    <option value=1 {{isset($contrato) ? ($contrato[0]->pago == '1' ? 'selected' : '') : ''}}>
                                        Semanal
                                    </option>
                                    <option value=2 {{isset($contrato) ? ($contrato[0]->pago == '2' ? 'selected' : '') : ''}}>
                                        Quincenal
                                    </option>
                                    <option value=4 {{isset($contrato) ? ($contrato[0]->pago == '4' ? 'selected' : '') : ''}}>
                                        Mensual
                                    </option>
                                </select>
                                @if($errors->has('formapago'))
                                    <div class="invalid-feedback">{{$errors->first('formapago')}}</div>
                                @endif
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">
                                        ACTUALIZAR
                                        FORMA DE PAGO
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <ul>
                                <li style="color: #ea9999">Es recomendable que el cobrador este fuera del contrato en la app movil antes de cualquier cambio en el contrato</li>
                                @if($contrato[0]->pago != '4')
                                    <li style="color: #ea9999">Al cambiar a MENSUAL aceptas que actualizaste las fotos de tarjetas de pensión</li>
                                @endif
                            </ul>
                        </div>
                    </form>
                </div>
                <div class="col-1"></div>
                <div class="col-1"></div>
            @else
                <div @if($contrato[0]->diatemporal != null) class="col-6" @else class="col-9" @endif></div>
            @endif

            @if($contrato[0]->pago != '0' && ($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 4 || $contrato[0]->estatus_estadocontrato == 12))
                <div class="col-3">
                    <form action="{{route('entregarDiaPago',[$idFranquicia,$idContrato])}}" enctype="multipart/form-data"
                          method="GET" onsubmit="btnSubmit.disabled = true;">
                        @csrf
                        <div class="row">
                            <div class="col-8">
                                <label for="diapago">&nbsp;</label>
                                <select class="custom-select {!! $errors->first('diapago','is-invalid')!!}" name="diapago"
                                        id="diapago3"
                                        @if(Auth::user()->rol_id != 6 && Auth::user()->rol_id != 7 && Auth::user()->rol_id != 8) disabled="disabled" @endif>
                                    <option selected value='0'>DIA DE PAGO</option>
                                    <option value='Monday' {{isset($contrato) ? ($contrato[0]->diapago == 'Monday' ? 'selected' : '') : ''}}>Lunes</option>
                                    <option value='Tuesday' {{isset($contrato) ? ($contrato[0]->diapago == 'Tuesday' ? 'selected' : '') : ''}}>Martes
                                    </option>
                                    <option value='Wednesday' {{isset($contrato) ? ($contrato[0]->diapago == 'Wednesday' ? 'selected' : '') : ''}}>
                                        Miercoles
                                    </option>
                                    <option value='Thursday' {{isset($contrato) ? ($contrato[0]->diapago == 'Thursday' ? 'selected' : '') : ''}}>Jueves
                                    </option>
                                    <option value='Friday' {{isset($contrato) ? ($contrato[0]->diapago == 'Friday' ? 'selected' : '') : ''}}>Viernes
                                    </option>
                                    <option value='Saturday' {{isset($contrato) ? ($contrato[0]->diapago == 'Saturday' ? 'selected' : '') : ''}}>Sabado
                                    </option>
                                    <option value='Sunday' {{isset($contrato) ? ($contrato[0]->diapago == 'Sunday' ? 'selected' : '') : ''}}>Domingo
                                    </option>
                                </select>
                                @if($errors->has('diapago'))
                                    <div class="invalid-feedback">{{$errors->first('diapago')}}</div>
                                @endif
                            </div>
                            <div class="col-4" @if(Auth::user()->rol_id != 6 && Auth::user()->rol_id != 7 && Auth::user()->rol_id != 8) hidden @endif>
                                <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">AGREGAR
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            @if($contrato[0]->diatemporal != null)
                <div class="col-2">
                    <div class="form-group">
                        <label>DIA TEMPORAL</label>
                        <input type="date" name="fechadiatemporal" class="form-control" @isset($contrato[0]->diatemporal) value="{{$contrato[0]->diatemporal}}" @endisset>
                    </div>
                </div>
                <div class="col-1">
                    <label for="diatemporal">&nbsp;</label>
                    <a type="button" class="btn btn-outline-danger btn-block" href="{{route('eliminardiatemporal',[$idFranquicia,$idContrato])}}">ELIMINAR</a>
                </div>
            @endif

        </div>
        <hr>

        @if(Auth::user()->rol_id == 4)
            <div class="col-3">
                <h2> @lang('mensajes.mensajehistorialclinico')</h2>
            </div>
            <table id="tablaHistoriales2" class="table table-bordered table-sm">
                @if(sizeof($historialesclinicos)>0)
                    <thead>
                    <tr>
                        <th style=" text-align:center;width:20%;">FECHA ENTREGA</th>
                        <th style=" text-align:center;width:80%;">OBSERVACIONES LABORATORIO</th>
                        <th style=" text-align:center;width:80%;">OBSERVACIONES ITERNO</th>
                    </tr>
                    </thead>
                @endif
                <tbody>
                @foreach($historialesclinicos as $historial)
                    <tr>
                        <td align='center'>{{$historial->fechaentrega}}</td>
                        @if($historial->observaciones != null)
                            <td align='center'>{{$historial->observaciones}}</td>
                        @else
                            <td align='center'>NA</td>
                        @endif
                        @if($historial->observacionesinterno != null)
                            <td align='center'>{{$historial->observacionesinterno}}</td>
                        @else
                            <td align='center'>NA</td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        @if(Auth::user()->rol_id != 4)
            <div class="row">
                <div class="col-3">
                    <h2> @lang('mensajes.mensajehistorialclinico')</h2>
                </div>
                <table id="tablaHistoriales" class="table table-bordered table-striped table-sm">
                    @if(sizeof($historialesclinicos)>0)
                        <thead>
                        <tr>
                            <th style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4); white-space: nowrap;"
                                scope="col">DIAGNOSTICO
                            </th>
                            <th style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4); white-space: nowrap;"
                                scope="col">FECHA VISITA
                            </th>
                            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4); white-space: nowrap;"
                                scope="col">FECHA DE ENTREGA
                            </th>
                            <th style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4); white-space: nowrap;"
                                scope="col">OBSERVACIONES LABORATORIO
                            </th>
                            <th style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4); white-space: nowrap;"
                                scope="col">OBSERVACIONES INTERNO
                            </th>
                            @if($contrato[0]->estatus_estadocontrato == 0 && $contrato[0]->total > 0)
                                <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4); white-space: nowrap;"
                                    scope="col">EDITAR
                                </th>
                            @endif
                        </tr>
                        </thead>
                    @endif
                    <tbody>
                    @foreach($historialesclinicos as $historial)
                        <tr>
                            <td align='center' style="font-size: 11px; white-space: nowrap;">{{$historial->diagnostico}}</td>
                            <td align='center' style="font-size: 11px; white-space: nowrap;">{{$historial->created_at}}</td>
                            <td align='center' style="font-size: 11px; white-space: nowrap;">{{$historial->fechaentrega}}</td>
                            @if($historial->observaciones != null)
                                <td align='center' style="font-size: 11px;">{{$historial->observaciones}}</td>
                            @else
                                <td align='center' style="font-size: 11px;">NA</td>
                            @endif
                            @if($historial->observacionesinterno != null)
                                <td align='center' style="font-size: 11px;">{{$historial->observacionesinterno}}</td>
                            @else
                                <td align='center' style="font-size: 11px;">NA</td>
                            @endif
                            @if($contrato[0]->estatus_estadocontrato == 0 && $contrato[0]->total > 0)
                                <td align='center'><a
                                        href="{{route('actualizarhistorial',[$idFranquicia,$idContrato,$historial->id])}}">
                                        <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i>
                                        </button>
                                    </a></td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <hr>

            <h2> @lang('mensajes.mensajepromociones')</h2>
            <div class="row">
                <div class="col-6">
                    @if($contrato[0]->estatus_estadocontrato == 0)
                        @if($contrato[0]->idcontratorelacion == null && $contrato[0]->id_promocion === null)
                            @if($contrato[0]->total > 0)
                                @if($contrato[0]->enganche < 1)
                                    <form action="{{route('agregarpromocion',[$idFranquicia,$idContrato])}}"
                                          enctype="multipart/form-data" method="GET" onsubmit="btnSubmit.disabled = true;">
                                        @csrf
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for=""><br></label>
                                                    <select
                                                        class="custom-select {!! $errors->first('promocion','is-invalid')!!}"
                                                        name="promocion">
                                                        @if(count($promociones) > 0)
                                                            <option selected value="0">Seleccionar</option>
                                                            @foreach($promociones as $promocion)
                                                                <option
                                                                    value="{{$promocion->id}}">{{$promocion->titulo}}</option>
                                                            @endforeach
                                                        @else
                                                            <option selected value="0">Sin registros</option>
                                                        @endif
                                                    </select>
                                                    @if($errors->has('promocion'))
                                                        <div class="invalid-feedback">{{$errors->first('promocion')}}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <button class="btn btn-outline-success btn-block" name="btnSubmit"
                                                            type="submit">AGREGAR PROMOCIÓN
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            @endif
                        @endif
                    @endif
                </div>
            </div>
            <table id="tablaPromociones" class="table-bordered table-striped table-general table-sm">
                @if(sizeof($promocioncontrato)>0)
                    <thead>
                    <tr>
                        <th style=" text-align:center;" scope="col">ESTADO</th>
                        <th style=" text-align:center;" scope="col">TITULO</th>
                        <th style=" text-align:center;" scope="col">INICIO</th>
                        <th style=" text-align:center;" scope="col">FIN</th>
                        @if($contrato[0]->estatus_estadocontrato == 0)
                            <th style=" text-align:center;" scope="col">ELIMINAR</th>
                        @endif
                        @if($contrato[0]->id_promocion > 0)
                            @if($contraspadre2[0]->armazones == $contraspadre2[0]->contador && $contratosterminadostodos == null)
                                <th style=" text-align:center;" scope="col">QUITAR</th>
                            @endif
                        @else
                            @if($contrato[0]->estatus_estadocontrato > 0)
                                <th style=" text-align:center;" scope="col">QUITAR</th>
                            @endif
                        @endif
                        <!-- <th  style =" text-align:center;" scope="col">EDITAR</th>                                 -->
                    </tr>
                    </thead>
                @endif
                <tbody>
                @foreach($promocioncontrato as $promocion)
                    <tr>
                        @if($promocion->estado == 1)
                            <td align='center'><i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i></td>
                        @else
                            <td align='center'><i class='fas fa-times' style="color:#ffaca6;font-size:25px;"></i></td>
                        @endif
                        <td align='center'>{{$promocion->titulo}}</td>
                        <td align='center'>{{$promocion->inicio}}</td>
                        <td align='center'>{{$promocion->fin}}</td>
                        @if($contrato[0]->estatus_estadocontrato == 0)
                            <td align='center'><a
                                    href="{{route('eliminarPromocion',[$idFranquicia,$promocion->id_contrato, $promocion->id])}}"
                                    name="btnSubmit"
                                    class="btn btn-outline-danger">ELIMINAR</a></td>
                        @endif
                        @if($contrato[0]->id_promocion > 0)
                            @if($contraspadre2[0]->armazones == $contraspadre2[0]->contador && $contratosterminadostodos == null)
                                @if($promocion->estado == 1)
                                    <td align='center'><a
                                            href="{{route('desactivarPromocion',[$idFranquicia,$promocion->id_contrato, $promocion->id])}}"
                                            class="btn btn-outline-warning">QUITAR</a></td>
                                @else
                                    <td align='center'><a
                                            href="{{route('desactivarPromocion',[$idFranquicia,$promocion->id_contrato, $promocion->id])}}"
                                            class="btn btn-outline-primary">ACTIVAR</a></td>
                                @endif
                            @endif
                        @else
                            @if($contrato[0]->estatus_estadocontrato > 0)
                                <td align='center'><a
                                        href="{{route('desactivarPromocion',[$idFranquicia,$promocion->id_contrato, $promocion->id])}}"
                                        class="btn btn-outline-primary">ACTIVAR</a></td>
                            @endif
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
            <hr>
        @endif

        <div class="row">
            <div class="col-1">
                <label>&nbsp;</label>
                <h2>@lang('mensajes.mensajeabonos')</h2>
            </div>
            @if($contrato[0]->subscripcion == null)
                <div class="col-2">
                    <a type="button" href="" class="btn btn-outline-success btn-block" data-toggle="modal"
                       data-target="#AbrirAbono">NUEVO</a>
                </div>
                <div class="col-1"></div>
            @else
                <div class="col-3">
                    <label>&nbsp;</label>
                    <h5 style="color: red;">Ya se encuentra subscrito el contrato a una cuenta</h5>
                </div>
            @endif
            <div class="col-2"></div>

            @if(Auth::user()->id == 1 || Auth::user()->id == 61 || Auth::user()->id == 761)
                <div class="col-3">
                    <form action="{{route('agregarabonominimo',[$idFranquicia,$contrato[0]->id])}}" enctype="multipart/form-data"
                          method="GET" onsubmit="btnSubmit.disabled = true;">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="abonoMinimo">Abono minimo</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="number" name="abonoMinimo" min="150"
                                                   class="form-control {!! $errors->first('abonoMinimo')!!}"
                                                   placeholder="Abono minimo" value="{{$contrato[0]->abonominimo}}">
                                            {!! $errors->first('abonoMinimo','<div class="invalid-feedback">El abono minimo es
                                                                obligatorio, debera ser mayor a 150</div>')!!}
                                        </div>
                                        <div class="col-6" @if(Auth::user()->rol_id != 6 && Auth::user()->rol_id != 7 && Auth::user()->rol_id != 8) hidden @endif>
                                            <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit" style="margin-top: 0px;">ACTUALIZAR</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            @if($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 4 || $contrato[0]->estatus_estadocontrato == 5
                    || ($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9
                        ||  $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11 && $contrato[0]->estadogarantia == 2))
                @isset($fechaultimoabono)
                    <div class="col-3">
                        <form action="{{route('actualizarfechaultimoabonocontrato',[$idFranquicia,$idContrato])}}" enctype="multipart/form-data"
                              method="GET" onsubmit="btnSubmit.disabled = true;">
                            @csrf
                            <div class="row">
                                <div class="col-8">
                                    <div class="form-group">
                                        <label>Ultimo abono</label>
                                        <input type="date" name="fechaultimoabono" class="form-control" @isset($fechaultimoabono) value="{{$fechaultimoabono}}" @endisset>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">ACTUALIZAR
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endisset
            @endif

        </div>
        <table id="tablaAbonos" class="table-bordered table-striped table-general table-sm">
            @if(sizeof($abonos)>0)
                <thead>
                <tr>
                    <th style=" text-align:center;" scope="col">CODIGO</th>
                    <th style=" text-align:center;" scope="col">USUARIO</th>
                    <th style=" text-align:center;" scope="col">ABONO</th>
                    <th style=" text-align:center;" scope="col">FECHA</th>
                    <th style=" text-align:center;" scope="col">TIPO DE ABONO</th>
                    <th style=" text-align:center;" scope="col">FORMA DE PAGO</th>
                    @if(Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)
                        <th style=" text-align:center;" scope="col">FOLIO</th>
                    @endif
                    <th style=" text-align:center;" scope="col">ELIMINAR ABONO</th>
                    <th style=" text-align:center;" scope="col">IMPRIMIR TICKET</th>
                </tr>
                </thead>
            @endif
            <tbody>
            @foreach($abonos as $ab)

                <tr>
                    <td align='center' width="7%">{{$ab->id}}</td>
                    <td align='center'>{{$ab->usuario}}</td>
                    <td align='center'>{{$ab->abono}}</td>
                    <td align='center'>{{$ab->created_at}}</td>
                    @switch($ab->tipoabono)
                        @case(0)
                            @if($ab->atraso > 0)
                                <td align='center'>ATRASO</td>
                            @else
                                <td align='center'>NORMAL</td>
                            @endif
                            @break
                        @case(1)
                            <td align='center'>ENGANCHE</td>
                            @break
                        @case(2)
                            <td align='center'>ENTREGA PRODUCTO</td>
                            @break
                        @case(3)
                            <td align='center'>ABONO PERIODO</td>
                            @break
                        @case(4)
                            <td align='center'>CONTADO ENGANCHE</td>
                            @break
                        @case(5)
                            <td align='center'>CONTADO SIN ENGANCHE</td>
                            @break
                        @case(6)
                            <td align='center'>LIQUIDADO</td>
                            @break
                        @case(7)
                            <td align='center'>PRODUCTO</td>
                            @break
                        @case(8)
                            <td align='center'>CANCELACIÓN</td>
                            @break
                    @endswitch
                    @switch($ab->metodopago)
                        @case(0)
                            <td align='center'>EFECTIVO</td>
                            @break
                        @case(1)
                            <td align='center'>TARJETA</td>
                            @break
                        @case(2)
                            <td align='center'>TRANSFERENCIA</td>
                            @break
                        @case(3)
                            <td align='center'>CANCELACIÓN</td>
                            @break
                    @endswitch
                    @if(Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)
                        @if($ab->folio != null)
                            <td align='center'>{{$ab->folio}}</td>
                        @else
                            <td align='center'>S / F</td>
                        @endif
                    @endif
                    <td align='center'>
                        @if($ab->metodopago == 1)
                            <a class="btn btn-secondary disable btn-sm">N/A</a>
                        @else
                            @if((Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8) && ($ab->tipoabono != 7)
                                    && ($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1
                                        || $contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 4
                                        || $contrato[0]->estatus_estadocontrato == 5 || $contrato[0]->estatus_estadocontrato == 12)
                                    && ($folioOIdUltimoAbonoEliminar != null && ($folioOIdUltimoAbonoEliminar == $ab->id || $folioOIdUltimoAbonoEliminar == $ab->folio
                                    || $fechaHoraUltimoAbonoEliminar == $ab->created_at)))
                                <a href="#" data-href="{{route('eliminarAbono',[$idFranquicia,$idContrato,$ab->id])}}"
                                   data-toggle="modal" data-target="#confirmacion">
                                    <button type="button" class="btn btn-outline-danger disable btn-sm">ELIMINAR</button>
                                </a>
                            @endif
                        @endif
                    </td>
                    <td align='center'>
                        @if((Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8) &&
                            ($folioOIdUltimoAbono != null && ($folioOIdUltimoAbono == $ab->id || $folioOIdUltimoAbono == $ab->folio || $fechaUltimoAbono == $ab->created_at)))
                            <button type="button" class="btn btn-outline-info disable btn-sm" onclick="imprimirTicketAbono()">IMPRIMIR</button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <hr>

        @if(Auth::user()->rol_id != 4)
            @if($contrato[0]->estatus_estadocontrato == 0 ||
                    (Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8 &&
                    ($contrato[0]->estatus_estadocontrato == 2 ||
                     $contrato[0]->estatus_estadocontrato == 4 ||
                      $contrato[0]->estatus_estadocontrato == 5 ||
                       $contrato[0]->estatus_estadocontrato == 12)))
                <div class="row">
                    <div class="col-1">
                        <label>&nbsp;</label>
                        <h2>@lang('mensajes.mensajeproductos')</h2>
                    </div>
                    <div class="col-2">
                        <a type="button" href="" class=" btn btn-outline-success btn-block" data-toggle="modal"
                           data-target="#AbrirPro">NUEVO</a>
                    </div>
                    <table id="tablaCP" class="table-bordered table-striped table-general table-sm">
                        @if(sizeof($contratoproducto)>0)
                            <thead>
                            <tr>
                                <th style=" text-align:center;" scope="col">ESTADO DE SOLICITUD</th>
                                <th style=" text-align:center;" scope="col">PRODUCTO</th>
                                <th style=" text-align:center;" scope="col">PRECIO</th>
                                <th style=" text-align:center;" scope="col">PIEZAS</th>
                                <th style=" text-align:center;" scope="col">TOTAL</th>
                                <th style=" text-align:center;" scope="col">ELIMINAR PRODUCTO</th>
                                <th style=" text-align:center;" scope="col">IMPRIMIR TICKET</th>
                            </tr>
                            </thead>
                        @endif
                        <tbody>

                        @foreach($contratoproducto as $CP)

                            <tr style="@if($CP->id_tipoproducto == 1 && $CP->estadoautorizacion == 0) background-color: rgba(255,15,0,0.17) @endif">
                                @if($CP->id_tipoproducto == 1)
                                    <td align='center'>@if($CP->estadoautorizacion == 0) PENDIENTE @else AUTORIZADO @endif</td>
                                    <td align='center'>Armazon: {{$CP->nombre}} | {{$CP->color}}</td>
                                @else
                                    <td align='center'>NO APLICA</td>
                                    <td align='center'>{{$CP->nombre}}</td>
                                @endif
                                @if($CP->preciop == null)
                                    <td align='center'>$ {{$CP->precio}}</td>
                                @else
                                    <td align='center'>$ {{$CP->preciop}}</td>
                                @endif
                                <td align='center'>{{$CP->piezas}}</td>
                                <td align='center'>$ {{$CP->total}}</td>
                                @if($CP->existeAbono != null && $CP->existeAbono != 1)
                                    <td align='center'>
                                        <a href="#" data-href="{{route('eliminarcontratoproducto',[$idFranquicia,$idContrato,$CP->id])}}"
                                           data-toggle="modal" data-target="#confirm-delete">
                                            <button type="button" class="btn btn-outline-danger disable btn-sm">ELIMINAR</button>
                                        </a>
                                    </td>
                                @else
                                    <td align='center'></td>
                                @endif
                                <td align='center'>
                                    @if($solicitudArmazonTicket != null)
                                        @if($solicitudArmazonTicket[0]->id_armazon == $CP->id_producto && $CP->id_tipoproducto == 1 && $CP->estadoautorizacion == 0)
                                            <div><a class="btn btn-outline-info btn-sm" onclick="imprimirTicketSolicitudArmazon('{{$solicitudArmazonTicket[0]->sucursal}}',
                                                                             '{{$solicitudArmazonTicket[0]->id_contrato}}', '{{$solicitudArmazonTicket[0]->usuario_solicitud}}',
                                                                             '{{\Carbon\Carbon::parse($solicitudArmazonTicket[0]->fecha_solicitud)->format('Y-m-d')}}',
                                                                             '{{$solicitudArmazonTicket[0]->armazon}}' , '{{$solicitudArmazonTicket[0]->color}}' ,
                                                                             '{{$solicitudArmazonTicket[0]->tipo}}', '{{$solicitudArmazonTicket[0]->observaciones}}')">IMPRIMIR</a></div>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
                <hr>

                <!-- modal para productos -->
                <div class="modal fade" id="AbrirPro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                     aria-hidden="true">
                    <form action="{{route('agregarproducto',[$idFranquicia,$idContrato])}}" enctype="multipart/form-data"
                          method="POST" onsubmit="btnSubmit.disabled = true;">
                        @csrf
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ExampleAbrir" style="color: #FFFFFF">Productos</h5>
                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-7">
                                            <div class="form-group">
                                                <label>Elegir un producto</label>
                                                <select name="producto"
                                                        class="form-control {!! $errors->first('producto','is-invalid')!!}"
                                                        placeholder="producto" value="{{ old('producto',0) }}" id="producto">
                                                    <option selected value='nada'>Seleccionar</option>
                                                    @foreach($productos as $pro)
                                                        @if ($pro->piezas > 0)
                                                            @if($pro->preciop == null)
                                                                @if($pro->id_tipoproducto == 1)
                                                                    <option value="{{$pro->id}}">{{$pro->nombre}} | {{$pro->color}} | $ {{ $pro->precio }}
                                                                        | {{$pro->piezas}}pza.
                                                                    </option>
                                                                @else
                                                                    <option value="{{$pro->id}}">{{$pro->nombre}} | $ {{ $pro->precio }}
                                                                        | {{$pro->piezas}}pza.
                                                                    </option>
                                                                @endif
                                                            @else
                                                                @if($pro->id_tipoproducto == 1)
                                                                    <option value="{{$pro->id}}">{{$pro->nombre}} | {{$pro->color}} | Normal :
                                                                        $ {{ $pro->precio }} | Con
                                                                        descuento: $ {{ $pro->preciop }} | {{$pro->piezas}}pza.
                                                                @else
                                                                    <option value="{{$pro->id}}">{{$pro->nombre}} | Normal :
                                                                        $ {{ $pro->precio }} | Con
                                                                        descuento: $ {{ $pro->preciop }} | {{$pro->piezas}}pza.
                                                                    </option>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </select>
                                                {!! $errors->first('producto','<div class="invalid-feedback">Elegir un producto, campo
                                                    obligatorio</div>')!!}
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label># Piezas</label>
                                                <input type="number" name="piezas" id="piezas"
                                                       class="form-control {!! $errors->first('piezas','is-invalid')!!}" min="1"
                                                       placeholder="Numero de piezas" value="{{ old('piezas', 1) }}">
                                                {!! $errors->first('piezas','<div class="invalid-feedback">Elegir una cantidad de
                                                    piezas,
                                                    campo obligatorio</div>')!!}
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
                                    <hr>
                                    <label>Solicitud de armazón</label>
                                    <div style="margin-left: 40px;">
                                        <div class="row" style="margin-bottom: 10px;">
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="opcion"
                                                           id="opcion" value="0" checked>
                                                    <label class="form-check-label" for="opcion">
                                                        Ninguna
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="opcion"
                                                           id="opcion" value="1" @if(!$polizaActiva) disabled onclick="return false;" @endif>
                                                    <label class="form-check-label" for="opcion">
                                                        Con poliza
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <input type="text" name="folioPoliza" id="folioPoliza" @if(!$polizaActiva) readonly @endif
                                                    class="form-control {!! $errors->first('folioPoliza','is-invalid')!!}"
                                                           @if(!$polizaActiva) placeholder="" @else placeholder="Folio poliza" @endif value="{{ old('folioPoliza') }}">
                                                    {!! $errors->first('folioPoliza','<div class="invalid-feedback">Ingresa folio de poliza</div>')!!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-bottom: 15px;">
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="opcion"
                                                           id="opcion" value="2">
                                                    <label class="form-check-label" for="opcion">
                                                        Defecto de fábrica
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-12" style="color: #ea9999;">
                                                Al hacer la solicitud, esta consiente de que solo debe aplicarse cuando recién llego el lente.
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Observaciones</label>
                                                    <textarea rows="3" name="observaciones" id="observaciones" class="form-control" placeholder="Observaciones"> {{old('Observaciones')}} </textarea>
                                                    {!! $errors->first('observaciones','<div class="invalid-feedback">Campo de observaciones obligatorio.</div>')!!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12" style="color: #ea9999;">
                                        Nota: Recuerda que la solicitud se autorizará hasta que llegue el armazón a laboratorio.
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-7"></div>
                                        <div class="col-1" style="margin-top: 10px;">
                                            <label>Total:</label>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <input type="text" name="totalprecioproducto" id="totalprecioproducto" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    @if($solicitudArmazon != null)
                                        @switch($solicitudArmazon[0]->tipo)
                                            @case(8)
                                                @switch($solicitudArmazon[0]->estatus)
                                                    @case(0)
                                                        <div class="col-12" style="color: #0AA09E; font-weight: bold; padding-top:5px; padding-left: 15px;">
                                                            Solicitud de armazón pendiente.
                                                        </div>
                                                        @break
                                                    @case(2)
                                                        <div class="col-12" style="color: #ea9999; font-weight: bold; margin-left: 5px;">
                                                            Ultima solicitud de armazón rechazada.
                                                        </div>
                                                        @break
                                                @endswitch
                                                @break
                                            @case(9)
                                                @switch($solicitudArmazon[0]->estatus)
                                                    @case(0)
                                                        <div class="col-12" style="color: #0AA09E; font-weight: bold; padding-top:5px; padding-left: 15px;">
                                                            Solicitud de armazón por poliza pendiente.
                                                        </div>
                                                        @break
                                                    @case(2)
                                                        <div class="col-12" style="color: #ea9999; font-weight: bold; margin-left: 5px;">
                                                            Ultima solicitud de armazón por poliza rechazada.
                                                        </div>
                                                        @break
                                                @endswitch
                                                @break
                                            @case(10)
                                                @switch($solicitudArmazon[0]->estatus)
                                                    @case(0)
                                                        <div class="col-12" style="color: #0AA09E; font-weight: bold; padding-top:5px; padding-left: 15px;">
                                                            Solicitud de armazón por defecto de fábrica pendiente.
                                                        </div>
                                                        @break
                                                    @case(2)
                                                        <div class="col-12" style="color: #ea9999; font-weight: bold; margin-left: 5px;">
                                                            Ultima solicitud de armazón por defecto de fábrica rechazada.
                                                        </div>
                                                        @break
                                                @endswitch
                                                @break
                                        @endswitch
                                        <br>
                                    @endif
                                    @if($contrato[0]->estatus_estadocontrato == 2 ||
                                         $contrato[0]->estatus_estadocontrato == 4 ||
                                          $contrato[0]->estatus_estadocontrato == 5 ||
                                           $contrato[0]->estatus_estadocontrato == 12)
                                        <div class="col-12" style="color: #ea9999">
                                            Al aceptar confirmas que tienes la evidencia de la adquisición del producto.
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="button" data-dismiss="modal" id="btnCancelarModalProducto">Cancelar</button>
                                    <button class="btn btn-success" name="btnSubmit" type="submit" id="btnAceptarModalProducto">Aceptar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            @endif
        @endif
        @if(Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="movimientos-tab" data-toggle="tab" href="#movimientos" role="tab" aria-controls="movimientos"
                   aria-selected="true">Movimientos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="evidencias-tab" data-toggle="tab" href="#evidencias" role="tab" aria-controls="evidencias"
                   aria-selected="false">Evidencias</a>
            </li>
        </ul>
        <div class="tab-content">
        <!--Seccion de movimientos-->
            <div class="tab-pane active" id="movimientos" role="tabpanel" aria-labelledby="movimientos-tab">
                <h2>@lang('mensajes.mensajehistorialcontrato')</h2>
                <form id="fragregarhistorialmovimientocontrato"
                      action="{{route('agregarhistorialmovimientocontrato',[$idFranquicia,$idContrato])}}"
                      enctype="multipart/form-data"
                      method="GET" onsubmit="btnSubmit.disabled = true;">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <input type="text" name="movimiento" class="form-control {!! $errors->first('movimiento','is-invalid')!!}" placeholder="MOVIMIENTO"
                                       maxlength="250">
                                {!! $errors->first('movimiento','<div class="invalid-feedback">Campo movimiento obligatorio.</div>')!!}
                            </div>
                        </div>
                        <div class="col-4">
                            <select name="tipoMovimiento" id="tipoMovimiento" class=" form-control {!! $errors->first('tipoMovimiento','is-invalid')!!} @error('tipoMovimiento') is-invalid @enderror" value="{{old('tipoMovimiento')}}">
                                <option value="">SELECCIONAR TIPO DE MOVIMIENTO</option>
                                <option value="5">NORMAL</option>
                                <option value="6">SEGUIMIENTO LIO/FUGA GARANTIA</option>
                            </select>
                            {!! $errors->first('tipoMovimiento','<div class="invalid-feedback">Selecciona un tipo de movimiento.</div>')!!}
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit"
                                        style="margin-top: 0px">AGREGAR
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <table id="tablaHistorialC" class="table-striped table-general table-sm">
                    @if(sizeof($historialcontrato)>0)
                        <thead>
                        <tr>
                            <th style="text-align:center;" scope="col">Usuario</th>
                            <th style="text-align:center; white-space: normal;" scope="col">Cambios</th>
                            <th style="text-align:center;" scope="col">Fecha</th>
                            <th style="text-align:center;" scope="col">ACCION</th>
                        </tr>
                        </thead>
                    @endif
                    <tbody>
                    @foreach($historialcontrato as $hc)
                        <tr>
                            <td align='center'>{{$hc->name}}</td>
                            <td align='center' style="white-space: normal;">{{$hc->cambios}}</td>
                            <td align='center'>{{$hc->created_at}}</td>
                            <td align='center'>
                                @if($hc->id_usuarioC == Auth::user()->id && ($hc->tipomensaje == 5 || $hc->tipomensaje == 6) && date("Y-m-d H:i:s", strtotime($hc->created_at ." +15 minutes")) > Carbon\Carbon::now())
                                    <div style="display: flex; justify-content: center; align-content: center;">
                                        <a class="btn btn-outline-danger btn-sm" href="{{route('eliminarhistorialmovimientocontrato',[$idFranquicia, $hc->id_contrato, $hc->id])}}">ELIMINAR</a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        <!--Seccion de fotos-->
            <div class="tab-pane" id="evidencias" role="tabpanel" aria-labelledby="evidencias-tab">
                <h2>Evidencias</h2>
                <form id="formAgregarFotoMovimiento"
                      action="{{route('agregarhistorialfotocontrato',[$idFranquicia,$idContrato])}}"
                      enctype="multipart/form-data"
                      method="POST" onsubmit="btnSubmit.disabled = true;">
                    @csrf
                    <div class="row">
                        <div class="col-5">
                            <div class="form-group">
                                <label>Foto</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input {!! $errors->first('fotomovimiento','is-invalid')!!}" name="fotomovimiento" id="fotomovimiento"
                                           accept="image/jpg" capture="camera">
                                    <label class="custom-file-label" for="fotomovimiento">Choose file...</label>
                                    {!! $errors->first('fotomovimiento','<div class="invalid-feedback">Foto movimiento obligatoria en formato JPG.</div>')!!}
                                </div>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea rows="3" name="observaciones" id="observaciones" class="form-control" placeholder="Observaciones"> {{old('Observaciones')}} </textarea>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group" style="margin-top: 30px;">
                                <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit" form="formAgregarFotoMovimiento"
                                        style="margin-top: 0px">AGREGAR
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <table id="tblSupervision" class="table-bordered table-striped table-general table-sm">
                    <thead>
                    <tr>
                        <th  style =" text-align:center;" scope="col">USUARIO</th>
                        <th  style =" text-align:center;" scope="col">FOTO</th>
                        <th  style =" text-align:center;" scope="col">DESCRIPCIÓN</th>
                        <th  style =" text-align:center;" scope="col">FECHA</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($historialFotosContrato) > 0)
                        @foreach($historialFotosContrato as $historialFoto)
                            <tr>
                                <td  style =" text-align:center;" scope="col">{{$historialFoto->name}}</td>
                                @if(isset($historialFoto->foto) && !empty($historialFoto->foto) && file_exists($historialFoto->foto))
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle"><img src="{{asset($historialFoto->foto)}}" style="width:50px;height:50px;" class="img-thumbnail"></td>
                                @else
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">S/C</td>
                                @endif
                                <td  style ="text-align:center; white-space: normal;" scope="col">@if(strlen($historialFoto->observaciones) > 0) {{$historialFoto->observaciones}} @else SIN OBSERVACIONES @endif</td>
                                <td  style =" text-align:center;" scope="col">{{$historialFoto->created_at}}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th  style =" text-align:center;" scope="col" colspan="4">SIN REGISTROS</th>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        @endif

        @if(((Auth::user()->rol_id == 12 || Auth::user()->rol_id == 13) &&
            ($contrato[0]->estatus_estadocontrato == 0 && $contrato[0]->idcontratorelacion == null)) ||  //Asistente u opto y estado del contrato en no terminado (Debe ser contrato padre)
            ((Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)
            && ($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 2
                    || $contrato[0]->estatus_estadocontrato == 3 || $contrato[0]->estatus_estadocontrato == 4 || $contrato[0]->estatus_estadocontrato == 5
                    || $contrato[0]->estatus_estadocontrato == 12 || $contrato[0]->estatus_estadocontrato == 14 || $contrato[0]->estatus_estadocontrato == 15)))
            <div class="row" style="margin-bottom: 60px;">

                @if((Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8) && $contrato[0]->estatus_estadocontrato == 3)
                    @if($hoyNumero >= 6 || $hoyNumero <= 2)
                        @if($solicitudCancelar != null)
                            @if($solicitudCancelar[0]->estatus == 0)
                                <div class="col-12">
                                    <a type="button" class="btn btn-outline-primary btn-block"
                                       href="{{route('validarContrato',[$idFranquicia,$idContrato])}}">VALIDAR CONTRATO</a>
                                    <div class="row" style="color: #0AA09E; font-weight: bold; padding-top: 10px; padding-right: 15px; justify-content: end;">
                                        La solicitud para @if($solicitudCancelar[0]->tipo == 1)
                                            cancelar el
                                        @else
                                            levantar lio/fuga al
                                        @endif contrato esta pendiente por confirmar.
                                    </div>
                                </div>
                            @endif
                            @if($solicitudCancelar[0]->estatus == 2)
                                <div class="col-6">
                                    <a type="button" class="btn btn-outline-primary btn-block"
                                       href="{{route('validarContrato',[$idFranquicia,$idContrato])}}">VALIDAR CONTRATO</a>
                                </div>
                                <div class="col-6">
                                    <a class="btn btn-outline-success btn-block" data-toggle="modal"
                                       data-target="#cancelarContrato" style="margin-top: 0px; margin-bottom: 0px;">SOLICITAR CANCELAR CONTRATO</a>
                                    <div class="row" style="color: #ea9999; font-weight: bold; padding-top:10px; padding-right: 15px; justify-content: end;">
                                        La última solicitud para @if($solicitudCancelar[0]->tipo == 1)
                                            cancelar el
                                        @else
                                            levantar lio/fuga al
                                        @endif contrato fue rechazada.
                                    </div>
                                </div>
                            @endif
                        @endif
                        @if($solicitudCancelar == null)
                            <div class="col-6">
                                <a type="button" class="btn btn-outline-primary btn-block"
                                   href="{{route('validarContrato',[$idFranquicia,$idContrato])}}">VALIDAR CONTRATO</a>
                            </div>
                            <div class="col-6">
                                <a class="btn btn-outline-success btn-block"
                                   data-toggle="modal" data-target="#cancelarContrato"
                                   style="margin-top: 0px; margin-bottom: 0px;">SOLICITAR CANCELAR CONTRATO</a>
                            </div>
                        @endif
                    @else
                        @if($solicitudCancelar == null)
                            <div class="col-12">
                                <a type="button" class="btn btn-outline-primary btn-block"
                                   href="{{route('validarContrato',[$idFranquicia,$idContrato])}}">VALIDAR CONTRATO</a>
                            </div>
                        @endif
                        @if($solicitudCancelar != null)
                            @if($solicitudCancelar[0]->estatus == 0)
                                <div class="col-12">
                                    <a type="button" class="btn btn-outline-primary btn-block"
                                       href="{{route('validarContrato',[$idFranquicia,$idContrato])}}">VALIDAR CONTRATO</a>
                                    <div class="row" style="color: #0AA09E; font-weight: bold; padding-top: 10px; padding-right: 15px; justify-content: end;">
                                        La solicitud para @if($solicitudCancelar[0]->tipo == 1)
                                            cancelar el
                                        @else
                                            levantar lio/fuga al
                                        @endif contrato esta pendiente por confirmar.
                                    </div>
                                </div>
                            @endif
                            @if($solicitudCancelar[0]->estatus == 2)
                                <div class="col-6">
                                    <a type="button" class="btn btn-outline-primary btn-block"
                                       href="{{route('validarContrato',[$idFranquicia,$idContrato])}}">VALIDAR CONTRATO</a>
                                    <div class="row" style="color: #ea9999; font-weight: bold; padding-right: 15px; justify-content: end;">
                                        La última solicitud para @if($solicitudCancelar[0]->tipo == 1)
                                            cancelar el
                                        @else
                                            levantar lio/fuga al
                                        @endif contrato fue rechazada.
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endif
                @else
                    @if($contrato[0]->estatus_estadocontrato != 14)
                        @if($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 4 || $contrato[0]->estatus_estadocontrato == 12)
                            @if($hoyNumero >= 6 || $hoyNumero <= 2)
                                @if($solicitudCancelar == null && $solicitudSupervisar == null)
                                    <div class="col-6">
                                        <a class="btn btn-outline-secondary btn-block" data-toggle="modal"
                                           data-target="#modalSupervisarContrato">SUPERVISAR
                                            CONTRATO</a>
                                    </div>
                                    <div class="col-6">
                                        <a class="btn btn-outline-success btn-block"
                                           data-toggle="modal"
                                           data-target="#cancelarContrato"
                                           style="margin-top: 0px; margin-bottom: 0px;">SOLICITAR CANCELAR CONTRATO</a>
                                    </div>
                                @else
                                    @if($solicitudSupervisar != null || $solicitudCancelar != null)
                                        @if($solicitudSupervisar == null)
                                            <div class="col-6">
                                                <a class="btn btn-outline-secondary btn-block" data-toggle="modal"
                                                   data-target="#modalSupervisarContrato">SUPERVISAR
                                                    CONTRATO</a>
                                            </div>
                                        @else
                                            <div class="col-6">
                                                @if($solicitudSupervisar[0]->estatus == 0)
                                                    <div class="row" style="color: #0AA09E; font-weight: bold; padding-top: 10px; padding-left: 15px;">
                                                        La solicitud para cambiar estatus de contrato a supervisar pendiente por confirmar.
                                                    </div>
                                                @endif
                                                @if($solicitudSupervisar[0]->estatus == 2)
                                                    <a class="btn btn-outline-secondary btn-block" data-toggle="modal"
                                                       data-target="#modalSupervisarContrato">SUPERVISAR
                                                        CONTRATO</a>
                                                    <div class="row" style="color: #ea9999; font-weight: bold; padding-top: 10px; padding-left: 15px;">
                                                        La ultima solicitud para cambiar estatus de contrato a supervisar fue rechazada.
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        @if($solicitudCancelar == null)
                                            <div class="col-6">
                                                <a class="btn btn-outline-success btn-block"
                                                   data-toggle="modal"
                                                   data-target="#cancelarContrato"
                                                   style="margin-top: 0px; margin-bottom: 0px;">SOLICITAR CANCELAR CONTRATO</a>
                                            </div>
                                        @else
                                            @if($solicitudCancelar[0]->estatus == 0)
                                                <div class="col-6">
                                                    <div class="row" style="color: #0AA09E; font-weight: bold; padding-top: 10px; padding-right: 15px; justify-content: end;">
                                                        La solicitud para @if($solicitudCancelar[0]->tipo == 1)
                                                            cancelar el
                                                        @else
                                                            levantar lio/fuga al
                                                        @endif contrato esta pendiente por confirmar.
                                                    </div>
                                                </div>
                                            @endif
                                            @if($solicitudCancelar[0]->estatus == 2)
                                                <div class="col-6">
                                                    <a class="btn btn-outline-success btn-block"
                                                       data-toggle="modal" data-target="#cancelarContrato"
                                                       style="margin-top: 0px; margin-bottom: 0px;">SOLICITAR CANCELAR CONTRATO</a>
                                                    <div class="row" style="color: #ea9999; font-weight: bold;  padding-top:10px; padding-right: 15px; justify-content: end;">
                                                        La última solicitud para @if($solicitudCancelar[0]->tipo == 1)
                                                            cancelar el
                                                        @else
                                                            levantar lio/fuga al
                                                        @endif contrato fue rechazada.
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endif
                                @endif
                            @else
                                @if($solicitudCancelar == null && $solicitudSupervisar == null ||  ($solicitudCancelar != null && $solicitudSupervisar == null))
                                    <div class="col-12">
                                        <a class="btn btn-outline-secondary btn-block" data-toggle="modal"
                                           data-target="#modalSupervisarContrato">SUPERVISAR CONTRATO</a>
                                    </div>
                                @endif
                                @if($solicitudSupervisar != null && $solicitudCancelar != null)
                                    <div class="col-6">
                                        @if($solicitudSupervisar[0]->estatus == 0)
                                            <div class="row" style="color: #0AA09E; font-weight: bold; padding-top: 10px; padding-left: 15px;">
                                                La solicitud para cambiar estatus de contrato a supervisar pendiente por confirmar.
                                            </div>
                                        @endif
                                        @if($solicitudSupervisar[0]->estatus == 2)
                                            <a class="btn btn-outline-secondary btn-block" data-toggle="modal"
                                               data-target="#modalSupervisarContrato">SUPERVISAR CONTRATO</a>
                                            <div class="row" style="color: #ea9999; font-weight: bold; padding-top: 10px; padding-left: 15px;">
                                                La ultima solicitud para cambiar estatus de contrato a supervisar fue rechazada.
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-6">
                                        @if($solicitudCancelar[0]->estatus == 0)
                                            <div class="row" style="color: #0AA09E; font-weight: bold; padding-top: 10px; padding-right: 15px; justify-content: end;">
                                                La solicitud para @if($solicitudCancelar[0]->tipo == 1)
                                                    cancelar el
                                                @else
                                                    levantar lio/fuga al
                                                @endif contrato esta pendiente por confirmar.
                                            </div>
                                        @endif
                                        @if($solicitudCancelar[0]->estatus == 2)
                                            <div class="row" style="color: #ea9999; font-weight: bold;  padding-top:10px; padding-right: 15px; justify-content: end;">
                                                La última solicitud para @if($solicitudCancelar[0]->tipo == 1)
                                                    cancelar el
                                                @else
                                                    levantar lio/fuga al
                                                @endif contrato fue rechazada.
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                @if($solicitudSupervisar != null && $solicitudCancelar == null)
                                    <div class="col-12">
                                        @if($solicitudSupervisar[0]->estatus == 0)
                                            <div class="row" style="color: #0AA09E; font-weight: bold; padding-top: 10px; padding-left: 15px;">
                                                La solicitud para cambiar estatus de contrato a supervisar pendiente por confirmar.
                                            </div>
                                        @endif
                                        @if($solicitudSupervisar[0]->estatus == 2)
                                            <a class="btn btn-outline-secondary btn-block" data-toggle="modal"
                                               data-target="#modalSupervisarContrato">SUPERVISAR CONTRATO</a>
                                            <div class="row" style="color: #ea9999; font-weight: bold; padding-top: 10px; padding-left: 15px;">
                                                La ultima solicitud para cambiar estatus de contrato a supervisar fue rechazada.
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                @if($solicitudSupervisar == null && $solicitudCancelar != null)
                                    @if($solicitudCancelar[0]->estatus == 0)
                                        <div class="col-12">
                                            <a class="btn btn-outline-secondary btn-block" data-toggle="modal"
                                               data-target="#modalSupervisarContrato">SUPERVISAR CONTRATO</a>
                                            <div class="row" style="color: #0AA09E; font-weight: bold; padding-top: 10px; padding-right: 15px; justify-content: end;">
                                                La solicitud para @if($solicitudCancelar[0]->tipo == 1)
                                                    cancelar el
                                                @else
                                                    levantar lio/fuga al
                                                @endif contrato esta pendiente por confirmar.
                                            </div>
                                        </div>
                                    @endif
                                    @if($solicitudCancelar[0]->estatus == 2)
                                        <div class="col-12">
                                            <a class="btn btn-outline-secondary btn-block" data-toggle="modal"
                                               data-target="#modalSupervisarContrato">SUPERVISAR CONTRATO</a>
                                            <div class="row" style="color: #ea9999; font-weight: bold;padding-top: 10px; padding-right: 15px; justify-content: end;">
                                                La última solicitud para @if($solicitudCancelar[0]->tipo == 1)
                                                    cancelar el
                                                @else
                                                    levantar lio/fuga al
                                                @endif contrato fue rechazada.
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endif
                        @else
                            @if($contrato[0]->estatus_estadocontrato == 15)
                                @if($hoyNumero >= 6 || $hoyNumero <= 2)
                                    @if($solicitudCancelar == null)
                                        <div class="col-6">
                                            <a class="btn btn-outline-secondary btn-block" data-toggle="modal"
                                               data-target="#modalRestablecerContrato">RESTABLECER CONTRATO</a>
                                        </div>
                                        <div class="col-6">
                                            <a class="btn btn-outline-success btn-block"
                                               data-toggle="modal" data-target="#cancelarContrato"
                                               style="margin-top: 0px; margin-bottom: 0px;">SOLICITAR CANCELAR CONTRATO</a>
                                        </div>
                                    @endif
                                    @if($solicitudCancelar != null)
                                        @if($solicitudCancelar[0]->estatus == 0)
                                            <div class="col-12">
                                                <a class="btn btn-outline-secondary btn-block" data-toggle="modal"
                                                   data-target="#modalRestablecerContrato">RESTABLECER CONTRATO</a>
                                                <div class="row" style="color: #0AA09E; font-weight: bold; padding-top: 10px; padding-right: 15px; justify-content: end;">
                                                    La solicitud para @if($solicitudCancelar[0]->tipo == 1)
                                                        cancelar el
                                                    @else
                                                        levantar lio/fuga al
                                                    @endif contrato esta pendiente por confirmar.
                                                </div>
                                            </div>
                                        @endif
                                        @if($solicitudCancelar[0]->estatus == 2)
                                            <div class="col-6">
                                                <a class="btn btn-outline-secondary btn-block" data-toggle="modal"
                                                   data-target="#modalRestablecerContrato">RESTABLECER CONTRATO</a>
                                            </div>
                                            <div class="col-6">
                                                <a class="btn btn-outline-success btn-block"
                                                   data-toggle="modal" data-target="#cancelarContrato"
                                                   style="margin-top: 0px; margin-bottom: 0px;">SOLICITAR CANCELAR CONTRATO</a>
                                                <div class="row" style="color: #ea9999; font-weight: bold; padding-top:10px; padding-right: 15px; justify-content: end;">
                                                    La última solicitud para @if($solicitudCancelar[0]->tipo == 1)
                                                        cancelar el
                                                    @else
                                                        levantar lio/fuga al
                                                    @endif contrato fue rechazada.
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    @if($solicitudCancelar == null)
                                        <div class="col-12">
                                            <a class="btn btn-outline-secondary btn-block" data-toggle="modal"
                                               data-target="#modalRestablecerContrato">RESTABLECER CONTRATO</a>
                                        </div>
                                    @endif
                                    @if($solicitudCancelar != null)
                                        @if($solicitudCancelar[0]->estatus == 0)
                                            <div class="col-12">
                                                <a class="btn btn-outline-secondary btn-block" data-toggle="modal"
                                                   data-target="#modalRestablecerContrato">RESTABLECER CONTRATO</a>
                                                <div class="row" style="color: #0AA09E; font-weight: bold; padding-top: 10px; padding-right: 15px; justify-content: end;">
                                                    La solicitud para @if($solicitudCancelar[0]->tipo == 1)
                                                        cancelar el
                                                    @else
                                                        levantar lio/fuga al
                                                    @endif contrato esta pendiente por confirmar.
                                                </div>
                                            </div>
                                        @endif
                                        @if($solicitudCancelar[0]->estatus == 2)
                                            <div class="col-12">
                                                <a class="btn btn-outline-secondary btn-block" data-toggle="modal"
                                                   data-target="#modalRestablecerContrato">RESTABLECER CONTRATO</a>
                                                <div class="row" style="color: #ea9999; font-weight: bold; padding-top:10px; padding-right: 15px; justify-content: end;">
                                                    La última solicitud para @if($solicitudCancelar[0]->tipo == 1)
                                                        cancelar el
                                                    @else
                                                        levantar lio/fuga al
                                                    @endif contrato fue rechazada.
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            @else
                                @if($hoyNumero >= 6 || $hoyNumero <= 2)
                                    @if($solicitudCancelar == null)
                                        <div class="col-12">
                                            <a type="button" class="btn btn-outline-success btn-block"
                                               data-toggle="modal"
                                               data-target="#cancelarContrato"
                                               style="margin-top: 0px; margin-bottom: 0px;">SOLICITAR CANCELAR CONTRATO</a>
                                        </div>
                                    @endif
                                    @if($solicitudCancelar != null)
                                        @if($solicitudCancelar[0]->estatus == 0)
                                            <div class="col-12">
                                                <div class="row" style="color: #0AA09E; font-weight: bold; padding-top: 20px; padding-right: 15px; justify-content: end;">
                                                    La solicitud para @if($solicitudCancelar[0]->tipo == 1)
                                                        cancelar el
                                                    @else
                                                        levantar lio/fuga al
                                                    @endif contrato esta pendiente por confirmar.
                                                </div>
                                            </div>
                                        @endif
                                        @if($solicitudCancelar[0]->estatus == 2)
                                            <div class="col-12">
                                                <a type="button" class="btn btn-outline-success btn-block"
                                                   data-toggle="modal"
                                                   data-target="#cancelarContrato" style="margin-bottom: 0px;">SOLICITAR CANCELAR CONTRATO</a>
                                                <div class="row" style="color: #ea9999; font-weight: bold; padding-top:10px; padding-right: 15px; justify-content: end;">
                                                    La última solicitud para @if($solicitudCancelar[0]->tipo == 1)
                                                        cancelar el
                                                    @else
                                                        levantar lio/fuga al
                                                    @endif contrato fue rechazada.
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            @endif
                        @endif
                    @endif
                @endif
            </div>
        @endif

        <!-- Restablecer contratos cancelados, rechazados y lio/fuga -->
        @if((Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)
                && ($contrato[0]->estatus_estadocontrato == 6 || $contrato[0]->estatus_estadocontrato == 8 || $contrato[0]->estatus_estadocontrato == 14))
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-block" data-toggle="modal"
                       data-target="#modalRestablecerContratoCanceladoRechazadoLioFuga">RESTABLECER CONTRATO</a>
                </div>
            </div>
            <!-- modal para restablecer contratos cancelados, rechazados y lio/fuga -->
            <div class="modal fade" id="modalRestablecerContratoCanceladoRechazadoLioFuga" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <form action="{{route('restablecercontratocanceladorechazadoliofuga',[$idFranquicia,$idContrato])}}"
                      method="GET" onsubmit="btnSubmit.disabled = true;">
                    @csrf
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                ¿Realmente quieres restablecer el contrato?
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                                <button class="btn btn-success" name="btnSubmit" type="submit">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @endif
        <br>
        <br>
        <br>
        <br>

        <!-- modal para Cancelar contratos -->
        <div class="modal fade" id="cancelarContrato" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <form action="{{route('cancelarContrato',[$idFranquicia,$idContrato])}}" enctype="multipart/form-data"
                  method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            Solicitud para cancelar o levantar lio/fuga al contrato.
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6">
                                    Describa el motivo:
                                </div>
                                @if(Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)
                                    <div class="col-1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="cbLioFuga" id="customCheck1">
                                            <label class="custom-control-label" for="customCheck1">Lio/Fuga</label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($contrato[0]->subscripcion != null)
                                <p style="color: red;margin-top: 5px;">Nota: La subscripcion tambien sera cancelada.</p>
                            @endif
                            <br>
                            <textarea name="comentarios"
                                      class="form-control {!! $errors->first('comentarios','is-invalid')!!}" rows="10"
                                      cols="60" maxlength="1000"></textarea>
                            {!! $errors->first('comentarios','<div class="invalid-feedback">campo
                                obligatorio</div>')!!}
                            <div class="form-group row">
                                <label class="col-sm-12 col-form-label" style="color: #ea9999;">El mensaje debe contener como minimo 15 caracteres y un maximo de 1000.</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                            <button class="btn btn-success" name="btnSubmit" type="submit">Aceptar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- modal para Confirmar contratos -->
        <div class="modal fade" id="Abrirconfirmar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        Solicitud de confirmación
                    </div>
                    <div class="modal-body">
                        ¿Deseas confirmar esta acción?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
                        <a class="btn btn-success"
                           href="{{route('nuevocontrato2',[$idFranquicia,$idContrato])}}">Aceptar</a>
                    </div>
                </div>
            </div>
        </div>


        <!-- modal para Confirmar contratos hijos -->
        <div class="modal fade" id="Abrirconfirmarhijo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        Solicitud de confirmación
                    </div>
                    <div class="modal-body">
                        ¿Estas seguro que quieres terminar el contrato?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
                        <a class="btn btn-success"
                           href="{{route('contratoHijos',[$idFranquicia,$idContrato])}}">Aceptar</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- modal para Entregar contratos -->
        <div class="modal fade" id="Abrirentregar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        Solicitud de confirmación
                    </div>
                    <div class="modal-body">
                        Se esta realizando la entrega del producto al cliente.
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                        <a class="btn btn-success"
                           href="{{route('entregarContrato',[$idFranquicia,$idContrato])}}">Aceptar</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- modal para Abonos -->
        <div class="modal fade" id="AbrirAbono" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <form action="{{route('agregarabono',[$idFranquicia,$idContrato])}}" enctype="multipart/form-data"
                  method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ExampleAbrir" style="color: #FFFFFF">Abonos</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>SALDO:</label>
                                        <input type="text" name="saldo2" class="form-control" readonly
                                               value="$ {{$contrato[0]->total}}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label>ABONAR:</label>
                                    <input type="number" step=".1" name="abono" id="abono"
                                           class="form-control {!! $errors->first('abono','is-invalid')!!}"
                                           min="0"
                                           placeholder="Abono" @if($contrato[0]->totalproducto != null &&
                                $contrato[0]->estatus_estadocontrato == 0 && $contrato[0]->totalabono <
                                    $contrato[0]->
                                    totalproducto)
                                               value="{{$contrato[0]->totalproducto}}"
                                           @else value="{{ old('abono') }}" @endif>
                                    {!! $errors->first('abono','<div class="invalid-feedback">El abono es
                                        obligatorio,
                                        debera ser mayor a cero</div>')!!}
                                    <input type="hidden" id="abonocancelaciontemporal" value="0">
                                </div>
                                <div class="col-12">
                                    <label>METODO DE PAGO:</label>
                                    <select class="custom-select" name="metodopago" id="metodopago"
                                            onchange="metodoSeleccionado(this)">
                                        <option value="0">EFECTIVO</option>
                                        @if($contrato[0]->estatus_estadocontrato != 7 && $contrato[0]->estatus_estadocontrato != 10 && $contrato[0]->estatus_estadocontrato != 11
                                            && $contrato[0]->estatus_estadocontrato != 9)
                                            <option value="1" hidden>TARJETA</option>
                                            <option value="2">TRANSFERENCIA</option>
                                        @endif
                                        <option value="3">CANCELACIÓN</option>
                                    </select>
                                </div>

                                @if($contrato[0]->estatus_estadocontrato != 7 && $contrato[0]->estatus_estadocontrato != 10 && $contrato[0]->estatus_estadocontrato != 11
                                    && $contrato[0]->estatus_estadocontrato != 9)
                                    <div class="col-12" id="mostrarmeses" style="display:none;">
                                        <label>PAGO A MESES</label>
                                        <select class="custom-select" name="meses" id="meses">
                                            <option value="0">PAGO UNICO</option>
                                            <option value="1">3 MESES</option>
                                            <option value="2">6 MESES</option>
                                            <option value="3">9 MESES</option>
                                        </select>
                                    </div>
                                @endif
                            </div>
                            @if(Auth::user()->rol_id == 4 && ($contrato[0]->estatus_estadocontrato >= 2 && $contrato[0]->estatus_estadocontrato < 12))
                                @if($contrato[0]->pago == 1 && $contrato[0]->total > 150 || $contrato[0]->pago == 2 &&
                                $contrato[0]->total > 300 || $contrato[0]->pago == 4 && $contrato[0]->total > 450)
                                    <label>ABONOS POR ADELANTADO:</label>
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="form-group">
                                                <input type="number" name="adelanto" min="1" max="3"
                                                       class="form-control {!! $errors->first('adelanto')!!}"
                                                       placeholder="Adelanto" value="{{ old('adelanto') }}">
                                                {!! $errors->first('adelanto','<div class="invalid-feedback">La descripcion es
                                                    obligatoria.</div>')!!}
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-check">
                                                <input type="checkbox" name="adelantar" id="adelantar"
                                                       class="form-check-input"
                                                       value="1">
                                                <label class="form-check-label" for="adelantar">Adelantar
                                                    abono</label>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                            <br>
                            @if($contrato[0]->estadogarantia == 0 || $contrato[0]->estadogarantia == 1)
                                <div class="row">
                                    <div class="col-12">
                                        <label style="color: #ea9999;">No se puede agregar el abono de cancelacion de momento, es necesario cancelar o terminar el proceso de
                                            garantía.</label>
                                    </div>
                                </div>
                            @endif
                            <div class="modal-footer">
                                <button class="btn btn-primary" type="button"
                                        data-dismiss="modal">Cancelar
                                </button>
                                <button class="btn btn-success" name="btnSubmit" type="submit">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- modal para supervisar contratos -->
        <div class="modal fade" id="modalSupervisarContrato" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <form action="{{route('solicitarautorizacionsupervisarcontrato',[$idFranquicia,$idContrato])}}"
                  method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">Describa la solicitud para cambio de estatus del contrato a supervision. </div>
                        <div class="modal-body">
                            <textarea name="mensaje" id="mensaje" class="form-control {!! $errors->first('mensaje','is-invalid')!!}" rows="10" cols="60"></textarea>
                            {!! $errors->first('mensaje','<div class="invalid-feedback">Campo obligatorio, minimo 15 caracteres y maximo 1000.</div>')!!}
                            <div class="row">
                                <label class="col-sm-12 col-form-label" style="color: #ea9999;">El mensaje debe contener como minimo 15 caracteres y un maximo de 1000.</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                            <button class="btn btn-success" name="btnSubmit" type="submit">Aceptar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- modal para restablecer contratos -->
        <div class="modal fade" id="modalRestablecerContrato" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <form action="{{route('restablecercontrato',[$idFranquicia,$idContrato])}}"
                  method="GET" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            ¿Realmente quieres restablecer el contrato?
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                            <button class="btn btn-success" name="btnSubmit" type="submit">Aceptar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!--Modal para Solicitar Autorizacion Aumentar disminuir-->
        <div class="modal fade" id="modalsolicitaraumentardescontar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <form action="{{route('solicitarautorizacionaumentardisminuir',[$idFranquicia,$idContrato])}}" enctype="multipart/form-data"
                  method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            Solicitud de autorización para cambio de precio.
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-sm-5 col-form-label"></label>
                                <label class="col-sm-4 col-form-label">Costo total actual: </label>
                                <input type="text" name="totalactual" id="totalactual"
                                       class="form-control col-sm-2" value="{{$contrato[0]->total}}" readonly>
                                <label class="col-sm-1 col-form-label"></label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-9 col-form-label">Indica la cantidad que quieres agregar/restar al total: </label>
                                <input type="number" name="aumentardescontar" id="aumentardescontar" class="form-control col-sm-2" min="-9999"
                                       max="9999" value="0">
                                <label class="col-sm-1 col-form-label"></label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label"></label>
                                <label class="col-sm-5 col-form-label">Costo total actualizado: </label>
                                <input type="text" name="totalactualizado" id="totalactualizado"
                                       class="form-control col-sm-2" readonly>
                                <label class="col-sm-1 col-form-label"></label>
                            </div>
                            Explica detalladamente el por que requieres alterar el total del contrato:
                            <textarea name="mensaje"
                                      id="mensaje"
                                      class="form-control {!! $errors->first('mensaje','is-invalid')!!}" rows="10"
                                      cols="60" maxlength="1000">
                            </textarea>
                            {!! $errors->first('mensaje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                            <div class="form-group row">
                                <label class="col-sm-12 col-form-label" style="color: #ea9999;">El mensaje debe contener como minimo 15 caracteres y un maximo de 1000.</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                            <button class="btn btn-success" name="btnSubmit" type="submit">Aceptar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- modal para eliminar abono -->
        <div class="modal fade" id="confirmacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        Solicitud de confirmacion
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                ¿Estas seguro que quieres eliminar el abono?
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-success" data-dismiss="modal">Cancelar</button>
                        <a class="btn btn-outline-danger btn-ok">Eliminar</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- modal para eliminar contrato producto -->
        <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        Solicitud de confirmacion
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                ¿Estas seguro que quieres eliminar el producto?
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-success" data-dismiss="modal">Cancelar</button>
                        <a class="btn btn-outline-danger btn-ok">Eliminar</a>
                    </div>
                </div>
            </div>
        </div>

        <!--Modal para Solicitar Autorizacion abono minimo-->
        <div class="modal fade" id="modalsolicitarabonominimo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <form action="{{route('solicitarautorizacionabonominimo',[$idFranquicia,$contrato[0]->id])}}" enctype="multipart/form-data"
                  method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            Solicitud para autorización de abono minimo.
                        </div>
                        <div class="modal-body">Describa la solicitud para abono minimo.
                            <textarea name="mensaje"
                                      id="mensaje"
                                      class="form-control {!! $errors->first('mensaje','is-invalid')!!}" rows="10"
                                      cols="60">
                        </textarea>
                            {!! $errors->first('mensaje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                            <button class="btn btn-success" name="btnSubmit" type="submit">Aceptar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection

<script>
    var cantmeses = "{{$abonostarjetameses[0]->cont}}";
    var estadocontra = "{{$contrato[0]->estatus_estadocontrato}}";
    var promoactiva = "{{$contrato[0]->id_promocion}}";
    var pago = "{{$contrato[0]->pago}}";
    var idcontratorelacion = "{{$contrato[0]->idcontratorelacion}}";
</script>

{{--Datos ticket abono--}}
<script>
    var ciudad = "SUC. {{$sucursal[0]->ciudad}}";
    var telefonoAtencion = "{{$sucursal[0]->telefonoatencionclientes}}";
    var whatsapp = "{{$sucursal[0]->whatsapp}}";
    var nombreCliente = "{{$contrato[0]->nombre}}";
    var idContrato = "{{$idContrato}}";
    var totalContrato = "${{$contrato[0]->total}}";
    var abonoContrato = "{{$abono}}"
    var totalAnterior = "{{$totalAnterior}}";
    var folioAbono = "{{$folioAbono}}";
    var totalAbonoLetra = "{{$totalAbonoLetra}}";
    var fechaImprimirTicket = "{{$fechaImprimirTicket}}";
    var nombreUsuario = "{{$nombreUsuario}}";
    var estadoContrato = "{{$nombreUsuario}}";
    var contratoPagado = ({{$contrato[0]->estatus_estadocontrato}} == 5) ? "PAGADO" : "";
</script>
