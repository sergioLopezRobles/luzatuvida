@extends('layouts.appclientes')
@section('titulo','Vacantes'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
<div class="row" style="margin-top: 25px;">
    <div class="col-7">
        <img src="/imagenes/general/clientes/vacantes/vacantes_portada.jpg" style="width: 100%; height: 100%; max-height: 800px; object-fit: cover">
    </div>
    <div class="col-5" style="position: relative;">
        <div class="row" style="display: grid; justify-items: center;">
            <div class="col-8" style="position: absolute; top: 50%; left: 50%;   transform: translate(-50%, -50%);">
                <label class="header-text mb-5">¡Unete a la <br> transformación!</label>
                <p class="description-text"> En Luz a tu Vida, nos esforzamos por llevar la salud visual a todas las personas, constantemente formando convenios con
                    diferentes instituciones y llevando exámenes visuales y productos oftálmicos de calidad, directo al cliente.
                </p>
                <p class="description-text mt-5">¿Cómo Trabajamos? <br>
                    Realizamos con constancia campañas de salud visual, las cuales consisten en la realización de exámenes de la vista completos a los clientes
                    directo a su domicilio, con el fin de detectar y corregir oportunamente padecimientos visuales, que pudieran estar afectando la salud, con
                    el objetivo de prevenir accidentes y mejorar su calidad de vida.
                </p>
            </div>
        </div>
    </div>
</div>
<div class="row p-5" id="contenedorVacantes" name="contenedorVacantes" style="display: grid; justify-items: center;  background: rgba(248,246,246,0.82);">
    <div class="col-7">
        <label class="header-text">Vacantes</label>
        <div class="row" style="display:flex; justify-content: space-between;">
            <div class="col-3 footer-seccion-link" style="display: grid; justify-items: center;">
                <img src="/imagenes/general/clientes/vacantes/ic_asistente.png" style="width: 180px; object-fit:cover;">
                <a class="mt-3" href="{{route('vacantesagendarcita',[$idFranquicia,13])}}"><u>Asistente de Optometrista</u></a>
            </div>
            <div class="col-3 footer-seccion-link" style="display: grid; justify-items: center;">
                <img src="/imagenes/general/clientes/vacantes/ic_cobranza.png" style="width: 180px; object-fit:cover;">
                <a class="mt-3" href="{{route('vacantesagendarcita',[$idFranquicia,4])}}"><u>Gestor de Cobranaza</u></a>
            </div>
            <div class="col-3 footer-seccion-link" style="display: grid; justify-items: center;">
                <img src="/imagenes/general/clientes/vacantes/ic_administracion.png" style="width: 180px; object-fit:cover;">
                <a class="mt-3" href="{{route('vacantesagendarcita',[$idFranquicia,6])}}"><u>Administracion</u></a>
            </div>
        </div>
        <div class="row mt-5" style="display: grid; justify-content: end;">
            <p class="description-text"><b>Mande su solicitud elaborada:</b> <br> marketing@luzatuvida.com.mx </p>
        </div>
    </div>
</div>
    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
