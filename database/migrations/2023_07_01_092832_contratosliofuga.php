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
        Schema::create('contratosliofuga', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id_contrato");
            $table->string("id_franquicia");
            $table->string("nombre")->nullable();
            $table->string("colonia")->nullable();
            $table->string("calle")->nullable();
            $table->string("numero")->nullable();
            $table->string("telefono")->nullable();
            $table->text("cambios")->nullable();
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
        Schema::dropIfExists('contratosliofuga');
    }
};
