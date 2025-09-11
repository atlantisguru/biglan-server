<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifications;
use App\Models\ApiTokens;
use Carbon\Carbon;

class EnvironmentController extends Controller
{

	/*
	 * Checks tokens from Environment.
	 */
	public function __construct()
    {
       	
    	if (request()->has('token')) {
        	$plainToken = request()->token;
       		$incomingTokenHash = hash('sha256', $plainToken);
        	
        	$token = ApiTokens::where('token_hash', $incomingTokenHash)->first();
        
        	if(isset($token)) {
            	
            	if (!$token->is_active) {
                	abort(Response::HTTP_UNAUTHORIZED, 'Nincs jogosultságod ehhez a művelethez.');
                	return "ERROR";
                }
            
            	if (isset($token->expires_at)) {
                
                	if(Carbon::parse($token->expires_at) < Carbon::now()) {
                    
                    	$token->is_active = 0;
                    	$token->save();
                    	abort(Response::HTTP_UNAUTHORIZED, 'Nincs jogosultságod ehhez a művelethez.');
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
                	$token->tokenable_type = "Notifications";
                }
            
            	$token->save();
            
            } else {
            	abort(Response::HTTP_UNAUTHORIZED, 'Nincs jogosultságod ehhez a művelethez.');
            	return "ERROR";
            }
        }
    	
    }

	/*
	 * Saves the values of sensors sent by an IoT device (Notifications/Sensor type)
	 */
	public function payload(Request $request) {
    
    	$data = $request->all();

        foreach ($data as $key => $value) {
        	$notification = Notifications::where("name", $key)->first();
        	if (!empty($notification)) {
        		$notification->last_value = $value;
    			$notification->save();
    	   	}
        }
    }

}
