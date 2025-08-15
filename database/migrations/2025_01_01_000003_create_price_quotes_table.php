<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_quotes', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->string('sku');
            $t->integer('unit_price_cents');
            $t->integer('qty');
            $t->timestamp('quote_expires_at');
            $t->integer('basis_spot_cents');
            $t->unsignedBigInteger('basis_version');
            $t->integer('tolerance_bps');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_quotes');
    }
};
