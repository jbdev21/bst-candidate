<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Payments\WebhookVerifier;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function payments(Request $request, WebhookVerifier $verifier)
    {
        $payload = $request->getContent();
        $sig = $request->header('X-Signature', '');
        if (! $verifier->verify(env('PAYMENT_WEBHOOK_SECRET'), $payload, $sig)) {
            return response()->json(['error' => 'invalid_signature'], 400);
        }
        $data = $request->json()->all();
        $intent = $data['payment_intent_id'] ?? '';
        $event = $data['event'] ?? '';
        $order = Order::where('payment_intent_id', $intent)->first();
        if (! $order) {
            return response()->json(['error' => 'unknown_intent'], 400);
        }
        if ($event === 'payment_authorized') {
            $order->update(['status' => 'authorized']);
        } elseif ($event === 'payment_captured' && $order->status === 'authorized') {
            $order->update(['status' => 'captured']);
        }

        return response()->json(['ok' => true]);
    }
}
