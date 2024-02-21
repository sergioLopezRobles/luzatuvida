<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateControlentradasalidausuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('controlentradasalidausuario', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id_usuario");
            $table->string("horaini");
            $table->string("horafin");
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
        Schema::dropIfExists('controlentradasalidausuario');
    }
}
