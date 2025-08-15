<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spot_prices', function (Blueprint $t) {
            $t->id();
            $t->string('metal');
            $t->integer('price_per_oz_cents');
            $t->timestamp('as_of');
        });
        Schema::create('spot_price_histories', function (Blueprint $t) {
            $t->id();
            $t->string('metal');
            $t->integer('price_per_oz_cents');
            $t->timestamp('as_of');
            $t->index(['metal', 'as_of']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spot_price_histories');
        Schema::dropIfExists('spot_prices');
    }
};
