<?php

namespace App\Repositories\Contracts\Criteria;

interface CriterionInterface
{
    public function apply($model);
}
