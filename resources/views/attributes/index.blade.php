@extends('layouts.app')

@section('content')
<x-widget title="Attributes">
    <a href="{{ route('attributes.create') }}" class="btn btn-sm btn-success mb-2">Add Attribute</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Values</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attributes as $attr)
                <tr>
                    <td>{{ $attr->name }}</td>
                    <td>{{ $attr->values->pluck('value')->implode(', ') }}</td>
                    <td>
                        <a href="{{ route('attributes.edit', $attr->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        <button data-id="{{ $attr->id }}" class="btn btn-sm btn-danger delete-attr">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-widget>
@endsection

@section('scripts')
<script>
$(function() {
    $('.delete-attr').on('click', function() {
        if(!confirm('Delete this attribute?')) return;
        let id = $(this).data('id');
        $.ajax({
            url: '/attributes/' + id,
            type: 'DELETE',
            data: {_token: '{{ csrf_token() }}'},
            success: function(res){ location.reload(); },
            error: function(){ alert('Failed to delete'); }
        });
    });
});
</script>
@endsection
