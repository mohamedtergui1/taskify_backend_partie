<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;
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
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="error", type="object")
     *         )
     *     )
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
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="password", type="string", example="password123")
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Login successful"),
 *             @OA\Property(property="data", type="object", ref="#/components/schemas/User"),
 *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Unauthorized"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
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
         *     summary="Logout user and destroy JWT token",
         *     @OA\Response(response="200", description="Successfully logged out"),
         *     @OA\Response(response="401", description="Unauthorized"),
         *     security={{"bearerAuth":{}}}
         * )
         */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            "status" => true
            ,
            "data" => null
            ,
            'message' => 'Successfully logged out']);
    }

}
