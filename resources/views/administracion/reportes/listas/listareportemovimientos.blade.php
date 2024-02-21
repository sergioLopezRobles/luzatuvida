@if(sizeof($historialMovimientos) > 0)
    @foreach($historialMovimientos as $historialmovimiento)
        @if(str_contains($historialmovimiento->cambios, 'elimino'))
            <tr style="background-color: rgba(255,15,0,0.17)">
                <td align='center'>{{$historialmovimiento->name}}</td>
                <td align='center'>{{$historialmovimiento->id_contrato}}</td>
                <td align='center'>{{$historialmovimiento->nombre}}</td>
                <td align='center'>{{$historialmovimiento->cambios}}</td>
                <td align='center'>{{$historialmovimiento->created_at}}</td>
                <td align='center'><a href="{{route('vercontrato',[$idFranquicia,$historialmovimiento->id_contrato])}}" target="_blank" class="btn btn-primary btn-sm
">ABRIR</a></td>
            </tr>
        @else
        <tr>
            <td align='center'>{{$historialmovimiento->name}}</td>
            <td align='center'>{{$historialmovimiento->id_contrato}}</td>
            <td align='center'>{{$historialmovimiento->nombre}}</td>
            <td align='center'>{{$historialmovimiento->cambios}}</td>
            <td align='center'>{{$historialmovimiento->created_at}}</td>
            <td align='center'><a href="{{route('vercontrato',[$idFranquicia,$historialmovimiento->id_contrato])}}" target="_blank" class="btn btn-primary btn-sm
">ABRIR</a></td>
        </tr>
        @endif
    @endforeach
@else
    <th style="text-align: center;" colspan="6">Sin registros</th>
@endif
