@extends('layouts.app')
@section('titulo','Campañas'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>Campañas</h2>
        <form id="frmCrearVehiculo" action="{{route('crearcampania',$idFranquicia)}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Titulo de campaña</label>
                        <input type="text" name="titulo" id="titulo" class="form-control {!! $errors->first('titulo','is-invalid')!!}" placeholder="Titulo de campaña"
                               value="{{old('titulo')}}">
                        {!! $errors->first('titulo','<div class="invalid-feedback">Agerga el titulo de la campaña.</div>')!!}
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Foto de campaña (JPG)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input {!! $errors->first('foto','is-invalid')!!}" name="foto" id="foto"
                                   accept="image/jpg">
                            <label class="custom-file-label" for="foto">Choose file...</label>
                            {!! $errors->first('foto','<div class="invalid-feedback">Foto de campaña obligatoria en formato JPG de tamaño maximo 1MB..</div>')!!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Fecha inicio</label>
                        <input type="date" name="fechaInicio" id="fechaInicio"
                               class="form-control {!! $errors->first('fechaInicio','is-invalid')!!}" value="{{old('fechaInicio')}}">
                        @if($errors->has('fechaInicio'))
                            <div class="invalid-feedback">{{$errors->first('fechaInicio')}}</div>
                        @endif
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Fecha fin</label>
                        <input type="date" name="fechaFin" id="fechaFin"
                               class="form-control {!! $errors->first('fechaFin','is-invalid')!!}" value="{{old('fechaFin')}}">
                        @if($errors->has('fechaFin'))
                            <div class="invalid-feedback">{{$errors->first('fechaFin')}}</div>
                        @endif
                    </div>
                </div>
                <div class="col-4">
                    <div class="custom-control custom-switch" style="margin-top: 40px;">
                        <input type="checkbox" class="custom-control-input" id="swReferenciaAutomatica" name="swReferenciaAutomatica" value="1">
                        <label class="custom-control-label" for="swReferenciaAutomatica">Generar numero de referencia automaticamente.</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea  rows="3" name="observaciones" id="observaciones" class="form-control" placeholder="Observaciones"> {{old('Observaciones')}} </textarea>
                    </div>
                </div>
            </div>
            <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">Crear campaña</button>
        </form>
        <hr>
        <table id="tablaCampañas" class="table-bordered table-striped table-general table-sm">
            <thead>
            <tr>
                <th  style =" text-align:center;" scope="col">CAMPAÑA</th>
                <th  style =" text-align:center;" scope="col">ESTADO</th>
                <th  style =" text-align:center;" scope="col">CODIGO</th>
                <th  style =" text-align:center;" scope="col">FORMA DE REFERENCIA</th>
                <th  style =" text-align:center;" scope="col">TITULO</th>
                <th  style =" text-align:center;" scope="col">FECHA INICIO</th>
                <th  style =" text-align:center;" scope="col">FECHA FINAL</th>
                <th  style =" text-align:center;" scope="col">OBSERVACIONES</th>
                <th  style =" text-align:center;" scope="col">VER</th>
                <th  style =" text-align:center;" scope="col">EDITAR</th>
                <th  style =" text-align:center;" scope="col">ELIMINAR</th>
            </tr>
            </thead>
            <tbody>
            @if(count($listaCampanias) > 0)

                @foreach($listaCampanias as $campania)
                    <tr>
                        <th style="font-size: 11px; text-align: center; vertical-align: middle">{{$indice = $indice - 1}}</th>
                        @if($campania->estado  == 1)
                            <td align='center'><i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i></td>
                        @else
                            <td align='center'><i class='fas fa-times' style="color:#ffaca6;font-size:25px;"></i></td>
                        @endif
                        <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$campania->id}}</td>
                        @if($campania->tiporeferencia == 1)
                            <td style="font-size: 11px; text-align: center; vertical-align: middle">AUTOMÁTICO</td>
                        @else
                            <td style="font-size: 11px; text-align: center; vertical-align: middle">MANUAL</td>
                        @endif
                        <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$campania->titulo}}</td>
                        <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$campania->fechainicio}}</td>
                        <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$campania->fechafinal}}</td>
                        <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$campania->observaciones}}</td>
                        <td style="font-size: 11px; text-align: center; vertical-align: middle"><a class="btn btn-outline-success btn-sm" href="{{route('vercampania',[$idFranquicia, $campania->id])}}">
                                <i class="bi bi-eye-fill"></i></a>
                        </td>
                        <td style="font-size: 11px; text-align: center; vertical-align: middle"><a class="btn btn-outline-success btnActualizarCampania btn-sm" data-toggle="modal" data-target="#modalActualizarCampania"
                                         data_parametros_modal="{{$campania->id. "," . $campania->titulo . "," . $campania->fechainicio . "," .
                                                                  $campania->fechafinal . "," . $campania->observaciones . "," . $campania->tiporeferencia . "," . $idFranquicia}}">
                                <i class="bi bi-pencil-fill"></i></a>
                        </td>
                        <td style="font-size: 11px; text-align: center; vertical-align: middle"><a class="btn btn-outline-danger btnEliminarCampania btn-sm" data-toggle="modal" data-target="#modalEliminarCampaña"
                                         data_parametros_modal="{{$campania->id . "," . $idFranquicia}}">
                                <i class="bi bi-trash3-fill"></i></a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td align='center' colspan="10" style="font-size: 10px;">Sin registros</td>
                </tr>
            @endif
            </tbody>
        </table>

        <!-- Modal de confirmacion para actualizar campaña-->
        <div class="modal fade" id="modalActualizarCampania"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  aria-hidden="true">
            <form action="{{route('actualizarcampania')}}" enctype="multipart/form-data"
                  method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #0AA09E; color: white;">
                            <h5> Actualizar campaña</h5>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" class="form-control" name="idCampaniaModal" id="idCampaniaModal">
                            <input type="hidden" class="form-control" name="idFranquicia" id="idFranquicia">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Foto de campaña (JPG)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input {!! $errors->first('fotoModal','is-invalid')!!}" name="fotoModal" id="fotoModal"
                                                   accept="image/jpg">
                                            <label class="custom-file-label" for="fotoModal">Choose file...</label>
                                            {!! $errors->first('fotoModal','<div class="invalid-feedback">Foto de campaña obligatoria en formato JPG de tamaño maximo 1MB..</div>')!!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="custom-control custom-switch" style="margin-top: 40px; display: flex; justify-content: center; align-items: center;">
                                        <input type="checkbox" class="custom-control-input" id="swReferenciaAutomaticaActualizar" name="swReferenciaAutomaticaActualizar" value="1">
                                        <label class="custom-control-label" for="swReferenciaAutomaticaActualizar">Generar numero de referencia automaticamente.</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Titulo de campaña</label>
                                        <input type="text" name="tituloModal" id="tituloModal" class="form-control {!! $errors->first('tituloModal','is-invalid')!!}" placeholder="Titulo de campaña"
                                               value="{{old('tituloModal')}}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Fecha inicio</label>
                                        <input type="date" name="fechaInicioModal" id="fechaInicioModal"
                                               class="form-control {!! $errors->first('fechaInicioModal','is-invalid')!!}" value="{{old('fechaInicioModal')}}">
                                        @if($errors->has('fechaInicioModal'))
                                            <div class="invalid-feedback">{{$errors->first('fechaInicioModal')}}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Fecha fin</label>
                                        <input type="date" name="fechaFinModal" id="fechaFinModal"
                                               class="form-control {!! $errors->first('fechaFinModal','is-invalid')!!}" value="{{old('fechaFinModal')}}">
                                        @if($errors->has('fechaFinModal'))
                                            <div class="invalid-feedback">{{$errors->first('fechaFinModal')}}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Observaciones</label>
                                        <textarea  rows="3" name="observacionesModal" id="observacionesModal" class="form-control" style="text-transform: uppercase"
                                                   placeholder="Observaciones"> {{old('ObservacionesModal')}} </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
                            <button class="btn btn-outline-success btn-ok" name="btnSubmit" type="submit">Actualizar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal de confirmacion para eliminar campaña-->
        <div class="modal fade" id="modalEliminarCampaña" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <form action="{{route('eliminarcampania')}}" enctype="multipart/form-data"
                  method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #0AA09E; color: white;"><b>Eliminar campaña</b></div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    ¿Estas seguro que quieres eliminar la campaña?
                                </div>
                                <input type="hidden" name="idCampania"/>
                                <input type="hidden" name="idFranquicia"/>
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
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
