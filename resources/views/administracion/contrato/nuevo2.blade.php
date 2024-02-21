@extends('layouts.app')
@section('titulo','Contrato nuevo'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')     
  @include('parciales.notificaciones')
  <!-- {{isset($errors)? var_dump($errors) : ''}} -->
  <div class="contenedor">     
    <h2>@lang('mensajes.mensajenuevocontratofranquicia')</h2>
    <form id="frmFranquiciaNueva" action="{{route('crearcontrato2',[$idFranquicia,$idContrato])}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
      @csrf
      <div class="franquicia">
        <div class="row">
          <div class="col-2">
          <div class="form-group">
              <label>Zona:</label>
              <input type="text" name="zona" class="form-control {!! $errors->first('zona','is-invalid')!!}"   readonly value="{{ $contratos[0]->zona}}">
              {!! $errors->first('zona','<div class="invalid-feedback">El nombre es obligatorio.</div>')!!}
            </div>
          </div>    
          <div class="col-2">
          <div class="form-group">
              <label>Optometrista:</label>
              <input type="text" name="Optometrista" class="form-control {!! $errors->first('Optometrista','is-invalid')!!}"   readonly value="{{ $contratos[0]->name}}">
              {!! $errors->first('Optometrista','<div class="invalid-feedback">El nombre es obligatorio.</div>')!!}
            </div>
          </div> 
          <div class="col-2">
          <div class="form-group">
              <label>Forma de pago:</label>
              <input type="text" name="formapago" class="form-control {!! $errors->first('formapago','is-invalid')!!}"  placeholder="Nombre" readonly value="{{ $contratos[0]->pago}}">
              {!! $errors->first('formapago','<div class="invalid-feedback">El nombre es obligatorio.</div>')!!}
            </div>
          </div>  
          <div class="col-3">
            <div class="form-group">
              <label>Nombre del paciente:</label>
              <input type="text" name="nombre" class="form-control {!! $errors->first('nombre','is-invalid')!!}"  placeholder="Nombre"  value="{{ $contratos[0]->nombre}}">
              {!! $errors->first('nombre','<div class="invalid-feedback">El nombre es obligatorio.</div>')!!}
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <label>Calle</label>
              <input type="text" name="calle" class="form-control {!! $errors->first('calle','is-invalid')!!}"  placeholder="Calle"  readonly value="{{ $contratos[0]->calle}}">
              {!! $errors->first('calle','<div class="invalid-feedback">La calle es obligatoria.</div>')!!}
            </div>
          </div>
        </div>  
        <div class="row">
          <div class="col-3">
              <div class="form-group">
                <label>Numero</label>
                <input type="text" name="numero" class="form-control {!! $errors->first('numero','is-invalid')!!}"  placeholder="Numero"  readonly value="{{ $contratos[0]->numero}}">
                {!! $errors->first('numero','<div class="invalid-feedback">El numero es obligatorio.</div>')!!}
              </div>
          </div>
          <div class="col-3">
              <div class="form-group">
                <label>Departamento</label>
                <input type="text" name="departamento" class="form-control {!! $errors->first('departamento','is-invalid')!!}"  placeholder="Departamento"  readonly value="{{ $contratos[0]->depto}}">               
                {!! $errors->first('departamento','<div class="invalid-feedback">El numero es obligatorio.</div>')!!}
              </div>
          </div>
          <div class="col-3">
              <div class="form-group">
                <label>Al lado de</label>
                <input type="text" name="alladode" class="form-control {!! $errors->first('alladode','is-invalid')!!}"  placeholder="Al lado de"  readonly value="{{ $contratos[0]->alladode}}">
                {!! $errors->first('alladode','<div class="invalid-feedback">Campo obligatorio</div>')!!}
              </div>
          </div>
          <div class="col-3">
              <div class="form-group">
                <label>Frente a</label>
                <input type="text" name="frentea" class="form-control {!! $errors->first('frentea','is-invalid')!!}"  placeholder="Frente a"  readonly value="{{ $contratos[0]->frentea}}">
                {!! $errors->first('frentea','<div class="invalid-feedback">Campo obligatorio</div>')!!}
              </div>
           </div>
          </div>    
        <div class="row">
          <div class="col-3">
            <div class="form-group">
              <label>Entre calles</label>
              <input type="text" name="entrecalles" class="form-control {!! $errors->first('entrecalles','is-invalid')!!}"  placeholder="Entre calles"  readonly value="{{ $contratos[0]->entrecalles}}">
              {!! $errors->first('entrecalles','<div class="invalid-feedback">El campo es obligatorio.</div>')!!}
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <label>Colonia</label>
              <input type="text" name="colonia" class="form-control {!! $errors->first('colonia','is-invalid')!!}"  placeholder="Colonia"  readonly value="{{ $contratos[0]->colonia}}">
              {!! $errors->first('colonia','<div class="invalid-feedback">La colonia es obligatoria.</div>')!!}
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <label>Localidad</label>
              <input type="text" name="localidad" class="form-control {!! $errors->first('localidad','is-invalid')!!}"  placeholder="Localidad" readonly value="{{ $contratos[0]->localidad}}">
              {!! $errors->first('localidad','<div class="invalid-feedback">La localidad es obligatoria.</div>')!!}
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <label>Telefono</label>
              <input type="text" name="telefono" class="form-control {!! $errors->first('telefono','is-invalid')!!}"  placeholder="Telefono"  value="{{ $contratos[0]->telefono}}">
              {!! $errors->first('telefono','<div class="invalid-feedback">El telefono debe contener 10 numeros.</div>')!!}
            </div>
          </div>         
        </div>  
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label>Tipo Casa</label>
              <input type="text" name="casatipo" class="form-control {!! $errors->first('casatipo','is-invalid')!!}"  placeholder="Tipo Casa"  readonly value="{{ $contratos[0]->casatipo}}">
              {!! $errors->first('casatipo','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
            </div>
          </div>
          <div class="col-2">
            <div class="form-group">
              <label>Casa color</label>
              <input type="text" name="casacolor" class="form-control {!! $errors->first('casacolor','is-invalid')!!}"  placeholder="Casa color" readonly value="{{ $contratos[0]->casacolor}}">
              {!! $errors->first('casacolor','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
            </div>
           </div>
           <div class="col-4">
            <div class="form-group">
              <label>Nombre referencia</label>
              <input type="text" name="nr" class="form-control {!! $errors->first('nr','is-invalid')!!}"  placeholder="Nombre de referencia" readonly value="{{ $contratos[0]->nombrereferencia}}">
              {!! $errors->first('nr','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <label>Telefono referencia</label>
              <input type="text" name="tr" class="form-control {!! $errors->first('tr','is-invalid')!!}"  placeholder="Telefono de referencia" readonly value="{{ $contratos[0]->telefonoreferencia}}">
              {!! $errors->first('tr','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
            </div>
           </div>
         </div>
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label>Foto INE</label>
              <input type="file" name="fotoine" class="form-control-file  {!! $errors->first('fotoine','is-invalid')!!}" accept="image/png">
              {!! $errors->first('fotoine','<div class="invalid-feedback">La foto debera estar en formato png.</div>')!!}
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <label>Foto de la casa</label>
              <input type="file" name="fotocasa" class="form-control-file  {!! $errors->first('fotocasa','is-invalid')!!}" accept="image/png">
              {!! $errors->first('fotocasa','<div class="invalid-feedback">La foto debera estar en formato png.</div>')!!}
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <label>Comprobante de domicilio</label>
              <input type="file" name="comprobantedomicilio" class="form-control-file  {!! $errors->first('comprobantedomicilio','is-invalid')!!}" accept="image/png">
              {!! $errors->first('comprobantedomicilio','<div class="invalid-feedback">La foto debera estar en formato png.</div>')!!}
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <label>Tarjeta de pensión</label>
              <input type="file" name="tarjetapension" class="form-control-file  {!! $errors->first('tarjetapension','is-invalid')!!}" accept="image/png">
              {!! $errors->first('tarjetapension','<div class="invalid-feedback">Elegir foto solo si es pago mensual</div>')!!}
            </div>
          </div>  
          <div class="col-6">
            <div class="form-group">
              <label>Promoción</label>
              <input type="text" name="promocion" class="form-control {!! $errors->first('promocion','is-invalid')!!}"  placeholder="Nombre" readonly value="{{ $contratos[0]->titulo}}">
              {!! $errors->first('promocion','<div class="invalid-feedback">El nombre es obligatorio.</div>')!!}
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