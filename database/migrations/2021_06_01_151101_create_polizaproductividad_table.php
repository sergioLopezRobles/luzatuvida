<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolizaproductividadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polizaproductividad', function (Blueprint $table) {
            $table->id();
            $table->string('id_franquicia');
            $table->string('id_poliza');
            $table->string('id_usuario');
            $table->string('sueldo');
            $table->string('totaleco');
            $table->string('totaljr');
            $table->string('totaldoradouno');
            $table->string('totaldoradodos');
            $table->string('totalplatino');
            $table->string('numeroventas');
            $table->string('productividad');
            $table->string('insumos');
            $table->string('rol')->nullable();
            $table->string('totalpremium')->default("0");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('polizaproductividad');
    }
}
