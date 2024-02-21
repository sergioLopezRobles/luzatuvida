@extends('layouts.app')
@section('titulo','Contratos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">

        <h2>@lang('mensajes.mensajetitulotablalaboratorio')</h2>

        <form action="{{route('listaconfirmaciones')}}" enctype="multipart/form-data" method="GET">
            <div class="row">
                <div class="col-4">
                    <input name="filtro" type="text" class="form-control" placeholder="Buscar.." id="txtfiltro" value="{{$filtro}}">
                </div>
                <div class="col-5">
                    <button type="submit" class="btn btn-outline-success">Filtrar</button>
                </div>
            </div>
        </form>
        <div class="row mt-2">
            <div class="col-12">
                <i class="bi bi-square-fill" style="color: rgba(255,15,0,0.17); font-size: 20px;"></i> <label>ENVIOS EXPRESS.</label>
            </div>
        </div>
        <table id="tablaContratos" class="table-bordered table-striped table-general table-sm" style="margin-top: 10px;">
            <thead>
            <tr>
                <th style=" text-align:center;" scope="col">SUCURSAL</th>
                <th style=" text-align:center;" scope="col">CONTRATO</th>
                <th style=" text-align:center;" scope="col">USUARIOCREACION</th>
                <th style=" text-align:center;" scope="col">OPTOMETRISTA</th>
                <th style=" text-align:center;" scope="col">CLIENTE</th>
                <th style=" text-align:center;" scope="col">TELEFONO</th>
                <th style=" text-align:center;" scope="col">FECHA CREACION</th>
                <th style=" text-align:center;" scope="col">FECHA REGISTRO</th>
                <th style=" text-align:center;" scope="col">HORA REGISTRO</th>
                <th style=" text-align:center;" scope="col">ESTATUS</th>
                <th style=" text-align:center;" scope="col">VER</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th style=" text-align:center;;background-color:#0AA09E;color:#FFFFFF;" colspan="17">CON COMENTARIOS (CONFIRMACIONES)</th>
            </tr>
            @if(!is_null($contratosConComentarios) && count($contratosConComentarios)>0)
                @foreach($contratosConComentarios as $contratoConComentarios)
                    <tr @if($contratoConComentarios->tienegarantia != null) style="background-color: rgba(255,15,0,0.17)" @endif>
                        <td align='center'>{{$contratoConComentarios->sucursal}}</td>
                        <td align='center'>{{$contratoConComentarios->id}}</td>
                        <td align='center'>{{$contratoConComentarios->usuariocreacion}}</td>
                        <td align='center'>{{$contratoConComentarios->name}}</td>
                        <td align='center'>{{$contratoConComentarios->nombre}}</td>
                        <td align='center'>{{$contratoConComentarios->telefono}}</td>
                        <td align='center'>{{$contratoConComentarios->created_at}}</td>
                        <td align='center'>{{\Carbon\Carbon::parse($contratoConComentarios->fecharegistro)->format('Y-m-d')}}</td>
                        <td align='center'>{{\Carbon\Carbon::parse($contratoConComentarios->fecharegistro)->format('H:i:s')}}</td>
                        @if($contratoConComentarios->estatus_estadocontrato == 1)
                            <td align='center'>
                                <button type="button" class="btn btn-success terminados btn-sm" style="color:#FEFEFE;">{{$contratoConComentarios->descripcion}}</button>
                            </td>
                        @endif
                        @if($contratoConComentarios->estatus_estadocontrato == 9)
                            <td align='center'>
                                <button type="button" class="btn btn-danger enprocesodeaprobacion btn-sm" style="color:#FEFEFE;">{{$contratoConComentarios->descripcion}}</button>
                            </td>
                        @endif
                        <td align='center'><a href="{{route('estadoconfirmacion',[$contratoConComentarios->id])}}">
                                <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endif
            <tr>
                <th style=" text-align:center;;background-color:#0AA09E;color:#FFFFFF;" colspan="17">TODOS (CONFIRMACIONES)</th>
            </tr>

            @if(!is_null($contratosScomentarios) && count($contratosScomentarios)>0)
                @foreach($contratosScomentarios as $contratoScomentarios)
                    <tr @if($contratoScomentarios->tienegarantia != null) style="background-color: rgba(255,15,0,0.17)" @endif>
                        <td align='center'>{{$contratoScomentarios->sucursal}}</td>
                        <td align='center'>{{$contratoScomentarios->id}}</td>
                        <td align='center'>{{$contratoScomentarios->usuariocreacion}}</td>
                        <td align='center'>{{$contratoScomentarios->name}}</td>
                        <td align='center'>{{$contratoScomentarios->nombre}}</td>
                        <td align='center'>{{$contratoScomentarios->telefono}}</td>
                        <td align='center'>{{$contratoScomentarios->created_at}}</td>
                        <td align='center'>{{\Carbon\Carbon::parse($contratoScomentarios->fecharegistro)->format('Y-m-d')}}</td>
                        <td align='center'>{{\Carbon\Carbon::parse($contratoScomentarios->fecharegistro)->format('H:i:s')}}</td>
                        @if($contratoScomentarios->estatus_estadocontrato == 1)
                            <td align='center'>
                                <button type="button" class="btn btn-success terminados btn-sm" style="color:#FEFEFE;">{{$contratoScomentarios->descripcion}}</button>
                            </td>
                        @endif
                        @if($contratoScomentarios->estatus_estadocontrato == 9)
                            <td align='center'>
                                <button type="button" class="btn btn-danger enprocesodeaprobacion btn-sm" style="color:#FEFEFE;">{{$contratoScomentarios->descripcion}}</button>
                            </td>
                        @endif
                        <td align='center'><a href="{{route('estadoconfirmacion',[$contratoScomentarios->id])}}">
                                <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endif
            @if(!is_null($contratosFueraConfimaciones) && count($contratosFueraConfimaciones)>0)
                <tr>
                    <th style=" text-align:center;;background-color:#0AA09E;color:#FFFFFF;" colspan="17">CONTRATOS FUERA DE CONFIRMACIONES</th>
                </tr>
                @foreach($contratosFueraConfimaciones as $contratoFueraConfimaciones)
                    <tr>
                        <td align='center'>{{$contratoFueraConfimaciones->sucursal}}</td>
                        <td align='center'>{{$contratoFueraConfimaciones->id}}</td>
                        <td align='center'>{{$contratoFueraConfimaciones->usuariocreacion}}</td>
                        <td align='center'>{{$contratoFueraConfimaciones->name}}</td>
                        <td align='center'>{{$contratoFueraConfimaciones->nombre}}</td>
                        <td align='center'>{{$contratoFueraConfimaciones->telefono}}</td>
                        <td align='center'>{{$contratoFueraConfimaciones->created_at}}</td>
                        <td align='center'>{{\Carbon\Carbon::parse($contratoFueraConfimaciones->fecharegistro)->format('Y-m-d')}}</td>
                        <td align='center'>{{\Carbon\Carbon::parse($contratoFueraConfimaciones->fecharegistro)->format('H:i:s')}}</td>
                        <td align='center'>
                            <button type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">{{$contratoFueraConfimaciones->descripcion}} </button>
                        </td>
                        <td align='center'><a href="{{route('estadoconfirmacion',[$contratoFueraConfimaciones->id])}}">
                                <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endif
            @if(!is_null($contratosSTerminar) && count($contratosSTerminar)>0)
                @foreach($contratosSTerminar as $contratoSTerminar)
                    <tr>
                        <td align='center'>{{$contratoSTerminar->sucursal}}</td>
                        <td align='center'>{{$contratoSTerminar->id}}</td>
                        <td align='center'>{{$contratoSTerminar->usuariocreacion}}</td>
                        <td align='center'>{{$contratoSTerminar->optometrista}}</td>
                        <td align='center'>{{$contratoSTerminar->nombre}}</td>
                        <td align='center'>{{$contratoSTerminar->telefono}}</td>
                        <td align='center'>{{$contratoSTerminar->created_at}} </td>
                        <td align='center'>{{\Carbon\Carbon::parse($contratoSTerminar->fecharegistro)->format('Y-m-d')}} </td>
                        <td align='center'>{{\Carbon\Carbon::parse($contratoSTerminar->fecharegistro)->format('H:i:s')}} </td>
                        <td align='center'>
                            <button type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">{{$contratoSTerminar->descripcion}} </button>
                        </td>
                        <td align='center'><a href="{{route('estadoconfirmacion',[$contratoSTerminar->id])}}">
                                <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endif

            @if(!is_null($contratosPendientes) && count($contratosPendientes)>0)
                @foreach($contratosPendientes as $contratoPendiente)
                    <tr>
                        <td align='center'>{{$contratoPendiente->sucursal}}</td>
                        <td align='center'>{{$contratoPendiente->id}}</td>
                        <td align='center'>{{$contratoPendiente->usuariocreacion}}</td>
                        <td align='center'>SIN CAPTURAR</td>
                        <td align='center'>SIN CAPTURAR</td>
                        <td align='center'>SIN CAPTURAR</td>
                        <td align='center'>SIN CAPTURAR</td>
                        <td align='center'>SIN CAPTURAR</td>
                        <td align='center'>SIN CAPTURAR</td>
                        <td align='center'>
                            <button type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">SIN CAPTURAR</button>
                        </td>
                        <td align='center'><a href="{{route('estadoconfirmacion',[$contratoPendiente->id])}}">
                                <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
        <hr>
        <div class="row" style="margin-top: 30px;">
            <div class="col-3">
                <button name="btnLaboratorio" id="btnLaboratorio" class="btn btn-outline-primary"></button>
            </div>
            <div class="row col-6" style="margin-top: 5px;">
                <div class="custom-control custom-checkbox" style="margin-right: 30px;">
                    <input type="checkbox"
                           class="custom-control-input"
                           name="cbAprobados" id="cbAprobados"
                           value="1" checked>
                    <label class="custom-control-label" for="cbAprobados">Aprobados</label>
                </div>
                <div class="custom-control custom-checkbox" style="margin-right: 30px;">
                    <input type="checkbox"
                           class="custom-control-input"
                           name="cbManofactura" id="cbManofactura">
                    <label class="custom-control-label" for="cbManofactura">Manofactura</label>
                </div>
                <div class="custom-control custom-checkbox" style="margin-right: 30px;">
                    <input type="checkbox"
                           class="custom-control-input"
                           name="cbProcesoEnvio" id="cbProcesoEnvio">
                    <label class="custom-control-label" for="cbProcesoEnvio">Proceso de envio</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox"
                           class="custom-control-input"
                           name="cbComentarios" id="cbComentario">
                    <label class="custom-control-label" for="cbComentario">Con Comentarios</label>
                </div>
            </div>
            <div class="col-1" id="spCargando">
                <div class="d-flex justify-content-center">
                    <div class="spinner-border" style="width: 2rem; height: 2rem;" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group" id="tablalaboratorio">

        </div>

        <hr>
        <div class="row" style="margin-top: 30px;">
            <div class="col-3">
                <button name="btnGarantias" id="btnGarantias" class="btn btn-outline-primary"></button>
            </div>
            <div class="col-1" id="spCargando2">
                <div class="d-flex justify-content-center">
                    <div class="spinner-border" style="width: 2rem; height: 2rem;" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group" id="tablagarantias">

        </div>

        <hr>
        <div class="row" style="margin-top: 30px;">
            <div class="col-3">
                <button name="btnRechazados" id="btnRechazados" class="btn btn-outline-primary"></button>
            </div>
            <div class="col-1" id="spCargando3">
                <div class="d-flex justify-content-center">
                    <div class="spinner-border" style="width: 2rem; height: 2rem;" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3" id="formFechaSeleccionar">
            <div class="col-3">
                <div class="form-group">
                    <label>Selecciona un dia de la semana </label>
                    <input type="date" name="fechaIni" id="fechaIni" class="form-control {!! $errors->first('fechaIni','is-invalid')!!}">
                    <input type="text" name="periodoAsistencia" id="periodoAsistencia" class="form-control"  readonly value=""
                           style="margin-top: 25px;">
                    @if($errors->has('fechaIni'))
                        <div class="invalid-feedback">{{$errors->first('fechaIni')}}</div>
                    @endif
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-outline-success btn-block" id="btnCargarContratosRechazadosConfirmaciones">Aplicar</button>
                </div>
            </div>
        </div>
        <div class="form-group" id="tablarechazados">

        </div>

    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
