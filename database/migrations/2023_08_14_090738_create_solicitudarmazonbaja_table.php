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
        Schema::create('solicitudarmazonbaja', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('id_autorizacion');
            $table->string('id_armazon');
            $table->string('fotofrente');
            $table->string('fotoatras');
            $table->string('fotolado1');
            $table->string('fotolado2');
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
        Schema::dropIfExists('solicitudarmazonbaja');
    }
};
