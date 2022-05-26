<?php

namespace App\Http\Controllers\Teams;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendInvitationToJoinTeam;
use App\Repositories\Contracts\TeamInterface;
use App\Repositories\Contracts\UserInterface;
use App\Repositories\Contracts\InvitationInterface;

class InvitationsController extends Controller
{
    public function __construct(
        protected InvitationInterface $invitations,
        protected TeamInterface $teams,
        protected UserInterface $user
    ) {
    }

    public function invite(Request $request, $teamId)
    {
        $team = $this->teams->find($teamId);

        $this->validate($request, ['email' => ['required', 'email']]);

        // check if the user owns the team
        if (!auth()->user()->isOwnerOfTeam($team)) {
            return response()->json(['email' => 'You are not the team owner'], 401);
        }
        // check if the email has pending invitation
        if ($team->hasPendingInvite($request->email)) {
            return response()->json(['email' => 'Email already has a pending invite'], 422);
        }

        $recipient = $this->users->findByEmail($request->email);
        // if recipient does not exist, send invite to join the team
        if (!$recipient) {
            $this->createInvitation(false, $team, $request->email);
            return response()->json(['message' => 'Invitation sent to user']);
        }

        // if the team already has the user
        if ($team->hasUser($recipient)) {
            return response()->json(['email' => 'User is already a team member'], 422);
        }

        $this->createInvitation(true, $team, $request->email);
        return response()->json(['message' => 'Invitation sent to user']);
    }

    public function resend($id)
    {
    }

    public function respond(Request $request, $teamId)
    {
    }

    public function destroy()
    {
    }

    protected function createInvitation(bool $isUserExists, Team $team, string $email)
    {
        $invitation = $this->invitations->create([
            'team_id' => $team->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $email,
            'token' => md5(uniqid(microtime())),
        ]);
        Mail::to($email)
            ->send(new SendInvitationToJoinTeam($invitation, $isUserExists));
    }
}