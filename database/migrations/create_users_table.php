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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
        	$table->string('token', 50);
            $table->string('username', 255);
            $table->string('email')->unique();
            $table->string('password');
        	$table->integer('confirmed')->default(0);
        	$table->string('push_id', 100)->nullable();
        	$table->string('theme', 255)->nullable();
        	$table->string('language', 10)->nullable();
        	$table->dateTime('last_login')->nullable();
        	$table->string('remember_token', 255)->nullable();
        	$table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id();
        	$table->integer('user_id')->nullable();
        	$table->string('email', 255);
            $table->string('token', 255);
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    
    	Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
        	$table->integer('user_id');
        	$table->string('activity', 255);
            $table->text('description')->nullable();
        	$table->string('ip', 50)->nullable();
            $table->text('browser')->nullable();
            $table->timestamps();
        });
    
    	Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
        	$table->integer('user_id');
        	$table->string('permission', 255);
            $table->timestamps();
        });
    
    	Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
        	$table->integer('user_id');
        	$table->string('name', 255);
            $table->text('value');
            $table->timestamps();
        });
    	
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('user_activities');
        Schema::dropIfExists('user_permissions');
    	Schema::dropIfExists('user_settings');
    }
};
