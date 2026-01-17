<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\PaymentIntent;
use Illuminate\Support\Facades\Log;

class ABASandboxController extends Controller
{
    private $merchantId;
    private $apiKey;
    private $callbackUrl;

    public function __construct()
    {
        $this->merchantId = config('services.aba.merchant_id');
        $this->apiKey = config('services.aba.api_key');
        $this->callbackUrl = url('/api/aba-callback');
    }

    public function createQR(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'payload' => 'required|array',
        ]);

        $userId = $request->user_id;
        $payload = $request->payload;
        $totalAmount = $payload['total'];

        $intent = PaymentIntent::create([
            'user_id' => $userId,
            'gateway' => 'aba',
            'amount' => $totalAmount,
            'currency' => 'KHR',
            'payload_snapshot' => json_encode($payload),
            'status' => 'pending',
        ]);

        $tranId = 'O' . substr(md5($intent->id . time()), 0, 15);
        $reqTime = now()->utc()->format('YmdHis');

        // Prepare items JSON - DO NOT use JSON_UNESCAPED_SLASHES for hash
        $itemsJson = json_encode($payload['items']);
        $itemsBase64 = base64_encode($itemsJson);
        
        // Prepare callback URL
        $callbackUrlRaw = $this->callbackUrl;
        $callbackUrlBase64 = base64_encode($callbackUrlRaw);
        
        // Amount - CRITICAL: KHR should NOT have decimal points!
        // USD: 100.00, KHR: 1430 (no decimals)
        $amount = number_format((float)$totalAmount, 0, '', '');

        // ✅ CRITICAL: Hash string order for generate-qr API
        // Based on PDF docs page 27: req_time + merchant_id + tran_id + amount + items +
        // shipping + firstname + lastname + email + phone + type + payment_option + 
        // return_url + cancel_url + continue_success_url + return_deeplink + currency + 
        // custom_fields + return_params
        
        // BUT the QR API documentation shows a DIFFERENT order without shipping/cancel_url!
        // Correct order for generate-qr: req_time + merchant_id + tran_id + amount + items + 
        // first_name + last_name + email + phone + purchase_type + payment_option +  
        // currency + callback_url + return_deeplink + custom_fields + return_params + 
        // payout + lifetime + qr_image_template
        
        // CORRECT ORDER: purchase_type → payment_option → currency
        $hashString = 
            $reqTime .
            $this->merchantId .
            $tranId .
            $amount .
            $itemsJson .
            '' .  // first_name
            '' .  // last_name  
            '' .  // email
            '' .  // phone
            'purchase' .
            'abapay_khqr' .  // payment_option comes BEFORE currency
            'KHR' .          // currency comes AFTER payment_option
            $callbackUrlRaw .
            '' .  // return_deeplink  
            '' .  // custom_fields
            '' .  // return_params
            '' .  // payout
            6 .
            'template3_color';

        // Generate hash
        $hash = base64_encode(hash_hmac('sha512', $hashString, $this->apiKey, true));

        // Detailed logging
        Log::info('ABA QR Request Debug', [
            'req_time' => $reqTime,
            'merchant_id' => $this->merchantId,
            'tran_id' => $tranId,
            'amount' => $amount,
            'items_json' => $itemsJson,
            'callback_url_raw' => $callbackUrlRaw,
            'hash_string' => $hashString,
            'hash_string_length' => strlen($hashString),
            'hash' => $hash,
        ]);

        // Build API payload
        $payloadQR = [
            'req_time' => $reqTime,
            'merchant_id' => $this->merchantId,
            'tran_id' => $tranId,
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone' => '',
            'amount' => $amount,
            'purchase_type' => 'purchase',
            'payment_option' => 'abapay_khqr',
            'items' => $itemsBase64,  // Base64 for API
            'currency' => 'KHR',
            'callback_url' => $callbackUrlBase64,  // Base64 for API
            'return_deeplink' => null,
            'custom_fields' => null,
            'return_params' => null,
            'payout' => null,
            'lifetime' => 6,
            'qr_image_template' => 'template3_color',
            'hash' => $hash,
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post('https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/generate-qr', $payloadQR);

        if (!$response->successful()) {
            Log::error('ABA QR Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'request_payload' => $payloadQR,
            ]);
            return response()->json(['error' => $response->body()], 500);
        }

        $intent->gateway_tran_id = $tranId;
        $intent->save();

        return response()->json([
            'payment_intent_id' => $intent->id,
            'tran_id' => $tranId,
            'qr' => $response->json(),
        ]);
    }

    public function callback(Request $request)
    {
        $tranId = $request->input('tran_id');
        $status = $request->input('status');

        $intent = PaymentIntent::where('gateway_tran_id', $tranId)->first();
        if (!$intent) return response()->json(['error' => 'Invalid tran_id'], 404);

        $intent->status = $status === 'success' ? 'success' : 'failed';
        $intent->save();

        if ($intent->status === 'success') {
            $payload = json_decode($intent->payload_snapshot, true);
            $transaction = \App\Models\Transaction::create([
                'user_id' => $intent->user_id,
                'total_sell_price' => $payload['total'],
                'total_items' => count($payload['items']),
                'status' => 'completed',
                'shipping_address' => $payload['shipping'],
                'discount_amount' => $payload['discount'] ?? 0,
                'shipping_charge' => $payload['shipping_charge'] ?? 0,
            ]);

            foreach ($payload['items'] as $item) {
                \App\Models\TransactionSaleLine::create([
                    'transaction_id' => $transaction->id,
                    'product_variant_id' => $item['variant_id'],
                    'price' => $item['price'],
                    'qty' => $item['quantity'],
                ]);
            }
        }

        Log::info('ABA Callback', $request->all());
        return response()->json(['ack' => 'ok']);
    }
}