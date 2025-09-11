<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\ApiTokens;
use Illuminate\Support\Str;

class ApiTokensController extends Controller
{

	/* 
	 * Process incoming payloads based on action
	 */
	public function payload(Request $request)
	{
      	switch($request["action"]){
			default:
				return null;
				break;
		}
        
	}

	public function listTokens() {
    
    	if(!auth()->user()->hasPermission('read-api-tokens')) { return redirect('dashboard'); }	
    
    	$tokens = ApiTokens::orderBy("is_active", "DESC")->get();
    
    	$masterKey = 'base64:' . base64_encode(env("MASTER_KEY"));
        $cipher = 'AES-256-CBC';
        $serialize = true;

        $encryptedColumnName = 'token';

        foreach ($tokens as $token) {
            $encryptedTokenValue = $token->{$encryptedColumnName};

            $decryptedValue = 'Dekódolási hiba';

            if (!empty($encryptedTokenValue)) {
                try {
                    $decryptedValue = Crypt::decrypt($encryptedTokenValue, $serialize, $masterKey, $cipher);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    //Log::error("Failed to decrypt token ID {$token->id ?? 'N/A'}: " . $e->getMessage());
                } catch (\Exception $e) {
                     //Log::error("Unexpected error decrypting token ID {$token->id ?? 'N/A'}: " . $e->getMessage());
                 }
            } else {
                 $decryptedValue = null;
            }

            $token->decrypted_token = $decryptedValue;
        }
    
    	return view('apitokens.list',  [ "tokens" => $tokens ]);
    
    }

	public function getToken(Request $request) {
    
    	$plainToken = $this->generateToken();
    	$wsid = "";
    	if (isset($request->wsid)) {
    		$wsid = $request->wsid;
        }
    
    	$request->token = $plainToken;
    	$request->name = "Token #" . $wsid;
    
    	$masterKey = 'base64:'.base64_encode(env("MASTER_KEY"));
        $cipher = 'AES-256-CBC';
        $serialize = true;
    	$token = new ApiTokens();
    	$token->name = request()->name;
    	$token->token = Crypt::encrypt($plainToken, $serialize, $masterKey, $cipher);
    	$token->token_hash = hash('sha256', $plainToken);
    	$token->save();
    
    	return $plainToken;
    
    }

	public function newToken() {
    
    	if(!auth()->user()->hasPermission('write-api-tokens')) { return redirect('dashboard'); }	
    
    	$token = $this->generateToken();
    	
    	return view('apitokens.new', ['token' => $token]);
    
    }

	public function revokeToken(Request $request) {
    
    	if(!auth()->user()->hasPermission('write-api-tokens')) { return redirect('dashboard'); }	
    
    	$token = $token = ApiTokens::find(request()->id);
    	
    	$token->is_active = 0;
    	$token->save();
    	
    	return redirect('apitokens');
    
    }


	public function saveToken(Request $request) {
    
    	if(!auth()->user()->hasPermission('write-api-tokens')) { return redirect('dashboard'); }	
    
    	$data = request()->except('_token');
    
    	$tokensTable = 'api_tokens';
        $tokenColumn = 'token';

        $masterKey = 'base64:'.base64_encode(env("MASTER_KEY"));
        $cipher = 'AES-256-CBC';
        $serialize = true;

        $rules = [
            'name' => ['required', 'string', 'min:5', 'max:255'],
            'token' => [
                'required',
                'string',
                'max:32',
            	'min:32',
                'alpha_num',
                function ($attribute, $value, $fail) use ($tokensTable, $tokenColumn, $masterKey, $cipher, $serialize) {
                    $existingEncryptedTokens = DB::table($tokensTable)->pluck($tokenColumn);

                    foreach ($existingEncryptedTokens as $encryptedToken) {
                        try {
                            $decryptedToken = Crypt::decrypt($encryptedToken, $serialize, $masterKey, $cipher);

                            if ($decryptedToken === $value) {
                                $fail(":attribute is already in use.");
                                return;
                            }
                        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                            \Log::warning("Could not decrypt existing token '{$encryptedToken}' during validation check: " . $e->getMessage());
                        }
                    }
                },
            ],
            'max_uses' => ['nullable', 'integer', 'gt:0'],
            'expires' => ['nullable', 'date', 'after:now'],
        ];
    
    	$messages = [
    		'required' => __('all.validation.required'),
    		'numeric' => __('all.validation.numeric'),
    		'date' => __('all.validation.date'),
    		'required_if' => __('all.validation.required'),
        	'regex' => __('all.validation.regex'),
        ];

        $validator = Validator::make($data, $rules, $messages);
    
    	if ($validator->fails()) {
        	return redirect()->back()->withErrors($validator)->withInput();
    	}
    
    	$token = new ApiTokens();
    	$token->name = request()->name;
    	$token->token = Crypt::encrypt(request()->token, $serialize, $masterKey, $cipher);
    	$token->token_hash = hash('sha256', request()->token);
    	if (isset(request()->max_uses)) {
        	$token->max_uses = request()->max_uses;
        }
    
    	if (isset(request()->expires)) {
        	$token->expires_at = request()->expires;
        }
    	
    	$token->save();
    
    	return redirect('/apitokens');
    
    
    }

	public function generateToken() {
    	$plainToken = '';
        $table = 'api_tokens';
        $column = 'token';

        $masterKey = 'base64:'.base64_encode(env("MASTER_KEY"));
        $cipher = 'AES-256-CBC';
        $serialize = true;

        do {
            $plainToken = Str::random(32);
			$existingEncryptedTokens = DB::table($table)->pluck($column);
			$isUnique = true;
            
            foreach ($existingEncryptedTokens as $encryptedToken) {
                try {
                    $decryptedToken = Crypt::decrypt($encryptedToken, $serialize, $masterKey, $cipher);

                    if ($decryptedToken === $plainToken) {
                        $isUnique = false;
                        break;
                    }
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    \Log::error("Failed to decrypt token: " . $e->getMessage());
                    continue;
                }
            }

        } while (!$isUnique);
    
        $encryptionKeyToStore = $plainToken;
        $encryptedTokenToStore = Crypt::encrypt($encryptionKeyToStore, $serialize, $masterKey, $cipher);
	
    	return $plainToken;
    
    }


}