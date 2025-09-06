<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdempotencyKey extends Model
{
    public $timestamps = false;

    protected $fillable = ['key', 'purpose', 'order_id', 'created_at'];
}
