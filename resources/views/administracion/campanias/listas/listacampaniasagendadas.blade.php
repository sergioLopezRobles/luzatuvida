<table id="tablaCampañas" class="table-bordered table-striped table-general table-sm">
    <thead>
    <tr>
        <th  style =" text-align:center;" scope="col">INDICE</th>
        <th  style =" text-align:center;" scope="col">ESTADO</th>
        <th  style =" text-align:center;" scope="col">CODIGO</th>
        <th  style =" text-align:center;" scope="col">NOMBRE</th>
        <th  style =" text-align:center;" scope="col">TELEFONO</th>
        <th  style =" text-align:center;" scope="col">REFERENCIA</th>
        <th  style =" text-align:center;" scope="col">FECHA QUE AGENDO</th>
        <th  style =" text-align:center;" scope="col">FECHA DE VENTA</th>
        <th  style =" text-align:center;" scope="col">OBSERVACIONES</th>
        <th  style =" text-align:center;" scope="col">ACTUALIZAR</th>
    </tr>
    </thead>
    <tbody>
    @if(count($listaCampaniasAgendadas) > 0)

        @foreach($listaCampaniasAgendadas as $campaniaAgendada)
            <tr>
                <th style="font-size: 11px; text-align: center; vertical-align: middle">{{$indice = $indice - 1}}</th>
                @if($campaniaAgendada->estado  == 1)
                    <td align='center'><i class='fas fa-check' style="color:#9be09c;font-size:25px;"></i></td>
                @else
                    <td align='center'><i class='fas fa-times' style="color:#ffaca6;font-size:25px;"></i></td>
                @endif
                <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$campaniaAgendada->id_campania}}</td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$campaniaAgendada->nombre}}</td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$campaniaAgendada->telefono}}</td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$campaniaAgendada->referencia}}</td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$campaniaAgendada->created_at}}</td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle">{{$campaniaAgendada->updated_at}}</td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle; white-space: normal;">{{$campaniaAgendada->observaciones}}</td>
                <td style="font-size: 11px; text-align: center; vertical-align: middle">
                    @if($campaniaAgendada->estado == 0)
                        <a class="btn btn-outline-success btnActualizarCupon btn-sm" data-toggle="modal" data-target="#modalActualizarDatosCupon"
                           data_parametros_modal="{{$campaniaAgendada->indice . "," . $campaniaAgendada->nombre . "," . $campaniaAgendada->telefono . "," . substr($campaniaAgendada->referencia, 5, 5)  . "," . $campaniaAgendada->observaciones}}">
                            <i class="bi bi-pencil-fill"></i></a>
                    @endif
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td align='center' colspan="10" style="font-size: 10px;">Sin registros</td>
        </tr>
    @endif
    </tbody>
</table>

<!-- Modal de actualizar datos registro cupon campania-->
<div class="modal fade" id="modalActualizarDatosCupon"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  aria-hidden="true">
    <form action="{{route('actualizardatospaciente')}}" enctype="multipart/form-data"
          method="POST" onsubmit="btnSubmit.disabled = true;">
        @csrf
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #0AA09E; color: white;">
                    <h5> Actualizar datos</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="idCampana" id="idCampana" value="{{$campaniaAgendada->id_campania}}">
                    <input type="hidden" class="form-control" name="idCuponCampana" id="idCuponCampana">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="text" name="nombrePaciente" id="nombrePaciente" class="form-control {!! $errors->first('nombrePaciente','is-invalid')!!}" placeholder="Nombre paciente"
                                       value="{{old('nombrePaciente')}}">
                                {!! $errors->first('nombrePaciente','<div class="invalid-feedback">Ingresa el nombre del paciente.</div>')!!}
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" name="telefonoPaciente" id="telefonoPaciente" class="form-control {!! $errors->first('telefonoPaciente','is-invalid')!!}" placeholder="Teléfono"
                                       value="{{old('telefonoPaciente')}}">
                                {!! $errors->first('telefonoPaciente','<div class="invalid-feedback">Ingresa el número de teléfono</div>')!!}
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>Numero de referencia</label>
                                <input type="text" name="numReferencia" id="numReferencia" class="form-control {!! $errors->first('numReferencia','is-invalid')!!}" placeholder="Numero de referencia"
                                       value="{{old('numReferencia')}}" @if($campaniaAgendada->tiporeferencia == 1) readonly @endif>
                                {!! $errors->first('numReferencia','<div class="invalid-feedback">Ingresa el número de referencia</div>')!!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea  rows="3" name="observaciones" id="observaciones" class="form-control" style="text-transform: uppercase"
                                           placeholder="Observaciones"> {{old('observaciones')}} </textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-outline-success btn-ok" name="btnSubmit" type="submit">Actualizar</button>
                </div>
            </div>
        </div>
    </form>
</div>
