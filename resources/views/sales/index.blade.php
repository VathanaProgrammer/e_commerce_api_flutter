@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <x-widget title="Sales list">
                    <table id="salesOrdersTable" class="table table-striped table-hover display nowrap w-full">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Total Items</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Payments</th>
                                <th>Shipping Address</th>
                                <th>Shipping Status</th>
                                <th>Invoice No</th>
                                <th>Discount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </x-widget>
            </div>
        </div>
    </div>

    @include('sales.view_model')
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            const table = $('#salesOrdersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('sales.orders.data') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user',
                        name: 'user.first_name'
                    },
                    {
                        data: 'total_items',
                        name: 'total_items'
                    },
                    {
                        data: 'total_price',
                        name: 'total_sell_price'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'payments',
                        name: 'payments',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'shipping_address',
                        name: 'shipping_address',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'shipping_status',
                        name: 'shipping_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'discount_amount',
                        name: 'discount_amount'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                drawCallback: function() {
                    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(drop) {
                        new bootstrap.Dropdown(drop);
                    });
                },
                dom: '<"d-flex justify-content-between mb-2"lfB>rtip',
                buttons: ['copy', 'csv', 'excel', 'print', 'pdf', 'colvis'],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ]
            });

            let t_modal = null;

            // Click handler for table rows
            $(document).on('click', '#salesOrdersTable tbody tr', function(e) {
                if ($(e.target).closest('.dropdown').length) {
                    return;
                }

                const rowData = table.row(this).data();

                if (rowData && rowData.id) {
                    loadTransactionDetails(rowData.id);
                }
            });

            function loadTransactionDetails(transactionId) {
                t_modal = new bootstrap.Modal(document.getElementById('transactionModal'));
                t_modal.show();

                $.ajax({
                    url: '{{ route('sales.show', ':id') }}'.replace(':id', transactionId),
                    method: 'GET',
                    success: function(data) {
                        // Update invoice number
                        $('#modal-invoice-no').text('(Invoice NO: ' + (data.transaction.invoice_no ||
                            'N/A') + ')');

                        // Update user info
                        $('#modal-user-image').attr('src', data.user.profile_image);
                        $('#modal-user-name').text(data.user.name);
                        $('#modal-user-email').text(data.user.email);

                        // Update transaction details
                        $('#modal-shipping-address').text(data.transaction.shipping_address || '--');
                        $('#modal-shipping').text(data.transaction.shipping_charge || '--');
                        $('#modal-shipping-status').html(data.transaction.shipping_status_badge);
                        $('#modal-date').text(data.transaction.created_at);

                        // Update summary
                        $('#modal-total-amount').text('$' + data.summary.total_amount);
                        $('#modal-total-items').text(data.summary.total_items);
                        $('#modal-discount').text('$' + data.summary.discount);
                        $('#modal-total-paid').text('$' + data.summary.total_paid);

                        //payments
                        $('#modal-payment-status').html(data.transaction.status_badge);

                        // Update payments table
                        let paymentsHtml = '';
                        if (data.payments && data.payments.length > 0) {
                            data.payments.forEach(function(payment, index) {
                                paymentsHtml += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${payment.method}</td>
                                        <td>${payment.status_badge}</td>
                                        <td>${payment.paid_at}</td>
                                        <td>$${payment.amount}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            paymentsHtml =
                                '<tr><td colspan="5" class="text-center">No payments</td></tr>';
                        }
                        $('#modal-payments-tbody').html(paymentsHtml);

                        // Update order items table
                        let itemsHtml = '';
                        if (data.sale_lines && data.sale_lines.length > 0) {
                            data.sale_lines.forEach(function(line) {
                                itemsHtml += `
                                    <tr class="${line.index % 2 === 0 ? '' : 'bg-light'}">
                                        <td>${line.index}</td>
                                        <td>${line.product_name}</td>
                                        <td>${line.variant || 'N/A'}</td>
                                        <td>$${line.price}</td>
                                        <td>${line.qty}</td>
                                        <td>$${line.subtotal}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            itemsHtml = '<tr><td colspan="6" class="text-center">No items</td></tr>';
                        }
                        $('#modal-items-tbody').html(itemsHtml);
                    },
                    error: function(xhr) {
                        console.error('Error loading transaction:', xhr);
                        console.error('Status:', xhr.status);
                        console.error('Response:', xhr.responseText);
                        console.error('Response JSON:', xhr.responseJSON);

                        let errorMessage = 'Failed to load transaction details.';

                        if (xhr.status === 404) {
                            errorMessage = 'Transaction not found (404).';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error (500). Check console for details.';
                        } else if (xhr.status === 0) {
                            errorMessage = 'Network error. Check your connection.';
                        }

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage += '\n' + xhr.responseJSON.message;
                        }
                        t_modal.hide();
                    }
                });
            }

            $('#close-transaction-modal').on('click', function() {
                if (t_modal) {
                    t_modal.hide();
                }
            });
        });
    </script>
@endsection
