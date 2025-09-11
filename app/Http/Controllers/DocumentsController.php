<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\Models\Documents;
use Illuminate\Support\Str;

class DocumentsController extends Controller
{

	/*
	 * Processing payloads based on "action" 
	 */
	public function payload(Request $request) {
    
    	$action = $request['action'];
    
    	switch($action) {
            case "uploadDocument":
            	return $this->uploadDocument($request);
            	break;
            case "lockDocument":
            	return $this->lockDocument($request);
            	break;
            case "trashDocument":
            	return $this->trashDocument($request);
            	break;
            case "restoreDocument":
            	return $this->restoreDocument($request);
            	break;
        	default:
        		break;
        }
    
    }

	/*
	 * Gives back the list of stored documents (Documents) 
	 */
	public function listDocuments() {
	
    	if(!auth()->user()->hasPermission('read-documents')) { return redirect('dashboard'); }	
    
    	return view('documents.main');
    
    }

	/*
	 * Gives back the list of stored, but deleted documents (Documents/Trash) 
	 */
	public function listTrashDocuments() {
	
    	if(!auth()->user()->hasPermission('read-documents')) { return redirect('dashboard'); }	
    
    	return view('documents.trash');
    
    }

	/*
	 * Stores an uploaded document (Documents/Upload) 
	 */
	public function uploadDocument($request) {
    
    	if(!auth()->user()->hasPermission('write-document')) { return redirect('dashboard'); }	
    
    	$document = new Documents();
    	$document->title = $request["title"];
    	$document->keywords = $request["keywords"];
    	$document->signed_at = $request["date"];
    	$document->user_id = Auth::user()->id;
    	$document->source = "upload";
    	$filename = Str::slug($document->title)."-".Carbon::now()->format("Ymd-His").".".last(explode(".", $request["filename"]));
    	$data = $request["file"];
    	$file = base64_decode($data);
    	file_put_contents(storage_path("documents/".$filename), $file);
    	$document->filename = $filename;
    	$document->filesize = filesize(storage_path("documents/".$filename));
    	$document->save();
    	return $request;
    }

	/*
	 * Switches the status of a document to deleted (Documents) 
	 */
	public function trashDocument($request) {
 		
    	if(!auth()->user()->hasPermission('delete-document')) { return "ERROR"; }	
    
    	$id = $request["id"];
    	$document = Documents::where("id", $id)->first();
    	$document->deleted = 1;
    	$document->deleter_id = Auth::user()->id;
    	$document->save();
    	return "OK";
    
    }

	/*
	 * Switches the status of a document from deleted to not deleted (Documents) 
	 */
	public function restoreDocument($request) {
    
    	if(!auth()->user()->hasPermission('write-document')) { return "ERROR"; }	
    
    	$id = $request["id"];
    	$document = Documents::where("id", $id)->first();
    	$document->deleted = 0;
    	$document->deleter_id = null;
    	$document->save();
    	return "OK";
    
    }
	
	/*
	 * Gives back the physical file (Documents) 
	 */
	public function getFile($filename) {
    
    	$path = storage_path('documents/' . $filename);
    	if (!File::exists($path)) {
        	abort(404);
    	}

    	$file = File::get($path);
    	$type = File::mimeType($path);

    	$response = response($file, 200);
    	$response->header("Content-Type", $type);

    	return $response;

    }

}