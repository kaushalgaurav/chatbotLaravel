<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('upload_jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('upload_uuid')->unique();
            $table->unsignedBigInteger('chatbot_id');
            $table->string('file_record_id')->nullable(); // store ChatbotFile id or multiple ids as csv
            $table->integer('total_rows')->default(0);
            $table->integer('processed_rows')->default(0);
            $table->integer('inserted')->default(0);
            $table->integer('updated')->default(0);
            $table->string('status')->default('queued'); // queued, processing, done, failed
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('upload_jobs');
    }
};
