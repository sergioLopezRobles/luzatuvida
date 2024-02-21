<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaniasagendadas', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id_campania");
            $table->string("nombre")->nullable();
            $table->string("telefono")->nullable();
            $table->string("referencia")->nullable();
            $table->string("estado")->nullable()->default("0");
            $table->string("observaciones")->nullable();
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
        Schema::dropIfExists('campaniasagendadas');
    }
};
