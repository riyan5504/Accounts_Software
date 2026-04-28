@extends('backend.master')

@section('content')
    <!--begin::App Content-->
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Quick Example-->
            <div class="card card-primary card-outline mb-2">
                <!--begin::Form-->
                <form action="{{ route('account.partner-store') }}" method="POST">
                    @csrf
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--start::partner-->
                        <div id="partnerContainer" class="border-0 shadow-sm ms-0">
                            <div
                                class="bg-success text-white d-flex justify-content-between align-partners-center mb-3 px-1 py-1 rounded">
                                <h4 class="mb-0 ms-1">Partner Details</h4>
                            </div>
                            <!-- ✅ Proper Row Structure -->
                            <div class="row g-2 align-partner-end mb-2">
                                <div class="form-group mb-1 col-md-5">
                                    <input type="text" name="p_name" class="form-control p_name" placeholder=" " required />
                                    <label for="p_name" class="floating-label">Partner Name</label>
                                </div>

                                <div class="form-group mb-1 col-md-3">
                                    <input type="text" name="p_phone" class="form-control p_phone" placeholder=" " required />
                                    <label for="p_phone" class="floating-label">Phone</label>
                                </div>
                                <div class="form-group mb-1 col-md-4">
                                    <input type="email" name="p_email" class="form-control p_email" placeholder=" " required/>
                                    <label for="p_email" class="floating-label">Email</label>
                                </div>
                                <div class="form-group mb-1 col-md-12">
                                    <textarea name="p_address" class="form-control p_address" rows="1" placeholder=" " required></textarea>
                                    <label for="address" class="floating-label">Address</label>
                                </div>
                            </div>
                        </div>
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
    <section class="m-1">
        <div class="card">
            <div class="ps-2 bg-success bg-opacity-50 text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Partner List</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered custom-table">
                    <thead>
                        <tr>
                            <th style="width: 70px">Sl. No.</th>
                            <th style="width: 200px">Name</th>
                            <th style="width: 180px">Phone</th>
                            <th style="width: 210px">Email</th>
                            <th style="width: 300px">Address</th>
                            <th style="text-align:center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($partners as $partner)
                            <tr class="align-middle">
                                <td style="text-align: center">{{ $loop->index + 1 }}</td>
                                <td>{{ $partner->p_name }}</td>
                                <td>{{ $partner->p_phone }}</td>
                                <td>{{ $partner->p_email }}</td>
                                <td>{{ $partner->p_address }}</td>
                                <td style="text-align: center">
                                    <a href="{{ url('/partner/edit/' . $partner->id) }}" class="btn ms-0 me-0">
                                        <i class="bi bi-pencil text-primary"></i>
                                    {{-- </a>
                                    <a href="{{ url('/partner/delete/' . $partner->id) }}" class="btn me-0 ms-0">
                                        <i class="bi bi-trash text-danger"></i>
                                    </a> --}}
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
