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
                <h1 style="color: black;"><b>ESTADO DE CUENTA</b></h1>
            </td>
         </tr>
 	 <tr>
	<td align="left">
                <h2 style="color: black;">Estimado(a): <b>{{$datos['nombrecliente']}}</b></h2>
    </td>
  	</tr>
   	 <tr>
            <td style="text-align: justify;padding: 10px;">
                Información sobre tu estado de cuenta:
            </td>
		<hr>
        </tr>
	<tr>
	 <td>
             <h3 style="color: black;"> <b>Folio del contrato:</b></h3>
             <b>{{$datos['idcontrato']}}</b>
	</td>
 	<td>
             <h3 style="color: black;margin-right: 40%;"> <b>Fecha de entrega:</b></h3>
             <b>{{$datos['entrega']}}</b>
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
        <th style=" text-align:center;background-color:#0AA09E;color:#FFFFFF;" scope="col">CANTIDAD</th>
    </tr>
	<tr>
 	     <td align='center' style="text-transform: uppercase;">SALDO ANTERIOR</td>
         <td align='center' style="text-transform: uppercase;">$ {{$datos['saldoanteriro']}}</td>
	</tr>
    <tr>
 	     <td align='center' style="text-transform: uppercase;">ABONOS AGREGADOS</td>
         <td align='center' style="text-transform: uppercase;">$ {{$datos['abonos']}}</td>
	</tr>
	<tr>
         <td align='center' style="color:#0AA09E;text-transform: uppercase">TOTAL</td>
         <td align='center' style="color:#0AA09E;text-transform: uppercase" colspan="2">$ {{$datos['total']}}</td>
	</tr>
    </table>
<h2 style="color: black;">Para dudas y aclaraciones: <b>{{$datos['telefonofranquicia']}}</b></h2>
</div>
</body>

</html>
