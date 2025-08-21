<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable(); // Permitir null temporariamente
            $table->string('street', 255);
            $table->string('number', 10);
            $table->string('complement', 255)->nullable();
            $table->string('neighborhood', 100);
            $table->string('zip_code', 10);
            $table->string('city', 100);
            $table->string('state', 2);
            $table->timestamps();
            // Chave estrangeira removida; será adicionada em migration separada
        });
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
};