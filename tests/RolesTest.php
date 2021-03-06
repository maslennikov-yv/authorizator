<?php

namespace Maslennikov\Authorizator\Tests;

use Maslennikov\Authorizator\Facade\Authorizator;

class RolesTest extends TestCase
{
    public function testRolesCanBeCreated()
    {
        $this->createRoles();

        $this->assertSame(1, Authorizator::roleModel()::where('slug', 'marketer')->count());
        $this->assertSame(1, Authorizator::roleModel()::where('slug', 'contributor')->count());
    }

    public function testRolesCanBeAssigned()
    {
        $this->createRoles();

        User::first()->assignRole('marketer')->save();

        $this->assertSame(1, User::first()->role()->where('slug', 'marketer')->count());
    }

    public function testRolesCanBeRemoved()
    {
        $this->createRoles();

        User::first()->assignRole('marketer')->save();
        $this->assertSame(1, User::first()->role()->where('slug', 'marketer')->count());

        User::first()->removeRole()->save();
        $this->assertSame(0, User::first()->role()->where('slug', 'marketer')->count());
    }

    public function testRoleUsersRelation()
    {
        $this->createRoles();

        User::first()->assignRole('marketer')->save();
        $this->assertSame(1, Authorizator::roleModel()::where(['slug' => 'marketer'])->first()->users()->count());
    }

    private function createRoles()
    {
        Authorizator::roleModel()::create([
            'slug' => 'marketer',
            'name' => 'Marketer',
        ]);

        Authorizator::roleModel()::create([
            'slug' => 'contributor',
            'name' => 'Contributor',
        ]);
    }
}
