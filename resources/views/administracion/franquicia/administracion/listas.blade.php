@extends('layouts.app')
@section('titulo','Administracion'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    <div class="contenedor">
        <h2>@lang('mensajes.mensajeproductos')</h2>
        <form action="{{route('filtrarproducto',$idFranquicia)}}" enctype="multipart/form-data" method="POST"
              onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="row">
                <div class="col-4">
                    <input name="filtro" type="text" class="form-control" placeholder="Buscar producto..">
                </div>
                <div class="col-6">
                    <button type="submit" name="btnSubmit" class="btn btn-outline-success">Filtrar</button>
                </div>
                <div class="col-2">
                    <a type="button" class="btn btn-outline-success btn-block"
                       href="{{route('productonuevo',$idFranquicia)}}">Nuevo producto</a>
                </div>
            </div>
        </form>
        <div @if(sizeof($productosGeneral) < 10) @else style="max-height: 350px; overflow-y: scroll; max-width: 100%" @endif>
            <table id="tablaContratos" class="table-bordered table-striped table-general">
                <tr>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" colspan="10">ALMACEN SUCURSAL</th>
                </tr>
                @if(sizeof($productosGeneral)>0)
                    <thead>
                    <tr>
                        <th style=" text-align:center;" scope="col">ESTADO</th>
                        <th style=" text-align:center;" scope="col">PRODUCTO</th>
                        <th style=" text-align:center;" scope="col">TIPO</th>
                        <th style=" text-align:center;" scope="col">FOTO</th>
                        <th style=" text-align:center;" scope="col">PIEZAS</th>
                        <th style=" text-align:center;" scope="col">PIEZAS RESTANTES</th>
                        <th style=" text-align:center;" scope="col">COLOR</th>
                        <th style=" text-align:center;" scope="col">PRECIO</th>
                        <th style=" text-align:center;" scope="col">VER</th>
                        <th style=" text-align:center;" scope="col">PROMOCION</th>
                    </tr>
                    </thead>
                @endif
                <tbody>
                @foreach($productosSucursal as $producto)
                    <tr>
                        @if($producto->estado  == 1)
                            <td align='center'><i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i></td>
                        @else
                            <td align='center'><i class='fas fa-check' style="color:#ffaca6;font-size:25px;"></i></td>
                        @endif
                        <td align='center'>{{$producto->nombre}}</td>
                        <td align='center'>{{$producto->tipo}}</td>
                        @if(isset($producto->foto) && !empty($producto->foto) && file_exists($producto->foto))
                            <td align='center'><img src="{{asset($producto->foto)}}" class="img-thumbnail"
                                                    style="width:50px;height:32px;"></td>
                        @elseif($producto->tipo == "Armazon")
                            <td align='center'>S/C</td>
                        @else
                            <td align='center'>NA</td>
                        @endif
                        <td align='center'>{{$producto->totalpiezas}}</td>
                        <td align='center'>{{$producto->piezas}}</td>
                        <td align='center'>{{$producto->color}}</td>
                        @if($producto->id_tipoproducto == 1)
                            <td align='center'>NA</td>
                        @else
                            @if($producto->activo == 1)
                                <td align='center'>Normal:${{$producto->precio}}<br>Promocion:${{$producto->preciop}}</br></td>
                            @else
                                <td align='center'>${{$producto->precio}}</td>
                            @endif
                        @endif
                        <td align='center'><a href="{{route('productoactualizar',[$idFranquicia,$producto->id])}}">
                                <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button>
                            </a></td>
                        @if($producto->activo  == 1)
                            <td align='center'><a href="{{route('productodesactivarpromo',[$idFranquicia,$producto->id])}}"
                                                  class="btn btn-outline-danger btn-sm">DESACTIVAR</a></td>
                        @else
                            @if($producto->id_tipoproducto > 1)
                                <td align='center'><a class="btn btn-primary btn-sm">DESACTIVADA</a></td>
                            @ELSE
                                <td align='center'><a class="btn btn-dark btn-sm">NA</a></td>
                            @endif
                        @endif
                    </tr>
                @endforeach
                <tr>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;"
                        colspan="10">ALMACEN GENERAL</th>
                </tr>
                @foreach($productosGeneral as $producto)
                    <tr>
                        @if($producto->estado  == 1)
                            <td align='center'><i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i></td>
                        @else
                            <td align='center'><i class='fas fa-check' style="color:#ffaca6;font-size:25px;"></i></td>
                        @endif
                        <td align='center'>{{$producto->nombre}}</td>
                        <td align='center'>{{$producto->tipo}}</td>
                        @if(isset($producto->foto) && !empty($producto->foto))
                            <td align='center'><img src="{{asset($producto->foto)}}" class="img-thumbnail"
                                                    style="width:50px;height:32px;"></td>
                        @elseif($producto->tipo == "Armazon")
                            <td align='center'>S/C</td>
                        @else
                            <td align='center'>NA</td>
                        @endif
                        <td align='center'>{{$producto->totalpiezas}}</td>
                        <td align='center'>{{$producto->piezas}}</td>
                        <td align='center'>{{$producto->color}}</td>
                        @if($producto->id_tipoproducto == 1)
                            <td align='center'>NA</td>
                        @else
                            @if($producto->activo == 1)
                                <td align='center'>Normal:${{$producto->precio}}<br>Promocion:${{$producto->preciop}}</br></td>
                            @else
                                <td align='center'>${{$producto->precio}}</td>
                            @endif
                        @endif
                        @if(Auth::user()->rol_id == 7)
                            <td align='center'><a href="{{route('productoactualizar',[$idFranquicia,$producto->id])}}">
                                    <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button>
                                </a></td>
                        @else
                            <td align='center'><a class="btn btn-dark btn-sm">NA</a></td>
                        @endif
                        <td align='center'><a class="btn btn-dark btn-sm">NA</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <hr>
        <div class="row">
            <h2>@lang('mensajes.mensajetratamientos')</h2>
            <!-- <div class="col-2">
           <a type="button" class="btn btn-outline-primary btn-block" href="{{route('tratamientonuevo',$idFranquicia)}}">Nuevo Tratamiento</a>
        </div> -->
        </div>
        <table id="tablaTratamientos" class="table-bordered table-striped table-general">
            @if(sizeof($tratamientos)>0)
                <thead>
                <tr>
                    <th style=" text-align:center;" scope="col">TRATAMIENTO</th>
                    <th style=" text-align:center;" scope="col">PRECIO</th>
                    <th style=" text-align:center;" scope="col">EDITAR</th>
                </tr>
                </thead>
            @endif
            <tbody>
            @foreach($tratamientos as $tratamiento)
                <tr>
                    <td align='center'>{{$tratamiento->nombre}}</td>
                    <td align='center'>${{$tratamiento->precio}}</td>
                    <td align='center'><a href="{{route('tratamientoactualizar',[$idFranquicia,$tratamiento->id])}}">
                            <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button>
                        </a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <hr>
        <div class="row">
            <div class="col-10"><h2>@lang('mensajes.mensajepaquetes')</h2></div>
        </div>
        <table id="tablaTratamientos" class="table-bordered table-striped table-general">
            @if(sizeof($paquetes)>0)
                <thead>
                <tr>
                    <th style=" text-align:center;" scope="col">PAQUETE</th>
                    <th style=" text-align:center;" scope="col">PRECIO</th>
                    <th style=" text-align:center;" scope="col">EDITAR</th>
                </tr>
                </thead>
            @endif
            <tbody>
            @foreach($paquetes as $paquete)
                <tr>
                    <td align='center'>{{$paquete->nombre}}</td>
                    <td align='center'>${{$paquete->precio}}</td>
                    <td align='center'><a href="{{route('paqueteactualizar',[$idFranquicia,$paquete->id])}}">
                            <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button>
                        </a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <hr>
        <div class="row">
            <div class="col-10"><h2>@lang('mensajes.mensajepromociones')</h2></div>
            <div class="col-2">
                <a type="button" class="btn btn-outline-success btn-block"
                   href="{{route('nuevaproarmazones',$idFranquicia)}}">Nueva promoción</a>
            </div>
        </div>
        <table id="tablaPromociones" class="table-bordered table-striped table-general">
            @if(sizeof($promociones)>0)
                <thead>
                <tr>
                    <th style=" text-align:center;" scope="col">ESTADO</th>
                    <th style=" text-align:center;" scope="col">TITULO</th>
                    <th style=" text-align:center;" scope="col">PRECIO</th>
                    <th style=" text-align:center;" scope="col">INICIO</th>
                    <th style=" text-align:center;" scope="col">FIN</th>
                    <th style=" text-align:center;" scope="col">EDITAR</th>
                    <th style=" text-align:center;" scope="col">ACTIVAR/DESACTIVAR</th>
                    <!-- <th  style =" text-align:center;" scope="col">EDITAR</th>                                 -->
                </tr>
                </thead>
            @endif
            <tbody>
            @foreach($promociones as $promocion)
                <tr>
                    @if($promocion->status  == 1)
                        <td align='center'><i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i></td>
                    @else
                        <td align='center'><i class='fas fa-times' style="color:#ffaca6;font-size:25px;"></i></td>
                    @endif
                    <td align='center'>{{$promocion->titulo}}</td>
                    @if($promocion->tipopromocion  == 1)
                        <td align='center'>$ {{$promocion->preciouno}}</td>
                    @else
                        <td align='center'>% {{$promocion->precioP}}</td>
                    @endif
                    <td align='center'>{{$promocion->inicio}}</td>
                    <td align='center'>{{$promocion->fin}}</td>
                    <td align='center'><a href="{{route('promocionactualizar',[$idFranquicia,$promocion->id])}}">
                            <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button>
                        </a></td>
                    @if($promocion->status  == 1 )
                        <td align='center'><a href="{{route('estadoPromocionEditar',[$idFranquicia,$promocion->id])}}"
                                              class="btn btn-outline-danger btn-sm">DESACTIVAR</a></td>
                    @else
                        <td align='center'><a href="{{route('promocionactualizar',[$idFranquicia,$promocion->id])}}"
                                              class="btn btn-outline-primary btn-sm">ACTIVAR</a></td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(Auth::user()->rol_id == 7)
            <hr>
            <div class="row">
                <div class="col-12">
                    <h2>Abono minimo</h2>
                </div>
            </div>
            <table id="tablaContratos" class="table-bordered table-striped table-general">
                <thead>
                <tr>
                    <th style=" text-align:center;" scope="col">TIPO PAGO</th>
                    <th style=" text-align:center;" scope="col">ABONO MINIMO</th>
                    <th style=" text-align:center;" scope="col">INICIO</th>
                    <th style=" text-align:center;" scope="col">EDITAR</th>
                </tr>
                </thead>
                <tbody>
                @foreach($abonosMinimos as $abonoMinimo)
                    <tr>
                        @switch($abonoMinimo->pago)
                            @case(1)
                                <td align='center'>SEMANAL</td>
                                @break
                            @case(2)
                                <td align='center'>QUINCENAL</td>
                                @break
                            @case(4)
                                <td align='center'>MENSUAL</td>
                                @break
                        @endswitch
                        <td align='center'>{{$abonoMinimo->abonominimo}}</td>
                        <td align='center'>{{$abonoMinimo->updated_at}}</td>
                        <td align='center'><a href="{{route('editarabonominimo',[$idFranquicia,$abonoMinimo->pago])}}">
                                <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
        @if(Auth::user()->rol_id == 7)
            <hr>
            <div class="row">
                <div class="col-12">
                    <h2>Comisiones ventas</h2>
                </div>
            </div>
            <table id="tablaContratos" class="table-bordered table-striped table-general">
                <thead>
                <tr>
                    <th style=" text-align:center;" scope="col">ROL</th>
                    <th style=" text-align:center;" scope="col">COMISIÓN</th>
                    <th style=" text-align:center;" scope="col">CONTRATOS</th>
                    <th style=" text-align:center;" scope="col">PAGO</th>
                    <th style=" text-align:center;" scope="col">EDITAR</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($comisionesventas) && sizeof($comisionesventas) > 0)
                    @foreach($comisionesventas as $comisionventa)
                        <tr>
                            @switch($comisionventa->usuario)
                                @case(0)
                                    <td align='center'>ASISTENTE</td>
                                    @break
                                @case(1)
                                    <td align='center'>OPTOMETRISTA</td>
                                    @break
                            @endswitch
                            <td align='center'>{{$comisionventa->comision}}</td>
                            <td align='center'>{{$comisionventa->totalcontratos}}</td>
                            @switch($comisionventa->usuario)
                                @case(0)
                                    <td align='center'>${{$comisionventa->valor}}</td>
                                    @break
                                @case(1)
                                    <td align='center'>{{$comisionventa->valor}}%</td>
                                    @break
                            @endswitch
                            <td align='center'><a href="{{route('editarcomisionventa',[$idFranquicia,$comisionventa->indice])}}">
                                    <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <th align='center' colspan="5">SIN REGISTROS</th>
                    </tr>
                @endif
                </tbody>
            </table>
        @endif
        <hr>
        <div class="row">
            <div class="col-10"><h2>Zonas</h2></div>
            <div class="col-2">
                <a type="button" class="btn btn-outline-success btn-block"
                   href="{{route('zonanueva',$idFranquicia)}}">Nueva zona</a>
            </div>
        </div>
        <table id="tablaZonas" class="table-bordered table-striped table-general">
            <thead>
            <tr>
                <th style=" text-align:center;" scope="col">NOMBRE ZONA</th>
                <th style=" text-align:center;" scope="col">EDITAR</th>
                <th style=" text-align:center;" scope="col">ELIMINAR</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($zonas) && sizeof($zonas) > 0)
                @foreach($zonas as $zona)
                    <tr>
                        <td align='center'>{{$zona->zona}}</td>
                        <td align='center'><a href="{{route('zonaeditar',[$idFranquicia,$zona->id])}}">
                                <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button>
                            </a>
                        </td>
                        <td align='center'>
                            <a class="btn btn-outline-danger btnEliminarZona btn-sm" href="#" data-toggle="modal" data-target="#confirmacionEliminarZona"
                               data_parametros_modal="{{$zona->id}}">
                                <i class="bi bi-trash3-fill"></i></a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <th align='center' colspan="2">SIN REGISTROS</th>
                </tr>
            @endif
            </tbody>
        </table>
        <hr>
        @if(Auth::user()->rol_id == 7)
            <div class="row">
                <div class="col-12"><h2>Acciones franquicia</h2></div>
            </div>
            <div class="row">
                <div class="col-2">
                    <div class="form-group">
                        @if($accionbanderaasistenciafranquicia != null && $accionbanderaasistenciafranquicia[0]->estatus == 1)
                            <input type="text" style="background-color:#0275d8;color:#FFFFFF;text-align:center"
                                   name="estatusaccionbanderaasistenciafranquiciatexto"
                                   class="form-control" readonly value="POR LECTOR DE BARRAS">
                        @else
                            <input type="text" style="background-color:#5cb85c;color:#FFFFFF;text-align:center"
                                   name="estatusaccionbanderaasistenciafranquiciatexto" class="form-control" readonly value="POR NÚMERO DE CONTROL">
                        @endif
                        <form action="{{route('actualizaraccionbanderaasistenciafranquicia',$idFranquicia)}}"
                              method="POST">
                            @csrf
                            <div class="custom-control custom-switch" style="text-align: left">
                                <input type="checkbox" class="custom-control-input" name="estatusaccionbanderaasistenciafranquicia" id="estatusaccionbanderaasistenciafranquicia"
                                       @if($accionbanderaasistenciafranquicia != null && $accionbanderaasistenciafranquicia[0]->estatus == 1) checked @endif
                                       onclick="eventactualizaraccionbanderaasistenciafranquicia(event)">
                                <label class="custom-control-label" for="estatusaccionbanderaasistenciafranquicia"></label>
                            </div>
                        </form>
                        <div><label>Asistencia</label></div>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <form  action="{{route('actualizarhorabanderaterminarpolizafranquicia',$idFranquicia)}}" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                            @csrf
                            <div class="row">
                                <input type="time" name="horaterminarpolizafranquicia" class="form-control" value="{{$horabanderaterminarpolizafranquicia}}">
                                <button class="btn btn-outline-success btn-block" type="submit">Actualizar</button>
                                <div><label>Hora para que se termine poliza automaticamente</label></div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        <hr>
        @endif
        <div class="row">
            <div class="col-10">
                <h2>@lang('mensajes.mensajeMensajes')</h2>
            </div>
            <div class="col-2">
                <a type="button" class="btn btn-outline-success btn-block" href="{{route('mensajenuevo',$idFranquicia)}}">Nuevo
                    mensaje</a>
            </div>
        </div>
        <table id="tablaContratos" class="table-bordered table-striped table-general">
            <thead>
            <tr>
                <th style=" text-align:center;" scope="col">DESCRIPCION</th>
                <th style=" text-align:center;" scope="col">FECHA PARA DESACTIVAR</th>
                <th style=" text-align:center;" scope="col">INTENTOS</th>
                <th style=" text-align:center;" scope="col">FECHA CREACION</th>
                <th style=" text-align:center;" scope="col">ELIMINAR</th>
            </tr>
            </thead>
            <tbody>
            @foreach($mensajes as $mensaje)
                <tr>
                    <td align='center'>{{$mensaje->descripcion}}</td>
                    <td align='center'>{{$mensaje->fechalimite}}</td>
                    <td align='center'>{{$mensaje->intentos}}</td>
                    <td align='center'>{{$mensaje->created_at}}</td>
                    <td align='center'><a href="{{route('eliminarmensaje',[$idFranquicia,$mensaje->id])}}"
                                          class="btn btn-outline-danger btn-sm">ELIMINAR</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <hr>

        <div class="modal fade" id="AbrirPro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <form action="" enctype="multipart/form-data" method="POST" onsubmit="btnSubmit.disabled = true;">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ExampleAbrir">Promoción</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">¿Seguro desea desactivar la promoción?</div>
                        <div class="modal-footer">
                            <button class="btn btn-default" type="button" data-dismiss="modal">Cancelar</button>
                            <a class="btn btn btn-outline-danger btn-ok" name="btnSubmit" type="submit">Aceptar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!--Modal de confirmaciones para eliminar zonas-->
        <div class="modal fade" id="confirmacionEliminarZona" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <form action="{{route('eliminarzona', $idFranquicia)}}" enctype="multipart/form-data"
                  method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #0AA09E; color: white;"><b>Eliminar zona</b></div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    ¿Estas seguro que quieres eliminar la zona?
                                </div>
                                <input type="hidden" name="idZona"/>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-success" data-dismiss="modal">Cancelar</button>
                            <button class="btn btn-outline-danger btn-ok" name="btnSubmit" type="submit">Eliminar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('parciales.notificaciones') {{-- Seccion de notificacion pie de pagina Evitar sobrepintar vista --}}
@endsection
