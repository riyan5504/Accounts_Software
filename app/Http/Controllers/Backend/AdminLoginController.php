<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function adminLogin()
    {
        return view('backend.login');
    }
    public function adminLogOut()
    {
        Auth::logout();
        return redirect('/admin/login');
    }
}
