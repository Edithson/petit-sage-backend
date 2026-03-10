<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('niveaux', function (Blueprint $table) {
            $table->id();
            $table->integer('numero');
            $table->string('nom');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('niveaux');
    }
};
