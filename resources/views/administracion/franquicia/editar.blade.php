@extends('layouts.app')
@section('titulo','Inicio'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2 style="text-align: left; color: #0AA09E">@lang('mensajes.mensajeeditarfranquicia')</h2>
        <h4 style="text-align: left; color: #0AA09E">@lang('mensajes.documentos')</h4>
        <form id="frmFranquiciaNueva" action="{{route('actualizarFranquicia',$franquicia[0]->id)}}"
              enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="franquicia">
                <div class="row">
                    <div class="col-4">
                        <img src="{{asset($franquicia[0]->foto)}}" class="img-thumbnail"
                             style="width:100px;height:100px;">
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label>Foto actual</label>
                            <input type="file" name="foto"
                                   class="form-control-file  {!! $errors->first('foto','is-invalid')!!} @if($franquicia[0]->foto != '') is-valid @endif"
                                   accept="image/png">
                            {!! $errors->first('foto','<div class="invalid-feedback">La foto debera estar en formato png.</div>')!!}
                            <div class="valid-feedback"><a class='fas fa-check'></a></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>CURP</label>
                            <input type="file" name="curp"
                                   class="form-control-file {!! $errors->first('curp','is-invalid')!!} @if($franquicia[0]->curp != '') is-valid @endif"
                                   accept="application/pdf">
                            {!! $errors->first('curp','<div class="invalid-feedback">El CURP debera estar en formato png.</div>')!!}
                            <div class="valid-feedback"><a class='fas fa-check'></a></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>RFC</label>
                            <input type="file" name="rfc"
                                   class="form-control-file {!! $errors->first('rfc','is-invalid')!!} @if($franquicia[0]->rfc != '') is-valid @endif"
                                   accept="application/pdf">
                            {!! $errors->first('rfc','<div class="invalid-feedback">El RFC debera estar en formato PDF.</div>')!!}
                            <div class="valid-feedback"><a class='fas fa-check'></a></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label>Alta en Hacienda</label>
                            <input type="file" name="hacienda"
                                   class="form-control-file {!! $errors->first('hacienda','is-invalid')!!} @if($franquicia[0]->hacienda != '') is-valid @endif"
                                   accept="application/pdf">
                            {!! $errors->first('hacienda','<div class="invalid-feedback">La alta en hacienda debera estar en formato PDF.</div>')!!}
                            <div class="valid-feedback"><a class='fas fa-check'></a></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Acta de Nacimiento</label>
                            <input type="file" name="actanacimiento"
                                   class="form-control-file {!! $errors->first('actanacimiento','is-invalid')!!} @if($franquicia[0]->actanacimiento != '') is-valid @endif"
                                   accept="application/pdf">
                            {!! $errors->first('actanacimiento','<div class="invalid-feedback">El acta de nacimiento debera estar en formato PDF.</div>')!!}
                            <div class="valid-feedback"><a class='fas fa-check'></a></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Identificacion Oficial</label>
                            <input type="file" name="identificacion"
                                   class="form-control-file {!! $errors->first('identificacion','is-invalid')!!} @if($franquicia[0]->identificacion != '') is-valid @endif"
                                   accept="application/pdf">
                            {!! $errors->first('identificacion','<div class="invalid-feedback">La identificacion debera estar en formato PDF.</div>')!!}
                            <div class="valid-feedback"><a class='fas fa-check'></a></div>
                        </div>
                    </div>
                </div>
                <hr>
                <h4>@lang('mensajes.direccion')</h4>
                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <label>Estado</label>
                            <input type="text" name="estado"
                                   class="form-control {!! $errors->first('estado','is-invalid')!!} @if($franquicia[0]->estado != '') is-valid @endif"
                                   placeholder="Estado" value="{{$franquicia[0]->estado}}">
                            {!! $errors->first('estado','<div class="invalid-feedback">El nombre del estado es demasiado largo.</div>')!!}
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Ciudad</label>
                            <input type="text" name="ciudad" id="link"
                                   class="form-control {!! $errors->first('ciudad','is-invalid')!!} @if($franquicia[0]->ciudad != '') is-valid @endif"
                                   placeholder="Ciudad" value="{{$franquicia[0]->ciudad}}">
                            {!! $errors->first('ciudad','<div class="invalid-feedback">El nombre de la ciudad es demasiado largo.</div>')!!}
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Colonia</label>
                            <input type="text" name="colonia" id="link"
                                   class="form-control {!! $errors->first('colonia','is-invalid')!!} @if($franquicia[0]->colonia != '') is-valid @endif"
                                   placeholder="Colonia" value="{{$franquicia[0]->colonia}}">
                            {!! $errors->first('colonia','<div class="invalid-feedback">El nombre de la colonia es demasiado largo.</div>')!!}
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Calle</label>
                            <input type="text" name="calle" id="calle"
                                   class="form-control {!! $errors->first('calle','is-invalid')!!} @if($franquicia[0]->calle != '') is-valid @endif"
                                   placeholder="Calle" value="{{$franquicia[0]->calle}}">
                            {!! $errors->first('calle','<div class="invalid-feedback">Nombre de la calle vacio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Entre calles</label>
                            <input type="text" name="entrecalles" id="entrecalles"
                                   class="form-control {!! $errors->first('entrecalles','is-invalid')!!} @if($franquicia[0]->entrecalles != '') is-valid @endif"
                                   placeholder="Entre calles" value="{{$franquicia[0]->entrecalles}}">
                            {!! $errors->first('entrecalles','<div class="invalid-feedback">Nombre de entre calle vacio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Numero Interior/Exterior</label>
                            <input type="number" min="0" name="numero" id="link"
                                   class="form-control {!! $errors->first('numero','is-invalid')!!} @if($franquicia[0]->numero != '') is-valid @endif"
                                   placeholder="Numero Interior/Exterior" value="{{$franquicia[0]->numero}}">
                            {!! $errors->first('numero','<div class="invalid-feedback">El numero es demasiado grande.</div>')!!}
                        </div>
                    </div>
                </div>
                <h4>Teléfonos de atención</h4>
                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <label>Teléfono de sucursal:</label>
                            <input type="text" name="telefonofranquicia"
                                   class="form-control {!! $errors->first('telefonofranquicia','is-invalid')!!}"
                                   placeholder="Teléfono" value="{{$franquicia[0]->telefonofranquicia}}">
                            {!! $errors->first('telefonofranquicia','<div class="invalid-feedback">El teléfono debe contener 10 numeros.</div>')!!}
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Teléfono atención clientes:</label>
                            <input type="text" name="telefonoatencionclientes"
                                   class="form-control {!! $errors->first('telefonoatencionclientes','is-invalid')!!}"
                                   placeholder="Ej: 000-000-00-00" value="{{$franquicia[0]->telefonoatencionclientes}}">
                            {!! $errors->first('telefonoatencionclientes','<div class="invalid-feedback">El teléfono debe contener al menos 10 dígitos. Ej: 000-000-00-00.</div>')!!}
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>WhatsApp:</label>
                            <input type="text" name="whatsapp"
                                   class="form-control {!! $errors->first('whatsapp','is-invalid')!!}"
                                   placeholder="Ej: 000-000-00-00" value="{{$franquicia[0]->whatsapp}}">
                            {!! $errors->first('whatsapp','<div class="invalid-feedback">El teléfono debe contener al menos 10 dígitos. Ej: 000-000-00-00.</div>')!!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <label>Comprobante de domicilio</label>
                            <input type="file" name="comprobante"
                                   class="form-control-file {!! $errors->first('comprobante','is-invalid')!!} @if($franquicia[0]->comprobante != '') is-valid @endif"
                                   accept="application/pdf">
                            {!! $errors->first('comprobante','<div class="invalid-feedback">El comprobante debera estar en formato PDF.</div>')!!}
                            <div class="valid-feedback"><a class='fas fa-check'></a></div>
                        </div>
                    </div>
                    @if(Auth::user()->rol_id == 7)
                        <div class="col-3">
                            <label> Estado franquicia</label>
                            <div class="form-check">
                                <input type="checkbox" name="activo" id="activo" class="form-check-input"
                                       @if($estado[0]->estado == 1) checked @endif>
                                <label class="form-check-label" for="activo">Activo/Inactivo</label>
                            </div>
                        </div>
                    @endif
                    <div class="col-3">
                        <div class="form-group">
                            <label>Coordenadas:</label>
                            <input type="text" name="coordenadas" id="coordenadas"
                                   class="form-control {!! $errors->first('coordenadas','is-invalid')!!}"
                                   placeholder="Ej: 21.51486599,-104.8928769" value="{{$franquicia[0]->coordenadas}}">
                            {!! $errors->first('coordenadas','<div class="invalid-feedback">Verifica el formato con el que ingresaste las coordenadas.</div><div class="invalid-feedback"> Ej: 21.51486599,-104.8928769</div>')!!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label>Observaciones</label>
                            <input type="text" name="observaciones" id="link"
                                   class="form-control {!! $errors->first('observaciones','is-invalid')!!}"
                                   placeholder="Observaciones" maxlength="300"
                                   value="{{$franquicia[0]->observaciones}}">
                            {!! $errors->first('observaciones','<div class="invalid-feedback">Se supero el numero maximo de caracteres.</div>')!!}
                        </div>
                    </div>
                </div>
                <hr>
                <h4>Stripe</h4>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>Clave publicable</label>
                            <input type="text" name="claveP"
                                   class="form-control"
                                   placeholder="Clave publicable" value="{{$franquicia[0]->clavepublicablestripe}}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Clave secreta</label>
                            <input type="text" name="claveS" id="link"
                                   class="form-control"
                                   placeholder="Clave secreta" value="{{$franquicia[0]->clavesecretastripe}}">
                        </div>
                    </div>
                </div>
                <div>
                    <div class="row">
                        <div class="col-4">
                            <a href="{{route('listafranquicia')}}"
                               class="btn btn-outline-success btn-block">@lang('mensajes.volverlistafranquicia')</a>
                        </div>
                        <div class="col-8">
                            <button class="btn btn-outline-success btn-block" name="btnSubmit"
                                    type="submit">@lang('mensajes.actualizar')</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
