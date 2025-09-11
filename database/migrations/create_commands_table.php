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
        Schema::create('commands', function (Blueprint $table) {
            $table->id();
            $table->text('command')->nullable();
            $table->timestamp('run_after_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('alias')->nullable();
           	$table->integer('user_id');
            $table->text('description')->nullable();
           	$table->integer('blocked')->default(0);
            $table->timestamps();
        });
    
    	Schema::create('command_workstations', function (Blueprint $table) {
            $table->id();
            $table->integer('wsid');
            $table->integer('command_id');
            $table->text('alias')->nullable();
           	$table->timestamps();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commands');
        Schema::dropIfExists('comand_workstations');
        
    }
};
