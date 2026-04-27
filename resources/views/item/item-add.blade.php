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
                                Purchase Entry
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/purchase/list') }}"
                                class="{{ request()->is('purchase/list') ? 'text-primary fw-bold' : 'text-dark' }}">
                                Purchase List
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ url('/purchase/vendor/list') }}"
                                class="{{ request()->is('/purchase/vendor/list') ? 'text-primary fw-bold' : 'text-dark' }}">
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
            <div class="card card-primary card-outline">
                <!--begin::Form-->
                <form action="{{ route('item.item-store') }}" method="POST">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                        <div id="itemsContainer" class="border-0 shadow-sm">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-items-center mb-3 px-1 py-1 rounded">
                                <h4 class="mb-0 ms-1">Item Details</h4>
                            </div>
                            <div class="item-row row g-2 align-items-end">
                                <div class="form-group col-sm-4 col-md-2 mb-1">
                                    <input type="text" name="item_name" class="form-control item_name" placeholder=" "
                                        required />
                                    <label for="item_name" class="floating-label">Item Name</label>
                                </div>
                                <div class="form-group col-sm-4 col-md-2 mb-1">
                                    <input type="text" name="item_code" class="form-control item_code" placeholder=" " />
                                    <label for="item_code" class="floating-label">Item Code</label>
                                </div>
                                <div class="form-group col-sm-4 col-md-2 mb-1 position-relative">
                                    <input type="text" name="cat_name" class="form-control cat_name" id="cat_name"
                                        placeholder=" " required />
                                    <label for="cat_id" class="floating-label">Category</label>

                                    <input type="hidden" name="cat_id" class="cat_id" id="cat_id">
                                    <button class="add-btn" type="button" data-bs-toggle="modal"
                                        data-bs-target="#addCategoryModal">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <div class="form-group col-sm-4 col-md-2 mb-1">
                                    <input type="text" name="size" class="form-control size" placeholder=" " />
                                    <label for="size" class="floating-label">Pack Size</label>
                                </div>
                                <div class="form-group col-sm-4 col-md-2 mb-1">
                                    <input type="number" step="0.01" name="unit_price" class="form-control unit_price"
                                        placeholder=" " required />
                                    <label for="unit_price" class="floating-label">Unit Price</label>
                                </div>
                                <div class="form-group col-sm-4 col-md-2 mb-1">
                                    <input type="number" name="opening_stock" class="form-control opening_stock"
                                        placeholder=" "/>
                                    <label for="opening_stock" class="floating-label">Opening Stock Qty</label>
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
    <section class="mt-1">
        <div class="card">
            <div class="ps-2 bg-success bg-opacity-50 text-dark">
                <h5>Category List</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered custom-table">
                    <thead>
                        <tr>
                            <th style="width: 70px">Sl. No.</th>
                            <th style="width: 120px">Code</th>
                            <th style="width: 200px">Name</th>
                            <th style="width: 130px">Category</th>
                            <th style="width: 130px">Pack Size</th>
                            <th style="width: 130px">Unit Price</th>
                            <th style="width: 130px">Opening Qty</th>
                            <th style="text-align:center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr class="align-middle">
                                <td style="text-align: center">{{ $loop->index + 1 }}</td>
                                <td>{{ $item->item_code }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ optional($item->category)->cat_name }}</td>
                                <td>{{ $item->size }}</td>
                                <td>{{ $item->unit_price }}</td>
                                <td>{{ $item->opening_stock }}</td>
                                <td style="text-align: center">
                                    <a href="{{ url('/item/edit/' . $item->id) }}" class="btn ms-0 me-0">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </a>
                                    <a href="{{ url('/item/delete/' . $item->id) }}" class="btn me-0 ms-0">
                                        <i class="bi bi-trash text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No Data Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
    </section>
    <!--end::App Content-->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="{{ route('item.category-store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Add Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body form-group mb-1">
                        <input type="text" name="cat_name" class="form-control cat_name" placeholder=" " />
                        <label for="cat_id" class="floating-label ps-3">Category</label>
                    </div>

                    @error('cat_name')
                        <small class="text-danger p-4">{{ $message }}</small>
                    @enderror

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
@push('script')
    @if ($errors->has('cat_name'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
                myModal.show();
            });
        </script>
    @endif
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
    <script>
        $('#cat_name').autocomplete({
            source: "{{ route('category.search') }}",
            minLength: 1,
            select: function(event, ui) {
                $('#cat_name').val(ui.item.value);
                $('#cat_id').val(ui.item.id);
                return false;
            }
        });
    </script>
@endpush
