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
        Schema::create('contratostemporalesatrasosenabonos', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id_franquicia");
            $table->string("id");
            $table->string("estatus_estadocontrato")->nullable();
            $table->string("fechacobroini")->nullable();
            $table->string("fechacobrofin")->nullable();
            $table->string("diapago")->nullable();
            $table->string("formadepago")->nullable();
            $table->string("fechaatraso")->nullable();
            $table->string("diaseleccionado")->nullable();
            $table->string("fechaentrega")->nullable();
            $table->string("fechacobroiniantes")->nullable();
            $table->string("fechacobrofinantes")->nullable();
            $table->string("costoatraso")->nullable();
            $table->string("total")->nullable();
            $table->string("pagosadelantar")->nullable();
            $table->string("abonominimo")->nullable();
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
        Schema::dropIfExists('contratostemporalesatrasosenabonos');
    }
};
