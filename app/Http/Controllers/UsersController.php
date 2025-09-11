<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Users;
use App\Models\UserPermissions;
use App\Models\UserActivities;


class UsersController extends Controller
{
	/*
 	 * Processing payloads based on "settings" (User Settings)
     */
    public function payload(Request $request) {

		$settings = $request["settings"];
		
		switch($settings) {
        	case "switchTheme":
        		$this->switchTheme($request);
        		break;
        	case "switchLanguage":
        		$this->switchLanguage($request);
        		break;
        	default:
        		break;
        }
	
	}

	/*
 	 * Gives back the view of User Settings (User Settings)
     */
	public function loadView() {
		
    	$languages = array_diff(scandir(resource_path('lang')), ['..', '.']);
    
    	return view("users.settings", [ "languages" => $languages ]);
    
    }

	/*
 	 * Gives back a list view of Users (Users)
     */
	public function listUsers() {
    	
    	if(!auth()->user()->hasPermission('read-users')) { return redirect('dashboard');}
    
    	$users = Users::orderBy("username", "ASC")->paginate(20);
    
    	return view("users.users", [ "users" => $users ] );
    
    }

	/*
 	 * Saves the permissions of a user (Users)
     */
	public function savePermissions(Request $request) {
    
    	if(!auth()->user()->hasPermission('write-user-permissions')) { return redirect('dashboard');}
    
    	$token = $request['token'];
    	$user = Users::where('token', $token)->first();
    
    	$permissions = $request["permissions"];
    	$userPermissions = UserPermissions::where('user_id', $user->id)->pluck("permission")->toArray();
    
    	if (!isset($permissions)) {
        	$permissions = array();
        }
    
    	if (!isset($userPermissions)) {
        	$userPermissions = array();
        }
    
        //új jogosultságok
    	$newPermissions = array_diff($permissions, $userPermissions);
    	foreach($newPermissions as $item) {
        	$permission = new UserPermissions();
            $permission->user_id = $user->id;
        	$permission->permission = $item;
        	$permission->save();
        }
    	
    	//megszüntetendő jogosultságok
    	$removeablePermissions = array_diff($userPermissions, $permissions);
    	if (count($removeablePermissions) > 0) {
    		$items = UserPermissions::where('user_id', $user->id)->whereIn('permission', $removeablePermissions);
    		$items->delete();
        }
        
    	$activity = new UserActivities();
    	$activity->user_id = auth()->user()->id;
    	$activity->activity = "changed user permissions";
    	$activity->description = $user->username . " | removed: " . implode(", ", $removeablePermissions) . " | " . "added: " . implode(", ", $newPermissions);
    	$activity->ip = $request->getClientIp();
    	$activity->browser = $request->userAgent();
    	$activity->save();
    		
    
    	return redirect()->back()->with('success', __('all.users.user_permission_save_success'));
    
    }

