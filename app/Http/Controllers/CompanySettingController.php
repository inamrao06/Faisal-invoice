<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Throwable;

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

        return redirect()->to(route('company.settings.edit') . '#tab-smtp')->with('success', 'Company SMTP settings saved.');
    }

    public function testSmtp(Request $request)
    {
        $company = $this->company();
        $returnUrl = route('company.settings.edit') . '#tab-smtp';

        $data = $request->validate([
            'test_email' => ['required', 'email', 'max:150'],
        ]);

        if (($company->smtp_mailer ?? 'smtp') !== 'smtp') {
            return redirect()->to($returnUrl)->withErrors(['test_email' => 'Select SMTP as the company mail driver before sending a test.']);
        }

        if (blank($company->smtp_host) || blank($company->smtp_from_address)) {
            return redirect()->to($returnUrl)->withErrors(['test_email' => 'Save a company SMTP host and from address before testing.']);
        }

        $encryption = $company->smtp_encryption ?: null;
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $company->smtp_host,
            'mail.mailers.smtp.port' => (int) ($company->smtp_port ?: 587),
            'mail.mailers.smtp.username' => $company->smtp_username,
            'mail.mailers.smtp.password' => $company->smtp_password,
            'mail.mailers.smtp.scheme' => $encryption === 'ssl' ? 'smtps' : 'smtp',
            'mail.mailers.smtp.encryption' => $encryption,
            'mail.from.address' => $company->smtp_from_address,
            'mail.from.name' => $company->smtp_from_name ?: $company->name,
        ]);

        try {
            Mail::purge('smtp');
            Mail::mailer('smtp')->raw('This is a test email from ' . $company->name . '.', function ($message) use ($company, $data) {
                $message->from($company->smtp_from_address, $company->smtp_from_name ?: $company->name)
                    ->to($data['test_email'])
                    ->subject('Company SMTP test email');
            });
        } catch (Throwable $exception) {
            report($exception);
            return redirect()->to($returnUrl)->withErrors(['test_email' => 'Test email could not be sent. Check this company SMTP settings and application log.']);
        }

        return redirect()->to($returnUrl)->with('smtp_test_success', 'Test email sent to ' . $data['test_email'] . '.');
    }

    public function updateAccount(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'max:50'],
        ]);

        $user->update($data);

        return redirect()->to(route('company.settings.edit') . '#tab-profile')->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user->update(['password' => $data['password']]);

        return redirect()->to(route('company.settings.edit') . '#tab-password')->with('success', 'Password changed.');
    }

    private function company(): Branch
    {
        $companyId = auth()->user()->branch_id;
        abort_unless($companyId, 403);

        return Branch::findOrFail($companyId);
    }
}
