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
        Schema::create('freelancer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('skill_name')->nullable();
            $table->binary('picture')->nullable();
            $table->binary('cv')->nullable();
            $table->longText('bio')->nullable();
            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');
            $table->integer('Total_Rating')->default(0);
            $table->integer('Total_Rated_times')->default(0);
            $table->enum('Category',['Development & IT','Design & Creative','Sales & Marketing','Writing & Translation','Admin & Customer Support',' Legal','HR & Training','Engineering & Architecture']);
            $table->integer('total_compeleted_jobs')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freelancer');
    }
};
