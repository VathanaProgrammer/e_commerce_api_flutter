<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\PaymentIntent;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use App\Enums\ShippingStatus;
class MockQRController extends Controller
{
    public function createQR(Request $request)
    {
        Log::info('Debug', ['Message' => $request->all()]);
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
            'gateway' => 'acleda',
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

    public function autoPayAfter2Sec(Request $r, $tranId)
    {
        Log::info('Debug', ['Message' => $r->all()]);
        $intent = PaymentIntent::where('gateway_tran_id', $tranId)->firstOrFail();

        if ($intent->status !== 'pending') {
            return response()->json(['message' => 'Already processed']);
        }

        // 1️⃣ Mark PaymentIntent as success
        $intent->status = 'success';
        $intent->save();

        // 2️⃣ Create Transaction using payload_snapshot
        $payload = json_decode($intent->payload_snapshot, true);

        $transaction = DB::transaction(function () use ($intent, $payload) {
            $cartItems = $payload['items'] ?? [];
            $shipping = $payload['more_address'] ?? [];

            // Total discount calculation
            $discountAmount = 0;
            foreach ($cartItems as $item) {
                if (!empty($item['discount'])) {
                    $discount = $item['discount'];
                    $price = $item['price'];
                    $qty = $item['quantity'];
                    if ($discount['is_percentage']) {
                        $discountAmount += $price * $discount['value'] / 100 * $qty;
                    } else {
                        $discountAmount += $discount['value'] * $qty;
                    }
                }
            }

            // Create Transaction
            $transaction = \App\Models\Transaction::create([
                'user_id' => $intent->user_id,
                'total_sell_price' => $payload['total'] ?? $intent->amount,
                'total_items' => array_sum(array_column($cartItems, 'quantity')),
                'discount_amount' => $discountAmount,
                'invoice_no' => 'INV-'. Str::random(5),
                'shipping_status' => ShippingStatus::default(),
                'status' => 'completed',
                'shipping_address' => $payload['shipping_address'],
                'shipping_charge' => $payload['shipping_charge'] ?? 0,
                'lat' => $shipping['latitude'] ?? null, 
                'long' => $shipping['longitude'] ?? null,
            ]);

            // Create Transaction Sale Lines
            foreach ($cartItems as $item) {
                \App\Models\TransactionSaleLine::create([
                    'transaction_id' => $transaction->id,
                    'product_variant_id' => $item['product_id'],
                    'price' => $item['price'],
                    'qty' => $item['quantity'],
                ]);
            }

            // Create Payment record
            \App\Models\Payment::create([
                'transaction_id' => $transaction->id,
                'amount' => $payload['total'] ?? $intent->amount,
                'method' => $intent->gateway,
                'status' => PaymentStatus::Completed,
                'paid_at' => now(),
            ]);

            return $transaction;
        });

        return response()->json([
            'message' => 'Payment successful (TEST)',
            'tran_id' => $tranId,
            'amount' => $intent->amount,
            'transaction_id' => $transaction->id,
            'success' => true,
            'status' => 'success',
        ]);
    }


    public function scan($tranId)
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
            'status' => 'success', // optional for frontend check
        ]);
    }
}