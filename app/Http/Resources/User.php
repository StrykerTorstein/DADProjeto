<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class User extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'age' => 'TODO',
            'test_var1' => 'Hello',
            'test_var2' => '123',
            'test_var3' => 'World!',
            'variavel_comum' => 9999
        ];
    }
}
