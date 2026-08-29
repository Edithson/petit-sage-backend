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
        Schema::table('thematiques', function (Blueprint $table) {
            $table->string('methode')->nullable();
            $table->string('technique')->nullable();
            $table->string('origin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thematiques', function (Blueprint $table) {
            $table->dropColumn(['methode', 'technique', 'origin']);
        });
    }
};
