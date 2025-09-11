<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Workstations;
use Carbon\Carbon;
use App\Models\WsEvents;

class CleanWsEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workstation:cleanevents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans lock/unlock events older than 1 year from ws_events.';

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
    
    	$events = WsEvents::whereIn('event', ['lock', 'unlock'])->where('created_at', '<', Carbon::now()->subYear())->delete();
    	
    }
}
