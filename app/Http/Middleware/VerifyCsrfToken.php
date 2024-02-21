<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/api/servicio/iniciarsesion',
        '/api/servicio/sincronizaruno',
        '/api/servicio/sincronizarcero',
        '/api/servicio/sincronizardos',
        '/api/servicio/cerrarsesion',
        '/api/servicio/supervision',
        '/api/servicio/verificarfotossupervision',
        '/api/servicio/registrar',
        '/api/servicio/historialmovimientos/contrato',
        'servicio/agregararchivoexcel',
        'servicio/liquidararchivo',
        '/api/laboratorio/servicio/iniciarsesion',
        '/api/laboratorio/servicio/cerrarsesion',
        '/api/laboratorio/servicio/estatusconexion',
        '/api/laboratorio/servicio/filtrar',
        '/api/laboratorio/servicio/enviados/filtrar',
        '/api/laboratorio/servicio/contrato/estado',
        '/api/laboratorio/servicio/contrato/actualizar',
        '/api/laboratorio/servicio/contratos/enviar',
        '/api/laboratorio/servicio/solicitud/respuesta',
        '/api/laboratorio/servicio/sincronizarBD',
        '/api/laboratorio/servicio/armazones/control'
    ];
}
