<?php

namespace Tests\Feature\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ArticleUpdateRequestTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    public function setUp():void
    {
        parent::setUp();
    }
    
    public function provideValidData()
    {
        return [
            [[]],
            [[
                'title' => 'hello title',
                'description' => "this is test description"
            ]],
        ];
    }

    public function provideInvalidData()
    {
        return [
            [[
                'title' => "abcd",
                'description' => "pqrs",
            ]],
        ];
    }

    /**
     * @dataProvider provideValidData
     */
    public function testValidData(array $data)
    {
        $request = new ArticleUpdateRequest();

        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testInvalidData(array $data)
    {
        $request = new ArticleUpdateRequest();

        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
    }
}
