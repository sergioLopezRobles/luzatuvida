<?php

namespace App\Http\Controllers\Dominios\Administracion;

use App\Clases\contratosGlobal;
use App\Clases\polizaGlobales;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use App\Clases;
use Image;

class poliza extends Controller
{

    public function tablaPoliza($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 8)) //DIRECTOR Y ADMINISTRATIVO
        {

            $polizas = DB::table('poliza as p')
                ->select('p.id AS ID', 'p.id_franquicia AS FRANQUICIA', 'p.realizo AS REALIZO', 'p.autorizo AS AUTORIZO', 'p.total AS TOTAL', 'p.created_at AS CREATED_AT')
                ->whereRaw("p.id_franquicia  = '$idFranquicia'")
                ->orderBy('p.created_at', 'DESC')
                ->paginate(20);
            $franquiciaPoliza = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquicia'");
            $franquicias = DB::select("SELECT id, ciudad FROM franquicias WHERE id != '00000'");

            return view('administracion.poliza.tabla', ['polizas' => $polizas, 'idFranquicia' => $idFranquicia, 'franquiciaPoliza' => $franquiciaPoliza,
                'franquicias' => $franquicias
            ]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public static function verpoliza($idFranquicia, $idPoliza)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 8)) //ADMINISTRADORES
        {


            $poliza = DB::select("SELECT * FROM poliza WHERE id = '$idPoliza' AND id_franquicia = '$idFranquicia'");
            if($poliza != null) { //Existe la poliza en la sucursal actual?

                $fechaPoliza = Carbon::parse($poliza[0]->created_at)->format('Y-m-d');
                $opcion = 1;
                $nombrefranquicia = DB::select("SELECT f.ciudad FROM franquicias f WHERE f.id = '$idFranquicia'");
                $nombrefranquicia = $nombrefranquicia[0]->ciudad == null ? "SIN NOMBRE" : $nombrefranquicia[0]->ciudad;

                return view('administracion.poliza.nueva', [
                    "idFranquicia" => $idFranquicia,
                    "idPoliza" => $idPoliza,
                    "poliza" => $poliza,
                    "fecha" => $fechaPoliza,
                    "opcion"=> $opcion,
                    "nombrefranquicia"=> $nombrefranquicia
                ]);

            }
            return back()->with("alerta","No se encontro la poliza.");
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function crearcargarpolizatiemporeal(Request $request) {

        //Opcion
        //0- Creacion poliza
        //1- Ver poliza

        $idFranquicia = $request->input('idFranquicia');
        $opcion = $request->input('opcion');
        $idPoliza = $request->input('idPoliza');
        $view = null;

        if($opcion == 1) {
            //Ver poliza

            try {

                $polizaGlobales = new polizaGlobales(); //Creamos una nueva instancia de polizaGlobales

                $poliza = DB::select("SELECT created_at FROM poliza WHERE id = '$idPoliza' AND id_franquicia = '$idFranquicia'");
                $fechaPoliza = Carbon::parse($poliza[0]->created_at)->format('Y-m-d');

                $polizaGlobales::calcularTotales($idFranquicia, $idPoliza, $fechaPoliza);

                $poliza = DB::select("SELECT * FROM poliza WHERE id = '$idPoliza' AND id_franquicia = '$idFranquicia'");

                //CONSULTA DE COMISIONES PARA ASISTENTES Y OPTOMETRISTAS
                $comisionunototalcontratosasistente = $poliza[0]->totalcontratosasistentecomision1 == null ? 13 : $poliza[0]->totalcontratosasistentecomision1;
                $comisionunovalorasistente = $poliza[0]->valorasistentecomision1 == null ? 80 : $poliza[0]->valorasistentecomision1;
                $comisiondostotalcontratosasistente = $poliza[0]->totalcontratosasistentecomision2 == null ? 20 : $poliza[0]->totalcontratosasistentecomision2;
                $comisiondosvalorasistente = $poliza[0]->valorasistentecomision2 == null ? 120 : $poliza[0]->valorasistentecomision2;
                $comisionunototalcontratosoptometrista = $poliza[0]->totalcontratosoptometristacomision1 == null ? 30 : $poliza[0]->totalcontratosoptometristacomision1;
                $comisionunovaloroptometrista = $poliza[0]->valoroptometristacomision1 == null ? 4 : $poliza[0]->valoroptometristacomision1;
                $comisiondostotalcontratosoptometrista = $poliza[0]->totalcontratosoptometristacomision2 == null ? 40 : $poliza[0]->totalcontratosoptometristacomision2;
                $comisiondosvaloroptometrista = $poliza[0]->valoroptometristacomision2 == null ? 5 : $poliza[0]->valoroptometristacomision2;
                $comisiontrestotalcontratosoptometrista = $poliza[0]->totalcontratosoptometristacomision3 == null ? 10 : $poliza[0]->totalcontratosoptometristacomision3;
                $comisiontresvaloroptometrista = $poliza[0]->valoroptometristacomision3 == null ? 3 : $poliza[0]->valoroptometristacomision3;

                //Traemos la ultima poliza de la semana actual.
                $ultimaPoliza = DB::select("SELECT * FROM poliza WHERE id_franquicia = '$idFranquicia' AND STR_TO_DATE(created_at,'%Y-%m-%d') < STR_TO_DATE('$fechaPoliza','%Y-%m-%d')
                                                    ORDER BY created_at DESC LIMIT 1");//Traemos la ultima poliza sin importar si es de la semana actual o no.

                $sumaoficina = DB::select("SELECT coalesce(Sum(monto),0)  as suma from ingresosoficina WHERE id_poliza ='$idPoliza'");
                $gastosadmon = DB::select("SELECT * FROM gastos WHERE id_poliza = '$idPoliza' and tipogasto IN (0,7,8,15,16,17,18)");
                $sumaadmin = DB::select("SELECT coalesce(Sum(monto),0)  as suma from gastos WHERE id_poliza = '$idPoliza' and tipogasto IN (0,7,8,15,16,17,18)");
                $gastosventas = DB::select("SELECT * FROM gastos WHERE id_poliza = '$idPoliza' and tipogasto IN (1,4,9,10)");
                $sumaventas = DB::select("SELECT coalesce(Sum(monto),0)  as suma from gastos WHERE id_poliza = '$idPoliza' and tipogasto IN (1,4,9,10)");
                $gastoscobranza = DB::select("SELECT * FROM gastos WHERE id_poliza = '$idPoliza' and tipogasto IN (2,5,6,11,12,13,14)");
                $sumacobranza = DB::select("SELECT coalesce(Sum(monto),0)  as suma from gastos WHERE id_poliza ='$idPoliza' and tipogasto IN (2,5,6,11,12,13,14)");
                $otrosgastos = DB::select("SELECT * FROM gastos WHERE id_poliza = '$idPoliza' and tipogasto = 3");
                $sumaotros = DB::select("SELECT coalesce(Sum(monto),0)  as suma from gastos WHERE id_poliza = '$idPoliza' and tipogasto = 3");

                $arrayTemporalValidacion = array();
                $ventas = DB::select("SELECT pvd.id_usuario, pvd.id_poliza, pvd.nombre,pvd.lunes,pvd.martes,pvd.miercoles,pvd.jueves,pvd.viernes,pvd.sabado,pvd.acumuladas,pvd.ingresosgotas,pvd.ingresosenganche,
                                                   pvd.ingresospoliza,pvd.ingresosventas,pvd.ingresosventasacumulado,pvd.ingresosabonos,
                                                   (SELECT a.id_tipoasistencia FROM asistencia a WHERE a.id_usuario = pvd.id_usuario AND a.id_poliza = '$idPoliza') AS asistencia
                                                    FROM users u INNER JOIN polizaventasdias pvd ON u.id = pvd.id_usuario
                                                    WHERE pvd.id_poliza = '$idPoliza' AND pvd.rol = '12' ORDER BY u.name");

                $ventasAsistente = DB::select("SELECT pvd.nombre,pvd.lunes,pvd.martes,pvd.miercoles,pvd.jueves,pvd.viernes,pvd.sabado,pvd.acumuladas,pvd.ingresosgotas,pvd.ingresosenganche,
                                                    pvd.ingresospoliza,pvd.ingresosventas,pvd.ingresosventasacumulado,pvd.ingresosabonos,
                                                    (SELECT a.id_tipoasistencia FROM asistencia a WHERE a.id_usuario = pvd.id_usuario AND a.id_poliza = '$idPoliza') AS asistencia
                                                    FROM users u INNER JOIN polizaventasdias pvd ON u.id = pvd.id_usuario
                                                    WHERE pvd.id_poliza = '$idPoliza' AND pvd.rol = '13' ORDER BY u.name");

                $productividad = DB::select("SELECT * FROM polizaproductividad pvd INNER JOIN users u ON u.id = pvd.id_usuario WHERE pvd.id_poliza = '$idPoliza' AND pvd.rol = '12'
                                                    ORDER BY u.name");

                if($productividad != null) {
                    //Hay datos en productividad

                    foreach ($productividad as $product) {
                        //Recorrido de productividad por usuario

                        $arrayRespuesta = $polizaGlobales::obtenerNumeroContratosEntregadosAbonoAtrasadoLiquidadosConGarantiaPorPaqueteYContratosYSumaTotalRealContratosEntregadosPorUsuario
                        ($idFranquicia, $idPoliza, $poliza[0]->created_at, $product->id_usuario);
                        $arrayNumContratosPorPaquetes = $arrayRespuesta[0]; //Arreglo con el numero de contratos entregados por paquetes
                        $contratosEntregados = $arrayRespuesta[1]; //Todos los contratos entregados
                        $sumaTotalRealContratosEntregados = $arrayRespuesta[2]; //Suma de los totalReal de los contratos entregados

                        $product->totaleco = 0;
                        $product->totaljr = 0;
                        $product->totaldoradouno = 0;
                        $product->totaldoradodos = 0;
                        $product->totalplatino = 0;
                        $product->totalpremium = 0;

                        if($arrayNumContratosPorPaquetes != null) {
                            //Arreglo con el numero de contratos entregados por paquetes contiene informacion

                            if(array_key_exists("1", $arrayNumContratosPorPaquetes)) {
                                //Existe llave con el paquete 1
                                $product->totaleco += $arrayNumContratosPorPaquetes['1'];
                            }
                            if(array_key_exists("2", $arrayNumContratosPorPaquetes)) {
                                //Existe llave con el paquete 2
                                $product->totaleco += $arrayNumContratosPorPaquetes['2'];
                            }
                            if(array_key_exists("3", $arrayNumContratosPorPaquetes)) {
                                //Existe llave con el paquete 3
                                $product->totaleco += $arrayNumContratosPorPaquetes['3'];
                            }
                            if(array_key_exists("4", $arrayNumContratosPorPaquetes)) {
                                //Existe llave con el paquete 4
                                $product->totaljr = $arrayNumContratosPorPaquetes['4'];
                            }
                            if(array_key_exists("5", $arrayNumContratosPorPaquetes)) {
                                //Existe llave con el paquete 5
                                $product->totaldoradouno = $arrayNumContratosPorPaquetes['5'];
                            }
                            if(array_key_exists("6", $arrayNumContratosPorPaquetes)) {
                                //Existe llave con el paquete 6
                                $product->totaldoradodos = $arrayNumContratosPorPaquetes['6'];
                            }
                            if(array_key_exists("7", $arrayNumContratosPorPaquetes)) {
                                //Existe llave con el paquete 7
                                $product->totalplatino = $arrayNumContratosPorPaquetes['7'];
                            }
                            if(array_key_exists("8", $arrayNumContratosPorPaquetes)) {
                                //Existe llave con el paquete 8
                                $product->totalpremium = $arrayNumContratosPorPaquetes['8'];
                            }

                        }

                        $product->numeroventas = count($contratosEntregados); //numeroventas = Todos los contratos entregados
                        $product->contratosporentregar = $polizaGlobales::obtenerContratosPorEntregarOMontoTotalRealPoliza($idFranquicia, $idPoliza, $poliza[0]->created_at,
                            $product->id_usuario, 0);
                        $product->montototalreal = $polizaGlobales::obtenerContratosPorEntregarOMontoTotalRealPoliza($idFranquicia, $idPoliza, $poliza[0]->created_at,
                            $product->id_usuario, 1);
                        $product->montoentregadostotalreal = $sumaTotalRealContratosEntregados;

                        $arrayRespuestaObjetivoVentas = $polizaGlobales::obtenerDineroObjetivoEnVentas($contratosEntregados, $product->numeroventas, $product->totaleco,
                            $product->totaljr,$product->totaldoradouno + $product->totaldoradodos, $product->totalplatino, $product->totalpremium,
                            $comisionunototalcontratosoptometrista, $comisionunovaloroptometrista, $comisiondostotalcontratosoptometrista, $comisiondosvaloroptometrista,
                            $comisiontrestotalcontratosoptometrista, $comisiontresvaloroptometrista);
                        $product->dineroobjetivoventastreinta = $arrayRespuestaObjetivoVentas[0];
                        $product->dineroobjetivoventascuarenta = $arrayRespuestaObjetivoVentas[1];
                        $product->dineroobjetivoventaspremium = $arrayRespuestaObjetivoVentas[2];
                    }

                }

                $productividadAsistente = DB::select("SELECT * FROM polizaproductividad pvd INNER JOIN users u ON u.id = pvd.id_usuario WHERE pvd.id_poliza = '$idPoliza' AND pvd.rol = '13'
                                                                ORDER BY u.name");

                if ($productividadAsistente != null) {
                    //Hay datos en productividadAsistente

                    foreach ($productividadAsistente as $productAsist) {
                        //Recorrido de productividadAsistente por usuario
                        $productAsist->numObjetivoSemanaAnterior = $polizaGlobales::obtenerNumeroObjetivoSemanaAnteriorAsistente($idFranquicia, $poliza[0]->created_at, $productAsist->id_usuario);
                        $arrayResultados = $polizaGlobales::obtenerNoVentasAprobadasVentasAcumuladasYVentasAcumuladasAprobadasAsistente($idFranquicia, $idPoliza, $poliza[0]->created_at,
                            $productAsist->id_usuario);
                        $productAsist->sumaContratosNumVentas = $arrayResultados[0];
                        $productAsist->sumaContratosAprobadas = $arrayResultados[1];
                        $productAsist->sumaContratosVentasAcumuladas = $arrayResultados[2];
                        $productAsist->sumaContratosVentasAcumuladasAprobadas = $arrayResultados[3];
                    }

                }

                $cobranzatabla = DB::select("SELECT * FROM polizacobranza pvd WHERE pvd.id_poliza = '$idPoliza' AND pvd.nombre IS NOT NULL ORDER BY pvd.zona");

                foreach ($cobranzatabla as $cobrador) {
                    $cobrador->gas = $polizaGlobales::actualizarTotalGasolinaPorUsuario($idFranquicia, $idPoliza, $cobrador->id_usuario);
                }

                $totaldia = DB::select("SELECT SUM(ingresosventas) as ingreso FROM polizaventasdias WHERE id_poliza = '$idPoliza'");
                $totalingresocobranza = DB::select("SELECT SUM(ingresocobranza) as ingreso FROM polizacobranza WHERE id_poliza = '$idPoliza'");
                $ingresos = DB::select("SELECT * FROM ingresosoficina WHERE id_poliza = '$idPoliza'");
                $productividadgasto = DB::select("SELECT * FROM gastos WHERE id_poliza = '$idPoliza' AND tipogasto = 4");
                $usuarioscobranza = DB::select("SELECT u.id,u.name FROM users u INNER JOIN usuariosfranquicia uf ON u.id = uf.id_usuario WHERE rol_id = '4'
                                                        AND uf.id_franquicia = '$idFranquicia' AND u.supervisorcobranza = '0' ORDER BY u.name");
                $historial = DB::select("SELECT u.name,hc.cambios,hc.created_at FROM historialpoliza hc INNER JOIN users u ON u.id = hc.id_usuarioC
                                       WHERE id_poliza = '$idPoliza' ORDER BY hc.created_at DESC");

                $totalUltimaPoliza = $ultimaPoliza == null ? 0 : $ultimaPoliza[0]->total;
                $ingresosAdmin = $poliza[0]->ingresosadmin == null ? 0 : $poliza[0]->ingresosadmin;
                $ingresosVentas = $poliza[0]->ingresosventas == null ? 0 : $poliza[0]->ingresosventas;
                $ingresosCobranza = $poliza[0]->ingresoscobranza == null ? 0 : $poliza[0]->ingresoscobranza;

                $gastosAdmin = $poliza[0]->gastosadmin == null ? 0 : $poliza[0]->gastosadmin;
                $gastosCobranza = $poliza[0]->gastoscobranza == null ? 0 : $poliza[0]->gastoscobranza;
                $otrosGastos = $poliza[0]->otrosgastos == null ? 0 : $poliza[0]->otrosgastos;
                $gastoVentas = $poliza[0]->gastosventas == null ? 0 : $poliza[0]->gastosventas;

                $total = (float)$totalUltimaPoliza + (float)$ingresosVentas + (float)$ingresosCobranza + (float)$ingresosAdmin - (float)$gastoVentas - (float)$gastosAdmin -
                    (float)$gastosCobranza - (float)$otrosGastos;

                //Consulta de suma de insumos
                $sumainsumos = DB::select("SELECT (preciom+precioa+preciob+preciot+precioe) as suma FROM insumos");

                //Obtener abono minimo semanal de la sucursal
                $contratosGlobal = new contratosGlobal;
                $abonoMinimoSemanal = $contratosGlobal::calculoCantidadFormaDePago($idFranquicia, 1);

                $productos = DB::select("SELECT * FROM producto WHERE (id_tipoproducto = 1 OR (id_tipoproducto != 1 AND id_franquicia = '$idFranquicia')) AND estado = 1 ORDER BY id_tipoproducto, nombre ASC");

                $gastosgeneralpoliza = DB::select("SELECT id FROM gastos WHERE id_poliza = '$idPoliza'");

                $view = view('administracion.poliza.listas.contenidopoliza',
                    array("idFranquicia" => $idFranquicia,
                        "idPoliza" => $idPoliza,
                        "ultimaPoliza" => $ultimaPoliza,
                        "poliza" => $poliza,
                        "sumaoficina" => $sumaoficina,
                        "gastosadmon" => $gastosadmon,
                        "sumaadmin" => $sumaadmin,
                        "gastosventas" => $gastosventas,
                        "sumaventas" => $sumaventas,
                        "gastoscobranza" => $gastoscobranza,
                        "sumacobranza" => $sumacobranza,
                        "otrosgastos" => $otrosgastos,
                        "sumaotros" => $sumaotros,
                        "ventas" => $ventas,
                        "ventasAsistente" => $ventasAsistente,
                        "productividad" => $productividad,
                        "productividadAsistente" => $productividadAsistente,
                        "cobranzatabla" => $cobranzatabla,
                        "fecha" => $fechaPoliza,
                        "totaldia" => $totaldia,
                        "totalingresocobranza" => $totalingresocobranza,
                        "ingresos" => $ingresos,
                        'productividadgasto' => $productividadgasto,
                        'usuarioscobranza' => $usuarioscobranza,
                        'historial' => $historial,
                        'totalUltimaPoliza' => $totalUltimaPoliza,
                        'ingresosVentas' => $ingresosVentas,
                        'ingresosCobranza' => $ingresosCobranza,
                        'ingresosAdmin' => $ingresosAdmin,
                        'gastosAdmin' => $gastosAdmin,
                        'gastoVentas' => $gastoVentas,
                        'gastosCobranza' => $gastosCobranza,
                        'otrosGastos' => $otrosGastos,
                        'total' => $total,
                        'comisionunototalcontratosasistente' => $comisionunototalcontratosasistente,
                        'comisionunovalorasistente' => $comisionunovalorasistente,
                        'comisiondostotalcontratosasistente' => $comisiondostotalcontratosasistente,
                        'comisiondosvalorasistente' => $comisiondosvalorasistente,
                        'comisionunototalcontratosoptometrista' => $comisionunototalcontratosoptometrista,
                        'comisiondostotalcontratosoptometrista' => $comisiondostotalcontratosoptometrista,
                        'comisiontrestotalcontratosoptometrista' => $comisiontrestotalcontratosoptometrista,
                        'sumainsumos' => $sumainsumos,
                        'abonoMinimoSemanal' => $abonoMinimoSemanal,
                        'productos' => $productos,
                        'gastosgeneralpoliza' => $gastosgeneralpoliza
                    ))->render();

            } catch (\Exception $e) {
                \Log::info("Error: " . $e);
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }

        }

        return response()->json(array("valid" => "true","view" => $view));

    }

    public function agregarObservacion($idFranquicia, $idPoliza, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 6) || ((Auth::user()->rol_id) == 8)) {
            try {
                $usuarioId = Auth::user()->id;
                $actualizar = carbon::now();
                $obs = request('observaciones');
                $poli = DB::select("SELECT * FROM poliza WHERE id = $idPoliza ");
                $observaciones = $poli[0]->observaciones;

                if ($observaciones == null && $obs == null) {
                    return redirect()->route('verpoliza', ['idFranquicia' => $idFranquicia, 'idPoliza' => $idPoliza])->with('alerta', 'Favor de llenar el campo de observaciones');
                }
                if ($observaciones == null) {
                    DB::table('poliza')->where([['id', '=', $idPoliza], ['id_franquicia', '=', $idFranquicia]])->update([
                        'observaciones' => $obs
                    ]);
                    DB::table('historialpoliza')->insert([
                        'id_usuarioC' => $usuarioId, 'id_poliza' => $idPoliza, 'created_at' => $actualizar,
                        'cambios' => "Agregó la siguiente observacion: '$obs'"
                    ]);
                } else {
                    DB::table('poliza')->where([['id', '=', $idPoliza], ['id_franquicia', '=', $idFranquicia]])->update([
                        'observaciones' => null
                    ]);
                    DB::table('historialpoliza')->insert([
                        'id_usuarioC' => $usuarioId, 'id_poliza' => $idPoliza, 'created_at' => $actualizar,
                        'cambios' => "Eliminó la siguiente observacion: '$obs'"
                    ]);
                }
                return redirect()->route('verpoliza', ['idFranquicia' => $idFranquicia, 'idPoliza' => $idPoliza])
                                ->with('bien', 'la observacion se actualizo correctamente de la poliza')->with('pestaña', 'general');
            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function terminarPoliza($idFranquicia, $idPoliza, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) { //Director,Principal,Administrador
            try {
                $existeLaPoliza = DB::select("SELECT id,estatus FROM poliza WHERE id_franquicia = '$idFranquicia' AND id = '$idPoliza'");
                \Log::info("POLIZA2:".$idPoliza);
                if($existeLaPoliza != null){
                    $polizaGlobales = new polizaGlobales();
                    try {
                        switch($existeLaPoliza[0]->estatus){
                            case 0://Valor por default cuando se crea la poliza
                                $polizaGlobales::entregarPoliza($idFranquicia,$idPoliza);
                                return back()->with('bien', 'El estatus de la poliza se actualizo correctamente.')->with('pestaña', 'general');
                            case 2://En este estatus el administrador ya no la puede editar y lo hace para avisarle al director que ya esta lista la poliza.
                                $entregarORegresar = $request->entregar; // 1 Poliza entregada , 2 Regresar la poliza a 0 para que puedan seguir agregando gastos,entradas,etc.
                                if($entregarORegresar != null ){
                                    if($entregarORegresar == 2){
                                        $polizaGlobales::entregarORegresarPoliza($idPoliza,$entregarORegresar);
                                        return back()->with('bien', 'El estatus de la poliza se actualizo correctamente.')->with('pestaña', 'general');
                                    }
                                    if( $entregarORegresar == 1 && (Auth::user()->rol_id) == 7){
                                        $polizaGlobales::entregarORegresarPoliza($idPoliza,$entregarORegresar);
                                        return back()->with('bien', 'El estatus de la poliza se actualizo correctamente.')->with('pestaña', 'general');
                                    }else{
                                        return back()->with('alerta', 'Solo un superior puede realizar esta accion.')->with('pestaña', 'general');
                                    }
                                }
                            break;
                        }
                        return back()->with('alerta', 'Accion no valida.')->with('pestaña', 'general');
                    }catch(\Exception $e){
                        \Log::info("Error:".$e);
                        return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
                    }
                }
                return back()->with('error', 'No se encontro la poliza solicitada.');
            } catch (\Exception $e) {
                \Log::info("Error: " . $e);
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function ingresarOficina($idFranquicia, $idPoliza, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {

            $validacion = Validator::make($request->all(), [
                'descripcion' => 'required|string|max:255',
                'recibo' => 'required|string|max:255',
                'monto' => 'required|integer',
                'fotorecibo' => 'required|image|mimes:jpg'
            ]);

            if ($validacion->fails()) {
                return back()->with('alerta', 'Uno o más campos vacíos / Agregar archivo .jpg en recibo.');
            }

            if (request('recibo') < 0) {
                return back()->with('alerta', 'El recibo no puede ser menor a 0.');
            }

            if (request('monto') < 0) {
                return back()->with('alerta', 'El monto no puede ser menor a 0.');
            }

            //Validar tamaño de recibo
            $contratosGlobal = new contratosGlobal;
            if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('fotorecibo'))){
                return back()->with('alerta',"Verifica el archivo 'Foto recibo', el tamaño maximo permitido es 1MB.");
            }

            try {

                $fotorecibo = "";
                if (request()->hasFile('fotorecibo')) {
                    $fotoBruta = 'Foto-Recibo-Poliza-' . $idPoliza . '-' . time() . '.' . request()->file('fotorecibo')->getClientOriginalExtension();
                    $fotorecibo = request()->file('fotorecibo')->storeAs('uploads/imagenes/polizas/fotorecibo', $fotoBruta, 'disco');
                }

                $idIngresoOficina = DB::table('ingresosoficina')->insertGetId([
                    'id_poliza' => $idPoliza, 'descripcion' => request('descripcion'), 'id_usuario' => Auth::user()->id,
                    'numrecibo' => request('recibo'), 'monto' => request('monto'), 'foto' => $fotorecibo, 'created_at' => Carbon::now()
                ]);

                //Reducir imagen
                $this->reducirCalidadImagenesPolizaIngresoOficinaYGastos($idPoliza, $idIngresoOficina, false);

                DB::table('historialpoliza')->insert([
                    'id_usuarioC' => Auth::user()->id, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                    'cambios' => "Agregó un ingreso de oficina con un monto de $" . request('monto') . ", numero de recibo: '" . request('recibo') . "',
                                    con la siguiente descripción: '" . request('descripcion') . "'"
                ]);

                return back()->with('bien', 'El ingreso de oficina se agrego correctamente a la poliza.')->with('pestaña', 'oficina');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function registrarAsistencia($idFranquicia, $idPoliza, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {
            $rules = [
                'optometrista' => 'required',
                'asistencia' => 'required|integer',
            ];
            if (request('optometrista') == "nada") {
                return back()->withErrors(['optometrista' => 'Favor de seleccionar una opcion.'])->with('pestaña', 'ventas');
            }

            if (request('asistencia') == "nada") {
                return back()->withErrors(['asistencia' => 'Selecciona el tipo de asistencia.'])->with('pestaña', 'ventas');
            }
            request()->validate($rules);
            try {
                $poliza = DB::select("SELECT * from poliza WHERE id = '$idPoliza' AND id_franquicia = '$idFranquicia'");
                $optos = request('optometrista');
                $asistencia = request('asistencia');
                $username2 = DB::select("SELECT * from users WHERE id = '$optos'");
                if ($optos != 0) {
                    $username = $username2[0]->name;
                }
                $now = carbon::now();
                $usuarioId = Auth::user()->id;
                $nowparce = $poliza[0]->created_at;
                if ($optos == '0') {
                    DB::update("UPDATE asistencia
                SET id_tipoasistencia = $asistencia
                WHERE id_poliza = '$idPoliza' AND created_at ='$nowparce'");

                    DB::table('historialpoliza')->insert([
                        'id_usuarioC' => $usuarioId, 'id_poliza' => $idPoliza, 'created_at' => $now,
                        'cambios' => "Agregó asistencia para todos"
                    ]);
                    return redirect()->route('verpoliza', ['idFranquicia' => $idFranquicia, 'idPoliza' => $idPoliza])
                                     ->with('bien', 'Se agrego la asistencia correctamente a la poliza')->with('pestaña', 'ventas');
                }
                DB::update("UPDATE asistencia
                SET id_tipoasistencia = $asistencia
                WHERE id_poliza = '$idPoliza' AND created_at ='$nowparce'
                AND id_usuario = $optos");
                if ($asistencia == 1) {
                    $asisten = 'ASISTENCIA';
                } elseif ($asistencia == 2) {
                    $asisten = 'RETARDO';
                } else {
                    $asisten = 'FALTA';
                }
                DB::table('historialpoliza')->insert([
                    'id_usuarioC' => $usuarioId, 'id_poliza' => $idPoliza, 'created_at' => $now,
                    'cambios' => "Se registro '$asisten' para el usuario '$username'"
                ]);
                return redirect()->route('verpoliza', ['idFranquicia' => $idFranquicia, 'idPoliza' => $idPoliza])
                                 ->with('bien', 'Se agrego la asistencia correctamente a la poliza')->with('pestaña', 'ventas');
            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function ingresarGasto($idFranquicia, $idPoliza, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {

            $validacion = Validator::make($request->all(), [
                'descripcion2' => 'required|string|max:255',
                'factura' => 'required|string|max:255',
                'monto2' => 'required|integer',
                'tipogasto' => 'required|integer',
                'fotofactura' => 'required|image|mimes:jpg'
            ]);

            if ($validacion->fails()) {
                return back()->with('alerta', 'Uno o más campos vacíos / Agregar archivo .jpg en factura.');
            }

            if (request('factura') < 0) {
                return back()->withErrors(['factura' => 'El numero de factura no puede ser menor a 0.'])->with('pestaña', 'gastos');
            }
            if (request('monto2') < 0) {
                return back()->withErrors(['monto2' => 'El monto no puede ser menor a 0.'])->with('pestaña', 'gastos');
            }

            //Validar tamaño de foto factura
            $contratosGlobal = new contratosGlobal;
            if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('fotofactura'))){
                return back()->with('alerta',"Verifica el archivo 'Foto factura', el tamaño maximo permitido es 1MB.");
            }

            try {

                $observaciones = request('observaciones');
                if (strlen($observaciones) <= 0) {
                    $observaciones = "ninguna";
                }

                $fotofactura = "";
                if (request()->hasFile('fotofactura')) {
                    $fotoBruta = 'Foto-factura-Poliza-' . $idPoliza . '-' . time() . '.' . request()->file('fotofactura')->getClientOriginalExtension();
                    $fotofactura = request()->file('fotofactura')->storeAs('uploads/imagenes/polizas/fotofactura', $fotoBruta, 'disco');
                }

                $idGasto = DB::table('gastos')->insertGetId([
                    'id_poliza' => $idPoliza, 'descripcion' => request('descripcion2'),
                    'factura' => request('factura'), 'observaciones' => $observaciones, 'monto' => request('monto2'), 'foto' => $fotofactura,
                    'tipogasto' => request('tipogasto'), 'created_at' => Carbon::now()
                ]);

                //Reducir imagen
                $this->reducirCalidadImagenesPolizaIngresoOficinaYGastos($idPoliza, $idGasto, true);

                $tipogasto = null;
                switch (request('tipogasto')){
                    case 0:
                        $tipogasto = "administración";
                        break;
                    case 1:
                        $tipogasto = "ventas";
                        break;
                    case 2:
                        $tipogasto = "cobranza";
                        break;
                    case 3:
                        $tipogasto = "otros";
                        break;
                }

                DB::table('historialpoliza')->insert([
                    'id_usuarioC' => Auth::user()->id, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                    'cambios' => "Agregó un gasto de $tipogasto con un monto de $" . request('monto2') . ", con la descripción: '" . request('descripcion2') .
                                    "', numero de factura: '" . request('factura') . "',
                                    con las siguientes observaciones: '" . $observaciones . "'"
                ]);

                return redirect()->route('verpoliza', ['idFranquicia' => $idFranquicia, 'idPoliza' => $idPoliza])
                                 ->with('bien', "El gasto de $tipogasto se agrego correctamente a la poliza")->with('pestaña', 'gastos');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al dministrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    private function reducirCalidadImagenesPolizaIngresoOficinaYGastos($idPoliza, $idIngresoOficinaOGasto, $gasto)
    {

        try {

            if ($gasto) {
                //idGasto
                $query = "SELECT foto FROM gastos WHERE id_poliza = '$idPoliza' AND id = '$idIngresoOficinaOGasto'";
            }else {
                //idIngresoOficina
                $query = "SELECT foto FROM ingresosoficina WHERE id_poliza = '$idPoliza' AND id = '$idIngresoOficinaOGasto'";
            }

            $registro = DB::select($query);

            if ($registro != null) {
                //Existe registro

                $alto = Image::make(config('filesystems.disks.disco.root') . '/' . $registro[0]->foto)->height();
                $ancho = Image::make(config('filesystems.disks.disco.root') . '/' . $registro[0]->foto)->width();
                if ($alto > $ancho) {
                    $imagen = Image::make(config('filesystems.disks.disco.root') . '/' . $registro[0]->foto)->resize(600, 800);
                } else {
                    $imagen = Image::make(config('filesystems.disks.disco.root') . '/' . $registro[0]->foto)->resize(800, 600);
                }
                $imagen->save();

            }

        }catch(\Exception $e){
            \Log::info("Error:".$e);
            return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
        }

    }

    public function eliminarGasto($idFranquicia, $idPoliza, $idGasto, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {

            try {

                $gasto = DB::select("SELECT monto, factura, observaciones, pertenencia, foto, id_usuario, tipogasto, descripcion, id_tipocobranza
                                            FROM gastos WHERE id_poliza = $idPoliza AND id = '$idGasto'");

                if ($gasto != null) {
                    //Existe el gasto

                    $monto2 = $gasto[0]->monto;
                    $pertenencia = $gasto[0]->pertenencia;
                    $factura = $gasto[0]->factura;
                    $observaciones = $gasto[0]->observaciones;
                    $usuario = $gasto[0]->id_usuario;
                    $tipogasto = $gasto[0]->tipogasto;
                    $descripcion = $gasto[0]->descripcion;
                    $id_tipocobranza = $gasto[0]->id_tipocobranza;

                    $mensajecambionombrecobranza = "";
                    if ($usuario != null && $tipogasto == 2) {
                        //Existe cobrador en el gasto

                        $usuarioconsulta = DB::select("SELECT u.name from users u WHERE u.id = '$usuario'");
                        if ($usuarioconsulta != null) {
                            //Existe el usuario
                            $mensajecambionombrecobranza = " al cobrador " . $usuarioconsulta[0]->name;
                        }else {
                            //No existe el usuario
                            return back()->with('alerta', 'El usuario no existe.');
                        }

                    }

                    if ($gasto[0]->foto != null) {
                        //Existe foto de factura en el gasto
                        Storage::disk('disco')->delete($gasto[0]->foto);
                    }

                    DB::delete("DELETE FROM gastos WHERE id = '$idGasto' AND id_poliza = '$idPoliza'");

                    if ($pertenencia == 2) {
                        DB::delete("DELETE FROM tipocobranza WHERE id = '$id_tipocobranza'");
                    }

                    $mensajecambiofacturaobservaciones = "";
                    if(strlen($factura) > 0 && strlen($observaciones) > 0) {
                        $mensajecambiofacturaobservaciones = ", numero de factura: '$factura',
                                    con las siguientes observaciones: '$observaciones'";
                    }

                    switch ($tipogasto){
                        case 0:
                        case 7:
                        case 8:
                        case 17:
                        case 18:
                            $tipogasto = "administración";
                            break;
                        case 15:
                        case 16:
                            $tipogasto = "supervisión cobranza";
                            break;
                        case 1:
                        case 4:
                        case 9:
                        case 10:
                            $tipogasto = "ventas";
                            break;
                        case 2:
                        case 5: //Tarjeta
                        case 6: //Transferencia
                        case 11: //Armazon
                        case 12: //Poliza de seguro
                        case 13: //Gotas
                        case 14: //Vitaminas
                            $tipogasto = "cobranza";
                            break;
                        case 3:
                            $tipogasto = "otros";
                            break;
                    }

                    DB::table('historialpoliza')->insert([
                        'id_usuarioC' => Auth::user()->id, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                        'cambios' => "Eliminó un gasto de $tipogasto con un monto de $" . $monto2 . ", por concepto de: '" . $descripcion . "'"
                            . $mensajecambiofacturaobservaciones
                            . $mensajecambionombrecobranza
                    ]);

                    return redirect()->route('verpoliza', ['idFranquicia' => $idFranquicia, 'idPoliza' => $idPoliza])
                        ->with('bien', "El gasto de $tipogasto se elimino correctamente de la poliza")->with('pestaña', 'gastos');

                }
                //No existe el gasto
                return back()->with('alerta', 'El gasto que deseas eliminar ya no existe.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function eliminarOficina($idFranquicia, $idPoliza, $idOficina, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {

            try {

                $usuarioId = Auth::user()->id;
                $actualizar = carbon::now();
                $existenteOficina = DB::select("SELECT * FROM ingresosoficina WHERE id_poliza = $idPoliza AND id = '$idOficina'");
                if ($existenteOficina != null) {
                    //Existe ingreso oficina

                    if ($existenteOficina[0]->tipo == 0) {
                        //Tipo ingreso de oficina es diferente a cobranza

                        $monto2 = $existenteOficina[0]->monto;
                        $numrecibo = $existenteOficina[0]->numrecibo;
                        $descripcion = $existenteOficina[0]->descripcion;
                        if ($existenteOficina[0]->foto != null) {
                            //Tiene foto el ingreso de oficina
                            Storage::disk('disco')->delete($existenteOficina[0]->foto); //Eliminar foto
                        }

                        $mensaje = "con un monto de $$monto2, numero de recibo: '$numrecibo'";
                        if ($existenteOficina[0]->id_producto != null) {
                            //El ingreso de oficina fue de producto
                            $producto = DB::select("SELECT * FROM producto WHERE id = '" . $existenteOficina[0]->id_producto . "'");

                            $nombreproducto = "";
                            if ($producto != null) {
                                //Existe producto
                                //Aumentar piezas del producto
                                $nombreproducto = "armazón " . $producto[0]->nombre . " | " . $producto[0]->color; //Por default armazon
                                switch ($producto[0]->id_tipoproducto) {
                                    case 3: //Gotas
                                        $nombreproducto = "gotas";
                                        break;
                                    case 4: //Vitaminas
                                        $nombreproducto = "vitaminas";
                                        break;
                                }

                                $mensaje = "de producto $nombreproducto con un monto de $$monto2";

                                //Aumentar piezas del producto
                                DB::table('producto')->where('id', '=', $existenteOficina[0]->id_producto)->update([
                                    'piezas' => $producto[0]->piezas + $existenteOficina[0]->piezas
                                ]);

                                if ($existenteOficina[0]->id_gasto != null) {
                                    //Entro un gasto en la poliza
                                    $gasto = DB::select("SELECT id, monto FROM gastos WHERE id_poliza = '$idPoliza' AND id = '" . $existenteOficina[0]->id_gasto . "'");
                                    if ($gasto != null) {
                                        //Existe gasto
                                        DB::delete("DELETE FROM gastos WHERE id = '" . $existenteOficina[0]->id_gasto . "' AND id_poliza = '$idPoliza'"); //Eliminar gasto
                                        DB::table('historialpoliza')->insert([
                                            'id_usuarioC' => $usuarioId, 'id_poliza' => $idPoliza, 'created_at' => $actualizar,
                                            'cambios' => "Eliminó un gasto de oficina de producto con un monto de $" . $gasto[0]->monto . ", por concepto de: '$descripcion'"
                                        ]);
                                    }
                                }
                            }
                        }

                        DB::delete("DELETE FROM ingresosoficina WHERE id = '$idOficina' AND id_poliza = '$idPoliza'");

                        DB::table('historialpoliza')->insert([
                            'id_usuarioC' => $usuarioId, 'id_poliza' => $idPoliza, 'created_at' => $actualizar,
                            'cambios' => "Eliminó un ingreso de oficina $mensaje, por concepto de: '$descripcion'"
                        ]);

                        return redirect()->route('verpoliza', ['idFranquicia' => $idFranquicia, 'idPoliza' => $idPoliza])
                            ->with('bien', 'El ingreso de oficina se elimino correctamente de la poliza')->with('pestaña', 'oficina');

                    }
                    //Tipo ingreso de oficina es de cobranza
                    return redirect()->route('verpoliza', ['idFranquicia' => $idFranquicia, 'idPoliza' => $idPoliza])
                        ->with('alerta', 'El ingreso de oficina no se puede eliminar ya que es un ingreso por parte de cobranza')->with('pestaña', 'oficina');

                }
                //No existe ingreso oficina
                return redirect()->route('verpoliza', ['idFranquicia' => $idFranquicia, 'idPoliza' => $idPoliza])
                    ->with('alerta', 'El ingreso de oficina que deseas eliminar ya no existe')->with('pestaña', 'oficina');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function tablaAsistencia($idSucursal, $idPoliza)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 8)) //ADMINISTRADORES
        {
            $franquiciaPoliza = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idSucursal'");
            $listaAsistencia = DB::select("SELECT u.id,u.name,a.id_tipoasistencia,a.created_at,a.updated_at, a.registroentrada, a.registrosalida,
                                                 (SELECT r.rol FROM roles r WHERE r.id = u.rol_id) as rol, u.supervisorcobranza as supervisorcobranza
                                                 FROM asistencia a INNER JOIN users u ON a.id_usuario = u.id WHERE id_poliza = '$idPoliza' order by u.name");
            $usuarios = DB::select("SELECT u.id,u.name, (SELECT r.rol FROM roles r WHERE r.id = u.rol_id) as rol FROM users u INNER JOIN usuariosfranquicia uf ON uf.id_usuario = u.id WHERE uf.id_franquicia = '$idSucursal'
                                            ORDER BY u.name");
            return view('administracion.poliza.tablaasistencia', ['idFranquicia' => $idSucursal, "idPoliza" => $idPoliza, "listaAsistencia" => $listaAsistencia,
                             'franquiciaPoliza' => $franquiciaPoliza, "usuarios" => $usuarios]);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function registrarAsistenciaTabla($idFranquicia, $idPoliza)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 8)) //ADMINISTRADORES
        {
            $rules = [
                'usuario' => 'required|integer',
                'asistencia' => 'required|integer',
                'asistenciaTipo' => 'required|integer'
            ];
            request()->validate($rules);
            $usuario = request("usuario");
            $asistencia = request("asistencia");
            $asistenciaTipo = request("asistenciaTipo");
            $cadenaTipoAsistencia = "";

            if ($usuario == 0) {
                DB::table("asistencia")->where('id_poliza', '=', $idPoliza)->update([
                    "id_tipoasistencia" => $asistencia,
                    "updated_at" => Carbon::now()
                ]);
                return back()->with("bien", "La asistencia se modifico correctamente.");
            } else {
                $existeUsuario = DB::select("SELECT id, id_tipoasistencia, registroentrada FROM asistencia WHERE id_usuario = '$usuario' AND id_poliza = '$idPoliza'");
                if (!is_null($existeUsuario)) {
                    if ($asistencia == 0 || $asistencia == 1 || $asistencia == 2) {
                        //Validar si es tipo asistencia entrada o salida
                        if($asistenciaTipo == 0 || $asistenciaTipo == 1){

                            if($asistenciaTipo == 0 || ($asistenciaTipo == 1 && ($existeUsuario[0]->registroentrada != null && $existeUsuario[0]->id_tipoasistencia != 0))){
                                //Es registro de asistencia de entrada o
                                // Registro de salida y tiene registrada asistencia de entrada o es diferente de falta

                                switch ($asistenciaTipo){
                                    case 0:
                                        //Asistencia de entrada
                                        DB::table("asistencia")->where("id_usuario", "=", $usuario)->where('id_poliza', '=', $idPoliza)->update([
                                            "registroentrada" => Carbon::now(), "id_tipoasistencia" => $asistencia, "updated_at" => Carbon::now()
                                        ]);
                                        $cadenaTipoAsistencia = "tomó asistencia a";
                                        break;

                                    case 1:
                                        //Asistencia de salida
                                        DB::table("asistencia")->where("id_usuario", "=", $usuario)->where('id_poliza', '=', $idPoliza)->update([
                                            "registrosalida" => Carbon::now(), "updated_at" => Carbon::now()
                                        ]);
                                        $cadenaTipoAsistencia = "registró hora de salida a";
                                        break;
                                }

                                $usuarioId = Auth::user()->id;
                                $existeUsuarioLogeado = DB::select("SELECT name FROM users WHERE id = '$usuarioId'");
                                $existeUsuarioAsistencia = DB::select("SELECT name FROM users WHERE id = '$usuario'");
                                $existeUsuarioLogeado = $existeUsuarioLogeado == null ? "" : $existeUsuarioLogeado[0]->name;
                                $existeUsuarioAsistencia = $existeUsuarioAsistencia == null ? "" : $existeUsuarioAsistencia[0]->name;

                                //Guardar movimiento en historial poliza
                                DB::table('historialpoliza')->insert([
                                    'id_usuarioC' => $usuarioId, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                                    'cambios' => "$existeUsuarioLogeado " . $cadenaTipoAsistencia . " $existeUsuarioAsistencia manualmente"
                                ]);

                                //Guardar movimiento en historial sucursal
                                DB::table('historialsucursal')->insert([
                                    'id_usuarioC' => $usuarioId, 'id_franquicia' => $idFranquicia,
                                    'tipomensaje' => "0", 'created_at' => Carbon::now(),
                                    'cambios' => "$existeUsuarioLogeado " .$cadenaTipoAsistencia . " $existeUsuarioAsistencia manualmente", 'seccion' => "0"
                                ]);

                                return back()->with("bien", "La asistencia se modifico correctamente.");
                            }
                            return back()->with("alerta", "No puedes registrar hora de salida debido a que no cuentas con asistencia.");
                        }
                        return back()->with("alerta", "Valor no valido para tipo de asistencia.");
                    }
                    return back()->with("alerta", "Valor no valido para la asistencia.");
                }
                return back()->with("alerta", "No se encontro el usuario.");
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function asistenciaIndividual($idFranquicia, $idPoliza)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 8)) //ADMINISTRADORES
        {

            $usuario = request('usuario');

            $accionbanderaasistenciafranquicia = DB::select("SELECT estatus FROM accionesbanderasfranquicia WHERE id_franquicia = '$idFranquicia' AND tipo = '0' ORDER BY created_at DESC LIMIT 1");
            $accionbanderaasistenciafranquicia = $accionbanderaasistenciafranquicia == null ? 1 : $accionbanderaasistenciafranquicia[0]->estatus; //En caso de que no encuentre registro sera por codigo de barras por default

            $franquicia = DB::select("SELECT f.ciudad FROM franquicias f WHERE f.id = '$idFranquicia'");

            if ($usuario != null) {

                $queryAsistencia = "u.codigoasistencia = '$usuario'";//Asistencia por codigo de asistencia
                $movimiento = "por código de asistencia";//Asistencia por codigo de asistencia
                if ($accionbanderaasistenciafranquicia == 1) {
                    //Asistencia por codigo de barras
                    $queryAsistencia = "u.barcode = '$usuario'";
                    $movimiento = "por código de barras";
                }

                $usuarioExiste = DB::select("SELECT u.id,u.foto,u.name,u.rol_id FROM users u
                                                        INNER JOIN usuariosfranquicia uf on uf.id_usuario = u.id
                                                        INNER JOIN asistencia a ON a.id_usuario = u.id WHERE $queryAsistencia");
                if ($usuarioExiste != null) {

                    $ahora = Carbon::now()->format('H:i:s');
                    $controlentradasalidausuario = DB::select("SELECT horaini, horafin FROM controlentradasalidausuario WHERE id_usuario = '" . $usuarioExiste[0]->id . "'");

                    if($controlentradasalidausuario != null) {

                        $asistencia = 1;

                        $retardoInicio = $controlentradasalidausuario[0]->horaini;
                        $retardoFin = $controlentradasalidausuario[0]->horafin;
                        if ($ahora >= $retardoInicio && $ahora <= $retardoFin) {
                            $asistencia = 2;
                        } else if ($ahora > $retardoFin) {
                            $asistencia = 0;
                        }

                        DB::table("asistencia")->where("id_usuario", "=", $usuarioExiste[0]->id)->where("id_poliza", "=", $idPoliza)->update([
                            "id_tipoasistencia" => $asistencia,
                            "registroentrada" => Carbon::now(), "updated_at" => Carbon::now()
                        ]);

                        $usuarioId = Auth::user()->id;
                        $existeUsuarioAsistencia = DB::select("SELECT name FROM users WHERE id = '" . $usuarioExiste[0]->id . "'");
                        $existeUsuarioAsistencia = $existeUsuarioAsistencia == null ? "" : $existeUsuarioAsistencia[0]->name;

                        //Guardar movimiento en historial poliza
                        DB::table('historialpoliza')->insert([
                            'id_usuarioC' => $usuarioId, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                            'cambios' => "$existeUsuarioAsistencia tomó asistencia " . $movimiento
                        ]);

                        //Guardar movimiento en historial sucursal
                        DB::table('historialsucursal')->insert([
                            'id_usuarioC' => $usuarioId, 'id_franquicia' => $idFranquicia,
                            'tipomensaje' => "0", 'created_at' => Carbon::now(),
                            'cambios' => "$existeUsuarioAsistencia tomó asistencia " . $movimiento, 'seccion' => "0"
                        ]);

                        return view('administracion.poliza.asistenciaindividual', ['idFranquicia' => $idFranquicia, "idPoliza" => $idPoliza, "usuario" => $usuarioExiste,
                                         "asistencia" => $asistencia, 'franquicia' => $franquicia, 'accionbanderaasistenciafranquicia' => $accionbanderaasistenciafranquicia]);

                    }

                }

            }
            return view('administracion.poliza.asistenciaindividual', ['idFranquicia' => $idFranquicia, "idPoliza" => $idPoliza, 'franquicia' => $franquicia,
                'accionbanderaasistenciafranquicia' => $accionbanderaasistenciafranquicia]);
        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function polizaactualizarasisoptocobranza($idFranquicia, $idPoliza, $idUsuario)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 8)) //ADMINISTRADORES
        {

            try {

                $usuarioId = Auth::user()->id;
                $actualizar = carbon::now();
                $poliza = DB::select("SELECT * FROM poliza WHERE id = '$idPoliza'");
                if ($poliza != null) {
                    //Existe poliza

                    if($poliza[0]->estatus == 0) {
                        //Estatus de la poliza es NO TERMINADA

                        $usuario = DB::select("SELECT * FROM users WHERE id = '$idUsuario'");

                        if ($usuario != null) {
                            //Existe el usuario

                            $rol_id = $usuario[0]->rol_id;
                            $name = $usuario[0]->name;

                            $hoy = Carbon::now();
                            //$hoy = Carbon::parse("2023-03-13");
                            $hoyNumero = $hoy->dayOfWeekIso;

                            $polizaGlobales = new polizaGlobales(); //Creamos una nueva instancia de polizaGlobales
                            $contratosGlobal = new contratosGlobal; //Creamos una nueva instancia de contratosGlobal

                            //Traemos la ultima poliza de la semana actual.
                            $polizaAnterior = DB::select("SELECT * FROM poliza WHERE id_franquicia = '$idFranquicia'
                                                                AND STR_TO_DATE(created_at,'%Y-%m-%d') < STR_TO_DATE('" . $poliza[0]->created_at . "','%Y-%m-%d')
                                                                ORDER BY created_at DESC LIMIT 1");//Traemos la ultima poliza sin importar si es de la semana actual o no.
                            $polizaAnteriorId = $polizaAnterior == null ? "" : $polizaAnterior[0]->id;

                            switch ($rol_id) {
                                case 4:
                                    //Cobranza
                                    $id_zona = $usuario[0]->id_zona;

                                    $cobradoresAsignadosAZona = $contratosGlobal::obtenerCobradoresAsignadosZona($id_zona);
                                    foreach ($cobradoresAsignadosAZona as $cobradorAsignadoAZona) {
                                        //Recorrido cobradores
                                        DB::update("UPDATE abonos
                                        SET poliza = '$idPoliza'
                                        WHERE id_usuario = '" . $cobradorAsignadoAZona->id . "'
                                        AND poliza IS NULL");//Actualizamos los abonos
                                    }

                                    //Seleccionamos abonos ingresados por administradores, directores y principal
                                    $abonos = DB::select("SELECT a.id, a.indice
                                                        FROM abonos a
                                                        INNER JOIN users u
                                                        ON a.id_usuario = u.id
                                                        WHERE a.poliza IS NULL
                                                        AND a.id_zona = '$id_zona'
                                                        AND u.rol_id IN (6,7,8)");

                                    if($abonos != null) {
                                        //Existen abonos a actualizar
                                        foreach ($abonos as $abono) {
                                            $idAbono = $abono->id;
                                            $indice = $abono->indice;
                                            DB::update("UPDATE abonos
                                                                SET poliza  = '$idPoliza'
                                                                WHERE id = '$idAbono'
                                                                AND indice = '$indice'");//Actualizamos todos los abonos
                                        }
                                    }

                                    $polizaGlobales::calculoDeCobranza($idFranquicia, $idPoliza, $polizaAnteriorId, $idUsuario);
                                    break;
                                case 12:
                                    //Optometrista

                                    //Eliminar datos de la tabla polizaventasdias de igual manera se van a volver a insertar
                                    DB::delete("DELETE FROM polizaventasdias
                                                    WHERE id_poliza = '$idPoliza'
                                                    AND id_franquicia = '$idFranquicia'
                                                    AND id_usuario = '$idUsuario'
                                                    AND rol = '12'");

                                    //Eliminar datos de la tabla polizaproductividad de igual manera se van a volver a insertar
                                    DB::delete("DELETE FROM polizaproductividad
                                                    WHERE id_poliza = '$idPoliza'
                                                    AND id_franquicia = '$idFranquicia'
                                                    AND id_usuario = '$idUsuario'
                                                    AND rol = '12'");

                                    //Eliminar gasto de la tabla gastos que contengan ese $idPoliza y el idusuario de la optometrista
                                    DB::delete("DELETE FROM gastos WHERE id_poliza = '$idPoliza' AND tipogasto IN (9,10) AND id_usuario = '$idUsuario'");

                                    //Actualizamos todos los contratos
                                    DB::update("UPDATE contratos SET polizaoptometrista = '$idPoliza'
                                                    WHERE (estatus_estadocontrato IN (2,4,5,7,10,11,12) OR (estatus_estadocontrato = '9' AND esperapoliza = 0))
                                                    AND datos = '1'
                                                    AND polizaoptometrista IS NULL
                                                    AND id_optometrista = '$idUsuario'");

                                    $ventasOptos = $polizaGlobales::calculosVentasOptos($idPoliza, $polizaAnteriorId, $idUsuario); //Obtenemos las ventas de Optometristas
                                    $productividadOptos = $polizaGlobales::calculoProductividadOptos($idPoliza, $polizaAnteriorId, $idUsuario); //Obetenemos la productividad de Optos

                                    foreach ($ventasOptos as $ventaOpto) {
                                        $idOpto = $ventaOpto->id;
                                        $nombreOpto = $ventaOpto->name;
                                        $rolOpto = $ventaOpto->rol;
                                        $acumuladas = $ventaOpto->acumuladas;
                                        if ($hoyNumero == 2) {
                                            //Es martes
                                            $acumuladas = 0;
                                        }
                                        $diaActual = $ventaOpto->diaActual;
                                        $acumuladasTotal = $diaActual + $acumuladas;
                                        $ingresosGotas = $ventaOpto->gotas;
                                        $ingresoEnganche = $ventaOpto->enganche;
                                        $ingresoAbonos = $ventaOpto->abonos;
                                        $ingresoPoliza = $ventaOpto->polizas;
                                        $ingresosVentas = ($ingresoPoliza == null ? 0 : $ingresoPoliza) + ($ingresoEnganche == null ? 0 : $ingresoEnganche) +
                                                          ($ingresosGotas == null ? 0 : $ingresosGotas) + ($ingresoAbonos == null ? 0 : $ingresoAbonos);

                                        $polizagastosgotas = $ventaOpto->polizagastosgotas;
                                        $piezasgotas = $ventaOpto->piezasgotas;
                                        if ($polizagastosgotas > 0 && $piezasgotas > 0) {
                                            //Se vendieron gotas
                                            DB::table('gastos')->insert([
                                                'id_poliza' => $idPoliza, 'descripcion' => "Pago gotas para optometrista $nombreOpto por la cantidad de $piezasgotas piezas con total de $$polizagastosgotas",
                                                'monto' => $polizagastosgotas, 'tipogasto' => 9, 'created_at' => Carbon::now(), 'id_usuario' => $idOpto
                                            ]);
                                            DB::table('historialpoliza')->insert([
                                                'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                                                'cambios' => "Agregó un gasto de ventas con un monto de $" . $polizagastosgotas
                                                    . ", con la descripción: Pago gotas para optometrista $nombreOpto por la cantidad de $piezasgotas piezas con total de $$polizagastosgotas actualizado"
                                            ]);//Se agrega idUsuario de Sistema automatico

                                        }
                                        $polizagastospolizas = $ventaOpto->polizagastospolizas;
                                        $piezaspolizas = $ventaOpto->piezaspolizas;
                                        if ($polizagastospolizas > 0 && $piezaspolizas > 0) {
                                            //Se vendieron gotas
                                            DB::table('gastos')->insert([
                                                'id_poliza' => $idPoliza, 'descripcion' => "Pago polizas para optometrista $nombreOpto por la cantidad de $piezaspolizas piezas con total de $$polizagastospolizas",
                                                'monto' => $polizagastospolizas, 'tipogasto' => 10, 'created_at' => Carbon::now(), 'id_usuario' => $idOpto
                                            ]);
                                            DB::table('historialpoliza')->insert([
                                                'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                                                'cambios' => "Agregó un gasto de ventas con un monto de $" . $polizagastospolizas
                                                    . ", con la descripción: Pago polizas para optometrista $nombreOpto por la cantidad de $piezaspolizas piezas con total de $$polizagastospolizas actualizado"
                                            ]);//Se agrega idUsuario de Sistema automatico
                                        }

                                        $ingresosVentasAcumuladas = $ingresosVentas + ($ventaOpto->ingresosventasacumulado == null ? 0 : $ventaOpto->ingresosventasacumulado);

                                        $ventasOptosQuery = $polizaGlobales::obtenerQueryVentasOptos($polizaAnteriorId,$hoyNumero, $diaActual, $idFranquicia, $idOpto, $rolOpto);
                                        $query = "INSERT INTO polizaventasdias (id,id_franquicia,id_usuario,rol,id_poliza,fechapoliza,fechapolizacierre,nombre,lunes,martes,miercoles,jueves,
                                                                                viernes,sabado,acumuladas,asistencia,ingresosgotas,ingresosenganche,ingresospoliza,totaldia,ingresosventas,
                                                                                ingresosventasacumulado,ingresosabonos)
                                        VALUES(null,'$idFranquicia','$idOpto','12','$idPoliza','$hoy',null,'$nombreOpto'," . $ventasOptosQuery . ",'$acumuladasTotal',null,'$ingresosGotas',
                                                    '$ingresoEnganche','$ingresoPoliza',null,'$ingresosVentas','$ingresosVentasAcumuladas','$ingresoAbonos')";

                                        DB::insert($query);
                                    }

                                    foreach ($productividadOptos as $productividadOpto) {
                                        $idOpto = $productividadOpto->ID;
                                        $sueldo = $productividadOpto->SUELDO;
                                        $ECOJRANT = $productividadOpto->ECOJRANT == null ? 0 : $productividadOpto->ECOJRANT;
                                        $JUNIORANT = $productividadOpto->JUNIORANT == null ? 0 : $productividadOpto->JUNIORANT;
                                        $DORADOUNOANT = $productividadOpto->DORADOUNOANT == null ? 0 : $productividadOpto->DORADOUNOANT;
                                        $DORADODOSANT = $productividadOpto->DORADODOSANT == null ? 0 : $productividadOpto->DORADODOSANT;
                                        $PLATINOANT = $productividadOpto->PLATINOANT == null ? 0 : $productividadOpto->PLATINOANT;
                                        if ($hoyNumero == 2) {
                                            //Es martes
                                            $ECOJRANT = 0;
                                            $JUNIORANT = 0;
                                            $DORADOUNOANT = 0;
                                            $DORADODOSANT = 0;
                                            $PLATINOANT = 0;
                                        }
                                        $ECOJR = $productividadOpto->ECOJR == null ? 0 : $productividadOpto->ECOJR;
                                        $totalEcoAcu = $ECOJRANT + $ECOJR;
                                        $JUNIOR = $productividadOpto->JUNIOR == null ? 0 : $productividadOpto->JUNIOR;
                                        $totalJrAcu = $JUNIORANT + $JUNIOR;
                                        $DORADOUNO = $productividadOpto->DORADOUNO == null ? 0 : $productividadOpto->DORADOUNO;
                                        $totalDoradoAcu = $DORADOUNOANT + $DORADOUNO;
                                        $DORADODOS = $productividadOpto->DORADODOS == null ? 0 : $productividadOpto->DORADODOS;
                                        $totalDoradoDosAcu = $DORADODOSANT + ($DORADODOS == 0 ? $DORADODOS : ($DORADODOS / 2));
                                        $PLATINO = $productividadOpto->PLATINO == null ? 0 : $productividadOpto->PLATINO;

                                        $totalPlatinoAcu = $PLATINOANT + $PLATINO;
                                        $numeroVentas = $totalEcoAcu + $totalJrAcu + $totalDoradoAcu + $totalDoradoDosAcu + $totalPlatinoAcu;
                                        $productividad = ($numeroVentas * 100) / 30;
                                        $insumos = ($productividadOpto->INSUMOS == null ? 0 : $productividadOpto->INSUMOS) * $numeroVentas;

                                        DB::table("polizaproductividad")->insert([
                                            "id_franquicia" => $idFranquicia,
                                            "id_poliza" => $idPoliza,
                                            "id_usuario" => $idOpto,
                                            "rol" => '12',
                                            "sueldo" => $sueldo,
                                            "totaleco" => $totalEcoAcu,
                                            "totaljr" => $totalJrAcu,
                                            "totaldoradouno" => $totalDoradoAcu,
                                            "totaldoradodos" => $totalDoradoDosAcu,
                                            "totalplatino" => $totalPlatinoAcu,
                                            "numeroventas" => $numeroVentas,
                                            "productividad" => $productividad,
                                            "insumos" => $insumos
                                        ]);

                                    }

                                    break;
                                case 13:
                                    //Asistente

                                    //Eliminar datos de la tabla polizaventasdias de igual manera se van a volver a insertar
                                    DB::delete("DELETE FROM polizaventasdias
                                                    WHERE id_poliza = '$idPoliza'
                                                    AND id_franquicia = '$idFranquicia'
                                                    AND id_usuario = '$idUsuario'
                                                    AND rol = '13'");

                                    //Eliminar datos de la tabla polizaproductividad de igual manera se van a volver a insertar
                                    DB::delete("DELETE FROM polizaproductividad
                                                    WHERE id_poliza = '$idPoliza'
                                                    AND id_franquicia = '$idFranquicia'
                                                    AND id_usuario = '$idUsuario'
                                                    AND rol = '13'");

                                    //Actualizamos todos los contratos
                                    DB::update("UPDATE contratos SET poliza = '$idPoliza'
                                                    WHERE (estatus_estadocontrato IN (2,4,5,7,10,11,12) OR (estatus_estadocontrato = '9' AND esperapoliza = 0))
                                                    AND datos = '1'
                                                    AND poliza IS NULL
                                                    AND id_usuariocreacion = '$idUsuario'");

                                    $ventasAsis = $polizaGlobales::calculosVentasAsis($idPoliza, $polizaAnteriorId, $idUsuario); //Obtenemos las ventas de Asistentes
                                    $productividadAsis = $polizaGlobales::calculoProductividadAsis($idPoliza, $polizaAnteriorId, $idUsuario); //Optenemos la productividad de Asis

                                    foreach ($ventasAsis as $ventaAsis) {
                                        $idOpto = $ventaAsis->id;
                                        $nombreOpto = $ventaAsis->name;
                                        $rolOpto = $ventaAsis->rol;
                                        $acumuladas = $ventaAsis->acumuladas;
                                        if ($hoyNumero == 2) {
                                            //Es martes
                                            $acumuladas = 0;
                                        }
                                        $diaActual = $ventaAsis->diaActual;
                                        $acumuladasTotal = $diaActual + $acumuladas;
                                        $ingresosGotas = 0;
                                        $ingresoEnganche = 0;
                                        $ingresoAbonos = 0;
                                        $ingresoPoliza = 0;
                                        $ingresosVentas = 0;
                                        $ingresosVentasAcumuladas = 0;

                                        $ventasOptosQuery = $polizaGlobales::obtenerQueryVentasOptos($polizaAnteriorId,$hoyNumero, $diaActual, $idFranquicia, $idOpto, $rolOpto);
                                        $query = "INSERT INTO polizaventasdias (id,id_franquicia,id_usuario,rol,id_poliza,fechapoliza,fechapolizacierre,nombre,lunes,martes,miercoles,jueves,
                                                                                viernes,sabado,acumuladas,asistencia,ingresosgotas,ingresosenganche,ingresospoliza,totaldia,ingresosventas,
                                                                                ingresosventasacumulado,ingresosabonos)
                                        VALUES(null,'$idFranquicia','$idOpto','13','$idPoliza','$hoy',null,'$nombreOpto'," . $ventasOptosQuery . ",'$acumuladasTotal',null,'$ingresosGotas',
                                                    '$ingresoEnganche','$ingresoPoliza',null,'$ingresosVentas','$ingresosVentasAcumuladas','$ingresoAbonos')";
                                        DB::insert($query);
                                    }

                                    foreach ($productividadAsis as $productividadAsi) {
                                        $idAsis = $productividadAsi->ID;
                                        $sueldo = $productividadAsi->SUELDO;
                                        $ECOJRANT = $productividadAsi->ECOJRANT == null ? 0 : $productividadAsi->ECOJRANT;
                                        $JUNIORANT = $productividadAsi->JUNIORANT == null ? 0 : $productividadAsi->JUNIORANT;
                                        $DORADOUNOANT = $productividadAsi->DORADOUNOANT == null ? 0 : $productividadAsi->DORADOUNOANT;
                                        $DORADODOSANT = $productividadAsi->DORADODOSANT == null ? 0 : $productividadAsi->DORADODOSANT;
                                        $PLATINOANT = $productividadAsi->PLATINOANT == null ? 0 : $productividadAsi->PLATINOANT;
                                        if ($hoyNumero == 2) {
                                            //Es martes
                                            $ECOJRANT = 0;
                                            $JUNIORANT = 0;
                                            $DORADOUNOANT = 0;
                                            $DORADODOSANT = 0;
                                            $PLATINOANT = 0;
                                        }
                                        $ECOJR = $productividadAsi->ECOJR == null ? 0 : $productividadAsi->ECOJR;
                                        $totalEcoAcu = $ECOJRANT + $ECOJR;
                                        $JUNIOR = $productividadAsi->JUNIOR == null ? 0 : $productividadAsi->JUNIOR;
                                        $totalJrAcu = $JUNIORANT + $JUNIOR;
                                        $DORADOUNO = $productividadAsi->DORADOUNO == null ? 0 : $productividadAsi->DORADOUNO;
                                        $totalDoradoAcu = $DORADOUNOANT + $DORADOUNO;
                                        $DORADODOS = $productividadAsi->DORADODOS == null ? 0 : $productividadAsi->DORADODOS;
                                        $totalDoradoDosAcu = $DORADODOSANT + ($DORADODOS == 0 ? $DORADODOS : ($DORADODOS / 2));
                                        $PLATINO = $productividadAsi->PLATINO == null ? 0 : $productividadAsi->PLATINO;
                                        $totalPlatinoAcu = $PLATINOANT + $PLATINO;
                                        $numeroVentas = $totalEcoAcu + $totalJrAcu + $totalDoradoAcu + $totalDoradoDosAcu + $totalPlatinoAcu;
                                        $productividad = ($numeroVentas * 100) / 10;
                                        $insumos = $productividadAsi->INSUMOS == null ? 0 : $productividadAsi->INSUMOS;

                                        DB::table("polizaproductividad")->insert([
                                            "id_franquicia" => $idFranquicia,
                                            "id_poliza" => $idPoliza,
                                            "id_usuario" => $idAsis,
                                            "rol" => '13',
                                            "sueldo" => $sueldo,
                                            "totaleco" => $totalEcoAcu,
                                            "totaljr" => $totalJrAcu,
                                            "totaldoradouno" => $totalDoradoAcu,
                                            "totaldoradodos" => $totalDoradoDosAcu,
                                            "totalplatino" => $totalPlatinoAcu,
                                            "numeroventas" => $numeroVentas,
                                            "productividad" => $productividad,
                                            "insumos" => $insumos
                                        ]);
                                    }

                                    break;
                            }

                            //Actualizar ingresosventas e ingresoscobranza
                            $totaldia = DB::select("SELECT SUM(ingresosventas) as ingreso FROM polizaventasdias WHERE id_poliza = '$idPoliza'");
                            $totalingresocobranza = DB::select("SELECT SUM(ingresocobranza) as ingreso FROM polizacobranza WHERE id_poliza = '$idPoliza'");

                            $ingresosVentas = $totaldia[0]->ingreso == null ? 0 : $totaldia[0]->ingreso;
                            $ingresosCobranza = $totalingresocobranza[0]->ingreso == null ? 0 : $totalingresocobranza[0]->ingreso;

                            DB::table("poliza")->where("id", "=", $idPoliza)->where("id_franquicia", "=", $idFranquicia)->update([
                                "ingresosventas" => $ingresosVentas,
                                "ingresoscobranza" => $ingresosCobranza
                            ]);

                            //Guardar en historialpoliza
                            DB::table('historialpoliza')->insert([
                                'id_usuarioC' => $usuarioId, 'id_poliza' => $idPoliza, 'created_at' => $actualizar,
                                'cambios' => "Se actualizo la poliza con el usuario: " . $name
                            ]);

                            return redirect()->route('verpoliza', ['idFranquicia' => $idFranquicia, 'idPoliza' => $idPoliza])
                                             ->with('bien', 'Se actualizo correctamente la poliza')->with('pestaña', 'general');

                        }

                        return back()->with('alerta', 'El usuario no existe.');

                    }

                    return back()->with('alerta', 'No se puede actualizar la poliza por que el estatus es diferente a NO TERMINADA.');

                }

                return back()->with('alerta', 'La poliza no existe.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function ingresooficinaproducto($idFranquicia, $idPoliza)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {

            $piezas = request('piezas');
            $id_producto = request('producto');
            $descripcionproducto = request('descripcionproducto');

            if ($piezas < 1) {
                //Piezas es menor o igual a 0
                return back()->with('alerta', 'El numero de piezas es obligatorio, intenta de nuevo');
            }
            if ($id_producto == 'nada') {
                //No se selecciono ninguno producto
                return back()->with('alerta', 'El producto es obligatorio, intenta de nuevo');
            }
            if ($piezas == null) {
                //No se agrego ningun numero de piezas
                return back()->with('alerta', 'El numero de piezas es obligatorio, intenta de nuevo');
            }
            if (strlen($descripcionproducto) == 0) {
                //No se agrego descripcion
                $descripcionproducto = "Ninguna";
            }

            try {

                $producto = DB::select("SELECT * FROM producto WHERE id = '" . $id_producto . "'");

                if ($producto != null) {
                    //Existe el producto

                    if ($piezas > $producto[0]->piezas) {
                        //Total de piezas ingresadas es mayor a las piezas de inventario del producto
                        return back()->with('alerta', 'El numero de piezas ingresado es mayor a las piezas restantes del producto');
                    }

                    $nombreproducto = "armazón " . $producto[0]->nombre . " | " . $producto[0]->color; //Por default armazon
                    switch ($producto[0]->id_tipoproducto) {
                        case 3: //Gotas
                            $nombreproducto = "gotas";
                            break;
                        case 4: //Vitaminas
                            $nombreproducto = "vitaminas";
                            break;
                    }

                    $monto = $producto[0]->precio * $piezas;
                    if ($producto[0]->preciop != null) {
                        //Tiene promocion
                        $monto = $producto[0]->preciop * $piezas;
                    }

                    //Descontar piezas del producto
                    DB::table('producto')->where('id', '=', $id_producto)->update([
                        'piezas' => $producto[0]->piezas - $piezas
                    ]);

                    $idGasto = null;
                    if ($producto[0]->polizagastos != null && $producto[0]->polizagastos > 0) {
                        //polizagastos es diferente a nulo y es mayor a 0
                        $montopolizagotas = $producto[0]->polizagastos * $piezas;
                        $idGasto = DB::table('gastos')->insertGetId([
                            'id_poliza' => $idPoliza, 'descripcion' => "Pago de $nombreproducto por la cantidad de $piezas piezas con total de $$montopolizagotas, con la siguiente descripción: '$descripcionproducto'",
                            'monto' => $montopolizagotas, 'tipogasto' => 0, 'created_at' => Carbon::now()
                        ]);
                        DB::table('historialpoliza')->insert([
                            'id_usuarioC' => 699, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                            'cambios' => "SAgregó un gasto de oficina de producto de $nombreproducto con un monto de $" . $montopolizagotas
                                . ", con la descripción: '$descripcionproducto'"
                        ]);//Se agrega idUsuario de Sistema automatico
                    }

                    DB::table('ingresosoficina')->insert([
                        'id_poliza' => $idPoliza, 'descripcion' => "Ingreso de oficina de $piezas piezas de $nombreproducto con la siguiente descripción: '$descripcionproducto'",
                        'monto' => $monto, 'id_producto' => $id_producto, 'piezas' => $piezas, 'id_gasto' => $idGasto, 'created_at' => Carbon::now(),
                        'id_usuario' => Auth::user()->id
                    ]);

                    DB::table('historialpoliza')->insert([
                        'id_usuarioC' => Auth::user()->id, 'id_poliza' => $idPoliza, 'created_at' => Carbon::now(),
                        'cambios' => "Agregó un ingreso de oficina de producto de $nombreproducto con un monto de $$monto, con la siguiente descripción: '$descripcionproducto'"
                    ]);

                    return back()->with('bien', 'El ingreso de producto de oficina se agrego correctamente a la poliza.')->with('pestaña', 'oficina');

                }
                //No existe el producto
                return back()->with('alerta', 'El producto no existe.')->with('pestaña', 'oficina');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function filtrarlistapolizafranquicia($idFranquicia,  Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7)) //DIRECTOR
        {
            $idFranquiciaSeleccionada = request('franquiciaSeleccionada');

            $franquiciaPoliza = DB::select("SELECT id as idFranquicia,estado,ciudad,colonia,numero FROM franquicias WHERE id = '$idFranquiciaSeleccionada'");

            if ($franquiciaPoliza != null) {
                //Existe franquicia
                $polizas = DB::table('poliza as p')
                    ->select('p.id AS ID', 'p.id_franquicia AS FRANQUICIA', 'p.realizo AS REALIZO', 'p.autorizo AS AUTORIZO', 'p.total AS TOTAL', 'p.created_at AS CREATED_AT')
                    ->whereRaw("p.id_franquicia  = '$idFranquiciaSeleccionada'")
                    ->orderBy('p.created_at', 'DESC')
                    ->paginate(20);

                $franquicias = DB::select("SELECT id, ciudad FROM franquicias WHERE id != '00000'");

                return view('administracion.poliza.tabla', ['polizas' => $polizas, 'idFranquicia' => $idFranquiciaSeleccionada, 'franquiciaPoliza' => $franquiciaPoliza,
                    'franquicias' => $franquicias
                ]);

            }
            //No existe sucursal
            return back()->with('alerta', 'La sucursal seleccionada no existe');

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function ingresarCobranza($idFranquicia, $idPoliza, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {
            $validacion = Validator::make($request->all(), [
                'cantidad' => 'required|string|max:255',
            ]);
            if ($validacion->fails()) {
                return back()->with('alerta', 'Campo cantidad obligatorio.');
            }
            if (request('cantidad') < 0) {
                return back()->with('alerta', 'La cantidad no puede ser menor a 0.');
            }
            if (request('usuario') == 'nada') {
                return back()->with('alerta', 'Usuario obligatorio.');
            }

            try {

                $actualizar = carbon::now();
                $usuarioId = Auth::user()->id;
                $user = request('usuario');
                $cantidad = request('cantidad');

                $usuario = DB::select("SELECT u.id, z.zona, u.name from users u INNER JOIN zonas z on u.id_zona = z.id WHERE u.id = '$user'");

                if ($usuario != null) {
                    //El usuario existe

                    $zonausuario = $usuario[0]->zona;
                    $nombreusuario = $usuario[0]->name;

                    $idtipocobranza = DB::table('tipocobranza')->insertGetId([
                        'id_poliza' => $idPoliza, 'cantidad' => $cantidad, 'id_franquicia' => $idFranquicia,
                        'id_usuario' => $user, 'tipo' => 2, 'created_at' => Carbon::now()
                    ]);

                    DB::table('gastos')->insert([
                        'id_poliza' => $idPoliza, 'descripcion' => 'GASOLINA-ZONA ' . $zonausuario, 'monto' => $cantidad, 'tipogasto' => 2,
                        'pertenencia' => 2, 'id_usuario' => $user, 'id_tipocobranza' => $idtipocobranza,
                        'created_at' => Carbon::now()
                    ]);

                    DB::table('historialpoliza')->insert([
                        'id_usuarioC' => $usuarioId, 'id_poliza' => $idPoliza, 'created_at' => $actualizar,
                        'cambios' => "Se actualizo la cantidad de $" . $cantidad . " por concepto de gas para el cobrador $nombreusuario"
                    ]);

                    return redirect()->route('verpoliza', ['idFranquicia' => $idFranquicia, 'idPoliza' => $idPoliza])
                        ->with('bien', 'El registro de cobranza se actualizo correctamente')->with('pestaña', 'cobranza');

                }
                //No existe usuario
                return back()->with('alerta', 'El usuario no existe.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function actualizarfotogasto($idFranquicia, $idPoliza, Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 7) || ((Auth::user()->rol_id) == 8) || ((Auth::user()->rol_id) == 6)) {

            $validacion = Validator::make($request->all(), [
                'fotogasto' => 'required|image|mimes:jpg',
            ]);

            if ($validacion->fails()) {
                return back()->with('alerta', 'Campo foto obligatorio.');
            }

            if (request('idgasto') == 'nada') {
                return back()->with('alerta', 'Campo gasto obligatorio.');
            }

            //Validar tamaño de foto factura
            $contratosGlobal = new contratosGlobal;
            if(!$contratosGlobal::validarPesoArchivosAdjuntosSucursalContrato(request()->file('fotogasto'))){
                return back()->with('alerta',"Verifica el archivo 'Foto gasto', el tamaño maximo permitido es 1MB.");
            }

            try {

                $actualizar = carbon::now();
                $usuarioId = Auth::user()->id;
                $idgasto = request('idgasto');

                $gasto = DB::select("SELECT id, foto FROM gastos WHERE id = '$idgasto' AND id_poliza = '$idPoliza'");

                if ($gasto != null) {
                    //El usuario existe

                    $fotofactura = "";
                    if (request()->hasFile('fotogasto')) {
                        $fotoBruta = 'Foto-factura-Poliza-' . $idPoliza . '-' . time() . '.' . request()->file('fotogasto')->getClientOriginalExtension();
                        $fotofactura = request()->file('fotogasto')->storeAs('uploads/imagenes/polizas/fotofactura', $fotoBruta, 'disco');

                        $alto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/polizas/fotofactura/' . $fotoBruta)->height();
                        $ancho = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/polizas/fotofactura/' . $fotoBruta)->width();
                        if ($alto > $ancho) {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/polizas/fotofactura/' . $fotoBruta)->resize(600, 800);
                        } else {
                            $imagenfoto = Image::make(config('filesystems.disks.disco.root') . '/uploads/imagenes/polizas/fotofactura/' . $fotoBruta)->resize(800, 600);
                        }
                        $imagenfoto->save();

                        if ($gasto[0]->foto != null) { //Tiene foto el gasto?
                            //Eliminar foto actual
                            Storage::disk('disco')->delete($gasto[0]->foto);
                        }
                    }

                    //Actualizar registro de gasto
                    DB::table("gastos")->where("id_poliza", "=", $idPoliza)->where("id", "=", $idgasto)->update([
                        "foto" => $fotofactura
                    ]);

                    //Guardar movimiento
                    DB::table('historialpoliza')->insert([
                        'id_usuarioC' => $usuarioId, 'id_poliza' => $idPoliza, 'created_at' => $actualizar,
                        'cambios' => "Se actualizo la foto del gasto $idgasto"
                    ]);

                    return redirect()->route('verpoliza', ['idFranquicia' => $idFranquicia, 'idPoliza' => $idPoliza])
                        ->with('bien', "La foto del gasto $idgasto se actualizo correctamente")->with('pestaña', 'cobranza');

                }
                //No existe gasto
                return back()->with('alerta', 'El gasto no existe.');

            } catch (\Exception $e) {
                \Log::info("Error: " . $e->getMessage());
                return back()->with('error', 'Tuvimos un problema, por favor contacta al administrador de la pagina.');
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function cargarinformacionmodalpolizatiemporeal(Request $request) {

        //Opcion
        //0 -> Ingresos ventas
        //1 -> Productividad

        $idFranquicia = $request->input('idFranquicia');
        $id_usuario = $request->input('id_usuario');
        $id_poliza = $request->input('id_poliza');
        $opcion = $request->input('opcion');

        $array = array();
        $data = "";
        $contratos = array();

        if ($opcion == 0) {
            //Ingresos ventas

            //Contratos con gotas
            $consultacontratos = DB::select("SELECT c.id AS ID_CONTRATO FROM contratos c
                                                INNER JOIN contratoproducto cp ON c.id = cp.id_contrato
                                                INNER JOIN abonos a ON a.id_contrato = c.id
                                                INNER JOIN producto p ON cp.id_producto = p.id
                                                WHERE (a.id_usuario = (SELECT us.id FROM users us WHERE us.rol_id IN (12,13) AND us.id = a.id_usuario))
												AND c.id_optometrista = '$id_usuario' AND p.id_tipoproducto = 3
                                                AND a.id_contratoproducto = cp.id AND a.poliza = '$id_poliza'");

            $contratos = array_merge($contratos, $consultacontratos);

            //Contratos con enganches
            $consultacontratos = DB::select("SELECT a.id_contrato AS ID_CONTRATO FROM abonos a
                                                INNER JOIN contratos c ON c.id = a.id_contrato WHERE a.tipoabono IN (1,4,5)
                                                AND c.id_optometrista = '$id_usuario' AND a.id_usuario = c.id_usuariocreacion
                                                AND a.poliza = '$id_poliza'");

            $contratos = array_merge($contratos, $consultacontratos);

            //Contratos con abonos diferentes a enganche
            $consultacontratos = DB::select("SELECT a.id_contrato AS ID_CONTRATO FROM abonos a
                                                INNER JOIN contratos c ON c.id = a.id_contrato WHERE a.tipoabono NOT IN (1,4,5,7)
                                                AND c.id_optometrista = '$id_usuario' AND a.id_usuario = c.id_usuariocreacion
                                                AND a.poliza = '$id_poliza'");

            $contratos = array_merge($contratos, $consultacontratos);

            //Contratos con polizas
            $consultacontratos = DB::select("SELECT c.id AS ID_CONTRATO FROM contratos c
                                                INNER JOIN contratoproducto cp ON c.id = cp.id_contrato
                                                INNER JOIN abonos a ON a.id_contrato = c.id
                                                INNER JOIN producto p ON cp.id_producto = p.id
                                                WHERE (a.id_usuario = (SELECT us.id FROM users us WHERE us.rol_id IN (12,13) AND us.id = a.id_usuario))
												AND c.id_optometrista = '$id_usuario' AND p.id_tipoproducto = 2
                                            	AND a.id_contratoproducto = cp.id AND a.poliza = '$id_poliza'");

            $contratos = array_merge($contratos, $consultacontratos);

        } else {
            //Productividad

            $poliza = DB::select("SELECT id, created_at FROM poliza WHERE id = '$id_poliza'");

            if ($poliza != null) {
                //Existe poliza
                $fechapoliza = Carbon::parse($poliza[0]->created_at)->format('Y-m-d');
                $hoyNumero = Carbon::parse($fechapoliza)->dayOfWeekIso;

                if ($hoyNumero != 2) {
                    //Dia diferente de martes

                    $fecha = Carbon::parse($fechapoliza)->format('Y-m-d');

                    $hoyNumeroTemporal = $hoyNumero;
                    if ($hoyNumero == 1) {
                        //Es lunes
                        $hoyNumeroTemporal = 8;
                    }

                    for ($i = ($hoyNumeroTemporal - 2); $i > 0; $i--) {

                        //Obtener fechas de dias anteriores
                        $fecha = Carbon::create($fecha)->subDays(1)->format('Y-m-d'); //Descontando dias
                        $polizaAnterior = DB::select("SELECT id FROM poliza WHERE id_franquicia = '$idFranquicia' AND STR_TO_DATE(created_at,'%Y-%m-%d') = STR_TO_DATE('$fecha','%Y-%m-%d')");

                        if ($polizaAnterior != null) {
                            //Existe poliza
                            $consultacontratos = DB::select("SELECT c.id AS ID_CONTRATO, ec.descripcion AS DESCRIPCIONESTATUS FROM historialclinico hc INNER JOIN contratos c ON c.id = hc.id_contrato
                                                                INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato WHERE hc.tipo = '0' AND hc.id_paquete IN (1,2,3,4,5,6,7,8) AND c.polizaoptometrista = '" . $polizaAnterior[0]->id . "'
                                                                AND c.id_optometrista = '$id_usuario' AND c.aprobacionventa IN (0,1)");
                            $contratos = array_merge($contratos, $consultacontratos);
                        }

                    }

                }

                $consultacontratos = DB::select("SELECT c.id AS ID_CONTRATO, ec.descripcion AS DESCRIPCIONESTATUS FROM historialclinico hc INNER JOIN contratos c ON c.id = hc.id_contrato
                                                            INNER JOIN estadocontrato ec ON ec.estatus = c.estatus_estadocontrato WHERE hc.tipo = '0' AND hc.id_paquete IN (1,2,3,4,5,6,7,8) AND c.polizaoptometrista = '$id_poliza'
                                                            AND c.id_optometrista = '$id_usuario' AND c.aprobacionventa IN (0,1)");
                $contratos = array_merge($contratos, $consultacontratos);

            }

        }

        foreach ($contratos as $contrato) {
            //Recorrido de contratos
            $idContrato = trim($contrato->ID_CONTRATO);
            $descripcionEstatus = "";
            if ($opcion == 1) {
                //Productividad
                $descripcionEstatus = " - " . trim($contrato->DESCRIPCIONESTATUS);
            }
            if (!in_array($idContrato, $array)) {
                //No existe id_contrato en el array el cual se contara
                $data = $data . $idContrato . $descripcionEstatus . "<br>";
                array_push($array, $idContrato);
            }
        }

        return response()->json(['data' => $data]);

    }

}
