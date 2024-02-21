@extends('layouts.app')
@section('titulo','Contrato nuevo'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    @include('parciales.notificaciones')
    <div class="contenedor">
        <h2>@lang('mensajes.mensajenuevohistorialclinico')</h2>

        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label>Optometrista</label>
                    <input type="text" name="nombre" class="form-control" readonly value="{{ $optometristas[0]->name}}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Nombre del paciente:</label>
                    <input type="text" name="nombre" class="form-control" readonly value="{{ $datosContrato[0]->nombre}}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                <label>Calle</label>
                <input type="text" name="calle" class="form-control" readonly value="{{ $datosContrato[0]->calle}}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label>Numero</label>
                    <input type="text" name="numero" class="form-control" readonly value="{{ $datosContrato[0]->numero}}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Departamento</label>
                    <input type="text" name="departamento" class="form-control" readonly value="{{ $datosContrato[0]->depto}}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Al lado de</label>
                    <input type="text" name="alladode" class="form-control" readonly value="{{ $datosContrato[0]->alladode}}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label>Frente a</label>
                    <input type="text" name="frentea" class="form-control" readonly value="{{ $datosContrato[0]->frentea}}">

                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Entre calles</label>
                    <input type="text" name="entrecalles" class="form-control" readonly value="{{ $datosContrato[0]->entrecalles}}">

                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Colonia</label>
                    <input type="text" name="colonia" class="form-control" readonly value="{{ $datosContrato[0]->colonia}}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label>Localidad</label>
                    <input type="text" name="localidad" class="form-control" readonly value="{{ $datosContrato[0]->localidad}}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Telefono</label>
                    <input type="text" name="telefono" class="form-control" readonly value="{{ $datosContrato[0]->telefono}}">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Tipo Casa</label>
                    <input type="text" name="casatipo" class="form-control" readonly value="{{ $datosContrato[0]->casatipo}}">
                </div>
            </div>
        </div>
        <hr>
        <form id="frmnuevohistorialclinico" action="{{route('crearhistorialclinico2',[$idFranquicia,$idContrato])}}"  enctype="multipart/form-data" method="POST"
              onsubmit="btnSubmit.disabled = true;" >
            @csrf
            <div class="franquicia">
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label>Edad</label>
                            <input type="text" name="edad" class="form-control {!! $errors->first('edad','is-invalid')!!}"  placeholder="Edad" readonly value="{{ $historialC[0]->edad}}">
                            {!! $errors->first('edad','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="form-group">
                            <label>Diagnostico</label>
                            <input type="text" name="diagnostico" class="form-control {!! $errors->first('diagnostico','is-invalid')!!}"  placeholder="Diagnostico" readonly
                                   value="{{ $historialC[0]->diagnostico}}">
                            {!! $errors->first('diagnostico','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label>Ocupacion</label>
                            <input type="text" name="ocupacion" class="form-control {!! $errors->first('ocupacion','is-invalid')!!}"  placeholder="Ocupacion" readonly
                                   value="{{ $historialC[0]->ocupacion}}">
                            {!! $errors->first('ocupacion','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Diabetes</label>
                            <input type="text" name="diabetes" class="form-control {!! $errors->first('diabetes','is-invalid')!!}"  placeholder="Diabetes" readonly
                                   value="{{ $historialC[0]->diabetes}}">
                            {!! $errors->first('diabetes','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Hipertension</label>
                            <input type="text" name="hipertension" class="form-control {!! $errors->first('hipertension','is-invalid')!!}"  placeholder="Hipertension" readonly
                                   value="{{ $historialC[0]->hipertension}}">
                            {!! $errors->first('hipertension','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                </div>
                <h6>Molestia</h6>
                <div class="row">
                    <div class="col-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="dolor" id="dolores"  readonly value="1" @if($historialC[0]->dolor == 1) checked @endif>
                            <label class="custom-control-label" for="dolores">Dolor de cabeza</label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="ardor" id="ardores"  readonly value="1" @if($historialC[0]->ardor == 1) checked @endif>
                            <label class="custom-control-label" for="ardores">Ardor de los ojos</label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="golpe" id="golpes"  readonly value="1" @if($historialC[0]->golpeojos == 1) checked @endif>
                            <label class="custom-control-label" for="golpes">Golpe en cabeza</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="otroM" id="customCheck4" readonly value="1" @if($historialC[0]->otroM == 1) checked @endif>
                                <label class="custom-control-label" for="customCheck4">Otro</label>
                            </div>
                            <input type="text" name="molestia" class="form-control {!! $errors->first('molestia','is-invalid')!!}"  placeholder="Otro" readonly
                                   value="{{ $historialC[0]->molestiaotro}}">
                            {!! $errors->first('molestia','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Ultimo examen</label>
                            <input type="date" name="ultimoexamen" class="form-control {!! $errors->first('ultimoexamen','is-invalid')!!}"  placeholder="Ultimo examen" readonly
                                   value="{{ $historialC[0]->ultimoexamen}}">
                            {!! $errors->first('ultimoexamen','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                </div>
                <hr>
                <h2>@lang('mensajes.mensajeproducto')</h2>
                <div class="row">
                    <div class="col-4">
                        <label for="">Producto</label>
                        <select class="custom-select {!! $errors->first('producto','is-invalid')!!}" name="producto">
                            @if(count($armazones) > 0)
                                <option selected value = 'nada'>Seleccionar</option>
                                @foreach($armazones as $armazon)
                                    @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 10)
                                        <option value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                            {{$armazon->nombre}} | {{$armazon->color}} | {{$armazon->piezas}}pza.
                                        </option>
                                    @endif
                                @endforeach
                            @else
                                <option selected>Sin registros</option>
                            @endif
                        </select>
                        {!! $errors->first('producto','<div class="invalid-feedback">Elegir un producto , campo obligatorio </div>')!!}
                    </div>
                    <div class="col-4">
                <div class="form-group">
                    <label>Paquete:</label>
                    <input type="text" name="paquete" class="form-control" readonly value="{{$historialC[0]->nombre}}">
                </div>
            </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Fecha de Entrega</label>
                            <input type="date" name="fechaentrega" class="form-control {!! $errors->first('fechaentrega','is-invalid')!!}"  placeholder="Fecha de entrega"
                                   value="{{ old('fechaentrega') }}">
                            {!! $errors->first('fechaentrega','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                </div>
                <h4>Vision Cercana</h4>
                <h6>Ojo derecho</h6>
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <label>Esferico</label>
                            <input type="text" name="esfericod" class="form-control {!! $errors->first('esfericod','is-invalid')!!}"  placeholder="Esferico" value="{{ old('esfericod') }}">
                            @if($errors->has('esfericod'))
                           <div class="invalid-feedback">{{$errors->first('esfericod')}}</div>
                          @endif
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Cilindro</label>
                            <input type="number" step=".01" name="cilindrod" class="form-control {!! $errors->first('cilindrod','is-invalid')!!}"  placeholder="Cilindro"
                                   value="{{ old('cilindrod',0) }}">
                            @if($errors->has('cilindrod'))
                            <div class="invalid-feedback">{{$errors->first('cilindrod')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Eje</label>
                            <input type="text" name="ejed" class="form-control {!! $errors->first('ejed','is-invalid')!!}"  placeholder="Eje" value="{{ old('ejed') }}">
                            @if($errors->has('ejed'))
                            <div class="invalid-feedback">{{$errors->first('ejed')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Add</label>
                            <input type="text" name="addd" class="form-control {!! $errors->first('addd','is-invalid')!!}"  placeholder="Add" value="{{ old('addd') }}">
                            @if($errors->has('addd'))
                            <div class="invalid-feedback">{{$errors->first('addd')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>ALT</label>
                            <input type="text" name="altd" class="form-control {!! $errors->first('altd','is-invalid')!!}"  placeholder="ALT." value="{{ old('altd') }}">
                            @if($errors->has('altd'))
                            <div class="invalid-feedback">{{$errors->first('altd')}}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <h6>Ojo Izquierdo</h6>
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <label>Esferico</label>
                            <input type="text" name="esfericod2" class="form-control {!! $errors->first('esfericod2','is-invalid')!!}"  placeholder="Esferico" value="{{ old('esfericod2') }}">
                            @if($errors->has('esfericod2'))
                            <div class="invalid-feedback">{{$errors->first('esfericod2')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Cilindro</label>
                            <input type="number" step=".01" name="cilindrod2" class="form-control {!! $errors->first('cilindrod2','is-invalid')!!}"  placeholder="Cilindro"
                                   value="{{ old('cilindrod2',0) }}">
                            @if($errors->has('cilindrod2'))
                            <div class="invalid-feedback">{{$errors->first('cilindrod2')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Eje</label>
                            <input type="text" name="ejed2" class="form-control {!! $errors->first('ejed2','is-invalid')!!}"  placeholder="Eje" value="{{ old('ejed2') }}">
                            @if($errors->has('ejed2'))
                            <div class="invalid-feedback">{{$errors->first('ejed2')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Add</label>
                            <input type="text" name="addd2" class="form-control {!! $errors->first('addd2','is-invalid')!!}"  placeholder="Add" value="{{ old('addd2') }}">
                            @if($errors->has('addd2'))
                            <div class="invalid-feedback">{{$errors->first('addd2')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>ALT</label>
                            <input type="text" name="altd2" class="form-control {!! $errors->first('altd2','is-invalid')!!}"  placeholder="ALT." value="{{ old('altd2') }}">
                            @if($errors->has('altd2'))
                            <div class="invalid-feedback">{{$errors->first('altd2')}}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <h6>Material</h6>
                <div class="row">
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="material" id="material" value="0" checked {{old('material') == '0' ? 'checked' : ''}}>
                            <label class="form-check-label" for="material">Hi Index</label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="material" id="material" value="1" {{old('material') == '1' ? 'checked' : ''}}>
                            <label class="form-check-label" for="material">CR</label>
                        </div>
                    </div>
                    <!-- <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="material" id="material" value="2" {{old('material') == '2' ? 'checked' : ''}}>
                            <label class="form-check-label" for="material">Policarbonato</label>
                        </div>
                    </div> -->
                    <div class="col-2">
                        <div class="form-check">
                            <input  class="form-check-input" type="radio" name="material" id="material" value="3" {{old('material') == '3' ? 'checked' : ''}}>
                            <label class="form-check-label" for="material">Otro</label>
                        </div>
                    </div>
                        <div class="col-3">
                        <div class="form-check">
                            <input type="text" name="motro" class="form-control {!! $errors->first('motro','is-invalid')!!}"  placeholder="Otro" value="{{ old('motro') }}">
                            {!! $errors->first('motro','<div class="invalid-feedback">Solo se permite con la opción "otro".</div>')!!}
                        </div>
                        </div>
                        <div class="col-3">
                           <div class="form-check">
                            <input type="number" name="costomaterial" class="form-control {!! $errors->first('costo','is-invalid')!!}" min="0"  placeholder="Precio/Costo" value="{{ old('costo') }}">
                            {!! $errors->first('costo','<div class="invalid-feedback"> obligatorio.</div>')!!}
                           </div>
                        </div>
                    </div>
                </div>
                <h6>Tipo de bifocal</h6>
                <div class="row">
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" value="0" checked {{old('bifocal') == '0' ? 'checked' : ''}}>
                            <label class="form-check-label" for="exampleRadios1">
                                FT
                            </label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" value="1" {{old('bifocal') == '1' ? 'checked' : ''}}>
                            <label class="form-check-label" for="exampleRadios1">
                                Blend
                            </label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" value="2" {{old('bifocal') == '2' ? 'checked' : ''}}>
                            <label class="form-check-label" for="exampleRadios1">
                                Progresivo
                            </label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" value="3" {{old('bifocal') == '3' ? 'checked' : ''}}>
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
                                {{old('fotocromatico') == '1' ? 'checked' : ''}}>
                            <label class="custom-control-label" for="customCheck9">Fotocromatico</label>
                            @if($errors->has('fotocromatico'))
                            <div class="invalid-feedback">{{$errors->first('fotocromatico')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input  {!! $errors->first('ar','is-invalid')!!}" name="ar" id="customCheck10" value="1" checked
                                {{old('ar') == '1' ? 'checked' : ''}}>
                            <label class="custom-control-label" for="customCheck10">A/R</label>
                            @if($errors->has('ar'))
                            <div class="invalid-feedback">{{$errors->first('ar')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input  {!! $errors->first('tinte','is-invalid')!!}" name="tinte"  id="customCheck11"  value="1"
                                {{old('tinte') == '1' ? 'checked' : ''}}>
                            <label class="custom-control-label" for="customCheck11">Tinte</label>
                            @if($errors->has('tinte'))
                            <div class="invalid-feedback">{{$errors->first('tinte')}}</div>
                            @endif

                        </div>
                    </div>
                    <div class="col-1">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="blueray" id="customCheck12"  value="1" {{old('blueray') == '1' ? 'checked' : ''}}>
                            <label class="custom-control-label" for="customCheck12">BlueRay</label>
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="custom-control custom-checkbox">
                            <input  class="custom-control-input" type="checkbox" name="otroTra" id="customCheck13" value="1" {{old('otroTra') == '1' ? 'checked' : ''}}>
                            <label class="custom-control-label" for="customCheck13">Otro</label>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="custom-control custom-checkbox">
                            <input type="text" name="otroT" class="form-control {!! $errors->first('otroT','is-invalid')!!}" min="0"  placeholder="Otro" value="{{ old('otroT') }}">
                            {!! $errors->first('otroT','<div class="invalid-feedback">Solo permitido si eliges la opción de otro, elegir su precio también</div>')!!}
                        </div>
                        </div>
                        <div class="col-2">
                           <div class="custom-control custom-checkbox">
                            <input type="number" name="costoT" class="form-control {!! $errors->first('costoT','is-invalid')!!}" min="0"  placeholder="Precio/Costo" value="{{ old('costoT') }}">
                            {!! $errors->first('costoT','<div class="invalid-feedback">solo si eliges la opción otro.</div>')!!}
                           </div>
                        </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label>Observaciones laboratorio</label>
                            <input type="text" name="observaciones" class="form-control {!! $errors->first('observaciones')!!}"
                                   placeholder="Escribe una observacion para laboratorio" value="{{ old('observaciones') }}">
                            {!! $errors->first('observaciones','<div class="invalid-feedback">La descripcion es obligatoria.
                            </div>')!!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label>Observaciones interno</label>
                            <input type="text" name="observacionesinterno" class="form-control {!! $errors->first('observacionesinterno')!!}"
                                   placeholder="Escribe una observacion para uso interno" value="{{ old('observacionesinterno') }}">
                            {!! $errors->first('observacionesinterno','<div class="invalid-feedback">La descripcion es obligatoria.
                            </div>')!!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <a href="{{route('vercontrato',[$idFranquicia,$idContrato])}}" class="btn btn-outline-success btn-block">@lang('mensajes.regresar')</a>
                    </div>
                    <div class="col-8">
                        <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">@lang('mensajes.mensajengenerar')</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
