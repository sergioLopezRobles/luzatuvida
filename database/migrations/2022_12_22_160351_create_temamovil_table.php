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
        Schema::create('temamovil', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('fotologo')->nullable();
            $table->string('coloriconos')->nullable();
            $table->string('colorencabezados')->nullable();
            $table->string('colornavbar')->nullable();
            $table->integer("estadoconfiguracion")->nullable();
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
        Schema::dropIfExists('temamovil');
    }
};
