<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use  App\Booking;
use  App\Room;
use  App\Payment;
use  App\Customer;
use Carbon\Carbon;
use Log;

class PaymentController extends Controller
{

    //Auth middleware
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * @OA\Get(
     *   path="/payments",
     *   summary="list of payments",
     *   tags={"Payment"},
     *    @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="number of element per page or all",
     *         required=true,
     *         style="form"
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="list of all payments"
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
    public function allPayments(Request $request)
    {
         $per_page = $request->get("per_page");
         if($per_page=="all"){
            return response()->json(['payments' =>  Payment::all()], 200);    
         }
         return response()->json(['payments' =>  Payment::paginate($per_page)], 200);
    }

        /**
     * @OA\Get(
     *   path="/payment/{id}",
     *   summary="single payment",
     *   tags={"Payment"},
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="payment id",
     *         required=true,
     *         style="form"
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="Specific Payment"
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
    public function singlePayment($id)
    {
        try {
            $payment = Payment::findOrFail($id);

            return response()->json(['payment' => $payment], 200);

        } catch (\Exception $e) {

            return response()->json(['message' => 'payment not found!'], 404);
        }

    }

    /**
     * @OA\Post(
     *   path="/payment",
     *   summary="Add a Payment",
     *   tags={"Payment"},
     * @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(
     *   required={ "customer_id", "booking_id"},
     *   @OA\Property(property="customer_id", type="integer"),
     *   @OA\Property(property="booking_id", type="integer"),
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

     public function savePayment(Request $request){
        $this->validate($request, [
            'customer_id' => 'required|integer',
            'booking_id' => 'required|integer'
        ]);

        try {

            $book = Room::findOrFail($request->booking_id);
            $customer = Customer::findOrFail($request->customer_id);
            $payment = new Payment;
            $payment->booking_id = $book->id;
            $payment->customer_id = $customer->id;
            $status = $payment->save();
            if($status)
            {
                $check_payment_status = Booking::where('id', $book->id)->value("payment_status");
                if($check_payment_status=="Pending"){
                Booking::where('id', $book->id)->update(["payment_status"=>"Success"]);
                }
            }
            Log::info('Payment Done user: '. $book->id);

            //successful response
            return response()->json(['Payment' => $payment, 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            // error message
            Log::error('Payment Fail error : '.$e);
            return response()->json(['message' => 'booking Save Failed!'], 409);
        }
     }

    
     


}