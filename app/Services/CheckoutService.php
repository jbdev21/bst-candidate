<?php

namespace App\Services;

use App\DTO\IdempotencyKeyDTO;
use App\DTO\OrderDTO;
use App\DTO\OrderLineDTO;
use App\DTO\ProductDTO;
use App\DTO\SpotPriceDTO;
use App\Models\IdempotencyKey;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\PriceQuote;
use App\Models\Product;
use App\Models\SpotPrice;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(private FulfillmentInventoryService $inventory) {}

    /**
     * @return array<string, mixed>
     */
    public function beginCheckout(string $quoteId, string $idempotencyKey): array
    {
        $quote = PriceQuote::findOrFail($quoteId);

        // Check if quote has expired (treat now() == quote_expires_at as expired)
        if ($quote->quote_expires_at <= now()) {
            return [
                'error' => 'REQUOTE_REQUIRED',
                'status' => 409,
            ];
        }

        // Check tolerance against current spot price
        // First get the product to find its metal
        $productModel = Product::where('sku', $quote->sku)->first();
        if (! $productModel) {
            return [
                'error' => 'PRODUCT_NOT_FOUND',
                'status' => 404,
            ];
        }
        $product = ProductDTO::fromArray($productModel->toArray());

        $currentSpotModel = SpotPrice::where('metal', $product->metal)
            ->orderBy('as_of', 'desc')
            ->first();

        if (! $currentSpotModel) {
            return [
                'error' => 'SPOT_PRICE_UNAVAILABLE',
                'status' => 500,
            ];
        }
        $currentSpot = SpotPriceDTO::fromArray($currentSpotModel->toArray());

        // Calculate price change in basis points
        $originalSpot = $quote->basis_spot_cents;
        $currentSpotPrice = $currentSpot->price_per_oz_cents;

        if ($originalSpot > 0) {
            $changeBps = abs(($currentSpotPrice - $originalSpot) * 10000) / $originalSpot;

            if ($changeBps > $quote->tolerance_bps) {
                return [
                    'error' => 'REQUOTE_REQUIRED',
                    'status' => 409,
                ];
            }
        }

        // Check for existing idempotency key
        $existingKeyModel = IdempotencyKey::where('key', $idempotencyKey)
            ->where('purpose', 'checkout')
            ->first();

        if ($existingKeyModel) {
            $existingKey = IdempotencyKeyDTO::fromArray($existingKeyModel->toArray());
            // Return existing order
            $orderModel = Order::findOrFail($existingKey->order_id);
            $order = OrderDTO::fromArray($orderModel->toArray());

            return [
                'success' => true,
                'order_id' => $order->id,
                'payment_intent_id' => $order->payment_intent_id,
                'status' => 200,
            ];
        }

        // Check inventory availability via fulfillment service
        $availableQty = $this->inventory->getAvailableQuantity($quote->sku);
        if ($availableQty < $quote->qty) {
            return [
                'error' => 'OUT_OF_STOCK',
                'status' => 409,
            ];
        }

        // Create order
        return DB::transaction(function () use ($quote, $idempotencyKey) {
            $totalCents = $quote->unit_price_cents * $quote->qty;

            $orderDTO = new OrderDTO(
                user_id: (string) $quote->user_id,
                total_cents: $totalCents,
                status: 'pending',
                payment_intent_id: 'pi_'.uniqid().'_'.bin2hex(random_bytes(8))
            );

            $orderModel = Order::create($orderDTO->toArray());
            $orderDTO = OrderDTO::fromArray($orderModel->toArray());

            // Create order line
            $orderLineDTO = new OrderLineDTO(
                order_id: (string) $orderModel->id,
                sku: $quote->sku,
                qty: $quote->qty,
                unit_price_cents: $quote->unit_price_cents,
                subtotal_cents: $totalCents
            );

            OrderLine::create($orderLineDTO->toArray());

            // Store idempotency key
            $idempotencyKeyDTO = new IdempotencyKeyDTO(
                key: $idempotencyKey,
                purpose: 'checkout',
                order_id: (string) $orderModel->id,
                created_at: now()->toDateTimeString()
            );

            IdempotencyKey::create($idempotencyKeyDTO->toArray());

            return [
                'success' => true,
                'order_id' => $orderDTO->id,
                'payment_intent_id' => $orderDTO->payment_intent_id,
                'status' => 200,
            ];
        });
    }
}
