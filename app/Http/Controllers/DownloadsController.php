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
use App\Models\Downloads;

class DownloadsController extends Controller
{

	/*
	 * Processing payload based on "action"
	 */
	public function payload(Request $request) {
    
    	$action = $request['action'];
    
    	switch($action) {
            case "uploadFile":
            	return $this->uploadFile($request);
            	break;
        	case "publishFile":
            	return $this->publishFile($request);
            	break;
        	case "deleteFile":
            	return $this->deleteFile($request);
            	break;
        	default:
        		break;
        }
    
    }

	/*
	 * List of downloadable files based on authentication status (Downloads)
	 */
	public function viewDownloads() {
    
    	if (Auth::check()) {
        
        	if(!auth()->user()->hasPermission('read-downloads')) { return redirect('dashboard'); }
    
        	$downloads = Downloads::get();
        	
        	return view("downloads.downloads", compact("downloads"));
        
        } else {
        
    		$downloads = Downloads::where("published", 1)->orderBy("counter", "DESC")->get();
        
        	return view("downloads.public", compact("downloads"));
        }
    
    }

	/*
	 * Upload file to downloads (Downloads/Upload)
	 */
	public function uploadFile($request) {
    
    	if(!auth()->user()->hasPermission('upload-download')) { return redirect('dashboard'); }	
    
    	$download = new Downloads();
    	$download->alias = $request["alias"];
    	$published = filter_var($request["published"], FILTER_VALIDATE_BOOLEAN);
    	if($published == true) {
        	$download->published = 1;
        } else {
        	$download->published = 0;
        }
    	
    	$filename = explode(".", $request["filename"])[0]."-".Carbon::now()->format("YmdHis").".".last(explode(".", $request["filename"]));
    	$filename = str_replace(" ", "-", $filename);
    	$data = $request["file"];
    	$file = base64_decode($data);
    	file_put_contents(storage_path("downloads/".$filename), $file);
    	$download->filename = $filename;
    	$download->size = filesize(storage_path("downloads/".$filename))/1024;
    	$download->save();
    	return $request;
    }

	/*
	 * Sets the status of a downloadable file to public/private (Downloads)
	 */
	public function publishFile($request) {
    
    	if(!auth()->user()->hasPermission('write-downloads')) { return redirect('dashboard'); }	
    
    	\Log::info($request);
    
    	$download = Downloads::where("id", $request["id"])->first();
    
    	if (!isset($download)) {
        	return "ERROR";
        }
    
    	$published = filter_var($request["published"], FILTER_VALIDATE_BOOLEAN);
    
    	if($published === true) {
        	$download->published = 1;
        } else {
        	$download->published = 0;
        }
    
    	$saved = $download->save();
    	
    	if ($saved) {
        	return "OK";
        }
    
    	return "ERROR";
    }

	/*
	 * Removes a file from downloads (Downloads)
	 */
	public function deleteFile($request) {
    
    	if(!auth()->user()->hasPermission('write-downloads')) { return redirect('dashboard'); }	
    
    	$download = Downloads::where("id", $request["id"])->first();
    
    	if (!isset($download)) {
        	return "ERROR";
        }
    
    	$filePath = storage_path("downloads/".$download->filename);

    	if (file_exists($filePath)) {
        	unlink($filePath);
        }
    
    	$deleted = $download->delete();
    	if ($deleted) {
        	return "OK";
        }
    
    	return "ERROR";
    }

	/*
	 * Gives back a file from storage path
	 */
	public function fileDownload(Request $request) {
    
    	$filename = request()->filename;
    	
    	$file = Downloads::where("filename", $filename)->first();
    	
    	if(!isset($file)) {
        	return $this->viewDownloads();
        }
    
    	if(!$file->published && !auth()->check()) {
        	return $this->viewDownloads();
        }
    
    
    	$file->counter = $file->counter+1;
    	$file->save();
    
    	$path = storage_path('downloads/'.$file->filename);
    
    	if(!file_exists($path)) {
        	return $this->viewDownloads();
        }
    	
    	return response()->download($path, $file->filename);
    	
    }

}