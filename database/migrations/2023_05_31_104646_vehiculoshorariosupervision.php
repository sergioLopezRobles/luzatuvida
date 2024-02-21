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
        Schema::create('vehiculoshorariosupervision', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id_franquicia");
            $table->string("horalimitechoferfoto1")->nullable()->default("09:00");
            $table->string("horalimitechoferfoto2")->nullable()->default("23:59");
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
        Schema::dropIfExists('vehiculoshorariosupervision');
    }
};
