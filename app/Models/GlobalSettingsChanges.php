<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GlobalSettings;

class GlobalSettingsChanges extends Model
{
	public function globalsettings() {
    
    	return $this->belongsTo(GlobalSettings::class, 'gsid');

    }
}