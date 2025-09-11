<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users;
use App\Models\GlobalSettings;
use App\Models\GlobalSettingsChanges;
use Carbon\Carbon;

class GlobalSettingsController extends Controller
{

	/*
	 *	Lists all the Global Settings (Global Settings) 
	 */
	public function listGlobalSettings() {
    
    	if(!auth()->user()->hasPermission('read-global-settings')) { return redirect('dashboard'); }	
 
    	$settings = GlobalSettings::orderBy("name", "ASC")->get();
    
    	return view("globalsettings.list", compact("settings"));
    
    }

	/*
	 *	List of the changes of the Global Settings (Global Settings/Logs) 
	 */
	public function listGlobalSettingsLogs() {
    
    	if(!auth()->user()->hasPermission('read-global-settings-eventlog')) { return redirect('dashboard'); }	
 
    	$settingsLogs = GlobalSettingsChanges::orderBy("created_at", "DESC")->take(50)->get();
    
    	return view("globalsettings.logs", compact("settingsLogs"));
    
    }

	/*
	 *	Saves the value of a Global Setting (Global Settings) 
	 */
	public function saveGlobalSettings(Request $request) {
    
    	if(!auth()->user()->hasPermission('write-global-settings')) { return redirect('dashboard'); }	
    
    	$settings = GlobalSettings::get();
    
    	foreach($settings as $setting) {
        
    		if (isset($request[$setting->name])) {
            
            	if ($request[$setting->name] != $setting->value) {
               
               	 	$log = new GlobalSettingsChanges();
                	$log->gsid = $setting->id;
                	$log->event = "<i>" . Auth::user()->username . "</i> megváltoztatta <i>" . $setting->name . "</i> beállítás értékét: " . $setting->value . " => <b>" . $request[$setting->name] . "</b>";
                	$log->save();
               
                	$setting->value = $request[$setting->name];
                	$setting->save();
                
                }
            
            }
            
        }
    
    	return redirect("globalsettings");
    
    }

}