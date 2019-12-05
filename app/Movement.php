<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $fillable = [
        'id',
        'type',
        'transfer_wallet_id',
        'type_payment',
        'date',
        'start_balance', 
        'end_balance',
        'value'
    ];


    public function wallet()
    {
        return $this->belongsTo('App\Wallet','transfer_wallet_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }
}
