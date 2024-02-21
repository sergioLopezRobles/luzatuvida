@if($numTotalContratos != null)
    <div class="row" style="margin-top: 5px; margin-bottom: 5px;">
        <div class="col-6">
            <div @if(sizeof($cuentasLocalidad) < 6) style="margin-right: 10px;" @else style="margin-right: 10px; max-height: 300px; overflow-y: scroll;" @endif>
                <table class="table table-bordered table-striped table-sm" style="text-align: center; position: relative; border-collapse: collapse;" id="tablaCuentasLocalidad">
                    <thead>
                    <tr>
                        <th style=" text-align:center; font-size: 12px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col" colspan="2"><b>CUENTAS ACTIVAS POR LOCALIDAD</b></th>
                    </tr>
                    <tr>
                        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">LOCALIDAD</th>
                        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">TOTAL</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($cuentasLocalidad as $i => $cuentaLocalidad)
                        <tr>
                            <td align='center' style="font-size: 10px;">{{$i}}</td>
                            <td align='center' style="font-size: 10px;">{{$cuentaLocalidad}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-6">
            <div @if(sizeof($cuentasColonia) < 5) style="margin-left: 10px;" @else style="margin-left: 10px; max-height: 300px; overflow-y: scroll;" @endif>
                <table class="table table-bordered table-striped table-sm" style="text-align: center; position: relative; border-collapse: collapse;" id="tablaCuentasColonia">
                    <thead>
                    <tr>
                        <th style=" text-align:center; font-size: 12px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col" colspan="2"><b>CUENTAS ACTIVAS POR COLONIA</b></th>
                    </tr>
                    <tr>
                        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">LOCALIDAD</th>
                        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">COLONIA</th>
                        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">TOTAL</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($cuentasColonia as $cuentaColonia)
                        <tr>
                            <td align='center' style="font-size: 10px;">{{$cuentaColonia->LOCALIDAD}}</td>
                            <td align='center' style="font-size: 10px;">{{$cuentaColonia->COLONIA}}</td>
                            <td align='center' style="font-size: 10px;">{{$cuentaColonia->CUENTAS}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px;">
        <div class="col-4">
            <button type="button" class="btn btn-primary" style="margin-bottom: 10px;">
                Total registros <span class="badge bg-secondary">{{$numTotalContratos}}</span>
            </button>
            <a href="#" id="btnExportarExcel" onclick="exportarAExcel('Tabla Cuentas Activas','tablaCuentasActivas');" style="text-decoration:none; color:black; padding-left: 15px;">
                <button type="button" class="btn btn-success" style="margin-bottom: 10px;" > Exportar </button>
            </a>
        </div>
        <div class="col-6"></div>
        <div class="col-2">
            <i class="fa-solid fa-location-dot" data-toggle="modal" data-target="#modalgooglemaps" style="cursor: pointer" id="btnCrearMarcadores"
               onclick="crearMarcadoresGoogleMaps('{{json_encode($contratosaprobados)}}', '{{json_encode($contratosmanofactura)}}', '{{json_encode($contratosprocesoaprobacion)}}',
                   '{{json_encode($contratosenviados)}}', '{{json_encode($contratosentregados)}}', '{{json_encode($contratosatrasados)}}');">Ver Mapa</i>
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
@endif
    <table class="table-bordered table-striped table-sm" id="tablaCuentasActivas" style="width: 100%; margin-top: 20px;">
        <thead>
        <tr>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">FECHA VENTA</th>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">FECHA ENTREGA PREVISTA</th>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">CONTRATO</th>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">ESTADO</th>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">LOCALIDAD</th>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">ENTRE CALLES</th>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">COLONIA</th>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">CALLE</th>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">NUMERO</th>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">NOMBRE</th>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">TELEFONO</th>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">ZONA</th>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">TOTAL</th>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">ULTIMO ABONO</th>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">FORMA PAGO</th>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">PERIODO</th>
            <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">UBICACIÓN</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th style="text-align:center; background-color:#0AA09E; color:#FFFFFF; font-size: 10px; position: sticky; top: 50px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="17">APROBADOS
            </th>
        </tr>
        @foreach($contratosaprobados as $contratoaprobado)
            <tr>
                <td align='center' style="font-size: 10px;">{{$contratoaprobado->FECHAVENTA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoaprobado->FECHAENTREGAHISTORIAL}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoaprobado->CONTRATO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoaprobado->ESTATUS}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoaprobado->LOCALIDAD}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoaprobado->ENTRECALLES}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoaprobado->COLONIA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoaprobado->CALLE}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoaprobado->NUMERO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoaprobado->NOMBRE}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoaprobado->TELEFONO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoaprobado->ZONA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoaprobado->TOTAL}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoaprobado->ULTIMOABONO}}</td>
                @switch($contratoaprobado->FORMAPAGO)
                    @case(0)
                    <td align='center' style="font-size: 10px;">CONTADO</td>
                    @break
                    @case(1)
                    <td align='center' style="font-size: 10px;">SEMANAL</td>
                    @break
                    @case(2)
                    <td align='center' style="font-size: 10px;">QUINCENAL</td>
                    @break
                    @case(4)
                    <td align='center' style="font-size: 10px;">MENSUAL</td>
                    @break
                @endswitch
                <td align='center' style="font-size: 10px;">@if($contratoaprobado->PERIODOINI != null && $contratoaprobado->PERIODOFIN != null) {{$contratoaprobado->PERIODOINI}} - {{$contratoaprobado->PERIODOFIN}} @endif</td>
                @if (($contratoaprobado->COORDENADAS)!= null)
                    <td align='center' style="font-size: 10px;">{{$contratoaprobado->COORDENADAS}}</td>
                @else
                    <td align='center' style="font-size: 10px;">Sin Capturar</td>
                @endif
            </tr>
        @endforeach
        <tr>
            <th style="text-align:center; background-color:#0AA09E; color:#FFFFFF; font-size: 10px; position: sticky; top: 50px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                colspan="17">MANOFACTURA/EN PROCESO DE ENVIO
            </th>
        </tr>
        @foreach($contratosmanofactura as $contratomanofactura)
            <tr>
                <td align='center' style="font-size: 10px;">{{$contratomanofactura->FECHAVENTA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratomanofactura->FECHAENTREGAHISTORIAL}}</td>
                <td align='center' style="font-size: 10px;">{{$contratomanofactura->CONTRATO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratomanofactura->ESTATUS}}</td>
                <td align='center' style="font-size: 10px;">{{$contratomanofactura->LOCALIDAD}}</td>
                <td align='center' style="font-size: 10px;">{{$contratomanofactura->ENTRECALLES}}</td>
                <td align='center' style="font-size: 10px;">{{$contratomanofactura->COLONIA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratomanofactura->CALLE}}</td>
                <td align='center' style="font-size: 10px;">{{$contratomanofactura->NUMERO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratomanofactura->NOMBRE}}</td>
                <td align='center' style="font-size: 10px;">{{$contratomanofactura->TELEFONO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratomanofactura->ZONA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratomanofactura->TOTAL}}</td>
                <td align='center' style="font-size: 10px;">{{$contratomanofactura->ULTIMOABONO}}</td>
                @switch($contratomanofactura->FORMAPAGO)
                    @case(0)
                    <td align='center' style="font-size: 10px;">CONTADO</td>
                    @break
                    @case(1)
                    <td align='center' style="font-size: 10px;">SEMANAL</td>
                    @break
                    @case(2)
                    <td align='center' style="font-size: 10px;">QUINCENAL</td>
                    @break
                    @case(4)
                    <td align='center' style="font-size: 10px;">MENSUAL</td>
                    @break
                @endswitch
                <td align='center' style="font-size: 10px;">@if($contratomanofactura->PERIODOINI != null && $contratomanofactura->PERIODOFIN != null) {{$contratomanofactura->PERIODOINI}} - {{$contratomanofactura->PERIODOFIN}} @endif</td>
                @if (($contratomanofactura->COORDENADAS)!= null)
                    <td align='center' style="font-size: 10px;">{{$contratomanofactura->COORDENADAS}}</td>
                @else
                    <td align='center' style="font-size: 10px;">Sin Capturar</td>
                @endif
            </tr>
        @endforeach
        <tr>
            <th style="text-align:center; background-color:#0AA09E; color:#FFFFFF; font-size: 10px; position: sticky; top: 50px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="17">EN
                PROCESO DE APROBACION
            </th>
        </tr>
        @foreach($contratosprocesoaprobacion as $contratoprocesoaprobacion)
            <tr>
                <td align='center' style="font-size: 10px;">{{$contratoprocesoaprobacion->FECHAVENTA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoprocesoaprobacion->FECHAENTREGAHISTORIAL}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoprocesoaprobacion->CONTRATO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoprocesoaprobacion->ESTATUS}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoprocesoaprobacion->LOCALIDAD}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoprocesoaprobacion->ENTRECALLES}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoprocesoaprobacion->COLONIA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoprocesoaprobacion->CALLE}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoprocesoaprobacion->NUMERO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoprocesoaprobacion->NOMBRE}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoprocesoaprobacion->TELEFONO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoprocesoaprobacion->ZONA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoprocesoaprobacion->TOTAL}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoprocesoaprobacion->ULTIMOABONO}}</td>
                @switch($contratoprocesoaprobacion->FORMAPAGO)
                    @case(0)
                    <td align='center' style="font-size: 10px;">CONTADO</td>
                    @break
                    @case(1)
                    <td align='center' style="font-size: 10px;">SEMANAL</td>
                    @break
                    @case(2)
                    <td align='center' style="font-size: 10px;">QUINCENAL</td>
                    @break
                    @case(4)
                    <td align='center' style="font-size: 10px;">MENSUAL</td>
                    @break
                @endswitch
                <td align='center' style="font-size: 10px;">@if($contratoprocesoaprobacion->PERIODOINI != null && $contratoprocesoaprobacion->PERIODOFIN != null) {{$contratoprocesoaprobacion->PERIODOINI}} - {{$contratoprocesoaprobacion->PERIODOFIN}} @endif</td>
                @if (($contratoprocesoaprobacion->COORDENADAS)!= null)
                    <td align='center' style="font-size: 10px;">{{$contratoprocesoaprobacion->COORDENADAS}}</td>
                @else
                    <td align='center' style="font-size: 10px;">Sin Capturar</td>
                @endif
            </tr>
        @endforeach
        <tr>
            <th style="text-align:center; background-color:#0AA09E; color:#FFFFFF; font-size: 10px; position: sticky; top: 50px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="17">ENVIADOS
            </th>
        </tr>
        @foreach($contratosenviados as $contratoenviado)
            <tr>
                <td align='center' style="font-size: 10px;">{{$contratoenviado->FECHAVENTA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoenviado->FECHAENTREGAHISTORIAL}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoenviado->CONTRATO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoenviado->ESTATUS}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoenviado->LOCALIDAD}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoenviado->ENTRECALLES}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoenviado->COLONIA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoenviado->CALLE}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoenviado->NUMERO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoenviado->NOMBRE}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoenviado->TELEFONO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoenviado->ZONA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoenviado->TOTAL}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoenviado->ULTIMOABONO}}</td>
                @switch($contratoenviado->FORMAPAGO)
                    @case(0)
                    <td align='center' style="font-size: 10px;">CONTADO</td>
                    @break
                    @case(1)
                    <td align='center' style="font-size: 10px;">SEMANAL</td>
                    @break
                    @case(2)
                    <td align='center' style="font-size: 10px;">QUINCENAL</td>
                    @break
                    @case(4)
                    <td align='center' style="font-size: 10px;">MENSUAL</td>
                    @break
                @endswitch
                <td align='center' style="font-size: 10px;">@if($contratoenviado->PERIODOINI != null && $contratoenviado->PERIODOFIN != null) {{$contratoenviado->PERIODOINI}} - {{$contratoenviado->PERIODOFIN}} @endif</td>
                @if (($contratoenviado->COORDENADAS)!= null)
                    <td align='center' style="font-size: 10px;">{{$contratoenviado->COORDENADAS}}</td>
                @else
                    <td align='center' style="font-size: 10px;">Sin Capturar</td>
                @endif
            </tr>
        @endforeach
        <tr>
            <th style="text-align:center; background-color:#0AA09E; color:#FFFFFF; font-size: 10px; position: sticky; top: 50px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="17">ENTREGADOS
            </th>
        </tr>
        @foreach($contratosentregados as $contratoentregado)
            <tr>
                <td align='center' style="font-size: 10px;">{{$contratoentregado->FECHAVENTA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoentregado->FECHAENTREGAHISTORIAL}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoentregado->CONTRATO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoentregado->ESTATUS}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoentregado->LOCALIDAD}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoentregado->ENTRECALLES}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoentregado->COLONIA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoentregado->CALLE}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoentregado->NUMERO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoentregado->NOMBRE}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoentregado->TELEFONO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoentregado->ZONA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoentregado->TOTAL}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoentregado->ULTIMOABONO}}</td>
                @switch($contratoentregado->FORMAPAGO)
                    @case(0)
                    <td align='center' style="font-size: 10px;">CONTADO</td>
                    @break
                    @case(1)
                    <td align='center' style="font-size: 10px;">SEMANAL</td>
                    @break
                    @case(2)
                    <td align='center' style="font-size: 10px;">QUINCENAL</td>
                    @break
                    @case(4)
                    <td align='center' style="font-size: 10px;">MENSUAL</td>
                    @break
                @endswitch
                <td align='center' style="font-size: 10px;">@if($contratoentregado->PERIODOINI != null && $contratoentregado->PERIODOFIN != null){{$contratoentregado->PERIODOINI}} - {{$contratoentregado->PERIODOFIN}} @endif</td>
                @if (($contratoentregado->COORDENADAS)!= null)
                    <td align='center' style="font-size: 10px;">{{$contratoentregado->COORDENADAS}}</td>
                @else
                    <td align='center' style="font-size: 10px;">Sin Capturar</td>
                @endif
            </tr>
        @endforeach
        <tr>
            <th style="text-align:center; background-color:#0AA09E; color:#FFFFFF; font-size: 10px; position: sticky; top: 50px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="17">ATRASADOS
            </th>
        </tr>
        @foreach($contratosatrasados as $contratoatrasado)
            <tr>
                <td align='center' style="font-size: 10px;">{{$contratoatrasado->FECHAVENTA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoatrasado->FECHAENTREGAHISTORIAL}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoatrasado->CONTRATO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoatrasado->ESTATUS}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoatrasado->LOCALIDAD}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoatrasado->ENTRECALLES}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoatrasado->COLONIA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoatrasado->CALLE}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoatrasado->NUMERO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoatrasado->NOMBRE}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoatrasado->TELEFONO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoatrasado->ZONA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoatrasado->TOTAL}}</td>
                <td align='center' style="font-size: 10px;">{{$contratoatrasado->ULTIMOABONO}}</td>
                @switch($contratoatrasado->FORMAPAGO)
                    @case(0)
                    <td align='center' style="font-size: 10px;">CONTADO</td>
                    @break
                    @case(1)
                    <td align='center' style="font-size: 10px;">SEMANAL</td>
                    @break
                    @case(2)
                    <td align='center' style="font-size: 10px;">QUINCENAL</td>
                    @break
                    @case(4)
                    <td align='center' style="font-size: 10px;">MENSUAL</td>
                    @break
                @endswitch
                <td align='center' style="font-size: 10px;">@if($contratoatrasado->PERIODOINI != null && $contratoatrasado->PERIODOFIN != null) {{$contratoatrasado->PERIODOINI}} - {{$contratoatrasado->PERIODOFIN}} @endif</td>
                @if (($contratoatrasado->COORDENADAS)!= null)
                    <td align='center' style="font-size: 10px;">{{$contratoatrasado->COORDENADAS}}</td>
                @else
                    <td align='center' style="font-size: 10px;">Sin Capturar</td>
                @endif
            </tr>
        @endforeach
        <tr>
            <th style="text-align:center; background-color:#0AA09E; color:#FFFFFF; font-size: 10px; position: sticky; top: 50px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" colspan="17">OTROS</th>
        </tr>
        @foreach($contratosotros as $contratootro)
            <tr>
                <td align='center' style="font-size: 10px;">{{$contratootro->FECHAVENTA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratootro->FECHAENTREGAHISTORIAL}}</td>
                <td align='center' style="font-size: 10px;">{{$contratootro->CONTRATO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratootro->ESTATUS}}</td>
                <td align='center' style="font-size: 10px;">{{$contratootro->LOCALIDAD}}</td>
                <td align='center' style="font-size: 10px;">{{$contratootro->ENTRECALLES}}</td>
                <td align='center' style="font-size: 10px;">{{$contratootro->COLONIA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratootro->CALLE}}</td>
                <td align='center' style="font-size: 10px;">{{$contratootro->NUMERO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratootro->NOMBRE}}</td>
                <td align='center' style="font-size: 10px;">{{$contratootro->TELEFONO}}</td>
                <td align='center' style="font-size: 10px;">{{$contratootro->ZONA}}</td>
                <td align='center' style="font-size: 10px;">{{$contratootro->TOTAL}}</td>
                <td align='center' style="font-size: 10px;">{{$contratootro->ULTIMOABONO}}</td>
                @switch($contratootro->FORMAPAGO)
                    @case(0)
                        <td align='center' style="font-size: 10px;">CONTADO</td>
                        @break
                    @case(1)
                        <td align='center' style="font-size: 10px;">SEMANAL</td>
                        @break
                    @case(2)
                        <td align='center' style="font-size: 10px;">QUINCENAL</td>
                        @break
                    @case(4)
                        <td align='center' style="font-size: 10px;">MENSUAL</td>
                        @break
                @endswitch
                <td align='center' style="font-size: 10px;">@if($contratootro->PERIODOINI != null && $contratootro->PERIODOFIN != null) {{$contratootro->PERIODOINI}} - {{$contratootro->PERIODOFIN}} @endif</td>
                @if (($contratootro->COORDENADAS)!= null)
                    <td align='center' style="font-size: 10px;">{{$contratootro->COORDENADAS}}</td>
                @else
                    <td align='center' style="font-size: 10px;">Sin Capturar</td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>

