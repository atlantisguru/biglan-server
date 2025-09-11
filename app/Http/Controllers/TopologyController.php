<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Workstations;	
use App\Models\NetworkDevices;
use App\Models\NetworkEdges;
use App\Models\NetworkPrinters;

class TopologyController extends Controller
{

	/*
 	 * Processing payloads based on "action" (Topology)
     */
	public function payload(Request $request) {
		if (isset($request["action"])) {
			
			$action = $request["action"];
			
			switch($action){
				
				case "addEdge":
					return $this->addEdge($request);
					break;
            	case "removeEdge":
            		return $this->removeEdge($request);
            	default:
					return null;
					break;
			}
		}
    }

	/*
 	 * Stores a new network connection between 2 devices (Topology)
     */
	public function addEdge(Request $request) {
    
    	if(!auth()->user()->hasPermission('write-topology')) { return "ERROR"; }
    
    
    	$target = $request->input("target");
    	$source = $request->input("source");
    	$type = $request->input("type");

    	$edge = NetworkEdges::where("source", "=", $source)
        	->where("target", "=", $target)
       	 	->orWhere(function ($query) use ($source, $target) {
          	  $query->where("source", "=", $target)
           	     ->where("target", "=", $source);
        	})
        	->first();

   		if ($edge) {
        	return "OK";
    	}

    	$edge = new NetworkEdges();
    	$edge->type = $type;
    	$edge->source = $source;
    	$edge->target = $target;
    	$edge->save();

    	return "OK";
	}

	/*
 	 * Removes a network connection between 2 devices (Topology)
     */
	public function removeEdge($request) {
    
    	if(!auth()->user()->hasPermission('write-topology')) { return "ERROR"; }
    	
    	$edge = NetworkEdges::where("id", "=", $request["id"]);
    	$edge->delete();
    	return "OK";
        
    }

    /*
 	 * Gives back the view of the network's logical topology (Topology)
     */
	public function viewTopology(Request $request) {
    	
    	if(!auth()->user()->hasPermission('read-topology')) { return redirect('dashboard'); }
    
    	return view("topology.topology");
    }

	/*
 	 * Gives back the status of endpoint devices to live update the topology view (Topology)
     */
	public function getUpdate() {
    
    if(!auth()->user()->hasPermission('read-topology')) { return redirect('dashboard'); }
    
    	
    $workstationColumns = ['id', 'alias as label', \DB::raw('IF(startup_at IS NULL,false,true) as online'), \DB::raw('IF(startup_at IS NOT NULL && heartbeat < DATE_SUB(NOW(), INTERVAL 122 SECOND),true, false) as lost')];
    $networkDeviceColumns = ['id', DB::raw('CONCAT(COALESCE(alias,"?")," / ", COALESCE(hardware,"?")) as label'), 'ports as size', 'active'];
    $printerColumns = ['id', 'alias as label', 'alias as black_toner'];
    
    $workstations = Workstations::with([])
        ->select($workstationColumns)
        ->get()
        ->toArray();

    $network_devices = NetworkDevices::with([])
        ->select($networkDeviceColumns)
        ->where('active', 1)
        ->get()
        ->toArray();

    $network_printers = NetworkPrinters::with([])
        ->select($printerColumns)
        ->get()
        ->toArray();

    $nodes = [];
    $edges = NetworkEdges::get()->toArray();

    foreach ($workstations as $workstation) {
        $workstation["id"] = "ws".$workstation["id"];
        $workstation["type"] = "ws";
        $nodes[] = $workstation;
    }

    foreach ($network_devices as $nd) {
        $nd["id"] = "nd".$nd["id"]; 
        $nodes[] = $nd;
    }

    foreach ($network_printers as $pr) {
        $pr["id"] = "pr".$pr["id"]; 
        $pr["type"] = "pr";
        $nodes[] = $pr;
    }

    	return response()->json(["nodes" => $nodes, "edges" => $edges]);
	}

}
