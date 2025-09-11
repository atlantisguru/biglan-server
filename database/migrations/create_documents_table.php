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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->text('keywords');
           	$table->string('source', 255);
            $table->text('filename');
            $table->integer('filesize');
        	$table->date('signed_at')->nullable();
            $table->integer('user_id');
        	$table->integer('deleted')->default(0);
        	$table->integer('deleter_id')->nullable();
        	$table->integer('locked')->default(0);
        	$table->integer('locker_id')->nullable();
            $table->timestamps();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
        
    }
};
