<?php

namespace App\Repositories\Contracts\Criteria;

interface CriteriaInterface
{
    public function withCriteria(...$criteria);
}
