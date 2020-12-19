<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use  App\Customer;
use Log;

class CustomerController extends Controller
{

    //Auth middleware
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * @OA\Get(
     *   path="/customers",
     *   summary="list of customers",
     *   tags={"Customer"},
     *    @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="number of element per page or all",
     *         required=true,
     *         style="form"
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="list of all customers"
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
    public function allCustomers(Request $request)
    {
         $per_page = $request->get("per_page");
         if($per_page=="all"){
            return response()->json(['customers' =>  Customer::all()], 200);    
         }
         return response()->json(['customers' =>  Customer::paginate($per_page)], 200);
    }

        /**
     * @OA\Get(
     *   path="/customer/{id}",
     *   summary="single customer",
     *   tags={"Customer"},
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="customer id",
     *         required=true,
     *         style="form"
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="Specific customer"
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
    public function singleCustomer($id)
    {
        try {
            $user = Customer::findOrFail($id);

            return response()->json(['customer' => $user], 200);

        } catch (\Exception $e) {

            return response()->json(['message' => 'customer not found!'], 404);
        }

    }

    /**
     * @OA\Post(
     *   path="/customer",
     *   summary="Add a Customer",
     *   tags={"Customer"},
     * @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(
     *   required={"first_name","last_name","email","phone"},
     *   @OA\Property(property="first_name", type="string", format="name", example="John"),
     *   @OA\Property(property="last_name", type="string", format="name", example="Doe"),
     *   @OA\Property(property="email", type="string", format="name", example="user1@mail.com"),
     *   @OA\Property(property="phone", type="string", example="01710000000"),
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

     //Save Customer

     public function saveCustomer(Request $request){
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:11',
        ]);

        try {

            $customer = new Customer;
            $customer->first_name = $request->input('first_name');
            $customer->last_name = $request->input('last_name');
            $customer->email = $request->input('email');
            $customer->phone = $request->input('phone');
            $customer->save();
            Log::info('Customer user: '.$customer->id);

            //successful response
            return response()->json(['customer' => $customer, 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            // error message
            Log::error('Customer save error : '.$e);
            return response()->json(['message' => 'Customer Save Failed!'], 409);
        }
     }


     /**
     * @OA\Delete(
     *   path="/deletecustomer/{id}",
     *   summary="delete customer",
     *   tags={"Customer"},
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="customer id",
     *         required=true,
     *         style="form"
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="Specific customer delete"
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


    //Delete Customer
    public function deleteCustomer($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();
            Log::info(' customer delete:'. $customer->id);
            return response()->json(['cusutomer' => $customer], 200);

        } catch (\Exception $e) {
            Log::error('delete customer:'. $e);
            return response()->json(['message' => 'customer not found!'], 404);
        }

    }


    /**
     * @OA\Put(
     *   path="/customer",
     *   summary="Update a Customer",
     *   tags={"Customer"},
     * @OA\RequestBody(
     *   required=true,
     *   @OA\JsonContent(
     *   required={"id","first_name","last_name","email","phone"},
     *   @OA\Property(property="id", type="integer"),
     *   @OA\Property(property="first_name", type="string", format="name", example="John2"),
     *   @OA\Property(property="last_name", type="string", format="name", example="Doe2"),
     *   @OA\Property(property="email", type="string", format="name", example="user122@mail.com"),
     *   @OA\Property(property="phone", type="string", example="01710000000")
     *    ),
     *    ),
     
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

    //Update Customer
    public function updateCustomer(Request $request)
    {
        $this->validate($request, [
            'id'=>'required|integer',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:11',
        ]);
        try {
            $id = $request->id;
            $customer = Customer::findOrFail($id);
            $customer->name = $request->name;
            $customer->save();
            Log::info(' customer update:'. $customer->id);
            return response()->json(['customer' => $customer], 200);

        } catch (\Exception $e) {
            Log::error(' customer update:'. $e);
            return response()->json(['message' => 'customer not found!'], 404);
        }

    }

    /**
     * @OA\Get(
     *   path="/customersearch",
     *   summary="Search of customers",
     *   tags={"Customer"},
     *    @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="customer email",
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
     *     description="list of all customers"
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
     
     //Search Customer
    public function searchCustomer(Request $request)
    {
        try {
            $email = $request->email;
            $per_page = $request->per_page;
            $customer = Customer::where('email','like','%'.$email.'%')->paginate($per_page);
            Log::info(' customer search:'. $customer);
            return response()->json(['customer' => $customer], 200);

        } catch (\Exception $e) {
            Log::error(' customer search:'. $e);
            return response()->json(['message' => 'customer not found!'], 404);
        }

    }

}