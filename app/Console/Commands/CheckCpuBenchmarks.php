<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Workstations;

class CheckCpuBenchmarks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:checkcpubenchmarks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monthly checking CPU bechmarks on cpubenchmark.net and store the results to workstations.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
    	$workstations = Workstations::select(DB::raw('COUNT(id) as counter'), 'cpu')
    		->groupBy('cpu')
    		->orderBy('counter', 'DESC')
    		->get();
    
    	foreach($workstations as $workstation) {
        
        	$cpu = $workstation->cpu;
        	$websiteSource = @file_get_contents('https://www.cpubenchmark.net/cpu.php?cpu=' . urlencode($cpu) );
        	$pos = strpos($websiteSource, "Multithread Rating");
			$sub = substr($websiteSource, $pos+30, 160);
    		$score = preg_replace('/\s+/', '', strip_tags($sub)); 
    		$score = (int)filter_var($score, FILTER_SANITIZE_NUMBER_INT);
            if ($score != "" && $score != 0) {

            	Workstations::where('cpu', $cpu)->update(['cpu_score' => $score]);

            }
        
        }
    
    }
}
