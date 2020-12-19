<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use  App\Room;
use Log;

class RoomController extends Controller
{

    //Auth middleware
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * @OA\Get(
     *   path="/rooms",
     *   summary="list of rooms",
     *   tags={"Room"},
     *    @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="number of element per page or all",
     *         required=true,
     *         style="form"
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="list of all rooms"
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
    public function allRooms(Request $request)
    {
         $per_page = $request->get("per_page");
         if($per_page=="all"){
            return response()->json(['rooms' =>  Room::all()], 200);    
         }
         return response()->json(['rooms' =>  Room::paginate($per_page)], 200);
    }

        /**
     * @OA\Get(
     *   path="/room/{id}",
     *   summary="single room",
     *   tags={"Room"},
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="room id",
     *         required=true,
     *         style="form"
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="Specific room"
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

    //Get Single Room
    public function singleRoom($id)
    {
        try {
            $room = Room::findOrFail($id);

            return response()->json(['room' => $room], 200);

        } catch (\Exception $e) {

            return response()->json(['message' => 'room not found!'], 404);
        }

    }

    /**
     * @OA\Post(
     *   path="/room",
     *   summary="Add a room",
     *   tags={"Room"},
     * @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(
     *   required={"room_number", "price","locked","max_persons","room_type"},
     *   @OA\Property(property="room_number", type="string", format="name", example="12B"),
     *   @OA\Property(property="price", type="number", format="float"),
     *   @OA\Property(property="locked", type="boolean"),
     *   @OA\Property(property="max_persons", type="integer"),
     *   @OA\Property(property="room_type", type="string", example="big or small"),
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

     //Save room

     public function saveRoom(Request $request){
        $this->validate($request, [
            'room_number' => 'required|string|unique:rooms',
            'price' => 'required|numeric|between:0.00,999999.99',
            'locked' => 'required|boolean',
            'max_persons' => 'required|numeric|gt:0',
            'room_type' => 'required|in:big,small',
        ]);

        try {

            $room = new Room;
            $room->room_number = $request->input('room_number');
            $room->price = $request->input('price');
            $room->locked = $request->input('locked');
            $room->max_persons = $request->input('max_persons');
            $room->room_type = $request->input('room_type');
            $room->save();
            Log::info('room user: '.$room->id);

            //successful response
            return response()->json(['room' => $room, 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            // error message
            Log::error('room save error : '.$e);
            return response()->json(['message' => 'room Save Failed!'], 409);
        }
     }


     /**
     * @OA\Put(
     *   path="/room",
     *   summary="update a room",
     *   tags={"Room"},
     * @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(
     *   required={"id","room_number", "price","locked","max_persons","room_type"},
     *   @OA\Property(property="id", type="integer"),   
     *   @OA\Property(property="room_number", type="string", format="name", example="12B"),
     *   @OA\Property(property="price", type="number", format="float"),
     *   @OA\Property(property="locked", type="boolean"),
     *   @OA\Property(property="max_persons", type="integer"),
     *   @OA\Property(property="room_type", type="string", example="big or small"),
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

     //Save room

     public function updateRoom(Request $request){
        $this->validate($request, [
            'id' => 'required|integer',
            'room_number' => 'required|string|unique:rooms',
            'price' => 'required|numeric|between:0.00,999999.99',
            'locked' => 'required|boolean',
            'max_persons' => 'required|numeric|gt:0',
            'room_type' => 'required|in:big,small',
        ]);

        try {
            $id = $request->id;
            $room = Room::findOrFail($id);
            $room->room_number = $request->input('room_number');
            $room->price = $request->input('price');
            $room->locked = $request->input('locked');
            $room->max_persons = $request->input('max_persons');
            $room->room_type = $request->input('room_type');
            $room->save();
            Log::info('room update: '.$room->id);

            //successful response
            return response()->json(['room' => $room], 200);

        } catch (\Exception $e) {
            // error message
            Log::error('room update error : '.$e);
            return response()->json(['message' => 'room Update Failed!'], 409);
        }
     }


     /**
     * @OA\Delete(
     *   path="/deleteroom/{id}",
     *   summary="delete room",
     *   tags={"Room"},
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="room id",
     *         required=true,
     *         style="form"
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="Specific room delete"
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


    //Delete room
    public function deleteRoom($id)
    {
        try {
            $room = Room::findOrFail($id);
            $room->delete();
            Log::info(' room delete:'. $room->id);
            return response()->json(['cusutomer' => $room], 200);

        } catch (\Exception $e) {
            Log::error('delete room:'. $e);
            return response()->json(['message' => 'room not found!'], 404);
        }

    }


    /**
     * @OA\Get(
     *   path="/roomsearch",
     *   summary="Search of room",
     *   tags={"Room"},
     *    @OA\Parameter(
     *         name="room_number",
     *         in="query",
     *         description="room number",
     *         required=true,
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="number of element per page or all",
     *         required=true,
     *         style="form"
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="list of all rooms"
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
    
     
     //Search Room
    public function searchRoom(Request $request)
    {
        try {
            $room_number = $request->email;
            $per_page = $request->per_page;
            $room = Room::where('room_number','like','%'.$room_number.'%')->paginate($per_page);
            Log::info(' room search:'. $room);
            return response()->json(['room' => $room], 200);

        } catch (\Exception $e) {
            Log::error(' room search:'. $e);
            return response()->json(['message' => 'room not found!'], 404);
        }
 
     }


}