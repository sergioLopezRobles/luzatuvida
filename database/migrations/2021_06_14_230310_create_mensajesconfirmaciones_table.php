<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMensajesconfirmacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mensajesconfirmaciones', function (Blueprint $table) {
            $table->id();
            $table->string('id_contrato');
            $table->string('id_usuario');
            $table->string('comentario');
            $table->timestamp('fecha');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mensajesconfirmaciones');
    }
}
