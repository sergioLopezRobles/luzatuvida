<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolizaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poliza', function (Blueprint $table) {
            $table->id();
            $table->string('id_franquicia')->nullable();
            $table->string('ingresosadmin')->default("0");
            $table->string('ingresosventas')->default("0");
            $table->string('ingresoscobranza')->default("0");
            $table->string('gastosadmin')->default("0");
            $table->string('gastosventas')->default("0");
            $table->string('gastoscobranza')->default("0");
            $table->string('otrosgastos')->default("0");
            $table->string('realizo')->nullable();
            $table->string('autorizo')->nullable();
            $table->string('total')->default("0");
            $table->string('estatus')->nullable();
            $table->string('observaciones')->nullable();
            $table->string('polizafechaterminada')->nullable();
            $table->string('totalcontratosasistentecomision1')->nullable();
            $table->string('valorasistentecomision1')->nullable();
            $table->string('totalcontratosasistentecomision2')->nullable();
            $table->string('valorasistentecomision2')->nullable();
            $table->string('totalcontratosoptometristacomision1')->nullable();
            $table->string('valoroptometristacomision1')->nullable();
            $table->string('totalcontratosoptometristacomision2')->nullable();
            $table->string('valoroptometristacomision2')->nullable();
            $table->string('totalcontratosoptometristacomision3')->nullable();
            $table->string('valoroptometristacomision3')->nullable();
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
        Schema::dropIfExists('poliza');
    }
}
