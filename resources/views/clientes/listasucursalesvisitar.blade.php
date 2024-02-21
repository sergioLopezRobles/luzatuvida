@if($franquicias != null)
    @foreach($franquicias as $franquicia)
        @if($franquicia->id != $idFranquicia)
            <div class="col-4 mb-3" style="display: grid; justify-items: center;">
                <p style="text-align: center; font-family: serif; text-transform: uppercase; font-size: 16px;">SUCURSAL <br> {{$franquicia->ciudad}}, {{$franquicia->estado}} <br> CALLE {{$franquicia->calle}} @if($franquicia->numero != null) NUMERO {{$franquicia->numero}} @endif
                    <br> COLONIA {{$franquicia->colonia}} <br> @if($franquicia->telefonoatencionclientes != null) TEL: {{$franquicia->telefonoatencionclientes}} @endif
                    <br> @if($franquicia->whatsapp != null) WHATSAPP: {{$franquicia->whatsapp}} @endif</p>
            </div>
        @else
            <div class="col-4 mb-3">
                <p style="text-align: center; font-family: serif; font-weight: bold; text-transform: uppercase; font-size: 16px;">MATRIZ <br> TEPIC, NAYARIT <br> CALLE DURANGO NORTE  #357 <br> COLONIA CENTRO <br> TEL: 3113429347 <br> WHATSAPP: 3223841987</p>
            </div>
        @endif
    @endforeach
@endif
