@extends('layouts.app')
@section('titulo','Actualizar Historial Clinico'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
  <!-- {{isset($errors)? var_dump($errors) : ''}} -->

  <div class="contenedor">
        <h2>@lang('mensajes.mensajeactualizarhistorialclinico')</h2>
        <hr>
        <form id="frmactdatosHistoriallinico" action="{{route('editarHistorial',[$idFranquicia,$idContrato,$idHistorial])}}"  enctype="multipart/form-data" method="POST" onsubmit="btnSubmit
        .disabled = true;">
            @csrf
            <div class="franquicia">
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label>Edad</label>
                            <input type="text" name="edad" class="form-control {!! $errors->first('edad','is-invalid')!!}"  placeholder="Edad" readonly value="{{ $datosHistorial[0]->edad}}">
                            {!! $errors->first('edad','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="form-group">
                            <label>Diagnostico</label>
                            <input type="text" name="diagnostico" class="form-control {!! $errors->first('diagnostico','is-invalid')!!}"  placeholder="Diagnostico" readonly
                                   value="{{ $datosHistorial[0]->diagnostico}}">
                            {!! $errors->first('diagnostico','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label>Ocupacion</label>
                            <input type="text" name="ocupacion" class="form-control {!! $errors->first('ocupacion')!!}"  placeholder="Ocupacion" readonly value="{{ $datosHistorial[0]->ocupacion}}">
                            {!! $errors->first('ocupacion','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Diabetes</label>
                            <input type="text" name="diabetes" class="form-control {!! $errors->first('diabetes')!!}"  placeholder="Diabetes" readonly value="{{ $datosHistorial[0]->diabetes}}">
                            {!! $errors->first('diabetes','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Hipertension</label>
                            <input type="text" name="hipertension" class="form-control {!! $errors->first('hipertension','is-invalid')!!}"  placeholder="Hipertension" readonly
                                   value="{{ $datosHistorial[0]->hipertension}}">
                            {!! $errors->first('hipertension','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                </div>
                <h6>Molestia</h6>
                <div class="row">
                    <div class="col-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="dolor" id="dolores"  readonly value="1" @if($datosHistorial[0]->dolor == 1) checked @endif>
                            <label class="custom-control-label" for="dolores">Dolor de cabeza</label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="ardor" id="ardores"  readonly value="1" @if($datosHistorial[0]->ardor == 1) checked @endif>
                            <label class="custom-control-label" for="ardores">Ardor de los ojos</label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="golpe" id="golpes"  readonly value="1" @if($datosHistorial[0]->golpeojos == 1) checked @endif>
                            <label class="custom-control-label" for="golpes">Golpe en cabeza</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="otroM" id="customCheck4" readonly value="1" @if($datosHistorial[0]->otroM == 1) checked @endif>
                                <label class="custom-control-label" for="customCheck4">Otro</label>
                            </div>
                            <input type="text" name="molestia" class="form-control {!! $errors->first('molestia','is-invalid')!!}"  placeholder="Otro" readonly
                                   value="{{ $datosHistorial[0]->molestiaotro}}">
                            {!! $errors->first('molestia','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Ultimo examen</label>
                            <input type="date" name="ultimoexamen" class="form-control {!! $errors->first('ultimoexamen','is-invalid')!!}"  placeholder="Ultimo examen" readonly
                                   value="{{ $datosHistorial[0]->ultimoexamen}}">
                            {!! $errors->first('ultimoexamen','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                </div>
                <hr>
                <h2>@lang('mensajes.mensajeproducto')</h2>
                <div class="row">
                <div class="col-4">
                        <div class="form-group">
                            <label>Producto</label>
                            <input type="text" name="producto" class="form-control {!! $errors->first('producto','is-invalid')!!}"  placeholder="producto" readonly
                                   value="{{ $datosHistorial[0]->nombre2}}">
                            {!! $errors->first('producto','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-4">
                <div class="form-group">
                    <label>Paquete:</label>
                    <input type="text" name="paquete" class="form-control" readonly value="{{ $datosHistorial[0]->nombre}}">
                </div>
            </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Fecha de Entrega</label>
                            <input type="date" name="fechaentrega" class="form-control {!! $errors->first('fechaentrega','is-invalid')!!}"  placeholder="Fecha de entrega" readonly
                                   value="{{ $datosHistorial[0]->fechaentrega}}">
                            {!! $errors->first('fechaentrega','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                </div>
                <div id="mostrarvision"></div>
                <h6>Ojo derecho</h6>
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <label>Esferico</label>
                            <input type="text" name="esfericod" class="form-control {!! $errors->first('esfericod','is-invalid')!!}"  placeholder="Esferico"  readonly
                                   value="{{ $datosHistorial[0]->esfericoder}}">
                            {!! $errors->first('esfericod','<div class="invalid-feedback">Valor no valido</div>')!!}
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Cilindro</label>
                            <input type="text" name="cilindrod" class="form-control {!! $errors->first('cilindro','is-invalid')!!}"  placeholder="Cilindro" readonly
                                   value="{{ $datosHistorial[0]->cilindroder}}">
                            {!! $errors->first('cilindro','<div class="invalid-feedback">Valor no valido</div>')!!}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Eje</label>
                            <input type="text" name="ejed" class="form-control {!! $errors->first('ejed','is-invalid')!!}"  placeholder="Eje" readonly value="{{ $datosHistorial[0]->ejeder}}">
                            {!! $errors->first('ejed','<div class="invalid-feedback">Valor no valido</div>')!!}
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Add</label>
                            <input type="text" name="addd" class="form-control {!! $errors->first('addd','is-invalid')!!}"  placeholder="Add" readonly value="{{ $datosHistorial[0]->addder}}">
                            {!! $errors->first('addd','<div class="invalid-feedback">Valor no valido</div>')!!}
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>ALT</label>
                            <input type="text" name="altd" class="form-control {!! $errors->first('altd','is-invalid')!!}"  placeholder="ALT." readonly value="{{ $datosHistorial[0]->altder}}">
                            {!! $errors->first('altd','<div class="invalid-feedback">Valor no valido</div>')!!}
                        </div>
                    </div>
                </div>
                <h6>Ojo Izquierdo</h6>
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <label>Esferico</label>
                            <input type="text" name="esfericod2" class="form-control {!! $errors->first('esfericod','is-invalid')!!}"  placeholder="Esferico" readonly
                                   value="{{ $datosHistorial[0]->esfericoizq}}">
                            {!! $errors->first('esfericod','<div class="invalid-feedback">Valor no valido</div>')!!}
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Cilindro</label>
                            <input type="text" name="cilindrod2" class="form-control {!! $errors->first('cilindro','is-invalid')!!}"  placeholder="Cilindro" readonly
                                   value="{{ $datosHistorial[0]->cilindroizq}}">
                            {!! $errors->first('cilindro','<div class="invalid-feedback">Valor no valido</div>')!!}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Eje</label>
                            <input type="text" name="ejed2" class="form-control {!! $errors->first('ejed','is-invalid')!!}"  placeholder="Eje" readonly value="{{ $datosHistorial[0]->ejeizq}}">
                            {!! $errors->first('ejed','<div class="invalid-feedback">Valor no valido</div>')!!}
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Add</label>
                            <input type="text" name="addd2" class="form-control {!! $errors->first('addd','is-invalid')!!}"  placeholder="Add" readonly value="{{ $datosHistorial[0]->addizq}}">
                            {!! $errors->first('addd','<div class="invalid-feedback">Valor no valido</div>')!!}
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>ALT</label>
                            <input type="text" name="altd2" class="form-control {!! $errors->first('altd','is-invalid')!!}"  placeholder="ALT." readonly value="{{ $datosHistorial[0]->altizq}}">
                            {!! $errors->first('altd','<div class="invalid-feedback">Valor no valido</div>')!!}
                        </div>
                    </div>
                </div>
                <h6>Material</h6>
                <div class="row">
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="material" id="material" value="Hi Index" @if($datosHistorial[0]->material == 0) checked @endif>
                            <label class="form-check-label" for="material">Hi Index</label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="material" id="material" value="CR" @if($datosHistorial[0]->material == 1) checked @endif>
                            <label class="form-check-label" for="material">CR</label>
                        </div>
                    </div>
                    <!-- <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="material" id="material" value="Policarbonato" @if($datosHistorial[0]->material == 2) checked @endif>
                            <label class="form-check-label" for="material">Policarbonato</label>
                        </div>
                    </div> -->
                    <div class="col-2">
                        <div class="form-check">
                            <input  class="form-check-input" type="radio" name="material" id="material" value="otro" @if($datosHistorial[0]->material == 3) checked @endif>
                            <label class="form-check-label" for="material">Otro</label>
                        </div>
                    </div>
                        <div class="col-3">
                        <div class="form-check">
                            <input type="text" name="motro" class="form-control {!! $errors->first('motro','is-invalid')!!}"  placeholder="Otro" readonly value="{{ $datosHistorial[0]->materialotro}}">
                            {!! $errors->first('motro','<div class="invalid-feedback">Solo se permite con la opción "otro".</div>')!!}
                        </div>
                        </div>
                        <div class="col-3">
                           <div class="form-check">
                            <input type="number" name="costomaterial" class="form-control {!! $errors->first('costo','is-invalid')!!}" min="0"  placeholder="Precio/Costo" readonly
                                   value="{{ $datosHistorial[0]->costomaterial}}">
                            {!! $errors->first('costo','<div class="invalid-feedback"> obligatorio.</div>')!!}
                           </div>
                        </div>
                    </div>
                </div>
                <h6>Tipo de bifocal</h6>
                <div class="row">
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" value="FT" @if($datosHistorial[0]->bifocal == 0) checked @endif>
                            <label class="form-check-label" for="exampleRadios1">
                                FT
                            </label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" value="Blend" @if($datosHistorial[0]->bifocal == 1) checked @endif >
                            <label class="form-check-label" for="exampleRadios1">
                                Blend
                            </label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" value="Progresivo" @if($datosHistorial[0]->bifocal == 2) checked @endif >
                            <label class="form-check-label" for="exampleRadios1">
                                Progresivo
                            </label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" value="N/A" @if($datosHistorial[0]->bifocal == 3) checked @endif >
                            <label class="form-check-label" for="exampleRadios1">
                                N/A
                            </label>
                        </div>
                    </div>
                </div>
                <h6>Tratamiento</h6>
                <div class="row">
                    <div class="col-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input {!! $errors->first('fotocromatico','is-invalid')!!}" name="fotocromatico" id="customCheck9"  value="1"
                                   @if($datosHistorial[0]->fotocromatico != null) checked @endif>
                            <label class="custom-control-label" for="customCheck9">Fotocromatico</label>
                            @if($errors->has('fotocromatico'))
                            <div class="invalid-feedback">{{$errors->first('fotocromatico')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input {!! $errors->first('ar','is-invalid')!!}" name="ar" id="customCheck10" value="1"
                                   @if($datosHistorial[0]->ar !== null) checked @endif>
                            <label class="custom-control-label" for="customCheck10">A/R</label>
                            @if($errors->has('ar'))
                            <div class="invalid-feedback">{{$errors->first('ar')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input {!! $errors->first('tinte','is-invalid')!!}" name="tinte"  id="customCheck11"  value="1"
                                   @if($datosHistorial[0]->tinte != null) checked @endif>
                            <label class="custom-control-label" for="customCheck11">Tinte</label>
                            @if($errors->has('tinte'))
                            <div class="invalid-feedback">{{$errors->first('tinte')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input {!! $errors->first('blueray','is-invalid')!!}" name="blueray" id="customCheck12"  value="1"
                                   @if($datosHistorial[0]->blueray != null) checked @endif>
                            <label class="custom-control-label" for="customCheck12">BlueRay</label>
                            @if($errors->has('blueray'))
                            <div class="invalid-feedback">{{$errors->first('blueray')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="custom-control custom-checkbox">
                            <input  class="custom-control-input" type="checkbox" name="otroTra" id="customCheck13" value="1" @if($datosHistorial[0]->otroT == 1) checked @endif>
                            <label class="custom-control-label" for="customCheck13">Otro</label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="custom-control custom-checkbox">
                            <input type="text" name="otroT" class="form-control {!! $errors->first('otroT','is-invalid')!!}" min="0"  placeholder="Otro"
                                   value="{{ $datosHistorial[0]->tratamientootro}}">
                            {!! $errors->first('otroT','<div class="invalid-feedback">Solo permitido si eliges la opción de otro, elegir su precio también</div>')!!}
                        </div>
                        </div>
                        <div class="col-2">
                           <div class="custom-control custom-checkbox">
                            <input type="number" name="costoT" class="form-control {!! $errors->first('costoT','is-invalid')!!}" min="0"  placeholder="Precio/Costo"
                                   value="{{ $datosHistorial[0]->costotratamiento}}">
                            {!! $errors->first('costoT','<div class="invalid-feedback">solo si eliges la opción otro.</div>')!!}
                           </div>
                        </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                        <label>Observaciones laboratorio</label>
                        <input type="text" name="observaciones" class="form-control {!! $errors->first('observaciones')!!}"  placeholder="Observaciones"  readonly
                               value="{{ $datosHistorial[0]->observaciones}}">
                        {!! $errors->first('observaciones','<div class="invalid-feedback">La descripcion es obligatoria.</div>')!!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label>Observaciones interno</label>
                            <input type="text" name="observacionesinterno" class="form-control {!! $errors->first('observacionesinterno')!!}"  placeholder="observacionesinterno"  readonly
                                   value="{{ $datosHistorial[0]->observacionesinterno}}">
                            {!! $errors->first('observacionesinterno','<div class="invalid-feedback">La descripcion es obligatoria.</div>')!!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <a href="{{route('vercontrato',[$idFranquicia,$datosHistorial[0]->id_contrato])}}" class="btn btn-outline-success btn-block">@lang('mensajes.regresar')</a>
                    </div>
                    <div class="col-8">
                        <button class="btn btn-outline-success btn-block"  name="btnSubmit" type="submit">@lang('mensajes.mensajengenerar')</button>
                    </div>
                </div>
        </form>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
