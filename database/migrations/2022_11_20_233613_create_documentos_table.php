<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->string('documento')->nullable(); 
            $table->date('fecha');
            $table->string('path')->nullable();
            $table->string('remitente')->nullable(); 
            $table->string('dni')->nullable(); 
            $table->string('destino')->nullable(); 
            $table->string('tipo')->nullable(); 
            $table->string('numero_doc')->nullable(); 
            $table->string('tipo_doc')->nullable();  //interno o externo
            $table->string('direccion')->nullable();
            $table->string('referencia')->nullable();
            $table->string('anexo')->nullable();
            $table->string('folio')->nullable();
            $table->boolean('estado')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->integer('prioridad')->nullable();
            $table->bigInteger('oficina_id')->nullable();
            $table->integer('num_corre')->nullable();
            //$table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('documentos');
    }
}
