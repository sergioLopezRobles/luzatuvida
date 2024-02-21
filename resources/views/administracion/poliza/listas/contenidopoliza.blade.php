<!--<div class="row">
    <div class="col-10"></div>
    <div class="col-2">
        <a href="#" id="exportarBtn" onclick="exportarPolizaExcel('Poliza{{$fecha}}.xlsx');" style="text-decoration:none; color:black; padding-left: 15px;">
            <button type="button" class="btn btn-success" style="margin-bottom: 10px;" >Exportar a excel</button>
        </a>
    </div>
</div>-->
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab"
           aria-controls="general"
           aria-selected="true">GENERAL</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="ingresosoficina-tab" data-toggle="tab" href="#ingresosoficina" role="tab"
           aria-controls="ingresosoficina" aria-selected="false">INGRESOS OFICINA</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="ingresosventas-tab" data-toggle="tab" href="#ingresosventas" role="tab"
           aria-controls="ingresosventas" aria-selected="false">INGRESOS VENTAS</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="productividad-tab" data-toggle="tab" href="#productividad" role="tab"
           aria-controls="productividad" aria-selected="false">PRODUCTIVIDAD</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="cobranza-tab" data-toggle="tab" href="#cobranza" role="tab" aria-controls="cobranza"
           aria-selected="false">COBRANZA</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="gastos-tab" data-toggle="tab" href="#gastos" role="tab" aria-controls="gastos"
           aria-selected="false">GASTOS</a>
    </li>
    @if(Auth::user()->rol_id == 7)
        <li class="nav-item">
            <a class="nav-link" id="historial-tab" data-toggle="tab" href="#historial" role="tab"
               aria-controls="historial"
               aria-selected="false">HISTORIAL</a>
        </li>
    @endif
