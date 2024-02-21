<?php

namespace App\Http\Controllers\Dominios\Clientes;

use App\Clases\contratosGlobal;
use App\Clases\polizaGlobales;
use App\Http\Controllers\Dominios\Administracion\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class citas extends Controller
{
    public function vacantes(){
        $idFranquicia = "6E2AA";
        return view('clientes.citas.vacantes', ['idFranquicia' => $idFranquicia]);
    }

    public function calendariocitas($idFranquicia){

        $franquicia = DB::select("SELECT f.ciudad, f.estado, f.telefonoatencionclientes, f.whatsapp FROM franquicias f WHERE f.id = '$idFranquicia'");

        //Franquicias para modal de agendar citas
        $franquicias = DB::select("SELECT f.id, f.ciudad,  f.calle, f.entrecalles, f.colonia, f.numero, f.estado, f.whatsapp, f.telefonoatencionclientes, REPLACE(f.coordenadas,' ', '') AS coordenadas
                                         FROM franquicias f WHERE f.id != '00000'
                                         ORDER BY f.ciudad ASC");

        return view('clientes.citas.agendarcita', ['franquicia' => $franquicia, 'idFranquicia' => $idFranquicia, 'franquicias' => $franquicias]);
    }

    public function  obtenercitasdisponibles(Request $request){
        $idFranquicia = $request->input('idFranquicia');
        $fechaSeleccionada = $request->input('fechaSeleccionada');

        //Dar formato a fecha -> usado para cuando sea fecha enviada desde JS
        $fechaSeleccionada = str_replace("/","-",$fechaSeleccionada);
        $fechaSeleccionada = Carbon::parse($fechaSeleccionada)->format('Y-m-d');

        $citasAgendadas = DB::select("SELECT * FROM agendacitas ac WHERE ac.fechacitaagendada = '$fechaSeleccionada' AND ac.id_franquicia = '$idFranquicia' AND ac.estadocita = '0'
                                            ORDER BY ac.horacitaagendada ASC");
        $horarioDisponibles = array();

        $franquicia = DB::select("SELECT f.ciudad, f.estado, f.telefonofranquicia, f.telefonoatencionclientes, f.whatsapp FROM franquicias f WHERE f.id = '$idFranquicia'");

        if($franquicia != null){

            $horaInicioCita = Carbon::parse('9:00:00')->format('H:i:s');
            $citas = [];
            $dia = Carbon::parse($fechaSeleccionada)->dayOfWeekIso;

            //Dia es diferente de Sabado?
            if($dia != 6){
                //Entre semana - Total de citas posibles a agendar
                $totalCitas = 16;
            }else {
                //Es sabado - Horario de atencion solo recibe 12 citas como total para terminar a 3:00 pm
                $totalCitas = 12;

            }

            //Ciclo para recorrear las citas dispobles a agendar durante todo el dia
            for ($i = 0; $i < $totalCitas; $i = $i + 1){
                $banderaHorarioDisponible = true;
                $horaFinCita = date('H:i:s', strtotime($horaInicioCita.'+ 30 minute'));

                $now = Carbon::now();
                $fechaActual = Carbon::parse($now)->format('Y-m-d');
                $horaActual = Carbon::parse($now)->format('H:i:s');
                //Verificar si se seleccion una fecha mayor o igual a hoy
                if($fechaActual <= $fechaSeleccionada ){
                    //Existen citas agendadas?
                    if($citasAgendadas != null){
                        //Verificar horarios ya no disponibles
                        foreach ($citasAgendadas as $citaAgendada){
                            if($citaAgendada->horacitaagendada == $horaInicioCita && $citaAgendada->estadocita == '0') {
                                $banderaHorarioDisponible = false;
                            }
                        }
                    }

                    if($banderaHorarioDisponible){
                        //Validar si no a pasado la hora
                        if(($fechaActual == $fechaSeleccionada && $horaActual < $horaInicioCita) || $fechaSeleccionada > $fechaActual){
                            array_push($horarioDisponibles, $horaInicioCita);
                        }
                    }
                }

                $horaInicioCita = $horaFinCita;
            }

            $response = ['horarioDisponibles' => $horarioDisponibles, 'fechaSeleccionada' => $fechaSeleccionada, 'idFranquicia' => $idFranquicia];

            return response()->json($response);
        }

    }

    public function agedarcita(Request $request){
        $idFranquicia = $request->input('idFranquicia');
        $nombre = $request->input('nombre');
        $telefono = $request->input('telefono');
        $email = $request->input('email');
        $observaciones = $request->input('observaciones');
        $fechaCita = $request->input('fechaCita');
        $horaCita = $request->input('horaCita');
        $fechaHoraCita = $fechaCita . ", " .$horaCita;
        $fechaHoraCita = str_replace("/","-",$fechaHoraCita);
        $localidad = $request->input('localidad');
        $colonia = $request->input('colonia');
        $domicilio = $request->input('domicilio');
        $numero = $request->input('numero');
        $entrecalles = $request->input('entrecalles');
        $otrotipocita = null;

        //RB lugar de cita
        // 0 -> cita en sucursal
        // 1 -> cita a domicilio
        $rbExamenSucursal = $request->input('lugarCita');
        $lugarcita = "sucursal";    //Lugar de cita por default sucursal

        if($rbExamenSucursal != null){
            //Se selecciono un rb para lugar de cita

            if($rbExamenSucursal == 1){
                //Seleccionamos lugar de cita a domicilio
                $lugarcita = "domicilio";
            }

        }else{
            //No se selecciono ningun rb para lugar de cita
            $response = ['bandera' => false, 'mensaje' => "Debes seleccionar el lugar de preferencia para que realicen tu consulta"];
            return response()->json($response);
        }

        //RB tipo de cita
        // 0 -> Examen
        // 1 -> Armazon
        // 2 -> Gotas
        // 3 -> Otro tipo
        $rbTipoCita = $request->input('tipoCita');
        if($rbTipoCita != null){
            //Se selecciono un rb para tipo de cita

            if($rbTipoCita == 3){
                $otrotipocita = $request->input('otroTipoCita');

                if($otrotipocita == null){
                    //Campo es vacio
                    $response = ['bandera' => false, 'mensaje' => "Especifique el tipo de cita a realizar"];
                    return response()->json($response);
                }
            }

        }else{
            //No se selecciono ningun rb para lugar de cita
            $response = ['bandera' => false, 'mensaje' => "Debes seleccionar el tipo de consulta que requieres."];
            return response()->json($response);
        }

        //Seprara Horario de cita en fecha y hora
        $horarioAgendado = explode(",", $fechaHoraCita);
        $fechaCita = Carbon::parse($horarioAgendado[0])->format('Y-m-d');
        $horaCita = Carbon::parse($horarioAgendado[1])->format('H:i:s');
        $fechaMinimaAgendarCitaDomicilio = Carbon::parse(self::tiempoAnticipacionCitaDomicilio())->format('Y-m-d');

        //Rango de fechas para agendar citas
        $now = Carbon::now();
        $fechaActual = Carbon::parse($now)->format('Y-m-d');
        $polizaGlobales = new polizaGlobales();
        $numeroDia = $now->dayOfWeekIso;    //Obtenemos el dia de la semana actual
        $fechaLunes = $fechaActual;
        if($numeroDia != 1){
            //Dia actual es diferente de lunes
            $fechaLunes = $polizaGlobales::obtenerDia($numeroDia, 1);   //se obtenie la fecha del lunes anterior a la fecha actaul
        }
        $fechaDomingo =  date("Y-m-d",strtotime($fechaLunes."+ 6 days"));

        $fechaHoraActual = Carbon::parse($now)->format('Y-m-d H:i:s');
        $fechaHoraCita = Carbon::parse($fechaHoraCita)->format('Y-m-d H:i:s');
        //Validar que fecha para agendar cita no sea menos a hoy
        if($fechaHoraActual <= $fechaHoraCita){
            //Fecha es igual o mayor a hoy

            //Validar si es cita a domicilio que la tenga al menos 72 horas de anticipacion
            if(($rbExamenSucursal == 0) || ($rbExamenSucursal == 1 && $fechaCita >= $fechaMinimaAgendarCitaDomicilio)){
                //Validar formato de telefono ingresado - formato: 333-333-33-33
                if(preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{2}-[0-9]{2}$/", $telefono) || preg_match("/^[0-9]{10}$/", $telefono)){
                    //Verificar si telefono tiene formato Tel:9999999999
                    if(strlen($telefono) == 10){
                        //tiene formato sin guiones - Partir numero y concatenar guiones
                        $telefonoFormato = substr($telefono,0,3).'-'.substr($telefono,3,3).'-'.substr($telefono,6,2).'-'.substr($telefono,8,2);
                        //Remplazar variable telefono por telefono con formato
                        $telefono = $telefonoFormato;
                    }

                    //Verificar que no hay ninguna cita agendada para el horario seleccionado
                    $existeCitaAgendadaSemanaActual = DB::select("SELECT * FROM agendacitas ac WHERE ac.telefono = '$telefono'
                               AND STR_TO_DATE(ac.fechacitaagendada,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaLunes','%Y-%m-%d') AND STR_TO_DATE('$fechaDomingo','%Y-%m-%d') AND ac.estadocita = '0'");

                    if($existeCitaAgendadaSemanaActual == null){
                        //El paciente no cuenta con una cita agendada para esta semana

                        $existeCitaAgendada = DB::select("SELECT * FROM agendacitas ac WHERE ac.id_franquicia = '$idFranquicia' AND ac.fechacitaagendada = '$fechaCita'
                                                   AND ac.horacitaagendada = '$horaCita' AND ac.estadocita = '0'");

                        if($existeCitaAgendada == null){
                            //No existen citas agendadas

                            //Almacenar cita
                            $contratosGlobal = new contratosGlobal();
                            $referenciaCita = $contratosGlobal::generarReferenciaCita($idFranquicia);

                            DB::table('agendacitas')->insert([
                                'id_franquicia' => $idFranquicia, 'nombre' => $nombre, 'telefono' => $telefono, 'email' => $email, 'observaciones' => $observaciones,
                                'fechacitaagendada' => $fechaCita, 'horacitaagendada' => $horaCita, 'estadocita' => '0', 'localidad' => $localidad, 'colonia' => $colonia,
                                'domicilio' => $domicilio, 'numero' => $numero, 'entrecalles' => $entrecalles, 'lugarcita' => $lugarcita, 'tipocita' => $rbTipoCita,
                                'otrotipocita' => $otrotipocita, 'referencia' => $referenciaCita, 'created_at' => Carbon::now()
                            ]);

                            //Registrar numero de referencia en tabla de referencias de tipo cita
                            DB::table('referencias')->insert([
                                'tipo' => '01', 'referencia' => $referenciaCita, 'created_at' => Carbon::now()
                            ]);

                            //Registrar nueva notificacion a generar alertas multiples
                            DB::table("notificacionesvisualizaciones")->insert([
                                'fechanotificacion' => $fechaActual,
                                'numeronotificaciones' => 20,
                                'tiponotificacion' => '1',
                                'referencia_cita' => $referenciaCita,
                                'created_at' => $now
                            ]);

                            //Generar JSON con datos para comprobante
                            $sucursal = DB::select("SELECT f.ciudad, f.estado, f.colonia, f.calle, f.entrecalles, f.numero FROM franquicias f WHERE f.id = '$idFranquicia'");
                            $datosComprobante = null;
                            $datosComprobante["sucursal"] = $sucursal[0]->ciudad . ', '.$sucursal[0]->estado;
                            if($sucursal[0]->calle != null){
                                $datosComprobante["direccionsucursal"] = $sucursal[0]->calle . ' NO. '.$sucursal[0]->numero .', COL. '.$sucursal[0]->colonia;
                            }else{
                                $datosComprobante["direccionsucursal"] = "SIN DATOS";
                            }

                            if($sucursal[0]->entrecalles != null){
                                $datosComprobante["entrecallesucursal"] = $sucursal[0]->entrecalles;
                            }else{
                                $datosComprobante["entrecallesucursal"] = "SIN DATOS";
                            }

                            $datosComprobante["nombre"] = $nombre;
                            $datosComprobante["telefono"] = $telefono;
                            $datosComprobante["email"] = $email;
                            $datosComprobante["observaciones"] = strtoupper($observaciones);
                            $datosComprobante["fechacita"] = $fechaCita;
                            $datosComprobante["horacita"] = $horaCita;
                            $datosComprobante["lugarcita"] = $lugarcita;
                            $datosComprobante["tipocita"] = $rbTipoCita;
                            $datosComprobante["otrotipocita"] = $otrotipocita;
                            $datosComprobante["localidad"] = $localidad;
                            $datosComprobante["colonia"] = $colonia;
                            $datosComprobante["domicilio"] = $domicilio;
                            $datosComprobante["numero"] = $numero;
                            $datosComprobante["entrecalles"] = $entrecalles;
                            $datosComprobante["referencia"] = $referenciaCita;

                            $response = ['bandera' => true, 'mensaje' => "Cita agendada correctamente. Fecha cita: '".$fechaCita."' Horario: '".$horaCita."'",
                                'datosComprobante' => $datosComprobante];

                        }else{
                            //Existe una cita agendada para esa hora
                            $response = ['bandera' => false, 'mensaje' => 'Horario seleccionada ya no disponibles, por favor intenta registrar la cita en un nuevo horario.'];
                        }

                    }else {
                        $response = ['bandera' => false, 'mensaje' => "No puedes agendar una nueva cita dentro de la semana de:  '" . $fechaLunes . "' a '" . $fechaDomingo . "' debido que tienes una consulta pendiente."];
                    }

                }else {
                    //Numero de telefono incorrecto
                    $response = ['bandera' => false, 'mensaje' => "Número de teléfono incorrecto."];
                }

            }else{
                //Fecha de anticipacion para cita a domicilio no es de 72 horas
                $response = ['bandera' => false, 'mensaje' => "Para una cita ha domicilio requieres agendarla con al menos 72 horas de anticipación. Fecha sugerida para consulta a domicilio: '.$fechaMinimaAgendarCitaDomicilio"];

            }
        }else{
            //Fecha menor al dia de hoy
            $response = ['bandera' => false, 'mensaje' => "La fecha para agendar cita debe ser mayor o igual al dia de hoy."];
        }

        return response()->json($response);

    }

    public function vacantesagendarcita($idFranquicia, $idRol){

        $franquicias = DB::select("SELECT f.id, f.ciudad,  f.calle, f.entrecalles, f.colonia, f.numero, f.estado, f.whatsapp, f.telefonoatencionclientes, REPLACE(f.coordenadas,' ', '') AS coordenadas
                                         FROM franquicias f WHERE f.id != '00000'
                                         ORDER BY f.ciudad ASC");

        return view('clientes.citas.agendacitavacante', ['idFranquicia' => $idFranquicia, 'idRol' => $idRol, 'franquicias' => $franquicias]);
    }

    public function obtenervacantesfranquicia(Request $request){
        $idFranquicia = $request->input('idFranquicia');

        //Obtener vacantes para rol de cobranza, administracion, asistente y chofer
        $vacantes = DB::select("SELECT v.id_rol, (SELECT r.rol FROM roles r WHERE r.id = v.id_rol) AS vacante
                                      FROM vacantes v WHERE v.id_franquicia = '$idFranquicia' AND v.estado = 0 AND v.id_rol IN (4,6,13,17) GROUP BY v.id_rol ORDER BY vacante ASC");

        $response = ['vacantes' => $vacantes];

        return response()->json($response);
    }
    public function agendarcitavacantesucursal($idFranquicia, Request $request){

        request()->validate([
            'nombre' => 'required|string|min:5|max:255',
            'apellidos' => 'required|string|min:5|max:255',
            'telefono' => 'required',
            'email' => 'required',
            'sucursalSeleccionada' => 'required',
            'vacanteSeleccionada' => 'required',
            'fecha' => 'required',
            'curriculum' => 'required|file|mimes:pdf',
            'cbCondiicones' => 'required'

        ]);

        $nombre = $request->input('nombre');
        $apellidos = $request->input('apellidos');
        $telefono = $request->input('telefono');
        $idFranquiciaSeleccionada = $request->input('sucursalSeleccionada');
        $rolVacante = $request->input('rolVocanteSeleccionado');
        $fechacita = $request->input('fecha');

        $existeSucursal = DB::select("SELECT f.ciudad FROM franquicias f WHERE f.id= '$idFranquiciaSeleccionada'");
        if($existeSucursal != null){
            //Si existe sucursal
            $vacanteDisponible = DB::select("SELECT * FROM vacantes v WHERE v.id_franquicia = '$idFranquiciaSeleccionada' AND v.id_rol = '$rolVacante' AND v.estado = 0 ORDER BY v.created_at LIMIT 1");
            if($vacanteDisponible != null){
                //Vacante disponible para agendar cita
                $identificarVacante = $vacanteDisponible[0]->identificador;
                $hoy = Carbon::parse(Carbon::now())->format("Y-m-d");
                if(Carbon::parse($fechacita)->format("Y-m-d") >= $hoy){
                    if(preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{2}-[0-9]{2}$/", $telefono) || preg_match("/^[0-9]{10}$/", $telefono)){

                        //Guaradr CV en servidor
                        if (request()->hasFile('curriculum')) {
                            $archivoCVBruta = 'Solicitud-Vacante-Referencia-' . $identificarVacante . '-' . time() . '.' . request()->file('curriculum')->getClientOriginalExtension();
                            $curriculum = request()->file('curriculum')->storeAs('uploads/imagenes/paginaclientes/vacantes/curriculum', $archivoCVBruta, 'disco');
                        }

                        //Insertar cita para entrevista de trabajo
                        DB::table('vacantes')->insert([
                            'id_franquicia' => $idFranquiciaSeleccionada, 'id_rol' => $rolVacante, 'observacionessolicitud' => "",
                            'nombresolicitante' => $nombre . " " .$apellidos, 'telefono' => $telefono, 'fechacita' => $fechacita,
                            'observaciones' => "Cita agendada desde pagina de clientes", 'estado'=>'8',
                            'identificador' => $identificarVacante, 'curriculum' => $curriculum, 'created_at' => Carbon::now()
                        ]);

                        return back()->with('bien',"Cita para entrevista agendada correctamente.");

                    }else{
                        //Numero de telefono incorrecto
                        return back()->with('alerta',"Número de teléfono incorrecto.");
                    }
                }else{
                    //Fecha seleccioinada es menor a hoy
                    return back()->with('alerta',"Fecha para agendar cita debe ser igual o mayor a hoy.");
                }
            }else{
                //Vacante ya no necesaria
                return back()->with('alerta',"Vacante seleccionada no disponible.");
            }

        }else{
            //No existe sucursal
            return back()->with('alerta',"No existe sucursal seleccionada para aplicar a vacante.");
        }

    }
    public function tiempoAnticipacionCitaDomicilio(){
        //Rango de fechas para agendar citas
        $now = Carbon::now();
        $fechaActual = Carbon::parse($now)->format('Y-m-d');

        $fechaMinimaAnticipacion = $fechaActual;
        $diasHabilesAnticipacion = 0;

        //Sumar dias a la fecha hasta ajustar 3 dias habiles (Lunes - Sabado) - seguir sumando hasta ajustar 72 horas naturales
        while ($diasHabilesAnticipacion <= 3){

            //Sumar 1 dia a fecha actual
            $fechaMinimaAnticipacion =  date("Y-m-d",strtotime($fechaMinimaAnticipacion."+ 1 days"));
            //Obtener dia de fecha resultante
            $numeroDia = Carbon::parse($fechaMinimaAnticipacion)->dayOfWeekIso;
            if($numeroDia != 7){
                //numero dia es diferente de sabado
                $diasHabilesAnticipacion = $diasHabilesAnticipacion + 1;
            }
        }

        //Retornar fecha minima para agendar cita a domicilio
        return $fechaMinimaAnticipacion;

    }
}
