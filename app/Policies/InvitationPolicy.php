<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Invitation;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvitationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invitation  $invitation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Invitation $invitation)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invitation  $invitation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Invitation $invitation)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invitation  $invitation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Invitation $invitation)
    {
        return $user->id === $invitation->sender_id;
    }

    /**
     * Determine whether the user can respond.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invitation  $invitation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function respond(User $user, Invitation $invitation)
    {
        return $user->email === $invitation->recipient_email;
    }

    /**
     * Determine whether the user can resend notification.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invitation  $invitation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function resend(User $user, Invitation $invitation)
    {
        return $user->isOwnerOfTeam($invitation->team);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invitation  $invitation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Invitation $invitation)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invitation  $invitation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Invitation $invitation)
    {
        //
    }
}
