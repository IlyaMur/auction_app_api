<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserInterface;

class UserController extends Controller
{
    public function __construct(protected UserInterface $users)
    {
    }

    public function index()
    {
        $users = $this->users->all();

        return UserResource::collection($users);
    }
}
