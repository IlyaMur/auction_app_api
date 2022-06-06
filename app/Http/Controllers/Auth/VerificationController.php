<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Repositories\Contracts\UserInterface;

class VerificationController extends Controller
{
    public function __construct(protected UserInterface $users)
    {
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request, User $user)
    {
        if (!URL::hasValidSignature($request)) {
            return response()->json(
                [
                    'errors' => [
                        'message' => 'Invalid verification link or signature'
                    ]
                ],
                422
            );
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(
                [
                    'errors' => [
                        'message' => 'Email address already verified'
                    ]
                ],
                422
            );
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['message' => 'Email successfully verified']);
    }

    public function resend(Request $request)
    {
        $this->validate($request, [
            'email' => ['email', 'required']
        ]);

        $user = $this->users->findWhereFirst('email', $request->email);

        if (!$user) {
            return response()->json(['errors' => [
                'email' => 'No user could be found this email address'
            ]], 422);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(
                [
                    'errors' => [
                        'message' => 'Email address already verified'
                    ]
                ],
                422
            );
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status' => 'verification link resent']);
    }
}
