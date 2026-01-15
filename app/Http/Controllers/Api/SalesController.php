<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\TransactionSaleLine;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    //
    public function test(Request $r)
    {
        Log::info('Data', ['data' => $r->all()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_sell_price' => 'required|numeric',
            'total_items' => 'required|integer',
            'discount_amount' => 'required|numeric',
            'status' => 'required|string',
            'shipping_status' => 'required|string',
            'shipping_address' => 'nullable|string',
            'sale_lines' => 'required|array',
            'sale_lines.*.product_variant_id' => 'required|exists:product_variants,id',
            'sale_lines.*.price' => 'required|numeric',
            'sale_lines.*.qty' => 'required|integer',
            'payment.method' => 'required|in:cash,acleda,aba',
            'payment.amount' => 'required|numeric',
            'payment.status' => 'required|in:pending,completed,failed',
        ]);

        DB::beginTransaction();

        try {
            // Create transaction
            $transaction = Transaction::create([
                'user_id' => $validated['user_id'],
                'total_sell_price' => $validated['total_sell_price'],
                'total_items' => $validated['total_items'],
                'discount_amount' => $validated['discount_amount'],
                'status' => $validated['status'],
                'shipping_status' => $validated['shipping_status'],
                'shipping_address' => $validated['shipping_address'],
                'invoice_no' => 'INV-' . time() . '-' . $validated['user_id'],
            ]);

            // Create sale lines
            foreach ($validated['sale_lines'] as $line) {
                $transaction->saleLines()->create([
                    'product_variant_id' => $line['product_variant_id'],
                    'price' => $line['price'],
                    'qty' => $line['qty'],
                ]);
            }

            // Create payment
            $transaction->payments()->create([
                'amount' => $validated['payment']['amount'],
                'method' => $validated['payment']['method'],
                'status' => $validated['payment']['status'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'transaction_id' => $transaction->id,
                'invoice_no' => $transaction->invoice_no,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to process order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}