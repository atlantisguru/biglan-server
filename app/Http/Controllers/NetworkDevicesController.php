<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NetworkDevices;
use App\Models\NetworkEdges;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\DocumentTemplates;
use App\Models\Documents;

class NetworkDevicesController extends Controller
{

	/*
	 *	Processing the incoming payload based on "action" 
	 */
	public function payload(Request $request) {
		
    	if (isset($request["action"])) {
			
			$action = $request["action"];
			
			switch($action){
				
				case "archiveNetworkDevice":
					return $this->archiveNetworkDevice($request);
					break;
            	case "activeNetworkDevice":
					return $this->activeNetworkDevice($request);
					break;
            	case "getNetworkDevice":
            		return $this->getNetworkDevice($request);
            		break;
            	case "updateData":
            		return $this->updateData($request);
            		break;
            	default:
					return null;
					break;
			}
		}
    }

	/*
	 *	Updates the value of an edited field of a Network Device (Network Devices) 
	 */
	public function updateData($request) {
    
    	if(!auth()->user()->hasPermission('write-network-device')) { return "ERROR"; }
    
    	$id = $request["id"];
    	$field = $request["field"];
    	$value = $request["value"];
    	$networkdevice = NetworkDevices::where("id", $id)->first();
    	$networkdevice->$field = $value;
    	
    	$save = $networkdevice->save();
    
    	if ($save) {
        	return "OK";
        }
    
    }
    
	/*
	 *	Gives back information of a Network Device (Network Devices) 
	 */
	public function getNetworkDevice($request) {
    
    	if(!auth()->user()->hasPermission('read-network-devices')) { return "ERROR"; }
    	
    	$id = $request["id"];
    	
    	$networkdevice = NetworkDevices::where("id", $id)->first();
    	
    	if ($networkdevice == null) {
        	return null;
        }
    	
    	$connection = NetworkEdges::where("target", "LIKE", "nd".$id)->orderBy("id", "ASC")->first();
    	
    	if ($connection == null) {
        	$conn = "N/A";
        } else {
        	$nd_id = filter_var($connection->target, FILTER_SANITIZE_NUMBER_INT);
        	$nd = NetworkDevices::where("id", $nd_id)->first();
        	$conn = $nd->alias . "(" . $nd->hardware . " - " . $nd->ports . "P)";
        }
    
    	return response()->json([
        			"name" => $networkdevice->alias,
        			"brand" => $networkdevice->hardware,
        			"type" => $networkdevice->type,
        			"serial" => $networkdevice->serial,
        			"ip" => $networkdevice->ip,
        			"mac" => $networkdevice->mac,
        			"ports" => $networkdevice->ports,
        			"speed" => $networkdevice->speed,
        			"network" => $conn,
        			"active" => $networkdevice->active,
        ]);
    
    }

	/*
	 *	Changes the active status of a Network Device (Network Devices) 
	 */
	public function activeNetworkDevice($request) {
    
    	if(!auth()->user()->hasPermission('write-network-device')) { return "ERROR"; }
    
    	$id = $request["id"];
    	$networkdevice = NetworkDevices::where("id", $id)->first();
    	if ($networkdevice == null) {
        	return null;
        }
    
    	$hasConnections = NetworkEdges::where("target", "LIKE", "nd".$id)->orWhere("source", "LIKE", "nd".$id)->exists();
    
    	if ($hasConnections) {
        	return null;
        }
    
    	$status = $networkdevice->active;
    
    	if ($status === 1) {
        	$networkdevice->active = 0;
        } else {
        	$networkdevice->active = 1;
        }
    
    	$networkdevice->save();
    
    	return "OK";
    
    }

