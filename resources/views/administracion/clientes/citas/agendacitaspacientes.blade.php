@extends('layouts.app')
@section('titulo','Agenda de citas'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>Agenda pacientes</h2>
        <input type="hidden" id="idFranquicia" value="{{$idFranquicia}}">
        <input type="hidden" id="fechaActual" value="{{\Carbon\Carbon::parse($fechaActual)->format("Y-m-d")}}">
        <input type="hidden" id="rolUsuarioLogueado" value="{{Auth::user()->rol_id}}">

        <div class="row">
                <!-- Lista de citas -->
                <div class="col-6" id="divCitasAgendadas" style="max-height: 600px;">
                    <!--Sucursales-->
                    @if(Auth::user()->rol_id == 7)
                    <div class="row" style="margin-top: 30px;">
                        <div class="col-6" style="justify-content: center;">
                            <div class="form-group">
                                <label>Sucursales:</label>
                                <select name="sucursalSeleccionada"
                                        id="sucursalSeleccionada"
                                        class="form-control">
                                    @if(count($sucursales) > 0)
                                        <option value="">Seleccionar</option>
                                        @foreach($sucursales as $sucursal)
                                            <option
                                                value="{{$sucursal->id}}" {{isset($idFranquicia) ? ($idFranquicia == $sucursal->id ? 'selected' : '' ) : '' }}>{{$sucursal->ciudad}}</option>
                                        @endforeach
                                    @else
                                        <option selected>Sin registros</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="col-12" style="@if(Auth::user()->rol_id == 7) max-height: 490px; @else max-height: 600px; @endif; overflow-y: auto;">
                        <div class="row">
                            <table class="table table-striped table-general table-sm" id="tblAgenda" style="width: 100%; height: 100%; border-collapse: collapse;">
                                <thead id="tblCitasAgendadasEncebezados">
                                <tr>
                                    <th scope="col" colspan="8" id="encabezadotblCitasAgendadas">Lista de citas agendadas</th>
                                </tr>
                                <tr>
                                    <th scope="col" style="max-width: 100px; width: 100px;">Hora cita</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Nombre paciente</th>
                                    <th scope="col">Telefono</th>
                                    <th scope="col">Lugar de consulta</th>
                                    <th scope="col">Tipo de cita</th>
                                    <th scope="col">Descripcion cita</th>
                                </tr>
                                </thead>
                                <tbody id="tblCitasAgendadas">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Spinner para lista de horarios disponibles -->
                    <div class="col-12" id="spCargando" style="justify-content: center; position: relative; margin-top: 20%;">
                        <div class="d-flex justify-content-center" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%)">
                            <div class="spinner-border" style="width: 4rem; height: 4rem; margin-top: 30px;" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6" style="padding-left: 100px; max-height: 600px; overflow-y: auto;">
                    <form action="{{route('agendarcitaadministracion',[$idFranquicia])}}" enctype="multipart/form-data"
                          method="POST" class="was-validated" id="formCitaPaciente"  onsubmit="btnAgendar.disabled = true;">
                        @csrf
                    <input type="hidden" id="idFranquiciaSeleccionadaDirector" name="idFranquiciaSeleccionadaDirector">
                    <input type="hidden" id="indiceCitaPaciente">
                    @include('administracion.clientes.citas.formularioagendarcita')
                    </form>
                </div>
            </div>

            <!--Tabla para historial de movimientos-->
            <div style="margin-top: 50px;">
                <h2>Historial de movimientos </h2>
                <table class="table table-striped table-general table-sm">
                    <thead>
                    <tr>
                        <th scope="col">Usuario</th>
                        <th scope="col">Cambios</th>
                        <th scope="col">Fecha</th>
                    </tr>
                    </thead>
                    <tbody id="tblMovimientos">
                    </tbody>
                </table>
            </div>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
