<?php

namespace App\Repositories\Contracts;

interface InvitationInterface
{
    public function addUserToTeam($team, $userId);
    public function removeUserFromTeam($team, $userId);
}
