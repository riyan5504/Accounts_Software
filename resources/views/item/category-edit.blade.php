@extends('backend.master')

@section('content')
    <!--begin::App Content-->
    <div class="app-content m-1">
        <div class="card card-primary card-outline">
            <!--begin::Form-->
            <form action="{{ route('item.category-update', $category->id) }}" method="POST">
                @csrf
                <!--begin::Body-->
                <div class="card-body">
                    <div id="itemsContainer" class="border-0 shadow-sm">
                        <div
                            class="bg-success text-white d-flex justify-content-between align-items-center mb-3 px-1 py-1 rounded">
                            <h4 class="mb-0 ms-1">Edit Category Details</h4>
                        </div>
                        <div class="align-items-end">
                            <div class="form-group mb-1">
                                <input type="text" name="cat_name" value="{{$category->cat_name}}" class="form-control cat_name" placeholder=" "
                                    required />
                                <label for="cat_name" class="floating-label">Category Name</label>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                    <!--end::Footer-->
            </form>
            <!--end::Form-->
        </div>
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
                                    <a href="{{ route('item.category-edit', $category->id) }}" class="btn ms-0 me-0">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </a>
                                    <a href="{{ route('item.category-delete', $category->id) }}" class="btn me-0 ms-0">
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
@endsection
