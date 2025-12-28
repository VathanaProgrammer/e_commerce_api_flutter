<!-- Add Attribute Modal -->
<div class="modal fade" id="addAttributeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-0 text-black">

            <div class="modal-header">
                <h5 class="modal-title">Add Attribute</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="addAttributeForm">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Attribute Name</label>
                        <input type="text" name="name" class="form-control form-control-sm rounded-0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Values (comma separated)</label>
                        <input type="text" name="values" class="form-control form-control-sm rounded-0"
                               placeholder="Red, Blue, Green">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">
                        Save
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>