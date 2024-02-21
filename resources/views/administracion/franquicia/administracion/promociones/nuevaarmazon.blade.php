@extends('layouts.app')
@section('titulo','Contratos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2>@lang('mensajes.mensajenuevapromocion')</h2>
     <form  action="{{route('promocioncrear',$idFranquicia)}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
     @csrf
    <div class="row">
      <div class="col-12">
          <div class="form-group">
              <label>Titulo de la promoción</label>
              <input type="text" name="titulo" class="form-control {!! $errors->first('titulo','is-invalid')!!}" placeholder="Titulo de la promoción" value="{{ old('titulo') }}">
                {!! $errors->first('titulo','<div class="invalid-feedback">Campo obligatorio</div>')!!}
          </div>
      </div>
    </div>
    <div class="row">
    <div class="col-3">
            <div class="form-group">
                <label># Contratos: </label>
                <input type="number" name="armazones" class="form-control {!! $errors->first('armazones','is-invalid')!!}" min="1" placeholder="armazones" value="{{ old('armazones',1) }}">
                {!! $errors->first('armazones','<div class="invalid-feedback">Campo obligatorio no puede ser menor a 1</div>')!!}
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Descuento en % </label>
                <input type="number" name="cantidad" class="form-control {!! $errors->first('cantidad','is-invalid')!!}" min="1" max="100" placeholder="Cantidad" value="{{ old('cantidad') }}">
              @if($errors->has('cantidad'))
              <div class="invalid-feedback">{{$errors->first('cantidad')}}</div>
              @endif
              </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Descuento con precio $</label>
                <input type="number" name="preciouno" class="form-control {!! $errors->first('preciouno','is-invalid')!!}" min="1" placeholder="precio unico" value="{{ old('preciouno') }}">
                @if($errors->has('preciouno'))
              <div class="invalid-feedback">{{$errors->first('preciouno')}}</div>
              @endif
            </div>
        </div>
        <div class="col-2">
          <label>Promoción por precio</label>
          <div class="form-check">
              <input type="checkbox" name="tipopromocion" id="tipopromocion" class="form-check-input" value="1"  style="transform: scale(1.5);">
              <label class="form-check-label" for="tipopromocion">por precio $</label>
          </div>
        </div>
</div>
    <hr>
    <div class="row">
      <div class="col-3">
          <div class="form-group">
              <label>Fecha de inicio de la promocion</label>
              <input type="date" name="inicio" class="form-control {!! $errors->first('inicio','is-invalid')!!}"  placeholder="Fecha de inicio" value="{{ old('inicio') }}">
              {!! $errors->first('inicio','<div class="invalid-feedback">La fecha inicial debe ser menor a la final</div>')!!}
          </div>
      </div>
      <div class="col-3">
          <div class="form-group">
              <label>Fecha para finalizar la promocion</label>
              <input type="date" name="fin" class="form-control {!! $errors->first('fin','is-invalid')!!}"  placeholder="Fecha para finalizar" value="{{ old('fin') }}">
              {!! $errors->first('fin','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
          </div>
      </div>
      <div class="col-2">
          <div class="form-group">
              <label for="contarventa">Contar para</label>
              <select class="custom-select {!! $errors->first('contarventa','is-invalid')!!}" name="contarventa" id="contarventa">
                  <option value="0" selected >Optometrista/Asistente</option>
                  <option value="1">Solo optometrista</option>
                  <option value="2">Solo asistente</option>
                  <option value="3">Ninguna</option>
              </select>
              {!! $errors->first('contarventa','<div class="invalid-feedback">Por favor, selecciona una opcion.</div>')!!}
          </div>
      </div>
        <div class="col-2">
            <div class="form-group">
                <label for="tipopromocion2">Tipo de promoción</label>
                <select class="custom-select {!! $errors->first('tipopromocion2','is-invalid')!!}" name="tipopromocion2" id="tipopromocion2">
                    <option value="" selected >Seleccionar tipo</option>
                    <option value="0" selected >Normal</option>
                    <option value="1">Reposición</option>
                    <option value="2">Empleado</option>
                </select>
                {!! $errors->first('tipopromocion2','<div class="invalid-feedback">Por favor, selecciona un tipo de promoción.</div>')!!}
            </div>
        </div>
      <div class="col-2">
          <label>Tipo: </label>
          <div class="form-check">
              <input type="checkbox" name="administrador" class="form-check-input" value="1" style="transform: scale(1.5);">
              <label class="form-check-label" for="administrador">Administrador</label>
          </div>
        </div>
    </div>
    @if(Auth::user()->rol_id == 7)
        <hr>
            <h5>Sucursales a las que aplica promoción</h5>
            <div class="row" style="margin-top: 10px; margin-bottom: 10px;">
                @foreach($sucursales as $sucursal)
                    <div class="col-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input " name="{{$sucursal->id}}" id="{{$sucursal->id}}" value="1">
                            <label class="custom-control-label" for="{{$sucursal->id}}">{{$sucursal->colonia}} {{$sucursal->numero}},{{$sucursal->ciudad}} {{$sucursal->estado}}</label>
                        </div>
                    </div>
                @endforeach
            </div>
    @endif
    <div class="row">
        <div class="col-4">
            <a href="{{route('listasfranquicia',$idFranquicia)}}" class="btn btn-outline-success btn-block">@lang('mensajes.regresar')</a>
        </div>
        <div class="col">
          <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">@lang('mensajes.mensajecrearpromocion')</button>
        </div>
      </div>
     </form>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
