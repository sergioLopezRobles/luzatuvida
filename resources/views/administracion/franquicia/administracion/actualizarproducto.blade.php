@extends('layouts.app')
@section('titulo','Actualizar producto'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>@lang('mensajes.mensajeeditarproducto')</h2>
        <form action="{{route('productoeditar',[$idFranquicia,$producto[0]->id])}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control {!! $errors->first('nombre','is-invalid')!!}" placeholder="Nombre" value="{{$producto[0]->nombre}}">
                        {!! $errors->first('nombre','<div class="invalid-feedback">Nombre no valido</div>')!!}
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Total piezas</label>
                        <input type="number" name="piezas" class="form-control {!! $errors->first('piezas','is-invalid')!!}" min="0" placeholder="piezas"
                               value="{{$producto[0]->totalpiezas}}">
                        {!! $errors->first('piezas','<div class="invalid-feedback">La cantidad de piezas es obligatoria y/o positiva</div>')!!}
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Piezas restantes</label>
                        <input type="number" class="form-control" min="0" placeholder="piezas" disabled
                               value="{{$producto[0]->piezas}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Precio</label>
                        <input type="number" name="precio" class="form-control {!! $errors->first('precio','is-invalid')!!}" placeholder="Precio" value="{{$producto[0]->precio}}">
                        {!! $errors->first('precio','<div class="invalid-feedback">Precio no valido</div>')!!}
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Color</label>
                        <input type="text" name="color" class="form-control {!! $errors->first('color','is-invalid')!!}" placeholder="color" value="{{$producto[0]->color}}">
                        {!! $errors->first('color','<div class="invalid-feedback">Color no valido</div>')!!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <div class="form-group">
                        @if(isset($producto[0]->foto))
                            <img src="{{asset($producto[0]->foto)}}" class="img-thumbnail" style="width:100px;height:65px;">
                        @else
                            <h6>NA</h6>
                        @endif
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Actualizar foto</label>
                        <input type="file" name="foto" class="form-control-file  {!! $errors->first('foto','is-invalid')!!}" accept="image/jpg">
                        {!! $errors->first('foto','<div class="invalid-feedback">La foto debera estar en formato jpg.</div>')!!}
                    </div>
                </div>
                <div class="col-3 invisible">
                    <div class="form-group">
                        <label for="">Tipo de producto:</label>
                        <select name="tipoproducto" class="form-control {!! $errors->first('tipoproducto','is-invalid')!!}" placeholder="tipo de producto"
                                value="{{ old('tipoproducto',0) }}">
                            <option selected value=0>Seleccionar</option>
                            @foreach($tipoproducto as $tipo)
                                @if($tipo->id == $producto[0]->id_tipoproducto)
                                    <option selected value="{{$tipo->id}}">{{$tipo->tipo}}</option>
                                @else
                                    <option value="{{$tipo->id}}">{{$tipo->tipo}}</option>
                                @endif
                            @endforeach
                        </select>
                        {!! $errors->first('tipoproducto','<div class="invalid-feedback">Elegir un tipo de producto, campo obligatorio </div>')!!}
                    </div>
                </div>
                <div class="col-2">
                    <label>Estado</label>
                    <div class="form-check">
                        <input type="checkbox" name="estado" class="form-check-input" value="1" @if($producto[0]->estado == 1) checked @endif >
                        <label class="form-check-label" for="estado">Activo/Inactivo</label>
                    </div>
                </div>
                @if($producto[0]->id_tipoproducto == 1)
                    <div class="col-2">
                        <div class="form-check" style="margin-top: 30px;">
                            <input type="checkbox" name="premium" class="form-check-input" value="1" @if($producto[0]->premium == 1) checked @endif >
                            <label class="form-check-label" for="premium">Premium</label>
                        </div>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-2">
                    <div class="form-group">
                        <label>Gasto en póliza (Administración)</label>
                        <input type="number" name="polizagastosadministracion" class="form-control {!! $errors->first('polizagastosadministracion','is-invalid')!!}" min="0"
                               value="{{$producto[0]->polizagastosadministracion}}"
                               placeholder="Gasto en poliza">
                        {!! $errors->first('polizagastosadministracion','<div class="invalid-feedback">El gasto de poliza administración es obligatorio y deberá ser mayor a cero.</div>')!!}
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Gasto en póliza (Asist/Opto)</label>
                        <input type="number" name="polizagastos" class="form-control {!! $errors->first('polizagastos','is-invalid')!!}" min="0"
                               value="{{$producto[0]->polizagastos}}"
                               placeholder="Gasto en poliza">
                        {!! $errors->first('polizagastos','<div class="invalid-feedback">El gasto de poliza asistente/optometrista es obligatorio y deberá ser mayor a cero.</div>')!!}
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Gasto en póliza (Cobranza)</label>
                        <input type="number" name="polizagastoscobranza" class="form-control {!! $errors->first('polizagastoscobranza','is-invalid')!!}" min="0"
                               value="{{$producto[0]->polizagastoscobranza}}"
                               placeholder="Gasto en poliza">
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
                               value="{{$producto[0]->iniciop}}">
                        @if($errors->has('iniciop'))
                            <div class="invalid-feedback">{{$errors->first('iniciop')}}</div>
                        @endif
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Fecha final de la promoción</label>
                        <input type="date" name="finp" class="form-control {!! $errors->first('finp','is-invalid')!!}" placeholder="Fecha de inicio" value="{{$producto[0]->finp}}">
                        @if($errors->has('finp'))
                            <div class="invalid-feedback">{{$errors->first('finp')}}</div>
                        @endif
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Precio con promoción:</label>
                        <input type="number" name="preciop" class="form-control {!! $errors->first('preciop','is-invalid')!!}" min="0" placeholder="Precio promoción"
                               value="{{$producto[0]->preciop}}">
                        @if($errors->has('preciop'))
                            <div class="invalid-feedback">{{$errors->first('preciop')}}</div>
                        @endif
                    </div>
                </div>
                <div class="col-2">
                    <label> Estado promoción</label>
                    <div class="form-check">
                        <input type="checkbox" name="activo" id="activo" class="form-check-input" @if($producto[0]->activo == 1) checked @endif>
                        <label class="form-check-label" for="activo">Activo/Inactivo</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <a href="{{route('listasfranquicia',$idFranquicia)}}" class="btn btn-outline-success btn-block">@lang('mensajes.volveradminfranquicia')</a>
                </div>
                <div class="col-8">
                    <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">@lang('mensajes.actualizar')</button>
                </div>
            </div>
        </form>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
