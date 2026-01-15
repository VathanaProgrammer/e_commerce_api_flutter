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
        $amount = $request->amount;

        // Store order in memory (for school project)
        $this->orders[$orderId] = ['amount' => $amount, 'paid' => false];

        $payload = [
            'merchantId' => $this->merchantId,
            'orderId' => $orderId,
            'amount' => $amount,
            'currency' => 'KHR',
            'callbackUrl' => url('/api/aba-callback'),
            'description' => 'Test payment sandbox'
        ];

        // Sign payload with RSA private key
        $privateKey = file_get_contents($this->privateKeyPath);
        $signature = '';
        openssl_sign(json_encode($payload), $signature, $privateKey, OPENSSL_ALGO_SHA256);
        $signature = base64_encode($signature);

        // Use Guzzle to send request
        try {
            $client = new Client();
            $response = $client->post($this->abaApiUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Signature' => $signature,
                ],
                'json' => $payload,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return response()->json(['qrImageUrl' => $data['qrImageUrl'] ?? null]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Failed to create QR'], 500);
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