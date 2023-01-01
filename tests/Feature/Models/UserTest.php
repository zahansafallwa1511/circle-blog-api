<?php

namespace Tests\Feature\Models;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\User;
use App\Models\Article;
use App\Models\Role;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Str;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $testData;


    public function setUp():void
    {
        parent::setUp();
        $this->setUpFaker();

        // Create one test user
        $this->user = User::factory()->create();

        $this->testData = [
            'valid' => [
                [
                    'name' => $this->faker->name(),
                    'email' => $this->faker->unique()->safeEmail(),
                    'email_verified_at' => now(),
                    'password' => bcrypt("password"), // password
                    'remember_token' => Str::random(10),
                ],
                [
                    'name' => $this->faker->name(),
                    'email' => $this->faker->unique()->safeEmail(),
                    'email_verified_at' => now(),
                    'password' => bcrypt("password"), // password
                    'remember_token' => Str::random(10),
                ],
            ],
            'invalid' => [
                [

                ],
                [
                    'email' => $this->faker->unique()->safeEmail(),
                    'email_verified_at' => now(),
                    'password' => bcrypt("password"), // password
                    'remember_token' => Str::random(10),
                ],
                [
                    'name' => $this->faker->name(),
                    'email_verified_at' => now(),
                    'password' => bcrypt("password"), // password
                    'remember_token' => Str::random(10),
                ],
                [
                    'name' => $this->faker->name(),
                    'email' => $this->faker->unique()->safeEmail(),
                    'remember_token' => Str::random(10),
                ],                
            ]
        ];
    }

    public function testItShouldSaveValidData()
    {
        foreach($this->testData['valid'] as $i => $testData) {
            // Fill the model with the valid test data
            $model = new User($testData);

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
            $model = new User($testData);
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

    public function testItHasManyArticles()
    {
        $user = new User;
        $foreign_key = 'id';
        $related_foreign_key = 'author_id';
        $relationship = $user->articles();

        $this->assertInstanceOf(HasMany::class, $relationship);
        $this->assertInstanceOf(Article::class, $relationship->getRelated());
        $this->assertEquals($foreign_key, $relationship->getForeignKeyName());
        $this->assertTrue(Schema::hasColumns($relationship->getRelated()->getTable(), array($related_foreign_key)));
    }

    public function testItBelongsToManyRoles()
    {
        $user = new User;
        $related_key = 'role_id';
        $relationship = $user->roles();

        $this->assertInstanceOf(BelongsToMany::class, $relationship);
        $this->assertInstanceOf(Role::class, $relationship->getRelated());
        $this->assertEquals($related_key, $relationship->getRelatedPivotKeyName());
    }

    public function testIsAdmin(){
        $admin = User::factory()->create();
        $user = User::factory()->create();
        $admin->roles()->attach(1);
        $admin->roles()->attach(2);
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isAdmin());
    }
}
