<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\UserSettings;
use Illuminate\Http\Request;
use App\Models\Workstations;
use App\Models\WsEvents;
use App\Models\WsInterventions;
use App\Models\WsIps;
use App\Models\NetworkPrinters;
use App\Models\WsUserAccounts;
use App\Models\WsHarddrives;
use App\Models\ServiceAccesses;
use App\Models\WsPrints;
use App\Models\WsLabels;
use App\Models\WsFilters;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Notifications;
use App\Models\GlobalSettings;

class DashboardController extends Controller
{
	
	/*
	 * Processing incoming payloads based on "action" 
	 */
	public function payload(Request $request) {
    	$action = $request["action"];
    
    	switch($action){
        	case "saveBlock":
         		return $this->saveBlock($request);
        		break;
        	case "deleteBlock":
         		return $this->deleteBlock($request);
        		break;
            case "viewGeneralStatistics":
         		return $this->generalDashboardStatistics();
        		break;
            case "getNotifications":
         		return $this->getNotifications();
        		break;
            default:
    			return $this->generalDashboardStatistics();
    			break;
        }
    
    }

	/*
	 * Stores a user defined block (Dashboard) 
	 */
	public function saveBlock($request) {
    
    	$userSettings = new UserSettings();
    	$userSettings->user_id = Auth::user()->id;
    	$userSettings->name = "dashboard-block";
    	$userSettings->value = array();	
    	$userSettings->value = json_encode(array(
        	"type" => $request["type"],
        	"data" => $request["data"]
        ));
		
    	$saved = $userSettings->save();
    
    	if($saved) {
        	return "OK";
        }
    
    	return "ERROR";
    
    }

	/*
	 * Removes a user defined block (Dashboard) 
	 */
	public function deleteBlock($request) {
    
    	$user_id = Auth::user()->id;
    	$id = $request["id"];
    	$block = UserSettings::where("id", $id)->where("user_id", $user_id)->first();
    
    	if (isset($block)) {
        	$delete = $block->delete();
        	if ($delete) {
            	return "OK";
            } else {
            	return "ERROR";
            }
        }
        
    	return "ERROR";
    	
    }

	/*
	 * Sorting by timestamp helper function 
	 */
	public function sortByDate($a, $b)
		{
    		$a = $a['timestamp'];
    		$b = $b['timestamp'];

    		if ($a == $b) return 0;
    		return ($a > $b) ? -1 : 1;
		}

	/*
	 * Shows notification badges for user (Menu) 
	 */
	public function getNotifications() {
    	
    	$services = Notifications::where("triggered", "=", "1")->where("monitored", "=", "1")->count();
    	$heartbeatlosses = Workstations::heartBeatLoss()->count();
    
    	return response()->json([
                        "notifications" => $services,
        				"heartbeatlosses" => $heartbeatlosses
        ]);
    }

