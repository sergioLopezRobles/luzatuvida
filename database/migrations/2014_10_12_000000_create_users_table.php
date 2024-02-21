<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('rol_id');
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('foto')->nullable();
            $table->string('actanacimiento')->nullable();
            $table->string('identificacion')->nullable();
            $table->string('curp')->nullable();
            $table->string('comprobantedomicilio')->nullable();
            $table->string('segurosocial')->nullable();
            $table->string('solicitud')->nullable();
            $table->string('tarjetapago')->nullable();
            $table->string('otratarjetapago')->nullable();
            $table->string('contratolaboral')->nullable();
            $table->string('contactoemergencia')->nullable();
            $table->integer('id_zona')->nullable();
            $table->string('sueldo')->nullable()->default(0);
            $table->string("logueado")->default("0");
            $table->string("codigoasistencia");
            $table->string("renovacion")->nullable();
            $table->string("fechaeliminacion")->nullable();
            $table->string("pagare")->nullable();
            $table->string("tarjeta")->nullable();
            $table->string("otratarjeta")->nullable();
            $table->string("supervisorcobranza")->nullable();
            $table->string("id_franquiciaprincipal")->nullable();
            $table->date("fechanacimiento")->nullable();
            $table->string("estatus")->default("1");
            $table->dateTime("ultimaconexion")->nullable();
            $table->string("barcode")->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
