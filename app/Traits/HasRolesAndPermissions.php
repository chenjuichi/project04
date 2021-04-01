<?php

namespace App\Traits;

use App\Role;
use App\Permission;

trait HasRolesAndPermissions
{
    /**
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles');
    }

    //預載
    public function getAllUsersAndUserRoles()
    {
        //return $this->with('roles')->get();
        $allUsersAndUserRoles = $this->with('roles')->get();
        return response()->json(["allUsersAndUserRoles" => $allUsersAndUserRoles]);
    }

    public function assignUserWithRole($name, $role_slug)
    {
        $role = Role::where('slug', $role_slug)->first();
        $user = $this->where('name', $name)->first();

        $user->roles()->attach($role);
    }

    /**
     * @return mixed
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'users_permissions');
    }

    /**
     * @param  mixed ...$roles
     * @return bool
     */
    public function hasRole(...$roles)
    {
        foreach ($roles as $role) {
            if ($this->roles->contains('slug', $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param  $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        //return (bool) $this->permissions->where('slug', $permission->slug)->count();
        return (bool) $this->permissions->where('slug', $permission)->count();
    }

    /**
     * @param  $permission
     * @return bool
     */
    public function hasPermissionTo($permission)
    //protected function hasPermissionTo($permission)
    {
        //return $this->hasPermission($permission);
        return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
    }

    /**
     * @param  $permission
     * @return bool
     */
    public function hasPermissionThroughRole($permission)
    {
        //$tt = $permission->roles->first();
        $pm = Permission::find(1);
        foreach ($pm->roles as $role) {
            if ($this->roles->contains($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param  array $permissions
     * @return mixed
     */
    public function getAllPermissions(array $permissions)
    {
        return Permission::whereIn('slug', $permissions)->get();
    }

    /**
     * @param  mixed ...$permissions
     * @return $this
     */
    public function givePermissionsTo(...$permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        if ($permissions === null) {
            return $this;
        }
        $this->permissions()->saveMany($permissions);
        return $this;
    }

    /**
     * @param  mixed ...$permissions
     * @return $this
     */
    public function deletePermissions(...$permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        $this->permissions()->detach($permissions);
        return $this;
    }

    /**
     * @param  mixed ...$permissions
     * @return HasRolesAndPermissions
     */
    public function refreshPermissions(...$permissions)
    {
        $this->permissions()->detach();
        return $this->givePermissionsTo($permissions);
    }
}
