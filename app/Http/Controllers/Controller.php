<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Booking System API ",
     *      description="Booking System API Documentation",
     * )
     *
     *  @OA\Server(
     *      url="http://localhost:8000/api/v1",
     *      description="Dev server"
     *  )
     *
     *  @OA\Server(
     *      url="http://localhost:8000/api/v1",
     *      description="Staging server"
     *  )
     *
     *  @OA\Server(
     *      url="http://liveurl/api/v1",
     *      description="Live server"
     *  )
     */

    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }
}
