@extends('layouts.app')

@section('content')
    <x-widget title="Create Product">

        <form id="form_add_product">
            @csrf

            <div class="row g-3 align-items-end">

                {{-- Product Name --}}
                <div class="col-auto">
                    <label class="form-label small mb-1">Product Name</label>
                    <input type="text" name="name" class="form-control form-control-sm rounded-0"
                        placeholder="Product name">
                </div>

                {{-- Category with Quick Add --}}
                <div class="col-auto">
                    <label class="form-label small mb-1">Category</label>
                    <div class="d-flex">
                        <select name="category_id" class="form-select form-select-sm rounded-0 me-2">
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-0" data-bs-toggle="modal"
                            data-bs-target="#quickAddModal">
                            Add
                        </button>
                    </div>
                </div>

            </div>

            {{-- Description Lines --}}
            <div class="mt-3">
                <label class="form-label small mb-1">Product Description Lines</label>
                <div id="descriptionLines">
                    <div class="row g-2 mb-2 align-items-center">
                        <div class="col-auto">
                            <input type="text" name="description_lines[]" class="form-control form-control-sm rounded-0"
                                placeholder="Description line">
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm btn-outline-danger removeLine">Remove</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addLine">Add Line</button>
            </div>

            {{-- Attributes --}}
            <div class="mt-3">
                <label class="form-label small mb-1">Attributes</label>
                <div id="attributesSection">
                    <div class="row g-2 mb-2 align-items-center">
                        <div class="col-auto">
                            <input type="text" name="attributes[]" class="form-control form-control-sm rounded-0"
                                placeholder="Attribute name">
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm btn-outline-danger removeAttr">Remove</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="addAttr">Add Attribute</button>
            </div>

            {{-- Variants --}}
            <div class="mt-3">
                <label class="form-label small mb-1">Variants</label>
                <div id="variantsSection">
                    <div class="row g-2 mb-2 align-items-center">
                        <div class="col-auto">
                            <input type="text" name="variant_sku[]" class="form-control form-control-sm rounded-0"
                                placeholder="SKU">
                        </div>
                        <div class="col-auto">
                            <input type="number" name="variant_price[]" class="form-control form-control-sm rounded-0"
                                placeholder="Price">
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm btn-outline-danger removeVariant">Remove</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-warning" id="addVariant">Add Variant</button>
            </div>

            {{-- Submit --}}
            <div class="mt-4">
                <button type="submit" class="btn btn-sm btn-success rounded-0">Create Product</button>
            </div>

        </form>

    </x-widget>

    {{-- Quick Add Category Modal --}}
    <div class="modal fade" id="quickAddModal" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content rounded-0">
                <form method="POST" action="{{ route('categories.store') }}">
                    @csrf
                    <div class="modal-header py-2">
                        <h6 class="modal-title text-black">Add Category</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="name" class="form-control form-control-sm rounded-0"
                            placeholder="Category name">
                    </div>
                    <div class="modal-footer py-2">
                        <button class="btn btn-sm btn-primary rounded-0">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
