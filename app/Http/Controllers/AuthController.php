<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users;
use Auth;
use Session;
use Carbon\Carbon;
use App\Models\UserActivities;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    //use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
	/*
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
*/
	protected $redirectAfterLogout = '/login';

	/*
     * Logout the User (User/Logout) 
     */
	public function logout(Request $request) {
    	
		$this->saveUserActivity(Auth::user()->id, "logout", null, $request->getClientIp(), $request->userAgent());
    
    	Auth::logout();
    	
    	$rememberMeCookie = Auth::getRecallerName();
    	$cookie = \Cookie::forget($rememberMeCookie);
    	
    	Session::flush();
    	
    	return redirect('/login')->withCookie($cookie);
    }

	/*
     * Authenticate the User (Login) 
     */
	public function authenticate(Request $request)
    {
    	
    	$email = $request['email'];
    	$password = $request['password'];
    	
    	$users = Users::get();
    	$user = $users->where('email', '=', $email)->first();
    	
    if (Auth::attempt(['email' => $email, 'password' => $password], request()->has('remember_me'))) {
    	if (Auth::user()->confirmed):
    			
    			$user->last_login = Carbon::now();
    			$user->save();
    			$this->saveUserActivity(Auth::user()->id, "login success", null, $request->getClientIp(), $request->userAgent());
    		
    		return redirect('/dashboard');
    	
    	else:
    
    		
    		$this->logout();
    
    	endif;
    }
    
    	$this->saveUserActivity(0, "login failed", $email, $request->getClientIp(), $request->userAgent());
    
    	return redirect()->back()
    		->withInput();
    	
    }

	/*
     * Login form (Login) 
     */
	public function viewLogin() {
    
    	return view("users.login");
    
    }

	/*
     * Saves the User's activity (Users/Activities) 
     */
	public function saveUserActivity($userId, $event, $description = null, $ip = null, $browser = null) {
    
    	$activity = new UserActivities();
    	$activity->user_id = $userId;
    	$activity->activity = $event;
    	$activity->description = $description;
    	$activity->ip = $ip;
    	$activity->browser = $browser;
    	$activity->save();
    
    }

}
