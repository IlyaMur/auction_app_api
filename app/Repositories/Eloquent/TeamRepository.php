<?php

namespace App\Repositories\Eloquent;

use App\Models\Team;
use App\Repositories\Contracts\TeamInterface;
use App\Repositories\Eloquent\BaseRepository;

class TeamRepository extends BaseRepository implements TeamInterface
{
    public function model()
    {
        return Team::class;
    }

    public function fetchUserTeams()
    {
        return auth()->user()->teams;
    }
}
