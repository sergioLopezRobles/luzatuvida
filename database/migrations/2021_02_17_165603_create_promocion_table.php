<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromocionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promocion', function (Blueprint $table) {
            $table->id();
            $table->string("id_franquicia");
            $table->string("titulo");
            $table->string("precioP")->nullable();
            $table->date("inicio");
            $table->date("fin");
            $table->integer("status");
            $table->string("contado")->default(0);
            $table->integer("forzado")->default(0);
            $table->string("tipopromocion")->default(0);
            $table->string("preciouno")->nullable();
            $table->integer("contarventa")->default(0)->nullable();
            $table->integer("tipo")->default(0)->nullable();
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
        Schema::dropIfExists('promocion');
    }
}
