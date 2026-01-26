@extends('layouts.app')

@section('styles')
<style>
    .stat-card {
        border: none;
        border-radius: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        background: rgba(255, 255, 255, 0.2);
    }
    .bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #2af598 0%, #009efd 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    
    .recent-orders-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .table thead th {
        border-top: none;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Dashboard Overview</h4>
            <p class="text-muted small mb-0">Welcome back, {{ auth()->user()->first_name }}! Here's what's happening today.</p>
        </div>
        <div>
            <button class="btn btn-white shadow-sm btn-sm fw-bold border">
                <i class="fas fa-calendar-alt me-2 text-primary"></i> {{ now()->format('M d, Y') }}
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-4">
        <!-- Revenue -->
        <div class="col-md-3">
            <div class="card stat-card bg-gradient-primary text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                        <span class="badge bg-white bg-opacity-25">+12.5%</span>
                    </div>
                    <h6 class="text-white text-opacity-75 small mb-1">Total Revenue</h6>
                    <h3 class="fw-bold mb-0">${{ number_format($data['total_sales'], 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Orders -->
        <div class="col-md-3">
            <div class="card stat-card bg-gradient-success text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                        <span class="badge bg-white bg-opacity-25">+8.2%</span>
                    </div>
                    <h6 class="text-white text-opacity-75 small mb-1">Total Orders</h6>
                    <h3 class="fw-bold mb-0">{{ number_format($data['total_orders']) }}</h3>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col-md-3">
            <div class="card stat-card bg-gradient-warning text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon"><i class="fas fa-box"></i></div>
                        <span class="badge bg-white bg-opacity-25">Stock</span>
                    </div>
                    <h6 class="text-white text-opacity-75 small mb-1">Active Products</h6>
                    <h3 class="fw-bold mb-0">{{ number_format($data['total_products']) }}</h3>
                </div>
            </div>
        </div>

        <!-- Customers -->
        <div class="col-md-3">
            <div class="card stat-card bg-gradient-info text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <span class="badge bg-white bg-opacity-25">Live</span>
                    </div>
                    <h6 class="text-white text-opacity-75 small mb-1">Total Customers</h6>
                    <h3 class="fw-bold mb-0">{{ number_format($data['total_users']) }}</h3>
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
                    <canvas id="salesChart" style="min-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Top Products -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Quick Actions</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm py-2 rounded-3 text-start">
                            <i class="fas fa-plus-circle me-2"></i> Add New Product
                        </a>
                        <a href="{{ route('sales.orders') }}" class="btn btn-outline-secondary btn-sm py-2 rounded-3 text-start">
                            <i class="fas fa-list me-2"></i> View All Orders
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm py-2 rounded-3 text-start">
                            <i class="fas fa-user-plus me-2"></i> Manage Users
                        </a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">System Status</h5>
                    <div class="d-flex align-items-center mb-3">
                        <div class="status-dot bg-success"></div>
                        <span class="small fw-medium">API: Online</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="status-dot bg-success"></div>
                        <span class="small fw-medium">Payments: Active</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="status-dot bg-success"></div>
                        <span class="small fw-medium">Database: Optimized</span>
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
                                    <td class="ps-4"><span class="fw-medium text-primary">#INV-{{ $order->invoice_no ?? $order->id }}</span></td>
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
                                        <button class="btn btn-light btn-sm rounded-pill" onclick="loadTransactionDetails({{ $order->id }})">
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

{{-- Modal for view details will be handled by the layout include or specific page JS --}}
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Mock data for the chart
        const labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        const data = {
            labels: labels,
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

        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        padding: 10,
                        backgroundColor: '#1e293b',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            drawBorder: false,
                            color: '#f1f5f9'
                        },
                        ticks: {
                            callback: value => '$' + value,
                            font: { size: 11 },
                            color: '#94a3b8'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11 },
                            color: '#94a3b8'
                        }
                    }
                }
            }
        };

        new Chart(ctx, config);
    });

    // Function to handle showing order details (calling from sales script if shared or implementing here)
    function loadTransactionDetails(id) {
        // Since the details modal is probably in sales/view_model, 
        // we might need to include it or just redirect to sales list.
        // For now, let's redirect or use toastr.
        window.location.href = "{{ route('sales.orders') }}?order_id=" + id;
    }
</script>
@endsection
