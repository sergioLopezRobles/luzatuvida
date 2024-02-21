@extends('layouts.app')
@section('titulo','Grafica Ventas'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
    <h2 style="text-align: left; color: #0AA09E">Total de ventas</h2>
    <div id="accordion">
        <div class="card">
            <div class="card-header" id="headingOne">
                <h5 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne"
                            aria-expanded="true"
                            aria-controls="collapseOne">
                        Filtros
                    </button>
                </h5>
            </div>
            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body">
                    <div class="row">
                            <div class="col-3">
                                <label for="franquiciaSeleccionada">Sucursal</label>
                                <div class="form-group">
                                    <select name="franquiciaSeleccionada"
                                            class="form-control"
                                            id="franquiciaSeleccionada">
                                        @if(count($franquicias) > 0)
                                            <option value="" selected>Todas las sucursales</option>
                                            @foreach($franquicias as $franquicia)
                                                <option
                                                    value="{{$franquicia->id}}" {{ isset($franquiciaSeleccionada) ? ($franquiciaSeleccionada == $franquicia->id ? 'selected' : '' ) : '' }}>
                                                    {{$franquicia->ciudad}}
                                                </option>
                                            @endforeach
                                        @else
                                            <option selected>Sin registros</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        <div class="col-2">
                            <label for="rolSeleccionado">Rol</label>
                            <div class="form-group">
                                <select name="rolSeleccionado"
                                        class="form-control"
                                        id="rolSeleccionado">
                                    @if(count($roles) > 0)
                                        <option value="{{$cadenaRoles}}" selected>Todos los roles</option>
                                        @foreach($roles as $rol)
                                            <option
                                                value="{{$rol->id}}">{{$rol->rol}}</option>
                                        @endforeach
                                    @else
                                        <option selected>Sin registros</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-2">
                            <label for="usuarioSeleccionado">Usuario</label>
                            <div class="form-group">
                                <select name="usuarioSeleccionado"
                                        class="form-control"
                                        id="usuarioSeleccionado">
                                        <option selected value="">Todos los usuarios</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label>Fecha inicial</label>
                                <input type="date" name="fechaInicio" id="fechaInicio" class="form-control {!! $errors->first('fechaInicial','is-invalid')!!}"
                                       @isset($fechaInicial) value = "{{$fechaInicial}}" @endisset>
                                @if($errors->has('fechaInicial'))
                                    <div class="invalid-feedback">{{$errors->first('fechaInicial')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label>Fecha final</label>
                                <input type="date" name="fechaFin" id="fechaFin" class="form-control {!! $errors->first('fechaFinal','is-invalid')!!}"
                                       @isset($fechaFinal) value = "{{$fechaFinal}}" @endisset>
                                @if($errors->has('fechaFinal'))
                                    <div class="invalid-feedback">{{$errors->first('fechaFinal')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-1" id="spCargando">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" style="width: 2rem; height: 2rem; margin-top: 30px;" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-2"> </div>
        <div class="col-7" id="graficasVentas" style="margin: 20px;"> </div>
    </div>
    </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
