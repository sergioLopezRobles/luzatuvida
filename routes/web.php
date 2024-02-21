<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

//Route::group(['domain' => 'adminlabo.luzatuvida.com.mx'], function (){             //Comentar linea para ingresar a pagina administracion

//Grupo de rutas para subdominio -> pagina administrativa

    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/home', function () {
        return redirect()->route('login');
    });

//DIRECCIONAMIENTO

    Route::group(['middleware' => ['auth', 'sesionCaducadaUsuario']], function () {
        Route::get('redireccion', 'App\Http\Controllers\Dominios\Administracion\direccionamiento@redireccionar')->name('redireccionar');
        Route::get('sucursal/estado', 'App\Http\Controllers\Dominios\Administracion\direccionamiento@estadofranquicia')->name('estadofranquicia');
    });

//FRANQUICIA
    Route::group(['middleware' => ['auth', 'empresaActiva', 'notificaciones', 'sesionCaducadaUsuario']], function () {
        Route::get('sucursal/tabla', 'App\Http\Controllers\Dominios\Administracion\franquicias@tablaFranquicia')->name('listafranquicia');
        Route::get('sucursal/nueva', 'App\Http\Controllers\Dominios\Administracion\franquicias@nuevaFranquicia')->name('nuevafranquicia');
        Route::post('sucursal/crear', 'App\Http\Controllers\Dominios\Administracion\franquicias@crearFranquicia')->name('crearfranquicia');
        Route::get('sucursal/{idFranquicia}/editar', 'App\Http\Controllers\Dominios\Administracion\franquicias@editarFranquicia')->name('editarFranquicia');
        Route::post('sucursal/{idFranquicia}/editar', 'App\Http\Controllers\Dominios\Administracion\franquicias@actualizarFranquicia')->name('actualizarFranquicia');
        Route::get('sucursal/{idFranquicia}/usuarios', 'App\Http\Controllers\Dominios\Administracion\franquicias@usuariosfranquicia')->name('usuariosFranquicia');
        Route::post('sucursal/{idFranquicia}/usuarios', 'App\Http\Controllers\Dominios\Administracion\franquicias@nuevousuariofranquicia')->name('nuevoUsuarioFranquicia');
        Route::post('sucursal/usuario/eliminar', 'App\Http\Controllers\Dominios\Administracion\franquicias@eliminarsuariofranquicia')->name('eliminarUsuarioFranquicia');
        Route::get('sucursal/{idfranquicia}/usuario/{idusuario}/editar', 'App\Http\Controllers\Dominios\Administracion\franquicias@editarsuariofranquicia')->name('editarUsuarioFranquicia');
        Route::post('sucursal/{idfranquicia}/usuario/{idusuario}/editar', 'App\Http\Controllers\Dominios\Administracion\franquicias@actualizarsuariofranquicia')->name('actualizarUsuarioFranquicia');
        Route::get('sucursal/{idFranquicia}/usuario/{idusuario}/codigobarras/generar', 'App\Http\Controllers\Dominios\Administracion\franquicias@generarcodigodebarrasusuario')->name('generarcodigodebarrasusuario');
        Route::get('sucursal/{idFranquicia}/usuario/{idusuario}/actualizar/dispositivo/{idDispositivo}', 'App\Http\Controllers\Dominios\Administracion\franquicias@actualizarsuariofranquiciadispositivo')->name('actualizarUsuarioFranquiciadispositivo');
        Route::get('/descargar-archivo/{idUsuario}/{archivo}', 'App\Http\Controllers\Dominios\Administracion\franquicias@descargarArchivo')->name('descargarArchivo');
        Route::get('/usuariosfranquiciatiemporeal', 'App\Http\Controllers\Dominios\Administracion\franquicias@usuariosfranquiciatiemporeal')->name('usuariosfranquiciatiemporeal');
        Route::post('sucursal/{idFranquicia}/usuarios/filtrosucursal', 'App\Http\Controllers\Dominios\Administracion\franquicias@usuariosfiltrosucursal')->name('usuariosfiltrosucursal');
        Route::post('sucursal/{idfranquicia}/usuario/{idusuario}/editar/controlentradasalida', 'App\Http\Controllers\Dominios\Administracion\franquicias@actualizarControlEntradaSalidaUsuarioFranquicia')->name('actualizarControlEntradaSalidaUsuarioFranquicia');
        Route::get('/descomprimirZipUsuario', 'App\Http\Controllers\Dominios\Administracion\franquicias@descomprimirZipUsuario')->name('descomprimirZipUsuario');
        Route::get('sucursal/usuario/{id_Usuario}/seccion/{id_seccion}/permiso/{id_permiso}/actualizar', 'App\Http\Controllers\Dominios\Administracion\franquicias@asignarDenegarPermisosUsuarios')->name('asignarDenegarPermisosUsuarios');
        Route::get('sucursal/{idFranquicia}/vacantes/administracion/tabla', 'App\Http\Controllers\Dominios\Administracion\franquicias@listavacantesadministracion')->name('listavacantesadministracion');
        Route::get('sucursal/{idFranquicia}/vacantes/redes/tabla', 'App\Http\Controllers\Dominios\Administracion\franquicias@listavacantesredes')->name('listavacantesredes');
        Route::post('sucursal/{idFranquicia}/vacantes/administracion/solicitar', 'App\Http\Controllers\Dominios\Administracion\franquicias@solicitarvacante')->name('solicitarvacante');
        Route::post('sucursal/{idFranquicia}/vacantes/administracion/horario/actualizar', 'App\Http\Controllers\Dominios\Administracion\franquicias@actualizarhorariocitavacantes')->name('actualizarhorariocitavacantes');
        Route::get('/cancelarvacante', 'App\Http\Controllers\Dominios\Administracion\franquicias@cancelarvacante')->name('cancelarvacante');
        Route::get('/notificarcitavacante', 'App\Http\Controllers\Dominios\Administracion\franquicias@notificacioncitavacante')->name('notificacioncitavacante');
        Route::post('sucursal/{idFranquicia}/vacantes/administracion/filtrar', 'App\Http\Controllers\Dominios\Administracion\franquicias@filtrarsolicitudesvacantesadmin')->name('filtrarsolicitudesvacantesadmin');
        Route::post('sucursal/{idFranquicia}/vacantes/redes/filtrar', 'App\Http\Controllers\Dominios\Administracion\franquicias@filtrarlistavacantesredes')->name('filtrarlistavacantesredes');
        Route::get('/agendar', 'App\Http\Controllers\Dominios\Administracion\franquicias@agendarcitavacante')->name('agendarcitavacante');
        Route::get('/actualizarCita', 'App\Http\Controllers\Dominios\Administracion\franquicias@actualizarcitavacante')->name('actualizarcitavacante');
        Route::get('sucursal/{idFranquicia}/vacantes/reporte/mensajes', 'App\Http\Controllers\Dominios\Administracion\franquicias@reportevacantesmensajes')->name('reportevacantesmensajes');
        Route::post('sucursal/{idFranquicia}/vacantes/reporte/mensajes/nuevo', 'App\Http\Controllers\Dominios\Administracion\franquicias@nuevomensajevacantes')->name('nuevomensajevacantes');
        Route::get('sucursal/{idFranquicia}/vacantes/reporte/mensajes/{indice}/leer', 'App\Http\Controllers\Dominios\Administracion\franquicias@leermensajevacante')->name('leermensajevacante');
        Route::post('sucursal/{idFranquicia}/vacantes/reporte/mensajes/responder', 'App\Http\Controllers\Dominios\Administracion\franquicias@respondermensajevacante')->name('respondermensajevacante');
        Route::get('sucursal/{idFranquicia}/vacantes/reporte/mensajes/{indice}/eliminar', 'App\Http\Controllers\Dominios\Administracion\franquicias@eliminarmensajevacante')->name('eliminarmensajevacante');
        Route::post('sucursal/{idfranquicia}/usuario/{idusuario}/expediente/insertar', 'App\Http\Controllers\Dominios\Administracion\franquicias@agregarExpedienteUsuario')->name('agregarExpedienteUsuario');
        Route::get('sucursal/{idfranquicia}/usuario/{idusuario}/expediente/{indice}/descargar', 'App\Http\Controllers\Dominios\Administracion\franquicias@descargarArchivoExpedienteUsuario')->name('descargarArchivoExpedienteUsuario');
        Route::get('sucursal/{idfranquicia}/usuario/{idusuario}/expediente/{indice}/eliminar', 'App\Http\Controllers\Dominios\Administracion\franquicias@eliminarArchivoExpedienteUsuario')->name('eliminarArchivoExpedienteUsuario');
        Route::post('sucursal/{idFranquicia}/usuario/{idusuario}/vehiculo/asignacion', 'App\Http\Controllers\Dominios\Administracion\franquicias@asignarVehiculoUsuarioChofer')->name('asignarVehiculoUsuarioChofer');
        Route::post('sucursal/{idFranquicia}/usuario/{idusuario}/vehiculo/quitarasignacion', 'App\Http\Controllers\Dominios\Administracion\franquicias@quitarAsignacionVehiculoUsuarioChofer')->name('quitarAsignacionVehiculoUsuarioChofer');
        Route::get('/cargarlistausuariosasignados', 'App\Http\Controllers\Dominios\Administracion\franquicias@cargarlistausuariosasignados')->name('cargarlistausuariosasignados');
        //Actualizar telefonos sucursal- Ejecutar solo una vez
        Route::get('sucursal/telefonos/actualizar', 'App\Http\Controllers\Dominios\Administracion\franquicias@actualizarTelefonoSucursal')->name('actualizarTelefonoSucursal');
        //Actualizar campo barcode usuario - Ejecutar una vez
        Route::get('sucursal/usuarios/barcode/actualizar', 'App\Http\Controllers\Dominios\Administracion\franquicias@actualizarcampobarcodeusuariobd')->name('actualizarcampobarcodeusuariobd');

        Route::get('sucursal/{idfranquicia}/usuario/{idusuario}/editar/zona', 'App\Http\Controllers\Dominios\Administracion\franquicias@actualizarusuariozonafranquicia')->name('actualizarusuariozonafranquicia');
        Route::get('sucursal/{idfranquicia}/vacantes/solicitud/{indice}/curriculum/descargar', 'App\Http\Controllers\Dominios\Administracion\franquicias@descargarcurriculumcitavacante')->name('descargarcurriculumcitavacante');
        Route::get('sucursal/{idfranquicia}/usuario/{idusuario}/cobranzacambio/solicitar', 'App\Http\Controllers\Dominios\Administracion\franquicias@solicitarautorizacioncambiocobranza')->name('solicitarautorizacioncambiocobranza');
        Route::get('sucursal/{idfranquicia}/usuario/autorizacion/{indice}/estado/{estado}/actualizar', 'App\Http\Controllers\Dominios\Administracion\franquicias@actualizarestadoautorizacioncambiocobranza')->name('actualizarestadoautorizacioncambiocobranza');
        Route::post('sucursal/{idfranquicia}/usuario/{idusuario}/editar/actualizarestatususuario', 'App\Http\Controllers\Dominios\Administracion\franquicias@actualizarestatususuario')->name('actualizarestatususuario');
        Route::post('sucursal/{idfranquicia}/usuario/{idusuario}/editar/actualizarexcepcionasistenciausuario', 'App\Http\Controllers\Dominios\Administracion\franquicias@actualizarexcepcionasistenciausuario')->name('actualizarexcepcionasistenciausuario');
        Route::get('sucursal/{idfranquicia}/usuario/{idusuario}/eliminarzonausuario', 'App\Http\Controllers\Dominios\Administracion\franquicias@eliminarzonausuario')->name('eliminarzonausuario');
    });

