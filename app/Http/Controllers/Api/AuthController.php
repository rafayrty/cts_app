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
     * @return \App\Models\User
     */
    public function register(RegisterRequest $request)
    {
        $validate_phone = User::where('phone', $request->phone)->where('country_code', $request->country_code)->get()->first();
        if ($validate_phone) {
            abort(422, 'Phone Number already Exists');
        }

        return $this->registerService->register($request);
    }

    /**
     * Resend Email
     *
     * @return \App\Models\User
     */
    //public function resend_email(Request $request)
    //{
    //$this->validate($request, [
    //'email' => 'required|email',
    //]);
    //$user = User::where('email', $request->email)->get()->first();

    //if (! $user) {
    //abort(404);
    //}

    //return $this->registerService->verifyEmail(User::findOrFail($user->id));
    //}

    /**
     * Resend Email
     *
     * @return \App\Models\User
     */
    public function resend_email(Request $request)
    {
        $this->validate($request, [
            'number' => 'required|max:255|min:3',
            'country_code' => 'required',
        ]);

        $user = User::where('phone', $request->number)->where('country_code', $request->country_code)->get()->first();

        if (! $user) {
            abort(404);
        }

        return $this->registerService->verifyEmail(User::findOrFail($user->id));
    }

    /**
     * Resend Phone
     *
     * @return \App\Models\User
     */
    public function resend(Request $request)
    {
        $this->validate($request, [
            'number' => 'required|max:255|min:3',
            'country_code' => 'required',
        ]);

        $user = User::where('phone', $request->number)->where('country_code', $request->country_code)->get()->first();

        if (! $user) {
            abort(404);
        }

        return $this->registerService->verifyPhone(User::findOrFail($user->id));
    }

    /**
     * Register a New User in storage.
     *
     * @return \App\Models\User
     */
    //public function email_validation(Request $request)
    //{
    //return $this->registerService->register($request);
    //}

    /**
     * Verify a user using email
     *
     * @return \App\Models\User
     */
    //public function email_verification(Request $request)
    //{
    //$this->validate($request, [
    //'email' => 'required|email',
    //'verification_code' => 'required',
    //]);

    //return $this->registerService->email_verification($request);
    //}

    /**
     * Register a New User in storage.
     *
     * @return \App\Models\User
     */
    public function verification(Request $request)
    {
        $this->validate($request, [
            'number' => 'required|max:255|min:3',
            'country_code' => 'required',
            'verification_code' => 'required',
        ]);

        return $this->registerService->phone_verification($request);
    }

    /**
     * Login a New User.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        if (EnsureFrontendRequestsAreStateful::fromFrontend($request)) {
            $login = $this->loginService->loginFrontend($request);

            if ($login['status'] == 200) {
                return response()->json($login['user'], 200);
            } elseif ($login['status'] == 412) {
                return response()->json(['message' => $login['message'], 'user' => $login['user']], $login['status']);
            }

            return response()->json(['message' => $login['message']], $login['status']);
        }
    }

    /**
     * Update The User
     *
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
