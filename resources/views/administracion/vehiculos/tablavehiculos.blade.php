@extends('layouts.app')
@section('titulo','Vehiculos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <input type="hidden" id="idFranquicia" value="{{$idFranquicia}}">
    <h2>Agregar nuevo vehículo</h2>
    <form id="frmCrearVehiculo" action="{{route('nuevovehiculo',$idFranquicia)}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
        @csrf
        <div class="col" style="padding-top: 20px;">
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label>Numero de serie</label>
                        <input type="text" name="numSerie" id="numSerie" class="form-control {!! $errors->first('numSerie','is-invalid')!!}"  placeholder="EJ012345678901234 (16 digitos)"
                               value="{{old('numSerie')}}">
                        {!! $errors->first('numSerie','<div class="invalid-feedback">El campo número de serie es obligatorio y debe tener un mínimo de 16 dígitos.</div>')!!}
                    </div>
                </div>
                <div class="col-3">
                    <div class="row" style="justify-content: space-around">
                        <div class="form-group" style="width: 45%;">
                            <label>Kilometraje</label>
                            <input type="number" name="kilometraje" id="kilometraje" class="form-control {!! $errors->first('kilometraje','is-invalid')!!}"  placeholder="100"  min="0"
                                   value="{{old('kilometraje')}}">
                            {!! $errors->first('kilometraje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                        <div class="form-group" style="width: 45%;">
                            <label>Siguiente Kilometraje</label>
                            <input type="number" name="sigKilometraje" id="sigKilometraje" class="form-control {!! $errors->first('sigKilometraje','is-invalid')!!}"  placeholder="100" min="0"
                                   value="{{old('sigKilometraje')}}">
                            {!! $errors->first('sigKilometraje','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Ultimo Servicio</label>
                        <input type="date" name="ultimoServicio" id="ultimoServicio"
                               class="form-control {!! $errors->first('ultimoServicio','is-invalid')!!}" value="{{old('ultimoServicio')}}" max="<?= date('Y-m-d'); ?>">
                        @if($errors->has('ultimoServicio'))
                            <div class="invalid-feedback">{{$errors->first('ultimoServicio')}}</div>
                        @endif
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Siguiente Servicio</label>
                        <input type="date" name="sigServicio" id="sigServicio"
                               class="form-control {!! $errors->first('sigServicio','is-invalid')!!}" value="{{old('sigServicio')}}" min="<?= date('Y-m-d'); ?>">
                        @if($errors->has('sigServicio'))
                            <div class="invalid-feedback">{{$errors->first('sigServicio')}}</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label>Tipo vehículo</label>
                        <select name="idTipoVehiculo" id="idTipoVehiculo" class=" form-control {!! $errors->first('idTipoVehiculo','is-invalid')!!}"
                                value="{{old('idTipoVehiculo')}}" required>
                            <option value="">Seleccionar</option>
                            @if(sizeof($tipoVehiculos) > 0)
                                @foreach($tipoVehiculos as $tipoVehiculo)
                                    <option value="{{$tipoVehiculo->id}}">{{$tipoVehiculo->tipo}}</option>
                                @endforeach
                            @else
                                <option value="">Sin registros</option>
                            @endif
                        </select>
                        {!! $errors->first('idTipoVehiculo','<div class="invalid-feedback">Selecciona un tipo de vehiculo.</div>')!!}
                        <div class="invalid-feedback" id="errorRol"></div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Marca</label>
                        <input type="text" name="marcaVehiculo" id="marcaVehiculo" class="form-control {!! $errors->first('marcaVehiculo','is-invalid')!!}"
                               placeholder="Honda | Yamaha | Italika | Suzuki" value="{{old('marcaVehiculo')}}">
                        {!! $errors->first('marcaVehiculo','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Numero de cilindros</label>
                        <input type="number" name="numCilindros" id="numCilindros" class="form-control {!! $errors->first('numCilindros','is-invalid')!!}"
                               placeholder="1" min="0" value="{{old('numCilindros')}}">
                        {!! $errors->first('numCilindros','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Linea</label>
                        <input type="text" name="lineaVehiculo" id="lineaVehiculo" class="form-control {!! $errors->first('lineaVehiculo','is-invalid')!!}"
                               placeholder="DIO" value="{{old('lineaVehiculo')}}">
                        {!! $errors->first('lineaVehiculo','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Modelo</label>
                        <input type="text" name="modeloVehiculo" id="modeloVehiculo" class="form-control {!! $errors->first('modeloVehiculo','is-invalid')!!}"
                               placeholder="<?php echo date('Y'); ?>" value="{{old('modeloVehiculo')}}">
                        {!! $errors->first('modeloVehiculo','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label>Clase</label>
                        <input type="text" name="claseVehiculo" id="claseVehiculo" class="form-control {!! $errors->first('claseVehiculo','is-invalid')!!}"
                               placeholder="Turismo | Trabajo | Crucero" value="{{old('claseVehiculo')}}">
                        {!! $errors->first('claseVehiculo','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Estado vehículo</label>
                        <input type="text" name="tipoVehiculo" id="tipoVehiculo" class="form-control {!! $errors->first('tipoVehiculo','is-invalid')!!}"
                               placeholder="Moto nueva" value="{{old('tipoVehiculo')}}">
                        {!! $errors->first('tipoVehiculo','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Capacidad</label>
                        <input type="text" name="capacidad" id="capacidad" class="form-control {!! $errors->first('capacidad','is-invalid')!!}"
                               placeholder="110CC/125CC" value="{{old('capacidad')}}">
                        {!! $errors->first('capacidad','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Numero de motor</label>
                        <input type="text" name="numMotor" id="numMotor" class="form-control {!! $errors->first('numMotor','is-invalid')!!}"
                               placeholder="EJ1234567890 (11-17 Digitos)" value="{{old('numMotor')}}">
                        {!! $errors->first('numMotor','<div class="invalid-feedback">El campo número de motor es obligatorio y debe tener entre 11 y 17 dígitos.</div>')!!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label>Placas</label>
                        <input type="text" name="placas" id="placas" class="form-control {!! $errors->first('placas','is-invalid')!!}"
                               placeholder="000-00-00" value="{{old('placas')}}">
                        {!! $errors->first('placas','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Numero de póliza</label>
                        <input type="text" name="numeroPoliza" id="numeroPoliza" class="form-control {!! $errors->first('numeroPoliza','is-invalid')!!}"
                               placeholder="NUMERO POLIZA" value="{{old('numeroPoliza')}}">
                        {!! $errors->first('numeroPoliza','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Vigencia póliza</label>
                        <input type="date" name="vigenciaPoliza" id="vigenciaPoliza"
                               class="form-control {!! $errors->first('vigenciaPoliza','is-invalid')!!}" value="{{old('vigenciaPoliza')}}" min="<?= date('Y-m-d'); ?>">
                        @if($errors->has('vigenciaPoliza'))
                            <div class="invalid-feedback">{{$errors->first('vigenciaPoliza')}}</div>
                        @endif
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Factura (PDF)</label>
                        <input type="file" name="factura" id="factura" class="form-control-file {!! $errors->first('factura','is-invalid')!!}" accept="application/pdf">
                        {!! $errors->first('factura','<div class="invalid-feedback">La factura debera estar en formato pdf.</div>')!!}
                    </div>
                </div>
            </div>
            <div class="form-group" style="text-align: center">
                <label>Descripción</label>
                <input type="text" name="descripcion" id="descripcion" class="form-control {!! $errors->first('descripcion','is-invalid')!!}"
                       placeholder="Descripción" style="text-align: center" value="{{old('descripcion')}}">
                {!! $errors->first('descripcion','<div class="invalid-feedback">Campo obligatorio.</div>')!!}
            </div>
            <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit">Agregar vehículo</button>
        </div>
    </form>
    <div class="row">
        <div class="col-12" id="spCargando">
            <div class="d-flex justify-content-center">
                <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 15px;" role="status">
                    <span class="visually-hidden"></span>
                </div>
            </div>
        </div>
    </div>
    <div id="tbllistavehiculos" style="padding-top: 20px;">

    </div>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
