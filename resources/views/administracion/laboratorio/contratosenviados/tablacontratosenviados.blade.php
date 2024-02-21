@if(sizeof($contratosEnviadosSComentarios) > 0)
    @foreach($contratosEnviadosSComentarios as $contratoEnviadoSComentarios)
        <tr>
            <td align='center'>{{$contratoEnviadoSComentarios->created_at}}</td>
            <td align='center'>{{$contratoEnviadoSComentarios->fechaentrega}}</td>
            <td align='center'>{{$contratoEnviadoSComentarios->fechaenvio}}</td>
            <td align='center'>{{$contratoEnviadoSComentarios->id}}</td>
            <td align='center'>{{$contratoEnviadoSComentarios->ciudad}}</td>
            <td align='center'>{{$contratoEnviadoSComentarios->ultimoestatusmanufactura}}</td>

            @if($contratoEnviadoSComentarios->estatus_estadocontrato == 12)
                <td align='center'>
                    <button type="button" class="btn btn-info enviado btn-sm"
                            style="color:#FEFEFE;">{{$contratoEnviadoSComentarios->descripcion}}</button>
                </td>
            @endif
            <td align='center'><a href="{{route('estadolaboratorio',$contratoEnviadoSComentarios->id)}}">
                    <button type="button" class="btn btn-outline-success btn-sm"><i class="fas fa-pen"></i></button>
                </a></td>
        </tr>
    @endforeach
@else
    <td align='center' colspan="6">Sin registros</td>
@endif

