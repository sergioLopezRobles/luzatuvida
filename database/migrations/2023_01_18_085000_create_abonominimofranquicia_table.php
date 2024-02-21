<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbonoMinimoFranquiciaTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abonominimofranquicia', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('id_franquicia')->nullable();
            $table->integer('pago')->nullable();
            $table->integer('abonominimo')->nullable();
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
        Schema::dropIfExists('abonominimofranquicia');
    }
}
