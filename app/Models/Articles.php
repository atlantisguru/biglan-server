<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Articles extends Model
{
	
    public function user() {
  	 	return $this->hasOne('App\Models\Users', 'id', 'user_id');
    }

	public function categories() {
  	 	$categories = ArticleCategoryRelations::where("article_id", $this->id)->orderBy("id", "ASC")->get();
    	return $categories;
    }

    public function comments() {
  	 	$comments = ArticleComments::where("article_id", $this->id)->orderBy("created_at", "ASC")->get();
    	return $comments;
    }


}