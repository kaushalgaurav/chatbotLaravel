<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('chatbots', function (Blueprint $table) {
            $table->id();
            $table->string('name');                // chatbot name
            $table->unsignedBigInteger('user_id');         // owner
            $table->string('description')->nullable(); // short description
            $table->integer('platform')->nullable();    // e.g., "1 for web", "2 for store"
            $table->string('language')->default('en'); // default language
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes(); // for soft deletes
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('chatbots');
    }
};
