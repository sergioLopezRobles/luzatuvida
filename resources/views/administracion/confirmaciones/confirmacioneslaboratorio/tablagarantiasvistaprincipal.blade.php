<table id="tablaContratos" class="table-bordered table-striped table-general table-sm" style="margin-top: 10px;">
    <thead>
    <tr>
        <th  style =" text-align:center;" scope="col">SUCURSAL</th>
        <th  style =" text-align:center;" scope="col">CONTRATO</th>
        <th  style =" text-align:center;" scope="col">FECHA CREACION</th>
        <th  style =" text-align:center;" scope="col">FECHA CEREACION GARANTIA</th>
        <th  style =" text-align:center;" scope="col">ESTATUS GARANTIA</th>
        <th  style =" text-align:center;" scope="col">ESTATUS</th>
        <th  style =" text-align:center;" scope="col">ASISTENTE</th>
        <th  style =" text-align:center;" scope="col">OPTOMETRISTA</th>
        <th  style =" text-align:center;" scope="col">VER</th>
    </tr>
    </thead>
    <tbody>
    @if(!is_null($contratosGarantias) && count($contratosGarantias)>0)
        @foreach($contratosGarantias as $contrato)
            <tr>
                <td align='center'>{{$contrato->sucursal}}</td>
                <td align='center'>{{$contrato->id_contrato}}</td>
                <td align='center'>{{\Carbon\Carbon::parse($contrato->fechacreacioncontrato)->format('Y-m-d')}}</td>
                <td align='center'>{{\Carbon\Carbon::parse($contrato->fechacreaciongarantia)->format('Y-m-d')}}</td>
                @switch($contrato->estadogarantia)
                    @case(0)
                    <td align='center' style="color:#FFFFFF; background-color:#ea9999;">Reportada</td>
                    @break
                    @case(1)
                    <td align='center' style="color:#FFFFFF; background-color:#5bc0de;">Asignada</td>
                    @break
                @endswitch
                <td align='center'><button type="button" class="btn btn-secondary btn-sm" style="color:#FEFEFE;">{{$contrato->descripcion}} </button></td>
                <td align='center'>{{$contrato->nombre_usuariocreacion}}</td>
                <td align='center'>{{$contrato->nombreoptometrista}}</td>
                <td align='center'> <a href="{{route('estadoconfirmacion',[$contrato->id_contrato])}}" >
                        <button type="button" class="btn btn-outline-success btn-sm"><i  class="fas fa-pen"></i></button></a>
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
