<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\ReviewRepositoryInterface;
use App\Repositories\Eloquents\ReviewRepository;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use App\Repositories\Eloquents\TicketRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // ... existing bindings ...
        $this->app->bind(
            \App\Repositories\Interfaces\LanguageRepositoryInterface::class,
            \App\Repositories\Eloquents\LanguageRepository::class
        );
        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepository::class);
        $this->app->bind(
            \App\Repositories\Interfaces\TagRepositoryInterface::class,
            \App\Repositories\Eloquents\TagRepository::class
        );
        $this->app->bind(TicketRepositoryInterface::class, TicketRepository::class);
        $this->app->bind(
            \App\Repositories\Interfaces\RoleRepositoryInterface::class,
            \App\Repositories\Eloquents\RoleRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
} 