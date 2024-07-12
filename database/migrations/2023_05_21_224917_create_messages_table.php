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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->foreign('conversation_id')
            ->references('id')
            ->on('conversations')
            ->onDelete('cascade');
            $table->longText('message');
            $table->unsignedBigInteger('sender');
            $table->unsignedBigInteger('reciever');
            $table->enum('State', ['unseen','seen'])->default('unseen');
            $table->enum('Status', ['Available','DeletedForSender','DeletedForReciever'])->default('Available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
