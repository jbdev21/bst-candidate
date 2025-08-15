<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::insert([
            ['sku' => 'GOLD1OZ', 'name' => '1oz Gold Eagle', 'metal' => 'GOLD', 'weight_oz' => 1.000, 'premium_cents' => 8999, 'active' => true],
            ['sku' => 'SILV1OZ', 'name' => '1oz Silver Eagle', 'metal' => 'SILVER', 'weight_oz' => 1.000, 'premium_cents' => 399, 'active' => true],
            ['sku' => 'SILV10OZ', 'name' => '10oz Silver Bar', 'metal' => 'SILVER', 'weight_oz' => 10.000, 'premium_cents' => 2999, 'active' => true],
        ]);
    }
}
