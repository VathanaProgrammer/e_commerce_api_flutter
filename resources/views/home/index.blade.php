@extends('layouts.app')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    body {
        font-family: 'Outfit', sans-serif;
        background-color: #f4f7fe;
    }

    .dashboard-wrapper {
        padding: 2.5rem 1.5rem;
    }

    /* Clean Card Style */
    .modern-card {
        background: #ffffff;
        border: none;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative;
        overflow: hidden;
    }

    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    }

    /* Stat Cards */
    .stat-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 20px;
    }

    .icon-primary { background: #eef2ff; color: #4f46e5; }
    .icon-success { background: #f0fdf4; color: #22c55e; }
    .icon-warning { background: #fffbeb; color: #f59e0b; }
    .icon-info { background: #f0f9ff; color: #0ea5e9; }

    .stat-label {
        font-size: 0.95rem;
        font-weight: 500;
        color: #64748b;
        margin-bottom: 5px;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        letter-spacing: -0.02em;
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .fade-in-up {
        animation: fadeIn 0.8s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
        opacity: 0;
    }

    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }

    /* Tables & Lists */
    .table-modern thead th {
        background: transparent;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #94a3b8;
        padding: 1.25rem 1rem;
    }

    .table-modern td {
        padding: 1.25rem 1rem;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }

    .product-img-sq {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        object-fit: cover;
        background: #f1f5f9;
    }

    /* Action Buttons */
    .btn-action {
        border-radius: 12px;
        padding: 10px 16px;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
    }

    .btn-action:active {
        transform: scale(0.95);
    }

    /* Charts */
    .chart-container {
        height: 280px;
        position: relative;
    }

    .badge-soft {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .badge-soft-success { background: #f0fdf4; color: #16a34a; }
</style>
@endsection

@section('content')
<div class="dashboard-wrapper">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 fade-in-up">
        <div>
            <h1 class="fw-700 mb-1" style="font-size: 1.85rem; color: #1e293b;">Good Morning, {{ auth()->user()->first_name }}</h1>
            <p class="text-muted mb-0">Here's a summary of your shop's performance today.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <button class="btn btn-white border shadow-sm rounded-pill px-4 fw-600">
                <i class="bi bi-download me-2 text-primary"></i> Export Report
            </button>
            <a href="{{ route('products.create') }}" class="btn btn-primary rounded-pill px-4 fw-600 shadow-sm border-0">
                <i class="bi bi-plus-lg me-2"></i> Add Product
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6 fade-in-up delay-1">
            <div class="modern-card p-4">
                <div class="stat-card-icon icon-primary">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <h6 class="stat-label">Total Revenue</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="stat-value mb-0">${{ number_format($data['total_sales'] ?? 0, 2) }}</h3>
                    <span class="badge-soft badge-soft-success">+12.5%</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 fade-in-up delay-2">
            <div class="modern-card p-4">
                <div class="stat-card-icon icon-success">
                    <i class="bi bi-bag-check"></i>
                </div>
                <h6 class="stat-label">Total Orders</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="stat-value mb-0">{{ number_format($data['total_orders'] ?? 0) }}</h3>
                    <span class="badge-soft badge-soft-success">+8.2%</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 fade-in-up delay-3">
            <div class="modern-card p-4">
                <div class="stat-card-icon icon-warning">
                    <i class="bi bi-box-seam"></i>
                </div>
                <h6 class="stat-label">Stock Inventory</h6>
                <h3 class="stat-value mb-0">{{ number_format($data['total_products'] ?? 0) }} Items</h3>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 fade-in-up delay-4">
            <div class="modern-card p-4">
                <div class="stat-card-icon icon-info">
                    <i class="bi bi-people"></i>
                </div>
                <h6 class="stat-label">Active Users</h6>
                <h3 class="stat-value mb-0">{{ number_format($data['total_users'] ?? 0) }}</h3>
            </div>
        </div>
    </div>

    <!-- Analytics & Top Products -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8 fade-in-up delay-1">
            <div class="modern-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-700 mb-0">Sales Performance</h5>
                    <select class="form-select w-auto border-0 bg-light rounded-pill px-3 shadow-sm" style="font-size: 0.85rem;">
                        <option>Weekly View</option>
                        <option>Monthly View</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="salesTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 fade-in-up delay-2">
            <div class="modern-card p-4 h-100">
                <h5 class="fw-700 mb-4">Top Performing Products</h5>
                <div class="list-group list-group-flush">
                    @forelse($data['top_products'] ?? [] as $product)
                    <div class="list-group-item bg-transparent border-0 px-0 mb-3 d-flex align-items-center">
                        <img src="{{ $product->variant->product->preview_image_url ?? 'https://placehold.co/100x100?text=Product' }}" class="product-img-sq me-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-600 text-truncate" style="max-width: 150px;">{{ $product->variant->product->name ?? 'Unknown Product' }}</h6>
                            <span class="text-muted small">{{ $product->total_qty }} Sales</span>
                        </div>
                        <div class="text-end">
                            <h6 class="mb-0 fw-700 text-primary">${{ number_format($product->total_revenue, 2) }}</h6>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">No product data available</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="fade-in-up delay-3">
        <div class="modern-card">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-700 mb-0">Recent Transactions</h5>
                <a href="{{ route('sales.orders') }}" class="btn btn-link text-primary fw-600 p-0 text-decoration-none">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Customer Name</th>
                            <th>Purchase Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['recent_orders'] as $order)
                        <tr>
                            <td><span class="fw-600 text-dark">#{{ $order->invoice_no ?? $order->id }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px; font-weight: 700; color: #4f46e5; font-size: 0.85rem;">
                                        {{ strtoupper(substr($order->user->first_name ?? 'C', 0, 1)) }}
                                    </div>
                                    <span class="fw-500 text-dark">{{ $order->user->first_name ?? 'Customer' }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $order->created_at->format('M d, Y') }}</td>
                            <td><span class="fw-700 text-dark">${{ number_format($order->total_sell_price, 2) }}</span></td>
                            <td>{!! $order->status->badge() !!}</td>
                            <td class="text-end">
                                <button class="btn btn-light btn-sm rounded-pill px-3 view-order-details" data-id="{{ $order->id }}">
                                    Details
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No recent orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // --- 📊 SALES PERFORMANCE CHART ---
        const initChart = () => {
            const ctx = document.getElementById('salesTrendsChart').getContext('2d');
            
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.1)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($data['revenue_chart']['labels']),
                    datasets: [{
                        label: 'Gross Sales',
                        data: @json($data['revenue_chart']['data']),
                        fill: true,
                        backgroundColor: gradient,
                        borderColor: '#4f46e5',
                        borderWidth: 3,
                        tension: 0.45,
                        pointRadius: 4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4f46e5',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            titleFont: { size: 13, weight: 'bold' },
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: false,
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            grid: { borderDash: [5, 5], color: '#f1f5f9' },
                            ticks: { 
                                color: '#94a3b8',
                                font: { size: 11, weight: '500' },
                                callback: val => '$' + val
                            } 
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { 
                                color: '#94a3b8', 
                                font: { size: 11, weight: '500' } 
                            } 
                        }
                    }
                }
            });
        };

        initChart();

        // Details Button Animation
        $(document).on('click', '.view-order-details', function() {
            const orderId = $(this).data('id');
            window.location.href = "{{ route('sales.orders') }}?order_id=" + orderId;
        });
    });
</script>
@endsection