//POLIZA
    Route::group(['middleware' => ['auth', 'empresaActiva', 'notificaciones', 'sesionCaducadaUsuario']], function () {
        Route::get('sucursal/{idFranquicia}/polizas/tabla', 'App\Http\Controllers\Dominios\Administracion\poliza@tablaPoliza')->name('listapoliza');
        Route::get('sucursal/{idFranquicia}/poliza/{idPoliza}', 'App\Http\Controllers\Dominios\Administracion\poliza@verpoliza')->name('verpoliza');
        Route::post('sucursal/{idFranquicia}/poliza/{idPoliza}/terminar', 'App\Http\Controllers\Dominios\Administracion\poliza@terminarPoliza')->name('terminarPoliza');

        //ASISTENCIAS
        Route::post('sucursal/{idFranquicia}/poliza/{idPoliza}/asistencia', 'App\Http\Controllers\Dominios\Administracion\poliza@registrarAsistencia')->name('registrarAsistencia');
        Route::get('sucursal/{idFranquicia}/poliza/{idPoliza}/asistencia/tabla', 'App\Http\Controllers\Dominios\Administracion\poliza@tablaAsistencia')->name('tablaAsistencia');
        Route::post('sucursal/{idFranquicia}/poliza/{idPoliza}/asistencia/tabla', 'App\Http\Controllers\Dominios\Administracion\poliza@registrarAsistenciaTabla')->name('registrarAsistenciaTabla');
        Route::get('sucursal/{idFranquicia}/poliza/{idPoliza}/asistencia/individual', 'App\Http\Controllers\Dominios\Administracion\poliza@asistenciaIndividual')->name('asistenciaIndividual');
        Route::get('sucursal/{idFranquicia}/asistencia/generar-codigos', 'App\Http\Controllers\Dominios\Administracion\poliza@generarcodigos');

        Route::post('sucursal/{idFranquicia}/poliza/{idPoliza}/ingreso', 'App\Http\Controllers\Dominios\Administracion\poliza@ingresarOficina')->name('ingresarOficina');
        Route::post('sucursal/{idFranquicia}/poliza/{idPoliza}/observacion', 'App\Http\Controllers\Dominios\Administracion\poliza@agregarObservacion')->name('agregarObservacion');
        Route::post('sucursal/{idFranquicia}/poliza/{idPoliza}/gasto', 'App\Http\Controllers\Dominios\Administracion\poliza@ingresarGasto')->name('ingresarGasto');
        Route::post('sucursal/{idFranquicia}/poliza/{idPoliza}/cobranza', 'App\Http\Controllers\Dominios\Administracion\poliza@ingresarCobranza')->name('ingresarCobranza');
        Route::get('sucursal/{idFranquicia}/poliza/{idPoliza}/gasto/{idGasto}/eliminar', 'App\Http\Controllers\Dominios\Administracion\poliza@eliminarGasto')->name('eliminarGasto');
        Route::get('sucursal/{idFranquicia}/poliza/{idPoliza}/gasto/{idOficina}/eliminar/oficina', 'App\Http\Controllers\Dominios\Administracion\poliza@eliminarOficina')->name('eliminarOficina');
        Route::get('/crearcargarpolizatiemporeal', 'App\Http\Controllers\Dominios\Administracion\poliza@crearcargarpolizatiemporeal')->name('crearcargarpolizatiemporeal');
        Route::get('sucursal/{idFranquicia}/poliza/{idPoliza}/{idUsuario}/polizaactualizarasisoptocobranza', 'App\Http\Controllers\Dominios\Administracion\poliza@polizaactualizarasisoptocobranza')->name('polizaactualizarasisoptocobranza');
        Route::post('sucursal/{idFranquicia}/poliza/{idPoliza}/ingresooficinaproducto', 'App\Http\Controllers\Dominios\Administracion\poliza@ingresooficinaproducto')->name('ingresooficinaproducto');
        Route::post('sucursal/{idFranquicia}/polizas/tabla/filtrar', 'App\Http\Controllers\Dominios\Administracion\poliza@filtrarlistapolizafranquicia')->name('filtrarlistapolizafranquicia');
        Route::post('sucursal/{idFranquicia}/poliza/{idPoliza}/actualizarfotogasto', 'App\Http\Controllers\Dominios\Administracion\poliza@actualizarfotogasto')->name('actualizarfotogasto');
        Route::get('/cargarinformacionmodalpolizatiemporeal', 'App\Http\Controllers\Dominios\Administracion\poliza@cargarinformacionmodalpolizatiemporeal')->name('cargarinformacionmodalpolizatiemporeal');
    });

