<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
                            'name' => 'required|string|min:2|max:255',
                            'email' => 'required|string|email|max:255|unique:users',
                            'password' => 'required|string|min:8|confirmed',
                        ]);

        if($validatedData->fails()){
            return response()->json($validatedData->errors(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
                'message' => 'User registered successfully',
                'user' => $user
        ]);
    }

    public function login(Request $request){
        $validatedData = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if($validatedData->fails()){
            return response()->json($validatedData->errors(), 400);
        }

        if(!$token = auth()->attempt($validatedData->validated())){
            return response()->json([
                'error' => 'Invalid Credentials'
            ]);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL()*60
        ]);
    }

    public function profile(){
        return response()->json(auth()->user());
    }

    public function refresh(){
        return $this->respondWithToken(auth()->refresh());
    }
    
    public function logout(){
        auth()->logout();

        return response()->json([
            'message' => 'User successfully logged out'
        ]);
    }
}
