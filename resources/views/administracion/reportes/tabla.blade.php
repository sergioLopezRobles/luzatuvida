@extends('layouts.app')
@section('titulo','Reporte enviados'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2 style="text-align: left; color: #0AA09E">Enviados</h2>
        <input type="hidden" value="{{$idFranquicia}}" id="idFranquicia" name ="idFranquicia">
    <form action="{{route('filtrarlistacontratosreportes', $idFranquicia)}}"
          enctype="multipart/form-data" method="POST"
          onsubmit="btnSubmit.disabled = true;">
        @csrf
        <div class="row">
            <div class="col-2">
                <div class="form-group">
                    <label>Fecha inicial</label>
                    <input type="date" name="fechainibuscar"
                           class="form-control {!! $errors->first('fechainibuscar','is-invalid')!!}"
                           @isset($fechainibuscar) value="{{$fechainibuscar}}" @endisset>
                    @if($errors->has('fechainibuscar'))
                        <div class="invalid-feedback">{{$errors->first('fechainibuscar')}}</div>
                    @endif
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Fecha final</label>
                    <input type="date" name="fechafinbuscar"
                           class="form-control {!! $errors->first('fechafinbuscar','is-invalid')!!}"
                           @isset($fechafinbuscar) value="{{$fechafinbuscar}}" @endisset>
                    @if($errors->has('fechafinbuscar'))
                        <div class="invalid-feedback">{{$errors->first('fechafinbuscar')}}</div>
                    @endif
                </div>
            </div>
            @if(Auth::user()->rol_id == 7 || Auth::user()->rol_id == 15)
                <div class="col-2">
                    <label for="franquiciaSeleccionada">Sucursal</label>
                    <div class="form-group">
                        <select name="franquiciaSeleccionada"
                                class="form-control">
                            @if(count($franquicias) > 0)
                                <option value="" selected>Todas las sucursales</option>
                                @foreach($franquicias as $franquicia)
                                    <option
                                        value="{{$franquicia->id}}" {{ isset($franquiciaSeleccionada) ? ($franquiciaSeleccionada == $franquicia->id ? 'selected' : '' ) : '' }}>
                                        {{$franquicia->ciudad}}
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
                <select class="custom-select {!! $errors->first('zonaSeleccionada','is-invalid')!!}" name="zonaSeleccionada">
                    <option value="" selected>Todas las zonas</option>
                    <option value="1" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 1 ? 'selected' : '' ) : '' }}>1</option>
                    <option value="2" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 2 ? 'selected' : '' ) : '' }}>2</option>
                    <option value="3" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 3 ? 'selected' : '' ) : '' }}>3</option>
                    <option value="4" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 4 ? 'selected' : '' ) : '' }}>4</option>
                    <option value="5" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 5 ? 'selected' : '' ) : '' }}>5</option>
                    <option value="6" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 6 ? 'selected' : '' ) : '' }}>6</option>
                    <option value="7" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 7 ? 'selected' : '' ) : '' }}>7</option>
                    <option value="Oficina" {{ isset($zonaSeleccionada) ? ($zonaSeleccionada == 'Oficina' ? 'selected' : '' ) : '' }}>Oficina</option>
                </select>
                {!! $errors->first('zonaSeleccionada','<div class="invalid-feedback">Por favor, selecciona la zona.</div>')!!}
            </div>
            <div class="col-3">
                <button class="btn btn-outline-success" name="btnSubmit"
                        type="submit">@lang('mensajes.mensajefiltrar')
                </button>
                @if(sizeof($contratosreportes) > 0)
                    <a href="#" id="btnExportarExcel" onclick="exportarAExcel('Reporte Enviados','tablaReporteEnviados');" style="text-decoration:none; color:black; padding-left: 15px;">
                        <button type="button" class="btn btn-success">Exportar </button>
                    </a>
                @endif
            </div>
        </div>
    </form>
    <div class="contenedortblReportes" style="max-height: 600px; overflow-y: auto; width: 100%; overflow-x: auto; margin-top: 20px;">
        <table class="table-bordered table-striped table-general table-sm" id="tablaReporteEnviados">
            <thead>
            <tr>
                <th scope="col">FECHA VENTA</th>
                <th scope="col">FECHA ENTREGA PREVISTA</th>
                <th scope="col">FECHA ENVIO</th>
                <th scope="col">CONTRATO</th>
                <th scope="col">ESTADO</th>
                <th scope="col">LOCALIDAD</th>
                <th scope="col">ENTRE CALLES</th>
                <th scope="col">COLONIA</th>
                <th scope="col">CALLE</th>
                <th scope="col">NUMERO</th>
                <th scope="col">NOMBRE</th>
                <th scope="col">TELEFONO</th>
                <th scope="col">ZONA</th>
                <th scope="col">TOTAL</th>
                <th scope="col">ULTIMO ABONO</th>
                <th scope="col">FORMA PAGO</th>
            </tr>
            </thead>
            <tbody>
            @if(sizeof($contratosreportes) > 0)
                @foreach($contratosreportes as $contratoreporte)
                    <tr>
                        <td align='center' style="font-size: 10px;">{{$contratoreporte->FECHAVENTA}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratoreporte->FECHAENTREGA}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratoreporte->FECHAENVIO}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratoreporte->CONTRATO}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratoreporte->ESTATUS}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratoreporte->LOCALIDAD}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratoreporte->ENTRECALLES}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratoreporte->COLONIA}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratoreporte->CALLE}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratoreporte->NUMERO}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratoreporte->NOMBRE}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratoreporte->TELEFONO}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratoreporte->ZONA}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratoreporte->TOTAL}}</td>
                        <td align='center' style="font-size: 10px;">{{$contratoreporte->ULTIMOABONO}}</td>
                        @switch($contratoreporte->FORMAPAGO)
                            @case(0)
                            <td align='center' style="font-size: 10px;">CONTADO</td>
                            @break
                            @case(1)
                            <td align='center' style="font-size: 10px;">SEMANAL</td>
                            @break
                            @case(2)
                            <td align='center' style="font-size: 10px;">QUINCENAL</td>
                            @break
                            @case(4)
                            <td align='center' style="font-size: 10px;">MENSUAL</td>
                            @break
                        @endswitch
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
