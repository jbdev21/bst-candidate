<script setup lang="ts">
import { ref, computed, onUnmounted } from 'vue';

interface Product {
    id: number;
    sku: string;
    name: string;
    metal: string;
    weight_oz: string;
    premium_cents: number;
    active: number;
    created_at: string | null;
    updated_at: string | null;
}

interface Quote {
    quote_id: number;
    unit_price_cents: number;
    quote_expires_at: string;
}

interface ApiError {
    error: string;
}

interface Props {
    products: Product[];
}

const props = defineProps<Props>();

const sku = ref<string>(props.products[0]?.sku || 'GOLD1OZ');
const qty = ref<number>(1);
const quote = ref<Quote | null>(null);
const error = ref<string>('');
const success = ref<string>('');
const isProcessing = ref<boolean>(false);
const timeRemaining = ref<number>(0);
let countdownInterval: ReturnType<typeof setInterval> | null = null;

const isQuoteExpired = computed((): boolean => timeRemaining.value <= 0);
const isCheckoutDisabled = computed((): boolean => isQuoteExpired.value || isProcessing.value || !quote.value);

const timeDisplay = computed((): string => {
    const minutes: number = Math.floor(timeRemaining.value / 60);
    const seconds: number = timeRemaining.value % 60;
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
});

const errorMessage = computed((): string => {
    const errorMap: Record<string, string> = {
        'REQUOTE_REQUIRED': 'Prices moved while you were checking out. Get a fresh quote to continue.',
        'OUT_OF_STOCK': 'This item just sold out at our fulfillment partner. Try a smaller quantity or another product.',
        'invalid_signature': 'We couldn\'t confirm payment with the provider. Please retry.',
        'unknown_intent': 'We couldn\'t confirm payment with the provider. Please retry.'
    };
    return errorMap[error.value] || error.value;
});

function startCountdown(): void {
    if (!quote.value?.quote_expires_at) return;
    
    const updateCountdown = (): void => {
        if (!quote.value) return;
        
        const expiryTime: number = new Date(quote.value.quote_expires_at).getTime();
        const now: number = new Date().getTime();
        const remaining: number = Math.max(0, Math.floor((expiryTime - now) / 1000));
        timeRemaining.value = remaining;
        
        if (remaining === 0 && countdownInterval) {
            clearInterval(countdownInterval);
        }
    };
    
    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);
}

function clearMessages(): void {
    error.value = '';
    success.value = '';
}

async function getQuote(): Promise<void> {
    clearMessages();
    isProcessing.value = true;
    
    try {
        const response: Response = await fetch('/api/quote', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sku: sku.value, qty: qty.value }),
        });
        
        if (response.ok) {
            const quoteData: Quote = await response.json();
            quote.value = quoteData;
            startCountdown();
        } else {
            const errorData: ApiError = await response.json();
            error.value = errorData.error || 'Failed to get quote';
        }
    } catch {
        error.value = 'Network error occurred';
    } finally {
        isProcessing.value = false;
    }
}

async function checkout(): Promise<void> {
    if (isCheckoutDisabled.value || !quote.value) return;
    
    clearMessages();
    isProcessing.value = true;
    
    try {
        const response: Response = await fetch('/api/checkout', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'Idempotency-Key': crypto.randomUUID() 
            },
            body: JSON.stringify({ quote_id: quote.value.quote_id }),
        });
        
        if (response.ok) {
            success.value = 'Order created successfully!';
            quote.value = null;
            timeRemaining.value = 0;
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
        } else {
            const errorData: ApiError = await response.json();
            error.value = errorData.error || 'Checkout failed';
        }
    } catch {
        error.value = 'Network error occurred';
    } finally {
        isProcessing.value = false;
    }
}

onUnmounted(() => {
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
});
</script>

<template>
    <div class="mx-auto max-w-xl space-y-4 p-6">
        <h1 class="text-2xl font-bold">Quote Demo</h1>
        
        <!-- Error Banner -->
        <div 
            v-if="error" 
            role="alert" 
            tabindex="0"
            class="rounded-md bg-red-50 border border-red-200 p-4 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
        >
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-800">{{ errorMessage }}</p>
                    <button 
                        @click="getQuote"
                        class="mt-2 inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    >
                        Get Fresh Quote
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Banner -->
        <div 
            v-if="success" 
            role="alert" 
            tabindex="0"
            class="rounded-md bg-green-50 border border-green-200 p-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
        >
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L7.53 10.36a.75.75 0 00-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-green-800">{{ success }}</p>
                </div>
            </div>
        </div>

        <!-- Quote Form -->
        <div class="space-y-3">
            <div class="flex gap-2">
                <select v-model="sku" class="rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option v-for="product in props.products" :key="product.sku" :value="product.sku">
                        {{ product.name }} ({{ product.sku }})
                    </option>
                </select>
                <input 
                    type="number" 
                    v-model="qty" 
                    min="1" 
                    class="rounded-md border border-gray-300 px-3 py-2 w-20 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                />
                <button 
                    @click="getQuote" 
                    :disabled="isProcessing"
                    class="bg-black px-4 py-2 text-white rounded-md hover:bg-gray-800 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
                >
                    {{ isProcessing ? 'Loading...' : 'Get Quote' }}
                </button>
            </div>
        </div>

        <!-- Quote Display -->
        <div v-if="quote" class="border rounded-lg p-4 bg-gray-50">
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="font-medium">Unit Price:</span>
                    <span class="font-semibold">{{ Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(quote.unit_price_cents / 100) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="font-medium">Total Price:</span>
                    <span class="font-semibold text-lg">{{ Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format((quote.unit_price_cents / 100) * qty) }}</span>
                </div>
                
                <!-- Countdown Timer -->
                <div class="flex justify-between items-center py-2 border-t">
                    <span class="font-medium">Quote expires in:</span>
                    <span 
                        :class="[
                            'font-mono text-lg font-bold',
                            timeRemaining <= 30 ? 'text-red-600' : 'text-green-600'
                        ]"
                    >
                        {{ timeDisplay }}
                    </span>
                </div>
                
                <!-- Quote Expired Message -->
                <div v-if="isQuoteExpired" class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                    <p class="text-yellow-800 font-medium">Quote expired — get a new quote.</p>
                    <button 
                        @click="getQuote"
                        class="mt-2 inline-flex items-center rounded-md bg-yellow-600 px-3 py-2 text-sm font-semibold text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
                    >
                        Get New Quote
                    </button>
                </div>
                
                <!-- Checkout Button -->
                <div v-else>
                    <button 
                        @click="checkout" 
                        :disabled="isCheckoutDisabled"
                        class="w-full mt-3 bg-emerald-600 px-4 py-3 text-white rounded-md font-semibold hover:bg-emerald-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                    >
                        {{ isProcessing ? 'Processing...' : 'Checkout' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
