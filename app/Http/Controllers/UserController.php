<?php

namespace App\Http\Controllers;

// use App\Models\Permission;
// use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function indexUsers()
    {
        $users = User::with('roles')->get();

        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function storeUsers(Request $request)
    {


        $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'email' => 'required|email|unique:users|max:255',
            'phone' => 'required|numeric|unique:users',
            'password' => 'required|string|min:6|same:confirmpassword',
            'pin' => 'required|digits:4',
            'role_id' => 'required|exists:roles,id',
        ]);


        try {
            $user = User::create([
                'fullname' => $request->fullname,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'pin' => $request->pin,
            ]);

            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $request->role_id,
            ]);

            session()->flash('success', 'User created successfully!');

            return redirect()->route('list_users');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }


    // edit users with permissions 
    public function editUsersWithPermission(Request $request, $user)
    {
        // Eager load necessary relationships
        $user = User::with(['roles.permissions', 'permissions'])->findOrFail($user);
        $roles = Role::all();
        $permissions = Permission::all();
    
        // Get all permissions assigned to user (both through roles and direct)
        $userPermissions = $user->getAllPermissions()->pluck('id')->unique()->toArray();
    
        // Define permission categories with their keywords
        $permissionCategories = [
            'Dashboard' => ['dashboard'],
            'Stores' => ['store'],
            'Inventory Management' => [
                'item category',
                'item group',
                'item type',
                'unit',
                'items',
                'cost & stock',
                'inventory'
            ],
            'Purchase Management' => [
                'purchase order',
                'good receive note',
                'goods returns',
                'goods issued note',
                'purchase'
            ],
            'POS' => ['pos'],
            'User Management' => [
                'user role',
                'users',
                'permissions',
                'roles'
            ],
            'Vendor & Finance' => [
                'vendors',
                'payments',
                'currency',
                'finance'
            ],
            'Financial Settings' => [
                'tax',
                'extra charges',
                'discounts',
                'reason',
                'financial'
            ],
            'Reports' => ['reports', 'reporting'],
            'Settings' => [
                'company details',
                'settings',
                'configuration'
            ],
            'Virtual Devices' => ['virtual devices', 'devices']
        ];
    
        // Initialize categorized permissions array
        $categorizedPermissions = array_fill_keys(array_keys($permissionCategories), []);
    
        // Categorize permissions
        foreach ($permissions as $permission) {
            $permissionName = strtolower($permission->name);
            $categorized = false;
    
            foreach ($permissionCategories as $category => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($permissionName, $keyword)) {
                        $categorizedPermissions[$category][] = $permission;
                        $categorized = true;
                        break 2;
                    }
                }
            }
    
            // If permission doesn't match any category, put it in Settings
            if (!$categorized) {
                $categorizedPermissions['Settings'][] = $permission;
            }
        }
    
        // Remove empty categories
        $categorizedPermissions = array_filter($categorizedPermissions, function($permissions) {
            return !empty($permissions);
        });
    
        // Sort permissions within each category
        foreach ($categorizedPermissions as &$categoryPermissions) {
            usort($categoryPermissions, function($a, $b) {
                return strcmp($a->name, $b->name);
            });
        }
    
        return view('users.auto_generate.index', compact(
            'user',
            'roles',
            'categorizedPermissions',
            'userPermissions'
        ));
    }
    public function updateUsersWithPermission(Request $request, User $user)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id . '|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id . '|max:255',
            'phone' => 'required|numeric|unique:users,phone,' . $user->id,
            'pin' => 'required|digits:4',
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);
    
        DB::beginTransaction();
        try {
            // Update user details
            $user->update([
                'fullname' => $request->fullname,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'pin' => $request->pin,
                'role_id' => $request->role_id
            ]);
    
            // Get the selected role
            $role = Role::findOrFail($request->role_id);
    
            // Remove existing role assignments
            $user->roles()->detach();
    
            // Assign new role
            $user->assignRole($role);
    
            // Get the role's default permissions
            $rolePermissions = DB::table('role_has_permissions')
                ->where('role_id', $role->id)
                ->pluck('permission_id')
                ->toArray();
    
            // Remove all existing direct permission assignments for the user
            DB::table('model_has_permissions')
                ->where('model_type', get_class($user))
                ->where('model_id', $user->id)
                ->delete();
    
            // Handle permissions
            $selectedPermissions = $request->input('permissions', []);
    
            // Prepare permissions to assign
            $permissionsToAssign = [];
    
            // Add role permissions
            foreach ($rolePermissions as $permissionId) {
                $permissionsToAssign[] = $permissionId;
            }
    
            // Add selected additional permissions
            foreach ($selectedPermissions as $permissionId) {
                if (!in_array($permissionId, $permissionsToAssign)) {
                    $permissionsToAssign[] = $permissionId;
                }
            }
    
            // Assign new permissions
            if (!empty($permissionsToAssign)) {
                $permissions = Permission::whereIn('id', $permissionsToAssign)->get();
                $user->syncPermissions($permissions);
            } else {
                // If no permissions, remove all
                $user->permissions()->detach();
            }
    
            DB::commit();
    
            session()->flash('success', 'User updated successfully!');
            return redirect()->route('list_users');
        } catch (\Exception $e) {
            DB::rollBack();
    
            Log::error('Error updating user', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
    
            return redirect()->back()->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }
    

    public function deleteUsers(User $user)
    {
        $user->delete();

        session()->flash('success', 'User deleted successfully!');
        return redirect()->route('list_users');
    }


}
