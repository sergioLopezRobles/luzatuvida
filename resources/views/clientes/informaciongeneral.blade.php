@extends('layouts.appclientes')
@section('titulo','Sobre mí'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="row" style="margin-top:25px;">
        <img src="/imagenes/general/clientes/sobremi_fondo.jpg" style="width: 100%; height: 100%; height: 386px; object-fit: cover;">
        <div class="col-12" style="display: grid; justify-items: center; position: absolute; top: 90px;">
            <div class="col-5" style="background: rgba(250,248,248,0.85); height: 320px; padding: 20px;">
                <p style="font-size: 2.5rem; font-family: serif; font-weight: bold; text-align: center;">LABORATORIO ÓPTICO <br> "LUZ A TU VIDA"</p>
                <p class="description-text oblique" style="text-align: center;">=TRANSFORMANDO MIRADAS=</p>
                <div class="row" style="display: flex; justify-content: center;">
                    <button type="button" class="btn btn-dark btn-lg">Más información</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-5">
            <div class="row">
                <img src="/imagenes/general/clientes/sobremi_examenvisual.jpg" style="width: 100%; height: 1000px; object-fit: cover;">
            </div>
        </div>
        <div class="col-7" style="display: grid; justify-items: center;">
            <div class="col-7" style="padding-top: 150px; padding-bottom: 100px; display: grid; justify-items: center">
                <p class="header-text fa-bold">Transformando Miradas</p>
                <p class="description-text-lg">Desde que comenzamos en el año 2016, hemos hecho todo lo posible para brindar salud visual de manera accesible,
                    asegurando la calidad de productos para los clientes. </p>
                <p class="description-text-lg">A diario, lo que buscamos es ayudar a las personas que padecen de problemáticas de la vista que muchas de las
                    veces son difíciles de detectar. Es nuestra pasión tratar a los pacientes y asegurar su bienestar. </p>
                <p class="description-text-lg">Nosotros facilitamos el proceso de mejorar la salud visual, esto porque gracias a nuestras campañas,
                    podemos acudir directo a los domicilios de los clientes para así brindar nuestro apoyo.
                </p>
            </div>
        </div>
    </div>
    <div class="row" style="padding: 80px; background: rgba(243,243,243,0.96)">
        <div class="col-8" style="background: white; display: grid; justify-items: center;">
            <div class="col-6" style="margin-top: 50px; padding-bottom: 30px;">
                <p style="font-size: 3rem; font-family: serif;">¡ÚNETE A LA TRANSFORMACION!</p>
                <p style="font-family: Serif; font-size: 26px;">Conoce más de nosotros y únete...</p>
                <p class="description-text oblique">"¡Bienvenido/a a nuestro equipo! Estamos emocionados de contar contigo y esperamos que puedas aportar tu
                    talento y energía a nuestra empresa. Juntos, trabajaremos para lograr nuestros objetivos y continuar creciendo."</p>
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
                <a type="button" class="btn btn-dark mt-5" href="{{route('vacantes',$idFranquicia)}}">Agendar entrevista</a>
            </div>

        </div>
        <div class="col-4">
            <div class="row">
                <img src="/imagenes/general/clientes/sobremi_reclutamiento.jpg" style="width: 100%; height: 639px; object-fit: cover;">
            </div>
        </div>
    </div>
    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
