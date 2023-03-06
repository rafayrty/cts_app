<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        if (! Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password]) &&
           ! Auth::guard('web')->attempt(['email' => $this->phone_verification($request), 'password' => $request->password])) {
            return ['message' => 'Phone/Email or Password Is Incorrect', 'status' => 404];
        } elseif (Auth::guard('web')->user()->verified_at === null) {
            $user = Auth::user();
            Auth::guard('web')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return ['message' => 'Email Needs To Be Verified First', 'status' => 412, 'user' => $user];
        } else {
            $user = Auth::user();

            return [
                'message' => 'success',
                'status' => 200,
                'user' => $user,
            ];
        }
    }

    public function phone_verification($request)
    {
        $user = User::where(DB::raw('CONCAT("+",country_code,phone)'), $request->email)->get()->first();

        if ($user) {
            return $user->email;
        }

        return '';
    }
}
