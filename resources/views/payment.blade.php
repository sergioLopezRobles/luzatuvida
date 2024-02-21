@extends('layouts.app')
@section('titulo','Contratos'){{-- Corresponde al Titulo de la pestaña--}}
@section('content')
    @include('parciales.notificaciones')

<h2>PAGO CON TARJETA</h2>
<link rel="stylesheet" href="{{ asset('/css/style.css') }}" />
<div class="card"  >
    <div class="card-body">
        <form action="{{route('charge',[$idFranquicia,$idContrato,$abono,$banderacase,$nuevoabono,$cantidadsubscripcion])}}" enctype="multipart/form-data" method="GET"
              id="payment-form" onsubmit="btnSubmit.disabled = true;">
            @csrf
            <div class="form-row i-am-centered">
                <div class="row">
                @if($nuevoabono > 0)
                  <h4>El total del contrato es de $ {{$contrato[0]->total}}, se pagara en abonos la cantidad: {{number_format($contrato[0]->total / $cantidadsubscripcion, 2, ".", "")}}</h4>
                  @endif
                  <div class="col-12">
                            <input type="text" class="form-control" name="amount" placeholder="CANTIDAD"
                             readonly value="@if($nuevoabono > 0) {{$nuevoabono}} @else {{$abono}} @endif"/>
                        </div>
                        &nbsp;
                        <div class="col-12">
                            <input type="email" class="form-control" name="email" placeholder="CORREO (OPCIONAL)" />
                        </div>

                        <div class="col-12">
                            <br>
                            <br>
                            <label  for="card-element">
                            TARJETA DE DEBITO O CREDITO:
                            </label>
                            &nbsp;
                            <div class="form-control" id="card-element" ></div>

                            <!-- Used to display form errors. -->
                            <div id="card-errors" role="alert"></div>
                            <input type="hidden" class="form-control" name="paymentMethod" id="paymentMethod">
                        </div>
                        <div class="col-12">
                        </div>
                        <br>
                        <div class="col-2">
                         <a type="button" class="btn btn-outline-success i-am-centered" href="{{route('vercontrato',[$idFranquicia,$idContrato])}}">CANCELAR</a>
                        </div>
                       <div class="col-5">
                        <button class="btn btn-outline-success i-am-centered"  name="btnSubmit" id="payButton" >REALIZAR PAGO</button>
                        {{ csrf_field() }}
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
var publishable_key = '{{ env('STRIPE_KEY') }}';
</script>
<script src="{{ asset('js/card.js') }}"></script>

@endsection
