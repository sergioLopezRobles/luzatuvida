@extends('layouts.appclientes')
@section('titulo','Bienvenida'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <input type="hidden" id="idFranquicia" name="idFranquicia" value="{{$idFranquicia}}">
    <input type="hidden" id="fechaActual" name="fechaActual" value="{{$fechaActual}}">
    <div class="row" style="margin-top: 25px;" id="contenedorBienvenida">
        <div id="carouselExampleIndicators" class="col-12 carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                @foreach($imagnesCarrusel as $imagen)
                    <li data-target="#carouselExampleIndicators" data-slide-to="{{$imagen->posicion}}" @if($imagen->posicion == 1) class="active" @endif></li>
                @endforeach
            </ol>
                <div class="carousel-inner">
                    @foreach($imagnesCarrusel as $imagen)
                        <div class="col-12 carousel-item @if($imagen->posicion == 1) active @endif">
                            <div class="row">
                                <img class="d-block w-100" src="{{asset($imagen->imagen)}}" style="height: 357px; object-fit: cover;">
                            </div>
                        </div>
                    @endforeach
                </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only" style="background: black">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
            <div class="col-12" style="display:grid; justify-items:center; position: absolute; top: 25%;">
                <p style="font-size: 71px; font-family: serif; font-style: italic; color: white; font-weight: bold;">LUZ A TU VIDA</p>
                <p style="font-size: 20px; font-family: serif; font-style: oblique; color: white;">Transformando Miradas</p>
            </div>
        </div>
    </div>
    <div class="row" style="background: rgba(248,248,248,0.97); display: flex; align-items: center;">
        <div class="col-6">
            <div class="row" style="display: flex; justify-content: center; margin-bottom: 30px;">
                <div class="col-7">
                    <p class="header-text" style="text-align: center;">SOBRE NOSOTROS</p>
                    <div class="row description-text-bold">
                        Somos un laboratorio óptico con más de 7 años de experiencia, dedicado a la venta, fabricación y
                        distribución de lentes oftalmológicos, distribuidos en varios estados de la República Mexicana.
                    </div>
                    <br>
                    <div class="row description-text-bold">
                        <p>MISIÓN:</p>
                        Proporcionar soluciones ópticas de alta calidad que mejoren la vida de nuestros clientes.
                        Nos esforzamos por brindar un servicio personalizado y profesional que garantice la salud visual y la satisfacción de cada individuo que confía en nosotros.
                        Trabajamos incansablemente para ofrecer productos y servicios innovadores que iluminen y transformen la experiencia visual de nuestros clientes.
                    </div>
                    <br>
                    <div class="row description-text-bold">
                        <p>VISIÓN:</p>
                        Ser reconocidos como líderes en el cuidado de la salud visual y en la industria óptica.
                        Queremos ser la primera elección de las personas cuando busquen soluciones para sus necesidades visuales.
                        Aspiramos a expandir nuestra presencia de manera sostenible, colaborando con la comunidad y aprovechando la última tecnología para mejorar la calidad de vida a través de una visión saludable.
                    </div>
                    <br>
                    <div class="row description-text-bold">
                        <p>VALORES:</p>
                    </div>
                    <ul class="description-text-bold">
                        <li>Excelencia</li>
                        <li>Compromiso</li>
                        <li>Responsabilidad</li>
                        <li>Integridad</li>
                        <li>Calidad</li>
                        <li>Innovación</li>
                        <li>Colaboración</li>
                    </ul>
                    <div style="display:flex; justify-content: center; margin-top: 30px;">
                        <a href="{{route('horariodeatencion')}}" type="button" class="btn btn-outline-dark-client">CONTACTANOS</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="row" style="margin-right: 0px;">
                <img src="/imagenes/general/clientes/bienvenida/examenvisual.jpg" style="width: 100%; height: 100%; max-height: 1100px; object-fit: cover;">
            </div>
        </div>
    </div>

    <!-- Seccion de citas-->
    <div class="row" id="contendorCitas" name="contendorCitas">
        <div class="col-4" style="background: #1d2124; padding-top: 400px; padding-bottom: 400px;">
            <div style="display: flex; justify-content: center"><p class="header-text-white">Haz tu cita y obtén</p></div>
            <div style="display: flex; justify-content: center"><p class="header-text-white">exámen de la vista</p></div>
            <div style="display: flex; justify-content: center"><p class="header-text-white">¡GRATIS!</p></div>
            <div class="row" style="display: flex; justify-content: center; margin-top: 50px;">
                <div class="col-8">
                    <div>
                        <p class="description-text white oblique">Durango Norte 357-A, Centro.</p>
                        <p class="description-text white oblique">Tepic, Nayarit 63000.</p>
                        <br>
                        <p class="description-text white oblique">Tel: 311-223-24-83</p>
                        <p class="description-text white oblique">WhatsApp: 322-384-19-87</p>
                        <p class="description-text white oblique">marketing@luzatuvida.com.mx</p>
                    </div>
                    <div class="row">
                        <a href="https://wa.me/523223841987" target="_blank" class="btn btn-sm" style="text-decoration: none">
                            <i class="bi bi-whatsapp fa-2x" style="color: white;"></i>
                        </a>
                        <a href="https://www.facebook.com/laboratorioopticoLATVTepic?mibextid=LQQJ4d" target="_blank">
                            <button type="button" class="btn btn-sm"><i class="bi bi-facebook fa-2x" style="color: white"></i></button>
                        </a>
                        <a href="https://www.facebook.com/laboratorioopticoLATVTepic?mibextid=LQQJ4d" target="_blank">
                            <button type="button" class="btn btn-sm"><i class="bi bi-instagram fa-2x" style="color: white"></i></button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-8" style="padding-top: 40px; padding-bottom: 40px;">
            <div class="row">
                <div class="col-2"></div>
                <div class="col-8">
                    @include('clientes.citas.agendacita')
                </div>
            </div>
        </div>
    </div>

    <!--Seccion de productos-->
    <div class="row">
        <div class="col-12" style="background: rgba(248,248,248,0.97); padding-top: 30px; padding-bottom: 30px;">
            <div style="display: grid; justify-items: center;">
                <label class="mt-2 header-text">NUESTROS PRODUCTOS</label>
                <label class="description-text oblique">Anteojos increíbles y mucho más</label>
            </div>
        </div>
        <div class="row" style="background: rgba(239,239,239,0.85); display: flex; justify-content: space-evenly;
             width: 100%; padding-top: 10px; padding-bottom: 10px; margin: 0px;">
            <div class="col-3" style="display: grid; justify-items: center;">
                <img src="/imagenes/general/clientes/bienvenida/lente_contacto.png" style="width: 100%; height: 100%; object-fit: cover;">
                <div style="display:grid; justify-items:center; position: absolute; bottom: 10px; font-size: 16px;">
                    <p class="products-title">LENTES DE CONTACTO</p>
                    <p class="footer-seccion-link"><a href="">Información</a></p>
                </div>
            </div>
            <div class="col-3" style="display: grid; justify-items: center;">
                <img src="/imagenes/general/clientes/bienvenida/lentes_sol.png" style="width: 100%; height: 100%; object-fit: cover;">
                <div style="display:grid; justify-items:center; position: absolute; bottom: 10px; font-size: 16px;">
                    <p class="products-title">LENTES DE SOL</p>
                    <p class="footer-seccion-link"><a href="">Información</a></p>
                </div>
            </div>
            <div class="col-3" style="display: grid; justify-items: center;">
                <img src="/imagenes/general/clientes/bienvenida/gafas.png" style="width: 100%; height: 100%; object-fit: cover;">
                <div style="display:grid; justify-items:center; position: absolute; bottom: 10px; font-size: 16px;">
                    <p class="products-title">ANTEOJOS</p>
                    <p class="footer-seccion-link"><a href="">Información</a></p>
                </div>
            </div>
        </div>
    </div>

    <!--Seccion de contactanos-->
    <div class="row" style="display: grid; justify-items: center; padding-top: 30px; padding-bottom: 30px;">
        <label class="header-text">CONTÁCTENOS</label>
        <label class="description-text mt-4">Laboratorio Optico Luz a Tu Vida, Durango Norte, Centro, Tepic, Nayarit, Mexico</label>
        <label class="description-text mt-4">marketing@luzatuvida.com.mx</label>
        <label class="description-text mt-4">3223841987</label>
    </div>
    <div class="row" style="box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.5);">
        <img src="/imagenes/general/clientes/bienvenida/examenvisual_metrica.jpg" style="width: 100%; height: 100%; max-height: 446px; object-fit: cover;">
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection

