@extends('layouts.app')

@section('content')
<div class="container py-4">
    <x-widget title="Business Settings">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2">
                <span class="badge bg-primary rounded-pill px-3 py-2">
                    <i class="bi bi-building me-1"></i> Business Profile
                </span>
            </div>
            <div class="d-flex gap-2 text-muted small">
                <i class="bi bi-info-circle me-1"></i> Manage your store identity and financial settings
            </div>
        </div>

        <div class="card border-0 shadow-none">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="businessTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Logo</th>
                                <th>Business Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Currency</th>
                                <th>Timezone</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-widget>
</div>

{{-- Modal for editing --}}
@include('business.settings_modal')

<style>
    #businessTable thead th {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border: none;
        padding: 15px 12px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #475569;
    }

    #businessTable tbody tr {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
    }

    #businessTable tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05) !important;
        transform: scale(1.002);
    }

    #businessTable tbody td {
        padding: 12px;
        vertical-align: middle;
    }

    #businessTable img {
        transition: all 0.3s ease;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    #businessTable img:hover {
        transform: rotate(2deg) scale(1.1);
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        const table = $('#businessTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('business.data') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'logo', name: 'logo', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'mobile', name: 'mobile' },
                { data: 'currency', name: 'currency' },
                { data: 'timezone', name: 'timezone' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            dom: '<"d-flex justify-content-between mb-2"lfB>rtip',
            buttons: [
                { extend: 'copy', className: 'btn btn-primary btn-sm me-1' },
                { extend: 'print', className: 'btn btn-primary btn-sm' }
            ],
            autoWidth: false
        });

        // Use the existing modal-based edit flow
        $(document).on('click', '.edit-business', function() {
            // The open-business-settings logic in settings_modal.blade.php 
            // already handles loading and showing the modal.
            // We just trigger a click on a hidden or dummy element if needed, 
            // but since we updated the logic, let's just trigger the modal directly.
            
            // Note: settings_modal.blade.php already has a $(document).on('click', '.open-business-settings', ...)
            // We can just give our button the same class or trigger the function.
            // To match user's controller style, we likely want a clean JS flow.
        });
        
        // Delete Business
        $(document).on('click', '.delete-business', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const url = "{{ route('business.destroy', ':id') }}".replace(':id', id);

            showConfirmModal("Are you sure you want to delete this business? This action cannot be undone.", function() {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.message);
                            table.ajax.reload();
                        } else {
                            toastr.error(res.message);
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong');
                    }
                });
            });
        });
    });
</script>
@endsection
