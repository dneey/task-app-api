<?php

namespace App\Http\Controllers\Auth\Traits;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Log;
use App\User;
use Illuminate\Support\Facades\Auth;

trait RegistersUsers
{
    // use RedirectsUsers;

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            Log::info('Validator failed');
            return response()->json(['responseMessage' => 'Validation failed', 'data' => $validator->errors()]);
        }
        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        return response()->json(['responseCode' => '200', 'responseMessage' => 'User registered successfully. Please login', 'data' => []]);
    }
}
