<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('chatbot_publication_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('publication_id'); // link to chatbot_publications.id
            $table->json('old_payload');
            $table->json('new_payload');
            $table->unsignedInteger('version');
            $table->unsignedBigInteger('changed_by'); // optional: who changed it
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('chatbot_publication_histories');
    }
};
