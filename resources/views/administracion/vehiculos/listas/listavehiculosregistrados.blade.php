<table id="tablaVehiculos" class="table-bordered table-striped table-general table-sm">
    <thead>
    <tr>
        <th  style =" text-align:center;" scope="col">CIUDAD</th>
        <th  style =" text-align:center;" scope="col">ASIGNADO A</th>
        <th  style =" text-align:center;" scope="col">SERIE</th>
        <th  style =" text-align:center;" scope="col">TIPO VEHICULO</th>
        <th  style =" text-align:center;" scope="col">MARCA</th>
        <th  style =" text-align:center;" scope="col">MODELO</th>
        <th  style =" text-align:center;" scope="col">PLACAS</th>
        <th  style =" text-align:center;" scope="col">ULTIMO SERVICIO</th>
        <th  style =" text-align:center;" scope="col">SIGUIENTE SERVICIO</th>
        <th  style =" text-align:center;" scope="col">VER</th>
        <th  style =" text-align:center;" scope="col">ELIMINAR</th>
    </tr>
    </thead>
    <tbody>
    @if(count($listavehiculos) > 0)

        @foreach($listavehiculos as $vehiculo)
            <tr>
                <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$vehiculo->ciudadfranquicia}}</td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle">
                    @if($vehiculo->asignacion != null)
                        @switch($vehiculo->rol)
                            @case (4)
                                @if($vehiculo->zona != null)
                                    {{$vehiculo->zona}} - {{$vehiculo->asignacion}}
                                @else
                                    {{$vehiculo->asignacion}}
                                @endif
                                @break
                            @case (17) {{$vehiculo->asignacion}} @break
                        @endswitch
                    @else SIN ASIGNACIÓN @endif</td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$vehiculo->numserie}}</td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$vehiculo->tipovehiculo}}</td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$vehiculo->marca}}</td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$vehiculo->modelo}}</td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle">@if($vehiculo->placas != null){{$vehiculo->placas}}@else S/C @endif</td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$vehiculo->ultimoservicio}}</td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$vehiculo->siguienteservicio}}</td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle"><a class="btn btn-outline-success btn-sm" href="{{route('vervehiculo',[$vehiculo->id_franquicia, $vehiculo->indice])}}" >
                        <i class="fa-solid fa-motorcycle"></i></a>
                </td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle">
                    <a class="btn btn-outline-danger btnEliminarVehiculo btn-sm" href="#" data-toggle="modal" data-target="#modalEliminarVehiculo"
                       data_parametros_modal="{{$vehiculo->id_franquicia. "," . $vehiculo->indice}}">
                        <i class="bi bi-trash3-fill"></i></a>
                </td>
            </tr>
        @endforeach
    @endif
    @if(count($listavehiculos) == 0)
        <tr>
            <td align='center' colspan="10" style="font-size: 10px;">Sin registros</td>
        </tr>
    @endif
    </tbody>
</table>

<div class="modal fade" id="modalEliminarVehiculo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="{{route('eliminarVehiculoSucursal')}}" enctype="multipart/form-data"
          method="POST" onsubmit="btnSubmit.disabled = true;">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #0AA09E; color: white;"><b>Eliminar vehículo</b></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            ¿Estas seguro que quieres dar de baja el vehículo?
                        </div>
                        <input type="hidden" name="idFranquicia" />
                        <input type="hidden" name="idVehiculo" />
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
