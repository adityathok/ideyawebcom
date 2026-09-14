<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doc_versions', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->string('label');
            $t->string('slug');
            $t->boolean('is_current')->default(false);
            $t->unsignedInteger('sort_order')->default(0);
            $t->date('released_at')->nullable();
            $t->timestamps();
            $t->unique(['product_id', 'slug']);
            $t->index(['product_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doc_versions');
    }
};
