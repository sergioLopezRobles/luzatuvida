@extends('layouts.app')
@section('titulo','Reporte buzón'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2 style="text-align: left; color: #0AA09E">Buzón quejas/sugerencias</h2>
    <input type="hidden" id="idFranquicia" name="idFranquicia" value="{{$idFranquicia}}">
    @if(Auth::user()->rol_id == 7)
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
                        <div class="row">
                            <div class="col-3">
                                <label for="franquiciaSeleccionada">Sucursal</label>
                                <div class="form-group">
                                    <select name="franquiciaSeleccionada"
                                            class="form-control"
                                            id="franquiciaSeleccionada">
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
                            <div class="col-2" id="spCargando">
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
    @endif
    <div class="contenedortblBuzon">
        <table class="table-bordered table-striped table-sm" style="text-align: left; position: relative; border-collapse: collapse; margin-top: 20px; width: 100%; overflow-x: auto;" id="tblBuzon">
            @if(Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8)
                <thead>
                <tr>
                    <th style=" text-align:center; font-size: 10px; background: white; width: 15%;" >FECHA CREACIÓN</th>
                    <th style=" text-align:center; font-size: 10px; background: white; width: 15%; " >USUARIO CREACIÓN</th>
                    <th style=" text-align:center; font-size: 10px; background: white; width: 70%;">MENSAJE</th>
                </tr>
                </thead>
                @if(sizeof($mensajesBuzon) > 0)
                    <tbody>
                    @foreach($mensajesBuzon as $mensajeBuzon)
                        <tr>
                            <td align='center' style="font-size: 10px;">{{$mensajeBuzon->created_at}}</td>
                            <td align='center' style="font-size: 10px;">{{$mensajeBuzon->usuario_creacion}}</td>
                            <td align='center' style="font-size: 10px;">{{$mensajeBuzon->mensaje}}</td>
                        </tr>
                    @endforeach
                    @else
                        <tr>
                            <td align='center' style="font-size: 10px;" colspan="3">Sin registros</td>
                        </tr>
                    @endif
                    </tbody>
                @endif
        </table>
    </div>
    <p></p>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
