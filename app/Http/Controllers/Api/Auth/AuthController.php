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


    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User registration data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name", "email", "password"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="User's full name"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     description="User's email address"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="User's password (minimum 6 characters)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="User registered successfully"),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
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
                "status" => false,
                'error' => $validator->errors()
            ], 422);
        }

        $user = $this->userRepository->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            "status" => true,
            'message' => 'User registered successfully',
            "data" => $user,
            "token" => $token
        ], 201);
    }
     /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate user and generate JWT token",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Login successful"),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */

 function login(Request $request)
 {
     $rules = [
         'email' => 'required|email',
         'password' => 'required|string'
     ];

     $validator = validator($request->all(), $rules);

     if ($validator->fails()) {
         return response()->json([
             "status" => false,
             'errors' => $validator->errors()
         ], 400);
     }

     if (!Auth::attempt($request->only('email', 'password'))) {
         return response()->json([
             "status" => false,
             'message' => 'Unauthorized',
             "data" => null
         ], 401);
     }

     $user = Auth::user();
     $token = $user->createToken('auth_token')->plainTextToken;

     return response()->json([
         "status" => true,
         'message' => 'Login successful',
         "data" => $user,
         "token" => $token
     ], 200);
 }



 /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Log out a user",
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not logged in or token invalid",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
        public function logout(Request $request)
        {
            $user = Auth::user();

            if ($user) {
                $user->tokens()->delete();
                Auth::guard('web')->logout();

                return response()->json([
                    "status" => true,
                    "data" => null,
                    'message' => 'User logged out successfully'
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => 'No user is currently authenticated'
                ], 401);
            }
        }
}
