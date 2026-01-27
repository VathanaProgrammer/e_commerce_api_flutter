<!-- Bootstrap Modal with Styled Example Data -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true" style="z-index: 999999 !important;">
    <div class="modal-dialog modal-xl-plus modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header text-gray-700">
                <h5 class="modal-title font-normal" id="transactionModalLabel">Sell details <span
                        class="text-blue-800 font-semibold" id="modal-invoice-no">(Invoice NO:)</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <!-- User Info Row -->
                <div class="bg-white p-3 rounded mb-4 gap-4 d-flex align-items-center">
                    <div class="me-3" style="width: 60px; height: 60px; overflow: hidden; border-radius: 50%;">
                        <img id="modal-user-image" src="https://imgs.search.brave.com/UxkLnybGBQEtj_vcmSoFJFPmrmGgh2j_CDexNdr-YSI/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9zdGF0/aWMudmVjdGVlenku/Y29tL3N5c3RlbS9y/ZXNvdXJjZXMvdGh1/bWJuYWlscy8wNTUv/MTU4Lzg5OC9zbWFs/bC9iZWF1dGlmdWwt/eW91bmctZWFzdC1h/c2lhbi13b21hbi1w/b3NpbmctbmVhci1i/bG9vbWluZy1mbG93/ZXJzLWluLXNwcmlu/Z3RpbWUtY29uY2Vw/dC1vZi15b3V0aC1h/bmQtYmVhdXR5LXBo/b3RvLmpwZWc"
                            alt="Profile" class="w-100 h-100 object-fit-cover">
                    </div>
                    <div>
                        <p class="mb-1 text-gray-700"><strong>Name:</strong> <span id="modal-user-name">John Doe</span></p>
                        <p class="mb-1 text-gray-700"><strong>Email:</strong> <span id="modal-user-email">john@example.com</span></p>
                    </div>
                    <div>
                        <p class="mb-1 text-gray-700"><strong>Shipping Address:</strong> <span id="modal-shipping-address"></span></p>
                        <p class="mb-1 text-gray-700"><strong>Payment Status:</strong> <span id="modal-payment-status"></span></p>
                    </div>
                    <div>
                        <p class="mb-1 text-gray-700"><strong>Shipping Status:</strong> <span id="modal-shipping-status"></span></p>
                        <p class="mb-1 text-gray-700"><strong>Date:</strong> <span id="modal-date"></span></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Payments Table -->
                    <div class="col-md-6 mb-3">
                        <h4 class="rounded font-normal text-gray-700 mb-2">Payment Info:</h4>
                        <table class="table">
                            <thead class="base-bg ">
                                <tr class="">
                                    <th>#</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody class="table-body-bg" id="modal-payments-tbody">
                                {{-- <tr>
                                    <td>1</td>
                                    <td>CASH</td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                    <td>10 Jan 2026 10:00</td>
                                    <td>$150.00</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>ACLEDA</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>11 Jan 2026 12:00</td>
                                    <td>$50.00</td>
                                </tr> --}}
                            </tbody>
                        </table>

                    </div>

                    <div class="col-md-6 mb-3">
                        <h4 class="rounded font-normal text-gray-700 mb-2">Transaction Summary:</h4>
                        <div class="bg-light div-row-bg">
                            <div class="d-flex justify-content-between">
                                <p class="mb-0"><strong>Total Amount:</strong></p>
                                <p class="mb-0"><strong id="modal-total-amount">$0.00</strong></p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="mb-0"><strong>Total Items:</strong></p>
                                <p class="mb-0"><strong id="modal-total-items">0</strong></p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="mb-0"><strong>Discount:</strong></p>
                                <p class="mb-0"><strong id="modal-discount">$0.00</strong></p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="mb-0"><strong>Shipping:</strong></p>
                                <p class="mb-0"><strong id="modal-shipping">$0.00</strong></p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="mb-0"><strong>Total Paid:</strong></p>
                                <p class="mb-0"><strong id="modal-total-paid">0.00</strong></p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Order Items Table -->
                <div>
                    <h6 class="bg-light p-2 rounded mb-2">Order Items</h6>
                    <table class="table table-hover">
                        <thead class="base-bg">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Variant</th>
                                <th>Price</th>
                                <th>Quntity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="modal-items-tbody">
                            {{-- <tr class="bg-light">
                                <td>1</td>
                                <td>Product A</td>
                                <td>SKU-001</td>
                                <td>$50.00</td>
                                <td>1</td>
                                <td>$50.00</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Product B</td>
                                <td>SKU-002</td>
                                <td>$75.00</td>
                                <td>2</td>
                                <td>$150.00</td>
                            </tr> --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-primary rounded-pill px-4" id="btnPrintModal">
                    <i class="bi bi-printer me-2"></i>Print
                </button>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>