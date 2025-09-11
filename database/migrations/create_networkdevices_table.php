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
        Schema::create('network_devices', function (Blueprint $table) {
            $table->id();
            $table->string('alias', 255)->nullable();
            $table->string('hardware', 255)->nullable();
        	$table->string('serial', 255)->nullable();
        	$table->string('ip', 255)->nullable();
        	$table->string('mac', 255)->nullable();
        	$table->integer('active')->default(1);
        	$table->string('type', 255)->nullable();
        	$table->text('speed')->nullable();
        	$table->integer('manageable')->default(0);
        	$table->integer('snmp_capable')->default(0);
        	$table->bigInteger('uptime')->default(0);
        	$table->bigInteger('in')->default(0);
        	$table->bigInteger('out')->default(0);
        	$table->integer('ports')->default(4);
        	$table->timestamps();
        });
    
    	Schema::create('network_edges', function (Blueprint $table) {
            $table->id();
            $table->string('type',100)->nullable();
            $table->string('source',255);
            $table->string('target',255);
            $table->timestamps();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network_devices');
        Schema::dropIfExists('network_edges');
        
    }
};
