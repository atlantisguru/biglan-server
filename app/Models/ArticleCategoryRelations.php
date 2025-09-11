<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleCategoryRelations extends Model
{
	
	public function category() {
  	 	return $this->hasOne('App\Models\ArticleCategories', 'id', 'category_id');
    }

	public function article() {
  	 	return $this->hasOne('App\Models\Articles', 'article_id', 'id');
    }
	
}
