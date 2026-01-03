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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('item_type'); // 'product' ou 'bundle'
            $table->unsignedBigInteger('item_id'); // ID du produit ou bundle
            $table->string('item_name'); // Nom au moment de l'achat
            $table->decimal('quantity', 10, 2); // Quantité commandée
            $table->integer('unit_price_cents'); // Prix unitaire en centimes
            $table->integer('total_price_cents'); // Prix total en centimes
            $table->timestamps();

            $table->index('order_id');
            $table->index(['item_type', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
