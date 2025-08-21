<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_variation_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('variation_id');
            $table->string('path', 255);
            $table->timestamps();

            $table->foreign('variation_id')->references('id')->on('product_variations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_variation_images');
    }
};