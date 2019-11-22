<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'email',
        'balance',
    ];

    public function owner()
    {
        return $this->hasOne('App\User', 'email', 'email')->withTrashed();
    }
}
