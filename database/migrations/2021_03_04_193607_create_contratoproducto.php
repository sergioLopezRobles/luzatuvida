<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContratoproducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contratoproducto', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('id','5');
            $table->string("id_contrato");
            $table->string("id_producto");
            $table->string("id_franquicia");
            $table->string("id_usuario");
            $table->string("estadoautorizacion")->default("1")->nullable();
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
        Schema::dropIfExists('contratoproducto');
    }
}
