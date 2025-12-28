<!-- Edit Attribute Modal -->
<div class="modal fade" id="editAttributeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-0 text-black">

            <div class="modal-header">
                <h5 class="modal-title">Edit Attribute</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editAttributeForm">
                @csrf
                @method('PUT')

                <input type="hidden" name="id" id="edit_attr_id">

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Attribute Name</label>
                        <input type="text" name="name" id="edit_attr_name"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Values (comma separated)</label>
                        <input type="text" name="values" id="edit_attr_values"
                               class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">
                        Update
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
