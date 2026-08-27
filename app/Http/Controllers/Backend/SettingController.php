<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function settings()
    {
        return view('setting.setting-module');
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
            'email' => ['nullable', 'email', 'max:255',],
            'phone' => ['nullable', 'string', 'max:30',],
            'mobile' => ['nullable', 'string', 'max:30',],
            'address' => ['nullable', 'string', 'max:1000',],
            'website' => ['nullable', 'url', 'max:255',],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048',],
            'tax_number' => ['nullable', 'string', 'max:100',],
            'registration_number' => ['nullable', 'string', 'max:100',],
            'contact_person' => ['nullable', 'string', 'max:150',],
            'currency' => ['required', 'string', 'max:10',],
            'timezone' => ['required', 'string', 'max:100',],
            'footer_text' => ['nullable', 'string', 'max:2000',],
            'status' => ['nullable', 'boolean',],
        ]);


        /*Logo Upload*/
        if ($request->hasFile('logo')) {
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $logoPath = $request->file('logo')->store(
                'company/logos',
                'public'
            );

            $validated['logo'] = $logoPath;
        }

        $validated['status'] = $request->has('status');

        $company->update($validated);

        return redirect()
            ->route('company.settings')
            ->with('success', 'Company settings updated successfully.');
    }
}
