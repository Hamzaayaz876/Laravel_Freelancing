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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')
            ->references('id')
            ->on('clients')
            ->onDelete('cascade');
            $table->string('Title');
            $table->string('Description');
            $table->enum('Level', ['Beginner', 'Intermediate','Expert'])->default('Intermediate');
            $table->integer('Comments_Number')->default(0);
            $table->integer('Applications_Number')->default(0);
            $table->enum('State', ['Pending', 'Inprogress','Done'])->default('Pending');
            $table->integer('Budget');
            $table->string('skill_name');
            $table->enum('Category',['Development & IT','Design & Creative','Sales & Marketing','Writing & Translation','Admin & Customer Support',' Legal','HR & Training','Engineering & Architecture']);
            $table->dateTime('Application_Dealine')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
