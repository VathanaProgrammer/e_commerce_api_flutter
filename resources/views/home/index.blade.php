@extends('layouts.app')

@section('styles')
<style>
    /* Stat Cards Animation */
    .stat-card {
        border: none;
        border-radius: 20px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        background-color: #ffffff; /* Fallback */
        position: relative;
        z-index: 1;
        animation: cardSlideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) both;
    }
    
    .row > div:nth-child(1) .stat-card { animation-delay: 0.1s; }
    .row > div:nth-child(2) .stat-card { animation-delay: 0.2s; }
    .row > div:nth-child(3) .stat-card { animation-delay: 0.3s; }
    .row > div:nth-child(4) .stat-card { animation-delay: 0.4s; }

    @keyframes cardSlideUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stat-card .card-body {
        color: #ffffff !important;
    }

    .stat-card h3, .stat-card h6, .stat-card i {
        color: #ffffff !important;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: left 0.5s ease;
    }

    .stat-card:hover::before {
        left: 100%;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    
    /* Cards Section Animation */
    .recent-orders-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        animation: fadeInUp 0.6s ease 0.5s both;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .table thead th {
        border-top: none;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: #6c757d;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05) !important;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(1.1); }
    }

    /* Quick Actions Animation */
    .quick-action-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .quick-action-btn:hover {
        transform: translateX(5px);
    }

    .quick-action-btn i {
        transition: transform 0.3s ease;
    }

    .quick-action-btn:hover i {
        transform: scale(1.2);
    }

    /* Header Animation */
    .dashboard-header {
        animation: slideDown 0.5s ease both;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Animated Counter */
    .counter-value {
        display: inline-block;
    }

    /* Chart Container Animation */
    .chart-container {
        animation: fadeInUp 0.6s ease 0.4s both;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 dashboard-header">
        <div>
            <h4 class="fw-bold mb-1" style="font-size: 1.75rem;">Dashboard Overview</h4>
            <p class="text-muted mb-0">Welcome back, <span class="fw-semibold text-primary">{{ auth()->user()->first_name }}</span>! Here's what's happening today.</p>
        </div>
        <div>
            <button class="btn btn-white shadow-sm btn-sm fw-bold border rounded-pill px-3" style="transition: all 0.3s ease;">
                <i class="bi bi-calendar3 me-2 text-primary"></i> {{ now()->format('M d, Y') }}
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-4">
        <!-- Revenue -->
        <div class="col-md-3">
            <div class="card stat-card bg-gradient-primary text-white shadow h-100 position-relative">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center mb-3 text-white">
                        <div class="stat-icon text-white"><i class="bi bi-wallet2"></i></div>
                        <span class="badge bg-white bg-opacity-25 rounded-pill px-3">+12.5%</span>
                    </div>
                    <h6 class="text-white text-opacity-75 small mb-1 text-uppercase" style="letter-spacing: 0.5px; color: rgba(255,255,255,0.85) !important;">Total Revenue</h6>
                    <h3 class="fw-bold mb-0 counter-value text-white" style="color: #ffffff !important;">${{ number_format($data['total_sales'], 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Orders -->
        <div class="col-md-3">
            <div class="card stat-card bg-gradient-success text-white shadow h-100 position-relative">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center mb-3 text-white">
                        <div class="stat-icon text-white"><i class="bi bi-bag-check"></i></div>
                        <span class="badge bg-white bg-opacity-25 rounded-pill px-3">+8.2%</span>
                    </div>
                    <h6 class="text-white text-opacity-75 small mb-1 text-uppercase" style="letter-spacing: 0.5px; color: rgba(255,255,255,0.85) !important;">Total Orders</h6>
                    <h3 class="fw-bold mb-0 counter-value text-white" style="color: #ffffff !important;">{{ number_format($data['total_orders']) }}</h3>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col-md-3">
            <div class="card stat-card bg-gradient-warning text-white shadow h-100 position-relative">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center mb-3 text-white">
                        <div class="stat-icon text-white"><i class="bi bi-box-seam"></i></div>
                        <span class="badge bg-white bg-opacity-25 rounded-pill px-3">Stock</span>
                    </div>
                    <h6 class="text-white text-opacity-75 small mb-1 text-uppercase" style="letter-spacing: 0.5px; color: rgba(255,255,255,0.85) !important;">Active Products</h6>
                    <h3 class="fw-bold mb-0 counter-value text-white" style="color: #ffffff !important;">{{ number_format($data['total_products']) }}</h3>
                </div>
            </div>
        </div>

        <!-- Customers -->
        <div class="col-md-3">
            <div class="card stat-card bg-gradient-info text-white shadow h-100 position-relative">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center mb-3 text-white">
                        <div class="stat-icon text-white"><i class="bi bi-people"></i></div>
                        <span class="badge bg-white bg-opacity-25 rounded-pill px-3">Live</span>
                    </div>
                    <h6 class="text-white text-opacity-75 small mb-1 text-uppercase" style="letter-spacing: 0.5px; color: rgba(255,255,255,0.85) !important;">Total Customers</h6>
                    <h3 class="fw-bold mb-0 counter-value text-white" style="color: #ffffff !important;">{{ number_format($data['total_users']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sales Chart -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Sales Analytics</h5>
                        <select class="form-select form-select-sm w-auto border-0 bg-light">
                            <option>Last 7 Days</option>
                            <option>Last 30 Days</option>
                        </select>
                    </div>
                    <div style="height: 300px; position: relative;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Top Products -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4" style="animation: fadeInUp 0.6s ease 0.5s forwards; opacity: 0;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-lightning-charge text-warning me-2"></i>Quick Actions
                    </h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm py-2 rounded-3 text-start quick-action-btn d-flex align-items-center">
                            <i class="bi bi-plus-circle me-2"></i> 
                            <span>Add New Product</span>
                            <i class="bi bi-arrow-right ms-auto"></i>
                        </a>
                        <a href="{{ route('sales.orders') }}" class="btn btn-outline-secondary btn-sm py-2 rounded-3 text-start quick-action-btn d-flex align-items-center">
                            <i class="bi bi-list-ul me-2"></i> 
                            <span>View All Orders</span>
                            <i class="bi bi-arrow-right ms-auto"></i>
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm py-2 rounded-3 text-start quick-action-btn d-flex align-items-center">
                            <i class="bi bi-person-plus me-2"></i> 
                            <span>Manage Users</span>
                            <i class="bi bi-arrow-right ms-auto"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4" style="animation: fadeInUp 0.6s ease 0.6s forwards; opacity: 0;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-activity text-success me-2"></i>System Status
                    </h5>
                    <div class="d-flex align-items-center mb-3 p-2 rounded-2" style="background: rgba(40, 167, 69, 0.1);">
                        <div class="status-dot bg-success"></div>
                        <span class="small fw-medium">API: Online</span>
                        <span class="ms-auto badge bg-success rounded-pill">Active</span>
                    </div>
                    <div class="d-flex align-items-center mb-3 p-2 rounded-2" style="background: rgba(40, 167, 69, 0.1);">
                        <div class="status-dot bg-success"></div>
                        <span class="small fw-medium">Payments: Active</span>
                        <span class="ms-auto badge bg-success rounded-pill">Running</span>
                    </div>
                    <div class="d-flex align-items-center p-2 rounded-2" style="background: rgba(40, 167, 69, 0.1);">
                        <div class="status-dot bg-success"></div>
                        <span class="small fw-medium">Database: Optimized</span>
                        <span class="ms-auto badge bg-success rounded-pill">Healthy</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="col-12 mt-4">
            <div class="card recent-orders-card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Recent Orders</h5>
                        <a href="{{ route('sales.orders') }}" class="btn btn-link btn-sm text-decoration-none p-0">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th class="ps-4">ORDER ID</th>
                                    <th>CUSTOMER</th>
                                    <th>ITEMS</th>
                                    <th>TOTAL</th>
                                    <th>STATUS</th>
                                    <th class="text-end pe-4">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['recent_orders'] as $order)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-medium text-primary">#INV-{{ $order->invoice_no ?? $order->id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                                {{ strtoupper(substr($order->user->first_name ?? 'G', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="small fw-bold">{{ $order->user->first_name ?? 'Guest' }}</div>
                                                <div class="text-muted" style="font-size: 11px;">{{ $order->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $order->total_items }} Items</td>
                                    <td class="fw-bold text-success">${{ number_format($order->total_sell_price, 2) }}</td>
                                    <td>{!! $order->status->badge() !!}</td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-light btn-sm rounded-pill view-order-details" data-id="{{ $order->id }}">
                                            <i class="fas fa-eye text-primary"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No recent orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        
        // --- 📊 SALES ANALYTICS CHART ---
        const initSalesChart = () => {
            const $canvas = $('#salesChart');
            if (!$canvas.length) return;

            const ctx = $canvas[0].getContext('2d');
            
            const salesData = {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Sales ($)',
                    data: [1200, 1900, 1500, 2500, 2200, 3000, 2800],
                    fill: true,
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderColor: '#667eea',
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#667eea',
                    pointBorderWidth: 2
                }]
            };

            const chartConfig = {
                type: 'line',
                data: salesData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            padding: 10,
                            backgroundColor: '#1e293b',
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { 
                                callback: val => '$' + val,
                                font: { size: 11 },
                                color: '#94a3b8'
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { 
                                font: { size: 11 },
                                color: '#94a3b8'
                            }
                        }
                    }
                }
            };

            new Chart(ctx, chartConfig);
        };

        // --- 🖱️ EVENT HANDLERS ---
        const bindEvents = () => {
            // Handle viewing order details
            $(document).on('click', '.view-order-details', function() {
                const orderId = $(this).data('id');
                window.location.href = "{{ route('sales.orders') }}?order_id=" + orderId;
            });
        };

        // Initialize Everything
        initSalesChart();
        bindEvents();

    });
</script>
@endsection

