<?php

namespace App\Models;

//use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;
use App\Models\UserPermissions;

class Users extends Authenticatable
{
    //use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

	protected $table = "users";

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

	public function hasPermission($permission) {
    
    	if (is_array($permission)) {
        	$result = UserPermissions::where('user_id', auth()->id())->whereIn('permission', $permission)->count();
        	if ($result == count($permission)) {
            	return true;
            }
        } else {
			$result = UserPermissions::where('user_id', auth()->id())->where('permission', $permission)->count();        
        	if ($result == 1) {
            	return true;
            }
        }
    
    	return false;
    }

	public function permissions() {
    
    	return $this->hasMany('App\Models\UserPermissions', 'user_id', 'id')->pluck("permission")->toArray();
    
    }


}
