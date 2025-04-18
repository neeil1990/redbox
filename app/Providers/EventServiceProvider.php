<?php

namespace App\Providers;

use App\Events\MonitoringProjectBeforeDelete;
use App\Events\MonitoringProjectCreated;
use App\Listeners\AssignAdminMonitoringRoleForAuthUser;
use App\Listeners\AssignRoleRegisteredUser;
use App\Listeners\RemoveAllRolesMonitoringProjectUsers;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Verified::class => [
            AssignRoleRegisteredUser::class,
        ],
        MonitoringProjectCreated::class => [
            AssignAdminMonitoringRoleForAuthUser::class,
        ],
        MonitoringProjectBeforeDelete::class => [
            RemoveAllRolesMonitoringProjectUsers::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