	/*
 	 * Gives back a list of User Permissions (Users)
     */
	public function userPermissions(Request $request) {
    	
    	if(!auth()->user()->hasPermission('read-user-permissions')) { return redirect('dashboard');}
    
    	$token = $request['token'];
    	$user = Users::where('token', $token)->first();
    
    	$userPermissions = UserPermissions::where('user_id', $user->id)->pluck("permission")->toArray();
    
    	$permissions = [
        	[
            	"group-name" => __('all.nav.dashboard'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_blocks'),
                		"name" => "read-blocks"
                	],
                	[
                		"alias" => __('all.users.user_permission_write_blocks'),
                		"name" => "write-blocks"
                	],
                	[
                		"alias" => __('all.users.user_permission_read_eventstream'),
                		"name" => "read-eventstream"
                	],
              		[
                		"alias" => __('all.users.user_permission_read_interventionstream'),
                		"name" => "read-interventionstream"
                	],
              		[
                		"alias" => __('all.users.user_permission_write_intervention'),
                		"name" => "write-intervention"
                	],
                	[
                		"alias" => __('all.users.user_permission_read_intervention_suggestions'),
                		"name" => "read-intervention-suggestions"
                	],
              	
              	],	
            ],
    		[
            	"group-name" => __('all.nav.workstations'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_workstations'),
                		"name" => "read-workstations"
                	],
                	[
                		"alias" => __('all.users.user_permission_read_workstation'),
                		"name" => "read-workstation"
                	],
                	[
                		"alias" => __('all.users.user_permission_write_workstation'),
                		"name" => "write-workstation"
                	],
              		[
                		"alias" => __('all.users.user_permission_write_workstation_command'),
                		"name" => "write-workstation-command"
                	],
              		[
                		"alias" => __('all.users.user_permission_delete_workstation'),
                		"name" => "delete-workstation"
                	],
               	],	
            ],
        	[
            	"group-name" => __('all.nav.ip_table'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_subnetworks'),
                		"name" => "read-subnetworks"
                	],
                	[
                		"alias" => __('all.users.user_permission_write_subnetwork'),
                		"name" => "write-subnetwork"
                	],
                	[
                		"alias" => __('all.users.user_permission_write_ips'),
                		"name" => "write-ips"
                	],
              	],	
            ],
        	[
            	"group-name" => __('all.nav.notification_center'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_notifications'),
                		"name" => "read-notifications"
                	],
                	[
                		"alias" => __('all.users.user_permission_write_notification'),
                		"name" => "write-notification"
                	],
                	[
                		"alias" => __('all.users.user_permission_delete_notification'),
                		"name" => "delete-notification"
                	],
               		[
                		"alias" => __('all.users.user_permission_read_notifications_eventlog'),
                		"name" => "read-notifications-eventlog"
                	],
              	],	
            ],
        	[
            	"group-name" => __('all.nav.network_printers'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_network_printers'),
                		"name" => "read-network-printers"
                	],
                	[
                		"alias" => __('all.users.user_permission_write_network_printer'),
                		"name" => "write-network-printer"
                	],
                	[
                		"alias" => __('all.users.user_permission_delete_network_printer'),
                		"name" => "delete-network-printer"
                	],
              	],	
            ],
        	[
            	"group-name" => __('all.nav.network_devices'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_network_devices'),
                		"name" => "read-network-devices"
                	],
                	[
                		"alias" => __('all.users.user_permission_write_network_device'),
                		"name" => "write-network-device"
                	],
                	[
                		"alias" => __('all.users.user_permission_delete_network_device'),
                		"name" => "delete-network-device"
                	],
              	],	
            ],
        	[
            	"group-name" => __('all.nav.topology'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_topology'),
                		"name" => "read-topology"
                	],
                	[
                		"alias" => __('all.users.user_permission_write_topology'),
                		"name" => "write-topology"
                	],
              	],	
            ],
        	[
            	"group-name" => __('all.nav.command_center'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_batch_command'),
                		"name" => "read-batch-command"
                	],
                	[
                		"alias" => __('all.users.user_permission_write_batch_command'),
                		"name" => "write-batch-command"
                	],
                	[
                		"alias" => __('all.users.user_permission_read_script'),
                		"name" => "read-script"
                	],
                	[
                		"alias" => __('all.users.user_permission_delete_script'),
                		"name" => "delete-script"
                	],
              	],	
            ],
        	[
            	"group-name" => __('all.nav.articles'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_articles'),
                		"name" => "read-articles"
                	],
                	[
                		"alias" => __('all.users.user_permission_read_post'),
                		"name" => "read-post"
                	],
                	[
                		"alias" => __('all.users.user_permission_write_post'),
                		"name" => "write-post"
                	],
                	[
                		"alias" => __('all.users.user_permission_read_comment'),
                		"name" => "read-comment"
                	],
              		[
                		"alias" => __('all.users.user_permission_write_commment'),
                		"name" => "write-comment"
                	],
              	],	
            ],
        	[
            	"group-name" => __('all.nav.documents'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_documents'),
                		"name" => "read-documents"
                	],
                	[
                		"alias" => __('all.users.user_permission_write_document'),
                		"name" => "write-document"
                	],
                	[
                		"alias" => __('all.users.user_permission_delete_document'),
                		"name" => "delete-document"
                	],
                ],	
            ],
        	[
            	"group-name" => __('all.nav.operating_systems'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_operating_systems'),
                		"name" => "read-operating-systems"
                	],
                ],	
            ],
        	[
            	"group-name" => __('all.nav.local_printers'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_printers'),
                		"name" => "read-printers"
                	],
                ],	
            ],
        	[
            	"group-name" => __('all.nav.monitors'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_monitors'),
                		"name" => "read-monitors"
                	],
                ],	
            ],
        	[
            	"group-name" => __('all.nav.global_settings'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_global_settings'),
                		"name" => "read-global-settings"
                	],
                	[
                		"alias" => __('all.users.user_permission_write_global_settings'),
                		"name" => "write-global-settings"
                	],
                	[
                		"alias" => __('all.users.user_permission_read_global_settings_eventlog'),
                		"name" => "read-global-settings-eventlog"
                	],
                
                ],	
            ],
        	[
            	"group-name" => __('all.nav.downloads'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_downloads'),
                		"name" => "read-downloads"
                	],
                	[
                		"alias" => __('all.users.user_permission_write_downloads'),
                		"name" => "write-downloads"
                	],
                	[
                		"alias" => __('all.users.user_permission_upload_download'),
                		"name" => "upload-download"
                	],
                	[
                		"alias" => __('all.users.user_permission_delete_download'),
                		"name" => "delete-download"
                	],
                
                ],	
            ],
        	[
            	"group-name" => __('all.nav.updates'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_updates'),
                		"name" => "read-updates"
                	],
                	[
                		"alias" => __('all.users.user_permission_upload_update'),
                		"name" => "upload-update"
                	],
                	[
                		"alias" => __('all.users.user_permission_edit_updates'),
                		"name" => "edit-update"
                	],
                ],	
            ],
        	[
            	"group-name" => __('all.nav.users'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_users'),
                		"name" => "read-users"
                	],
                	[
                		"alias" => __('all.users.user_permission_read_user_activities'),
                		"name" => "read-user-activities"
                	],
                	[
                		"alias" => __('all.users.user_permission_read_user_permissions'),
                		"name" => "read-user-permissions"
                	],
                	[
                		"alias" => __('all.users.user_permission_write_user_permissions'),
                		"name" => "write-user-permissions"
                	],
                	[
                		"alias" => __('all.users.user_permission_write_user_status'),
                		"name" => "write-user-status"
                	],
                ],	
            ],
        	[
            	"group-name" => __('all.nav.api_tokens'),
            	"rights" => [
                	[
                		"alias" => __('all.users.user_permission_read_api_tokens'),
                		"name" => "read-api-tokens"
                	],
                	[
                		"alias" => __('all.users.user_permission_write_api_tokens'),
                		"name" => "write-api-tokens"
                	],
                	[
                		"alias" => __('all.users.user_permission_revoke_api_tokens'),
                		"name" => "revoke-api-tokens"
                	],
                ],	
            ],
        ];
    
    	if(isset($user)) {
        	return view('users.permissions', ['user' => $user, "permissions" => $permissions, "userPermissions" => $userPermissions, "token" => $token]);
        }
    
    }

	/*
 	 * Gives back a list view of User's Activities (Users)
     */
	public function userActivities(Request $request) {
    	
    	if(!auth()->user()->hasPermission('read-user-activities')) { return redirect('dashboard');}
    
    	$token = $request['token'];
    	$user = Users::where('token', $token)->first();
    
    	$userActivities = UserActivities::where('user_id', $user->id)->orderBy('created_at', 'DESC')->paginate(50);
    
    	if(isset($user)) {
        	return view('users.activities', ['user' => $user, "userActivities" => $userActivities]);
        }
    
    }

	/*
 	 * Changes the status of a User (Users)
     */
	public function userStatus(Request $request) {
    	
    	if(!auth()->user()->hasPermission('write-user-status')) { return redirect('dashboard');}
    
    	$token = $request['token'];
    	$user = Users::where('token', $token)->first();
    
    	if(!isset($user)) {
        	return redirect("users");
        }
    
    	if($user->confirmed == 1) {
        	$user->confirmed = 0;
        } else {
        	$user->confirmed = 1;
        }
    	
    	$user->save();
    
    	return redirect("users");
    
    }

	/*
 	 * Changes the theme for a user (User Settings)
     */
	public function switchTheme($request) {
    	
    	$theme = $request["theme"];
    	$user = Users::where("id", \Auth::user()->id)->first();	
    	switch($theme) {
        	case "dark":
        		$user->theme = "dark";
        		break;
        	case "light":
        		$user->theme = "light";
        		break;
        	default:
        		$user->theme = null;
        		break;
        }
    	
    	$user->save();
    	return "OK";
    }

	/*
 	 * Changes the language for a user (User Settings)
     */
	public function switchLanguage($request) {
    	
    	$language = $request["language"];
    	$user = Users::where("id", \Auth::user()->id)->first();	
    	switch($language) {
        	case "hu":
        		$user->language = "hu";
        		break;
        	case "en":
        		$user->language = "en";
        		break;
        	default:
        		$user->language = null;
        		break;
        }
    	
    	$user->save();
    	return "OK";
    }

}
