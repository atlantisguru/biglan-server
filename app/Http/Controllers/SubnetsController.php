<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Subnets;
use App\Models\SubnetIps;
use App\Models\SubnetIpChanges;
use App\Models\WsIps;
use App\Models\Workstations;
use App\Models\NetworkPrinters;
use App\Models\WsEvents;

class SubnetsController extends Controller
{

	/*
 	 * Processing payloads based on "action" (Ip Table)
     */
	public function payload(Request $request) {
    		
    	$action = $request["action"];
    
    	switch($action) {
        	case "deleteIP":
        		return $this->deleteIP($request);
        		break;
        	case "changeIP":
        		return $this->ipAddressAssignChange($request);
       		default:
        		return null; 
        }
    
    }

	/*
 	 * Removes an IP from the database (Ip Table)
     */
	public function deleteIP($request) {
    
    	if(!auth()->user()->hasPermission('write-ips')) { return "ERROR"; }
    
    	$id = $request["id"];
    	$ipObject = WsIps::where("id", $id)->first();
    	$ip = $ipObject->ip;
    	$wsid = $ipObject->wsid;
    
    	if ($ipObject != null) {
        	$deleted = $ipObject->delete();
        }
    
    	if ($deleted) {
        	$event = new WsEvents();
    		$event->wsid = $wsid;
    		$event->level = 0;
    		$event->event = "ip address removed";
    		$event->description = $ip . " removed manually by " . Auth::user()->username;
    	
    		$saved = $event->save();
        
        	return "OK";
        
        } else {
        
        	return "ERROR";
        }
    
    }

	/*
 	 * Stores a new subnet (Ip Table)
     */
	public function createSubnet(Request $request) {
    	
    	if(!auth()->user()->hasPermission('write-subnetwork')) { return redirect('dashboard'); }
    
    	$errors = array();
    	
    	if (!preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\/\d{1,2}/", $request["identifier"])) {
    		$errors[] = "Az alhálózat formátuma nem megfelelő.";    
        }
    
        if (!preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $request["gateway"])) {
    		$errors[] = "Az alapértelmezett alhálózat formátuma nem megfelelő.";    
        }
            
        if ($request["alias"] == "") {
        	$errors[] = "Mindenképpen adj egy elnevezést (alias) az alhálózatnak";    
        }
    
    	if (count($errors) != 0) {
        	return back()->withErrors($errors)->withInput();
        }
   		
    	$type = intval(4);
    	$identifier = explode("/", $request["identifier"])[0];
    	$mask = intval(explode("/", $request["identifier"])[1]);
    	$gateway = $request["gateway"];
    	$alias = $request["alias"];
    
    	if (isset($request["description"])) {
    		$description = $request["description"];
        }
    	
        $subnet = new Subnets();
        $subnet->type = $type;
        $subnet->identifier = $identifier;
        $subnet->mask = $mask;
        $subnet->gateway = $gateway;
        $subnet->alias = $alias;
        if($description != "") {
        	$subnet->description = $description;
        }
            
