@extends('layouts.app')
@section('titulo','Contrato nuevo'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')     
  @include('parciales.notificaciones')
  <!-- {{isset($errors)? var_dump($errors) : ''}} -->
  <div class="contenedor">     
    <h2>@lang('mensajes.mensajenuevocontratofranquicia')</h2>
    <form id="frmFranquiciaNueva" action="{{route('crearcontrato',$idFranquicia)}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
      @csrf
      <div class="franquicia" >
        <div class="row">
          <div class="col-2">
            <label for="">Zona</label>                                               
            <select class="custom-select {!! $errors->first('zona','is-invalid')!!}" name="zona">                               
                @if(count($zonas) > 0)
                    <option selected>Seleccionar</option>
                  @foreach($zonas as $zona)
                    @if($ultimaZona != null)
                       @if($ultimaZona[0]->ID == $zona->ID)
                        <option selected value="{{$zona->ID}}" >{{$zona->zona}}</option>
                        @else
                        <option value="{{$zona->ID}}">{{$zona->zona}}</option>
                        @endif
                      @else
                      <option value="{{$zona->ID}}">{{$zona->zona}}</option>
                      @endif
                    @endforeach
                  @else 
                    <option selected>Sin registros</option>
                @endif
            </select> 
            {!! $errors->first('zona','<div class="invalid-feedback">Elegir una zona, campo obligatorio </div>')!!}
          </div>    
          <div class="col-2">
          <label for="">Optometrista</label>                                               
            <select class="custom-select {!! $errors->first('optometrista','is-invalid')!!}" name="optometrista">                               
                @if(count($optometristas) > 0)
                     <option selected>Seleccionar</option> 
                    @foreach($optometristas as $optometrista)
                     @if($ultimoOptometrista != null)
                       @if($ultimoOptometrista[0]->ID == $optometrista->ID)
                        <option selected value="{{$optometrista->ID}}">{{$optometrista->NAME}}</option>
                        @else
                        <option value="{{$optometrista->ID}}">{{$optometrista->NAME}}</option>
                        @endif
                      @else
                      <option value="{{$optometrista->ID}}">{{$optometrista->NAME}}</option>
                      @endif
                    @endforeach
                  @else 
                    <option selected>Sin registros</option>
                @endif
            </select> 
            {!! $errors->first('optometrista','<div class="invalid-feedback">Elegir una zona, campo obligatorio </div>')!!}
          </div> 
          <div class="col-2">
          <label for="">Forma de pago</label>                                               
            <select class="custom-select {!! $errors->first('formapago','is-invalid')!!}" name="formapago" id="formapago">                               
               <option selected >Seleccionar</option>
                   <option value="Contado" {{ old('formapago') == 'Contado' ? 'selected' : '' }}>Contado</option>
                   <option value="Semanal" {{ old('formapago') == 'Semanal' ? 'selected' : '' }}>Semanal</option>
                   <option value="Quincenal" {{ old('formapago') == 'Quincenal' ? 'selected' : '' }}>Quincenal</option>
                   <option value="Mensual" {{ old('formapago') == 'Mensual' ? 'selected' : '' }}>Mensual</option>
            </select> 
            {!! $errors->first('formapago','<div class="invalid-feedback">Elegir una forma de pago </div>')!!}
          </div>  
          <div class="col-3">
            <div class="form-group">
              <label>Nombre</label>
              <input type="text" name="nombre" class="form-control {!! $errors->first('nombre','is-invalid')!!}"  placeholder="Nombre" value="{{ old('nombre') }}">
              {!! $errors->first('nombre','<div class="invalid-feedback">El nombre es obligatorio.</div>')!!}
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <label>Calle</label>
              <input type="text" name="calle" class="form-control {!! $errors->first('calle','is-invalid')!!}"  placeholder="Calle"  value="{{ old('calle') }}">
              {!! $errors->first('calle','<div class="invalid-feedback">La calle es obligatoria.</div>')!!}
            </div>
          </div>
        </div>  
        <div class="row">
          <div class="col-3">
              <div class="form-group">
                <label>Numero</label>
                <input type="text" name="numero" class="form-control {!! $errors->first('numero','is-invalid')!!}"  placeholder="Numero"  value="{{ old('numero') }}">
                {!! $errors->first('numero','<div class="invalid-feedback">El numero es obligatorio.</div>')!!}
              </div>
          </div>
          <div class="col-3">
              <div class="form-group">
                <label>Departamento</label>
                <input type="text" name="departamento" class="form-control {!! $errors->first('departamento','is-invalid')!!}"  placeholder="Departamento"  value="{{ old('departamento') }}">               
                {!! $errors->first('departamento','<div class="invalid-feedback">El numero es obligatorio.</div>')!!}
              </div>
          </div>
          <div class="col-3">
              <div class="form-group">
                <label>Al lado de</label>
                <input type="text" name="alladode" class="form-control {!! $errors->first('alladode','is-invalid')!!}"  placeholder="Al lado de"  value="{{ old('alladode') }}">
                {!! $errors->first('alladode','<div class="invalid-feedback">Campo obligatorio</div>')!!}
              </div>
          </div>
          <div class="col-3">
              <div class="form-group">
                <label>Frente a</label>
                <input type="text" name="frentea" class="form-control {!! $errors->first('frentea','is-invalid')!!}"  placeholder="Frente a"  value="{{ old('frentea') }}">
                {!! $errors->first('frentea','<div class="invalid-feedback">Campo obligatorio</div>')!!}
              </div>
           </div>
          </div>    
        <div class="row">
          <div class="col-3">
            <div class="form-group">
              <label>Entre calles</label>
              <input type="text" name="entrecalles" class="form-control {!! $errors->first('entrecalles','is-invalid')!!}"  placeholder="Entre calles"  value="{{ old('entrecalles') }}">
              {!! $errors->first('entrecalles','<div class="invalid-feedback">El campo es obligatorio.</div>')!!}
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <label>Colonia</label>
              <input type="text" name="colonia" class="form-control {!! $errors->first('colonia','is-invalid')!!}"  placeholder="Colonia"  value="{{ old('colonia') }}">
              {!! $errors->first('colonia','<div class="invalid-feedback">La colonia es obligatoria.</div>')!!}
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <label>Localidad</label>
              <input type="text" name="localidad" class="form-control {!! $errors->first('localidad','is-invalid')!!}"  placeholder="Localidad"  value="{{ old('localidad')}}">
              {!! $errors->first('localidad','<div class="invalid-feedback">La localidad es obligatoria.</div>')!!}
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <label>Telefono</label>
              <input type="text" name="telefono" class="form-control {!! $errors->first('telefono','is-invalid')!!}"  placeholder="Telefono"  value="{{ old('telefono') }}">
              {!! $errors->first('telefono','<div class="invalid-feedback">El telefono debe contener 10 numeros.</div>')!!}
            </div>
          </div>         
        </div>  
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label>Tipo Casa</label>
              <input type="text" name="casatipo" class="form-control {!! $errors->first('casatipo','is-invalid')!!}"  placeholder="Tipo Casa"  value="{{ old('casatipo') }}">
              {!! $errors->first('casatipo','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <label>Casa color</label>
              <input type="text" name="casacolor" class="form-control {!! $errors->first('casacolor','is-invalid')!!}"  placeholder="Casa color" value="{{ old('casacolor') }}">
              {!! $errors->first('casacolor','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
            </div>
           </div>
           <div class="col-4">
            <div class="form-group">
              <label>Nombre referencia</label>
              <input type="text" name="nr" class="form-control {!! $errors->first('nr','is-invalid')!!}"  placeholder="Nombre de referencia"  value="{{ old('nr') }}">
              {!! $errors->first('nr','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <label>Telefono referencia</label>
              <input type="text" name="tr" class="form-control {!! $errors->first('tr','is-invalid')!!}"  placeholder="Telefono de referencia" value="{{ old('tr') }}">
              {!! $errors->first('tr','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
            </div>
           </div>
         </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label>Foto INE Frente</label>
              <input type="file" name="fotoine" class="form-control-file  {!! $errors->first('fotoine','is-invalid')!!}" accept="image/png">
              {!! $errors->first('fotoine','<div class="invalid-feedback">La foto debera estar en formato png.</div>')!!}
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <label>Foto INE Atrás</label>
              <input type="file" name="fotoineatras" class="form-control-file  {!! $errors->first('fotoineatras','is-invalid')!!}" accept="image/png">
              {!! $errors->first('fotoineatras','<div class="invalid-feedback">Llenar ambos campos del INE</div>')!!}
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <label>Foto de la casa</label>
              <input type="file" name="fotocasa" class="form-control-file  {!! $errors->first('fotocasa','is-invalid')!!}" accept="image/png">
              {!! $errors->first('fotocasa','<div class="invalid-feedback">La foto debera estar en formato png.</div>')!!}
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <label>Comprobante de domicilio</label>
              <input type="file" name="comprobantedomicilio" class="form-control-file  {!! $errors->first('comprobantedomicilio','is-invalid')!!}" accept="image/png">
              {!! $errors->first('comprobantedomicilio','<div class="invalid-feedback">La foto debera estar en formato png.</div>')!!}
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <label>Tarjeta de pensión frente:</label>
              <input type="file" name="tarjetapension" class="form-control-file  {!! $errors->first('tarjetapension','is-invalid')!!}" accept="image/png">
              {!! $errors->first('tarjetapension','<div class="invalid-feedback">Elegir foto solo si es pago mensual</div>')!!}
            </div>
          </div>  
          <div class="col-2">
            <div class="form-group">
              <label>Tarjeta de pensión Atras:</label>
              <input type="file" name="tarjetapensionatras" class="form-control-file  {!! $errors->first('tarjetapensionatras','is-invalid')!!}" accept="image/png">
              {!! $errors->first('tarjetapensionatras','<div class="invalid-feedback">Elegir foto solo si es pago mensual y elegir ambos lados.</div>')!!}
            </div>
          </div>  
           </div> 
        <div class="row">
          <div class="col-4">
              <a href="{{route('listacontrato',$idFranquicia)}}" class="btn btn-outline-success">@lang('mensajes.regresar')</a>
          </div>
          <div class="col">
            <button class="btn btn-outline-success" name="btnSubmit" type="submit">@lang('mensajes.mensajecrearnuevocontrato')</button>
          </div>
        </div>  
      </div>
    </form>
  </div> 
@endsection