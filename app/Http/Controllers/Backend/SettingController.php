<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function settings()
    {
        return view('settings.setting-module');
    }

    public function company()
    {
        $user = Auth::user();

        if (!$user || !$user->company_id) {
            abort(403, 'Company not assigned to this user.');
        }

        $company = Company::findOrFail($user->company_id);

        return view('settings.company-info', compact('company'));
    }

    public function companyUpdate(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->company_id) {
            abort(403, 'Company not assigned to this user.');
        }

        $company = Company::findOrFail($user->company_id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255',],
            'short_name' => ['required', 'string', 'max:255',],
            'email' => ['nullable', 'email', 'max:255',],
            'phone' => ['nullable', 'string', 'max:30',],
            'address' => ['nullable', 'string', 'max:1000',],
            'website' => ['nullable', 'url', 'max:255',],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048',],
            'tax_number' => ['nullable', 'string', 'max:100',],
            'registration_number' => ['nullable', 'string', 'max:100',],
            'contact_person' => ['nullable', 'string', 'max:150',],
            'established_date' => ['nullable', 'date',],
        ]);


        // Logo Upload
        if ($request->hasFile('logo')) {

            // Delete old logo
            if ($company->logo) {
                $oldLogo = public_path('backend/dist/assets/img/' . $company->logo);

                if (File::exists($oldLogo)) {
                    File::delete($oldLogo);
                }
            }

            // Generate unique filename
            $logoName = 'company_logo_' . time() . '_' . Str::random(10) . '.' .
                $request->file('logo')->getClientOriginalExtension();

            // Move logo to public directory
            $request->file('logo')->move(
                public_path('backend/dist/assets/img'),
                $logoName
            );

            // Save filename in database
            $validated['logo'] = $logoName;
        }

        $company->update($validated);

        return redirect()
            ->route('settings.company.info')
            ->with('success', 'Company settings updated successfully.');
    }
}
