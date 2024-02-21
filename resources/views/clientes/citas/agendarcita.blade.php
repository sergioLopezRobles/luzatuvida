@extends('layouts.appclientes')
@section('titulo','Agendar cita'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <input type="hidden" value="{{$idFranquicia}}" id="idFranquicia">
        <div class="row" style="justify-content: center; margin-top: 30px;">
            <div style="background-color: #0AA09E; width: 50% ;color: white; padding: 20px;">
                <p style="margin-bottom: 0px">Atención a clientes contactanos en nuestro @if($franquicia[0]->whatsapp != null) <b>WhatsApp: {{$franquicia[0]->whatsapp}}. </b> @endif</p>
                <p style="margin-bottom: 0px">Horario de atención: Lunes a Viernes de 9:00 am a 5:00 pm. Sábados de 9:00 am a 3:00 pm.</p>
                <p style="margin-bottom: 0px">Si tienes dudas sobre cómo programar tu cita puedes llamar a nuestro atención a clientes <b>{{$franquicia[0]->telefonoatencionclientes}}</b>.</p>
                <p style="margin-bottom: 0px">Horarios de atención: <b>Lunes a Viernes 8:00 am a 8:00 pm y Sábado 8:00 am a 3:00 pm.</b> </p>
            </div>
        </div>
        <div class="row" style="margin-top: 30px;">
            <!-- Calendario -->
            <div class="col-5" style="justify-content: center;">
                <p style="font-weight: bold; font-size: 24px; text-align: center; font-family:Californian FB; text-transform: uppercase;">Calendario citas</p>
                <div id='calendar' style="width: 90%; color: black;"></div>
            </div>
            <!-- Spinner para lista de horarios disponibles -->
            <div class="col-7" id="spCargando" style="justify-content: center; position: relative;">
                <div class="d-flex justify-content-center" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%)">
                    <div class="spinner-border" style="width: 4rem; height: 4rem; margin-top: 30px;" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                </div>
            </div>
            <!-- Lista de horarios disponibles -->
            <div class="col-7" id="horarioCitas">
                <p style="font-weight: bold; font-size: 24px; text-align: center; font-family:Californian FB; text-transform: uppercase;">Horarios disponibles ({{$franquicia[0]->ciudad}}, {{$franquicia[0]->estado}})</p>
                <div id="listacitasdisponibles" style="max-height: 500px; overflow-y: auto;"> </div>
            </div>
        </div>

        <!--Modal para agendar cita-->
        <div class="modal fade was-validated" id="modalcitaagendar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #0AA09E; justify-content: center;">
                            <p style="color: white; font-weight: bold; font-size: 14px;">AGENDAR CITA</p>
                        </div>
                        <div class="modal-body" style="overflow-y: auto; max-height: 600px;">
                            <div style="alignment: center; font-weight: bold;" id="sucursal"> </div>
                            <div style="margin-bottom: 10px;">
                                <p style="font-size: 12px; text-transform: uppercase; margin-bottom: 0px;" id="telefonoSucursal"></p>
                                <p style="font-size: 12px; text-transform: uppercase; margin-bottom: 0px;" id="telefonoAtencionClientes"></p>
                                <p style="font-size: 12px; text-transform: uppercase; margin-bottom: 0px;" id="whatsappAtencionClientes"></p>
                            </div>
                            <div style="alignment: center; font-size: 14px; font-weight: bold;" id="horarioSeleccionado"> </div>
                            <div style="alignment: center; font-size: 14px;color: rgba(255,15,0,0.2); font-weight: bold;">Para realizar la cancelación de tu cita debes comunicarte a los teléfonos de atención a clientes correspondiente a la sucursal elegida. </div>
                            <div class="col-12" style="background-color: #0AA09E; text-align: center; font-weight: bold; color: white; margin-top: 20px; margin-bottom: 20px;">DATOS DEL PACIENTE</div>
                            <div style="margin-top: 15px;">
                                <div class="row">
                                    <div class="col-10">
                                        <div class="form-group">
                                            <label>Nombre</label>
                                            <input type="text" name="nombre" id="nombre" class="form-control"  placeholder="NOMBRE" value="{{ old('nombre') }}" required>
                                            <div class="invalid-feedback" id="errornombre"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-10">
                                        <div class="form-group">
                                            <label>Teléfono</label>
                                            <input type="text" name="telefono" id="telefono" class="form-control"  placeholder="TEL: 999-999-99-99" value="{{ old('telefono') }}" required>
                                            <div class="invalid-feedback" id="errortelefono"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-10">
                                        <div class="form-group">
                                            <label>Fecha/Hora cita</label>
                                            <input type="text" id="fechaHoraCita" name="fechaHoraCita" class="form-control" value="{{ old('fechaHoraCita') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12" style="background-color: #0AA09E; text-align: center; font-weight: bold; color: white; margin-top: 20px; margin-bottom: 20px;">LUGAR DONDE DESEA QUE REALICEN SU CONSULTA </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-check" style="justify-content: center;">
                                        <input class="form-check-input" type="radio" name="radioExamen"
                                               id="radioExamenSucursal"  value="0" checked {{old('radioExamen') == '0' ? 'checked' : ''}} style="margin-top: 15px;">
                                        <i class="bi bi-buildings-fill fa-2x" style="color:#0AA09E; margin-left: 5px;"></i>
                                        <label class="form-check-label" for="radioExamen">Asistir a sucursal</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="radioExamen"
                                               id="radioExamenDomicilio" value="1"  {{old('radioExamen') == '1' ? 'checked' : ''}} style="margin-top: 15px;">
                                        <i class="bi bi-house-door-fill fa-2x" style="color:#0AA09E; margin-left: 5px;"></i>
                                        <label class="form-check-label" for="radioExamen">Asistir a domicilio</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12" style="background-color: #0AA09E; text-align: center; font-weight: bold; color: white; margin-top: 20px; margin-bottom: 20px;">TIPO DE CITA</div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-check" style="justify-content: center;">
                                        <input class="form-check-input" type="radio" name="rbTipoCita"
                                               id="radioCitaExamen"  value="0" checked {{old('rbTipoCita') == '0' ? 'checked' : ''}} style="margin-top: 15px;">
                                        <i class="bi bi-file-earmark-medical fa-2x" style="color:#0AA09E; margin-left: 5px;"></i>
                                        <label class="form-check-label" for="rbTipoCita">Examen de la vista</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="rbTipoCita"
                                               id="radioCitaArmazon" value="1"  {{old('rbTipoCita') == '1' ? 'checked' : ''}} style="margin-top: 15px;">
                                        <i class="bi bi-eyeglasses fa-2x" style="color:#0AA09E; margin-left: 5px;"></i>
                                        <label class="form-check-label" for="rbTipoCita">Reporte de armazón</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="rbTipoCita"
                                               id="radioCitaGotas" value="2"  {{old('rbTipoCita') == '2' ? 'checked' : ''}} style="margin-top: 15px;">
                                        <i class="bi bi-eyedropper fa-2x" style="color:#0AA09E; margin-left: 5px;"></i>
                                        <label class="form-check-label" for="rbTipoCita">Compra de gotas</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="rbTipoCita"
                                               id="radioCitaOtro" value="3"  {{old('rbTipoCita') == '3' ? 'checked' : ''}} style="margin-top: 15px;">
                                        <i class="bi bi-plus-circle-dotted fa-2x" style="color:#0AA09E; margin-left: 5px;"></i>
                                        <label class="form-check-label" for="rbTipoCita">Otro</label>
                                    </div>
                                </div>
                                <div class="col-12" style="margin-top: 15px;">
                                    <div class="form-group">
                                        <label>Otro</label>
                                        <input type="text" name="otroTipoCita" id="otroTipoCita" class="form-control"  placeholder="OTRO TIPO CITA" value="{{ old('otroTipoCita') }}">
                                        <div class="invalid-feedback" id="errorotrotipocita"></div>
                                    </div>
                                </div>
                            </div>
                            <div id="divDatosCitaDomicilio">
                                <div class="col-12" style="background-color: #0AA09E; text-align: center; font-weight: bold; color: white; margin-top: 20px; margin-bottom: 20px;">DATOS PARA VISITA</div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Localidad</label>
                                            <input type="text" name="localidad" id="localidad" class="form-control"  placeholder="LOCALIDAD" value="{{ old('localidad') }}">
                                            <div class="invalid-feedback" id="errorlocalidad"></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Colonia</label>
                                            <input type="text" name="colonia" id="colonia" class="form-control"  placeholder="COLONIA" value="{{ old('colonia') }}">
                                            <div class="invalid-feedback" id="errorcolonia"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8">
                                        <div class="form-group">
                                            <label>Domicilio</label>
                                            <input type="text" name="domicilio" id="domicilio" class="form-control"  placeholder="DOMICILIO" value="{{ old('domicilio') }}">
                                            <div class="invalid-feedback" id="errordomicilio"></div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Número</label>
                                            <input type="text" name="numero" id="numero" class="form-control"  placeholder="NÚMERO" value="{{ old('numero') }}">
                                            <div class="invalid-feedback" id="errornumero"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Entre calles</label>
                                            <input type="text" name="entrecalles" id="entrecalles" class="form-control"  placeholder="ENTRE CALLES" value="{{ old('entrecalles') }}">
                                            <div class="invalid-feedback" id="errorentrecalles"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-danger" data-dismiss="modal" id="btnCerrarModalAgendarCita">Cancelar</button>
                            <button type="button" class="btn btn-outline-success-client" id="btnNuevaCita" onclick="agendarCita()">Agendar</button>
                        </div>
                    </div>
                </div>
        </div>
        <div id="comprobanteCitaPDF"></div>

    </div>
    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection



