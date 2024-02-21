@extends('layouts.appclientes')
@section('titulo','Visitanos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <div class="col-3">
            <input type="hidden" id="jsonSucursales" value="{{json_encode($franquicias)}}">
            <div class="row" style="font-size: 24px; margin-bottom: 10px; padding-left: 10px; font-weight: bold; color: #0AA09E;">Localiza tu sucursal más cercana</div>
            <div class="row mb-3" style="justify-content: space-between;">
                <div class="col-7">
                    <div class="input-group rounded">
                        <input type="search" class="form-control rounded" id="filtarSucursal" placeholder="Buscar" aria-label="Search" aria-describedby="search-addon" />
                        <span class="input-group-text border-0" id="icFiltrar"><i class="fas fa-search" onclick="filtrarListaSucursales()" style="cursor: pointer;"></i></span>
                    </div>
                </div>
                <div class="col-5">
                    <button class="btn btn-outline-success-client" onclick="verMiUbicacion()">Ir a mi ubicación</button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-3" style="overflow-y: auto; height: 550px;" id="divSucursalesVisitar">
                @foreach($franquicias as $franquicia)
                    <div class="informacion">
                        <p style="font-weight: bold; font-size: 14px;">Laboratorio Óptico Luz a tu vida {{$franquicia->ciudad}}</p>
                        @if($franquicia->calle != null)
                            <p style="font-size: 12px">{{$franquicia->calle}} NO. {{$franquicia->numero}}, ENTRE: {{$franquicia->entrecalles}},  COL. {{$franquicia->colonia}},{{$franquicia->ciudad}}, {{$franquicia->estado}} </p>
                        @else
                            <p style="font-size: 12px">{{$franquicia->ciudad}}, {{$franquicia->colonia}}, NO. {{$franquicia->numero}}, {{$franquicia->estado}} </p>
                        @endif
                        <p style="font-size: 14px">Teléfono: {{$franquicia->telefonoatencionclientes}}</p>
                        @if($franquicia->whatsapp != null)
                            <p style="font-size: 14px">Whatsapp: {{$franquicia->whatsapp}}</p>
                        @endif
                        <div style="display:flex; justify-content: right; margin: 10px;">
                            <div class="btn-group" role="group">
                                <a class="btn btn-outline-primary btn-sm" href="{{route('calendariocitas',[$franquicia->id])}}" target="_blank">Agendar cita</a>
                                @if($franquicia->coordenadas != null)
                                    <a class="btn btn-outline-primary btn-sm" onclick="verUbiciacionSucursal('{{$franquicia->coordenadas}}')">Ver ubicación</a>
                                    <a class="btn btn-outline-primary btn-sm" href="https://api.whatsapp.com/send?text=https://maps.google.com/?q={{$franquicia->coordenadas}}&z=18"
                                       data-action="share/whatsapp/share" target="_blank">Compartir</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-9">
                {{-- Mapa --}}
                <div id="mapMarcaderesSucursales" style="border: black 3px solid; width: 100%; height: 550px;">

                </div>
            </div>
        </div>
    </div>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
