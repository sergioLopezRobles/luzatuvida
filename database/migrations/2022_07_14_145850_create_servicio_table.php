<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicioTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicio', function (Blueprint $table) {
            $table->increments("indice");
            $table->string("id_moto")->nullable();
            $table->string("id_vehiculo")->nullable();
            $table->integer("kilometraje");
            $table->integer("siguientekilometraje")->nullable();
            $table->string("ultimoservicio")->nullable();
            $table->string("siguienteservicio")->nullable();
            $table->string("descripcion")->nullable();
            $table->string("factura")->nullable();
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
        Schema::dropIfExists('servicio');
    }

}
