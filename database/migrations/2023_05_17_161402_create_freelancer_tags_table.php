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
        Schema::create('freelancer_tags', function (Blueprint $table) {
            $table->id();
            $table->string("Tag_name");
            $table->unsignedBigInteger('Freelancer_id');
            $table->foreign('Freelancer_id')
            ->references('id')
            ->on('freelancer')
            ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freelancer_tags');
    }
};
