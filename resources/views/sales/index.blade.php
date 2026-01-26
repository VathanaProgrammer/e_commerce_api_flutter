@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <x-widget title="Sales Orders">
                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                <i class="bi bi-receipt me-1"></i> All Orders
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                <i class="bi bi-funnel me-1"></i> Click row to view details
                            </span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="salesOrdersTable" class="table table-hover display nowrap w-full">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <input type="checkbox" id="select-all" class="form-check-input">
                                    </th>
                                    <th>Action</th>
                                    <th>User</th>
                                    <th>Total Items</th>
                                    <th>Total Price</th>
                                    <th>Status</th>
                                    <th>Payments</th>
                                    <th>Shipping Address</th>
                                    <th>Shipping Status</th>
                                    <th>Invoice No</th>
                                    <th>Discount</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </x-widget>
            </div>
        </div>
    </div>

    @include('sales.view_model')

    <style>
        #salesOrdersTable thead th {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: none;
            padding: 15px 12px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #475569;
        }

        #salesOrdersTable tbody tr {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
        }

        #salesOrdersTable tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.08) !important;
            transform: scale(1.002);
        }

        #salesOrdersTable tbody td {
            padding: 14px 12px;
            vertical-align: middle;
        }
    </style>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            let map = null;
            let marker = null;
            
            const table = $('#salesOrdersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('sales.orders.data') }}',
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
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
                        data: 'total_sell_price',
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

            $(document).on('click', '.transaction-checkbox', function(e) {
                e.stopPropagation();
            });

            $('#select-all').on('change', function() {
                $('.transaction-checkbox').prop('checked', this.checked);
            });

            function getSelectedTransactionIds() {
                return $('.transaction-checkbox:checked')
                    .map(function() {
                        return $(this).val();
                    })
                    .get();
            }

            // Initialize modal once and reuse
            const transactionModalEl = document.getElementById('transactionModal');
            let t_modal = null;
            
            // Get or create modal instance
            function getTransactionModal() {
                if (!t_modal) {
                    t_modal = new bootstrap.Modal(transactionModalEl, {
                        backdrop: true,
                        keyboard: true
                    });
                }
                return t_modal;
            }

            // Clean up modal on hidden
            transactionModalEl.addEventListener('hidden.bs.modal', function () {
                // Ensure backdrop is removed
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css({
                    'overflow': '',
                    'padding-right': ''
                });
            });

            // Click handler for table rows
            $(document).on('click', '#salesOrdersTable tbody tr', function(e) {
                // Ignore clicks on checkboxes and dropdowns
                if ($(e.target).closest('.dropdown, .transaction-checkbox, input[type="checkbox"]').length) {
                    return;
                }

                const rowData = table.row(this).data();

                if (rowData && rowData.id) {
                    loadTransactionDetails(rowData.id);
                }
            });

            $(document).on('click', '.view-details-btn', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                loadTransactionDetails(id);
            });

            function loadTransactionDetails(transactionId) {
                const modal = getTransactionModal();
                modal.show();

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
                        $('#modal-shipping').text('$ ' + data.transaction.shipping_charge || '--');
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
                        
                        // Close modal and clean up
                        const modal = getTransactionModal();
                        modal.hide();
                        
                        // Show error after modal closes
                        setTimeout(function() {
                            toastr.error(errorMessage);
                        }, 300);
                    }
                });
            }

            // Delete Sale/Transaction
            $(document).on('click', '.delete-sale', function(e) {
                e.preventDefault();
                let url = $(this).data('url');

                showConfirmModal("Are you sure you want to delete this transaction?", function() {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message || 'Transaction deleted successfully');
                                table.ajax.reload();
                            } else {
                                toastr.error(res.message || 'Failed to delete transaction');
                            }
                        },
                        error: function() {
                            toastr.error('Something went wrong');
                        }
                    });
                });
            });
            // Print Details
            $(document).on('click', '#btnPrintModal', function() {
                const invoiceNo = $('#modal-invoice-no').text();
                const content = $('#transactionModal .modal-body').html();
                const businessName = "{{ session('business.name', 'My Business') }}";
                const businessAddress = "{{ session('business.address', '') }}";
                const businessMobile = "{{ session('business.mobile', '') }}";
                const businessEmail = "{{ session('business.email', '') }}";
                
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Print Invoice - ${invoiceNo}</title>
                            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                            <style>
                                body { padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
                                .print-header { border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
                                .business-name { font-size: 24px; font-weight: bold; color: #1e293b; }
                                .invoice-title { font-size: 20px; color: #64748b; }
                                .table th { background-color: #f8fafc !important; }
                                @media print {
                                    .btn-print-hidden { display: none; }
                                }
                            </style>
                        </head>
                        <body>
                            <div class="print-header d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="business-name">${businessName}</div>
                                    <div class="text-muted small">${businessAddress}</div>
                                    <div class="text-muted small">Phone: ${businessMobile} | Email: ${businessEmail}</div>
                                </div>
                                <div class="text-end">
                                    <div class="invoice-title">INVOICE</div>
                                    <div class="fw-bold">${invoiceNo}</div>
                                    <div class="small text-muted">Date: ${$('#modal-date').text()}</div>
                                </div>
                            </div>
                            ${content}
                            <script>
                                window.onload = function() {
                                    window.print();
                                    setTimeout(function() { window.close(); }, 100);
                                };
                            <\/script>
                        </body>
                    </html>
                `);
                printWindow.document.close();
            });
        });
    </script>
@endsection
