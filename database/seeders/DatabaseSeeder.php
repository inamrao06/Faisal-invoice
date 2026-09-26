<?php
namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchType;
use App\Models\Currency;
use App\Models\User;
use App\Models\ExpenseHead;
use App\Models\PaymentMethod;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Company Types ──────────────────────────────────────────
        foreach ([
            'car_sale_purchase' => 'Car Sales / Purchase',
            'service_booking'   => 'Service Booking / Workshop',
            'pos'               => 'POS / Parts Store',
        ] as $code => $name) {
            BranchType::firstOrCreate(['code' => $code], ['name' => $name, 'is_active' => true]);
        }

        // ── Currencies ─────────────────────────────────────────────
        foreach ([
            ['PKR', 'Pakistani Rupee', 'Rs',  true],
            ['USD', 'US Dollar',       '$',   false],
            ['AED', 'UAE Dirham',      'AED', false],
        ] as [$code, $name, $symbol, $default]) {
            Currency::firstOrCreate(['code' => $code], [
                'name' => $name, 'symbol' => $symbol,
                'is_default' => $default, 'is_active' => true,
            ]);
        }

        $currencyId = Currency::where('is_default', true)->value('id');

        // ── Demo Company (Branch) ──────────────────────────────────
        $branch = Branch::firstOrCreate(['code' => 'HO'], [
            'name'               => 'Head Office Motors',
            'type'               => 'service_booking',
            'currency_id'        => $currencyId,
            'company_email'      => 'info@headofficemotors.com',
            'email'              => 'contact@headofficemotors.com',
            'phone'              => '+92 300 0000000',
            'city'               => 'Multan',
            'address'            => '123 Main Boulevard, Multan, Pakistan',
            'authorized_person'  => 'Muhammad Ali',
            'designation'        => 'Chief Executive Officer',
            'manager_name'       => 'Ahmad Khan',
            'website'            => 'https://headofficemotors.com',
            'tax_number'         => 'NTN-1234567',
            'invoice_prefix'     => 'HO',
            'is_active'          => true,
        ]);

        // ── Super Admin (no company) ───────────────────────────────
        User::firstOrCreate(['email' => 'superadmin@example.com'], [
            'name'             => 'Super Admin',
            'password'         => 'password',
            'role'             => 'super_admin',
            'user_type'        => 'super_admin',
            'company_role_num' => 0,
            'branch_id'        => null,
            'is_active'        => true,
        ]);

        // ── Company Admin — full access (role = 0) ─────────────────
        User::firstOrCreate(['email' => 'admin@example.com'], [
            'name'             => 'System Administrator',
            'password'         => 'password',
            'role'             => 'admin',
            'user_type'        => 'admin',
            'company_role_num' => 0,   // 0 = full access
            'branch_id'        => $branch->id,
            'is_active'        => true,
        ]);

        // ── Expense Heads ──────────────────────────────────────────
        foreach ([
            'Office Expense', 'Utilities', 'Fuel and Transport',
            'Repair and Maintenance', 'Salary and Wages',
            'Marketing', 'Rent', 'Other Expense',
        ] as $i => $name) {
            ExpenseHead::firstOrCreate(
                ['code' => 'EXP-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT)],
                ['name' => $name, 'is_active' => true]
            );
        }

        // ── Payment Methods ────────────────────────────────────────
        foreach ([
            'cash'          => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'cheque'        => 'Cheque',
            'card'          => 'Card',
            'online'        => 'Online',
            'credit'        => 'Credit',
        ] as $code => $name) {
            PaymentMethod::firstOrCreate(['code' => $code], ['name' => $name, 'is_active' => true]);
        }

        // ── App Settings ───────────────────────────────────────────
        foreach ([
            'business_name'  => 'BookingPro',
            'site_name'      => 'BookingPro',
            'currency'       => 'PKR',
            'theme_mode'     => 'light',
            'header_color'   => '#ffffff',
            'sidebar_color'  => '#0f172a',
            'accent_color'   => '#2563eb',
        ] as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        // ── Notification Templates ─────────────────────────────────
        foreach (\App\Models\NotificationTemplate::defaults() as $tpl) {
            \App\Models\NotificationTemplate::firstOrCreate(
                ['event_key' => $tpl['event_key']],
                array_diff_key($tpl, ['event_key' => null])
            );
        }
    }
}
