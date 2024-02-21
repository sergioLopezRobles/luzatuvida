<?php

namespace App\Console\Commands;

use App\Clases\contratosGlobal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class contratoslistatemporales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contratoslistatemporales:llenartabla';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Llenara tabla de contratoslistatemporales por sucursal a partir de obtener un rango de contratos por su indice';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        \Log::info("COMANDO CONTRATOS LISTA TEMPORALES : LLENAR TABLA EJECUTADO");

        //Franquicia ejecutando tarea de insertar contratos tabla contratoslistatemporales
        $franquiciapendiente = DB::select("SELECT * FROM franquiciaregistrostemporales WHERE tipotarea = 1 AND bandera = 1 ORDER BY indice LIMIT 1");

        if($franquiciapendiente == null) {
            //No hay franquicia pendiente

            $tareasEjecutar = DB::select("SELECT * FROM franquiciaregistrostemporales  WHERE tipotarea = 1 AND bandera = 0 ORDER BY indice LIMIT 1");

            //Existe franquicia con tarea asignada?
            if($tareasEjecutar != null) {

                //Recorrer tareas a ejecutar por sucursal
                foreach ($tareasEjecutar as $tarea) {

                    $idFranquicia = $tarea->id_franquicia;

                    //indice de tarea ejecuntando
                    $indiceTarea = $tarea->indice;
                    DB::update("UPDATE franquiciaregistrostemporales SET bandera = '1' WHERE indice = '$indiceTarea'");

                    //Indice de contratos recorridos por franquicia
                    $indice = DB::select("SELECT f.indicecontratos FROM franquicias f WHERE f.id = '$idFranquicia'");
                    $indiceInicial = $indice[0]->indicecontratos;
                    $indiceFinal = $indiceInicial + 10000;

                    try {

                        $contratos = DB::select("SELECT c.id FROM contratos c WHERE c.datos = 1 AND c.id_franquicia = '$idFranquicia'
                                                   AND c.indice >= '$indiceInicial' AND c.indice < '$indiceFinal'");

                        $ultimoContratoDB =DB::select("SELECT c.indice FROM contratos c ORDER BY c.indice DESC LIMIT 1");
                        $ultimoIndiceBD = $ultimoContratoDB[0]->indice;

                        foreach ($contratos as $contrato){
                            $idContrato = $contrato->id;
                            //Insertar contrato en tabla contratoslistatemporales
                            DB::select("INSERT INTO contratoslistatemporales (id, id_franquicia, estatus_estadocontrato, descripcion, idcontratorelacion, created_at,
                                                                fechaentrega,fechaatraso, fechagarantia, estadogarantia, nombre_usuariocreacion, id_zona,
                                                                zona, localidad, colonia, calle, numero, nombre, telefono, nombrereferencia, telefonoreferencia,
                                                                totalreal, totalproducto, totalpromocion, totalabono, total, ultimoabono, promocionactiva, alias)
                                                                SELECT c.id, c.id_franquicia, c.estatus_estadocontrato,
                                                                (SELECT e.descripcion FROM estadocontrato e WHERE e.estatus = c.estatus_estadocontrato) as descripcion,
                                                                c.idcontratorelacion, c.created_at,
                                                                (SELECT hc.fechaentrega FROM historialclinico hc WHERE hc.id_contrato = c.id ORDER BY hc.created_at DESC LIMIT 1) as fechaentrega, c.fechaatraso,
                                                                (SELECT g.created_at FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS fechagarantia,
                                                                (SELECT g.estadogarantia FROM garantias g WHERE g.id_contrato = c.id ORDER BY g.created_at DESC LIMIT 1) AS estadogarantia,
                                                                c.nombre_usuariocreacion, c.id_zona, (SELECT z.zona FROM zonas z WHERE z.id = c.id_zona) AS zona,
                                                                c.localidad, c.colonia, c.calle, c.numero, c.nombre, c.telefono, c.nombrereferencia, c.telefonoreferencia,
                                                                c.totalreal, c.totalproducto, c.totalpromocion, c.totalabono, c.total, c.ultimoabono,
                                                                (SELECT p.estado FROM promocioncontrato p WHERE p.id_contrato = c.id AND p.id_franquicia = c.id_franquicia) AS promo, c.alias
                                                                FROM contratos c WHERE c.id = '$idContrato'");

                        }

                        //Ya se recorrieron todos los contratos de la BD?
                        if($indiceFinal >= $ultimoIndiceBD){
                            //Recorrieron todos los contratos - Eliminar registro de tabla franquiciaregistrostemporales
                            DB::delete("DELETE FROM franquiciaregistrostemporales WHERE indice = '$indiceTarea'");

                            //Reinciar indice para busqueda de contratos por franquicia
                            DB::update("UPDATE franquicias SET indicecontratos = 0 WHERE id = '$idFranquicia'");

                        }else{
                            //Todavia no se recorren todos los contratos de la BD en busqueda de contratios pertenecientes a la franquicia

                            //Actualizar indice para la franquicia
                            DB::update("UPDATE franquicias SET indicecontratos = '$indiceFinal' WHERE id = '$idFranquicia'");

                            //Actualizar bandera de tarea para que vuelva a entrar y continue con el siguiente bloque de contratos
                            DB::update("UPDATE franquiciaregistrostemporales SET bandera = '0' WHERE indice = '$indiceTarea'");
                        }

                    } catch (\Exception $e) {
                        \Log::info("Error: Comando : contratoslistatemporales: " . $idFranquicia . "\n" . $e);
                        continue;
                    }
                }

            }

        }

    }
}
