<script setup>
import { ref } from 'vue';

const sku = ref('GOLD1OZ');
const qty = ref(1);
const quote = ref(null);
const status = ref('');

async function getQuote() {
    const res = await fetch('/api/quote', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ sku: sku.value, qty: qty.value }),
    });
    quote.value = await res.json();
}

async function checkout() {
    status.value = 'Loading...';
    const res = await fetch('/api/checkout', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Idempotency-Key': crypto.randomUUID() },
        body: JSON.stringify({ quote_id: quote.value.quote_id }),
    });
    if (res.status === 200) {
        status.value = 'Order created';
    } else {
        const j = await res.json();
        status.value = j.error || 'Error';
    }
}
</script>

<template>
    <div class="mx-auto max-w-xl space-y-4 p-6">
        <h1 class="text-2xl font-bold">Quote Demo</h1>
        <div class="space-x-2">
            <select v-model="sku">
                <option value="GOLD1OZ">GOLD1OZ</option>
                <option value="SILV1OZ">SILV1OZ</option>
                <option value="SILV10OZ">SILV10OZ</option>
            </select>
            <input type="number" v-model="qty" min="1" class="border px-2" />
            <button class="bg-black px-3 py-1 text-white" @click="getQuote">Get Quote</button>
        </div>
        <div v-if="quote" class="border p-3">
            <div>Unit Price: {{ Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(quote.unit_price_cents / 100) }}</div>
            <div class="py-2">
                Total Price: {{ Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format((quote.unit_price_cents / 100) * qty) }}
            </div>
            <div>Expires: {{ quote.quote_expires_at }}</div>
            <button class="mt-3 bg-emerald-600 px-3 py-1 text-white" @click="checkout">Checkout</button>
        </div>
        <div v-if="status">Status: {{ status }}</div>
    </div>
</template>
