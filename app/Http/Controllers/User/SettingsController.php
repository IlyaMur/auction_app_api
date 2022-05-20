<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use MatanYadaev\EloquentSpatial\Objects\Point;

class SettingsController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
            'name' => ['required'],
            'about' => ['required', 'string', 'min:20'],
            'tagline' => ['required'],
            'formatted_address' => ['required'],
            'location.latitude' => ['required', 'numeric', 'min:-90', 'max: 90'],
            'location.longitude' => ['required', 'numeric', 'min:-180', 'max:180'],
        ]);

        $location = new Point($request->location['latitude'], $request->location['longitude']);

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
    }
}
