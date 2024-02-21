@extends('layouts.app')
@section('titulo','Administracion imagenes'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2 style="text-align: left; color: #0AA09E">Administración pagina clientes</h2>
        {{--Seccion de agregar nueva imagen a BD--}}
        <div id="accordion">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Agregar imagen
                        </button>
                    </h5>
                </div>
                <div id="collapseOne"  aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <form class="was-validated" action="{{route('agregarimagencarrucel',$idFranquicia)}}" enctype="multipart/form-data" method="POST"
                              id="formNuevaImagenCarrusel">
                            @csrf
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Seleccionar imagen</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input {!! $errors->first('nuevaImagen','is-invalid')!!}" name="nuevaImagen" id="nuevaImagen" accept="image/jpg" required>
                                            <label class="custom-file-label" for="contratolaboral">Selecciona una imagen JPG...</label>
                                            {!! $errors->first('nuevaImagen','<div class="invalid-feedback">La imagen debe estar en formato jpg.</div>')!!}
                                            <label style="color: rgba(255,15,0,0.17); font-weight: bold;">Tamaño requerido de imagen - 1350 x 900 píxeles.</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-7">
                                    <div class="form-group">
                                        <div class="custom-file">
                                            <button class="btn btn-outline-success btn-block" type="submit" id="btnNuevaImagenCarrusel" form="formNuevaImagenCarrusel">Agregar</button>
                                            <label style="color: rgba(255,15,0,0.17); font-weight: bold;">Al agregar la imagen esta se ingresara en la última posición de almacenamiento, si desea moverla de posición actualízala enseguida</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <hr class="mt-4">
        {{--Tabla de imagenes existentes--}}
        <div class="row" style="margin-top: 30px;">
            <div class="col-6">
                <h4>Actualizar posición imagen</h4>
                <form action="{{route('actualizarposicionimagencarrucel',$idFranquicia)}}" enctype="multipart/form-data" method="POST"
                      id="formActualizarPosicion">
                    @csrf
                <div class="row">
                    <div class="col-10">
                            <div class="form-group">
                                <label>Imagenes:</label>
                                <select name="imagenSeleccionada"
                                        id="imagenSeleccionada"
                                        class="form-control {!! $errors->first('imagenSeleccionada','is-invalid')!!}">
                                    @if(count($imagenesCarrusel) > 0)
                                        <option value="">Seleccionar imagen</option>
                                        @foreach($imagenesCarrusel as $imagen)
                                            <option
                                                value="{{$imagen->id}}">Posición: {{$imagen->posicion}} | Nombre: {{$imagen->nombre}}</option>
                                        @endforeach
                                    @else
                                        <option selected>Sin registros</option>
                                    @endif
                                </select>
                                {!! $errors->first('imagenSeleccionada','<div class="invalid-feedback">Seleccionada una imagen correcta.</div>')!!}
                            </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-10">
                        <div class="form-group">
                            <label>Posición:</label>
                            <select name="posicionSeleccionada"
                                    id="posicionSeleccionada"
                                    class="form-control {!! $errors->first('posicionSeleccionada','is-invalid')!!}">
                                @if(count($imagenesCarrusel) > 0)
                                    <option value="">Seleccionar posición</option>
                                    @foreach($imagenesCarrusel as $imagen)
                                        <option
                                            value="{{$imagen->posicion}}">{{$imagen->posicion}}
                                        </option>
                                    @endforeach
                                @else
                                    <option selected>Sin registros</option>
                                @endif
                            </select>
                            {!! $errors->first('posicionSeleccionada','<div class="invalid-feedback">Seleccionada una posición correcta.</div>')!!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-10">
                        <button class="btn btn-outline-success btn-block" type="submit" form="formActualizarPosicion" id="btnNuevoUsuarioFranquicia">Actualizar posición</button>
                    </div>
                </div>
                </form>
            </div>
            <div class="col-6">
                <table id="tblImagenesCarrusel" class="table-bordered table-striped table-general table-sm">
                    <thead>
                    <tr>
                        <th  style =" text-align:center;" scope="col">POSICIÓN IMAGEN</th>
                        <th  style =" text-align:center;" scope="col">IMAGEN</th>
                        <th  style =" text-align:center;" scope="col">NOMBRE IMAGEN</th>
                        <th  style =" text-align:center;" scope="col">ELIMINAR</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($imagenesCarrusel != null)
                        @foreach($imagenesCarrusel as $imagen)
                            <tr>
                                <td align='center' style="vertical-align: middle;">{{$imagen->posicion}}</td>
                                <td align='center'> <img src="{{asset($imagen->imagen)}}" style="width:50px;height:50px;" class="img-thumbnail" > </td>
                                <td align='center'> {{$imagen->nombre}}</td>
                                <td align='center'>
                                    <a class="btn btn-outline-danger btn-sm" href="{{route('eliminarimagencarrusel',[$idFranquicia,$imagen->id])}}">
                                        <i class="bi bi-trash3-fill"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td align='center' colspan="4">Sin registros</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
