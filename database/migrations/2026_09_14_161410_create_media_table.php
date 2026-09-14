<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table): void {
            $table->id();
            $table->string('disk')->default((string) config('media.disk', 'public'));
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->string('extension', 20)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            // `hash` menyimpan sha1 isi file: dipakai untuk mengenali unggahan
            // yang identik supaya file yang sama tidak menumpuk di disk.
            $table->string('hash', 64)->nullable();
            $table->string('alt_text')->nullable();
            $table->string('caption', 500)->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['disk', 'path']);
            $table->index('hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
