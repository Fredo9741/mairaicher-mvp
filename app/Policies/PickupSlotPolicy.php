<?php

namespace App\Policies;

use App\Models\PickupSlot;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PickupSlotPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['developer', 'maraicher']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PickupSlot $pickupSlot): bool
    {
        return in_array($user->role, ['developer', 'maraicher']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['developer', 'maraicher']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PickupSlot $pickupSlot): bool
    {
        return in_array($user->role, ['developer', 'maraicher']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PickupSlot $pickupSlot): bool
    {
        // Only developers can delete pickup slots
        return $user->role === 'developer';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PickupSlot $pickupSlot): bool
    {
        return $user->role === 'developer';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PickupSlot $pickupSlot): bool
    {
        return $user->role === 'developer';
    }
}
