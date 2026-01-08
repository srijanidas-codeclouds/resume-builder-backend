<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // $table->uuid('resume_id')->unique();

            $table->string('full_name');
            $table->string('designation');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('location')->nullable();

            // Social links
            // $table->string('linkedin')->nullable();
            // $table->string('github')->nullable();
            // $table->string('portfolio')->nullable();
            // $table->string('twitter')->nullable();
            $table->timestampsTz();

            $table->foreignUuid('resume_id')
                ->references('id')->on('resumes')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_details');
    }
};

