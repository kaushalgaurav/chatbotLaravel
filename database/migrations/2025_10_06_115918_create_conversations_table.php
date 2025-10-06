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
            $table->unsignedBigInteger('user_id'); // reference to users table
            $table->string('session_id')->nullable(); // optional session identifier
            $table->enum('sender', ['user', 'bot']); // who sent the message
            $table->text('message'); // store message content
            $table->timestamps();

            // Optional foreign key if you have a users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
