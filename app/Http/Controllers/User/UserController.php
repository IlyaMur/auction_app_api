<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserInterface;
use App\Repositories\Eloquent\Criteria\EagerLoad;

class UserController extends Controller
{
    public function __construct(protected UserInterface $users)
    {
    }

    public function index()
    {
        $users = $this->users
            ->withCriteria([new EagerLoad(['designs'])])
            ->all();

        return UserResource::collection($users);
    }

    public function search(Request $request)
    {
        $request->validate([
            'longitude' => ['numeric', 'min:-90', 'max: 90'],
            'latitude' => ['numeric', 'min:-180', 'max:180'],
        ]);

        $designers = $this->users->search($request);

        return UserResource::collection($designers);
    }
}
