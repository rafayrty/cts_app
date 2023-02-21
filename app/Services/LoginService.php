<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    /**
     * Authenticate a User For Frontend Dashboard
     *
     * @param  \App\DTO\LoginData  $data
     * @return array <string,mixed>
     */
    public function loginFrontend(LoginRequest $request)
    {
        //if (! Auth::guard('web')->attempt(array_merge(['email' => $request->email, 'password' => $request->password], ['active' => 1]))) {
        if (! Auth::guard('web')->attempt(array_merge(['email' => $request->email, 'password' => $request->password]))) {
            return ['message' => 'Email or Password Is Incorrect', 'status' => 404];
        //} elseif (Auth::guard('web')->user()->email_verified_at === null) {
        //Auth::guard('web')->logout();
        //request()->session()->invalidate();
        //request()->session()->regenerateToken();
        //return ['message' => 'Email Needs To Be Verified First', 'status' => 412];
        } else {
            $user = Auth::user();

            return [
                'message' => 'success',
                'status' => 200,
                'user' => $user,
            ];
        }
    }
}