</ul>
<div class="tab-content" style="margin-top:30px;">
    <div class="tab-pane active" id="general" role="tabpanel" aria-labelledby="general-tab">
        <div class="col-12">
            <form action="{{route('agregarObservacion',[$idFranquicia,$idPoliza])}}" enctype="multipart/form-data"
                  method="POST" onsubmit="btnSubmit.disabled = true;">
                @csrf
                <div class="row">
                    <div class="col-10">
                        <div class="form-group">
                            <label>OBSERVACIONES:</label>
                            @if($poliza[0]->observaciones == null)
                                <input type="text" name="observaciones"
                                       class="form-control {!! $errors->first('observaciones','is-invalid')!!}"
                                       placeholder="observaciones" value="{{ old('observaciones') }}">
                                {!! $errors->first('observaciones','<div class="invalid-feedback">La observación es
                                    obligatoria.</div>')!!}
                            @else
                                <input type="text" name="observaciones"
                                       class="form-control {!! $errors->first('observaciones','is-invalid')!!}"
                                       placeholder="observaciones" readonly value="{{ $poliza[0]->observaciones}}">
                                {!! $errors->first('observaciones','<div class="invalid-feedback">La observación es
                                    obligatoria.</div>')!!}
                            @endif
                        </div>
                    </div>
                    <div class="col-2">
                        <!-- <label> &nbsp; </label> -->
                        <div class="form-group">
                            @if($poliza[0]->observaciones == null)
                                <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit" style="margin-top: 30px;">
                                    Agregar
                                </button>
                            @else
                                <label> &nbsp; </label>
                                <button class="btn btn-outline-danger btn-block" name="btnSubmit" type="submit" style="margin-top: 30px;">
                                    Eliminar
                                </button>
                            @endif
                        </div>

                    </div>
                </div>
            </form>
        </div>

        <div class="container">
            <div class="row" style="margin-bottom:20px;">
                <div class="col-4" style="color:#0AA09E">
                    <h6>SALDO ANTERIOR EN CAJA</h6>
                </div>
                <div class="col-4" style="color:#0AA09E">
                    <h6>${{$totalUltimaPoliza}}</h6>
                </div>
            </div>

            <div class="row" style="margin-bottom:20px;">
                <div class="col-4">
                    <h6>INGRESOS ADMINISTRACION</h6>
                </div>
                <div class="col-4">
                    <h6>$ {{$ingresosAdmin}}</h6>
                </div>
            </div>

            <div class="row" style="margin-bottom:20px;">
                <div class="col-4">
                    <h6>INGRESOS VENTAS</h6>
                </div>
                <div class="col-4">
                    <h6>$ {{$ingresosVentas}}</h6>
                </div>
            </div>
            <div class="row" style="margin-bottom:20px;">
                <div class="col-4">
                    <h6>INGRESOS DE COBRANZA</h6>
                </div>
                <div class="col-4">
                    <u>
                        <h6>$ {{$ingresosCobranza}}</h6>
                    </u>
                </div>
            </div>
            <hr>
            <div class="row" style="margin-left:10%;margin-bottom:30px;">
                <div class="col-3" style="color:#0AA09E">
                    <h5>TOTAL INGRESOS</h5>
                </div>
                <div class="col-4" style="color:#0AA09E">
                    <h3>$ {{$totalUltimaPoliza+$ingresosAdmin+$ingresosVentas+$ingresosCobranza}}</h3>
                </div>
            </div>

            <div class="row" style="margin-bottom:20px;">
                <div class="col-4">
                    <h6>GASTOS ADMINISTRACION</h6>
                </div>
                <div class="col-4">
                    <h6>${{$gastosAdmin}}</h6>
                </div>
            </div>
            <div class="row" style="margin-bottom:20px;">
                <div class="col-4">
                    <h6>GASTOS VENTAS</h6>
                </div>
                <div class="col-4">
                    <h6>$ {{$gastoVentas}}</h6>
                </div>
            </div>
            <div class="row" style="margin-bottom:20px;">
                <div class="col-4">
                    <h6>GASTOS COBRANZA</h6>
                </div>
                <div class="col-4">
                    <h6>$ {{$gastosCobranza}}</h6>
                </div>
            </div>

            <div class="row" style="margin-bottom:20px;">
                <div class="col-4">
                    <h6>OTROS GASTOS</h6>
                </div>
                <div class="col-4">
                    <u>
                        <h6>$ {{$otrosGastos}}</h6>
                    </u>
                </div>
            </div>
            <hr>
            <div class="row" style="margin-left:10%;margin-bottom:40px;">
                <div class="col-3" style="color:#0AA09E">
                    <h5>TOTAL GASTOS</h5>
                </div>
                <div class="col" style="color:#0AA09E">
                    <h3>$ {{$gastosAdmin+$gastoVentas+$gastosCobranza+$otrosGastos}}</h3>
                </div>
            </div>
            <div class="row" style="margin-bottom:20px;">
                <div class="col-5" style="color:#0AA09E">
                    <h3>REALIZO: {{$poliza[0]->realizo}}</h3>
                </div>
                <div class="col" style="color:#0AA09E">
                    <h3>AUTORIZO: {{$poliza[0]->autorizo}}</h3>
                </div>
            </div>
            <div class="row" style="margin-bottom:20px;">
                <div class="col-5" style="color:#0AA09E">
                    <h3>TOTAL POLIZA</h3>
                </div>
                <div class="col-2" style="background-color:#0AA09E;text-align:center;">
                    <h3 style="color:white; margin-top: 10px;">
                        $ {{($totalUltimaPoliza+$ingresosAdmin+$ingresosVentas+$ingresosCobranza)-($gastosAdmin+$gastoVentas+$gastosCobranza+$otrosGastos)}}</h3>
                </div>
            </div>
            <hr>
        </div>
    </div>
    <div class="tab-pane" id="ingresosoficina" role="tabpanel" aria-labelledby="ingresosoficina-tab">
        <div class="col-12">
            @if($poliza[0]->estatus == 0)
                <div id="accordion">
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Normal
                                </button>
                            </h5>
                        </div>
                        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                            <div class="card-body">
                                <form action="{{route('ingresarOficina',[$idFranquicia,$idPoliza])}}" enctype="multipart/form-data"
                                      method="POST" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label>Descripción:</label>
                                                <input type="text" name="descripcion"
                                                       class="form-control {!! $errors->first('descripcion','is-invalid')!!}"
                                                       placeholder="Descripción" value="{{ old('descripcion') }}">
                                                {!! $errors->first('descripcion','<div class="invalid-feedback">La descripcion es
                                                    obligatoria.</div>')!!}
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Recibo:</label>
                                                <input type="number" name="recibo" min="0"
                                                       class="form-control {!! $errors->first('recibo','is-invalid')!!}"
                                                       placeholder="Recibo"
                                                       value="{{ old('recibo') }}">
                                                {!! $errors->first('recibo','<div class="invalid-feedback">El Recibo es obligatorio.</div>
                                                ')!!}
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label>Foto del recibo:</label>
                                                <input type="file" name="fotorecibo"
                                                       class="form-control-file  {!! $errors->first('fotorecibo','is-invalid')!!}"
                                                       accept="image/jpg">
                                                {!! $errors->first('fotorecibo','<div class="invalid-feedback">obligatorio
                                                </div>')!!}
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Monto:</label>
                                                <input type="number" name="monto" min="0"
                                                       class="form-control {!! $errors->first('monto','is-invalid')!!}"
                                                       placeholder="Monto"
                                                       value="{{ old('monto') }}">
                                                {!! $errors->first('monto','<div class="invalid-feedback">El monto es obligatorio.</div>
                                                ')!!}
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <!-- <label> &nbsp; </label> -->
                                            <div class="form-group">
                                                <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit" style="margin-top: 30px;">
                                                    Agregar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div id="accordion">
                    <div class="card">
                        <div class="card-header" id="headingTwo">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                    Venta de producto
                                </button>
                            </h5>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                            <div class="card-body">
                                <form action="{{route('ingresooficinaproducto',[$idFranquicia,$idPoliza])}}" enctype="multipart/form-data"
                                      method="POST" onsubmit="btnSubmit.disabled = true;">
                                    @csrf
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label>Elegir un producto</label>
                                                <select name="producto"
                                                        class="form-control"
                                                        placeholder="producto" value="{{ old('producto',0) }}" id="producto">
                                                    <option selected value='nada'>Seleccionar</option>
                                                    @foreach($productos as $pro)
                                                        @if($pro->preciop == null)
                                                            @if($pro->id_tipoproducto == 1)
                                                                <option value="{{$pro->id}}">{{$pro->nombre}} | {{$pro->color}} | $ {{ $pro->precio }}
                                                                    | {{$pro->piezas}}pza.
                                                                </option>
                                                            @else
                                                                <option value="{{$pro->id}}">{{$pro->nombre}} | $ {{ $pro->precio }}
                                                                    | {{$pro->piezas}}pza.
                                                                </option>
                                                            @endif
                                                        @else
                                                            @if($pro->id_tipoproducto == 1)
                                                                <option value="{{$pro->id}}">{{$pro->nombre}} | {{$pro->color}} | Normal :
                                                                    $ {{ $pro->precio }} | Con
                                                                    descuento: $ {{ $pro->preciop }} | {{$pro->piezas}}pza.
                                                            @else
                                                                <option value="{{$pro->id}}">{{$pro->nombre}} | Normal :
                                                                    $ {{ $pro->precio }} | Con
                                                                    descuento: $ {{ $pro->preciop }} | {{$pro->piezas}}pza.
                                                                </option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-1">
                                            <div class="form-group">
                                                <label># Piezas</label>
                                                <input type="number" name="piezas" id="piezas"
                                                       class="form-control" min="1"
                                                       placeholder="Numero de piezas" value="{{ old('piezas', 0) }}">
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="form-group">
                                                <label>Descripción:</label>
                                                <input type="text" name="descripcionproducto"
                                                       class="form-control"
                                                       placeholder="Descripción" value="{{ old('descripcionproducto') }}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit" style="margin-top: 30px;">
                                                    Agregar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <br>
            <table id="tablaHistorialC" class="table-bordered table-striped table-general table-sm tabla-exportar">
                <thead>
                <tr>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">#</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">DESCRIPCIÓN</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">RECIBO</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">FOTO</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">MONTO</th>
                    @if($poliza[0]->estatus == 0)
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">
                            ELIMINAR
                        </th>
                    @endif
                </tr>
                </thead>
                @if(sizeof($ingresos) === 0)
                    <tbody>
                    <tr>
                        <td align='center' colspan="5">Sin registros</td>
                    </tr>
                    </tbody>
                @else
                    <tbody>
                    @foreach($ingresos as $ingreso)
                        <tr>
                            <td align='center'>{{$loop->iteration}}</td>
                            <td>{{$ingreso->descripcion}}</td>
                            <td align='center'>{{$ingreso->numrecibo}}</td>
                            @if(isset($ingreso->foto))
                                <td align='center'>
                                    <div class="col-12" data-toggle="modal" data-target="#imagemodal" style="cursor: pointer" onclick="mostrarfotoingresosoficina('{{$ingreso->id}}')">
                                        <img id="img1{{$ingreso->id}}" src="{{asset($ingreso->foto)}}" class="img-thumbnail"
                                                            style="width:100px;height:65px;">
                                    </div>
                                </td>
                            @else
                                <td align='center'>NA</td>
                            @endif
                            <td align='center'>$ {{$ingreso->monto}}</td>
                            @if($poliza[0]->estatus == 0 && $ingreso->tipo == 0)
                                <td align='center'><a type="button" class="btn btn-danger" style="color:#FEFEFE;"
                                                      href="{{route('eliminarOficina',[$idFranquicia,$ingreso->id_poliza, $ingreso->id])}}">ELIMINAR</a>
                                </td>
                            @else
                                <td align='center'></td>
                            @endif
                        </tr>
                    @endforeach
                    <td align='center' colspan="3"
                        style="text-align:center;background-color:#0AA09E;color:#FFFFFF;"></td>
                    <td align='center' colspan="1" style="color:#0AA09E;">TOTAL</td>
                    <td align='center' colspan="1" style="color:#0AA09E;">$ {{$sumaoficina[0]->suma}}</td>
                    </tbody>
                @endif

            </table>
            <hr>
        </div>

    </div>
    <div class="tab-pane" id="ingresosventas" role="tabpanel" aria-labelledby="ingresosventas-tab">
        <table id="tablaVentas" class="table-bordered table-striped table-general table-sm tabla-exportar" style=" text-align:center;">
            <thead>
            <tr>
                <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col" rowspan="2">
                    OPTOMETRISTAS
                </th>
                <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 0px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                    scope="col" colspan="6">
                    VENTAS
                </th>
                <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col" rowspan="2">
                    VENTAS ACUMULADAS
                </th>
                <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col" rowspan="2">
                    ASISTENCIA
                </th>
                <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col" rowspan="2">
                    INGRESO GOTAS
                </th>
                <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col" rowspan="2">
                    INGRESO DE ENGANCHE
                </th>
                <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col" rowspan="2">
                    INGRESO DE ABONOS
                </th>
                <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col" rowspan="2">
                    INGRESO POLIZA
                </th>
                <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col" rowspan="2">
                    INGRESO VENTAS
                </th>
                <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col" rowspan="2">
                    INGRESO ACUMULADO SEMANA
                </th>
                <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col" rowspan="2">
                    ACCIÓN
                </th>
            </tr>
            <tr>
                <th align='center' colspan=""
                    style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;bordercolor:#0AA09E; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);">Lu
                </th>
                <th align='center' colspan=""
                    style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;bordercolor:#0AA09E; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);">Ma
                </th>
                <th align='center' colspan=""
                    style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;bordercolor:#0AA09E; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);">Mi
                </th>
                <th align='center' colspan=""
                    style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;bordercolor:#0AA09E; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);">Ju
                </th>
                <th align='center' colspan=""
                    style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;bordercolor:#0AA09E; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);">Vi
                </th>
                <th align='center' colspan=""
                    style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;bordercolor:#0AA09E; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);">Sa
                </th>
            </tr>
            </thead>
            <tbody>
            @php
                $totaldiageneral = 0;
                $totalacumuladogeneral = 0;
            @endphp
            @foreach($ventas as $venta)
                <tr>
                    <td align='center'>{{$venta->nombre}}</td>
                    <td align='center' colspan="">{{$venta->lunes}}</td>
                    <td align='center' colspan="">{{$venta->martes}}</td>
                    <td align='center' colspan="">{{$venta->miercoles}}</td>
                    <td align='center' colspan="">{{$venta->jueves}}</td>
                    <td align='center' colspan="">{{$venta->viernes}}</td>
                    <td align='center' colspan="">{{$venta->sabado}}</td>
                    <td align='center' colspan="">{{$venta->acumuladas}}</td>
                    @if($venta->asistencia != null)
                        @switch($venta->asistencia)
                            @case(0)
                                <td align='center' colspan="">F</td>
                                @break
                            @case(1)
                                <td align='center' colspan="">A</td>
                                @break
                            @case(2)
                                <td align='center' colspan="">R</td>
                                @break
                        @endswitch
                    @else
                        <td align='center' colspan="">N/A</td>
                    @endif
                    <td align='center' colspan="">{{$venta->ingresosgotas}}</td>
                    <td align='center' colspan="">{{$venta->ingresosenganche}}</td>
                    <td align='center' colspan="">{{$venta->ingresosabonos}}</td>
                    <td align='center' colspan="">{{$venta->ingresospoliza}}</td>
                    <td align='center' colspan="">{{$venta->ingresosventas}}</td>
                    <td align='center' colspan="">$ {{$venta->ingresosventasacumulado}}</td>
                    <td align='center' colspan="">
                        <a href="#" onclick="abrirmodalinformacion('{{$idFranquicia}}', '{{$venta->id_usuario}}', '{{$venta->id_poliza}}', '{{$venta->nombre}}', '0')"
                           data-toggle="modal" class="btn btn-primary btn-sm">VER</a>
                    </td>
                    @php
                        $totaldiageneral = $totaldiageneral + $venta->ingresosventas;
                        $totalacumuladogeneral = $totalacumuladogeneral + $venta->ingresosventasacumulado;
                    @endphp
                </tr>
            @endforeach
            <td align='center' colspan=""
                style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 10px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);">
                ASISTENTES
            </td>
            <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 0px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                scope="col" colspan="6">
                VENTAS
            </th>
            <td align='center' colspan="9"
                style="text-align:center;background-color:#0AA09E;color:#FFFFFF;"></td>
            @foreach($ventasAsistente as $venta)
                <tr>
                    <td align='center'>{{$venta->nombre}}</td>
                    <td align='center' colspan="">{{$venta->lunes}}</td>
                    <td align='center' colspan="">{{$venta->martes}}</td>
                    <td align='center' colspan="">{{$venta->miercoles}}</td>
                    <td align='center' colspan="">{{$venta->jueves}}</td>
                    <td align='center' colspan="">{{$venta->viernes}}</td>
                    <td align='center' colspan="">{{$venta->sabado}}</td>
                    <td align='center' colspan="">{{$venta->acumuladas}}</td>
                    @if($venta->asistencia != null)
                        @switch($venta->asistencia)
                            @case(0)
                                <td align='center' colspan="">F</td>
                                @break
                            @case(1)
                                <td align='center' colspan="">A</td>
                                @break
                            @case(2)
                                <td align='center' colspan="">R</td>
                                @break
                        @endswitch
                    @else
                        <td align='center' colspan="">N/A</td>
                    @endif
                    <td align='center' colspan="">{{$venta->ingresosgotas}}</td>
                    <td align='center' colspan="">{{$venta->ingresosenganche}}</td>
                    <td align='center' colspan="">{{$venta->ingresosabonos}}</td>
                    <td align='center' colspan="">{{$venta->ingresospoliza}}</td>
                    <td align='center' colspan="">0</td>
                    <td align='center' colspan="">$0</td>
                    <td align='center' colspan=""></td>
                </tr>
            @endforeach
            <tr>
                <td align='center' colspan="12"
                    style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;"></td>
                <td align='center' style="color:#0AA09E;">TOTAL DIA</td>
                <td align='center' style="color:#0AA09E;">$ {{$totaldiageneral}}</td>
                <td align='center'></td>
            </tr>
            <tr>
                <td align='center' colspan="13"
                    style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;"></td>
                <td align='center' colspan="" style="color:#0AA09E;">TOTAL ACUMULADO</td>
                <td align='center' style="color:#0AA09E;">$ {{$totalacumuladogeneral}}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="tab-pane" id="productividad" role="tabpanel" aria-labelledby="productividad-tab">
            <div id="contenedorProductividad" style="max-height: 600px; overflow-y: auto; width: 100%; overflow-x: auto;">
                <table id="tablaPro" class="table-bordered table-striped table-general table-sm" style=" text-align:center;">
                    <thead>
                    <tr>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" colspan="2"> ECO JR</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" colspan="2">JR</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" colspan="2">DORADO 1 Y 2</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" colspan="2">PLATINO</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" colspan="2">PREMIUM</th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                    </tr>
                    <tr>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">NOMBRE</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">PUESTO</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">SUELDO</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">TOTAL CONTRATOS</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">POR ENTREGAR</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">MONTO TOTAL POR ENTREGAR</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">OBJETIVO</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">TOTAL</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">OBJETIVO</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">TOTAL</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">OBJETIVO</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">TOTAL</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">OBJETIVO</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">TOTAL</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">OBJETIVO</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">TOTAL</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">NO. VENTAS ENTREGADAS</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">PORCENTAJE ENTREGADO</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">MONTO ENTREGADOS
                            TOTAL
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col" colspan="2">
                            OBJETIVO EN
                            VENTAS {{$comisionunototalcontratosoptometrista}}
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col" colspan="2">
                            OBJETIVO EN
                            VENTAS {{$comisiondostotalcontratosoptometrista}}
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col" colspan="2">
                            OBJETIVO EN
                            VENTAS PREMIUM {{$comisiontrestotalcontratosoptometrista}}
                        </th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">TOTAL A PAGAR
                        </th>
                        @if(Auth::user()->rol_id == 7)
                            <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                                scope="col">INSUMOS</th>
                        @endif
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">ACCIÓN</th>
                        @if($poliza[0]->estatus == 0 && (Auth::user()->id == 1 || Auth::user()->id  == 61 || Auth::user()->id  == 761))
                            <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                                scope="col">ACTUALIZAR</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($productividad as $pro)
                        <tr>
                            <td align='center' colspan="">{{$pro->name}}</td>
                            <td align='center' colspan="">OPTOMETRISTA</td>
                            <td align='center' colspan="">$ {{$pro->sueldo}}</td>
                            <td align='center' colspan="">{{$pro->contratosporentregar + $pro->numeroventas}}</td>
                            <td align='center' colspan="">{{$pro->contratosporentregar}}</td>
                            <td align='center' colspan="">$ {{$pro->montototalreal}}</td>
                            <td align='center' colspan=""><b><i>24</i></b></td>
                            <td align='center' colspan="">{{$pro->totaleco}}</td>
                            <td align='center' colspan=""><b><i>4</i></b></td>
                            <td align='center' colspan="">{{$pro->totaljr}}</td>
                            <td align='center' colspan=""><b><i>10</i></b></td>
                            <td align='center' colspan="">{{$pro->totaldoradouno+$pro->totaldoradodos}}</td>
                            <td align='center' colspan=""><b><i>2</i></b></td>
                            <td align='center' colspan="">{{$pro->totalplatino}}</td>
                            <td align='center' colspan=""><b><i>10</i></b></td>
                            <td align='center' colspan="">{{$pro->totalpremium}}</td>
                            <td align='center' colspan="">{{$pro->numeroventas}}</td>
                            @if(($pro->contratosporentregar + $pro->numeroventas) > 0)
                                <td align='center' colspan="">% {{round(($pro->numeroventas * 100) / ($pro->contratosporentregar + $pro->numeroventas), 2)}}</td>
                            @else
                                <td align='center' colspan="">% 0</td>
                            @endif
                            <td align='center' colspan="">$ {{$pro->montoentregadostotalreal}}</td>
                            @if(round((100 / $comisionunototalcontratosoptometrista) * ($pro->numeroventas - $pro->totalpremium), 2) > 100)
                                <td align='center' colspan="">% 100</td>
                            @else
                                <td align='center' colspan="">% {{round((100 / $comisionunototalcontratosoptometrista) * ($pro->numeroventas - $pro->totalpremium), 2)}}</td>
                            @endif
                            <td align='center' colspan="">$ {{$pro->dineroobjetivoventastreinta}}</td>
                            @if(round((100 / $comisiondostotalcontratosoptometrista) * ($pro->numeroventas - $pro->totalpremium), 2) > 100)
                                <td align='center' colspan="">% 100</td>
                            @else
                                <td align='center' colspan="">% {{round((100 / $comisiondostotalcontratosoptometrista) * ($pro->numeroventas - $pro->totalpremium), 2)}}</td>
                            @endif
                            <td align='center' colspan="">$ {{$pro->dineroobjetivoventascuarenta}}</td>
                            @if(round((100 / $comisiontrestotalcontratosoptometrista) * $pro->totalpremium, 2) > 100)
                                <td align='center' colspan="">% 100</td>
                            @else
                                <td align='center' colspan="">% {{round((100 / $comisiontrestotalcontratosoptometrista) * $pro->totalpremium, 2)}}</td>
                            @endif
                            <td align='center' colspan="">$ {{$pro->dineroobjetivoventaspremium}}</td>
                            <td align='center' colspan="">$ {{$pro->sueldo + $pro->dineroobjetivoventastreinta + $pro->dineroobjetivoventascuarenta + $pro->dineroobjetivoventaspremium}}</td>
                            @if(Auth::user()->rol_id == 7)
                                <td align='center' colspan="">$ {{($pro->contratosporentregar + $pro->numeroventas) * ($sumainsumos == null ? 0 : $sumainsumos[0]->suma)}}</td>
                            @endif
                            <td align='center' colspan="">
                                <a href="#" onclick="abrirmodalinformacion('{{$idFranquicia}}', '{{$pro->id_usuario}}', '{{$pro->id_poliza}}', '{{$pro->name}}', '1')"
                                   data-toggle="modal" class="btn btn-primary btn-sm">VER</a>
                            </td>
                            @if($poliza[0]->estatus == 0 && (Auth::user()->id == 1 || Auth::user()->id  == 61 || Auth::user()->id  == 761))
                                <td align='center' colspan="">
                                    <a href="#" data-href="{{route('polizaactualizarasisoptocobranza',[$idFranquicia,$idPoliza,$pro->id_usuario])}}"
                                       data-toggle="modal" data-target="#confirmacion" class="btn btn-primary btn-sm">APLICAR</a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        <hr style="background-color: #0AA09E; height: 2px;">
            <div id="contenedorProductividadSemana" style="max-height: 600px; overflow-y: auto; width: 100%; overflow-x: auto;">
                <table id="tablaPro" class="table-bordered table-striped table-general table-sm" style=" text-align:center;">
                    <thead>
                    <tr>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" colspan="2">SEMANA ANTERIOR</th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;" scope="col"></th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" colspan="2">SEMANA ACTUAL</th>
                        <th style=" text-align:center;" scope="col"></th>
                    </tr>
                    <tr>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">NOMBRE</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">OBJETIVO {{$comisionunototalcontratosasistente}}</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">OBJETIVO {{$comisiondostotalcontratosasistente}}</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">NO. VENTAS (DIA)</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">APROBADAS</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">VENTAS ACUMULADAS</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">VENTAS ACUMULADAS APROBADAS</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">COMISION</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">OBJETIVO {{$comisionunototalcontratosasistente}}</th>
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                            scope="col">OBJETIVO {{$comisiondostotalcontratosasistente}}</th>
                        @if($poliza[0]->estatus == 0 && (Auth::user()->id == 1 || Auth::user()->id  == 61 || Auth::user()->id  == 761))
                            <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF; position: sticky; top: 29px; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);"
                                scope="col">ACTUALIZAR</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($productividadAsistente as $pro)
                        <tr>
                            <td align='center' colspan="">{{$pro->name}}</td>
                            @if($pro->numObjetivoSemanaAnterior >= $comisiondostotalcontratosasistente)
                                <td align='center' colspan="">NA</td>
                                <td align='center' colspan="">{{$pro->numObjetivoSemanaAnterior}} (Cumplido)</td>
                            @else
                                @if($pro->numObjetivoSemanaAnterior >= $comisionunototalcontratosasistente)
                                    <td align='center' colspan="">{{$pro->numObjetivoSemanaAnterior}} (Cumplido)</td>
                                    <td align='center' colspan="">NA</td>
                                @else
                                    <td align='center' colspan="">{{$pro->numObjetivoSemanaAnterior}} (NA)</td>
                                    <td align='center' colspan="">NA</td>
                                @endif
                            @endif
                            <td align='center' colspan="">{{$pro->sumaContratosNumVentas}}</td>
                            <td align='center' colspan="">{{$pro->sumaContratosAprobadas}}</td>
                            <td align='center' colspan="">{{$pro->sumaContratosVentasAcumuladas}}</td>
                            <td align='center' colspan="">{{$pro->sumaContratosVentasAcumuladasAprobadas}}</td>
                            @if($pro->numObjetivoSemanaAnterior >= $comisiondostotalcontratosasistente)
                                <td align='center' colspan="">$ {{$pro->sumaContratosVentasAcumuladasAprobadas * $comisiondosvalorasistente}}</td>
                            @else
                                @if($pro->numObjetivoSemanaAnterior >= $comisionunototalcontratosasistente)
                                    <td align='center' colspan="">$ {{$pro->sumaContratosVentasAcumuladasAprobadas * $comisionunovalorasistente}}</td>
                                @else
                                    <td align='center' colspan="">$ 0</td>
                                @endif
                            @endif
                            @if($pro->sumaContratosVentasAcumuladasAprobadas >= $comisiondostotalcontratosasistente)
                                <td align='center' colspan="">NA</td>
                                <td align='center' colspan="">Cumplido</td>
                            @else
                                @if($pro->sumaContratosVentasAcumuladasAprobadas >= $comisionunototalcontratosasistente)
                                    <td align='center' colspan="">Cumplido</td>
                                    <td align='center' colspan="">NA</td>
                                @else
                                    <td align='center' colspan="">NA</td>
                                    <td align='center' colspan="">NA</td>
                                @endif
                            @endif
                            @if($poliza[0]->estatus == 0 && (Auth::user()->id == 1 || Auth::user()->id  == 61 || Auth::user()->id  == 761))
                                <td align='center' colspan="">
                                    <a href="#" data-href="{{route('polizaactualizarasisoptocobranza',[$idFranquicia,$idPoliza,$pro->id_usuario])}}"
                                       data-toggle="modal" data-target="#confirmacion" class="btn btn-primary btn-sm">APLICAR</a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
    </div>
    <div class="tab-pane" id="cobranza" role="tabpanel" aria-labelledby="cobranza-tab">
        @if($poliza[0]->estatus == 0)
            <div class="col-12">
                <h5 style="color:#0AA09E;">Control de gasolina</h5>
                <form action="{{route('ingresarCobranza',[$idFranquicia,$idPoliza])}}" enctype="multipart/form-data"
                      method="POST" onsubmit="btnSubmit.disabled = true;">
                    @csrf
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label>Cantidad:</label>
                                <input type="number" name="cantidad"
                                       class="form-control {!! $errors->first('cantidad','is-invalid')!!}"
                                       placeholder="Cantidad" value="{{ old('cantidad') }}">
                                {!! $errors->first('cantidad','<div class="invalid-feedback">La cantidad es
                                    obligatoria.</div>')!!}
                            </div>
                        </div>
                        <div class="col-3">
                            <label for="">Usuario:</label>
                            <select class="custom-select {!! $errors->first('usuario','is-invalid')!!}"
                                    name="usuario"
                                    id="usuario">
                                <option selected value="nada">Seleccionar</option>
                                @foreach($usuarioscobranza as $uc)
                                    <option value="{{$uc->id}}">{{$uc->name}}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('usuario','<div class="invalid-feedback">Elegir un usuario </div>
                            ')!!}
                        </div>
                        <div class="col-3">
                            <!-- <label> &nbsp; </label> -->
                            <div class="form-group">
                                <button class="btn btn-outline-success btn-block" name="btnSubmit" type="submit" style="margin-top: 30px;">
                                    Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @endif
        <div id="contenedorCobranza" style="max-height: 600px; overflow-y: auto; width: 100%; overflow-x: auto;">
            <table id="tablaCobranza" class="table-bordered table-striped table-general table-sm">
                <thead>
                <tr>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">NOMBRE</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">ZONA</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">TABULAR</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">TARJETAS
                        PAGADAS
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">TARJETA COBRADA
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">TARJ.
                        ACUMULADA-SEMANA
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col"> DIARIO
                        ACUMULADO
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">$ ABONO PROMEDIO
                        ACUMULADO
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">GAS</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">INGRESO
                        COBRANZA
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">INGRESO
                        OFICINA
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">INGRESO
                        SUPERVISOR
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">INGRESO
                        USUARIOS ELIMINADOS
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">INGRESO
                        ACUMULADO
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">TARJETAS 75%</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">TARJ. POR COBRAR
                        75%
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">$ POR COBRAR
                        75%
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">TARJETAS 80%</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">TARJ. POR COBRAR
                        80%
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">$ POR COBRAR
                        80%
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">TARJETAS 85%</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">TARJ. POR COBRAR
                        85%
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">$ POR COBRAR
                        85%
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">TARJETAS 90%</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">TARJ. POR COBRAR
                        90%
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">$ POR COBRAR
                        90%
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">SUELDO BASE</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">6% - 75%</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">8% - 80</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">9% - 85</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">10% - 90</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">TOTAL A PAGAR
                    </th>
                    @if($poliza[0]->estatus == 0 && (Auth::user()->id == 1 || Auth::user()->id  == 61 || Auth::user()->id  == 761))
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">ACTUALIZAR</th>
                    @endif
                </tr>
                </thead>
                @if(sizeof($cobranzatabla) === 0 && $poliza[0]->estatus != 1)
                    <tbody>
                    <tr>
                        <td align='center' colspan="23">Sin registros</td>
                    </tr>
                    </tbody>
                @else
                    <tbody>
                    @foreach($cobranzatabla as $cobranza)
                        <tr>
                            <td align='center'>{{$cobranza->nombre}}</td>
                            <td align='center'>{{$cobranza->zona}}</td>
                            <td align='center'>{{$cobranza->tabular}}</td>
                            <td align='center'>{{$cobranza->pagadas}}</td>
                            <td align='center'>{{$cobranza->cobradas}}</td>
                            <td align='center'>{{$cobranza->acumuladasemana}}</td>
                            <td align='center'>%{{round($cobranza->diarioacumulado,2)}}</td>
                            @if($cobranza->acumuladasemana > 0)
                                <td align='center'>{{round($cobranza->ingresoacumulado / $cobranza->acumuladasemana,2)}}</td>
                            @else
                                <td align='center'>0</td>
                            @endif
                            <td align='center' @if($cobranza->gas >= 600) style="color: #ea9999; font-weight: bold;" @endif>{{$cobranza->gas}}</td>
                            <td align='center'>$ {{$cobranza->ingresocobranza}}</td>
                            <td align='center'>$ {{$cobranza->ingresooficina}}</td>
                            <td align='center'>$ {{$cobranza->ingresosupervisor}}</td>
                            <td align='center'>$ {{$cobranza->ingresousuarioseliminados}}</td>
                            <td align='center'>$ {{$cobranza->ingresoacumulado}}</td>
                            <td align='center'>{{round($cobranza->tabular * 0.75,2)}}</td>
                            @if(round(($cobranza->tabular * 0.75) - $cobranza->acumuladasemana,2) < 0)
                                <td align='center'>0</td>
                            @else
                                <td align='center'>{{round(($cobranza->tabular * 0.75) - $cobranza->acumuladasemana,2)}}</td>
                            @endif
                            @if(round((($cobranza->tabular * 0.75) - $cobranza->acumuladasemana) * $abonoMinimoSemanal,2) < 0)
                                <td align='center'>$ 0</td>
                            @else
                                <td align='center'>$ {{round((($cobranza->tabular * 0.75) - $cobranza->acumuladasemana) * $abonoMinimoSemanal,2)}}</td>
                            @endif
                            <td align='center'>{{round($cobranza->tabular * 0.80,2)}}</td>
                            @if(round(($cobranza->tabular * 0.80) - $cobranza->acumuladasemana,2) < 0)
                                <td align='center'>0</td>
                            @else
                                <td align='center'>{{round(($cobranza->tabular * 0.80) - $cobranza->acumuladasemana,2)}}</td>
                            @endif
                            @if(round((($cobranza->tabular * 0.80) - $cobranza->acumuladasemana) * $abonoMinimoSemanal,2) < 0)
                                <td align='center'>$ 0</td>
                            @else
                                <td align='center'>$ {{round((($cobranza->tabular * 0.80) - $cobranza->acumuladasemana) * $abonoMinimoSemanal,2)}}</td>
                            @endif
                            <td align='center'>{{round($cobranza->tabular * 0.85,2)}}</td>
                            @if(round(($cobranza->tabular * 0.85) - $cobranza->acumuladasemana,2) < 0)
                                <td align='center'>0</td>
                            @else
                                <td align='center'>{{round(($cobranza->tabular * 0.85) - $cobranza->acumuladasemana,2)}}</td>
                            @endif
                            @if(round((($cobranza->tabular * 0.85) - $cobranza->acumuladasemana) * $abonoMinimoSemanal,2) < 0)
                                <td align='center'>$ 0</td>
                            @else
                                <td align='center'>$ {{round((($cobranza->tabular * 0.85) - $cobranza->acumuladasemana) * $abonoMinimoSemanal,2)}}</td>
                            @endif
                            <td align='center'>{{round($cobranza->tabular * 0.90,2)}}</td>
                            @if(round(($cobranza->tabular * 0.90) - $cobranza->acumuladasemana,2) < 0)
                                <td align='center'>0</td>
                            @else
                                <td align='center'>{{round(($cobranza->tabular * 0.90) - $cobranza->acumuladasemana,2)}}</td>
                            @endif
                            @if(round((($cobranza->tabular * 0.90) - $cobranza->acumuladasemana) * $abonoMinimoSemanal,2) < 0)
                                <td align='center'>$ 0</td>
                            @else
                                <td align='center'>$ {{round((($cobranza->tabular * 0.90) - $cobranza->acumuladasemana) * $abonoMinimoSemanal,2)}}</td>
                            @endif
                            <td align='center'>$ {{$cobranza->sueldo}}</td>
                            @if($cobranza->tabular > 150 && $cobranza->acumuladasemana > 0)
                                @if(round($cobranza->ingresoacumulado / $cobranza->acumuladasemana,2) >= 150)
                                    @if(round(($cobranza->tabular * 0.90) - $cobranza->acumuladasemana,2) <= 0)
                                        <td align='center'>$ 0</td>
                                        <td align='center'>$ 0</td>
                                        <td align='center'>$ 0</td>
                                        <td align='center'>$ {{$cobranza->ingresoacumulado * 0.10}}</td>
                                        <td align='center'>$ {{$cobranza->sueldo + ($cobranza->ingresoacumulado * 0.10)}}</td>
                                    @else
                                        @if(round(($cobranza->tabular * 0.85) - $cobranza->acumuladasemana,2) <= 0)
                                            <td align='center'>$ 0</td>
                                            <td align='center'>$ 0</td>
                                            <td align='center'>$ {{$cobranza->ingresoacumulado * 0.09}}</td>
                                            <td align='center'>$ 0</td>
                                            <td align='center'>$ {{$cobranza->sueldo + ($cobranza->ingresoacumulado * 0.09)}}</td>
                                        @else
                                            @if(round(($cobranza->tabular * 0.80) - $cobranza->acumuladasemana,2) <= 0)
                                                <td align='center'>$ 0</td>
                                                <td align='center'>$ {{$cobranza->ingresoacumulado * 0.08}}</td>
                                                <td align='center'>$ 0</td>
                                                <td align='center'>$ 0</td>
                                                <td align='center'>$ {{$cobranza->sueldo + ($cobranza->ingresoacumulado * 0.08)}}</td>
                                            @else
                                                @if(round(($cobranza->tabular * 0.75) - $cobranza->acumuladasemana,2) <= 0)
                                                    <td align='center'>$ {{$cobranza->ingresoacumulado * 0.06}}</td>
                                                    <td align='center'>$ 0</td>
                                                    <td align='center'>$ 0</td>
                                                    <td align='center'>$ 0</td>
                                                    <td align='center'>$ {{$cobranza->sueldo + ($cobranza->ingresoacumulado * 0.06)}}</td>
                                                @else
                                                    <td align='center'>$ 0</td>
                                                    <td align='center'>$ 0</td>
                                                    <td align='center'>$ 0</td>
                                                    <td align='center'>$ 0</td>
                                                    <td align='center'>$ {{$cobranza->sueldo}}</td>
                                                @endif
                                            @endif
                                        @endif
                                    @endif
                                @else
                                    <td align='center'>$ 0</td>
                                    <td align='center'>$ 0</td>
                                    <td align='center'>$ 0</td>
                                    <td align='center'>$ 0</td>
                                    <td align='center'>$ {{$cobranza->sueldo}}</td>
                                @endif
                            @else
                                <td align='center'>$ 0</td>
                                <td align='center'>$ 0</td>
                                <td align='center'>$ 0</td>
                                <td align='center'>$ 0</td>
                                <td align='center'>$ {{$cobranza->sueldo}}</td>
                            @endif
                            @if($poliza[0]->estatus == 0 && (Auth::user()->id == 1 || Auth::user()->id  == 61 || Auth::user()->id  == 761))
                                <td align='center' style="font-size:15px;">
                                    <a href="#" data-href="{{route('polizaactualizarasisoptocobranza',[$idFranquicia,$idPoliza,$cobranza->id_usuario])}}"
                                       data-toggle="modal" data-target="#confirmacion" class="btn btn-primary btn-sm">APLICAR</a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                @endif
            </table>
            <div class="row">
                <label class="col-12" style="color: #ea9999;">NOTA: Si la apertura es menor o igual a 150 no aplica comisiones.</label>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="gastos" role="tabpanel" aria-labelledby="gastos-tab">
        <div class="col-12">
            @if($poliza[0]->estatus == 0)
                <h5 style="color:#0AA09E;">Nuevo gasto</h5>
                <div class="col-12">
                    <form action="{{route('ingresarGasto',[$idFranquicia,$idPoliza])}}"
                          enctype="multipart/form-data"
                          method="POST" onsubmit="btnSubmit2.disabled = true;">
                        @csrf
                        <div class="row">
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Descripcion:</label>
                                    <input type="text" name="descripcion2"
                                           class="form-control {!! $errors->first('descripcion2','is-invalid')!!}"
                                           placeholder="Descripcion" value="{{ old('descripcion2') }}">
                                    {!! $errors->first('descripcion2','<div class="invalid-feedback">La descripcion es
                                        obligatoria.</div>')!!}
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-group">
                                    <label>No. Factura:</label>
                                    <input type="number" name="factura" min="0"
                                           class="form-control {!! $errors->first('factura','is-invalid')!!}"
                                           placeholder="Factura"
                                           value="{{ old('factura') }}">
                                    {!! $errors->first('factura','<div class="invalid-feedback">la factura es obligatorio.</div>
                                    ')!!}
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Foto de la factura:</label>
                                    <input type="file" name="fotofactura"
                                           class="form-control-file  {!! $errors->first('fotofactura','is-invalid')!!}"
                                           accept="image/jpg">
                                    {!! $errors->first('fotofactura','<div class="invalid-feedback">Obligatorio
                                        .</div>')!!}
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Observaciones:</label>
                                    <input type="text" name="observaciones"
                                           class="form-control {!! $errors->first('observaciones','is-invalid')!!}"
                                           placeholder="observaciones" value="{{ old('observaciones') }}">
                                    {!! $errors->first('descripcion2','<div class="invalid-feedback">La descripcion es
                                        obligatoria.</div>')!!}
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="form-group">
                                    <label>Monto:</label>
                                    <input type="number" name="monto2" min="0"
                                           class="form-control {!! $errors->first('monto2','is-invalid')!!}"
                                           placeholder="Monto"
                                           value="{{ old('monto2') }}">
                                    {!! $errors->first('monto2','<div class="invalid-feedback">El monto es obligatorio.</div>
                                    ')!!}
                                </div>
                            </div>
                            <div class="col-2">
                                <label for="">Tipo gasto:</label>
                                <select class="custom-select {!! $errors->first('tipogasto','is-invalid')!!}"
                                        name="tipogasto"
                                        id="tipogasto">
                                    <option selected>Seleccionar</option>
                                    <option value="0" {{ old('tipogasto') == '0' ? 'selected' : '' }}>Gastos
                                        Administración
                                    </option>
                                    <option value="1" {{ old('tipogasto') == '1' ? 'selected' : '' }}>Gastos
                                        ventas
                                    </option>
                                    <option value="2" {{ old('tipogasto') == '2' ? 'selected' : '' }}>Gastos
                                        cobranza
                                    </option>
                                    <option value="3" {{ old('tipogasto') == '3' ? 'selected' : '' }}>Otros gastos
                                    </option>
                                </select>
                                {!! $errors->first('tipogasto','<div class="invalid-feedback">Elegir una forma de gasto </div>
                                ')!!}
                            </div>
                            <div class="col-1">
                                <!-- <label> &nbsp; </label> -->
                                <div class="form-group">
                                    <button class="btn btn-outline-success btn-block" name="btnSubmit2" style="margin-top: 30px;"
                                            type="submit">Agregar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <hr>
                <h5 style="color:#0AA09E;">Actualizar foto de gasto</h5>
                <div class="col-12">
                    <form action="{{route('actualizarfotogasto',[$idFranquicia,$idPoliza])}}"
                          enctype="multipart/form-data"
                          method="POST" onsubmit="btnSubmit2.disabled = true;">
                        @csrf
                        <div class="row">
                            <div class="col-2">
                                <label for="">Gasto:</label>
                                <select class="custom-select"
                                        name="idgasto"
                                        id="idgasto">
                                    <option selected value="nada">Seleccionar</option>
                                    @foreach($gastosgeneralpoliza as $gastogeneralpoliza)
                                        <option value="{{$gastogeneralpoliza->id}}">{{$gastogeneralpoliza->id}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>Foto gasto:</label>
                                    <input type="file" name="fotogasto"
                                           class="form-control-file"
                                           accept="image/jpg">
                                </div>
                            </div>
                            <div class="col-1">
                                <!-- <label> &nbsp; </label> -->
                                <div class="form-group">
                                    <button class="btn btn-outline-success btn-block" name="btnSubmit2" style="margin-top: 30px;"
                                            type="submit">Agregar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <hr>
            @endif
            <h3 style="text-align:center;">Gastos de administración</h3>
            <table id="tablaGastosAdmon" class="table-bordered table-striped table-general table-sm">
                <thead>
                <tr>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">ID</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">FECHA</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">DESCRIPCION
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">NO. FACTURA
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">FOTO</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">OBSERVACIONES
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">MONTO</th>
                    @if($poliza[0]->estatus == 0)
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">
                            ELIMINAR
                        </th>
                    @endif
                </tr>
                </thead>
                @if($gastosadmon == null || sizeof($gastosadmon) === 0)
                    <tbody>
                    <tr>
                        <td align='center' colspan="8">Sin registros</td>
                    </tr>
                    </tbody>

                @else
                    <tbody>
                    @foreach($gastosadmon as $gadmin)
                        <tr>
                            <td align='center'>{{$gadmin->id}}</td>
                            <td align='center'>{{$gadmin->created_at}}</td>
                            <td>{{$gadmin->descripcion}}</td>
                            <td align='center'>{{$gadmin->factura}}</td>
                            @if(isset($gadmin->foto))
                                <td align='center'>
                                    <div class="col-12" data-toggle="modal" data-target="#imagemodal" style="cursor: pointer" onclick="mostrarfotogastosadministracion('{{$gadmin->id}}')">
                                        <img id="img2{{$gadmin->id}}" src="{{asset($gadmin->foto)}}" class="img-thumbnail"
                                             style="width:100px;height:65px;">
                                    </div>
                                </td>
                            @else
                                <td align='center'>NA</td>
                            @endif
                            <td align='center'>{{$gadmin->observaciones}}</td>
                            <td align='center'>$ {{$gadmin->monto}}</td>
                            @if($poliza[0]->estatus == 0)
                                <td align='center'><a type="button" class="btn btn-danger" style="color:#FEFEFE;"
                                                      href="{{route('eliminarGasto',[$idFranquicia,$gadmin->id_poliza, $gadmin->id])}}">ELIMINAR</a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    @if($poliza[0]->estatus == 0)
                        <td align='center' colspan="5" style="text-align:center;background-color:#0AA09E;color:#FFFFFF;"></td>
                    @else
                        <td align='center' colspan="4" style="text-align:center;background-color:#0AA09E;color:#FFFFFF;"></td>
                    @endif
                    <td align='center' style="color:#0AA09E;">TOTAL GASTOS DE ADMINISTRACIÓN</td>
                    <td align='center' style="color:#0AA09E;" colspan="1">$ {{$sumaadmin[0]->suma}}</td>
                    </tbody>
                @endif
            </table>
            <hr>
            <h3 style="text-align:center;">Gastos ventas</h3>
            <table id="tablaGastosVentas" class="table-bordered table-striped table-general table-sm">
                <thead>
                <tr>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">ID</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">FECHA</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">DESCRIPCION
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">NO. FACTURA
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">FOTO</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">OBSERVACIONES
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">MONTO</th>
                    @if($poliza[0]->estatus == 0)
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">
                            ELIMINAR
                        </th>
                    @endif
                </tr>
                </thead>
                @if($gastosventas == null || sizeof($gastosventas)===0)
                    <tbody>
                    <tr>
                        <td align='center' colspan="8">Sin registros</td>
                    </tr>
                    </tbody>
                @else
                    <tbody>
                    @foreach($gastosventas as $gastosventa)
                        <tr>
                            <td align='center'>{{$gastosventa->id}}</td>
                            <td align='center'>{{$gastosventa->created_at}}</td>
                            <td>{{$gastosventa->descripcion}}</td>
                            <td align='center'>{{$gastosventa->factura}}</td>
                            @if(isset($gastosventa->foto))
                                <td align='center'>
                                    <div class="col-12" data-toggle="modal" data-target="#imagemodal" style="cursor: pointer" onclick="mostrarfotogastosventas('{{$gastosventa->id}}')">
                                        <img id="img3{{$gastosventa->id}}" src="{{asset($gastosventa->foto)}}"
                                             class="img-thumbnail"
                                             style="width:100px;height:65px;">
                                    </div>
                                </td>
                            @else
                                <td align='center'>NA</td>
                            @endif
                            <td align='center'>{{$gastosventa->observaciones}}</td>
                            <td align='center'>$ {{$gastosventa->monto}}</td>
                            @if($poliza[0]->estatus == 0)
                                <td align='center'><a type="button" class="btn btn-danger" style="color:#FEFEFE;"
                                                      href="{{route('eliminarGasto',[$idFranquicia,$gastosventa->id_poliza, $gastosventa->id])}}">ELIMINAR</a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    @if($poliza[0]->estatus == 0)
                        <td align='center' colspan="5" style="text-align:center;background-color:#0AA09E;color:#FFFFFF;"></td>
                    @else
                        <td align='center' colspan="4" style="text-align:center;background-color:#0AA09E;color:#FFFFFF;"></td>
                    @endif
                    <td align='center' style="color:#0AA09E;">TOTAL GASTOS VENTAS</td>
                    <td align='center' style="color:#0AA09E;" colspan="1">$ {{$sumaventas[0]->suma}}</td>
                    </tbody>
                @endif

            </table>
            <hr>
            <h3 style="text-align:center;">Gastos cobranza</h3>
            <table id="tablaGastosCobranza" class="table-bordered table-striped table-general table-sm">
                <thead>
                <tr>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">ID</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">FECHA</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">DESCRIPCION
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">NO. FACTURA
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">FOTO</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">OBSERVACIONES
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">MONTO</th>
                    @if($poliza[0]->estatus == 0)
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">
                            ELIMINAR
                        </th>
                    @endif
                </tr>
                </thead>
                @if($gastoscobranza == null || sizeof($gastoscobranza)===0)
                    <tbody>
                    <tr>
                        <td align='center' colspan="8">Sin registros</td>
                    </tr>
                    </tbody>
                @else
                    <tbody>
                    @foreach($gastoscobranza as $gc)
                        <tr>
                            <td align='center'>{{$gc->id}}</td>
                            <td align='center'>{{$gc->created_at}}</td>
                            <td>{{$gc->descripcion}}</td>
                            <td align='center'>{{$gc->factura}}</td>
                            @if(isset($gc->foto))
                                <td align='center'>
                                    <div class="col-12" data-toggle="modal" data-target="#imagemodal" style="cursor: pointer" onclick="mostrarfotogastoscobranza('{{$gc->id}}')">
                                        <img id="img4{{$gc->id}}" src="{{asset($gc->foto)}}" class="img-thumbnail"
                                             style="width:100px;height:65px;">
                                    </div>
                                </td>
                            @else
                                <td align='center'>NA</td>
                            @endif
                            <td align='center'>{{$gc->observaciones}}</td>
                            <td align='center'>$ {{$gc->monto}}</td>
                            @if($poliza[0]->estatus == 0)
                                <td align='center'><a type="button" class="btn btn-danger" style="color:#FEFEFE;"
                                                      href="{{route('eliminarGasto',[$idFranquicia,$gc->id_poliza, $gc->id])}}">ELIMINAR</a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    @if($poliza[0]->estatus == 0)
                        <td align='center' colspan="5" style="text-align:center;background-color:#0AA09E;color:#FFFFFF;"></td>
                    @else
                        <td align='center' colspan="4" style="text-align:center;background-color:#0AA09E;color:#FFFFFF;"></td>
                    @endif
                    <td align='center' style="color:#0AA09E;">TOTAL GASTOS DE COBRANZA</td>
                    <td align='center' style="color:#0AA09E;" colspan="1">$ {{$sumacobranza[0]->suma}}</td>
                    </tbody>
                @endif

            </table>
            <hr>
            <h3 style="text-align:center;">Otros gastos</h3>
            <table id="tablaOtrosGastos" class="table-bordered table-striped table-general table-sm">
                <thead>
                <tr>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">ID</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">FECHA</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">DESCRIPCION
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">NO. FACTURA
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">FOTO</th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">OBSERVACIONES
                    </th>
                    <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">MONTO</th>
                    @if($poliza[0]->estatus == 0)
                        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">
                            ELIMINAR
                        </th>
                    @endif
                </tr>
                </thead>
                @if($otrosgastos == null || sizeof($otrosgastos)===0)
                    <tbody>
                    <tr>
                        <td align='center' colspan="8">Sin registros</td>
                    </tr>
                    </tbody>
                @else
                    <tbody>
                    @foreach($otrosgastos as $og)
                        <tr>
                            <td align='center'>{{$og->id}}</td>
                            <td align='center'>{{$og->created_at}}</td>
                            <td>{{$og->descripcion}}</td>
                            <td align='center'>{{$og->factura}}</td>
                            @if(isset($og->foto))
                                <td align='center'>
                                    <div class="col-12" data-toggle="modal" data-target="#imagemodal" style="cursor: pointer" onclick="mostrarfotogastosotros('{{$og->id}}')">
                                        <img id="img5{{$og->id}}" src="{{asset($og->foto)}}" class="img-thumbnail"
                                             style="width:100px;height:65px;">
                                    </div>
                                </td>
                            @else
                                <td align='center'>NA</td>
                            @endif
                            <td align='center'>{{$og->observaciones}}</td>
                            <td align='center'>$ {{$og->monto}}</td>
                            @if($poliza[0]->estatus == 0)
                                <td align='center'><a type="button" class="btn btn-danger" style="color:#FEFEFE;"
                                                      href="{{route('eliminarGasto',[$idFranquicia,$og->id_poliza, $og->id])}}">ELIMINAR</a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    @if($poliza[0]->estatus == 0)
                        <td align='center' colspan="5" style="text-align:center;background-color:#0AA09E;color:#FFFFFF;"></td>
                    @else
                        <td align='center' colspan="4" style="text-align:center;background-color:#0AA09E;color:#FFFFFF;"></td>
                    @endif
                    <td align='center' style="color:#0AA09E;">TOTAL OTROS GASTOS</td>
                    <td align='center' style="color:#0AA09E;" colspan="1">$ {{$sumaotros[0]->suma}}</td>
                    </tbody>
                @endif
            </table>
            <hr>
        </div>
    </div>
    <div class="tab-pane" id="historial" role="tabpanel" aria-labelledby="historial-tab">
        <table id="tablaHistorial" class="table-bordered table-striped table-general table-sm">
            <thead>
            <tr>
                <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">USUARIO</th>
                <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">CAMBIOS</th>
                <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">FECHA</th>
            </tr>
            </thead>
            @if(sizeof($historial) === 0)
                <tbody>
                <tr>
                    <td align='center' colspan="7">Sin registros</td>
                </tr>
                </tbody>
            @else
                <tbody>
                @foreach($historial as $ht)
                    <tr>
                        <td align='center'>{{$ht->name}}</td>
                        <td>{{$ht->cambios}}</td>
                        <td align='center'>{{$ht->created_at}}</td>
                    </tr>
                @endforeach
                </tbody>
            @endif
        </table>
    </div>

    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <img src="" class="imagepreview" style="width: 100%; margin-top: 60px; margin-bottom: 60px; cursor: grabbing">
                </div>
            </div>
        </div>
    </div>

    <!-- modal para boton APLICAR para cada usuario -->
    <div class="modal fade" id="confirmacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #0AA09E; color:white;">
                    Solicitud de confirmación
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" style="color: #dc3545">
                            Ten en cuenta que al actualizar se volveran a ingresar los gastos eliminados en caso de haberlo hecho
                            ¿Estas seguro?
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
                    <a class="btn btn-outline-success btn-ok">Aceptar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- modal para boton ACCION para cada usuario -->
    <div class="modal fade" id="modalinformacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #0AA09E; color:white;">
                    Información
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <label id="contenidomodalinformacion"></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-success" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

</div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx-populate/1.21.0/xlsx-populate.min.js"></script>
    <script>
        @if(session('pestaña'))
        $(function () {
            @switch(session('pestaña'))
            @case('general')
            $('#myTab a[href="#general"]').tab('show');
            @break
            @case('oficina')
            $('#myTab a[href="#ingresosoficina"]').tab('show');
            @break
            @case('gastos')
            $('#myTab a[href="#gastos"]').tab('show');
            @break
            @case('productividad')
            $('#myTab a[href="#productividad"]').tab('show');
            @break
            @case('ventas')
            $('#myTab a[href="#ingresosventas"]').tab('show');
            @break
            @case('cobranza')
            $('#myTab a[href="#cobranza"]').tab('show');
            @break
            @default
            @endswitch
        });
        @endif

        var x = 0;

        function mostrarfotoingresosoficina($idingresooficina) {
            pintarImagenEnModal(document.getElementById("img1"+$idingresooficina).src);
        }

        function mostrarfotogastosadministracion($idgasto) {
            pintarImagenEnModal(document.getElementById("img2"+$idgasto).src);
        }

        function mostrarfotogastosventas($idgasto) {
            pintarImagenEnModal(document.getElementById("img3"+$idgasto).src);
        }

        function mostrarfotogastoscobranza($idgasto) {
            pintarImagenEnModal(document.getElementById("img4"+$idgasto).src);
        }

        function mostrarfotogastosotros($idgasto) {
            pintarImagenEnModal(document.getElementById("img5"+$idgasto).src);
        }

        function pintarImagenEnModal($src) {
            $src = $src.replace('luzatuvida.test', 'adminlabo.luzatuvida.com.mx');
            //alert($src);
            $('.imagepreview').attr('src', $src);
            x = 0;
            rotarImagen(x);
        }

        $(".imagepreview").on('click',function (){
            rotarImagen(x++);
        });

        function rotarImagen($x){
            $rotate = "";
            switch ($x) {
                case 0:
                    $rotate = "rotate(0deg)";
                    break;
                case 1:
                    $rotate = "rotate(90deg)";
                    break;
                case 2:
                    $rotate = "rotate(180deg)";
                    break;
                case 3:
                    $rotate = "rotate(270deg)";
                    x = 0;
                    break;
            }

            $('.imagepreview').css({
                "transform": $rotate
            });
        }

        $('#confirmacion').on('show.bs.modal', function(e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
        });

        function abrirmodalinformacion($idFranquicia, $id_usuario, $id_poliza, $nombreusuario, $opcion) {
            //Opcion
            //0 -> Ingresos ventas
            //1 -> Productividad

            if($("#spCargando").is(":hidden")) {
                //No hay ninguna peticion pendiente

                //Mostramos spinner cargando
                $("#spCargando").show();
                $("#btnTerminarPoliza, #btnTablaAsistencia").prop('disabled', true);

                $.ajax({
                    url: '/cargarinformacionmodalpolizatiemporeal',
                    method: 'GET',
                    data: {
                        idFranquicia: $idFranquicia,
                        id_usuario: $id_usuario,
                        id_poliza: $id_poliza,
                        opcion: $opcion
                    },
                    dataType: 'json',
                    success: function (respuesta) {

                        $mensaje = "";
                        if($opcion == 0) {
                            //Ingresos ventas
                            $mensaje = "No se encontro registro de abonos.";
                            if (respuesta.data.length > 0) {
                                //Si data es diferente de vacio
                                $mensaje = "ABONOS DE " + $nombreusuario.toUpperCase() + "<br><br>" + respuesta.data;
                            }
                        }else {
                            //Productividad
                            $mensaje = "No se encontro registro de contratos.";
                            if (respuesta.data.length > 0) {
                                //Si data es diferente de vacio
                                $mensaje = "CONTRATOS DE " + $nombreusuario.toUpperCase() + "<br><br>" + respuesta.data;
                            }
                        }

                        //Desbloquear elementos
                        $("#btnTerminarPoliza, #btnTablaAsistencia").prop('disabled', false);

                        $("#contenidomodalinformacion").html($mensaje);

                        //Ocultamos spinner
                        $("#spCargando").hide();

                        $("#modalinformacion").modal("show");
                    },
                    error: function () {
                        console.log('No se pudo obtener la informacion');
                    }
                });

            }else {
                //Hay una peticion pendiente
                myAlert("Espera a que se cargue el usuario anterior", 1);
            }

        }

        function exportarPolizaExcel(nombreArchivo){
            //Xlsx-populate

            XlsxPopulate.fromBlankAsync()
                .then(workbook => {
                    const sheet = workbook.sheet(0);
                    sheet.name("General"); // Agregar nombre a la primera hoja

                    // Establecer los estilos para el encabezado
                    const headerStyle = {
                        fill: '00A09E',  // Color de fondo
                        fontColor: 'FFFFFF',  // Color de fuente
                        bold: true,  // Negrita
                        horizontalAlignment: 'center', // Centrar el texto horizontalmente
                    };

                    // Establecer los estilos para el body de la tabla
                    const bodyStyle = {
                        fontColor: '000000',  // Color de fuente
                        horizontalAlignment: 'center', // Centrar el texto horizontalmente
                    };

                    //INGRESOS OFICINA
                    const sheet1 = workbook.addSheet("Ingresos Oficina"); // Agrega hoja de cálculo para la siguiente tabla
                    const tabla = document.getElementById("tablaHistorialC"); //Referencia a la tabla que se exportara

                    if(tabla != null) {

                        // Obtener los encabezados de la tabla
                        const encabezados = tabla.querySelectorAll('th'); //obtener los th de la tabla
                        encabezados.forEach((encabezado, index) => {
                            //Recorrido de la tabla
                            if (index != 5) {
                                //Columna diferente de ELIMINAR
                                let widthColum = 0; //Valor por default
                                //Crear ancho de cada columna del encabezado
                                switch (index) {
                                    case 0: //Columna A
                                        widthColum = 5;
                                        break;
                                    case 1: //Columna B
                                        widthColum = 120;
                                        break;
                                    case 2: //Columna C
                                    case 3: //Columna D
                                    case 4: //Columna E
                                        widthColum = 15;
                                        break;
                                }
                                sheet1.cell(1, index + 1).value(encabezado.textContent);
                                sheet1.cell(1, index + 1).style(headerStyle);
                                console.log("widthColum " + widthColum);
                                sheet1.column(index + 1).width(widthColum);
                                console.log("widthColum 2 " + widthColum);
                            }
                        });

                        // Obtener las filas de la tabla
                        const filas = tabla.querySelectorAll('tr'); //Obtener los tr de la tabla
                        const lastRowIndex = filas.length - 1; // Indice de la última fila

                        var formulaTabla = "";
                        filas.forEach((fila, rowIndex) => {
                            //Recorrido de filas de la tabla

                            const celdas = fila.querySelectorAll('td');
                            let columnIndex = 0; // Variable para rastrear la columna actual
                            let occupiedColumns = []; // Columnas ocupadas por celdas con colspan

                            celdas.forEach((celda) => {
                                //Recorrido de celdas de la tabla

                                const colspan = celda.getAttribute('colspan');
                                const rowspan = celda.getAttribute('rowspan');

                                let textContent = celda.textContent;

                                if (celda.textContent.length > 0) {
                                    //Texto de la celda es diferente de vacio
                                    textContent = celda.textContent.startsWith('$') ? Number(celda.textContent.substring(1)) : celda.textContent; // Convertir el valor con signo "$" a número
                                    textContent = !isNaN(Number(textContent)) ? Number(textContent) : textContent; //Quitar cualquier formato raro de la celda
                                    if (columnIndex == 3 && rowIndex != lastRowIndex) {
                                        //Columna de FOTO y no es la ultima fila
                                        textContent = "NA";
                                    }
                                }

                                const cellAddress = sheet1.cell(rowIndex + 1, columnIndex + 1).address(); //Se obtiene el address de la celda ejemplo A1, E15, G10 etc

                                // Agregar contenido a las celdas
                                for (let i = 0; i < (colspan ? parseInt(colspan) : 1); i++) {
                                    for (let j = 0; j < (rowspan ? parseInt(rowspan) : 1); j++) {

                                        if (columnIndex != 5) {
                                            //Columna diferente a la de ELIMINAR (Imprimir)
                                            if (columnIndex == 1) {
                                                sheet1.cell(rowIndex + j + 1, columnIndex + i + 1).value(textContent);
                                            } else {
                                                sheet1.cell(rowIndex + j + 1, columnIndex + i + 1).value(textContent).style(bodyStyle);
                                            }


                                            // Pintar las celdas con colspan de un color específico
                                            if (colspan && !occupiedColumns.includes(columnIndex + i)) {
                                                if (textContent.length == 0) {
                                                    //Renglon es igual a vacio
                                                    sheet1.cell(rowIndex + j + 1, columnIndex + i + 1).style(headerStyle);
                                                }
                                                occupiedColumns.push(columnIndex + i);
                                            }

                                            if (columnIndex == 4) {
                                                //Columna E
                                                if (rowIndex === lastRowIndex) {
                                                    // Última fila
                                                    sheet1.cell(rowIndex + j + 1, columnIndex + i + 1).formula("=SUM(" + formulaTabla.slice(0, formulaTabla.length - 1) + ")");
                                                } else {
                                                    formulaTabla += cellAddress + ":"; //Creacion de formula
                                                }
                                            }
                                        }
                                    }
                                }

                                columnIndex += colspan ? parseInt(colspan) : 1;
                            });
                        });

                    }

                    //INGRESOS VENTAS
                    const sheet2 = workbook.addSheet("Ingresos Ventas"); // Agrega hoja de cálculo para la siguiente tabla
                    const tabla2 = document.getElementById("tablaVentas"); //Referencia a la tabla que se exportara

                    if(tabla2 != null) {

                        // Agregar las celdas del encabezado de la tabla
                        const headerCells = [
                            {value: 'OPTOMETRISTAS', rowspan: 2, style: headerStyle},
                            {value: 'VENTAS', colspan: 6, style: headerStyle},
                            {value: 'VENTAS ACUMULADAS', rowspan: 2, style: headerStyle},
                            {value: 'ASISTENCIA', rowspan: 2, style: headerStyle},
                            {value: 'INGRESO GOTAS', rowspan: 2, style: headerStyle},
                            {value: 'INGRESO DE ENGANCHE', rowspan: 2, style: headerStyle},
                            {value: 'INGRESO POLIZA', rowspan: 2, style: headerStyle},
                            {value: 'INGRESO VENTAS', rowspan: 2, style: headerStyle},
                            {value: 'INGRESO ACUMULADO SEMANA', rowspan: 2, style: headerStyle},
                        ];

                        let bandera = false;
                        let endCol = 0;
                        headerCells.forEach((cell, index) => {

                            let widthColum = 5; //Valor por default
                            //Crear ancho de cada columna del encabezado
                            switch (index) {
                                case 0: //Columna A
                                    widthColum = 50;
                                    break;
                                case 7: //Columna H
                                case 9: //Columna J
                                case 10: //Columna K
                                case 11: //Columna L
                                case 12: //Columna M
                                case 13: //Columna N
                                    widthColum = 15;
                                    break;
                                case 8: //Columna I
                                    widthColum = 10;
                                    break;
                            }
                            console.log("widthColum 3 " + widthColum);
                            sheet2.column(index + 1).width(widthColum);
                            console.log("widthColum 4 " + widthColum);

                            if (!bandera) {
                                //Es el primer encabezado se tomara el index
                                endCol = index + 1;
                            } else {
                                //Es el segundo en delante encabezados se tomara el endCol
                                endCol = endCol + 1;
                            }
                            const cellRange = sheet2.range(1, endCol, 2, endCol);
                            cellRange.value(cell.value).style(cell.style);
                            if (cell.rowspan) {
                                //Tiene rowspan el encabezado
                                cellRange.merged(true);
                            }
                            if (cell.colspan) {
                                // Calcular el rango para las celdas con colspan
                                endCol = index + cell.colspan;
                                const colspanRange = sheet2.range(1, index + 1, 1, endCol);
                                colspanRange.style(headerStyle); // Aplicar el estilo del encabezado
                                if (cell.colspan > 1) {
                                    colspanRange.merged(true); // Fusionar las celdas si colspan es mayor que 1
                                }
                                bandera = true;
                            }
                        });

                        // Agregar las celdas de los días de la semana
                        const daysOfWeek = ['LU', 'MA', 'MI', 'JU', 'VI', 'SA'];
                        daysOfWeek.forEach((day, index) => {
                            sheet2.cell(2, index + 2).value(day).style(headerStyle);
                        });

                        // Obtener las filas de la tabla
                        const filas2 = tabla2.querySelectorAll('tr'); //Obtener los tr de la tabla

                        var formulaTabla2VentasAcumuladas = "";
                        filas2.forEach((fila, rowIndex) => {
                            //Recorrido de filas de la tabla

                            const celdas = fila.querySelectorAll('td');
                            let columnIndex = 0; // Variable para rastrear la columna actual
                            let occupiedColumns = []; // Columnas ocupadas por celdas con colspan

                            celdas.forEach((celda) => {
                                //Recorrido de celdas de la tabla

                                const colspan = celda.getAttribute('colspan');
                                const rowspan = celda.getAttribute('rowspan');

                                let textContent = celda.textContent;

                                if (celda.textContent.length > 0) {
                                    //Texto de la celda es diferente de vacio
                                    textContent = celda.textContent.startsWith('$') ? Number(celda.textContent.substring(1)) : celda.textContent; // Convertir el valor con signo "$" a número
                                    textContent = !isNaN(Number(textContent)) ? Number(textContent) : textContent; //Quitar cualquier formato raro de la celda
                                }

                                const cellAddress = sheet2.cell(rowIndex + 1, columnIndex + 1).address(); //Se obtiene el address de la celda ejemplo A1, E15, G10 etc

                                // Agregar contenido a las celdas
                                for (let i = 0; i < (colspan ? parseInt(colspan) : 1); i++) {
                                    for (let j = 0; j < (rowspan ? parseInt(rowspan) : 1); j++) {
                                        sheet2.cell(rowIndex + j + 1, columnIndex + i + 1).value(textContent).style(bodyStyle);

                                        // Pintar las celdas con colspan de un color específico
                                        if (colspan && !occupiedColumns.includes(columnIndex + i)) {
                                            sheet2.cell(rowIndex + j + 1, columnIndex + i + 1).style(headerStyle);
                                            occupiedColumns.push(columnIndex + i);
                                        }

                                        if (columnIndex >= 1 && columnIndex <= 6) {
                                            //Columna de la B a la G
                                            formulaTabla2VentasAcumuladas += cellAddress + ":"; //Creacion de formula
                                        }

                                        if (columnIndex == 7) {
                                            //Columna H
                                            sheet2.cell(rowIndex + j + 1, columnIndex + i + 1).formula("=SUM(" + formulaTabla2VentasAcumuladas.slice(0, formulaTabla2VentasAcumuladas.length - 1) + ")");
                                            formulaTabla2VentasAcumuladas = ""; //Vaciar formula
                                        }
                                    }
                                }

                                //Pintar fila de encabezado ASISTENTES
                                if (celda.textContent.split('\n').join('').replace(/\s+/g, '') === "ASISTENTES") {
                                    const colspanRange = sheet2.range(rowIndex + 1, 1, rowIndex + 1, 14); //Fila donde esta el encabezado de ASISTENTES
                                    colspanRange.style(headerStyle); // Aplicar el estilo del encabezado
                                }

                                columnIndex += colspan ? parseInt(colspan) : 1;
                            });
                        });

                    }

                    // Generar el archivo de Excel en formato Blob
                    return workbook.outputAsync('blob');
                })
                .then(blob => {
                    // Crear un objeto URL para el Blob
                    const url = window.URL.createObjectURL(blob);

                    // Crear un enlace para descargar el archivo de Excel
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = nombreArchivo;
                    a.click();

                    // Liberar el objeto URL
                    window.URL.revokeObjectURL(url);
                })
                .catch(error => {
                    console.error(error);
                });
        }

    </script>
