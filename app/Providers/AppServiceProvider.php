<?php

namespace App\Providers;

use App\Bundle\FlowScheduler\Domain\Ports\UserRepositoryInterface;
use App\Bundle\FlowScheduler\Domain\Ports\AuthenticatorInterface;
use App\Bundle\FlowScheduler\Domain\Ports\EmployeeRepositoryInterface;
use App\Bundle\FlowScheduler\Domain\Ports\ShiftRepositoryInterface;
use App\Bundle\FlowScheduler\Domain\Ports\AssignmentRepositoryInterface;
use App\Bundle\FlowScheduler\Infrastructure\Auth\LaravelAuthenticator;
use App\Bundle\FlowScheduler\Infrastructure\Persistence\Repositories\EloquentEmployeeRepository;
use App\Bundle\FlowScheduler\Infrastructure\Persistence\Repositories\EloquentShiftRepository;
use App\Bundle\FlowScheduler\Infrastructure\Persistence\Repositories\EloquentAssignmentRepository;
use App\Bundle\FlowScheduler\Infrastructure\Persistence\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(AuthenticatorInterface::class, LaravelAuthenticator::class);
        $this->app->bind(EmployeeRepositoryInterface::class, EloquentEmployeeRepository::class);
        $this->app->bind(ShiftRepositoryInterface::class, EloquentShiftRepository::class);
        $this->app->bind(AssignmentRepositoryInterface::class, EloquentAssignmentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
