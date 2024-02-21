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
        Schema::create('contratosabonoscontratostemporalessincronizacion', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id_contrato")->nullable();
            $table->string("id_usuario")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contratosabonoscontratostemporalessincronizacion');
    }
};
