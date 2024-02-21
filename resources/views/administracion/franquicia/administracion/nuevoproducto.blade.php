@extends('layouts.app')
@section('titulo','Nuevo producto'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>@lang('mensajes.mensajenuevoproducto')</h2>
        <form id="frmFranquiciaNueva" action="{{route('productocrear',$idFranquicia)}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Producto</label>
                        <input type="text" name="producto" class="form-control {!! $errors->first('producto','is-invalid')!!}" placeholder="Nombre del producto"
                               value="{{ old('producto') }}">
                        {!! $errors->first('producto','<div class="invalid-feedback">El nombre del producto es obligatorio.</div>')!!}
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Número de piezas</label>
                        <input type="number" name="piezas" class="form-control {!! $errors->first('piezas','is-invalid')!!}" min="0" placeholder="Numero de piezas"
                               value="{{ old('piezas') }}">
                        {!! $errors->first('piezas','<div class="invalid-feedback">El numero de piezas son obligatorias y/o un numero positivo.</div>')!!}
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Precio</label>
                        <input type="number" name="precio" class="form-control {!! $errors->first('precio','is-invalid')!!}" min="0" placeholder="Precio del producto"
                               value="{{ old('precio') }}">
                        {!! $errors->first('precio','<div class="invalid-feedback">El precio es obligatorio y debera ser mayor a cero.</div>')!!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <div class="form-group">
                        <label>Subir foto</label>
                        <input type="file" name="foto" class="form-control-file  {!! $errors->first('foto','is-invalid')!!}" accept="image/jpg">
                        {!! $errors->first('foto','<div class="invalid-feedback">La foto debera estar en formato jpg.</div>')!!}
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Color</label>
                        <input type="text" name="color" class="form-control {!! $errors->first('color','is-invalid')!!}" placeholder="Color del producto"
                               value="{{ old('color') }}">
                        {!! $errors->first('color','<div class="invalid-feedback">El color del producto es obligatorio.</div>')!!}
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label for="">Tipo de producto</label>
                        <select name="tipoproducto" class="form-control {!! $errors->first('tipoproducto','is-invalid')!!}" placeholder="tipo de producto"
                                value="{{ old('tipoproducto',0) }}">
                            <option selected value=0>Seleccionar</option>
                            @foreach($tipoproducto as $tipo)
                                <option value="{{$tipo->id}}">{{$tipo->tipo}}</option>
                            @endforeach
                        </select>
                        {!! $errors->first('tipoproducto','<div class="invalid-feedback">Elegir un tipo de producto, campo obligatorio </div>')!!}
                    </div>
                </div>
                <div class="col-2">
                    <label>Estado</label>
                    <div class="form-check">
                        <input type="checkbox" name="estado" class="form-check-input" checked value="1">
                        <label class="form-check-label" for="estado">Activo/Inactivo</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-check" style="margin-top: 30px;">
                        <input type="checkbox" name="premium" class="form-check-input" value="1">
                        <label class="form-check-label" for="premium">Premium</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <div class="form-group">
                        <label>Gasto en póliza (Administración)</label>
                        <input type="number" name="polizagastosadministracion" class="form-control {!! $errors->first('polizagastosadministracion','is-invalid')!!}" min="0" value="0"
                               placeholder="Gasto en poliza" value="{{ old('polizagastosadministracion') }}">
                        {!! $errors->first('polizagastosadministracion','<div class="invalid-feedback">El gasto de poliza administración es obligatorio y deberá ser mayor a cero.</div>')!!}
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Gasto en póliza (Asist/Opto)</label>
                        <input type="number" name="polizagastos" class="form-control {!! $errors->first('polizagastos','is-invalid')!!}" min="0" value="0"
                               placeholder="Gasto en poliza" value="{{ old('polizagastos') }}">
                        {!! $errors->first('polizagastos','<div class="invalid-feedback">El gasto de poliza asistente/optometrista es obligatorio y deberá ser mayor a cero.</div>')!!}
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Gasto en póliza (Cobranza)</label>
                        <input type="number" name="polizagastoscobranza" class="form-control {!! $errors->first('polizagastoscobranza','is-invalid')!!}" min="0" value="0"
                               placeholder="Gasto en poliza" value="{{ old('polizagastoscobranza') }}">
                        {!! $errors->first('polizagastoscobranza','<div class="invalid-feedback">El gasto de poliza cobranza es obligatorio y deberá ser mayor a cero.</div>')!!}
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label>Fecha de inicio de la promoción</label>
                        <input type="date" name="iniciop" class="form-control {!! $errors->first('iniciop','is-invalid')!!}" placeholder="Fecha de inicio"
                               value="{{ old('iniciop') }}">
                        @if($errors->has('iniciop'))
                            <div class="invalid-feedback">{{$errors->first('iniciop')}}</div>
                        @endif
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Fecha final de la promoción</label>
                        <input type="date" name="finp" class="form-control {!! $errors->first('finp','is-invalid')!!}" placeholder="Fecha de inicio" value="{{ old('finp') }}">
                        @if($errors->has('finp'))
                            <div class="invalid-feedback">{{$errors->first('finp')}}</div>
                        @endif
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Precio con promoción</label>
                        <input type="number" name="preciop" class="form-control {!! $errors->first('preciop','is-invalid')!!}" min="0" placeholder="Precio promoción"
                               value="{{ old('preciop') }}">
                        @if($errors->has('preciop'))
                            <div class="invalid-feedback">{{$errors->first('preciop')}}</div>
                        @endif
                    </div>
                </div>
                <div class="col-2">
                    <label> Estado promoción</label>
                    <div class="form-check">
                        <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1">
                        <label class="form-check-label" for="activo">Activo/Inactivo</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <a href="{{route('listasfranquicia',$idFranquicia)}}" class="btn btn-outline-success btn-block">@lang('mensajes.regresar')</a>
                </div>
                <div class="col">
                    <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">@lang('mensajes.mensajecrearproducto')</button>
                </div>
            </div>
        </form>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
