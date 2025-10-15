<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chatbot_id');
            $table->string('product_name')->nullable();
            $table->string('product_unique_id');
            $table->string('product_image')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 14, 4)->nullable();
            $table->string('tags')->nullable();
            $table->string('product_link')->nullable();
            $table->timestamps();

            $table->index(['chatbot_id', 'product_unique_id']);
            // Optionally enforce uniqueness per chatbot:
            $table->unique(['chatbot_id', 'product_unique_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('products');
    }
};
