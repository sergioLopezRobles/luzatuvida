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
        Schema::create('vacantesmensajes', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('id_franquicia')->nullable();
            $table->string('id_usuario')->nullable();
            $table->text('mensaje')->nullable();
            $table->text('respuesta')->nullable();
            $table->text('estadomensaje')->nullable();
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
        Schema::dropIfExists('vacantesmensajes');
    }
};
