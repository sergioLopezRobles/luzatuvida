<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notificacionesvisualizaciones', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("fechanotificacion")->nullable();
            $table->integer("numeronotificaciones")->nullable();
            $table->string("tiponotificacion")->nullable();
            $table->string("id_producto")->nullable();
            $table->string("referencia_cita")->nullable();
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
        Schema::dropIfExists('notificacionesvisualizaciones');
    }
};
