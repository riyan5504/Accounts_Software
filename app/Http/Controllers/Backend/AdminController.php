<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function adminDashboard()
    {
        $user = auth()->user();

        // company name বের করা
        if ($user->company_id == null) {
            $companyName = 'Admin Panel';
            $users = User::count();
        } else {
            $company = Company::find($user->company_id);
            $companyName = $company->name ?? 'No Company';
            $users = User::where('company_id', $user->company_id)->count();
        }
        $purchases = Purchase::count();

        return view('backend.dashboard', compact('purchases', 'users', 'companyName'));
    }
}
