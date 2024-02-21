<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorialcontratoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historialcontrato', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('id','5');
            $table->string("id_contrato");
            $table->string("id_usuarioC");
            $table->text("cambios");
            $table->string("tipomensaje")->default(0);
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
        Schema::dropIfExists('historialcontrato');
    }
}
