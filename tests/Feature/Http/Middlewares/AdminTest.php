<?php

namespace Tests\Feature\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

use App\Http\Middleware\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;


class AdminTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    public function setUp():void
    {
        parent::setUp();
    }
    
    public function testNonAdminsAreNotAllowed(){
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $middleware = new Admin;
        $request = new Request;

        $response = $middleware->handle($request, function(){});
        $this->assertEquals($response->getStatusCode(), 403);
    }

    public function testAdminsAreAllowed(){
        $admin = User::factory()->create();
        $admin->roles()->attach(1);
        $this->actingAs($admin, 'api');

        $middleware = new Admin;
        $request = new Request;

        $response = $middleware->handle($request, function(){});
        $this->assertEquals($response, null);
    }
}
