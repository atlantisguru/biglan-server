<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\ServiceUpdates;

class UpdatesController extends Controller
{

	/*
 	 * Processing payloads based on "action" (Service Updates)
     */
	public function payload(Request $request) {
    
    	$action = $request['action'];
    
    	switch($action) {
        	case "uploadFile":
            	return $this->uploadFile($request);
            	break;
        	case "deleteUpdate":
            	return $this->deleteUpdate($request);
            	break;
        	case "deployUpdate":
            	return $this->deployUpdate($request);
            	break;
        	case "revokeUpdate":
            	return $this->revokeUpdate($request);
            	break;
        	default:
        		break;
        }
    
    
    }

	/*
 	 * Stores a newly uploaded service update file (Service Updates)
     */
	public function uploadFile($request) {
    
    	if(!auth()->user()->hasPermission('upload-update')) { return redirect('dashboard'); }	
    
    	$update = new ServiceUpdates();
    	$update->description = $request["notes"];
    	$update->channel = $request["channel"];
    	
    	$filename = explode(".", $request["filename"])[0]."-".Carbon::now()->format("YmdHis").".".last(explode(".", $request["filename"]));
    	$data = $request["file"];
    	
    	$file = base64_decode($data);
    	$path = storage_path("updates/".$filename);
       	file_put_contents($path, $file);
    	
    	$command = "exiftool -ProductVersion -s3 " . escapeshellarg($path);
		$version = shell_exec($command);
    
    	$versionRegex = '/^\d+(\.\d+)*$/';

		if (!preg_match($versionRegex, $version)) {
    		return "ERROR";
		}
		
    	$update->version = preg_replace('/\r\n|\r|\n/', '', $version);    
    	$update->filename = $filename;
    
    	$saved = $update->save();
    	
    	if ($saved) {
    		return "OK";
        } else {
        	return "ERROR";
        }
    
    }

	/*
 	 * Gives back a file of a downloadable update (Service Updates)
     */
	public function downloadUpdate(Request $request) {
    
    	$filename = request()->filename;
    	
    	$file = ServiceUpdates::where("filename", $filename)->first();
    	
    	if(!isset($file)) {
        	return null;
        }
    
    	$file->counter = $file->counter + 1;
    	$file->save();
    
    	$path = storage_path('updates/'.$file->filename);
    
    	if(!file_exists($path)) {
        	return null;
        }
    	
    	return response()->download($path, $file->filename);
    	
    }

	/*
 	 * Removes a file (and database entry) from updates (Service Updates)
     */
	public function deleteUpdate($request) {
    
    	if(!auth()->user()->hasPermission('edit-update')) { return redirect('dashboard'); }	
    
    	$update = ServiceUpdates::where("id", $request["id"])->first();
    	
    	if(!isset($update)) {
        	return "ERROR";
        }
    
    	if($update->counter > 0 || $update->active == 1) {
        	return "ERROR";
        }
    
    	$path = storage_path("updates/".$update->filename);
    	
    	if (file_exists($path)) {
        	unlink($path);
        }   	
    
    	$deleted = $update->delete();
    	
    	if ($deleted) {
    		return "OK";
        } else {
        	return "ERROR";
        }
    
    }

	/*
 	 * Changes the deploy status of an update (Service Updates)
     */
	public function deployUpdate($request) {
    
    	if(!auth()->user()->hasPermission('edit-update')) { return redirect('dashboard'); }	
    
    	$update = ServiceUpdates::where("id", $request["id"])->first();
    	
    	if(!isset($update)) {
        	return "ERROR";
        }
    
    	if($update->active == 1) {
        	return "ERROR";
        }
    
    	
    	$update->active = 1;
    	$saved = $update->save();
    	
    	if ($saved) {
    		return "OK";
        } else {
        	return "ERROR";
        }
    
    }

	/*
 	 * Changes the deploy status of an update (Service Updates)
     */
	public function revokeUpdate($request) {
    
    	if(!auth()->user()->hasPermission('edit-update')) { return redirect('dashboard'); }	
    
    	$update = ServiceUpdates::where("id", $request["id"])->first();
    	
    	if(!isset($update)) {
        	return "ERROR";
        }
    
    	if($update->active == 0) {
        	return "ERROR";
        }
    
    	$update->active = 0;
    	$saved = $update->save();
    	
    	if ($saved) {
    		return "OK";
        } else {
        	return "ERROR";
        }
    
    }

	/*
 	 * Gives back the list view of Service Updates (Service Updates)
     */
	public function viewUpdates() {
    
    	if (Auth::check()) {
        
        	if(!auth()->user()->hasPermission('read-updates')) { return redirect('dashboard'); }
    
        	$updates = ServiceUpdates::orderBy('created_at', 'DESC')->get();
        	
        	return view("updates.updates", compact("updates"));
        
        }    
    }

}
