<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Movement extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'wallet_id' => $this->wallet_id,
            'type' => $this->type,
            'email'=> ($this->transfer&&$this->wallet!=null)?$this->wallet->email:'',
            'category' => (isset($this->category))?$this->category->name:'',
            'transfer_wallet_id' => $this->transfer_wallet_id,
            'type_payment' => $this->type_payment,
            'description' => $this->description,
            'source_description' => $this->source_description,
            'iban' => $this->iban,
            'mb_entity_code' => $this->mb_entity_code,
            'mb_payment_reference' => $this->mb_payment_reference,
            'date' => $this->date,
            'start_balance' => $this->start_balance, 
            'end_balance' => $this->end_balance,
            'value' => $this->value
        ];
    }
}
