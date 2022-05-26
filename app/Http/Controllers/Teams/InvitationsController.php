<?php

namespace App\Http\Controllers\Teams;

use App\Models\Team;
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
        protected UserInterface $users
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
        $invitation = $this->invitations->find($id);

        $this->authorize($invitation);

        $recipient = $this->users->findByEmail($invitation->recipient_email);

        Mail::to($invitation->recipient_email)
            ->send(new SendInvitationToJoinTeam($invitation, !is_null($recipient)));

        return response()->json(['message' => 'Invitation resent']);
    }

    public function respond(Request $request, $id)
    {
        $this->validate($request, [
            'token' => ['required'],
            'decision' => ['required']
        ]);

        $invitation = $this->invitations->find($id);

        $this->authorize($invitation);

        if ($invitation->token !== $request->token) {
            return response()->json(['message' => 'Invalid Token'], 401);
        }

        if ($request->decision !== 'deny') {
            $this->invitations->addUserToTeam($invitation->team, auth()->id());
        }

        $invitation->delete();
        return response()->json(['message' => 'Successful']);
    }

    public function destroy($id)
    {
        $invitation = $this->invitations->find($id);

        $this->authorize($invitation);
        $invitation->delete();

        return response()->json(['message' => 'Deleted']);
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
