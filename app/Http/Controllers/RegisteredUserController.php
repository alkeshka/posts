<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
    public function store(StoreUserRequest $request)
    {
        $validatedAttributes = $request->validated();

        $dbData = array_merge($validatedAttributes, ['users_role_type' => User::ROLE_USER]);
        $user = User::create($dbData);

        Auth::login($user);

        return redirect('/');
    }

}
