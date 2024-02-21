<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGastosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->string('id_poliza')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('factura')->nullable();
            $table->string('foto')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('monto')->nullable();
            $table->string('tipogasto')->nullable();
            $table->string('id_usuario')->nullable();
            $table->string('pertenencia')->nullable();
            $table->integer('salieron')->nullable();
            $table->string('id_tipocobranza')->nullable();
            $table->string('id_zona')->nullable();
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
        Schema::dropIfExists('gastos');
    }
}
