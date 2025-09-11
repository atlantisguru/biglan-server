<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CommandWorkstations;

class Commands extends Model
{
	
    public function user() {
  	 	return $this->hasOne('App\Models\Users', 'id', 'user_id');
    }

	public function waiting() {
    	return CommandWorkstations::where("command_id", $this->id)->whereNull("result")->count();
    }

	public function done() {
    	return CommandWorkstations::where("command_id", $this->id)->whereNotNull("result")->count();
    }

	public function commands() {
    	return $this->hasMany('App\Models\CommandWorkstations', 'command_id', 'id');
    }
	

}