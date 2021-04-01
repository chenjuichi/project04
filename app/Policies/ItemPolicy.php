<?php

namespace App\Policies;

use App\User;
use App\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    //public function view(User $user, User $model)
    public function view(User $user)
    {
        $permission = Permission::where('slug', 'manage-users')->first();
        return $user->hasRole($permission->roles);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $permission = Permission::where('slug', 'create-tasks')->first();
        return $user->hasRole($permission->roles);        
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    //public function update(User $user, User $model)
    public function update(User $user)
    {
        $permission = Permission::where('slug', 'update-item')->first();
        return $user->hasRole($permission->roles);        
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    //public function delete(User $user, User $model)
    public function delete(User $user)
    {
        $permission = Permission::where('slug', 'delete-item')->first();
        return $user->hasRole($permission->roles);        
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }
}
