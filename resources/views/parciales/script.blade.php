<!-- Scripts -->
<script src="{{ asset('js/app.js') }}" defer></script>
<script src="{{ asset('js/all.js') }}" defer></script>
<script src="{{ asset('js/global.js') }}" defer></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://js.stripe.com/v3/"></script>


@if(Route::is('nuevafranquicia') && Auth::user()->rol_id == 7)
<script src="{{ asset('js/administracion/franquicias/nueva.js') }}" defer></script>
@endif

@if(Route::is('listafranquicia') && Auth::user()->rol_id == 7)
  <script src="{{ asset('js/administracion/franquicias/lista.js') }}" defer></script>
@endif
@if((Route::is('usuariosFranquicia') || Route::is('cobranzamovimientos') || Route::is('usuariosfiltrosucursal') || Route::is('vercontrato') || Route::is('eliminarUsuarioFranquicia'))
    && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8))
    <script src="{{ asset('js/administracion/franquicias/usuarios.js') }}" defer></script>
@endif
@if(Route::is('payment'))
  <script src="{{ asset('js/card.js') }}" defer></script>
@endif
@if(Route::is('editarUsuarioFranquicia') && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8))
  <script src="{{ asset('js/administracion/franquicias/editarusuario.js') }}" defer></script>
@endif

@if(Route::is('estadolaboratorio') && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 16))
    <script src="{{ asset('js/administracion/laboratorio/estadolaboratorio.js') }}" defer></script>
@endif

@if(Route::is('nuevohistorialclinico') || Route::is('vercontrato') || Route::is('nuevohistorialclinico2') || Route::is('nuevocontrato2') || Route::is('contratoHijos') ||
    Route::is('nuevohistorialclinico2') || Route::is('crearhistorialclinico2'))
    <script src="{{ asset('js/administracion/contratos/nuevocontrato.js') }}" defer></script>
    <script src="{{ asset('js/administracion/contratos/ticketabonosucursal.js') }}" defer></script>
    <script src="{{ asset('js/administracion/laboratorio/ticketsolicitudarmazon.js') }}" defer></script>
@endif

@if(Route::is('listacontratoscuentasactivas') || Route::is('filtrarlistacontratoscuentasactivas') || Route::is('cobranzamovimientos') && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 6))
    <script src="{{ asset('js/administracion/googlemaps/marcadoresContratos.js') }}" defer></script>
    <script src="{{ asset('js/administracion/reportes/cuentasactivas.js') }}" defer></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{config('googlemap')['map_apikey']}}&v=3"></script>
    <script src="https://kit.fontawesome.com/3ddf490e9c.js" crossorigin="anonymous"></script>
    <script src="{{asset('js/administracion/exportardocumento/exportarExcel.js') }}" defer></script>
@endif

@if((Route::is('cobranzamovimientos') || Route::is('llamadascobranza') || Route::is('cobranzamovimientosvalidacioncorte')) &&
    (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8))
    <script src="{{ asset('js/administracion/movimientos/cobranza.js') }}" defer></script>
@endif

@if((Route::is('usuariosFranquicia') || Route::is('usuariosfiltrosucursal')) && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8))
    <script src="{{ asset('js/administracion/franquicias/usuariosinfranquicia.js') }}" defer></script>
@endif

@if(Route::is('listacontratospaquetes') && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8))
    <script src="{{ asset('js/administracion/reportes/paquetes.js') }}" defer></script>
    <script src="{{asset('js/administracion/exportardocumento/exportarExcel.js') }}" defer></script>
@endif

@if(Route::is('listaconfirmaciones') && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 15 || Auth::user()->rol_id == 8))
    <script src="{{ asset('js/administracion/confirmaciones/confirmacioneslaboratorio.js') }}" defer></script>
@endif

@if(Route::is('listalaboratorio') && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 16))
    <script src="{{ asset('js/administracion/laboratorio/contratosenviadostiemporeal.js') }}" defer></script>
