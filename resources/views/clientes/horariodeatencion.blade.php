@extends('layouts.appclientes')
@section('titulo','Horario de atención'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="row" style="margin-top: 25px;">
        <img src="/imagenes/general/clientes/horariodeatencion_armazon.jpg" style="width: 100%; height: 100%; max-height: 407px; object-fit: cover;">
    </div>
    <div class="row" style="display: grid; justify-items: center;">
        <div class="col-7 mt-5"><label class="header-text">NUESTRAS SUCURSALES.</label></div>
        <div class="col-7 description-text">Tenemos la fortuna de contar con 10 sucursales en México,
            donde estamos transformando vidas en varios estados, incluyendo Nayarit, Sinaloa, Estado de México y Jalisco.
            Nuestra misión es llevar a cabo de manera constante campañas de salud visual, en las cuales realizamos exámenes de la vista completos directamente en el hogar de nuestros clientes.
            El propósito fundamental de estas campañas es identificar y abordar oportunamente cualquier problema visual que pueda estar afectando la salud de nuestros clientes,
            con el objetivo de prevenir accidentes y mejorar significativamente su calidad de vida.</div>
        <div class="col-7 mt-5"><label class="header-text">HORARIOS DE ATENCIÓN</label></div>
        <div class="col-7">
            <div class="row">
                <div class="col-8">
                    <div class="row">
                        <div class="col-6 description-text">
                            <p class="oblique">Visítanos</p>
                            <p>Lun - Vie: 9  a.m. - 6  p.m.</p>
                            <p>Sábado: 10  a.m. - 2 p.m.</p>
                            <p>Domingo: Cerrado</p>
                        </div>
                        <div class="col-6 description-text">
                            <p class="oblique">Contacto</p>
                            <p>Por whatsapp - 322 384 1987</p>
                            <p>Por llamada - 311 223 2483</p>
                            <p>Correo: marketing@luzatuvida.com.mx</p>
                        </div>
                    </div>
                </div>
                <div class="col-4" style="display: flex; justify-content: center; ">
                    <i class="bi bi-clock fa-10x" style="color: #045679;"></i>
                </div>
            </div>
        </div>
        <div class="col-12" style="background: rgba(234,232,232,0.78);">
            <div class="row" style="height: 450px; margin: 60px; display: flex; justify-content: flex-end; align-content: center; position: relative;">
                <div class="col-7">
                    <div class="row">
                        <img src="/imagenes/general/clientes/horarioatencion_tinte.jpg" style="width: 100%; height: 100%; max-height: 400px; object-fit: cover;">
                    </div>
                </div>
                <div class="col-8" style="position: absolute; bottom:0%;  left:0px; height: 350px; background: white;">
                    <div class="row" style="display: flex; justify-content: center; margin-top: 50px;">
                        <div class="col-8" style="display: flex; justify-content: center; flex-direction: column; height: 250px;">
                            <div class="row" style="display: flex; flex-direction: column; align-items: center;">
                                <div class="header-text">SUCURSAL MÁS CERCANA </div>
                                <div class="description-text">"Tu satisfacción es nuestro objetivo principal" </div>
                            </div>
                            <div class="row" style="margin-top: 50px;">
                                <input type="hidden" id="idFranquicia" name="idFranquicia" value="{{$idFranquicia}}">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="description-text">CIUDAD</label>
                                        <input type="text" name="ciudad" id="ciudad" class="form-control"  placeholder="CIUDAD" value="{{ old('ciudad') }}" style="font-size: 14px">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="description-text">ESTADO</label>
                                        <input type="text" name="estado" id="estado" class="form-control"  placeholder="ESTADO" value="{{ old('estado') }}" style="font-size: 14px">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display: flex; justify-content: center;">
                                <div class="col-4">
                                    <button type="button" class="btn btn-danger btn-block" onclick="verMiUbicacion()">Ir a mi ubicación</button>
                                </div>
                                <div class="col-8">
                                    <button type="button" class="btn btn-dark btn-block" onclick="filtrarListaSucursales()">Buscar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-7 mt-5">
            <div class="12" style="display:flex; justify-content: center;"> <label style="font-size: 30px; font-family: Serif;">VISITANOS Y LE ATENDEREMOS CON GUSTO</label></div>
            <div class="row" style="display: flex; flex-wrap: wrap;" id="divSucursalesVisitar">
                @if($franquicias != null)
                    @foreach($franquicias as $franquicia)
                        @if($franquicia->id != $idFranquicia)
                            <div class="col-4 mb-3" style="display: grid; justify-items: center;">
                                <p style="text-align: center; font-family: serif; text-transform: uppercase; font-size: 14px;">SUCURSAL <br> {{$franquicia->ciudad}}, {{$franquicia->estado}} <br> CALLE {{$franquicia->calle}} @if($franquicia->numero != null) NUMERO {{$franquicia->numero}} @endif
                                    <br> COLONIA {{$franquicia->colonia}} <br> @if($franquicia->telefonoatencionclientes != null) TEL: {{$franquicia->telefonoatencionclientes}} @endif
                                    <br> @if($franquicia->whatsapp != null) WHATSAPP: {{$franquicia->whatsapp}} @endif</p>
                            </div>
                        @else
                            <div class="col-4 mb-3">
                                <p style="text-align: center; font-family: serif; font-weight: bold; text-transform: uppercase; font-size: 14px;">MATRIZ <br> TEPIC, NAYARIT <br> CALLE DURANGO NORTE  #357 <br> COLONIA CENTRO <br> TEL: 3113429347 <br> WHATSAPP: 3223841987</p>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>

        </div>
    </div>

    <!--Mapa de sucursales existentes-->
    <input type="hidden" id="jsonSucursales" value="{{json_encode($franquicias)}}">
    <div class="row">
        <div id="mapMarcaderesSucursales" style="width: 100%; height: 500px;">

        </div>
    </div>

    <div class="row mt-1">
        <div style="position: relative; width: 100%; display:grid; justify-items:center;">
            <img src="/imagenes/general/clientes/horarioatencion_armazon2.jpg" style="width: 100%; height: 100%; max-height: 346px; object-fit: cover;">

            <div class="col-8" style="position: absolute; top: 70%; background: white">
                <div class="col-12">
                    <div class="row p-1" style="display: flex; justify-content: space-evenly;">
                        <div class="col-3 description-text" style="display: grid; justify-items: center;">
                            <p>Llamar</p>
                            <p class="oblique">311 223 2483 </p>
                        </div>
                        <div class="col-3 description-text" style="display: grid; justify-items: center;">
                            <p>Email</p>
                            <p class="oblique">marketing@luzatuvida.com.mx</p>
                        </div>
                        <div class="col-3 description-text" style="display: grid; justify-items: center;">
                            <p>Seguir</p>
                            <div class="row">
                                <a href="https://wa.me/523223841987" target="_blank">
                                    <button type="button" class="btn btn-sm"><i class="bi bi-whatsapp" style="color: black; font-size: 20px;"></i></button>
                                </a>
                                <a href="https://www.facebook.com/laboratorioopticoLATVTepic?mibextid=LQQJ4d" target="_blank">
                                    <button type="button" class="btn btn-sm"><i class="bi bi-facebook" style="color: black; font-size: 20px;"></i></button>
                                </a>
                                <a href="https://www.facebook.com/laboratorioopticoLATVTepic?mibextid=LQQJ4d" target="_blank">
                                    <button type="button" class="btn btn-sm"><i class="bi bi-instagram" style="color: black; font-size: 20px;"></i></button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
