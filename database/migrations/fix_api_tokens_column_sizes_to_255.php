<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE api_tokens MODIFY token VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE api_tokens MODIFY token_hash VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE api_tokens MODIFY token VARCHAR(512) NOT NULL');
        DB::statement('ALTER TABLE api_tokens MODIFY token_hash VARCHAR(64) NULL');
    }
};
