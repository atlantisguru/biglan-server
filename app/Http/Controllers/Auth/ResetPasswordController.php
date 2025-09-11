<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\PasswordResetTokens;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    //use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
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

	public function check(Request $request) {
    	$token = $request["token"];
    	$tokenEntry = PasswordResetTokens::where('token', $token)->first();
    	if(isset($tokenEntry)) {
    		return view("users.passwordreset", ["resetToken" => $token]);
        } else {
        	return redirect()->to('/login');
        }
    }

	public function reset(Request $request) {
    	
    	$token = $request["resetToken"];
    	$tokenEntry = PasswordResetTokens::where('token', $token)->first();
    
    	if(!isset($tokenEntry)) {
        	return redirect()->to('/login');
        }
    
    	$data = request()->except('_token');
    
    	$rules = [
        	'password' => [
        		'required',
            	'string',
        		'min:8',
            	'confirmed'
            ],
        ];
    
    	$messages = [
    			'required' => __('all.validation.required'),
    			'min' => __('all.validation.min_8'),
        		'string' => __('all.validation.string'),
        		'password.confirmed' => __('all.validation.password_confirm'),
        ];
    	
    	$validator = Validator::make($data, $rules, $messages);
    
    	if ($validator->fails()) {
        	return redirect()->back()->withErrors($validator)->withInput();
    	}
    
    	$user = Users::where('id', $tokenEntry->user_id)->first();
        $user->password = bcrypt($request['password']);
        $saved = $user->save();
        
        if($saved) {
        	$tokenEntry->delete();
           	return redirect()->to('/login')->with('success', __('all.lost_password.reset_success'));
        }
            
                
    }

}
