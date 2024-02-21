<table id="tablaContratos" class="table-bordered table-striped table-general table-sm" style="margin-top: 10px;">
    <thead>
    <tr>
        <th  style =" text-align:center;" scope="col">SUCURSAL</th>
        <th  style =" text-align:center;" scope="col">CONTRATO</th>
        <th  style =" text-align:center;" scope="col">USUARIOCREACION</th>
        <th  style =" text-align:center;" scope="col">OPTOMETRISTA</th>
        <th  style =" text-align:center;" scope="col">FECHA CREACIÓN</th>
        <th  style =" text-align:center;" scope="col">ESTATUS</th>
        <th  style =" text-align:center;" scope="col">VER</th>
    </tr>
    </thead>
    <tbody>
    @if(!is_null($contratosConComentarios) && count($contratosConComentarios)>0)
        <tr>
            <th  style =" text-align:center;;background-color:#0AA09E;color:#FFFFFF;" colspan="17">CON COMENTARIOS</th>
        </tr>
        @foreach($contratosConComentarios as $contratoConComentarios)
            <tr>
                <td align='center'>{{$contratoConComentarios->sucursal}}</td>
                <td align='center'>{{$contratoConComentarios->id}}</td>
                <td align='center'>{{$contratoConComentarios->usuariocreacion}}}}</td>
                <td align='center'>{{$contratoConComentarios->name}}</td>
                <td align='center'>{{\Carbon\Carbon::parse($contratoConComentarios->created_at)->format('Y-m-d')}}</td>
                @if($contratoConComentarios->estatus_estadocontrato == 7)
                    <td align='center'><button type="button" class="btn btn-primary aprobado btn-sm" style="color:#FEFEFE;">{{$contratoConComentarios->descripcion}}</button></td>
                @endif
                @if($contratoConComentarios->estatus_estadocontrato == 10)
                    <td align='center'> <button type="button" class="btn btn-warning manofactura btn-sm" style="color:#FEFEFE;">{{$contratoConComentarios->descripcion}}</button></td>
                @endif
                @if($contratoConComentarios->estatus_estadocontrato == 11)
                    <td align='center'> <button type="button" class="btn btn-info enprocesodeenvio btn-sm" style="color:#FEFEFE;">{{$contratoConComentarios->descripcion}}</button></td>
                @endif
                <td align='center'> <a href="{{route('estadoconfirmacion',[$contratoConComentarios->id])}}" >
                        <button type="button" class="btn btn-outline-success btn-sm"><i  class="fas fa-pen"></i></button></a>
                </td>
            </tr>
        @endforeach
    @endif
    @if(!is_null($contratosScomentarios) && count($contratosScomentarios)>0)
        <tr>
            <th  style =" text-align:center;;background-color:#0AA09E;color:#FFFFFF;" colspan="17">TODOS</th>
        </tr>
        @foreach($contratosScomentarios as $contratoScomentarios)
            <tr>
                <td align='center'>{{$contratoScomentarios->sucursal}}</td>
                <td align='center'>{{$contratoScomentarios->id}}</td>
                <td align='center'>{{$contratoScomentarios->usuariocreacion}}</td>
                <td align='center'>{{$contratoScomentarios->name}}</td>
                <td align='center'>{{\Carbon\Carbon::parse($contratoScomentarios->created_at)->format('Y-m-d')}}</td>
                @if($contratoScomentarios->estatus_estadocontrato == 7)
                    <td align='center'><button type="button" class="btn btn-primary aprobado btn-sm" style="color:#FEFEFE;">{{$contratoScomentarios->descripcion}}</button></td>
                @endif
                @if($contratoScomentarios->estatus_estadocontrato == 10)
                    <td align='center'> <button type="button" class="btn btn-warning manofactura btn-sm" style="color:#FEFEFE;">{{$contratoScomentarios->descripcion}}</button></td>
                @endif
                @if($contratoScomentarios->estatus_estadocontrato == 11)
                    <td align='center'> <button type="button" class="btn btn-info enprocesodeenvio btn-sm" style="color:#FEFEFE;">{{$contratoScomentarios->descripcion}}</button></td>
                @endif
                <td align='center'> <a href="{{route('estadoconfirmacion',[$contratoScomentarios->id])}}" >
                        <button type="button" class="btn btn-outline-success btn-sm"><i  class="fas fa-pen"></i></button></a>
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
