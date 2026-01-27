@extends('layouts.app')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    body {
        font-family: 'Outfit', sans-serif;
        background-color: #f4f7fe;
    }

    /* Dashboard Overhaul - High-End Aesthetic */
    .dashboard-container {
        padding-top: 2rem;
        background-color: #f8fafc;
        min-height: 100vh;
    }

    /* Vibrant Stat Cards */
    .stat-card-vibrant {
        border-radius: 24px;
        padding: 1.75rem;
        color: white;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1);
    }

    .stat-card-vibrant:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
    }

    .gradient-revenue { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); }
    .gradient-orders { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .gradient-products { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .gradient-users { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }

    .stat-icon-white {
        width: 44px;
        height: 44px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 1.5rem;
    }

    .stat-v-label {
        font-weight: 500;
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }

    .stat-v-value {
        font-weight: 800;
        font-size: 1.85rem;
        margin-bottom: 0;
        letter-spacing: -0.025em;
    }

    /* Content Cards */
    .content-card-modern {
        background: white;
        border-radius: 24px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        height: 100%;
        transition: box-shadow 0.3s ease;
    }

    .content-card-modern:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .card-title-modern {
        font-weight: 700;
        color: #1e293b;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Table Refinement */
    .table-custom-clean th {
        background: transparent;
        border-top: none;
        border-bottom: 1px solid #f1f5f9;
        color: #64748b;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        padding: 1rem;
    }

    .table-custom-clean td {
        padding: 1.25rem 1rem;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
        color: #334155;
        font-size: 0.9rem;
    }

    .table-custom-clean tr:hover {
        background-color: #f8fafc;
    }

    /* Buttons */
    .btn-gradient-primary {
        background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 30px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-gradient-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(79, 70, 229, 0.4);
        color: white;
    }

    .badge-status {
        padding: 6px 12px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .product-circle-img {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>

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
<div class="container-fluid py-4 px-lg-5">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 fade-in-up">
        <div>
            <h1 class="fw-800 mb-1" style="font-size: 2.25rem; color: #0f172a; letter-spacing: -0.05em;">Dashboard</h1>
            <p class="text-muted fw-500 mb-0">Overview of your business performance</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-3">
            <button id="btnExportReport" class="btn btn-white border-0 shadow-sm rounded-pill px-4 fw-700 transition-all hover-lift-sm" style="color: #475569;">
                <i class="bi bi-file-earmark-arrow-down me-2"></i> Export
            </button>
            <a href="{{ route('products.create') }}" class="btn btn-gradient-primary shadow hover-lift-sm">
                <i class="bi bi-plus-lg me-2"></i> New Product
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6 fade-in-up delay-1">
            <div class="stat-card-vibrant gradient-revenue">
                <div>
                    <div class="stat-icon-white">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="stat-v-label">Total Revenue</div>
                    <h3 class="stat-v-value">${{ number_format($data['total_sales'] ?? 0, 2) }}</h3>
                </div>
                <div class="mt-4 pt-2 border-top border-white border-opacity-10 d-flex align-items-center justify-content-between">
                    <span class="small opacity-75">Growth WoW</span>
                    <span class="fw-700 bg-white bg-opacity-20 px-2 py-1 rounded-pill small">
                        {{ ($data['growth'] ?? 0) >= 0 ? '+' : '' }}{{ $data['growth'] ?? 0 }}%
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 fade-in-up delay-2">
            <div class="stat-card-vibrant gradient-orders">
                <div>
                    <div class="stat-icon-white">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <div class="stat-v-label">Success Orders</div>
                    <h3 class="stat-v-value">{{ number_format($data['total_orders'] ?? 0) }}</h3>
                </div>
                <div class="mt-4 pt-2 border-top border-white border-opacity-10">
                    <span class="small opacity-75">Lifetime Transactions</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 fade-in-up delay-3">
            <div class="stat-card-vibrant gradient-products">
                <div>
                    <div class="stat-icon-white">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="stat-v-label">Total Catalog</div>
                    <h3 class="stat-v-value">{{ number_format($data['total_products'] ?? 0) }}</h3>
                </div>
                <div class="mt-4 pt-2 border-top border-white border-opacity-10">
                    <span class="small opacity-75">Active Products</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 fade-in-up delay-4">
            <div class="stat-card-vibrant gradient-users">
                <div>
                    <div class="stat-icon-white">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-v-label">New Customers</div>
                    <h3 class="stat-v-value">{{ number_format($data['total_users'] ?? 0) }}</h3>
                </div>
                <div class="mt-4 pt-2 border-top border-white border-opacity-10">
                    <span class="small opacity-75">Verified Users</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics & Top Products -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8 fade-in-up delay-1">
            <div class="content-card-modern">
                <div class="card-title-modern">
                    <span>Revenue Trend</span>
                    <span class="badge bg-light text-primary rounded-pill px-3 py-2 border shadow-sm small fw-700">Past 7 Days</span>
                </div>
                <div class="chart-container">
                    <canvas id="salesTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 fade-in-up delay-2">
            <div class="content-card-modern">
                <div class="card-title-modern">Top Selling</div>
                <div class="list-group list-group-flush border-0">
                    @forelse($data['top_products'] ?? [] as $product)
                    <div class="list-group-item bg-transparent border-0 px-0 mb-3 d-flex align-items-center transition-all hover-lift-sm">
                        <img src="{{ $product->variant->product->preview_image_url ?? 'https://placehold.co/100x100?text=Product' }}" class="product-circle-img me-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-700 text-dark text-truncate" style="max-width: 150px;">{{ $product->variant->product->name ?? 'Unknown' }}</h6>
                            <span class="text-muted small fw-600">{{ $product->total_qty }} units</span>
                        </div>
                        <div class="text-end">
                            <h6 class="mb-0 fw-800 text-dark">${{ number_format($product->total_revenue, 2) }}</h6>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="bi bi-graph-up opacity-10 fs-1 d-block mb-3"></i>
                        <span class="text-muted small fw-600">Collecting sales data...</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Table -->
    <div class="fade-in-up delay-3">
        <div class="content-card-modern">
            <div class="card-title-modern">
                <span>Recent Activity Log</span>
                <a href="{{ route('sales.orders') }}" class="btn btn-sm btn-light border-0 px-3 fw-700 text-primary rounded-pill">
                    Journal <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom-clean mb-0">
                    <thead>
                        <tr>
                            <th class="ps-0">Reference</th>
                            <th>Customer & Account</th>
                            <th>Status Badge</th>
                            <th>Transaction Vol</th>
                            <th class="text-end pe-0">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['recent_orders'] ?? [] as $order)
                        <tr>
                            <td class="ps-0">
                                <span class="fw-800 text-dark">#{{ $order->invoice_no ?? $order->id }}</span>
                                <div class="text-muted small mt-1">{{ $order->created_at->format('M d, H:i') }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3 border shadow-sm" style="width: 36px; height: 36px; font-size: 0.85rem; font-weight: 800; color: #6366f1;">
                                        {{ strtoupper(substr($order->user->first_name ?? 'C', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-700 text-dark">{{ ($order->user->first_name ?? 'Walk-in') . ' ' . ($order->user->last_name ?? '') }}</div>
                                        <div class="text-muted small" style="font-size: 0.75rem;">Verified Member</div>
                                    </div>
                                </div>
                            </td>
                            <td>{!! $order->status->badge() !!}</td>
                            <td>
                                <span class="fw-800 text-dark">${{ number_format($order->total_sell_price, 2) }}</span>
                            </td>
                            <td class="text-end pe-0">
                                <a href="{{ route('sales.show', $order->id) }}" class="btn btn-light btn-sm rounded-pill px-3 fw-700 transition-all">
                                    Explore <i class="bi bi-chevron-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted py-4">
                                    <i class="bi bi-inboxes d-block fs-1 opacity-20 mb-3"></i>
                                    <span class="fw-600">No activity recorded today.</span>
                                </div>
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

