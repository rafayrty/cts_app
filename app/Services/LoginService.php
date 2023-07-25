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
        //Check if Email or phone
        $loginMethod = 'phone';

        if (filter_var($request->identifier, FILTER_VALIDATE_EMAIL)) {
            $loginMethod = 'email';
        }

        if ($loginMethod == 'email') {
            return $this->login_email($request->identifier, $request->password);
        }

        return $this->login_phone($request->identifier, $request->password);
    }

    public function login_email($email, $password)
    {

        if (! Auth::guard('web')->attempt(['email' => $email, 'password' => $password])) {
            return ['message' => 'Email or Password Is Incorrect', 'status' => 404];
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

    public function login_phone($phone, $password)
    {
        //if (! Auth::guard('web')->attempt(array_merge(['email' => $request->email, 'password' => $request->password], ['active' => 1]))) {
        if (! Auth::guard('web')->attempt(['email' => $this->phone_verification($phone), 'password' => $password])) {
            return ['message' => 'Phone or Password Is Incorrect', 'status' => 404];
        } elseif (Auth::guard('web')->user()->verified_at === null) {
            $user = Auth::user();
            Auth::guard('web')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return ['message' => 'Phone Number Needs To Be Verified First', 'status' => 412, 'user' => $user];
        } else {
            $user = Auth::user();

            return [
                'message' => 'success',
                'status' => 200,
                'user' => $user,
            ];
        }
    }

    public function phone_verification($phone)
    {
        //$user = User::where(DB::raw('CONCAT("+",country_code,phone)'), $phone)->get()->first();
        $user = User::where('phone', $phone)->get()->first();

        if ($user) {
            return $user->email;
        }

        return '';
    }
}
