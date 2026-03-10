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
        Schema::table('questions', function (Blueprint $table) {
            // On retire l'index unique en utilisant son nom exact (trouvé dans ton erreur)
            $table->dropUnique('questions_thematique_id_partie_id_numero_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // On remet la contrainte en cas de rollback (php artisan migrate:rollback)
            $table->unique(['thematique_id', 'partie_id', 'numero'], 'questions_thematique_id_partie_id_numero_unique');
        });
    }
};