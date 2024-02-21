@if(sizeof($cuentasColonia) > 0)
    <form action="{{route('actualizartraspasarcontratozona',[Auth::user()->rol_id == 7 ? $franquiciaSeleccionada : $idFranquicia, $zonaSeleccionada])}}" enctype="multipart/form-data" method="GET"
          onsubmit="btnSubmit.disabled = true;">
        <div style="color: #0AA09E; font-weight: bold;">Paso 2 - Selecciona las colonias (Recomendación de 5 en 5).</div>
        <div class="row" style="margin-top: 5px; margin-bottom: 5px;">
            <div class="col-6">
                <div @if(sizeof($cuentasColonia) < 5) style="margin-top: 30px; margin-left: 10px;" @else style="margin-top: 30px; margin-left: 10px; max-height: 300px; overflow-y: scroll;" @endif>
                    <table class="table table-bordered table-striped" style="text-align: center; position: relative; border-collapse: collapse;" id="tablaCuentasColonia">
                        <thead>
                        <tr>
                            <th style=" text-align:center; font-size: 12px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col"
                                colspan="1"><b>CUENTAS POR COLONIA</b></th>
                            <th style=" text-align:center; font-size: 12px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col"
                                colspan="1"><b>NUMERO CONTRATOS</b></th>
                            <th style=" text-align:center; font-size: 12px; background: white; position: sticky; top: 0; box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);" scope="col"
                                colspan="1"><b>SELECCIONAR</b></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cuentasColonia as $i => $cuentaColonia)
                            <tr>
                                <td align='center' style="font-size: 10px;">{{$i}}</td>
                                <td align='center' style="font-size: 10px;">{{$cuentaColonia}}</td>
                                <td align='center'>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="check{{str_replace('.', '', str_replace(' ', '', $i))}}"
                                               id="customCheck{{str_replace('.', '', str_replace(' ', '', $i))}}">
                                        <label class="custom-control-label"
                                               for="customCheck{{str_replace('.', '', str_replace(' ', '', $i))}}"></label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <hr>
            </div>
        </div>
        <div style="color: #0AA09E; font-weight: bold;">Paso 3 - Selecciona la zona a donde quieres mandar esos contratos de las colonias seleccionadas.</div>
        <div class="row">
            <div class="col-6">
                <label for="zonas">Seleccionar zona a traspasar</label>
                <div class="form-group">
                    <select name="zonaTraspasoSeleccionada"
                            class="form-control"
                            id="zonaTraspasoSeleccionada">
                        @if(count($zonas) > 0)
                            @foreach($zonas as $zona)
                                <option
                                    value="{{$zona->id}}">{{$zona->zona}}
                                </option>
                            @endforeach
                        @else
                            <option selected>Sin registros</option>
                        @endif
                    </select>
                </div>
            </div>
        </div>
        <div style="color: #0AA09E; font-weight: bold;">Paso 4 - Presionar boton cambiar zona.</div>
        <div class="row">
            @if($hoyNumero != 1)
                <div class="col-6" style="color: #ea9999; font-weight: bold;">Solo se puede traspasar los días lunes.</div>
            @else
                <div class="col-6">
                    <button type="submit" name="btnSubmit" class="btn btn-outline-success btn-block" style="margin-top: 10px;">Cambiar zona</button>
                </div>
            @endif
        </div>
    </form>
@endif

