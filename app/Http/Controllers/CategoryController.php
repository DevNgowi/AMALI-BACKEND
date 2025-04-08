<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ItemGroup;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function indexCategory()
    {
        $categories = Category::with('itemGroup:id,name')->latest()->get(['id', 'name', 'item_group_id']);
        $item_groups = ItemGroup::all();
    
        return view('category.index', compact('categories', 'item_groups'));
    }
    

    public function indexItemCategoryLocal()
    {
        $categories = Category::with('itemGroup')
            ->select([
                'categories.id as category_id',
                'categories.name as category_name',
            ])
            ->join('item_groups', 'categories.item_group_id', '=', 'item_groups.id') // Added join
            ->selectRaw('item_groups.name as item_group_name') // Added item group name
            ->get();
    
        return response()->json([
            'data' => $categories,
            'message' => 'Successfully retrieved categories',
            'code' => 200 
        ]);
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'item_group_id' => 'required|exists:item_groups,id'
        ]);

        Category::create([
            'name' => $request->name,
            'item_group_id' => $request->item_group_id,
        ]);
        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function updateCategory(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'item_group_id' => 'required|exists:item_groups,id'
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'item_group_id' => $request->item_group_id,
        ]);
        return redirect()->route('list_category')->with('success', 'Category updated successfully.');
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('list_category')->with('success', 'Category deleted successfully.');
    }
}