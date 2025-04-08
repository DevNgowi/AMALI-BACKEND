<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexRoles()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createRoles()
    {
        return view('roles.auto_generate.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeRoles(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);
    
        $role = new Role();
        $role->name = $request->input('name');
        $role->save();
        return redirect()->back()->with('success', 'Role created successfully!');
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editRoles(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateRoles(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        $role = Role::findOrFail($id);
        $role->name = $request->input('name');
        $role->save();
    
        return redirect()->back()->with('success', 'Role updated successfully!');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function deleteRoles(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->back()->with('success', 'Role deleted successfully!');
    }
    
    
}
