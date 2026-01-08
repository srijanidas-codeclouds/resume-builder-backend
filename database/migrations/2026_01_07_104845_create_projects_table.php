<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // $table->uuid('resume_id');

            $table->string('name');
            $table->text('description');
            $table->json('tech_stack');

            $table->string('live_link')->nullable();
            $table->string('github_link')->nullable();

            $table->date('start_date');
            $table->date('end_date')->nullable();

            // $table->timestampsTz();

            $table->foreignUuid('resume_id')
                ->references('id')->on('resumes')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
