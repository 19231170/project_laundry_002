<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AdjustIdsForPostgresql extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get all tables
        $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
        
        foreach ($tables as $table) {
            $tableName = $table->table_name;
            
            // Skip migrations table and other system tables
            if ($tableName == 'migrations' || $tableName == 'failed_jobs' || $tableName == 'password_resets') {
                continue;
            }
            
            // Check if the table has an id column
            $hasId = DB::select("SELECT column_name FROM information_schema.columns 
                                WHERE table_schema = 'public' 
                                AND table_name = '$tableName' 
                                AND column_name = 'id'");
                                
            if (!empty($hasId)) {
                // Create sequence for each table
                DB::statement("CREATE SEQUENCE IF NOT EXISTS {$tableName}_id_seq");
                DB::statement("ALTER TABLE {$tableName} ALTER COLUMN id SET DEFAULT nextval('{$tableName}_id_seq')");
                DB::statement("SELECT setval('{$tableName}_id_seq', (SELECT MAX(id) FROM {$tableName}))");
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This is a one-way migration, no need to reverse
    }
}
