<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Create Stripe Payment Intent
     */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'planId' => 'required|integer',
            'amount' => 'required|integer|min:50', // amount in cents, minimum 0.50 in currency
        ]);

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount,
                'currency' => 'mad', // Morocco Dirham, change if needed
                'metadata' => [
                    'plan_id' => $request->planId,
                    'user_id' => $request->user()->id ?? null, // if auth used
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create payment intent: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify PayPal payment by order ID
     */
public function verifyPaypalPayment(Request $request)
{
    $request->validate([
        'orderID' => 'required|string',
    ]);

    $orderId = $request->input('orderID');
    Log::info('Starting PayPal verification', ['orderID' => $orderId]);

    // Step 1: Get PayPal OAuth Access Token with increased timeout
    $response = Http::asForm()->withBasicAuth(
        env('PAYPAL_CLIENT_ID'),
        env('PAYPAL_SECRET')
    )
    ->timeout(30)  // Increase timeout here
    ->post(env('PAYPAL_API_URL') . '/v1/oauth2/token', [
        'grant_type' => 'client_credentials'
    ]);

    if (!$response->ok()) {
        Log::error('Failed to authenticate with PayPal', ['response' => $response->body()]);
        return response()->json(['error' => 'Failed to authenticate with PayPal'], 500);
    }

    $accessToken = $response->json()['access_token'];
    Log::info('Obtained PayPal access token');

    // Step 2: Get order details from PayPal with increased timeout
    $orderResponse = Http::withToken($accessToken)
        ->timeout(30)  // Increase timeout here as well
        ->get(env('PAYPAL_API_URL') . "/v2/checkout/orders/{$orderId}");

    if (!$orderResponse->ok()) {
        Log::error('Failed to fetch PayPal order', ['response' => $orderResponse->body()]);
        return response()->json(['error' => 'Failed to fetch PayPal order'], 500);
    }

    $orderData = $orderResponse->json();
    Log::info('Fetched PayPal order data', ['orderData' => $orderData]);

    // Check if payment is completed
    if (isset($orderData['status']) && $orderData['status'] === 'COMPLETED') {
        Log::info('PayPal payment completed successfully', ['orderID' => $orderId]);

        // TODO: Save transaction to DB or activate plan subscription here

        return response()->json([
            'success' => true,
            'message' => 'Payment verified successfully.',
            'order' => $orderData,
        ]);
    }

    Log::warning('PayPal payment not completed', ['orderID' => $orderId, 'status' => $orderData['status'] ?? 'unknown']);

    return response()->json(['error' => 'Payment not completed'], 400);
}

}
