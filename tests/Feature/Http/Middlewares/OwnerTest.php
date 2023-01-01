<?php

namespace Tests\Feature\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

use App\Http\Middleware\Owner;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\Models\Article;
use Mockery;

class OwnerTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    public function setUp():void
    {
        parent::setUp();
    }
    
    public function testNonOwnersAreNotAllowed(){
        $firstArticle = Article::factory()->create();
        $secondArticle = Article::factory()->create();
        $this->actingAs($secondArticle->author()->first(), 'api');

        $middleware = new Owner;
        $request = Mockery::mock(Request::class);

        $route = Mockery::mock(\stdClass::class);

        $request->shouldReceive('route')
                ->once()
                ->andReturn($route);
                
        $route->shouldReceive('parameter')
                ->once()
                ->with('article')
                ->andReturn($firstArticle);

        $response = $middleware->handle($request, function(){});
        $this->assertEquals($response->getStatusCode(), 403);
    }

    public function testOwnersAreAllowed(){
        $firstArticle = Article::factory()->create();
        $secondArticle = Article::factory()->create();
        $this->actingAs($firstArticle->author()->first(), 'api');

        $middleware = new Owner;
        $request = Mockery::mock(Request::class);

        $route = Mockery::mock(\stdClass::class);

        $request->shouldReceive('route')
                ->once()
                ->andReturn($route);
                
        $route->shouldReceive('parameter')
                ->once()
                ->with('article')
                ->andReturn($firstArticle);

        $response = $middleware->handle($request, function(){});
        $this->assertEquals($response, null);
    }
}