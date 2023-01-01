<?php

namespace Tests\Feature\Models;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Role;
use App\Models\User;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Schema;

class RoleTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $testData;


    public function setUp():void
    {
        parent::setUp();
    }


    public function testItBelongsToManyUsers()
    {
        $role = new Role;
        $related_key = 'user_id';
        $relationship = $role->users();

        $this->assertInstanceOf(BelongsToMany::class, $relationship);
        $this->assertInstanceOf(User::class, $relationship->getRelated());
        $this->assertEquals($related_key, $relationship->getRelatedPivotKeyName());
    }
}
