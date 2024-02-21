<form id="frmCitaVacante" action="{{route('agendarcitavacantesucursal',$idFranquicia)}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
    @csrf
<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label class="description-text">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control {!! $errors->first('nombre','is-invalid')!!}"  placeholder="NOMBRE" value="{{ old('nombre') }}" style="font-size: 14px">
            {!! $errors->first('nombre','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label class="description-text">Apellidos</label>
            <input type="text" name="apellidos" id="apellidos" class="form-control {!! $errors->first('apellidos','is-invalid')!!}"  placeholder="APELLIDOS" value="{{ old('apellidos') }}" style="font-size: 14px">
            {!! $errors->first('apellidos','<div class="invalid-feedback">Campo apellidos obligatorio.</div>')!!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label class="description-text">Email</label>
            <input type="text" name="email" id="email" class="form-control {!! $errors->first('email','is-invalid')!!}"  placeholder="correo@ejemplo.com" value="{{ old('email') }}" style="font-size: 14px">
            {!! $errors->first('email','<div class="invalid-feedback">Campo email obligatorio. Ej: correo@ejemplo.com.</div>')!!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label class="description-text">Teléfono</label>
            <input type="text" name="telefono" id="telefono" class="form-control {!! $errors->first('telefono','is-invalid')!!}"  placeholder="333-333-33-33" value="{{ old('telefono') }}" style="font-size: 14px">
            {!! $errors->first('telefono','<div class="invalid-feedback">Campo telefono obligatorio. Ej: 333-333-33-33.</div>')!!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label class="description-text">Sucursal</label>
            <select name="sucursalSeleccionada" id="sucursalSeleccionada"
                    class="form-control {!! $errors->first('sucursalSeleccionada','is-invalid')!!}">
                @if(count($franquicias) > 0)
                    <option value="">Seleccionar sucursal</option>
                    @foreach($franquicias as $franquicia)
                        <option
                            value="{{$franquicia->id}}">{{$franquicia->ciudad}}</option>
                    @endforeach
                @else
                    <option selected>Sin registros</option>
                @endif
            </select>
            {!! $errors->first('sucursalSeleccionada','<div class="invalid-feedback">Seleccionada una sucursal valida.</div>')!!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label class="description-text">Vacantes disponibles</label>
            <select name="vacanteSeleccionada" id="vacanteSeleccionada"
                    class="form-control {!! $errors->first('vacanteSeleccionada','is-invalid')!!}">
                    <option value="">Seleccionar vacante</option>
            </select>
            {!! $errors->first('vacanteSeleccionada','<div class="invalid-feedback">Seleccionada una vacante valida.</div>')!!}
            <input type="hidden" id="rolVocanteSeleccionado" name="rolVocanteSeleccionado">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label class="description-text">Elige una fecha</label>
            <input type="date" name="fecha" id="fecha" class="form-control {!! $errors->first('fecha','is-invalid')!!}"
                   value="{{\Carbon\Carbon::parse(\Carbon\Carbon::now())->format("Y-m-d")}}" min="<?= date('Y-m-d'); ?>" style="font-size: 14px">
            {!! $errors->first('cbCondiicones','<div class="invalid-feedback">Seleccionada una fecha valida.</div>')!!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label class="description-text">Subir CV (PDF)</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input {!! $errors->first('curriculum','is-invalid')!!}" name="curriculum" id="curriculum" accept="application/pdf">
                <label class="custom-file-label" for="curriculum" style="font-size: 14px">Choose file...</label>
                {!! $errors->first('curriculum','<div class="invalid-feedback">Curriculum Vitae debera estar en formato PDF.</div>')!!}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input {!! $errors->first('cbCondiicones','is-invalid')!!}" value="1" id="cbCondiicones" name="cbCondiicones">
            <label class="custom-control-label description-text" for="cbCondiicones">Acepto los términos y condiciones</label>
                {!! $errors->first('cbCondiicones','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
        </div>
        </div>
    </div>
</div>
<div style="margin-top: 40px;">
    <button name="btnSubmit" type="submit" class="btn btn-dark btn-block" form="frmCitaVacante">ACEPTAR</button>
</div>
</form>
