<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\PaymentIntent;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MockQRController extends Controller
{
    public function createTestQR(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'payload' => 'required|array',
        ]);

        $userId = $request->user_id;
        $payload = $request->payload;
        $totalAmount = $payload['total'];

        // 1️⃣ Create payment intent
        $intent = PaymentIntent::create([
            'user_id' => $userId,
            'gateway' => 'test_qr',
            'amount' => $totalAmount,
            'currency' => 'KHR',
            'payload_snapshot' => json_encode($payload),
            'status' => 'pending',
        ]);

        // 2️⃣ Fake transaction id
        $tranId = 't-' . Str::uuid();

        // 3️⃣ URL that QR will open when scanned
        $qrUrl = url("/api/test-qr/pay/{$tranId}");

        // 4️⃣ Generate QR (PNG → Base64)
        $qrPng = QrCode::format('png')
            ->size(300)
            ->generate($qrUrl);

        $qrBase64 = 'data:image/png;base64,' . base64_encode($qrPng);

        // 5️⃣ Save tran_id
        $intent->gateway_tran_id = $tranId;
        $intent->save();

        // 6️⃣ RETURN TO FRONTEND
        return response()->json([
            'payment_intent_id' => $intent->id,
            'tran_id' => $tranId,
            'qr_image' => $qrBase64,
            'scan_url' => $qrUrl,
        ]);
    }

    public function scan(PaymentIntent $tranId)
    {
        $intent = PaymentIntent::where('gateway_tran_id', $tranId)->firstOrFail();

        if ($intent->status !== 'pending') {
            return response()->json(['message' => 'Already processed']);
        }

        $intent->status = 'success';
        $intent->save();

        return response()->json([
            'message' => 'Payment successful (TEST)',
            'tran_id' => $tranId,
            'amount' => $intent->amount,
        ]);
    }
}