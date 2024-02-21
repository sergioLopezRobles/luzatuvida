<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producto', function (Blueprint $table) {
            $table->string('id','5');
            $table->string('id_franquicia')->nullable();
            $table->string('id_tipoproducto')->default('1');
            $table->string('nombre');
            $table->string('piezas');
            $table->string('precio')->nullable();
            $table->string('foto')->nullable();
            $table->string('color');
            $table->boolean('estado')->default(1);
            $table->date('iniciop')->nullable();
            $table->date('finp')->nullable();
            $table->string('preciop')->nullable();
            $table->boolean('activo')->nullable();
            $table->integer('forzado')->default(0);
            $table->string('totalpiezas');
            $table->string('polizagastos')->default('0');
            $table->string('polizagastosadministracion')->default('0');
            $table->string('polizagastoscobranza')->default('0');
            $table->string('premium')->default('0');
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
        Schema::dropIfExists('producto');
    }
}
