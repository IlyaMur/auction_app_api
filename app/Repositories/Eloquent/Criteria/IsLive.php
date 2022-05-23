<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Contracts\Criteria\CriterionInterface;

class IsLive implements CriterionInterface
{
    public function apply($model)
    {
        return $model->where('is_live', true);
    }
}
