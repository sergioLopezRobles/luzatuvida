@extends('layouts.app')
@section('titulo','Contratos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>@lang('mensajes.mensajehistoriallaboratorio')</h2>
        <form id="fragregarhistorialmovimientolaboratorio"
              action="{{route('agregarhistorialmovimientolaboratorio',$idContrato)}}"
              enctype="multipart/form-data"
              method="GET" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="row">
                <div class="col-10">
                    <div class="form-group">
                        <input type="text" name="movimiento" class="form-control" placeholder="Movimiento"
                               maxlength="250">
                    </div>
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
        <table id="tablaHistorialC" class="table table-striped table-sm" style="border: 1px solid #dee2e6; margin-bottom: 1%;">

            <thead>
            <tr>
                <th style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">Usuario
                </th>
                <th style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">Cambios
                </th>
                <th style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">Fecha</th>
            </tr>
            </thead>

            <tbody>
            @foreach($historialcontrato as $hc)
                <tr>
                    <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap;">{{$hc->name}}</td>
                    <td align='center' style="text-align:center; font-size: 11px;">{{$hc->cambios}}</td>
                    <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap;">{{$hc->created_at}}</td>
                </tr>
            @endforeach
            @if(sizeof($historialcontrato) == 0)
                <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap;" colspan="3">Sin registros</td>
            @endif
            </tbody>
        </table>
        <hr>
        <div class="row">
            <div class="col-6">
                <h2>Actualizar armazón</h2>
                <h5 style="color: #ea9999">Deberás recibir el armazón anterior y enviar uno nuevo.</h5>
                <h5 style="color: #ea9999">(Se da entrada al armazón anterior y salida al nuevo.)</h5>
                @if(count($historialClinico) > 0)
                    @foreach($historialClinico as $historial)
                        <form id="frmarmazon{{$historial->id}}" action="{{route('actualizararmazonlaboratorio',[$idContrato, $historial->id])}}"
                              enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                            @csrf
                            <div class="row">
                                <div class="col-6" style="padding-bottom: 25px;">
                                    <label for="">Armazón de receta ({{$historial->id}})</label>
                                    <select class="custom-select"
                                            name="armazon{{$historial->id}}">
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
                                                            value="{{$armazon->id}}">
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
                                <div class="col-4" style="padding-top: 30px">
                                    <a type="button" class="btn btn-outline-success btn-block" data-toggle="modal"
                                       data-target="#modalactualizararmazonlaboratorio{{$historial->id}}">Actualizar armazón
                                    </a>
                                </div>

                                <div class="modal fade" id="modalactualizararmazonlaboratorio{{$historial->id}}"
                                     tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                Solicitud de confirmación
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12" style="color: #ea9999">
                                                        Esta accion no se podra revertir ¿Estas seguro de actualizar el armazón?
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-primary" type="button"
                                                        data-dismiss="modal">Cancelar
                                                </button>
                                                <button class="btn btn-success" name="btnSubmit" type="submit"
                                                        form="frmarmazon{{$historial->id}}">Aceptar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    @endforeach
                @endif
            </div>
            <div class="col-6">
                <h2>Agregar armazón</h2>
                <h5 style="color: #ea9999">Deberás enviar el armazón (Solo se le da salida al armazón)</h5>
                <form id="frmproducto" action="{{route('agregarproductoarmazoncontratolaboratorio',[$idContrato])}}"
                      enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                    @csrf
                    <div class="row">
                        <div class="col-6" style="padding-bottom: 25px;">
                            <label for="">Armazón</label>
                            <select class="custom-select"
                                    name="producto">
                                @if(count($armazones) > 0)
                                    <option selected value=''>Seleccionar</option>
                                    @foreach($armazones as $armazon)
                                        @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 0)
                                            <option
                                                value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                | {{$armazon->piezas}}pza.
                                            </option>
                                        @endif
                                    @endforeach
                                @else
                                    <option selected>Sin registros</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-4" style="padding-top: 30px">
                            <a type="button" class="btn btn-outline-success btn-block" data-toggle="modal"
                               data-target="#modalagregarproductoarmazoncontratolaboratorio">Agregar armazón
                            </a>
                        </div>

                        <div class="modal fade" id="modalagregarproductoarmazoncontratolaboratorio"
                             tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        Solicitud de confirmación
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-12" style="color: #ea9999">
                                                Esta accion no se podra revertir ¿Estas seguro de agregar el armazón?
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary" type="button"
                                                data-dismiss="modal">Cancelar
                                        </button>
                                        <button class="btn btn-success" name="btnSubmit" type="submit"
                                                form="frmproducto">Aceptar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
                <table id="tablaHistorialC" class="table table-bordered table-sm" style="margin-bottom: 1%;">
                    <thead>
                    <tr>
                        <th style=" text-align:center;" scope="col">Producto</th>
                        <th style=" text-align:center;" scope="col">Cantidad</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($contratoproductoarmazones as $cp)
                        <tr>
                            <td align='center'>Armazon: {{$cp->nombre}} | {{$cp->color}}</td>
                            <td align='center'>{{$cp->piezas}}</td>
                        </tr>
                    @endforeach
                    @if(sizeof($contratoproductoarmazones) == 0)
                        <td align='center' colspan="2">Sin registros</td>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        @if($garantia != null)
            <div class="row">
                <div class="col-12">
                    <p style="color: #FFFFFF; background-color: #ea9999; font-size: 80px; font-weight: bold; text-align: center">ENVIO EXPRESS</p>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-2">
                <h2>@lang('mensajes.mensajecontrato')</h2>
            </div>
            <div class="col-3">
                <img hidden style="display: block;" id="imgImprimir" src="data:image/png;base64,{{DNS1D::getBarcodePNG($idContrato, 'C39')}}" alt="barcode"
                     onclick="imprimir();"/><br><br>
            </div>
        </div>
        <form action="{{route('estadolaboratorioactualizar',[$idContrato])}}" method="POST" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="row">
                <div class="col-2">
                    <label for="contrato">Contrato</label>
                    <input name="contrato" type="text" readonly class="form-control" placeholder="Contrato" value="{{$contrato[0]->id}}">
                </div>
                <div class="col-2">
                    <label for="contrato">Sucursal</label>
                    <input name="contrato" type="text" readonly class="form-control" placeholder="Contrato" value="{{$contrato[0]->ciudad}}">
                </div>
                <div class="col-2">
                    <label for="contrato">Fecha</label>
                    <input name="contrato" type="text" readonly class="form-control" placeholder="Contrato" value="{{$contrato[0]->fecha}}">
                </div>
                <div class="col-2">
                    <label for="estatusactual">Estatus actual</label>
                    @if($contrato[0]->estatus_estadocontrato == 7)
                        <td align='center'>
                            <button type="button" class="btn btn-primary btn-block aprobado" style="color:#FEFEFE;">{{$contrato[0]->descripcion}}</button>
                        </td>
                    @endif
                    @if($contrato[0]->estatus_estadocontrato == 10)
                        <td align='center'>
                            <button type="button" class="btn btn-warning btn-block manofactura" style="color:#FEFEFE;">{{$contrato[0]->descripcion}}</button>
                        </td>
                    @endif
                    @if($contrato[0]->estatus_estadocontrato == 11)
                        <td align='center'>
                            <button type="button" class="btn btn-info btn-block enprocesodeenvio" style="color:#FEFEFE;">{{$contrato[0]->descripcion}}</button>
                        </td>
                    @endif
                    @if(($contrato[0]->estatus_estadocontrato != 7) && ($contrato[0]->estatus_estadocontrato != 10) && ($contrato[0]->estatus_estadocontrato != 11))
                        <td align='center'>
                            <button type="button" class="btn btn-secondary btn-block" style="color:#FEFEFE;">{{$contrato[0]->descripcion}}</button>
                        </td>
                    @endif
                </div>
                @if($contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11)
                    <div class="col-2">
                        <label for="estatus">Estatus del contrato</label>
                        <select class="custom-select {!! $errors->first('estatus','is-invalid')!!}" name="estatus">
                            <option value="a" selected>Seleccionar</option>
                            @if($contrato[0]->estatus_estadocontrato == 7)
                                <option value="10">Manufactura</option>
                                <option value="11">En proceso de envio</option>
                            @endif
                            @if($contrato[0]->estatus_estadocontrato == 10)
                                <option value="11">En proceso de envio</option>
                                <option value="12">Enviado</option>
                            @endif
                            @if($contrato[0]->estatus_estadocontrato == 11)
                                <option value="12">Enviado</option>
                            @endif
                        </select>
                        {!! $errors->first('estatus','<div class="invalid-feedback">Por favor, selecciona un estatus valido.</div>')!!}
                    </div>
                    <div class="col-2"><label>&nbsp;</label>
                        <button class="btn btn-outline-success" name="btnSubmit" type="submit" style=" margin-top:30px;">@lang('mensajes.mensajebotonconfirmacionestado')
                        </button>
                    </div>
                @endif
            </div>
        </form>
        @php($contador = 1)
        @php($pintarMensajeHistorial = true)
        @foreach($historialClinico as $historial)
            @if(!$banderaHistorialesCorrectos && $pintarMensajeHistorial)
                <p style="color: #ea9999; font-size: 28px; font-weight: bold;">Hay un problema con el historial clinico, coméntale a confirmaciones que lo verifique con soporte tecnico.</p>
                @php($pintarMensajeHistorial = false)
            @endif
            @if((Auth::user()->rol_id == 16 && $banderaHistorialesCorrectos) || Auth::user()->rol_id == 7)
                <div class="row" style="margin-top: 20px;">
                    <div class="col-2">
                        <h3>@lang('mensajes.mensajetituloreceta') {{$loop->iteration}} ({{$historial->id}})</h3>
                        <h5 style="color: #0AA09E;"><br>
                            @if($historial->garantia != null)
                                " Garantía - {{$historial->optogarantia}} "
                            @endif
                            @if($historial->garantia == null) " Optometrista - {{$historial->optocontrato}} " @endif </br> </h5>
                    </div>
                    <div class="col-2">
                        <h3> Modelo: {{$historial->armazon}}</h3>
                    </div>
                    <div class="col-2">
                        <h3>Color: {{$historial->colorarmazon}}</h3>
                    </div>
                    <div class="col-2">
                        <h3>Piezas restantes: {{$historial->piezasr}}</h3>
                    </div>
                    <div class="col-2">
                        <button class="btn btn-outline-success btn-block" id="idhistorial{{$historial->id}}"
                                onclick="imprimir('{{$historial->id}}');">Imprimir ticket
                        </button>
                    </div>
                    <div class="col-2">
                        <button class="btn btn-outline-success btn-block" id="idhistorial{{$historial->id}}"
                                onclick="imprimirEtiqueta();">Imprimir etiqueta
                        </button>
                    </div>
                </div>
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
                            <input class="form-check-input" type="radio" name="material{{$historial->id}}" @if($historial->material == 0) checked @endif onclick="return false;">
                            <label class="form-check-label" for="material{{$historial->id}}">Hi Index</label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="material{{$historial->id}}" @if($historial->material == 1) checked @endif onclick="return false;">
                            <label class="form-check-label" for="material{{$historial->id}}">CR</label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="material{{$loop->iteration}}" id="material{{$loop->iteration}}"
                                       @if($historial->material == 2) checked @endif onclick="return false;">
                                <label class="form-check-label" for="material{{$loop->iteration}}">Policarbonato</label>
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
                            <input class="form-check-input" type="radio" name="material{{$historial->id}}" @if($historial->material == 3) checked @endif onclick="return false;">
                            <label class="form-check-label" for="material{{$historial->id}}">Otro</label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-check">
                            <input type="text" name="motro" class="form-control" placeholder="Otro" value="{{$historial->materialotro}}" readonly>
                        </div>
                    </div>
                </div>
                <h6>Tipo de bifocal</h6>
                <div class="row">
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" @if($historial->bifocal == 0) checked @endif onclick="return false;">
                            <label class="form-check-label" for="exampleRadios1">
                                FT
                            </label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" @if($historial->bifocal == 1) checked @endif onclick="return false;">
                            <label class="form-check-label" for="exampleRadios1">
                                Blend
                            </label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" @if($historial->bifocal == 2) checked @endif onclick="return false;">
                            <label class="form-check-label" for="exampleRadios1">
                                Progresivo
                            </label>
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" @if($historial->bifocal == 3) checked @endif onclick="return false;">
                            <label class="form-check-label" for="exampleRadios1">
                                N/A
                            </label>
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" @if($historial->bifocal == 4) checked @endif onclick="return false;">
                            <label class="form-check-label" for="exampleRadios1">
                                Otro
                            </label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="custom-control custom-checkbox">
                            <input type="text" name="otroB" class="form-control" min="0" placeholder="Otro" value="{{$historial->bifocalotro}}" readonly>
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
                </div>
                <form action="{{route('actualizarobservaciones',[$contrato[0]->id, $historial->id])}}" method="POST" onsubmit="btnSubmit.disabled = true;">
                    @csrf
                    <div class="row">
                        <div @if($contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 10 ||
                         $contrato[0]->estatus_estadocontrato == 11) class="col-10" @else class="col-12" @endif>
                            <div class="form-group">
                                <label>Observaciones</label>
                                <input type="text" name="observaciones" id="observaciones" class="form-control" value="{{$historial->observaciones}}" @if($contrato[0]->estatus_estadocontrato != 7 &&
                        $contrato[0]->estatus_estadocontrato != 10 && $contrato[0]->estatus_estadocontrato != 11) readonly @endif>
                            </div>
                        </div>
                        @if($contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 10 ||
                                 $contrato[0]->estatus_estadocontrato == 11)
                            <div class="col-2">
                                <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit" style="margin-top: 30px;">Actualizar</button>
                            </div>
                        @endif
                    </div>
                </form>
                <hr>
                @if($historial->garantia != null)
                    <div class="row">
                        <div class="col-4">
                            <a class="btn btn-outline-danger btn-block"  data-toggle="modal"
                               data-target="#modalcancelargarantialaboratorio">Cancelar garantia
                            </a>
                        </div>
                    </div>
                @endif
            @endif
        @endforeach
        <form action="{{route('comentariolaboratorio',[$idContrato])}}" method="GET" onsubmit="btnSubmit.disabled = true;">
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
                    <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">@lang('mensajes.mensajebotonconfirmacionestadocomentario')</button>
                </div>
            </div>
        </form>
        <table class="table-bordered table-striped table-general table-sm" style="margin-bottom: 20px;">
            <thead>
            <tr>
                <th style=" text-align:center;" scope="col">USUARIO</th>
                <th style=" text-align:center;" scope="col">COMENTARIO</th>
                <th style=" text-align:center;" scope="col">FECHA</th>
            </tr>
            </thead>
            <tbody>
            @foreach($comentarios as $comentario)
                <tr>
                    <td align='center' style="width: 20%;">{{$comentario->name}}</td>
                    <td align='center' style="width: 60%;">{{$comentario->comentario}}</td>
                    <td align='center' style="width: 20%;">{{$comentario->fecha}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if($garantia == null && ($contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11))
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-danger btn-block" data-toggle="modal" data-target="#rechazarContrato">RECHAZAR
                        CONTRATO</a>
                </div>
            </div>
        @endif
        <br>
        <br>
        <br>

        <!-- modal para Rechazar contratos -->
        <div class="modal fade" id="rechazarContrato" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <form action="{{route('rechazarContratoLaboratorio',[$idContrato])}}" enctype="multipart/form-data"
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
        <img hidden id="logo" src="/imagenes/general/administracion/logo.png" alt="">
    </div>

    <!--Modal para cancelacion de Garantia-->
    <div class="modal fade" id="modalcancelargarantialaboratorio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <form action="{{route('cancelarGarantiaHistorialLaboratorio',[$idContrato, $historial->id])}}" enctype="multipart/form-data"
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

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
<script>
    {{-- Datos para imprimir historial ticket --}}
    var cuidad = "{{$contrato[0]->ciudad}}";
    var zona = "{{$contrato[0]->zona}}";
    var idHistorial1 = "{{$idHistorial1}}";
    var fechaEntregaHistorial1 = "{{$fechaEntregaHistorial1}}";
    var nombreProducto1 = "{{$nombreProducto1}}";
    var colorProducto1 = "{{$colorProducto1}}";
    var observacionesHistorial1 = "{{$observacionesHistorial1}}";
    var esfericoder1 = "{{$esfericoder1}}";
    var cilindroder1 = "{{$cilindroder1}}";
    var ejeder1 = "{{$ejeder1}}";
    var addder1 = "{{$addder1}}";
    var altder1 = "{{$altder1}}";
    var esfericoizq1 = "{{$esfericoizq1}}";
    var cilindroizq1 = "{{$cilindroizq1}}";
    var ejeizq1 = "{{$ejeizq1}}";
    var addizq1 = "{{$addizq1}}";
    var altizq1 = "{{$altizq1}}";
    var material1 = "{{$material1}}";
    var bifocal1 = "{{$bifocal1}}";
    var tratamientos1 = "{{$tratamientos1}}";
    var idHistorial2 = "{{$idHistorial2}}";
    var fechaEntregaHistorial2 = "{{$fechaEntregaHistorial2}}";
    var nombreProducto2 = "{{$nombreProducto2}}";
    var colorProducto2 = "{{$colorProducto2}}";
    var observacionesHistorial2 = "{{$observacionesHistorial2}}";
    var esfericoder2 = "{{$esfericoder2}}";
    var cilindroder2 = "{{$cilindroder2}}";
    var ejeder2 = "{{$ejeder2}}";
    var addder2 = "{{$addder2}}";
    var altder2 = "{{$altder2}}";
    var esfericoizq2 = "{{$esfericoizq2}}";
    var cilindroizq2 = "{{$cilindroizq2}}";
    var ejeizq2 = "{{$ejeizq2}}";
    var addizq2 = "{{$addizq2}}";
    var altizq2 = "{{$altizq2}}";
    var material2 = "{{$material2}}";
    var bifocal2 = "{{$bifocal2}}";
    var tratamientos2 = "{{$tratamientos2}}";
    var idcontrato = "{{$idContrato}}";
    var estadoGarantia = "{{$estadoGarantia}}";

    {{-- Datos para imprimir etiqueta --}}
    var localidadcontrato = "{{$contrato[0]->localidadentrega}}";
    var coloniacontrato = "{{$contrato[0]->coloniaentrega}}";
    var callecontrato = "{{$contrato[0]->calleentrega}}";
    var entrecallescontrato = "{{$contrato[0]->entrecallesentrega}}";
    var numerocontrato = "{{$contrato[0]->numeroentrega}}";
    var departamentocontrato = "{{$contrato[0]->deptoentrega}}";
    var telefonocontrato = "{{$contrato[0]->telefono}}";
    var nombrecontrato = "{{$contrato[0]->nombre}}";
    var comentarioscontrato = "{{$comentariosContrato}}";
</script>

