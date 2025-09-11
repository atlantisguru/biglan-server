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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type', 255);
            $table->string('name', 255)->unique()->index();
            $table->string('alias', 255);
            $table->text('description')->nullable();
            $table->integer('level')->default(1);
        	$table->string('channel', 255)->nullable();
            $table->text('target');
            $table->integer('monitored')->default(0);
        	$table->integer('triggered')->default(0);
        	$table->integer('notified')->default(1);
        	$table->text('last_value')->nullable();
            $table->string('unit', 50)->nullable();
            $table->timestamps();
        });
    
    	Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('notification_id');
            $table->integer('status')->nullable();
            $table->string('event', 255)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('notification_logs');
        
    }
};
