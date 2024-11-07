<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProposalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isWakil() || $user->isDireksi();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Proposal $proposal): bool
    {
        return $user->isWakil() || $user->isDireksi();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isWakil();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Proposal $proposal): bool
    {
        return $user->isWakil() || $user->isDireksi();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Proposal $proposal): bool
    {
        return $user->isWakil();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Proposal $proposal): bool
    {
        return $user->isWakil();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Proposal $proposal): bool
    {
        return $user->isWakil();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isWakil();
    }

    public function restoreAny(User $user): bool
    {
        return $user->isWakil();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->isWakil();
    }
}
