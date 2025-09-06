<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpotPrice extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['metal', 'price_per_oz_cents', 'as_of'];
}
