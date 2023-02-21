<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Services\LoginService;
use App\Services\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class AuthController extends Controller
{
    public function __construct(RegisterService $registerService,
    LoginService $loginService
) {
        $this->registerService = $registerService;
        $this->loginService = $loginService;
    }

    /**
     * Register a New User in storage.
     *
     * @param  \App\Http\Requests\RegisterRequest  $request
     * @return \App\Models\User
     */
    public function register(RegisterRequest $request)
    {
        return $this->registerService->register($request);
    }

    /**
     * Login a New User.
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        if (EnsureFrontendRequestsAreStateful::fromFrontend($request)) {
            $login = $this->loginService->loginFrontend($request);

            if ($login['status'] == 200) {
                return response()->json($login['user'], 200);
            }

            return response()->json(['message' => $login['message']], $login['status']);
        }
    }

    /**
     * Update The User
     *
     * @param  \App\Http\Requests\UserUpdateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update_user(UserUpdateRequest $request)
    {
        $user = User::findOrFail($request->user()->id);
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country_code' => $request->country_code,
        ]);

        if ($request->current_passsword != null) {
            //Check if matched
            if (Hash::check($request->current_passsword, $user->password)) {
                $user = User::findOrFail($request->user()->id);

                $user->update(['password' => Hash::make($request->new_password)]);
            } else {
                return response()->json(['message' => 'Current Password is Incorrect']);
            }
        }

        return response()->json(['message' => 'Account was Updated Successfully']);
    }

    /**
     * Logout The User
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        if (EnsureFrontendRequestsAreStateful::fromFrontend($request)) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return true;
        }

        return true;
    }
}
