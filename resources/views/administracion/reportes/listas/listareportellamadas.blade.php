<table class="table-bordered table-sm" style="width: 100%; margin-top: 20px;">
    <thead>
    <tr>
        <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">COBRADOR</th>
        <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">CONTRATO</th>
        <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">CLIENTE</th>
        <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col" colspan="4">MENSAJE</th>
    </tr>
    </thead>
    <tbody>
    @if($contratoscorte != null)

        @foreach($contratoscorte as $contratocorte)

            <tr @if($contratocorte->mensaje != null) style="background-color: #AAFAAA"
                @else style="background-color: #FACD73" @endif>
                <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;">{{$contratocorte->nombrecobrador}}</td>
                <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;">{{$contratocorte->id_contrato}}</td>
                <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;">{{$contratocorte->nombrecliente}}</td>
                <td align='center' style="text-align:center; font-size: 11px; padding: 5px;" colspan="4">{{$contratocorte->mensaje}}</td>
            </tr>
        @endforeach
    @else
        <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;" colspan="7">Sin registros</td>
    @endif
    </tbody>
</table>
