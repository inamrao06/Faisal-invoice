<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyDocumentTemplate extends Model
{
    protected $fillable = ['branch_id', 'type', 'name', 'body', 'is_default', 'is_active'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean', 'is_active' => 'boolean'];
    }

    public function company()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public static function defaultFor(int $branchId, string $type): ?self
    {
        return self::where('branch_id', $branchId)
            ->where('type', $type)
            ->where('is_active', true)
            ->where('is_default', true)
            ->latest()
            ->first();
    }

    public function render(array $values): string
    {
        return strtr($this->body, collect($values)->map(fn ($value) => e((string) $value))->all());
    }

    public static function invoiceValues(VehicleSaleInvoice $invoice): array
    {
        $company = $invoice->company;
        return array_merge(NotificationTemplate::companyTagValues($company), [
            '[invoice_no]' => $invoice->invoice_no,
            '[invoice_date]' => $invoice->invoice_date?->format('d M Y'),
            '[sale_date]' => $invoice->sale_date?->format('d M Y'),
            '[sale_type]' => str($invoice->transaction_type)->replace('_', ' ')->title(),
            '[buyer_name]' => $invoice->customer?->name,
            '[buyer_phone]' => $invoice->customer?->phone,
            '[buyer_email]' => $invoice->customer?->email,
            '[seller_name]' => $invoice->sellerCustomer?->name,
            '[seller_phone]' => $invoice->sellerCustomer?->phone,
            '[vehicle]' => $invoice->vehicle?->make_model,
            '[registration]' => $invoice->vehicle?->registration_no,
            '[vin]' => $invoice->vehicle?->vin,
            '[vehicle_price]' => number_format((float) $invoice->vehicle_price, 2),
            '[discount]' => number_format((float) $invoice->discount, 2),
            '[commission]' => number_format((float) $invoice->commission_amount, 2),
            '[invoice_amount]' => number_format((float) $invoice->total_sale_price, 2),
            '[paid_amount]' => number_format((float) $invoice->total_paid, 2),
            '[balance_amount]' => number_format((float) $invoice->balance_amount, 2),
            '[payment_status]' => str($invoice->payment_status)->replace('_', ' ')->title(),
        ]);
    }

    public static function expenseValues(Expense $expense): array
    {
        $company = $expense->company;
        return array_merge(NotificationTemplate::companyTagValues($company), [
            '[expense_no]' => $expense->invoice_no,
            '[expense_date]' => $expense->expense_date?->format('d M Y'),
            '[expense_amount]' => number_format((float) $expense->total_amount, 2),
            '[expense_category]' => $expense->head?->name,
            '[expense_description]' => $expense->remarks,
            '[payment]' => str($expense->payment_method)->replace('_', ' ')->title(),
            '[vehicle]' => $expense->vehicle?->make_model,
            '[registration]' => $expense->vehicle?->registration_no,
            '[expense_lists]' => $expense->items->map(fn ($item) => $item->description . ' - ' . number_format((float) $item->line_total, 2))->join(', '),
            '[subtotal]' => number_format((float) $expense->subtotal, 2),
            '[tax_amount]' => number_format((float) $expense->tax_amount, 2),
            '[total_amount]' => number_format((float) $expense->total_amount, 2),
            '[status]' => str($expense->status)->title(),
        ]);
    }
}
