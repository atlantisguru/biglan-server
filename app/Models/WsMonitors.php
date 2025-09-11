<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WsMonitors extends Model
{
    public function workstation() {
		return $this->hasOne('App\Models\Workstations', 'id', 'wsid')->first();
	}
}
