@if($errors->has('foto') || $errors->has('curp') || $errors->has('rfc') || $errors->has('hacienda') || $errors->has('actanacimiento') ||
    $errors->has('identificacion') || $errors->has('estado') || $errors->has('ciudad') || $errors->has('colonia') ||$errors->has('numero') || $errors->has('comprobante') || $errors->has('observaciones'))
  <script>
    closeNav();
    ocultarTodo("franquicia");   
  </script>
@endif
@if(Session::has('fcreada'))
  <script>  
    closeNav();
    ocultarTodo("franquiciaLista");
    limpiarFormularios();  
    actualizarListaFranquicias();
  </script>
@endif
