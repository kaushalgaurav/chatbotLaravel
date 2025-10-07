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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('conversation_id'); // unique identifier for each conversation
            $table->string('bot_id', 100); // reference to publications table (varchar)
            $table->string('session_id')->nullable(); // optional session identifier
            $table->enum('sender', ['user', 'bot']); // who sent the message
            $table->text('message'); // store message content
            $table->timestamps();

            // Optional foreign key if publications.id is also string/varchar
            // $table->foreign('bot_id')->references('id')->on('publications')->onDelete('cascade');

            // Index for faster lookups
            $table->index('conversation_id');
            $table->index('session_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
