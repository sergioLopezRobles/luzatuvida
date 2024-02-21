@extends('layouts.app')
@section('titulo','Actualizar contrato'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <!-- {{isset($errors)? var_dump($errors) : ''}} -->
    <div class="contenedor">
        <div class="row">
            <div class="col-3">
                <h2>@lang('mensajes.mensajeactuzalizarcontrato')</h2>
            </div>
            <div class="col-9" style="display: flex; flex-direction: row-reverse">
                @if($contratoListaNegra != null && $contratoListaNegra[0]->estado != 2)
                    <div class="col-3">
                        <input type="text" style="background-color:#050505;color:#FFFFFF;text-align:center" class="form-control" readonly value="LISTA NEGRA">
                    </div>
                @endif
                <div class="col-3">
                    <button class="btn btn-outline-success btn-block mt-0" type="button"
                            data-toggle="modal" data-target="#modalreposicioncontrato">Reposición
                    </button>
                </div>
            </div>
        </div>
        <form id="frmActualizarContrato" action="{{route('contratoeditar',[$idFranquicia,$contrato[0]->id])}}"
              enctype="multipart/form-data" method="POST">
            @csrf
            <div class="franquicia">
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <label>Contrato</label>
                            <input type="text" name="id"
                                   class="form-control"
                                   placeholder="Contrato" readonly value="{{$contrato[0]->id}}">
                        </div>
                    </div>
                    <div class="col-1">
                        <label for="">Zona</label>
                        <select class="custom-select" name="zona" id="zona" required>
                            @if(count($zonas) > 0)
                                <option selected value="">Seleccionar</option>
                                @foreach($zonas as $zona)
                                    <option
                                        value="{{$zona->id}}" {{ isset($contrato) ? ($contrato[0]->id_zona== $zona->id ? 'selected' : '' ) : '' }}>{{$zona->zona}}</option>
                                @endforeach
                            @else
                                <option selected>Sin registros</option>
                            @endif
                        </select>
                        <div class="invalid-feedback" id="errorZona"></div>
                    </div>
                    <div class="col-2">
                        {{--Input oculto para validacion con js sobre opto y asistente asignado--}}
                        <input type="hidden" value="{{$contrato[0]->estatus_estadocontrato}}" id="estadoContrato">
                        <input type="hidden" value="{{$numTotalGarantias[0]->numTotalGarantias}}" id="numGarantiasContrato">
                        <label for="">Optometrista</label>
                        @if(($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) &&
                             $numTotalGarantias[0]->numTotalGarantias == 0)
                            <select class="custom-select" name="optometrista" id="optometrista" required>
                                <option selected value="">Seleccionar</option>
                                <option selected
                                        value="{{$contrato[0]->id_optometrista}}">{{$contrato[0]->nombreopto}}</option>
                                @foreach($optometristas as $optometrista)
                                    @if($optometrista->ID == $contrato[0]->id_optometrista)
                                        @continue
                                    @else
                                        <option value="{{$optometrista->ID}}">{{$optometrista->NAME}}</option>
                                    @endif
                                @endforeach
                                <!-- <option selected>Sin registros</option> -->
                            </select>
                            <div class="invalid-feedback" id="errorOptometrista"></div>
                        @else
                            <input type="text" class="form-control" readonly value="{{$contrato[0]->nombreopto}}">
                        @endif
                    </div>
                    <div class="col-2">
                        <label for="">Asistente</label>
                        @if(($contrato[0]->estatus_estadocontrato == 0 || $contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9) &&
                             $numTotalGarantias[0]->numTotalGarantias == 0)
                            <select class="custom-select" name="asistente" id="asistente" required>
                                <option selected value="">Seleccionar</option>
                                <option selected
                                        value="{{$contrato[0]->id_usuariocreacion}}">{{$contrato[0]->nombre_usuariocreacion}}</option>
                                @foreach($asistentes as $asistente)
                                    @if($asistente->ID == $contrato[0]->id_usuariocreacion)
                                        @continue
                                    @else
                                        <option value="{{$asistente->ID}}">{{$asistente->NAME}}</option>
                                    @endif
                                @endforeach
                                <!-- <option selected>Sin registros</option> -->
                            </select>
                            <div class="invalid-feedback" id="errorAsistente"></div>
                        @else
                            <input type="text" class="form-control" readonly value="{{$contrato[0]->nombre_usuariocreacion}}">
                        @endif
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Ubicacion</label>
                            <input type="text" name="coordenadas"
                                   class="form-control {!! $errors->first('coordenadas','is-invalid')!!}"
                                   placeholder="Coordenadas"
                                   value="{{$contrato[0]->coordenadas}}">
                            {!! $errors->first('coordenadas','<div class="invalid-feedback">Campo obligatorio</div>')!!}
                        </div>
                    </div>
                    <div class="col-2">
                        <a href="{{url('https://www.google.com/maps/place?key=AIzaSyC4wzK36yxyLG6yzpqUPnV4j8Y74aKkq-M&q=' . $contrato[0]->coordenadas)}}"
                           target="_blank">
                            <button type="button" class="btn btn-outline-success">Ver ubicación</button>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <label>Nombre del paciente</label>
                            <input type="text" name="nombre" id="nombre"
                                   class="form-control"
                                   placeholder="Nombre" value="{{$contrato[0]->nombre}}" required>
                            <div class="invalid-feedback" id="errorNombre"></div>
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="form-group">
                            <label>Alias</label>
                            <input type="text" name="alias" id="alias"
                                   class="form-control"
                                   placeholder="Alias" value="{{$contrato[0]->alias}}" required>
                            <div class="invalid-feedback" id="errorAlias"></div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Telefono</label>
                            <input type="text" name="telefono" id="telefono"
                                   class="form-control"
                                   placeholder="Telefono" value="{{$contrato[0]->telefono}}" required>
                            <div class="invalid-feedback" id="errorTelefono"></div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Telefono referencia</label>
                            <input type="text" name="tr" id="tr" class="form-control"
                                   placeholder="Telefono de referencia" value="{{$contrato[0]->telefonoreferencia}}" required>
                            <div class="invalid-feedback" id="errorTelefonoReferencia"></div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Nombre referencia</label>
                            <input type="text" name="nr" id="nr" class="form-control"
                                   placeholder="Nombre de referencia" value="{{$contrato[0]->nombrereferencia}}" required>
                            <div class="invalid-feedback" id="errorNombreReferencia"></div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Correo electronico</label>
                            <input type="email" name="correo" id="correo"
                                   class="form-control"
                                   placeholder="Correo electronico" value="{{$contrato[0]->correo}}">
                            <div class="invalid-feedback" id="errorCorreo"></div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3 mb-3">
                    <div class="col-3">
                        <table id="tblCobradorAsignado" class="table-bordered table-striped table-general table-sm">
                            <thead>
                            <tr>
                                <th colspan="2">Cobrador asignado a contrato</th>
                            </tr>
                            <tr>
                                <th>Nombre</th>
                                <th>Ultima sincronizacion</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($cobradoresContrato != null)
                                @foreach($cobradoresContrato as $cobrador)
                                    <tr>
                                        <td style ="text-align:center; white-space: normal;" scope="col">{{$cobrador->name}}</td>
                                        <td style ="text-align:center; white-space: normal;" scope="col">@if($cobrador->ultimaconexion != null) {{$cobrador->ultimaconexion}} @else SIN REGISTRO @endif</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2">SIN REGISTROS</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <h4>Lugar de venta</h4>
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <label>Localidad</label>
                            <input type="text" name="localidad" id="localidad"
                                   class="form-control"
                                   placeholder="Localidad" value="{{$contrato[0]->localidad}}" required>
                            <div class="invalid-feedback" id="errorLocalidad"></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Colonia</label>
                            <input type="text" name="colonia" id="colonia"
                                   class="form-control"
                                   placeholder="Colonia" value="{{$contrato[0]->colonia}}" required>
                            <div class="invalid-feedback" id="errorColonia"></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Calle</label>
                            <input type="text" name="calle" id="calle"
                                   class="form-control" placeholder="Calle"
                                   value="{{$contrato[0]->calle}}" required>
                            <div class="invalid-feedback" id="errorCalle"></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Entre calles</label>
                            <input type="text" name="entrecalles" id="entrecalles"
                                   class="form-control"
                                   placeholder="Entre calles" value="{{$contrato[0]->entrecalles}}" required>
                            <div class="invalid-feedback" id="errorEntrecalles"></div>
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="form-group">
                            <label>Numero</label>
                            <input type="text" name="numero" id="numero"
                                   class="form-control"
                                   placeholder="Numero" value="{{$contrato[0]->numero}}" required>
                            <div class="invalid-feedback" id="errorNumero"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <label>Departamento</label>
                            <input type="text" name="departamento" id="departamento" class="form-control" placeholder="Departamento"
                                   value="{{$contrato[0]->depto}}" required>
                            <div class="invalid-feedback" id="errorDepartamento"></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Al lado de</label>
                            <input type="text" name="alladode" id="alladode"
                                   class="form-control"
                                   placeholder="Al lado de" value="{{$contrato[0]->alladode}}" required>
                            <div class="invalid-feedback" id="errorAlladode"></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Frente a</label>
                            <input type="text" name="frentea" id="frentea"
                                   class="form-control"
                                   placeholder="Frente a" value="{{$contrato[0]->frentea}}" required>
                            <div class="invalid-feedback" id="errorFrentea"></div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Tipo Casa</label>
                            <input type="text" name="casatipo" id="casatipo"
                                   class="form-control"
                                   placeholder="Tipo Casa" value="{{$contrato[0]->casatipo}}" required>
                            <div class="invalid-feedback" id="errorCasatipo"></div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Casa color</label>
                            <input type="text" name="casacolor" id="casacolor"
                                   class="form-control"
                                   placeholder="Casa color" value="{{$contrato[0]->casacolor}}" required>
                            <div class="invalid-feedback" id="errorCasacolor">Campo obligatorio</div>
                        </div>
                    </div>
                </div>
                <h4>Lugar de cobranza</h4>
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <label>Localidad</label>
                            <input type="text" name="localidadentrega" id="localidadentrega"
                                   class="form-control"
                                   placeholder="Localidad" value="{{$contrato[0]->localidadentrega}}" required>
                            <div class="invalid-feedback" id="errorLocalidadentrega"></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Colonia</label>
                            <input type="text" name="coloniaentrega" id="coloniaentrega"
                                   class="form-control"
                                   placeholder="Colonia" value="{{$contrato[0]->coloniaentrega}}" required>
                            <div class="invalid-feedback" id="errorColoniaentrega"></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Calle</label>
                            <input type="text" name="calleentrega" id="calleentrega"
                                   class="form-control" placeholder="Calle"
                                   value="{{$contrato[0]->calleentrega}}" required>
                            <div class="invalid-feedback" id="errorCalleentrega"></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Entre calles</label>
                            <input type="text" name="entrecallesentrega" id="entrecallesentrega"
                                   class="form-control"
                                   placeholder="Entre calles" value="{{$contrato[0]->entrecallesentrega}}" required>
                            <div class="invalid-feedback" id="errorEntrecallesentrega"></div>
                        </div>
                    </div>
                    <div class="col-1">
                        <div class="form-group">
                            <label>Numero</label>
                            <input type="text" name="numeroentrega" id="numeroentrega"
                                   class="form-control"
                                   placeholder="Numero" value="{{$contrato[0]->numeroentrega}}" required>
                            <div class="invalid-feedback" id="errorNumeroentrega"></div>
                        </div>
                    </div>
                </div>
                <!--Segundo renglon-->
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <label>Departamento</label>
                            <input type="text" name="departamentoentrega" id="departamentoentrega" class="form-control" placeholder="Departamento"
                                   value="{{$contrato[0]->deptoentrega}}" required>
                            <div class="invalid-feedback" id="errorDepartamentoentrega"></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Al lado de</label>
                            <input type="text" name="alladodeentrega" id="alladodeentrega"
                                   class="form-control"
                                   placeholder="Al lado de" value="{{$contrato[0]->alladodeentrega}}" required>
                            <div class="invalid-feedback" id="errorAlladodeentrega"></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Frente a</label>
                            <input type="text" name="frenteaentrega" id="frenteaentrega"
                                   class="form-control"
                                   placeholder="Frente a" value="{{$contrato[0]->frenteaentrega}}" required>
                            <div class="invalid-feedback" id="errorFrenteaentrega"></div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Tipo Casa</label>
                            <input type="text" name="casatipoentrega" id="casatipoentrega"
                                   class="form-control"
                                   placeholder="Tipo Casa" value="{{$contrato[0]->casatipoentrega}}" required>
                            <div class="invalid-feedback" id="errorCasatipoentrega"></div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Casa color</label>
                            <input type="text" name="casacolorentrega" id="casacolorentrega"
                                   class="form-control"
                                   placeholder="Casa color" value="{{$contrato[0]->casacolorentrega}}" required>
                            <div class="invalid-feedback" id="errorCasacolorentrega"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <label>Foto INE Frente:</label>
                            <div class="custom-file">
                                <input type="file" name="fotoine" id="fotoine"
                                       class="custom-file-input"
                                       accept="image/jpg">
                                <label class="custom-file-label" for="fotoine">Choose file...</label>
                                <div class="invalid-feedback" id="errorFotoIne"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Foto INE Atrás</label>
                            <div class="custom-file">
                                <input type="file" name="fotoineatras" id="fotoineatras"
                                       class="custom-file-input"
                                       accept="image/jpg">
                                <label class="custom-file-label" for="fotoineatras">Choose file...</label>
                                <div class="invalid-feedback" id="errorfotoineatras"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Pagare:</label>
                            <div class="custom-file">
                                <input type="file" name="pagare" id="pagare"
                                       class="custom-file-input"
                                       accept="image/jpg">
                                <label class="custom-file-label" for="pagare">Choose file...</label>
                                <div class="invalid-feedback" id="errorPagare"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Foto de la casa</label>
                            <div class="custom-file">
                                <input type="file" name="fotocasa" id="fotocasa"
                                       class="custom-file-input"
                                       accept="image/jpg">
                                <label class="custom-file-label" for="fotocasa">Choose file...</label>
                                <div class="invalid-feedback" id="errorFotocasa"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Comprobante de domicilio</label>
                            <div class="custom-file">
                                <input type="file" name="comprobantedomicilio" id="comprobantedomicilio"
                                       class="custom-file-input"
                                       accept="image/jpg">
                                <label class="custom-file-label" for="comprobantedomicilio">Choose file...</label>
                                <div class="valid-feedback" id="errorComprobanteDom"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Tarjeta de pension frente</label>
                            <div class="custom-file">
                                <input type="file" name="tarjeta" id="tarjeta"
                                       class="custom-file-input"
                                       accept="image/jpg">
                                <label class="custom-file-label" for="tarjeta">Choose file...</label>
                                <div class="valid-feedback" id="errorTarjeta"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <div data-toggle="modal" data-target="#imagemodal" id="img1" style="cursor: pointer">
                        <img src="{{asset($contrato[0]->fotoine)}}" style="width:250px;height:250px;" class="img-thumbnail">
                    </div>
                    <input type="text" name="observacionfotoine" id="observacionfotoine" class="form-control" placeholder="Observación"
                           value="{{$contrato[0]->observacionfotoine}}">
                </div>
                <div class="col-2">
                    <div data-toggle="modal" data-target="#imagemodal" id="img2" style="cursor: pointer">
                        <img src="{{asset($contrato[0]->fotoineatras)}}" style="width:250px;height:250px;"
                             class="img-thumbnail">
                    </div>
                    <input type="text" name="observacionfotoineatras" id="observacionfotoineatras" class="form-control" placeholder="Observación"
                           value="{{$contrato[0]->observacionfotoineatras}}">
                </div>
                <div class="col-2">
                    <div data-toggle="modal" data-target="#imagemodal" id="img3" style="cursor: pointer">
                        <img src="{{asset($contrato[0]->pagare)}}" style="width:250px;height:250px;" class="img-thumbnail">
                    </div>
                    <input type="text" name="observacionpagare" id="observacionpagare" class="form-control" placeholder="Observación"
                           value="{{$contrato[0]->observacionpagare}}">
                </div>
                <div class="col-2">
                    <div data-toggle="modal" data-target="#imagemodal" id="img4" style="cursor: pointer">
                        <img src="{{asset($contrato[0]->fotocasa)}}" style="width:250px;height:250px;"
                             class="img-thumbnail">
                    </div>
                    <input type="text" name="observacionfotocasa" id="observacionfotocasa" class="form-control" placeholder="Observación"
                           value="{{$contrato[0]->observacionfotocasa}}">
                </div>
                <div class="col-2">
                    <div data-toggle="modal" data-target="#imagemodal" id="img5" style="cursor: pointer">
                        <img src="{{asset($contrato[0]->comprobantedomicilio)}}" style="width:250px;height:250px;"
                             class="img-thumbnail">
                    </div>
                    <input type="text" name="observacioncomprobantedomicilio" id="observacioncomprobantedomicilio" class="form-control" placeholder="Observación"
                           value="{{$contrato[0]->observacioncomprobantedomicilio}}">
                </div>
                @if($contrato[0]->tarjeta != '')
                    <div class="col-2" data-toggle="modal" data-target="#imagemodal" id="img6" style="cursor: pointer">
                        <img src="{{asset($contrato[0]->tarjeta)}}" style="width:250px;height:250px;"
                             class="img-thumbnail">
                    </div>
                @endif
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="col-2">
                    <div class="form-group">
                        <label>Tarjeta de pension atras</label>
                        <div class="custom-file">
                            <input type="file" name="tarjetapensionatras" id="tarjetapensionatras"
                                   class="custom-file-input"
                                   accept="image/jpg">
                            <label class="custom-file-label" for="tarjetapensionatras">Choose file...</label>
                            <div class="invalid-feedback" id="errorTrajetapensionatras"></div>
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Otros</label>
                        <div class="custom-file">
                            <input type="file" name="fotootros" id="fotoOtros"
                                   class="custom-file-input"
                                   accept="image/jpg">
                            <label class="custom-file-label" for="fotootros">Choose file...</label>
                            <div class="valid-feedback" id="errorFotoOtros"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="padding-bottom: 10px;">
                @if($contrato[0]->tarjetapensionatras != '' || $contrato[0]->fotootros != '')
                    @if($contrato[0]->tarjetapensionatras != '' && $contrato[0]->fotootros != '')
                        <div class="col-2" data-toggle="modal" data-target="#imagemodal" id="img7"
                             style="cursor: pointer">
                            <img src="{{asset($contrato[0]->tarjetapensionatras)}}" style="width:250px;height:250px;"
                                 class="img-thumbnail">
                        </div>
                        <div class="col-2">
                            <div data-toggle="modal" data-target="#imagemodal" id="img8"
                                 style="cursor: pointer">
                                <img src="{{asset($contrato[0]->fotootros)}}" style="width:250px;height:250px;"
                                     class="img-thumbnail">
                            </div>
                            <input type="text" name="observacionfotootros" id="observacionfotootros" class="form-control" placeholder="Observación"
                                   value="{{$contrato[0]->observacionfotootros}}">
                        </div>
                    @else
                        @if($contrato[0]->tarjetapensionatras != '')
                            <div class="col-2" data-toggle="modal" data-target="#imagemodal" id="img7"
                                 style="cursor: pointer">
                                <img src="{{asset($contrato[0]->tarjetapensionatras)}}"
                                     style="width:250px;height:250px;" class="img-thumbnail">
                            </div>
                        @else
                            <div class="col-2"></div>
                            <div class="col-2">
                                <div data-toggle="modal" data-target="#imagemodal" id="img8"
                                     style="cursor: pointer">
                                    <img src="{{asset($contrato[0]->fotootros)}}" style="width:250px;height:250px;"
                                         class="img-thumbnail">
                                </div>
                                <input type="text" name="observacionfotootros" id="observacionfotootros" class="form-control" placeholder="Observación"
                                       value="{{$contrato[0]->observacionfotootros}}">
                            </div>
                        @endif
                    @endif
                @endif
            </div>
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-outline-success btn-block" onclick="actualizarContrato()" id="btnActualizarContrato">@lang('mensajes.mensajeactuzalizarcontrato')</button>
                </div>
            </div>
        </form>
        <form action="{{route('agregarnota',[$idFranquicia,$contrato[0]->id])}}"
              method="POST" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="row">
                <div class="col-10">
                    <div class="form-group">
                        <label>Nota del cobrador</label>
                        <input type="text" name="nota" maxlength="255"
                               class="form-control {!! $errors->first('nota','is-invalid')!!}"
                               placeholder="Nota del cobrador" value="{{$contrato[0]->nota}}">
                        {!! $errors->first('nota','<div class="invalid-feedback">No puede superar los 255 caracteres.</div>')!!}
                    </div>
                </div>
                <div class="col-2">
                    <button class="btn btn-outline-success btn-block" name="btnSubmit"
                            type="submit">@lang('mensajes.mensajeactualizarnota')</button>
                </div>
            </div>
        </form>
        <hr>
        <h2>Información diagnóstico</h2>
        <form id="frmDiagnostico" action="{{route('actualizardiagnosticoeditarcontrato',[$idFranquicia,$contrato[0]->id])}}" method="POST">
            @csrf
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label>Edad</label>
                        <input type="number" name="edad" id="edad" class="form-control" @if($datosDiagnosticoHistorial[0]->edad != null) value="{{$datosDiagnosticoHistorial[0]->edad}}"
                               @else value="{{old('edad')}}" @endif required>
                        <div class="invalid-feedback" id="errorEdad"></div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Diagnóstico</label>
                        <input type="text" name="diagnostico" id="diagnostico" class="form-control" @if($datosDiagnosticoHistorial[0]->diagnostico != null)
                            value="{{$datosDiagnosticoHistorial[0]->diagnostico}}" @else value="{{old('diagnostico')}}" @endif required>
                        <div class="invalid-feedback" id="errorDiagnostico"></div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Ocupación</label>
                        <input type="text" name="ocupacion" id="ocupacion" class="form-control" @if($datosDiagnosticoHistorial[0]->ocupacion != null) value="{{$datosDiagnosticoHistorial[0]->ocupacion}}"
                               @else value="{{old('ocupacion')}}" @endif required>
                        <div class="invalid-feedback" id="errorOcupacion"></div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Diabetes</label>
                        <input type="text" name="diabetes" id="diabetes" class="form-control" @if($datosDiagnosticoHistorial[0]->diabetes != null) value="{{$datosDiagnosticoHistorial[0]->diabetes}}"
                               @else value="{{old('diabetes')}}" @endif required>
                        <div class="invalid-feedback" id="errorDiabetes"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Hipertensión</label>
                        <input type="text" name="hipertension" id="hipertension" class="form-control" @if($datosDiagnosticoHistorial[0]->hipertension != null)
                            value="{{$datosDiagnosticoHistorial[0]->hipertension}}" @else value="{{old('hipertension')}}"@endif required>
                        <div class="invalid-feedback" id="errorHipertencion"></div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>¿Se encuentra embarazada?</label>
                        <input type="text" name="embarazada" id="embarazada" class="form-control" @if($datosDiagnosticoHistorial[0]->embarazada != null) value="{{$datosDiagnosticoHistorial[0]->embarazada}}"
                               @else value="{{old('embarazada')}}"@endif required>
                        <div class="invalid-feedback" id="errorEmbarazada"></div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>¿Durmió de 6 a 8 horas?</label>
                        <input type="text" name="durmioseisochohoras" id="durmioseisochohoras" class="form-control" @if($datosDiagnosticoHistorial[0]->durmioseisochohoras != null)
                            value="{{$datosDiagnosticoHistorial[0]->durmioseisochohoras}}" @else value="{{old('durmioseisochohoras')}}"@endif required>
                        <div class="invalid-feedback" id="errorHorasS"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>Principal actividad en el día</label>
                        <input type="text" name="actividaddia" id="actividaddia" class="form-control" @if($datosDiagnosticoHistorial[0]->actividaddia != null)
                            value="{{$datosDiagnosticoHistorial[0]->actividaddia}}" @else value = "{{old('actividaddia')}}" @endif required>
                        <div class="invalid-feedback" id="errorPrincipalA"></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>Principal problema que padece en sus ojos</label>
                        <input type="text" name="problemasojos" id="problemasojos" class="form-control" @if($datosDiagnosticoHistorial[0]->problemasojos != null)
                            value="{{$datosDiagnosticoHistorial[0]->problemasojos}}" @else value="{{old('problemasojos')}}" @endif required>
                        <div class="invalid-feedback" id="errorPrincipalP"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-10">
                    <h6>Molestia</h6>
                </div>
                <div class="col-2">
                    <h6>Último examen</h6>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input " name="dolor" id="dolor" @if($datosDiagnosticoHistorial[0]->dolor == 1) checked @endif>
                        <label class="custom-control-label" for="dolor">Dolor de cabeza</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input " name="ardor" id="ardor" @if($datosDiagnosticoHistorial[0]->ardor == 1) checked @endif>
                        <label class="custom-control-label" for="ardor">Ardor en los ojos</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="golpeojos" id="golpeojos" @if($datosDiagnosticoHistorial[0]->golpeojos == 1) checked @endif>
                        <label class="custom-control-label" for="golpeojos">Golpe en cabeza</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input " type="checkbox" name="otroM" id="otroM" @if($datosDiagnosticoHistorial[0]->otroM == 1) checked @endif {{old('otroM')}}>
                        <label class="custom-control-label" for="otroM">Otro</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <input type="text" name="molestiaotro" id="molestiaotroInput" class="form-control" min="0"  placeholder="Otro"
                               @if($datosDiagnosticoHistorial[0]->molestiaotro != null) value="{{$datosDiagnosticoHistorial[0]->molestiaotro}}"@endif>
                        <div class="invalid-feedback" id="errorMolestiaOtros"></div>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <input type="date" name="ultimoexamen" id="ultimoexamen" class="form-control" min="0" max="{{ now()->toDateString('Y-m-d') }}"
                               placeholder="" @if($datosDiagnosticoHistorial[0]->ultimoexamen != null) value="{{$datosDiagnosticoHistorial[0]->ultimoexamen}}" @else value="{{old('ultimoexamen')}}"@endif required>
                        <div class="invalid-feedback" id="errorUltimoExamen"></div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <button class="btn btn-outline-success btn-block" form="frmDiagnostico" id="btnActualizarDiagnosticoContrato"
                            onclick="actualizarDiagnosticoContrato()">Actualizar diagnostico</button>
                </div>
            </div>
        </form>
        <hr>
        <input type="hidden" id="jsonHistoriales" name="jsonHistoriales" value="{{json_encode($arrayHistoriales)}}">
        <h4 style="margin-top: 10px">Historiales clinicos</h4>
        <h4 style="margin-top: 10px">Total garantias: {{$numTotalGarantias[0]->numTotalGarantias}}</h4>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="historialBase-tab" data-toggle="tab" href="#historialBase" role="tab" aria-controls="historialBase"
                   aria-selected="true">Historial base</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="historialActivo-tab" data-toggle="tab" href="#historialActivo" role="tab" aria-controls="historialActivo"
                   aria-selected="true">Historial activo (Garantias Asignadas/Creadas)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="garantiasTerminadas-tab" data-toggle="tab" href="#garantiasTerminadas" role="tab" aria-controls="garantiasTerminadas"
                   aria-selected="true">Garantias Terminadas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="historialCancelados-tab" data-toggle="tab" href="#historialCancelados" role="tab" aria-controls="historialCancelados"
                   aria-selected="false">Historial garantia cancelada</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="historialCambioPaquete-tab" data-toggle="tab" href="#historialCambioPaquete" role="tab" aria-controls="historialCambioPaquete"
                   aria-selected="false">Historial Cambio de paquete</a>
            </li>
        </ul>

        <div class="tab-content">
            <!--Seccion historiales base-->
            <div class="tab-pane active" id="historialBase" role="tabpanel" aria-labelledby="historialBase-tab">
                @php($contador = 1)
                @if($historialesBase != null)
                    @foreach($historialesBase as $historial)
                        @if($historial->tipo == 0)
                            <h4 style="margin-top: 10px">Garantias: {{$historial->numGarantias}}</h4>
                        @endif
                        <div class="row">
                            <div class="col-2">
                                @switch($historial->tipo)
                                    @case(0)
                                        <h4 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}
                                            ({{$historial->id}})</h4>
                                        @break
                                    @case(1)
                                        <h4 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}
                                            ({{$historial->id}}) "Garantía de {{$historial->idhistorialpadre}}"</h4>
                                        @break
                                    @case(2)
                                        <h4 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}
                                            ({{$historial->id}}) "Cambio paquete"</h4>
                                        @break
                                @endswitch
                            </div>
                            <div class="col-2">
                                <h4 style="margin-top: 10px;">Modelo: {{$historial->armazon}}</h4>
                            </div>
                            <div class="col-4">
                                <h4 style="margin-top: 10px;">Color: {{$historial->colorarmazon}}</h4>
                            </div>
                            @if($loop->iteration == 1)
                                <div class="col-2">
                                    <h4 style="margin-top: 10px;">Paquete: {{$historial->paquete}}</h4>
                                </div>
                            @endif
                        </div>
                            @if($contrato[0]->estatus_estadocontrato <= 1 || $contrato[0]->estatus_estadocontrato == 9)
                                <form id="frmarmazon{{$historial->id}}" action="{{route('editarHistorialArmazon',[$idFranquicia,$idContrato,$historial->id])}}"
                                      enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="">Armazón</label>
                                            <select class="custom-select"
                                                    name="producto">
                                                @if(count($armazones) > 0)
                                                    <option selected value=''>Seleccionar</option>
                                                    @foreach($armazones as $armazon)
                                                        @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 0)
                                                            @if($armazon->id == $historial->id_producto)
                                                                <option selected value="{{$armazon->id}}">
                                                                    {{$armazon->nombre}} | {{$armazon->color}}
                                                                    | {{$armazon->piezas}}pza.
                                                                </option>
                                                            @else
                                                                <option
                                                                    value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                                                    {{$armazon->nombre}} | {{$armazon->color}}
                                                                    | {{$armazon->piezas}}pza.
                                                                </option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <option selected>Sin registros</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <button class="btn btn-outline-success btn-block" name="btnSubmit" form="frmarmazon{{$historial->id}}"
                                                    type="submit">Actualizar armazón
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                            @if(($contrato[0]->estatus_estadocontrato <= 1 || $contrato[0]->estatus_estadocontrato == 9) && str_contains(strtoupper($historial->armazon),'PROPIO'))
                                <form id="frmActualizarFotoArmazon{{$historial->id}}" action="{{route('actualizarfotoarmazon',[$idFranquicia,$contrato[0]->id,$historial->id])}}"
                                      enctype="multipart/form-data" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-1">
                                            <div class="form-group">
                                                <label>Foto armazon</label>
                                                <div data-toggle="modal" data-target="#imagemodal" id="fotoarmazon{{$loop->iteration}}" style="cursor: pointer">
                                                    @if(isset($historial->fotoarmazon) && !empty($historial->fotoarmazon) && file_exists($historial->fotoarmazon))
                                                        <img src="{{asset($historial->fotoarmazon)}}" style="width:120px;height:120px;" class="img-thumbnail">
                                                    @else
                                                        <img src="/imagenes/general/administracion/sinfoto.png" style="width:120px;height:120px;" class="img-thumbnail">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="custom-file">
                                                <input type="file" name="fotoArmazon{{$historial->id}}" id="fotoArmazon{{$historial->id}}"
                                                       class="custom-file-input {!! $errors->first('fotoArmazon' . $historial->id,'is-invalid')!!}" accept="image/jpg">
                                                <label class="custom-file-label" for="fotoArmazon">Choose file...</label>
                                                {!! $errors->first('fotoArmazon' . $historial->id,'<div class="invalid-feedback">Foto armazón debe ser en formato JPG y de tamaño maximo 1MB.</div>')!!}
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <button class="btn btn-outline-success btn-block" name="btnSubmit" form="frmActualizarFotoArmazon{{$historial->id}}"
                                                    type="submit" style="margin-top: 0px;">Actualizar foto armazon
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        @if($historial->tipo == 1 && $historial->estadogarantia == 2)
                            @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9)
                                <div style="color: #ea9999; font-weight: bold; margin-bottom: 10px;">
                                    Para cancelar la garantía, debes de comentarle a confirmaciones.
                                </div>
                            @endif
                            @if($contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11)
                                <div style="color: #ea9999; font-weight: bold; margin-bottom: 10px;">
                                    Para cancelar la garantía, debes de comentarle a laboratorio.
                                </div>
                            @endif
                        @endif
                        @if(($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 5 || $contrato[0]->estatus_estadocontrato == 12 || $contrato[0]->estatus_estadocontrato == 4)
                            && $historial->tipo == 0)
                            <div class="row">
                                @if($contrato[0]->cuentaregresivafechaentrega >= 0
                                        || $contrato[0]->garantiacanceladaelmismodia
                                        || !$bandera
                                        || $contrato[0]->estatus_estadocontrato == 12)
                                    <div class="col-6">
                                        <form id="frmgarantia"
                                              action="{{route('agregarGarantiaHistorial',[$idFranquicia,$contrato[0]->id,$historial->id])}}"
                                              enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                            @csrf
                                            @if($historial->optometristaasignado == null)
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label for="">Optometrista</label>
                                                        <select
                                                            class="custom-select {!! $errors->first('optometristagarantia','is-invalid')!!}"
                                                            name="optometristagarantia">
                                                            @if(count($optometristas) > 0)
                                                                <option selected value='nada'>Seleccionar</option>
                                                                @foreach($optometristas as $optometrista)
                                                                    <option
                                                                        value="{{$optometrista->ID}}">
                                                                        {{$optometrista->NAME}}
                                                                    </option>
                                                                @endforeach
                                                            @else
                                                                <option selected>Sin registros</option>
                                                            @endif
                                                        </select>
                                                        {!! $errors->first('optometristagarantia','<div class="invalid-feedback">Elegir un optometrista , campo obligatorio
                                                        </div>')!!}
                                                    </div>
                                                    <div class="col-6">
                                                        <button class="btn btn-outline-success btn-block" name="btnSubmit"
                                                                type="submit">Nueva garantia
                                                        </button>
                                                    </div>
                                                </div>
                                                @if($contrato[0]->cuentaregresivafechaentrega >= 0)
                                                    <div style="color: #ea9999; font-weight: bold;">Quedan {{$contrato[0]->cuentaregresivafechaentrega}} días para bloquear o deshabilitar garantias a este
                                                        contrato
                                                    </div>
                                                @else
                                                    @if($contrato[0]->fechaentrega != null)
                                                        <div style="color: #ea9999; font-weight: bold;">Fecha limite para garantias ya expiró</div>
                                                    @endif
                                                @endif
                                            @endif
                                        </form>
                                    </div>
                                @else
                                    @if($contrato[0]->fechaentrega != null && $bandera)
                                        <div class="col col-6">
                                            @if($solicitudAutorizacion != null)
                                                @if($solicitudAutorizacion[0]->estatus == 0)
                                                    <div style="color: #0AA09E; font-weight: bold;"> Solicitud de garantía pendiente.</div>
                                                @endif
                                                @if($solicitudAutorizacion[0]->estatus == 1)
                                                    <form id="frmgarantia"
                                                          action="{{route('agregarGarantiaHistorial',[$idFranquicia,$contrato[0]->id,$historial->id])}}"
                                                          enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <label for="">Optometrista</label>
                                                                <select
                                                                    class="custom-select {!! $errors->first('optometristagarantia','is-invalid')!!}"
                                                                    name="optometristagarantia">
                                                                    @if(count($optometristas) > 0)
                                                                        <option selected value='nada'>Seleccionar</option>
                                                                        @foreach($optometristas as $optometrista)
                                                                            <option
                                                                                value="{{$optometrista->ID}}">
                                                                                {{$optometrista->NAME}}
                                                                            </option>
                                                                        @endforeach
                                                                    @else
                                                                        <option selected>Sin registros</option>
                                                                    @endif
                                                                </select>
                                                                {!! $errors->first('optometristagarantia','<div class="invalid-feedback">Elegir un optometrista , campo obligatorio
                                                                </div>')!!}
                                                            </div>
                                                            <div class="col-6">
                                                                <button class="btn btn-outline-success btn-block" name="btnSubmit"
                                                                        type="submit">Nueva garantia
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                @endif
                                                @if($solicitudAutorizacion[0]->estatus == 2)
                                                    <div style="margin-bottom: 5px;">
                                                        <a type="button" href="" class="btn btn-outline-success"
                                                           data-toggle="modal"
                                                           data-target="#modalsolicitarautorizacion">Solicitar garantía</a>
                                                    </div>
                                                    <div style="color: #ea9999; font-weight: bold;"> Ultima solicitud de garantía rechazada.</div>
                                                @endif
                                            @else
                                                <div style="margin-bottom: 5px;">
                                                    <a type="button" href="" class="btn btn-outline-success"
                                                       data-toggle="modal"
                                                       data-target="#modalsolicitarautorizacion">Solicitar garantía</a>
                                                </div>
                                            @endif
                                            <div style="color: #ea9999; font-weight: bold;">
                                                Fecha limite para garantias ya expiró
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-6"></div>
                                    @endif
                                @endif

                                @if((Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) && $contrato[0]->estadogarantia != 2)
                                    <div class="col-6">
                                        <form id="frmpaquetes{{$historial->id}}"
                                              action="{{route('solicitarautorizacioncambiopaquete',[$idFranquicia,$idContrato,$historial->id])}}" enctype="multipart/form-data"
                                              method="POST" onsubmit="btnSubmit.disabled = true;">
                                            @csrf
                                            <div class="row">
                                                @if($solicitudCambioPaquete == null || $solicitudCambioPaquete[0]->estatus == 1)
                                                    <div class="col-5"></div>
                                                    <div class="col-7">
                                                        <div class="form-group">
                                                            <a type="button" class="btn btn-outline-success btn-block"
                                                               data-toggle="modal"
                                                               data-target="#modalsolicitarcambiopaquete{{$historial->id}}">Solicitar cambio paquete</a>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($solicitudCambioPaquete != null)
                                                    @if($solicitudCambioPaquete[0]->estatus == 0)
                                                        <div class="col-6">
                                                            <div class="row" style="color: #0AA09E; font-weight: bold; padding-top:10px; padding-left: 15px; margin-top: 30px;">
                                                                Solicitud de cambio de paquete pendiente.
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($solicitudCambioPaquete[0]->estatus == 2)
                                                        <div class="col-6">
                                                            <div class="row" style="color: #ea9999; font-weight: bold; margin-left: 5px; padding-top: 5px; margin-top: 30px;">
                                                                Ultima solicitud de cambio de paquete rechazada.
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <a type="button" class="btn btn-outline-success btn-block"
                                                                   data-toggle="modal"
                                                                   data-target="#modalsolicitarcambiopaquete{{$historial->id}}">Solicitar cambio paquete</a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif

                                                <!--Modal para Solicitar Autorizacion Cambiar paquete-->
                                                <div class="modal fade" id="modalsolicitarcambiopaquete{{$historial->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                                     aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                Solicitud de autorización para cambio de paquete.
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group row">
                                                                    <label class="col-sm-3 col-form-label">Paquetes:</label>
                                                                    <select
                                                                        class="custom-select col-sm-7 {!! $errors->first('paquetehistorialeditar','is-invalid')!!}"
                                                                        name="paquetehistorialeditar{{$historial->id}}">
                                                                        @if(count($paquetes) > 0)
                                                                            <option selected value=''>Seleccionar</option>
                                                                            @foreach($paquetes as $paquete)
                                                                                <option
                                                                                    value="{{$paquete->id}}">
                                                                                    {{$paquete->nombre}}
                                                                                </option>
                                                                            @endforeach
                                                                        @else
                                                                            <option selected>Sin registros</option>
                                                                        @endif
                                                                    </select>
                                                                    {!! $errors->first('paquetehistorialeditar','<div class="invalid-feedback">Elegir un paquete , campo obligatorio
                                                                    </div>')!!}
                                                                    <label class="col-sm-1 col-form-label"></label>
                                                                </div>
                                                                Explica detalladamente el por que requieres cambiar el paquete del contrato:
                                                                <textarea name="mensaje"
                                                                          id="mensaje"
                                                                          class="form-control {!! $errors->first('mensaje','is-invalid')!!}" rows="10"
                                                                          cols="60">
                                                            </textarea>
                                                                {!! $errors->first('mensaje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                                                                <div class="form-group row">
                                                                    <label class="col-sm-12 col-form-label" style="color: #ea9999;">El mensaje debe contener como minimo 15 caracteres y un maximo de
                                                                        1000.</label>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                                                                <button class="btn btn-success" name="btnSubmit" type="submit"
                                                                        form="frmpaquetes{{$historial->id}}">Aceptar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if(($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 5 || $contrato[0]->estatus_estadocontrato == 12
                            || $contrato[0]->estatus_estadocontrato == 4) && $historial->tipo == 0)
                            @if(!$bandera)
                                <div class="row">
                                    <div class="col-3">
                                        <a class="btn btn-outline-danger btn-block"  data-toggle="modal"
                                           data-target="#modalcancelargarantia">Cancelar garantia
                                        </a>
                                    </div>
                                </div>
                            @else
                                @if($historial->cancelargarantia != null)
                                    <div class="row">
                                        @if($historial->optometristaasignado != null)
                                            <div class="col-3">
                                                <label for="">Optometrista
                                                    asignado {{$historial->optometristaasignado}}</label>
                                            </div>
                                        @endif
                                        <div class="col-3">

                                            <a class="btn btn-outline-danger btn-block"  data-toggle="modal"
                                               data-target="#modalcancelargarantia">Cancelar garantia
                                            </a>
                                        </div>
                                    </div>
                                    <div style="color: #ea9999; font-weight: bold; margin-bottom: 5px;"> Recuerda que al reportar garantia solo se deben enviar las micas.</div>
                                    @if($contrato[0]->cuentaregresivafechaentrega >= 0)
                                        <div style="color: #ea9999; font-weight: bold;">Quedan {{$contrato[0]->cuentaregresivafechaentrega}} días para bloquear o deshabilitar garantias a este contrato</div>
                                    @endif
                                @endif
                            @endif
                        @endif
                        <form id="frmActualizarHistorialClinico{{$historial->id}}"
                              action="{{route('actualizarhistorialclinico',[$idFranquicia,$idContrato,$historial->id])}}" enctype="multipart/form-data"
                              method="POST" onsubmit="btnSubmit.disabled = true;">
                            @csrf
                            @if($historial->paquete == 'DORADO 2' || $historial->paquete == 'LECTURA')
                                <h5 style="color: #0AA09E;">Sin conversión</h5>
                                @if($historial->hscesfericoder != null)
                                    <div id="mostrarvision"></div>
                                    <h6>Ojo derecho</h6>
                                    <div class="row">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Esferico</label>
                                                <input type="text" name="esfericodsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                                value="{{$historial->hscesfericoder}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Cilindro</label>
                                                <input type="text" name="cilindrodsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                                value="{{$historial->hsccilindroder}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Eje</label>
                                                <input type="text" name="ejedsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                                value="{{$historial->hscejeder}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Add</label>
                                                <input type="text" name="adddsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                                value="{{$historial->hscaddder}}">
                                            </div>
                                        </div>
                                    </div>
                                    <h6>Ojo Izquierdo</h6>
                                    <div class="row">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Esferico</label>
                                                <input type="text" name="esfericoizqsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                                value="{{$historial->hscesfericoizq}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Cilindro</label>
                                                <input type="text" name="cilindroizqsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                                value="{{$historial->hsccilindroizq}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Eje</label>
                                                <input type="text" name="ejeizqsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                                value="{{$historial->hscejeizq}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Add</label>
                                                <input type="text" name="addizqsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                                value="{{$historial->hscaddizq}}">
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <h6 style="color: #0AA09E; margin-left: 30px">Sin capturar</h6>
                                @endif
                                <h5 style="color: #0AA09E;">Con conversión</h5>
                            @endif
                            <div id="mostrarvision"></div>
                            <h6>Ojo derecho</h6>
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Esferico</label>
                                        <input type="text" name="esfericod{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                        value="{{$historial->esfericoder}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Cilindro</label>
                                        <input type="text" name="cilindrod{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                        value="{{$historial->cilindroder}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Eje</label>
                                        <input type="text" name="ejed{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif value="{{$historial->ejeder}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Add</label>
                                        <input type="text" name="addd{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif value="{{$historial->addder}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>ALT</label>
                                        <input type="text" name="altd{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif value="{{$historial->altder}}">
                                    </div>
                                </div>
                            </div>
                            <h6>Ojo Izquierdo</h6>
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Esferico</label>
                                        <input type="text" name="esfericod2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                        value="{{$historial->esfericoizq}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Cilindro</label>
                                        <input type="text" name="cilindrod2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                        value="{{$historial->cilindroizq}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Eje</label>
                                        <input type="text" name="ejed2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                        value="{{$historial->ejeizq}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Add</label>
                                        <input type="text" name="addd2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                        value="{{$historial->addizq}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>ALT</label>
                                        <input type="text" name="altd2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialBase) readonly @endif
                                        value="{{$historial->altizq}}">
                                    </div>
                                </div>
                            </div>
                            <h6>Material</h6>
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                               id="material{{$historial->id}}" value="0" @if($historial->material == 0) checked @endif
                                               @if($actualizarHistorialBase) onclick="habilitarCamposMaterial('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="material{{$historial->id}}">Hi Index</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                               id="material{{$historial->id}}" value="1" @if($historial->material == 1) checked @endif
                                               @if($actualizarHistorialBase) onclick="habilitarCamposMaterial('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="material{{$historial->id}}">CR</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}" value="2"
                                                   @if($historial->material == 2) checked @endif
                                                   @if($actualizarHistorialBase) onclick="habilitarCamposMaterial('{{$historial->id}}')" @else onclick="return false;" @endif>
                                            <label class="form-check-label" for="material{{$historial->id}}">Policarbonato</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="policarbonato{{$historial->id}}" id="policarbonato{{$historial->id}}"
                                                   value="1" @if($historial->material == 2 && $historial->policarbonatotipo == 1) checked @endif
                                                   @if(!$actualizarHistorialBase) onclick="return false;" @else onclick="seleccionarPolicarbonato('#policarbonato{{$historial->id}}','#lbPolicarbonato{{$historial->id}}')" @endif
                                                   @if($historial->material != 2) disabled @endif>
                                            <label class="custom-control-label" for="policarbonato{{$historial->id}}" id="lbPolicarbonato{{$historial->id}}" name="lbPolicarbonato{{$historial->id}}">@if($historial->policarbonatotipo == 0) Adulto @else Niño @endif</label>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                               id="material{{$historial->id}}" value="3" @if($historial->material == 3) checked @endif
                                               @if($actualizarHistorialBase) onclick="habilitarCamposMaterial('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="material{{$historial->id}}">Otro</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-check">
                                        <input type="text" name="motro{{$historial->id}}" id="motro{{$historial->id}}" class="form-control" placeholder="OTRO"
                                               value="{{$historial->materialotro}}" @if($historial->material != 3) disabled @endif>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-check">
                                        <input type="text" name="costoMaterial{{$historial->id}}" id="costoMaterial{{$historial->id}}" class="form-control"
                                               value="${{$historial->costomaterial}}" @if($historial->material != 3) disabled @endif>
                                    </div>
                                </div>
                            </div>
                            <h6>Tipo de bifocal</h6>
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                               id="bifocal{{$historial->id}}" value="0" @if($historial->bifocal == 0) checked @endif
                                               @if($actualizarHistorialBase) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="bifocal{{$historial->id}}">
                                            FT
                                        </label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                               id="bifocal{{$historial->id}}" value="1" @if($historial->bifocal == 1) checked @endif
                                               @if($actualizarHistorialBase) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="bifocal{{$historial->id}}">
                                            Blend
                                        </label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                               id="bifocal{{$historial->id}}" value="2" @if($historial->bifocal == 2) checked @endif
                                               @if($actualizarHistorialBase) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="bifocal{{$historial->id}}">
                                            Progresivo
                                        </label>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                               id="bifocal{{$historial->id}}" value="3" @if($historial->bifocal == 3) checked @endif
                                               @if($actualizarHistorialBase) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="bifocal{{$historial->id}}">
                                            N/A
                                        </label>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                               id="bifocal{{$historial->id}}" value="4" @if($historial->bifocal == 4) checked @endif
                                               @if($actualizarHistorialBase) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="bifocal{{$historial->id}}">
                                            Otro
                                        </label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="text" name="otroB{{$historial->id}}" id="otroB{{$historial->id}}" class="form-control" min="0" placeholder="OTRO"
                                               value="{{$historial->bifocalotro}}"  @if($historial->bifocal != 4) disabled @endif>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="text" name="costoBifocal{{$historial->id}}" id="costoBifocal{{$historial->id}}" class="form-control" min="0"
                                               value="${{$historial->costobifocal}}" @if($historial->bifocal != 4) disabled @endif>
                                    </div>
                                </div>
                            </div>
                            <h6>Tratamiento</h6>
                            <div class="row">
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input " name="fotocromatico{{$historial->id}}" id="fotocromatico{{$historial->id}}"
                                               @if($historial->fotocromatico == 1) checked @endif @if(!$actualizarHistorialBase) onclick="return false;" @endif>
                                        <label class="custom-control-label" for="fotocromatico{{$historial->id}}">Fotocromatico</label>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input " name="ar{{$historial->id}}" id="ar{{$historial->id}}"
                                               @if($historial->ar == 1) checked @endif @if(!$actualizarHistorialBase) onclick="return false;" @endif>
                                        <label class="custom-control-label" for="ar{{$historial->id}}">A/R</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="tinte{{$historial->id}}" id="tinte{{$historial->id}}"
                                                   @if($historial->tinte == 1) checked @endif
                                                   @if(!$actualizarHistorialBase) onclick="return false;" @else onclick="[habilitarDeshabilitarColoresTratamiento('#tinte{{$historial->id}}', '#colorTinte{{$historial->id}}'), habilitarDeshabilitarColoresTratamiento('#tinte{{$historial->id}}', '#estiloTinte{{$historial->id}}')]" @endif>
                                            <label class="custom-control-label" for="tinte{{$historial->id}}">Tinte</label>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="row">
                                                <div class="col-6">
                                                    <select name="colorTinte{{$historial->id}}" id="colorTinte{{$historial->id}}" class=" form-control" required>
                                                        <option value="">COLOR</option>
                                                        @foreach($coloresTratamientos as $color)
                                                            @if($color->id_tratamiento == 5)
                                                                <option value="{{$color->indice}}" @if($color->indice == $historial->id_tratamientocolortinte) selected @endif>{{$color->color}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <select name="estiloTinte{{$historial->id}}" id="estiloTinte{{$historial->id}}" class=" form-control" required>
                                                        <option value="" selected>ESTILO</option>
                                                        <option value="0" @if($historial->estilotinte == 0) selected @endif>DESVANECIDO</option>
                                                        <option value="1" @if($historial->estilotinte == 1) selected @endif>COMPLETO</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="polarizado{{$historial->id}}" id="polarizado{{$historial->id}}" @if($historial->polarizado == 1) checked @endif
                                            @if(!$actualizarHistorialBase) onclick="return false;"  @else onclick="habilitarDeshabilitarColoresTratamiento('#polarizado{{$historial->id}}', '#colorPolarizado{{$historial->id}}')" @endif>
                                            <label class="custom-control-label" for="polarizado{{$historial->id}}">Polarizado</label>
                                        </div>
                                        <div class="form-group mt-1">
                                            <select name="colorPolarizado{{$historial->id}}" id="colorPolarizado{{$historial->id}}" class=" form-control" required>
                                                <option value="">COLOR</option>
                                                @foreach($coloresTratamientos as $color)
                                                    @if($color->id_tratamiento == 7)
                                                        <option value="{{$color->indice}}" @if($color->indice == $historial->id_tratamientocolorpolarizado) selected @endif>{{$color->color}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="espejo{{$historial->id}}" id="espejo{{$historial->id}}" @if($historial->espejo == 1) checked @endif
                                            @if(!$actualizarHistorialBase) onclick="return false;" @else onclick="habilitarDeshabilitarColoresTratamiento('#espejo{{$historial->id}}', '#colorEspejo{{$historial->id}}')" @endif>
                                            <label class="custom-control-label" for="espejo{{$historial->id}}">Espejo</label>
                                        </div>
                                        <div class="form-group mt-1">
                                            <select name="colorEspejo{{$historial->id}}" id="colorEspejo{{$historial->id}}" class=" form-control" required>
                                                <option value="">COLOR</option>
                                                @foreach($coloresTratamientos as $color)
                                                    @if($color->id_tratamiento == 8)
                                                        <option value="{{$color->indice}}" @if($color->indice == $historial->id_tratamientocolorespejo) selected @endif>{{$color->color}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="blueray{{$historial->id}}" id="blueray{{$historial->id}}"
                                               @if($historial->blueray == 1) checked @endif @if(!$actualizarHistorialBase) onclick="return false;" @endif>
                                        <label class="custom-control-label" for="blueray{{$historial->id}}">BlueRay</label>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input " type="checkbox" name="otroTra{{$historial->id}}" id="otroTra{{$historial->id}}"
                                               @if($historial->otroT == 1) checked @endif
                                               @if(!$actualizarHistorialBase) onclick="return false;" @else onclick="[habilitarDeshabilitarCampoOtroTratamiento('#otroTra{{$historial->id}}', '#otroT{{$historial->id}}'),habilitarDeshabilitarCampoOtroTratamiento('#otroTra{{$historial->id}}', '#costoTratamiento{{$historial->id}}')]" @endif>
                                        <label class="custom-control-label" for="otroTra{{$historial->id}}">Otro</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="text" name="otroT{{$historial->id}}" id="otroT{{$historial->id}}" class="form-control" min="0" placeholder="OTRO"
                                               value="{{$historial->tratamientootro}}">
                                        <div class="invalid-feedback" id="errorOtroT{{$historial->id}}"></div>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="text" name="costoTratamiento{{$historial->id}}" id="costoTratamiento{{$historial->id}}" class="form-control" min="0"
                                               value="${{$historial->costotratamiento}}">
                                        <div class="invalid-feedback" id="errorCostoTratemiento{{$historial->id}}"></div>
                                    </div>
                                </div>
                            </div>
                            @if($actualizarHistorialBase)
                                <button class="btn btn-outline-success btn-block btnActualizarHistorialClinico" name="btnSubmit"
                                        type="button" form="frmActualizarHistorialClinico{{$historial->id}}"
                                        data-toggle="modal" data-target="#confirmacionActualizarHistorial" data_parametros_modal="{{$historial->id}}">Actualizar historial
                                </button>
                            @endif
                        </form>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Observaciones laboratorio</label>
                                    <textarea class="form-control" style="text-transform: uppercase" name="cilindrod2"
                                              rows="4" cols="60" readonly>{{$historial->observaciones}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Observaciones interno</label>
                                    <textarea class="form-control" style="text-transform: uppercase" name="cilindrod2"
                                              rows="4" cols="60" readonly>{{$historial->observacionesinterno}}</textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                    @endforeach
                @else
                    <div class="row">
                        <div class="col-3">
                            <h3 style="margin-top: 10px;">(Sin resultados)</h3>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Seccion de historiales activos-->
            <div class="tab-pane" id="historialActivo" role="tabpanel" aria-labelledby="historialActivo-tab">
                @if($historialesActivos != null)
                    @foreach($historialesActivos as $historial)
                        @if($historial->tipo == 0)
                            <h4 style="margin-top: 10px">Garantias: {{$historial->numGarantias}}</h4>
                        @endif
                        <div class="row">
                            <div class="col-2">
                                @switch($historial->tipo)
                                    @case(0)
                                        <h4 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}
                                            ({{$historial->id}})</h4>
                                        @break
                                    @case(1)
                                        <h4 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}
                                            ({{$historial->id}}) "Garantía de {{$historial->idhistorialpadre}}"</h4>
                                        @break
                                    @case(2)
                                        <h4 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}
                                            ({{$historial->id}}) "Cambio paquete"</h4>
                                        @break
                                @endswitch
                            </div>
                            <div class="col-2">
                                <h4 style="margin-top: 10px;">Modelo: {{$historial->armazon}}</h4>
                            </div>
                            <div class="col-4">
                                <h4 style="margin-top: 10px;">Color: {{$historial->colorarmazon}}</h4>
                            </div>
                            @if($loop->iteration == 1)
                                <div class="col-2">
                                    <h4 style="margin-top: 10px;">Paquete: {{$historial->paquete}}</h4>
                                </div>
                            @endif
                        </div>
                        @if($contrato[0]->estatus_estadocontrato <= 1 || $contrato[0]->estatus_estadocontrato == 9)
                            <form id="frmarmazon" action="{{route('editarHistorialArmazon',[$idFranquicia,$idContrato,$historial->id])}}"
                                  enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                @csrf
                                <div class="row">
                                    <div class="col-4">
                                        <label for="">Armazón</label>
                                        <select class="custom-select"
                                                name="producto">
                                            @if(count($armazones) > 0)
                                                <option selected value=''>Seleccionar</option>
                                                @foreach($armazones as $armazon)
                                                    @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 0)
                                                        @if($armazon->id == $historial->id_producto)
                                                            <option selected value="{{$armazon->id}}">
                                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                                | {{$armazon->piezas}}pza.
                                                            </option>
                                                        @else
                                                            <option
                                                                value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                                | {{$armazon->piezas}}pza.
                                                            </option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @else
                                                <option selected>Sin registros</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <button class="btn btn-outline-success btn-block" name="btnSubmit"
                                                type="submit">Actualizar armazón
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                        @if(str_contains(strtoupper($historial->armazon),'PROPIO'))
                                <form id="frmActualizarFotoArmazon{{$historial->id}}" action="{{route('actualizarfotoarmazon',[$idFranquicia,$contrato[0]->id,$historial->id])}}"
                                      enctype="multipart/form-data" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-1">
                                            <div class="form-group">
                                                <label>Foto armazon</label>
                                                <div data-toggle="modal" data-target="#imagemodal" id="fotoarmazon1" style="cursor: pointer">
                                                    @if(isset($historial->fotoarmazon) && !empty($historial->fotoarmazon) && file_exists($historial->fotoarmazon))
                                                        <img src="{{asset($historial->fotoarmazon)}}" style="width:120px;height:120px;" class="img-thumbnail">
                                                    @else
                                                        <img src="/imagenes/general/administracion/sinfoto.png" style="width:120px;height:120px;" class="img-thumbnail">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="custom-file">
                                                <input type="file" name="fotoArmazon{{$historial->id}}" id="fotoArmazon{{$historial->id}}"
                                                       class="custom-file-input {!! $errors->first('fotoArmazon' . $historial->id,'is-invalid')!!}" accept="image/jpg">
                                                <label class="custom-file-label" for="fotoArmazon">Choose file...</label>
                                                {!! $errors->first('fotoArmazon' . $historial->id,'<div class="invalid-feedback">Foto armazón debe ser en formato JPG y de tamaño maximo 1MB.</div>')!!}
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <button class="btn btn-outline-success btn-block" name="btnSubmit" form="frmActualizarFotoArmazon{{$historial->id}}"
                                                    type="submit" style="margin-top: 0px;">Actualizar foto armazon
                                            </button>
                                        </div>
                                    </div>
                                </form>
                        @endif
                        @if($historial->tipo == 1 && $historial->estadogarantia == 2)
                            @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9)
                                <div style="color: #ea9999; font-weight: bold; margin-bottom: 10px;">
                                    Para cancelar la garantía, debes de comentarle a confirmaciones.
                                </div>
                            @endif
                            @if($contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11)
                                <div style="color: #ea9999; font-weight: bold; margin-bottom: 10px;">
                                    Para cancelar la garantía, debes de comentarle a laboratorio.
                                </div>
                            @endif
                        @endif
                        @if(($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 5 || $contrato[0]->estatus_estadocontrato == 12 || $contrato[0]->estatus_estadocontrato == 4)
                            && $historial->tipo == 0)
                            <div class="row">
                                @if($contrato[0]->cuentaregresivafechaentrega >= 0
                                        || $contrato[0]->garantiacanceladaelmismodia
                                        || !$bandera
                                        || $contrato[0]->estatus_estadocontrato == 12)
                                    <div class="col-6">
                                        <form id="frmgarantia"
                                              action="{{route('agregarGarantiaHistorial',[$idFranquicia,$contrato[0]->id,$historial->id])}}"
                                              enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                            @csrf
                                            @if($historial->optometristaasignado == null)
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label for="">Optometrista</label>
                                                        <select
                                                            class="custom-select {!! $errors->first('optometristagarantia','is-invalid')!!}"
                                                            name="optometristagarantia">
                                                            @if(count($optometristas) > 0)
                                                                <option selected value='nada'>Seleccionar</option>
                                                                @foreach($optometristas as $optometrista)
                                                                    <option
                                                                        value="{{$optometrista->ID}}">
                                                                        {{$optometrista->NAME}}
                                                                    </option>
                                                                @endforeach
                                                            @else
                                                                <option selected>Sin registros</option>
                                                            @endif
                                                        </select>
                                                        {!! $errors->first('optometristagarantia','<div class="invalid-feedback">Elegir un optometrista , campo obligatorio
                                                        </div>')!!}
                                                    </div>
                                                    <div class="col-6">
                                                        <button class="btn btn-outline-success btn-block" name="btnSubmit"
                                                                type="submit">Nueva garantia
                                                        </button>
                                                    </div>
                                                </div>
                                                @if($contrato[0]->cuentaregresivafechaentrega >= 0)
                                                    <div style="color: #ea9999; font-weight: bold;">Quedan {{$contrato[0]->cuentaregresivafechaentrega}} días para bloquear o deshabilitar garantias a este
                                                        contrato
                                                    </div>
                                                @else
                                                    @if($contrato[0]->fechaentrega != null)
                                                        <div style="color: #ea9999; font-weight: bold;">Fecha limite para garantias ya expiró</div>
                                                    @endif
                                                @endif
                                            @endif
                                        </form>
                                    </div>
                                @else
                                    @if($contrato[0]->fechaentrega != null && $bandera)
                                        <div class="col col-6">
                                            @if($solicitudAutorizacion != null)
                                                @if($solicitudAutorizacion[0]->estatus == 0)
                                                    <div style="color: #0AA09E; font-weight: bold;"> Solicitud de garantía pendiente.</div>
                                                @endif
                                                @if($solicitudAutorizacion[0]->estatus == 1)
                                                    <form id="frmgarantia"
                                                          action="{{route('agregarGarantiaHistorial',[$idFranquicia,$contrato[0]->id,$historial->id])}}"
                                                          enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <label for="">Optometrista</label>
                                                                <select
                                                                    class="custom-select {!! $errors->first('optometristagarantia','is-invalid')!!}"
                                                                    name="optometristagarantia">
                                                                    @if(count($optometristas) > 0)
                                                                        <option selected value='nada'>Seleccionar</option>
                                                                        @foreach($optometristas as $optometrista)
                                                                            <option
                                                                                value="{{$optometrista->ID}}">
                                                                                {{$optometrista->NAME}}
                                                                            </option>
                                                                        @endforeach
                                                                    @else
                                                                        <option selected>Sin registros</option>
                                                                    @endif
                                                                </select>
                                                                {!! $errors->first('optometristagarantia','<div class="invalid-feedback">Elegir un optometrista , campo obligatorio
                                                                </div>')!!}
                                                            </div>
                                                            <div class="col-6">
                                                                <button class="btn btn-outline-success btn-block" name="btnSubmit"
                                                                        type="submit">Nueva garantia
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                @endif
                                                @if($solicitudAutorizacion[0]->estatus == 2)
                                                    <div style="margin-bottom: 5px;">
                                                        <a type="button" href="" class="btn btn-outline-success"
                                                           data-toggle="modal"
                                                           data-target="#modalsolicitarautorizacion">Solicitar garantía</a>
                                                    </div>
                                                    <div style="color: #ea9999; font-weight: bold;"> Ultima solicitud de garantía rechazada.</div>
                                                @endif
                                            @else
                                                <div style="margin-bottom: 5px;">
                                                    <a type="button" href="" class="btn btn-outline-success"
                                                       data-toggle="modal"
                                                       data-target="#modalsolicitarautorizacion">Solicitar garantía</a>
                                                </div>
                                            @endif
                                            <div style="color: #ea9999; font-weight: bold;">
                                                Fecha limite para garantias ya expiró
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-6"></div>
                                    @endif
                                @endif

                                @if((Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) && $contrato[0]->estadogarantia != 2)
                                    <div class="col-6">
                                        <form id="frmpaquetes{{$historial->id}}"
                                              action="{{route('solicitarautorizacioncambiopaquete',[$idFranquicia,$idContrato,$historial->id])}}" enctype="multipart/form-data"
                                              method="POST" onsubmit="btnSubmit.disabled = true;">
                                            @csrf
                                            <div class="row">
                                                @if($solicitudCambioPaquete == null || $solicitudCambioPaquete[0]->estatus == 1)
                                                    <div class="col-5"></div>
                                                    <div class="col-7">
                                                        <div class="form-group">
                                                            <a type="button" class="btn btn-outline-success btn-block"
                                                               data-toggle="modal"
                                                               data-target="#modalsolicitarcambiopaquete{{$historial->id}}">Solicitar cambio paquete</a>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($solicitudCambioPaquete != null)
                                                    @if($solicitudCambioPaquete[0]->estatus == 0)
                                                        <div class="col-6">
                                                            <div class="row" style="color: #0AA09E; font-weight: bold; padding-top:10px; padding-left: 15px; margin-top: 30px;">
                                                                Solicitud de cambio de paquete pendiente.
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($solicitudCambioPaquete[0]->estatus == 2)
                                                        <div class="col-6">
                                                            <div class="row" style="color: #ea9999; font-weight: bold; margin-left: 5px; padding-top: 5px; margin-top: 30px;">
                                                                Ultima solicitud de cambio de paquete rechazada.
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <a type="button" class="btn btn-outline-success btn-block"
                                                                   data-toggle="modal"
                                                                   data-target="#modalsolicitarcambiopaquete{{$historial->id}}">Solicitar cambio paquete</a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif

                                                <!--Modal para Solicitar Autorizacion Cambiar paquete-->
                                                <div class="modal fade" id="modalsolicitarcambiopaquete{{$historial->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                                     aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                Solicitud de autorización para cambio de paquete.
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group row">
                                                                    <label class="col-sm-3 col-form-label">Paquetes:</label>
                                                                    <select
                                                                        class="custom-select col-sm-7 {!! $errors->first('paquetehistorialeditar','is-invalid')!!}"
                                                                        name="paquetehistorialeditar{{$historial->id}}">
                                                                        @if(count($paquetes) > 0)
                                                                            <option selected value=''>Seleccionar</option>
                                                                            @foreach($paquetes as $paquete)
                                                                                <option
                                                                                    value="{{$paquete->id}}">
                                                                                    {{$paquete->nombre}}
                                                                                </option>
                                                                            @endforeach
                                                                        @else
                                                                            <option selected>Sin registros</option>
                                                                        @endif
                                                                    </select>
                                                                    {!! $errors->first('paquetehistorialeditar','<div class="invalid-feedback">Elegir un paquete , campo obligatorio
                                                                    </div>')!!}
                                                                    <label class="col-sm-1 col-form-label"></label>
                                                                </div>
                                                                Explica detalladamente el por que requieres cambiar el paquete del contrato:
                                                                <textarea name="mensaje"
                                                                          id="mensaje"
                                                                          class="form-control {!! $errors->first('mensaje','is-invalid')!!}" rows="10"
                                                                          cols="60">
                                                            </textarea>
                                                                {!! $errors->first('mensaje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                                                                <div class="form-group row">
                                                                    <label class="col-sm-12 col-form-label" style="color: #ea9999;">El mensaje debe contener como minimo 15 caracteres y un maximo de
                                                                        1000.</label>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                                                                <button class="btn btn-success" name="btnSubmit" type="submit"
                                                                        form="frmpaquetes{{$historial->id}}">Aceptar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if(($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 5 || $contrato[0]->estatus_estadocontrato == 12
                            || $contrato[0]->estatus_estadocontrato == 4) && $historial->tipo == 0)
                            @if(!$bandera)
                                <div class="row">
                                    <div class="col-3">
                                        <a class="btn btn-outline-danger btn-block"  data-toggle="modal"
                                           data-target="#modalcancelargarantia">Cancelar garantia
                                        </a>
                                    </div>
                                </div>
                            @else
                                @if($historial->cancelargarantia != null)
                                    <div class="row">
                                        @if($historial->optometristaasignado != null)
                                            <div class="col-3">
                                                <label for="">Optometrista
                                                    asignado {{$historial->optometristaasignado}}</label>
                                            </div>
                                        @endif
                                        <div class="col-3">

                                            <a class="btn btn-outline-danger btn-block"  data-toggle="modal"
                                               data-target="#modalcancelargarantia">Cancelar garantia
                                            </a>
                                        </div>
                                    </div>
                                    <div style="color: #ea9999; font-weight: bold; margin-bottom: 5px;"> Recuerda que al reportar garantia solo se deben enviar las micas.</div>
                                    @if($contrato[0]->cuentaregresivafechaentrega >= 0)
                                        <div style="color: #ea9999; font-weight: bold;">Quedan {{$contrato[0]->cuentaregresivafechaentrega}} días para bloquear o deshabilitar garantias a este contrato</div>
                                    @endif
                                @endif
                            @endif
                        @endif
                        <form id="frmActualizarHistorialClinico{{$historial->id}}"
                              action="{{route('actualizarhistorialclinico',[$idFranquicia,$idContrato,$historial->id])}}" enctype="multipart/form-data"
                              method="POST" onsubmit="btnSubmit.disabled = true;">
                            @csrf
                            @if($historial->paquete == 'DORADO 2' || $historial->paquete == 'LECTURA')
                                <h5 style="color: #0AA09E;">Sin conversión</h5>
                                @if($historial->hscesfericoder != null)
                                    <div id="mostrarvision"></div>
                                    <h6>Ojo derecho</h6>
                                    <div class="row">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Esferico</label>
                                                <input type="text" name="esfericodsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                                value="{{$historial->hscesfericoder}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Cilindro</label>
                                                <input type="text" name="cilindrodsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                                value="{{$historial->hsccilindroder}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Eje</label>
                                                <input type="text" name="ejedsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                                value="{{$historial->hscejeder}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Add</label>
                                                <input type="text" name="adddsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                                value="{{$historial->hscaddder}}">
                                            </div>
                                        </div>
                                    </div>
                                    <h6>Ojo Izquierdo</h6>
                                    <div class="row">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Esferico</label>
                                                <input type="text" name="esfericoizqsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                                value="{{$historial->hscesfericoizq}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Cilindro</label>
                                                <input type="text" name="cilindroizqsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                                value="{{$historial->hsccilindroizq}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Eje</label>
                                                <input type="text" name="ejeizqsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                                value="{{$historial->hscejeizq}}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Add</label>
                                                <input type="text" name="addizqsc{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                                value="{{$historial->hscaddizq}}">
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <h6 style="color: #0AA09E; margin-left: 30px">Sin capturar</h6>
                                @endif
                                <h5 style="color: #0AA09E;">Con conversión</h5>
                            @endif
                            <div id="mostrarvision"></div>
                            <h6>Ojo derecho</h6>
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Esferico</label>
                                        <input type="text" name="esfericod{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                        value="{{$historial->esfericoder}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Cilindro</label>
                                        <input type="text" name="cilindrod{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                        value="{{$historial->cilindroder}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Eje</label>
                                        <input type="text" name="ejed{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif value="{{$historial->ejeder}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Add</label>
                                        <input type="text" name="addd{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif value="{{$historial->addder}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>ALT</label>
                                        <input type="text" name="altd{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif value="{{$historial->altder}}">
                                    </div>
                                </div>
                            </div>
                            <h6>Ojo Izquierdo</h6>
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Esferico</label>
                                        <input type="text" name="esfericod2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                        value="{{$historial->esfericoizq}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Cilindro</label>
                                        <input type="text" name="cilindrod2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                        value="{{$historial->cilindroizq}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Eje</label>
                                        <input type="text" name="ejed2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                        value="{{$historial->ejeizq}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>Add</label>
                                        <input type="text" name="addd2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                        value="{{$historial->addizq}}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>ALT</label>
                                        <input type="text" name="altd2{{$historial->id}}" class="form-control" @if(!$actualizarHistorialGarantia) readonly @endif
                                        value="{{$historial->altizq}}">
                                    </div>
                                </div>
                            </div>
                            <h6>Material</h6>
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                               id="material{{$historial->id}}" value="0" @if($historial->material == 0) checked @endif
                                               @if($actualizarHistorialGarantia) onclick="habilitarCamposMaterial('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="material{{$historial->id}}">Hi Index</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                               id="material{{$historial->id}}" value="1" @if($historial->material == 1) checked @endif
                                               @if($actualizarHistorialGarantia) onclick="habilitarCamposMaterial('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="material{{$historial->id}}">CR</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}" value="2"
                                                   @if($historial->material == 2) checked @endif
                                                   @if($actualizarHistorialGarantia) onclick="habilitarCamposMaterial('{{$historial->id}}')" @else onclick="return false;" @endif>
                                            <label class="form-check-label" for="material{{$historial->id}}">Policarbonato</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="policarbonato{{$historial->id}}" id="policarbonato{{$historial->id}}" value="1"
                                                   @if($historial->material == 2 && $historial->policarbonatotipo == 1) checked @endif
                                                   @if(!$actualizarHistorialGarantia) onclick="return false;" @else onclick="seleccionarPolicarbonato('#policarbonato{{$historial->id}}','#lbPolicarbonato{{$historial->id}}')" @endif
                                                   @if($historial->material != 2) disabled @endif>
                                            <label class="custom-control-label" for="policarbonato{{$historial->id}}" id="lbPolicarbonato{{$historial->id}}" name="lbPolicarbonato{{$historial->id}}">@if($historial->policarbonatotipo == 0) Adulto @else Niño @endif</label>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                               id="material{{$historial->id}}" value="3" @if($historial->material == 3) checked @endif
                                               @if($actualizarHistorialGarantia) onclick="habilitarCamposMaterial('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="material{{$historial->id}}">Otro</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-check">
                                        <input type="text" name="motro{{$historial->id}}" id="motro{{$historial->id}}" class="form-control" placeholder="OTRO"
                                               value="{{$historial->materialotro}}" @if($historial->material != 3) disabled @endif>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-check">
                                        <input type="text" name="costoMaterial{{$historial->id}}" id="costoMaterial{{$historial->id}}" class="form-control"
                                               value="${{$historial->costomaterial}}" @if($historial->material != 3) disabled @endif>
                                    </div>
                                </div>
                            </div>
                            <h6>Tipo de bifocal</h6>
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                               id="bifocal{{$historial->id}}" value="0" @if($historial->bifocal == 0) checked @endif
                                               @if($actualizarHistorialGarantia) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="bifocal{{$historial->id}}">
                                            FT
                                        </label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                               id="bifocal{{$historial->id}}" value="1" @if($historial->bifocal == 1) checked @endif
                                               @if($actualizarHistorialGarantia) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="bifocal{{$historial->id}}">
                                            Blend
                                        </label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                               id="bifocal{{$historial->id}}" value="2" @if($historial->bifocal == 2) checked @endif
                                               @if($actualizarHistorialGarantia) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="bifocal{{$historial->id}}">
                                            Progresivo
                                        </label>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                               id="bifocal{{$historial->id}}" value="3" @if($historial->bifocal == 3) checked @endif
                                               @if($actualizarHistorialGarantia) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="bifocal{{$historial->id}}">
                                            N/A
                                        </label>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                               id="bifocal{{$historial->id}}" value="4" @if($historial->bifocal == 4) checked @endif
                                               @if($actualizarHistorialGarantia) onclick="habilitarCamposBifocal('{{$historial->id}}')" @else onclick="return false;" @endif>
                                        <label class="form-check-label" for="bifocal{{$historial->id}}">
                                            Otro
                                        </label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="text" name="otroB{{$historial->id}}" id="otroB{{$historial->id}}" class="form-control" min="0" placeholder="OTRO"
                                               value="{{$historial->bifocalotro}}"  @if($historial->bifocal != 4) disabled @endif>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="text" name="costoBifocal{{$historial->id}}" id="costoBifocal{{$historial->id}}" class="form-control" min="0"
                                               value="${{$historial->costobifocal}}" @if($historial->bifocal != 4) disabled @endif>
                                    </div>
                                </div>
                            </div>
                            <h6>Tratamiento</h6>
                            <div class="row">
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input " name="fotocromatico{{$historial->id}}" id="fotocromatico{{$historial->id}}"
                                               @if($historial->fotocromatico == 1) checked @endif @if(!$actualizarHistorialGarantia) onclick="return false;" @endif>
                                        <label class="custom-control-label" for="fotocromatico{{$historial->id}}">Fotocromatico</label>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input " name="ar{{$historial->id}}" id="ar{{$historial->id}}"
                                               @if($historial->ar == 1) checked @endif @if(!$actualizarHistorialGarantia) onclick="return false;" @endif>
                                        <label class="custom-control-label" for="ar{{$historial->id}}">A/R</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="tinte{{$historial->id}}" id="tinte{{$historial->id}}"
                                                   @if($historial->tinte == 1) checked @endif
                                                   @if(!$actualizarHistorialGarantia) onclick="return false;" @else onclick="[habilitarDeshabilitarColoresTratamiento('#tinte{{$historial->id}}', '#colorTinte{{$historial->id}}'), habilitarDeshabilitarColoresTratamiento('#tinte{{$historial->id}}', '#estiloTinte{{$historial->id}}')]" @endif>
                                            <label class="custom-control-label" for="tinte{{$historial->id}}">Tinte</label>
                                        </div>
                                        <div class="form-group mt-1">
                                            <div class="row">
                                                <div class="col-6">
                                                    <select name="colorTinte{{$historial->id}}" id="colorTinte{{$historial->id}}" class=" form-control" required>
                                                        <option value="">COLOR</option>
                                                        @foreach($coloresTratamientos as $color)
                                                            @if($color->id_tratamiento == 5)
                                                                <option value="{{$color->indice}}" @if($color->indice == $historial->id_tratamientocolortinte) selected @endif>{{$color->color}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <select name="estiloTinte{{$historial->id}}" id="estiloTinte{{$historial->id}}" class=" form-control" required>
                                                        <option value="">ESTILO</option>
                                                        <option value="0" @if($historial->estilotinte == 0) selected @endif>DESVANECIDO</option>
                                                        <option value="1" @if($historial->estilotinte == 1) selected @endif>COMPLETO</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="polarizado{{$historial->id}}" id="polarizado{{$historial->id}}" @if($historial->polarizado == 1) checked @endif
                                            @if(!$actualizarHistorialGarantia) onclick="return false;"  @else onclick="habilitarDeshabilitarColoresTratamiento('#polarizado{{$historial->id}}', '#colorPolarizado{{$historial->id}}')" @endif>
                                            <label class="custom-control-label" for="polarizado{{$historial->id}}">Polarizado</label>
                                        </div>
                                        <div class="form-group mt-1">
                                            <select name="colorPolarizado{{$historial->id}}" id="colorPolarizado{{$historial->id}}" class=" form-control" required>
                                                <option value="">COLOR</option>
                                                @foreach($coloresTratamientos as $color)
                                                    @if($color->id_tratamiento == 7)
                                                        <option value="{{$color->indice}}" @if($color->indice == $historial->id_tratamientocolorpolarizado) selected @endif>{{$color->color}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="espejo{{$historial->id}}" id="espejo{{$historial->id}}" @if($historial->espejo == 1) checked @endif
                                            @if(!$actualizarHistorialGarantia) onclick="return false;" @else onclick="habilitarDeshabilitarColoresTratamiento('#espejo{{$historial->id}}', '#colorEspejo{{$historial->id}}')" @endif>
                                            <label class="custom-control-label" for="espejo{{$historial->id}}">Espejo</label>
                                        </div>
                                        <div class="form-group mt-1">
                                            <select name="colorEspejo{{$historial->id}}" id="colorEspejo{{$historial->id}}" class=" form-control" required>
                                                <option value="">COLOR</option>
                                                @foreach($coloresTratamientos as $color)
                                                    @if($color->id_tratamiento == 8)
                                                        <option value="{{$color->indice}}" @if($color->indice == $historial->id_tratamientocolorespejo) selected @endif>{{$color->color}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="blueray{{$historial->id}}" id="blueray{{$historial->id}}"
                                               @if($historial->blueray == 1) checked @endif @if(!$actualizarHistorialGarantia) onclick="return false;" @endif>
                                        <label class="custom-control-label" for="blueray{{$historial->id}}">BlueRay</label>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input " type="checkbox" name="otroTra{{$historial->id}}" id="otroTra{{$historial->id}}"
                                               @if($historial->otroT == 1) checked @endif
                                               @if(!$actualizarHistorialGarantia) onclick="return false;" @else onclick="[habilitarDeshabilitarCampoOtroTratamiento('#otroTra{{$historial->id}}', '#otroT{{$historial->id}}'),habilitarDeshabilitarCampoOtroTratamiento('#otroTra{{$historial->id}}', '#costoTratamiento{{$historial->id}}')]" @endif>
                                        <label class="custom-control-label" for="otroTra{{$historial->id}}">Otro</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="text" name="otroT{{$historial->id}}" id="otroT{{$historial->id}}" class="form-control" min="0" placeholder="OTRO"
                                               value="{{$historial->tratamientootro}}">
                                        <div class="invalid-feedback" id="errorOtroT{{$historial->id}}"></div>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="text" name="costoTratamiento{{$historial->id}}" id="costoTratamiento{{$historial->id}}" class="form-control" min="0"
                                               value="${{$historial->costotratamiento}}">
                                        <div class="invalid-feedback" id="errorCostoTratemiento{{$historial->id}}"></div>
                                    </div>
                                </div>
                            </div>
                            @if($actualizarHistorialGarantia)
                                <button class="btn btn-outline-success btn-block btnActualizarHistorialClinico" name="btnSubmit"
                                        type="button" form="frmActualizarHistorialClinico{{$historial->id}}"
                                        data-toggle="modal" data-target="#confirmacionActualizarHistorial" data_parametros_modal="{{$historial->id}}">Actualizar historial
                                </button>
                            @endif
                        </form>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Observaciones laboratorio</label>
                                    <textarea class="form-control" style="text-transform: uppercase" name="cilindrod2"
                                              rows="4" cols="60" readonly>{{$historial->observaciones}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Observaciones interno</label>
                                    <textarea class="form-control" style="text-transform: uppercase" name="cilindrod2"
                                              rows="4" cols="60" readonly>{{$historial->observacionesinterno}}</textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                    @endforeach
                @else
                    <div class="row">
                        <div class="col-3">
                            <h3 style="margin-top: 10px;">(Sin resultados)</h3>
                        </div>
                    </div>
                @endif
            </div>

            <!--Seccion historiales garantias terminadas -->
            <div class="tab-pane" id="garantiasTerminadas" role="tabpanel" aria-labelledby="garantiasTerminadas-tab">
                @if($historialesGarantiaTerminada != null)
                    @foreach($historialesGarantiaTerminada as $historial)
                        @if($historial->tipo == 0)
                            <h4 style="margin-top: 10px">Garantias: {{$historial->numGarantias}}</h4>
                        @endif
                        <div class="row">
                            <div class="col-2">
                                @switch($historial->tipo)
                                    @case(0)
                                        <h4 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}
                                            ({{$historial->id}})</h4>
                                        @break
                                    @case(1)
                                        <h4 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}
                                            ({{$historial->id}}) "Garantía de {{$historial->idhistorialpadre}}"</h4>
                                        @break
                                    @case(2)
                                        <h4 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}
                                            ({{$historial->id}}) "Cambio paquete"</h4>
                                        @break
                                @endswitch
                            </div>
                            <div class="col-2">
                                <h4 style="margin-top: 10px;">Modelo: {{$historial->armazon}}</h4>
                            </div>
                            <div class="col-4">
                                <h4 style="margin-top: 10px;">Color: {{$historial->colorarmazon}}</h4>
                            </div>
                            @if($loop->iteration == 1)
                                <div class="col-2">
                                    <h4 style="margin-top: 10px;">Paquete: {{$historial->paquete}}</h4>
                                </div>
                            @endif
                        </div>
                        @if($contrato[0]->estatus_estadocontrato <= 1 || $contrato[0]->estatus_estadocontrato == 9)
                            <form id="frmarmazon" action="{{route('editarHistorialArmazon',[$idFranquicia,$idContrato,$historial->id])}}"
                                  enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                @csrf
                                <div class="row">
                                    <div class="col-4">
                                        <label for="">Armazón</label>
                                        <select class="custom-select"
                                                name="producto">
                                            @if(count($armazones) > 0)
                                                <option selected value=''>Seleccionar</option>
                                                @foreach($armazones as $armazon)
                                                    @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 0)
                                                        @if($armazon->id == $historial->id_producto)
                                                            <option selected value="{{$armazon->id}}">
                                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                                | {{$armazon->piezas}}pza.
                                                            </option>
                                                        @else
                                                            <option
                                                                value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                                | {{$armazon->piezas}}pza.
                                                            </option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @else
                                                <option selected>Sin registros</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <button class="btn btn-outline-success btn-block" name="btnSubmit"
                                                type="submit">Actualizar armazón
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                        @if($historial->tipo == 1 && $historial->estadogarantia == 2)
                            @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9)
                                <div style="color: #ea9999; font-weight: bold; margin-bottom: 10px;">
                                    Para cancelar la garantía, debes de comentarle a confirmaciones.
                                </div>
                            @endif
                            @if($contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11)
                                <div style="color: #ea9999; font-weight: bold; margin-bottom: 10px;">
                                    Para cancelar la garantía, debes de comentarle a laboratorio.
                                </div>
                            @endif
                        @endif
                        @if(($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 5 || $contrato[0]->estatus_estadocontrato == 12 || $contrato[0]->estatus_estadocontrato == 4)
                            && $historial->tipo == 0)
                            <div class="row">
                                @if($contrato[0]->cuentaregresivafechaentrega >= 0
                                        || $contrato[0]->garantiacanceladaelmismodia
                                        || !$bandera
                                        || $contrato[0]->estatus_estadocontrato == 12)
                                    <div class="col-6">
                                        <form id="frmgarantia"
                                              action="{{route('agregarGarantiaHistorial',[$idFranquicia,$contrato[0]->id,$historial->id])}}"
                                              enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                            @csrf
                                            @if($historial->optometristaasignado == null)
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label for="">Optometrista</label>
                                                        <select
                                                            class="custom-select {!! $errors->first('optometristagarantia','is-invalid')!!}"
                                                            name="optometristagarantia">
                                                            @if(count($optometristas) > 0)
                                                                <option selected value='nada'>Seleccionar</option>
                                                                @foreach($optometristas as $optometrista)
                                                                    <option
                                                                        value="{{$optometrista->ID}}">
                                                                        {{$optometrista->NAME}}
                                                                    </option>
                                                                @endforeach
                                                            @else
                                                                <option selected>Sin registros</option>
                                                            @endif
                                                        </select>
                                                        {!! $errors->first('optometristagarantia','<div class="invalid-feedback">Elegir un optometrista , campo obligatorio
                                                        </div>')!!}
                                                    </div>
                                                    <div class="col-6">
                                                        <button class="btn btn-outline-success btn-block" name="btnSubmit"
                                                                type="submit">Nueva garantia
                                                        </button>
                                                    </div>
                                                </div>
                                                @if($contrato[0]->cuentaregresivafechaentrega >= 0)
                                                    <div style="color: #ea9999; font-weight: bold;">Quedan {{$contrato[0]->cuentaregresivafechaentrega}} días para bloquear o deshabilitar garantias a este
                                                        contrato
                                                    </div>
                                                @else
                                                    @if($contrato[0]->fechaentrega != null)
                                                        <div style="color: #ea9999; font-weight: bold;">Fecha limite para garantias ya expiró</div>
                                                    @endif
                                                @endif
                                            @endif
                                        </form>
                                    </div>
                                @else
                                    @if($contrato[0]->fechaentrega != null && $bandera)
                                        <div class="col col-6">
                                            @if($solicitudAutorizacion != null)
                                                @if($solicitudAutorizacion[0]->estatus == 0)
                                                    <div style="color: #0AA09E; font-weight: bold;"> Solicitud de garantía pendiente.</div>
                                                @endif
                                                @if($solicitudAutorizacion[0]->estatus == 1)
                                                    <form id="frmgarantia"
                                                          action="{{route('agregarGarantiaHistorial',[$idFranquicia,$contrato[0]->id,$historial->id])}}"
                                                          enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <label for="">Optometrista</label>
                                                                <select
                                                                    class="custom-select {!! $errors->first('optometristagarantia','is-invalid')!!}"
                                                                    name="optometristagarantia">
                                                                    @if(count($optometristas) > 0)
                                                                        <option selected value='nada'>Seleccionar</option>
                                                                        @foreach($optometristas as $optometrista)
                                                                            <option
                                                                                value="{{$optometrista->ID}}">
                                                                                {{$optometrista->NAME}}
                                                                            </option>
                                                                        @endforeach
                                                                    @else
                                                                        <option selected>Sin registros</option>
                                                                    @endif
                                                                </select>
                                                                {!! $errors->first('optometristagarantia','<div class="invalid-feedback">Elegir un optometrista , campo obligatorio
                                                                </div>')!!}
                                                            </div>
                                                            <div class="col-6">
                                                                <button class="btn btn-outline-success btn-block" name="btnSubmit"
                                                                        type="submit">Nueva garantia
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                @endif
                                                @if($solicitudAutorizacion[0]->estatus == 2)
                                                    <div style="margin-bottom: 5px;">
                                                        <a type="button" href="" class="btn btn-outline-success"
                                                           data-toggle="modal"
                                                           data-target="#modalsolicitarautorizacion">Solicitar garantía</a>
                                                    </div>
                                                    <div style="color: #ea9999; font-weight: bold;"> Ultima solicitud de garantía rechazada.</div>
                                                @endif
                                            @else
                                                <div style="margin-bottom: 5px;">
                                                    <a type="button" href="" class="btn btn-outline-success"
                                                       data-toggle="modal"
                                                       data-target="#modalsolicitarautorizacion">Solicitar garantía</a>
                                                </div>
                                            @endif
                                            <div style="color: #ea9999; font-weight: bold;">
                                                Fecha limite para garantias ya expiró
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-6"></div>
                                    @endif
                                @endif

                                @if((Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) && $contrato[0]->estadogarantia != 2)
                                    <div class="col-6">
                                        <form id="frmpaquetes{{$historial->id}}"
                                              action="{{route('solicitarautorizacioncambiopaquete',[$idFranquicia,$idContrato,$historial->id])}}" enctype="multipart/form-data"
                                              method="POST" onsubmit="btnSubmit.disabled = true;">
                                            @csrf
                                            <div class="row">
                                                @if($solicitudCambioPaquete == null || $solicitudCambioPaquete[0]->estatus == 1)
                                                    <div class="col-5"></div>
                                                    <div class="col-7">
                                                        <div class="form-group">
                                                            <a type="button" class="btn btn-outline-success btn-block"
                                                               data-toggle="modal"
                                                               data-target="#modalsolicitarcambiopaquete{{$historial->id}}">Solicitar cambio paquete</a>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($solicitudCambioPaquete != null)
                                                    @if($solicitudCambioPaquete[0]->estatus == 0)
                                                        <div class="col-6">
                                                            <div class="row" style="color: #0AA09E; font-weight: bold; padding-top:10px; padding-left: 15px; margin-top: 30px;">
                                                                Solicitud de cambio de paquete pendiente.
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($solicitudCambioPaquete[0]->estatus == 2)
                                                        <div class="col-6">
                                                            <div class="row" style="color: #ea9999; font-weight: bold; margin-left: 5px; padding-top: 5px; margin-top: 30px;">
                                                                Ultima solicitud de cambio de paquete rechazada.
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <a type="button" class="btn btn-outline-success btn-block"
                                                                   data-toggle="modal"
                                                                   data-target="#modalsolicitarcambiopaquete{{$historial->id}}">Solicitar cambio paquete</a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif

                                                <!--Modal para Solicitar Autorizacion Cambiar paquete-->
                                                <div class="modal fade" id="modalsolicitarcambiopaquete{{$historial->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                                     aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                Solicitud de autorización para cambio de paquete.
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group row">
                                                                    <label class="col-sm-3 col-form-label">Paquetes:</label>
                                                                    <select
                                                                        class="custom-select col-sm-7 {!! $errors->first('paquetehistorialeditar','is-invalid')!!}"
                                                                        name="paquetehistorialeditar{{$historial->id}}">
                                                                        @if(count($paquetes) > 0)
                                                                            <option selected value=''>Seleccionar</option>
                                                                            @foreach($paquetes as $paquete)
                                                                                <option
                                                                                    value="{{$paquete->id}}">
                                                                                    {{$paquete->nombre}}
                                                                                </option>
                                                                            @endforeach
                                                                        @else
                                                                            <option selected>Sin registros</option>
                                                                        @endif
                                                                    </select>
                                                                    {!! $errors->first('paquetehistorialeditar','<div class="invalid-feedback">Elegir un paquete , campo obligatorio
                                                                    </div>')!!}
                                                                    <label class="col-sm-1 col-form-label"></label>
                                                                </div>
                                                                Explica detalladamente el por que requieres cambiar el paquete del contrato:
                                                                <textarea name="mensaje"
                                                                          id="mensaje"
                                                                          class="form-control {!! $errors->first('mensaje','is-invalid')!!}" rows="10"
                                                                          cols="60">
                                                            </textarea>
                                                                {!! $errors->first('mensaje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                                                                <div class="form-group row">
                                                                    <label class="col-sm-12 col-form-label" style="color: #ea9999;">El mensaje debe contener como minimo 15 caracteres y un maximo de
                                                                        1000.</label>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                                                                <button class="btn btn-success" name="btnSubmit" type="submit"
                                                                        form="frmpaquetes{{$historial->id}}">Aceptar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if(($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 5 || $contrato[0]->estatus_estadocontrato == 12
                            || $contrato[0]->estatus_estadocontrato == 4) && $historial->tipo == 0)
                            @if(!$bandera)
                                <div class="row">
                                    <div class="col-3">
                                        <a class="btn btn-outline-danger btn-block"  data-toggle="modal"
                                           data-target="#modalcancelargarantia">Cancelar garantia
                                        </a>
                                    </div>
                                </div>
                            @else
                                @if($historial->cancelargarantia != null)
                                    <div class="row">
                                        @if($historial->optometristaasignado != null)
                                            <div class="col-3">
                                                <label for="">Optometrista
                                                    asignado {{$historial->optometristaasignado}}</label>
                                            </div>
                                        @endif
                                        <div class="col-3">

                                            <a class="btn btn-outline-danger btn-block"  data-toggle="modal"
                                               data-target="#modalcancelargarantia">Cancelar garantia
                                            </a>
                                        </div>
                                    </div>
                                    <div style="color: #ea9999; font-weight: bold; margin-bottom: 5px;"> Recuerda que al reportar garantia solo se deben enviar las micas.</div>
                                    @if($contrato[0]->cuentaregresivafechaentrega >= 0)
                                        <div style="color: #ea9999; font-weight: bold;">Quedan {{$contrato[0]->cuentaregresivafechaentrega}} días para bloquear o deshabilitar garantias a este contrato</div>
                                    @endif
                                @endif
                            @endif
                        @endif
                        @if($historial->paquete == 'DORADO 2' || $historial->paquete == 'LECTURA')
                            <h5 style="color: #0AA09E;">Sin conversión</h5>
                            @if($historial->hscesfericoder != null)
                                <div id="mostrarvision"></div>
                                <h6>Ojo derecho</h6>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Esferico</label>
                                            <input type="text" name="esfericod" class="form-control" readonly
                                                   value="{{$historial->hscesfericoder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Cilindro</label>
                                            <input type="text" name="cilindrod" class="form-control" readonly
                                                   value="{{$historial->hsccilindroder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Eje</label>
                                            <input type="text" name="ejed" class="form-control" readonly
                                                   value="{{$historial->hscejeder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Add</label>
                                            <input type="text" name="addd" class="form-control" readonly
                                                   value="{{$historial->hscaddder}}">
                                        </div>
                                    </div>
                                </div>
                                <h6>Ojo Izquierdo</h6>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Esferico</label>
                                            <input type="text" name="esfericod2" class="form-control" readonly
                                                   value="{{$historial->hscesfericoizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Cilindro</label>
                                            <input type="text" name="cilindrod2" class="form-control" readonly
                                                   value="{{$historial->hsccilindroizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Eje</label>
                                            <input type="text" name="ejed2" class="form-control" readonly
                                                   value="{{$historial->hscejeizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Add</label>
                                            <input type="text" name="addd2" class="form-control" readonly
                                                   value="{{$historial->hscaddizq}}">
                                        </div>
                                    </div>
                                </div>
                            @else
                                <h6 style="color: #0AA09E; margin-left: 30px">Sin capturar</h6>
                            @endif
                            <h5 style="color: #0AA09E;">Con conversión</h5>
                        @endif
                        <div id="mostrarvision"></div>
                        <h6>Ojo derecho</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Esferico</label>
                                    <input type="text" name="esfericod" class="form-control" readonly
                                           value="{{$historial->esfericoder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Cilindro</label>
                                    <input type="text" name="cilindrod" class="form-control" readonly
                                           value="{{$historial->cilindroder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Eje</label>
                                    <input type="text" name="ejed" class="form-control" readonly value="{{$historial->ejeder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Add</label>
                                    <input type="text" name="addd" class="form-control" readonly value="{{$historial->addder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>ALT</label>
                                    <input type="text" name="altd" class="form-control" readonly value="{{$historial->altder}}">
                                </div>
                            </div>
                        </div>
                        <h6>Ojo Izquierdo</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Esferico</label>
                                    <input type="text" name="esfericod2" class="form-control" readonly
                                           value="{{$historial->esfericoizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Cilindro</label>
                                    <input type="text" name="cilindrod2" class="form-control" readonly
                                           value="{{$historial->cilindroizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Eje</label>
                                    <input type="text" name="ejed2" class="form-control" readonly
                                           value="{{$historial->ejeizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Add</label>
                                    <input type="text" name="addd2" class="form-control" readonly
                                           value="{{$historial->addizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>ALT</label>
                                    <input type="text" name="altd2" class="form-control" readonly
                                           value="{{$historial->altizq}}">
                                </div>
                            </div>
                        </div>
                        <h6>Material</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                           id="material{{$historial->id}}" @if($historial->material == 0) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">Hi Index</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                           id="material{{$historial->id}}" @if($historial->material == 1) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">CR</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}"
                                               @if($historial->material == 2) checked @endif onclick="return false;">
                                        <label class="form-check-label" for="material{{$historial->id}}">Policarbonato</label>
                                    </div>
                                    @if($historial->material == 2 && ($historial->policarbonatotipo == 0 || $historial->policarbonatotipo == 1))
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="@if($historial->policarbonatotipo == 0) ADULTO @else NIÑO @endif">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                           id="material{{$historial->id}}" @if($historial->material == 3) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">Otro</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input type="text" name="motro" class="form-control" placeholder="Otro"
                                           value="{{$historial->materialotro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input type="text" name="costoMaterial" class="form-control"
                                           value="${{$historial->costomaterial}}" readonly>
                                </div>
                            </div>
                        </div>
                        <h6>Tipo de bifocal</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                           id="exampleRadios{{$historial->id}}" @if($historial->bifocal == 0) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        FT
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                           id="exampleRadios{{$historial->id}}" @if($historial->bifocal == 1) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Blend
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                           id="exampleRadios{{$historial->id}}" @if($historial->bifocal == 2) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Progresivo
                                    </label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                           id="exampleRadios{{$historial->id}}" @if($historial->bifocal == 3) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        N/A
                                    </label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                           id="exampleRadios{{$historial->id}}" @if($historial->bifocal == 4) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Otro
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="otroB" class="form-control" min="0" placeholder="Otro"
                                           value="{{$historial->bifocalotro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="costoBifocal" class="form-control" min="0"
                                           value="${{$historial->costobifocal}}" readonly>
                                </div>
                            </div>
                        </div>
                        <h6>Tratamiento</h6>
                        <div class="row">
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input " name="fotocromatico" id="customCheck9"
                                           @if($historial->fotocromatico == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck9">Fotocromatico</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input " name="ar" id="customCheck10"
                                           @if($historial->ar == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck10">A/R</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="tinte" id="customCheck11" @if($historial->tinte == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck11">Tinte</label>
                                    </div>
                                    @if($historial->tinte == 1 && ($historial->colortinte != null && $historial->estilotinte != null))
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colortinte}} | @if($historial->estilotinte == 0) DESVANECIDO @else COMPLETO @endif">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="polarizado" id="customCheck14" @if($historial->polarizado == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck14">Polarizado</label>
                                    </div>
                                    @if($historial->polarizado == 1 && $historial->colorpolarizado != null)
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colorpolarizado}}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="espejo" id="customCheck15" @if($historial->espejo == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck15">Espejo</label>
                                    </div>
                                    @if($historial->espejo == 1 && $historial->colorespejo != null)
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colorespejo}}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="blueray" id="customCheck12"
                                           @if($historial->blueray == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck12">BlueRay</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input " type="checkbox" name="otroTra" id="customCheck13"
                                           @if($historial->otroT == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck13">Otro</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="otroT" class="form-control" min="0" placeholder="Otro"
                                           value="{{$historial->tratamientootro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="costoTratamiento" class="form-control" min="0"
                                           value="${{$historial->costotratamiento}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Observaciones laboratorio</label>
                                    <textarea class="form-control" style="text-transform: uppercase" name="cilindrod2"
                                              rows="4" cols="60" readonly>{{$historial->observaciones}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Observaciones interno</label>
                                    <textarea class="form-control" style="text-transform: uppercase" name="cilindrod2"
                                              rows="4" cols="60" readonly>{{$historial->observacionesinterno}}</textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                    @endforeach
                @else
                    <div class="row">
                        <div class="col-3">
                            <h3 style="margin-top: 10px;">(Sin resultados)</h3>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Seccion de historiales garantias canceladas-->
            <div class="tab-pane" id="historialCancelados" role="tabpanel" aria-labelledby="historialCancelados-tab">
                @if($historialesCancelados != null)
                    @foreach($historialesCancelados as $historial)
                        @if($historial->tipo == 0)
                            <h4 style="margin-top: 10px">Garantias: {{$historial->numGarantias}}</h4>
                        @endif
                        <div class="row">
                            <div class="col-2">
                                @switch($historial->tipo)
                                    @case(0)
                                        <h4 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}
                                            ({{$historial->id}})</h4>
                                        @break
                                    @case(1)
                                        <h4 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}
                                            ({{$historial->id}}) "Garantía de {{$historial->idhistorialpadre}}"</h4>
                                        @break
                                    @case(2)
                                        <h4 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}
                                            ({{$historial->id}}) "Cambio paquete"</h4>
                                        @break
                                @endswitch
                            </div>
                            <div class="col-2">
                                <h4 style="margin-top: 10px;">Modelo: {{$historial->armazon}}</h4>
                            </div>
                            <div class="col-4">
                                <h4 style="margin-top: 10px;">Color: {{$historial->colorarmazon}}</h4>
                            </div>
                            @if($loop->iteration == 1)
                                <div class="col-2">
                                    <h4 style="margin-top: 10px;">Paquete: {{$historial->paquete}}</h4>
                                </div>
                            @endif
                        </div>
                        @if($contrato[0]->estatus_estadocontrato <= 1 || $contrato[0]->estatus_estadocontrato == 9)
                            <form id="frmarmazon" action="{{route('editarHistorialArmazon',[$idFranquicia,$idContrato,$historial->id])}}"
                                  enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                @csrf
                                <div class="row">
                                    <div class="col-4">
                                        <label for="">Armazón</label>
                                        <select class="custom-select"
                                                name="producto">
                                            @if(count($armazones) > 0)
                                                <option selected value=''>Seleccionar</option>
                                                @foreach($armazones as $armazon)
                                                    @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 0)
                                                        @if($armazon->id == $historial->id_producto)
                                                            <option selected value="{{$armazon->id}}">
                                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                                | {{$armazon->piezas}}pza.
                                                            </option>
                                                        @else
                                                            <option
                                                                value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                                | {{$armazon->piezas}}pza.
                                                            </option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @else
                                                <option selected>Sin registros</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <button class="btn btn-outline-success btn-block" name="btnSubmit"
                                                type="submit">Actualizar armazón
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                        @if($historial->tipo == 1 && $historial->estadogarantia == 2)
                            @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9)
                                <div style="color: #ea9999; font-weight: bold; margin-bottom: 10px;">
                                    Para cancelar la garantía, debes de comentarle a confirmaciones.
                                </div>
                            @endif
                            @if($contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11)
                                <div style="color: #ea9999; font-weight: bold; margin-bottom: 10px;">
                                    Para cancelar la garantía, debes de comentarle a laboratorio.
                                </div>
                            @endif
                        @endif
                        @if(($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 5 || $contrato[0]->estatus_estadocontrato == 12 || $contrato[0]->estatus_estadocontrato == 4)
                            && $historial->tipo == 0)
                            <div class="row">
                                @if($contrato[0]->cuentaregresivafechaentrega >= 0
                                        || $contrato[0]->garantiacanceladaelmismodia
                                        || !$bandera
                                        || $contrato[0]->estatus_estadocontrato == 12)
                                    <div class="col-6">
                                        <form id="frmgarantia"
                                              action="{{route('agregarGarantiaHistorial',[$idFranquicia,$contrato[0]->id,$historial->id])}}"
                                              enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                            @csrf
                                            @if($historial->optometristaasignado == null)
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label for="">Optometrista</label>
                                                        <select
                                                            class="custom-select {!! $errors->first('optometristagarantia','is-invalid')!!}"
                                                            name="optometristagarantia">
                                                            @if(count($optometristas) > 0)
                                                                <option selected value='nada'>Seleccionar</option>
                                                                @foreach($optometristas as $optometrista)
                                                                    <option
                                                                        value="{{$optometrista->ID}}">
                                                                        {{$optometrista->NAME}}
                                                                    </option>
                                                                @endforeach
                                                            @else
                                                                <option selected>Sin registros</option>
                                                            @endif
                                                        </select>
                                                        {!! $errors->first('optometristagarantia','<div class="invalid-feedback">Elegir un optometrista , campo obligatorio
                                                        </div>')!!}
                                                    </div>
                                                    <div class="col-6">
                                                        <button class="btn btn-outline-success btn-block" name="btnSubmit"
                                                                type="submit">Nueva garantia
                                                        </button>
                                                    </div>
                                                </div>
                                                @if($contrato[0]->cuentaregresivafechaentrega >= 0)
                                                    <div style="color: #ea9999; font-weight: bold;">Quedan {{$contrato[0]->cuentaregresivafechaentrega}} días para bloquear o deshabilitar garantias a este
                                                        contrato
                                                    </div>
                                                @else
                                                    @if($contrato[0]->fechaentrega != null)
                                                        <div style="color: #ea9999; font-weight: bold;">Fecha limite para garantias ya expiró</div>
                                                    @endif
                                                @endif
                                            @endif
                                        </form>
                                    </div>
                                @else
                                    @if($contrato[0]->fechaentrega != null && $bandera)
                                        <div class="col col-6">
                                            @if($solicitudAutorizacion != null)
                                                @if($solicitudAutorizacion[0]->estatus == 0)
                                                    <div style="color: #0AA09E; font-weight: bold;"> Solicitud de garantía pendiente.</div>
                                                @endif
                                                @if($solicitudAutorizacion[0]->estatus == 1)
                                                    <form id="frmgarantia"
                                                          action="{{route('agregarGarantiaHistorial',[$idFranquicia,$contrato[0]->id,$historial->id])}}"
                                                          enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <label for="">Optometrista</label>
                                                                <select
                                                                    class="custom-select {!! $errors->first('optometristagarantia','is-invalid')!!}"
                                                                    name="optometristagarantia">
                                                                    @if(count($optometristas) > 0)
                                                                        <option selected value='nada'>Seleccionar</option>
                                                                        @foreach($optometristas as $optometrista)
                                                                            <option
                                                                                value="{{$optometrista->ID}}">
                                                                                {{$optometrista->NAME}}
                                                                            </option>
                                                                        @endforeach
                                                                    @else
                                                                        <option selected>Sin registros</option>
                                                                    @endif
                                                                </select>
                                                                {!! $errors->first('optometristagarantia','<div class="invalid-feedback">Elegir un optometrista , campo obligatorio
                                                                </div>')!!}
                                                            </div>
                                                            <div class="col-6">
                                                                <button class="btn btn-outline-success btn-block" name="btnSubmit"
                                                                        type="submit">Nueva garantia
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                @endif
                                                @if($solicitudAutorizacion[0]->estatus == 2)
                                                    <div style="margin-bottom: 5px;">
                                                        <a type="button" href="" class="btn btn-outline-success"
                                                           data-toggle="modal"
                                                           data-target="#modalsolicitarautorizacion">Solicitar garantía</a>
                                                    </div>
                                                    <div style="color: #ea9999; font-weight: bold;"> Ultima solicitud de garantía rechazada.</div>
                                                @endif
                                            @else
                                                <div style="margin-bottom: 5px;">
                                                    <a type="button" href="" class="btn btn-outline-success"
                                                       data-toggle="modal"
                                                       data-target="#modalsolicitarautorizacion">Solicitar garantía</a>
                                                </div>
                                            @endif
                                            <div style="color: #ea9999; font-weight: bold;">
                                                Fecha limite para garantias ya expiró
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-6"></div>
                                    @endif
                                @endif

                                @if((Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) && $contrato[0]->estadogarantia != 2)
                                    <div class="col-6">
                                        <form id="frmpaquetes{{$historial->id}}"
                                              action="{{route('solicitarautorizacioncambiopaquete',[$idFranquicia,$idContrato,$historial->id])}}" enctype="multipart/form-data"
                                              method="POST" onsubmit="btnSubmit.disabled = true;">
                                            @csrf
                                            <div class="row">
                                                @if($solicitudCambioPaquete == null || $solicitudCambioPaquete[0]->estatus == 1)
                                                    <div class="col-5"></div>
                                                    <div class="col-7">
                                                        <div class="form-group">
                                                            <a type="button" class="btn btn-outline-success btn-block"
                                                               data-toggle="modal"
                                                               data-target="#modalsolicitarcambiopaquete{{$historial->id}}">Solicitar cambio paquete</a>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($solicitudCambioPaquete != null)
                                                    @if($solicitudCambioPaquete[0]->estatus == 0)
                                                        <div class="col-6">
                                                            <div class="row" style="color: #0AA09E; font-weight: bold; padding-top:10px; padding-left: 15px; margin-top: 30px;">
                                                                Solicitud de cambio de paquete pendiente.
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($solicitudCambioPaquete[0]->estatus == 2)
                                                        <div class="col-6">
                                                            <div class="row" style="color: #ea9999; font-weight: bold; margin-left: 5px; padding-top: 5px; margin-top: 30px;">
                                                                Ultima solicitud de cambio de paquete rechazada.
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <a type="button" class="btn btn-outline-success btn-block"
                                                                   data-toggle="modal"
                                                                   data-target="#modalsolicitarcambiopaquete{{$historial->id}}">Solicitar cambio paquete</a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif

                                                <!--Modal para Solicitar Autorizacion Cambiar paquete-->
                                                <div class="modal fade" id="modalsolicitarcambiopaquete{{$historial->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                                     aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                Solicitud de autorización para cambio de paquete.
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group row">
                                                                    <label class="col-sm-3 col-form-label">Paquetes:</label>
                                                                    <select
                                                                        class="custom-select col-sm-7 {!! $errors->first('paquetehistorialeditar','is-invalid')!!}"
                                                                        name="paquetehistorialeditar{{$historial->id}}">
                                                                        @if(count($paquetes) > 0)
                                                                            <option selected value=''>Seleccionar</option>
                                                                            @foreach($paquetes as $paquete)
                                                                                <option
                                                                                    value="{{$paquete->id}}">
                                                                                    {{$paquete->nombre}}
                                                                                </option>
                                                                            @endforeach
                                                                        @else
                                                                            <option selected>Sin registros</option>
                                                                        @endif
                                                                    </select>
                                                                    {!! $errors->first('paquetehistorialeditar','<div class="invalid-feedback">Elegir un paquete , campo obligatorio
                                                                    </div>')!!}
                                                                    <label class="col-sm-1 col-form-label"></label>
                                                                </div>
                                                                Explica detalladamente el por que requieres cambiar el paquete del contrato:
                                                                <textarea name="mensaje"
                                                                          id="mensaje"
                                                                          class="form-control {!! $errors->first('mensaje','is-invalid')!!}" rows="10"
                                                                          cols="60">
                                                            </textarea>
                                                                {!! $errors->first('mensaje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                                                                <div class="form-group row">
                                                                    <label class="col-sm-12 col-form-label" style="color: #ea9999;">El mensaje debe contener como minimo 15 caracteres y un maximo de
                                                                        1000.</label>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                                                                <button class="btn btn-success" name="btnSubmit" type="submit"
                                                                        form="frmpaquetes{{$historial->id}}">Aceptar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if(($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 5 || $contrato[0]->estatus_estadocontrato == 12
                            || $contrato[0]->estatus_estadocontrato == 4) && $historial->tipo == 0)
                            @if(!$bandera)
                                <div class="row">
                                    <div class="col-3">
                                        <a class="btn btn-outline-danger btn-block"  data-toggle="modal"
                                           data-target="#modalcancelargarantia">Cancelar garantia
                                        </a>
                                    </div>
                                </div>
                            @else
                                @if($historial->cancelargarantia != null)
                                    <div class="row">
                                        @if($historial->optometristaasignado != null)
                                            <div class="col-3">
                                                <label for="">Optometrista
                                                    asignado {{$historial->optometristaasignado}}</label>
                                            </div>
                                        @endif
                                        <div class="col-3">

                                            <a class="btn btn-outline-danger btn-block"  data-toggle="modal"
                                               data-target="#modalcancelargarantia">Cancelar garantia
                                            </a>
                                        </div>
                                    </div>
                                    <div style="color: #ea9999; font-weight: bold; margin-bottom: 5px;"> Recuerda que al reportar garantia solo se deben enviar las micas.</div>
                                    @if($contrato[0]->cuentaregresivafechaentrega >= 0)
                                        <div style="color: #ea9999; font-weight: bold;">Quedan {{$contrato[0]->cuentaregresivafechaentrega}} días para bloquear o deshabilitar garantias a este contrato</div>
                                    @endif
                                @endif
                            @endif
                        @endif
                        @if($historial->paquete == 'DORADO 2' || $historial->paquete == 'LECTURA')
                            <h5 style="color: #0AA09E;">Sin conversión</h5>
                            @if($historial->hscesfericoder != null)
                                <div id="mostrarvision"></div>
                                <h6>Ojo derecho</h6>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Esferico</label>
                                            <input type="text" name="esfericod" class="form-control" readonly
                                                   value="{{$historial->hscesfericoder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Cilindro</label>
                                            <input type="text" name="cilindrod" class="form-control" readonly
                                                   value="{{$historial->hsccilindroder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Eje</label>
                                            <input type="text" name="ejed" class="form-control" readonly
                                                   value="{{$historial->hscejeder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Add</label>
                                            <input type="text" name="addd" class="form-control" readonly
                                                   value="{{$historial->hscaddder}}">
                                        </div>
                                    </div>
                                </div>
                                <h6>Ojo Izquierdo</h6>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Esferico</label>
                                            <input type="text" name="esfericod2" class="form-control" readonly
                                                   value="{{$historial->hscesfericoizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Cilindro</label>
                                            <input type="text" name="cilindrod2" class="form-control" readonly
                                                   value="{{$historial->hsccilindroizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Eje</label>
                                            <input type="text" name="ejed2" class="form-control" readonly
                                                   value="{{$historial->hscejeizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Add</label>
                                            <input type="text" name="addd2" class="form-control" readonly
                                                   value="{{$historial->hscaddizq}}">
                                        </div>
                                    </div>
                                </div>
                            @else
                                <h6 style="color: #0AA09E; margin-left: 30px">Sin capturar</h6>
                            @endif
                            <h5 style="color: #0AA09E;">Con conversión</h5>
                        @endif
                        <div id="mostrarvision"></div>
                        <h6>Ojo derecho</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Esferico</label>
                                    <input type="text" name="esfericod" class="form-control" readonly
                                           value="{{$historial->esfericoder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Cilindro</label>
                                    <input type="text" name="cilindrod" class="form-control" readonly
                                           value="{{$historial->cilindroder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Eje</label>
                                    <input type="text" name="ejed" class="form-control" readonly value="{{$historial->ejeder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Add</label>
                                    <input type="text" name="addd" class="form-control" readonly value="{{$historial->addder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>ALT</label>
                                    <input type="text" name="altd" class="form-control" readonly value="{{$historial->altder}}">
                                </div>
                            </div>
                        </div>
                        <h6>Ojo Izquierdo</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Esferico</label>
                                    <input type="text" name="esfericod2" class="form-control" readonly
                                           value="{{$historial->esfericoizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Cilindro</label>
                                    <input type="text" name="cilindrod2" class="form-control" readonly
                                           value="{{$historial->cilindroizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Eje</label>
                                    <input type="text" name="ejed2" class="form-control" readonly
                                           value="{{$historial->ejeizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Add</label>
                                    <input type="text" name="addd2" class="form-control" readonly
                                           value="{{$historial->addizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>ALT</label>
                                    <input type="text" name="altd2" class="form-control" readonly
                                           value="{{$historial->altizq}}">
                                </div>
                            </div>
                        </div>
                        <h6>Material</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                           id="material{{$historial->id}}" @if($historial->material == 0) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">Hi Index</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                           id="material{{$historial->id}}" @if($historial->material == 1) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">CR</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}"
                                               @if($historial->material == 2) checked @endif onclick="return false;">
                                        <label class="form-check-label" for="material{{$historial->id}}">Policarbonato</label>
                                    </div>
                                    @if($historial->material == 2 && ($historial->policarbonatotipo == 0 || $historial->policarbonatotipo == 1))
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="@if($historial->policarbonatotipo == 0) ADULTO @else NIÑO @endif">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                           id="material{{$historial->id}}" @if($historial->material == 3) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">Otro</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input type="text" name="motro" class="form-control" placeholder="Otro"
                                           value="{{$historial->materialotro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input type="text" name="costoMaterial" class="form-control"
                                           value="${{$historial->costomaterial}}" readonly>
                                </div>
                            </div>
                        </div>
                        <h6>Tipo de bifocal</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                           id="exampleRadios{{$historial->id}}" @if($historial->bifocal == 0) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        FT
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                           id="exampleRadios{{$historial->id}}" @if($historial->bifocal == 1) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Blend
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                           id="exampleRadios{{$historial->id}}" @if($historial->bifocal == 2) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Progresivo
                                    </label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                           id="exampleRadios{{$historial->id}}" @if($historial->bifocal == 3) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        N/A
                                    </label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                           id="exampleRadios{{$historial->id}}" @if($historial->bifocal == 4) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Otro
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="otroB" class="form-control" min="0" placeholder="Otro"
                                           value="{{$historial->bifocalotro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="costoBifocal" class="form-control" min="0"
                                           value="${{$historial->costobifocal}}" readonly>
                                </div>
                            </div>
                        </div>
                        <h6>Tratamiento</h6>
                        <div class="row">
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input " name="fotocromatico" id="customCheck9"
                                           @if($historial->fotocromatico == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck9">Fotocromatico</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input " name="ar" id="customCheck10"
                                           @if($historial->ar == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck10">A/R</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="tinte" id="customCheck11" @if($historial->tinte == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck11">Tinte</label>
                                    </div>
                                    @if($historial->tinte == 1 && ($historial->colortinte != null && $historial->estilotinte != null))
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colortinte}} | @if($historial->estilotinte == 0) DESVANECIDO @else COMPLETO @endif">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="polarizado" id="customCheck14" @if($historial->polarizado == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck14">Polarizado</label>
                                    </div>
                                    @if($historial->polarizado == 1 && $historial->colorpolarizado != null)
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colorpolarizado}}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="espejo" id="customCheck15" @if($historial->espejo == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck15">Espejo</label>
                                    </div>
                                    @if($historial->espejo == 1 && $historial->colorespejo != null)
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colorespejo}}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="blueray" id="customCheck12"
                                           @if($historial->blueray == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck12">BlueRay</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input " type="checkbox" name="otroTra" id="customCheck13"
                                           @if($historial->otroT == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck13">Otro</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="otroT" class="form-control" min="0" placeholder="Otro"
                                           value="{{$historial->tratamientootro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="costoTratamiento" class="form-control" min="0"
                                           value="${{$historial->costotratamiento}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Observaciones laboratorio</label>
                                    <textarea class="form-control" style="text-transform: uppercase" name="cilindrod2"
                                              rows="4" cols="60" readonly>{{$historial->observaciones}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Observaciones interno</label>
                                    <textarea class="form-control" style="text-transform: uppercase" name="cilindrod2"
                                              rows="4" cols="60" readonly>{{$historial->observacionesinterno}}</textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                    @endforeach
                @else
                    <div class="row">
                        <div class="col-3">
                            <h3 style="margin-top: 10px;">(Sin resultados)</h3>
                        </div>
                    </div>
                @endif
            </div>

            <!--Seccion de historiales cambio de paquete-->
            <div class="tab-pane" id="historialCambioPaquete" role="tabpanel" aria-labelledby="historialCambioPaquete-tab">
                @if($historialesCambio != null)
                    @foreach($historialesCambio as $historial)
                        @if($historial->tipo == 0)
                            <h4 style="margin-top: 10px">Garantias: {{$historial->numGarantias}}</h4>
                        @endif
                        <div class="row">
                            <div class="col-2">
                                @switch($historial->tipo)
                                    @case(0)
                                        <h4 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}
                                            ({{$historial->id}})</h4>
                                        @break
                                    @case(1)
                                        <h4 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}
                                            ({{$historial->id}}) "Garantía de {{$historial->idhistorialpadre}}"</h4>
                                        @break
                                    @case(2)
                                        <h4 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}
                                            ({{$historial->id}}) "Cambio paquete"</h4>
                                        @break
                                @endswitch
                            </div>
                            <div class="col-2">
                                <h4 style="margin-top: 10px;">Modelo: {{$historial->armazon}}</h4>
                            </div>
                            <div class="col-4">
                                <h4 style="margin-top: 10px;">Color: {{$historial->colorarmazon}}</h4>
                            </div>
                            @if($loop->iteration == 1)
                                <div class="col-2">
                                    <h4 style="margin-top: 10px;">Paquete: {{$historial->paquete}}</h4>
                                </div>
                            @endif
                        </div>
                        @if($contrato[0]->estatus_estadocontrato <= 1 || $contrato[0]->estatus_estadocontrato == 9)
                            <form id="frmarmazon" action="{{route('editarHistorialArmazon',[$idFranquicia,$idContrato,$historial->id])}}"
                                  enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                @csrf
                                <div class="row">
                                    <div class="col-4">
                                        <label for="">Armazón</label>
                                        <select class="custom-select"
                                                name="producto">
                                            @if(count($armazones) > 0)
                                                <option selected value=''>Seleccionar</option>
                                                @foreach($armazones as $armazon)
                                                    @if($armazon->id_tipoproducto == 1 && $armazon->estado == 1 && $armazon->piezas > 0)
                                                        @if($armazon->id == $historial->id_producto)
                                                            <option selected value="{{$armazon->id}}">
                                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                                | {{$armazon->piezas}}pza.
                                                            </option>
                                                        @else
                                                            <option
                                                                value="{{$armazon->id}}" {{old('producto') == $armazon->id ? 'selected' : ''}}>
                                                                {{$armazon->nombre}} | {{$armazon->color}}
                                                                | {{$armazon->piezas}}pza.
                                                            </option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @else
                                                <option selected>Sin registros</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <button class="btn btn-outline-success btn-block" name="btnSubmit"
                                                type="submit">Actualizar armazón
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                        @if($historial->tipo == 1 && $historial->estadogarantia == 2)
                            @if($contrato[0]->estatus_estadocontrato == 1 || $contrato[0]->estatus_estadocontrato == 9)
                                <div style="color: #ea9999; font-weight: bold; margin-bottom: 10px;">
                                    Para cancelar la garantía, debes de comentarle a confirmaciones.
                                </div>
                            @endif
                            @if($contrato[0]->estatus_estadocontrato == 7 || $contrato[0]->estatus_estadocontrato == 10 || $contrato[0]->estatus_estadocontrato == 11)
                                <div style="color: #ea9999; font-weight: bold; margin-bottom: 10px;">
                                    Para cancelar la garantía, debes de comentarle a laboratorio.
                                </div>
                            @endif
                        @endif
                        @if(($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 5 || $contrato[0]->estatus_estadocontrato == 12 || $contrato[0]->estatus_estadocontrato == 4)
                            && $historial->tipo == 0)
                            <div class="row">
                                @if($contrato[0]->cuentaregresivafechaentrega >= 0
                                        || $contrato[0]->garantiacanceladaelmismodia
                                        || !$bandera
                                        || $contrato[0]->estatus_estadocontrato == 12)
                                    <div class="col-6">
                                        <form id="frmgarantia"
                                              action="{{route('agregarGarantiaHistorial',[$idFranquicia,$contrato[0]->id,$historial->id])}}"
                                              enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                            @csrf
                                            @if($historial->optometristaasignado == null)
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label for="">Optometrista</label>
                                                        <select
                                                            class="custom-select {!! $errors->first('optometristagarantia','is-invalid')!!}"
                                                            name="optometristagarantia">
                                                            @if(count($optometristas) > 0)
                                                                <option selected value='nada'>Seleccionar</option>
                                                                @foreach($optometristas as $optometrista)
                                                                    <option
                                                                        value="{{$optometrista->ID}}">
                                                                        {{$optometrista->NAME}}
                                                                    </option>
                                                                @endforeach
                                                            @else
                                                                <option selected>Sin registros</option>
                                                            @endif
                                                        </select>
                                                        {!! $errors->first('optometristagarantia','<div class="invalid-feedback">Elegir un optometrista , campo obligatorio
                                                        </div>')!!}
                                                    </div>
                                                    <div class="col-6">
                                                        <button class="btn btn-outline-success btn-block" name="btnSubmit"
                                                                type="submit">Nueva garantia
                                                        </button>
                                                    </div>
                                                </div>
                                                @if($contrato[0]->cuentaregresivafechaentrega >= 0)
                                                    <div style="color: #ea9999; font-weight: bold;">Quedan {{$contrato[0]->cuentaregresivafechaentrega}} días para bloquear o deshabilitar garantias a este
                                                        contrato
                                                    </div>
                                                @else
                                                    @if($contrato[0]->fechaentrega != null)
                                                        <div style="color: #ea9999; font-weight: bold;">Fecha limite para garantias ya expiró</div>
                                                    @endif
                                                @endif
                                            @endif
                                        </form>
                                    </div>
                                @else
                                    @if($contrato[0]->fechaentrega != null && $bandera)
                                        <div class="col col-6">
                                            @if($solicitudAutorizacion != null)
                                                @if($solicitudAutorizacion[0]->estatus == 0)
                                                    <div style="color: #0AA09E; font-weight: bold;"> Solicitud de garantía pendiente.</div>
                                                @endif
                                                @if($solicitudAutorizacion[0]->estatus == 1)
                                                    <form id="frmgarantia"
                                                          action="{{route('agregarGarantiaHistorial',[$idFranquicia,$contrato[0]->id,$historial->id])}}"
                                                          enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <label for="">Optometrista</label>
                                                                <select
                                                                    class="custom-select {!! $errors->first('optometristagarantia','is-invalid')!!}"
                                                                    name="optometristagarantia">
                                                                    @if(count($optometristas) > 0)
                                                                        <option selected value='nada'>Seleccionar</option>
                                                                        @foreach($optometristas as $optometrista)
                                                                            <option
                                                                                value="{{$optometrista->ID}}">
                                                                                {{$optometrista->NAME}}
                                                                            </option>
                                                                        @endforeach
                                                                    @else
                                                                        <option selected>Sin registros</option>
                                                                    @endif
                                                                </select>
                                                                {!! $errors->first('optometristagarantia','<div class="invalid-feedback">Elegir un optometrista , campo obligatorio
                                                                </div>')!!}
                                                            </div>
                                                            <div class="col-6">
                                                                <button class="btn btn-outline-success btn-block" name="btnSubmit"
                                                                        type="submit">Nueva garantia
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                @endif
                                                @if($solicitudAutorizacion[0]->estatus == 2)
                                                    <div style="margin-bottom: 5px;">
                                                        <a type="button" href="" class="btn btn-outline-success"
                                                           data-toggle="modal"
                                                           data-target="#modalsolicitarautorizacion">Solicitar garantía</a>
                                                    </div>
                                                    <div style="color: #ea9999; font-weight: bold;"> Ultima solicitud de garantía rechazada.</div>
                                                @endif
                                            @else
                                                <div style="margin-bottom: 5px;">
                                                    <a type="button" href="" class="btn btn-outline-success"
                                                       data-toggle="modal"
                                                       data-target="#modalsolicitarautorizacion">Solicitar garantía</a>
                                                </div>
                                            @endif
                                            <div style="color: #ea9999; font-weight: bold;">
                                                Fecha limite para garantias ya expiró
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-6"></div>
                                    @endif
                                @endif

                                @if((Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8) && $contrato[0]->estadogarantia != 2)
                                    <div class="col-6">
                                        <form id="frmpaquetes{{$historial->id}}"
                                              action="{{route('solicitarautorizacioncambiopaquete',[$idFranquicia,$idContrato,$historial->id])}}" enctype="multipart/form-data"
                                              method="POST" onsubmit="btnSubmit.disabled = true;">
                                            @csrf
                                            <div class="row">
                                                @if($solicitudCambioPaquete == null || $solicitudCambioPaquete[0]->estatus == 1)
                                                    <div class="col-5"></div>
                                                    <div class="col-7">
                                                        <div class="form-group">
                                                            <a type="button" class="btn btn-outline-success btn-block"
                                                               data-toggle="modal"
                                                               data-target="#modalsolicitarcambiopaquete{{$historial->id}}">Solicitar cambio paquete</a>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($solicitudCambioPaquete != null)
                                                    @if($solicitudCambioPaquete[0]->estatus == 0)
                                                        <div class="col-6">
                                                            <div class="row" style="color: #0AA09E; font-weight: bold; padding-top:10px; padding-left: 15px; margin-top: 30px;">
                                                                Solicitud de cambio de paquete pendiente.
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($solicitudCambioPaquete[0]->estatus == 2)
                                                        <div class="col-6">
                                                            <div class="row" style="color: #ea9999; font-weight: bold; margin-left: 5px; padding-top: 5px; margin-top: 30px;">
                                                                Ultima solicitud de cambio de paquete rechazada.
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <a type="button" class="btn btn-outline-success btn-block"
                                                                   data-toggle="modal"
                                                                   data-target="#modalsolicitarcambiopaquete{{$historial->id}}">Solicitar cambio paquete</a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif

                                                <!--Modal para Solicitar Autorizacion Cambiar paquete-->
                                                <div class="modal fade" id="modalsolicitarcambiopaquete{{$historial->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                                     aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                Solicitud de autorización para cambio de paquete.
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group row">
                                                                    <label class="col-sm-3 col-form-label">Paquetes:</label>
                                                                    <select
                                                                        class="custom-select col-sm-7 {!! $errors->first('paquetehistorialeditar','is-invalid')!!}"
                                                                        name="paquetehistorialeditar{{$historial->id}}">
                                                                        @if(count($paquetes) > 0)
                                                                            <option selected value=''>Seleccionar</option>
                                                                            @foreach($paquetes as $paquete)
                                                                                <option
                                                                                    value="{{$paquete->id}}">
                                                                                    {{$paquete->nombre}}
                                                                                </option>
                                                                            @endforeach
                                                                        @else
                                                                            <option selected>Sin registros</option>
                                                                        @endif
                                                                    </select>
                                                                    {!! $errors->first('paquetehistorialeditar','<div class="invalid-feedback">Elegir un paquete , campo obligatorio
                                                                    </div>')!!}
                                                                    <label class="col-sm-1 col-form-label"></label>
                                                                </div>
                                                                Explica detalladamente el por que requieres cambiar el paquete del contrato:
                                                                <textarea name="mensaje"
                                                                          id="mensaje"
                                                                          class="form-control {!! $errors->first('mensaje','is-invalid')!!}" rows="10"
                                                                          cols="60">
                                                            </textarea>
                                                                {!! $errors->first('mensaje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                                                                <div class="form-group row">
                                                                    <label class="col-sm-12 col-form-label" style="color: #ea9999;">El mensaje debe contener como minimo 15 caracteres y un maximo de
                                                                        1000.</label>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                                                                <button class="btn btn-success" name="btnSubmit" type="submit"
                                                                        form="frmpaquetes{{$historial->id}}">Aceptar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if(($contrato[0]->estatus_estadocontrato == 2 || $contrato[0]->estatus_estadocontrato == 5 || $contrato[0]->estatus_estadocontrato == 12
                            || $contrato[0]->estatus_estadocontrato == 4) && $historial->tipo == 0)
                            @if(!$bandera)
                                <div class="row">
                                    <div class="col-3">
                                        <a class="btn btn-outline-danger btn-block"  data-toggle="modal"
                                           data-target="#modalcancelargarantia">Cancelar garantia
                                        </a>
                                    </div>
                                </div>
                            @else
                                @if($historial->cancelargarantia != null)
                                    <div class="row">
                                        @if($historial->optometristaasignado != null)
                                            <div class="col-3">
                                                <label for="">Optometrista
                                                    asignado {{$historial->optometristaasignado}}</label>
                                            </div>
                                        @endif
                                        <div class="col-3">

                                            <a class="btn btn-outline-danger btn-block"  data-toggle="modal"
                                               data-target="#modalcancelargarantia">Cancelar garantia
                                            </a>
                                        </div>
                                    </div>
                                    <div style="color: #ea9999; font-weight: bold; margin-bottom: 5px;"> Recuerda que al reportar garantia solo se deben enviar las micas.</div>
                                    @if($contrato[0]->cuentaregresivafechaentrega >= 0)
                                        <div style="color: #ea9999; font-weight: bold;">Quedan {{$contrato[0]->cuentaregresivafechaentrega}} días para bloquear o deshabilitar garantias a este contrato</div>
                                    @endif
                                @endif
                            @endif
                        @endif
                        @if($historial->paquete == 'DORADO 2' || $historial->paquete == 'LECTURA')
                            <h5 style="color: #0AA09E;">Sin conversión</h5>
                            @if($historial->hscesfericoder != null)
                                <div id="mostrarvision"></div>
                                <h6>Ojo derecho</h6>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Esferico</label>
                                            <input type="text" name="esfericod" class="form-control" readonly
                                                   value="{{$historial->hscesfericoder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Cilindro</label>
                                            <input type="text" name="cilindrod" class="form-control" readonly
                                                   value="{{$historial->hsccilindroder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Eje</label>
                                            <input type="text" name="ejed" class="form-control" readonly
                                                   value="{{$historial->hscejeder}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Add</label>
                                            <input type="text" name="addd" class="form-control" readonly
                                                   value="{{$historial->hscaddder}}">
                                        </div>
                                    </div>
                                </div>
                                <h6>Ojo Izquierdo</h6>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Esferico</label>
                                            <input type="text" name="esfericod2" class="form-control" readonly
                                                   value="{{$historial->hscesfericoizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Cilindro</label>
                                            <input type="text" name="cilindrod2" class="form-control" readonly
                                                   value="{{$historial->hsccilindroizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Eje</label>
                                            <input type="text" name="ejed2" class="form-control" readonly
                                                   value="{{$historial->hscejeizq}}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Add</label>
                                            <input type="text" name="addd2" class="form-control" readonly
                                                   value="{{$historial->hscaddizq}}">
                                        </div>
                                    </div>
                                </div>
                            @else
                                <h6 style="color: #0AA09E; margin-left: 30px">Sin capturar</h6>
                            @endif
                            <h5 style="color: #0AA09E;">Con conversión</h5>
                        @endif
                        <div id="mostrarvision"></div>
                        <h6>Ojo derecho</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Esferico</label>
                                    <input type="text" name="esfericod" class="form-control" readonly
                                           value="{{$historial->esfericoder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Cilindro</label>
                                    <input type="text" name="cilindrod" class="form-control" readonly
                                           value="{{$historial->cilindroder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Eje</label>
                                    <input type="text" name="ejed" class="form-control" readonly value="{{$historial->ejeder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Add</label>
                                    <input type="text" name="addd" class="form-control" readonly value="{{$historial->addder}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>ALT</label>
                                    <input type="text" name="altd" class="form-control" readonly value="{{$historial->altder}}">
                                </div>
                            </div>
                        </div>
                        <h6>Ojo Izquierdo</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Esferico</label>
                                    <input type="text" name="esfericod2" class="form-control" readonly
                                           value="{{$historial->esfericoizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Cilindro</label>
                                    <input type="text" name="cilindrod2" class="form-control" readonly
                                           value="{{$historial->cilindroizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Eje</label>
                                    <input type="text" name="ejed2" class="form-control" readonly
                                           value="{{$historial->ejeizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Add</label>
                                    <input type="text" name="addd2" class="form-control" readonly
                                           value="{{$historial->addizq}}">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>ALT</label>
                                    <input type="text" name="altd2" class="form-control" readonly
                                           value="{{$historial->altizq}}">
                                </div>
                            </div>
                        </div>
                        <h6>Material</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                           id="material{{$historial->id}}" @if($historial->material == 0) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">Hi Index</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                           id="material{{$historial->id}}" @if($historial->material == 1) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">CR</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="material{{$historial->id}}" id="material{{$historial->id}}"
                                               @if($historial->material == 2) checked @endif onclick="return false;">
                                        <label class="form-check-label" for="material{{$historial->id}}">Policarbonato</label>
                                    </div>
                                    @if($historial->material == 2 && ($historial->policarbonatotipo == 0 || $historial->policarbonatotipo == 1))
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="@if($historial->policarbonatotipo == 0) ADULTO @else NIÑO @endif">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="material{{$historial->id}}"
                                           id="material{{$historial->id}}" @if($historial->material == 3) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="material{{$historial->id}}">Otro</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input type="text" name="motro" class="form-control" placeholder="Otro"
                                           value="{{$historial->materialotro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input type="text" name="costoMaterial" class="form-control"
                                           value="${{$historial->costomaterial}}" readonly>
                                </div>
                            </div>
                        </div>
                        <h6>Tipo de bifocal</h6>
                        <div class="row">
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                           id="exampleRadios{{$historial->id}}" @if($historial->bifocal == 0) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        FT
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                           id="exampleRadios{{$historial->id}}" @if($historial->bifocal == 1) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Blend
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                           id="exampleRadios{{$historial->id}}" @if($historial->bifocal == 2) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Progresivo
                                    </label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                           id="exampleRadios{{$historial->id}}" @if($historial->bifocal == 3) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        N/A
                                    </label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bifocal{{$historial->id}}"
                                           id="exampleRadios{{$historial->id}}" @if($historial->bifocal == 4) checked
                                           @endif onclick="return false;">
                                    <label class="form-check-label" for="exampleRadios{{$historial->id}}">
                                        Otro
                                    </label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="otroB" class="form-control" min="0" placeholder="Otro"
                                           value="{{$historial->bifocalotro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="costoBifocal" class="form-control" min="0"
                                           value="${{$historial->costobifocal}}" readonly>
                                </div>
                            </div>
                        </div>
                        <h6>Tratamiento</h6>
                        <div class="row">
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input " name="fotocromatico" id="customCheck9"
                                           @if($historial->fotocromatico == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck9">Fotocromatico</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input " name="ar" id="customCheck10"
                                           @if($historial->ar == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck10">A/R</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="tinte" id="customCheck11" @if($historial->tinte == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck11">Tinte</label>
                                    </div>
                                    @if($historial->tinte == 1 && ($historial->colortinte != null && $historial->estilotinte != null))
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colortinte}} | @if($historial->estilotinte == 0) DESVANECIDO @else COMPLETO @endif">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="polarizado" id="customCheck14" @if($historial->polarizado == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck14">Polarizado</label>
                                    </div>
                                    @if($historial->polarizado == 1 && $historial->colorpolarizado != null)
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colorpolarizado}}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="espejo" id="customCheck15" @if($historial->espejo == 1) checked @endif onclick="return false;">
                                        <label class="custom-control-label" for="customCheck15">Espejo</label>
                                    </div>
                                    @if($historial->espejo == 1 && $historial->colorespejo != null)
                                        <div style="margin-top: 10px;">
                                            <input type="text" class="form-control" readonly value="{{$historial->colorespejo}}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="blueray" id="customCheck12"
                                           @if($historial->blueray == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck12">BlueRay</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input " type="checkbox" name="otroTra" id="customCheck13"
                                           @if($historial->otroT == 1) checked @endif onclick="return false;">
                                    <label class="custom-control-label" for="customCheck13">Otro</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="otroT" class="form-control" min="0" placeholder="Otro"
                                           value="{{$historial->tratamientootro}}" readonly>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="text" name="costoTratamiento" class="form-control" min="0"
                                           value="${{$historial->costotratamiento}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Observaciones laboratorio</label>
                                    <textarea class="form-control" style="text-transform: uppercase" name="cilindrod2"
                                              rows="4" cols="60" readonly>{{$historial->observaciones}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Observaciones interno</label>
                                    <textarea class="form-control" style="text-transform: uppercase" name="cilindrod2"
                                              rows="4" cols="60" readonly>{{$historial->observacionesinterno}}</textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                    @endforeach
                @else
                    <div class="row">
                        <div class="col-3">
                            <h3 style="margin-top: 10px;">(Sin resultados)</h3>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div>
            <h4>Lista negra</h4>
            <form action="{{route('reportaractualizarsolicitudcontratolistanegra', [$idFranquicia,$contrato[0]->id])}}" enctype="multipart/form-data"
                  method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                @if($contratoListaNegra != null)
                    @switch($contratoListaNegra[0]->estado)
                        @case(0)
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Descripcion reporte de contrato a lista negra</label>
                                        <textarea class="form-control {!! $errors->first('descripcion','is-invalid')!!}" style="text-transform: uppercase" name="descripcion" id="descripcion" rows="4" cols="60" placeholder="DESCRIPCION">{{$contratoListaNegra[0]->descripcion}}</textarea>
                                        {!! $errors->first('descripcion','<div class="invalid-feedback">Campo descripcion obligatorio</div>')!!}
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display: flex; flex-direction: row-reverse;">
                                <div class="col-2">
                                    <a href="{{route('solicitudrechazaraprobarcontratolistanegra', [$idFranquicia,$contrato[0]->id, '2'])}}" class="btn btn-outline-danger btn-block">Rechazar solicitud</a>
                                </div>
                                <div class="col-2">
                                    <a href="{{route('solicitudrechazaraprobarcontratolistanegra', [$idFranquicia,$contrato[0]->id, '1'])}}" class="btn btn-outline-dark btn-block">Aprobar solicitud</a>
                                </div>
                                <div class="col-3">
                                    <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit" style="margin-top: 0px;">Actualizar</button>
                                </div>
                            </div>
                            @break
                        @case(1)
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Descripcion reporte de contrato a lista negra</label>
                                        <textarea class="form-control {!! $errors->first('descripcion','is-invalid')!!}" style="text-transform: uppercase" name="descripcion" id="descripcion" rows="4" cols="60" placeholder="DESCRIPCION" readonly>{{$contratoListaNegra[0]->descripcion}}</textarea>
                                        {!! $errors->first('descripcion','<div class="invalid-feedback">Campo descripcion obligatorio</div>')!!}
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display: flex; flex-direction: row-reverse;">
                                <div class="col-3">
                                    @if($solicitudAutorizacionListaNegra != null)
                                        @if($solicitudAutorizacionListaNegra[0]->estatus == 0)
                                            <div style="color: #0AA09E; font-weight: bold; margin-top: 0px;"> Solicitud de lista negra pendiente.</div>
                                        @endif
                                    @else
                                        <a href="{{route('solicitudautorizacioncontratolistanegra', [$idFranquicia,$contrato[0]->id])}}" class="btn btn-outline-success btn-block" style="margin-top: 0px; margin-bottom: 0px;">Solicitar Autorización</a>
                                    @endif
                                </div>
                            </div>
                            @break
                        @case(2)
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Descripcion reporte de contrato a lista negra</label>
                                        <textarea class="form-control {!! $errors->first('descripcion','is-invalid')!!}" style="text-transform: uppercase" name="descripcion" id="descripcion" rows="4" cols="60" placeholder="DESCRIPCION"></textarea>
                                        {!! $errors->first('descripcion','<div class="invalid-feedback">Campo descripcion obligatorio</div>')!!}
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display: flex; flex-direction: row-reverse;">
                                <div class="col-3">
                                    <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit" style="margin-top: 0px;">Reportar a lista negra</button>
                                </div>
                            </div>
                            @break
                    @endswitch
                @else
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Descripcion reporte de contrato a lista negra</label>
                                <textarea class="form-control {!! $errors->first('descripcion','is-invalid')!!}" style="text-transform: uppercase" name="descripcion" id="descripcion" rows="4" cols="60" placeholder="DESCRIPCION"></textarea>
                                {!! $errors->first('descripcion','<div class="invalid-feedback">Campo descripcion obligatorio</div>')!!}
                            </div>
                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-direction: row-reverse;">
                        <div class="col-3">
                            <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit" style="margin-top: 0px;">Reportar a lista negra</button>
                        </div>
                    </div>
                @endif
            </form>
        </div>
        <div class="row">
            <div class="col">
                <a href="{{route('listacontrato',$idFranquicia)}}"
                   class="btn btn-outline-success btn-block">@lang('mensajes.regresar')</a>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <img src="" class="imagepreview"
                         style="width: 100%; margin-top: 60px; margin-bottom: 60px; cursor: grabbing">
                </div>
            </div>
        </div>
    </div>

    <!--Modal para Solicitar Autorizacion Garantia-->
    <div class="modal fade" id="modalsolicitarautorizacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <form action="{{route('solicitarautorizaciongarantia',[$idFranquicia,$contrato[0]->id])}}" enctype="multipart/form-data"
              method="POST" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        Solicitud para autorización de garantia.
                    </div>
                    <div class="modal-body">Describa la solicitud de garantia.
                        <textarea name="mensaje"
                                  id="mensaje"
                                  class="form-control {!! $errors->first('mensaje','is-invalid')!!}" rows="10"
                                  cols="60">
                        </textarea>
                        {!! $errors->first('mensaje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                        <button class="btn btn-success" name="btnSubmit" type="submit">Aceptar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!--Modal para cancelacion de Garantia-->
    <div class="modal fade" id="modalcancelargarantia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <form action="{{route('cancelarGarantiaHistorial',[$idFranquicia,$contrato[0]->id,$historial->id])}}" enctype="multipart/form-data"
              method="POST" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        Cancelación de garantía.
                    </div>
                    <div class="modal-body">
                        Explica detalladamente el por que requieres cancelar la garantía del contrato:
                        <textarea name="mensaje"
                                  id="mensaje"
                                  class="form-control {!! $errors->first('mensaje','is-invalid')!!}" rows="5"
                                  cols="60" maxlength="1000">
                            </textarea>
                        {!! $errors->first('mensaje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        <div class="form-group row">
                            <label class="col-sm-12 col-form-label" style="color: #ea9999;">El mensaje debe contener como minimo 15 caracteres y un maximo de 1000.</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                        <button class="btn btn-success" name="btnSubmit" type="submit">Aceptar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!--Modal para confirmar actualizacion de historial clinico-->
    <div class="modal fade" id="confirmacionActualizarHistorial" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Actualizar historial clinico
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            ¿Estas seguro que quieres actualiza historial clinico de este contrato?
                            <hr>
                        </div>
                        <input type="hidden" name="idHistorialFormulario" id="idHistorialFormulario" />
                    </div>
                    <br>
                    <div class="row" style="padding-left: 20px;">
                        <div class="col-12" style="color: #dc3545">
                            <b>Al actualizar el historial, debes tener en cuenta las siguientes consideraciones:
                                <br>
                                <lu>
                                    <li>Has verificado los datos que deseas modificar.</li>
                                    <li>Debes actualizar el precio total del contrato mediante una "Solicitud de Cambio de Precio" para aquellas características del producto que incorporen costos adicionales y que no hayan sido registrados en el campo de "Otros".</li>
                                </lu>
                            </b>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-success" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-outline-danger" onclick="generarActualizacionHistorialClinico()" id="btnAceptarActualizarHistorial">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal para crear reposicion de contrato -->
    <div class="modal fade" id="modalreposicioncontrato" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <form action="{{route('contratoreponer',[$idFranquicia,$contrato[0]->id])}}" enctype="multipart/form-data"
              method="POST" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">Crear Reposición</div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Asignar optometrista</label>
                                    <select class="custom-select {!! $errors->first('optometristaReposicion','is-invalid')!!}" name="optometristaReposicion" id="optometristaReposicion" required>
                                        <option selected value="">Seleccionar</option>
                                        @foreach($optometristas as $optometrista)
                                            <option value="{{$optometrista->ID}}" @if($contrato[0]->id_optometrista == $optometrista->ID) selected @endif>{{$optometrista->NAME}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('optometristaReposicion','<div class="invalid-feedback">Campo optometrista obligatorio.</div>')!!}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Promoción</label>
                                    <select class="custom-select {!! $errors->first('promocionReposicion','is-invalid')!!}" name="promocionReposicion" id="promocionReposicion">
                                        <option selected value="">Seleccionar</option>
                                        @foreach($promocionesReposicion as $promocion)
                                            <option value="{{$promocion->id}}">{{$promocion->titulo}}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('promocionReposicion','<div class="invalid-feedback">Campo promoción obligatorio.</div>')!!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                        <button class="btn btn-success" name="btnSubmit" type="submit">Crear</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
