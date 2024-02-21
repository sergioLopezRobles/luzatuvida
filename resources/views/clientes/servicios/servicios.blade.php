@extends('layouts.appclientes')
@section('titulo','Servicios'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div style="margin-top: 20px;">
        <input type="hidden" id="idFranquicia" name="idFranquicia" value="{{$idFranquicia}}">
        <input type="hidden" id="fechaActual" name="fechaActual" value="{{$fechaActual}}">
        <div class="row" style="padding: 50px; background: rgba(248,246,246,0.82)">
            <div class="col-12">
                <div style="display: flex; justify-content: center"><p class="header-text">NUESTROS SERVICIOS</p></div>
                <div style="display: flex; justify-content: center">
                    <p class="description-text oblique">Apasionados por brindar salud visual con la mayor calidad posible.</p>
                </div>
                <div style="display: flex; justify-content: center">
                    <p class="description-text oblique">Agenda tu cita y obten tu examen gratis. Pregunta por nuestras promociones vigentes. </p>
                </div>
                <div style="display: flex; justify-content: center">
                    <a type="button" class="btn btn-dark mt-5" href="#agendarCita">¡Agendar ahora!</a>
                </div>
            </div>
        </div>
        <div class="row" id="agendarCita">
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
        <div class="row" style="display: flex; justify-content: center;">
            <div class="col-7" style="margin-top: 50px;">
                <label class="header-text">SERVICIOS</label>
                <ol class="description-text oblique">
                    <li><b>Fabricación de lentes oftálmicas personalizadas: </b>Diseñamos lentes oftálmicas a medida según las indicaciones médicas de optometristas.
                        Estos lentes se utilizan para corregir problemas visuales como miopía, hipermetropía, astigmatismo y presbicia.</li>
                    <li><b>Montaje de lentes en monturas: </b>Este proceso implica el corte y pulido preciso de las lentes para que encajen perfectamente en las monturas seleccionadas.</li>
                    <li><b>Reparación de gafas: </b>Ofrecemos servicios de reparación para gafas y monturas dañadas, que pueden incluir la sustitución de componentes, ajustes y soldaduras en monturas metálicas.</li>
                    <li><b>Tratamientos y recubrimientos: </b>Aplicamos tratamientos y recubrimientos especializados a las lentes, como anti-reflectantes, anti-rayaduras y tintes,
                        para proteger las lentes y mejorar la calidad de la visión.</li>
                    <li><b>Lentes de contacto: </b>Nos especializamos en la adaptación, fabricación y suministro de lentes de contacto, tanto blandas como rígidas.</li>
                    <li><b>Consultas técnicas: </b>Brindamos asesoramiento técnico en salud visual, orientando a los pacientes sobre las opciones de lentes, materiales y recubrimientos más adecuados para sus necesidades individuales.</li>
                </ol>
            </div>
        </div>
        <div class="row" style="padding:80px; box-shadow: 0px 1px 1px rgba(229,229,229,0.82);">
            <div class="row" style="display:flex; justify-content: space-around; background: rgba(248,246,246,0.82); width: 100%; padding-top: 80px;">
                <div class="col-3" style="position: relative; min-height:375px; min-width:355px;">
                    <div class="row" style="display: flex; justify-content: center;">
                        <img src="/imagenes/general/clientes/servicios/servicios_credito.jpg" style="width: 286px; height: 355px; object-fit:cover;">
                    </div>
                    <div style="background: white; position: absolute; bottom: 0%; right: 15px; padding: 10px; min-width: 60%;">
                        <label class="services-title">Lentes a creadito</label>
                    </div>
                </div>
                <div class="col-3" style="position: relative; min-height:375px; min-width:355px;">
                    <div class="row" style="display: flex; justify-content: center;">
                        <img src="/imagenes/general/clientes/servicios/servicios_examen.jpg" style="width: 286px; height: 355px; object-fit:cover;">
                    </div>
                    <div style="background: white; position: absolute; bottom: 0%; right: 15px; padding: 10px; min-width: 60%;">
                        <label class="services-title">Examen de la vista</label>
                    </div>
                </div>
                <div class="col-3" style="position: relative; min-height:375px; min-width:355px;">
                    <div class="row" style="display: flex; justify-content: center;">
                        <img src="/imagenes/general/clientes/servicios/servicios_campañaescolar.jpg" style="width: 286px; height: 355px; object-fit:cover;">
                    </div>
                    <div style="background: white; position: absolute; bottom: 0%; right: 15px; padding: 10px; min-width: 60%;">
                        <label class="services-title">Campañas escolares</label>
                    </div>
                </div>
            </div>
            <div class="row" style="display:flex; justify-content: space-around; background: rgba(248,246,246,0.82); width: 100%; padding-top: 80px; padding-bottom: 80px;">
                <div class="col-3" style="position: relative; min-height:375px; min-width:355px;">
                    <div class="row" style="display: flex; justify-content: center;">
                        <img src="/imagenes/general/clientes/servicios/servicios_pagoameses.jpg" style="width: 286px; height: 355px; object-fit:cover;">
                    </div>
                    <div style="background: white; position: absolute; bottom: 0%; right: 15px; padding: 10px; min-width: 60%;">
                        <label class="services-title">Meses sin intereses</label>
                    </div>
                </div>
                <div class="col-3" style="position: relative; min-height:375px; min-width:355px;">
                    <div class="row" style="display: flex; justify-content: center;">
                        <img src="/imagenes/general/clientes/servicios/servicios_poliza.jpg" style="width: 286px; height: 355px; object-fit:cover;">
                    </div>
                    <div style="background: white; position: absolute; bottom: 0%; right: 15px; padding: 10px; min-width: 60%;">
                        <label class="services-title">Póliza de seguro</label>
                    </div>
                </div>
                <div class="col-3" style="position: relative; min-height:375px; min-width:355px;">
                    <div class="row" style="display: flex; justify-content: center;">
                        <img src="/imagenes/general/clientes/servicios/servicios_campañaempresarial.jpg" style="width: 286px; height: 355px; object-fit:cover;">
                    </div>
                    <div style="background: white; position: absolute; bottom: 0%; right: 15px; padding: 10px; min-width: 60%;">
                        <label class="services-title">Campañas empresariales</label>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