@endif

@if((Route::is('estadoconfirmacion') && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 15 || Auth::user()->rol_id == 8)))
    <script src="{{ asset('js/administracion/confirmaciones/estadoconfirmacion.js') }}" defer></script>
    <script src="{{ asset('js/administracion/historialclinico/historialclinico.js') }}" defer></script>
    <script src="{{ asset('js/administracion/franquicias/usuarios.js') }}" defer></script>
@endif

@if((Route::is('listacontratospagados') || Route::is('listacontratospagadosseguimiento') || Route::is('filtrarlistacontratospagadosseguimiento') ||
    Route::is('reportecontratossupervision') || Route::is('reportecontratossupervisionfiltrar')) && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 6))
    <script src="{{ asset('js/administracion/googlemaps/marcadoresContratos.js') }}" defer></script>
    <script src="{{ asset('js/administracion/reportes/pagados.js') }}" defer></script>
    <script src="{{ asset('js/administracion/reportes/seguimientopacientes.js') }}" defer></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{config('googlemap')['map_apikey']}}&v=3"></script>
    <script src="https://kit.fontawesome.com/3ddf490e9c.js" crossorigin="anonymous"></script>
    <script src="{{asset('js/administracion/exportardocumento/exportarExcel.js') }}" defer></script>
@endif

@if(Route::is('listacontratoscancelados') && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 6))
    <script src="{{ asset('js/administracion/googlemaps/marcadoresContratos.js') }}" defer></script>
    <script src="{{ asset('js/administracion/reportes/cancelados.js') }}" defer></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{config('googlemap')['map_apikey']}}&v=3"></script>
    <script src="https://kit.fontawesome.com/3ddf490e9c.js" crossorigin="anonymous"></script>
    <script src="{{asset('js/administracion/exportardocumento/exportarExcel.js') }}" defer></script>
@endif

@if(Route::is('editarUsuarioFranquicia'))
    <script src="{{ asset('js/administracion/franquicias/vistaprevia.js') }}" defer></script>
@endif

@if((Route::is('filtrarlistacontratosreportes') || Route::is('listacontratosreportes')) && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8 ||
     Auth::user()->rol_id == 6 || Auth::user()->rol_id == 15))
    <script src="{{asset('js/administracion/exportardocumento/exportarExcel.js') }}" defer></script>
@endif

@if((Route::is('crearpoliza') || Route::is('verpoliza')) && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8))
    <script src="{{ asset('js/administracion/poliza/crearcargarpoliza.js') }}" defer></script>
@endif

@if((Route::is('reportemovimientos') || Route::is('usuariosreportemovimiento')) && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8))
    <script src="{{ asset('js/administracion/reportes/movimientos.js') }}" defer></script>
@endif

@if((Route::is('reportegraficas')) && (Auth::user()->rol_id == 7))
    <script src="{{ asset('js/administracion/reportes/graficas.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.8.0/dist/chart.min.js"></script>
@endif

@if((Route::is('listavehiculos')) && (Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 ||Auth::user()->rol_id == 8))
    <script src="{{ asset('js/administracion/vehiculos/vehiculos.js') }}" defer></script>
    <script src="https://kit.fontawesome.com/3ddf490e9c.js" crossorigin="anonymous"></script>
@endif

@if((Route::is('vervehiculo')) && (Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 ||Auth::user()->rol_id == 8))
    <script src="{{ asset('js/administracion/franquicias/vistaprevia.js') }}" defer></script>
@endif

@if((Route::is('reportellamadas')) && (Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 ||Auth::user()->rol_id == 8))
    <script src="{{ asset('js/administracion/reportes/llamadas.js') }}" defer></script>
@endif

