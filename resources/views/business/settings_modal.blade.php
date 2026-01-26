<!-- Business Settings Modal -->
<div class="modal fade" id="businessSettingsModal" tabindex="-1" aria-labelledby="businessSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <!-- Modal Header -->
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="d-flex align-items-center gap-3 text-white py-2">
                    <div class="settings-icon rounded-circle bg-white bg-opacity-25 p-2">
                        <i class="bi bi-gear-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fw-bold" id="businessSettingsModalLabel">Business Settings</h5>
                        <small class="text-white-50">Manage your business information</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">
                <form id="businessSettingsForm" enctype="multipart/form-data">
                    @csrf

                    <!-- Nav Tabs -->
                    <ul class="nav nav-pills nav-fill mb-4 gap-2" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill" id="general-tab" data-bs-toggle="pill" 
                                    data-bs-target="#general-pane" type="button" role="tab">
                                <i class="bi bi-building me-2"></i>General
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="contact-tab" data-bs-toggle="pill" 
                                    data-bs-target="#contact-pane" type="button" role="tab">
                                <i class="bi bi-telephone me-2"></i>Contact
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="financial-tab" data-bs-toggle="pill" 
                                    data-bs-target="#financial-pane" type="button" role="tab">
                                <i class="bi bi-currency-dollar me-2"></i>Financial
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="social-tab" data-bs-toggle="pill" 
                                    data-bs-target="#social-pane" type="button" role="tab">
                                <i class="bi bi-share me-2"></i>Social
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="settingsTabContent">
                        
                        <!-- General Tab -->
                        <div class="tab-pane fade show active" id="general-pane" role="tabpanel">
                            <div class="row g-4">
                                <!-- Logo Upload -->
                                <div class="col-md-4 text-center">
                                    <div class="logo-upload-section p-4 rounded-4 h-100" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                                        <label class="form-label fw-semibold mb-3">Business Logo</label>
                                        <div class="logo-preview-wrapper mx-auto mb-3 position-relative" style="width: 120px; height: 120px;">
                                            <div class="logo-preview w-100 h-100 rounded-3 bg-white d-flex align-items-center justify-content-center overflow-hidden shadow-sm border">
                                                <img id="businessLogoPreview" src="" alt="Logo" class="w-100 h-100" style="object-fit: contain; display: none;">
                                                <i class="bi bi-building text-muted" id="logoPlaceholder" style="font-size: 3rem;"></i>
                                            </div>
                                            <button type="button" id="removeLogoBtn" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" style="display: none; width: 28px; height: 28px;">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                        <input type="file" name="logo" id="businessLogoInput" class="form-control form-control-sm rounded-pill" accept="image/*">
                                        <small class="text-muted mt-2 d-block">PNG, JPG (Max 2MB)</small>
                                    </div>
                                </div>

                                <!-- Business Info -->
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Business Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-shop text-muted"></i></span>
                                                <input type="text" name="name" id="businessName" class="form-control border-start-0" placeholder="Enter business name" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Timezone</label>
                                            <select name="timezone" id="businessTimezone" class="form-select">
                                                <option value="UTC">UTC</option>
                                                <option value="Asia/Phnom_Penh">Asia/Phnom_Penh (Cambodia)</option>
                                                <option value="Asia/Bangkok">Asia/Bangkok (Thailand)</option>
                                                <option value="Asia/Singapore">Asia/Singapore</option>
                                                <option value="America/New_York">America/New_York</option>
                                                <option value="America/Los_Angeles">America/Los_Angeles</option>
                                                <option value="Europe/London">Europe/London</option>
                                                <option value="Europe/Paris">Europe/Paris</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Date Format</label>
                                            <select name="date_format" id="businessDateFormat" class="form-select">
                                                <option value="Y-m-d">2026-01-26</option>
                                                <option value="d/m/Y">26/01/2026</option>
                                                <option value="m/d/Y">01/26/2026</option>
                                                <option value="d-m-Y">26-01-2026</option>
                                                <option value="M d, Y">Jan 26, 2026</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Time Format</label>
                                            <select name="time_format" id="businessTimeFormat" class="form-select">
                                                <option value="H:i">24h (14:30)</option>
                                                <option value="h:i A">12h (02:30 PM)</option>
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Invoice Footer Text</label>
                                            <textarea name="footer_text" id="businessFooterText" class="form-control" rows="2" placeholder="Text to display on invoices..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Tab -->
                        <div class="tab-pane fade" id="contact-pane" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                        <input type="email" name="email" id="businessEmail" class="form-control border-start-0" placeholder="business@example.com">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone text-muted"></i></span>
                                        <input type="text" name="mobile" id="businessMobile" class="form-control border-start-0" placeholder="+855 12 345 678">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-geo-alt text-muted"></i></span>
                                        <input type="text" name="address" id="businessAddress" class="form-control border-start-0" placeholder="Street address">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">City</label>
                                    <input type="text" name="city" id="businessCity" class="form-control" placeholder="City">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Country</label>
                                    <input type="text" name="country" id="businessCountry" class="form-control" placeholder="Country">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Postal Code</label>
                                    <input type="text" name="postal_code" id="businessPostalCode" class="form-control" placeholder="12000">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Website</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-globe text-muted"></i></span>
                                        <input type="url" name="website" id="businessWebsite" class="form-control border-start-0" placeholder="https://www.example.com">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Tab -->
                        <div class="tab-pane fade" id="financial-pane" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Currency</label>
                                    <select name="currency" id="businessCurrency" class="form-select">
                                        <option value="USD">USD - US Dollar</option>
                                        <option value="KHR">KHR - Cambodian Riel</option>
                                        <option value="EUR">EUR - Euro</option>
                                        <option value="GBP">GBP - British Pound</option>
                                        <option value="THB">THB - Thai Baht</option>
                                        <option value="VND">VND - Vietnamese Dong</option>
                                        <option value="SGD">SGD - Singapore Dollar</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Currency Symbol</label>
                                    <input type="text" name="currency_symbol" id="businessCurrencySymbol" class="form-control" placeholder="$" maxlength="5">
                                </div>

                                <div class="col-12">
                                    <div class="card border-0 rounded-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h6 class="mb-1 fw-bold"><i class="bi bi-percent me-2"></i>Tax Settings</h6>
                                                    <small class="text-muted">Configure tax for your business</small>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="tax_enabled" id="businessTaxEnabled" style="width: 3em; height: 1.5em;">
                                                    <label class="form-check-label fw-semibold" for="businessTaxEnabled">Enable Tax</label>
                                                </div>
                                            </div>

                                            <div class="row g-3 tax-fields">
                                                <div class="col-md-6">
                                                    <label class="form-label">Tax Name</label>
                                                    <input type="text" name="tax_name" id="businessTaxName" class="form-control" placeholder="VAT">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Tax Rate (%)</label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" min="0" max="100" name="tax_rate" id="businessTaxRate" class="form-control" placeholder="10">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Tab -->
                        <div class="tab-pane fade" id="social-pane" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-facebook text-primary me-2"></i>Facebook
                                    </label>
                                    <input type="text" name="facebook" id="businessFacebook" class="form-control" placeholder="facebook.com/yourpage">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-instagram text-danger me-2"></i>Instagram
                                    </label>
                                    <input type="text" name="instagram" id="businessInstagram" class="form-control" placeholder="@yourbusiness">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-telegram text-info me-2"></i>Telegram
                                    </label>
                                    <input type="text" name="telegram" id="businessTelegram" class="form-control" placeholder="@yourbusiness">
                                </div>
                            </div>

                            <div class="mt-4 p-3 rounded-3 bg-light">
                                <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Tips</h6>
                                <ul class="mb-0 small text-muted">
                                    <li>Add social links to display on invoices and receipts</li>
                                    <li>Customers can easily find and follow your business</li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4" id="saveBusinessSettings">
                    <i class="bi bi-check-lg me-2"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #businessSettingsModal .nav-pills .nav-link {
        color: #64748b;
        background: #f1f5f9;
        transition: all 0.3s ease;
    }

    #businessSettingsModal .nav-pills .nav-link:hover {
        background: #e2e8f0;
    }

    #businessSettingsModal .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    #businessSettingsModal .form-control,
    #businessSettingsModal .form-select {
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        padding: 10px 14px;
        transition: all 0.3s ease;
    }

    #businessSettingsModal .form-control:focus,
    #businessSettingsModal .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    #businessSettingsModal .input-group-text {
        border: 2px solid #e2e8f0;
        border-right: none;
        border-radius: 10px 0 0 10px;
    }

    #businessSettingsModal .input-group .form-control {
        border-left: none;
        border-radius: 0 10px 10px 0;
    }

    #businessSettingsModal .tab-pane {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    #businessSettingsModal .logo-preview-wrapper:hover .logo-preview {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    #businessSettingsModal .logo-preview {
        transition: all 0.3s ease;
    }

    .tax-fields {
        transition: all 0.3s ease;
    }
</style>

<script>
    $(document).ready(function() {
        const settingsModal = document.getElementById('businessSettingsModal');
        let businessSettingsModalInstance = null;

        // Initialize modal
        function getBusinessSettingsModal() {
            if (!businessSettingsModalInstance) {
                businessSettingsModalInstance = new bootstrap.Modal(settingsModal, {
                    backdrop: true,
                    keyboard: true
                });
            }
            return businessSettingsModalInstance;
        }

        // Open settings modal
        $(document).on('click', '.open-business-settings', function(e) {
            e.preventDefault();
            const id = $(this).data('id') || '';
            loadBusinessSettings(id);
            getBusinessSettingsModal().show();
        });

        // Load business settings
        function loadBusinessSettings(id) {
            let url = "{{ route('business.show', ':id') }}".replace(':id', id);
            
            // If no ID (like from a generic link), we might need to handle it 
            // but the DataTable button always provides an ID.
            if (!id) {
                // Fallback for generic calls if any
                url = "{{ route('business.show', 1) }}"; 
            }

            $.ajax({
                url: url,
                method: 'GET',
                success: function(res) {
                    if (res.success && res.data) {
                        const data = res.data;
                        
                        // Set ID for the update URL
                        $('#businessSettingsForm').attr('data-id', data.id);

                        // General
                        $('#businessName').val(data.name || '');
                        $('#businessTimezone').val(data.timezone || 'UTC');
                        $('#businessDateFormat').val(data.date_format || 'Y-m-d');
                        $('#businessTimeFormat').val(data.time_format || 'H:i');
                        $('#businessFooterText').val(data.footer_text || '');

                        // Logo
                        if (data.logo_url) {
                            $('#businessLogoPreview').attr('src', data.logo_url).show();
                            $('#logoPlaceholder').hide();
                            $('#removeLogoBtn').show();
                        } else {
                            $('#businessLogoPreview').hide();
                            $('#logoPlaceholder').show();
                            $('#removeLogoBtn').hide();
                        }

                        // Contact
                        $('#businessEmail').val(data.email || '');
                        $('#businessMobile').val(data.mobile || '');
                        $('#businessAddress').val(data.address || '');
                        $('#businessCity').val(data.city || '');
                        $('#businessCountry').val(data.country || '');
                        $('#businessPostalCode').val(data.postal_code || '');
                        $('#businessWebsite').val(data.website || '');

                        // Financial
                        $('#businessCurrency').val(data.currency || 'USD');
                        $('#businessCurrencySymbol').val(data.currency_symbol || '$');
                        $('#businessTaxEnabled').prop('checked', data.tax_enabled || false);
                        $('#businessTaxName').val(data.tax_name || 'VAT');
                        $('#businessTaxRate').val(data.tax_rate || 0);

                        // Social
                        $('#businessFacebook').val(data.facebook || '');
                        $('#businessInstagram').val(data.instagram || '');
                        $('#businessTelegram').val(data.telegram || '');

                        // Toggle tax fields
                        toggleTaxFields(data.tax_enabled);
                    }
                },
                error: function() {
                    toastr.error('Failed to load business settings');
                }
            });
        }

        // Toggle tax fields visibility
        function toggleTaxFields(enabled) {
            if (enabled) {
                $('.tax-fields').css('opacity', '1').find('input').prop('disabled', false);
            } else {
                $('.tax-fields').css('opacity', '0.5').find('input').prop('disabled', true);
            }
        }

        $('#businessTaxEnabled').on('change', function() {
            toggleTaxFields($(this).is(':checked'));
        });

        // Logo preview
        $('#businessLogoInput').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#businessLogoPreview').attr('src', e.target.result).show();
                    $('#logoPlaceholder').hide();
                    $('#removeLogoBtn').show();
                }
                reader.readAsDataURL(file);
            }
        });

        // Remove logo
        $('#removeLogoBtn').on('click', function() {
            $.ajax({
                url: "{{ route('business.logo.remove') }}",
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.success) {
                        $('#businessLogoPreview').attr('src', '').hide();
                        $('#logoPlaceholder').show();
                        $('#removeLogoBtn').hide();
                        $('#businessLogoInput').val('');
                        toastr.success('Logo removed');
                    }
                },
                error: function() {
                    toastr.error('Failed to remove logo');
                }
            });
        });

        // Save settings
        $('#saveBusinessSettings').on('click', function() {
            const form = $('#businessSettingsForm')[0];
            const formData = new FormData(form);
            const id = $('#businessSettingsForm').attr('data-id') || '';
            
            // Handle checkbox
            formData.set('tax_enabled', $('#businessTaxEnabled').is(':checked') ? 1 : 0);

            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat me-2" style="animation: spin 1s linear infinite;"></i>Saving...');

            let updateUrl = "{{ route('business.settings.update', ':id') }}".replace(':id', id);
            if (!id) {
                updateUrl = "{{ route('business.settings.update') }}"; // Fallback to first if no ID
            }

            $.ajax({
                url: updateUrl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message || 'Settings saved successfully');
                        
                        // Close modal
                        getBusinessSettingsModal().hide();

                        // Reload table if on business settings page
                        if ($('#businessTable').length) {
                            $('#businessTable').DataTable().ajax.reload();
                        } else {
                            // Reload page to refresh session data on other pages
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }
                    } else {
                        toastr.error(res.message || 'Failed to save settings');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, val) {
                            toastr.error(val[0]);
                        });
                    } else {
                        toastr.error('Failed to save settings');
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="bi bi-check-lg me-2"></i>Save Changes');
                }
            });
        });

        // Cleanup on modal close
        settingsModal.addEventListener('hidden.bs.modal', function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css({
                'overflow': '',
                'padding-right': ''
            });
        });

        // Currency symbol auto-update
        $('#businessCurrency').on('change', function() {
            const symbols = {
                'USD': '$',
                'KHR': '៛',
                'EUR': '€',
                'GBP': '£',
                'THB': '฿',
                'VND': '₫',
                'SGD': '$'
            };
            const currency = $(this).val();
            if (symbols[currency]) {
                $('#businessCurrencySymbol').val(symbols[currency]);
            }
        });
    });
</script>
