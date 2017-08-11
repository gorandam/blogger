<?php

namespace App;

use App\Notifications\CustomPasswordReset;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    //Here we define one to many relationship method
    public function posts()
    {
      return $this->hasMany('App\Post');
    }
    //Here we overwrite this canRessetPassword method with our notification class
    public function sendPasswordResetNotification($token)
    {
       $this->notify(new CustomPasswordReset($token));
    }
}
