@if($idUsuario != null)
    <input type="hidden" id="idUsuarioActual" value="{{$idUsuario[0]->id}}">
    <div class="row">
        <div class="col-3">
            <div class="form-group">
                <label>Ultimo corte</label>
                <input type="text" name="ultimocorte" class="form-control"  readonly value="{{$ultimocorte}}">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label for="">Asistencia</label>
                <select class="custom-select"
                        name="asistencia" id="asistencia">
                    <option selected value="nada">Seleccionar</option>
                    <option value="0">Falta</option>
                    <option value="1">Asistencia</option>
                    <option value="2">Retardo</option>
                </select>
            </div>
        </div>
        <div class="col-2" style="margin-top: 30px">
            <a href="#" data-href="" data-toggle="modal" data-target="#confirmacion"><button id="btnReiniciarCorte" type="button" class="btn btn-outline-danger">Reiniciar corte</button></a>
        </div>
    </div>
    <div class="modal fade" id="confirmacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        Reiniciar corte
                    </div>
                    <div class="modal-body">
                        ¿Estas seguro de reiniciar el corte para ({{$idUsuario[0]->name}})?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-success" data-dismiss="modal">No</button>
                        <button class="btn btn-outline-danger btn-ok" name="btnSubmit" id="btnSubmit" onclick="reinicarCorteCobranza()">Si</button>
                    </div>
                </div>
            </div>
    </div>
@endif
@if($semanalC != null || $quincenalC != null || $mensualC != null || $contadoC != null)
    <h4>Contratos cobrados</h4>
    <div class="row">
        <div class="col-2">
            <div class="form-group">
                <label>Semanales</label>
                <input type="text" name="estado" class="form-control"  readonly value="{{$semanalC}}">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Quincenales</label>
                <input type="text" name="estado" class="form-control"  readonly value="{{$quincenalC}}">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Mensuales</label>
                <input type="text" name="estado" class="form-control"  readonly value="{{$mensualC}}">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Contado</label>
                <input type="text" name="estado" class="form-control"  readonly value="{{$contadoC}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-2">
            <div class="form-group">
                <label>Efectivo</label>
                <input type="text" name="estado" class="form-control"  readonly value="{{$efectivoC}}">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Tarjeta</label>
                <input type="text" name="estado" class="form-control"  readonly value="{{$tarjetaC}}">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Transferencia</label>
                <input type="text" name="estado" class="form-control"  readonly value="{{$transferenciaC}}">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Cancelación</label>
                <input type="text" name="estado" class="form-control"  readonly value="{{$cancelacionC}}">
            </div>
        </div>
        <div class="col-3">
            <!-- En blanco -->
        </div>
        @isset($totalContadorContratosConAbonosC)
            <div class="col-1">
                <div class="form-group">
                    <label>Total contratos</label>
                    <input type="text" name="estado" class="form-control" style="background-color: #0AA09E; color: white;"  readonly value="{{$totalContadorContratosConAbonosC}}">
                </div>
            </div>
        @endisset
    </div>
    <div class="row">
        <div class="col-2">
            <div class="form-group">
                <label>Efectivo</label>
                <input type="text" name="estado" class="form-control"  readonly value="$ {{$ingresosEfectivo}}">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Tarjeta</label>
                <input type="text" name="estado" class="form-control"  readonly value="$ {{$ingresosTarjeta}}">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Transferencia</label>
                <input type="text" name="estado" class="form-control"  readonly value="$ {{$ingresosTransferencia}}">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Cancelación</label>
                <input type="text" name="estado" class="form-control"  readonly value="$ {{$ingresosCancelacion}}">
            </div>
        </div>
        <div class="col-1">
            <div class="form-group">
                <label>Producto</label>
                <input type="text" name="estado" class="form-control"  readonly value="$ {{$ingresosProducto}}">
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <label>Ingresos en efectivo</label>
                <input type="text" name="estado" class="form-control" style="background-color: #FFC107; color: #212529;"  readonly value="$ {{$totalIngresosSEfectivo}}">
            </div>
        </div>
        <div class="col-1">
            <div class="form-group">
                <label>Total ingresos</label>
                <input type="text" name="estado" class="form-control" style="background-color: #0AA09E; color: white;"  readonly value="$ {{$totalIngresosC}}">
            </div>
        </div>
    </div>
@endif

<div class="row" style="margin-bottom: 5px;">
    <div class="col-2">
        <h4>Abonos</h4>
    </div>
    <div class="col-2">
        <i class="fa-solid fa-location-dot" data-toggle="modal" data-target="#modalgooglemaps" style="cursor: pointer" id="btnCrearMarcadores"
           onclick="crearMarcadoresAbonosMovimientoscobranza('{{json_encode($movimientos)}}');">Ver Mapa</i>
    </div>
    <div class="modal fade" id="modalgooglemaps" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    Marcadores
                </div>
                <div class="modal-body">
                    <div id="map" style="border: black 3px solid; width:100%; height: 500px;"></div>
                    <div id="simbologia" style="background-color: white; margin-right: 20px; margin-top: 60px; font-size: 12px; font-weight: bold; text-align: center;">Información</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-success" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Mostrar boton de imprimir corte solo si opcion es distinta a movimientos cobrador y diferente de corte actual-->
    @if($opcion != 0 && $opcion != 1)
        <div class="col-8" align="right">
            <button class="btn btn-outline-info" onclick="descargarTicketAbonosCorte('{{json_encode($abonosCorteTicket)}}','{{$totalIngresosSEfectivo}}', '{{$nombreCobrador}}', '{{$fechaActual}}')">Descargar ticket</button>
        </div>
    @endif
