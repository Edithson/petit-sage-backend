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
        Schema::create('thematiques', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('description')->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('media_type')->nullable();
            $table->string('media_url')->nullable();
            $table->string('media_description')->nullable();
            $table->string('emoji')->nullable()->default('🎨');
            $table->string('couleur')->nullable()->default('#ff6600ff');
            $table->integer('nbr_min_point')->default(1);
            $table->foreignIdFor(Niveau::class)->nullable()->onDelete('set null');
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
        Schema::dropIfExists('thematiques');
    }
};
