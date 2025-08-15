<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->integer('total_cents');
            $t->string('status');
            $t->string('payment_intent_id')->unique();
            $t->timestamps();
        });
        Schema::create('order_lines', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('order_id');
            $t->string('sku');
            $t->integer('qty');
            $t->integer('unit_price_cents');
            $t->integer('subtotal_cents');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_lines');
        Schema::dropIfExists('orders');
    }
};
