<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Models\Workstations;
use App\Models\WsEvents;
use App\Models\WsIps;
use App\Models\WsUserAccounts;
use App\Models\WsHarddrives;
use App\Models\WsPrinters;
use App\Models\WsKeys;
use App\Models\ServiceUpdates;
use Carbon\Carbon;
use App\Models\NetworkDevices;
use App\Models\NetworkEdges;
use App\Models\WsDns;
use App\Models\WsMonitors;
use App\Models\WsMemories;
use App\Models\WsPrintStats;
use App\Models\SubnetIps;
use App\Models\SubnetIpChanges;
use App\Models\Documents;
use App\Models\CommandWorkstations;
use App\Models\GlobalSettings;
use App\Models\ApiTokens;


class ApiController extends Controller
{

	/*
	 * Checks tokens from Workstations.
	 */
	public function __construct()
    {
       
    	if (request()->has('token')) {
        	$plainToken = request()->token;
       		$incomingTokenHash = hash('sha256', $plainToken);
        	
        	$token = ApiTokens::where('token_hash', $incomingTokenHash)->first();
        
        	if(isset($token)) {
            	
            	if (!$token->is_active) {
                	return "ERROR";
                }
            
            	if (isset($token->expires_at)) {
                
                	if(Carbon::parse($token->expires_at) < Carbon::now()) {
                    
                    	$token->is_active = 0;
                    	$token->save();
                    	return "ERROR";
                    	
                    }
                
                }
            
            	if (isset($token->max_uses)) {
                	$token->uses_count = $token->uses_count + 1;
                
                	if ($token->max_uses == $token->uses_count) {
                    	$token->is_active = 0;
                    }
                
                }
            
            	$token->last_used_at = Carbon::now();
            
            	if (!isset($token->tokenable_type) || !isset($token->tokenable_id)) {
                	$token->tokenable_type = "Workstations";
                	if (request()->has("wsid")) {
                		if (request()->wsid != "ID") {
                			$token->tokenable_id = request()->wsid;
                    	}
                    }
                	
                }
            
            	$token->save();
            
            } else {
            	return "ERROR";
            }
        } else {
        	return "ERROR";
        }
    	
    }

	/* 
	 * Process incoming service payloads based on if it is an "action" or "event"
	 */
	public function payload(Request $request)
	{
    	if (isset($request["action"])) {
			
			$action = $request["action"];
			
			switch($action){
				
				case "update":
					return $this->update($request);
					break;
            	case "getWaitingCommandsCounter":
            		return $this->getWaitingCommandsCounter($request);
            		break;
            	case "getCommand":
            		return $this->getCommand($request);
            		break;
            	case "saveCommandResult":
            		return $this->saveCommandResult($request);
            		break;
            	default:
					return null;
					break;
			}
		}
    
    	if (isset($request["event"])) {
			$event = $request["event"];
			$wsid = $request["wsid"];	
        
        	$except = array("identification", "basic", "refresh", "heartbeat", "external", "print", "register", "key exchange");
			if ( !in_array($event, $except) )
			{
				$this->storeEvent($request);
			}
        
        	switch($event){
				
            	case "identification":
            		return $this->identifyWorkstation($request["uuid"], $request["board"], $request["product"], $request["mac"], $request["hostname"]);
            		break;
            	case "key exchange":
            		return $this->keyExchange($request);
            		break;
            	/*
				case "register":
					return $this->register($request);
					break;
				case "refresh":
					return $this->refresh($request);
					break;
                    */
            	case "basic":
					return $this->refresh($request);
					break;
            	case "external":
            		return $this->external($request["wsid"], $request);
					break;
				case "shutdown":
					return $this->shutdown($request);
					break;
				case "service stopped":
					return $this->shutdown($request);
					break;
				case "suspend":
					return $this->shutdown($request);
					break;
				case "boot":
					return $this->boot($request);
            		break;
            	case "resume":
					return $this->boot($request);
            		break;
				case "lock":
					return $this->idle($request);
            		break;
            	case "logoff":
					return $this->idle($request);
            		break;
            	case "unlock":
					return $this->idle($request);
            		break;
            	case "logon":
					return $this->idle($request);
            		break;
            	case "heartbeat":
					return $this->heartbeat($request);
					break;
				case "anydesk connected":
					return $this->anydesk($request);
					break;
				case "anydesk disconnected":
					return $this->anydesk($request);
					break;
				case "teamviewer connected":
					return $this->teamviewer($request);
					break;
            	case "teamviewer disconnected":
					return $this->teamviewer($request);
					break;
            	case "vnc connected":
					return $this->vnc($request);
					break;
				case "vnc disconnected":
					return $this->vnc($request);
					break;
				case "usb connected":
					return $this->usb($request);
					break;
            	case "usb removed":
					return $this->usb($request);
					break;
            	case "print":
            		return $this->printing($request);
					break;
            	
            	default:
					return null;
					break;
			}
        
		}
		
		return null;
	}

	/*
	 * Generates a 32 character (256bit) token
	 */
	public function generateToken($length = 32) {
    
    	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    	$charactersLength = strlen($characters);
    	$randomString = '';
    	
    	for ($i = 0; $i < $length; $i++) {
       		$randomString .= $characters[random_int(0, $charactersLength - 1)];
    	}
    	
    	return $randomString;
	}

