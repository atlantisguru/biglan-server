<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\Users;
use Auth;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use App\Models\UserActivities;
use App\Models\GlobalSettings;

class LoginController extends Controller
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
    public function __construct()
    {
       // $this->middleware('guest')->except('logout');
    }

	protected $redirectAfterLogout = '/login';

	public function logout(Request $request) {
    	
		$this->saveUserActivity(Auth::user()->id, "logout", null, $request->getClientIp(), $request->userAgent());
    
    	Auth::logout();
    	
    	$rememberMeCookie = Auth::getRecallerName();
    	$cookie = \Cookie::forget($rememberMeCookie);
    	
    	Session::flush();
    	
    	
    	return redirect('/login')->withCookie($cookie);
    
    }

	public function authenticate(Request $request) {
    	
    	$email = $request['email'];
    	$password = $request['password'];
    	
    	$user = Users::where('email', '=', $email)->first();
    
    	
    
    	if (!isset($user)){
        	return redirect()->to('/login')->with('failed', __('all.login.login_failed') )->withInput($request->except('password'));
        }
    	
    	if (Auth::attempt(['email' => $email, 'password' => $password], $request->has('remember_me'))) {
    		
        	if (!Auth::user()->confirmed) {
            	return redirect()->to('/login')->with('failed', __('all.login.login_failed') )->withInput($request->except('password'));
            }
        
    		$user->last_login = Carbon::now();
    		$user->save();
    		$this->saveUserActivity(Auth::user()->id, "login success", null, $request->getClientIp(), $request->userAgent());
    		return redirect('/dashboard');
        
        }
        
    	$this->saveUserActivity(0, "login failed", $email, $request->getClientIp(), $request->userAgent());
    	
    	return redirect()->to('/login')->with('failed', __('all.login.login_failed') )->withInput($request->except('password'));
    	
    }

	public function viewLogin() {
    
    	$usersCounter = Users::count();
    
    	if ($usersCounter === 0) {
        	return redirect('/register');
        }
    
    	$enabled = GlobalSettings::where('name', 'enable-registration')->first();
    	$enableRegistration = $enabled->value;
    
    	return view("users.login", compact('enableRegistration'));
    
    }

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
