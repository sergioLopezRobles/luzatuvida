<div id="formularioAgendarCita" name = "formularioAgendarCita">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control {!! $errors->first('nombre','is-invalid')!!}"  placeholder="NOMBRE" value="{{ old('nombre') }}" style="font-size: 14px" required>
                {!! $errors->first('nombre','<div class="invalid-feedback">Campo de nombre obligatorio.</div>')!!}
                <div class="invalid-feedback" id="errornombre"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="email" class="form-control {!! $errors->first('email','is-invalid')!!}"  placeholder="CORREO@EJEMPLO.COM" value="{{ old('email') }}" style="font-size: 14px" required>
                {!! $errors->first('email','<div class="invalid-feedback">Campo email obligatorio. Ej: correo@ejemplo.com.</div>')!!}
                <div class="invalid-feedback" id="erroremail"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label>Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control {!! $errors->first('telefono','is-invalid')!!}"  placeholder="TEL: 999-999-99-99" value="{{ old('telefono') }}" style="font-size: 14px" required>
                {!! $errors->first('telefono','<div class="invalid-feedback">Campo telefono obligatorio. Ej: 333-333-33-33.</div>')!!}
                <div class="invalid-feedback" id="errortelefono"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label>Observaciones</label>
                <input type="text" name="observaciones" id="observaciones" class="form-control"  placeholder="OBSERVACIONES" value="{{ old('observaciones') }}" style="font-size: 14px">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6" id="contenedorFecha" name="contenedorFecha">
            <div class="form-group">
                <label>Fecha</label>
                <input type="date" name="fechaCita" id="fechaCita" class="form-control {!! $errors->first('fechaCita','is-invalid')!!}" value="{{ old('fecha') }}" style="font-size: 16px">
                {!! $errors->first('fechaCita','<div class="invalid-feedback">Selecciona una fecha valida.</div>')!!}
            </div>
        </div>
        <div class="col-6" id="contenedorHorario" name="contenedorHorario">
            <div class="form-group">
                <label>Horario</label>
                <input type="text" name="horarioSeleccionado" id="horarioSeleccionado" placeholder="SELECCIONA HORARIO" class="form-control {!! $errors->first('horarioSeleccionado','is-invalid')!!}" value="{{ old('horarioSeleccionado') }}" style="font-size: 14px" readonly>
                {!! $errors->first('horarioSeleccionado','<div class="invalid-feedback">Selecciona un horario disponible de la lista de citas.</div>')!!}
            </div>
        </div>
    </div>
    <div class="col-12" style="background-color: #0AA09E; text-align: center; font-weight: bold; color: white; margin-top: 10px; margin-bottom: 10px; padding: 5px;">LUGAR DONDE DESEA QUE REALICEN SU CONSULTA </div>
    <div class="row">
        <div class="col-6">
            <div class="form-check" style="justify-content: center;">
                <input class="form-check-input" type="radio" name="radioExamen"
                       id="radioExamenSucursal"  value="0" checked {{old('radioExamen') == '0' ? 'checked' : ''}} style="margin-top: 15px;">
                <i class="bi bi-buildings-fill fa-2x" style="color:#0AA09E; margin-left: 5px;"></i>
                <label class="form-check-label description-text" for="radioExamen">Asistir a sucursal</label>
            </div>
        </div>
        <div class="col-6">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="radioExamen"
                       id="radioExamenDomicilio" value="1"  {{old('radioExamen') == '1' ? 'checked' : ''}} style="margin-top: 15px;">
                <i class="bi bi-house-door-fill fa-2x" style="color:#0AA09E; margin-left: 5px;"></i>
                <label class="form-check-label description-text" for="radioExamen">Asistir a domicilio</label>
            </div>
        </div>
    </div>
    <div class="col-12" style="background-color: #0AA09E; text-align: center; font-weight: bold; color: white; margin-top: 10px; margin-bottom: 10px; padding: 5px;">TIPO DE CITA</div>
    <div class="row">
        <div class="col-6">
            <div class="form-check" style="justify-content: center;">
                <input class="form-check-input" type="radio" name="rbTipoCita"
                       id="radioCitaExamen"  value="0" checked {{old('rbTipoCita') == '0' ? 'checked' : ''}} style="margin-top: 15px;">
                <i class="bi bi-file-earmark-medical fa-2x" style="color:#0AA09E; margin-left: 5px;"></i>
                <label class="form-check-label description-text" for="rbTipoCita">Examen de la vista</label>
            </div>
        </div>
        <div class="col-6">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="rbTipoCita"
                       id="radioCitaArmazon" value="1"  {{old('rbTipoCita') == '1' ? 'checked' : ''}} style="margin-top: 15px;">
                <i class="bi bi-eyeglasses fa-2x" style="color:#0AA09E; margin-left: 5px;"></i>
                <label class="form-check-label description-text" for="rbTipoCita">Reporte de armazón</label>
            </div>
        </div>
        <div class="col-6">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="rbTipoCita"
                       id="radioCitaGotas" value="2"  {{old('rbTipoCita') == '2' ? 'checked' : ''}} style="margin-top: 15px;">
                <i class="bi bi-eyedropper fa-2x" style="color:#0AA09E; margin-left: 5px;"></i>
                <label class="form-check-label description-text" for="rbTipoCita">Compra de gotas</label>
            </div>
        </div>
        <div class="col-6">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="rbTipoCita"
                       id="radioCitaOtro" value="3"  {{old('rbTipoCita') == '3' ? 'checked' : ''}} style="margin-top: 15px;">
                <i class="bi bi-plus-circle-dotted fa-2x" style="color:#0AA09E; margin-left: 5px;"></i>
                <label class="form-check-label description-text" for="rbTipoCita">Otro</label>
            </div>
        </div>
        <div class="col-12" style="margin-top: 15px;">
            <div class="form-group">
                <label>Otro</label>
                <input type="text" name="otroTipoCita" id="otroTipoCita" class="form-control"  placeholder="OTRO TIPO CITA" value="{{ old('otroTipoCita') }}" style="font-size: 16px;">
                <div class="invalid-feedback" id="errorotrotipocita"></div>
            </div>
        </div>
    </div>
    <div id="divDatosCitaDomicilio">
        <div class="col-12" style="background-color: #0AA09E; text-align: center; font-weight: bold; color: white; margin-top: 20px; margin-bottom: 20px; padding: 5px;">DATOS PARA VISITA</div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label>Localidad</label>
                    <input type="text" name="localidad" id="localidad" class="form-control"  placeholder="LOCALIDAD" value="{{ old('localidad') }}" style="font-size: 14px;">
                    <div class="invalid-feedback" id="errorlocalidad"></div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label>Colonia</label>
                    <input type="text" name="colonia" id="colonia" class="form-control"  placeholder="COLONIA" value="{{ old('colonia') }}" style="font-size: 14px;">
                    <div class="invalid-feedback" id="errorcolonia"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-8">
                <div class="form-group">
                    <label>Domicilio</label>
                    <input type="text" name="domicilio" id="domicilio" class="form-control"  placeholder="DOMICILIO" value="{{ old('domicilio') }}" style="font-size: 14px;">
                    <div class="invalid-feedback" id="errordomicilio"></div>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Número</label>
                    <input type="text" name="numero" id="numero" class="form-control"  placeholder="NÚMERO" value="{{ old('numero') }}" style="font-size: 14px;">
                    <div class="invalid-feedback" id="errornumero"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>Entre calles</label>
                    <input type="text" name="entrecalles" id="entrecalles" class="form-control"  placeholder="ENTRE CALLES" value="{{ old('entrecalles') }}" style="font-size: 14px;">
                    <div class="invalid-feedback" id="errorentrecalles"></div>
                </div>
            </div>
        </div>
    </div>
    <div style="margin-top: 40px;">
        <button type="button" class="btn btn-outline-success-client btn-block" id="btnAgendar" name="btnAgendar" form="formCitaPaciente" onclick="agendarCitaAdministracion()">Agendar</button>
        <div class="btn-group" role="group" style="display: flex;">
            <button type="button" class="btn btn-outline-danger btn-ok" id="btnCancelo" onclick="notificarCitaPaciente('cancelar')">Cancelar cita</button>
            <button type="button" class="btn btn-outline-success-client btn-ok" id="btnAsistio" onclick="notificarCitaPaciente('asistencia')">Asistio paciente</button>
        </div>
    </div>
</div>
