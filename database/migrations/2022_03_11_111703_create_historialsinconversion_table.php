<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorialsinconversionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historialsinconversion', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id_contrato");
            $table->string("id_historial");
            $table->string("esfericoder")->nullable();
            $table->string("cilindroder")->nullable();
            $table->string("ejeder")->nullable();
            $table->string("addder")->nullable();
            $table->string("esfericoizq")->nullable();
            $table->string("cilindroizq")->nullable();
            $table->string("ejeizq")->nullable();
            $table->string("addizq")->nullable();
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
        Schema::dropIfExists('historialsinconversion');
    }
}