//CONTRATO
    Route::group(['middleware' => ['auth', 'empresaActiva', 'notificaciones', 'sesionCaducadaUsuario']], function () {
        Route::get('sucursal/{id}/contratos/tabla', 'App\Http\Controllers\Dominios\Administracion\contratos@listacontrato')->name('listacontrato');
        Route::post('sucursal/{idFranquicia}/contratos/tabla/buscar', 'App\Http\Controllers\Dominios\Administracion\contratos@filtrarlistacontrato')->name('filtrarlistacontrato');
        Route::post('sucursal/{idFranquicia}/contratos/tabla', 'App\Http\Controllers\Dominios\Administracion\contratos@filtrarlistacontratocheckbox')->name('filtrarlistacontratocheckbox');
        Route::get('sucursal/{idFranquicia}/contrato/nuevo', 'App\Http\Controllers\Dominios\Administracion\contratos@nuevocontrato')->name('nuevocontrato');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/cancelar', 'App\Http\Controllers\Dominios\Administracion\contratos@cancelarContrato')->name('cancelarContrato');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/nuevo/2', 'App\Http\Controllers\Dominios\Administracion\contratos@nuevocontrato2')->name('nuevocontrato2');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/nuevo/hijo/2', 'App\Http\Controllers\Dominios\Administracion\contratos@contratoHijos')->name('contratoHijos');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/entregar', 'App\Http\Controllers\Dominios\Administracion\contratos@entregarContrato')->name('entregarContrato');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/atraso', 'App\Http\Controllers\Dominios\Administracion\contratos@abonoAtrasado')->name('abonoAtrasado');
        Route::post('sucursal/{idFranquicia}/contrato/nuevo', 'App\Http\Controllers\Dominios\Administracion\contratos@crearcontrato')->name('crearcontrato');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/promocion/agregar', 'App\Http\Controllers\Dominios\Administracion\contratos@agregarpromocion')->name('agregarpromocion');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/dia', 'App\Http\Controllers\Dominios\Administracion\contratos@entregarDiaPago')->name('entregarDiaPago');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/pago', 'App\Http\Controllers\Dominios\Administracion\contratos@agregarformapago')->name('agregarformapago');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/abono/agregar', 'App\Http\Controllers\Dominios\Administracion\contratos@agregarabono')->name('agregarabono');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/abono/{idAbono}/eliminar', 'App\Http\Controllers\Dominios\Administracion\contratos@eliminarAbono')->name('eliminarAbono');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/producto/{idContratoProducto}/eliminar', 'App\Http\Controllers\Dominios\Administracion\contratos@eliminarcontratoproducto')->name('eliminarcontratoproducto');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/abono/agregar/2', 'App\Http\Controllers\Dominios\Administracion\contratos@agregarabono2')->name('agregarabono2');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/nuevos/2', 'App\Http\Controllers\Dominios\Administracion\contratos@crearcontrato2')->name('crearcontrato2');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/promocion/{idPromocion}/desactivar', 'App\Http\Controllers\Dominios\Administracion\contratos@desactivarPromocion')->name('desactivarPromocion');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/promocion/{idPromocion}/eliminar', 'App\Http\Controllers\Dominios\Administracion\contratos@eliminarPromocion')->name('eliminarPromocion');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/actualizar', 'App\Http\Controllers\Dominios\Administracion\contratos@contratoactualizar')->name('contratoactualizar');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/actualizar', 'App\Http\Controllers\Dominios\Administracion\contratos@contratoeditar')->name('contratoeditar');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/reposicion', 'App\Http\Controllers\Dominios\Administracion\contratos@contratoreponer')->name('contratoreponer');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/producto/agregar', 'App\Http\Controllers\Dominios\Administracion\contratos@agregarproducto')->name('agregarproducto');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}', 'App\Http\Controllers\Dominios\Administracion\contratos@vercontrato')->name('vercontrato');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/validarcontrato', 'App\Http\Controllers\Dominios\Administracion\contratos@validarContrato')->name('validarContrato');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/nota', 'App\Http\Controllers\Dominios\Administracion\contratos@agregarnota')->name("agregarnota");
        Route::get('correo', 'App\Http\Controllers\Dominios\Administracion\contratos@correo');
        Route::get('sms', 'App\Http\Controllers\Dominios\Administracion\contratos@sms');

        Route::get('actualizar-contratos-pagos', 'App\Http\Controllers\Dominios\Administracion\contratos@darFormatoContratos');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/agregarhistorialmovimientocontrato', 'App\Http\Controllers\Dominios\Administracion\contratos@agregarhistorialmovimientocontrato')->name('agregarhistorialmovimientocontrato');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/historialmovimientocontrato/{idHistorial}/eliminar', 'App\Http\Controllers\Dominios\Administracion\contratos@eliminarhistorialmovimientocontrato')->name('eliminarhistorialmovimientocontrato');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/agregarhistorialfotocontrato', 'App\Http\Controllers\Dominios\Administracion\contratos@agregarhistorialfotocontrato')->name('agregarhistorialfotocontrato');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/restablecercontrato', 'App\Http\Controllers\Dominios\Administracion\contratos@restablecercontrato')->name('restablecercontrato');
        Route::get('sucursal/{idFranquicia}/migrarcuentas/tabla', 'App\Http\Controllers\Dominios\Administracion\contratos@migrarcuentas')->name('migrarcuentas');
        Route::post('sucursal/{idFranquicia}/migrarcuentasarchivo/tabla', 'App\Http\Controllers\Dominios\Administracion\contratos@migrarcuentasarchivo')->name('migrarcuentasarchivo');

        Route::get('sucursal/{idFranquicia}/contratos/traspasar', 'App\Http\Controllers\Dominios\Administracion\contratos@traspasarcontrato')->name('traspasarcontrato');
        Route::post('sucursal/{idFranquicia}/contratos/traspasar/contrato', 'App\Http\Controllers\Dominios\Administracion\contratos@buscarcontratotraspasar')->name('buscarcontratotraspasar');
        Route::get('sucursal/{idFranquicia}/contratos/traspasar/contrato/{idContrato}', 'App\Http\Controllers\Dominios\Administracion\contratos@obtenercontratotraspasar')->name('obtenercontratotraspasar');
        Route::post('sucursal/{idFranquicia}/contratos/traspasar/{idContrato}/contrato', 'App\Http\Controllers\Dominios\Administracion\contratos@generartraspasocontrato')->name('generartraspasocontrato');
        Route::get('/cargarpromociones', 'App\Http\Controllers\Dominios\Administracion\contratos@cargarpromocionesfranquicia')->name('cargarpromociones');

        Route::get('sucursal/{idFranquicia}/contratos/reporte', 'App\Http\Controllers\Dominios\Administracion\contratos@reportecontratos')->name('reportecontratos');
        Route::get('/reportescontratosdirector', 'App\Http\Controllers\Dominios\Administracion\contratos@obtenerreportecontratosdirector')->name('reportescontratosdirector');

        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/restablecercontratocanceladorechazadoliofuga', 'App\Http\Controllers\Dominios\Administracion\contratos@restablecercontratocanceladorechazadoliofuga')->name('restablecercontratocanceladorechazadoliofuga');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/eliminardiatemporal', 'App\Http\Controllers\Dominios\Administracion\contratos@eliminardiatemporal')->name('eliminardiatemporal');

        //Solicitar autorizacion garantia y cancelar contrato
        Route::get('sucursal/{idFranquicia}/contratos/solicitud/autorizar', 'App\Http\Controllers\Dominios\Administracion\contratos@listasolicitudautorizacion')->name('listasolicitudautorizacion');
        Route::get('sucursal/contrato/{idContrato}/solicitud/{indice}/autorizar', 'App\Http\Controllers\Dominios\Administracion\contratos@autorizarcontrato')->name('autorizarcontrato');
        Route::get('sucursal/contrato/{idContrato}/solicitud/{indice}/rechazar', 'App\Http\Controllers\Dominios\Administracion\contratos@rechazarcontrato')->name('rechazarcontrato');
        Route::post('sucursal/{idFranquicia}/contratos/{idContrato}/garantia/solicitar', 'App\Http\Controllers\Dominios\Administracion\contratos@solicitarautorizaciongarantia')->name('solicitarautorizaciongarantia');
        Route::post('sucursal/{idFranquicia}/contratos/{idContrato}/aumentardisminuir/solicitar', 'App\Http\Controllers\Dominios\Administracion\contratos@solicitarautorizacionaumentardisminuir')->name('solicitarautorizacionaumentardisminuir');
        Route::post('sucursal/{idFranquicia}/contratos/{idContrato}/paquete/{idHistorial}/solicitar', 'App\Http\Controllers\Dominios\Administracion\contratos@solicitarautorizacioncambiopaquete')->name('solicitarautorizacioncambiopaquete');
        Route::post('sucursal/{idFranquicia}/contratos/{idContrato}/traspasocontratolaboratorio/solicitar', 'App\Http\Controllers\Dominios\Administracion\contratos@solicitarautorizaciontraspasocontratolaboratorio')->name('solicitarautorizaciontraspasocontratolaboratorio');
        Route::post('sucursal/{idFranquicia}/contratos/{idContrato}/abonominimocontrato/solicitar', 'App\Http\Controllers\Dominios\Administracion\contratos@solicitarautorizacionabonominimo')->name('solicitarautorizacionabonominimo');
        Route::post('sucursal/{idFranquicia}/contratos/{idContrato}/supervisarcontrato/solicitar', 'App\Http\Controllers\Dominios\Administracion\contratos@solicitarautorizacionsupervisarcontrato')->name('solicitarautorizacionsupervisarcontrato');
        Route::post('sucursal/{idFranquicia}/contratos/{idContrato}/listanegra/solicitar', 'App\Http\Controllers\Dominios\Administracion\contratos@reportaractualizarsolicitudcontratolistanegra')->name('reportaractualizarsolicitudcontratolistanegra');
        Route::get('sucursal/{idFranquicia}/contratos/{idContrato}/listanegra/solicitud/rechazaraprobar/{opcion}', 'App\Http\Controllers\Dominios\Administracion\contratos@solicitudrechazaraprobarcontratolistanegra')->name('solicitudrechazaraprobarcontratolistanegra');
        Route::get('sucursal/{idFranquicia}/contratos/{idContrato}/listanegra/autorizacion/solicitar', 'App\Http\Controllers\Dominios\Administracion\contratos@solicitudautorizacioncontratolistanegra')->name('solicitudautorizacioncontratolistanegra');

        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/actualizarfechaultimoabonocontrato', 'App\Http\Controllers\Dominios\Administracion\contratos@actualizarfechaultimoabonocontrato')->name('actualizarfechaultimoabonocontrato');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/agregarabonominimo', 'App\Http\Controllers\Dominios\Administracion\contratos@agregarabonominimo')->name('agregarabonominimo');

        Route::get('sucursal/{idFranquicia}/traspasarcontratozona/tabla', 'App\Http\Controllers\Dominios\Administracion\contratos@traspasarcontratozona')->name('traspasarcontratozona');
        Route::get('/traspasarcontratozonatiemporeal', 'App\Http\Controllers\Dominios\Administracion\contratos@traspasarcontratozonatiemporeal')->name('traspasarcontratozonatiemporeal');
        Route::get('sucursal/{idFranquicia}/{idZonaSeleccionada}/tabla/actualizartraspasarcontratozona', 'App\Http\Controllers\Dominios\Administracion\contratos@actualizartraspasarcontratozona')->name('actualizartraspasarcontratozona');
        Route::get('/zonastraspasarcontratozona', 'App\Http\Controllers\Dominios\Administracion\contratos@zonastraspasarcontratozona')->name('zonastraspasarcontratozona');

        //Ruta actualizar tabla autorizaciones - Ejecutar una vez
        Route::get('autorizaciones/tabla/actualizar', 'App\Http\Controllers\Dominios\Administracion\contratos@actualizarTablaAutorizacionesNuevosCampos')->name('actualizarTablaAutorizacionesNuevosCampos');

        //Ruta abonominimo tabla contratos - Ejecutar una vez
        Route::get('contratos/abonominimo/insertar', 'App\Clases\contratosGlobal@insertarAbonoMinimoTablaContrato')->name('insertarAbonoMinimoTablaContrato');

        //Ruta cargar datos tabla contratosliofuga - ejecutar una vez
        Route::get('contratos/{idFranquicia}/liofuga/insertar', 'App\Http\Controllers\Dominios\Administracion\contratos@llenarTablaContratosLioFuga')->name('llenarTablaContratosLioFuga');

        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/actualizarpagosadelantarcontrato', 'App\Http\Controllers\Dominios\Administracion\contratos@actualizarpagosadelantarcontrato')->name("actualizarpagosadelantarcontrato");
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/actualizardiagnosticoeditarcontrato', 'App\Http\Controllers\Dominios\Administracion\contratos@actualizardiagnosticoeditarcontrato')->name('actualizardiagnosticoeditarcontrato');
        Route::get('/obtenerprecioproductomodal', 'App\Http\Controllers\Dominios\Administracion\contratos@obtenerprecioproductomodal')->name('obtenerprecioproductomodal');
        Route::get('/obtenertotalactualizadomodalsolicitaraumentardescontar', 'App\Http\Controllers\Dominios\Administracion\contratos@obtenertotalactualizadomodalsolicitaraumentardescontar')->name('obtenertotalactualizadomodalsolicitaraumentardescontar');
        Route::get('/obtenertotalactualizadomodalsolicitaraumentardescontar', 'App\Http\Controllers\Dominios\Administracion\contratos@obtenertotalactualizadomodalsolicitaraumentardescontar')->name('obtenertotalactualizadomodalsolicitaraumentardescontar');
    });

    Route::post('servicio/agregararchivoexcel', 'App\Http\Controllers\Dominios\Administracion\contratos@agregarArchivoExcel')->name('agregarArchivoExcel');
    Route::post('servicio/liquidararchivo', 'App\Http\Controllers\Dominios\Administracion\contratos@liquidarContratosArchivo')->name('liquidarContratosArchivo');

