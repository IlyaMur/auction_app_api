<?php

namespace App\Repositories\Eloquent;

use App\Models\Invitation;
use App\Repositories\Contracts\InvitationInterface;
use App\Repositories\Eloquent\BaseRepository;

class InvitationRepository extends BaseRepository implements InvitationInterface
{
    public function model()
    {
        return Invitation::class;
    }

    public function addUserToTeam($team, $userId)
    {
        $team->members()->attach($userId);
    }

    public function removeUserFromTeam($team, $userId)
    {
        $team->members()->detach($userId);
    }
}
