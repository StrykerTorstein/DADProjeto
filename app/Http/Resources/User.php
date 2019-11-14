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
            'teste2joao' => 101,
            'teste1joao' => 100,
            'age' => 99,
            'test_var3' => 'World!',
            'test_var2' => '123',
            'test_var1' => 'Hello',
        ];
    }
}
