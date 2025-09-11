<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleComments extends Model
{
	 public function user() {
  	 	return $this->hasOne('App\Models\Users', 'id', 'user_id');
    }
}