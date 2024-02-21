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
        Schema::create('autorizacionarmazonlaboratorio', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('id_autorizacion')->nullable();
            $table->string('id_armazon')->nullable();
            $table->string('piezas')->nullable();
            $table->string('foliopoliza')->nullable();
            $table->text('observaciones')->nullable();
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
        Schema::dropIfExists('autorizacionarmazonlaboratorio');
    }
};
