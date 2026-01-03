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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // Numéro de commande unique
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->integer('total_price_cents'); // Prix total en centimes
            $table->date('pickup_date'); // Date de retrait
            $table->foreignId('pickup_slot_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['pending', 'paid', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->string('stripe_payment_intent_id')->nullable(); // ID Stripe
            $table->text('notes')->nullable(); // Notes internes
            $table->timestamps();

            $table->index('order_number');
            $table->index('status');
            $table->index('pickup_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
