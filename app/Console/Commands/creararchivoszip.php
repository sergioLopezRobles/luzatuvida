<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use ZipArchive;
use Extractor;

class creararchivoszip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'creararchivoszip:usuario';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comprimira todos los archivos referentes a cada uno de los usuarios registrados en la base de datos y generara un archivo zip';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $usuarios = DB::select("SELECT * FROM users");

            foreach ($usuarios as $usuario){
                //Recorremos los usuarios para verificar sus archivos

                $archivosComprimir = [];

                if($usuario->actanacimiento != null && str_contains($usuario->actanacimiento,'uploads/imagenes/usuarios/actanacimiento/')){
                    array_push($archivosComprimir,$usuario->actanacimiento);
                }if($usuario->identificacion != null && str_contains($usuario->identificacion,'uploads/imagenes/usuarios/identificacion/')){
                    array_push($archivosComprimir,$usuario->identificacion);
                }if($usuario->curp != null && str_contains($usuario->curp,'uploads/imagenes/usuarios/curp/')){
                    array_push($archivosComprimir,$usuario->curp);
                }if($usuario->comprobantedomicilio != null && str_contains($usuario->comprobantedomicilio,'uploads/imagenes/usuarios/comprobante/')){
                    array_push($archivosComprimir,$usuario->comprobantedomicilio);
                }if($usuario->segurosocial != null && str_contains($usuario->segurosocial,'uploads/imagenes/usuarios/seguro/')){
                    array_push($archivosComprimir,$usuario->segurosocial);
                }if($usuario->solicitud != null && str_contains($usuario->solicitud,'uploads/imagenes/usuarios/solicitud/')){
                    array_push($archivosComprimir,$usuario->solicitud);
                }if($usuario->tarjetapago != null && str_contains($usuario->tarjetapago,'uploads/imagenes/usuarios/tarjetapago/')){
                    array_push($archivosComprimir,$usuario->tarjetapago);
                }if($usuario->contactoemergencia != null && str_contains($usuario->contactoemergencia,'uploads/imagenes/usuarios/contactoemergencia/')){
                    array_push($archivosComprimir,$usuario->contactoemergencia);
                }if($usuario->contratolaboral != null && str_contains($usuario->contratolaboral,'uploads/imagenes/usuarios/contratolaboral/')){
                    array_push($archivosComprimir,$usuario->contratolaboral);
                }if($usuario->pagare != null && str_contains($usuario->pagare,'uploads/imagenes/usuarios/pagare/')){
                    array_push($archivosComprimir,$usuario->pagare);
                }if($usuario->otratarjetapago != null && str_contains($usuario->otratarjetapago,'uploads/imagenes/usuarios/otratarjetapago/')){
                    array_push($archivosComprimir,$usuario->otratarjetapago);
                }

                if(sizeof($archivosComprimir) > 0){
                    //Si al menos se tiene un archivo a comprimir

                    $idUsuario = $usuario -> id;
                    //Creamos el nuevo archivo de tipo ZIP
                    $zip = new ZipArchive();
                    // Abrimos el archivo ZIP
                    $zip->open(config('filesystems.disks.disco.root') .'/uploads/imagenes/usuarios/zip/'.'Archivos-Usuario'. '-' .$idUsuario. '.zip', ZipArchive::CREATE);
                    // correspondiente al usuario

                    //Recorremos todos los archivos encontrados a comprimir
                    foreach ($archivosComprimir as $archivoComprimir){
                        $nombreArchivo = explode("/", $archivoComprimir);

                        if(file_exists(config('filesystems.disks.disco.root') . '/'.$archivoComprimir)){
                            //Si existe el archivo lo ingresa en el zip
                            $zip->addFile(config('filesystems.disks.disco.root') . '/'.$archivoComprimir, ''.$nombreArchivo[4]);
                        }
                    }

                    $zip->close(); //Cerrar el archivo zip
                }
            }
        }catch(\Exception $e){
            \Log::info("Error: ".$e);
        }

        \Log::info("Los archivos zip fueron creados correctamente.");
    }
}
