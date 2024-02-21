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
        Schema::create('historialfotoscontrato', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id");
            $table->string("id_contrato");
            $table->string("id_usuarioC")->nullable();
            $table->string("foto")->nullable();
            $table->text("observaciones")->nullable();
            $table->string("tipomensaje")->nullable();
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
        Schema::dropIfExists('historialfotoscontrato');
    }
};
