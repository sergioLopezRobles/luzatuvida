<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngresosoficinaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingresosoficina', function (Blueprint $table) {
            $table->id();
            $table->string('id_poliza')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('numrecibo')->nullable();
            $table->string('foto')->nullable();
            $table->string('monto')->nullable();
            $table->string('id_producto')->nullable();
            $table->string('piezas')->nullable();
            $table->string('id_gasto')->nullable();
            $table->string('id_usuario')->nullable();
            $table->string('tipo')->default("0");
            $table->string('id_zona')->nullable();
            $table->string('indiceabono')->nullable();
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
        Schema::dropIfExists('ingresosoficina');
    }
}
