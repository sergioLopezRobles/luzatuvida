<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiculosTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id_franquicia");
            $table->string("numserie")->nullable();
            $table->string("marca")->nullable();
            $table->integer("cilindros")->nullable();
            $table->string("linea")->nullable();
            $table->string("modelo")->nullable();
            $table->string("clase")->nullable();
            $table->string("tipo")->nullable();
            $table->string("capacidad")->nullable();
            $table->string("nummotor")->nullable();
            $table->string("placas")->nullable()->default("000-00-00");
            $table->string("numeropoliza")->nullable();
            $table->string("vigenciapoliza")->nullable();
            $table->string("id_tipovehiculo")->nullable();
            $table->string("identificador")->nullable();
            $table->string("estado")->nullable();
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
        Schema::dropIfExists('vehiculos');
    }

}