	/*
	 * Gives back all the information Dashboard shows (Dashboard) 
	 */
	public function generalDashboardStatistics() {
    	
    	$all = Workstations::count();
    	$online = Workstations::online()->count();
    	$offline = Workstations::offline()->count();
    	$heartbeatLoss = Workstations::heartBeatLoss()->count();
    	$idle = Workstations::idle()->count();
    	$teamviewer = Workstations::teamviewer()->count();
    	$vnc = Workstations::vnc()->count();
    	$anydesk = Workstations::anydesk()->count();
    	$rdp = Workstations::rdp()->count();
    	$usb = Workstations::usb()->count();
    	
    
    	$ip_conflict = DB::table('ws_ips')
    		->select('ip', DB::raw('GROUP_CONCAT(DISTINCT wsid) as wsids'))
    		->whereNotIn('wsid', function ($query) {
        		$query->select('wsid')
            		->from('ws_ips')
            		->where('ip', 'LIKE', '10.0.10.%')
            		->orWhere('ip', 'LIKE', '192.168.56.1');
    			})
    		->groupBy('ip')
    		->havingRaw('COUNT(DISTINCT wsid) > 1')
    		->get();
    
    	$mac_conflict = \DB::table('workstations')->select('active_mac', \DB::raw('GROUP_CONCAT(DISTINCT id) as ids'))->groupBy('active_mac')->havingRaw('COUNT(DISTINCT id) > 1 AND active_mac != null')->get();
    
    	$printersBlackToner5 = NetworkPrinters::whereRaw("black_toner_level BETWEEN 0 AND 5")->where("ip", "!=", "")->count();
    	$printersBlackToner20 = NetworkPrinters::whereRaw("black_toner_level BETWEEN 6 AND 10")->where("ip", "!=", "")->count();
    	$printers = NetworkPrinters::get()->count();
    	$supply_link = GlobalSettings::where("name","network-printer-supply-order-link")->first();
    
    	$event_stream = [];
    	$events = WsEvents::whereNotIn("event", ["lock","unlock"])->take(50)->orderBy("created_at", "DESC")->get();
    	
    	if($events->count() > 0) {
    	
        	$lastWsEventDateTime = Carbon::parse(last($events->toArray())["created_at"])->format("Y-m-d H:i:s");	
    
        	$event_stream = $events->map(function ($event) {
    		$name = $event->wsid != 0 ? $event->workstation->alias : "Ismeretlen";
    		return [
        		"id" => $event->wsid ?? 0,
        		"event" => $event->event,
        		"name" => $name,
        		"type" => "ws",
        		"timestamp" => Carbon::parse($event->created_at),
        		"datetime" => Carbon::parse($event->created_at)->format("H:i:s"),
        		"description" => $event->description
    		];
			})->toArray();
        
        }
    
    	$operation_stream = array();
		$operation_events = WsInterventions::select('wsid', 'event', 'created_at', 'description')
    		->with('workstation')
    		->where("event", "LIKE" , "intervention")
    		->where("created_at", ">=", Carbon::now()->subMonths(1))
    		->orderBy("created_at", "DESC")
    		->get();
		foreach($operation_events as $o_event) {
    		if(isset($o_event) && $o_event->wsid != 0) {
        		$operation_stream[] = array(
            		"id" => $o_event->wsid ,
            		"event" => $o_event->event,
            		"name" => $o_event->workstation->alias,
            		"type" => "ws",
            		"timestamp" => Carbon::parse($o_event->created_at),
            		"datetime" => Carbon::parse($o_event->created_at)->format("H:i:s"),
            		"description" => $o_event->description
        		);
    		}
		}
    
    	$userBlocks = UserSettings::where("user_id", Auth::id())->where("name", "dashboard-block")->get();
    
    	$blocks = array();
    
    	foreach($userBlocks as $userBlock) {
        	$data = json_decode($userBlock->value);
        	$data->id = $userBlock->id;
        	if($data->type == "notifications") { 
        		$blocks[] = $data;
            }
        }
    	
    	if(count($blocks) > 0) {
    		foreach($blocks as $block) {
            	if ($block->type == "notifications") {
                	$ids = array_map(function($item) {
    					return $item->id;
					}, $block->data);
                	$notifications = Notifications::whereIn("id", $ids)->get();
                	$block->data = array();
                	foreach($notifications as $notification) {
                    	$block->data[] = array(
                        	"id" => $notification->id,
                        	"name" => $notification->name,
                        	"alias" => $notification->alias,
                        	"triggered" => $notification->triggered,
                        	"value" => $notification->last_value,
                        	"unit" => $notification->unit
                        ); 
                    }
                }
            }
        }
    
    	$invalidHostnames = Workstations::whereRaw("CHAR_LENGTH(hostname) > 15")
    		->orWhereRaw("CHAR_LENGTH(hostname) = 0")
    		->orWhereRaw("hostname NOT REGEXP '^[a-zA-Z0-9]+([-][a-zA-Z0-9]+)*$'")
    		->orWhereRaw("hostname LIKE '-%'")
    		->orWhereRaw("hostname LIKE '%-'")
    		->count();
    
    	$dashboard = array(
                        "ip-conflict" => count($ip_conflict),
        				"mac-conflict" => count($mac_conflict),
        				"ws-all" => $all,
        				"ws-online" => $online,
        				"ws-offline" => $offline,
        				"ws-heartbeatloss" => $heartbeatLoss,
                        "ws-idle" => $idle,
                        "teamviewer" => $teamviewer,
                        "anydesk" => $anydesk,
        				"rdp" => $rdp,
                        "vnc" => $vnc,
                        "usb" => $usb,
        				"invalid-hostnames" => $invalidHostnames,
                        "printers" => $printers,
        				"printers-black-toner-5" => $printersBlackToner5,
                        "printers-black-toner-20" => $printersBlackToner20,
        );
    
    	return response()->json([
        					"dashboard" => $dashboard,
                        	"printerSupplyURL" => $supply_link,
        					"event_stream" => $event_stream,
        					"operation_stream" => $operation_stream,
                        	"blocks" => $blocks,
                        ]);
    
    }

