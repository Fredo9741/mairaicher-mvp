<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pour SQLite, on doit recréer la table complète avec la nouvelle structure
        // car SQLite ne supporte pas ALTER COLUMN pour les ENUM

        // Étape 1: Créer une table temporaire avec la nouvelle structure
        Schema::create('products_new', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('price_cents');
            $table->enum('unit', ['kg', 'piece'])->default('piece');
            $table->decimal('stock', 10, 2)->default(0);
            $table->string('image')->nullable();
            $table->enum('category', ['legume', 'fruit', 'volaille', 'epicerie'])->default('epicerie');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category');
            $table->index('is_active');
        });

        // Étape 2: Copier les données en transformant 'autre' en 'epicerie'
        DB::statement("
            INSERT INTO products_new (id, name, slug, description, price_cents, unit, stock, image, category, is_active, created_at, updated_at)
            SELECT id, name, slug, description, price_cents, unit, stock, image,
                   CASE WHEN category = 'autre' THEN 'epicerie' ELSE category END,
                   is_active, created_at, updated_at
            FROM products
        ");

        // Étape 3: Supprimer l'ancienne table et renommer la nouvelle
        Schema::dropIfExists('products');
        Schema::rename('products_new', 'products');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pour le rollback, on refait la même opération en sens inverse
        Schema::create('products_old', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('price_cents');
            $table->enum('unit', ['kg', 'piece'])->default('piece');
            $table->decimal('stock', 10, 2)->default(0);
            $table->string('image')->nullable();
            $table->enum('category', ['legume', 'volaille', 'autre'])->default('autre');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category');
            $table->index('is_active');
        });

        DB::statement("
            INSERT INTO products_old (id, name, slug, description, price_cents, unit, stock, image, category, is_active, created_at, updated_at)
            SELECT id, name, slug, description, price_cents, unit, stock, image,
                   CASE WHEN category = 'epicerie' THEN 'autre' ELSE category END,
                   is_active, created_at, updated_at
            FROM products
        ");

        Schema::dropIfExists('products');
        Schema::rename('products_old', 'products');
    }
};
