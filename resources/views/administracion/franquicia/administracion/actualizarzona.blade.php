@extends('layouts.app')
@section('titulo','Editar zona'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>Editar zona</h2>
        <div class="row">
            <div class="col-3">
                <form action="{{route('cambiarzonaeditar',$idFranquicia)}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;" >
                    @csrf
                    <div class="row">
                        <div class="col-8">
                            <label>Seleccionar Zona</label>
                            <select name="zonaSeleccionada" id="zonaSeleccionada" class="form-control form-control{!! $errors->first('zonaSeleccionada','is-invalid')!!}" required>
                                @if(sizeof($zonas) > 0)
                                    @foreach($zonas as $zona)
                                        <option value="{{$zona->id}}" @if($zona->id == $idZona) selected @endif>{{$zona->zona}}</option>
                                    @endforeach
                                @else
                                    <option value="">Sin registros</option>
                                @endif
                            </select>
                            {!! $errors->first('zonaSeleccionada','<div class="invalid-feedback">Selecciona una zona.</div>')!!}
                        </div>
                        <div class="col-4" style="margin-top: 30px;">
                            <button type="submit" name="btnSubmit" class="btn btn-outline-success btn-ca">Aplicar</button>
                        </div>
                    </div>

                </form>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Zona seleccionada</label>
                    <input type="text" name="paquete" class="form-control" value="{{$nombreZona}}" readonly>
                </div>
            </div>
            <div class="col-7">
                <button class="btn btn-outline-success btn-block" href="#" data-toggle="modal" data-target="#nuevaColonia" style="margin-top: 30px;">Agregar colonia</button>
            </div>
        </div>
        <form action="{{route('zonafiltrarcolonias', [$idFranquicia, $idZona])}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;" >
            @csrf
            <div class="row" style="margin-top: 20px;">
                <div class="col-4">
                    <label>Filtrar colonias</label>
                    <input type="text" name="filtroColonias" class="form-control" placeholder="Buscar...">
                </div>
                <div class="col-3" style="margin-top: 30px;">
                    <button type="submit" name="btnSubmit" class="btn btn-outline-success btn-ca">Filtrar</button>
                </div>
            </div>
        </form>
        <table id="tablaZonas" class="table-bordered table-striped table-general" style="margin-top: 20px;">
            <thead>
            <tr>
                <th style=" text-align:center;" scope="col">LOCALIDAD</th>
                <th style=" text-align:center;" scope="col">COLONIA</th>
                <th style=" text-align:center;" scope="col">ELIMINAR</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($colonias) && sizeof($colonias) > 0)
                @foreach($colonias as $colonia)
                    <tr>
                        <td align='center'>{{$colonia->localidad}}</td>
                        <td align='center'>{{$colonia->colonia}}</td>
                        <td align='center'><a href="{{route('eliminarcoloniazona',[$idFranquicia, $idZona, $colonia->indice])}}">
                                <button type="button" class="btn btn-outline-danger btn-sm"><i class="fa fa-trash"></i></button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <th align='center' colspan="3">SIN REGISTROS</th>
                </tr>
            @endif
            </tbody>
        </table>
        <div class="row" style="margin-top: 30px;">
            <div class="col-12">
                <a href="{{route('listasfranquicia',$idFranquicia)}}" class="btn btn-outline-success btn-block">@lang('mensajes.volveradminfranquicia')</a>
            </div>
        </div>

        <!--Modal para dar de alta una nueva colonia a una zona-->
        <div class="modal fade" id="nuevaColonia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <form action="{{route('agregarcoloniazona', [$idFranquicia, $idZona])}}" enctype="multipart/form-data"
                  method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #0AA09E; color: #FFFFFF; font-weight: bold;">
                            Agregar nueva colonia
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-10">
                                    <div class="form-group">
                                        <label>Nombre zona</label>
                                        <input type="text" name="paquete" class="form-control" value="{{$nombreZona}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Nueva colonia</label>
                                        <input type="text" name="colonia" class="form-control {!! $errors->first('colonia','is-invalid')!!}" value="{{ old('colonia') }}" required >
                                        {!! $errors->first('colonia','<div class="invalid-feedback">Ingresa el nombre de la colonia.</div>')!!}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Localidad</label>
                                        <input type="text" name="localidad" class="form-control {!! $errors->first('localidad','is-invalid')!!}" value="{{ old('localidad') }}" required>
                                        {!! $errors->first('localidad','<div class="invalid-feedback">Ingresa el nombre de la localidad.</div>')!!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12" style="color: #ea9999;">
                                    <b>Al agregar la colonia aceptas y estas consiente que podran asignar contratos a dicha area.</b>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
                            <button class="btn btn-outline-success btn-ok" name="btnSubmit" type="submit">Agregar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
