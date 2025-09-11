<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Workstations;

class WsEvents extends Model
{

	protected $fillable = ['wsid', 'event', 'description'];	

    public static function events() {
		 return self::take(40)->orderBy('created_at', 'DESC')->get();
	}
	
	public function workstation() {
	    return $this->belongsTo(Workstations::class, 'wsid');
    }
    
}
