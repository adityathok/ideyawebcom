<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doc_feedback', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('doc_page_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->boolean('helpful');
            $t->text('comment')->nullable();
            $t->string('visitor_hash', 64);
            $t->timestamps();
            $t->unique(['doc_page_id', 'visitor_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doc_feedback');
    }
};
