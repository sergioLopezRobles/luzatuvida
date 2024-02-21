@extends('layouts.app')
@section('titulo','Traspasar'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2>Traspasar contratos de sucursal</h2>

    <form id="frmBuscarContrato" action="{{route('buscarcontratotraspasar',$idFranquicia)}}" enctype="multipart/form-data" method="POST"
          onsubmit="btnSubmit.disabled = true;">
        @csrf
        <div class="row">
            <div class="col-4" style="margin-left: 10px;">
                    <input type="text" name="idContrato" id="idContrato" class="form-control {!! $errors->first('idContrato','is-invalid')!!}"  placeholder="CONTRATO" value="{{$idContrato}}">
                    {!! $errors->first('idContrato','<div class="invalid-feedback">Ingresa un valor para buscar.</div>')!!}
            </div>
            <div class="col-1">
                    <button form="frmBuscarContrato" type="submit" name="btnSubmit" class="btn btn-outline-success">Buscar</button>
            </div>
        </div>
    </form>
    @if($contrato != null || strlen($contrato) > 0)
        <div class="col" style="padding-top: 30px;">
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Nombre Cliente</label>
                        <input type="text" name="nombreCliente" class="form-control" readonly  placeholder="Nombre del cliente" value="{{$contrato[0]->nombreCliente}}">
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Sucursal actual</label>
                        <input type="text" name="sucursalActual" class="form-control" readonly  placeholder="Sucursal actual" value="{{$contrato[0]->sucursalActual}}">
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Telefono</label>
                        <input type="text" name="numeroTelefono" class="form-control" readonly  placeholder="Telefono / Telefono referencia"
                               value="{{$contrato[0]->telefono}} / {{$contrato[0]->telefonoReferencia}}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label>Localidad</label>
                        <input type="text" name="localidad" class="form-control" readonly  placeholder="Localidad" value="{{$contrato[0]->localidad}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Colonia</label>
                        <input type="text" name="colonia" class="form-control" readonly  placeholder="Colonia" value="{{$contrato[0]->colonia}}">
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Calle</label>
                        <input type="text" name="calle" class="form-control" readonly  placeholder="Calle" value="{{$contrato[0]->calle}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Numero</label>
                        <input type="text" name="numero" class="form-control" readonly  placeholder="Numero" value="{{$contrato[0]->numero}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Zona</label>
                        <input type="text" name="zona" class="form-control" readonly  placeholder="Zona" value="{{$contrato[0]->zona}}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Total</label>
                        <input type="text" name="total" class="form-control" readonly  placeholder="Total" value="{{$contrato[0]->total}}">
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Saldo</label>
                        <input type="text" name="saldo" class="form-control" readonly  placeholder="Saldo" value="{{$contrato[0]->saldo}}">
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Ultimo abono</label>
                        @if($contrato[0]->ultimoabono != null)
                            <input type="text" name="ultimoAbono" class="form-control" readonly  placeholder="Ultimo abono" value="{{$contrato[0]->ultimoabono}}">
                        @else
                            <input type="text" name="ultimoAbono" class="form-control" readonly  placeholder="Ultimo abono" value="SIN CAPTURAR">
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label>Promoción</label>
                        @if($contrato[0]->promociones != null)
                            <input type="text" name="promocion" class="form-control" readonly  placeholder="Promocion" value="{{$contrato[0]->promociones}}">
                        @else
                            <input type="text" name="promocion" class="form-control" readonly  placeholder="Promocion" value="SIN PROMOCION">
                        @endif
                    </div>
                </div>
            </div>
            <hr style="background-color: #0AA09E; height: 1px;">

            @if(($contrato[0]->estatus_estadocontrato != 7 && $contrato[0]->estatus_estadocontrato != 10 && $contrato[0]->estatus_estadocontrato != 11
                    && ($garantias == null || $garantias[0]->estadogarantia == 3 || $garantias[0]->estadogarantia == 4))
                    || ($solicitudAutorizacion != null && $solicitudAutorizacion[0]->estatus == 1 && $garantias == null))
                <form id="frmTraspasarContrato" action="{{route('generartraspasocontrato',[$idFranquicia,$idContrato])}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                    @csrf
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label>Sucursal:</label>
                                <select name="sucursalSeleccionada"
                                        id="sucursalSeleccionada"
                                        class="custom-select {!! $errors->first('sucursalSeleccionada','is-invalid')!!}">
                                    @if(count($franquicias) > 0)
                                        <option value="">Seleccionar sucursal</option>
                                        @foreach($franquicias as $franquicia)
                                            <option
                                                value="{{$franquicia->id}}">{{$franquicia->ciudad}}</option>
                                        @endforeach
                                    @else
                                        <option selected>Sin registros</option>
                                    @endif
                                </select>
                                {!! $errors->first('sucursalSeleccionada','<div class="invalid-feedback">Selecciona una sucursal.</div>')!!}
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label>Zona:</label>
                                <select name="zonaSeleccionada"
                                        id="zonaSeleccionada"
                                        class="custom-select {!! $errors->first('zonaSeleccionada','is-invalid')!!}">
                                    <option selected>Sin registros</option>
                                </select>
                                {!! $errors->first('zonaSeleccionada','<div class="invalid-feedback">Selecciona una zona.</div>')!!}
                            </div>
                        </div>
                        @if($contrato[0]->promociones != null)
                            <input type="hidden" value="{{$contrato[0]->promociones}}" id="promocionContrato">
                            <div class="col-4">
                                <div class="form-group">
                                    <label>Promoción:</label>
                                    <select name="promocionSeleccionada"
                                            id="promocionSeleccionada"
                                            class="custom-select {!! $errors->first('promocionSeleccionada','is-invalid')!!}">
                                        <option selected>Sin registros</option>
                                    </select>
                                    {!! $errors->first('promocionSeleccionada','<div class="invalid-feedback">Selecciona una promoción.</div>')!!}
                                </div>
                            </div>
                        @endif
                        <div class="col-2" id="spCargando">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 30px;" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="padding-top: 50px;">
                        <div class="col-12">
                            <button  form="frmTraspasarContrato" type="submit" name="btnSubmit" class="btn btn-outline-success btn-block">Traspasar</button>
                        </div>
                    </div>
                </form>
            @else
                @if($garantias == null)
                    @if($solicitudAutorizacion != null && $solicitudAutorizacion[0]->estatus != 3)
                        @if($solicitudAutorizacion[0]->estatus == 0)
                            <div style="color: #0AA09E; font-weight: bold;"> Solicitud de traspaso pendiente.</div>
                        @endif
                        @if($solicitudAutorizacion[0]->estatus == 2)
                            <div style="margin-bottom: 5px;">
                                <a type="button" href="" class="btn btn-outline-success"
                                   data-toggle="modal"
                                   data-target="#modalsolicitarautorizaciontraspasocontratolaboratorio">Solicitar traspaso contrato</a>
                            </div>
                            <div style="color: #ea9999; font-weight: bold;"> Ultima solicitud de traspaso rechazada.</div>
                        @endif
                    @else
                        <div style="margin-bottom: 5px;">
                            <a type="button" href="" class="btn btn-outline-success"
                               data-toggle="modal"
                               data-target="#modalsolicitarautorizaciontraspasocontratolaboratorio">Solicitar traspaso contrato</a>
                        </div>
                    @endif

                    <!--Modal para Solicitar Autorizacion Traspaso Contrato-->
                    <div class="modal fade" id="modalsolicitarautorizaciontraspasocontratolaboratorio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                         aria-hidden="true">
                        <form action="{{route('solicitarautorizaciontraspasocontratolaboratorio',[$idFranquicia,$idContrato])}}" enctype="multipart/form-data"
                              method="POST" onsubmit="btnSubmit.disabled = true;">
                            @csrf
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        Solicitud para autorización de traspaso.
                                    </div>
                                    <div class="modal-body">Describa la solicitud de traspaso.
                                        <textarea name="mensaje"
                                                  id = "mensaje"
                                                  class="form-control" rows="10"
                                                  cols="60" >
                                                </textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                                        <button class="btn btn-success" name="btnSubmit" type="submit">Aceptar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <div style="color: #ea9999; font-weight: bold;"> No se puede realizar el proceso al contrato en este momento hasta que se entregue el lente o se cancele la garantía.</div>
                @endif
            @endif

        </div>
    @endif
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
