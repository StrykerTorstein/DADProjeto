<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'id',
        'email',
        'balance',
    ];

    public function user()
    {
        return $this->hasOne('App\User', 'email', 'email')->withTrashed();
    }

    public function movements(){
        return $this->hasMany('App\Movement')->withTrashed();
    }


}
