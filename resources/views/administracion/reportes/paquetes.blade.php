@extends('layouts.app')
@section('titulo','Reporte paquetes'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2 style="text-align: left; color: #0AA09E">Paquetes</h2>
    <form action="{{route('listacontratospaquetes', $idFranquicia)}}"
          enctype="multipart/form-data" method="GET"
          onsubmit="btnSubmit.disabled = true;">
        @csrf
        <div class="row">
            <div class="col-2">
                <label for="idUsuario">Usuario</label>
                <select name="idUsuario"
                        id="idUsuario"
                        class="form-control">
                    @if(count($usuariosVentas) > 0)
                        <option value="" selected>Seleccionar..</option>
                        @foreach($usuariosVentas as $usuario)
                            <option
                                value="{{$usuario->id}}" @if($usuario != null && $idUsuario == $usuario->id)  selected @endif >{{$usuario->rol}} - {{$usuario->nombre}}</option>
                        @endforeach
                    @else
                        <option selected>Sin registros</option>
                    @endif
                </select>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Selecciona un dia de la semana </label>
                    <input type="date" name="fechaIni" id="fechaIni" class="form-control {!! $errors->first('fechaIni','is-invalid')!!}" @isset($fechaIni) value = "{{$fechaIni}}" @endisset>
                    <input type="text" name="periodoAsistencia" id="periodoAsistencia" class="form-control"  readonly value=""
                           style="margin-top: 25px;">
                    @if($errors->has('fechaIni'))
                        <div class="invalid-feedback">{{$errors->first('fechaIni')}}</div>
                    @endif
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <div class="custom-control custom-checkbox" style="margin-top: 38px">
                        <input type="checkbox"
                               class="custom-control-input"
                               name="cbFiltro" id="cbFiltro">
                        <label class="custom-control-label" for="cbFiltro" id="lblFiltro">Filtrar por poliza/fecha: Poliza</label>
                    </div>
                    <div class="custom-control custom-checkbox" style="margin-top: 38px">
                        <input type="checkbox"
                               class="custom-control-input"
                               name="cbOrdenarPaquete" id="cbOrdenarPaquete">
                        <label class="custom-control-label" for="cbOrdenarPaquete">Ordenar por paquete</label>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-outline-success btn-block" name="btnCargarAsistencia" id="btnCargarContratos">Aplicar</button>
                </div>
            </div>
            <div class="col-1" id="spCargando">
                <div class="d-flex justify-content-center">
                    <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 30px;" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-12">
            <button type="button" class="btn btn-primary" style="margin-bottom: 10px; margin-right: 20px;">
                Lectura <span class="badge bg-secondary" id="lectura">0</span>
            </button>
            <button type="button" class="btn btn-primary" style="margin-bottom: 10px; margin-right: 20px;">
                Proteccion <span class="badge bg-secondary" id="proteccion">0</span>
            </button>
            <button type="button" class="btn btn-primary" style="margin-bottom: 10px; margin-right: 20px;">
                Eco JR <span class="badge bg-secondary" id="ecojr">0</span>
            </button>
            <button type="button" class="btn btn-primary" style="margin-bottom: 10px; margin-right: 20px;">
                JR <span class="badge bg-secondary" id="jr">0</span>
            </button>
            <button type="button" class="btn btn-primary" style="margin-bottom: 10px; margin-right: 20px;">
                Dorado 1 <span class="badge bg-secondary" id="dorado1">0</span>
            </button>
            <button type="button" class="btn btn-primary" style="margin-bottom: 10px; margin-right: 20px;">
                Dorado 2 <span class="badge bg-secondary" id="dorado2">0</span>
            </button>
            <button type="button" class="btn btn-primary" style="margin-bottom: 10px; margin-right: 20px;">
                Platino <span class="badge bg-secondary" id="platino">0</span>
            </button>
            <button type="button" class="btn btn-primary" style="margin-bottom: 10px; margin-right: 20px;">
                Premium <span class="badge bg-secondary" id="premium">0</span>
            </button>
            <button type="button" class="btn btn-danger" style="margin-bottom: 10px; margin-right: 20px; background-color: #FFACA6; border-color: #FFACA6;">
                Rechazado <span class="badge bg-secondary" id="rechazado">0</span>
            </button>
            <button type="button" class="btn btn-danger" style="margin-bottom: 10px; margin-right: 20px; background-color: #FFACA6; border-color: #FFACA6;">
                Cancelado/Lio/Fuga <span class="badge bg-secondary" id="cancelado_lio_fuga">0</span>
            </button>
        </div>
    </div>
    <div class="row" style="margin-top: 10px;" id="divBtnTotalRegistros">
        <button type="button" class="btn btn-primary" style="margin-bottom: 10px;">
            Total registros <span class="badge bg-secondary" id="totalContratos">0</span>
        </button>
        <a href="#" id="btnExportarExcel" onclick="exportarAExcel('Reporte Paquetes','tablaReportePaquetes');" style="text-decoration:none; color:black; padding-left: 15px;">
            <button type="button" class="btn btn-success"> Exportar </button>
        </a>
    </div>
    <div class="contenedortblReportes" style="overflow-x:auto;">
        <table class="table table-bordered table-striped table-general table-sm" id="tablaReportePaquetes">
            <thead>
            <tr>
                <th scope="col">POLIZA</th>
                <th scope="col">NOMBRE</th>
                <th scope="col">SUCURSAL</th>
                <th scope="col">FECHA DE CREACION</th>
                <th scope="col">CONTRATO</th>
                <th scope="col">ESTATUS</th>
                <th scope="col">PAQUETE</th>
                <th scope="col">FOTOCROMATICO</th>
                <th scope="col">AR</th>
                <th scope="col">TINTE</th>
                <th scope="col">BLUERAY</th>
                <th scope="col">OTRO</th>
                <th scope="col">TOTAL</th>
            </tr>
            </thead>
            <tbody id="tblpaquetes">

            </tbody>
        </table>
    </div>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
