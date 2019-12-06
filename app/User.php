<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'id',
        'name',
        'email',
        'type',
        'active',
        'photo',
        'nif'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function userTypeToStr($type)
    {
        switch ($type) {
            case 'u':
                return 'User';
            case 'o':
                return 'Operator';
            case 'a':
                return 'Administrator';
        }
        return 'Unknown';
    }

    public function isUser(){
        return $this->type === "u";
    }
    
    public function isOperator(){
        return $this->type === "o";
    }

    public function isAdministrator(){
        return $this->type === "a";
    }

    public function isActive(){
        return $this->active === 1;
    }

    public function wallet()
    {
        return $this->hasOne('App\Wallet', 'email', 'email');
    }

    public function movements(){
        return $this->hasMany('App\Movement', 'wallet_id');
    }
}