	/*
	 * Dashboard view with all the workstation filter cards (Dashboard) 
	 */
	public function viewDashboard() {
    	
    	$userBlocks = UserSettings::where("user_id", Auth::id())->where("name", "dashboard-block")->get();
    
    	$blocks = array();
    
    	foreach($userBlocks as $userBlock) {
        	$data = json_decode($userBlock->value);
        	$data->id = $userBlock->id;
        	$blocks[] = $data;
        }
    	
    	if(count($blocks) > 0) {
    		foreach($blocks as $block) {
            	if ($block->type == "notifications") {
                	$ids = array_map(function($item) {
    					return $item->id;
					}, $block->data);
                	$notifications = Notifications::whereIn("id", $ids)->get();
                	$block->data = array();
                	foreach($notifications as $notification) {
                    	$block->data[] = array(
                        	"id" => $notification->id,
                        	"name" => $notification->name,
                        	"alias" => $notification->alias,
                        	"triggered" => $notification->triggered,
                        	"value" => $notification->last_value,
                        	"unit" => $notification->unit
                        ); 
                    }
                }
            }
        }
    
    	$supply_link = GlobalSettings::where("name","network-printer-supply-order-link")->first();
    	
    	$problems = [];
    
    	$filters = WsFilters::get();
    	$interventions = array();	
    
    	if(isset($filters)) {
        
        	foreach($filters as $filter) {
            
            	$name = $filter->name;
            	$shortDescription = $filter->description;
            	$parameters = json_decode($filter->parameters);
            	$hash = $filter->hash;
            	$description = array();
            	$count = 0;
            	$query = "";
            	$query = Workstations::query();
            
            	if (isset($parameters->cpuscore_value)) {
                	
                	$value = $parameters->cpuscore_value;
                	$modificator = $parameters->cpuscore_modificator;
                	
                	if ($modificator == "less") {
                    
                    	$query = $query->where("cpu_score", "<=", $value)->where("cpu_score", "!=", 0);
                    	
                    	$description[] = __('all.dashboard.filter_cpu_score_less', ['value' => $value]);
                    
                    }
                
                	if ($modificator == "more") {
                    
                    	$query = $query->where("cpu_score", ">=", $value);
                    
                    	$description[] = __('all.dashboard.filter_cpu_score_more', ['value' => $value]);
                    }
                
                
                }
            
            	if (isset($parameters->boottime_value)) {
                	
                	$value = $parameters->boottime_value;
                	
                	$query = $query->where("boot_time", ">", $value);
                    	
                    $description[] = __('all.dashboard.filter_boottime_more', ['seconds' => $value]);
                                    
                }
            
            	if (isset($parameters->uptime_value)) {
                	
                	$value = $parameters->uptime_value;
                	
                	$query = $query->selectRaw("workstations.*, DATEDIFF(workstations.heartbeat, workstations.bootup_at) AS uptime_days")->whereNotNull("workstations.startup_at")->having("uptime_days", ">", $value);
                
                    $description[] = __('all.dashboard.filter_uptime_more', ['days' => $value]);
                
                
                }
            
            	if (isset($parameters->cpuage_value)) {
                	
                	$value = $parameters->cpuage_value;
      				$modificator = $parameters->cpuage_modificator;
                	
                	if ($modificator == "less") {
                    
                    	$year = Carbon::now()->subYears($value);
                    
                    	$query = $query->whereYear("cpu_release_date", ">", $year);
                    	
                    	$description[] = __('all.dashboard.filter_cpu_age_less', ['age' => $value]);
                    
                    }
                
                	if ($modificator == "more") {
                    
                    	$year = Carbon::now()->subYears($value);
                    
                    	$query = $query->whereYear("cpu_release_date", "<", $year);
                    
                    	$description[] = __('all.dashboard.filter_cpu_age_more', ['age' => $value]);
                    }
                
                	if ($modificator == "exactly") {
                    
                    	$year = Carbon::now()->subYears($value);
                    
                    	$query = $query->whereYear("cpu_release_date", "=", $year);
                    
                    	$description[] = __('all.dashboard.filter_cpu_age_exactly', ['age' => $value]);
                    }
                
                
                }
            
            	if (isset($parameters->memory_value)) {
                	
                	$value = $parameters->memory_value;
      				$modificator = $parameters->memory_modificator;
                	$memory = $value * 1024;	
                
                
                	if ($modificator == "less") {
                    
                    	$query = $query->where("ram", "<", $memory);
                    	
                    	$description[] = __('all.dashboard.filter_memory_less', ['memory' => $value]);
                    
                    }
                
                	if ($modificator == "more") {
                    
                    	$year = Carbon::now()->subYears($value);
                    
                    	$query = $query->where("ram", ">", $memory);
                    
                    	$description[] = __('all.dashboard.filter_memory_more', ['memory' => $value]);
                    }
                
                	if ($modificator == "exactly") {
                    
                    	$query = $query->where("ram", "=", $memory);
                    
                    	$description[] = __('all.dashboard.filter_memory_exactly', ['memory' => $value]);
                    }
                
                
                }
            
            	if (isset($parameters->os_drive_free_space)) {
                	
                	$value = $parameters->os_drive_free_space;
      				
                	$query = $query->where("os_drive_free_space", "<=", $value);
                    	
                    $description[] = __('all.dashboard.filter_os_drive_free_space_less_or_equal', ['space' => $value]);
                    
                }
            
            	if (isset($parameters->brand_value)) {
                
                	$value = $parameters->brand_value;
                	$modificator = $parameters->brand_modificator;
                	
                	if ($modificator == "contains") {
                    
                    	$query = $query->where('hardware', 'LIKE', "%{$value}%");
                    	
                    	$description[] = __('all.dashboard.filter_brand_contains', ['value' => $value]);
                    
                    }
                
                	if ($modificator == "not-contains") {
                    
                    	$query = $query->where('hardware', 'NOT LIKE', '%'. $value. '%');
                    	
                    	$description[] = __('all.dashboard.filter_brand_not_contains', ['value' => $value]);
                    
                    }
                
                	if ($modificator == "exactly") {
                    
                    	$query = $query->where('hardware', '=', $value);
                    	
                    	$description[] = __('all.dashboard.filter_brand_exactly', ['value' => $value]);
                    
                    }
                
                
                }
            
            	if (isset($parameters->hostname_value)) {
                
                	$value = $parameters->hostname_value;
                	$modificator = $parameters->hostname_modificator;
                	
                	if ($modificator == "contains") {
                    
                    	$query = $query->where('hostname', 'LIKE', "%" . $value. "%");
                    	
                    	$description[] = __('all.dashboard.filter_hostname_contains', ['value' => $value]);
                    
                    }
                
                	if ($modificator == "not-contains") {
                    
                    	$query = $query->where('hostname', 'NOT LIKE', '%'. $value. '%');
                    	
                    	$description[] = __('all.dashboard.filter_hostname_not_contains', ['value' => $value]);
                    
                    }
                
                	if ($modificator == "exactly") {
                    
                    	$query = $query->where('hostname', '=', $value);
                    	
                    	$description[] = __('all.dashboard.filter_hostname_exactly', ['value' => $value]);
                    
                    }
                
                
                }
            
            	if (isset($parameters->workgroup_value)) {
                
                	$value = $parameters->workgroup_value;
                	$modificator = $parameters->workgroup_modificator;
                	
                	if ($modificator == "contains") {
                    
                    	$query = $query->where('workgroup', 'LIKE', "%" . $value. "%");
                    	
                    	$description[] = __('all.dashboard.filter_workgroup_contains', ['value' => $value]);
                    
                    }
                
                	if ($modificator == "not-contains") {
                    
                    	$query = $query->where('workgroup', 'NOT LIKE', '%'. $value. '%');
                    	
                    	$description[] = __('all.dashboard.filter_workgroup_not_contains', ['value' => $value]);
                    
                    }
                
                	if ($modificator == "exactly") {
                    
                    	$query = $query->where('workgroup', '=', $value);
                    	
                    	$description[] = __('all.dashboard.filter_workgroup_exactly', ['value' => $value]);
                    
                    }
                
                
                }
            
            	if (isset($parameters->osname_value)) {
                
                	$value = $parameters->osname_value;
                	$modificator = "contains";
                	
                	if ($modificator == "contains") {
                    
                    	$query = $query->where('os', 'LIKE', "%{$value}%");
                 
                    	$description[] = __('all.dashboard.filter_osname_contains', ['value' => $value]);
                    
                    }
                
                }
            
            	if (isset($parameters->type)) {
                	
                	$types = implode(", ", $parameters->type);
                
                	$query = $query->whereIn("type", $parameters->type);
                
                	$description[] = __('all.dashboard.filter_hw_types', ['types' => $types]);
                    
                
                }
            
            	if (isset($parameters->disk)) {
                	
                	$types = implode(", ", $parameters->disk);
                
                	$query = $query->select("workstations.*", "ws_harddrives.mediatype")->rightJoin("ws_harddrives", "ws_harddrives.wsid", "=", "workstations.id")->whereIn("ws_harddrives.mediatype", $parameters->disk)->groupBy("workstations.id");
                	
                	$description[] = __('all.dashboard.filter_disk_types', ['types' => $types]);
                
                }
            
            	if (isset($parameters->serial)) {
                	
                	$query = $query->whereNull("serial");
                
                	$description[] = __('all.dashboard.filter_serial_missing');
                
                }
            
            	if (isset($parameters->inventory)) {
                	
                	$query = $query->whereNull("inventory_id");
                
                	$description[] = __('all.dashboard.filter_inventory_missing');
                
                }
            
            	if (isset($parameters->offline_value)) {
                
                	$value = $parameters->offline_value;
                
                	$date = Carbon::now()->subDays($value)->format("Y-m-d H:i:s");
                
                	$query = $query->where("heartbeat", "<", $date)->where("service_version", "!=", "0");
                
                	$description[] = __('all.dashboard.filter_offline_more', ['days' => $value]);
                
                }
            
            	if (isset($parameters->osupdate_value)) {
                
                	$value = $parameters->osupdate_value;
                
                	$date = Carbon::now()->subMonths($value)->format("Y-m-d H:i:s");
                
                	$query = $query->where("wu_installed", "<", $date)->where("service_version", "!=", "0");
                
                	$description[] = __('all.dashboard.filter_osupdate_outdated', ['months' => $value]);
                
                }
            
            	
            	if (isset($parameters->admin_account)) {
                
                	$adminUserNames = GlobalSettings::where("name", "exclude-admin-username-list")->first()->value;
    				$adminUserNames = str_replace(' ', '', $adminUserNames);
    				$excludedUserNames = explode(",", $adminUserNames);
    
    				$query = $query->rightJoin("ws_user_accounts", "ws_user_accounts.wsid", "=", "workstations.id")->where("ws_user_accounts.is_admin", 1)->whereNotIn('ws_user_accounts.username', $excludedUserNames)->groupBy("workstations.id");
                
                	$description[] = __('all.dashboard.filter_admin_rights');
                
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
                        
                        	$description[] = __('all.dashboard.filter_has_any_labels', ['labels' =>  implode(", ", $values)]);
                        
                        } else {
                        	
                        
                        	$having = array();
                        
                        	foreach($values as $value) {
                            
                            		$having[] = "label_list LIKE '%{$value}%'";
                            
                            }
                        
                        	$query = $query->havingRaw(implode(" AND ", $having));
                        
                        	$description[] = __('all.dashboard.filter_has_all_labels', ['labels' =>  implode(", ", $values)]);
                        }
                    
                    	
                    } else {
                    
                    	if ($connection == "or") {
                        
                        	$having = array();
                        
                        	foreach($values as $value) {
                            
                            		$having[] = "label_list NOT LIKE '%{$value}%'";
                            
                            }
                        
                        	$query = $query->havingRaw(implode(" OR ", $having));
                        
                        	$description[] = __('all.dashboard.filter_not_has_any_labels', ['labels' =>  implode(", ", $values)]);
                         	                        
                        } else {
                        
                        	$having = array();
                        
                        	foreach($values as $value) {
                            
                            		$having[] = "label_list NOT LIKE '%{$value}%'";
                            
                            }
                        
                        	$query = $query->havingRaw(implode(" AND ", $having));
                        	$description[] = __('all.dashboard.filter_not_has_all_labels', ['labels' =>  implode(", ", $values)]);
                        	
                        	
                        }
                    }
                
                }
            
            	
            	if (isset($parameters->ipv6)) {
                
                	$query = $query->select("ws_ips.ip", "workstations.*")->join("ws_ips", "ws_ips.wsid", "=", "workstations.id")->whereRaw("INET6_ATON(ws_ips.ip) IS NULL")->groupBy("workstations.id");
                	
                	$description[] = __('all.dashboard.filter_has_ipv6');
                
                }
            
            	if (isset($parameters->support)) {
                
                	$date = Carbon::now()->format("Y-m-d");
                
                	$query = $query->select("operating_systems.last_support_date", "workstations.*")->join("operating_systems", "operating_systems.name", "=", "workstations.os")->where("operating_systems.last_support_date", "<", $date);
                	
                	$description[] = __('all.dashboard.filter_ossupport_ended');
                
                }
            	
            	$count = count($query->get());
            
            	$interventions[] = array(
                	"name" => $name,
                	"shortDescription" => $shortDescription,
                	"description" => $description,
                	"count" => $count,
                	"hash" => $hash
                );
            
            }
        
        }
    
    	return view("dashboard", ["interventions" => $interventions, "printerSupplyURL" => $supply_link, "blocks" => $blocks]);
    
    }

}
