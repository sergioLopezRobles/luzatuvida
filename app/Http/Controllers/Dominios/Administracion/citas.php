<?php

namespace App\Http\Controllers\Dominios\Administracion;

use App\Clases\contratosGlobal;
use App\Clases\polizaGlobales;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class citas extends Controller
{
    public function listaagendacitas($idFranquicia)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) //ADMINISTRACION, PRINCIPAL, DIRECTOR
        {

            $sucursales = DB::select("SELECT * FROM franquicias f WHERE f.id != '00000'");

            $fechaActual = Carbon::now();
            Carbon::parse($fechaActual)->format('Y-m-d');
            return view('administracion.clientes.citas.agendacitaspacientes', ['idFranquicia' => $idFranquicia, 'fechaActual' => $fechaActual, 'sucursales' => $sucursales]);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function cargaragendacitaspacientes(Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) //ADMINISTRACION, PRINCIPAL, DIRECTOR
        {
            $idFranquicia = $request->input('idFranquicia');
            $fechaSeleccionada = $request->input('fechaSeleccionada');
            $fechaSeleccionada = Carbon::parse($fechaSeleccionada)->format('Y-m-d');

            $now = Carbon::now();
            $fechaActual = Carbon::parse($now)->format('Y-m-d H:i:s');

            $citasExistentes = DB::select("SELECT ac.indice, ac.nombre, ac.email, ac.telefono, ac.observaciones, ac.horacitaagendada, ac.estadocita, ac.localidad,
                                                ac.colonia, ac.domicilio, ac.numero, ac.entrecalles, ac.lugarcita, ac.tipocita, ac.otrotipocita,
                                                f.ciudad, f.estado, ac.referencia FROM agendacitas ac
                                                INNER JOIN franquicias f ON f.id = ac.id_franquicia WHERE STR_TO_DATE(ac.fechacitaagendada,'%Y-%m-%d') = STR_TO_DATE('$fechaSeleccionada','%Y-%m-%d')
                                                AND ac.id_franquicia = '$idFranquicia' ORDER BY ac.horacitaagendada ASC");

            $horaInicioCita = Carbon::parse('9:00:00')->format('H:i:s');
            $dia = Carbon::parse($fechaSeleccionada)->dayOfWeekIso;

            //Dia es diferente de Sabado?
            if ($dia != 6) {
                //Entre semana - Total de citas posibles a agendar
                $totalCitas = 32;
            } else {
                //Es sabado - Horario de atencion solo recibe 12 citas como total para terminar a 3:00 pm
                $totalCitas = 24;

            }

            $fraquicia = DB::select("SELECT f.ciudad, f.estado FROM franquicias f WHERE f.id = '$idFranquicia'");
            $citasAgendadas = [];
            //Ciclo para recorrear las citas dispobles a agendar durante todo el dia
            for ($i = 0; $i < $totalCitas; $i = $i + 1) {
                $datos = array();
                $banderaHorarioDisponible = true;
                $fechaHoraSeleccionada = Carbon::parse($fechaSeleccionada . ' ' . $horaInicioCita)->format('Y-m-d H:i:s');

                if ($citasExistentes != null) {
                    //Verificar horarios ya no disponibles
                    foreach ($citasExistentes as $citaExistente) {
                        if (Carbon::parse($citaExistente->horacitaagendada)->format('H:i:s') == $horaInicioCita) {
                            $datos['indice'] = $citaExistente->indice;
                            $datos['sucursal'] = $citaExistente->ciudad . ', ' . $citaExistente->estado;
                            $datos['nombre'] = $citaExistente->nombre;
                            $datos['email'] = $citaExistente->email;
                            $datos['telefono'] = $citaExistente->telefono;
                            $datos['observaciones'] = $citaExistente->observaciones;
                            $datos['localidad'] = $citaExistente->localidad;
                            $datos['colonia'] = $citaExistente->colonia;
                            $datos['domicilio'] = $citaExistente->domicilio;
                            $datos['numero'] = $citaExistente->numero;
                            $datos['entrecalles'] = $citaExistente->entrecalles;
                            $datos['lugarcita'] = $citaExistente->lugarcita;
                            $datos['tipocita'] = $citaExistente->tipocita;
                            $datos['otrotipocita'] = $citaExistente->otrotipocita;
                            $datos['referencia'] = $citaExistente->referencia;

                            switch ($citaExistente->estadocita){
                                case '0':
                                    //Agendado
                                    $datos['estado'] = "AGENDADO";
                                    $datos['horaCita'] = $horaInicioCita;
                                    $banderaHorarioDisponible = false;
                                    break;
                                case '1':
                                    //Asistio a cita
                                    $datos['estado'] = "ASISTIO";
                                    $datos['horaCita'] = $horaInicioCita;
                                    $banderaHorarioDisponible = false;
                                    break;
                                case '2':
                                    //Cancelo cita
                                    $datos['estado'] = "CANCELO";
                                    $datos['horaCita'] = $horaInicioCita;

                                    //Es una fecha ya no validad - No pintar renglon en tabla para posibilidad de agendar nueva cita en ese horario
                                    if ($fechaActual >= $fechaHoraSeleccionada) {
                                        //Cita cancelada y fecha ya no es valida para agendar
                                        $banderaHorarioDisponible = false;
                                    }
                                    break;
                            }

                            //Registrar cita en arreglo
                            array_push($citasAgendadas, $datos);
                        }
                    }
                }
                if ($banderaHorarioDisponible) {
                    //Validar que fecha y hora no sea menor a fecha y hora actual

                    //Asignar valor de franquicia
                    if ($fraquicia != null) {
                        $datos['sucursal'] = $fraquicia[0]->ciudad . ', ' . $fraquicia[0]->estado;
                    } else {
                        $datos['sucursal'] = "";
                    }

                    if ($fechaActual <= $fechaHoraSeleccionada) {
                        //Horario disponible para agendar
                        $datos['nombre'] = "";
                        $datos['telefono'] = "";
                        $datos['estado'] = "DISPONIBLE";
                        $datos['horaCita'] = $horaInicioCita;
                    } else {
                        //Horario disponible para agendar
                        $datos['nombre'] = "";
                        $datos['telefono'] = "";
                        $datos['estado'] = "NO DISPONIBLE";
                        $datos['horaCita'] = $horaInicioCita;
                    }

                    array_push($citasAgendadas, $datos);
                }
                $horaInicioCita = date('H:i:s', strtotime($horaInicioCita . '+ 15 minute'));
            }

            //Movimientos de sucursal para citas pacientes
            $fechaActual = Carbon::parse($fechaActual)->format('Y-m-d');
            if(Carbon::parse($now)->format('Y-m-d') > $fechaSeleccionada){
                 //Fecha actual es mayor que fecha seleccionada
                $cadenaFiltroFecha = " AND STR_TO_DATE(hs.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaSeleccionada','%Y-%m-%d') AND STR_TO_DATE('$fechaActual','%Y-%m-%d')";

            }else{
                //Fecha seleccionada es mayor a fecha actual
                $cadenaFiltroFecha = " AND STR_TO_DATE(hs.created_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaActual','%Y-%m-%d') AND STR_TO_DATE('$fechaSeleccionada','%Y-%m-%d')";
            }

            $movimientosCitasPacientes = DB::select("SELECT hs.cambios, hs.created_at, (SELECT u.name FROM users u WHERE u.id = hs.id_usuarioC) as usuario
                                                          FROM historialsucursal hs
                                                          WHERE hs.tipomensaje = '11' AND hs.seccion = '2' AND hs.id_franquicia = '$idFranquicia'
                                                          " . $cadenaFiltroFecha . " ORDER BY hs.created_at DESC");

            $response = ['data' => $citasAgendadas, 'fechaSeleccionada' => $fechaSeleccionada, 'movimientosCitasPacientes' => $movimientosCitasPacientes];
            return response()->json($response);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }

    }

    public function agendarcitaadministracion($idFranquicia, Request $request)
    {

        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) //ADMINISTRACION, PRINCIPAL, DIRECTOR
        {
            $rolUsuario = Auth::user()->rol_id;
            $nombre = $request->input('nombre');
            $email = $request->input('email');
            $telefono = $request->input('telefono');
            $observaciones = $request->input('observaciones');
            $fechaCita = $request->input('fechaCita');
            $horaCita = $request->input('horarioSeleccionado');
            $localidad = $request->input('localidad');
            $colonia = $request->input('colonia');
            $domicilio = $request->input('domicilio');
            $numero = $request->input('numero');
            $entrecalles = $request->input('entrecalles');
            $otrotipocita = null;

            $validaciones = Validator::make($request->all(), [
                'nombre' => 'required|string',
                'telefono' => 'required|string|min:10|max:13',
                'fechaCita' => 'required|string',
                'horarioSeleccionado' => 'required|string'
            ]);

            if ($validaciones->fails()) {
                return back()->withErrors($validaciones)->withInput()->with('alerta', 'Existe uno o mas campos con datos incorrectos, verifica la información ingresada para agendar la cita.');
            }

            //RB tipo de cita
            // 0 -> cita en sucursal
            // 1 -> cita a domicilio
            $rbExamenSucursal = $request->input('radioExamen');
            $lugarcita = "sucursal";    //Lugar de cita por default sucursal

            if($rbExamenSucursal == 1){
                //Seleccionamos lugar de cita a domicilio
                $lugarcita = "domicilio";

                $validaciones = Validator::make($request->all(),[
                    'localidad' => 'required|string',
                    'colonia' => 'required|string',
                    'domicilio' => 'required|string',
                    'numero' => 'required|string',
                    'entrecalles' => 'required|string'
                ]);

                if ($validaciones->fails()) {
                    return back()->with('alerta', "Si requieres una cita ha domicilio debes completar todos los campos de la sección 'DATOS PARA VISITA'")->withInput($request->all());
                }
            }

            //RB tipo de cita
            // 0 -> Examen
            // 1 -> Armazon
            // 2 -> Gotas
            // 3 -> Otro tipo
            $rbTipoCita = $request->input('rbTipoCita');
            if($rbTipoCita == 3){
                $otrotipocita = $request->input('otroTipoCita');

                //Validar que se haya llenado coapo de texto otro tipo de cita
                $validaciones = Validator::make($request->all(),[
                    'otroTipoCita' => 'required|string'
                ]);

                if ($validaciones->fails()) {
                    return back()->withErrors($validaciones)->withInput()->with('alerta',"Especifica tipo de cita que requieres en seccion 'TIPO DE CITA'");
                }
            }

            //Rol de director - Tomar idFranquicia seleccionada
            if($rolUsuario == 7){
                $idFranquicia = $request->input('idFranquiciaSeleccionadaDirector');
            }

            //Formato de fecha y hora
            $fechaCita = Carbon::parse($fechaCita)->format('Y-m-d');
            $horaCita = Carbon::parse($horaCita)->format('H:i:s');
            $fechaMinimaAgendarCitaDomicilio = Carbon::parse(self::tiempoAnticipacionCitaDomicilio())->format('Y-m-d');

            //Rango de fechas para agendar citas
            $now = Carbon::now();
            $fechaActual = Carbon::parse($now)->format('Y-m-d');
            $polizaGlobales = new polizaGlobales();
            $numeroDia = $now->dayOfWeekIso;    //Obtenemos el dia de la semana actual
            $fechaLunes = $fechaActual;
            if ($numeroDia != 1) {
                //Dia actual es diferente de lunes
                $fechaLunes = $polizaGlobales::obtenerDia($numeroDia, 1);   //se obtenie la fecha del lunes anterior a la fecha actaul
            }

            $fechaDomingo = date("Y-m-d", strtotime($fechaLunes . "+ 6 days"));

            $fechaHoraActual = Carbon::parse($now)->format('Y-m-d H:i:s');
            $fechaHoraCita = Carbon::parse($fechaCita . " " . $horaCita)->format('Y-m-d H:i:s');
            //Validar que fecha para agendar cita no sea menos a hoy
            if ($fechaHoraActual <= $fechaHoraCita) {
                //Fecha es igual o mayor a hoy

                //Validar si es cita a domicilio que la tenga al menos 72 horas de anticipacion
                if(($rbExamenSucursal == 0) || ($rbExamenSucursal == 1 && $fechaCita >= $fechaMinimaAgendarCitaDomicilio)){
                    //Validar formato de telefono ingresado - formato: 333-333-33-33
                    if (preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{2}-[0-9]{2}$/", $telefono) || preg_match("/^[0-9]{10}$/", $telefono)) {
                        //Verificar si telefono tiene formato Tel:9999999999
                        if(strlen($telefono) == 10){
                            //tiene formato sin guiones - Partir numero y concatenar guiones
                            $telefonoFormato = substr($telefono,0,3).'-'.substr($telefono,3,3).'-'.substr($telefono,6,2).'-'.substr($telefono,8,2);
                            //Remplazar variable telefono por telefono con formato
                            $telefono = $telefonoFormato;
                        }

                        //Verificar que no hay ninguna cita agendada para el horario seleccionado
                        $existeCitaAgendadaSemanaActual = DB::select("SELECT * FROM agendacitas ac WHERE ac.telefono = '$telefono' AND ac.estadocita = '0'
                               AND STR_TO_DATE(ac.fechacitaagendada,'%Y-%m-%d') BETWEEN STR_TO_DATE('$fechaLunes','%Y-%m-%d') AND STR_TO_DATE('$fechaDomingo','%Y-%m-%d')");

                        if ($existeCitaAgendadaSemanaActual == null) {
                            //El paciente no cuenta con una cita agendada para esta semana

                            $existeCitaAgendada = DB::select("SELECT * FROM agendacitas ac WHERE ac.id_franquicia = '$idFranquicia' AND ac.fechacitaagendada = '$fechaCita'
                                                   AND ac.horacitaagendada = '$horaCita' AND ac.estadocita = '0'");

                            if ($existeCitaAgendada == null) {
                                //No existen citas agendadas

                                //Almacenar cita
                                $contratosGlobal = new contratosGlobal();
                                $referenciaCita = $contratosGlobal::generarReferenciaCita($idFranquicia);

                                DB::table('agendacitas')->insert([
                                    'id_franquicia' => $idFranquicia, 'nombre' => $nombre, 'email' => $email,'telefono' => $telefono, 'observaciones' => $observaciones,
                                    'fechacitaagendada' => $fechaCita, 'horacitaagendada' => $horaCita, 'estadocita' => '0',
                                    'localidad' => $localidad, 'colonia' => $colonia, 'domicilio' => $domicilio, 'numero' => $numero,
                                    'entrecalles' => $entrecalles, 'lugarcita' => $lugarcita, 'tipocita' => $rbTipoCita, 'otrotipocita' => $otrotipocita,
                                    'referencia' => $referenciaCita, 'created_at' => Carbon::now()
                                ]);

                                //Registrar numero de referencia en tabla de referencias de tipo cita
                                DB::table('referencias')->insert([
                                    'tipo' => '01', 'referencia' => $referenciaCita, 'created_at' => Carbon::now()
                                ]);

                                //Registrar movimiento en historial sucursal
                                $id_usuario = Auth::id();
                                DB::table('historialsucursal')->insert([
                                    'id_usuarioC' => $id_usuario,
                                    'id_franquicia' => $idFranquicia, 'tipomensaje' => '11',
                                    'created_at' => Carbon::now(),
                                    'cambios' => " Agendó cita con nombre de: '".$nombre."' para dia: '".$fechaCita."' horario: '".$horaCita."'",
                                    'seccion' => '2'
                                ]);

                                //Registrar nueva notificacion a generar alertas multiples
                                DB::table("notificacionesvisualizaciones")->insert([
                                    'fechanotificacion' => $fechaActual,
                                    'numeronotificaciones' => 20,
                                    'tiponotificacion' => '1',
                                    'referencia_cita' => $referenciaCita,
                                    'created_at' => $now
                                ]);

                                return back()->with('bien', "Cita agendada correctamente. Fecha cita: '" . $fechaCita . "' Horario: '" . $horaCita . "'")->withInput($request->only('fechaCita'));

                            } else {
                                //Existe una cita agendada para esa hora
                                return back()->with('alerta', 'Horario seleccionada ya no disponibles, por favor intenta registrar la cita en un nuevo horario.')->withInput($request->all());
                            }

                        } else {
                            return back()->with('alerta', "No puedes agendar una nueva cita dentro de la semana de:  '" . $fechaLunes . "' a '" . $fechaDomingo . "' debido que tienes una cita ya registrada con telefono: '" .$telefono."'")->withInput($request->all());
                        }

                    } else {
                        //Numero de telefono incorrecto
                        return back()->with('alerta', 'Número de teléfono incorrecto.')->withInput($request->all());
                    }

                }else{
                    //Fecha de anticipacion para cita a domicilio no es de 72 horas
                    return back()->with('alerta', 'Para una cita a domicilio requieres agendarla con al menos 72 horas de anticipación. Fecha sugerida para consulta a domicilio: '.$fechaMinimaAgendarCitaDomicilio)->withInput($request->all());
                }
            } else {
                //Fecha menor al dia de hoy
                return back()->with('alerta', 'La fecha para agendar cita debe ser mayor o igual al dia de hoy.')->withInput($request->all());
            }

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
        }
    }

    public function notificarcitapacienteadministracion(Request $request)
    {
        if (Auth::check() && ((Auth::user()->rol_id) == 6 || (Auth::user()->rol_id) == 7 || (Auth::user()->rol_id) == 8)) //ADMINISTRACION, PRINCIPAL, DIRECTOR
        {
            $idFranquicia = $request->input('idFranquicia');
            $indiceSolicitud = $request->input('indiceSolicitud');
            $accion = $request->input('accion');

            $existeSucursal = DB::select("SELECT f.id FROM franquicias f WHERE f.id = '$idFranquicia'");

            if($existeSucursal != null){
                $existeCita = DB::select("SELECT ac.indice, ac.nombre, ac.fechacitaagendada, ac.horacitaagendada FROM agendacitas ac WHERE ac.indice = '$indiceSolicitud' AND ac.id_franquicia = '$idFranquicia'");
                if($existeCita != null){
                    switch ($accion){
                        case "asistencia":
                            $estadoCita = 1;
                            $mensajeMovimiento = " Notificó que el paciente '" . $existeCita[0]->nombre . "' asistió a cita agendada con fecha: '".$existeCita[0]->fechacitaagendada."' hora: '".$existeCita[0]->horacitaagendada."'";
                            break;
                        case "cancelar":
                            $estadoCita = 2;
                            $mensajeMovimiento = " Notificó que el paciente '" .$existeCita[0]->nombre . "' canceló cita agendada con fecha: '".$existeCita[0]->fechacitaagendada."' hora: '".$existeCita[0]->horacitaagendada."'";
                            break;
                    }

                    //Actualizar estado de solicitud
                    DB::table('agendacitas')->where('indice', '=', $indiceSolicitud)->update([
                        'estadocita' => $estadoCita, 'updated_at' => Carbon::now()
                    ]);

                    //Registrar movimiento en historial sucursal
                    $id_usuario = Auth::id();
                    DB::table('historialsucursal')->insert([
                        'id_usuarioC' => $id_usuario,
                        'id_franquicia' => $idFranquicia, 'tipomensaje' => '11',
                        'created_at' => Carbon::now(),
                        'cambios' => $mensajeMovimiento,
                        'seccion' => '2'
                    ]);

                    $bandera = true;
                    $mensaje = "La cita ha sido notificada correctamente. Espera a que se actualice la agenda de citas nuevamente para verificar el cambio.";

                }else{
                    $bandera = false;
                    $mensaje = "No existe la cita dentro de la agenda de la sucursal.";
                }

            }else{
                $bandera = false;
                $mensaje = "No existe la sucursal";
            }

            $response = ['bandera' => $bandera, 'mensaje' => $mensaje];
            return response()->json($response);

        } else {
            if (Auth::check()) {
                return redirect()->route('redireccionar');
            } else {
                return redirect()->route('login');
            }
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
