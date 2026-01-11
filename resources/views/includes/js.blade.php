<script type="text/javascript">
    console.log('Global JS loaded');

    $(document).ready(function() {

        let globalConfirmModal = new bootstrap.Modal(document.getElementById('globalConfirmModal'));
        let confirmCallback = null;

        /**
         * Open the global confirmation modal
         * @param {String} message - Message to show in modal
         * @param {Function} onConfirm - Callback when "Yes" is clicked
         */
        window.showConfirmModal = function(message = "Are you sure?", onConfirm = null) {
            document.getElementById('globalConfirmModalBody').innerHTML = message;
            confirmCallback = onConfirm;
            globalConfirmModal.show();
        }

        // Handle confirm button click
        document.getElementById('globalConfirmModalConfirmBtn').addEventListener('click', function() {
            if (typeof confirmCallback === "function") {
                confirmCallback();
            }
            globalConfirmModal.hide();
        });


        $(document).on('submit', '.logout-form', function(e) {
            e.preventDefault();
            let form = this;
            showConfirmModal("Are you sure you want to logout?", function() {
                form.submit();
            });
        });

//         // -----------------------------
//         // Submit Product Form via AJAX
//         // -----------------------------

//         // -----------------------------
//         // Product Description Lines
//         // -----------------------------
//         $('#addLine').on('click', function() {
//             let lineHtml = `
//         <div class="row g-2 mb-2 align-items-center">
//             <div class="col-auto">
//                 <input type="text" name="description_lines[]" class="form-control form-control-sm rounded-0" placeholder="Description line">
//             </div>
//             <div class="col-auto">
//                 <button type="button" class="btn btn-sm btn-outline-danger removeLine">Remove</button>
//             </div>
//         </div>`;
//             $('#descriptionLines').append(lineHtml);
//         });

//         $(document).on('click', '.removeLine', function() {
//             $(this).closest('.row').remove();
//         });

//         // -----------------------------
//         // Attributes
//         // -----------------------------
//         $('#addAttr').on('click', function() {
//             let attrHtml = `
//         <div class="row g-2 mb-2 align-items-center">
//             <div class="col-auto">
//                 <input type="text" name="attributes[]" class="form-control form-control-sm rounded-0" placeholder="Attribute name">
//             </div>
//             <div class="col-auto">
//                 <button type="button" class="btn btn-sm btn-outline-danger removeAttr">Remove</button>
//             </div>
//         </div>`;
//             $('#attributesSection').append(attrHtml);
//         });

//         $(document).on('click', '.removeAttr', function() {
//             $(this).closest('.row').remove();
//         });

//         // -----------------------------
//         // Variants
//         // -----------------------------
// $('#addVariant').off('click').on('click', function() {
//     let variantHtml = `
//     <div class="row g-2 mb-2 align-items-center">
//         <div class="col-auto">
//             <input type="text" name="variant_sku[]" class="form-control form-control-sm rounded-0" placeholder="SKU">
//         </div>
//         <div class="col-auto">
//             <div class="input-group input-group-sm">
//                 <span class="input-group-text">$</span>
//                 <input type="number" name="variant_price[]" class="form-control form-control-sm rounded-0" placeholder="Price">
//             </div>
//         </div>
//         <div class="col-auto">
//             <div class="attribute-values">
//                 <!-- Attribute values will be dynamically appended here if needed -->
//             </div>
//         </div>
//         <div class="col-auto">
//             <button type="button" class="btn btn-sm btn-outline-danger removeVariant">Remove</button>
//         </div>
//     </div>`;
//     $('#variantsSection').append(variantHtml);
// });
//         $(document).on('click', '.removeVariant', function() {
//             $(this).closest('.row').remove();
//         });

//         // -----------------------------
//         // Quick Add Category via Modal (AJAX example)
//         // -----------------------------
        $('#quickAddModal form').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let categoryName = form.find('input[name="name"]').val().trim();

            if (!categoryName) {
                // Hide modal first
                $('#quickAddModal').modal('hide');

                // Show toastr after modal fully closed
                $('#quickAddModal').on('hidden.bs.modal.toastError', function() {
                    toastr.error('Please enter category name!');
                    $('#quickAddModal').off('hidden.bs.modal.toastError'); // remove listener
                });

                return;
            }

            $.ajax({
                url: "{{ route('categories.store') }}",
                method: "POST",
                data: form.serialize(),
                success: function(res) {
                    $('select[name="category_id"]').append(
                        `<option value="${res.data.id}" selected>${res.data.name}</option>`
                    );
                    $('#quickAddModal').modal('hide');

                    console.log('Category added successfully', res.data);

                    if (res.data.success) {
                        toastr.success(res.data.msg || 'Category added successfully');
                        form[0].reset();
                    } else {
                        toastr.error(res.data.msg || 'Failed to add category');
                    }

                },
                error: function(err) {
                    $('#quickAddModal').modal('hide');

                    $('#quickAddModal').on('hidden.bs.modal.toastFail', function() {
                        toastr.error('Failed to add category');
                        $('#quickAddModal').off('hidden.bs.modal.toastFail');
                    });
                }
            });
        });

    });

    // Page-specific JS
    @yield('page-js')
</script>
