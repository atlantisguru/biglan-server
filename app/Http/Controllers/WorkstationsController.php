<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Arr;
use App\Models\Workstations;
use App\Models\WsEvents;
use App\Models\WsInterventions;
use App\Models\WsIps;
use App\Models\WsUserAccounts;
use App\Models\WsHarddrives;
use App\Models\WsPrinters;
use App\Models\WsConnections;
use App\Models\WsKeys;
use App\Models\ServiceUpdates;
use Carbon\Carbon;
use App\Models\NetworkDevices;
use App\Models\NetworkEdges;
use App\Models\WsDns;
use App\Models\WsMonitors;
use App\Models\WsMemories;
use App\Models\WsPrintStats;
use App\Models\WsLabels;
use App\Models\WsFilters;
use App\Models\ConsoleScripts;
use App\Models\Subnets;
use App\Models\SubnetIps;
use App\Models\SubnetIpChanges;
use App\Models\WsControlLog;
use Illuminate\Support\Facades\Auth;
use App\Models\CommandWorkstations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Models\GlobalSettings;
use App\Models\InterventionTemplates;
use App\Models\Documents;

class WorkstationsController extends Controller
{
	/*
 	 * Processing incoming payloads based on "action" (Workstations)
     */
    public function payload(Request $request)
	{
		if (isset($request["action"])) {
			
			$action = $request["action"];
			
			switch($action){
				
				case "editAlias":
            		if(!auth()->user()->hasPermission('write-workstation')) { return "ERROR"; }
            		return $this->editAlias($request);
            		break;
            	case "editWorkstation":
            		if(!auth()->user()->hasPermission('write-workstation')) { return "ERROR"; }
            		return $this->editWorkstation($request);
            		break;
            	case "archiveWorkstation":
            		if(!auth()->user()->hasPermission('delete-workstation')) { return "ERROR"; }
            		return $this->archiveWorkstation($request);
            		break;
            	case "saveWorkstationNetworkConnection":
            		if(!auth()->user()->hasPermission('write-workstation')) { return "ERROR"; }
            		return $this->editNetworkEdge($request);
            		break;
            	case "getWorkstationConnections":
            		if(!auth()->user()->hasPermission('read-workstation')) { return "ERROR"; }
            		return $this->getWsConnections($request);
            		break;
            	case "saveWorkstationConnection":
            		if(!auth()->user()->hasPermission('write-workstation')) { return "ERROR"; }
            		return $this->saveWsConnection($request);
            		break;
            	case "deleteWorkstationConnection":
            		if(!auth()->user()->hasPermission('write-workstation')) { return "ERROR"; }
            		return $this->deleteWsConnection($request);
            		break;
            	case "sendCommand":
            		return $this->command($request);
            		break;
            	case "checkStatus":
            		return $this->checkStatus($request);
            		break;
            	case "WOL":
            		return $this->WOL($request);
            		break;
            	case "ping":
            		return $this->ping($request);
            		break;
            	case "checkNewEvents":
            		return $this->checkNewEvents($request);
            		break;
            	case "checkOlderEvents":
            		return $this->checkOlderEvents($request);
            		break;
            	case "saveCommand":
            		return $this->saveCommand($request);
            		break;
            	case "quickSearch":
            		return $this->quickSearch($request);
            		break;
            	case "getWorkstationConnections":
            		return $this->getWorkstationConnections($request);
            		break;
            	case "createWsLabel":
            		if(!auth()->user()->hasPermission('write-workstation')) { return "ERROR"; }
            		return $this->createWsLabel($request);
            		break;
            	case "deleteWsLabel":
            		if(!auth()->user()->hasPermission('write-workstation')) { return "ERROR"; }
            		return $this->deleteWsLabel($request);
            		break;
            	case "createOperatorEvent":
            		if(!auth()->user()->hasPermission('write-intervention')) { return "ERROR"; }
            		return $this->createOperatorEvent($request);
            		break;
            	case "deleteInterventionSuggestion":
            		return $this->deleteInterventionSuggestion($request);
            		break;
            	case "addInterventionSuggestion":
            		return $this->addInterventionSuggestion($request);
            		break;
            	case "deleteFilter":
            		return $this->deleteFilter($request);
            		break;
            	case "printGraph":
            		return $this->printGraph($request);
            		break;
            	default:
					return null;
					break;
			}
		}
		
		return null;
	}
	
	/*
 	 * Gives a list of workstations with their IPs (Dashboard/magnifying glass or DBL-CTRL)
     */
    public function quickSearch(Request $request)
	{
    	$phrase = $request->input('phrase');
    	$workstations = Workstations::with('wsIps')
        	->where('alias', 'LIKE', '%' . $phrase . '%')
        	->get();
		
    	$result = $workstations->map(function ($ws) {
        	return [
            	'id' => $ws->id,
            	'alias' => $ws->alias,
            	'ip' => (isset($ws->ips()->first()->ip))?$ws->ips()->first()->ip:"N/A",
            	'status' => $ws->status(),
        	];
    	});
        

    	return response()->json(['workstations' => $result]);
	}

	/*
 	 * Gives back a 30 days statistics about prints of a workstation (Workstation/Prints)
     */
	public function printGraph($request) {
    
    	$statistics = WsPrintStats::select(\DB::raw("DATE(created_at) as datum, counter, pages"))->where("wsid", $request["wsid"])->orderBy("created_at", "DESC")->where('created_at', '>', now()->subDays(30)->endOfDay())->get();
    	
    	$endDate = Carbon::today();
        $startDate = Carbon::today()->subDays(30);
    	while ($startDate->lte($endDate)) {
        	$dateArray[]["date"] = $startDate->format('Y-m-d');
        	$startDate->addDay();
    	}
    
    	$printArray = $dateArray;
    	$max = 0;
    	$i = 0;
    	$allPages = 0;
    	$allCounter = 0;
    	foreach($printArray as $item) {
        	$print = $statistics->where("datum", $item["date"])->first();
        	$printArray[$i]["pages"] = 0;
        	$printArray[$i]["counter"] = 0;
        	if (isset($print)) {
        		$printArray[$i]["pages"] = $print->pages;
            	$printArray[$i]["counter"] = $print->counter;
            	if ($print->pages > $max) {
                	$max = $print->pages;
                }
            	$allPages = $allPages + $print->pages;
            	$allCounter = $allCounter + $print->counter;
            }
        	$i++;
        }
    	
    	return response()->json([
        			"max" => $max,
        			"allpages" => $allPages,
        			"allcounter" => $allCounter,
        			"printarray" => $printArray
        ]);
    
    }

	/*
 	 * Generates a 30-day array for Multifunction Printer statistics (Assets/Netwok Printers)
     */
	public function generateDateArray()	{
    	$dateArray = [];

    	$currentTime = Carbon::now();
		$hour = $currentTime->hour;

		if ($hour < 10) {
			$endDate = Carbon::today()->subDays(0);
	        $startDate = Carbon::today()->subDays(30);
        } else {
			$endDate = Carbon::today();
        	$startDate = Carbon::today()->subDays(30);
        }
    	
    	while ($startDate->lte($endDate)) {
        	$dateArray[]["date"] = $startDate->format('Y-m-d');
        	$startDate->addDay();
    	}

    	return $dateArray;
	}

	/*
 	 * Gives back a list of connections for a workstations (Workstations/Connections)
     */
	public function getWorkstationConnections(Request $request)
	{
    	$wsid = $request['wsid'];
    	$connections = WsConnections::where("wsid", $wsid)->whereIn("type", ["vnc","anydesk","teamviewer","phone", "location"])->get()->toArray();
		
    	return response()->json(['connections' => $connections]);
	}

