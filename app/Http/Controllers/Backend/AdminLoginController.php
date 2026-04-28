<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function register(Request $request)
    {
        $request->validate([
            'company_name' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $company = Company::create([
            'name' => $request->company_name,
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'company_admin',
        ]);

        return redirect()->route('admin.login')->with('success', 'Registration successful');
    }

    public function adminLogOut()
    {
        Auth::logout();
        return redirect('/admin/login');
    }
}
