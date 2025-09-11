<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Workstations;
use Carbon\Carbon;
use App\Models\WsEvents;

class CheckHeartbeatLoss extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workstation:heartbeatloss';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check workstations heartbeat loss status.';

	 /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
    
    	//2 minutes heartbeat loss after logoff event sent
    	$workstations = Workstations::where('heartbeat', '<=', Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s'))->whereNotNull('startup_at')->get();
    
   		foreach($workstations as $ws) {
    		if ($ws->lastEvent()->event == "logoff") {
        		$event = new WsEvents();
        		$event->wsid = $ws->id;
        		$event->event = "shutdown expected";
        		$event->description = "heartbeat loss for 2 minutes after logoff event. shutdown expected";
            	$event->save();
        		$ws->startup_at = null;
            	$ws->usb = 0;
        		$ws->teamviewer = 0;
        		$ws->anydesk = 0;
        		$ws->vnc = 0;
        		$ws->save();
        	}
     	}
    
    	//60 minutes heartbeat loss without any event sent
    	$workstations = Workstations::where('heartbeat', '<=', Carbon::now()->subMinutes(60)->format('Y-m-d H:i:s'))->whereNotNull('startup_at')->get();
    
    	foreach($workstations as $ws) {
        	$event = new WsEvents();
        	$event->wsid = $ws->id;
        	$event->event = "heartbeat loss";
        	$event->description = "heartbeat loss for 60 minutes. shutdown technically.";
            $event->save();
        	$ws->startup_at = null;
        	$ws->usb = 0;
        	$ws->teamviewer = 0;
        	$ws->anydesk = 0;
        	$ws->vnc = 0;
        	$ws->save();
        }
    }
}
