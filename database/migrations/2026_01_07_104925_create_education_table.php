<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('education', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // $table->uuid('resume_id');

            $table->string('institution');
            $table->string('degree');
            $table->string('field')->nullable();
            $table->string('grade')->nullable();

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
        Schema::dropIfExists('education');
    }
};
