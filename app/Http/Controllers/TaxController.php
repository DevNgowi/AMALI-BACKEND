<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaxRequest;
use App\Http\Requests\UpdateTaxRequest;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{

    public function listTax()
    {
        $taxOptions = Tax::all();
        return response()->json($taxOptions);
    }
    public function indexTax()
    {
        $taxes = Tax::paginate(10);
        return view('Financial_Settings.Tax.index', compact('taxes'));

    }

    public function storeTax(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'tax_type' => 'required|string',
            'tax_mode' => 'required|string',
            'tax_percentage' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
        ]);

        Tax::create($request->all());
        return redirect()->route('list_tax')->with('success', 'Tax created successfully.');

    }


    public function updateTax(Request $request, string $id)
    {
        $tax = Tax::findOrFail($id);
        $request->validate([

            'name' => 'required|string|max:255',
            'tax_type' => 'required|string',
            'tax_mode' => 'required|string',
            'tax_percentage' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
        ]);
        $tax->update($request->all());
        return redirect()->route('list_tax')->with('success', 'Tax updated successfully.');

    }

    public function deleteTax($id)
    {
        $tax = Tax::findOrFail($id);
        $tax->delete();
        return redirect()->route('list_tax')->with('success', 'Tax deleted successfully.');

    }

}
