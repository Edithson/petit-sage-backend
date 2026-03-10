<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Niveau;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profils', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('sexe')->nullable();
            $table->integer('age')->nullable();
            $table->string('profil')->nullable()->default('/storage/profil/profil.png');
            $table->foreignIdFor(User::class)->onDelete('cascade');
            $table->foreignIdFor(Niveau::class)->nullable()->onDelete('set null');
            $table->tinyText('password');
            $table->boolean('is_active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profils');
    }
};
