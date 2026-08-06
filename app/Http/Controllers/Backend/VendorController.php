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
        $vendors = Vendor::latest()->get();
        return view('vendor.vendor-add', compact('vendors'));
    }

    public function vendorStore(Request $request)
    {
        $vendor = Vendor::updateOrCreate(
            [
                'company_id' => auth()->user()->company_id,
                'phone' => $request->phone
            ],
            [
                'v_name' => $request->v_name,
                'email' => $request->email,
                'address' => $request->address,
                'opening_balance' => $request->opening_balance ?? 0,
            ]
        );
        return back()->with('success', 'Vendor Details saved successfully!');
    }

    public function vendorList()
    {
        $vendors = Vendor::all();
        return view('vendor.list', compact('vendors'));
    }
    public function vendorEdit($id)
    {
        $vendors = Vendor::latest()->get();
        $vendor = Vendor::find($id);
        return view('vendor.edit', compact('vendor', 'vendors'));
    }
    public function vendorUpdate(Request $request, $id)
    {
        $vendor = Vendor::find($id);
        $vendor->v_name = $request->v_name;
        $vendor->phone = $request->phone;
        $vendor->email = $request->email;
        $vendor->address = $request->address;
        $vendor->opening_balance = $request->opening_balance ?? 0;
        $vendor->save();
        return redirect('/purchase/vendor/add')->with('success', 'Vendor Details Updated successfully!');
    }
    public function vendorDelete($id)
    {
        $vendor = Vendor::find($id);
        $vendor->delete();
        return back()->with('success', 'Vendor Delete successfully!');
    }
}
