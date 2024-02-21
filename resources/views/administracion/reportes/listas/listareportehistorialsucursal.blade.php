@if(sizeof($listaMovimientos) > 0)
    @foreach($listaMovimientos as $movimiento)

        <tr>
            <td align='center'>{{$movimiento->name}}</td>
            <td align='center'>{{$movimiento->cambios}}</td>
            <td align='center'>{{$movimiento->created_at}}</td>
        </tr>

    @endforeach
@else
    <td align='center' colspan="3">Sin registros</td>
@endif
