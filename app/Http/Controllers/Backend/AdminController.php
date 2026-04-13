<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
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

        if ($user->is_admin) {
            $users = User::count(); // all companies
        } else {
            $users = User::where('company_id', $user->company_id)->count();
        }
        $purchases = Purchase::count();
        return view('backend.dashboard', compact('purchases', 'users'));
    }
}
