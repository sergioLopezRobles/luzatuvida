@extends('layouts.app')
@section('titulo','Vehiculos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>Editar vehículo</h2>
        <form id="frmCrearVehiculo" action="{{route('actualizarvehiculo',[$idFranquicia,$vehiculo[0]->indice])}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="col" style="padding-top: 20px;">
                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <label>Identificador de vehículo</label>
                            <input type="text" name="identificador" id="identificador" class="form-control"
                                   @if($vehiculo[0]->identificador != null) value="{{$vehiculo[0]->identificador}}" @else value="SIN REGISTRO" @endif readonly>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Numero de serie</label>
                            <input type="text" name="numSerie" id="numSerie" class="form-control {!! $errors->first('numSerie','is-invalid')!!}"
                                   placeholder="EJ012345678901234 (16 digitos)" value="{{$vehiculo[0]->numserie}}" @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) readonly @endif>
                            {!! $errors->first('numSerie','<div class="invalid-feedback">El campo número de serie es obligatorio y debe tener un mínimo de 16 dígitos.</div>')!!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <label>Tipo vehículo</label>
                            <!--Si es cobranza o chofer se mostrara tipo de vehiculo en un campo de texto normal-->
                            @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17)
                                @if(sizeof($tipoVehiculos) > 0)
                                    @foreach($tipoVehiculos as $tipoVehiculo)
                                        @if($tipoVehiculo->id == $vehiculo[0]->id_tipovehiculo)
                                            <input type="text" name="idTipoVehiculo" id="idTipoVehiculo" class="form-control"
                                                   value="{{$tipoVehiculo->tipo}}" readonly>
                                        @endif
                                    @endforeach
                                @else
                                    <input type="text" name="idTipoVehiculo" id="idTipoVehiculo" class="form-control"
                                           value="SIN REGISTRO" readonly>
                                @endif
                            @else
                                <!--Si es rol administracion, principal o director se mostrara tipo de vehiculo en select-->
                                <select name="idTipoVehiculo" id="idTipoVehiculo" class=" form-control {!! $errors->first('idTipoVehiculo','is-invalid')!!}"
                                        value="{{old('idTipoVehiculo')}}" required>
                                    <option value="">Seleccionar</option>
                                    @if(sizeof($tipoVehiculos) > 0)
                                        @foreach($tipoVehiculos as $tipoVehiculo)
                                            <option value="{{$tipoVehiculo->id}}" @if($tipoVehiculo->id == $vehiculo[0]->id_tipovehiculo) selected @endif>{{$tipoVehiculo->tipo}}</option>
                                        @endforeach
                                    @else
                                        <option value="">Sin registros</option>
                                    @endif
                                </select>
                                {!! $errors->first('idTipoVehiculo','<div class="invalid-feedback">Selecciona un tipo de vehiculo.</div>')!!}
                                <div class="invalid-feedback" id="errorRol"></div>
                            @endif
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Marca</label>
                            <input type="text" name="marcaVehiculo" id="marcaVehiculo" class="form-control {!! $errors->first('marcaVehiculo','is-invalid')!!}"
                                   placeholder="Honda | Yamaha | Italika | Suzuki" value="{{$vehiculo[0]->marca}}" @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) readonly @endif>
                            {!! $errors->first('marcaVehiculo','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Numero de cilindros</label>
                            <input type="number" name="numCilindros" id="numCilindros" class="form-control {!! $errors->first('numCilindros','is-invalid')!!}"
                                   placeholder="1" min="0" value="{{$vehiculo[0]->cilindros}}" @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) readonly @endif>
                            {!! $errors->first('numCilindros','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Linea</label>
                            <input type="text" name="lineaVehiculo" id="lineaVehiculo" class="form-control {!! $errors->first('lineaVehiculo','is-invalid')!!}"
                                   placeholder="DIO" value="{{$vehiculo[0]->linea}}" @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) readonly @endif>
                            {!! $errors->first('lineaVehiculo','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Modelo</label>
                            <input type="text" name="modeloVehiculo" id="modeloVehiculo" class="form-control {!! $errors->first('modeloVehiculo','is-invalid')!!}"
                                   placeholder="<?php echo date('Y'); ?>" value="{{$vehiculo[0]->modelo}}" @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) readonly @endif>
                            {!! $errors->first('modeloVehiculo','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <label>Clase</label>
                            <input type="text" name="claseVehiculo" id="claseVehiculo" class="form-control {!! $errors->first('claseVehiculo','is-invalid')!!}"
                                   placeholder="Turismo | Trabajo | Crucero" value="{{$vehiculo[0]->clase}}" @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) readonly @endif>
                            {!! $errors->first('claseVehiculo','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Estado vehículo</label>
                            <input type="text" name="tipoVehiculo" id="tipoVehiculo" class="form-control {!! $errors->first('tipoVehiculo','is-invalid')!!}"
                                   placeholder="Moto nueva" value="{{$vehiculo[0]->tipo}}" @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) readonly @endif>
                            {!! $errors->first('tipoVehiculo','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Capacidad</label>
                            <input type="text" name="capacidad" id="capacidad" class="form-control {!! $errors->first('capacidad','is-invalid')!!}"
                                   placeholder="110CC/125CC" value="{{$vehiculo[0]->capacidad}}" @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) readonly @endif>
                            {!! $errors->first('capacidad','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Numero de motor</label>
                            <input type="text" name="numMotor" id="numMotor" class="form-control {!! $errors->first('numMotor','is-invalid')!!}"
                                   placeholder="EJ1234567890 (11-17 Digitos)" value="{{$vehiculo[0]->nummotor}}" @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) readonly @endif>
                            {!! $errors->first('numMotor','<div class="invalid-feedback">El campo número de motor es obligatorio y debe tener entre 11 y 17 dígitos.</div>')!!}
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Placas</label>
                            <input type="text" name="placas" id="placas" class="form-control {!! $errors->first('placas','is-invalid')!!}"
                                   placeholder="000-00-00" value="{{$vehiculo[0]->placas}}" @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) readonly @endif>
                            {!! $errors->first('placas','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Numero de póliza</label>
                            <input type="text" name="numeroPoliza" id="numeroPoliza" class="form-control {!! $errors->first('numeroPoliza','is-invalid')!!}"
                                   placeholder="NUMERO POLIZA" value="{{$vehiculo[0]->numeropoliza}}" @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) readonly @endif>
                            {!! $errors->first('numeroPoliza','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Vigencia póliza</label>
                            <input type="date" name="vigenciaPoliza" id="vigenciaPoliza"
                                   class="form-control {!! $errors->first('vigenciaPoliza','is-invalid')!!}"
                                   value="{{$vehiculo[0]->vigenciapoliza}}" @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) readonly @endif>
                            @if($errors->has('vigenciaPoliza'))
                                <div class="invalid-feedback">{{$errors->first('vigenciaPoliza')}}</div>
                            @endif
                        </div>
                    </div>
                </div>
                @if(Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)
                    <div class="row" style="justify-content: end;">
                        <div class="col-3"> <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">ACTUALIZAR</button> </div>
                    </div>
                @endif
                <hr style="background-color: #0AA09E; height: 1px;">
            </div>
        </form>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="servicios-tab" data-toggle="tab" href="#servicios" role="tab" aria-controls="servicios"
                   aria-selected="true">Servicios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="supervision-tab" data-toggle="tab" href="#supervision" role="tab" aria-controls="supervision"
                   aria-selected="false">Supervisión</a>
            </li>
        </ul>
        <div class="tab-content">
            <!--Seccion de Servicios-->
            <div class="tab-pane active" id="servicios" role="tabpanel" aria-labelledby="servicios-tab">
                <div class="row" style="justify-content: end; padding-bottom: 30px; margin-top: 10px;">
                    <div class="col-3"> <button class="btn btn-outline-success btn-block" data-toggle="modal" data-target="#modalnuevoservicio" id="btnNuevoServicio">Nuevo servicio</button> </div>
                </div>
                <table id="tblServicios" class="table-bordered table-striped table-general table-sm">
                    <thead>
                    <tr>
                        <th  style =" text-align:center;" scope="col">SERVICIO</th>
                        <th  style =" text-align:center;" scope="col">KILOMETRAJE</th>
                        <th  style =" text-align:center;" scope="col">SIGUIENTE KILOMETRAJE</th>
                        <th  style =" text-align:center;" scope="col">SERVICIO</th>
                        <th  style =" text-align:center;" scope="col">SIGUIENTE SERVICIO</th>
                        <th  style =" text-align:center;" scope="col">FACTURA</th>
                        <th  style =" text-align:center;" scope="col">DESCRIPCIÓN</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($servicios) > 0)
                        @foreach($servicios as $servicio)
                            <tr style="align-items: center">
                                <th style="font-size: 12px; text-align: center; vertical-align: middle">{{$i = $i - 1}}</th>
                                <td style="font-size: 12px; text-align: center; vertical-align: middle">{{$servicio->kilometraje}}</td>
                                <td style="font-size: 12px; text-align: center; vertical-align: middle">{{$servicio->siguientekilometraje}}</td>
                                <td style="font-size: 12px; text-align: center; vertical-align: middle">{{$servicio->ultimoservicio}}</td>
                                <td style="font-size: 12px; text-align: center; vertical-align: middle">{{$servicio->siguienteservicio}}</td>
                                <td style="text-align: center;">
                                    <div class="col">
                                        <a type="button" class="btn btn-outline-success btn-block btn-sm" data-toggle="modal" data-target="#vistaprevia"
                                           onclick="crearVistaPrevia('{{asset($servicio->factura)}}', 'Factura - {{$servicio->numserie}} - {{$servicio->ultimoservicio}}', '1')">Ver</a>
                                        <a type="button" class="btn btn-outline-success btn-block btn-sm" href="{{route('descargarfacturaservicio',[$vehiculo[0]->indice])}}" >Descargar</a>
                                    </div>
                                </td>
                                <td style="font-size: 10px; text-align: center; vertical-align: middle">{{$servicio->descripcion}}</td>
                            </tr>
                        @endforeach
                    @endif
                    @if(count($servicios) == 0)
                        <tr>
                            <td align='center' colspan="7" style="font-size: 12px;">Sin registros</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>

            <!--Seccion de evidencias del vehiculo-->
            <div class="tab-pane" id="supervision" role="tabpanel" aria-labelledby="supervision-tab">
                <div class="row">
                    <div class="col-6" style="margin-top: 10px;">
                        <form action="{{route('actualizarhorariolimitechofer',$idFranquicia)}}" enctype="multipart/form-data"
                              method="POST" onsubmit="btnSubmit.disabled = true;" id="formHorarioFoto" name="formHorarioFoto">
                            @csrf
                            <div class="row">
                                <div class="col-5">
                                    <div class="form-group">
                                        <label style="font-size: 14px; font-weight: bold;">Hora limite actualizar foto 'Kilometraje mañana':</label>
                                        <input type="time" class="form-control {!! $errors->first('horalimiteFoto1','is-invalid')!!}" id="horalimiteFoto1" name="horalimiteFoto1"
                                               @if($horarioImagenes != null) value="{{Carbon\Carbon::parse($horarioImagenes[0]->horalimitechoferfoto1)->format('H:i')}}" @else value="09:00" required @endif
                                               @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) readonly @endif>
                                        {!! $errors->first('horalimiteFoto1','<div class="invalid-feedback">Ingresa una hora limite para que chofer pueda ingresar o actualizar FOTO 1.</div>')!!}
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="form-group">
                                        <label style="font-size: 14px; font-weight: bold;">Hora limite actualizar foto 'Kilometraje tarde':</label>
                                        <input type="time" class="form-control {!! $errors->first('horalimiteFoto2','is-invalid')!!}" id="horalimiteFoto2" name="horalimiteFoto2"
                                               @if($horarioImagenes != null) value="{{Carbon\Carbon::parse($horarioImagenes[0]->horalimitechoferfoto2)->format('H:i')}}" @else value="23:59" required @endif
                                               @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) readonly @endif>
                                        {!! $errors->first('horalimiteFoto2','<div class="invalid-feedback">Ingresa una hora limite para que chofer pueda ingresar o actualizar FOTO 2.</div>')!!}
                                    </div>
                                </div>
                                @if(Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)
                                    <div class="col-2">
                                        <button type="submit" name="btnSubmit" class="btn btn-outline-success" style="margin-top: 30px;" form="formHorarioFoto">ACEPTAR</button>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="col-3"></div>
                    <div class="col-3">
                        <div class="row" style="justify-content: end; padding-bottom: 30px; margin-top: 40px;">
                            <div class="col-12"><a class="btn btn-outline-success btn-block" href="{{route('nuevasupervision',[$idFranquicia, $vehiculo[0]->indice])}}">NUEVO</a></div>
                        </div>
                    </div>
                </div>
                <table id="tblSupervision" class="table-bordered table-striped table-general table-sm">
                    <thead>
                    <tr>
                        <th  style =" text-align:center;" scope="col">#</th>
                        <th  style =" text-align:center;" scope="col">ESTATUS</th>
                        <th  style =" text-align:center;" scope="col">FECHA</th>
                        <th  style =" text-align:center;" scope="col">KILOMETRAJE MAÑANA</th>
                        <th  style =" text-align:center;" scope="col">KILOMETRAJE TARDE</th>
                        <th  style =" text-align:center;" scope="col">LADO IZQUIERDO</th>
                        <th  style =" text-align:center;" scope="col">LADO DERECHO</th>
                        <th  style =" text-align:center;" scope="col">FRENTE</th>
                        <th  style =" text-align:center;" scope="col">ATRAS</th>
                        <th  style =" text-align:center;" scope="col">EXTRA 1</th>
                        <th  style =" text-align:center;" scope="col">EXTRA 2</th>
                        <th  style =" text-align:center;" scope="col">EXTRA 3</th>
                        <th  style =" text-align:center;" scope="col">EXTRA 4</th>
                        <th  style =" text-align:center;" scope="col">EXTRA 5</th>
                        <th  style =" text-align:center;" scope="col">EXTRA 6</th>
                        <th  style =" text-align:center;" scope="col">ACTUALIZAR</th>
                        @if(Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)
                            <th  style =" text-align:center;" scope="col">APROBAR</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($supervisionVehicular) > 0)
                        @foreach($supervisionVehicular as $supervision)
                            <tr style="align-items: center">
                                <td style="font-size: 12px; text-align: center; vertical-align: middle">{{$indiceSupervision = $indiceSupervision - 1}}</td>
                                @if($supervision->estado  == 1)
                                    <td align='center'><i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i></td>
                                @else
                                    <td align='center'><i class='fas fa-times' style="color:#ffaca6;font-size:25px;"></i></td>
                                @endif
                                <td style="font-size: 12px; text-align: center; vertical-align: middle">{{$supervision->created_at}}</td>
                                @if(isset($supervision->kilometraje1) && !empty($supervision->kilometraje1))
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">
                                        <img src="{{asset($supervision->kilometraje1)}}" style="width:50px;height:50px; cursor:pointer;" class="img-thumbnail"
                                             data-toggle="modal" data-target="#vistaprevia" onclick="crearVistaPrevia('{{asset($supervision->kilometraje1)}}', 'Kilometraje mañana', '0')">
                                    </td>
                                @else
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">S/C</td>
                                @endif
                                @if(isset($supervision->kilometraje2) && !empty($supervision->kilometraje2))
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">
                                        <img src="{{asset($supervision->kilometraje2)}}" style="width:50px;height:50px; cursor:pointer;" class="img-thumbnail" data-toggle="modal"
                                             data-target="#vistaprevia" onclick="crearVistaPrevia('{{asset($supervision->kilometraje2)}}', 'Kilometraje tarde', '0')">
                                    </td>
                                @else
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">S/C</td>
                                @endif
                                @if(isset($supervision->ladoizquierdo) && !empty($supervision->ladoizquierdo))
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">
                                        <img src="{{asset($supervision->ladoizquierdo)}}" style="width:50px;height:50px; cursor:pointer;" class="img-thumbnail" data-toggle="modal"
                                             data-target="#vistaprevia" onclick="crearVistaPrevia('{{asset($supervision->ladoizquierdo)}}', 'Lado izquierdo', '0')">
                                    </td>
                                @else
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">S/C</td>
                                @endif
                                @if(isset($supervision->ladoderecho) && !empty($supervision->ladoderecho))
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">
                                        <img src="{{asset($supervision->ladoderecho)}}" style="width:50px;height:50px; cursor:pointer;" class="img-thumbnail" data-toggle="modal"
                                             data-target="#vistaprevia" onclick="crearVistaPrevia('{{asset($supervision->ladoderecho)}}', 'Lado derecho', '0')">
                                    </td>
                                @else
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">S/C</td>
                                @endif
                                @if(isset($supervision->frente) && !empty($supervision->frente))
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">
                                        <img src="{{asset($supervision->frente)}}" style="width:50px;height:50px; cursor:pointer;" class="img-thumbnail" data-toggle="modal"
                                             data-target="#vistaprevia" onclick="crearVistaPrevia('{{asset($supervision->frente)}}', 'Foto frente', '0')">
                                    </td>
                                @else
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">S/C</td>
                                @endif
                                @if(isset($supervision->atras) && !empty($supervision->atras))
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">
                                        <img src="{{asset($supervision->atras)}}" style="width:50px;height:50px; cursor:pointer;" class="img-thumbnail" data-toggle="modal"
                                             data-target="#vistaprevia" onclick="crearVistaPrevia('{{asset($supervision->atras)}}', 'Foto atras', '0')">
                                    </td>
                                @else
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">S/C</td>
                                @endif
                                @if(isset($supervision->extra1) && !empty($supervision->extra1))
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">
                                        <img src="{{asset($supervision->extra1)}}" style="width:50px;height:50px; cursor:pointer" class="img-thumbnail"  data-toggle="modal"
                                             data-target="#vistaprevia" onclick="crearVistaPrevia('{{asset($supervision->extra1)}}', 'Foto extra1', '0')">
                                    </td>
                                @else
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">S/C</td>
                                @endif
                                @if(isset($supervision->extra2) && !empty($supervision->extra2))
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">
                                        <img src="{{asset($supervision->extra2)}}" style="width:50px;height:50px; cursor:pointer;" class="img-thumbnail" data-toggle="modal"
                                             data-target="#vistaprevia" onclick="crearVistaPrevia('{{asset($supervision->extra2)}}', 'Foto extra2', '0')">
                                    </td>
                                @else
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">S/C</td>
                                @endif
                                @if(isset($supervision->extra3) && !empty($supervision->extra3))
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">
                                        <img src="{{asset($supervision->extra3)}}" style="width:50px;height:50px; cursor:pointer;" class="img-thumbnail" data-toggle="modal"
                                             data-target="#vistaprevia" onclick="crearVistaPrevia('{{asset($supervision->extra3)}}', 'Foto extra3', '0')">
                                    </td>
                                @else
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">S/C</td>
                                @endif
                                @if(isset($supervision->extra4) && !empty($supervision->extra4))
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">
                                        <img src="{{asset($supervision->extra4)}}" style="width:50px;height:50px; cursor: pointer;" class="img-thumbnail" data-toggle="modal"
                                             data-target="#vistaprevia" onclick="crearVistaPrevia('{{asset($supervision->extra4)}}', 'Foto extra4', '0')">
                                    </td>
                                @else
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">S/C</td>
                                @endif
                                @if(isset($supervision->extra5) && !empty($supervision->extra5))
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">
                                        <img src="{{asset($supervision->extra5)}}" style="width:50px;height:50px; cursor: pointer;" class="img-thumbnail" data-toggle="modal"
                                             data-target="#vistaprevia" onclick="crearVistaPrevia('{{asset($supervision->extra5)}}', 'Foto extra5', '0')">
                                    </td>
                                @else
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">S/C</td>
                                @endif
                                @if(isset($supervision->extra6) && !empty($supervision->extra6))
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">
                                        <img src="{{asset($supervision->extra6)}}" style="width:50px;height:50px; cursor: pointer;" class="img-thumbnail" data-toggle="modal"
                                             data-target="#vistaprevia" onclick="crearVistaPrevia('{{asset($supervision->extra6)}}', 'Foto extra6', '0')">
                                    </td>
                                @else
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">S/C</td>
                                @endif
                                <td style="font-size: 12px; text-align: center; vertical-align: middle">
                                    <!--Tienes 24 horas para actualizar las imagenes-->
                                    @if((Auth::user()->rol_id == 7) || (((Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) && $supervision->estado != 1) ||
                                        ((Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) && date("Y-m-d H:i:s", strtotime($supervision->created_at ." +24 hours")) > Carbon\Carbon::now() && $supervision->estado != 1)))
                                        <div style="padding-bottom: 10px;">
                                            <a class="btn btn-outline-info btn-sm" href="{{route('actualizarsupervisionvehiculo',[$idFranquicia, $vehiculo[0]->indice, $supervision->indice])}}">ACTUALIZAR</a>
                                        </div>
                                    @endif
                                </td>
                                @if(Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)
                                    <td style="font-size: 12px; text-align: center; vertical-align: middle">
                                        @if($supervision->estado != 1)
                                            <div style="padding-bottom: 10px;">
                                                <a class="btn btn-outline-success btn-sm" href="{{route('autorizarsupervisionvehiculo',[$idFranquicia, $supervision->indice])}}">AUTORIZAR</a>
                                            </div>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                    @if(count($supervisionVehicular) == 0)
                        <tr>
                            <td align='center' @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17)colspan="16" @else colspan="17" @endif style="font-size: 12px;">Sin registros</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                @if(Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8)
                    <hr>
                    <h2>Historial movimientos</h2>
                    <table id="tablaHistorialM" class="table-striped table-general table-sm" style="margin-top: 15px;">
                        <thead>
                        <tr>
                            <th style="text-align:center;" scope="col">Usuario</th>
                            <th style="text-align:center; white-space: normal;" scope="col">Cambios</th>
                            <th style="text-align:center;" scope="col">Fecha</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(sizeof($historialMovimientos)>0)
                            @foreach($historialMovimientos as $movimiento)
                                <tr>
                                    <td align='center'>{{$movimiento->usuario}}</td>
                                    <td align='center' style="white-space: normal;">{{$movimiento->cambios}}</td>
                                    <td align='center'>{{$movimiento->created_at}}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td align='center' colspan="3">SIN REGISTROS</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <!--Ventana modal para vista previa -->
        <div class="modal fade" id="vistaprevia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div id="aparienciaModal" class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" id="encabezadoVistaPrevia"> </div>
                    <div class="modal-body">
                        <div id="vistacontenido"> </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ventana modal para nuevo servicio-->
        <div class="modal fade" id="modalnuevoservicio"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #0AA09E;">
                        <h5> Alta de nuevo servicio </h5>
                    </div>
                    <div class="modal-body">
                        <form id="frmNuevoServicio" action="{{route('registrarnuevoservicio',[$idFranquicia,$vehiculo[0]->indice])}}" enctype="multipart/form-data"
                              method="POST" onsubmit="btnSubmit.disabled = true;">
                            @csrf
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Kilometraje</label>
                                        <input type="number" name="kilometraje" id="kilometraje" class="form-control {!! $errors->first('kilometraje','is-invalid')!!}"  placeholder="100"  min="0">
                                        {!! $errors->first('kilometraje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Siguiente Kilometraje</label>
                                        <input type="number" name="sigKilometraje" id="sigKilometraje" class="form-control {!! $errors->first('sigKilometraje','is-invalid')!!}"  placeholder="100" min="0">
                                        {!! $errors->first('sigKilometraje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Ultimo Servicio</label>
                                        <input type="date" name="ultimoServicio" id="ultimoServicio"
                                               class="form-control {!! $errors->first('ultimoServicio','is-invalid')!!}" value="{{$ultimoServicio[0]->ultimoservicio}}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Siguiente Servicio</label>
                                        <input type="date" name="sigServicio" id="sigServicio"
                                               class="form-control {!! $errors->first('sigServicio','is-invalid')!!}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Factura (PDF)</label>
                                        <input type="file" name="factura" id="factura" class="form-control-file {!! $errors->first('factura','is-invalid')!!}" accept="application/pdf">
                                        {!! $errors->first('factura','<div class="invalid-feedback">La factura debera estar en formato pdf.</div>')!!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <textarea  rows="5" name="descripcion" id="descripcion" class="form-control {!! $errors->first('descripcion','is-invalid')!!}"  placeholder="Descripcion"> </textarea>
                                    {!! $errors->first('descripcion','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="col">
                            <div class="row" style="justify-content: start">
                                <label><b> Numero de serie: </b> {{$vehiculo[0]->numserie}}</label>
                            </div>
                            <div class="row" style="padding-left: 10px;">
                                <div class="col-2">
                                    <label><b> Marca: </b> {{$vehiculo[0]->marca}} </label>
                                </div>
                                <div class="col-4">
                                    <label><b> Numero de cilindros: </b> {{$vehiculo[0]->cilindros}} </label>
                                </div>
                                <div class="col-3">
                                    <label><b> Linea: </b> {{$vehiculo[0]->linea}}</label>
                                </div>
                                <div class="col-3">
                                    <label><b> Modelo: </b> {{$vehiculo[0]->modelo}}</label>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 10px; padding-top: 20px;">
                                <div style="padding-left: 15px;">
                                    <label><b> Clase: </b> {{$vehiculo[0]->clase}} </label>
                                    <label style="padding-left: 30px;"><b> Tipo: </b> {{$vehiculo[0]->tipo}} </label>
                                    <label style="padding-left: 30px;"><b> Capacidad: </b> {{$vehiculo[0]->capacidad}} </label>
                                    <label style="padding-left: 30px;"><b> Numero de motor: </b> {{$vehiculo[0]->nummotor}}</label>
                                </div>
                            </div>
                            <div class="row" style="justify-content: end; padding-top: 30px;">
                                <button class="btn btn-primary" type="button"
                                        data-dismiss="modal" style="margin-right: 20px;"> Cancelar </button>
                                <button class="btn btn-success" name="btnSubmit" type="submit"
                                        form="frmNuevoServicio"> Aceptar </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
