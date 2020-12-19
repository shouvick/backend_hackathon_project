<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use  App\User;
use Illuminate\Support\Facades\Auth;
use Log;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/register",
     *   summary="Register a User",
     *   tags={"Auth User Register"},
     * @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(
     *   required={"email","password"},
     *   @OA\Property(property="name", type="string", format="name", example="John Doe"),
     *   @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *   @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *   @OA\Property(property="password_confirmation", type="string", example="PassWord12345"),
     *    ),
     * ),
     
     *   @OA\Response(
     *     response=200,
     *     description="Ok"
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Created"
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Internal Server Error"
     *   ),
     *  @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     *   ),
     * )
     */

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        try {

            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $password = $request->input('password');
            $user->password = app('hash')->make($password);
            $user->save();
            Log::info('Register user: '.$user->id);

            //successful response
            return response()->json(['user' => $user, 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            // error message
            Log::error('Register user error : '.$e);
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }

    }


            /**
     * @OA\Post(
     *   path="/login",
     *   summary="Login a User",
     *   tags={"Auth User Register"},
     * @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(
     *   required={"email","password"},
     *   @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *   @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     
     *   @OA\Response(
     *     response=200,
     *     description="Ok"
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Created"
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Internal Server Error"
     *   ),
     *  @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     *   ),
     * )
     */

    public function login(Request $request)
    {
          //validate incoming request 
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            Log::error('Login user token : Unauthorized ');
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        Log::info('Login user token: '.$token);
        return $this->respondWithToken($token);
    }


}