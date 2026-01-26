@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">

        {{-- <!-- Products Widget -->
        <div class="col-12">
            <x-widget title="Home">
                <!-- Table placeholder -->
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- You can fill rows later dynamically -->
                        <tr>
                            <td colspan="5" class="text-center text-muted">Table data will go here</td>
                        </tr>
                    </tbody>
                </table>
            </x-widget>
        </div> --}}

        <div class="col-12 mb-4">
            <div class="row g-4">
                <!-- Total Sales -->
                <div class="col-md-3">
                    <div class="card bg-primary text-white h-100 shadow-sm border-0">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase mb-1 small opacity-75">Total Revenue</h6>
                                <h3 class="mb-0 fw-bold">${{ number_format($data['total_sales'], 2) }}</h3>
                            </div>
                            <div class="fs-1 opacity-50"><i class="fas fa-dollar-sign"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="col-md-3">
                    <div class="card bg-success text-white h-100 shadow-sm border-0">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase mb-1 small opacity-75">Total Orders</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($data['total_orders']) }}</h3>
                            </div>
                            <div class="fs-1 opacity-50"><i class="fas fa-shopping-cart"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Active Products -->
                <div class="col-md-3">
                    <div class="card bg-warning text-dark h-100 shadow-sm border-0">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase mb-1 small opacity-75">Active Products</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($data['total_products']) }}</h3>
                            </div>
                            <div class="fs-1 opacity-50"><i class="fas fa-box-open"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Total Customers -->
                <div class="col-md-3">
                    <div class="card bg-info text-white h-100 shadow-sm border-0">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase mb-1 small opacity-75">Customers</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($data['total_users']) }}</h3>
                            </div>
                            <div class="fs-1 opacity-50"><i class="fas fa-users"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <x-widget title="Welcome to the Admin Panel">
                <p class="mb-0 text-muted">Use the side navigation to manage your store inventory, process orders, and manage users settings.</p>
            </x-widget>


    </div>
</div>
@endsection
