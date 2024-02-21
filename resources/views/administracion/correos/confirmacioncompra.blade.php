<html>
<head>
    <title>Correo de prueba</title>
    <meta charset="utf-8">
</head>
<body>
<div>
<table width="100%" style="box-shadow: 3px 3px 10px #AAA;max-width:600px;">
        <tr>
            <td style="display:block;margin-left:auto;margin-right:auto;width:30%;">
              <a  href="{{ url('/') }}"><img id="logo" src={{asset("imagenes/general/administracion/logo.png")}}></a>
            </td>
        </tr>
        <tr>
            <td align="right">
                <br>
                <h1 style="color: black;"><b>¡GRACIAS!</b></h1>
            </td>
         </tr>
 	 <tr>
	<td align="left">
                <h2 style="color: black;">Estimado(a): <b>{{$datos['nombrecliente']}}</b></h2>
    </td>
  	</tr>
 	<tr>
	<td align="left">
                <h4 style="color: black;"><b>El equipo de luz a tu vida agradece tu compra!</b></h4>
            </td>
  	</tr>
   	 <tr>
            <td style="text-align: justify;padding: 10px;">
                Información sobre tu pedido:
            </td>
		<hr>
        </tr>
	<tr>
	 <td>
             <h3 style="color: black;"> <b>Folio del contrato:</b></h3>
             <b>{{$datos['idcontrato']}}</b>
	</td>
 	<td>
             <h3 style="color: black;margin-right: 40%;"> <b>Fecha de pedido:</b></h3>
             <b>{{$datos['creacion']}}</b>
	</td>
  	</tr>
	<tr>
	 <td>
             <h3 style="color: black;"> <b>Fecha de entrega:</b></h3>
             <b>{{$datos['entrega']}}</b>
	</td>
 	<td>
             <h3 style="color: black;margin-right: 40%;"> <b>Sucursal:</b></h3>
             <b>{{$datos['colonia']}}, {{$datos['numero']}}, {{$datos['ciudad']}}, {{$datos['estado']}}</b>
	</td>
  	</tr>
 	 <tr>
            <td style="text-align: justify;padding: 10px;">
                Datos:
            </td>
		<hr>
        </tr>
<br>
  	<tr>
     	 <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">DESCRIPCIÓN</th>
         <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">TIPO PRODUCTO</th>
	<th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">PRECIO</th>
      </tr>
	<tr>
 	 <td align='center' style="text-transform: uppercase;">{{$datos['nombrepaquete']}}</td>
         <td align='center' style="text-transform: uppercase;"></td>
         <td align='center' style="text-transform: uppercase;">$ {{$datos['historial']}}</td>
	</tr>
    @if($datos['productos'] != null)
      @foreach($datos['productos'] as $pro)
        <tr>
             <td align='center' style="text-transform: uppercase;">{{$pro->nombre}}</td>
             <td align='center' style="text-transform: uppercase;">{{$pro->tipo}}</td>
             <td align='center' style="text-transform: uppercase;">$ {{$pro->precio}}</td>
        </tr>
        @endforeach
    @endif
	<tr>
 	 <td align='center' style="text-transform: uppercase;">PROMOCION: @if($datos['promoenganche'] > 0) ENGANCHE @endif
          {{$datos['promocionnombre']}}</td>
         <td align='center' style="text-transform: uppercase;"></td>
         <td align='center'style="text-transform: uppercase;">@if($datos['promoenganche'] > 0) -$100 @endif
            @if($datos['promo'] != null) -$ @endif {{$datos['promo']}}</td>
	</tr>
	<tr>
 	 <td align='center' style="text-transform: uppercase;">Abonos</td>
         <td align='center' style="text-transform: uppercase;"></td>
         <td align='center'  style="text-transform: uppercase;">@if($datos['totalabono'] != null) -$ @endif {{$datos['totalabono']}}</td>
	</tr>
	<tr>
 	 <td align='center' colspan="1" style="text-transform: uppercase;"></td>
         <td align='center' style="color:#0AA09E;text-transform: uppercase">TOTAL</td>
         <td align='center' style="color:#0AA09E;text-transform: uppercase" colspan="2">$ {{$datos['total']}}</td>
	</tr>
    </table>
<h2 style="color: black;">Para dudas y aclaraciones: <b>{{$datos['telefonofranquicia']}}</b></h2>
</div>
</body>

</html>
