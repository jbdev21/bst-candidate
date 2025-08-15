<?php

namespace Database\Seeders;

use App\Models\SpotPrice;
use App\Models\SpotPriceHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SpotSeeders extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now('UTC');
        SpotPrice::insert([
            ['metal' => 'GOLD', 'price_per_oz_cents' => 240000, 'as_of' => $now],
            ['metal' => 'SILVER', 'price_per_oz_cents' => 3000, 'as_of' => $now],
        ]);
        for ($i = 10; $i >= 1; $i--) {
            SpotPriceHistory::insert([
                ['metal' => 'GOLD', 'price_per_oz_cents' => 240000 + ($i * 10), 'as_of' => $now->copy()->subDays($i)],
                ['metal' => 'SILVER', 'price_per_oz_cents' => 3000 + $i, 'as_of' => $now->copy()->subDays($i)],
            ]);
        }
    }
}
