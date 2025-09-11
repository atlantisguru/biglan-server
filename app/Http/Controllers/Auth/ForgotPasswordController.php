<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\PasswordResetTokens;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    //use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

	//use AuthenticatesUsers;

    public function __construct()
    {
        //$this->middleware('guest');
    }

	public function send(Request $request)
    {
    
    	$data = request()->except('_token');
    
    	$rules = [
        	'email' => [
        		'required',
        		'email',
        		function ($attribute, $value, $fail) {
                	$user = Users::where('email', $value)->first();
            		if (!isset($user) || $user->confirmed == 0) {
                    	$fail(__('all.validation.email_not_found'));
            		}
        		},
            	function ($attribute, $value, $fail) {
                	$token_exists = PasswordResetTokens::where('email', $value)->count();
            		if ($token_exists) {
                    	$fail(__('all.validation.email_token_found'));
            		}
        		},
            ],
        ];
    
    	$messages = [
    			'email.required' => __('all.validation.required'),
    			'email.email' => __('all.validation.email'),
    	];
    	
    	$validator = Validator::make($data, $rules, $messages);
    
    	if ($validator->fails()) {
        	return redirect()->back()->withErrors($validator)->withInput();
    	}
    	
    	$user = Users::where('email', $request["email"])->first();
    
    	$token = new PasswordResetTokens();
	    $token->user_id = $user->id;
	    $token->email = $user->email;
	    $token->token = $this->getToken();
	    $saved = $token->save();
	    if ($saved) {
	    	$data['token'] = $token->token;
	    	$data['username'] = $user->username;
	    			
		    try {
               	Mail::send('emails.' .$user->language . '.reset', $data, function($message) use ($user) {
		    		$message->to($user->email, $user->username)->subject(__('all.users.set_new_password_subject'));
		    	});
		    } catch(Exception $e) {
            	\Log::info($e);
		    	return $e->getMessage();
		    }
        }
    
	    return back()->withInput()->with('success', '<i class="fa fa-check"></i> ' . __('all.users.forgot_password_email_sent_out'));
    
    }
	
	protected function getToken() {
    	return hash_hmac('sha256', Str::random(40), config('app.key'));
    }
    
    protected function tokenExists($email) {
    	$count = PasswordResetTokens::where('email', $email)->count();
    	return $count > 0;
    }

	public function viewLostPassword() {
    
    	return view("users.lostpassword");
    
    }

}
