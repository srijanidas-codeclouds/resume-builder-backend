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
    Schema::table('resumes', function (Blueprint $table) {
        $table->string('title')->nullable()->change();
        $table->text('summary')->nullable()->change();
        $table->json('skills')->nullable()->change();
        $table->json('languages')->nullable()->change();
        $table->string('accent_color')->nullable()->change();
        $table->string('template')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('resumes', function (Blueprint $table) {
        $table->string('title')->nullable(false)->change();
        $table->text('summary')->nullable(false)->change();
        $table->json('skills')->nullable(false)->change();
        $table->json('languages')->nullable(false)->change();
        $table->string('accent_color')->nullable(false)->change();
        $table->string('template')->nullable(false)->change();
    });
}

};