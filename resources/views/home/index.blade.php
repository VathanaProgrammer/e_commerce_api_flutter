@extends('layouts.app')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    body {
        font-family: 'Outfit', sans-serif;
        background-color: #f4f7fe;
    }

    /* Container adjustment - Fluid but with standard padding */
    .dashboard-container {
        padding-top: 1.5rem;
        padding-left: 2rem;
        padding-right: 2rem;
    }

    /* Premium card look */
    .premium-card {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.07);
    }

    .card-header-custom {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        background: #ffffff;
    }

    .card-body-custom {
        padding: 1.5rem;
        flex-grow: 1;
    }

    .bg-light-soft { background: #f8fafc; }

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
    .badge-soft-danger { background: #fef2f2; color: #dc2626; }

    .transition-all { transition: all 0.3s ease; }
    .hover-lift-sm:hover { transform: translateY(-3px); }
</style>
@endsection

@section('content')
<div class="container-fluid dashboard-container">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 fade-in-up">
        <div>
            <h1 class="fw-700 mb-1" style="font-size: 1.85rem; color: #1e293b;">Good morning, {{ auth()->user()->first_name }}</h1>
            <p class="text-muted mb-0">Operational performance & business intelligence dashboard.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <button id="btnExportReport" class="btn btn-white border shadow-sm rounded-pill px-4 fw-600">
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
            <div class="premium-card">
                <div class="card-body-custom">
                    <div class="stat-card-icon icon-primary">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <h6 class="stat-label">Total Revenue</h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <h3 class="stat-value mb-0">${{ number_format($data['total_sales'] ?? 0, 2) }}</h3>
                        <span class="badge-soft {{ ($data['growth'] ?? 0) >= 0 ? 'badge-soft-success' : 'badge-soft-danger' }}">
                            {{ ($data['growth'] ?? 0) >= 0 ? '+' : '' }}{{ $data['growth'] ?? 0 }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 fade-in-up delay-2">
            <div class="premium-card">
                <div class="card-body-custom">
                    <div class="stat-card-icon icon-success">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <h6 class="stat-label">Orders Success</h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <h3 class="stat-value mb-0">{{ number_format($data['total_orders'] ?? 0) }}</h3>
                        <span class="text-muted small">Lifetime Total</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 fade-in-up delay-3">
            <div class="premium-card">
                <div class="card-body-custom">
                    <div class="stat-card-icon icon-warning">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h6 class="stat-label">Active Catalog</h6>
                    <h3 class="stat-value mb-0">{{ number_format($data['total_products'] ?? 0) }}</h3>
                    <p class="text-muted small mb-0 mt-1">Managed products</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 fade-in-up delay-4">
            <div class="premium-card">
                <div class="card-body-custom">
                    <div class="stat-card-icon icon-info">
                        <i class="bi bi-people"></i>
                    </div>
                    <h6 class="stat-label">Verified Reach</h6>
                    <h3 class="stat-value mb-0">{{ number_format($data['total_users'] ?? 0) }}</h3>
                    <p class="text-muted small mb-0 mt-1">Registered customers</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics & Top Products -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8 fade-in-up delay-1">
            <div class="premium-card">
                <div class="card-header-custom">
                    <h5 class="fw-700 mb-0">Performance Trend</h5>
                    <div class="badge bg-light text-dark rounded-pill px-3 py-2 border shadow-sm small fw-600">
                        Past 7 Days
                    </div>
                </div>
                <div class="card-body-custom">
                    <div class="chart-container">
                        <canvas id="salesTrendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 fade-in-up delay-2">
            <div class="premium-card">
                <div class="card-header-custom">
                    <h5 class="fw-700 mb-0">Top Performing</h5>
                </div>
                <div class="card-body-custom">
                    <div class="list-group list-group-flush">
                        @forelse($data['top_products'] ?? [] as $product)
                        <div class="list-group-item bg-transparent border-0 px-0 mb-3 d-flex align-items-center transition-all hover-lift-sm">
                            <img src="{{ $product->variant->product->preview_image_url ?? 'https://placehold.co/100x100?text=Product' }}" class="product-img-sq me-3 shadow-sm border">
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-600 text-truncate" style="max-width: 150px;">{{ $product->variant->product->name ?? 'Unknown' }}</h6>
                                <span class="text-muted small fw-500">{{ $product->total_qty }} units sold</span>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0 fw-700 text-dark">${{ number_format($product->total_revenue, 2) }}</h6>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="bi bi-graph-up opacity-25 fs-1 d-block mb-3"></i>
                            <span class="text-muted small">No analytics yet.</span>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="fade-in-up delay-3">
        <div class="premium-card">
            <div class="card-header-custom bg-light-soft d-flex justify-content-between align-items-center">
                <h5 class="fw-700 mb-0">Recent Activity Log</h5>
                <a href="{{ route('sales.orders') }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-600 text-primary">
                    View All Activity <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Transaction ID</th>
                            <th>Customer Representative</th>
                            <th>Current Status</th>
                            <th>Total Volume</th>
                            <th class="text-end pe-4">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['recent_orders'] ?? [] as $order)
                        <tr class="transition-all">
                            <td class="ps-4 fw-700 text-primary">#{{ $order->invoice_no ?? $order->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3 border shadow-sm" style="width: 32px; height: 32px; font-size: 0.8rem; font-weight: 700; color: #4f46e5;">
                                        {{ strtoupper(substr($order->user->first_name ?? 'C', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-600 text-dark">{{ ($order->user->first_name ?? 'Walk-in') . ' ' . ($order->user->last_name ?? '') }}</div>
                                        <div class="text-muted small" style="font-size: 0.7rem;">Verified Account</div>
                                    </div>
                                </div>
                            </td>
                            <td>{!! $order->status->badge() !!}</td>
                            <td class="fw-700">${{ number_format($order->total_sell_price, 2) }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('sales.show', $order->id) }}" class="btn btn-action btn-light-muted transition-all">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inboxes d-block fs-1 opacity-25 mb-2"></i>
                                No recent activity found.
                            </td>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Export button handler
        const btnExport = document.getElementById('btnExportReport');
        if (btnExport) {
            btnExport.addEventListener('click', function() {
                if (window.toastr) {
                    toastr.info('Preparing your comprehensive business report...', 'Export Started');
                    setTimeout(() => {
                        window.print();
                    }, 1000);
                } else {
                    window.print();
                }
            });
        }

        const ctx = document.getElementById('salesTrendsChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.15)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($data['revenue_chart']['labels']),
                datasets: [{
                    label: 'Daily Revenue ($)',
                    data: @json($data['revenue_chart']['data']),
                    borderColor: '#4f46e5',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        callbacks: {
                            label: (ctx) => ` $${ctx.parsed.y.toLocaleString()}`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: {
                            font: { family: 'Outfit', size: 11 },
                            color: '#64748b',
                            callback: (val) => '$' + val
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Outfit', size: 11 }, color: '#64748b' }
                    }
                }
            }
        });
    });
</script>
@endsection

