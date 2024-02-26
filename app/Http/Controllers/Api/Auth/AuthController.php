<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->middleware('auth:sanctum')->only('logout');
        $this->userRepository = $userRepository;
    }
    public function register(Request $request)
    {

        $rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ];


        $validator = validator($request->all(), $rules);


        if ($validator->fails()) {
            return response()->json([
                "status" => false
                ,
                'error' => $validator->errors()

            ], 400);
        }


        $user = $this->userRepository->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            "status" => true
            ,
            'message' => 'User registered successfully'
            ,
            "data" => $user
            ,
            "token" => $token
        ], 201);
    }

    function login(Request $request)
    {

        $rules = [
            'email' => 'required|email',
            'password' => 'required|string'
        ];


        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                "status" => false
                ,
                'error' => $validator->errors()

            ], 400);
        }
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = $this->userRepository->getByEmail($request->email);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            "status" => true
            ,
            'message' => 'login successfully'
            ,
            "data" => $user
            ,
            "token" => $token
        ], 200);


    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

}
