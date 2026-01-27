@extends('layouts.app')

@section('styles')
<style>
    :root {
        --depth-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
        --hover-depth-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.15);
        --card-bg: rgba(255, 255, 255, 0.95);
    }

    /* Dashboard Container Perspective */
    .dashboard-container {
        perspective: 1000px;
    }

    /* 3D Clean Stat Cards */
    .stat-card-3d {
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        border-radius: 24px;
        transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        background: var(--card-bg);
        backdrop-filter: blur(10px);
        box-shadow: var(--depth-shadow) !important;
        overflow: hidden;
        position: relative;
        transform-style: preserve-3d;
        height: 100%;
    }
    
    .stat-card-3d:hover {
        transform: translateY(-8px) rotateX(2deg) rotateY(-1deg);
        box-shadow: var(--hover-depth-shadow) !important;
        border-color: rgba(255, 255, 255, 0.6) !important;
    }

    .stat-card-3d::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
        z-index: 10;
    }

    .stat-icon-3d {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 20px;
        transition: all 0.4s ease;
        transform: translateZ(20px);
    }

    /* Gradient Backgrounds for Icons with subtle 3D */
    .icon-box-primary { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: white; }
    .icon-box-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
    .icon-box-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }
    .icon-box-info { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; }

    /* Counter Styling */
    .counter-value {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #1e293b;
        margin-bottom: 4px;
        display: block;
        transform: translateZ(30px);
    }

    .stat-label {
        color: #64748b;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        transform: translateZ(10px);
    }

    /* Glass Panels for Charts/Tables */
    .glass-panel {
        background: var(--card-bg);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 28px;
        box-shadow: var(--depth-shadow);
        transition: transform 0.4s ease, box-shadow 0.4s ease;
    }

    .glass-panel:hover {
        box-shadow: var(--hover-depth-shadow);
    }

    /* Recent Orders Enhancement */
    .table-clean {
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .table-clean tbody tr {
        background: white;
        transition: all 0.3s ease;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .table-clean tbody tr td {
        border: none;
        padding: 1.25rem 1rem;
    }

    .table-clean tbody tr td:first-child { border-radius: 12px 0 0 12px; }
    .table-clean tbody tr td:last-child { border-radius: 0 12px 12px 0; }

    .table-clean tbody tr:hover {
        transform: scale(1.01) translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        z-index: 10;
        position: relative;
    }

    /* Animation Keyframes */
    @keyframes fadeInUp3D {
        from {
            opacity: 0;
            transform: translateY(30px) translateZ(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0) translateZ(0);
        }
    }

    .animate-3d {
        animation: fadeInUp3D 0.8s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        opacity: 0;
    }

    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }

    /* Quick Action Buttons */
    .btn-3d-action {
        border-radius: 16px;
        padding: 12px 20px;
        border: none;
        background: #f8fafc;
        color: #1e293b;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .btn-3d-action:hover {
        background: white;
        transform: translateX(5px) scale(1.02);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        color: #6366f1;
    }

    .btn-3d-action i {
        font-size: 1.25rem;
        padding: 8px;
        border-radius: 12px;
        background: rgba(99, 102, 241, 0.1);
    }

    /* Status Pill */
    .status-badge-3d {
        padding: 6px 12px;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
</style>
@endsection

@section('content')
<div class="container py-5 dashboard-container">
    <!-- Header Section -->
    <div class="row align-items-center mb-5 animate-3d">
        <div class="col-lg-8">
            <h1 class="fw-800 display-6 mb-2" style="color: #1e293b;">Hello, <span class="text-primary">{{ auth()->user()->first_name }}</span> 👋</h1>
            <p class="text-muted lead mb-0">Unified commerce analytics & operations dashboard.</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <div class="d-inline-flex p-2 bg-white rounded-pill shadow-sm border">
                <div class="px-3 py-1 border-end">
                    <span class="small text-muted d-block">Server Status</span>
                    <span class="badge bg-success-subtle text-success p-0" style="font-size: 0.7rem;"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Optimal</span>
                </div>
                <div class="px-3 py-1">
                    <span class="small text-muted d-block">Local Time</span>
                    <span class="fw-bold small">{{ now()->format('h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-5">
        <!-- Revenue -->
        <div class="col-md-3">
            <div class="card stat-card-3d animate-3d delay-1">
                <div class="card-body p-4">
                    <div class="stat-icon-3d icon-box-primary shadow-lg">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <span class="stat-label">Total Revenue</span>
                    <h2 class="counter-value">${{ number_format($data['total_sales'] ?? 0, 2) }}</h2>
                    <div class="d-flex align-items-center mt-3">
                        <span class="text-success fw-bold small"><i class="bi bi-graph-up-arrow me-1"></i>+12.5%</span>
                        <span class="text-muted small ms-2">vs last month</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders -->
        <div class="col-md-3">
            <div class="card stat-card-3d animate-3d delay-2">
                <div class="card-body p-4">
                    <div class="stat-icon-3d icon-box-success shadow-lg">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <span class="stat-label">Total Orders</span>
                    <h2 class="counter-value">{{ number_format($data['total_orders'] ?? 0) }}</h2>
                    <div class="d-flex align-items-center mt-3">
                        <span class="text-success fw-bold small"><i class="bi bi-graph-up-arrow me-1"></i>+8.2%</span>
                        <span class="text-muted small ms-2">processed</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col-md-3">
            <div class="card stat-card-3d animate-3d delay-3">
                <div class="card-body p-4">
                    <div class="stat-icon-3d icon-box-warning shadow-lg">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <span class="stat-label">Active Products</span>
                    <h2 class="counter-value">{{ number_format($data['total_products'] ?? 0) }}</h2>
                    <div class="d-flex align-items-center mt-3">
                        <span class="text-warning fw-bold small"><i class="bi bi-check-circle me-1"></i>Optimized</span>
                        <span class="text-muted small ms-2">in catalog</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customers -->
        <div class="col-md-3">
            <div class="card stat-card-3d animate-3d delay-4">
                <div class="card-body p-4">
                    <div class="stat-icon-3d icon-box-info shadow-lg">
                        <i class="bi bi-people"></i>
                    </div>
                    <span class="stat-label">Total Customers</span>
                    <h2 class="counter-value">{{ number_format($data['total_users'] ?? 0) }}</h2>
                    <div class="d-flex align-items-center mt-3">
                        <span class="text-info fw-bold small"><i class="bi bi-person-check me-1"></i>Verified</span>
                        <span class="text-muted small ms-2">active users</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sales Chart -->
        <div class="col-lg-8 animate-3d delay-1">
            <div class="card glass-panel border-0 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-800 mb-1">Performance Overview</h5>
                            <p class="text-muted small mb-0">Daily sales trajectory</p>
                        </div>
                        <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                            <button class="btn btn-white btn-sm px-3 active">7D</button>
                            <button class="btn btn-white btn-sm px-3">30D</button>
                        </div>
                    </div>
                    <div style="height: 350px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side Widgets -->
        <div class="col-lg-4 animate-3d delay-2">
            <!-- Quick Management -->
            <div class="card glass-panel border-0 mb-4 p-2">
                <div class="card-body">
                    <h6 class="fw-800 mb-4 text-uppercase letter-spacing-1 text-primary">Operations</h6>
                    
                    <a href="{{ route('products.create') }}" class="btn-3d-action text-decoration-none">
                        <i class="bi bi-plus-square"></i>
                        <span>New Product</span>
                        <i class="bi bi-chevron-right ms-auto small opacity-50"></i>
                    </a>
                    
                    <a href="{{ route('sales.orders') }}" class="btn-3d-action text-decoration-none">
                        <i class="bi bi-receipt"></i>
                        <span>Manage Orders</span>
                        <i class="bi bi-chevron-right ms-auto small opacity-50"></i>
                    </a>
                    
                    <a href="{{ route('users.index') }}" class="btn-3d-action text-decoration-none">
                        <i class="bi bi-shield-lock"></i>
                        <span>Access Control</span>
                        <i class="bi bi-chevron-right ms-auto small opacity-50"></i>
                    </a>
                </div>
            </div>

            <!-- Health Monitor -->
            <div class="card glass-panel border-0 p-2">
                <div class="card-body">
                    <h6 class="fw-800 mb-4 text-uppercase letter-spacing-1 text-success">System Health</h6>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="p-2 rounded-3 bg-success-subtle me-3">
                            <i class="bi bi-cpu text-success fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-600">API Load</span>
                                <span class="small text-muted">24%</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 10px;">
                                <div class="progress-bar bg-success" style="width: 24%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="p-2 rounded-3 bg-primary-subtle me-3">
                            <i class="bi bi-hdd-network text-primary fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-600">Storage</span>
                                <span class="small text-muted">1.2 GB / 5 GB</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 10px;">
                                <div class="progress-bar bg-primary" style="width: 35%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="col-12 mt-4 animate-3d delay-3">
            <div class="card glass-panel border-0">
                <div class="card-header bg-transparent py-4 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-800 mb-1">Latest Transactions</h5>
                            <p class="text-muted small mb-0">Real-time order synchronization</p>
                        </div>
                        <a href="{{ route('sales.orders') }}" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
                            Explorer All <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive px-4 pb-4">
                        <table class="table table-clean align-middle mb-0">
                            <thead>
                                <tr class="text-muted small text-uppercase fw-700">
                                    <th class="border-0">Order</th>
                                    <th class="border-0">Customer</th>
                                    <th class="border-0">Payload</th>
                                    <th class="border-0">Revenue</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['recent_orders'] as $order)
                                <tr>
                                    <td>
                                        <span class="fw-800 text-dark">#{{ $order->invoice_no ?? $order->id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 fw-800" style="width: 40px; height: 40px; font-size: 14px;">
                                                {{ strtoupper(substr($order->user->first_name ?? 'G', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-700 text-dark">{{ $order->user->first_name ?? 'Guest' }}</div>
                                                <div class="text-muted small">{{ $order->created_at->format('M d, H:i') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="badge bg-light text-dark border py-2 px-3 rounded-3 fw-600">
                                            <i class="bi bi-box me-1"></i> {{ $order->total_items }} SKU
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-800 text-primary">${{ number_format($order->total_sell_price, 2) }}</span>
                                    </td>
                                    <td>
                                        {!! $order->status->badge() !!}
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-outline-primary btn-sm rounded-pill px-3 view-order-details" data-id="{{ $order->id }}">
                                            View Logs
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                        No transactions recorded today.
                                    </td>
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
            
            // Create Gradient
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

            const salesData = {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Revenue',
                    data: [1200, 1900, 1500, 2500, 2200, 3000, 2800],
                    fill: true,
                    backgroundColor: gradient,
                    borderColor: '#6366f1',
                    borderWidth: 3,
                    tension: 0.45,
                    pointRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 3,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#6366f1',
                    pointHoverBorderColor: '#fff',
                }]
            };

            const chartConfig = {
                type: 'line',
                data: salesData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            padding: 12,
                            backgroundColor: '#1e293b',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            cornerRadius: 12,
                            displayColors: false,
                            callbacks: {
                                label: (context) => ` Revenue: $${context.parsed.y.toLocaleString()}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { 
                                color: 'rgba(0,0,0,0.03)',
                                drawBorder: false 
                            },
                            ticks: { 
                                callback: val => '$' + val,
                                font: { size: 11, weight: '600' },
                                color: '#94a3b8',
                                padding: 10
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { 
                                font: { size: 11, weight: '600' },
                                color: '#94a3b8',
                                padding: 10
                            }
                        }
                    }
                }
            };

            new Chart(ctx, chartConfig);
        };

        // --- 🖱️ EVENT HANDLERS ---
        const bindEvents = () => {
            $(document).on('click', '.view-order-details', function() {
                const orderId = $(this).data('id');
                window.location.href = "{{ route('sales.orders') }}?order_id=" + orderId;
            });
        };

        // Initialize Everything
        initSalesChart();
        bindEvents();

        // 3D Card Hover Effect Enhancement
        $('.stat-card-3d').on('mousemove', function(e) {
            const card = $(this);
            const rect = card[0].getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;
            
            card.css('transform', `perspective(1000px) translateY(-8px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`);
        }).on('mouseleave', function() {
            $(this).css('transform', '');
        });

    });
</script>
@endsection

