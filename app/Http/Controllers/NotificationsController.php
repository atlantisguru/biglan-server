<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Notifications;
use App\Models\NotificationLogs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Documents;

class NotificationsController extends Controller
{

	/*
 	 * Processing payloads based on "action" (Notification center)
     */
	public function payload(Request $request) {
    
    $action = $request["action"];
    
    	switch($action){
			case "changeNotificationMonitoredStatus":
				return $this->changeNotificationMonitoredStatus($request);
				break;
    		case "getNotificationStatuses":
				return $this->getNotificationStatuses();
				break;
    		case "getNotificationDetails":
				return $this->getNotificationDetails($request);
				break;
    		case "saveNotification":
				return $this->saveNotification($request);
				break;
    		case "deleteNotification":
				return $this->deleteNotification($request);
				break;
    		default:
    			return null;
    	}
    
    }

	/*
 	 * Gives back the list view of Notifications (Notification center)
     */
	public function listNotifications() {
    	
    	if(!auth()->user()->hasPermission('read-notifications')) { return redirect('dashboard'); }
    
    	$notifications = Notifications::orderBy("type", "ASC")->orderBy("alias", "ASC")->get();
    
    	return view("notifications.list", compact("notifications"));
    
    }

	/*
 	 * Gives back the dashboard view of Notifications (Notification center)
     */
	public function showNotificationDashboard() {
    
    	if(!auth()->user()->hasPermission('read-notifications')) { return redirect('dashboard'); }
    
    	$notifications = Notifications::orderBy("alias", "ASC")->get();
    
    	return view("notifications.dashboard", compact("notifications"));
    
    }

	/*
 	 * Gives back the log list view of Notifications (Notification center)
     */
	public function listNotificationLogs() {
    
    	if(!auth()->user()->hasPermission('read-notifications-eventlog')) { return redirect('dashboard'); }
    
    	$notificationLogs = NotificationLogs::orderBy("created_at", "DESC")->paginate(200);
    
    	return view("notifications.logs", compact("notificationLogs"));
    
    }

	/*
 	 * Form to create new Notification (Notification Center/New)
     */
	public function newNotification() {
    
    	if(!auth()->user()->hasPermission('write-notification')) { return redirect('dashboard'); }
    
    	return view("notifications.new");
    
    }

	/*
 	 * Saves a new Notification (Notification Center/Save)
     */
	public function createNotification(Request $request) {
    
    	if(!auth()->user()->hasPermission('write-notification')) { return redirect('dashboard'); }
    
    	$data = request()->except('_token');
    	
    	if ($request->type == "http-status-code") {
        	$rules = [
        		'alias' => 'required',
        		'name' => [
            		'required',
            		'regex:/^[a-z0-9-]{2,100}$/'
           		],
    			'website' => 'required',
            	'expression' => 'required',
            ];
        }
    
    	if ($request->type == "ping") {
        	$rules = [
        		'alias' => 'required',
        		'name' => [
            		'required',
            		'regex:/^[a-z0-9-]{2,100}$/'
           		],
    			'ip' => 'required','ip',
        	];
        }
    
    	if ($request->type == "socket-polling") {
        	$rules = [
        		'alias' => 'required',
        		'name' => [
            		'required',
            		'regex:/^[a-z0-9-]{2,100}$/'
           		],
    			'ip' => 'required','ip',
            	'port' => 'required','numeric',
            ];
        }
    
    	if ($request->type == "snmp") {
        	$rules = [
        		'alias' => 'required',
        		'name' => [
            		'required',
            		'regex:/^[a-z0-9-]{2,100}$/'
           		],
    			'ip' => 'required','ip',
            	'oid' => 'required',
            	'expression' => 'required',	
            ];
        }
    
    	if ($request->type == "sensor-value") {
        	$rules = [
        		'alias' => 'required',
        		'name' => [
            		'required',
            		'regex:/^[a-z0-9-]{2,100}$/'
           		],
    			'expression' => 'required',	
            ];
        }
    
    	if ($request->type == "biglan-command") {
        	$rules = [
        		'alias' => 'required',
        		'name' => [
            		'required',
            		'regex:/^[a-z0-9-]{2,100}$/'
           		],
    			'ip' => 'required','ip',
            	'wsid' => 'required', 'numeric',
            	'expression' => 'required',
            	'command' => 'required',
            ];
        }
    	
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
        	\Log::info($request->type);
        	return redirect()->back()->withErrors($validator)->withInput();
    	}
    
