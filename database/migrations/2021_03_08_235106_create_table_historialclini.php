<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableHistorialclini extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historialclinico', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('id','5');
            $table->string('id_contrato');
            $table->string('edad');
            $table->date('fechaentrega');
            $table->text('diagnostico');
            $table->string('ocupacion')->nullable();
            $table->string('diabetes')->nullable();
            $table->string('hipertension');
            $table->boolean('dolor')->default(0);
            $table->boolean('ardor')->default(0);
            $table->boolean('golpeojos')->default(0);
            $table->boolean('otroM')->default(0);
            $table->string('molestiaotro')->nullable();
            $table->date('ultimoexamen')->nullable();
            $table->string('esfericoder')->nullable();
            $table->string('cilindroder')->nullable();
            $table->string('ejeder')->nullable();
            $table->string('addder')->nullable();
            $table->string('altder')->nullable();
            $table->string('esfericoizq')->nullable();
            $table->string('cilindroizq')->nullable();
            $table->string('ejeizq')->nullable();
            $table->string('addizq')->nullable();
            $table->string('altizq')->nullable();
            $table->string('id_producto')->nullable();
            $table->integer('id_paquete')->nullable();
            $table->string('material')->nullable();
            $table->string('materialotro')->nullable();
            $table->string('costomaterial')->nullable();
            $table->string('bifocal')->nullable();
            $table->integer('fotocromatico')->nullable();
            $table->integer('ar')->nullable();
            $table->integer('tinte')->nullable();
            $table->integer('blueray')->nullable();
            $table->integer('otroT')->nullable();
            $table->string('tratamientootro')->nullable();
            $table->string('costotratamiento')->nullable();
            $table->string('observaciones')->nullable();
            $table->string('observacionesinterno')->nullable();
            $table->string('tipo')->default(0);
            $table->string('bifocalotro')->nullable();
            $table->string('costobifocal')->nullable();
            $table->string('embarazada')->nullable();
            $table->string('durmioseisochohoras')->nullable();
            $table->string('actividaddia')->nullable();
            $table->string('problemasojos')->nullable();
            $table->string('policarbonatotipo')->nullable();
            $table->string('id_tratamientocolortinte')->nullable();
            $table->string('estilotinte')->nullable();
            $table->string('polarizado')->nullable();
            $table->string('id_tratamientocolorpolarizado')->nullable();
            $table->string('espejo')->nullable();
            $table->string('id_tratamientocolorespejo')->nullable();
            $table->string('fotoarmazon')->nullable();
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
        Schema::dropIfExists('historialclinico');
    }
}