</div>

<div class="row" style="margin-bottom: 20px; display: flex; flex-direction: column;">
    <div class="col-2">
        <h4>Tickets de visita</h4>
    </div>
    <div class="col-12">
        <table  class="table-bordered table-striped table-general table-sm">
            <thead>
            <tr>
                <th  style ="text-align:center;" scope="col">CONTRATO</th>
                <th  style ="text-align:center;" scope="col">MOVIMIENTO</th>
                <th  style ="text-align:center;" scope="col">FECHA</th>
                <th  style ="text-align:center;" scope="col">LINK</th>
            </tr>
            </thead>
            <tbody>
            @if($ticketVisita != null)
                @foreach($ticketVisita as $ticket)
                    <tr  style="background-color: rgba(255,15,0,0.17)">
                        <td  style ="text-align:center;">{{$ticket->id_contrato}}</td>
                        <td  style ="text-align:center;">{{$ticket->cambios}}</td>
                        <td  style ="text-align:center;">{{$ticket->created_at}}</td>
                        <td align='center' style="text-align:center; font-size: 11px; padding: 5px;"><a href="{{route('vercontrato',[$idFranquicia,$ticket->id_contrato])}}" target="_blank" class="btn btn-primary btn-sm">ABRIR</a></td>
                    <tr>
                @endforeach
            @else
                <tr>
                    <td style ="text-align:center;" colspan="4"> Sin registros </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>

<table  class="table-bordered table-striped table-general table-sm">
    <thead>
    <tr>
        <th  style =" text-align:center;" scope="col">INDICE</th>
        <th  style =" text-align:center;" scope="col">NOMBRE</th>
        <th  style =" text-align:center;" scope="col">CONTRATO</th>
        <th  style =" text-align:center;" scope="col">NOMBRE CLIENTE</th>
        <th  style =" text-align:center;" scope="col">ABONO</th>
        <th  style =" text-align:center;" scope="col">FORMA DE PAGO</th>
        <th  style =" text-align:center;" scope="col">FOLIO</th>
        <th  style =" text-align:center;" scope="col">TIPO DE PAGO</th>
        <th  style =" text-align:center;" scope="col">FECHA</th>
        <th  style =" text-align:center;" scope="col">LINK</th>
    </tr>
    </thead>
    <tbody>
    @if(sizeof($movimientos) > 0)
            @for($i = 0; $i < sizeof($movimientos); $i = $i + 1)
                <tr @if($movimientos[$i]["abono"] < $movimientos[$i]["abonominimo"] && $movimientos[$i]["total"] > 0) style="background-color: rgba(255,15,0,0.17)" @endif>
                    <td align='center' @switch($movimientos[$i]["numColor"])
                    @case (0) style="background-color: #0275d8;" {{-- AZUL FUERTE --}}
                    @break
                    @case (1) style="background-color: #fff2cc;" {{-- CREMA --}}
                    @break
                    @case (2) style="background-color: #5cb85c;" {{-- VERDE --}}
                    @break
                    @case (3) style="background-color: #ff0000;" {{-- ROJO --}}
                    @break
                    @case (4) style="background-color: #f0cc0a;" {{-- AMARILLO --}}
                    @break
                    @case (5) style="background-color: #5bc0de;" {{-- AZUL BAJO --}}
                    @break
                    @case (6) style="background-color: #000000;" {{-- NEGRO --}}
                    @break
                    @case (7) style="background-color: #6c757d;" {{-- GRIS --}}
                    @break
                    @default style="background-color: #FFFFFF;" {{-- BLANCO --}}
                    @endswitch>{{$movimientos[$i]["indice"]}}</td>
                    <td align='center'>{{$movimientos[$i]["name"]}}</td>
                    <td align='center'>{{$movimientos[$i]["id_contrato"]}}</td>
                    <td align='center'>{{$movimientos[$i]["nombre"]}}</td>
                    <td align='center'>{{$movimientos[$i]["abono"]}}</td>
                    @switch($movimientos[$i]["metodopago"])
                        @case(0)
                            <td align='center'>EFECTIVO</td>
                            @break
                        @case(1)
                            <td align='center'>TARJETA</td>
                            @break
                        @case(2)
                            <td align='center'>TRANSFERENCIA</td>
                            @break
                        @case(3)
                            <td align='center'>CANCELACIÓN</td>
                            @break
                    @endswitch
                    <td align='center'>{{$movimientos[$i]["folio"]}}</td>
                    @switch($movimientos[$i]["pago"])
                        @case(0)
                            <td align='center'>CONTADO</td>
                            @break
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
                    <td align='center'>{{$movimientos[$i]["created_at"]}}</td>
                    <td align='center'> <a href="{{route('vercontrato',[$idFranquicia,$movimientos[$i]["id_contrato"]])}}" target="_blank" class="btn btn-primary btn-sm">ABRIR</a></td>
                </tr>
            @endfor
    @else
        <td align='center' style="width: 20%;" colspan="10">Sin registros</td>
    @endif
    </tbody>
</table>

