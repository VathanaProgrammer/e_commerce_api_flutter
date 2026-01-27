@extends('layouts.app')

@section('styles')
<style>
    :root {
        --depth-1: 0 4px 10px rgba(0,0,0,0.1);
        --depth-2: 0 15px 35px rgba(0,0,0,0.15);
        --depth-3: 0 30px 60px rgba(0,0,0,0.25);
        --accent-glow: 0 0 20px rgba(99, 102, 241, 0.4);
    }

    /* Immersive 3D Space */
    .dashboard-container {
        perspective: 2000px;
        transform-style: preserve-3d;
        padding-top: 2rem;
    }

    /* Super 3D Card Base */
    .super-3d-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 30px;
        transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        transform-style: preserve-3d;
        box-shadow: var(--depth-2);
        position: relative;
        overflow: visible; /* Allow Z-depth elements to pop out */
        height: 100%;
        cursor: pointer;
    }
    
    .super-3d-card:hover {
        transform: translateY(-15px) rotateX(4deg) rotateY(-4deg);
        box-shadow: var(--depth-3);
        border-color: rgba(255, 255, 255, 0.8);
    }

    /* Floating Internal Elements */
    .layer-base { transform: translateZ(20px); }
    .layer-mid { transform: translateZ(50px); }
    .layer-top { transform: translateZ(80px); }

    /* Shadow under card when hovering */
    .super-3d-card::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: 5%;
        width: 90%;
        height: 20px;
        background: rgba(0,0,0,0.15);
        filter: blur(20px);
        border-radius: 50%;
        opacity: 0;
        transition: opacity 0.6s ease;
        transform: translateZ(-50px);
    }

    .super-3d-card:hover::after {
        opacity: 1;
    }

    /* Icon Box with Extreme Depth */
    .icon-box-3d {
        width: 70px;
        height: 70px;
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 25px;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
        box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.5);
        position: relative;
    }

    .icon-box-3d::before {
        content: '';
        position: absolute;
        inset: -5px;
        background: inherit;
        filter: blur(15px);
        opacity: 0.3;
        border-radius: inherit;
        transform: translateZ(-10px);
    }

    /* Typography Over 3D */
    .counter-hero {
        font-size: 2.75rem;
        font-weight: 900;
        letter-spacing: -0.04em;
        background: linear-gradient(180deg, #1e293b 0%, #475569 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0;
        filter: drop-shadow(0 5px 10px rgba(0,0,0,0.1));
    }

    .stat-label-3d {
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: #64748b;
        margin-bottom: 8px;
        display: block;
    }

    /* Decorative Floating Pills */
    .floating-pill {
        position: absolute;
        padding: 5px 12px;
        border-radius: 100px;
        font-size: 0.7rem;
        font-weight: 700;
        background: rgba(255,255,255,0.9);
        box-shadow: var(--depth-1);
        right: -10px;
        top: 20px;
        border: 1px solid rgba(0,0,0,0.05);
    }

    /* Table Rows as Floating Bars */
    .floating-table tr {
        background: white;
        margin-bottom: 15px;
        display: table-row;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        border-radius: 20px;
    }

    .floating-table tbody {
        border-spacing: 0 12px;
        border-collapse: separate;
    }

    .floating-table tr td {
        border: none;
        padding: 1.5rem 1rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }

    .floating-table tr td:first-child { border-radius: 20px 0 0 20px; padding-left: 2rem; }
    .floating-table tr td:last-child { border-radius: 0 20px 20px 0; padding-right: 2rem; }

    .floating-table tr:hover {
        transform: scale(1.02) translateZ(30px);
        box-shadow: var(--depth-2);
        position: relative;
        z-index: 100;
    }

    /* Operations Buttons */
    .op-btn-3d {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 20px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        transition: all 0.4s ease;
        text-decoration: none;
        color: #1e293b;
        font-weight: 700;
    }

    .op-btn-3d:hover {
        background: #6366f1;
        color: white;
        transform: translateZ(40px) translateX(10px);
        box-shadow: 0 15px 30px -10px rgba(99, 102, 241, 0.5);
    }

    .op-btn-3d i {
        font-size: 1.4rem;
        width: 45px;
        height: 45px;
        background: rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
    }

    .op-btn-3d:hover i {
        background: rgba(255,255,255,0.2);
    }

    /* Background Orbs */
    .bg-orb {
        position: fixed;
        width: 400px;
        height: 400px;
        border-radius: 50%;
        filter: blur(80px);
        z-index: -1;
        opacity: 0.4;
        animation: floatOrb 20s infinite alternate;
    }

    @keyframes floatOrb {
        from { transform: translate(-10%, -10%) rotate(0deg); }
        to { transform: translate(10%, 10%) rotate(360deg); }
    }

    /* Performance Chart Glass */
    .chart-container-3d {
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 40px;
        padding: 2.5rem;
        box-shadow: var(--depth-2);
        transform-style: preserve-3d;
    }

</style>
@endsection

@section('content')
<div class="bg-orb" style="background: #e0e7ff; top: -100px; right: -100px;"></div>
<div class="bg-orb" style="background: #fae8ff; bottom: -100px; left: -100px; animation-delay: -5s;"></div>

<div class="container py-4 dashboard-container">
    <!-- Super Header -->
    <div class="row mb-5 align-items-center" style="transform: translateZ(100px);">
        <div class="col-lg-8">
            <h1 class="display-5 fw-900 mb-0" style="color: #0f172a; letter-spacing: -0.05em;">
                Command <span class="text-primary" style="text-shadow: 0 10px 20px rgba(99, 102, 241, 0.2);">Center</span>
            </h1>
            <p class="text-muted fw-600 fs-5 mt-2">Welcome back, {{ auth()->user()->first_name }}. The system is running at peak performance.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="bg-white p-2 rounded-pill shadow-sm d-inline-block border">
                <span class="badge bg-primary rounded-pill px-3 py-2 me-2">LIVE</span>
                <span class="fw-800 pe-3">{{ now()->format('D, M d') }}</span>
            </div>
        </div>
    </div>

    <!-- Main 3D Grid -->
    <div class="row g-5 mb-5">
        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card super-3d-card p-4">
                <div class="layer-top floating-pill text-success">
                    <i class="bi bi-arrow-up-right me-1"></i> 12.5%
                </div>
                <div class="layer-mid icon-box-3d" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="layer-base">
                    <span class="stat-label-3d">Net Revenue</span>
                    <h2 class="counter-hero">${{ number_format($data['total_sales'] ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card super-3d-card p-4">
                <div class="layer-mid icon-box-3d">
                    <i class="bi bi-cart4"></i>
                </div>
                <div class="layer-base">
                    <span class="stat-label-3d">Transactions</span>
                    <h2 class="counter-hero">{{ number_format($data['total_orders'] ?? 0) }}</h2>
                </div>
                <div class="layer-top floating-pill text-primary" style="top: auto; bottom: 20px;">
                    Peak Volume
                </div>
            </div>
        </div>

        <!-- Products Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card super-3d-card p-4">
                <div class="layer-mid icon-box-3d" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <div class="layer-base">
                    <span class="stat-label-3d">Active Assets</span>
                    <h2 class="counter-hero">{{ number_format($data['total_products'] ?? 0) }}</h2>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card super-3d-card p-4">
                <div class="layer-mid icon-box-3d" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                    <i class="bi bi-person-bounding-box"></i>
                </div>
                <div class="layer-base">
                    <span class="stat-label-3d">User Reach</span>
                    <h2 class="counter-hero">{{ number_format($data['total_users'] ?? 0) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics & Ops -->
    <div class="row g-5">
        <div class="col-lg-8">
            <div class="chart-container-3d">
                <div class="d-flex justify-content-between align-items-center mb-5" style="transform: translateZ(30px);">
                    <div>
                        <h4 class="fw-900 mb-0">Market Analytics</h4>
                        <p class="text-muted small">Real-time revenue dynamics</p>
                    </div>
                    <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                        <button class="btn btn-white btn-sm px-4 active">WEEKLY</button>
                        <button class="btn btn-white btn-sm px-4">MONTHLY</button>
                    </div>
                </div>
                <div style="height: 400px; transform: translateZ(50px);">
                    <canvas id="superChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <h5 class="fw-800 mb-4 ps-2" style="transform: translateZ(20px);">Quick Executions</h5>
            <div style="perspective: 1000px;">
                <a href="{{ route('products.create') }}" class="op-btn-3d">
                    <i class="bi bi-plus-lg"></i>
                    <span>Deploy New Product</span>
                </a>
                <a href="{{ route('sales.orders') }}" class="op-btn-3d">
                    <i class="bi bi-kanban"></i>
                    <span>Order Pipeline</span>
                </a>
                <a href="{{ route('users.index') }}" class="op-btn-3d">
                    <i class="bi bi-fingerprint"></i>
                    <span>Security & Access</span>
                </a>
            </div>

            <div class="card super-3d-card mt-4 bg-dark text-white p-4" style="border: none;">
                <div class="d-flex align-items-center mb-3">
                    <div class="spinner-grow text-success spinner-grow-sm me-3"></div>
                    <h6 class="mb-0 fw-800">SYSTEM STATUS</h6>
                </div>
                <div class="progress bg-white bg-opacity-10 mb-2" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: 85%"></div>
                </div>
                <span class="small text-muted">Node Cluster: Stable (99.9% Uptime)</span>
            </div>
        </div>

        <!-- Floating Table -->
        <div class="col-12 mt-4">
            <div class="chart-container-3d">
                <div class="d-flex justify-content-between align-items-center mb-4" style="transform: translateZ(30px);">
                    <h4 class="fw-900 mb-0">Incoming Activity</h4>
                    <a href="{{ route('sales.orders') }}" class="btn btn-primary rounded-pill px-4 fw-800 shadow-lg">EXPLORE ALL</a>
                </div>
                <div class="table-responsive">
                    <table class="table floating-table align-middle">
                        <thead>
                            <tr class="text-muted small">
                                <th class="border-0 opacity-50">IDENTIFIER</th>
                                <th class="border-0 opacity-50">SOURCE</th>
                                <th class="border-0 opacity-50 text-center">PAYLOAD</th>
                                <th class="border-0 opacity-50">VALUE</th>
                                <th class="border-0 opacity-50 text-end">STATE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['recent_orders'] as $order)
                            <tr>
                                <td><span class="fw-900">#{{ $order->invoice_no ?? $order->id }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sq me-3 bg-indigo-subtle">
                                            {{ strtoupper(substr($order->user->first_name ?? 'G', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-800 text-dark">{{ $order->user->first_name ?? 'Guest' }}</div>
                                            <div class="small text-muted">{{ $order->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-600">
                                        {{ $order->total_items }} SKU
                                    </span>
                                </td>
                                <td><span class="fw-900 text-primary">${{ number_format($order->total_sell_price, 2) }}</span></td>
                                <td class="text-end">{!! $order->status->badge() !!}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-5">System idle - No records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-sq {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: #6366f1;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    const ctx = document.getElementById('superChart').getContext('2d');
    
    // Glossy Gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '23:59'],
            datasets: [{
                data: [400, 600, 550, 900, 800, 1200, 1100],
                borderColor: '#6366f1',
                borderWidth: 5,
                backgroundColor: gradient,
                fill: true,
                tension: 0.5,
                pointRadius: 0,
                pointHoverRadius: 8,
                pointHoverBorderWidth: 4,
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#6366f1',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { display: false },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8', font: { weight: '800', size: 10 } }
                }
            }
        }
    });

    // Super 3D Hover Interaction
    $('.super-3d-card, .chart-container-3d').on('mousemove', function(e) {
        const card = $(this);
        const rect = card[0].getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const rotateX = (y - rect.height/2) / 10;
        const rotateY = (rect.width/2 - x) / 10;
        
        card.css('transform', `rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-10px)`);
    }).on('mouseleave', function() {
        $(this).css('transform', '');
    });
});
</script>
@endsection

