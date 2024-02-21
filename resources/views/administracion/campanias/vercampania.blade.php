@extends('layouts.app')
@section('titulo','Ver campaña'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <input type="hidden" name="idCampania" id="idCampania" value="{{$campania[0]->id}}">
        <input type="hidden" name="tipoReferencia" id="tipoReferencia" value="{{$campania[0]->tiporeferencia}}">
        <input type="hidden" name="idFranquicia" id="idFranquicia" value="{{$idFranquicia}}">
        <div class="row">
            <div class="col-12" style="display: flex; justify-content: center; align-items: center;">
                <div class="form-group">
                    <center><h2>{{$campania[0]->titulo}}</h2></center>
                    <div style="margin-bottom: 10px;">
                        @if(isset($campania[0]->foto) && !empty($campania[0]->foto) && file_exists($campania[0]->foto))
                            <img src="{{asset($campania[0]->foto)}}" style="width:650px;height:364px;" class="img-thumbnail">
                        @else
                            <img src="/imagenes/general/administracion/sinfoto.png" style="width:250px;height:300px;" class="img-thumbnail">
                        @endif
                    </div>
                </div>
            </div>
        </div>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control {!! $errors->first('nombre','is-invalid')!!}" placeholder="Nombre paciente"
                               value="{{old('nombre')}}">
                        {!! $errors->first('nombre','<div class="invalid-feedback">Agerga el nombre del paciente.</div>')!!}
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Telefono</label>
                        <input type="text" name="telefono" id="telefono" class="form-control {!! $errors->first('telefono','is-invalid')!!}" placeholder="Tel: 33333333333 | 333-333-33-33"
                               value="{{old('telefono')}}">
                        {!! $errors->first('telefono','<div class="invalid-feedback">Agerga el telefono del paciente.</div>')!!}
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group" id="divNumeroReferencia">
                        <label>Numero de referencia</label>
                        <input type="text" name="referencia" id="referencia" class="form-control {!! $errors->first('referencia','is-invalid')!!}" placeholder="Numero de referencia"
                               value="{{old('referencia')}}">
                        {!! $errors->first('referencia','<div class="invalid-feedback">Numero de referencia obligatorio con un tamaño maximo de 4 digitos.</div>')!!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea rows="4" name="observaciones" id="observaciones" class="form-control {!! $errors->first('observaciones','is-invalid')!!}"
                                  placeholder="Observaciones"> {{old('fechaFin')}} </textarea>
                        {!! $errors->first('observaciones','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                    </div>
                </div>
            </div>
            <button class="btn btn-outline-success btn-block" name="btnAgendarCampania" id="btnAgendarCampania" onclick="agendarcampania()" type="button">AGENDAR</button>
        <hr>
        <div class="row" style="margin-top: 20px;">
            <div class="contenedor" style="display: flex; justify-content: center; align-items: center;" id="divUltimaReferencia" name="divUltimaReferencia">
                <div class="col-3">
                    <p style="color: black; text-align: center; font-size: 30px;"><b>Ultima referencia</b></p>
                    <p style="color: darkred; text-align: center; font-size: 28px;" id="ultimaReferencia"><b>SIN REGISTRO</b></p>
                </div>
            </div>
        </div>
        <hr>
        <div id="listaCampaniasAgendadas">

        </div>

    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
