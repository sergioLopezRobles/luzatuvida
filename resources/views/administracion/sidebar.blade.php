<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
<div class="contenedor-menu" style="border: transparent solid">
<ul class="list-unstyled ps-0">

    @if(((Auth::user()->rol_id == 7) && @isset($idFranquicia)) || Auth::user()->rol_id==6  || Auth::user()->rol_id == 8 || Auth::user()->rol_id == 20)
        <li class="mb-1">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#campanias-collapse">Campañas</button>
            <div class="collapse" id="campanias-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                    <li><a href="{{route('listacampanias',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Lista</a></li>
                </ul>
            </div>
        </li>
    @endif

    @if(((Auth::user()->rol_id == 7) && @isset($idFranquicia)) || Auth::user()->rol_id==6  || Auth::user()->rol_id == 8)
        <li class="mb-1">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#clientes-collapse">Clientes</button>
            <div class="collapse" id="clientes-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                    <li><a href="{{route('listaagendacitas',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Citas</a></li>
                    @if(Auth::user()->rol_id == 7)
                        <li><a href="{{route('administracionimagenes',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Administracion</a></li>
                    @endif
                </ul>
            </div>
        </li>
    @endif

    @if(Auth::user()->rol_id==15)
        <li class="mb-1">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#contratosConfirmaciones-collapse">Contratos</button>
            <div class="collapse" id="contratosConfirmaciones-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                    <li><a href="{{route('listasolicitudautorizacion',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Autorizaciones</a></li>
                    <li><a href="{{route('listaconfirmaciones')}}">Lista</a></li>
                    <li><a href="{{route('listagarantiasconfirmaciones')}}">Garantías</a></li>
                    <li><a href="{{route('traspasarcontrato',$idSucursalGlobal)}}">Traspasar</a></li>
                    <li><a href="{{route('reportecontratos',$idSucursalGlobal)}}">Reporte</a></li>
                </ul>
            </div>
        </li>
    @endif

    @if(Auth::user()->rol_id==7)
        <li class="mb-1">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#confirmacionesDirector-collapse">Confirmaciones</button>
            <div class="collapse" id="confirmacionesDirector-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                    <li><a href="{{route('listaconfirmaciones')}}">Lista</a></li>
                </ul>
            </div>
        </li>
    @endif

    @if(((Auth::user()->rol_id == 7) && @isset($idFranquicia)) || Auth::user()->rol_id==6 || Auth::user()->rol_id == 13 || Auth::user()->rol_id == 12)
        <li class="mb-1">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#contratos-collapse">Contratos</button>
            <div class="collapse" id="contratos-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                    @if(Auth::user()->rol_id == 7)
                        <li> <a href="{{route('listasolicitudautorizacion',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Autorizaciones</a></li>
                    @endif
                    <li><a href="{{route('listacontrato',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Lista</a></li>

                    @if(Auth::user()->rol_id == 7)
                        <li> <a href="{{route('migrarcuentas',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Migrar cuentas</a></li>
                    @endif
                    <li><button class="btn btn-toggle rounded collapsed collapse-toggle-sub" data-toggle="collapse" aria-haspopup="true" data-target="#traspasar-collapse">Traspasar</button> </li>
                    <div class="collapse" id="traspasar-collapse">
                        <ul class="btn-toggle-nav list-unstyled small collapse-item-sub">
                            <li><a href="{{route('traspasarcontrato',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Entre sucursales</a></li>
                            <li><a href="{{route('traspasarcontratozona',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Entre zonas</a></li>
                        </ul>
                    </div>
                    @if(Auth::user()->rol_id == 7 || Auth::user()->rol_id == 6)
                        <li><a href="{{route('reportecontratos',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Reporte</a></li>
                    @endif
                </ul>
            </div>
        </li>
    @endif

    @if(Auth::user()->rol_id==8)
        <li class="mb-1">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#contratos-collapse">Contratos</button>
            <div class="collapse" id="contratos-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                    <li> <a href="{{route('listasolicitudautorizacion',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Autorizaciones</a></li>
                    <li><a href="{{route('listacontrato',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Lista</a></li>
                    <li><button class="btn btn-toggle rounded collapsed collapse-toggle-sub" data-toggle="collapse" aria-haspopup="true" data-target="#traspasar-collapse">Traspasar</button> </li>
                    <div class="collapse" id="traspasar-collapse">
                        <ul class="btn-toggle-nav list-unstyled small collapse-item-sub">
                            <li><a href="{{route('traspasarcontrato',$idSucursalGlobal)}}">Entre sucursales</a></li>
                            <li><a href="{{route('traspasarcontratozona',$idSucursalGlobal)}}" target="_blank">Entre zonas</a></li>
                        </ul>
                    </div>
                    <li><a href="{{route('reportecontratos',$idSucursalGlobal)}}">Reporte</a></li>
                </ul>
            </div>
        </li>
    @endif

    @if(Auth::user()->rol_id == 7)
        <li class="mb-1">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#laboratorio-collapse">Laboratorio</button>
            <div class="collapse" id="laboratorio-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                    <li><a href="{{route('listaproductoslaboratorio')}}">Control de armazones</a></li>
                    <li><a href="{{route('listaarmazoneslaboratorio')}}">Inventario de armazones</a></li>
                    <li><a href="{{route('listalaboratorio')}}">Lista</a></li>
                </ul>
            </div>
        </li>
    @endif

    @if(Auth::user()->rol_id==16)
        <li class="mb-1">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#contratosLaboratorio-collapse">Contratos</button>
            <div class="collapse" id="contratosLaboratorio-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                    <li><a href="{{route('listasolicitudautorizacion',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Autorizaciones</a></li>
                    <li><a href="{{route('listaproductoslaboratorio')}}">Control de armazones</a></li>
                    <li><a href="{{route('listaarmazoneslaboratorio')}}">Inventario de armazones</a></li>
                    <li><a href="{{route('listalaboratorio')}}">Lista</a></li>
                </ul>
            </div>
        </li>
    @endif

    @if(((Auth::user()->rol_id == 7) && @isset($idFranquicia)) || Auth::user()->rol_id==6  || Auth::user()->rol_id == 8)
        <li class="mb-1">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#movimientos-collapse">Movimientos</button>
            <div class="collapse" id="movimientos-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                    <li><a href="{{route('cobranzamovimientos',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Abonos de cobranza</a></li>
                    <li><button class="btn btn-toggle rounded collapsed collapse-toggle-sub" data-toggle="collapse" aria-haspopup="true" data-target="#cobranza-collapse">Llamadas</button>
                    </li>
                    <div class="collapse" id="cobranza-collapse">
                        <ul class="btn-toggle-nav list-unstyled small collapse-item-sub">
                            <li><a href="{{route('llamadascobranza',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Entrar</a></li>
                        </ul>
                    </div>
                    <li><a href="{{route('ventasmovimientos',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Ventas</a></li>
                </ul>
            </div>
        </li>
    @endif

    @if(((Auth::user()->rol_id == 7) && @isset($idFranquicia)) || Auth::user()->rol_id==6 || Auth::user()->rol_id==8)
        <li class="mb-1">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#polizas-collapse">Polizas</button>
            <div class="collapse" id="polizas-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                    <li><a href="{{route('listapoliza',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Lista</a></li>
                </ul>
            </div>
        </li>
    @endif

    @if(((Auth::user()->rol_id == 7) && @isset($idFranquicia)) || Auth::user()->rol_id == 6  || Auth::user()->rol_id == 18)
            <li class="mb-1">
                <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#redes-collapse">Redes</button>
                <div class="collapse" id="redes-collapse">
                    <ul class="btn-toggle-nav list-unstyled small collapse-item">
                        <li><a href="{{route('reportevacantesmensajes',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Reporte de mensajes</a></li>
                        <li><a href="{{route('listavacantesadministracion',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Vacantes Administración</a></li>
                        @if(((Auth::user()->rol_id == 7) && @isset($idFranquicia)) ||  Auth::user()->rol_id == 18)
                        <li><a href="{{route('listavacantesredes',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Vacantes Redes</a></li>
                        @endif
                    </ul>
                </div>
            </li>
    @endif

    @if(((Auth::user()->rol_id == 7) && @isset($idFranquicia)) || Auth::user()->rol_id==6  || Auth::user()->rol_id == 8 || Auth::user()->rol_id==15)
        @if(Auth::user()->rol_id==15)
            <li class="mb-1">
                <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#reportes-collapse">Reportes</button>
                <div class="collapse" id="reportes-collapse">
                    <ul class="btn-toggle-nav list-unstyled small collapse-item">
                        <li><a href="{{route('listacontratosreportes',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Entrar</a></li>
                    </ul>
                </div>
            </li>
        @else
            <li class="mb-1">
                <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#reportes-collapse">Reportes</button>
                <div class="collapse" id="reportes-collapse">
                    <ul class="btn-toggle-nav list-unstyled small collapse-item">
                        @if(Auth::user()->rol_id==7)
                            <li><a href="{{route('reporteabonossucursal',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Abonos</a></li>
                            <li><a href="{{route('listareportearmazones',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Armazones</a></li>
                        @endif
                        <li><a href="{{route('listareporteasistencia',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Asistencia</a></li>
                        @if(Auth::user()->rol_id==6 || Auth::user()->rol_id==7  || Auth::user()->rol_id == 8)
                            <li><a href="{{route('reportebuzon',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Buzón</a></li>
                        @endif
                        <li><a href="{{route('listacontratoscancelados',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Cancelados</a></li>
                        <li><a href="{{route('listacontratoscuentasactivas',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Cuentas activas</a></li>
                        <li><a href="{{route('cuentasfisicas')}}" target="_blank">Cuentas fisicas</a></li>
                        <li><a href="{{route('listacontratosreportes',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Enviados</a></li>
                        @if(Auth::user()->rol_id==7)
                             <li><a href="{{route('reportegraficas',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Graficas</a></li>
                        @endif
                        @if(Auth::user()->rol_id==6 || Auth::user()->rol_id==7  || Auth::user()->rol_id == 8)
                            <li><a href="{{route('reportellamadas',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Llamadas</a></li>
                        @endif
                        <li><a href="{{route('reportemovimientos',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Movimientos</a></li>
                        <li><a href="{{route('reportemovimientoscontratos',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Movimientos contratos</a></li>
                        @if(Auth::user()->rol_id==7)
                           <li><a href="{{route('listareportehistorialsucursal',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Movimientos sucursal</a></li>
                        @endif
                        <li><button class="btn btn-toggle rounded collapsed collapse-toggle-sub" data-toggle="collapse" aria-haspopup="true" data-target="#pagados-collapse">Pagados</button> </li>
                            <div class="collapse" id="pagados-collapse">
                                <ul class="btn-toggle-nav list-unstyled small collapse-item-sub">
                                    <li><a href="{{route('listacontratospagados',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Lista pagados</a></li>
                                    <li><a href="{{route('listacontratospagadosseguimiento',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Seguimiento paciente</a></li>
                                </ul>
                            </div>
                        <li><a href="{{route('listacontratospaquetes',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Paquetes</a></li>
                        <li><a href="{{route('listareporteproductos',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Productos</a></li>
                        <li><a href="{{route('reportecontratossupervision',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}" target="_blank">Supervisión</a></li>
                    </ul>
                </div>
            </li>
        @endif
    @endif

    @if(Auth::user()->rol_id==7)
        <li class="mb-1">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#sucursal-collapse">Sucursales</button>
            <div class="collapse" id="sucursal-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                    <li><a href="{{route('nuevafranquicia')}}">Nueva</a></li>
                    <li><a href="{{route('listafranquicia')}}">Lista</a></li>
                </ul>
            </div>
        </li>
    @endif

    @if(Auth::user()->rol_id==6 || ((Auth::user()->rol_id == 7) && @isset($idFranquicia)) || Auth::user()->rol_id == 8)
       <li class="mb-1">
           <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#tutoriales-collapse">Tutoriales</button>
           <div class="collapse" id="tutoriales-collapse">
               <ul class="btn-toggle-nav list-unstyled small collapse-item">
                   <li><a href="{{route('listatutoriales',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Lista</a></li>
               </ul>
           </div>
       </li>
    @endif

    @if(Auth::user()->rol_id==6 || ((Auth::user()->rol_id == 7) && @isset($idFranquicia)) || Auth::user()->rol_id == 8)
        <li class="mb-1">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#vehiculos-collapse">Vehículos</button>
            <div class="collapse" id="vehiculos-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                    <li><a href="{{route('listavehiculos',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Lista</a></li>
                </ul>
            </div>
        </li>
    @endif

    @if(Auth::user()->rol_id == 4 || Auth::user()->rol_id == 17)
        <li class="mb-1">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#vehiculos-collapse">Vehículos</button>
            <div class="collapse" id="vehiculos-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                    <li><a href="{{route('redireccionar')}}">Entrar</a></li>
                </ul>
            </div>
        </li>
    @endif

    @if(Auth::user()->rol_id==8 || Auth::user()->rol_id==6 || ((Auth::user()->rol_id == 7) && @isset($idFranquicia)))
        <hr style="background:white;">
        <li class="mb-1">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#administracion-collapse">Administracion</button>
            <div class="collapse" id="administracion-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                    <li><a href="{{route('listasfranquicia',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Lista</a></li>
                </ul>
            </div>
        </li>
    @endif

    @if(Auth::user()->rol_id == 7 && @isset($idFranquicia))
        <li class="mb-1">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#desarrollo-collapse" style="margin-bottom:
            10px;">Desarrollo</button>
            <div class="collapse" id="desarrollo-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                    <li><a href="{{route('general',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Entrar</a></li>
                </ul>
            </div>
        </li>
    @endif

    @if(Auth::user()->rol_id == 7 && @isset($idFranquicia))
        <li class="mb-1">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#insumos-collapse">Insumos</button>
            <div class="collapse" id="insumos-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                    <li><a href="{{route('insumos',Auth::user()->rol_id == 7 ? $idFranquicia :  $idSucursalGlobal)}}">Entrar</a></li>
                </ul>
            </div>
        </li>
    @endif

     @if(Auth::user()->rol_id == 6 || Auth::user()->rol_id == 8)
        <li class="mb-1" style="margin-bottom: 25px;">
            <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#usuarios-collapse">Usuarios</button>
            <div class="collapse" id="usuarios-collapse">
                <ul class="btn-toggle-nav list-unstyled small collapse-item">
                     <li><a href="{{route('usuariosFranquicia',[$idSucursalGlobal])}}">Lista</a></li>
                     <li><a href="{{route('listavacantesadministracion',[$idSucursalGlobal])}}">Vacantes</a></li>
                </ul>
            </div>
         </li>
     @endif

        @if(Auth::user()->rol_id == 18 || Auth::user()->rol_id == 20)
            <li class="mb-1" style="margin-bottom: 25px;">
                <button class="btn btn-toggle rounded collapsed collapse-toggle" data-toggle="collapse" aria-haspopup="true" data-target="#usuarios-collapse">Vacantes</button>
                <div class="collapse" id="usuarios-collapse">
                    <ul class="btn-toggle-nav list-unstyled small collapse-item">
                        <li><a href="{{route('listavacantesredes',[$idSucursalGlobal])}}">Entrar</a></li>
                    </ul>
                </div>
            </li>
        @endif
</ul>
    @if(Auth::user()->rol_id == 7 && @isset($sucursalGlobalDirector))
        <div class="div-sucursal-director"> SUCURSAL: {{$sucursalGlobalDirector}} </div>
    @endif
</div>