        $save = $subnet->save();
    	if ($save) {
        	return redirect('/subnets');
        }
    }

	/*
 	 * Gives back a list view of created subnets (Ip Table)
     */
	public function listSubnets() {
    	
    	if(!auth()->user()->hasPermission('read-subnetworks')) { return redirect('dashboard'); }
    
    	$subnets = Subnets::orderBy("created_at", "ASC")->get();
    	
    	$subnetIPs = array();
    
    	foreach($subnets as $subnet) {
        	$subnetIPs[$subnet->id] = $this->generateSubnetIPs($subnet->identifier, $subnet->mask);
        	$subnet->count = count($subnetIPs[$subnet->id]);
        	
        	$mask = $this->convertDecimalMaskToIP($subnet->mask);
        
        	$existingSubnetIPs = \DB::table('subnet_ips')
    								->whereRaw('(INET_ATON(ip) & INET_ATON(?)) = INET_ATON(?)', [$mask, $subnet->identifier])
            						->select('ip', 'alias')
            						->orderBy('ip','ASC')
    								->get();
    		
        	foreach($existingSubnetIPs as $existing) {
            	$subnetIPs[$subnet->id][$existing->ip]["ip"] = $existing->ip;
            	$subnetIPs[$subnet->id][$existing->ip]["alias"] = $existing->alias;
            }
        
        	$existingWSIPs = \DB::table('ws_ips')
            						->join('workstations', 'workstations.id', '=', 'ws_ips.wsid')
    								->whereRaw('(INET_ATON(ip) & INET_ATON(?)) = INET_ATON(?)', [$mask, $subnet->identifier])
            						->select('ws_ips.ip', 'ws_ips.wsid', 'workstations.alias')
            						->orderBy('ip','ASC')
    								->get();
        	
        	foreach($existingWSIPs as $existing) {
            	$subnetIPs[$subnet->id][$existing->ip]["ip"] = $existing->ip;
            	$subnetIPs[$subnet->id][$existing->ip]["alias"] = $existing->alias;
            	$subnetIPs[$subnet->id][$existing->ip]["wsid"] = $existing->wsid;
            }
        
        	$existingPrinterIPs = \DB::table('network_printers')
            						->whereRaw('(INET_ATON(ip) & INET_ATON(?)) = INET_ATON(?)', [$mask, $subnet->identifier])
            						->select('ip', 'alias', 'brand', 'id')
            						->orderBy('ip','ASC')
    								->get();
        	
        	foreach($existingPrinterIPs as $existing) {
            	$subnetIPs[$subnet->id][$existing->ip]["ip"] = $existing->ip;
            	$subnetIPs[$subnet->id][$existing->ip]["alias"] = $existing->alias . " (" . $existing->brand . ")";
            	$subnetIPs[$subnet->id][$existing->ip]["prid"] = $existing->id;
            }
        
        	
        }
    	
    	return view("subnets.list", compact("subnets", "subnetIPs"));
    
    }

	/*
 	 * Form to create a new subnet (Ip Table)
     */
	public function newSubnet() {
    	
    	if(!auth()->user()->hasPermission('write-subnetwork')) { return redirect('dashboard'); }
    
    	return view("subnets.new");
    
    }

	/*
 	 * Generates a list of IPs based on the subnet parameters (Ip Table)
     */
	public function generateSubnetIPs($identifier, $mask){
    	
    	$subnetIPs = array();

    	$binaryNetworkIdentifier = decbin(ip2long($identifier));

    	$paddedNetworkIdentifier = str_pad($binaryNetworkIdentifier, 32, '0', STR_PAD_LEFT);

    	$networkPortionBinary = substr($paddedNetworkIdentifier, 0, $mask);

    	$hostBits = 32 - $mask;

    	$subnetSize = pow(2, $hostBits);

    	$startIP = 1;
    	$endIP = $subnetSize - 2;

    	for ($i = $startIP; $i <= $endIP; $i++) {
        	$decimalIP = bindec($networkPortionBinary . str_pad(decbin($i), $hostBits, '0', STR_PAD_LEFT));
			$subnetIPs[long2ip($decimalIP)]["ip"] = long2ip($decimalIP);
    	}

    	return $subnetIPs;
    }

	/*
 	 * Converts a decimal mask to IP address (Ip Table)
     */
	public function convertDecimalMaskToIP($decimalMask) {
    	
    	$binaryMask = str_repeat('1', $decimalMask) . str_repeat('0', 32 - $decimalMask);
		$ipAddressMask = long2ip(bindec($binaryMask));

		return $ipAddressMask;
    }

	/*
 	 * Stores the change of an IP address assignment (Ip Table)
     */
	public function ipAddressAssignChange(Request $request) {
    
    	if(!auth()->user()->hasPermission('write-ips')) { return "ERROR"; }
    
    	$ip = SubnetIps::where('ip', $request["ip"])->first();
    
    	if ($ip == null) {
        	$ip = new SubnetIps();
        	$ip->ip = $request["ip"];
        	$ip->alias = "";
        }
    
    	if ($ip->alias != $request["alias"]) {
        	$ipChange = new SubnetIpChanges();
        	$ipChange->ip = $ip->ip;
        	$ipChange->event = "Alias: " . ((isset($ip->alias))?$ip->alias:"") . " -> " . $request["alias"] . " (" . auth()->user()->username . ")";
        	$ipChange->save();
        }
    
    	$ip->alias = $request["alias"];
    	$ip->save();
    	
    	return "OK";
    
    }

}
