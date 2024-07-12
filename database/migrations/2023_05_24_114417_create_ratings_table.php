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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')
            ->references('id')
            ->on('clients')
            ->onDelete('cascade');
            $table->unsignedBigInteger('Freelancer_id');
            $table->foreign('Freelancer_id')
            ->references('id')
            ->on('freelancer')
            ->onDelete('cascade');
            $table->string('Review');
            $table->decimal('number');
            $table->unsignedBigInteger('Project_id');
            $table->foreign('Project_id')
            ->references('id')
            ->on('projects')
            ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
