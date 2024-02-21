<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateFranquiciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('franquicias', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('id')->unique();
            $table->string('creadopor');
            $table->string('actualizadopor')->nullable();
            $table->string('foto')->nullable();
            $table->string('curp')->nullable();
            $table->string('rfc')->nullable();
            $table->string('hacienda')->nullable();
            $table->string('actanacimiento')->nullable();
            $table->string('identificacion')->nullable();
            $table->string('estado')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('colonia')->nullable();
            $table->string('calle')->nullable();
            $table->string('entrecalles')->nullable();
            $table->string('numero')->nullable();
            $table->string('telefonofranquicia');
            $table->string('comprobante')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('telefonoatencionclientes')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('horaatencioninicio')->default('08:00')->nullable();
            $table->string('horaatencionfin')->default('17:00')->nullable();
            $table->string('coordenadas')->nullable();
            $table->integer('indicecontratos')->default(0)->nullable();
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
        Schema::dropIfExists('franquicias');
    }
}
