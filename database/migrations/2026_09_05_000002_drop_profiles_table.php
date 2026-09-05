<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('profiles');
    }

    public function down(): void
    {
        Schema::create('profiles', function (Blueprint $t): void {
            $t->id();
            $t->string('company_name');
            $t->string('tagline')->nullable();
            $t->text('about');
            $t->string('email');
            $t->string('phone')->nullable();
            $t->text('address')->nullable();
            $t->json('social')->nullable();
            $t->string('logo')->nullable();
            $t->timestamps();
        });
    }
};
