<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Enums\PaymentStatus;
use App\Enums\ShippingStatus;
use App\Enums\TransactionStatus;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SalesController extends Controller
{
    /**
     * Display the sales list page
     */
    public function index()
    {
        return view('sales.index');
    }

    /**
     * Get DataTables data
     */
    public function data(Request $request)
    {
        $transactions = Transaction::with(['user', 'payments'])->latest();

        return DataTables::of($transactions)
            ->addColumn('checkbox', function ($tx) {
                return '
                        <input type="checkbox"
                            class="form-check-input transaction-checkbox"
                            value="' . $tx->id . '">
                    ';
            })

            ->addColumn(
                'user',
                fn($tx) =>
                $tx->user
                    ? $tx->user->first_name . ' ' . $tx->user->last_name
                    : 'Guest'
            )

            ->addColumn('total_items', fn($tx) => $tx->total_items)

            ->editColumn('total_sell_price', fn($tx) => '$ ' . number_format($tx->total_sell_price, 2))

            ->editColumn('status', fn($tx) => $tx->status->badge())

            ->addColumn('payments', function ($tx) {
                if ($tx->payments->isEmpty()) {
                    return '<span class="text-muted small">--</span>';
                }

                $html = '<ul class="mb-0 list-unstyled">';
                foreach ($tx->payments as $p) {
                    $html .= '<li class="small">'
                        . strtoupper($p->method)
                        . ' ' . $p->status->badge()
                        . '</li>';
                }
                $html .= '</ul>';

                return $html;
            })

            ->editColumn('shipping_address', fn($tx) => $tx->shipping_address ?? '<span class="text-muted">--</span>')

            ->editColumn('shipping_status', fn($tx) => $tx->shipping_status->badge())

            ->editColumn('invoice_no', fn($tx) => $tx->invoice_no ?? '<span class="text-muted">--</span>')

            ->editColumn('discount_amount', fn($tx) => '$ ' . number_format($tx->discount_amount, 2))

            ->addColumn('action', function ($tx) {
                $mapLink = '';
                if ($tx->lat && $tx->long) {
                    $mapLink = '<li>
                                <a class="dropdown-item"
                                   href="https://www.openstreetmap.org/?mlat=' . $tx->lat . '&mlon=' . $tx->long . '#map=15/' . $tx->lat . '/' . $tx->long . '"
                                   target="_blank">
                                   View on Map
                                </a>
                            </li>';
                }

                return '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item view-details-btn"
                                   href="#"
                                   data-id="' . $tx->id . '">
                                   <i class="bi bi-eye me-2"></i>View Details
                                </a>
                            </li>
                            ' . $mapLink . '
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger delete-sale"
                                   href="#"
                                   data-url="' . route('sales.destroy', $tx->id) . '">
                                    <i class="bi bi-trash me-2"></i>Delete
                                </a>
                            </li>
                        </ul>
                    </div>
                ';
            })

            ->rawColumns([
                'checkbox',
                'status',
                'payments',
                'shipping_address',
                'shipping_status',
                'invoice_no',
                'discount_amount',
                'total_sell_price',
                'action',
            ])

            ->make(true);
    }

    /**
     * Get transaction details for modal
     */
    public function show($id)
    {
        $transaction = Transaction::with([
            'user',
            'saleLines.variant.product',
            'saleLines.variant.attributeValues.attribute',
            'payments',
        ])->findOrFail($id);

        $txStatus = $transaction->status;            // already enum
        $shippingStatus = $transaction->shipping_status; // already enum

        return response()->json([
            'transaction' => [
                'id' => $transaction->id,
                'invoice_no' => $transaction->invoice_no,
                'total_sell_price' => number_format($transaction->total_sell_price, 2),
                'total_items' => $transaction->total_items,
                'discount_amount' => number_format($transaction->discount_amount, 2),
                'shipping_address' => $transaction->shipping_address,
                'status' => $txStatus->label(),
                'status_badge' => $txStatus->badge(),
                'shipping_charge' => $transaction->shipping_charge ?? '--',
                'shipping_status' => $shippingStatus->label(),
                'shipping_status_badge' => $shippingStatus->badge(),
                'created_at' => $transaction->created_at->format('H:i A d/m/Y'),
            ],

            'user' => $transaction->user ? [
                'name' => $transaction->user->first_name . ' ' . $transaction->user->last_name,
                'email' => $transaction->user->email,
                'profile_image' =>
                $transaction->user->profile_image_url
                    ?? 'https://ui-avatars.com/api/?name='
                    . urlencode($transaction->user->first_name . ' ' . $transaction->user->last_name),
            ] : null,

            'payments' => $transaction->payments->map(function ($payment) {
                $statusEnum = $payment->status; // already enum
                return [
                    'id' => $payment->id,
                    'method' => strtoupper($payment->method),
                    'status' => $statusEnum->label(),
                    'status_badge' => $statusEnum->badge(),
                    'amount' => number_format($payment->amount, 2),
                    'paid_at' => $payment->paid_at
                        ? $payment->paid_at->format('d M Y H:i A')
                        : 'N/A',
                ];
            }),

            'sale_lines' => $transaction->saleLines->map(function ($line, $index) {
                $variantDetails = $line->variant->attributeValues
                    ->map(fn($v) => $v->attribute->name . ': ' . $v->value)
                    ->implode(', ');

                return [
                    'index' => $index + 1,
                    'product_name' => $line->variant->product->name,
                    'variant' => $line->variant->sku ?: $variantDetails,
                    'price' => number_format($line->price, 2),
                    'qty' => $line->qty,
                    'subtotal' => number_format($line->price * $line->qty, 2),
                ];
            }),

            'summary' => [
                'subtotal' => number_format(
                    $transaction->total_sell_price + $transaction->discount_amount,
                    2
                ),
                'discount' => number_format($transaction->discount_amount, 2),
                'total_paid' => number_format(
                    $transaction->payments
                        ->filter(fn($p) => $p->status === PaymentStatus::Completed)
                        ->sum('amount'),
                    2
                ),
                'total_amount' => number_format($transaction->total_sell_price, 2),
                'total_items' => $transaction->total_items,
            ],
        ]);
    }
    public function destroy($id)
    {
        try {
            Transaction::findOrFail($id)->delete();
            return response()->json(['success' => true, 'message' => 'Transaction deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete transaction'], 500);
        }
    }

    /**
     * Print invoice
     */
    public function printInvoice($id)
    {
        $transaction = Transaction::with([
            'user',
            'saleLines.variant.product',
            'saleLines.variant.attributeValues.attribute',
        ])->findOrFail($id);

        return view('sales.invoices.print', compact('transaction'));
    }
}