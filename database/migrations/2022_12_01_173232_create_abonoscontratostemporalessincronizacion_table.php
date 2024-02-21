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
        Schema::create('abonoscontratostemporalessincronizacion', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('id_usuariocobrador');
            $table->string('id');
            $table->string('folio')->nullable();
            $table->string("id_contrato")->nullable();
            $table->string("id_usuario")->nullable();
            $table->string("abono")->nullable();
            $table->string("metodopago")->nullable();
            $table->string("adelantos")->nullable();
            $table->string("tipoabono")->nullable();
            $table->string("atraso")->nullable();
            $table->string('corte')->default('2');
            $table->string("coordenadas")->nullable();
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('abonoscontratostemporalessincronizacion');
    }
};
