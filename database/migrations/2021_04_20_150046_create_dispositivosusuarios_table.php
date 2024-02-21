<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDispositivosusuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispositivosusuarios', function (Blueprint $table) {
            $table->id();
            $table->string('id_usuario');
            $table->string('versionandroid');
            $table->string('modelo');
            $table->string('identificadorunico');
            $table->string('versiongradle')->nullable();
            $table->string('lenguajetelefono')->nullable();
            $table->string('estatus')->default(0);
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
        Schema::dropIfExists('dispositivosusuarios');
    }
}
