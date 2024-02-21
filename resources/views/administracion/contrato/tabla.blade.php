@extends('layouts.app')
@section('titulo','Contratos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>Contratos de la sucursal</h2>
        <form action="{{route('filtrarlistacontrato',$idFranquicia)}}" enctype="multipart/form-data" method="POST"
              onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="row">
                <div class="col-4">
                    <input name="filtro" type="text" class="form-control" placeholder="Buscar.." value="{{ $filtro ?? '' }}">
                </div>
                <div class="col-5">
                    <div>
                        <button type="submit" name="btnSubmit" class="btn btn-outline-success">Buscar</button>
                    </div>
                    <div style="margin-top: 10px;">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="swBusquedaFiltro" name="swBusquedaFiltro" value="1"
                                   @if($swBusquedaFiltro == 1) checked @endif>
                            <label class="custom-control-label" for="swBusquedaFiltro" id="labelSWBusquedaFiltro">@if($swBusquedaFiltro == 1) Busqueda avanzada. @else Busqueda rapida. @endif</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        @if(Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)
            <button type="button" class="btn btn-primary" style="margin-top:10px; margin-bottom: 10px;">
                Total registros <span class="badge bg-secondary" id="totalContratos">{{$totalRegistros}}</span>
            </button>
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
                            <form action="{{route('filtrarlistacontratocheckbox',$idFranquicia)}}"
                                  enctype="multipart/form-data" method="POST"
                                  onsubmit="btnSubmit.disabled = true;">
                                @csrf
                                <div class="row" id="divFiltrosContratos">
                                    <div class="col-1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="cbGarantias" id="customCheck1"
                                                   value="1" @if($cbGarantias != null) checked @endif>
                                            <label class="custom-control-label" for="customCheck1">Garantias</label>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="cbSupervision" id="customCheck2"
                                                   value="1" @if($cbSupervision != null) checked @endif>
                                            <label class="custom-control-label" for="customCheck2">Supervision</label>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="cbAtrasado" id="customCheck3"
                                                   value="1" @if($cbAtrasado != null) checked @endif>
                                            <label class="custom-control-label" for="customCheck3">Atrasados</label>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="cbEntrega" id="customCheck4"
                                                   value="1" @if($cbEntrega != null) checked @endif>
                                            <label class="custom-control-label" for="customCheck4">Entrega</label>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="cbLaboratorio" id="customCheck5"
                                                   value="1" @if($cbLaboratorio != null) checked @endif>
                                            <label class="custom-control-label" for="customCheck5">Laboratorio</label>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="cbConfirmacion" id="customCheck6"
                                                   value="1" @if($cbConfirmacion != null) checked @endif>
                                            <label class="custom-control-label" for="customCheck6">Confirmación</label>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="cbTodos" id="customCheck7"
                                                   value="1" @if($cbTodos != null) checked @endif>
                                            <label class="custom-control-label" for="customCheck7">Todos</label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <select name="zonaU"
                                                    id="zonaU"
                                                    class="form-control">
                                                @if(count($zonas) > 0)
                                                    <option value="">Todas las zonas</option>
                                                    @foreach($zonas as $zona)
                                                        <option
                                                            value="{{$zona->id}}" {{ isset($zonaU) ? ($zonaU == $zona->id ? 'selected' : '' ) : '' }}>{{$zona->zona}}</option>
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div>
                                            <button class="btn btn-outline-success" name="btnSubmit"
                                                    type="submit">Aplicar
                                            </button>
                                        </div>
                                        <div style="margin-top: 10px;">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="swBusquedaAvanzada" name="swBusquedaAvanzada" value="1"
                                                       @if($swBusquedaAvanzada == 1) checked @endif>
                                                <label class="custom-control-label" for="swBusquedaAvanzada" id="labelSWBusquedaCheckBox">@if($swBusquedaAvanzada == 1) Busqueda avanzada. @else Busqueda rapida. @endif</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>Fecha inicial</label>
                                            <input type="date" name="fechainibuscar" class="form-control {!! $errors->first('fechainibuscar','is-invalid')!!}"
                                                   @isset($fechainibuscar) value = "{{$fechainibuscar}}" @endisset>
                                            @if($errors->has('fechainibuscar'))
                                                <div class="invalid-feedback">{{$errors->first('fechainibuscar')}}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>Fecha final</label>
                                            <input type="date" name="fechafinbuscar" class="form-control {!! $errors->first('fechafinbuscar','is-invalid')!!}"
                                                   @isset($fechafinbuscar) value = "{{$fechafinbuscar}}" @endisset>
                                            @if($errors->has('fechafinbuscar'))
                                                <div class="invalid-feedback">{{$errors->first('fechafinbuscar')}}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label style="font-size: 17px; margin-top: 35px; color: #ea9999; font-weight: bold">Seleccionar mas de 3 filtros o busqueda avanzada puede demorar varios
                                            minutos</label>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="contenedortblContratos" style="max-height: 600px; overflow-y: auto; width: 100%; overflow-x: auto; margin-top: 20px;">
            <table id="tablaContratos" class="table table-bordered table-contratos table-sm">
                    <thead>
                    <tr>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">VER
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">EDITAR
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col"> CONTRATO
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col"> ESTATUS
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col"> RELACIÓN
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col"> FECHA CREACIÓN
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col"> FECHA ENTREGA
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col"> FECHA GARANTIA
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col"> ASISTENTE
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">ZONA
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col"> LOCALIDAD
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col"> COLONIA
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col"> CALLE
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col"> NUMERO
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col"> CLIENTE
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">
                            TELEFONO
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">TOTAL
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">TOTAL PRODUCTOS
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">TOTAL PROMOCIÓN
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">ABONO
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">SALDO
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">ULTIMO
                            ABONO
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">PROMOCION
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">ATRASO
                        </th>
                    </tr>
                    </thead>
                <tbody>
                @if($cbGarantias == 1)
                    <tr>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; @if(Auth::user()->rol_id == 4) top: 20px; @else top: 42px;
                            @endif box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="24">GARANTIAS (REPORTADAS/ASIGNADAS)
                        </th>
                    </tr>
                    @if($contratosreportesgarantia != null)
                        @foreach($contratosreportesgarantia as $contrato)
                            <tr>
                                <td align='center' style="font-size: 10px;"><a href="{{route('vercontrato',[$idFranquicia,$contrato->id])}}" target="_blank">
                                        <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i
                                                class="fas fa-book-open"></i></button>
                                    </a></td>
                                <td align='center' style="font-size: 10px;"><a
                                        href="{{route('contratoactualizar',[$idFranquicia,$contrato->id])}}" target="_blank">
                                        <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i class="fas fa-pen"></i>
                                        </button>
                                    </a></td>
                                <td align='center' @if($contrato->estadogarantia == 0) style="font-size: 10px; background-color:#ea9999;"
                                    @else style="font-size: 10px; background-color:#5bc0de;" @endif><p style="color:#FFFFFF">{{$contrato->id}}</p></td>
                                @switch($contrato->estatus_estadocontrato)
                                    @case(2)
                                        <td align='center' style="font-size: 10px;">
                                            <button type="button" class="btn btn-primary entregados"
                                                    style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                        </td>
                                        @break
                                    @case(5)
                                        <td align='center' style="font-size: 10px;">
                                            <button type="button" class="btn btn-info pagados"
                                                    style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                        </td>
                                        @break
                                    @case(12)
                                        <td align='center' style="font-size: 10px;">
                                            <button type="button" class="btn btn-success terminados"
                                                    style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                        </td>
                                        @break
                                    @case(4)
                                        @if($contrato->dias <= 10)
                                            <td align='center' style="font-size: 10px;">
                                                <button type="button" class="btn btn-light atrasados"
                                                        style="color:#000000; font-size: 10px;">{{$contrato->descripcion}}</button>
                                            </td>
                                        @endif
                                        @if($contrato->dias >= 11 && $contrato->dias <= 20)
                                            <td align='center' style="font-size: 10px;">
                                                <button type="button" class="btn btn-light prueba2"
                                                        style="color:#000000; font-size: 10px;">{{$contrato->descripcion}}</button>
                                            </td>
                                        @endif
                                        @if($contrato->dias > 20)
                                            <td align='center' style="font-size: 10px;">
                                                <button type="button" class="btn btn-light ahora3"
                                                        style="color:#000000; font-size: 10px;">{{$contrato->descripcion}}</button>
                                            </td>
                                        @endif
                                        @break
                                @endswitch
                                <td align='center' style="font-size: 10px;">{{$contrato->idcontratorelacion}}</td>
                                <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->created_at)->format('Y-m-d')}}</td>
                                <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->fechaentrega)->format('Y-m-d')}}</td>
                                <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->fechagarantia)->format('Y-m-d')}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->nombre_usuariocreacion}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->zona}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->localidad}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->colonia}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->calle}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->numero}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->nombre}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->telefono}}</td>
                                <td align='center' style="font-size: 10px;">${{$contrato->totalreal}}</td>
                                @if($contrato->totalproducto != null && $contrato->totalproducto > 0)
                                    <td align='center' style="font-size: 10px;">${{$contrato->totalproducto}}</td>
                                @else
                                    <td align='center' style="font-size: 10px;">NA</td>
                                @endif
                                @if($contrato->totalpromocion != null && $contrato->totalpromocion > 0)
                                    <td align='center' style="font-size: 10px;">${{$contrato->totalpromocion}}</td>
                                @else
                                    <td align='center' style="font-size: 10px;">NA</td>
                                @endif
                                @if($contrato->totalabono != null)
                                    <td align='center' style="font-size: 10px;">${{$contrato->totalabono}}</td>
                                @else
                                    <td align='center' style="font-size: 10px;"></td>
                                @endif
                                <td align='center' style="font-size: 10px;">${{$contrato->total}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->ultimoabono}}</td>
                                @if($contrato->idcontratorelacion == null)
                                    @if($contrato->promo == 0)
                                        <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#ffaca6; font-size: 18px;"
                                                                                       aria-hidden="true"></i></td>
                                    @else
                                        <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#0AA09E; font-size: 18px;"
                                                                                       aria-hidden="true"></i></td>
                                    @endif
                                @else
                                    <td align='center' style="font-size: 10px;"></td>
                                @endif
                                <td align='center' style="font-size: 10px;">{{$contrato->dias}}</td>

                            </tr>
                        @endforeach
                    @endif
                @endif
                @if($cbGarantias == 1)

                    <tr>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; @if(Auth::user()->rol_id == 4) top: 20px; @else top: 42px;
                            @endif box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="24">GARANTIAS
                        </th>
                    </tr>
                    @if($contratosgarantiascreadas != null)
                        @foreach($contratosgarantiascreadas as $contrato)
                            <tr>
                                <td align='center' style="font-size: 10px;"><a href="{{route('vercontrato',[$idFranquicia,$contrato->id])}}" target="_blank">
                                        <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i
                                                class="fas fa-book-open"></i></button>
                                    </a></td>
                                <td align='center' style="font-size: 10px;"><a
                                        href="{{route('contratoactualizar',[$idFranquicia,$contrato->id])}}" target="_blank">
                                        <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i class="fas fa-pen"></i>
                                        </button>
                                    </a></td>
                                <td align='center' @if($contrato->estadogarantia == 2) style="font-size: 10px; background-color:#5cb85c;"
                                    @else style="font-size: 10px; background-color:#5bc0de;" @endif><p style="color:#FFFFFF">{{$contrato->id}}</p></td>
                                @switch($contrato->estatus_estadocontrato)
                                    @case(1)
                                        <td align='center' style="font-size: 10px;">
                                            <button type="button" class="btn btn-success terminados"
                                                    style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                        </td>
                                        @break
                                @endswitch
                                <td align='center' style="font-size: 10px;">{{$contrato->idcontratorelacion}}</td>
                                <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->created_at)->format('Y-m-d')}}</td>
                                <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->fechaentrega)->format('Y-m-d')}}</td>
                                <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->fechagarantia)->format('Y-m-d')}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->nombre_usuariocreacion}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->zona}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->localidad}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->colonia}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->calle}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->numero}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->nombre}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->telefono}}</td>
                                <td align='center' style="font-size: 10px;">${{$contrato->totalreal}}</td>
                                @if($contrato->totalproducto != null && $contrato->totalproducto > 0)
                                    <td align='center' style="font-size: 10px;">${{$contrato->totalproducto}}</td>
                                @else
                                    <td align='center' style="font-size: 10px;">NA</td>
                                @endif
                                @if($contrato->totalpromocion != null && $contrato->totalpromocion > 0)
                                    <td align='center' style="font-size: 10px;">${{$contrato->totalpromocion}}</td>
                                @else
                                    <td align='center' style="font-size: 10px;">NA</td>
                                @endif
                                @if($contrato->totalabono != null)
                                    <td align='center' style="font-size: 10px;">${{$contrato->totalabono}}</td>
                                @else
                                    <td align='center' style="font-size: 10px;"></td>
                                @endif
                                <td align='center' style="font-size: 10px;">${{$contrato->total}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->ultimoabono}}</td>
                                @if($contrato->idcontratorelacion == null)
                                    @if($contrato->promo == 0)
                                        <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#ffaca6; font-size: 18px;"
                                                                                       aria-hidden="true"></i></td>
                                    @else
                                        <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#0AA09E; font-size: 18px;"
                                                                                       aria-hidden="true"></i></td>
                                    @endif
                                @else
                                    <td align='center' style="font-size: 10px;"></td>
                                @endif
                                <td align='center' style="font-size: 10px;">{{$contrato->dias}}</td>
                            </tr>
                        @endforeach
                    @endif
                @endif
                @if($cbSupervision == 1)
                    <tr>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; @if(Auth::user()->rol_id == 4) top: 20px; @else top: 42px;
                            @endif box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="24">SUPERVISION
                        </th>
                    </tr>
                    @if($contratossupervision != null)
                        @foreach($contratossupervision as $contrato)
                            <tr>
                                <td align='center' style="font-size: 10px;"><a
                                        href="{{route('contratoactualizar',[$idFranquicia,$contrato->id])}}" target="_blank">
                                        <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i class="fas fa-pen"></i>
                                        </button>
                                    </a></td>
                                <td align='center' style="font-size: 10px;"><a href="{{route('vercontrato',[$idFranquicia,$contrato->id])}}" target="_blank">
                                        <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i
                                                class="fas fa-book-open"></i></button>
                                    </a></td>
                                <td align='center' style="font-size: 10px;"><p style="color:#0AA09E">{{$contrato->id}}</p></td>
                                <td align='center' style="font-size: 10px;">
                                    <button type="button" class="btn"
                                            style="background-color:#F88F32;color:#FFFFFF;text-align:center;font-size: 10px;">{{$contrato->descripcion}}</button>
                                </td>
                                <td align='center' style="font-size: 10px;">{{$contrato->idcontratorelacion}}</td>
                                <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->created_at)->format('Y-m-d')}}</td>
                                <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->fechaentrega)->format('Y-m-d')}}</td>
                                <td align='center'></td>
                                <td align='center' style="font-size: 10px;">{{$contrato->nombre_usuariocreacion}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->zona}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->localidad}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->colonia}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->calle}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->numero}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->nombre}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->telefono}}</td>
                                <td align='center' style="font-size: 10px;">${{$contrato->totalreal}}</td>
                                @if($contrato->totalproducto != null && $contrato->totalproducto > 0)
                                    <td align='center' style="font-size: 10px;">${{$contrato->totalproducto}}</td>
                                @else
                                    <td align='center' style="font-size: 10px;">NA</td>
                                @endif
                                @if($contrato->totalpromocion != null && $contrato->totalpromocion > 0)
                                    <td align='center' style="font-size: 10px;">${{$contrato->totalpromocion}}</td>
                                @else
                                    <td align='center' style="font-size: 10px;">NA</td>
                                @endif
                                @if($contrato->totalabono != null)
                                    <td align='center' style="font-size: 10px;">${{$contrato->totalabono}}</td>
                                @else
                                    <td align='center' style="font-size: 10px;"></td>
                                @endif
                                <td align='center' style="font-size: 10px;">${{$contrato->total}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->ultimoabono}}</td>
                                @if($contrato->idcontratorelacion == null)
                                    @if($contrato->promo == 0)
                                        <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#ffaca6; font-size: 18px;"
                                                                                       aria-hidden="true"></i></td>
                                    @else
                                        <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#0AA09E; font-size: 18px;"
                                                                                       aria-hidden="true"></i></td>
                                    @endif
                                @else
                                    <td align='center' style="font-size: 10px;"></td>
                                @endif
                                <td align='center' style="font-size: 10px;">{{$contrato->dias}}</td>
                            </tr>
                        @endforeach
                    @endif
                @endif

                @if($cbEntrega == 1)
                    <tr>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; @if(Auth::user()->rol_id == 4) top: 20px; @else top: 44px;
                            @endif box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="24">NO ENTREGADOS
                        </th>
                    </tr>
                    @if($contratosnoenviados != null)
                        @foreach($contratosnoenviados as $contrato)
                            <tr>
                                <td align='center' style="font-size: 10px;"><a href="{{route('vercontrato',[$idFranquicia,$contrato->id])}}" target="_blank">
                                        <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i
                                                class="fas fa-book-open"></i></button>
                                    </a></td>
                                <td align='center' style="font-size: 10px;"><a
                                        href="{{route('contratoactualizar',[$idFranquicia,$contrato->id])}}" target="_blank">
                                        <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i class="fas fa-pen"></i>
                                        </button>
                                    </a></td>
                                <td align='center' style="font-size: 10px;"><p style="color:#0AA09E">{{$contrato->id}}</p></td>
                                <td align='center' style="font-size: 10px;">
                                    <button type="button" class="btn btn-success terminados"
                                            style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                </td>
                                <td align='center' style="font-size: 10px;">{{$contrato->idcontratorelacion}}</td>
                                <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->created_at)->format('Y-m-d')}}</td>
                                <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->fechaentrega)->format('Y-m-d')}}</td>
                                <td align='center'></td>
                                <td align='center' style="font-size: 10px;">{{$contrato->nombre_usuariocreacion}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->zona}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->localidad}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->colonia}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->calle}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->numero}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->nombre}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->telefono}}</td>
                                <td align='center' style="font-size: 10px;">${{$contrato->totalreal}}</td>

                                @if($contrato->totalproducto != null && $contrato->totalproducto > 0)
                                    <td align='center' style="font-size: 10px;">${{$contrato->totalproducto}}</td>
                                @else
                                    <td align='center' style="font-size: 10px;">NA</td>
                                @endif
                                @if($contrato->totalpromocion != null && $contrato->totalpromocion > 0)
                                    <td align='center' style="font-size: 10px;">${{$contrato->totalpromocion}}</td>
                                @else
                                    <td align='center' style="font-size: 10px;">NA</td>
                                @endif
                                @if($contrato->totalabono != null)
                                    <td align='center' style="font-size: 10px;">${{$contrato->totalabono}}</td>
                                @else
                                    <td align='center' style="font-size: 10px;"></td>
                                @endif
                                <td align='center' style="font-size: 10px;">${{$contrato->total}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->ultimoabono}}</td>
                                @if($contrato->idcontratorelacion == null)
                                    @if($contrato->promo == 0)
                                        <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#ffaca6; font-size: 18px;"
                                                                                       aria-hidden="true"></i></td>
                                    @else
                                        <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#0AA09E; font-size: 18px;"
                                                                                       aria-hidden="true"></i></td>
                                    @endif
                                @else
                                    <td align='center' style="font-size: 10px;"></td>
                                @endif
                                <td align='center' style="font-size: 10px;">{{$contrato->dias}}</td>
                            </tr>
                        @endforeach
                    @endif
                @endif
                @if($cbAtrasado == 1)
                    <tr>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; @if(Auth::user()->rol_id == 4) top: 20px; @else top: 42px; @endif
                            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="24">ATRASADOS
                        </th>
                    </tr>
                    @if($contratosatrasados != null)
                        @foreach($contratosatrasados as $contrato)
                            <tr>
                                <td align='center' style="font-size: 10px;"><a href="{{route('vercontrato',[$idFranquicia,$contrato->id])}}" target="_blank">
                                        <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i
                                                class="fas fa-book-open"></i></button>
                                    </a></td>
                                <td align='center' style="font-size: 10px;"><a
                                        href="{{route('contratoactualizar',[$idFranquicia,$contrato->id])}}" target="_blank">
                                        <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i class="fas fa-pen"></i>
                                        </button>
                                    </a></td>
                                <td align='center' style="font-size: 10px;"><p style="color:#0AA09E">{{$contrato->id}}</p></td>
                                @switch($contrato->estatus_estadocontrato)
                                    @case(4)
                                        @if($contrato->dias <= 10)
                                            <td align='center' style="font-size: 10px;">
                                                <button type="button" class="btn btn-light atrasados"
                                                        style="color:#000000; font-size: 10px;">{{$contrato->descripcion}}</button>
                                            </td>
                                        @endif
                                        @if($contrato->dias >= 11 && $contrato->dias <= 20)
                                            <td align='center' style="font-size: 10px;">
                                                <button type="button" class="btn btn-light prueba2"
                                                        style="color:#000000; font-size: 10px;">{{$contrato->descripcion}}</button>
                                            </td>
                                        @endif
                                        @if($contrato->dias > 20)
                                            <td align='center' style="font-size: 10px;">
                                                <button type="button" class="btn btn-light ahora3"
                                                        style="color:#000000; font-size: 10px;">{{$contrato->descripcion}}</button>
                                            </td>
                                        @endif
                                        @break
                                @endswitch
                                <td align='center' style="font-size: 10px;">{{$contrato->idcontratorelacion}}</td>
                                <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->created_at)->format('Y-m-d')}}</td>
                                <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->fechaentrega)->format('Y-m-d')}}</td>
                                <td align='center'></td>
                                <td align='center' style="font-size: 10px;">{{$contrato->nombre_usuariocreacion}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->zona}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->localidad}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->colonia}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->calle}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->numero}}</td>
                                <td align='center' style="font-size: 10px;"><b>{{$contrato->nombre}}</b></td>
                                <td align='center' style="font-size: 10px;">{{$contrato->telefono}}</td>
                                <td align='center' style="font-size: 10px;">${{$contrato->totalreal}}</td>
                                @if($contrato->totalproducto != null && $contrato->totalproducto > 0)
                                    <td align='center' style="font-size: 10px;">${{$contrato->totalproducto}}</td>
                                @else
                                    <td align='center' style="font-size: 10px;">NA</td>
                                @endif
                                @if($contrato->totalpromocion != null && $contrato->totalpromocion > 0)
                                    <td align='center' style="font-size: 10px;">${{$contrato->totalpromocion}}</td>
                                @else
                                    <td align='center' style="font-size: 10px;">NA</td>
                                @endif
                                @if($contrato->totalabono != null)
                                    <td align='center' style="font-size: 10px;">${{$contrato->totalabono}}</td>
                                @else
                                    <td align='center' style="font-size: 10px;"></td>
                                @endif
                                <td align='center' style="font-size: 10px;">${{$contrato->total}}</td>
                                <td align='center' style="font-size: 10px;">{{$contrato->ultimoabono}}</td>
                                @if($contrato->idcontratorelacion == null)
                                    @if($contrato->promo == 0)
                                        <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#ffaca6; font-size: 18px;"
                                                                                       aria-hidden="true"></i></td>
                                    @else
                                        <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#0AA09E; font-size: 18px;"
                                                                                       aria-hidden="true"></i></td>
                                    @endif
                                @else
                                    <td align='center' style="font-size: 10px;"></td>
                                @endif
                                <td align='center' style="font-size: 10px;">{{$contrato->dias}}</td>
                            </tr>
                        @endforeach
                    @endif
                @endif
                @if($contratosprioritarios != null)
                    <tr>
                        <th style=" text-align:center;;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; @if(Auth::user()->rol_id == 4) top: 20px; @else top: 42px; @endif
                            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="24">
                            PRIORITARIOS
                        </th>
                    </tr>
                    @foreach($contratosprioritarios as $contrato)
                        <tr>
                            <td align='center' style="font-size: 10px;"><a href="{{route('vercontrato',[$idFranquicia,$contrato->id])}}" target="_blank">
                                    <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i
                                            class="fas fa-book-open"></i></button>
                                </a></td>
                            <td align='center' style="font-size: 10px;"><a
                                    href="{{route('contratoactualizar',[$idFranquicia,$contrato->id])}}" target="_blank">
                                    <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i class="fas fa-pen"></i>
                                    </button>
                                </a></td>
                            <td align='center' style="font-size: 10px;"><p style="color:#0AA09E">{{$contrato->id}}</p></td>
                            @switch($contrato->estatus_estadocontrato)
                                @case(2)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-primary entregados"
                                                style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                                @case(5)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-info pagados"
                                                style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                            @endswitch
                            <td align='center' style="font-size: 10px;">{{$contrato->idcontratorelacion}}</td>
                            <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->created_at)->format('Y-m-d')}}</td>
                            <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->fechaentrega)->format('Y-m-d')}}</td>
                            <td align='center'></td>
                            <td align='center' style="font-size: 10px;">{{$contrato->nombre_usuariocreacion}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->zona}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->localidad}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->colonia}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->calle}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->numero}}</td>
                            <td align='center' style="font-size: 10px;"><b>{{$contrato->nombre}}</b></td>

                            <td align='center' style="font-size: 10px;">{{$contrato->telefono}}</td>
                            <td align='center' style="font-size: 10px;">${{$contrato->totalreal}}</td>
                            @if($contrato->totalproducto != null && $contrato->totalproducto > 0)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalproducto}}</td>
                            @else
                                <td align='center' style="font-size: 10px;">NA</td>
                            @endif
                            @if($contrato->totalpromocion != null && $contrato->totalpromocion > 0)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalpromocion}}</td>
                            @else
                                <td align='center' style="font-size: 10px;">NA</td>
                            @endif
                            @if($contrato->totalabono != null)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalabono}}</td>
                            @else
                                <td align='center' style="font-size: 10px;"></td>
                            @endif
                            <td align='center' style="font-size: 10px;">${{$contrato->total}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->ultimoabono}}</td>
                            @if($contrato->idcontratorelacion == null)
                                @if($contrato->promo == 0)
                                    <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#ffaca6; font-size: 18px;"
                                                                                   aria-hidden="true"></i></td>
                                @else
                                    <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#0AA09E; font-size: 18px;"
                                                                                   aria-hidden="true"></i></td>
                                @endif
                            @else
                                <td align='center' style="font-size: 10px;"></td>
                            @endif
                            <td align='center' style="font-size: 10px;">{{$contrato->dias}}</td>
                        </tr>
                    @endforeach
                @endif
                @if($contratosperiodo != null)
                    <tr>
                        <th style=" text-align:center;;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; @if(Auth::user()->rol_id == 4) top: 20px; @else top: 42px; @endif
                            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="24">PERIODO
                        </th>
                    </tr>
                    @foreach($contratosperiodo as $contrato)
                        <tr>
                            <td align='center' style="font-size: 10px;"><p style="color:#0AA09E">{{$contrato->id}}</p></td>
                            @switch($contrato->estatus_estadocontrato)
                                @case(2)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-primary entregados"
                                                style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                                @case(5)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-info pagados"
                                                style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                            @endswitch
                            <td align='center' style="font-size: 10px;">{{$contrato->idcontratorelacion}}</td>
                            <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->created_at)->format('Y-m-d')}}</td>
                            <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->fechaentrega)->format('Y-m-d')}}</td>
                            <td align='center'></td>
                            <td align='center' style="font-size: 10px;">{{$contrato->nombre_usuariocreacion}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->zona}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->localidad}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->colonia}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->calle}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->numero}}</td>
                            <td align='center' style="font-size: 10px;"><b>{{$contrato->nombre}}</b></td>

                            <td align='center' style="font-size: 10px;">{{$contrato->telefono}}</td>
                            <td align='center' style="font-size: 10px;">${{$contrato->totalreal}}</td>
                            @if($contrato->totalproducto != null && $contrato->totalproducto > 0)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalproducto}}</td>
                            @else
                                <td align='center' style="font-size: 10px;">NA</td>
                            @endif
                            @if($contrato->totalpromocion != null && $contrato->totalpromocion > 0)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalpromocion}}</td>
                            @else
                                <td align='center' style="font-size: 10px;">NA</td>
                            @endif
                            @if($contrato->totalabono != null)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalabono}}</td>
                            @else
                                <td align='center' style="font-size: 10px;"></td>
                            @endif
                            <td align='center' style="font-size: 10px;">${{$contrato->total}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->ultimoabono}}</td>
                            @if($contrato->idcontratorelacion == null)
                                @if($contrato->promo == 0)
                                    <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#ffaca6; font-size: 18px;"
                                                                                   aria-hidden="true"></i></td>
                                @else
                                    <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#0AA09E; font-size: 18px;"
                                                                                   aria-hidden="true"></i></td>
                                @endif
                            @else
                                <td align='center' style="font-size: 10px;"></td>
                            @endif
                            <td align='center' style="font-size: 10px;">{{$contrato->dias}}</td>
                            <td align='center' style="font-size: 10px;"><a
                                    href="{{route('contratoactualizar',[$idFranquicia,$contrato->id])}}" target="_blank">
                                    <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i class="fas fa-pen"></i>
                                    </button>
                                </a></td>
                            <td align='center' style="font-size: 10px;"><a href="{{route('vercontrato',[$idFranquicia,$contrato->id])}}" target="_blank">
                                    <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i
                                            class="fas fa-book-open"></i></button>
                                </a></td>
                        </tr>
                    @endforeach
                @endif
                @if($cbEntrega == 1)
                    <tr>
                        <th style=" text-align:center;;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; @if(Auth::user()->rol_id == 4) top: 20px; @else top: 42px; @endif
                            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="24">POR
                            ENTREGAR
                        </th>
                    </tr>
                @endif
                @if($contratosentregar != null)
                    @foreach($contratosentregar as $contrato)
                        <tr>
                            <td align='center' style="font-size: 10px;"><a href="{{route('vercontrato',[$idFranquicia,$contrato->id])}}" target="_blank">
                                    <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i
                                            class="fas fa-book-open"></i></button>
                                </a></td>
                            <td align='center' style="font-size: 10px;"><a
                                    href="{{route('contratoactualizar',[$idFranquicia,$contrato->id])}}" target="_blank">
                                    <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i class="fas fa-pen"></i>
                                    </button>
                                </a></td>
                            <td align='center' style="font-size: 10px;"><p style="color:#0AA09E">{{$contrato->id}}</p></td>
                            @switch($contrato->estatus_estadocontrato)
                                @case(12)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-success terminados"
                                                style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                            @endswitch
                            <td align='center' style="font-size: 10px;">{{$contrato->idcontratorelacion}}</td>
                            <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->created_at)->format('Y-m-d')}}</td>
                            <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->fechaentrega)->format('Y-m-d')}}</td>
                            <td align='center'></td>
                            <td align='center' style="font-size: 10px;">{{$contrato->nombre_usuariocreacion}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->zona}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->localidad}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->colonia}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->calle}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->numero}}</td>
                            <td align='center' style="font-size: 10px;"><b>{{$contrato->nombre}}</b></td>
                            <td align='center' style="font-size: 10px;">{{$contrato->telefono}}</td>
                            <td align='center' style="font-size: 10px;">${{$contrato->totalreal}}</td>
                            @if($contrato->totalproducto != null && $contrato->totalproducto > 0)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalproducto}}</td>
                            @else
                                <td align='center' style="font-size: 10px;">NA</td>
                            @endif
                            @if($contrato->totalpromocion != null && $contrato->totalpromocion > 0)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalpromocion}}</td>
                            @else
                                <td align='center' style="font-size: 10px;">NA</td>
                            @endif
                            @if($contrato->totalabono != null)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalabono}}</td>
                            @else
                                <td align='center' style="font-size: 10px;"></td>
                            @endif
                            <td align='center' style="font-size: 10px;">${{$contrato->total}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->ultimoabono}}</td>
                            @if($contrato->idcontratorelacion == null)
                                @if($contrato->promo == 0)
                                    <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#ffaca6; font-size: 18px;"
                                                                                   aria-hidden="true"></i></td>
                                @else
                                    <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#0AA09E; font-size: 18px;"
                                                                                   aria-hidden="true"></i></td>
                                @endif
                            @else
                                <td align='center' style="font-size: 10px;"></td>
                            @endif
                            <td align='center' style="font-size: 10px;">{{$contrato->dias}}</td>
                        </tr>
                    @endforeach
                @endif
                @if($cbLaboratorio == 1)
                    <tr>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; @if(Auth::user()->rol_id == 4) top: 20px; @else top: 42px;
                            @endif box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="24">
                            LABORATORIO
                        </th>
                    </tr>
                @endif
                @if($contratoslaboratorio != null)
                    @foreach($contratoslaboratorio as $contrato)
                        <tr>
                            <td align='center' style="font-size: 10px;"><a
                                    href="{{route('contratoactualizar',[$idFranquicia,$contrato->id])}}" target="_blank">
                                    <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i class="fas fa-pen"></i>
                                    </button>
                                </a></td>
                            <td align='center' style="font-size: 10px;"><a
                                    href="{{route('vercontrato',[$idFranquicia,$contrato->id])}}" target="_blank">
                                    <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i
                                            class="fas fa-book-open"></i></button>
                                </a></td>
                            <td align='center' style="font-size: 10px;"><p style="color:#0AA09E">{{$contrato->id}}</p></td>
                            @switch($contrato->estatus_estadocontrato)
                                @case(7)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-primary aprobado"
                                                style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                                @case(10)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-warning manofactura"
                                                style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                                @case(11)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-info enprocesodeenvio"
                                                style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                            @endswitch
                            <td align='center' style="font-size: 10px;">{{$contrato->idcontratorelacion}}</td>
                            <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->created_at)->format('Y-m-d')}}</td>
                            <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->fechaentrega)->format('Y-m-d')}}</td>
                            <td align='center'></td>
                            <td align='center' style="font-size: 10px;">{{$contrato->nombre_usuariocreacion}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->zona}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->localidad}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->colonia}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->calle}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->numero}}</td>
                            <td align='center' style="font-size: 10px;"><b>{{$contrato->nombre}}</b></td>
                            <td align='center' style="font-size: 10px;">{{$contrato->telefono}}</td>
                            <td align='center' style="font-size: 10px;">${{$contrato->totalreal}}</td>
                            @if($contrato->totalproducto != null && $contrato->totalproducto > 0)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalproducto}}</td>
                            @else
                                <td align='center' style="font-size: 10px;">NA</td>
                            @endif
                            @if($contrato->totalpromocion != null && $contrato->totalpromocion > 0)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalpromocion}}</td>
                            @else
                                <td align='center' style="font-size: 10px;">NA</td>
                            @endif
                            @if($contrato->totalabono != null)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalabono}}</td>
                            @else
                                <td align='center' style="font-size: 10px;"></td>
                            @endif
                            <td align='center' style="font-size: 10px;">${{$contrato->total}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->ultimoabono}}</td>
                            @if($contrato->idcontratorelacion == null)
                                @if($contrato->promo == 0)
                                    <td align='center' style="font-size: 10px;"><i class="fas fa-tag"
                                                                                   style="color:#ffaca6; font-size: 18px;"
                                                                                   aria-hidden="true"></i></td>
                                @else
                                    <td align='center' style="font-size: 10px;"><i class="fas fa-tag"
                                                                                   style="color:#0AA09E; font-size: 18px;"
                                                                                   aria-hidden="true"></i></td>
                                @endif
                            @else
                                <td align='center' style="font-size: 10px;"></td>
                            @endif
                            <td align='center' style="font-size: 10px;">{{$contrato->dias}}</td>
                        </tr>
                    @endforeach
                @endif

                @if($cbConfirmacion == 1)
                    <tr>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; @if(Auth::user()->rol_id == 4) top: 20px; @else top: 42px;
                            @endif box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="24">
                            CONFIRMACIONES
                        </th>
                    </tr>
                @endif
                @if($contratosconfirmaciones != null)
                    @foreach($contratosconfirmaciones as $contrato)
                        <tr>
                            <td align='center' style="font-size: 10px;"><a
                                    href="{{route('vercontrato',[$idFranquicia,$contrato->id])}}" target="_blank">
                                    <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i
                                            class="fas fa-book-open"></i></button>
                                </a></td>
                            <td align='center' style="font-size: 10px;"><a
                                    href="{{route('contratoactualizar',[$idFranquicia,$contrato->id])}}" target="_blank">
                                    <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i class="fas fa-pen"></i>
                                    </button>
                                </a></td>
                            <td align='center' style="font-size: 10px;"><p style="color:#0AA09E">{{$contrato->id}}</p></td>
                            @switch($contrato->estatus_estadocontrato)
                                @case(1)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-success terminados"
                                                style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                                @case(9)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-warning manofactura"
                                                style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                            @endswitch
                            <td align='center' style="font-size: 10px;">{{$contrato->idcontratorelacion}}</td>
                            <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->created_at)->format('Y-m-d')}}</td>
                            <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->fechaentrega)->format('Y-m-d')}}</td>
                            <td align='center'></td>
                            <td align='center' style="font-size: 10px;">{{$contrato->nombre_usuariocreacion}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->zona}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->localidad}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->colonia}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->calle}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->numero}}</td>
                            <td align='center' style="font-size: 10px;"><b>{{$contrato->nombre}}</b></td>
                            <td align='center' style="font-size: 10px;">{{$contrato->telefono}}</td>
                            <td align='center' style="font-size: 10px;">${{$contrato->totalreal}}</td>
                            @if($contrato->totalproducto != null && $contrato->totalproducto > 0)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalproducto}}</td>
                            @else
                                <td align='center' style="font-size: 10px;">NA</td>
                            @endif
                            @if($contrato->totalpromocion != null && $contrato->totalpromocion > 0)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalpromocion}}</td>
                            @else
                                <td align='center' style="font-size: 10px;">NA</td>
                            @endif
                            @if($contrato->totalabono != null)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalabono}}</td>
                            @else
                                <td align='center' style="font-size: 10px;"></td>
                            @endif
                            <td align='center' style="font-size: 10px;">${{$contrato->total}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->ultimoabono}}</td>
                            @if($contrato->idcontratorelacion == null)
                                @if($contrato->promo == 0)
                                    <td align='center' style="font-size: 10px;"><i class="fas fa-tag"
                                                                                   style="color:#ffaca6; font-size: 18px;"
                                                                                   aria-hidden="true"></i></td>
                                @else
                                    <td align='center' style="font-size: 10px;"><i class="fas fa-tag"
                                                                                   style="color:#0AA09E; font-size: 18px;"
                                                                                   aria-hidden="true"></i></td>
                                @endif
                            @else
                                <td align='center' style="font-size: 10px;"></td>
                            @endif
                            <td align='center' style="font-size: 10px;">{{$contrato->dias}}</td>
                        </tr>
                    @endforeach
                @endif

                @if($cbTodos == 1)
                    <tr>
                        <th style=" text-align:center;;background-color:#0AA09E;color:#FFFFFF; font-size: 10px; position: sticky; @if(Auth::user()->rol_id == 4) top: 20px; @else top: 42px; @endif
                            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="24">TODOS
                        </th>
                    </tr>
                @endif
                @if($contratos != null)
                    @foreach($contratos as $contrato)
                        <tr>
                            <td align='center' style="font-size: 10px;"><a
                                    href="{{route('contratoactualizar',[$idFranquicia,$contrato->id])}}" target="_blank">
                                    <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i class="fas fa-pen"></i>
                                    </button>
                                </a></td>
                            <td align='center' style="font-size: 10px;"><a href="{{route('vercontrato',[$idFranquicia,$contrato->id])}}" target="_blank">
                                    <button type="button" class="btn btn-outline-success" style="font-size: 10px;"><i
                                            class="fas fa-book-open"></i></button>
                                </a></td>
                            @if($contrato->estatus_estadocontrato >= 1)
                                <td align='center' style="font-size: 10px;"><p style="color:#0AA09E">{{$contrato->id}}</p></td>
                            @else
                                <td align='center' style="font-size: 10px;"><p style="color:#c9c9c9">{{$contrato->id}}</p></td>
                            @endif
                            @switch($contrato->estatus_estadocontrato)
                                @case(0)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-secondary noterminados"
                                                style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                                @case(2)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-primary entregados"
                                                style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                                @case(3)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-danger precancelados"
                                                style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                                @case(5)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-info pagados"
                                                style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                                @case(6)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-info cancelados"
                                                style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                                @case(8)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-danger precancelados"
                                                style="color:#ff0000; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                                @case(14)
                                    <td align='center' style="font-size: 10px;">
                                        <button type="button" class="btn btn-info cancelados"
                                                style="color:#FEFEFE; font-size: 10px;">{{$contrato->descripcion}}</button>
                                    </td>
                                    @break
                            @endswitch
                            <td align='center' style="font-size: 10px;">{{$contrato->idcontratorelacion}}</td>
                            <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->created_at)->format('Y-m-d')}}</td>
                            <td align='center' style="font-size: 10px;">{{\Carbon\Carbon::parse($contrato->fechaentrega)->format('Y-m-d')}}</td>
                            <td align='center'></td>
                            <td align='center' style="font-size: 10px;">{{$contrato->nombre_usuariocreacion}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->zona}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->localidad}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->colonia}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->calle}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->numero}}</td>
                            <td align='center' style="font-size: 10px;"><b>{{$contrato->nombre}}</b></td>
                            <td align='center' style="font-size: 10px;">{{$contrato->telefono}}</td>
                            <td align='center' style="font-size: 10px;">${{$contrato->totalreal}}</td>
                            @if($contrato->totalproducto != null && $contrato->totalproducto > 0)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalproducto}}</td>
                            @else
                                <td align='center' style="font-size: 10px;">NA</td>
                            @endif
                            @if($contrato->totalpromocion != null && $contrato->totalpromocion > 0)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalpromocion}}</td>
                            @else
                                <td align='center' style="font-size: 10px;">NA</td>
                            @endif
                            @if($contrato->totalabono != null)
                                <td align='center' style="font-size: 10px;">${{$contrato->totalabono}}</td>
                            @else
                                <td align='center' style="font-size: 10px;"></td>
                            @endif
                            <td align='center' style="font-size: 10px;">${{$contrato->total}}</td>
                            <td align='center' style="font-size: 10px;">{{$contrato->ultimoabono}}</td>
                            @if($contrato->idcontratorelacion == null)
                                @if($contrato->promo == 0)
                                    <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#ffaca6; font-size: 18px;"
                                                                                   aria-hidden="true"></i></td>
                                @else
                                    <td align='center' style="font-size: 10px;"><i class="fas fa-tag" style="color:#0AA09E; font-size: 18px;"
                                                                                   aria-hidden="true"></i></td>
                                @endif
                            @else
                                <td align='center' style="font-size: 10px;"></td>
                            @endif
                            <td align='center' style="font-size: 10px;">{{$contrato->dias}}</td>

                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
