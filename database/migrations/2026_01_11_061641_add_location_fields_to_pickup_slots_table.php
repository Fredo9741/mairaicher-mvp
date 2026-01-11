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
        Schema::table('pickup_slots', function (Blueprint $table) {
            // Coordonnées GPS pour la carte (précision GPS standard)
            $table->decimal('lat', 10, 8)->nullable()->after('name');
            $table->decimal('lng', 11, 8)->nullable()->after('lat');

            // Adresse complète du point de retrait
            $table->string('address')->nullable()->after('lng');

            // Horaires d'ouverture par jour (JSON)
            // Format: {"monday": {"open": "09:00", "close": "18:00", "closed": false}, ...}
            $table->json('working_hours')->nullable()->after('address');

            // Suppression des champs start_time et end_time (remplacés par working_hours)
            $table->dropColumn(['start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup_slots', function (Blueprint $table) {
            // Restaure les anciens champs
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // Supprime les nouveaux champs
            $table->dropColumn(['lat', 'lng', 'address', 'working_hours']);
        });
    }
};
