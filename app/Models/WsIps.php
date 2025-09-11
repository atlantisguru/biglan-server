<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Workstations;

class WsIps extends Model
{

	protected $fillable = ['wsid', 'ip'];

    public function workstation() {
    	return self::hasOne("App\Models\Workstations", "id", "wsid")->first();
    }
}
