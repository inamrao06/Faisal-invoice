<?php
namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CurrencyController extends Controller
{
    public function index()
    {
        return view('settings.currencies.index', ['currencies' => Currency::orderBy('code')->paginate(15)]);
    }

    public function create()
    {
        return view('settings.currencies.form', ['currency' => new Currency(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        Currency::create($this->validated($request));
        return redirect()->route('settings.currencies.index')->with('success', 'Currency created.');
    }

    public function edit(Currency $currency)
    {
        return view('settings.currencies.form', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $currency->update($this->validated($request, $currency));
        return redirect()->route('settings.currencies.index')->with('success', 'Currency updated.');
    }

    private function validated(Request $request, ?Currency $currency = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'max:80'],
            'code' => ['required', 'max:10', Rule::unique('currencies')->ignore($currency)],
            'symbol' => ['nullable', 'max:10'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_default'] = $request->boolean('is_default');
        $data['is_active'] = $request->boolean('is_active');
        if ($data['is_default']) Currency::where('id', '!=', $currency?->id)->update(['is_default' => false]);
        return $data;
    }
}
