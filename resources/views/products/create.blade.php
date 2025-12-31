@extends('layouts.app')

@section('content')
    <x-widget title="Create Product">
        <form id="form_add_product">
            @csrf

            {{-- PRODUCT BASIC --}}
            <div class="row g-3 align-items-end">
                <div class="col-auto">
                    <label class="form-label small">Product Name</label>
                    <input type="text" name="name" class="form-control form-control-sm rounded-0">
                </div>

                <div class="col-auto">
                    <label class="form-label small">Category</label>
                    <select name="category_id" class="form-select form-select-sm rounded-0">
                        <option value="">Select</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- DESCRIPTION --}}
            <div class="mt-3">
                <label class="form-label small">Descriptions</label>
                <div id="descriptionLines">
                    <input type="text" name="description_lines[]" class="form-control form-control-sm mb-1 rounded-0">
                </div>
                <button type="button" id="addLine" class="btn btn-sm btn-outline-primary">Add Line</button>
            </div>

            {{-- ATTRIBUTES --}}
            <div class="mt-4">
                <label class="form-label small">Attributes</label>
                <div id="attributesSection">
                    @foreach ($attributes as $attr)
                        <div class="attribute-box border p-2 mb-2" data-id="{{ $attr->id }}">
                            <label class="small">{{ $attr->name }}</label>
                            <div class="values">
                                @foreach ($attr->values as $val)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input attribute-check" type="checkbox"
                                            name="product_attributes[{{ $attr->id }}][]" value="{{ $val->id }}">
                                        <label class="form-check-label">{{ $val->value }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- VARIANTS --}}
            <div class="mt-4">
                <label class="form-label small">Variants</label>
                <div id="variantsSection"></div>
                <button type="button" id="generateVariants" class="btn btn-sm btn-outline-warning">Generate
                    Variants</button>
            </div>

            {{-- DISCOUNT --}}
            <div class="mt-4">
                <label class="form-label small">Discount</label>
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <input type="text" name="discount[name]" class="form-control form-control-sm rounded-0"
                            placeholder="Discount Name">
                    </div>
                    <div class="col-auto">
                        <input type="number" step="0.01" name="discount[value]"
                            class="form-control form-control-sm rounded-0" placeholder="Value">
                    </div>
                    <div class="col-auto">
                        <select name="discount[is_percentage]" class="form-select form-select-sm rounded-0">
                            <option value="1">Percentage %</option>
                            <option value="0">Fixed Amount $</option>
                        </select>
                    </div>
                    <div class="col-auto d-flex align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="discount[active]" value="1" checked>
                            <label class="form-check-label small">Active</label>
                        </div>
                    </div>
                </div>
            </div>


            {{-- SUBMIT --}}
            <div class="mt-4">
                <button class="btn btn-sm btn-success">SAVE PRODUCT</button>
            </div>
        </form>
    </x-widget>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('#addLine').click(function() {
                $('#descriptionLines').append(
                    `<input type="text" name="description_lines[]" class="form-control form-control-sm mb-1 rounded-0">`
                );
            });

            function cartesian(arr) {
                return arr.reduce((a, b) =>
                    a.flatMap(d => b.map(e => [...d, e])), [
                        []
                    ]
                );
            }

            $('#generateVariants').click(function() {
                $('#variantsSection').empty();
                let selected = {};
                $('.attribute-box').each(function() {
                    let attrId = $(this).data('id');
                    let vals = [];
                    $(this).find('.attribute-check:checked').each(function() {
                        vals.push({
                            id: $(this).val(),
                            name: $(this).next('label').text()
                        });
                    });
                    if (vals.length) selected[attrId] = vals;
                });

                let keys = Object.keys(selected);
                if (keys.length === 0) return;

                let arrays = keys.map(k => selected[k]);
                let combos = cartesian(arrays);

                combos.forEach((combo, index) => {
                    let selectsHtml = combo.map(c =>
                        `<div>${c.name} <input type="hidden" name="variants[${index}][attributes][]" value="${c.id}"></div>`
                    ).join('');

                    $('#variantsSection').append(`
                <div class="variant-box border p-2 mb-2">
                    <input type="text" name="variants[${index}][sku]" class="form-control form-control-sm mb-1" placeholder="SKU">
                    <input type="number" name="variants[${index}][price]" class="form-control form-control-sm mb-1" placeholder="Price">
                    ${selectsHtml}
                </div>
            `);
                });
            });

            $('#form_add_product').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                $.ajax({
                    url: "{{ route('products.store') }}",
                    method: 'POST',
                    data: form.serialize(),
                    success: function(res) {
                        toastr.success( res.msg || 'Product created!');
                        form[0].reset();
                        $('#variantsSection').empty();
                        window.location.href = res.location;
                        console.log('Product created', res);
                    },
                    error: function(err) {
                        toastr.error('Failed.');
                    }
                });
            });
        });
    </script>
@endsection
