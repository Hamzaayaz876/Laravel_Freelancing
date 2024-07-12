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
        Schema::create('money_handlers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Freelancer_id')->nullable();
            $table->foreign('Freelancer_id')
            ->references('id')
            ->on('freelancer')
            ->onDelete('cascade');
            $table->unsignedBigInteger('Project_id');
            $table->foreign('Project_id')
            ->references('id')
            ->on('projects')
            ->onDelete('cascade');
            $table->decimal('amountOfMoney');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_handlers');
    }
};
