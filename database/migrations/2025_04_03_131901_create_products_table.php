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
            $table->string('product_name', 255);
            $table->string('description', 255);
            $table->decimal('price', 10, 2);
            $table->integer('stock_quantity');
            $table->tinyInteger('status')->default(1);
            $table->string('main_image_url', 255)->nullable();
            $table->json('collection_image_url')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->timestamps();
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
