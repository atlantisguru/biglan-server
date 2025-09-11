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
        Schema::create('console_scripts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
        	$table->string('alias', 255);
        	$table->text('code')->nullable();
            $table->integer('usage')->default(0);
        	$table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('console_scripts');
        
    }
};
