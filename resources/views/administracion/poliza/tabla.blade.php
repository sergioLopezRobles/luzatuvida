@extends('layouts.app')
@section('titulo','Polizas'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
{{--    <div class="row">--}}
{{--        <div class="col-9">--}}
{{--        </div>--}}
{{--        <div class="col-3">--}}
{{--            <a class="btn btn-outline-success btn-block" href="{{route('crearpoliza',$idFranquicia)}}">NUEVA POLIZA</a>--}}
{{--        </div>--}}
{{--    </div>--}}
    <h2>Polizas</h2>
        @if(Auth::user()->rol_id== 7)
            <form id="frmFiltrarPolizas" action="{{route('filtrarlistapolizafranquicia',$idFranquicia)}}" enctype="multipart/form-data" method="POST"
                  onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <select name="franquiciaSeleccionada"
                                    class="form-control"
                                    id="franquiciaSeleccionada">
                                @if(count($franquicias) > 0)
                                    @foreach($franquicias as $franquicia)
                                        <option
                                            value="{{$franquicia->id}}"
                                            {{ isset($franquicia) ? ($franquicia->id == $idFranquicia ? 'selected' : '' ) : '' }}>{{$franquicia->ciudad}}
                                        </option>
                                    @endforeach
                                @else
                                    <option selected>Sin registros</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-1">
                        <button form="frmFiltrarPolizas" type="submit" name="btnSubmit" class="btn btn-outline-success">Actualizar</button>
                    </div>
                </div>
            </form>
        @endif
    <table id="tablaFranquicias" class="table-bordered table-striped table-general table-sm">
        @if(sizeof($polizas)>0)
            <thead>
            <tr>
                <th style=" text-align:center;" scope="col">POLIZA</th>
                <th style=" text-align:center;" scope="col">FRANQUICIA</th>
                <th style=" text-align:center;" scope="col">TERMINO</th>
                <th style=" text-align:center;" scope="col">AUTORIZO</th>
                <th style=" text-align:center;" scope="col">TOTAL</th>
                <th style=" text-align:center;" scope="col">FECHA</th>
                <th style=" text-align:center;" scope="col">VER</th>
            </tr>
            </thead>
        @endif
        <tbody>
        @foreach($polizas as $poliza)
            <tr>
                <td align='center'>{{$poliza->ID}}</td>
                <td align='center'>{{$poliza->FRANQUICIA}}</td>
                <td align='center'>{{$poliza->REALIZO}}</td>
                <td align='center'>{{$poliza->AUTORIZO}}</td>
                <td align='center'>{{$poliza->TOTAL}}</td>
                <td align='center'>{{$poliza->CREATED_AT}}</td>
                <td align='center'><a href="{{route('verpoliza',[$idFranquicia,$poliza->ID])}}">
                        <button style="margin:0px;" type="button" class="btn btn-outline-success btn-sm"><i
                                class="fas fa-book-open"></i></button>
                    </a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if(Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)
        @if($polizas!=null)
            <div class="d-flex justify-content-center">
                {{$polizas->links('pagination::bootstrap-4')}}
            </div>
        @endif
    @endif
    </div>
    {{--    Seccion de notificacion pie de pagina--}}
    @include('parciales.notificaciones')
@endsection
