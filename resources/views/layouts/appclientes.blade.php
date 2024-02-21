<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    {{--    <meta name="viewport" content="width=device-width, initial-scale=1">--}}

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titulo','Luz a tu vida')</title>

    @include('parciales.link')
    @include('parciales.script')
    @include('parciales.spinner')
    @include('parciales.notificaciones')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<div id="app">
    <nav id="navbar" class="navbar navbar-expand-md shadow-sm navbar-custom" style="background: #FFFFFF; max-height: 45px;">
        <div class="row" style="width: 100%; display: flex; position: fixed; z-index: 999; top: 0px; background-color: white;">
            <div class="col-3" style="display:flex; justify-content: flex-end; padding: 5px;">
                <a  href="{{ url('/') }}"><img id="logo" src="/imagenes/general/administracion/logo.png"></a>
            </div>
            <div class="container col-6">
                    <div class="col-12" style="display: flex; justify-content: space-between;">
                        <a href="{{ url('/') }}">
                            <button type="button" class="btn btn-sm link-navegacion">Inicio</button>
                        </a>
                        <a href="{{route('informaciongeneral')}}">
                            <button type="button" class="btn btn-sm link-navegacion">Sobre nosotros</button>
                        </a>
                        <a href="{{route('horariodeatencion')}}">
                            <button type="button" class="btn btn-sm link-navegacion">Horarios de atención</button>
                        </a>
                        <a href="{{route('productoslista')}}">
                            <button type="button" class="btn btn-sm link-navegacion">Productos</button>
                        </a>
                        <a href="{{route('servicioslista')}}">
                            <button type="button" class="btn btn-sm link-navegacion">Servicios</button>
                        </a>
                        <a href="{{route('formulariorastreo')}}">
                            <button type="button" class="btn btn-sm link-navegacion">¿Y mis lentes?</button>
                        </a>
                </div>
            </div>
            <div class="container col-3">
                <div>
                    <a href="https://wa.me/523223841987" target="_blank" class="btn"  style="text-decoration: none">
                        <i class="bi bi-whatsapp" style="font-size: 20px;"></i>
                    </a>
                    <a href="https://www.facebook.com/laboratorioopticoLATVTepic?mibextid=LQQJ4d" target="_blank">
                        <button type="button" class="btn"><i class="bi bi-facebook" style="font-size: 20px;"></i></button>
                    </a>
                    <a href="https://www.facebook.com/laboratorioopticoLATVTepic?mibextid=LQQJ4d" target="_blank">
                        <button type="button" class="btn"><i class="bi bi-instagram" style="font-size: 20px;"></i></button>
                    </a>
                    <a href="https://www.facebook.com/laboratorioopticoLATVTepic?mibextid=LQQJ4d" target="_blank">
                        <button type="button" class="btn invisible"><i class="bi bi-cart2" style="font-size: 20px;"></i></button>
                    </a>
                </div>
            </div>

        </div>
    </nav>
    <main>
        <div id="main">
            <div class="whatsapp-button" style="cursor:pointer;">
                <a href="https://wa.me/523223841987" target="_blank"><img src="/imagenes/general/clientes/whatsapp.png" width="20px" height="20px"></a>
                <i class="bi bi-chevron-compact-right" style="color: white;"></i>
            </div>
            @yield('content')
        </div>
    </main>
    <!--Footer-->
    <div class="row" style="background-color: #ffffff; padding-top: 20px; padding-bottom:20px; ">
        <div class="col-5" style="display: grid; justify-content: end; padding-right: 30px;">
            <div class="col-12">
                <p class="footer-seccion-title">Productos</p>
                <ul style="list-style: none">
                    <li class="footer-seccion-link"><a href="{{route('productoslista')}}">Armazones adaptados a ti</a></li>
                    <li class="footer-seccion-link"><a href="#contenedorMicasLuz">Micas luz</a></li>
                    <li class="footer-seccion-link"><a href="#contenedorGotas">Gotas</a></li>
                </ul>
            </div>
            <div class="col-12">
                <p class="footer-seccion-title">Oficinas a mi alcance</p>
                <ul style="list-style: none">
                    <li class="footer-seccion-link"><a href="{{route('horariodeatencion')}}">Sucursales</a></li>
                    <li class="footer-seccion-link"><a href="{{route('bienvenida')}}#contendorCitas">Examen de la vista gratis.</a></li>
                </ul>
            </div>
        </div>
        <div class="col-2" style="display: grid; justify-items: center;">
            <div class="col-12" style="display: flex; justify-content: center; max-height: 210px;">
                <img src="/imagenes/general/clientes/imagotipo.png" style="width:256px; height: 187px; position:absolute; top: 50%; transform: translateY(-50%)">
            </div>
            <div style="position: absolute; bottom: 10px; font-size: 14px; text-decoration: none; color: black;">
                © 2023 por Luz a Tu Vida. Transformando Miradas
            </div>
        </div>
        <div class="col-5" style="display: grid; justify-content: start; padding-left: 30px;">
            <div class="col-12">
                <p class="footer-seccion-title">Sobre nuestra transformación</p>
                <ul style="list-style: none">
                    <li class="footer-seccion-link"><a href="{{route('bienvenida')}}#contenedorBienvenida">Transformando miradas</a></li>
                    <li class="footer-seccion-link"><a href="{{route('formulariorastreo')}}">¿Y mis lentes?</a></li>
                    <li class="footer-seccion-link"><a href="{{route('vacantes','6E2AA')}}">Unete a la transformación</a></li>
                    <li class="footer-seccion-link"><a href="https://drive.google.com/file/d/1lpaaYNb4Rw6qgFpMrUcEG7tSh_qH9ZSi/view?usp=drive_link" target="_blank">Términos y condiciones</a></li>
                </ul>
            </div>
            <div class="col-12">
                <p class="footer-seccion-title">Contáctanos </p>
                <ul style="list-style: none">
                    <li class="footer-seccion-link"><a href="mailto:marketing@luzatuvida.com.mx">marketing@luzatuvida.com.mx</a></li>
                    <li>
                        <div class="row">
                            <a href="https://wa.me/523223841987" target="_blank" class="btn btn-sm"  style="text-decoration: none">
                                <i class="bi bi-whatsapp" style="font-size: 20px;"></i>
                            </a>
                            <a href="https://www.facebook.com/laboratorioopticoLATVTepic?mibextid=LQQJ4d" target="_blank">
                                <button type="button" class="btn btn-sm"><i class="bi bi-facebook" style="font-size: 20px;"></i></button>
                            </a>
                            <a href="https://www.facebook.com/laboratorioopticoLATVTepic?mibextid=LQQJ4d" target="_blank">
                                <button type="button" class="btn btn-sm"><i class="bi bi-instagram" style="font-size: 20px;"></i></button>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
</body>
</html>
