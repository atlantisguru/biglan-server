<?php

namespace App\Http\Controllers\Auth;

use App\Models\Users;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Random;
use App\Models\GlobalSettings;
use App\Models\UserPermissions;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    //use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest');
    }

   public function store(Request $request) {
    	
   		$data = request()->except('_token');
   
   		$rules = [
        
        	'email' => ['required', 'email', 'unique:users'],
        	'username' => ['required', 'min:8', 'unique:users'],
        	'password' => [
        		'required',
            	'string',
        		'min:8',
            	'confirmed'
            ],
        ];
   
   		$messages = [
    			'required' => __('all.validation.required'),
        		'email.unique' => __('all.validation.email_unique'),
        		'username.unique' => __('all.validation.username_unique'),
        		'email.email' => __('all.validation.email'),
        		'min' => __('all.validation.min_8'),
        		'string' => __('all.validation.string'),
        		'password.confirmed' => __('all.validation.password_confirm'),
    	];
   
   		$validator = Validator::make($data, $rules, $messages);
    
    	if ($validator->fails()) {
        	return redirect()->back()->withErrors($validator)->withInput();
    	}
   		
   		$firstUser = false;
   		$users = Users::count();
   
   		if($users === 0) {
        	$firstUser = true;
        }
   
   		$token = Str::random(32);
   		while (Users::where('token', $token)->exists()) {
    		$token = Str::random(32);
		}
   
    	$user = new Users();
    
    	$user->username = $request['username'];
    	$user->password = bcrypt($request['password']);
   		$user->token = $token;
   
    	$user->email = $request['email'];
    
   		if($firstUser === true) {
   			$user->confirmed = 1;     	
        }
   
    	$user->save();
   		
   		if($firstUser === true) {
   			$userId = $user->id;
        	$this->createFirstUserPermissions($userId);
        }
   
  	 	return redirect()->to('/login')->with('success', __('all.login.register_success'));
    	
    }

	public function createFirstUserPermissions($userId) {
    
    	$permissions = [
    		['user_id' => $userId, 'permission' => 'read-blocks'],
    		['user_id' => $userId, 'permission' => 'write-blocks'],
    		['user_id' => $userId, 'permission' => 'read-eventstream'],
    		['user_id' => $userId, 'permission' => 'read-interventionstream'],
    		['user_id' => $userId, 'permission' => 'write-intervention'],
    		['user_id' => $userId, 'permission' => 'read-intervention-suggestions'],
    		['user_id' => $userId, 'permission' => 'read-workstations'],
    		['user_id' => $userId, 'permission' => 'read-workstation'],
    		['user_id' => $userId, 'permission' => 'write-workstation'],
    		['user_id' => $userId, 'permission' => 'write-workstation-command'],
    		['user_id' => $userId, 'permission' => 'delete-workstation'],
    		['user_id' => $userId, 'permission' => 'read-subnetworks'],
    		['user_id' => $userId, 'permission' => 'write-subnetwork'],
    		['user_id' => $userId, 'permission' => 'write-ips'],
    		['user_id' => $userId, 'permission' => 'read-notifications'],
    		['user_id' => $userId, 'permission' => 'write-notification'],
    		['user_id' => $userId, 'permission' => 'delete-notification'],
    		['user_id' => $userId, 'permission' => 'read-notifications-eventlog'],
    		['user_id' => $userId, 'permission' => 'read-network-printers'],
    		['user_id' => $userId, 'permission' => 'write-network-printer'],
    		['user_id' => $userId, 'permission' => 'delete-network-printer'],
    		['user_id' => $userId, 'permission' => 'read-network-devices'],
    		['user_id' => $userId, 'permission' => 'write-network-device'],
    		['user_id' => $userId, 'permission' => 'delete-network-device'],
    		['user_id' => $userId, 'permission' => 'read-topology'],
    		['user_id' => $userId, 'permission' => 'write-topology'],
    		['user_id' => $userId, 'permission' => 'read-batch-command'],
    		['user_id' => $userId, 'permission' => 'write-batch-command'],
    		['user_id' => $userId, 'permission' => 'read-script'],
    		['user_id' => $userId, 'permission' => 'delete-script'],
    		['user_id' => $userId, 'permission' => 'read-articles'],
    		['user_id' => $userId, 'permission' => 'read-post'],
    		['user_id' => $userId, 'permission' => 'write-post'],
    		['user_id' => $userId, 'permission' => 'read-comment'],
    		['user_id' => $userId, 'permission' => 'write-comment'],
    		['user_id' => $userId, 'permission' => 'read-documents'],
    		['user_id' => $userId, 'permission' => 'write-document'],
    		['user_id' => $userId, 'permission' => 'delete-document'],
    		['user_id' => $userId, 'permission' => 'read-operating-systems'],
    		['user_id' => $userId, 'permission' => 'read-printers'],
    		['user_id' => $userId, 'permission' => 'read-monitors'],
    		['user_id' => $userId, 'permission' => 'read-global-settings'],
    		['user_id' => $userId, 'permission' => 'write-global-settings'],
    		['user_id' => $userId, 'permission' => 'read-global-settings-eventlog'],
    		['user_id' => $userId, 'permission' => 'read-downloads'],
    		['user_id' => $userId, 'permission' => 'write-downloads'],
    		['user_id' => $userId, 'permission' => 'upload-download'],
    		['user_id' => $userId, 'permission' => 'delete-download'],
    		['user_id' => $userId, 'permission' => 'read-updates'],
    		['user_id' => $userId, 'permission' => 'upload-update'],
    		['user_id' => $userId, 'permission' => 'edit-update'],
    		['user_id' => $userId, 'permission' => 'read-users'],
    		['user_id' => $userId, 'permission' => 'read-user-activities'],
    		['user_id' => $userId, 'permission' => 'read-user-permissions'],
    		['user_id' => $userId, 'permission' => 'write-user-permissions'],
    		['user_id' => $userId, 'permission' => 'write-user-status'],
    		['user_id' => $userId, 'permission' => 'read-api-tokens'],
    		['user_id' => $userId, 'permission' => 'write-api-tokens'],
    		['user_id' => $userId, 'permission' => 'revoke-api-tokens'],
    	];

		UserPermissions::insert($permissions);
    }

	public function viewRegister() {
    
    	$enabled = GlobalSettings::where('name', 'enable-registration')->first();
    
    	if(isset($enabled)) {
        	if(!$enabled->value) {
        		return view("users.login");    	
            }
        }
    
    	return view("users.register");
    
    }

}
