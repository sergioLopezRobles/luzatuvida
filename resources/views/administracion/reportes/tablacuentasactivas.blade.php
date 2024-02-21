@extends('layouts.app')
@section('titulo','Reporte cuentas activas'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2 style="text-align: left; color: #0AA09E">Cuentas activas</h2>
        <input type="hidden" value="{{$idFranquicia}}" id="idFranquiciaActual" name="idFranquiciaActual">
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
                        <div class="row" id="contenedorFiltrosCheckbox">
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="cbProcesoAprobacion" id="cbProcesoAprobacion"
                                           value="1" @if($cbProcesoAprobacion != null) checked @endif>
                                    <label class="custom-control-label" for="cbProcesoAprobacion">En proceso de aprobacion</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="cbAprobados" id="cbAprobados"
                                           value="1" @if($cbAprobados != null) checked @endif>
                                    <label class="custom-control-label" for="cbAprobados">Aprobados</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="cbManofactura" id="cbManofactura"
                                           value="1" @if($cbManofactura != null) checked @endif>
                                    <label class="custom-control-label" for="cbManofactura">Manofactura/En proceso de envio</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="cbEnviados" id="cbEnviados"
                                           value="1" @if($cbEnviados != null) checked @endif>
                                    <label class="custom-control-label" for="cbEnviados">Enviados</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="cbEntregados" id="cbEntregados"
                                           value="1" @if($cbEntregados != null) checked @endif>
                                    <label class="custom-control-label" for="cbEntregados">Entregados</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           name="cbAtrasados" id="cbAtrasados"
                                           value="1" @if($cbAtrasados != null) checked @endif>
                                    <label class="custom-control-label" for="cbAtrasados">Atrasados</label>
                                </div>
                            </div>

                        </div>
                        <div class="row" id="contenedorFiltrosSelect">
                            @if(Auth::user()->rol_id == 7)
                                <div class="col-2">
                                    <label for="franquiciaSeleccionada">Sucursal</label>
                                    <div class="form-group">
                                        <select name="franquiciaSeleccionada"
                                                class="form-control"
                                                id="franquiciaSeleccionada">
                                            @if(count($franquicias) > 0)
                                                <option value="" selected>Todas las sucursales</option>
                                                @foreach($franquicias as $franquicia)
                                                    <option
                                                        value="{{$franquicia->id}}"
                                                        {{ isset($franquiciaSeleccionada) ? ($franquiciaSeleccionada == $franquicia->id ? 'selected' : '' ) : '' }}>{{$franquicia->ciudad}}
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
                                <label for="zonaSeleccionada">Zonas</label>
                                <select class="custom-select {!! $errors->first('zonaSeleccionada','is-invalid')!!}" name="zonaSeleccionada" id="zonaSeleccionada">
                                    <option value="" selected>Todas las zonas</option>
                                    <option value="1" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 1 ? 'selected' : '' ) : '' }}>1</option>
                                    <option value="2" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 2 ? 'selected' : '' ) : '' }}>2</option>
                                    <option value="3" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 3 ? 'selected' : '' ) : '' }}>3</option>
                                    <option value="4" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 4 ? 'selected' : '' ) : '' }}>4</option>
                                    <option value="5" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 5 ? 'selected' : '' ) : '' }}>5</option>
                                    <option value="6" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 6 ? 'selected' : '' ) : '' }}>6</option>
                                    <option value="7" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 7 ? 'selected' : '' ) : '' }}>7</option>
                                    <option value="8" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 8 ? 'selected' : '' ) : '' }}>8</option>
                                    <option value="Oficina" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 'Oficina' ? 'selected' : '' ) : '' }}>Oficina</option>
                                </select>
                                {!! $errors->first('zonaSeleccionada','<div class="invalid-feedback">Por favor, selecciona la zona.</div>')!!}
                            </div>
                            <div class="col-2">
                                <label for="formaPagoSeleccionada">Forma de pago</label>
                                <select class="custom-select {!! $errors->first('formaPagoSeleccionada','is-invalid')!!}" name="formaPagoSeleccionada" id="formaPagoSeleccionada">
                                    <option value="" selected>Todas las formas de pago</option>
                                    <option value="0" {{ isset($formaPagoSeleccionada) ? ($formaPagoSeleccionada == 0 ? 'selected' : '' ) : '' }}>Contado</option>
                                    <option value="1" {{ isset($formaPagoSeleccionada) ? ($formaPagoSeleccionada == 1 ? 'selected' : '' ) : '' }}>Semanal</option>
                                    <option value="2" {{ isset($formaPagoSeleccionada) ? ($formaPagoSeleccionada == 2 ? 'selected' : '' ) : '' }}>Quincenal</option>
                                    <option value="4" {{ isset($formaPagoSeleccionada) ? ($formaPagoSeleccionada == 4 ? 'selected' : '' ) : '' }}>Mensual</option>
                                </select>
                                {!! $errors->first('formaPagoSeleccionada','<div class="invalid-feedback">Por favor, selecciona la forma de pago.</div>')!!}
                            </div>
                                <div class="col-2" style="margin-top: 30px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="cbUltimoAbono" id="cbUltimoAbono"
                                               value="">
                                        <label class="custom-control-label" for="cbUltimoAbono">Filtrar por ultimo abono</label>
                                    </div>
                                </div>
                                <div class="col-2" style="margin-top: 30px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="cbPeriodoActual" id="cbPeriodoActual"
                                               value="">
                                        <label class="custom-control-label" for="cbPeriodoActual">Contratos periodo actual</label>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <button class="btn btn-outline-success btn-block" id="btnFiltrarCuentas" onclick="cargarListaCuentasActivas()">Filtrar</button>
                                </div>
                                <div class="col-1" id="spCargando">
                                    <div class="d-flex justify-content-center">
                                        <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 30px;" role="status">
                                            <span class="visually-hidden"></span>
                                        </div>
                                    </div>
                                </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group" id="listacuentasactivas">

    </div>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
