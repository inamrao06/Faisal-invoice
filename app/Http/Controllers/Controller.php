<?php
namespace App\Http\Controllers;

abstract class Controller
{
    /** Currency symbol of the logged-in user's company (falls back to PKR). */
    protected function currencySymbol(): string
    {
        return auth()->user()?->company?->currency?->symbol ?: 'PKR';
    }
}
