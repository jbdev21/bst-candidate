<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceQuote extends Model
{
    protected $fillable = [
        'user_id', 'sku', 'unit_price_cents', 'qty', 'quote_expires_at', 'basis_spot_cents', 'basis_version', 'tolerance_bps',
    ];

    protected $casts = ['quote_expires_at' => 'datetime'];
}
