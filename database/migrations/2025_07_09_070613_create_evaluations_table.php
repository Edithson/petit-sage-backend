<?php

use App\Models\User;
use App\Models\Partie;
use App\Models\Profil;
use App\Models\Thematique;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Thematique::class)->onDelete('cascade');
            $table->foreignIdFor(User::class)->onDelete('cascade');
            $table->foreignIdFor(Profil::class)->nullable()->onDelete('set null');
            $table->foreignIdFor(Partie::class)->onDelete('cascade');
            $table->integer('score');
            $table->time('temps');
            $table->json('question'); //contient les questions ratés
            $table->string('drawing_data'); //dessin
            $table->integer('max_score')->nullable()->default(10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
