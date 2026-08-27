@extends('backend.master')

@section('title', 'Company Settings')

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
        padding: 16px 20px;
        font-weight: 600;
    }

    .settings-card .card-body {
        padding: 25px;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 6px;
    }

    .form-control,
    .form-select {
        min-height: 42px;
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

    .section-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
</style>
@endpush


@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Company Settings</h4>
            <small class="text-muted">
                Manage your company information
            </small>
        </div>
    </div>


    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif


    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following errors:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form action="{{ route('company.settings.update') }}"
          method="POST"
          enctype="multipart/form-data">

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

                        <div class="section-title">
                            Basic Information
                        </div>

                        <div class="row">

                            {{-- Company Name --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Company Name
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       value="{{ old('name', $company->name ?? '') }}"
                                       placeholder="Enter company name"
                                       required>
                            </div>


                            {{-- Short Name --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Short Name
                                </label>

                                <input type="text"
                                       name="short_name"
                                       class="form-control"
                                       value="{{ old('short_name', $company->short_name ?? '') }}"
                                       placeholder="Company short name">
                            </div>


                            {{-- Address --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    Address
                                </label>

                                <textarea name="address"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Enter company address">{{ old('address', $company->address ?? '') }}</textarea>
                            </div>


                            {{-- Phone --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Phone
                                </label>

                                <input type="text"
                                       name="phone"
                                       class="form-control"
                                       value="{{ old('phone', $company->phone ?? '') }}"
                                       placeholder="01XXXXXXXXX">
                            </div>


                            {{-- Email --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Email
                                </label>

                                <input type="email"
                                       name="email"
                                       class="form-control"
                                       value="{{ old('email', $company->email ?? '') }}"
                                       placeholder="company@example.com">
                            </div>


                            {{-- Website --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Website
                                </label>

                                <input type="url"
                                       name="website"
                                       class="form-control"
                                       value="{{ old('website', $company->website ?? '') }}"
                                       placeholder="https://example.com">
                            </div>


                            {{-- Tax ID --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Tax / BIN Number
                                </label>

                                <input type="text"
                                       name="tax_number"
                                       class="form-control"
                                       value="{{ old('tax_number', $company->tax_number ?? '') }}"
                                       placeholder="Enter Tax / BIN number">
                            </div>


                            {{-- Registration Number --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Registration Number
                                </label>

                                <input type="text"
                                       name="registration_number"
                                       class="form-control"
                                       value="{{ old('registration_number', $company->registration_number ?? '') }}"
                                       placeholder="Enter registration number">
                            </div>


                            {{-- Established Date --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Established Date
                                </label>

                                <input type="date"
                                       name="established_date"
                                       class="form-control"
                                       value="{{ old('established_date', $company->established_date ?? '') }}">
                            </div>

                        </div>

                    </div>
                </div>


                {{-- Contact / Other Information --}}
                <div class="card settings-card mb-4">

                    <div class="card-header">
                        <i class="bi bi-info-circle me-2"></i>
                        Additional Information
                    </div>

                    <div class="card-body">

                        <div class="row">

                            {{-- Contact Person --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Contact Person
                                </label>

                                <input type="text"
                                       name="contact_person"
                                       class="form-control"
                                       value="{{ old('contact_person', $company->contact_person ?? '') }}"
                                       placeholder="Contact person name">
                            </div>


                            {{-- Mobile --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Mobile
                                </label>

                                <input type="text"
                                       name="mobile"
                                       class="form-control"
                                       value="{{ old('mobile', $company->mobile ?? '') }}"
                                       placeholder="Mobile number">
                            </div>


                            {{-- Currency --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Currency
                                </label>

                                <select name="currency"
                                        class="form-select">

                                    <option value="BDT"
                                        {{ old('currency', $company->currency ?? 'BDT') == 'BDT' ? 'selected' : '' }}>
                                        BDT - Bangladeshi Taka
                                    </option>

                                    <option value="USD"
                                        {{ old('currency', $company->currency ?? '') == 'USD' ? 'selected' : '' }}>
                                        USD - US Dollar
                                    </option>

                                    <option value="EUR"
                                        {{ old('currency', $company->currency ?? '') == 'EUR' ? 'selected' : '' }}>
                                        EUR - Euro
                                    </option>

                                </select>
                            </div>


                            {{-- Timezone --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Timezone
                                </label>

                                <select name="timezone"
                                        class="form-select">

                                    <option value="Asia/Dhaka"
                                        {{ old('timezone', $company->timezone ?? 'Asia/Dhaka') == 'Asia/Dhaka' ? 'selected' : '' }}>
                                        Asia/Dhaka
                                    </option>

                                </select>
                            </div>


                            {{-- Footer Text --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    Invoice / Report Footer
                                </label>

                                <textarea name="footer_text"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Example: Thank you for doing business with us.">{{ old('footer_text', $company->footer_text ?? '') }}</textarea>
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
                                <img src="{{ asset('storage/' . $company->logo) }}"
                                     id="logoPreview"
                                     alt="Company Logo">
                            @else

                                <div class="logo-placeholder"
                                     id="logoPlaceholder">

                                    <i class="bi bi-image"></i>
                                    <span>No Logo</span>

                                </div>

                                <img src=""
                                     id="logoPreview"
                                     alt="Logo Preview"
                                     style="display:none;">

                            @endif

                        </div>


                        <label class="form-label">
                            Upload Logo
                        </label>

                        <input type="file"
                               name="logo"
                               id="logoInput"
                               class="form-control"
                               accept="image/png,image/jpeg,image/jpg,image/webp">

                        <small class="text-muted d-block mt-2">
                            Recommended: PNG or JPG
                        </small>

                        <small class="text-muted">
                            Maximum size: 2 MB
                        </small>

                    </div>

                </div>


                {{-- Company Status --}}
                <div class="card settings-card mb-4">

                    <div class="card-header">
                        <i class="bi bi-toggle-on me-2"></i>
                        Company Status
                    </div>

                    <div class="card-body">

                        <div class="form-check form-switch">

                            <input class="form-check-input"
                                   type="checkbox"
                                   name="status"
                                   value="1"
                                   id="status"
                                   {{ old('status', $company->status ?? 1) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="status">
                                Active Company
                            </label>

                        </div>

                    </div>

                </div>


                {{-- Save Button --}}
                <div class="card settings-card">

                    <div class="card-body">

                        <button type="submit"
                                class="btn btn-primary w-100">

                            <i class="bi bi-save me-1"></i>
                            Save Company Settings

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </form>

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