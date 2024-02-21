<div class="row" style="padding-top: 10px;">
    <div class="col-4">
        <button type="button" class="btn btn-primary" style="margin-bottom: 10px;">
            Total registros <span class="badge bg-secondary">{{$numTotalContratos}}</span>
        </button>
        @if(count($contratosPagados)>0)
            <a href="#" id="btnExportarExcel" onclick="exportarAExcel('Reporte Pagados','tablaReportePagados');" style="text-decoration:none; color:black; padding-left: 15px;">
                <button type="button" class="btn btn-success" style="margin-bottom: 10px;" > Exportar </button>
            </a>
        @endif
    </div>
    <div class="col-6"></div>
    <div class="col-2">
        <i class="fa-solid fa-location-dot" data-toggle="modal" data-target="#modalgooglemaps" style="cursor: pointer" id="btnCrearMarcadores"
           onclick="crearMarcadoresReportes('{{json_encode($contratosPagados)}}')";>Ver Mapa</i>
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
                <div id="simbologia" style="background-color: white; margin-right: 20px; margin-top: 60px; font-size: 12px; font-weight: bold;text-align: center;">Información</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-success" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="contenedortblReportes" style="max-height: 600px; overflow-y: auto; width: 100%; overflow-x: auto; margin-top: 20px;">
    <table class="table table-bordered table-striped table-general table-sm" id="tablaReportePagados">
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
            <th scope="col">ULTIMO ABONO</th>
            <th scope="col">FORMA PAGO</th>
            <th scope="col">UBICACIÓN</th>
        </tr>
        </thead>
        <tbody>
        @if(!is_null($contratosPagados) && count($contratosPagados)>0)
            @foreach($contratosPagados as $contratopagado)
                <tr>
                    <td align='center'>{{$contratopagado->FECHAVENTA}}</td>
                    <td align='center'>{{$contratopagado->FECHAENTREGA}}</td>
                    <td align='center'>{{$contratopagado->CONTRATO}}</td>
                    <td align='center'>{{$contratopagado->ESTATUS}}</td>
                    <td align='center'>{{$contratopagado->LOCALIDAD}}</td>
                    <td align='center'>{{$contratopagado->ENTRECALLES}}</td>
                    <td align='center'>{{$contratopagado->COLONIA}}</td>
                    <td align='center'>{{$contratopagado->CALLE}}</td>
                    <td align='center'>{{$contratopagado->NUMERO}}</td>
                    <td align='center'>{{$contratopagado->NOMBRE}}</td>
                    <td align='center'>{{$contratopagado->TELEFONO}}</td>
                    <td align='center'>{{$contratopagado->ZONA}}</td>
                    <td align='center'>{{$contratopagado->TOTAL}}</td>
                    <td align='center'>{{$contratopagado->ULTIMOABONO}}</td>
                    @switch($contratopagado->FORMAPAGO)
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
                    @if (($contratopagado->COORDENADAS)!= null)
                        <td align='center'>{{$contratopagado->COORDENADAS}}</td>
                    @else
                        <td align='center'>Sin Capturar</td>
                    @endif
                </tr>
            @endforeach
        @endif
        @if(count($contratosPagados) == 0)
            <tr>
                <th style="text-align: center;" colspan="16">SIN REGISTROS
                </th>
            </tr>
        @endif
        </tbody>
    </table>
</div>
