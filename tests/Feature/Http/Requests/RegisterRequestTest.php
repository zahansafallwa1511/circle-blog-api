<?php

namespace Tests\Feature\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterRequestTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;
    
    public function provideValidData()
    {
        return [
            [[
                'email' => 'test@test.com',
                'name' => "test_name",
                'password' => "testpass"
            ]],
        ];
    }
    
    public function provideInvalidData()
    {
        return [
            [[]],
            [[
                'email' => "abcd",
                'password' => "pqrs",
            ]],
            [[
                'email' => "",
                'password' => "pqrs",
            ]],
        ];
    }

    /**
     * @dataProvider provideValidData
     */
    public function testValidData(array $data)
    {
        $request = new RegisterRequest();

        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testInvalidData(array $data)
    {
        $request = new RegisterRequest();

        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
    }
}
