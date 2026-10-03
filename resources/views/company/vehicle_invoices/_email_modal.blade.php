@php
    $currency = $invoice->company?->currency?->symbol ?? 'PKR';
    $vehicle = $invoice->vehicle;
    $companyName = $invoice->company?->name ?? 'our company';
    $modalId = 'invoiceEmailModal-' . $invoice->id;
    $defaultSubject = 'Vehicle Invoice ' . $invoice->invoice_no . ' from ' . $companyName;
    $paymentLines = $invoice->payments->map(function ($payment) use ($currency) {
        return '- ' . optional($payment->payment_date)->format('d M Y') . ' | '
            . str($payment->payment_type ?? 'Payment')->replace('_', ' ')->title() . ' | '
            . ($payment->method ?? 'Payment') . ' | '
            . $currency . ' ' . number_format((float) $payment->amount, 2);
    })->implode("\n");
    $defaultMessage = "Dear " . ($invoice->customer?->name ?? 'Customer') . ",\n\n"
        . "Please find your vehicle invoice " . $invoice->invoice_no . " dated " . $invoice->invoice_date?->format('d M Y') . " below.\n\n"
        . "Car details:\n"
        . "Vehicle: " . ($vehicle?->make_model ?? '-') . "\n"
        . "Registration: " . ($vehicle?->registration_no ?? '-') . "\n"
        . "VIN / Chassis No: " . ($vehicle?->vin ?? '-') . "\n"
        . "Year: " . ($vehicle?->year ?? '-') . "\n"
        . "Mileage: " . ($vehicle?->mileage !== null ? number_format((float) $vehicle->mileage) : '-') . "\n\n"
        . "Payment details:\n"
        . "Total: " . $currency . ' ' . number_format((float) $invoice->total_sale_price, 2) . "\n"
        . "Paid: " . $currency . ' ' . number_format((float) $invoice->total_paid, 2) . "\n"
        . "Balance: " . $currency . ' ' . number_format((float) $invoice->balance_amount, 2) . "\n"
        . ($paymentLines ? "\nPayments received:\n" . $paymentLines . "\n" : "\n")
        . "\nGood luck with your car! We hope you enjoy driving it.\n\n"
        . "Kind regards,\n"
        . "Team " . $companyName . "\n"
        . "cars@carhive.uk | www.carhive.uk\n"
        . "07399 517094 | 01700 800521\n"
        . "35 Fengate, Peterborough, PE1 5BA";
@endphp
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="POST" action="{{ route('company.vehicle-invoices.email', $invoice) }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Send Invoice to Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label" for="{{ $modalId }}-to">Send To</label>
                        <input type="email" class="form-control" id="{{ $modalId }}-to" name="to" value="{{ old('to', $invoice->customer?->email) }}" placeholder="customer@example.com" required>
                        @unless($invoice->customer?->email)
                            <div class="form-text text-warning">This customer has no saved email — type one to send.</div>
                        @endunless
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" for="{{ $modalId }}-subject">Subject</label>
                        <input type="text" class="form-control" id="{{ $modalId }}-subject" name="subject" value="{{ old('subject', $defaultSubject) }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="{{ $modalId }}-message">Message</label>
                        <textarea class="form-control" id="{{ $modalId }}-message" name="message" rows="4" placeholder="Message shown above the invoice...">{{ old('message', $defaultMessage) }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="info-strip">
                            <p class="text-muted fs-12 text-uppercase mb-1">Invoice Summary</p>
                            <p class="mb-0 fs-13">
                                {{ $invoice->invoice_no }} · {{ $invoice->customer?->name }} · {{ $invoice->vehicle?->make_model }} ({{ $invoice->vehicle?->registration_no ?: '—' }})<br>
                                Total {{ $currency }} {{ number_format($invoice->total_sale_price, 2) }} · Paid {{ $currency }} {{ number_format($invoice->total_paid, 2) }} · Balance {{ $currency }} {{ number_format($invoice->balance_amount, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
                @if($errors->any())
                    <div class="alert alert-danger mt-3 mb-0">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Send Invoice</button>
            </div>
        </form>
    </div>
</div>