@if((Route::is('traspasarcontrato') || Route::is('buscarcontratotraspasar') || Route::is('obtenercontratotraspasar')) && (Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 ||
     Auth::user()->rol_id == 8 || Auth::user()->rol_id == 15))
    <script src="{{ asset('js/administracion/contratos/traspasarcontratos.js') }}" defer></script>
@endif

@if((Route::is('reportecontratos')) && (Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 15))
    <script src="{{ asset('js/administracion/contratos/reportecontratos.js') }}" defer></script>
@endif

@if((Route::is('listareporteasistencia')) && (Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 ||Auth::user()->rol_id == 8))
    <script src="{{ asset('js/administracion/reportes/asistencia.js') }}" defer></script>
    <script src="{{asset('js/administracion/exportardocumento/exportarExcel.js') }}" defer></script>
@endif

@if(Route::is('login'))
    <script src="{{ asset('js/administracion/login.js') }}" defer></script>
@endif

@if((Route::is('reportebuzon')) && (Auth::user()->rol_id == 7))
    <script src="{{ asset('js/administracion/reportes/buzon.js') }}" defer></script>
@endif

@if((Route::is('listareportehistorialsucursal')) && (Auth::user()->rol_id == 7))
    <script src="{{ asset('js/administracion/reportes/movimientossucursal.js') }}" defer></script>
@endif

@if(Route::is('traspasarcontratozona') && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 6))
    <script src="{{ asset('js/administracion/contratos/traspasarcontratozona.js') }}" defer></script>
@endif

@if(Route::is('contratoactualizar') || Route::is('contratoeditar'))
    <script src="{{ asset('js/administracion/contratos/actualizarcontrato.js') }}" defer></script>
    <script src="{{ asset('js/administracion/historialclinico/historialclinico.js') }}" defer></script>
    <script src="{{ asset('js/administracion/franquicias/vistaprevia.js') }}" defer></script>
@endif

@if((Route::is('listavacantesredes') || Route::is('filtrarlistavacantesredes') || Route::is('listavacantesadministracion')
    || Route::is('filtrarsolicitudesvacantesadmin') || Route::is('reportevacantesmensajes')) && (Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8 ||
    Auth::user()->rol_id == 18 || Auth::user()->rol_id == 20))
    <script src="{{ asset('js/administracion/franquicias/agendarcitavacante.js') }}" defer></script>
@endif

@if(Route::is('listaagendacitas'))
    <script src="{{ asset('js/administracion/citas/calendarioAdministracion.js') }}" defer></script>
    <script src="{{ asset('js/administracion/citas/agendarCitaAdministracion.js') }}" defer></script>
    <script src="{{ asset('js/clientes/formatotelefono.js') }}" defer></script>
@endif

@if((Route::is('listacampanias') || Route::is('vercampania') || Route::is('agendarcampania')) && (Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 ||
     Auth::user()->rol_id == 8 || Auth::user()->rol_id == 20))
    <script src="{{ asset('js/administracion/campanias/agendarcampanias.js') }}" defer></script>
@endif

@if((Route::is('listacontrato') || Route::is('filtrarlistacontrato') || Route::is('filtrarlistacontratocheckbox')) && (Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8))
    <script src="{{ asset('js/administracion/contratos/listacontratos.js') }}" defer></script>
@endif

@if((Route::is('reportemovimientoscontratos') || Route::is('reportemovimientoscontratosfiltrar')) && (Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8))
    <script src="{{ asset('js/administracion/reportes/movimientoscontratos.js') }}" defer></script>
    <script src="{{asset('js/administracion/exportardocumento/exportarExcel.js') }}" defer></script>
@endif

