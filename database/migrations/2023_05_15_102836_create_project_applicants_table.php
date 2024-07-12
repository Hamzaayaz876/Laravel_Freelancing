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
        Schema::create('project_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Freelancer_id');
            $table->foreign('Freelancer_id')
            ->references('id')
            ->on('freelancer')
            ->onDelete('cascade');
            $table->unsignedBigInteger('Project_id');
            $table->foreign('Project_id')
            ->references('id')
            ->on('projects')
            ->onDelete('cascade');
            $table->string('Cover_Letter');
            $table->enum('State', ['Rejected', 'Pending','Accepted','Successfull'])->default('Pending');
            //$table->enum('Category',['Development & IT','Design & Creative','Sales & Marketing','Writing & Translation','Admin & Customer Support',' Legal','HR & Training','Engineering & Architecture']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_applicants');
    }
};
