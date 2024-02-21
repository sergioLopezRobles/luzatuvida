@extends('layouts.appclientes')
@section('titulo','Rastrear contrato'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div style="margin-top: 50px;">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="rastreo-tab" data-toggle="tab" href="#rastreo" role="tab" aria-controls="rastreo"
                   aria-selected="true">Rastreo de contrato</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="credito-tab" data-toggle="tab" href="#credito" role="tab" aria-controls="credito"
                   aria-selected="false">Estado de cuenta</a>
            </li>
        </ul>
        <div class="tab-content">

            <!--Rastreo de contrato-->
            <div class="tab-pane active" id="rastreo" role="tabpanel" aria-labelledby="rastreo-tab">
                @if($rastreoContrato != null)
                    <div style="border: lightgrey solid 1px;">
                        <div id="datosEntrega" style="border-bottom:lightgrey solid 1px;">
                            <div style="margin: 10px; font-size: 12px;">
                                <div><h2 class="header-text">LABORATORIO OPTICO LUZ A TU VIDA</h2></div>
                                <div style="text-transform: uppercase; font-size: 16px; font-family: Serif;">Numero de contrato para seguimiento: {{$datosContrato[0]->id}}</div>
                                <div style="text-transform: uppercase; font-size: 16px; font-family: Serif;">Fecha estimada de entrega: {{$datosContrato[0]->fechaentregaestimada}} </div>
                            </div>
                        </div>
                        <div id="simbologiaEstatus" style="border-bottom:lightgrey solid 1px;">
                            <div class="row" style="margin: 20px; font-size: 12px; justify-content: center;">
                                <div align="center">
                                    <i class="bi bi-file-text-fill fa-4x  @if($rastreoContrato[0]->fecharegistro != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                    <p class="description-text">Contrato registrado</p>
                                </div>
                                <div align="center">
                                    <i class="bi bi-three-dots fa-3x  @if($rastreoContrato[1]->fecharegistro != null || $rastreoContrato[2]->fecharegistro != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                </div>
                                <div align="center">
                                    <i class="bi bi-list-check fa-4x  @if($rastreoContrato[1]->fecharegistro != null || $rastreoContrato[2]->fecharegistro != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                    <p class="description-text">Contrato en revisión</p>
                                </div>
                                <div align="center">
                                    <i class="bi bi-three-dots fa-3x @if($rastreoContrato[2]->fecharegistro != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                </div>
                                <div align="center">
                                    <i class="bi bi-file-earmark-check-fill fa-4x @if($rastreoContrato[2]->fecharegistro != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                    <p class="description-text">Contrato aprobado</p>
                                </div>
                                <div align="center">
                                    <i class="bi bi-three-dots fa-3x @if($rastreoContrato[3]->fecharegistro != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                </div>
                                <div align="center">
                                    <i class="bi bi-gear-wide-connected fa-4x @if($rastreoContrato[3]->fecharegistro != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                    <p class="description-text">Preparando producto</p>
                                </div>
                                <div align="center">
                                    <i class="bi bi-three-dots fa-3x @if($rastreoContrato[4]->fecharegistro != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                </div>
                                <div align="center" style="margin-left: 5px;">
                                    <i class="bi bi-hourglass-split fa-4x @if($rastreoContrato[4]->fecharegistro != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                    <p class="description-text">Por enviar</p>
                                </div>
                                <div align="center" style="margin-left: 10px;">
                                    <i class="bi bi-three-dots fa-3x @if($rastreoContrato[5]->fecharegistro != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                </div>
                                <div align="center" style="margin-left: 20px;">
                                    <i class="bi bi-box-seam-fill fa-4x @if($rastreoContrato[5]->fecharegistro != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                    <p class="description-text">Enviado</p>
                                </div>
                                @if($banderaGarantia == 0 || ($banderaGarantia == 1 && $estadoContrato == 2))
                                    <div align="center" style="margin-left: 20px;">
                                        <i class="bi bi-three-dots fa-3x @if($rastreoContrato[6]->fecharegistro != null || $datosContrato[0]->fechaentrega != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                    </div>
                                    <div align="center" style="margin-left: 20px;">
                                        <i class="bi bi-check-circle-fill fa-4x @if($rastreoContrato[6]->fecharegistro != null || $datosContrato[0]->fechaentrega != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                        <p class="description-text">Entregado</p>
                                    </div>
                                @endif
                                @if($banderaGarantia == 1 && $estadoContrato == 12)
                                    <div align="center" style="margin-left: 20px;">
                                        <i class="bi bi-three-dots fa-3x estado-rastreo-pendiente"></i>
                                    </div>
                                    <div align="center" style="margin-left: 20px;">
                                        <i class="bi bi-check-circle-fill fa-4x estado-rastreo-pendiente"></i> <p class="description-text">Entregado</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        {{-- Seccion de seguimeinto de contrato ordenado por estatus--}}
                        <div id="seguimientoContrato" style="background-color: #F7F7F7; margin: 25px;">
                            {{-- Estatus de contrato terminado --}}
                            <div class="row">
                                <div class="col-2" style="padding-left: 40px; padding-top: 20px; font-weight: bold; font-size: 18px; font-family: Serif;"> {{$rastreoContrato[0]->fecharegistro}} </div>
                                @if($rastreoContrato[0]->fecharegistro != null)
                                    <div class="col-1" align="center">
                                        <i class="bi bi-file-text-fill fa-3x estado-rastreo-listo"></i>
                                    </div>
                                    <div class="col-9">
                                        <p class="estado-rastreo-titulo-habilidato">CONTRATO REGISTRADO</p>
                                            <?php
                                            $nombre = explode(" ", $datosContrato[0]->usuariocreacion);
                                            ?>
                                        <p class="estado-rastreo-descripcion-habilitado">Contrato elaborado por parte de: {{$nombre[0]}}, en
                                            {{$datosContrato[0]->localidad}}, {{$datosContrato[0]->created_at}}</p>
                                    </div>
                                @else
                                    <div class="col-1" align="center">
                                        <i class="bi bi-circle-fill estado-rastreo-pendiente"></i>
                                    </div>
                                    <div class="col-9">
                                        <p class="estado-rastreo-titulo-deshabilitado">CONTRATO REGISTRADO</p>
                                    </div>
                                @endif
                            </div>
                            {{-- Renglon de flecha siguiente paso de seguimiento --}}
                            <div class="row">
                                <div class="col-2"> </div>
                                <div class="col-1" align="center">
                                    <i class="bi bi-chevron-down fa-2x @if($rastreoContrato[1]->fecharegistro != null || $rastreoContrato[2]->fecharegistro != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                </div>
                                <div class="col-9"></div>
                            </div>
                            {{-- Estatus proceso de aprobacion --}}
                            <div class="row">
                                <div class="col-2" style="padding-left: 40px; padding-top: 20px; font-weight: bold; font-size: 18px; font-family: Serif;">
                                    @if($rastreoContrato[1]->fecharegistro != null) {{$rastreoContrato[1]->fecharegistro}} @else {{$rastreoContrato[2]->fecharegistro}} @endif </div>
                                @if($rastreoContrato[1]->fecharegistro != null || $rastreoContrato[2]->fecharegistro != null)
                                    <div class="col-1" align="center">
                                        <i class="bi bi-list-check fa-3x estado-rastreo-listo"></i>
                                    </div>
                                    <div class="col-9">
                                        <p class="estado-rastreo-titulo-habilidato">PROCESO DE APROBACIÓN</p>
                                        <p class="estado-rastreo-descripcion-habilitado">Verificando información proporcionada al momento de realizar el contrato en domicilio. </p>
                                        <p class="estado-rastreo-descripcion-habilitado">Espere una llamada por parte de atención a clientes para darle la bienvenida.</p>
                                    </div>
                                @else
                                    <div class="col-1" align="center">
                                        <i class="bi bi-circle-fill estado-rastreo-pendiente"></i>
                                    </div>
                                    <div class="col-9">
                                        <p class="estado-rastreo-titulo-deshabilitado">PROCESO DE APROBACIÓN</p>
                                    </div>
                                @endif
                            </div>
                            {{-- Renglon de flecha siguiente paso de seguimiento --}}
                            <div class="row">
                                <div class="col-2"> </div>
                                <div class="col-1" align="center">
                                    <i class="bi bi-chevron-down fa-2x @if($rastreoContrato[2]->fecharegistro != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                </div>
                                <div class="col-9"></div>
                            </div>
                            {{-- Estatus aprobado --}}
                            <div class="row">
                                <div class="col-2" style="padding-left: 40px; padding-top: 20px; font-weight: bold; font-size: 18px; font-family: Serif;"> {{$rastreoContrato[2]->fecharegistro}} </div>
                                @if($rastreoContrato[2]->fecharegistro != null)
                                    <div class="col-1" align="center">
                                        <i class="bi bi-file-earmark-check-fill fa-3x estado-rastreo-listo"></i>
                                    </div>
                                    <div class="col-9">
                                        <p class="estado-rastreo-titulo-habilidato">APROBADO</p>
                                        <p class="estado-rastreo-descripcion-habilitado">La información proporcionada fue verificada correctamente, usted ha sido aprobado para adquirir su producto.</p>
                                        <p class="estado-rastreo-descripcion-habilitado">Su pedido será trasferido a laboratorio.</p>
                                    </div>
                                @else
                                    <div class="col-1" align="center">
                                        <i class="bi bi-circle-fill estado-rastreo-pendiente"></i>
                                    </div>
                                    <div class="col-9">
                                        <p class="estado-rastreo-titulo-deshabilitado">APROBADO</p>
                                    </div>
                                @endif
                            </div>
                            {{-- Renglon de flecha siguiente paso de seguimiento --}}
                            <div class="row">
                                <div class="col-2"> </div>
                                <div class="col-1" align="center">
                                    <i class="bi bi-chevron-down fa-2x @if($rastreoContrato[3]->fecharegistro != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                </div>
                                <div class="col-9"></div>
                            </div>
                            {{-- Estatus manufactura --}}
                            <div class="row">
                                <div class="col-2" style="padding-left: 40px; padding-top: 20px; font-weight: bold; font-size: 18px; font-family: Serif;"> {{$rastreoContrato[3]->fecharegistro}} </div>
                                @if($rastreoContrato[3]->fecharegistro != null)
                                    <div class="col-1" align="center">
                                        <i class="bi bi-gear-wide-connected fa-3x estado-rastreo-listo"></i>
                                    </div>
                                    <div class="col-9">
                                        <p class="estado-rastreo-titulo-habilidato">MANUFACTURA</p>
                                        <p class="estado-rastreo-descripcion-habilitado">Su producto esta siendo diseñado bajo las especificaciones del tratamiento elegido.</p>
                                    </div>
                                @else
                                    <div class="col-1" align="center">
                                        <i class="bi bi-circle-fill estado-rastreo-pendiente"></i>
                                    </div>
                                    <div class="col-9">
                                        <p class="estado-rastreo-titulo-deshabilitado">MANUFACTURA</p>
                                    </div>
                                @endif
                            </div>
                            {{-- Renglon de flecha siguiente paso de seguimiento --}}
                            <div class="row">
                                <div class="col-2"> </div>
                                <div class="col-1" align="center">
                                    <i class="bi bi-chevron-down fa-2x @if($rastreoContrato[4]->fecharegistro != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                </div>
                                <div class="col-9"></div>
                            </div>
                            {{-- Estatus proceso de envio --}}
                            <div class="row">
                                <div class="col-2" style="padding-left: 40px; padding-top: 20px; font-weight: bold; font-size: 18px; font-family: Serif;"> {{$rastreoContrato[4]->fecharegistro}} </div>
                                @if($rastreoContrato[4]->fecharegistro != null)
                                    <div class="col-1" align="center">
                                        <i class="bi bi-hourglass-split fa-3x estado-rastreo-listo"></i>
                                    </div>
                                    <div class="col-9">
                                        <p class="estado-rastreo-titulo-habilidato">PROCESO DE ENVIO</p>
                                        <p class="estado-rastreo-descripcion-habilitado">El producto ha sido terminado, se encuentra próximo a ser enviado a la sucursal correspondiente.</p>
                                    </div>
                                @else
                                    <div class="col-1" align="center">
                                        <i class="bi bi-circle-fill estado-rastreo-pendiente"></i>
                                    </div>
                                    <div class="col-9">
                                        <p class="estado-rastreo-titulo-deshabilitado">PROCESO DE ENVIO</p>
                                    </div>

                                @endif
                            </div>
                            {{-- Renglon de flecha siguiente paso de seguimiento --}}
                            <div class="row">
                                <div class="col-2"> </div>
                                <div class="col-1" align="center">
                                    <i class="bi bi-chevron-down fa-2x @if($rastreoContrato[5]->fecharegistro != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                </div>
                                <div class="col-9"></div>
                            </div>
                            {{-- Estatus enviado--}}
                            <div class="row">
                                <div class="col-2" style="padding-left: 40px; padding-top: 20px; font-weight: bold; font-size: 18px; font-family: Serif;"> {{$rastreoContrato[5]->fecharegistro}} </div>
                                @if($rastreoContrato[5]->fecharegistro != null)
                                    <div class="col-1" align="center">
                                        <i class="bi bi-box-seam-fill fa-3x estado-rastreo-listo"></i>
                                    </div>
                                    <div class="col-9">
                                        <p class="estado-rastreo-titulo-habilidato">ENVIADO</p>
                                        <p class="estado-rastreo-descripcion-habilitado">Producto en camino a sucursal: {{$datosContrato[0]->sucursal}}, espere a que sea entregado en su domicilio.</p>
                                    </div>
                                @else
                                    <div class="col-1" align="center">
                                        <i class="bi bi-circle-fill estado-rastreo-pendiente"></i>
                                    </div>
                                    <div class="col-9">
                                        <p class="estado-rastreo-titulo-deshabilitado">ENVIADO</p>
                                    </div>
                                @endif
                            </div>
                            @if($banderaGarantia == 0 || ($banderaGarantia == 1 && $estadoContrato == 2))
                                {{-- Renglon de flecha siguiente paso de seguimiento --}}
                                <div class="row">
                                    <div class="col-2"> </div>
                                    <div class="col-1" align="center">
                                        <i class="bi bi-chevron-down fa-2x @if($rastreoContrato[6]->fecharegistro != null || $datosContrato[0]->fechaentrega != null) estado-rastreo-listo @else estado-rastreo-pendiente @endif"></i>
                                    </div>
                                    <div class="col-9"></div>
                                </div>
                                {{-- Estatus entregado --}}
                                <div class="row">
                                    <div class="col-2" style="padding-left: 40px; padding-top: 20px; font-weight: bold; font-size: 18px; font-family: Serif;"> {{$datosContrato[0]->fechaentrega}} </div>
                                    @if($rastreoContrato[6]->fecharegistro != null || $datosContrato[0]->fechaentrega != null)
                                        <div class="col-1" align="center">
                                            <i class="bi bi-check-circle-fill fa-3x estado-rastreo-listo"></i>
                                        </div>
                                        <div class="col-9">
                                            <p class="estado-rastreo-titulo-habilidato">ENTREGADO</p>
                                            <p class="estado-rastreo-descripcion-habilitado">Producto entregado en domicilio proporcionado al realizar el contrato. FECHA DE ENTREGA: {{$datosContrato[0]->fechaentrega}}</p>
                                        </div>
                                    @else
                                        <div class="col-1" align="center">
                                            <i class="bi bi-circle-fill estado-rastreo-pendiente"></i>
                                        </div>
                                        <div class="col-9">
                                            <p class="estado-rastreo-titulo-deshabilitado">ENTREGADO</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @if($banderaGarantia == 1 && $estadoContrato == 12)
                                {{-- Renglon de flecha siguiente paso de seguimiento --}}
                                <div class="row">
                                    <div class="col-2"> </div>
                                    <div class="col-1" align="center">
                                        <i class="bi bi-chevron-down fa-2x estado-rastreo-pendiente "></i>
                                    </div>
                                    <div class="col-9"></div>
                                </div>
                                {{-- Estatus entregado --}}
                                <div class="row">
                                    <div class="col-2" style="padding-left: 40px; padding-top: 20px; font-weight: bold; font-size: 18px; font-family: Serif;"> {{$datosContrato[0]->fechaentrega}} </div>
                                    <div class="col-1" align="center">
                                        <i class="bi bi-circle-fill estado-rastreo-pendiente"></i>
                                    </div>
                                    <div class="col-9">
                                        <p class="estado-rastreo-titulo-deshabilitado">ENTREGADO</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!--estado de cuenta de contrato-->
            <div class="tab-pane" id="credito" role="tabpanel" aria-labelledby="credito-tab">
                <div style="border: lightgrey solid 1px;">
                    <div id="datosEntrega" style="border-bottom:lightgrey solid 1px;">
                        <div style="margin: 10px; font-size: 12px;">
                            <div><h2 class="header-text">LABORATORIO OPTICO LUZ A TU VIDA</h2></div>
                            <div style="text-transform: uppercase; font-size: 16px; font-family: Serif;">Numero de contrato: {{$datosContrato[0]->id}}</div>
                            @if($ultimoAbono != null)
                                <div style="text-transform: uppercase; font-size: 16px; font-family: Serif;">Fecha ultimo abono: {{$ultimoAbono[0]->fechaultimoabono}} </div>
                            @endif
                        </div>
                    </div>
                    <div class="row" style="margin: 15px;">
                        <div class="col-2">
                            <div class="form-group">
                                <label class="form-label description-sm" style="font-size: 20px;">Fecha venta</label>
                                <input type="text" class="form-control" style="font-size: 18px;" value="{{$datosContrato[0]->created_at}}" readonly>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label class="form-label description-sm" style="font-size: 20px;">Total contrato</label>
                                <input type="text" class="form-control" style="font-size: 18px;" value="${{$datosContrato[0]->totalreal}}" readonly>
                            </div>
                        </div>
                        @if($datosContrato[0]->id_promocion != null)
                            <div class="col-2">
                                <div class="form-group">
                                    <label class="description-sm" style="font-size: 20px;">Total promoción</label>
                                    <input type="text" class="form-control" style="font-size: 18px;" value="${{$datosContrato[0]->totalpromocion}}" readonly>
                                </div>
                            </div>
                        @endif
                        <div class="col-2">
                            <div class="form-group">
                                <label class="description-sm" style="font-size: 20px;">Total productos</label>
                                <input type="text" class="form-control" style="font-size: 18px;" value="${{$datosContrato[0]->totalproducto}}" readonly>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label class="description-sm" style="font-size: 20px;">Total abonos</label>
                                <input type="text" class="form-control" style="font-size: 18px;" value="${{$datosContrato[0]->totalabono}}" readonly>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label class="form-label description-sm" style="font-size: 20px;">Saldo</label>
                                <input type="text" class="form-control" style="font-size: 18px;" value="${{$datosContrato[0]->total}}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin: 15px;">
                        <div class="col-6">
                            <table id="tblAbonos" class="table-bordered table-striped table-general table-sm">
                                <thead>
                                <tr>
                                    <th  style="text-align:center;" scope="col" colspan="4">ABONOS</th>
                                </tr>
                                <tr>
                                    <th  style =" text-align:center;" scope="col">FECHA ABONO</th>
                                    <th  style =" text-align:center;" scope="col">FOLIO ABONO</th>
                                    <th  style =" text-align:center;" scope="col">METODO DE PAGO</th>
                                    <th  style =" text-align:center;" scope="col">CANTIDAD</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($abonos) > 0)
                                    @foreach($abonos as $abono)
                                        <tr style="align-items: center">
                                            <td style="font-size: 12px; text-align: center; vertical-align: middle">{{$abono->created_at}}</td>
                                            <td style="font-size: 12px; text-align: center; vertical-align: middle">@if($abono->folio != null) {{$abono->folio}} @else NA @endif</td>
                                            @switch($abono->metodopago)
                                                @case(0)
                                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">EFECTIVO</td>
                                                    @break
                                                @case(1)
                                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">TARJETA</td>
                                                    @break
                                                @case(2)
                                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">TRANSFERENCIA</td>
                                                    @break
                                                @case(3)
                                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">CANCELACIÓN</td>
                                                    @break
                                            @endswitch
                                            <td style="font-size: 12px; text-align: center; vertical-align: middle">${{$abono->abono}}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td align='center' colspan="7" style="font-size: 12px;">Sin registros</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            <table id="tblAbonos" class="table-bordered table-striped table-general table-sm">
                                <thead>
                                <tr>
                                    <th  style =" text-align:center;" scope="col" colspan="4">COMPRA DE PRUDUCTOS</th>
                                </tr>
                                <tr>
                                    <th  style =" text-align:center;" scope="col">FECHA COMPRA</th>
                                    <th  style =" text-align:center;" scope="col">FOLIO</th>
                                    <th  style =" text-align:center;" scope="col">PRODUCTO</th>
                                    <th  style =" text-align:center;" scope="col">METODO DE PAGO</th>
                                    <th  style =" text-align:center;" scope="col">CANTIDAD</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($abonosProductos) > 0)
                                    @foreach($abonosProductos as $abonoProducto)
                                        <tr style="align-items: center">
                                            <td style="font-size: 12px; text-align: center; vertical-align: middle">{{$abonoProducto->created_at}}</td>
                                            <td style="font-size: 12px; text-align: center; vertical-align: middle">@if($abonoProducto->folio != null) {{$abonoProducto->folio}} @else NA @endif</td>
                                            @if($abonoProducto->tipoproducto == 1)
                                                <td style="font-size: 12px; text-align: center; vertical-align: middle">Armazón | {{$abonoProducto->nombreproducto}} | {{$abonoProducto->color}}</td>
                                            @else
                                                <td style="font-size: 12px; text-align: center; vertical-align: middle">{{$abonoProducto->nombreproducto}}</td>
                                            @endif
                                            @switch($abonoProducto->metodopago)
                                                @case(0)
                                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">EFECTIVO</td>
                                                    @break
                                                @case(1)
                                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">TARJETA</td>
                                                    @break
                                                @case(2)
                                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">TRANSFERENCIA</td>
                                                    @break
                                                @case(3)
                                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">CANCELACIÓN</td>
                                                    @break
                                            @endswitch
                                            <td style="font-size: 12px; text-align: center; vertical-align: middle">${{$abonoProducto->abono}}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td align='center' colspan="7" style="font-size: 12px;">Sin registros</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
