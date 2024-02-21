<div id="formularioAgendarCita" name = "formularioAgendarCita">
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label class="description-text">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control"  placeholder="NOMBRE" value="{{ old('nombre') }}" required>
                <div class="invalid-feedback" id="errornombre">Hola</div>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label class="description-text">Apellidos</label>
                <input type="text" name="apellidos" id="apellidos" class="form-control"  placeholder="APELLIDOS" value="{{ old('apellidos') }}" required>
                <div class="invalid-feedback" id="errorapellidos"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label class="description-text">Email</label>
                <input type="text" name="email" id="email" class="form-control"  placeholder="CORREO@EJEMPLO.COM" value="{{ old('email') }}" required>
                <div class="invalid-feedback" id="erroremail"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label class="description-text">Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control"  placeholder="TEL: 999-999-99-99" value="{{ old('telefono') }}" required>
                <div class="invalid-feedback" id="errortelefono"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label class="description-text">Observaciones</label>
                <input type="text" name="observaciones" id="observaciones" class="form-control {!! $errors->first('observaciones','is-invalid')!!}"  placeholder="OBSERVACIONES" value="{{ old('observaciones') }}">
                <div class="invalid-feedback" id="errortelefono"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6" id="contenedorFecha" name="contenedorFecha">
            <div class="form-group">
                <label class="description-text">Fecha</label>
                <input type="date" name="fecha" id="fecha" class="form-control" value="{{ old('fecha') }}">
                <div class="invalid-feedback" id="errorfecha"></div>
            </div>
        </div>
        <div class="col-6" id="contenedorHorario" name="contenedorHorario">
            <div class="form-group">
                <label class="description-text">Horario:</label>
                <select name="horarioSeleccionado" id="horarioSeleccionado"
                        class="form-control" required>
                    <option value="">Selecciona horario</option>
                </select>
                <div class="invalid-feedback" id="errorhorario"></div>
            </div>
        </div>
        <div class="col-4" id="spCargando" style="justify-content: center;">
            <div class="d-flex justify-content-center">
                <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 40px;" role="status">
                    <span class="visible"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12" style="background-color: #1d2124; text-align: center; font-weight: bold; color: white; margin-top: 10px; margin-bottom: 10px; padding: 5px;">LUGAR DONDE DESEA QUE REALICEN SU CONSULTA </div>
    <div class="row">
        <div class="col-6">
            <div class="form-check" style="justify-content: center;">
                <input class="form-check-input" type="radio" name="radioExamen"
                       id="radioExamenSucursal"  value="0" checked {{old('radioExamen') == '0' ? 'checked' : ''}} style="margin-top: 15px;">
                <i class="bi bi-buildings-fill fa-2x" style="color:#1d2124; margin-left: 5px;"></i>
                <label class="form-check-label description-text" for="radioExamen">Asistir a sucursal</label>
            </div>
        </div>
        <div class="col-6">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="radioExamen"
                       id="radioExamenDomicilio" value="1"  {{old('radioExamen') == '1' ? 'checked' : ''}} style="margin-top: 15px;">
                <i class="bi bi-house-door-fill fa-2x" style="color:#1d2124; margin-left: 5px;"></i>
                <label class="form-check-label description-text" for="radioExamen">Asistir a domicilio</label>
            </div>
        </div>
    </div>
    <div class="col-12" style="background-color: #1d2124; text-align: center; font-weight: bold; color: white; margin-top: 10px; margin-bottom: 10px; padding: 5px;">TIPO DE CITA</div>
    <div class="row">
        <div class="col-6">
            <div class="form-check" style="justify-content: center;">
                <input class="form-check-input" type="radio" name="rbTipoCita"
                       id="radioCitaExamen"  value="0" checked {{old('rbTipoCita') == '0' ? 'checked' : ''}} style="margin-top: 15px;">
                <i class="bi bi-file-earmark-medical fa-2x" style="color:#1d2124; margin-left: 5px;"></i>
                <label class="form-check-label description-text" for="rbTipoCita">Examen de la vista</label>
            </div>
        </div>
        <div class="col-6">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="rbTipoCita"
                       id="radioCitaArmazon" value="1"  {{old('rbTipoCita') == '1' ? 'checked' : ''}} style="margin-top: 15px;">
                <i class="bi bi-eyeglasses fa-2x" style="color:#1d2124; margin-left: 5px;"></i>
                <label class="form-check-label description-text" for="rbTipoCita">Reporte de armazón</label>
            </div>
        </div>
        <div class="col-6">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="rbTipoCita"
                       id="radioCitaGotas" value="2"  {{old('rbTipoCita') == '2' ? 'checked' : ''}} style="margin-top: 15px;">
                <i class="bi bi-eyedropper fa-2x" style="color:#1d2124; margin-left: 5px;"></i>
                <label class="form-check-label description-text" for="rbTipoCita">Compra de gotas</label>
            </div>
        </div>
        <div class="col-6">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="rbTipoCita"
                       id="radioCitaOtro" value="3"  {{old('rbTipoCita') == '3' ? 'checked' : ''}} style="margin-top: 15px;">
                <i class="bi bi-plus-circle-dotted fa-2x" style="color:#1d2124; margin-left: 5px;"></i>
                <label class="form-check-label description-text" for="rbTipoCita">Otro</label>
            </div>
        </div>
        <div class="col-12" style="margin-top: 15px;">
            <div class="form-group">
                <label class="description-text">Otro</label>
                <input type="text" name="otroTipoCita" id="otroTipoCita" class="form-control"  placeholder="OTRO TIPO CITA" value="{{ old('otroTipoCita') }}">
                <div class="invalid-feedback" id="errorotrotipocita"></div>
            </div>
        </div>
    </div>
    <div id="divDatosCitaDomicilio">
        <div class="col-12" style="background-color: #1d2124; text-align: center; font-weight: bold; color: white; margin-top: 20px; margin-bottom: 20px; padding: 5px;">DATOS PARA VISITA</div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label class="description-text">Localidad</label>
                    <input type="text" name="localidad" id="localidad" class="form-control"  placeholder="LOCALIDAD" value="{{ old('localidad') }}">
                    <div class="invalid-feedback" id="errorlocalidad"></div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label class="description-text">Colonia</label>
                    <input type="text" name="colonia" id="colonia" class="form-control"  placeholder="COLONIA" value="{{ old('colonia') }}">
                    <div class="invalid-feedback" id="errorcolonia"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-8">
                <div class="form-group">
                    <label class="description-text">Domicilio</label>
                    <input type="text" name="domicilio" id="domicilio" class="form-control"  placeholder="DOMICILIO" value="{{ old('domicilio') }}">
                    <div class="invalid-feedback" id="errordomicilio"></div>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label class="description-text">Número</label>
                    <input type="text" name="numero" id="numero" class="form-control"  placeholder="NÚMERO" value="{{ old('numero') }}">
                    <div class="invalid-feedback" id="errornumero"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label class="description-text">Entre calles</label>
                    <input type="text" name="entrecalles" id="entrecalles" class="form-control"  placeholder="ENTRE CALLES" value="{{ old('entrecalles') }}">
                    <div class="invalid-feedback" id="errorentrecalles"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <div class="form-outline mb-8" style="margin-top: 20px; display: flex; justify-content: center;">
                    <!--LOCAL-->
                    <!--<div class="g-recaptcha" data-sitekey="6Lc-QM4kAAAAAKETeaEjXOltVmqfXXpxGo6SsjdF"></div>-->
                    <!--SERVIDOR-->
                    <div class="g-recaptcha" data-sitekey="6LdJ_UIlAAAAAPTH3_rBwPjJqiN3Hk1jewhGo93T"></div>
                    @if ($errors->has('g-recaptcha-response'))
                        <span class="feedbak-error">
                              <strong style="font-size: 80%; color: #e3342f; font-weight: lighter;">Por favor, introduce un captcha correcto.</strong>
                        </span>
                    @endif
                </div>
                <div class="feedbak-error" id="errorcaptcha" name="errorcaptcha" style="font-size: 80%; color: #e3342f; font-weight: lighter; display: flex; justify-content: center;"></div>
            </div>
        </div>
    </div>
    <div style="margin-top: 40px;">
        <button type="button" class="btn btn-outline-dark btn-block" name="btnNuevaCita" id="btnNuevaCita" onclick="agendarCita()">AGENDAR</button>
        <div class="col-12" id="spAgendar" style="justify-content: center;">
            <div class="d-flex justify-content-center">
                <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visible"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="comprobanteCitaPDF"></div>
@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}

