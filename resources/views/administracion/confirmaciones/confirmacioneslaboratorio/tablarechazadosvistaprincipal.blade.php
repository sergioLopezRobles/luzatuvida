<table id="tablaContratos" class="table-bordered table-striped table-general table-sm" style="margin-top: 10px;">
    <thead>
    <tr>
        <th style=" text-align:center;" scope="col">SUCURSAL</th>
        <th style=" text-align:center;" scope="col">CONTRATO</th>
        <th style=" text-align:center;" scope="col">USUARIOCREACION</th>
        <th style=" text-align:center;" scope="col">OPTOMETRISTA</th>
        <th style=" text-align:center;" scope="col">CLIENTE</th>
        <th style=" text-align:center;" scope="col">TELEFONO</th>
        <th style=" text-align:center;" scope="col">FECHA CREACION</th>
        <th style=" text-align:center;" scope="col">FECHA RECHAZO</th>
        <th style=" text-align:center;" scope="col">ESTATUS</th>
        <th style=" text-align:center;" scope="col">RESTABLECER</th>
    </tr>
    </thead>
    <tbody>
    @if(!is_null($contratosRechazados) && count($contratosRechazados)>0)
        @foreach($contratosRechazados as $contrato)
            <tr>
                <td align='center'>{{$contrato->sucursal}}</td>
                <td align='center'>{{$contrato->id}}</td>
                <td align='center'>{{$contrato->usuariocreacion}}</td>
                <td align='center'>{{$contrato->name}}</td>
                <td align='center'>{{$contrato->nombre}}</td>
                <td align='center'>{{$contrato->telefono}}</td>
                <td align='center'>{{$contrato->created_at}}</td>
                <td align='center'>{{$contrato->fecharechazadoconfirmaciones}}</td>
                @if($contrato->estatus_estadocontrato == 8)
                    <td align='center'>
                        <button type="button" class="btn btn-danger precancelados"
                                style="color:#ff0000; font-size: 10px;">{{$contrato->descripcion}}</button>
                    </td>
                @endif
                <td align='center'><a href="{{route('restablecercontratorechazado',[$contrato->id])}}">
                        <button type="button" class="btn btn-outline-success btn-sm"><i class="bi bi-arrow-clockwise"></i></button>
                    </a>
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
