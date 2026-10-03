<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\VehicleSaleInvoice;
use App\Models\WarrantyDuration;
use App\Models\WarrantyProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Throwable;

class CompanyVehicleSaleInvoiceController extends Controller
{
    private const GENERAL_TERMS = '';

    /*
    |--------------------------------------------------------------------------
    | Invoice Listing
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $invoices = VehicleSaleInvoice::with([
            'customer',
            'vehicle',
            'company.currency',
        ])
            ->where('branch_id', $this->companyId())
            ->latest()
            ->paginate(15);

        return view('company.vehicle_invoices.index', [
            'invoices' => $invoices,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Invoice
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $invoice = new VehicleSaleInvoice([
            'invoice_date' => now(),
            'sale_date' => now(),
            'transaction_type' => 'vehicle_sale',
        ]);

        return view(
            'company.vehicle_invoices.form',
            $this->formData($invoice)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store Invoice
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validatedData = $this->validated($request);

        $invoice = DB::transaction(function () use ($request, $validatedData) {
            return $this->save(
                $request,
                $validatedData
            );
        });

        return redirect()
            ->route('company.vehicle-invoices.show', $invoice)
            ->with('success', 'Vehicle invoice created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Show Invoice
    |--------------------------------------------------------------------------
    */

    public function show(VehicleSaleInvoice $vehicle_invoice)
    {
        $this->authorizeInvoice($vehicle_invoice);

        $vehicle_invoice->load([
            'customer',
            'sellerCustomer',
            'vehicle.category',
            'category',
            'warrantyProvider',
            'payments',
            'company.currency',
        ]);

        return view('company.vehicle_invoices.show', [
            'invoice' => $vehicle_invoice,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Invoice
    |--------------------------------------------------------------------------
    */

    public function edit(VehicleSaleInvoice $vehicle_invoice)
    {
        $this->authorizeInvoice($vehicle_invoice);

        $vehicle_invoice->load('payments');

        return view(
            'company.vehicle_invoices.form',
            $this->formData($vehicle_invoice)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Invoice
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        VehicleSaleInvoice $vehicle_invoice
    ) {
        $this->authorizeInvoice($vehicle_invoice);

        $validatedData = $this->validated($request);

        DB::transaction(function () use (
            $request,
            $validatedData,
            $vehicle_invoice
        ) {
            $this->save(
                $request,
                $validatedData,
                $vehicle_invoice
            );
        });

        return redirect()
            ->route(
                'company.vehicle-invoices.show',
                $vehicle_invoice
            )
            ->with(
                'success',
                'Vehicle invoice updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Print Invoice
    |--------------------------------------------------------------------------
    */

    public function print(VehicleSaleInvoice $vehicle_invoice)
    {
        $this->authorizeInvoice($vehicle_invoice);

        $vehicle_invoice = $this->loadPrintableInvoice($vehicle_invoice);

        return view('company.vehicle_invoices.print', [
            'invoice' => $vehicle_invoice,
            'publicUrl' => route('company.vehicle-invoices.print', $vehicle_invoice),
            'backUrl' => route('company.vehicle-invoices.show', $vehicle_invoice),
        ]);
    }

    public function publicPrint(VehicleSaleInvoice $vehicle_invoice)
    {
        $vehicle_invoice = $this->loadPrintableInvoice($vehicle_invoice);

        return view('company.vehicle_invoices.print', [
            'invoice' => $vehicle_invoice,
            'publicUrl' => route('company.vehicle-invoices.print', $vehicle_invoice),
            'backUrl' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Email Invoice
    |--------------------------------------------------------------------------
    */

    public function email(
        Request $request,
        VehicleSaleInvoice $vehicle_invoice
    ) {
        $this->authorizeInvoice($vehicle_invoice);

        $vehicle_invoice->load([
            'customer',
            'sellerCustomer',
            'vehicle.category',
            'category',
            'warrantyProvider',
            'payments',
            'company.currency',
        ]);

        $data = $request->validate([
            'to' => [
                'required',
                'email',
                'max:150',
            ],

            'subject' => [
                'required',
                'max:180',
            ],

            'message' => [
                'nullable',
                'max:2000',
            ],
        ], [], [
            'to' => 'Send To',
        ]);

        $company = $vehicle_invoice->company;

        $mailer = $this->configureCompanyMail($company);

        try {

            Mail::mailer($mailer)->send(
                'emails.vehicle_invoice',
                [
                    'invoice' => $vehicle_invoice,
                    'note' => $data['message'] ?? '',
                ],
                function ($mail) use ($data, $company) {

                    $mail
                        ->to($data['to'])
                        ->cc('carhives@gmail.com')
                        ->subject($data['subject']);

                    if (filled($company?->smtp_from_address)) {

                        $mail->from(
                            $company->smtp_from_address,
                            $company->smtp_from_name
                                ?: $company->name
                        );
                    }
                }
            );

        } catch (Throwable $exception) {

            report($exception);

            return redirect()
                ->route(
                    'company.vehicle-invoices.show',
                    $vehicle_invoice
                )
                ->withErrors([
                    'to' => 'Invoice email could not be sent. '
                        .'Please check the company SMTP settings '
                        .'and application log.',
                ]);
        }

        return redirect()
            ->route(
                'company.vehicle-invoices.show',
                $vehicle_invoice
            )
            ->with(
                'success',
                'Invoice '
                .$vehicle_invoice->invoice_no
                .' sent successfully to '
                .$data['to']
                .'.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Configure Company SMTP
    |--------------------------------------------------------------------------
    */

    private function configureCompanyMail(?Branch $company): string
    {
        /*
         * If company SMTP is not configured,
         * use the application's default mailer.
         */

        if (
            blank($company?->smtp_host)
            ||
            blank($company?->smtp_from_address)
        ) {
            return config('mail.default');
        }

        $encryption = $company->smtp_encryption ?: null;

        config([
            'mail.default' => 'smtp',

            'mail.mailers.smtp.host' => $company->smtp_host,

            'mail.mailers.smtp.port' => (int) ($company->smtp_port ?: 587),

            'mail.mailers.smtp.username' => $company->smtp_username,

            'mail.mailers.smtp.password' => $company->smtp_password,

            'mail.mailers.smtp.scheme' => $encryption === 'ssl'
                    ? 'smtps'
                    : 'smtp',

            'mail.mailers.smtp.encryption' => $encryption,

            'mail.from.address' => $company->smtp_from_address,

            'mail.from.name' => $company->smtp_from_name
                ?: $company->name,
        ]);

        Mail::purge('smtp');

        return 'smtp';
    }

    private function loadPrintableInvoice(
        VehicleSaleInvoice $invoice
    ): VehicleSaleInvoice {
        $invoice->load([
            'customer',
            'sellerCustomer',
            'vehicle.category',
            'category',
            'warrantyProvider',
            'payments',
            'company.currency',
        ]);

        return $invoice;
    }

    /*
    |--------------------------------------------------------------------------
    | Form Data
    |--------------------------------------------------------------------------
    */

    private function formData(VehicleSaleInvoice $invoice): array
    {
        $this->ensureWarrantyDurationDefaults();

        return [

            'invoice' => $invoice,

            'customers' => Customer::where(
                'branch_id',
                $this->companyId()
            )
                ->orderBy('name')
                ->get(),

            'vehicles' => Vehicle::with('category')
                ->where(
                    'branch_id',
                    $this->companyId()
                )
                ->orderBy('make_model')
                ->get(),

            'categories' => VehicleCategory::where(
                'branch_id',
                $this->companyId()
            )
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),

            'warrantyProviders' => WarrantyProvider::where(
                'branch_id',
                $this->companyId()
            )
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),

            'warrantyDurations' => WarrantyDuration::where(
                'branch_id',
                $this->companyId()
            )
                ->where('is_active', true)
                ->orderBy('months')
                ->get(),

            'paymentMethods' => PaymentMethod::where(
                function ($query) {
                    $query
                        ->where(
                            'branch_id',
                            $this->companyId()
                        );
                }
            )
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    private function validated(Request $request): array
    {
        $companyId = $this->companyId();

        $customerIds = Customer::where(
            'branch_id',
            $companyId
        )->pluck('id')->all();

        $vehicleIds = Vehicle::where(
            'branch_id',
            $companyId
        )->pluck('id')->all();

        $categoryIds = VehicleCategory::where(
            'branch_id',
            $companyId
        )->pluck('id')->all();

        $warrantyProviderIds = WarrantyProvider::where(
            'branch_id',
            $companyId
        )->pluck('id')->all();

        return $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Buyer
            |--------------------------------------------------------------------------
            */

            'buyer_customer_mode' => [
                'required',
                Rule::in([
                    'existing',
                    'new',
                ]),
            ],

            'customer_id' => [
                'nullable',
                'required_if:buyer_customer_mode,existing',
                Rule::in($customerIds),
            ],

            'buyer_name' => [
                'nullable',
                'required_if:buyer_customer_mode,new',
                'max:150',
            ],

            'buyer_email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'buyer_phone' => [
                'nullable',
                'max:50',
            ],

            'buyer_address' => [
                'nullable',
                'max:1000',
            ],

            'buyer_postcode' => [
                'nullable',
                'max:30',
            ],

            /*
            |--------------------------------------------------------------------------
            | Seller
            |--------------------------------------------------------------------------
            */

            'seller_customer_mode' => [
                'nullable',
                'required_if:transaction_type,customer_to_customer',
                Rule::in([
                    'existing',
                    'new',
                ]),
            ],

            'seller_name' => [
                'nullable',
                'required_if:seller_customer_mode,new',
                'max:150',
            ],

            'seller_email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'seller_phone' => [
                'nullable',
                'max:50',
            ],

            'seller_address' => [
                'nullable',
                'max:1000',
            ],

            'seller_postcode' => [
                'nullable',
                'max:30',
            ],

            /*
            |--------------------------------------------------------------------------
            | Vehicle
            |--------------------------------------------------------------------------
            */

            'vehicle_mode' => [
                'required',
                Rule::in([
                    'existing',
                    'new',
                ]),
            ],

            'vehicle_id' => [
                'nullable',
                'required_if:vehicle_mode,existing',
                Rule::in($vehicleIds),
            ],

            'new_vehicle_make_model' => [
                'nullable',
                'required_if:vehicle_mode,new',
                'max:150',
            ],

            'new_vehicle_registration_no' => [
                'nullable',
                'max:80',
            ],

            'new_vehicle_vin' => [
                'nullable',
                'max:120',
            ],

            'new_vehicle_year' => [
                'nullable',
                'integer',
                'min:1900',
                'max:2100',
            ],

            'new_vehicle_mileage' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'new_vehicle_keys_count' => [
                'nullable',
                'integer',
                'min:0',
                'max:20',
            ],

            /*
            |--------------------------------------------------------------------------
            | Category
            |--------------------------------------------------------------------------
            */

            'vehicle_category_id' => [
                'required',
                Rule::in($categoryIds),
            ],

            /*
            |--------------------------------------------------------------------------
            | Invoice
            |--------------------------------------------------------------------------
            */

            'invoice_date' => [
                'required',
                'date',
            ],

            'sale_date' => [
                'nullable',
                'date',
            ],

            'transaction_type' => [
                'required',
                Rule::in([
                    'vehicle_sale',
                    'customer_to_customer',
                    'vehicle_reservation',
                    'part_payment',
                    'final_payment',
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */

            'vehicle_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'discount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'tax' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'commission_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'commission_notes' => [
                'nullable',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Warranty
            |--------------------------------------------------------------------------
            */

            'warranty_provider_id' => [
                'nullable',
                Rule::in($warrantyProviderIds),
            ],

            'warranty_duration_months' => [
                'nullable',
                'integer',
                'min:0',
                'max:120',
            ],

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            'notes' => [
                'nullable',
                'max:2000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Payments
            |--------------------------------------------------------------------------
            */

            'payments' => [
                'nullable',
                'array',
            ],

            'payments.*.payment_date' => [
                'required_with:payments',
                'date',
            ],

            'payments.*.payment_type' => [
                'required_with:payments',
                Rule::in([
                    'deposit',
                    'part_payment',
                    'final_payment',
                    'further_payment',
                ]),
            ],

            'payments.*.method' => [
                'required_with:payments',
                'max:60',
            ],

            'payments.*.amount' => [
                'required_with:payments',
                'numeric',
                'min:0.01',
            ],

            'payments.*.notes' => [
                'nullable',
                'max:500',
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Save Invoice
    |--------------------------------------------------------------------------
    */

    private function save(
        Request $request,
        array $data,
        ?VehicleSaleInvoice $invoice = null
    ): VehicleSaleInvoice {

        /*
        |--------------------------------------------------------------------------
        | Related Records
        |--------------------------------------------------------------------------
        */

        $category = VehicleCategory::find(
            $data['vehicle_category_id']
        );

        $provider = filled(
            $data['warranty_provider_id'] ?? null
        )
            ? WarrantyProvider::find(
                $data['warranty_provider_id']
            )
            : null;

        /*
        |--------------------------------------------------------------------------
        | Buyer
        |--------------------------------------------------------------------------
        */

        $buyer = $this->resolveCustomer(
            $data,
            'buyer'
        );

        /*
        |--------------------------------------------------------------------------
        | Seller
        |--------------------------------------------------------------------------
        */

        $seller = null;

        if (
            $data['transaction_type']
            === 'customer_to_customer'
        ) {
            $seller = $this->resolveCustomer(
                $data,
                'seller'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Vehicle
        |--------------------------------------------------------------------------
        */

        $vehicle = $this->resolveVehicle($data);

        /*
        |--------------------------------------------------------------------------
        | Validate Buyer / Seller
        |--------------------------------------------------------------------------
        */

        if (
            $seller
            &&
            $seller->id === $buyer->id
        ) {
            abort(
                422,
                'Seller and buyer must be different customers.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate Amounts
        |--------------------------------------------------------------------------
        */

        $vehiclePrice = (float) $data['vehicle_price'];

        $discount = (float) (
            $data['discount'] ?? 0
        );

        $commission = (float) (
            $data['commission_amount'] ?? 0
        );

        $totalSalePrice = max(
            0,
            $vehiclePrice
            - $discount
            + $commission
        );
        $tax = (float) (
            $data['tax'] ?? 0
        );
        $totalSalePrice = $totalSalePrice + $tax;

        /*
        |--------------------------------------------------------------------------
        | Calculate Paid Amount
        |--------------------------------------------------------------------------
        */

        $totalPaid = collect(
            $data['payments'] ?? []
        )->sum(
            fn ($payment) => (float) $payment['amount']
        );

        /*
        |--------------------------------------------------------------------------
        | Calculate Balance
        |--------------------------------------------------------------------------
        */

        $balanceAmount = max(
            0,
            $totalSalePrice - $totalPaid
        );

        /*
        |--------------------------------------------------------------------------
        | Payment Status
        |--------------------------------------------------------------------------
        */

        $paymentStatus = 'outstanding';

        if ($balanceAmount <= 0) {

            $paymentStatus = 'paid';

        } elseif ($totalPaid > 0) {

            $paymentStatus =
                $data['transaction_type']
                === 'vehicle_reservation'
                    ? 'deposit'
                    : 'part_paid';
        }

        /*
        |--------------------------------------------------------------------------
        | Create Invoice
        |--------------------------------------------------------------------------
        */

        if (! $invoice) {

            $branch = Branch::findOrFail(
                $this->companyId()
            );

            $sequence =
                VehicleSaleInvoice::withTrashed()
                    ->where(
                        'branch_id',
                        $branch->id
                    )
                    ->count()
                    + 1;

            $invoiceNumber = sprintf(
                '%s-%06d',
                $branch->invoice_prefix
                    ?: $branch->code,
                $sequence
            );

            $invoice = new VehicleSaleInvoice([

                'invoice_no' => $invoiceNumber,

                'branch_id' => $branch->id,

                'created_by' => auth()->id(),

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Fill Invoice
        |--------------------------------------------------------------------------
        */

        $invoice->fill([

            'customer_id' => $buyer->id,

            'seller_customer_id' => $seller?->id,

            'vehicle_id' => $vehicle->id,

            'vehicle_category_id' => $category?->id,

            'invoice_date' => $data['invoice_date'],

            'sale_date' => $data['sale_date'] ?? null,

            'transaction_type' => $data['transaction_type'],

            'vehicle_price' => $vehiclePrice,

            'discount' => $discount,

            'tax' => $tax,

            'commission_amount' => $commission,

            'commission_notes' => $data['commission_notes'] ?? null,

            'total_sale_price' => $totalSalePrice,

            'total_paid' => $totalPaid,

            'balance_amount' => $balanceAmount,

            'payment_status' => $paymentStatus,

            'warranty_provider_id' => $provider?->id,

            'warranty_provider_name' => $provider?->name,
             'warranty_provider_description' => $provider?->description,


            'warranty_duration_months' => $data['warranty_duration_months']
                ?? $category?->default_warranty_months,

            'warranty_terms' => $provider?->terms,

            'vehicle_terms' => $category?->terms,

            'general_terms' => self::GENERAL_TERMS,

            'notes' => $data['notes'] ?? null,

        ]);

        $invoice->save();

        /*
        |--------------------------------------------------------------------------
        | Save Payments
        |--------------------------------------------------------------------------
        */

        $invoice->payments()->delete();

        foreach (
            $data['payments'] ?? [] as $payment
        ) {

            $invoice->payments()->create(
                $payment
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Update Vehicle Status
        |--------------------------------------------------------------------------
        */

        $vehicle->update([

            'status' => $balanceAmount <= 0
                    ? 'sold'
                    : 'reserved',

            'vehicle_category_id' => $category?->id,

            'sale_price' => $vehiclePrice,

        ]);

        return $invoice;
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Customer
    |--------------------------------------------------------------------------
    */

    private function resolveCustomer(
        array $data,
        string $role
    ): Customer {

        $companyId =
            $this->companyId();

        /*
        |--------------------------------------------------------------------------
        | Existing Customer
        |--------------------------------------------------------------------------
        */

        if (
            ($data[$role.'_customer_mode']
                ?? 'existing')
            === 'existing'
        ) {

            $customerId =
                $role === 'buyer'
                    ? $data['customer_id']
                    : $data['seller_customer_id'];

            return Customer::where(
                'branch_id',
                $companyId
            )->findOrFail(
                $customerId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | New Customer
        |--------------------------------------------------------------------------
        */

        $email =
            $data[$role.'_email']
            ?? null;

        $attributes = [

            'branch_id' => $companyId,

            'name' => $data[$role.'_name'],

            'email' => $email,

            'phone' => $data[$role.'_phone']
                ?? null,

            'address' => $data[$role.'_address']
                ?? null,

            'postcode' => $data[$role.'_postcode']
                ?? null,

        ];

        /*
        |--------------------------------------------------------------------------
        | Update Existing Customer By Email
        |--------------------------------------------------------------------------
        */

        if (filled($email)) {

            return Customer::updateOrCreate(
                [
                    'branch_id' => $companyId,

                    'email' => $email,
                ],
                $attributes
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create New Customer
        |--------------------------------------------------------------------------
        */

        return Customer::create(
            $attributes
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Vehicle
    |--------------------------------------------------------------------------
    */

    private function resolveVehicle(
        array $data
    ): Vehicle {

        $companyId =
            $this->companyId();

        /*
        |--------------------------------------------------------------------------
        | Existing Vehicle
        |--------------------------------------------------------------------------
        */

        if (
            ($data['vehicle_mode']
                ?? 'existing')
            === 'existing'
        ) {

            return Vehicle::where(
                'branch_id',
                $companyId
            )->findOrFail(
                $data['vehicle_id']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | New Vehicle
        |--------------------------------------------------------------------------
        */

        $attributes = [

            'branch_id' => $companyId,

            'vehicle_category_id' => $data['vehicle_category_id'],

            'make_model' => $data['new_vehicle_make_model'],

            'registration_no' => $data['new_vehicle_registration_no']
                ?? null,

            'vin' => $data['new_vehicle_vin']
                ?? null,

            'year' => $data['new_vehicle_year']
                ?? null,

            'mileage' => $data['new_vehicle_mileage']
                ?? null,

            'keys_count' => $data['new_vehicle_keys_count']
                ?? null,

            'sale_price' => $data['vehicle_price'],

            'status' => 'available',
        ];

        /*
        |--------------------------------------------------------------------------
        | Find By Registration Number
        |--------------------------------------------------------------------------
        */

        if (
            filled(
                $attributes['registration_no']
            )
        ) {

            return Vehicle::updateOrCreate(
                [
                    'branch_id' => $companyId,

                    'registration_no' => $attributes['registration_no'],
                ],
                $attributes
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Find By VIN
        |--------------------------------------------------------------------------
        */

        if (
            filled(
                $attributes['vin']
            )
        ) {

            return Vehicle::updateOrCreate(
                [
                    'branch_id' => $companyId,

                    'vin' => $attributes['vin'],
                ],
                $attributes
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create Vehicle
        |--------------------------------------------------------------------------
        */

        return Vehicle::create(
            $attributes
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Company / Branch ID
    |--------------------------------------------------------------------------
    */

    private function companyId(): int
    {
        abort_unless(
            auth()->user()->branch_id,
            403
        );

        return auth()->user()->branch_id;
    }

    /*
    |--------------------------------------------------------------------------
    | Invoice Authorization
    |--------------------------------------------------------------------------
    */

    private function authorizeInvoice(
        VehicleSaleInvoice $invoice
    ): void {

        abort_unless(
            $invoice->branch_id ===
                $this->companyId(),
            403
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Warranty Duration Defaults
    |--------------------------------------------------------------------------
    */

    private function ensureWarrantyDurationDefaults(): void
    {
        $durations = [
            0,
            3,
            6,
            12,
            18,
            24,
            36,
            48,
            60,
        ];

        foreach ($durations as $months) {

            WarrantyDuration::firstOrCreate(

                [
                    'branch_id' => $this->companyId(),

                    'months' => $months,
                ],

                [
                    'name' => $months
                        .' month'
                        .(
                            $months === 1
                                ? ''
                                : 's'
                        ),

                    'is_system' => true,

                    'is_active' => true,
                ]
            );
        }
    }
}
