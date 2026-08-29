@extends('backend.master')

@push('style')
    <style>
        .settings-card {
            border: 0;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .settings-card .card-header {
            background: #fff;
            border-bottom: 1px solid #eee;
            padding: 4px 6px;
            font-weight: 600;
        }

        .settings-card .card-body {
            padding: 5px;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 2px;
        }

        .form-control,
        .form-select {
            min-height: 32px;
            border-radius: 6px;
        }

        .logo-preview-wrapper {
            width: 150px;
            height: 150px;
            border: 1px dashed #bbb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fafafa;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .logo-preview-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .logo-placeholder {
            text-align: center;
            color: #999;
        }

        .logo-placeholder i {
            font-size: 40px;
            display: block;
            margin-bottom: 5px;
        }
    </style>
@endpush

@section('content')
    <div class="app-content">
        <div class="container-fluid">
            {{-- Page Header --}}
            <div class="d-flex justify-content-between align-items-center mb-1">
                <div>
                    <h4 class="mb-1">Company Settings</h4>
                    <small class="text-muted">
                        Manage your company information
                    </small>
                </div>
            </div>
            <div class="card card-primary card-outline">
                <form action="{{ route('settings.company.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        {{-- LEFT SIDE --}}
                        <div class="col-lg-8">
                            {{-- Basic Information --}}
                            <div class="card settings-card mb-4">
                                <div class="card-header">
                                    <i class="bi bi-building me-2"></i>
                                    Company Information
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        {{-- Company Name --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Company Name
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ old('name', $company->name ?? '') }}"
                                                placeholder="Enter company name" required>
                                        </div>

                                        {{-- Short Name --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Short Name</label>
                                            <input type="text" name="short_name" class="form-control"
                                                value="{{ old('short_name', $company->short_name ?? '') }}"
                                                placeholder="Company short name">
                                        </div>

                                        {{-- Address --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Address
                                                <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="address" class="form-control" rows="2"
                                                placeholder="Enter company address">{{ old('address', $company->address ?? '') }}</textarea>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Contact Person</label>
                                            <input type="text" name="contact_person" class="form-control"
                                                value="{{ old('contact_person', $company->contact_person ?? '') }}"
                                                placeholder="Contact person name">
                                        </div>

                                        {{-- Phone --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="phone" class="form-control"
                                                value="{{ old('phone', $company->phone ?? '') }}" placeholder="01XXXXXXXXX">
                                        </div>

                                        {{-- Email --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control"
                                                value="{{ old('email', $company->email ?? '') }}"
                                                placeholder="company@example.com">
                                        </div>

                                        {{-- Website --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Website</label>
                                            <input type="url" name="website" class="form-control"
                                                value="{{ old('website', $company->website ?? '') }}"
                                                placeholder="https://example.com">
                                        </div>

                                        {{-- Tax ID --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tax / BIN Number</label>
                                            <input type="text" name="tax_number" class="form-control"
                                                value="{{ old('tax_number', $company->tax_number ?? '') }}"
                                                placeholder="Enter Tax / BIN number">
                                        </div>

                                        {{-- Registration Number --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Registration Number</label>
                                            <input type="text" name="registration_number" class="form-control"
                                                value="{{ old('registration_number', $company->registration_number ?? '') }}"
                                                placeholder="Enter registration number">
                                        </div>

                                        {{-- Established Date --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Established Date</label>
                                            <input type="date" name="established_date" class="form-control"
                                                value="{{ old('established_date', $company->established_date ?? '') }}">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- RIGHT SIDE --}}
                        <div class="col-lg-4">
                            {{-- Company Logo --}}
                            <div class="card settings-card mb-4">
                                <div class="card-header">
                                    <i class="bi bi-image me-2"></i>
                                    Company Logo
                                </div>

                                <div class="card-body">
                                    <div class="logo-preview-wrapper">
                                        @if(!empty($company->logo))
                                            <img src="{{ asset('backend/dist/assets/img/' . $company->logo) }}"
                                                alt="{{ $company->name }}">
                                        @else
                                            <div class="logo-placeholder" id="logoPlaceholder">
                                                <i class="bi bi-image"></i>
                                                <span>No Logo</span>
                                            </div>
                                            <img src="" id="logoPreview" alt="Logo Preview" style="display:none;">
                                        @endif
                                    </div>

                                    <label class="form-label">Upload Logo</label>
                                    <input type="file" name="logo" id="logoInput" class="form-control"
                                        accept="image/png,image/jpeg,image/jpg,image/webp">
                                    <small class="text-muted d-block mt-2">Recommended: PNG or JPG</small>
                                    <small class="text-muted">Maximum size: 2 MB</small>
                                </div>
                            </div>

                            {{-- Save Button --}}
                            <div class="card settings-card">
                                <div class="card-body">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-save me-1"></i>
                                        Save Company Information
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        document.getElementById('logoInput')?.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (!file) {
                return;
            }
            const reader = new FileReader();

            reader.onload = function (e) {
                const preview = document.getElementById('logoPreview');
                const placeholder = document.getElementById('logoPlaceholder');
                preview.src = e.target.result;
                preview.style.display = 'block';

                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        });
    </script>
@endpush