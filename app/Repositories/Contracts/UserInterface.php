<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface UserInterface
{
    public function findByEmail($email);
    public function search(Request $request);
}
