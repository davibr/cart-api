<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\User;

class UserService
{

    /**
     * Gets the user from the request.
     *
     * @param  Request  $request
     * @return Cart
     */
    public static function getUserFromRequest(Request $request)
    {
        return User::find($request->user_id);
    }
}