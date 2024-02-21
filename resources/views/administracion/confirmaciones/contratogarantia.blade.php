@extends('layouts.app')
@section('titulo','Contratos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2>@lang('mensajes.mensajeconfirmacionestado')</h2>

    <div class="row">
        <div class="col-3">
            <div class="form-group">
                <label>Estado</label>
                <input type="text" name="estado" class="form-control" readonly value="{{$infoFranquicia[0]->estado}}">
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Ciudad</label>
                <input type="text" name="ciudad" class="form-control" readonly value="{{$infoFranquicia[0]->ciudad}}">
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Colonia</label>
                <input type="text" name="colonia" class="form-control" readonly value="{{$infoFranquicia[0]->colonia}}">
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Numero Interior/Exterior</label>
                <input type="text" name="numero" class="form-control" readonly value="{{$infoFranquicia[0]->numero}}">
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-3">
            <label>Zona</label>
            <input type="text" class="form-control" readonly value="{{$contrato[0]->zonacontrato}}">
        </div>
        <div class="col-3">
            <label>Optometrista</label>
            <input type="text" class="form-control" readonly value="{{$contrato[0]->opto}}">
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Nombre del cliente:</label>
                <input type="text" name="nombre"
                       class="form-control" placeholder="Nombre" readonly
                       value="{{$contrato[0]->nombre}}">
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Calle</label>
                <input type="text" name="calle" class="form-control" readonly
                       placeholder="Calle" value="{{$contrato[0]->calle}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <div class="form-group">
                <label>Numero</label>
                <input type="text" name="numero"
                       class="form-control" placeholder="Numero" readonly
                       value="{{$contrato[0]->numero}}">
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Departamento</label>
                <input type="text" name="departamento"
                       class="form-control"
                       placeholder="Departamento" readonly value="{{$contrato[0]->depto}}">
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Al lado de</label>
                <input type="text" name="alladode"
                       class="form-control" placeholder="Al lado de" readonly
                       value="{{$contrato[0]->alladode}}">
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Frente a</label>
                <input type="text" name="frentea"
                       class="form-control" placeholder="Frente a" readonly
                       value="{{$contrato[0]->frentea}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <div class="form-group">
                <label>Entre calles</label>
                <input type="text" name="entrecalles"
                       class="form-control"
                       placeholder="Entre calles" readonly value="{{$contrato[0]->entrecalles}}">
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Colonia</label>
                <input type="text" name="colonia"
                       class="form-control" placeholder="Colonia" readonly
                       value="{{$contrato[0]->colonia}}">
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Localidad</label>
                <input type="text" name="localidad"
                       class="form-control" placeholder="Localidad" readonly
                       value="{{$contrato[0]->localidad}}">
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Telefono del paciente</label>
                <input type="text" name="telefono"
                       class="form-control" placeholder="Telefono" readonly
                       value="{{$contrato[0]->telefono}}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-2">
            <div class="form-group">
                <label>Tipo Casa</label>
                <input type="text" name="casatipo"
                       class="form-control" placeholder="Tipo Casa" readonly
                       value="{{$contrato[0]->casatipo}}">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Casa color</label>
                <input type="text" name="casacolor"
                       class="form-control"
                       placeholder="Casa color" readonly value="{{$contrato[0]->casacolor}}">
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Nombre referencia</label>
                <input type="text" name="nr" class="form-control"
                       placeholder="Nombre de referencia" readonly value="{{$contrato[0]->nombrereferencia}}">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Telefono referencia</label>
                <input type="text" name="tr" class="form-control"
                       placeholder="Telefono de referencia" readonly value="{{$contrato[0]->telefonoreferencia}}">
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Correo electronico</label>
                <input type="text" class="form-control" readonly value="{{$contrato[0]->correo}}">
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-3">
            <img src="{{asset($contrato[0]->fotoine)}}" style="width:250px;height:250px;" class="img-thumbnail">
        </div>
        <div class="col-3">
            <img src="{{asset($contrato[0]->fotoineatras)}}" style="width:250px;height:250px;" class="img-thumbnail">
        </div>
        <div class="col-3">
            <img src="{{asset($contrato[0]->fotocasa)}}" style="width:250px;height:250px;" class="img-thumbnail">
        </div>
        <div class="col-3">
            <img src="{{asset($contrato[0]->comprobantedomicilio)}}" style="width:250px;height:250px;"
                 class="img-thumbnail">
        </div>
    </div>
    <div class="row" style="margin-top: 10px;">
        <div class="col-3">
            <img src="{{asset($contrato[0]->pagare)}}" style="width:250px;height:250px;" class="img-thumbnail">
        </div>
        @if($contrato[0]->tarjeta != null && strlen($contrato[0]->tarjeta)>0)
            <div class="col-3">
                <img src="{{asset($contrato[0]->tarjeta)}}" style="width:250px;height:250px;" class="img-thumbnail">
            </div>
        @endif
        @if( $contrato[0]->tarjeta != null && strlen($contrato[0]->tarjetapensionatras)>0)
            <div class="col-3">
                <img src="{{asset($contrato[0]->tarjetapensionatras)}}" style="width:250px;height:250px;"
                     class="img-thumbnail">
            </div>
        @endif
    </div>
    <hr>
    @php($contador = 1)
    @if(isset($historiales))
        @foreach($historiales as $historial)
            <div class="row">
                <div class="col-2">
                    <h3 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}</h3>
                </div>
                <div class="col-2">
                    <h3 style="margin-top: 10px;">Modelo: {{$historial->armazon}}</h3>
                </div>
                @if($loop->iteration == 1)
                    <div class="col-2">
                        <h3 style="margin-top: 10px;">Paquete: {{$historial->paquete}}</h3>
                    </div>
                @endif
            </div>
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
                        <input type="text" name="ejed2" class="form-control" readonly value="{{$historial->ejeizq}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Add</label>
                        <input type="text" name="addd2" class="form-control" readonly value="{{$historial->addizq}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>ALT</label>
                        <input type="text" name="altd2" class="form-control" readonly value="{{$historial->altizq}}">
                    </div>
                </div>
            </div>
            <h6>Material</h6>
            <div class="row">
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="material{{$loop->iteration}}"
                               id="material{{$loop->iteration}}" @if($historial->material == 0) checked
                               @endif onclick="return false;">
                        <label class="form-check-label" for="material{{$loop->iteration}}">Hi Index</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="material{{$loop->iteration}}"
                               id="material{{$loop->iteration}}" @if($historial->material == 1) checked
                               @endif onclick="return false;">
                        <label class="form-check-label" for="material{{$loop->iteration}}">CR</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="material{{$loop->iteration}}"
                               id="material{{$loop->iteration}}" @if($historial->material == 2) checked
                               @endif onclick="return false;">
                        <label class="form-check-label" for="material{{$loop->iteration}}">Policarbonato</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="material{{$loop->iteration}}"
                               id="material{{$loop->iteration}}" @if($historial->material == 3) checked
                               @endif onclick="return false;">
                        <label class="form-check-label" for="material{{$loop->iteration}}">Otro</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-check">
                        <input type="text" name="motro" class="form-control" placeholder="Otro"
                               value="{{$historial->materialotro}}" readonly>
                    </div>
                </div>
            </div>
            <h6>Tipo de bifocal</h6>
            <div class="row">
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bifocal{{$loop->iteration}}"
                               id="exampleRadios{{$loop->iteration}}" @if($historial->bifocal == 0) checked
                               @endif onclick="return false;">
                        <label class="form-check-label" for="exampleRadios{{$loop->iteration}}">
                            FT
                        </label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bifocal{{$loop->iteration}}"
                               id="exampleRadios{{$loop->iteration}}" @if($historial->bifocal == 1) checked
                               @endif onclick="return false;">
                        <label class="form-check-label" for="exampleRadios{{$loop->iteration}}">
                            Blend
                        </label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bifocal{{$loop->iteration}}"
                               id="exampleRadios{{$loop->iteration}}" @if($historial->bifocal == 2) checked
                               @endif onclick="return false;">
                        <label class="form-check-label" for="exampleRadios{{$loop->iteration}}">
                            Progresivo
                        </label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bifocal{{$loop->iteration}}"
                               id="exampleRadios{{$loop->iteration}}" @if($historial->bifocal == 3) checked
                               @endif onclick="return false;">
                        <label class="form-check-label" for="exampleRadios{{$loop->iteration}}">
                            N/A
                        </label>
                    </div>
                </div>
            </div>
            <h6>Tratamiento</h6>
            <div class="row">
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input " name="fotocromatico" id="customCheck9"
                               @if($historial->fotocromatico == 1) checked @endif onclick="return false;">
                        <label class="custom-control-label" for="customCheck9">Fotocromatico</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input " name="ar" id="customCheck10"
                               @if($historial->ar == 1) checked @endif onclick="return false;">
                        <label class="custom-control-label" for="customCheck10">A/R</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="tinte" id="customCheck11"
                               @if($historial->tinte == 1) checked @endif onclick="return false;">
                        <label class="custom-control-label" for="customCheck11">Tinte</label>
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
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label>Observaciones laboratorio</label>
                        <input type="text" name="cilindrod2" class="form-control" readonly
                               value="{{$historial->observaciones}}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label>Observaciones interno</label>
                        <input type="text" name="cilindrod2" class="form-control" readonly
                               value="{{$historial->observacionesinterno}}">
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

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection

