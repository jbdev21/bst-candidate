<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpotPriceHistory extends Model
{
    public $timestamps = false;

    protected $fillable = ['metal', 'price_per_oz_cents', 'as_of'];
}
