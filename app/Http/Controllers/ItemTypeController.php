<?php

namespace App\Http\Controllers;

use App\Models\ItemType;
use Illuminate\Http\Request;

class ItemTypeController extends Controller
{
    public function indexItemType()
    {
        $item_types = ItemType::paginate(10);
        return view('item_types.index', compact('item_types'));
    }

    public function storeItemType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:item_types,name',
        ]);

        $item_type = new ItemType();
        $item_type->name = $request->name;
        $item_type->save();

        return redirect()->route('list_item_type')->with('success', 'Item Type added successfully!');
    }

    public function updateItemType(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:item_types,name,' . $id,
        ]);

        $item_type = ItemType::findOrFail($id);
        $item_type->name = $request->name;
        $item_type->save();

        return redirect()->route('list_item_type')->with('success', 'Item Type updated successfully!');
    }

    public function deleteItemType($id)
    {
        $item_type = ItemType::findOrFail($id);
        $item_type->delete();

        return redirect()->route('list_item_type')->with('success', 'Item Type deleted successfully!');
    }
}

