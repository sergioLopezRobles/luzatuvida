<div class="row" style="padding-top: 10px;">
    <div class="col-4">
        <button type="button" class="btn btn-primary" style="margin-bottom: 10px;">
            Total registros <span class="badge bg-secondary">{{$numTotalContratos}}</span>
        </button>
        @if(count($contratosCancelados)>0)
            <a href="#" id="btnExportarExcel" onclick="exportarAExcel('Reporte Cancelados','tablaReporteCancelado');" style="text-decoration:none; color:black; padding-left: 15px;">
                <button type="button" class=" btn btn-success" style="margin-bottom: 10px;"> Exportar </button>
            </a>
        @endif
    </div>
    <div class="col-6"></div>
    <div class="col-2">
        <i class="fa-solid fa-location-dot" data-toggle="modal" data-target="#modalgooglemaps" style="cursor: pointer" id="btnCrearMarcadores"
           onclick="crearMarcadoresReportes('{{json_encode($contratosCancelados)}}')";>Ver Mapa</i>
    </div>
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
<div class="contenedortblReportes" style="max-height: 600px; overflow-y: auto; width: 100%; overflow-x: auto; margin-top: 20px;">
    <table class="table-bordered table-striped table-general table-sm" id="tablaReporteCancelado">
        <thead>
        <tr>
            <th scope="col">FECHA VENTA</th>
            <th scope="col">FECHA ENTREGA PREVISTA</th>
            <th scope="col">CONTRATO</th>
            <th scope="col">ESTADO</th>
            <th scope="col">LOCALIDAD</th>
            <th scope="col">ENTRE CALLES</th>
            <th scope="col">COLONIA</th>
            <th scope="col">CALLE</th>
            <th scope="col">NUMERO</th>
            <th scope="col">NOMBRE</th>
            <th scope="col">TELEFONO</th>
            <th scope="col">ZONA</th>
            <th scope="col">TOTAL</th>
            <th scope="col">FECHA CANCELADO</th>
            <th scope="col">FORMA PAGO</th>
            <th scope="col">UBICACIÓN</th>
        </tr>
        </thead>
        <tbody>
        @if(!is_null($contratosCancelados) && count($contratosCancelados)>0)
            @foreach($contratosCancelados as $contratoCancelado)
                <tr>
                    <td align='center'>{{$contratoCancelado->FECHAVENTA}}</td>
                    <td align='center'>{{$contratoCancelado->FECHAENTREGA}}</td>
                    <td align='center'>{{$contratoCancelado->CONTRATO}}</td>
                    <td align='center'>{{$contratoCancelado->ESTATUS}}</td>
                    <td align='center'>{{$contratoCancelado->LOCALIDAD}}</td>
                    <td align='center'>{{$contratoCancelado->ENTRECALLES}}</td>
                    <td align='center'>{{$contratoCancelado->COLONIA}}</td>
                    <td align='center'>{{$contratoCancelado->CALLE}}</td>
                    <td align='center'>{{$contratoCancelado->NUMERO}}</td>
                    <td align='center'>{{$contratoCancelado->NOMBRE}}</td>
                    <td align='center'>{{$contratoCancelado->TELEFONO}}</td>
                    <td align='center'>{{$contratoCancelado->ZONA}}</td>
                    <td align='center'>{{$contratoCancelado->TOTAL}}</td>
                    <td align='center'>{{$contratoCancelado->FECHACANCELACION}}</td>
                    @switch($contratoCancelado->FORMAPAGO)
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
                    @if (($contratoCancelado->COORDENADAS)!= null)
                        <td align='center'>{{$contratoCancelado->COORDENADAS}}</td>
                    @else
                        <td align='center'>Sin Capturar</td>
                    @endif
                </tr>
            @endforeach
        @endif
        @if(count($contratosCancelados) == 0)
            <tr>
                <th style="text-align: center;" colspan="16">SIN REGISTROS
                </th>
            </tr>
        @endif
        </tbody>
    </table>
</div>
