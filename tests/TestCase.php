<?php

namespace Maslennikov\Authorizator\Tests;

use Maslennikov\Authorizator\Facade\Authorizator;
use Maslennikov\Authorizator\Providers\AuthorizatorServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(realpath(__DIR__ . '/../database/migrations/'));

        $this->setupDatabase($this->app);
    }

    /**
     * Load package service provider
     * @param \Illuminate\Foundation\Application $app
     * @return array|string[]
     */
    protected function getPackageProviders($app)
    {
        return [
            AuthorizatorServiceProvider::class,
        ];
    }

    /**
     * Load package alias
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set(User::class);
    }

    protected function setupDatabase($app)
    {
        $user = User::create();
        $user->save();

        collect([
            [
                'slug' => 'user',
                'name' => 'User',
                'children' => null,
                'permissions' => ['blog.view'],
            ],
            [
                'slug' => 'content',
                'name' => 'Content manager',
                'children' => ['user'],
                'permissions' => ['blog.create', 'blog.edit', 'blog.delete'],
            ],
            [
                'slug' => 'editor',
                'name' => 'Editor',
                'children' => ['content'],
                'permissions' => ['blog.publish'],
            ],
            [
                'slug' => 'manager',
                'name' => 'Manager',
                'children' => ['user'],
                'permissions' => ['user.manage'],
            ],
            [
                'slug' => 'admin',
                'name' => 'Administrator',
                'children' => ['editor', 'manager'],
                'permissions' => null,
            ],
            [
                'slug' => 'guest',
                'name' => 'Guest',
                'children' => null,
                'permissions' => null,
            ],
        ])->each(function (array $row) {
            Authorizator::roleModel()::create(
                [
                    'slug' => $row['slug'],
                    'name' => $row['name'],
                    'children' => $row['children'],
                    'permissions' => $row['permissions'],
                ]
            );
        });
    }
}