	/*
	 * Checks if there is a new version of the service running on the workstations
	 */
	protected function update($request) {
		    
		$currentRelease = ServiceUpdates::where("version","LIKE", $request["version"])->where("channel", "LIKE", $request["channel"])->first();
    		
    	if (!isset($currentRelease)) {
        
        	$currentRelease = ServiceUpdates::where("channel", $request["channel"])->where("active", 1)->orderBy("created_at", "DESC")->first();
        
        }
    
    	$newRelease = ServiceUpdates::where("channel", $request["channel"])->where("created_at", ">", $currentRelease->created_at)->where("active", 1)->orderBy("created_at", "DESC")->first();
		
    	if (!isset($newRelease)) {
        
        	$newRelease = ServiceUpdates::where("channel", $request["channel"])->where("active", 1)->orderBy("created_at", "DESC")->first();
        
        }
    
    	if ($currentRelease->version != $newRelease->version) {
         	
        	if (version_compare($request["version"], "2.0.0.4") > 0) {
            
            	return url("/updates/" . $newRelease->filename);
            
            } else {
        	
            	return $newRelease->version;
            
            }
        
        } else {
           	
        	return null;
        
        }
        
       	return null;
    
   }

	/*
	 * Checks how many commands are waiting to be executed on the server (CommandCenter)
	 */
	public function getWaitingCommandsCounter($request) {
    	
    	$counter = DB::table("command_workstations")
        			->leftJoin('commands', 'command_workstations.command_id', '=', 'commands.id')
        			->whereNull("result")
        			->where("command_workstations.wsid", "=", $request["wsid"])
        			->where("commands.blocked","0")
       				->where("commands.run_after_at", "<=", Carbon::now()->format("Y-m-d H:i:s"))
        			->get();
    	return $counter->count();
    }

	/*
	 * Service downloads the commands (CommandCenter)
     */
	//TODO: JSON response
	public function getCommand($request) {
    
    	$command = DB::table("command_workstations")
        			->leftJoin('commands', 'command_workstations.command_id', '=', 'commands.id')
        			->selectRaw("command_workstations.id AS command_workstations_id, commands.command AS command")
        			->whereNull("result")
        			->where("command_workstations.wsid", "=", $request["wsid"])
        			->where("commands.blocked","0")
       				->where("commands.run_after_at", "<=", Carbon::now()->format("Y-m-d H:i:s"))
        			->orderBy("commands.run_after_at", "ASC")
        			->first();
    
    	if ($command != null) {
        	$command_workstation = CommandWorkstations::where("id", $command->command_workstations_id)->first();
    		$command_workstation->result = "[Lekérdezve]";
    		$command_workstation->save();
    		return $command->command_workstations_id."@@@".str_replace("\\","\\\\",$command->command);
        } else {
        	return "null";
        }
    
    }

	/*
	 * Saving the result of the command coming from the service (CommandCenter)
     */
	public function saveCommandResult($request) {
    	
    	if (isset($request["command_workstations_id"])) {
        	$command = CommandWorkstations::where("id", $request["command_workstations_id"])->first();
        	if ($command != null) {
            	if ($request["result"] == null) {
                	$request["result"] = "[Üres eredmény]";
                }
            	$command->result = $request["result"];
            	$command->save();
            }
        
        }
    
    }

	

	/*
 	* Stores the USB/RDP related events
    */
	protected function storeEvent($payload)
	{
		
    	if (empty($payload['wsid']) || $payload['wsid'] == 0) {
       		return null;
    	}

    	$workstation = Workstations::find($payload['wsid']);
    	if (is_null($workstation)) {
        	return null;
    	}
    	
		$event = new WsEvents();
		$event->event = $payload["event"];
        $event->wsid = $payload["wsid"];
    
		if ($event->event == "usb connected" && strpos($payload["description"], "Remote Display Adapter") !== false ) {
        	$event->event = "rdp connected";
        	$workstation->rdp = 1;
        }
    
    	if ($event->event == "usb removed" && strpos($payload["description"], "Remote Display Adapter") !== false ) {
        	$event->event = "rdp disconnected";
        	$workstation->rdp = 0;
        }
        
		if (!empty($payload['description'])) {
        	$event->description = utf8_encode($payload['description']);
    	}
    
    	$saved = $event->save();
    	$newToken = $this->generateToken();
    	$workstation->msg_token = $newToken;
    	$workstation->save();
    	
		return ($saved) ? "OK" : null;
        
	}

	/*
 	* Workstation identification process based on:
 	* - UUID
    * - Product serial number
    * - Motherboard serial number
    * - First found onboard mac address
    * 
    * If at least 25% unique match in the database, sends back the specific WSID for the workstation
    * Otherwise creates a new entry
    * If identification fails, registration of the workstation in the database not possible
    * (no unique identification data available)
 	*/
	public function identifyWorkstation($uuid, $board, $product, $mac, $hostname)
	{
    	try {
        	$serialPattern = "/^(?=.*\d).{4,26}$/";
       		$uuidPattern = "/^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$/";
        	$macPattern = "/^[a-fA-F0-9]{12}$/";

        	$validations = [
            	'uuid' => preg_match($uuidPattern, $uuid),
            	'product_serial' => preg_match($serialPattern, $product),
            	'mboard_serial' => preg_match($serialPattern, $board),
            	'first_mac' => preg_match($macPattern, $mac)
        	];

        	if (in_array(false, $validations, true)) {
            	return "ERROR";
        	}

        	$entities = [
            	['identifier' => 'uuid', 'value' => $uuid],
            	['identifier' => 'product_serial', 'value' => $product],
            	['identifier' => 'mboard_serial', 'value' => $board],
            	['identifier' => 'first_mac', 'value' => $mac],
        	];

        	foreach ($entities as &$entity) {
            	$matches = Workstations::where($entity['identifier'], $entity['value']);
            	$entity['count'] = $matches->count();
            	$entity['wsid'] = $entity['count'] === 1 ? $matches->first()->id : 0;
        	}

        	$count = 0;
        	$newCount = 0;
        	$wsid = null;

        	foreach ($entities as $ent) {
            	if ($ent['count'] === 1) {
                	$wsid = $wsid ?? $ent['wsid'];
                	if ($ent['wsid'] === $wsid) {
                    	$count++;
                	}
            	} elseif ($ent['count'] === 0) {
                	$newCount++;
            	}
        	}

        	if ($count > 0) {
            	$score = floor(($count / count($entities)) * 100);
            	$workstation = Workstations::find($wsid);
            		if ($workstation) {
                	$workstation->score = $score;
                	$workstation->save();
                	return $wsid;
            	}
        	}

        	if ($newCount > 0 && $count === 0) {
            	$workstation = new Workstations();
            	$workstation->uuid = $uuid;
            	$workstation->mboard_serial = $board;
            	$workstation->product_serial = $product;
            	$workstation->first_mac = $mac;
            	$workstation->alias = $hostname;

            	return $workstation->save() ? $workstation->id : "ERROR";
        	}

        	return "ERROR";
    
    	} catch (Exception $e) {
        	\Log::error($e->getMessage());
        	return "ERROR";
    	}
    
	}
	
