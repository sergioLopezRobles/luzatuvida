<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToContratosTotales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->string('totalhistorial')->nullable();
            $table->string('totalpromocion')->nullable();
            $table->string('totalproducto')->nullable();
            $table->string('totalabono')->nullable();
            $table->string('totalreal')->nullable();
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
            $table->dropColumn(['totalhistorial']);
            $table->dropColumn(['totalpromocion']);
            $table->dropColumn(['totalproducto']);
            $table->dropColumn(['totalabono']);
        });
    }
}
