<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use  App\User;
use Log;

class UserController extends Controller
{

    //Auth middleware
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * @OA\Get(
     *   path="/users",
     *   summary="list of users",
     *   tags={"User"},
     *    @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="number of element per page or all",
     *         required=true,
     *         style="form"
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="list of all users"
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Internal Server Error"
     *   ),
     *  @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     *   ),
     *   security={ {"api_key": {}} },
     * )
     */
    
    //Get All User
    public function allUsers(Request $request)
    {
         $per_page = $request->get("per_page");
         if($per_page=="all"){
            Log::info('get all user:'. User::all());
            return response()->json(['users' =>  User::all()], 200);    
         }
         Log::info('get all user:'. User::paginate($per_page));
         return response()->json(['users' =>  User::paginate($per_page)], 200);
    }

    /**
     * @OA\Get(
     *   path="/user/{id}",
     *   summary="single users",
     *   tags={"User"},
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="user id",
     *         required=true,
     *         style="form"
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="Specific user"
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Internal Server Error"
     *   ),
     *  @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     *   ),
     *   security={ {"api_key": {}} },
     * )
     */

    //Get Single User
    public function singleUser($id)
    {
        try {
            $user = User::findOrFail($id);
            Log::info('get all user:'. $user);
            return response()->json(['user' => $user], 200);

        } catch (\Exception $e) {
            Log::error('get all user:'. $e);
            return response()->json(['message' => 'user not found!'], 404);
        }

    }


    /**
     * @OA\Delete(
     *   path="/deleteuser/{id}",
     *   summary="delete user",
     *   tags={"User"},
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="user id",
     *         required=true,
     *         style="form"
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="Specific user delete"
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Internal Server Error"
     *   ),
     *  @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     *   ),
     *   security={ {"api_key": {}} },
     * )
     */

    //Delete User
    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            Log::info(' user delete:'. $user->id);
            return response()->json(['user' => $user], 200);

        } catch (\Exception $e) {
            Log::error('delete user:'. $e);
            return response()->json(['message' => 'user not found!'], 404);
        }

    }


    /**
     * @OA\Put(
     *   path="/user",
     *   summary="Update a User",
     *   tags={"User"},
     * @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(
     *   required={"email","password"},
     *   @OA\Property(property="id", type="integer"),
     *   @OA\Property(property="name", type="string", format="name", example="John Doe")
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
     *   security={ {"api_key": {}} },
     * )
     */

    //Update User
    public function updateUser(Request $request)
    {
        try {
            $id = $request->id;
            $user = User::findOrFail($id);
            $user->name = $request->name;
            $user->save();
            Log::info(' user update:'. $user->id);
            return response()->json(['user' => $user], 200);

        } catch (\Exception $e) {
            Log::error(' user delete:'. $e);
            return response()->json(['message' => 'user not found!'], 404);
        }

    }

}