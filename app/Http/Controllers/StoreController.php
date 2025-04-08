<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     
     public function indexStoreLocal()
    {
        $stores = DB::table('stores')
            ->select('id', 'name')
            ->distinct()
            ->get();
        return response()->json([
            'data' => $stores,
            'success' => true,
        ]);
    }


    public function listStore()
    {
        $stores = Store::all(['id', 'name']);
        return response()->json($stores);
    }

    public function indexStore()
    {
        $stores = Store::with('user')->get();
        
        // Check if a user is logged in before fetching users
        $users = Auth::check() ? User::all() : collect(); // Returns an empty collection if not logged in
    
        return view('store.index', compact('stores', 'users'));
    }
    


    /**
     * Store a newly created resource in storage.
     */
    public function storeStore(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'manager_id' => 'required|exists:users,id',
        ]);

        try {
            Store::create($validatedData);
            return redirect()->route('list_stores')->with('success', 'Store created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create store: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStore(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'manager_id' => 'required|exists:users,id',
        ]);

        try {
            $store = Store::findOrFail($id);
            $store->update($validatedData);
            return redirect()->route('list_stores')->with('success', 'Store updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update store: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteStore(string $id)
    {
        try {
            $store = Store::findOrFail($id);
            $store->delete();
            return redirect()->route('list_stores')->with('success', 'Store deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete store: ' . $e->getMessage());
        }
    }
}
