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
            'payload' => 'required|array', // cart + totals + shipping + discount
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

        $itemsJson = json_encode($payload['items'], JSON_UNESCAPED_SLASHES);
        $itemsBase64 = base64_encode($itemsJson);
        $callbackUrlRaw = $this->callbackUrl;
        $callbackUrlBase64 = base64_encode($callbackUrlRaw);
        $amountStr = number_format($totalAmount, 2, '.', '');

        $hashString =
            $reqTime .
            $this->merchantId .
            $tranId .
            $amountStr .
            $itemsJson .
            '' . '' . '' . '' .
            'purchase' .
            'abapay_khqr' .
            $callbackUrlRaw .
            '' .
            'KHR' .
            '' . '' . '' .
            6 .
            'template3_color';

        $hash = base64_encode(hash_hmac('sha512', $hashString, $this->apiKey, true));

        $payloadQR = [
            'req_time' => $reqTime,
            'merchant_id' => $this->merchantId,
            'tran_id' => $tranId,
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone' => '',
            'amount' => $amountStr,
            'purchase_type' => 'purchase',
            'payment_option' => 'abapay_khqr',
            'items' => $itemsBase64,
            'currency' => 'KHR',
            'callback_url' => $callbackUrlBase64,
            'return_deeplink' => '',
            'custom_fields' => '',
            'return_params' => '',
            'payout' => '',
            'lifetime' => 6,
            'qr_image_template' => 'template3_color',
            'hash' => $hash,
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post('https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/generate-qr', $payloadQR);

        if (!$response->successful()) {
            Log::error('ABA QR Error', ['status' => $response->status(), 'body' => $response->body()]);
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
        $status = $request->input('status'); // success / failed

        $intent = PaymentIntent::where('gateway_tran_id', $tranId)->first();
        if (!$intent) return response()->json(['error' => 'Invalid tran_id'], 404);

        $intent->status = $status === 'success' ? 'success' : 'failed';
        $intent->save();

        // ✅ Only now create actual transaction
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