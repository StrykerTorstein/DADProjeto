<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'wallet_id',
        'type',
        'transfer',
        'transfer_wallet_id',
        'transfer_movement_id',
        'type_payment',
        'category_id',
        'iban',
        'mb_entity_code',
        'mb_payment_reference',
        'description',
        'source_description',
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
