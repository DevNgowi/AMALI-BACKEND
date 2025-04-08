<?php 


namespace App\Http\Controllers;

use App\Models\ExtraCharge;
use App\Models\ExtraChargeType;
use App\Models\Tax;
use Illuminate\Http\Request;

class ExtraChargeController extends Controller
{
    public function indexExtraCharge()
    {
        $extraCharges = ExtraCharge::with('tax', 'extraChargeType')->get();
        $chargeTypes = ExtraChargeType::all();
        $taxes = Tax::all();
        return view('Financial_Settings.Extra_Charge.index', compact('extraCharges', 'chargeTypes', 'taxes'));
    }

    public function storeExtraCharge(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'tax_id' => 'nullable|exists:taxes,id', 
            'charge_type_id' => 'required|exists:extra_charge_types,id',
            'amount' => 'required|numeric|min:0',
        ]);

        ExtraCharge::create([
            'name' => $request->name,
            'tax_id' => $request->tax_id, 
            'charge_type_id' => $request->charge_type_id,
            'amount' => $request->amount,
        ]);

        return redirect()->back()->with('success', 'Extra charge added successfully.');
    }

    public function updateExtraCharge(Request $request, $id)
    {
        $extraCharge = ExtraCharge::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'tax_id' => 'nullable|exists:taxes,id', 
            'charge_type_id' => 'required|exists:extra_charge_types,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $extraCharge->update([
            'name' => $request->name,
            'tax_id' => $request->tax_id, 
            'charge_type_id' => $request->charge_type_id,
            'amount' => $request->amount,
        ]);
        return redirect()->back()->with('success', 'Extra charge updated successfully.');
    }

    public function deleteExtraCharge($id)
    {
        $extraCharge = ExtraCharge::findOrFail($id);
        $extraCharge->delete();
        return redirect()->back()->with('success', 'Extra charge deleted successfully.');
    }
}