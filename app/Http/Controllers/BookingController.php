<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use  App\Booking;
use  App\Room;
use  App\Customer;
use Carbon\Carbon;
use Log;

class BookingController extends Controller
{

    //Auth middleware
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * @OA\Get(
     *   path="/bookings",
     *   summary="list of booking",
     *   tags={"Booking"},
     *    @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="number of element per page or all",
     *         required=true,
     *         style="form"
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="list of all booking"
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
    public function allBookings(Request $request)
    {
         $per_page = $request->get("per_page");
         if($per_page=="all"){
            return response()->json(['bookings' =>  Booking::all()], 200);    
         }
         return response()->json(['bookings' =>  Booking::paginate($per_page)], 200);
    }

        /**
     * @OA\Get(
     *   path="/booking/{id}",
     *   summary="single booking",
     *   tags={"Booking"},
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="booking id",
     *         required=true,
     *         style="form"
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="Specific Booking"
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
    public function singleBooking($id)
    {
        try {
            $booking = Booking::findOrFail($id);

            return response()->json(['booking' => $booking], 200);

        } catch (\Exception $e) {

            return response()->json(['message' => 'booking not found!'], 404);
        }

    }

    /**
     * @OA\Post(
     *   path="/booking",
     *   summary="Add a booking",
     *   tags={"Booking"},
     * @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(
     *   required={"room_id", "arrival","checkout","customer_id","book_type","book_time"},
     *   @OA\Property(property="room_id", type="integer"),
     *   @OA\Property(property="arrival", type="string", example="2020-12-19 17:07:13"),
     *   @OA\Property(property="checkout", type="string",example="2020-12-19 17:07:13"),
     *   @OA\Property(property="customer_id", type="integer"),
     *   @OA\Property(property="book_type", type="string", example="check-in or check-out"),
     *   @OA\Property(property="book_time", type="string", example="2020-12-19 17:07:13"),
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

     public function saveBooking(Request $request){
        $this->validate($request, [
            'room_id' => 'required|integer',
            'arrival' => 'required|date_format:Y-m-d H:i:s',
            'checkout' => 'required|date_format:Y-m-d H:i:s',
            'customer_id' => 'required|integer',
            'book_type' => 'required|in:check-in,check-out',
            'book_time' => 'required|date_format:Y-m-d H:i:s'
        ]);

        try {

            $already_book = Booking::where('room_id',$request->room_id)->where('book_type','=','check-in')->value('room_id');
            $repeated_book = Booking::where('room_id',$request->room_id)->where('book_type','=','check-in')->where('customer_id','=',$request->customer_id)->value('room_id');
            if($repeated_book){
                return response()->json( ['message' => 'you have already booked this room.Enjoy!!']);
            }
            if($already_book)
            {
                return response()->json( ['message' => 'This room already booked.Sorry!!']);
            }
            $room = Room::findOrFail($request->room_id);
            $customer = Customer::findOrFail($request->customer_id);
            $book = new Booking;
            $book->room_id = $room->id;
            $book->arrival = $request->input('arrival');
            $book->checkout = $request->input('checkout');
            $book->customer_id = $customer->id;
            $book->book_type = $request->input('book_type');
            $book->book_time = Carbon::now()->toDateString();
            $book->save();
            Log::info('booking user: '.$book->id);

            //successful response
            return response()->json(['booking' => $book, 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            // error message
            Log::error('bookng save error : '.$e);
            return response()->json(['message' => 'booking Save Failed!'], 409);
        }
     }

    
        /**
     * @OA\Put(
     *   path="/booking",
     *   summary="Update a booking",
     *   tags={"Booking"},
     * @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(
     *   required={"id","room_id", "arrival","checkout","customer_id","book_type","book_time"},
     *   @OA\Property(property="room_id", type="integer"),
     *   @OA\Property(property="arrival", type="string", example="2020-12-19 17:07:13"),
     *   @OA\Property(property="checkout", type="string",example="2020-12-19 17:07:13"),
     *   @OA\Property(property="customer_id", type="integer"),
     *   @OA\Property(property="book_type", type="string", example="check-in or check-out"),
     *   @OA\Property(property="book_time", type="string", example="2020-12-19 17:07:13"),
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

     //update room
     public function updateBooking(Request $request){
        $this->validate($request, [
            'id'=> 'required|integer',
            'room_id' => 'required|integer',
            'arrival' => 'required|date_format:Y-m-d H:i:s',
            'checkout' => 'required|date_format:Y-m-d H:i:s',
            'customer_id' => 'required|integer',
            'book_type' => 'required|in:check-in,check-out',
            'book_time' => 'required|date_format:Y-m-d H:i:s'
        ]);

        try {
            $id = $request->id;
            $room = Room::findOrFail($request->room_id);
            $customer = Room::findOrFail($request->customer_id);
            $already_book = Booking::where('room_id',$room->room_id)->where('book_type','=','check-in')->value('room_id');
            if($already_book)
            {
                return response()->json( ['message' => 'This room already booked.Sorry!!']);
            }
            $book = Booking::findOrFail($id);
            $book->room_id = $room->id;
            $book->arrival = $request->input('arrival');
            $book->checkout = $request->input('checkout');
            $book->customer_id = $customer->id;
            $book->book_type = $request->input('book_type');
            $book->book_time = Carbon::now()->toDateString();
            $book->save();
            Log::info('booking update: '.$book->id);

            //successful response
            return response()->json(['booking' => $book], 200);
        } catch (\Exception $e) {
            // error message
            Log::error('bookng update error : '.$e);
            return response()->json(['message' => 'booking update Failed!'], 409);
        }
     }

    

          /**
     * @OA\Delete(
     *   path="/deletebooking/{id}",
     *   summary="delete booking",
     *   tags={"Booking"},
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="booking id",
     *         required=true,
     *         style="form"
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="Specific Booking delete"
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
    public function deleteBooking($id)
    {
        try {
            $book = Booking::findOrFail($id);
            $book->delete();
            Log::info(' book delete:'. $book->id);
            return response()->json(['book' => $book], 200);

        } catch (\Exception $e) {
            Log::error('delete book:'. $e);
            return response()->json(['message' => 'book not found!'], 404);
        }

    }


        /**
     * @OA\Get(
     *   path="/bookingsearch",
     *   summary="Search of booking",
     *   tags={"Booking"},
     *    @OA\Parameter(
     *         name="book_type",
     *         in="query",
     *         description="check-in or check-out",
     *         required=false,
     *         style="form"
     *     ),
     *    @OA\Parameter(
     *         name="customer_name",
     *         in="query",
     *         description="customer_name",
     *         required=false,
     *         style="form"
     *     ),
     * @OA\Parameter(
     *         name="room_name",
     *         in="query",
     *         description="room number",
     *         required=false,
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
     *     description="list of all bookings"
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
     public function searchBooking(Request $request)
     {
         try {
             $customer_name = $request->customer_name;
             $room_name = $request->room_name;
             $book_type = $request->book_type;
             $per_page = $request->per_page;
             $data = Booking::join("customers","bookings.customer_id","customers.id")->join("rooms","bookings.room_id","rooms.id")->where('customers.first_name','like','%'.$customer_name.'%')->orWhere('rooms.room_number','like','%'.$room_name.'%')->orWhere('bookings.book_type','=',$book_type)->paginate($per_page);
             return response()->json(['book' => $data], 200);
 
         } catch (\Exception $e) {
             Log::error(' room search:'. $e);
             return response()->json(['message' => 'search not found!'], 404);
         }
  
      }
 



     


}