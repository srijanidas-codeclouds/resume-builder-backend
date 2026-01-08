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
        Schema::create('resumes', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Ownership
            // $table->uuid('user_id');

            // Core resume data
            $table->string('title');
            $table->text('summary');

            // Flexible sections
            $table->json('skills');
            $table->json('languages');

            // UI / template settings
            $table->string('accent_color');
            $table->string('template')->default('default');

            $table->timestampsTz();

            // Foreign key constraint
            $table->foreignUuid('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resumes');
    }
};
