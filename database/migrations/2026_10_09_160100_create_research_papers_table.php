<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('research_papers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('authors');
            $table->text('abstract');
            $table->string('category', 40)->index();
            $table->string('keywords')->nullable();
            $table->unsignedSmallInteger('publication_year')->nullable()->index();
            $table->string('publication_status', 30)->default('preprint')->index();
            $table->string('doi')->nullable();
            $table->string('external_url')->nullable();
            $table->text('citation')->nullable();
            $table->text('supplementary')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('review_status', 30)->default('pending')->index();
            $table->boolean('is_published')->default(false)->index();
            $table->text('review_note')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_papers');
    }
};
