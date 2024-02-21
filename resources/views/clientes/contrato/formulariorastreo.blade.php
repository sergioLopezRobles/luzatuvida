@extends('layouts.appclientes')
@section('titulo','Rastrear contrato'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div style="margin-top: 30px;">
        <form action="{{route('rastrearcontrato')}}" enctype="multipart/form-data" id="formRastreo" name="formRastreo"
              method="POST" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="row" style="display: flex; justify-content: center;">
                <div class="col-8">
                    <h2 class="header-text">RASTREO DE PEDIDO Y ESTADO DE CUENTA</h2>
                    <p class="description-text oblique">Obtén información sobre tu pedido, ingresando los datos de tu cuenta de cliente.</p>
                    <div class="row" style="display: flex; justify-content: center;">
                        <div class="col-10">
                            <div class="row" style="margin-top: 100px;">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label description-sm" for="idContrato"># CONTRATO</label>
                                        <input type="text" id="idContrato" name="idContrato" class="form-control {!! $errors->first('idContrato','is-invalid')!!}"
                                               style="font-size: 18px;" placeholder="99999999999999" value="{{old('idContrato')}}" required/>
                                        {!! $errors->first('idContrato','<div class="invalid-feedback">Numero contrato debe contener 14 digitos.</div>')!!}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label description-sm" for="telefono">TELÉFONO</label>
                                        <input type="text" id="telefono" name="telefono" class="form-control {!! $errors->first('telefono','is-invalid')!!}"
                                               style="font-size: 18px;" placeholder="999-999-99-99" value="{{old('telefono')}}"  required/>
                                        {!! $errors->first('telefono','<div class="invalid-feedback">Numero de teléfono incorrecto.</div>')!!}
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3" style="display: flex; justify-content: center;">
                                <div>
                                    <!--CAPTCHA-->
                                    <!--LOCAL-->
                                    <!--<div class="g-recaptcha" data-sitekey="6Lc-QM4kAAAAAKETeaEjXOltVmqfXXpxGo6SsjdF"></div>-->
                                    <!--SERVIDOR-->
                                    <div class="g-recaptcha" data-sitekey="6LdJ_UIlAAAAAPTH3_rBwPjJqiN3Hk1jewhGo93T"></div>
                                    @if ($errors->has('g-recaptcha-response'))
                                        <span class="feedbak-error">
                                        <strong style="font-size: 80%; color: #e3342f; font-weight: lighter;">Por favor, introduce un captcha correcto.</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row" style="display: flex; justify-content: center; margin-top: 30px;">
                                <button type="submit" class="btn btn-dark btn-block">BUSCAR</button>
                            </div>
                            <div class="row" style="display: flex; justify-content: end">
                                <img src="/imagenes/general/clientes/rastreo.png" style="margin-right: 40px; width: 180px; height: 180px;">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>
        <div class="row" style="background-color: black; height: 400px;">
            <div class="col-12" style="margin-top: 70px;">
                <div style="display: flex; justify-content: center"><p style="font-family: Serif; font-size: 40px; color: white;">-DESCUENTO DE FIDELIDAD-</p></div>
                <div style="display: flex; justify-content: center"><p style="font-family: Serif; font-size: 60px; color: white;">10%, 15% y 20%</p></div>
                <div style="display: flex; justify-content: center">
                    <button class="btn btn-light btn-lg" onclick="">¡Consultar ahora!</button>
                </div>
            </div>
        </div>
    </div>
    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection

