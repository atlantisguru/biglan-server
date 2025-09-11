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
        Schema::create('global_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('type', 255)->nullable();
           	$table->text('value');
        	$table->text('description')->nullable();
        	$table->timestamps();
        });
    
    	Schema::create('global_settings_changes', function (Blueprint $table) {
            $table->id();
            $table->integer('gsid');
            $table->text('event')->nullable();
        	$table->timestamps();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_settings');
        Schema::dropIfExists('global_settings_changes');
        
    }
};
