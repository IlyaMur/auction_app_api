<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\ChatInterface;
use App\Repositories\Contracts\TeamInterface;
use App\Repositories\Contracts\UserInterface;
use App\Repositories\Eloquent\ChatRepository;
use App\Repositories\Eloquent\TeamRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Contracts\DesignInterface;
use App\Repositories\Eloquent\DesignRepository;
use App\Repositories\Contracts\CommentInterface;
use App\Repositories\Contracts\MessageInterface;
use App\Repositories\Eloquent\CommentRepository;
use App\Repositories\Eloquent\MessageRepository;
use App\Repositories\Contracts\InvitationInterface;
use App\Repositories\Eloquent\InvitationRepository;

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
        // Repository pattern implementation
        $this->app->bind(DesignInterface::class, DesignRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(CommentInterface::class, CommentRepository::class);
        $this->app->bind(TeamInterface::class, TeamRepository::class);
        $this->app->bind(TeamInterface::class, TeamRepository::class);
        $this->app->bind(InvitationInterface::class, InvitationRepository::class);
        $this->app->bind(MessageInterface::class, MessageRepository::class);
        $this->app->bind(ChatInterface::class, ChatRepository::class);
    }
}