    	$notification = new Notifications();
    	$notification->alias = $request["alias"];
    	$notification->name = $request["name"];
    	$notification->description = $request["description"];
    	$notification->type = $request["type"];
    	
    	if ($notification->type === "http-status-code") {
        	$notification->target = json_encode(array(
            	"website" => $request["website"],
            	"expression" => $request["expression"]
            ));
        }
    
    
    	if ($notification->type === "ping") {
        	$notification->target = $request["ip"];
        }
    
    	if ($notification->type === "socket-polling") {
        	$notification->target = $request["ip"] . ":" . $request["port"];
        }
    	
    	if ($notification->type === "sensor-value") {
        	$notification->target = $request["expression"];
        	$notification->unit = $request["unit"];
        }
    
    	if ($notification->type === "mass-heartbeat-loss") {
        	$notification->target = $request["expression"];
        }
    
    	if ($notification->type === "biglan-command") {
        	$notification->target = json_encode(array(
            	"wsid" => $request["wsid"],
            	"command" => $request["command"],
            	"expression" => $request["expression"]
            ));
        }
    	
    	if ($notification->type === "snmp") {
        	$notification->target = json_encode(array(
            	"ip" => $request["ip"],
            	"oid" => $request["oid"],
            	"expression" => $request["expression"]
            ));
        }
    	
    	$saved = $notification->save();
    