	/*
 	* On boot, every service generates a random 32 character encryption key
    * Server stores them encrypted with MASTER KEY to use it for communication on port 8008 (Console)
    */
	public function keyExchange($request) {
    	try {
        
        	$masterKey = 'base64:'.base64_encode(env("MASTER_KEY"));
    		$encryptionKey = $request["key"];
        	$wsid = $request["wsid"];
    		if ($encryptionKey != null) {
    	   		$key = 'base64:'.base64_encode($masterKey);
				$cipher = 'AES-256-CBC';
				
        		$encryptedKey = Crypt::encrypt($encryptionKey, $serialize = true, $masterKey, $cipher);
            }
        
        	$exists = WsKeys::where("wsid", $wsid)->first();
        
        	if ($exists != null) {
            	$key = $exists;
            } else {
            	$key = new WsKeys();
        	}
        
        	$key->wsid = $wsid;
        	$key->encryption_key = $encryptedKey;
        	
        	$saved = $key->save();
        	$workstation = Workstations::where('id', $wsid)->first();
    		$newToken = $this->generateToken();
    		$workstation->msg_token = $newToken;
    		$workstation->save();
    	
			return ($saved) ? "OK" : "ERROR";
            
        } catch(Exception $e) {
        	\Log::info($e);
        }
    }

	/*
	 * Stores basic data for a workstation 
	 */
	//TODO: Obsolete??????
	protected function register($payload)
	{
    	
    	$workstation = new Workstations();
		$workstation->alias = $payload["hostname"];
		$workstation->hostname = $payload["hostname"];
		$workstation->workgroup = $payload["workgroup"];
		$workstation->os = $payload["os"];
    	
    	if (isset($payload["uuid"])) {
    		$workstation->uuid = $payload["uuid"];
        }
    
    	if (isset($payload["firstmac"])) {
    		$workstation->first_mac = $payload["firstmac"];
        }
    
    	if (isset($payload["motherboardserial"])) {
    		$workstation->mboard_serial = $payload["motherboardserial"];
        }
    
    	if(isset($payload['serial'])) {
    		$workstation->product_serial = $payload['serial'];
        }
    
    	if (isset($payload["os_activated"])) {
    		$workstation->os_activated = $payload["os_activated"];
        }
    
    	if (isset($payload["ram"])) {
        	if (($payload["ram"] / 1024) > 1024) {
            	$payload["ram"] = $payload["ram"]/1024;
            }
    		$workstation->ram = $payload["ram"];
        } else {
        	$workstation->ram = 0;
        }
    
		$workstation->ram_slots = $payload["ram_slots"];
    	$workstation->ram_max_capacity = $payload["ram_max_capacity"];
    	$workstation->cpu = $payload["cpu"];
    
    	$cpu = Workstations::where("cpu", $payload["cpu"])->first();
    	if(isset($cpu)) {
        	$workstation->cpu_release_date = $cpu->cpu_release_date;
        }
    
		$workstation->hardware = $payload["hardware"];
		$workstation->fast_startup = $payload["fast_startup"];
		$workstation->wu_checked = $payload["wu_checked"];
		$workstation->wu_installed = $payload["wu_installed"];
		$workstation->service_version = $payload["service_version"];
		$workstation->os_drive_size = $payload["os_drive_size"];
		$workstation->os_drive_free_space = $payload["os_drive_free_space"];
		$workstation->bootup_at = $payload["bootup_at"];
    	$workstation->update_channel = $payload["channel"];
  		if(isset($payload['serial'])) {
    		$workstation->serial = $payload['serial'];
        }
    	$saved = $workstation->save();
		
		if (!$saved) {
        	
			return null;
		} else {
			return $workstation->id;			
		}
	
	}

