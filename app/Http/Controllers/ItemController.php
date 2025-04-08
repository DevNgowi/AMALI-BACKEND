<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Barcode;
use App\Models\BrandApplicableItem;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\ItemBarcode;
use App\Models\ItemBrand;
use App\Models\ItemCost;
use App\Models\ItemGroup;
use App\Models\ItemImage;
use App\Models\ItemPrice;
use App\Models\ItemStock;
use App\Models\ItemStore;
use App\Models\ItemTax;
use App\Models\ItemType;
use App\Models\ItemUnit;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Tax;
use App\Models\Unit;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexItem()
    {
        $items = Item::with([
            'category',
            'itemType',
            'itemGroup',
            'barcodes',
            'stocks',
            'itemCosts',
            'taxes',
            'units'
        ])
            ->latest()
            ->get();

        return view('items.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createItem()
    {
        $categories = Category::all();
        $units = Unit::all();
        $item_types = ItemType::all();
        $item_groups = ItemGroup::all();
        $item_brands = ItemBrand::all();

        return view('items.auto_generate.create', compact('categories', 'units', 'item_types', 'item_groups', 'item_brands'));
    }

    /**
     * Store a newly created resource in storage.
     */


    public function storeItem(Request $request)
    {

        try {
            \Log::info('Starting item creation process', ['request_data' => $request->all()]);

            // Clean up nullable values
            $request->merge([
                'brand_id' => $request->brand_id === 'None' ? null : $request->brand_id,
                'item_group_id' => $request->item_group_id === 'None' ? null : $request->item_group_id,
                'tax_id' => collect($request->tax_id)->map(fn($tax) => $tax === 'None' ? null : $tax)->toArray(),
            ]);

            // Validate request data
            $validatedData = $request->validate([
                'name' => 'required|string',
                'barcode' => 'required|string|unique:barcodes,code',
                'category_id' => 'required|integer|exists:categories,id',
                'item_type_id' => 'required|integer|exists:item_types,id',
                'item_group_id' => 'nullable|integer|exists:item_groups,id',
                'buying_unit_id' => 'required|integer|exists:units,id',
                'selling_unit_id' => 'required|integer|exists:units,id',
                'exprire_date' => 'nullable|date',
                'brand_id' => 'nullable|integer|exists:item_brands,id',
                'item_image' => 'nullable|file|image|max:2048',

                // Store related validations
                'store_id' => 'required|array',
                'store_id.*' => 'required|integer|exists:stores,id',

                'min_quantity' => 'required|array',
                'min_quantity.*' => 'required|numeric|min:0',

                'max_quantity' => 'required|array',
                'max_quantity.*' => 'required|numeric|min:0',

                'stock_quantity' => 'required|array',
                'stock_quantity.*' => 'required|numeric|min:0',

                'purchase_rate' => 'required|array',
                'purchase_rate.*' => 'required|numeric|min:0',

                'selling_price' => 'required|array',
                'selling_price.*' => 'required|numeric|min:0',

                'tax_id' => 'nullable|array',
                'tax_id.*' => 'nullable|integer|exists:taxes,id',
            ]);

            DB::beginTransaction();

            try {
                // Create barcode
                $barcode = Barcode::create(['code' => $request->barcode]);

                // Handle image upload
                $imagePath = null;
                if ($request->hasFile('item_image')) {
                    $imagePath = $request->file('item_image')->store('uploads/item_images', 'public');
                }

                // Create item
                $item = Item::create([
                    'name' => $request->name,
                    'category_id' => $request->category_id,
                    'item_type_id' => $request->item_type_id,
                    'item_group_id' => $request->item_group_id,
                    'exprire_date' => $request->exprire_date,
                ]);

                // Create item barcode relation
                ItemBarcode::create([
                    'item_id' => $item->id,
                    'barcode_id' => $barcode->id,
                ]);

                // Create brand relation if exists
                if ($request->brand_id) {
                    BrandApplicableItem::create([
                        'item_id' => $item->id,
                        'brand_id' => $request->brand_id,
                    ]);
                }

                // Process store relationships
                foreach ($request->store_id as $index => $storeId) {
                    // Create store relationship
                    ItemStore::create([
                        'item_id' => $item->id,
                        'store_id' => $storeId,
                    ]);

                    // Create stock record
                    $stock = Stock::create([
                        'item_id' => $item->id,
                        'store_id' => $storeId,
                        'min_quantity' => $request->min_quantity[$index],
                        'max_quantity' => $request->max_quantity[$index],
                    ]);

                    // Create item stock relationship
                    ItemStock::create([
                        'item_id' => $item->id,
                        'stock_id' => $stock->id,
                        'stock_quantity' => $request->stock_quantity[$index]
                    ]);

                    // Create purchase cost record
                    ItemCost::create([
                        'item_id' => $item->id,
                        'store_id' => $storeId,
                        'unit_id' => $request->buying_unit_id,
                        'amount' => $request->purchase_rate[$index],
                    ]);

                    // Create selling price record
                    ItemPrice::create([
                        'item_id' => $item->id,
                        'store_id' => $storeId,
                        'unit_id' => $request->selling_unit_id,
                        'amount' => $request->selling_price[$index],
                    ]);

                    // Create tax relationships if any
                    if (isset($request->tax_id[$index]) && $request->tax_id[$index] !== 'None') {
                        ItemTax::create([
                            'item_id' => $item->id,
                            'store_id' => $storeId,
                            'tax_id' => $request->tax_id[$index],
                        ]);
                    }
                }

                // Create image relation if exists
                if ($imagePath) {
                    $image = Image::create(['file_path' => $imagePath]);
                    ItemImage::create([
                        'item_id' => $item->id,
                        'image_id' => $image->id,
                    ]);
                }

                // Create unit relationships
                ItemUnit::create([
                    'item_id' => $item->id,
                    'buying_unit_id' => $request->buying_unit_id,
                    'selling_unit_id' => $request->selling_unit_id,
                ]);

                DB::commit();
                \Log::info('Item creation completed successfully', ['item_id' => $item->id]);

                return redirect()->route('list_item')->with('success', 'Item added successfully!');

            } catch (\Exception $e) {
                DB::rollback();
                \Log::error('Error in item creation process', [
                    'error_message' => $e->getMessage(),
                    'stack_trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Unexpected error', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error occurred while adding item: ' . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editItem($id)
    {
        $item = Item::with([
            'category',
            'itemType',
            'itemGroup',
            'barcodes',
            'stocks',
            'itemCosts',
            'taxes',
            'itemPrices',
            'itemStocks'
        ])->findOrFail($id);


        $categories = Category::all();
        $units = Unit::all();
        $item_types = ItemType::all();
        $item_groups = ItemGroup::all();
        $item_brands = ItemBrand::all();
        $stores = Store::all(); // Get all stores
        $taxes = Tax::all(); // Get all taxes

        // Return the view with the necessary data
        return view('items.auto_generate.edit', compact(
            'categories',
            'units',
            'item_types',
            'item_groups',
            'item_brands',
            'stores',
            'taxes',
            'item'
        ));
    }


    public function updateItem(Request $request, $id)
    {
        try {
            \Log::info('Starting item update process', ['item_id' => $id, 'request_data' => $request->all()]);

            // Validate the incoming request
            $validatedData = $request->validate([
                'name' => 'required|string',
                'barcode' => 'required|string',
                'category_id' => 'required|integer|exists:categories,id',
                'item_type_id' => 'required|integer|exists:item_types,id',
                'buying_unit_id' => 'required|integer|exists:units,id',
                'selling_unit_id' => 'required|integer|exists:units,id',
                'exprire_date' => "nullable|date",
                'brand_id' => 'nullable|integer|exists:item_brands,id',
                'item_image' => 'nullable|file|image|max:2048',
                'store_id' => 'required|array',
                'store_id.*' => 'required|integer|exists:stores,id',
                'min_quantity' => 'required|array',
                'min_quantity.*' => 'required|numeric|min:0',
                'max_quantity' => 'required|array',
                'max_quantity.*' => 'required|numeric|min:0',
                'stock_quantity' => 'required|array',
                'stock_quantity.*' => 'required|numeric|min:0',
                'purchase_rate' => 'required|array',
                'purchase_rate.*' => 'required|numeric|min:0',
                'selling_price' => 'required|array',
                'selling_price.*' => 'required|numeric|min:0',
                'tax_id' => 'nullable|array',
                'tax_id.*' => 'nullable|integer|exists:taxes,id',
            ]);

            \Log::info('Validation passed successfully');

            DB::beginTransaction();

            try {
                $item = Item::findOrFail($id);
                \Log::info('Item found', ['item_id' => $item->id]);

                // Handle barcode update
                $existingItemBarcode = ItemBarcode::where('item_id', $item->id)->first();
                if ($existingItemBarcode) {
                    $barcode = Barcode::find($existingItemBarcode->barcode_id);
                    if ($barcode && $barcode->code !== $request->barcode) {
                        // Update existing barcode if changed
                        $barcode->update(['code' => $request->barcode]);
                        \Log::info('Barcode updated', ['barcode_id' => $barcode->id, 'code' => $request->barcode]);
                    } elseif (!$barcode) {
                        // Create new barcode if it doesn't exist
                        $barcode = Barcode::create(['code' => $request->barcode]);
                        $existingItemBarcode->update(['barcode_id' => $barcode->id]);
                        \Log::info('New barcode created and linked', ['barcode_id' => $barcode->id]);
                    }
                } else {
                    // Create new barcode and link it to item
                    $barcode = Barcode::firstOrCreate(['code' => $request->barcode]);
                    ItemBarcode::create([
                        'item_id' => $item->id,
                        'barcode_id' => $barcode->id
                    ]);
                    \Log::info('New barcode and item_barcode created', ['barcode_id' => $barcode->id, 'item_id' => $item->id]);
                }

                // Handle image upload
                if ($request->hasFile('item_image')) {
                    \Log::info('Processing item image upload');
                    $imagePath = $request->file('item_image')->store('uploads/item_images', 'public');
                    \Log::info('Image uploaded successfully', ['path' => $imagePath]);

                    if ($item->image) {
                        $item->image->update(['file_path' => $imagePath]);
                    } else {
                        $image = Image::create(['file_path' => $imagePath]);
                        ItemImage::create(['item_id' => $item->id, 'image_id' => $image->id]);
                    }
                }

                // Update item details
                $item->update([
                    'name' => $request->name,
                    'category_id' => $request->category_id,
                    'item_type_id' => $request->item_type_id,
                    'exprire_date' => $request->exprire_date,
                ]);

                // Update brand relation
                if ($request->brand_id) {
                    $brandRelation = BrandApplicableItem::where('item_id', $item->id)->first();
                    if ($brandRelation) {
                        $brandRelation->update(['brand_id' => $request->brand_id]);
                    } else {
                        BrandApplicableItem::create(['item_id' => $item->id, 'brand_id' => $request->brand_id]);
                    }
                }

                foreach ($request->store_id as $index => $storeId) {
                    // Stock handling
                    $stock = Stock::where('item_id', $item->id)
                        ->where('store_id', $storeId)
                        ->first();

                    if (!$stock) {
                        $stock = new Stock([
                            'store_id' => $storeId,
                            'item_id' => $item->id,
                            'min_quantity' => $request->min_quantity[$index],
                            'max_quantity' => $request->max_quantity[$index],
                        ]);
                        $stock->save();
                    } else {
                        $stock->update([
                            'min_quantity' => $request->min_quantity[$index],
                            'max_quantity' => $request->max_quantity[$index],
                        ]);
                    }

                    // Item Stock handling
                    $itemStock = ItemStock::where('item_id', $item->id)
                        ->where('stock_id', $stock->id)
                        ->first();

                    if (!$itemStock) {
                        $itemStock = new ItemStock([
                            'stock_id' => $stock->id,
                            'item_id' => $item->id,
                            'stock_quantity' => $request->stock_quantity[$index]
                        ]);
                        $itemStock->save();
                    } else {
                        $itemStock->update(['stock_quantity' => $request->stock_quantity[$index]]);
                    }

                    // Item Cost handling
                    $itemCost = ItemCost::where('item_id', $item->id)
                        ->where('store_id', $storeId)
                        ->first();

                    if (!$itemCost) {
                        $itemCost = new ItemCost([
                            'store_id' => $storeId,
                            'unit_id' => $request->buying_unit_id,
                            'amount' => $request->purchase_rate[$index]
                        ]);
                        $item->itemCosts()->save($itemCost);
                    } else {
                        $itemCost->update(['amount' => $request->purchase_rate[$index]]);
                    }

                    // Item Price handling
                    $itemPrice = ItemPrice::where('item_id', $item->id)
                        ->where('store_id', $storeId)
                        ->first();

                    if (!$itemPrice) {
                        $itemPrice = new ItemPrice([
                            'store_id' => $storeId,
                            'unit_id' => $request->selling_unit_id,
                            'amount' => $request->selling_price[$index]
                        ]);
                        $item->itemPrices()->save($itemPrice);
                    } else {
                        $itemPrice->update(['amount' => $request->selling_price[$index]]);
                    }

                    // Item Tax handling
                    $itemTax = ItemTax::where('item_id', $item->id)
                        ->where('store_id', $storeId)
                        ->first();

                    if (!$itemTax && isset($request->tax_id[$index])) {
                        $itemTax = new ItemTax([
                            'store_id' => $storeId,
                            'tax_id' => $request->tax_id[$index]
                        ]);
                        $item->itemTaxes()->save($itemTax);
                    } elseif ($itemTax) {
                        if (isset($request->tax_id[$index])) {
                            $itemTax->update(['tax_id' => $request->tax_id[$index]]);
                        } else {
                            $itemTax->delete();
                        }
                    }
                }

                DB::commit();
                \Log::info('Item update completed successfully', ['item_id' => $item->id]);

                return redirect()->route('list_item')->with('success', 'Item updated successfully!');
            } catch (\Exception $e) {
                DB::rollback();
                \Log::error('Error in item update process', [
                    'error_message' => $e->getMessage(),
                    'stack_trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Unexpected error', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error occurred while updating item. ' . $e->getMessage());
        }
    }

    public function deleteItem($id)
    {
        try {
            DB::beginTransaction();

            $item = Item::findOrFail($id);
            BrandApplicableItem::where('item_id', $item->id)->delete();
            ItemUnit::where('item_id', $item->id)->delete();
            if ($item->image) {
                Storage::disk('public')->delete($item->image->file_path);

                $item->image->delete();
            }

            $item->delete();

            DB::commit();

            return redirect()->route('list_item')->with('success', 'Item deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Error occurred while deleting item. ' . $e->getMessage());
        }
    }



}
