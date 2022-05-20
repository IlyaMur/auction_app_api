<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function getMe()
    {
        return auth()->user()
            ? new UserResource(auth()->user())
            : response()->json(null, 401);
    }
}
