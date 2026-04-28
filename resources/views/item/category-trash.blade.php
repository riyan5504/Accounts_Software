@extends('backend.master')

@section('content')
    <!--begin::App Content-->
    <div class="app-content m-1">
        <section class="mt-1">
            <div class="card">
                <div class="ps-2 bg-success bg-opacity-50 text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Category List</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-bordered custom-table">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Sl. No.</th>
                                <th style="width: 700px">Category Name</th>
                                <th style="text-align: center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr class="align-middle">
                                    <td style="text-align: center">{{ $loop->index + 1 }}</td>
                                    <td>{{ $category->cat_name }}</td>
                                    <td style="text-align: center">
                                        <form action="{{ route('item.category-restore', $category->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('item.category-forceDelete', $category->id) }}" method="POST"
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
@endsection
