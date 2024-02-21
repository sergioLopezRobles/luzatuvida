@extends('layouts.app')
@section('titulo','Contratos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>@lang('mensajes.mensajeconfirmacionestado')</h2>

        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label>Estado</label>
                    <input type="text" name="estado" class="form-control" readonly value="{{$infoFranquicia[0]->estado}}">
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Ciudad</label>
                    <input type="text" name="ciudad" class="form-control" readonly value="{{$infoFranquicia[0]->ciudad}}">
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Colonia</label>
                    <input type="text" name="colonia" class="form-control" readonly value="{{$infoFranquicia[0]->colonia}}">
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Numero Interior/Exterior</label>
                    <input type="text" name="numero" class="form-control" readonly value="{{$infoFranquicia[0]->numero}}">
                </div>
            </div>
        </div>
        <hr>
        @if($tieneHistorialGarantia != null)
            <div class="row">
                <div class="col-12">
                    <p style="color: #FFFFFF; background-color: #ea9999; font-size: 80px; font-weight: bold; text-align: center">ENVIO EXPRESS</p>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-2">
                <label for="contrato">Contrato</label>
                <input name="contrato" type="text" readonly class="form-control" placeholder="Contrato" value="{{$contrato[0]->id}}">
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Total</label>
                    <input type="text" name="total" class="form-control" readonly value="$ {{$contrato[0]->totalreal}}">
                </div>
            </div>
            @if($contrato[0]->titulopromocion != null)
                <div class="col-2">
                    <div class="form-group">
                        <label>Total promocion</label>
                        <input type="text" name="totalpromocion" class="form-control" readonly value="$ {{$contrato[0]->totalpromocion}}">
                    </div>
                </div>
            @endif
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
        </div>
        <form id="formulario" action="{{route('estadoconfirmacionactualizar',[$idContrato])}}" method="POST">
            @csrf
            <div class="row">
                @if($contrato[0]->titulopromocion != null)
                    <div class="@if($tieneHistorialGarantia != null) col-5 @else col-4 @endif">
                        <div class="form-group">
                            <label>Promocion</label>
                            <input type="text" class="form-control" readonly value="{{$contrato[0]->titulopromocion}}">
                        </div>
                    </div>
                @else
                    <div class="@if($tieneHistorialGarantia != null) col-5 @else col-4 @endif"></div>
                @endif
                <div class="col-2">
                    <label for="estatusactual">Estatus actual</label>
                    @switch($contrato[0]->estatus_estadocontrato)
                        @case(1)
                            <button name="estatusactual" type="button" class="btn btn-success btn-block terminados" style="color:#FEFEFE;">{{$contrato[0]->descripcion}}</button>
                            @break
                        @case(7)
                            <button name="estatusactual" type="button" class="btn btn-primary btn-block aprobado" style="color:#FEFEFE;">{{$contrato[0]->descripcion}}</button>
                            @break
                        @case(9)
                            <button name="estatusactual" type="button" class="btn btn-danger btn-block enprocesodeaprobacion"
                                    style="color:#FEFEFE;">{{$contrato[0]->descripcion}}</button>
                            @break
                        @case(10)
                            <button name="estatusactual" type="button" class="btn btn-warning btn-block manofactura" style="color:#FEFEFE;">{{$contrato[0]->descripcion}}</button>
                            @break
                        @case(11)
                            <button name="estatusactual" type="button" class="btn btn-info btn-block enprocesodeenvio" style="color:#FEFEFE;">{{$contrato[0]->descripcion}}</button>
                            @break
                        @default
                            <button name="estatusactual" type="button" class="btn btn-info btn-block noterminados" style="color:#FEFEFE;">{{$contrato[0]->descripcion}}</button>
                    @endswitch
                </div>
                <div class="@if($tieneHistorialGarantia != null) col-3 @else col-2 @endif">
                    <label for="estatus">Estatus del contrato</label>
                    <select class="custom-select {!! $errors->first('estatus','is-invalid')!!}" name="estatus" id="estatus" @if($contrato[0]->estatus_estadocontrato != 1
                                    && $contrato[0]->estatus_estadocontrato != 7
                                    && $contrato[0]->estatus_estadocontrato != 9) disabled @endif>
                        <option value="a" selected>Seleccionar</option>
                        @if($tieneHistorialGarantia != null)
                            <option value="1">Editable</option>
                        @else
                            @if(($contrato[0]->estatus_estadocontrato != 7 && $contrato[0]->poliza != null) || $contrato[0]->poliza == null)
                                <option value="0">No terminado</option>
                            @endif
                        @endif
                        @if(($contrato[0]->estatus_estadocontrato != 7 && $contrato[0]->poliza != null) || $contrato[0]->poliza == null || $tieneHistorialGarantia != null)
                            <option value="9">En proceso de aprobacion</option>
                        @endif
                        @if($contrato[0]->estatus_estadocontrato != 7)
                            <option value="7">Aprobado</option>
                        @endif
                    </select>
                    {!! $errors->first('estatus','<div class="invalid-feedback">Por favor, selecciona un estatus valido.</div>')!!}
                    <div class="row" id="alertImagenesConfirmaciones" name="alertImagenesConfirmaciones" style="color: #ea9999; margin-left: 5px;">
                        Al aprobar el contrato estás consiente de que cuenta con imagenes pendientes por registrar.
                    </div>
                    <input type="hidden" id="poliza" value="{{$contrato[0]->poliza}}">
                    <input type="hidden" id="estatuscontrato" value="{{$contrato[0]->estatus_estadocontrato}}">
                    <input type="hidden" id="banderaImagenesPendientes" name="banderaImagenesPendientes" value="{{$banderaImagenesPendientes}}">
                </div>
                @if(($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) && $tieneHistorialGarantia == null)
                    <div class="col-2">
                        <label for="aprobacionventa">Contar para</label>
                        <select class="custom-select {!! $errors->first('aprobacionventa','is-invalid')!!}" name="aprobacionventa" id="aprobacionventa"
                                @if(($contrato[0]->estatus_estadocontrato != 1 && $contrato[0]->estatus_estadocontrato != 9) || $contrato[0]->poliza != null) disabled @endif>
                            @if($contrato[0]->titulopromocion != null)
                                <option value="0" @if($contrato[0]->contarventacontrato == 0) selected @endif>Optometrista/Asistente</option>
                                <option value="1" @if($contrato[0]->contarventacontrato == 1) selected @endif>Solo optometrista</option>
                                <option value="2" @if($contrato[0]->contarventacontrato == 2) selected @endif>Solo asistente</option>
                                <option value="3" @if($contrato[0]->contarventacontrato == 3) selected @endif>Ninguna</option>
                            @else
                                <option value="0" @if($contrato[0]->aprobacionventa == 0) selected @endif>Optometrista/Asistente</option>
                                <option value="1" @if($contrato[0]->aprobacionventa == 1) selected @endif>Solo optometrista</option>
                                <option value="2" @if($contrato[0]->aprobacionventa == 2) selected @endif>Solo asistente</option>
                                <option value="3" @if($contrato[0]->aprobacionventa == 3) selected @endif>Ninguna</option>
                            @endif
                        </select>
                        {!! $errors->first('aprobacionventa','<div class="invalid-feedback">Por favor, selecciona una opcion.</div>')!!}
                    </div>
                @endif
                @if($contrato[0]->pago != 0 && $tieneHistorialGarantia == null && ($idFranquicia != 'TXDHF' && $idFranquicia != 'WJPQB')
                                && ($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7))
                    <div class="col-1">
                        <label for="abonominimocontrato">Abono minimo</label>
                        <select class="custom-select {!! $errors->first('abonominimocontrato','is-invalid')!!}" name="abonominimocontrato" id="abonominimocontrato"
                                @if($contrato[0]->estatus_estadocontrato == 7) disabled @endif>
                            @switch($contrato[0]->pago)
                                @case(1)
                                    <option value="250" @if($contrato[0]->abonominimo == 250) selected @endif>$250</option>
                                    <option value="200" @if($contrato[0]->abonominimo == 200) selected @endif>$200</option>
                                    @break
                                @case(2)
                                    <option value="500" @if($contrato[0]->abonominimo == 500) selected @endif>$500</option>
                                    <option value="400" @if($contrato[0]->abonominimo == 400) selected @endif>$400</option>
                                    @break
                                @case(4)
                                    <option value="800" @if($contrato[0]->abonominimo == 800) selected @endif>$800</option>
                                    <option value="600" @if($contrato[0]->abonominimo == 600) selected @endif>$600</option>
                                    @break
                            @endswitch
                        </select>
                        {!! $errors->first('abonominimocontrato','<div class="invalid-feedback">Por favor, selecciona una opcion.</div>')!!}
                    </div>
                @endif
                <div class="@if($contrato[0]->pago != 0 && $tieneHistorialGarantia == null && ($idFranquicia != 'TXDHF' && $idFranquicia != 'WJPQB') && ($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9)) col-1 @else col-2 @endif">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button class="btn btn-outline-success btn-block" type="submit" @if($contrato[0]->estatus_estadocontrato != 1
                                    && $contrato[0]->estatus_estadocontrato != 7
                                    && $contrato[0]->estatus_estadocontrato != 9) disabled @endif>@lang('mensajes.mensajebotonconfirmacionestado')</button>
                    </div>
                </div>
            </div>
        </form>
        @if(($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) && $tieneHistorialGarantia == null)
            <form action="{{route('actualizaresperapolizacontrato',[$idContrato])}}"
                  method="POST">
                @csrf
                <div class="row">
                    <div class="col-4"></div>
                    <div class="col-1">
                        <div class="custom-control custom-switch" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" name="esperapoliza" id="esperapoliza"
                                   @if($contrato[0]->esperapoliza == 1) checked @endif
                                   @if($contrato[0]->poliza != null) disabled @endif
                                   onclick="eventactualizaresperapolizacontrato(event)">
                            <label class="custom-control-label" for="esperapoliza" style="font-size: 15px">Espera</label>
                        </div>
                    </div>
                </div>
            </form>
        @endif
        @if(($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) && $contrato[0]->subscripcion == null)
            <form action="{{route('actualizarTotalContratoConfirmaciones',$contrato[0]->id)}}"
                  enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="row">
                    <div class="col-2"></div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Actualizar total:</label>
                            <input type="number" name="totalActualizado"
                                   class="form-control {!! $errors->first('totalActualizado','is-invalid')!!}" placeholder="0"  min="0" required>
                            {!! $errors->first('totalActualizado','<div class="invalid-feedback">Ingresa cantidad para nuevo total.</div>')!!}
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button class="btn btn-outline-success btn-block" type="submit">@lang('mensajes.actualizar')</button>
                        </div>
                    </div>
                </div>
            </form>
        @endif
        <div class="row">
            {{--        Formulario para actualizar fecha --}}
            <div class="col-6">
                <form action="{{route('actualizarfechaentregaconfirmaciones',[$idContrato])}}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Fecha entrega</label>
                                <input @if($contrato[0]->estatus_estadocontrato != 1
                                    && $contrato[0]->estatus_estadocontrato != 7
                                    && $contrato[0]->estatus_estadocontrato != 9) disabled @endif
                                type="date" name="fechaentrega" class="form-control {!! $errors->first('fechaentrega','is-invalid')!!}"
                                       @isset($contrato[0]->fechaentrega) value = "{{$contrato[0]->fechaentrega}}" @endisset>
                                @if($errors->has('fechaentrega'))
                                    <div class="invalid-feedback">{{$errors->first('fechaentrega')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button class="btn btn-outline-success btn-block" type="submit"
                                        @if($contrato[0]->estatus_estadocontrato != 1
                                                && $contrato[0]->estatus_estadocontrato != 7
                                                && $contrato[0]->estatus_estadocontrato != 9) disabled @endif>Actualizar fecha
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            {{--        Formulario para actualizar forma de pago--}}
            @if(($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9) && $contrato[0]->total > 0)
                <div class="col-6">
                    <form action="{{route('actualizarformapagoconfirmaciones',$contrato[0]->id)}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-2"></div>
                            <input type="hidden" name="idFranquiciaContrato" id="idFranquiciaContrato" value="{{$idFranquicia}}">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="formapago">Forma de pago</label>
                                    <select class="custom-select {!! $errors->first('formapago','is-invalid')!!}" name="formapago" id="formapago">
                                        <option value="nada" selected>Todas las formas de pago</option>
                                        <option value="1" {{ isset($contrato[0]->pago) ? ($contrato[0]->pago == 1 ? 'selected' : '' ) : '' }}>Semanal</option>
                                        <option value="2" {{ isset($contrato[0]->pago) ? ($contrato[0]->pago == 2 ? 'selected' : '' ) : '' }}>Quincenal</option>
                                        <option value="4" {{ isset($contrato[0]->pago) ? ($contrato[0]->pago == 4 ? 'selected' : '' ) : '' }}>Mensual</option>
                                    </select>
                                    {!! $errors->first('formapago','<div class="invalid-feedback">Por favor, selecciona la forma de pago.</div>')!!}
                                    @if($contrato[0]->pago != 4)
                                        <p style="color: #ea9999">Al cambiar a MENSUAL aceptas que actualizaste las fotos de tarjetas de pensión.</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button class="btn btn-outline-success btn-block" type="submit"
                                            @if($contrato[0]->estatus_estadocontrato != 1
                                                    && $contrato[0]->estatus_estadocontrato != 7
                                                    && $contrato[0]->estatus_estadocontrato != 9) disabled @endif>Actualizar forma pago
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!--Formulario para producto y abono minimo-->
        <div class="row">
            <div class="col-6">
                @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9)
                    <form id="frmagregarproductoconfirmaciones" action="{{route('agregarproductoconfirmaciones',[$idContrato])}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <label>Producto</label>
                                <select name="producto"
                                        class="form-control">
                                    <option selected value=''>Seleccionar</option>
                                    @foreach($productos as $pro)
                                        @if ($pro->piezas >= 10)
                                            @if($pro->preciop == null)
                                                <option value="{{$pro->id}}">{{$pro->nombre}} | $ {{ $pro->precio }}
                                                    | {{$pro->piezas}}pza.
                                                </option>
                                            @else
                                                <option value="{{$pro->id}}">{{$pro->nombre}} | Normal :
                                                    $ {{ $pro->precio }} | Con
                                                    descuento: $ {{ $pro->preciop }} | {{$pro->piezas}}pza.
                                                </option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <a type="button" href="" class="btn btn-block btn-outline-success" data-toggle="modal" style="margin-top: 30px"
                                       data-target="#modalagregarproductoconfirmaciones">Agregar</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- modal para agregarproductoconfirmaciones contratos -->
                    <div class="modal fade" id="modalagregarproductoconfirmaciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    Solicitud de confirmación
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12" style="color: #ea9999">
                                            Esta accion no se podra revertir, al aceptar confirmas que se te entrego la evidencia de la adquisición del producto.
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                                    <button class="btn btn-success" name="btnSubmit" type="submit" form="frmagregarproductoconfirmaciones">Aceptar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-6">
                <div class="row">
                    <div class="col-2"></div>
                    @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9 && ($contrato[0]->estadogarantia == null &&
                $contrato[0]->total > 0))
                        <div class="col-10">
                            @if($solicitudAbonoMinimo != null)
                                <form action="{{route('agregarabonominimoconfirmaciones',[$contrato[0]->id])}}" enctype="multipart/form-data"
                                      method="GET" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="row">
                                        <div class="col-7">
                                            <label>Abono minimo</label>
                                            @if($solicitudAbonoMinimo[0]->estatus == 0)
                                                <div class="row" style="color: #0AA09E; font-weight: bold; padding-top:5px; padding-left: 15px;">
                                                    Solicitud de abono minimo pendiente.
                                                </div>
                                            @endif
                                            @if($solicitudAbonoMinimo[0]->estatus == 1)
                                                <input type="number" name="abonoMinimo" min="150"
                                                       class="form-control {!! $errors->first('abonoMinimo')!!}"
                                                       placeholder="Abono minimo" value="{{ $contrato[0]->abonominimo}}">
                                                {!! $errors->first('abonoMinimo','<div class="invalid-feedback">El abono minimo es
                                                obligatorio, debera ser mayor a 150</div>')!!}
                                            @endif
                                            @if($solicitudAbonoMinimo[0]->estatus == 2)
                                                <a class="btn btn-outline-success btn-block" data-toggle="modal"
                                                   data-target="#modalsolicitarabonominimo" style="margin-top: 0px; margin-bottom: 0px;">Solicitar cambio</a>
                                                <div class="row" style="color: #ea9999; font-weight: bold; margin-left: 5px;">
                                                    Ultima solicitud de abono minimo rechazada.
                                                </div>
                                            @endif
                                            @if($solicitudAbonoMinimo[0]->estatus == 3)
                                                <a class="btn btn-outline-success btn-block" data-toggle="modal"
                                                   data-target="#modalsolicitarabonominimo" style="margin-top: 0px; margin-bottom: 0px;">Solicitar cambio</a>
                                            @endif
                                        </div>
                                        @if($solicitudAbonoMinimo[0]->estatus == 1)
                                            <div class="col-5">
                                                <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit" style="margin-top: 30px;">Actualizar</button>
                                            </div>
                                    </div>
                            @endif
                        </div>
                        </form>
                    @else
                        <div class="row">
                            <div class="col-7">
                                <label>Abono minimo</label>
                                <a class="btn btn-outline-success btn-block" data-toggle="modal"
                                   data-target="#modalsolicitarabonominimo" style="margin-top: 0px; margin-bottom: 0px;">Solicitar cambio</a>
                            </div>
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <table id="tablaCP" class="table-bordered table-striped table-general">
        @if(sizeof($contratoproducto)>0)
            <thead>
            <tr>
                <th style=" text-align:center;" scope="col">PRODUCTO</th>
                <th style=" text-align:center;" scope="col">PRECIO</th>
                <th style=" text-align:center;" scope="col">PIEZAS</th>
                <th style=" text-align:center;" scope="col">TOTAL</th>
                <th style=" text-align:center;" scope="col">ELIMINAR</th>
            </tr>
            </thead>
        @endif
        <tbody>
        @foreach($contratoproducto as $CP)
            <tr>
                <td align='center'>{{$CP->nombre}}</td>
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
            </tr>
        @endforeach
        </tbody>
    </table>

    <hr>

    <form action="{{route('agregarnotaconfirmaciones',[$contrato[0]->id])}}"
          method="POST" onsubmit="btnSubmit.disabled = true;">
        @csrf
        <div class="row">
            <div class="col-10">
                <div class="form-group">
                    <label>Nota del cobrador</label>
                    <input type="text" name="nota" maxlength="255"
                           class="form-control {!! $errors->first('nota','is-invalid')!!}"
                           placeholder="Nota del cobrador" value="{{$contrato[0]->nota}}">
                    {!! $errors->first('nota','<div class="invalid-feedback">No puede superar los 255 caracteres.</div>')!!}
                </div>
            </div>
            <div class="col-2" style="margin-top: 30px">
                <button class="btn btn-outline-success btn-block" name="btnSubmit"
                        type="submit">@lang('mensajes.mensajeactualizarnota')</button>
            </div>
        </div>
    </form>
    <hr>
    <div class="row">
        <div class="col-10"><h4>Abonos</h4></div>
    </div>
    <table id="tablaAbonos" class="table-bordered table-striped table-general table-sm">
        @if(sizeof($abonos)>0)
            <thead>
            <tr>
                <th style=" text-align:center;" scope="col">CODIGO</th>
                <th style=" text-align:center;" scope="col">ABONO</th>
                <th style=" text-align:center;" scope="col">FECHA</th>
                <th style=" text-align:center;" scope="col">TIPO DE ABONO</th>
                <th style=" text-align:center;" scope="col">FORMA DE PAGO</th>
            </tr>
            </thead>
        @endif
        <tbody>
        @foreach($abonos as $ab)

            <tr>
                <td align='center' width="7%">{{$ab->id}}</td>
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
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr>
    <div class="row">
        <div class="col-10"><h4>Productos</h4></div>
    </div>
    <table id="tablaProductosContrato" class="table-bordered table-striped table-general table-sm">
        @if(sizeof($contratoproducto)>0)
            <thead>
            <tr>
                <th style=" text-align:center;" scope="col">PRODUCTO</th>
                <th style=" text-align:center;" scope="col">PRECIO</th>
                <th style=" text-align:center;" scope="col">PIEZAS</th>
                <th style=" text-align:center;" scope="col">TOTAL</th>
            </tr>
            </thead>
        @endif
        <tbody>

        @foreach($contratoproducto as $CP)

            <tr>
                @if($CP->id_tipoproducto == 1)
                    <td align='center'>Armazon: {{$CP->nombre}} | {{$CP->color}}</td>
                @else
                    <td align='center'>{{$CP->nombre}}</td>
                @endif
                @if($CP->preciop == null)
                    <td align='center'>$ {{$CP->precio}}</td>
                @else
                    <td align='center'>$ {{$CP->preciop}}</td>
                @endif
                <td align='center'>{{$CP->piezas}}</td>
                <td align='center'>$ {{$CP->total}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <hr>

    @if(sizeof($promociones)>0)
        <div class="row">
            <div class="col-10"><h4>Promociones activas de la sucursal</h4></div>
        </div>
        <table id="tablaPromociones" class="table-bordered table-striped table-general" style="width: 100%; margin-bottom: 20px;">
            <thead>
            <tr>
                <th style="text-align:center;" scope="col">TITULO</th>
                <th style="text-align:center;" scope="col">PRECIO</th>
                <th style="text-align:center;" scope="col">INICIO</th>
                <th style="text-align:center;" scope="col">FIN</th>
            </tr>
            </thead>
            <tbody>
            @foreach($promociones as $promocion)
                <tr>
                    <td style="text-align:center; font-size: 11px; font-family: Calibri;">{{$promocion->titulo}}</td>
                    @if($promocion->tipopromocion  == 1)
                        <td style="text-align:center; font-size: 11px; font-family: Calibri;">$ {{$promocion->preciouno}}</td>
                    @else
                        <td style="text-align:center; font-size: 11px; font-family: Calibri;">% {{$promocion->precioP}}</td>
                    @endif
                    <td style="text-align:center; font-size: 11px; font-family: Calibri;">{{$promocion->inicio}}</td>
                    <td style="text-align:center; font-size: 11px; font-family: Calibri;">{{$promocion->fin}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    <h4> Promoción contrato </h4>
    <div class="row">
        <div class="col-6">
            @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9)
                @if($contrato[0]->id_promocion === null)
                    @if($contrato[0]->total > 0)
                        @if($contrato[0]->enganche < 1)
                            @if($solicitudPromocion != null)
                                @if($solicitudPromocion[0]->estatus == 0)
                                    <div style="color: #0AA09E; font-weight: bold;"> Solicitud de promoción pendiente.</div>
                                @endif
                                @if($solicitudPromocion[0]->estatus == 2)
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for=""><br></label>
                                                <select
                                                    class="custom-select {!! $errors->first('promocion','is-invalid')!!}"
                                                    name="promocion">
                                                    @if(count($promocionesConfirmaciones) > 0)
                                                        <option selected value="0">Seleccionar</option>
                                                        @foreach($promocionesConfirmaciones as $promocion)
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
                                                        type="submit" style="margin-top: 30px;">AGREGAR PROMOCIÓN
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="color: #ea9999; font-weight: bold;"> Ultima solicitud de promocion rechazada, solicitar nuevamente.</div>
                                    </form>
                                @endif
                            @else
                                <form action="{{route('agregarpromocionconfirmaciones',$idContrato)}}"
                                      enctype="multipart/form-data" method="GET" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for=""><br></label>
                                                <select
                                                    class="custom-select {!! $errors->first('promocion','is-invalid')!!}"
                                                    name="promocion">
                                                    @if(count($promocionesConfirmaciones) > 0)
                                                        <option selected value="0">Seleccionar</option>
                                                        @foreach($promocionesConfirmaciones as $promocion)
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
                                                        type="submit" style="margin-top: 30px;">AGREGAR PROMOCIÓN
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endif
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
                <th style=" text-align:center;" scope="col">ELIMINAR</th>
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
                @if($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9)
                    <td align='center'><a href="{{route('eliminarpromocionconfirmaciones',[$idContrato, $promocion->id])}}"
                                          name="btnSubmit" class="btn btn-outline-danger btn-sm">ELIMINAR</a></td>
                @else
                    <td align='center'></td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>

    <hr>
    @if($contrato[0]->coordenadas != null)
        <div class="row">
            <div style="position:relative;text-align:right;height:325px;width:100%;">
                <iframe style="overflow:hidden;background:none!important;height:325px;width:100%;"
                        src="https://maps.google.com/maps?q={{$contrato[0]->coordenadas}}&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed">
                </iframe>
            </div>
        </div>
        <hr>
    @endif

    <form action="{{route('actualizarContratoConfirmaciones',$contrato[0]->id)}}"
          enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
        @csrf
        <div class="row">
            <div class="col-1">
                <label for="">Zona</label>
                @if($tieneHistorialGarantia == null)
                    <select class="custom-select {!! $errors->first('zona','is-invalid')!!}" name="zona">
                        @if(count($zonas) > 0)
                            <option selected value="">Seleccionar</option>
                            @foreach($zonas as $zona)
                                <option
                                    value="{{$zona->id}}" {{ isset($contrato) ? ($contrato[0]->id_zona== $zona->id ? 'selected' : '' ) : '' }}>{{$zona->zona}}</option>
                            @endforeach
                        @else
                            <option selected>Sin registros</option>
                        @endif
                    </select>
                    {!! $errors->first('zona','<div class="invalid-feedback">Elegir una zona, campo obligatorio </div>
                    ')!!}
                @else
                    <input type="text" class="form-control" readonly value="{{$contrato[0]->zona}}">
                @endif
            </div>
            <div class="col-2">
                <label>Optometrista</label>
                @if(($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) && $tieneHistorialGarantia == null)
                    <select class="custom-select" name="optometrista">
                        <option selected value="{{$contrato[0]->id_optometrista}}">{{$contrato[0]->opto}}</option>
                        @foreach($optometristas as $optometrista)
                            @if($optometrista->ID == $contrato[0]->id_optometrista)
                                @continue
                            @else
                                <option value="{{$optometrista->ID}}">{{$optometrista->NAME}}</option>
                            @endif
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" readonly value="{{$contrato[0]->opto}}">
                @endif
                <input hidden id="nombreoptometristaoculto" value="{{$contrato[0]->opto}}">
                <a class="btn btn-outline-success btn-sm mt-1" onclick="copiarNombreOptometristaOAsistente(0);">Copiar</a>
            </div>
            <div class="col-2">
                <label for="">Asistente</label>
                @if(($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) && $tieneHistorialGarantia == null)
                    <select class="custom-select" name="asistente">
                        <option selected value="{{$contrato[0]->id_usuariocreacion}}">{{$contrato[0]->nombreasistente}}</option>
                        @foreach($asistentes as $asistente)
                            @if($asistente->ID == $contrato[0]->id_usuariocreacion)
                                @continue
                            @else
                                <option value="{{$asistente->ID}}">{{$asistente->NAME}}</option>
                            @endif
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" readonly value="{{$contrato[0]->nombreasistente}}">
                @endif
                <input hidden id="nombreasistenteoculto" value="{{$contrato[0]->nombreasistente}}">
                <a class="btn btn-outline-success btn-sm mt-1" onclick="copiarNombreOptometristaOAsistente(1);">Copiar</a>
            </div>
            <div class="col-1">
                <div class="form-group">
                    <label>Edad</label>
                    <input type="text" name="edad"
                           class="form-control {!! $errors->first('edad','is-invalid')!!}" placeholder="Edad"
                           value="{{$contrato[0]->edad}}">
                    {!! $errors->first('edad','<div class="invalid-feedback">La edad es obligatoria.</div>')!!}
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Correo electronico</label>
                    <input type="text" class="form-control" readonly value="{{$contrato[0]->correo}}">
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Ubicacion</label>
                    <input type="text" name="coordenadas"
                           class="form-control {!! $errors->first('coordenadas','is-invalid')!!}" placeholder="Coordenadas"
                           value="{{$contrato[0]->coordenadas}}">
                    {!! $errors->first('coordenadas','<div class="invalid-feedback">Campo obligatorio</div>')!!}
                </div>
            </div>
            <div class="col-1" style="margin-top: 30px">
                <a href="{{url('https://www.google.com/maps/place?key=AIzaSyC4wzK36yxyLG6yzpqUPnV4j8Y74aKkq-M&q=' . $contrato[0]->coordenadas)}}" target="_blank">
                    <button type="button" class="btn btn-outline-success">Ver ubicación</button>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label>Nombre del cliente</label>
                    <input type="text" name="nombre"
                           class="form-control {!! $errors->first('nombre','is-invalid')!!}" placeholder="Nombre"
                           value="{{$contrato[0]->nombre}}">
                    {!! $errors->first('nombre','<div class="invalid-feedback">El nombre es obligatorio.</div>')!!}
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Alias del cliente</label>
                    <input type="text" name="alias"
                           class="form-control {!! $errors->first('alias','is-invalid')!!}" placeholder="Alias"
                           value="{{$contrato[0]->alias}}">
                    {!! $errors->first('alias','<div class="invalid-feedback">El alias es obligatorio.</div>')!!}
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Telefono del paciente</label>
                    <input type="text" name="telefono"
                           class="form-control {!! $errors->first('telefono','is-invalid')!!}" placeholder="Telefono"
                           value="{{$contrato[0]->telefono}}">
                    {!! $errors->first('telefono','<div class="invalid-feedback">El telefono debe contener 10
                        numeros.</div>')!!}
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Nombre referencia</label>
                    <input type="text" name="nr" class="form-control {!! $errors->first('nr','is-invalid')!!}"
                           placeholder="Nombre de referencia" value="{{$contrato[0]->nombrereferencia}}">
                    {!! $errors->first('nr','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Telefono referencia</label>
                    <input type="text" name="tr" class="form-control {!! $errors->first('tr','is-invalid')!!}"
                           placeholder="Telefono de referencia" value="{{$contrato[0]->telefonoreferencia}}">
                    {!! $errors->first('tr','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                </div>
            </div>
        </div>
        <h3>Lugar de venta</h3>
        <div class="row">
            <div class="col-1">
                <div class="form-group">
                    <label>Numero</label>
                    <input type="text" name="numero"
                           class="form-control {!! $errors->first('numero','is-invalid')!!}" placeholder="Numero"
                           value="{{$contrato[0]->numero}}">
                    {!! $errors->first('numero','<div class="invalid-feedback">El numero es obligatorio.</div>')!!}
                </div>
            </div>
            <div class="col-1">
                <div class="form-group">
                    <label>Departamento</label>
                    <input type="text" name="departamento"
                           class="form-control {!! $errors->first('departamento','is-invalid')!!}"
                           placeholder="Departamento" value="{{$contrato[0]->depto}}">
                    {!! $errors->first('departamento','<div class="invalid-feedback">Campo obligatorio.</div>
                    ')!!}
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label id="alladode">Al lado de</label>
                    <input type="text" name="alladode"
                           class="form-control {!! $errors->first('alladode','is-invalid')!!}" placeholder="Al lado de"
                           value="{{$contrato[0]->alladode}}">
                    {!! $errors->first('alladode','<div class="invalid-feedback">Campo obligatorio</div>')!!}
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Frente a</label>
                    <input type="text" name="frentea"
                           class="form-control {!! $errors->first('frentea','is-invalid')!!}" placeholder="Frente a"
                           value="{{$contrato[0]->frentea}}">
                    {!! $errors->first('frentea','<div class="invalid-feedback">Campo obligatorio</div>')!!}
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Calle</label>
                    <input type="text" name="calle" class="form-control {!! $errors->first('calle','is-invalid')!!}"
                           placeholder="Calle" value="{{$contrato[0]->calle}}">
                    {!! $errors->first('calle','<div class="invalid-feedback">La calle es obligatoria.</div>')!!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label>Entre calles</label>
                    <input type="text" name="entrecalles"
                           class="form-control {!! $errors->first('entrecalles','is-invalid')!!}"
                           placeholder="Entre calles" value="{{$contrato[0]->entrecalles}}">
                    {!! $errors->first('entrecalles','<div class="invalid-feedback">El campo es obligatorio.</div>
                    ')!!}
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Colonia</label>
                    <div class="row">
                        <div class="col-8">
                            <input type="text" name="colonia"
                                   class="form-control {!! $errors->first('colonia','is-invalid')!!}" placeholder="Colonia"
                                   value="{{$contrato[0]->colonia}}">
                        </div>
                        <div class="col-4">
                            <a type="bottom" class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#modalColoniasConfirmaciones">Ver colonias</a>
                        </div>
                    </div>
                    {!! $errors->first('colonia','<div class="invalid-feedback">La colonia es obligatoria.</div>')!!}
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Localidad</label>
                    <input type="text" name="localidad"
                           class="form-control {!! $errors->first('localidad','is-invalid')!!}" placeholder="Localidad"
                           value="{{$contrato[0]->localidad}}">
                    {!! $errors->first('localidad','<div class="invalid-feedback">La localidad es obligatoria.</div>
                    ')!!}
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Tipo Casa</label>
                    <input type="text" name="casatipo"
                           class="form-control {!! $errors->first('casatipo','is-invalid')!!}" placeholder="Tipo Casa"
                           value="{{$contrato[0]->casatipo}}">
                    {!! $errors->first('casatipo','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Casa color</label>
                    <input type="text" name="casacolor"
                           class="form-control {!! $errors->first('casacolor','is-invalid')!!}"
                           placeholder="Casa color" value="{{$contrato[0]->casacolor}}">
                    {!! $errors->first('casacolor','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                </div>
            </div>
        </div>
        <h3>Lugar de cobranza</h3>
        <div class="row">
            <div class="col-1">
                <div class="form-group">
                    <label>Numero</label>
                    <input type="text" name="numeroentrega"
                           class="form-control {!! $errors->first('numeroentrega','is-invalid')!!}" placeholder="Numero"
                           value="{{$contrato[0]->numeroentrega}}">
                    {!! $errors->first('numeroentrega','<div class="invalid-feedback">El numero es obligatorio.</div>')!!}
                </div>
            </div>
            <div class="col-1">
                <div class="form-group">
                    <label>Departamento</label>
                    <input type="text" name="departamentoentrega"
                           class="form-control {!! $errors->first('departamentoentrega','is-invalid')!!}"
                           placeholder="Departamento" value="{{$contrato[0]->deptoentrega}}">
                    {!! $errors->first('departamentoentrega','<div class="invalid-feedback">Campo obligatorio.</div>
                    ')!!}
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label id="alladodeentrega">Al lado de</label>
                    <input type="text" name="alladodeentrega"
                           class="form-control {!! $errors->first('alladodeentrega','is-invalid')!!}" placeholder="Al lado de"
                           value="{{$contrato[0]->alladodeentrega}}">
                    {!! $errors->first('alladodeentrega','<div class="invalid-feedback">Campo obligatorio</div>')!!}
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Frente a</label>
                    <input type="text" name="frenteaentrega"
                           class="form-control {!! $errors->first('frenteaentrega','is-invalid')!!}" placeholder="Frente a"
                           value="{{$contrato[0]->frenteaentrega}}">
                    {!! $errors->first('frenteaentrega','<div class="invalid-feedback">Campo obligatorio</div>')!!}
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Calle</label>
                    <input type="text" name="calleentrega" class="form-control {!! $errors->first('calleentrega','is-invalid')!!}"
                           placeholder="Calle" value="{{$contrato[0]->calleentrega}}">
                    {!! $errors->first('calleentrega','<div class="invalid-feedback">La calle es obligatoria.</div>')!!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label>Entre calles</label>
                    <input type="text" name="entrecallesentrega"
                           class="form-control {!! $errors->first('entrecallesentrega','is-invalid')!!}"
                           placeholder="Entre calles" value="{{$contrato[0]->entrecallesentrega}}">
                    {!! $errors->first('entrecallesentrega','<div class="invalid-feedback">El campo es obligatorio.</div>
                    ')!!}
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Colonia</label>
                    <input type="text" name="coloniaentrega"
                           class="form-control {!! $errors->first('coloniaentrega','is-invalid')!!}" placeholder="Colonia"
                           value="{{$contrato[0]->coloniaentrega}}">
                    {!! $errors->first('coloniaentrega','<div class="invalid-feedback">La colonia es obligatoria.</div>
                    ')!!}
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Localidad</label>
                    <input type="text" name="localidadentrega"
                           class="form-control {!! $errors->first('localidadentrega','is-invalid')!!}" placeholder="Localidad"
                           value="{{$contrato[0]->localidadentrega}}">
                    {!! $errors->first('localidadentrega','<div class="invalid-feedback">La localidad es obligatoria.</div>
                    ')!!}
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Tipo Casa</label>
                    <input type="text" name="casatipoentrega"
                           class="form-control {!! $errors->first('casatipoentrega','is-invalid')!!}" placeholder="Tipo Casa"
                           value="{{$contrato[0]->casatipoentrega}}">
                    {!! $errors->first('casatipoentrega','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Casa color</label>
                    <input type="text" name="casacolorentrega"
                           class="form-control {!! $errors->first('casacolorentrega','is-invalid')!!}"
                           placeholder="Casa color" value="{{$contrato[0]->casacolorentrega}}">
                    {!! $errors->first('casacolorentrega','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-10"></div>
            <div class="col-2">
                <button class="btn btn-outline-success btn-block"
                        name="btnSubmit" type="submit" @if($contrato[0]->estatus_estadocontrato != 1
                                    && $contrato[0]->estatus_estadocontrato != 7
                                    && $contrato[0]->estatus_estadocontrato != 9
                                    && $contrato[0]->estatus_estadocontrato != 10) disabled @endif>@lang('mensajes.mensajeactuzalizarcontrato')</button>
            </div>
        </div>
    </form>
    <h3>Lugar de entrega</h3>
    <form action="{{route('actualizarlugarentregaconfirmaciones',[$idContrato])}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-3">
                <select class="custom-select {!! $errors->first('lugarEntrega','is-invalid')!!}" name="lugarEntrega" id="lugarEntrega" @if($contrato[0]->estatus_estadocontrato != 1
                                    && $contrato[0]->estatus_estadocontrato != 9) disabled @endif>
                    <option value="" selected>Seleccionar</option>
                    <option value="0" @if($contrato[0]->opcionlugarentrega == 0 || $contrato[0]->opcionlugarentrega == null) selected @endif>Lugar de venta</option>
                    <option value="1" @if($contrato[0]->opcionlugarentrega == 1) selected @endif>Lugar de cobranza</option>
                </select>
                {!! $errors->first('lugarEntrega','<div class="invalid-feedback">Selecciona un lugar de entrega valido.</div>')!!}
            </div>
            <div class="col-3">
                <div class="form-group">
                    <button class="btn btn-outline-success btn-block @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9)  visible @else invisible @endif"
                            type="submit">Actualizar lugar de entrega</button>
                </div>
            </div>
        </div>
    </form>
    <hr>
    <form action="{{route('confirmacionesagregardocumentos',[$idContrato])}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-3">
                <div data-toggle="modal" data-target="#imagemodal" id="img1" style="cursor: pointer">
                    <img src="{{asset($contrato[0]->fotoine)}}" style="width:250px;height:250px;" class="img-thumbnail">
                </div>
                <input type="text" name="observacionfotoine" id="observacionfotoine" class="form-control" placeholder="Observación"
                       value="{{$contrato[0]->observacionfotoine}}" style="width:250px;">
            </div>
            <div class="col-3">
                <div data-toggle="modal" data-target="#imagemodal" id="img2" style="cursor: pointer">
                    <img src="{{asset($contrato[0]->fotoineatras)}}" style="width:250px;height:250px;" class="img-thumbnail">
                </div>
                <input type="text" name="observacionfotoineatras" id="observacionfotoineatras" class="form-control" placeholder="Observación"
                       value="{{$contrato[0]->observacionfotoineatras}}" style="width:250px;">
            </div>
            <div class="col-3">
                <div data-toggle="modal" data-target="#imagemodal" id="img3" style="cursor: pointer">
                    <img src="{{asset($contrato[0]->fotocasa)}}" style="width:250px;height:250px;" class="img-thumbnail">
                </div>
                <input type="text" name="observacionfotocasa" id="observacionfotocasa" class="form-control" placeholder="Observación"
                       value="{{$contrato[0]->observacionfotocasa}}" style="width:250px;">
            </div>
            <div class="col-3">
                <div data-toggle="modal" data-target="#imagemodal" id="img4" style="cursor: pointer">
                    <img src="{{asset($contrato[0]->comprobantedomicilio)}}" style="width:250px;height:250px;" class="img-thumbnail">
                </div>
                <input type="text" name="observacioncomprobantedomicilio" id="observacioncomprobantedomicilio" class="form-control" placeholder="Observación"
                       value="{{$contrato[0]->observacioncomprobantedomicilio}}" style="width:250px;">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="col-3">
                <div data-toggle="modal" data-target="#imagemodal" id="img5" style="cursor: pointer">
                    <img src="{{asset($contrato[0]->pagare)}}" style="width:250px;height:250px;" class="img-thumbnail">
                </div>
                <input type="text" name="observacionpagare" id="observacionpagare" class="form-control" placeholder="Observación"
                       value="{{$contrato[0]->observacionpagare}}" style="width:250px;">
            </div>
            @if($contrato[0]->fotootros != null && strlen($contrato[0]->fotootros)>0)
                <div class="col-3">
                    <div data-toggle="modal" data-target="#imagemodal" id="img6" style="cursor: pointer">
                        <img src="{{asset($contrato[0]->fotootros)}}" style="width:250px;height:250px;" class="img-thumbnail">
                    </div>
                    <input type="text" name="observacionfotootros" id="observacionfotootros" class="form-control" placeholder="Observación"
                           value="{{$contrato[0]->observacionfotootros}}" style="width:250px;">
                </div>
            @endif
            @if($contrato[0]->tarjeta != null && strlen($contrato[0]->tarjeta)>0)
                <div class="col-3">
                    <div data-toggle="modal" data-target="#imagemodal" id="img7" style="cursor: pointer">
                        <img src="{{asset($contrato[0]->tarjeta)}}" style="width:250px;height:250px;" class="img-thumbnail">
                    </div>
                </div>
            @endif
            @if( $contrato[0]->tarjeta != null && strlen($contrato[0]->tarjetapensionatras)>0)
                <div class="col-3">
                    <div data-toggle="modal" data-target="#imagemodal" id="img8" style="cursor: pointer">
                        <img src="{{asset($contrato[0]->tarjetapensionatras)}}" style="width:250px;height:250px;" class="img-thumbnail">
                    </div>
                </div>
            @endif
        </div>
        <hr>
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label>Foto INE Frente</label>
                    <input type="file" name="fotoine"
                           class="form-control-file  {!! $errors->first('fotoine','is-invalid')!!}" accept="image/jpg">
                    {!! $errors->first('fotoine','<div class="invalid-feedback">La foto debera estar en formato jpg.
                    </div>')!!}
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Foto INE Atrás</label>
                    <input type="file" name="fotoineatras"
                           class="form-control-file  {!! $errors->first('fotoineatras','is-invalid')!!}"
                           accept="image/jpg">
                    {!! $errors->first('fotoineatras','<div class="invalid-feedback">Llenar ambos campos del INE
                    </div>')!!}
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Pagare:</label>
                    <input type="file" name="pagare"
                           class="form-control-file  {!! $errors->first('pagare','is-invalid')!!}" accept="image/jpg">
                    {!! $errors->first('pagare','<div class="invalid-feedback">La foto debera estar en formato jpg.
                    </div>')!!}
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Foto de la casa</label>
                    <input type="file" name="fotocasa"
                           class="form-control-file  {!! $errors->first('fotocasa','is-invalid')!!}"
                           accept="image/jpg">
                    {!! $errors->first('fotocasa','<div class="invalid-feedback">La foto debera estar en formato
                        jpg.</div>')!!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label>Comprobante de domicilio</label>
                    <input type="file" name="comprobantedomicilio"
                           class="form-control-file  {!! $errors->first('comprobantedomicilio','is-invalid')!!}"
                           accept="image/jpg">
                    {!! $errors->first('comprobantedomicilio','<div class="invalid-feedback">La foto debera estar en
                        formato jpg.</div>')!!}
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Tarjeta de pensión frente:</label>
                    <input type="file" name="tarjetapension"
                           class="form-control-file  {!! $errors->first('tarjetapension','is-invalid')!!}"
                           accept="image/jpg">
                    {!! $errors->first('tarjetapension','<div class="invalid-feedback">La foto debera estar en
                        formato jpg.</div>')!!}
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Tarjeta de pensión Atras:</label>
                    <input type="file" name="tarjetapensionatras"
                           class="form-control-file  {!! $errors->first('tarjetapensionatras','is-invalid')!!}"
                           accept="image/jpg">
                    {!! $errors->first('tarjetapensionatras','<div class="invalid-feedback">La foto debera estar en
                        formato jpg.</div>')!!}
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Otros:</label>
                    <input type="file" name="fotootros"
                           class="form-control-file  {!! $errors->first('fotootros','is-invalid')!!}"
                           accept="image/jpg">
                    {!! $errors->first('fotootros','<div class="invalid-feedback">La foto debera estar en
                        formato jpg.</div>')!!}
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <label>&nbsp;</label>
                <button class="btn btn-outline-success btn-block" type="submit" @if($contrato[0]->estatus_estadocontrato != 1
                                    && $contrato[0]->estatus_estadocontrato != 7
                                    && $contrato[0]->estatus_estadocontrato != 9
                                    && $contrato[0]->estatus_estadocontrato != 10) disabled @endif>@lang('mensajes.mensajebotonconfirmacionestado')</button>
            </div>
        </div>
    </form>
    <h2>Información diagnóstico</h2>
    @if($datosDiagnosticoHistorial != null)
        <form id="frmDiagnostico" action="{{route('actualizardiagnosticoconfirmaciones',[$idContrato])}}" method="POST">
            @csrf
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label>Edad</label>
                        <input type="text" name="edad" id="edad" class="form-control"
                               @if($datosDiagnosticoHistorial[0]->edad != null) value="{{$datosDiagnosticoHistorial[0]->edad}}"
                               @else value="{{old('edad')}}" @endif>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Diagnóstico</label>
                        <input type="text" name="diagnostico" id="diagnostico" class="form-control" @if($datosDiagnosticoHistorial[0]->diagnostico != null)
                            value="{{$datosDiagnosticoHistorial[0]->diagnostico}}" @else value="{{old('diagnostico')}}" @endif>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Ocupación</label>
                        <input type="text" name="ocupacion" id="ocupacion" class="form-control"
                               @if($datosDiagnosticoHistorial[0]->ocupacion != null) value="{{$datosDiagnosticoHistorial[0]->ocupacion}}"
                               @else value="{{old('ocupacion')}}" @endif>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Diabetes</label>
                        <input type="text" name="diabetes" id="diabetes" class="form-control"
                               @if($datosDiagnosticoHistorial[0]->diabetes != null) value="{{$datosDiagnosticoHistorial[0]->diabetes}}"
                               @else value="{{old('diabetes')}}" @endif>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Hipertensión</label>
                        <input type="text" name="hipertension" id="hipertension" class="form-control" @if($datosDiagnosticoHistorial[0]->hipertension != null)
                            value="{{$datosDiagnosticoHistorial[0]->hipertension}}" @else value="{{old('hipertension')}}"@endif>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>¿Se encuentra embarazada?</label>
                        <input type="text" name="embarazada" id="embarazada" class="form-control"
                               @if($datosDiagnosticoHistorial[0]->embarazada != null) value="{{$datosDiagnosticoHistorial[0]->embarazada}}"
                               @else value="{{old('embarazada')}}"@endif>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>¿Durmió de 6 a 8 horas?</label>
                        <input type="text" name="durmioseisochohoras" id="durmioseisochohoras" class="form-control" @if($datosDiagnosticoHistorial[0]->durmioseisochohoras != null)
                            value="{{$datosDiagnosticoHistorial[0]->durmioseisochohoras}}" @else value="{{old('durmioseisochohoras')}}"@endif>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>Principal actividad en el día</label>
                        <input type="text" name="actividaddia" id="actividaddia" class="form-control" @if($datosDiagnosticoHistorial[0]->actividaddia != null)
                            value="{{$datosDiagnosticoHistorial[0]->actividaddia}}" @else value = "{{old('actividaddia')}}" @endif>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>Principal problema que padece en sus ojos</label>
                        <input type="text" name="problemasojos" id="problemasojos" class="form-control" @if($datosDiagnosticoHistorial[0]->problemasojos != null)
                            value="{{$datosDiagnosticoHistorial[0]->problemasojos}}" @else value="{{old('problemasojos')}}" @endif>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-10">
                    <h6>Molestia</h6>
                </div>
                <div class="col-2">
                    <h6>Último examen</h6>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input " name="dolor" id="dolor" @if($datosDiagnosticoHistorial[0]->dolor == 1) checked @endif>
                        <label class="custom-control-label" for="dolor">Dolor de cabeza</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input " name="ardor" id="ardor" @if($datosDiagnosticoHistorial[0]->ardor == 1) checked @endif>
                        <label class="custom-control-label" for="ardor">Ardor en los ojos</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="golpeojos" id="golpeojos" @if($datosDiagnosticoHistorial[0]->golpeojos == 1) checked @endif>
                        <label class="custom-control-label" for="golpeojos">Golpe en cabeza</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input " type="checkbox" name="otroM" id="otroM" @if($datosDiagnosticoHistorial[0]->otroM == 1) checked @endif {{old('otroM')}}>
                        <label class="custom-control-label" for="otroM">Otro</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="text" name="molestiaotro" id="molestiaotro" class="form-control {!! $errors->first('molestiaotro','is-invalid')!!}" min="0" placeholder="Otro"
                               @if($datosDiagnosticoHistorial[0]->molestiaotro != null) value="{{$datosDiagnosticoHistorial[0]->molestiaotro}}"
                               @else value="{{old('molestiaotro')}}"@endif>
                        {!! $errors->first('molestiaotro','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <input type="date" name="ultimoexamen" id="ultimoexamen" class="form-control" min="0" placeholder=""
                               @if($datosDiagnosticoHistorial[0]->ultimoexamen != null)
                                   value="{{$datosDiagnosticoHistorial[0]->ultimoexamen}}" @else value="{{old('ultimoexamen')}}"@endif>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <button class="btn btn-outline-success btn-block" form="frmDiagnostico" type="submit" @if($contrato[0]->estatus_estadocontrato != 1
                                        && $contrato[0]->estatus_estadocontrato != 7
                                        && $contrato[0]->estatus_estadocontrato != 9
                                        && $contrato[0]->estatus_estadocontrato != 10) disabled @endif>Actualizar diagnostico
                    </button>
                </div>
            </div>
        </form>
    @else
        <p style="color: #ea9999">Debido a un problema, es necesario solicitar a la asistente/optometrista que guarden la información del diagnóstico e historial y crear de nuevo
            el contrato.</p>
    @endif
    <hr>
    <h4 style="margin-top: 10px">Historiales clinicos</h4>
    <input type="hidden" id="jsonHistoriales" name="jsonHistoriales" value="{{json_encode($arrayHistoriales)}}">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        @if($historialesActivos != null)
            <li class="nav-item">
                <a class="nav-link active" id="historialActivo-tab" data-toggle="tab" href="#historialActivo" role="tab" aria-controls="historialActivo"
                   aria-selected="true">Historial activo (Garantias Asignadas/Creadas)</a>
            </li>
        @endif
        <li class="nav-item">
            <a class="nav-link @if($historialesActivos == null) active @endif" id="historialBase-tab" data-toggle="tab" href="#historialBase" role="tab" aria-controls="historialBase"
               aria-selected="true">Historial base</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="garantiasTerminadas-tab" data-toggle="tab" href="#garantiasTerminadas" role="tab" aria-controls="garantiasTerminadas"
               aria-selected="true">Garantias Terminadas</a>
        </li>
        @if($historialesActivos == null)
            <li class="nav-item">
                <a class="nav-link" id="historialActivo-tab" data-toggle="tab" href="#historialActivo" role="tab" aria-controls="historialActivo"
                   aria-selected="true">Historial activo (Garantias Asignadas/Creadas)</a>
            </li>
        @endif
        <li class="nav-item">
            <a class="nav-link" id="historialCancelados-tab" data-toggle="tab" href="#historialCancelados" role="tab" aria-controls="historialCancelados"
               aria-selected="false">Historial garantia cancelada</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="historialCambioPaquete-tab" data-toggle="tab" href="#historialCambioPaquete" role="tab" aria-controls="historialCambioPaquete"
               aria-selected="false">Historial Cambio de paquete</a>
        </li>
    </ul>

    <div class="tab-content">
        @if($historialesActivos != null)
            <!--Seccion historiales activos-->
            <div class="tab-pane active" id="historialActivo" role="tabpanel" aria-labelledby="historialActivo-tab">
                @if($historialesActivos != null)
                    @php($contador = 1)
                    @foreach($historialesActivos as $historial)
                        <div class="row">
                            <div class="col-2">
                                <h3 style="margin-top: 10px;">
                                    @lang('mensajes.mensajetituloreceta') {{$loop->iteration}} ({{$historial->id}}) |{{ \Carbon\Carbon::parse($historial->created_at)->format('Y-m-d')}}|
                                    @if($historial->garantia != null)
                                        Garantía - {{$historial->optogarantia}}
                                    @else
                                        @if($historial->tipo == 2)
                                            Cambio paquete
                                        @endif
                                    @endif
                                </h3>
                            </div>
                            <div class="col-2">
                                <h3 style="margin-top: 10px;">Modelo: {{$historial->armazon}}</h3>
                            </div>
                            <div class="col-2">
                                <h3 style="margin-top: 10px;">Color: {{$historial->colorarmazon}}</h3>
                            </div>
                            @if($loop->iteration == 1)
                                <div class="col-2">
                                    <h3 style="margin-top: 10px;">Paquete: {{$historial->paquete}}</h3>
                                </div>
                            @endif
                            @if($historial->tipo == 0 && $garantia == null && ($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9))
                                <div class="col-4">
                                    <form id="frmpaquetesconfirmaciones{{$historial->id}}"
                                          action="{{route('actualizarpaquetehistorialconfirmaciones',[$idContrato,$historial->id])}}"
                                          enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                        @csrf
                                        <div class="row">

                                            <div class="col-6">
                                                <label for="">Paquetes</label>
                                                <select
                                                    class="custom-select {!! $errors->first('paquetehistorialeditarconfirmaciones','is-invalid')!!}"
                                                    name="paquetehistorialeditarconfirmaciones{{$historial->id}}">
                                                    @if(count($paquetes) > 0)
                                                        <option selected value=''>Seleccionar</option>
                                                        @foreach($paquetes as $paquete)
                                                            <option
                                                                value="{{$paquete->id}}">
                                                                {{$paquete->nombre}}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option selected>Sin registros</option>
                                                    @endif
                                                </select>
                                                {!! $errors->first('paquetehistorialeditarconfirmaciones','<div class="invalid-feedback">Elegir un paquete , campo obligatorio
                                                </div>')!!}
                                            </div>
                                            <div class="col-6" style="margin-top: 30px">
                                                <div class="form-group">
                                                    <a type="button" href="" class="btn btn-outline-success"
                                                       data-toggle="modal"
                                                       data-target="#modalactualizarpaquetehistorialconfirmaciones{{$historial->id}}">Actualizar
                                                        paquete</a>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="modalactualizarpaquetehistorialconfirmaciones{{$historial->id}}"
                                                 tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                                 aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            Solicitud de confirmación
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    ¿Estas seguro de cambiar el paquete?
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button class="btn btn-primary" type="button"
                                                                    data-dismiss="modal">Cancelar
                                                            </button>
                                                            <button class="btn btn-success" name="btnSubmit" type="submit"
                                                                    form="frmpaquetesconfirmaciones{{$historial->id}}">Aceptar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                        <!--Seccion de armazon-->
                        @if(($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) && $garantia == null)
                            <!--Si es dorado 2 el select de cambiar armazon en todas sus recetas si es garantia solo mostrar en garantia activa-->
                            @if($historial->paquete == 'DORADO 2' || $historial->garantia != null)
                                <form id="frmarmazon{{$historial->id}}" action="{{route('actualizararmazonconfirmaciones',[$idContrato,$historial->id])}}"
                                      enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="row">
                                        <div class="col-4" style="padding-bottom: 25px;">
                                            <label for="">Armazón</label>
                                            <select class="custom-select"
                                                    name="producto">
                                                @if(count($armazones) > 0)
                                                    <option selected value=''>Seleccionar</option>
                                                    @foreach($armazones as $armazon)
                                                        @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 0)
                                                            @if($armazon->id == $historial->id_producto)
                                                                <option selected value="{{$armazon->id}}">
                                                                    {{$armazon->nombre}} | {{$armazon->color}}
                                                                    | {{$armazon->piezas}}pza.
                                                                </option>
                                                            @else
                                                                <option
                                                                    value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                                                    {{$armazon->nombre}} | {{$armazon->color}}
                                                                    | {{$armazon->piezas}}pza.
                                                                </option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-2" style="padding-top: 30px">
                                            <button class="btn btn-outline-success btn-block" name="btnSubmit" form="frmarmazon{{$historial->id}}"
                                                    type="submit">Actualizar armazón
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @else
                                <!--Es diferente de una garantia y el paquete es distinto  dorado 2 - Solo mostrar una vez la opcion de cambio de armazon-->
                                @if($historial->paquete != 'DORADO 2' &&  $historial->garantia == null && $loop->iteration == 1)
                                    <form id="frmarmazon{{$historial->id}}" action="{{route('actualizararmazonconfirmaciones',[$idContrato,$historial->id])}}"
                                          enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                        @csrf
                                        <div class="row">
                                            <div class="col-4" style="padding-bottom: 25px;">
                                                <label for="">Armazón</label>
                                                <select class="custom-select"
                                                        name="producto">
                                                    @if(count($armazones) > 0)
                                                        <option selected value=''>Seleccionar</option>
                                                        @foreach($armazones as $armazon)
                                                            @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 0)
                                                                @if($armazon->id == $historial->id_producto)
                                                                    <option selected value="{{$armazon->id}}">
                                                                        {{$armazon->nombre}} | {{$armazon->color}}
                                                                        | {{$armazon->piezas}}pza.
                                                                    </option>
                                                                @else
                                                                    <option
                                                                        value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                                                        {{$armazon->nombre}} | {{$armazon->color}}
                                                                        | {{$armazon->piezas}}pza.
                                                                    </option>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <option selected>Sin registros</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-2" style="padding-top: 30px;">
                                                <button class="btn btn-outline-success btn-block" name="btnSubmit" form="frmarmazon{{$historial->id}}"
                                                        type="submit">Actualizar armazón
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            @endif
                        @endif
                        <form id="frmActualizarHistorialClinicoConfirmaciones{{$historial->id}}"
                              action="{{route('actualizarhistorialclinicoconfirmaciones',[$idContrato,$historial->id])}}" enctype="multipart/form-data"
                              method="POST" onsubmit="btnSubmit.disabled = true;">
                            @csrf
                            @if($historial->paquete == 'DORADO 2' || $historial->paquete == 'LECTURA')
                                <h5 style="color: #0AA09E;">Sin conversión</h5>
                                @if($historial->hscesfericoder != null)
                                    <div class="Historial">
                                        <div id="mostrarvision"></div>
                                        <h6>Ojo derecho</h6>
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label>Esferico</label>
                                                    <input type="text" name="esfericodsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                                    value="{{$historial->hscesfericoder}}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label>Cilindro</label>
                                                    <input type="text" name="cilindrodsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                                    value="{{$historial->hsccilindroder}}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label>Eje</label>
                                                    <input type="text" name="ejedsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                                    value="{{$historial->hscejeder}}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label>Add</label>
                                                    <input type="text" name="adddsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                                    value="{{$historial->hscaddder}}">
                                                </div>
                                            </div>
                                        </div>
                                        <h6>Ojo Izquierdo</h6>
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label>Esferico</label>
                                                    <input type="text" name="esfericoizqsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                                    value="{{$historial->hscesfericoizq}}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label>Cilindro</label>
                                                    <input type="text" name="cilindroizqsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                                    value="{{$historial->hsccilindroizq}}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label>Eje</label>
                                                    <input type="text" name="ejeizqsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                                    value="{{$historial->hscejeizq}}">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label>Add</label>
                                                    <input type="text" name="addizqsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                                    value="{{$historial->hscaddizq}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <h6 style="color: #0AA09E; margin-left: 30px">Sin capturar</h6>
                                @endif
                                <h5 style="color: #0AA09E;">Con conversión</h5>
                            @endif
                            <div class="Historial">
                                <div id="mostrarvision"></div>
                                <h6>Ojo derecho</h6>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Esferico</label>
                                            <input type="text" name="esfericod{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                            value="{{$historial->esfericoder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Cilindro</label>
                                            <input type="text" name="cilindrod{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                            value="{{$historial->cilindroder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Eje</label>
                                            <input type="text" name="ejed{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif value="{{$historial->ejeder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Add</label>
                                            <input type="text" name="addd{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif value="{{$historial->addder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>ALT</label>
                                            <input type="text" name="altd{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif value="{{$historial->altder}}">
                                        </div>
                                    </div>
                                </div>
                                <h6>Ojo Izquierdo</h6>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Esferico</label>
                                            <input type="text" name="esfericod2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                            value="{{$historial->esfericoizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Cilindro</label>
                                            <input type="text" name="cilindrod2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                            value="{{$historial->cilindroizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Eje</label>
                                            <input type="text" name="ejed2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                            value="{{$historial->ejeizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Add</label>
                                            <input type="text" name="addd2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                            value="{{$historial->addizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>ALT</label>
                                            <input type="text" name="altd2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                            value="{{$historial->altizq}}">
                                        </div>
                                    </div>
                                </div>
                                <h6>Material</h6>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                                   id="material{{$historial->id}}" value="0" @if($historial->material == 0) checked @endif
                                                   @if($actualizarHistorialGarantia) onclick="habilitarCamposMaterial('{{$historial->id}}')" @else onclick="return false;" @endif>
                                            <label class="form-check-label" for="material{{$historial->id}}">Hi Index</label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                                   id="material{{$historial->id}}" value="1" @if($historial->material == 1) checked @endif
                                                   @if($actualizarHistorialGarantia) onclick="habilitarCamposMaterial('{{$historial->id}}')" @else onclick="return false;" @endif>
                                            <label class="form-check-label" for="material{{$historial->id}}">CR</label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}" value="2"
                                                       @if($historial->material == 2) checked @endif
                                                       @if($actualizarHistorialGarantia) onclick="habilitarCamposMaterial('{{$historial->id}}')" @else onclick="return false;" @endif>
                                                <label class="form-check-label" for="material{{$historial->id}}">Policarbonato</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="policarbonato{{$historial->id}}" id="policarbonato{{$historial->id}}" value="1"
                                                       @if($historial->material == 2 && $historial->policarbonatotipo == 1) checked @endif
                                                       @if(!$actualizarHistorialGarantia) onclick="return false;" @else onclick="seleccionarPolicarbonato('#policarbonato{{$historial->id}}','#lbPolicarbonato{{$historial->id}}')" @endif
                                                       @if($historial->material != 2) disabled @endif>
                                                <label class="custom-control-label" for="policarbonato{{$historial->id}}" id="lbPolicarbonato{{$historial->id}}" name="lbPolicarbonato{{$historial->id}}">@if($historial->policarbonatotipo == 0) Adulto @else Niño @endif</label>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                                   id="material{{$historial->id}}" value="3" @if($historial->material == 3) checked @endif
                                                   @if($actualizarHistorialGarantia) onclick="habilitarCamposMaterial('{{$historial->id}}')" @else onclick="return false;" @endif>
                                            <label class="form-check-label" for="material{{$historial->id}}">Otro</label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-check">
                                            <input type="text" name="motro{{$historial->id}}" id="motro{{$historial->id}}" class="form-control" placeholder="OTRO"
                                                   value="{{$historial->materialotro}}" @if($historial->material != 3) disabled @endif>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="form-check">
                                            <input type="text" name="costoMaterial{{$historial->id}}" id="costoMaterial{{$historial->id}}" class="form-control"
                                                   value="${{$historial->costomaterial}}" @if($historial->material != 3) disabled @endif>
                                        </div>
                                    </div>
                                </div>
                                <h6>Tipo de bifocal</h6>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                                   id="bifocal{{$historial->id}}" value="0" @if($historial->bifocal == 0) checked @endif
                                                   @if($actualizarHistorialGarantia) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                            <label class="form-check-label" for="bifocal{{$historial->id}}">
                                                FT
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                                   id="bifocal{{$historial->id}}" value="1" @if($historial->bifocal == 1) checked @endif
                                                   @if($actualizarHistorialGarantia) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                            <label class="form-check-label" for="bifocal{{$historial->id}}">
                                                Blend
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                                   id="bifocal{{$historial->id}}" value="2" @if($historial->bifocal == 2) checked @endif
                                                   @if($actualizarHistorialGarantia) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                            <label class="form-check-label" for="bifocal{{$historial->id}}">
                                                Progresivo
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                                   id="bifocal{{$historial->id}}" value="3" @if($historial->bifocal == 3) checked @endif
                                                   @if($actualizarHistorialGarantia) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                            <label class="form-check-label" for="bifocal{{$historial->id}}">
                                                N/A
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                                   id="bifocal{{$historial->id}}" value="4" @if($historial->bifocal == 4) checked @endif
                                                   @if($actualizarHistorialGarantia) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                            <label class="form-check-label" for="bifocal{{$historial->id}}">
                                                Otro
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="text" name="otroB{{$historial->id}}" id="otroB{{$historial->id}}" class="form-control" min="0" placeholder="OTRO"
                                                   value="{{$historial->bifocalotro}}"  @if($historial->bifocal != 4) disabled @endif>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="text" name="costoBifocal{{$historial->id}}" id="costoBifocal{{$historial->id}}" class="form-control" min="0"
                                                   value="${{$historial->costobifocal}}" @if($historial->bifocal != 4) disabled @endif>
                                        </div>
                                    </div>
                                </div>
                                <h6>Tratamiento</h6>
                                <div class="row">
                                    <div class="col-1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input " name="fotocromatico{{$historial->id}}" id="fotocromatico{{$historial->id}}"
                                                   @if($historial->fotocromatico == 1) checked @endif @if(!$actualizarHistorialGarantia) onclick="return false;" @endif>
                                            <label class="custom-control-label" for="fotocromatico{{$historial->id}}">Fotocromatico</label>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input " name="ar{{$historial->id}}" id="ar{{$historial->id}}"
                                                   @if($historial->ar == 1) checked @endif @if(!$actualizarHistorialGarantia) onclick="return false;" @endif>
                                            <label class="custom-control-label" for="ar{{$historial->id}}">A/R</label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="tinte{{$historial->id}}" id="tinte{{$historial->id}}"
                                                       @if($historial->tinte == 1) checked @endif
                                                       @if(!$actualizarHistorialGarantia) onclick="return false;" @else onclick="[habilitarDeshabilitarColoresTratamiento('#tinte{{$historial->id}}', '#colorTinte{{$historial->id}}'), habilitarDeshabilitarColoresTratamiento('#tinte{{$historial->id}}', '#estiloTinte{{$historial->id}}')]" @endif>
                                                <label class="custom-control-label" for="tinte{{$historial->id}}">Tinte</label>
                                            </div>
                                            <div class="form-group mt-1">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <select name="colorTinte{{$historial->id}}" id="colorTinte{{$historial->id}}" class=" form-control" required>
                                                            <option value="">COLOR</option>
                                                            @foreach($coloresTratamientos as $color)
                                                                @if($color->id_tratamiento == 5)
                                                                    <option value="{{$color->indice}}" @if($color->indice == $historial->id_tratamientocolortinte) selected @endif>{{$color->color}}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <select name="estiloTinte{{$historial->id}}" id="estiloTinte{{$historial->id}}" class=" form-control" required>
                                                            <option value="">ESTILO</option>
                                                            <option value="0" @if($historial->estilotinte == 0) selected @endif>DESVANECIDO</option>
                                                            <option value="1" @if($historial->estilotinte == 1) selected @endif>COMPLETO</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="polarizado{{$historial->id}}" id="polarizado{{$historial->id}}" @if($historial->polarizado == 1) checked @endif
                                                @if(!$actualizarHistorialGarantia) onclick="return false;"  @else onclick="habilitarDeshabilitarColoresTratamiento('#polarizado{{$historial->id}}', '#colorPolarizado{{$historial->id}}')" @endif>
                                                <label class="custom-control-label" for="polarizado{{$historial->id}}">Polarizado</label>
                                            </div>
                                            <div class="form-group mt-1">
                                                <select name="colorPolarizado{{$historial->id}}" id="colorPolarizado{{$historial->id}}" class=" form-control" required>
                                                    <option value="">COLOR</option>
                                                    @foreach($coloresTratamientos as $color)
                                                        @if($color->id_tratamiento == 7)
                                                            <option value="{{$color->indice}}" @if($color->indice == $historial->id_tratamientocolorpolarizado) selected @endif>{{$color->color}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="espejo{{$historial->id}}" id="espejo{{$historial->id}}" @if($historial->espejo == 1) checked @endif
                                                @if(!$actualizarHistorialGarantia) onclick="return false;" @else onclick="habilitarDeshabilitarColoresTratamiento('#espejo{{$historial->id}}', '#colorEspejo{{$historial->id}}')" @endif>
                                                <label class="custom-control-label" for="espejo{{$historial->id}}">Espejo</label>
                                            </div>
                                            <div class="form-group mt-1">
                                                <select name="colorEspejo{{$historial->id}}" id="colorEspejo{{$historial->id}}" class=" form-control" required>
                                                    <option value="">COLOR</option>
                                                    @foreach($coloresTratamientos as $color)
                                                        @if($color->id_tratamiento == 8)
                                                            <option value="{{$color->indice}}" @if($color->indice == $historial->id_tratamientocolorespejo) selected @endif>{{$color->color}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="blueray{{$historial->id}}" id="blueray{{$historial->id}}"
                                                   @if($historial->blueray == 1) checked @endif @if(!$actualizarHistorialGarantia) onclick="return false;" @endif>
                                            <label class="custom-control-label" for="blueray{{$historial->id}}">BlueRay</label>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input " type="checkbox" name="otroTra{{$historial->id}}" id="otroTra{{$historial->id}}"
                                                   @if($historial->otroT == 1) checked @endif
                                                   @if(!$actualizarHistorialGarantia) onclick="return false;" @else onclick="[habilitarDeshabilitarCampoOtroTratamiento('#otroTra{{$historial->id}}', '#otroT{{$historial->id}}'),habilitarDeshabilitarCampoOtroTratamiento('#otroTra{{$historial->id}}', '#costoTratamiento{{$historial->id}}')]" @endif>
                                            <label class="custom-control-label" for="otroTra{{$historial->id}}">Otro</label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="text" name="otroT{{$historial->id}}" id="otroT{{$historial->id}}" class="form-control" min="0" placeholder="OTRO"
                                                   value="{{$historial->tratamientootro}}">
                                            <div class="invalid-feedback" id="errorOtroT{{$historial->id}}"></div>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="text" name="costoTratamiento{{$historial->id}}" id="costoTratamiento{{$historial->id}}" class="form-control" min="0"
                                                   value="${{$historial->costotratamiento}}">
                                            <div class="invalid-feedback" id="errorCostoTratemiento{{$historial->id}}"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($actualizarHistorialGarantia)
                                <button class="btn btn-outline-success btn-block btnActualizarHistorialClinicoConfirmaciones" name="btnSubmit"
                                        type="button" form="frmActualizarHistorialClinicoConfirmaciones{{$historial->id}}"
                                        data-toggle="modal" data-target="#confirmacionActualizarHistorialConfirmaciones" data_parametros_modal="{{$historial->id}}">Actualizar historial
                                </button>
                            @endif
                        </form>
                        <form action="{{route('observacioninternalaboratoriohistorial',[$idContrato,$historial->id,0])}}" method="POST">
                            @csrf
                            <div class="row">
                                <div
                                    class="@if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)col-10 @else col-12 @endif">
                                    <div class="form-group">
                                        <label>Observaciones laboratorio</label>
                                        <textarea name="observacionlaboratorio" class="form-control {!! $errors->first('comentario','is-invalid')!!}" style="text-transform: uppercase"
                                                  rows="4" cols="60"
                                                  @if($contrato[0]->estatus_estadocontrato != 1 && $contrato[0]->estatus_estadocontrato != 9 && $contrato[0]->estatus_estadocontrato != 7) readonly @endif>{{$historial->observaciones}}</textarea>
                                    </div>
                                </div>
                                @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button class="btn btn-outline-success btn-block" type="submit">@lang('mensajes.mensajeobservacioninternaconfirmaciones')</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </form>
                        <form action="{{route('observacioninternalaboratoriohistorial',[$idContrato,$historial->id,1])}}" method="POST">
                            @csrf
                            <div class="row">
                                <div
                                    class="@if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)col-10 @else col-12 @endif">
                                    <div class="form-group">
                                        <label>Observaciones interno</label>
                                        <textarea name="observacioninterna" class="form-control {!! $errors->first('comentario','is-invalid')!!}" style="text-transform: uppercase"
                                                  rows="4" cols="60"
                                                  @if($contrato[0]->estatus_estadocontrato != 1 && $contrato[0]->estatus_estadocontrato != 9 && $contrato[0]->estatus_estadocontrato != 7) readonly @endif>{{$historial->observacionesinterno}}</textarea>
                                    </div>
                                </div>
                                @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button class="btn btn-outline-success btn-block" type="submit">@lang('mensajes.mensajeobservacioninternaconfirmaciones')</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </form>
                        @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9)
                            @if($historial->cancelargarantia != null)
                                <div class="row">
                                    <div class="col-4">
                                        <a class="btn btn-outline-danger btn-block" data-toggle="modal"
                                           data-target="#modalcancelargarantiaconfirmaciones">Cancelar garantia
                                        </a>
                                    </div>
                                </div>

                                <!--Modal para cancelacion de Garantia-->
                                <div class="modal fade" id="modalcancelargarantiaconfirmaciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                     aria-hidden="true">
                                    <form action="{{route('cancelarGarantiaHistorialConfirmaciones', [$idContrato,$historial->id])}}" enctype="multipart/form-data"
                                          method="POST" onsubmit="btnSubmit.disabled = true;">
                                        @csrf
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    Cancelación de garantía.
                                                </div>
                                                <div class="modal-body">
                                                    Explica detalladamente el por que requieres cancelar la garantía del contrato:
                                                    <textarea name="mensaje"
                                                              id="mensaje"
                                                              class="form-control {!! $errors->first('mensaje','is-invalid')!!}" rows="5"
                                                              cols="60" maxlength="1000">
                                                    </textarea>
                                                    {!! $errors->first('mensaje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                                                    <div class="form-group row">
                                                        <label class="col-sm-12 col-form-label" style="color: #ea9999;">El mensaje debe contener como minimo 15 caracteres y un maximo de
                                                            1000.</label>
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
                            @endif
                        @endif
                        <hr>
                    @endforeach
                @else
                    <div class="row">
                        <div class="col-3">
                            <h3 style="margin-top: 10px;">(Sin resultados)</h3>
                        </div>
                    </div>
                    <hr>
                @endif
            </div>
        @endif

        <!--Seccion historiales base-->
        <div class="tab-pane @if($historialesActivos == null) active @endif" id="historialBase" role="tabpanel" aria-labelledby="historialBase-tab">
            @if($historialesBase != null)
                @foreach($historialesBase as $historial)
                    <div class="row">
                        <div class="col-2">
                            <h3 style="margin-top: 10px;">
                                @lang('mensajes.mensajetituloreceta') {{$loop->iteration}} ({{$historial->id}}) |{{ \Carbon\Carbon::parse($historial->created_at)->format('Y-m-d')}}|
                                @if($historial->garantia != null)
                                    Garantía - {{$historial->optogarantia}}
                                @else
                                    @if($historial->tipo == 2)
                                        Cambio paquete
                                    @endif
                                @endif
                            </h3>
                        </div>
                        <div class="col-2">
                            <h3 style="margin-top: 10px;">Modelo: {{$historial->armazon}}</h3>
                        </div>
                        <div class="col-2">
                            <h3 style="margin-top: 10px;">Color: {{$historial->colorarmazon}}</h3>
                        </div>
                        @if($loop->iteration == 1)
                            <div class="col-2">
                                <h3 style="margin-top: 10px;">Paquete: {{$historial->paquete}}</h3>
                            </div>
                        @endif
                        @if($historial->tipo == 0 && $garantia == null && ($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9))
                            <div class="col-4">
                                <form id="frmpaquetesconfirmaciones{{$historial->id}}"
                                      action="{{route('actualizarpaquetehistorialconfirmaciones',[$idContrato,$historial->id])}}"
                                      enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="row">

                                        <div class="col-6">
                                            <label for="">Paquetes</label>
                                            <select
                                                class="custom-select {!! $errors->first('paquetehistorialeditarconfirmaciones','is-invalid')!!}"
                                                name="paquetehistorialeditarconfirmaciones{{$historial->id}}">
                                                @if(count($paquetes) > 0)
                                                    <option selected value=''>Seleccionar</option>
                                                    @foreach($paquetes as $paquete)
                                                        <option
                                                            value="{{$paquete->id}}">
                                                            {{$paquete->nombre}}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                            {!! $errors->first('paquetehistorialeditarconfirmaciones','<div class="invalid-feedback">Elegir un paquete , campo obligatorio
                                            </div>')!!}
                                        </div>
                                        <div class="col-6" style="margin-top: 30px">
                                            <div class="form-group">
                                                <a type="button" href="" class="btn btn-outline-success"
                                                   data-toggle="modal"
                                                   data-target="#modalactualizarpaquetehistorialconfirmaciones{{$historial->id}}">Actualizar
                                                    paquete</a>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="modalactualizarpaquetehistorialconfirmaciones{{$historial->id}}"
                                             tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                             aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        Solicitud de confirmación
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                ¿Estas seguro de cambiar el paquete?
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-primary" type="button"
                                                                data-dismiss="modal">Cancelar
                                                        </button>
                                                        <button class="btn btn-success" name="btnSubmit" type="submit"
                                                                form="frmpaquetesconfirmaciones{{$historial->id}}">Aceptar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                    <!--Seccion de armazon-->
                    @if(($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) && $garantia == null)
                        <!--Si es dorado 2 el select de cambiar armazon en todas sus recetas si es garantia solo mostrar en garantia activa-->
                        @if($historial->paquete == 'DORADO 2' || $historial->garantia != null)
                            <form id="frmarmazon{{$historial->id}}" action="{{route('actualizararmazonconfirmaciones',[$idContrato,$historial->id])}}"
                                  enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                @csrf
                                <div class="row">
                                    <div class="col-4" style="padding-bottom: 25px;">
                                        <label for="">Armazón</label>
                                        <select class="custom-select"
                                                name="producto">
                                            @if(count($armazones) > 0)
                                                <option selected value=''>Seleccionar</option>
                                                @foreach($armazones as $armazon)
                                                    @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 0)
                                                        @if($armazon->id == $historial->id_producto)
                                                            <option selected value="{{$armazon->id}}">
                                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                                | {{$armazon->piezas}}pza.
                                                            </option>
                                                        @else
                                                            <option
                                                                value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                                | {{$armazon->piezas}}pza.
                                                            </option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @else
                                                <option selected>Sin registros</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-2" style="padding-top: 30px">
                                        <button class="btn btn-outline-success btn-block" name="btnSubmit" form="frmarmazon{{$historial->id}}"
                                                type="submit">Actualizar armazón
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <!--Es diferente de una garantia y el paquete es distinto  dorado 2 - Solo mostrar una vez la opcion de cambio de armazon-->
                            @if($historial->paquete != 'DORADO 2' &&  $historial->garantia == null && $loop->iteration == 1)
                                <form id="frmarmazon{{$historial->id}}" action="{{route('actualizararmazonconfirmaciones',[$idContrato,$historial->id])}}"
                                      enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="row">
                                        <div class="col-4" style="padding-bottom: 25px;">
                                            <label for="">Armazón</label>
                                            <select class="custom-select"
                                                    name="producto">
                                                @if(count($armazones) > 0)
                                                    <option selected value=''>Seleccionar</option>
                                                    @foreach($armazones as $armazon)
                                                        @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 0)
                                                            @if($armazon->id == $historial->id_producto)
                                                                <option selected value="{{$armazon->id}}">
                                                                    {{$armazon->nombre}} | {{$armazon->color}}
                                                                    | {{$armazon->piezas}}pza.
                                                                </option>
                                                            @else
                                                                <option
                                                                    value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                                                    {{$armazon->nombre}} | {{$armazon->color}}
                                                                    | {{$armazon->piezas}}pza.
                                                                </option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-2" style="padding-top: 30px;">
                                            <button class="btn btn-outline-success btn-block" name="btnSubmit" form="frmarmazon{{$historial->id}}"
                                                    type="submit">Actualizar armazón
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        @endif
                    @endif
                    @if(($contrato[0]->estatus_estadocontrato <= 1 || $contrato[0]->estatus_estadocontrato == 9) && str_contains(strtoupper($historial->armazon),'PROPIO'))
                        <form id="frmActualizarFotoArmazonConfirmaciones{{$historial->id}}" action="{{route('actualizarfotoarmazonconfirmaciones',[$contrato[0]->id,$historial->id])}}"
                              enctype="multipart/form-data" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-1">
                                    <div class="form-group">
                                        <label>Foto armazon</label>
                                        <div data-toggle="modal" data-target="#imagemodal" id="fotoarmazon1" style="cursor: pointer">
                                            @if(isset($historial->fotoarmazon) && !empty($historial->fotoarmazon) && file_exists($historial->fotoarmazon))
                                                <img src="{{asset($historial->fotoarmazon)}}" style="width:120px;height:120px;" class="img-thumbnail">
                                            @else
                                                <img src="/imagenes/general/administracion/sinfoto.png" style="width:120px;height:120px;" class="img-thumbnail">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="custom-file">
                                        <input type="file" name="fotoArmazon{{$historial->id}}" id="fotoArmazon{{$historial->id}}"
                                               class="custom-file-input {!! $errors->first('fotoArmazon' . $historial->id,'is-invalid')!!}" accept="image/jpg">
                                        <label class="custom-file-label" for="fotoArmazon">Choose file...</label>
                                        {!! $errors->first('fotoArmazon' . $historial->id,'<div class="invalid-feedback">Foto armazón debe ser en formato JPG y de tamaño maximo 1MB.</div>')!!}
                                    </div>
                                </div>
                                <div class="col-2">
                                    <button class="btn btn-outline-success btn-block" name="btnSubmit" form="frmActualizarFotoArmazonConfirmaciones{{$historial->id}}"
                                            type="submit" style="margin-top: 0px;">Actualizar foto armazon
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                    <form id="frmActualizarHistorialClinicoConfirmaciones{{$historial->id}}"
                          action="{{route('actualizarhistorialclinicoconfirmaciones',[$idContrato,$historial->id])}}" enctype="multipart/form-data"
                          method="POST" onsubmit="btnSubmit.disabled = true;">
                        @csrf
                        @if($historial->paquete == 'DORADO 2' || $historial->paquete == 'LECTURA')
                            <h5 style="color: #0AA09E;">Sin conversión</h5>
                            @if($historial->hscesfericoder != null)
                                <div class="Historial">
                                    <div id="mostrarvision"></div>
                                    <h6>Ojo derecho</h6>
                                    <div class="row">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Esferico</label>
                                                <input type="text" name="esfericodsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                                value="{{$historial->hscesfericoder}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Cilindro</label>
                                                <input type="text" name="cilindrodsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                                value="{{$historial->hsccilindroder}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Eje</label>
                                                <input type="text" name="ejedsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                                value="{{$historial->hscejeder}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Add</label>
                                                <input type="text" name="adddsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                                value="{{$historial->hscaddder}}">
                                            </div>
                                        </div>
                                    </div>
                                    <h6>Ojo Izquierdo</h6>
                                    <div class="row">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Esferico</label>
                                                <input type="text" name="esfericoizqsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                                value="{{$historial->hscesfericoizq}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Cilindro</label>
                                                <input type="text" name="cilindroizqsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                                value="{{$historial->hsccilindroizq}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Eje</label>
                                                <input type="text" name="ejeizqsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                                value="{{$historial->hscejeizq}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Add</label>
                                                <input type="text" name="addizqsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                                value="{{$historial->hscaddizq}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <h6 style="color: #0AA09E; margin-left: 30px">Sin capturar</h6>
                            @endif
                            <h5 style="color: #0AA09E;">Con conversión</h5>
                        @endif
                        <div class="Historial">
                            <div id="mostrarvision"></div>
                            <h6>Ojo derecho</h6>
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Esferico</label>
                                        <input type="text" name="esfericod{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                        value="{{$historial->esfericoder}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Cilindro</label>
                                        <input type="text" name="cilindrod{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                        value="{{$historial->cilindroder}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Eje</label>
                                        <input type="text" name="ejed{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif value="{{$historial->ejeder}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Add</label>
                                        <input type="text" name="addd{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif value="{{$historial->addder}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>ALT</label>
                                        <input type="text" name="altd{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif value="{{$historial->altder}}">
                                    </div>
                                </div>
                            </div>
                            <h6>Ojo Izquierdo</h6>
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Esferico</label>
                                        <input type="text" name="esfericod2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                        value="{{$historial->esfericoizq}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Cilindro</label>
                                        <input type="text" name="cilindrod2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                        value="{{$historial->cilindroizq}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Eje</label>
                                        <input type="text" name="ejed2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                        value="{{$historial->ejeizq}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Add</label>
                                        <input type="text" name="addd2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                        value="{{$historial->addizq}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>ALT</label>
                                        <input type="text" name="altd2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                        value="{{$historial->altizq}}">
                                    </div>
                                </div>
                            </div>
                            <h6>Material</h6>
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                               id="material{{$historial->id}}" value="0" @if($historial->material == 0) checked @endif
                                               @if($actualizarHistorialBase) onclick="habilitarCamposMaterial('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="material{{$historial->id}}">Hi Index</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                               id="material{{$historial->id}}" value="1" @if($historial->material == 1) checked @endif
                                               @if($actualizarHistorialBase) onclick="habilitarCamposMaterial('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="material{{$historial->id}}">CR</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}" value="2"
                                                   @if($historial->material == 2) checked @endif
                                                   @if($actualizarHistorialBase) onclick="habilitarCamposMaterial('{{$historial->id}}')" @else onclick="return false;" @endif>
                                            <label class="form-check-label" for="material{{$historial->id}}">Policarbonato</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="policarbonato{{$historial->id}}" id="policarbonato{{$historial->id}}" value="1"
                                                   @if($historial->material == 2 && $historial->policarbonatotipo == 1) checked @endif
                                                   @if(!$actualizarHistorialBase) onclick="return false;" @else onclick="seleccionarPolicarbonato('#policarbonato{{$historial->id}}','#lbPolicarbonato{{$historial->id}}')" @endif
                                                   @if($historial->material != 2) disabled @endif>
                                            <label class="custom-control-label" for="policarbonato{{$historial->id}}" id="lbPolicarbonato{{$historial->id}}" name="lbPolicarbonato{{$historial->id}}">@if($historial->policarbonatotipo == 0) Adulto @else Niño @endif</label>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                               id="material{{$historial->id}}" value="3" @if($historial->material == 3) checked @endif
                                               @if($actualizarHistorialBase) onclick="habilitarCamposMaterial('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="material{{$historial->id}}">Otro</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-check">
                                        <input type="text" name="motro{{$historial->id}}" id="motro{{$historial->id}}" class="form-control" placeholder="OTRO"
                                               value="{{$historial->materialotro}}" @if($historial->material != 3) disabled @endif>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-check">
                                        <input type="text" name="costoMaterial{{$historial->id}}" id="costoMaterial{{$historial->id}}" class="form-control"
                                               value="${{$historial->costomaterial}}" @if($historial->material != 3) disabled @endif>
                                    </div>
                                </div>
                            </div>
                            <h6>Tipo de bifocal</h6>
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                               id="bifocal{{$historial->id}}" value="0" @if($historial->bifocal == 0) checked @endif
                                               @if($actualizarHistorialBase) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="bifocal{{$historial->id}}">
                                            FT
                                        </label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                               id="bifocal{{$historial->id}}" value="1" @if($historial->bifocal == 1) checked @endif
                                               @if($actualizarHistorialBase) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="bifocal{{$historial->id}}">
                                            Blend
                                        </label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                               id="bifocal{{$historial->id}}" value="2" @if($historial->bifocal == 2) checked @endif
                                               @if($actualizarHistorialBase) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="bifocal{{$historial->id}}">
                                            Progresivo
                                        </label>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                               id="bifocal{{$historial->id}}" value="3" @if($historial->bifocal == 3) checked @endif
                                               @if($actualizarHistorialBase) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="bifocal{{$historial->id}}">
                                            N/A
                                        </label>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                               id="bifocal{{$historial->id}}" value="4" @if($historial->bifocal == 4) checked @endif
                                               @if($actualizarHistorialBase) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="bifocal{{$historial->id}}">
                                            Otro
                                        </label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="text" name="otroB{{$historial->id}}" id="otroB{{$historial->id}}" class="form-control" min="0" placeholder="OTRO"
                                               value="{{$historial->bifocalotro}}"  @if($historial->bifocal != 4) disabled @endif>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="text" name="costoBifocal{{$historial->id}}" id="costoBifocal{{$historial->id}}" class="form-control" min="0"
                                               value="${{$historial->costobifocal}}" @if($historial->bifocal != 4) disabled @endif>
                                    </div>
                                </div>
                            </div>
                            <h6>Tratamiento</h6>
                            <div class="row">
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input " name="fotocromatico{{$historial->id}}" id="fotocromatico{{$historial->id}}"
                                               @if($historial->fotocromatico == 1) checked @endif @if(!$actualizarHistorialBase) onclick="return false;" @endif>
                                        <label class="custom-control-label" for="fotocromatico{{$historial->id}}">Fotocromatico</label>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input " name="ar{{$historial->id}}" id="ar{{$historial->id}}"
                                               @if($historial->ar == 1) checked @endif @if(!$actualizarHistorialBase) onclick="return false;" @endif>
                                        <label class="custom-control-label" for="ar{{$historial->id}}">A/R</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="tinte{{$historial->id}}" id="tinte{{$historial->id}}"
                                                   @if($historial->tinte == 1) checked @endif
                                                   @if(!$actualizarHistorialBase) onclick="return false;" @else onclick="[habilitarDeshabilitarColoresTratamiento('#tinte{{$historial->id}}', '#colorTinte{{$historial->id}}'), habilitarDeshabilitarColoresTratamiento('#tinte{{$historial->id}}', '#estiloTinte{{$historial->id}}')]" @endif>
                                            <label class="custom-control-label" for="tinte{{$historial->id}}">Tinte</label>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="row">
                                                <div class="col-6">
                                                    <select name="colorTinte{{$historial->id}}" id="colorTinte{{$historial->id}}" class=" form-control" required>
                                                        <option value="">COLOR</option>
                                                        @foreach($coloresTratamientos as $color)
                                                            @if($color->id_tratamiento == 5)
                                                                <option value="{{$color->indice}}" @if($color->indice == $historial->id_tratamientocolortinte) selected @endif>{{$color->color}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <select name="estiloTinte{{$historial->id}}" id="estiloTinte{{$historial->id}}" class=" form-control" required>
                                                        <option value="">ESTILO</option>
                                                        <option value="0" @if($historial->estilotinte == 0) selected @endif>DESVANECIDO</option>
                                                        <option value="1" @if($historial->estilotinte == 1) selected @endif>COMPLETO</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="polarizado{{$historial->id}}" id="polarizado{{$historial->id}}" @if($historial->polarizado == 1) checked @endif
                                            @if(!$actualizarHistorialBase) onclick="return false;"  @else onclick="habilitarDeshabilitarColoresTratamiento('#polarizado{{$historial->id}}', '#colorPolarizado{{$historial->id}}')" @endif>
                                            <label class="custom-control-label" for="polarizado{{$historial->id}}">Polarizado</label>
                                        </div>
                                        <div class="form-group mt-1">
                                            <select name="colorPolarizado{{$historial->id}}" id="colorPolarizado{{$historial->id}}" class=" form-control" required>
                                                <option value="">COLOR</option>
                                                @foreach($coloresTratamientos as $color)
                                                    @if($color->id_tratamiento == 7)
                                                        <option value="{{$color->indice}}" @if($color->indice == $historial->id_tratamientocolorpolarizado) selected @endif>{{$color->color}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="espejo{{$historial->id}}" id="espejo{{$historial->id}}" @if($historial->espejo == 1) checked @endif
                                            @if(!$actualizarHistorialBase) onclick="return false;" @else onclick="habilitarDeshabilitarColoresTratamiento('#espejo{{$historial->id}}', '#colorEspejo{{$historial->id}}')" @endif>
                                            <label class="custom-control-label" for="espejo{{$historial->id}}">Espejo</label>
                                        </div>
                                        <div class="form-group mt-1">
                                            <select name="colorEspejo{{$historial->id}}" id="colorEspejo{{$historial->id}}" class=" form-control" required>
                                                <option value="">COLOR</option>
                                                @foreach($coloresTratamientos as $color)
                                                    @if($color->id_tratamiento == 8)
                                                        <option value="{{$color->indice}}" @if($color->indice == $historial->id_tratamientocolorespejo) selected @endif>{{$color->color}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="blueray{{$historial->id}}" id="blueray{{$historial->id}}"
                                               @if($historial->blueray == 1) checked @endif @if(!$actualizarHistorialBase) onclick="return false;" @endif>
                                        <label class="custom-control-label" for="blueray{{$historial->id}}">BlueRay</label>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input " type="checkbox" name="otroTra{{$historial->id}}" id="otroTra{{$historial->id}}"
                                               @if($historial->otroT == 1) checked @endif
                                               @if(!$actualizarHistorialBase) onclick="return false;" @else onclick="[habilitarDeshabilitarCampoOtroTratamiento('#otroTra{{$historial->id}}', '#otroT{{$historial->id}}'),habilitarDeshabilitarCampoOtroTratamiento('#otroTra{{$historial->id}}', '#costoTratamiento{{$historial->id}}')]" @endif>
                                        <label class="custom-control-label" for="otroTra{{$historial->id}}">Otro</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="text" name="otroT{{$historial->id}}" id="otroT{{$historial->id}}" class="form-control" min="0" placeholder="OTRO"
                                               value="{{$historial->tratamientootro}}">
                                        <div class="invalid-feedback" id="errorOtroT{{$historial->id}}"></div>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="text" name="costoTratamiento{{$historial->id}}" id="costoTratamiento{{$historial->id}}" class="form-control" min="0"
                                               value="${{$historial->costotratamiento}}">
                                        <div class="invalid-feedback" id="errorCostoTratemiento{{$historial->id}}"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($actualizarHistorialBase)
                            <button class="btn btn-outline-success btn-block mb-1 btnActualizarHistorialClinicoConfirmaciones" name="btnSubmit"
                                    type="button" form="frmActualizarHistorialClinicoConfirmaciones{{$historial->id}}"
                                    data-toggle="modal" data-target="#confirmacionActualizarHistorialConfirmaciones" data_parametros_modal="{{$historial->id}}">Actualizar historial
                            </button>
                        @endif
                    </form>
                    <form action="{{route('observacioninternalaboratoriohistorial',[$idContrato,$historial->id,0])}}" method="POST">
                        @csrf
                        <div class="row">
                            <div
                                class="@if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)col-10 @else col-12 @endif">
                                <div class="form-group">
                                    <label>Observaciones laboratorio</label>
                                    <textarea name="observacionlaboratorio" class="form-control {!! $errors->first('comentario','is-invalid')!!}" style="text-transform: uppercase"
                                              rows="4" cols="60"
                                              @if($contrato[0]->estatus_estadocontrato != 1 && $contrato[0]->estatus_estadocontrato != 9 && $contrato[0]->estatus_estadocontrato != 7) readonly @endif>{{$historial->observaciones}}</textarea>
                                </div>
                            </div>
                            @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button class="btn btn-outline-success btn-block" type="submit">@lang('mensajes.mensajeobservacioninternaconfirmaciones')</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                    <form action="{{route('observacioninternalaboratoriohistorial',[$idContrato,$historial->id,1])}}" method="POST">
                        @csrf
                        <div class="row">
                            <div
                                class="@if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)col-10 @else col-12 @endif">
                                <div class="form-group">
                                    <label>Observaciones interno</label>
                                    <textarea name="observacioninterna" class="form-control {!! $errors->first('comentario','is-invalid')!!}" style="text-transform: uppercase"
                                              rows="4" cols="60"
                                              @if($contrato[0]->estatus_estadocontrato != 1 && $contrato[0]->estatus_estadocontrato != 9 && $contrato[0]->estatus_estadocontrato != 7) readonly @endif>{{$historial->observacionesinterno}}</textarea>
                                </div>
                            </div>
                            @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button class="btn btn-outline-success btn-block" type="submit">@lang('mensajes.mensajeobservacioninternaconfirmaciones')</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                    @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9)
                        @if($historial->cancelargarantia != null)
                            <div class="row">
                                <div class="col-4">
                                    <a class="btn btn-outline-danger btn-block" data-toggle="modal"
                                       data-target="#modalcancelargarantiaconfirmaciones">Cancelar garantia
                                    </a>
                                </div>
                            </div>

                            <!--Modal para cancelacion de Garantia-->
                            <div class="modal fade" id="modalcancelargarantiaconfirmaciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                 aria-hidden="true">
                                <form action="{{route('cancelarGarantiaHistorialConfirmaciones', [$idContrato,$historial->id])}}" enctype="multipart/form-data"
                                      method="POST" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                Cancelación de garantía.
                                            </div>
                                            <div class="modal-body">
                                                Explica detalladamente el por que requieres cancelar la garantía del contrato:
                                                <textarea name="mensaje"
                                                          id="mensaje"
                                                          class="form-control {!! $errors->first('mensaje','is-invalid')!!}" rows="5"
                                                          cols="60" maxlength="1000">
                                                </textarea>
                                                {!! $errors->first('mensaje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label" style="color: #ea9999;">El mensaje debe contener como minimo 15 caracteres y un maximo de
                                                        1000.</label>
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
                        @endif
                    @endif
                    @if(($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) && $banderaEliminarHistorial == true)
                        <div class="row">
                            <div class="col-12">
                                <a href="#" data-href="{{route('eliminarhistorialclinicoconfirmaciones',[$idContrato,$historial->id, $historial->indice])}}"
                                   data-toggle="modal" data-target="#confirm-delete-historialclinico">
                                    <button type="button" class="btn btn-outline-danger disable">Eliminar historial clinico</button>
                                </a>
                            </div>
                        </div>
                    @endif
                    <hr>
                @endforeach
            @else
                <div class="row">
                    <div class="col-3">
                        <h3 style="margin-top: 10px;">(Sin resultados)</h3>
                    </div>
                </div>
                <hr>
            @endif
        </div>
        <!--Seccion historiales garantias terminadas -->
        <div class="tab-pane" id="garantiasTerminadas" role="tabpanel" aria-labelledby="garantiasTerminadas-tab">
            @if($historialesGarantiaTerminada != null)
                @php($contador = 1)
                @foreach($historialesGarantiaTerminada as $historial)
                    <div class="row">
                        <div class="col-2">
                            <h3 style="margin-top: 10px;">
                                @lang('mensajes.mensajetituloreceta') {{$loop->iteration}} ({{$historial->id}}) |{{ \Carbon\Carbon::parse($historial->created_at)->format('Y-m-d')}}|
                                @if($historial->garantia != null)
                                    Garantía - {{$historial->optogarantia}}
                                @else
                                    @if($historial->tipo == 2)
                                        Cambio paquete
                                    @endif
                                @endif
                            </h3>
                        </div>
                        <div class="col-2">
                            <h3 style="margin-top: 10px;">Modelo: {{$historial->armazon}}</h3>
                        </div>
                        <div class="col-2">
                            <h3 style="margin-top: 10px;">Color: {{$historial->colorarmazon}}</h3>
                        </div>
                        @if($loop->iteration == 1)
                            <div class="col-2">
                                <h3 style="margin-top: 10px;">Paquete: {{$historial->paquete}}</h3>
                            </div>
                        @endif
                        @if($historial->tipo == 0 && $garantia == null && ($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9))
                            <div class="col-4">
                                <form id="frmpaquetesconfirmaciones{{$historial->id}}"
                                      action="{{route('actualizarpaquetehistorialconfirmaciones',[$idContrato,$historial->id])}}"
                                      enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="row">

                                        <div class="col-6">
                                            <label for="">Paquetes</label>
                                            <select
                                                class="custom-select {!! $errors->first('paquetehistorialeditarconfirmaciones','is-invalid')!!}"
                                                name="paquetehistorialeditarconfirmaciones{{$historial->id}}">
                                                @if(count($paquetes) > 0)
                                                    <option selected value=''>Seleccionar</option>
                                                    @foreach($paquetes as $paquete)
                                                        <option
                                                            value="{{$paquete->id}}">
                                                            {{$paquete->nombre}}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                            {!! $errors->first('paquetehistorialeditarconfirmaciones','<div class="invalid-feedback">Elegir un paquete , campo obligatorio
                                            </div>')!!}
                                        </div>
                                        <div class="col-6" style="margin-top: 30px">
                                            <div class="form-group">
                                                <a type="button" href="" class="btn btn-outline-success"
                                                   data-toggle="modal"
                                                   data-target="#modalactualizarpaquetehistorialconfirmaciones{{$historial->id}}">Actualizar
                                                    paquete</a>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="modalactualizarpaquetehistorialconfirmaciones{{$historial->id}}"
                                             tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                             aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        Solicitud de confirmación
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                ¿Estas seguro de cambiar el paquete?
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-primary" type="button"
                                                                data-dismiss="modal">Cancelar
                                                        </button>
                                                        <button class="btn btn-success" name="btnSubmit" type="submit"
                                                                form="frmpaquetesconfirmaciones{{$historial->id}}">Aceptar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                    <!--Seccion de armazon-->
                    @if(($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) && $garantia == null)
                        <!--Si es dorado 2 el select de cambiar armazon en todas sus recetas si es garantia solo mostrar en garantia activa-->
                        @if($historial->paquete == 'DORADO 2' || $historial->garantia != null)
                            <form id="frmarmazon{{$historial->id}}" action="{{route('actualizararmazonconfirmaciones',[$idContrato,$historial->id])}}"
                                  enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                @csrf
                                <div class="row">
                                    <div class="col-4" style="padding-bottom: 25px;">
                                        <label for="">Armazón</label>
                                        <select class="custom-select"
                                                name="producto">
                                            @if(count($armazones) > 0)
                                                <option selected value=''>Seleccionar</option>
                                                @foreach($armazones as $armazon)
                                                    @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 0)
                                                        @if($armazon->id == $historial->id_producto)
                                                            <option selected value="{{$armazon->id}}">
                                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                                | {{$armazon->piezas}}pza.
                                                            </option>
                                                        @else
                                                            <option
                                                                value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                                | {{$armazon->piezas}}pza.
                                                            </option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @else
                                                <option selected>Sin registros</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-2" style="padding-top: 30px">
                                        <button class="btn btn-outline-success btn-block" name="btnSubmit" form="frmarmazon{{$historial->id}}"
                                                type="submit">Actualizar armazón
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <!--Es diferente de una garantia y el paquete es distinto  dorado 2 - Solo mostrar una vez la opcion de cambio de armazon-->
                            @if($historial->paquete != 'DORADO 2' &&  $historial->garantia == null && $loop->iteration == 1)
                                <form id="frmarmazon{{$historial->id}}" action="{{route('actualizararmazonconfirmaciones',[$idContrato,$historial->id])}}"
                                      enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="row">
                                        <div class="col-4" style="padding-bottom: 25px;">
                                            <label for="">Armazón</label>
                                            <select class="custom-select"
                                                    name="producto">
                                                @if(count($armazones) > 0)
                                                    <option selected value=''>Seleccionar</option>
                                                    @foreach($armazones as $armazon)
                                                        @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 0)
                                                            @if($armazon->id == $historial->id_producto)
                                                                <option selected value="{{$armazon->id}}">
                                                                    {{$armazon->nombre}} | {{$armazon->color}}
                                                                    | {{$armazon->piezas}}pza.
                                                                </option>
                                                            @else
                                                                <option
                                                                    value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                                                    {{$armazon->nombre}} | {{$armazon->color}}
                                                                    | {{$armazon->piezas}}pza.
                                                                </option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-2" style="padding-top: 30px;">
                                            <button class="btn btn-outline-success btn-block" name="btnSubmit" form="frmarmazon{{$historial->id}}"
                                                    type="submit">Actualizar armazón
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        @endif
                    @endif
                    @if($historial->paquete == 'DORADO 2' || $historial->paquete == 'LECTURA')
                        <h5 style="color: #0AA09E;">Sin conversión</h5>
                        @if($historial->hscesfericoder != null)
                            <div class="Historial">
                                <div id="mostrarvision"></div>
                                <h6>Ojo derecho</h6>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Esferico</label>
                                            <input type="text" name="esfericod" class="form-control" readonly value="{{$historial->hscesfericoder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Cilindro</label>
                                            <input type="text" name="cilindrod" class="form-control" readonly value="{{$historial->hsccilindroder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Eje</label>
                                            <input type="text" name="ejed" class="form-control" readonly value="{{$historial->hscejeder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Add</label>
                                            <input type="text" name="addd" class="form-control" readonly value="{{$historial->hscaddder}}">
                                        </div>
                                    </div>
                                </div>
                                <h6>Ojo Izquierdo</h6>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Esferico</label>
                                            <input type="text" name="esfericod2" class="form-control" readonly value="{{$historial->hscesfericoizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Cilindro</label>
                                            <input type="text" name="cilindrod2" class="form-control" readonly value="{{$historial->hsccilindroizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Eje</label>
                                            <input type="text" name="ejed2" class="form-control" readonly value="{{$historial->hscejeizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Add</label>
                                            <input type="text" name="addd2" class="form-control" readonly value="{{$historial->hscaddizq}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <h6 style="color: #0AA09E; margin-left: 30px">Sin capturar</h6>
                        @endif
                        <h5 style="color: #0AA09E;">Con conversión</h5>
                    @endif
                    <div class="Historial">
                        <div id="mostrarvision"></div>
                        <h6>Ojo derecho</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Esferico</label>
                                    <input type="text" name="esfericod" class="form-control" readonly value="{{$historial->esfericoder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Cilindro</label>
                                    <input type="text" name="cilindrod" class="form-control" readonly value="{{$historial->cilindroder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Eje</label>
                                    <input type="text" name="ejed" class="form-control" readonly value="{{$historial->ejeder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Add</label>
                                    <input type="text" name="addd" class="form-control" readonly value="{{$historial->addder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>ALT</label>
                                    <input type="text" name="altd" class="form-control" readonly value="{{$historial->altder}}">
                                </div>
                            </div>
                        </div>
                        <h6>Ojo Izquierdo</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Esferico</label>
                                    <input type="text" name="esfericod2" class="form-control" readonly value="{{$historial->esfericoizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Cilindro</label>
                                    <input type="text" name="cilindrod2" class="form-control" readonly value="{{$historial->cilindroizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Eje</label>
                                    <input type="text" name="ejed2" class="form-control" readonly value="{{$historial->ejeizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Add</label>
                                    <input type="text" name="addd2" class="form-control" readonly value="{{$historial->addizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>ALT</label>
                                    <input type="text" name="altd2" class="form-control" readonly value="{{$historial->altizq}}">
                                </div>
                            </div>
                        </div>
                        <h6>Material</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}"
                                           @if($historial->material == 0) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">Hi Index</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}"
                                           @if($historial->material == 1) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">CR</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}"
                                               @if($historial->material == 2) checked @endif onclick="return false;">
                                        <label class="form-check-label" for="material{{$historial->id}}">Policarbonato</label>
                                    </div>
                                    @if($historial->material == 2 && ($historial->policarbonatotipo == 0 || $historial->policarbonatotipo == 1))
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="@if($historial->policarbonatotipo == 0) ADULTO @else NIÑO @endif">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}"
                                           @if($historial->material == 3) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">Otro</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input type="text" name="motro" class="form-control" placeholder="Otro" value="{{$historial->materialotro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input type="text" name="costomaterial" class="form-control" value="${{$historial->costomaterial}}" readonly>
                                </div>
                            </div>
                        </div>
                        <h6>Tipo de bifocal</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}" id="exampleRadios{{$historial->id}}"
                                           @if($historial->bifocal == 0) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        FT
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}" id="exampleRadios{{$historial->id}}"
                                           @if($historial->bifocal == 1) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Blend
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}" id="exampleRadios{{$historial->id}}"
                                           @if($historial->bifocal == 2) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Progresivo
                                    </label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}" id="exampleRadios{{$historial->id}}"
                                           @if($historial->bifocal == 3) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        N/A
                                    </label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}" id="exampleRadios{{$historial->id}}"
                                           @if($historial->bifocal == 4) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Otro
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="otroB" class="form-control" min="0" placeholder="Otro" value="{{$historial->bifocalotro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="costoBifocal" class="form-control" min="0" value="${{$historial->costobifocal}}" readonly>
                                </div>
                            </div>
                        </div>
                        <h6>Tratamiento</h6>
                        <div class="row">
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input " name="fotocromatico" id="customCheck9" @if($historial->fotocromatico == 1) checked
                                           @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck9">Fotocromatico</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input " name="ar" id="customCheck10" @if($historial->ar == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck10">A/R</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="tinte" id="customCheck11" @if($historial->tinte == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck11">Tinte</label>
                                    </div>
                                    @if($historial->tinte == 1 && ($historial->colortinte != null && $historial->estilotinte != null))
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colortinte}} | @if($historial->estilotinte == 0) DESVANECIDO @else COMPLETO @endif">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="polarizado" id="customCheck14" @if($historial->polarizado == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck14">Polarizado</label>
                                    </div>
                                    @if($historial->polarizado == 1 && $historial->colorpolarizado != null)
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colorpolarizado}}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="espejo" id="customCheck15" @if($historial->espejo == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck15">Espejo</label>
                                    </div>
                                    @if($historial->espejo == 1 && $historial->colorespejo != null)
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colorespejo}}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="blueray" id="customCheck12" @if($historial->blueray == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck12">BlueRay</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input " type="checkbox" name="otroTra" id="customCheck13" @if($historial->otroT == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck13">Otro</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="otroT" class="form-control" min="0" placeholder="Otro" value="{{$historial->tratamientootro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="costoTratamiento" class="form-control" min="0" value="${{$historial->costotratamiento}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form action="{{route('observacioninternalaboratoriohistorial',[$idContrato,$historial->id,0])}}" method="POST">
                        @csrf
                        <div class="row">
                            <div
                                class="@if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)col-10 @else col-12 @endif">
                                <div class="form-group">
                                    <label>Observaciones laboratorio</label>
                                    <textarea name="observacionlaboratorio" class="form-control {!! $errors->first('comentario','is-invalid')!!}" style="text-transform: uppercase"
                                              rows="4" cols="60"
                                              @if($contrato[0]->estatus_estadocontrato != 1 && $contrato[0]->estatus_estadocontrato != 9 && $contrato[0]->estatus_estadocontrato != 7) readonly @endif>{{$historial->observaciones}}</textarea>
                                </div>
                            </div>
                            @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button class="btn btn-outline-success btn-block" type="submit">@lang('mensajes.mensajeobservacioninternaconfirmaciones')</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                    <form action="{{route('observacioninternalaboratoriohistorial',[$idContrato,$historial->id,1])}}" method="POST">
                        @csrf
                        <div class="row">
                            <div
                                class="@if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)col-10 @else col-12 @endif">
                                <div class="form-group">
                                    <label>Observaciones interno</label>
                                    <textarea name="observacioninterna" class="form-control {!! $errors->first('comentario','is-invalid')!!}" style="text-transform: uppercase"
                                              rows="4" cols="60"
                                              @if($contrato[0]->estatus_estadocontrato != 1 && $contrato[0]->estatus_estadocontrato != 9 && $contrato[0]->estatus_estadocontrato != 7) readonly @endif>{{$historial->observacionesinterno}}</textarea>
                                </div>
                            </div>
                            @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button class="btn btn-outline-success btn-block" type="submit">@lang('mensajes.mensajeobservacioninternaconfirmaciones')</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                    @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9)
                        @if($historial->cancelargarantia != null)
                            <div class="row">
                                <div class="col-4">
                                    <a class="btn btn-outline-danger btn-block" data-toggle="modal"
                                       data-target="#modalcancelargarantiaconfirmaciones">Cancelar garantia
                                    </a>
                                </div>
                            </div>

                            <!--Modal para cancelacion de Garantia-->
                            <div class="modal fade" id="modalcancelargarantiaconfirmaciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                 aria-hidden="true">
                                <form action="{{route('cancelarGarantiaHistorialConfirmaciones', [$idContrato,$historial->id])}}" enctype="multipart/form-data"
                                      method="POST" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                Cancelación de garantía.
                                            </div>
                                            <div class="modal-body">
                                                Explica detalladamente el por que requieres cancelar la garantía del contrato:
                                                <textarea name="mensaje"
                                                          id="mensaje"
                                                          class="form-control {!! $errors->first('mensaje','is-invalid')!!}" rows="5"
                                                          cols="60" maxlength="1000">
                                                    </textarea>
                                                {!! $errors->first('mensaje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label" style="color: #ea9999;">El mensaje debe contener como minimo 15 caracteres y un maximo de
                                                        1000.</label>
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
                        @endif
                    @endif
                    <hr>
                @endforeach
            @else
                <div class="row">
                    <div class="col-3">
                        <h3 style="margin-top: 10px;">(Sin resultados)</h3>
                    </div>
                </div>
                <hr>
            @endif
        </div>
        @if($historialesActivos == null)
            <div class="tab-pane" id="historialActivo" role="tabpanel" aria-labelledby="historialActivo-tab">
                <div class="row">
                    <div class="col-3">
                        <h3 style="margin-top: 10px;">(Sin resultados)</h3>
                    </div>
                </div>
                <hr>
            </div>
        @endif
        <div class="tab-pane" id="historialCancelados" role="tabpanel" aria-labelledby="historialCancelados-tab">
            @if($historialesCancelados != null)
                @php($contador = 1)
                @foreach($historialesCancelados as $historial)
                    <div class="row">
                        <div class="col-2">
                            <h3 style="margin-top: 10px;">
                                @lang('mensajes.mensajetituloreceta') {{$loop->iteration}} ({{$historial->id}}) |{{ \Carbon\Carbon::parse($historial->created_at)->format('Y-m-d')}}|
                                @if($historial->garantia != null)
                                    Garantía - {{$historial->optogarantia}}
                                @else
                                    @if($historial->tipo == 2)
                                        Cambio paquete
                                    @endif
                                @endif
                            </h3>
                        </div>
                        <div class="col-2">
                            <h3 style="margin-top: 10px;">Modelo: {{$historial->armazon}}</h3>
                        </div>
                        <div class="col-2">
                            <h3 style="margin-top: 10px;">Color: {{$historial->colorarmazon}}</h3>
                        </div>
                        @if($loop->iteration == 1)
                            <div class="col-2">
                                <h3 style="margin-top: 10px;">Paquete: {{$historial->paquete}}</h3>
                            </div>
                        @endif
                        @if($historial->tipo == 0 && $garantia == null && ($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9))
                            <div class="col-4">
                                <form id="frmpaquetesconfirmaciones{{$historial->id}}"
                                      action="{{route('actualizarpaquetehistorialconfirmaciones',[$idContrato,$historial->id])}}"
                                      enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="row">

                                        <div class="col-6">
                                            <label for="">Paquetes</label>
                                            <select
                                                class="custom-select {!! $errors->first('paquetehistorialeditarconfirmaciones','is-invalid')!!}"
                                                name="paquetehistorialeditarconfirmaciones{{$historial->id}}">
                                                @if(count($paquetes) > 0)
                                                    <option selected value=''>Seleccionar</option>
                                                    @foreach($paquetes as $paquete)
                                                        <option
                                                            value="{{$paquete->id}}">
                                                            {{$paquete->nombre}}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                            {!! $errors->first('paquetehistorialeditarconfirmaciones','<div class="invalid-feedback">Elegir un paquete , campo obligatorio
                                            </div>')!!}
                                        </div>
                                        <div class="col-6" style="margin-top: 30px">
                                            <div class="form-group">
                                                <a type="button" href="" class="btn btn-outline-success"
                                                   data-toggle="modal"
                                                   data-target="#modalactualizarpaquetehistorialconfirmaciones{{$historial->id}}">Actualizar
                                                    paquete</a>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="modalactualizarpaquetehistorialconfirmaciones{{$historial->id}}"
                                             tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                             aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        Solicitud de confirmación
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                ¿Estas seguro de cambiar el paquete?
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-primary" type="button"
                                                                data-dismiss="modal">Cancelar
                                                        </button>
                                                        <button class="btn btn-success" name="btnSubmit" type="submit"
                                                                form="frmpaquetesconfirmaciones{{$historial->id}}">Aceptar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                    <!--Seccion de armazon-->
                    @if(($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) && $garantia == null)
                        <!--Si es dorado 2 el select de cambiar armazon en todas sus recetas si es garantia solo mostrar en garantia activa-->
                        @if($historial->paquete == 'DORADO 2' || $historial->garantia != null)
                            <form id="frmarmazon{{$historial->id}}" action="{{route('actualizararmazonconfirmaciones',[$idContrato,$historial->id])}}"
                                  enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                @csrf
                                <div class="row">
                                    <div class="col-4" style="padding-bottom: 25px;">
                                        <label for="">Armazón</label>
                                        <select class="custom-select"
                                                name="producto">
                                            @if(count($armazones) > 0)
                                                <option selected value=''>Seleccionar</option>
                                                @foreach($armazones as $armazon)
                                                    @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 0)
                                                        @if($armazon->id == $historial->id_producto)
                                                            <option selected value="{{$armazon->id}}">
                                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                                | {{$armazon->piezas}}pza.
                                                            </option>
                                                        @else
                                                            <option
                                                                value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                                | {{$armazon->piezas}}pza.
                                                            </option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @else
                                                <option selected>Sin registros</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-2" style="padding-top: 30px">
                                        <button class="btn btn-outline-success btn-block" name="btnSubmit" form="frmarmazon{{$historial->id}}"
                                                type="submit">Actualizar armazón
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <!--Es diferente de una garantia y el paquete es distinto  dorado 2 - Solo mostrar una vez la opcion de cambio de armazon-->
                            @if($historial->paquete != 'DORADO 2' &&  $historial->garantia == null && $loop->iteration == 1)
                                <form id="frmarmazon{{$historial->id}}" action="{{route('actualizararmazonconfirmaciones',[$idContrato,$historial->id])}}"
                                      enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="row">
                                        <div class="col-4" style="padding-bottom: 25px;">
                                            <label for="">Armazón</label>
                                            <select class="custom-select"
                                                    name="producto">
                                                @if(count($armazones) > 0)
                                                    <option selected value=''>Seleccionar</option>
                                                    @foreach($armazones as $armazon)
                                                        @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 0)
                                                            @if($armazon->id == $historial->id_producto)
                                                                <option selected value="{{$armazon->id}}">
                                                                    {{$armazon->nombre}} | {{$armazon->color}}
                                                                    | {{$armazon->piezas}}pza.
                                                                </option>
                                                            @else
                                                                <option
                                                                    value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                                                    {{$armazon->nombre}} | {{$armazon->color}}
                                                                    | {{$armazon->piezas}}pza.
                                                                </option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-2" style="padding-top: 30px;">
                                            <button class="btn btn-outline-success btn-block" name="btnSubmit" form="frmarmazon{{$historial->id}}"
                                                    type="submit">Actualizar armazón
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        @endif
                    @endif
                    @if($historial->paquete == 'DORADO 2' || $historial->paquete == 'LECTURA')
                        <h5 style="color: #0AA09E;">Sin conversión</h5>
                        @if($historial->hscesfericoder != null)
                            <div class="Historial">
                                <div id="mostrarvision"></div>
                                <h6>Ojo derecho</h6>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Esferico</label>
                                            <input type="text" name="esfericod" class="form-control" readonly value="{{$historial->hscesfericoder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Cilindro</label>
                                            <input type="text" name="cilindrod" class="form-control" readonly value="{{$historial->hsccilindroder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Eje</label>
                                            <input type="text" name="ejed" class="form-control" readonly value="{{$historial->hscejeder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Add</label>
                                            <input type="text" name="addd" class="form-control" readonly value="{{$historial->hscaddder}}">
                                        </div>
                                    </div>
                                </div>
                                <h6>Ojo Izquierdo</h6>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Esferico</label>
                                            <input type="text" name="esfericod2" class="form-control" readonly value="{{$historial->hscesfericoizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Cilindro</label>
                                            <input type="text" name="cilindrod2" class="form-control" readonly value="{{$historial->hsccilindroizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Eje</label>
                                            <input type="text" name="ejed2" class="form-control" readonly value="{{$historial->hscejeizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Add</label>
                                            <input type="text" name="addd2" class="form-control" readonly value="{{$historial->hscaddizq}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <h6 style="color: #0AA09E; margin-left: 30px">Sin capturar</h6>
                        @endif
                        <h5 style="color: #0AA09E;">Con conversión</h5>
                    @endif
                    <div class="Historial">
                        <div id="mostrarvision"></div>
                        <h6>Ojo derecho</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Esferico</label>
                                    <input type="text" name="esfericod" class="form-control" readonly value="{{$historial->esfericoder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Cilindro</label>
                                    <input type="text" name="cilindrod" class="form-control" readonly value="{{$historial->cilindroder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Eje</label>
                                    <input type="text" name="ejed" class="form-control" readonly value="{{$historial->ejeder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Add</label>
                                    <input type="text" name="addd" class="form-control" readonly value="{{$historial->addder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>ALT</label>
                                    <input type="text" name="altd" class="form-control" readonly value="{{$historial->altder}}">
                                </div>
                            </div>
                        </div>
                        <h6>Ojo Izquierdo</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Esferico</label>
                                    <input type="text" name="esfericod2" class="form-control" readonly value="{{$historial->esfericoizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Cilindro</label>
                                    <input type="text" name="cilindrod2" class="form-control" readonly value="{{$historial->cilindroizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Eje</label>
                                    <input type="text" name="ejed2" class="form-control" readonly value="{{$historial->ejeizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Add</label>
                                    <input type="text" name="addd2" class="form-control" readonly value="{{$historial->addizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>ALT</label>
                                    <input type="text" name="altd2" class="form-control" readonly value="{{$historial->altizq}}">
                                </div>
                            </div>
                        </div>
                        <h6>Material</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}"
                                           @if($historial->material == 0) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">Hi Index</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}"
                                           @if($historial->material == 1) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">CR</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}"
                                               @if($historial->material == 2) checked @endif onclick="return false;">
                                        <label class="form-check-label" for="material{{$historial->id}}">Policarbonato</label>
                                    </div>
                                    @if($historial->material == 2 && ($historial->policarbonatotipo == 0 || $historial->policarbonatotipo == 1))
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="@if($historial->policarbonatotipo == 0) ADULTO @else NIÑO @endif">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}"
                                           @if($historial->material == 3) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">Otro</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input type="text" name="motro" class="form-control" placeholder="Otro" value="{{$historial->materialotro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input type="text" name="costomaterial" class="form-control" value="${{$historial->costomaterial}}" readonly>
                                </div>
                            </div>
                        </div>
                        <h6>Tipo de bifocal</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}" id="exampleRadios{{$historial->id}}"
                                           @if($historial->bifocal == 0) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        FT
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}" id="exampleRadios{{$historial->id}}"
                                           @if($historial->bifocal == 1) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Blend
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}" id="exampleRadios{{$historial->id}}"
                                           @if($historial->bifocal == 2) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Progresivo
                                    </label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}" id="exampleRadios{{$historial->id}}"
                                           @if($historial->bifocal == 3) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        N/A
                                    </label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}" id="exampleRadios{{$historial->id}}"
                                           @if($historial->bifocal == 4) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Otro
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="otroB" class="form-control" min="0" placeholder="Otro" value="{{$historial->bifocalotro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="costoBifocal" class="form-control" min="0" value="${{$historial->costobifocal}}" readonly>
                                </div>
                            </div>
                        </div>
                        <h6>Tratamiento</h6>
                        <div class="row">
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input " name="fotocromatico" id="customCheck9" @if($historial->fotocromatico == 1) checked
                                           @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck9">Fotocromatico</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input " name="ar" id="customCheck10" @if($historial->ar == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck10">A/R</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="tinte" id="customCheck11" @if($historial->tinte == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck11">Tinte</label>
                                    </div>
                                    @if($historial->tinte == 1 && ($historial->colortinte != null && $historial->estilotinte != null))
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colortinte}} | @if($historial->estilotinte == 0) DESVANECIDO @else COMPLETO @endif">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="polarizado" id="customCheck14" @if($historial->polarizado == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck14">Polarizado</label>
                                    </div>
                                    @if($historial->polarizado == 1 && $historial->colorpolarizado != null)
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colorpolarizado}}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="espejo" id="customCheck15" @if($historial->espejo == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck15">Espejo</label>
                                    </div>
                                    @if($historial->espejo == 1 && $historial->colorespejo != null)
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colorespejo}}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="blueray" id="customCheck12" @if($historial->blueray == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck12">BlueRay</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input " type="checkbox" name="otroTra" id="customCheck13" @if($historial->otroT == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck13">Otro</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="otroT" class="form-control" min="0" placeholder="Otro" value="{{$historial->tratamientootro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="costoTratamiento" class="form-control" min="0" value="${{$historial->costotratamiento}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form action="{{route('observacioninternalaboratoriohistorial',[$idContrato,$historial->id,0])}}" method="POST">
                        @csrf
                        <div class="row">
                            <div
                                class="@if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)col-10 @else col-12 @endif">
                                <div class="form-group">
                                    <label>Observaciones laboratorio</label>
                                    <textarea name="observacionlaboratorio" class="form-control {!! $errors->first('comentario','is-invalid')!!}" style="text-transform: uppercase"
                                              rows="4" cols="60"
                                              @if($contrato[0]->estatus_estadocontrato != 1 && $contrato[0]->estatus_estadocontrato != 9 && $contrato[0]->estatus_estadocontrato != 7) readonly @endif>{{$historial->observaciones}}</textarea>
                                </div>
                            </div>
                            @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button class="btn btn-outline-success btn-block" type="submit">@lang('mensajes.mensajeobservacioninternaconfirmaciones')</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                    <form action="{{route('observacioninternalaboratoriohistorial',[$idContrato,$historial->id,1])}}" method="POST">
                        @csrf
                        <div class="row">
                            <div
                                class="@if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)col-10 @else col-12 @endif">
                                <div class="form-group">
                                    <label>Observaciones interno</label>
                                    <textarea name="observacioninterna" class="form-control {!! $errors->first('comentario','is-invalid')!!}" style="text-transform: uppercase"
                                              rows="4" cols="60"
                                              @if($contrato[0]->estatus_estadocontrato != 1 && $contrato[0]->estatus_estadocontrato != 9 && $contrato[0]->estatus_estadocontrato != 7) readonly @endif>{{$historial->observacionesinterno}}</textarea>
                                </div>
                            </div>
                            @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button class="btn btn-outline-success btn-block" type="submit">@lang('mensajes.mensajeobservacioninternaconfirmaciones')</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                    @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9)
                        @if($historial->cancelargarantia != null)
                            <div class="row">
                                <div class="col-4">
                                    <a class="btn btn-outline-danger btn-block" data-toggle="modal"
                                       data-target="#modalcancelargarantiaconfirmaciones">Cancelar garantia
                                    </a>
                                </div>
                            </div>

                            <!--Modal para cancelacion de Garantia-->
                            <div class="modal fade" id="modalcancelargarantiaconfirmaciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                 aria-hidden="true">
                                <form action="{{route('cancelarGarantiaHistorialConfirmaciones', [$idContrato,$historial->id])}}" enctype="multipart/form-data"
                                      method="POST" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                Cancelación de garantía.
                                            </div>
                                            <div class="modal-body">
                                                Explica detalladamente el por que requieres cancelar la garantía del contrato:
                                                <textarea name="mensaje"
                                                          id="mensaje"
                                                          class="form-control {!! $errors->first('mensaje','is-invalid')!!}" rows="5"
                                                          cols="60" maxlength="1000">
                                                </textarea>
                                                {!! $errors->first('mensaje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label" style="color: #ea9999;">El mensaje debe contener como minimo 15 caracteres y un maximo de
                                                        1000.</label>
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
                        @endif
                    @endif
                    <hr>
                @endforeach
            @else
                <div class="row">
                    <div class="col-3">
                        <h3 style="margin-top: 10px;">(Sin resultados)</h3>
                    </div>
                </div>
                <hr>
            @endif
        </div>
        <div class="tab-pane" id="historialCambioPaquete" role="tabpanel" aria-labelledby="historialCambioPaquete-tab">
            @if($historialesCambio != null)
                @foreach($historialesCambio as $historial)
                    <div class="row">
                        <div class="col-2">
                            <h3 style="margin-top: 10px;">
                                @lang('mensajes.mensajetituloreceta') {{$loop->iteration}} ({{$historial->id}}) |{{ \Carbon\Carbon::parse($historial->created_at)->format('Y-m-d')}}|
                                @if($historial->garantia != null)
                                    Garantía - {{$historial->optogarantia}}
                                @else
                                    @if($historial->tipo == 2)
                                        Cambio paquete
                                    @endif
                                @endif
                            </h3>
                        </div>
                        <div class="col-2">
                            <h3 style="margin-top: 10px;">Modelo: {{$historial->armazon}}</h3>
                        </div>
                        <div class="col-2">
                            <h3 style="margin-top: 10px;">Color: {{$historial->colorarmazon}}</h3>
                        </div>
                        @if($loop->iteration == 1)
                            <div class="col-2">
                                <h3 style="margin-top: 10px;">Paquete: {{$historial->paquete}}</h3>
                            </div>
                        @endif
                        @if($historial->tipo == 0 && $garantia == null && ($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9))
                            <div class="col-4">
                                <form id="frmpaquetesconfirmaciones{{$historial->id}}"
                                      action="{{route('actualizarpaquetehistorialconfirmaciones',[$idContrato,$historial->id])}}"
                                      enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="row">

                                        <div class="col-6">
                                            <label for="">Paquetes</label>
                                            <select
                                                class="custom-select {!! $errors->first('paquetehistorialeditarconfirmaciones','is-invalid')!!}"
                                                name="paquetehistorialeditarconfirmaciones{{$historial->id}}">
                                                @if(count($paquetes) > 0)
                                                    <option selected value=''>Seleccionar</option>
                                                    @foreach($paquetes as $paquete)
                                                        <option
                                                            value="{{$paquete->id}}">
                                                            {{$paquete->nombre}}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                            {!! $errors->first('paquetehistorialeditarconfirmaciones','<div class="invalid-feedback">Elegir un paquete , campo obligatorio
                                            </div>')!!}
                                        </div>
                                        <div class="col-6" style="margin-top: 30px">
                                            <div class="form-group">
                                                <a type="button" href="" class="btn btn-outline-success"
                                                   data-toggle="modal"
                                                   data-target="#modalactualizarpaquetehistorialconfirmaciones{{$historial->id}}">Actualizar
                                                    paquete</a>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="modalactualizarpaquetehistorialconfirmaciones{{$historial->id}}"
                                             tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                             aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        Solicitud de confirmación
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                ¿Estas seguro de cambiar el paquete?
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-primary" type="button"
                                                                data-dismiss="modal">Cancelar
                                                        </button>
                                                        <button class="btn btn-success" name="btnSubmit" type="submit"
                                                                form="frmpaquetesconfirmaciones{{$historial->id}}">Aceptar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                    <!--Seccion de armazon-->
                    @if(($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) && $garantia == null)
                        <!--Si es dorado 2 el select de cambiar armazon en todas sus recetas si es garantia solo mostrar en garantia activa-->
                        @if($historial->paquete == 'DORADO 2' || $historial->garantia != null)
                            <form id="frmarmazon{{$historial->id}}" action="{{route('actualizararmazonconfirmaciones',[$idContrato,$historial->id])}}"
                                  enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                @csrf
                                <div class="row">
                                    <div class="col-4" style="padding-bottom: 25px;">
                                        <label for="">Armazón</label>
                                        <select class="custom-select"
                                                name="producto">
                                            @if(count($armazones) > 0)
                                                <option selected value=''>Seleccionar</option>
                                                @foreach($armazones as $armazon)
                                                    @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 0)
                                                        @if($armazon->id == $historial->id_producto)
                                                            <option selected value="{{$armazon->id}}">
                                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                                | {{$armazon->piezas}}pza.
                                                            </option>
                                                        @else
                                                            <option
                                                                value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                                | {{$armazon->piezas}}pza.
                                                            </option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @else
                                                <option selected>Sin registros</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-2" style="padding-top: 30px">
                                        <button class="btn btn-outline-success btn-block" name="btnSubmit" form="frmarmazon{{$historial->id}}"
                                                type="submit">Actualizar armazón
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <!--Es diferente de una garantia y el paquete es distinto  dorado 2 - Solo mostrar una vez la opcion de cambio de armazon-->
                            @if($historial->paquete != 'DORADO 2' &&  $historial->garantia == null && $loop->iteration == 1)
                                <form id="frmarmazon{{$historial->id}}" action="{{route('actualizararmazonconfirmaciones',[$idContrato,$historial->id])}}"
                                      enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="row">
                                        <div class="col-4" style="padding-bottom: 25px;">
                                            <label for="">Armazón</label>
                                            <select class="custom-select"
                                                    name="producto">
                                                @if(count($armazones) > 0)
                                                    <option selected value=''>Seleccionar</option>
                                                    @foreach($armazones as $armazon)
                                                        @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 0)
                                                            @if($armazon->id == $historial->id_producto)
                                                                <option selected value="{{$armazon->id}}">
                                                                    {{$armazon->nombre}} | {{$armazon->color}}
                                                                    | {{$armazon->piezas}}pza.
                                                                </option>
                                                            @else
                                                                <option
                                                                    value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                                                    {{$armazon->nombre}} | {{$armazon->color}}
                                                                    | {{$armazon->piezas}}pza.
                                                                </option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-2" style="padding-top: 30px;">
                                            <button class="btn btn-outline-success btn-block" name="btnSubmit" form="frmarmazon{{$historial->id}}"
                                                    type="submit">Actualizar armazón
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        @endif
                    @endif
                    @if($historial->paquete == 'DORADO 2' || $historial->paquete == 'LECTURA')
                        <h5 style="color: #0AA09E;">Sin conversión</h5>
                        @if($historial->hscesfericoder != null)
                            <div class="Historial">
                                <div id="mostrarvision"></div>
                                <h6>Ojo derecho</h6>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Esferico</label>
                                            <input type="text" name="esfericod" class="form-control" readonly value="{{$historial->hscesfericoder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Cilindro</label>
                                            <input type="text" name="cilindrod" class="form-control" readonly value="{{$historial->hsccilindroder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Eje</label>
                                            <input type="text" name="ejed" class="form-control" readonly value="{{$historial->hscejeder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Add</label>
                                            <input type="text" name="addd" class="form-control" readonly value="{{$historial->hscaddder}}">
                                        </div>
                                    </div>
                                </div>
                                <h6>Ojo Izquierdo</h6>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Esferico</label>
                                            <input type="text" name="esfericod2" class="form-control" readonly value="{{$historial->hscesfericoizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Cilindro</label>
                                            <input type="text" name="cilindrod2" class="form-control" readonly value="{{$historial->hsccilindroizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Eje</label>
                                            <input type="text" name="ejed2" class="form-control" readonly value="{{$historial->hscejeizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Add</label>
                                            <input type="text" name="addd2" class="form-control" readonly value="{{$historial->hscaddizq}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <h6 style="color: #0AA09E; margin-left: 30px">Sin capturar</h6>
                        @endif
                        <h5 style="color: #0AA09E;">Con conversión</h5>
                    @endif
                    <div class="Historial">
                        <div id="mostrarvision"></div>
                        <h6>Ojo derecho</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Esferico</label>
                                    <input type="text" name="esfericod" class="form-control" readonly value="{{$historial->esfericoder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Cilindro</label>
                                    <input type="text" name="cilindrod" class="form-control" readonly value="{{$historial->cilindroder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Eje</label>
                                    <input type="text" name="ejed" class="form-control" readonly value="{{$historial->ejeder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Add</label>
                                    <input type="text" name="addd" class="form-control" readonly value="{{$historial->addder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>ALT</label>
                                    <input type="text" name="altd" class="form-control" readonly value="{{$historial->altder}}">
                                </div>
                            </div>
                        </div>
                        <h6>Ojo Izquierdo</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Esferico</label>
                                    <input type="text" name="esfericod2" class="form-control" readonly value="{{$historial->esfericoizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Cilindro</label>
                                    <input type="text" name="cilindrod2" class="form-control" readonly value="{{$historial->cilindroizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Eje</label>
                                    <input type="text" name="ejed2" class="form-control" readonly value="{{$historial->ejeizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Add</label>
                                    <input type="text" name="addd2" class="form-control" readonly value="{{$historial->addizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>ALT</label>
                                    <input type="text" name="altd2" class="form-control" readonly value="{{$historial->altizq}}">
                                </div>
                            </div>
                        </div>
                        <h6>Material</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}"
                                           @if($historial->material == 0) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">Hi Index</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}"
                                           @if($historial->material == 1) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">CR</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}"
                                               @if($historial->material == 2) checked @endif onclick="return false;">
                                        <label class="form-check-label" for="material{{$historial->id}}">Policarbonato</label>
                                    </div>
                                    @if($historial->material == 2 && ($historial->policarbonatotipo == 0 || $historial->policarbonatotipo == 1))
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="@if($historial->policarbonatotipo == 0) ADULTO @else NIÑO @endif">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}"
                                           @if($historial->material == 3) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">Otro</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input type="text" name="motro" class="form-control" placeholder="Otro" value="{{$historial->materialotro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input type="text" name="costomaterial" class="form-control" value="${{$historial->costomaterial}}" readonly>
                                </div>
                            </div>
                        </div>
                        <h6>Tipo de bifocal</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}" id="exampleRadios{{$historial->id}}"
                                           @if($historial->bifocal == 0) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        FT
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}" id="exampleRadios{{$historial->id}}"
                                           @if($historial->bifocal == 1) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Blend
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}" id="exampleRadios{{$historial->id}}"
                                           @if($historial->bifocal == 2) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Progresivo
                                    </label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}" id="exampleRadios{{$historial->id}}"
                                           @if($historial->bifocal == 3) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        N/A
                                    </label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}" id="exampleRadios{{$historial->id}}"
                                           @if($historial->bifocal == 4) checked @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Otro
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="otroB" class="form-control" min="0" placeholder="Otro" value="{{$historial->bifocalotro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="costoBifocal" class="form-control" min="0" value="${{$historial->costobifocal}}" readonly>
                                </div>
                            </div>
                        </div>
                        <h6>Tratamiento</h6>
                        <div class="row">
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input " name="fotocromatico" id="customCheck9" @if($historial->fotocromatico == 1) checked
                                           @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck9">Fotocromatico</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input " name="ar" id="customCheck10" @if($historial->ar == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck10">A/R</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="tinte" id="customCheck11" @if($historial->tinte == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck11">Tinte</label>
                                    </div>
                                    @if($historial->tinte == 1 && ($historial->colortinte != null && $historial->estilotinte != null))
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colortinte}} | @if($historial->estilotinte == 0) DESVANECIDO @else COMPLETO @endif">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="polarizado" id="customCheck14" @if($historial->polarizado == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck14">Polarizado</label>
                                    </div>
                                    @if($historial->polarizado == 1 && $historial->colorpolarizado != null)
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colorpolarizado}}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="espejo" id="customCheck15" @if($historial->espejo == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck15">Espejo</label>
                                    </div>
                                    @if($historial->espejo == 1 && $historial->colorespejo != null)
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colorespejo}}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="blueray" id="customCheck12" @if($historial->blueray == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck12">BlueRay</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input " type="checkbox" name="otroTra" id="customCheck13" @if($historial->otroT == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck13">Otro</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="otroT" class="form-control" min="0" placeholder="Otro" value="{{$historial->tratamientootro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="costoTratamiento" class="form-control" min="0" value="${{$historial->costotratamiento}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form action="{{route('observacioninternalaboratoriohistorial',[$idContrato,$historial->id,0])}}" method="POST">
                        @csrf
                        <div class="row">
                            <div
                                class="@if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)col-10 @else col-12 @endif">
                                <div class="form-group">
                                    <label>Observaciones laboratorio</label>
                                    <textarea name="observacionlaboratorio" class="form-control {!! $errors->first('comentario','is-invalid')!!}" style="text-transform: uppercase"
                                              rows="4" cols="60"
                                              @if($contrato[0]->estatus_estadocontrato != 1 && $contrato[0]->estatus_estadocontrato != 9 && $contrato[0]->estatus_estadocontrato != 7) readonly @endif>{{$historial->observaciones}}</textarea>
                                </div>
                            </div>
                            @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button class="btn btn-outline-success btn-block" type="submit">@lang('mensajes.mensajeobservacioninternaconfirmaciones')</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                    <form action="{{route('observacioninternalaboratoriohistorial',[$idContrato,$historial->id,1])}}" method="POST">
                        @csrf
                        <div class="row">
                            <div
                                class="@if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)col-10 @else col-12 @endif">
                                <div class="form-group">
                                    <label>Observaciones interno</label>
                                    <textarea name="observacioninterna" class="form-control {!! $errors->first('comentario','is-invalid')!!}" style="text-transform: uppercase"
                                              rows="4" cols="60"
                                              @if($contrato[0]->estatus_estadocontrato != 1 && $contrato[0]->estatus_estadocontrato != 9 && $contrato[0]->estatus_estadocontrato != 7) readonly @endif>{{$historial->observacionesinterno}}</textarea>
                                </div>
                            </div>
                            @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9 || $contrato[0]->estatus_estadocontrato == 7)
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button class="btn btn-outline-success btn-block" type="submit">@lang('mensajes.mensajeobservacioninternaconfirmaciones')</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                    @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9)
                        @if($historial->cancelargarantia != null)
                            <div class="row">
                                <div class="col-4">
                                    <a class="btn btn-outline-danger btn-block" data-toggle="modal"
                                       data-target="#modalcancelargarantiaconfirmaciones">Cancelar garantia
                                    </a>
                                </div>
                            </div>

                            <!--Modal para cancelacion de Garantia-->
                            <div class="modal fade" id="modalcancelargarantiaconfirmaciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                 aria-hidden="true">
                                <form action="{{route('cancelarGarantiaHistorialConfirmaciones', [$idContrato,$historial->id])}}" enctype="multipart/form-data"
                                      method="POST" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                Cancelación de garantía.
                                            </div>
                                            <div class="modal-body">
                                                Explica detalladamente el por que requieres cancelar la garantía del contrato:
                                                <textarea name="mensaje"
                                                          id="mensaje"
                                                          class="form-control {!! $errors->first('mensaje','is-invalid')!!}" rows="5"
                                                          cols="60" maxlength="1000">
                                                </textarea>
                                                {!! $errors->first('mensaje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label" style="color: #ea9999;">El mensaje debe contener como minimo 15 caracteres y un maximo de
                                                        1000.</label>
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
                        @endif
                    @endif
                    <hr>
                @endforeach
            @else
                <div class="row">
                    <div class="col-3">
                        <h3 style="margin-top: 10px;">(Sin resultados)</h3>
                    </div>
                </div>
                <hr>
            @endif
        </div>
    </div>

    @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 9
            || $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11)
        <form action="{{route('comentarioconfirmacion',[$idContrato])}}" method="GET">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label>Comentario</label>
                        <textarea name="comentario" style="max-height: 100px;" class="form-control {!! $errors->first('comentario','is-invalid')!!}"
                                  rows="10" cols="60"></textarea>
                        {!! $errors->first('comentario','<div class="invalid-feedback">campo
                            obligatorio.</div>')!!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button class="btn btn-outline-success btn-block" type="submit">@lang('mensajes.mensajebotonconfirmacionestadocomentario')</button>
                </div>
            </div>
        </form>
    @endif
    <table class="table-bordered table-striped table-sm" style="margin-top: 20px; width: 100%; margin-bottom: 20px;">
        <thead>
        <tr>
            <th style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">USUARIO</th>
            <th style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">COMENTARIO</th>
            <th style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">FECHA</th>
        </tr>
        </thead>
        <tbody>
        @if(sizeof($comentarios) > 0)
            @foreach($comentarios as $comentario)
                <tr>
                    <td align='center' style="width: 20%; font-size: 11px; white-space: nowrap;">{{$comentario->name}}</td>
                    <td align='center' style="width: 60%; font-size: 11px;">{{$comentario->comentario}}</td>
                    <td align='center' style="width: 20%; font-size: 11px; white-space: nowrap;">{{$comentario->fecha}}</td>
                </tr>
            @endforeach
        @else
            <td align='center' style="font-size: 11px; white-space: nowrap;" colspan="3">Sin registros</td>
        @endif
        </tbody>
    </table>
    <h2>Historial de movimientos de confirmaciones</h2>
    <form id="fragregarhistorialmovimientocontratoconfirmaciones"
          action="{{route('agregarhistorialmovimientocontratoconfirmaciones',[$idContrato])}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
        @csrf
        <div class="row">
            <div class="col-10">
                <div class="form-group">
                    <input type="text" name="movimiento" class="form-control {!! $errors->first('movimiento','is-invalid')!!}" placeholder="MOVIMIENTO"
                           maxlength="250">
                    {!! $errors->first('movimiento','<div class="invalid-feedback">Campo movimiento obligatorio.</div>')!!}
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit"
                            style="margin-top: 0px" form="fragregarhistorialmovimientocontratoconfirmaciones">AGREGAR
                    </button>
                </div>
            </div>
        </div>
    </form>
    <table id="tablaHistorialC" class="table table-striped table-general table-sm" style="border: 1px solid #dee2e6; margin-bottom: 5%;">

        <thead>
        <tr>
            <th style=" text-align:center;" scope="col">Usuario</th>
            <th style=" text-align:center;" scope="col">Cambios</th>
            <th style=" text-align:center;" scope="col">Fecha</th>
        </tr>
        </thead>

        <tbody>
        @if(sizeof($historialcontrato) > 0)
            @foreach($historialcontrato as $hc)
                <tr>
                    <td align='center'>{{$hc->name}}</td>
                    <td align='center' style="white-space: normal">{{$hc->cambios}}</td>
                    <td align='center'>{{$hc->created_at}}</td>
                </tr>
            @endforeach
        @else
            <td align='center' colspan="3">Sin registros</td>
        @endif
        </tbody>
    </table>

    {{--  Rechazar contrato y restablecer contrato  --}}
    @if($garantia == null)
        @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9)
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-danger btn-block" data-toggle="modal" data-target="#rechazarContrato">RECHAZAR
                        CONTRATO</a>
                </div>
            </div>
            <!-- modal para Rechazar contratos -->
            <div class="modal fade" id="rechazarContrato" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <form action="{{route('rechazarContratoConfirmaciones',[$idContrato])}}" enctype="multipart/form-data"
                      method="POST" onsubmit="btnSubmit.disabled = true;">
                    @csrf
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                Solicitud de confirmación
                            </div>
                            <div class="modal-body">
                                ¿Deseas rechazar este contrato?
                                <br>
                                <br>
                                Especifique sus razones:
                                <textarea name="comentarios"
                                          class="form-control {!! $errors->first('comentarios','is-invalid')!!}" rows="10"
                                          cols="60"></textarea>
                                {!! $errors->first('comentarios','<div class="invalid-feedback">Campo
                                    obligatorio</div>')!!}
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

        @if($contrato[0]->estatus_estadocontrato == 8)
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-block" data-toggle="modal"
                       data-target="#modalRestablecerContrato">RESTABLECER CONTRATO</a>
                </div>
            </div>
            <!-- modal para restablecer contratos -->
            <div class="modal fade" id="modalRestablecerContrato" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <form action="{{route('restablecercontratoconfirmaciones',[$idContrato])}}"
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
    @endif

    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <img src="" class="imagepreview" style="width: 100%; margin-top: 60px; margin-bottom: 60px; cursor: grabbing">
                </div>
            </div>
        </div>
    </div>

    <!--Modal para Solicitar Autorizacion abono minimo-->
    <div class="modal fade" id="modalsolicitarabonominimo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <form action="{{route('solicitarautorizacionabonominimoconfirmaciones',[$contrato[0]->id])}}" enctype="multipart/form-data"
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

    <!--Modal para aprobacion venta cuando se pase el contrato a estatus APROBADO-->
    <div class="modal fade" id="modalaprobacionventa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-12 col-form-label">Esta venta contara para</label>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-12 col-form-label" style="text-transform: uppercase;"
                               id="optometristamodal">Optometrista: {{$contrato[0]->opto}}</label>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-12 col-form-label" style="text-transform: uppercase;"
                               id="asistentemodal">Asistente: {{$contrato[0]->nombreasistente}}</label>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-12 col-form-label">Ten en cuenta que después de un dia de haber sido aprobado el contrato no se podra revertir</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-success" name="btnAceptar" id="btnAceptar">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal para mostrar lista de colonias registradas por zona-->
    <div class="modal fade" id="modalColoniasConfirmaciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><b>Lista de colonias por zona</b></div>
                <div class="modal-body" style=" max-height: 550px; overflow-y: auto">
                    @if($zonasColonias != null)
                        <div class="row mt-2 mb-2">
                            <div class="col-4">
                                <label style="justify-content: center; font-weight: bold;">Buscar colonia</label>
                            </div>
                            <div class="col-8">
                                <input class="form-control" type="text" id="searchInput" placeholder="BUSCAR...">
                            </div>
                        </div>
                        <div style="max-height: 450px; overflow-y: auto;">
                            @foreach($zonasColonias as $zona)
                                <table class="table-bordered table-striped table-general table-sm search-table" style="text-align: center; margin-bottom: 10px;">
                                    <thead>
                                    <tr>
                                        <th style="text-align:center;" colspan="2"><b>Zona {{$zona->zona}}</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($arregloColonias[$indice] != null)
                                        @foreach($arregloColonias[$indice] as $colonias)
                                            <tr>
                                                <td align='center' style="font-size: 10px;">{{$colonias->localidad}}</td>
                                                <td align='center' style="font-size: 10px;">{{$colonias->colonia}}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td align='center' style="font-size: 10px;" colspan="2">SIN REGISTROS</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                                <input type="hidden" value="{{$indice = $indice + 1}}">
                            @endforeach
                        </div>
                    @else
                        <div><b>Sucursal sin zonas registradas</b></div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal para dar de baja usuario de la sucursal-->
    <div class="modal fade" id="confirmacionActualizarHistorialConfirmaciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Actualizar historial clinico
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            ¿Estas seguro que quieres actualiza historial clinico de este contrato?
                            <hr>
                        </div>
                        <input type="hidden" name="idHistorialFormularioConfirmaciones" id="idHistorialFormularioConfirmaciones" />
                    </div>
                    <br>
                    <div class="row" style="padding-left: 20px;">
                        <div class="col-12" style="color: #dc3545">
                            <b>Al actualizar el historial, debes tener en cuenta las siguientes consideraciones:
                                <br>
                                <lu>
                                    <li>Has verificado los datos que deseas modificar.</li>
                                    <li>Debes actualizar el precio total del contrato mediante una "Solicitud de Cambio de Precio" para aquellas características del producto que incorporen costos adicionales y que no hayan sido registrados en el campo de "Otros".</li>
                                </lu>
                            </b>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-success" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-outline-danger" onclick="generarActualizacionHistorialClinicoConfirmaciones()" id="btnAceptarActualizarHistorialConfirmaciones">Aceptar</button>
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

    <!-- modal para eliminar historial clinico -->
    <div class="modal fade" id="confirm-delete-historialclinico" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Solicitud de confirmacion
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            ¿Estas seguro que quieres eliminar el historial clinico?
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

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
