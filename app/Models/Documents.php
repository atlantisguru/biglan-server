<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{
    public function uploader() {
  	 	return $this->hasOne('App\Models\Users', 'id', 'user_id');
    }

	public function locker() {
  	 	return $this->hasOne('App\Models\Users', 'id', 'locker_id');
    }

	public function deleter() {
  	 	return $this->hasOne('App\Models\Users', 'id', 'deleter_id');
    }
}
