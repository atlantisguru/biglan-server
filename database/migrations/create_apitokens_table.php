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
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tokenable_type')->nullable()->index();
            $table->unsignedBigInteger('tokenable_id')->nullable()->index();
        	$table->string('name', 255)->nullable();
            $table->string('token', 128)->unique();
            $table->unsignedInteger('uses_count')->default(0);
            $table->unsignedInteger('max_uses')->nullable()->index();
            $table->dateTime('expires_at')->nullable()->index();
            $table->dateTime('last_used_at')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
			$table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_tokens');
        
    }
};
