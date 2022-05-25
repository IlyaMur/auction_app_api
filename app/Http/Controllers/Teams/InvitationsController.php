<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\InvitationInterface;

class InvitationsController extends Controller
{
    public function __construct(protected InvitationInterface $invitation)
    {

    }

    public function invite(Request $request, $teamId)
    {

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
}
