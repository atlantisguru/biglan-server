<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Workstations;


class WsPrinters extends Model
{
    public function workstation() {
    	return $this->hasOne('App\Models\Workstations', 'id', 'wsid')->first();
	}
}
