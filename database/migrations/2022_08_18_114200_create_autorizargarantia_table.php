<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutorizarGarantiaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('autorizaciones', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id_referencia")->nullable(true);
            $table->string("id_contrato")->nullable(true);
            $table->string("fechacreacioncontrato")->nullable(true);
            $table->string("estadocontrato")->nullable(true);
            $table->string("id_franquicia")->nullable(true);
            $table->string("id_usuarioC");
            $table->string("id_mensaje")->nullable(true);
            $table->text("mensaje")->nullable(true);
            $table->text("estatus");
            $table->text("tipo");
            $table->text("valor")->nullable();
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
        Schema::dropIfExists('autorizaciones');
    }

}