	/*
	 * Updates basic data for a workstation 
	 */
	protected function refresh($payload)
	{
    	
    	try {
        
			$workstation = Workstations::where("id", $payload["wsid"])->first();
		
			if ($workstation == null) {return null;}
		
			$workstation->hostname = $payload["hostname"];
			$workstation->workgroup = $payload["workgroup"];
			
        	if(isset($payload["os"])) {
    			
            	if ($payload["os"] != $workstation->os) {
            		$event = new WsEvents();
					$event->wsid = $workstation->id;
					$event->event = "os changed";
					$event->description = $workstation->os . " -> " . $payload["os"];
					$event->save();
            	}
        		
            	$workstation->os = $payload["os"];
        	
            }
    
    		if (isset($payload["uuid"])) {
    			$workstation->uuid = $payload["uuid"];
        	}
    	
    		if (isset($payload["firstmac"])) {
    			$workstation->first_mac = $payload["firstmac"];
        	}
    
    		if (isset($payload["motherboardserial"])) {
    			$workstation->mboard_serial = $payload["motherboardserial"];
        	}
    
    		if(isset($payload['serial'])) {
    			$workstation->product_serial = $payload['serial'];
        		if (!isset($workstation->serial) || $workstation->serial == "" ) {
            		$workstation->serial = $payload['serial'];
            	}
        	}
        
        	if(isset($payload['activemac'])) {
        		$mac = $payload['activemac'];
        		$mac = str_replace("<br>", "", $mac);
        		$mac = str_replace(":", "-", $mac);
        		if (strpos($mac, '-') === false) {
        			$mac = preg_replace('/([a-fA-F0-9]{2})(?=[a-fA-F0-9]{2})/', '$1-', $mac);
        			$mac = rtrim($mac, '-');
            	}
    			$workstation->active_mac = $mac;
        	}
        
        	if (!isset($workstation->hardware) || $workstation->hardware == "" ) {
        		$workstation->hardware = $payload["hardware"];
    		}
        
        
        	if(isset($payload["os_activated"])) {
    			$workstation->os_activated = $payload["os_activated"];
        	}
    	
        	$ram = filter_var($payload["ram"], FILTER_SANITIZE_NUMBER_INT);
        
        	if ( ( $ram / 1024) > 1024 ) {
            	$ram = $ram/1024;
        	}
        
    		$workstation->ram = $ram;
    		$workstation->ram_slots = $payload["ram_slots"];
    		$workstation->ram_max_capacity = $payload["ram_max_capacity"];
    		$workstation->cpu = $payload["cpu"];
    		if(isset($payload['hardware']) && !isset($workstaion->hardware)) {
    			//$workstation->hardware = $payload["hardware"];
        	}
			$workstation->fast_startup = $payload["fast_startup"];
			$workstation->wu_checked = $payload["wu_checked"];
			$workstation->wu_installed = $payload["wu_installed"];
    		if ($workstation->service_version != $payload["service_version"]) {
        		$event = new WsEvents();
				$event->wsid = $workstation->id;
				$event->event = "service updated";
				$event->description = $workstation->service_version . " -> " . $payload["service_version"];
				$event->save();
        	}
			$workstation->service_version = $payload["service_version"];
			$workstation->os_drive_size = $payload["os_drive_size"];
			$workstation->os_drive_free_space = $payload["os_drive_free_space"];
			$workstation->bootup_at = $payload["bootup_at"];
    	
    		$bootTimeInSeconds = Carbon::now()->diffInSeconds(Carbon::parse($payload["bootup_at"]));
    		$lastUpdated = Carbon::now()->diffInHours(Carbon::parse($payload["wu_installed"]));
        	if ($lastUpdated > 72 && (int)$bootTimeInSeconds <= 900) {
        		if ((int)$bootTimeInSeconds <= 60) {
            		$workstation->boot_time = (int)$bootTimeInSeconds;
            	} else {
        			$workstation->boot_time = ((int)$bootTimeInSeconds-60);
            	}
        	}
        
			$workstation->update_channel = $payload["channel"];
    		if(isset($payload['serial']) && !isset($workstaion->serial)) {
    			//$workstation->serial = $payload['serial'];
        	}
    
    		$saved = $workstation->save();
    		$newToken = $this->generateToken();
    		$workstation->msg_token = $newToken;
    		$workstation->save();
    	
			return ($saved) ? "OK" : null;
        
        } catch(Exception $e) {
        	
        	\Log::info($e);
        
        }
    
	}

	/*
	 * Stores more data for a workstation 
	 */
	protected function external($wsid, $payload) {
    	
    	if (isset($payload["ips"])) {
			$this->saveIPAddresses($wsid, $payload["ips"]);
		}
		if (isset($payload["accounts"])) {
			$this->saveUserAccounts($wsid, $payload["accounts"]);
		}
		if (isset($payload["disks"])) {
			$this->saveHarddrives($wsid, $payload["disks"]);
		}
		if (isset($payload["printers"])) {
			$this->savePrinters($wsid, $payload["printers"]);
		}
    	if (isset($payload["dns"])) {
			$this->saveDnsAddresses($wsid, $payload["dns"]);
		}
    	if (isset($payload["monitors"])) {
			$this->saveMonitors($wsid, $payload["monitors"]);
		}
    	if (isset($payload["memories"])) {
			$this->saveMemories($wsid, $payload["memories"]);
		}
    	
    	$workstation = Workstations::where('id', $wsid)->first();
    	$newToken = $this->generateToken();
    	$workstation->msg_token = $newToken;
    	$workstation->save();
    	
		return "OK";
        
	}

	/*
 	 * Shutdown event of a workstation
     */
	protected function shutdown($payload)
	{
		$workstation = Workstations::where("id", $payload["wsid"])->first();
		
		if ($workstation == null) {return null;}
		
		$workstation->startup_at = null;
		$workstation->usb = 0;
		$workstation->teamviewer = 0;
    	$workstation->idle = 0;
    	$workstation->anydesk = 0;
    	$workstation->rdp = 0;
    	$workstation->vnc = 0;
    	
		$saved = $workstation->save();
    	$newToken = $this->generateToken();
    	$workstation->msg_token = $newToken;
    	$workstation->save();
    	
		return ($saved) ? "OK" : null;
        
	}

	/*
 	 * Boot event of a workstation
     */
	protected function boot($payload)
	{
    	$workstation = Workstations::where("id", $payload["wsid"])->first();
		
		if ($workstation == null) {return null;}
		
		$workstation->startup_at = Carbon::now()->format("Y-m-d H:i:s");
    	$workstation->heartbeat = Carbon::now()->format("Y-m-d H:i:s");
    	$workstation->idle = 0;
    	
    	$saved = $workstation->save();
    	$newToken = $this->generateToken();
    	$workstation->msg_token = $newToken;
    	$workstation->save();
    	
		return ($saved) ? "OK" : null;
        
	}

