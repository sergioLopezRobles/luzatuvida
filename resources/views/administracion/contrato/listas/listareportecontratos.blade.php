@php
    $i = 0;
@endphp

@foreach($franquicias as $franquicia)

    <div class="row" style="margin-top: 20px;">
        <div class="col-12" style="text-align: center; margin-bottom: 10px;"><h5><b>REPORTE DE VENTAS {{$franquicia->ciudad}}</b></h5></div>
        <div class="col-1"></div>
        <div class="col-10">
            <table class="table table-bordered table-striped table-sm" style="text-align: center; position: relative; border-collapse: collapse;">
                <thead>
                <tr>
                    <th style=" text-align:center; font-size: 12px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col" colspan="9"><b>VENTAS DE OPTOMETRISTA</b></th>
                </tr>
                <tr>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">OPTOMETRISTA</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">LUNES</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">MARTES</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">MIERCOLES</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">JUEVES</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">VIERNES</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">SABADO</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">TOTAL</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">RECHAZADOS/<br>CANCELADOS</th>
                </tr>
                </thead>
                <tbody>
                @foreach($ventasPorFranquiciaOptometrsitas[$i] as $ventasPorFranquiciaOptometrsita)
                    @if(($ventasPorFranquiciaOptometrsita->totalVentas) > 0 || ($ventasPorFranquiciaOptometrsita->ventasRechazadas) > 0)
                        <tr>
                            <td align='center' style="font-size: 10px;">{{$ventasPorFranquiciaOptometrsita-> name}}</td>
                            <td align='center' style="font-size: 10px;">{{$ventasPorFranquiciaOptometrsita->ventasLunes}}</td>
                            <td align='center' style="font-size: 10px;">{{$ventasPorFranquiciaOptometrsita->ventasMartes}}</td>
                            <td align='center' style="font-size: 10px;">{{$ventasPorFranquiciaOptometrsita->ventasMiercoles}}</td>
                            <td align='center' style="font-size: 10px;">{{$ventasPorFranquiciaOptometrsita->ventasJueves}}</td>
                            <td align='center' style="font-size: 10px;">{{$ventasPorFranquiciaOptometrsita->ventasViernes}}</td>
                            <td align='center' style="font-size: 10px;">{{$ventasPorFranquiciaOptometrsita->ventasSabado}}</td>
                            <th align='center' style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">{{$ventasPorFranquiciaOptometrsita->totalVentas}}</th>
                            <td align='center' style="font-size: 10px; background:#ea9999">{{$ventasPorFranquiciaOptometrsita->ventasRechazadas}}</td>
                        </tr>
                    @endif
                @endforeach
                    <tr>
                        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">TOTAL</th>
                        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">{{$totalVentasOptometristas[$i][0]}}</th>
                        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">{{$totalVentasOptometristas[$i][1]}}</th>
                        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">{{$totalVentasOptometristas[$i][2]}}</th>
                        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">{{$totalVentasOptometristas[$i][3]}}</th>
                        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">{{$totalVentasOptometristas[$i][4]}}</th>
                        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">{{$totalVentasOptometristas[$i][5]}}</th>
                        <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col"></th>
                        <th style=" text-align:center; font-size: 10px; background:#ea9999; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">{{$totalVentasOptometristas[$i][6]}}</th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-1"> </div>
        <div class="col-1"></div>
        <div class="col-10">
            <table class="table table-bordered table-striped table-sm" style="text-align: center; position: relative; border-collapse: collapse;">
                <thead>
                <tr>
                    <th style=" text-align:center; font-size: 12px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col" colspan="9"><b>VENTAS DE ASISTENTE</b></th>
                </tr>
                <tr>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">ASISTENTE</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">LUNES</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">MARTES</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">MIERCOLES</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">JUEVES</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">VIERNES</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">SABADO</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">TOTAL</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">RECHAZADOS/<br>CANCELADOS</th>
                </tr>
                </thead>
                <tbody>
                @foreach($ventasPorFranquiciaAsistentes[$i] as $ventasPorFranquiciaAsistente)
                    @if(($ventasPorFranquiciaAsistente -> totalVentas) > 0 ||($ventasPorFranquiciaAsistente -> ventasRechazadas) > 0 )
                        <tr>
                            <td align='center' style="font-size: 10px;">{{$ventasPorFranquiciaAsistente-> name}}</td>
                            <td align='center' style="font-size: 10px;">{{$ventasPorFranquiciaAsistente->ventasLunes}}</td>
                            <td align='center' style="font-size: 10px;">{{$ventasPorFranquiciaAsistente->ventasMartes}}</td>
                            <td align='center' style="font-size: 10px;">{{$ventasPorFranquiciaAsistente->ventasMiercoles}}</td>
                            <td align='center' style="font-size: 10px;">{{$ventasPorFranquiciaAsistente->ventasJueves}}</td>
                            <td align='center' style="font-size: 10px;">{{$ventasPorFranquiciaAsistente->ventasViernes}}</td>
                            <td align='center' style="font-size: 10px;">{{$ventasPorFranquiciaAsistente->ventasSabado}}</td>
                            <th align='center' style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">{{$ventasPorFranquiciaAsistente->totalVentas}}</th>
                            <td align='center' style="font-size: 10px; background:#ea9999">{{$ventasPorFranquiciaAsistente->ventasRechazadas}}</td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">TOTAL</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">{{$totalVentasAsistentes[$i][0]}}</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">{{$totalVentasAsistentes[$i][1]}}</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">{{$totalVentasAsistentes[$i][2]}}</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">{{$totalVentasAsistentes[$i][3]}}</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">{{$totalVentasAsistentes[$i][4]}}</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">{{$totalVentasAsistentes[$i][5]}}</th>
                    <th style=" text-align:center; font-size: 10px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col"></th>
                    <th style=" text-align:center; font-size: 10px; background:#ea9999; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col">{{$totalVentasAsistentes[$i][6]}}</th>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    @php
        $i = $i + 1;
    @endphp
@endforeach
