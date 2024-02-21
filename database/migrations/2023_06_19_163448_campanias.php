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
        Schema::create('campanias', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id")->nullable();
            $table->string("titulo")->nullable();
            $table->string("observaciones")->nullable();
            $table->string("fechainicio")->nullable();
            $table->string("fechafinal")->nullable();
            $table->string("foto")->nullable();
            $table->string("estado")->nullable();
            $table->string("tiporeferencia")->nullable();
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
        Schema::dropIfExists('campanias');
    }
};
