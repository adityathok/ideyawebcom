<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doc_pages', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->foreignId('version_id')->constrained('doc_versions')->cascadeOnDelete();
            $t->foreignId('parent_id')->nullable()->constrained('doc_pages')->nullOnDelete();
            $t->string('title');
            $t->string('slug');
            $t->text('excerpt')->nullable();
            $t->mediumText('body');
            $t->string('status')->default('draft');
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamp('published_at')->nullable();
            $t->unsignedInteger('view_count')->default(0);
            $t->softDeletes();
            $t->timestamps();
            $t->unique(['version_id', 'slug']);
            $t->index(['product_id', 'status']);
            $t->index(['version_id', 'parent_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doc_pages');
    }
};