	/*
 	 * Updates idle status of a workstation based on lock/unlock, login/logoff events
     */
	public function idle($payload)
	{
    	$wsid = $payload['wsid'];
    	$event = $payload['event'];

    	$idle = ($event === 'logon' || $event === 'unlock') ? 0 : 1;

    	\DB::table('workstations')->where('id', $wsid)->update(['idle' => $idle]);

    	$workstation = Workstations::where('id', $wsid)->first();
    	$newToken = $this->generateToken();
    	$workstation->msg_token = $newToken;
    	$workstation->save();
    	
        return "OK";
        
	}

	/*
 	 * Every service sends a heartbeat event in every minutes
 	 * It helps identify a network related issue
     */
	protected function heartbeat($payload)
	{
    
    	$id = $payload["wsid"];
    	$date = Carbon::now()->format("Y-m-d H:i:s");
    	$workstation = Workstations::find($id);
    
    	if ($workstation == null) {return null;}
    
    	if(!isset($workstation->startup_at)) {
        
           	$workstation->startup_at = $date;
        
        	$event = new WsEvents();
        	$event->wsid = $id;
        	$event->level = 0;
        	$event->event = "boot";
        	$event->description = "only heartbeat sent";
        	$event->save();
        	
        }
    	
    	$workstation->heartbeat = $date;
    	
    	$saved = $workstation->save();
    	$newToken = $this->generateToken();
    	$workstation->msg_token = $newToken;
    	$workstation->save();
    	
		return ($saved) ? "OK" : null;
        
	}

	/*
 	 * Registers a connection to a workstation via AnyDesk
     */
	protected function anydesk($payload)
	{
		$workstation = Workstations::where("id", $payload["wsid"])->first();
		
		if ($workstation == null) {return null;}
		
		if ($payload["event"] == "anydesk connected") {
			$workstation->anydesk = 1;
		} else {
			$workstation->anydesk = 0;
		}
		
    	$saved = $workstation->save();
    	$newToken = $this->generateToken();
    	$workstation->msg_token = $newToken;
    	$workstation->save();
    	
		return ($saved) ? "OK" : null;
        
	}
	
	/*
 	 * Registers a connection to a workstation via Teamviewer
     */
	protected function teamviewer($payload) {
		
    	$workstation = Workstations::where("id", $payload["wsid"])->first();
		
		if ($workstation == null) {return null;}
		
		if ($payload["event"] == "teamviewer connected") {
			$workstation->teamviewer = 1;
		} else {
			$workstation->teamviewer = 0;
		}
		
    	$saved = $workstation->save();
    	$newToken = $this->generateToken();
    	$workstation->msg_token = $newToken;
    	$workstation->save();
    	
		return ($saved) ? "OK" : null;
        
	}

	/*
 	 * Registers a connection to a workstation via VNC
     */
	protected function vnc($payload) {
		
    	$workstation = Workstations::where("id", $payload["wsid"])->first();
		
		if ($workstation == null) {return null;}
		
		if ($payload["event"] == "vnc connected") {
			$workstation->vnc = 1;
		} else {
			$workstation->vnc = 0;
		}
		
    	$saved = $workstation->save();
    	$newToken = $this->generateToken();
    	$workstation->msg_token = $newToken;
    	$workstation->save();
    	
		return ($saved) ? "OK" : null;
        
	}

	/*
 	 * Registers if someone connected a USB device to the computer with storage capacity
     */
	protected function usb($payload) {
		$workstation = Workstations::where("id", $payload["wsid"])->first();
		
		if ($workstation == null) {return null;}
		
		if ($payload["event"] == "usb connected") {
			$workstation->usb = $workstation->usb+1;
		} else {
			if ($workstation->usb > 0) {
				$workstation->usb = $workstation->usb-1;
			}
		}
		
    	$saved = $workstation->save();
    	$newToken = $this->generateToken();
    	$workstation->msg_token = $newToken;
    	$workstation->save();
    	
		return ($saved) ? "OK" : null;
        
    }

	/*
 	 * Storing print data for statistical purposes
     */
	protected function printing($request) {

		try {
        
        	$wsid = $request["wsid"];
        	if (!isset($wsid)) {
            	return null;
            }
        
        	$workstation = Workstations::where("id", $wsid)->first();
        	
        	if ($workstation == null) {
            	return null; 
            }
        	
        	$pages = (int)$request["TotalPages"]; 
        	 
        	if ($pages < 1) {
            	return null;
            }
            
            $date = Carbon::now()->format("Y-m-d");
            $exists = WsPrintStats::where("wsid", $wsid)->whereDate('created_at', $date)->first();
        
        	if (isset($exists)) {
            	
            		$exists->counter = $exists->counter + 1;
            		$exists->pages = $exists->pages + $pages;
            		$exists->save();
                
            } else {
            	
         			$stat = new WsPrintStats();
            		$stat->wsid = $wsid;
            		$stat->counter = 1;
            		$stat->pages = $pages;
            		$stat->save();
                
            }
            
            $newToken = $this->generateToken();
    		$workstation->msg_token = $newToken;
    		$workstation->save();
    	
			return "OK";
            
        } catch (Exception $e) {}
    }

