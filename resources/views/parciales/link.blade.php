<!-- Fonts -->
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"><!-- Styles -->
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<link href="{{ asset('css/spinner.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('css/global.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('css/estiloAdaptable.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('css/formatoTablas.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('css/menu.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('css/alertas.css') }}" rel="stylesheet" media="all">

@if(Route::is('login'))
    <link href="{{ asset('css/login.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('editarFranquicia'))
  <link href="{{ asset('css/franquicias/editar.css') }}" rel="stylesheet">
  <link href="{{ asset('css/fontawesome.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('nuevafranquicia') || Route::is('insumos'))
  <link href="{{ asset('css/franquicias/nueva.css') }}" rel="stylesheet">
  <link href="{{ asset('css/fontawesome.css') }}" rel="stylesheet" media="all">
  <link href="{{ asset('css/all.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('listafranquicia'))
  <link href="{{ asset('css/franquicias/lista.css') }}" rel="stylesheet">
  <link href="{{ asset('css/fontawesome.css') }}" rel="stylesheet" media="all">
  <link href="{{ asset('css/all.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('listapoliza') || Route::is('verpoliza') || Route::is('crearpoliza') || Route::is('tablaAsistencia') || Route::is('asistenciaIndividual') || Route::is('filtrarlistapolizafranquicia'))
    <link href="{{ asset('css/franquicias/polizas/poliza.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('usuariosFranquicia') || Route::is('usuariosfiltrosucursal'))
  <link href="{{ asset('css/franquicias/usuarios.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('listacontratospaquetes'))
    <link href="{{ asset('css/franquicias/usuarios.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('editarUsuarioFranquicia'))
  <link href="{{ asset('css/franquicias/editarusuario.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('nuevocontrato'))
  <link href="{{ asset('css/contratos/nuevo.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('payment'))
  <link href="{{ asset('css/style.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('nuevocontrato2' || 'contratoHijos'))
  <link href="{{ asset('css/contratos/nuevo.css') }}" rel="stylesheet" media="all">
  <link href="{{ asset('css/historialclinico/nuevo.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('contratoactualizar'))
  <link href="{{ asset('css/contratos/actualizarcontrato.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('listacontrato') || Route::is('filtrarlistacontrato') || Route::is('filtrarlistacontratocheckbox') || Route::is('listalaboratorio') ||
    Route::is('estadolaboratorio') ||
    Route::is('auxiliarlaboratorio') || Route::is('actualizarestadoenviado') || Route::is('filtrarcontratosenviados') || Route::is('reportecontratos') || Route::is('listaarmazoneslaboratorio'))
  <link href="{{ asset('css/contratos/tabla.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('listaconfirmaciones') ||  Route::is('estadoconfirmacion') || Route::is('listagarantiasconfirmaciones') || Route::is('vercontratogarantiaconfirmaciones'))
    <link href="{{ asset('css/contratos/tabla.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('css/confirmaciones/estadoconfirmacion.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('nuevohistorialclinico'))
  <link href="{{ asset('css/historialclinico/nuevo.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('nuevohijo'))
  <link href="{{ asset('css/historialclinico/nuevo.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('nuevohistorialclinico2'))
  <link href="{{ asset('css/historialclinico/nuevo.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('actualizarhistorial'))
  <link href="{{ asset('css/historialclinico/nuevo.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('listasfranquicia') || Route::is('filtrarproducto'))
  <link href="{{ asset('css/adminfranquicia/tablas.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('productonuevo'))
  <link href="{{ asset('css/franquicias/productos/nuevoproducto.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('tratamientonuevo'))
  <link href="{{ asset('css/franquicias/tratamientos/nuevotratamiento.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('productoactualizar'))
  <link href="{{ asset('css/adminfranquicia/editarproducto.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('tratamientoactualizar'))
  <link href="{{ asset('css/adminfranquicia/editartratamiento.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('paquetenuevo'))
  <link href="{{ asset('css/franquicias/paquetes/nuevopaquete.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('paqueteactualizar') || Route::is('editarabonominimo') || Route::is('editarcomisionventa') || Route::is('zonanueva') || Route::is('zonaeditar') ||
    Route::is('zonafiltrarcolonias'))
  <link href="{{ asset('css/adminfranquicia/editarpaquete.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('promocionnueva'))
  <link href="{{ asset('css/adminfranquicia/nuevapromocion.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('nuevaproarmazones'))
  <link href="{{ asset('css/adminfranquicia/nuevapromoarmazon.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('promocionactualizar'))
  <link href="{{ asset('css/adminfranquicia/nuevapromoarmazon.css') }}" rel="stylesheet" media="all">
