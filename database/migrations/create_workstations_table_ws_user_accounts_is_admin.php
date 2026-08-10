<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ws_user_accounts', function (Blueprint $table) {
            $table->integer('is_admin')->default(0)->after('sid');
        });
    }

    public function down(): void
    {
        Schema::table('ws_user_accounts', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
