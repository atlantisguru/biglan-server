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
        Schema::create('subnets', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->nullable();
        	$table->string('identifier', 255)->nullable();
        	$table->integer('mask')->nullable();
        	$table->string('gateway', 255)->nullable();
        	$table->text('alias')->nullable();
        	$table->text('description')->nullable();
            $table->timestamps();
        });
    
    	Schema::create('subnet_ips', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 255);
            $table->string('alias', 255)->nullable();
        	$table->timestamps();
        });
    
    	Schema::create('subnet_ip_changes', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 255);
            $table->text('event');
        	$table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subnets');
        Schema::dropIfExists('subnet_ips');
        Schema::dropIfExists('subnet_ip_changes');
        
    }
};
