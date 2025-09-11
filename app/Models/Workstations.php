<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\WsIps;
use App\Models\WsPrintStats;
use App\Models\WsConnections;


class Workstations extends Model
{

	public function status() {
		if($this->startup_at == null) {
        	return "offline";
        } else {
        	if (Carbon::parse($this->heartbeat) >= Carbon::now()->subSeconds(122)) {
            	if ($this->idle == 1) {
                	return "idle";
                } else {
                	return "online";
                }
            } else {
            	return "heartbeatLoss";
            }
        }
	}
	
    public static function online() {
		return self::whereNotNull('startup_at')->where('heartbeat', '>=', Carbon::now()->subSeconds(122));
	}
	
	public static function offline() {
		return self::whereNull('startup_at');
	}
	
	public static function heartbeatLoss() {
	 
     return self::where('heartbeat', '<', Carbon::now()->subSeconds(122))->whereNotNull('startup_at')->orderBy("heartbeat","ASC");
	}

	public static function idle() {
		return self::where('heartbeat', '>', Carbon::now()->subSeconds(122))->whereNotNull('startup_at')->where('idle',1);
	}
	
	public static function usb() {
		return self::where('usb', "!=", "0");
	}

	public static function vnc() {
    	return self::where('vnc', "!=", "0");
    }
	
	public static function teamviewer() {
		return self::where('teamviewer', "!=", "0");
	}
	
	public static function anydesk() {
		return self::where('anydesk', "!=", "0");
	}
	
	public static function rdp() {
		return self::where('rdp', "!=", "0");
	}
	
	public static function hddlow10() {
		return self::where('os_drive_free_space', "<=", "10");
	}
	
	public static function hddlow20() {
		return self::where('os_drive_free_space', "<=", "20")->where('os_drive_free_space', ">", "10");
	}

	public function events() {
    	return $this->hasMany('App\Models\WsEvents', 'wsid', 'id');
  	}

	public function interventions() {
    	return $this->hasMany('App\Models\WsInterventions', 'wsid', 'id');
  	}

	public function lastEvent() {
    	return WsEvents::where("wsid", $this->id)->orderBy("created_at", "DESC")->first();
    }

	public function hdds() {
  	 	return $this->hasMany('App\Models\WsHarddrives', 'wsid', 'id');
    }

	public function labels() {
  		return $this->hasMany('App\Models\WsLabels', 'wsid', 'id');
    }

	public function ips() {
    	$ips = $this->hasMany('App\Models\WsIps', 'wsid', 'id');
    
    	if ($ips->count() == 0) {
        	$ips = collect([['ip' => '0']]);
    	}
    
    	return $ips;
	}

	public function connections() {
     	return $this->hasMany(WsConnections::class, 'wsid', 'id');
	}

	public function wsIps()
    {
        return $this->hasMany(WsIps::class, 'wsid', 'id');
    }

	public function dns() {
  	 	return $this->hasMany('App\Models\WsDns', 'wsid', 'id');
    }

	public function accounts() {
  	 	return $this->hasMany('App\Models\WsUserAccounts', 'wsid', 'id');
    }

	public function printers() {
  	 	return $this->hasMany('App\Models\WsPrinters', 'wsid', 'id');
    }

	public function printStats() {
  		return $this->hasMany('App\Models\WsPrintStats', 'wsid', 'id');
  	}

	public function monitors() {
  	 	return $this->hasMany('App\Models\WsMonitors', 'wsid', 'id');
    }

	public function memories() {
  	 	return $this->hasMany('App\Models\WsMemories', 'wsid', 'id');
    }

	
}
