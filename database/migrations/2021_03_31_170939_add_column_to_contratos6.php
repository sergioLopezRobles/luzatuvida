<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToContratos6 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->string('diapago')->nullable();
            $table->string('fechacobroini')->nullable();
            $table->string('fechacobrofin')->nullable();
            $table->string('fechacobroiniantes')->nullable();
            $table->string('fechacobrofinantes')->nullable();
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
            $table->dropColumn(['diapago']);
            $table->dropColumn(['fechacobroini']);
            $table->dropColumn(['fechacobrofin']);
            $table->dropColumn(['fechacobroiniantes']);
            $table->dropColumn(['fechacobrofinantes']);
        });
    }
}
