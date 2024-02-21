@extends('layouts.app')
@section('titulo','Actualizar promocion'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2>@lang('mensajes.mensajeeditarpromocion')</h2>
    <form  action="{{route('promocioneditar',[$idFranquicia,$promocion[0]->id])}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
     @csrf
    <div class="row">
      <div class="col-12">
          <div class="form-group">
              <label>Titulo de la promoción</label>
              <input type="text" name="titulo" class="form-control {!! $errors->first('titulo','is-invalid')!!}" placeholder="Titulo de la promoción" value="{{$promocion[0]->titulo}}">
                {!! $errors->first('titulo','<div class="invalid-feedback">Escribe un titulo.</div>')!!}
          </div>
      </div>
    </div>
    <div class="row producto">
    <div class="col-4">
            <div class="form-group">
                <label># Contratos: </label>
                <input type="text" name="armazones2" class="form-control {!! $errors->first('armazones2','is-invalid')!!}" min="1" placeholder="armazones" value="{{$promocion[0]->armazones}}">
                @if($errors->has('armazones2'))
                  <div class="invalid-feedback">{{$errors->first('armazones2')}}</div>
                @endif
            </div>
        </div>

        <div class="col-3">
            <div class="form-group">
                <label>Descuento en % </label>
                <input type="number" name="cantidad" class="form-control {!! $errors->first('cantidad','is-invalid')!!}" min="1" placeholder="Numero de armazones" value="{{$promocion[0]->precioP}}">
                @if($errors->has('cantidad'))
                  <div class="invalid-feedback">{{$errors->first('cantidad')}}</div>
                @endif
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Descuento con precio $</label>
                <input type="number" name="preciouno" class="form-control {!! $errors->first('preciouno','is-invalid')!!}" min="1" placeholder="precio unico" value="{{$promocion[0]->preciouno}}">
                @if($errors->has('preciouno'))
              <div class="invalid-feedback">{{$errors->first('preciouno')}}</div>
              @endif
            </div>
        </div>
        <div class="col-2">
          <label>Promoción por precio</label>
          <div class="form-check">
              <input type="checkbox" name="tipopromocion" id="tipopromocion" class="form-check-input" value="1"  style="transform: scale(1.5);" @if($promocion[0]->tipopromocion == 1) checked @endif >
              <label class="form-check-label" for="tipopromocion">por precio $</label>
          </div>
        </div>
</div>
    <hr>
    <div class="row">
      <div class="col-3">
          <div class="form-group">
              <label>Fecha de inicio de la promocion</label>
              <input type="date" name="inicio" class="form-control {!! $errors->first('inicio','is-invalid')!!}"  placeholder="Fecha de inicio" value="{{$promocion[0]->inicio}}">
              @if($errors->has('inicio'))
                <div class="invalid-feedback">{{$errors->first('inicio')}}</div>
              @endif
          </div>
      </div>
      <div class="col-3">
          <div class="form-group">
              <label>Fecha para finalizar la promocion</label>
              <input type="date" name="fin" class="form-control {!! $errors->first('fin','is-invalid')!!}"  placeholder="Fecha para finalizar" value="{{$promocion[0]->fin}}">
              @if($errors->has('fin'))
                <div class="invalid-feedback">{{$errors->first('fin')}}</div>
              @endif
          </div>
      </div>
      <div class="col-2">
          <div class="form-group">
              <label for="contarventa">Contar para</label>
              <select class="custom-select {!! $errors->first('contarventa','is-invalid')!!}" name="contarventa" id="contarventa">
                  <option value="0" @if($promocion[0]->contarventa == null OR $promocion[0]->contarventa == "" OR $promocion[0]->contarventa == 0) selected @endif>Optometrista/Asistente</option>
                  <option value="1" @if($promocion[0]->contarventa == 1) selected @endif>Solo optometrista</option>
                  <option value="2" @if($promocion[0]->contarventa == 2) selected @endif>Solo asistente</option>
                  <option value="3" @if($promocion[0]->contarventa == 3) selected @endif>Ninguna</option>
              </select>
              {!! $errors->first('contarventa','<div class="invalid-feedback">Por favor, selecciona una opcion.</div>')!!}
          </div>
      </div>
      <div class="col-2">
          <div class="form-group">
              <label for="tipopromocion2">Tipo de promoción</label>
              <select class="custom-select {!! $errors->first('tipopromocion2','is-invalid')!!}" name="tipopromocion2" id="tipopromocion2">
                  <option value="" selected >Seleccionar tipo</option>
                  <option value="0" @if($promocion[0]->tipo == 0) selected @endif >Normal</option>
                  <option value="1" @if($promocion[0]->tipo == 1) selected @endif >Reposición</option>
                  <option value="1" @if($promocion[0]->tipo == 2) selected @endif >Empleado</option>
              </select>
              {!! $errors->first('tipopromocion2','<div class="invalid-feedback">Por favor, selecciona un tipo de promoción.</div>')!!}
          </div>
      </div>
      <div class="col-2">
          <label>Tipo: </label>
          <div class="form-check">
              <input type="checkbox" name="administrador" class="form-check-input" value="1" style="transform: scale(1.5);" @if($promocion[0]->contado == 1) checked @endif  >
              <label class="form-check-label" for="administrador">Administrador</label>
          </div>
      </div>
    </div>
    <div class="row">
    <div class="col-4">
            <a href="{{route('listasfranquicia',$idFranquicia)}}" class="btn btn-outline-success btn-block">@lang('mensajes.regresar')</a>
          </div>
        <div class="col-8">
          <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">@lang('mensajes.actualizar')</button>
        </div>
      </div>
 </form>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
