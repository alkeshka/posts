<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validation
        $userAttributes = $request->validate([
           'first_name' => ['required', 'min:3', 'max:20'],
           'last_name' => ['nullable', 'max:20'],
           'email' => ['required', 'email'],
           'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $userAttributes = array_merge($userAttributes, [
            'users_role_id' => 2
        ]);

        $user = User::create($userAttributes);

        Auth::login($user);

        return redirect('/');
    }

}
