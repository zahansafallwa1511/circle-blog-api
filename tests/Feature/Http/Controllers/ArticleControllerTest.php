<?php

namespace Tests\Feature\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

use Illuminate\Http\Response;

use App\Models\User;
use App\Models\Article;
use App\Helpers\ProxyRequest;

class ArticleControllerTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    public function setUp():void
    {
        parent::setUp();
    }

    public function testIndex()
    {
       $publishedArticles = Article::factory(['is_published' => 1])->count(20)->create();
       $unPublishedArticles = Article::factory()->count(20)->create();

       //get article by default pagination
       $article = new Article;
       $response = $this->get("api/v1/articles");
       $this->assertEquals($response->getStatusCode(), 200);
       $this->assertEquals($response['total'], 20);
       $this->assertEquals($response['per_page'], $article->getPerPage());

       //get article by default custom per_page pagination
       $perPage = 10;
       $response = $this->get("api/v1/articles?per_page=".$perPage);
       $this->assertEquals($response->getStatusCode(), 200);
       $this->assertEquals($response['total'], 20);
       $this->assertEquals($response['per_page'], $perPage);
    }

    public function testStore()
    {
        $article = Article::factory()->make();
        $user = User::factory()->create();

        //valid data submit by guest
        $response = $this->withHeaders(['Accept' => 'application/json'])
                    ->post(route('articles.store'), $article->getAttributes());
        $this->assertEquals($response->getStatusCode(), 401);


        $this->actingAs($user, 'api');
        
        //invalid data submit
        $response = $this->withHeaders(['Accept' => 'application/json'])
                    ->post(route('articles.store'), []);
        $this->assertEquals($response->getStatusCode(), 422);
        
        //valid data submit
        $response = $this->withHeaders(['Accept' => 'application/json'])
                    ->post(route('articles.store'), $article->getAttributes());
        $this->assertEquals($response->getStatusCode(), 201);

    }

    public function testShow(){
        //try to show unpublished article
        $user = User::factory()->create();
        $article = Article::factory(['author_id' => $user->id])->create();
        $response = $this->get("api/v1/articles/{$article->id}");
        $this->assertEquals($response->getStatusCode(), 404);

        //publish article
        $article->is_published = 1;
        $article->save();

        //try to show published article
        $response = $this->get("api/v1/articles/{$article->id}");
        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals(isset($response['id']), $article->id);

        //try to show non existant article
        $response = $this->get("api/v1/articles/0");
        $this->assertEquals($response->getStatusCode(), 404);
    }

    public function testUpdate()
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $article = Article::factory(['author_id' => $owner->id])->create();
        $changedArticle = Article::factory()->make();

        //guest trying to update an article
        $response = $this->withHeaders(['Accept' => 'application/json'])
                         ->put(route('articles.update', $article->id), $changedArticle->getAttributes());
        $this->assertEquals($response->getStatusCode(), 403);

        $this->actingAs($user, 'api');
        //non owner trying to update an article
        $response = $this->withHeaders(['Accept' => 'application/json'])
                         ->put(route('articles.update', $article->id), $changedArticle->getAttributes());
        $this->assertEquals($response->getStatusCode(), 403);

        $this->actingAs($owner, 'api');
        //owner trying to update an article with empty data
        $response = $this->withHeaders(['Accept' => 'application/json'])
                         ->put(route('articles.update', $article->id), []);
        $this->assertEquals($response->getStatusCode(), 200);

        //owner trying to update an article with new data
        $response = $this->withHeaders(['Accept' => 'application/json'])
                         ->put(route('articles.update', $article->id), $changedArticle->getAttributes());
        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals(Article::find($article->id)->title, $changedArticle->title);
        $this->assertEquals(Article::find($article->id)->description, $changedArticle->description);
    }

    public function testDelete()
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $article = Article::factory(['author_id' => $owner->id])->create();

        //non owner trying to delete an article
        $this->actingAs($user, 'api');
        $response = $this->delete("api/v1/articles/{$article->id}");
        $this->assertEquals($response->getStatusCode(), 403);

        //owner trying to delete
        $this->actingAs($owner, 'api');
        $response = $this->delete("api/v1/articles/{$article->id}");
        $this->assertEquals($response->getStatusCode(), 200);

        //owner trying to delete deleted article again
        $response = $this->delete("api/v1/articles/{$article->id}");
        $this->assertEquals($response->getStatusCode(), 404);

    }

    public function testPublish()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(1);
        $owner = User::factory()->create();
        $article = Article::factory(['author_id' => $owner->id])->create();

        //owner trying to publish
        $this->actingAs($owner, 'api');
        $response = $this->get("api/v1/articles/{$article->id}/publish");
        $this->assertEquals($response->getStatusCode(), 403);

        //admin trying to publish
        $this->actingAs($admin, 'api');
        $response = $this->get("api/v1/articles/{$article->id}/publish");
        $this->assertEquals($response->getStatusCode(), 200);

        //admin trying to publish non existant article
        $this->actingAs($admin, 'api');
        $response = $this->get("api/v1/articles/0/publish");
        $this->assertEquals($response->getStatusCode(), 404);
    }
}
