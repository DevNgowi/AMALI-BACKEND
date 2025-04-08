<?php
namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\DiscountType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscountController extends Controller
{
  public function indexDiscount()
  {
    $discountTypes = DiscountType::all();
    $discounts = Discount::all();
    // dd($discounts);

    return view('Financial_Settings.Discount.index', compact('discounts', 'discountTypes'));
  }

public function storeDiscount(Request $request)
{

    try {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:discounts,code',
            'discount_type_id' => 'required|exists:discount_types,id',
            'value' => 'required|numeric',
            'minimum_purchase_amount' => 'nullable|numeric',
            'maximum_discount_amount' => 'nullable|numeric',
            'is_active' => 'boolean',
            'is_combinable' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'usage_limit' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $discount = Discount::create($validatedData);
        
        return redirect()->back()->with('success', 'Discount created successfully.');
    } catch (\Exception $e) {
        \Log::error('Error creating discount: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Failed to create discount: ' . $e->getMessage())
            ->withInput();
    }
}

  public function updateDiscount(Request $request, $id)
  {

    $request->validate([
      'name' => 'required|string|max:255',
      'code' => 'nullable|string|unique:discounts,code,' . $id,
      'discount_type_id' => 'required|exists:discount_types,id',
      'value' => 'required|numeric',
      'minimum_purchase_amount' => 'nullable|numeric',
      'maximum_discount_amount' => 'nullable|numeric',
      'is_active' => 'boolean',
      'is_combinable' => 'boolean',
      'starts_at' => 'nullable|date',
      'expires_at' => 'nullable|date|after:starts_at',
      'usage_limit' => 'nullable|integer',
      'description' => 'nullable|string',
    ]);

    $discount = Discount::findOrFail($id);
    $discount->update([
      'name' => $request->name,
      'code' => $request->code,
      'discount_type_id' => $request->discount_type_id,
      'value' => $request->value,
      'minimum_purchase_amount' => $request->minimum_purchase_amount,
      'maximum_discount_amount' => $request->maximum_discount_amount,
      'is_active' => $request->is_active ?? true,
      'is_combinable' => $request->is_combinable ?? false,
      'starts_at' => $request->starts_at,
      'expires_at' => $request->expires_at,
      'usage_limit' => $request->usage_limit,
      'description' => $request->description,
      'updated_by' => Auth::id(),
    ]);
    return redirect()->back()->with('success', 'Discount updated successfully.');
  }
  public function deleteDiscount($id)
  {
    $discount = Discount::findOrFail($id);
    $discount->delete();
    return redirect()->back()->with('success', 'Discount deleted successfully.');
  }
}