<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Thematique;
use App\Models\Partie;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('intitule_text')->nullable(); // Contenu texte de l'intitulé
            $table->string('intitule_media_url')->nullable(); // URL du média de l'intitulé (local ou externe)
            $table->text('intitule_media_description')->nullable(); // Description du média de l'intitulé
            $table->foreignIdFor(Thematique::class)->onDelete('cascade');
            $table->foreignIdFor(Partie::class)->onDelete('cascade');
            $table->string('degre_difficulte');
            $table->string('type_reponse'); // 'unique' ou 'multiple'
            $table->text('indice')->nullable();
            $table->text('explication')->nullable();
            $table->json('reponses'); // Tableau d'objets des réponses (stocké en JSON)
            $table->integer('numero')->default(1); // Numéro d'ordre de la question
            $table->foreignIdFor(User::class, 'created_by')->nullable()->onDelete('set null');
            $table->foreignIdFor(User::class, 'last_updated_by')->nullable()->onDelete('set null');
            $table->foreignIdFor(User::class, 'deleted_by')->nullable()->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
