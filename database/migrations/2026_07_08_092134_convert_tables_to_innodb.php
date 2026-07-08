<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Si la base de données est SQLite ou PostgreSQL, la conversion de moteur n'est pas nécessaire.
        if (in_array(DB::getDriverName(), ['sqlite', 'pgsql'])) {
            return;
        }

        $database = env('DB_DATABASE', 'ludophylosophie');
        
        // Récupérer toutes les tables MyISAM de la base de données active
        $tables = DB::select("
            SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = ? 
            AND ENGINE = 'MyISAM'
        ", [$database]);

        foreach ($tables as $table) {
            $tableName = $table->TABLE_NAME;
            // Altérer chaque table vers InnoDB
            DB::statement("ALTER TABLE `{$tableName}` ENGINE=InnoDB");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (in_array(DB::getDriverName(), ['sqlite', 'pgsql'])) {
            return;
        }

        $database = env('DB_DATABASE', 'ludophylosophie');
        $tables = DB::select("
            SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = ? 
            AND ENGINE = 'InnoDB'
        ", [$database]);

        foreach ($tables as $table) {
            $tableName = $table->TABLE_NAME;
            // Revenir à MyISAM (si nécessaire, par exemple lors d'un rollback)
            DB::statement("ALTER TABLE `{$tableName}` ENGINE=MyISAM");
        }
    }
};
