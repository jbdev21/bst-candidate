<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['sku', 'name', 'metal', 'weight_oz', 'premium_cents', 'active'];
}
