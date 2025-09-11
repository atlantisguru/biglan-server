<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLogs extends Model
{
    public function notification() {
    
    	return $this->belongsTo(Notifications::class, 'notification_id');

    }

}