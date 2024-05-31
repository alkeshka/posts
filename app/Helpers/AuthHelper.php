<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class AuthHelper
{
    public function getAuthenticatedUserId()
    {
        return Auth::id();
    }
}
