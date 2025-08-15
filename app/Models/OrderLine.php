<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLine extends Model
{
    protected $fillable = ['order_id', 'sku', 'qty', 'unit_price_cents', 'subtotal_cents'];
}