	/*
 	 * Storing IP Addresses of a workstation
     */
	protected function saveIPAddresses($wsid, $IPAddresses) {
    
    	$wsid = (int)$wsid;
    
    	if ($wsid == 0 || $wsid == null) {return null;}
    	
        $storedIpAddresses = WsIps::where('wsid', $wsid)->pluck('ip')->toArray();	
    	$arrivedIpAddresses = explode(',', $IPAddresses);		
    	$workstation = Workstations::where('id', $wsid)->first();
        
        if (!isset($storedIpAddresses)) { 
        	$storedIpAddresses = array();
        }
        
        if (!isset($arrivedIpAddresses)) { 
        	$arrivedIpAddresses = array();
        }
    	
        //új ip címek
    	$newIpAddresses = array_diff($arrivedIpAddresses, $storedIpAddresses);
    	if (count($newIpAddresses) > 0) {
            WsEvents::create([
           		'wsid' => $wsid,
        		'event' => 'new ip address',
        		'description' => implode(', ', $newIpAddresses)
        	]);
            
            foreach($newIpAddresses as $item) {
        		WsIps::create([
            		'wsid' => $wsid,
                	'ip' => $item
            	]);
         		
                $sameIpAddress = WsIps::where('wsid', '!=', $wsid)->where('ip', $item)->orderBy('created_at', 'ASC')->first();
                if( isset($sameIpAddress) ) {
                	WsEvents::create([
           				'wsid' => $wsid,
        				'event' => 'ip conflict',
        				'description' => $item . ' already registered to ' . $sameIpAddress->workstation()->alias
        			]);
                }
                    
                	$storedIpAddress = SubnetIps::where('ip', $item)->first();
            		
                    if( isset($storedIpAddress) ) {
                    	if( $storedIpAddress->alias != $workstation->alias ) {
                        	SubnetIpChanges::Create([
                            	'ip' => $item,
                                'event' => 'Alias: ' . $storedIpAddress->alias . ' => ' . $workstation->alias . ' [SYSTEM]'
                            ]);
                            
                            $storedIpAddress->alias = $workstation->alias;
                            $storedIpAddress->save();
                        }
                    } else {
                    	SubnetIps::create([
                        	'ip' => $item,
                            'alias' => $workstation->alias
                        ]);
                    }
            }
        }
        
    	//megszüntetendő ip címek
    	$removeableIpAddresses = array_diff($storedIpAddresses, $arrivedIpAddresses);
    	if (count($removeableIpAddresses) > 0) {
    		$items = WsIps::where('wsid', $wsid)->whereIn('ip', $removeableIpAddresses);
    		
            foreach($items as $item) {
            	$storedIpAddress = SubnetIps::where('ip', $item->ip)->first();
            	if (isset($storedIpAddress)) {
            		SubnetIpChanges::Create([
                    	'ip' => $item->ip,
                		'event' => $item->workstation()->alias . ' => üres [SYSTEM]'
                	]);
                	$storedIpAddress->alias = 'üres (' . $item->workstation()->alias . ')';
                	$storedIpAddress->save();
                }
            }
            
            $items->delete();
        	
            WsEvents::create([
           		'wsid' => $wsid,
        		'event' => 'ip address removed',
        		'description' => implode(', ', $removeableIpAddresses)
        	]);
        }
        
        return;
        
	}

	/*
 	 * Storing DNS Addresses of a workstation
     */
	
	protected function saveDnsAddresses($wsid, $ips) {
    	
    	if ($wsid == 0 || $wsid == null) {return null;}
    	
    	$storedDnsAddresses = WsDns::where('wsid', $wsid)->pluck('ip')->toArray();	
    	$arrivedDnsAddresses = explode(',', $ips);		
    	
        if (!isset($storedDnsAddresses)) { 
        	$storedDnsAddresses = array();
        }
        
        if (!isset($arrivedDnsAddresses)) { 
        	$arrivedDnsAddresses = array();
        }
    	
        $newDnsAddresses = array_diff($arrivedDnsAddresses, $storedDnsAddresses);
    	
        if (count($newDnsAddresses) > 0) {
        	foreach($newDnsAddresses as $item) {
        		WsDns::create([
            		'wsid' => $wsid,
                	'ip' => $item
            	]);
        	}
        
        	WsEvents::create([
           		'wsid' => $wsid,
        		'event' => 'new dns address',
        		'description' => implode(', ', $newDnsAddresses)
        	]);
        
        }
        
    	$removeableDnsAddresses = array_diff($storedDnsAddresses, $arrivedDnsAddresses);
    	
    	if (count($removeableDnsAddresses) > 0) {
    		$items = WsDns::where('wsid', $wsid)->whereIn('ip', $removeableDnsAddresses);
    		$items->delete();
        	
        	WsEvents::create([
           		'wsid' => $wsid,
        		'event' => 'dns address removed',
        		'description' => implode(', ', $removeableDnsAddresses)
        	]);
        
        }
        
        return;
    	
	}

