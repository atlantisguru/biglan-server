<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\OperatingSystemsController;
use Carbon\Carbon;
use App\Models\OperatingSystems;

class CheckOperatingSystems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'operatingsystems:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check operating systems.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    
    	$run = new OperatingSystemsController();
    	$run->scrapeOperatingSystems();
    	
    }

}