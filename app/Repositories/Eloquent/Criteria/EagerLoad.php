<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Contracts\Criteria\CriterionInterface;

class EagerLoad implements CriterionInterface
{
    public function __construct(protected $relationships)
    {
    }

    public function apply($model)
    {
        return $model->with($this->relationships);
    }
}
