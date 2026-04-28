@extends('backend.master')

@section('content')
    <!--begin::App Content-->
    <div class="app-content">
        <section class="mt-1">
            <div class="card">
                <div class="ps-2 bg-success bg-opacity-50 text-dark">
                    <h5>Item List</h5>
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
                                        <form action="{{ route('item.restore', $item->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('item.forceDelete', $item->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
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
    </div>

    <!--end::App Content-->
@endsection
