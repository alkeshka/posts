<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSessionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(StoreSessionRequest $request)
    {
        $validatedValues = $request->validated();

        if (!Auth::attempt($validatedValues)) {
            throw ValidationException::withMessages([
                'auth_exception' => 'Sorry the credentials are not valid. Please try again.',
            ]);
        }

        request()->session()->regenerate();

        return redirect('/');
    }

    public function destroy()
    {
        auth()->logout();
        return redirect('/');
    }
}
