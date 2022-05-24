<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Contracts\DesignInterface;
use App\Repositories\Eloquent\DesignRepository;
use App\Repositories\Contracts\CommentInterface;
use App\Repositories\Eloquent\CommentRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(DesignInterface::class, DesignRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(CommentInterface::class, CommentRepository::class);
    }
}
