@extends('layouts.app')
@section('titulo','Supervision vehicular'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>Actualizar supervisión vehicular</h2>
            <form action="{{route('editarsupervisionvehiculo',[$idFranquicia,$idVehiculo, $idSupervision])}}" class="was-validated" enctype="multipart/form-data" method="POST"
                  id="formNuevaSupervision">
                @csrf
                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <label>Kilometraje mañana (JPG)</label>
                            @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17)
                                <br>
                                @if($horarioActualizacionFotos != null)
                                    <label style="font-size: 14px; font-weight: bold;">Horario limite para cargar imagen o actualizar: {{Carbon\Carbon::parse($horarioActualizacionFotos[0]->horalimitechoferfoto1)->format('H:i')}} hrs</label>
                                @else
                                    <label style="font-size: 14px; font-weight: bold;">Horario limite para cargar imagen o actualizar: 09:00 hrs</label>
                                @endif
                            @endif
                            <div style="margin-bottom: 10px;">
                                @if(isset($supervisionActualizar[0]->kilometraje1) && !empty($supervisionActualizar[0]->kilometraje1) && file_exists($supervisionActualizar[0]->kilometraje1))
                                    <img src="{{asset($supervisionActualizar[0]->kilometraje1)}}" style="width:250px;height:300px;" class="img-thumbnail">
                                @else
                                    <img src="/imagenes/general/administracion/sinfoto.png" style="width:250px;height:300px;" class="img-thumbnail">
                                @endif
                            </div>
                            @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17)
                                @if(($horarioActualizacionFotos != null && (Carbon\Carbon::parse(Carbon\Carbon::now())->format('H:i') < Carbon\Carbon::parse($horarioActualizacionFotos[0]->horalimitechoferfoto1)->format('H:i')))
                                    || ($horarioActualizacionFotos == null && (Carbon\Carbon::parse(Carbon\Carbon::now())->format('H:i') < Carbon\Carbon::parse('09:00')->format('H:i'))))
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input {!! $errors->first('kilometraje1','is-invalid')!!}" name="kilometraje1" id="kilometraje1"
                                               accept="image/jpg" capture="camera">
                                        <label class="custom-file-label" for="kilometraje1">Choose file...</label>
                                        {!! $errors->first('kilometraje1','<div class="invalid-feedback">Foto kilometraje mañana obligatoria en formato JPG de tamaño maximo 1MB.</div>')!!}
                                    </div>
                                @else
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input {!! $errors->first('kilometraje1','is-invalid')!!}" name="kilometraje1" id="kilometraje1"
                                               accept="image/jpg" capture="camera" disabled>
                                        <label class="custom-file-label" for="kilometraje1">Choose file...</label>
                                        {!! $errors->first('kilometraje1','<div class="invalid-feedback">Foto kilometraje mañana obligatoria en formato JPG de tamaño maximo 1MB.</div>')!!}
                                    </div>
                                    <label style="font-size: 14px;color: rgba(255,15,0,0.2); font-weight: bold;">Horario expirado, solicita a administración actualizar la foto.</label>
                                @endif
                            @else
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input {!! $errors->first('kilometraje1','is-invalid')!!}" name="kilometraje1" id="kilometraje1"
                                           accept="image/jpg" capture="camera">
                                    <label class="custom-file-label" for="kilometraje1">Choose file...</label>
                                    {!! $errors->first('kilometraje1','<div class="invalid-feedback">Foto kilometraje mañana obligatoria en formato JPG de tamaño maximo 1MB.</div>')!!}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Kilometraje tarde (JPG)</label>
                            @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17)
                                @if($horarioActualizacionFotos != null)
                                    <label style="font-size: 14px; font-weight: bold;">Horario limite para cargar imagen o actualizar: {{Carbon\Carbon::parse($horarioActualizacionFotos[0]->horalimitechoferfoto2)->format('H:i')}} hrs</label>
                                @else
                                    <label style="font-size: 14px; font-weight: bold;">Horario limite para cargar imagen o actualizar: 23:59 hrs</label>
                                @endif
                            @endif
                            <div style="margin-bottom: 10px;">
                                @if(isset($supervisionActualizar[0]->kilometraje2) && !empty($supervisionActualizar[0]->kilometraje2) && file_exists($supervisionActualizar[0]->kilometraje2))
                                    <img src="{{asset($supervisionActualizar[0]->kilometraje2)}}" style="width:250px;height:300px;" class="img-thumbnail">
                                @else
                                    <img src="/imagenes/general/administracion/sinfoto.png" style="width:250px;height:300px;" class="img-thumbnail">
                                @endif
                            </div>
                            @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17)
                                @if(($horarioActualizacionFotos != null && (Carbon\Carbon::parse($horarioActualizacionFotos[0]->horalimitechoferfoto2)->format('H:i') > Carbon\Carbon::parse(Carbon\Carbon::now())->format('H:i')
                                  && Carbon\Carbon::parse($horarioActualizacionFotos[0]->horalimitechoferfoto1)->format('H:i') < Carbon\Carbon::parse(Carbon\Carbon::now())->format('H:i')))
                                  || ($horarioActualizacionFotos == null && (Carbon\Carbon::parse(Carbon\Carbon::now())->format('H:i') > Carbon\Carbon::parse('09:00')->format('H:i')
                                  && Carbon\Carbon::parse(Carbon\Carbon::now())->format('H:i') < Carbon\Carbon::parse('23:59')->format('H:i'))))
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input {!! $errors->first('kilometraje2','is-invalid')!!}" name="kilometraje2" id="kilometraje2"
                                               accept="image/jpg" capture="camera">
                                        <label class="custom-file-label" for="kilometraje2">Choose file...</label>
                                        {!! $errors->first('kilometraje2','<div class="invalid-feedback">Foto kilometraje tarde obligatoria en formato JPG de tamaño maximo 1MB.</div>')!!}
                                    </div>
                                @else
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input {!! $errors->first('kilometraje2','is-invalid')!!}" name="kilometraje2" id="kilometraje2"
                                               accept="image/jpg" capture="camera" disabled>
                                        <label class="custom-file-label" for="kilometraje2">Choose file...</label>
                                        {!! $errors->first('kilometraje2','<div class="invalid-feedback">Foto kilometraje tarde obligatoria en formato JPG de tamaño maximo 1MB.</div>')!!}
                                    </div>
                                    <label style="font-size: 14px;color: rgba(255,15,0,0.2); font-weight: bold;">Es necesario contar con foto 'Kilometraje mañana' y la hora posterior a: @if($horarioActualizacionFotos[0]->horalimitechoferfoto1 != null) {{Carbon\Carbon::parse($horarioActualizacionFotos[0]->horalimitechoferfoto1)->format('H:i')}} @else 09:00 @endif  hrs.</label>
                                @endif
                            @else
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input {!! $errors->first('kilometraje2','is-invalid')!!}" name="kilometraje2" id="kilometraje2"
                                           accept="image/jpg" capture="camera">
                                    <label class="custom-file-label" for="foto1">Choose file...</label>
                                    {!! $errors->first('kilometraje2','<div class="invalid-feedback">Foto kilometraje tarde obligatoria en formato JPG de tamaño maximo 1MB.</div>')!!}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) style="margin-bottom: 40px;" @endif>Lado izquierdo (JPG)</label>
                            <div style="margin-bottom: 10px;">
                                @if(isset($supervisionActualizar[0]->ladoizquierdo) && !empty($supervisionActualizar[0]->ladoizquierdo) && file_exists($supervisionActualizar[0]->ladoizquierdo))
                                    <img src="{{asset($supervisionActualizar[0]->ladoizquierdo)}}" style="width:250px;height:300px;" class="img-thumbnail">
                                @else
                                    <img src="/imagenes/general/administracion/sinfoto.png" style="width:250px;height:300px;" class="img-thumbnail">
                                @endif
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input {!! $errors->first('ladoizquierdo','is-invalid')!!}" name="ladoizquierdo" id="ladoizquierdo"
                                       accept="image/jpg" capture="camera">
                                <label class="custom-file-label" for="ladoizquierdo">Choose file...</label>
                                {!! $errors->first('ladoizquierdo','<div class="invalid-feedback">Foto lado izquierdo obligatoria en formato JPG de tamaño maximo 1MB..</div>')!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17) style="margin-bottom: 40px;" @endif>Lado derecho (JPG)</label>
                            <div style="margin-bottom: 10px;">
                                @if(isset($supervisionActualizar[0]->ladoderecho) && !empty($supervisionActualizar[0]->ladoderecho) && file_exists($supervisionActualizar[0]->ladoderecho))
                                    <img src="{{asset($supervisionActualizar[0]->ladoderecho)}}" style="width:250px;height:300px;" class="img-thumbnail">
                                @else
                                    <img src="/imagenes/general/administracion/sinfoto.png" style="width:250px;height:300px;" class="img-thumbnail">
                                @endif
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input {!! $errors->first('ladoderecho','is-invalid')!!}" name="ladoderecho" id="ladoderecho"
                                       accept="image/jpg" capture="camera">
                                <label class="custom-file-label" for="ladoderecho">Choose file...</label>
                                {!! $errors->first('ladoderecho','<div class="invalid-feedback">Foto lado derecho obligatoria en formato JPG de tamaño maximo 1MB.</div>')!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Frente (JPG)</label>
                            <div style="margin-bottom: 10px;">
                                @if(isset($supervisionActualizar[0]->frente) && !empty($supervisionActualizar[0]->frente) && file_exists($supervisionActualizar[0]->frente))
                                    <img src="{{asset($supervisionActualizar[0]->frente)}}" style="width:250px;height:300px;" class="img-thumbnail">
                                @else
                                    <img src="/imagenes/general/administracion/sinfoto.png" style="width:250px;height:300px;" class="img-thumbnail">
                                @endif
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input {!! $errors->first('frente','is-invalid')!!}" name="frente" id="frente" accept="image/jpg" capture="camera">
                                <label class="custom-file-label" for="frente">Choose file...</label>
                                {!! $errors->first('frente','<div class="invalid-feedback">Foto frente requerida en formato JPG de tamaño maximo 1MB.</div>')!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Atras (JPG)</label>
                            <div style="margin-bottom: 10px;">
                                @if(isset($supervisionActualizar[0]->atras) && !empty($supervisionActualizar[0]->atras) && file_exists($supervisionActualizar[0]->atras))
                                    <img src="{{asset($supervisionActualizar[0]->atras)}}" style="width:250px;height:300px;" class="img-thumbnail">
                                @else
                                    <img src="/imagenes/general/administracion/sinfoto.png" style="width:250px;height:300px;" class="img-thumbnail">
                                @endif
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input {!! $errors->first('atras','is-invalid')!!}" name="atras" id="atras" accept="image/jpg" capture="camera">
                                <label class="custom-file-label" for="atras">Choose file...</label>
                                {!! $errors->first('atras','<div class="invalid-feedback">Foto atras requerida en formato JPG de tamaño maximo 1MB.</div>')!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Extra 1(JPG)</label>
                            <div style="margin-bottom: 10px;">
                                @if(isset($supervisionActualizar[0]->extra1) && !empty($supervisionActualizar[0]->extra1) && file_exists($supervisionActualizar[0]->extra1))
                                    <img src="{{asset($supervisionActualizar[0]->extra1)}}" style="width:250px;height:300px;" class="img-thumbnail">
                                @else
                                    <img src="/imagenes/general/administracion/sinfoto.png" style="width:250px;height:300px;" class="img-thumbnail">
                                @endif
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input {!! $errors->first('extra1','is-invalid')!!}" name="extra1" id="extra1" accept="image/jpg" capture="camera">
                                <label class="custom-file-label" for="extra1">Choose file...</label>
                                {!! $errors->first('extra1','<div class="invalid-feedback">Foto extra requerida en formato JPG de tamaño maximo 1MB.</div>')!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Extra 2(JPG)</label>
                            <div style="margin-bottom: 10px;">
                                @if(isset($supervisionActualizar[0]->extra2) && !empty($supervisionActualizar[0]->extra2) && file_exists($supervisionActualizar[0]->extra2))
                                    <img src="{{asset($supervisionActualizar[0]->extra2)}}" style="width:250px;height:300px;" class="img-thumbnail">
                                @else
                                    <img src="/imagenes/general/administracion/sinfoto.png" style="width:250px;height:300px;" class="img-thumbnail">
                                @endif
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input {!! $errors->first('extra2','is-invalid')!!}" name="extra2" id="extra2" accept="image/jpg" capture="camera">
                                <label class="custom-file-label" for="extra2">Choose file...</label>
                                {!! $errors->first('extra2','<div class="invalid-feedback">Foto extra 2 requerida en formato JPG de tamaño maximo 1MB.</div>')!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Extra 3(JPG)</label>
                            <div style="margin-bottom: 10px;">
                                @if(isset($supervisionActualizar[0]->extra3) && !empty($supervisionActualizar[0]->extra3) && file_exists($supervisionActualizar[0]->extra3))
                                    <img src="{{asset($supervisionActualizar[0]->extra3)}}" style="width:250px;height:300px;" class="img-thumbnail">
                                @else
                                    <img src="/imagenes/general/administracion/sinfoto.png" style="width:250px;height:300px;" class="img-thumbnail">
                                @endif
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input {!! $errors->first('extra3','is-invalid')!!}" name="extra3" id="extra3" accept="image/jpg" capture="camera">
                                <label class="custom-file-label" for="extra3">Choose file...</label>
                                {!! $errors->first('extra3','<div class="invalid-feedback">Foto extra 3 requerida en formato JPG de tamaño maximo 1MB.</div>')!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Extra 4(JPG)</label>
                            <div style="margin-bottom: 10px;">
                                @if(isset($supervisionActualizar[0]->extra4) && !empty($supervisionActualizar[0]->extra4) && file_exists($supervisionActualizar[0]->extra4))
                                    <img src="{{asset($supervisionActualizar[0]->extra4)}}" style="width:250px;height:300px;" class="img-thumbnail">
                                @else
                                    <img src="/imagenes/general/administracion/sinfoto.png" style="width:250px;height:300px;" class="img-thumbnail">
                                @endif
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input {!! $errors->first('extra4','is-invalid')!!}" name="extra4" id="extra4" accept="image/jpg" capture="camera">
                                <label class="custom-file-label" for="extra4">Choose file...</label>
                                {!! $errors->first('extra4','<div class="invalid-feedback">Foto extra 4 requerida en formato JPG de tamaño maximo 1MB.</div>')!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Extra 5(JPG)</label>
                            <div style="margin-bottom: 10px;">
                                @if(isset($supervisionActualizar[0]->extra5) && !empty($supervisionActualizar[0]->extra5) && file_exists($supervisionActualizar[0]->extra5))
                                    <img src="{{asset($supervisionActualizar[0]->extra5)}}" style="width:250px;height:300px;" class="img-thumbnail">
                                @else
                                    <img src="/imagenes/general/administracion/sinfoto.png" style="width:250px;height:300px;" class="img-thumbnail">
                                @endif
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input {!! $errors->first('extra5','is-invalid')!!}" name="extra5" id="extra5" accept="image/jpg" capture="camera">
                                <label class="custom-file-label" for="extra5">Choose file...</label>
                                {!! $errors->first('extra5','<div class="invalid-feedback">Foto extra 5 requerida en formato JPG de tamaño maximo 1MB.</div>')!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Extra 6(JPG)</label>
                            <div style="margin-bottom: 10px;">
                                @if(isset($supervisionActualizar[0]->extra6) && !empty($supervisionActualizar[0]->extra6) && file_exists($supervisionActualizar[0]->extra6))
                                    <img src="{{asset($supervisionActualizar[0]->extra6)}}" style="width:250px;height:300px;" class="img-thumbnail">
                                @else
                                    <img src="/imagenes/general/administracion/sinfoto.png" style="width:250px;height:300px;" class="img-thumbnail">
                                @endif
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input {!! $errors->first('extra6','is-invalid')!!}" name="extra6" id="extra6" accept="image/jpg" capture="camera">
                                <label class="custom-file-label" for="extra6">Choose file...</label>
                                {!! $errors->first('extra6','<div class="invalid-feedback">Foto extra 6 requerida en formato JPG de tamaño maximo 1MB.</div>')!!}
                            </div>
                        </div>
                    </div>
                </div>
                <div style="margin-top: 20px">
                    <button type="submit" name="btnSubmit" class="btn btn-outline-success btn-block" form="formNuevaSupervision">ACTUALIZAR SUPERVISIÓN</button>
                </div>
            </form>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection