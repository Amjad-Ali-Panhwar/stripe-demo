<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Order;
use Illuminate\Support\Str;

class StripeController extends Controller
{
      // Show payment page
    public function showCheckoutPage()
    {
        return view('checkout'); // we'll create this view
    }

    // Create checkout session (POST)
    public function createCheckoutSession(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret') ?? env('STRIPE_SECRET'));

        // Example: product price $10 => 1000 cents
        $amount = 1000;
        $currency = 'usd';
        $email = $request->input('email', null);

        // Create an order record with pending status first
        $order = Order::create([
            'email' => $email,
            'stripe_session_id' => 'temp_' . Str::random(10),
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
        ]);

        // Use idempotency key to avoid duplicate charges
        $idempotencyKey = 'checkout_' . $order->id . '_' . time();

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => 'Demo Product',
                    ],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'customer_email' => $email,
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.cancel'),
        ], [
            'idempotency_key' => $idempotencyKey
        ]);

        // Save real session id on order
        $order->update(['stripe_session_id' => $session->id]);

        // Return session URL for redirect (you can also return session id to handle via JS)
        return response()->json(['url' => $session->url]);
    }

    // Success page
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        return view('success', compact('sessionId'));
    }

    // Cancel page
    public function cancel()
    {
        return view('cancel');
    }

    // Webhook endpoint (we'll route to this)
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['message' => 'Invalid payload'], 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // Handle the event
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // Find order by session id
            $order = Order::where('stripe_session_id', $session->id)->first();
            if ($order) {
                $order->update([
                    'status' => 'paid',
                    'stripe_payment_intent_id' => $session->payment_intent ?? null,
                ]);
            }
        }

        if ($event->type === 'payment_intent.payment_failed') {
            $intent = $event->data->object;
            // Optional: mark order failed by matching payment_intent
            Order::where('stripe_payment_intent_id', $intent->id)
                ->update(['status' => 'failed']);
        }

        // Respond with 2xx to acknowledge receipt of the event
        return response()->json(['received' => true], 200);
    }
}
