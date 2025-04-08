<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function indexCurrency()
    {
        $currencies = Currency::with('country')->paginate(10);
        $countries = Country::all();
        return view('Vendor_&_Finance.Currency.index', compact('currencies', 'countries'));
    }

    public function storeCurrency(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'sign' => 'required|string|max:10',
            'sign_placement' => 'required|in:before,after',
            'currency_name_in_words' => 'required|string|max:255',
            'digits_after_decimal' => 'required|integer|min:0|max:10',
        ]);

        try {
            
            Currency::create($validated);

            return redirect()
                ->route('list_currency')
                ->with('success', 'Currency added successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to add currency')
                ->withInput();
        }
    }

    public function updateCurrency(Request $request, string $id)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'sign' => 'required|string|max:10',
            'sign_placement' => 'required|in:before,after',
            'currency_name_in_words' => 'required|string|max:255',
            'digits_after_decimal' => 'required|integer|min:0|max:10',
        ]);

        try {
            $currency = Currency::findOrFail($id);
            $currency->update($validated);
            return redirect()
                ->route('list_currency')
                ->with('success', 'Currency updated successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update currency')
                ->withInput();
        }
    }

    public function deleteCurrency($id)
    {
        try {
            $currency = Currency::findOrFail($id);
            $currency->delete();
            return redirect()
                ->route('list_currency')
                ->with('success', 'Currency deleted successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete currency');
        }
    }
}