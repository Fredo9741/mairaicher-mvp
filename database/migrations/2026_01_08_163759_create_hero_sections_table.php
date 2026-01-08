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
        Schema::create('hero_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Domaine des Papangues');
            $table->string('subtitle')->default('Domaine des Papangues : agriculture, élevage biologique et traditionnel');
            $table->string('badge_text')->default('Production locale & Agriculture durable');
            $table->string('image')->nullable(); // URL R2 de l'image
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_sections');
    }
};