<!-- Rutas de confirmaciones cerrar alertas en automatico-->
@if(Route::is('listaconfirmaciones') || Route::is('estadoconfirmacion') || Route::is('estadoconfirmacionactualizar') || Route::is('comentarioconfirmacion') ||
    Route::is('confirmacionesagregardocumentos') || Route::is('actualizarContratoConfirmaciones') || Route::is('actualizarTotalContratoConfirmaciones') ||
    Route::is('rechazarContratoConfirmaciones') || Route::is('observacioninternalaboratoriohistorial') || Route::is('actualizarfechaentregaconfirmaciones') ||
    Route::is('listagarantiasconfirmaciones') || Route::is('vercontratogarantiaconfirmaciones') || Route::is('cancelarGarantiaHistorialConfirmaciones') ||
    Route::is('listaconfirmacioneslaboratrio') || Route::is('agregarproductoconfirmaciones') || Route::is('listaconfirmacionesgarantiasprincipal') ||
    Route::is('actualizardiagnosticoconfirmaciones') || Route::is('actualizarformapagoconfirmaciones') || Route::is('restablecercontratoconfirmaciones') ||
    Route::is('actualizarpaquetehistorialconfirmaciones') || Route::is('agregarnotaconfirmaciones') || Route::is('actualizararmazonconfirmaciones') ||
    Route::is('solicitarautorizacionabonominimoconfirmaciones') || Route::is('agregarabonominimoconfirmaciones') || Route::is('listaconfirmacionesrechazadosprincipal'))
    <script src="{{ asset('js/administracion/confirmaciones/alertas.js') }}" defer></script>
@endif

@if(Route::is('listasolicitudautorizacion') && (Auth::user()->rol_id == 7 || Auth::user()->rol_id == 16))
    <script src="{{ asset('js/administracion/laboratorio/ticketsolicitudarmazon.js') }}" defer></script>
    <script src="{{ asset('js/administracion/contratos/autorizaciones.js')}}" defer></script>
@endif

@if((Route::is('listasfranquicia') || Route::is('tratamientoactualizar')) && (Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 ||Auth::user()->rol_id == 8))
    <script src="{{ asset('js/administracion/franquicias/administracion.js') }}" defer></script>
@endif

@if((Route::is('reporteabonossucursal') || Route::is('filtrarreporteabonossucursal')) && (Auth::user()->rol_id == 7))
    <script src="{{asset('js/administracion/exportardocumento/exportarExcel.js') }}" defer></script>
@endif

@if((Route::is('listareporteproductos') || Route::is('listareporteproductosfiltrar') || Route::is('reporteabonossucursal') ||
     Route::is('filtrarreporteabonossucursal')) && (Auth::user()->rol_id == 6 || Auth::user()->rol_id == 7 || Auth::user()->rol_id == 8))
    <script src="{{asset('js/administracion/exportardocumento/exportarExcel.js') }}" defer></script>
@endif

<!-- Pagina clientes-->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@if(Route::is('bienvenida') || Route::is('visitanos'))
    <script src="{{ asset('js/clientes/formatotelefono.js') }}" defer></script>
@endif

@if(Route::is('visitanos') || Route::is('horariodeatencion') || Route::is('vacantesagendarcita'))
    <script src="https://maps.googleapis.com/maps/api/js?key={{config('googlemap')['map_apikey']}}&v=3"></script>
    <script src="{{ asset('js/clientes/googlemaps/marcadoresSucursales.js') }}" defer></script>
    <script src="{{ asset('js/clientes/formatotelefono.js') }}" defer></script>
    <script src="{{ asset('js/clientes/citas/vacantes.js') }}" defer></script>
@endif

@if(Route::is('bienvenida') || Route::is('rastrearcontrato') || Route::is('formulariorastreo') || Route::is('servicioslista') || Route::is('calendariocitas'))
    <script src="{{ asset('js/clientes/citas/calendario.js') }}" defer></script>
    <script src="{{ asset('js/clientes/citas/agendarcitas.js') }}" defer></script>
    <!--     <script src='fullcalendar/dist/index.global.js'></script> -->
    <script src="{{ asset('js/clientes/formatotelefono.js') }}" defer></script>

    <!-- Librerias para exportar a PDF-->
    <script src="js/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"></script>

@endif
