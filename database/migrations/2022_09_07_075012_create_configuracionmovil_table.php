<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfiguracionMovilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuracionmovil', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('ruta_ftp')->nullable();
            $table->string('usuario_ftp')->nullable();
            $table->string('contrasena_ftp')->nullable();
            $table->string('preciodolar')->nullable();
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
        Schema::dropIfExists('configuracionmovil');
    }
}
