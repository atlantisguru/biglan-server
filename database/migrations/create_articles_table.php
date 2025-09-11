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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->longText('body')->nullable();
            $table->integer('user_id');
            $table->integer('version_num');
            $table->timestamps();
        });
    
    	Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id');
            $table->string('name', 255);
            $table->timestamps();
        });
    
    	Schema::create('article_category_relations', function (Blueprint $table) {
            $table->id();
            $table->integer('article_id');
            $table->integer('category_id');
            $table->timestamps();
        });
    
    	Schema::create('article_comments', function (Blueprint $table) {
            $table->id();
            $table->integer('article_id');
            $table->integer('user_id');
            $table->longText('comment');
            $table->timestamps();
        });
    
    	Schema::create('article_versions', function (Blueprint $table) {
            $table->id();
            $table->integer('article_id');
            $table->string('title', 255);
            $table->longText('body');
            $table->integer('user_id');
            $table->integer('version_num');
            $table->timestamps();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
        Schema::dropIfExists('article_categories');
        Schema::dropIfExists('article_category_relations');
        Schema::dropIfExists('article_comments');
        Schema::dropIfExists('article_versions');
       
    }
};
