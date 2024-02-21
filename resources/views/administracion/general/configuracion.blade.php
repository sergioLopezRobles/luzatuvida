@extends('layouts.app')
@section('titulo','Configuracion'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2 style="text-align: left; color: #0AA09E">Configuración aplicación movil</h2>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="ftp-tab" data-toggle="tab" href="#ftp" role="tab" aria-controls="ftp"
                   aria-selected="true">FTP</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tema-tab" data-toggle="tab" href="#tema" role="tab" aria-controls="tema"
                   aria-selected="false">TEMA</a>
            </li>
        </ul>

        <div class="tab-content" style="margin-top:30px;">
            <div class="tab-pane active" id="ftp" role="tabpanel" aria-labelledby="ftp-tab">
                <form action="{{route('actualizarconfiguracionftp')}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                    @csrf
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label>Ruta</label>
                                <input type="text" name="ruta_ftp" class="form-control" placeholder="Ruta"
                                       @isset($datosFtp[0]->ruta_ftp) value="{{$datosFtp[0]->ruta_ftp}}" @endisset>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>Usuario</label>
                                <input type="text" name="usuario_ftp" class="form-control" placeholder="Usuario"
                                       @isset($datosFtp[0]->usuario_ftp) value="{{$datosFtp[0]->usuario_ftp}}" @endisset>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>Contraseña</label>
                                <input type="text" name="contrasena_ftp" class="form-control" placeholder="Contraseña"
                                       @isset($datosFtp[0]->contrasena_ftp) value="{{$datosFtp[0]->contrasena_ftp}}" @endisset>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label>Precio dolar</label>
                                <input type="text" name="preciodolar" class="form-control" placeholder="Precio dolar"
                                       @isset($datosFtp[0]->preciodolar) value="{{$datosFtp[0]->preciodolar}}" @endisset>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 40px;">
                        <div class="col-12">
                            <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">Actualizar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane" id="tema" role="tabpanel" aria-labelledby="tema-tab">
                <form id="frmConfiguracionMovil" action="{{route('actualizarConfiguracion')}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                    @csrf
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-3">
                            <div class="form-group">
                                <label>Logotipo (FOTO PNG)</label>
                                <input type="file" id="imagenLogo" name="imagenLogo" class="form-control-file {!! $errors->first('imagenLogo','is-invalid')!!}" accept="image/png">
                                {!! $errors->first('imagenLogo','<div class="invalid-feedback">La foto debera estar en formato png.</div>')!!}
                            </div>
                            <div style="size: 8px; color: #ea9999;">Tamaño de imagen recomendada (500 x 163 px)</div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label>Color iconos</label>
                                <input type="color" name="icono" id="icono" class="form-control form-control-color{!! $errors->first('icono','is-invalid')!!}" @isset($configuracionActual[0]->coloriconos)
                                value="{{$configuracionActual[0]->coloriconos}}" @endisset>
                                {!! $errors->first('icono','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label>Color encabezados</label>
                                <input type="color" name="encabezados" id="encabezados" class="form-control form-control-color{!! $errors->first('encabezados','is-invalid')!!}"
                                       @isset($configuracionActual[0]->colorencabezados) value="{{$configuracionActual[0]->colorencabezados}}" @endisset>
                                {!! $errors->first('encabezados','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label>Color barra de navegación</label>
                                <input type="color" name="navbar" id="navbar" class="form-control form-control-color{!! $errors->first('navbar','is-invalid')!!}"
                                       @isset($configuracionActual[0]->colornavbar) value="{{$configuracionActual[0]->colornavbar}}" @endisset>
                                {!! $errors->first('navbar','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="custom-control custom-checkbox" style="margin-top: 35px;">
                                <input type="checkbox" class="custom-control-input" name="cbDefault" id="cbDefault" value="1">
                                <label class="custom-control-label" for="cbDefault">Configuracion predeterminada</label>
                            </div>
                        </div>
                    </div>
                    @if($configuracionActual != null && $configuracionActual[0]->fotologo != null)
                        <div class="row" style="margin-top: 15px; max-height: 200px;">
                            <div class="col-3" style="display: flex; justify-content: center;">
                                <img src="{{asset($configuracionActual[0]->fotologo)}}" style="width: 90%; height: 90%;" class="img-thumbnail">
                            </div>
                        </div>
                    @endif
                    <div class="row" style="margin-top: 40px;">
                        <div class="col-12">
                            <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">Actualizar configuración</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
