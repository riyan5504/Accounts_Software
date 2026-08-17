<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth');
    }

    public function customerAdd()
    {
        $customers = Customer::latest()->get();
        return view('customer.customer-add', compact('customers'));
    }

    public function customerStore(Request $request)
    {
        $customer = Customer::updateOrCreate(
            [
                'company_id' => auth()->user()->company_id,
                'phone' => $request->phone
            ],
            [
                'c_name' => $request->c_name,
                'email' => $request->email,
                'address' => $request->address,
                'opening_balance' => $request->opening_balance ?? 0,
            ]
        );
        return back()->with('success', 'Customer Details saved successfully!');
    }

    public function customerList()
    {
        $customers = Customer::all();
        return view('customer.list', compact('customers'));
    }
    public function customerEdit($id)
    {
        $customers = Customer::latest()->get();
        $customer = Customer::find($id);
        return view('customer.edit', compact('customer', 'customers'));
    }
    public function customerUpdate(Request $request, $id)
    {
        $customer = Customer::find($id);
        $customer->c_name = $request->c_name;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->opening_balance = $request->opening_balance ?? 0;
        $customer->save();
        return redirect('sales/customer/add')->with('success', 'Customer Details Updated successfully!');
    }
    public function customerDelete($id)
    {
        $customer = Customer::find($id);
        $customer->delete();
        return back()->with('success', 'Customer Delete successfully!');
    }
}
