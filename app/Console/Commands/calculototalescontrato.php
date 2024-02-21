<?php

namespace App\Console\Commands;

use App\Clases\contratosGlobal;
use App\Clases\globalesServicioWeb;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class calculototalescontrato extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actualizar:calculototalescontrato';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para sacar el totalproducto, totalabonos y total del contrato dependiendo si tiene o no promocion';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        \Log::info("COMANDO CALCULO TOTALES EJECUTADO");

        $contratos = DB::select("SELECT * FROM contratostemporalescalculototalescontrato");

        if($contratos != null) {
            //Hay contratos

            $globalesServicioWeb = new globalesServicioWeb;
            $contratosGlobal = new contratosGlobal;

            foreach ($contratos as $contrato) {

                try{

                    $idContrato = $contrato->id;

                    \Log::info("COMANDO CALCULO TOTALES ID_CONTRATO: " . $idContrato);

                    $idFranquicia = $contrato->id_franquicia;
                    $estadoContrato = $contrato->estatus_estadocontrato;
                    $totalContrato = $contrato->total;

                    if($estadoContrato == 5) {
                        //LIQUIDADO
                        if($totalContrato > 0) {
                            //TOTAL MAYOR A 0

                            //Actualizar estado del contrato a ENTREGADO
                            DB::table("contratos")->where("id", "=", $idContrato)->where("id_franquicia", "=", $idFranquicia)->update([
                                'estatus_estadocontrato' => 2,
                                'costoatraso' => 0
                            ]);

                            //Insertar en tabla registroestadocontrato
                            DB::table('registroestadocontrato')->insert([
                                'id_contrato' => $idContrato,
                                'estatuscontrato' => 2,
                                'created_at' => Carbon::now()
                            ]);

                        }else {
                            //TOTAL ES IGUAL A 0
                            $existeRegistroGarantias = DB::select("SELECT id FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia IN (0,1)");
                            if($existeRegistroGarantias == null) {
                                //No existen garantia REPORTADA, NI ASIGNADA
                                //Eliminar registros de la tabla contratostemporalessincronizacion que contengan ese idContrato
                                DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");
                                //Eliminar registros de la tabla abonoscontratostemporalessincronizacion que contengan ese idContrato
                                DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_contrato = '$idContrato'");
                            }
                        }

                    }else {
                        //NO TERMINADO, TERMINADO, ENTREGADO, ABONO ATRASADO, ENVIADO

                        $totalAbonoActualizar = 0;
                        $totalProductoActualizar = 0;
                        $totalabono = DB::select("SELECT SUM(abono) as totalabono FROM abonos WHERE id_contrato = '$idContrato'");
                        $totalproducto = DB::select("SELECT SUM(total) as totalproducto FROM contratoproducto WHERE id_contrato = '$idContrato'");
                        if($totalabono[0]->totalabono != null) {
                            $totalAbonoActualizar = $totalabono[0]->totalabono;
                        }
                        if($totalproducto[0]->totalproducto != null) {
                            $totalProductoActualizar = $totalproducto[0]->totalproducto;
                        }

                        //Actualizar TOTALABONO Y TOTALPRODUCTO del contrato
                        DB::table("contratos")->where("id", "=", $idContrato)->where("id_franquicia", "=", $idFranquicia)->update([
                            'totalabono' => $totalAbonoActualizar,
                            'totalproducto' => $totalProductoActualizar
                        ]);

                        //Sacar TOTAL del contrato
                        if($globalesServicioWeb::obtenerEstadoPromocion($idContrato, $idFranquicia)) {
                            //Tiene promocion y esta activa
                            $promocionterminada = $contrato->promocionterminada;
                            if($promocionterminada == 1) {
                                //Promocion ha sido terminada
                                DB::update("UPDATE contratos
                                    SET total = coalesce(totalpromocion,0)  + coalesce(totalproducto,0) - coalesce(totalabono,0)
                                    WHERE idcontratorelacion = '$idContrato' OR id = '$idContrato'");
                            }else {
                                //Promocion no ha sido terminada
                                DB::update("UPDATE contratos
                                    SET total = coalesce(totalhistorial,0) + coalesce(totalproducto,0) - coalesce(totalabono,0)
                                    WHERE idcontratorelacion = '$idContrato' OR id = '$idContrato'");
                            }

                        }else {
                            //No tiene promocion o existe la promocion pero esta desactivada
                            DB::update("UPDATE contratos
                                SET total = coalesce(totalhistorial,0) + coalesce(totalproducto,0) - coalesce(totalabono,0)
                                WHERE idcontratorelacion = '$idContrato' OR id ='$idContrato'");
                        }

                        //Obtener estatus_estadocontrato y total actualizados despues de hacer el procedimiento anterior
                        $contrato = DB::select("SELECT estatus_estadocontrato, total FROM contratos WHERE id = '$idContrato' AND id_franquicia = '$idFranquicia'");

                        if($contrato != null) {
                            //Se encontro el contrato
                            $estadoContratoActualizado = $contrato[0]->estatus_estadocontrato;
                            if($estadoContratoActualizado == 2 || $estadoContratoActualizado == 4 || $estadoContratoActualizado == 12) {
                                //Estado del contrato es ENTREGADO, ATRASADO o ENVIADO
                                $totalContratoActualizado = $contrato[0]->total;
                                if($totalContratoActualizado == 0) {
                                    //TOTAL es igual a 0

                                    $actualizar = false; //Bandera que se utiliza para cambiar el contrato a liquidado o no

                                    if($estadoContratoActualizado == 12) {
                                        //Estado del contrato es ENVIADO

                                        $existeRegistroGarantias = DB::select("SELECT id FROM garantias WHERE id_contrato = '$idContrato'");

                                        if($existeRegistroGarantias == null) {
                                            //No existe ningun registro en la tabla garantia

                                            $ultimoabono = DB::select("SELECT id_usuario FROM abonos WHERE id_contrato = '$idContrato'
                                                                                ORDER BY created_at DESC LIMIT 1");

                                            if ($ultimoabono != null) {
                                                //Existe por lo menos un abono
                                                $idUsuarioAbono = $ultimoabono[0]->id_usuario;
                                                $usuario = DB::select("SELECT rol_id FROM users WHERE id = '$idUsuarioAbono'");
                                                if ($usuario != null) {
                                                    //Existe usuario
                                                    if ($usuario[0]->rol_id == 4) {
                                                        //El ultimo abono fue insertado por un cobrador
                                                        $actualizar = true;

                                                        //Actualizar fecha entrega y entrega producto
                                                        DB::table("contratos")->where("id", "=", $idContrato)->where("id_franquicia", "=", $idFranquicia)->update([
                                                            'fechaentrega' => Carbon::now(),
                                                            'entregaproducto' => 1
                                                        ]);

                                                        //Guardar en historial de movimientos
                                                        DB::table('historialcontrato')->insert([
                                                            'id' => $globalesServicioWeb::generarIdAlfanumerico('historialcontrato', '5'),
                                                            'id_usuarioC' => 699, //idUsuario de sistemas automatico
                                                            'id_contrato' => $idContrato,
                                                            'created_at' => Carbon::now(),
                                                            'cambios' => " El contrato cambio a pagado por validación de abono"
                                                        ]);

                                                    }
                                                }
                                            }

                                        }

                                    }else {
                                        //Estado del contrato es ENTREGADO o ATRASADO

                                        $tieneGarantiaCreada = DB::select("SELECT estadogarantia FROM garantias WHERE id_contrato = '$idContrato' AND estadogarantia = '2'");

                                        if ($tieneGarantiaCreada == null) {
                                            //No tiene garantia creada
                                            $actualizar = true;
                                        }

                                    }

                                    if($actualizar) {
                                        //Actualizar estado del contrato a LIQUIDADO
                                        DB::table("contratos")->where("id", "=", $idContrato)->where("id_franquicia", "=", $idFranquicia)->update([
                                            'estatus_estadocontrato' => 5
                                        ]);

                                        //Insertar en tabla registroestadocontrato
                                        DB::table('registroestadocontrato')->insert([
                                            'id_contrato' => $idContrato,
                                            'estatuscontrato' => 5,
                                            'created_at' => Carbon::now()
                                        ]);

                                    }

                                }
                            }

                        }

                    }

                    //Actualizar contrato en tabla contratostemporalessincronizacion
                    $contratosGlobal::actualizarDatosPorContratoTablaContratosTemporalesSincronizacion($idContrato);

                    //Obtener estatus_estadocontrato y total actualizados despues de hacer el procedimiento anterior
                    $contratotemporalsincronizacion = DB::select("SELECT estatus_estadocontrato, total FROM contratos WHERE id = '$idContrato' AND id_franquicia = '$idFranquicia'");
                    if ($contratotemporalsincronizacion != null) {
                        //Existe el contrato en tabla contratostemporalessincronizacion
                        if ($contratotemporalsincronizacion[0]->estatus_estadocontrato == 5 && $contratotemporalsincronizacion[0]->total == 0) {
                            //LIQUIDADO Y TOTAL ES IGUAL A 0 (Eliminar registros de la tabla contratostemporalessincronizacion por que ya esta liquidado)
                            DB::delete("DELETE FROM contratostemporalessincronizacion WHERE id = '$idContrato'");
                            //Eliminar registros de la tabla abonoscontratostemporalessincronizacion que contengan ese idContrato
                            DB::delete("DELETE FROM abonoscontratostemporalessincronizacion WHERE id_contrato = '$idContrato'");
                        }
                    }

                    //Eliminar registro de la tabla contratostemporalescalculototalescontrato
                    DB::delete("DELETE FROM contratostemporalescalculototalescontrato WHERE id = '$idContrato'");

                    //Actualizar datos en tabla contratos lista temporales
                    $contratosGlobal::insertarActualizarDatosContratoListaTemporales($idContrato);

                }catch(\Exception $e){
                    \Log::info("Error: Comando : calculototalescontrato: " . $contrato->id . "\n" . $e);
                    continue;
                }

            }

        }

        \Log::info("COMANDO CALCULO TOTALES TERMINADO");

    }
}
