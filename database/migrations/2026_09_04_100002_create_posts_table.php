<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $t->string('title');
            $t->string('slug')->unique();
            $t->text('excerpt')->nullable();
            $t->mediumText('body');
            $t->string('cover_image')->nullable();
            $t->string('status')->default('draft');
            $t->timestamp('published_at')->nullable();
            $t->unsignedInteger('view_count')->default(0);
            $t->softDeletes();
            $t->timestamps();
            $t->index('slug');
            $t->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
