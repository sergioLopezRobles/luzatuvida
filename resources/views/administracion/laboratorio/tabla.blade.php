@extends('layouts.app')
@section('titulo','Contratos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">

    <h2>@lang('mensajes.mensajetitulotablalaboratorio')</h2>

    <form action="{{route('listalaboratorio')}}" enctype="multipart/form-data" method="GET"
          onsubmit="btnSubmit.disabled = true;">
        <div class="row">
            <div class="col-4">
                <input name="filtro" id="filtro" type="text" class="form-control" placeholder="Buscar.." value="{{$filtro}}">
            </div>
            <div class="col-5">
                <button type="submit" name="btnSubmit" class="btn btn-outline-success">Filtrar</button>
            </div>
        </div>
    </form>

    <form action="{{route('actualizarestadoenviado')}}" enctype="multipart/form-data" method="GET"
          onsubmit="btnSubmit.disabled = true;">
        <div class="row mt-2">
            <div class="col-5">
                <i class="bi bi-square-fill" style="color: rgba(255,15,0,0.17); font-size: 20px;"></i> <label>ENVIOS EXPRESS.</label>
            </div>
            <div class="col-4"></div>
            <div class="col-3">
                <button type="submit" name="btnSubmit" class="btn btn-outline-success btn-block">ENVIAR TODOS</button>
            </div>
        </div>
        <table id="tablaContratos" class="table-bordered table-striped table-general table-sm" style="margin-top: 10px; margin-bottom: 10px;">
            <thead>
            <tr>
                <th style=" text-align:center;" scope="col">CREADO EL</th>
                <th style=" text-align:center;" scope="col">FECHA DE ENTREGA</th>
                <th style=" text-align:center;" scope="col">CONTRATO</th>
                <th style=" text-align:center;" scope="col">SUCURSAL</th>
                <th style=" text-align:center;" scope="col">FECHA DE MANUFACTURA</th>
                <th style=" text-align:center;" scope="col">ESTATUS</th>
                <th style=" text-align:center;" scope="col">VER</th>
                <th style=" text-align:center;" scope="col">SELECCIONAR</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th style=" text-align:center;;background-color:#0AA09E;color:#FFFFFF;" colspan="17">CON COMENTARIOS (LABORATORIO)
                </th>
            </tr>
            @foreach($contratosComentarios as $contratoComentarios)
                <tr @if($contratoComentarios->tienegarantia != null) style="background-color: rgba(255,15,0,0.17)" @endif>
                    <td align='center'>{{$contratoComentarios->created_at}}</td>
                    <td align='center'>{{$contratoComentarios->fechaentrega}}</td>
                    <td align='center'>{{$contratoComentarios->id}}</td>
                    <td align='center'>{{$contratoComentarios->ciudad}}</td>
                    <td align='center'>{{$contratoComentarios->ultimoestatusmanufactura}}</td>

                    @if($contratoComentarios->estatus_estadocontrato == 7)
                        <td align='center'>
                            <button type="button" class="btn btn-primary aprobado btn-sm"
                                    style="color:#FEFEFE;">{{$contratoComentarios->descripcion}}</button>
                        </td>
                    @endif
                    @if($contratoComentarios->estatus_estadocontrato == 10)
                        <td align='center'>
                            <button type="button" class="btn btn-warning manofactura btn-sm"
                                    style="color:#FEFEFE;">{{$contratoComentarios->descripcion}}</button>
                        </td>
                    @endif
                    @if($contratoComentarios->estatus_estadocontrato == 11)
                        <td align='center'>
                            <button type="button" class="btn btn-info enprocesodeenvio btn-sm"
                                    style="color:#FEFEFE;">{{$contratoComentarios->descripcion}}</button>
                        </td>
                    @endif
                    <td align='center'><a href="{{route('estadolaboratorio',$contratoComentarios->id)}}">
                            <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button>
                        </a></td>

                </tr>
            @endforeach
            <tr>
                <th style=" text-align:center;;background-color:#0AA09E;color:#FFFFFF;" colspan="17">TODOS (LABORATORIO)</th>
            </tr>
            @foreach($contratosSComentariosLabo as $contratoSComentariosLabo)
                <tr @if($contratoSComentariosLabo->tienegarantia != null) style="background-color: rgba(255,15,0,0.17)" @endif>
                    <td align='center'>{{$contratoSComentariosLabo->created_at}}</td>
                    <td align='center'>{{$contratoSComentariosLabo->fechaentrega}}</td>
                    <td align='center'>{{$contratoSComentariosLabo->id}}</td>
                    <td align='center'>{{$contratoSComentariosLabo->ciudad}}</td>
                    <td align='center'>{{$contratoSComentariosLabo->ultimoestatusmanufactura}}</td>

                    @if($contratoSComentariosLabo->estatus_estadocontrato == 7)
                        <td align='center'>
                            <button type="button" class="btn btn-primary aprobado btn-sm"
                                    style="color:#FEFEFE;">{{$contratoSComentariosLabo->descripcion}}</button>
                        </td>
                    @endif
                    @if($contratoSComentariosLabo->estatus_estadocontrato == 10)
                        <td align='center'>
                            <button type="button" class="btn btn-warning manofactura btn-sm"
                                    style="color:#FEFEFE;">{{$contratoSComentariosLabo->descripcion}}</button>
                        </td>
                    @endif
                    @if($contratoSComentariosLabo->estatus_estadocontrato == 11)
                        <td align='center'>
                            <button type="button" class="btn btn-info enprocesodeenvio btn-sm"
                                    style="color:#FEFEFE;">{{$contratoSComentariosLabo->descripcion}}</button>
                        </td>
                    @endif
                    <td align='center'><a href="{{route('estadolaboratorio',$contratoSComentariosLabo->id)}}">
                            <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button>
                        </a></td>

                    @if($contratoSComentariosLabo->estatus_estadocontrato == 11)
                        <td align='center'>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       name="check{{$contratoSComentariosLabo->id}}"
                                       id="customCheck{{$contratoSComentariosLabo->id}}">
                                <label class="custom-control-label"
                                       for="customCheck{{$contratoSComentariosLabo->id}}"></label>
                            </div>
                        </td>
                    @endif
                </tr>
            @endforeach
            @if((!is_null($otrosContratos) && count($otrosContratos)>0) || (!is_null($contratosSTerminar) && count($contratosSTerminar)>0) || (!is_null($contratosPendientes) &&
                count($contratosPendientes)>0))
                <tr>
                    <th style=" text-align:center;;background-color:#0AA09E;color:#FFFFFF;" colspan="17"> CONTRATOS FUERA DE LABORATORIO </th>
                </tr>
            @endif
            @if(!is_null($otrosContratos) && count($otrosContratos)>0)
                @foreach($otrosContratos as $otroContrato)
                    <tr>
                        <td align='center'>{{$otroContrato->created_at}}</td>
                        <td align='center'>{{$otroContrato->fechaentrega}}</td>
                        <td align='center'>{{$otroContrato->id}}</td>
                        <td align='center'>{{$otroContrato->ciudad}}</td>
                        <td align='center'>{{$otroContrato->ultimoestatusmanufactura}}</td>
                        <td align='center'>
                                <button type="button" class="btn btn-secondary btn-sm"
                                        style="color:#FEFEFE;">{{$otroContrato->descripcion}}</button>
                        </td>
                        <td align='center'><a href="{{route('estadolaboratorio',$otroContrato->id)}}">
                                <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button></a>
                        </td>
                    </tr>
                @endforeach
            @endif
            @if(!is_null($contratosSTerminar) && count($contratosSTerminar)>0)
                @foreach($contratosSTerminar as $contratoSTerminar)
                    <tr>
                        <td align='center'>{{$contratoSTerminar->created_at}}</td>
                        <td align='center'>SIN CAPTURAR</td>
                        <td align='center'>{{$contratoSTerminar->id}}</td>
                        <td align='center'>{{$contratoSTerminar->ciudad}}</td>
                        <td align='center'>
                            <button type="button" class="btn btn-secondary btn-sm"
                                    style="color:#FEFEFE;">{{$contratoSTerminar->descripcion}}</button>
                        </td>
                        <td align='center'>
                            <button type="button" class="btn btn-outline-success btn-sm" disabled="disabled"><i class="fas fa-pen"></i></button>
                        </td>
                    </tr>
                @endforeach
            @endif
            @if(!is_null($contratosPendientes) && count($contratosPendientes)>0)
                @foreach($contratosPendientes as $contratoPendiente)
                    <tr>
                        <td align='center'>SIN CAPTURAR</td>
                        <td align='center'>SIN CAPTURAR</td>
                        <td align='center'>{{$contratoPendiente->id}}</td>
                        <td align='center'>{{$contratoPendiente->ciudad}}</td>
                        <td align='center'>
                            <button type="button" class="btn btn-secondary btn-sm"
                                    style="color:#FEFEFE;">SIN CAPTURAR</button>
                        </td>
                        <td align='center'>
                            <button type="button" class="btn btn-outline-success btn-sm" disabled="disabled"><i class="fas fa-pen"></i></button>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </form>
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
                        @csrf
                        <div class="row">
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Fecha inicial</label>
                                    <input type="date" name="fechainibuscar" id="fechainibuscar" class="form-control {!! $errors->first('fechainibuscar','is-invalid')!!}"
                                           @isset($fechainibuscar) value = "{{$fechainibuscar}}" @endisset>
                                    @if($errors->has('fechainibuscar'))
                                        <div class="invalid-feedback">{{$errors->first('fechainibuscar')}}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Fecha final</label>
                                    <input type="date" name="fechafinbuscar" id="fechafinbuscar" class="form-control {!! $errors->first('fechafinbuscar','is-invalid')!!}"
                                           @isset($fechafinbuscar) value = "{{$fechafinbuscar}}" @endisset>
                                    @if($errors->has('fechafinbuscar'))
                                        <div class="invalid-feedback">{{$errors->first('fechafinbuscar')}}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-3">
                                <label for="franquiciaSeleccionada">Sucursal</label>
                                <div class="form-group">
                                    <select name="franquiciaSeleccionada"
                                            id="franquiciaSeleccionada"
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
                            <div class="col-3" id="spCargando">
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

    <table id="tablaContratos" class="table-bordered table-striped table-general table-sm" style="margin-top: 10px;">
        <thead>
        <tr>
            <th style=" text-align:center;" scope="col">CREADO EL</th>
            <th style=" text-align:center;" scope="col">FECHA DE ENTREGA</th>
            <th style=" text-align:center;" scope="col">FECHA DE ENVIO</th>
            <th style=" text-align:center;" scope="col">CONTRATO</th>
            <th style=" text-align:center;" scope="col">SUCURSAL</th>
            <th style=" text-align:center;" scope="col">FECHA DE MANUFACTURA</th>
            <th style=" text-align:center;" scope="col">ESTATUS</th>
            <th style=" text-align:center;" scope="col">VER</th>
        </tr>
        </thead>
        <tbody id="tablaContratosEnviados">
        </tbody>
    </table>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
