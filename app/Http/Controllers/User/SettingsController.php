<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Rules\CheckSamePassword;
use App\Rules\MatchOldPassword;
use Illuminate\Http\Request;
use MatanYadaev\EloquentSpatial\Objects\Point;

class SettingsController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required'],
            'about' => ['required', 'string', 'min:20'],
            'tagline' => ['required'],
            'location.latitude' => ['numeric', 'min:-90', 'max: 90'],
            'location.longitude' => ['numeric', 'min:-180', 'max:180'],
        ]);

        $location = $request->has('location.latitude')
            ? new Point($request->location['latitude'], $request->location['longitude'])
            : null;


        $user->update([
            'name' => $request->name,
            'formatted_address' => $request->formatted_address,
            'location' => $location,
            'available_to_hire' => $request->available_to_hire,
            'about' => $request->about,
            'tagline' => $request->tagline,
        ]);

        return new UserResource($user);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword()],
            'password' => ['required', 'confirmed', 'min:6', new CheckSamePassword()],
        ]);

        $request->user()->update([
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'message' => 'Password updated'
        ]);
    }
}
