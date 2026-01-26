@extends('layouts.app')

@section('styles')
<style>
    /* Stat Cards Base */
    .stat-card {
        border: none !important;
        border-radius: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        position: relative;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        background-color: #fff;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }

    .stat-card .card-body {
        position: relative;
        z-index: 5;
    }

    /* Force text visibility */
    .stat-card h3, 
    .stat-card h6, 
    .stat-card i, 
    .stat-card span,
    .stat-card .counter-value {
        color: #ffffff !important;
        opacity: 1 !important;
        visibility: visible !important;
        display: inline-block;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
    }

    /* Gradients */
    .bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; }
    .bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important; }
    .bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important; }
    .bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important; }
    
    /* Layout robustness */
    .recent-orders-card, .dashboard-header, .chart-container {
        opacity: 1 !important;
        visibility: visible !important;
        transform: none !important;
        animation: none !important;
    }

    .table thead th {
        background: #f8fafc;
        border-bottom: 2px solid #edf2f7;
        color: #64748b;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
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
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-white bg-opacity-25 rounded-3 p-2">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-25 rounded-pill px-3">+12.5%</span>
                    </div>
                    <h6 class="text-white text-opacity-75 small mb-1 text-uppercase fw-bold">Total Revenue</h6>
                    <h3 class="fw-bold mb-0 text-white">${{ number_format($data['total_sales'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Orders -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-white bg-opacity-25 rounded-3 p-2">
                            <i class="bi bi-bag-check fs-4"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-25 rounded-pill px-3">+8.2%</span>
                    </div>
                    <h6 class="text-white text-opacity-75 small mb-1 text-uppercase fw-bold">Total Orders</h6>
                    <h3 class="fw-bold mb-0 text-white">{{ number_format($data['total_orders'] ?? 0) }}</h3>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-dark bg-opacity-10 rounded-3 p-2">
                            <i class="bi bi-box-seam fs-4"></i>
                        </div>
                        <span class="badge bg-dark bg-opacity-10 rounded-pill px-3 text-dark">Stock</span>
                    </div>
                    <h6 class="text-dark text-opacity-75 small mb-1 text-uppercase fw-bold">Active Products</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($data['total_products'] ?? 0) }}</h3>
                </div>
            </div>
        </div>

        <!-- Customers -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-info text-dark h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bg-dark bg-opacity-10 rounded-3 p-2">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                        <span class="badge bg-dark bg-opacity-10 rounded-pill px-3 text-dark">Live</span>
                    </div>
                    <h6 class="text-dark text-opacity-75 small mb-1 text-uppercase fw-bold">Total Customers</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($data['total_users'] ?? 0) }}</h3>
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

