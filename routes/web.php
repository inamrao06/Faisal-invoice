<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyCarSpecificationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyCustomerController;
use App\Http\Controllers\CompanyDashboardController;
use App\Http\Controllers\CompanyEmployeeController;
use App\Http\Controllers\CompanyExpenseController;
use App\Http\Controllers\CompanyExpenseReportController;
use App\Http\Controllers\CompanyExpenseTypeController;
use App\Http\Controllers\CompanyPaymentMethodController;
use App\Http\Controllers\CompanyPermissionController;
use App\Http\Controllers\CompanyRoleController;
use App\Http\Controllers\CompanySalaryController;
use App\Http\Controllers\CompanySalaryReportController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\CompanyTemplateController;
use App\Http\Controllers\CompanyTypeController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\CompanyVehicleCategoryController;
use App\Http\Controllers\CompanyVehicleController;
use App\Http\Controllers\CompanyVehicleSaleInvoiceController;
use App\Http\Controllers\CompanyWarrantyDurationController;
use App\Http\Controllers\CompanyWarrantyProviderController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImpersonationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::get('/public/vehicle-invoices/{vehicle_invoice}/print', [CompanyVehicleSaleInvoiceController::class, 'publicPrint'])->name('public.vehicle-invoices.print');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/impersonation/stop', [ImpersonationController::class, 'stop'])->name('impersonation.stop');
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('companies', CompanyController::class)->parameters(['companies' => 'company'])->except('destroy')->middleware('can:manage-companies');
    Route::post('/users/{user}/impersonate', [ImpersonationController::class, 'start'])->name('users.impersonate')->middleware('can:manage-users');
    Route::resource('users', UserController::class)->except('show')->middleware('can:manage-users');

    Route::prefix('company')->name('company.')->middleware('can:company-area')->group(function () {
        Route::get('/dashboard', CompanyDashboardController::class)->name('dashboard');
        Route::resource('expense-types', CompanyExpenseTypeController::class)->parameters(['expense-types' => 'expense_type'])->except('show', 'destroy');
        Route::resource('payment-methods', CompanyPaymentMethodController::class)->parameters(['payment-methods' => 'payment_method'])->except('show', 'destroy');
        Route::resource('warranty-providers', CompanyWarrantyProviderController::class)->parameters(['warranty-providers' => 'warranty_provider'])->except('show', 'destroy');
        Route::resource('warranty-durations', CompanyWarrantyDurationController::class)->parameters(['warranty-durations' => 'warranty_duration'])->except('show', 'destroy');
        Route::resource('vehicle-categories', CompanyVehicleCategoryController::class)->parameters(['vehicle-categories' => 'vehicle_category'])->except('show', 'destroy');
        Route::resource('customers', CompanyCustomerController::class)->except('show', 'destroy');
        Route::resource('vehicles', CompanyVehicleController::class)->except('show', 'destroy');
        Route::resource('vehicle-invoices', CompanyVehicleSaleInvoiceController::class)->parameters(['vehicle-invoices' => 'vehicle_invoice'])->except('destroy');
        Route::get('/vehicle-invoices/{vehicle_invoice}/print', [CompanyVehicleSaleInvoiceController::class, 'print'])->name('vehicle-invoices.print');
        Route::post('/vehicle-invoices/{vehicle_invoice}/email', [CompanyVehicleSaleInvoiceController::class, 'email'])->name('vehicle-invoices.email');
        Route::resource('templates', CompanyTemplateController::class)->parameters(['templates' => 'template'])->except('show');
        Route::get('/car-specifications/{type}', [CompanyCarSpecificationController::class, 'index'])->name('car-specifications.index');
        Route::get('/car-specifications/{type}/create', [CompanyCarSpecificationController::class, 'create'])->name('car-specifications.create');
        Route::post('/car-specifications/{type}', [CompanyCarSpecificationController::class, 'store'])->name('car-specifications.store');
        Route::get('/car-specifications/{type}/{specification}/edit', [CompanyCarSpecificationController::class, 'edit'])->name('car-specifications.edit');
        Route::put('/car-specifications/{type}/{specification}', [CompanyCarSpecificationController::class, 'update'])->name('car-specifications.update');
        Route::resource('roles', CompanyRoleController::class)->except('show', 'destroy');
        Route::resource('permissions', CompanyPermissionController::class)->except('show', 'destroy');
        Route::get('/settings', [CompanySettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings/profile', [CompanySettingController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/smtp', [CompanySettingController::class, 'updateSmtp'])->name('settings.smtp');
        Route::post('/settings/smtp/test', [CompanySettingController::class, 'testSmtp'])->name('settings.smtp.test');
        Route::put('/settings/account', [CompanySettingController::class, 'updateAccount'])->name('settings.account');
        Route::put('/settings/password', [CompanySettingController::class, 'updatePassword'])->name('settings.password');
        Route::resource('expenses', CompanyExpenseController::class)->only('index', 'create', 'store', 'show', 'edit', 'update', 'destroy');
        Route::get('/expenses-data', [CompanyExpenseController::class, 'data'])->name('expenses.data');
        Route::get('/expense-dropdowns', [CompanyExpenseController::class, 'dropdowns'])->name('expenses.dropdowns');
        Route::get('/reports/expenses', CompanyExpenseReportController::class)->name('reports.expenses');
        Route::resource('employees', CompanyEmployeeController::class);
        Route::get('/salaries', [CompanySalaryController::class, 'index'])->name('salaries.index');
        Route::get('/salaries/generate', [CompanySalaryController::class, 'create'])->name('salaries.create');
        Route::post('/salaries/generate', [CompanySalaryController::class, 'store'])->name('salaries.store');
        Route::get('/salaries/{salary}', [CompanySalaryController::class, 'show'])->name('salaries.show');
        Route::get('/salaries/{salary}/edit', [CompanySalaryController::class, 'edit'])->name('salaries.edit');
        Route::put('/salaries/{salary}', [CompanySalaryController::class, 'update'])->name('salaries.update');
        Route::post('/salaries/{salary}/mark-paid', [CompanySalaryController::class, 'markPaid'])->name('salaries.mark-paid');
        Route::delete('/salaries/{salary}', [CompanySalaryController::class, 'destroy'])->name('salaries.destroy');
        Route::get('/reports/salaries', CompanySalaryReportController::class)->name('reports.salaries');
        Route::resource('users', CompanyUserController::class)->except('show')->middleware('can:manage-users');

    });

    Route::prefix('settings')->name('settings.')->middleware('can:manage-settings')->group(function () {
        Route::get('/company-types', [CompanyTypeController::class, 'index'])->name('company-types.index');
        Route::post('/company-types', [CompanyTypeController::class, 'update'])->name('company-types.update');
        Route::resource('currencies', CurrencyController::class)->except('show', 'destroy');

        // General settings
        Route::get('/general', [SettingController::class, 'general'])->name('general');
        Route::post('/general', [SettingController::class, 'updateGeneral'])->name('general.update');
        Route::post('/general/mail', [SettingController::class, 'updateMail'])->name('general.mail');
        Route::post('/general/mail/test', [SettingController::class, 'testMail'])->name('general.mail.test');
        Route::post('/general/env', [SettingController::class, 'updateEnv'])->name('general.env');

        // Theme
        Route::get('/theme', [SettingController::class, 'theme'])->name('theme');
        Route::post('/theme', [SettingController::class, 'updateTheme'])->name('theme.update');

        // Notification Templates
    });
});
