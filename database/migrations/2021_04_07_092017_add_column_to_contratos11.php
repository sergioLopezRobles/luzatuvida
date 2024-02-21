<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToContratos11 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->string('enganche')->nullable();
            $table->string('entregaproducto')->nullable();
            $table->string('diaseleccionado')->nullable();
            $table->string('fechaentrega')->nullable();
            $table->string('promocionterminada')->nullable();
            $table->string('poliza')->nullable();
            $table->string('fechasubscripcion')->nullable();
            $table->string('subscripcion')->nullable();
            $table->text('calleentrega')->nullable();
            $table->text('numeroentrega')->nullable();
            $table->text('deptoentrega')->nullable();
            $table->text('alladodeentrega')->nullable();
            $table->text('frenteaentrega')->nullable();
            $table->text('entrecallesentrega')->nullable();
            $table->text('coloniaentrega')->nullable();
            $table->text('localidadentrega')->nullable();
            $table->text('casatipoentrega')->nullable();
            $table->text('casacolorentrega')->nullable();
            $table->text('alias')->nullable();
            $table->text('aprobacionventa')->nullable();
            $table->integer('esperapoliza')->default(0);
            $table->text('polizaoptometrista')->nullable();
            $table->text('fecharechazadoconfirmaciones')->nullable();
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
            $table->dropColumn(['enganche']);
            $table->dropColumn(['entregaproducto']);
            $table->dropColumn(['diaseleccionado']);
            $table->dropColumn(['fechaentrega']);
            $table->dropColumn(['promocionterminada']);
            $table->dropColumn(['poliza']);
        });
    }
}
