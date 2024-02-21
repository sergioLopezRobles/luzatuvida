@extends('layouts.app')
@section('titulo','Lista Franquicias'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
  <div class="contenedor">
      <h2 style="text-align: left; color: #0AA09E">Sucursales</h2>
      <div class="contenedortblFranquicias">
          <table id="tablaFranquicias" class="table table-bordered table-sm">
              @if(sizeof($franquicias)>0)
                  <thead>
                  <tr>
                      <th  style =" text-align:center;" scope="col">SUCURSAL</th>
                      <th  style =" text-align:center;" scope="col">ESTATUS</th>
                      <th  style =" text-align:center;" scope="col">ESTADO</th>
                      <th  style =" text-align:center;" scope="col">CIUDAD</th>
                      <th  style =" text-align:center;" scope="col">USUARIOS</th>
                      <th  style =" text-align:center;" scope="col">EDITAR</th>
                      <th  style =" text-align:center;" scope="col">VER</th>
                  </tr>
                  </thead>
              @endif
              <tbody>
              @foreach($franquicias as $franquicia)
                  <tr>
                      <td align='center'>{{$franquicia->ID}}</td>
                      @if($franquicia->ESTATUS  == 1)
                          <td align='center'><i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i></td>
                      @else
                          <td align='center' ><i class='fas fa-check' style="color:#ffaca6;font-size:25px;"></i></td>
                      @endif
                      <td align='center'>{{$franquicia->ESTADO}}</td>
                      <td align='center'>{{$franquicia->CIUDAD}}</td>
                      <td align='center'> <a href="{{route('usuariosFranquicia',$franquicia->ID)}}" ><button type="button" class="btn"><i  class="fas fa-users"></i></button></a></td>
                      <td align='center'> <a href="{{route('editarFranquicia',$franquicia->ID)}}" ><button type="button" class="btn"><i  class="fas fa-pen"></i></button></a></td>
                      <td align='center'> <a href="{{route('listacontrato',$franquicia->ID)}}" ><button type="button" class="btn"><i  class="fas fa-book-open"></i></button></a></td>
                  </tr>
              @endforeach
              </tbody>
          </table>
      </div>
  </div>

@include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
