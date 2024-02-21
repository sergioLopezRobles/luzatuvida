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
        Schema::create('contratostemporalescalculototalescontrato', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id");
            $table->string("id_franquicia")->nullable();
            $table->string("estatus_estadocontrato")->nullable();
            $table->string("totalabono")->nullable();
            $table->string("totalproducto")->nullable();
            $table->string("total")->nullable();
            $table->string("promocionterminada")->nullable();
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
        Schema::dropIfExists('contratostemporalescalculototalescontrato');
    }
};
