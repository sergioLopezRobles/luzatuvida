@extends('layouts.app')
@section('titulo','Reporte abonos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2 style="text-align: left; color: #0AA09E">Abonos</h2>
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
                        <form action="{{route('filtrarreporteabonossucursal', $idFranquicia)}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                            @csrf
                            <div class="row">
                                <div class="col-3">
                                    <label for="franquiciaSeleccionada">Sucursal</label>
                                    <div class="form-group">
                                        <select name="franquiciaSeleccionada"
                                                class="form-control"
                                                id="franquiciaSeleccionada">
                                            @if(count($franquicias) > 0)
                                                <option value="" selected>Seleccionar sucursal</option>
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
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Selecciona dia de la semana</label>
                                        <input type="date" name="fechaSeleccionada" id="fechaSeleccionada" class="form-control {!! $errors->first('fechaSeleccionada','is-invalid')!!}"
                                               @isset($fechaSeleccionada) value = "{{$fechaSeleccionada}}" @endisset>
                                        <input type="text" class="form-control mt-1" name="quincenaSeleccionada" id="quincenaSeleccionada" value="{{$quincena}}" readonly>
                                        @if($errors->has('fechaSeleccionada'))
                                            <div class="invalid-feedback">Selecciona una fecha valida mayor a 01 de septiembre de 2023.</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="custom-control custom-checkbox" style="margin-top: 35px;">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="cbTipoAbono" id="cbTipoAbono"
                                               value="1" @if($cbTipoAbono == 1) checked @endif>
                                        <label class="custom-control-label" for="cbTipoAbono">Incluir abonos de polizas de seguro.</label>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-6">
                                                <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">@lang('mensajes.mensajefiltrar')</button>
                                            </div>
                                            <div class="col-6" style="margin-top: 30px;">
                                                @if(sizeof($abonos) > 0)
                                                    <a href="#" id="btnExportarExcel" onclick="exportarAExcel('Reporte Abonos','tablaReporteAbonos');" style="text-decoration:none;">
                                                        <button type="button" class="btn btn-success btn-block">Exportar </button>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="contenedortblReportes mt-5">
            <table class="table-bordered table-striped table-general table-sm" id="tablaReporteAbonos">
                <thead>
                <tr>
                    <th scope="col">CONTRATO</th>
                    <th scope="col">LOCALIDAD</th>
                    <th scope="col">COLINIA</th>
                    <th scope="col">CALLE</th>
                    <th scope="col">NUMERO</th>
                    <th scope="col">NOMBRE CLIENTE</th>
                    <th scope="col">TELEFONO</th>
                    <th scope="col">ABONO</th>
                    <th scope="col">DESCRIPCION</th>
                    <th scope="col">PRODCUTO</th>
                    <th scope="col">COLOR</th>
                    <th scope="col">FECHA CREACIÓN</th>
                </tr>
                </thead>
                <tbody>
                @if(sizeof($abonos) > 0)
                    @foreach($abonos as $abono)
                        <tr>
                            <td align='center' style="font-size: 10px;">{{$abono->id}}</td>
                            <td align='center' style="font-size: 10px;">{{$abono->localidad}}</td>
                            <td align='center' style="font-size: 10px;">{{$abono->colonia}}</td>
                            <td align='center' style="font-size: 10px;">{{$abono->calle}}</td>
                            <td align='center' style="font-size: 10px;">{{$abono->numero}}</td>
                            <td align='center' style="font-size: 10px;">{{$abono->nombre}}</td>
                            <td align='center' style="font-size: 10px;">{{$abono->telefono}}</td>
                            <td align='center' style="font-size: 10px;">{{$abono->abono}}</td>
                            <td align='center' style="font-size: 10px;">{{$abono->DESCRIPCION}}</td>
                            <td align='center' style="font-size: 10px;"> @if($abono->tipoabono == '7') {{$abono->producto}} @else NA @endif  </td>
                            <td align='center' style="font-size: 10px;">@if($abono->tipoabono == '7') {{$abono->color}} @else NA @endif</td>
                            <td align='center' style="font-size: 10px;">{{$abono->created_at}}</td>
                        </tr>
                    @endforeach
                @else
                    <td align='center' colspan="16" style="font-size: 10px;">Sin registros</td>
                @endif
                </tbody>
            </table>
        </div>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
