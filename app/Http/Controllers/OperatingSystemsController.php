<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OperatingSystems;
use App\Models\Workstations;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OperatingSystemsController extends Controller
{

	/*
 	 * Processing payloads based on "action" (Operating Systems)
     */
	public function payload(Request $request) {
    
    $action = $request["action"];
    
    	switch($action){
			case "editOperatingSystem":
				return $this->editOperatingSystem($request);
				break;
        	case "scrapeOperatingSystems":
				return $this->scrapeOperatingSystems();
				break;
        	default:
    			return null;
    	}
    
    }

	/*
 	 * List view of Operating Systems (Operating Systems)
     */
	public function listOperatingSystems() {
    	
    	if(!auth()->user()->hasPermission('read-operating-systems')) { return redirect('dashboard'); }
    	
    
 		$operatingSystems = OperatingSystems::where("counter", ">", 0)->orderBy("name", "ASC")->get();
    	$today = Carbon::now()->format("Y-m-d");
    	$all = Workstations::count();
    
    	return view("operatingsystems.list", compact("operatingSystems", "today", "all"));
    
    }

	/*
 	 * Collects all the operating systems from the workstations database table (Operating Systems)
     */
	public function scrapeOperatingSystems() {
    
    	$workstationOperatingSystems = \DB::select("SELECT os as name, count(id) as number FROM workstations GROUP BY os");
    	OperatingSystems::query()->update(["counter" => 0]);
    	$operatingSystems = OperatingSystems::select("name", "version")->get();
    	
    	$haystackArray = $operatingSystems->pluck("name")->toArray();
    	
    	foreach($workstationOperatingSystems as $os) {
        	if (array_search($os->name, $haystackArray) === false) {
    			$newOS = new OperatingSystems();
            	$newOS->name = $os->name;
            	$newOS->counter = $os->number;
            	$newOS->save();
            } else {
            	$oldOS = OperatingSystems::where("name", "=", $os->name)->first();
            	$oldOS->counter = $os->number;
            	$oldOS->save();
            }
        }
    
    	return "OK";
    
    }

	/*
 	 * Saves the changes of an operating system's release date or end of life date (Operating Systems)
     */
	public function editOperatingSystem($request) {
   
    	$id = $request["id"];
    	$field = $request["field"];
    	$value = $request["value"];
    
    	$os = OperatingSystems::where("id", $id)->first();
    	$os->$field = $value;
    	
    	$save = $os->save();
    
    	if ($save) {
        	return "OK";
        }
    
    }

}