	/*
 	 * Storing User Accounts of a workstation
 	 * (only locally registred)
     */
	protected function saveUserAccounts($wsid, $userAccounts) {
		$accounts = json_decode(utf8_encode($userAccounts));
		if(!isset($accounts) || $accounts == "") {
        	return null;
        }
		//Ha az adott felhasználói fiók még nincs regisztrálva a munkaállomáshoz, akkor letárolás
		foreach($accounts as $account) {
			$user = WsUserAccounts::where("wsid", $wsid)->where("sid", $account->sid)->first();
		
        	if (!isset($user)) {
				$user = new WsUserAccounts();
				$user->wsid = $wsid;
				$user->username = $account->username;
				$user->is_admin = $account->is_admin;
				$user->sid = $account->sid;
				$saved = $user->save();
				//új felhasználói fiók rögzítése esemény
				if ($saved) {
					$event = new WsEvents();
					$event->wsid = $wsid;
					$event->event = "new user account";
					$event->description = $account->username;
					$event->save();
				}
			} else {
				//ha megváltozott a felhasználói fiók típusa
				if ($user->is_admin != $account->is_admin) {
					$user->is_admin = $account->is_admin;
					
					$event = new WsEvents();
					$event->wsid = $wsid;
					$event->event = "user account type changed";
					
					if ($account->is_admin == 1) {
						$event->description = $account->username . " becomes Administrator";
					} else {
						$event->description = $account->username . " becomes User";
					}
					
					$event->save();
				}
				
				//ha megváltozott a felhasználói fiók neve
				if ($user->username != $account->username) {
					
					$event = new WsEvents();
					$event->wsid = $wsid;
					$event->event = "user account renamed";
					$event->description = $user->username . " renamed to " . $account->username;
					$event->save();
					
					$user->username = $account->username;
					
				}
				$user->save();
				
			}
				
			
		}
		//Ellenőrizni, hogy van-e olyan felhasználói fiók letárolva, amit már nem küld a munkaállomás
		//Jelenleg letárolt felhasználói fiókok (beleértve a már újonnan regisztráltakat is)
		$oldUserAccounts = WsUserAccounts::where("wsid", $wsid)->pluck('sid')->all();
		$newUserAccounts =	array_column($accounts, "sid");
		
		$deleteable = array_diff($oldUserAccounts, $newUserAccounts);
		
		if (count($deleteable) > 0) {
			foreach($deleteable as $deleteUserAccount) {
				$deleteUser = WsUserAccounts::where("wsid", $wsid)->where("sid", $deleteUserAccount)->first();
				$username = $deleteUser->name;
				if ($deleteUser->delete()) {
					$event = new WsEvents();
					$event->wsid = $wsid;
					$event->event = "user account removed";
					$event->description = $username;
					$event->save();
				}
			}
		}
		return;
	}
	
	/*
 	 * Storing Printers of a workstation
     */
	protected function savePrinters($wsid, $printersJson) {
		$printers = json_decode(utf8_encode($printersJson));
		if(!isset($printers) || $printers == "") {
        	return null;
        }
		//Ha az adott nyomtató még nincs regisztrálva a munkaállomáshoz, akkor letárolás
		foreach($printers as $pr) {
			$printer = WsPrinters::where("wsid", $wsid)->where("name", $pr->name)->first();
			if (!isset($printer)) {
				$printer = new WsPrinters();
				$printer->wsid = $wsid;
				$printer->name = $pr->name;
				$printer->port = $pr->port;
				$printer->default = $pr->default;
				$printer->network = $pr->network;
				$printer->shared = $pr->shared;
				$saved = $printer->save();
				//új nyomtató rögzítése esemény
				if ($saved) {
					$event = new WsEvents();
					$event->wsid = $wsid;
					$event->event = "new printer";
					$event->description = $pr->name;
					$event->save();
				}
			} else {
				$printer->wsid = $wsid;
				$printer->name = $pr->name;
				$printer->port = $pr->port;
				$printer->default = $pr->default;
				$printer->network = $pr->network;
				$printer->shared = $pr->shared;
				$saved = $printer->save();
			}			
			
		}
		
    	//Ellenőrizni, hogy van-e olyan nyomtató letárolva, amit már nem küld a munkaállomás
		//Jelenleg letárolt nyomtatók (beleértve a már újonnan regisztráltakat is)
		$oldPrinters = WsPrinters::where("wsid", $wsid)->pluck('name')->all();
		$newPrinters = array_column($printers, "name");
		
		$deleteable = array_diff($oldPrinters, $newPrinters);
		
		if (count($deleteable) > 0) {
			foreach($deleteable as $deletePrinter) {
				$deleting = WsPrinters::where("wsid", $wsid)->where("name", $deletePrinter)->first();
				$printer = $deleting->name;
				if ($deleting->delete()) {
					$event = new WsEvents();
					$event->wsid = $wsid;
					$event->event = "printer removed";
					$event->description = $printer;
					$event->save();
				}
			}
		}
		return;
	}

	/*
 	 * Storing RAM Modules of a workstation
     */
	protected function saveMemories($wsid, $memoriesJson) {
		$memories = json_decode(utf8_encode($memoriesJson));
		
		//Ha az adott RAM még nincs regisztrálva a munkaállomáshoz, akkor letárolás
		foreach($memories as $memory) {
			$ram = WsMemories::where("wsid", $wsid)->where("serial", $memory->serial)->first();
			if (!isset($ram)) {
				$ram = new WsMemories();
				$ram->wsid = $wsid;
				$ram->capacity = (int)$memory->capacity/1024/1024;
				$ram->slot = $memory->slot;
				$ram->manufacturer = $memory->manufacturer;
				$ram->serial = $memory->serial;
				$ram->speed = $memory->speed;
				$ram->type = $memory->type;
				$saved = $ram->save();
				//új RAM rögzítése esemény
				if ($saved) {
					$event = new WsEvents();
					$event->wsid = $wsid;
					$event->event = "new memory";
					$event->description = $ram->maufacturer . " " . $ram->capacity . "MB";
					$event->save();
				}
			}
		}
		//Ellenőrizni, hogy van-e olyan RAM letárolva, amit már nem küld a munkaállomás
		//Jelenleg letárolt RAM-ok (beleértve a már újonnan regisztráltakat is)
		$oldMemories = WsMemories::where("wsid", $wsid)->pluck('serial')->all();
		$newMemories = array_column($memories, "serial");
		
		$deleteable = array_diff($oldMemories, $newMemories);
		
		if (count($deleteable) > 0) {
			foreach($deleteable as $deleteMemory) {
				$deleting = WsMemories::where("wsid", $wsid)->where("serial", $deleteMemory)->first();
				$memory = $deleting->manufacturer . " " . $deleting->capacity . "MB (" . $deleting->serial . ")";
				if ($deleting->delete()) {
					$event = new WsEvents();
					$event->wsid = $wsid;
					$event->event = "memory removed";
					$event->description = $memory;
					$event->save();
				}
			}
		}
		return;
	}

