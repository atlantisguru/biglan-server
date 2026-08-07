<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ws_keys', function (Blueprint $table) {
            $table->id();
            $table->integer('wsid')->unique();
            $table->text('encryption_key');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ws_keys');
    }
};
