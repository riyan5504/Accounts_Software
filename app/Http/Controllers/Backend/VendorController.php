<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function vendorAdd()
    {
        return view('vendor.vendor-add');
    }

    public function vendorStore(Request $request)
    {
        $vendor = Vendor::updateOrCreate(
            ['v_name' => $request->v_name, 'phone' => $request->phone],
            [
                'email' => $request->email,
                'address' => $request->address,
            ]
        );
        return redirect('/vendor/list');
    }

    public function vendorList()
    {
        $vendors = Vendor::all();
        return view('vendor.list', compact('vendors'));
    }
    public function vendorEdit($id)
    {
        $vendor = Vendor::find($id);
        return view('vendor.edit', compact('vendor'));
    }
    public function vendorUpdate(Request $request, $id)
    {
        $vendor = Vendor::find($id);
        $vendor->v_name = $request->v_name;
        $vendor->phone = $request->phone;
        $vendor->email = $request->email;
        $vendor->address = $request->address;
        $vendor->save();
        return redirect('/vendor/list');
    }
    public function vendorDelete($id)
    {
        $vendor = Vendor::find($id);
        $vendor->delete();
        return redirect()->back();
    }
}
