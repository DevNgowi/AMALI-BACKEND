<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemBrandRequest;
use App\Http\Requests\UpdateItemBrandRequest;
use App\Models\ItemBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemBrandController extends Controller
{

    public function storeItemBrand(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:item_brands,name',
            'description' => 'nullable|string',
        ]);

        $brand = ItemBrand::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('create_item');
    }

}
