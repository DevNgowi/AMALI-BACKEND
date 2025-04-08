<?php

namespace App\Http\Controllers;

use App\Models\ItemGroup;
use App\Models\Category; // Assuming you have a Category model
use Illuminate\Http\Request;

class ItemGroupController extends Controller
{
    // Display a listing of the item groups
    public function indexItemGroup()
    {
        $item_groups = ItemGroup::latest()->get();
        return view('item_group.index', compact('item_groups'));
    }

    public function indexItemGroupLocal()
    {
        $item_groups = ItemGroup::select('id', 'name')->get();
        return response()->json([
            'data' => $item_groups,
            'message' => 'Successfully get group of items',
            'code' => 201
        ]);
    }


    

    // Store a new item group
    public function storeItemGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        ItemGroup::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Item Group added successfully!');
    }

    public function editItemGroup($id)
    {
        $item_group = ItemGroup::findOrFail($id);
        return response()->json(['item_group' => $item_group]);
    }

    // Update the specified item group in storage
    public function updateItemGroup(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $item_group = ItemGroup::findOrFail($id);
        $item_group->update([
            'name' => $request->name,
        ]);

        return redirect()->route('list_item_group')->with('success', 'Item Group updated successfully!');
    }

    public function deleteItemGroup($id)
    {
        $item_group = ItemGroup::findOrFail($id);
        $item_group->delete();
        return redirect()->route('list_item_group')->with('success', 'Item Group deleted successfully!');
    }
}
