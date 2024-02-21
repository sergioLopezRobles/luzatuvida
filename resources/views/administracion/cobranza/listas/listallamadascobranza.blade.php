<div class="row" style="margin-left: 5px; margin-top: 30px; margin-right: 5px;">
    <table class="table-bordered table-striped table-sm" style="width: 100%; overflow-x: auto;">
        <thead>
        <tr>
            <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">COBRADOR</th>
            <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">CONTRATO</th>
            <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">CLIENTE</th>
            <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">ULTIMO ABONO</th>
            <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">SALDO</th>
            <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">TELEFONO</th>
            <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">TELEFONO REFERENCIA</th>
            <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">MENSAJE</th>
            <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">ACCION</th>
            <th  style=" text-align:center; font-size: 11px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">LINK</th>
        </tr>
        </thead>
        <tbody>
        @if($contratoscortellamada != null && sizeof($contratoscortellamada) > 0)

            @foreach($contratoscortellamada as $contratocortellamada)

                <tr @if($contratocortellamada->mensaje != null) style="background-color: #AAFAAA"
                    @else style="background-color: #FACD73" @endif>
                    <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;" >{{$contratocortellamada->nombrecobrador}}</td>
                    <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;" >{{$contratocortellamada->id_contrato}}</td>
                    <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;" >{{$contratocortellamada->nombrecliente}}</td>
                    <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;" >${{$contratocortellamada->ultimoabono}}</td>
                    <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;" >${{$contratocortellamada->total}}</td>
                    <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;" >{{$contratocortellamada->telefono}}</td>
                    <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;" >{{$contratocortellamada->telefonoreferencia}}</td>
                    <td align='center' style="text-align:center; font-size: 11px; padding: 5px;" >{{$contratocortellamada->mensaje}}</td>
                    <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;" >
                        <a class="btn btn-primary btnmarcarcontratocortellamada btn-sm" href="#" data-toggle="modal"
                           data-target="#modalmarcarcontratocortellamada"
                           data_parametros_modal="
                                        {{$contratocortellamada->indice .
                                            "," . $contratocortellamada->id_contrato .
                                            "," . $contratocortellamada->mensaje .
                                            "," . $contratocortellamada->telefono .
                                            "," . $contratocortellamada->telefonoreferencia .
                                            "," . $contratocortellamada->nombrecliente .
                                            "," . $contratocortellamada->ultimoabono .
                                            "," . $contratocortellamada->total
                                        }}">MARCAR
                        </a>
                    </td>
                    <td align='center' style="text-align:center; font-size: 11px; padding: 5px;"><a href="{{route('vercontrato',[$idFranquicia,$contratocortellamada->id_contrato])}}" target="_blank" class="btn
                    btn-primary btn-sm">ABRIR</a></td>
                </tr>

            @endforeach

        @else
            <td align='center' style="text-align:center; font-size: 11px; white-space: nowrap; padding: 5px;"  colspan="10">Sin registros</td>
        @endif
        </tbody>
    </table>
</div>

<!-- modal para confirmar cortellamada -->
<div class="modal fade" id="modalmarcarcontratocortellamada" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form action="{{route('marcarcontratocortellamada')}}" enctype="multipart/form-data"
          method="GET" onsubmit="btnSubmit.disabled = true;">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Solicitud de confirmación
                </div>
                <div class="modal-body">
                    <div class="col-12" style="color: #ea9999">
                        Al presionar "aceptar" aceptas que se le realizo la llamada al cliente
                    </div>
                    <input type="hidden" name="indice" />
                    <input type="hidden" name="id_contrato" />
                    <br>
                    Describa lo que se comento durante la llamada.
                    <textarea name="mensaje"
                              class="form-control" rows="10"
                              cols="60"></textarea>
                    <br>
                    <div class="row">
                        <div class="col-5"><p id="codigocontrato"></p></div>
                        <div class="col-7" style="text-align: right"><p id="nombrecliente"></p></div>
                    </div>
                    <div class="row">
                        <div class="col-6"><p id="telefono"></p></div>
                        <div class="col-6" style="text-align: right"><p id="telefonoreferencia"></p></div>
                    </div>
                    <div class="row">
                        <div class="col-8"><p id="ultimoabono"></p></div>
                        <div class="col-4" style="text-align: right"><p id="total"></p></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-success" name="btnSubmit" type="submit">Aceptar</button>
                </div>
            </div>
        </div>
    </form>
</div>
