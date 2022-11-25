<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Nullable;

class CreateProcesosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procesos', function (Blueprint $table) {
            $table->id();
            $table->dateTime('recepcion')->nullable();
            $table->dateTime('derivar')->nullable();
            $table->bigInteger('oficina_input')->nullable();
            $table->bigInteger('oficina_ouput')->nullable();
            $table->bigInteger('documento_id')->unsigned();
            $table->boolean('recibido')->nullable();
            $table->string('observacion')->nullable();
            $table->boolean('estado_der')->nullable();
            $table->boolean('estado_rep')->nullable();
            $table->string('tipo')->nullable();
            $table->string('numero')->nullable();
            $table->integer('num_corre')->nullable();
            $table->foreign('documento_id')->references('id')->on('documentos');
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
        Schema::dropIfExists('procesos');
    }
}
