<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Contracts\Criteria\CriterionInterface;

class ForUser implements CriterionInterface
{
    public function __construct(protected $user_id)
    {
    }

    public function apply($model)
    {
        return $model->where('user_id', $this->user_id);
    }
}
