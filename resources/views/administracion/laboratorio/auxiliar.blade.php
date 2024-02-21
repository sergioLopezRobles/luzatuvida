@extends('layouts.app')
@section('titulo','Contratos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    @include('parciales.notificaciones')

    <form action="{{route('auxiliarlaboratorio')}}" enctype="multipart/form-data" method="GET" onsubmit="btnSubmit.disabled = true;">
        <div class="row">
            <div class="col-4">
                <input name="filtro" type="text" class="form-control" placeholder="Buscar..">
            </div>
            <div class="col-5">
                <button type="submit" name="btnSubmit" class="btn btn-outline-success">Filtrar</button>
            </div>
        </div>
    </form>
    @php($contador = 1)
    @if(isset($historiales))
        @foreach($historiales as $historial)
            <div class="row">
                <div class="col-2">
                    <h3 style="margin-top: 10px;">@lang('mensajes.mensajetituloreceta') {{$loop->iteration}}</h3>
                </div>
            </div>
            <div id="mostrarvision"></div>
            <h6>Ojo derecho</h6>
            <div class="row">
                <div class="col-2">
                    <div class="form-group">
                        <label>Esferico</label>
                        <input type="text" name="esfericod" class="form-control" readonly value="{{$historial->esfericoder}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Cilindro</label>
                        <input type="text" name="cilindrod" class="form-control" readonly value="{{$historial->cilindroder}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Eje</label>
                        <input type="text" name="ejed" class="form-control" readonly value="{{$historial->ejeder}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Add</label>
                        <input type="text" name="addd" class="form-control" readonly value="{{$historial->addder}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>ALT</label>
                        <input type="text" name="altd" class="form-control" readonly value="{{$historial->altder}}">
                    </div>
                </div>
            </div>
            <h6>Ojo Izquierdo</h6>
            <div class="row">
                <div class="col-2">
                    <div class="form-group">
                        <label>Esferico</label>
                        <input type="text" name="esfericod2" class="form-control" readonly value="{{$historial->esfericoizq}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Cilindro</label>
                        <input type="text" name="cilindrod2" class="form-control" readonly value="{{$historial->cilindroizq}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Eje</label>
                        <input type="text" name="ejed2" class="form-control" readonly value="{{$historial->ejeizq}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Add</label>
                        <input type="text" name="addd2" class="form-control" readonly value="{{$historial->addizq}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>ALT</label>
                        <input type="text" name="altd2" class="form-control" readonly value="{{$historial->altizq}}">
                    </div>
                </div>
            </div>
            <h6>Material</h6>
            <div class="row">
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="material" id="material" @if($historial->material == 0) checked @endif onclick="return false;">
                        <label class="form-check-label" for="material">Hi Index</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="material" id="material" @if($historial->material == 1) checked @endif onclick="return false;">
                        <label class="form-check-label" for="material">CR</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="material" id="material" @if($historial->material == 2) checked @endif onclick="return false;">
                        <label class="form-check-label" for="material">Policarbonato</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-check">
                        <input  class="form-check-input" type="radio" name="material" id="material" @if($historial->material == 3) checked @endif onclick="return false;">
                        <label class="form-check-label" for="material">Otro</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-check">
                        <input type="text" name="motro" class="form-control" placeholder="Otro" value="{{$historial->materialotro}}" readonly>
                    </div>
                </div>
            </div>
            <h6>Tipo de bifocal</h6>
            <div class="row">
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" @if($historial->bifocal == 0) checked @endif onclick="return false;">
                        <label class="form-check-label" for="exampleRadios1">
                            FT
                        </label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" @if($historial->bifocal == 1) checked @endif onclick="return false;">
                        <label class="form-check-label" for="exampleRadios1">
                            Blend
                        </label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" @if($historial->bifocal == 2) checked @endif onclick="return false;">
                        <label class="form-check-label" for="exampleRadios1">
                            Progresivo
                        </label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bifocal" id="exampleRadios1" @if($historial->bifocal == 3) checked @endif onclick="return false;">
                        <label class="form-check-label" for="exampleRadios1">
                            N/A
                        </label>
                    </div>
                </div>
            </div>
            <h6>Tratamiento</h6>
            <div class="row">
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input " name="fotocromatico" id="customCheck9"  @if($historial->fotocromatico == 1) checked @endif onclick="return false;">
                        <label class="custom-control-label" for="customCheck9">Fotocromatico</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input " name="ar" id="customCheck10" @if($historial->ar == 1) checked @endif onclick="return false;">
                        <label class="custom-control-label" for="customCheck10">A/R</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="tinte"  id="customCheck11" @if($historial->tinte == 1) checked @endif onclick="return false;">
                        <label class="custom-control-label" for="customCheck11">Tinte</label>
                    </div>
                </div>
                <div class="col-1">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="blueray" id="customCheck12"  @if($historial->blueray == 1) checked @endif onclick="return false;">
                        <label class="custom-control-label" for="customCheck12">BlueRay</label>
                    </div>
                </div>
                <div class="col-1">
                    <div class="custom-control custom-checkbox">
                        <input  class="custom-control-input " type="checkbox" name="otroTra" id="customCheck13" @if($historial->otroT == 1) checked @endif onclick="return false;">
                        <label class="custom-control-label" for="customCheck13">Otro</label>
                    </div>
                </div>
                <div class="col-2">
                    <div class="custom-control custom-checkbox">
                        <input type="text" name="otroT" class="form-control" min="0"  placeholder="Otro" value="{{$historial->tratamientootro}}" readonly>
                    </div>
                </div>
            </div>
            <hr>
        @endforeach
    @else
        <div class="row">
            <div class="col-3">
                <h3 style="margin-top: 10px;">(Sin resultados)</h3>
            </div>
        </div>
    @endif
@endsection
