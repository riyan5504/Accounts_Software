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

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            $user = Auth::user();

            session([
                'company_id' => $user->company_id,
                'role' => $user->role
            ]);

            return redirect('/admin/dashboard');
        }

        return back()->with('error', 'Invalid login credentials');
    }

    public function adminLogOut()
    {
        Auth::logout();
        return redirect('/admin/login');
    }
}