	/*
	 *	Removes a Network Device from database with all the related data and creates an HTML file to the Documents (Network Devices) 
	 */
	public function archiveNetworkDevice($request) {
    
    	if(!auth()->user()->hasPermission('delete-network-device')) { return "ERROR"; }
    
    	$id = $request["id"];
    	$networkdevice = NetworkDevices::where("id", $id)->first();
    	if ($networkdevice == null) {
        	return null;
        }
    
    	$connection = NetworkEdges::whereRaw("(target = CONCAT('nd', $networkdevice->id))")->first();
    	
    	if ($connection == null) {
        	$conn = "N/A";
        } else {
        	$nd_id = filter_var($connection->source, FILTER_SANITIZE_NUMBER_INT);
        	$nd = NetworkDevices::where("id", $nd_id)->first();
        	$conn = $nd->alias . "(" . $nd->hardware . " - " . $nd->ports . "P)";
        }
        
    	$content = view('networkdevices.archive', compact('networkdevice','conn'))->render();
    	$filename = "arhivalt-halozati-eszkoz".$networkdevice->alias."-".$networkdevice->id.".html";
    	//archív html létrehozása
    	file_put_contents(storage_path("documents/".$filename), $content);
    
    	//archív html fájl rögzítése dokumentumtárban
    	$doc = new Documents();
    	$doc->title = "Archivált hálózati eszköz - " . $networkdevice->alias . " - " . $networkdevice->serial . " - " . $networkdevice->hardware . " - " . $networkdevice->type;
    	$doc->keywords = "archív,hálózati,eszköz,".$networkdevice->alias.",".$networkdevice->serial.",".$networkdevice->hardware.",".$networkdevice->type;
    	$doc->source = "generated";
    	$doc->filename = $filename;
    	$doc->filesize = filesize(storage_path("documents/".$filename));
    	$doc->signed_at = Carbon::now()->format("Y-m-d");
    	$doc->user_id = Auth::user()->id;
    	$doc->save();
    
    	//munkaállomás és adatainak törlése adatbázisból
    	//hálózati kapcsolatok
    	NetworkEdges::where("target", "nd".$id)->delete();
    	NetworkEdges::where("source", "nd".$id)->delete();
    	//hálózati eszköz törlése
    	NetworkDevices::where("id", $id)->delete();
    
    	return "OK";
    
    }

	/*
	 *	Gives back a list of Network Devices (Network Devices) 
	 */
	public function listNetworkDevices(Request $request) {
    
    	if(!auth()->user()->hasPermission('read-network-devices')) { return redirect("dashboard"); }
    
    	$networkdevices = NetworkDevices::orderBy('alias')->get();
    	return view("networkdevices.list", ["networkdevices" => $networkdevices]);
    }

	/*
	 *	Form to register a new Network Device (Network Devices) 
	 */
	public function newNetworkDevice(Request $request) {
    
    	if(!auth()->user()->hasPermission('write-network-device')) { return redirect("dashboard"); }
    
    	$networkdevices = NetworkDevices::get();
    	return view("networkdevices.new", ["networkdevices" => $networkdevices]);
    }

	/*
	 *	Stores the newly created Network Device (Network Devices) 
	 */
	public function saveNetworkDevice(Request $request) {
    	
    	if(!auth()->user()->hasPermission('write-network-device')) { return redirect("dashboard"); }
    
    	$networkDevice = new NetworkDevices();
    	
    	if ($request["alias"] !== null) {
        	$networkDevice->alias = $request["alias"];
        } else {
        	$networkDevice->alias = "NetworkDevice" . rand(pow(10, 1), pow(10, 2)-1);
        }
    
    	if ($request["hardware"] !== null) {
        	$networkDevice->hardware = $request["hardware"];
        } else {
        	$networkDevice->hardware = "N/A";
        }
    
    	if ($request["serial"] !== null) {
        	$networkDevice->serial = $request["serial"];
        } else {
        	$networkDevice->serial = "N/A";
        }
    
    	if ($request["mac"] !== null) {
        	$networkDevice->mac = $request["mac"];
        }
    
    	if ($request["ip"] !== null) {
        	$networkDevice->ip = $request["ip"];
        }
        
        if ($request["type"] !== null) {
        	$networkDevice->type = $request["type"];
        }
    
    	if ($request["ports"] !== null) {
        	$networkDevice->ports = $request["ports"];
        } else {
        	$networkDevice->ports = 5;
        }
    
    	$saved = $networkDevice->save();
    	if ($saved) {
    		$id = $networkDevice->id;
        	return redirect("/networkdevices");
        }
    }
	
}
