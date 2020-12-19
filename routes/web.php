<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    //User Module
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
    $router->get('user/{id}', 'UserController@singleUser');
    $router->get('users', 'UserController@allUsers');
    $router->put('user', 'UserController@updateUser');
    $router->delete('deleteuser/{id}', 'UserController@deleteUser');

    //Booking Module
    $router->get('booking/{id}', 'BookingController@singleBooking');
    $router->post('booking', 'BookingController@saveBooking');
    $router->get('bookings', 'BookingController@allBookings');
    $router->put('booking', 'BookingController@updateBooking');
    $router->delete('deletebooking/{id}', 'BookingController@deleteBooking');
    $router->get('bookingsearch', 'BookingController@searchBooking');

    //Customer Module
    $router->get('customer/{id}', 'CustomerController@singleCustomer');
    $router->get('customersearch', 'CustomerController@searchCustomer');
    $router->post('customer', 'CustomerController@saveCustomer');
    $router->get('customers', 'CustomerController@allCustomers');
    $router->put('customer', 'CustomerController@updateCustomer');
    $router->delete('deletecustomer/{id}', 'CustomerController@deleteCustomer');

    //Room Module
    $router->get('room/{id}', 'RoomController@singleRoom');
    $router->post('room', 'RoomController@saveRoom');
    $router->get('rooms', 'RoomController@allRooms');
    $router->put('room', 'RoomController@updateRoom');
    $router->delete('deleteroom/{id}', 'RoomController@deleteRoom');
    $router->get('roomsearch', 'RoomController@searchRoom');


    //Payment Module
    $router->get('payment/{id}', 'PaymentController@singlePayment');
    $router->post('payment', 'PaymentController@savePayment');
    $router->get('payments', 'PaymentController@allPayments');

});
