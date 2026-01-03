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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('price_cents'); // Prix en centimes
            $table->enum('unit', ['kg', 'piece'])->default('piece'); // Unité de vente
            $table->decimal('stock', 10, 2)->default(0); // Stock disponible
            $table->string('image')->nullable(); // Chemin de l'image
            $table->enum('category', ['legume', 'volaille', 'autre'])->default('autre');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
