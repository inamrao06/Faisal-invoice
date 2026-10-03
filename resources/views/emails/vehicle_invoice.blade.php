@php
    $company = $invoice->company;
    $currency = $company?->currency?->symbol ?? 'PKR';
    $label = 'font-size:10px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#94a3b8;';
    $cell = 'padding:10px 14px;font-size:13px;';
    $box = 'padding:12px 14px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;';
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invoice->invoice_no }}</title>
</head>
<body style="margin:0;padding:24px 12px;background:#eef2f7;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#334155;">
<table role="presentation" width="680" cellpadding="0" cellspacing="0" align="center" style="max-width:680px;width:100%;background:#ffffff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;">
    <tr>
        <td style="padding:24px 28px;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>
                <td style="vertical-align:top;">
                    @if($company?->logo_path)
                        <img src="{{ asset('storage/'.$company->logo_path) }}" alt="{{ $company->name }}" height="46" style="height:46px;max-width:170px;object-fit:contain;display:block;margin-bottom:10px;">
                    @endif
                    <div style="font-size:19px;font-weight:800;color:#0f172a;">{{ $company?->name ?? 'Company' }}</div>
                    <div style="font-size:12px;color:#64748b;margin-top:4px;line-height:1.6;">
                        {{ collect([$company?->company_email, $company?->phone])->filter()->implode(' · ') }}<br>
                        {{ trim(($company?->address ?? '').' '.($company?->city ?? '')) }}
                        @if($company?->tax_number)<br>Tax No: {{ $company->tax_number }}@endif
                    </div>
                </td>
                <td align="right" style="vertical-align:top;">
                    <div style="{{ $label }}">Invoice No</div>
                    <div style="font-size:22px;font-weight:800;color:#0f172a;margin:4px 0 8px;">{{ $invoice->invoice_no }}</div>
                    <span style="display:inline-block;padding:4px 10px;border-radius:999px;background:#dcfce7;color:#15803d;font-size:11px;font-weight:800;">{{ str($invoice->payment_status)->replace('_',' ')->title() }}</span>
                </td>
            </tr></table>
        </td>
    </tr>

    @if(trim($note ?? ''))
        <tr><td style="padding:22px 28px 0;"><div style="font-size:14px;line-height:1.65;color:#475569;">{!! nl2br(e($note)) !!}</div></td></tr>
    @endif

    <tr>
        <td style="padding:20px 28px 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>
                <td style="{{ $box }}vertical-align:top;">
                    <div style="{{ $label }}margin-bottom:5px;">Bill To</div>
                    <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $invoice->customer?->name }}</div>
                    <div style="font-size:12px;color:#64748b;line-height:1.6;">
                        {{ collect([$invoice->customer?->phone, $invoice->customer?->email])->filter()->implode('<br>') }}<br>
                        {{ trim(($invoice->customer?->address ?? '').' '.($invoice->customer?->postcode ?? '')) }}
                    </div>
                </td>
                <td width="12"></td>
                <td style="{{ $box }}vertical-align:top;">
                    <div style="{{ $label }}margin-bottom:5px;">Vehicle</div>
                    <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $invoice->vehicle?->make_model }}</div>
                    <div style="font-size:12px;color:#64748b;line-height:1.6;">
                        Reg: {{ $invoice->vehicle?->registration_no ?: '—' }}<br>
                        VIN: {{ $invoice->vehicle?->vin ?: '—' }}<br>
                        @if($invoice->vehicle?->year)Year: {{ $invoice->vehicle->year }} · @endif
                        @if($invoice->vehicle?->mileage)Mileage: {{ number_format($invoice->vehicle->mileage) }}@endif<br>
                        Type: {{ $invoice->category?->name ?? '—' }}
                    </div>
                </td>
            </tr></table>
        </td>
    </tr>

    <tr>
        <td style="padding:18px 28px 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>
                @foreach(['Invoice Date' => $invoice->invoice_date?->format('d M Y'), 'Sale Date' => $invoice->sale_date?->format('d M Y') ?? '—', 'Sale Type' => str($invoice->transaction_type)->replace('_',' ')->title(), 'Balance' => $currency.' '.number_format($invoice->balance_amount, 2)] as $key => $value)
                    @if(!$loop->first)<td width="10"></td>@endif
                    <td style="padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;text-align:center;">
                        <div style="{{ $label }}">{{ $key }}</div>
                        <div style="font-size:13px;font-weight:700;color:#0f172a;margin-top:3px;">{{ $value }}</div>
                    </td>
                @endforeach
            </tr></table>
        </td>
    </tr>

    <tr>
        <td style="padding:22px 28px 0;">
            <div style="font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#64748b;margin-bottom:8px;">Totals &amp; Payments</div>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:8px;">
                <tr>
                    <td style="{{ $cell }}color:#475569;background:#f8fafc;">Vehicle Price</td>
                    <td align="right" style="{{ $cell }}font-weight:700;color:#0f172a;background:#f8fafc;">{{ $currency }} {{ number_format($invoice->vehicle_price, 2) }}</td>
                </tr>
                <tr>
                    <td style="{{ $cell }}color:#475569;border-top:1px solid #f1f5f9;">Discount</td>
                    <td align="right" style="{{ $cell }}font-weight:700;color:#0f172a;border-top:1px solid #f1f5f9;">{{ $currency }} {{ number_format($invoice->discount, 2) }}</td>
                </tr>
                @if((float) $invoice->commission_amount > 0)
                    <tr>
                        <td style="{{ $cell }}color:#475569;border-top:1px solid #f1f5f9;">Commission @if($invoice->commission_notes)<span style="color:#94a3b8;">({{ $invoice->commission_notes }})</span>@endif</td>
                        <td align="right" style="{{ $cell }}font-weight:700;color:#0f172a;border-top:1px solid #f1f5f9;">{{ $currency }} {{ number_format($invoice->commission_amount, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td style="{{ $cell }}font-weight:800;color:#0f172a;border-top:1px solid #e2e8f0;">Total Sale Price</td>
                    <td align="right" style="{{ $cell }}font-size:14px;font-weight:800;color:#1D4ED8;border-top:1px solid #e2e8f0;">{{ $currency }} {{ number_format($invoice->total_sale_price, 2) }}</td>
                </tr>
                @forelse($invoice->payments as $payment)
                    <tr>
                        <td style="padding:9px 14px;font-size:12px;color:#64748b;border-top:1px solid #f1f5f9;">{{ $payment->payment_date?->format('d/m/Y') }} · {{ str($payment->payment_type)->replace('_',' ')->title() }} ({{ $payment->method }})</td>
                        <td align="right" style="padding:9px 14px;font-size:12px;font-weight:700;color:#15803d;border-top:1px solid #f1f5f9;">{{ $currency }} {{ number_format($payment->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" style="padding:9px 14px;font-size:12px;color:#94a3b8;border-top:1px solid #f1f5f9;">No payments recorded yet.</td></tr>
                @endforelse
                <tr>
                    <td style="{{ $cell }}color:#475569;border-top:1px solid #f1f5f9;">Total Paid</td>
                    <td align="right" style="{{ $cell }}font-weight:700;color:#15803d;border-top:1px solid #f1f5f9;">{{ $currency }} {{ number_format($invoice->total_paid, 2) }}</td>
                </tr>
                <tr>
                    <td style="{{ $cell }}font-weight:800;color:#0f172a;background:#f8fafc;border-top:1px solid #e2e8f0;">Balance Due</td>
                    <td align="right" style="{{ $cell }}font-size:15px;font-weight:800;color:#0f172a;background:#f8fafc;border-top:1px solid #e2e8f0;">{{ $currency }} {{ number_format($invoice->balance_amount, 2) }}</td>
                </tr>
            </table>
        </td>
    </tr>

    @if($invoice->warranty_provider_name)
        <tr>
            <td style="padding:20px 28px 0;">
                <div style="font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#64748b;margin-bottom:8px;">Warranty</div>
                <div style="{{ $box }}font-size:13px;color:#475569;line-height:1.6;">
                    <strong style="color:#0f172a;">{{ $invoice->warranty_provider_name }}</strong>@if($invoice->warranty_duration_months !== null) · {{ $invoice->warranty_duration_months }} month(s)@endif
                    @if($invoice->warranty_terms)<br>{!! nl2br(e($invoice->warranty_terms)) !!}@endif
                </div>
            </td>
        </tr>
    @endif

    @if($invoice->vehicle_terms || $invoice->general_terms)
        <tr>
            <td style="padding:20px 28px 0;">
                <div style="font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#64748b;margin-bottom:8px;">Terms</div>
                @if($invoice->vehicle_terms)
                    <div style="padding:12px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:12px;line-height:1.6;color:#475569;">
                        <div style="{{ $label }}margin-bottom:5px;">Vehicle Terms</div>{!! nl2br(e($invoice->vehicle_terms)) !!}
                    </div>
                @endif
                @if($invoice->general_terms)
                    <div style="padding:12px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:12px;line-height:1.6;color:#475569;margin-top:10px;">{!! nl2br(e($invoice->general_terms)) !!}</div>
                @endif
            </td>
        </tr>
    @endif

    @if($company?->signature_path || $company?->stamp_path || $company?->authorized_person || $company?->manager_name)
        <tr>
            <td align="right" style="padding:24px 28px 0;">
                @if($company?->signature_path)<img src="{{ asset('storage/'.$company->signature_path) }}" alt="Signature" style="max-height:56px;max-width:180px;object-fit:contain;">@endif
                @if($company?->stamp_path)<img src="{{ asset('storage/'.$company->stamp_path) }}" alt="Stamp" style="max-height:62px;max-width:110px;object-fit:contain;margin-left:10px;">@endif
                <div style="font-size:12px;color:#64748b;margin-top:6px;">{{ $company?->authorized_person ?: ($company?->manager_name ?: 'Authorized Signatory') }}</div>
                @if($company?->designation)<div style="font-size:12px;color:#94a3b8;">{{ $company->designation }}</div>@endif
            </td>
        </tr>
    @endif

    <tr>
        <td style="padding:24px 28px;border-top:1px solid #e2e8f0;background:#f8fafc;font-size:11px;color:#94a3b8;line-height:1.6;">
            This invoice was sent by {{ $company?->name ?? 'our company' }}.@if($company?->phone) For questions call {{ $company->phone }}.@endif @if($company?->website){{ $company->website }}@endif
        </td>
    </tr>
</table>
</body>
</html>
