<?php

namespace App\Http\Controllers;

use App\Models\Reason;
use App\Models\ReasonType;
use Illuminate\Http\Request;

class ReasonController extends Controller
{
    public function indexReason()
    {
        // Get all reasons with their associated reason types
        $reasons = Reason::with('reasonType')->get();
        // Get all reason types
        $reasonTypes = ReasonType::all();
        
        // Return the view with reasons and reason types
        return view('Financial_Settings.Reason.index', compact('reasons', 'reasonTypes'));
    }

    public function storeReason(Request $request)
    {
        $request->validate([
            'reason_type_id' => 'required|exists:reason_types,id',  
            'description' => 'required|string|max:255',
        ]);
        
        Reason::create([
            'reason_type_id' => $request->reason_type_id,
            'description' => $request->description,
        ]);
        
        return redirect()->route('list_reason')->with('success', 'Reason added successfully!');
    }

    public function updateReason(Request $request, $id)
    {
        $reason = Reason::findOrFail($id);
        $request->validate([
            'reason_type_id' => 'required|exists:reason_types,id', 
            'description' => 'required|string|max:255',
        ]);
        
        $reason->update([
            'reason_type_id' => $request->reason_type_id,
            'description' => $request->description,
        ]);
        
        return redirect()->route('list_reason')->with('success', 'Reason updated successfully!');
    }

    public function deleteReason($id)
    {
        $reason = Reason::findOrFail($id);
        $reason->delete();
        return redirect()->route('list_reason')->with('success', 'Reason deleted successfully!');
    }
}
