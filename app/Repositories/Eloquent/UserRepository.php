<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\Contracts\UserInterface;
use App\Repositories\Eloquent\BaseRepository;
use MatanYadaev\EloquentSpatial\Objects\Point;

class UserRepository extends BaseRepository implements UserInterface
{
    public function model()
    {
        return User::class;
    }

    public function findByEmail($email)
    {
        return $this->model
            ->where('email', $email)
            ->first();
    }

    public function search(Request $request)
    {
        $query = (new $this->model)->newQuery();

        // only designers who have designs
        if ($request->has_designs) {
            $query->has('designs');
        }

        // check for available_to_hire
        if ($request->available_to_hire) {
            $query->where('available_to_hire', true);
        }

        // Geo search
        $ltd = $request->latitude;
        $lng = $request->longitude;
        $unit = $request->unit;
        $dist = $request->distance;

        $hasDistFilter = $ltd && $lng && $dist;

        if ($hasDistFilter) {
            $dist *= $unit === 'km' ? 1000 : 1609.34;
            $point = new Point($ltd, $lng);

            $query->whereDistanceSphere('location', $point, '<', $dist);
        }

        // order the results
        if ($request->orderBy === 'closest' && $hasDistFilter) {
            $query->orderByDistanceSphere('location', $point);
        } elseif ($request->orderBy === 'latest') {
            $query->latest();
        } else {
            $query->oldest();
        }

        return $query->with(['designs' => fn ($q) => $q->limit(4)]);
    }
}
