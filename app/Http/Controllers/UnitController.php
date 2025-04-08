<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    // Display a list of units
    public function indexUnit() {
        $units = Unit::paginate(10); // Paginate to show 10 units per page
        return view('unit.index', compact('units'));
    }

    // Store a new unit
    public function storeUnit(Request $request){
        // Validate incoming request data (only name is required now)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create the unit and store it in the database (status is default to active)
        Unit::create([
            'name' => $validated['name'],
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Unit created successfully.');
    }

    // Update an existing unit
    public function updateUnit(Request $request, string $id){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $unit = Unit::findOrFail($id);
        $unit->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('list_unit')->with('success', 'Unit updated successfully.');
    }

    // Delete a unit
    public function deleteUnit($id){
        // Find the unit by ID and delete it
        $unit = Unit::findOrFail($id);
        $unit->delete();

        // Redirect back with a success message
        return redirect()->route('list_unit')->with('success', 'Unit deleted successfully.');
    }
}
