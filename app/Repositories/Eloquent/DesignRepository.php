<?php

namespace App\Repositories\Eloquent;

use App\Models\Design;
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
}
