<?php

namespace App\Http\Controllers\Teams;

use App\Models\Team;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Repositories\Contracts\TeamInterface;
use App\Repositories\Contracts\UserInterface;
use App\Repositories\Contracts\InvitationInterface;

class TeamsController extends Controller
{
    public function __construct(
        protected TeamInterface $teams,
        protected UserInterface $users,
        protected InvitationInterface $invitations
    ) {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return TeamResource
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name'],
        ]);

        // create team in the db
        $team = $this->teams->create([
            'owner_id' => auth()->id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return new TeamResource($team);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Find team by the id.
     *
     * @param  int  $id
     * @return TeamResource
     */
    public function findById($id)
    {
        $team = $this->teams->find($id);

        return new TeamResource($team);
    }

    /**
     * Find team by the slug.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function findBySlug($id)
    {
        //
    }

    /**
     * Fetch teams where current user is a member.
     *
     * @return TeamResource
     */
    public function fetchUserTeams()
    {
        $teams = $this->teams->fetchUserTeams();

        return TeamResource::collection($teams);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return TeamResource
     */
    public function update(Request $request, $id)
    {
        $team = $this->teams->find($id);

        $this->authorize($team);

        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', "unique:teams,name,{$id}"]
        ]);

        $team = $this->teams->update($id, [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return new TeamResource($team);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->authorize($this->teams->find($id));
        $this->teams->delete($id);

        return response()->json(['message' => 'Team was deleted']);
    }

    /**
     * Remove the user from the team.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFromTeam($teamId, $userId)
    {
        $team = $this->teams->find($teamId);

        if ($this->users->find($userId)->isOwnerOfTeam($team)) {
            return response()->json(['message' => 'You are the team owner'], 401);
        }

        if (!auth()->user()->isOwnerOfTeam($team) && auth()->id() != $userId) {
            return response()->json(['message' => 'You can\'t do this'], 401);
        }

        $this->invitations->removeUserFromTeam($team, $userId);
        return response()->json(['message' => 'Success']);
    }
}