	public function deleteInterventionSuggestion($request) {
    	
    	$value = $request["value"];
    	$interventionSuggestion = InterventionTemplates::where("message", $value)->first();
    
    	if(isset($interventionSuggestion)) {
        	
        	$deleted = $interventionSuggestion->delete();
        
        	if($deleted) {
            	return "OK";
            }
        
        }
    
    	return "ERROR";
    	
    }

	/*
 	 * Saves a new Intervention Suggestion (Workstations/Interventions)
     */
	public function addInterventionSuggestion($request) {
    	
    	$value = $request["value"];
    
    	$exists = InterventionTemplates::where("message", $value)->first();
    
    	if (isset($exists)) {
        	return "ERROR";
        }
    
    	$interventionSuggestion = new InterventionTemplates;
    	$interventionSuggestion->message = $value;
    
        $saved = $interventionSuggestion->save();
        
       	if($saved) {
           	return "OK";
        }
        
    	return "ERROR";
    	
    }
    
	/*
 	 * Gives back the status of a workstations (Workstations)
     */
	public function checkStatus($request) {
    	try {
        	$id = $request['id'];
        	$workstation = Workstations::where("id", $id)->first();
        
            return $workstation->status();
        
        } catch(Exception $e) {}
    }

	/*
 	 * Wake On LAN functionality (Workstations/Console)
     */
	public function WOL($request) {
    	try {
        	$wsid = $request['wsid'];
        	$workstation = Workstations::where("id", $wsid)->first();
        	
        	$ip = $this->getFirstMatchingIp($wsid);
        	$ipSegments = explode(".", $ip);
        	
        	$ipSegments[3] = 255;
        	$broadcast = $this->getBroadcastAddressFromSubnet($this->getSubnetFromIP($ip));
        	
        	if (!isset($workstation->active_mac)) {
            	return "1";
            }
        
        	$hwaddr = pack('H*', preg_replace('/[^0-9a-fA-F]/', '', $workstation->active_mac));

    		$packet = sprintf('%s%s', str_repeat(chr(255), 6), str_repeat($hwaddr, 16));

    		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

    		if ($sock !== false) {
        		$options = socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, true);

        		if ($options !== false) {
            		socket_sendto($sock, $packet, strlen($packet), 0, $broadcast, 7);
            		socket_close($sock);
        		}
    		}
            
        	return "0";   
        } catch(Exception $e) {}
    }

	/*
 	 * Pings a workstation (Workstations/Console)
 	 * It helps the WOL function detect if the workstation turned on successfully or not
     */
	public function ping($request) {
    	try {
        	$id = $request['wsid'];
        	$ip = $this->getFirstMatchingIp($id);
        	$timeout = 1;
        	
        	exec("/bin/ping -c 1 -W 1 {$ip}", $output, $status);
        	
        	if ($status == 0) {
            	return "0";
            } else {
            	return "1";
            }
        	
        } catch(Exception $e) {}
    }

	/*
 	 * Gives back a list of events of a workstation based on the last known event (Workstations/Events)
     */
	public function checkNewEvents($request) {
    	try {
        	$id = $request['id'];
        	$date = str_replace(".", "-", $request['lastdate']);
        	$workstation = Workstations::where("id", $id)->first();
        
        	if ($workstation == null) {
            	return 0;
            }
        	
        	$events = WsEvents::select(DB::raw("created_at, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS formatted_at, event, description"))->where("wsid", $id)->where("created_at", ">", $date)->orderBy("created_at", "ASC")->get();	
        
        	if ($events != null) {
            	return $events;
            }
        
        } catch(Exception $e) {}
    }

	/*
 	 * Gives back a list of events for a workstation when the event list scrolled down to the bottom of the currently shown list (Workstations/Events)
     */
	public function checkOlderEvents($request) {
    	try {
        	$id = $request['id'];
        	$date = $request['lastdate'];
        	
        	$workstation = Workstations::where("id", $id)->first();
        
        	if ($workstation == null) {
            	return 0;
            }
        	
        	$events = WsEvents::select(DB::raw("created_at, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS formatted_at, event, description"))->where("wsid", $id)->where("created_at", "<", $date)->take(30)->orderBy("created_at", "DESC")->get();	
        
        	if ($events != null) {
            	return $events;
            }
        
        } catch(Exception $e) {}
    }

	/*
 	 * Saves a new connection for a workstation (Workstations/Connections)
     */
	public function saveWsConnection($request) {
    	
    	if ($request["wsid"] == null) {return "ERROR";} 
    
    	$ws = Workstations::where("id", $request["wsid"])->first();
    
    	if (!isset($ws)) {return "ERROR";}
    
    	$saved = false;
    
    	$connection = new WsConnections();
    	$connection->wsid = $request["wsid"];
    	$connection->type = $request["type"];
    	$connection->value = $request["value"];
    	$connection->notes = $request["notes"];
    
    	$saved = $connection->save();
    
    	if ($saved == true) {
        	$event = new WsEvents();
        	$event->wsid = $request["wsid"];
        	$event->event = "connection created";
        	$event->description = $request["type"] ."/". $request["value"] ." (" . Auth::user()->username . ")";
        	$event->save();
        }
    
    	if ($saved == true) {
    		return "OK";
        } else {
        	return "ERROR";
        }
    }

	/*
 	 * Removes a connection for a workstation (Workstations/Connections)
     */
	public function deleteWsConnection($request) {
    	
    	if ($request["id"] == null) {return "ERROR";} 
    
    	$connection = WsConnections::where("id", $request["id"])->first();
    	$type = $connection->type;
    	$value = $connection->value;
    	$wsid = $connection->wsid;
    
    	if (!isset($connection)) {return "ERROR";}
    
    	$deleted = $connection->delete();
    	
    	if ($deleted == true) {
        	$event = new WsEvents();
        	$event->wsid = $wsid;
        	$event->event = "connection deleted";
        	$event->description = $type ."/". $value ." (" . Auth::user()->username . ")";
        	$event->save();
        }
    
    	if ($deleted == true) {
    		return "OK";
        } else {
        	return "ERROR";
        }
    }
	
	/*
 	 * Gives back a list of connections for a workstations (Workstations/Connections)
     */
	public function getWsConnections($request) {
    	
    	if ($request["wsid"] == null) {return "ERROR";} 
    
    	$connections = WsConnections::where("wsid", $request["wsid"])->get();
    	
    	if (!isset($connections)) {return "ERROR";}
    
    	$conn = array();
    
    	$types = [
                                            "vnc" => ["name" => "RealVNC",
                                                      "url" => "com.realvnc.vncviewer.connect://",
                                                      "action" => __('all.workstations.connect')
                                                      ],
                                            "anydesk" => ["name" => "Anydesk",
                                                      "url" => "anydesk:",
                                                      "action" => __('all.workstations.connect')
                                                      ],
                                            "teamviewer" => ["name" => "Teamviewer",
                                                      "url" => "https://start.teamviewer.com/",
                                                      "action" => __('all.workstations.connect')
                                                      ],
                                            "phone" => ["name" => __('all.workstations.phone'),
                                                      "url" => "tel:",
                                                      "action" =>	__('all.workstations.call')
                                                      ],
                                            "email" => ["name" => __('all.workstations.email'),
                                                      "url" => "mailto:",
                                                      "action" => __('all.workstations.send')
                                                      ],
                                            "location" => ["name" => __('all.workstations.location'),
                                                      "url" => "https://www.google.com/maps?q=",
                                                      "action" => __('all.workstations.navigate')
                                                      ],
                                            "url" => ["name" => "URL",
                                                      "url" => "",
                                                      "action" => __('all.workstations.open')
                                                      ]
                                        ];
    
    	foreach($connections as $connection) {
        
        	$conn[] = array(
            	"id" => $connection->id,
            	"name" => $types[$connection->type]["name"],
            	"value" => $connection->value,
            	"notes" => (isset($connection->notes))?$connection->notes:"",
            	"url" => $types[$connection->type]["url"] . $connection->value,
            	"action" => $types[$connection->type]["action"]
            );
        
        }
    
    
    	return $conn;
    }

	/*
 	 * Saves a label for a workstation (Workstations/Datasheet)
     */
	public function createWsLabel($request) {
    	
    	if ($request["wsid"] == null) {return;} 
    
    	$saved = false;
    
    	$exists = WsLabels::where("name", $request["name"])->where("wsid", $request["wsid"])->first();
    	if ($exists == null) {
        	$label = new WsLabels();
        	$label->wsid = $request["wsid"];
        	$label->name = $request["name"];
        	$saved = $label->save();
        }
    
    	if ($saved == true) {
        	$event = new WsEvents();
        	$event->wsid = $request["wsid"];
        	$event->event = "label created";
        	$event->description = "'". $request["name"] ."' (" . Auth::user()->username . ")";
        	$event->save();
        }
	
    	$labels = WsLabels::where("wsid", $request["wsid"])->get();
    	return response()->json(["labels" => $labels]);
    }

	/*
 	 * Removes a label of a workstation (Workstations/Datasheet)
     */
	public function deleteWsLabel($request) {
    
    	if ($request["wsid"] == null) {return;} 
    
    	$deleted = false;
    	
    	$exists = WsLabels::where("id", $request["id"])->first();
    	$labelName = $exists->name;
    	$labelWSID = $exists->wsid;
    
    	if ($exists != null) {
        	$deleted = $exists->delete();
        }
    
    	if ($deleted == true) {
        	$event = new WsEvents();
        	$event->wsid = $labelWSID;
        	$event->event = "label deleted";
        	$event->description = "'". $labelName ."' (" . Auth::user()->username . ")"; 
        	$event->save();
        }
		
    	$labels = WsLabels::where("wsid", $request["wsid"])->get();
    	return response()->json(["labels" => $labels]);
    }

	/*
 	 * Saves an intervention event for a workstation (Workstations/Interventions)
     */
	public function createOperatorEvent($request) {
    		
    		$event = new WsEvents();
        	$event->wsid = $request["wsid"];
    		if($request["operators"] == "") { $request["operators"] = Auth::user()->username; }
    		$event->event = "intervention";
        	$event->description = $request["event"] . " (" . $request["operators"] . ")";
        	$event->save();
    		
    		$operatorEvent = new WsInterventions();
        	$operatorEvent->wsid = $request["wsid"];
    		if($request["operators"] == "") { $request["operators"] = Auth::user()->username; }
    		$operatorEvent->event = "intervention";
        	$operatorEvent->description = $request["event"] . " (" . $request["operators"] . ")";
        	$operatorEvent->save();
    
        	return "";
            
    }

	/*
 	 * Display form for manually registering a new workstation (Workstations/New)
     */
	public function newWorkstation() {
    	$workstations = Workstations::get();
    	$monitors = WsMonitors::get();
    	$os = [];
    	$wg = [];
    	$cpu = [];
    	$hw = [];
    	$mon_man = [];
    	foreach($workstations as $ws) {
        	$cpu[] = array("label" => $ws->cpu, "value" => $ws->cpu, "release" => $ws->cpu_release_date);
        	$os[] = array("label" => $ws->os, "value" => $ws->os);
        	$wg[] = array("label" => $ws->workgroup, "value" => $ws->workgroup);
        	$hw[] = array("label" => $ws->hardware, "value" => $ws->hardware);
        }
    
    	foreach($monitors as $mon) {
        	$mon_man[] = array("label" => $mon->manufacturer, "value" => $mon->manufacturer);
        	$mon_name[] = array("label" => $mon->name, "value" => $mon->name);
        }
    	
    	$cpu = array_values(array_unique($cpu, SORT_REGULAR));
    	$os = array_values(array_unique($os, SORT_REGULAR));
    	$wg = array_values(array_unique($wg, SORT_REGULAR));
    	$hw = array_values(array_unique($hw, SORT_REGULAR));
    	$mon_man = array_values(array_unique($mon_man, SORT_REGULAR));
    	$mon_name = array_values(array_unique($mon_name, SORT_REGULAR));
    
    	return view("workstations.new", ["cpu" => json_encode($cpu), "os" => json_encode($os), "wg" => json_encode($wg), "hw" => json_encode($hw), "mon_man" => json_encode($mon_man), "mon_name" => json_encode($mon_name)]);
    }

	/*
 	 * Saves the manually registered new workstation (Workstations/New)
     */
	public function saveWorkstation(Request $request) {
    
    	$data = request()->except('_token');
    	
    	$rules = [
        	'alias' => 'required',
        	'os' => 'required',
        	'cpu' => 'required',
        	'ram' => 'required|numeric',
        	'ram_slots' => 'nullable|numeric',
        	'ram_max_capacity' => 'nullable|numeric',
        	'cpu_release_date' => 'nullable|date',
        	'ip.*' => 'nullable|ip',
        	'dns.*' => 'nullable|ip',
        	'product_serial' => 'required_without_all:mboard_serial,uuid,first_mac',
    		'mboard_serial' => 'required_without_all:product_serial,uuid,first_mac',
    		'uuid' => 'required_without_all:product_serial,mboard_serial,first_mac|nullable|regex:/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/',
        	'first_mac' => 'required_without_all:product_serial,mboard_serial,uuid|nullable|regex:/^[0-9a-fA-F]{12}$/',
        	'disk_capacity.*' => 'nullable|numeric',
        ];

    	$messages = [
    		'required' => __('all.validation.required'),
    		'numeric' => __('all.validation.numeric'),
    		'date' => __('all.validation.date'),
    		'ip' => __('all.validation.ip'),
    		'required_without_all' => __('all.validation.required_without_all'),
        	'required_if' => __('all.validation.required'),
        	'regex' => __('all.validation.regex'),
        ];
    
    	$validator = Validator::make($data, $rules, $messages);

    	if ($validator->fails()) {
        	return redirect()->back()->withErrors($validator)->withInput();
    	}
    
    	$workstation = new Workstations();
    	$workstation->product_serial = $request["product_serial"];
    	$workstation->mboard_serial = $request["mboard_serial"];
    	$workstation->uuid = $request["uuid"];
    	$workstation->first_mac = $request["first_mac"];
    
    	$workstation->alias = $request["alias"];
    	$workstation->hostname = $request["hostname"];
    	$workstation->workgroup = $request["workgroup"];
    	$workstation->os = $request["os"];
    	$workstation->cpu = $request["cpu"];
    	$workstation->cpu_release_date = $request["cpu_release_date"];
    	$workstation->hardware = $request["hardware"];
    	$workstation->type = $request["type"];
    	$workstation->serial = $request["product_serial"];
    	$workstation->ram = $request["ram"];
    	$workstation->ram_slots = $request["ram_slots"];
    	$workstation->ram_max_capacity = (int)$request["ram_max_capacity"]*1024;
    	$workstation->service_version = 0;
    
    	$saved = $workstation->save();
    
    	if($saved) {
        	
        	$addresses = $request["ip"];
        	foreach($addresses as $address) {
            	if ($address != "") {
            		$ip = new WsIps();
            		$ip->wsid = $workstation->id;
            		$ip->ip = $address;
            		$ip->save();
                }
            }
        
        	foreach($request["dns"] as $address) {
            	if ($address != "") {
            		$dns = new WsDns();
            		$dns->wsid = $workstation->id;
            		$dns->ip = $address;
            		$dns->save();
                }
            }
        
        	$i = 0;
        	foreach($request["disk_serial"] as $serial) {
            	if ($serial != "") {
                $hdd = new WsHarddrives();
                $hdd->wsid = $workstation->id;
                $hdd->serial = $serial;
                $hdd->mediatype = $request["disk_model"][$i];
                $hdd->model = $request["disk_model"][$i];
                $hdd->capacity = $request["disk_capacity"][$i];
                $hdd->save();
                $i = $i+1;
                }
            }
        
        	$i = 0;
        	foreach($request["monitor_serial"] as $serial) {
            	if ($serial != "") {
                $monitor = new WsMonitors();
            	$monitor->wsid = $workstation->id;
            	$monitor->serial = $serial;
            	$monitor->manufacturer = $request["monitor_manufacturer"][$i];
            	$monitor->name = $request["monitor_name"][$i];
            	$monitor->save();
            	$i = $i+1;
                }
            }
        
        	$event = new WsEvents();
        	$event->wsid = $workstation->id;
        	$event->event = "manually registered";
        	$event->save();
        
        	return redirect()->to('/workstations/'. $workstation->id);
    
        } else {
    
        	return back()->withInput(Input::all());
    	
        }
    
    }

	/*
 	 * Saves the workstation alias name if it has changed manually (Workstations)
     */
	public function editAlias($request) {
    	
    	$workstation = Workstations::where("id", $request["wsid"])->first();
        if($workstation == null) { return ""; }
    	
    	$workstation->alias = $request["alias"];
    	$workstation->save();
    	
    	return response()->json(["alias" => $workstation->alias]);
       
    }

	/*
 	 * Saves a field value of a workstation if it has changed (Workstations)
     */
	public function editWorkstation($request) {
    
    	if($request["table"] == "ws_monitors") {
        	$monitor = WsMonitors::where("id", $request["id"])->first();
        	if($monitor == null) { return "not found monitor"; }
        	$field = $request["field"];
    		$monitor->$field = $request["value"];
    		$monitor->save();
    		return "monitor OK";
        }
    	
    	if ($request["field"] == "") {
        	return "";
        }
    
    	
    
    	$workstation = Workstations::where("id", $request["wsid"])->first();
        if($workstation == null) { return "not found ws"; }
    
    	$field = $request["field"];
    	
    	$oldValue = $workstation->$field;
    	$newValue = $request["value"];
    	$field = $request["field"];
 		$workstation->$field = $request["value"];
    	$saved = $workstation->save();
        
    	if ($saved) {
        
        	$event = new WsEvents();
    		$event->wsid = $workstation->id;
    		$event->event = $field . " changed";
    		$event->description = $oldValue . " -> " . $newValue . " (". Auth::user()->username .")";
    		$event->save();
        	return "ws OK";
        } else {
        	return "ERROR";
        }
       
    }

	/*
 	 * Removes a workstation and all of its related data from the database and creates an HTML file in the Documents(Workstations/Actions)
     */
	public function archiveWorkstation($request) {
    	$wsid = $request["wsid"];
    	$workstation = Workstations::where("id", $wsid)->first();
    	if ($workstation == null) {
        	return "";
        }
    
    	$events = WsEvents::where("wsid", $wsid)->orderBy("created_at", "DESC")->get();
    	$connection = NetworkEdges::whereRaw("(target = CONCAT('ws', $workstation->id))")->first();
    	$printPeriod = \DB::table('ws_print_stats')->select(\DB::raw('CONCAT(MIN(created_at), " - ", MAX(created_at)) as period'))->where('wsid', $wsid)->first();
    	if (!empty($printPeriod)) {
    		$period = $printPeriod->period;
        } else {
        	$perion = "";
        }
    	
    	if ($connection == null) {
        	$conn = "N/A";
        } else {
        	$nd_id = filter_var($connection->source, FILTER_SANITIZE_NUMBER_INT);
        	$nd = NetworkDevices::where("id", $nd_id)->first();
        	$conn = $nd->alias . "(" . $nd->hardware . " - " . $nd->ports . "P)";
        }
        
    	$content = view('workstations.archive', compact('workstation', 'events', 'conn', 'period'))->render();
    	$filename = "arhivalt-munkaallomas-".$workstation->hostname."-".$workstation->id.".html";
    	//archív html létrehozása
    	file_put_contents(storage_path("documents/".$filename), $content);
    
    	//archív html fájl rögzítése dokumentumtárban
    	$doc = new Documents();
    	$doc->title = "Archivált munkaállomás - " . $workstation->alias . " - " . $workstation->serial . " - " . $workstation->invenory_id;
    	$doc->keywords = "archív,munkaállomás,".$workstation->alias.",".$workstation->serial.",".$workstation->hostname.",".$workstation->inventory_id.",".$workstation->hardware;
    	$doc->source = "generated";
    	$doc->filename = $filename;
    	$doc->filesize = filesize(storage_path("documents/".$filename));
    	$doc->signed_at = Carbon::now()->format("Y-m-d");
    	$doc->user_id = Auth::user()->id;
    	$doc->save();
    
    	//munkaállomás és adatainak törlése adatbázisból
    	//hálózati kapcsolatok
    	NetworkEdges::where("target", "ws".$wsid)->delete();
    	//perifériák (monitor, ram, hdd, cimkék, ip, dns, control log, felhasználói fiókok)
    	WsIps::where("wsid", $wsid)->delete();
    	WsMonitors::where("wsid", $wsid)->delete();
    	WsMemories::where("wsid", $wsid)->delete();
    	WsHarddrives::where("wsid", $wsid)->delete();
    	WsLabels::where("wsid", $wsid)->delete();
    	WsDns::where("wsid", $wsid)->delete();
    	WsControlLog::where("wsid", $wsid)->delete();
    	WsUserAccounts::where("wsid", $wsid)->delete();
    	//nyomtatók és nyomtatások
    	WsPrintStats::where("wsid", $wsid)->delete();
    	WsPrinters::where("wsid", $wsid)->delete();
    	//események
    	WsEvents::where("wsid", $wsid)->delete();
    	WsInterventions::where("wsid", $wsid)->delete();
    	//Távoli elérések törlése
    	WsConnections::where("wsid", $wsid)->delete();
    	//munkaállomás
    	Workstations::where("id", $wsid)->delete();
    	//parancsok
    	CommandWorkstations::where("wsid", $wsid)->delete();
    	return "OK";
    	
    }

	/*
 	 * Saves a command from the Console (Workstations/Console)
     */
	public function saveCommand($request) {
    	$command = $request["command"];
    	$alias = $request["alias"];
    	$script = new ConsoleScripts();
    	$script->alias = $alias;
    	$script->user_id = auth()->user()->id;
    	$script->code = $command;
    	
    	if($script->save()) {
        	return "OK";
        } else {
        	return null;
        }
    	
    }

	/*
 	 * Checks if an IP address is in the range of the registered subnets (IP Table)
     */
	public function isIPInSubnet($ip) {
    
    	$subnets = Subnets::all();
    
    	foreach($subnets as $sn) {
        	$ip = ip2long($ip);
    		$subnet = ip2long($sn->identifier);
    		$mask = -1 << (32 - $sn->mask);
        
        	$subnet &= $mask;
        	
        	if (($ip & $mask) == $subnet) {
            	return true;
            }
        }
    
    	return false;
    	
    }

	/*
 	 * Gives back the subnet from an IP address
     */
	public function getSubnetFromIP($ip) {
    
    	$subnets = Subnets::all();
    	$matchCount = 0;
    
    	foreach($subnets as $sn) {
        	$ip = ip2long($ip);
    		$subnet = ip2long($sn->identifier);
    		$mask = -1 << (32 - $sn->mask);
    		$subnet &= $mask;
        	if (($ip & $mask) == $subnet) {
            	return $sn->identifier . "/" . $sn->mask;
            }
        }
    
    	return false;
    	
    }

	/*
 	 * Gives back the first maching IP address of a workstation which is in the range one of the registered subnets
     */
	public function getFirstMatchingIp($wsid) {
    	$ipAddresses = WsIps::where('wsid', $wsid)->get();
    
    	foreach ($ipAddresses as $ip) {
        	if ($this->isIPInSubnet($ip->ip) == true) {
            	return $ip->ip;
            }
        }
    	
    	return null;
	
    }

	/*
 	 * Gives back the broadcast address from the subnet (Workstations/Console/WOL)
     */
	function getBroadcastAddressFromSubnet($subnet) {
    	list($subnetIp, $bits) = explode('/', $subnet);
    	$ip = ip2long($subnetIp);
    	$mask = -1 << (32 - $bits);
    	$broadcast = ($ip & $mask) | (~$mask);
    	return long2ip($broadcast);
	}

	/*
 	 * Form to create a new workstation filter (Dashboard/Suggested Interventions)
     */
	public function createFilter() {
    
    	return view("workstations.filter");
    
    }

	/*
 	 * Removes a workstation filter (Dashboard/Suggested Interventions)
     */
	public function deleteFilter($request) {
    
    	$filter = WsFilters::where("hash", $request["hash"])->first();
    
    	if (isset($filter)) {
        	
        	$deleted = $filter->delete();
        
        	if ($deleted) {
            
            	return "OK";
            
            }
        
        }
    
    	return "ERROR";
    
    }

	/*
 	 * Saves a new workstation filter (Dashboard/Suggested Interventions)
     */
	public function saveFilter(Request $request) {
 
    	$data = request()->except('_token');
    	
    	$rules = [
        	'filter_name' => 'required',
        	'cpuscore_value' => 'nullable|numeric',
    		'cpuage_value' => 'nullable|numeric',
    		'memory_value' => 'nullable|numeric',
        	'os_drive_free_space' => 'nullable|numeric',
        	'osupdate_value' => 'nullable|numeric',
    		'offline_value' => 'nullable|numeric',
    		'boottime_value' => 'nullable|numeric',
    		'uptime_value' => 'nullable|numeric',
    	];

    	$messages = [
    		'required' => __('all.validation.required'),
    		'numeric' => __('all.validation.numeric'),
    		'date' => __('all.validation.date'),
    		'ip' => __('all.validation.ip'),
    		'required_without_all' => __('all.validation.required_without_all'),
        	'regex' => __('all.validation.regex'),
        	'required_if' => __('all.validation.required'),
        ];
    
    	$validator = Validator::make($data, $rules, $messages);

    	if ($validator->fails()) {
        	return redirect()->back()->withErrors($validator)->withInput();
    	}
    
    
    	$name = $request["filter_name"];
    
    	$parameters = array();
        
    	if (!empty($request["brand_value"])) {
        	$parameters["brand_modificator"] = $request["brand_modificator"];
        	$parameters["brand_value"] = $request["brand_value"];
        }
    
        if (!empty($request["hostname_value"])) {
        	$parameters["hostname_modificator"] = $request["hostname_modificator"];
        	$parameters["hostname_value"] = $request["hostname_value"];
        }
    
    	if (!empty($request["workgroup_value"])) {
        	$parameters["workgroup_modificator"] = $request["workgroup_modificator"];
        	$parameters["workgroup_value"] = $request["workgroup_value"];
        }
    
    	if (!empty($request["cpuscore_value"])) {
        	$parameters["cpuscore_modificator"] = $request["cpuscore_modificator"];
        	$parameters["cpuscore_value"] = $request["cpuscore_value"];
        }
        
    	if (!empty($request["cpuage_value"])) {
        	$parameters["cpuage_modificator"] = $request["cpuage_modificator"];
        	$parameters["cpuage_value"] = $request["cpuage_value"];
        }
        
    	if (!empty($request["memory_value"])) {
        	$parameters["memory_modificator"] = $request["memory_modificator"];
        	$parameters["memory_value"] = $request["memory_value"];
        }
    	
    	if (!empty($request["os_drive_free_space"])) {
        	$parameters["os_drive_free_space"] = $request["os_drive_free_space"];
        }
    	
   	 	if (!empty($request["osname_value"])) {
        	$parameters["osname_value"] = $request["osname_value"];
        }
   
    
    	if (isset($request["disk"])) {
        	$parameters["disk"] = $request["disk"];
        }
    
    	if (isset($request["type"])) {
        	$parameters["type"] = $request["type"];
        }
    
    	if (!empty($request["label_value"])) {
        	$labels = array();
        	foreach(explode(",",$request["label_value"]) as $label) {
            	$labels[] = trim($label);
            }
        	$parameters["label_value"] = implode(",", $labels);
        	
        	$parameters["label_connection"] = $request["label_connection"];
        	$parameters["label_modificator"] = $request["label_modificator"];
        }
    
    	if (!empty($request["osupdate_value"])) {
        	$parameters["osupdate_value"] = $request["osupdate_value"];
        }
    	
    	if (!empty($request["offline_value"])) {
        	$parameters["offline_value"] = $request["offline_value"];
        }
    	
    	if (!empty($request["boottime_value"])) {
        	$parameters["boottime_value"] = $request["boottime_value"];
        }
    	
    	if (!empty($request["uptime_value"])) {
        	$parameters["uptime_value"] = $request["uptime_value"];
        }
    
    	if (isset($request["ipv6"])) {
        	$parameters["ipv6"] = $request["ipv6"];
        }
    
    	if (isset($request["serial"])) {
        	$parameters["serial"] = $request["serial"];
        }
    
    	if (isset($request["inventory"])) {
        	$parameters["inventory"] = $request["inventory"];
        }
    
    	if (isset($request["admin_account"])) {
        	$parameters["admin_account"] = $request["admin_account"];
        }
    
    	if (isset($request["support"])) {
        	$parameters["support"] = $request["support"];
        }
    
    	$filter = new WsFilters();
    
    	$filter->name = $name;
    	if(isset($request["filter_short_description"])) {
    		$filter->description = $request["filter_short_description"];
        }
        $filter->parameters = json_encode($parameters);
    	$filter->save();
    	$filter->hash = md5($filter->id);
    	$filter->save();
    	
    	return redirect()->to('/workstations/filter/'. $filter->hash);
    
    }

	/*
 	 * List of workstations (Workstations)
     */
	public function listWorkstations(Request $request) {
    
    	if(!auth()->user()->hasPermission('read-workstations')) { return redirect('dashboard'); }
    	
    
   		if (isset($request["filter"])) {
        	
        	switch($request["filter"]) {
            	case "ipconflict":
            		$ips = \DB::table('ws_ips')
    ->select('ip', \DB::raw('GROUP_CONCAT(DISTINCT wsid) as wsids'))
    ->whereNotIn('wsid', function($query) {
        $query->select('wsid')
              ->distinct()
              ->from('ws_ips')
              ->where('ip', 'LIKE', '10.0.10.%')
              ->orWhere('ip', 'LIKE', '192.168.56.1');
    })
    ->groupBy('ip')
    ->havingRaw('COUNT(DISTINCT wsid) > 1')
    ->get();
            		$wsid_array = array();
            		foreach($ips as $ip) {
                    	$wsids = explode(",", $ip->wsids);
                    	foreach($wsids as $wsid) {
                    		$wsid_array[] = $wsid;
                        }
                    }
            		
            		$workstations = Workstations::whereIn('id', $wsid_array)->get();
            		break;
            	case "macconflict":
            		$macs = \DB::table('workstations')
    ->select('active_mac', \DB::raw('GROUP_CONCAT(DISTINCT id) as ids'))
    ->groupBy('active_mac')
    ->havingRaw('COUNT(DISTINCT id) > 1 AND active_mac != null')
    ->get();
            		$wsid_array = array();
            		foreach($macs as $mac) {
                    	$wsids = explode(",", $mac->ids);
                    	foreach($wsids as $wsid) {
                        	if($mac != "") {
                    		$wsid_array[] = $wsid;
                            }
                        }
                    }
            		
            		$workstations = Workstations::whereIn('id', $wsid_array)->with(['labels','ips'])->get();
            		break;
            	case "heartbeatLoss":
            		$workstations = Workstations::heartbeatLoss()->with(['labels','ips'])->get();
            		break;
            	case "usb":
            		$workstations = Workstations::usb()->with(['labels','ips'])->get();
            		break;
            	case "online":
            		$workstations = Workstations::online()->with(['labels','ips'])->get();
            		break;
            	case "idle":
            		$workstations = Workstations::idle()->with(['labels','ips'])->get();
            		break;
            	case "offline":
            		$workstations = Workstations::offline()->with(['labels','ips'])->get();
            		break;
            	case "anydesk":
            		$workstations = Workstations::anydesk()->with(['labels','ips'])->get();
            		break;
            	case "rdp":
            		$workstations = Workstations::rdp()->with(['labels','ips'])->get();
            		break;
            	case "teamviewer":
            		$workstations = Workstations::teamviewer()->with(['labels','ips'])->get();
            		break;
            	case "vnc":
            		$workstations = Workstations::vnc()->with(['labels','ips'])->get();
            		break;
            	case "invalidhostnames":
            		$workstations = Workstations::whereRaw("CHAR_LENGTH(hostname) > 15")
    		->orWhereRaw("CHAR_LENGTH(hostname) = 0")
    		->orWhereRaw("hostname NOT REGEXP '^[a-zA-Z0-9]+([-][a-zA-Z0-9]+)*$'")
    		->orWhereRaw("hostname LIKE '-%'")
    		->orWhereRaw("hostname LIKE '%-'")->get();
            		break;
            	default:
        			$filter = WsFilters::where("hash", $request["filter"])->first();
    	    		
                    if (isset($filter)) {
                    	$name = $filter->name;
            	$parameters = json_decode($filter->parameters);
            	$hash = $filter->hash;
            	$description = array();
            	$count = 0;
            	$query = "";
            	$query = Workstations::with(['labels', 'ips']);
            
            	if (isset($parameters->cpuscore_value)) {
                	
                	$value = $parameters->cpuscore_value;
                	$modificator = $parameters->cpuscore_modificator;
                	
                	if ($modificator == "less") {
                    
                    	$query = $query->selectRaw("workstations.*, cpu_score AS cpu_points")->where("cpu_score", "<=", $value)->where("cpu_score", "!=", 0);
                    
                    }
                
                	if ($modificator == "more") {
                    
                    	$query = $query->selectRaw("workstations.*, cpu_score AS cpu_points")->where("cpu_score", ">=", $value);
                    
                    }
                
                
                }
            
            	if (isset($parameters->boottime_value)) {
                	
                	$value = $parameters->boottime_value;
                	
                	$query = $query->selectRaw("workstations.*, boot_time AS boot_seconds")->where("boot_time", ">", $value);
                                    
                }
                    
                if (isset($parameters->uptime_value)) {
                	
                	$value = $parameters->uptime_value;
                	
                	$query = $query->selectRaw("workstations.*, DATEDIFF(heartbeat, bootup_at) AS uptime_days")->whereNotNull("startup_at")->having("uptime_days", ">", $value);
                                    
                }
            
            	if (isset($parameters->cpuage_value)) {
                	
                	$value = $parameters->cpuage_value;
      				$modificator = $parameters->cpuage_modificator;
                	
                	if ($modificator == "less") {
                    
                    	$year = Carbon::now()->subYears($value);
                    
                    	$query = $query->selectRaw("workstations.*, TIMESTAMPDIFF(YEAR, cpu_release_date, NOW()) AS cpu_age")->whereYear("cpu_release_date", ">", $year);
                    	
                    }
                
                	if ($modificator == "more") {
                    
                    	$year = Carbon::now()->subYears($value);
                    
                    	$query = $query->selectRaw("workstations.*, TIMESTAMPDIFF(YEAR, cpu_release_date, NOW()) AS cpu_age")->whereYear("cpu_release_date", "<", $year);
                    
                    }
                
                	if ($modificator == "exactly") {
                    
                    	$year = Carbon::now()->subYears($value);
                    
                    	$query = $query->selectRaw("workstations.*, TIMESTAMPDIFF(YEAR, cpu_release_date, NOW()) AS cpu_age")->whereYear("cpu_release_date", "=", $year);
                    
                    }
                
                }
            
            	if (isset($parameters->memory_value)) {
                	
                	$value = $parameters->memory_value;
      				$modificator = $parameters->memory_modificator;
                	$memory = $value * 1024;	
                
                
                	if ($modificator == "less") {
                    
                    	$query = $query->selectRaw("workstations.*, CAST((workstations.ram/1024) as DECIMAL(10,1)) as memory")->where("ram", "<", $memory);
                    	
                    }
                
                	if ($modificator == "more") {
                    
                    	$year = Carbon::now()->subYears($value);
                    
                    	$query = $query->selectRaw("workstations.*, CAST((workstations.ram/1024) as DECIMAL(10,1)) as memory")->where("ram", ">", $memory);
                    
                    }
                
                	if ($modificator == "exactly") {
                    
                    	$query = $query->selectRaw("workstations.*, CAST((workstations.ram/1024) as DECIMAL(10,1)) as memory")->where("ram", "=", $memory);
                    
                    }
                
                }
                    
                if (isset($parameters->os_drive_free_space)) {
                	
                	$value = $parameters->os_drive_free_space;
      				
                	$query = $query->selectRaw("workstations.*, workstations.os_drive_free_space as freespace")->where("os_drive_free_space", "<=", $value)->orderBy("freespace", "ASC");
                    
                }
            
            	if (isset($parameters->brand_value)) {
                
                	$value = $parameters->brand_value;
                	$modificator = $parameters->brand_modificator;
                	
                	if ($modificator == "contains") {
                    
                    	$query = $query->selectRaw("workstations.*, workstations.hardware as brand")->where('hardware', 'LIKE', "%{$value}%");
                    	
                    }
                
                	if ($modificator == "not-contains") {
                    
                    	$query = $query->selectRaw("workstations.*, workstations.hardware as brand")->where('hardware', 'NOT LIKE', '%'. $value. '%');
                    	
                    }
                
                	if ($modificator == "exactly") {
                    
                    	$query = $query->selectRaw("workstations.*, workstations.hardware as brand")->where('hardware', '=', $value);
                    	
                    }
                
                }
                    
                if (isset($parameters->hostname_value)) {
                
                	$value = $parameters->hostname_value;
                	$modificator = $parameters->hostname_modificator;
                	
                	if ($modificator == "contains") {
                    
                    	$query = $query->where('hostname', 'LIKE', "%" . $value. "%");
                    	
                    }
                
                	if ($modificator == "not-contains") {
                    
                    	$query = $query->where('hostname', 'NOT LIKE', '%'. $value. '%');
                    	
                    }
                
                	if ($modificator == "exactly") {
                    
                    	$query = $query->where('hostname', '=', $value);
                    	
                    }
                
                }
            
            	if (isset($parameters->workgroup_value)) {
                
                	$value = $parameters->workgroup_value;
                	$modificator = $parameters->workgroup_modificator;
                	
                	if ($modificator == "contains") {
                    
                    	$query = $query->where('workgroup', 'LIKE', "%" . $value. "%");
                    	
                    }
                
                	if ($modificator == "not-contains") {
                    
                    	$query = $query->where('workgroup', 'NOT LIKE', '%'. $value. '%');
                    	
                    }
                
                	if ($modificator == "exactly") {
                    
                    	$query = $query->where('workgroup', '=', $value);
                    	
                    }
                
                }
            
                if (isset($parameters->osname_value)) {
                
                	$value = $parameters->osname_value;
                	$modificator = "contains";
                	
                	if ($modificator == "contains") {
                    
                    	$query = $query->where('os', 'LIKE', "%{$value}%");
                    	
                    }
                
                }    
                    
            	if (isset($parameters->type)) {
                	
                	$types = implode(", ", $parameters->type);
                
                	$query = $query->whereIn("type", $parameters->type);
                
                }
            
            	if (isset($parameters->disk)) {
                	
                	$types = implode(", ", $parameters->disk);
                
                	$query = $query->selectRaw("workstations.*, ws_harddrives.mediatype AS disk, ws_harddrives.mediatype")->rightJoin("ws_harddrives", "ws_harddrives.wsid", "=", "workstations.id")->whereIn("ws_harddrives.mediatype", $parameters->disk)->groupBy("workstations.id");
                	
                }
            
            	if (isset($parameters->serial)) {
                	
                	$query = $query->whereNull("serial");
                
                }
            
            	if (isset($parameters->inventory)) {
                	
                	$query = $query->whereNull("inventory_id");
                
                }
            
            	if (isset($parameters->offline_value)) {
                
                	$value = $parameters->offline_value;
                
                	$date = Carbon::now()->subDays($value)->format("Y-m-d H:i:s");
                
                	$query = $query->selectRaw("workstations.*, DATEDIFF(NOW(), heartbeat) AS offline_days")->where("heartbeat", "<", $date)->where("service_version", "!=", "0")->orderBy("offline_days", "ASC");
                
                }
            
            	if (isset($parameters->osupdate_value)) {
                
                	$value = $parameters->osupdate_value;
                
                	$date = Carbon::now()->subMonths($value)->format("Y-m-d H:i:s");
                
                	$query = $query->selectRaw("workstations.*, TIMESTAMPDIFF(MONTH, workstations.wu_installed, NOW()) AS os_updated_months")->where("workstations.wu_installed", "<", $date)->where("workstations.service_version", "!=", "0");
                
                }
            
            	if (isset($parameters->admin_account)) {
                
                	$adminUserNames = GlobalSettings::where("name", "exclude-admin-username-list")->first()->value;
    				$adminUserNames = str_replace(' ', '', $adminUserNames);
    				$excludedUserNames = explode(",", $adminUserNames);
    
    				$query = $query->selectRaw("workstations.*, ws_user_accounts.is_admin, ws_user_accounts.username")->rightJoin("ws_user_accounts", "ws_user_accounts.wsid", "=", "workstations.id")->where("ws_user_accounts.is_admin", 1)->whereNotIn('ws_user_accounts.username', $excludedUserNames)->groupBy("workstations.id");
                
                }
            
            	if (isset($parameters->label_value)) {
                
                	$values = explode(",", $parameters->label_value);
                	$modificator = $parameters->label_modificator;
                	$connection = $parameters->label_connection;
                	
                	$query = $query->selectRaw("GROUP_CONCAT(ws_labels.name) as label_list, workstations.*")->rightJoin("ws_labels", "ws_labels.wsid", "=", "workstations.id")->groupBy("workstations.id");
                
                	if ($modificator == "contains") {
                    
                    	if ($connection == "or") {
                    	
                        	$having = array();
                        
                        	foreach($values as $value) {
                            
                            		$having[] = "label_list LIKE '%{$value}%'";
                            
                            }
                        
                        	$query = $query->havingRaw(implode(" OR ", $having));
                        
                        } else {
                        
                        	$having = array();
                        
                        	foreach($values as $value) {
                            
                            		$having[] = "label_list LIKE '%{$value}%'";
                            
                            }
                        
                        	$query = $query->havingRaw(implode(" AND ", $having));
                        
                        }
                    
                    } else {
                    
                    	if ($connection == "or") {
                        
                        	$having = array();
                        
                        	foreach($values as $value) {
                            
                            		$having[] = "label_list NOT LIKE '%{$value}%'";
                            
                            }
                        
                        	$query = $query->havingRaw(implode(" OR ", $having));
                        
                        	                        
                        } else {
                        
                        	$having = array();
                        
                        	foreach($values as $value) {
                            
                            		$having[] = "label_list NOT LIKE '%{$value}%'";
                            
                            }
                        
                        	$query = $query->havingRaw(implode(" AND ", $having));
                        
                        }
                    }
                
                }
            
            	if (isset($parameters->ipv6)) {
                
                	$query = $query->select("ws_ips.ip", "workstations.*")->join("ws_ips", "ws_ips.wsid", "=", "workstations.id")->whereRaw("INET6_ATON(ws_ips.ip) IS NULL")->groupBy("workstations.id");
                	
                }
            
            	if (isset($parameters->support)) {
                
                	$date = Carbon::now()->format("Y-m-d");
                
                	$query = $query->select("operating_systems.last_support_date", "workstations.*")->join("operating_systems", "operating_systems.name", "=", "workstations.os")->where("operating_systems.last_support_date", "<", $date);
                	
                }
            	
            	$workstations = $query->orderBy('alias', 'ASC')->get();
                
                    } else {
                    	$workstations = Workstations::with(['labels', 'ips'])->orderBy('alias', 'ASC')->get();
                    }
                    break;
            }
        	
        
        } else {
    		$workstations = Workstations::with(['labels', 'ips'])->orderBy('alias', 'ASC')->get();
        }
    
    	$keyword = $request["keyword"];
    	
    	return view('workstations.list', [ "workstations" => $workstations, "filter" => $request["filter"], "keyword" => $keyword]);
	}

	/*
 	 * List of connected Displays of the Workstations (Assets/Displays)
     */
	public function listDisplays(Request $request) {

    		if(!auth()->user()->hasPermission('read-monitors')) { return redirect('dashboard'); }
    
    		$displays = WsMonitors::all();
	    	return view('workstations.displays', [ "displays" => $displays ]);
    
    }
	
	/*
 	 * List of connected Printers of the Workstations (Assets/Printers)
     */
	public function listPrinters(Request $request) {

    		if(!auth()->user()->hasPermission('read-printers')) { return redirect('dashboard'); }
    
    		$printers = WsPrinters::get();
	    	return view('workstations.printers', [ "printers" => $printers ]);
    
    }

	/*
 	 * View of a workstation with every related data (Workstations/Workstation)
     */
	public function getWorkstation(Request $request) {
    		
    	if(!auth()->user()->hasPermission('read-workstation')) { return redirect('dashboard'); }
    
    	$id = $request["id"];
    	$workstation = Workstations::with(['labels', 'ips', 'printers', 'monitors', 'dns', 'memories', 'accounts', 'events'])->where("id", $id)->first();
    	if (!isset($workstation)) {
        	return "Gép nem található.";
        }
            	
    	$networkdevices = NetworkDevices::orderBy('alias', 'ASC')->get();
    	
    	$networkEdge = NetworkEdges::whereRaw("(target = CONCAT('ws', $workstation->id))")->first();
        
    	if (isset($networkEdge)) {
        	$connection = $networkEdge->source;
        } else {
	    	$networkEdge = NetworkEdges::whereRaw("(source = CONCAT('ws', $workstation->id))")->first();
                		
        	if (isset($networkEdge)) {
            	$connection = $networkEdge->target;
            } else {
	        	$connection = null;
            }
    	}
		        
    	$connections = WsConnections::where("wsid", $id)->get();
    			
    			$scripts = ConsoleScripts::orderBy("alias", "ASC")->get();
            	$cpu_score = $workstation->cpu_score;
            	
    			if ($this->getFirstMatchingIp($id) != null) {
                	$isReachableByLAN = true;
                	$vncIP = $this->getFirstMatchingIp($id);
                } else {
                	$isReachableByLAN = false;
                	$vncIP = null;
                }
    
    			return view("workstations.workstation", ["workstation" => $workstation, "networkdevices" => $networkdevices, "connection" => $connection, "connections" => $connections, "working" => [], "scripts" => $scripts, "cpu_score" => $cpu_score, "isLAN" => $isReachableByLAN, "vncIP" => $vncIP]);
	}
	
	/*
 	 * Function to send Powershell or CMD commands to a workstation through pot 8080 (Workstation/Console)
     */
	public function command($request, $log = true) {
    	
    	if(isset(Auth::user()->id)) {
    		$user_id = Auth::user()->id;
        	if(!auth()->user()->hasPermission('write-workstation-command')) { return "You have no permission to send command to workstations."; }
        }
    
    	$masterKey = 'base64:'.base64_encode(env("MASTER_KEY"));
    	$encryptionKey = WsKeys::where("wsid", $request["id"])->first();
    	if ($encryptionKey != null) {
    	   	$cipher = 'AES-256-CBC';
			$password = Crypt::decrypt($encryptionKey->encryption_key, $unserialize = true, $masterKey, $cipher);
        }
    
    	$method = 'aes-256-cbc';

		$password = substr(hash('sha256', $password, true), 0, 32);
		
		$iv = chr(0x30) . chr(0x30) . chr(0x30) . chr(0x30) . chr(0x30) . chr(0x30) . chr(0x30) . chr(0x30) . chr(0x30) . chr(0x30) . chr(0x30) . chr(0x30) . chr(0x30) . chr(0x30) . chr(0x30) . chr(0x30);
    	
    	if ($log === true) {
    		$wsControlLog = new WsControlLog();
    		$wsControlLog->wsid = $request["id"];
    		$wsControlLog->log = $request["command"];
    		$wsControlLog->user_id = $user_id;
    		$wsControlLog->save();
        }
    
    	try {
        	
        	$id = $request['id'];
        	$exceptions = WsLabels::select("wsid")->where("name", "noscript")->pluck("wsid")->toArray();	
        	  
        	if(in_array($id, $exceptions)) {
		    	return "BigLanServer>> Access denied. Workstation cannot controlled remotely.";
            }
        
            $command = base64_encode(openssl_encrypt(str_replace("\\","\\\\",$request["command"]), $method, $password, OPENSSL_RAW_DATA, $iv));
        	
        	$workstation = Workstations::where("id", $id)->first();
        	if ($workstation == null) {
            	$wsControlLog = new WsControlLog();
    			$wsControlLog->wsid = $request["id"];
    			$wsControlLog->outbound = 0;
    			$wsControlLog->log = "BigLanServer>> Workstation with id " . $id . " not found.";
    			$wsControlLog->user_id = $user_id;
    			$wsControlLog->save();
            	return "BigLan Server>> Workstation with id " . $id . " not found.";
            }
        	$ip = $this->getFirstMatchingIp($workstation->id);
            if ($this->getFirstMatchingIp($workstation->id) == null) {
        		$wsControlLog = new WsControlLog();
    			$wsControlLog->wsid = $request["id"];
    			$wsControlLog->outbound = 0;
    			$wsControlLog->log = "BigLan Server>> IP address not in registered subnet ranges.";
    			$wsControlLog->user_id = $user_id;
    			$wsControlLog->save();
            	return "BigLan Server>> IP address not in registered subnet ranges.";
            }
        	
        	$response=null;
        	try {
        		$fp = @fsockopen($ip, 8080, $errno, $errstr,15);
            } catch(Exception $ex) {
            	$fp = 0;
            }
			if (!$fp) {
    			$response .= "$errstr ($errno)<br />\n";
			} else {
    			fwrite($fp, $command);
            	stream_set_blocking($fp, TRUE); 
            	stream_set_timeout($fp, 15);
            	while (!feof($fp)) {
        			$response .= fgets($fp, 128);
                	$info = stream_get_meta_data($fp); 
                }
            	
    			fclose($fp);
            	
            	if ($info['timed_out']) {
                	$wsControlLog = new WsControlLog();
    				$wsControlLog->wsid = $request["id"];
    				$wsControlLog->outbound = 0;
    				$wsControlLog->log = "BigLan Server>> Connection timed out.";
    				$wsControlLog->user_id = $user_id;
    				$wsControlLog->save();
                	return "BigLan Server>> Connection timed out.";
                } else {
                	
                	$result = "".openssl_decrypt(base64_decode($response), $method, $password, OPENSSL_RAW_DATA, $iv);
                	
                	if ($log === true) {
                		$wsControlLog = new WsControlLog();
    					$wsControlLog->wsid = $request["id"];
    					$wsControlLog->outbound = 0;
    					$wsControlLog->log = $result;
    					$wsControlLog->user_id = $user_id;
    					$wsControlLog->save();
                    }
                	
                	if ($result != "" && isset($id)) {
                		$this->checkResultContent($id, $result);
                    }
                
                	return $result;
                }
			}
        
        } catch(\Exception $e) {
        	
        	\Log::error($e);
        	$wsControlLog = new WsControlLog();
           	$wsControlLog->wsid = $request["id"];
            $wsControlLog->outbound = 0;
            $wsControlLog->log = "BigLanServer>> Error in PHP.";
            $wsControlLog->user_id = $user_id;
    		$wsControlLog->save();
        	return "BigLanServer>> Error in PHP.";
        }
    
    }

	/*
 	 * Create a label automatically for a workstation based on a command's result (Workstation/Console)
     */
	public function checkResultContent($wsid, $content) {
    
    	$pos = strpos($content, "makeLabel=");

    	if ($pos !== false) {
        	$labelValue = substr($content, $pos + strlen("makeLabel="));
        
        	$labelName = strip_tags($labelValue);

        	$label["wsid"] = $wsid;
        	$label["name"] = $labelName;
        	$this->createWsLabel($label);
        
        }
    		
    }
	
	/*
 	 * Saves the network endpoint connection of a workstation (Workstation/Network Connection)
     */
	public function editNetworkEdge($request) {
    	
    	$networkEdge = NetworkEdges::where('target', 'ws' . $request['wsid'])->first();
    	
    	if ( isset($networkEdge) ) {
    		$networkEdge->source = "nd".$request["connection"];
        } else {
        	$networkEdge = NetworkEdges::where('source', 'ws' . $request['wsid'])->first();
        	if ( isset($networkEdge) ) {
            	$networkEdge->target = "nd".$request["connection"];
            } else {
            	$networkEdge = new NetworkEdges();
        		$networkEdge->source = "nd" .$request['target'];
        		$networkEdge->target = "ws" .$request["wsid"];
            }
        }
    
    	$saved = $networkEdge->save();
    
    	if ($saved) {
        	return "OK";
        } else {
        	return "ERROR";
        }
    
    }

}
