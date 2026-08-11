<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'intervention_templates',
            'console_scripts',
            'ws_filters',
            'notifications',
            'global_settings',
            'downloads',
            'documents',
            'service_updates',
        ];

        foreach ($tables as $table) {
            DB::table($table)
                ->whereNull('created_at')
                ->update([
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {

    }
};
