<?php

namespace Maslennikov\Authorizator\Providers;

use Illuminate\Support\ServiceProvider;
use Maslennikov\Authorizator\Authorizator;
use Maslennikov\Authorizator\Models\Role;
use Maslennikov\Authorizator\Observers\RoleObserver;

class AuthorizatorServiceProvider extends ServiceProvider
{
    /**
     * @var array|string[]
     */
    public array $bindings = [
        'Authorizator' => Authorizator::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../Config/Authorizator.php' => config_path('Authorizator.php'),
        ], 'Authorizator-config');

        $this->publishes([
            __DIR__ . '/../Database/Migrations' => database_path('migrations'),
        ], 'Authorizator-migrations');

        Role::observe(RoleObserver::class);
    }

}