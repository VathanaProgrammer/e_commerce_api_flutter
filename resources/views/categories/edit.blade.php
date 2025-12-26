<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content rounded-0">
            <form method="POST">
                @csrf
                <div class="modal-header py-2">
                    <h6 class="modal-title text-black">Edit Category</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="name" class="form-control form-control-sm rounded-0" placeholder="Category name">
                    <input type="hidden" name="cate_id">
                </div>
                <div class="modal-footer py-2">
                    <button class="btn btn-sm btn-primary rounded-0">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
