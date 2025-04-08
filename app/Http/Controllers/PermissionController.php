<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
  
    public function indexPermissions(Request $request)
    {
        $permissions = Permission::all();
        return view('permissions.index', compact('permissions'));

    }


    /**
     * Remove the specified permission from storage.
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id); // Find permission by ID
        $permission->delete(); // Delete permission

        // Redirect with success message
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully');
    }

  

    /**
     * @param Request $request
     * @param $permissionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignToRole(Request $request, $permissionId): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'role' => 'required|exists:roles,name', // Validate that the role exists
        ]);

        $permission = Permission::findOrFail($permissionId); // Find permission by ID
        $role = \Spatie\Permission\Models\Role::findByName($request->role); // Find role by name

        $role->givePermissionTo($permission); // Assign permission to role

        return response()->json([
            'success' => 'Permission assigned to role successfully',
        ]);

    }

    /**
     * Assign permission to a user.
     */
    public function assignToUser(Request $request, $permissionId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id', // Validate that the user exists
        ]);

        $permission = Permission::find($permissionId); // Find permission by ID
        $user = \App\Models\User::find($request->user_id); // Find user by ID

        if(!$user){
            return response()->json([
                'error' => 'User not found',
            ],404);
        }

        $user->givePermissionTo($permission); // Assign permission to user

        return response()->json([
            'success' => 'Permission assigned to user successfully',
        ]);

    }
}