    	if ($saved) {
        	return redirect("notifications");
        }
    
    }

	/*
 	 * Changes the status of a Notification (Notification Center)
     */
	public function changeNotificationMonitoredStatus($request) {
    	
    	if(!auth()->user()->hasPermission('write-notification')) { return redirect('dashboard'); }
    
    	$locale = config('locale');
		App::setLocale($locale);
    	
    	$notificationId = $request["nid"];
    	$monitored = $request["monitored"];
    
    	$notification = Notifications::where("id", $notificationId)->first();
    
    	$notification->monitored = $monitored;
        $saved = $notification->save();
    
    	if($saved) {
        	$notificationLog = new NotificationLogs();
        	$notificationLog->notification_id = $notification->id;
        	$notificationLog->event = "monitoring changed";
        	$notificationLog->description = __('all.notification_center.notification_status_changed') . ": " . (($notification->monitored == 0)?mb_strtoupper(__('all.notification_center.disabled'), 'UTF-8'):mb_strtoupper(__('all.notification_center.activated'), 'UTF-8')). " (".auth()->user()->username.")";
            $notificationLog->save();
            
        	return $notification->monitored;
        } else {
        	return "";
        }
        
    }

	/*
 	 * Gives back the status of a Notification (Notification center)
     */
	public function getNotificationStatuses() {
    
   		if(!auth()->user()->hasPermission('read-notifications')) { return redirect('dashboard'); }
    
    	$lastTime = Carbon::now()->subSeconds(16)->format("Y-m-d H:i:s");
    
    	$notifications = Notifications::select("id", "triggered", "monitored", "last_value", "unit")->where("updated_at", ">=", $lastTime)->get();
    
    	return $notifications;
    
    }

	/*
 	 * Gives back the details of a Notification (Notification center)
     */
	public function getNotificationDetails($request) {
    
    	if(!auth()->user()->hasPermission('read-notifications')) { return "ERROR"; }
    
    	$id = $request["id"];	
    
    	$notification = Notifications::where("id", $id)->first();
    	
    	$target = $notification->target;
    
    	if ($notification->type === "socket-polling") {
        	$target = array(
            	"ip" => explode(":", $notification->target)[0],
            	"port" => explode(":", $notification->target)[1]
            );
        
        }
        
    	if ($notification->type === "http-status-code") {
        	$target = array(
            	"website" => json_decode($notification->target, true)["website"],
            	"expression" => json_decode($notification->target, true)["expression"]
            );
        }
    
    
    	if ($notification->type === "biglan-command") {
        	$target = array(
            	"wsid" => json_decode($notification->target, true)["wsid"],
            	"command" => json_decode($notification->target, true)["command"],
            	"expression" => json_decode($notification->target, true)["expression"]
            );
        }
    
    	if ($notification->type === "snmp") {
        	$target = array(
            	"ip" => json_decode($notification->target, true)["ip"],
            	"oid" => json_decode($notification->target, true)["oid"],
            	"expression" => json_decode($notification->target, true)["expression"]
            );
        }
    
    	if (!isset($notification->description)) {
        	$notification->description = "[Nincs leírás]";
        }
    
    	if (isset($notification)) {
    		$result = array(
            	"id" => $notification->id,
        		"alias" => $notification->alias,
            	"name" => $notification->name,
            	"type" => $notification->type,
        		"description" => $notification->description,
        		"target" => $target,
            	"unit" => $notification->unit
        	);
    
    		return $result;
        } else {
        	return null;
        }
    }

	/*
 	 * Saves the changes of a Notification (Notification center)
     */
	public function saveNotification($request) {
    
    	if(!auth()->user()->hasPermission('write-notification')) { return "ERROR"; }
    
    	$id = $request["id"];	
    
    	$notification = Notifications::where("id", $id)->first();
    
    	if($request["alias"] === "") {
        	return "Error";
        }
    
    	$notification->alias = $request["alias"];
    	$notification->name = $request["name"];
    	if ($request["description"] === "" || $request["description"] === "[Nincs leírás]") {
    		$notification->description = null;
        } else {
        	$notification->description = $request["description"];
        }
    
    	if ($notification->type === "socket-polling") {
        	$notification->target = $request["ip"] . ":" . $request["port"];	
        }
        	
    	if ($notification->type === "biglan-command") {
        	$notification->target = json_encode(array(
            	"wsid" => $request["wsid"],
            	"command" => $request["command"],
            	"expression" => $request["expression"]
            ));
        }
    
    	if ($notification->type === "http-status-code") {
        	$notification->target = json_encode(array(
            	"website" => $request["website"],
            	"expression" => $request["expression"]
            ));
        }
    
    	if ($notification->type === "snmp") {
        	$notification->target = json_encode(array(
            	"ip" => $request["ip"],
            	"oid" => $request["oid"],
            	"expression" => $request["expression"]
            ));
        }
    
    	if ($notification->type === "sensor-value" || $notification->type === "mass-heartbeat-loss") {
        	$notification->target = $request["expression"];
        	if ($request["unit"] != "null" && $request["unit"] != "") {
            	$notification->unit = $request["unit"];
            }
        } 
    
    	if ($notification->type === "ping") {
        	$notification->target = $request["ip"];	
        }
    
    	$saved = $notification->save();
    
    	if($saved) {
        	return "OK";
        } else {
        	return "Error";
        }
    
    }

	/*
 	 * Removes a Notification and all of it's related data (Notification Center)
     */
	public function deleteNotification($request) {
    
    	if(!auth()->user()->hasPermission('delete-notification')) { return "ERROR"; }
    
    	$id = $request["id"];	
    
    	$notification = Notifications::where("id", $id)->first();
    	if (!isset($notification)) {
        	return "Error";
        }
		$events = NotificationLogs::where("notification_id", $id)->get();
    
    	$content = view('notifications.archive', compact('notification', 'events'))->render();
    	$filename = "arhivalt-felugyelet-".$notification->alias."-".$notification->id.".html";
    	//archív html létrehozása
    	file_put_contents(storage_path("documents/".$filename), $content);
    
    	//archív html fájl rögzítése dokumentumtárban
    	$doc = new Documents();
    	$doc->title = "Archivált felügyelet - " . $notification->name . " - " . $notification->id;
    	$doc->keywords = "archív,felügyelet,".$notification->alias.",".$notification->name.",".$notification->type;
    	$doc->source = "generated";
    	$doc->filename = $filename;
    	$doc->filesize = filesize(storage_path("documents/".$filename));
    	$doc->signed_at = Carbon::now()->format("Y-m-d");
    	$doc->user_id = Auth::user()->id;
    	$doc->save();
    	
    	NotificationLogs::where("notification_id", $id)->delete();
    	Notifications::where("id", $id)->delete();

    	return "OK";
    
    }

}