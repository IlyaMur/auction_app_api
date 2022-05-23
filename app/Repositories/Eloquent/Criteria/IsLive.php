<?php

namespace App\Repositories\Eloquent\Criteria;

class IsLive
{
    public function apply($model)
    {
        return $model->where('is_live', true);
    }
}
