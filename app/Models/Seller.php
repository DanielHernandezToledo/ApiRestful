<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seller extends User
{
    use HasFactory;

    protected $table = 'users';

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
