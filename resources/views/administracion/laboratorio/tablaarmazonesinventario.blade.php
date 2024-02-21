@extends('layouts.app')
@section('titulo','Inventario laboratorio'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>Armazones</h2>
        <form  action="{{route('crearsolicitudarmazonbaja')}}" enctype="multipart/form-data" method="POST"
               id="formBajaArmazon">
            @csrf
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label>ARMAZÓN</label>
                        <select name="armazon" id="armazon" class="form-control {!! $errors->first('armazon','is-invalid')!!}" required>
                            <option selected value="">Seleccionar armazón</option>
                            @foreach($armazones as $armazon)
                                <option value="{{$armazon->id}}">{{$armazon->nombre}} | {{$armazon->color}} | {{$armazon->totalpiezas}}pzas.</option>
                            @endforeach
                        </select>
                        {!! $errors->first('armazon','<div class="invalid-feedback">Selecciona un armazón valido.</div>')!!}
                    </div>
                </div>
                <div class="col-9">
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label>FOTO FRENTE (JPG)</label>
                                <div class="custom-file">
                                    <input type="file" name="fotofrente" id="fotofrente" class="custom-file-input {!! $errors->first('fotofrente','is-invalid')!!}"
                                           accept="image/jpg">
                                    <label class="custom-file-label" for="fotofrente">Choose file...</label>
                                    {!! $errors->first('fotofrente','<div class="invalid-feedback">Foto frente es obligatoria y en formato JPG.</div>')!!}
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label>FOTO ATRAS (JPG)</label>
                                <div class="custom-file">
                                    <input type="file" name="fotoatras" id="fotoatras" class="custom-file-input {!! $errors->first('fotoatras','is-invalid')!!}"
                                           accept="image/jpg">
                                    <label class="custom-file-label" for="fotoatras">Choose file...</label>
                                    {!! $errors->first('fotoatras','<div class="invalid-feedback">Foto atras es obligatoria y en formato JPG.</div>')!!}
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label>FOTO LADO 1 (JPG)</label>
                                <div class="custom-file">
                                    <input type="file" name="fotolado1" id="fotolado1" class="custom-file-input {!! $errors->first('fotolado1','is-invalid')!!}"
                                           accept="image/jpg">
                                    <label class="custom-file-label" for="fotolado1">Choose file...</label>
                                    {!! $errors->first('fotolado1','<div class="invalid-feedback">Foto lado 1 es obligatoria y en formato JPG.</div>')!!}
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label>FOTO LADO 2 (JPG)</label>
                                <div class="custom-file">
                                    <input type="file" name="fotolado2" id="fotolado2" class="custom-file-input {!! $errors->first('fotolado2','is-invalid')!!}"
                                           accept="image/jpg">
                                    <label class="custom-file-label" for="fotolado2">Choose file...</label>
                                    {!! $errors->first('fotolado2','<div class="invalid-feedback">Foto lado 2 es obligatoria y en formato JPG.</div>')!!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea rows="4" name="descripcion" id="descripcion" class="form-control {!! $errors->first('descripcion','is-invalid')!!}"
                                  placeholder="DESCRIPCIÓN"> {{old('descripcion')}} </textarea>
                        {!! $errors->first('descripcion','<div class="invalid-feedback">Campo descripción obligatorio.</div>')!!}
                    </div>
                </div>
            </div>
            <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit" form="formBajaArmazon">ACEPTAR</button>
        </form>

        <table id="tbllistasolicitudes" class="table-bordered table-striped table-general table-sm" style="margin-top: 20px;">
            <thead>
            <tr>
                <th  style =" text-align:center;" scope="col">ARMAZON</th>
                <th  style =" text-align:center;" scope="col">FOTO FRENTE</th>
                <th  style =" text-align:center;" scope="col">FOTO ATRAS</th>
                <th  style =" text-align:center;" scope="col">FOTO LADO 1</th>
                <th  style =" text-align:center;" scope="col">FOTO LADO 2</th>
                <th  style =" text-align:center;" scope="col">ESTADO SOLICITUD</th>
            </tr>
            </thead>
            <tbody>
            @if($soliciudes != null)
                @foreach($soliciudes as $solicitud)
                    <tr style="@if($solicitud->estado == 0) background: #D6EAF8 @endif
                               @if($solicitud->estado == 1) background: #60f1b2; @endif @if($solicitud->estado == 2) background-color: rgba(255,15,0,0.17); @endif">
                        <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$solicitud->armazon}}</td>
                        <td style="font-size: 11px; text-align: center; vertical-align: middle">
                            <img src="{{asset($solicitud->fotofrente)}}" style="width:50px;height:50px;" class="img-thumbnail">
                        </td>
                        <td style="font-size: 11px; text-align: center; vertical-align: middle">
                            <img src="{{asset($solicitud->fotoatras)}}" style="width:50px;height:50px;" class="img-thumbnail">
                        </td>
                        <td style="font-size: 11px; text-align: center; vertical-align: middle">
                            <img src="{{asset($solicitud->fotolado1)}}" style="width:50px;height:50px;" class="img-thumbnail">
                        </td>
                        <td style="font-size: 11px; text-align: center; vertical-align: middle">
                            <img src="{{asset($solicitud->fotolado2)}}" style="width:50px;height:50px;" class="img-thumbnail">
                        </td>
                        <td style="font-size: 11px; text-align: center; vertical-align: middle">
                            @switch($solicitud->estado)
                                @case(0)
                                    <button type="button" class="btn btn-info btn-sm" style="color:#FEFEFE; cursor: default;">PENDIENTE</button>
                                @break
                                @case(1)
                                    <button type="button" class="btn btn-success btn-sm" style="color:#FEFEFE; cursor: default;">AUTORIZADO</button>
                                @break
                                @case(2)
                                    <button type="button" class="btn btn-danger btn-sm" style="color:#FEFEFE; cursor: default;">RECHAZADO</button>
                                @break
                            @endswitch
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td style="font-size: 11px; text-align: center; vertical-align: middle" colspan="6">SIN REGISTROS</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>


    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
