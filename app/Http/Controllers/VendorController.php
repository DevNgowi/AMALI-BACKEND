<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function indexVendor()
    {
        $vendors = Vendor::with(['city', 'country'])->get();
        $cities = City::all();
        $countries = Country::all();
        return view('vendors.index', compact('vendors', 'cities', 'countries'));
    }

    public function storeVendor(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:vendors,email',
            'phone' => 'required',
            'address' => 'required',
            'city_id' => 'required|exists:cities,id',
            'country_id' => 'required|exists:countries,id',
            'contact_person' => 'required',
            'tin' => 'nullable',
            'vrn' => 'nullable',
            'status' => 'required|in:active,inactive'
        ]);

        Vendor::create($request->all());
        return redirect()->route('list_vendors')->with('success', 'Vendor created successfully');
    }

    public function editVendor($id)
    {
        $vendor = Vendor::find($id);
        if (!$vendor) {
            return redirect()->route('list_vendors')->with('error', 'Vendor not found');
        }
    
        $cities = City::all();
        $countries = Country::all();
    
        return view('vendors.auto_generate.index', compact('vendor', 'cities', 'countries'));
    }
    
    public function updateVendor(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:vendors,email,'.$id,
            'phone' => 'required',
            'address' => 'required',
            'city_id' => 'required|exists:cities,id',
            'country_id' => 'required|exists:countries,id',
            'contact_person' => 'required',
            'tin' => 'required',
            'vrn' => 'required',
            'status' => 'required|in:active,inactive'
        ]);

        $vendor = Vendor::findOrFail($id);
        $vendor->update($request->all());
        return redirect()->route('list_vendors')->with('success', 'Vendor updated successfully');
    }

    public function deleteVendor($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();
        return redirect()->route('list_vendors')->with('success', 'Vendor deleted successfully');
    }
}
