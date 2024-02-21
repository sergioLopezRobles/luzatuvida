@extends('layouts.appclientes')
@section('titulo','Productos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
        <div class="row" style="display: flex; justify-content: end; margin-top: 25px;">
            <div class="col-4">
                <div class="row">
                    <img src="/imagenes/general/clientes/productos/productos.jpg" style="width: 100%; max-height: 379px; object-fit: cover;">
                </div>
            </div>
            <div class="col-6" style="display: flex; align-items: center;">
                <label style="font-family: Serif; font-size: 50px; font-weight: bold;">Nuestros Productos</label>
            </div>
        </div>
        <div class="row" id="contenedorMicasLuz" name="contenedorMicasLuz">
            <div class="col-6">
                <div class="row" style="display: flex; justify-content: center;">
                        <p align="center" style="font-family: Serif; font-size: 56px; font-style: italic;">¡Micas con <br> tratamientos <br> adaptadas a tus <br> necesidades!</p>
                </div>
                <div class="row" style="display: flex; justify-content: center;">
                    <div class="col-6">
                        <p class="description-text">
                            <b>Fotocromático: </b>Es una tecnología que permite que los lentes cambien automáticamente de claro a oscuro en respuesta a la luz solar,
                            adaptándose así a las condiciones de iluminación. Esto proporciona comodidad visual al reducir el deslumbramiento y proteger los ojos de los rayos UV (ultravioleta).
                        </p>
                    </div>
                </div>
                <div class="row" style="display: flex; justify-content: center;">
                    <div class="col-6">
                        <p class="description-text">
                        <b>Antirreflejante: </b>Es una aplicación técnica que reduce de manera significativa los reflejos no deseados en la superficie de los lentes.
                            Esta tecnología mejora la calidad óptica, proporcionando una visión más nítida y reduciendo el brillo excesivo.
                        </p>
                    </div>
                </div>
                <div class="row" style="display: flex; justify-content: center;">
                    <div class="col-6">
                        <p class="description-text">
                        <b>Blu-Ray: </b>Es un tratamiento avanzado diseñado para filtrar y reducir la exposición a la luz azul emitida por dispositivos digitales y fuentes de luz artificial,
                            como pantallas de computadoras y teléfonos móviles. Este tratamiento ayuda a aliviar la fatiga visual y protege los ojos al mismo tiempo, brindando una visión más cómoda y preservando la salud visual.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="row">
                    <img src="/imagenes/general/clientes/productos/produtos_micas.jpg" style="width: 100%; height: 829px; object-fit:cover;">
                </div>
            </div>
        </div>
        <div class="row" id="contenedorGotas" name="contenedorGotas">
            <dvi class="col-6">
                <div class="row">
                    <div class="col-8">
                        <div class="row">
                            <img src="/imagenes/general/clientes/productos/productos_gotas.jpg" style="width:100%; max-height:434px; object-fit:cover;">
                        </div>
                    </div>
                </div>

            </dvi>
            <dvi class="col-6">
                <div class="row">
                    <div class="col-8">
                        <p style="font-weight: bold; font-family: Serif; font-size: 26px; color: red; margin-top: 30px;">OFERTA ESPECIAL</p>
                        <p style="font-weight: bold; font-family: Serif; font-size: 56px; font-style: oblique;">OPTICLEAR</p>
                        <p class="description-text oblique">
                            Ofrecemos gotas para los ojos con ingredientes naturales que permiten prevenir problemas relacionadas
                            a la carnosidad, irritación, sequedad, vista cansada, conjuntivitis, cataratas y comezón.
                        </p>
                        <button type="button" class="btn btn-dark mt-5">¡Comprar ahora!</button>
                    </div>
                </div>
            </dvi>
        </div>
        <div class="row" style="background: rgba(248,246,246,0.82)">
            <div class="col-4" style="position: relative;">
                <div class="row">
                    <div class="col-7" style="position: absolute; top: 50%; left: 50%;   transform: translate(-50%, -50%);">
                        <div style="display: flex; justify-content: center"><p class="products-title">LENTES DE SOL</p></div>
                        <div style="display: flex; justify-content: center"> <p class="description-text">Armazones con estilo y con tratamientos que defienden tus ojos de los rayos UV (ultravioleta). </p></div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="row" style="display: flex; justify-content: center">
                    <img src="/imagenes/general/clientes/bienvenida/gafas.png" style="width: 70%; max-height: 350px; object-fit:cover;">
                </div>
            </div>
            <div class="col-4" style="position: relative;">
                <div class="row">
                    <div class="col-7" style="position: absolute; top: 50%; left: 50%;   transform: translate(-50%, -50%);">
                        <div style="display: flex; justify-content: center"><p class="products-title">LENTES DE CONTACTO</p></div>
                        <div style="display: flex; justify-content: center">
                            <p class="description-text">Contamos con una gran selección de productos para el cuidado
                                de los ojos. Explore nuestra colección hoy mismo. Esperamos que encuentre lo que busca.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="background: rgba(248,246,246,0.82)">
            <div class="col-4">
                <div class="row" style="display: flex; justify-content: center">
                    <img src="/imagenes/general/clientes/bienvenida/lentes_sol.png" style="width: 70%; max-height: 350px; object-fit:cover;">
                </div>
            </div>
            <div class="col-4" style="position: relative;">
                <div class="row">
                    <div class="col-7" style="position: absolute; top: 50%; left: 50%;   transform: translate(-50%, -50%);">
                        <div style="display: flex; justify-content: center"><p class="products-title">ANTEOJOS</p></div>
                        <div style="display: flex; justify-content: center">
                            <p class="description-text">Lentes elaborados con variedad de armazones a elección del cliente y micas con tratamientos adaptadas a las necesidades del cliente.  </p></div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="row" style="display: flex; justify-content: center">
                    <img src="/imagenes/general/clientes/bienvenida/lente_contacto.png" style="width: 70%; max-height: 350px; object-fit:cover;">
                </div>
            </div>
        </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
