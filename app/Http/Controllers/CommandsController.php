<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Workstations;
use App\Models\Commands;
use App\Models\CommandWorkstations;
use App\Models\ConsoleScripts;
use Carbon\Carbon;

class CommandsController extends Controller
{

	/*
	 *  Processing payloads based on "action"
	 */
	public function payload(Request $request) {
		
    	if (isset($request["action"])) {
			
    		$action = $request["action"];
			
			switch($action){
				case "saveCommand":
            		return $this->saveCommand($request);
            		break;
    			case "deleteScript":
            		return $this->deleteScript($request);
            		break;
            	case "viewScript":
            		return $this->viewScript($request);
            		break;
            	case "emergencyStop":
            		return $this->emergencyStop($request);
            		break;
            	default:
					return null;
					break;
			}
		}	
    }

	/*
	 * Gives a list of created commands (CommandCenter)
	 */
	public function listCommands() {
    
    	if(!auth()->user()->hasPermission('read-batch-command')) { return redirect('dashboard'); }	
    
    	$commands = Commands::with('commands')->orderBy("created_at", "DESC")->paginate(10);
    	return view("commands.list", compact("commands"));
    
    }

	/*
	 * Show the details of a specific Command (CommandCenter)
	 */
	public function viewCommand($id) {
    
    	if(!auth()->user()->hasPermission('read-batch-command')) { return redirect('dashboard'); }	
    
    	$command = Commands::where("id", $id)->first();
    	
    	$results = CommandWorkstations::where("command_id", $command->id)->get();
    	return view("commands.command", compact("command", "results"));
    
    }

	/*
	 * Form to create a new command (CommandCenter/New)
	 */
	public function newCommand() {
    	
    	if(!auth()->user()->hasPermission('write-batch-command')) { return redirect('dashboard'); }	
    
    	$workstations = Workstations::where("os", "LIKE", "%Windows 11%")->orWhere("os", "LIKE", "%Windows 10%")->orWhere("os", "LIKE", "%Windows 8.1%")->orderBy("alias", "ASC")->get();
    	$scripts = ConsoleScripts::orderBy("alias", "ASC")->get();
    	return view("commands.edit", compact("workstations", "scripts"));
    }

	/*
	 * Shows the scripts previously saved in Workstation/Console (CommandCenter/Scripts)
	 */
	public function viewScripts() {
    
    	if(!auth()->user()->hasPermission('read-script')) { return redirect('dashboard'); }	
    
    	$scripts = ConsoleScripts::orderBy("alias", "ASC")->get();
    	return view("commands.scripts", compact("scripts"));
    }

	/*
	 * Workstations stop downloading and executing the Command (CommandCenter)
	 */
	public function emergencyStop($request) {
    
    	if(!auth()->user()->hasPermission('write-batch-command')) { return redirect('dashboard'); }	
    
    	if (isset($request["command_id"])) {
    		if ($request["command_id"] != "") {
         		$command = Commands::where("id", $request["command_id"])->first();
            	$command->blocked = 1;
            	$command->save();
            }
        }
    
    }
    
    /*
	 * Stores a new Command (CommandCenter)
	 */
	public function saveCommand($request) {
    	
    	if(!auth()->user()->hasPermission('write-batch-command')) { return redirect('dashboard'); }	
    
    	$errors = array();
    	
    	if (isset($request["command_id"])) {
        	if ($request["command_id"] == "") {
            	$command = new Commands();
            } else {
            	$command = Commands::where("id", $request["command_id"])->first();
            	if ($command == null) {
                	$errors[] = __('all.command_center.no_command_found');
                	return response()->json(['status' => 'error', 'errors' => $errors]);
                } else {
                	$runState = CommandWorkstations::where("command_id", $command->id)->whereNotNull("result")->count();
                	if($runState != null) {
                    	$errors = __('all.command_center.edit_not_possible');
                    }
                }
            }
        }
    
    	if ($request["command"] == "") {
        	$errors[] = __('all.command_center.no_script_selected');
        }
    	
    	if(isset($request["workstations"])) {
    		$workstations = explode(",", $request["workstations"]);
    		if (count($workstations) < 1) {
        		$errors[] = __('all.command_center.no_workstation_selected');
        	}
        } else {
        		$errors[] = __('all.command_center.no_workstation_selected');
        }
    	
    	if ($request["date"] == null) {
        	$errors[] = __('all.command_center.no_date_selected');
        }
    
    	if ($request["time"] == null) {
        	$errors[] = __('all.command_center.no_time_selected');
        }
    
    	$datetime = Carbon::parse($request["date"] . " " . $request["time"]);
    	$now = Carbon::now();
    	if ($datetime <= $now) {
        	$errors[] = __('all.command_center.date_time_past');
        }
    
        if(count($errors) > 0) {
        	return response()->json(['status' => 'error', 'errors' => $errors]);
        }
        
    	$command->command = $request["command"];
       	$command->alias = $request["alias"];
       	$command->description = $request["description"];
       	$command->user_id = Auth::user()->id;
    	$command->run_after_at = $datetime->format("Y-m-d H:i:s");
    	
    	$save = $command->save();
    
    	if (!$save) {
        	$errors[] = "Nem siker���lt menteni a parancsot.";
        	return response()->json(['status' => 'error', 'errors' => $errors]);
        }
    
    	$oldWorkstations = CommandWorkstations::where("command_id", $command->id)->pluck("wsid")->toArray();
        
    	foreach($workstations as $wsid) {
        	if (!in_array($wsid, $oldWorkstations)) {
            	$newWorkstation = new CommandWorkstations();
            	$newWorkstation->command_id = $command->id;
            	$newWorkstation->wsid = $wsid;
            	$newWorkstation->save();
            } else {
            	if (($key = array_search($wsid, $oldWorkstations)) !== false) {
 				   unset($oldWorkstations[$key]);
				}
            }
        }
    	
    	$leftOverWorkstations = CommandWorkstations::where('command_id', $command->id)->whereIn('wsid', $oldWorkstations);
    	$leftOverWorkstations->delete();
    
    	return response()->json(['status' => 'ok', 'command_id' => $command->id]);
    
    }

	/*
	 * Removes a previously saved script (CommandCenter/Scripts/Delete)
	 */
	public function deleteScript($request) {
    
    	if(!auth()->user()->hasPermission('delete-script')) { return "ERROR"; }	
    
    	$id = $request["id"];
    
    	$exists = ConsoleScripts::where("id", $id)->first();
    
    	if(isset($exists)) {
        	$deleted = $exists->delete();
        
        	if($deleted) {
            	return "OK";
            }
        
        }
    
    	return "ERROR";
    
    }
	
	/*
	 * Gives back a previously saved script in Workstation/Console (CommandCenter)
	 */
	public function viewScript($request) {

    	if(!auth()->user()->hasPermission('read-script')) { return "ERROR"; }	
    
    
    	$id = $request["id"];
    
    	$exists = ConsoleScripts::where("id", $id)->first();
    
    	if(isset($exists)) {
        
        	return $exists->code;
        
        }
    
    	return "ERROR";
    
    }

}