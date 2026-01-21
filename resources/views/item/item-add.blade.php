@extends('backend.master')

@section('content')
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0 mt-0">Add Item</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/purchase') }}"
                                class="{{ request()->is('purchase') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Purchase
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/purchase/entry') }}"
                                class="{{ request()->is('purchase/entry') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Entry
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/purchase/list') }}"
                                class="{{ request()->is('purchase/list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                List
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/item/list') }}"
                                class="{{ request()->is('item/list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Item List
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/vendor/list') }}"
                                class="{{ request()->is('vendor/list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Vendor List
                            </a>
                        </li>
                    </ol>
                </div>

            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content Header-->
    <!--begin::App Content-->
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Quick Example-->
            <div class="card card-primary card-outline mb-2">
                <!--begin::Header-->
                <div class="card-header">
                    <div class="card-title">Add Item Informetion</div>
                </div>
                <!--end::Header-->
                <!--begin::Form-->
                <form action="{{ url('/item/store') }}" method="POST">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                        <div id="itemsContainer" class="border-0 shadow-sm">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-items-center mb-3 px-1 py-1 rounded">
                                <h4 class="mb-0 ms-1">Item Details</h4>
                            </div>
                            <div class="item-row row g-2 align-items-end mb-2">
                                <div class="form-group col-sm-6 col-md-4 mb-1">
                                    <input type="text" name="item_name" class="form-control item_name" placeholder=" " required />
                                    <label for="item_name" class="floating-label">Item Name</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-4 mb-1">
                                    <input type="text" name="item_code" class="form-control item_code" placeholder=" "/>
                                    <label for="item_code" class="floating-label">Item Code</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-4 mb-1">
                                    <input type="text" name="cat_name" class="form-control cat_name" placeholder=" " required />
                                    <label for="cat_id" class="floating-label">Category</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-4 mb-1">
                                    <input type="text" name="size" class="form-control size" placeholder=" "/>
                                    <label for="size" class="floating-label">Pack Size</label>
                                </div>
                                <div class="form-group col-sm-6 col-md-4 mb-1">
                                    <input type="number" step="0.01" name="unit_price" class="form-control unit_price" placeholder=" " required />
                                    <label for="unit_price" class="floating-label">Unit Price</label>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                        <!--begin::Footer-->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                        <!--end::Footer-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Quick Example-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content-->
@endsection
@push('script')
    <script>
        $(document).ready(function() {

            const container = $('#itemsContainer');
            let currentSerial = {{ $lastSerial + 1 }}; // Database থেকে last serial

            function generateItemCode(itemName) {
                if (!itemName) return '';

                const words = itemName.trim().split(/\s+/);
                let prefix = '';

                // Default prefix 2 letters
                if (words.length >= 2) {
                    prefix = (words[0][0] + words[1][0]).toUpperCase();
                } else if (words[0].length >= 2) {
                    prefix = words[0].substring(0, 2).toUpperCase();
                } else {
                    prefix = words[0][0].toUpperCase() + 'X';
                }

                // Existing prefixes
                let existingPrefixes = [];
                $('.item_code').each(function() {
                    const val = $(this).val();
                    if (val && val.length >= 2) {
                        existingPrefixes.push(val.substring(0, 2).toUpperCase());
                    }
                });

                // Prefix uniqueness check
                if (existingPrefixes.includes(prefix)) {
                    const name = words.join('').toUpperCase();
                    for (let i = 1; i < name.length; i++) {
                        const newPrefix = prefix[0] + name[i];
                        if (!existingPrefixes.includes(newPrefix)) {
                            prefix = newPrefix;
                            break;
                        }
                    }
                }

                return prefix + String(currentSerial).padStart(2, '0');
            }

            function attachRowListeners(row) {
                const itemNameInput = row.find('.item_name');
                const itemCodeInput = row.find('.item_code');
                const catNameInput = row.find('.cat_name');
                const sizeInput = row.find('.size');
                const priceField = row.find('.unit_price');

                row.data('isExistingItem', false);

                // Autocomplete
                itemNameInput.autocomplete({
                    source: "{{ route('item.search') }}",
                    minLength: 1,
                    select: function(event, ui) {
                        if (ui.item && ui.item.id) {
                            itemNameInput.val(ui.item.value);
                            itemCodeInput.val(ui.item.item_code);
                            catNameInput.val(ui.item.cat_name);
                            sizeInput.val(ui.item.size);
                            priceField.val(ui.item.unit_price);

                            row.data('isExistingItem', true);
                        }
                    },
                    response: function(event, ui) {
                        // যদি কিছু না পাওয়া যায় → new item
                        if (ui.content.length === 0) {
                            const val = $(this).val().trim();
                            if (val && !row.data('isExistingItem')) {
                                const code = generateItemCode(val);
                                itemCodeInput.val(code);
                            }
                        }
                    },
                    open: function() {
                        row.data('isExistingItem', false);
                    }
                });

                // Input change → clear old fields এবং generate new code
                itemNameInput.on('input', function() {
                    const val = $(this).val().trim();

                    if (!row.data('isExistingItem')) {
                        catNameInput.val('');
                        sizeInput.val('');
                        unitInput.prop('selectedIndex', 0);
                        priceField.val('');
                    }

                    if (val && !row.data('isExistingItem')) {
                        const code = generateItemCode(val);
                        itemCodeInput.val(code);
                    } else if (!val) {
                        itemCodeInput.val('');
                    }
                });
            }

            // First row attach
            attachRowListeners(container.find('.item-row').first());

            // Add new row
            $('#addItem').on('click', function() {
                const newRow = container.find('.item-row').first().clone();
                newRow.find('input').val('');
                newRow.find('select').prop('selectedIndex', 0);
                container.append(newRow);

                currentSerial++; // Increment serial only on new row
                attachRowListeners(newRow);
            });
        });
    </script>
@endpush
