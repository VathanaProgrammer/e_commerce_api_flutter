<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class ABASandboxController extends Controller
{
    private $merchantId;
    private $apiKey;
    private $callbackUrl;

    public function __construct()
    {
        $this->merchantId = config('services.aba.merchant_id');
        $this->apiKey = config('services.aba.api_key'); // HMAC key from ABA sandbox
        $this->callbackUrl = url('/api/aba-callback');
    }

    public function createQR(Request $request)
    {
        $request->validate([
            'orderId' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        $orderId = $request->orderId;
        $amount = number_format((float) $request->amount, 2, '.', '');
        $reqTime = now()->utc()->format('YmdHis');
        $tranId = 'ORD-' . $orderId . '-' . time();
        $currency = 'KHR';

        // Base64 encode items & callback URL
        $items = base64_encode(json_encode([['name' => 'Test Item', 'quantity' => 1, 'price' => $amount]], JSON_UNESCAPED_SLASHES));
        $callbackUrl = base64_encode($this->callbackUrl);

        $purchaseType = 'purchase';
        $paymentOption = 'abapay_khqr';
        $lifetime = 6;
        $qrImageTemplate = 'template3_color';

        // Build hash string in exact order
        $hashString =
            $reqTime .
            $this->merchantId .
            $tranId .
            $amount .
            $items .
            '' . // first_name
            '' . // last_name
            '' . // email
            '' . // phone
            $purchaseType .
            $paymentOption .
            $callbackUrl .
            '' . // return_deeplink
            $currency .
            '' . // custom_fields
            '' . // return_params
            '' . // payout
            $lifetime .
            $qrImageTemplate;

        $hash = base64_encode(hash_hmac('sha512', $hashString, $this->apiKey, true));

        $payload = [
            'req_time'          => $reqTime,
            'merchant_id'       => $this->merchantId,
            'tran_id'           => $tranId,
            'first_name'        => '',
            'last_name'         => '',
            'email'             => '',
            'phone'             => '',
            'amount'            => $amount,
            'purchase_type'     => $purchaseType,
            'payment_option'    => $paymentOption,
            'items'             => $items,
            'currency'          => $currency,
            'callback_url'      => $callbackUrl,
            'return_deeplink'   => '',
            'custom_fields'     => '',
            'return_params'     => '',
            'payout'            => '',
            'lifetime'          => $lifetime,
            'qr_image_template' => $qrImageTemplate,
            'hash'              => $hash,
        ];

        // Call ABA sandbox QR API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/generate-qr', $payload);

        if (!$response->successful()) {
            Log::error('ABA QR Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return response()->json(['error' => $response->body()], 500);
        }

        return response()->json([
            'tran_id' => $tranId,
            'qr' => $response->json()
        ]);
    }

    public function callback(Request $request)
    {
        Log::info('ABA Callback', $request->all());
        return response()->json(['ack' => 'ok']);
    }
}