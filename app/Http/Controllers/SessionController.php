<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSessionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    /**
     * Show the login view.
     *
     * @return View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param StoreSessionRequest $request
     * @return RedirectResponse
     * @throws ValidationException
     */
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

    /**
     * Log the user out.
     *
     * @return RedirectResponse
     */
    public function destroy()
    {
        auth()->logout();
        return redirect('/');
    }
}