//HISTORIAL CLINICO
    Route::group(['middleware' => ['auth', 'empresaActiva', 'notificaciones', 'sesionCaducadaUsuario']], function () {
//    Route::get('sucursal/{idFranquicia}/contrato/historialclinico/nuevo', 'App\Http\Controllers\Dominios\Administracion\historialclinico@nuevohistorialclinico')->name('nuevohistorialclinico');
//    Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/historialclinico/nuevo/2', 'App\Http\Controllers\Dominios\Administracion\historialclinico@nuevohistorialclinico2')->name('nuevohistorialclinico2');
        Route::post('sucursal/{idFranquicia}/contrato/historialclinico/nuevo', 'App\Http\Controllers\Dominios\Administracion\historialclinico@crearhistorialclinico')->name('crearhistorialclinico');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/historialclinico/crear/2', 'App\Http\Controllers\Dominios\Administracion\historialclinico@crearhistorialclinico2')->name('crearhistorialclinico2');
        Route::get('sucursal/{idFranquicia}/contrato/{idContrato}/historialclinico/{idHistorial}/actualizar', 'App\Http\Controllers\Dominios\Administracion\historialclinico@actualizarhistorial')->name('actualizarhistorial');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/historialclinico/{idHistorial}/actualizar', 'App\Http\Controllers\Dominios\Administracion\historialclinico@editarHistorial')->name('editarHistorial');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/historialclinico/{idHistorial}/actualizararmazon', 'App\Http\Controllers\Dominios\Administracion\historialclinico@editarHistorialArmazon')->name('editarHistorialArmazon');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/historialclinico/{idHistorial}/fotoarmazon/actualizar', 'App\Http\Controllers\Dominios\Administracion\historialclinico@actualizarfotoarmazon')->name('actualizarfotoarmazon');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/historialclinico/{idHistorial}/agregargarantia', 'App\Http\Controllers\Dominios\Administracion\historialclinico@agregarGarantiaHistorial')->name('agregarGarantiaHistorial');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/historialclinico/{idHistorial}/cancelargarantia', 'App\Http\Controllers\Dominios\Administracion\historialclinico@cancelarGarantiaHistorial')->name('cancelarGarantiaHistorial');
        Route::post('sucursal/{idFranquicia}/contrato/{idContrato}/historialclinico/{idHistorial}/actualizar', 'App\Http\Controllers\Dominios\Administracion\historialclinico@actualizarhistorialclinico')->name('actualizarhistorialclinico');
    });

