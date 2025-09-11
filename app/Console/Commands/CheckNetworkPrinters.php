<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\NetworkPrintersController;
use Carbon\Carbon;
use App\Models\GlobalSettings;
use App\Models\NetworkPrinters;
use App\Models\NetworkPrinterStatistics;

class CheckNetworkPrinters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'networkprinters:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check network printers levels and counters.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    
    	$run = new NetworkPrintersController();
    	$run->queryNetworkPrinters();
    	$this->copyToStatistics();
    	
    }

	public function copyToStatistics() {
    
    	$printers = NetworkPrinters::get();
    
    	foreach($printers as $printer) {
        
        	$statistics = new NetworkPrinterStatistics();
        	$statistics->printer_id = $printer->id;
        	$statistics->black_toner_level = $printer->black_toner_level;
        	$statistics->cyan_toner_level = $printer->cyan_toner_level;
        	$statistics->magenta_toner_level = $printer->magenta_toner_level;
        	$statistics->yellow_toner_level = $printer->yellow_toner_level;
        	$statistics->print_counter = $printer->print_counter;
        	$statistics->save();
        
        }
    
    }

}