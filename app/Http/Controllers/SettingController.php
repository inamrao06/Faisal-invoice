<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Throwable;

class SettingController extends Controller
{
    /* ─────────────────────────────────────────────────────────────
     | GENERAL SETTINGS  (tabs: App Info, Mail, ENV)
     ─────────────────────────────────────────────────────────────*/

    public function general()
    {
        $settings = Setting::pluck('value', 'key');
        $env      = $this->readEnv();

        return view('settings.general', compact('settings', 'env'));
    }

    /* Tab 1 – App Info (logo, site name, contact) */
    public function updateGeneral(Request $request)
    {
        $data = $request->validate([
            'site_name'   => ['required', 'max:120'],
            'slug'        => ['required', 'max:120'],
            'number'      => ['nullable', 'max:40'],
            'email'       => ['nullable', 'email', 'max:120'],
            'address'     => ['nullable', 'max:255'],
            'tagline'     => ['nullable', 'max:200'],
            'logo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'favicon'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,ico', 'max:512'],
            'timezone'    => ['nullable', 'max:60'],
            'date_format' => ['nullable', 'max:30'],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('site-logos', 'public');
        }
        if ($request->hasFile('favicon')) {
            $data['favicon_path'] = $request->file('favicon')->store('site-logos', 'public');
        }
        unset($data['logo'], $data['favicon']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Setting::updateOrCreate(['key' => 'business_name'], ['value' => $data['site_name']]);

        // Sync APP_NAME in .env
        $this->writeEnv(['APP_NAME' => '"' . $data['site_name'] . '"']);

        return back()->with('success', 'App information saved.');
    }

    /* Tab 2 – Mail Settings */
    public function updateMail(Request $request)
    {
        $data = $request->validate([
            'mail_mailer'       => ['required', Rule::in(['smtp', 'log'])],
            'mail_host'         => ['nullable', 'max:120'],
            'mail_port'         => ['nullable', 'numeric'],
            'mail_username'     => ['nullable', 'max:120'],
            'mail_password'     => ['nullable', 'max:120'],
            'mail_encryption'   => ['nullable', Rule::in(['', 'tls', 'ssl'])],
            'mail_from_address' => ['nullable', 'email', 'max:120'],
            'mail_from_name'    => ['nullable', 'max:120'],
        ]);

        $envMap = [
            'mail_mailer'       => 'MAIL_MAILER',
            'mail_host'         => 'MAIL_HOST',
            'mail_port'         => 'MAIL_PORT',
            'mail_username'     => 'MAIL_USERNAME',
            'mail_password'     => 'MAIL_PASSWORD',
            'mail_encryption'   => 'MAIL_ENCRYPTION',
            'mail_from_address' => 'MAIL_FROM_ADDRESS',
            'mail_from_name'    => 'MAIL_FROM_NAME',
        ];

        $envUpdates = [];
        foreach ($data as $key => $value) {
            if (isset($envMap[$key])) {
                $envUpdates[$envMap[$key]] = str_contains((string) $value, ' ') ? '"' . $value . '"' : $value;
            }
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $envUpdates['MAIL_SCHEME'] = ($data['mail_encryption'] ?? '') === 'ssl' ? 'smtps' : 'smtp';
        $this->writeEnv($envUpdates);
        Artisan::call('config:clear');

        return redirect()->to(route('settings.general') . '#tab-mail')->with('success', 'Mail settings saved.');
    }

    public function testMail(Request $request)
    {
        $returnUrl = route('settings.general') . '#tab-mail';
        $validator = Validator::make($request->all(), [
            'test_email' => ['required', 'email', 'max:120'],
        ]);
        if ($validator->fails()) {
            return redirect()->to($returnUrl)->withErrors($validator)->withInput();
        }
        $recipient = $validator->validated()['test_email'];
        $settings = Setting::pluck('value', 'key');

        if (($settings['mail_mailer'] ?? config('mail.default')) !== 'smtp') {
            return redirect()->to($returnUrl)->withErrors(['test_email' => 'Save SMTP as the mail driver before sending a test.']);
        }

        $host = $settings['mail_host'] ?? config('mail.mailers.smtp.host');
        $fromAddress = $settings['mail_from_address'] ?? config('mail.from.address');
        if (! $host || ! $fromAddress) {
            return redirect()->to($returnUrl)->withErrors(['test_email' => 'Save an SMTP host and from address first.']);
        }

        $encryption = $settings['mail_encryption'] ?? '';
        config([
            'mail.mailers.smtp.host' => $host,
            'mail.mailers.smtp.port' => (int) ($settings['mail_port'] ?? config('mail.mailers.smtp.port')),
            'mail.mailers.smtp.username' => $settings['mail_username'] ?? config('mail.mailers.smtp.username'),
            'mail.mailers.smtp.password' => $settings['mail_password'] ?? config('mail.mailers.smtp.password'),
            'mail.mailers.smtp.scheme' => $encryption === 'ssl' ? 'smtps' : 'smtp',
        ]);

        try {
            Mail::purge('smtp');
            Mail::mailer('smtp')->raw('This is a test email from ' . ($settings['site_name'] ?? config('app.name')) . '.', function ($message) use ($recipient, $fromAddress, $settings) {
                $message->from($fromAddress, $settings['mail_from_name'] ?? config('mail.from.name'))
                    ->to($recipient)
                    ->subject('SMTP test email');
            });
        } catch (Throwable $exception) {
            report($exception);
            return redirect()->to($returnUrl)->withErrors(['test_email' => 'Test email could not be sent. Check the SMTP settings and application log.']);
        }

        return redirect()->to($returnUrl)->with('mail_test_success', 'Test email sent to ' . $recipient . '.');
    }

    /* Tab 3 – ENV Settings (App URL, debug, env) */
    public function updateEnv(Request $request)
    {
        $data = $request->validate([
            'app_url'       => ['required', 'url', 'max:200'],
            'app_env'       => ['required', Rule::in(['local', 'staging', 'production'])],
            'app_debug'     => ['boolean'],
            'app_timezone'  => ['nullable', 'max:60'],
            'app_locale'    => ['nullable', 'max:10'],
        ]);

        $this->writeEnv([
            'APP_URL'      => $data['app_url'],
            'APP_ENV'      => $data['app_env'],
            'APP_DEBUG'    => $request->boolean('app_debug') ? 'true' : 'false',
            'APP_TIMEZONE' => $data['app_timezone'] ?? 'UTC',
            'APP_LOCALE'   => $data['app_locale'] ?? 'en',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Artisan::call('config:clear');
        return back()->with('success', 'Application environment settings saved.');
    }

    /* ─────────────────────────────────────────────────────────────
     | THEME
     ─────────────────────────────────────────────────────────────*/

    public function theme()
    {
        return view('settings.theme', ['settings' => Setting::pluck('value', 'key')]);
    }

    public function updateTheme(Request $request)
    {
        $data = $request->validate([
            'theme_mode'    => ['required', Rule::in(['light', 'dark'])],
            'header_color'  => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'sidebar_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'accent_color'  => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'active_color'  => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'border_color'  => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'body_bg_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'card_bg_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'text_color'    => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'muted_color'   => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'input_bg_color'=> ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Theme settings updated.');
    }

    /* ─────────────────────────────────────────────────────────────
     | HELPERS
     ─────────────────────────────────────────────────────────────*/

    /** Read .env file into associative array */
    public function readEnv(): array
    {
        $path = base_path('.env');
        if (! file_exists($path)) return [];

        $lines  = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $result = [];

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            if (! str_contains($line, '='))          continue;

            [$key, $val] = explode('=', $line, 2);
            $result[trim($key)] = trim(trim($val), '"\'');
        }

        return $result;
    }

    /** Write / overwrite specific keys in .env */
    private function writeEnv(array $updates): void
    {
        $path = base_path('.env');
        if (! file_exists($path)) return;

        $content = file_get_contents($path);

        foreach ($updates as $key => $value) {
            $pattern = '/^' . preg_quote($key, '/') . '=.*/m';

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $key . '=' . $value, $content);
            } else {
                $content .= PHP_EOL . $key . '=' . $value;
            }
        }

        file_put_contents($path, $content);
    }
}
