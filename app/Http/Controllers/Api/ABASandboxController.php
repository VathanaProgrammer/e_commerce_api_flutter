<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class ABASandboxController extends Controller
{
    private $merchantId;
    private $privateKeyPath;
    private $abaApiUrl;

    public function __construct()
    {
        $this->merchantId = config('services.aba.merchant_id');
        $this->privateKeyPath = config('services.aba.private_key_path');
        $this->abaApiUrl = config('services.aba.api_url');
    }
    // In-memory orders for school project (or use DB)
    private $orders = [];
    public function createQR(Request $request)
    {
        $request->validate([
            'orderId' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        $orderId = $request->orderId;
        $amount = (float) $request->amount;

        // Store order in memory
        $this->orders[$orderId] = ['amount' => $amount, 'paid' => false];

        $payload = [
            'req_time' => date('YmdHis'),
            'merchant_id' => $this->merchantId,
            'tran_id' => $orderId,
            'amount' => $amount,
            'purchase_type' => 'purchase',
            'payment_option' => 'abapay_khqr',
            'items' => [['name' => 'Test Item', 'quantity' => 1, 'price' => $amount]],
            'currency' => 'KHR',
            'callback_url' => url('/api/aba-callback'), // plain URL
            'lifetime' => 6,
            'qr_image_template' => 'template3_color',
        ];

        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $privateKey = file_get_contents($this->privateKeyPath);
        $signature = '';
        openssl_sign($payloadJson, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        $payload['hash'] = base64_encode($signature);

        try {
            $client = new Client();
            $response = $client->post($this->abaApiUrl, [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ]);

            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            return response()->json([
                'qrImage' => $data['qrImage'] ?? null,
                'qrString' => $data['qrString'] ?? null,
                'status' => $data['status'] ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create QR: ' . $e], 500);
        }
    }



    public function callback(Request $request)
    {
        $orderId = $request->input('orderId');
        $status = $request->input('status');

        Log::info('ABA Callback', $request->all());

        if (isset($this->orders[$orderId])) {
            if ($status === 'PAID') {
                $this->orders[$orderId]['paid'] = true;
            }
        }

        return response()->json(['received' => true]);
    }

    public function status($orderId)
    {
        if (!isset($this->orders[$orderId])) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json(['paid' => $this->orders[$orderId]['paid']]);
    }
}