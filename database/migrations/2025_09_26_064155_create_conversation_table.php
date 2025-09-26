<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversation', function (Blueprint $table) {
            $table->id(); // id INT AUTO_INCREMENT PRIMARY KEY
            $table->string('client_id');
            $table->string('conv_id');
            $table->string('session_id');
            $table->enum('sender', ['user', 'bot']);
            $table->text('message');
            $table->timestamp('created_at')->useCurrent();

            // If you want updated_at too, you can add:
            // $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation');
    }
};
