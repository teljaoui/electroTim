<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->string('img');
            $table->string('img2');
            $table->string('img3');
            $table->string('img4');
            $table->integer('price');
            $table->string('brand');
            $table->integer('quantities');
            $table->boolean('offer')->nullable()->default(null);
            $table->integer('offerPrice')->nullable()->default(null);
            $table->integer('percentage')->nullable()->default(null);
            $table->date('datefin')->nullable()->default(null);
            $table->boolean('Advertisement')->nullable()->default(null);
            $table->string('Adimg')->nullable()->default(null);
            $table->unsignedBigInteger('categorie_id');
            $table->foreign('categorie_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
