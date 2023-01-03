<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;

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
