<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorialSucursalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historialsucursal', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id_producto")->nullable(true);
            $table->string("id_usuarioC");
            $table->string("id_franquicia")->nullable(true);
            $table->string("referencia")->nullable(true);
            $table->text("cambios");
            $table->text("seccion");
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
        Schema::dropIfExists('historialsucursal');
    }

}
