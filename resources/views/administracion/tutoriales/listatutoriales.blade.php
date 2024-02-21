@extends('layouts.app')
@section('titulo','Tutoriales'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>Tutoriales</h2>
        @if(Auth::user()->id == 61 || Auth::user()->id == 1 || Auth::user()->id == 761)
            <form id="frmAgregarVideo" action="{{route('agregarvideotutorial',$idFranquicia)}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <label>Título video</label>
                            <input type="text" name="titulo" id="titulo" class="form-control {!! $errors->first('titulo','is-invalid')!!}"  placeholder="TÍTULO"
                                   value="{{old('titulo')}}">
                            {!! $errors->first('titulo','<div class="invalid-feedback">Titulo de video obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Descripción</label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control {!! $errors->first('descripcion','is-invalid')!!}"  placeholder="DESCRIPCIÓN"
                                   value="{{old('descripcion')}}">
                            {!! $errors->first('descripcion','<div class="invalid-feedback">Descripción de video obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Enlace</label>
                            <input type="url" id="enlace" name="enlace"  class="form-control {!! $errors->first('enlace','is-invalid')!!}" placeholder="https://www.ejemplo.com">
                            {!! $errors->first('enlace','<div class="invalid-feedback">Enlace de video obligatorio. Ej:https://www.ejemplo.com</div>')!!}
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Sección</label>
                            <select name="seccionSeleccionada"
                                    class="form-control {!! $errors->first('seccionSeleccionada','is-invalid')!!}">
                                @if(count($secciones) > 0)
                                    <option value="">Selecciona una seccion</option>
                                    @foreach($secciones as $seccion)
                                        <option value="{{$seccion->id}}">{{$seccion->descripcion}}</option>
                                    @endforeach
                                @else
                                    <option selected>Sin registros</option>
                                @endif
                            </select>
                            {!! $errors->first('seccionSeleccionada','<div class="invalid-feedback">Sección de video obligatoria.</div>')!!}
                        </div>
                    </div>
                </div>
                <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit" form="frmAgregarVideo">Agregar video</button>
            </form>
            <hr>
        @endif
        <form id="frmBuscarVideo" action="{{route('listatutorialesfiltrar',$idFranquicia)}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="row" style="margin-top: 20px; margin-bottom: 20px;">
                <div class="col-4">
                    <input name="filtro" type="text" class="form-control" placeholder="Buscar..">
                </div>
                <div class="col-5">
                    <div>
                        <button type="submit" name="btnSubmit" class="btn btn-outline-success">Buscar</button>
                    </div>
                </div>
            </div>
        </form>
        <div id="accordion" class="accordion">
            <div class="card">
                <div class="card-header" id="tutorialesAdministracion">
                    <h5 class="mb-0">
                        <button id="collapsedAdministracions" class="btn btn-link collapsed" data-toggle="collapse" data-target="#administracion" aria-expanded="false" aria-controls="administracion">
                            Administración
                        </button>
                    </h5>
                </div>
                <div id="administracion" class="collapse @if($tutorialesAdministracion != null) show @endif" aria-labelledby="administracion" data-parent="#accordion">
                    <div class="card-body">
                        @if($tutorialesAdministracion != null)
                            <ol>
                                @foreach($tutorialesAdministracion as $tutorial)
                                    <li style="margin-bottom: 10px;">
                                        <a href="{{$tutorial->link}}" style="text-transform: uppercase; text-decoration: none; color: black;" target="_blank">{{$tutorial->titulo}}</a>
                                        @if(Auth::user()->id == 61 || Auth::user()->id == 1 || Auth::user()->id == 761)
                                            <a class="btn btn-outline-danger btn-sm" href="{{route('eliminarvideotutorial',[$idFranquicia, $tutorial->indice])}}">Eliminar</a>
                                        @endif
                                    </li>

                                @endforeach
                            </ol>
                        @else
                            <label> SIN CONTENIDO AGREGADO</label>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card" style="margin-top: 20px;">
                <div class="card-header" id="tutorialesCobranza">
                    <h5 class="mb-0">
                        <button id="collapsedCobranza" class="btn btn-link collapsed" data-toggle="collapse" data-target="#cobranza" aria-expanded="false" aria-controls="cobranza">
                            Cobranza
                        </button>
                    </h5>
                </div>
                <div id="cobranza" class="collapse @if($tutorialesCobranza != null) show @endif" aria-labelledby="cobranza" data-parent="#accordion">
                    <div class="card-body">
                        @if($tutorialesCobranza != null)
                            <ol>
                                @foreach($tutorialesCobranza as $tutorial)
                                    <li style="margin-bottom: 10px;">
                                        <a href="{{$tutorial->link}}" style="text-transform: uppercase; text-decoration: none; color: black;" target="_blank">{{$tutorial->titulo}}</a>
                                        @if(Auth::user()->id == 61 || Auth::user()->id == 1 || Auth::user()->id == 761)
                                            <a class="btn btn-outline-danger btn-sm" href="{{route('eliminarvideotutorial',[$idFranquicia, $tutorial->indice])}}">Eliminar</a>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        @else
                            <label> SIN CONTENIDO AGREGADO</label>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card" style="margin-top: 20px;">
                <div class="card-header" id="tutorialesConfirmaciones">
                    <h5 class="mb-0">
                        <button id="collapsedConfirmaciones" class="btn btn-link collapsed" data-toggle="collapse" data-target="#confirmaciones" aria-expanded="false" aria-controls="confirmaciones">
                            Confirmaciones
                        </button>
                    </h5>
                </div>
                <div id="confirmaciones" class="collapse @if($tutorialesConfirmaciones != null) show @endif" aria-labelledby="confirmaciones" data-parent="#accordion">
                    <div class="card-body">
                        @if($tutorialesConfirmaciones != null)
                            <ol>
                                @foreach($tutorialesConfirmaciones as $tutorial)
                                    <li style="margin-bottom: 10px;">
                                        <a href="{{$tutorial->link}}" style="text-transform: uppercase; text-decoration: none; color: black;" target="_blank">{{$tutorial->titulo}}</a>
                                        @if(Auth::user()->id == 61 || Auth::user()->id == 1 || Auth::user()->id == 761)
                                            <a class="btn btn-outline-danger btn-sm" href="{{route('eliminarvideotutorial',[$idFranquicia, $tutorial->indice])}}">Eliminar</a>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        @else
                            <label> SIN CONTENIDO AGREGADO</label>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card" style="margin-top: 20px;">
                <div class="card-header" id="tutorialesLaboratorio">
                    <h5 class="mb-0">
                        <button id="collapsedLaboratorio" class="btn btn-link collapsed" data-toggle="collapse" data-target="#laboratorio" aria-expanded="false" aria-controls="laboratorio">
                            Laboratorio
                        </button>
                    </h5>
                </div>
                <div id="laboratorio" class="collapse @if($tutorialesLaboratorio != null) show @endif" aria-labelledby="laboratorio" data-parent="#accordion">
                    <div class="card-body">
                        @if($tutorialesLaboratorio != null)
                            <ol>
                                @foreach($tutorialesLaboratorio as $tutorial)
                                    <li style="margin-bottom: 10px;">
                                        <a href="{{$tutorial->link}}" style="text-transform: uppercase; text-decoration: none; color: black;" target="_blank">{{$tutorial->titulo}}</a>
                                        @if(Auth::user()->id == 61 || Auth::user()->id == 1 || Auth::user()->id == 761)
                                            <a class="btn btn-outline-danger btn-sm" href="{{route('eliminarvideotutorial',[$idFranquicia, $tutorial->indice])}}">Eliminar</a>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        @else
                            <label> SIN CONTENIDO AGREGADO</label>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card" style="margin-top: 20px;">
                <div class="card-header" id="tutorialesVentas">
                    <h5 class="mb-0">
                        <button id="collapsedCobranza" class="btn btn-link collapsed" data-toggle="collapse" data-target="#ventas" aria-expanded="false" aria-controls="ventas">
                            Ventas
                        </button>
                    </h5>
                </div>
                <div id="ventas" class="collapse @if($tutorialesVentas != null) show @endif" aria-labelledby="ventas" data-parent="#accordion">
                    <div class="card-body">
                        @if($tutorialesVentas != null)
                            <ol>
                                @foreach($tutorialesVentas as $tutorial)
                                    <li style="margin-bottom: 10px;">
                                        <a href="{{$tutorial->link}}" style="text-transform: uppercase; text-decoration: none; color: black;" target="_blank">{{$tutorial->titulo}}</a>
                                        @if(Auth::user()->id == 61 || Auth::user()->id == 1 || Auth::user()->id == 761)
                                            <a class="btn btn-outline-danger btn-sm" href="{{route('eliminarvideotutorial',[$idFranquicia, $tutorial->indice])}}">Eliminar</a>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        @else
                            <label> SIN CONTENIDO AGREGADO</label>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card" style="margin-top: 20px;">
                <div class="card-header" id="tutorialesOtros">
                    <h5 class="mb-0">
                        <button id="collapsedOtros" class="btn btn-link collapsed" data-toggle="collapse" data-target="#otros" aria-expanded="false" aria-controls="otros">
                            Otros
                        </button>
                    </h5>
                </div>
                <div id="otros" class="collapse @if($tutorialesOtros != null) show @endif" aria-labelledby="tutorialesOtros" data-parent="#accordion">
                    <div class="card-body">
                        @if($tutorialesOtros != null)
                            <ol>
                                @foreach($tutorialesOtros as $tutorial)
                                    <li style="margin-bottom: 10px;">
                                        <a href="{{$tutorial->link}}" style="text-transform: uppercase; text-decoration: none; color: black;" target="_blank">{{$tutorial->titulo}}</a>
                                        @if(Auth::user()->id == 61 || Auth::user()->id == 1 || Auth::user()->id == 761)
                                            <a class="btn btn-outline-danger btn-sm" href="{{route('eliminarvideotutorial',[$idFranquicia, $tutorial->indice])}}">Eliminar</a>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        @else
                            <label> SIN CONTENIDO AGREGADO</label>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
