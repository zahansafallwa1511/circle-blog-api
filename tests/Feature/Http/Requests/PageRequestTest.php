<?php

namespace Tests\Feature\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

use App\Http\Requests\PageRequest;
use Illuminate\Support\Facades\Hash;

class PageRequestTest extends TestCase
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
                'per_page' => 1,
            ]],
            [[
                'per_page' => 10,
            ]],
        ];
    }

    public function provideInvalidData()
    {
        return [
            [[
                'per_page' => -1,
            ]],
            [[
                'per_page' => 0,
            ]],
        ];
    }

    /**
     * @dataProvider provideValidData
     */
    public function testValidData(array $data)
    {
        $request = new PageRequest();

        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testInvalidData(array $data)
    {
        $request = new PageRequest();

        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
    }
}