@endif
@if(Route::is('vercontrato'))
  <link href="{{ asset('css/historialclinico/nuevo.css') }}" rel="stylesheet" media="all">
  <link href="{{ asset('css/fontawesome.css') }}" rel="stylesheet" media="all">
  <link href="{{ asset('css/all.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('register'))
    <link href="{{ asset('css/registro.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('traspasarcontrato') || Route::is('obtenercontratotraspasar') || Route::is('listasolicitudautorizacion') || Route::is('traspasarcontratozona'))
    <link href="{{ asset('css/contratos/tabla.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('listareporteasistencia') || Route::is('listacontratoscuentasactivas') || Route::is('listareportehistorialsucursal')
     || Route::is('listacontratosreportes') || Route::is('filtrarlistacontratosreportes') || Route::is('listareportearmazones')
     || Route::is('filtrareportearmazones') || Route::is('reportemovimientos') || Route::is('listacontratospagadosseguimiento') || Route::is('filtrarlistacontratospagadosseguimiento')
     || Route::is('listareporteproductos') || Route::is('listareporteproductosfiltrar') || Route::is('reportemovimientoscontratos') || Route::is('reportemovimientoscontratosfiltrar')
     || Route::is('reporteabonossucursal') || Route::is('filtrarreporteabonossucursal') || Route::is('reportecontratossupervision') || Route::is('reportecontratossupervisionfiltrar'))
    <link href="{{ asset('css/reportes/reporteasistencia.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('cobranzamovimientos') || Route::is('ventasmovimientos') || Route::is('filtrarventasmovimientos') || Route::is('cobranzamovimientosvalidacioncorte'))
    <link href="{{ asset('css/movimientos/movimientos.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('general') || Route::is('configuracion') || Route::is('dispositivonuevo') || Route::is('listavehiculos') || Route::is('vervehiculo') || Route::is('nuevasupervision') ||
     Route::is('actualizarsupervisionvehiculo') || Route::is('mensajenuevo') || Route::is('listacampanias') || Route::is('vercampania') || Route::is('agendarcampania') ||
     Route::is('listatutoriales') || Route::is('listatutorialesfiltrar'))
    <link href="{{ asset('css/desarrollo.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('listaproductoslaboratorio') || Route::is('filtrarlistaproductoslaboratorio'))
    <link href="{{ asset('css/laboratorio/laboratorio.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('listavacantesadministracion') || Route::is('listavacantesredes') || Route::is('filtrarsolicitudesvacantesadmin')
     || Route::is('filtrarlistavacantesredes') || Route::is('reportevacantesmensajes'))
    <link href="{{ asset('css/franquicias/usuarios.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('listaagendacitas'))
    <link href="{{ asset('css/franquicias/usuarios.css') }}" rel="stylesheet" media="all">
@endif

@if(Route::is('administracionimagenes'))
    <link href="{{ asset('css/adminpaginaclientes.css') }}" rel="stylesheet" media="all">
@endif


<!-- Pagina clientes-->
<link href="{{ asset('css/clientes/bienvenida.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('css/clientes/globalclientes.css') }}" rel="stylesheet" media="all">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
