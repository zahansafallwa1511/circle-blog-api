<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

use App\Helpers\ProxyRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{

    protected $proxy;

    public function __construct(ProxyRequest $proxy)
    {
        $this->proxy = $proxy;
    }

    public function register(RegisterRequest $request)
    {
        $user = new User;
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->password = bcrypt($request->get('name'));

        if(!$user->save()){
            return response()->json([
                'message' => 'Could not register',
            ], 422);
        }

        $response = $this->proxy->grantPasswordToken(
            $user->email,
            $request->get('password')
        );

        if(!$response){
            return response()->json([
                'message' => 'Account created. Token not generated',
            ], 201);
        }

        return response()->json([
            'token' => $response->access_token,
            'expiresIn' => $response->expires_in,
            'message' => 'Your account has been created',
        ], 201);
    }


    public function login(LoginRequest $request){

        $response = $this->proxy
            ->grantPasswordToken($request->get('email'), $request->get('password'));
        
        if(!$response){
            return response()->json([
                'message' => 'Could not issue token',
            ], 422);
        }

        return response()->json([
            'token' => $response->access_token,
            'expiresIn' => $response->expires_in,
            'message' => 'You have been logged in',
            'success' => true
        ], 200);
    }
    
    public function refresh(){
        $response = $this->proxy->refreshAccessToken();

        if(!$response){
            return response()->json([
                'message' => 'Could not refresh token',
            ], 422);
        }

        return response()->json([
            'token' => $response->access_token,
            'expiresIn' => $response->expires_in,
            'message' => 'Token has been refreshed',
            'success' => true
        ], 200);
    }

    public function logout(){
        
        try{
            $token = request()->user()->token();
            $token->delete();
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Could not be logged out',
            ], 401);
        }
    
        // remove the httponly cookie
        cookie()->queue(cookie()->forget('refresh_token'));

        return response()->json([
            'message' => 'You have been successfully logged out',
            'success' => true
        ], 200);
    }
}