//ADMINISTRACION FRANQUICIA E INSUMOS
    Route::group(['middleware' => ['auth', 'empresaActiva', 'notificaciones', 'sesionCaducadaUsuario']], function () {
        Route::get('sucursal/{idFranquicia}/tablas/administracion', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@listas')->name('listasfranquicia');
        Route::get('sucursal/{idFranquicia}/producto/nuevo', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@productonuevo')->name('productonuevo');
        Route::post('sucursal/{idFranquicia}/producto/nuevo', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@productocrear')->name('productocrear');
        Route::post('sucursal/{idFranquicia}/productos/tabla', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@filtrarproducto')->name('filtrarproducto');
        Route::get('sucursal/{idFranquicia}/producto/{idProducto}/actualizar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@productoactualizar')->name('productoactualizar');
        Route::post('sucursal/{idFranquicia}/producto/{idProducto}/actualizar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@productoeditar')->name('productoeditar');
        Route::get('sucursal/{idFranquicia}/tratamiento/nuevo', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@tratamientonuevo')->name('tratamientonuevo');
        Route::post('sucursal/{idFranquicia}/tratamiento/nuevo', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@tratamientocrear')->name('tratamientocrear');
        Route::get('sucursal/{idFranquicia}/tratamiento/{idTratamiento}/actualizar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@tratamientoactualizar')->name('tratamientoactualizar');
        Route::post('sucursal/{idFranquicia}/tratamiento/{idTratamiento}/actualizar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@tratamientoeditar')->name('tratamientoeditar');
        //Route::get('sucursal/{id}/paquete/nuevo','App\Http\Controllers\Dominios\Administracion\adminfranquicia@paquetenuevo')->name('paquetenuevo');
        //Route::post('sucursal/{id}/paquete/nuevo','App\Http\Controllers\Dominios\Administracion\adminfranquicia@paquetecrear')->name('paquetecrear');
        Route::get('sucursal/{idFranquicia}/paquete/{idPaquete}/actualizar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@paqueteactualizar')->name('paqueteactualizar');
        Route::post('sucursal/{idFranquicia}/paquete/{idPaquete}/actualizar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@paqueteeditar')->name('paqueteeditar');
        Route::get('sucursal/{idFranquicia}/producto/{idProducto}/promocion/desactivar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@productodesactivarpromo')->name('productodesactivarpromo');
        Route::get('sucursal/{idFranquicia}/mensaje/nuevo', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@mensajeNuevo')->name('mensajenuevo');
        Route::post('sucursal/{idFranquicia}/mensaje/crear', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@crearmensaje')->name('crearmensaje');
        Route::get('sucursal/{idFranquicia}/mensaje/{idMensaje}/eliminar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@eliminarmensaje')->name('eliminarmensaje');
        Route::post('sucursal/{idFranquicia}/subir-excel', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@subirarchivoexcel')->name('subirarchivoexcel');
        Route::get('sucursal/{idFranquicia}/insumos', 'App\Http\Controllers\Dominios\Administracion\franquicias@insumos')->name('insumos');
        Route::post('insumos', 'App\Http\Controllers\Dominios\Administracion\franquicias@actualizarinsumos')->name('actualizarinsumos');
        Route::get('sucursal/{idFranquicia}/abonominimo/{tipoPago}/editar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@editarabonominimo')->name('editarabonominimo');
        Route::post('sucursal/{idFranquicia}/abonominimo/{tipoPago}/actualizar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@actualizarabonominimo')->name('actualizarabonominimo');
        Route::get('sucursal/{idFranquicia}/comisionventa/{indice}/editar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@editarcomisionventa')->name('editarcomisionventa');
        Route::post('sucursal/{idFranquicia}/comisionventa/{indice}/actualizar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@actualizarcomisionventa')->name('actualizarcomisionventa');
        Route::get('sucursal/{idFranquicia}/zonas/nueva', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@zonanueva')->name('zonanueva');
        Route::post('sucursal/{idFranquicia}/zonas/crear', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@zonacrear')->name('zonacrear');
        Route::get('sucursal/{idFranquicia}/zonas/{idZona}/editar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@zonaeditar')->name('zonaeditar');
        Route::post('sucursal/{idFranquicia}/zonas/{idZona}/filtrar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@zonafiltrarcolonias')->name('zonafiltrarcolonias');
        Route::post('sucursal/{idFranquicia}/zonas/{idZona}/agregar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@agregarcoloniazona')->name('agregarcoloniazona');
        Route::get('sucursal/{idFranquicia}/zonas/{idZona}/colonia/{indiceColonia}/eliminar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@eliminarcoloniazona')->name('eliminarcoloniazona');
        Route::post('sucursal/{idFranquicia}/zonas/cambiar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@cambiarzonaeditar')->name('cambiarzonaeditar');
        Route::post('sucursal/{idFranquicia}/zonas/eliminar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@eliminarzona')->name('eliminarzona');
        Route::post('sucursal/{idFranquicia}/tratamiento/{idTratamiento}/color', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@agregarcolortratamiento')->name('agregarcolortratamiento');
        Route::post('sucursal/{idFranquicia}/tratamiento/color/eliminar', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@eliminarcolortratamiento')->name('eliminarcolortratamiento');
        Route::post('sucursal/{idFranquicia}/franquicia/actualizaraccionbanderaasistenciafranquicia', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@actualizaraccionbanderaasistenciafranquicia')->name('actualizaraccionbanderaasistenciafranquicia');
        Route::post('sucursal/{idFranquicia}/franquicia/actualizarhorabanderaterminarpolizafranquicia', 'App\Http\Controllers\Dominios\Administracion\adminfranquicia@actualizarhorabanderaterminarpolizafranquicia')->name('actualizarhorabanderaterminarpolizafranquicia');
    });

//COBRANZA MOVIMIENTOS
    Route::group(['middleware' => ['auth', 'empresaActiva', 'notificaciones', 'sesionCaducadaUsuario']], function () {
        Route::get('sucursal/{idFranquicia}/cobranza/movimientos', 'App\Http\Controllers\Dominios\Administracion\cobranza@mostrarMovimientos')->name('cobranzamovimientos');
        Route::get('/reiniciarcorte', 'App\Http\Controllers\Dominios\Administracion\cobranza@cobranzamovimientosreiniciarcorte')->name('cobranzamovimientosreiniciarcorte');
        Route::get('sucursal/{idFranquicia}/ventas/movimientos', 'App\Http\Controllers\Dominios\Administracion\ventas@mostrarMovimientosVentas')->name('ventasmovimientos');
        Route::get('/movimientostiemporeal', 'App\Http\Controllers\Dominios\Administracion\cobranza@movimientostiemporeal')->name('movimientostiemporeal');
        Route::get('sucursal/{idFranquicia}/ventas/movimientos/filtro', 'App\Http\Controllers\Dominios\Administracion\ventas@filtrarMovimientosVentas')->name('filtrarventasmovimientos');
        Route::get('sucursal/cobranza/movimientos/marcarcontratocortellamada', 'App\Http\Controllers\Dominios\Administracion\cobranza@marcarcontratocortellamada')->name('marcarcontratocortellamada');
        Route::get('sucursal/{idFranquicia}/cobranza/llamadas', 'App\Http\Controllers\Dominios\Administracion\cobranza@llamadascobranza')->name('llamadascobranza');
        Route::get('/listallamadascobranza', 'App\Http\Controllers\Dominios\Administracion\cobranza@listallamadascobranza')->name('listallamadascobranza');
        Route::get('sucursal/{idFranquicia}/cobranza/movimientos/{idUsuario}/usuario', 'App\Http\Controllers\Dominios\Administracion\cobranza@validacioncortecobranza')->name('cobranzamovimientosvalidacioncorte');
    });

//REPORTES
    Route::group(['middleware' => ['auth', 'empresaActiva', 'notificaciones', 'sesionCaducadaUsuario']], function () {
        Route::get('sucursal/{idFranquicia}/reporte/enviados/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@listacontratosreportes')->name('listacontratosreportes');
        Route::post('sucursal/{idFranquicia}/reporte/enviados/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@filtrarlistacontratosreportes')->name('filtrarlistacontratosreportes');
        Route::get('sucursal/{idFranquicia}/reporte/cuentasactivas/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@listacontratoscuentasactivas')->name('listacontratoscuentasactivas');
        Route::post('reporte/cuentasactivas/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@filtrarlistacontratoscuentasactivas')->name('filtrarlistacontratoscuentasactivas');
        Route::get('sucursal/{idFranquicia}/reporte/paquetes/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@listacontratospaquetes')->name('listacontratospaquetes');
        Route::get('reporte/cuentasfisicas/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@validarCuentasLocalPagina')->name('cuentasfisicas');
        Route::post('reporte/cuentasfisicas/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@validarCuentasLocalPaginaArchivo')->name('cuentasfisicasarchivo');
        Route::get('/paquetestiemporeal', 'App\Http\Controllers\Dominios\Administracion\reportes@paquetestiemporeal')->name('paquetestiemporeal');
        Route::get('/cuentasactivastiemporeal', 'App\Http\Controllers\Dominios\Administracion\reportes@cuentasactivastiemporeal')->name('cuentasactivastiemporeal');
        Route::get('sucursal/{idFranquicia}/reporte/cancelados/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@listacontratoscancelados')->name('listacontratoscancelados');
        Route::get('sucursal/{idFranquicia}/reporte/pagados/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@listacontratospagados')->name('listacontratospagados');
        Route::get('/contratosPagados', 'App\Http\Controllers\Dominios\Administracion\reportes@contratosPagadosTiempoReal')->name('contratosPagados');
        Route::get('/contratosCancelados', 'App\Http\Controllers\Dominios\Administracion\reportes@contratosCanceladosTiempoReal')->name('contratosCancelados');
        Route::get('sucursal/{idFranquicia}/reporte/movimientos/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@reportemovimientos')->name('reportemovimientos');
        Route::get('/usuariosreportemovimiento', 'App\Http\Controllers\Dominios\Administracion\reportes@usuariosreportemovimiento')->name('usuariosreportemovimiento');
        Route::get('/filtroreportemovimientos', 'App\Http\Controllers\Dominios\Administracion\reportes@filtroreportemovimientos')->name('filtroreportemovimientos');
        Route::get('sucursal/{idFranquicia}/reporte/graficas', 'App\Http\Controllers\Dominios\Administracion\reportes@reportegraficas')->name('reportegraficas');
        Route::get('/creargraficaventas', 'App\Http\Controllers\Dominios\Administracion\reportes@creargraficaventas')->name('creargraficaventas');
        Route::get('/obtenerUsuariosFranquicia', 'App\Http\Controllers\Dominios\Administracion\reportes@obtenerUsuariosFranquicia')->name('obtenerUsuariosFranquicia');
        Route::get('sucursal/{idFranquicia}/reporte/llamadas/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@reportellamadas')->name('reportellamadas');
        Route::get('/listareportellamadas', 'App\Http\Controllers\Dominios\Administracion\reportes@listareportellamadas')->name('listareportellamadas');
        Route::get('sucursal/{idFranquicia}/reporte/asistencia/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@listareporteasistencia')->name('listareporteasistencia');
        Route::get('/diasSemanaSeleccionada', 'App\Http\Controllers\Dominios\Administracion\reportes@obtenerdiasasistencia')->name('obtenerdiasasistencia');
        Route::get('/cargarListaAsistencia', 'App\Http\Controllers\Dominios\Administracion\reportes@cargarListaAsistencia')->name('cargarListaAsistencia');
        Route::get('/registrarAsistenciaUsuario', 'App\Http\Controllers\Dominios\Administracion\reportes@registrarAsistenciaUsuario')->name('registrarAsistenciaUsuario');
        Route::get('sucursal/{idFranquicia}/reporte/buzon/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@reportebuzon')->name('reportebuzon');
        Route::get('/reportebuzontiemporeal', 'App\Http\Controllers\Dominios\Administracion\reportes@reportebuzontiemporeal')->name('reportebuzontiemporeal');
        Route::get('sucursal/{idFranquicia}/reporte/movimientos/sucursal/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@listareportehistorialsucursal')->name('listareportehistorialsucursal');
        Route::get('/cargarListaMovimientosSucursal', 'App\Http\Controllers\Dominios\Administracion\reportes@cargarlistamovimientossucursal')->name('cargarlistamovimientossucursal');
        Route::get('sucursal/{idFranquicia}/reporte/contratos/armazones/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@listareportearmazones')->name('listareportearmazones');
        Route::post('sucursal/{idFranquicia}/reporte/contratos/armazones/tabla/filtrar', 'App\Http\Controllers\Dominios\Administracion\reportes@filtrareportearmazones')->name('filtrareportearmazones');
        Route::get('sucursal/{idFranquicia}/reporte/pagados/seguimiento/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@listacontratospagadosseguimiento')->name('listacontratospagadosseguimiento');
        Route::post('sucursal/{idFranquicia}/reporte/pagados/seguimiento/tabla/filtrar', 'App\Http\Controllers\Dominios\Administracion\reportes@filtrarlistacontratospagadosseguimiento')->name('filtrarlistacontratospagadosseguimiento');
        Route::get('sucursal/{idFranquicia}/reporte/productos/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@listareporteproductos')->name('listareporteproductos');
        Route::post('sucursal/{idFranquicia}/reporte/productos/tabla/filtrar', 'App\Http\Controllers\Dominios\Administracion\reportes@listareporteproductosfiltrar')->name('listareporteproductosfiltrar');
        Route::get('sucursal/{idFranquicia}/reporte/contratos/movimientos/tabla', 'App\Http\Controllers\Dominios\Administracion\reportes@reportemovimientoscontratos')->name('reportemovimientoscontratos');
        Route::post('sucursal/{idFranquicia}/reporte/contratos/movimientos/filtrar', 'App\Http\Controllers\Dominios\Administracion\reportes@reportemovimientoscontratosfiltrar')->name('reportemovimientoscontratosfiltrar');
        Route::get('/cargarlistazonasfranquicia', 'App\Http\Controllers\Dominios\Administracion\reportes@cargarlistazonasfranquicia')->name('cargarlistazonasfranquicia');
        Route::get('/cargarlistacoloniasfranquicia', 'App\Http\Controllers\Dominios\Administracion\reportes@cargarlistacoloniasfranquicia')->name('cargarlistacoloniasfranquicia');
        Route::get('sucursal/{idFranquicia}/reporte/abonos', 'App\Http\Controllers\Dominios\Administracion\reportes@reporteabonossucursal')->name('reporteabonossucursal');
        Route::post('sucursal/{idFranquicia}/reporte/abonos/filtrar', 'App\Http\Controllers\Dominios\Administracion\reportes@filtrarreporteabonossucursal')->name('filtrarreporteabonossucursal');
        Route::get('sucursal/{idFranquicia}/reporte/contratos/supervision', 'App\Http\Controllers\Dominios\Administracion\reportes@reportecontratossupervision')->name('reportecontratossupervision');
        Route::post('sucursal/{idFranquicia}/reporte/contratos/supervision/filtrar', 'App\Http\Controllers\Dominios\Administracion\reportes@reportecontratossupervisionfiltrar')->name('reportecontratossupervisionfiltrar');
    });

//PROMOCIONES
    Route::group(['middleware' => ['auth', 'empresaActiva', 'notificaciones', 'sesionCaducadaUsuario']], function () {
        Route::get('sucursal/{idFranquicia}/promocion/nueva', 'App\Http\Controllers\Dominios\Administracion\promociones@promocionnueva')->name('promocionnueva');
        Route::get('sucursal/{idFranquicia}/promocion/armazones/nueva', 'App\Http\Controllers\Dominios\Administracion\promociones@nuevapromoarmazones')->name('nuevaproarmazones');
        Route::post('sucursal/{idFranquicia}/promocion/armazones/nueva', 'App\Http\Controllers\Dominios\Administracion\promociones@promocioncrear')->name('promocioncrear');
        Route::get('sucursal/{idFranquicia}/promocion/{idPromocion}/actualizar', 'App\Http\Controllers\Dominios\Administracion\promociones@promocionactualizar')->name('promocionactualizar');
        Route::post('sucursal/{idFranquicia}/promocion/{idPromocion}/actualizar', 'App\Http\Controllers\Dominios\Administracion\promociones@promocioneditar')->name('promocioneditar');
        Route::get('sucursal/{idFranquicia}/promocion/{idPromocion}/actualizar/estado', 'App\Http\Controllers\Dominios\Administracion\promociones@estadoPromocionEditar')->name('estadoPromocionEditar');

    });

//CONFIRMACIONES
    Route::group(['middleware' => ['auth', 'empresaActiva', 'sesionCaducadaUsuario']], function () {
        Route::get('confirmaciones/tabla', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@listaconfirmaciones')->name('listaconfirmaciones');
        Route::get('confirmacion/{idContrato}/estado', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@estadoconfirmacion')->name('estadoconfirmacion');
        Route::post('confirmacion/{idContrato}/estado', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@estadoconfirmacionactualizar')->name('estadoconfirmacionactualizar');
        Route::get('confirmacion/{idContrato}/estado/comentario', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@comentarioconfirmacion')->name('comentarioconfirmacion');
        Route::post('confirmacion/{idContrato}/agregar-documentos', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@confirmacionesagregardocumentos')->name('confirmacionesagregardocumentos');
        Route::post('confirmacion/{idContrato}/actualizar-contrato', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@actualizarContratoConfirmaciones')->name('actualizarContratoConfirmaciones');
        Route::post('confirmacion/{idContrato}/actualizar-total-contrato', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@actualizarTotalContratoConfirmacioness')->name('actualizarTotalContratoConfirmaciones');
        Route::post('confirmacion/{idContrato}/rechazar', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@rechazarContratoConfirmaciones')->name('rechazarContratoConfirmaciones');
        Route::post('confirmacion/{idContrato}/historial/{idHistorial}/{opcion}/actualizar', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@observacioninternalaboratoriohistorial')->name('observacioninternalaboratoriohistorial');
        Route::post('confirmacion/{idContrato}/actualizar-fechaentrega', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@actualizarfechaentregaconfirmaciones')->name('actualizarfechaentregaconfirmaciones');
        Route::get('confirmacion/tablagarantias', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@listagarantiasconfirmaciones')->name('listagarantiasconfirmaciones');
        Route::get('confirmacion/{idContrato}/contratogarantia', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@vercontratogarantiaconfirmaciones')->name('vercontratogarantiaconfirmaciones');
        Route::post('confirmacion/contrato/{idContrato}/historialclinico/{idHistorial}/cancelargarantiaconfirmaciones', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@cancelarGarantiaHistorialConfirmaciones')->name('cancelarGarantiaHistorialConfirmaciones');
        Route::get('/listaconfirmacioneslaboratrio', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@listaconfirmacioneslaboratrio')->name('listaconfirmacioneslaboratrio');
        Route::post('confirmacion/{idContrato}/agregarproductoconfirmaciones', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@agregarproductoconfirmaciones')->name('agregarproductoconfirmaciones');
        Route::get('/listaconfirmacionesgarantiasprincipal', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@listaconfirmacionesgarantiasprincipal')->name('listaconfirmacionesgarantiasprincipal');
        Route::post('confirmacion/{idContrato}/diagnostico/actualizar', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@actualizardiagnosticoconfirmaciones')->name('actualizardiagnosticoconfirmaciones');
        Route::post('confirmacion/contrato/{idContrato}/pago', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@actualizarformapagoconfirmaciones')->name('actualizarformapagoconfirmaciones');
        Route::get('confirmacion/{idContrato}/restablecercontratoconfirmaciones', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@restablecercontratoconfirmaciones')->name('restablecercontratoconfirmaciones');
        Route::post('confirmacion/{idContrato}/historialclinico/{idHistorial}/actualizarpaquetehistorialconfirmaciones', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@actualizarpaquetehistorialconfirmaciones')->name('actualizarpaquetehistorialconfirmaciones');
        Route::post('confirmacion/contrato/{idContrato}/nota', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@agregarnotaconfirmaciones')->name("agregarnotaconfirmaciones");
        Route::post('confirmacion/contrato/{idContrato}/historialclinico/{idHistorial}/actualizararmazonconfirmaciones', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@editarHistorialArmazonConfirmaciones')->name('actualizararmazonconfirmaciones');
        Route::post('confirmaciones/contratos/{idContrato}/abonominimocontrato/solicitar', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@solicitarautorizacionabonominimoconfirmaciones')->name('solicitarautorizacionabonominimoconfirmaciones');
        Route::get('confirmaciones/contratos/{idContrato}/agregarabonominimo', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@agregarabonominimoconfirmaciones')->name('agregarabonominimoconfirmaciones');
        Route::post('confirmaciones/contratos/{idContrato}/abonominimocontrato/actualizaresperapolizacontrato', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@actualizaresperapolizacontrato')->name("actualizaresperapolizacontrato");
        Route::post('confirmaciones/contratos/{idContrato}/movientoscontratos/agregar', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@agregarhistorialmovimientocontratoconfirmaciones')->name("agregarhistorialmovimientocontratoconfirmaciones");
        Route::get('confirmaciones/contratos/{idContrato}/historialclinico/{idHistorial}/indice/{indiceHistorial}/eliminar', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@eliminarhistorialclinicoconfirmaciones')->name("eliminarhistorialclinicoconfirmaciones");
        Route::get('confirmaciones/contratos/{idContrato}/promocion/agregar', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@agregarpromocionconfirmaciones')->name("agregarpromocionconfirmaciones");
        Route::get('confirmaciones/contratos/{idContrato}/promocion/{idPromocion}/eliminar', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@eliminarpromocionconfirmaciones')->name('eliminarpromocionconfirmaciones');
        Route::post('confirmaciones/contratos/{idContrato}/lugarentrega/actualizar', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@actualizarlugarentregaconfirmaciones')->name('actualizarlugarentregaconfirmaciones');
        Route::post('confirmaciones/contratos/{idContrato}/historialclinico/{idHistorial}/actualizar', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@actualizarhistorialclinicoconfirmaciones')->name('actualizarhistorialclinicoconfirmaciones');
        Route::post('confirmaciones/contratos/{idContrato}/historialclinico/{idHistorial}/fotoarmazon/actualizar', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@actualizarfotoarmazonconfirmaciones')->name('actualizarfotoarmazonconfirmaciones');
        Route::get('/listaconfirmacionesrechazadosprincipal', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@listaconfirmacionesrechazadosprincipal')->name('listaconfirmacionesrechazadosprincipal');
        Route::get('confirmaciones/contratos/{idContrato}/actualizar/restablecercontratorechazado', 'App\Http\Controllers\Dominios\Administracion\confirmaciones@restablecercontratorechazado')->name('restablecercontratorechazado');

    });

//LABORATORIO
    Route::group(['middleware' => ['auth', 'empresaActiva', 'notificaciones', 'sesionCaducadaUsuario']], function () {
        Route::get('laboratorio/tabla', 'App\Http\Controllers\Dominios\Administracion\laboratorio@listalaboratorio')->name('listalaboratorio');
        Route::get('laboratorio/{idContrato}/estado', 'App\Http\Controllers\Dominios\Administracion\laboratorio@estadolaboratorio')->name('estadolaboratorio');
        Route::post('laboratorio/{idContrato}/estado', 'App\Http\Controllers\Dominios\Administracion\laboratorio@estadolaboratorioactualizar')->name('estadolaboratorioactualizar');
        Route::post('laboratorio/{idContrato}/rechazar', 'App\Http\Controllers\Dominios\Administracion\laboratorio@rechazarContratoLaboratorio')->name('rechazarContratoLaboratorio');
        Route::get('laboratorio/{idContrato}/estado/comentario', 'App\Http\Controllers\Dominios\Administracion\laboratorio@comentariolaboratorio')->name('comentariolaboratorio');
        Route::get('laboratorio/auxiliar', 'App\Http\Controllers\Dominios\Administracion\laboratorio@auxiliarlaboratorio')->name('auxiliarlaboratorio');
        Route::get('laboratorio/tabla/actualizarestadoenviado', 'App\Http\Controllers\Dominios\Administracion\laboratorio@actualizarestadoenviado')->name('actualizarestadoenviado');
        Route::get('laboratorio/tabla/filtrarcontratosenviados', 'App\Http\Controllers\Dominios\Administracion\laboratorio@filtrarcontratosenviados')->name('filtrarcontratosenviados');
        Route::get('/contratosenviadostiemporeal', 'App\Http\Controllers\Dominios\Administracion\laboratorio@contratosenviadostiemporeal')->name('contratosenviadostiemporeal');
        Route::post('laboratorio/contrato/{idContrato}/historialclinico/{idHistorial}/cancelargarantialaboratorio', 'App\Http\Controllers\Dominios\Administracion\laboratorio@cancelarGarantiaHistorialLaboratorio')->name('cancelarGarantiaHistorialLaboratorio');
        Route::post('laboratorio/contrato/{idContrato}/historialclinico/{idHistorial}/actualizararmazonlaboratorio', 'App\Http\Controllers\Dominios\Administracion\laboratorio@actualizararmazonlaboratorio')->name('actualizararmazonlaboratorio');
        Route::post('laboratorio/contrato/{idContrato}/agregarproductoarmazoncontratolaboratorio', 'App\Http\Controllers\Dominios\Administracion\laboratorio@agregarproductoarmazoncontratolaboratorio')->name('agregarproductoarmazoncontratolaboratorio');
        Route::get('laboratorio/contrato/{idContrato}/agregarhistorialmovimientolaboratorio', 'App\Http\Controllers\Dominios\Administracion\laboratorio@agregarhistorialmovimientolaboratorio')->name('agregarhistorialmovimientolaboratorio');
        Route::get('laboratorio/productos/compra/lista', 'App\Http\Controllers\Dominios\Administracion\laboratorio@listaproductoslaboratorio')->name('listaproductoslaboratorio');
        Route::post('laboratorio/productos/compra/lista/filtrar', 'App\Http\Controllers\Dominios\Administracion\laboratorio@filtrarlistaproductoslaboratorio')->name('filtrarlistaproductoslaboratorio');
        Route::post('laboratorio/contrato/{idContrato}/{idHistorial}/observacion', 'App\Http\Controllers\Dominios\Administracion\laboratorio@actualizarobservaciones')->name("actualizarobservaciones");
        Route::get('laboratorio/productos/armazones', 'App\Http\Controllers\Dominios\Administracion\laboratorio@listaarmazoneslaboratorio')->name("listaarmazoneslaboratorio");
        Route::post('laboratorio/productos/armazones/baja/solicitar', 'App\Http\Controllers\Dominios\Administracion\laboratorio@crearsolicitudarmazonbaja')->name("crearsolicitudarmazonbaja");
        Route::get('laboratorio/productos/armazones/solicitud/{indice}/{opcion}/rechazarautorizar', 'App\Http\Controllers\Dominios\Administracion\laboratorio@solicitudarmazonbajarechazarautorizar')->name("solicitudarmazonbajarechazarautorizar");
        //Ejecutar solo una vez
        Route::get('laboratorio/contratos/tabla/contratoslaboratorio', 'App\Http\Controllers\Dominios\Administracion\laboratorio@llenarTablaContratosLaboratorio')->name('llenarTablaContratosLaboratorio');
    });


//GENERAL
    Route::group(['middleware' => ['auth', 'sesionCaducadaUsuario']], function () {
        Route::get('sucursal/{idFranquicia}/general', 'App\Http\Controllers\Dominios\Administracion\general@listas')->name('general');
        Route::get('sucursal/{idFranquicia}/general/dispositivo/nuevo/', 'App\Http\Controllers\Dominios\Administracion\general@dispositivonuevo')->name('dispositivonuevo');
        Route::post('sucursal/{idFranquicia}/general/dispositivo/nuevo', 'App\Http\Controllers\Dominios\Administracion\general@dispositivocrear')->name('dispositivocrear');
        Route::get('sucursal/{idFranquicia}/general/dispositivo/{id}/estatus/{estatus}', 'App\Http\Controllers\Dominios\Administracion\general@dispositivoestatus')->name('dispositivoestatus');
        Route::get('sucursal/{idFranquicia}/general/configuracion', 'App\Http\Controllers\Dominios\Administracion\general@configuracion')->name('configuracion');
        Route::post('general/configuracion/actualizar', 'App\Http\Controllers\Dominios\Administracion\general@actualizarConfiguracion')->name('actualizarConfiguracion');
        Route::post('general/configuracionftp/actualizar', 'App\Http\Controllers\Dominios\Administracion\general@actualizarconfiguracionftp')->name('actualizarconfiguracionftp');
    });

//SERVICIO WEB TRABAJADOR UNO
    Route::post('/api/servicio/iniciarsesion', 'App\Http\Controllers\Dominios\Administracion\serviciowebtrabajadoruno@iniciarsesion');
    Route::post('/api/servicio/sincronizaruno', 'App\Http\Controllers\Dominios\Administracion\serviciowebtrabajadoruno@sincronizaruno');
    Route::post('/api/servicio/sincronizarcero', 'App\Http\Controllers\Dominios\Administracion\serviciowebtrabajadoruno@sincronizarcero');
    Route::post('/api/servicio/sincronizardos', 'App\Http\Controllers\Dominios\Administracion\serviciowebtrabajadoruno@sincronizardos');
    Route::post('/api/servicio/cerrarsesion', 'App\Http\Controllers\Dominios\Administracion\serviciowebtrabajadoruno@cerrarsesion');
    Route::post('/api/servicio/supervision', 'App\Http\Controllers\Dominios\Administracion\serviciowebtrabajadoruno@verificarsupervisionvehiculo');
    Route::post('/api/servicio/verificarfotossupervision', 'App\Http\Controllers\Dominios\Administracion\serviciowebtrabajadoruno@verificarfotossupervisionvehiculo');
    Route::post('/api/servicio/registrar', 'App\Http\Controllers\Dominios\Administracion\serviciowebtrabajadoruno@registrarsupervisionvehiculo');
    Route::post('/api/servicio/historialmovimientos/contrato', 'App\Http\Controllers\Dominios\Administracion\serviciowebtrabajadoruno@obtenerhistorialmovimientoscontratos');

//STRIPE
    Route::get('/payment/{idFranquicia}/{idContrato}/{abono}/{banderacase}/{nuevoabono}/{cantidadsubscripcion}', 'App\Http\Controllers\Dominios\Administracion\PaymentController@index')->name('payment');
    Route::get('/charge/{idFranquicia}/contrato/{idContrato}/pagar/{abono}/{banderacase}/{nuevoabono}/{cantidadsubscripcion}', 'App\Http\Controllers\Dominios\Administracion\PaymentController@charge')->name('charge');


//LOGIN
    Route::get('iniciar-sesion', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
    Route::post('iniciar-sesion', 'App\Http\Controllers\Auth\LoginController@login');
    Route::post('salir', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');

    Route::get('registro', 'App\Http\Controllers\Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('registro', 'App\Http\Controllers\Auth\RegisterController@register');

    Route::get('restablecer/contraseña', 'App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('correo/contraseña', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('restablecer/contraseña/{token}', 'App\Http\Controllers\Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('restablcer/contraseña', 'App\Http\Controllers\Auth\ResetPasswordController@reset');

    Route::get('confirmar/contraseña', ' App\Http\Controllers\Auth\ConfirmPasswordController@showConfirmForm');
    Route::post('confirmar/contraseña', 'App\Http\Controllers\Auth\ConfirmPasswordController@confirm');

    Route::get('webfonts/fa-solid-900.ttf', function () {
        return;
    });
    Route::get('webfonts/fa-solid-900.woff2', function () {
        return;
    });
    Route::get('webfonts/fa-solid-900.woff ', function () {
        return;
    });

//TUTORIALES
    Route::group(['middleware' => ['auth', 'empresaActiva', 'sesionCaducadaUsuario']], function () {
        Route::get('tutoriales/{idFranquicia}/lista', 'App\Http\Controllers\Dominios\Administracion\tutoriales@listatutoriales')->name('listatutoriales');
        Route::post('tutoriales/{idFranquicia}/lista/filtrar', 'App\Http\Controllers\Dominios\Administracion\tutoriales@listatutorialesfiltrar')->name('listatutorialesfiltrar');
        Route::post('tutoriales/{idFranquicia}/video/agregar', 'App\Http\Controllers\Dominios\Administracion\tutoriales@agregarvideotutorial')->name('agregarvideotutorial');
        Route::get('tutoriales/{idFranquicia}/video/{idVideo}/eliminar', 'App\Http\Controllers\Dominios\Administracion\tutoriales@eliminarvideotutorial')->name('eliminarvideotutorial');
    });

//VEHICULOS
    Route::group(['middleware' => ['auth', 'empresaActiva', 'notificaciones', 'sesionCaducadaUsuario']], function () {
        Route::get('vehiculos/{idFranquicia}/tabla', 'App\Http\Controllers\Dominios\Administracion\vehiculos@listavehiculos')->name('listavehiculos');
        Route::get('/cargarlistavehiculos', 'App\Http\Controllers\Dominios\Administracion\vehiculos@cargarlistavehiculos')->name('cargarlistavehiculos');
        Route::post('vehiculos/{idFranquicia}/crear', 'App\Http\Controllers\Dominios\Administracion\vehiculos@nuevovehiculo')->name('nuevovehiculo');
        Route::get('vehiculos/{idFranquicia}/{idVehiculo}/editar', 'App\Http\Controllers\Dominios\Administracion\vehiculos@vervehiculo')->name('vervehiculo');
        Route::post('vehiculos/{idFranquicia}/{idVehiculo}/editar', 'App\Http\Controllers\Dominios\Administracion\vehiculos@actualizarvehiculo')->name('actualizarvehiculo');
        Route::post('vehiculos/{idFranquicia}/{idVehiculo}/servicio', 'App\Http\Controllers\Dominios\Administracion\vehiculos@registrarnuevoservicio')->name('registrarnuevoservicio');
        Route::get('vehiculos/{idFranquicia}/{idVehiculo}/supervision/nueva', 'App\Http\Controllers\Dominios\Administracion\vehiculos@nuevasupervision')->name('nuevasupervision');
        Route::post('vehiculos/{idFranquicia}/{idVehiculo}/supervision/nueva/crear', 'App\Http\Controllers\Dominios\Administracion\vehiculos@crearnuevasupervision')->name('crearnuevasupervision');
        Route::get('vehiculos/{idFranquicia}/{idVehiculo}/supervision/{idSupervision}/actualizar', 'App\Http\Controllers\Dominios\Administracion\vehiculos@actualizarsupervisionvehiculo')->name('actualizarsupervisionvehiculo');
        Route::post('vehiculos/{idFranquicia}/{idVehiculo}/supervision/{idSupervision}/editar', 'App\Http\Controllers\Dominios\Administracion\vehiculos@editarsupervisionvehiculo')->name('editarsupervisionvehiculo');
        Route::get('vehiculos/{idFranquicia}/supervision/{idSupervision}/autorizar', 'App\Http\Controllers\Dominios\Administracion\vehiculos@autorizarsupervisionvehiculo')->name('autorizarsupervisionvehiculo');
        Route::post('vehiculo/sucursal/eliminar', 'App\Http\Controllers\Dominios\Administracion\vehiculos@eliminarVehiculo')->name('eliminarVehiculoSucursal');
        Route::post('vehiculos/{idFranquicia}/horario/fotos/actualizar', 'App\Http\Controllers\Dominios\Administracion\vehiculos@actualizarhorariolimitechofer')->name('actualizarhorariolimitechofer');
        Route::get('/descargar-factura-servicio/{idVehiculo}', 'App\Http\Controllers\Dominios\Administracion\vehiculos@descargarfacturaservicio')->name('descargarfacturaservicio');
        //Ejecutar sola una vez para actualizar tabla servicios
        Route::get('actualizarTablaServicio', 'App\Http\Controllers\Dominios\Administracion\vehiculos@actualizarTablaServiciosVehiculos');
    });

//CAMPAÑAS
Route::group(['middleware' => ['auth', 'empresaActiva', 'notificaciones', 'sesionCaducadaUsuario']], function () {
    Route::get('campania/{idFranquicia}/tabla', 'App\Http\Controllers\Dominios\Administracion\campanias@listacampanias')->name('listacampanias');
    Route::post('campania/{idFranquicia}/crear', 'App\Http\Controllers\Dominios\Administracion\campanias@crearcampania')->name('crearcampania');
    Route::get('campania/{idFranquicia}/{idCampania}/ver', 'App\Http\Controllers\Dominios\Administracion\campanias@vercampania')->name('vercampania');
    Route::get('/cargarlistacampaniasagendadas', 'App\Http\Controllers\Dominios\Administracion\campanias@cargarlistacampaniasagendadas')->name('cargarlistacampaniasagendadas');
    Route::get('/agendarcampania', 'App\Http\Controllers\Dominios\Administracion\campanias@agendarcampania')->name('agendarcampania');
    Route::post('campania/actualizar', 'App\Http\Controllers\Dominios\Administracion\campanias@actualizarcampania')->name('actualizarcampania');
    Route::post('campania/datos/paciente/actualizar', 'App\Http\Controllers\Dominios\Administracion\campanias@actualizardatospaciente')->name('actualizardatospaciente');
    Route::post('campania/eliminar', 'App\Http\Controllers\Dominios\Administracion\campanias@eliminarcampania')->name('eliminarcampania');
});

//APP ESCRITORIO LABORATORIO
    Route::post('/api/laboratorio/servicio/iniciarsesion', 'App\Http\Controllers\Dominios\Administracion\servicioweblaboratorio@iniciarsesionlaboratorio');
    Route::post('/api/laboratorio/servicio/cerrarsesion', 'App\Http\Controllers\Dominios\Administracion\servicioweblaboratorio@cerrarsesionlaboratorio');
    Route::get('/api/laboratorio/servicio/estatusconexion', 'App\Http\Controllers\Dominios\Administracion\servicioweblaboratorio@verificarConexionAppLaboratorio');
    Route::post('/api/laboratorio/servicio/filtrar', 'App\Http\Controllers\Dominios\Administracion\servicioweblaboratorio@filtrarContrato');
    Route::post('/api/laboratorio/servicio/enviados/filtrar', 'App\Http\Controllers\Dominios\Administracion\servicioweblaboratorio@filtrarContratosEnviados');
    Route::get('/api/laboratorio/servicio/sucursales', 'App\Http\Controllers\Dominios\Administracion\servicioweblaboratorio@listaSucursales');
    Route::post('/api/laboratorio/servicio/contrato/estado', 'App\Http\Controllers\Dominios\Administracion\servicioweblaboratorio@estadolaboratorio');
    Route::post('/api/laboratorio/servicio/contrato/actualizar', 'App\Http\Controllers\Dominios\Administracion\servicioweblaboratorio@actualizarContratoLaboratorio');
    Route::post('/api/laboratorio/servicio/contratos/enviar', 'App\Http\Controllers\Dominios\Administracion\servicioweblaboratorio@actualizarestadoenviado');
    Route::get('/api/laboratorio/servicio/autorizaciones', 'App\Http\Controllers\Dominios\Administracion\servicioweblaboratorio@listaSolicitudesAutorizacion');
    Route::post('/api/laboratorio/servicio/solicitud/respuesta', 'App\Http\Controllers\Dominios\Administracion\servicioweblaboratorio@autorizarcontrato');
    Route::post('/api/laboratorio/servicio/sincronizarBD', 'App\Http\Controllers\Dominios\Administracion\servicioweblaboratorio@sincronizarBD');
    Route::post('/api/laboratorio/servicio/armazones/control', 'App\Http\Controllers\Dominios\Administracion\servicioweblaboratorio@controlArmazonesLaboratorio');

Route::group(['middleware' => ['auth', 'empresaActiva', 'notificaciones', 'sesionCaducadaUsuario']], function () {
    Route::get('citas/{idFranquicia}/lista', 'App\Http\Controllers\Dominios\Administracion\citas@listaagendacitas')->name('listaagendacitas');
    Route::get('/cargaragendacitaspacientes', 'App\Http\Controllers\Dominios\Administracion\citas@cargaragendacitaspacientes')->name('cargaragendacitaspacientes');
    Route::post('citas/{idFranquicia}/agendar', 'App\Http\Controllers\Dominios\Administracion\citas@agendarcitaadministracion')->name('agendarcitaadministracion');
    Route::get('/notificarcitapaciente', 'App\Http\Controllers\Dominios\Administracion\citas@notificarcitapacienteadministracion')->name('notificarcitapacienteadministracion');
});

//ADMINISTRACION PAGINA CLIENTES
Route::group(['middleware' => ['auth', 'empresaActiva', 'notificaciones', 'sesionCaducadaUsuario']], function () {
    Route::get('sucursal/{idFranquicia}/administracion/imagenes', 'App\Http\Controllers\Dominios\Administracion\adminpaginaclientes@administracionimagenes')->name('administracionimagenes');
    Route::post('sucursal/{idFranquicia}/administracion/imagenes/nueva', 'App\Http\Controllers\Dominios\Administracion\adminpaginaclientes@agregarimagencarrucel')->name('agregarimagencarrucel');
    Route::post('sucursal/{idFranquicia}/administracion/imagenes/posicion/actualizar', 'App\Http\Controllers\Dominios\Administracion\adminpaginaclientes@actualizarposicionimagencarrucel')->name('actualizarposicionimagencarrucel');
    Route::get('sucursal/{idFranquicia}/administracion/imagenes/{idImagen}/eliminar', 'App\Http\Controllers\Dominios\Administracion\adminpaginaclientes@eliminarimagencarrusel')->name('eliminarimagencarrusel');
});

//Funciones varias
    Route::group(['middleware' => ['auth', 'empresaActiva', 'notificaciones', 'sesionCaducadaUsuario']], function () {
        Route::get('generar-cadenas/{tamanio}/{cantidad}', function ($tamanio, $cantidad) {
            if ($tamanio < 1 || $cantidad < 1) {
                return response('Los parámetros no pueden ser menores a 0.', 400);
            }

            if ($tamanio > 100) {
                return response('El tamaño máximo de cadena es 100.', 400);
            }

            if ($cantidad > 100) {
                return response('El número máximo de cadenas es 100.', 400);
            }

            $codigos = [];

            for ($i = 0; $i < $cantidad; $i++) {
                $codigo = Str::random($tamanio);
                $codigos[] = $codigo;
            }

            return $codigos;
        });
    });

//***************************** PARA CUALQUIER RUTA NO REGISTRADA ********************************************************
    Route::get('{any}', function (Request $request) {
        if (Auth::check()) {
            return redirect()->route('redireccionar');
        } else {
            return redirect()->route('login');
        }
    })->where('any', '.*');

/*});             //Comentar linea para ingresar a pagina administracion

//Comentar todo el siguiente bloque de rutas para ingresar a subdominio - pagina de administracion

//Dominio - Rutas para pagina clientes
Route::get('/', function () {
    return redirect()->route('bienvenida');
});

Route::get('/home', function () {
    return redirect()->route('bienvenida');
});

//RUTAS NAVEGACION MENU
Route::get('inicio', 'App\Http\Controllers\Dominios\Clientes\navegacion@bienvenidaClientes')->name('bienvenida');
Route::get('menu/horario-de-atencion', 'App\Http\Controllers\Dominios\Clientes\navegacion@horariodeatencion')->name('horariodeatencion');
Route::get('menu/sobre-mi', 'App\Http\Controllers\Dominios\Clientes\navegacion@informaciongeneral')->name('informaciongeneral');
Route::get('menu/rastreo/lentes', 'App\Http\Controllers\Dominios\Clientes\navegacion@formulariorastreo')->name('formulariorastreo');
Route::get('menu/productos/lista', 'App\Http\Controllers\Dominios\Clientes\navegacion@productoslista')->name('productoslista');
Route::get('menu/servicios/lista', 'App\Http\Controllers\Dominios\Clientes\navegacion@servicioslista')->name('servicioslista');

//RASTREO DE CONTRATO - ESTADO DE CUENTA
Route::post('contrato/rastrear', 'App\Http\Controllers\Dominios\Clientes\navegacion@rastrearContrato')->name('rastrearcontrato');

//AGENDA DE CITAS
Route::get('/filtrarSucursales', 'App\Http\Controllers\Dominios\Clientes\navegacion@filtrarSucursales')->name('filtrarSucursales');
Route::get('visitanos', 'App\Http\Controllers\Dominios\Clientes\navegacion@listavisitanosucursales')->name('visitanos');

Route::group(['middleware' => ['proteccionRutaClientes']], function () {
    Route::get('cita/{idFranquicia}/vacantes', 'App\Http\Controllers\Dominios\Clientes\citas@vacantes')->name('vacantes');
    Route::get('cita/{idFranquicia}/vacantes/{idRol}/agendar', 'App\Http\Controllers\Dominios\Clientes\citas@vacantesagendarcita')->name('vacantesagendarcita');
    Route::get('/obtenervacantesfranquicia', 'App\Http\Controllers\Dominios\Clientes\citas@obtenervacantesfranquicia');
    Route::post('cita/{idFranquicia}/vacantes/cita/agendar', 'App\Http\Controllers\Dominios\Clientes\citas@agendarcitavacantesucursal')->name('agendarcitavacantesucursal');
    Route::get('cita/{idFranquicia}/calendario', 'App\Http\Controllers\Dominios\Clientes\citas@calendariocitas')->name('calendariocitas');
    Route::get('/obtenercitasdisponibles', 'App\Http\Controllers\Dominios\Clientes\citas@obtenercitasdisponibles')->name('obtenercitasdisponibles');
    Route::get('/agendarcita', 'App\Http\Controllers\Dominios\Clientes\citas@agedarcita')->name('agendarcita');
});*/

