<?php

namespace Tests\Feature\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

use Illuminate\Http\Response;

use App\Models\User;
use App\Helpers\ProxyRequest;
use Mockery;

class AuthControllerTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    protected $validCredentials;
    protected $validRegisterCredentials;
    protected $invalidCredentials;
    protected $invalidPassword;
    protected $proxyMock;

    public function setUp():void
    {
        parent::setUp();

        $this->validCredentials = [
            'email' => 'test@test.com',
            'password' => 'password',
        ];

        $this->validRegisterCredentials = [
            'name' => 'test user',
            'email' => 'regtest@test.com',
            'password' => 'password',
        ];

        $this->inValidCredentials = [
            'email' => 'wrong_test@test.com',
            'password' => 'secret',
        ];

        $this->inValidPassword = [
            'email' => 'test@test.com',
            'password' => 'secret',
        ];

        User::factory([
            'email' => $this->validCredentials['email'],
            'password' => bcrypt($this->validCredentials['password']),
        ])->create();

        $this->proxyMock = Mockery::mock(ProxyRequest::class);
    }

    public function testValidUserCanLogin()
    {
        $resp = new \stdClass();
        $resp->access_token = "abcd";
        $resp->expires_in = "1234";

        $this->proxyMock->shouldReceive('grantPasswordToken')
        ->once()
        ->with($this->validCredentials['email'], $this->validCredentials['password'])
        ->andReturn($resp);

        $this->app->instance(ProxyRequest::class, $this->proxyMock);

        $response = $this->call('POST', 'api/v1/login', $this->validCredentials);
        $this->assertEquals($response->getStatusCode(), 200);
    }

    public function testInvalidUserEmailCanNotLogin()
    {
        //sending invalid email
        $response = $this->call('POST', 'api/v1/login', $this->inValidCredentials);
        $this->assertEquals($response->getStatusCode(), 422);
    }

     /**
     * @test
     */
    public function testInvalidUserPasswordCanNotLogin()
    {
        //sending valid email, wrong password
        $this->proxyMock->shouldReceive('grantPasswordToken')
        ->once()
        ->with($this->inValidPassword['email'], $this->inValidPassword['password'])
        ->andReturn(false);

        $this->app->instance(ProxyRequest::class, $this->proxyMock);

        $response = $this->call('POST', 'api/v1/login', $this->inValidPassword);
        $this->assertEquals($response->getStatusCode(), 422);
    }

    public function testValidGuestsCanRegister()
    {
        $resp = new \stdClass();
        $resp->access_token = "abcd";
        $resp->expires_in = "1234";

        $this->proxyMock->shouldReceive('grantPasswordToken')
        ->once()
        ->with($this->validRegisterCredentials['email'], $this->validRegisterCredentials['password'])
        ->andReturn($resp);

        $this->app->instance(ProxyRequest::class, $this->proxyMock);

        $response = $this->call('POST', 'api/v1/register', $this->validRegisterCredentials);
        $this->assertEquals($response->getStatusCode(), 201);
    }

    public function testInvalidGuestsCanNotRegister()
    {
        $response = $this->call('POST', 'api/v1/register', $this->validCredentials);
        $this->assertEquals($response->getStatusCode(), 422);
    }
}
