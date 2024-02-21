@extends('layouts.app')
@section('titulo','Actualizar tratamiento'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2>@lang('mensajes.mensajeeditartratamiento')</h2>
    <form  action="{{route('tratamientoeditar',[$idFranquicia,$tratamiento[0]->id])}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;" id="formEditarTratamiento">
        @csrf
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label>Tratamiento</label>
                    <input type="text" name="tratamiento" class="form-control {!! $errors->first('tratamiento','is-invalid')!!}"  placeholder="Tratamiento"
                           readonly value="{{$tratamiento[0]->nombre}}">
                    {!! $errors->first('tratamiento','<div class="invalid-feedback">Nombre no valido</div>')!!}
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Precio</label>
                    <input type="number" name="precio" class="form-control {!! $errors->first('precio','is-invalid')!!}" min="0" placeholder="Precio"
                           value="{{$tratamiento[0]->precio}}">
                    {!! $errors->first('precio','<div class="invalid-feedback">Precio no valido</div>')!!}
                </div>
            </div>
            <div class="col-4">
                <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit" style="margin-top: 30px;" form="formEditarTratamiento">@lang('mensajes.actualizar')</button>
            </div>
        </div>
    </form>
    @if(str_contains(strtoupper($tratamiento[0]->nombre), "TINTE"))
        <hr>
        <h2>Colores disponibles para tratamiento</h2>
            <form action="{{route('agregarcolortratamiento',[$idFranquicia,$tratamiento[0]->id])}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;" id="formAgregarColor">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>Color</label>
                            <input type="text" name="colorTratamiento" id="colorTratamiento" class="form-control {!! $errors->first('colorTratamiento','is-invalid')!!}"  placeholder="Color">
                            {!! $errors->first('colorTratamiento','<div class="invalid-feedback">Color de tratamiento obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-2">
                        <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit" style="margin-top: 30px;" form="formAgregarColor">Agregar color</button>
                    </div>
                </div>
            </form>
        <div style="margin-top: 20px; margin-bottom: 30px;">
            <table id="tablaColoresTratamiento" class="table-bordered table-striped table-general">
                <thead>
                <tr>
                    <th style="text-align:center;" scope="col">INDICE</th>
                    <th style="text-align:center;" scope="col">COLOR</th>
                    <th style="text-align:center;" scope="col">FECHA DE REGISTRO</th>
                    <th style="text-align:center;" scope="col">ELIMINAR</th>
                </tr>
                </thead>
                <tbody>
                @if($coloresTratamiento != null)
                    @foreach($coloresTratamiento as $color)
                        <tr>
                            <td align='center'>{{$indice}}</td>
                            <td align='center'>{{$color->color}}</td>
                            <td align='center'>{{$color->created_at}}</td>
                            <td align='center'>
                                <a class="btn btn-outline-danger btnEliminarColor btn-sm" href="#" data-toggle="modal" data-target="#modalEliminarColor"
                                   data_parametros_modal="{{$color->indice}}">
                                    <i class="bi bi-trash3-fill"></i></a>
                            </td>
                        </tr>
                        @php $indice = $indice + 1 @endphp
                    @endforeach
                @else
                    <tr>
                        <th align='center' colspan="4">SIN REGISTROS</th>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    @endif
        <div class="row">
            <a href="{{route('listasfranquicia',$idFranquicia)}}" class="btn btn-outline-success btn-block">@lang('mensajes.volveradminfranquicia')</a>
        </div>
    </div>

    <div class="modal fade" id="modalEliminarColor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form action="{{route('eliminarcolortratamiento',$idFranquicia)}}" enctype="multipart/form-data"
              method="POST" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #0AA09E; color: white;"><b>Eliminar color</b></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                ¿Estas seguro que quieres eliminar el color?
                            </div>
                            <input type="hidden" name="idColor"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-success" data-dismiss="modal">Cancelar</button>
                        <button class="btn btn-outline-danger btn-ok" name="btnSubmit" type="submit">Eliminar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
