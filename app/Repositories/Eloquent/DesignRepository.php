<?php

namespace App\Repositories\Eloquent;

use App\Models\Design;
use Illuminate\Http\Request;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\DesignInterface;

class DesignRepository extends BaseRepository implements DesignInterface
{
    public function model()
    {
        return Design::class;
    }

    public function applyTags($id, array $data)
    {
        $this->find($id)->retag($data);
    }

    public function addComment($designId, array $data)
    {
        return $this->find($designId)
            ->comments()
            ->create($data);
    }

    public function like($id)
    {
        $design = $this->model->findOrFail($id);

        $design->islikedByUser(auth()->id())
            ? $design->unlike()
            : $design->like();
    }

    public function isLikedByUser($id)
    {
        return $this->model
            ->findOrFail($id)
            ->isLikedByUser(auth()->id());
    }

    public function search(Request $request)
    {
        $query = (new $this->model)
            ->newQuery()
            ->with('user');
        $query->where('is_live', true);

        // return only designs with comments
        if ($request->has_comments) {
            $query->has('comments');
        }

        // return only designs assigned to teams
        if ($request->has_team) {
            $query->has('team');
        }

        // search title and description for provided string
        if ($request->q) {
            $query->where(
                fn ($q) => $q->where('title', 'like', '%' . $request->q . '%')
                    ->orWhere('description', 'like', '%' . $request->q . '%')
            );
        }

        // order the query by likes or latest first
        if ($request->orderBy === 'likes') {
            $query->withCount('likes')
                ->orderByDesc('likes_count');
        } else {
            $query->latest();
        }

        return $query->get();
    }
}
