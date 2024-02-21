@extends('layouts.app')
@section('titulo','Editar usuario'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <div class="row">
        <h2 style="text-align: left; color: #0AA09E" name="titulo" id="titulo">Informacion de {{$usuario[0]->name}} </h2>
        <label class="mt-2 ml-3"> @if($usuario[0]->ultimaconexion != null) (Ultima conexión: {{$usuario[0]->ultimaconexion}}) @endif </label>
    </div>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="editar-tab" data-toggle="tab" href="#editar" role="tab" aria-controls="editar"
               aria-selected="true">Editar</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="controlentradasalida-tab" data-toggle="tab" href="#controlentradasalida" role="tab" aria-controls="controlentradasalida"
               aria-selected="false">Control Entrada - Salida</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="expedientePersonal-tab" data-toggle="tab" href="#expedientePersonal" role="tab" aria-controls="expedientePersonal"
               aria-selected="false">Expediente personal</a>
        </li>
        @if($usuario[0]->rol_id == 12 || $usuario[0]->rol_id == 13 ||  $usuario[0]->rol_id == 4 || $usuario[0]->rol_id == 16)
            <li class="nav-item">
                <a class="nav-link" id="dispositivos-tab" data-toggle="tab" href="#dispositivos" role="tab"
                   aria-controls="dispositivos" aria-selected="false">Dispositivos</a>
            </li>
        @endif
        <!--Seccion de vehiculos rol de cobranza o chofer-->
        @if($usuario[0]->rol_id == 4 || $usuario[0]->rol_id == 17)
            <li class="nav-item">
                <a class="nav-link" id="vehiculos-tab" data-toggle="tab" href="#vehiculos" role="tab"
                   aria-controls="vehiculos" aria-selected="false">Asignar vehiculo</a>

            </li>
        @endif
        <!--Seccion de permisos-->
        @if((Auth::user()->id == 1 || Auth::user()->id  == 19 || Auth::user()->id  == 376 || Auth::user()->id  == 61) && (
                $usuario[0]->rol_id == 6 || $usuario[0]->rol_id == 7 || $usuario[0]->rol_id == 8))
            <!--Usuarios con acceso: Christian Arcadia, Alan Irving, Fernando Carrillo, Sergio Lopez-->
            <!--Roles a asignar permisos: Administracion, Director, Principal-->
            <li class="nav-item">
                <a class="nav-link" id="permisos-tab" data-toggle="tab" href="#permisos" role="tab"
                   aria-controls="permisos" aria-selected="false">Permisos</a>
            </li>
        @endif
    </ul>

    <div class="tab-content" style="margin-top:30px;">
        <div class="tab-pane active" id="editar" role="tabpanel" aria-labelledby="editar-tab">

            <div class="row">
                <div class="col-8"></div>
                <div class="col-2">
                    @if(Auth::user()->id == 1 || Auth::user()->id  == 61 || Auth::user()->id  == 761)
                        @if($excepcionasistencia != null)
                            <input type="text" style="background-color:#0275d8;color:#FFFFFF;text-align:center"
                                   name="estatususuariotexto"
                                   class="form-control" readonly value="ACTIVO">
                        @else
                            <input type="text" style="background-color:#F88F32;color:#FFFFFF;text-align:center"
                                   name="estatususuariotexto" class="form-control" readonly value="INACTIVO">
                        @endif
                    @endif
                </div>
                <div class="col-2">
                    @switch($usuario[0]->estatus)
                        @case(0)
                        @case(2)
                            <input type="text" style="background-color:#F88F32;color:#FFFFFF;text-align:center"
                                   name="estatususuariotexto" class="form-control" readonly value="SUSPENDIDO">
                            @break
                        @case(1)
                            <input type="text" style="background-color:#0275d8;color:#FFFFFF;text-align:center"
                                   name="estatususuariotexto"
                                   class="form-control" readonly value="ACTIVO">
                            @break
                    @endswitch
                </div>
            </div>
            <div class="row">
                <div class="col-8"></div>
                <div class="col-2">
                    @if(Auth::user()->id == 1 || Auth::user()->id  == 61 || Auth::user()->id  == 761)
                        <form action="{{route('actualizarexcepcionasistenciausuario',[$idFranquicia,$idusuario])}}"
                              method="POST">
                            @csrf
                            <div class="custom-control custom-switch" style="text-align: right">
                                <input type="checkbox" class="custom-control-input" name="estatusexcepcionusuario" id="estatusexcepcionusuario"
                                       @if($excepcionasistencia != null) checked @endif
                                       onclick="eventactualizarexcepcionasistenciausuario(event)" @if($usuarioSinFranquicia) disabled @endif>
                                <label class="custom-control-label" for="estatusexcepcionusuario"></label>
                            </div>
                        </form>
                    @endif
                </div>
                <div class="col-2">
                    <form action="{{route('actualizarestatususuario',[$idFranquicia,$idusuario])}}"
                          method="POST">
                        @csrf
                        <div class="custom-control custom-switch" style="text-align: right">
                            <input type="checkbox" class="custom-control-input" name="estatususuario" id="estatususuario"
                                   @if($usuario[0]->estatus == 1) checked @endif
                                   onclick="eventactualizarestatususuario(event)" @if($usuarioSinFranquicia) disabled @endif>
                            <label class="custom-control-label" for="estatususuario"></label>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-8"></div>
                <div class="col-2" style="text-align: right">
                    @if(Auth::user()->id == 1 || Auth::user()->id  == 61 || Auth::user()->id  == 761)
                        Excepción asistencia
                    @endif
                </div>
                <div class="col-2" style="text-align: right">
                    Estado usuario
                </div>
            </div>
            <div class="row">
                <div class="col-8"></div>
                <div class="col-2"></div>
                <div class="col-2" style="text-align: right">
                    @switch($usuario[0]->estatus)
                        @case(0)
                            <label style="color: #ea9999;">Suspendido por faltas.</label>
                            @break
                        @case(2)
                            <label style="color: #ea9999;">Suspendido por administración.</label>
                            @break
                    @endswitch
                </div>
            </div>
            <br>

            @if((Auth::user()->id == 61 || Auth::user()->id == 1 || Auth::user()->id == 761) && $usuario[0]->rol_id == 4 && $usuarioSinFranquicia == false)
                <form  action="{{route('actualizarusuariozonafranquicia',[$id,$idusuario])}}" class="was-validated" enctype="multipart/form-data" method="GET">
                    @csrf
                    <div class="row">
                        <div class="col-10"></div>
                        <div class="col-2">
                            <button class="btn btn-outline-success btn-block" type="submit">Actualizar</button>
                        </div>
                    </div>
                </form>
            @endif

            <form  action="{{route('actualizarUsuarioFranquicia',[$id,$idusuario])}}" class="was-validated" enctype="multipart/form-data" method="POST"
                   id="formEditarUsuario">
                @csrf
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control"  placeholder="Nombre"  value="{{$usuario[0]->name}}" required @if($usuarioSinFranquicia == true) readonly @endif>
                            <div class="invalid-feedback">Nombre no valido.</div>
                            <div class="valid-feedback" id="errorNombre">Correcto.</div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Numero de control</label>
                            <input type="text" class="form-control" readonly  value="{{$usuario[0]->codigoasistencia}}">
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Correo</label>
                            <input type="email" name="correo" id="correo" class="form-control"  placeholder="Correo"  value="{{$usuario[0]->email}}" required @if($usuarioSinFranquicia == true) readonly @endif>
                            <div class="invalid-feedback" id="errorCorreo">El correo no es valido.</div>
                            <div class="valid-feedback">Correcto.</div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Contraseña</label>
                            <input type="password" name="contrasena" class="form-control"  placeholder="Contraseña" id="contrasena" @if($usuarioSinFranquicia == true) readonly @endif>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Confirmar contraseña</label>
                            <input type="password" name="ccontrasena" class="form-control" id="contrasena2" placeholder="Confirmar contraseña" @if($usuarioSinFranquicia == true) readonly @endif>
                        </div>
                    </div>
                    <div class="col-2">
                        <a class="btn btn-outline-success btn-block" onclick="generarPassword()" @if($usuarioSinFranquicia == true) style="visibility: hidden" @endif>Generar contraseña</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6"></div>
                    <div class="col-2">
                        <div class="custom-control custom-switch" @if($usuarioSinFranquicia == true) style="visibility: hidden" @endif>
                            <input type="checkbox" class="custom-control-input" onchange="mostrarPassword()" id="cbMostrarContrasena">
                            <label class="custom-control-label" for="cbMostrarContrasena">Mostrar contraseña</label>
                        </div>
                    </div>
                    <div class="col-4"></div>
                </div>
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <div>
                                <label>Seleccionar el Rol</label>
                                <select name="rol" id="rol" class="form-control form-control-sm {!! $errors->first('rol','is-invalid')!!}" required @if($usuarioSinFranquicia == true) readonly @endif>
                                    <option></option>
                                    @foreach($roles as $rol)
                                        @if($rol->id == $usuario[0]->rol_id)
                                            <option selected value="{{$rol->id}}">{{$rol->rol}}</option>
                                        @else
                                            <option value="{{$rol->id}}">{{$rol->rol}}</option>
                                        @endif

                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Selecciona un rol.</div>
                                <div class="valid-feedback">Correcto.</div>
                            </div>
                            <div>
                                @if($usuario[0]->rol_id == 4 && $usuario[0]->supervisorcobranza != 1)
                                    @if($solicitudAutorizacion != null)
                                        @switch($solicitudAutorizacion[0]->estatus)
                                            @case(0)
                                                <div class="row" style="color: #0AA09E; font-weight: bold; padding-left: 15px; margin-top: 10px; margin-bottom: 10px;">
                                                    Solicitud de cambio de cobranza a supervisor pendiente.
                                                </div>
                                                @break
                                            @case(1)
                                                @if($solicitudAutorizacion[0]->diasRestantes > 0)
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" name="cbsupervisorcobranza" id="cbsupervisorcobranza">
                                                        <label class="custom-control-label" for="cbsupervisorcobranza">Supervisor cobranza</label>
                                                    </div>
                                                    <div style="color: #0AA09E; font-weight: bold; margin-top: 10px;"> La solicitud de cambio a supervisor fue autorizada, tienes {{$solicitudAutorizacion[0]->diasRestantes}} dias restantes.</div>
                                                @else
                                                    <div class="form-group">
                                                        <a href="{{route('solicitarautorizacioncambiocobranza',[$id,$idusuario])}}" class="btn btn-outline-success btn-sm mt-2">Solicitar cambio a supervisor</a>
                                                        <div style="color: #ea9999; font-weight: bold; margin-top: 10px;">El tiempo disponible para llevar a cabo el cambio ha vencido.</div>
                                                    </div>
                                                @endif
                                                @break
                                            @case(2)
                                                <div class="form-group">
                                                    <a href="{{route('solicitarautorizacioncambiocobranza',[$id,$idusuario])}}" class="btn btn-outline-success btn-sm mt-2">Solicitar cambio a supervisor</a>
                                                    <div style="color: #ea9999; font-weight: bold; margin-top: 10px;"> Ultima solicitud de garantía rechazada.</div>
                                                </div>
                                                @break
                                        @endswitch
                                    @else
                                        <div class="form-group">
                                            <a href="{{route('solicitarautorizacioncambiocobranza',[$id,$idusuario])}}" class="btn btn-outline-success btn-sm mt-2">Solicitar cambio a supervisor</a>
                                        </div>
                                    @endif
                                @else
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="cbsupervisorcobranza" id="cbsupervisorcobranza"
                                               @if($usuario[0]->supervisorcobranza == 1) checked @endif>
                                        <label class="custom-control-label" for="cbsupervisorcobranza">Supervisor cobranza</label>
                                    </div>
                                @endif
                            </div>
                            @if($usuario[0]->rol_id == 4)
                                <div>
                                    <label style="color: #ea9999;">Para el cambio de supervisor a cobrador normal o viseversa verifica que haya sincronizado correctamente y realiza el corte.</label>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="form-group">
                            <label>Zona</label>
                            <select name="idzona" id="idzona" class="form-control  @error('idzona') is-invalid @enderror" @if($usuarioSinFranquicia == true) readonly @endif>
                                <option value=""></option>
                                @foreach($zonas as $zona)
                                    @if($zona->id == $usuario[0]->id_zona)
                                        <option selected value="{{$zona->id}}">{{$zona->zona}}</option>
                                    @else
                                        <option value="{{$zona->id}}">{{$zona->zona}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="invalid-feedback" name="erroridzona" id="erroridzona"></div>
                            <div class="valid-feedback">Correcto.</div>
                            @if($usuario[0]->rol_id == 4 && $usuario[0]->id_zona != null)
                                <div class="form-group">
                                    <a href="{{route('eliminarzonausuario',[$id,$idusuario])}}" class="btn btn-outline-danger btn-sm mt-2">Quitar zona</a>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($usuario[0]->rol_id == 12 ||$usuario[0]->rol_id  == 4 || $usuario[0]->rol_id  == 13 || $usuario[0]->rol_id  == 14)
                        <div class="col-1">
                            <div class="form-group">
                                <label>Sueldo</label>
                                <input type="number" name="sueldo" id="sueldo" class="form-control" min="0" placeholder="Sueldo"
                                       value="{{$usuario[0]->sueldo}}" required @if($usuarioSinFranquicia == true) readonly @endif>
                                <div class="invalid-feedback" id="errorSueldo"></div>
                            </div>
                        </div>
                    @endif
                    @if($usuario[0]->rol_id == 12 || $usuario[0]->rol_id == 13)
                        <div class="col-2">
                            <label for="franquiciaprincipal">Sucursal donde se reflejaran @if($usuario[0]->rol_id != 12 && $usuario[0]->rol_id != 13) abonos @else ventas @endif</label>
                            <div class="form-group">
                                <select name="franquiciaprincipal"
                                        class="form-control"
                                        id="franquiciaprincipal" @if($usuarioSinFranquicia) disabled @endif>
                                    @if(count($franquicias) > 0)
                                        @foreach($franquicias as $franquicia)
                                            <option
                                                value="{{$franquicia->id}}"
                                                {{ isset($usuario[0]->id_franquiciaprincipal) ? ($usuario[0]->id_franquiciaprincipal == $franquicia->id ? 'selected' : '' ) : '' }}>{{$franquicia->ciudad}}
                                            </option>
                                        @endforeach
                                    @else
                                        <option selected>Sin registros</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="col-2">
                        <div class="form-group">
                            <label>Tarjeta</label>
                            <input type="text" name="tarjeta" id="tarjeta" class="form-control" minlength="16" maxlength="20" placeholder="Tarjeta"
                                   value="{{$usuario[0]->tarjeta}}" required @if($usuarioSinFranquicia == true) readonly @endif>
                            <div class="invalid-feedback">Numero de tarjeta deben ser minimo 16 digitos.</div>
                            <div class="valid-feedback">Correcto.</div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Otra tarjeta</label>
                            <input type="text" name="otratarjeta" id="otratarjeta" class="form-control" minlength="16" maxlength="20" placeholder="Tarjeta"
                                   value="{{$usuario[0]->otratarjeta}}" required @if($usuarioSinFranquicia == true) readonly @endif>
                            <div class="invalid-feedback">Numero de tarjeta deben ser minimo 16 digitos.</div>
                            <div class="valid-feedback">Correcto.</div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Fecha de nacimiento </label>
                            <input type="date" name="fechanacimiento" id="fechanacimiento"
                                   class="form-control {!! $errors->first('fechanacimiento','is-invalid')!!}"
                                   value="{{$usuario[0]->fechanacimiento}}" max="<?= date('Y-m-d'); ?>" required @if($usuarioSinFranquicia) disabled @endif>
                            <div class="invalid-feedback">Fecha nacimiento obligatoria.</div>
                            <div class="valid-feedback">Correcto.</div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <div>
                                <label>Recordatorio</label>
                                <input type="date" name="fecharenovacion"
                                       class="form-control {!! $errors->first('fecharenovacion','is-invalid')!!}"
                                       placeholder="Fecha de renovación"
                                       value="@if($usuario[0]->renovacion != null){{ \Carbon\Carbon::parse($usuario[0]->renovacion)->format('Y-m-d')}}@endif"
                                       @if($usuarioSinFranquicia == true) readonly @endif>
                                @if($errors->has('fecharenovacion'))
                                    <div class="invalid-feedback">{{$errors->first('fecharenovacion')}}</div>
                                @endif
                            </div>
                            <div class="custom-control custom-switch" @if($usuarioSinFranquicia == true) style="visibility: hidden" @endif>
                                <input type="checkbox" class="custom-control-input" name="sinrenovacion" id="sinrenovacion">
                                <label class="custom-control-label" for="sinrenovacion">Sin renovacion</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div id="barcode" name="barcode"></div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Codigo de barras</label>
                            <a type="button" class="btn btn-outline-success btn-block mt-0 mb-0"
                               href="{{route('generarcodigodebarrasusuario',[$idFranquicia, $idusuario])}}" >Descargar</a>
                        </div>
                    </div>

                </div>
                <hr>
                <div class="row">
                    <div class="col-1">
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            @if(isset($usuario[0]->foto) && !empty($usuario[0]->foto) && file_exists($usuario[0]->foto))
                                <img src="{{asset($usuario[0]->foto)}}" width="210" height="280" style="border-radius: 10%">
                            @else
                                <img src="{{asset('imagenes\general\asistencia\avatarasistencia.png')}}" width="210" height="280" style="border-radius: 50%">
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-1">
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Foto del usuario ( FOTO JPG)</label>
                            @if($usuario[0]->foto != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px; margin-left: 10px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px; margin-left: 10px;"></i>
                            @endif
                            <div class="custom-file">
                                <input type="hidden" value="{{$usuario[0]->foto}}" id="usuarioFoto">
                            <input type="file" name="foto" id="foto" class="custom-file-input @if($usuario[0]->foto != '') is-valid @endif"
                                   accept="image/jpg" @if($usuarioSinFranquicia == true) disabled @endif>
                            <label class="custom-file-label" for="foto">Choose file...</label>
                            <div class="valid-feedback" id="errorFoto"></div>
                            </div>
                            <div class="row">
                                @if($usuario[0]->foto)
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block"
                                           href="{{route('descargarArchivo',[$usuario[0]->id,0])}}" >Descargar</a>
                                    </div>
                                    <div class="col-6">
                                        <a type="button"  class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#vistaprevia"
                                           onclick="crearVistaPrevia('{{asset($usuario[0]->foto)}}', 'Foto - {{$usuario[0]->name}}', '0')">Ver </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Acta de nacimiento (PDF)</label>
                            @if($usuario[0]->actanacimiento != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px; margin-left: 10px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px; margin-left: 10px;"></i>
                            @endif
                            <div class="custom-file">
                                <input type="hidden" value="{{$usuario[0]->actanacimiento}}" id="usuarioActanacimiento">
                            <input type="file" name="actanacimiento" id="actanacimiento" class="custom-file-input @if($usuario[0]->actanacimiento != '') is-valid @endif"
                                   accept="application/pdf" @if($usuarioSinFranquicia == true) disabled @endif>
                            <label class="custom-file-label" for="actanacimiento">Choose file...</label>
                            <div class="valid-feedback" id="errorActanacimiento"></div>
                            </div>
                            <div class="row">
                                @if($usuario[0]->actanacimiento != null)
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block"
                                           href="{{route('descargarArchivo',[$usuario[0]->id,1])}}" >Descargar</a>
                                    </div>
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#vistaprevia"
                                           onclick="crearVistaPreviaUsuarios('{{asset($usuario[0]->actanacimiento)}}', 'Acta de nacimiento - {{$usuario[0]->name}}', {{$usuario[0]->id}})">Ver</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Identificacion Oficial (PDF)</label>
                            @if($usuario[0]->identificacion != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px; margin-left: 10px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px; margin-left: 10px;"></i>
                            @endif
                            <div class="custom-file">
                                <input type="hidden" value="{{$usuario[0]->identificacion}}" id="usuarioIdentificacion">
                            <input type="file" name="identificacion" id="identificacion" class="custom-file-input @if($usuario[0]->identificacion != '') is-valid @endif"
                                   accept="application/pdf" @if($usuarioSinFranquicia == true) disabled @endif>
                            <label class="custom-file-label" for="identificacion">Choose file...</label>
                            <div class="valid-feedback" id="errorIdentificacion"></div>
                            </div>
                            <div class="row">
                                @if($usuario[0]->identificacion != null)
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block"
                                           href="{{route('descargarArchivo',[$usuario[0]->id,2])}}" >Descargar</a>
                                    </div>
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#vistaprevia"
                                           onclick="crearVistaPreviaUsuarios('{{asset($usuario[0]->identificacion)}}', 'Identificacion oficial - {{$usuario[0]->name}}', {{$usuario[0]->id}})">Ver</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>CURP (FOTO JPG)</label>
                            @if($usuario[0]->curp != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px; margin-left: 10px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px; margin-left: 10px;"></i>
                            @endif
                            <div class="custom-file">
                                <input type="hidden" value="{{$usuario[0]->curp}}" id="usuarioCurp">
                            <input type="file" name="curp" id="curp" class="custom-file-input @if($usuario[0]->curp != '') is-valid @endif"
                                   accept="image/jpg" @if($usuarioSinFranquicia == true) disabled @endif>
                            <label class="custom-file-label" for="curp">Choose file...</label>
                            <div class="valid-feedback" id="errorCurp"></div>
                            </div>
                            <div class="row">
                                @if($usuario[0]->curp != null)
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block"
                                           href="{{route('descargarArchivo',[$usuario[0]->id,3])}}" >Descargar</a>
                                    </div>
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#vistaprevia"
                                           onclick="crearVistaPreviaUsuarios('{{asset($usuario[0]->curp)}}', 'CURP - {{$usuario[0]->name}}', {{$usuario[0]->id}})">Ver</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Comprobante de domicilio (PDF)</label>
                            @if($usuario[0]->comprobantedomicilio != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px; margin-left: 10px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px; margin-left: 10px;"></i>
                            @endif
                            <div class="custom-file">
                                <input type="hidden" value="{{$usuario[0]->comprobantedomicilio}}" id="usuarioComprobante">
                            <input type="file" name="comprobante" id="comprobante" class="custom-file-input @if($usuario[0]->comprobantedomicilio != '') is-valid @endif"
                                   accept="application/pdf" @if($usuarioSinFranquicia == true) disabled @endif>
                            <label class="custom-file-label" for="comprobante">Choose file...</label>
                            <div class="valid-feedback" id="errorComprobante"></div>
                            </div>
                            <div class="row">
                                @if($usuario[0]->comprobantedomicilio != null)
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block"
                                           href="{{route('descargarArchivo',[$usuario[0]->id,4])}}" >Descargar</a>
                                    </div>
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#vistaprevia"
                                           onclick="crearVistaPreviaUsuarios('{{asset($usuario[0]->comprobantedomicilio)}}', 'Comprobante de domicilio - {{$usuario[0]->name}}', {{$usuario[0]->id}})">Ver</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-1"></div>
                </div>
                <div class="row">
                    <div class="col-1"></div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Seguro social (PDF)</label>
                            @if($usuario[0]->segurosocial != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px; margin-left: 10px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px; margin-left: 10px;"></i>
                            @endif
                            <div class="custom-file">
                                <input type="hidden" value="{{$usuario[0]->segurosocial}}" id="usuarioSeguroSocial">
                            <input type="file" name="seguro" id="seguro" class="custom-file-input @if($usuario[0]->segurosocial != '') is-valid @endif"
                                   accept="application/pdf" @if($usuarioSinFranquicia == true) disabled @endif>
                            <label class="custom-file-label" for="seguro">Choose file...</label>
                            <div class="valid-feedback" id="errorSeguroSocial"></div>
                            </div>
                            <div class="row">
                                @if($usuario[0]->segurosocial != null)
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block"
                                           href="{{route('descargarArchivo',[$usuario[0]->id,5])}}" >Descargar</a>
                                    </div>
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#vistaprevia"
                                           onclick="crearVistaPreviaUsuarios('{{asset($usuario[0]->segurosocial)}}', 'Seguro Social - {{$usuario[0]->name}}', {{$usuario[0]->id}})">Ver</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Solicitud / Curriculum (PDF)</label>
                            @if($usuario[0]->solicitud != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px; margin-left: 10px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px; margin-left: 10px;"></i>
                            @endif
                            <div class="custom-file">
                                <input type="hidden" value="{{$usuario[0]->solicitud}}" id="usuarioSolicitud">
                            <input type="file" name="solicitud" id="solicitud" class="custom-file-input @if($usuario[0]->solicitud != '') is-valid @endif"
                                   accept="application/pdf" @if($usuarioSinFranquicia == true) disabled @endif>
                            <label class="custom-file-label" for="solicitud">Choose file...</label>
                            <div class="valid-feedback" id="errorSolicitud"></div>
                            </div>
                            <div class="row">
                                @if($usuario[0]->solicitud != null)
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block"
                                           href="{{route('descargarArchivo',[$usuario[0]->id,6])}}" >Descargar</a>
                                    </div>
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#vistaprevia"
                                           onclick="crearVistaPreviaUsuarios('{{asset($usuario[0]->solicitud)}}', 'Solicitud / CV- {{$usuario[0]->name}}', {{$usuario[0]->id}})">Ver</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Tarjeta para pago (JPG)</label>
                            @if($usuario[0]->tarjetapago != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px; margin-left: 10px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px; margin-left: 10px;"></i>
                            @endif
                            <div class="custom-file">
                                <input type="hidden" value="{{$usuario[0]->tarjetapago}}" id="usuarioTarjetapago">
                            <input type="file" name="tarjetapago" id="tarjetapago" class="custom-file-input @if($usuario[0]->tarjetapago != '') is-valid @endif"
                                   accept="image/jpg" @if($usuarioSinFranquicia == true) disabled @endif>
                            <label class="custom-file-label" for="tarjetapago">Choose file...</label>
                            <div class="valid-feedback" id="errorTarjetapago"></div>
                            </div>
                            <div class="row">
                                @if($usuario[0]->tarjetapago != null)
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block"
                                           href="{{route('descargarArchivo',[$usuario[0]->id,7])}}" >Descargar</a>
                                    </div>
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#vistaprevia"
                                           onclick="crearVistaPreviaUsuarios('{{asset($usuario[0]->tarjetapago)}}', 'Tarjeta para pago - {{$usuario[0]->name}}', {{$usuario[0]->id}})">Ver</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Otra tarjeta (JPG)</label>
                            @if($usuario[0]->otratarjetapago != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px; margin-left: 10px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px; margin-left: 10px;"></i>
                            @endif
                            <div class="custom-file">
                                <input type="hidden" value="{{$usuario[0]->otratarjetapago}}" id="usuarioOtratarjetapago">
                            <input type="file" name="otratarjetapago" id="otratarjetapago" class="custom-file-input @if($usuario[0]->otratarjetapago != '') is-valid @endif"
                                   accept="image/jpg" @if($usuarioSinFranquicia == true) disabled @endif>
                            <label class="custom-file-label" for="otratarjetapago">Choose file...</label>
                            <div class="valid-feedback" id="errorOtratarjetapago"></div>
                            </div>
                            <div class="row">
                                @if($usuario[0]->otratarjetapago != null)
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block"
                                           href="{{route('descargarArchivo',[$usuario[0]->id,11])}}" >Descargar</a>
                                    </div>
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#vistaprevia"
                                           onclick="crearVistaPreviaUsuarios('{{asset($usuario[0]->otratarjetapago)}}', 'Otra tarjeta para pago - {{$usuario[0]->name}}', {{$usuario[0]->id}})">Ver</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Contacto de Emergencia (PDF)</label>
                            @if($usuario[0]->contactoemergencia != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px; margin-left: 10px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px; margin-left: 10px;"></i>
                            @endif
                            <div class="custom-file">
                                <input type="hidden" value="{{$usuario[0]->contactoemergencia}}" id="usuarioContactoemergencia">
                            <input type="file" name="contactoemergencia" id="contactoemergencia" class="custom-file-input @if($usuario[0]->contactoemergencia != '') is-valid @endif"
                                   accept="application/pdf" @if($usuarioSinFranquicia == true) disabled @endif>
                            <label class="custom-file-label" for="contactoemergencia">Choose file...</label>
                            <div class="valid-feedback" id="errorContactoemergencia"></div>
                            </div>
                            <div class="row">
                                @if($usuario[0]->contactoemergencia != null)
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block"
                                           href="{{route('descargarArchivo',[$usuario[0]->id,8])}}">Descargar</a>
                                    </div>
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#vistaprevia"
                                           onclick="crearVistaPreviaUsuarios('{{asset($usuario[0]->contactoemergencia)}}', 'Contacto de emergencia - {{$usuario[0]->name}}', {{$usuario[0]->id}})">Ver</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-1"></div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Contrato laboral (PDF)</label>
                            @if($usuario[0]->contratolaboral != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px; margin-left: 10px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px; margin-left: 10px;"></i>
                            @endif
                            <div class="custom-file">
                                <input type="hidden" value="{{$usuario[0]->contratolaboral}}" id="usuarioContrato">
                            <input type="file" name="contratolaboral" id="contratolaboral" class="custom-file-input @if($usuario[0]->contratolaboral != '') is-valid @endif"
                                   accept="application/pdf" @if($usuarioSinFranquicia == true) disabled @endif>
                            <label class="custom-file-label" for="contratolaboral">Choose file...</label>
                            <div class="valid-feedback" id="errorContrato"></div>
                            </div>
                            <div class="row">
                                @if($usuario[0]->contratolaboral != null)
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block"
                                           href="{{route('descargarArchivo',[$usuario[0]->id,9])}}" >Descargar</a>
                                    </div>
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#vistaprevia"
                                           onclick="crearVistaPreviaUsuarios('{{asset($usuario[0]->contratolaboral)}}', 'Contrato laboral - {{$usuario[0]->name}}', {{$usuario[0]->id}})">Ver</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Pagare (PDF)</label>
                            @if($usuario[0]->pagare != null)
                                <i class='fas fa-check' style="color:#9be09c;font-size:25px; margin-left: 10px;"></i>
                            @else
                                <i class='fas fa-times' style="color:#ffaca6;font-size:25px; margin-left: 10px;"></i>
                            @endif
                            <div class="custom-file">
                                <input type="hidden" value="{{$usuario[0]->pagare}}" id="usuarioPagare">
                            <input type="file" name="pagare" id="pagare" class="custom-file-input @if($usuario[0]->pagare != '') is-valid @endif"
                                   accept="application/pdf" @if($usuarioSinFranquicia == true) disabled @endif>
                            <label class="custom-file-label" for="pagare">Choose file...</label>
                            <div class="valid-feedback" id="errorPagare"></div>
                            </div>
                            <div class="row">
                                @if($usuario[0]->pagare != null)
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block"
                                           href="{{route('descargarArchivo',[$usuario[0]->id,10])}}" >Descargar</a>
                                    </div>
                                    <div class="col-6">
                                        <a type="button" class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#vistaprevia"
                                           onclick="crearVistaPreviaUsuarios('{{asset($usuario[0]->pagare)}}', 'Pagare - {{$usuario[0]->name}}', {{$usuario[0]->id}})">Ver</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @if(Auth::user()->rol_id == 7)
                    <hr>
                    <h2 style="text-align: left; color: #0AA09E">@lang('mensajes.mensajesucursales')</h2>
                    <h5>@lang('mensajes.mensajesucursalesconfirmaciones')</h5>
                    <div class="row">
                        @php
                            $i = 0;
                        @endphp
                        @foreach($sucursales as $sucursal)
                            @php
                                $existe = false;
                            @endphp
                            @foreach($sucursalesSeleccionadas as $sucursalSeleccionada)
                                @if($sucursalSeleccionada->id_franquicia ==  $sucursal->id)
                                    @php
                                        $existe = true;
                                    @endphp
                                @endif
                            @endforeach
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input " name="{{$sucursal->id}}" id="{{$sucursal->id}}" @if($existe) checked @endif
                                    value="1" @if($usuarioSinFranquicia == true) disabled @endif>
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
                <div class="row">
                    <div class="col-4">
                        <a href="{{route('usuariosFranquicia',$id)}}" class="btn btn-outline-success btn-block">@lang('mensajes.regresar')</a>
                    </div>
                    <div class="col">
                        <button class="btn btn-outline-success btn-block"  onclick="actualizarUsarioFranquicia()" id="btnActualizarUsuarioFranquicia"
                                @if($usuarioSinFranquicia == true) disabled @endif >@lang('mensajes.mensajeactualizarusuario')</button>
                    </div>
                </div>
            </form>

        </div>

        <div class="tab-pane" id="controlentradasalida" role="tabpanel" aria-labelledby="controlentradasalida-tab">
            <form  action="{{route('actualizarControlEntradaSalidaUsuarioFranquicia',[$id,$idusuario])}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <label>Hora para que comience a contar como retardo</label>
                            <input type="time" name="horaini" class="form-control" value="{{$usuario[0]->horaini}}" @if($usuarioSinFranquicia == true) disabled @endif>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Hora para que comience a contar como falta</label>
                            <input type="time" name="horafin" class="form-control" value="{{$usuario[0]->horafin}}" @if($usuarioSinFranquicia == true) disabled @endif>
                        </div>
                    </div>
                    <div class="col-2" style="margin-top: 25px; @if($usuarioSinFranquicia == true) visibility: hidden   @endif">
                        <button class="btn btn-outline-success btn-block" type="submit">Actualizar</button>
                    </div>
                </div>
            </form>
        </div>

        <!--Ventana de expediente usuario-->
        <div class="tab-pane" id="expedientePersonal" role="tabpanel" aria-labelledby="expedientePersonal-tab">
            <form  action="{{route('agregarExpedienteUsuario',[$id,$idusuario])}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label>Descripción</label>
                            <input type="text" name="expDescripcion" id="expDescripcion" class="form-control {!! $errors->first('expDescripcion','is-invalid')!!}"
                                   placeholder="Descripción" value="{{old('expDescripcion')}}" required  @if($usuarioSinFranquicia == true) disabled @endif>
                            {!! $errors->first('expDescripcion','<div class="invalid-feedback">Campo Descripcion obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Archivo (PDF)</label>
                            <div class="custom-file">
                                <input type="file" name="documento" id="documento" class="custom-file-input {!! $errors->first('documento','is-invalid')!!}"
                                       accept="application/pdf" required @if($usuarioSinFranquicia == true) disabled @endif>
                                <label class="custom-file-label" for="documento">Choose file...</label>
                                {!! $errors->first('documento','<div class="invalid-feedback">Archivo obligatorio en formato PDF, tamaño maximo 1MB.</div>')!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-2" @if($usuarioSinFranquicia == true) style="visibility: hidden" @endif>
                        <button class="btn btn-outline-success btn-block" type="submit">Aceptar</button>
                    </div>
                </div>
            </form>

            <table id="tablaExpediente" class="table-bordered table-striped table-general">
                <thead>
                <tr>
                    <th  style =" text-align:center;" scope="col">FECHA REGISTRO</th>
                    <th  style =" text-align:center;" scope="col">NOMBRE ARCHIVO</th>
                    <th  style =" text-align:center;" scope="col">DESCRIPCION</th>
                    <th  style =" text-align:center;" scope="col">VER</th>
                    <th  style =" text-align:center;" scope="col">DESCARGAR</th>
                    <th  style =" text-align:center;" scope="col">ELIMINAR</th>
                </tr>
                </thead>
                <tbody>
                @if($documentosExpediente != null && sizeof($documentosExpediente) > 0)
                    @foreach($documentosExpediente as $documento)
                        <tr>
                            <td align='center'>{{$documento->created_at}}</td>
                            <td align='center'>{{$documento->nombre}}</td>
                            <td align='center'>{{$documento->descripcion}}</td>
                            <td align='center'>
                                <a class="btn btn-outline-success btn-sm" style="margin:0px;" data-toggle="modal" data-target="#vistaprevia"
                                   onclick="crearVistaPrevia('{{asset($documento->documento)}}', '{{$documento->descripcion}}', '1')"><i class="bi bi-eye-fill"></i></a>
                            </td>
                            <td align='center'>
                                <a class="btn btn-outline-success btn-sm" style="margin:0px;" href="{{route('descargarArchivoExpedienteUsuario',[$id,$idusuario,$documento->indice])}}">
                                    <i class="bi bi-cloud-arrow-down-fill"></i></a>
                            </td>
                            <td align='center'>
                                @if($usuarioSinFranquicia == false)
                                    <a class="btn btn-outline-danger btn-sm" href="{{route('eliminarArchivoExpedienteUsuario',[$id,$idusuario,$documento->indice])}}">
                                        <i class="bi bi-trash3-fill"></i></a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td align='center' colspan="6">SIN REGISTROS</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>

        @if($usuario[0]->rol_id == 12 || $usuario[0]->rol_id == 13 || $usuario[0]->rol_id == 4 || $usuario[0]->rol_id == 16)
            <div class="tab-pane" id="dispositivos" role="tabpanel" aria-labelledby="dispositivos-tab">
                <table id="tablaFranquicias" class="table-bordered table-striped table-general">
                    <thead>
                    <tr>
                        <th  style =" text-align:center;" scope="col">ESTATUS</th>
                        <th  style =" text-align:center;" scope="col">MODELO</th>
                        <th  style =" text-align:center;" scope="col">IDIOMA</th>
                        <th  style =" text-align:center;" scope="col">VERSIÓN ANDROID</th>
                        <th  style =" text-align:center;" scope="col">VERSIÓN GRADLE</th>
                        <th  style =" text-align:center;" scope="col">REGISTRO</th>
                        <th  style =" text-align:center;" scope="col">ACTIVAR/DESACTIVAR</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($dispositivosusuario as $dispositivo)
                        <tr>
                            @if($dispositivo->estatus  == 1)
                                <td align='center'><i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i></td>
                            @else
                                <td align='center' ><i class='fas fa-check' style="color:#ffaca6;font-size:25px;"></i></td>
                            @endif
                            <td align='center'>{{$dispositivo->modelo}}</td>
                            <td align='center'>{{$dispositivo->lenguajetelefono}}</td>
                            <td align='center'>{{$dispositivo->versionandroid}}</td>
                            <td align='center'>{{$dispositivo->versiongradle}}</td>
                            <td align='center'>{{$dispositivo->created_at}}</td>
                            @if($dispositivo->estatus  == 1 )
                                <td align='center'> <a href="{{route('actualizarUsuarioFranquiciadispositivo',[$id,$idusuario,$dispositivo->id])}}" class="btn btn-outline-danger
                                btn-sm" @if($usuarioSinFranquicia == true) style="visibility: hidden" @endif>DESACTIVAR</a></td>
                            @else
                                <td align='center'> <a href="{{route('actualizarUsuarioFranquiciadispositivo',[$id,$idusuario,$dispositivo->id])}}" class="btn btn-outline-primary
                                btn-sm" @if($usuarioSinFranquicia == true) style="visibility: hidden" @endif>ACTIVAR</a></td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!--Seccion asignacion de vehiculo rol de cobranza o chofer-->
        @if($usuario[0]->rol_id == 4 || $usuario[0]->rol_id == 17)
            <div class="tab-pane" id="vehiculos" role="tabpanel" aria-labelledby="vehiculos-tab">
                <form  action="{{route('asignarVehiculoUsuarioChofer',[$id,$idusuario])}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                    @csrf
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label>Vehiculos registrados disponibles para asignar</label>
                                <select name="vehiculoAsignado" id="vehiculoAsignado" class="form-control" @if($usuarioSinFranquicia == true) disabled @endif>
                                    <option value="" selected>Seleccionar vehículo</option>
                                    @if(sizeof($vehiculos))
                                        @foreach($vehiculos as $vehiculo)
                                            <option value="{{$vehiculo->indice}}" @if($vehiculoAsignado != null) @if($vehiculoAsignado[0]->id_vehiculo == $vehiculo->indice) selected @endif @endif>{{$vehiculo->marca}} | {{$vehiculo->modelo}} | {{$vehiculo->placas}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-2" @if($usuarioSinFranquicia == true) style="visibility: hidden" @endif>
                            <button class="btn btn-outline-success btn-block" type="submit">Asignar</button>
                        </div>
                    </div>
                </form>
                @if($vehiculoAsignado != null)
                    <form  action="{{route('quitarAsignacionVehiculoUsuarioChofer',[$id,$idusuario])}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;" id="formQuitarVehiculo" name="formQuitarVehiculo">
                        @csrf
                        <div class="row">
                            <div class="col-3">
                                    <div class="form-group">
                                        <label>Vehículo asignado a usuario</label>
                                        <input type="text" class="form-control" value="{{$vehiculoAsignado[0]->marca}} - {{$vehiculoAsignado[0]->modelo}} - {{$vehiculoAsignado[0]->placas}}" readonly>
                                    </div>
                            </div>
                            <div class="col-2" @if($usuarioSinFranquicia == true) style="visibility: hidden" @endif>
                                <button class="btn btn-outline-danger btn-block" form="formQuitarVehiculo" type="submit" style="margin-top: 30px;">Quitar asignación</button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        @endif

        <!--Ventana de permisos usuario-->
        @if((Auth::user()->id == 1 || Auth::user()->id  == 19 || Auth::user()->id  == 376 || Auth::user()->id  == 61) && (
        $usuario[0]->rol_id == 6 || $usuario[0]->rol_id == 7 || $usuario[0]->rol_id == 8))
            <div class="tab-pane" id="permisos" role="tabpanel" aria-labelledby="permisos-tab">
                <div class="row" style="margin-top: 20px;">
                    <div class="col-2"></div>
                    <div class="col-8">
                        <table id="tablaPermisos" class="table-bordered" style="text-align: center; position: relative; border-collapse: collapse; width: 100%; margin-bottom: 20px;">
                            @foreach($secciones as $seccion)
                                <tr>
                                    <th align='center' colspan="3" style="text-align:center; font-size: 14px; position: sticky;">{{strtoupper($seccion->descripcion)}}</th>
                                </tr>
                                <tbody>
                                @foreach($permisosUsuario as $permisoUsuario)
                                    @if($seccion-> descripcion == $permisoUsuario->descripcion_seccion)
                                        <tr>
                                            @if(strlen($permisoUsuario->fecha_permisoasignado) > 0)
                                                <td align='center'><i class='fas fa-check' style="color:#9be09c;font-size:20px;"></i></td>
                                            @else
                                                <td align='center'><i class='fas fa-times' style="color:#ffaca6;font-size:20px;"></i></td>
                                            @endif
                                            <td style="text-align:center; font-size: 11px;">{{$permisoUsuario->descripcion_permiso}}</td>
                                            @if(strlen($permisoUsuario->fecha_permisoasignado) > 0)
                                                <td style="text-align:center;">
                                                    <a href="{{route('asignarDenegarPermisosUsuarios',[$idusuario,$permisoUsuario->id_seccion,$permisoUsuario->tipo_permiso])}}"
                                                       class="btn btn-outline-danger btn-sm"
                                                       style="text-align:center; font-size: 11px; @if($usuarioSinFranquicia == true) visibility: hidden @endif">DESACTIVAR</a>
                                                </td>
                                            @else
                                                <td style="text-align:center;">
                                                    <a href="{{route('asignarDenegarPermisosUsuarios',[$idusuario,$permisoUsuario->id_seccion,$permisoUsuario->tipo_permiso])}}"
                                                       class="btn btn-outline-primary btn-sm"
                                                       style="text-align:center; font-size: 11px; @if($usuarioSinFranquicia == true) visibility: hidden @endif">ACTIVAR</a>
                                                </td>
                                            @endif
                                        </tr>
                                    @endif
                                @endforeach
                                @endforeach
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

    <!--Ventana modal para vista previa -->
    <div class="modal fade" id="vistaprevia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
        <div id="aparienciaModal" class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="encabezadoVistaPrevia"> </div>
                <div class="modal-body">
                    <div id="vistacontenido"> </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btnCerrarVistaPrevia">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
