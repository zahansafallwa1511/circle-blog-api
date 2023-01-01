<?php

namespace Tests\Feature\Models;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Article;
use App\Models\User;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $testData;
    protected $user;

    public function setUp():void
    {
        parent::setUp();
        $this->setUpFaker();

        // Create one test user
        $this->user = User::factory()->create();

        $this->testData = [
            'valid' => [
                [
                    'title' => $this->faker->words(5, true),
                    'description' => $this->faker->words(20, true),
                    'author_id' => $this->user['id'],
                ],
                [
                    'title' => $this->faker->words(5, true),
                    'description' => $this->faker->words(20, true),
                    'author_id' => $this->user['id'],
                ],
            ],
            'invalid' => [
                [
                    'The title field is required.',
                    'The description field is required.',
                    'The author id field is required.'
                ],
                [  
                    'title' => $this->faker->words(5, true),
                    'description' => $this->faker->words(20, true),
                    'author_id' => 0,
                ],
            ]
        ];
    }

    public function testItShouldSaveValidData()
    {
        foreach($this->testData['valid'] as $i => $testData) {
            // Fill the model with the valid test data
            $model = new Article($testData);

            // There should be no errors
            $this->assertEquals(
                true,
                $model->save(),
                "Invalid test $i"
            );
        }
    }

    public function testItShouldNotSaveInvalidData()
    {
        foreach($this->testData['invalid'] as $i => $testData) {
            // Fill the model with the invalid test data
            $model = new Article($testData);
            $result = true;
            try{
                $model->save();
            }catch(\Exception $e){
                $result = false;
            }
            $this->assertEquals(
                false,
                $result,
                "Invalid test $i"
            );
        }
    }

    public function testItBelongsToAuthor(){
        $article = new Article;
        $foreign_key = 'author_id';
        $related_foreign_key = 'id';
        $relationship = $article->author();

        $this->assertInstanceOf(BelongsTo::class, $relationship);
        $this->assertInstanceOf(User::class, $relationship->getRelated());
        $this->assertEquals($foreign_key, $relationship->getForeignKeyName());
        $this->assertTrue(Schema::hasColumns($relationship->getRelated()->getTable(), array($related_foreign_key)));
    }
}
