@extends('layouts.app')

@section('content')
<div class="container py-4">
    <x-widget title="Edit Product">
        <form id="form_update_product" method="POST" enctype="multipart/form-data"
              action="{{ route('products.update', $product->id) }}">
            @csrf
            @method('PUT')

            <div class="row g-4">

                {{-- LEFT --}}
                <div class="col-md-6">
                    <h6>Basic Info</h6>

                    <input type="text" name="name" class="form-control mb-2"
                           value="{{ old('name', $product->name) }}">

                    <select name="category_id" class="form-select mb-2">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ $cat->id == $product->category_id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>

                    <input type="file" name="image" class="form-control mb-2">

                    @if($product->image)
                        <img src="{{ asset('uploads/products/'.$product->image) }}"
                             style="max-height:150px">
                    @endif
                </div>

                {{-- RIGHT --}}
                <div class="col-md-6">
                    <h6>Attributes</h6>

                    @foreach ($attributes as $attr)
                        <div class="attribute-box mb-2" data-id="{{ $attr->id }}">
                            <strong>{{ $attr->name }}</strong><br>

                            @foreach ($attr->values as $val)
                                <label class="me-2">
                                    <input type="checkbox" class="attribute-check"
                                           value="{{ $val->id }}"
                                           @foreach($product->variants as $v)
                                               @if($v->attributeValues->contains('id',$val->id)) checked @endif
                                           @endforeach>
                                    {{ $val->value }}
                                </label>
                            @endforeach
                        </div>
                    @endforeach

                    <h6 class="mt-3">Variants</h6>

                    <div id="variantsSection">

                        @foreach ($product->variants as $i => $variant)
                            <div class="variant-box mb-2"
                                 data-combo="{{ $variant->attributeValues->pluck('id')->sort()->implode('-') }}">

                                <div class="d-flex gap-2">
                                    <input type="text" name="variants[{{ $i }}][sku]"
                                           value="{{ $variant->sku }}"
                                           class="form-control form-control-sm">

                                    <input type="number" name="variants[{{ $i }}][price]"
                                           value="{{ $variant->price }}"
                                           class="form-control form-control-sm">

                                    <button type="button"
                                            class="btn btn-danger btn-sm remove-variant">X</button>
                                </div>

                                @foreach ($variant->attributeValues as $av)
                                    <input type="hidden"
                                           name="variants[{{ $i }}][attributes][]"
                                           value="{{ $av->id }}">
                                @endforeach
                            </div>
                        @endforeach

                    </div>

                    <button type="button"
                            id="generateVariants"
                            class="btn btn-warning btn-sm mt-2">
                        Generate Variants
                    </button>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button class="btn btn-success">UPDATE PRODUCT</button>
            </div>
        </form>
    </x-widget>
</div>
@endsection
@section('scripts')
<script>
function cartesian(arr) {
    return arr.reduce((a, b) =>
        a.flatMap(d => b.map(e => [...d, e])), [[]]);
}

$('#generateVariants').click(function () {

    let selected = {};
    $('.attribute-box').each(function () {
        let attrId = $(this).data('id');
        let values = [];

        $(this).find('.attribute-check:checked').each(function () {
            values.push({
                id: $(this).val(),
                name: $(this).parent().text().trim()
            });
        });

        if (values.length) selected[attrId] = values;
    });

    let keys = Object.keys(selected);
    if (!keys.length) return;

    let combos = cartesian(keys.map(k => selected[k]));

    let existing = $('#variantsSection .variant-box')
        .map(function () { return $(this).data('combo'); })
        .get();

    let index = $('#variantsSection .variant-box').length;

    combos.forEach(combo => {

        let comboKey = combo.map(v => v.id).sort().join('-');
        if (existing.includes(comboKey)) return;

        let attrs = combo.map(v =>
            `<input type="hidden" name="variants[${index}][attributes][]" value="${v.id}">
             <small class="me-2">${v.name}</small>`
        ).join('');

        $('#variantsSection').append(`
            <div class="variant-box mb-2" data-combo="${comboKey}">
                <div class="d-flex gap-2">
                    <input type="text" name="variants[${index}][sku]"
                           class="form-control form-control-sm" placeholder="SKU">

                    <input type="number" name="variants[${index}][price]"
                           class="form-control form-control-sm" placeholder="Price">

                    <button type="button"
                            class="btn btn-danger btn-sm remove-variant">X</button>
                </div>
                ${attrs}
            </div>
        `);

        index++;
    });
});

$(document).on('click', '.remove-variant', function () {
    $(this).closest('.variant-box').remove();
});
</script>
@endsection
