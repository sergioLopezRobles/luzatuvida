@extends('layouts.appclientes')
@section('titulo','Cita vacantes'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
<div class="row" style="display: flex; justify-content: center; position: relative; margin-top: 25px;">
    <img src="/imagenes/general/clientes/vacantes/vacantes_fondo.jpg" style="width: 100%; max-height: 500px; object-fit:cover;">
    <div class="col-11" style="position: absolute; top: 13%; background: white;">
        <div class="row">
            <div class="col-4">
                <div class="row">
                    @switch($idRol)
                        @case(4)
                            <img src="/imagenes/general/clientes/vacantes/vacantes_cobranza.jpg" style="width: 100%; max-height: 400px; min-height:400px; object-fit:cover;">
                        @break
                        @case(6)
                            <img src="/imagenes/general/clientes/vacantes/vacantes_administracion.jpg" style="width: 100%; max-height: 400px; min-height:400px; object-fit:cover;">
                            @break
                        @case(13)
                            <img src="/imagenes/general/clientes/vacantes/vacantes_asistente.jpg" style="width: 100%; max-height: 400px; min-height:400px; object-fit:cover;">
                            @break
                    @endswitch
                </div>
            </div>
            <div class="col-8" style="position: relative;">
                <div class="row" style="display:flex; justify-content:center;">
                    <div class="col-8" style="position: absolute; top: 50%; left: 50%;   transform: translate(-50%, -50%);">
                        @switch($idRol)
                            @case(4)
                                <p style="font-size: 56px; font-family: Serif;">¡BIENVENIDOS!</p>
                                <p class="description-text oblique">¡Bienvenido(@) al área de gestor de cobranza en nuestra página web! Estamos emocionados de que estés
                                    considerando unirte a nuestro equipo. Aquí es donde comienza tu emocionante viaje hacia oportunidades profesionales excepcionales.
                                    Al registrarte, estarás un paso más cerca de formar parte de un equipo apasionado y comprometido. Explora las emocionantes posiciones
                                    que tenemos disponibles y da el primer paso para convertir tus habilidades en logros significativos.
                                </p>
                                @break
                            @case(6)
                                <p style="font-size: 56px; font-family: Serif;">¡BIENVENIDA!</p>
                                <p class="description-text oblique">¡Bienvenida al área de administracón de optometrista en nuestra página web! Estamos emocionados de que estés considerando unirte
                                    a nuestro equipo. Aquí es donde comienza tu emocionante viaje hacia oportunidades profesionales excepcionales.
                                    Al registrarte, estarás un paso más cerca de formar parte de un equipo apasionado y comprometido. Explora las
                                    emocionantes posiciones que tenemos disponibles y da el primer paso para convertir tus habilidades en logros significativos.
                                </p>
                                @break
                            @case(13)
                                <p style="font-size: 56px; font-family: Serif;">¡BIENVENIDA!</p>
                                <p class="description-text oblique">¡Bienvenida al área de asistente de optometrista en nuestra página web! Estamos emocionados de que estés considerando unirte
                                    a nuestro equipo. Aquí es donde comienza tu emocionante viaje hacia oportunidades profesionales excepcionales.
                                    Al registrarte, estarás un paso más cerca de formar parte de un equipo apasionado y comprometido. Explora las
                                    emocionantes posiciones que tenemos disponibles y da el primer paso para convertir tus habilidades en logros significativos.
                                </p>
                                @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="background: rgba(248,246,246,0.82); display: flex; justify-content: center;">
    <div class="col-11 mt-5 mb-5" style="background: white;">
        <div class="row" style="display: flex; justify-content: center;">
            <div class="col-8">
                <div class="row" style="padding: 30px;">
                    <div class="col-6" style="position: relative;">
                        <div class="row" style="position: absolute; top: 10%; display: flex; flex-direction: column;">
                            <p style="font-family: Serif; font-size: 40px;">Sucursal Matriz</p>
                            <p style="font-family: Serif; font-size: 26px;">Durango 357-A Centro, <br> 63000 Tepic, Nayarit, <br> México </p>
                            <p class="description-text-lg  oblique">Teléfono <br> 311-223-24-83</p>
                            <p class="description-text-lg  oblique">WhatsApp <br> 322-384-19-87</p>
                            <p class="description-text-lg oblique">Email <br> marketing@luzatuvida.com.mx</p>
                        </div>
                        <div class="row" style="position: relative; top: 94%;">
                            <a class="btn btn-outline-dark btn-block" href="{{route('vacantes',$idFranquicia)}}#contenedorVacantes">REGRESAR</a>
                        </div>
                    </div>
                    <div class="col-6">
                        @include('clientes.citas.formulariocitavacante')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Mapa de sucursales existentes-->
<input type="hidden" id="jsonSucursales" value="{{json_encode($franquicias)}}">
<div class="row" style="display: flex; justify-content: center; position: relative;">
    <div id="mapMarcaderesSucursales" style="width: 100%; height: 500px;">

    </div>
</div>
<div class="row" style="display: flex; justify-content: center; position: relative;">
    <img src="/imagenes/general/clientes/vacantes/vacantes_fondo.jpg" style="width: 100%; max-height: 200px; object-fit:cover;">
    <div class="col-11" style="position: absolute; top: 50%; left: 50%;   transform: translate(-50%, -50%);background: white;">
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
    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
