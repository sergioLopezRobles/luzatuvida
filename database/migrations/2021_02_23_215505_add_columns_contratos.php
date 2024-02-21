<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsContratos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contratos', function (Blueprint $table) {
        $table->integer('id_optometrista')->nullable();
        $table->string('tarjeta')->nullable();
        $table->integer('pago')->nullable();
        $table->integer('abonominimo')->nullable();
        $table->integer('id_promocion')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contratos', function (Blueprint $table) {
        $table->dropColumn(['id_optometrista']);
        $table->dropColumn(['tarjeta']);
        $table->dropColumn(['pago']);
        $table->integer('abonominimo')->nullable();
        $table->dropColumn(['id_promocion']);
    });
    }
}
