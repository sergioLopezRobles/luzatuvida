<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolizaventasdiasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polizaventasdias', function (Blueprint $table) {
            $table->id();
            $table->string('id_franquicia');
            $table->string('id_usuario');
            $table->string('rol');
            $table->string('id_poliza');
            $table->string('fechapoliza');
            $table->string('fechapolizacierre')->nullable();
            $table->string('nombre');
            $table->string('lunes');
            $table->string('martes');
            $table->string('miercoles');
            $table->string('jueves');
            $table->string('viernes');
            $table->string('sabado');
            $table->string('acumuladas');
            $table->string('asistencia')->nullable();
            $table->string('ingresosgotas');
            $table->string('ingresosenganche');
            $table->string('ingresospoliza');
            $table->string('totaldia')->nullable();
            $table->string('ingresosventas');
            $table->string('ingresosventasacumulado');
            $table->string('ingresosabonos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('polizaventasdias');
    }
}
