@extends('layouts.app')
@section('titulo','Usuarios'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2 style="text-align: left; color: #0AA09E">@lang('mensajes.mensajeusuariosfranquicia')</h2>
        <div id="accordion">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Nuevo usuario
                        </button>
                    </h5>
                </div>
                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <form action="{{route('nuevoUsuarioFranquicia',$id)}}" class="was-validated" enctype="multipart/form-data" method="POST"
                              id="formNuevoUsuario">
                            @csrf
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" name="nombre" id="nombre" class="form-control {!! $errors->first('nombre','is-invalid')!!}"  placeholder="Nombre" value="{{ old('nombre') }}" required>
                                        {!! $errors->first('nombre','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                                        <div class="invalid-feedback" id="errorNombre"></div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Correo</label>
                                        <input type="email" name="correo" id="correo" class="form-control {!! $errors->first('correo','is-invalid')!!}" placeholder="Correo" value="{{ old('correo') }}" required>
                                        {!! $errors->first('correo','<div class="invalid-feedback">Ejemplo: usuario@luzatuvida.com.</div>')!!}
                                        <div class="invalid-feedback" id="errorCorreo"></div>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-group">
                                        <label>Sueldo</label>
                                        <input type="number" name="sueldo" id="sueldo" class="form-control {!! $errors->first('sueldo','is-invalid')!!}" min="0" placeholder="Sueldo"  value="{{ old('sueldo') }}" required>
                                        {!! $errors->first('sueldo','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                                        <div class="invalid-feedback" id="errorSueldo"></div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Contraseña</label>
                                        <input type="password" name="contrasena" class="form-control {!! $errors->first('contrasena','is-invalid')!!}"  placeholder="Contraseña"  id="password" value="{{old('password')}}" required>
                                        {!! $errors->first('contrasena','<div class="invalid-feedback">La contraseña debe contener: Una letra mayuscula,una minuscula,un numero,un caracter especial y al menos 8 caracteres.</div>')!!}
                                        <div class="invalid-feedback" id="errorContrasena"></div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Confirmar contraseña</label>
                                        <input type="password" name="ccontrasena" class="form-control {!! $errors->first('ccontrasena','is-invalid')!!}" id="password2"  placeholder="Confirmar contraseña" value="{{old('password2')}}" required>
                                        {!! $errors->first('ccontrasena','<div class="invalid-feedback">La contraseña debe contener: Una letra mayuscula,una minuscula,un numero,un caracter especial y al menos 8 caracteres.</div>')!!}
                                        <div class="invalid-feedback" id="errorConfirmarContrasena"></div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div style="margin-left: 5px; margin-right: 5px; margin-top: 30px;">
                                        <a class="btn btn-outline-success btn-block" onclick="generarPassword()">Generar contraseña</a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6"></div>
                                <div class="col-2">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" onchange="mostrarPassword()" id="cbMostrarContrasena">
                                        <label class="custom-control-label" for="cbMostrarContrasena">Mostrar contraseña</label>
                                    </div>
                                </div>
                                <div class="col-4"></div>
                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Seleccionar el Rol</label>
                                        <select name="rol" id="rol" class=" form-control {!! $errors->first('rol','is-invalid')!!} @error('rol') is-invalid @enderror" value="{{old('rol')}}" required>
                                            <option value=""></option>
                                            @foreach($roles as $rol)
                                                @if($rol->id != 7)
                                                    <option value="{{$rol->id}}">{{$rol->rol}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        {!! $errors->first('rol','<div class="invalid-feedback">Selecciona un rol.</div>')!!}
                                        <div class="invalid-feedback" id="errorRol"></div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Seleccionar la zona</label>
                                        <select name="idzona" id="idZona" class="form-control  @error('idzona') is-invalid @enderror" value="{{old('idZona')}}" required>
                                            <option value=""></option>
                                            @foreach($zonas as $zona)
                                                <option value="{{$zona->id}}">{{$zona->zona}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" name="erroridZona" id="erroridZona"></div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Tarjeta</label>
                                        <input type="text" name="tarjeta" id="tarjeta" class="form-control {!! $errors->first('tarjeta','is-invalid')!!}" min="0" placeholder="Tarjeta"  value="{{old('tarjeta')}}" required>
                                        {!! $errors->first('tarjeta','<div class="invalid-feedback">Numero de tarjeta deben ser 16 digitos.</div>')!!}
                                        <div class="invalid-feedback" id="errorTarjeta"></div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Otra tarjeta</label>
                                        <input type="text" name="otratarjeta" id="otratarjeta" class="form-control {!! $errors->first('otratarjeta','is-invalid')!!}" min="0" placeholder="Tarjeta" value="{{ old('otratarjeta') }}" required>
                                        {!! $errors->first('otratarjeta','<div class="invalid-feedback">Numero de tarjeta deben ser 16 digitos.</div>')!!}
                                        <div class="invalid-feedback" id="errorOtraTarjeta"></div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Fecha de nacimiento </label>
                                        <input type="date" name="fechanacimiento" id="fechanacimiento"
                                               class="form-control {!! $errors->first('fechanacimiento','is-invalid')!!}"
                                               value="{{ old('fechanacimiento') }}" max="<?= date('Y-m-d'); ?>" required>
                                        {!! $errors->first('fechanacimiento','<div class="invalid-feedback">Fecha nacimiento obligatoria.</div>')!!}
                                        <div class="invalid-feedback" id="errorFechaNacimiento"></div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Recordatorio </label>
                                        <input type="date" name="fecharenovacion"
                                               class="form-control {!! $errors->first('fecharenovacion','is-invalid')!!}"
                                               placeholder="Fecha de renovación" value="{{ old('fecharenovacion') }}">
                                        @if($errors->has('fecharenovacion'))
                                            <div class="invalid-feedback">{{$errors->first('fecharenovacion')}}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="cbsupervisorcobranza" id="cbsupervisorcobranza">
                                        <label class="custom-control-label" for="cbsupervisorcobranza">Supervisor cobranza</label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Foto del usuario ( FOTO JPG)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input {!! $errors->first('foto','is-invalid')!!}" accept="image/jpg" name="foto" id="foto" value="{{old('foto')}}">
                                            <label class="custom-file-label" for="foto">Choose file...</label>
                                            {!! $errors->first('foto','<div class="invalid-feedback">La foto debera estar en formato JPG.</div>')!!}
                                            <div id="errorFoto"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Acta de nacimiento (PDF)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input {!! $errors->first('actanacimiento','is-invalid')!!}" name="actanacimiento" id="actanacimiento" accept="application/pdf" value="{{old('actanacimiento')}}">
                                            <label class="custom-file-label" for="actanacimiento">Choose file...</label>
                                            {!! $errors->first('actanacimiento','<div class="invalid-feedback">El acta de nacimiento debera estar en formato PDF.</div>')!!}
                                            <div class="invalid-feedback" id="errorActanacimiento"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Identificacion Oficial (PDF)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input {!! $errors->first('identificacion','is-invalid')!!}" name="identificacion" id="identificacion" accept="application/pdf" value="{{old('identificacion')}}">
                                            <label class="custom-file-label" for="identificacion">Choose file...</label>
                                            {!! $errors->first('identificacion','<div class="invalid-feedback">La identificacion debera estar en formato PDF.</div>')!!}
                                            <div class="invalid-feedback" id="errorIdentificacion"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>CURP (FOTO JPG)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input {!! $errors->first('curp','is-invalid')!!}" name="curp" id="curp" accept="image/jpg" value="{{old('curp')}}">
                                            <label class="custom-file-label" for="curp">Choose file...</label>
                                            {!! $errors->first('curp','<div class="invalid-feedback">El CURP debera estar en formato JPG.</div>')!!}
                                            <div class="invalid-feedback" id="errorCurp"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Comprobante de domicilio (PDF)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input {!! $errors->first('comprobante','is-invalid')!!}" name="comprobante" id="comprobante" accept="application/pdf" value="{{old('comprobante')}}">
                                            <label class="custom-file-label" for="comprobante">Choose file...</label>
                                            {!! $errors->first('comprobante','<div class="invalid-feedback">El comprobante debera estar en formato PDF.</div>')!!}
                                            <div class="invalid-feedback" id="errorComprobanteDom"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Seguro social (PDF)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input {!! $errors->first('seguro','is-invalid')!!}" name="seguro" id="seguro" accept="application/pdf" value="{{old('seguro')}}">
                                            <label class="custom-file-label" for="seguro">Choose file...</label>
                                            {!! $errors->first('seguro','<div class="invalid-feedback">El seguro social debera estar en formato PDF.</div>')!!}
                                            <div class="invalid-feedback" id="errorSeguroSocial"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Solicitud / Curriculum (PDF)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input {!! $errors->first('solicitud','is-invalid')!!}" name="solicitud" id="solicitud" accept="application/pdf" value="{{old('solicitud')}}">
                                            <label class="custom-file-label" for="solicitud">Choose file...</label>
                                            {!! $errors->first('solicitud','<div class="invalid-feedback">La solicitud debera estar en formato PDF.</div>')!!}
                                            <div class="invalid-feedback" id="errorSolicitud"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Tarjeta pago (FOTO JPG)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input {!! $errors->first('tarjetapago','is-invalid')!!}" name="tarjetapago" id="tarjetapago" accept="image/jpg" value="{{old('tarjetapago')}}">
                                            <label class="custom-file-label" for="tarjetapago">Choose file...</label>
                                            {!! $errors->first('tarjetapago','<div class="invalid-feedback">El numero de la tarjeta debera estar en formato JPG.</div>')!!}
                                            <div class="invalid-feedback" id="errorTarjetaPago"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Otra tarjeta (FOTO JPG)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input {!! $errors->first('otratarjetapago','is-invalid')!!}" name="otratarjetapago" id="otratarjetapago" accept="image/jpg" value="{{old('otratarjetapago')}}">
                                            <label class="custom-file-label" for="otratarjetapago">Choose file...</label>
                                            {!! $errors->first('otratarjetapago','<div class="invalid-feedback">El numero de la tarjeta debera estar en formato JPG.</div>')!!}
                                            <div class="invalid-feedback" id="errorOtraTarjetaPago"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Contacto de Emergencia (PDF)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input {!! $errors->first('contactoemergencia','is-invalid')!!}" name="contactoemergencia" id="contactoemergencia" accept="application/pdf" value="{{old('contactoemergencia')}}">
                                            <label class="custom-file-label" for="contactoemergencia">Choose file...</label>
                                            {!! $errors->first('contactoemergencia','<div class="invalid-feedback">El contacto de emergencia debera estar en formato PDF.</div>')!!}
                                            <div class="invalid-feedback" id="errorContactoemergencia"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Contrato laboral (PDF)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input {!! $errors->first('contratolaboral','is-invalid')!!}" name="contratolaboral" id="contratolaboral" accept="application/pdf" value="{{old('contratolaboral')}}">
                                            <label class="custom-file-label" for="contratolaboral">Choose file...</label>
                                            {!! $errors->first('contratolaboral','<div class="invalid-feedback">El contrato laboral debera estar en formato PDF.</div>')!!}
                                            <div class="invalid-feedback" id="errorContrato"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Pagare (PDF)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input {!! $errors->first('pagare','is-invalid')!!}" name="pagare" id="pagare" accept="application/pdf" value="{{old('pagare')}}">
                                            <label class="custom-file-label" for="pagare">Choose file...</label>
                                            {!! $errors->first('pagare','<div class="invalid-feedback">El pagare debera estar en formato PDF.</div>')!!}
                                            <div class="invalid-feedback" id="errorPagare"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(Auth::user()->rol_id == 7)
                                <hr>
                                <h5>@lang('mensajes.mensajesucursalesconfirmaciones')</h5>
                                <div class="row">
                                    @php
                                        $i = 0;
                                    @endphp
                                    @foreach($sucursales as $sucursal)
                                        <div class="col-2">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input " name="{{$sucursal->id}}" id="{{$sucursal->id}}" value="1">
                                                <label class="custom-control-label" for="{{$sucursal->id}}">{{$sucursal->colonia}} {{$sucursal->numero}},{{$sucursal->ciudad}} {{$sucursal->estado}}</label>
                                            </div>
                                        </div>
                                        @php
                                            $i = $i +2;
                                        @endphp
                                        @if($i==12)
                                </div>
                                <div class="row">
                                    @php
                                        $i = 0;
                                    @endphp
                                    @endif
                                    @endforeach
                                </div>
                            @endif
                            <button class="btn btn-outline-success btn-block" onclick="validarCamposNuevoUsario()" id="btnNuevoUsuarioFranquicia">@lang('mensajes.agregarusuario')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
            <div class="card">
                <div class="card-header" id="headingTwo">
                    <h5 class="mb-0">
                        <button id="collapsedUsuarioExistente" class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Usuario existente
                        </button>
                    </h5>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                <label >Filtrar usuarios</label>
                                <input type="hidden" id="id_franquicia" name="id_franquicia" value="{{$franquicia[0]->id}}">
                                <input name="filtro" id="filtro" type="text" class="form-control" placeholder="Buscar..">
                            </div>
                            <div class="col-2">
                                <button type="button" id="btnFiltrar" class="btn btn-outline-success btn-ca">Filtrar</button>
                            </div>
                            <div class="col-1" id="spCargando">
                                <div class="d-flex justify-content-center">
                                    <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 30px;" role="status">
                                        <span class="visually-hidden"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <form  action="{{route('nuevoUsuarioFranquicia',$id)}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;" >
                                    @csrf
                                    <div class="row">
                                        <div class="col-8">
                                            <label>Seleciona el usuario existente</label>
                                            <select class="custom-select" name="usuarioP" id="usuariosFranquicia">

                                            </select>
                                        </div>
                                        <div class="card-body" style="margin-top: 10px;">
                                            <div class="btn-group" role="group">
                                                <button type="submit" id="btnAsignar" name="btnSubmit" class="btn btn-outline-success">Asignar</button>
                                                <input type="hidden" id="idFranquiciaActual" name="idFranquiciaActual" value="{{$id}}">
                                                <button type="button" id="btnVerUsuarioExistente" name="btnVerUsuarioExistente" class="btn btn-outline-success">Ver</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3">
                                <label >Buscar usuarios en sucursal</label>
                                <input name="ipBuscar" id="ipBuscar" type="text" class="form-control" placeholder="Buscar..">
                                <input name="idFranquicia" id="idFranquicia" type="hidden" class="form-control" value="{{$franquicia[0]->id}}">
                            </div>
                            <div class="col-1">
                                <button type="button" id="btnBuscar" class="btn btn-outline-success btn-ca">Buscar</button>
                            </div>
                            <div class="col-1" id="spCargandoBuscador">
                                <div class="d-flex justify-content-center">
                                    <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 30px;" role="status">
                                        <span class="visually-hidden"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-1" id="espacioBuscador">

                            </div>
                            <div class="col-4">
                                <label>Usuarios asignados a sucursal</label>
                                <select class="custom-select" name="usuarioAsignados" id="usuarioAsignados">
                                    <option>SIN REGISTROS</option>

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
{{--        Seccion de vacantes para director--}}
        @if(Auth::user()->rol_id == 7)
            <div class="card">
                <div class="card-header" id="vacantes">
                    <h5 class="mb-0">
                        <button id="collapsedVacantes" class="btn btn-link collapsed" data-toggle="collapse" data-target="#vacantesDirector" aria-expanded="false" aria-controls="vacantesDirector">
                            Vacantes
                        </button>
                    </h5>
                </div>
                <div id="vacantesDirector" class="collapse" aria-labelledby="vacantesDirector" data-parent="#accordion">
                    <div class="card-body">
                        <div class="btn-group" role="group">
                                <a class="btn btn-outline-primary" href="{{route('listavacantesadministracion',[$franquicia[0]->id])}}" target="_blank">DESDE ADMINISTRACION</a>
                                <a class="btn btn-outline-primary" href="{{route('listavacantesredes',[$franquicia[0]->id])}}" target="_blank">DESDE REDES</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if(Auth::user()->rol_id == 7)
            <form action="{{route('usuariosfiltrosucursal',$id)}}" enctype="multipart/form-data"
                  method="POST" onsubmit="btnSubmit.disabled = true;" >
                @csrf
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <label>Ver usuarios de:</label>
                            <select name="sucursalSeleccionada"
                                    class="form-control">
                                @if(count($sucursales) > 0)
                                    <option value="">Todas las sucursales</option>
                                    @foreach($sucursales as $sucursal)
                                        <option
                                            value="{{$sucursal->id}}" {{ isset($sucursalSeleccionada) ? ($sucursalSeleccionada == $sucursal->id ? 'selected' : '' ) : '' }}>{{$sucursal->ciudad}}</option>
                                    @endforeach
                                @else
                                    <option selected>Sin registros</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="submit" name="btnSubmit" class="btn btn-outline-success btn-ca">Aplicar</button>
                    </div>
                </div>
            </form>
        @endif
        @if($totalRenovacion != null)
            <button type="button" class="btn btn-primary" style="margin-bottom: 10px;">
                Contratos a renovar <span class="badge bg-secondary">{{$totalRenovacion[0]->total}}</span>
            </button>
        @endif
        <div class="contenedortblUsuarios" style="overflow-x: auto;">
            <table id="tablaFranquicias" class="table-bordered table-striped table-general table-sm">
                @if(sizeof($usuariosfranquicia)>0)
                    <thead>
                    <tr>
                        <th  style =" text-align:center;" scope="col">SUCURSAL</th>
                        <th  style =" text-align:center;" scope="col">FOTO</th>
                        <th  style =" text-align:center;" scope="col">NOMBRE</th>
                        <th  style =" text-align:center;" scope="col">CORREO</th>
                        <th  style =" text-align:center;" scope="col">ROL / ZONA</th>
                        <th  style =" text-align:center;" scope="col">RENOVACION</th>
                        <th  style =" text-align:center;" scope="col">NO. CONTROL</th>
                        <th  style =" text-align:center; white-space: break-spaces;" scope="col">FECHA DE CREACIÓN</th>
                        <th  style =" text-align:center; white-space: break-spaces;" scope="col">FECHA DE CUMPLEAÑOS</th>
                        <th  style =" text-align:center; white-space: break-spaces;" scope="col">ULTIMA CONEXIÓN</th>
                        <th  style =" text-align:center; white-space: break-spaces;" scope="col">ACTA DE NACIMIENTO</th>
                        <th  style =" text-align:center;" scope="col">INE</th>
                        <th  style =" text-align:center;" scope="col">CURP</th>
                        <th  style =" text-align:center;" scope="col">SEGURO</th>
                        <th  style =" text-align:center; white-space: break-spaces;" scope="col">CV/SOLICITUD DE EMPLEO</th>
                        <th  style =" text-align:center;" scope="col">TARJETA</th>
                        <th  style =" text-align:center; white-space: break-spaces;" scope="col">OTRA TARJETA</th>
                        <th  style =" text-align:center;" scope="col">CONTACTO</th>
                        <th  style =" text-align:center;" scope="col">CONTRATO</th>
                        <th  style =" text-align:center;" scope="col">PAGARE</th>
                        <th  style =" text-align:center;" scope="col">ELIMINAR</th>
                        <th  style =" text-align:center;" scope="col">VER</th>
                    </tr>
                    </thead>
                @endif
                <tbody>
                @foreach($usuariosfranquicia as $usuariof)
                    <tr style=" @if(($usuariof->renovacion != null) &&(\Carbon\Carbon::parse($usuariof->renovacion)->format('Y-m-d') <= \Carbon\Carbon::now())) background-color: rgba(255,15,0,0.17)
                        @else
                        @if($usuariof->DIASPARACUMPLEANIOS != null && ($usuariof->DIASPARACUMPLEANIOS == -1)) background-color: rgba(209,156,250,0.85) @endif
                        @if($usuariof->DIASPARACUMPLEANIOS != null && ($usuariof->DIASPARACUMPLEANIOS > 0 && $usuariof->DIASPARACUMPLEANIOS < 6)) background-color: rgba(114,202,229,0.85) @endif
                        @if($usuariof->FECHACUMPLEANIOS != null && (\Carbon\Carbon::parse($usuariof->FECHACUMPLEANIOS)->format('Y-m-d') == (\Carbon\Carbon::parse(\Carbon\Carbon::now())->format('Y-m-d')))) background-color: rgba(199,246,163,0.8) @endif @endif">
                        <td align='center' style="vertical-align: middle;">{{$usuariof->CIUDADFRANQUICIA}}</td>
                        <td align='center'> <img src="{{asset($usuariof->FOTO)}}" style="width:50px;height:50px;" class="img-thumbnail" > </td>
                        <td align='center' style="vertical-align: middle;">{{$usuariof->NOMBRE}}</td>
                        <td align='center' style="vertical-align: middle;">{{$usuariof->CORREO}}</td>
                        @if($usuariof->ROL != "COBRANZA")
                            <td align='center' style="vertical-align: middle;">{{$usuariof->ROL}}</td>
                        @else
                            <td align='center' style="vertical-align: middle;">{{$usuariof->ROL . ($usuariof->SUPERVISORCOBRANZA == 0 ? "" : " (SUPERVISOR)")}} / {{$usuariof->ZONA}}</td>
                        @endif
                        @if($usuariof->renovacion != null)
                            <td align='center' style="vertical-align: middle;">{{$usuariof->renovacion}}</td>
                        @else
                            <td align='center' style="vertical-align: middle;">SIN RENOVAR</td>
                        @endif
                        <td align='center' style="vertical-align: middle;">{{$usuariof->NOCONTROL}}</td>
                        <td align='center' style="vertical-align: middle;">{{$usuariof->FECHACREACION}}</td>
                        <td align='center' style="vertical-align: middle;">@if($usuariof->FECHACUMPLEANIOS != null) {{$usuariof->FECHACUMPLEANIOS}} @else SIN CAPTURAR @endif</td>
                        <td align='center' style="vertical-align: middle;">{{$usuariof->ULTIMACONEXION}}</td>
                        <td align='center' style="vertical-align: middle;">
                        @if($usuariof->ACTANACIMIENTO != null)
                            <i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i>
                        @else
                            <i class='fas fa-times' style="color:#ffaca6;font-size:25px;"></i>
                        @endif
                        </td>
                        <td align='center' style="vertical-align: middle;">
                            @if($usuariof->INE != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px;"></i>
                            @endif
                        </td>
                        <td align='center' style="vertical-align: middle;">
                            @if($usuariof->CURP != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px;"></i>
                            @endif
                        </td>
                        <td align='center' style="vertical-align: middle;">
                            @if($usuariof->SEGURO != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px;"></i>
                            @endif
                        </td>
                        <td align='center' style="vertical-align: middle;">
                            @if($usuariof->CV != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px;"></i>
                            @endif
                        </td>
                        <td align='center' style="vertical-align: middle;">
                            @if($usuariof->TARJETA != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px;"></i>
                            @endif
                        </td>
                        <td align='center' style="vertical-align: middle;">
                            @if($usuariof->OTRATARJETA != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px;"></i>
                            @endif
                        </td>
                        <td align='center' style="vertical-align: middle;">
                            @if($usuariof->CONTACTO != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px;"></i>
                            @endif
                        </td>
                        <td align='center' style="vertical-align: middle;">
                            @if($usuariof->CONTRATO != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px;"></i>
                            @endif
                        </td>
                        <td align='center' style="vertical-align: middle;">
                            @if($usuariof->PAGARE != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i>
                            @else
                                @if($usuariof->ROL != "COBRANZA")
                                    <labe>N/A</labe>
                                @else
                                    <i class='fas fa-times' style="color:#ffaca6;font-size:25px;"></i>
                                @endif
                            @endif
                        </td>
                        <td align='center'>
                            <a class="btn btn-outline-success btnEliminarUsario btn-sm" href="#" data-toggle="modal" data-target="#confirmacion"
                               data_parametros_modal="{{$id. "," . $usuariof->ID_FRANQUICIA . "," . $usuariof->ID}}">
                                <i  class="fas fa-user-times"></i></a>
                        </td>
                        <td align='center'><a href="{{route('editarUsuarioFranquicia',[$usuariof->ID_FRANQUICIA,$usuariof->ID])}}">
                                <button type="button" class="btn btn-outline-success btn-sm"><i  class="fas fa-user-edit"></i></button></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!--Modal para dar de baja usuario de la sucursal-->
        <div class="modal fade" id="confirmacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <form action="{{route('eliminarUsuarioFranquicia')}}" enctype="multipart/form-data"
                  method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            Solicitud de confirmacion
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    ¿Estas seguro que quieres dar de baja al usuario de la sucursal?
                                    <hr>
                                </div>
                                <input type="hidden" name="idFranquicia" />
                                <input type="hidden" name="idFranquiciaUsuario" />
                                <input type="hidden" name="idUsuario" />
                            </div>
                            <br>
                            <div class="row" style="padding-left: 20px;">
                                <div class="col-12" style="color: #dc3545">
                                    <b>Al forzar la baja, debes tener en cuenta las siguientes consideraciones:
                                        <br>PARA EL CAMBIO DE SUCURSAL
                                        <br>ASISTENTES/OPTOMETRISTAS: Pueden perder contratos si no te aseguras de que sincronicen y cierren sesión.
                                        <br>COBRANZA: Pueden perder abonos si no te aseguras de que sincronicen y cierren sesión.
                                        <br>
                                        <br>ESTA OPCIÓN ES PREFERIBLE SOLO EN CASO DE EMERGENCIA, POR EJEMPLO:
                                        <br>Si el usuario ya no trabajará definitivamente en la empresa.
                                        <br>Si el cobrador está involucrado en actividades sospechosas, es preferible desactivar el dispositivo móvil y eliminarlo posteriormente después de que haya sincronizado los abonos.
                                    </b>
                                </div>
                                <div class="col-12">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="cbForzarbaja" id="cbForzarbaja"
                                               value="1">
                                        <label class="custom-control-label" for="cbForzarbaja">Forzar baja</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-success" data-dismiss="modal">Cancelar</button>
                            <button class="btn btn-outline-danger btn-ok" name="btnSubmit" type="submit">Eliminar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
