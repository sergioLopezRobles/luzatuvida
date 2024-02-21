<div class="col-12">
    <div class="row">
        <div class="col-4">
            <label id="leyendaPeriodo" name="leyendaPeriodo" style="color:rgba(255,15,0,0.4); font-weight: bold;"> </label>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <label for="usuarioSeleccionado">Usuarios</label>
            <div class="form-group">
                <select name="usuarioSeleccionado"
                        class="form-control"
                        id="usuarioSeleccionado">
                    @if($usuariosPoliza != null)
                        <option value="" selected>Seleccionar usuario</option>
                        @foreach($usuariosPoliza as $usuario)
                            <option
                                value="{{$usuario->id}}">{{$usuario->nombre}}
                            </option>
                        @endforeach
                    @else
                        <option value="" selected>Sin registros</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label for="">Asistencia</label>
                <select class="custom-select {!! $errors->first('asistencia','is-invalid')!!}" name="asistencia" id="asistencia">
                    <option selected value="">Seleccionar</option>
                    <option value="0">Falta</option>
                    <option value="1">Asistencia</option>
                    <option value="2">Retardo</option>
                </select>
                {!! $errors->first('asistencia','<div class="invalid-feedback">Campo obligatorio </div>')!!}
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label for="">Tipo de asistencia</label>
                <select class="custom-select {!! $errors->first('asistenciaTipo','is-invalid')!!}" name="asistenciaTipo" id="asistenciaTipo">
                    <option selected value="">Seleccionar</option>
                    <option value="0">Entrada</option>
                    <option value="1">Salida</option>
                </select>
                {!! $errors->first('asistenciaTipo','<div class="invalid-feedback">Campo obligatorio </div>')!!}
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <input type="hidden" class="form-control" id="diaActualSeleccionado" name="diaActualSeleccionado" value="{{$diaSeleccionado}}">
                <input type="hidden" class="form-control" id="idPolizaDiaSeleccionado" name="idPolizaDiaSeleccionado" value="{{$idPolizaDiaSeleccionado}}">
                <button type="button" class="btn btn-outline-success btn-block" name="btnRegistrarAsistencia" id="btnRegistrarAsistencia">Aplicar</button>
            </div>
        </div>
        <div class="col-2" id="spCargando2">
            <div class="d-flex justify-content-center">
                <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 25px;" role="status">
                    <span class="visually-hidden"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<table class="table table-bordered table-striped table-sm" style="text-align: center; position: relative; border-collapse: collapse;" id="tblReporteAsistencia">
    <thead>
    <tr>
        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">NOMBRE</th>
        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">LUNES</th>
        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">MARTES</th>
        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">MIERCOLES</th>
        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">JUEVES</th>
        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">VIERNES</th>
        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">SABADO</th>
    </tr>
    </thead>
    <tbody>
    @if($asistenciaUsuarios != null)
        @foreach($asistenciaUsuarios as $asistenciaUsuario)
            <tr>
                <td align='center' style="font-size: 10px;">{{$asistenciaUsuario->name}}</td>
                <td align='center' style="font-size: 10px;">{{$asistenciaUsuario->asistenciaLunes}}</td>
                <td align='center' style="font-size: 10px;">{{$asistenciaUsuario->asistenciaMartes}}</td>
                <td align='center' style="font-size: 10px;">{{$asistenciaUsuario->asistenciaMiercoles}}</td>
                <td align='center' style="font-size: 10px;">{{$asistenciaUsuario->asistenciaJueves}}</td>
                <td align='center' style="font-size: 10px;">{{$asistenciaUsuario->asistenciaViernes}}</td>
                <td align='center' style="font-size: 10px;">{{$asistenciaUsuario->asistenciaSabado}}</td>
            </tr>
        @endforeach
    @else
        <tr>
            <td align='center' style="font-size: 10px;" colspan="7">Sin registros</td>
        </tr>
    @endif
    </tbody>
</table>
