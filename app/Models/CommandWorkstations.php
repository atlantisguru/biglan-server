<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommandWorkstations extends Model
{
	
    public function command() {
  	 	return $this->hasOne('App\Models\Commands', 'id', 'command_id');
    }

	public function workstation() {
  	 	return $this->hasOne('App\Models\Workstations', 'id', 'wsid');
    }
	

}