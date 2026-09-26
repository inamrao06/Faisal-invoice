<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanySettingController extends Controller
{
    public function edit()
    {
        return view('company.settings.edit', ['company' => $this->company()]);
    }

    public function updateProfile(Request $request)
    {
        $company = $this->company();

        $data = $request->validate([
            'name' => ['required', 'max:150'],
            'company_email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'max:50'],
            'website' => ['nullable', 'url', 'max:150'],
            'address' => ['nullable', 'max:1000'],
            'city' => ['nullable', 'max:120'],
            'tax_number' => ['nullable', 'max:80'],
            'invoice_prefix' => ['nullable', 'max:20'],
            'authorized_person' => ['nullable', 'max:150'],
            'designation' => ['nullable', 'max:120'],
            'manager_name' => ['nullable', 'max:150'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'signature' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'stamp' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('company-logos', 'public');
        }
        if ($request->hasFile('signature')) {
            $data['signature_path'] = $request->file('signature')->store('company-signatures', 'public');
        }
        if ($request->hasFile('stamp')) {
            $data['stamp_path'] = $request->file('stamp')->store('company-stamps', 'public');
        }
        unset($data['logo'], $data['signature'], $data['stamp']);

        $company->update($data);

        return redirect()->route('company.settings.edit')->with('success', 'Company details saved.');
    }

    public function updateSmtp(Request $request)
    {
        $company = $this->company();

        $data = $request->validate([
            'smtp_mailer' => ['required', Rule::in(['smtp', 'log'])],
            'smtp_host' => ['nullable', 'max:150'],
            'smtp_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'smtp_username' => ['nullable', 'max:150'],
            'smtp_password' => ['nullable', 'max:150'],
            'smtp_encryption' => ['nullable', Rule::in(['', 'tls', 'ssl'])],
            'smtp_from_address' => ['nullable', 'email', 'max:150'],
            'smtp_from_name' => ['nullable', 'max:150'],
        ]);

        if (blank($data['smtp_password'] ?? null)) {
            unset($data['smtp_password']);
        }

        $company->update($data);

        return redirect()->route('company.settings.edit')->with('success', 'Company SMTP settings saved.');
    }

    private function company(): Branch
    {
        $companyId = auth()->user()->branch_id;
        abort_unless($companyId, 403);

        return Branch::findOrFail($companyId);
    }
}
