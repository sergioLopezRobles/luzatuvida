@extends('layouts.app')
@section('titulo','Traspaso contratos zona'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2 style="text-align: left; color: #0AA09E">Traspaso contratos zona</h2>
        <div style="color: #0AA09E; font-weight: bold;">Paso 1 - Seleccionar la zona de donde vas a tomar las cuentas y presionar boton filtrar.</div>
        <div class="row" id="contenedorFiltrosSelect">
            @if(Auth::user()->rol_id == 7)
                <div class="col-3">
                    <label for="franquiciaSeleccionada">Sucursal</label>
                    <div class="form-group">
                        <select name="franquiciaSeleccionada"
                                class="form-control"
                                id="franquiciaSeleccionada">
                            @if(count($franquicias) > 0)
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
                <div class="form-group">
                    <select name="zonaSeleccionada"
                            class="form-control"
                            id="zonaSeleccionada">
                        @if(count($zonasPrincipal) > 0)
                            @foreach($zonasPrincipal as $zonaPrincipal)
                                <option
                                    value="{{$zonaPrincipal->zona}}">{{$zonaPrincipal->zona}}
                                </option>
                            @endforeach
                        @else
                            <option selected>Sin registros</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-1">
                <button class="btn btn-outline-success btn-block" style="margin-top: 30px" id="btnFiltrarContratoZona" onclick="cargarListaContratoZona()">Filtrar</button>
            </div>
            <div class="col-2" id="spCargando">
                <div class="d-flex justify-content-center">
                    <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 30px;" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group" id="listatraspasarcontratozona">

        </div>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
