<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigfranquiciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configfranquicia', function (Blueprint $table) {
            $table->id();
            $table->string('id_franquicia');
            $table->integer('estado');
            $table->string('retardoIni')->default("08:11:00");
            $table->string('retardoFin')->default("08:15:00");
            $table->string('retardoAsistenteIni')->default("08:41:00");
            $table->string('retardoAsistenteFin')->default("08:45:00");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configfranquicia');
    }
}
