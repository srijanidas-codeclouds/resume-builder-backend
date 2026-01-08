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
        Schema::create('socials', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('linkedIn')->nullable();
            $table->string('github')->nullable();
            $table->string('portfolio')->nullable();
            $table->string('twitter')->nullable();

            $table->foreignUuid('resume_id')->references('id')->on('resumes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('socials');
    }
};
