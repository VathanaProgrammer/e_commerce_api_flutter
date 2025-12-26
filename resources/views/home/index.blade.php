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

        <div class="col-12">
            <x-widget title="Welcome to the LUXON Admin Panel">
                <p class="mb-0">Use the navigation menu to manage products, categories, and orders.</p>
            </x-widget>

    </div>
</div>
@endsection