	/*
 	 * Storing Displays of a workstation
     */
	protected function saveMonitors($wsid, $monitorsJson) {
		$monitors = json_decode(utf8_encode($monitorsJson));
    	
    	//Ha az adott monitor még nincs regisztrálva a munkaállomáshoz, akkor letárolás
		if(isset($monitors)) {
    		foreach($monitors as $monitor) {
				$display = WsMonitors::where("wsid", $wsid)->where("instance_name", $monitor->instance_name)->first();
				if (!isset($display)) {
					$display = new WsMonitors();
					$display->wsid = $wsid;
					$display->instance_name = $monitor->instance_name;
					$display->manufacturer = $monitor->manufacturer;
					$display->name = $monitor->name;
					$display->serial = $monitor->serial;
                	$display->year = $monitor->year;
					$saved = $display->save();
					//új monitor rögzítése esemény
					if ($saved) {
						$event = new WsEvents();
						$event->wsid = $wsid;
						$event->event = "new monitor";
						$event->description = $display->manufacturer . " " . $display->name . " (S/N:" . $display->serial . ")";
						$event->save();
					}
				}
			}
    	
			//Ellenőrizni, hogy van-e olyan monitor letárolva, amit már nem küld a munkaállomás
			//Jelenleg letárolt monitorok (beleértve a már újonnan regisztráltakat is)
			$oldMonitors = WsMonitors::where("wsid", $wsid)->pluck('instance_name')->all();
			$newMonitors = array_column($monitors, "instance_name");
			$deleteable = array_diff($oldMonitors, $newMonitors);
		
        	if (count($deleteable) > 0) {
				foreach($deleteable as $deleteMonitor) {
					$deleting = WsMonitors::where("wsid", $wsid)->where("instance_name", $deleteMonitor)->first();
					$monitor = $deleting->manufacturer . " " . $deleting->name . " (S/N:" . $deleting->serial . ", Azon:" . $deleting->inventory_id . ")";
            			if ($deleting->delete()) {
							$event = new WsEvents();
							$event->wsid = $wsid;
							$event->event = "monitor removed";
							$event->description = $monitor;
							$event->save();
						}
				}
			}
        }
		return;
	}
	
	/*
 	 * Storing Harddrives/SSDs of a workstation
     */
	protected function saveHarddrives($wsid, $disks) {
		$harddrives = json_decode($disks);
		if(!isset($harddrives) || $harddrives == "") {
        	return null;
        }
		
    	//if the given drive not registered yet, store it
		foreach($harddrives as $harddrive) {
			$drive = WsHarddrives::where("wsid", $wsid)->where("serial", $harddrive->serial)->first();
			
        	switch($harddrive->status) {
            	case "0":
            		$harddrive->status = "OK";
            		break;
            	case "1":
            		$harddrive->status = "Pred Fail";
            		break;
            	default:
            		$harddrive->status = "Fail";
            		break;
           	}
        
        	if (isset($harddrive->mediatype)) {
        		switch($harddrive->mediatype) {
               		case "3":
               			$harddrive->mediatype = "HDD";
               			break;
               		case "4":
               			$harddrive->mediatype = "SSD";
               			break;
               		case "5":
               			$harddrive->mediatype = "SCM";
               			break;
               		default:
               			$harddrive->mediatype = "Unspecified";
               			break;
            	}
            } else {
            	$harddrive->mediatype = "Unspecified";
            }
        
			if (!isset($drive)) {
				$drive = new WsHarddrives();
				$drive->wsid = $wsid;
				$drive->serial = $harddrive->serial;
				$drive->model = $harddrive->model;
				$drive->capacity = $harddrive->capacity;
            	$drive->mediatype = $harddrive->mediatype;
                $drive->status = $harddrive->status;
				$saved = $drive->save();
				//registering new drive
				if ($saved) {
					$event = new WsEvents();
					$event->wsid = $wsid;
					$event->event = "new harddrive";
					$event->description = $harddrive->model . " (" . $harddrive->serial . ") " . $harddrive->capacity . "GB";
					$event->save();
				}
			} else {
				//if the status of the drive changed
				if ($drive->status != $harddrive->status) {
					$drive->status = $harddrive->status;
                
                	$event = new WsEvents();
					$event->wsid = $wsid;
					$event->event = "harddrive status changed";
					$event->description = $harddrive->model . " status is " . $harddrive->status;
					$event->save();
				}
				
            	if ($drive->mediatype != $harddrive->mediatype) {
					$drive->mediatype = $harddrive->mediatype;
                }
            
				$drive->save();
				
			}
			
		}
	
    	$oldHarddrives = WsHarddrives::where("wsid", $wsid)->pluck('serial')->all();
		$newHarddrives = array_column($harddrives, "serial");
		
		$deleteable = array_diff($oldHarddrives, $newHarddrives);
		
		if (count($deleteable) > 0) {
			foreach($deleteable as $deleteHarddrive) {
				$deleteHarddrive = WsHarddrives::where("wsid", $wsid)->where("serial", $deleteHarddrive)->first();
				$hdd = $deleteHarddrive;
				if ($deleteHarddrive->delete()) {
					$event = new WsEvents();
					$event->wsid = $wsid;
					$event->event = "harddrive removed";
					$event->description = $hdd->model . " (" . $hdd->serial . ") " . $hdd->capacity . "GB";
					$event->save();
				}
			}
		}
		return;
	}

}
